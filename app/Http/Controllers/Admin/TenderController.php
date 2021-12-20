<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tender;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use DB;
use Illuminate\Support\Facades\Auth;
use App\AssetImage;
use App\Lib\Permissions;
use App\Tender_technical_eligibility;
use App\Tender_financial_eligibility;
use App\Tender_client_detail;
use App\Tender_pre_bid_document;
use App\Tender_other_communication;
use App\Tender_condition_contract;
use App\Tender_participated_bidder;
use App\Department;
use App\TenderCategory;
use App\User;
use App\TenderPattern;
use App\Tender_physical_submission;
use App\TenderCorrigendum;
use App\Tender_boq_bidder;
use App\Tender_opening_status_technical;
use App\Tender_opening_status_financial;
use App\Tender_participated_bidder_log;
use App\Companies;
use App\TenderPermission;
use App\BankPaymentApproval;
use App\TenderPaymentRequest;
use Illuminate\Support\Facades\Storage;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use Rap2hpoutre\FastExcel\FastExcel;
use Validator;
use App\Lib\UserActionLogs;


class TenderController extends Controller
{
    public $data;
    public $common_task;
    private $notification_task;
    public $user_action_logs;
    private $module_id = 57;
    public function __construct() {
        $this->data['module_title']="Tender";
        $this->data['module_link']='admin.tender';
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();

        $add_tender = TenderPermission::where('type',"add_tender")->first();
        if($add_tender){
            $this->data['add_tender_permission'] = $add_tender->user_id;
        }else{
            $this->data['add_tender_permission'] = "";
        }

        $edit_tender = TenderPermission::where('type',"edit_tender")->first();
        if($edit_tender){
            $this->data['edit_tender_permission'] = $edit_tender->user_id;
        }else{
            $this->data['edit_tender_permission'] = "";
        }

    }

    public function index(){
        $this->data['page_title']='Tender';
        $this->data['add_permission']= Permissions::checkPermission($this->module_id, 3);
        if(Auth::user()->id==$this->data['add_tender_permission'] || Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.Admin')){
            // $where_raw="";
            return view('admin.tender.index', $this->data);
        }else{
            return redirect()->route('admin.dashboard')->with('error','Access denied.');
        }

    }

    public function add_tender(){
    	$this->data['page_title']='Add Tender';
        $this->data['department'] = Department::orderBy('dept_name')->pluck('dept_name','id');
        $this->data['tendercategory'] = TenderCategory::whereStatus('Enabled')->orderBy('tender_category')->pluck('tender_category','id');
        $this->data['user'] = User::whereStatus('Enabled')->pluck('name','id');
        $this->data['tenderpattern'] = TenderPattern::whereStatus('Enabled')->orderBy('tender_pattern_name')->pluck('tender_pattern_name','id');
        $this->data['companies'] = Companies::whereStatus('Enabled')->orderBy('company_name')->pluck('company_name', 'id');
        // dd($this->data);
        return view('admin.tender.add_tender', $this->data);
    }

    public function dateTimeConvert($data){
        if($data){
            $myTime = strtotime($data);
            return date("Y-m-d H:i:s", $myTime);
        }else{
            return Null;
        }

		// return $data;
    }

    public function save_tender(Request $request){

        $data = $request->all();

        $tender_arr = [];
        $authority_arr = [];
        $client_arr = [];
        // dd($data);
        foreach ($data['department_id'] as $key => $value) {

            $tender_arr['tender_sr_no'] = $this->get_tender_srno();
            $tender_arr['department_id'] = $value;
            $tender_arr['company_id'] = $data['company_id'][$key];
            $tender_arr['tender_id_per_portal'] = $data['tender_id_per_portal'][$key];
            $tender_arr['portal_name'] = $data['portal_name'][$key];
            $tender_arr['tender_no'] = $data['tender_no'][$key];
            $tender_arr['name_of_work'] = $data['name_of_work'][$key];
            $tender_arr['state_name_work_execute'] = $data['state_name_work_execute'][$key];
            $tender_arr['estimate_cost'] = $data['estimate_cost'][$key];
            $tender_arr['joint_venture'] = $data['joint_venture'][$key];
            $tender_arr['joint_venture_count'] = ($data['joint_venture_count'][$key])? $data['joint_venture_count'][$key] : Null;
            $tender_arr['quote_type'] = $data['quote_type'][$key];
            $tender_arr['tender_pattern'] = $data['tender_pattern'][$key];
            $tender_arr['other_quote_type'] = ($data['other_quote_type'][$key])? $data['other_quote_type'][$key] : "";
            $tender_arr['tender_category_id'] = $data['tender_category_id'][$key];
            $tender_arr['last_date_time_download'] = $this->dateTimeConvert($data['last_date_time_download'][$key]);
            $tender_arr['last_date_time_online_submit'] = $this->dateTimeConvert($data['last_date_time_online_submit'][$key]);
            $tender_arr['last_date_time_physical_submit'] = $this->dateTimeConvert($data['last_date_time_physical_submit'][$key]);
            $tender_arr['created_ip'] = $request->ip();
            $tender_arr['updated_ip'] = $request->ip();
            $tender_arr['updated_by'] = Auth::user()->id;

            /*if(is_array($data['assign_tender'][$key])){
                $new_arr = implode(',', $data['assign_tender'][$key]);
            }else{
                $new_arr = "";
            }*/
            // $tender_arr['assign_tender'] = $new_arr;

            $tender_id = DB::table('tender')->insertGetId($tender_arr);
            $client_arr['tender_id'] = $tender_id;
            $client_arr['client_name'] = $data['client_name'][$key];
            $client_arr['client_address'] = $data['client_address'][$key];
            $client_arr['client_email'] = $data['client_email'][$key];
            $client_arr['client_landline_no'] = $data['client_landline_no'][$key];
            $client_arr['client_mobile_no'] = $data['client_mobile_no'][$key];
            $client_arr['client_fax_no'] = $data['client_fax_no'][$key];
            $client_arr['created_ip'] = $request->ip();
            $client_arr['updated_ip'] = $request->ip();
            DB::table('tender_client_detail')->insert($client_arr);

            foreach ($data['authority_name'][$key] as $key_auth => $value_auth) {
                        $authority_arr['tender_id'] = $tender_id;
                        $authority_arr['authority_name'] = $value_auth;
                        $authority_arr['authority_designation'] = $data['authority_designation'][$key][$key_auth];
                        $authority_arr['authority_email'] = $data['authority_email'][$key][$key_auth];
                        $authority_arr['authority_mobile_no'] = $data['authority_mobile_no'][$key][$key_auth];
                        $authority_arr['created_ip'] = $request->ip();
                        $authority_arr['updated_ip'] = $request->ip();

                        DB::table('tender_authority_contact_detail')->insert($authority_arr);
                        // dd($authority_arr);
            }

            // User Action Log
            $company_name = Companies::whereId($data['company_id'][$key])->value('company_name');
            $dept_name = Department::whereId($value)->value('dept_name');
            $tender_pattern_name = TenderPattern::whereId($data['tender_pattern'][$key])->value('tender_pattern_name');
            $tender_category = TenderCategory::whereId($data['tender_category_id'][$key])->value('tender_category');
            $add_string = "<br>Company Name: ".$company_name."<br>Department Name: ".$dept_name."<br>Tender Id Per Portal: ".$data['tender_id_per_portal'][$key]."<br>Portal Name: ".$data['portal_name'][$key]."<br>Tender No: ".$data['tender_no'][$key]."<br>Estimate Cost: ".$data['estimate_cost'][$key]."<br>Tender Pattern: ".$tender_pattern_name."<br>Tender Category: ".$tender_category;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_arr['tender_sr_no']." added".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            //email
            /*$t_user = explode(',', $tender_arr['assign_tender']);
            $users_email = user::whereIn('id', $t_user)->pluck('email')->toArray();
            $tender_data = $this->get_tender_details($tender_id);
            $mail_data = [];
            $mail_data['name'] = "";
            $mail_data['to_email'] = $users_email;
            $mail_data['client_name'] = $tender_data['client_name'];
            $mail_data['tender_sr_no'] = $tender_data['tender_sr_no'];
            $mail_data['tender_id'] = $tender_data['tender_id_per_portal'];
            $mail_data['dept_name'] = $tender_data['dept_name'];
            $mail_data['portal_name'] = $tender_data['portal_name'];
            $mail_data['tender_no'] = $tender_data['tender_no'];
            $mail_data['state_name'] = $tender_data['state_name_work_execute'];
            $mail_data['name_of_work'] = $tender_data['name_of_work'];
            $this->common_task->tender_assign($mail_data);

            $messages = "You are also selected as assigned employee for this tender. So please Login to your account for more details";
            $this->notification_task->tenderAssignNotify($t_user, 'Tender Assign', $messages);*/
        }



        return redirect()->route('admin.tender')->with('success', 'Data successfully inserted.');
    }

    public function get_tender_srno(){
    	$tender = Tender::latest('id')->first();
    	if($tender){
    		$new_tender = $tender['id'];
    		$tender = 100000 + $new_tender + 1;
    	}else{
    		$tender = 100001;
    	}
    	return $tender;
    }

