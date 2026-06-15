<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIssueItem extends Model
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
