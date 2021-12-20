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
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <!-- <th>Letter Head Ref No</th>
                                <th>Company</th>
                                <th>Letter Head Number</th> -->
                                <th>Company</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Title</th>
                                <th>Letter Head Content</th>
                                <th>Vendor Name</th>
                                <th>Letter Head Ref No</th>
                                <th>Letter Head Number</th>
                                <th>Work Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
        <div id="letter_content" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Letter Head Content</h4>
                    </div>
                    <div class="modal-body" id="tableBodylatterContent">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div id="letter_word_detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Work Detail</h4>
                    </div>
                    <div class="modal-body" id="tableBodyworkDetail">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                var table = $('#company_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_used_letter_head_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'company_name'
                        },
                        {"taregts": 1, 'data': 'project_name'
                        },
                        {"taregts": 2, 'data': 'other_project_detail'
                        },
                        {"taregts": 3, 'data': 'title'
                        },
                        {"taregts": 4, "searchable": false,"orderable": false, "render": function (data, type, row) {

                                return "<textarea style='display:none;' id='letter_content_input_" + row.id + "' >"+row.letter_head_content+"</textarea><a href='#' class='btn btn-info btn-rounded' onclick='open_letter_content(" + row.id + ")' data-toggle='modal' data-target='#letter_content' title='View Content'><i class='fa fa-eye'></i></a>";
                            }
                        },
                        {"taregts": 5, 'data': 'vendor_name'
                        },
                        {"taregts": 6, 'data': 'letter_head_ref_no'
                        },
                        {"taregts": 7, 'data': 'letter_head_number'
                        },
                        {"taregts": 8, "searchable": false,"orderable": false, "render": function (data, type, row) {

                                return "<textarea style='display:none;' id='letter_workdetail_" + row.id + "' >"+row.work_detail+"</textarea><a href='#' class='btn btn-info btn-rounded' onclick='open_work_detail(" + row.id + ")' data-toggle='modal' data-target='#letter_word_detail' title='View Content'><i class='fa fa-eye'></i></a>";
                            }
                        },
                    ]

                });

            })
        function open_letter_content(id) {

            $('#tableBodylatterContent').html($('#letter_content_input_' + id).val());
        }
        function open_work_detail(id) {
            $('#tableBodyworkDetail').html($('#letter_workdetail_' + id).val());
        }
        </script>
        @endsection