    public function get_tender_list(){
        $datatable_fields = array('tender.id','tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work','tender.state_name_work_execute','tender.estimate_cost','tender.joint_venture','tender.joint_venture_count','tender.quote_type','tender.other_quote_type','tender_pattern.tender_pattern_name','tender_category.tender_category','tender.last_date_time_download','tender.last_date_time_online_submit','tender.last_date_time_physical_submit');
        $request = Input::all();
        $conditions_array = ['tender_status' => 'Pending'];

        $getfiled =array('tender.id','tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work','tender.state_name_work_execute','tender.estimate_cost','tender.joint_venture','tender.joint_venture_count','tender.quote_type','tender.other_quote_type','tender_pattern.tender_pattern_name','tender_category.tender_category','tender.last_date_time_download','tender.last_date_time_online_submit','tender.last_date_time_physical_submit');

        $table = "tender";
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'department';
        $join_str[0]['join_table_id'] = 'department.id';
        $join_str[0]['from_table_id'] = 'tender.department_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'tender_category';
        $join_str[1]['join_table_id'] = 'tender_category.id';
        $join_str[1]['from_table_id'] = 'tender.tender_category_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'tender_pattern';
        $join_str[2]['join_table_id'] = 'tender_pattern.id';
        $join_str[2]['from_table_id'] = 'tender.tender_pattern';

/*        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'company';
        $join_str[3]['join_table_id'] = 'company.id';
        $join_str[3]['from_table_id'] = 'tender.company_id';*/


        echo Tender::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function delete_tender($id) {
        if (Tender::where('id', $id)->delete()) {
        	DB::table('tender_authority_contact_detail')->where('tender_id',$id)->delete();
            return redirect()->route('admin.tender')->with('success', 'Delete successfully.');
        }
        return redirect()->route('admin.tender')->with('error', 'Error during operation. Try again!');
    }

    public function edit_tender($id){

    	$tender = Tender::select('tender.*','tender_client_detail.*')->Leftjoin('tender_client_detail', 'tender.id', '=', 'tender_client_detail.tender_id')->where('tender.id',$id)->first();

    	$this->data['page_title']='Edit Tender';
    	$this->data['tender'] = $tender;
        $this->data['tender_id'] = $id;
    	$this->data['department'] = Department::orderBy('dept_name')->pluck('dept_name','id');
        $this->data['tendercategory'] = TenderCategory::whereStatus('Enabled')->orderBy('tender_category')->pluck('tender_category','id');
        $this->data['user'] = User::whereStatus('Enabled')->orderBy('name')->pluck('name','id');
        $this->data['tenderpattern'] = TenderPattern::whereStatus('Enabled')->orderBy('tender_pattern_name')->pluck('tender_pattern_name','id');
        $this->data['tender_authority_contact_detail'] = DB::table('tender_authority_contact_detail')->whereTenderId($id)->get()->toArray();
        $this->data['tender_authority_contact_detail_count'] = count($this->data['tender_authority_contact_detail']);
        $this->data['companies'] = Companies::whereStatus('Enabled')->orderBy('company_name')
        ->pluck('company_name', 'id');
        // dd($this->data);
        return view('admin.tender.edit_tender', $this->data);
    }

    public function get_tender_details($id){
        $tender_data = Tender::select('tender.id','tender.tender_sr_no','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work','tender.state_name_work_execute','tender_client_detail.client_name','department.dept_name')->Leftjoin('tender_client_detail', 'tender.id', '=', 'tender_client_detail.tender_id')->Leftjoin('department', 'tender.department_id', '=', 'department.id')->where('tender.id',$id)->first();
        return $tender_data;
    }

    public function update_tender(Request $request){

    	/*if(is_array($request->get('assign_tender'))){
    		$new_arr = implode(',', $request->get('assign_tender'));
		}else{
			$new_arr = "";
		}*/

        $tender_arr = [
    		'department_id' => $request->get('department_id'),
            'company_id' => $request->get('company_id'),
    		'tender_id_per_portal' => $request->get('tender_id_per_portal'),
    		'portal_name' => $request->get('portal_name'),
    		'tender_no' => $request->get('tender_no'),
    		'name_of_work' => $request->get('name_of_work'),
    		'state_name_work_execute' => $request->get('state_name_work_execute'),
    		'estimate_cost' => $request->get('estimate_cost'),
    		'joint_venture' => $request->get('joint_venture'),
    		'joint_venture_count' => ($request->get('joint_venture_count'))? $request->get('joint_venture_count') : Null,
    		'quote_type' => $request->get('quote_type'),
            'tender_pattern' => $request->get('tender_pattern'),
    		'other_quote_type' => ($request->get('other_quote_type'))? $request->get('other_quote_type') : Null,
    		'tender_category_id' => $request->get('tender_category_id'),
    		'last_date_time_download' => $this->dateTimeConvert($request->get('last_date_time_download')),
    		'last_date_time_online_submit' => $this->dateTimeConvert($request->get('last_date_time_online_submit')),
    		'last_date_time_physical_submit' => $this->dateTimeConvert($request->get('last_date_time_physical_submit')),
    		'updated_ip' => $request->ip(),
    		'updated_by' => Auth::user()->id,
    		// 'assign_tender' => $new_arr,
    	];
    	if(Tender::where('id',$request->get('id'))->update($tender_arr)){

            $client_arr = [
                'tender_id' => $request->get('id'),
                'client_name' => $request->get('client_name'),
                'client_address' => $request->get('client_address'),
                'client_email' => $request->get('client_email'),
                'client_landline_no' => $request->get('client_landline_no'),
                'client_mobile_no' => $request->get('client_mobile_no'),
                'client_fax_no' => $request->get('client_fax_no'),
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];

            DB::table('tender_client_detail')->where('tender_id',$request->get('id'))->delete();
            DB::table('tender_client_detail')->insert($client_arr);

            $authority_arr = [];
            DB::table('tender_authority_contact_detail')->where('tender_id',$request->get('id'))->delete();
            $data = $request->all();
            foreach ($data['authority_name'] as $key => $value) {
                    $authority_arr['tender_id'] = $request->get('id');
                    $authority_arr['authority_name'] = $value;
                    $authority_arr['authority_designation'] = $data['authority_designation'][$key];
                    $authority_arr['authority_email'] = $data['authority_email'][$key];
                    $authority_arr['authority_mobile_no'] = $data['authority_mobile_no'][$key];
                    $authority_arr['created_ip'] = $request->ip();
                    $authority_arr['updated_ip'] = $request->ip();
                    DB::table('tender_authority_contact_detail')->insert($authority_arr);
            }


            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $dept_name = Department::whereId($request->get('department_id'))->value('dept_name');
            $tender_pattern_name = TenderPattern::whereId($request->get('tender_pattern'))->value('tender_pattern_name');
            $tender_category = TenderCategory::whereId($request->get('tender_category_id'))->value('tender_category');
            $add_string = "<br>Company Name: ".$company_name."<br>Department Name: ".$dept_name."<br>Tender Id Per Portal: ".$request->get('tender_id_per_portal')."<br>Portal Name: ".$request->get('portal_name')."<br>Tender No: ".$request->get('tender_no')."<br>Estimate Cost: ".$request->get('estimate_cost')."<br>Tender Pattern: ".$tender_pattern_name."<br>Tender Category: ".$tender_category;
            $tender_sr_no = Tender::where('id',$request->get('id'))->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." updated".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            //email
            /*$t_user = explode(',', $new_arr);
            $users_email = user::whereIn('id', $t_user)->pluck('email')->toArray();
            $tender_data = $this->get_tender_details($request->get('id'));

            $mail_data = [];
            $mail_data['name'] = "";
            $mail_data['to_email'] = $users_email;
            $mail_data['client_name'] = $tender_data['client_name'];
            $mail_data['tender_sr_no'] = $tender_data['tender_sr_no'];
            $mail_data['tender_id'] = $tender_data['tender_id_per_portal'];
            $mail_data['dept_name'] = $tender_data['dept_name'];
            $mail_data['portal_name'] = $tender_data['portal_name'];
            $mail_data['tender_no'] = $tender_data['tender_no'];
            $mail_data['state_name'] = $tender_data['state_name_work_execute'];
            $mail_data['name_of_work'] = $tender_data['name_of_work'];
            $this->common_task->tender_update($mail_data);

            $messages = "You are also selected as assigned employee for this tender. So please Login to your account for more details";
            $this->notification_task->tenderUpdateNotify($t_user, 'Tender Update', $messages);*/


    		return redirect()->route('admin.tender')->with('success', 'Edit successfully.');
    	}

		return redirect()->route('admin.tender')->with('error', 'Error during operation. Try again!');
    }

    public function select_tender(Request $request){

        if(!empty($request->input('select_tender_ids')))
        {

            $this->data['page_title'] = 'Assign Tender';
            $this->data['select_tender'] = Tender::select('id','tender_sr_no','name_of_work','portal_name')->whereIn('id',explode(',', $request->input('select_tender_ids')))->with('tender_client')->get()->toArray();;
            $default_assign_user = TenderPermission::where('type','default_assign_user')->get()->first();

            if($default_assign_user){
                $this->data['default_assign_user'] = User::whereIn('id',explode(',', $default_assign_user->user_id))->pluck('name','id');
            }else{
                $this->data['default_assign_user'] = [];
            }

            $simple_assign_user = TenderPermission::where('type','simple_assign_user')->get()->first();
            if($simple_assign_user){
                $this->data['simple_assign_user'] = User::whereIn('id',explode(',', $simple_assign_user->user_id))->pluck('name','id');
            }else{
                $this->data['simple_assign_user'] = [];
            }


            // dd($this->data);
            return view('admin.tender.assign_tender', $this->data);

            //
            $updare_arr = [
                'tender_status' => 'Selected',
                'selected_at' => date('Y-m-d H:i:s'),
                'selected_by' => Auth::user()->id,
            ];

            if (Tender::whereIn('id', explode(',',$request->input('select_tender_ids')))->update($updare_arr)) {

                //email code
                $ids = explode(',', $request->input('select_tender_ids'));
                $t_detail = Tender::whereIn('id',$ids)->get()->toArray();
                foreach ($t_detail as $key => $value) {
                    $t_user = explode(',', $value['assign_tender']);
                    $users_email = user::whereIn('id', $t_user)->pluck('email')->toArray();
                    $tender_data = $this->get_tender_details($value['id']);

                    $mail_data = [];
                    $mail_data['name'] = "";
                    $mail_data['to_email'] = $users_email;
                    $mail_data['client_name'] = $tender_data['client_name'];
                    $mail_data['tender_sr_no'] = $tender_data['tender_sr_no'];
                    $mail_data['tender_id'] = $tender_data['tender_id_per_portal'];
                    $mail_data['dept_name'] = $tender_data['dept_name'];
                    $mail_data['portal_name'] = $tender_data['portal_name'];
                    $mail_data['tender_no'] = $tender_data['tender_no'];
                    $mail_data['state_name'] = $tender_data['state_name_work_execute'];
                    $mail_data['name_of_work'] = $tender_data['name_of_work'];
                    $this->common_task->tender_selected($mail_data);

                    $messages = "Your tender selected. So please Login to your account for more details";
                    $this->notification_task->tenderSelectNotify($t_user, 'Tender Selected', $messages);
                }

                return redirect()->route('admin.tender')->with('success', 'Tender selected successfully.');
            }
        }
        return redirect()->route('admin.tender')->with('error', 'Error during operation. Try again!');
    }

    public function save_tender_assign(Request $request){
        // dd($request->all());
        $request_data = $request->all();
        $assign_arr = [];
        foreach ($request_data['tender_id'] as $key => $value) {
            array_push($request_data['assign_tender_user'][$key], $request_data['default_user']);
            $assign_arr['assign_tender'] = implode(',', $request_data['assign_tender_user'][$key]);
            $assign_arr['tender_status'] = 'Selected';
            $assign_arr['selected_at'] = date('Y-m-d H:i:s');
            $assign_arr['selected_by'] = Auth::user()->id;
            $assign_arr['updated_ip'] = $request->ip();

            if(Tender::whereId($value)->update($assign_arr)){

                // User Action Log
                $tender_sr_no = Tender::where('id',$value)->value('tender_sr_no');
                $action_data = [
                    'user_id' => Auth::user()->id,
                    'task_body' => "Tender Sr No. ".$tender_sr_no." selected",
                    'created_ip' => $request->ip(),
                ];
                $this->user_action_logs->action($action_data);

                $t_user = explode(',', $assign_arr['assign_tender']);
                $users_email = user::whereIn('id', $t_user)->pluck('email')->toArray();
                $tender_data = $this->get_tender_details($value);

                $mail_data = [];
                $mail_data['name'] = "";
                $mail_data['to_email'] = $users_email;
                $mail_data['client_name'] = $tender_data['client_name'];
                $mail_data['tender_sr_no'] = $tender_data['tender_sr_no'];
                $mail_data['tender_id'] = $tender_data['tender_id_per_portal'];
                $mail_data['dept_name'] = $tender_data['dept_name'];
                $mail_data['portal_name'] = $tender_data['portal_name'];
                $mail_data['tender_no'] = $tender_data['tender_no'];
                $mail_data['state_name'] = $tender_data['state_name_work_execute'];
                $mail_data['name_of_work'] = $tender_data['name_of_work'];
                $this->common_task->tender_selected($mail_data);

                $messages = "Your tender selected. So please Login to your account for more details";
                $this->notification_task->tenderSelectNotify($t_user, 'Tender Selected', $messages);
            }

        }
        return redirect()->route('admin.tender')->with('success', 'Tender selected successfully.');
    }

    public function selected_tender(){
        $this->data['page_title']='Selected Tender';
        return view('admin.tender.selected_tender', $this->data);
    }

    public function get_seleced_tender_list(){
        $datatable_fields = array('tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work','tender.state_name_work_execute','tender.estimate_cost','tender.joint_venture','tender.joint_venture_count','tender.quote_type','tender.other_quote_type','tender_pattern.tender_pattern_name','tender_category.tender_category','tender.last_date_time_download','tender.last_date_time_online_submit','tender.last_date_time_physical_submit','tender.assign_tender');
        $request = Input::all();
        $conditions_array = ['tender_status' => 'Selected'];

        $getfiled =array('tender.id','tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work','tender.state_name_work_execute','tender.estimate_cost','tender.joint_venture','tender.joint_venture_count','tender.quote_type','tender.other_quote_type','tender_pattern.tender_pattern_name','tender_category.tender_category','tender.last_date_time_download','tender.last_date_time_online_submit','tender.last_date_time_physical_submit','tender.assign_tender',\DB::raw("GROUP_CONCAT(users.name) as fullname"),'submission_status');

        $table = "tender";
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'department';
        $join_str[0]['join_table_id'] = 'department.id';
        $join_str[0]['from_table_id'] = 'tender.department_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'tender_category';
        $join_str[1]['join_table_id'] = 'tender_category.id';
        $join_str[1]['from_table_id'] = 'tender.tender_category_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'tender_pattern';
        $join_str[2]['join_table_id'] = 'tender_pattern.id';
        $join_str[2]['from_table_id'] = 'tender.tender_pattern';
        if(Auth::user()->id==$this->data['edit_tender_permission'] || Auth::user()->role==config('constants.SuperUser')){
            $where_raw="";
        }
        else{
            $logged_in_user_id= Auth::user()->id;
        $where_raw="FIND_IN_SET($logged_in_user_id,tender.assign_tender)";
        }
        echo Tender::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str,[],$where_raw);
        die();
    }

    public function edit_selected_tender($id){
        $tender = Tender::select('tender.*','tender_client_detail.*')->Leftjoin('tender_client_detail', 'tender.id', '=', 'tender_client_detail.tender_id')->where('tender.id',$id)->first();

        $this->data['page_title']='Edit Selected Tender';
        $this->data['tender'] = $tender;
        $this->data['tender_id'] = $id;

        $this->data['tender_financial_eligibility'] = Tender_financial_eligibility::whereTenderId($id)->get()->toArray();
        $this->data['tender_financial_eligibility_count'] = count($this->data['tender_financial_eligibility']);

        $this->data['tender_technical_eligibility'] = Tender_technical_eligibility::whereTenderId($id)->get()->toArray();
        $this->data['tender_technical_eligibility_count'] = count($this->data['tender_technical_eligibility']);

        $this->data['tender_bid_meet'] = Tender_pre_bid_document::whereTenderId($id)->get()->toArray();
        $this->data['tender_bid_meet_count'] = count($this->data['tender_bid_meet']);

        $this->data['tender_other_communication'] = Tender_other_communication::whereTenderId($id)->get()->toArray();
        $this->data['tender_other_communication_count'] = count($this->data['tender_other_communication']);

        $this->data['tender_condition_contract'] = Tender_condition_contract::whereTenderId($id)->get()->toArray();
        $this->data['tender_condition_contract_count'] = count($this->data['tender_condition_contract']);


        $this->data['department'] = Department::orderBy('dept_name')->pluck('dept_name','id');
        $this->data['companies'] = Companies::whereStatus('Enabled')->orderBy('company_name')->pluck('company_name', 'id');
        $this->data['tendercategory'] = TenderCategory::whereStatus('Enabled')->orderBy('tender_category')->pluck('tender_category','id');
        $this->data['user'] = User::whereStatus('Enabled')->pluck('name','id');
        $this->data['tenderpattern'] = TenderPattern::whereStatus('Enabled')->orderBy('tender_pattern_name')->pluck('tender_pattern_name','id');
        $this->data['tender_physical_submission'] = Tender_physical_submission::whereStatus('Enabled')->pluck('mode_name','id');
        $this->data['tender_authority_contact_detail'] = DB::table('tender_authority_contact_detail')->whereTenderId($id)->get()->toArray();
        $this->data['tender_authority_contact_detail_count'] = count($this->data['tender_authority_contact_detail']);
        $this->data['tender_tender_corrigendum_count'] = DB::table('tender_corrigendum')->whereTenderId($id)->count();

        $this->data['check_tender_fee'] = TenderPaymentRequest::where('tender_id',$id)->where('tender_type','fee')->count();
        $this->data['check_tender_emd'] = TenderPaymentRequest::where('tender_id',$id)->where('tender_type','emd')->count();

        // dd($this->data);
        return view('admin.tender.edit_selected_tender', $this->data);
    }

