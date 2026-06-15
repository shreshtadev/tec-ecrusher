<?php

namespace App\Filament\Resources\Challans\Pages;

use App\Enums\TripsheetColumnDef;
use App\Filament\Resources\Challans\ChallanResource;
use App\Models\Party;
use App\Models\StockLevel;
use App\Services\ExportToExcelService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListChallans extends ListRecords
{
    protected static string $resource = ChallanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->failureNotification(Notification::make()
                    ->danger()
                    ->title('Tripsheet/Stock Error')
                    ->body('We could not save your submission. Please check inventory.'),)->before(function (Action $action) {
                    $data = $action->getLivewire()->form->getState();

                    $itemId = $data['item_id'] ?? null;
                    $quantity = $data['quantity_cft'] ?? null;

                    if ($itemId && $quantity) {
                        $availableStock = StockLevel::where('item_id', $itemId)->pluck('available_qty')->first();

                        if ($quantity > $availableStock) {
                            return false; // Prevent form submission
                        }
                    }

                    return true; // Allow form submission
                })->visible(
                    fn() => StockLevel::where('available_qty', '>', 0)->exists()
                ),
            ActionGroup::make([
                Action::make('export_by_day')
                    ->label('Tripsheet - Today')
                    ->action(function () {
                        $query = $this->getFilteredTableQuery();

                        $start = Carbon::now()->startOfDay();
                        $end = Carbon::now()->endOfDay();

                        $items = (clone $query)
                            ->with([
                                'party',
                                'driver',
                                'vehicle',
                                'item',
                                'invoice',
                            ])
                            ->whereBetween('challan_date', [$start, $end])
                            ->get();

                        return ExportToExcelService::download(
                            $items,
                            TripsheetColumnDef::columns(),
                            'Tripsheets - Day',
                            'tripsheets-day-' . Carbon::now()->format('Y-m-d') . '.xlsx',
                            true
                        );
                    }),

                Action::make('export_by_week')
                    ->label('Tripsheet - Week')
                    ->action(function () {
                        $query = $this->getFilteredTableQuery();

                        $start = Carbon::now()->startOfWeek();
                        $end = Carbon::now()->endOfWeek();

                        $items = (clone $query)
                            ->with([
                                'party',
                                'driver',
                                'vehicle',
                                'item',
                                'invoice',
                            ])
                            ->whereBetween('challan_date', [$start, $end])
                            ->get();

                        return ExportToExcelService::download(
                            $items,
                            TripsheetColumnDef::columns(),
                            'Tripsheets - Week',
                            'tripsheets-week-' . Carbon::now()->format('Y-m-d') . '.xlsx',
                            true
                        );
                    }),
                Action::make('export_custom')
                    ->label('Custom Range')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        DatePicker::make('start_date')
                            ->required()
                            ->native(false),

                        DatePicker::make('end_date')
                            ->required()
                            ->native(false)
                            ->afterOrEqual('start_date'),
                    ])
                    ->action(function (array $data) {

                        $query = $this->getFilteredSortedTableQuery();

                        $start = Carbon::parse($data['start_date'])->startOfDay();
                        $end = Carbon::parse($data['end_date'])->endOfDay();

                        $items = (clone $query)
                            ->with([
                                'party',
                                'driver',
                                'vehicle',
                                'item',
                                'invoice',
                            ])
                            ->whereBetween('challan_date', [$start, $end])
                            ->get();

                        return ExportToExcelService::download(
                            $items,
                            TripsheetColumnDef::columns(),
                            'Tripsheets',
                            sprintf(
                                'tripsheets-%s-to-%s.xlsx',
                                $start->format('Y-m-d'),
                                $end->format('Y-m-d')
                            ),
                            true
                        );
                    }),
                Action::make('export_by_party')
                    ->label('Tripsheets - Party')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Select::make('party_id')
                            ->label('Party')
                            ->options(Party::orderBy('full_name')->pluck('full_name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                        DatePicker::make('start_date')
                            ->native(false)
                            ->label('From'),
                        DatePicker::make('end_date')
                            ->native(false)
                            ->label('To')
                            ->afterOrEqual('start_date'),
                    ])
                    ->action(function (array $data) {
                        $query = $this->getFilteredSortedTableQuery();

                        $start = isset($data['start_date']) ? Carbon::parse($data['start_date'])->startOfDay() : null;
                        $end = isset($data['end_date']) ? Carbon::parse($data['end_date'])->endOfDay() : null;

                        $itemsQuery = (clone $query)->with(['party', 'driver', 'vehicle', 'invoice', 'challan_items.item']);

                        $itemsQuery->where('party_id', $data['party_id']);

                        if ($start && $end) {
                            $itemsQuery->whereBetween('challan_date', [$start, $end]);
                        }

                        $challans = $itemsQuery->get();

                        // Build flat rows: one row per challan_item with challan-level columns repeated
                        $rows = new Collection();

                        foreach ($challans as $c) {
                            foreach ($c->challan_items as $ci) {
                                $rows->push([
                                    'date' => $c->created_at->toDateString(),
                                    'time' => $c->created_at->format('H:i'),
                                    'challan_number' => $c->challan_number,
                                    'party' => $c->party?->full_name ?? '',
                                    'vehicle' => $c->vehicle?->vehicle_number ?? '',
                                    'driver' => $c->driver?->full_name ?? '',
                                    'invoice' => $c->invoice?->invoice_number ?? '',
                                    'item' => $ci->item?->material_name ?? $ci->item_id,
                                    'quantity_cft' => $ci->quantity_cft,
                                    'rate_at_sale' => $ci->rate_at_sale,
                                    'amount' => $ci->amount,
                                ]);
                            }
                        }

                        $party = Party::find($data['party_id']);

                        $fileName = sprintf('tripsheets-%s-%s.xlsx', str_replace(' ', '_', $party?->full_name ?? 'party'), now()->format('Y-m-d'));

                        return ExportToExcelService::download($rows, TripsheetColumnDef::columnsByParty(), 'Tripsheets', $fileName, false);
                    }),
            ])->icon('heroicon-o-printer'),
        ];
    }
}
