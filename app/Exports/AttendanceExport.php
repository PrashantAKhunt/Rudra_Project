<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use App\User;
use App\AttendanceDetail;
use App\AttendanceMaster;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AttendanceExport implements FromView, WithEvents
{
    use RegistersEventListeners;
    use Exportable;
    protected $data;
    protected $first_date;
    protected $second_date;

    function __construct($data,$first_date,$second_date) {
        $this->data = $data;
        $this->first_date = $first_date;
        $this->second_date = $second_date;

        //dd($this->data);
    }

    public function view(): View
    {
        $days = [];
        $date = \Carbon\Carbon::parse($this->first_date);
        $date2 = \Carbon\Carbon::parse($this->second_date);
        $firstMonth =  $date;
        $lastMonth =  $date2;

        for($i = 0; $i <= $date2->diffInDays($date); $i++){
            $fdate = Carbon::parse($firstMonth)->addDays($i)->format('D');
            $days[$i]['day'] = $fdate;
            $days[$i]['date'] = Carbon::parse($firstMonth)->addDays($i)->format('d/m/y');
        }

        $firstMonth = $firstMonth->format('d.m.Y');
        $lastMonth = $lastMonth->format('d.m.Y');

        $userDetails = $this->data($this->data, $this->first_date, $this->second_date);

        //dd($days, $firstMonth, $lastMonth, $userDetails);
        return view('admin.attendance_report.attendance_excel', compact('days','firstMonth','lastMonth','userDetails'));
    }

    public function data($data, $first_date, $second_date)
    {
        # code...
        $result = [];

        if(isset($this->data['user_id']) && count($this->data['user_id']) > 0)
        {
            $users = User::orderBy('name')->where("status", "Enabled")->whereIN('id', $this->data['user_id'])->get();
        }
        else
        {
            $users = User::orderBy('name')->where("status", "Enabled")->get();
        }

        if(isset($users) && count($users) > 0)
        {
            $i = 1;
            foreach ($users as $key => $value) {
                $result[] = [
                    'id' => $i,
                    'user_name' => $value->name,
                    'emp_code' => $value->employee->emp_code,
                    'designation' => $value->employee->designation,
                    'department' => $value->employee->department->dept_name,
                    'attendance' => $this->attendance($value->id, $first_date, $second_date)
                ];
                $i++;
            }
        }

        /* $p = $ab = $hf = $pl = $ho = $wo = $lc = 0;

        foreach($result[0]['attendance'] as $item)
        {
            if($item['time_differance'] == 0 && $item['in'] != "")
            {
                print_r($item);
                echo "halfday";
                echo "<br>";
                $hf++;
            }
            elseif($item['time_differance'] >= 8)
            {
                print_r($item);
                echo "present";
                echo "<br>";
                $p++;
            }
            elseif($item['time_differance'] > 0 && $item['time_differance'] <= 8)
            {
                print_r($item);
                echo "halfday";
                echo "<br>";
                $hf++;
            }
            elseif($item['time_differance'] == 0 && $item['in'] == "" && $item['availability_status'] == "weekend" || $item['availability_status'] == "Weekend")
            {
                print_r($item);
                echo "workout";
                echo "<br>";
                $wo++;
            }
            elseif($item['time_differance'] == 0 && $item['in'] == "" && $item['availability_status'] != "weekend" || $item['availability_status'] != "Weekend")
            {
                print_r($item);
                echo "pl";
                echo "<br>";
                $pl++;
            }
        }

        dd($hf, $pl, $p, $wo); */

        return $result;
    }

    public function attendance($user_id, $first_date, $second_date)
    {
        # code...
        $days = [];
        $date = \Carbon\Carbon::parse($this->first_date);
        $date2 = \Carbon\Carbon::parse($this->second_date);
        $firstMonth =  $date;
        $lastMonth =  $date2;

        for($i = 0; $i <= $date2->diffInDays($date); $i++){
            $fdate = Carbon::parse($firstMonth)->addDays($i)->format('Y-m-d');
            $days[$i] = $this->attendance_details(AttendanceMaster::whereDate('date', $fdate)->where(['user_id' => $user_id])->first());
        }

        //dd($days);

        return $days;
    }

    public function attendance_details($data)
    {
        # code...
        $result = [];

        if(!empty($data))
        {
            $result = [
                'in' => $data->first_in == null ? "" : Carbon::parse($data->first_in)->format('h:i A'),
                'out' => $data->last_out == null ? "" : Carbon::parse($data->last_out)->format('h:i A'),
                'total_hours' => $data->total_hours,
                'availability_status' => $data->availability_status == null ? "Weekend" :config::get('constants.AVAILABILITY_STATUS')[$data->availability_status],
                'time_differance' => Carbon::parse($data->total_hours)->format('H')
            ];
        }

        return $result;
    }
}
