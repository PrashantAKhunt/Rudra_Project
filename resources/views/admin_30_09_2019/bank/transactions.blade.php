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
                
                <a href="{{ route('admin.import_csv') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Import Bank Transaction</a>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="bank_table" class="table table-striped">
                        <thead>
                            <tr>
                                
                                <th>Bank name</th>
                                <th>Company name</th>
                                <th>Transaction ID</th>
                                <th>Transaction Date</th>
                                <th>Particular</th>
                                <th>Internal</th>
                                <th>Voucher Type </th>
                                <th>Project</th>
                                <th>Head ID</th>
                                <th>Sub head</th>
                                <th>Received</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Narration</th>
                                <th>Remark</th> 
                                <th>Status</th>
                                <th>Created date</th>
                                <!--<th>Action</th>-->
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
                var table = $('#bank_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_transaction_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        
                        {"taregts": 0, 'data': 'bank_name'
                        },
                        {"taregts": 1, 'data': 'company_name'
                        },

                        {"taregts": 2, 'data': 'tx_id'
                        },
                        {
                            "taregts": 3, "render":function(data,type,row){
                                return moment(row.tx_date).format("DD-MM-YYYY");
                            }
                        },
                        {"taregts": 4, 'data': 'particular'
                        },
                        {"taregts": 5, 'data': 'cheque_num'
                        },
                        {"taregts": 6, 'data': 'internal'
                        },
                        {"taregts": 7, 'data': 'voucher_type'
                        },
                        {"taregts": 8, 'data': 'project'
                        },
                        {"taregts": 9, 'data': 'head_id'
                        },
                        {"taregts": 10, 'data': 'sub_head'
                        },
                        {"taregts": 11, 'data': 'received'
                        },

                        {"taregts": 12, 'data': 'paid'
                        },
                        {"taregts": 13, 'data': 'balance'
                        },
                        {"taregts": 14, 'data': 'narration'
                        },
                        {"taregts": 15, 'data': 'remark'
                        },
                        {
                            "taregts": 16, "render":function(data,type,row){
                                return moment(row.created_at).format("DD-MM-YYYY");
                            }
                        }
                    ]

                });
            })
        </script>
        @endsection