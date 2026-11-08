<?php

namespace App\Http\Controllers;

use App\Models\Household;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    // staff only: list with ?search=name and ?purok=3
    public function index(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        return Household::query()
            ->when($request->search, fn ($q, $s) => $q->where('head_name', 'like', "%{$s}%"))
            ->when($request->purok, fn ($q, $p) => $q->where('purok', $p))
            ->orderBy('purok')
            ->orderBy('head_name')
            ->get();
    }

    // staff can update any household; a resident only their own
    public function updateSafety(Request $request, Household $household)
    {
        $user = $request->user();
        abort_unless($user->isStaff() || $user->household_id === $household->id, 403, 'Not allowed.');

        $data = $request->validate([
            'safety' => 'required|in:safe,evacuated,needs_help',
        ]);

        $household->update($data);

        return $household;
    }
}