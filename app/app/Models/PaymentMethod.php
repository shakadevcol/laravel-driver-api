<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    const CARD = 1;
    const NEQUI = 2;

    public function paymentInformations(): HasMany
    {
        return $this->hasMany(PaymentInformation::class);
    }
}
