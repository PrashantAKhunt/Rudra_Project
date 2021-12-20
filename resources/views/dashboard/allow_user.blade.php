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
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="allow_user" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>IMEI Number</th>
                                <th>Model Name</th>
                                <th>Status</th>
                                <th>Created date</th>
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
                var table = $('#allow_user').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    // "order": [[4, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_allow_user_list_all'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        
                        {"taregts": 0, 'data': 'name'
                        },
                        {"taregts": 1, 'data': 'imei_number'
                        },
                        {"taregts": 2, 'data': 'model_name'
                        },
                        {"taregts": 3,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if(row.status=='Approved'){
                                out += '<b class="text-success">Approved</b>';
                                }
                                else{
                                out += '<b class="text-danger">Rejected</b>';    
                                }
                                return out;
                            }
                        },
                        {"taregts": 4, "render":function(data,type,row){
                            return moment(row.created_at).format("DD-MM-YYYY");
                        }
                        },
                        
                        {"taregts": 5, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 
                                if(row.status=='Rejected'){
                                    out = '<a href="<?php echo url('approved_device_user') ?>'+'/'+id+'" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>'; 
                                }
                                
                                out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_device_user'); ?>/' + id + '\'\n\
                                title="Delete"><i class="fa fa-trash"></i></a>';
                                return out;
                            }
                        },
                    ]

                });
            })
        function delete_confirm(e) {
            swal({
                title: "Are you sure you want to delete device user ?",
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