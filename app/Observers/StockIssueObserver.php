<?php

namespace App\Observers;

use App\Models\StockIssue;
use App\Services\Inventory\StockIssueService;

class StockIssueObserver
{

    public function __construct(private StockIssueService $stockIssueService)
    {
        //
    }

    /**
     * Handle the StockIssue "created" event.
     */
    public function created(StockIssue $stockIssue): void
    {
        $this->stockIssueService->issue($stockIssue);
    }

    /**
     * Handle the StockIssue "updated" event.
     */
    public function updated(StockIssue $stockIssue): void
    {
        $this->stockIssueService->revertIssue($stockIssue);
    }
}
