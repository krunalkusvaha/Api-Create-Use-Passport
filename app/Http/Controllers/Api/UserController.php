<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;

class UserController extends Controller
{
  public $successStatus = 200; 

  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
  const RESULT_SUCCESS = "Success RC200";
  const RESULT_ERROR = "Server not found RC100";
  const RESULT_UNAUTH = "RC300";

  public function response($status, $message = "", $data=array()){
		
		$array = [];
		$array["status"] = $status;
		$array["message"] = $message;
		$array["data"] = $data;
		
		return response()->json($array);
	}

  public function register(Request $request) {

    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6|max:8',
      'confirm_password' => 'required|same:password',
    ],[
      'name.required' => 'Please enter name.',
      'email.required' => 'Please enter email.',
      'password.required' => 'Please enter password.',
			'confirm_password.same' => 'Password and confirm password are not match.'
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
    $success['message'] = 'Register successfully.';

    return response()->json([
      'success' => true,
      'token' => $success,
      'user' => $user
    ]);
  }

  public function login(Request $request) { 

    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required',
    ],[
      'email.required' => 'Please enter your email',
      'password.required' => 'Please enter your password',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    if (Auth::attempt(['email' => request('email'), 'password' => request('password')],$validator->validated())) {
      $user = Auth::user();
      // dd($user);  
      $success['token'] =  $user->createToken('MyApp')->accessToken; 
      $success['userId'] = $user->id;
      $success['message'] = 'Login successfully';

      return response()->json([
        'success' => true,
        'token' => $success,
        'user' => $user,
      ]);
    }else{
      return response()->json([
        'success' => false,
        'message' => 'Oppes! You have entered Invalid Email or Password',
      ], 401);
    }

    // if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
    //   $user = Auth::user(); 
    //   // dd($user);
    //   $success['token'] =  $user->createToken('MyApp')->accessToken; 
    //   $success['userId'] = $user->id;
    //   $success['message'] = 'Successfully login';

    //   return response()->json([
    //     'success' => true,
    //     'token' => $success,
    //     'user' => $user
    //   ]); 
    // } 
    // else{  
    //   return response()->json([
    //     'success' => false,
    //     'message' => 'Oppes! You have entered Invalid Email or Password',
    //   ], 401);
    //   // return response()->json(['error'=>'Unauthorised'], 401); 
    // } 
  }

  public function get_userDetails() { 
      
    if($user = Auth::user()){  
      // dd($user);
      return response()->json([
        'success' => true,
        'message' => 'Successfully fetched login user details.',
        'data'=>$user->toArray()
      ]); 
    } else {
      return response()->json([
        'success' => false,
        'message' => 'Unable to fetched login user details.'
      ]);
    }
  }

  public function user_update_post(Request $request) {
        
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email',
    ],[
      'name.required' => 'Please enter name',
      'email.required' => 'Please enter email',
    ]);

    if($validator->fails()){
      return $this->response(self::RESULT_ERROR,$validator->errors()->first());
    }
       
    $user = User::find(Auth::user()->id);
    // dd($user);
    $user->name = $request->name;
    $user->email = $request->email;
    $user->save();
    return $this->response(self::RESULT_SUCCESS,"Profile details has been updated successfully.",array($user));	
  }


  public function change_password_post(Request $request){
		// dd($request->all());
		$validator = Validator::make($request->all(), [
			'current_password' => array(
				'required', function($attribute, $value, $fail){
					if (!Hash::check($value, Auth::user()->password)) {
						$fail('Entered current password does not match with old password');
					}
				}
			),
			'new_password' => 'required',
			'new_confirm_password' => 'required|same:new_password'
		],[
			'current_password.required' => 'Please enter current password',
			'new_password.required' => 'Please enter new password',
			'new_confirm_password.required' => 'Please enter confirm password',
			'new_confirm_password.same' => 'New password and confirm password are not match.'
		]);

		if($validator->fails()){
			return $this->response(self::RESULT_ERROR,$validator->errors()->first());
		}
        
	  // $user=Admin::find(Auth::guard('admin')->user()->id)->update(['password'=> Hash::make($request->new_password)]);
    $user=User::find(Auth::user()->id);
    $user->update(['password'=> Hash::make($request->new_password)]);
    return $this->response(self::RESULT_SUCCESS,"Password has been updated successfully.",array($user));
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
