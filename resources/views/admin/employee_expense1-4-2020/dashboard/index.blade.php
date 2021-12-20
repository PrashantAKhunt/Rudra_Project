@extends('layouts.admin_app')

@section('content')
<style>
    .tableBodyScroll tbody {
        display: block;
        max-height: 250px;
        overflow-y: auto;
    }

    .tableBodyScroll thead,
    tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Dashboard</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            @if (session('error'))
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('error') }}
            </div>
            @endif
            @if (session('success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('success') }}
            </div>
            @endif 
        </div>
        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-success">
                <div class="panel-heading">Your Today's Attendance Log ({{date('d-m-Y')}}) <span id="today_attendance_count" class="label label-rouded label-inverse">0</span>
                    <span id="today_attendance_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="get_your_today_attendance();" ><i class="fa fa-refresh"></i></a> 

                        <a href="{{ route('admin.attendance') }}" title="View All" ><i class="fa fa-eye"></i></a>

                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>In/Out</th>
                            <th>Time</th>
                            <th>Attend Type</th>
                            <th>Is Approved</th>

                            </thead>
                            <tbody id="your_today_attendance_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-primary">
                <div class="panel-heading">Upcoming Holidays In Week <span id="holiday_count" class="label label-rouded label-inverse">0</span>
                    <span id="holiday_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="get_holiday_list();" ><i class="fa fa-refresh"></i></a> 
                        <a href="{{ route('admin.holiday') }}" title="View All" ><i class="fa fa-eye"></i></a>
                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>Festival Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>

                            </thead>
                            <tbody id="holiday_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-danger">
                <div class="panel-heading">Upcoming Birthday and Anniversary <span id="birth_day_count" class="label label-rouded label-inverse">0</span>
                    <span id="birth_day_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="get_birthday_ani_list();" ><i class="fa fa-refresh"></i></a> 

                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>Name</th>
                            <th>Event</th>
                            </thead>
                            <tbody id="birthday_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-info">
                <div class="panel-heading"> Today's Leave <span id="today_leave_count" class="label label-rouded label-inverse">0</span>
                    <span id="today_leave_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="get_today_leave();" ><i class="fa fa-refresh"></i></a> 
                        @if($leave_viewall_permission)
                        <a href="{{ route('admin.all_leave') }}" title="View All" ><i class="fa fa-eye"></i></a>
                        @endif
                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Assigned User</th>

                            </thead>
                            <tbody id="today_leave_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if($leave_viewall_permission)
        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-primary">
                <div class="panel-heading">Pending Leave Approvals <span id="pending_leave_count" class="label label-rouded label-inverse">0</span>
                    <span id="pending_leave_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="get_leave_approval();" ><i class="fa fa-refresh"></i></a> 

                        <a href="{{ route('admin.all_leave') }}" title="View All" ><i class="fa fa-eye"></i></a>

                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Start Date</th>
                            <th>End Date</th>

                            </thead>
                            <tbody id="leave_approval_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif


        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-warning">
                <div class="panel-heading">Leave Relive Request <span id="leave_relive_count" class="label label-rouded label-inverse">0</span>
                    <span id="leave_relive_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="get_assigned_work();" ><i class="fa fa-refresh"></i></a> 

                        <a href="{{ route('admin.relieving_request') }}" title="View All" ><i class="fa fa-eye"></i></a>

                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Start Date</th>
                            <th>End Date</th>

                            </thead>
                            <tbody id="leave_assignedwork_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-success">
                <div class="panel-heading">Your Pending Leaves 
                    <span id="your_pending_leaves_count" class="label label-rouded label-inverse">0</span>
                    <span id="your_pending_leaves_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="get_your_leave();" ><i class="fa fa-refresh"></i></a> 

                        <a href="{{ route('admin.leave') }}" title="View All" ><i class="fa fa-eye"></i></a>

                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>Subject</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reliever Status</th>

                            </thead>
                            <tbody id="your_leave_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if($attendance_approval_viewall_permission)
        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-primary">
                <div class="panel-heading">Pending Attendance Approvals <span id="pending_attendance_approval_count" class="label label-rouded label-inverse">0</span>
                    <span id="pending_attendance_approval_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="pending_attendance_approval();" ><i class="fa fa-refresh"></i></a> 

                        <a href="{{ route('admin.approve_attendance') }}" title="View All" ><i class="fa fa-eye"></i></a>

                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>User name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>In/Out</th>

                            </thead>
                            <tbody id="attendance_approval_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-lg-12 col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">Pending Expense Approvals <span id="pending_expense_count" class="label label-rouded label-inverse">0</span>
                    <span id="pending_expense_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="pending_expense_approval();" ><i class="fa fa-refresh"></i></a> 

                        <a href="{{ route('admin.employee_expense') }}" title="View All" ><i class="fa fa-eye"></i></a>

                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>User name</th>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Other Project Detail</th>
                            <th>Merchant</th>
                            <th>Amount</th>

                            </thead>
                            <tbody id="expense_approval_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.Admin') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-primary">
                <div class="panel-heading">Pending Driver Expense <span id="pending_driver_expense_count" class="label label-rouded label-inverse">0</span>
                    <span id="pending_driver_expense_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="get_driver_expense_approval();" ><i class="fa fa-refresh"></i></a> 

                        <a href="{{ route('admin.all_expense') }}" title="View All" ><i class="fa fa-eye"></i></a>

                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>Driver Name</th>
                            <th>Vehicle</th>
                            <th>Date</th>
                            <th>Amount</th>

                            </thead>
                            <tbody id="driver_expense_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div class="col-lg-6 col-sm-6">
            <div class="panel panel-primary">
                <div class="panel-heading">Driver Trip Approval <span id="driver_trip_approval_count" class="label label-rouded label-inverse">0</span>
                    <span id="driver_trip_approval_count_refresh"></span>
                    <div class="pull-right">

                        <a href="#" title="Minimize/Maximize" data-perform="panel-collapse"><i class="ti-minus"></i></a> 
                        <a href="javascript:void(0)" title="Refresh" onclick="get_driver_trip_approval();" ><i class="fa fa-refresh"></i></a> 

                        <a href="{{ route('admin.close_trip_index') }}" title="View All" ><i class="fa fa-eye"></i></a>

                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true" style="">
                    <div class="panel-body">
                        <table class="table table-striped tableBodyScroll">
                            <thead>
                            <th>Driver Name</th>
                            <th>Vehicle</th>
                            <th>Opening Time</th>
                            <th>Closing Time</th>

                            </thead>
                            <tbody id="driver_trip_tbl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <br><br>

    <div class="row">

        <!--row -->
        @if(Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.Admin') || Auth::user()->role==config('constants.SuperUser'))
        <div class="row">
            <div class="col-md-12" >
                <div class="white-box">
                    <h3 class="box-title">Work Remotely Locator</h3>
                    <div id="map" class="gmaps" style="min-height:700px;"></div>
                </div>
            </div>

        </div>
        @endif

    </div>



    @endsection
    @section('script')
    <script type="text/javascript">
        var loader_path="{{ asset('admin_asset/assets/plugins/images/ajax-loader.gif') }}";
        function hide_loader(){
            setTimeout(function(){$('#loading_img').remove();},2000);
        }
        function get_today_leave(){
            $('#today_leave_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('admin.get_today_leaves')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id': "{{ Auth::user()->id }}"},
                success:function(data){
                   hide_loader();
                if (data.status){
                $('#today_leave_tbl').html("");
                $('#today_leave_count').text(data.data.length);
                for (let i = 0; i < data.data.length; i++){
                var profile_img = "{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}";
                if (data.data[i].profile_image){
                profile_img = data.data[i].profile_image;
                }
                $('#today_leave_tbl').append('<tr><td><img width="40px" height="40px" src="' + profile_img + '" alt="user" class="img-circle">' + data.data[i].name + '</td>\n\
                                                       <td>' + data.data[i].subject + '</td> \n\
                                                        <td>' + data.data[i].assigned_username + '</td></tr>');
                }
                }
                else{
                $('#today_leave_tbl').html('<tr><td colspan="3">No Record Found</td></tr>')
                }
                }
        });
        }

        function get_leave_approval(){
        $('#pending_leave_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.get_leave_approval_list')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id': "{{ Auth::user()->id }}"},
                success:function(data){
                   hide_loader();
                if (data.status && data.data.length > 0){
                $('#pending_leave_count').text(data.data.length);
                $('#leave_approval_tbl').html("");
                for (let i = 0; i < data.data.leave_approval_list.length; i++){
                var profile_img = "{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}";
                if (data.data.leave_approval_list[i].profile_image){
                profile_img = data.data.leave_approval_list[i].profile_image;
                }
                $('#leave_approval_tbl').append('<tr><td><img width="40px" height="40px" src="' + profile_img + '" alt="user" class="img-circle">' + data.data.leave_approval_list[i].name + '</td>\n\
                                                       <td>' + data.data.leave_approval_list[i].subject + '</td> \n\
                                                        <td>' + moment(data.data.leave_approval_list[i].start_date).format('DD-MM-YYYY') + '</td>\n\
                                                        <td>' + moment(data.data.leave_approval_list[i].end_date).format('DD-MM-YYYY') + '</td></tr>');
                }
                }
                else{
                $('#leave_approval_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }

        function get_assigned_work(){
        $('#leave_relive_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.get_leave_assigned_work')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id': "{{ Auth::user()->id }}"},
                success:function(data){
                    hide_loader();
                if (data.status && data.data.length > 0){
                    $('#leave_relive_count').text(data.data.length);
                $('#leave_assignedwork_tbl').html("");
                for (let i = 0; i < data.data.leave_work_request.length; i++){
                var profile_img = "{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}";
                if (data.data.leave_work_request[i].profile_image){
                profile_img = data.data.leave_work_request[i].profile_image;
                }
                $('#leave_assignedwork_tbl').append('<tr><td><img width="40px" height="40px" src="' + profile_img + '" alt="user" class="img-circle">' + data.data.leave_work_request[i].name + '</td>\n\
                                                       <td>' + data.data.leave_work_request[i].subject + '</td> \n\
                                                        <td>' + moment(data.data.leave_work_request[i].start_date).format('DD-MM-YYYY') + '</td>\n\
                                                        <td>' + moment(data.data.leave_work_request[i].end_date).format('DD-MM-YYYY') + '</td></tr>');
                }
                }
                else{
                $('#leave_assignedwork_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }

        function get_your_leave(){
        $('#your_pending_leaves_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.get_all_pending_leave')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id': "{{ Auth::user()->id }}"},
                success:function(data){
                    hide_loader();
                if (data.status && data.data.pending_leave_list.length > 0){
                    $('#your_pending_leaves_count').text(data.data.pending_leave_list.length);
                $('#your_leave_tbl').html("");
                for (let i = 0; i < data.data.pending_leave_list.length; i++){
                var status = "NA";
                if (data.data.pending_leave_list[i].assign_work_status == "Pending"){
                status = '<b class="text-warning">Pending</b>';
                }
                else if (data.data.pending_leave_list[i].assign_work_status == "Accepted"){
                status = '<b class="text-success">Accepted</b>';
                }
                else{
                status = '<b class="text-danger">' + data.data.pending_leave_list[i].assign_work_status + '</b>';
                }
                $('#your_leave_tbl').append('<tr>\n\
                                                       <td>' + data.data.pending_leave_list[i].subject + '</td> \n\
                                                        <td>' + moment(data.data.pending_leave_list[i].start_date).format('DD-MM-YYYY') + '</td>\n\
                                                        <td>' + moment(data.data.pending_leave_list[i].end_date).format('DD-MM-YYYY') + '</td>\n\
                                                        <td>' + status + '</td></tr>');
                }
                }
                else{
                $('#your_leave_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }

        function get_your_today_attendance(){
        $('#today_attendance_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.get_attendance_detail')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id': "{{ Auth::user()->id }}", 'attend_user_id': "{{ Auth::user()->id }}", 'attendance_date':"{{date('Y-m-d')}}"},
                success:function(data){
                hide_loader();
                if (data.status && data.data.attendance_log.length > 0){
                    $('#today_attendance_count').text(data.data.attendance_log.length);
                $('#your_today_attendance_tbl').html("");
                for (let i = 0; i < data.data.attendance_log.length; i++){

                $('#your_today_attendance_tbl').append('<tr>\n\
                                                       <td>' + data.data.attendance_log[i].punch_type + '</td> \n\
                                                        <td>' + data.data.attendance_log[i].time + '</td>\n\\n\
                                                        <td>' + data.data.attendance_log[i].device_type + '</td>\n\
                                                        <td>' + data.data.attendance_log[i].is_approved + '</td></tr>');
                }
                }
                else{
                $('#your_today_attendance_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }

        function pending_attendance_approval(){
        $('#pending_attendance_approval_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.get_approval_attendance_list')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id': "{{ Auth::user()->id }}"},
                success:function(data){
hide_loader();
                if (data.status && data.data.attendance_approval_list.length > 0){
                $('#attendance_approval_tbl').html("data.data.attendance_approval_list.length");
                $('#pending_attendance_approval_count').text();
                for (let i = 0; i < data.data.attendance_approval_list.length; i++){
                var profile_img = "{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}";
                if (data.data.attendance_approval_list[i].profile_image){
                profile_img = data.data.attendance_approval_list[i].profile_image;
                }
                $('#attendance_approval_tbl').append('<tr>\n\
                                                       <td><img width="40px" height="40px" src="' + profile_img + '" alt="user" class="img-circle">' + data.data.attendance_approval_list[i].name + '</td> \n\
                                                        <td>' + data.data.attendance_approval_list[i].date + '</td>\n\\n\
                                                        <td>' + data.data.attendance_approval_list[i].time + '</td>\n\
                                                        <td>' + data.data.attendance_approval_list[i].punch_type + '</td></tr>');
                }
                }
                else{
                $('#attendance_approval_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }

        function pending_expense_approval(){
        $('#pending_expense_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.get_all_expense')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id': "{{ Auth::user()->id }}", 'page_number':1},
                success:function(data){
hide_loader();
                if (data.status && data.data.expense_list.length > 0){
                $('#expense_approval_tbl').html("");
                $('#pending_expense_count').text(data.data.expense_list.length);
                for (let i = 0; i < data.data.expense_list.length; i++){
                var profile_img = "{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}";
                if (data.data.expense_list[i].profile_image){
                profile_img = data.data.expense_list[i].profile_image;
                }
                $('#expense_approval_tbl').append('<tr>\n\
                                                       <td><img width="40px" height="40px" src="' + profile_img + '" alt="user" class="img-circle">' + data.data.expense_list[i].name + '</td> \n\
                                                        <td>' + moment(data.data.expense_list[i].date).format("DD-MM-YYYY") + '</td>\n\\n\
                                                        <td>' + data.data.expense_list[i].title + '</td>\n\
                                                        <td>' + data.data.expense_list[i].project_name + '</td>\n\
                                                        <td>' + data.data.expense_list[i].other_project + '</td>\n\
                                                        <td>' + data.data.expense_list[i].merchant_name + '</td>\n\
                                                        <td>' + data.data.expense_list[i].amount + '</td></tr>');
                }
                }
                else{
                $('#expense_approval_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }

        function get_holiday_list(){
        $('#holiday_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.get_upcoming_holiday')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {},
                success:function(data){
hide_loader();
                if (data.status && data.data.length > 0){
                $('#holiday_tbl').html("");
                $('#holiday_count').text(data.data.length);
                for (let i = 0; i < data.data.length; i++){

                $('#holiday_tbl').append('<tr>\n\
                                                       <td>' + data.data[i].title + '</td> \n\
                                                        <td>' + moment(data.data[i].start_date).format("DD-MM-YYYY") + '</td>\n\\n\
\n\                                                     <td>' + moment(data.data[i].end_date).format("DD-MM-YYYY") + '</td></tr>');
                }
                }
                else{
                $('#holiday_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }

        function get_birthday_ani_list(){
        $('#birth_day_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.get_upcomings')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id':"{{ Auth::user()->id }}"},
                success:function(data){
hide_loader();
                if (data.status && data.data.length > 0){
                $('#birthday_tbl').html("");
                $('#birth_day_count').text(data.data.length);
                for (let i = 0; i < data.data.length; i++){
                var profile_img = "{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}";
                if (data.data[i].profile_image){
                profile_img = data.data[i].profile_image;
                }
                $('#birthday_tbl').append('<tr>\n\
                                                       <td><img width="40px" height="40px" src="' + profile_img + '" alt="user" class="img-circle">' + data.data[i].name + '</td> \n\
\n\                                                     <td>' + data.data[i].date + '</td></tr>');
                }
                }
                else{
                $('#birthday_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }
        
        function get_driver_expense_approval(){
        $('#pending_driver_expense_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.get_driver_expense_approval_list')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id':"{{ Auth::user()->id }}"},
                success:function(data){
hide_loader();
                if (data.status && data.data.length > 0){
                $('#driver_expense_tbl').html("");
                $('#pending_driver_expense_count').text(data.data.length);
                for (let i = 0; i < data.data.length; i++){
                var profile_img = "{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}";
                if (data.data[i].profile_image){
                profile_img = data.data[i].profile_image;
                }
                $('#driver_expense_tbl').append('<tr>\n\
                                                       <td><img width="40px" height="40px" src="' + profile_img + '" alt="user" class="img-circle">' + data.data[i].driver_name + '</td> \n\
                                                        <td>' + data.data[i].vehicle_name + '</td>\n\
                                                        <td>' + moment(data.data[i].date_of_expense).format("DD-MM-YYYY") + '</td>\n\
\n\                                                     <td>' + data.data[i].amount + '</td></tr>');
                }
                }
                else{
                $('#driver_expense_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }
        
        function get_driver_trip_approval(){
            $('#driver_trip_approval_count_refresh').append('<img id="loading_img" src="'+loader_path+'" />');
        $.ajax({
        url: "{{route('api.trip_approval_list')}}",
                type: "post",
                dataType: "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'user_id':"{{ Auth::user()->id }}"},
                success:function(data){
hide_loader();
                if (data.status && data.data.trip_approval_list.length > 0){
                $('#driver_trip_tbl').html("");
                $('#driver_trip_approval_count').text(data.data.trip_approval_list.length);
                for (let i = 0; i < data.data.trip_approval_list.length; i++){
                var profile_img = "{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}";
                if (data.data.trip_approval_list[i].driver_profile_image){
                profile_img = data.data.trip_approval_list[i].driver_profile_image;
                }
                $('#driver_trip_tbl').append('<tr>\n\
                                                       <td><img width="40px" height="40px" src="' + profile_img + '" alt="user" class="img-circle">' + data.data.trip_approval_list[i].driver_name + '</td> \n\
                                                        <td>' + data.data.trip_approval_list[i].vehicle_name + '</td>\n\
                                                        <td>' + moment(data.data.trip_approval_list[i].opening_time).format("DD-MM-YYYY") + '</td>\n\
\n\                                                     <td>' + moment(data.data.trip_approval_list[i].closing_time).format("DD-MM-YYYY") + '</td></tr>');
                }
                }
                else{
                $('#driver_trip_tbl').html('<tr><td colspan="4">No Record Found</td></tr>')
                }
                }
        });
        }
        
        $(window).on('load', function() {
        $('.ti-minus').click();
        setTimeout(function(){
        get_today_leave();
        get_leave_approval();
        get_assigned_work();
        get_your_leave();
        get_your_today_attendance();
        pending_attendance_approval();
        pending_expense_approval();
        get_holiday_list();
        get_birthday_ani_list();
        get_driver_expense_approval();
        get_driver_trip_approval();
        }, 1000);
        })

    </script>


    <script type="text/javascript">

                $(document).ready(function() {
        @foreach($all_notify_list as $notify)
                $.toast({
                heading: "{{$notify['title']}}",
                        text: "{{$notify['message']}}",
                        position: 'top-right',
                        //loaderBg:'#ff6849',
                        icon: 'info',
                        hideAfter: false,
                        textColor: 'white',
                        stack: 100
                });
        @endforeach
        });</script>
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyDgkVVgz-uXLTW-_UEwHw0CWx9EBdY2L-E" type="text/javascript"></script>

    <script type="text/javascript">
        var markers = [];
        var locations = [
<?php foreach ($graphDetails as $key => $loco) { ?>
            ['<?php echo $loco['name'] . '(' . $loco['remote_punch_reason'] . ')(' . $loco['punch_type'] . ')' ?>', <?php echo $loco['attend_latitude'] ?>, <?php echo $loco['attend_longitude'] ?>, <?php echo $key + 1; ?>],
<?php } ?>
        ];
        var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 6,
                center: new google.maps.LatLng(23.033863, 72.585022),
                mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        var infowindow = new google.maps.InfoWindow();
        var marker, i;
        for (i = 0; i < locations.length; i++) {

        marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map,
                icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/library_maps.png'
        });
        google.maps.event.addListener(marker, 'click', (function (marker, i) {
        return function () {
        infowindow.setContent('<strong>' + locations[i][0] + '</strong>');
        infowindow.open(map, marker);
        }
        })(marker, i));
        markers.push(marker);
        }
    </script>
    @endsection
    <style type="text/css">
        .bg-theme-dark.m-b-15 {
            height: 370px;
        }
        div#leave_app {
            float: left;
            overflow-y: auto;
            height: 270px;
        }
        div#expenseApp {
            float: left;
            overflow-y: auto;
            height: 270px;
            /*width: 410px;*/
        }

    </style>
