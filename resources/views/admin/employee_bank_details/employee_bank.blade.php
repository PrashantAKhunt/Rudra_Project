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
                if(in_array(1, $role)) {
                    if((Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.SuperUser')) || empty($employee_detail)) {
                ?>
                <a href="{{ route('admin.add_employee_bank') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</a>
                <?php }
                    }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th>Employee Name</th>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>IFSC Code</th>
                        <th>Name On Account</th>
                        <th>Pancard Number</th>
                        <th>PF Number</th>
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
        dom: 'lBfrtip',
        buttons: ['excel'],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[1, "DESC"]],
        stateSave: true,
        "ajax": {
            url: "<?php echo route('admin.employee_bank_list'); ?>",
            type: "GET",
        },
        "columns": [

            {"taregts": 0, 'data': 'name'
            },
            {"taregts": 1, 'data': 'bank_name'
            },
            {"taregts": 2, 'data': 'account_number'
            },
            {"taregts": 3, 'data': 'ifsc_code'
            },
            {"taregts": 4, 'data': 'name_on_account'
            },
            {"taregts": 5, 'data': 'pancard_number'
            },
            {"taregts": 5, 'data': 'pf_number'
            },
            {"taregts": 7, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    if($.inArray('2',access_rule) !== -1){
                        out = '<a href="<?php echo url('edit_employee_bank') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                    }
                    if($.inArray('4',access_rule) !== -1){
                        out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_employee_bank'); ?>/' + id + '\'\n\
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
    function delete_confirm(e) {
    swal({
        title: "Are you sure you want to delete bank details ?",
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
