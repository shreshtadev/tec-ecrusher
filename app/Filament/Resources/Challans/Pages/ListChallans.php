<?php

namespace App\Filament\Resources\Challans\Pages;

use App\Filament\Resources\Challans\ChallanResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Domains\Common\Services\ExportToExcelService;
use Carbon\Carbon;

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
                        $query = $this->getTableQuery();

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
                        $query = $this->getTableQuery();

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
            ])
        ];
    }
}
