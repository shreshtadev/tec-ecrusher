<?php

namespace App\Models;


class StockIssue extends BModel
{
    public function stockIssueItems()
    {
        return $this->hasMany(StockIssueItem::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
