<?php

namespace App\Http\Controllers\API\Transformers;

class PaymentPromiseTransformer extends Transformer {

    public function transform($paymentPromise) {
        return $paymentPromise;
    }
}