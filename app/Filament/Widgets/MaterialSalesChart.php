<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MaterialSalesChart extends ChartWidget
{
    protected ?string $heading = 'Revenue by Material (Current Month)';

    // Limits the size on the dashboard so it fits next to other widgets
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get sales grouped by Item ID for the current month
        $data = DB::table('invoices')
            ->join('challans', 'invoices.id', '=', 'challans.invoice_id')
            ->join('items', 'challans.item_id', '=', 'items.id')
            ->whereMonth('invoices.created_at', now()->month)
            ->whereYear('invoices.created_at', now()->year)
            ->select('items.material_name', DB::raw('SUM(invoices.total_amount) as total_revenue'))
            ->groupBy('items.material_name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->pluck('total_revenue')->toArray(),
                    'backgroundColor' => [
                        '#36A2EB', // Blue
                        '#FF6384', // Red
                        '#FFCE56', // Yellow
                        '#4BC0C0', // Teal
                        '#9966FF', // Purple
                    ],
                ],
            ],
            'labels' => $data->pluck('material_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
