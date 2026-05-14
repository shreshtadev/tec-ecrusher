<?php

namespace App\Filament\Resources\LedgerEntries;

use App\Domains\Accounting\Models\LedgerEntry;
use App\Domains\Common\Enums\NavigGroup;
use App\Filament\Resources\LedgerEntries\Pages\CreateLedgerEntry;
use App\Filament\Resources\LedgerEntries\Pages\EditLedgerEntry;
use App\Filament\Resources\LedgerEntries\Pages\ListLedgerEntries;
use App\Filament\Resources\LedgerEntries\Schemas\LedgerEntryForm;
use App\Filament\Resources\LedgerEntries\Tables\LedgerEntriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class LedgerEntryResource extends Resource
{
    protected static ?string $model = LedgerEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Accounting;

    public static function form(Schema $schema): Schema
    {
        return LedgerEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LedgerEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLedgerEntries::route('/'),
            // 'create' => CreateLedgerEntry::route('/create'),
            // 'edit' => EditLedgerEntry::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
