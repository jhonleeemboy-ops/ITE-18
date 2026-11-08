<?php

namespace App\Http\Controllers;

use App\Models\Advisory;
use App\Models\EvacuationCenter;
use App\Models\Household;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        return [
            'households_total'    => Household::count(),
            'people_total'        => (int) Household::sum('members'),
            'households_by_safety' => Household::select('safety', DB::raw('count(*) as total'))
                ->groupBy('safety')->pluck('total', 'safety'),
            'households_by_purok' => Household::select('purok', DB::raw('count(*) as total'))
                ->groupBy('purok')->orderBy('purok')->pluck('total', 'purok'),
            'centers' => [
                'count'     => EvacuationCenter::count(),
                'capacity'  => (int) EvacuationCenter::sum('capacity'),
                'occupants' => (int) EvacuationCenter::sum('occupants'),
            ],
            'incidents_by_status' => Incident::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->pluck('total', 'status'),
            'active_advisories' => Advisory::where('status', 'active')->count(),
        ];
    }
}