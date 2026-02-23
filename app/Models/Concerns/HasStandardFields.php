<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasStandardFields
{
    use HasUuids;
    use SoftDeletes;
}
