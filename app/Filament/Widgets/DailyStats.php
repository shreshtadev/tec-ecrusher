<?php

namespace App\Filament\Widgets;

use App\Domains\Accounting\Models\Voucher;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Override;

class DailyStats extends StatsOverviewWidget
{
    #[Override]
    protected function getColumns(): int|array|null
    {
        return 3;
    }

    protected function getStats(): array
    {
        $today = Carbon::today();

        return [

            // TODAY SECTION
            Stat::make('Today\'s Trips', Challan::whereDate('created_at', $today)->count())
                ->description('Active Challans')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make(
                'Today\'s Sales',
                '₹'.number_format(
                    Invoice::whereDate('created_at', $today)->sum('total_amount'),
                    2
                )
            )
                ->description('Today Invoiced')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(
                'Today\'s Collections',
                '₹'.number_format(
                    Voucher::where('voucher_type', 'Receipt')
                        ->whereDate('date', $today)
                        ->sum('amount'),
                    2
                )
            )
                ->description('Today Receipts')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            // TOTAL SECTION
            Stat::make('Total Trips', Challan::count())
                ->description('All Challans')
                ->descriptionIcon('heroicon-m-truck')
                ->color('gray'),

            Stat::make(
                'Total Sales',
                '₹'.number_format(
                    Invoice::sum('total_amount'),
                    2
                )
            )
                ->description('Lifetime Sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(
                'Total Collections',
                '₹'.number_format(
                    Voucher::where('voucher_type', 'Receipt')->sum('amount'),
                    2
                )
            )
                ->description('Lifetime Receipts')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
        ];
    }
}
