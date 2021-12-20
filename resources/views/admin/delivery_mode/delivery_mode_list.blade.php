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
                <a href="#Mymodel" class="btn btn-primary pull-right" title="Add" id="add_btn" data-toggle="modal"><i class="fa fa-plus"></i> Add Delivery Mode</a>
                @endif 
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="doc_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th width="500px">Delivery Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modes as $list_data)
                            <tr>
                                <td>{{$list_data->name}}</td>

                                <td>
                                @if($view_special_permission)
                                    @if($list_data->status=='Enabled')
                                    <a href="{{ route('admin.change_delivery_mode_status',['id'=>$list_data->id,'status'=>'Disabled']) }}" class="btn btn-success" title="Click To Disable">Enabled</a>
                                    @else
                                    <a href="{{ route('admin.change_delivery_mode_status',['id'=>$list_data->id,'status'=>'Enabled']) }}" class="btn btn-danger" title="Click To Enable">Disabled</a>
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
                                    <a href="#myModal" title="Edit" id="edit_btn" onclick="edit_delivery('{{ route('admin.edit_delivery_mode',['id'=>$list_data->id]) }}');" data-toggle="modal" class="btn btn-primary btn-rounded"> <i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                    <!-- <a href="#" title="Edit" id="edit_btn" onclick="delete_confirm(this);" data-href="{{ route('admin.delete_delivery_mode',['id'=>$list_data->id]) }}" class="btn btn-danger btn-rounded"> <i class="fa fa-trash"></i></a> -->

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
        <div id="myModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content" id="model_data">

                </div>
            </div>
        </div>

        <div id="Mymodel" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content" id="model_data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="panel-title">Add Delivery Mode</h3>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('admin.add_delivery_mode') }}" id="add_delivery">
                            @csrf
                            <div class="row">
                                <div class="col-xs-12">

                                    <label for="name">Delivery Name<span class="error">*</span></label>

                                    <input type="input" name="name" id="name" value="" class="form-control" />

                                    <label id="category_name-error" class="error" for="name"></label>
                                </div>
                                <div class="col-xs-2">

                                    <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>


                </div>
            </div>
        </div>
        @endsection

        @section('script')
        <script>
            $('#doc_table').DataTable({
                    stateSave:true
                });
            function edit_delivery(route) {

                $('#model_data').html('');
                $.ajax({
                    url: route,
                    type: "GET",
                    dataType: "html",
                    catch: false,
                    success: function(data) {
                        $('#model_data').append(data);
                    }
                });
            }
        </script>

        <script>
            function delete_confirm(e) {
                swal({
                    title: "Are you sure you want to delete this delivery ?",
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

        <script>
            jQuery("#add_delivery").validate({
                ignore: [],
                rules: {
                    name: {
                        required: true,

                    },
                },
                messages: {
                    name: {
                        required: "This field is require."
                    },
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.parent());
                }

            });
        </script>
        @endsection