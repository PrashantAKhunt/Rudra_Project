@extends('layouts.admin_app')

@section('content')
<style>
div.dt-buttons{
position:relative;
float:center;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="#"> {{ $page_title }}</a></li>
            </ol>
        </div>
    </div>
    <!-- <form action="{{ route('admin.payroll_approve_all') }}" id="payroll_approve_all" method="post"> -->
    <!-- @csrf -->
    <form action="{{ route('admin.payroll_approve_all') }}" id="payroll_approve_all" method="post">
                @csrf
        <input type="hidden" name="itemselectionsAll" id="itemselectionsAll"/>
    </form>

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
            <button id="approve_all" class="btn btn-primary pull-left"> <i class="fa fa-check"></i> Approve All</button>
            
            {{-- Payroll Generate By HR --}}
            @if(Auth::user()->role == config('constants.REAL_HR'))
                &nbsp;<button id="payroll_generate_hr" class="btn btn-success"> <i class="fa fa-refresh" aria-hidden="true"></i> Payroll Generate</button>
            @endif
            @if(Auth::user()->role == config('constants.ACCOUNT_ROLE'))
            <button style="margin-left: 15px;" id="add_payments_details" onclick="paymentDetails()" class="btn btn-primary pull-left"> <i class="fa fa-check"></i> Payment Details</button>    
			@endif
            <a href="{{ route('admin.finance_report') }}" class="btn btn-primary pull-right"><i class="fa fa-file"></i> Payroll Report</a>
                <p class="text-muted m-b-30"></p>
                <br>                
                <div class="table-responsive">
                    <table id="payroll_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox"  name="itemselectionsAll[]" id="select-all" onClick="toggle(this)" /></th>
                                <th> 
                                @if(Auth::user()->role == config('constants.ACCOUNT_ROLE'))
                                    <input type="checkbox"  name="itempaymentsAll[]" id="select-payment-all" onClick="toggle(this)" />
                                @endif
                                Payment Detail
                                
                                </th>
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
                                <th>Gross CTC</th>                                
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
    <!-- </form> -->
</div>
<div id="done_payments_model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">

                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3>Payment Details</h3>
                        </div>
                        <div class="modal-body">
                            <div class="form-group ">
                                <label><b>Cheque No:</b></label>
                                <p id="ch_no"></p>
                            </div>
                            <div class="form-group ">
                                <label><b>Payment Details:</b></label>
                                <p id="payment_note"></p>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
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
<div id="attendance_model" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="attendance_data">
        </div>
    </div>
</div>
 <!-- Approve Model -->
 <div id="PaymentDetailsmodel" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content" id="model_data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="panel-title">Add Payment Details</h3>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('admin.submit_payments_details') }}" id="add_payments_form">
                            @csrf
                            <input type="hidden" name="payroll_ids" id="payroll_ids" >
                            <div class="row">
                            <div class="form-group ">
                                <label>Company Name</label>

                                    <select class="form-control required" id="company_id" name="company_id">
                                        <option value="">Select Company</option>

                                    </select>

                            </div>
                            <div class="form-group ">
                                <label>Bank Name</label>

                                    <select class="form-control required" id="bank_id" name="bank_id">
                                        <option value="">Select bank</option>

                                    </select>

                            </div>
                            <div class="form-group ">
                                    <label>Cheque Ref Number</label>
                                    <select class="form-control required" name="cheque_ref_no" id="cheque_ref_no">
                                        <option value="">Select Cheque Ref Number</option>
                                    </select>
                                </div>
                            <div class="form-group ">
                                        <label>Cheque Number</label>
                                        <select class="form-control required" name="cheque_number" id="cheque_number">
                                           <option value="">Select Cheque</option>

                                       </select>

                                    </div>
                                
                                <div class="form-group ">
                                    <label>Payment Details</label>
                                    <textarea class="form-control" rows="3" name="payment_details" id="payment_details"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-2">
                                    <button type="submit" class="btn btn-success btn-block">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>


                </div>
            </div>
        </div>
        <!-- End Approve Model -->
@endsection

