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
                <form action="{{ route('admin.delete_cheques') }}" id="delete_cheque_frm" method="post">
                @csrf
                <input type="hidden" name="del_cheque_ids" id="del_cheque_ids"/>
                </form>
                <a href="{{ route('admin.add_rtgs_neft__register') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add NEFT/RTGS Register</a>
                <!-- <button class="btn btn-danger btn-rounded" id="del_cheque">Delete Cheque</button> -->
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                               <!--  <th>#</th> -->
                                <th>Company</th>
                                <th>Bank</th>
                                <th>Project</th>
                                <th>Vendor</th>
                                <th>Check Ref No</th>
                                <th>Chque No</th>
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
                
                $("#del_cheque").click(function(){
                    var favorite = [];
                    $.each($("input[name='delete_ch']:checked"), function(){
                        favorite.push($(this).val());
                    });
                    var id = favorite.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select cheque !",
                            //text: "You want to change status of admin user.",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Okay",
                            closeOnConfirm: true
                        });
                    }
                    else {
                        
                        swal({
                            title: "Are you sure you want to delete cheque ?",
                            //text: "You want to change status of admin user.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            closeOnConfirm: false
                        }, function () {
                           $("#del_cheque_ids").val(id);
                           $("#delete_cheque_frm").submit();
                        });
                    }
                });

                var table = $('#company_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_rtgs_neft_register_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        // {"taregts": 1, "searchable": false, "orderable": false,
                        //     "render": function (data, type, row) {
                        //         var id = row.id;
                        //         out = '&nbsp;<input type="checkbox" value='+id+' name="delete_ch" >';
                        //         return out;
                        //     }
                        // },
                        {"taregts": 2, 'data': 'company_name'
                        },
                        {"taregts": 3, 'data': 'bank_name'
                        },
                        {"taregts": 4, 'data': 'project_name'
                        },
                        {"taregts": 5, 'data': 'vendor_name'
                        },
                        {"taregts": 6, 'data': 'check_ref_no'
                        },
                        {"taregts": 7, 'data': 'ch_no'
                        },
                        {"taregts": 8, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 
                                // out = '<a href="<?php echo url('edit_company') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>'; 
                                out = '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_rtgs_neft_register'); ?>/' + id + '\'\n\
                                        title="Delete"><i class="fa fa-trash"></i></a>';
                                return out;
                            }
                        },
                    ]

                });

            })
            function delete_confirm(e) {
            swal({
                title: "Are you sure you want to delete RTGS/NEFT details?",
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