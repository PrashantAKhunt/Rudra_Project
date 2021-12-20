@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Edit Online Payment Details</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
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
                        <form action="{{ route('admin.update_online_payment') }}" id="update_payment" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $online_payment_detail[0]->id; ?>" />
                            <div class="form-group ">
                                <label>Select Company</label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}" <?php echo ($online_payment_detail[0]->company_id == $company_list_data->id) ? "selected='selected'" : '' ?>>{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Bank Name</label>
                                <div id="response">
                                    <select class="form-control" id="bank_id" name="bank_id">
                                        <option value="">Select bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Transaction Type</label>
                                <div id="response">
                                    <select class="form-control" id="transaction_type" name="transaction_type">
                                        <option value="">Select Transaction</option>
                                        <option value="Netbanking" <?php echo ($online_payment_detail[0]->transaction_type == 'Netbanking') ? "selected='selected'" : '' ?>>Netbanking</option>
                                        <option value="Debit Card" <?php echo ($online_payment_detail[0]->transaction_type == 'Debit Card') ? "selected='selected'" : '' ?>>Debit Card</option>
                                        <option value="Credit Card" <?php echo ($online_payment_detail[0]->transaction_type == 'Credit Card') ? "selected='selected'" : '' ?>>Credit Card</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="display:none;" id="transaction_id_drop">
                                <label>Payment Card</label>
                                <div id="response">
                                    <select class="form-control" id="transaction_id" name="transaction_id">
                                        <option value="">Select Payment Card</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Select Client</label>
                                <select class="form-control" id="client_id" name="client_id">
                                    <option value="">Select Client</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Project</label>
                                <select class="form-control" id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Project Site</label>
                                <select class="form-control" id="project_site_id" name="project_site_id">
                                    <option value="">Select Site</option>
                                </select>
                            </div>
                            <div class="form-group" id="other_cash_txt" style="display:none;">
                                <label>Other Project</label>
                                <input type="text" class="form-control" name="other_project_detail" id="other_project_detail" value="<?php echo $online_payment_detail[0]->other_project_detail; ?>" />
                            </div>

                            <div class="form-group ">
                                <label>Vendor Name</label>
                                <div id="vendor_response">
                                    <select class="form-control" id='vendor_id' name='vendor_id'>
                                        <option value="">Select vendor</option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="form-group ">
                                <label>Vendor/Party Bank Details</label>
                                <textarea class="form-control" name="bank_details" id="bank_details"><?php echo $online_payment_detail[0]->bank_details; ?></textarea>
                                
                            </div> -->

                            <div class="form-group ">
                                <label>Vendor/Party Bank Details</label>
                                <!-- <textarea class="form-control" name="bank_details" id="bank_details"></textarea> -->
                                <select class="form-control" id="bank_details" name="bank_details">
                                    <option value="">Select Vendor/Party bank</option>
                                </select>

                            </div>

                            <div class="form-group ">
                                <label>Amount</label>
                                <input type="textarea" class="form-control" name="amount" id="amount" value="<?php echo $online_payment_detail[0]->amount; ?>" />
                            </div>
                            <div class="form-group ">
                                <label>Work Detail</label>
                                <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false" value="<?php echo $online_payment_detail[0]->note; ?>"><?php echo $online_payment_detail[0]->note; ?></textarea>
                            </div>

                            <div class="form-group ">
                                <label>Transation Detail</label>
                                <input type="text" class="form-control valid" rows="6" name="transation_detail" id="transation_detail" spellcheck="false" value="<?php echo $online_payment_detail[0]->transation_detail; ?>">
                            </div>

                            <div class="form-group ">
                                <label>Payment File (CTRL+click to select multiple files)</label>
                                <input type="file" onchange="get_count(this)" class="form-control" name="online_payment_file[][]" id="online_payment_file" multiple="" />
                                <input type="hidden" name="file_counts[]" id="file_counts" value="0" />
                            </div>

                            @if($online_payment_detail[0]->status=='Pending')
                            <button type="submit" class="btn btn-success">Submit</button>
                            @endif

                            <button type="button" onclick="window.location.href ='{{ route('admin.online_payment') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    jQuery('#add_payment').validate({
        ignore: [],
        rules: {
            bank_id: {
                required: true,
            },
            transaction_type: {
                required: true,
            },
            amount: {
                required: true
            },
            company_id: {
                required: true
            },
            client_id: {
                required: true
            },
            project_site_id: {
                required: true
            },
            project_id: {
                required: true
            },
            vendor_id: {
                required: true
            }
        }
    });
    $(document).ready(function() {
        jQuery('#issue_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        /* --------------------------------------Default-------------------------------------- */
        var company_id = $("#company_id").val();


        //Clients list:
        htmlStr = '';
        $.ajax({
            url: "{{ route('admin.get_company_client_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                company_id: company_id
            },
            dataType: "JSON",
            success: function(data) {
                $("#client_id").empty();
                $("#client_id").append("<option value='' selected>Select Client</option>");
                $.each(data, function(index, clients_obj) {
                    if (clients_obj.id == 1) {
                                htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                            }else{
                                htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                            }
                    //$("#client_id").append('<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>');
                });
                $("#client_id").append(htmlStr);
                $("#client_id").val("<?php echo $online_payment_detail[0]->client_id; ?>");
            }
        });

        client_id = "<?php echo $online_payment_detail[0]->client_id; ?>";
        $.ajax({
            url: "{{ route('admin.get_client_project_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                client_id: client_id
            },
            dataType: "JSON",
            success: function(data) {
                $("#project_id").empty();
                $("#project_id").append("<option value='' selected>Select Project</option>");
                $.each(data, function(index, projects_obj) {
                    $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                });
                // setTimeout(() => {

                $("#project_id").val("<?php echo $online_payment_detail[0]->project_id; ?>");
                var other_project = "<?php echo $online_payment_detail[0]->project_id; ?>";
                if (other_project == 1) {
                    $("#other_cash_txt").show();
                }
                //}, 1000);
            }
        });
        //site
        project_id = client_id = "<?php echo $online_payment_detail[0]->project_id; ?>";
        $.ajax({
            url: "{{ route('admin.get_project_sites_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                project_id: project_id
            },
            dataType: "JSON",
            success: function(data) {
                $("#project_site_id").empty();
                $("#project_site_id").append("<option value='' selected>Select Site</option>");
                $.each(data, function(index, project_site_obj) {
                    $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                });
                setTimeout(() => {
                    $("#project_site_id").val("<?php echo $online_payment_detail[0]->project_site_id; ?>");
                }, 1000);
            }
        });
        /* $.ajax({
            url: "{{ route('admin.get_cash_project_list')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function(data, textStatus, jQxhr) {
                $('#project_id').empty();
                $('#project_id').append(data);
                $("#project_id").val("<?php echo $online_payment_detail[0]->project_id; ?>");
                var other_project = "<?php echo $online_payment_detail[0]->project_id; ?>";
                if (other_project == 1) {
                    $("#other_cash_txt").show();
                }
            },
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        }); */

        $.ajax({
            url: "{{ route('admin.get_cash_vendor_list')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function(data, textStatus, jQxhr) {
                $('#vendor_id').empty();
                $('#vendor_id').append(data);
                $("#vendor_id").val("<?php echo $online_payment_detail[0]->vendor_id; ?>");
            },
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        $.ajax({
            url: "{{ route('admin.get_bank_list_cheque')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function(data, textStatus, jQxhr) {
                $("#bank_id").empty();
                $('#bank_id').append(data);
                $("#bank_id").val("<?php echo $online_payment_detail[0]->bank_id; ?>");
            },
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
        var vendor_id = "<?php echo $online_payment_detail[0]->vendor_id; ?>"
        $.ajax({
            url: "{{ route('admin.get_vendor_bank_details')}}",
            type: 'get',
            data: "company_id=" + company_id + "&" + "vendor_id=" + vendor_id,
            success: function(data, textStatus, jQxhr) {
                $('#bank_details').empty();
                $('#bank_details').append(data);
                $("#bank_details").val("<?php echo $online_payment_detail[0]->bank_details; ?>");
            },
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        var transaction_type = $("#transaction_type").val();
        if (transaction_type == 'Debit Card' || transaction_type == 'Credit Card') {
            $("#transaction_id").val("");

            var company_id = $("#company_id").val();
            var bank_id = "<?php echo $online_payment_detail[0]->bank_id; ?>"
            var transaction_type = $("#transaction_type").val();
            $.ajax({
                url: "{{ route('admin.get_bank_card_list')}}",
                type: 'get',
                data: "company_id=" + company_id + "&" + "bank_id=" + bank_id + "&" + "transaction_type=" + transaction_type,
                success: function(data, textStatus, jQxhr) {
                    $('#transaction_id').empty();
                    $('#transaction_id').append(data);
                    $('#transaction_id').val("<?php echo $online_payment_detail[0]->transaction_id; ?>");
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

            $("#transaction_id_drop").show();
        } else {
            $("#transaction_id_drop").hide();
        }

        var id = "<?php echo $online_payment_detail[0]->id; ?>"

        /*-------------------------------------- Ajax call-------------------------------------- */
        $("#company_id").change(function() {

            var company_id = $("#company_id").val();
            if (company_id.length >= 1) {

                //Clients list:
                htmlStr= '';
                $.ajax({
                    url: "{{ route('admin.get_company_client_list') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        company_id: company_id
                    },
                    dataType: "JSON",
                    success: function(data) {

                        $("#client_id").empty();
                        $("#client_id").append("<option value='' selected>Select Client</option>");
                        $.each(data, function(index, clients_obj) {
                            if (clients_obj.id == 1) {
                                htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                            }else{
                                htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                            }
                            //$("#client_id").append('<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>');
                        });
                        $("#client_id").append(htmlStr);

                    }
                });

                /* $.ajax({
                    url: "{{ route('admin.get_cash_project_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {
                        $('#project_id').empty();
                        $('#project_id').append(data);
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                }); */

                $.ajax({
                    url: "{{ route('admin.get_bank_list_cheque')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {
                        $("#bank_id").empty();
                        $('#bank_id').append(data);
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });

                $.ajax({
                    url: "{{ route('admin.get_cash_vendor_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {
                        $('#vendor_id').empty();
                        $('#vendor_id').append(data);
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }

        });

        $("#bank_id").change(function() {
            var company_id = $("#company_id").val();
            var bank_id = $("#bank_id").val();

            $.ajax({
                url: "{{ route('admin.get_cheque_list_bank')}}",
                type: 'get',
                data: "company_id=" + company_id,
                success: function(data, textStatus, jQxhr) {
                    $('#cheque_number').empty();
                    $('#cheque_number').html(data);
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

        $("#vendor_id").change(function() {
            var company_id = $("#company_id").val();
            var vendor_id = $("#vendor_id").val();

            $.ajax({
                url: "{{ route('admin.get_vendor_bank_details')}}",
                type: 'get',
                data: "company_id=" + company_id + "&" + "vendor_id=" + vendor_id,
                success: function(data, textStatus, jQxhr) {
                    $('#bank_details').empty();
                    $('#bank_details').append(data);
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

        $('#client_id').change(() => { //projects 
            //project list
            client_id = $("#client_id").val();
            $.ajax({
                url: "{{ route('admin.get_client_project_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    client_id: client_id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#project_id").empty();
                    $("#project_id").append("<option value='' selected>Select Project</option>");
                    $.each(data, function(index, projects_obj) {
                        $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                    });

                }
            });
        });

        //$('#user_id').select2();
        $("#project_id").change(function() {
            var project_id = $("#project_id").val();

            $.ajax({
                url: "{{ route('admin.get_project_sites_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    project_id: project_id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#project_site_id").empty();
                    $("#project_site_id").append("<option value='' selected>Select Site</option>");
                    $.each(data, function(index, project_site_obj) {
                        $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                    })

                }
            });
            if (project_id == 1) {
                $("#other_cash_txt").show();
            } else {
                $("#other_cash_txt").hide();
            }
        });

        $("#transaction_type").change(function() {
            var transaction_type = $("#transaction_type").val();
            if (transaction_type == 'Debit Card' || transaction_type == 'Credit Card') {
                $("#transaction_id").val("");

                var company_id = $("#company_id").val();
                var bank_id = $("#bank_id").val();
                var transaction_type = $("#transaction_type").val();
                $.ajax({
                    url: "{{ route('admin.get_bank_card_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id + "&" + "bank_id=" + bank_id + "&" + "transaction_type=" + transaction_type,
                    success: function(data, textStatus, jQxhr) {
                        $('#transaction_id').empty();
                        $('#transaction_id').append(data);
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });

                $("#transaction_id_drop").show();
            } else {
                $("#transaction_id_drop").hide();
            }
        });

    });
</script>
@endsection