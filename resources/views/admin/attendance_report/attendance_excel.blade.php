<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Document</title>
    <style>
        table{
            text-align: center !important;
        }
        th, tr, td{
            border: 1px solid;
        }
        th{
            font-weight: bolder;
        }
        .font-weight-bolder{
            font-weight: bolder;
        }
    </style>
</head>
<body>

    @foreach ($userDetails as $ud)

        @php
            $late_time_hour = "00:00";
            $early_going_hour = "00:00";
            $max_entry_time = new DateTime("09:45 AM");
            $entry_time1 = new DateTime("09:30 AM");
            $min_exit_time = new DateTime("05:30 PM");
            $exit_time = new DateTime("06:30 PM");

            $count = 0;
            $total_working_days = 0;
            $employee_presant_days = 0;
            $total_wo_days = 0;
            $total_paid_wo_days = 0;
            $absent_days = 0;
            $total_paid_leaves = 0;
            $employee_paid_days = 0;
        @endphp

        @foreach ($ud['attendance'] as $k=>$at)
            @if($at)
                @php
                    $in_time = new DateTime($at['in']);
                    $out_time = new DateTime($at['out']);
                    $isLate = "NO";
                    $isEarly = "NO";


                    if($at['availability_status'] == "Present")
                    {
                        $total_paid_leaves += $in_time > $max_entry_time?0.5:0;
                        $total_paid_leaves +=  $min_exit_time > $out_time ? 0.5:0;

                        $count += ($in_time < $max_entry_time && $in_time > $entry_time1)?1:0;
                        $count += ($min_exit_time < $out_time && $min_exit_time > $out_time)? 1:0;
                        $employee_presant_days++;
                    }
                    else {
                        $total_paid_leaves += 1;
                        $absent_days++;
                    }

                    if($at['availability_status'] != "Holiday" && $at['availability_status'] != "Weekend")
                    {
                        $total_working_days++;
                    }

                    if($count == 3)
                    {
                        $count = 0;
                        $total_paid_leaves += 0.5;
                    }

                @endphp
            @endif
        @endforeach

    <table style="width: 100%; border-collapse: collapse; text-align: center !important;" border="1">
        <thead>

            <tr>
                <th colspan="10" height="40" align="center"><b>Attandance report - From {{$firstMonth}} to {{$lastMonth}}</b></th>
            </tr>

            <tr>
                <th colspan="5" height="17" align="center"><b>Employee Name : {{ $ud['user_name'] }} </b></th>
                <th colspan="5" height="17" align="center"><b>Employee Department : {{ $ud['department'] }} </b></th>
            </tr>

            <tr>
                <th colspan="5" height="17" align="center"><b>Employee ID : {{ $ud['emp_code'] }} </b></th>
                <th colspan="5" height="17" align="center"><b>Employee Designation :{{ $ud['designation'] }}  </b></th>
            </tr>

            <tr>
                <th colspan="5" height="17" align="center"><b>Total Working Days: {{ $total_working_days }} </b></th>
                <th colspan="5" height="17" align="center"><b>Employee Present Days: {{ $employee_presant_days }}</b></th>
            </tr>

            <tr>
                <th colspan="5" height="17" align="center"><b>Total WO And HO Days: {{$total_wo_days}} </b></th>
                <th colspan="5" height="17" align="center"><b>Employee Paid WO And HO: {{$total_paid_wo_days}} </b></th>
            </tr>

            <tr>
                <th colspan="5" height="17" align="center"><b>Employee Absent Days: {{$absent_days}} </b></th>
                <th colspan="5" height="17" align="center"><b>Employee Paid Leaves: {{$total_paid_leaves}} </b></th>
            </tr>

            <tr>
                <th colspan="5" height="17" align="center"><b>Employee Paid Days: {{$employee_paid_days}}</b></th>
                <th colspan="5" height="17" align="center"><b>  </b></th>
            </tr>
            <tr>
                <th width="15" height="17" align="left"><b>Sr.No.</b></th>
                <th width="25" height="17" align="left"><b>First In</b></th>
                <th width="25" height="17" align="left"><b>Last Out</b></th>
                <th width="15" height="17" align="left"><b>Total Hours</b></th>
                <th width="15" height="17" align="left"><b>Day</b></th>
                <th width="15" height="25" align="left"><b>Is Late Coming? </b></th>
                <th width="25" height="17" align="left"><b>Late Time Hours</b></th>
                <th width="25" height="17" align="left"><b>Is Early Going?</b></th>
                <th width="30" height="17" align="left"><b>Early going Time Hours</b></th>
                <th width="15" height="17" align="left"><b>Avaibility </b></th>
            </tr>
        </thead>
        <tbody>

            @foreach ($ud['attendance'] as $k=>$at)

                <tr>
                @if ($at)
                    @php
                        if($at['availability_status'] == "Holiday" || $at['availability_status'] == "Weekend")
                        {
                            $color = "#d8d8d8";
                        }
                        else
                        {
                            $color = "#ffffff";
                        }
                    @endphp

                    @php
                        $late_time_hour = "00:00";
                        $early_going_hour = "00:00";
                        $max_entry_time = new DateTime("09:45 AM");
                        $entry_time1 = new DateTime("09:30 AM");
                        $min_exit_time = new DateTime("05:30 PM");
                        $exit_time = new DateTime("06:30 PM");
                        $in_time = new DateTime($at['in']);
                        $out_time = new DateTime($at['out']);
                        $count = 0;


                        if($at['availability_status'] != "Holiday" && $at['availability_status'] != "Weekend" && $in_time > $max_entry_time)
                        {
                            $isLate = "YES";
                            $late_time_hour = $in_time->diff($max_entry_time);
                            $late_time_hour = ' '.$late_time_hour->h.' : '.$late_time_hour->i;
                            $color = "#ffff00";
                        }
                        else
                        {
                            $isLate = "NO";
                        }

                        if($at['availability_status'] != "Holiday" && $at['availability_status'] != "Weekend" && $min_exit_time > $out_time)
                        {
                            $isEarly = "YES";
                            $early_going_hour = $min_exit_time->diff($out_time);
                            $early_going_hour = ' '.$early_going_hour->h.' : '.$early_going_hour->i;
                            $color = "#ffff00";
                        }
                        else
                        {
                            $isEarly = "NO";
                        }


                    @endphp
                    <td style="background-color: {{$color}}; align:left">{{$k}}</td>
                    <td style="background-color: {{$color}}">{{$days[$k]['date']}} {{$at['in'] ? $at['in']:"00:00"}}</td>
                    <td style="background-color: {{$color}}">{{$days[$k]['date']}} {{$at['out'] ? $at['out']:"00:00"}}</td>
                    <td style="background-color: {{$color}}">{{$at['total_hours']}}</td>
                    <td style="background-color: {{$color}}">{{$days[$k]['day']}} - {{$at['availability_status']}}</td>
                    <td style="background-color: {{$color}}">{{ $isLate }}  </td>
                    <td style="background-color: {{$color}}">{{$late_time_hour}}</td>
                    <td style="background-color: {{$color}}">{{$isEarly}}</td>
                    <td style="background-color: {{$color}}">{{$early_going_hour}}</td>
                    <td style="background-color: {{$color}}">{{$at['availability_status']}}</td>
                @else
                    <td>"N/A"</td>
                    <td>"N/A"</td>
                    <td>"N/A"</td>
                    <td>"N/A"</td>
                    <td>"N/A"</td>
                    <td>"N/A"</td>
                    <td>"N/A"</td>
                    <td>"N/A"</td>
                    <td>"N/A"</td>
                    <td>"N/A"</td>
                @endif
                </tr>
            @endforeach



            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
        </tbody>
    </table>

    @endforeach

    {{-- <table style="width: 100%; border-collapse: collapse; text-align: center !important;" border="1">
        <thead>
            <tr>
                <th colspan="{{count($days)+3+9}}" height="17" align="center"><b>Attandance report - From {{$firstMonth}} to {{$lastMonth}}</b></th>
            </tr>
            <tr>
                <th width="15" height="17" align="center"><b>No.</b></th>
                <th width="15" height="17" align="center"><b>Employee Name</b></th>
                <th width="15" height="17" align="center"></th>
                @if(isset($days) && !empty($days) && count($days) > 0)
                    @foreach($days as $item)
                        <th width="15" height="17" align="center"><b>{{strtoupper($item['day'])}}</b></th>
                    @endforeach
                @endif
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if(isset($userDetails) && !empty($userDetails) && count($userDetails) > 0)
                @foreach($userDetails as $user)
                    @php
                        $p = $ab = $hf = $pl = $ho = $wo = $lc = 0;
                    @endphp
                    <tr>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{$user['id']}}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{$user['emp_code']}}: {{$user['user_name']}}</b></td>
                        <td width="15" height="17"></td>
                        @if(isset($days) && !empty($days) && count($days) > 0)
                            @foreach($days as $item)
                                <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{strtoupper($item['date'])}}</b></td>
                            @endforeach
                        @endif
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>P</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>AB</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>HF</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>PL-EL</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>HO</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>WO</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>LC</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>Total Paid Days</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>Total working days</b></td>
                    </tr>
                    <tr>
                        <td width="15" height="17"></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>Designation: {{$user['designation']}}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>IN</b></td>
                        @if(isset($user['attendance']) && !empty($user['attendance']) && count($user['attendance']) > 0)
                            @foreach($user['attendance'] as $item)
                                @if(count($item) > 0)
                                    @if($item['time_differance'] == 0 && $item['in'] != "")
                                        @php
                                            $hf++;
                                        @endphp
                                    @elseif($item['time_differance'] >= 8)
                                        @php
                                            $p++;
                                        @endphp
                                    @elseif($item['time_differance'] > 0 && $item['time_differance'] <= 8)
                                        @php
                                            $hf++;
                                        @endphp
                                    @elseif($item['time_differance'] == 0 && $item['in'] == "" && $item['availability_status'] == "weekend" || $item['availability_status'] == "Weekend")
                                        @php
                                            $wo++;
                                        @endphp
                                    @elseif($item['time_differance'] == 0 && $item['in'] == "" && $item['availability_status'] != "weekend" || $item['availability_status'] != "Weekend")
                                        @php
                                            $pl++;
                                        @endphp
                                    @endif
                                @endif
                                <td width="15" height="17" align="center">@if(count($item) > 0) {{$item['in']}} @endif</td>
                            @endforeach
                        @endif
                        <td width="15" height="17" align="center">{{ $p }}</td>
                        <td width="15" height="17" align="center">{{ $ab }}</td>
                        <td width="15" height="17" align="center">{{ $hf }}</td>
                        <td width="15" height="17" align="center">{{ $pl }}</td>
                        <td width="15" height="17" align="center">{{ $ho }}</td>
                        <td width="15" height="17" align="center">{{ $wo }}</td>
                        <td width="15" height="17" align="center">{{ $lc }}</td>
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center">{{ count($days) - $wo - $ho }}</td>
                    </tr>
                    <tr>
                        <td width="15" height="17"></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>Dept : {{$user['department']}}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>OUT</b></td>
                        @if(isset($user['attendance']) && !empty($user['attendance']) && count($user['attendance']) > 0)
                            @foreach($user['attendance'] as $item)
                                <td width="15" height="17" align="center">@if(count($item) > 0) {{$item['out']}} @endif</td>
                            @endforeach
                        @endif
                        <td width="15" height="17" align="center">{{ $p }}</td>
                        <td width="15" height="17" align="center">{{ $ab }}</td>
                        <td width="15" height="17" align="center">{{ $hf }}</td>
                        <td width="15" height="17" align="center">{{ $pl }}</td>
                        <td width="15" height="17" align="center">{{ $ho }}</td>
                        <td width="15" height="17" align="center">{{ $wo }}</td>
                        <td width="15" height="17" align="center">{{ $lc }}</td>
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center">{{ count($days) - $wo - $ho }}</td>
                    </tr>
                    <tr>
                        <td width="15" height="17"></td>
                        <td width="15" height="17"></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>Total hrs</b></td>
                        @if(isset($user['attendance']) && !empty($user['attendance']) && count($user['attendance']) > 0)
                            @foreach($user['attendance'] as $item)
                                <td width="15" height="17" align="center">@if(count($item) > 0) {{$item['total_hours']}} @endif</td>
                            @endforeach
                        @endif
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center"></td>
                        <td width="15" height="17" align="center"></td>
                    </tr>
                    <tr>
                        <td width="15" height="17"></td>
                        <td width="15" height="17"></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>DAY</b></td>
                        @if(isset($user['attendance']) && !empty($user['attendance']) && count($user['attendance']) > 0)
                            @foreach($user['attendance'] as $item)
                                <td width="15" height="17" align="center">
                                    @if(count($item) > 0)
                                        @if($item['availability_status'] == "Present")
                                            <b>P</b>
                                        @elseif($item['availability_status'] == "Pending")
                                            <b>PEN</b>
                                        @elseif($item['availability_status'] == "leave" || $item['availability_status'] == "Leave")
                                            <b>EL</b>
                                        @elseif($item['availability_status'] == "holiday" || $item['availability_status'] == "Holiday")
                                            <b>HO</b>
                                        @elseif($item['availability_status'] == "weekend" || $item['availability_status'] == "Weekend")
                                            <b>WO</b>
                                        @elseif($item['availability_status'] == "Mixed Leave")
                                            <b>EL</b>
                                        @endif
                                    @else
                                        <b>EL</b>
                                    @endif
                                </td>
                            @endforeach
                        @endif
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{ $p }}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{ $ab }}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{ $hf }}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{ $pl }}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{ $ho }}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{ $wo }}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{ $lc }}</b></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"></td>
                        <td width="15" height="17" class="font-weight-bolder" align="center"><b>{{ count($days) - $wo - $ho }}</b></td>
                    </tr>
                    <tr>
                        <td colspan="{{count($days)+3}}"></td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table> --}}
</body>
</html>
