<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Voucher;
use App\Services\DayBookService;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class DayBook extends TableWidget
{
    protected string $view = 'filament.widgets.day-book-widget';
    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'md' => 2,
        'xl' => 3,
    ];

    public ?Account $account = null;

    public array $balances = [];

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Voucher::query();

                // Retrieve active table filters
                $filters = $this->tableFilters ?? [];

                // Filter by Account if selected
                $accountId = $filters['account_id']['value'] ?? null;
                if ($accountId) {
                    $this->account = Account::find($accountId);
                    $query->where(function (Builder $q) use ($accountId) {
                        $q->where('from_account_id', $accountId)
                            ->orWhere('to_account_id', $accountId);
                    });
                } else {
                    $this->account = null;
                    $this->balances = [];
                }

                // Filter by Voucher Date Range
                $fromDateStr = $filters['voucher_date']['from_date'] ?? null;
                $toDateStr = $filters['voucher_date']['to_date'] ?? null;

                $fromDate = $fromDateStr ? Carbon::parse($fromDateStr) : Carbon::today()->startOfMonth();
                $toDate = $toDateStr ? Carbon::parse($toDateStr)->endOfDay() : Carbon::today()->endOfDay();

                if ($fromDateStr) {
                    $query->whereDate('voucher_date', '>=', $fromDate->toDateString());
                }
                if ($toDateStr) {
                    $query->whereDate('voucher_date', '<=', $toDate->toDateString());
                }

                // If account is selected, calculate opening & closing balances using DayBookService
                if ($this->account) {
                    $service = app(DayBookService::class);
                    $report = $service->getAccountReport($this->account, $fromDate, $toDate);
                    $this->balances = [
                        'opening_balance' => $report['opening_balance'],
                        'closing_balance' => $report['closing_balance'],
                    ];
                }

                return $query;
            })
            ->columns([
                TextColumn::make('voucher_no')
                    ->searchable(),
                TextColumn::make('voucher_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('party.full_name')
                    ->label('Party')
                    ->searchable(),
                TextColumn::make('voucher_type')
                    ->badge(),
                TextColumn::make('reference_no')
                    ->searchable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_mode')
                    ->searchable(),
                TextColumn::make('invoice.invoice_number')
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->searchable(),
                TextColumn::make('fromAccount.title')
                    ->searchable(),
                TextColumn::make('toAccount.title')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('account_id')
                    ->label('Account')
                    ->options(Account::query()->orderBy('title')->pluck('title', 'id'))
                    ->searchable()
                    ->preload(),

                Filter::make('voucher_date')
                    ->form([
                        DatePicker::make('from_date')
                            ->label('From Date')
                            ->default(Carbon::today()->startOfMonth()),
                        DatePicker::make('to_date')
                            ->label('To Date')
                            ->default(Carbon::today()->endOfDay()),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from_date'] ?? null) {
                            $indicators[] = 'From: ' . Carbon::parse($data['from_date'])->toFormattedDateString();
                        }
                        if ($data['to_date'] ?? null) {
                            $indicators[] = 'To: ' . Carbon::parse($data['to_date'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
