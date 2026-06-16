<?php

namespace App\Filament\Resources\StockIssues;

use App\Enums\NavigGroup;
use App\Filament\Resources\StockIssues\Pages\CreateStockIssue;
use App\Filament\Resources\StockIssues\Pages\EditStockIssue;
use App\Filament\Resources\StockIssues\Pages\ListStockIssues;
use App\Filament\Resources\StockIssues\RelationManagers\StockIssueItemsRelationManager;
use App\Filament\Resources\StockIssues\Schemas\StockIssueForm;
use App\Filament\Resources\StockIssues\Tables\StockIssuesTable;
use App\Models\StockIssue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockIssueResource extends Resource
{
    protected static ?string $model = StockIssue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = NavigGroup::Inventory;

    protected static ?string $recordTitleAttribute = 'issue_no';

    public static function form(Schema $schema): Schema
    {
        return StockIssueForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockIssuesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StockIssueItemsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockIssues::route('/'),
            'create' => CreateStockIssue::route('/create'),
            'edit' => EditStockIssue::route('/{record}/edit'),
        ];
    }
}
