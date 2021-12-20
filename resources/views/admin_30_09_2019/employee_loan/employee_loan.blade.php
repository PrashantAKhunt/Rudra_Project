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
            <div class="white-box">
                <?php
                $role = [];
                if(!empty($access_rule)){
                    $role = explode(',', $access_rule);
                }
                if(in_array(3, $role)) {
                ?>
                <a href="{{ route('admin.add_employee_loan') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Apply Loan</a>
                <?php 
                  }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th>Employee Name</th>
                        <th>Loan Amount</th>
                        <th>Loan Expected Month</th>
                        <th>Loan Emi Start From</th>
                        <th>Loan Terms</th>
                        <th>Reason For Loan</th>
                        <th>Loan Status</th>
                        <th>Status</th>
                        <th>Action</th>
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
    access_rule = access_rule.split(',');
    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[1, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.employee_loan_list'); ?>",
            type: "GET",
        },
        "columns": [

            {"taregts": 0, 'data': 'name'
            },
            {"taregts": 1, 'data': 'loan_amount'
            },
            // {"taregts": 2, 'data': 'loan_expected_month'
            // },
            {"taregts": 2,
                    "render": function (data, type, row) {
                        return changeDateformat(row.loan_expected_month);
                    }
            },
            {"taregts": 3,
                    "render": function (data, type, row) {
                        return changeDateformat(row.loan_emi_start_from);
                    }
            },
            // {"taregts": 3, 'data': 'loan_emi_start_from'
            // },
            // {"taregts": 4, 'data': 'loan_terms'
            // },
            {"taregts": 4,
                    "render": function (data, type, row) {
                        return row.loan_terms+' month'
                    }
            },
            {"taregts": 5, 'data': 'loan_descption'
            },
            // {"taregts": 6, 'data': 'loan_status'
            // },
            {"taregts": 6, "render":function(data,type,row){
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
            {"taregts": 7,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = '';
                        if($.inArray('2',access_rule) !== -1) {
                            if(row.status=='Enabled'){
                            out += '<a href="<?php echo url('change_loan_status') ?>'+'/'+id+'/Disabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                            }
                            else{
                            out += '<a href="<?php echo url('change_loan_status') ?>'+'/'+id+'/Enabled'+'" class="btn btn-danger" title="Change Status">'+row.status+'</a>';    
                            }
                        }
                        return out;
                    }
            },
            {"taregts": 8, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    if($.inArray('2',access_rule) !== -1){
                        out = '<a href="<?php echo url('edit_employee_loan') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                    }
                    if($.inArray('4',access_rule) !== -1){
                        out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_employee_loan'); ?>/' + id + '\'\n\
                        title="Delete"><i class="fa fa-trash"></i></a>';
                    }
                    return out;
                }
            },
        ]

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
        closeOnConfirm: false
    }, function () {
        window.location.href = $(e).attr('data-href');
    });
}
</script>
@endsection
