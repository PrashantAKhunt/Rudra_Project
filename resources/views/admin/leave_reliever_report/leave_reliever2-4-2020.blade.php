<?php
    use Illuminate\Support\Facades\Config;
?>

@extends('layouts.admin_app')

@section('content')

<link href="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<link href="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
    </div>
    <div class="row">

        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('admin.leave_reliever_report') }}" id="attendance_frm" method="post" class="form-material" accept-charset="utf-8">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Date<label class="serror"></label> </label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control timeseconds shawCalRanges" name="date" id="date" value="<?php echo!empty($date) ? $date : "" ?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="col-sm-3 control-label" >Select User</label>
                                        <div class="col-sm-9">
                                            @if(!empty($user))
                                                <select name="user_id[]" id="user_id" class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose">
                                                    @foreach($user as $key => $value)
                                                        <?php if ( !empty($selectedUser) && in_array($key, $selectedUser)) { ?>
                                                            <option selected value="{{$key}}">{{$value}}</option>
                                                        <?php } else { ?>
                                                            <option value="{{$key}}">{{$value}}</option>
                                                        <?php } ?>    
                                                    @endforeach
                                                </select>                                   
                                            @endif
                                        </div>
                                        <div class="checkbox checkbox-success pull-right">
                                            <input id="select_all" <?php echo !empty($selectedUser)? "checked" : ""; ?> type="checkbox">
                                            <label for="select_all">Select All</label>
                                        </div>
                                    </div>
                                </div>                                    
                            </div>
                            <div class="row" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Reports Type<label class="serror"></label> </label>
                                        <div class="col-sm-9">
                                            <select class="form-control valid" id="report_type" name="report_type">
                                                <option <?php if($report_type == "all") { ?> selected <?php } ?> value="all">All</option>
                                                <option <?php if($report_type == "individual") { ?> selected <?php } ?> value="individual">Individual</option>
												<option <?php if($report_type == "user") { ?> selected <?php } ?> value="user">User</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>

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
            <div class="white-box">
                <a style="display: none;" href="{{ $csv_data }}"  class="btn btn-primary pull-right"><i class="fa fa-download "></i> Download CSV</a>
                <p class="text-muted m-b-30"></p>
                </br>
                <div class="table-responsive">
                    <table id="trip_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Reliever User Name</th>
                                <th>Subject</th>
                                <th>Assign Work Detail</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Leave Status</th>
                                <th>Status</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($records[0])){
                                foreach ($records as $key => $value) { 
								//dd($value);
								?>
                                <tr>
                                    <td><?php echo $value->name; ?></td>
                                    <td><?php echo $value->reliever_name; ?></td>
                                    <td><?php echo $value->subject; ?></td>
                                    <td><?php echo $value->assign_work_details; ?></td>
                                    <td><?php echo $value->start_date; ?></td>
                                    <td><?php echo $value->end_date; ?></td>
                                    <td>
                                    <?php 
                                        if($value->leave_status==1)
                                        {
                                            echo '<b class="text-warning">Pending</b>';
                                        }
                                        if($value->leave_status==2)
                                        {
                                            echo '<b class="text-success">Approved</b>';
                                        }
                                        if($value->leave_status==3)
                                        {
                                            echo '<b class="text-danger">Rejected</b>';
                                        }
                                        if($value->leave_status==4)
                                        {
                                            echo '<b class="text-danger">Canceled</b>';
                                        }
                                    ?>    
                                    </td>
                                    <td><?php echo $value->status; ?></td>
                                </tr>   
                                <?php }
                            } else { ?>
                                 
                            <?php } ?>    
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>    
    </div>
</div>
@endsection

@section('script')		
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/moment/moment.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/select2/dist/js/select2.full.min.js" type="text/javascript"></script>

<script>
$(document).ready(function () {
    $('.select2').select2();
	$('#trip_table').DataTable({
		dom: 'Bfrtip',buttons: [
            'copy', 'csv', 'excel', 'print'
        ],
  stateSave: true
} );
    $('#select_all').click(function() {
        if($(this).prop("checked") == true){
            $('#user_id').select2('destroy');   
            $('#user_id option').prop('selected', true);
            $('#user_id').select2();
        }else{
            $('#user_id').select2('destroy');   
            $('#user_id option').prop('selected', false);
            $('#user_id').select2();
        }
    });
    $('.showdropdowns').daterangepicker({
        showDropdowns: true,
          timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'MM/DD/YYYY h:mm A'
            }
    }); 

    $('.shawCalRanges').daterangepicker({
        showDropdowns: false,
        timePicker: true,
        timePickerIncrement: 1,
        timePicker24Hour: true,
        locale: {
        format: 'D/M/YYYY H:mm'
    },
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    alwaysShowCalendars: true,
    });

});
</script>
@endsection