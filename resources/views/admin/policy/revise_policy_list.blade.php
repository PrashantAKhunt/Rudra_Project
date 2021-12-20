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
                <?php
                $role = explode(',', $access_rule);
                if (in_array(3, $role)) {
                    ?>
                    <!-- <a href="{{ route('admin.add_policy') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Policy</a> -->
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br>                
                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Revise Number</th>
                                <th>Revise Title</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>  
            <div id="policyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">Userlist</h4>
                        </div>
                        <div class="modal-body" id="userTable">
                            <table id="user_policyTable" class="table table-striped">
                            </table>
                        </div>
                        <div class="col-md-12 pull-left">
                            <form action="{{ route('admin.confirm_user_policy') }}" id="confirm_user_policy" method="post">
                                @csrf
                                <input type="hidden" name="id" id="id">
                                <input type="hidden" name="policy_id" id="policy_id">
                                <input type="hidden" name="status" id="status">
                                <div class="clearfix"></div>
                                <br>
                                <?php
                                if (in_array(5, $role) && in_array(2, $role) && (Auth::user()->role == 4 || Auth::user()->role == 1)) {
                                    ?>
                                    <!--<button type="button" onclick="UserConfirmPolicy('Approved')" class="btn btn-success">Confirm Policy</button>-->
                                    <!--<button type="button" onclick="UserConfirmPolicy('Rejected')" class="btn btn-danger">Reject Policy</button>-->
                                    <?php
                                }
                                ?>
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
        </div>    
        @endsection

        @section('script')		
        <script>
            $(document).ready(function () {
                var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');

                var table = $('#policy_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "ajax": {
                        url: "<?php echo route('admin.get_revise_policy_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "data": "title"},
                        {"taregts": 1, "searchable": true, "data": "revise_number"},
                        {"taregts": 2, "searchable": true, "data": "revise_note"},
                        {"taregts": 3,
                            "render": function (data, type, row) {
                                if (row.status == 'Pending')
                                {
                                    return '<b class="text-warning">Pending</b>';
                                    /*out = '<b class="text-warning">Pending</b>';
                                    role_id = "<?php Auth::user()->role; ?>";
                                    if ((($.inArray('2', access_rule) !== -1) && ($.inArray('5', access_rule) !== -1)) || (role_id == 4 || role_id == 1)) {
                                        return '';
                                        // out += ' <a class="btn btn-success" href="<?php echo url("approve_revise_policy") ?>'+'/'+row.id+'">Approved Policy</a>';
                                        out += ' <button type="button" onclick=revisePolicy("<?php echo url("approve_revise_policy") ?>' + '/' + row.id + '","Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                        out += ' <button type="button" onclick=revisePolicy("<?php echo url("reject_revise_policy") ?>' + '/' + row.id + '","Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                    }
                                    return out;*/
                                } else if (row.status == 'Approved')
                                {
                                    return '<b class="text-success">Approved</b>';
                                } else
                                {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },
                        {"taregts": 4, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                // if (($.inArray('2',access_rule) !== -1)) {
                                out += '<a href="<?php echo url("revise_policy_user_list") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-list"></i></a>';
                                //}
                                if (($.inArray('2', access_rule) !== -1) && ($.inArray('5', access_rule) !== -1)) {
                                    out += '<a data-toggle="modal" data-target="#policyModal" onclick="getUserDetails(' + row.id + ')" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>';
                                }
                                return out;
                            }
                        }
                    ]
                });
            })

            function revisePolicy(url, status) {
                swal({
                    title: "Are you sure you want to confirm " + status + " policy ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    location.href = url;
                });
            }
            function getUserDetails(id)
            {
                // AJAX request
                $.ajax({
                    url: "<?php echo url('get_policy_user_list') ?>" + "/" + id + "",
                    method: 'get',
                    data: {revise_policy_id: id},
                    dataType: 'json',
                    success: function (response) {
                        var myJSON = JSON.stringify(response);
                        if (response.length == 0 || response.status == 0)
                        {
                            $('#user_policyTable').empty();
                            $('#user_policyTable').append('<span>No Records Found !</span>');
                        } else {
                            //var myJSON = JSON.stringify(response);
                            //console.log(myJSON);
                            var html = '<thead>'
                                    + '<tr>'
                                    + '<th>Name</th>'
                                    + '<th>Status</th>'
                                    + '</tr>'
                                    + '</thead>'
                                    + '<tbody>';
                            $.each(response, function (k, v) {
                                html += '<tr>'
                                        + '<td>'
                                        + v.name
                                        + '</td>'
                                        + '<td>'
                                        + v.status
                                        + '</td>'
                                        + '</tr>'
                                $('#policy_id').val(v.policy_id);
                                $('#id').val(v.revise_policy_id);
                            });
                            html += '</tbody>'
                            $('#user_policyTable').empty();
                            $('#user_policyTable').append(html);
                        }
                    }
                });
            }
            function UserConfirmPolicy(msg) {
                $('#status').val(msg);
                swal({
                    title: "Are you sure you want to " + msg + " revise policy?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    $("#confirm_user_policy").submit();
                });
            }
        </script>
        @endsection