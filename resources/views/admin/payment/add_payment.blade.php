@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Add Bank Payment Details</h4>
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
                        <form action="{{ route('admin.insert_payment') }}" id="add_payment" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="tender_id" value="{{(count($tender_data)) ? $tender_data['tender_id'] : ""}}">
                            <input type="hidden" name="tender_type" id="tender_type" value="{{(count($tender_data)) ? $tender_data['tender_type'] : ""}}">
                            <input type="hidden" name="tender_company_id" id="tender_company_id" value="{{(count($tender_data)) ? $tender_data['company_id'] : ""}}">
                            <div class="form-group ">
                                <label>Payment Options <span class="error">*</span> </label>
                                <select class="form-control" onchange="get_budget_sheet();" name="payment_options" id="payment_options">
                                <option value="" selected>Select Option</option>
                                    <option value="Budget Sheet">Budget Sheet</option>
                                    <option value="Emergency Option">Emergency Option</option>
                                    <option value="Regular">Regular</option>
                                </select>
                            </div>

                            <div class="form-group" style='display:none;' id="budget_sheet">
                                <label>Budget Sheets <span class="error">*</span> </label>
                                <select class="form-control" name="budget_sheet_id"  id="budget_sheet_id">

                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Main Entry(Select main entry if this is part payment of any old entry) <span class="error">*</span> </label>
                                <select class="form-control" onchange="get_payment_data();" name="entry_code" id="entry_code">
                                    <option value="">Select Main Entry</option>
                                    @foreach($main_entry_list as $main_entry)
                                    <option value="{{ $main_entry }}">{{ $main_entry }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Select Company <span class="error">*</span> </label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}" {{(count($tender_data) && $tender_data['company_id'] == $company_list_data->id) ? "selected" : ""}}>{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Client <span class="error">*</span> </label>
                                <select class="form-control" id="client_id" name="client_id">
                                    <option value="">Select Client</option>
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Project Status <span class="error">*</span> </label>
                                <select class="form-control" name="project_type" id="project_type">
                                    <option value="">Select Project Status</option>
                                    <option value="Current">Current</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Project <span class="error">*</span> </label>
                                <select class="form-control" id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date <span class="error">*</span> </label>
                                <input type="date" required class="form-control" name="created_at_date" id="created_at_date" />
                            </div>
                            <div class="form-group" id="other_cash_txt" style="display:none;">
                                <label>Other Detail <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="other_project_detail" id="other_project_detail" />
                            </div>
                            <div class="form-group ">
                                <label>Select Project Site <span class="error">*</span> </label>
                                <select class="form-control" id="project_site_id" name="project_site_id">
                                    <option value="">Select Site</option>
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Vendor Name <span class="error">*</span> </label>
                                <div id="vendor_response">
                                    <select class="form-control" id='vendor_id' name='vendor_id'>
                                        <option value="">Select vendor</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group ">
                                <label>Bank Name <span class="error">*</span> </label>
                                <div id="response">
                                    <select class="form-control" id="bank_id" name="bank_id">
                                        <option value="">Select bank</option>
                                    </select>
                                </div>
                            </div>


                            <div class="form-group ">
                                <label>Vendor/Party Bank Details <span class="error">*</span> </label>

                                <select class="form-control" id="bank_details" name="bank_details">
                                    <option value="">Select Vendor/Party bank</option>
                                </select>

                            </div>

                           <!--  <div class="form-group ">
                                <label>Cheque</label>
                                <select name="cheque_number" required class="form-control" id="cheque_number">
                                    <option value="">Select cheque</option>
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Cheque Issue Date</label>
                                <input type="text" class="form-control" required name="issue_date" id="issue_date" value="" />
                            </div> -->

                            <div class="form-group ">
                                <label>Total Amount (Already Completed Amount: <span id="completed_amount">0.00</span>) <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="total_amount" id="total_amount" value="{{(count($tender_data)) ? $tender_data['payment_amount'] : ""}}" />
                            </div>

                            <div class="form-group ">
                                <label>Part Payment Amount (If not part payment then add amount same as above) <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="amount" id="amount" value="{{(count($tender_data)) ? $tender_data['payment_amount'] : ""}}" />
                            </div>
                            <div class="form-group ">
                                <label>Work Detail <span class="error">*</span> </label>
                                <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false"></textarea>
                            </div>

                            <!-- <div class="form-group ">
                                <label>Bank Payment File</label>
                                <input type="file" name="payment_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/msexcel
                                       ,application/pdf,image/jpg,image/jpeg,image/png" id="payment_file" class="form-control" />
                            </div> -->
                                
                            <div class="form-group ">
                                <label>MICR Code </label>
                                <input type="text" class="form-control" name="micr_code" id="micr_code"/>
                            </div>
                            <div class="form-group ">
                                <label>SWIFT Code </label>
                                <input type="text" class="form-control" name="swift_code" id="swift_code"/>
                            </div>
                            <div class="form-group ">
                                <label>IGST Amount <span class="error">*</span> </label>
                                <input type="number" class="form-control" name="igst_amount" id="igst_amount" value="0" />
                            </div>
                            <div class="form-group ">
                                <label>CGST Amount <span class="error">*</span> </label>
                                <input type="number" class="form-control" name="cgst_amount" id="cgst_amount" value="0" />
                            </div>
                            <div class="form-group ">
                                <label>SGST Amount <span class="error">*</span> </label>
                                <input type="number" class="form-control" name="sgst_amount" id="sgst_amount" value="0" />
                            </div>
                            <div class="form-group ">
                                <label>TDS Section Type <span class="error">*</span> </label>
                                <select name="section_type_id" id="section_type_id" class="form-control">
                                    <option value="">Select TDS Section Type</option>
                                    @if (count($section_type))
                                        @foreach ($section_type as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['section_type']}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>TDS Amount <span class="error">*</span> </label>
                                <input type="number" class="form-control" name="tds_amount" id="tds_amount" value="0" />
                            </div>

                            <div class="form-group invoice_no_part" style="display: none;">
                                <label>Invoice Number</label>
                                <input type="text" name="invoice_no" id="invoice_no" class="form-control" />
                            </div>
                            <div class="form-group invoice_no_part" style="display: none;">
                                <label>Invoice File</label>
                                <input type="file" name="invoice_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/msexcel
                                       ,application/pdf,image/jpg,image/jpeg,image/png" id="invoice_file" class="form-control" />
                            </div>
                            <div class="form-group ">
                                <label>Payment Method <span class="error">*</span> </label>
                                    <select class="form-control" id="payment_method" name="payment_method">
                                        <option value="">Select Payment Method</option>
                                        <option value="Cheque/Rtgs">Cheque/Rtgs</option>
                                        <option value="DD">DD</option>
                                    </select>
                            </div>
                            <div class="form-group ">
                                <label>Multiple Payment files <span class="error">*</span> </label>
                                <input type="file" name="bank_payment_files[]" multiple="" id="bank_payment_files" class="form-control" />
                            </div>
                            <div class="form-group ">
                                <label>Payment Amount : </label><span id="payment_amount" class="text-success">0.00</span>
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

    function get_budget_sheet() {
        var check_val = $('#payment_options').val();
        if (check_val == 'Budget Sheet') {

            $('#budget_sheet').show();
            $('.invoice_no_part').hide();
            $.ajax({
                url: "{{ route('admin.getBudgetSheets') }}",
                type: "get",
                dataType: "JSON",
                success: function(data) {

                    var items= data.data.budget_sheets;
                    $('#budget_sheet_id').select2('destroy').empty().select2();
                    $.each(items, function(index, object) {
                        $("#budget_sheet_id").append('<option value="' + object.id + '">' + object.budhet_sheet_no + '</option>');
                    });

                }
            });

        }else if(check_val == "Regular"){
            $('#budget_sheet').hide();
            $('.invoice_no_part').show();
            $("#company_id").select2("readonly", false);
            if($("#tender_company_id").val() == ""){
                $("#company_id").val("").trigger("change");
            }else{
                $("#company_id").val($("#company_id").val()).trigger("change");
            }

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
            $("#total_amount").prop("readonly", false);
            // $("#amount").val("");

            $("#entry_code").select2("readonly", false);
            // $("#entry_code").val("").trigger("change");
            // $("#entry_code").select2('destroy').select2();
        }
        else{
            $('#budget_sheet').hide();
            $('.invoice_no_part').hide();
            $("#company_id").select2("readonly", false);
            if($("#tender_company_id").val() == ""){
                $("#company_id").val("").trigger("change");
            }else{
                $("#company_id").val($("#company_id").val()).trigger("change");
            }
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
            $("#total_amount").prop("readonly", false);
            // $("#amount").val("");

            $("#entry_code").select2("readonly", false);
            // $("#entry_code").val("").trigger("change");
            // $("#entry_code").select2('destroy').select2();
        }

    }
    function get_payment_data() {
        $('#completed_amount').text('0.00');
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
                    $('#company_id').val(data.main_entry[0].company_id).trigger('change.select2');

                    if (data.main_entry[0].company_id) {

                        $('#company_id').trigger('change', [{
                            client_id: data.main_entry[0].client_id,
                            b_id: data.main_entry[0].bank_id,
                            v_id: data.main_entry[0].vendor_id,
                            d_id: data.main_entry[0].bank_details,
                            p_id: data.main_entry[0].project_id,
                            project_type: data.main_entry[0].project_type,
                            other_project: data.main_entry[0].other_project_detail,
                            project_site_id: data.main_entry[0].project_site_id
                        }]);
                    }
                    /* if (data.main_entry[0].client_id) {
                        $('#client_id').trigger('change', [{
                            p_id: data.main_entry[0].project_id,
                            other_project: data.main_entry[0].other_project_detail,
                            project_site_id: data.main_entry[0].project_site_id
                        }]);
                    } */
                    /* if (data.main_entry[0].vendor_id) {

                        $('#vendor_id').trigger('change', [{
                            d_id: data.main_entry[0].bank_details
                        }]);
                    } */

                    $('#note').val(data.main_entry[0].note);
                    $('#company_id').select2('destroy').attr("style", "pointer-events: none;");
                    $('#total_amount').attr('readonly', true);
                    $('#completed_amount').text(data.total_complete_amount);
                }
            })
        } else {
            //location.reload(true);
        }
    }
    jQuery('#add_payment').validate({
        ignore: [],
        rules: {
            bank_id: {
                required: true,
            },
            project_type: {
                required:true
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
            payment_method:{
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
        },
        submitHandler: function(form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });
    $(document).ready(function() {
        if($("#company_id").val()){
            setTimeout(function() {
                $("#company_id").trigger('change');
            }, 1000)
            $("#amount").trigger('keyup');
        }

        $('#payment_options').select2();
        $('#budget_sheet_id').select2();
        $('#project_type').select2();
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
        //----------  Client ,  Bank  , Vendor
        $("#company_id").change(function(e, json) {

            var company_id = $("#company_id").val();
            if (company_id.length >= 1) {

                //---------------------------------------  Clients list:
                htmlStr ='';
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
                        $('#bank_details').select2('destroy').empty().select2();
                        $('#project_type').select2("val","").trigger('change');
                        $('#project_id').select2('destroy').empty().select2();
                        $('#project_site_id').select2('destroy').empty().select2();


                        $("#client_id").append("<option value='' selected>Select Client</option>");
                        $.each(data, function(index, clients_obj) {
                            if (clients_obj.id == 1) {
                                // htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                            }else{
                                htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                            }
                        });
                        $("#client_id").append(htmlStr);
                        if (json) {

                            $('#client_id').val(json.client_id).trigger('change.select2');
                            $('#client_id').select2('destroy').attr("style", "pointer-events: none;");

                            $('#project_type').val(json.project_type).trigger('change.select2');
                            $("#project_type").select2("readonly", true);


                            setTimeout(() => {
                                $('#client_id').trigger('change', json);
                            }, 1000)

                        }
                    }
                });

                //------------------------------------- bank list
                $.ajax({
                    url: "{{ route('admin.get_bank_list_cheque')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {

                        $('#bank_id').select2('destroy').empty().select2();
                        $("#bank_id").append("<option value='' selected>Select Bank</option>");
                        $('#bank_id').append(data);
                        if (json) {
                            //$('#bank_id option[value=' + json.b_id + ']').attr('selected', 'selected');
                            $('#bank_id').val(json.b_id).trigger('change');
                            //$('#bank_id').change();
                        }

                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
                //------------------------------------  vendor list
                $.ajax({
                    url: "{{ route('admin.get_cash_vendor_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {
                        $('#vendor_id').select2('destroy').empty().select2();
                        $('#vendor_id').append(data);
                        if (json) {

                            $('#vendor_id').val(json.v_id).trigger('change',json);
                            $('#vendor_id').select2('destroy').attr("style", "pointer-events: none;");
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
        //---------------------------------- Project
        $('#client_id,#project_type').change((e, objectData) => {

            //project list
            client_id = $("#client_id").val();
            project_type = $("#project_type").val();
            if (client_id >= 1) {
                $.ajax({
                    url: "{{ route('admin.get_client_project_list') }}",
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {client_id: client_id,project_type:project_type},
                    dataType: "JSON",
                    success: function(data) {

                        $('#project_id').select2('destroy').empty().select2();
                        $('#project_site_id').select2('destroy').empty().select2();

                        $("#project_id").append("<option value='' selected>Select Project</option>");
                        $.each(data, function(index, projects_obj) {
                            if(projects_obj.project_name != "Other Project")
                                $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                        });
                        if (objectData) {

                            setTimeout(() => {

                                $('#project_id').val(objectData.p_id).trigger('change.select2');
                                $('#project_id').select2('destroy').attr("style", "pointer-events: none;");
                                $('#project_id').trigger('change', [{
                                    other_project: objectData.other_project,
                                    project_site_id: objectData.project_site_id
                                }]);
                            }, 2000);

                        }
                    }
                });
            }
        });

        //-------------------------------- Vendor bank
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
                            }, 2000)
                        }
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }
        });

        //--------------------------------------projects sites
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
                            if(project_site_obj.site_name != "Other")
                                $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                        })
                        if (objectData) {

                            //$('#project_site_id option[value=' + objectData.project_site_id + ']').attr('selected', 'selected');
                            $('#project_site_id').val(objectData.project_site_id).trigger('change.select2');
                            $('#project_site_id').select2('destroy').attr("style", "pointer-events: none;");
                        }
                    }
                });
            }
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

    });

    $("#budget_sheet_id").on('change',function(){
        var budget_sheet_id = $(this).val();
        $("#entry_code").select2('val', '');
        // get budget sheet data


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


                                    setTimeout(() => {
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
                    // ------------------------------------------------------
                    $("#entry_code").select2("readonly", true);
                    $("#entry_code").select2('val', '');
                    $.toast({
                        heading: "Entry Code Information",
                        text: "Don't have preview entry code",
                        position: 'top-right',
                        //loaderBg:'#ff6849',
                        icon: 'info',
                        hideAfter: 5000,
                        textColor: 'white',
                        stack: 100
                    });
                    // $("#entry_code").val("").trigger("change");
                    // $("#entry_code").select2('destroy').empty().select2();
                }
            }
        });
    });
    /* ---------------------------------------New Functions-------------------------------------------------- */
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
</script>
@endsection
