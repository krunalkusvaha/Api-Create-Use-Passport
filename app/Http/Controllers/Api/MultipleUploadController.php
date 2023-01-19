<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Str;


class MultipleUploadController extends Controller
{
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

    public function upload(Request $request) {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'image' => 'required',
        ],[
        'title.required' => 'Please enter title',
        'image.required' => 'Please select document.',
        ]);
        
        if($validator->fails()){
            return $this->response(self::RESULT_ERROR,$validator->errors()->first());
        }

        // $image = array();
        // $doc_name = str_replace(' ','-', $request->title);
        // $doc_name = Str::lower($doc_name);

        // if($files = $request->file('image')){
        //     foreach($files as $upload_file){

        //         $randomnumber = random_int(1000, 9999);
        //         $image_name = $doc_name;
        //         $extension =$image_name .'_'. $randomnumber . '.' . $upload_file->getClientOriginalExtension();


        //         $image_full_name =$extension;
        //         $upload_path = '/uplode/user-document/';
        //         $image_url = $image_full_name;
        //         $upload_file->move(public_path($upload_path,$image_full_name));
        //         $image[] = $image_url;
        //     }
        // }
        // Image::insert ([
        //     'title' => $request->title,
        //     'user_id' => Auth::user()->id,
        //     'image' => implode(' || ',$image),
        // ]);
        // return $this->response(self::RESULT_SUCCESS,"Document uploded successfully.",array($image));




        $doc_name = str_replace(' ','-', $request->title);
        $doc_name = Str::lower($doc_name);
        
        $images = $request->file('image');
        $imageName ='';
        foreach($images as $image){
            $randomnumber = random_int(1000, 9999);
            $image_name = $doc_name;
            $new_name =$image_name .'_'. $randomnumber . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uplode/user-document/'),$new_name);
            $imageName = $imageName.$new_name."|";

        }
        $imagedb = $imageName;
        $file= new Image();
        $file->title = $request->title;
        $file->user_id = Auth::user()->id;
        $file->image = json_encode($imagedb);
        $file->save();
        return $this->response(self::RESULT_SUCCESS,"Document uploded successfully.",array($file));
    }
 
}