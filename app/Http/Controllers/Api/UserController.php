<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200; 

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
          return response()->json([
            'success' => false,
            'message' => $validator->errors(),
          ], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('appToken')->accessToken;

        return response()->json([
          'success' => true,
          'token' => $success,
          'user' => $user
      ]);
    }

    public function login() { 
        
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            // dd($user);
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $success['userId'] = $user->id;
            $success['message'] = 'Successfully login';

            return response()->json([
                'success' => true,
                'token' => $success,
                'user' => $user
            ]); 
        } 
        else{  
            return response()->json([
                'success' => false,
                'message' => 'Oppes! You have entered Invalid Email or Password',
            ], 401);
            // return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function userDetails() { 
        
        $user = Auth::user();  
        // dd($user);
        return response()->json(['message' => 'Successfully fetched login user details.','data'=>$user->toArray()]); 
        // return response()->json(['success' => $user], $this-> successStatus); 
    }

    public function logout(Request $request) {
      if (Auth::user()) {
        $user = Auth::user()->token();
        $user->revoke();

        return response()->json([
          'success' => true,
          'message' => 'Logout successfully'
      ]);
      }else {
        return response()->json([
          'success' => false,
          'message' => 'Unable to Logout'
        ]);
      }
    }
}
