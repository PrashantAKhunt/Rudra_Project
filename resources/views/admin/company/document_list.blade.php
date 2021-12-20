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
            <div class="col-md-12">
                <a class="btn btn-info pull-right" href="{{ route('admin.companies') }}"><i class="fa fa-arrow-left"></i>Back</a>
            </div>
            <div class="col-md-12">
            <div class="white-box">

                <a href="{{ route('admin.add_company_document',['id'=>$id]) }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Document</a>
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Document</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($company_data[0])) {
                                foreach ($company_data as $key => $company_data_value) {
                                    ?>
                                    <tr>
                                        <td>{{$company_data_value->title}}</td>
                                        <td>
                                            <a class="btn btn-rounded btn-primary" target="_blank" href="<?php echo asset('storage/' . str_replace('public/', '', $company_data_value->doc_link)) ?>"><i class="fa fa-eye"></i> </a>       
                                        </td>
                                        <td>
                                            <a onclick="delete_confirm(this);" data-href="<?php echo url('delete_document/' . $company_data_value->id . '/' . $company_data_value->company_id); ?>" class="btn btn-primary btn-rounded"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<td colspan='5'>No record found !</td>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->
</div>
        </div>
    </div>
    @endsection
    @section('script')
    <script>
        $(document).ready(function () {
        })
        function delete_confirm(e) {
            swal({
                title: "Are you sure you want to delete company document?",
                //text: "You want to change status of admin user.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }, function () {
                window.location.href = $(e).attr('data-href');
            });
        }
    </script>
    @endsection