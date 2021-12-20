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
        <div class="col-md-12">
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
            @if(Auth::user()->role == config('constants.ACCOUNT_ROLE'))
            <button id="add_payments_details" onclick="paymentDetails()" class="btn btn-primary pull-left"> <i class="fa fa-check"></i> Payment Details</button>    
			@endif
                <?php
                $role = [];
                if(!empty($access_rule)){
                    $role = explode(',', $access_rule);
                }
                if(in_array(3, $role) && Auth::user()->role!=config('constants.SuperUser')) {
                ?>
                <a href="{{ route('admin.add_employee_loan') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Apply Loan</a>
                <a href="#" class="btn btn-primary pull-left"  title="Advance Salary" data-target="#myModal" data-toggle="modal"><i class="fa fa-plus"></i> Advance Salary</a>
                <?php
                  }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th> 
                                @if(Auth::user()->role == config('constants.ACCOUNT_ROLE'))
                                    <input type="checkbox"  name="itempaymentsAll[]" id="select-payment-all" onClick="toggle(this)" />
                                @endif
                                Payment Detail
                                
                        </th>
                        <th>Employee Name</th>
                        <th>Loan Type</th>
                        <th>Loan Amount</th>
                        <th>Loan Expected Month</th>
                        <th>Loan Emi Start From</th>
                        <th>Loan Terms</th>
                        <th>Reason For Loan</th>
                        <th>HR Approval Status</th>
                        <th>Super Admin Approval Status</th>
                        <th>Account Approval Status</th>
                        <th>Loan Status</th>
                        <th>Action</th>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- sample modal content -->
        <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Add Loan Reject Reason</h4>
                    </div>
                    <div class="modal-body">
                    <form action="{{ route('admin.reject_emp_loan') }}" id="reject_emp_loan" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id"/>
                        <div class="form-group ">
                            <label>Reject Note</label>
                            <textarea class="form-control" rows="5" name="note" id="note"></textarea>
                        </div>

                        <button type="Submit" class="btn btn-success">Submit</button>
                    </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div id="rejectNoteModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Reject Note</h4>
                    </div>
                    <div class="modal-body" id="reject_note_div">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <!-- Payment details -->
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
        <!--  -->
        <!-- Approve Model -->
    <div id="PaymentDetailsmodel" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content" id="model_data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="panel-title">Add Payment Details</h3>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('admin.submit_loan_payments_details') }}" id="add_payments_form">
                            @csrf
                            <input type="hidden" name="loan_ids" id="loan_ids" >
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

        <!-- start of modal for Advance Salary -->
        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Advance Salary</h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.advance_salary') }}" id="advancesalary_ajax" method="get" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id"/>                            
                             <div class="form-group"> 
                                <label>Description <span class="error">*</span> </label> 
                                <textarea class="form-control required" name="loan_description" id="loan_description" cols="10" rows="5"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </form>
                    </div>
                    <!-- <div class="modal-footer">
                    </div> -->
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div> 
        <!-- End of modal for Advance Salary -->

</div>
@endsection


