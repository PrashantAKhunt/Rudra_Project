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
                <a href="{{ route('admin.add_employee_expense') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Expense</a>
                <?php
                    }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Expense Category</th>
                                <th>Title</th>
                                <th>Merchant Name</th>
                                <th>Amount</th>
                                <th>Expense Date</th>
                                <?php if(in_array(2, $role)) {
                                ?>
                                
                                <th>Status</th>
                                <th>Action</th>
                                <?php }else{
                                ?>
                                <th>Status</th>
                                <?php
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($employee_expense_list)>0)
                            @foreach($employee_expense_list as $expense_category)
                            <tr>
                                <td>{{$expense_category->name }}</td>
                                <td>{{$expense_category->category_name }}</td>
                                <td>{{$expense_category->title }}</td>
                                <td>{{$expense_category->merchant_name }}</td>
                                <td>Rs{{$expense_category->amount }}</td>
                                <td>{{$expense_category->expense_date }}</td>
                                <!-- <td>{{$expense_category->status }}</td> -->
                                <?php if(in_array(2, $role)) {
                                ?>
                                @if($expense_category->status=='Pending')
                                    <td>
                                        <a href="{{ route('admin.change_employee_expense',['id'=>$expense_category->id,'status'=>'Approved']) }}" class="btn btn-success" title="Change Status">Approved</a>
                                        <a href="{{ route('admin.change_employee_expense',['id'=>$expense_category->id,'status'=>'Rejected']) }}" class="btn btn-danger" title="Change Status">Reject</a>
                                    </td>
                                @else
                                <td>
                                    <?php 
                                    if($expense_category->status=="Approved")
                                    {
                                        echo '<b class="text-success">Approved</b>';
                                    }
                                    else
                                    {
                                        echo  '<b class="text-danger">Rejected</b>';
                                    }
                                    ?>
                                </td>
                                @endif
                                <?php }else{?>
                                    <td>
                                    <?php
                                    if($expense_category->status=="Approved")
                                    {
                                        echo '<b class="text-success">Approved</b>';
                                    }
                                    elseif($expense_category->status=="Rejected")
                                    {
                                        echo  '<b class="text-danger">Rejected</b>';
                                    }
                                    else
                                    {
                                        echo  '<b class="text-danger">Pending</b>';
                                    }
                                    ?>
                                </td>
                                <?php
                                    }
                                ?>
                                <td>
                                <?php if(in_array(2, $role)) {
                                ?>
                                <a href="{{ route('admin.edit_employee_expense',['id'=>$expense_category->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>
                                <?php 
                                }
                                ?>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="12" align="center">
                                    No Records Found !
                                </td>
                            </tr>
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
            $(document).ready(function () {
               
            })
        </script>
        @endsection