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
    </div>
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
                    <table id="assignee_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Assignee</th>
                                <th>Company</th>
                                <th>Department</th>
                                <th>Custodian </th>
                                <th>Reck Number</th>
                                <th>File Number</th>
                                <th>Pages Number</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Date Of Asssign</th>
                                <th>Return Date</th>
                                <th>Actual Return Date</th>
                                <th>Status</th>
                                <th>Action</th>
                          
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Date -->
        <div class="modal fade" id="return_date" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Please accept and set return date..</h4>
                    </div>
                    <form action="{{ route('admin.assignee_return_date') }}" method="post">
                            @csrf
                        <div class="modal-body">
                            <div class="form-group ">
                            
                              <div class="col-md-6">
                              <label>Date</label>
                                    <input type="hidden" name="hardcopy_id" id="hardcopy_id">
                                    <input type="text" autocomplete="off" name="assignee_returnDate" id="assignee_returnDate" class="form-control" value="" required="required">
                                    </div>
                            </div>
                            <br/>
                            <br>
                            <br>
                            <button type="submit" class="btn btn-success" >Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            <!--  -->

        </div>
        
        @endsection
        @section('script')		
        <script>
            function set_return_date(id){
                $('#hardcopy_id').val(id);        
            }
            function set_actual_return_date(id){
                $('#hard_copy_id').val(id);        
            }

            $(document).ready(function () { 
                jQuery('#assignee_returnDate').datepicker({
            startDate: "+0d",
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd"
        });
        jQuery('#actual_assignee_returnDate').datepicker({
            startDate: "+0d",
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd"
        });
                var table = $('#assignee_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    //stateSave: true,
                    order:[[0,'DESC']],
                    "ajax": {
                        url: "<?php echo route('admin.get_assignee_requests'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "render": function (data, type, row) {
                                return row.assignee_name;
                            }
                        },
                        {"taregts": 1, "searchable": true, "render": function (data, type, row) {
                                return row.company_name;
                            }
                        },
                        {"taregts": 2, "searchable": true, "render": function (data, type, row) {
                                return row.dept_name;
                            }
                        },
                        {"taregts": 3, "searchable": true, "render": function (data, type, row) {
                                return row.custodion_name;
                            }
                        },
                        {"taregts": 4, "searchable": true, "render": function (data, type, row) {
                                return row.reck_number;
                            }
                        },
                        {"taregts": 5, "searchable": true, "data": "file_number"},
                        {"taregts": 6, "searchable": true, "render": function (data, type, row) {
                            var out = '';
                                if (row.start_page != null && row.start_page >0) {
                                      out += row.start_page + ' ' +  'to'  + ' ' + row.end_page;
                                }
                                return out;
                            }
                        },
                        {"taregts": 7, "searchable": true, "data": "type"},
                        {"taregts": 8, "searchable": true, "data": "title"},
                        {"taregts": 9, "searchable": true, "render": function (data, type, row) {
                                return moment(row.created_at).format("DD-MM-YYYY");
                            }
                        },
                        {"taregts": 10, "searchable": true, "render": function (data, type, row) {
                                return row.assignee_returnDate ? moment(row.assignee_returnDate).format("DD-MM-YYYY") : 'NA';
                            }
                        },
                        {"taregts": 11, "searchable": true, "render": function (data, type, row) {
                                return row.assignee_actual_returnDate ? moment(row.assignee_actual_returnDate).format("DD-MM-YYYY") : 'NA';
                            }
                        },
                        {"taregts": 12, "searchable": true, "render": function (data, type, row) {
                            
                            if (row.assignee_status == 'Assigned') {
                                    return '<span class="label label-rouded label-warning">Assigned</span>';
                                } else if(row.assignee_status == 'Accepted'){
                                    return '<span class="label label-rouded label-success">Accepted</span>';
                                } else if(row.assignee_status == 'Rejected'){
                                    return '<span class="label label-rouded label-danger">Rejected</span>';
                                } else {
                                    return '<span class="label label-rouded label-info">Return</span>';
                                }
                            }
                        },
                        {"taregts": 13, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (row.assignee_status == 'Assigned') {
                                    out+= ' <a onclick="set_return_date('+ row.id +');" data-target="#return_date" data-toggle="modal" href="#" class="btn btn-success btn-circle" title="Accept"><i class="fa fa-check"></i></a>';

                                    out += '<a onclick="assignee_rejected(this);" class="btn btn-danger btn-circle" href="#" data-href="<?php echo url('assignee_rejected') ?>'+'/'+ row.id +'"  title="Reject"><i class="fa fa-times"></i></a>';
                                }
                               
                                if (row.assignee_status == 'Accepted' &&  row.assignee_returnDate != null) {
                                    out += '<a onclick="assignee_done(this);" class="btn btn-info btn-rounded" href="#" data-href="<?php echo url('assignee_completed') ?>'+'/'+ row.id +'"  title="Done"><i class="fa fa-check"></i></a>';
                                }
                                return out;        
                            }
                        }
                        
                    ]
                });
            });

            
            function assignee_done(e) {
                swal({
                    title: "Are you sure you want to that complate this document process ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }
            

            function assignee_rejected(e) {
                swal({
                    title: "Are you sure you want to that reject this document process ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }

        </script>
        @endsection