@section('script')
<script>
    function done_payments(id) {
        $('#ch_no').text('NA');
        $('#payment_note').text('NA');
        $.ajax({
                url: "{{ route('admin.get_loan_payment_details')}}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id :id,},
                success: function(data, textStatus, jQxhr) {
                    $('#ch_no').text(data.data.loan_details.ch_no);
                    $('#payment_note').text(data.data.loan_details.payment_details);
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
        });
    }
    $("#add_payments_details").click(function(){
                    var favorite = [];
                    $.each($("input[name='add_payments']:checked"), function(){
                        favorite.push($(this).val());
                    });
                    var id = favorite.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select Loan entry !",
                            //text: "You want to change status of admin user.",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Okay",
                            closeOnConfirm: true
                        });
                    }
                    else {
                        $("#loan_ids").val(id);
                        $("#PaymentDetailsmodel").modal("show");
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
    var access_rule = '<?php echo $access_rule; ?>';
    var loanType = {1: 'Advance Salary', 2: 'Normal Loan'};
    access_rule = access_rule.split(',');
    var table = $('#emp_table').DataTable({
        dom: 'lBfrtip',
        buttons: ['excel'],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "stateSave": true,
        "order": [[1, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.employee_loan_list'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 0, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                if (row.loan_status=="Approved") {
                                    
                                    if(row.cheque_no == 0 ){
                                        @if(Auth::user()->role == config('constants.ACCOUNT_ROLE'))
                                            out = '&nbsp;<input type="checkbox" value='+id+' name="add_payments" >';
                                        @else
                                            out = 'NA';
                                        @endif
                                    } else {
                                        out = '<a href="#" data-toggle="modal" data-target="#done_payments_model" title="view payment details" onclick="done_payments('+id+')" class="btn btn-success btn-rounded"><i class="fa fa-info"></i></a>&nbsp;';
                                    }
                                } else {
                                    out = 'NA';
                                }
                                return out;
                            }
            },
            {"taregts": 1, 'data': 'name' },
            {"taregts": 2, "searchable": false, "render": function (data, type, row) {
                    return loanType[row.loan_type];
                }
            },
            {"taregts": 3, 'data': 'loan_amount' },
            {"taregts": 4,
                    "render": function (data, type, row) {
                        return changeDateformat(row.loan_expected_month);
                    }
            },
            {"taregts": 5,
                    "render": function (data, type, row) {
                        return changeDateformat(row.loan_emi_start_from);
                    }
            },
            {"taregts": 6,
                    "render": function (data, type, row) {
                        return row.loan_terms+' month'
                    }
            },
            {"taregts": 7, 'data': 'loan_descption'
            },
            {"taregts": 8, "render":function(data,type,row){
                    var out="";
                    if(row.first_approval_status=="Pending"){
                        out +='<b class="text-warning">Pending</b>';

                    }
                    else if(row.first_approval_status=="Approved"){
                        out += '<b class="text-success">Approved</b><br>';
                        if(row.first_approval_datetime){
                            out +=moment(row.first_approval_datetime).format('DD-MM-YYYY hh:mm a');
                        }
                    }
                    else{
                        out += '<b class="text-danger">Rejected</b><br>';
                        if(row.first_approval_datetime){
                            out +=moment(row.first_approval_datetime).format('DD-MM-YYYY hh:mm a');
                        }
                    }
                    return out;
                }
            },
            {"taregts": 9, "render":function(data,type,row){
                    var out="";
                    if(row.second_approval_status=="Pending"){
                        out += '<b class="text-warning">Pending</b>';
                    }
                    else if(row.second_approval_status=="Approved"){
                        out += '<b class="text-success">Approved</b><br>';
                        if(row.second_approval_datetime){
                            out +=moment(row.second_approval_datetime).format('DD-MM-YYYY hh:mm a');
                        }
                    }
                    else{
                        out += '<b class="text-danger">Rejected</b><br>';
                        if(row.second_approval_datetime){
                            out +=moment(row.second_approval_datetime).format('DD-MM-YYYY hh:mm a');
                        }
                    }
                    return out;
                }
            },
            {"taregts": 10, "render":function(data,type,row){
                    var out="";
                    if(row.third_approval_status=="Pending"){
                        out +=  '<b class="text-warning">Pending</b>';
                    }
                    else if(row.third_approval_status=="Approved"){
                        out +=  '<b class="text-success">Approved</b><br>';
                        if(row.third_approval_datetime){
                            out +=moment(row.third_approval_datetime).format('DD-MM-YYYY hh:mm a');
                        }
                    }
                    else{
                        out +=  '<b class="text-danger">Rejected</b><br>';
                        if(row.third_approval_datetime){
                            out +=moment(row.third_approval_datetime).format('DD-MM-YYYY hh:mm a');
                        }
                    }
                    return out;
                }
            },
            {"taregts": 11, "render":function(data,type,row){
                    if(row.loan_status=="Pending"){
                        return '<b class="text-warning">Pending</b>';
                    }
                    else if(row.loan_status=="Approved"){
                        return '<b class="text-success">Approved</b>'
                    }
                    else{
                        return '<b class="text-danger">Rejected</b>'
                    }
                }
            },

            {"taregts": 12, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    var login_user_id      = <?php echo Auth::user()->id;?>;
                    var login_user_role      = '<?php echo Auth::user()->role;?>';
                    var role_name = <?php echo config('constants.SuperUser');?>;

                    if(login_user_id==row.user_id && (row.first_approval_status!="Approved" || row.loan_status=="Rejected")){
                        if(row.loan_type == 2){
                            out = '<a href="<?php echo url('edit_employee_loan') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                        }
                        
                        out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_employee_loan'); ?>/' + id + '\'\n\
                                title="Delete"><i class="fa fa-trash"></i></a>';
                    }

                    if(row.first_approval_status=="Pending" && login_user_role=='<?php echo config('constants.REAL_HR');?>'){
                        out += '&nbsp;<a onclick="approve_confirm(this);" data-href="<?php echo url('approve_emp_loan') ?>'+'/'+id+'/'+'" class="btn btn-success btn-rounded" title="Approve Loan"><i class="fa fa-check"></i></a>';
                         out += '&nbsp;<a onclick="reject_emp_loan(this)" id='+id+'" class="btn btn-danger btn-rounded" title="Reject Loan"><i class="fa fa-remove"></i></a>';
                    }
                    else if (row.first_approval_status=="Approved" && row.second_approval_status=="Pending" && login_user_role=='<?php echo config('constants.SuperUser');?>') {
                        out += '&nbsp;<a onclick="approve_confirm(this);" data-href="<?php echo url('approve_emp_loan') ?>'+'/'+id+'/'+'" class="btn btn-success btn-rounded" title="Approve Loan"><i class="fa fa-check"></i></a>';
                         out += '&nbsp;<a onclick="reject_emp_loan(this)" id='+id+'" class="btn btn-danger btn-rounded" title="Reject Loan"><i class="fa fa-remove"></i></a>';
                    }
                    else if(row.first_approval_status=="Approved" && row.second_approval_status=="Approved" && row.third_approval_status=="Pending" && login_user_role=='<?php echo config('constants.ACCOUNT_ROLE');?>'){
                        out += '&nbsp;<a onclick="approve_confirm(this);" data-href="<?php echo url('approve_emp_loan') ?>'+'/'+id+'/'+'" class="btn btn-success btn-rounded" title="Approve Loan"><i class="fa fa-check"></i></a>';
                         out += '&nbsp;<a onclick="reject_emp_loan(this)" id='+id+'" class="btn btn-danger btn-rounded" title="Reject Loan"><i class="fa fa-remove"></i></a>';
                    }

                    if(row.loan_status=="Rejected"){
                        out += '&nbsp;<textarea style="display:none;" id="reject_detail_'+id+'">'+row.reject_note+'</textarea><a onclick="reject_note('+id+')" data-toggle="modal" data-target="#rejectNoteModal" class="btn btn-danger btn-rounded" title="Reject Note"><i class="fa fa-eye"></i></a>';
                    }

                    return out;
                }
            },
        ]

    });
    function paymentDetails(id=0) {
        $.ajax({
                url: "{{ route('admin.get_loan_payment_details') }}",
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
    jQuery("#upload_bank_transactions").validate({
        ignore: [],
        rules: {

            file: {
                required: true,
            },

        },

    });
function changeDateformat(date)
{
    //return date.split("-");
    var fields = date.split('-');
    var name = fields[0];
    var street = fields[1];
    var Month = ['Jan','Feb','March','April','May','Jun','July','Aug','Sep','Oct','Nov','Dec'];
    return Month[name-1]+'-'+street;
}
function delete_confirm(e) {
    swal({
        title: "Are you sure you want to delete loan ?",
        //text: "You want to change status of admin user.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: true
    }, function () {
        window.location.href = $(e).attr('data-href');
    });
}
function reject_note(id){
    var detail=$('#reject_detail_'+id).text();
    if(detail && detail!='null'){
    $('#reject_note_div').text(detail);
    }
    else{
        $('#reject_note_div').text("No record available.");
    }
}
function approve_confirm(e)
{
    swal({
        title: "Are you sure you want to approve loan ?",
        //text: "You want to change status of admin user.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: true
    }, function () {
        window.location.href = $(e).attr('data-href');
    });
}

function reject_emp_loan(e)
{
    $("#id").val($(e).attr('id'));
    $('#rejectModal').modal('show');
    // swal({
    //     title: "Are you sure you want to reject loan ?",
    //     //text: "You want to change status of admin user.",
    //     type: "warning",
    //     showCancelButton: true,
    //     confirmButtonColor: "#DD6B55",
    //     confirmButtonText: "Yes",
    //     closeOnConfirm: true
    // }, function () {
    //     window.location.href = $(e).attr('data-href');
    // });
}

$('#advancesalary_ajax').validate({
        rules:{
            description:{
                required:true
            },
        }
    });

</script>
@endsection
