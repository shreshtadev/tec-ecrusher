<?php

namespace App\Filament\Resources\StockIssues\RelationManagers;

use App\Filament\Resources\StockIssues\Schemas\StockIssueItemRelationForm;
use App\Filament\Resources\StockIssues\StockIssueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

class StockIssueItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockIssueItems';

    protected static ?string $relatedResource = StockIssueResource::class;

    public function form(Schema $schema): Schema
    {
        return StockIssueItemRelationForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("item.material_name"),
                TextColumn::make("quantity")->formatStateUsing(fn($state) => number_format($state, 0)),
            ])
            ->headerActions([
                CreateAction::make()->label("Add Issue Item?"),
            ]);
    }
}
