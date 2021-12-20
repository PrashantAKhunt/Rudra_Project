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
            <a href="{{ route('admin.add_cancel_cheque') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Stop Payment</a>
            @endif
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Cheque Book Ref No</th>
                                <th>Company</th>
                                <th>Bank</th>
                                <th>Cheque No</th>
                                <th>Stop payment Image</th>
                                <th>Letterhead Image</th>
                                <th>Outward No</th>
                        
                            </tr>
                        </thead>
                        <tbody>
                            
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
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_cancel_cheque_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 1, 'data': 'check_ref_no'
                        },
                        {"taregts": 2, 'data': 'company_name'
                        },
                        {"taregts": 3, "render": function (data, type, row) {
                                return row.bank_name + '(' + row.ac_number + ')';
                            }
                        },
                        {"taregts": 4, 'data': 'ch_no'
                        },
                        {"taregts": 5,"searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                var path = row.cancel_cheque_img;
                                if (path) {
                                    var baseURL = path.replace("public/","");
                                    var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                                    out += '<a href="'+ url +'" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>';
                                }
                                
                                return out;
                            }
                        },
                        {"taregts": 6,"searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                var path = row.cancel_letterhead_img;
                                if (path) {
                                    var baseURL = path.replace("public/","");
                                    var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                                    out += '<a href="'+ url +'" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>';
                                }
                                
                                return out;
                            }
                        },
                        {"taregts": 7, 'data': 'outward_no'
                        }

                    ]

                });

            })

           
        </script>
        @endsection