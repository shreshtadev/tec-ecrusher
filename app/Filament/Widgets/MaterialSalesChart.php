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
        $data = DB::table('invoices')
            ->join('challans', 'invoices.id', '=', 'challans.invoice_id')
            ->join('challan_items', 'challans.id', '=', 'challan_items.challan_id')
            ->join('items', 'challan_items.item_id', '=', 'items.id')
            ->whereMonth('invoices.created_at', now()->month)
            ->whereYear('invoices.created_at', now()->year)
            ->select('items.material_name', DB::raw('SUM(invoices.total_amount) as total_revenue'))
            ->groupBy('items.material_name')
            ->get();

        // If no data, return a placeholder to prevent chart crashes
        if ($data->isEmpty()) {
            return [
                'datasets' => [['label' => 'No Data', 'data' => [0], 'backgroundColor' => ['#cbd5e1']]],
                'labels' => ['No sales this month'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->pluck('total_revenue')->toArray(),
                    'backgroundColor' => ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF'],
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
