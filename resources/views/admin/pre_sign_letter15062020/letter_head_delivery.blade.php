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
                <h4 class="page-title">Pre-signed Letter-head Request</h4>        
                <?php
                $role = explode(',', $access_rule);
                if (in_array(3, $role)) {
                    ?>
                <?php } ?>
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
                <h4 class="page-title">Blank Letter-head Request</h4>        
                <?php
                $role = explode(',', $access_rule);
                if (in_array(3, $role)) {
                    ?>
                <?php } ?>
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

            <div id="confirmPreDelivery" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">Confirm Pre-Signed Letter-head Delivery</h4>
                        </div>
                        <form action="{{ route('admin.confirm_pre_request') }}" id="confirm_pre_request" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body" id="userTable">
                                <div class="form-group "> 
                                    <label>Letter Head PDF</label> 
                                    <input type="file" name="letter_head_image" id="letter_head_image" class="form-control" accept="application/pdf" data-accept="pdf" > 
                                </div>

                                 <div class="form-group "> 
                                    <label>Letter Head Number</label> 
                                    <select id='letter_head_number' name='letter_head_number[]' class="select2 m-b-10 select2-multiple" multiple="multiple">
                                        
                                        <?php
                                         foreach ($letter_head_data_signed as $key => $letter_head_data_value) {
                                            echo "<option value=".$letter_head_data_value['id'].">".$letter_head_data_value['letter_head_number']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <div class="col-md-12 pull-left">

                                    <input type="hidden" name="pre_id" id="pre_id">
                                    <input type="hidden" name="policy_id" id="policy_id">
                                    <input type="hidden" name="status" id="status">


                                    <button type="button" onclick="UserConfirmRequest('Approved', 'confirm_pre_request')" class="btn btn-success">Confirm Delivery</button>

                                </div>

                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div id="confirmProDelivery" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">Confirm Letter-head Delivery</h4>
                        </div>
                        <form action="{{ route('admin.confirm_pro_request') }}" id="confirm_pro_request" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="pro_id" id="pro_id">
                            <div class="modal-body" id="userTable">
                                <div class="form-group "> 
                                    <label>Letter Head PDF</label>
                                    <input type="file" name="letter_head_image" id="letter_head_image" class="form-control" accept="application/pdf" data-accept="pdf"> 
                                </div>

                                <div class="form-group "> 
                                    <label>Letter Head Number</label> 
                                    <select id='letter_head_numbers' name='letter_head_numbers[]' class="select2 m-b-10 select2-multiple" multiple="multiple">
                                        
                                        <?php
                                         foreach ($letter_head_data_blank as $key => $letter_head_data_value) {
                                            echo "<option value=".$letter_head_data_value['id'].">".$letter_head_data_value['letter_head_number']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <div class="col-md-12 pull-left">


                                    <button type="button" onclick="UserConfirmRequest('Approved', 'confirm_pro_request')" class="btn btn-success">Confirm Delivery</button>

                                </div>
                            </div>

                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>            
                <!-- /.modal-dialog -->



            </div>

            <div id="viewLetterModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">View Letter-head</h4>
                        </div>
                        <div class="modal-body" id="letterView">
                            <!-- <img src="" id="letterImage"> -->
                            <iframe id="letterImage" src="" height="500px" width="100%"></iframe>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
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
        var access_rule = '<?php echo $access_rule; ?>';
        $('#letter_head_numbers').select2();
        $('#letter_head_number').select2();
        access_rule = access_rule.split(',');
        var table = $('#pre_signTable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            stateSave: true,
            order:[[8,'DESC']],
            "ajax": {
                url: "<?php echo route('admin.letter_head_delivery_list'); ?>",
                type: "GET",
            },
            "columns": [
                {"taregts": 0, "searchable": true, "data": "user_name"},
                {"taregts": 1, "searchable": true, "data": "title"},
                {"taregts": 2, "searchable": true, "data": "company_name"},
                {"taregts": 3, "searchable": true, "data": "project_name"}, 
                {"taregts": 4, "searchable": true, "data": "other_project_detail"},
                
                {"taregts": 5, "searchable": true, "data": "letter_head_number"},
                {"taregts": 6, "searchable": true, "data": "vendor_name"}, 
                {"taregts": 7, "searchable": false,"orderable": false, "render": function (data, type, row) {

                        return "<textarea style='display:none;' id='letter_content_input_" + row.id + "' >"+row.note+"</textarea><a href='#' class='btn btn-info btn-rounded' onclick='open_letter_content(" + row.id + ")' data-toggle='modal' data-target='#letter_content' title='View Content'><i class='fa fa-eye'></i></a>";
                    }
                },
                {"taregts": 8, "searchable": true, "render": function (data, type, row) {
                        return moment(row.created_at).format('DD-MM-YYYY h:m a');
                    }
                    },
                {"taregts": 9,
                    "render": function (data, type, row) {
                        var out = '';
                        if (row.status == 'Pending')
                        {
                            return'<b class="text-warning">Pending</b>';
                        } else if (row.status == 'Approved')
                        {
                            return '<b class="text-success">Approved</b>';
                        } else
                        {
                            return '<b class="text-danger">Rejected</b>';
                        }
                    }
                },
                {"taregts": 10,
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
                {"taregts": 11, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = "";
                        out +='<a title="Download Request Content Document file" class="btn btn-rounded btn-info" href="<?php echo url("download_letter_head_content") ?>' + '/' + id + '" target="_blank"><i class="fa fa-download"></i></a> &nbsp';
                        if (($.inArray('5', access_rule) !== -1) && row.is_deliver_status == "In-Process") {
                            out += '<button type="button" onclick=confirmDelivery("<?php echo url("deliver_pre_sign_letter") ?>' + '/' + row.id + '","Approved","pre",' + row.id + ') class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                        }
                        if (row.letter_head_image) {
                            var url = "<?php echo url('/storage'); ?>" + "/" + row.letter_head_image;
                            out += '<button type="button" onclick=viewLetter("' + row.letter_head_image + '") class="btn btn-info btn-circle"><i class="fa fa-eye"></i> </button>';
                        }
                        // if (($.inArray('2',access_rule) !== -1)) {
                        //     out+= '<a href="<?php echo url("delete_policy") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                        // }
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
                url: "<?php echo route('admin.letter_head_delivery_pro_list'); ?>",
                type: "GET",
            },
            "columns": [
                {"taregts": 0, "searchable": true, "data": "user_name"},
                {"taregts": 1, "searchable": true, "data": "title"},
                {"taregts": 2, "searchable": true, "data": "company_name"},
                {"taregts": 3, "searchable": true, "data": "project_name"}, 
                {"taregts": 4, "searchable": true, "data": "other_project_detail"},
                
                {"taregts": 5, "searchable": true, "data": "letter_head_number"},
                {"taregts": 6, "searchable": true, "data": "vendor_name"}, 
                {"taregts": 7, "searchable": false,"orderable": false, "render": function (data, type, row) {

                        return "<textarea style='display:none;' id='letter_content_input_" + row.id + "'>"+row.note+"</textarea><a href='#' class='btn btn-info btn-rounded' onclick='open_letter_content(" + row.id + ")' data-toggle='modal' data-target='#letter_content' title='View Content'><i class='fa fa-eye'></i></a>";
                    }
                },
                    {"taregts": 8, "searchable": true, "render": function (data, type, row) {
                        return moment(row.created_at).format('DD-MM-YYYY h:m a');
                    }
                    },
                {"taregts": 9,
                    "render": function (data, type, row) {
                        var out = '';
                        if (row.status == 'Pending')
                        {
                            return'<b class="text-warning">Pending</b>';
                        } else if (row.status == 'Approved')
                        {
                            return '<b class="text-success">Approved</b>';
                        } else
                        {
                            return '<b class="text-danger">Rejected</b>';
                        }
                    }
                },
                {"taregts": 10,
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
                {"taregts": 11, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = "";
                        out +='<a title="Download Request Content Document file" class="btn btn-rounded btn-info" href="<?php echo url("download_normal_letter_head_content") ?>' + '/' + id + '" target="_blank"><i class="fa fa-download"></i></a> &nbsp;';
                        if (($.inArray('5', access_rule) !== -1) && row.is_deliver_status == "In-Process") {
                            out += ' <button type="button" onclick=confirmDelivery("<?php echo url("deliver_pro_sign_letter") ?>' + '/' + row.id + '","Approved","pro",' + row.id + ') class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                        }
                        if (row.letter_head_image) {
                            var url = "<?php echo url('/storage'); ?>" + "/" + row.letter_head_image;
                            out += '<button type="button" onclick=viewLetter("' + row.letter_head_image + '") class="btn btn-info btn-circle"><i class="fa fa-eye"></i> </button>';
                        }
                        // if (($.inArray('2',access_rule) !== -1)) {
                        //     out+= '<a href="<?php echo url("delete_policy") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                        // }
                        return out;
                    }
                }
            ]
        });
    })
    function open_letter_content(id) {

        $('#tableBodylatterContent').html($('#letter_content_input_' + id).val());
    }

    function confirmDelivery(url, status, msg, id) {
        if (msg == "pre")
        {
            $("#confirmPreDelivery").modal('toggle'); //see here usage    
            $("#pre_id").val(id);
        }
        if (msg == "pro")
        {
            $("#confirmProDelivery").modal('toggle'); //see here usage    
            $("#pro_id").val(id);
        }
    }

    function UserConfirmRequest(msg, id) {
        swal({
            title: "Delivery Confirmation",
            text: "Are you sure you want to give delivery confirmation of hard copy of letter-head?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            $("#" + id).submit();
        });
    }
    function viewLetter(letterImage) {
        var img_url="<?php echo url('/storage/'); ?>"+"/"+letterImage.replace('public/','');
        $("#letterImage").attr("src", img_url);
        $('#viewLetterModel').modal('toggle');
    }
</script>
@endsection