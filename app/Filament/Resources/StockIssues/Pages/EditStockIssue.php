<?php

namespace App\Filament\Resources\StockIssues\Pages;

use App\Filament\Resources\StockIssues\StockIssueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockIssue extends EditRecord
{
    protected static string $resource = StockIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
