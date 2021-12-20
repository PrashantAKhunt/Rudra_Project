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

                <!-- <a href="{{ route('admin.add_prelimary_process') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Action Process</a> -->

                <h4>Prelimary Action for Work related to documents</h4>
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="process_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Inward Registry No</th>
                                <th>Inward Title</th>
                                <th>Due Datetime</th>
                                <th>Querry Detail</th>
                                <th>Work allotment to department</th>
                                <th>Main/Prime Employee</th>
                                <th>Inward Document</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                           @if($process_list->count() > 0)
                            @foreach($process_list as $list_data)
                            <tr>
                               <td>{{$list_data->inward_outward_no}}</td>
                               <td>{{$list_data->inward_outward_title}}</td>
                                <td>  {{ Carbon\Carbon::parse($list_data->expected_ans_date)->format('d-m-Y h:i a') }}</td>
                                <td>{{$list_data->querry_details}}</td>
                                <td>{{$list_data->dept_name}}</td>
                                <td>{{$list_data->prime_employee_name}}</td>
                                <td>
                                    <a title="Download" href="{{ asset('storage/'.str_replace('public/','',!empty($list_data->document_file) ? $list_data->document_file : '')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a>
                                </td>
                                <td>
                                @if($list_data->prime_user_status == 'Assigned')
                                <b class="text-warning">
                                      {{ $list_data->prime_user_status }}</b>
                                @elseif($list_data->prime_user_status == 'Rejected')

                                      <textarea style="display:none;" id="reject__detail_{{ $list_data->id }}">{{ $list_data->reject_reason  }}</textarea>
                                       <button class="btn btn-danger btn-rounded" data-toggle="modal" data-target="#reject_detail_modal" onclick="reject_assign_reason('{{ $list_data->id }}')">Rejected</button>

                                @else
                                <b class="text-success">
                                       {{ $list_data->prime_user_status }}</b>
                                @endif
                                </td>
                                <td>
                                @if($list_data->prime_user_status != 'Accepted')
                                <a href="{{ route('admin.edit_prelimary_process',['id'=>$list_data->id]) }}" title="Edit"  class="btn btn-primary btn-rounded"> <i class="glyphicon glyphicon-edit"></i></a>
                                @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
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
            </div>
            <!--row -->

        </div>

        @endsection

        @section('script')
        <script>
        $(document).ready(function(){
            $('#process_table').DataTable({
                    stateSave:true
                });
        });
        //==============================
        function reject_assign_reason(id){
                $('#reject_reason').html($('#reject__detail_'+id).text());
            }
        </script>
        @endsection
