<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RideStatus extends Model
{
    use HasFactory;

    const ASSIGNED = 1;
    const FINISHED = 2;

    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class);
    }
}
