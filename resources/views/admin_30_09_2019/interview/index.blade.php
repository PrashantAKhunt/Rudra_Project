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
                <?php
                 $role = explode(',', $access_rule);
                ?>
                <?php 
                if(in_array(3, $role)){
                ?>
                <a href="{{ route('admin.add_interview') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Interviewee Detail</a>
            <?php } ?>
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
                                <th>Action</th>
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
                                    @else
                                    <b class="text-danger">Rejected</b>
                                    @endif
                                </td>
                                <td>
                                <?php if(in_array(2, $role)){
                                ?>
                                <a href="{{ route('admin.edit_interview',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>
                                <?php } ?>
                                <?php if(in_array(5, $role)){
                                ?>
                                <?php 
                                if($interview->emp_status=='selected'){
                                ?>
                                <a href="{{ route('admin.confirm_interview',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-list"></i></a>
                                <?php } ?>
                                <?php } ?>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="12" align="center">
                                    No Records Found !
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
        
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
               
            })
        </script>
        @endsection