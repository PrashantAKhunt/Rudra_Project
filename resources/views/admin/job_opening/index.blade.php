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
                $role = [];
                if(!empty($access_rule)){
                    $role = explode(',', $access_rule);
                }
                if(in_array(3, $role)) {
                ?>
                <a href="{{ route('admin.add_opening') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New Opening</a>
                <?php }?>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Job Id</th>
                                <th>Role</th>
                                <th>Location</th>
                                <th>Package</th>
                                <th>Experience Level</th>
                                <th>Job Type</th>
                                <th>Posted Date</th>
                                <th>Consultancy List</th>
                                
                                @if(in_array(2,$role))
                                    <th>Status</th>
                                    <th>Urgency Requirement</th>
                                    <th>Qualification</th>
                                    <th>Edit</th>
                                    <th>Close</th>
                                @endif
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($job_opening_list)>0)
                            @foreach($job_opening_list as $job_opening)
                            <tr>
                                <td>{{ $job_opening->title }}</td>
                                <td>{{ $job_opening->job_id }}</td>
                                <td>{{ $job_opening->role }}</td>
                                <td>{{ $job_opening->location }}</td>
                                <td>{{ $job_opening->package }}</td>
                                <td>{{ $job_opening->experience_level }}</td>
                                <td>{{ $job_opening->type }}</td>
                                <td>{{ date('d-m-Y',strtotime($job_opening->posting_date)) }}</td>
                                <td>{{ $job_opening->consultant_list }}</td>
                                <td>
                                    @if(in_array(2,$role))
                                    @if($job_opening->status=='Enable')
                                    <a href="{{ route('admin.opening_change_status',['id'=>$job_opening->id,'status'=>'Disable']) }}" class="btn btn-success" title="Click To Disable">Enabled</a>
                                    @else
                                    <a href="{{ route('admin.opening_change_status',['id'=>$job_opening->id,'status'=>'Enable']) }}" class="btn btn-danger" title="Click To Enable">Disabled</a>
                                    @endif
                                    @endif
                                </td>
                                <td>{{ $job_opening->urgency_requirement }}</td>
                                <td>{{ $job_opening->qualification }}</td>
                                <td> 
                                    @if(in_array(2,$role))
                                    <a href="{{ route('admin.edit_opening',['id'=>$job_opening->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>
                                    @endif
                                </td>
                                <td> 
                                    @if(in_array(5,$role) && $job_opening->close == 'NO')
                                        <a href="<?php echo url('close_opening').'/'.$job_opening->id.'/YES'; ?>" class="btn btn-rounded btn-danger"><i class="fa fa-times"></i></a>
                                    @endif
                                </td>
                                <td>
                                <a href="{{ route('admin.job_candidates',['id'=>$job_opening->id]) }}" title="view candidates" class="btn btn-rounded btn-info"><i class="fa fa-eye"></i></a>
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
                   $('#user_table').DataTable({
                     stateSave: true
                });
            })
        </script>
        @endsection