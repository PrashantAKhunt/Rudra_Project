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
            <a href="{{ route('admin.users') }}" class="btn btn-danger pull-right"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Back</a>

                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th>
                                Payment Detail
                                
                        </th>
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
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection


@section('script')
<script>
    
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
            url: "<?php echo route('admin.selected_employee_loan_list'); ?>",
            type: "GET",
            data : {
                user_id : `{{$user_id}}`
            }
        },
        "columns": [
            {"taregts": 0, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "NA";
                                return out;
                            }
            },
            {"taregts": 1, "searchable": false, "render": function (data, type, row) {
                    return loanType[row.loan_type];
                }
            },
            {"taregts": 2, 'data': 'loan_amount' },
            {"taregts": 3,
                    "render": function (data, type, row) {
                        return changeDateformat(row.loan_expected_month);
                    }
            },
            {"taregts": 4,
                    "render": function (data, type, row) {
                        return changeDateformat(row.loan_emi_start_from);
                    }
            },
            {"taregts": 5,
                    "render": function (data, type, row) {
                        return row.loan_terms+' month'
                    }
            },
            {"taregts": 6, 'data': 'loan_descption'
            },
            {"taregts": 7, "render":function(data,type,row){
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
            {"taregts": 8, "render":function(data,type,row){
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
            {"taregts": 9, "render":function(data,type,row){
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
            {"taregts": 10, "render":function(data,type,row){
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
        ]

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
</script>
@endsection
