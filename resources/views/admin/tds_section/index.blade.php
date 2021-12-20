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
                <a href="{{ route('admin.add_tds_section') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add {{ $page_title }}</a>
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="tds_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>TDS Section Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($tds_section))
                                @foreach ($tds_section as $key => $value)
                                <tr>
                                    <td>{{$value['section_type']}}</td>
                                    <td>
                                        @if ($value['status'] == "Enabled")
                                            <a href="<?php echo url('change_tds_section_status') ?>/{{$value['id']}}/Disabled" class="btn btn-success" title="Change Status">{{$value['status']}}</a>
                                        @else
                                            <a href="<?php echo url('change_tds_section_status') ?>/{{$value['id']}}/Enabled" class="btn btn-danger" title="Change Status">{{$value['status']}}</a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="<?php echo url('edit_tds_section') ?>/{{$value['id']}}" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>
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
            $(document).ready(function () {
                var table = $('#tds_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                });
            })
        </script>
        @endsection
