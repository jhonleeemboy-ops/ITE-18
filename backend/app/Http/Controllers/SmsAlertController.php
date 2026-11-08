<?php

namespace App\Http\Controllers;

use App\Models\Advisory;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsAlertController extends Controller
{
    public function store(Request $request)
    {
        // staff only
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'message'  => 'required|string|max:1000',
            'send_to'  => 'required',            // "all" or a Purok number 1-8
            'severity' => 'nullable|in:yellow,orange,red,info',
        ]);

        $purok = null;
        if ($data['send_to'] !== 'all') {
            $purok = (int) $data['send_to'];
            abort_unless($purok >= 1 && $purok <= 8, 422, 'Invalid Purok.');
        }

        // count the residents who have a mobile number (and are in that Purok, if chosen)
        $recipients = User::where('role', 'resident')
            ->whereNotNull('mobile_number')
            ->when($purok, fn ($q) => $q->whereHas('household', fn ($h) => $h->where('purok', $purok)))
            ->count();

        $result = DB::transaction(function () use ($data, $recipients) {
            $advisory = Advisory::create([
                'title'    => $data['title'],
                'message'  => $data['message'],
                'severity' => $data['severity'] ?? 'info',
                'status'   => 'active',
            ]);

            // sent stays 0 and sent_at stays null until an SMS provider confirms delivery
            $log = SmsLog::create([
                'advisory_id' => $advisory->id,
                'recipients'  => $recipients,
                'sent'        => 0,
                'failed'      => 0,
                'sent_at'     => null,
            ]);

            return ['advisory' => $advisory, 'sms_log' => $log];
        });

        return response()->json($result, 201);
    }
}