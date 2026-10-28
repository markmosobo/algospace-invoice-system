<?php

namespace App\Http\Controllers;

use App\Models\CropHarvest;
use App\Models\CropHarvestFunding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CropHarvestFundingController extends Controller
{
    /**
     * List funding transactions and balances for a harvest.
     *
     * GET /api/crop-harvests/{harvest}/fundings
     */
    public function index($harvestId)
    {
        $harvest = CropHarvest::findOrFail($harvestId);

        $transactions = $harvest->fundings()
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        $expenses = $this->harvestTotal($harvest);

        $balances = $this->calculateBalances($transactions);

        $totalFunded = $transactions
            ->where('transaction_type', 'funded')
            ->sum(fn ($item) => (float) $item->amount);

        $totalRepaid = $transactions
            ->where('transaction_type', 'repaid')
            ->sum(fn ($item) => (float) $item->amount);

        return response()->json([
            'success' => true,
            'data' => [
                'harvest_id' => $harvest->id,
                'total_expenses' => round($expenses, 2),
                'total_funded' => round($totalFunded, 2),
                'total_repaid' => round($totalRepaid, 2),
                'total_outstanding' => round(
                    $totalFunded - $totalRepaid,
                    2
                ),
                'unfunded_expenses' => round(
                    max(0, $expenses - $totalFunded),
                    2
                ),
                'balances' => $balances,
                'transactions' => $transactions,
            ],
        ]);
    }

    /**
     * Record money used to fund harvesting expenses.
     *
     * POST /api/crop-harvests/{harvest}/fundings
     */
    public function store(Request $request, $harvestId)
    {
        $validated = $request->validate([
            'funding_source' => [
                'required',
                'string',
                'max:30',
            ],
            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'transaction_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $source = strtolower(trim($validated['funding_source']));

        if ($source === '') {
            throw ValidationException::withMessages([
                'funding_source' => [
                    'Please specify who or what funded the expense.',
                ],
            ]);
        }

        $amount = (float) $validated['amount'];

        $funding = DB::transaction(function () use (
            $harvestId,
            $validated,
            $source,
            $amount
        ) {
            // Serialize funding changes for this harvest.
            $harvest = CropHarvest::whereKey($harvestId)
                ->lockForUpdate()
                ->firstOrFail();

            $transactions = $harvest->fundings()
                ->lockForUpdate()
                ->get();

            $alreadyFunded = $transactions
                ->where('transaction_type', 'funded')
                ->sum(fn ($item) => (float) $item->amount);

            $expenses = $this->harvestTotal($harvest);

            if (round($alreadyFunded + $amount, 2) >
                round($expenses, 2)) {
                throw ValidationException::withMessages([
                    'amount' => [
                        'This funding would exceed the total harvesting expenses of KSh '
                        . number_format($expenses, 2) . '.',
                    ],
                ]);
            }

            return $harvest->fundings()->create([
                'funding_source' => $source,
                'transaction_type' => 'funded',
                'amount' => $amount,
                'transaction_date' => $validated['transaction_date'],
                'notes' => $validated['notes'] ?? null,
            ]);
        }, 3);

        return response()->json([
            'success' => true,
            'message' => 'Harvest funding recorded successfully.',
            'data' => $funding,
        ], 201);
    }

    /**
     * Record a full or partial repayment to a funding source.
     *
     * POST /api/crop-harvests/{harvest}/fundings/repay
     */
    public function repay(Request $request, $harvestId)
    {
        $validated = $request->validate([
            'funding_source' => [
                'required',
                'string',
                'max:30',
            ],
            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'transaction_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $source = strtolower(trim($validated['funding_source']));

        if ($source === '') {
            throw ValidationException::withMessages([
                'funding_source' => [
                    'Please specify the funding source being repaid.',
                ],
            ]);
        }

        $amount = (float) $validated['amount'];

        $repayment = DB::transaction(function () use (
            $harvestId,
            $validated,
            $source,
            $amount
        ) {
            $harvest = CropHarvest::whereKey($harvestId)
                ->lockForUpdate()
                ->firstOrFail();

            $transactions = $harvest->fundings()
                ->lockForUpdate()
                ->get();

            $sourceTransactions = $transactions->filter(
                fn ($item) =>
                    $item->funding_source === $source
            );

            $funded = $sourceTransactions
                ->where('transaction_type', 'funded')
                ->sum(fn ($item) => (float) $item->amount);

            $repaid = $sourceTransactions
                ->where('transaction_type', 'repaid')
                ->sum(fn ($item) => (float) $item->amount);

            $outstanding = round($funded - $repaid, 2);

            if ($funded <= 0) {
                throw ValidationException::withMessages([
                    'funding_source' => [
                        'No funding has been recorded for this source.',
                    ],
                ]);
            }

            if ($amount > $outstanding) {
                throw ValidationException::withMessages([
                    'amount' => [
                        'The repayment exceeds the outstanding balance of KSh '
                        . number_format($outstanding, 2) . '.',
                    ],
                ]);
            }

            return $harvest->fundings()->create([
                'funding_source' => $source,
                'transaction_type' => 'repaid',
                'amount' => $amount,
                'transaction_date' => $validated['transaction_date'],
                'notes' => $validated['notes'] ?? null,
            ]);
        }, 3);

        return response()->json([
            'success' => true,
            'message' => 'Repayment recorded successfully.',
            'data' => $repayment,
        ], 201);
    }

/**
 * Update a funding or reimbursement transaction.
 *
 * PUT /api/crop-harvest-fundings/{funding}
 */
public function update(Request $request, $fundingId)
{
    $validated = $request->validate([
        'funding_source' => [
            'required',
            'string',
            'max:30',
        ],
        'amount' => [
            'required',
            'numeric',
            'gt:0',
            'decimal:0,2',
        ],
        'transaction_date' => [
            'required',
            'date_format:Y-m-d',
        ],
        'notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);

    $source = strtolower(trim($validated['funding_source']));

    if ($source === '') {
        throw ValidationException::withMessages([
            'funding_source' => [
                'Please specify a funding source.',
            ],
        ]);
    }

    $amount = round((float) $validated['amount'], 2);

    $funding = DB::transaction(function () use (
        $fundingId,
        $validated,
        $source,
        $amount
    ) {
        // Find the transaction and lock its parent harvest.
        $existing = CropHarvestFunding::findOrFail($fundingId);

        $harvest = CropHarvest::whereKey($existing->harvest_id)
            ->lockForUpdate()
            ->firstOrFail();

        // Reload and lock all ledger transactions for this harvest.
        $transactions = $harvest->fundings()
            ->lockForUpdate()
            ->get();

        $transaction = $transactions->firstWhere('id', $existing->id);

        if (!$transaction) {
            abort(404, 'Funding transaction not found.');
        }

        $type = $transaction->transaction_type;

        if (!in_array($type, ['funded', 'repaid'], true)) {
            throw ValidationException::withMessages([
                'transaction_type' => [
                    'The transaction type is invalid.',
                ],
            ]);
        }

        $oldSource = $transaction->funding_source;
        $oldAmount = round((float) $transaction->amount, 2);

        if ($type === 'funded') {
            // Calculate funding after replacing this entry.
            $totalFunded = $transactions
                ->where('transaction_type', 'funded')
                ->sum(fn ($item) => (float) $item->amount);

            $newTotalFunded = round(
                $totalFunded - $oldAmount + $amount,
                2
            );

            $expenses = round(
                (float) $harvest->labour_cost
                + (float) $harvest->transport_cost
                + (float) $harvest->other_cost,
                2
            );

            if ($newTotalFunded > $expenses) {
                throw ValidationException::withMessages([
                    'amount' => [
                        'Total funding cannot exceed harvest expenses of KSh '
                        . number_format($expenses, 2) . '.',
                    ],
                ]);
            }

            // A source must retain enough funding to cover its repayments.
            $sourceTransactions = $transactions->filter(
                fn ($item) =>
                    $item->funding_source === $oldSource
                    || $item->funding_source === $source
            );

            $fundedBySource = $sourceTransactions
                ->where('transaction_type', 'funded')
                ->groupBy('funding_source')
                ->map(fn ($items) => $items->sum(
                    fn ($item) =>
                        $item->id === $transaction->id
                            ? $amount
                            : (float) $item->amount
                ));

            $repaidBySource = $sourceTransactions
                ->where('transaction_type', 'repaid')
                ->groupBy('funding_source')
                ->map(fn ($items) => $items->sum(
                    fn ($item) => (float) $item->amount
                ));

            foreach ([$oldSource, $source] as $checkSource) {
                $funded = (float) ($fundedBySource[$checkSource] ?? 0);
                $repaid = (float) ($repaidBySource[$checkSource] ?? 0);

                if ($repaid > $funded) {
                    throw ValidationException::withMessages([
                        'funding_source' => [
                            'This change would leave reimbursements greater than the funding recorded for '
                            . $checkSource . '.',
                        ],
                    ]);
                }
            }
        } else {
            // Reimbursement must remain within the source's outstanding balance.
            $sourceTransactions = $transactions->filter(
                fn ($item) =>
                    $item->funding_source === $oldSource
                    || $item->funding_source === $source
            );

            foreach ([$oldSource, $source] as $checkSource) {
                $funded = $sourceTransactions
                    ->where('funding_source', $checkSource)
                    ->where('transaction_type', 'funded')
                    ->sum(fn ($item) => (float) $item->amount);

                $repaid = $sourceTransactions
                    ->where('funding_source', $checkSource)
                    ->where('transaction_type', 'repaid')
                    ->sum(fn ($item) =>
                        $item->id === $transaction->id
                            ? $amount
                            : (float) $item->amount
                    );

                if ($repaid > $funded) {
                    throw ValidationException::withMessages([
                        'amount' => [
                            'The change would exceed the outstanding balance for '
                            . $checkSource . '.',
                        ],
                    ]);
                }
            }
        }

        $transaction->update([
            'funding_source' => $source,
            'amount' => $amount,
            'transaction_date' => $validated['transaction_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return $transaction->fresh();
    }, 3);

    return response()->json([
        'success' => true,
        'message' => 'Funding transaction updated successfully.',
        'data' => $funding,
    ]);
}

/**
 * Delete a funding or reimbursement transaction.
 *
 * DELETE /api/crop-harvest-fundings/{funding}
 */
public function destroy($fundingId)
{
    DB::transaction(function () use ($fundingId) {
        $existing = CropHarvestFunding::findOrFail($fundingId);

        $harvest = CropHarvest::whereKey($existing->harvest_id)
            ->lockForUpdate()
            ->firstOrFail();

        $transactions = $harvest->fundings()
            ->lockForUpdate()
            ->get();

        $transaction = $transactions->firstWhere('id', $existing->id);

        if (!$transaction) {
            abort(404, 'Funding transaction not found.');
        }

        $source = $transaction->funding_source;
        $type = $transaction->transaction_type;
        $amount = round((float) $transaction->amount, 2);

        if ($type === 'funded') {
            $funded = $transactions
                ->where('funding_source', $source)
                ->where('transaction_type', 'funded')
                ->sum(fn ($item) =>
                    $item->id === $transaction->id
                        ? 0
                        : (float) $item->amount
                );

            $repaid = $transactions
                ->where('funding_source', $source)
                ->where('transaction_type', 'repaid')
                ->sum(fn ($item) => (float) $item->amount);

            if ($repaid > $funded) {
                throw ValidationException::withMessages([
                    'funding' => [
                        'This funding entry cannot be deleted because reimbursements already recorded for '
                        . $source . ' would exceed the remaining funding. Review those reimbursements first.',
                    ],
                ]);
            }
        }

        if ($type === 'repaid') {
            $funded = $transactions
                ->where('funding_source', $source)
                ->where('transaction_type', 'funded')
                ->sum(fn ($item) => (float) $item->amount);

            $repaid = $transactions
                ->where('funding_source', $source)
                ->where('transaction_type', 'repaid')
                ->sum(fn ($item) =>
                    $item->id === $transaction->id
                        ? 0
                        : (float) $item->amount
                );

            if ($repaid > $funded) {
                throw ValidationException::withMessages([
                    'funding' => [
                        'This reimbursement cannot be deleted because other reimbursements already exceed the available funding.',
                    ],
                ]);
            }
        }

        $transaction->delete();
    }, 3);

    return response()->json([
        'success' => true,
        'message' => 'Funding transaction deleted successfully.',
    ]);
}

    /**
     * Total harvesting expenses, including other expenses.
     */
    private function harvestTotal(CropHarvest $harvest): float
    {
        return round(
            (float) $harvest->labour_cost
            + (float) $harvest->transport_cost
            + (float) $harvest->other_cost,
            2
        );
    }

    /**
     * Calculate each source's contributions, repayments and balance.
     */
    private function calculateBalances($transactions): array
    {
        return $transactions
            ->groupBy('funding_source')
            ->map(function ($items, $source) {
                $funded = $items
                    ->where('transaction_type', 'funded')
                    ->sum(fn ($item) => (float) $item->amount);

                $repaid = $items
                    ->where('transaction_type', 'repaid')
                    ->sum(fn ($item) => (float) $item->amount);

                return [
                    'funding_source' => $source,
                    'total_funded' => round($funded, 2),
                    'total_repaid' => round($repaid, 2),
                    'outstanding' => round($funded - $repaid, 2),
                ];
            })
            ->values()
            ->all();
    }    
}
