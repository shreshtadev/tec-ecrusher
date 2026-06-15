<?php

namespace App\Models;


class StockIssue extends BModel
{
    public function items()
    {
        return $this->belongsTo(StockIssueItem::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
