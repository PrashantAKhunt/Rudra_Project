@extends('layouts.admin_app')

@section('content')
<?php

use App\Lib\CommonTask;

$common_task = new CommonTask();
?>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Add Budget Sheet</h4>
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
                    <div class="col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.insert_budget_sheet') }}" id="insert_budget_sheet" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Budget Sheet Year</label>
                                        <input type="text" readonly="" class="form-control" value="{{ date('Y') }}" name="budget_sheet_year" id="budget_sheet_year"/>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Budget Sheet Week</label>
                                        <select class="form-control" name="budget_sheet_week" id="budget_sheet_week">
                                            @for($i=date('W');$i<=52;$i++)
                                            <option @if(date('W')==$i) selected="" @endif value="{{ $i }}">{{ 'Week- '.$i.' ('.$common_task->getWeekStartAndEndDate($i,date("Y")).')' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Select Company</label>
                                        <select name="company_id" onchange="get_client_list();get_vendor_list();" required="" id="company_id" class="form-control">
                                            <option value="">Select Company</option>
                                            @foreach($company_list as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!--/span-->

                            </div>
                            <hr>
                            <div class="row">
                                <h3 class="title">Enter Records</h3>
                            </div>
                            <div id="single_data_div">
                                <div id="dynamic_div_0">

                                    <div class="row">
                                        <!--/span-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Select Department</label>
                                                <select name="department_id[]" required="" id="department_id" class="form-control">
                                                    <option value="">Select Department</option>
                                                    @foreach($department_list as $department)
                                                    <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Select Vendor</label>
                                                <select name="vendor_id[]" id="vendor_id" class="form-control">
                                                    <option value="">Select Vendor</option>

                                                </select>
                                            </div>
                                            <!--/span-->
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Schedule Date</label>
                                                <input class="form-control input-daterange-datepicker" type="text" id="schedule_date" name="schedule_date[]" value=""/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Description</label>
                                                <textarea name="description[]" id="description" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Remarks if any</label>
                                                <textarea name="remark_by[]" id="remark_by" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <!--/span-->

                                        <!--/span-->
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Total Amount</label>
                                                <input class="form-control" type="text" id="total_amount" name="total_amount[]" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Mode Of Payment</label>
                                                <select name="mode_of_payment[]" id="mode_of_payment" class="form-control">
                                                    <option value="Bank">Bank</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Credit Card">Credit Card</option>
                                                    <option value="Debit Card">Debit Card</option>
                                                    <option value="Online">Online</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Request Amount</label>
                                                <input type="text" name="request_amount[]" id="request_amount" class="form-control" />
                                            </div>
                                            <!--/span-->
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Select Client</label>
                                                <select class="form-control client_id" required id="client_id" name="client_id[]">
                                                <option value="">Select Client</option>

                                                </select>
                                            </div>
                                        </div>
                                
                                        <div class="col-md-3">
                                        <div class="form-group "> 
                                            <label>Select Project Status</label>
                                            <select class="form-control project_type" name="project_type[]" required id="project_type">
                                                <option value="">Select Project Status</option>
                                                <option value="Current">Current</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>
                                        </div>
                                        <div class="col-md-3 projects">
                                            <div class="form-group project">
                                                <label class="control-label">Select Project</label>
                                                <select name="project_id[]"  class="form-control project_id" onchange="get_sites_list(this);" id="project_id">
                                                    <option value="">Select Project</option>

                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-3 sites">
                                        <div class="form-group ">
                                            <label>Select Project Site</label>
                                                <select class="form-control project_site_id" id="project_site_id" name="project_site_id[]">
                                                    <option value="">Select Site</option>
                                                </select>
                                        </div>
                                        </div>
                                        <!-- span -->
                                        
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Purchase Order Number</label>
                                                    <input class="form-control purchase_order_number" type="text" id="purchase_order_number" name="purchase_order_number[]" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Purchase Order Date</label>
                                                    <input class="form-control purchase_order_date" type="text" id="purchase_order_date" name="purchase_order_date[]" value=""/>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Bill Number</label>
                                                    <input class="form-control bill_number" type="text" id="bill_number" name="bill_number[]" value=""/>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Bill Date</label>
                                                    <input class="form-control bill_date" type="text" id="bill_date" name="bill_date[]" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Multiple File</label>
                                                    <input type="file" onchange="get_count(this)" class="form-control" name="budget_sheet_file[][]" id="budget_sheet_file" multiple="" />
                                                    <input type="hidden" name="file_counts[]" id="file_counts" value="0" />
                                                </div>
                                                <!--/span-->
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Invoice File</label>
                                                    <input type="file" class="form-control" name="invoice_file[]" id="invoice_file" />
                                                </div>
                                                <!--/span-->
                                            </div>
                                        </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <button type="button" onclick="remove_more(this)" class="btn btn-danger remove_btn"><i class="fa fa-trash"></i> Remove</button>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                            <div id="dynamic_data_div">

                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" onclick="add_more();" id="add_button" class="btn btn-primary"><i class="fa fa-plus"></i> Add More</button>
                                </div>
                            </div>

                            <br>
                            <button type="button" onclick="submit_frm();" class="btn btn-success save_budget_sheet">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.budget_sheet') }}'" class="btn btn-default">Cancel</button>
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
var check_PON = 0;
var check_BN = 0;
    function get_count(e){
        var files = $(e)[0].files;

        $(e).next('input').val(files.length);
    }

    function get_client_list() {
        htmlStr = '';
                 $.ajax({
                    url: "{{ route('admin.get_company_client_list') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        company_id: $('#company_id').val()
                    },
                    dataType: "JSON",
                    success: function(data) {

                        $('select[name="client_id[]"]').empty();
                        $('select[name="client_id[]"]').append("<option value='' selected>Select Client</option>");
                        $('.project_type').val('');
                        $('.project_id').empty().append("<option value='' selected>Select Project</option>");
                        $('.project_site_id').empty().append("<option value='' selected>Select Site</option>");
                        
                        $.each(data, function(index, clients_obj) {

                            if (clients_obj.id == 1) {
                                // htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                            }else{
                                htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                            }

                            //$("#client_id").append('<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>');
                        });
                        $('select[name="client_id[]"]').append(htmlStr);

                    }
                });
    }

    $("body").on('change','.client_id',function(e2) {

        $(e2.target).parent().parent().parent().find('select[name="project_type[]"]').val('');
        $(e2.target).parent().parent().parent().find('select[name="project_id[]"]').empty().append("<option value='' selected>Select Project</option>");
        $(e2.target).parent().parent().parent().find('select[name="project_site_id[]"]').empty().append("<option value='' selected>Select Site</option>");
       
    });

    // function get_project_list(e) {
    $("body").on('change','.project_type',function(el) {
        el.preventDefault();
        var project_type = $(el.target).val();
        var client_id = $(el.target).parent().parent().parent().find('select[name="client_id[]"]').val();

        var project_box = $(el.target).parent().parent().parent().find('select[name="project_id[]"]');
        var sites_box = $(el.target).parent().parent().parent().find('select[name="project_site_id[]"]');
        
        if (client_id > 0) {
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

                    
                    project_box.empty();
                    project_box.append("<option value='' selected>Select Project</option>");
                    sites_box.empty();
                    sites_box.append("<option value='' selected>Select Site</option>");

                    $.each(data, function(index, projects_obj) {

                        if(projects_obj.project_name != "Other Project")
                        project_box.append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');

                    });
                }
            });
        }
            

    });

    function get_sites_list(e) {

        var project_id = $(e).val();

        var sites_box = $(e).parent().parent().parent().find('select[name="project_site_id[]"]');

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

                    sites_box.empty();
                    sites_box.append("<option value='' selected>Select Site</option>");

                    $.each(data, function(index, project_site_obj) {
                        if(project_site_obj.site_name != "Other")
                            sites_box.append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                    })

                }
            });

    }


    function get_vendor_list() {
        $.ajax({
            url: "{{ route('admin.get_vendorlist_by_company') }}",
            type: "post",
            dataType: "html",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {company_id: $('#company_id').val(), },
            success: function (data) {
                
                    $('select[name="vendor_id[]"]').html(data);
            }
        });
    }



    function submit_frm() {

        if (!$('#budget_sheet_year').val() || $('#budget_sheet_year').val() == "") {
            error_display('Budget sheet year is required.');
            $(this).focus();
            return false;
        }


        if (!$('#budget_sheet_week').val() || $('#budget_sheet_week').val() == "") {
            error_display('Budget sheet week is required.');
            $(this).focus();
            return false;
        }


        if (!$('#company_id').val() || $('#company_id').val() == "") {
            error_display('Company is required.');
            $(this).focus();
            return false;
        }

        $('select[name="department_id[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Department is required.');
                $(this).focus();
                return false;
            }
        });
        $('select[name="vendor_id[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Vendor is required.');
                $(this).focus();
                return false;
            }
        });
        $('textarea[name="description[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Description is required.');
                $(this).focus();
                return false;
            }
        });
        $('input[name="request_amount[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "" || isNaN($(this).val())) {
                error_display('Please enter valid request amount');
                $(this).focus();
                return false;
            }
        });
        $('input[name="schedule_date[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Schedule date is required.');
                $(this).focus();
                return false;
            }
        });
        $('input[name="mode_of_payment[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Mode of payment is required.');
                $(this).focus();
                return false;
            }
        });

        $('select[name="client_id[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Client is required.');
                $(this).focus();
                return false;
            }
        });

        $('select[name="project_type[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Project Status is required.');
                $(this).focus();
                return false;
            }
        });

        $('select[name="project_id[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Project is required.');
                $(this).focus();
                return false;
            }
        });

        $('select[name="project_site_id[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Site is required.');
                $(this).focus();
                return false;
            }
        });

        $('input[name="purchase_order_number[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Purchase order number is required.');
                $(this).focus();
                return false;
            }
        });

        $('input[name="purchase_order_date[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Purchase order date is required.');
                $(this).focus();
                return false;
            }
        });

        $('input[name="bill_number[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Bill number is required.');
                $(this).focus();
                return false;
            }
        });

        $('input[name="bill_date[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "") {
                error_display('Bill date is required.');
                $(this).focus();
                return false;
            }
        });


        $('input[name="total_amount[]"]').each(function (i, e) {

            if (!$(this).val() || $(this).val() == "" || isNaN($(this).val())) {
                error_display('Please enter valid total amount.');
                $(this).focus();
                return false;
            }
            if ($('input[name="total_amount[]"]').length === i + 1) {
                $('#insert_budget_sheet').submit();
            }
        });

    }
    function error_display(error_msg) {
        $.toast({
            heading: "Validation Error",
            text: error_msg,
            position: 'top-right',
            //loaderBg:'#ff6849',
            icon: 'error',
            hideAfter: 5000,
            textColor: 'white',
            stack: 100
        });
    }
    function add_more() {
        var old_html = $('#single_data_div').html();
        var new_html = old_html.replace('remove_btn', 'btnremove_show');
        //alert(new_html);
        $('#dynamic_data_div').append(new_html);
        $('.input-daterange-datepicker').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('.btnremove_show').show();
        //$('form#insert_budget_sheet').validate();
        //jQuery("#insert_budget_sheet").validate().form();
        intiDatepicker();
    }
    function remove_more(e) {
        $(e).parent().parent().parent().remove();
    }
    /*jQuery('#insert_budget_sheet').validate({
     ignore: [],
     rules: {
     'budget_sheet_year[]': {
     required: true,
     },
     'budget_sheet_week[]': {
     required: true,
     },
     'department_id[]': {
     required: true,
     },
     'vendor_id[]': {
     required: true,
     },
     'description[]': {
     required: true,
     },
     'remark_by[]': {
     required: true,
     },
     'request_amount[]': {
     required: true,
     },
     'schedule_date[]': {
     required: true,
     },

     'mode_of_payment[]': {
     required: true,
     },
     'project_id[]': {
     required: true,
     },
     'total_amount[]': {
     required: true,
     },
     }
     });*/
    $(document).ready(function () {
        $('.input-daterange-datepicker').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('.remove_btn').hide();
        intiDatepicker();
    });

    function intiDatepicker(){
        jQuery('.purchase_order_date,.bill_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
        });
    }

    $("body").on('change','.purchase_order_number',function() {
        var purchase_order_number = $(this).val();
        $.ajax({
            type : "POST",
            url : "{{url('check_purchase_order_number')}}",
            data : {
                "_token" : "{{csrf_token()}}",
                purchase_order_number : purchase_order_number,
            },
            success:function(data){
                if(data ===  "error"){
                    error_display('Purchase order number exits use another.');
                    $(".save_budget_sheet").attr("disabled", true);
                    check_PON = 1;
                }else{
                    check_PON = 0;
                    if(check_BN == 0){
                        $(".save_budget_sheet").attr("disabled", false);
                    }else{
                        $(".save_budget_sheet").attr("disabled", true);
                    }


                }
                /* $('input[name="purchase_order_number[]"]').each(function (i, e) {
                    if(purchase_order_number == $(this).val()){
                        error_display('Purchase order number exits user another.');
                        $(this).focus();
                        return false;
                    }
                }); */
            }
        });
    });
    $("body").on('change','.bill_number',function() {
        var bill_number = $(this).val();
        $.ajax({
            type : "POST",
            url : "{{url('check_bill_number')}}",
            data : {
                "_token" : "{{csrf_token()}}",
                bill_number : bill_number,
            },
            success:function(data){
                if(data ===  "error"){
                    error_display('Bill number exits use another.');
                    $(".save_budget_sheet").attr("disabled", true);
                    check_BN = 1;
                }else{
                    check_BN = 0;
                    if(check_PON == 0){
                        $(".save_budget_sheet").attr("disabled", false);
                    }else{
                        $(".save_budget_sheet").attr("disabled", true);
                    }
                }
            }
        });
    });
</script>
@endsection
