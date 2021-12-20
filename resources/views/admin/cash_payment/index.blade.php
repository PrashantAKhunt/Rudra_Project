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
                <?php
                $role = explode(',', $access_rule);
                if (in_array(3, $role)) {
                    ?>
                    <a href="{{ route('admin.add_cash_payment_detail') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Cash Payment</a>
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.CASH_PAYMENT_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Payment Option</th>
                                <th>Budget Sheet Number</th>
                                <th>Entry Code</th>
                                <th>Payment Title</th>
                                <th>Company Name</th>
                                <th>Client Name</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Project Site Name</th>
                                <th>Vendor Name</th>
                                <th>Requested By</th>
                                <th>Expence Done By</th>
                                <th>Amount</th>
                                <th>IGST Amount</th>
                                <th>CGST Amount</th>
                                <th>SGST Amount</th>
                                <th>Payment Note</th>
                                <th>Vendor Invoice No.</th>
                                <th>Admin Status</th>
                                <th>Accountant Status</th>
                                <th>Super Admin Status</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
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

@endsection

@section('script')
<script>
    function show_reject_note(id) {
        $('#reject_note_div').html($('#reject_note_' + id).val());

    }
    $(document).ready(function () {
        var access_rule = '<?php echo $access_rule; ?>';
        access_rule = access_rule.split(',');

        var table = $('#policy_table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            //stateSave: true,
            // order:[[15,'DESC']],
            "ajax": {
                url: "<?php echo route('admin.get_cash_payment_list'); ?>",
                type: "GET",
            },
            "columns": [
                {"targets": 0, "searchable": true, "data": "payment_options"},
                {"targets": 1, "searchable": true, "render": function (data, type, row) {
                        if (row.budhet_sheet_no) {
                            return row.budhet_sheet_no;
                        } else {
                            return "N/A";
                        }
                    }
                },
                {"taregts": 2, "searchable": true, "data": "entry_code"},
                {"taregts": 3, "searchable": true, "data": "title"},
                {"taregts": 4, "searchable": true, "data": "company_name"},
                {"targets": 5, "searchable": true, "render" : function(data, type, row){
                            if (row.client_name) {
                                if (row.client_name == 'Other Client') {
                                    return row.client_name ;
                                }else{
                                    return row.client_name + " (" + row.location + ")";
                                }

                                } else {
                                    return "N/A";
                                }
                                }},
                {"taregts": 6, "searchable": true, "data": "project_name"},
                {"taregts": 7, "searchable": true, "data": "other_cash_detail"},
                {"targets": 8, "searchable": true, "data": "site_name"},
                {"taregts": 9, "searchable": true, "data": "vendor_name"},
                {"taregts": 10, "searchable": true, "data": "requested_by_name"},
                {"taregts": 11, "searchable": true, "data": "expence_done_name"},
                {"taregts": 12, "searchable": true,
                    "render": function (data, type, row) {
                                var a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
                                var b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

                                var out = '';
                                if (row.amount && row.amount > 0) {
                                    function inWords (totalRent) {
                                        var number = parseFloat(totalRent).toFixed(2).split(".");
                                        var num = parseInt(number[0]);
                                        var digit = parseInt(number[1]);
                                    if ((num = num.toString()).length > 9) return 'overflow';
                                        var n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
                                        var d = ('00' + digit).substr(-2).match(/^(\d{2})$/);;
                                        if (!n) return; var str = '';
                                        str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
                                        str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
                                        str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
                                        str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
                                        str += (n[5] != 0) ? (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'Rupee ' : '';
                                        str += (d[1] != 0) ? ((str != '' ) ? "and " : '') + (a[Number(d[1])] || b[d[1][0]] + ' ' + a[d[1][1]]) + 'Paise ' : 'Only!';
                        
                                    return str;
                                    }
                                    out += Number(parseFloat(row.amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                                    out += `<br><b >${inWords(Number(row.amount))}</b>`;
                                    return out;
                                    var igst_amount = 0;
                                    var cgst_amount = 0;
                                    var sgst_amount = 0;
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
                                    main_amount = row.amount - igst_amount - cgst_amount - sgst_amount;
                                    var title = 'Amount = '+main_amount+', IGST = '+igst_amount+', CGST = '+cgst_amount+' , SGST = '+sgst_amount+'';
                                    // return '<a href="javascript:void(0)" title="'+title+'">'+amount+'</a>';
                                }else{
                                    return "0";
                                }

                            }
                },
                {"targets": 13, "searchable": true, "render": function (data, type, row) {
                            var a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
                            var b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

                            var out = '';
                        if (row.igst_amount && row.igst_amount > 0) {
                            function inWords (totalRent) {
                                        var number = parseFloat(totalRent).toFixed(2).split(".");
                                        var num = parseInt(number[0]);
                                        var digit = parseInt(number[1]);
                                    if ((num = num.toString()).length > 9) return 'overflow';
                                        var n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
                                        var d = ('00' + digit).substr(-2).match(/^(\d{2})$/);;
                                        if (!n) return; var str = '';
                                        str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
                                        str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
                                        str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
                                        str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
                                        str += (n[5] != 0) ? (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'Rupee ' : '';
                                        str += (d[1] != 0) ? ((str != '' ) ? "and " : '') + (a[Number(d[1])] || b[d[1][0]] + ' ' + a[d[1][1]]) + 'Paise ' : 'Only!';
                        
                                    return str;
                                    }
                            out += Number(parseFloat(row.igst_amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                            out += `<br><b >${inWords(Number(row.igst_amount))}</b>`;
                            return out;
                        }else{
                            return "0.00";
                        }

                    }
                },
                {"targets": 14, "searchable": true,"render": function (data, type, row) {
                    var a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
                            var b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

                            var out = '';
                        if (row.cgst_amount && row.cgst_amount > 0) {
                            function inWords (totalRent) {
                                        var number = parseFloat(totalRent).toFixed(2).split(".");
                                        var num = parseInt(number[0]);
                                        var digit = parseInt(number[1]);
                                    if ((num = num.toString()).length > 9) return 'overflow';
                                        var n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
                                        var d = ('00' + digit).substr(-2).match(/^(\d{2})$/);;
                                        if (!n) return; var str = '';
                                        str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
                                        str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
                                        str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
                                        str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
                                        str += (n[5] != 0) ? (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'Rupee ' : '';
                                        str += (d[1] != 0) ? ((str != '' ) ? "and " : '') + (a[Number(d[1])] || b[d[1][0]] + ' ' + a[d[1][1]]) + 'Paise ' : 'Only!';
                        
                                    return str;
                                    }
                            out += Number(parseFloat(row.cgst_amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                            out += `<br><b >${inWords(Number(row.cgst_amount))}</b>`;
                            return out;
                        }else{
                            return "0.00";
                        }

                    }
                },
                {"targets": 15, "searchable": true,"render": function (data, type, row) {
                    var a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
                            var b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

                            var out = '';
                        if (row.sgst_amount && row.sgst_amount > 0) {
                            function inWords (totalRent) {
                                        var number = parseFloat(totalRent).toFixed(2).split(".");
                                        var num = parseInt(number[0]);
                                        var digit = parseInt(number[1]);
                                    if ((num = num.toString()).length > 9) return 'overflow';
                                        var n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
                                        var d = ('00' + digit).substr(-2).match(/^(\d{2})$/);;
                                        if (!n) return; var str = '';
                                        str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
                                        str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
                                        str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
                                        str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
                                        str += (n[5] != 0) ? (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'Rupee ' : '';
                                        str += (d[1] != 0) ? ((str != '' ) ? "and " : '') + (a[Number(d[1])] || b[d[1][0]] + ' ' + a[d[1][1]]) + 'Paise ' : 'Only!';
                        
                                    return str;
                                    }
                            out += Number(parseFloat(row.sgst_amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                            out += `<br><b >${inWords(Number(row.sgst_amount))}</b>`;
                            return out;
                        }else{
                            return "0.00";
                        }

                    }
                },
                {"taregts": 16, "searchable": true, "data": "note"},
                {"taregts": 17, "searchable": true, "data": "vender_invoice_no"},
                {
                            "taregts": 18,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.first_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.first_approval_status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (row.first_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }

                            }
                        },
                        {
                            "taregts": 19,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.second_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.second_approval_status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (row.second_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }

                            }
                        },
                        {
                            "taregts": 20,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.third_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.third_approval_status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (row.third_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }

                            }
                        },

                {"taregts": 21,
                    "render": function (data, type, row) {
                        var out = '';
                        if (row.status == 'Pending')
                        {
                            return'<b class="text-warning">Pending</b>';
                        } else if (row.status == 'Approved')
                        {
                            return '<b class="text-success">Approved</b>';
                        } else
                        {
                            return '<input type="hidden" id="reject_note_' + row.id + '" value="' + row.reject_note + '" /><a class="btn btn-danger" data-toggle="modal" data-target="#reject_note_modal" href="#" onclick="show_reject_note(' + row.id + ');">Rejected</a>';
                        }
                    }
                },
                {"taregts": 22, "searchable": true, "render": function (data, type, row) {
                        var out = '';
                        if (row.created_at) {
                            out+='<span style="display: none;">'+ row.created_at +'</span>';
                            out+= moment(row.created_at).format("DD-MM-YYYY");
                        }
                        return out;
                    }
                },
                {"taregts": 23, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = "";
                        if (row.second_approval_status == 'Pending') {
                            if (($.inArray('2', access_rule) !== -1) && row.status != "Approved") {
                                out = '<a href="<?php echo url("edit_cash_payment_detail") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                            }
                        }
                        // if (($.inArray('2',access_rule) !== -1)) {
                        //     out+= '<a href="<?php //echo url("delete_policy")    ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                        // }
                        if (row.payment_file) {
                            var images = row.payment_file;
                            var images_arr = images.split(',');
                            $.each(images_arr, function (key, val) {
                                var file_path = val.replace("public/", "");
                                var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                                out += '<a href="' + download_link + '" title="Download Cash Payment File" class="btn btn-rounded btn-primary" target="_blank" download><i class="fa fa-download"></i></a>';
                            });

                            /* var file_path = row.payment_file.replace("public/", "");
                            var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                            out += '<a href="' + download_link + '" title="Download Cash Payment File" class="btn btn-rounded btn-primary" target="_blank" download><i class="fa fa-download"></i></a>'; */
                        }
                        return out;
                    }
                }
            ]
        });
    })
    function openPolicy(pdf, id) {
        $('#tableBodyPolicy').empty();
        var iframeUrl = "<iframe src=" + pdf + "#toolbar=0 height='400' width='880'></iframe>";
        $('#tableBodyPolicy').append(iframeUrl);
    }
</script>
@endsection
