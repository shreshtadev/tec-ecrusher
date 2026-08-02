<?php

namespace App\Filament\Resources\Parties\RelationManagers;

use App\Enums\UnitOpts;
use App\Enums\VehicleOpts;
use App\Filament\Resources\Vehicles\Schemas\VehicleRelationForm;
use App\Filament\Resources\Vehicles\VehicleResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicles';

    public function form(Schema $schema): Schema
    {
        return VehicleRelationForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('vehicle_number')
            ->columns([
                TextColumn::make('vehicle_number')->searchable(),
                TextColumn::make('capacity_cft')->label('Capacity (CFT)'),
                TextColumn::make('vehicle_type'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
            ]);
    }
}
