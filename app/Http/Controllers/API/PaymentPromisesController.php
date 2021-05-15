<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\Transformers\PaymentPromiseTransformer;
use App\Services\PaymentPromiseService;
use Illuminate\Http\Request;

class PaymentPromisesController extends ApiController
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

    public function __construct(PaymentPromiseTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function create(Request $request){
        // TODO create paymentpromises controller and services
        $validated = $request->validate([
            'account_id' => 'required',
            'amount' => 'required|numeric',
            'starting_date'=>'required',
            'frequency'=>'required|in:monthly,biweekly,quarter',
           
        ]);

        $paymentPromise = PaymentPromiseService::createPaymentPromise($request->account_id,$request);
        return $this->respondCreatedWithData('New payment promise created',$this->transformer->transform($paymentPromise));
        
    }
}
