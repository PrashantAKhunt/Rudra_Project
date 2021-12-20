<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\TdsSectionType;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Lib\Permissions;
use App\Lib\NotificationTask;

class TdsSectionController extends Controller
{
    public $data;
    private $notification_task;


    public function __construct()
    {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "TDS Section";
        $this->data['module_link'] = 'admin.tds_section';
        $this->module_id = 77;
    }

    public function index()
    {
        $this->data['page_title'] = 'TDS Section';
        $permission = Permissions::checkSpecialPermission($this->module_id);
        if (!$permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        // dd($this->data);
        $this->data['tds_section'] = TdsSectionType::get();
        return view('admin.tds_section.index', $this->data);
    }

    public function change_tds_section_status($id, $status)
    {
        $permission= Permissions::checkSpecialPermission($this->module_id);
        if(!$permission){
            return redirect()->route('admin.tds_section')->with('error','Access denied. You do not have permission to access this module.');
        }
        if (TdsSectionType::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.tds_section')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.tds_section')->with('error', 'Error during operation. Try again!');
    }

    public function add_tds_section()
    {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if (!$view_special_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add TDS Section';
        return view('admin.tds_section.add_section', $this->data);
    }

    public function save_tds_section(Request $request)
    {

        $validator_normal = Validator::make($request->all(), [
            'section_type' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->back()->with('error', 'Please follow validation rules.');
        }

        $section = [
            'section_type' => $request->get('section_type'),
            'user_id' => Auth::user()->id,
        ];

        if (Auth::user()->role != config('constants.SuperUser')) {
            $section['is_approved'] = 0;
            $section['status'] = 'Disabled';
        } else {
            $section['is_approved'] = 1;
            $section['status'] = 'Enabled';
        }

        if ($request->get('id')) {
            unset($section['status']);
            unset($section['is_approved']);
            if (TdsSectionType::whereId($request->get('id'))->update($section)) {
                return redirect()->route('admin.tds_section')->with('success', 'Data successfully updated.');
            }
        } else {
            if (TdsSectionType::insert($section)) {
                $module = 'TDS Section';
                $this->notification_task->entryApprovalNotify($module);
                return redirect()->route('admin.tds_section')->with('success', 'Data successfully inserted.');
            }
        }

        return redirect()->route('admin.tds_section')->with('error', 'Error during operation. Try again!');
    }

    public function edit_tds_section($id)
    {
        $permission= Permissions::checkSpecialPermission($this->module_id);
        if(!$permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }

        $this->data['page_title'] = "Edit TDS Section";
        $this->data['section_detail'] = TdsSectionType::where('id', $id)->first();
        if ($this->data['section_detail']->count() == 0) {
            return redirect()->route('admin.tds_section')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.tds_section.edit_section', $this->data);
    }
}
