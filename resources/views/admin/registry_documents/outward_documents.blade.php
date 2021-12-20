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
                    <table id="outward_docs_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="150px">Title</th>
                                <th>Registry No</th>
                                <th width="200px">Description</th>
                                <th>Company</th>
                                <th>Project</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Document</th>
                                <th>Received Date</th>
                                <th id="expected_date">Expected Ans Date</th>
                                <th>Created Date</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outwards_docs as $list_data)
                            <tr>

                                <td>{{$list_data->inward_outward_title}}</td>
                                <td>{{$list_data->inward_outward_no}}</td>
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
                                <td> <a title="View Details" href="{{ route('admin.view_outward_to_inward',['id'=>$list_data->parent_inward_outward_no]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-eye"></i></td>

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
            $('#outward_docs_table').DataTable({
                stateSave: true
            });
        </script>
        @endsection