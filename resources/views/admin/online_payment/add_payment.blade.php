@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Add Online Payment Details</h4>
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
                        <form action="{{ route('admin.insert_online_payment') }}" id="add_payment" method="post" enctype="multipart/form-data">
                            @csrf

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
                                <select class="form-control" name="budget_sheet_id"   id="budget_sheet_id">

                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Main Entry(Select main entry if this is part payment of any old entry)</label>
                                <select class="form-control" onchange="get_payment_data();" name="entry_code" id="entry_code">
                                    <option value="">Select Main Entry</option>
                                    @foreach($main_entry_list as $main_entry)
                                    <option value="{{ $main_entry }}">{{ $main_entry }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Company <span class="error">*</span></label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Transaction Type <span class="error">*</span> </label>
                                <div id="response">
                                    <select class="form-control" id="transaction_type" name="transaction_type">
                                        <option value="">Select Transaction</option>
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="Debit Card">Debit Card</option>
                                        <option value="Netbanking">Netbanking</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="bank_drop">
                                <label>Bank Name <span class="error">*</span> </label>
                                <div id="response">
                                    <select class="form-control" id="bank_id" name="bank_id">
                                        <option value="">Select bank</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" style="display:none;" id="transaction_id_drop">
                                <label>Payment Card <span class="error">*</span> </label>
                                <div id="response">
                                    <select class="form-control" id="transaction_id" name="transaction_id">
                                        <option value="">Select Payment Card</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group ">
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
                            <div class="form-group ">
                                <label>Select Project Site <span class="error">*</span> </label>
                                <select class="form-control" id="project_site_id" name="project_site_id">
                                    <option value="">Select Site</option>
                                </select>
                            </div>
                            <div class="form-group" id="other_cash_txt" style="display:none;">
                                <label>Other Detail <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="other_project_detail" id="other_project_detail" />
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
                                <label>Vendor/Party Bank Details <span class="error">*</span> </label>
                                <!-- <textarea class="form-control" name="bank_details" id="bank_details"></textarea> -->
                                <select class="form-control" id="bank_details" name="bank_details">
                                    <option value="">Select Vendor/Party bank</option>
                                </select>

                            </div>
                            <div class="form-group ">
                                <label>Total Amount (Already Completed Amount: <span id="completed_amount">0.00</span>) <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="total_amount" id="total_amount" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Part Payment Amount (If not part payment then add amount same as above) <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="amount" id="amount" value="" />
                            </div>
                            <div class="form-group ">
                                <label>IGST Amount</label>
                                <input type="number" class="form-control" name="igst_amount" id="igst_amount" value="0" />
                            </div>
                            <div class="form-group ">
                                <label>CGST Amount</label>
                                <input type="number" class="form-control" name="cgst_amount" id="cgst_amount" value="0" />
                            </div>
                            <div class="form-group ">
                                <label>SGST Amount</label>
                                <input type="number" class="form-control" name="sgst_amount" id="sgst_amount" value="0" />
                            </div>
                            <div class="form-group ">
                                <label>TDS Section Type</label>
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
                                <label>TDS Amount</label>
                                <input type="number" class="form-control" name="tds_amount" id="tds_amount" value="0" />
                            </div>
                            <div class="form-group ">
                                <label>Work Detail</label>
                                <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false"></textarea>
                            </div>
                            <div class="form-group ">
                                <label>Transaction UNR Number</label>
                                <input type="text" class="form-control valid" rows="6" name="transation_detail" id="transation_detail" spellcheck="false">
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
                                <label>Payment File (CTRL+click to select multiple files)</label>
                                <input type="file" onchange="get_count(this)" class="form-control" name="online_payment_file[][]" id="online_payment_file" multiple="" />
                                <input type="hidden" name="file_counts[]" id="file_counts" value="0" />
                            </div>
                            <div class="form-group ">
                                <label>Payment Amount : </label><span id="payment_amount" class="text-success">0.00</span>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
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

        }else if(check_val == 'Regular'){
            $('.invoice_no_part').show();
            $('#budget_sheet').hide();
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
        }else{
            $('#budget_sheet').hide();
            $('.invoice_no_part').hide();
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
    function get_count(e) {
        var files = $(e)[0].files;

        $(e).next('input').val(files.length);
    }
    function get_payment_data() {
        $('#completed_amount').text('0.00');
        var entry_code = $('#entry_code').val();
        if (entry_code) {

            $.ajax({
                url: "{{ route('admin.get_online_payment_data') }}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    entry_code: entry_code
                },
                success: function(data) {
                    // console.log(data);

                    if (data.main_entry[0]) {
                        $("#company_id").select2("readonly", true);
                        $("#company_id").val(data.main_entry[0].company_id).trigger("change");
                        if(data.main_entry[0].total_amount > 0){
                            $("#total_amount").val(data.main_entry[0].total_amount).prop("readonly", true);
                        }

                        setTimeout(function(){

                            if(data.main_entry[0].transaction_type == "Netbanking"){
                                $("#transaction_type").val("Netbanking").trigger("change");
                                $("#bank_id").val(data.main_entry[0].bank_id).trigger("change");
                            }else{
                                $("#transaction_type").val(data.main_entry[0].transaction_type).trigger("change");
                                setTimeout(function(){
                                    $("#transaction_id").val(data.main_entry[0].transaction_id).trigger("change");
                                }, 1000);
                            }

                            $("#client_id").select2("readonly", true);
                            $("#client_id").val(data.main_entry[0].client_id).trigger("change.select2");

                        setTimeout(function(){

                            $("#project_type").select2("readonly", true);
                            $("#project_type").val(data.main_entry[0].project_type).trigger("change");

                            setTimeout(function(){
                                $("#project_id").select2("readonly", true);
                                $("#project_id").val(data.main_entry[0].project_id).trigger("change");

                                setTimeout(function(){
                                    $("#project_site_id").select2("readonly", true);
                                    $("#project_site_id").val(data.main_entry[0].project_site_id).trigger("change");
                                }, 1000);
                            }, 1000);

                        }, 1000);

                            $("#vendor_id").select2("readonly", true);
                            $("#vendor_id").val(data.main_entry[0].vendor_id).trigger("change");

                            setTimeout(function(){
                                $("#bank_details").val(data.main_entry[0].bank_details).trigger("change");
                            }, 1000);
                        }, 1000);

                    }

                    $('#note').val(data.main_entry[0].note);
                    $('#total_amount').attr('readonly', true);
                    $('#completed_amount').text(data.total_complete_amount);
                }
            })
        } else {
            // location.reload(true);
        }
    }
    jQuery('#add_payment').validate({
        ignore: [],
        rules: {
            // bank_id: {
            //     required: true,
            // },
            payment_options: {
                required: true
            },  
            project_type: {
                required: true
            },
            transaction_type: {
                required: true,
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
            bank_details: {
                required: true
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
            invoice_no : {
                required: function(element) {
                    if ($("#payment_options").val() == 'Regular') {
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


        $('#project_type').select2();   
        $('#payment_options').select2();
        $('#budget_sheet_id').select2();
        $('#entry_code').select2();
        $('#company_id').select2();
        $('#client_id').select2();
        $('#project_id').select2();
        $('#project_site_id').select2();
        $('#vendor_id').select2();
        $('#bank_details').select2();
        $('#transaction_id').select2();
        $('#transaction_type').select2();

        jQuery('#issue_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
        $("#company_id").change(function() {

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

                        $('#bank_id').select2('destroy').empty().select2();
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

                        $('#vendor_id').select2('destroy').empty().select2();
                        $('#vendor_id').append(data);
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }

        });

        $('#client_id,#project_type').change(() => {
            //project list
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
                        if(projects_obj.project_name != "Other Project")
                            $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                    });


                }
            });
        }
        });

        $("#vendor_id").change(function() {
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
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }

        });

        $("#project_id").change(function() {
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

                }
            });
        }

            var project_id = $("#project_id").val();
            if (project_id == 1) {
                $("#other_project_detail").val("");
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
                    // data: "company_id=" + company_id + "&" + "bank_id=" + bank_id + "&" + "transaction_type=" + transaction_type,
                    data: "transaction_type=" + transaction_type,
                    success: function(data, textStatus, jQxhr) {
                        $('#transaction_id').select2('destroy').empty().select2();
                        $('#transaction_id').append(data);
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
                $('#bank_drop').hide();
                $("#transaction_id_drop").show();
            } else {
                $('#bank_drop').show();
                $("#transaction_id_drop").hide();
            }
        });

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
        // alert(budget_sheet_id);

        //get budget sheet entry code
        $.ajax({
            type : "POST",
            url : "{{route('admin.get_budget_sheet_online_entry_code')}}",
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
                    //if entry code does not exist
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
</script>
@endsection
