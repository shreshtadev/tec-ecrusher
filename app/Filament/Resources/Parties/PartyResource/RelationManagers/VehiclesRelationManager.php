<?php

namespace App\Filament\Resources\Parties\PartyResource\RelationManagers;

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
use Livewire\Form;

class VehiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicles';

    protected static ?string $relatedResource = VehicleResource::class;

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('vehicle_number')
                    ->required()
                    ->unique(ignorable: fn($record) => $record)
                    ->placeholder('KA-XX-XXXX')
                    ->maxLength(255),
                TextInput::make('capacity_cft')
                    ->label('Capacity (CFT)')
                    ->numeric()
                    ->required()
                    ->suffix('CFT'),
                Select::make('type')
                    ->options([
                        'Tipper' => 'Tipper',
                        'Truck' => 'Truck',
                        'Tractor' => 'Tractor',
                    ])
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('vehicle_number')
            ->columns([
                TextColumn::make('vehicle_number')->searchable(),
                TextColumn::make('capacity_cft')->label('Capacity (CFT)'),
                TextColumn::make('type'),
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
