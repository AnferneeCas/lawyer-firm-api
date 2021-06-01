<?php

namespace App\Http\Controllers\API\Transformers;

class ClientTransformer extends Transformer {

    public function transform($client) {
        // if(is_array($client)){
        //     return array([
        //         "clients" => $client->load('accounts'),
                
        //     ]);
        // }
        return $client->load(['accounts',]);
    }
}