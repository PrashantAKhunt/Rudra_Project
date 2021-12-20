<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\Clients;
use App\Companies;
use Illuminate\Support\Facades\Validator;
use App\Lib\Permissions;
use App\ClientDetail;
use App\Tender;
use App\Lib\NotificationTask;
use DB;

class ClientsController extends Controller
{
    public $data;
    private $module_id;
    public $notification_task;


    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Clients";
        $this->data['module_link'] = "admin.client";
        $this->module_id=63;
    }
    
    public function index() {   //chnage
        $view_permission= Permissions::checkPermission($this->module_id,5);
        if(!$view_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = "Clients";
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        
        return view('admin.client.index', $this->data);
    }

    public function get_clients_list() {   //chnage
        $datatable_fields = array('clients.client_name','clients.location','company.company_name','clients.status','clients.created_at');
        $request = Input::all();
        // $conditions_array = ['clients.is_approved' => 1];
        $conditions_array = [];

        $getfiled =array('clients.id','clients.created_at','clients.email','clients.client_name','clients.detail','company.company_name','clients.status','clients.phone_number','clients.location');
        $table = "clients";
        
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='clients.company_id';
        $join_str[0]['from_table_id'] = 'company.id';
        
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
                                                  
        die();
    }
    public function change_client_status($id, $status) {
        $view_permission= Permissions::checkPermission($this->module_id,2);
        if(!$view_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        if (Clients::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.client')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.client')->with('error', 'Error during operation. Try again!');
    }

    public function add_client() {
        $view_permission= Permissions::checkPermission($this->module_id,3);
        if(!$view_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add client';
        $this->data['companies'] = Companies::orderBy('company_name', 'asc')->pluck('company_name','id');
        // echo "<pre>";
        // print_r($this->data['companies']);die;
        return view('admin.client.add_client', $this->data);
    }

    public function insert_client(Request $request) {   //chnage
        // dd($request->all());
        $validator_normal = Validator::make($request->all(), [
            'client_name' => 'required',
            // 'tender_id' => 'required',
            'email' => 'required',
            'client_landline_no' => 'required',
            'client_mobile_no' => 'required',
            'client_fax_no' => 'required',
            // 'detail' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_client')->with('error', 'Please follow validation rules.');
        }
        
        $check_client = Clients::where('company_id',$request->input('company_id'))->where('client_name',$request->input('client_name'))->count();
        if($check_client){
            return redirect()->route('admin.add_client')->with('error', 'Client added for this company.');
        }

        $clientModel = new Clients();
        $clientModel->user_id = Auth::user()->id;
        $clientModel->client_name     = $request->input('client_name');
        $clientModel->email           = $request->input('email');
        // $clientModel->phone_number    = $request->input('phone_number');
        $clientModel->location        = $request->input('location');
        $clientModel->detail          = ($request->get('detail') ? $request->get('detail') : "");
        $clientModel->company_id = $request->input('company_id');
        $clientModel->tender_id     = ($request->input('tender_id') ? $request->input('tender_id') : 0);
        $clientModel->client_landline_no     = $request->input('client_landline_no');
        $clientModel->client_mobile_no     = $request->input('client_mobile_no');
        $clientModel->client_fax_no     = $request->input('client_fax_no');
        if (Auth::user()->role != config('constants.SuperUser')) {
            $clientModel->status = 'Disabled';
            $clientModel->is_approved = 0;
        } else {
            $clientModel->status = 'Enabled';
            $clientModel->is_approved = 1;
        }
        $clientModel->created_at = date('Y-m-d h:i:s');
        $clientModel->created_ip = $request->ip();
        $clientModel->updated_at = date('Y-m-d h:i:s');
        $clientModel->updated_ip = $request->ip();
        if ($clientModel->save()) {
            $module = 'Client';
            $this->notification_task->entryApprovalNotify($module);
            $request_data = $request->all();
            foreach ($request_data['contact_name'] as $key => $contact_name) {
                if($request_data['contact_name'][$key]){
                $ClientDetail = new ClientDetail();
                $ClientDetail->client_id           = $clientModel->id;
                $ClientDetail->client_name         = $request_data['contact_name'][$key];
                $ClientDetail->client_designation = $request_data['contact_designation'][$key];
                $ClientDetail->client_phone_number        = $request_data['contact_phone'][$key];
                $ClientDetail->client_email = $request_data['contact_email'][$key];
                $ClientDetail->save();
                }
            }
            return redirect()->route('admin.client')->with('success', 'New client added successfully.');
        } else {
            return redirect()->route('admin.add_client')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function get_client_tender(Request $request){
        // ->where('opening_commercial_status','Eligible')
        $tender = Tender::select('id','portal_name','tender_sr_no')->where('company_id',$request->get('company_id'))->where('opening_commercial_status','Eligible')->get()->toArray();
        echo json_encode($tender);
    }

    public function get_tender_detail(Request $request){
        // ->where('opening_commercial_status','Eligible')
        $tender = Tender::select('id')->whereId($request->get('tender_id'))->with(['tender_client','tender_authorites'])->get()->first();
        echo json_encode($tender);
    }

    public function edit_client($id) {
        $view_permission= Permissions::checkPermission($this->module_id,2);
        $this->data['page_title'] = "Edit client";
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['client_detail'] = Clients::where('clients.id', $id)->get();
        if ($this->data['client_detail']->count() == 0) {
            return redirect()->route('admin.client')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['companies']    = Companies::whereId($this->data['client_detail'][0]['company_id'])->first()->company_name;
        $tender  = Tender::whereId($this->data['client_detail'][0]['tender_id'])->first();
        if($tender){
            $this->data['tender'] = $tender->portal_name."-".$tender->tender_sr_no;
        }else{
            $this->data['tender'] = "Tender Not Select";
        }

        $this->data['clientDetail'] = ClientDetail::where('client_id',$id)->get()->toArray();
        $this->data['clientDetail_Count'] = count($this->data['clientDetail']);
        /*echo "<pre>";
        print_r($this->data['clientDetail']);
        die();*/
        return view('admin.client.edit_client', $this->data);
    }

    public function update_client(Request $request) {  //chnage
        // dd($request->all());
        $validator_normal = Validator::make($request->all(), [
            'client_name' => 'required',
            // 'detail' => 'required',
            'email' => 'required',
            'client_landline_no' => 'required',
            'client_mobile_no' => 'required',
            'client_fax_no' => 'required',
            // 'company_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.client')->with('error', 'Please follow validation rules.');
        } 
        $client_id = $request->input('id'); 
        $client_arr = [
            'user_id' =>  Auth::user()->id,
            'client_name' => $request->input('client_name'),
            'email' => $request->input('email'),
            'phone_number'=>$request->input('phone_number'),
            'location'=>$request->input('location'),
            'detail' => ($request->get('detail') ? $request->get('detail') : ""),
            // 'company_id' => $request->input('company_id'),
            // 'tender_id' => $request->input('tender_id'),
            'client_landline_no' => $request->input('client_landline_no'),
            'client_mobile_no' => $request->input('client_mobile_no'),
            'client_fax_no' => $request->input('client_fax_no'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        
        if(Clients::where('id', $client_id)->update($client_arr))
        {
            $request_data = $request->all();
            DB::table('clients_details')->where('client_id', '=', $client_id)->delete();
            foreach ($request_data['contact_name'] as $key => $contact_name) {
                if(!empty($request_data['contact_name'][$key]) && !empty($request_data['contact_phone'][$key]) && !empty($request_data['contact_email'][$key]))
                {
                    $ClientDetail = new ClientDetail();
                    $ClientDetail->client_id           = $client_id;
                    $ClientDetail->client_name         = $request_data['contact_name'][$key];
                    $ClientDetail->client_phone_number        = $request_data['contact_phone'][$key];
                    $ClientDetail->client_email = $request_data['contact_email'][$key];
                    $ClientDetail->client_designation = $request_data['contact_designation'][$key];
                    $ClientDetail->save();    
                }
                
            }
        }

        return redirect()->route('admin.client')->with('success', 'client successfully updated.');
    }
    
    public function get_clientlist_by_company(Request $request) {
        $company_id=$request->input('company_id');
        
        $client_list= Clients::where('company_id',$company_id)->where('status','Enabled')->orderBy('client_name')->get();
        
        $html='<option value="">Select client</option>';
        
        if($client_list->count()>0){
            foreach ($client_list as $client){
                $html .='<option value="'.$client->id.'">'.$client->client_name.'</option>';
            }
        }
        echo $html; die();
    }

    //check role name exist or not
    public function check_uniquePancardNumber(Request $request) {
        $pan_card_number = $request->pan_card_number;
        $client_id       = $request->client_id;
		if(!$request->company_id){
        $pancardheck     = Clients::select(['id'])->where('pan_card_number', '=', $pan_card_number)->first();
        }
		else{
			$pancardheck     = Clients::select(['id'])->where('pan_card_number', '=', $pan_card_number)->where('company_id',$request->company_id)->first();
		}
        //Check during add pancard details
        if(empty($request->client_id)) {
          if (!empty($pancardheck)) {
                echo 'false';
                die();
            } else {
                echo 'true';
                die();
            }  
        }

        //Check during edit pancard details
        if(!empty($request->pan_card_number) && !empty($request->client_id) && !empty($pancardheck)) {
            if($pancardheck->id==$client_id) {
                echo 'true';
                die();
            }
            else {
                echo 'false';
                die();
            }
        }
        else
        {
            echo 'true';
            die();
        }
    }

    public function get_client_contact_list(Request $request)
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $client_id = $request->id;

        $client_contact_files = ClientDetail::where('client_id', $client_id)
        ->get(['id', 'client_id', 'client_name','client_email','client_phone_number']);

        foreach ($client_contact_files as $key => $files) {

            $client_contact_files[$key]->client_name         = $files->client_name;
            $client_contact_files[$key]->client_email        = $files->client_email;
            $client_contact_files[$key]->client_phone_number = $files->client_phone_number;
        }

        $this->data['client_contact_files'] = $client_contact_files;

        if ($client_contact_files->count() == 0) {
            return response()->json(['status' => false, 'data' => $this->data]);
        } else {

            return response()->json(['status' => true, 'data' => $this->data]);
        }
    }
}
