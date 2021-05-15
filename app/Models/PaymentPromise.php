<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPromise extends Model
{
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTLY = 'quarter';
    const FREQUENCY_BIWEEKLY = 'biweekly';
}
