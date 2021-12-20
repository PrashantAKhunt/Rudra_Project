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
                @if($check_own_resign==0 && Auth::user()->role!=1)
                <a href="{{ route('admin.add_resign') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Submit Resign Request</a>
                <p class="text-muted m-b-30"></p>
                <br>
                @endif
                <div class="table-responsive">
                    <table id="resign_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Reason</th>
                                <th>Resign Date</th>
                                <th>Expected Relieving Date</th>
                                <th>Actual Relieving Date</th>
                                <th>Relieving Letter</th>
                                <th>Remaining Notice Period Days</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resign_list as $resign)
                            <tr>
                                <td>{{ $resign->name }}</td>
                                <td>{{ $resign->reason }}</td>
                                <td>{{ date('d-m-Y',strtotime($resign->created_at)) }}</td>
                                <td>
                                    @if ($resign->expected_relieving_date)
                                    {{ date('d-m-Y',strtotime($resign->expected_relieving_date)) }}
                                    @else
                                    --
                                    @endif
                                </td>
                                <td>
                                    @if ($resign->actual_relieving_date)
                                    {{ date('d-m-Y',strtotime($resign->actual_relieving_date)) }}
                                    @else
                                    --
                                    @endif
                                </td>

                                <td>
                                    @if($resign->relieving_letter)
                                    <a target="_blank" href="<?php echo asset('storage/' . str_replace('public/', '', $resign->relieving_letter)); ?>"><i class="fa fa-cloud-download"></i></a>
                                    @endif
                                </td>
                                <td>
                                    {{ $resign->notice_period_days }}
                                </td>
                                <td>
                                    @if($resign->status=="Pending")
                                    <span class="text-warning">{{ $resign->status }}</span>
                                    @elseif($resign->status=="Approved")
                                    <span class="text-success">{{ $resign->status }}</span>
                                    @elseif($resign->status=="Retain")
                                    <span class="text-danger">{{ $resign->status }}</span>
                                    @else
                                    <span class="text-warning">{{ $resign->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" onclick="get_resign_detail({{$resign->id}});" data-target="#resign_view" data-toggle="modal" class="btn btn-primary btn-rounded" title="View"><i class="fa fa-eye"></i></a>
                                    @if($resign->user_id==Auth::user()->id && in_array(2,$permission_arr) && $resign->status == 'Pending')
                                    <a href="{{ route('admin.edit_resign',['id'=>$resign->id]) }}" class="btn btn-primary btn-rounded" title="Edit"><i class="fa fa-edit"></i></a>
                                    @endif

                                    @if($resign->user_id==Auth::user()->id && $resign->first_approval_status == 'Pending' && $resign->status == 'Pending')
                                    <a href="#" onclick="revoked_resign({{$resign->id}});" data-target="#revoke_resign" data-toggle="modal" class="btn btn-primary btn-rounded" title="Revoke"><i class="fa fa-sign-in"></i></a>
                                    @endif

                                    @if(Auth::user()->role==9 && $resign->status=='Approved')
                                    <a onclick="relieving_date({{$resign->id}});" data-target="#relieving_date" data-toggle="modal" href="#" class="btn btn-success btn-rounded" title="Relieving Date"><i class="fa fa-calendar"></i></a>
                                    <a onclick="relieving_letter({{$resign->id}});" data-target="#relieving_letter" data-toggle="modal" href="#" class="btn btn-success btn-rounded" title="Relieving Letter"><i class="fa fa-cloud-upload"></i></a>
                                    @endif

                                    @if((in_array(5,$permission_arr) || in_array(6,$permission_arr)) && in_array(2,$permission_arr) && $resign->status=='Pending')
                                    @if( $resign->first_approval_status=='Pending' && $resign->user_id!=Auth::user()->id )
                                    <a href="{{ route('admin.approve_resign',['id'=>$resign->id]) }}" class="btn btn-success btn-rounded" title="Approve"><i class="fa fa-check"></i></a>
                                    <a onclick="retain_resign({{$resign->id}});" data-target="#retain_resign" data-toggle="modal" href="#" class="btn btn-success btn-rounded" title="Retain"><i class="fa fa-sign-in"></i></a>
                                    @elseif(Auth::user()->role==9 && $resign->status=='Pending' && $resign->second_approval_status=='Pending' && $resign->first_approval_status=='Approved')
                                    <a onclick="get_approve_user_resign({{$resign->id}});" data-target="#approve_resign_view" data-toggle="modal" href="#" class="btn btn-success btn-rounded" title="Approve"><i class="fa fa-check"></i></a>
                                    <a onclick="retain_resign({{$resign->id}});" data-target="#retain_resign" data-toggle="modal" href="#" class="btn btn-success btn-rounded" title="Retain"><i class="fa fa-sign-in"></i></a>
                                    @elseif(Auth::user()->role==1 && $resign->status=='Pending' && $resign->second_approval_status=='Approved' && $resign->first_approval_status=='Approved' && $resign->final_approval_status=='Pending')
                                    <!--<a href="{{ route('admin.approve_resign',['id'=>$resign->id]) }}" class="btn btn-success btn-rounded" title="Approve"><i class="fa fa-check"></i></a>-->
                                    <a onclick="retain_resign({{$resign->id}});" data-target="#retain_resign" data-toggle="modal" href="#" class="btn btn-success btn-rounded" title="Retain"><i class="fa fa-sign-in"></i></a>
                                    @endif
                                    @endif

                                    @if(Auth::user()->role==config('constants.SuperUser') && $resign->final_approval_status=='Pending')
                                    <a onclick="get_superadmin_approve_user_resign({{$resign->id}});" data-target="#superuser_approval_modal" data-toggle="modal" href="#" class="btn btn-success btn-rounded" title="Approve"><i class="fa fa-check"></i></a>
                                    @endif

                                    @if(Auth::user()->role==config('constants.REAL_HR') && $resign->status=='Approved')
                                    <a href="{{ route('admin.exit_interview_sheet',['id'=>$resign->id]) }}" title="View Sheet" class="btn btn-rounded btn-info"><i class="fa fa-eye"></i></a>
                                    <br>
                                    @if( $resign->exit_sheet_uploaded=='Yes' )
                                    <a target="_blank" title="Download sheet" class="btn btn-info btn-rounded" href="<?php echo asset('storage/' . str_replace('public/', '', $resign->exit_sheet)); ?>"><i class="fa fa-download"></i></a>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->
        </div>
        <div class="modal fade" id="resign_view" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Resignation Detail</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <p><b>Reason:</b><span id="resign_reason"></span></p>
                            <p><b>Resignation Details:</b>
                            <p id="resign_detail"></p>
                            <p><b>Revoked Details:</b>
                            <p id="revoked_details"></p>
                            <p><b>HR Approval Note:</b>
                            <p id="hr_approval_note"></p>
                            <p><b>Super User Approval Note:</b>
                            <p id="user_user_approval_note"></p>
                            <p><b>Retain Note:</b>
                            <p id="retain_note"></p>
                            <br />
                            <p><b>Asset Access Detail</b></p>
                            <div class="form-group asset_detail">
                            </div>
                            <p><b>Loan Detail</b></p>
                            <div class="form-group loan_detail">
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Salary:</b><span id="salary"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Salary Note:</b><span id="salary_note"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Leave:</b><span id="leave"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Leave Note:</b><span id="leave_note"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Advance:</b><span id="advance"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Advance Note:</b><span id="advance_note"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>List Assets:</b><span id="list_assets"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Assets Note:</b><span id="list_assets_note"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>List Files:</b><span id="list_files"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Files Note:</b><span id="list_files_note"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Eligibility Policy:</b><span id="eligibility_policy"></span></p>
                            </div>
                            <div class="form-group col-md-6">
                                <p><b>Policy Note:</b><span id="eligibility_policy_note"></span></p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="revoked_resign" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Revoke Detail</h4>
                    </div>
                    <form action="{{ route('admin.revoked_resign') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group ">
                                <input type="hidden" name="revoke_resign_id" id="revoke_resign_id" value="">
                                <textarea class="form-control" rows="4" name="revoked_details" id="revoked_details" required="required"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.resign') }}'" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="approve_resign_view" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Approve Resignation Detail</h4>
                    </div>
                    <form action="{{ route('admin.confirm_resign') }}" id="confirm_resign" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="resign_id" id="resign_id">
                            <div class="row">


                                <div class="form-group ">
                                    <label>Note <span class="error">*</span></label>
                                    <textarea class="form-control" rows="5" name="note" id="note" spellcheck="false" required="required"></textarea>
                                    <br>
                                    <label>Please Select HandOver Employee Name <span class="error">*</span></label>
                                    @if(!empty($employee))
                                    <select name="hand_over_user_id" class="form-control" required="required">
                                        <option value="">Select Employee</option>
                                        @foreach($employee as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                    <br>
                                    <div class="form-group col-md-6">
                                        <label>Salary <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="salary" required="required">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Note <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="salary_note">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Leave <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="leave">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Note <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="leave_note">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Advance <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="advance">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Note <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="advance_note">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>List of Assets <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="list_assets">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Note <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="list_assets_note">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>List of Files <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="list_files">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Note <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="list_files_note">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Eligibility as per Policy <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="eligibility_policy">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Note <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="eligibility_policy_note">
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Approve Resign</button>
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="superuser_approval_modal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Approve Resignation Detail</h4>
                    </div>
                    <form action="{{ route('admin.superadmin_confirm_resign') }}" id="superadmin_confirm_resign" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="super_user_resign_id" id="super_user_resign_id">
                            <div class="form-group ">
                                <label>Note</label>
                                <textarea class="form-control" rows="5" name="super_admin_note" id="super_admin_note" spellcheck="false" required="required"></textarea>
                                <br>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Approve Resign</button>
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="relieving_date" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Relieving Date</h4>
                </div>
                <form action="{{ route('admin.relieving_date') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group ">
                            <div class="col-sm-6">
                                <input type="hidden" name="resign_id" id="resign_id">
                                <input type="text" name="relieve_date" id="relieve_date" class="form-control" value="" required="required" readonly="readonly">
                            </div>
                        </div>
                        <br />
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.resign') }}'" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="relieving_letter" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Relieving Letter</h4>
                </div>
                <form action="{{ route('admin.relieving_letter') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group ">
                            <div class="col-sm-12">
                                <input type="hidden" name="resign_id" id="resign_id">
                                <label>Relieving Letter <span class="text-muted">Allowed file extensions are png, jpg, jpeg</span></label>
                                <div>
                                    <input type="file" name="relieve_letter" class="form-control" id="relieve_letter" />
                                </div>
                            </div>
                            <br />
                        </div>
                        <br />
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.resign') }}'" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="retain_resign" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Retain Detail</h4>
                </div>
                <form action="{{ route('admin.retain_resign') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group ">
                            <input type="hidden" name="resign_id" id="resign_id" value="">
                            <textarea class="form-control" rows="4" name="retain_details" id="retain_details" required="required"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.resign') }}'" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    function get_resign_detail(id) {
        $('#resign_reason').html('');
        $('#resign_detail').html('');
        $('#hr_approval_note').text('');
        $('#user_user_approval_note').text('');
        $('#retain_note').text('NA');
        $('#salary').html('');
        $('#salary_note').text('');
        $('#leave').html('');
        $('#leave_note').text('');
        $('#advance').html('');
        $('#advance_note').text('');
        $('#list_assets').html('');
        $('#list_assets_note').text('');
        $('#list_files').html('');
        $('#list_files_note').text('');
        $('#eligibility_policy').html('');
        $('#eligibility_policy_note').text('') || $('#eligibility_policy_note').text('NA');
        $.ajax({
            url: "{{ route('admin.get_resign_detail') }}",
            type: "post",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: id
            },
            success: function(data) {
                if (data.status) {
                    $('#resign_reason').html(data.data.resign_list.reason);
                    $('#resign_detail').html(data.data.resign_list.resign_details);
                    $('#revoked_details').html(data.data.resign_list.revoked_details);
                    $('#hr_approval_note').text(data.data.resign_list.second_note);
                    $('#user_user_approval_note').text(data.data.resign_list.final_note);
                    if(data.data.resign_list.salary){
                        $('#salary').html(data.data.resign_list.salary);
                    }else{
                        $('#salary').text(' N/A ');
                    }
                    if(data.data.resign_list.salary_note){
                        $('#salary_note').html(data.data.resign_list.salary_note);
                    }else{
                        $('#salary_note').text(' N/A ');
                    }
                    if(data.data.resign_list.leave){
                        $('#leave').html(data.data.resign_list.leave);
                    }else{
                        $('#leave').text(' N/A ');
                    }
                    if(data.data.resign_list.leave_note){
                        $('#leave_note').html(data.data.resign_list.leave_note);
                    }else{
                        $('#leave_note').text(' N/A ');
                    }
                    if(data.data.resign_list.advance){
                        $('#advance').html(data.data.resign_list.advance);
                    }else{
                        $('#advance').text(' N/A ');
                    }
                    if(data.data.resign_list.advance_note){
                        $('#advance_note').html(data.data.resign_list.advance_note);
                    }else{
                        $('#advance_note').text(' N/A ');
                    }
                    if(data.data.resign_list.list_assets){
                        $('#list_assets').html(data.data.resign_list.list_assets);
                    }else{
                        $('#list_assets').text(' N/A ');
                    }
                    if(data.data.resign_list.list_assets_note){
                        $('#list_assets_note').html(data.data.resign_list.list_assets_note);
                    }else{
                        $('#list_assets_note').text(' N/A ');
                    }
                    if(data.data.resign_list.list_files){
                        $('#list_files').html(data.data.resign_list.list_files);
                    }else{
                        $('#list_files').html(' N/A ');
                    }
                    if(data.data.resign_list.list_files_note){
                        $('#list_files_note').html(data.data.resign_list.list_files_note);
                    }else{
                        $('#list_files_note').text(' N/A ');
                    }
                    if(data.data.resign_list.eligibility_policy){
                        $('#eligibility_policy').html(data.data.resign_list.eligibility_policy);
                    }else{
                        $('#eligibility_policy').html(' N/A ');
                    }
                    if(data.data.resign_list.eligibility_policy_note){

                        $('#eligibility_policy_note').html(data.data.resign_list.eligibility_policy_note);
                    }else{
                        $('#eligibility_policy_note').html(' N/A ');
                    }

                    if (data.data.resign_list.status == 'Retain') {
                        $('#retain_note').text(data.data.resign_list.final_note);
                    }
                    var asset;
                    var loan;
                    $('.asset_detail').html("");
                    $('.loan_detail').html("");
                    $.each(data.data.asset, function(key, value) {
                        $('.asset_detail').append('<label>' + key + ' : </label>' + value + '<br/>');
                    });
                    $.each(data.data.loan, function(key, value) {
                        $('.loan_detail').append('<label>' + key + ' : </label>' + value + '<br/>');
                    });
                }
            }
        });
    }

    function revoked_resign(id) {
        $('#revoke_resign_id').val(id);
        $('#revoked_resign').modal();
    }

    function get_approve_user_resign(id) {
        $('#approve_resign_view #resign_id').val(id);
    }

    function relieving_date(id) {
        $('#relieving_date #resign_id').val(id);
    }

    function relieving_letter(id) {
        $('#relieving_letter #resign_id').val(id);
    }

    function retain_resign(id) {
        $('#retain_resign #resign_id').val(id);
    }

    function get_superadmin_approve_user_resign(id) {
        $('#super_user_resign_id').val(id)
    }
    $(document).ready(function() {
        $('#resign_table').DataTable();
        jQuery('#relieve_date').datepicker({
            startDate: "+0d",
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd"
        });
    });
    jQuery('#confirm_resign').validate({
        ignore: [],
        rules: {
            hand_over_user_id: {
                required: true,
            },
            salary: {
                required: true
            },
            salary_note: {
                required: true
            },
            leave: {
                required: true
            },
            leave_note: {
                required: true
            },
            advance: {
                required: true
            },
            advance_note: {
                required: true
            },
            list_assets: {
                required: true
            },
            list_assets_note: {
                required: true
            },
            list_files: {
                required: true
            },

            list_files_note: {
                required: true
            },
            eligibility_policy: {
                required: true
            },
            eligibility_policy_note: {
                required: true
            },

        }
    });
</script>
@endsection