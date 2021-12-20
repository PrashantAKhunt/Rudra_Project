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
                $role = explode(',', $access_rule);
                if(in_array(3, $role)) {
                ?>
                <a href="{{ route('admin.add_department') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Department</a>
                <?php
                }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Department Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department_list as $department)
                            <tr>
                                <td>{{ $department->dept_name }}</td>
                                <td>
                                <?php if(in_array(2, $role)) {
                                ?>
                                <a href="{{ route('admin.edit_department',['id'=>$department->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>
                                <?php
                                  }
                                ?>
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