<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Imports\BankTransactionImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use App\User;
use App\Announcements;
use App\Email_format;
use App\Mail\Mails;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;

class AnnouncementsController extends Controller {

    public $data;
    private $common_task;
    private $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Announcements";
        $this->data['module_link'] = "admin.announcements";

        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function index() {
        $this->data['page_title'] = "Announcements";
        return view('admin.announcements.index', $this->data);
    }

    public function get_announcements_list() {

        $datatable_fields = array('title', 'description');
        $request = Input::all();
        $conditions_array = [];

        $join_str = [];

        $getfiled = array('id', 'title', 'description', 'start_date', 'end_date', 'user_id');
        $table = "announcements";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_announcements() {
        $this->data['page_title'] = 'Add Announcements';
        $this->data['user'] = User::getUser();
        return view('admin.announcements.add_announcements', $this->data);
    }

    public function insert_announcements(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'title' => 'required',
                    'description' => 'required',
                    'date_range' => 'required',
                    //'end_date' => 'required',
                    'user_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_announcements')->with('error', 'Please follow validation rules.');
        }

        $date_range_arr = explode('-', $request->input('date_range'));

        $start_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date_range_arr[0])));
        $end_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date_range_arr[1])));

        $annModel = new Announcements();
        $annModel->title = $request->input('title');
        $annModel->description = $request->input('description');
        $annModel->start_date = $start_date;
        $annModel->end_date = $end_date;
        $annModel->user_id = implode(",", $request->input('user_id'));
        $annModel->created_at = date('Y-m-d h:i:s');
        $annModel->created_ip = $request->ip();
        $annModel->updated_at = date('Y-m-d h:i:s');
        $annModel->updated_ip = $request->ip();

        if ($annModel->save()) {

            //Send Announcements Mail
            /* $emailData = Email_format::find(5)->toArray(); // 5 = Announcements
              $subject = $emailData['subject'];
              $mailformat = $emailData['emailformat'];
              $mailformat = str_replace("%title%", $annModel->subject, $mailformat);
              $mailformat = str_replace("%description%", $annModel->description, $mailformat);

              $emailList = DB::table('users')->whereIn('id', $request->input('user_id'))->pluck('email')->toArray();

              Mail::to($emailList)->send(new Mails($subject, $mailformat)); */

            return redirect()->route('admin.announcements')->with('success', 'New announcements added successfully.');
        } else {
            return redirect()->route('admin.add_announcements')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_announcements($id) {

        $this->data['page_title'] = "Edit Announcements";
        $this->data['announcements_detail'] = Announcements::where('id', $id)->get();
        $this->data['user'] = User::getUser();
        if ($this->data['announcements_detail']->count() == 0) {
            return redirect()->route('admin.announcements')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.announcements.edit_announcements', $this->data);
    }

    public function update_announcements(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'title' => 'required',
                    'description' => 'required',
                    'date_range' => 'required',
                    //'end_date' => 'required',
                    'user_id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.announcements')->with('error', 'Please follow validation rules.');
        }

        $date_range_arr = explode('-', $request->input('date_range'));

        $start_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date_range_arr[0])));
        $end_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date_range_arr[1])));

        $annModel = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_id' => implode(",", $request->input('user_id')),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Announcements::where('id', $request->input('id'))->update($annModel);

        //Send Announcements Mail
        $emailData = Email_format::find(10)->toArray(); // 5 = Announcements
        $subject = $emailData['subject'];
        $mailformat = $emailData['emailformat'];
        $mailformat = str_replace("%title%", $request->input('title'), $mailformat);
        $mailformat = str_replace("%description%", $request->input('description'), $mailformat);

        $emailList = DB::table('users')->whereIn('id', $request->input('user_id'))->pluck('email')->toArray();
        //do not send email in update
        //Mail::to($emailList)->send(new Mails($subject, $mailformat));

        return redirect()->route('admin.announcements')->with('success', 'Announcements successfully updated.');
    }

    public function delete_announcements($id) {
        if ($annModel = Announcements::findOrFail($id)) {
            $annModel->delete();
            return redirect()->route('admin.announcements')->with('success', 'Announcements successfully delete.');
        }
        return redirect()->route('admin.announcements')->with('error', 'Error during operation. Try again!');
    }

    public function hide_show_announcement(Request $request) {
        $current_date_time = date('Y-m-d H:i:s');
        $show_announcement_list = Announcements::where('start_date', '<=', $current_date_time)
                ->where('end_date', '>', $current_date_time)
                ->where(['status' => 'Enabled', 'show_announcement' => 0])
                ->get();

        if ($show_announcement_list->count() > 0) {

            foreach ($show_announcement_list as $show_announcement) {
                $update_arr = [
                    'show_announcement' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Announcements::where('id', $show_announcement->id)->update($update_arr);

                //get user's list for email
                $user_ids = explode(',', $show_announcement->user_id);
                $user_emails = User::whereIn('id', $user_ids)->get(['email'])->pluck('email');

                $mail_data['title'] = $show_announcement->title;
                $mail_data['description'] = $show_announcement->description;
                $mail_data['email_list'] = $user_emails;
                $this->common_task->announcementEmail($mail_data);

                $this->notification_task->announcementNotify($user_ids,$show_announcement->id,$mail_data);
            }
        }

        //hide announcement
        $hide_announcement_list = Announcements::where('end_date', '<=', $current_date_time)
                ->where(['status' => 'Enabled', 'show_announcement' => 1])
                ->get();
        if ($hide_announcement_list->count() > 0) {
            foreach ($hide_announcement_list as $hide_announcement) {
                $update_arr = [
                    'show_announcement' => 0,
                    'status' => 'Disabled',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Announcements::where('id', $hide_announcement->id)->update($update_arr);
            }
        }
    }

}
