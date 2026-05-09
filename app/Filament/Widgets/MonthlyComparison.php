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

        // Query Sales per month (Invoices)
        $sales = Invoice::selectRaw('strftime("%m", created_at) as month, SUM(total_amount) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        // Query Collections per month (Receipt Vouchers)
        $collections = Voucher::selectRaw('strftime("%m", voucher_date) as month, SUM(amount) as total')
            ->where('type', 'Receipt')
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        // Map the data to ensure all 12 months are represented even if 0
        $salesData = collect(range(1, 12))->map(fn($m) => $sales[str_pad($m, 2, '0', STR_PAD_LEFT)] ?? 0);
        $collectionData = collect(range(1, 12))->map(fn($m) => $collections[str_pad($m, 2, '0', STR_PAD_LEFT)] ?? 0);

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
