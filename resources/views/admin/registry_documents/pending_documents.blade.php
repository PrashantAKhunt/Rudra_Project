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
                    <table id="documnet_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Registry No</th>
                                <th width="150px">Title</th>
                                <th width="200px">Description</th>
                                <th>Company</th>
                                <th>Project</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Document</th>
                                <th>Received Date</th>
                                <th id="expected_date">Expected Ans Date</th>
                                <th>Created Date</th>
                                <th>Assign Users</th>
                                <th>Type</th>
                                <th width="100px" , data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registry_docs as $list_data)
                            <tr>
                            <td>{{$list_data->inward_outward_no}}</td>
                                <td>{{$list_data->inward_outward_title}}</td>
                                <td>{{$list_data->description}}</td>
                                <td>{{$list_data->company_name}}</td>
                                <td>{{$list_data->project_name}}</td>
                                <td>{{$list_data->category_name}}</td>
                                <td>{{$list_data->sub_category_name}}</td>
                                <td><a title="Download" href="{{ asset('storage/'.str_replace('public/','',!empty($list_data->document_file) ? $list_data->document_file : 'public/no_image')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a></td>
                                <td><span style="display: none;">{{ $list_data->received_date }}</span>
                                    {{ Carbon\Carbon::parse($list_data->received_date)->format('d-m-Y') }}
                                </td>
                                @if($list_data->expected_ans_date!="" || $list_data->expected_ans_date!=NULL)
                                <td>
                                    <span style="display: none;">{{ $list_data->expected_ans_date }}</span>{{ Carbon\Carbon::parse($list_data->expected_ans_date)->format('d-m-Y') }}
                                </td>
                                @else
                                <td>NA</td>
                                @endif

                                <td><span style="display: none;">{{ $list_data->created_at }}</span>
                                    {{ Carbon\Carbon::parse($list_data->created_at)->format('d-m-Y H:i:s') }}
                                </td>
                                <td>
                                <h5>{{$list_data->users_list}}</h5>
                                </td>

                                <td>
                                    @if($list_data->type == 'Inwards')
                                    <b class="text-success">Inward</b>
                                    @else
                                    <b class="text-danger">Outward</b>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" onclick="approve_doc('<?php echo $list_data->id; ?>');" data-toggle="modal" data-target="#ApproveModel" class="btn btn-warning" title="Click To Approved">Pending</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Model  -->
                <div id="ApproveModel" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content" id="model_data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="panel-title">Do you want to approve this as important?</h3>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="{{ route('admin.mark_approve_documnet') }}">
                                    @csrf
                                    <div class="row">

                                        <input type="hidden" name="registry_id" id="registry_id" value="">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">Approved:</label>
                                                <div class="col-md-9">
                                                    <div class="radio-list">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="doc_mark" value="Approved">
                                                            Yes</label>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="doc_mark" value="None" checked="">
                                                            No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-xs-2">

                                            <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>

                                        </div>
                                    </div>

                                </form>
                            </div>


                        </div>
                    </div>
                </div>
                <!-- End Model here -->
            </div>
            <!--row -->

        </div>

        @endsection

        @section('script')
        <script>
            $('#documnet_table').DataTable({
                stateSave: true
            });

            function approve_doc(id) {
                $('#registry_id').val(id);
            }
        </script>


        @endsection