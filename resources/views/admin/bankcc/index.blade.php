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

                <a href="{{ route('admin.add_bank_cc') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Bank CC</a>
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="bankcc_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Company name</th>
                                <th>Bank name</th>
                                <th>Bank A/C Number</th>
                                <th>Amount</th>
                                <th>Bank Charges</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Action</th>
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
                var table = $('#bankcc_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_bankcc_list'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {"taregts": 0, 'data': 'company_name'
                        },
                        {"taregts": 1, 'data': 'bank_name'
                        },
                        {"taregts": 2, 'data': 'ac_number'
                        },
                        {"taregts": 3, 'data': 'amount'
                        },
                        {"taregts": 4, 'data': 'charges'
                        },
                        {"taregts": 5, "render": function (data, type, row) {
                                return moment(row.start_date).format("DD-MM-YYYY");
                            }
                        },
                        {"taregts": 6, "render": function (data, type, row) {
                                return moment(row.end_date).format("DD-MM-YYYY");
                            }
                        },

                        {"taregts": 7, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                out = '<a href="<?php echo url('admin/edit_bankcc') ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                return out;
                            }
                        },
                    ]

                });
            })
        </script>
        @endsection