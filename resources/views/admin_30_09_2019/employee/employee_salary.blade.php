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
        <div class="col-md-12" style="display: none;">
            <div class="white-box">
                <button onclick="window.location.href ='{{ route('admin.employee_salary_format') }}'" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Download Sample CSV Format</button>
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
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
                        <form action="{{ route('admin.employee_salary_upload') }}" id="upload_bank_transactions" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group upload-file">
                                <label>Upload Employee Salary CSV File</label>
                                <input type="file" class="form-control" name="emp_salary_file" id="emp_salary_file" />
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="white-box">
                <a href="{{ route('admin.add_employee_salary') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Employee Structure</a>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th>Employee Name</th>
                        <th>Employee Basic Salary</th>
                        <th>HRA</th>
                        <th>Other Allowance</th>
                        <th>Total Month Salary</th>
                        <th>Salary Month</th>
                        <th>Salary Year</th>
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

    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[1, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.employee_salary_list'); ?>",
            type: "GET",
        },
        "columns": [

            {"taregts": 0, 'data': 'name'
            },
            {"taregts": 1, 'data': 'basic_salary'
            },
            {"taregts": 2, 'data': 'hra'
            },
            {"taregts": 3, 'data': 'other_allowance'
            },
            {"taregts": 4, 'data': 'total_month_salary'
            },
            {"taregts": 5, 'data': 'salary_month'
            },
            {"taregts": 6, 'data': 'salary_year'
            },
            {"taregts": 7, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    
                    out = '<a href="<?php echo url('edit_employee_salary') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                    out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_employee_salary'); ?>/' + id + '\'\n\
                      title="Delete"><i class="fa fa-trash"></i></a>';
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
        title: "Are you sure you want to delete employee salary ?",
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
