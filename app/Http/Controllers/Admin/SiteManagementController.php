<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use Illuminate\Support\Facades\Validator;
use App\Companies;
use App\Projects;
use App\Tender;
use App\Site_manage_boq;
use App\Site_manage_daily_abstract;
use App\Site_manage_bill;
use App\Site_manage_item_block;
use DB;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;

class SiteManagementController extends Controller {

    public $data;

    public function __construct() {
        $this->data['module_title'] = "Site Management";
        $this->data['module_link'] = "admin.site_management";
    }

    public function index($company_id = "", $project_id = "") {
        $this->data['page_title'] = "BOQ";
        $this->data['company_list'] = Companies::where('status', 'Enabled')->orderBy('company_name')->get(['id', 'company_name']);
        $this->data['boq_list'] = [];
        $this->data['project_list'] = [];
        $this->data['company_id'] = "";
        $this->data['project_id'] = "";
        $this->data['boq_list'] = [];
        if ($company_id != "" && $project_id != "") {
            $this->data['company_id'] = $company_id;
            $this->data['project_id'] = $project_id;
            $this->data['project_list'] = Projects::where('company_id', $company_id)->get(['id', 'project_name']);
            $this->data['boq_list'] = Site_manage_boq::where('company_id', $company_id)
                    ->where('project_id', $project_id)
                    ->whereColumn('parent_boq','id')
                    ->get();
        }

        return view('admin.site_management.index', $this->data);
    }

    public function add_boq() {
        $this->data['page_title'] = 'Add BOQ';
        $this->data['company_list'] = Companies::where('status', 'Enabled')->orderBy('company_name')->get(['id', 'company_name']);
        return view('admin.site_management.add_boq', $this->data);
    }
    
    public function get_parent_boq_list(Request $request) {
        $project_id=$request->input('project_id');
        
    }

