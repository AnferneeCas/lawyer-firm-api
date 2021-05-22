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
        $interaction->message =  InteractionsService::removeInvalidChars($data->message);
        $interaction->interaction_type_id = $data->interaction_type_id;
        $interaction->characterization_id = $data->characterization_id;
        $interaction->interaction_status_type = $data->interaction_status_type;
        $interaction->save();
        
        return $interaction;
    }

    public static function breakDownInteraction($interaction,$account_id){
        $length = strlen($interaction);
        $maxLength = 499;
        $currentLength = 0;
        $user = Auth::user();
        if($length > $maxLength){
            while ($currentLength < $length) {
                $result = substr($interaction, $currentLength, $maxLength);  
                $data = (object) [
                    "message"=>$result,
                    "interaction_type_id"=>1,
                    "characterization_id"=>1,
                    "interaction_status_type"=>"Extrajudicial"
                ];
                InteractionsService::createInteraction($account_id,$data);
                $currentLength = $currentLength+$maxLength;
                if(($length - $currentLength) < $maxLength){
                    $maxLength = ($length - $currentLength);
                }             
            }
            
        }else{
            $data = (object) [
                "message"=>$interaction,
                "interaction_type_id"=>1,
                "characterization_id"=>1,
                "interaction_status_type"=>"Extrajudicial"
            ];
            return InteractionsService::createInteraction($account_id,$data);
        }
       
        

    }

    private static function removeInvalidChars( $text) {
        $regex = '/( [\x00-\x7F] | [\xC0-\xDF][\x80-\xBF] | [\xE0-\xEF][\x80-\xBF]{2} | [\xF0-\xF7][\x80-\xBF]{3} ) | ./x';
        return preg_replace($regex, '$1', $text);
    }
}