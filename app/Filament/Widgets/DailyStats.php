<?php

namespace App\Filament\Widgets;

use App\Domains\Accounting\Models\Voucher;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class DailyStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();

        return [
            Stat::make('Today\'s Trips', Challan::whereDate('created_at', $today)->count())
                ->description('Active Challans')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Today\'s Sales', '₹' . number_format(Invoice::whereDate('created_at', $today)->sum('total_amount'), 2))
                ->description('Total Invoiced Value')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Today\'s Collections', '₹' . number_format(Voucher::where('type', 'Receipt')->whereDate('date', $today)->sum('amount'), 2))
                ->description('Cash/Bank Inflow')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
        ];
    }
}
