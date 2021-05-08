<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\Transformers\ClientTransformer;
use App\Models\Client;
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
        
        $client = new Client();
        $client->name = $request->name;
        $client->email = $request->email;
        $client->social_id = $request->social_id;
        $client->ui=$request->ui;
        $client->work_address ='asdfads'.$request->work_address;
        $client->home_address = $request->home_address;
        $client->contact_number = $request->contact_number;
        $client->firm_id = $request->firm_id;
        $client->user_id = $this->getCurrentUser()->id;
        $client->save();


        return $this->respondCreatedWithData('',$this->transformer->transform($client));
    }
}
