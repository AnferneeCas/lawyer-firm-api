<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\Transformers\DemandTransformer;
use App\Http\Controllers\API\Transformers\InteractionTransformer;
use App\Models\Interaction;
use App\Rules\verifyAccountOwnership;
use App\Services\DemandsService;
use App\Services\InteractionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionsController extends ApiController
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

    public function __construct(InteractionTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function create(Request $request){
        $validated = $request->validate([
            'account_id' => ['required',new verifyAccountOwnership],
            'message' => 'required|max:500',
            'interaction_type_id'=>'required',
            'characterization_id'=>'required',
            'interaction_status_type'=>'required|in:judicial,extrajudicial',
        ]);

        $interaction = InteractionsService::createInteraction($request->account_id,$request);

        return $this->respondCreatedWithData('New interaction created',$this->transformer->transform($interaction));
        
    }
    public function get(Request $request,$id){
        $interaction = $this->getUserAssociatedInteraction($id);
    
        if($interaction){
           return $this->respond($this->transformer->transform($interaction));
        }else{
           return $this->respondNotFound();
        }
    }

    public function update(Request $request,$id){
        $validated = $request->validate([
            'message' => 'required|max:500',
            'interaction_type_id'=>'required',
            'characterization_id'=>'required',
            'interaction_status_type'=>'required|in:Judicial,Extrajudicial',
        ]);
        $interaction = $this->getUserAssociatedInteraction($id);
    
        if($interaction){
            $interaction->message = $request->message;
            $interaction->interaction_type_id = $request->interaction_type_id;
            $interaction->characterization_id = $request->characterization_id;
            $interaction->interaction_status_type = $request->interaction_status_type;
            $interaction->save(); 
           return $this->respond($this->transformer->transform($interaction));
        }else{
           return $this->respondNotFound();
        }
    }

    public function delete(Request $request,$id){
        $interaction = $this->getUserAssociatedInteraction($id);
        if($interaction){
            $interaction->delete();
            return $this->respond($this->transformer->transform($interaction));
        }else{
            return $this->respondNotFound();
         }
    }

    public function getAllInteractionByClient(Request $request,$id){
        $interactions =  $this->getCurrentUser()->interactions()->where('client_id',$id)->get();
        $interactions->load('characterization');
       return $this->transformer->transform($interactions);
    }

    private function getUserAssociatedInteraction($id){
        $interaction = $this->getCurrentUser()->interactions()->find($id);
        return $interaction;
    }
}
