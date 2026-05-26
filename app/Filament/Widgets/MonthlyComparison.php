<?php

namespace App\Filament\Widgets;

use App\Domains\Operations\Models\Invoice;
use App\Domains\Accounting\Models\Voucher;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class MonthlyComparison extends ChartWidget
{
    protected ?string $heading = 'Monthly Sales vs Collections';

    protected function getData(): array
    {
        $months = collect(range(1, 12))->map(fn($month) => Carbon::create(null, $month, 1)->format('M'));
        $sales = Invoice::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        // Query Collections - Changed strftime to MONTH and fixed column names
        $collections = Voucher::selectRaw('MONTH(voucher_date) as month, SUM(amount) as total')
            ->where('voucher_type', 'Receipt')
            ->whereYear('voucher_date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        // Map data (The key needs to be the integer month if using MONTH())
        $salesData = collect(range(1, 12))->map(fn($m) => $sales[$m] ?? 0);
        $collectionData = collect(range(1, 12))->map(fn($m) => $collections[$m] ?? 0);

        return [
            'datasets' => [
                [
                    'label' => 'Sales (Invoices)',
                    'data' => $salesData,
                    'borderColor' => '#3b82f6', // Blue
                    'fill' => 'start',
                ],
                [
                    'label' => 'Collections (Receipts)',
                    'data' => $collectionData,
                    'borderColor' => '#10b981', // Green
                    'fill' => 'start',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
