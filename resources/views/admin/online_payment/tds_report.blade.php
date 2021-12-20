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
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                    <form action="#" id="used_cheque" method="get">
                        @csrf
                        <div class="row">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="inputEmail4">Company</label>
                                    <select class="form-control" name="company_id" id="company_id">
                                        <option value="">Select Company</option>
                                        @if($companies)
                                            @foreach($companies as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="inputEmail4">Date</label>
                                    <input class="form-control input-daterange-datepicker" id="date_range" type="text" name="daterange" value="">
                                </div>
                                <div class="form-group col-md-3" style="margin-top: 30px;">
                                    <button class="btn btn-success" type="button" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
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
                <strong>Total TDS: </strong> <strong id="total_amount">1234</strong>
                <a href="{{ url()->previous() }}" class="btn btn-danger pull-right"> Back</a>
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>TDS Section Type</th>
                                <th>TDS Amount</th>
                                <th>Payment Option</th>
                                <th>Budget Sheet Number</th>
                                <th>User Name</th>
                                <th>Client Name</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Project Site Name</th>
                                <th>Vendor Name</th>
                                <th>Work Detail</th>
                                <th>Transaction UNR Number</th>
                                <th>Transation Type</th>
                                <th>Payment Card</th>
                                <th>Vendor/Party Bank Details</th>
                                <th>Bank Name</th>
                                <th>Amount</th>
                                <th>IGST Amount</th>
                                <th>CGST Amount</th>
                                <th>SGST Amount</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="reject_note_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Reject Reason</h4>
                    </div>
                    <div class="modal-body" id="reject_note_div">

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div id="work_note_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Work Detail</h4>
                    </div>

                     <div class="modal-body" id="work_note_div">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div id="onlinePaymentFilesModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Files</h4>
                    </div>

                    <br>
                    <br>

                    <table  class="table table-striped table-bordered" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Download</th>
                                <th>Filename</th>
                                <th id="del_action">Action</th>

                            </tr>
                        </thead>
                        <tbody id="file_table">

                        </tbody>
                    </table>

                    <!-- <div class="modal-body" id="files">
                    </div> -->

                </div>
                <!-- /.modal-content -->
            </div>
        </div>
        @endsection

        @section('script')
        <script>
        function get_online_payment_files(id,payment_status) {
                var online_payment_status = payment_status;
                $.ajax({
                    url: "{{ route('admin.get_online_payment_files') }}",
                    type: "post",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id,
                        payment_status:online_payment_status
                    },
                    success: function(data) {
                        var trHTML = '';
                        if (data.status) {

                            let online_payment_files_arr = data.data.online_payment_files;
                            let payment_status = data.data.payment_status;
                            if (online_payment_files_arr.length == 0) {

                                $('#file_table').empty();
                                trHTML += '<span>No Records Found !</span>';
                                $('#file_table').append(trHTML);

                            } else {

                                $('#file_table').empty();
                                $.each(online_payment_files_arr, function(index, files_obj) {
                                    no = index + 1;
                                    trHTML += '<tr id='+'del_'+no+'>' +
                                        '<td>' + no + '</td>' +
                                        '<td><a title="Download File" download href="' + files_obj.online_payment_file + '"><i class="fa fa-cloud-download fa-lg"></i></a></td>' +
                                        '<td>' + files_obj.file_name + '</td>' +
                                        '<td><a href="#" onclick="delete_file('+no+',' + files_obj.id + ', '+ files_obj.online_payment_id +');" id="deleteFile" class="btn btn-danger btn-rounded delete_files"><i class="fa fa-trash"></i></a></td>' +
                                        '</tr>';
                                });

                                $('#file_table').append(trHTML);
                                if(payment_status=='Approved') {
                                    $('.delete_files').hide();
                                    //$('#del_action').remove();
                                }

                            }

                        } else {

                            $('#file_table').empty();
                            trHTML += '<span>No Records Found !</span>';
                            $('#file_table').append(trHTML);
                        }

                    }
                });
        }

        function delete_file(del_id,id, i_id) {
                swal({

                    title: "Are you sure ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: true
                }, function() {

                    $('#del_'+del_id).remove();

                    $.ajax({
                        url: "{{ route('admin.delete_online_file') }}",
                        type: "post",
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id
                        },
                        success: function(data) {
                            if (data.status) {
                                // location.reload(true);
                                // $('#showFiles').click();
                                get_budget_sheet_files(i_id);
                                console.log("success");

                            }

                        }
                    });
                    //e.preventDefault();
                });


            }
        </script>

        <script>

            function show_reject_note(id) {
                $('#reject_note_div').html($('#reject_note_' + id).val());

            }
            function show_work_note(id) {
                $('#work_note_div').html($('#work_note_' + id).val());

            }

            $('.input-daterange-datepicker').daterangepicker({
                    // autoUpdateInput: false,
                    buttonClasses: ['btn', 'btn-sm'],
                    applyClass: 'btn-danger',
                    cancelClass: 'btn-inverse'
            });

            $(document).ready(function () {
                $("#date_range").val("");
                var total_sum_amount = 0;
                var table = $('#policy_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    //"stateSave": true,
                    "lengthMenu": [[10, 25, 50, 100,500,1000,2000], [10, 25, 50, 100,500,1000,2000]],
                    // "order": [[ 20, "desc" ]],
                    "ajax": {
                        url: "<?php echo route('admin.get_online_payment_tds_report'); ?>",
                        type: "GET",
                        "data": function ( d ) {
                            var company_id = $('#company_id').val();
                            var date_range = $("#date_range").val();

                            d.company_id = company_id;
                            d.date_range = date_range;
                            total_sum_amount = 0;
                        }
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "data": "company_name"},
                        {"taregts": 1, "searchable": true, "data": "section_type"},
                        {"targets": 2, "searchable": true,"render": function (data, type, row) {
                                if (row.tds_amount) {
                                    total_sum_amount += Number(row.tds_amount);
                                    return  row.tds_amount;
                                }else{
                                    return "0.00";
                                }

                            }
                        },
                        {"targets": 3, "searchable": true, "data": "payment_options"},
                        {"targets": 4, "searchable": true, "render": function (data, type, row) {
                            if (row.budhet_sheet_no) {
                                return row.budhet_sheet_no;
                            } else {
                                return "N/A";
                            }
                        }},
                        {"taregts": 5, "searchable": true, "data": "user_name"},

                        {"targets": 6, "searchable": true, "render" : function(data, type, row){
                            if (row.client_name) {
                                if (row.client_name == 'Other Client') {
                                    return row.client_name ;
                                }else{
                                    return row.client_name + " (" + row.location + ")";
                                }

                                } else {
                                    return "N/A";
                                }
                                }
                        },
                        {"taregts": 7, "searchable": true, "data": "project_name"},
                        {"taregts": 8, "searchable": true, "data": "other_project_detail"},
                        {"targets": 9, "searchable": true, "data" : "site_name"},
                        {"taregts": 10, "searchable": true, "data": "vendor_name"},
                        //{"taregts": 5, "searchable": true, "data": "note"},
                        {"taregts": 11,
                            "render": function (data, type, row) {
                                if(row.note==null)
                                {
                                    row.note = 'No Found';
                                }
                                return '<input style="display:none" type="textarea" id="work_note_' + row.id + '" value="' + row.note + '" /><a class="btn btn-warning" data-toggle="modal" data-target="#work_note_modal" href="#" onclick="show_work_note(' + row.id + ');">View</a>';
                            }
                        },
                        {"taregts": 12, "searchable": true, "data": "transation_detail"},
                        {"taregts": 13, "searchable": true, "data": "transaction_type"},
                        {"taregts": 14,
                            "render": function (data, type, row) {
                                if(row.payment_card==null)
                                {
                                    return "N/A";
                                }
                                else
                                {
                                    return row.payment_card.replace(/\d(?=\d{4})/g, "x");
                                }

                            }
                        },
                        {"taregts": 15,
                            "render": function (data, type, row) {
                                if (row.vendor_bank_name)
                                {
                                    return row.vendor_bank_name + " (" + row.ac_number + ")";
                                } else
                                {
                                    return "N/A";
                                }
                            }
                        },
                        {"taregts": 16, "render": function (data, type, row) {
                                    if (row.bank_name)
                                    {
                                        return row.bank_name;
                                    } else {
                                        return "N/A";
                                    }
                            }
                        },
                        {"taregts": 17, "searchable": true,
                            "render": function (data, type, row) {
                                if (row.amount) {
                                    return  Number(parseFloat(row.amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                                    var igst_amount = 0;
                                    var cgst_amount = 0;
                                    var sgst_amount = 0;
                                    var tds_amount = 0;
                                    var main_amount = 0;
                                    if(row.igst_amount){
                                        igst_amount = row.igst_amount;
                                    }
                                    if(row.cgst_amount){
                                        cgst_amount = row.cgst_amount;
                                    }
                                    if(row.sgst_amount){
                                        sgst_amount = row.sgst_amount;
                                    }
                                    if(row.tds_amount){
                                        tds_amount = row.tds_amount;
                                    }
                                    main_amount = row.amount - igst_amount - cgst_amount - sgst_amount + parseFloat(tds_amount);
                                    var title = 'Amount = '+main_amount+', IGST = '+igst_amount+', CGST = '+cgst_amount+' , SGST = '+sgst_amount+', TDS = '+tds_amount;
                                    // return '<a href="javascript:void(0)" title="'+title+'">'+amount+'</a>';
                                }else{
                                    return "0";
                                }

                            }
                        },
                        {"targets": 18, "searchable": true, "render": function (data, type, row) {
                                if (row.igst_amount) {
                                    return  Number(parseFloat(row.igst_amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                                }else{
                                    return "0.00";
                                }

                            }
                        },
                        {"targets": 19, "searchable": true,"render": function (data, type, row) {
                                if (row.cgst_amount) {
                                    return  Number(parseFloat(row.cgst_amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                                }else{
                                    return "0.00";
                                }

                            }
                        },
                        {"targets": 20, "searchable": true,"render": function (data, type, row) {
                                if (row.sgst_amount) {
                                    return  Number(parseFloat(row.sgst_amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                                }else{
                                    return "0.00";
                                }

                            }
                        },

                        {
                            "taregts": 21,
                            "searchable": true,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.created_at) {
                                    out+='<span style="display: none;">'+ row.created_at +'</span>';
                                    out+= moment(row.created_at).format("DD-MM-YYYY");
                                }
                                return out;
                            }
                        },
                    ],
                    "drawCallback": function( settings ) {
                        $("#total_amount").text("");
                        $("#total_amount").text(total_sum_amount);
                    }
                });
            })
            function openPolicy(pdf, id) {
                $('#tableBodyPolicy').empty();
                var iframeUrl = "<iframe src=" + pdf + "#toolbar=0 height='400' width='880'></iframe>";
                $('#tableBodyPolicy').append(iframeUrl);
            }
            $("#company_id").on('change',function(){
                // $('#policy_table').DataTable().draw();
            });

            $("#search_btn").on('click',function(){
                // var date_range = $("#date_range").val();
                // alert(date_range);
                $('#policy_table').DataTable().draw();
            });
        </script>
        @endsection
