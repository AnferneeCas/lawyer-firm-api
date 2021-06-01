<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\Transformers\PaymentPromiseTransformer;
use App\Models\PaymentPromise;
use App\Services\PaymentPromiseService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate([
            'account_id' => 'required',
            'amount' => 'required|numeric',
            'starting_date'=>'required',
            'frequency'=>['required',Rule::in([PaymentPromise::FREQUENCY_BIWEEKLY,PaymentPromise::FREQUENCY_MONTHLY,PaymentPromise::FREQUENCY_QUARTLY])],
        ]);

        $paymentPromise = PaymentPromiseService::createPaymentPromise($request->account_id,$request);
        return $this->respondCreatedWithData('New payment promise created',$this->transformer->transform($paymentPromise));
        
    }

    public function get(Request $request,$id){
        $paymentPromise = $this->getUserAssociatedPaymentPromise($id);
        if($paymentPromise){
            return $this->transformer->transform($paymentPromise);
        }else{
            return $this->respondNotFound();
        }
    }

    public function update(Request $request,$id){
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'starting_date'=>'required',
            'frequency'=>['required',Rule::in([PaymentPromise::FREQUENCY_BIWEEKLY,PaymentPromise::FREQUENCY_MONTHLY,PaymentPromise::FREQUENCY_QUARTLY])],
        ]);

        $paymentPromise = $this->getUserAssociatedPaymentPromise($id);
        if($paymentPromise){
            $paymentPromise->amount = $request->amount;
            $paymentPromise->starting_date = $request->starting_date;
            $paymentPromise->frequency = $request->frequency;
            $paymentPromise->save();
            return $this->respond($this->transformer->transform($paymentPromise));
        }else{
            return $this->respondNotFound();
        }
    }

    public function delete(Request $request,$id){
        $paymentPromise = $this->getUserAssociatedPaymentPromise($id);
        if($paymentPromise){
            $paymentPromise->delete();
            return $this->respond($this->transformer->transform($paymentPromise));
        }else{
            return $this->respondNotFound();
        }
    }

    private function getUserAssociatedPaymentPromise($id){
        return PaymentPromise::whereHas('account',function ($q){
            $q->whereHas('client',function ($q){
                $q->where('user_id',$this->getCurrentUser()->id);
            });
        })->find($id);
    }

}

