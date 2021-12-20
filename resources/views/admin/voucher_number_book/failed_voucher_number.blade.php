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
            <!-- {{ route('admin.add_voucher_number') }} -->
                
                {{-- <a href="#" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Failed Voucher Number</a> --}}
               
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Voucher Book Ref No</th>
                                <th>Voucher Number</th>
                                <th>Company</th>
                                <th>Requester Name</th>
                                <th>Accountant Status</th>
                                <th>SuperAdmin Status</th>
                                <th>Failed Status</th>
                                <th>Request Date</th>
                                <th>Failed Reason</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($failed_voucher_number)
                                @foreach($failed_voucher_number as $key => $value)
                                    <tr>
                                        <td>{{$value['voucher_ref_no']}}</td>
                                        
                                        <td>{{$value['voucher_numbers']}}</td>
                                        <td>{{$value['company_name']}}</td>
                                        <td>{{$value['name']}}</td>
                                        <td>
                                            @if($value['accountant_status'] == "Approved")
                                                <b class="text-success">{{$value['accountant_status']}}</b>
                                            @elseif($value['accountant_status'] == "Rejected")
                                                <b class="text-danger">{{$value['accountant_status']}}</b>
                                            @else
                                                <b class="text-warning">{{$value['accountant_status']}}</b>
                                            @endif
                                        </td>
                                        <td>
                                            @if($value['superadmin_status'] == "Approved")
                                                <b class="text-success">{{$value['superadmin_status']}}</b>
                                            @elseif($value['superadmin_status'] == "Rejected")
                                                <b class="text-danger">{{$value['superadmin_status']}}</b>
                                            @else
                                                <b class="text-warning">{{$value['superadmin_status']}}</b>
                                            @endif
                                        </td>
                                        <td>
                                            @if($value['failed_request_status'] == "Approved")
                                                <b class="text-success">{{$value['failed_request_status']}}</b>
                                            @elseif($value['failed_request_status'] == "Rejected")
                                                <b class="text-danger">{{$value['failed_request_status']}}</b>
                                            @else
                                                <b class="text-warning">{{$value['failed_request_status']}}</b>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($value['failed_request_date'])
                                            {{date('d/m/Y',strtotime($value['failed_request_date']))}}
                                            @endif
                                        </td>
                                        <td>
                                        <button class="btn btn-info btn-circle" id="{{$value['id']}}" onclick="show_fail_reason(this.id)"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                            <span id="copy_{{$value['id']}}" style="display:none">{{$value['failed_reason']}}</span> 
                                        </td>
                                        <td>
                                        @php
                                            $path = $value['failed_document'];
                                            $baseURL = str_replace("public/","",$path);
                                            $url =  URL::to('/')."/storage/".$baseURL;
                                        @endphp
                                        <a href="{{$url}}" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>
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
        <div class="modal fade" id="failed_reason" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Voucher number failed reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   <p id="dynamic_reason"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
            </div>
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                var table = $('#company_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ],
                });

            })
            function show_fail_reason(id){
                var reason_text = $("#copy_"+id).text();
                // alert(reason_text);
                $("#dynamic_reason").html(reason_text);
                $("#failed_reason").modal('show');

            }
        </script>
        @endsection