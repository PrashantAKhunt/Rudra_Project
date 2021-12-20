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

                <a href="{{ route('admin.add_bank_reconciliation') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Bank Reconciliation</a>
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="bank_reconciliation_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Company Name</th>
                                <th>Bank Name</th>
                                <th>Transaction Date</th>
                                <th>Value Date</th>
                                <th>Description</th>
                                <th>Reff / Cheque No</th>
                                <th>Branch Code</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
            <!--row -->

        </div>

        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                var table = $('#bank_reconciliation_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    // "order": [[1, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_bank_reconciliation_list'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {"taregts": 0, 'data': 'company_name'
                        },
                        {"taregts": 1, 'data': 'bank_name'
                        },
                        {"taregts": 2, 'data': 'txn_date'
                        },
                        {"taregts": 3, 'data': 'value_date'
                        },
                        {"taregts": 4, 'data': 'description'
                        },
                        {"taregts": 4, 'data': 'reff_cheque_no'
                        },
                        {"taregts": 4, 'data': 'branch_code'
                        },
                        {"taregts": 4, 'data': 'debit'
                        },
                        {"taregts": 4, 'data': 'credit'
                        },
                        {"taregts": 4, 'data': 'balance'
                        },
                    ],
                    "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                        if (aData.match_payment == 0) {
                            $('td', nRow).addClass('danger');
                        }
                    }

                });
            })
        </script>
        @endsection