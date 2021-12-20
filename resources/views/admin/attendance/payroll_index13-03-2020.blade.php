@extends('layouts.admin_app')

@section('content')
<style>
div.dt-buttons{
position:relative;
float:center;
}
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="#">sss {{ $page_title }}</a></li>
            </ol>
        </div>
    </div>
    <form action="{{ route('admin.payroll_approve_all') }}" id="payroll_approve_all" method="post">
    @csrf
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
            <button type="submit" class="btn btn-primary pull-left"> <i class="fa fa-check"></i> Approve All</button>    
			<a href="{{ route('admin.finance_report') }}" class="btn btn-primary pull-right"><i class="fa fa-file"></i> Payroll Report</a>
                <p class="text-muted m-b-30"></p>
                </br>                
                <div class="table-responsive">
                    <table id="payroll_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox"  name="itemselectionsAll[]" id="select-all" onClick="toggle(this)" /></th>
                                <th>User</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Basic Salary</th>
                                <th>HRA</th>
                                <th>Others</th>
                                <th>Food</th>
                                <th>Working Day</th>
                                <th>EMP Working Day</th>
                                <th>Total Leave</th>
                                <th>Sandwich Leave</th>
                                <th>Unpaid Leave</th>
                                <th>Unpaid LA</th>
                                <th>PT</th>
                                <th>PF</th>
                                <th>Loan Installment</th>
                                <th>Extra Loan/Deduction Amount</th>
                                <th>Extra Loan/Deduction Detail</th>
                                <th>Penalty</th>
                                <th>Manual Penalty</th>
                                <th>Penalty Note</th>
                                <th>Payable Salary</th>
                                <th>Gross Salary</th>                                
                                <th>Your Approval</th>
                                <th>Final Approval</th>
                                <th>Generated Date</th>
                                <th>Loan Installment Pause/Start</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>    
    </div>
    </form>
</div>

<div id="reject_model" class="modal fade">
    <div class="modal-dialog modal-lg">
        <form method="post" action="{{ route('admin.payroll_reject') }}">
            @csrf
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Rejection Reason</h3>
            </div>
            <div id="model_data" class="modal-body">
                <input type="hidden" name="payroll_id" id="payroll_id" value="0" />
                <label>Reason</label>
                <textarea class="form-control" name="reject_note" id="reject_note"></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Reject</button>
                
            </div>
        </div>
        </form>
    </div>
</div>
<div id="reject_reason_model" class="modal fade">
    <div class="modal-dialog modal-lg">
        
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Rejection Reason</h3>
            </div>
            <div id="model_data" class="modal-body">
                <p id="reject_reason_data"></p>
            </div>
            
        </div>
        
    </div>
</div>
@endsection

