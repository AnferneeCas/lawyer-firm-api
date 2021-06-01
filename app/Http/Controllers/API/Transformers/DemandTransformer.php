<?php

namespace App\Http\Controllers\API\Transformers;

class DemandTransformer extends Transformer {

    public function transform($demand) {
        error_log(json_encode($demand));
        $response = array();
        $response =array_merge($response,$demand->toArray());
        $accounts = $demand->accounts;
        $transformer =new AccountTransformer();
        $tmpAccount = array();
        foreach ($accounts as $account) {
            $tmp = $transformer->transform($account);
            array_push($tmpAccount,$tmp);
        }
        $response['accounts']= $tmpAccount;
        return $response;
    }
}