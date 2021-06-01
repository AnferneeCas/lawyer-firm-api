<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Demand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClientsService
{
   
    public static function create($data){
        $user = Auth::user();
        $client = new Client();
        $client->name = $data->name;
        $client->email = $data->email;
        $client->social_id = $data->social_id;
        $client->ui=$data->ui;
        $client->work_address =$data->work_address;
        $client->home_address = $data->home_address;
        $client->contact_number = $data->contact_number;
        $client->company_type = $data->company_type;
        $client->firm_id = $user->firm_id;
        $client->user_id = $user->id;
        $client->save();
        return $client;
    }


    public static function update($client,$data){
        if(!($client instanceof Client)){
            $client = Client::find($client);
        }   
        $client->name = $data->name;
        $client->email = $data->email;
        $client->social_id = $data->social_id;
        $client->ui=$data->ui;
        $client->work_address =$data->work_address;
        $client->home_address = $data->home_address;
        $client->contact_number = $data->contact_number;
        $client->company_type = $data->company_type;
        $client->save();
        return $client;
    }

    public static function delete($client){
        if(!($client instanceof Client)){
            $client = Client::find($client);
        }  
       return DB::transaction(function  () use ($client) {
            $accounts = $client->accounts;
            foreach ($accounts as $account) {
                AccountsService::delete($account);
            }
            $client->delete();
            return $client;
        }); 
        
    }

}