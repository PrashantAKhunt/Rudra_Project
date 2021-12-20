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
                <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
    </div>
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
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('admin.upload_exitInterviewSheet') }}" method="post" class="form-material" enctype="multipart/form-data" >
                        @csrf
                        <div class="form-body">
                        <input type="hidden" name="resign_id" value="{{ $resign_id }}">
                            <div class="row">
                                <!--  -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="col-sm-3 control-label">Upload Sheet</label>
                                    <div class="col-sm-9">
                                        <input type="file" name="exit_sheet" class="form-control exit_sheet" required />
                                        
                                    </div>    
                                    </div>
                                </div>
                                <!--  -->                                     
                            </div>
                            @if( count($asset_list) > 0)
                            <div class="form-actions">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Upload</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-12 col-sm-12">
            
            <div class="white-box">

                <p class="text-muted m-b-30"></p>
                </br>
                <div class="table-responsive">
                <input type="hidden" name="resign_id" value="{{ $resign_id }}">
                    <table id="exit_interview_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Asset Name</th>
                                <th>Asset Number</th>                                
                                <th>Handover Name</th>
                                <th>Taker Name</th>
                                <th>HR</th>
                            
                                                            
                            </tr>
                        </thead>
                        <tbody>
                        @if( count($asset_list) > 0)
                            @foreach($asset_list as $key => $asset)
                            <tr>
                                <td>{{ $key+ 1 }}</td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->asset_1 }}</td>
                                <td>
                                   
                                 <b class="text-primary"> {{ $asset->user_name }} <br> Confirmed </b>
                                </td>
                                <td>
                                    @if( $asset->hr_access_user_id )
                                        <b class="text-success">{{ $asset->taker_name }}<br>Assigned</b> 
                                    @else
                                        <a onclick="asset_assignClick({{ $asset->asset_id }},{{ $asset->id}} );" data-target="#asset_assign_view" data-toggle="modal" href="#" class="btn btn-success btn-rounded" title="Assign"><i class="fa fa-check"></i></a>
                                    @endif 
                                </td>
                                <td>
                                    @if( $asset->hr_user_id )
                                        <b class="text-info"> {{ $asset->hr_name   }}
                                            <br> Confirmed </b> 
                                    @else
                                        <b class="text-warning"> Pending </b> 
                                    @endif 
                                </td>
                                
                            </tr>

                            @endforeach
                            @endif 
                        </tbody>
                    </table>
                </div>
            </div> 
                       <!--  -->
            <div class="modal fade" id="asset_assign_view" role="dialog">
                     <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Asset Assign</h4>
                    </div>
                    <form action="{{ route('admin.asset_takerByHr') }}" id="asset_takerByHr" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="resign_id" id="resign_id" value="{{ $resign_id }}"> 
                            <input type="hidden" name="asset_id" id="asset_id">  
                            <input type="hidden" name="asset_access_id" id="asset_access_id">      
                                <div class="form-group "> 
                
                                    <label>Please Select Employee</label>  
                                    @if(!empty($users_list))
                                        <select name="user_id" class="form-control" required="required">
                                        <option value="">Select Employee</option>
                                            @foreach($users_list as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" >Submit</button>
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                    </div>
                </div>
        </div>
                       <!--  -->
        </div>    
    </div>
</div>
@endsection

@section('script')		

<script>
$(document).ready(function () {
    $('.select2').select2();
	
    $('#exit_interview_table').DataTable({
		dom: 'Bfrtip',buttons: [
            'print'
        ],
        stateSave: true
    });

   

});
</script>

<script>


function asset_assignClick(asset_id, asset_access_id){
       //$('#resign_id').val('');
        $('#asset_access_id').val('');
        $('#asset_id').val('');

        //$('#resign_id').val(id);
        $('#asset_access_id').val(asset_access_id);
        $('#asset_id').val(asset_id);
    
    }
</script>
@endsection