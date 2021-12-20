<?php

namespace App\Http\Controllers\Admin;

use App\Asset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vehicle_Insurance;
use App\Insurance_reminder_dates;
use Illuminate\Support\Facades\Validator;
use App\Employees;
use App\Companies;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Lib\Permissions;
class VehicleAssetController extends Controller {

    public $data;

    public function get_vehicle_assets() {
        $vehicle_insurance_full_view_permission = Permissions::checkPermission(38, 5);
        
        if(!$vehicle_insurance_full_view_permission){
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        
        $this->data['page_title'] = "Vehicle Insurance";

        $this->data['vehicle_assets'] = $vehicle_assets = Vehicle_Insurance::select('asset.name', 'asset.asset_1', 'asset.id', 'vehicle_insurance.*')
                ->join('asset', 'vehicle_insurance.asset_id', '=', 'asset.id')
                ->where('asset.asset_type', '=', 'Vehicle Asset')
                ->where('asset.status', '=', 'Enabled')
                ->where('vehicle_insurance.status', '=', 'Live')
                ->get();
               
                foreach ($vehicle_assets as $key => $assets) {
                    if ($assets->renew_date) {
                        
                        $currDate = strtotime(date('Y-m-d'));

                        $renwDate = strtotime($assets->renew_date);
        
                        if ($renwDate > $currDate) {
        
                            $datDiff = $renwDate - $currDate;
                            $left_days = round($datDiff / (60 * 60 * 24));
                            
                            $vehicle_assets[$key]->left_day = $left_days;
                            if ($left_days <= 30.0 && $left_days >= 11.0) {
                                $vehicle_assets[$key]->color_class = 'text-warning';
                            }elseif($left_days <= 10.0){
                                $vehicle_assets[$key]->color_class = 'text-danger';
                            }else{
                                $vehicle_assets[$key]->color_class = 'text-success';
                            }

                        }
                    }

                }


        return view('admin.vehicle_asset.index', $this->data);
    }

    public function add_vehicle_insurance() {
        $this->data['page_title'] = 'Add Insurance';
        $this->data['module_title'] = "Vehicle Assets";

        $company_id = Employees::where('user_id', Auth::user()->id)->value('company_id');

        $this->data['company_name'] = $name = Companies::where('id', $company_id)->value('company_name');


        return view('admin.vehicle_asset.add_insurance', $this->data);
    }

    public function asset_details(Request $request) {

        $company_id = Employees::where('user_id', Auth::user()->id)->value('company_id');
        $type = $request->type;

        $vehicle_ins_ids = Vehicle_Insurance::where('type', $type)->pluck('asset_id')->toArray();

        $asset_data = Asset::where('asset_type', 'Vehicle Asset')
                        ->where('status', 'Enabled')
                        ->where('company_id', $company_id)
                        ->whereNotIn('id', $vehicle_ins_ids)->orderBy('name')
                        ->get(['id', 'asset_1', 'name'])->toArray();


        return response()->json($asset_data);
    }

    public function insert_vehicle_insurance(Request $request) {


        $validator_normal = Validator::make($request->all(), [
                    'asset_id' => 'required',
                    'company' => 'required',
                    'number' => 'required',
                    'agent_name' => 'required',
                    'contact_number' => 'required',
                    'type' => 'required',
                    'amount' => 'required',
                    'insurance_date' => 'required',
                    'renew_date' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_vehicle_insurance')->with('error', 'Please follow validation rules.');
        }

        $company_id = Employees::where('user_id', Auth::user()->id)->value('company_id');
        $insurance_arr = [
            'asset_id' => $request->input('asset_id'),
            'company_id' => $company_id,
            'company_name' => $request->input('company'),
            'insurance_number' => $request->input('number'),
            'agent_name' => $request->input('agent_name'),
            'contact_number' => $request->input('contact_number'),
            'contact_email' => $request->input('contact_email'),
            'amount' => $request->input('amount'),
            'type' => $request->input('type'),
            'status' => 'Live',
            'renewal' => 'NO',
            'insurance_date' => date('Y-m-d', strtotime($request->input('insurance_date'))),
            'renew_date' => date('Y-m-d', strtotime($request->input('renew_date'))),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        $ins_id = Vehicle_Insurance::insertGetId($insurance_arr);

        if ($request->input('reminder_date')) {
            $reminder_date_arr = $request->input('reminder_date');

            
            foreach ($reminder_date_arr as $key => $date) {
                    $InsuranceDateModel = new Insurance_reminder_dates();
                    $InsuranceDateModel->vehicle_insurance_id = $ins_id;
                    $InsuranceDateModel->date = date('Y-m-d', strtotime($date));
                    $InsuranceDateModel->created_at = date('Y-m-d h:i:s');
                    $InsuranceDateModel->created_ip =  $request->ip();
                    $InsuranceDateModel->save();
            }
        }

        return redirect()->route('admin.vehicle_assets')->with('success', 'New Insurance Policy successfully Added.');
    }

    public function get_reminder_dates(Request $request){
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $reminder_dates = Insurance_reminder_dates::where('vehicle_insurance_id',$request->get('id'))->pluck('date')->toArray();

        $this->data['reminder_dates'] = $reminder_dates;

        if ($reminder_dates) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }
    } 

    public function expired_insurances_list() {
        $this->data['page_title'] = 'Expired Insurances';
        $this->data['module_title'] = "Vehicle Assets";

        $this->data['exp_insurance_list'] = $exp_insurance_list = Vehicle_Insurance::select('vehicle_insurance.*', 'asset.name', 'asset.asset_1')
                ->join('asset', 'vehicle_insurance.asset_id', '=', 'asset.id')
                ->where('vehicle_insurance.status', '=', 'Expired')
                ->where('vehicle_insurance.renewal', '=', 'NO')
                ->where('asset.status', '=', 'Enabled')
                ->orderBy('vehicle_insurance.insurance_date', 'ASC')
                ->get();          //->toArray();

        return view('admin.vehicle_asset.expired_insurances_list', $this->data);
    }

    public function renew_expired_vehicle_insurance($id) {
        $this->data['page_title'] = 'Renew Expired Insurances';
        $this->data['module_title'] = "Vehicle Assets";
        $company_id = Employees::where('user_id', Auth::user()->id)->value('company_id');

        $this->data['asset_data'] = Asset::where('asset_type', 'Vehicle Asset')
                ->where('status', 'Enabled')
                ->where('company_id', $company_id)
                ->get(['id', 'asset_1', 'name']);

        $this->data['exp_insurance_data'] = $exp_insurance_list = Vehicle_Insurance::select('*')
                ->where('id', '=', $id)
                ->get();


        if ($this->data['exp_insurance_data']->count() == 0) {
            return redirect()->route('admin.vehicle_assets')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.vehicle_asset.renew_expired_insurance', $this->data);
    }

    public function renewed_insurance(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'asset_id' => 'required',
                    'company' => 'required',
                    'number' => 'required',
                    'agent_name' => 'required',
                    'contact_number' => 'required',
                    'amount' => 'required',
                    'insurance_date' => 'required',
                    'renew_date' => 'required'
        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.renew_expired_vehicle_insurance')->with('error', 'Please follow validation rules.');
        }

        $renewal_arr = [
            'renewal' => "YES"
        ];

        $insurance_id = $request->input('id');

        Vehicle_Insurance::where('id', $insurance_id)->update($renewal_arr);
        $company_id = Employees::where('user_id', Auth::user()->id)->value('company_id');
        $insurance_arr = [
            'asset_id' => $request->input('asset_id'),
            'company_id' => $company_id,
            'company_name' => $request->input('company'),
            'insurance_number' => $request->input('number'),
            'agent_name' => $request->input('agent_name'),
            'contact_number' => $request->input('contact_number'),
            'contact_email' => $request->input('contact_email'),
            'amount' => $request->input('amount'),
            'type' => $request->input('type'),
            'status' => 'Live',
            'renewal' => 'NO',
            'insurance_date' => date('Y-m-d', strtotime($request->input('insurance_date'))),
            'renew_date' => date('Y-m-d', strtotime($request->input('renew_date'))),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        $ins_newid= Vehicle_Insurance::insertGetId($insurance_arr);

        if ($request->input('reminder_date')) {
            $reminder_date_arr = $request->input('reminder_date');

            
            foreach ($reminder_date_arr as $key => $date) {
                    $InsuranceDateModel = new Insurance_reminder_dates();
                    $InsuranceDateModel->vehicle_insurance_id = $ins_newid;
                    $InsuranceDateModel->date = date('Y-m-d', strtotime($date));
                    $InsuranceDateModel->created_at = date('Y-m-d h:i:s');
                    $InsuranceDateModel->created_ip =  $request->ip();
                    $InsuranceDateModel->save();
            }
        }

        return redirect()->route('admin.vehicle_assets')->with('success', 'Renewd Insurance successfully.');
    }

    public function insurances_list($id, $type) {
        $this->data['page_title'] = 'Insurances List';
        $this->data['module_title'] = "Vehicle Assets";

        $this->data['insurance_list'] = $insurance_list = Vehicle_Insurance::select('vehicle_insurance.*', 'asset.name', 'asset.asset_1')
                ->join('asset', 'vehicle_insurance.asset_id', '=', 'asset.id')
                ->where('vehicle_insurance.asset_id', '=', $id)
                ->where('vehicle_insurance.type', '=', $type)
                ->where('asset.status', '=', 'Enabled')
                ->orderBy('vehicle_insurance.insurance_date', 'ASC')
                ->get();          //->toArray();


                foreach ($insurance_list as $key => $assets) {
                    if ($assets->renew_date) {
                        
                        $currDate = strtotime(date('Y-m-d'));

                        $renwDate = strtotime($assets->renew_date);
        
                        if ($renwDate > $currDate) {
        
                            $datDiff = $renwDate - $currDate;
                            $left_days = round($datDiff / (60 * 60 * 24));
                            
                            $insurance_list[$key]->left_day = $left_days;
                            if ($left_days <= 30.0 && $left_days >= 11.0) {
                                $insurance_list[$key]->color_class = 'text-warning';
                            }elseif($left_days <= 10.0){
                                $insurance_list[$key]->color_class = 'text-danger';
                            }else{
                                $insurance_list[$key]->color_class = 'text-success';
                            }

                        }else{
                            $insurance_list[$key]->left_day = '';
                            $insurance_list[$key]->color_class = 'text-dark';

                        }
                    }

                }

        return view('admin.vehicle_asset.vehicle_insurance_list', $this->data);
    }

}
