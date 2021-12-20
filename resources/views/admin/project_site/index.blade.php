@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Project Sites</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>

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
            @if($view_special_permission)
                <a href="{{ route('admin.add_sites') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Project Sites</a>
            @endif
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="sites_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Company Name</th>
                                <th>Client Name</th>
                                <th>Projects</th>
                                <th>Site Name</th>
                                <th>Site Address</th>
                                <th>Site Details</th>
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
                var table = $('#sites_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[3, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_list_datatable_ajax'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {"taregts": 0, 'data': 'company_name'
                        },
                        {"taregts": 1, render:function(data,type,row){
                                return row.client_name+'-'+row.location;
                        }
                        },
                        {"taregts": 2, 'data': 'project_name'
                        },
                        {"taregts": 3, 'data': 'site_name'
                        },
                        {"taregts": 4, 'data': 'site_address'
                        },
                        {"taregts": 5, 'data': 'site_details'
                        },
                        {"taregts": 6,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (check_permission) {
                                    if (row.status == 'Enabled') {
                                        out += '<a href="<?php echo url('project_site__status') ?>' + '/' + id + '/Disabled' + '" class="btn btn-success" title="Change Status">' + row.status + '</a>';
                                    } else {
                                        out += '<a href="<?php echo url('project_site__status') ?>' + '/' + id + '/Enabled' + '" class="btn btn-danger" title="Change Status">' + row.status + '</a>';
                                    }
                                } else {
                                    if (row.status == 'Enabled') {
                                        out += '<a href="#" class="btn btn-success" title="Change Status">' + row.status + '</a>';
                                    } else {
                                        out += '<a href="#" class="btn btn-danger" title="Change Status">' + row.status + '</a>';
                                    }
                                }
                                return out;
                            }
                        },
                        {"taregts": 7, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (check_permission) {
                                    out += '<a href="<?php echo url('edit_project_sites') ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                }
                                return out;
                            }
                        },
                    ]

                });
            })

        </script>
        @endsection







