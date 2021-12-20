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
                @if($payment_card_add_permission)
                    @if($view_special_permission)
                <a href="{{ route('admin.add_payment_card') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Payment Card</a>
                    @endif
                @endif
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="payment_card_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Company</th>
                                <th>Bank Name</th>
                                <th>Account No</th>
                                <th>Card No</th>
                                <th>Card Name</th>
                                <th>Card Type</th>
                                <th>Assigncard Employee</th>
                                <th>Status</th>

                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

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
                var check_permission = <?php echo json_encode($view_special_permission); ?>;
                var table = $('#payment_card_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_payment_card_list'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {"taregts": 0, 'data': 'company_name'
                        },
                        {"taregts": 1, 'data': 'bank_name'
                        },
                        {"taregts": 2, 'data': 'ac_number'
                        },
                        {"taregts": 3, 'data': 'card_number'
                        },
                        {"taregts": 4, 'data': 'name_on_card'
                        },
                        {"taregts": 5, 'data': 'card_type'
                        },
                        {"taregts": 6, 'data': 'assigncard_user'
                        },
                        {"taregts": 7,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (check_permission) {
                                    if(row.status=='Enabled'){
                                    out += '<a href="<?php echo url('change_payment_card_status') ?>'+'/'+id+'/Disabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                                    }
                                    else{
                                    out += '<a href="<?php echo url('change_payment_card_status') ?>'+'/'+id+'/Enabled'+'" class="btn btn-danger" title="Change Status">'+row.status+'</a>';
                                    }
                                } else {
                                    if(row.status=='Enabled'){
                                    out += '<a href="#" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                                    }
                                    else{
                                    out += '<a href="#" class="btn btn-danger" title="Change Status">'+row.status+'</a>';
                                    }
                                }
                                return out;
                            }
                        },


                        {"taregts": 7, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out="";
                                @if($payment_card_edit_permission)
                                if (check_permission) {
                                    out = '<a href="<?php echo url('edit_payment_card') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                    // out +='&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_payment_card'); ?>/' + id + '\'\n\
                                    //         title="Delete"><i class="fa fa-trash"></i></a>';
                                }
                                @endif
                                return out;
                            }
                        },
                    ]

                });

            })
            function delete_confirm(e) {
            swal({
                title: "Are you sure you want to delete Payment Card Detail ?",
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
