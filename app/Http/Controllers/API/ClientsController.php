<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\Transformers\ClientTransformer;
use App\Models\Client;
use App\Services\ClientsService;
use Illuminate\Http\Request;

class ClientsController extends ApiController
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

    public function __construct(ClientTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function create(Request $request){
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required',
            'social_id'=>'required',
            'ui'=>'required',
            'firm_id'=>'required'
        ]);
        
        $client = ClientsService::create($request);
        return $this->respondCreatedWithData('',$this->transformer->transform($client));
    }

    public function get(Request $request,$id){
        $client =  $this->getUserAssociatedClient($id);

        if($client){
            return $this->transformer->transform($client);
        }else{
            return $this->respondNotFound();
        }
    }

    public function getAll(Request $request){
        $clients = $this->getUserAssociatedClients();
        if($clients){
            return $this->transformer->transform($clients);
        }else{
            return $this->respondNotFound();
        }
    }

    public function update(Request $request,$id){
        $validator = $request->validate(['name' => 'required|max:255',
        'email' => 'required',
        'social_id'=>'required',
        'ui'=>'required',]);

        $client = $this->getUserAssociatedClient($id);
        if($client){
            return $this->transformer->transform(ClientsService::update($client,$request));
        }else{
           return $this->respondNotFound();
        }
    }

    public function delete(Request $request,$id){
        $client = $this->getUserAssociatedClient($id);
        if($client){
            return $this->transformer->transform(ClientsService::delete($client));
        }else{
           return $this->respondNotFound();
        }
    }



    private function getUserAssociatedClient($id){
        return $this->getCurrentUser()->clients()->find($id);
    }
    private function getUserAssociatedClients(){
        return $this->getCurrentUser()->clients()->get();
    }
}
