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
                <a href="{{ route('admin.add_client') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Client</a>
            @endif
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="client_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Client name</th>
                                <th>Location</th>
                                <th>Company name</th>
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
        <div id="budgetSheetFilesModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Files</h4>
                    </div>

                    <br>
                    <br>

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>phone Number</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody id="file_table">

                        </tbody>
                    </table>

                    <!-- <div class="modal-body" id="files">
                    </div> -->

                </div>
                <!-- /.modal-content -->
            </div>
        </div>
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                var check_permission = <?php echo json_encode($view_special_permission); ?>;
                var table = $('#client_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
					"stateSave": true,
                    "order": [[0, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_client_list_all'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'client_name' },
                        {"taregts": 1, 'data': 'location' },
                        {"taregts": 2, 'data': 'company_name' },
                        {"taregts": 3,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (check_permission) {
                                    if(row.status=='Enabled'){
                                    out += '<a href="<?php echo url('change_client_status') ?>'+'/'+id+'/Disabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                                    }
                                    else{
                                    out += '<a href="<?php echo url('change_client_status') ?>'+'/'+id+'/Enabled'+'" class="btn btn-danger" title="Change Status">'+row.status+'</a>';
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
                        {"taregts": 4, "render":function(data,type,row){
                            return moment(row.created_at).format("DD-MM-YYYY");
                        }
                        },
                        {"taregts": 5, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out="";
                                if (check_permission) {
                                    out = '<a href="<?php echo url('edit_client') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                }
                                out += '<a href="#" onclick="get_client_contact_list(' + id + ');" title="View Files" id="showFiles" data-target="#budgetSheetFilesModel" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>';
                                return out;
                            }
                        },
                    ]
                });
            })
        </script>
        <script>
            function get_client_contact_list(id) {

                $.ajax({
                    url: "{{ route('admin.get_client_contact_list') }}",
                    type: "post",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    success: function(data) {
                        var trHTML = '';
                        $('#delete_head').show();
                        if (data.status) {

                            let client_files_arr = data.data.client_contact_files;
                            if (client_files_arr.length == 0) {

                                $('#file_table').empty();
                                trHTML += '<span>No Records Found !</span>';
                                $('#file_table').append(trHTML);

                            } else {


                                $('#file_table').empty();

                                $.each(client_files_arr, function(index, files_obj) {

                                    no = index + 1;
                                    trHTML += '<tr>' +
                                        '<td>' + no + '</td>' +
                                        '<td>' + files_obj.client_name + '</td>' +
                                        '<td>' + files_obj.client_email + '</td>' +
                                        '<td>' + files_obj.client_phone_number + '</td></tr>';

                                });
                                $('#file_table').append(trHTML);
                            }

                        } else {

                            $('#file_table').empty();
                            trHTML += '<span>No Records Found !</span>';
                            $('#file_table').append(trHTML);
                        }

                    }
                });
            }
        </script>
        @endsection
