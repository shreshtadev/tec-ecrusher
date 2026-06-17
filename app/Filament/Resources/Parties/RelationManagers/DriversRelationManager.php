<?php

namespace App\Filament\Resources\Parties\RelationManagers;

use App\Filament\Resources\Drivers\Schemas\DriverRelationForm;
use App\Filament\Resources\Parties\PartyResource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

class DriversRelationManager extends RelationManager
{
    protected static string $relationship = 'drivers';

    protected static ?string $relatedResource = PartyResource::class;
    protected static ?string $title = 'Drivers';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name'),
                TextColumn::make('phone_number')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return DriverRelationForm::configure($schema);
    }
}
