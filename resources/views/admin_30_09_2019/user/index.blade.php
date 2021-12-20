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
                 <?php
                 $role = explode(',', $access_rule);
                ?>
                <?php 
                if(in_array(3, $role)){
                ?>
                <a href="{{ route('admin.add_user') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Employee</a>
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                
                                <th>Name</th>
                                <th>Email</th>
                                <th>Joining Date</th>
                                <th>Department</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Register Date</th>
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
                var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');

                var table = $('#user_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[3, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_user_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        
                        {"taregts": 0, 'data': 'name'
                        },
                        {"taregts": 1, 'data': 'email'
                        },
                        {"taregts": 2, 'data': 'joining_date'
                        },
                        {"taregts": 3, 'data': 'dept_name'
                        }, 
                        {"taregts": 4, 'data': 'role_name'
                        },                        
                        {"taregts": 5,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if(row.status=='Enabled'){
                                out += '<a href="<?php echo url('change_status') ?>'+'/'+id+'/Disabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                                }
                                else{
                                out += '<a href="<?php echo url('change_status') ?>'+'/'+id+'/Enabled'+'" class="btn btn-danger" title="Change Status">'+row.status+'</a>';    
                                }
                                return out;
                            }
                        },
                        {"taregts": 6, "render":function(data,type,row){
                                
                                return moment(row.created_at).format("DD-MM-YYYY");
                        }
                        },
                        
                        {"taregts": 7, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out="";
                                if($.inArray('2',access_rule) !== -1){
                                    out = '<a href="<?php echo url('edit_user') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                }
                                if($.inArray('1',access_rule) !== -1){
                                    out +='&nbsp;&nbsp;<a href="<?php echo url('view_user') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>';
                                }
                                if($.inArray('4',access_rule) !== -1){
                                out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_user'); ?>/' + id + '\'\n\
                                    title="Delete"><i class="fa fa-trash"></i></a>';
                                }
                                return out;
                            }
                        },
                    ]

                });
            })
            function delete_confirm(e) {
                swal({
                    title: "Are you sure you want to delete user ?",
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