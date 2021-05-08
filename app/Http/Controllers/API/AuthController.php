<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Transformers\UserTransformer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Error;

class AuthController extends ApiController{
    private $transformer;
    
    public function __construct(Transformers\UserTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function authenticate(Request $request){
        $credentials = $request->only('email', 'password');

        $response = [
            'token' => null,
            'user' => null,
        ];
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = \JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            $response['token'] = $token;
        } catch (\JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = \Auth::user();

        $account = $this->getCurrentUserAccount();

        $user = $this->transformer->transform($user);

        $response['user'] = $user;

        return $this->respond($response);
    }

    public function register(Request $request){
        $validated=  $request->validate(['email'=>'required',
        'first_name'=>'required',
        'last_name'=>'required',
        'email'=>'required',
        'password'=>'required']);
        
        $user = new User([
            'name' => trim($request->input('first_name') . " " . $request->input('last_name')),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        $user->save();
        return $this->respondCreatedWithData('test',$user);
    }
}