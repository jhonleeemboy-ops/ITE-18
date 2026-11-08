<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    // residents see only their own, staff see all
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Incident::with('reporter.household')->latest();

        if (!$user->isStaff()) {
            $query->where('reporter_id', $user->id);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'        => 'required|in:evacuation,relief,medical,rescue,hazard',
            'description' => 'nullable|string|max:2000',
        ]);

        $priority = in_array($data['type'], ['medical', 'rescue']) ? 'high' : 'medium';

        $incident = Incident::create($data + [
            'reporter_id' => $request->user()->id,
            'status'      => 'pending',
            'priority'    => $priority,
        ]);

        return response()->json($incident->load('reporter.household'), 201);
    }

    // staff only: change status and/or priority
    public function update(Request $request, Incident $incident)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        $data = $request->validate([
            'status'   => 'sometimes|in:pending,in_progress,resolved',
            'priority' => 'sometimes|in:high,medium,low',
        ]);

        $incident->update($data);

        return $incident->load('reporter.household', 'notes');
    }

    // staff only: add a note
    public function addNote(Request $request, Incident $incident)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        $data = $request->validate(['text' => 'required|string|max:1000']);

        return response()->json($incident->notes()->create($data), 201);
    }
}