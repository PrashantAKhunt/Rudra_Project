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
            @if($view_special_permission)
                <a href="{{ route('admin.add_bank_charge_sub_category') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</a>
            @endif
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="bank_charge_table" class="table table-striped">
                        <thead>
                            <tr>

                               
                                <th>Sub-Category Title</th>
                                <th>Category Title</th>
                                <th>Detail</th>
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
        <!-- Modal -->
        <div id="view_project_managers" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Project Manager / Employee</h4>
                </div>
                <div class="modal-body">
                    <div id="put_managers" class="table-responsive"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </div>

            </div>
        </div>
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                var check_permission = <?php echo json_encode($view_special_permission); ?>;
                var table = $('#bank_charge_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "order": [[4, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_bank_sub_charge_table_list'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {"taregts": 0, 'data': 'title'
                        },
                        {"taregts": 1, 'data': 'category_title'
                        },
                        {"taregts": 2, 'data': 'detail'
                        },

                        {"taregts": 3, "render":function(data,type,row){
                            return moment(row.created_at).format("DD-MM-YYYY");
                        }
                        },

                        {"taregts": 4, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out="";
                                if (check_permission) {
                                    out = '<a href="<?php echo url('edit_bank_charge_sub_category') ?>'+'/'+id+'" title="Edit Bank Category" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                }
                                return out;
                            }
                        },
                    ]

                });
            })
        </script>
        @endsection
