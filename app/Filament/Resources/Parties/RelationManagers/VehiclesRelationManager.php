<?php

namespace App\Filament\Resources\Parties\RelationManagers;

use App\Filament\Resources\Vehicles\VehicleResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicles';

    protected static ?string $relatedResource = VehicleResource::class;

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
                EditAction::make(),
                DeleteAction::make(), // This triggers SoftDeletes as configured
            ]);
    }
}
