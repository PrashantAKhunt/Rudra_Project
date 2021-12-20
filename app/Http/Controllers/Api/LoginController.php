<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Login_log;
use App\Email_format;
use App\Mail\Mails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class LoginController extends Controller {

    public function authenticate(Request $request) {
        
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
                    'device_type' => 'required',
                    'device_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $email = $request->input('email');
        $password = $request->input('password');
        $device_id = $request->input('device_id');
        $device_type = $request->input('device_type');

        $user_detail = User::where(['email' => $email])
        ->where('status','Enabled')
        ->with(['employee.company'=>function($query){$query->select('id','company_name','company_short_name','detail');}])->with(['role'=>function($query){$query->select('id','role_name');}])->get(['id', 'name', 'email', 'password', 'role', 'status','user_attend_type','is_suspended','is_user_relieved'])->first();
		
        if (empty($user_detail)) {
            return response()->json(['status' => false, 'data' => [], 'msg' => config('errors.invalid_login.msg'), 'error' => config('errors.invalid_login.code')]);
        }      
        if (!Hash::check($password, $user_detail->password)) {
            return response()->json(['status' => false, 'data' => [], 'msg' => config('errors.invalid_login.msg'), 'error' => config('errors.invalid_login.code')]);
        }      
        
        if($user_detail->is_suspended=='YES' || $user_detail->is_user_relieved==1){
            return response()->json(['status' => false, 'data' => [], 'msg' => "You are suspended or relived from company. Please contact HR department.", 'error' => config('errors.invalid_login.code')]);
        }
        
        unset($user_detail->password);
		
        // make login log entry and pass access token to user
        $auth_token = base64_encode(time() . rand(10000, 99999) . 'AccountManager');
        $login_log_arr = [
            'device_id' => $device_id,
            'device_type' => $device_type,
            'auth_token' => $auth_token,
            'user_id' => $user_detail->id,
            'created_ip' => $request->ip()
        ];
        Login_log::insert($login_log_arr);        
        $user_detail->auth_token = $auth_token;				
        return response()->json(['status' => true, 'data' => $user_detail, 'msg' => 'Logged in successfully.']);
    }
    
    public function logout(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $auth_token = $request->header('Authorization');
        $user_id = $request->input('user_id');
        Login_log::where(['user_id'=>$user_id,'auth_token'=>$auth_token])->delete();		
        return response()->json(['status' => true, 'msg' => 'Logged out successfully!', 'data' => []]);
    }
    
    /*
     * * Forgot api for send verifation code to user email
     */
    public function forgot_password(Request $request) {
        $amResponse = $amResponseData = [];
        $validator = Validator::make($request->all(), [
                    'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $email = $request->input('email');
        
        //generate 4 digit code for user
        $digits = 4;
        $verification_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        $update_user_data = [
            'verify_code' => $verification_code,
			'is_verified' => 'No',
            'updated_ip' => $request->ip(),
        ];
        if (User::where('email', $email)->update($update_user_data)) {
            $userData = User::where('email', $email)->first();
            $userArray = $userData->toArray();

            //Send verificationcode to user for email
            $emailData = Email_format::find(2)->toArray(); // 2 = Forgot Password Email Format
            $subject = $emailData['subject'];
            $mailformat = $emailData['emailformat'];
            $mail_body = str_replace("%name%", $userArray['name'], str_replace("%verification_code%", $verification_code, stripslashes($mailformat)));			
            try {
                Mail::to($email)->send(new Mails($subject, $mail_body));
            } catch (\Exception $e) {				
				//We have to send mail again - error_code - 10012
				return response()->json(['status' => false, 'msg' => config('errors.mail_send_error.msg'), 'data' => [], 'error' => config('errors.mail_send_error.code')]);
            }
            $amResponseData = [
                'email' => $userData['email'],
            ];
            return response()->json(['status' => true, 'msg' => "Please check your email for verification code.", 'data' => $amResponseData]);
        } else {
            return response()->json(['status' => false, 'msg' => "Email does not exists in record.", 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }    
    
	public function resend_otp(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $user_detail = User::where('email', $request->input('email'))->where('role', 1)->get(['name', 'is_verified', 'role']);
        if ($user_detail->count() == 0) {
            return response()->json(['status' => false, 'msg' => "Email is not registered.", 'data' => [], 'error' => config('errors.wrong_login_type.code')]);
        }
        
        $digits = 4;
        $verification_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

        //Send verificationcode to user for email
        $emailData = Email_format::find(3)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mail_body = str_replace("%name%", $user_detail[0]->name, str_replace("%verification_code%", $verification_code, stripslashes($mailformat)));

        try {
            Mail::to($request->input('email'))->send(new Mails($subject, $mail_body));
        } catch (\Exception $e) {
            //We have to send mail again - error_code - 10012
			return response()->json(['status' => false, 'msg' => config('errors.mail_send_error.msg'), 'data' => [], 'error' => config('errors.mail_send_error.code')]);
        }

        $update_user_data = [ 'verify_code' => $verification_code ];
        User::where('email', $request->input('email'))->update($update_user_data);

        return response()->json(['status' => true, 'msg' => 'Please check your email for verification code.', 'data' => []]);
    }
	
    /*
     * * Forgot api for send verifation code to user email
     */
    public function verify_user(Request $request) {
        $amResponse = $amResponseData = [];
        $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'verify_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $email = $request->input('email');
        $verify_code = $request->input('verify_code');
        $userData = User::where('email', $email)->where('verify_code', $verify_code)->first();
        if (!empty($userData)) {
            $userArray = $userData->toArray();

            $update_user_data = [
                'verify_code' => null,
				'is_verified' => 'Yes',
                'modified_by' => $userData['id'],
                'updated_ip' => $request->ip(),				
            ];

            User::where('id', $userArray['id'])->update($update_user_data);

            $amResponseData = [
                'user_id' => $userData['id'],
                'email' => $userData['email'],
                'is_verified' => 'Yes', //get from global.php file
            ];

            return response()->json(['status' => true, 'msg' => 'Verified successfully', 'data' => $amResponseData]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Not Verified successfully', 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

	/*
	* * update password of user in forgot password screen
	*/
    public function update_forgot_password(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $email = $request->input('email');
        $password = Hash::make($request->input('password'));

        $update_user_data = [
            'password' => $password,
            'login_attempt' => 0,
            'updated_ip' => $request->ip(),
        ];

        if (User::where('email', $email)->update($update_user_data)) {
            return response()->json(['status' => true, 'msg' => 'Update password successfully!', 'data' => []]);
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }	
    
    public function get_app_update(){
        $last_app_version= \App\App_version::where('status','Enabled')->orderBy('created_at','DESC')->get();
        
        if($last_app_version->count()==0){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        
        return response()->json(['status'=>true,'msg'=>'record found','data'=>['latest_version'=>$last_app_version[0]->version_number]]);
    }
    
    public function update_app_version($version){
        \App\App_version::insert(['version_number'=>$version,'status'=>'Enabled','created_at'=>date('Y-m-d H:i:s')]);
        //Login_log::truncate();
        return response()->json(['status'=>true,'msg'=>'New version set successfully','data'=>[]]);
    }
    
    public function check_imei(Request $request){
        $validator = Validator::make($request->all(), [
            'imei_number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $imei_number = explode(',',$request_data['imei_number']);
        $ALLOW_IMEI = config('constants.ALLOW_IMEI');
        foreach ($imei_number as $key => $value) {
            if (in_array($value, $ALLOW_IMEI)) {
                return response()->json(['status' => true, 'msg' => 'IMEI number match successfully', 'data' => []]);
            }
        }
        return response()->json(['status' => false, 'data' => [], 'msg' => "IMEI number not match", 'error' => config('errors.invalid_login.code')]);
    }

    public function update_device_id(Request $request){
        $validator = Validator::make($request->all(), [
                    'old_device_id' => 'required',
                    'new_device_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        Login_log::where('device_id',$request_data['old_device_id'])->update(['device_id' => $request_data['new_device_id']]);
        return response()->json(['status'=>true,'msg'=>'Device id updated.','data'=>[]]);
    }
}