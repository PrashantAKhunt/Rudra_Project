<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Lib;

use Illuminate\Support\Facades\Config;
use App\Email_format;
use App\Mail\Mails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use DateTime;

use App\Travel_booking_files;
use App\Travel_option;
use App\User;
use App\Asset;
use App\AssetAccess;
use App\Interview;

/**
 * Description of CommonTask
 *
 * @author kishan
 */
class CommonTask {

    private $super_admin;
    private $hrMail;

    public function __construct() {
        $this->super_admin = \App\User::where('role', 1)->first();
        $this->hrMail = config::get('app.HR_EMAIL');
    }

 public function convert_digits_into_words($amount)
    {
        $number = $amount;
                    $no = floor($number);
                    $point = round($number - $no, 2) * 100;
                    $hundred = null;
                    $digits_1 = strlen($no);
                    $i = 0;
                    $str = array();
                    $words = array('0' => '', '1' => 'one', '2' => 'two',
                        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
                        '7' => 'seven', '8' => 'eight', '9' => 'nine',
                        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
                        '13' => 'thirteen', '14' => 'fourteen',
                        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
                        '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
                        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
                        '60' => 'sixty', '70' => 'seventy',
                        '80' => 'eighty', '90' => 'ninety');
                    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
                    while ($i < $digits_1) {
                        $divider = ($i == 2) ? 10 : 100;
                        $number = floor($no % $divider);
                        $no = floor($no / $divider);
                        $i += ($divider == 10) ? 1 : 2;
                        if ($number) {
                            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                            $str [] = ($number < 21) ? $words[$number] .
                                    " " . $digits[$counter] . $plural . " " . $hundred :
                                    $words[floor($number / 10) * 10]
                                    . " " . $words[$number % 10] . " "
                                    . $digits[$counter] . $plural . " " . $hundred;
                        } else
                            $str[] = null;
                    }
                    $str = array_reverse($str);
                    $result = implode('', $str);
                    $points = ($point) ? $words[$point / 10] . " " .
                            $words[$point = $point % 10] : '';

                            if ($point) {
                                return ucfirst($result) . "Rupee  " . ucfirst($points) . " Paise";
                            } else {
                                return ucfirst($result) . "Rupee  ";
                            }
                            
                          
    }
    public function calculate_leave_days($start_date, $end_date, $start_day, $end_day) {


        $earlier = new DateTime($start_date);
        $later = new DateTime($end_date);

        $diff = $later->diff($earlier)->format("%a");

        $diff = $diff + 1;
        if($start_day!=1){
            $diff=$diff-0.5;
        }
        if($end_day!=1){
            $diff=$diff-0.5;
        }
        return $diff;
    }
	
	public function search_document($search_keyword) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://139.59.8.252:3001/api/pdf_content/search_pdf/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('keyword' => $search_keyword),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
	
