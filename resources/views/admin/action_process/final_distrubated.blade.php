@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">

                <div class="row">
                    <div class="col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.update_distrubated_task') }}" id="add_process" method="post" >
                            @csrf
                            
                            @if(count($reject_div) > 0)
                           <div class="row" id="append_reject">
                            <p>-> Total hours of work assumed by prime user : {{ $reject_div['total_hour'] }} hours</p> 
                            <p>-> {{ $reject_div['reject_user_name'] }} is expecting work load of {{ $reject_div['expect_percentage'] }}% and prime user has rejected it with reason below</p> 
                            <p>-> prime user rejection reason: "{{ $reject_div['reject_note'] }}"</p> 
                           <input type="hidden" id="total_hour" value="{{ $reject_div['total_hour'] }}">
                           </div>
                         
                           <hr>
                           <br>
                            @endif
                            <div class="row">
                            @if($distrubuted_llist->count() > 0)
                            @foreach($distrubuted_llist as $key => $list)
                                <div class="col-md-4">
                                    <div class="form-group "> 
                                        <label>{{ $list->name }}</label> 
                                        <input type="hidden" name="distrubuted_work_id[{{ $key }}]" value="{{ $list->id }}">
                                        <input type="hidden" name="support_employee[{{ $key }}]" value="{{ $list->support_employee_id }}">
                                        <input type="number" class="form-control" step="any" title="percentage" placeholder="percentage" name="task_percentage[{{ $key }}]" id="find_percentage_{{ $key }}" onchange="set_hour(`{{ $key }}`)"  required value="{{ $list->task_percentage }}" /> 
                                        <br>
                                        <input type="number" class="form-control" step="any" title="hour" placeholder="hour" name="task_hour[{{ $key }}]" id="find_hour_{{$key}}" onchange="set_percentage(`{{$key}}`)" required value="{{ $list->task_hour }}" /> 
                                    </div>
                                    <input type="hidden" name="prime_id" value="{{ $list->inward_outward_prime_action_id }}">
                                </div>
                            @endforeach
                            @endif
                            </div>
                            
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.managment_view_list') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
@section('script')
<script>
//================================
function set_hour(id){
    var total_hour = Number($("#total_hour").val());
    var check_val = $("#find_percentage_"+id).val();
    var set_val = total_hour*Number(check_val) / 100 ;
    $("#find_hour_"+id).val(set_val);

}
//===================================
function set_percentage(id){
    var total_hour = Number($("#total_hour").val());
    var check_val = $("#find_hour_"+id).val();
    var set_val = 100*Number(check_val) / total_hour ;
    $("#find_percentage_"+id).val(set_val);

}
 
</script>
@endsection
