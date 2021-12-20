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
                        <form action="{{ route('admin.save_hotel') }}" id="save_hotel" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group "> 
                                        <label>Select Company</label>
                                        <select class="form-control" name="company_id" id="company_id">
                                            <option value="">Select Company</option>
                                            @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Project</label>
                                        <select class="form-control select2" class="project_id" name="project_id" id="project_id">
                                            <option value="">Select Project</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 other_project hide">
                                    <div class="form-group">
                                        <label>Other Project</label>
                                        <input type="text" class="form-control" name="other_project_details" id="other_project_details"/> 
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Stayed User</label>
                                        <select required="required" name="stay_user_ids[]" id="stay_user_ids" class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose">
                                            @foreach($stay_user_ids as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Hotel Name</label>
                                         <input type="text" name="hotel_name" maxlength="255" id="hotel_name" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Place</label>
                                         <input type="text" name="place" maxlength="255" id="place" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Booking No</label>
                                        <input type="text" name="booking_no" maxlength="255" id="booking_no" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Booking File (Only PDF)</label>
                                        <div>
                                            <input type="file" name="booking_image" accept="application/pdf" class="form-control" id="booking_image"/>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">                                
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Amount</label>
                                        <input type="number" name="total_amount" id="total_amount" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Payment Type</label>
                                        <select class="form-control select2" class="payment_type" name="payment_type" id="payment_type">
                                            @foreach($payment_type as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Check In</label>
                                        <input type="text" name="check_in_datetime" id="check_in_datetime" class="form-control" />
                                    </div>
                                </div>                                
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Check Out</label>
                                        <input type="text" name="check_out_datetime" id="check_out_datetime" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Work Details</label>
                                        <textarea class="form-control" rows="3" name="work_details" id="work_details"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Booking Notes</label>
                                        <textarea class="form-control" rows="3" name="booking_note" id="booking_note"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.hotel') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<script>
    
    $('#check_in_datetime').datetimepicker({ format:'DD-MM-YYYY HH:mm:ss' });
    $('#check_out_datetime').datetimepicker({ format:'DD-MM-YYYY HH:mm:ss' });
    $('.select2').select2();
    jQuery('#save_hotel').validate({
        ignore: [],
        rules: {
            company_id:{
                required: true,
            },
            project_id:{
                required: true,
            },
            stay_user_ids: {
                required: true
            },
            hotel_name: {
                required: true
            },
            booking_no: {
                required: true
            },
            booking_image: {
                required: true
            },
            check_in_datetime: {
                required: true
            },
            check_out_datetime: {
                required: true
            },
            total_amount: {
                required: true,
                numeric: true
            },
            place: {
                required: true
            },
            payment_type: {
                required: true
            },
        }
    });
    $(document).ready(function(){
         $("#company_id").change(function() {
            var company_id = $(this).val();
            if($(this).val().length >= 1)
            {
                $.ajax({
                    url: "{{ route('admin.get_cash_project_list')}}",
                    type: 'get',
                    data: "company_id="+company_id,
                    success: function( data, textStatus, jQxhr ){
                        $('#project_id').empty();
                        $('#project_id').append(data);
                    },
                    error: function( jqXhr, textStatus, errorThrown ){
                        console.log( errorThrown );
                    }
                });
            }
        });
        $("#project_id").change(function() {
            if($(this).val() == 1) {
                $("#other_project_details").parents('.other_project').removeClass('hide');
            } else {
                $("#other_project_details").val("");
                $("#other_project_details").parents('.other_project').addClass('hide');
            }
        });
    });
</script>
@endsection