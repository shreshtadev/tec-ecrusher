<?php

namespace App\Filament\Resources\Challans\Schemas;

use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Vehicle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ChallanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Challan Details')
                    ->schema([
                        TextInput::make('challan_number')
                            ->default(fn () => 'CHL-'.strtoupper(uniqid())) // Or your custom logic
                            ->readonly()
                            ->required(),

                        TextInput::make('status')
                            ->readOnly()->default('Pending'),
                    ])->columns(2),
                Section::make('Payment Details')->schema([
                    Select::make('payment_mode')
                        ->options([
                            'Cash' => 'Cash',
                            'A/C' => 'A/C',
                            'Card' => 'Credit/Debit Card',
                            'Bank Transfer' => 'Bank Transfer',
                            'Mobile Payment' => 'Mobile Payment (UPI)',
                            'Cheque' => 'Cheque',
                            'Other' => 'Other',
                        ])->default('A/C')->native(false),
                ]),

                Section::make('Assignments')
                    ->schema([
                        Select::make('company_id')
                            ->label('Companny')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->native(false),
                        // 1. Party Select
                        Select::make('party_id')
                            ->label('Party')
                            ->relationship('party', 'full_name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->disabled(fn (Get $get) => blank($get('company_id')))
                            ->dehydrated()
                            ->afterStateUpdated(fn (Set $set) => $set('vehicle_id', null))
                            ->required()->native(false),

                        // 2. Vehicle Select (Filtered by Party)
                        Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->relationship(
                                name: 'vehicle',
                                titleAttribute: 'vehicle_number',
                                modifyQueryUsing: function (Builder $query, Get $get) {
                                    return $query->when(
                                        $get('party_id'),
                                        fn (Builder $query, $partyId) => $query->where('party_id', $partyId),
                                        fn (Builder $query) => $query->whereRaw('1 = 0')
                                    );
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->disabled(fn (Get $get) => blank($get('party_id')))
                            ->dehydrated()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $vehicle = Vehicle::find($state);

                                if ($vehicle) {
                                    $set('quantity_cft', $vehicle->capacity_cft);
                                }
                            })
                            ->placeholder('Select a party first')
                            ->required()
                            ->native(false),

                        // 3. Driver Select (Filtered by Party)
                        Select::make('driver_id')
                            ->label('Driver')
                            ->relationship(name: 'driver', titleAttribute: 'full_name', modifyQueryUsing: function (Builder $query, Get $get) {
                                return $query->when(
                                    $get('party_id'),
                                    fn (Builder $query, $partyId) => $query->where('party_id', $partyId),
                                    fn (Builder $query) => $query->whereRaw('1 = 0')
                                );
                            })
                            ->native(false)
                            ->placeholder('Select a party first')
                            ->preload()
                            ->live()
                            ->disabled(fn (Get $get) => blank($get('party_id')))
                            ->dehydrated()
                            ->searchable()
                            ->required(),

                        // 4. Item Select
                        Select::make('item_id')
                            ->label('Item')
                            ->relationship(
                                name: 'item',
                                titleAttribute: 'material_name',
                                modifyQueryUsing: fn ($query) => $query->orderBy('material_name')
                            )
                            ->placeholder('Select an item')
                            ->preload()
                            ->searchable()
                            ->live()
                            ->required()
                            ->native(false),

                        TextInput::make('quantity_cft')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->prefix(function (Get $get) {
                                $itemId = $get('item_id');

                                if (! $itemId) {
                                    return null;
                                }

                                return Item::find($itemId)?->unit;
                            })->placeholder('Select an item first')
                            ->readOnly(fn (Get $get) => blank($get('item_id'))),
                    ])->columns(2),

            ])->disabled(fn ($record) => $record?->status === 'Invoiced');
    }
}