@section('script')		
<script>
    function set_reject_reason(data){
        $('#reject_reason_data').html(data);
    }
    function done_payments(id) {
        $('#ch_no').text('NA');
        $('#payment_note').text('NA');
        $.ajax({
                url: "{{ route('admin.get_payroll_details')}}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id :id,},
                success: function(data, textStatus, jQxhr) {
                    $('#ch_no').text(data.data.payroll_details.ch_no);
                    $('#payment_note').text(data.data.payroll_details.payment_details);
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
        });
    }
    $("#approve_all").click(function(){
                    var approve_ids = [];
                    $.each($("input[name='itemselections']:checked"), function(){
                        approve_ids.push($(this).val());
                    });
                    var id = approve_ids.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select Payroll entry !",
                            //text: "You want to change status of admin user.",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Okay",
                            closeOnConfirm: true
                        });
                    }
                    else {
                        $("#itemselectionsAll").val(id);
                        $("#payroll_approve_all").submit();
                    }
    });
    $("#add_payments_details").click(function(){
                    var favorite = [];
                    $.each($("input[name='add_payments']:checked"), function(){
                        favorite.push($(this).val());
                    });
                    var id = favorite.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select Payroll entry !",
                            //text: "You want to change status of admin user.",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Okay",
                            closeOnConfirm: true
                        });
                    }
                    else {
                        $("#payroll_ids").val(id);
                        $("#PaymentDetailsmodel").modal("show");
                    }
    });
    $(document).ready(function () {  
                $('#company_id').select2();
                $('#bank_id').select2();
                $('#cheque_ref_no').select2(); 
                $('#cheque_number').select2();   
                $('#add_payments_form').validate({
                    ignore:[],
                    rules: {
                        company_id: "required",
                        bank_id: "required",
                        cheque_ref_no: "required",
                        cheque_number: "required",
                        payment_details: "required",
                    }
                });  
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
                            action_html = '<input type="checkbox" name="itemselections" id="itemselections-' + id + '" value="' + id + '">';
                        }
                    @elseif(Auth::user()->role==config('constants.ASSISTANT'))
                        if(row.first_approval_status=='Approved' && row.second_approval_status=='Pending'){
                            action_html = '<input type="checkbox" name="itemselections" id="itemselections-' + id + '" value="' + id + '">';
                        }
                    @elseif(Auth::user()->role==config('constants.Admin'))
                        if(row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Pending'){
                            action_html = '<input type="checkbox" name="itemselections" id="itemselections-' + id + '" value="' + id + '">';
                        }
                    @elseif(Auth::user()->role==config('constants.SuperUser'))
                        if(row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Approved' && row.fourth_approval_status=='Pending'){
                            action_html = '<input type="checkbox" name="itemselections" id="itemselections-' + id + '" value="' + id + '">';
                        }
                    @else
                        if(row.first_approval_status=='Approved' && row.second_approval_status=='Approved' && row.third_approval_status=='Approved' && row.fourth_approval_status=='Approved' && row.fifth_approval_status=='Pending'){
                            action_html = '<input type="checkbox" name="itemselections" id="itemselections-' + id + '" value="' + id + '">';
                        }
                        @endif
                    return action_html;
                    }
                },
                {"taregts": 1, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                if(row.cheque_no == 0){
                                    @if(Auth::user()->role == config('constants.ACCOUNT_ROLE'))
                                    out = '&nbsp;<input type="checkbox" value='+id+' name="add_payments" >';
                                    @else
                                    out = 'NA';
                                    @endif
                                } else {
                                    out = '<a href="#" data-toggle="modal" data-target="#done_payments_model" title="view payment details" onclick="done_payments('+id+')" class="btn btn-success btn-rounded"><i class="fa fa-info"></i></a>&nbsp;';
                                }
                                return out;
                            }
                },
                {"targets": 2, "searchable": true, "data": "name"},
                {"targets": 3, "searchable": true, "data": "month"},
                {"targets": 4, "searchable": true, "data": "year"},
                {"targets": 5, "searchable": true, "data": "basic_salary"},
                {"targets": 6, "searchable": true, "data": "hra"},
                {"targets": 7, "searchable": true, "data": "others"},
                {"targets": 8, "searchable": true, "data": "food"},
                {"targets": 9, "searchable": true, "data": "working_day"},
                {"targets": 10, "searchable": true, "data": "employee_working_day"},
                {"targets": 11, "searchable": true, "data": "total_leave"},
                {"targets": 12, "searchable": true, "data": "total_sandwich_leave"},
                {"targets": 13, "searchable": true, "data": "unpaid_leave"},
                {"targets": 14, "searchable": true, "data": "unpaid_leave_amount"},
                {"targets": 15, "searchable": true, "data": "professional_tax"},
                {"targets": 16, "searchable": true, "data": "pf"},
                {"targets": 17, "searchable": true, "data": "loan_installment"},
                {"targets": 18, "searchable": true, "data": "extra_loan_amount"},
                {"targets": 19, "searchable": true, "data": "extra_loan_details"},
                {"targets": 20, "searchable": true, "data": "penalty"},
                {"targets": 21, "searchable": true, "data": "manual_penalty"},
                {"targets": 22, "searchable": true, "data": "penalty_note"},
                {"targets": 23, "searchable": true, "data": "payable_salary"},
                {"targets": 24, "searchable": false, "orderable": false,"render": function (data, type, row) {
                        
                        
                        if(row.salary_ctc){
                            var amt=((row.salary_ctc/row.total_month_days)*row.total_paid_days)-row.employer_pf;
                            return amt.toFixed(2);
                        }
                        else{
                            var amt=row.gross_salary;
                        return amt.toFixed(2);
                        }
                }},
                {"targets": 25,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = '';
                        @if(Auth::user()->role==config('constants.REAL_HR'))
                            if(row.first_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.first_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else if (row.first_approval_status == 'Approved') {
                                    if(moment(row.first_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>'  + moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                }else{
                                out='<b class="text-success">Approved<br></b>';
                                // we are here .format("DD-MM-YYYY h:mm A",'')

                            }
                        @elseif(Auth::user()->role==config('constants.ASSISTANT'))
                            if(row.second_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.second_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else if (row.second_approval_status == 'Approved') {
                                    if(moment(row.second_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>'  + moment(row.second_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                }else{
                                out='<b class="text-success">Approved<br></b>';
                                // we are here .format("DD-MM-YYYY h:mm A",'')

                            }
                        @elseif(Auth::user()->role==config('constants.Admin'))
                            if(row.third_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.third_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else if (row.third_approval_status == 'Approved') {
                                    if(moment(row.third_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>'  + moment(row.third_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                }else{
                                out='<b class="text-success">Approved<br></b>';
                                // we are here .format("DD-MM-YYYY h:mm A",'')

                            }
                        @elseif(Auth::user()->role==config('constants.SuperUser'))
                            if(row.fourth_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.fourth_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else if (row.fourth_approval_status == 'Approved') {
                                    if(moment(row.fourth_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>'  + moment(row.fourth_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                }else{
                                out='<b class="text-success">Approved<br></b>';
                                // we are here .format("DD-MM-YYYY h:mm A",'')

                            }
                        @else
                            if(row.fifth_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.fifth_approval_status=='Rejected'){
                                out='<b class="text-danger">Rejected</b>';
                            }else if (row.fifth_approval_status == 'Approved') {
                                    if(moment(row.fifth_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>'  + moment(row.fifth_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                }else{
                                out='<b class="text-success">Approved<br></b>';
                                // we are here .format("DD-MM-YYYY h:mm A",'')

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
                    {"targets": 26,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = '';                        
                            if(row.main_approval_status=='Pending'){
                                out='<b class="text-warning">Pending</b>';
                            }else if(row.main_approval_status=='Rejected'){
                                out='<a href="#" onclick="set_reject_reason(&apos;'+row.reject_note+'&apos;)" class="btn btn-danger" data-toggle="modal" data-target="#reject_reason_model">Rejected</b>';
                            }else if (row.main_approval_status == 'Approved') {
                                    if(moment(row.fifth_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>'  + moment(row.fifth_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                }else{
                                out='<b class="text-success">Approved<br></b>';
                                // we are here .format("DD-MM-YYYY h:mm A",'')

                            }                       
                        return out;
                    }
                },  
                {"targets": 27, "data": "date"},
                {"targets":28,"searchable":false,"orderable":false,"render":function(data,type,row){
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
                {"targets": 29, "searchable": false, "orderable": false,
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
                        var user_id = row.user_id, month = row.month, year = row.year;
                        out +='<a href="#attendance_model" onclick="view_attendance(&quot;<?php echo url('get_user_attendance') ?>' + '/' + user_id + '/' + month + '/' + year + '&quot;)" data-toggle="modal" class="btn btn-danger btn-rounded" title="View Attendance"><i class="fa fa-eye"></i></a>&nbsp;';
                        return out;
                    }
                },
            ]
        });

        $('#select-all').click(function(event) {
            if(this.checked) {
                // Iterate each checkbox
                $("input[name='itemselections']").each(function() {
                    this.checked = true;
                });
            } else {
                $("input[name='itemselections']").each(function() {
                    this.checked = false;
                });
            }
        });
        $('#select-payment-all').click(function(event) {
            if(this.checked) {
                // Iterate each checkbox
                $("input[name='add_payments']").each(function() {
                    this.checked = true;
                });
            } else {
                $("input[name='add_payments']").each(function() {
                    this.checked = false;
                });
            }
        }); 
    });

    function paymentDetails(id=0) {
        $.ajax({
                url: "{{ route('admin.get_payroll_details') }}",
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    id: id
                },
                dataType: "JSON",
                success: function(data) {
         
                    $('#company_id').select2('destroy').empty().select2().append(data.data.company_list);
                    $('#bank_id').select2('destroy').empty().select2();
                    $('#cheque_ref_no').select2('destroy').empty().select2();
                    $('#cheque_number').select2('destroy').empty().select2();
                    $('#payment_details').val('');
                
                }
            });
        
    }
    $("#company_id").on('change',function(){
            var company_id = $(this).val();
          
            $.ajax({
                url: "{{ route('admin.get_company_bank_list_ajax')}}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {company_id :company_id,},
                success: function(data, textStatus, jQxhr) {
                    $('#bank_id').select2('destroy').empty().select2().append(data);
                    $('#cheque_ref_no').select2('destroy').empty().select2();
                    $('#cheque_number').select2('destroy').empty().select2();
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
    });
    $("#bank_id").on('change',function(){
            var bank_id = $(this).val();
            var company_id = $('#company_id').val();
          
            $.ajax({
                url: "{{ route('admin.get_all_cheque_ref_list')}}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {company_id:company_id, bank_id :bank_id},
                success: function(data, textStatus, jQxhr) {
                    $('#cheque_ref_no').select2('destroy').empty().select2().append(data);
                    $('#cheque_number').select2('destroy').empty().select2();
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
    });
    $("#cheque_ref_no").on('change',function(){
            var cheque_ref_no = $(this).val();
          
            $.ajax({
                url: "{{ route('admin.get_signedUnfailed_cheque_list')}}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {cheque_book :cheque_ref_no,},
                success: function(data, textStatus, jQxhr) {
                    $('#cheque_number').select2('destroy').empty().select2().append(data);
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
    });
    function view_attendance(route)
    {
        $('#attendance_data').html('');
        $.ajax({
            url: route,
            type: "GET",
            dataType: "html",
            catch : false,
            success: function (data) {
                $('#attendance_data').append(data);
            }
        });
    }
    
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
    
    $("#payroll_generate_hr").on('click',function(){
        $(this).html('<i class="fa fa-refresh fa-spin"></i> Generating......');
        $("#payroll_generate_hr").prop('disabled',true);
        $.ajax({
            type : "GET",
            url : "{{route('admin.payroll_generate_hr')}}",
            success : function(data){
                // console.log(data);
                $("#payroll_generate_hr").prop('disabled',false);
                $("#payroll_generate_hr").html('<i class="fa fa-refresh" aria-hidden="true"></i> Payroll Generate');
                if(data.status == false){
                    swal("Info!", data.message, "error");
                }else{
                    $('#payroll_table').DataTable().ajax.reload();
                    swal("Info!", data.message, "success");
                }
            }
        })
    })
</script>
@endsection