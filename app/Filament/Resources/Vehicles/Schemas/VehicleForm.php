<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enums\UnitOpts;
use App\Enums\VehicleOpts;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('party_id')
                    ->relationship('party', 'full_name'),
                TextInput::make('vehicle_number')
                    ->required()->formatStateUsing(fn($state) => str($state)->replace(' ', '')),
                TextInput::make('capacity_cft')
                    ->label('Capacity')
                    ->numeric(),
                Select::make('unit')
                    ->required()
                    ->default('CFT')
                    ->options(
                        collect(UnitOpts::unitOptions())
                            ->mapWithKeys(fn($unit, $key) => [
                                $key => "
                <div style='display:flex; flex-direction:column;'>
                    <span style='font-weight:600;'>
                        {$unit['label']} ({$key})
                    </span>

                    <span style='font-size:11px; color:#6b7280;'>
                        Typical: {$unit['usage']}
                    </span>
                </div>
            ",
                            ])
                            ->toArray()
                    )->allowHtml()->native(false),
                Select::make('vehicle_type')
                    ->native(false)
                    ->allowHtml()
                    ->options(
                        collect(VehicleOpts::vehicleTypeOptions())
                            ->mapWithKeys(fn($type, $key) => [
                                $key => "
                    <div style='display:flex; flex-direction:column;'>
                        <span style='font-weight:600;'>
                            {$type['label']}
                        </span>

                        <span style='font-size:11px; color:#6b7280;'>
                            {$type['usage']}
                        </span>
                    </div>
                ",
                            ])
                            ->toArray()
                    ),
            ]);
    }
}
