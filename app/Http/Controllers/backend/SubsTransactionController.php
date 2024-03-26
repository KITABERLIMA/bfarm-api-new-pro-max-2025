<?php

namespace App\Http\Controllers\backend;

use App\Models\subs_transaction;
use App\Http\Requests\StoreSubsTransactionRequest;
use App\Http\Controllers\Controller;
use App\Models\subscription;

class SubsTransactionController extends Controller
{
    public function index()
    {
        $subsTransactions = subs_transaction::with('user', 'subscription')->get();

        if ($subsTransactions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No subscription transactions found.',
            ], 404);
        }

        $subsTransactions->transform(function ($transaction) {
            $transaction['start_date'] = ($transaction['start_date'] instanceof \DateTime) ? $transaction['start_date']->format('d-m-Y') : $transaction['start_date'];
            $transaction['end_date'] = ($transaction['end_date'] instanceof \DateTime) ? $transaction['end_date']->format('d-m-Y') : $transaction['end_date'];
            return $transaction;
        });

        return response()->json([
            'success' => true,
            'data' => $subsTransactions,
        ]);
    }

    public function store(StoreSubsTransactionRequest $request)
    {
        $validated = $request->validated();

        $subscription = subscription::find($validated['subs_id']);
        $duration = $subscription->duration;

        $price = $subscription->price;
        $validated['amount_paid'] = $price;

        $startDate = now();
        $endDate = now()->addMonths($duration);

        $validated['start_date'] = $startDate;
        $validated['end_date'] = $endDate;

        $subsTransaction = subs_transaction::create($validated);

        $subsTransaction['start_date'] = $startDate->format('d-m-Y');
        $subsTransaction['end_date'] = $endDate->format('d-m-Y');

        return response()->json([
            'success' => true,
            'message' => 'Subscription transaction successfully created.',
            'data' => $subsTransaction,
        ], 201);
    }

    public function show($id)
    {
        $subsTransaction = subs_transaction::with('user', 'subscription')->find($id);

        if (!$subsTransaction) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription transaction not found.',
            ], 404);
        }

        $subsTransaction['start_date'] = ($subsTransaction['start_date'] instanceof \DateTime) ? $subsTransaction['start_date']->format('d-m-Y') : $subsTransaction['start_date'];
        $subsTransaction['end_date'] = ($subsTransaction['end_date'] instanceof \DateTime) ? $subsTransaction['end_date']->format('d-m-Y') : $subsTransaction['end_date'];

        return response()->json([
            'success' => true,
            'data' => $subsTransaction,
        ]);
    }

    public function destroy($id)
    {
        $subsTransaction = subs_transaction::find($id);

        if (!$subsTransaction) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription transaction not found.',
            ], 404);
        }

        $subsTransaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription transaction successfully deleted.',
        ]);
    }
}
