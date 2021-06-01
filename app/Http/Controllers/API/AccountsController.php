<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Transformers\AccountTransformer;
use App\Models\Account;
use App\Models\Accounts\Ficohsa\AccountFicohsaTc;
use App\Models\Client;
use App\Rules\verifyClientOwnership;
use App\Services\AccountsService;
use App\Services\AccountTypeDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDO;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            return  $this->respondCreatedWithData('Account created succesfully', $this->transformer->transform(AccountsService::create($request->account_type,$request))) ;          
        }else{
            $this->respondNotFound('Client id not found');
        }
    }

    public function get(Request $request,$id){
        $account = $this->getUserAssociatedAccount($id);
        if($account){
            return $this->respond($this->transformer->transform($account));
        }else{
            return $this->respondNotFound();
        }
    }

    public function update(Request $request,$id){
        $account = $this->getUserAssociatedAccount($id);
        if($account){
            $validator = $request->validate($this->getValidatorByAccountType(AccountTypeDictionary::getAccountTypeByClass($account->accountable_type),false));
            $account = AccountsService::update($account,$request);
            return $this->respond($this->transformer->transform($account));
        }else{
            return $this->respondNotFound();
        }
    }

    public function delete(Request $request,$id){
        $account = $this->getUserAssociatedAccount($id);
        if($account){
            return $this->transformer->transform(AccountsService::delete($account));
        }else{
            return $this->respondNotFound();
        }
    }
    private function getValidatorByAccountType($accountableType,$mergeGeneralValidations=true){
        // TODO check how to use validate function outside of a request model
        $validations = 
        ['account_type'=>['required',Rule::in([AccountTypeDictionary::FICOHSA_TARJETA_CREDITO,AccountTypeDictionary::FICOHSA_PRESTAMO])],
        'client_id'=>['required',new verifyClientOwnership]];
        switch ($accountableType) {
            case AccountTypeDictionary::FICOHSA_TARJETA_CREDITO:
                $newValidations =[ 'ui' => 'required',
                'balance' => 'numeric|required',
                'assign_date'=>'required|date',
                'separation_date'=>'required|date',
                'status'=>'required|in:Extrajudicial,Judicial',
                'administration'=>'required',
                'product'=>'required',
                'segmentation'=>'required',
                'product_type'=>'required',
                'wallet'=>'required',
                // 'subcharacterization_id'=>'required'
                ];
               return $mergeGeneralValidations?array_merge($validations,$newValidations):$newValidations;
                break;

            case AccountTypeDictionary::FICOHSA_PRESTAMO:
                $newValidations = [ 'ui' => 'required',
                'balance' => 'numeric|required',
                'assign_date'=>'required|date',
                'separation_date'=>'required|date',
                'status'=>'required|in:Extrajudicial,Judicial',
                'administration'=>'required',
                'product'=>'required',
                'segmentation'=>'required',
                'product_type'=>'required',
                'wallet'=>'required',
                // 'subcharacterization_id'=>'required'
                ];
                return $mergeGeneralValidations?array_merge($validations,$newValidations):$newValidations;
                    break;    
            
            default:
                abort(404,"Account {$accountableType} type is not valid");
                break;
        }
        

    }

    private function getUserAssociatedAccount($id){
        return Account::whereHas('client',function ($q){
            $q->where('user_id',$this->getCurrentUser()->id);
        })->find($id);
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