    public function insert_boq(Request $request) {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'project_id' => 'required',
                    'item_description' => 'required',
                    'UOM' => 'required',
                    'quantity' => 'required',
                    'rate' => 'required',
                    'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.add_boq')->with('error', 'Please follow all validation rules.');
        }

        $request_data = $request->all();

        //get last inserted boq item
        $last_boq_item = Site_manage_boq::where('company_id', $request_data['company_id'])
                ->where('project_id', $request_data['project_id'])
                ->orderBy('item_no', 'DESC')
                ->limit(1)
                ->get(['item_no']);
        if ($last_boq_item->count() > 0) {
            $new_item_no = $last_boq_item[0]->item_no + 1;
        } else {
            $new_item_no = 1;
        }
        $boq_obj = new Site_manage_boq();
        $boq_obj->company_id = $request_data['company_id'];
        $boq_obj->project_id = $request_data['project_id'];

        //$boq_obj->tender_id = $request_data['tender_id'];
        $boq_obj->item_no = $new_item_no;
        $boq_obj->item_description = $request_data['item_description'];
        $boq_obj->UOM = $request_data['UOM'];
        $boq_obj->quantity = $request_data['quantity'];
        $boq_obj->quantity_as_drawing = $request_data['quantity_as_drawing'];
        $boq_obj->rate = $request_data['rate'];
        $boq_obj->amount = $request_data['quantity'] * $request_data['rate'];
        $boq_obj->created_at = date('Y-m-d H:i:s');
        $boq_obj->created_ip = $request->ip();
        $boq_obj->updated_at = date('Y-m-d H:i:s');
        $boq_obj->updated_ip = $request->ip();

        $boq_obj->save();

        //add more item
        $items_arr = [];
        if(isset($request_data['item_description_add']) && !empty($request_data['item_description_add'])){

            foreach ($request_data['item_description_add'] as $key => $value) {

                $last_boq_item = Site_manage_boq::where('company_id', $request_data['company_id'])
                        ->where('project_id', $request_data['project_id'])
                        ->orderBy('item_no', 'DESC')
                        ->limit(1)
                        ->get(['item_no']);
                if ($last_boq_item->count() > 0) {
                    $new_add_item_no = $last_boq_item[0]->item_no + 1;
                } else {
                    $new_add_item_no = 1;
                }

                $items_arr['parent_boq'] = $boq_obj->id; 
                $items_arr['item_description'] = $value; 
                $items_arr['company_id'] = $request_data['company_id']; 
                $items_arr['project_id'] = $request_data['project_id']; 
                $items_arr['UOM'] = $request_data['UOM_add'][$key]; 
                $items_arr['quantity'] = $request_data['quantity_add'][$key]; 
                $items_arr['quantity_as_drawing'] = $request_data['quantity_as_drawing_add'][$key]; 
                $items_arr['rate'] = $request_data['rate_add'][$key]; 
                $items_arr['amount'] = $request_data['amount_add'][$key]; 
                $items_arr['item_no'] = $new_add_item_no;
                $items_arr['created_at'] = date('Y-m-d H:i:s');
                $items_arr['created_ip'] = $request->ip();
                $items_arr['updated_at'] = date('Y-m-d H:i:s');
                $items_arr['updated_ip'] = $request->ip();

                Site_manage_boq::insert($items_arr); 
            }
        }
        Site_manage_boq::whereId($boq_obj->id)->update(['parent_boq'=>$boq_obj->id]);


        return redirect()->route('admin.site_management')->with('success', 'BOQ successfully added.');
    }

    public function edit_boq($id) {
        $this->data['page_title'] = 'Edit BOQ';
        $this->data['boq_detail'] = $boq_detail = Site_manage_boq::with(['company_detail', 'project_detail'])->where('id', $id)->get();
        $this->data['boq_detail_items'] = Site_manage_boq::whereNotIn('id',[$id])->where('parent_boq',$id)->get()->toArray(); 
        $this->data['boq_detail_items_counter'] = count($this->data['boq_detail_items']); 

        if ($boq_detail->count() == 0) {
            return redirect()->route('admin.site_management')->with('error', 'Error Occurred. Try Again.');
        }
        // dd($this->data);
        return view('admin.site_management.edit_boq', $this->data);
    }

    public function update_boq(Request $request) {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'item_description' => 'required',
                    'UOM' => 'required',
                    'quantity' => 'required',
                    'rate' => 'required',
                    'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.add_boq')->with('error', 'Please follow all validation rules.');
        }

        $request_data = $request->all();


        $boq_obj = Site_manage_boq::find($request_data['id']);

        //$boq_obj->tender_id = $request_data['tender_id'];

        $boq_obj->item_description = $request_data['item_description'];
        $boq_obj->UOM = $request_data['UOM'];
        $boq_obj->quantity = $request_data['quantity'];
        $boq_obj->rate = $request_data['rate'];
        $boq_obj->amount = $request_data['quantity'] * $request_data['rate'];
        $boq_obj->updated_at = date('Y-m-d H:i:s');
        $boq_obj->updated_ip = $request->ip();

        $boq_obj->save();

        //add more item
        $items_arr = [];
        if(isset($request_data['item_description_add']) && !empty($request_data['item_description_add'])){
            // Site_manage_boq::where('parent_boq',$request_data['id'])->delete();


            foreach ($request_data['item_description_add'] as $key => $value) {

                $last_boq_item = Site_manage_boq::where('company_id', $request_data['company_id'])
                        ->where('project_id', $request_data['project_id'])
                        ->orderBy('item_no', 'DESC')
                        ->limit(1)
                        ->get(['item_no']);
                if ($last_boq_item->count() > 0) {
                    $new_add_item_no = $last_boq_item[0]->item_no + 1;
                } else {
                    $new_add_item_no = 1;
                }

                $items_arr['parent_boq'] = $request_data['id']; 
                $items_arr['item_description'] = $value;
                $items_arr['company_id'] = $request_data['company_id']; 
                $items_arr['project_id'] = $request_data['project_id'];
                $items_arr['UOM'] = $request_data['UOM_add'][$key]; 
                $items_arr['quantity'] = $request_data['quantity_add'][$key]; 
                $items_arr['quantity_as_drawing'] = $request_data['quantity_as_drawing_add'][$key]; 
                $items_arr['rate'] = $request_data['rate_add'][$key]; 
                $items_arr['amount'] = $request_data['amount_add'][$key]; 
                
                

                if(isset($request_data['iten_id_add'][$key]) && !empty($request_data['iten_id_add'][$key])){
                    $items_arr['updated_at'] = date('Y-m-d H:i:s');
                    $items_arr['updated_ip'] = $request->ip();
                    Site_manage_boq::whereId($request_data['iten_id_add'][$key])->update($items_arr);
                }else{
                    $items_arr['created_at'] = date('Y-m-d H:i:s');
                    $items_arr['created_ip'] = $request->ip();
                    $items_arr['updated_at'] = date('Y-m-d H:i:s');
                    $items_arr['updated_ip'] = $request->ip();
                    $items_arr['item_no'] = $new_add_item_no;

                    Site_manage_boq::insert($items_arr);
                }
            }
        }

        return redirect()->route('admin.site_management')->with('success', 'BOQ successfully added.');
    }

    public function add_bulk_boq(Request $request){
        // dd($request->all());
        $path = $request->file('boq_file')->getRealPath();
        $no_record=0;
        Site_manage_boq::where('company_id',$request->get('company_id_add'))->where('project_id',$request->get('project_id_add'))->delete();
        $data = (new FastExcel)->import($path, function ($line) use(&$no_record,&$request){
            $i = 0;
        // print_r(count($line));
            if($line['Item No'] != "" && $line['Quantities'] != "" && $line['Unit'] != "" && $line['Item work'] != "" && $line['Estimated Rates'] != "" && $line['Total Amount'] != ""){
                $no_record=1;
                $arr = [
                    'company_id' => $request->get('company_id_add'),
                    'project_id' => $request->get('project_id_add'),
                    'item_no' => $line['Item No'],
                    'item_description' => $line['Item work'],
                    'UOM' => $line['Unit'],
                    'quantity' => $line['Quantities'],
                    'rate' => $line['Estimated Rates'],
                    'amount' => $line['Total Amount'],
                    'created_ip' => $_SERVER['REMOTE_ADDR'],
                    'updated_ip' => $_SERVER['REMOTE_ADDR'],
                ];
                Site_manage_boq::insert($arr);
                // print_r($arr);    
            }else{
            Site_manage_boq::where('company_id',$request->get('company_id_add'))->where('project_id',$request->get('project_id_add'))->delete();
            echo json_encode(['status'=>'false','message'=>"Please fill all fields."]);die();
        }
        });
        // die();
        if($no_record==0){
            echo json_encode(['status'=>'false','message'=>"File data format is not correct. please download sample file and fill data in that format."]);die();
        }
        $last_id = Site_manage_boq::where('company_id',$request->get('company_id_add'))->where('project_id',$request->get('project_id_add'))->first();
        if($last_id){
           Site_manage_boq::where('company_id',$request->get('company_id_add'))->where('project_id',$request->get('project_id_add'))->update(['parent_boq' => $last_id['id']]);
        }
        echo json_encode(['status'=>'true','message'=>"Your file uploaded successfully"]);die();
    }

    public function delete_boq_sub_item(Request $request){
        Site_manage_boq::whereId($request->get('id'))->delete();
    }

    public function daily_abstract(Request $request) {
        $this->data['page_title'] = 'Daily Abstract Report';
        $this->data['company_id'] = "";
        $this->data['project_id'] = "";
        $this->data['project_list'] = [];
        $this->data['item_list'] = [];
        $this->data['company_list'] = Companies::where('status', 'Enabled')->orderBy('company_name')->get(['id', 'company_name']);
        $this->data['date_range'] = "";
        if ($request->input('company_id') != "" && $request->input('project_id') != "" && $request->input('date_range') != "") {
            $this->data['company_id'] = $request->input('company_id');
            $this->data['project_id'] = $request->input('project_id');
            $this->data['date_range'] = $request->input('date_range');
            $this->data['project_list'] = Projects::where('company_id', $request->input('company_id'))->get(['id', 'project_name']);
            
            $site_boq = Site_manage_boq::where('company_id',$request->input('company_id'))->where('project_id',$request->input('project_id'))->pluck('id');

            $date_range = explode(' - ', $request->input('date_range'));
            
            $site_boq_abs = Site_manage_daily_abstract::whereIn('boq_id',$site_boq)->whereBetween('created_at',[$this->remove_slashes($date_range[0]),$this->remove_slashes($date_range[1])])->with(['sub_item'])->get()->toArray();
            $group = [];

            $i = 0;
            foreach ($site_boq_abs as $item)  {
                if (!isset($group[date('Y-m-d',strtotime($item['created_at']))])) {
                    $group[date('Y-m-d',strtotime($item['created_at']))][$i] = [];
                }
                foreach ($item as $key => $value) {
                    if ($key == 'created_at') continue;
                    $group[date('Y-m-d',strtotime($item['created_at']))][$i][$key] = $value;
                }
                    $i++;
            }
            // dd($group);
            $this->data['item_list'] = $group;
        }

        return view('admin.site_management.daily_abstract', $this->data);
    }

    public function remove_slashes($date){
        $new_data = str_replace("/", "-", $date);
        return date('Y-m-d',strtotime($new_data));
    }

    public function site_report(Request $request) {
        $this->data['page_title'] = 'Abstract Report';
        $this->data['company_id'] = "";
        $this->data['project_id'] = "";
        $this->data['project_list'] = [];
        $this->data['item_list'] = [];
        $this->data['company_list'] = Companies::where('status', 'Enabled')->orderBy('company_name')->get(['id', 'company_name']);
        $this->data['date_range'] = "";
        if ($request->input('company_id') != "" && $request->input('project_id') != "" && $request->input('date_range') != "") {
            $this->data['company_id'] = $request->input('company_id');
            $this->data['project_id'] = $request->input('project_id');
            $this->data['date_range'] = $request->input('date_range');
            $this->data['project_list'] = Projects::where('company_id', $request->input('company_id'))->get(['id', 'project_name']);

            $site_boq = Site_manage_boq::where('company_id',$request->input('company_id'))->where('project_id',$request->input('project_id'))->pluck('id');

            $date_range = explode(' - ', $request->input('date_range'));
            
            $site_boq_abs = Site_manage_bill::whereIn('boq_id',$site_boq)->whereBetween('created_at',[$this->remove_slashes($date_range[0]),$this->remove_slashes($date_range[1])])->with(['sub_item'])->get()->toArray();

            
            $group = [];

            $i = 0;
            foreach ($site_boq_abs as $item)  {
                if (!isset($group[date('Y-m-d',strtotime($item['created_at']))])) {
                    $group[date('Y-m-d',strtotime($item['created_at']))][$i] = [];
                }
                foreach ($item as $key => $value) {
                    if ($key == 'created_at') continue;
                    $group[date('Y-m-d',strtotime($item['created_at']))][$i][$key] = $value;
                }
                    $i++;
            }

            $this->data['item_list'] = $group;
            // dd($group);
        }        
        return view('admin.site_management.site_report', $this->data);
    }

    public function get_unique_bill_number($boq_id){
        $last_boq_item = Site_manage_bill::where('boq_id', $boq_id)
                        ->orderBy('updated_at', 'DESC')
                        ->limit(1)
                        ->get(['unique_bill_number']);
        if ($last_boq_item->count() > 0) {
            $new_add_item_no = $last_boq_item[0]->unique_bill_number + 1;
        } else {
            $new_add_item_no = 1;
        }
        return $new_add_item_no;
    }

    public function generate_boq_bill(){
        $this->data['page_title'] = 'Generate Bill';
        $this->data['company_list'] = Companies::where('status', 'Enabled')->orderBy('company_name')->get(['id', 'company_name']);
        return view('admin.site_management.bill_generate', $this->data);
    }

    public function boq_bill_create(Request $request){
        // dd($request->all());
        $site_boq = Site_manage_boq::where('company_id',$request->input('company_id'))->where('project_id',$request->input('project_id'))->pluck('id');
            
            if($site_boq){
                $site_boq_abs = Site_manage_daily_abstract::whereIn('boq_id',$site_boq)->where('bill_generate',0)->with(['sub_item'])->get()->toArray();

                    if(count($site_boq_abs) == 0){
                        return redirect()->route('admin.generate_boq_bill')->with('error', 'There is not any item for bill generate.');
                    }

                    $bill_arr = [];
                    foreach ($site_boq_abs as $key => $value) {
                        // get Site_manage_bill
                         $site_manage_bill = Site_manage_bill::where('boq_id',$value['boq_id'])->orderBy('updated_at', 'DESC')
                        ->limit(1)->get()->toArray();
                         if($site_manage_bill){
                            $bill_arr['unique_bill_number'] = $this->get_unique_bill_number($value['boq_id']);
                            $bill_arr['boq_id'] = $value['boq_id'];
                            $bill_arr['parent_boq_id'] = $value['sub_item']['parent_boq'];
                            $bill_arr['qe_prev_bill'] = $site_manage_bill[0]['qe_upto_date'];
                            $bill_arr['qe_today_bill'] = $value['qe_executed_today_qty'];
                            $bill_arr['qe_upto_date'] = $site_manage_bill[0]['qe_upto_date'] + $value['qe_executed_today_qty'];
                            $bill_arr['a_prev_bill'] = $site_manage_bill[0]['a_upto_date'];
                            $bill_arr['a_today_bill'] = $value['qe_executed_today_qty'] * $value['sub_item']['rate'];
                            $bill_arr['a_upto_date'] = $bill_arr['qe_upto_date'] * $value['sub_item']['rate'];
                            $bill_arr['created_ip'] = $request->ip();
                            $bill_arr['updated_ip'] = $request->ip();
                         }else{
                            $bill_arr['unique_bill_number'] = $this->get_unique_bill_number($value['boq_id']);
                            $bill_arr['boq_id'] = $value['boq_id'];
                            $bill_arr['parent_boq_id'] = $value['sub_item']['parent_boq'];
                            $bill_arr['qe_prev_bill'] = $value['qe_prev_day_qty'];
                            $bill_arr['qe_today_bill'] = $value['qe_executed_today_qty'];
                            $bill_arr['qe_upto_date'] = $value['qe_total_qty'];
                            $bill_arr['a_prev_bill'] = $value['wea_prev_day'];
                            $bill_arr['a_today_bill'] = $value['wea_today'];
                            $bill_arr['a_upto_date'] = $value['wea_total'];
                            $bill_arr['created_ip'] = $request->ip();
                            $bill_arr['updated_ip'] = $request->ip();
                         }

                        Site_manage_bill::insert($bill_arr); 
                        Site_manage_daily_abstract::where('boq_id',$value['boq_id'])->update(['bill_generate'=>1]);
                    }    

                    // dd($bill_arr);
                    return redirect()->route('admin.generate_boq_bill')->with('success', 'Bill generate successfully.');       
            }else{
                return redirect()->route('admin.generate_boq_bill')->with('error', 'There is no any item added.');   
            }
    }

    public function generate_bill_invoice(){
        $this->data['page_title'] = 'Bill Invoice';
        $this->data['company_list'] = Companies::where('status', 'Enabled')->orderBy('company_name')->get(['id', 'company_name']);
        return view('admin.site_management.bill_generate_invoice', $this->data);
    }

    public function get_bill_invoice(Request $request){

        $this->data['page_title'] = 'Bill Invoice';
        $this->data['bill_number_unique'] = $request->get('bill_number_unique');
        $this->data['project_detail'] = Projects::whereId($request->get('project_id'))->first();
        $this->data['company_detail'] = Companies::whereId($request->get('company_id'))->first();
        $this->data['site_manage_boq'] = Site_manage_boq::where('company_id',$request->input('company_id'))->where('project_id',$request->input('project_id'))->pluck('id');

        $this->data['site_manage_bill'] = Site_manage_bill::where('unique_bill_number',$request->get('bill_number'))->whereIn('boq_id',$this->data['site_manage_boq'])->with(['get_boq_detail'])->get()->toArray();
        // dd($this->data);
        return view('admin.site_management.bill_invoice', $this->data);
    }

    public function get_boq_bill_number(Request $request){
        $site_boq = Site_manage_boq::where('company_id',$request->input('company_id'))->where('project_id',$request->input('project_id'))->pluck('id');
        $site_boq_abs = Site_manage_bill::whereIn('boq_id',$site_boq)->groupBy('unique_bill_number')->get(['unique_bill_number'])->toArray();
        echo json_encode($site_boq_abs);
    }

    public function excess_saving(Request $request){
        $this->data['page_title'] = 'Excess Saving';
        $this->data['company_id'] = "";
        $this->data['project_id'] = "";
        $this->data['project_list'] = [];
        $this->data['item_list'] = [];
        $this->data['company_list'] = Companies::where('status', 'Enabled')->orderBy('company_name')->get(['id', 'company_name']);
        if ($request->input('company_id') != "" && $request->input('project_id') != "") {
            $this->data['company_id'] = $request->input('company_id');
            $this->data['project_id'] = $request->input('project_id');
            $this->data['project_list'] = Projects::where('company_id', $request->input('company_id'))->get(['id', 'project_name']);

            $site_boq = Site_manage_boq::where('company_id',$request->input('company_id'))->where('project_id',$request->input('project_id'))->pluck('id');


            $daily_abstract = [];
            foreach ($site_boq as $key_boq => $value_boq) {
                $site_boq[$key_boq] = Site_manage_daily_abstract::where('boq_id',$value_boq)->orderBy('created_at','DESC')->with(['sub_item'])->limit(1)->get()->toArray();
            }
            
            // $daily_abstract = Site_manage_daily_abstract::whereIn('boq_id',$site_boq)->latest()->groupBy('boq_id')->with(['sub_item'])->get()->toArray();
            // dd($site_boq);


            $new_item_arr = [];
            foreach ($site_boq as $key => $value) {
                if(count($value)){
                    $new_item_arr[$key]['item_no'] = $value[0]['sub_item']['item_no'];
                    $new_item_arr[$key]['item_description'] = $value[0]['sub_item']['item_description'];
                    $new_item_arr[$key]['UOM'] = $value[0]['sub_item']['UOM'];
                    $new_item_arr[$key]['quantity'] = $value[0]['sub_item']['quantity'];
                    $new_item_arr[$key]['rate'] = $value[0]['sub_item']['rate'];
                    $new_item_arr[$key]['amount'] = $value[0]['sub_item']['amount'];
                    $new_item_arr[$key]['final_qty'] = $value[0]['qe_total_qty'];
                    $new_item_arr[$key]['final_amount'] = $value[0]['wea_total'];
                    if($value[0]['qe_total_qty'] > $value[0]['sub_item']['quantity']){
                        $new_item_arr[$key]['excess_qty'] = $value[0]['qe_total_qty'] - $value[0]['sub_item']['quantity'];
                        $new_item_arr[$key]['excess_amount'] = $new_item_arr[$key]['excess_qty'] * $value[0]['sub_item']['rate'];
                    }else{
                        $new_item_arr[$key]['excess_qty'] = 0.00;
                        $new_item_arr[$key]['excess_amount'] = 0.00;
                    }

                    if($value[0]['qe_total_qty'] < $value[0]['sub_item']['quantity']){
                        $new_item_arr[$key]['saving_qty'] = $value[0]['qe_total_qty'] - $value[0]['sub_item']['quantity'];
                        $new_item_arr[$key]['saving_amount'] = $new_item_arr[$key]['saving_qty'] * $new_item_arr[$key]['final_qty'];
                    }else{
                        $new_item_arr[$key]['saving_qty'] = 0.00;
                        $new_item_arr[$key]['saving_amount'] = 0.00;
                    }
                }
                
            }
            // dd($new_item_arr);
            $this->data['item_list'] = $new_item_arr; 
        }
        return view('admin.site_management.excess_saving', $this->data);
    }

    public function boq_design(Request $request){
        $this->data['page_title'] = "BOQ Design";
        $this->data['company_id'] = "";
        $this->data['project_id'] = "";
        $this->data['project_list'] = [];
        $this->data['item_list'] = [];
        $this->data['company_list'] = Companies::where('status', 'Enabled')->orderBy('company_name')->get(['id', 'company_name']);
        $this->data['item_no'] = "";
        if ($request->input('company_id') != "" && $request->input('project_id') != "" && $request->input('item_no') != "") {
            $this->data['company_id'] = $request->input('company_id');
            $this->data['project_id'] = $request->input('project_id');
            $this->data['project_list'] = Projects::where('company_id', $request->input('company_id'))->get(['id', 'project_name']);
            $this->data['item_no'] = $request->input('item_no');


            $this->data['item_list'] = Site_manage_item_block::where('boq_id',$request->input('item_no'))->with(['get_boq_item'])->get()->toArray();
            // dd($this->data);
        }
        return view('admin.site_management.boq_design', $this->data);
    }

    public function add_boq_design(){
        $this->data['page_title'] = "Add BOQ Design";
        $this->data['company_list'] = Companies::where('status', 'Enabled')->orderBy('company_name')->get(['id', 'company_name']);
        // dd($this->data);
        return view('admin.site_management.add_boq_design', $this->data);   
    }

    public function get_itemno_block(Request $request){
        
        $site_manage_boq = Site_manage_boq::where('company_id',$request->get('company_id'))->where('project_id',$request->get('project_id'))->get()->toArray();

       $child_arr = [];
       $parent_arr = [];
       $item_arr = [];

       foreach ($site_manage_boq as $key => $value) {
            $site_manage_boq[$key]['check']  = $this->checkchilditem($value['id']);
       }

       foreach ($site_manage_boq as $key1 => $value1) {
            if($value1['check'] == "parent"){
                $item_arr[$key1]['id'] = $value1['id'];                 
                $item_arr[$key1]['item_no'] = $value1['item_no'];                 
            }           
       }
       // echo json_encode($item_arr);
       if(count($item_arr)){
        echo "<option value=''>Select Item</option>";
            foreach ($item_arr as $key => $value) {
                ?> 
                <option value="<?php echo $value['id'];?>"><?php echo $value['item_no'];?></option>;
                <?php
            }
       }else{
        echo "<option value=''>Item No Not Found</option>";
       }
    }

    public function getItems(){
       $site_manage_boq = Site_manage_boq::get()->toArray();

       $child_arr = [];
       $parent_arr = [];
       $item_arr = [];

       foreach ($site_manage_boq as $key => $value) {
            $site_manage_boq[$key]['check']  = $this->checkchilditem($value['id']);
       }

       foreach ($site_manage_boq as $key1 => $value1) {
            if($value1['check'] == "parent"){
                $item_arr[$key1]['id'] = $value1['id'];                 
                $item_arr[$key1]['item_no'] = $value1['item_no'];                 
            }           
       }

       return $item_arr;
    }

    public function checkchilditem($id){
        $check = Site_manage_boq::where('parent_boq',$id)->get()->toArray();
        if(count($check) > 1){
            return "child";
        }else{
            return "parent";
        }
    }

    public function insert_boq_design(Request $request){
        // dd($request->all());
        $data = $request->all();
        //Site_manage_item_block
        $block_arr = [];
        foreach ($data['block_title'] as $key => $value) {
            $block_arr[$key]['boq_id'] = $data['item_no'];
            $block_arr[$key]['block_title'] = $value;
            $block_arr[$key]['block_detail'] = $data['block_detail'][$key];
            $block_arr[$key]['created_ip'] = $request->ip();
            $block_arr[$key]['updated_ip'] = $request->ip();
            
            if($request->hasFile('block_drawing')){
                $boq_image = $request->file('block_drawing');
                    foreach ($boq_image as $key1 => $image){
                        $file_path[$key1] = $image->store('public/tender_image');
                        if ($file_path[$key1]) {
                                $block_arr[$key]['block_drawing'] = $file_path[$key];
                        } 
                    }
            }
        }

        Site_manage_item_block::insert($block_arr);

        return redirect()->route('admin.boq_design')->with('success', 'BOQ Design added.');
    }

    public function get_boq_design_list(){
        $datatable_fields = array('site_manage_item_block.id','company.company_name','project.project_name','site_manage_boq.item_no', 'site_manage_item_block.block_title','site_manage_item_block.block_detail','site_manage_item_block.block_drawing');
        $request = Input::all();
        $conditions_array = [];

        $getfiled =array('site_manage_item_block.id','company.company_name','project.project_name','site_manage_boq.item_no','site_manage_boq.item_description', 'site_manage_item_block.block_title','site_manage_item_block.block_detail','site_manage_item_block.block_drawing');
        $table = "site_manage_item_block";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'site_manage_boq';
        $join_str[0]['join_table_id'] = 'site_manage_boq.id';
        $join_str[0]['from_table_id'] = 'site_manage_item_block.boq_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'site_manage_boq.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'project';
        $join_str[2]['join_table_id'] = 'project.id';
        $join_str[2]['from_table_id'] = 'site_manage_boq.project_id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function boq_design_drawing($id){
        $file = Site_manage_item_block::whereId($id)->first();
        if($file->block_drawing){
            $isExists = Storage::exists($file->block_drawing);
            if($isExists){
                return Storage::download($file->block_drawing);
            }else{
                echo "File not exists";die();
            }
        }else{
            echo "File not exists";die();
        }
    }

    public function update_boq_design($id){
        $this->data['page_title'] = "Edit BOQ Design";
        $this->data['boq_block'] = Site_manage_item_block::whereId($id)->first();
        return view('admin.site_management.edit_boq_design', $this->data);
    }

    public function edit_boq_design(Request $request){
        // dd($request->all());
        if($request->hasFile('block_drawing')){
            $boq_image = $request->file('block_drawing');
            $block_drawing = $boq_image->store('public/tender_image');
        }else{
            $block_drawing = $request->get('block_drawing_hidden');
        }

        $update_arr = [
            'block_title' => $request->get('block_title'),
            'block_detail' => $request->get('block_detail'),
            'block_drawing' => $block_drawing,
            'updated_ip' => $request->ip(),
        ];

        Site_manage_item_block::whereId($request->get('id'))->update($update_arr);

        return redirect()->route('admin.boq_design')->with('success', 'BOQ Design updated.');
    }
}
