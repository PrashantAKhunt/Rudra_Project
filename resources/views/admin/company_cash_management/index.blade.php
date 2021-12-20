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
                <h3 class="pull-left">Total Cash: <span class="text-success" id="total_amount">0.00</span></h3>
                @if($company_cash_add_permission)
                <a href="{{ route('admin.add_company_cash') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Company Cash</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                
                    <table id="company_cash_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company </th>
                                <th>Balance</th>
                                <th>Updated Date</th>
                                <th>Add Balance</th>
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
                var company_cash_add_permission = <?php echo json_encode($company_cash_add_permission); ?>;
                var table = $('#company_cash_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_company_cash_list'); ?>",
                        type: "GET",
                        "data": function () {
                            total_cash_amount = 0;
                        }
                    },
                    "columns": [

                        {"taregts": 0, 'data': 'company_name'},
                        {"taregts": 1,
                            "render": function (data, type, row) {
                                if (row.balance) {
                                    total_cash_amount += Number(row.balance);
                                    return  Number(parseFloat(row.balance).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                                }

                            }
                        },
                        {"taregts": 2, "render":function(data,type,row){
                            return moment(row.updated_at).format("DD-MM-YYYY hh::mm A");
                           },
                        },
                        {"taregts": 3, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out="";
                                if (company_cash_add_permission) {
                                    out = '<a href="<?php echo url('edit_company_cash') ?>'+'/'+id+'" title="Add Balance" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                }
                                return out;
                            }
                        },
                    ],
                    "drawCallback": function( data ) {
                        let total_cash = data.json.total_balance > 0.00 ? data.json.total_balance : 0.00
                        $("#total_amount").text("");
                        $("#total_amount").text(total_cash);
                    }

                });

            });



        </script>
        @endsection
