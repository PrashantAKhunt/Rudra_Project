<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tender;
use DB;
use App\AssetImage;
use App\Lib\Permissions;
use App\Tender_technical_eligibility;
use App\Tender_financial_eligibility;
use App\Tender_client_detail;
use App\Tender_pre_bid_document;
use App\Tender_other_communication;
use App\Tender_condition_contract;
use App\Department;
use App\TenderCategory;
use App\User;
use App\TenderPattern;
use App\Tender_physical_submission;
use App\TenderCorrigendum;
use Illuminate\Support\Facades\Storage;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Validator;
use App\Tender_opening_status_financial;
use App\Tender_opening_status_technical;
use App\Tender_participated_bidder;
use App\Tender_submission_commercial;
use App\Tender_submission_financial_part;
use App\Tender_submission_technical_part;
use App\Tender_boq_bidder;
use App\Companies;
use App\TenderPermission;
use App\Lib\UserActionLogs;

class TenderController extends Controller {

    private $page_limit = 20;
    public $common_task;
    public $notification_task;
    public $user_action_logs;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
    }

    public function get_all_tender(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        $add_tender = TenderPermission::where('type',"add_tender")->first();
        if($add_tender){
            $add_tender_permission = $add_tender->user_id;
        }else{
            $add_tender_permission = "";
        }

        if($logged_in_userdata['id'] == $add_tender_permission || $logged_in_userdata['role'] == config('constants.SuperUser') || $logged_in_userdata['role'] == config('constants.Admin')){

        }else{
             return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $tender = Tender::select('tender.*','company.company_name' ,'tender_physical_submission.mode_name as physical_submission_mode_name', 'department.dept_name', 'tender_pattern.tender_pattern_name', 'tender_category.tender_category', \DB::raw("GROUP_CONCAT(users.name) as assign_employee"))
                ->with(['tender_technical_eligibility', 'tender_financial_eligibility',
                    'tender_other_communication', 'tender_condition_contract', 'tender_pre_bid_document','tender_corrigendum','tender_client',
                    'tender_authorites'])
                ->join('department', 'department.id', '=', 'tender.department_id')
                ->join('company','company.id','=','tender.company_id')
                ->leftJoin('tender_physical_submission', 'tender_physical_submission.id', '=', 'tender.physical_sub_mode')
                ->join('tender_category', 'tender_category.id', '=', 'tender.tender_category_id')
                ->join('tender_pattern', 'tender_pattern.id', '=', 'tender.tender_pattern')
                ->leftJoin("users", \DB::raw("FIND_IN_SET(users.id,tender.assign_tender)"), ">", \DB::raw("0"))
                ->groupBy('tender.id')
                ->where('tender_status', 'Pending')
                ->orderBy('id', 'DESC')
                ->offset($offset)
                ->limit($this->page_limit)
                ->get();


        if ($tender->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($tender as $key => $value) {
            if ($value->tender_fee_attechment) {
                $tender[$key]->tender_fee_attechment = asset('storage/' . str_replace('public/', '', $value->tender_fee_attechment));
            } else {
                $tender[$key]->tender_fee_attechment = "";
            }

            if ($value->tender_emd_attechment) {
                $tender[$key]->tender_emd_attechment = asset('storage/' . str_replace('public/', '', $value->tender_emd_attechment));
            } else {
                $tender[$key]->tender_emd_attechment = "";
            }

            if ($value->final_sub_file) {
                $tender[$key]->final_sub_file = asset('storage/' . str_replace('public/', '', $value->final_sub_file));
            } else {
                $tender[$key]->final_sub_file = "";
            }

            foreach ($value->tender_technical_eligibility as $key_te => $value_te) {
                if ($value_te->document_attechement) {
                    $value->tender_technical_eligibility[$key_te]->document_attechement = asset('storage/' . str_replace('public/', '', $value_te->document_attechement));
                } else {
                    $value->tender_technical_eligibility[$key_te]->document_attechement = "";
                }
            }

            foreach ($value->tender_financial_eligibility as $key_te => $value_te) {
                if ($value_te->document_attechement) {
                    $value->tender_financial_eligibility[$key_te]->document_attechement = asset('storage/' . str_replace('public/', '', $value_te->document_attechement));
                } else {
                    $value->tender_financial_eligibility[$key_te]->document_attechement = "";
                }
            }

            foreach ($value->tender_other_communication as $key_te => $value_te) {
                if ($value_te->communication_document_attechement) {
                    $value->tender_other_communication[$key_te]->communication_document_attechement = asset('storage/' . str_replace('public/', '', $value_te->communication_document_attechement));
                } else {
                    $value->tender_other_communication[$key_te]->communication_document_attechement = "";
                }
            }

            foreach ($value->tender_condition_contract as $key_te => $value_te) {
                if ($value_te->condition_document_attechement) {
                    $value->tender_condition_contract[$key_te]->condition_document_attechement = asset('storage/' . str_replace('public/', '', $value_te->condition_document_attechement));
                } else {
                    $value->tender_condition_contract[$key_te]->condition_document_attechement = "";
                }
            }

            foreach ($value->tender_pre_bid_document as $key_te => $value_te) {
                if ($value_te->query_point_document_attechment) {
                    $value->tender_pre_bid_document[$key_te]->query_point_document_attechment = asset('storage/' . str_replace('public/', '', $value_te->query_point_document_attechment));
                } else {
                    $value->tender_pre_bid_document[$key_te]->query_point_document_attechment = "";
                }
            }

            foreach ($value->tender_corrigendum as $key_te => $value_te) {
                if ($value_te->corrigendum_attechement) {
                    $value->tender_corrigendum[$key_te]->corrigendum_attechement = asset('storage/' . str_replace('public/', '', $value_te->corrigendum_attechement));
                } else {
                    $value->tender_corrigendum[$key_te]->corrigendum_attechement = "";
                }
            }

        }

        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $tender]);
    }

    public function get_selected_tender(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        $add_tender = TenderPermission::where('type',"add_tender")->first();
        if($add_tender){
            $add_tender_permission = $add_tender->user_id;
        }else{
            $add_tender_permission = "";
        }

        $edit_tender = TenderPermission::where('type',"edit_tender")->first();
        if($edit_tender){
            $edit_tender_permission = $edit_tender->user_id;
        }else{
            $edit_tender_permission = "";
        }
        if($logged_in_userdata['id'] == $edit_tender_permission || $logged_in_userdata['role'] == config('constants.SuperUser')){
            $logged_in_user_id = "";
            $where_raw = "";
        }else{
            $logged_in_user_id = $logged_in_userdata['id'];
            $where_raw="FIND_IN_SET($logged_in_user_id,tender.assign_tender)";
        }
        // dd($where_raw);
        $tender_query = Tender::select('tender.*','company.company_name' ,'tender_physical_submission.mode_name as physical_submission_mode_name', 'department.dept_name', 'tender_pattern.tender_pattern_name', 'tender_category.tender_category', \DB::raw("GROUP_CONCAT(users.name) as assign_employee"))
                ->with(['tender_technical_eligibility', 'tender_financial_eligibility',
                    'tender_other_communication', 'tender_condition_contract', 'tender_pre_bid_document','tender_corrigendum','tender_client',
                    'tender_authorites'])
                ->join('department', 'department.id', '=', 'tender.department_id')
                ->join('company','company.id','=','tender.company_id')
                ->leftJoin('tender_physical_submission', 'tender_physical_submission.id', '=', 'tender.physical_sub_mode')
                ->join('tender_category', 'tender_category.id', '=', 'tender.tender_category_id')
                ->join('tender_pattern', 'tender_pattern.id', '=', 'tender.tender_pattern')
                ->leftJoin("users", \DB::raw("FIND_IN_SET(users.id,tender.assign_tender)"), ">", \DB::raw("0"))
                ->groupBy('tender.id');
                $tender_query->where('tender.tender_status', 'Selected');
                if($logged_in_user_id){
                $tender_query->whereRaw($where_raw);
                }
            $tender = $tender_query->orderBy('tender.id', 'DESC')
            ->offset($offset)
            ->limit($this->page_limit)
            ->get();
        if ($tender->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($tender as $key => $value) {
            if ($value->tender_fee_attechment) {
                $tender[$key]->tender_fee_attechment = asset('storage/' . str_replace('public/', '', $value->tender_fee_attechment));
            } else {
                $tender[$key]->tender_fee_attechment = "";
            }

            if ($value->tender_emd_attechment) {
                $tender[$key]->tender_emd_attechment = asset('storage/' . str_replace('public/', '', $value->tender_emd_attechment));
            } else {
                $tender[$key]->tender_emd_attechment = "";
            }

            if ($value->final_sub_file) {
                $tender[$key]->final_sub_file = asset('storage/' . str_replace('public/', '', $value->final_sub_file));
            } else {
                $tender[$key]->final_sub_file = "";
            }

            foreach ($value->tender_technical_eligibility as $key_te => $value_te) {
                if ($value_te->document_attechement) {
                    $value->tender_technical_eligibility[$key_te]->document_attechement = asset('storage/' . str_replace('public/', '', $value_te->document_attechement));
                } else {
                    $value->tender_technical_eligibility[$key_te]->document_attechement = "";
                }
            }

            foreach ($value->tender_financial_eligibility as $key_te => $value_te) {
                if ($value_te->document_attechement) {
                    $value->tender_financial_eligibility[$key_te]->document_attechement = asset('storage/' . str_replace('public/', '', $value_te->document_attechement));
                } else {
                    $value->tender_financial_eligibility[$key_te]->document_attechement = "";
                }
            }

            foreach ($value->tender_other_communication as $key_te => $value_te) {
                if ($value_te->communication_document_attechement) {
                    $value->tender_other_communication[$key_te]->communication_document_attechement = asset('storage/' . str_replace('public/', '', $value_te->communication_document_attechement));
                } else {
                    $value->tender_other_communication[$key_te]->communication_document_attechement = "";
                }
            }

            foreach ($value->tender_condition_contract as $key_te => $value_te) {
                if ($value_te->condition_document_attechement) {
                    $value->tender_condition_contract[$key_te]->condition_document_attechement = asset('storage/' . str_replace('public/', '', $value_te->condition_document_attechement));
                } else {
                    $value->tender_condition_contract[$key_te]->condition_document_attechement = "";
                }
            }

            foreach ($value->tender_pre_bid_document as $key_te => $value_te) {
                if ($value_te->query_point_document_attechment) {
                    $value->tender_pre_bid_document[$key_te]->query_point_document_attechment = asset('storage/' . str_replace('public/', '', $value_te->query_point_document_attechment));
                } else {
                    $value->tender_pre_bid_document[$key_te]->query_point_document_attechment = "";
                }
            }

            foreach ($value->tender_corrigendum as $key_te => $value_te) {
                if ($value_te->corrigendum_attechement) {
                    $value->tender_corrigendum[$key_te]->corrigendum_attechement = asset('storage/' . str_replace('public/', '', $value_te->corrigendum_attechement));
                } else {
                    $value->tender_corrigendum[$key_te]->corrigendum_attechement = "";
                }
            }

        }

        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $tender]);
    }

    public function get_submission_tender(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        $add_tender = TenderPermission::where('type',"add_tender")->first();
        if($add_tender){
            $add_tender_permission = $add_tender->user_id;
        }else{
            $add_tender_permission = "";
        }

        $edit_tender = TenderPermission::where('type',"edit_tender")->first();
        if($edit_tender){
            $edit_tender_permission = $edit_tender->user_id;
        }else{
            $edit_tender_permission = "";
        }
        if($logged_in_userdata['id'] == $edit_tender_permission || $logged_in_userdata['role'] == config('constants.SuperUser')){
            $logged_in_user_id = "";
            $where_raw = "";
        }else{
            $logged_in_user_id = $logged_in_userdata['id'];
            $where_raw="FIND_IN_SET($logged_in_user_id,tender.assign_tender)";
        }

        $tender_query = Tender::select('tender.*', 'company.company_name','tender.tender_sr_no', 'department.dept_name', 'tender.tender_id_per_portal', 'tender.portal_name', 'tender.tender_no', 'tender.name_of_work', 'tender.state_name_work_execute', 'tender.estimate_cost', 'tender.joint_venture', 'tender.joint_venture_count', 'tender.quote_type', 'tender.other_quote_type', 'tender_pattern.tender_pattern_name', 'tender_category.tender_category', 'tender.last_date_time_download', 'tender.last_date_time_online_submit', 'tender.last_date_time_physical_submit', \DB::raw("GROUP_CONCAT(users.name) as assign_employee"))->join('department', 'department.id', '=', 'tender.department_id')
                        ->with(['tender_submission_technical_part',
                            'tender_submission_financial_part', 'tender_submission_commercial', 'tender_corrigendum','tender_client',
                            'tender_authorites'])
                        ->join('tender_category', 'tender_category.id', '=', 'tender.tender_category_id')
                        ->join('company','company.id','=','tender.company_id')
                        ->join('tender_pattern', 'tender_pattern.id', '=', 'tender.tender_pattern')
                        ->leftjoin("users", \DB::raw("FIND_IN_SET(users.id,tender.assign_tender)"), ">", \DB::raw("0"))
                        ->groupBy('tender.id')
                        ->where('submission_status', '1')
                        ->where('physical_sub_mode','!=',NULL)
                        ->orderBy('id', 'DESC');
                        if($logged_in_user_id){
                            $tender_query->whereRaw($where_raw);
                        }
                        $tender_query->offset($offset);
                        $tender_query->limit($this->page_limit);
                        $tender = $tender_query->get()->toArray();

        foreach ($tender as $key => $tender_detail) {

            if ($tender_detail['tender_fee_attechment']) {
                $tender[$key]['tender_fee_attechment'] = asset('storage/' . str_replace('public/', '', $tender_detail['tender_fee_attechment']));
            } else {
                $tender[$key]['tender_fee_attechment'] = "";
            }

            if ($tender_detail['tender_emd_attechment']) {
                $tender[$key]['tender_emd_attechment'] = asset('storage/' . str_replace('public/', '', $tender_detail['tender_emd_attechment']));
            } else {
                $tender[$key]['tender_emd_attechment'] = "";
            }

            if ($tender_detail['final_sub_file']) {
                $tender[$key]['final_sub_file'] = asset('storage/' . str_replace('public/', '', $tender_detail['final_sub_file']));
            } else {
                $tender[$key]['final_sub_file'] = "";
            }

            foreach ($tender_detail['tender_submission_technical_part'] as $key_technical => $value_technical) {
                if($value_technical['prepare_document_attechment']){
                    $tender[$key]['tender_submission_technical_part'][$key_technical]['prepare_document_attechment'] = asset('storage/' . str_replace('public/', '', $value_technical['prepare_document_attechment'])) ;
                }else{
                    $tender[$key]['tender_submission_technical_part'][$key_technical]['prepare_document_attechment'] = "";
                }

                if($value_technical['uploaded_document_attechment']){
                    $tender[$key]['tender_submission_technical_part'][$key_technical]['uploaded_document_attechment'] = asset('storage/' . str_replace('public/', '', $value_technical['uploaded_document_attechment'])) ;
                }else{
                    $tender[$key]['tender_submission_technical_part'][$key_technical]['uploaded_document_attechment'] = "";
                }
            }

            foreach ($tender_detail['tender_submission_financial_part'] as $key_technical => $value_technical) {
                if($value_technical['prepare_document_attechment']){
                    $tender[$key]['tender_submission_financial_part'][$key_technical]['prepare_document_attechment'] = asset('storage/' . str_replace('public/', '', $value_technical['prepare_document_attechment'])) ;
                }else{
                    $tender[$key]['tender_submission_financial_part'][$key_technical]['prepare_document_attechment'] = "";
                }

                if($value_technical['uploaded_document_attechment']){
                    $tender[$key]['tender_submission_financial_part'][$key_technical]['uploaded_document_attechment'] = asset('storage/' . str_replace('public/', '', $value_technical['uploaded_document_attechment'])) ;
                }else{
                    $tender[$key]['tender_submission_financial_part'][$key_technical]['uploaded_document_attechment'] = "";
                }
            }

            foreach ($tender_detail['tender_submission_commercial'] as $key_technical => $value_technical) {
                if($value_technical['prepare_document_attechment']){
                    $tender[$key]['tender_submission_commercial'][$key_technical]['prepare_document_attechment'] = asset('storage/' . str_replace('public/', '', $value_technical['prepare_document_attechment'])) ;
                }else{
                    $tender[$key]['tender_submission_commercial'][$key_technical]['prepare_document_attechment'] = "";
                }

                if($value_technical['uploaded_document_attechment']){
                    $tender[$key]['tender_submission_commercial'][$key_technical]['uploaded_document_attechment'] = asset('storage/' . str_replace('public/', '', $value_technical['uploaded_document_attechment'])) ;
                }else{
                    $tender[$key]['tender_submission_commercial'][$key_technical]['uploaded_document_attechment'] = "";
                }
            }

            foreach ($tender_detail['tender_corrigendum'] as $key_technical => $value_technical) {
                if($value_technical['corrigendum_attechement']){
                    $tender[$key]['tender_corrigendum'][$key_technical]['corrigendum_attechement'] = asset('storage/' . str_replace('public/', '', $value_technical['corrigendum_attechement'])) ;
                }else{
                    $tender[$key]['tender_corrigendum'][$key_technical]['corrigendum_attechement'] = "";
                }
            }


            if ($tender_detail['tender_fee_check_complated']) {
                $tender[$key]['preliminary_part']['fee_status'] = "GREEN";
            } else {
                $tender[$key]['preliminary_part']['fee_status'] = "RED";
            }

            if ($tender_detail['tender_emd_check_complated']) {
                $tender[$key]['preliminary_part']['emd_status'] = "GREEN";
            } else {
                $tender[$key]['preliminary_part']['emd_status'] = "RED";
            }

            if (empty($tender_detail['tender_submission_technical_part'])) {
                $tender[$key]['technical_part']['preparation'] = "RED";
                $tender[$key]['technical_part']['upload'] = "RED";
            } else {
                $preparation_complete = 1;
                $upload_complete = 1;
                foreach ($tender_detail['tender_submission_technical_part'] as $technical_part) {
                    if ($technical_part['prepare_document_checked']) {
                        continue;
                    } else {
                        $preparation_complete = 0;
                        break;
                    }
                }
                if ($preparation_complete == 0) {
                    $tender[$key]['technical_part']['preparation'] = "YELLOW";
                    $tender[$key]['technical_part']['upload'] = "RED";
                    $upload_complete = 0;
                } else {
                    $tender[$key]['technical_part']['preparation'] = "GREEN";
                }
                if ($upload_complete == 1) {
                    foreach ($tender_detail['tender_submission_technical_part'] as $technical_part) {
                        if ($technical_part['uploaded_document_checked']) {
                            continue;
                        } else {
                            $upload_complete = 0;
                            break;
                        }
                    }
                    if ($upload_complete == 0) {
                        $tender[$key]['technical_part']['upload'] = "YELLOW";
                    } else {
                        $tender[$key]['technical_part']['upload'] = "GREEN";
                    }
                }
            }

            if (empty($tender_detail['tender_submission_financial_part'])) {
                $tender[$key]['financial_part']['preparation'] = "RED";
                $tender[$key]['financial_part']['upload'] = "RED";
            } else {
                $preparation_complete = 1;
                $upload_complete = 1;
                foreach ($tender_detail['tender_submission_financial_part'] as $technical_part) {
                    if ($technical_part['prepare_document_checked']) {
                        continue;
                    } else {
                        $preparation_complete = 0;
                        break;
                    }
                }
                if ($preparation_complete == 0) {
                    $tender[$key]['financial_part']['preparation'] = "YELLOW";
                    $tender[$key]['financial_part']['upload'] = "RED";
                    $upload_complete = 0;
                } else {
                    $tender[$key]['financial_part']['preparation'] = "GREEN";
                }
                if ($upload_complete == 1) {
                    foreach ($tender_detail['tender_submission_financial_part'] as $technical_part) {
                        if ($technical_part['uploaded_document_checked']) {
                            continue;
                        } else {
                            $upload_complete = 0;
                            break;
                        }
                    }
                    if ($upload_complete == 0) {
                        $tender[$key]['financial_part']['upload'] = "YELLOW";
                    } else {
                        $tender[$key]['financial_part']['upload'] = "GREEN";
                    }
                }
            }

            if (empty($tender_detail['tender_submission_commercial'])) {
                $tender[$key]['commercial_part']['preparation'] = "RED";
                $tender[$key]['commercial_part']['upload'] = "RED";
            } else {
                $preparation_complete = 1;
                $upload_complete = 1;
                foreach ($tender_detail['tender_submission_commercial'] as $technical_part) {
                    if ($technical_part['prepare_document_checked']) {
                        continue;
                    } else {
                        $preparation_complete = 0;
                        break;
                    }
                }
                if ($preparation_complete == 0) {
                    $tender[$key]['commercial_part']['preparation'] = "YELLOW";
                    $tender[$key]['commercial_part']['upload'] = "RED";
                    $upload_complete = 0;
                } else {
                    $tender[$key]['commercial_part']['preparation'] = "GREEN";
                }
                if ($upload_complete == 1) {
                    foreach ($tender_detail['tender_submission_commercial'] as $technical_part) {
                        if ($technical_part['uploaded_document_checked']) {
                            continue;
                        } else {
                            $upload_complete = 0;
                            break;
                        }
                    }
                    if ($upload_complete == 0) {
                        $tender[$key]['commercial_part']['upload'] = "YELLOW";
                    } else {
                        $tender[$key]['commercial_part']['upload'] = "GREEN";
                    }
                }
            }
        }

        if (count($tender) == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $tender]);
    }

    public function get_opening_tender(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        $add_tender = TenderPermission::where('type',"add_tender")->first();
        if($add_tender){
            $add_tender_permission = $add_tender->user_id;
        }else{
            $add_tender_permission = "";
        }

        $edit_tender = TenderPermission::where('type',"edit_tender")->first();
        if($edit_tender){
            $edit_tender_permission = $edit_tender->user_id;
        }else{
            $edit_tender_permission = "";
        }
        if($logged_in_userdata['id'] == $edit_tender_permission || $logged_in_userdata['role'] == config('constants.SuperUser')){
            $logged_in_user_id = "";
            $where_raw = "";
        }else{
            $logged_in_user_id = $logged_in_userdata['id'];
            $where_raw="FIND_IN_SET($logged_in_user_id,tender.assign_tender)";
        }

        $tender_query = Tender::select('tender.*','company.company_name' ,'tender.id', 'tender.tender_sr_no', 'department.dept_name', 'tender.tender_id_per_portal', 'tender.portal_name', 'tender.tender_no', 'tender.name_of_work', 'tender.state_name_work_execute', 'tender.estimate_cost', 'tender.joint_venture', 'tender.joint_venture_count', 'tender.quote_type', 'tender.other_quote_type', 'tender_pattern.tender_pattern_name', 'tender_category.tender_category', 'tender.last_date_time_download', 'tender.last_date_time_online_submit', 'tender.last_date_time_physical_submit', \DB::raw("GROUP_CONCAT(users.name) as assign_employee"))->join('department', 'department.id', '=', 'tender.department_id')
                        ->with(['tender_participated_bidder', 'tender_opening_status_technical', 'tender_opening_status_financial','tender_corrigendum' ,'tender_client', 'tender_authorites'])
                        ->join('tender_category', 'tender_category.id', '=', 'tender.tender_category_id')
                        ->join('tender_pattern', 'tender_pattern.id', '=', 'tender.tender_pattern')
                ->join('company','company.id','=','tender.company_id')
                        ->leftjoin("users", \DB::raw("FIND_IN_SET(users.id,tender.assign_tender)"), ">", \DB::raw("0"))
                        ->groupBy('tender.id')
                        ->where('tender_status', 'Selected')
                        ->where('submission_status', 1)
                        ->orderBy('id', 'DESC');
                        if($logged_in_user_id){
                            $tender_query->whereRaw($where_raw);
                        }
                        $tender_query->offset($offset);
                        $tender_query->limit($this->page_limit);
                        $tender = $tender_query->get()->toArray();

        if (count($tender) == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($tender as $key => $tender_detail) {

            if ($tender_detail['tender_fee_attechment']) {
                $tender[$key]['tender_fee_attechment'] = asset('storage/' . str_replace('public/', '', $tender_detail['tender_fee_attechment']));
            } else {
                $tender[$key]['tender_fee_attechment'] = "";
            }

            if ($tender_detail['tender_emd_attechment']) {
                $tender[$key]['tender_emd_attechment'] = asset('storage/' . str_replace('public/', '', $tender_detail['tender_emd_attechment']));
            } else {
                $tender[$key]['tender_emd_attechment'] = "";
            }

            if ($tender_detail['final_sub_file']) {
                $tender[$key]['final_sub_file'] = asset('storage/' . str_replace('public/', '', $tender_detail['final_sub_file']));
            } else {
                $tender[$key]['final_sub_file'] = "";
            }

            if ($tender_detail['tender_opening_fee_reject_attachment']) {
                $tender[$key]['tender_opening_fee_reject_attachment'] = asset('storage/' . str_replace('public/', '', $tender_detail['tender_opening_fee_reject_attachment']));
            } else {
                $tender[$key]['tender_opening_fee_reject_attachment'] = "";
            }

            if ($tender_detail['tender_opening_emd_reject_attachment']) {
                $tender[$key]['tender_opening_emd_reject_attachment'] = asset('storage/' . str_replace('public/', '', $tender_detail['tender_opening_emd_reject_attachment']));
            } else {
                $tender[$key]['tender_opening_emd_reject_attachment'] = "";
            }

            if ($tender_detail['open_status_tech_reject_attachment']) {
                $tender[$key]['open_status_tech_reject_attachment'] = asset('storage/' . str_replace('public/', '', $tender_detail['open_status_tech_reject_attachment']));
            } else {
                $tender[$key]['open_status_tech_reject_attachment'] = "";
            }

            if ($tender_detail['open_status_fina_reject_attachment']) {
                $tender[$key]['open_status_fina_reject_attachment'] = asset('storage/' . str_replace('public/', '', $tender_detail['open_status_fina_reject_attachment']));
            } else {
                $tender[$key]['open_status_fina_reject_attachment'] = "";
            }

            if ($tender_detail['opening_commercial_reject_attachment']) {
                $tender[$key]['opening_commercial_reject_attachment'] = asset('storage/' . str_replace('public/', '', $tender_detail['opening_commercial_reject_attachment']));
            } else {
                $tender[$key]['opening_commercial_reject_attachment'] = "";
            }

            foreach ($tender_detail['tender_corrigendum'] as $key_technical => $value_technical) {
                if($value_technical['corrigendum_attechement']){
                    $tender[$key]['tender_corrigendum'][$key_technical]['corrigendum_attechement'] = asset('storage/' . str_replace('public/', '', $value_technical['corrigendum_attechement'])) ;
                }else{
                    $tender[$key]['tender_corrigendum'][$key_technical]['corrigendum_attechement'] = "";
                }
            }

            foreach ($tender_detail['tender_opening_status_technical'] as $key_technical => $value_technical) {
                if($value_technical['query_document_tech']){
                    $tender[$key]['tender_opening_status_technical'][$key_technical]['query_document_tech'] = asset('storage/' . str_replace('public/', '', $value_technical['query_document_tech'])) ;
                }else{
                    $tender[$key]['tender_opening_status_technical'][$key_technical]['query_document_tech'] = "";
                }
                if($value_technical['query_reply_document_tech']){
                    $tender[$key]['tender_opening_status_technical'][$key_technical]['query_reply_document_tech'] = asset('storage/' . str_replace('public/', '', $value_technical['query_reply_document_tech'])) ;
                }else{
                    $tender[$key]['tender_opening_status_technical'][$key_technical]['query_reply_document_tech'] = "";
                }
            }

            foreach ($tender_detail['tender_opening_status_financial'] as $key_technical => $value_technical) {
                if($value_technical['query_document_fina']){
                    $tender[$key]['tender_opening_status_financial'][$key_technical]['query_document_fina'] = asset('storage/' . str_replace('public/', '', $value_technical['query_document_fina'])) ;
                }else{
                    $tender[$key]['tender_opening_status_financial'][$key_technical]['query_document_fina'] = "";
                }
                if($value_technical['query_reply_document_fina']){
                    $tender[$key]['tender_opening_status_financial'][$key_technical]['query_reply_document_fina'] = asset('storage/' . str_replace('public/', '', $value_technical['query_reply_document_fina'])) ;
                }else{
                    $tender[$key]['tender_opening_status_financial'][$key_technical]['query_reply_document_fina'] = "";
                }
            }


            //fee
            if ($tender_detail['tender_opening_fee_status'] == "Eligible") {
                $tender[$key]['preliminary_part']['fee_status'] = "GREEN";
            } elseif ($tender_detail['tender_opening_fee_status'] == "Reject") {
                $tender[$key]['preliminary_part']['fee_status'] = "RED";
            } else {
                $tender[$key]['preliminary_part']['fee_status'] = "YELLOW";
            }
            //emd
            if ($tender_detail['tender_opening_emd_status'] == "Eligible") {
                $tender[$key]['preliminary_part']['emd_status'] = "GREEN";
            } elseif ($tender_detail['tender_opening_emd_status'] == "Reject") {
                $tender[$key]['preliminary_part']['emd_status'] = "RED";
            } else {
                $tender[$key]['preliminary_part']['emd_status'] = "YELLOW";
            }
            //technical part
            if ($tender_detail['open_status_tech'] == "Eligible") {
                $tender[$key]['technical_part']['emd_status'] = "GREEN";
            } elseif ($tender_detail['open_status_tech'] == "Reject") {
                $tender[$key]['technical_part']['emd_status'] = "RED";
            } else {
                $tender[$key]['technical_part']['emd_status'] = "YELLOW";
            }

            //financial part
            if ($tender_detail['open_status_fina'] == "Eligible") {
                $tender[$key]['financial_part']['emd_status'] = "GREEN";
            } elseif ($tender_detail['open_status_fina'] == "Reject") {
                $tender[$key]['financial_part']['emd_status'] = "RED";
            } else {
                $tender[$key]['financial_part']['emd_status'] = "YELLOW";
            }

            //commercial part
            if ($tender_detail['opening_commercial_status'] == "Eligible") {
                $tender[$key]['commercial_part']['emd_status'] = "GREEN";
            } elseif ($tender_detail['opening_commercial_status'] == "Reject") {
                $tender[$key]['commercial_part']['emd_status'] = "RED";
            } else {
                $tender[$key]['commercial_part']['emd_status'] = "YELLOW";
            }

            $tender[$key]['highest_party'] = $this->getHighestParty($tender_detail['id'],$tender_detail['company_id']);
            $tender[$key]['lowest_party'] = $this->getLowestParty($tender_detail['id'],$tender_detail['company_id']);
        }



        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $tender]);
    }

    public function getHighestParty($tender_id,$company_id){

        $bidders_max = Tender_boq_bidder::whereTenderId($tender_id)->where('own_company',0)->groupBy('bidder_id')->select('bidder_id', DB::raw('SUM(total_amount) as sum_amount'))->get()->max();
        if($bidders_max){
            $bidders_max = $bidders_max->toArray();
            $company = Tender_boq_bidder::where('bidder_id',$company_id)->where('own_company',1)->get()->sum('total_amount');

            $company_name = Companies::whereId($company_id)->first()->company_name;

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

            return $max_amount_name;
        }else{
            return "";
        }
    }

    public function getLowestParty($tender_id,$company_id){

        $bidders_min = Tender_boq_bidder::whereTenderId($tender_id)->where('own_company',0)->groupBy('bidder_id')->select('bidder_id', DB::raw('SUM(total_amount) as sum_amount'))->get()->min();
        if($bidders_min){
            $bidders_min = $bidders_min->toArray();
            $company = Tender_boq_bidder::where('bidder_id',$company_id)->where('own_company',1)->get()->sum('total_amount');

            $company_name = Companies::whereId($company_id)->first()->company_name;

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
            return $min_amount_name;
        }else{
            return "";
        }

    }

    public function get_prebid_query_tender(Request $request){
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $tender = Tender::select('tender.*','company.company_name' ,'tender_physical_submission.mode_name as physical_submission_mode_name', 'department.dept_name', 'tender_pattern.tender_pattern_name', 'tender_category.tender_category', \DB::raw("GROUP_CONCAT(users.name) as assign_employee"))
                ->with(['tender_pre_bid_document','tender_corrigendum','tender_client',
                    'tender_authorites'])
                ->join('department', 'department.id', '=', 'tender.department_id')
                ->join('company','company.id','=','tender.company_id')
                ->leftJoin('tender_physical_submission', 'tender_physical_submission.id', '=', 'tender.physical_sub_mode')
                ->join('tender_category', 'tender_category.id', '=', 'tender.tender_category_id')
                ->join('tender_pattern', 'tender_pattern.id', '=', 'tender.tender_pattern')
                ->leftJoin("users", \DB::raw("FIND_IN_SET(users.id,tender.assign_tender)"), ">", \DB::raw("0"))
                ->groupBy('tender.id')
                ->where('tender.tender_status', 'Selected')
                ->where('tender.pre_bid_meeting', 'Yes')
                ->orderBy('tender.id', 'DESC')
                ->offset($offset)
                ->limit($this->page_limit)
                ->get();
        if ($tender->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $tender]);
    }

    public function get_compairision_commercial(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'tender_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $tender_data = Tender::select('company_id')->whereId($request->get('tender_id'))->first();
        $company_id = $tender_data->company_id;


        if (!empty($request->get('bidder_ids'))) {
            $bidder_ids = explode(',', $request->get('bidder_ids'));
        } else {
            $bidder_ids = "";
        }

        if (!empty($request->get('item_ids'))) {
            $item_ids = explode(',', $request->get('item_ids'));
        } else {
            $item_ids = "";
        }

        if ($tender_data) {
            if ($bidder_ids) {
            $total_bidders = Tender_participated_bidder::whereTenderId($request->get('tender_id'))
                    ->whereIn('id', $bidder_ids)
                    ->get(['bidder_name', 'id']);
            }
            else{
                $total_bidders = Tender_participated_bidder::whereTenderId($request->get('tender_id'))
                    ->get(['bidder_name', 'id']);
            }
            if ($item_ids) {
                $company_items = Tender_boq_bidder::where('bidder_id', $company_id)->where('tender_id', $request->get('tender_id'))->where('own_company', 1)->whereIn('item_no', $item_ids)->get()->toArray();
            } else {
                $company_items = Tender_boq_bidder::where('bidder_id', $company_id)->where('tender_id', $request->get('tender_id'))->where('own_company', 1)->get()->toArray();
            }

            if ($bidder_ids) {
                $bidder_item = Tender_participated_bidder::whereTenderId($request->get('tender_id'))->whereIn('id', $bidder_ids)->get()->toArray();
            } else {
                $bidder_item = Tender_participated_bidder::whereTenderId($request->get('tender_id'))->get()->toArray();
            }

            foreach ($bidder_item as $key => $value) {
                $bidder_item[$key]['get_bidder_item'] = $this->getBidItem($value['id'], $item_ids);
            }
            // echo "<pre>";print_r($bidder_item);exit;
            if ($bidder_item) {
                $company_name = Companies::whereId($company_id)->first()->company_name;

                //color code logic

                foreach ($company_items as $item_key => $item) {
                    $item_amt_arr = []; $min_bidder=0;$max_bidder=0;$min_bid_amount=0.00;
                    $max_bid_amount=0.00;
                    foreach ($bidder_item as $key => $bidder) {
                        $item_amt_arr[$key]['key'] = $key;
                        $item_amt_arr[$key]['amount'] = $bidder['get_bidder_item'][$item_key]['total_amount'];
                    }
                    usort($item_amt_arr, function($a, $b) {
                        return $a['amount'] - $b['amount'];
                    });
                    $min_bidder=$item_amt_arr[0]['key'];
                    $min_bid_amount=$item_amt_arr[0]['amount'];
                    $max_bidder=$item_amt_arr[count($item_amt_arr)-1]['key'];
                    $max_bid_amount=$item_amt_arr[count($item_amt_arr)-1]['amount'];
                    if(isset($company_items[$item_key])){
                        if($company_items[$item_key]['total_amount']<=$min_bid_amount){
                            $company_items[$item_key]['color_code']="GREEN";
                        }
                        elseif($company_items[$item_key]['total_amount']>=$max_bid_amount){
                            $company_items[$item_key]['color_code']="RED";
                        }
                        else{
                            $company_items[$item_key]['color_code']=NULL;
                        }
                    }
                    foreach ($bidder_item as $key => $bidder) {
                        if($bidder['get_bidder_item'][$item_key]['total_amount']==$company_items[$item_key]['total_amount']){
                            $bidder_item[$key]['get_bidder_item'][$item_key]['color_code']="YELLOW";
                        }
                        elseif($key==$min_bidder){
                            $bidder_item[$key]['get_bidder_item'][$item_key]['color_code']="GREEN";
                        }
                        elseif($key==$max_bidder){
                            $bidder_item[$key]['get_bidder_item'][$item_key]['color_code']="RED";
                        }
                        else{
                            $bidder_item[$key]['get_bidder_item'][$item_key]['color_code']=NULL;
                        }
                    }
                }

                $tender = [
                    'company_items' => $company_items,
                    'your_company_name' => $company_name,
                    'bidder_item' => $bidder_item,
                    'total_bidders' => $total_bidders,
                ];
                return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $tender]);
            } else {
                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            }
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }


        // echo "<pre>";
        // print_r($company_items);
    }

    public function getBidItem($bidder, $item) {
        $bidder_item = Tender_boq_bidder::select('*');
        if ($bidder) {
            $bidder_item->where('bidder_id', $bidder);
        }

        if ($item) {
            $item_new = implode(',', $item);
            $bidder_item->whereIn('item_no', $item);
        }

        $data = $bidder_item->get()->toArray();

        return $data;
    }

    public function get_tender_details($id) {
        $tender_data = Tender::select('tender.id', 'tender.tender_sr_no', 'tender.tender_id_per_portal', 'tender.portal_name', 'tender.tender_no', 'tender.name_of_work', 'tender.state_name_work_execute', 'tender_client_detail.client_name', 'department.dept_name')->Leftjoin('tender_client_detail', 'tender.id', '=', 'tender_client_detail.tender_id')->Leftjoin('department', 'tender.department_id', '=', 'department.id')->where('tender.id', $id)->first();
        return $tender_data;
    }

    public function select_tender(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'tender_id' => 'required',
                    'assign_users' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $default_assign_user = TenderPermission::where('type','default_assign_user')->get()->first();
        if($default_assign_user){
            $default_user = User::whereIn('id',explode(',', $default_assign_user->user_id))->pluck('id')->toArray();
        }else{
            $default_user = "";
        }


        $default_user_arr = implode(',', $default_user);

        if($default_user_arr){
            $assign_tender_user = $request->get('assign_users').",".$default_user_arr;
        }else{
            $assign_tender_user = $request->get('assign_users');
        }

        $updare_arr = [
            'assign_tender' => $assign_tender_user,
            'tender_status' => 'Selected',
            'selected_at' => date('Y-m-d H:i:s'),
            'selected_by' => $request->get('user_id'),
            'updated_ip' => $request->ip(),
        ];
        // dd($updare_arr);
        if (Tender::whereIn('id', explode(',', $request->get('tender_id')))->update($updare_arr)) {
            $t_detail = Tender::where('id', $request->get('tender_id'))->get()->toArray();
            foreach ($t_detail as $key => $value) {
                $t_user = explode(',', $assign_tender_user);
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

            // User Action Log
            $tender_sr_no = Tender::where('id',$request->get('tender_id'))->value('tender_sr_no');
            $action_data = [
                'user_id' => $request->get('user_id'),
                'task_body' => "Tender Sr No. ".$tender_sr_no." selected",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true, 'msg' => "Tender selected successfully", 'data' => []]);
        } else {
            return response()->json(['status' => false, 'msg' => "Tender not select", 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function get_assign_user(Request $request){

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $simple_assign_user = TenderPermission::where('type','simple_assign_user')->get()->first();
        if($simple_assign_user){
            $default_assign_user = TenderPermission::where('type','default_assign_user')->get()->first();

            $default_user_name = User::select('name')->whereIn('id',explode(',', $default_assign_user->user_id))->get()->toArray();
            $default_user_name1 = [];
            foreach ($default_user_name as $key => $value) {
                $default_user_name1[$key] = $value['name'];
            }

            $assign_users = User::select('id','name')->whereIn('id',explode(',', $simple_assign_user->user_id))->get()->toArray();

            $main_arr = [
                'default_user' => implode(', ', $default_user_name1),
                'assign_user' => $assign_users
            ];
            return response()->json(['status' => true, 'msg' => "Tender assign users", 'data' => $main_arr]);
        }else{
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }
}
