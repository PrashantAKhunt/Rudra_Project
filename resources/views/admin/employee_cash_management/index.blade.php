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

                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="employee_cash_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee </th>
                                <th>Balance</th>
                                <th>Transfer datetime</th>
                                <th>Status</th>
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
            $(document).ready(function() {

                var table = $('#employee_cash_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [
                        [1, "DESC"]
                    ],
                    "ajax": {
                        url: "<?php echo route('admin.get_employee_cash_list'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {
                            "taregts": 0,
                            'data': 'name'
                        },
                        {
                            "taregts": 1,
                            "render": function(data, type, row) {
                                if (row.balance) {
                                    return Number(parseFloat(row.balance).toFixed(2)).toLocaleString('en', {
                                        minimumFractionDigits: 2
                                    });
                                }

                            }
                        },
                        {
                            "taregts": 2,
                            "render": function(data, type, row) {
                                return moment(row.created_at).format("DD-MM-YYYY hh::mm A");
                            },
                        },
                        // status = bydefault pending may be or later on then confirmed
                        {
                            "taregts": 3,
                            "render": function(data, type, row) {
                                let app_date = '';
                                
                                if(row.receive_datetime){
                                    app_date = moment(row.receive_datetime).format("DD-MM-YYYY hh:ss A");
                                }
                                if(!row.receive_status){
                                    return '<span class="text-warning">Pending</span>';
                                }else(row.receive_status == "Confirmed")
                                {
                                    return '<span class="text-success">Confirmed <br>' + app_date + '</span>';
                                    // return moment(row.updated_at).format("DD-MM-YYYY hh::mm A");
                                }

                            }
                        },
                        //  button to confirmthe pending and show the timestamp of confirmation recipient
                        {
                            "taregts": 4,
                            "searchable": false, "orderable": false,
                            "render": function(data, type, row) {
                                let action_btn= '';
                                if(!row.receive_status  && row.employee_id == "{{Auth::user()->id}}"){
                                    action_btn += '<a href="#" onclick="approve_confirm('+row.id+');" class="btn btn-success btn-rounded"><i class="fa fa-check" aria-hidden="true"></i></a>';         
                                }
                                return action_btn;
                            },
                        }

                    ]

                });

            })
        // 
        // 
    // 



    function approve_confirm(id) {
                        swal({
                        title: "Are you sure you want to confirm ?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes",
                                closeOnConfirm: false
                        }, function () {
                            window.location.href = "{{url('confirm_employee_cash')}}/"+id;
                        });
                    }
        </script>
        @endsection