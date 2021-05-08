<?php

namespace App\Http\Controllers\API\Transformers;

class ClientTransformer extends Transformer {

    public function transform($client) {
        return $client;
    }
}