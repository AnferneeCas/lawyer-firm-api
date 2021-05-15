<?php

namespace App\Http\Controllers\API\Transformers;

class InteractionTransformer extends Transformer {

    public function transform($user) {
        return $user;
    }
}