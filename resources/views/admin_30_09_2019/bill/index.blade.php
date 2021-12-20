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
                
                <a href="{{ route('admin.add_bill') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add bill</a>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="bill_table" class="table table-striped">
                        <thead>
                            <tr>
                                
                                <th >Bill date</th>
                                <th>Vendor name</th>
                                <th>Request by</th>
                                <th>Verify by</th>
                                <th>Bank name</th>
                                <th>Company name</th>
                                <th>Mode of payment  </th>
                                <th>Head name</th>
                                <th>Account number</th>
                                <th>Deduction details</th>
                                <th>Pending amount</th>
                                <th>Amount released</th>
                                <th>Notes</th>
                                <th>Budget sheet no</th>
                                <th>Status</th>
                                <th>Created date</th>
                                <th>Action</th>
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
                var table = $('#bill_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_bill_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        
                        {"taregts": 0, "render":function(data,type,row){
                                return moment(row.bill_date).format("DD-MM-YYYY");
                            }
                        },
                        {"taregts": 1, 'data': 'vendor_name'
                        },

                        {"taregts": 2, 'data': 'request_by'
                        },
                        {"taregts": 3, 'data': 'verify_by'
                        },
                        {"taregts": 4, 'data': 'bank_name'
                        },
                        {"taregts": 5, 'data': 'company_name'
                        },
                        {"taregts": 6, 'data': 'mode_of_payment'
                        },
                        {"taregts": 7, 'data': 'head_name'
                        },
                        {"taregts": 8, 'data': 'account_number'
                        },
                        {"taregts": 9, 'data': 'deduction_details'
                        },
                        {"taregts": 10, 'data': 'pending_amount'
                        },
                        {"taregts": 11, 'data': 'amount_released'
                        },

                        {"taregts": 12, 'data': 'notes'
                        },
                        {"taregts": 13, 'data': 'budget_sheet_no'
                        }, 
                        {"taregts": 14, "render":function(data,type,row){
                                if(row.status=="Pending"){
                                    return '<b class="text-warning">Pending</b>';
                                }
                                else if(row.status=="Approved"){
                                    return '<b class="text-success">Approved</b>'
                                }
                                else{
                                    return '<b class="text-danger">Rejected</b>'
                                }
                            }
                        }, 
                        {
                            "taregts": 15, "render":function(data,type,row){
                                return moment(row.created_at).format("DD-MM-YYYY");
                            }
                        },
                        {"taregts": 16, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 
                                out = '<a href="<?php echo url('admin/edit_bill') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>'; 
                                return out;
                            }
                        },
                    ]

                });
            })
        </script>
        @endsection