    public function save_tender_fee(Request $request){

        $id = $request->get('id');
        $update_fee = [
            "tender_fee" => $request->get('tender_fee'),
            "tender_fee_amount" => $request->get('tender_fee_amount'),
            "tender_fee_in_favour_of" => $request->get('tender_fee_in_favour_of'),
            "tender_fee_in_form_of" => $request->get('tender_fee_in_form_of'),
            "tender_fee_validity" => $request->get('tender_fee_validity'),
            "tender_fee_validity_date" => ($request->get('tender_fee_validity') == "On Date" ? $this->dateTimeConvert($request->get('tender_fee_validity_date')) : "") ,
            "created_ip" => $request->ip(),
            "updated_ip" => $request->ip(),
        ];

        if(Tender::whereId($id)->update($update_fee)){

            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender fee updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function save_tender_emd(Request $request){

        $id = $request->get('id');
        $update_fee = [
            "tender_emd" => $request->get('tender_emd'),
            "tender_emd_amount" => $request->get('tender_emd_amount'),
            "tender_emd_in_favour_of" => $request->get('tender_emd_in_favour_of'),
            "tender_emd_in_form_of" => $request->get('tender_emd_in_form_of'),
            "tender_emd_validity" => $request->get('tender_emd_validity'),
            "tender_emd_validity_date" => ($request->get('tender_emd_validity') == "On Date" ? $this->dateTimeConvert($request->get('tender_emd_validity_date')) : ""),
            "created_ip" => $request->ip(),
            "updated_ip" => $request->ip(),
        ];

        if(Tender::whereId($id)->update($update_fee)){

            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender emd updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function tender_tech_eli_sub(Request $request){
        // dd($request->all());

        $id = $request->get('id');

        $tech_arr = [];
        $data = $request->all();
        foreach ($data['technical_eligibility_document_name'] as $key => $value) {
            $tech_arr[$key]['tender_id'] = $id;
            $tech_arr[$key]['document_name'] = $value;
            $tech_arr[$key]['created_ip'] = $request->ip();
            $tech_arr[$key]['updated_ip'] = $request->ip();


            if(isset($data['technical_eligibility_document_attechement'][$key]) && !empty($data['technical_eligibility_document_attechement'][$key])){

                if($request->hasFile('technical_eligibility_document_attechement')){
                    $tech_image = $request->file('technical_eligibility_document_attechement');
                    foreach ($tech_image as $key1 => $image){
                        $file_path[$key1] = $image->store('public/tender_image');
                        if ($file_path[$key1]) {
                                $tech_arr[$key]['document_attechement'] = $file_path[$key];
                        }
                    }
                }else{
                    $tech_arr[$key]['document_attechement'] = $data['technical_eligibility_document_attechement_hidden'][$key];
                }
            }else{
                $tech_arr[$key]['document_attechement'] = $data['technical_eligibility_document_attechement_hidden'][$key];
            }
        }
        // dd($tech_arr);
        DB::table('tender_technical_eligibility')->where('tender_id',$id)->delete();

        $check = DB::table('tender_technical_eligibility')->where('tender_id',$id)->insert($tech_arr);
        if($check)
        {
            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender technical eligibility updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function tender_fina_eli_sub(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $tech_arr = [];
        $data = $request->all();
        foreach ($data['financial_eligibility_document_name'] as $key => $value) {
            $tech_arr[$key]['tender_id'] = $id;
            $tech_arr[$key]['document_name'] = $value;
            $tech_arr[$key]['created_ip'] = $request->ip();
            $tech_arr[$key]['updated_ip'] = $request->ip();

            if(isset($data['financial_eligibility_document_attechement'][$key]) && !empty($data['financial_eligibility_document_attechement'][$key])){

                if($request->hasFile('financial_eligibility_document_attechement')){
                    $tech_image = $request->file('financial_eligibility_document_attechement');
                    foreach ($tech_image as $key1 => $image){
                        $file_path[$key1] = $image->store('public/tender_image');
                        if ($file_path[$key1]) {
                                $tech_arr[$key]['document_attechement'] = $file_path[$key];
                        }
                    }
                }else{
                    $tech_arr[$key]['document_attechement'] = $data['financial_eligibility_document_attechement_hidden'][$key];
                }
            }else{
                $tech_arr[$key]['document_attechement'] = $data['financial_eligibility_document_attechement_hidden'][$key];
            }

        }

        // dd($tech_arr);
        DB::table('tender_financial_eligibility')->where('tender_id',$id)->delete();

        $check = DB::table('tender_financial_eligibility')->where('tender_id',$id)->insert($tech_arr);
        if($check)
        {
            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender financial eligibility updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function delete_financial_file(Request $request){
        DB::table('tender_financial_eligibility')->where('id',$request->get('id'))->delete();
    }

    public function delete_technical_file(Request $request){
        DB::table('tender_technical_eligibility')->where('id',$request->get('id'))->delete();
    }

    public function change_technical_file(Request $request){

        $id = $request->get('id');

        $tech_image = $request->file('file_img');
        $file_path = $tech_image->store('public/tender_image');
        $tech_arr = $file_path;
        DB::table('tender_technical_eligibility')->where('id',$id)->update(['document_attechement' => $tech_arr]);
    }

    public function change_financial_file(Request $request){

        $id = $request->get('id');

        $tech_image = $request->file('file_img');
        $file_path = $tech_image->store('public/tender_image');
        $tech_arr = $file_path;
        DB::table('tender_financial_eligibility')->where('id',$id)->update(['document_attechement' => $tech_arr]);
    }

    public function tender_submission(){
        $this->data['page_title']='Tender Submission';
        return view('admin.tender.tender_submission', $this->data);
    }

    public function get_submission_tender_list(){
        $datatable_fields = array('tender.id','tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work','tender.state_name_work_execute','tender.estimate_cost','tender.joint_venture','tender.joint_venture_count','tender.quote_type','tender.other_quote_type','tender_pattern.tender_pattern_name','tender_category.tender_category','tender.last_date_time_download','tender.last_date_time_online_submit','tender.last_date_time_physical_submit','tender.assign_tender','fullname');
        $request = Input::all();
        $conditions_array = ['submission_status' => '1'];

        $getfiled =array('tender.id','tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work','tender.state_name_work_execute','tender.estimate_cost','tender.joint_venture','tender.joint_venture_count','tender.quote_type','tender.other_quote_type','tender_pattern.tender_pattern_name','tender_category.tender_category','tender.last_date_time_download','tender.last_date_time_online_submit','tender.last_date_time_physical_submit','tender.assign_tender',\DB::raw("GROUP_CONCAT(users.name) as fullname"));

        $table = "tender";
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'department';
        $join_str[0]['join_table_id'] = 'department.id';
        $join_str[0]['from_table_id'] = 'tender.department_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'tender_category';
        $join_str[1]['join_table_id'] = 'tender_category.id';
        $join_str[1]['from_table_id'] = 'tender.tender_category_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'tender_pattern';
        $join_str[2]['join_table_id'] = 'tender_pattern.id';
        $join_str[2]['from_table_id'] = 'tender.tender_pattern';

        if(Auth::user()->id==$this->data['edit_tender_permission'] || Auth::user()->role==config('constants.SuperUser')){
            $where_raw="";
        }
        else{
            $logged_in_user_id= Auth::user()->id;
        $where_raw="FIND_IN_SET($logged_in_user_id,tender.assign_tender)";
        }

        echo Tender::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str,[],$where_raw);
        die();
    }

    public function edit_submission_tender($id){
        $tender = Tender::select('tender.*','tender_client_detail.*')->Leftjoin('tender_client_detail', 'tender.id', '=', 'tender_client_detail.tender_id')->where('tender.id',$id)->first();
        $this->data['page_title']='Tender Submission';
        $this->data['tender'] = $tender;
        $this->data['tender_id'] = $id;

        $this->data['tender_submission_technical_prepare']=DB::table('tender_submission_technical_part')->where('tender_id',$id)->get()->toArray();
        $this->data['tender_submission_technical_prepare_count']=count($this->data['tender_submission_technical_prepare']);

        $this->data['tender_submission_financial_prepare']=DB::table('tender_submission_financial_part')->where('tender_id',$id)->get()->toArray();
        $this->data['tender_submission_financial_prepare_count']=count($this->data['tender_submission_financial_prepare']);

        $this->data['tender_submission_commercial']=DB::table('tender_submission_commercial')->where('tender_id',$id)->get()->toArray();
        $this->data['tender_submission_commercial_count']=count($this->data['tender_submission_commercial']);

        // dd($this->data);
        return view('admin.tender.edit_tender_submission', $this->data);
    }

    public function save_tender_priliminary(Request $request){
        // dd($request->all());


        if($request->get('form_name') == 'fee'){

            if($request->hasFile('tender_fee_attechment')){
                $tender_fee_attechment_img = $request->file('tender_fee_attechment');
                $file_path = $tender_fee_attechment_img->store('public/tender_image');
                $tender_fee_attechment = $file_path;
            }else{
                $tender_fee_attechment = $request->get('tender_fee_attechment_hidden');
            }

            $arr = [
                'tender_fee_check_complated' => ($request->get('tender_fee_check_complated') == "on" ? "1" : ""),
                'tender_fee_status' => $request->get('tender_fee_status'),
                'tender_fee_attechment' => $tender_fee_attechment,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
        }

        if($request->get('form_name') == 'emd'){

            if($request->hasFile('tender_emd_attechment')){
                $tender_emd_attechment_img = $request->file('tender_emd_attechment');
                $file_path = $tender_emd_attechment_img->store('public/tender_image');
                $tender_emd_attechment = $file_path;
            }else{
                $tender_emd_attechment = $request->get('tender_emd_attechment_hidden');
            }

            $arr = [
                'tender_emd_check_complated' => ($request->get('tender_emd_check_complated') == "on" ? '1' : ""),
                'tender_emd_status' => $request->get('tender_emd_status'),
                'tender_emd_attechment' => $tender_emd_attechment,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
        }

        // dd($arr);

        if(Tender::whereId($request->get('id'))->update($arr)){

            // User Action Log
            $tender_sr_no = Tender::where('id',$request->get('id'))->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender ".$request->get('form_name')." submission updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function save_tender_submission(Request $request){

        if($request->get('final_sub_status') == "Yes"){
            if ($request->hasFile('final_sub_file')){
                $final_sub_file_img = $request->file('final_sub_file');
                $file_path = $final_sub_file_img->store('public/tender_image');
                $final_sub_file = $file_path;
            }else{
                $final_sub_file = $request->get('final_sub_file_hidden');
            }
        }else{
            $final_sub_file = Null;
        }


        $arr = [
            'final_sub_status' => $request->get('final_sub_status'),
            'final_sub_number' => ($request->get('final_sub_status') == "Yes" ? $request->get('final_sub_number') : Null),
            'final_sub_date_time' => ($request->get('final_sub_status') == "Yes" ? $this->dateTimeConvert($request->get('final_sub_date_time')) : Null),
            'final_sub_file' => $final_sub_file,
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
        ];

        if(Tender::whereId($request->get('id'))->update($arr)){

            // User Action Log
            $tender_sr_no = Tender::where('id',$request->get('id'))->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender final submission updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function downloadFeeDoc($id){
        $file = Tender::whereId($id)->first();
        if($file->tender_fee_attechment){
            $isExists = Storage::exists($file->tender_fee_attechment);
            if($isExists){
                return Storage::download($file->tender_fee_attechment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadEmdDoc($id){
        $file = Tender::whereId($id)->first();
        if($file->tender_emd_attechment){
            $isExists = Storage::exists($file->tender_emd_attechment);
            if($isExists){
                return Storage::download($file->tender_emd_attechment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function tender_sub_prepare_tech(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $tech_arr_add = [];
        $tech_arr_edit = [];
        $data = $request->all();

        foreach ($data['prepare_document_name'] as $key => $value) {
            if(isset($data['prepare_document_tech_id'][$key]) && !empty($data['prepare_document_tech_id'][$key])){

                $tech_arr_edit['tender_id'] = $id;
                $tech_arr_edit['prepare_document_name'] = $value;
                $tech_arr_edit['created_ip'] = $request->ip();
                $tech_arr_edit['updated_ip'] = $request->ip();

                if(isset($data['prepare_document_checked'][$key]) && !empty($data['prepare_document_checked'][$key])){
                    $data['prepare_document_checked'][$key] = "on";
                }else{
                    $data['prepare_document_checked'][$key] = "off";
                }
                $tech_arr_edit['prepare_document_checked'] = ($data['prepare_document_checked'][$key] == "on" ? "1" : Null);

                if(isset($data['prepare_document_attechment'][$key]) && !empty($data['prepare_document_attechment'][$key])){

                    if($request->hasFile('prepare_document_attechment')){
                        $tech_image = $request->file('prepare_document_attechment');
                        foreach ($tech_image as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr_edit['document_attechement'] = $file_path[$key];
                            }
                        }
                    }else{
                        $tech_arr_edit['prepare_document_attechment'] = $data['prepare_document_attechment_hidden'][$key];
                    }

                }else{
                    $tech_arr_edit['prepare_document_attechment'] = $data['prepare_document_attechment_hidden'][$key];
                }

                DB::table('tender_submission_technical_part')->where('id',$data['prepare_document_tech_id'][$key])->update($tech_arr_edit);
            }else{
                $tech_arr_add['tender_id'] = $id;
                $tech_arr_add['prepare_document_name'] = $value;
                $tech_arr_add['created_ip'] = $request->ip();
                $tech_arr_add['updated_ip'] = $request->ip();

                if(isset($data['prepare_document_checked'][$key]) && !empty($data['prepare_document_checked'][$key])){
                    $data['prepare_document_checked'][$key] = "on";
                }else{
                    $data['prepare_document_checked'][$key] = "off";
                }
                $tech_arr_add['prepare_document_checked'] = ($data['prepare_document_checked'][$key] == "on" ? "1" : Null);

                if(isset($data['prepare_document_attechment'][$key]) && !empty($data['prepare_document_attechment'][$key])){

                    $tech_image = $request->file('prepare_document_attechment');
                    foreach ($tech_image as $key1 => $image){
                        $file_path[$key1] = $image->store('public/tender_image');
                        if ($file_path[$key1]) {
                                $tech_arr_add['prepare_document_attechment'] = $file_path[$key];
                        }
                    }
                }else{
                    $tech_arr_add['prepare_document_attechment'] = $data['prepare_document_attechment_hidden'][$key];
                }
                DB::table('tender_submission_technical_part')->insert($tech_arr_add);
            }
        }
        // dd($tech_arr_edit);
        // dd($tech_arr_add);

        // User Action Log
        $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Tender Sr No. ".$tender_sr_no." tender technical prepration of document updated",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        echo "success";die();
        /*foreach ($data['prepare_document_name'] as $key => $value) {
            $tech_arr[$key]['tender_id'] = $id;
            $tech_arr[$key]['prepare_document_name'] = $value;

            if(isset($data['prepare_document_checked'][$key]) && !empty($data['prepare_document_checked'][$key])){
                $data['prepare_document_checked'][$key] = "on";
            }else{
                $data['prepare_document_checked'][$key] = "off";
            }
            $tech_arr[$key]['prepare_document_checked'] = ($data['prepare_document_checked'][$key] == "on" ? "1" : Null);

            $tech_image = $request->file('prepare_document_attechment');
            foreach ($tech_image as $key1 => $image){
                $file_path[$key1] = $image->store('public/tender_image');
                if ($file_path[$key1]) {
                        $tech_arr[$key]['prepare_document_attechment'] = $file_path[$key];
                }
            }

        }*/
        // dd($tech_arr);
        // DB::table('tender_submission_technical_part')->where('tender_id',$id)->delete();


        // if($check)
        // {
        //     echo "success";die();
        // }else{
        //     echo "error";die();
        // }
    }

    public function delete_prepare_technical_file(Request $request){
        DB::table('tender_submission_technical_part')->whereId($request->get('id'))->delete();
    }

    public function tender_sub_uploaded_tech(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $tech_arr_edit = [];
        $data = $request->all();

        foreach ($data['uploaded_document_name'] as $key => $value) {
            if(isset($data['uploaded_document_tech_id'][$key]) && !empty($data['uploaded_document_tech_id'][$key])){

                $tech_arr_edit['tender_id'] = $id;
                $tech_arr_edit['uploaded_document_name'] = $value;
                $tech_arr_edit['created_ip'] = $request->ip();
                $tech_arr_edit['updated_ip'] = $request->ip();

                if(isset($data['uploaded_document_checked'][$key]) && !empty($data['uploaded_document_checked'][$key])){
                    $data['uploaded_document_checked'][$key] = "on";
                }else{
                    $data['uploaded_document_checked'][$key] = "off";
                }
                $tech_arr_edit['uploaded_document_checked'] = ($data['uploaded_document_checked'][$key] == "on" ? "1" : Null);

                if(isset($data['uploaded_document_attechment'][$key]) && !empty($data['uploaded_document_attechment'][$key])){

                    if($request->hasFile('uploaded_document_attechment')){
                        $tech_image = $request->file('uploaded_document_attechment');
                        foreach ($tech_image as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr_edit['uploaded_document_attechment'] = $file_path[$key];
                            }
                        }
                    }

                }else{
                    $tech_arr_edit['uploaded_document_attechment'] = $data['uploaded_document_attechment_hidden'][$key];
                }

                DB::table('tender_submission_technical_part')->where('id',$data['uploaded_document_tech_id'][$key])->update($tech_arr_edit);
            }else{

            }
        }
        // dd($tech_arr_edit);

        // User Action Log
        $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Tender Sr No. ".$tender_sr_no." tender technical uploaded of document updated",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        echo "success";die();
        /*foreach ($data['uploaded_document_name'] as $key => $value) {
            $tech_arr[$key]['tender_id'] = $id;
            $tech_arr[$key]['uploaded_document_name'] = $value;

            if(isset($data['uploaded_document_checked'][$key]) && !empty($data['uploaded_document_checked'][$key])){
                $data['uploaded_document_checked'][$key] = "on";
            }else{
                $data['uploaded_document_checked'][$key] = "off";
            }
            $tech_arr[$key]['uploaded_document_checked'] = ($data['uploaded_document_checked'][$key] == "on" ? "1" : Null);

            $tech_image = $request->file('uploaded_document_attechment');
            foreach ($tech_image as $key1 => $image){
                $file_path[$key1] = $image->store('public/tender_image');
                if ($file_path[$key1]) {
                        $tech_arr[$key]['uploaded_document_attechment'] = $file_path[$key];
                }
            }

        }*/
        // dd($tech_arr);
        // DB::table('tender_submission_technical_part')->where('tender_id',$id)->delete();

        /*$check = DB::table('tender_submission_technical_part')->insert($tech_arr);
        if($check)
        {
            echo "success";die();
        }else{
            echo "error";die();
        }*/
    }

    public function get_tender_submission_tech(Request $request){
        $data = DB::table('tender_submission_technical_part')->whereTenderId($request->get('id'))->get();
        echo json_encode($data);
    }

    public function downloadsubtechdoc($id){
        $file = DB::table('tender_submission_technical_part')->whereId($id)->first();
        if($file->uploaded_document_attechment){
            $isExists = Storage::exists($file->uploaded_document_attechment);
            if($isExists){
                return Storage::download($file->uploaded_document_attechment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadsubpreparetechdoc($id){
        $file = DB::table('tender_submission_technical_part')->whereId($id)->first();
        if($file->prepare_document_attechment){
            $isExists = Storage::exists($file->prepare_document_attechment);
            if($isExists){
                return Storage::download($file->prepare_document_attechment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function tender_sub_prepare_fina(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $tech_arr_add = [];
        $tech_arr_edit = [];
        $data = $request->all();


        foreach ($data['prepare_document_name_fina'] as $key => $value) {
            if(isset($data['prepare_document_fina_id'][$key]) && !empty($data['prepare_document_fina_id'][$key])){

                $tech_arr_edit['tender_id'] = $id;
                $tech_arr_edit['prepare_document_name'] = $value;
                $tech_arr_edit['created_ip'] = $request->ip();
                $tech_arr_edit['updated_ip'] = $request->ip();

                if(isset($data['prepare_document_checked_fina'][$key]) && !empty($data['prepare_document_checked_fina'][$key])){
                    $data['prepare_document_checked_fina'][$key] = "on";
                }else{
                    $data['prepare_document_checked_fina'][$key] = "off";
                }
                $tech_arr_edit['prepare_document_checked'] = ($data['prepare_document_checked_fina'][$key] == "on" ? "1" : Null);

                if(isset($data['prepare_document_attechment_fina'][$key]) && !empty($data['prepare_document_attechment_fina'][$key])){

                    if($request->hasFile('prepare_document_attechment_fina')){
                        $tech_image = $request->file('prepare_document_attechment_fina');
                        foreach ($tech_image as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr_edit['prepare_document_attechment'] = $file_path[$key];
                            }
                        }
                    }else{
                        $tech_arr_edit['prepare_document_attechment'] = $data['prepare_document_attechment_fina_hidden'][$key];
                    }
                }else{
                    $tech_arr_edit['prepare_document_attechment'] = $data['prepare_document_attechment_fina_hidden'][$key];
                }

                DB::table('tender_submission_financial_part')->where('id',$data['prepare_document_fina_id'][$key])->update($tech_arr_edit);
            }else{
                $tech_arr_add['tender_id'] = $id;
                $tech_arr_add['prepare_document_name'] = $value;
                $tech_arr_add['created_ip'] = $request->ip();
                $tech_arr_add['updated_ip'] = $request->ip();

                if(isset($data['prepare_document_checked_fina'][$key]) && !empty($data['prepare_document_checked_fina'][$key])){
                    $data['prepare_document_checked_fina'][$key] = "on";
                }else{
                    $data['prepare_document_checked_fina'][$key] = "off";
                }
                $tech_arr_add['prepare_document_checked'] = ($data['prepare_document_checked_fina'][$key] == "on" ? "1" : Null);

                if(isset($data['prepare_document_attechment_fina'][$key]) && !empty($data['prepare_document_attechment_fina'][$key])){

                    $tech_image = $request->file('prepare_document_attechment_fina');
                    foreach ($tech_image as $key1 => $image){
                        $file_path[$key1] = $image->store('public/tender_image');
                        if ($file_path[$key1]) {
                                $tech_arr_add['prepare_document_attechment'] = $file_path[$key];
                        }
                    }
                }else{
                    $tech_arr_add['prepare_document_attechment'] = $data['prepare_document_attechment_fina_hidden'][$key];
                }
                DB::table('tender_submission_financial_part')->insert($tech_arr_add);
            }
        }

        // User Action Log
        $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Tender Sr No. ".$tender_sr_no." tender financial preparation of document updated",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        echo "success";die();

        /*foreach ($data['prepare_document_name_fina'] as $key => $value) {
            $tech_arr[$key]['tender_id'] = $id;
            $tech_arr[$key]['prepare_document_name'] = $value;

            if(isset($data['prepare_document_checked_fina'][$key]) && !empty($data['prepare_document_checked_fina'][$key])){
                $data['prepare_document_checked'][$key] = "on";
            }else{
                $data['prepare_document_checked'][$key] = "off";
            }
            $tech_arr[$key]['prepare_document_checked'] = ($data['prepare_document_checked'][$key] == "on" ? "1" : Null);

            $tech_image = $request->file('prepare_document_attechment_fina');
            foreach ($tech_image as $key1 => $image){
                $file_path[$key1] = $image->store('public/tender_image');
                if ($file_path[$key1]) {
                        $tech_arr[$key]['prepare_document_attechment'] = $file_path[$key];
                }
            }

        }*/
        // dd($tech_arr);
        // DB::table('tender_submission_financial_part')->where('tender_id',$id)->delete();

        /*$check = DB::table('tender_submission_financial_part')->insert($tech_arr);
        if($check)
        {
            echo "success";die();
        }else{
            echo "error";die();
        }*/
    }

    public function delete_prepare_financial_file(Request $request){
        DB::table('tender_submission_financial_part')->whereId($request->get('id'))->delete();
    }

    public function tender_sub_uploaded_fina(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $tech_arr_edit = [];
        $data = $request->all();


        foreach ($data['uploaded_document_name_fina'] as $key => $value) {
            if(isset($data['uploaded_document_fina_id'][$key]) && !empty($data['uploaded_document_fina_id'][$key])){

                $tech_arr_edit['tender_id'] = $id;
                $tech_arr_edit['uploaded_document_name'] = $value;
                $tech_arr_edit['created_ip'] = $request->ip();
                $tech_arr_edit['updated_ip'] = $request->ip();

                if(isset($data['uploaded_document_checked_fina'][$key]) && !empty($data['uploaded_document_checked_fina'][$key])){
                    $data['uploaded_document_checked_fina'][$key] = "on";
                }else{
                    $data['uploaded_document_checked_fina'][$key] = "off";
                }
                $tech_arr_edit['uploaded_document_checked'] = ($data['uploaded_document_checked_fina'][$key] == "on" ? "1" : Null);

                if(isset($data['uploaded_document_attechment_fina'][$key]) && !empty($data['uploaded_document_attechment_fina'][$key])){

                    if($request->hasFile('uploaded_document_attechment_fina'))
                    {
                        $tech_image = $request->file('uploaded_document_attechment_fina');
                        foreach ($tech_image as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr_edit['uploaded_document_attechment'] = $file_path[$key];
                            }
                        }
                    }
                }else{
                    $tech_arr_edit['uploaded_document_attechment'] = $data['uploaded_document_attechment_fina_hidden'][$key];
                }

                DB::table('tender_submission_financial_part')->where('id',$data['uploaded_document_fina_id'][$key])->update($tech_arr_edit);
            }else{

            }
        }

        // User Action Log
        $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Tender Sr No. ".$tender_sr_no." tender financial uploaded of document updated",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);
        // dd($tech_arr_edit);
        echo "success";die();
        /*foreach ($data['uploaded_document_name_fina'] as $key => $value) {
            $tech_arr[$key]['tender_id'] = $id;
            $tech_arr[$key]['uploaded_document_name'] = $value;

            if(isset($data['uploaded_document_checked_fina'][$key]) && !empty($data['uploaded_document_checked_fina'][$key])){
                $data['uploaded_document_checked'][$key] = "on";
            }else{
                $data['uploaded_document_checked'][$key] = "off";
            }
            $tech_arr[$key]['uploaded_document_checked'] = ($data['uploaded_document_checked'][$key] == "on" ? "1" : Null);

            $tech_image = $request->file('uploaded_document_attechment_fina');
            foreach ($tech_image as $key1 => $image){
                $file_path[$key1] = $image->store('public/tender_image');
                if ($file_path[$key1]) {
                        $tech_arr[$key]['uploaded_document_attechment'] = $file_path[$key];
                }
            }

        }*/
        // dd($tech_arr);
        // DB::table('tender_submission_financial_part')->where('tender_id',$id)->delete();

        /*$check = DB::table('tender_submission_financial_part')->insert($tech_arr);
        if($check)
        {
            echo "success";die();
        }else{
            echo "error";die();
        }*/
    }

    public function get_tender_submission_fina(Request $request){
        $data = DB::table('tender_submission_financial_part')->whereTenderId($request->get('id'))->get();
        echo json_encode($data);
    }

    public function downloadsubfinadoc($id){
        $file = DB::table('tender_submission_financial_part')->whereId($id)->first();
        if($file->uploaded_document_attechment){
            $isExists = Storage::exists($file->uploaded_document_attechment);
            if($isExists){
                return Storage::download($file->uploaded_document_attechment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadsubpreparefinadoc($id){
        $file = DB::table('tender_submission_financial_part')->whereId($id)->first();
        if($file->prepare_document_attechment){
            $isExists = Storage::exists($file->prepare_document_attechment);
            if($isExists){
                return Storage::download($file->prepare_document_attechment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function tender_sub_prepare_boq(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $tech_arr_add = [];
        $tech_arr_edit = [];
        $data = $request->all();

        foreach ($data['prepare_document_name_boq'] as $key => $value) {
            if(isset($data['prepare_document_boq_id'][$key]) && !empty($data['prepare_document_boq_id'][$key])){

                $tech_arr_edit['tender_id'] = $id;
                $tech_arr_edit['prepare_document_name'] = $value;
                $tech_arr_edit['created_ip'] = $request->ip();
                $tech_arr_edit['updated_ip'] = $request->ip();

                if(isset($data['prepare_document_checked_boq'][$key]) && !empty($data['prepare_document_checked_boq'][$key])){
                    $data['prepare_document_checked_boq'][$key] = "on";
                }else{
                    $data['prepare_document_checked_boq'][$key] = "off";
                }
                $tech_arr_edit['prepare_document_checked'] = ($data['prepare_document_checked_boq'][$key] == "on" ? "1" : Null);

                if(isset($data['prepare_document_attechment_boq'][$key]) && !empty($data['prepare_document_attechment_boq'][$key])){

                    if($request->hasFile('prepare_document_attechment_boq')){
                        $tech_image = $request->file('prepare_document_attechment_boq');
                        foreach ($tech_image as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr_edit['prepare_document_attechment'] = $file_path[$key];
                            }
                        }
                    }else{
                        $tech_arr_edit['prepare_document_attechment'] = $data['prepare_document_attechment_boq_hidden'][$key];
                    }
                }else{
                    $tech_arr_edit['prepare_document_attechment'] = $data['prepare_document_attechment_boq_hidden'][$key];
                }

                DB::table('tender_submission_commercial')->where('id',$data['prepare_document_boq_id'][$key])->update($tech_arr_edit);
            }else{
                $tech_arr_add['tender_id'] = $id;
                $tech_arr_add['prepare_document_name'] = $value;
                $tech_arr_add['created_ip'] = $request->ip();
                $tech_arr_add['updated_ip'] = $request->ip();

                if(isset($data['prepare_document_checked_boq'][$key]) && !empty($data['prepare_document_checked_boq'][$key])){
                    $data['prepare_document_checked_boq'][$key] = "on";
                }else{
                    $data['prepare_document_checked_boq'][$key] = "off";
                }
                $tech_arr_add['prepare_document_checked'] = ($data['prepare_document_checked_boq'][$key] == "on" ? "1" : Null);

                if(isset($data['prepare_document_attechment_boq'][$key]) && !empty($data['prepare_document_attechment_boq'][$key])){

                    $tech_image = $request->file('prepare_document_attechment_boq');
                    foreach ($tech_image as $key1 => $image){
                        $file_path[$key1] = $image->store('public/tender_image');
                        if ($file_path[$key1]) {
                                $tech_arr_add['prepare_document_attechment'] = $file_path[$key];
                        }
                    }
                }else{
                    $tech_arr_add['prepare_document_attechment'] = $data['prepare_document_attechment_boq_hidden'][$key];
                }
                DB::table('tender_submission_commercial')->insert($tech_arr_add);
            }
        }

        // User Action Log
        $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Tender Sr No. ".$tender_sr_no." tender commercial preparation of document updated",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        // dd($tech_arr_edit);
        echo "success";die();

        /*foreach ($data['prepare_document_name_boq'] as $key => $value) {
            $tech_arr[$key]['tender_id'] = $id;
            $tech_arr[$key]['prepare_document_name'] = $value;

            if(isset($data['prepare_document_checked_boq'][$key]) && !empty($data['prepare_document_checked_boq'][$key])){
                $data['prepare_document_checked'][$key] = "on";
            }else{
                $data['prepare_document_checked'][$key] = "off";
            }
            $tech_arr[$key]['prepare_document_checked'] = ($data['prepare_document_checked'][$key] == "on" ? "1" : Null);

            $tech_image = $request->file('prepare_document_attechment_boq');
            foreach ($tech_image as $key1 => $image){
                $file_path[$key1] = $image->store('public/tender_image');
                if ($file_path[$key1]) {
                        $tech_arr[$key]['prepare_document_attechment'] = $file_path[$key];
                }
            }

        }*/
        // dd($tech_arr);
        // DB::table('tender_submission_commercial')->where('tender_id',$id)->delete();

        /*$check = DB::table('tender_submission_commercial')->insert($tech_arr);
        if($check)
        {
            echo "success";die();
        }else{
            echo "error";die();
        }*/
    }

    public function delete_prepare_boq_file(Request $request){
        DB::table('tender_submission_commercial')->whereId($request->get('id'))->delete();
    }

    public function tender_sub_uploaded_boq(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $tech_arr_add = [];
        $tech_arr_edit = [];
        $data = $request->all();

        foreach ($data['uploaded_document_name_boq'] as $key => $value) {
            if(isset($data['uploaded_document_boq_id'][$key]) && !empty($data['uploaded_document_boq_id'][$key])){

                $tech_arr_edit['tender_id'] = $id;
                $tech_arr_edit['uploaded_document_name'] = $value;
                $tech_arr_edit['created_ip'] = $request->ip();
                $tech_arr_edit['updated_ip'] = $request->ip();

                if(isset($data['uploaded_document_checked_boq'][$key]) && !empty($data['uploaded_document_checked_boq'][$key])){
                    $data['uploaded_document_checked_boq'][$key] = "on";
                }else{
                    $data['uploaded_document_checked_boq'][$key] = "off";
                }
                $tech_arr_edit['uploaded_document_checked'] = ($data['uploaded_document_checked_boq'][$key] == "on" ? "1" : Null);

                if(isset($data['uploaded_document_attechment_boq'][$key]) && !empty($data['uploaded_document_attechment_boq'][$key])){

                    if($request->hasFile('uploaded_document_attechment_boq')){
                        $tech_image = $request->file('uploaded_document_attechment_boq');
                        foreach ($tech_image as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr_edit['uploaded_document_attechment'] = $file_path[$key];
                            }
                        }
                    }
                }else{
                    $tech_arr_edit['uploaded_document_attechment'] = $data['uploaded_document_attechment_boq_hidden'][$key];
                }

                DB::table('tender_submission_commercial')->where('id',$data['uploaded_document_boq_id'][$key])->update($tech_arr_edit);
            }else{

            }
        }

        // User Action Log
        $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Tender Sr No. ".$tender_sr_no." tender commercial uploaded of document updated",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);
        // dd($tech_arr_edit);
        echo "success";die();


/*        foreach ($data['uploaded_document_name_boq'] as $key => $value) {
            $tech_arr[$key]['tender_id'] = $id;
            $tech_arr[$key]['uploaded_document_name'] = $value;

            if(isset($data['uploaded_document_checked_boq'][$key]) && !empty($data['uploaded_document_checked_boq'][$key])){
                $data['uploaded_document_checked'][$key] = "on";
            }else{
                $data['uploaded_document_checked'][$key] = "off";
            }
            $tech_arr[$key]['uploaded_document_checked'] = ($data['uploaded_document_checked'][$key] == "on" ? "1" : Null);

            $tech_image = $request->file('uploaded_document_attechment_boq');
            foreach ($tech_image as $key1 => $image){
                $file_path[$key1] = $image->store('public/tender_image');
                if ($file_path[$key1]) {
                        $tech_arr[$key]['uploaded_document_attechment'] = $file_path[$key];
                }
            }

        }*/
        // dd($tech_arr);
        // DB::table('tender_submission_commercial')->where('tender_id',$id)->delete();

        /*$check = DB::table('tender_submission_commercial')->insert($tech_arr);
        if($check)
        {
            echo "success";die();
        }else{
            echo "error";die();
        }*/
    }

    public function get_tender_submission_boq(Request $request){
        $data = DB::table('tender_submission_commercial')->whereTenderId($request->get('id'))->whereNotNull('prepare_document_name')->get();
        echo json_encode($data);
    }

    public function downloadsubboqdoc($id){
        $file = DB::table('tender_submission_commercial')->whereId($id)->first();
        if($file->uploaded_document_attechment){
            $isExists = Storage::exists($file->uploaded_document_attechment);
            if($isExists){
                return Storage::download($file->uploaded_document_attechment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadsubprepareboqdoc($id){
        $file = DB::table('tender_submission_commercial')->whereId($id)->first();
        if($file->prepare_document_attechment){
            $isExists = Storage::exists($file->prepare_document_attechment);
            if($isExists){
                return Storage::download($file->prepare_document_attechment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function save_tender_detail(Request $request){
        // dd($request->all());
        /*if(is_array($request->get('assign_tender'))){
            $new_arr = implode(',', $request->get('assign_tender'));
        }else{
            $new_arr = "";
        }*/

        $tender_arr = [
            'department_id' => $request->get('department_id'),
            'company_id' => $request->get('company_id'),
            'tender_id_per_portal' => $request->get('tender_id_per_portal'),
            'portal_name' => $request->get('portal_name'),
            'tender_no' => $request->get('tender_no'),
            'name_of_work' => $request->get('name_of_work'),
            'state_name_work_execute' => $request->get('state_name_work_execute'),
            'estimate_cost' => $request->get('estimate_cost'),
            'joint_venture' => $request->get('joint_venture'),
            'joint_venture_count' => ($request->get('joint_venture_count'))? $request->get('joint_venture_count') : Null,
            'quote_type' => $request->get('quote_type'),
            'tender_pattern' => $request->get('tender_pattern'),
            'other_quote_type' => ($request->get('other_quote_type'))? $request->get('other_quote_type') : Null,
            'tender_category_id' => $request->get('tender_category_id'),
            'last_date_time_download' => $this->dateTimeConvert($request->get('last_date_time_download')),
            'last_date_time_online_submit' => $this->dateTimeConvert($request->get('last_date_time_online_submit')),
            'last_date_time_physical_submit' => $this->dateTimeConvert($request->get('last_date_time_physical_submit')),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
            // 'assign_tender' => $new_arr,
        ];
        if(Tender::where('id',$request->get('id'))->update($tender_arr)){

            $client_arr = [
                'tender_id' => $request->get('id'),
                'client_name' => $request->get('client_name'),
                'client_address' => $request->get('client_address'),
                'client_email' => $request->get('client_email'),
                'client_landline_no' => $request->get('client_landline_no'),
                'client_mobile_no' => $request->get('client_mobile_no'),
                'client_fax_no' => $request->get('client_fax_no'),
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];

            DB::table('tender_client_detail')->where('tender_id',$request->get('id'))->delete();
            DB::table('tender_client_detail')->insert($client_arr);

            $authority_arr = [];
            DB::table('tender_authority_contact_detail')->where('tender_id',$request->get('id'))->delete();
            $data = $request->all();
            foreach ($data['authority_name'] as $key => $value) {
                    $authority_arr['tender_id'] = $request->get('id');
                    $authority_arr['authority_name'] = $value;
                    $authority_arr['authority_designation'] = $data['authority_designation'][$key];
                    $authority_arr['authority_email'] = $data['authority_email'][$key];
                    $authority_arr['authority_mobile_no'] = $data['authority_mobile_no'][$key];
                    $authority_arr['created_ip'] = $request->ip();
                    $authority_arr['updated_ip'] = $request->ip();
                    DB::table('tender_authority_contact_detail')->insert($authority_arr);
            }


            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $dept_name = Department::whereId($request->get('department_id'))->value('dept_name');
            $tender_pattern_name = TenderPattern::whereId($request->get('tender_pattern'))->value('tender_pattern_name');
            $tender_category = TenderCategory::whereId($request->get('tender_category_id'))->value('tender_category');
            $add_string = "<br>Company Name: ".$company_name."<br>Department Name: ".$dept_name."<br>Tender Id Per Portal: ".$request->get('tender_id_per_portal')."<br>Portal Name: ".$request->get('portal_name')."<br>Tender No: ".$request->get('tender_no')."<br>Estimate Cost: ".$request->get('estimate_cost')."<br>Tender Pattern: ".$tender_pattern_name."<br>Tender Category: ".$tender_category;
            $tender_sr_no = Tender::where('id',$request->get('id'))->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." updated".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            //email
            /*$t_user = explode(',', $new_arr);
            $users_email = user::whereIn('id', $t_user)->pluck('email')->toArray();
            $tender_data = $this->get_tender_details($request->get('id'));

            $mail_data = [];
            $mail_data['name'] = "";
            $mail_data['to_email'] = $users_email;
            $mail_data['client_name'] = $tender_data['client_name'];
            $mail_data['tender_sr_no'] = $tender_data['tender_sr_no'];
            $mail_data['tender_id'] = $tender_data['tender_id_per_portal'];
            $mail_data['dept_name'] = $tender_data['dept_name'];
            $mail_data['portal_name'] = $tender_data['portal_name'];
            $mail_data['tender_no'] = $tender_data['tender_no'];
            $mail_data['state_name'] = $tender_data['state_name_work_execute'];
            $mail_data['name_of_work'] = $tender_data['name_of_work'];
            $this->common_task->tender_update($mail_data);

            $messages = "You are also selected as assigned employee for this tender. So please Login to your account for more details";
            $this->notification_task->tenderUpdateNotify($t_user, 'Tender Update', $messages);*/

            // return redirect()->route('admin.tender')->with('success', 'Edit successfully.');
            echo "success";die();
        }
        echo "error";die();
        // return redirect()->route('admin.tender')->with('error', 'Error during operation. Try again!');
    }

    public function tender_pre_bid_meet(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $tech_arr = [];
        $data = $request->all();

        if($data['pre_bid_meeting'] == "Yes"){
                $tender_arr = [
                    'pre_bid_meeting' => $data['pre_bid_meeting'],
                    'pre_bid_meeting_datetime' => $this->dateTimeConvert($data['pre_bid_meeting_datetime']),
                    'pre_bid_meeting_venue' => $data['pre_bid_meeting_venue'],
                ];
                Tender::whereId($id)->update($tender_arr);
                foreach ($data['query_point_document_name'] as $key => $value) {
                    $tech_arr[$key]['tender_id'] = $id;
                    $tech_arr[$key]['query_point_document_name'] = $value;
                    $tech_arr[$key]['created_ip'] = $request->ip();;
                    $tech_arr[$key]['updated_ip'] = $request->ip();;


                    if(isset($data['query_point_document_attechment'][$key]) && !empty($data['query_point_document_attechment'][$key])){

                        if($request->hasFile('query_point_document_attechment')){
                            $tech_image = $request->file('query_point_document_attechment');
                            foreach ($tech_image as $key1 => $image){
                                $file_path[$key1] = $image->store('public/tender_image');
                                if ($file_path[$key1]) {
                                        $tech_arr[$key]['query_point_document_attechment'] = $file_path[$key];
                                }
                            }
                        }else{
                            $tech_arr[$key]['query_point_document_attechment'] = $data['query_point_document_attechment_hidden'][$key];
                        }
                    }else{
                        $tech_arr[$key]['query_point_document_attechment'] = $data['query_point_document_attechment_hidden'][$key];
                    }
                }
                // dd($tech_arr);
                DB::table('tender_pre_bid_document')->where('tender_id',$id)->delete();

                $check = DB::table('tender_pre_bid_document')->insert($tech_arr);
                if($check)
                {

                    // User Action Log
                    $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
                    $action_data = [
                        'user_id' => Auth::user()->id,
                        'task_body' => "Tender Sr No. ".$tender_sr_no." pre-bid meeting updated",
                        'created_ip' => $request->ip(),
                    ];
                    $this->user_action_logs->action($action_data);

                    echo "success";die();
                }else{
                    echo "error";die();
                }
        }else{
            $tender_arr = [
                    'pre_bid_meeting' => $data['pre_bid_meeting'],
                    'pre_bid_meeting_datetime' => Null,
                    'pre_bid_meeting_venue' => "",
                ];
                Tender::whereId($id)->update($tender_arr);
                // User Action Log
                $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
                $action_data = [
                    'user_id' => Auth::user()->id,
                    'task_body' => "Tender Sr No. ".$tender_sr_no." pre-bid meeting updated",
                    'created_ip' => $request->ip(),
                ];
                $this->user_action_logs->action($action_data);

                DB::table('tender_pre_bid_document')->where('tender_id',$id)->delete();

                echo "success";die();
        }

    }

    public function delete_bid_document(Request $request){
        DB::table('tender_pre_bid_document')->where('id',$request->get('id'))->delete();
    }

    public function change_bid_document_file(Request $request){
        $id = $request->get('id');

        $tech_image = $request->file('file_img');
        $file_path = $tech_image->store('public/tender_image');
        $tech_arr = $file_path;
        DB::table('tender_pre_bid_document')->where('id',$id)->update(['query_point_document_attechment' => $tech_arr]);
    }

    public function tender_other_communication(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $comm_arr = [];
        $data = $request->all();
        foreach ($data['other_communication_title'] as $key => $value) {
            $comm_arr[$key]['tender_id'] = $id;
            $comm_arr[$key]['other_communication_title'] = $value;
            $comm_arr[$key]['other_communication_date'] = $this->dateTimeConvert($data['other_communication_date'][$key]);
            $comm_arr[$key]['created_ip'] = $request->ip();
            $comm_arr[$key]['updated_ip'] = $request->ip();


            if(isset($data['communication_document_attechement'][$key]) && !empty($data['communication_document_attechement'][$key])){

                if($request->hasFile('communication_document_attechement')){
                    $tech_image = $request->file('communication_document_attechement');
                    foreach ($tech_image as $key1 => $image){
                        $file_path[$key1] = $image->store('public/tender_image');
                        if ($file_path[$key1]) {
                                $comm_arr[$key]['communication_document_attechement'] = $file_path[$key];
                        }
                    }
                }else{
                    $comm_arr[$key]['communication_document_attechement'] = $data['communication_document_attechement_hidden'][$key];
                }
            }else{
                $comm_arr[$key]['communication_document_attechement'] = $data['communication_document_attechement_hidden'][$key];
            }

        }

        // dd($comm_arr);
        DB::table('tender_other_communication')->where('tender_id',$id)->delete();

        $check = DB::table('tender_other_communication')->where('tender_id',$id)->insert($comm_arr);
        if($check)
        {
            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender other communication updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function delete_communication_document(Request $request){
        DB::table('tender_other_communication')->where('id',$request->get('id'))->delete();
    }

    public function change_communication_document_file(Request $request){
        $id = $request->get('id');

        $tech_image = $request->file('file_img');
        $file_path = $tech_image->store('public/tender_image');
        $tech_arr = $file_path;
        DB::table('tender_other_communication')->where('id',$id)->update(['communication_document_attechement' => $tech_arr]);
    }

    public function save_tender_physical_sub(Request $request){

        $updare_arr = [
            'physical_sub_mode'=> $request->get('physical_sub_mode'),
            'physical_sub_mode_due_date'=> $this->dateTimeConvert($request->get('physical_sub_mode_due_date')),
            "created_ip" => $request->ip(),
            "updated_ip" => $request->ip(),
        ];

        if(Tender::whereId($request->get('id'))->update($updare_arr)){
            // User Action Log
            $tender_sr_no = Tender::where('id',$request->get('id'))->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender physical submission updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }
        echo "error";die();
    }

    public function tender_condition_contract(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $comm_arr = [];
        $data = $request->all();
        foreach ($data['condition_title'] as $key => $value) {
            $comm_arr[$key]['tender_id'] = $id;
            $comm_arr[$key]['condition_title'] = $value;
            $comm_arr[$key]['created_ip'] = $request->ip();
            $comm_arr[$key]['updated_ip'] = $request->ip();


            if(isset($data['condition_document_attechement'][$key]) && !empty($data['condition_document_attechement'][$key])){

                if($request->hasFile('condition_document_attechement')){
                $tech_image = $request->file('condition_document_attechement');
                foreach ($tech_image as $key1 => $image){
                    $file_path[$key1] = $image->store('public/tender_image');
                    if ($file_path[$key1]) {
                            $comm_arr[$key]['condition_document_attechement'] = $file_path[$key];
                    }
                }
                }else{
                    $comm_arr[$key]['condition_document_attechement'] = $data['condition_document_attechement_hidden'][$key];
                }
            }else{
                $comm_arr[$key]['condition_document_attechement'] = $data['condition_document_attechement_hidden'][$key];
            }

        }

        // dd($comm_arr);
        DB::table('tender_condition_contract')->where('tender_id',$id)->delete();

        $check = DB::table('tender_condition_contract')->where('tender_id',$id)->insert($comm_arr);
        if($check)
        {
            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender condition contract updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function delete_condition_document(Request $request){
        DB::table('tender_condition_contract')->where('id',$request->get('id'))->delete();
    }

    public function change_condition_file(Request $request){
        $id = $request->get('id');

        $tech_image = $request->file('file_img');
        $file_path = $tech_image->store('public/tender_image');
        $tech_arr = $file_path;
        DB::table('tender_condition_contract')->where('id',$id)->update(['condition_document_attechement' => $tech_arr]);
    }

    public function downloadtechdoc($id){
        $file = Tender_technical_eligibility::whereId($id)->first();
        if($file->document_attechement){
            $isExists = Storage::exists($file->document_attechement);
            if($isExists){
                return Storage::download($file->document_attechement);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadfinadoc($id){
        $file = Tender_financial_eligibility::whereId($id)->first();
        if($file->document_attechement){
            $isExists = Storage::exists($file->document_attechement);

            if($isExists){
                return Storage::download($file->document_attechement);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadbiddoc($id){
        $file = Tender_pre_bid_document::whereId($id)->first();
        if($file->query_point_document_attechment){
            $isExists = Storage::exists($file->query_point_document_attechment);

            if($isExists){
                return Storage::download($file->query_point_document_attechment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadcommdoc($id){
        $file = Tender_other_communication::whereId($id)->first();
        if($file->communication_document_attechement){
            $isExists = Storage::exists($file->communication_document_attechement);

            if($isExists){
                return Storage::download($file->communication_document_attechement);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadcondoc($id){
        $file = Tender_condition_contract::whereId($id)->first();
        if($file->condition_document_attechement){
            $isExists = Storage::exists($file->condition_document_attechement);

            if($isExists){
                return Storage::download($file->condition_document_attechement);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function pre_bid_query_report(){
        $this->data['page_title']='Pre-Bid Query Tender Reports';
        return view('admin.tender.pre_bid_query_report', $this->data);
    }

    public function get_prebid_query_tender_list(){
        $datatable_fields = array('tender.id','tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work','tender.state_name_work_execute','tender.estimate_cost','tender.joint_venture','tender.joint_venture_count','tender.quote_type','tender.other_quote_type','tender_pattern.tender_pattern_name','tender_category.tender_category','tender.last_date_time_download','tender.last_date_time_online_submit','tender.last_date_time_physical_submit','tender.assign_tender','fullname');
        $request = Input::all();
        $conditions_array = ['tender_status' => 'Selected','pre_bid_meeting' => 'Yes'];

        $getfiled =array('tender.id','tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work','tender.state_name_work_execute','tender.estimate_cost','tender.joint_venture','tender.joint_venture_count','tender.quote_type','tender.other_quote_type','tender_pattern.tender_pattern_name','tender_category.tender_category','tender.last_date_time_download','tender.last_date_time_online_submit','tender.last_date_time_physical_submit','tender.assign_tender',\DB::raw("GROUP_CONCAT(users.name) as fullname"));

        $table = "tender";
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'department';
        $join_str[0]['join_table_id'] = 'department.id';
        $join_str[0]['from_table_id'] = 'tender.department_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'tender_category';
        $join_str[1]['join_table_id'] = 'tender_category.id';
        $join_str[1]['from_table_id'] = 'tender.tender_category_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'tender_pattern';
        $join_str[2]['join_table_id'] = 'tender_pattern.id';
        $join_str[2]['from_table_id'] = 'tender.tender_pattern';

        if(Auth::user()->id==$this->data['edit_tender_permission'] || Auth::user()->role==config('constants.SuperUser')){
            $where_raw="";
        }
        else{
            $logged_in_user_id= Auth::user()->id;
        $where_raw="FIND_IN_SET($logged_in_user_id,tender.assign_tender)";
        }

        echo Tender::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str,[],$where_raw);
        die();
    }

    public function edit_prebid_query_tender($id){
        $tender = Tender::select('tender.*','tender_client_detail.*')->Leftjoin('tender_client_detail', 'tender.id', '=', 'tender_client_detail.tender_id')->where('tender.id',$id)->first();

        $this->data['page_title']='Edit Prebid Query Tender';
        $this->data['tender'] = $tender;
        $this->data['tender_id'] = $id;

        $this->data['department'] = Department::orderBy('dept_name')->pluck('dept_name','id');
        $this->data['tendercategory'] = TenderCategory::whereStatus('Enabled')->orderBy('tender_category')->pluck('tender_category','id');
        $this->data['user'] = User::whereStatus('Enabled')->pluck('name','id');
        $this->data['tenderpattern'] = TenderPattern::whereStatus('Enabled')->orderBy('tender_pattern_name')->pluck('tender_pattern_name','id');
        $this->data['tender_authority_contact_detail'] = DB::table('tender_authority_contact_detail')->whereTenderId($id)->get()->toArray();
        $this->data['tender_authority_contact_detail_count'] = count($this->data['tender_authority_contact_detail']);
        $this->data['tender_bid_meet'] = Tender_pre_bid_document::whereTenderId($id)->get()->toArray();
        $this->data['tender_bid_meet_count'] = count($this->data['tender_bid_meet']);
        $this->data['companies'] = Companies::whereStatus('Enabled')->orderBy('company_name')->pluck('company_name', 'id');
        // dd($this->data);
        return view('admin.tender.edit_prebid_query_tender', $this->data);
    }

    public function tender_pre_bid_meet_query_point(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $tech_arr = [];
        $data = $request->all();

        foreach ($data['query_point_document_name'] as $key => $value) {
            $tech_arr[$key]['tender_id'] = $id;
            $tech_arr[$key]['query_point_document_name'] = $value;
            $tech_arr[$key]['name_of_section'] = $data['name_of_section'][$key];
            $tech_arr[$key]['clause_number'] = $data['clause_number'][$key];
            $tech_arr[$key]['sub_clause_number'] = $data['sub_clause_number'][$key];
            $tech_arr[$key]['page_number'] = $data['page_number'][$key];
            $tech_arr[$key]['created_ip'] = $request->ip();;
            $tech_arr[$key]['updated_ip'] = $request->ip();;


            if(isset($data['query_point_document_attechment'][$key]) && !empty($data['query_point_document_attechment'][$key])){

                if($request->hasFile('query_point_document_attechment')){
                    $tech_image = $request->file('query_point_document_attechment');
                    foreach ($tech_image as $key1 => $image){
                        $file_path[$key1] = $image->store('public/tender_image');
                        if ($file_path[$key1]) {
                                $tech_arr[$key]['query_point_document_attechment'] = $file_path[$key];
                        }
                    }
                }else{
                    $tech_arr[$key]['query_point_document_attechment'] = $data['query_point_document_attechment_hidden'][$key];
                }
            }else{
                $tech_arr[$key]['query_point_document_attechment'] = $data['query_point_document_attechment_hidden'][$key];
            }
        }
        // dd($tech_arr);
        DB::table('tender_pre_bid_document')->where('tender_id',$id)->delete();

        $check = DB::table('tender_pre_bid_document')->insert($tech_arr);
        if($check)
        {
            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender pre-bid meet query point updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function get_pre_bid_query(Request $request){
        $data = Tender_pre_bid_document::whereTenderId($request->get('id'))->pluck('query_point_document_name','id');
        if(count($data)){
            ?>
            <option value="">Select Query</option>
            <?php
            foreach ($data as $key => $value) {
                ?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php
            }
        }else{
            ?>
            <option value="">Query Not Found</option>
            <?php
        }
    }

    public function get_corrigendum_list(Request $request){
        $datatable_fields = array('id','corrigendum_number','corrigendum_date','corrigendum_sr_number','corrigendum_answer');
        $request = Input::all();
        $conditions_array = ['tender_id' => $_POST['id']];

        $getfiled =array('id','corrigendum_number','corrigendum_date','corrigendum_sr_number','corrigendum_answer');

        $table = "tender_corrigendum";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, []);
        die();
    }

    public function save_tender_corrigendum(Request $request){
        // dd($request->all());
        $corrigendum_attechement_img = $request->file('corrigendum_attechement');
        $file_path = $corrigendum_attechement_img->store('public/tender_image');
        $corrigendum_attechement = $file_path;

        $arr = [
            'tender_id' => $request->get('id'),
            'pre_bid_query_id' => $request->get('pre_bid_query_id'),
            'corrigendum_number' => $request->get('corrigendum_number'),
            'corrigendum_date' => $this->dateTimeConvert($request->get('corrigendum_date')),
            'corrigendum_sr_number' => $request->get('corrigendum_sr_number'),
            'corrigendum_answer' => $request->get('corrigendum_answer'),
            'corrigendum_attechement' => $corrigendum_attechement,
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
        ];

        if(TenderCorrigendum::insert($arr)){

            // User Action Log
            $tender_sr_no = Tender::where('id',$request->get('id'))->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender corrigendum updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function downloadcorrigendumdoc($id){
        $file = TenderCorrigendum::whereId($id)->first();
        if($file->corrigendum_attechement){
            $isExists = Storage::exists($file->corrigendum_attechement);

            if($isExists){
                return Storage::download($file->corrigendum_attechement);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function tender_opening_report(){
        $this->data['page_title']='Tender Opening Reports';
        return view('admin.tender.tender_opening_report', $this->data);
    }

    public function save_opening_datetime(Request $request){
        // dd($request->all());
        if($request->get('form_name') == "preliminary"){
            Tender::whereId($request->get('opening_status_preliminary_datetime_id'))->update(['opening_status_preliminary_datetime'=> $this->dateTimeConvert($request->get('opening_status_preliminary_datetime'))]);
        }elseif ($request->get('form_name') == "technical") {
            Tender::whereId($request->get('opening_status_technical_datetime_id'))->update(['opening_status_technical_datetime'=> $this->dateTimeConvert($request->get('opening_status_technical_datetime'))]);
        }elseif ($request->get('form_name') == "financial") {
            Tender::whereId($request->get('opening_status_financial_datetime_id'))->update(['opening_status_financial_datetime'=> $this->dateTimeConvert($request->get('opening_status_financial_datetime'))]);
        }elseif ($request->get('form_name') == "commercial") {
            Tender::whereId($request->get('opening_status_commercial_datetime_id'))->update(['opening_status_commercial_datetime'=> $this->dateTimeConvert($request->get('opening_status_commercial_datetime'))]);
        }
    }

    public function get_opening_date(Request $request){
        // dd($request->all());
        $data = Tender::select('opening_status_preliminary_datetime','opening_status_technical_datetime','opening_status_financial_datetime','opening_status_commercial_datetime')->whereId($request->get('id'))->first();
        if($data->opening_status_preliminary_datetime){

            $data->opening_status_preliminary_datetime = date('d-m-Y H:i a',strtotime($data->opening_status_preliminary_datetime));
        }

        if($data->opening_status_technical_datetime){

            $data->opening_status_technical_datetime = date('d-m-Y H:i a',strtotime($data->opening_status_technical_datetime));
        }

        if($data->opening_status_financial_datetime){

            $data->opening_status_financial_datetime = date('d-m-Y H:i a',strtotime($data->opening_status_financial_datetime));
        }

        if($data->opening_status_commercial_datetime){

            $data->opening_status_commercial_datetime = date('d-m-Y H:i a',strtotime($data->opening_status_commercial_datetime));
        }
        echo json_encode($data);
    }

    public function get_opening_tender_list(){
        $datatable_fields = array('tender.id','tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no');
        $request = Input::all();

        $conditions_array = [['tender.tender_status' ,'=', 'Selected'],['tender.submission_status', 1]];

        $getfiled =array('tender.id','tender.tender_sr_no','department.dept_name','tender.tender_id_per_portal','tender.portal_name','tender.tender_no','tender.name_of_work');

        $table = "tender";
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'department';
        $join_str[0]['join_table_id'] = 'department.id';
        $join_str[0]['from_table_id'] = 'tender.department_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'tender_category';
        $join_str[1]['join_table_id'] = 'tender_category.id';
        $join_str[1]['from_table_id'] = 'tender.tender_category_id';

        if(Auth::user()->id==$this->data['edit_tender_permission'] || Auth::user()->role==config('constants.SuperUser')){
            $where_raw="";
        }
        else{
            $logged_in_user_id= Auth::user()->id;
        $where_raw="FIND_IN_SET($logged_in_user_id,tender.assign_tender)";
        }

        echo Tender::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str,[],$where_raw);
        die();
    }

    public function edit_tender_opening_report($id){

        $tender = Tender::where('tender.id',$id)->first();
        $this->data['page_title']='Tender Opening Report';
        $this->data['tender'] = $tender;
        $this->data['tender_id'] = $id;
        $this->data['tender_participated_bidder'] = DB::table('tender_participated_bidder')->whereTenderId($id)->get()->toArray();
        $this->data['tender_participated_bidder_count'] = count($this->data['tender_participated_bidder']);
        $this->data['tender_opening_status_technical'] = DB::table('tender_opening_status_technical')->whereTenderId($id)->get()->toArray();
        $this->data['tender_opening_status_technical_count'] = count($this->data['tender_opening_status_technical']);
        $this->data['tender_opening_status_financial'] = DB::table('tender_opening_status_financial')->whereTenderId($id)->get()->toArray();
        $this->data['tender_opening_status_financial_count'] = count($this->data['tender_opening_status_financial']);
        // dd($this->data);
        return view('admin.tender.edit_tender_opening_report', $this->data);
    }

    public function save_participated_bidder(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        $data = $request->all();

        $arr = [];
        $arr_log = [];

        foreach ($data['bidder_name'] as $key => $value) {

            if(isset($data['bidder_id'][$key]) && !empty($data['bidder_id'][$key])){
                $arr['tender_id'] = $id;
                $arr['bidder_name'] = $value;
                $arr['bidder_address'] = $data['bidder_address'][$key];
                $arr['bidder_contact_no'] = $data['bidder_contact_no'][$key];
                $arr['updated_ip'] = $request->ip();

                $arr_log['bidder_name'] = $value;
                $arr_log['bidder_address'] = $data['bidder_address'][$key];
                $arr_log['bidder_contact_no'] = $data['bidder_contact_no'][$key];
                $arr_log['updated_ip'] = $request->ip();

                // Tender_participated_bidder_log::where('bidder_name',$value)->delete();
                // Tender_participated_bidder_log::insert($arr_log);
                DB::table('tender_participated_bidder')->whereId($data['bidder_id'][$key])->update($arr);
            }else{
                $arr['tender_id'] = $id;
                $arr['bidder_name'] = $value;
                $arr['bidder_address'] = $data['bidder_address'][$key];
                $arr['bidder_contact_no'] = $data['bidder_contact_no'][$key];
                $arr['created_ip'] = $request->ip();
                $arr['updated_ip'] = $request->ip();

                $arr_log['bidder_name'] = $value;
                $arr_log['bidder_address'] = $data['bidder_address'][$key];
                $arr_log['bidder_contact_no'] = $data['bidder_contact_no'][$key];
                $arr_log['created_ip'] = $request->ip();
                $arr_log['updated_ip'] = $request->ip();

                Tender_participated_bidder_log::where('bidder_name',$value)->delete();
                Tender_participated_bidder_log::insert($arr_log);
                DB::table('tender_participated_bidder')->insert($arr);
            }


        }

        // User Action Log
        $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Tender Sr No. ".$tender_sr_no." participated bidder updated",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        echo "success";die();

        // dd($arr);
        // DB::table('tender_participated_bidder')->whereTenderId($id)->delete();
        /*if(DB::table('tender_participated_bidder')->insert($arr))
        {
            echo "success";die();
        }else{
            echo "error";die();
        }*/
    }

    public function delete_participated_bidder(Request $request){
        $id = $request->get('id');
        Tender_boq_bidder::where('bidder_id',$id)->delete();
        DB::table('tender_participated_bidder')->whereId($id)->delete();
    }

    public function get_bidder_log(Request $request){
        // dd($request->all());
        $bidder = Tender_participated_bidder_log::where('bidder_name', 'like', '%' . $request->get('search') . '%')->get()->toArray();
        echo json_encode($bidder);
/*        $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
      foreach($bidder as $key => $row)
      {
       $output .= '
       <li><a href="javascript:void(0)" id="'.$row['id'].'" onclick="getBidderData(this.id)">'.$row['bidder_name'].'</a></li>
       ';
      }
      $output .= '</ul>';
      echo $output;*/
    }

    public function get_bidder_log_detail(Request $request){
        $bidder = Tender_participated_bidder_log::whereId($request->get('id'))->first();
        echo json_encode($bidder);
    }

    public function save_opening_status(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        if($request->get('form_name') == 'fee'){

            if($request->get('tender_opening_fee_status') == "Reject"){
                if($request->hasFile('tender_opening_fee_reject_attachment')){
                    $tender_opening_fee_reject_attachment_img = $request->file('tender_opening_fee_reject_attachment');
                    $file_path = $tender_opening_fee_reject_attachment_img->store('public/tender_image');
                    $tender_opening_fee_reject_attachment = $file_path;
                }else{
                    $tender_opening_fee_reject_attachment = $request->get('tender_opening_fee_reject_attachment_hidden');
                }
                $tender_opening_fee_reject_reason = $request->get('tender_opening_fee_reject_reason');
            }else{
                $tender_opening_fee_reject_attachment = Null;
                $tender_opening_fee_reject_reason = Null;
            }

            $arr = [
                'tender_opening_fee_status' => $request->get('tender_opening_fee_status'),
                'tender_opening_fee_reject_reason' => $tender_opening_fee_reject_reason,
                'tender_opening_fee_reject_attachment' => $tender_opening_fee_reject_attachment,
                'updated_ip' => $request->ip(),
            ];
            // dd($arr);
        }else{

            if($request->get('tender_opening_emd_status') == "Reject"){
                if($request->hasFile('tender_opening_emd_reject_attachment')){
                    $tender_opening_emd_reject_attachment_img = $request->file('tender_opening_emd_reject_attachment');
                    $file_path = $tender_opening_emd_reject_attachment_img->store('public/tender_image');
                    $tender_opening_emd_reject_attachment = $file_path;
                }else{
                    $tender_opening_emd_reject_attachment = $request->get('tender_opening_emd_reject_attachment_hidden');
                }
                $tender_opening_emd_reject_reason = $request->get('tender_opening_emd_reject_reason');
            }else{
                $tender_opening_emd_reject_attachment = Null;
                $tender_opening_emd_reject_reason = Null;
            }

            $arr = [
                'tender_opening_emd_status' => $request->get('tender_opening_emd_status'),
                'tender_opening_release_date' => $this->dateTimeConvert($request->get('tender_opening_release_date')),
                'tender_opening_emd_reject_reason' => $tender_opening_emd_reject_reason,
                'tender_opening_emd_reject_attachment' => $tender_opening_emd_reject_attachment,
                'updated_ip' => $request->ip(),
            ];
            // dd($arr);
        }

        // DB::enableQueryLog();
        if(Tender::where('id',$id)->update($arr)){

            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." tender ".$request->get('form_name')." opening status updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
        // $query = DB::getQueryLog();
        // print_r($query);
    }

    public function downloadFeeRejectDoc($id){
        $file = Tender::whereId($id)->first();
        if($file->tender_opening_fee_reject_attachment){
            $isExists = Storage::exists($file->tender_opening_fee_reject_attachment);
            if($isExists){
                return Storage::download($file->tender_opening_fee_reject_attachment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadEmdRejectDoc($id){
        $file = Tender::whereId($id)->first();
        if($file->tender_opening_emd_reject_attachment){
            $isExists = Storage::exists($file->tender_opening_emd_reject_attachment);
            if($isExists){
                return Storage::download($file->tender_opening_emd_reject_attachment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function tender_opening_query_tech(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        if($request->get('open_status_tech') == "Reject"){
            if($request->hasFile('open_status_tech_reject_attachment')){
                $open_status_tech_reject_attachment_img = $request->file('open_status_tech_reject_attachment');
                $file_path = $open_status_tech_reject_attachment_img->store('public/tender_image');
                $open_status_tech_reject_attachment = $file_path;
            }else{
                $open_status_tech_reject_attachment = $request->get('open_status_tech_reject_attachment_hidden');
            }
            $open_status_tech_reject_reason = $request->get('open_status_tech_reject_reason');
        }else{
            $open_status_tech_reject_attachment = Null;
            $open_status_tech_reject_reason = Null;
        }

        $tender_arr = [
            'open_query_status_tech' => $request->get('open_query_status_tech'),
            'open_status_tech' => $request->get('open_status_tech'),
            'open_status_tech_reject_reason' => $open_status_tech_reject_reason,
            'open_status_tech_reject_attachment' => $open_status_tech_reject_attachment,
            'updated_ip' => $request->ip(),
        ];

        // dd($tender_arr);
        if(Tender::whereId($id)->update($tender_arr)){
            $data = $request->all();
            if($request->get('open_query_status_tech') == "Yes"){

                foreach ($data['query_detail_tech'] as $key => $value) {
                $tech_arr[$key]['tender_id'] = $id;
                $tech_arr[$key]['query_detail_tech'] = $value;
                $tech_arr[$key]['query_receive_date_tech'] = $this->dateTimeConvert($data['query_receive_date_tech'][$key]);
                $tech_arr[$key]['query_reply_tech'] = $data['query_reply_tech'][$key];
                $tech_arr[$key]['query_sub_date_tech'] = $this->dateTimeConvert($data['query_sub_date_tech'][$key]);
                $tech_arr[$key]['created_ip'] = $request->ip();
                $tech_arr[$key]['updated_ip'] = $request->ip();

                if(isset($data['query_document_tech'][$key]) && !empty($data['query_document_tech'][$key])){

                    if($request->hasFile('query_document_tech')){
                    $tech_image = $request->file('query_document_tech');
                        foreach ($tech_image as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr[$key]['query_document_tech'] = $file_path[$key];
                            }
                        }
                    }else{
                        $tech_arr[$key]['query_document_tech'] = $data['query_document_tech_hidden'][$key];
                    }
                }else{
                    $tech_arr[$key]['query_document_tech'] = $data['query_document_tech_hidden'][$key];
                }

                if(isset($data['query_reply_document_tech'][$key]) && !empty($data['query_reply_document_tech'][$key])){

                    if($request->file('query_reply_document_tech')){
                        $tech_image_reply = $request->file('query_reply_document_tech');
                        foreach ($tech_image_reply as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr[$key]['query_reply_document_tech'] = $file_path[$key];
                            }
                        }
                    }else{
                        $tech_arr[$key]['query_reply_document_tech'] = $data['query_reply_document_tech_hidden'][$key];
                    }
                }else{
                    $tech_arr[$key]['query_reply_document_tech'] = $data['query_reply_document_tech_hidden'][$key];
                }
            }
            // dd($tech_arr);
            DB::table('tender_opening_status_technical')->where('tender_id',$id)->delete();

            $check = DB::table('tender_opening_status_technical')->insert($tech_arr);
            }

            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." opening status of technical part updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }

    }

    public function downloadTechRejectDoc($id){
     $file = Tender::whereId($id)->first();
        if($file->open_status_tech_reject_attachment){
            $isExists = Storage::exists($file->open_status_tech_reject_attachment);
            if($isExists){
                return Storage::download($file->open_status_tech_reject_attachment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadtechQDdoc($id){
        $file = DB::table('tender_opening_status_technical')->whereId($id)->first();
        if($file->query_document_tech){
            $isExists = Storage::exists($file->query_document_tech);
            if($isExists){
                return Storage::download($file->query_document_tech);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadtechQRdoc($id){
        $file = DB::table('tender_opening_status_technical')->whereId($id)->first();
        if($file->query_reply_document_tech){
            $isExists = Storage::exists($file->query_reply_document_tech);
            if($isExists){
                return Storage::download($file->query_reply_document_tech);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function tender_opening_query_fina(Request $request){
        // dd($request->all());
        $id = $request->get('id');

        if($request->get('open_status_fina') == "Reject"){
            if($request->hasFile('open_status_fina_reject_attachment')){
                $open_status_fina_reject_attachment_img = $request->file('open_status_fina_reject_attachment');
                $file_path = $open_status_fina_reject_attachment_img->store('public/tender_image');
                $open_status_fina_reject_attachment = $file_path;
            }else{
                $open_status_fina_reject_attachment = $request->get('open_status_fina_reject_attachment_hidden');
            }
            $open_status_fina_reject_reason = $request->get('open_status_fina_reject_reason');
        }else{
            $open_status_fina_reject_attachment = Null;
            $open_status_fina_reject_reason = Null;
        }

        $tender_arr = [
            'open_query_status_fina' => $request->get('open_query_status_fina'),
            'open_status_fina' => $request->get('open_status_fina'),
            'open_status_fina_reject_reason' => $open_status_fina_reject_reason,
            'open_status_fina_reject_attachment' => $open_status_fina_reject_attachment,
            'updated_ip' => $request->ip(),
        ];
        // dd($tender_arr);
        if(Tender::whereId($id)->update($tender_arr)){
            $data = $request->all();
            if($request->get('open_query_status_fina') == "Yes"){

                foreach ($data['query_detail_fina'] as $key => $value) {
                $tech_arr[$key]['tender_id'] = $id;
                $tech_arr[$key]['query_detail_fina'] = $value;
                $tech_arr[$key]['query_receive_date_fina'] = $this->dateTimeConvert($data['query_receive_date_fina'][$key]);
                $tech_arr[$key]['query_reply_fina'] = $data['query_reply_fina'][$key];
                $tech_arr[$key]['query_sub_date_fina'] = $this->dateTimeConvert($data['query_sub_date_fina'][$key]);
                $tech_arr[$key]['created_ip'] = $request->ip();
                $tech_arr[$key]['updated_ip'] = $request->ip();


                if(isset($data['query_document_fina'][$key]) && !empty($data['query_document_fina'][$key])){

                    if($request->hasFile('query_document_fina')){
                        $tech_image = $request->file('query_document_fina');
                        foreach ($tech_image as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr[$key]['query_document_fina'] = $file_path[$key];
                            }
                        }
                    }else{
                        $tech_arr[$key]['query_document_fina'] = $data['query_document_fina_hidden'][$key];
                    }
                }else{
                    $tech_arr[$key]['query_document_fina'] = $data['query_document_fina_hidden'][$key];
                }
                /*$tech_image = $request->file('query_document_fina');
                foreach ($tech_image as $key1 => $image){
                    $file_path[$key1] = $image->store('public/tender_image');
                    if ($file_path[$key1]) {
                            $tech_arr[$key]['query_document_fina'] = $file_path[$key];
                    }
                }*/

                if(isset($data['query_reply_document_fina'][$key]) && !empty($data['query_reply_document_fina'][$key])){

                    if($request->hasFile('query_reply_document_fina')){
                        $tech_image_reply = $request->file('query_reply_document_fina');
                        foreach ($tech_image_reply as $key1 => $image){
                            $file_path[$key1] = $image->store('public/tender_image');
                            if ($file_path[$key1]) {
                                    $tech_arr[$key]['query_reply_document_fina'] = $file_path[$key];
                            }
                        }
                    }else{
                        $tech_arr[$key]['query_reply_document_fina'] = $data['query_reply_document_fina_hidden'][$key];
                    }
                }else{
                    $tech_arr[$key]['query_reply_document_fina'] = $data['query_reply_document_fina_hidden'][$key];
                }

                // $tech_image_reply = $request->file('query_reply_document_fina');
                // foreach ($tech_image_reply as $key1 => $image){
                //     $file_path1[$key1] = $image->store('public/tender_image');
                //     if ($file_path1[$key1]) {
                //             $tech_arr[$key]['query_reply_document_fina'] = $file_path1[$key];
                //     }
                // }
            }
            // dd($tech_arr);
            DB::table('tender_opening_status_financial')->where('tender_id',$id)->delete();

            $check = DB::table('tender_opening_status_financial')->insert($tech_arr);
            }

            // User Action Log
            $tender_sr_no = Tender::where('id',$id)->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." opening status of financial part updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function downloadFinaRejectDoc($id){
        $file = Tender::whereId($id)->first();
        if($file->open_status_fina_reject_attachment){
            $isExists = Storage::exists($file->open_status_fina_reject_attachment);
            if($isExists){
                return Storage::download($file->open_status_fina_reject_attachment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadfinaQDdoc($id){
        $file = DB::table('tender_opening_status_financial')->whereId($id)->first();
        if($file->query_document_fina){
            $isExists = Storage::exists($file->query_document_fina);
            if($isExists){
                return Storage::download($file->query_document_fina);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function downloadfinaQRdoc($id){
        $file = DB::table('tender_opening_status_financial')->whereId($id)->first();
        if($file->query_reply_document_fina){
            $isExists = Storage::exists($file->query_reply_document_fina);
            if($isExists){
                return Storage::download($file->query_reply_document_fina);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function save_opening_commercial_status(Request $request){
        // dd($request->all());

        if($request->get('opening_commercial_status') == "Reject"){
                if($request->hasFile('opening_commercial_reject_attachment')){
                    $opening_commercial_reject_attachment_img = $request->file('opening_commercial_reject_attachment');
                    $file_path = $opening_commercial_reject_attachment_img->store('public/tender_image');
                    $opening_commercial_reject_attachment = $file_path;
                }else{
                    $opening_commercial_reject_attachment = $request->get('opening_commercial_reject_attachment_hidden');
                }
                $opening_commercial_reject_reason = $request->get('opening_commercial_reject_reason');
            }else{
                $opening_commercial_reject_attachment = Null;
                $opening_commercial_reject_reason = Null;
            }


        $arr = [
            'opening_commercial_status' => $request->get('opening_commercial_status'),
            'opening_commercial_reject_reason' => $opening_commercial_reject_reason,
            'opening_commercial_reject_attachment' => $opening_commercial_reject_attachment,
            'updated_ip' => $request->ip(),
        ];
        // dd($arr);
        if(Tender::whereId($request->get('id'))->update($arr))
        {
            // User Action Log
            $tender_sr_no = Tender::where('id',$request->get('id'))->value('tender_sr_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Tender Sr No. ".$tender_sr_no." opening status of commercial part updated",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            echo "success";die();
        }else{
            echo "error";die();
        }
    }

    public function downloadCommRejectDoc($id){
        $file = Tender::whereId($id)->first();
        if($file->opening_commercial_reject_attachment){
            $isExists = Storage::exists($file->opening_commercial_reject_attachment);
            if($isExists){
                return Storage::download($file->opening_commercial_reject_attachment);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function get_bidder(Request $request){
        $bidder = Tender_participated_bidder::whereTenderId($request->get('id'))->get()->toArray();

        foreach ($bidder as $key => $value) {
            $bidder[$key]['check_uploade_boq'] = $this->checkBoqUpload($request->get('id'),$value['id']);
        }

        echo json_encode($bidder);
    }

    public function checkBoqUpload($tender_id,$bidder_id){
        $count = Tender_boq_bidder::where('bidder_id',$bidder_id)->where('tender_id',$tender_id)->count();
        if($count){
            return "(Uploaded Data)";
        }else{
            return "";
        }
    }

    public function tender_submission_process(Request $request){
        Tender::whereId($request->get('id'))->update(['submission_status'=>$request->get('status')]);
    }

    public function boqImportData(Request $request){
        // dd($request->all());

        $path = $request->file('opening_upload_file')->getRealPath();
        // dd($path);

        Tender_boq_bidder::where('bidder_id',$_POST['opening_bidder_id'])->where('tender_id',$_POST['id'])->delete();
        $no_record=0;
        $data = (new FastExcel)->import($path, function ($line) use(&$no_record){
            $i = 0;

        if($line['Item No'] != "" && $line['Quantities'] != "" && $line['Unit'] != "" && $line['Item work'] != "" && $line['Estimated Rates'] != "" && $line['Total Amount'] != ""){
            $no_record=1;
            $arr = [
                'tender_id' => $_POST['id'],
                'bidder_id' => $_POST['opening_bidder_id'],
                'item_no' => $line['Item No'],
                'qty' => $line['Quantities'],
                'unit' => $line['Unit'],
                'item_of_work' => $line['Item work'],
                'estimated_rates' => $line['Estimated Rates'],
                'total_amount' => $line['Total Amount'],
                'created_ip' => $_SERVER['REMOTE_ADDR'],
                'updated_ip' => $_SERVER['REMOTE_ADDR'],
            ];
            Tender_boq_bidder::insert($arr);
            // ;
            // print_r($arr);
        } /*else{
            $response = ['status'=>'false','message'=>"Please fill all fields"];
            echo json_encode($response);die();
        }*/


            // $response = ['status'=>'true','message'=>"Your file uploaded successfully"];
        });
        if($no_record==0){
            echo json_encode(['status'=>'false','message'=>"File data format is not correct. please download sample file and fill data in that format."]);die();
        }

        // User Action Log
        $tender_sr_no = Tender::where('id',$_POST['id'])->value('tender_sr_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Tender Sr No. ".$tender_sr_no." tender boq imported",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);
        echo json_encode(['status'=>'true','message'=>"Your file uploaded successfully"]);die();
    }

    public function yourboqImportData(Request $request){
        // dd($request->all());
        $path = $request->file('opening_upload_file')->getRealPath();
        // dd($path);
        Tender_boq_bidder::where('bidder_id',$_POST['company_id'])->where('tender_id',$_POST['id'])->delete();
        $no_record=0;
        $data = (new FastExcel)->import($path, function ($line) use(&$no_record){
            $i = 0;
        // print_r(count($line));
        if($line['Item No'] != "" && $line['Quantities'] != "" && $line['Unit'] != "" && $line['Item work'] != "" && $line['Estimated Rates'] != "" && $line['Total Amount'] != ""){
            $no_record=1;
            $arr = [
                'tender_id' => $_POST['id'],
                'bidder_id' => $_POST['company_id'],
                'item_no' => $line['Item No'],
                'qty' => $line['Quantities'],
                'unit' => $line['Unit'],
                'item_of_work' => $line['Item work'],
                'estimated_rates' => $line['Estimated Rates'],
                'total_amount' => $line['Total Amount'],
                'own_company' => 1,
                'created_ip' => $_SERVER['REMOTE_ADDR'],
                'updated_ip' => $_SERVER['REMOTE_ADDR'],
            ];
            Tender_boq_bidder::insert($arr);
            // ;
            // print_r($arr);
        } /*else{
            $response = ['status'=>'false','message'=>"Please fill all fields"];
            echo json_encode($response);die();
        }*/


            // $response = ['status'=>'true','message'=>"Your file uploaded successfully"];
        });
        if($no_record==0){
            echo json_encode(['status'=>'false','message'=>"File data format is not correct. please download sample file and fill data in that format."]);die();
        }

        // User Action Log
        $tender_sr_no = Tender::where('id',$_POST['id'])->value('tender_sr_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Tender Sr No. ".$tender_sr_no." your boq imported",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        echo json_encode(['status'=>'true','message'=>"Your file uploaded successfully"]);die();
    }

    public function sampleBOQUpload(){
        return Storage::download("boquploadsample.xlsx");
    }

    public function view_compairision_bidder(Request $request){
        // dd($request->all());
        $this->data['items'] = Tender_boq_bidder::whereTenderId($request->get('id'))->groupBy('item_no')->pluck('item_no','id');

        $this->data['bidder'] = Tender_participated_bidder::whereTenderId($request->get('id'))->pluck('bidder_name','id');

        $this->data['total_bidder'] = Tender_boq_bidder::whereTenderId($request->get('id'))->with('getBidderName')->groupBy('bidder_id')->get();

        $bidder_data = Tender_boq_bidder::whereTenderId($request->get('id'))->get()->toArray();

        if($request->get('data_type') == "all_data"){
            $this->data['bidder_item'] = Tender_participated_bidder::whereTenderId($request->get('id'))->with('getBidderItem')->get()->toArray();

            $this->data['company_items'] = Tender_boq_bidder::where('bidder_id',$request->get('company_id'))->where('tender_id',$request->get('id'))->where('own_company',1)->get()->toArray();

            $this->data['bidder_ids'] = [];
            $this->data['item_ids'] = [];

        }else{
            if(!empty($request->get('bidder_ids'))){
            $bidder_ids = $request->get('bidder_ids');
            }else{
                $bidder_ids = "";
            }

            if(!empty($request->get('item_ids'))){
                $item_ids = $request->get('item_ids');
            }else{
                $item_ids = "";
            }

            $this->data['bidder_ids'] = [];
            $tender_bidder = Tender_participated_bidder::whereTenderId($request->get('id'));
                if($bidder_ids){
                    $tender_bidder->whereIn('id',$bidder_ids);
                    $this->data['bidder_ids'] = $request->get('bidder_ids');
                }

            $bidder_item = $tender_bidder->get()->toArray();

            foreach ($bidder_item as $key => $value) {
                $bidder_item[$key]['get_bidder_item'] = $this->getBidItem($value['id'],$item_ids);
            }

            $this->data['bidder_item'] = $bidder_item;

            $this->data['item_ids'] = [];
            if($item_ids){
                $this->data['company_items'] = Tender_boq_bidder::where('bidder_id',$request->get('company_id'))->where('tender_id',$request->get('id'))->where('own_company',1)->whereIn('item_no',$item_ids)->get()->toArray();
                $this->data['item_ids'] = $request->get('item_ids');
            }else{
                $this->data['company_items'] = Tender_boq_bidder::where('bidder_id',$request->get('company_id'))->where('tender_id',$request->get('id'))->where('own_company',1)->get()->toArray();
            }
            // echo "<pre>";
            // print_r($request->get('item_ids'));
            // exit();
        }


        $this->data['company_name'] = Companies::whereId($request->get('company_id'))->first()->company_name;

        // echo "<pre>";
        // print_r($this->data);exit();
        return view('admin.tender.view_compairision_bidder', $this->data);
    }

    public function get_compairision_bidder(Request $request){
        // dd($request->all());

        if(!empty($request->get('bidder_ids'))){
            $bidder_ids = $request->get('bidder_ids');
        }else{
            $bidder_ids = "";
        }

        if(!empty($request->get('item_ids'))){
            $item_ids = $request->get('item_ids');
        }else{
            $item_ids = "";
        }

        $tender_bidder = Tender_participated_bidder::whereTenderId($request->get('id'));
            if($bidder_ids){
                $tender_bidder->whereIn('id',$bidder_ids);
            }

        $bidder_item = $tender_bidder->get()->toArray();

        foreach ($bidder_item as $key => $value) {
            $bidder_item[$key]['get_bidder_item'] = $this->getBidItem($value['id'],$item_ids);
        }
        // dd($bidder_item);

        ?>
        <thead>
                <tr>
                    <th></th>
                    <?php
                    if($bidder_item){
                        foreach($bidder_item as $key => $value){
                                ?><th><?php echo $value['bidder_name'];?></th><?php
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if($bidder_item){
                    foreach($bidder_item[0]['get_bidder_item'] as $key => $value){
                        ?><tr>
                            <td><?php echo $value['item_no'];?></td>
                            <?php
                            foreach($bidder_item as $key1 => $bidder_detail){
                                if(count($bidder_detail['get_bidder_item'])){

                                    ?><td><?php echo $bidder_detail['get_bidder_item'][$key]['total_amount'];?></td><?php
                                }else{
                                ?>
                                <td>NA</td><?php
                                }
                            }
                            ?>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        <?php
    }

    public function getBidItem($bidder,$item){
       $bidder_item = Tender_boq_bidder::select('*');
       if($bidder){
            $bidder_item->where('bidder_id',$bidder);
       }

       if($item){
            $item_new = implode(',', $item);
            $bidder_item->whereIn('item_no',$item);
       }

       $data = $bidder_item->get()->toArray();

       return $data;
    }

    public function get_tender_winner(Request $request){
        $bidders_max = Tender_boq_bidder::whereTenderId($request->get('id'))->where('own_company',0)->groupBy('bidder_id')->select('bidder_id', DB::raw('SUM(total_amount) as sum_amount'))->get()->max();
        $bidders_min = Tender_boq_bidder::whereTenderId($request->get('id'))->where('own_company',0)->groupBy('bidder_id')->select('bidder_id', DB::raw('SUM(total_amount) as sum_amount'))->get()->min();

        // echo "<pre>";
        // print_r($bidders_max);
        // print_r($bidders_min);
        // exit;

        if($bidders_min && $bidders_max){
            $bidders_max = $bidders_max->toArray();
            $bidders_min = $bidders_min->toArray();
            $company = Tender_boq_bidder::where('bidder_id',$request->get('company_id'))->where('own_company',1)->get()->sum('total_amount');

            $company_name = Companies::whereId($request->get('company_id'))->first()->company_name;

            $max_arr = [
                $bidders_max['bidder_id'] => $bidders_max['sum_amount'],
                "Your Company" => "$company",
            ];
            $max_value = max($max_arr);
            $max_key = array_search($max_value, $max_arr);

            if($max_key == "Your Company"){
                $max_amount_name = $company_name;
            }else{
                $bidder_name = Tender_participated_bidder::whereId($max_key)->first();
                $max_amount_name = $bidder_name['bidder_name'];
            }


            $min_arr = [
                $bidders_min['bidder_id'] => $bidders_min['sum_amount'],
                "Your Company" => "$company",
            ];
            $min_value = min($min_arr);
            $min_key = array_search($min_value, $min_arr);

            if($min_key == "Your Company"){
                $min_amount_name = $company_name;
            }else{
                $bidder_name = Tender_participated_bidder::whereId($min_key)->first();
                $min_amount_name = $bidder_name['bidder_name'];
            }

            $response = ['status' => "true", "min_name" => $min_amount_name, "max_name" => $max_amount_name];
        }else{
            $response = ['status' => "false"];
        }

        echo json_encode($response);die();
        /*if($bidders){
            $company = Tender_boq_bidder::where('bidder_id',$request->get('company_id'))->where('own_company',1)->get()->sum('total_amount');
            if($bidders['sum_amount'] > $company['total_amount']){
                $bidder_name = Tender_participated_bidder::whereId($bidders['bidder_id'])->first();
                echo $bidder_name['bidder_name'];
            }else{
                echo "Your Company";
            }
        }else{
            echo "empty";
        }*/

    }

    public function get_techical_criteria(Request $request){
        $tender = Tender_technical_eligibility::whereTenderId($request->get('id'))->get()->toArray();
        echo json_encode($tender);
    }

    public function get_financial_criteria(Request $request){
        $tender = Tender_financial_eligibility::whereTenderId($request->get('id'))->get()->toArray();
        echo json_encode($tender);
    }

    public function get_pre_bid_meeting(Request $request){
        $tender = Tender_pre_bid_document::whereTenderId($request->get('id'))->get()->toArray();
        echo json_encode($tender);
    }

    public function get_other_communication(Request $request){
        $tender = Tender_other_communication::whereTenderId($request->get('id'))->get()->toArray();

        foreach ($tender as $key => $value) {
            $tender[$key]['other_communication_date'] = date('d-m-Y',strtotime($value['other_communication_date']));
        }

        echo json_encode($tender);
    }

    public function get_condition_contract(Request $request){
        $tender = Tender_condition_contract::whereTenderId($request->get('id'))->get()->toArray();
        echo json_encode($tender);
    }

    public function get_opening_technical(Request $request){
        $tender = Tender_opening_status_technical::whereTenderId($request->get('id'))->get()->toArray();
        foreach ($tender as $key => $value) {
            $tender[$key]['query_receive_date_tech'] = date('d-m-Y',strtotime($value['query_receive_date_tech']));
            $tender[$key]['query_sub_date_tech'] = date('d-m-Y h:i a',strtotime($value['query_sub_date_tech']));
        }
        echo json_encode($tender);
    }

    public function get_opening_financial(Request $request){
        $tender = Tender_opening_status_financial::whereTenderId($request->get('id'))->get()->toArray();
        foreach ($tender as $key => $value) {
            $tender[$key]['query_receive_date_fina'] = date('d-m-Y',strtotime($value['query_receive_date_fina']));
            $tender[$key]['query_sub_date_fina'] = date('d-m-Y h:i a',strtotime($value['query_sub_date_fina']));
        }
        echo json_encode($tender);
    }

    public function downloadfinalsubdoc($id){
        $file = Tender::whereId($id)->first();
        if($file->final_sub_file){
            $isExists = Storage::exists($file->final_sub_file);
            if($isExists){
                return Storage::download($file->final_sub_file);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function tender_permission(){
        $this->data['page_title']='Tender Permission';
        $this->data['users']= User::whereStatus("Enabled")->orderBy('name')->pluck('name','id');
        $add_tender = TenderPermission::where('type',"add_tender")->first();
        if($add_tender){
            $this->data['add_user_permission'] = $add_tender->user_id;
        }else{
            $this->data['add_user_permission'] = 0;
        }

        $edit_tender = TenderPermission::where('type',"edit_tender")->first();
        if($edit_tender){
            $this->data['edit_user_permission'] = $edit_tender->user_id;
        }else{
            $this->data['edit_user_permission'] = 0;
        }

        $default_assign_user = TenderPermission::where('type',"default_assign_user")->first();
        if($default_assign_user){
            $this->data['default_assign_user'] = explode(',', $default_assign_user->user_id);
        }else{
            $this->data['default_assign_user'] = [];
        }

        $simple_assign_user = TenderPermission::where('type',"simple_assign_user")->first();
        if($simple_assign_user){
            $this->data['simple_assign_user'] = explode(',', $simple_assign_user->user_id);
        }else{
            $this->data['simple_assign_user'] = [];
        }
        return view('admin.tender.tender_permission', $this->data);
    }

    public function save_tender_permission(Request $request){
        // dd($request->all());
        if($request->get('form_name') == "add_tender"){
            $permission = [
                'user_id' => $request->get('add_tender_permission'),
                'type' => "add_tender",
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            TenderPermission::where('type',"add_tender")->delete();
            if(TenderPermission::insert($permission)){
                echo "success";
            }else{
                echo "error";
            }

        }

        if($request->get('form_name') == "edit_tender"){
            $permission = [
                'user_id' => $request->get('edit_tender_permission'),
                'type' => "edit_tender",
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            TenderPermission::where('type',"edit_tender")->delete();
            if(TenderPermission::insert($permission)){
                echo "success";
            }else{
                echo "error";
            }

        }

        if($request->get('form_name') == "default_assign_user"){
            $permission = [
                'user_id' => implode(',', $request->get('default_tender_user')),
                'type' => "default_assign_user",
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            TenderPermission::where('type',"default_assign_user")->delete();
            if(TenderPermission::insert($permission)){
                echo "success";
            }else{
                echo "error";
            }

        }

        if($request->get('form_name') == "simple_assign_user"){
            $permission = [
                'user_id' => implode(',', $request->get('tender_user')),
                'type' => "simple_assign_user",
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            TenderPermission::where('type',"simple_assign_user")->delete();
            if(TenderPermission::insert($permission)){
                echo "success";
            }else{
                echo "error";
            }

        }
    }

    public function tender_payment_request(Request $request){
        $add_data = [
            'user_id' => auth()->user()->id,
            'tender_id' => $request->get('tender_id'),
            'tender_type' => $request->get('tender_type'),
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
        ];
        // dd($add_data);
        if(TenderPaymentRequest::insert($add_data)){
            echo "success";die;
        }else{
            echo "error";die;
        }
    }
}