@section('script')		
<script>
    function set_reject_reason(data){
        $('#reject_reason_data').html(data);
    }
    $(document).ready(function () {
        
        var table = $('#payroll_table').DataTable({
			"pageLength": 50,
            "stateSave": true,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            
            "dom": 'lBfrtip',
            "buttons": [
                'csv', 'excel'
            ],
            "order":[[23,'desc']],
            "ajax": {
                url: "<?php echo route('admin.get_payroll_list'); ?>",
                type: "GET",
            },
            "columns": [
                {"targets": 0, "searchable": false,
                    "orderable": false,
                    "sClass": "text-center",
                    "render": function (data, type, row) {
                    var id = row.id;
                    var action_html = '';
                    @if(Auth::user()->role==config('constants.REAL_HR'))
                        if(row.first_approval_status=='Pending'){
                            action_html = '<input type="checkbox" name="itemselections[]" id="itemselections-' + id + '" value="' + id + '">';
                        }
                    @elseif(Auth::user()->role==config('constants.ASSISTANT'))
                        if(row.first_approval_status=='Approved' && row.second_approval_status=='Pending'){
                            action_html = '<input type="checkbox" name="itemselections[]" id="itemselections-' + id + '" value="' + id + '">';
                        }
                    @elseif(Auth::user()->role==config('constants.Admin'))
                        if(row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Pending'){
                            action_html = '<input type="checkbox" name="itemselections[]" id="itemselections-' + id + '" value="' + id + '">';
                        }
                    @elseif(Auth::user()->role==config('constants.SuperUser'))
                        if(row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Approved' && row.fourth_approval_status=='Pending'){
                            action_html = '<input type="checkbox" name="itemselections[]" id="itemselections-' + id + '" value="' + id + '">';
                        }
                    @else
                        if(row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Approved' && row.fourth_approval_status=='Approved' && row.fifth_approval_status=='Pending'){
                            action_html = '<input type="checkbox" name="itemselections[]" id="itemselections-' + id + '" value="' + id + '">';
                        }
                        @endif
                    return action_html;
                    }
                },
                {"targets": 1, "searchable": true, "data": "name"},
                {"targets": 2, "searchable": true, "data": "month"},
                {"targets": 3, "searchable": true, "data": "year"},
                {"targets": 4, "searchable": true, "data": "basic_salary"},
                {"targets": 5, "searchable": true, "data": "hra"},
                {"targets": 6, "searchable": true, "data": "others"},
                {"targets": 7, "searchable": true, "data": "food"},
                {"targets": 8, "searchable": true, "data": "working_day"},
                {"targets": 9, "searchable": true, "data": "employee_working_day"},
                {"targets": 10, "searchable": true, "data": "total_leave"},
                {"targets": 11, "searchable": true, "data": "total_sandwich_leave"},
                {"targets": 12, "searchable": true, "data": "unpaid_leave"},
                {"targets": 13, "searchable": true, "data": "unpaid_leave_amount"},
                {"targets": 14, "searchable": true, "data": "professional_tax"},
                {"targets": 15, "searchable": true, "data": "pf"},
                {"targets": 16, "searchable": true, "data": "loan_installment"},
                {"targets": 17, "searchable": true, "data": "extra_loan_amount"},
                {"targets": 18, "searchable": true, "data": "extra_loan_details"},
                {"targets": 19, "searchable": true, "data": "penalty"},
                {"targets": 20, "searchable": true, "data": "manual_penalty"},
                {"targets": 21, "searchable": true, "data": "penalty_note"},
                {"targets": 22, "searchable": true, "data": "payable_salary"},
                {"targets": 23, "searchable": false, "orderable": false,"render": function (data, type, row) {
                        var amt=row.gross_salary;
                        return amt.toFixed(2);
                }},
                {"targets": 24,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = '';
                        @if(Auth::user()->role==config('constants.REAL_HR'))
                            if(row.first_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.first_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else{
                                out='<b class="text-success">Approved</b>';
                            }
                        @elseif(Auth::user()->role==config('constants.ASSISTANT'))
                            if(row.second_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.second_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else{
                                out='<b class="text-success">Approved</b>';
                            }
                        @elseif(Auth::user()->role==config('constants.Admin'))
                            if(row.third_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.third_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else{
                                out='<b class="text-success">Approved</b>';
                            }
                        @elseif(Auth::user()->role==config('constants.SuperUser'))
                            if(row.fourth_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.fourth_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else{
                                out='<b class="text-success">Approved</b>';
                            }
                        @else
                            if(row.fifth_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.fifth_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else{
                                out='<b class="text-success">Approved</b>';
                            }
                        @endif
                        /*if(row.is_locked=='NO'){
                            out += '<a href="#" data-href="<?php //echo url('lock_payroll') ?>'+'/'+id+'" onclick="set_lock(this);" class="btn btn-success" title="Set Lock">No</a>';
                        } else {
                            out += '<span class="btn btn-danger" title="Change Status">Yes</span>';
                        }*/
                        return out;
                    }
                },
                    {"targets": 25,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = '';                        
                            if(row.main_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.main_approval_status=='Rejected'){
                                out='<a href="#" onclick="set_reject_reason(&apos;'+row.reject_note+'&apos;)" class="btn btn-danger" data-toggle="modal" data-target="#reject_reason_model">Rejected</b>';
                            }else{
                                out='<b class="text-success">Approved</b>';
                            }                       
                        return out;
                    }
                },  
                {"targets": 26, "data": "date"},
                {"targets":27,"searchable":false,"orderable":false,"render":function(data,type,row){
                        out="";
                        @if(Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.SuperUser'))
                        if(row.loan_installment>0 && row.loan_pause==0 && row.is_locked=='NO'){
                            out +='<button type="button" title="Pause Loan" onclick="pause_loan('+row.id+')" class="btn btn-danger btn-rounded"><i class="fa fa-stop"></i></button>';
                        }
                        if(row.loan_installment==0 && row.loan_pause==1 && row.is_locked=='NO'){
                            out +='<button type="button" title="Resume Loan" onclick="resume_loan('+row.id+')" class="btn btn-danger btn-rounded"><i class="fa fa-dollar"></i></button>';
                        }
                        @else
                            if(row.loan_pause==1){
                                out +='Paused';
                            }
                        @endif
                        return out;
                }},
                {"targets": 28, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out="";
                        
                        @if(Auth::user()->role==config('constants.REAL_HR'))
                            if(row.first_approval_status=='Pending'){
                                out +='<a href="#" onclick="approve_record('+row.id+')" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>&nbsp;';
                                out +='<a href="#" data-toggle="modal" data-target="#reject_model" onclick="reject_record('+row.id+')" class="btn btn-danger btn-rounded"><i class="fa fa-remove"></i></a>&nbsp;';
                            }
                            
                            if (row.first_approval_status!='Approved' && row.is_locked == 'NO'){
                            out += '<a href="<?php echo url("edit_payroll") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>&nbsp;';
                            }                            
                        @elseif(Auth::user()->role==config('constants.ASSISTANT'))
                            if(row.first_approval_status=='Approved' && row.second_approval_status=='Pending'){
                                out +='<a href="#" onclick="approve_record('+row.id+')" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>&nbsp;';
                                out +='<a href="#" data-toggle="modal" data-target="#reject_model" onclick="reject_record('+row.id+')" class="btn btn-danger btn-rounded"><i class="fa fa-remove"></i></a>&nbsp;';
                            }
                            if (row.first_approval_status=='Approved' && row.second_approval_status!='Approved' && row.is_locked == 'NO'){
                            out += '<a href="<?php echo url("edit_payroll") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>&nbsp;';
                            }
                        @elseif(Auth::user()->role==config('constants.Admin'))
                            if(row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Pending'){
                                out +='<a href="#" onclick="approve_record('+row.id+')" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>&nbsp;';
                                out +='<a href="#" data-toggle="modal" data-target="#reject_model" onclick="reject_record('+row.id+')" class="btn btn-danger btn-rounded"><i class="fa fa-remove"></i></a>&nbsp;';
                            }
                            if (row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status!='Approved' && row.is_locked == 'NO'){
                            out += '<a href="<?php echo url("edit_payroll") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>&nbsp;';
                            }
                        @elseif(Auth::user()->role==config('constants.SuperUser'))
                            if(row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Approved' && row.fourth_approval_status=='Pending'){
                                out +='<a href="#" onclick="approve_record('+row.id+')" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>&nbsp;';
                                out +='<a href="#" data-toggle="modal" data-target="#reject_model" onclick="reject_record('+row.id+')" class="btn btn-danger btn-rounded"><i class="fa fa-remove"></i></a>&nbsp;';
                            }
                            if (row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Approved' && row.fourth_approval_status!='Approved' && row.is_locked == 'NO'){
                            out += '<a href="<?php echo url("edit_payroll") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>&nbsp;';
                            }
                        @else
                           if(row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Approved' && row.fourth_approval_status=='Approved' && row.fifth_approval_status=='Pending'){
                                out +='<a href="#" onclick="approve_record('+row.id+')" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>&nbsp;';
                                out +='<a href="#" data-toggle="modal" data-target="#reject_model" onclick="reject_record('+row.id+')" class="btn btn-danger btn-rounded"><i class="fa fa-remove"></i></a>&nbsp;';
                            }
                            if (row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Approved' && row.fourth_approval_status=='Approved' && row.fifth_approval_status!='Approved' && row.is_locked == 'NO'){
                            out += '<a href="<?php echo url("edit_payroll") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>&nbsp;';
                            }
                        @endif                        
                        return out;
                    }
                },
            ]
        });

    $('#select-all').click(function(event) {
        if(this.checked) {
            // Iterate each checkbox
            $(':checkbox').each(function() {
                this.checked = true;
            });
        } else {
            $(':checkbox').each(function() {
                this.checked = false;
            });
        }
    });
    

    });
    function set_lock(e) {
        swal({
            title: "Are you sure you want to lock ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            window.location.href = $(e).attr('data-href');
        });
    }    
    
    function pause_loan(id){
        swal({
            title: "Are you sure you want to pause loan in this month ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: true
        }, function () {
            window.location.href='{{ url("pause_loan/") }}'+'/'+id;
        });
    }
    
    function resume_loan(id){
        swal({
            title: "Are you sure you want to resume loan in this month ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: true
        }, function () {
            window.location.href='{{ url("resume_loan/") }}'+'/'+id;
        });
    }
    
    function approve_record(id){
        swal({
            title: "Are you sure you want to approve?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            window.location.href = '<?php echo url('payroll_approve/') ?>'+'/'+id;
        });
    }
    
    function reject_record(id){
        $('#payroll_id').val(id);
    }
    
</script>
@endsection