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

    public static function update($account,$data){
        if($account instanceof Account){
            return AccountsService::updateAccountByAccountableType($account,$data);
        }else{
            $tmpAccount = Account::find($account);
            return AccountsService::updateAccountByAccountableType($tmpAccount,$data);
        }
    }

    public static function delete($account){
        if($account instanceof Account){
            return AccountsService::deleteAccount($account);
        }else{
            $tmpAccount = Account::find($account);
            return AccountsService::deleteAccount($account);
        }
    }

    private static function deleteAccount($account){
        $accountable = $account->accountable;

        return DB::transaction(function  () use ($account,$accountable) {
            $accountable->delete();
            $account->delete();  
            $account->documentRequest()->delete();
            $account->interactions()->delete();
            $account->paymentPromise()->delete();
            $account->demand()->delete();        
            return $account;         
        });
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

    private static function updateAccountByAccountableType($account,$data){
        switch ($account->accountable_type) {
            case AccountFicohsaTc::class:
                return AccountsService::updateFicohsaTcAccount($account,$data);
                break;
            case AccountFicohsaPtmo::class:   
                return AccountsService::updateFicohsaPtmoAccount($account,$data);
                break;
                        
            default:
                abort(400,"Account {$account->accountable_type} type is not valid");
                # code...
                break;
        } 
    }

    private static function updateFicohsaTcAccount($account,$data){
        $accountable = $account->accountable;

        $account->subcharacterization_id = $data->subcharacterization_id;
        
        $accountable->ui = $data->ui;
        $accountable->status =$data->status;
        $accountable->balance = $data->balance;
        $accountable->balance_usd = $data->balance/25;
        $accountable->assign_date = $data->assign_date;
        $accountable->separation_date = $data->separation_date;
        $accountable->administration = $data->administration;
        $accountable->product = $data->product;
        $accountable->segmentation = $data->segmentation;
        $accountable->product_type = $data->product_type;
        $accountable->wallet =$data->wallet;

        return DB::transaction(function  () use ($account,$accountable) {
            
            $accountable->save();
            $account->save();          
            return $account;         
        });
        
    }

    private static function updateFicohsaPtmoAccount($account,$data){
        $accountable = $account->accountable;

        $account->subcharacterization_id = $data->subcharacterization_id;
        
        $accountable->ui = $data->ui;
        $accountable->status =$data->status;
        $accountable->balance = $data->balance;
        $accountable->assign_date = $data->assign_date;
        $accountable->separation_date = $data->separation_date;
        $accountable->administration = $data->administration;
        $accountable->product = $data->product;
        $accountable->segmentation = $data->segmentation;
        $accountable->product_type = $data->product_type;
        $accountable->wallet =$data->wallet;

        return DB::transaction(function  () use ($account,$accountable) {
            
            $accountable->save();
            $account->save();          
            return $account;         
        });
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
        $ficohsaTc->product = "TC";
        $ficohsaTc->segmentation = $request->segmentation;
        $ficohsaTc->product_type = $request->product_type;
        $ficohsaTc->wallet =$request->wallet;

        $account = new Account();
        $account->client_id = $request->client_id;
        $account->accountable_type = AccountFicohsaTc::class;
        $account->firm_id = $user->firm_id;
        $account->subcharacterization_id = $request->subcharacterization_id;

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
        $ficohsaPtmo->product = "PTMO";
        $ficohsaPtmo->segmentation = $request->segmentation;
        $ficohsaPtmo->product_type = $request->product_type;
        $ficohsaPtmo->wallet =$request->wallet;

        $account = new Account();
        $account->client_id = $request->client_id;
        $account->accountable_type = AccountFicohsaPtmo::class;
        $account->firm_id = $user->firm_id;
        $account->subcharacterization_id = $request->subcharacterization_id;

        return DB::transaction(function  () use ($account,$ficohsaPtmo) {
            
            $ficohsaPtmo->save();
            $account->accountable_id = $ficohsaPtmo->id;
            $account->save();         
            return $account;         
        });

    }

}