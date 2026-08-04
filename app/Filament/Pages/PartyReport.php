<?php

namespace App\Filament\Pages;

use App\Enums\NavigGroup;
use App\Enums\PaymentOpts;
use App\Models\Challan;
use App\Models\ChallanItem;
use App\Models\Invoice;
use App\Models\Party;
use App\Services\ExportToExcelService;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PartyReport extends Page implements HasSchemas
{
    use HasPageShield, InteractsWithSchemas;

    protected string $view = 'filament.pages.party-report';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Reports;

    public ?array $data = [];

    public array $reportData = [];

    public function mount(): void
    {
        $this->form->fill([
            'period' => 'month',
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
                    ->placeholder('All Parties')
                    ->options(
                        Party::query()
                            ->has('challans')
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
                    ->default('month')
                    ->live(),

                DatePicker::make('from_date')
                    ->visible(fn() => ($this->data['period'] ?? '') === 'custom'),

                DatePicker::make('to_date')
                    ->visible(fn() => ($this->data['period'] ?? '') === 'custom'),

                Select::make('payment_status')
                    ->label('Payment Status')
                    ->placeholder('Any Status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'partial' => 'Partial',
                        'paid' => 'Paid',
                    ])
                    ->live(),

                Select::make('payment_mode')
                    ->label('Payment Mode')
                    ->placeholder('Any Mode')
                    ->options(PaymentOpts::options())
                    ->live(),

            ])
            ->columns(3);
    }

    public function updatedData(): void
    {
        $this->loadReport();
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([

                Action::make('download_party_summary')
                    ->label('Party Summary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        [$from, $to] = $this->getDateRange();

                        $parties = Party::query()
                            ->has('challans')
                            ->orderBy('full_name')
                            ->get();

                        $rows = $parties->map(function (Party $party) use ($from, $to) {
                            $invoiceQuery = Invoice::query()
                                ->where('party_id', $party->id)
                                ->whereBetween('invoice_date', [$from, $to]);

                            $this->applyInvoiceFilters($invoiceQuery);

                            $challanItemQuery = ChallanItem::query()
                                ->join('challans', 'challans.id', '=', 'challan_items.challan_id')
                                ->join('invoices', 'invoices.id', '=', 'challans.invoice_id')
                                ->where('invoices.party_id', $party->id)
                                ->whereBetween('invoices.invoice_date', [$from, $to]);

                            $invoiceCount = (clone $invoiceQuery)->count();
                            $totalAmount = (clone $invoiceQuery)->sum('total_amount');
                            $outstanding = (clone $invoiceQuery)->sum('outstanding_amount');
                            $totalQty = (clone $challanItemQuery)->sum('challan_items.quantity_cft');
                            $largestInvoice = (clone $invoiceQuery)->max('total_amount');

                            return [
                                'party' => $party->full_name,
                                'city' => $party->city,
                                'party_type' => $party->party_type,
                                'invoice_count' => $invoiceCount,
                                'total_qty_cft' => round((float) $totalQty, 2),
                                'total_amount' => round((float) $totalAmount, 2),
                                'outstanding' => round((float) $outstanding, 2),
                                'largest_invoice' => round((float) $largestInvoice, 2),
                            ];
                        })->filter(fn($row) => $row['invoice_count'] > 0)->values();

                        $fileName = 'party-summary-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.xlsx';

                        return ExportToExcelService::download(
                            $rows,
                            [
                                'party' => 'Party',
                                'city' => 'City',
                                'party_type' => 'Type',
                                'invoice_count' => 'Invoices',
                                'total_qty_cft' => 'Total Qty (CFT)',
                                'total_amount' => 'Total Amount (₹)',
                                'outstanding' => 'Outstanding (₹)',
                                'largest_invoice' => 'Largest Invoice (₹)',
                            ],
                            'Party Summary',
                            $fileName,
                        );
                    }),

                Action::make('download_invoices')
                    ->label('Invoices Detail')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        [$from, $to] = $this->getDateRange();

                        $invoiceQuery = Invoice::query()
                            ->with('party')
                            ->whereBetween('invoice_date', [$from, $to])
                            ->orderByDesc('invoice_date')
                            ->orderByDesc('id');

                        if (! blank($this->data['party_id'] ?? null)) {
                            $invoiceQuery->where('party_id', $this->data['party_id']);
                        }

                        $this->applyInvoiceFilters($invoiceQuery);

                        $rows = $invoiceQuery->get()->map(fn(Invoice $inv) => [
                            'invoice_date' => date('d-m-Y', strtotime($inv->invoice_date)),
                            'invoice_number' => $inv->invoice_number,
                            'party' => $inv->party?->full_name,
                            'payment_mode' => $inv->payment_mode,
                            'payment_status' => $inv->payment_status,
                            'total_amount' => $inv->total_amount,
                            'discount_amount' => $inv->discount_amount,
                            'grand_total' => $inv->grand_total,
                            'outstanding_amount' => $inv->outstanding_amount,
                            'driver_bata' => $inv->driver_bata,
                        ]);

                        $partyLabel = ! blank($this->data['party_id'] ?? null)
                            ? '-' . str(Party::find($this->data['party_id'])?->full_name ?? '')->slug()
                            : '';

                        $fileName = 'invoices' . $partyLabel . '-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.xlsx';

                        return ExportToExcelService::download(
                            $rows,
                            [
                                'invoice_date' => 'Invoice Date',
                                'invoice_number' => 'Invoice No',
                                'party' => 'Party',
                                'payment_mode' => 'Payment Mode',
                                'payment_status' => 'Payment Status',
                                'total_amount' => 'Total Amount (₹)',
                                'discount_amount' => 'Discount (₹)',
                                'outstanding_amount' => 'Outstanding (₹)',
                                'driver_bata' => 'Driver Bata (₹)',
                            ],
                            'Invoices',
                            $fileName,
                        );
                    }),

                Action::make('download_challan_items_grouped')
                    ->label('Challan Details')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        [$from, $to] = $this->getDateRange();

                        $challanItemQuery = ChallanItem::query()
                            ->select([
                                'challan_items.challan_id',
                                'challan_items.quantity_cft',
                                'challans.challan_number',
                                'challans.challan_date',
                                'challans.party_id',
                                'challans.payment_mode',
                                'challans.status',
                                'challans.driver_bata',
                                'invoices.invoice_number',
                                'invoices.total_amount',
                                'parties.full_name as party_name',
                                'vehicles.vehicle_number',
                                'drivers.full_name as driver_name',
                                'items.material_name',
                                'items.unit',
                            ])
                            ->join('challans', 'challans.id', '=', 'challan_items.challan_id')
                            ->leftJoin('invoices', 'invoices.id', '=', 'challans.invoice_id')
                            ->leftJoin('parties', 'parties.id', '=', 'challans.party_id')
                            ->leftJoin('vehicles', 'vehicles.id', '=', 'challans.vehicle_id')
                            ->leftJoin('drivers', 'drivers.id', '=', 'challans.driver_id')
                            ->leftJoin('items', 'items.id', '=', 'challan_items.item_id')
                            ->whereBetween('challans.challan_date', [$from, $to])
                            ->orderByDesc('challans.challan_date')
                            ->orderByDesc('challans.id')
                            ->orderBy('challan_items.id');

                        if (! blank($this->data['party_id'] ?? null)) {
                            $challanItemQuery->where('challans.party_id', $this->data['party_id']);
                        }

                        if (! blank($this->data['payment_mode'] ?? null)) {
                            $challanItemQuery->where('challans.payment_mode', $this->data['payment_mode']);
                        }

                        if (! blank($this->data['payment_status'] ?? null)) {
                            $challanItemQuery->where('invoices.payment_status', $this->data['payment_status']);
                        }

                        $rows = $challanItemQuery
                            ->get()
                            ->map(function ($item) {
                                return [
                                    'challan_date' => $item->challan_date ? date('Y-M-d h:i A', strtotime($item->challan_date)) : '-',
                                    'challan_number' => $item->challan_number ?? '-',
                                    'party' => $item->party_name ?? '-',
                                    'vehicle' => $item->vehicle_number ?? '-',
                                    'driver' => $item->driver_name ?? '-',
                                    'item' => $item->material_name ?? '-',
                                    'quantity' => $item->quantity_cft ?? 0,
                                    'unit' => $item->unit ?? '-',
                                    'payment_mode' => $item->payment_mode ?? '-',
                                    'status' => $item->status ?? '-',
                                    'driver_bata' => $item->driver_bata ?? 0,
                                    'invoice_number' => $item->invoice_number ?? '-',
                                    'total_amount' => $item->total_amount ?? 0,
                                ];
                            })
                            ->values();

                        $partyLabel = ! blank($this->data['party_id'] ?? null)
                            ? '-' . str(Party::find($this->data['party_id'])?->full_name ?? '')->slug()
                            : '';

                        $fileName = 'challan-items-grouped' . $partyLabel . '-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.xlsx';

                        return ExportToExcelService::download(
                            $rows,
                            [
                                'challan_date' => 'Challan Date',
                                'challan_number' => 'Challan No',
                                'party' => 'Party',
                                'vehicle' => 'Vehicle',
                                'driver' => 'Driver',
                                'item' => 'Item',
                                'quantity' => 'Quantity',
                                'unit' => 'Unit',
                                'payment_mode' => 'Payment Mode',
                                'total_amount' => 'Total Amount (₹)',
                                'status' => 'Status',
                                'driver_bata' => 'Driver Bata (₹)',
                            ],
                            'All challan items by Challan',
                            $fileName,
                        );
                    }),

                Action::make('download_challans')
                    ->label('Agg. Challans Detail')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        [$from, $to] = $this->getDateRange();

                        $challanQuery = Challan::query()
                            ->with(['party', 'vehicle', 'driver', 'items', 'invoice'])
                            ->whereBetween('challan_date', [$from, $to])
                            ->orderByDesc('challan_date')
                            ->orderByDesc('id');

                        if (! blank($this->data['party_id'] ?? null)) {
                            $challanQuery->where('party_id', $this->data['party_id']);
                        }

                        if (! blank($this->data['payment_mode'] ?? null)) {
                            $challanQuery->where('payment_mode', $this->data['payment_mode']);
                        }

                        $rows = $challanQuery->get()->map(fn(Challan $c) => [
                            'challan_date' => date('Y-M-d h:i A', strtotime($c->challan_date)),
                            'challan_number' => $c->challan_number,
                            'party' => $c->party?->full_name,
                            'vehicle' => $c->vehicle?->vehicle_number ?? '-',
                            'driver' => $c->driver?->full_name ?? '-',
                            'item' => $c->challan_items?->map(fn($challanItem) => ($challanItem->item?->material_name ?? '-') . ' (' . ($challanItem->quantity_cft ?? 0) . ' ' . ($challanItem->item?->unit ?? '-') . ')')?->join(', ') ?? '-',
                            'payment_mode' => $c->payment_mode,
                            'status' => $c->status,
                            'driver_bata' => $c->driver_bata,
                            'invoice_number' => $c->invoice?->invoice_number ?? '-',
                            'total_amount' => $c->invoice?->total_amount ?? 0,
                        ]);

                        $partyLabel = ! blank($this->data['party_id'] ?? null)
                            ? '-' . str(Party::find($this->data['party_id'])?->full_name ?? '')->slug()
                            : '';

                        $fileName = 'challans' . $partyLabel . '-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.xlsx';

                        return ExportToExcelService::download(
                            $rows,
                            [
                                'challan_date' => 'Challan Date',
                                'challan_number' => 'Challan No',
                                'party' => 'Party',
                                'vehicle' => 'Vehicle',
                                'driver' => 'Driver',
                                'item' => 'Item',
                                'payment_mode' => 'Payment Mode',
                                'total_amount' => 'Total Amount (₹)',
                                'status' => 'Status',
                                'driver_bata' => 'Driver Bata (₹)',
                            ],
                            'Challans',
                            $fileName,
                        );
                    }),

            ])
                ->label('Download Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->button(),
        ];
    }

    protected function getDateRange(): array
    {
        $period = $this->data['period'] ?? 'month';

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
                Carbon::parse($this->data['from_date'] ?? now()->startOfMonth()),
                Carbon::parse($this->data['to_date'] ?? now()->endOfMonth())->endOfDay(),
            ],
        };
    }

    /**
     * Apply invoice-level filters (payment_status, payment_mode) to a query builder.
     */
    protected function applyInvoiceFilters(Builder $query): void
    {
        if (! blank($this->data['payment_status'] ?? null)) {
            $query->where('payment_status', $this->data['payment_status']);
        }

        if (! blank($this->data['payment_mode'] ?? null)) {
            $query->where('payment_mode', $this->data['payment_mode']);
        }
    }

    public function loadReport(): void
    {
        [$from, $to] = $this->getDateRange();

        $partyId = $this->data['party_id'] ?? null;

        $invoiceQuery = Invoice::query()
            ->whereBetween('invoice_date', [$from, $to]);

        if (! blank($partyId)) {
            $invoiceQuery->where('party_id', $partyId);
        }

        $this->applyInvoiceFilters($invoiceQuery);

        $challanItemQuery = ChallanItem::query()
            ->join('challans', 'challans.id', '=', 'challan_items.challan_id')
            ->join('invoices', 'invoices.id', '=', 'challans.invoice_id')
            ->whereBetween('invoices.invoice_date', [$from, $to]);

        if (! blank($partyId)) {
            $challanItemQuery->where('invoices.party_id', $partyId);
        }

        if (! blank($this->data['payment_status'] ?? null)) {
            $challanItemQuery->where('invoices.payment_status', $this->data['payment_status']);
        }

        if (! blank($this->data['payment_mode'] ?? null)) {
            $challanItemQuery->where('invoices.payment_mode', $this->data['payment_mode']);
        }

        $itemWiseSales = (clone $challanItemQuery)
            ->join('items', 'items.id', '=', 'challan_items.item_id')
            ->selectRaw('
                challan_items.item_id,
                items.material_name,
                items.price_per_unit,
                SUM(challan_items.quantity_cft) as total_qty,
                SUM(challan_items.amount) as total_amount
            ')
            ->groupBy(
                'challan_items.item_id',
                'items.material_name',
                'items.price_per_unit'
            )
            ->get();

        $invoices = (clone $invoiceQuery)
            ->with('party')
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->get();

        $this->reportData = [
            'party_id' => $partyId,
            'invoice_count' => (clone $invoiceQuery)->count(),
            'total_amount' => (clone $invoiceQuery)->sum('total_amount'),
            'grand_total' => (clone $invoiceQuery)->sum('grand_total'),
            'outstanding_amount' => (clone $invoiceQuery)->sum('outstanding_amount'),
            'total_qty' => (clone $challanItemQuery)->sum('challan_items.quantity_cft'),
            'average_invoice_value' => (clone $invoiceQuery)->avg('total_amount'),
            'largest_invoice' => (clone $invoiceQuery)->max('total_amount'),
            'invoices' => $invoices,
            'item_sales' => $itemWiseSales,
        ];
    }
}
