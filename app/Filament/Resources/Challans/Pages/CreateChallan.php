<?php

namespace App\Filament\Resources\Challans\Pages;

use App\Filament\Resources\Challans\ChallanResource;
use App\Services\StockService;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateChallan extends CreateRecord
{
    protected static string $resource = ChallanResource::class;


    protected function afterCreate(): void
    {
        $challan = $this->record;
        $stockService = app(StockService::class);
        try {
            DB::transaction(function () use ($challan, $stockService) {
                $stockService->reserve($challan);
                $newInvoice = $stockService->createInvoice($challan);
                $stockService->finalize($newInvoice);
            });
        } catch (Exception $e) {
            // Log error or handle appropriately
            report($e);
            throw $e;
        }
    }
}
