<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DayBookService
{
    /**
     * Get the opening balance, closing balance, and vouchers within a date range for a specific account.
     *
     * @return array{
     *     opening_balance: float,
     *     closing_balance: float,
     *     vouchers: Collection<int, Voucher>
     * }
     */
    public function getAccountReport(Account $account, Carbon $fromDate, Carbon $toDate): array
    {
        // 1. Calculate historical balance change after the selected date range.
        // We start from the account's CURRENT balance in the database and wind back the changes.

        // Sum of all inflows to this account on or after fromDate
        $inflowsAfterFromDate = Voucher::where('to_account_id', $account->id)
            ->where('voucher_date', '>=', $fromDate->toDateString())
            ->sum('amount');

        // Sum of all outflows from this account on or after fromDate
        $outflowsAfterFromDate = Voucher::where('from_account_id', $account->id)
            ->where('voucher_date', '>=', $fromDate->toDateString())
            ->sum('amount');

        // Opening Balance = Current Balance - Inflows(>=fromDate) + Outflows(>=fromDate)
        $openingBalance = (float) $account->balance - (float) $inflowsAfterFromDate + (float) $outflowsAfterFromDate;

        // 2. Fetch all vouchers within the specified date range.
        $vouchers = Voucher::with(['party', 'fromAccount', 'toAccount', 'invoice'])
            ->where(function ($query) use ($account) {
                $query->where('from_account_id', $account->id)
                    ->orWhere('to_account_id', $account->id);
            })
            ->whereBetween('voucher_date', [
                $fromDate->toDateString(),
                $toDate->toDateString(),
            ])
            ->orderBy('voucher_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 3. Calculate closing balance.
        // Closing Balance = Opening Balance + Inflows(within range) - Outflows(within range)
        $inflowsInRange = 0.0;
        $outflowsInRange = 0.0;

        foreach ($vouchers as $voucher) {
            if ($voucher->to_account_id === $account->id) {
                $inflowsInRange += (float) $voucher->amount;
            }
            if ($voucher->from_account_id === $account->id) {
                $outflowsInRange += (float) $voucher->amount;
            }
        }

        $closingBalance = $openingBalance + $inflowsInRange - $outflowsInRange;

        return [
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'vouchers' => $vouchers,
        ];
    }
}
