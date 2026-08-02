<?php

namespace App\Filament\Resources\Challans;

use App\Enums\NavigGroup;
use App\Filament\Resources\Challans\Pages\CreateChallan;
use App\Filament\Resources\Challans\Pages\EditChallan;
use App\Filament\Resources\Challans\Pages\ListChallans;
use App\Filament\Resources\Challans\Schemas\ChallanForm;
use App\Filament\Resources\Challans\Tables\ChallansTable;
use App\Models\Challan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;
use UnitEnum;

class ChallanResource extends Resource
{
    protected static ?string $model = Challan::class;

    protected static ?string $navigationLabel = 'Trip Sheets';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Operation;

    protected static ?string $recordTitleAttribute = 'challan_number';

    public static function form(Schema $schema): Schema
    {
        return ChallanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChallansTable::configure($table);
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
            'index' => ListChallans::route('/'),
            'create' => CreateChallan::route('/create'),
            'edit' => EditChallan::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
