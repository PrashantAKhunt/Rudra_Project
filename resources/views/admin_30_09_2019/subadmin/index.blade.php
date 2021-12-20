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
                
                <a href="{{ route('admin.add_subadmin') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Sub-admin</a>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="subadmin_table" class="table table-striped">
                        <thead>
                            <tr>
                                
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
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
                var table = $('#subadmin_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[0, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_subadmin_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        
                        {"taregts": 0, 'data': 'name'
                        },
                        {"taregts": 1, 'data': 'email'
                        },
                        {"taregts": 2, 'data': 'mobile'
                        },
                        
                        {"taregts": 4,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if(row.status=='Enabled'){
                                out += '<a href="<?php echo url('admin.admin_change_status') ?>'+'/'+id+'/Disabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                                }
                                else{
                                out += '<a href="<?php echo url('admin.admin_change_status') ?>'+'/'+id+'/Enabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';    
                                }
                                return out;
                            }
                        },
                        
                        {"taregts": 6, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '<a href="<?php echo url('admin/edit_subadmin') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                return out;
                            }
                        },
                    ]

                });
            })
        </script>
        @endsection