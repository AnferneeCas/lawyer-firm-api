<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentPromise extends Model
{
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTLY = 'quarter';
    const FREQUENCY_BIWEEKLY = 'biweekly';
    use SoftDeletes;

    public function account (){
        return $this->belongsTo('App\Models\Account');
    }
}
