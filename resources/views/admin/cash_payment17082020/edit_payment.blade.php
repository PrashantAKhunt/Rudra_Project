@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Edit Cash Payment</h4>
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
                        <form action="{{ route('admin.update_cash_payment') }}" id="update_cash_payment" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $cash_payment_detail[0]->id; ?>" />

                            <div class="form-group ">
                                <label>Payment Options</label>
                                <select class="form-control" onchange="get_budget_sheet();" name="payment_options" id="payment_options">

                                        <option <?php if ($cash_payment_detail[0]->payment_options == "Budget Sheet") { ?> selected <?php } ?> value="Budget Sheet">Budget Sheet</option>
                                        <option <?php if ($cash_payment_detail[0]->payment_options == "Emergency Option") { ?> selected <?php } ?> value="Emergency Option">Emergency Option</option>


                                </select>
                            </div>

                            <div class="form-group" style='display:none;' id="budget_sheet">
                                <label>Budget Sheets</label>
                                <select class="form-control" name="budget_sheet_id"  id="budget_sheet_id">

                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Company</label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}" <?php echo ($cash_payment_detail[0]->company_id == $company_list_data->id) ? "selected='selected'" : '' ?> >{{ $company_list_data->company_name }}</option>
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
                                <label>Other Project Detail</label>
                                <input type="text" class="form-control" name="other_cash_detail" id="other_cash_detail" value="<?php echo $cash_payment_detail[0]->other_cash_detail; ?>" />
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
                                <label>Requested By</label>
                                <div id="vendor_response">
                                    <select class="form-control" id='requested_by' name='requested_by'>
                                        <option value="">Select Requested By</option>
                                        @if (count($users))
                                            @foreach ($users as $key => $value)
                                                <option value="{{$key}}" @if($cash_payment_detail[0]->requested_by == $key) selected @endif >{{$value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label>Expence Done By</label>
                                <div id="vendor_response">
                                    <select class="form-control" id='expence_done_by' name='expence_done_by'>
                                        <option value="">Select Expence Done By</option>
                                        @if (count($users))
                                            @foreach ($users as $key => $value)
                                                <option value="{{$key}}" @if($cash_payment_detail[0]->expence_done_by == $key) selected @endif>{{$value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label>Payment Title</label>
                                <input type="text" class="form-control" name="title" id="title" value="<?php echo $cash_payment_detail[0]->title; ?>" />
                            </div>
                            <div class="form-group ">
                                <label>Amount</label>
                                <input type="textarea" class="form-control" name="amount" id="amount" value="<?php echo $cash_payment_detail[0]->amount; ?>" />
                            </div>
                            <div class="form-group ">
                                <label>Payment Note</label>
                                <textarea class="form-control" name="note" id="note">{{ $cash_payment_detail[0]->note }}</textarea>
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
    jQuery('#update_cash_payment').validate({
        ignore: [],
        rules: {
            title: {
                required: true,
            },
            payment_options: {
                required: true
            },
            amount: {
                required: true,
                number: true
            },
            vendor_id: {
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
            requested_by: {
                required: true
            },
            expence_done_by: {
                required: true
            },

        }
    });
    $(document).ready(function () {

        $('#payment_options').select2();
        $('#budget_sheet_id').select2();
        $('#company_id').select2();
        $('#client_id').select2();
        $('#project_id').select2();
        $('#project_site_id').select2();
        $('#vendor_id').select2();
        $('#requested_by,#expence_done_by').select2();
        //=======================================================Budget sheet ===================

        var check_val = "<?php echo $cash_payment_detail[0]->payment_options; ?>";
        if (check_val == 'Budget Sheet') {
            $('#budget_sheet').show();
            $.ajax({
                    url: "{{ route('admin.getBudgetSheets') }}",
                    type: "get",
                    dataType: "JSON",
                    success: function(data) {

                        var items= data.data.budget_sheets;
                        $('#project_id').select2('destroy').empty().select2();
                        $.each(items, function(index, object) {
                            $("#budget_sheet_id").append('<option value="' + object.id + '">' + object.budhet_sheet_no + '</option>');
                        });

                        $("#budget_sheet_id").val("<?php echo $cash_payment_detail[0]->budget_sheet_id; ?>").trigger('change.select2');
                    }
                });

        }

        //=======================================================Budget sheet ===================
        var company_id = $("#company_id").val();
        //------------------------------------------------Clients list:
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
                    $("#client_id").val("<?php echo $cash_payment_detail[0]->client_id; ?>").trigger('change.select2');
                }, 1000);


            }
        });

        //---------------------------- Project
        client_id = "<?php echo $cash_payment_detail[0]->client_id; ?>";
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

                $('#project_id').select2('destroy').empty().select2();
                $("#project_id").append("<option value='' selected>Select Project</option>");
                $.each(data, function(index, projects_obj) {
                    $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                });
               // setTimeout(() => {

                    $("#project_id").val("<?php echo $cash_payment_detail[0]->project_id; ?>").trigger('change.select2');
                    var other_project = "<?php echo $cash_payment_detail[0]->project_id; ?>";
                    if (other_project == 1) {
                        $("#other_cash_txt").show();
                    }
                //}, 1000);
            }
        });

        //----------------------------- site
        project_id = client_id = "<?php echo $cash_payment_detail[0]->project_id; ?>";
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
                    $("#project_site_id").val("<?php echo $cash_payment_detail[0]->project_site_id; ?>").trigger('change.select2');
                }, 1000);
            }
        });


        //-------------------------- Vendor
        $.ajax({
            url: "{{ route('admin.get_cash_vendor_list')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function (data, textStatus, jQxhr) {

                $('#vendor_id').select2('destroy').empty().select2();
                $('#vendor_id').append(data);
                $("#vendor_id").val("<?php echo $cash_payment_detail[0]->vendor_id; ?>").trigger('change.select2');
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        $("#company_id").change(function () {    //clients , vendor

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
                        $('#project_id').select2('destroy').empty().select2();
                        $('#project_site_id').select2('destroy').empty().select2();

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

                // $.ajax({
                //     url: "{{ route('admin.get_cash_project_list')}}",
                //     type: 'get',
                //     data: "company_id=" + company_id,
                //     success: function (data, textStatus, jQxhr) {
                //         $('#project_id').empty();
                //         $('#project_id').append(data);
                //     },
                //     error: function (jqXhr, textStatus, errorThrown) {
                //         console.log(errorThrown);
                //     }
                // });

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

        $('#client_id').change(() => {        //projects
            //project list
            client_id = $("#client_id").val();
            if (client_id >= 1)
            {
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

                    $('#project_id').select2('destroy').empty().select2();
                    $('#project_site_id').select2('destroy').empty().select2();

                    $("#project_id").append("<option value='' selected>Select Project</option>");
                    $.each(data, function(index, projects_obj) {
                        $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                    });

                }
            });
        }
        });

        $("#project_id").change(function () {      //sites
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
                        $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                    })
                    // if (objectData) {
                    //     $('#project_site_id option[value=' + objectData.project_site_id + ']').attr('selected', 'selected');
                    //     $('#project_site_id').attr("style", "pointer-events: none;");
                    // }
                }
            });
        }

            if (project_id == 1) {
                $('#other_cash_detail').val('');
                $("#other_cash_txt").show();
            } else {
                $("#other_cash_txt").hide();
            }
        });

    });

</script>
@endsection
