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
                <h4 class="page-title">Issued Cheques Report</h4>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                        <table id="cheque_issued_table" class="table table-striped">
                            <thead>
                                <tr>
                                
                                <th>Bank</th>
                                <th>A/C Number</th>
                                <th>Cheque Ref Number</th>                                
                                <th>From->To</th>
                                <th>Total Cheques</th>
                                <th>Balanced Cheques</th>
                                <th>Cheque Number</th>
                               
                                </tr>
                            </thead>
                            <tbody>
                            @if( count($issedData) > 0)
                        @foreach($issedData as $value)
                            <tr>
                                <td>{{ $value->bank_name }}</td>
                                <td>{{ $value->ac_number }}</td>
                                <td>{{ $value->check_ref_no }}</td>
                                <td>{{ $value->from }} to {{ $value->to }}</td>      
                                <td>{{ $value->total_cheque }}</td>
                                <td>{{ $value->balanced_cheque }}</td>
                                <td>{{ $value->ch_no }}</td>
                               
                               
                            </tr>
                            @endforeach 
                            @endif 
                            </tbody>
                        </table>
                        
                </div>
            </div>
            <!--row -->

        </div>
        <div class="col-md-12 col-lg-12 col-sm-12">

            <div class="white-box">                
                <!--<div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Failed Cheques Report</h4>
                    </div>
                    </div> -->
                <h4 class="page-title">Failed Cheques Report</h4>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="cheque_failed_table" class="table table-striped">
                        <thead>
                            <tr>
                           
                                <th>Bank</th>
                                <th>A/C Number</th>
                                <th>Cheque Ref No</th>
                                <th>Cheque No</th>
                                <th>Failed Reason</th>
                                <th>Document</th>
                              
                            </tr>
                        </thead>
                        <tbody>
                        @if( count($failedData) > 0)
                        @foreach($failedData as $value)
                            <tr>
                                <td>{{ $value->bank_name }}</td>
                                <td>{{ $value->ac_number }}</td>
                                <td>{{ $value->check_ref_no }}</td>
                                <td>{{ $value->ch_no }}</td>      
                                <td>{{ $value->failed_reason }}</td>
                                <td>
                                  @if($value->failed_document)
                                 <a href="{{ asset('storage/'.str_replace('public/','',!empty($value->failed_document) ? $value->failed_document : 'public/no_image')) }}" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>
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


$(document).ready(function () {
            
    $('#cheque_issued_table').DataTable({
		dom: 'Bfrtip',buttons: [
             'csv', 'excel', 'print'
        ],
        stateSave: true
    });

    $('#cheque_failed_table').DataTable({
		dom: 'Bfrtip',buttons: [
             'csv', 'excel', 'print'
        ],
        stateSave: true
    });
            
            
});
                 
</script>
@endsection