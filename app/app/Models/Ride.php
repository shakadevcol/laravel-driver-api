<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'initial_location',
        'final_location',
        'ride_status',
        'driver_id',
        'user_id',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(RideStatus::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
}
