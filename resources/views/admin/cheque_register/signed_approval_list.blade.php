@extends('layouts.admin_app')

@section('content')
<?php

use Illuminate\Support\Facades\Config; ?>
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
                    <table id="approval_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Cheque Book Ref No</th>
                                <th>Cheque start No</th>
                                <th>Cheque End No</th>
                                <th>Requested By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $data)
                            <tr>
                                <td>{{$data->cheque_book_ref_no}}</td>
                                <td>{{$data->cheque_start_no}}</td>
                                <td>{{$data->cheque_end_no}}</td>
                                <td>{{$data->name}}</td>
                                <td>
                                    @if($data->status == 'Pending')
                                       <b class="text-warning">Pending</b>
                                    @elseif($data->status == 'Rejected')

                                          <textarea style="display:none;" id="reject__detail_{{ $data->id }}">{{ $data->reject_note  }}</textarea>
                                          <button class="btn btn-danger btn-rounded" data-toggle="modal" data-target="#reject_detail_modal" onclick="reject_assign_reason('{{ $data->id }}')">Rejected</button>
                                          @if($data->status_datetime)
                                                    <br>{{date('d-m-Y h:i A', strtotime($data->status_datetime))}}
                                            @endif
                                    @else
                                       <b class="text-success">Accepted
                                       @if($data->status_datetime)
                                            <br>{{date('d-m-Y h:i A', strtotime($data->status_datetime))}}
                                       @endif
                                       </b>
                                    @endif
                                </td>
                                <td>
                                    @if($data->status == 'Pending' && config::get('constants.SuperUser') == Auth::user()->role)

                                    <a href="#" title="Accept" onclick="accept_confirm(this);" data-href="{{ route('admin.accept_approval_cheque_book',['id'=>$data->id]) }}" class="btn btn-success btn-rounded"> <i class="fa fa-check"></i></a>
                                    <a href="#Mymodel" title="Reject" onclick="reject_confirm(`{{$data->id}}`);" data-toggle="modal" class="btn btn-danger btn-rounded"> <i class="fa fa-times"></i></a>
                                    
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

        <div id="Mymodel" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content" id="model_data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="panel-title">Note: Please add reject not</h3>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('admin.reject_approval_cheque_book') }}" id="add_delivery">
                            @csrf
                            <div class="row">
                                <div class="col-xs-12">

                                    <label for="reject_note">Reject note</label>
                                    <input type="hidden" name="cheque_id" id="cheque_id" value="">
                                    <input type="input" name="reject_note" id="reject_note" value="" class="form-control" required />
                                </div>
                            </div>
                                <br>
                                <div class="row">
                                <div class="col-xs-2">
                                    <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
                                </div>
                                </div>
                            </div>

                        </form>
                    </div>


                </div>
            </div>
        </div>
        <!--  -->
        <div id="reject_detail_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" id="myModalLabel">Reject reason</h4>
                            </div>

                            <div class="modal-body" id="reject_reason">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
        <!--  -->
        @endsection

        @section('script')
        <script>
            $('#approval_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    stateSave:true
                });
        </script>

        <script>
            function accept_confirm(e) {
                swal({
                    title: "Are you sure you want to accept this ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }
            //--------------------------------------
            function reject_confirm(id) {

                $('#cheque_id').val(id);
            }
            //==============================
            function reject_assign_reason(id){
                $('#reject_reason').html($('#reject__detail_'+id).text());
            }
        </script>
        @endsection
