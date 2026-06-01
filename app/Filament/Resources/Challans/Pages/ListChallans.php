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
use Filament\Notifications\Notification;

class ListChallans extends ListRecords
{
    protected static string $resource = ChallanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->failureNotification(Notification::make()
                ->danger()
                ->title('Tripsheet/Stock Error')
                ->body('We could not save your submission. Please check inventory.'),),
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
                            ->whereBetween('created_at', [$start, $end])
                            ->get();

                        return ExportToExcelService::download(
                            $items,
                            TripsheetColumnDef::columns(),
                            'Tripsheets - Day',
                            'tripsheets-day-' . Carbon::now()->format('Y-m-d') . '.xlsx'
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
                            ->whereBetween('created_at', [$start, $end])
                            ->get();
                        return ExportToExcelService::download(
                            $items,
                            TripsheetColumnDef::columns(),
                            'Tripsheets - Week',
                            'tripsheets-week-' . Carbon::now()->format('Y-m-d') . '.xlsx'
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

                        return ExportToExcelService::download(
                            $items,
                            TripsheetColumnDef::columns(),
                            'Tripsheets',
                            sprintf(
                                'tripsheets-%s-to-%s.xlsx',
                                $start->format('Y-m-d'),
                                $end->format('Y-m-d')
                            )
                        );
                    })
            ]),
        ];
    }
}
