@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Edit Bank Payment Details</h4>
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
                        <form action="{{ route('admin.update_payment') }}" id="update_payment" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $bank_payment_detail[0]->id; ?>" />

                            <div class="form-group ">
                                <label>Payment Options</label>
                                <select class="form-control" onchange="get_budget_sheet();" name="payment_options" id="payment_options">
                                
                                        <option <?php if ($bank_payment_detail[0]->payment_options == "Budget Sheet") { ?> selected <?php } ?> value="Budget Sheet">Budget Sheet</option>
                                        <option <?php if ($bank_payment_detail[0]->payment_options == "Emergency Option") { ?> selected <?php } ?> value="Emergency Option">Emergency Option</option>
                                    
    
                                </select>
                            </div>

                            <div class="form-group" style='display:none;' id="budget_sheet">
                                <label>Budget Sheets</label>
                                <select class="form-control" name="budget_sheet_id"  id="budget_sheet_id">
                                    
                                </select>
                            </div>


                            <div class="form-group" id="entry_code_div">
                                <label>Select Main Entry(Select main entry if this is part payment of any old entry)</label>
                                <select class="form-control" onchange="get_payment_data();" name="entry_code" id="entry_code">
                                    <option value="">Select Main Entry</option>
                                    @foreach($main_entry_list as $main_entry)
                                    <option value="{{ $main_entry }}" <?php echo ($bank_payment_detail[0]->entry_code == $main_entry) ? "selected='selected'" : '' ?>>{{ $main_entry }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Company</label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}" <?php echo ($bank_payment_detail[0]->company_id == $company_list_data->id) ? "selected='selected'" : '' ?>>{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Select Client</label>
                                <select class="form-control" id="client_id" name="client_id">
                                    <option value="">Select Client</option>
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Select Project</label>
                                <select class="form-control" id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                </select>
                            </div>

                            <div class="form-group" id="other_cash_txt" style="display:none;">
                                <label>Other Project</label>
                                <input type="text" class="form-control" name="other_project_detail" id="other_project_detail" value="<?php echo $bank_payment_detail[0]->other_project_detail; ?>" />
                            </div>
                            <div class="form-group ">
                                <label>Select Project Site</label>
                                <select class="form-control" id="project_site_id" name="project_site_id">
                                    <option value="">Select Site</option>
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Vendor Name</label>
                                <div id="vendor_response">
                                    <select class="form-control" id='vendor_id' name='vendor_id'>
                                        <option value="">Select vendor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label>Bank Name</label>
                                <div id="response">
                                    <select class="form-control" id="bank_id" name="bank_id">
                                        <option value="">Select bank</option>
                                    </select>
                                </div>
                            </div>



                            <div class="form-group ">
                                <label>Vendor/Party Bank Details</label>
                                <!-- <textarea class="form-control" name="bank_details" id="bank_details"></textarea> -->
                                <select class="form-control" id="bank_details" name="bank_details">
                                    <option value="">Select Vendor/Party bank</option>
                                </select>

                            </div>

                            <div class="form-group ">
                                <label>Cheque</label>
                                <select class="form-control" id="cheque_number" name="cheque_number">
                                    <option value="">Select cheque</option>
                                </select>
                                <input type="hidden" name="old_cheque_number" id="old_cheque_number" value="<?php echo !(empty($ch_no)) ? $ch_no : "" ?>">
                            </div>

                            <div class="form-group ">
                                <label>Cheque Issue Date</label>
                                <input type="text" class="form-control" name="issue_date" id="issue_date" value="<?php echo !(empty($cheque_number[0]->issue_date)) ? date('d-m-Y', strtotime($cheque_number[0]->issue_date)) : "" ?>" />
                            </div>

                            <div class="form-group ">
                                <label>Total Amount </label>
                                <input type="text" @if(!$bank_payment_detail[0]->main_entry) readonly="" @endif class="form-control" name="total_amount" id="total_amount" value="<?php echo $bank_payment_detail[0]->total_amount; ?>" />
                            </div>

                            <div class="form-group ">
                                <label>Amount</label>
                                <input type="textarea" class="form-control" name="amount" id="amount" value="<?php echo $bank_payment_detail[0]->amount; ?>" />
                            </div>
                            <div class="form-group ">
                                <label>Work Detail</label>
                                <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false" value="<?php echo $bank_payment_detail[0]->note; ?>"><?php echo $bank_payment_detail[0]->note; ?></textarea>
                            </div>
                            <div class="form-group ">
                                <label>Bank Payment File</label>
                                <input type="file" name="payment_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/msexcel
                                ,application/pdf,image/jpg,image/jpeg,image/png" id="payment_file" class="form-control" />
                            </div>
                            <div class="form-group ">
                                <label>Invoice File</label>
                                <input type="file" name="invoice_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/msexcel
                                ,application/pdf,image/jpg,image/jpeg,image/png" id="invoice_file" class="form-control" />
                            </div>

                            <div class="form-group ">
                                <label>Multiple Payment files</label>
                                <input type="file" name="bank_payment_files[]" multiple="" id="bank_payment_files" class="form-control" />
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.payment') }}'" class="btn btn-default">Cancel</button>
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
    function get_payment_data() {
        $("#other_project_detail").val("");
        var entry_code = $('#entry_code').val();
        if (entry_code) {

            $.ajax({
                url: "{{ route('admin.get_bank_payment_data') }}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    entry_code: entry_code
                },
                success: function(data) {
                    $('#total_amount').val(data.main_entry[0].total_amount);
                    $('#amount').val('');
                    $('#company_id').val(data.main_entry[0].company_id);
                    if (data.main_entry[0].company_id) {
                        $('#company_id').trigger('change', [{
                            //p_id: data.main_entry[0].project_id,
                            client_id : data.main_entry[0].client_id,
                            b_id: data.main_entry[0].bank_id,
                            v_id: data.main_entry[0].vendor_id,
                            //other_project: data.main_entry[0].other_project_detail
                        }]);
                    }
                    if (data.main_entry[0].client_id) {
                        $('#client_id').trigger('change', [{
                            p_id: data.main_entry[0].project_id,
                            other_project: data.main_entry[0].other_project_detail,
                            project_site_id: data.main_entry[0].project_site_id
                        }]);
                    }
                    if (data.main_entry[0].vendor_id) {
                        $('#vendor_id').trigger('change', [{
                            d_id: data.main_entry[0].bank_details
                        }]);
                    }

                    $('#note').val(data.main_entry[0].note);
                    $('#company_id').attr("style", "pointer-events: none;");
                    $('#total_amount').attr('readonly', true);

                    jQuery('#issue_date').datepicker({
                        autoclose: true,
                        todayHighlight: true,
                        format: "dd-mm-yyyy"
                    });
                }
            })

        } else {

            location.reload(true);

        }
    }

    function get_budget_sheet() {
        var check_val = $('#payment_options').val();
        if (check_val == 'Budget Sheet') {
            $('#budget_sheet').show();
            $.ajax({
                url: "{{ route('admin.getBudgetSheets') }}",
                type: "get",
                dataType: "JSON",
                success: function(data) {
                      var items= data.data.budget_sheets;
                    
                    $('#budget_sheet_id').select2('destroy'); 
                    $('#budget_sheet_id').empty();
                    $('#budget_sheet_id').select2();
                    $.each(items, function(index, object) {

                        $("#budget_sheet_id").append('<option value="' + object.id + '">' + object.budhet_sheet_no + '</option>');

                    });

                }
            });
            
        }else{
            $('#budget_sheet').hide();
        }
        
    }

    jQuery('#add_payment').validate({
        ignore: [],
        rules: {
            bank_id: {
                required: true,
            },
            payment_options: {
                required: true
            },
            bank_details: {
                required: true
            },
            total_amount: {
                required: true
            },
            amount: {
                required: true
            },
            company_id: {
                required: true
            },
            project_id: {
                required: true
            },
            vendor_id: {
                required: true
            },
            issue_date: {
                required: true
            },
            cheque_number: {
                required: true
            }
        }
    });
    $(document).ready(function() {
        //$('#budget_sheet_id').select2();
        jQuery('#issue_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        let check_entry = <?php echo $bank_payment_detail[0]->main_entry; ?>;
        if (check_entry == 0) {

            $('#company_id').attr("style", "pointer-events: none;");
            $('#project_id').attr("style", "pointer-events: none;");
            $('#vendor_id').attr("style", "pointer-events: none;");
            $('#bank_details').attr("style", "pointer-events: none;");
            $('#client_id').attr("style", "pointer-events: none;");
            $('#project_site_id').attr("style", "pointer-events: none;");
        } else {
            document.getElementById('entry_code_div').style.display = 'none';
        }

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
                    //'<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>');
                });
                $("#client_id").append(htmlStr);
                setTimeout(() => {
                    $("#client_id").val("<?php echo $bank_payment_detail[0]->client_id; ?>");
                }, 1000);


            }
        });


        //=======================================================Budget sheet ===================
       
        var check_val = "<?php echo $bank_payment_detail[0]->payment_options; ?>";
        if (check_val == 'Budget Sheet') {
            $('#budget_sheet').show();
        $.ajax({
                url: "{{ route('admin.getBudgetSheets') }}",
                type: "get",
                dataType: "JSON",
                success: function(data) {
                     
                    var items= data.data.budget_sheets;
                    
                    $('#budget_sheet_id').select2();
                    $.each(items, function(index, object) {

                        $("#budget_sheet_id").append('<option value="' + object.id + '">' + object.budhet_sheet_no + '</option>');

                    });

                    $("#budget_sheet_id").val("<?php echo $bank_payment_detail[0]->budget_sheet_id; ?>");
                    
                    $('#budget_sheet_id').trigger('change');
                }
            });

        }

        //=======================================================Budget sheet ===================


        client_id = "<?php echo $bank_payment_detail[0]->client_id; ?>";
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
                    
                    $("#project_id").val("<?php echo $bank_payment_detail[0]->project_id; ?>");
                    var other_project = "<?php echo $bank_payment_detail[0]->project_id; ?>";
                    if (other_project == 1) {   
                        $("#other_cash_txt").show();
                    }
                //}, 1000);
            }
        });

        //site
        project_id = client_id = "<?php echo $bank_payment_detail[0]->project_id; ?>";
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
                    $("#project_site_id").val("<?php echo $bank_payment_detail[0]->project_site_id; ?>");
                }, 1000);
            }
        });

        $.ajax({
            url: "{{ route('admin.get_cash_vendor_list')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function(data, textStatus, jQxhr) {
                $('#vendor_id').empty();
                $('#vendor_id').append(data);
                $("#vendor_id").val("<?php echo $bank_payment_detail[0]->vendor_id; ?>");
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
                $('#bank_id').empty();
                $('#bank_id').append(data);
                $("#bank_id").val("<?php echo $bank_payment_detail[0]->bank_id; ?>");
            },
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        var vendor_id = "<?php echo $bank_payment_detail[0]->vendor_id; ?>"
        $.ajax({
            url: "{{ route('admin.get_vendor_bank_details')}}",
            type: 'get',
            data: "company_id=" + company_id + "&" + "vendor_id=" + vendor_id,
            success: function(data, textStatus, jQxhr) {
                $('#bank_details').empty();
                $('#bank_details').append(data);
                $("#bank_details").val("<?php echo $bank_payment_detail[0]->bank_details; ?>");
            },
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        var chno = "<?php echo $ch_no; ?>";
        var id = "<?php echo $bank_payment_detail[0]->id; ?>"
        $.ajax({
            url: "{{ route('admin.get_bank_cheque_list_edit')}}",
            type: 'get',
            data: "company_id=" + company_id + '&id=' + id,
            success: function(data, textStatus, jQxhr) {
                $('#cheque_number').empty();
                $('#cheque_number').append(data);
                if (chno != 0) {
                    $("#cheque_number").val(chno);
                }
                //console.log("<?php echo $chno; ?>")
            },
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        $("#company_id").change(function(e, json) {      //clients

            var company_id = $("#company_id").val();
            if (company_id.length >= 1) {

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
                        if (json) {
                            setTimeout(() => {
                                $('#client_id option[value=' + json.client_id + ']').attr('selected', 'selected');
                                $('#client_id').attr("style", "pointer-events: none;");
                            }, 2000);

                        }
                    }
                });

                $.ajax({ //banks
                    url: "{{ route('admin.get_bank_list_cheque')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {
                        $('#bank_id').empty();
                        $('#bank_id').append(data);
                        if (json) {
                            $('#bank_id option[value=' + json.b_id + ']').attr('selected', 'selected');
                            $('#bank_id').change();
                        }
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });

                $.ajax({ //vendors
                    url: "{{ route('admin.get_cash_vendor_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {
                        $('#vendor_id').empty();
                        $('#vendor_id').append(data);
                        if (json) {
                            $('#vendor_id option[value=' + json.v_id + ']').attr('selected', 'selected');
                            $('#vendor_id').change();
                            $('#vendor_id').attr("style", "pointer-events: none;");
                        }
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });

                $('#bank_details').empty();
                $('#cheque_number').empty();
            }

        });

        $("#bank_id").change(function() {          //cheuqes 
            var company_id = $("#company_id").val();
            var bank_id = $("#bank_id").val();

            $.ajax({
                url: "{{ route('admin.get_cheque_list_bank_payment')}}",
                type: 'get',
                data: "company_id=" + company_id + "&" + "bank_id=" + bank_id,
                success: function(data, textStatus, jQxhr) {
                    $('#cheque_number').empty();
                    $('#cheque_number').append(data);
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

        $("#vendor_id").change(function(e, object) { //bank details
            var company_id = $("#company_id").val();
            var vendor_id = $("#vendor_id").val();

            $.ajax({
                url: "{{ route('admin.get_vendor_bank_details')}}",
                type: 'get',
                data: "company_id=" + company_id + "&" + "vendor_id=" + vendor_id,
                success: function(data, textStatus, jQxhr) {
                    $('#bank_details').empty();
                    $('#bank_details').append(data);
                    if (object) {

                        setTimeout(function() {
                            $('#bank_details option[value=' + object.d_id + ']').attr('selected', 'selected');
                            $('#bank_details').change();
                            $('#bank_details').attr("style", "pointer-events: none;");
                        }, 2000)

                    }
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

        $('#client_id').change((e, objectData) => {        //projects 
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
                    if (objectData.p_id) {
                        $('#project_id option[value=' + objectData.p_id + ']').attr('selected', 'selected');
                        $('#project_id').trigger('change', [{
                            other_project: objectData.other_project,
                            project_site_id: objectData.project_site_id
                        }]);
                        $('#project_id').attr("style", "pointer-events: none;");

                    }
                }
            });
        });

        $("#project_id").change(function(e, objectData) { //other project details, sites
            project_id = $("#project_id").val();
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
                    if (objectData) {
                        $('#project_site_id option[value=' + objectData.project_site_id + ']').attr('selected', 'selected');
                        $('#project_site_id').attr("style", "pointer-events: none;");
                    }
                }
            });

            var project_id = $("#project_id").val();
            if (project_id == 1) {
                $("#other_cash_txt").show();
                $("#other_project_detail").val("");
                if (objectData) {
                    $("#other_project_detail").val(objectData.other_project);
                    $('#other_project_detail').attr("style", "pointer-events: none;");
                }
            } else {
                $("#other_cash_txt").hide();
            }
        });

        //==============================================

    });
</script>
@endsection