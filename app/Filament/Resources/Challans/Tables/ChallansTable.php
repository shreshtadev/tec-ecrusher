<?php

namespace App\Filament\Resources\Challans\Tables;

use App\Domains\Operations\Events\ChallanFinalized;
use App\Domains\Operations\Events\ChallansFinalized;
use App\Domains\Operations\Models\Challan;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class ChallansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('challan_number')
                    ->searchable(),
                TextColumn::make('party.full_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('company.name'),
                TextColumn::make('vehicle.vehicle_number')
                    ->numeric()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('item.material_name')
                    ->numeric()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('item.unit')->label('Unit')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quantity_cft')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->searchable()->badge(),
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
            ])
            ->filters([
                SelectFilter::make('status'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('finalize')
                    ->label('Finalize')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Challan $record) => $record->status === 'Pending')
                    ->action(function (Challan $record) {
                        // Dispatch the event
                        ChallanFinalized::dispatch($record);

                        Notification::make()
                            ->title('Challan Finalized')
                            ->success()
                            ->send();
                    }),
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(Challan $record) => route('print.challan', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),

                BulkAction::make('bulkFinalize')
                    ->label('Finalize Selected')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn() => true)
                    ->action(function (Collection $records) {
                        try {
                            ChallansFinalized::dispatch($records);
                            Notification::make()
                                ->title('Challans Finalized')
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            logger()->error('Error finalizing challans: ' . $e->getMessage(), [
                                'exception' => $e,
                                'record_ids' => $records->pluck('id'),
                            ]);
                            Notification::make()
                                ->title('Error finalizing challans.')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-document-duplicate'),
            ]);
    }
}
