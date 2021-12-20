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
                <li><a href="#">{{ $page_title }}</a></li>
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
            <div class="white-box">
                <?php if(in_array(3, $role)){
                    if(Auth::user()->role==config('constants.REAL_HR')){ ?>
                    <a href="{{ route('admin.add_interview') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Interview Detail</a>
                <?php } 
                } ?>
                <p class="text-muted m-b-30"></p>
                <br>                
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Interviewee Id</th>
                                <th>Designation</th>
                                <th>Job Id</th>
                                <th>Contact Number</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Confirm</th>
                                <th>Schedule</th>
                                <th>Marks</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($interview_list)>0)
                            @foreach($interview_list as $interview)
                            <tr>
                                <td>{{ $interview->name }}</td>
                                <td>{{ $interview->interviewee_id }}</td>
                                <td>{{ $interview->title }}</td>
                                <td>{{ $interview->job_id }}</td>
                                <td>{{ $interview->contact_number }}</td>
                                <td>
                                    @if($interview->emp_status=='pending')
                                    <b class="text-warning">Pending</b>
                                    @elseif($interview->emp_status=='selected')
                                    <b class="text-success">Selected</b>
                                    @elseif($interview->emp_status=='completed')
                                    <b class="text-info">Completed</b>
                                    @else
                                    <b class="text-danger">Rejected</b>
                                    @endif
                                </td>
                                <td>
                                    <?php if(in_array(2, $role) && in_array(5, $role)){
                                        if($interview->emp_status != 'selected'){ ?>
                                            <a href="{{ route('admin.edit_interview',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>
                                    <?php } } ?>
                                </td>
                                <td>
                                    <?php if(in_array(5, $role)){
                                        if($interview->emp_status == 'selected'){ ?>
                                        <a href="{{ route('admin.confirm_interview',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-check"></i></a>
                                    <?php } } ?>
                                </td>
                                <td>
                                    <?php if(in_array(5, $role) && $interview->emp_status == 'pending'){ ?>
                                        <a href="{{ route('admin.add_next_interview',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-calendar"></i></a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if(in_array(Auth::user()->id, explode(',',$interview->interviewers)) && $interview->emp_status = 'pending'){ ?>
                                        <a href="{{ route('admin.interview_marks',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary"><i class="fa  fa-file-text-o"></i></a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if(in_array(6, $role)){ ?>
                                        <a href="{{ route('admin.interview_details',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-file"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            @endforeach
                            
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endsection 
        @section('script')
        <script>
            $(document).ready(function () {
               $('#user_table').DataTable({
                   stateSave:true
               });
            })
        </script>
        @endsection