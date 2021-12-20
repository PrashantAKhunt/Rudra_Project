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
                                <label>Payment Options<span class="error">*</span></label>
                                <select class="form-control" onchange="get_budget_sheet();" name="payment_options" id="payment_options">

                                        <option <?php if ($bank_payment_detail[0]->payment_options == "Budget Sheet") { ?> selected <?php } ?> value="Budget Sheet">Budget Sheet</option>
                                        <option <?php if ($bank_payment_detail[0]->payment_options == "Emergency Option") { ?> selected <?php } ?> value="Emergency Option">Emergency Option</option>
                                        <option <?php if ($bank_payment_detail[0]->payment_options == "Regular") { ?> selected <?php } ?> value="Regular">Regular</option>


                                </select>
                            </div>

                            <div class="form-group" style='display:none;' id="budget_sheet">
                                <label>Budget Sheets<span class="error">*</span></label>
                                <select class="form-control" name="budget_sheet_id"  id="budget_sheet_id">

                                </select>
                            </div>


                            <div class="form-group" id="entry_code_div">
                                <label>Select Main Entry(Select main entry if this is part payment of any old entry)<span class="error">*</span></label>
                                <select class="form-control" onchange="get_payment_data();" name="entry_code" id="entry_code">
                                    <option value="">Select Main Entry</option>
                                    @foreach($main_entry_list as $main_entry)
                                    <option value="{{ $main_entry }}" <?php echo ($bank_payment_detail[0]->entry_code == $main_entry) ? "selected='selected'" : '' ?>>{{ $main_entry }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Company<span class="error">*</span></label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}" <?php echo ($bank_payment_detail[0]->company_id == $company_list_data->id) ? "selected='selected'" : '' ?>>{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Select Client<span class="error">*</span></label>
                                <select class="form-control" id="client_id" name="client_id">
                                    <option value="">Select Client</option>
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Project Status<span class="error">*</span></label>
                                <select class="form-control" name="project_type" id="project_type">
                                <option value="">Select Project Status</option>
                                    <option @if($bank_payment_detail[0]->project_type=='Current') selected @endif value="Current">Current</option>
                                    <option @if($bank_payment_detail[0]->project_type=='Completed') selected @endif value="Completed">Completed</option>
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Project<span class="error">*</span></label>
                                <select class="form-control" id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                </select>
                            </div>

                            <div class="form-group" id="other_cash_txt" style="display:none;">
                                <label>Other Project<span class="error">*</span></label>
                                <input type="text" class="form-control" name="other_project_detail" id="other_project_detail" value="<?php echo $bank_payment_detail[0]->other_project_detail; ?>" />
                            </div>
                            <div class="form-group ">
                                <label>Select Project Site<span class="error">*</span></label>
                                <select class="form-control" id="project_site_id" name="project_site_id">
                                    <option value="">Select Site</option>
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Vendor Name<span class="error">*</span></label>
                                <div id="vendor_response">
                                    <select class="form-control" id='vendor_id' name='vendor_id'>
                                        <option value="">Select vendor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label>Bank Name<span class="error">*</span></label>
                                <div id="response">
                                    <select class="form-control" id="bank_id" name="bank_id">
                                        <option value="">Select bank</option>
                                    </select>
                                </div>
                            </div>



                            <div class="form-group ">
                                <label>Vendor/Party Bank Details<span class="error">*</span></label>
                                <!-- <textarea class="form-control" name="bank_details" id="bank_details"></textarea> -->
                                <select class="form-control" id="bank_details" name="bank_details">
                                    <option value="">Select Vendor/Party bank</option>
                                </select>

                            </div>

                            <!-- <div class="form-group ">
                                <label>Cheque</label>
                                <select class="form-control" id="cheque_number" name="cheque_number">
                                    <option value="">Select cheque</option>
                                </select>
                                <input type="hidden" name="old_cheque_number" id="old_cheque_number" value="<?php //echo !(empty($ch_no)) ? $ch_no : "" ?>">
                            </div>

                            <div class="form-group ">
                                <label>Cheque Issue Date</label>
                                <input type="text" class="form-control" name="issue_date" id="issue_date" value="<?php //echo !(empty($cheque_number[0]->issue_date)) ? date('d-m-Y', strtotime($cheque_number[0]->issue_date)) : "" ?>" />
                            </div> -->

                            <div class="form-group ">
                                <label>Total Amount <span class="error">*</span></label>
                                <input type="text" @if(!$bank_payment_detail[0]->main_entry) readonly="" @endif class="form-control" name="total_amount" id="total_amount" value="<?php echo $bank_payment_detail[0]->total_amount; ?>" />
                            </div>

                            <div class="form-group ">
                                <label>Amount<span class="error">*</span></label>
                                <input type="textarea" class="form-control" name="amount" id="amount" value="<?php echo $bank_payment_detail[0]->amount - $bank_payment_detail[0]->igst_amount - $bank_payment_detail[0]->cgst_amount - $bank_payment_detail[0]->sgst_amount + $bank_payment_detail[0]->tds_amount; ?>" />
                            </div>
                            <div class="form-group ">
                                <label>Work Detail<span class="error">*</span></label>
                                <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false" value="<?php echo $bank_payment_detail[0]->note; ?>"><?php echo $bank_payment_detail[0]->note; ?></textarea>
                            </div>
                            <!-- <div class="form-group ">
                                <label>Bank Payment File</label>
                                <input type="file" name="payment_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/msexcel
                                ,application/pdf,image/jpg,image/jpeg,image/png" id="payment_file" class="form-control" />
                            </div> -->
                            <div class="form-group ">
                                <label>MICR Code</label>
                                <input type="text" class="form-control" name="micr_code" id="micr_code" value="{{($bank_payment_detail[0]->micr_code) ? $bank_payment_detail[0]->micr_code : ''}}" />
                            </div>
                            <div class="form-group ">
                                <label>SWIFT Code</label>
                                <input type="text" class="form-control" name="swift_code" id="swift_code" value="{{($bank_payment_detail[0]->swift_code) ? $bank_payment_detail[0]->swift_code : ''}}" />
                            </div>
                            <div class="form-group ">
                                <label>IGST Amount<span class="error">*</span></label>
                                <input type="number" class="form-control" name="igst_amount" id="igst_amount" value="{{($bank_payment_detail[0]->igst_amount) ? $bank_payment_detail[0]->igst_amount : 0}}" />
                            </div>
                            <div class="form-group ">
                                <label>CGST Amount<span class="error">*</span></label>
                                <input type="number" class="form-control" name="cgst_amount" id="cgst_amount" value="{{($bank_payment_detail[0]->cgst_amount) ? $bank_payment_detail[0]->cgst_amount : 0}}" />
                            </div>
                            <div class="form-group ">
                                <label>SGST Amount<span class="error">*</span></label>
                                <input type="number" class="form-control" name="sgst_amount" id="sgst_amount" value="{{($bank_payment_detail[0]->sgst_amount) ? $bank_payment_detail[0]->sgst_amount : 0}}" />
                            </div>
                            <div class="form-group ">
                                <label>TDS Section Type<span class="error">*</span></label>
                                <select name="section_type_id" id="section_type_id" class="form-control">
                                    <option value="">Select TDS Section Type</option>
                                    @if (count($section_type))
                                        @foreach ($section_type as $key => $value)
                                            <option value="{{$value['id']}}" @if($value['id'] == $bank_payment_detail[0]->section_type_id) selected @endif>{{$value['section_type']}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>TDS Amount<span class="error">*</span></label>
                                <input type="number" class="form-control" name="tds_amount" id="tds_amount" value="{{($bank_payment_detail[0]->tds_amount) ? $bank_payment_detail[0]->tds_amount : 0}}" />
                            </div>
                            <div class="form-group invoice_no_part" style="display: none;">
                                <label>Invoice Number<span class="error">*</span></label>
                                <input type="text" name="invoice_no" id="invoice_no" class="form-control" value="<?php echo $bank_payment_detail[0]->invoice_no; ?>"/>
                            </div>
                            <div class="form-group invoice_no_part" style="display: none;">
                                <label>Invoice File<span class="error">*</span></label>
                                <input type="file" name="invoice_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/msexcel
                                ,application/pdf,image/jpg,image/jpeg,image/png" id="invoice_file" class="form-control" />
                            </div>
                            <div class="form-group ">
                                <label>Payment Method<span class="error">*</span></label>
                                    <select class="form-control" id="payment_method" name="payment_method">
                                        <option value="">Select Payment Method</option>
                                        <option value="Cheque/Rtgs" @if($bank_payment_detail[0]->payment_method == 'Cheque/Rtgs') selected @endif>Cheque/Rtgs</option>
                                        <option value="DD" @if($bank_payment_detail[0]->payment_method == 'DD') selected @endif>DD</option>
                                    </select>
                            </div>
                            <div class="form-group ">
                                <label>Multiple Payment files<span class="error">*</span></label>
                                <input type="file" name="bank_payment_files[]" multiple="" id="bank_payment_files" class="form-control" />
                            </div>
                            <div class="form-group ">
                                <label>Payment Amount : </label><span id="payment_amount" class="text-success"><?php echo $bank_payment_detail[0]->amount - $bank_payment_detail[0]->igst_amount - $bank_payment_detail[0]->cgst_amount - $bank_payment_detail[0]->sgst_amount + $bank_payment_detail[0]->tds_amount; ?></span>
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
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {entry_code: entry_code},
                success: function(data) {
                    $('#total_amount').val(data.main_entry[0].total_amount);
                    $('#amount').val('');
                    $('#company_id').val(data.main_entry[0].company_id).trigger('change.select2');
                    if (data.main_entry[0].company_id) {
                        $('#company_id').trigger('change', [{
                            client_id : data.main_entry[0].client_id,
                            project_type : data.main_entry[0].project_type,
                            b_id: data.main_entry[0].bank_id,
                            v_id: data.main_entry[0].vendor_id,
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
                    $('#company_id').select2('destroy').attr("style", "pointer-events: none;");
                    $('#total_amount').attr('readonly', true);

                    jQuery('#issue_date').datepicker({
                        autoclose: true,
                        todayHighlight: true,
                        format: "dd-mm-yyyy"
                    });
                }
            })

        } else {

            // location.reload(true);

        }
    }

    function get_budget_sheet() {

        var check_val = $('#payment_options').val();
        if (check_val == 'Budget Sheet') {
            $('#budget_sheet').show();
            $('.invoice_no_part').hide();
            $('#invoice_no').val("");
            $.ajax({
                url: "{{ route('admin.getBudgetSheets') }}",
                type: "get",
                dataType: "JSON",
                success: function(data) {

                    $('#budget_sheet_id').select2('destroy').empty().select2();
                    var items= data.data.budget_sheets;
                    $.each(items, function(index, object) {
                        $("#budget_sheet_id").append('<option value="' + object.id + '">' + object.budhet_sheet_no + '</option>');
                    });

                }
            });

        }else if(check_val == "Regular"){
            $('#budget_sheet').hide();
            $('.invoice_no_part').show();
            $("#company_id").select2("readonly", false);
            $("#company_id").val("").trigger("change");
            // $("#company_id").select2('destroy').empty().select2();

            $("#client_id").select2("readonly", false);
            $("#client_id").select2('destroy').empty().select2();

            $("#project_type").select2("readonly", false);
            $('#project_type').select2("val","").trigger('change');

            $("#project_id").select2("readonly", false);
            $("#project_id").select2('destroy').empty().select2();

            $("#project_site_id").select2("readonly", false);
            $("#project_site_id").select2('destroy').empty().select2();

            $("#vendor_id").select2("readonly", false);
            $("#vendor_id").select2('destroy').empty().select2();
            $("#total_amount").val("").prop("readonly", false);
            $("#amount").val("");
            $("#entry_code").select2("readonly", false);
        }
        else{
            $('#budget_sheet').hide();
            $('.invoice_no_part').hide();
            $('#invoice_no').val("");
            $("#company_id").select2("readonly", false);
            $("#company_id").val("").trigger("change");
            // $("#company_id").select2('destroy').empty().select2();

            $("#client_id").select2("readonly", false);
            $("#client_id").select2('destroy').empty().select2();

            $("#project_type").select2("readonly", false);
            $('#project_type').select2("val","").trigger('change');


            $("#project_id").select2("readonly", false);
            $("#project_id").select2('destroy').empty().select2();

            $("#project_site_id").select2("readonly", false);
            $("#project_site_id").select2('destroy').empty().select2();

            $("#vendor_id").select2("readonly", false);
            $("#vendor_id").select2('destroy').empty().select2();
            $("#total_amount").val("").prop("readonly", false);
            $("#amount").val("");
            $("#entry_code").select2("readonly", false);
        }

    }

    jQuery('#update_payment').validate({
        ignore: [],
        rules: {
            bank_id: {
                required: true,
            },
            project_type: {
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
                required: true,
                max: function() {
                    return parseInt($('#total_amount').val());
                }
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
            invoice_no : {
                required: function(element) {
                    if ($("#payment_options").val() == 'Regular') {
                        return true;
                    } else {
                        return false;
                    }
                }
            },
            igst_amount:{
                required:true
            },
            cgst_amount:{
                required:true
            },
            sgst_amount:{
                required:true
            },
            tds_amount:{
                required:true
            },
            section_type_id:{
                required: function(element) {
                    if ($("#tds_amount").val() > 0) {
                        return true;
                    } else {
                        return false;
                    }
                }
            },
            payment_method:{
                required:true
            },
        }
    });
    $(document).ready(function() {

        //$('#budget_sheet_id').select2();
        $('#payment_options').select2();

        $('#project_type').select2();

        $('#budget_sheet_id').select2();
        $('#entry_code').select2();
        $('#company_id').select2();
        $('#client_id').select2();
        $('#project_id').select2();
        $('#project_site_id').select2();
        $('#vendor_id').select2();
        $('#bank_id').select2();
        $('#bank_details').select2();

        jQuery('#issue_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        let check_entry = <?php echo $bank_payment_detail[0]->main_entry; ?>;
        if (check_entry == 0) {

            $('#company_id').select2('destroy').attr("style", "pointer-events: none;");
            $("#project_type").select2("readonly", true);
            $('#project_id').select2('destroy').attr("style", "pointer-events: none;");
            $('#vendor_id').select2('destroy').attr("style", "pointer-events: none;");
            $('#bank_details').select2('destroy').attr("style", "pointer-events: none;");
            $('#client_id').select2('destroy').attr("style", "pointer-events: none;");
            $('#project_site_id').select2('destroy').attr("style", "pointer-events: none;");

        } else {
            document.getElementById('entry_code_div').style.display = 'none';
        }

        $("#amount").trigger('keyup');
        //=======================================================Budget sheet ===================

            var check_val = "<?php echo $bank_payment_detail[0]->payment_options; ?>";
            if (check_val == 'Budget Sheet') {
                $('#budget_sheet').show();
                $('.invoice_no_part').hide();
                $.ajax({
                    url: "{{ route('admin.getBudgetSheets') }}",
                    type: "get",
                    dataType: "JSON",
                    success: function(data) {

                        $('#budget_sheet_id').select2('destroy').empty().select2();
                        var items= data.data.budget_sheets;
                        $.each(items, function(index, object) {
                            $("#budget_sheet_id").append('<option value="' + object.id + '">' + object.budhet_sheet_no + '</option>');
                        });
                        $("#budget_sheet_id").val("<?php echo $bank_payment_detail[0]->budget_sheet_id; ?>").trigger('change.select2');
                    }
                });

            }else if(check_val == "Regular"){
                $('.invoice_no_part').show();
            }

        //=======================================================Budget sheet ===================
        var company_id = $("#company_id").val();
        //---------------------------------Clients list:
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

                $('#client_id').select2('destroy').empty().select2();
                $("#client_id").append("<option value='' selected>Select Client</option>");
                $.each(data, function(index, clients_obj) {
                    if (clients_obj.id == 1) {
                                htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                            }else{
                                htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                            }

                });
                $("#client_id").append(htmlStr);
                setTimeout(() => {
                    $('#client_id').val("<?php echo $bank_payment_detail[0]->client_id; ?>").trigger('change.select2');
                }, 1000);


            }
        });

        client_id = "<?php echo $bank_payment_detail[0]->client_id; ?>";
        project_type = "<?php echo $bank_payment_detail[0]->project_type; ?>";
        $.ajax({
            url: "{{ route('admin.get_client_project_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                client_id: client_id, project_type:project_type
            },
            dataType: "JSON",
            success: function(data) {

                $('#project_id').select2('destroy').empty().select2();
                $("#project_id").append("<option value='' selected>Select Project</option>");
                $.each(data, function(index, projects_obj) {
                    $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                });
               // setTimeout(() => {
                    $('#project_id').val("<?php echo $bank_payment_detail[0]->project_id; ?>").trigger('change.select2');
                    //$("#project_id").val("<?php echo $bank_payment_detail[0]->project_id; ?>");
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

                $('#project_site_id').select2('destroy').empty().select2();
                $("#project_site_id").append("<option value='' selected>Select Site</option>");
                $.each(data, function(index, project_site_obj) {
                    $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                });
                setTimeout(() => {
                    $('#project_site_id').val("<?php echo $bank_payment_detail[0]->project_site_id; ?>").trigger('change.select2');
                    //$("#project_site_id").val("<?php echo $bank_payment_detail[0]->project_site_id; ?>");
                }, 1000);
            }
        });

        $.ajax({
            url: "{{ route('admin.get_cash_vendor_list')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function(data, textStatus, jQxhr) {

                $('#vendor_id').select2('destroy').empty().select2();
                $('#vendor_id').append(data);
                $('#vendor_id').val("<?php echo $bank_payment_detail[0]->vendor_id; ?>").trigger('change.select2');
               // $("#vendor_id").val("<?php echo $bank_payment_detail[0]->vendor_id; ?>");
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

                $('#bank_id').select2('destroy').empty().select2();
                $('#bank_id').append(data);
                $('#bank_id').val("<?php echo $bank_payment_detail[0]->bank_id; ?>").trigger('change.select2');
                //$("#bank_id").val("<?php echo $bank_payment_detail[0]->bank_id; ?>");
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
             
                $('#bank_details').select2('destroy').empty().select2();
                $('#bank_details').append(data);
                $('#bank_details').val("<?php echo $bank_payment_detail[0]->bank_details; ?>").trigger('change.select2');
                
            },
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        //-------------------------------- AJAX call --------------------------------------------

        //-----------------------  Client ,  Bank  , Vendor
        $("#company_id").change(function(e, json) {

            var company_id = $("#company_id").val();
            if (company_id.length >= 1) {

                //---------------------------  Clients
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


                        $('#client_id').select2('destroy').empty().select2();
                        $('#project_type').select2("val","").trigger('change');
                        $('#bank_details').select2('destroy').empty().select2();
                        $('#project_id').select2('destroy').empty().select2();
                        $('#project_site_id').select2('destroy').empty().select2();

                        $("#client_id").append("<option value='' selected>Select Client</option>");
                        $.each(data, function(index, clients_obj) {
                            if (clients_obj.id == 1) {
                                htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                            }else{
                                htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                            }
                        });
                        $("#client_id").append(htmlStr);
                        if (json) {
                            setTimeout(() => {
                                $('#client_id').val(json.client_id).trigger('change.select2');
                                $('#client_id').select2('destroy').attr("style", "pointer-events: none;");
            
                            }, 2000);
                        }
                    }
                });
                //------------------------------ banks
                $.ajax({
                    url: "{{ route('admin.get_bank_list_cheque')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {

                        $('#bank_id').select2('destroy').empty().select2();
                        $('#bank_id').append(data);
                        if (json) {
                            $('#bank_id').val(json.b_id).trigger('change');
                            //$('#bank_id').change();
                            // $('#bank_id option[value=' + json.b_id + ']').attr('selected', 'selected');
                            // $('#bank_id').change();
                        }
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
                //------------------------------ vendors
                $.ajax({
                    url: "{{ route('admin.get_cash_vendor_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {

                        $('#vendor_id').select2('destroy').empty().select2();
                        $('#vendor_id').append(data);
                        if (json) {
                            $('#vendor_id').val(json.v_id).trigger('change');
                            //$('#vendor_id').change();
                            $('#vendor_id').select2('destroy').attr("style", "pointer-events: none;");
                            // $('#vendor_id option[value=' + json.v_id + ']').attr('selected', 'selected');
                            // $('#vendor_id').change();
                            // $('#vendor_id').attr("style", "pointer-events: none;");
                        }
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });

                $('#bank_details').empty();
                // $('#cheque_number').empty();
            }

        });

        //-----------------------------  Vendor bank
        $("#vendor_id").change(function(e, object) {
            var company_id = $("#company_id").val();
            var vendor_id = $("#vendor_id").val();
            if (company_id >= 1 && vendor_id >= 1) {
            $.ajax({
                url: "{{ route('admin.get_vendor_bank_details')}}",
                type: 'get',
                data: "company_id=" + company_id + "&" + "vendor_id=" + vendor_id,
                success: function(data, textStatus, jQxhr) {

                    $('#bank_details').select2('destroy').empty().select2();
                    $('#bank_details').append(data);
                    if (object) {

                        setTimeout(function() {
                            $('#bank_details').val(object.d_id).trigger('change.select2');
                            $('#bank_details').select2('destroy').attr("style", "pointer-events: none;");
                            // $('#bank_details').change();
                            
                            // $('#bank_details option[value=' + object.d_id + ']').attr('selected', 'selected');
                            // $('#bank_details').change();
                            // $('#bank_details').attr("style", "pointer-events: none;");
                        }, 2000)

                    }
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }

        });

        //-------------------------------------- projects
        $('#client_id,#project_type').change((e, objectData) => {
           
            client_id = $("#client_id").val();
            project_type = $("#project_type").val();
             if (client_id >= 1) {
            $.ajax({
                url: "{{ route('admin.get_client_project_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    client_id: client_id,project_type:project_type
                },
                dataType: "JSON",
                success: function(data) {

                    $('#project_id').select2('destroy').empty().select2();
                    $('#project_site_id').select2('destroy').empty().select2();

                    $("#project_id").append("<option value='' selected>Select Project</option>");
                    $.each(data, function(index, projects_obj) {
                        $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                    });
                    if (objectData) {
                        if (objectData.p_id) {

                            $('#project_id').val(objectData.p_id).trigger('change.select2');
                            $('#project_id').select2('destroy').attr("style", "pointer-events: none;");
                            $('#project_id').trigger('change', [{
                                other_project: objectData.other_project,
                                project_site_id: objectData.project_site_id
                            }]);
                        }
                    }
                    
                }
            });
        }
        });

        //---------------------------- sites
        $("#project_id").change(function(e, objectData) {
            project_id = $("#project_id").val();
            if (project_id >= 1) {
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

                    $('#project_site_id').select2('destroy').empty().select2();
                    $("#project_site_id").append("<option value='' selected>Select Site</option>");
                    $.each(data, function(index, project_site_obj) {
                        $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                    })
                    if (objectData) {
                        $('#project_site_id').val(objectData.project_site_id).trigger('change.select2');
                        $('#project_site_id').select2('destroy').attr("style", "pointer-events: none;");
                    }
                }
            });
        }

            //-----------------------  other project
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
    $("#igst_amount,#cgst_amount,#sgst_amount,#tds_amount").change(function(){
        if($("#igst_amount").val() == ""){
            $("#igst_amount").val("0");
        }
        if($("#cgst_amount").val() == ""){
            $("#cgst_amount").val("0");
        }
        if($("#sgst_amount").val() == ""){
            $("#sgst_amount").val("0");
        }
        if($("#tds_amount").val() == ""){
            $("#tds_amount").val("0");
        }
    });
    $("#amount,#igst_amount,#cgst_amount,#sgst_amount,#tds_amount").on('keyup',function(){
        var amount = $("#amount").val();
        var igst_amount = $("#igst_amount").val();
        if(igst_amount == ""){
            igst_amount = "0";
        }
        var cgst_amount = $("#cgst_amount").val();
        if(cgst_amount == ""){
            cgst_amount = "0";
        }
        var sgst_amount = $("#sgst_amount").val();
        if(sgst_amount == ""){
            sgst_amount = "0";
        }
        var tds_amount = $("#tds_amount").val();
        if(tds_amount == ""){
            tds_amount = "0";
        }

        var main_amount = (parseFloat(amount) + parseFloat(igst_amount) + parseFloat(cgst_amount) + parseFloat(sgst_amount)) - parseFloat(tds_amount)
        if(main_amount > 0){
            $("#payment_amount").text(main_amount);
        }else{
            $("#payment_amount").text("0.00");
        }
    });

    $("#budget_sheet_id").on('change',function(){
        
        var budget_sheet_id = $(this).val();
        $("#entry_code").select2('val', '');

              //get budget sheet entry code
                $.ajax({
                    type : "POST",
                    url : "{{route('admin.get_budget_sheet_entry_code')}}",
                    data : {
                        "_token":"{{csrf_token()}}",
                        'budget_sheet_id' : budget_sheet_id,
                    },
                    dataType : "JSON",
                    success:function(data){
                        // console.log(data);
                        if(data.entry_code){
                            $("#entry_code").select2("readonly", true);
                            $("#entry_code").val(data.entry_code).trigger("change");
                        }else{
                            // if entry code does not exist
                                $.ajax({
                                    type : "POST",
                                    url : "{{route('admin.get_budget_sheet_data')}}",
                                    data : {
                                        "_token":"{{csrf_token()}}",
                                        'budget_sheet_id' : budget_sheet_id,
                                    },
                                    dataType : "JSON",
                                    success:function(data){
                                    if(data){
                                        // console.log(data);
                                        $("#company_id").select2("readonly", true);
                                        $("#company_id").val(data.company_id).trigger("change");
                                        if(data.total_amount > 0){
                                            $("#total_amount").val(data.total_amount).prop("readonly", true);
                                        }

                                        if(data.release_hold_amount_status === "Approved"){
                                            $("#amount").val(data.release_hold_amount);
                                        }else{
                                            $("#amount").val("");
                                        }

                                        setTimeout(function(){
                                    
                                            $("#client_id").select2("readonly", true);
                                            $("#client_id").val(data.client_id).trigger("change.select2");

                                            setTimeout(function(){
                                            
                                                $("#project_type").select2("readonly", true);
                                                $("#project_type").val(data.project_type).trigger("change");

                                                setTimeout(function(){
                                                    $("#project_id").select2("readonly", true);
                                                    $("#project_id").val(data.project_id).trigger("change");

                                                    setTimeout(function(){
                                                        $("#project_site_id").select2("readonly", true);
                                                        $("#project_site_id").val(data.project_site_id).trigger("change");
                                                    }, 1000);
                                                }, 1000);

                                            }, 1000);

                                            $("#vendor_id").select2("readonly", true);
                                            $("#vendor_id").val(data.vendor_id).trigger("change");

                                        }, 1000);

                                    }

                                }
                            });
                    $("#entry_code").select2("readonly", true);
                    $("#entry_code").select2('val', '');
                    // $.toast({
                    //     heading: "Entry Code Information",
                    //     text: "Don't have preview entry code",
                    //     position: 'top-right',
                    //     //loaderBg:'#ff6849',
                    //     icon: 'info',
                    //     hideAfter: 5000,
                    //     textColor: 'white',
                    //     stack: 100
                    // });
                    // $("#entry_code").val("").trigger("change");
                    // $("#entry_code").select2('destroy').empty().select2();
                }
            }
        });
    });
</script>
@endsection
