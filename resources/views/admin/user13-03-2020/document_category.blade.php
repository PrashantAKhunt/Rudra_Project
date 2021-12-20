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
                <?php

                ?>

                <a href="#Mymodel" class="btn btn-primary pull-right" title="Edit" id="edit_btn" data-toggle="modal"><i class="fa fa-plus"></i> Add Category</a>


                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th width="700px">Category Name</th>
                                <th >Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category_list as $list_data)
                            <tr>
                                <td>{{$list_data->category_name}}</td>

                                <td>

                                    @if($list_data->status=='Enabled')
                                    <a href="{{ route('admin.change_doc_status',['id'=>$list_data->id,'status'=>'Disabled']) }}" class="btn btn-success" title="Click To Disable">Enabled</a>
                                    @else
                                    <a href="{{ route('admin.change_doc_status',['id'=>$list_data->id,'status'=>'Enabled']) }}" class="btn btn-danger" title="Click To Enable">Disabled</a>
                                    @endif

                                </td>
                                <td>

                                    <a href="#myModal" title="Edit" id="edit_btn" onclick="edit_document('{{ route('admin.edit_document',['id'=>$list_data->id]) }}');" data-toggle="modal" class="btn btn-primary btn-rounded"> <i class="glyphicon glyphicon-edit"></i></a>
                                    <a href="#" title="Edit" id="edit_btn" onclick="delete_confirm(this);" data-href="delete_document/{{$list_data->id}}" class="btn btn-danger btn-rounded"> <i class="fa fa-trash"></i></a>

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
                        <h3 class="panel-title">Add Category</h3>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('admin.add_document') }}" id="add_category">
                            @csrf
                            <div class="row">
                                <div class="col-xs-12">

                                    <label for="category_name">Category Name</label>

                                    <input type="input" name="category_name" id="category_name" value="" class="form-control" />

                                    <label id="category_name-error" class="error" for="category_name"></label>
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
            function edit_document(route) {


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
                    title: "Are you sure you want to delete category ?",
                    //text: "You want to change status of admin user.",
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
            jQuery("#add_category").validate({
                ignore: [],
                rules: {
                    category_name: {
                        required: true,

                    },
                },
                messages: {
                    category_name: {
                        required: "This field is require."
                    },
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.parent());
                }

            });
        </script>
        @endsection