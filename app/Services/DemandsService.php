<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\Demand;
use Illuminate\Support\Facades\DB;

class DemandsService
{
    public static function create($account_id,$data){
        $demand = new Demand();
        $demand->presentation_date = $data->presentation_date;
        $demand->record_number = $data->record_number;
        $demand->city = $data->city;
        $demand->amount = $data->amount;
        $demand->court_id = $data->court_id;
        $demand->judge_id = $data->judge_id;
        
        $account = Account::find($account_id);
        return DB::transaction(function  () use ($account,$demand) {
            $demand->save();
            $account->demand_id = $demand->id;
            $account->save();
            return $demand;
        });
    }
}