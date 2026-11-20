<?php

namespace App\Http\Controllers;

use App\Models\Advisory;
use App\Models\SmsLog;
use App\Models\User;
use App\Services\SmsSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsAlertController extends Controller
{
    public function store(Request $request, SmsSender $sms)
    {
        // staff only
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'message'  => 'required|string|max:1000',
            'send_to'  => 'required',            // "all" or a Purok number 1-23
            'severity' => 'nullable|in:yellow,orange,red,info',
        ]);

        $purok = null;
        if ($data['send_to'] !== 'all') {
            $purok = (int) $data['send_to'];
            abort_unless($purok >= 1 && $purok <= 23, 422, 'Invalid Purok.');
        }

        // mobile numbers of the residents (in that Purok, if one was chosen)
        $numbers = User::where('role', 'resident')
            ->whereNotNull('mobile_number')
            ->when($purok, fn ($q) => $q->whereHas('household', fn ($h) => $h->where('purok', $purok)))
            ->pluck('mobile_number')
            ->all();

        // 1. Save the advisory and the log first, so the alert is never lost
        $result = DB::transaction(function () use ($data, $numbers) {
            $advisory = Advisory::create([
                'title'    => $data['title'],
                'message'  => $data['message'],
                'severity' => $data['severity'] ?? 'info',
                'status'   => 'active',
            ]);

            // sent stays 0 and sent_at stays null until the SMS gateway accepts the message
            $log = SmsLog::create([
                'advisory_id' => $advisory->id,
                'recipients'  => count($numbers),
                'sent'        => 0,
                'failed'      => 0,
                'sent_at'     => null,
            ]);

            return ['advisory' => $advisory, 'sms_log' => $log];
        });

        // 2. Hand the message to the SMS gateway, if one is set up in .env
        $error = null;

        if ($sms->isConfigured() && count($numbers) > 0) {
            $outcome = $sms->send($numbers, "BRGY AMPAYON: {$data['title']}. {$data['message']}");

            $result['sms_log']->update([
                'sent'    => $outcome['sent'],
                'failed'  => $outcome['failed'],
                'sent_at' => $outcome['sent'] > 0 ? now() : null,
            ]);

            $error = $outcome['error'];
        }

        // tell the page what really happened, so it never claims an SMS was sent when it was not
        $result['sms'] = [
            'configured' => $sms->isConfigured(),
            'error'      => $error,
        ];

        return response()->json($result, 201);
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        return SmsLog::with('advisory:id,title')->latest()->get();
    }

    // staff only: delete an alert. This removes its advisory (so residents no longer
    // see it in the portal) and its log row. It cannot recall texts already sent.
    public function destroy(Request $request, SmsLog $smsLog)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        // deleting the advisory also deletes its log rows (cascadeOnDelete on sms_logs)
        $smsLog->advisory()->delete();
        $smsLog->delete(); // in case the log has no advisory

        return response()->noContent();
    }
}