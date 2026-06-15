<?php

namespace App\Filament\Resources\Parties\RelationManagers;

use App\Filament\Resources\Parties\PartyResource;
use App\Models\Item;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

class ItemPricesRelationManager extends RelationManager
{
    protected static string $relationship = 'itemPrices';

    protected static ?string $relatedResource = PartyResource::class;
    protected static ?string $title = 'Material Prices';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.material_name')
                    ->searchable(),

                TextColumn::make('price_per_unit')
                    ->money('INR'),
            ])
            ->headerActions([
                CreateAction::make()->label('New Price'),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('item_id')
                ->relationship('item', 'material_name')
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                    $foundItem = Item::where('id', $state)->first();
                    $set('price_per_unit', $foundItem->price_per_unit);
                })
                ->preload()
                ->required()->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn($rule) => $rule
                        ->where('party_id', $this->getOwnerRecord()->id)
                ),

            TextInput::make('price_per_unit')
                ->numeric()
                ->live()
                ->required(),
        ]);
    }
}
