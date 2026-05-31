<?php

namespace App\Filament\Resources\Challans\Pages;

use App\Filament\Resources\Challans\ChallanResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Domains\Common\Services\ExportToExcelService;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use App\Domains\Common\Enums\TripsheetColumnDef;

class ListChallans extends ListRecords
{
    protected static string $resource = ChallanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ActionGroup::make([
                Action::make('export_by_day')
                    ->label('By Day')
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
                            ->whereBetween('created_at', [$start, $end])
                            ->get();

                        return ExportToExcelService::download(
                            $items,
                            TripsheetColumnDef::columns(),
                            'Challans - Day',
                            'challans-day-' . Carbon::now()->format('Y-m-d') . '.xlsx'
                        );
                    }),

                Action::make('export_by_week')
                    ->label('By Week')
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
                            ->whereBetween('created_at', [$start, $end])
                            ->get();
                        return ExportToExcelService::download(
                            $items,
                            TripsheetColumnDef::columns(),
                            'Challans - Week',
                            'challans-week-' . Carbon::now()->format('Y-m-d') . '.xlsx'
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
                            ->whereBetween('created_at', [$start, $end])
                            ->get();
                        // $items = $items->map(function ($challan) {
                        //     return [
                        //         'Challan No' => $challan->challan_number,
                        //         'Date' => $challan->created_at->format('Y-m-d'),

                        //         'Party' => $challan->party?->full_name,
                        //         'Driver' => $challan->driver?->full_name,
                        //         'Vehicle' => $challan->vehicle?->vehicle_number,
                        //         'Item' => $challan->item?->material_name,
                        //         'Invoice' => $challan->invoice?->invoice_number,

                        //         'Quantity' => $challan->quantity_qft,
                        //     ];
                        // });

                        return ExportToExcelService::download(
                            $items,
                            TripsheetColumnDef::columns(),
                            'Challans',
                            sprintf(
                                'challans-%s-to-%s.xlsx',
                                $start->format('Y-m-d'),
                                $end->format('Y-m-d')
                            )
                        );
                    })
            ]),
        ];
    }
}
