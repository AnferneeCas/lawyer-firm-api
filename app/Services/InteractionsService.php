<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\Demand;
use App\Models\Interaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InteractionsService
{
    public static function createInteraction($account_id,$data){
        $account = Account::find($account_id);
        $user = Auth::user();
        $interaction = new Interaction();
        $interaction->user_id = $user->id;
        $interaction->account_id = $account_id;
        $interaction->client_id = $account->client->id;
        $interaction->message = $data->message;
        $interaction->interaction_type_id = $data->interaction_type_id;
        $interaction->characterization_id = $data->characterization_id;
        $interaction->interaction_status_type = $data->interaction_status_type;
        $interaction->save();
        
        return $interaction;
    }
}