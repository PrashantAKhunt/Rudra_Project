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
                
                <a href="{{ route('admin.add_consultant') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New Consultant</a>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Specialty</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consultant_list as $consultant)
                            <tr>
                                <td>{{ $consultant->name }}</td>
                                <td>{{ $consultant->email }}</td>
                                <td>{{ $consultant->address }}</td>
                                <td>{{ $consultant->specialty }}</td>
                                <td>
                                    @if($consultant->status=='Enable')
                                    <a href="{{ route('admin.recruitment_change_status',['id'=>$consultant->id,'status'=>'Disable']) }}" class="btn btn-success" title="Click To Disable">Enabled</a>
                                    @else
                                    <a href="{{ route('admin.recruitment_change_status',['id'=>$consultant->id,'status'=>'Enable']) }}" class="btn btn-danger" title="Click To Enable">Disabled</a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.edit_consultant',['id'=>$consultant->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>
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
            $(document).ready(function () {
               
            })
        </script>
        @endsection