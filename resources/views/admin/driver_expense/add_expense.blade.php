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
                    <form action="{{ route('admin.insert_expense') }}" enctype="multipart/form-data" id="add_expense" method="post">
                        @csrf
                        <div class="col-sm-6 col-xs-6">
                            <!-- <div class="form-group">
                                <label>Fuel Type</label>
                                <select class="form-control" class="fuel_type" name="fuel_type" id="fuel_type">
                                    <option value="Petrol">Petrol</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="CNG">CNG</option>
                                </select>
                            </div>-->
                           <!--  <div class="form-group">
                                <label>Expense Date</label>
                                <input type="text" class="form-control datepicker date_of_expense"  name="date_of_expense" id="date_of_expense" value="<?php echo date('Y-m-d'); ?>" readonly="true" />
                            </div> -->
                            <div class="form-group">
                                <label>Amount <span class="error">*</span></label>
                                <input type="text" class="form-control amount"  name="amount" id="amount" />
                            </div>
                            <div class="form-group">
                                <label>Meter Reading Photo <span class="error">*</span><span class="text-muted">Allowed file extensions are png, jpg, jpeg</span></label>
                                <div>
                                    <input type="file" name="meter_reading_photo" class="form-control" id="meter_reading_photo" accept="image/x-png,image/png, image/jpg, image/jpeg" data-accept="jpg,png,jpeg"/>
                                </div>
                            </div>
                            <div class="form-group ">
                                    <label>Fuel Price<span class="error">*</span></label>
                                    <input type="text" class="form-control" name="fuel_price" id="fuel_price">
                            </div>
                            <br>
                            <div class="form-group ">
                                <label>Accountant<span class="error">*</span></label>
                                <select class="form-control" id="moniter_user_id" name="moniter_user_id">
                                    <option value="">Select user</option>
                                    <?php
                                    foreach ($users as $key => $usersData) {
                                    ?>
                                    <option value="<?php echo $usersData['id']?>">{{$usersData['name']}}</option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group ">
                                <label>Expense Reimbursement<span class="error">*</span></label>
                                <select name="reambance_type" id="reambance_type" class="form-control">
                                    <option value="">Select Expense Reimbursement</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group ">
                                <label>Payment Type<span class="error">*</span></label>

                                <!-- <select name="payment_type" id="payment_type" class="form-control"> -->
                                <select class="form-control required"  name="payment_type" id="payment_type" onchange="optionCheck()" style="display:block" style="display:none">
                                    <option value="">Payment type</option>
                                    <option value="Card" >Card</option><!-- Yes -->
                                    <option value="Cash" >Cash</option><!-- No -->
                                </select>
                            </div>
                            </div>
                            
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label>Vehicle Type<span class="error">*</span></label>
                                <select class="form-control" class="vehicle_type" name="vehicle_type" id="vehicle_type">
                                    <option value="">Select Vehicle Type</option>
                                    @foreach($asset_list as $asset)
                                    <option value="{{ $asset->id }}" <?php if($asset->id==$asset_id){ echo "selected";}?> >{{ $asset->name.' ('.$asset->asset_1.'-'.$asset->fuel_type.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Meter Reading<span class="error">*</span><span id="last_reading"> (Last Reading:-0)</span></label>
                                <input type="text" class="form-control" name="meter_reading" id="meter_reading">
                            </div>

                            <div class="form-group">
                                <label>Comment<span class="error">*</span></label>
                                <textarea name="comment" id="comment" class="form-control" ></textarea>
                            </div>
                            <div class="form-group">
                                <label>Bill Photo <span class="error">*</span><span class="text-muted">Allowed file extensions are png, jpg, jpeg</span></label>
                                <div>
                                    <input type="file" name="bill_photo" class="form-control" id="bill_photo" accept="image/x-png,image/png, image/jpg, image/jpeg" data-accept="jpg,png,jpeg"/>
                                </div>
                            </div>
                                <!-- <div class="form-group">
                                    <label>Cash/Card</label>
                                    <input type="text"class="form-control">
                                </div> -->
                                <div id="yes-info" style="display: none;">
                                        <div class="form-group">
                                            <label>Card Number</label>
                                            <input class="form-control" maxlength="4" type="text"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"  name="card_type" id="card_type" placeholder=" Card Number4321"  style="display: block;"/>
                                        </div>
                                </div>
                        </div>
                        </div>
                        <div class="col-md-12">
                        <button type="submit" class="btn btn-success pull-left">Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.expense') }}'" class="btn btn-default pull-left">Cancel</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function () {
        $("#vehicle_type").change(function() {

            var txt = $('option:selected', this).val();

            $.ajax({
                url: "{{ route('admin.get_assign_asset')}}",
                type: 'get',
                data: "asset_id="+txt,
                success: function( data, textStatus, jQxhr ){
                    var myJSON = jQuery.parseJSON(data);
                    var reading = 0;
                    if(myJSON.success) {
                        reading = myJSON.data;
                    }
                    $("#last_reading").text(" (Last Reading:-"+reading+")");
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });


        $.ajax({
                url: "{{ route('admin.get_assign_asset')}}",
                type: 'get',
                data: "asset_id="+$("#vehicle_type").val(),
                success: function( data, textStatus, jQxhr ){
                    var myJSON = jQuery.parseJSON(data);
                    var reading = 0;
                    if(myJSON.success) {
                        reading = myJSON.data;
                    }
                    $("#last_reading").text(" (Last Reading:-"+reading+")");
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });


    });

    var __dayDiff = 0;
    jQuery('#add_expense').validate({
        ignore: [],
        rules: {
            vehicle_type: {
                required: true
            },
            comment: {
                required: true,
            },
            meter_reading: {
                required: true,
                number:true
            },
            fuel_price: {
                required: true,
                number:true
            },
            moniter_user_id: {
                required: true,
            },
            meter_reading_photo: {
                required: true,
            },
            bill_photo: {
                required: true,
            },
            amount: {
                required: true,
                number : true
            },
            card_type:{
                required: function (){
                    return $("#card_type").is(":visible");
                },
                number : true
            },
            reambance_type : {
                required : true
            }
        }
    });

    $('.select2').select2();
    $('.datepicker').datepicker({
        format : 'yyyy-m-d',
    });
    $('.clockpicker').clockpicker({
        donetext: 'Done',
    });
    function optionCheck(){
        var option = document.getElementById("payment_type").value;
        if(option == "Card"){
            document.getElementById("yes-info").style.display ="block";
        }else{
            $("#card_type").val('');
            document.getElementById("yes-info").style.display ="none";
        }
    }

</script>
@endsection
