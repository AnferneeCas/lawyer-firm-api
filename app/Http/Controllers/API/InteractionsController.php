<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\Transformers\DemandTransformer;
use App\Http\Controllers\API\Transformers\InteractionTransformer;
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
            'account_id' => 'required',
            'message' => 'required|max:500',
            'interaction_type_id'=>'required',
            'characterization_id'=>'required',
            'interaction_status_type'=>'required|in:judicial,extrajudicial',
        ]);

        $interaction = InteractionsService::createInteraction($request->account_id,$request);

        return $this->respondCreatedWithData('New interaction created',$this->transformer->transform($interaction));


        // TODO create interaction service and endpoints
        
    }
}