	public function check_holiday_weekend_attendance($user_id,$attend_date) {
        
        //check if this is holiday
        $holiday_check = \App\Holiday::where('start_date','<=',$attend_date)->where('end_date','>=',$attend_date)->get()->count();
        
        $is_holiday_weekend=0;
        //check if date is weenend
        if (date('D', strtotime($attend_date)) == 'Sun') {
            $is_holiday_weekend = 1;
        }
        elseif($holiday_check>0){
            $is_holiday_weekend = 1;
        }
        else{
            $is_holiday_weekend = 0;
        }
        
        if($is_holiday_weekend){
            //check if approved the request
            $approval_check = \App\WorkOff_AttendanceRequest::where('date',$attend_date)
                    ->where('user_id',$user_id)
                    ->where('status','Approved')
                    ->get();
            if($approval_check->count()>0){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }
        
    }

    public function time_to_decimal($time) {
        $timeArr = explode(':', $time);
        $decTime = ($timeArr[0] * 60) + ($timeArr[1]) + ($timeArr[2] / 60);

        return $decTime;
    }

    public function getUserRole($user_id) {
        $user_role_data = \App\User::where('id', $user_id)->get('role');
        return $user_role_data[0]->role;
    }

    public function getRoleByModulePermission($module_id, $permission_id) {
        $role_ids = \App\Role_module::where('module_id', $module_id)
                ->whereRaw('FIND_IN_SET(' . $permission_id . ',access_level)')
                ->get(['role_id']);
        return $role_ids;
    }

    public function getPermissionArr($role, $module_id) {
        $permission_data = \App\Role_module::where(['role_id' => $role, 'module_id' => $module_id])->get();
        if ($permission_data->count() == 0) {
            return [];
        }
        $permission_arr = explode(',', $permission_data[0]->access_level);
        return $permission_arr;
    }

    public function getWeekStartAndEndDate($week, $year) {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('Y-m-d');
        return date('d-m-Y', strtotime($ret['week_start'])) . ' to ' . date('d-m-Y', strtotime($ret['week_end']));
    }

    //list of functions for send mails

    /*
     * $data contains below index
     * start_date,start_day,end_date,end_day,leave_subject,description,name,assign_work_details
     */
    public function assignLeaveWorkEmail($data) {

        $emailData = Email_format::find(15)->toArray(); // 5 = Applied Leave
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%assign_name%", $data['assign_name'], $mailformat);
        $mailformat = str_replace("%start_date%", $data['start_date'], $mailformat);
        $mailformat = str_replace("%start_day%", config::get('constants.LEAVE_DAY.' . $data['start_day']), $mailformat);
        $mailformat = str_replace("%end_date%", $data['end_date'], $mailformat);
        $mailformat = str_replace("%end_day%", config::get('constants.LEAVE_DAY.' . $data['end_day']), $mailformat);
        $mailformat = str_replace("%subject%", $data['leave_subject'], $mailformat);
        $mailformat = str_replace("%description%", $data['description'], $mailformat);
        $mailformat = str_replace("%leave_user_name%", $data['name'], $mailformat);
        $mailformat = str_replace("%assign_work_details%", $data['assign_work_details'], $mailformat);

        Mail::to($data['email'])->cc($this->super_admin->email)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * start_date,start_day,end_date,end_day,leave_subject,description,notify_id
     */

    public function applyLeaveEmail($data) {

        $emailData = Email_format::find(5)->toArray(); // 5 = Applied Leave
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%start_date%", $data['start_date'], $mailformat);
        $mailformat = str_replace("%start_day%", config::get('constants.LEAVE_DAY.' . $data['start_day']), $mailformat);
        $mailformat = str_replace("%end_date%", $data['end_date'], $mailformat);
        $mailformat = str_replace("%end_day%", config::get('constants.LEAVE_DAY.' . $data['end_day']), $mailformat);
        $mailformat = str_replace("%subject%", $data['leave_subject'], $mailformat);
        $mailformat = str_replace("%description%", $data['description'], $mailformat);


        $notify_arr = explode(',', $data['notify_id']);
        $emailList = DB::table('users')->whereIn('id', $notify_arr)->pluck('email')->toArray();
        
        Mail::to($this->super_admin->email)->cc($emailList)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,leave_person_name,assigned_person_name,assign_work_reject_note
     */

    public function rejectLeaveWorkEmail($data) {

        $emailData = Email_format::find(16)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%leave_person_name%", $data['leave_person_name'], $mailformat);
        $mailformat = str_replace("%assigned_person_name%", $data['assigned_person_name'], $mailformat);
        $mailformat = str_replace("%assign_work_reject_note%", $data['assign_work_reject_note'], $mailformat);

        Mail::to($data['to_email'])->cc($this->super_admin->email)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,leave_person_name,assigned_person_name
     */

    public function acceptLeaveWorkEmail($data) {

        $emailData = Email_format::find(17)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%leave_person_name%", $data['leave_person_name'], $mailformat);
        $mailformat = str_replace("%assigned_person_name%", $data['assigned_person_name'], $mailformat);

        Mail::to($data['to_email'])->cc($this->super_admin->email)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,name,approver_name
     */

    public function remoteAttendanceApprovedEmail($data) {

        $emailData = Email_format::find(18)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%approver_name%", $data['approver_name'], $mailformat);

        Mail::to($data['to_email'])->cc($this->super_admin->email)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,name,approver_name
     */

    public function remoteAttendanceRejectedEmail($data) {

        $emailData = Email_format::find(21)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%approver_name%", $data['approver_name'], $mailformat);
        $mailformat = str_replace("%reject_reason%", $data['reject_reason'], $mailformat);

        Mail::to($data['to_email'])->cc($this->super_admin->email)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,attend_name,department,cc_email_arr(array containing email ids to cc)
     */

    public function remoteAttendanceEmail($data) {

        $emailData = Email_format::find(23)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%attend_name%", $data['attend_name'], $mailformat);
        $mailformat = str_replace("%department%", $data['department'], $mailformat);

        array_push($data['cc_email_arr'], $this->super_admin->email);

        Mail::to($data['to_email'])->cc($data['cc_email_arr'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,attend_name,department,cc_email_arr(array containing email ids to cc)
     */

    public function onDutyAttendanceEmail($data) {

        $emailData = Email_format::find(25)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%attend_request_username%", $data['attend_request_username'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);
        $mailformat = str_replace("%start_time%", $data['start_time'], $mailformat);
        $mailformat = str_replace("%end_time%", $data['end_time'], $mailformat);
        $mailformat = str_replace("%remote_punch_reason%", $data['remote_punch_reason'], $mailformat);

        array_push($data['cc_email_arr'], $this->super_admin->email);

        Mail::to($data['to_email'])->cc($data['cc_email_arr'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,attend_name,department,cc_email_arr(array containing email ids to cc)
     */

    public function resignationEmail($data) {

        $emailData = Email_format::find(26)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%sr_name%", $data['sr_name'], $mailformat);
        $mailformat = str_replace("%resignee_name%", $data['resignee_name'], $mailformat);

        //array_push($data['cc_email_arr'],$this->super_admin->email);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,username,resign_user_name 
     */

    public function handoverUserEmail($data) {

        $emailData = Email_format::find(29)->toArray();
        
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%username%", $data['username'], $mailformat);
        $mailformat = str_replace("%resign_user_name%", $data['resign_user_name'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    public function compoffEmail($data) {

        $emailData = Email_format::find(71)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%leave_type%", $data['leave_type'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);

        Mail::to($data['to_email'])->cc(array_merge($data['hr_email'],$data['admin_email']))->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,attend_name,department,cc_email_arr(array containing email ids to cc)
     */

    public function revokeResignationEmail($data) {

        $emailData = Email_format::find(61)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%sr_name%", $data['sr_name'], $mailformat);
        $mailformat = str_replace("%resignee_name%", $data['resignee_name'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,attend_name,department,cc_email_arr(array containing email ids to cc)
     */

    public function relievingEmail($data) {

        $emailData = Email_format::find(65)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);
        $mailformat = str_replace("%resignee_name%", $data['resignee_name'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,attend_name,department,cc_email_arr(array containing email ids to cc)
     */

    public function retainEmail($data) {

        $emailData = Email_format::find(66)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%resignee_name%", $data['resignee_name'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,attend_name,department,cc_email_arr(array containing email ids to cc)
     */

    public function approveResignationEmail($data) {

        $emailData = Email_format::find(62)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%sr_name%", $data['sr_name'], $mailformat);
        $mailformat = str_replace("%resignee_name%", $data['resignee_name'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,attend_name,department,cc_email_arr(array containing email ids to cc)
     */

    public function handoverResignationEmail($data) {

        $emailData = Email_format::find(63)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%sr_name%", $data['sr_name'], $mailformat);
        $mailformat = str_replace("%resignee_name%", $data['resignee_name'], $mailformat);
        $mailformat = str_replace("%handover_name%", $data['handover_name'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,attend_name,department,cc_email_arr(array containing email ids to cc)
     */

    public function intimateResignationEmail($data) {

        $emailData = Email_format::find(64)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%sr_name%", $data['sr_name'], $mailformat);
        $mailformat = str_replace("%resignee_name%", $data['resignee_name'], $mailformat);
        $mailformat = str_replace("%handover_name%", $data['handover_name'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    public function travelEmail($data) {

        $emailData = Email_format::find(67)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%text%", $data['text'], $mailformat);
        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%travelers%", $data['travelers'], $mailformat);
        $mailformat = str_replace("%travel_company%", $data['travel_company'], $mailformat);
        $mailformat = str_replace("%ticket_no%", $data['ticket_no'], $mailformat);
        $mailformat = str_replace("%departure_datetime%", $data['departure_datetime'], $mailformat);
        $mailformat = str_replace("%arrival_datetime%", $data['arrival_datetime'], $mailformat);
        $mailformat = str_replace("%total_amount%", $data['total_amount'], $mailformat);
        $mailformat = str_replace("%from%", $data['from'], $mailformat);
        $mailformat = str_replace("%to%", $data['to'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }
	
	public function travel_request($data) {

        $emailData = Email_format::find(73)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%text%", $data['text'], $mailformat);
        $mailformat = str_replace("%name%", $data['name'], $mailformat);
      

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
      $data contains below index
      to_email,superUser_name,text,name
     */
    public function travel_options($data) {

        $emailData = Email_format::find(74)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%superUser_name%", $data['superUser_name'], $mailformat);
        $mailformat = str_replace("%text%", $data['text'], $mailformat);
        $mailformat = str_replace("%name%", $data['name'], $mailformat);
      
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
      $data contains below index
      to_email,assistent_name,arrival_datetime,from,to,travel_via,details,travel_image,approved_by
     */
    public function approve_travel_option($data) {

        $emailData = Email_format::find(75)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%assistent_name%", $data['assistent_name'], $mailformat);
        $mailformat = str_replace("%approved_by%", $data['approved_by'], $mailformat);
        $mailformat = str_replace("%approval_note%", $data['approval_note'], $mailformat);
        $mailformat = str_replace("%request_no%", $data['request_no'], $mailformat);


        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
      $data contains below index
      to_email,reject_note,unique_no
     */
    public function reject_all_travel_option($data) {

        $emailData = Email_format::find(88)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%unique_no%", $data['unique_no'], $mailformat);
        $mailformat = str_replace("%reject_note%", $data['reject_note'], $mailformat);
       
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }


     /*
      $data contains below index
      to_email,assistent_name,arrival_datetime,departure_datetime,from,to,
      travel_via,payment_type, amount, cc_email_arr(array containing email ids to cc)
     */
    public function travel_booking_confirmed($data, $id)
    {

        $emailData = Email_format::find(76)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%assistent_name%", $data['assistent_name'], $mailformat);
       
        $mailformat = str_replace("%travel_via%", $data['travel_via'], $mailformat);
        $mailformat = str_replace("%payment_type%", $data['payment_type'], $mailformat);
        $mailformat = str_replace("%amount%", $data['amount'], $mailformat);
        $mailformat = str_replace("%shedule%", $data['shedule'], $mailformat);

        $attachments_arr = [];

        $files = Travel_booking_files::where('travel_option_id', $id)->pluck('file_name')->toArray();

        $travel_data = Travel_option::where('id', $id)->get(['from', 'to', 'departure_datetime']);

        foreach ($files as $key => $file) {


            $full_path = asset('storage/' . str_replace('public/', '', $file));

            $path_info = pathinfo($full_path);

            $as_name = $travel_data[0]->from . "_" . $travel_data[0]->to . date('d-m-Y', strtotime($travel_data[0]->departure_datetime)) . "." .  $path_info['extension'];
            $attachments_arr[$full_path] =
                [
                    'as' => $as_name,
                    'mime' => $data['mimeType'][$key]
                ];
        }



        Mail::to($data['to_email'])->cc($data['email_list'])->queue(new Mails($subject, $mailformat), function ($message) use ($attachments_arr) {
            foreach ($attachments_arr as $filePath => $fileParameters) {
                $message->attach($filePath, $fileParameters);
            }
        });
    }

    public function hotelEmail($data) {

        $emailData = Email_format::find(72)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%text%", $data['text'], $mailformat);
        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%stayed_user%", $data['stayed_user'], $mailformat);
        $mailformat = str_replace("%hotel_name%", $data['hotel_name'], $mailformat);
        $mailformat = str_replace("%booking_no%", $data['booking_no'], $mailformat);
        $mailformat = str_replace("%check_in_datetime%", $data['check_in_datetime'], $mailformat);
        $mailformat = str_replace("%check_out_datetime%", $data['check_out_datetime'], $mailformat);
        $mailformat = str_replace("%total_amount%", $data['total_amount'], $mailformat);
        $mailformat = str_replace("%place%", $data['place'], $mailformat);        

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * assign_person_name,asset_name,email
     */

    public function assignAssetEmail($data) {

        $emailData = Email_format::find(19)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%assign_person_name%", $data['assign_person_name'], $mailformat);
        $mailformat = str_replace("%asset_name%", $data['asset_name'], $mailformat);

        Mail::to($this->super_admin->email)->cc($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * asset_name,email
     */

    public function assignConfirmationAssetEmail($data) {

        $emailData = Email_format::find(20)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%asset_name%", $data['asset_name'], $mailformat);

        Mail::to($this->super_admin->email)->cc($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * asset_name,email_list,reason,asseiner_username
     */

    public function assignRejectAssetEmail($data) {

        $emailData = Email_format::find(53)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%asset_name%", $data['asset_name'], $mailformat);
        $mailformat = str_replace("%asseiner_username%", $data['asseiner_username'], $mailformat);
        $mailformat = str_replace("%reason%", $data['reason'], $mailformat);

        Mail::to($data['email_list'])->cc($this->super_admin->email)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * asset_name,email
     */

    public function sendConfirmationEmail($data) {

        $emailData = Email_format::find(22)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%position%", $data['position'], $mailformat);
        $mailformat = str_replace("%email%", $data['email'], $mailformat);
        $mailformat = str_replace("%link%", $data['link'], $mailformat);
        $mailformat = str_replace("%password%", $data['password'], $mailformat);

        Mail::to($this->super_admin->email)->cc($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * asset_name,email
     */

    public function sendJoinConfirmationEmail($data) {

        $emailData = Email_format::find(24)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%name%", $data['name'], $mailformat);

        Mail::to($data['hr_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * policy_name
     */

    public function sendRevisePolicyEmail($data) {
        $emailData = Email_format::find(27)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%policy%", $data['policy'], $mailformat);
        $emailList = DB::table('users')->where('status','Enabled')->pluck('email')->toArray();

        Mail::to($this->super_admin->email)->cc($emailList)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * policy_name
     */

    public function approveRevisePolicyEmail($data) {
        $emailData = Email_format::find(28)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%policy%", $data['policy'], $mailformat);
        $emailList = DB::table('users')->pluck('email')->toArray();

        Mail::to($this->super_admin->email)->cc($emailList)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * policy_name
     */

    public function approveRejectPaymentEmail($data) {

        $amount = !empty($data['amount']) ? $data['amount'] : 0;

        $emailData = Email_format::find(31)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%username%", $data['username'], $mailformat);
        $mailformat = str_replace("%amount%", $amount, $mailformat);
        $mailformat = str_replace("%status%", $data['status'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * title,description,email_list
     */

    public function announcementEmail($data) {
        $emailData = Email_format::find(10)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%description%", $data['description'], $mailformat);

        Mail::to($data['email_list'])->cc($this->super_admin->email)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * username,budget_meeting_number,status,email
     */

    public function approveRejectBudgetEmail($data) {

        $emailData = Email_format::find(32)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%username%", $data['username'], $mailformat);
        $mailformat = str_replace("%budget_meeting_number%", $data['budget_meeting_number'], $mailformat);
        $mailformat = str_replace("%status%", $data['status'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * Budget File
     */

    public function approveRejectSignEmail($data) {

        $emailData = Email_format::find(33)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%username%", $data['username'], $mailformat);
        $mailformat = str_replace("%doctype%", $data['doctype'], $mailformat); // Pre Signed and Pro Sign
        $mailformat = str_replace("%status%", $data['status'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * request_user_name, deliver_user_name, letter_head_number, email_list
     */

    public function preSignedLetterHeadDeliveryEmail($data) {

        $emailData = Email_format::find(34)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%request_user_name%", $data['request_user_name'], $mailformat);
        $mailformat = str_replace("%deliver_user_name%", $data['deliver_user_name'], $mailformat); // Pre Signed and Pro Sign
        $mailformat = str_replace("%letter_head_number%", $data['letter_head_number'], $mailformat);

        Mail::to($data['email_list'])->cc($this->super_admin->email)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * request_user_name, deliver_user_name, letter_head_number, email_list
     */

    public function LetterHeadDeliveryEmail($data) {

        $emailData = Email_format::find(35)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%request_user_name%", $data['request_user_name'], $mailformat);
        $mailformat = str_replace("%deliver_user_name%", $data['deliver_user_name'], $mailformat); // Pre Signed and Pro Sign
        $mailformat = str_replace("%letter_head_number%", $data['letter_head_number'], $mailformat);

        Mail::to($data['email_list'])->cc($this->super_admin->email)->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * leave_user, start_end, leave_subject, start_date,end_date,email
     */

    public function lognLeaveEmail($data) {

        $emailData = Email_format::find(36)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%leave_user%", $data['leave_user'], $mailformat);
        $mailformat = str_replace("%start_end%", $data['start_end'], $mailformat);
        $mailformat = str_replace("%leave_subject%", $data['leave_subject'], $mailformat);
        $mailformat = str_replace("%start_date%", $data['start_date'], $mailformat);
        $mailformat = str_replace("%end_date%", $data['end_date'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function applyLoanEmail($data) {

        $emailData = Email_format::find(43)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%admin_user%", $data['admin_user'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%loan_amount%", $data['loan_amount'], $mailformat);
        $mailformat = str_replace("%loan_term%", $data['loan_term'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function editLoanEmail($data) {

        $emailData = Email_format::find(44)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%admin_user%", $data['admin_user'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%loan_amount%", $data['loan_amount'], $mailformat);
        $mailformat = str_replace("%loan_term%", $data['loan_term'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function cancelLoanEmail($data) {

        $emailData = Email_format::find(45)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%admin_user%", $data['admin_user'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%loan_amount%", $data['loan_amount'], $mailformat);
        $mailformat = str_replace("%loan_term%", $data['loan_term'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function approveLoanEmail($data) {

        $emailData = Email_format::find(46)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%loan_amount%", $data['loan_amount'], $mailformat);
        $mailformat = str_replace("%loan_term%", $data['loan_term'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function rejectLoanEmail($data) {

        $emailData = Email_format::find(47)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%loan_amount%", $data['loan_amount'], $mailformat);
        $mailformat = str_replace("%loan_term%", $data['loan_term'], $mailformat);
        $mailformat = str_replace("%reject_note%", $data['reject_note'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_user_name, budget_sheet_number, to_email,request_user_name
     */

    public function budgetSheetRequestEmail($data) {

        $emailData = Email_format::find(48)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%to_user_name%", $data['to_user_name'], $mailformat);
        $mailformat = str_replace("%budget_sheet_number%", $data['budget_sheet_number'], $mailformat);
        $mailformat = str_replace("%request_user_name%", $data['request_user_name'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * user_name,place,reason,date,notify_id
     */

    public function applyAttendaceRequestEmail($data) {

        $emailData = Email_format::find(52)->toArray(); // 52 = Applied Remote Visit Attendance
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%detail%", $data['detail'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * user_name,place,reason,reject_reason,date,notify_id
     */

    public function rejectAttendaceRequestEmail($data) {

        $emailData = Email_format::find(51)->toArray(); // 51 = reject Applied Remote Visit Attendance
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%place%", $data['place'], $mailformat);
        $mailformat = str_replace("%reason%", $data['reason'], $mailformat);
        $mailformat = str_replace("%reject_reason%", $data['reject_reason'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * user_name,place,reason,date,notify_id
     */

    public function approveAttendaceRequestEmail($data) {

        $emailData = Email_format::find(50)->toArray(); // 49 = Approved Applied Remote Visit Attendance
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%place%", $data['place'], $mailformat);
        $mailformat = str_replace("%reason%", $data['reason'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * user_name,place,reason,date,notify_id
     */

    public function cancelAttendaceRequestEmail($data) {

        $emailData = Email_format::find(49)->toArray(); // 49 = Cancel Applied Remote Visit Attendance
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%place%", $data['place'], $mailformat);
        $mailformat = str_replace("%reason%", $data['reason'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * driver_name,trip_user_name,to_email_list
     */

    public function tripOpeningAlertEmail($data) {

        $emailData = Email_format::find(54)->toArray(); // 49 = Cancel Applied Remote Visit Attendance
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%driver_name%", $data['driver_name'], $mailformat);
        $mailformat = str_replace("%trip_user_name%", $data['trip_user_name'], $mailformat);


        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * driver_name,trip_user_name,to_email_list
     */

    public function tripCloseAlertEmail($data) {

        $emailData = Email_format::find(56)->toArray(); // 49 = Cancel Applied Remote Visit Attendance
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%driver_name%", $data['driver_name'], $mailformat);
        $mailformat = str_replace("%trip_user_name%", $data['trip_user_name'], $mailformat);


        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * driver_name,trip_user_name,to_email_list
     */

    public function tripApproveAlertEmail($data) {

        $emailData = Email_format::find(57)->toArray(); // 49 = Cancel Applied Remote Visit Attendance
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%driver_name%", $data['driver_name'], $mailformat);
        $mailformat = str_replace("%trip_user_name%", $data['trip_user_name'], $mailformat);


        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * driver_name,trip_user_name,to_email_list
     */

    public function tripRejectAlertEmail($data) {

        $emailData = Email_format::find(58)->toArray(); // 49 = Cancel Applied Remote Visit Attendance
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%driver_name%", $data['driver_name'], $mailformat);
        $mailformat = str_replace("%trip_user_name%", $data['trip_user_name'], $mailformat);
        $mailformat = str_replace("%reject_note%", $data['reject_note'], $mailformat);


        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * inward_title,inward_number,to_email_list
     */

    public function newInwardAlertEmail($data) {

        $emailData = Email_format::find(68)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%inward_title%", $data['inward_title'], $mailformat);
        $mailformat = str_replace("%inward_number%", $data['inward_number'], $mailformat);
       
        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * outward_title,outward_number,to_email_list
     */

    public function newOutwardAlertEmail($data) {

        $emailData = Email_format::find(69)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%outward_title%", $data['outward_title'], $mailformat);
        $mailformat = str_replace("%outward_number%", $data['outward_number'], $mailformat);
       
        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * outward_title,outward_number,to_email_list
     */

    public function registryResponseReminderEmail($data) {

        $emailData = Email_format::find(70)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%registry_number%", $data['registry_number'], $mailformat);
        $mailformat = str_replace("%expected_ans_date%", $data['expected_ans_date'], $mailformat);
       
        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
	
	/*
     * $data contains below index
     * anniversary_user_name,to_email_list
     */

    public function marraigeAnniversaryWishEmail($data) {

        $emailData = Email_format::find(40)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%anniversary_user_name%", $data['anniversary_user_name'], $mailformat);
        
        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * anniversary_user_name,to_email_list
     */

    public function marraigeAnniversaryAlertEmail($data) {

        $emailData = Email_format::find(39)->toArray();
        $subject = str_replace("%anniversary_user_name%", $data['anniversary_user_name'], $emailData['subject']);
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%anniversary_user_name%", $data['anniversary_user_name'], $mailformat);
        
        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * birthday_user_name,to_email_list
     */

    public function birthDayWishEmail($data) {

        $emailData = Email_format::find(38)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%birthday_user_name%", $data['birthday_user_name'], $mailformat);
        
        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * birthday_user_name,to_email_list
     */

    public function birthDayAlertEmail($data) {

        $emailData = Email_format::find(37)->toArray();
        $subject = str_replace("%birthday_user_name%", $data['birthday_user_name'], $emailData['subject']);
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%birthday_user_name%", $data['birthday_user_name'], $mailformat);
        
        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * anniversary_user_name,to_email_list
     */

    public function workAnniversaryWishEmail($data) {

        $emailData = Email_format::find(42)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%anniversary_user_name%", $data['anniversary_user_name'], $mailformat);
        
        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * anniversary_user_name,to_email_list
     */

    public function workAnniversaryAlertEmail($data) {

        $emailData = Email_format::find(41)->toArray();
        $subject = str_replace("%anniversary_user_name%", $data['anniversary_user_name'], $emailData['subject']);
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%anniversary_user_name%", $data['anniversary_user_name'], $mailformat);
        
        Mail::to($data['to_email_list'])->queue(new Mails($subject, $mailformat));
    }
	
	 /*
     * $data contains below index
     * 
     */
    public function approveRejectOnlinePaymentEmail($data) {

        $amount = !empty($data['amount']) ? $data['amount'] : 0;

        $emailData = Email_format::find(77)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%username%", $data['username'], $mailformat);
        $mailformat = str_replace("%amount%", $amount, $mailformat);
        $mailformat = str_replace("%status%", $data['status'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * 
     */
    public function approveRejectVehicleMaintenancePaymentEmail($data) {

        $amount = !empty($data['amount']) ? $data['amount'] : 0;

        $emailData = Email_format::find(78)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        //$mailformat = str_replace("%user_name%", $user_data[0]->name, $mailformat);
        $mailformat = str_replace("%username%", $data['username'], $mailformat);
        $mailformat = str_replace("%amount%", $amount, $mailformat);
        $mailformat = str_replace("%status%", $data['status'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }
	
	  /*
     * $data contains below index
     * to_email,assistent_name,ins_number,ins_type,expiration_date
     */

    public function cron_renewdInsNotify($data) {   //insurance

        $emailData = Email_format::find(84)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%ins_number%", $data['ins_number'], $mailformat);
        $mailformat = str_replace("%ins_type%", $data['ins_type'], $mailformat);
        $mailformat = str_replace("%expiration_date%", $data['expiration_date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }
	
	/*
     * $data contains below index
     * to_email,assistent_name,ins_number,ins_type,expiration_date
     */

    public function cron_expiredInsNotify($data) {

        $emailData = Email_format::find(85)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%ins_number%", $data['ins_number'], $mailformat);
        $mailformat = str_replace("%ins_type%", $data['ins_type'], $mailformat);
        $mailformat = str_replace("%expiration_date%", $data['expiration_date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }
	
	public function hrpendingjobLeaveEmail($data) {

        $emailData  = Email_format::find(79)->toArray();
        $subject    = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/all_leave'), $mailformat);
        $mailformat = str_replace("%username%","Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function adminpendingjobLeaveEmail($data) {

        $emailData  = Email_format::find(79)->toArray();
        $subject    = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/all_leave'), $mailformat);
        $mailformat = str_replace("%username%","Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function relievingpendingjobLeaveEmail($data) {

        $emailData  = Email_format::find(80)->toArray();
        $subject    = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/relieving_request'), $mailformat);
        $mailformat = str_replace("%username%","Folk", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function cashPaymentPendingJobEmail($data) {

        $emailData  = Email_format::find(81)->toArray();
        $subject    = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/relieving_request'), $mailformat);
        $mailformat = str_replace("%username%","Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function bankPaymentPendingJobEmail($data) {

        $emailData  = Email_format::find(82)->toArray();
        $subject    = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/relieving_request'), $mailformat);
        $mailformat = str_replace("%username%","Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function onlinePaymentPendingJobEmail($data) {

        $emailData  = Email_format::find(83)->toArray();
        $subject    = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/relieving_request'), $mailformat);
        $mailformat = str_replace("%username%","Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
      $data contains below index
      to_email,reject_note,unique_no
     */
    /*public function reject_all_travel_option($data) {

        $emailData = Email_format::find(88)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%unique_no%", $data['unique_no'], $mailformat);
        $mailformat = str_replace("%reject_note%", $data['reject_note'], $mailformat);
       
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }*/
    
    public function empExpensePendingJobEmail($data) {

        $emailData  = Email_format::find(86)->toArray();
        $subject    = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/employee_expense_list'), $mailformat);
        $mailformat = str_replace("%username%","Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function driverExpensePendingJobEmail($data) {

        $emailData  = Email_format::find(87)->toArray();
        $subject    = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/all_expense'), $mailformat);
        $mailformat = str_replace("%username%","Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
      $data contains below index
      to_email,reject_note,unique_no
     */
    public function reject_travel_expense($data) {

        $emailData = Email_format::find(90)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%request_no%", $data['request_no'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%rejected_user_name%", $data['rejected_user_name'], $mailformat);
        $mailformat = str_replace("%reject_note%", $data['reject_note'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }
	
	public function process_document($inward_id, $file_name) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://139.59.8.252:8081/api/pdf_content/insert_pdf/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('file_path' => $file_name, 'inward_id' => $inward_id),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return;
    }

    


    public function setSuperUserId($user_id_arr,$position){
        

        $superUser_id = User::where('role',config('constants.SuperUser'))
            ->get();
       
               $superUser = $superUser_id[0]->id;
                    if (in_array($superUser,$user_id_arr)) {
                        unset($user_id_arr[array_search($superUser,$user_id_arr)]);    
                    }
                array_splice($user_id_arr,$position,0,$superUser);

                return $user_id_arr;


  
    }
    
    public function budgetSheetPendingJobEmail($data) {

        $emailData = Email_format::find(92)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/budget_sheet'), $mailformat);
        $mailformat = str_replace("%username%", "Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function preSignLetterHeadPendingJobEmail($data) {

        $emailData = Email_format::find(93)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/approved_letter_head_report'), $mailformat);
        $mailformat = str_replace("%username%", "Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    public function letterHeadPendingJobEmail($data) {

        $emailData = Email_format::find(94)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%table%", $data['body'], $mailformat);
        $mailformat = str_replace("%link%", url('/approved_letter_head_report'), $mailformat);
        $mailformat = str_replace("%username%", "Team Member", $mailformat);
        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }
	
	public function intimateResignationEmailHR($data) {
        $emailData  = Email_format::find(95)->toArray();
        $subject    = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%sr_name%", $data['sr_name'], $mailformat);
        $mailformat = str_replace("%resignee_name%", $data['resignee_name'], $mailformat);
        $mailformat = str_replace("%handover_name%", $data['handover_name'], $mailformat);
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
      $data contains below index
      asset_name , return date
     */
    public function intimateAssetMaintenanceDate($data) {

        $emailData = Email_format::find(96)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%asset_name%", $data['asset_name'], $mailformat);
        $mailformat = str_replace("%return_date%", $data['return_date'], $mailformat);
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    
     /*
      $data contains below index
      desgination , user_name 
     */

     //============09/04/2020
    public function interSelectConfirm($data) {

        $emailData = Email_format::find(97)->toArray();
        $subject = str_replace("%job_title%", $data['desgination'], $emailData['subject']);
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%desgination%", $data['desgination'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);

        
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

     /*
      $data contains below index
       desgination , user_name 
     */
    public function interRejectConfirm($data) {

        $emailData = Email_format::find(98)->toArray();
        $subject = str_replace("%job_title%", $data['desgination'], $emailData['subject']);
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%desgination%", $data['desgination'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);

        
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

     /*
      $data contains below index
      desgination , user_name 
     */
    public function interHoldConfirm($data) {

        $emailData = Email_format::find(99)->toArray();
        $subject = str_replace("%job_title%", $data['desgination'], $emailData['subject']);
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%desgination%", $data['desgination'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);

       
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

/*
      $data contains below index
     */
    public function interviewersNotifyMail($data, $interview_date , $interviewerEmail) {

            $emailData = Email_format::find(14)->toArray(); // 14 => Send mail to interviewer
            $subject = $emailData['subject'];
            $mailformat = $emailData['emailformat'];

            $mailformat = str_replace("%message%", "Please take next round interview of below candidate.", $mailformat);
            $mailformat = str_replace("%team%", "HR Team", $mailformat);
            $mailformat = str_replace("%candidate_name%", $data->candidate, $mailformat);
            $mailformat = str_replace("%candidate_id%", $data->interviewee_id, $mailformat);
            $mailformat = str_replace("%interview_date%", $interview_date, $mailformat);
            $mailformat = str_replace("%desgination%", $data->title, $mailformat);
            $mailformat = str_replace("%job_id%", $data->job_id, $mailformat);
            $mailformat = str_replace("%job_role%", $data->role, $mailformat);
            $mailformat = str_replace("%job_location%", $data->location, $mailformat);
            $mailformat = str_replace("%job_description%", $data->description, $mailformat);
        

        Mail::to($interviewerEmail)->queue(new Mails($subject, $mailformat));
    }

    /*
      $data contains below index
      candidate_name , team , interview_date, candidate_id
     */
    public function interviewMarksUpdate($data, $interview_date) {

            $emailData = Email_format::find(59)->toArray(); // 59 => Send mail to hr
            
            $subject = $emailData['subject'];
            $mailformat = $emailData['emailformat'];
            $mailformat = str_replace("%message%", "Please check interview round is completed and marks added.", $mailformat);
            $mailformat = str_replace("%team%", "Interviewer", $mailformat);
            $mailformat = str_replace("%candidate_name%", $data->name, $mailformat);
            $mailformat = str_replace("%interview_date%", $interview_date, $mailformat);
            $mailformat = str_replace("%candidate_id%", $data->interviewee_id, $mailformat);

            $hrEmail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();




            Mail::to($hrEmail)->queue(new Mails($subject, $mailformat));
        
    }
    

    public function hrMailAfterApproval($data) {

        $emailData = Email_format::find(13)->toArray(); // 13 => Send mail to HR after select/reject

            $subject = $emailData['subject'];
            $mailformat = $emailData['emailformat'];
            $mailformat = str_replace("%emp_status%", $data['emp_status'], $mailformat);
            $mailformat = str_replace("%name%", $data['name'], $mailformat);
            $mailformat = str_replace("%candidate_id%", $data['candidate_id'], $mailformat);
            $mailformat = str_replace("%desgination%", $data['desgination'], $mailformat);
            $mailformat = str_replace("%job_id%", $data['job_id'], $mailformat);
            $mailformat = str_replace("%job_role%", $data['job_role'], $mailformat);
            $mailformat = str_replace("%job_location%", $data['job_location'], $mailformat);
            $mailformat = str_replace("%job_description%", $data['job_description'], $mailformat);

            $hrEmail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();



            Mail::to($hrEmail)->queue(new Mails($subject, $mailformat));

    }


     /*
     * $data contains below index
     * to_email,ins_number,ins_type,expiration_date
     */

    public function cron_expiredEmployeeInsNotify($data) {   //13/04/2020

        $emailData = Email_format::find(100)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%ins_number%", $data['ins_number'], $mailformat);
        $mailformat = str_replace("%ins_type%", $data['ins_type'], $mailformat);
        $mailformat = str_replace("%expiration_date%", $data['expiration_date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }


    /*
     * $data contains below index
     * to_email,ins_number,ins_type,expiration_date
     */

    public function cron_renewdEmployeeInsNotify($data) {   //13/04/2020

        $emailData = Email_format::find(101)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%ins_number%", $data['ins_number'], $mailformat);
        $mailformat = str_replace("%ins_type%", $data['ins_type'], $mailformat);
        $mailformat = str_replace("%expiration_date%", $data['expiration_date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }


     /*
     * $data contains below index
     * to_email,return_date,assignee_name
     */

    public function cron_remindSubmitHardCopyNotify($data) {   //24/04/2020

        $emailData = Email_format::find(102)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%assignee_name%", $data['assignee_name'], $mailformat);
        $mailformat = str_replace("%return_date%", $data['return_date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }



     /*
     * $data contains below index
     * to_email,return_date,assignee_name
     */

    public function cron_submitHardCopyNotify($data) {   //24/04/2020

        $emailData = Email_format::find(103)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%assignee_name%", $data['assignee_name'], $mailformat);
        $mailformat = str_replace("%return_date%", $data['return_date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

        /*
      $data contains below index
      to_email,client_name,tender_sr_no,tender_id,dept_name,portal_name,tender_no,state_name,
      name_of_work
     */

    public function tender_selected($data) {

        $emailData = Email_format::find(106)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%client_name%", $data['client_name'], $mailformat);
        $mailformat = str_replace("%tender_sr_no%", $data['tender_sr_no'], $mailformat);
        $mailformat = str_replace("%tender_id%", $data['tender_id'], $mailformat);
        $mailformat = str_replace("%dept_name%", $data['dept_name'], $mailformat);
        $mailformat = str_replace("%portal_name%", $data['portal_name'], $mailformat);
        $mailformat = str_replace("%tender_no%", $data['tender_no'], $mailformat);
        $mailformat = str_replace("%state_name%", $data['state_name'], $mailformat);
        $mailformat = str_replace("%name_of_work%", $data['name_of_work'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }    

    /*
      $data contains below index
      to_email,client_name,tender_sr_no,tender_id,dept_name,portal_name,tender_no,state_name,
      name_of_work
     */

    public function tender_assign($data) {

        $emailData = Email_format::find(104)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%client_name%", $data['client_name'], $mailformat);
        $mailformat = str_replace("%tender_sr_no%", $data['tender_sr_no'], $mailformat);
        $mailformat = str_replace("%tender_id%", $data['tender_id'], $mailformat);
        $mailformat = str_replace("%dept_name%", $data['dept_name'], $mailformat);
        $mailformat = str_replace("%portal_name%", $data['portal_name'], $mailformat);
        $mailformat = str_replace("%tender_no%", $data['tender_no'], $mailformat);
        $mailformat = str_replace("%state_name%", $data['state_name'], $mailformat);
        $mailformat = str_replace("%name_of_work%", $data['name_of_work'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
      $data contains below index
      to_email,client_name,tender_sr_no,tender_id,dept_name,portal_name,tender_no,state_name,
      name_of_work
     */

    public function tender_update($data) {

        $emailData = Email_format::find(105)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%client_name%", $data['client_name'], $mailformat);
        $mailformat = str_replace("%tender_sr_no%", $data['tender_sr_no'], $mailformat);
        $mailformat = str_replace("%tender_id%", $data['tender_id'], $mailformat);
        $mailformat = str_replace("%dept_name%", $data['dept_name'], $mailformat);
        $mailformat = str_replace("%portal_name%", $data['portal_name'], $mailformat);
        $mailformat = str_replace("%tender_no%", $data['tender_no'], $mailformat);
        $mailformat = str_replace("%state_name%", $data['state_name'], $mailformat);
        $mailformat = str_replace("%name_of_work%", $data['name_of_work'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    //09/05/2020
     /*
     * $data contains below index
     * registry_no, user_name, cc_email_list, email_list
     */

    public function acceptDocumentRegistry($data) {

        $emailData = Email_format::find(107)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%registry_no%", $data['registry_no'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);


        Mail::to($data['email_list'])->cc($data['cc_email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * registry_no, user_name, cc_email_list, email_list
     */
    public function rejectDocumentRegistry($data) {

        $emailData = Email_format::find(108)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%registry_no%", $data['registry_no'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);


        Mail::to($data['email_list'])->cc($data['cc_email_list'])->queue(new Mails($subject, $mailformat));
    }

     //13/05/2020
    /*
     * $data contains below index
     * registry_no, user_name, date, email_list
     */
    public function acceptRegistryDocumentPrimeUser($data) {

        $emailData = Email_format::find(110)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%registry_no%", $data['registry_no'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * registry_no, user_name, date, reason, email_list
     */
    public function rejectRegistryDocumentPrimeUser($data) {

        $emailData = Email_format::find(109)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%reason%", $data['reason'], $mailformat);
        $mailformat = str_replace("%registry_no%", $data['registry_no'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }


    /*
      $data contains below index
      to_email,client_name,tender_sr_no,tender_id,dept_name,portal_name,tender_no,state_name,
      name_of_work,opening_email_type,opening_date
     */
    public function opening_tender($data) {

        $emailData = Email_format::find(111)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%client_name%", $data['client_name'], $mailformat);
        $mailformat = str_replace("%tender_sr_no%", $data['tender_sr_no'], $mailformat);
        $mailformat = str_replace("%tender_id%", $data['tender_id'], $mailformat);
        $mailformat = str_replace("%dept_name%", $data['dept_name'], $mailformat);
        $mailformat = str_replace("%portal_name%", $data['portal_name'], $mailformat);
        $mailformat = str_replace("%tender_no%", $data['tender_no'], $mailformat);
        $mailformat = str_replace("%state_name%", $data['state_name'], $mailformat);
        $mailformat = str_replace("%name_of_work%", $data['name_of_work'], $mailformat);
        $mailformat = str_replace("%opening_email_type%", $data['opening_email_type'], $mailformat);
        $mailformat = str_replace("%opening_date%", $data['opening_date'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

     //18/05/2020
      /*
     * $data contains below index
     * registry_no, user_name, date, email_list
     */
    public function acceptDistrubutedWorkRequestSupportEmp($data) {

        $emailData = Email_format::find(112)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);
        $mailformat = str_replace("%registry_no%", $data['registry_no'], $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * registry_no, user_name, date, reason, email_list
     */
    public function rejectDistrubutedWorkRequestSupportEmp($data) {

        $emailData = Email_format::find(113)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);
        $mailformat = str_replace("%registry_no%", $data['registry_no'], $mailformat);
        $mailformat = str_replace("%reason%", $data['reason'], $mailformat); 

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }


     /*
     * $data contains below index
     * registry_no, user_name, date, reason, email_list
     */
    public function rejectWorkByPrimeUser($data) {

        $emailData = Email_format::find(114)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);
        $mailformat = str_replace("%reject_note%", $data['reject_note'], $mailformat);
        $mailformat = str_replace("%rejected_by%", $data['rejected_by'], $mailformat);
       
        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

     /*
     * $data contains below index
     * registry_no, user_name, date, reason, email_list
     */
    public function rejectFinalTaskPrimeUser($data) {   //Pending

        $emailData = Email_format::find(115)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%date%", $data['date'], $mailformat);
        $mailformat = str_replace("%registry_no%", $data['registry_no'], $mailformat);
        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%reject_note%", $data['reject_note'], $mailformat);
       
      
        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

    //----------- Cheque / RTGS CRON ----------------
    /*
     * $data contains below index
     * cheque_book_no
     */

    public function cronRemainBlankCheque($data) {
        $emailData = Email_format::find(116)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
      
        $mailformat = str_replace("%cheque_book_ref_no%", $data['cheque_book_no'], $mailformat);
         dd($mailformat);
        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    /*
     * $data contains below index
     * cheque_book_no
     */

    public function cronRemainSignedCheque($data) {
        $emailData = Email_format::find(117)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
      
        $mailformat = str_replace("%cheque_book_ref_no%", $data['cheque_book_no'], $mailformat);
        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }

    /*
     * $data contains below index
     * rtgs_book_no
     */

    public function cronRemainBlankRtgs($data) {
        $emailData = Email_format::find(118)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
      
        $mailformat = str_replace("%rtgs_book_ref_no%", $data['rtgs_book_no'], $mailformat);
        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }

     /*
     * $data contains below index
     * rtgs_book_no
     */

    public function cronRemainSignedrtgs($data) {
        $emailData = Email_format::find(119)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
      
        $mailformat = str_replace("%rtgs_book_ref_no%", $data['rtgs_book_no'], $mailformat);
       
        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

    // START - Company Document Management Mails

    /*
     * $data contains below index
     * document_title,email_list,request_by
     */

    public function newDocumentRequestEmail($data) {

        $emailData = Email_format::find(123)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%document_title%", $data['document_title'], $mailformat);
        $mailformat = str_replace("%request_by%", $data['request_by'], $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * document_title,email_list,approved_by, return_date
     */

    public function approvedDocumentRequestEmailByAdmin($data) {

        $emailData = Email_format::find(121)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%document_title%", $data['document_title'], $mailformat);
        $mailformat = str_replace("%to_user_name%", $data['to_user_name'], $mailformat);
        $mailformat = str_replace("%approved_by%", $data['approved_by'], $mailformat);
        $mailformat = str_replace("%return_date%", $data['return_date'], $mailformat);

        Mail::to($data['email_list'])->cc($data['cc_email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * document_title,email_list,approved_by
     */

    public function approvedDocumentRequestEmailByCustodian($data) {

        $emailData = Email_format::find(122)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%document_title%", $data['document_title'], $mailformat);
        $mailformat = str_replace("%to_user_name%", $data['to_user_name'], $mailformat);
        $mailformat = str_replace("%approved_by%", $data['approved_by'], $mailformat);

        $adminEmail = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();

        Mail::to($data['email_list'])->cc($adminEmail)->queue(new Mails($subject, $mailformat));
    }    

    /*
     * $data contains below index
     * document_title,email_list,reject_by,reason
     */

    public function rejectDocumentRequestEmail($data) {

        $emailData = Email_format::find(120)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%document_title%", $data['document_title'], $mailformat);
        $mailformat = str_replace("%to_user_name%", $data['to_user_name'], $mailformat);
        $mailformat = str_replace("%reject_by%", $data['reject_by'], $mailformat);
        $mailformat = str_replace("%reason%", $data['reason'], $mailformat);

        if($data['rejected_user'] == 'custodian'){

            $adminEmail = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();

            Mail::to($data['email_list'])->cc($adminEmail)->queue(new Mails($subject, $mailformat));

        }else{
            Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
        }

    }

    /*
     * $data contains below index
     * document_title,email_list,return_by
     */

    public function returnDocumentRequestEmail($data) {

        $emailData = Email_format::find(124)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%document_title%", $data['document_title'], $mailformat);
        $mailformat = str_replace("%return_by%", $data['return_by'], $mailformat);

        $adminEmail = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();

        Mail::to($data['email_list'])->cc($adminEmail)->queue(new Mails($subject, $mailformat));
    }

    // END - Company Document Management Mails

    
    //22/06/2020
    public function apiVendorEntryRejected($data) {
        $emailData = Email_format::find(125)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%vendor_name%", $data['vendor_name'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //23/06/2020
    public function apiCompanyEntryRejected($data) {
        $emailData = Email_format::find(126)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%company_name%", $data['company_name'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);
       //dd($mailformat ,$data['email_list'] );
        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }

     //23/06/2020
    public function apiClientEntryRejected($data) {
        $emailData = Email_format::find(127)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%client_name%", $data['client_name'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //23/06/2020
    public function apiProjectEntryRejected($data) {
        $emailData = Email_format::find(128)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%project_name%", $data['project_name'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
	
	//24/06/2020
    public function apiVendorBankEntryRejected($data) {
        $emailData = Email_format::find(129)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%vendor_bank_name%", $data['vendor_bank_name'], $mailformat);
        $mailformat = str_replace("%account_number%", $data['account_number'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //24/06/2020
    public function apiProjectSiteEntryRejected($data) {
        $emailData = Email_format::find(130)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%project_site_name%", $data['project_site_name'], $mailformat);
        $mailformat = str_replace("%project_site_address%", $data['project_site_address'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //24/06/2020
    public function apiBankEntryRejected($data) {
        $emailData = Email_format::find(131)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%bank_name%", $data['bank_name'], $mailformat);
        $mailformat = str_replace("%account_number%", $data['account_number'], $mailformat);
        $mailformat = str_replace("%branch%", $data['branch'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //24/06/2020
    public function apiBankChargeCategoryEntryRejected($data) {
        $emailData = Email_format::find(132)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //24/06/2020
    public function apiBankChargeSubCategoryEntryRejected($data) {
        $emailData = Email_format::find(133)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //25/06/2020
    public function apiPaymentCardEntryRejected($data) {
        $emailData = Email_format::find(134)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%card_number%", $data['card_number'], $mailformat);
        $mailformat = str_replace("%name_on_card%", $data['name_on_card'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
     //25/06/2020
    public function apiCompanyDocumentEntryRejected($data) {
        $emailData = Email_format::find(138)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //25/06/2020
    public function apiTenderCategoryEntryRejected($data) {
        $emailData = Email_format::find(139)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
     //25/06/2020
    public function apiTenderPatternEntryRejected($data) {
        $emailData = Email_format::find(140)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //25/06/2020
    public function apiTenderPhysicalEntryRejected($data) {
        $emailData = Email_format::find(141)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //26/06/2020
    public function apiRegistryCategoryEntryRejected($data) {
        $emailData = Email_format::find(142)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //26/06/2020
    public function apiRegistrySubCategoryEntryRejected($data) {
        $emailData = Email_format::find(143)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //26/06/2020
    public function apiDeliveryModeEntryRejected($data) {
        $emailData = Email_format::find(144)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //26/06/2020
    public function apiSenderEntryRejected($data) {
        $emailData = Email_format::find(145)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
	//-------------------------
	public function policy_auto_approve($data) {
        $emailData = Email_format::find(91)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%policy_number%", $data['policy_number'], $mailformat);

        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));

    }

    public function empAttendancePendingEmail($data){
        $emailData = Email_format::find(89)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%username%", $data['username'], $mailformat);
        $mailformat = str_replace("%table%", $data['body'], $mailformat);

        Mail::to($data['email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * 
     */
    public function voucher_assigned($data)
    {   
        $emailData = Email_format::find(135)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * name
     */
    public function voucher_accepted($data)
    {
        $emailData = Email_format::find(136)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%name%", $data['name'], $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * name
     */
    public function voucher_rejected($data)
    {
        $emailData = Email_format::find(137)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        
        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

    //08/07/2020
    public function complianceReminder($data) {
        $emailData = Email_format::find(146)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%compliance_category%", $data['compliance_category'], $mailformat);
        $mailformat = str_replace("%company%", $data['company'], $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }
    //14/07/2020
    public function repeatComplianceRemind($data) {
        $emailData = Email_format::find(147)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        
        $mailformat = str_replace("%compliance_category%", $data['compliance_category'], $mailformat);
        $mailformat = str_replace("%company%", $data['company'], $mailformat);
        $mailformat = str_replace("%compliance_name%", $data['compliance_name'], $mailformat);
        $mailformat = str_replace("%due_date%", $data['due_date'], $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));

    }

    //21/08/2020
    public function apiTdsSectionEntryRejected($data)
    {
        $emailData = Email_format::find(148)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%super_user%", $this->super_admin->name, $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

    //21/08/2020
    public function budgetSheetReleaseAmountRequestEmail($data)
    {
        $emailData = Email_format::find(149)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%request_by%", $data['request_by'], $mailformat);
        $mailformat = str_replace("%budget_sheet_no%", $data['budget_sheet_no'], $mailformat);
        // echo "<pre>";
        // print_r($mailformat);exit;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    //21/08/2020
    public function budgetSheetReleaseAmountChangeEmail($data)
    {
        $emailData = Email_format::find(150)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%old%", $data['old_release_hold_amount'], $mailformat);
        $mailformat = str_replace("%new%", $data['completed_amount'], $mailformat);
        $mailformat = str_replace("%budget_sheet_no%", $data['budget_sheet_no'], $mailformat);
        // echo "<pre>";
        // print_r($mailformat);exit;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    //31/08/2020
    public function budgetSheetReleaseAmountRejectEmail($data)
    {
        $emailData = Email_format::find(151)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        $mailformat = str_replace("%new%", $data['completed_amount'], $mailformat);
        $mailformat = str_replace("%action%", $data['action'], $mailformat);
        $mailformat = str_replace("%budget_sheet_no%", $data['budget_sheet_no'], $mailformat);
        // echo "<pre>";
        // print_r($mailformat);exit;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }
    //03/09/2020 nish
    public function attendanceApprovalRejectEmail($data)
    {
        $emailData = Email_format::find(152)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];

        $mailformat = str_replace("%user_name%", $data['user_name'], $mailformat);
        $mailformat = str_replace("%date%", $data['date'], $mailformat);
        $mailformat = str_replace("%in%", $data['in'], $mailformat);
        $mailformat = str_replace("%out%", $data['out'], $mailformat);
        $mailformat = str_replace("%reject_note%", $data['reject_note'], $mailformat);
        $mailformat = str_replace("%super_user%", $data['super_user'], $mailformat);
        // echo "<pre>";
        // print_r($mailformat);exit;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    //10/09/2020
    public function meetingMOMNotify($data)
    {

        $emailData = Email_format::find(153)->toArray();
      
        $mailformat = $emailData['emailformat'];
        $mail_subject = $data['meeting_subject'].'('.$data['meeting_code'].')';
        $subject = str_replace("%subject%", $mail_subject, $emailData['subject']);
        $to_email = ['info@raudratech.com'];
       
        Mail::to($to_email)->cc($data['cc_mails'])->queue(new Mails($subject, $mailformat, $data['attach_file']));
    }

    // Send mail everday punch in out data to superadmin and admin
    // Date : 12/09/2020
    // By : Jaldip
    public function sendDailyPunchInOut($data) {

        $emailData = Email_format::find(154)->toArray();
        $mailformat = $emailData['emailformat'];

        $subject = $data['mail_subject'];
        $mailformat = str_replace("%mail_format%", $data['mail_format'], $mailformat);
       
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));

    }

    /*
     * $data contains below index
     * document_title,email_list,request_by
     */

    public function softcopyRequestEmail($data) {

        $emailData = Email_format::find(155)->toArray();
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%request_by%", $data['request_by'], $mailformat);
        $mailformat = str_replace("%document_title%", $data['document_title'], $mailformat);
        $mailformat = str_replace("%request_type%", $data['request_type'], $mailformat);

        Mail::to($data['email_list'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,table
     */
    public function sendTodayActivityLog($data) {
        $emailData = Email_format::find(156)->toArray();
        $mailformat = $emailData['emailformat'];

        $subject = $data['mail_subject'];
        $mailformat = str_replace("%table%", $data['mail_format'], $mailformat);
        // echo "<pre>";print_r($mailformat);die;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * to_email,table
     */
    public function expensesCMDEmail($data) {
        $emailData = Email_format::find(157)->toArray();
        $mailformat = $emailData['emailformat'];
        $subject = $emailData['subject'];
        $mailformat = str_replace("%from_name%", $data['from_name'], $mailformat);
        $mailformat = str_replace("%amount%", $data['amount'], $mailformat);
        $mailformat = str_replace("%title%", $data['title'], $mailformat);
        $mailformat = str_replace("%bill_number%", $data['bill_number'], $mailformat);
        $mailformat = str_replace("%merchant_name%", $data['merchant_name'], $mailformat);
        $mailformat = str_replace("%expense_date%", $data['expense_date'], $mailformat);
        // echo "<pre>";print_r($mailformat);die;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }
    

    public function get_vehicle_asset_driver()
    {
        $driver_list = User::where('status', "=" ,'Enabled')->where('role', "=", '8')->pluck('id')->toArray();

        $access_vehicle_id = Asset::where('asset_type', "=", 'Vehicle Asset')->pluck('id')->toArray();
        
        $access_user_id = AssetAccess::whereIn('asset_id', $access_vehicle_id)->pluck('asset_access_user_id')->toArray();
        
        $driver_list = array_unique(array_merge($driver_list, $access_user_id));
        
        return $driver_list;
    }

    /*
     * $data contains below index
     * to_email,table_data
     */
    public function dailyOnlinePaymentEmailReport($data) {
        $emailData = Email_format::find(158)->toArray();
        $mailformat = $emailData['emailformat'];
        $subject = $emailData['subject'];
        // 
        $mailformat = str_replace("%table_data%", $data['table_data'], $mailformat);
        // echo "<pre>";print_r($mailformat);die;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    /*
     * $data contains below index
     * to_email,table_data
     */
    public function dailyBankPaymentEmailReport($data) {
        $emailData = Email_format::find(159)->toArray();
        $mailformat = $emailData['emailformat'];
        $subject = $emailData['subject'];
        // 
        $mailformat = str_replace("%table_data%", $data['table_data'], $mailformat);
        // echo "<pre>";print_r($mailformat);die;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }
    
    /*
     * $data contains below index
     * to_email,table_data
     */
    public function dailyCashPaymentEmailReport($data) {
        $emailData = Email_format::find(160)->toArray();
        $mailformat = $emailData['emailformat'];
        $subject = $emailData['subject'];
        // 
        $mailformat = str_replace("%table_data%", $data['table_data'], $mailformat);
        // echo "<pre>";print_r($mailformat);die;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    //dailyEmployeeLeaveReport
    /*
     * $data contains below index
     * to_email,table_data
     */
    public function dailyEmployeeLeaveReport($data) {
        $emailData = Email_format::find(161)->toArray();
        $mailformat = $emailData['emailformat'];
        $subject = $emailData['subject'];
        // 
        $mailformat = str_replace("%table_data%", $data['table_data'], $mailformat);
        // echo "<pre>";print_r($mailformat);die;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));
    }

    // get_trip_management
    public function get_trip_management()
    {
        $driver_list = User::where('status', "=" ,'Enabled')->where('role', "=", '8')->pluck('id')->toArray();

        $access_vehicle_id = Asset::where('asset_type', "=", 'Vehicle Asset')->pluck('id')->toArray();
        
        $access_user_id = AssetAccess::whereIn('asset_id', $access_vehicle_id)->pluck('asset_access_user_id')->toArray();
        
        $driver_list = array_unique(array_merge($driver_list, $access_user_id));
        
        return $driver_list;
    }

    /*
     * $data contains below index
     * to_email,table_data
     */
    public function sendComfirmLetter($data) {
        $emailData = Email_format::find(162)->toArray();
        // $mailformat = $emailData['emailformat'];
        // $subject = $emailData['subject'];
        // may be nedded use this below  line,
        // $mailformat = str_replace("%table_data%", $data['table_data'], $mailformat);
        // echo "<pre>";print_r($mailformat);die;
        // Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));

        $mailformat = $emailData['emailformat'];
        $subject = $emailData['subject'];
        $mailformat = str_replace("%name%", $data['to_name'], $mailformat);
        $mailformat = str_replace("%companyname%", $data['companyname'], $mailformat);
        // echo "<pre>";print_r($mailformat);die;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat));

    }
    
    /*
     * $data contains below index
     * to_email
     */
    public function sendFinishProcessReport($data) {
        $emailData = Email_format::find(163)->toArray();
        $mailformat = $emailData['emailformat'];
        $subject = $emailData['subject'];
        $mailformat = str_replace("%name%", $data['name'], $mailformat);
        // echo "<pre>";print_r($mailformat);die;
        Mail::to($data['to_email'])->queue(new Mails($subject, $mailformat), function ($message) use ($data) {
            foreach ($data['attachments_arr'] as $filePath => $fileParameters) {
                $message->attach($filePath, $fileParameters);
            }
        });
    }

}
