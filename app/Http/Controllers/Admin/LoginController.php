<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Hash;

// use App\Lib\OpenFire;
class LoginController extends Controller
{
    private $open_fire_obj;
    public function __construct() {
        // $this->open_fire_obj=new OpenFire();
    }

    public function login(){
        return view('admin.login');
    }
    
    public function authenticate(Request $request){
        $validator= Validator::make($request->all(),[
            'email'=>"required|email",
            'password'=>"required"
        ]);
        
        if($validator->fails()){
            return redirect()->route('login')->with('error','Email and password is required.');
        }
        
        $email=$request->input('email');
        $password=$request->input('password');
        $auth_arr=[
            'email'=>$email,
            'password'=>$password,
            'is_suspended'=>'NO',
            'is_user_relieved'=>0,
            'status'=>'Enabled'
        ];
        
        if(Auth::attempt($auth_arr)){
            $emp_record= \App\Employees::where('user_id', Auth::user()->id)->get(['company_id']);
            if($emp_record->count()==0){
                Auth::logout();
                return redirect()->route('admin.login')->with('error','Invalid email or password.');
            }
            // $this->open_fire_obj->check_create_openfire_user(Auth::user()->id, Auth::user()->name);
            session(['selected_company'=>$emp_record[0]->company_id]);
            return redirect()->route('admin.dashboard')->with('success','Logged in successfully.');
        }
        else{
            return redirect()->route('admin.login')->with('error','Invalid email or password.');
        }
        
    }
    
    public function reset_password(){
		 
        return view('admin.reset_password');
    }
}
