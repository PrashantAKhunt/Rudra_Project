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

            <div class="row">
                <div class="col-md-4">
                    <div class="white-box">
                        <h3>July, 2019 Salary Slip</h3>
                        <a href="{{ asset('salary_slip_new/BasicSalarySlip.pdf') }}" target="_blank" class="btn btn-primary btn-rounded"><i class="fa fa-download"></i> Download</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="white-box">
                        <h3>August, 2019 Salary Slip</h3>
                        <a href="{{ asset('salary_slip_new/BasicSalarySlip.pdf') }}" target="_blank" class="btn btn-primary btn-rounded"><i class="fa fa-download"></i> Download</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="white-box">
                        <h3>September, 2019 Salary Slip</h3>
                        <a href="{{ asset('salary_slip_new/BasicSalarySlip.pdf') }}" target="_blank" class="btn btn-primary btn-rounded"><i class="fa fa-download"></i> Download</a>
                    </div>
                </div>
            </div>


            <!--row -->

        </div>

        @endsection
        @section('script')

        @endsection