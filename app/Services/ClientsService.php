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
        $client->firm_id = $user->firm_id;
        $client->user_id = $user->id;
        $client->save();
        error_log("CLIENTE  {$client->name}  {$client->ui} created");
        return $client;
    }



}