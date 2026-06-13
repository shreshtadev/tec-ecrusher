<?php

namespace App\Filament\Resources\Parties;

use App\Enums\NavigGroup;
use App\Filament\Resources\Parties\Pages\CreateParty;
use App\Filament\Resources\Parties\Pages\EditParty;
use App\Filament\Resources\Parties\Pages\ListParties;
use App\Filament\Resources\Parties\RelationManagers\ItemPricesRelationManager;
use App\Filament\Resources\Parties\RelationManagers\VehiclesRelationManager;
use App\Filament\Resources\Parties\Schemas\PartyForm;
use App\Filament\Resources\Parties\Tables\PartiesTable;
use App\Models\Party;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PartyResource extends Resource
{
    protected static ?string $model = Party::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::MasterData;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return PartyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemPricesRelationManager::class,
            VehiclesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParties::route('/'),
            'create' => CreateParty::route('/create'),
            'edit' => EditParty::route('/{record}/edit'),
        ];
    }
}
