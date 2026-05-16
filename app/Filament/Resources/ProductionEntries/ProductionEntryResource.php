<?php

namespace App\Filament\Resources\ProductionEntries;

use App\Domains\Common\Enums\NavigGroup;
use App\Domains\Operations\Models\ProductionEntry;
use App\Filament\Resources\ProductionEntries\Pages\CreateProductionEntry;
use App\Filament\Resources\ProductionEntries\Pages\EditProductionEntry;
use App\Filament\Resources\ProductionEntries\Pages\ListProductionEntries;
use App\Filament\Resources\ProductionEntries\Schemas\ProductionEntryForm;
use App\Filament\Resources\ProductionEntries\Tables\ProductionEntriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ProductionEntryResource extends Resource
{
    protected static ?string $model = ProductionEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Operation;

    public static function form(Schema $schema): Schema
    {
        return ProductionEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductionEntriesTable::configure($table);
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
            'index' => ListProductionEntries::route('/'),
            'create' => CreateProductionEntry::route('/create'),
            'edit' => EditProductionEntry::route('/{record}/edit'),
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
