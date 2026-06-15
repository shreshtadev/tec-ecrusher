<?php

namespace App\Filament\Pages;

use App\Enums\NavigGroup;
use App\Services\StockReportingService;
use App\Models\Item;
use App\Models\Warehouse;
use Carbon\Carbon;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class StockReport extends Page implements HasSchemas
{
    use HasPageShield, InteractsWithSchemas;

    protected string $view = 'filament.pages.stock-report';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Reports;

    public ?array $data = [];

    public array $reportData = [];

    public function mount(): void
    {
        $this->form->fill([
            'period' => 'today',
        ]);

        $this->loadReport();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->options(
                        Warehouse::query()->orderBy('name')->pluck('name', 'id')
                    )
                    ->searchable()
                    ->live(),

                Select::make('item_id')
                    ->label('Item')
                    ->options(
                        Item::query()->orderBy('material_name')->pluck('material_name', 'id')
                    )
                    ->searchable()
                    ->live(),

                Select::make('period')
                    ->options([
                        'today' => 'Today',
                        'week' => 'This Week',
                        'month' => 'This Month',
                        'custom' => 'Custom Range',
                    ])
                    ->default('today')
                    ->live(),

                DatePicker::make('from_date'),

                DatePicker::make('to_date'),
            ])
            ->columns(4);
    }

    public function updatedData(): void
    {
        $this->loadReport();
    }

    protected function getDateRange(): array
    {
        $period = $this->data['period'] ?? 'today';

        return match ($period) {
            'today' => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'custom' => [
                isset($this->data['from_date']) ? Carbon::parse($this->data['from_date']) : Carbon::now()->startOfDay(),
                isset($this->data['to_date']) ? Carbon::parse($this->data['to_date'])->endOfDay() : Carbon::now()->endOfDay(),
            ],
        };
    }

    public function loadReport(): void
    {
        $service = app(StockReportingService::class);

        [$from, $to] = $this->getDateRange();

        $warehouse = null;
        if (! empty($this->data['warehouse_id'])) {
            $warehouseId = $this->extractId($this->data['warehouse_id']);
            $warehouse = $warehouseId ? Warehouse::find($warehouseId) : null;
        }

        $item = null;
        if (! empty($this->data['item_id'])) {
            $itemId = $this->extractId($this->data['item_id']);
            $item = $itemId ? Item::find($itemId) : null;
        }

        // Stock levels (optionally filtered by warehouse)
        $stockLevels = collect($service->getStockLevelReport($warehouse));

        $totalValue = $stockLevels->sum('stock_value');
        $totalItems = $stockLevels->count();

        // Movements within date range
        $movements = collect($service->getMovementReport($from, $to, $item, $warehouse));

        // Aging for selected item+warehouse
        $aging = collect();
        if ($item instanceof Item && $warehouse instanceof Warehouse) {
            $aging = $service->getStockAgingReport($item, $warehouse);
        }

        // Item costing if item selected
        $costing = collect();
        if ($item instanceof Item) {
            $costing = $service->getItemCostingReport($item);
        }

        $this->reportData = [
            'stock_levels' => $stockLevels,
            'total_value' => $totalValue,
            'total_items' => $totalItems,
            'movements' => $movements,
            'aging' => $aging,
            'costing' => $costing,
        ];
    }

    /**
     * Extract numeric id from various possible values (scalar, array, stdClass).
     */
    private function extractId(mixed $value): ?int
    {
        if (is_null($value)) {
            return null;
        }

        if (is_object($value)) {
            foreach (['id', 'value', 'key'] as $k) {
                if (isset($value->{$k})) {
                    return is_numeric($value->{$k}) ? (int) $value->{$k} : null;
                }
            }
            return null;
        }

        if (is_array($value)) {
            foreach (['id', 'value', 'key'] as $k) {
                if (isset($value[$k])) {
                    return is_numeric($value[$k]) ? (int) $value[$k] : null;
                }
            }
            return null;
        }

        // scalar
        return is_numeric($value) ? (int) $value : null;
    }
}
