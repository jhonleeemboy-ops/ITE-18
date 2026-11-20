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

    // staff only: add a household that was listed by barangay personnel
    public function store(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        $data = $request->validate([
            'head_name' => 'required|string|max:255',
            'purok'     => 'required|integer|between:1,23',
            'members'   => 'required|integer|min:1|max:50',
            'safety'    => 'nullable|in:safe,evacuated,needs_help',
        ]);

        // leave out an empty safety so the table default (safe) is used
        $household = Household::create(array_filter($data, fn ($value) => $value !== null));

        return response()->json($household->fresh(), 201);
    }

    // staff only: correct a household's details
        // staff can correct any household; a resident only their own
    public function update(Request $request, Household $household)
    {
        $user = $request->user();
        abort_unless($user->isStaff() || $user->household_id === $household->id, 403, 'Not allowed.');

        $data = $request->validate([
            'head_name' => 'sometimes|required|string|max:255',
            'purok'     => 'sometimes|required|integer|between:1,23',
            'members'   => 'sometimes|required|integer|min:1|max:50',
            'safety'    => 'sometimes|required|in:safe,evacuated,needs_help',
        ]);

        $household->update($data);

        return $household;
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