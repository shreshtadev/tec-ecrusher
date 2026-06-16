<?php

namespace App\Filament\Resources\StockIssues\Schemas;

use App\Models\Company;
use App\Models\Warehouse;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class StockIssueForm
{
    public static function configure(Schema $schema): Schema
    {
        $issueNo = now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        return $schema
            ->components([
                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->required()
                    ->options(fn() => Warehouse::pluck('name', 'id'))->native(false)->default(fn(Select $component): string => array_key_first($component->getOptions())),
                TextInput::make("issue_no")->default($issueNo),
                DatePicker::make('issue_date')
                    ->required()->default(now()),
                TextInput::make('purpose')
                    ->required()->default("Internal Issue"),
                Hidden::make("status")->default("draft"),
                Select::make('company_id')
                    ->options(fn() => Company::pluck('name', 'id'))
                    ->required()->default(fn(Select $component): string => array_key_first($component->getOptions())),
                Textarea::make('remarks')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
