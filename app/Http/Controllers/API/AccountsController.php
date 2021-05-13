<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Transformers\AccountTransformer;
use App\Models\Account;
use App\Models\Accounts\Ficohsa\AccountFicohsaTc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDO;
use Illuminate\Support\Facades\DB;

class AccountsController extends ApiController
{
    private $transformer;
      /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function __construct(AccountTransformer $accountTransformer)
    {
        $this->transformer = $accountTransformer;
    }
    public function create(Request $request){
        $validated = $request->validate($this->getValidatorByAccountType($request->account_type));

        $user =$this->getCurrentUser();
        $client = $user->clients()->find($request->client_id);
        if($client){
            return $this->createAccountByAccountableType($request->account_type,$request);            
        }else{
            $this->respondNotFound('Client id not found');
        }
    }

    private function getValidatorByAccountType($accountableType){
        // TODO check how to use validate function outside of a request model
        $validations = 
        ['account_type'=>'required|in:FICOHSA_TC',
        'client_id'=>'required'];
        switch ($accountableType) {
            case Account::FICOHSA_TC:
               return  array_merge($validations,[ 'ui' => 'required',
               'balance' => 'numeric|required',
               'assign_date'=>'required|date',
               'separation_date'=>'required|date',
               'status'=>'required|in:extrajudicial,judicial']) ;
                break;
            
            default:
                return $validations;
                break;
        }
        

    }

    private function createAccountByAccountableType($accountableType,$request){
        switch ($accountableType) {
            case Account::FICOHSA_TC:
                return $this->createFicohsaTcAccount($request);
                # code...
                break;
            
            default:
                # code...
                break;
        }
    }

    private function createFicohsaTcAccount($request){
        $user = Auth::user();
        $ficohsaTc = new AccountFicohsaTc();

        $ficohsaTc->status = $request->status;
        $ficohsaTc->ui = $request->ui;
        $ficohsaTc->balance = $request->balance;
        $ficohsaTc->balance_usd = $request->balance / 25;
        $ficohsaTc->assign_date = $request->assign_date;
        $ficohsaTc->separation_date = $request->separation_date;

        $account = new Account();
        $account->client_id = $request->client_id;
        $account->accountable_type = AccountFicohsaTc::class;
        $account->firm_id = $user->firm_id;

        return DB::transaction(function  () use ($account,$ficohsaTc) {
            
            $ficohsaTc->save();
            $account->accountable_id = $ficohsaTc->id;
            $account->save();          
            return $this->transformer->transform($account);         
        });

    }
    private function getAccountableClass($accountableType){
        switch ($accountableType) {
            case 'value':
                # code...
                break;
            
            default:
                # code...
                break;
        }
    }
}
