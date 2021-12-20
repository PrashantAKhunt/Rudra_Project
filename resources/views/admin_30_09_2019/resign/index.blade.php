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
                @if($check_own_resign==0 && Auth::user()->role!=1)
                <a href="{{ route('admin.add_resign') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Submit Resign Request</a>
                <p class="text-muted m-b-30"></p>
                <br>
                @endif
                <div class="table-responsive">
                    <table id="resign_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Name</th>
                                <th>Reason</th>
                                <th>Resign Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resign_list as $resign)
                            <tr>
                                <td>{{ $resign->name }}</td>
                                <td>{{ $resign->reason }}</td>
                                <td>{{ date('d-m-Y',strtotime($resign->created_at)) }}</td>
                                <td>
                                    @if($resign->status=="Pending")
                                    <span class="text-warning">{{ $resign->status }}</span>
                                    @elseif($resign->status=="Approved")
                                    <span class="text-success">{{ $resign->status }}</span>
                                    @elseif($resign->status=="Rejected")
                                    <span class="text-danger">{{ $resign->status }}</span>
                                    @else
                                    <span class="text-warning">{{ $resign->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" onclick="get_resign_detail({{$resign->id}});" data-target="#resign_view" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>
                                    @if($resign->user_id==Auth::user()->id && in_array(2,$permission_arr))
                                    <a href="{{ route('admin.edit_resign',['id'=>$resign->id]) }}" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>
                                    @endif
                                    @if($resign->user_id!=Auth::user()->id && (in_array(5,$permission_arr) || in_array(6,$permission_arr)) && in_array(2,$permission_arr) && $resign->status=='Pending')
                                        @if(Auth::user()->role==5 && $resign->first_approval_status=='Pending')
                                            <a onclick="get_approve_user_resign({{$resign->id}});" data-target="#approve_resign_view" data-toggle="modal" href="" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        @elseif(Auth::user()->role==4 && $resign->second_approval_status=='Pending')
                                            <a onclick="get_approve_user_resign({{$resign->id}});" data-target="#approve_resign_view" data-toggle="modal" href="" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        @elseif(Auth::user()->role==1 && $resign->final_approval_status=='Pending')
                                            <a onclick="get_approve_user_resign({{$resign->id}});" data-target="#approve_resign_view" data-toggle="modal" href="" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
        <div class="modal fade" id="resign_view" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Resignation Detail</h4>
                    </div>
                    <div class="modal-body">
                        <p><b>Reason:</b> <span id="resign_reason"></span></p>
                        <p><b>Resignation Details:</b></p>
                        <p id="resign_detail"></p>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="modal fade" id="approve_resign_view" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Approval Resignation Detail</h4>
                    </div>
                    <div class="modal-body">
                       <table id="asset_expenseTable" class="table table-striped">
                      </table>
                         <form action="{{ route('admin.confirm_resign') }}" id="confirm_resign" method="post" enctype="multipart/form-data">
                            @csrf                           
                            <div class="form-group "> 
                                <label>Note</label> 
                                <textarea class="form-control" rows="5" name="note" id="note" spellcheck="false"></textarea>
                            </div>
                            @if($resign->user_id!=Auth::user()->id && (in_array(5,$permission_arr) || in_array(6,$permission_arr)) && in_array(2,$permission_arr) && $resign->status=='Pending')
                                <?php 
                                if(Auth::user()->role==5 && $resign->status=='Pending' && $resign->first_approval_status=='Pending') {
                                ?>
                                    <button type="button" onclick="ConfirmResign('Approved',{{$resign->id}})" class="btn btn-success">Approve Resign</button>
                                    <button type="button" onclick="ConfirmResign('Rejected',{{$resign->id}})" class="btn btn-danger">Reject Resign</button>
                                <?php
                                }
                                ?>

                                <?php 
                                if(Auth::user()->role==4 && $resign->status=='Pending' && $resign->second_approval_status=='Pending' && $resign->first_approval_status=='Approved') {
                                ?>
                                    <button type="button" onclick="ConfirmResign('Approved',{{$resign->id}})" class="btn btn-success">Approve Resign</button>
                                    <button type="button" onclick="ConfirmResign('Rejected',{{$resign->id}})" class="btn btn-danger">Reject Resign</button>
                                <?php
                                }
                                ?>


                                <?php 
                                if(Auth::user()->role==1 && $resign->status=='Pending' && $resign->final_approval_status=='Pending' && $resign->first_approval_status=='Approved' && $resign->second_approval_status=='Approved') {
                                ?>
                                    <button type="button" onclick="ConfirmResign('Approved',{{$resign->id}})" class="btn btn-success">Approve Resign</button>
                                    <button type="button" onclick="ConfirmResign('Rejected',{{$resign->id}})" class="btn btn-danger">Reject Resign</button>
                                <?php
                                }
                                ?>


                            @endif
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        @endsection
        @section('script')
        <script>
            function get_resign_detail(id) {
            $('#resign_reason').html('');
            $('#resign_detail').html('');
            $.ajax({
            url: "{{ route('admin.get_resign_detail') }}",
                    type: "post",
                    dataType: "json",
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {id: id},
                    success: function (data) {
                    if (data.status) {
                    $('#resign_reason').html(data.data[0].reason);
                    $('#resign_detail').html(data.data[0].resign_details);
                    }
                    }
            });
            }

            function get_approve_user_resign(id) {
                $('#resign_reason').html('');
                $('#resign_detail').html('');
                $.ajax({
                url: "{{ route('admin.get_resign_detail') }}",
                        type: "post",
                        dataType: "json",
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {id: id},
                        success: function (data) {
                        if (data.status) {
                            var html = '<thead>'
                                +'<tr>'
                                +'<th>First Approval Status</th>'
                                +'<th>Seconds Approval Status</th>'
                                +'<th>Final Approval Status</th>'
                                +'</tr>'
                                +'</thead>'
                                +'<tbody>'; 
                            $.each(data.data, function(k, v) {
                             var fclass = 'text-warning';
                             if(v.first_approval_status=='Approved'){
                              fclass='text-success';
                             }
                             
                             var sclass = 'text-warning';
                             if(v.second_approval_status=='Approved'){
                              sclass='text-success';
                             }
                             
                             var finalclass = 'text-warning';
                             if(v.final_approval_status=='Approved'){
                              finalclass='text-success';
                             }

                             html+='<tr>'
                                    +'<td><span class="'+fclass+'">'
                                    +v.first_approval_status
                                    +'</span></td>'
                                    +'<td><span class="'+sclass+'">'
                                    +v.second_approval_status
                                    +'</span></td>'
                                    +'<td><span class="'+finalclass+'">'
                                    +v.final_approval_status
                                    +'</span></td>'
                                    +'</tr>'
                            }); 
                            html+='</tbody>'
                            $('#asset_expenseTable').empty();
                            $('#asset_expenseTable').append(html);
                        }
                    }
                });
            }

            function ConfirmResign(msg,id) {
                var note = $('#note').val();
                swal({
                    title: "Are you sure you want to "+msg+" resign ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: true
                }, function () {
                    $.ajax({
                        url: "{{ route('admin.confirm_resign') }}",
                            type: "post",
                            dataType: "json",
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {id: id,approval_status:msg,note:note},
                            success: function (data) {
                            if (data.status) {
                                //$("#confirm_resign").submit();
                                var url = "<?php echo url('resign') ?>"
                                location.href = url
                            }
                        }
                    });
                });
            }

            $(document).ready(function () {
                $('#resign_table').DataTable();
            });
        </script>
        @endsection