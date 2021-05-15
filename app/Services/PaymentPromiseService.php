<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\Demand;
use App\Models\Interaction;
use App\Models\PaymentPromise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentPromiseService
{
    public static function createPaymentPromise($account_id,$data){

        $paymentPromise = new PaymentPromise();
        $paymentPromise->account_id =$account_id;
        $paymentPromise->amount = $data->amount;
        $paymentPromise->frequency= $data->frequency;
        $paymentPromise->starting_date = $data->starting_date;
        $paymentPromise->save();
        
        return $paymentPromise;
    }
}