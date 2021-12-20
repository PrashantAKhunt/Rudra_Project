@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Add Cash Payment</h4>
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
                        <form action="{{ route('admin.insert_cash_payment') }}" id="insert_cash_payment" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group ">
                                <label>Payment Options <span class="error">*</span> </label>
                                <select class="form-control" onchange="get_budget_sheet();" name="payment_options" id="payment_options">
                                <option value="" selected>Select Option</option>
                                    <option value="Budget Sheet">Budget Sheet</option>
                                    <option value="Emergency Option">Emergency Option</option>
                                </select>
                            </div>

                            <div class="form-group" style='display:none;' id="budget_sheet">
                                <label>Budget Sheets <span class="error">*</span> </label>
                                <select class="form-control" name="budget_sheet_id"  id="budget_sheet_id">

                                </select>
                            </div>

                            <div class="form-group">
                                <label>Select Company <span class="error">*</span> </label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
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

                            <div class="form-group" id="other_cash_txt" style="display:none;">
                                <label>Other Project Detailv <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="other_cash_detail" id="other_cash_detail"/>
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
                                <label>Requested By <span class="error">*</span> </label>
                                <div id="vendor_response">
                                    <select class="form-control" id='requested_by' name='requested_by'>
                                        <option value="">Select Requested By</option>
                                        @if (count($users))
                                            @foreach ($users as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label>Expence Done By <span class="error">*</span> </label>
                                <div id="vendor_response">
                                    <select class="form-control" id='expence_done_by' name='expence_done_by'>
                                        <option value="">Select Expence Done By</option>
                                        @if (count($users))
                                            @foreach ($users as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label>Payment Title <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="title" id="title" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Amount <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="amount" id="amount" value="" />
                            </div>

                            <div class="form-group ">
                                <label>Payment Note</label>
                                <textarea class="form-control" name="note" id="note"></textarea>
                            </div>
                            <div class="form-group ">
                                <label>Vendor Invoice No.</label>
                                <input type="text" class="form-control" name="vender_invoice_no" id="vender_invoice_no" value="" />
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
                                <label>Cash Payment File</label>
                                <input type="file" name="payment_file[]" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/msexcel
                                 ,application/pdf,image/jpg,image/jpeg,image/png" id="payment_file" class="form-control" multiple/>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.cash_payment') }}'" class="btn btn-default">Cancel</button>
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

        }else{
            $('#budget_sheet').hide();
        }

    }
    jQuery('#insert_cash_payment').validate({
        ignore: [],
        rules: {
            title: {
                required: true,
            },
            payment_options: {
                required: true
            },
            project_type: {
                required:true
            },
            project_id: {
                required: true,
            },
            company_id: {
                required: true,
            },
            client_id: {
                required: true
            },
            project_site_id: {
                required: true
            },
            amount: {
                required: true,
                number:true
            },
            vendor_id:{
                required:true
            },
            payment_note:{
                required:true
            },
            requested_by:{
                required:true
            },
            expence_done_by:{
                required:true
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
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });
    $(document).ready(function () {

        $('#payment_options').select2();
        $('#budget_sheet_id,#requested_by,#expence_done_by').select2();
        $('#company_id').select2();
        $('#project_type').select2();
        $('#client_id').select2();
        $('#project_id').select2();
        $('#project_site_id').select2();
        $('#vendor_id').select2();

        //----------------------------- Client , bank , vendor
        $("#company_id").change(function () {

            var company_id = $("#company_id").val();
            if (company_id.length >= 1)
            {
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

                        });
                        $("#client_id").append(htmlStr);


                    }
                });


                $.ajax({
                    url: "{{ route('admin.get_cash_vendor_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function (data, textStatus, jQxhr) {

                        $('#vendor_id').select2('destroy').empty().select2();
                        $('#vendor_id').append(data);
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }

        });

        //------------------------------- project
        $('#client_id,#project_type').change(() => {

            client_id = $("#client_id").val();
            project_type = $("#project_type").val();

            if (client_id >= 1)
            {
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
                    $('#project_site_id').select2('destroy').empty().select2();
                    $("#project_id").append("<option value='' selected>Select Project</option>");

                    $.each(data, function(index, projects_obj) {

                        if(projects_obj.project_name != "Other Project"){
                            $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                        }


                    });
                }
            });
        }
        });

        //------------------------------- site
        $("#project_id").change(function () {
            var project_id = $("#project_id").val();
            if (project_id >= 1)
            {
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
                        if(project_site_obj.site_name != "Other"){
                            $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                        }
                    })

                }
            });
        }
            if (project_id == 1) {
                $("#other_cash_detail").val("");
                $("#other_cash_txt").show();
            } else {
                $("#other_cash_txt").hide();
            }
        });

    });
    $("#igst_amount,#cgst_amount,#sgst_amount").change(function(){
        if($("#igst_amount").val() == ""){
            $("#igst_amount").val(0);
        }
        if($("#cgst_amount").val() == ""){
            $("#cgst_amount").val(0);
        }
        if($("#sgst_amount").val() == ""){
            $("#sgst_amount").val(0);
        }
    });
</script>
@endsection
