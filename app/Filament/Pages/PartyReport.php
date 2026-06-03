<?php

namespace App\Filament\Pages;

use App\Domains\Common\Enums\NavigGroup;
use App\Domains\Master\Models\Party;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use UnitEnum;

class PartyReport extends Page implements HasSchemas
{
    use InteractsWithSchemas, HasPageShield;
    protected string $view = 'filament.pages.party-report';
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

                Select::make('party_id')
                    ->label('Party')
                    ->options(
                        Party::query()
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
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

            'today' => [
                now()->startOfDay(),
                now()->endOfDay(),
            ],

            'week' => [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ],

            'month' => [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ],

            'custom' => [
                Carbon::parse($this->data['from_date']),
                Carbon::parse($this->data['to_date'])->endOfDay(),
            ],
        };
    }

    public function loadReport(): void
    {
        if (empty($this->data['party_id'])) {
            return;
        }

        [$from, $to] = $this->getDateRange();

        $baseQuery = Invoice::query()
            ->where('party_id', $this->data['party_id'])
            ->whereBetween('created_at', [$from, $to]);

        $itemWiseSales = Challan::query()
            ->join('items', 'items.id', '=', 'challans.item_id')
            ->join('invoices', 'invoices.id', '=', 'challans.invoice_id')
            ->selectRaw('
        challans.item_id,
        items.material_name,
        items.price_per_unit,
        SUM(challans.quantity_cft) as total_qty,
        SUM(challans.quantity_cft * items.price_per_unit) as total_amount
    ')
            ->where('invoices.party_id', $this->data['party_id'])
            ->whereBetween('invoices.created_at', [$from, $to])
            ->groupBy(
                'challans.item_id',
                'items.material_name',
                'items.price_per_unit'
            )
            ->get();

        $this->reportData = [

            'invoice_count' => (clone $baseQuery)->count(),

            'total_amount' => (clone $baseQuery)->sum('total_amount'),

            'total_qty' => Challan::query()
                ->whereHas('invoice', function ($query) use ($from, $to) {
                    $query
                        ->where('party_id', $this->data['party_id'])
                        ->whereBetween('created_at', [$from, $to]);
                })
                ->sum('quantity_cft'),

            'invoices' => (clone $baseQuery)
                ->select([
                    'invoice_number',
                    'created_at',
                    'total_amount',
                    'party_id',
                ])
                ->latest()
                ->get(),
            'average_invoice_value' => (clone $baseQuery)->avg('total_amount'),

            'largest_invoice' => (clone $baseQuery)->max('total_amount'),
            'item_sales' => $itemWiseSales,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('view_party_report');
    }
}
