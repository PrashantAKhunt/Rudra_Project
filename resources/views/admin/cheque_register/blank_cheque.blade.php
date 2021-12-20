@extends('layouts.admin_app')

@section('content')
<?php

use Illuminate\Support\Facades\Config; ?>
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

            @if( config::get('constants.ACCOUNT_ROLE') == Auth::user()->role) 
             <a href="{{ route('admin.add_cheque_register') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Cheque</a>
            @endif    
                <p class="text-muted m-b-30"></p>
                <br>
        
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Cheque Book Ref No</th>
                                <th>Cheque No</th>
                                <th>Company</th>
                                <th>Bank(Account No)</th>
                                
                            <!-- <th>Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @if($blank_list)
                                @foreach($blank_list as $key => $value)
                                <tr>
                                    <td>{{$value[0]['check_ref_no']}}</td>
                                    @php
                                        $last_no = end($value);
                                    @endphp
                                    <td>{{$value[0]['ch_no']}} - {{$last_no['ch_no']}}</td>
                                    <td>{{$value[0]['company_name']}}</td>
                                    <td>{{$value[0]['bank_name']}}({{$value[0]['ac_number']}})</td>
                                    
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
            
            //=============================Ajax Table
                var table = $('#company_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    // "processing": true,
                    // "serverSide": true,
                    // "responsive": true,
                    // "order": [[1, "DESC"]],
                    stateSave: true
                
                 });

            })
        </script>
        @endsection