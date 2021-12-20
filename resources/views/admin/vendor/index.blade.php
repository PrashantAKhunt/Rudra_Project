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
                <a href="{{ route('admin.add_vendor') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Vendor</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="vendor_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Vendor name</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Address</th>
                                <th>Pancard Number</th>
                                <th>GST Number</th>
                                <th>Company name</th>
                                <th>Vender Detail</th>
                                <th>Status</th>
                                <th>Created date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
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
                var table = $('#vendor_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
					"stateSave": true,
                    "order": [[0, "DESC"]],
                    "lengthMenu": [[10, 25, 50, 100,500,1000,-1], [10, 25, 50, 100,500,1000]],
                    "ajax": {
                        url: "<?php echo route('admin.get_vendor_list_all'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'vendor_name' },
                        {"taregts": 1, 'data': 'email' },
                        {"taregts": 2, 'data': 'contact_no' },
                        {"taregts": 3, 'data': 'address' },
                        {"taregts": 4, 'data': 'pan_card_number' },
                        {"taregts": 5, 'data': 'gst_number' },
                        {"taregts": 6, 'data': 'company_name' },
                        {"taregts": 7, 'data': 'detail' },
                        {"taregts": 8,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                            if (check_permission) {
                                if(row.status=='Enabled'){
                                out += '<a href="<?php echo url('change_vendor_status') ?>'+'/'+id+'/Disabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                                }
                                else{
                                out += '<a href="<?php echo url('change_vendor_status') ?>'+'/'+id+'/Enabled'+'" class="btn btn-danger" title="Change Status">'+row.status+'</a>';
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
                        {"taregts": 9, "render":function(data,type,row){
                            return moment(row.created_at).format("DD-MM-YYYY");
                        }
                        },
                        {"taregts": 10, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out="";
                                if (check_permission) {
                                    out += '<a href="<?php echo url('edit_vendor') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                }
                                return out;
                            }
                        },
                    ]
                });
            })
        </script>
        @endsection
