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
                <li><a href="{{ route('admin.inward_outward') }}">{{ $module_title }}</a></li>
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
                    <table id="doc_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Document Inward Number</th>
                                <th>Inward Title</th>
                                <th>Description</th>
                                <th>Mode of Documents Delivered</th>
                                <th>Inward Document</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignee_registry as $registry)
                            <tr>
                                <td>{{$registry->inward_outward_no}}</td>
                                <td>{{$registry->inward_outward_title}}</td>
                                <td>{{$registry->description}}</td>
                                <td>{{$registry->doc_delivery_mode}}</td>
                                <td>
                                    <a title="Download " href="{{ asset('storage/'.str_replace('public/','',!empty($registry->document_file) ? $registry->document_file : 'public/no_image')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a>
                                </td>
                                <td>
                                <button type="button" title="Accept" onclick="accept_registry(this);" data-href="{{ route('admin.accept_registry', $registry->id ) }}" class="btn btn-danger btn-rounded"> <i class="fa fa-check"></i></button>
                                <button type="button" data-target="#rejectModal" data-toggle="modal" onclick="rejectRegistry({{$registry->id}});" class="btn btn-warning btn-rounded"><i class="fa fa-times"></i> </button>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.reject_registry') }}" method="POST" id="reject_note_frm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body" id="userTable">
                                <div class="form-group ">
                                    <label>Reject Note</label>
                                    <input type="hidden" name="registry_id" id="registry_id" value="" />
                                    <textarea class="form-control valid" rows="6" required name="reject_note" id="reject_note" spellcheck="false"></textarea>

                                </div>
                            </div>
                            <div class="col-md-12 pull-left">
                                <div class="clearfix"></div>
                                <br>
                                <button type="submit" class="btn btn-danger">Reject</button>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
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
        $(document).ready(function () {
            $('#doc_table').DataTable({
                    stateSave:true
                });
        });

        function accept_registry(e) {
                swal({
                    title: "Are you sure you want to accept this Registry Document?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }

            function rejectRegistry(id) {
                $('#registry_id').val(id);
            }

        </script>
        @endsection
