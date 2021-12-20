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
                 if(in_array(3, $role)){
                ?>
                <a href="{{ route('admin.add_expense_category') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Expense Category</a>
                <?php
                    }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <?php if(in_array(2, $role)) {
                                ?>
                                <th>Status</th>
                                <th>Action</th>
                                <?php }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expense_category_list as $expense_category)
                            <tr>
                                <td>{{$expense_category->category_name }}</td>
                                <?php if(in_array(2, $role)) {
                                ?>
                                @if($expense_category->status=='Enabled')
                                    <td><a href="{{ route('admin.change_expense_category',['id'=>$expense_category->id,'status'=>'Disabled']) }}" class="btn btn-success" title="Change Status">Enabled</a></td>
                                @else
                                    <td><a href="{{ route('admin.change_expense_category',['id'=>$expense_category->id,'status'=>'Enabled']) }}" class="btn btn-danger" title="Change Status">Disabled</a></td>
                                @endif
                                <?php } ?>
                                <td>
                                <?php if(in_array(2, $role)) {
                                ?>
                                <a href="{{ route('admin.edit_expense_category',['id'=>$expense_category->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>
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