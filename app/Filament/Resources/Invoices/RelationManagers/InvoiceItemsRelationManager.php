<?php

namespace App\Filament\Resources\Invoices\RelationManagers;

use App\Filament\Resources\InvoiceItems\InvoiceItemResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'invoiceItems';

    protected static ?string $relatedResource = InvoiceItemResource::class;
    public function form(Schema $schema): Schema
    {
        return InvoiceItemResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')->label('Sl No.'),
                TextColumn::make('quantity')->label('Quantity'),
                TextColumn::make('rate_at_sale')->label('Rate at Sale'),
                TextColumn::make('amount')->label('Amount'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
