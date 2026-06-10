<?php

namespace App\Filament\Resources\Vouchers\Pages;

use App\Enums\VoucherColumnDef;
use App\Filament\Resources\Vouchers\VoucherResource;
use App\Services\ExportToExcelService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ActionGroup::make([
                Action::make('export_by_day')
                    ->label('Vouchers - Today')
                    ->action(function () {
                        $query = $this->getFilteredTableQuery();

                        $start = Carbon::now()->startOfDay();
                        $end = Carbon::now()->endOfDay();

                        $items = (clone $query)
                            ->with([
                                'party',
                                'invoice',
                            ])
                            ->whereBetween('created_at', [$start, $end])
                            ->get();

                        return ExportToExcelService::download(
                            $items,
                            VoucherColumnDef::columns(),
                            'Vouchers - Today',
                            'vouchers-day-'.Carbon::now()->format('Y-m-d').'.xlsx',
                            false
                        );
                    }),

                Action::make('export_by_week')
                    ->label('Vouchers - Week')
                    ->action(function () {
                        $query = $this->getFilteredTableQuery();

                        $start = Carbon::now()->startOfWeek();
                        $end = Carbon::now()->endOfWeek();

                        $items = (clone $query)
                            ->with([
                                'party',
                                'invoice',
                            ])
                            ->whereBetween('created_at', [$start, $end])
                            ->get();

                        return ExportToExcelService::download(
                            $items,
                            VoucherColumnDef::columns(),
                            'Vouchers - Week',
                            'vouchers-week-'.Carbon::now()->format('Y-m-d').'.xlsx',
                            false
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
                                'invoice',
                            ])
                            ->whereBetween('created_at', [$start, $end])
                            ->get();

                        return ExportToExcelService::download(
                            $items,
                            VoucherColumnDef::columns(),
                            'Vouchers',
                            sprintf(
                                'vouchers-%s-to-%s.xlsx',
                                $start->format('Y-m-d'),
                                $end->format('Y-m-d')
                            ),
                            false
                        );
                    }),
            ])->icon('heroicon-o-printer'),
        ];
    }
}
