<?php

namespace App\Http\Controllers;

use App\Models\ReliefDistribution;
use App\Models\ReliefItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReliefController extends Controller
{
    public function items(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        return ReliefItem::orderBy('name')->get();
    }

    public function distributions(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        return ReliefDistribution::with('household', 'reliefItem')
            ->latest('distributed_at')
            ->limit(100)
            ->get();
    }

    // records a distribution and reduces the stock in one transaction
    public function distribute(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403, 'Staff only.');

        $data = $request->validate([
            'household_id'   => 'required|exists:households,id',
            'relief_item_id' => 'required|exists:relief_items,id',
            'quantity'       => 'required|integer|min:1',
        ]);

        $record = DB::transaction(function () use ($data) {
            $item = ReliefItem::lockForUpdate()->findOrFail($data['relief_item_id']);

            if ($item->quantity_in_stock < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Only {$item->quantity_in_stock} {$item->unit} left in stock.",
                ]);
            }

            $item->decrement('quantity_in_stock', $data['quantity']);

            return ReliefDistribution::create($data + ['distributed_at' => now()]);
        });

        return response()->json($record->load('household', 'reliefItem'), 201);
    }
}