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
                    <table id="leave_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Applier Name</th>
                                <th>LEAVE DATE</th>
                                <th>Work</th>  
                                <th>Leave Deatil</th>                              
                                <th>STATUS</th>
                                <th>ACTION</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($reliever_leave_list)) {
                                foreach ($reliever_leave_list as $key => $reliever_leave_list_data) {
                                    ?>  
                                    <tr role="row">
                                        <?php
                                        $diff = date(strtotime($reliever_leave_list_data->end_date)) - date(strtotime($reliever_leave_list_data->start_date));
                                        $days = ($diff / 1000 / 60 / 60 / 24) + 1;
                                        $date = date("d, M Y", strtotime($reliever_leave_list_data->start_date));
                                        $show_date = $date . '</br> No. of days - ' . 0;
                                        ?>
                                        <td><?= $reliever_leave_list_data->name; ?></td>
                                        <td><?= date('d, M Y', strtotime($reliever_leave_list_data->start_date)); ?></td>
                                        <td>
                                            <input type="hidden" id="work_detail_{{$reliever_leave_list_data->id}}" value="{{ $reliever_leave_list_data->assign_work_details }}" />
                                            <button onclick="set_work_detail({{ $reliever_leave_list_data->id }})" class="btn btn-info btn-rounded" type="button" data-toggle="modal" data-target="#work_detail_modal" title="Work Detail"><i class="fa fa-eye"></i></button>
                                        </td>
                                        <td>
                                            <input type="hidden" id="detail_{{ $reliever_leave_list_data->leave_id}}" value="{{ $reliever_leave_list_data->description }}" />
                                            <button onclick="show_detail({{ $reliever_leave_list_data->leave_id }})" class="btn btn-warning btn-rounded" type="button" data-toggle="modal" data-target="#details_modal" title="Work Detail"><i class="fa fa-eye"></i></button>
                                         
                                        </td>
                                        <?php
                                        if ($reliever_leave_list_data->assign_work_status == 'Rejected') {
                                            $class = 'label-danger';
                                            $show_date = 'style="display:none"';
                                        } elseif ($reliever_leave_list_data->assign_work_status == 'Accepted') {
                                            $class = 'label-success';
                                            $show_date = 'style="display:none"';
                                        } else {
                                            $class = 'label-info';
                                            $show_date = 'style="display:block"';
                                        }
                                        ?>
                                        <td><span class="label <?php echo $class; ?> rounded"><?= $reliever_leave_list_data->assign_work_status; ?></span></td>
                                        <td>
                                            <div <?= $show_date; ?>>
                                                <a onclick="AcceptRelieving('<?php echo route('admin.relival_change_status', ['id' => $reliever_leave_list_data->leave_id, 'status' => 'Accepted']); ?>')" class="btn btn-success" title="Click To Accept">Accept</a>
                                                <a data-toggle="modal" data-target="#relievModal" onclick="set_leave_id('<?php echo $reliever_leave_list_data->leave_id; ?>')" class="btn btn-danger" title="Click To Reject">Reject</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                    <div id="relievModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="myModalLabel">Relieval Reject Note</h4>
                                </div>
                                <div class="modal-body" id="userTable">
                                    <form action="{{ route('admin.confirm_relieving') }}" id="user_relieving_form" method="post">
                                        @csrf
                                        <input type="hidden" name="id" id="id" value="">
                                        <textarea name="reason_note" rows="5" required=""></textarea>
                                        <div class="clearfix"></div>
                                        <br>
                                        </div>
                                        <div class="col-md-12 pull-left">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" onclick="ConfirmRelieving('Rejected')" class="btn btn-danger">Reject</button>
                                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                                    </form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div> 
                 <!-- Leave Detail Model -->
                 <div id="details_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Detail</h4>
                    </div>

                    <div class="modal-body" id="detail_div">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- End model here -->   
                </div>
            </div>
            <!--row -->
        </div>        	


        <div id="work_detail_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Work Detail</h4>
                    </div>
                    <div class="modal-body" style="word-break: break-all;" id="work_detail_body">

                    </div>


                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div> 
        @endsection
        @section('script')		
        <script>
            function show_detail(id) {
                $('#detail_div').html($('#detail_' + id).val());

            }
            function set_work_detail(id){
            $('#work_detail_body').html($('#work_detail_' + id).val());
            }
            function set_leave_id(id) {
            $('#id').val(id);
            }
            $('#leave_table').DataTable();
            jQuery('#user_relieving_form').validate({
            ignore: [],
                    rules: {
                    reason_note: {
                    required: true,
                    }
                    }
            });
            $(document).ready(function () {

            })
                    function ConfirmRelieving(msg) {
                    swal({
                    title: "Are you sure you want to reject reliev leave",
                            //text: "You want to change status of admin user.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            closeOnConfirm: false
                    }, function () {
                    $("#user_relieving_form").submit();
                    });
                    }

            function AcceptRelieving(url) {
            swal({
            title: "Are you sure you want to Accept reliever leave",
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

        </script>
        @endsection
