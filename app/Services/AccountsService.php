<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Accounts\Ficohsa\AccountFicohsaPtmo;
use App\Models\Accounts\Ficohsa\AccountFicohsaTc;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Demand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AccountsService
{
   public static function create($accountableType,$data){
    return  AccountsService::createAccountByAccountableType($accountableType,$data);   
   }
    private static function createAccountByAccountableType($accountableType,$request){
        switch ($accountableType) {
            case AccountTypeDictionary::FICOHSA_TARJETA_CREDITO:
                return AccountsService::createFicohsaTcAccount($request);
                # code...
                break;
            case AccountTypeDictionary::FICOHSA_PRESTAMO:   
                return AccountsService::createFicohsaPtmoAccount($request);
                break;
                     
            default:
                abort(400,"Account {$accountableType} type is not valid");
                # code...
                break;
        }
    }

    private static function createFicohsaTcAccount($request){
        $user = Auth::user();
        $ficohsaTc = new AccountFicohsaTc();

        $ficohsaTc->status = $request->status;
        $ficohsaTc->ui = $request->ui;
        $ficohsaTc->balance = $request->balance;
        $ficohsaTc->balance_usd = $request->balance / 25;
        $ficohsaTc->assign_date = $request->assign_date;
        $ficohsaTc->separation_date = $request->separation_date;
        $ficohsaTc->administration = $request->administration;
        $ficohsaTc->product = $request->product;
        $ficohsaTc->segmentation = $request->segmentation;
        $ficohsaTc->product_type = $request->product_type;
        $ficohsaTc->wallet =$request->wallet;

        $account = new Account();
        $account->client_id = $request->client_id;
        $account->accountable_type = AccountFicohsaTc::class;
        $account->firm_id = $user->firm_id;

        return DB::transaction(function  () use ($account,$ficohsaTc) {
            
            $ficohsaTc->save();
            $account->accountable_id = $ficohsaTc->id;
            $account->save();          
            return $account;         
        });

    }

    private static function createFicohsaPtmoAccount($request){
        $user = Auth::user();
        $ficohsaPtmo = new AccountFicohsaPtmo();

        $ficohsaPtmo->status = $request->status;
        $ficohsaPtmo->ui = $request->ui;
        $ficohsaPtmo->balance = $request->balance;
        $ficohsaPtmo->assign_date = $request->assign_date;
        $ficohsaPtmo->separation_date = $request->separation_date;
        $ficohsaPtmo->administration = $request->administration;
        $ficohsaPtmo->product = $request->product;
        $ficohsaPtmo->segmentation = $request->segmentation;
        $ficohsaPtmo->product_type = $request->product_type;
        $ficohsaPtmo->wallet =$request->wallet;

        $account = new Account();
        $account->client_id = $request->client_id;
        $account->accountable_type = AccountFicohsaPtmo::class;
        $account->firm_id = $user->firm_id;

        return DB::transaction(function  () use ($account,$ficohsaPtmo) {
            
            $ficohsaPtmo->save();
            $account->accountable_id = $ficohsaPtmo->id;
            $account->save();         
            return $account;         
        });

    }

}