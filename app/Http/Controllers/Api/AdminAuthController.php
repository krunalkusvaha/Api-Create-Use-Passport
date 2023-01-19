<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;


class AdminAuthController extends Controller 
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const RESULT_SUCCESS = "RC200";
	const RESULT_ERROR = "RC100";
	const RESULT_UNAUTH = "RC300";

    public function response($status, $message = "", $data=array()){
		
		$array = [];
		$array["status"] = $status;
		$array["message"] = $message;
		$array["data"] = $data;
		
		return response()->json($array);
	}

    public function adminLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required',
        ],[
            'email.required' => 'Please enter your email',
            'password.required' => 'Please enter your password',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = Admin::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token,'user' => $user];
                return response($response, 200);
            } else {
                $response = ["message" => "Oppes! You have entered Invalid Email or Password."];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
        // if(Auth::guard('api')->check(['email' => request('email'), 'password' => request('password')])){ 
        //     $user = Auth::guard("api")->user(); 
        //     $success['token'] =  $user->createToken('MyAdmin')->accessToken; 
        //     $success['userId'] = $user->id;
        //     $success['message'] = 'Successfully login admin';

        //     return response()->json([
        //         'success' => true,
        //         'token' => $success,
        //         'user' => $user
        //     ]); 
        // } 
        // else{  
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Oppes! You have entered Invalid Email or Password',
        //     ], 401);
        // } 
    }

    public function adminlogout(Request $request) {
        if (Auth::guard('admin')->user()) {
            $user = Auth::guard('admin')->user()->token();
            $user->revoke();

            return response()->json([
            'success' => true,
            'message' => 'You are logged out successfully.'
        ]);
        }else {
            return response()->json([
            'success' => false,
            'message' => 'Unable to logout admin.'
            ]);
        }
    }

    public function get_admin_profile() {

        if($user = Auth::guard('admin')->user()){  
        // dd($user);
            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched admin profile.',
                'data'=>$user->toArray()
            ]); 
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetched admin profile details.'
            ]);
        }
    }

    public function profile_update_post(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
        ],[
            'username.required' => 'Please enter username',
            'email.required' => 'Please enter email',
        ]);
        if($validator->fails()){
			return $this->response(self::RESULT_ERROR,$validator->errors()->first());
		}
        
        $user = Admin::find(Auth::guard('admin')->user()->id);
        // dd($user);
		$user->username = $request->username;
		$user->email = $request->email;
		$user->save();
		return $this->response(self::RESULT_SUCCESS,"Profile details has been updated successfully.",array($user));	
    }

    public function change_password_post(Request $request){
		// dd($request->all());
		$validator = Validator::make($request->all(), [
			'current_password' => array(
				'required', function($attribute, $value, $fail){
					if (!Hash::check($value, Auth::guard('admin')->user()->password)) {
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
        $user=Admin::find(Auth::guard('admin')->user()->id);
        $user->update(['password'=> Hash::make($request->new_password)]);
		return $this->response(self::RESULT_SUCCESS,"Password has been updated successfully.",array($user));
	}
}
