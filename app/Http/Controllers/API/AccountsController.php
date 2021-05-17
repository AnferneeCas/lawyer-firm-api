<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Transformers\AccountTransformer;
use App\Models\Account;
use App\Models\Accounts\Ficohsa\AccountFicohsaTc;
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

    private function getValidatorByAccountType($accountableType){
        // TODO check how to use validate function outside of a request model
        $validations = 
        ['account_type'=>['required',Rule::in([AccountTypeDictionary::FICOHSA_TARJETA_CREDITO,AccountTypeDictionary::FICOHSA_PRESTAMO])],
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
