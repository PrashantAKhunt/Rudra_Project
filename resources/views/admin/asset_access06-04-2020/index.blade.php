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
        <div class="col-md-12">
            <div class="white-box">
                <?php
                $role = explode(',', $access_rule);

                if (in_array(3, $role)) {
                    ?>
                    <a href="{{ route('admin.add_asset_access') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Asset Assign</a>
                    <?php
                }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                        <table id="emp_table" class="table table-striped">
                            <thead>
                            <th>User Name</th>
                            <th>Asset Name</th>
                            <th>Assign Date</th>
                            <th>Return Date</th>
                            <th>Currently Assigned</th>
                            <th>Acceptance Status</th>
                            <th>Action</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- sample modal content -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit Asset</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.update_asset_access') }}" id="update_asset_access" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id"/>                            
                        <div class="form-group "> 
                            <label>Select Asset</label>
                            <select class="form-control" name="asset_id" id="asset_id" disabled="">
                                <option value="">Select Asset</option>
                                @foreach($Asset_List as $asset_list_data)
                                <option value="{{ $asset_list_data->id }}">{{ $asset_list_data->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group "> 
                            <label>Employee Name</label> 
                            <select class="form-control" name="user_id" id="user_id">
                                <option value="">Select User</option>
                                @foreach($UsersName as $users_name_data)
                                <option value="{{ $users_name_data->id }}">{{ $users_name_data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group "> 
                            <label>Access Date</label> 
                            <input type="text" class="form-control" name="assign_date" id="assign_date"/>
                        </div>

                        <div class="form-group "> 
                            <label>Asset Description</label> 
                            <textarea class="form-control" rows="5" name="asset_description" id="asset_description">
                            </textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>  

    <!-- sample modal content -->
    <div id="assetModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add Asset Expense</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.add_asset_expense') }}" id="add_asset_expense" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="expense_asset_id" name="expense_asset_id"/>                            
                        <div class="form-group "> 
                            <label>Photo</label>
                            <input type="file" accept="image/png,image/x-png, image/jpg, image/jpeg" name="asset_expense_image" id="asset_expense_image" class="dropify" />
                        </div>
                        <div class="form-group "> 
                            <label>Amount</label> 
                            <input type="text" class="form-control" name="amount" id="amount"/>
                        </div>
                        <div class="form-group "> 
                            <label>Note</label> 
                            <textarea class="form-control" rows="5" name="note" id="note">
                            </textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div id="assetDetailsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Asset Expense Details</h4>
                </div>
                <div class="modal-body" id="tableBody">
                    <table id="asset_expenseTable" class="table table-striped">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>

<div id="reject_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="{{ route('admin.reject_asset_assigned') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Reject Reason</h4>
                </div>
                <div class="modal-body" id="tableBody">
                    <input type="hidden" name="reject_asset_id" id="reject_asset_id" />
                    <textarea class="form-control" name="reject_reason" id="reject_reason"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" >Submit</button>
                </div>
            </div>
        </form>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@endsection


@section('script')
<script>
    $(document).ready(function () {
        $('#update_asset').validate({
            rules: {
                name: {
                    required: true
                },
                description: {
                    required: true
                }
            }
        });
        removeTextAreaWhiteSpace();
        removeTextAreaWhiteSpaceNote();
    });
    jQuery('#assign_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-mm-yyyy"
    });

    jQuery('#return_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-mm-yyyy"
    });
    function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('asset_description');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g, '');
    }
    function removeTextAreaWhiteSpaceNote() {
        var myTxtArea = document.getElementById('note');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g, '');
    }

    function asset_reject(id) {
        $('#reject_asset_id').val(id);
    }

    var access_rule = '<?php echo $access_rule; ?>';
    access_rule = access_rule.split(',');
    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        stateSave: true,
        "order": [[2, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.asset_access_list'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 0, 'data': 'user_name'
            },
            {"taregts": 1, 'data': 'asset_name'
            },
            {"taregts": 2, "render": function (data, type, row) {

                    return moment(row.asset_access_date).format("DD-MM-YYYY");
                }
            },
            {"taregts": 3, "render": function (data, type, row) {

                    return moment(row.asset_return_date).format("DD-MM-YYYY");
                }
            },
            {"taregts": 4, "render": function (data, type, row) {
                    var out = '';
                    if (row.is_allocate) {
                        return '<span class="text-success">Yes</span>';
                    } else {
                        return '<span class="text-danger">No</span>';
                    }
                }
            },
            {"taregts": 5,
                "render": function (data, type, row) {
                    var id = row.is_allocate;
                    var loggedin_user = "<?php echo Auth::user()->id; ?>";
                    
                    if (row.status == "Assigned" && row.asset_access_user_id == loggedin_user) {
                        out = '<a onclick="asset_confirm(this);" data-href="<?php echo url('change_asset_access'); ?>/' + row.id + '/1" class="btn btn-success" title="Accept">Confirm</a>';
                        out += ' <a onclick="asset_reject(' + row.id + ');" data-toggle="modal" data-target="#reject_modal"  class="btn btn-danger" title="Reject">Reject</a>';
                        return out;
                    } else if (row.status == "Assigned" && row.asset_access_user_id != loggedin_user) {
                         return '<span class="text-warning">Assigned</span>';
                    } else if (row.status == "Confirmed") {
                        return '<span class="text-success">Confirmed</span>';
                    } else if (row.status == "Rejected") {
                        return '<span class="text-danger">Rejected</span>';
                    } else {
                        return '<span class="text-info">Submitted</span>';
                    }
                }
            },
            {"taregts": 6, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out = "";

                    if (($.inArray('2', access_rule) !== -1) && ($.inArray('5', access_rule) !== -1)) {
                        out = '<a onclick="getTable(' + id + ')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#myModal" title="Edit"><i class="fa fa-edit"></i></a>';
                    }
                    else if(($.inArray('2', access_rule) !== -1) && row.is_allocate==1){
                        //out = '<a onclick="getTable(' + id + ')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#myModal" title="Edit"><i class="fa fa-edit"></i></a>';
                    }
                    /*if (($.inArray('4',access_rule) !== -1)){
                     out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_asset_access'); ?>/' + id + '\'\n\
                     title="Delete"><i class="fa fa-trash"></i></a>'
                     }*/
                    if (($.inArray('2', access_rule) !== -1) && row.is_allocate==1) {
                        out += '<a onclick="getTable(' + id + ')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#assetModal" title="Add Expense"><i class="fa fa-money" aria-hidden="true"></i></a>';
                    }
                    if (($.inArray('1', access_rule) !== -1)) {
                        out += '<a onclick="getAssetDetails(' + row.asset_id + ')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#assetDetailsModal" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    }
                    return out;
                }
            },
        ]

    });

    jQuery("#update_asset_access").validate({
        ignore: [],
        rules: {
            asset_id: {
                required: true,
            },
            user_id: {
                required: true,
            },
            assign_date: {
                required: true,
            },
            asset_description: {
                required: true,
            }
        }
    });

    jQuery("#add_asset_expense").validate({
        ignore: [],
        rules: {
            expense_asset_id: {
                required: true,
            },
            amount: {
                required: true,
            },
            note: {
                required: true,
            }
        }
    });

    jQuery("#upload_bank_transactions").validate({
        ignore: [],
        rules: {

            file: {
                required: true,
            },

        },

    });
    function changeDateformat(date)
    {
        //return date.split("-");
        var fields = date.split('-');
        var name = fields[0];
        var street = fields[1];
        var Month = ['Jan', 'Feb', 'March', 'April', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return Month[name - 1] + '-' + street;
    }
    function delete_confirm(e) {
        swal({
            title: "Are you sure you want to delete Asset Access?",
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
    function asset_confirm(e) {
        swal({
            title: "Are you sure you want change status ?",
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
    function getTable(id)
    {
        // AJAX request
        $.ajax({
            url: "<?php echo url('edit_asset_access') ?>" + "/" + id + "",
            method: 'get',
            data: {event_id: id},
            dataType: 'json',
            success: function (response) {
                if (response.length == 0)
                {
                    $('#clTable').empty();
                    $('#clTable').append('<span>No Records Found !</span>');
                } else {
                    //var myJSON = JSON.stringify(response);
                    //console.log(myJSON);
                    $.each(response, function (k, v) {
                        $("#id").val(v.id);
                        $("#asset_id").val(v.asset_id);
                        $("#expense_asset_id").val(v.asset_id);
                        $("#assign_date").val(v.asset_access_date);
                        $("#return_date").val(v.asset_return_date);
                        $("#user_id").val(v.asset_access_user_id);
                        $("#asset_description").val(v.asset_access_description);
                    });

                }
            }
        });
    }
    function getAssetDetails(id)
    {
        // AJAX request
        $.ajax({
            url: "<?php echo url('get_asset_expense_details') ?>" + "/" + id + "",
            method: 'get',
            data: {asset_id: id},
            dataType: 'json',
            success: function (response) {
                var myJSON = JSON.stringify(response);
                if (response.length == 0 || response.status == 0)
                {
                    $('#asset_expenseTable').empty();
                    $('#asset_expenseTable').append('<span>No Records Found !</span>');
                } else {
                    //var myJSON = JSON.stringify(response);
                    //console.log(myJSON);
                    var html = '<thead>'
                            + '<tr>'
                            + '<th>Amount</th>'
                            + '<th>Image</th>'
                            + '<th>Note</th>'
                            + '</tr>'
                            + '</thead>'
                            + '<tbody>';
                    $.each(response, function (k, v) {
                        var url = "<?php echo url('/storage/'); ?>" + "/" + v.image;
                        var imgTag = "<img height='100px' width='100px' src=" + url.replace("public/", "") + ">";
                        html += '<tr>'
                                + '<td>'
                                + v.amount
                                + '</td>'
                                + '<td>'
                                + imgTag
                                + '</td>'
                                + '<td>'
                                + v.note
                                + '</td>'
                                + '</tr>'
                    });
                    html += '</tbody>'
                    $('#asset_expenseTable').empty();
                    $('#asset_expenseTable').append(html);
                }
            }
        });
    }
</script>
@endsection
