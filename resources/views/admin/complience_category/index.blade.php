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

                @if($compliance_add_permission)
                <a href="{{ route('admin.add_compliance_category') }}" class="btn btn-primary pull-right" ><i class="fa fa-plus"></i> Add Compliance</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="compliance_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="500px">Compliance Category Name</th>
                                <th>Compliance Category Detail</th>
                                <th>Status</th>
                                <th>Cretaed Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $list_data)
                            <tr>
                                <td>{{$list_data->compliance_name}}</td>
                                <td>{{$list_data->compliance_detail}}</td>
                                <td>

                                    @if($list_data->status=='Enabled')
                                    <a href="{{ route('admin.change_compliance_status',['id'=>$list_data->id,'status'=>'Disabled']) }}" class="btn btn-success" title="Click To Disable">Enabled</a>
                                    @else
                                    <a href="{{ route('admin.change_compliance_status',['id'=>$list_data->id,'status'=>'Enabled']) }}" class="btn btn-danger" title="Click To Enable">Disabled</a>
                                    @endif

                                </td>
                                <th>
                                    {{ date('d-m-Y',strtotime( $list_data->created_at )) }}
                                </th>
                                <td>
                                    @if($compliance_edit_permission)
                                    <a href="{{ route('admin.edit_compliance_category',['id'=> $list_data->id]) }}" title="Edit"  class="btn btn-primary btn-rounded"> <i class="glyphicon glyphicon-edit"></i></a>
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

        @endsection

        @section('script')
        <script>
            $('#compliance_table').DataTable({
                    stateSave:true,
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                });
        </script>
        @endsection
