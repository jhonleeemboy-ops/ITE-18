<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\HouseholdMember;
use Illuminate\Http\Request;

class HouseholdMemberController extends Controller
{
    // staff can see any household's members; a resident only their own
    public function index(Request $request, Household $household)
    {
        $this->checkAccess($request, $household);

        return $household->familyMembers()->orderBy('id')->get();
    }

    public function store(Request $request, Household $household)
    {
        $this->checkAccess($request, $household);

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'age'          => 'required|integer|between:0,120',
            'relationship' => 'nullable|string|max:50',
            'is_pwd'       => 'boolean',
        ]);

        $member = $household->familyMembers()->create($data);

        // the household's member count can never be smaller than the list
        $listed = $household->familyMembers()->count();
        if ($listed > $household->members) {
            $household->update(['members' => $listed]);
        }

        return response()->json($member->fresh(), 201);
    }

    public function destroy(Request $request, HouseholdMember $member)
    {
        $this->checkAccess($request, $member->household);

        $member->delete();

        return response()->noContent();
    }

    private function checkAccess(Request $request, Household $household): void
    {
        $user = $request->user();

        abort_unless($user->isStaff() || $user->household_id === $household->id, 403, 'Not allowed.');
    }
}