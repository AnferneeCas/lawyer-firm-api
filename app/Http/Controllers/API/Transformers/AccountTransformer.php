<?php

namespace App\Http\Controllers\API\Transformers;

class AccountTransformer extends Transformer {

    public function transform($account) {
        $result = array();
        $result = array_merge($result,$account->toArray());
        $result['type']=$account->accountable_type::ACCOUNT_TYPE;
        $result['data'] = $account->accountable;
        return $result;
    }
}