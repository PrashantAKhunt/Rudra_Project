<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Banks;
use App\Companies;
use App\Vendors;
use App\Vendors_bank;
use App\Http\Controllers\Controller;
use App\Lib\Permissions;
use App\Lib\NotificationTask;

class VendorsBankController extends Controller
{

    private $notification_task;
    
    public function __construct()
    {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Vendors Bank";
        $this->data['module_link'] = "admin.vendors_bank";
        $this->module_id = 45;
        
    }
    public function index()
    {
        $vendor_bank_full_view_permission = Permissions::checkPermission(45, 5);
        if(!$vendor_bank_full_view_permission){
            return redirect()->route('admin.dashboard')->with('error','Access Denied.');
        }
        $this->data['page_title'] = "Vendors Bank";
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        $this->data['vendor_bank']  = Vendors_bank::where('vendors_bank.is_approved',1)
            ->join('company', 'vendors_bank.company_id', '=', 'company.id')
            ->join('vendor', 'vendors_bank.vendor_id', '=', 'vendor.id')
            ->get(['company.company_name', 'vendor.vendor_name', 'vendors_bank.*']);

        return view('admin.vendors_bank.index', $this->data);
    }

    public function change_vendor_bank_status($id, $status)
    {

        if (Vendors_bank::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.vendors_bank')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.vendors_bank')->with('error', 'Error during operation. Try again!');
    }

    public function add_vendors_bank()
    {
        $this->data['page_title'] = 'Add Vendor Bank';
        $this->data['module_title'] = 'Vendor Bank';
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['companies'] = Companies::orderBy('company_name')->get(['company_name', 'id']);
        $this->data['vendors'] = Vendors::where('status', 'Enabled')->get(['vendor_name', 'id']);

        return view('admin.vendors_bank.add_bank', $this->data);
    }

    public function companies_vendor(Request $request)
    {


        $company_id = $request->company_id;

        $vendors = Vendors::select('vendor.*')
            ->where('company_id', $company_id)
            ->where('status', 'Enabled')
            ->orderBy('vendor_name','ASC')
            ->get();


        return response()->json($vendors);
    }

    public function insert_vendors_bank(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'bank_name' => 'required',
            'detail' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_vendors_bank')->with('error', 'Please follow validation rules.');
        }
        $bank_arr = [
            'user_id' => Auth::user()->id,
            'company_id' => $request->input('company_id'),
            'vendor_id' => $request->input('vendor_id'),
            'bank_name' => $request->input('bank_name'),
            'beneficiary_name' => $request->input('beneficiary_name'),
            'ac_number' => $request->input('account_number'),
            'ifsc' => $request->input('ifsc'),
            'micr_code' => $request->input('micr_code'),
            'swift_code' => $request->input('swift_code'),
            'branch' => $request->input('branch'),
            'account_type' => $request->input('account_type'),
            'detail' => $request->input('detail'),
            // 'status' => 'Disabled',
            // 'is_approved' => 0,
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        if (Auth::user()->role != config('constants.SuperUser')) {
            $bank_arr['status'] = 'Disabled';
            $bank_arr['is_approved'] = 0;
        } else {
            $bank_arr['status'] = 'Enabled';
            $bank_arr['is_approved'] = 1;
        }
        Vendors_bank::insert($bank_arr);
                $module = 'Vendor Bank';
                $this->notification_task->entryApprovalNotify($module);
        return redirect()->route('admin.vendors_bank')->with('success', 'New Vendor Bank successfully Added.');
    }


    public function edit_vendors_bank($id)
    {
        $this->data['page_title'] = "Edit Vendor bank";
        $this->data['module_title'] = ' Vendor Bank';
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }

        $this->data['vendor_bank_detail'] = $vendor_bank = Vendors_bank::where('id', $id)->get();

        if ($this->data['vendor_bank_detail']->count() == 0) {

            return redirect()->route('admin.vendors_bank')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['companies'] = $companies = Companies::orderBy('company_name')->get(['company_name', 'id']);

        $this->data['vendors']  = Vendors::where('company_id', '=', $vendor_bank[0]->company_id)->where('status', 'Enabled')->orderBy('vendor_name')->get(['vendor_name', 'id']);
        //148 ma other ;

        return view('admin.vendors_bank.edit_bank', $this->data);
    }

    public function update_vendors_bank(Request $request)
    {


        $validator_normal = Validator::make($request->all(), [

            'bank_name' => 'required',
            'detail' => 'required'

        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.vendors_bank')->with('error', 'Please follow validation rules.');
        }

        $vendor_bank_id = $request->input('id');
        $bank_arr = [
            'user_id' => Auth::user()->id,
            'company_id' => $request->input('company_id'),
            'vendor_id' => $request->input('vendor_id'),
            'bank_name' => $request->input('bank_name'),
            'beneficiary_name' => $request->input('beneficiary_name'),
            'ac_number' => $request->input('account_number'),
            'ifsc' => $request->input('ifsc'),
            'branch' => $request->input('branch'),
            'account_type' => $request->input('account_type'),
            'detail' => $request->input('detail'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];


        Vendors_bank::where('id', $vendor_bank_id)->update($bank_arr);

        return redirect()->route('admin.vendors_bank')->with('success', 'Vendor Bank Detail successfully Updated !.');
    }

    //check Account Number exist or not
    public function check_uniqueAccountNumber(Request $request)
    {
        $company_id = $request->company_id;
        $vendor_id = $request->vendor_id;
        $account_number = $request->account_number;
        $vendor_bank_id    = $request->id;

         
        $accnoheck  = Vendors_bank::select(['id'])->where('ac_number', '=', $account_number)
            ->where('company_id', '=', $company_id)
            ->where('vendor_id', '=', $vendor_id)
            ->first();
        
        //Check during add account_number details
        if (empty($vendor_bank_id)) {
            if (empty($accnoheck === null)) {


                echo 'false';
                die();
            } else {

                echo 'true';
                die();
            }
        }

        //Check during edit account_number details
        if (!empty($request->account_number) && !empty($request->id) && !empty($accnoheck)) {


            if ($accnoheck->id == $vendor_bank_id) {


                echo 'true';
                die();
            } else {

                echo 'false';
                die();
            }
        } else {

            echo 'true';
            die();
        }
    }
}
