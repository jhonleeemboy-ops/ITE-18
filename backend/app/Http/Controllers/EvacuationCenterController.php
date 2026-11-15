<?php

namespace App\Http\Controllers;

use App\Models\CenterPurok;
use App\Models\EvacuationCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EvacuationCenterController extends Controller
{
    // everyone signed in can see the centers and the Puroks they serve
    public function index()
    {
        return EvacuationCenter::with('puroks')->orderBy('name')->get();
    }

    // staff only: add a center and (optionally) the Puroks it serves
    public function store(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        $data = $request->validate([
            'name'     => 'required|string|max:255|unique:evacuation_centers,name',
            'capacity' => 'required|integer|min:1|max:100000',
            'puroks'   => 'nullable|array',
            'puroks.*' => 'integer|between:1,8|distinct',
        ]);

        $center = DB::transaction(function () use ($data) {
            // a new center starts empty and on standby (the table defaults)
            $center = EvacuationCenter::create([
                'name'     => $data['name'],
                'capacity' => $data['capacity'],
            ]);

            foreach ($data['puroks'] ?? [] as $purok) {
                CenterPurok::create([
                    'evacuation_center_id' => $center->id,
                    'purok'                => $purok,
                ]);
            }

            return $center;
        });

        return response()->json($center->fresh()->load('puroks'), 201);
    }

    // staff only: people arriving (check_in) or leaving (check_out)
    public function updateOccupants(Request $request, EvacuationCenter $center)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        $data = $request->validate([
            'action' => 'required|in:check_in,check_out',
            'people' => 'required|integer|min:1',
        ]);

        $center = DB::transaction(function () use ($data, $center) {
            // lock the row so two staff members can't count the same space twice
            $center = EvacuationCenter::lockForUpdate()->findOrFail($center->id);

            if ($data['action'] === 'check_in') {
                $spaceLeft = $center->capacity - $center->occupants;

                if ($data['people'] > $spaceLeft) {
                    throw ValidationException::withMessages([
                        'people' => "Only {$spaceLeft} places left in this center.",
                    ]);
                }

                $occupants = $center->occupants + $data['people'];
            } else {
                if ($data['people'] > $center->occupants) {
                    throw ValidationException::withMessages([
                        'people' => "Only {$center->occupants} people are in this center.",
                    ]);
                }

                $occupants = $center->occupants - $data['people'];
            }

            $center->update([
                'occupants' => $occupants,
                'status'    => EvacuationCenter::statusFor($occupants, $center->capacity),
            ]);

            return $center;
        });

        return $center->load('puroks');
    }
}
