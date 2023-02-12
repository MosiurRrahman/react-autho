<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(){
        return User::get();
    }

    public function registerStore(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:6',
            'c_password'=>'required|min:6|same:password',
        ]);
       if($validator->fails()){
           $response =[
             'success'=>false,
             'message'=>$validator->errors(),
           ];
           return response()->json($response);
       }
       $user = User::create([
          'name' => $request->name,
          'email' => $request->email,
          'password' => Hash::make($request->password),
       ]);
       $success['token']=$user->createToken('authToken')->plainTextToken;
       $success['name']=$user->name;
       if($user){
           $response = [
               'success'=> true,
               'data'=>$success,
               'message'=>"User Register Successfully"
           ];
           return response()->json($response);
       }
    }
    public function loginDashboard(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $response =[
                'success'=>false,
                'message'=>$validator->errors(),
            ];
            return response()->json($response);
        }

        $credentials = $request->only(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            $response =[
                'message' => ['Credentials Wrong']
            ];
            return response()->json($response);
        }

        $user = Auth::user();

        $success['token']=$user->createToken('authToken')->plainTextToken;
        $success['name']=$user->name;
        if($user){
            $response = [
                'success'=> true,
                'data'=>$success,
                'message'=>"User Register Successfully"
            ];
            return response()->json($response);
        }
    }
}
