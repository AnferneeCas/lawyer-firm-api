<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\Transformers\DemandTransformer;
use App\Models\Demand;
use App\Rules\verifyAccountOwnership;
use App\Services\DemandsService;
use Illuminate\Http\Request;

class DemandsController extends ApiController
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

    public function __construct(DemandTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function create(Request $request){
        $validated = $request->validate([
            'account_id' => ['required',new verifyAccountOwnership],
            'presentation_date' => 'required',
            'record_number'=>'required',
            'city'=>'required',
            'amount'=>'required|numeric',
            'court_id'=>'required',
            'judge_id'=>'required',
            'started_at'=>'required',
            'type'=>'required'
        ]);
        
        $demand  = DemandsService::create($request->account_id,$request);
      
        return $this->respondCreatedWithData('Demand created',$this->transformer->transform($demand));
    }
    
    public function get(Request $request,$id){
        $demand = $this->getUserAssociatedDemand($id);
        if($demand){
            return $this->respond($this->transformer->transform($demand));
        }else{
            return $this->respondNotFound();
        }
    }

    public function update(Request $request,$id){
        $demand = $this->getUserAssociatedDemand($id);
        $validate = $request->validate([
            'presentation_date' => 'required',
            'record_number'=>'required',
            'city'=>'required',
            'amount'=>'required|numeric',
            'court_id'=>'required',
            'judge_id'=>'required',
            'started_at'=>'required',
            'type'=>'required'
        ]);

        if($demand){
            $demand->presentation_date = $request->presentation_date;
            $demand->record_number = $request->record_number;
            $demand->city = $request->city;
            $demand->amount = $request->amount;
            $demand->court_id = $request->court_id;
            $demand->judge_id = $request->judge_id;
            $demand->started_at = $request->started_at;
            $demand->type = $request->type;
            $demand->save();
            return $this->respond($this->transformer->transform($demand));
        }else{
            return $this->respondNotFound();
        }
    }

    public function delete(Request $request,$id){
        $demand = $this->getUserAssociatedDemand($id);
        if($demand){
            $accounts = $demand->accounts;
            foreach ($accounts as $account) {
                $account->demand_id = null;
                $account->save();
            }
            $demand->delete();
            return $this->respond($this->transformer->transform($demand));
        }else{  
            return $this->respondNotFound();
        }
    }

    private function getUserAssociatedDemand($id){
        $interaction = Demand::whereHas('accounts',function ($q) use($id){
            $q->whereHas('client',function ($q){
                $q->where('user_id',$this->getCurrentUser()->id);
            });
        })->find($id);  
        return $interaction;
    }
}
