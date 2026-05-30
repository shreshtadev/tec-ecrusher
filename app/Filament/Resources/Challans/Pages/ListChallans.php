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

                        $items = (clone $query)->whereBetween('created_at', [$start, $end])->get();

                        return ExportToExcelService::download(
                            $items,
                            null,
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

                        $items = (clone $query)->whereBetween('created_at', [$start, $end])->get();

                        return ExportToExcelService::download(
                            $items,
                            null,
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
                            ->whereBetween('created_at', [$start, $end])
                            ->get();

                        return ExportToExcelService::download(
                            $items,
                            null,
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
