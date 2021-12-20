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
                <h4 class="page-title">Approved Letter-head Report</h4>        
               
                <p class="text-muted m-b-30"></p>
                <br>                
                <div class="table-responsive">
                    <table id="pre_signTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Title</th>
                                <th>Company</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Letter Head Ref Number</th>
                                <th>Letter Head Number</th>
                                <th>Vendor Name</th>
                                <th>Requested Content</th>
                                <th>Requested Date</th>
                                <th>Status</th>
                                <th>Deliver Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>  

            <div class="white-box"> 
                <h4 class="page-title">Letter-head Report</h4>        
             
                <p class="text-muted m-b-30"></p>
                <br>                
                <div class="table-responsive">
                    <table id="signTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Title</th>
                                
                                <th>Company</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Letter Head Ref Number</th>
                                <th>Letter Head Number</th>
                                <th>Vendor Name</th>
                                <th>Requested Content</th>
                                <th>Requested Date</th>
                                <th>Status</th>
                                <th>Deliver Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>  

           
        </div>
    </div>
</div>

<div id="letter_content" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Requested Content</h4>
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
@endsection

@section('script')		
<script>
    jQuery('#confirm_pre_request').validate({
        ignore: [],
        rules: {
            letter_head_image: {
                required: true,
            },
            letter_head_number: {
                required: true,
            },
        }
    });
    jQuery('#confirm_pro_request').validate({
        ignore: [],
        rules: {
            letter_head_image: {
                required: true,
            },
            letter_head_number: {
                required: true,
            },
        }
    });
    $(document).ready(function () {
      
        $('#letter_head_numbers').select2();
        $('#letter_head_number').select2();
       
        var table = $('#pre_signTable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            stateSave: true,
            order:[[8,'DESC']],
            "ajax": {
                url: "<?php echo route('admin.approved_letter_head_report_list'); ?>",
                type: "GET",
            },
            "columns": [
                {"taregts": 0, "searchable": true, "data": "user_name"},
                {"taregts": 1, "searchable": true, "data": "title"},
                {"taregts": 2, "searchable": true, "data": "company_name"},
                {"taregts": 3, "searchable": true, "data": "project_name"}, 
                {"taregts": 4, "searchable": true, "data": "other_project_detail"},
                {"taregts": 5, "searchable": true, "data": "letter_head_ref_no"},
                {"taregts": 6, "searchable": true, "data": "letter_head_number"},
                {"taregts": 7, "searchable": true, "data": "vendor_name"}, 
                {"taregts": 8, "searchable": false,"orderable": false, "render": function (data, type, row) {

                        return "<textarea style='display:none;' id='letter_content_input_" + row.id + "' >"+row.note+"</textarea><a href='#' class='btn btn-info btn-rounded' onclick='open_letter_content(" + row.id + ")' data-toggle='modal' data-target='#letter_content' title='View Content'><i class='fa fa-eye'></i></a>";
                    }
                },
                {"taregts": 9, "searchable": true, "render": function (data, type, row) {
                        return moment(row.created_at).format('DD-MM-YYYY h:m a');
                    }
                    },
                {"taregts": 10,
                    "render": function (data, type, row) {
                        var out = '';
                        if (row.status == 'Approved')
                        {
                            return '<b class="text-success">Approved</b>';
                        } 
                    }
                },
                {"taregts": 11,
                    "render": function (data, type, row) {
                        var out = '';
                        if (row.is_deliver_status == 'In-Process')
                        {
                            return'<b class="text-warning">In-Process</b>';
                        } else if (row.is_deliver_status == 'Delivered')
                        {
                            return '<b class="text-success">Delivered</b>';
                        } else
                        {
                            return '<b class="text-danger">Pending</b>';
                        }
                    }
                },
                {"taregts": 12, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = "";
                        out +='<a title="Download Content Document file" class="btn btn-rounded btn-info" href="<?php echo url("download_letter_head_content") ?>' + '/' + id + '" target="_blank"><i class="fa fa-download"></i></a> &nbsp';
                        
                        
                       
                        return out;
                    }
                }
            ]
        });
        var table = $('#signTable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            stateSave: true,
            order:[[8,'DESC']],
            "ajax": {
                url: "<?php echo route('admin.letter_head_report_list'); ?>",
                type: "GET",
            },
            "columns": [
                {"taregts": 0, "searchable": true, "data": "user_name"},
                {"taregts": 1, "searchable": true, "data": "title"},
                {"taregts": 2, "searchable": true, "data": "company_name"},
                {"taregts": 3, "searchable": true, "data": "project_name"}, 
                {"taregts": 4, "searchable": true, "data": "other_project_detail"},
                {"taregts": 5, "searchable": true, "data": "letter_head_ref_no"},
                {"taregts": 6, "searchable": true, "data": "letter_head_number"},
                {"taregts": 7, "searchable": true, "data": "vendor_name"}, 
                {"taregts": 8, "searchable": false,"orderable": false, "render": function (data, type, row) {
                        
                        return "<textarea style='display:none;' id='letter_content_input_" + row.id + "' >"+row.note+"</textarea><a href='#' class='btn btn-info btn-rounded' onclick='open_letter_content(" + row.id + ")' data-toggle='modal' data-target='#letter_content' title='View Content'><i class='fa fa-eye'></i></a>";
                    }
                },
                    {"taregts": 9, "searchable": true, "render": function (data, type, row) {
                        return moment(row.created_at).format('DD-MM-YYYY h:m a');
                    }
                    },
                {"taregts": 10,
                    "render": function (data, type, row) {
                        var out = '';
                        if (row.status == 'Approved')
                        {
                            return '<b class="text-success">Approved</b>';
                        } 
                    }
                },
                {"taregts": 11,
                    "render": function (data, type, row) {
                        var out = '';
                        if (row.is_deliver_status == 'In-Process')
                        {
                            return'<b class="text-warning">In-Process</b>';
                        } else if (row.is_deliver_status == 'Delivered')
                        {
                            return '<b class="text-success">Delivered</b>';
                        } else
                        {
                            return '<b class="text-danger">Pending</b>';
                        }
                    }
                },
                {"taregts": 12, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = "";
                        out +='<a title="Download Content Document file" class="btn btn-rounded btn-info" href="<?php echo url("download_normal_letter_head_content") ?>' + '/' + id + '" target="_blank"><i class="fa fa-download"></i></a> &nbsp;';
                       
                       
                        return out;
                    }
                }
            ]
        });
    })
    function open_letter_content(id) {

        $('#tableBodylatterContent').html($('#letter_content_input_' + id).val());
    }

    

    
    
</script>
@endsection