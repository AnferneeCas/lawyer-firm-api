<?php

namespace App\Http\Controllers\API\Transformers;

class UserTransformer extends Transformer {

    public function transform($user) {
        return $user;
    }
}