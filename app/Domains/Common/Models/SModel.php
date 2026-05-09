<?php

namespace App\Domains\Common\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SModel extends BModel
{
    use SoftDeletes;
}
