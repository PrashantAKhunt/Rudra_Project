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
            <!-- {{ route('admin.add_rtgs_register') }} -->
                @if($add_permission)
                    <a href="{{ route('admin.add_voucher_number') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Voucher Book</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Voucher Book Ref No</th>
                                <th>Voucher Number</th>
                                <th>Company</th>
                                {{-- <th>Client</th>
                                <th>Project</th>
                                <th>Project Site</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if($blank_list)
                                @foreach($blank_list as $key => $value)
                                <tr>
                                    <td>{{$value[0]['voucher_ref_no']}}</td>
                                    @php
                                        $last_no = end($value);
                                    @endphp
                                    <td>{{$value[0]['voucher_no']}} - {{$last_no['voucher_no']}}</td>
                                    <td>{{$value[0]['company_name']}}</td>
                                    {{-- <td>{{$value[0]['client_name']}}</td>
                                    <td>{{$value[0]['project_name']}}</td>
                                    <td>{{$value[0]['site_name']}}</td> --}}
                                    
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
                $('#company_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ],
                });
                var table = $('#company_table_last').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    // "order": [[1, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_blank_voucher_number_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'voucher_ref_no'
                        },
                        {"taregts": 1, 'data': 'voucher_no'
                        },
                        {"taregts": 2, 'data': 'company_name'
                        },
                    ]

                });

            })
        </script>
        @endsection