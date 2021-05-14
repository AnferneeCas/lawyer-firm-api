<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\Transformers\DemandTransformer;
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
            'account_id' => 'required',
            'presentation_date' => 'required',
            'record_number'=>'required',
            'city'=>'required',
            'amount'=>'required|numeric',
            'court_id'=>'required',
            'judge_id'=>'required'
        ]);
        
        $demand  = DemandsService::create($request->account_id,$request);
      
        return $this->respondCreatedWithData('Demand created',$this->transformer->transform($demand));
    }
}
