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
        
                @if($view_special_permission)
                <a href="{{ route('admin.add_sender') }}" class="btn btn-primary pull-right" ><i class="fa fa-plus"></i> Add Sender</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="sender_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="500px">Sender Name</th>
                                <th>Sender Detail</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sender_list as $list_data)
                            <tr>
                                <td>{{$list_data->name}}</td>
                                <td>{{$list_data->description}}</td>
                                <td>
                                @if($view_special_permission)
                                    @if($list_data->status=='Enabled')
                                    <a href="{{ route('admin.change_sender_status',['id'=>$list_data->id,'status'=>'Disabled']) }}" class="btn btn-success" title="Click To Disable">Enabled</a>
                                    @else
                                    <a href="{{ route('admin.change_sender_status',['id'=>$list_data->id,'status'=>'Enabled']) }}" class="btn btn-danger" title="Click To Enable">Disabled</a>
                                    @endif
                                @else
                                    @if($list_data->status=='Enabled')
                                    <a href="#" class="btn btn-success" title="Click To Disable">Enabled</a>
                                    @else
                                    <a href="#" class="btn btn-danger" title="Click To Enable">Disabled</a>
                                    @endif
                                @endif

                                </td>
                                <td>
                                    @if($view_special_permission)
                                    <a href="{{ route('admin.edit_sender',['id'=> $list_data->id]) }}" title="Edit"  class="btn btn-primary btn-rounded"> <i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                    <!-- <a href="#" title="Delete" onclick="delete_confirm(this);" data-href="{{ route('admin.delete_sender',['id'=> $list_data->id]) }}" class="btn btn-danger btn-rounded"> <i class="fa fa-trash"></i></a> -->

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
    
        @endsection

        @section('script')
        <script>
            $('#sender_table').DataTable({
                    stateSave:true
                });
        </script>

        <script>
            function delete_confirm(e) {
                swal({
                    title: "Are you sure you want to delete this entry ?",
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