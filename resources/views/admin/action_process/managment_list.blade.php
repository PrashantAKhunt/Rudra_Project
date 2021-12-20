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
                <h4>Rejected Task Distribution Documents by Main/Prime Employee</h4>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="process_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Inward Registry No</th>
                                <th>Inward Title</th>
                                <th>Action Plan Detail</th>
                                <th>Support Required from Department</th>
                                <th width="300px">Support Required from Employee <br>
                                    <h5> Name -> Percentage -> Hour</h5></th>
                                <th>Inward Document</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($prime_list->count() > 0)
                            @foreach($prime_list as $list_data)
                            <tr>

                               <td>{{ $list_data->inward_outward_no }}</td>
                               <td>{{ $list_data->inward_outward_title }}</td>
                               <td>{{ $list_data->work_details }}</td>
                               <td>

                               @if( count($list_data->emp_distrubution) > 0 )
                                  {{ $list_data->emp_distrubution[0]['depart_name'] }}
                               @endif
                               </td>
                               <td>
                               @if( count($list_data->emp_distrubution) > 0 )
                               @foreach($list_data->emp_distrubution as $key => $list)

                                  <h5> {{ $list->name }} -> {{ $list->task_percentage }}% -> {{ $list->task_hour }}H</h5>

                               @endforeach
                               @endif
                               </td>
                               <td>
                                    <a title="Download" href="{{ asset('storage/'.str_replace('public/','',!empty($list_data->document_file) ? $list_data->document_file : '')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a>
                                </td>
                                <td>
                                  @if( $list_data->final_status == "Rejected")
                                    <a href="{{ route('admin.distrubuted_llist',['id'=>$list_data->id]) }}" title="Edit"  class="btn btn-primary btn-rounded"> <i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
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
        //==============================================
        </script>
        @endsection
