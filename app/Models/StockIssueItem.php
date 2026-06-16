<?php

namespace App\Models;

class StockIssueItem extends BModel
{
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function stockIssue()
    {
        return $this->belongsTo(StockIssue::class);
    }
}
