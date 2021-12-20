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
                                <label>Select Company</label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}" <?php echo ($bank_payment_detail[0]->company_id == $company_list_data->id) ? "selected='selected'" : '' ?> >{{ $company_list_data->company_name }}</option>
                                    @endforeach
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
                            
                            <!-- <div class="form-group ">
                                <label>Vendor/Party Bank Details</label>
                                <textarea class="form-control" name="bank_details" id="bank_details"><?php echo $bank_payment_detail[0]->bank_details; ?></textarea>
                                
                            </div> -->

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
                                <input type="hidden" name="old_cheque_number" id="old_cheque_number" value="<?php echo !(empty($chno))?$chno:""?>">
                            </div>

                            <div class="form-group "> 
                                <label>Cheque Issue Date</label> 
                                <input type="text" class="form-control" name="issue_date" id="issue_date" value="<?php echo !(empty($cheque_number[0]->issue_date))?date('d-m-Y', strtotime($cheque_number[0]->issue_date)):""?>" /> 
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
    jQuery('#add_payment').validate({
        ignore: [],
        rules: {
            bank_id: {
                required: true,
            },
            bank_details: {
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
            issue_date:{
                required:true
            },
            cheque_number:{
                required:true
            }
        }
    });
    $(document).ready(function () {
        jQuery('#issue_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
        var company_id = $("#company_id").val();
        $.ajax({
            url: "{{ route('admin.get_cash_project_list')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function (data, textStatus, jQxhr) {
                $('#project_id').empty();
                $('#project_id').append(data);
                $("#project_id").val("<?php echo $bank_payment_detail[0]->project_id; ?>");
                var other_project = "<?php echo $bank_payment_detail[0]->project_id; ?>";
                if (other_project == 1) {
                    $("#other_cash_txt").show();
                }
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        $.ajax({
            url: "{{ route('admin.get_cash_vendor_list')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function (data, textStatus, jQxhr) {
                $('#vendor_id').empty();
                $('#vendor_id').append(data);
                $("#vendor_id").val("<?php echo $bank_payment_detail[0]->vendor_id; ?>");
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        $.ajax({
            url: "{{ route('admin.get_bank_list_cheque')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function (data, textStatus, jQxhr) {
                $('#bank_id').append(data);
                $("#bank_id").val("<?php echo $bank_payment_detail[0]->bank_id; ?>");
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
        var vendor_id = "<?php echo $bank_payment_detail[0]->vendor_id; ?>"
        $.ajax({
            url: "{{ route('admin.get_vendor_bank_details')}}",
            type: 'get',
            data: "company_id=" + company_id+"&"+"vendor_id="+vendor_id,
            success: function (data, textStatus, jQxhr) {
                $('#bank_details').empty();
                $('#bank_details').append(data);
                $("#bank_details").val("<?php echo $bank_payment_detail[0]->bank_details; ?>");
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });


        var chno = "<?php echo $ch_no; ?>";
        var id   = "<?php echo $bank_payment_detail[0]->id; ?>"
        $.ajax({
            url: "{{ route('admin.get_bank_cheque_list_edit')}}",
            type: 'get',
            data: "company_id=" + company_id+'&id='+id,
            success: function (data, textStatus, jQxhr) {
                $('#cheque_number').empty();
                $('#cheque_number').append(data);
                if(chno!=0) {
                    $("#cheque_number").val(chno);    
                }
                //console.log("<?php echo $chno; ?>")
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        $("#company_id").change(function () {

            var company_id = $("#company_id").val();
            if (company_id.length >= 1)
            {
                $.ajax({
                    url: "{{ route('admin.get_cash_project_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function (data, textStatus, jQxhr) {
                        $('#project_id').empty();
                        $('#project_id').append(data);
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });

                $.ajax({
                    url: "{{ route('admin.get_bank_list_cheque')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function (data, textStatus, jQxhr) {
                        $('#bank_id').append(data);
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });

                $.ajax({
                    url: "{{ route('admin.get_cash_vendor_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function (data, textStatus, jQxhr) {
                        $('#vendor_id').empty();
                        $('#vendor_id').append(data);
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }

        });

        $("#bank_id").change(function () {
            var company_id = $("#company_id").val();
            var bank_id = $("#bank_id").val();

            $.ajax({
                url: "{{ route('admin.get_cheque_list_bank')}}",
                type: 'get',
                data: "company_id=" + company_id,
                success: function (data, textStatus, jQxhr) {
                    $('#cheque_number').html(data);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

        $("#vendor_id").change(function () {
            var company_id = $("#company_id").val();
            var vendor_id  = $("#vendor_id").val();

            $.ajax({
                url: "{{ route('admin.get_vendor_bank_details')}}",
                type: 'get',
                data: "company_id=" + company_id+"&"+"vendor_id="+vendor_id,
                success: function (data, textStatus, jQxhr) {
                    $('#bank_details').empty();
                    $('#bank_details').append(data);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });
        
        //$('#user_id').select2();
        $("#project_id").change(function () {
            var project_id = $("#project_id").val();
            if (project_id == 1) {
                $("#other_cash_txt").show();
            } else {
                $("#other_cash_txt").hide();
            }
        });

    });

</script>
@endsection
