@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Update Maintenance</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.vehicle_maintenance') }}">{{ $module_title }}</a></li>
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
                    <div class="col-md-12">
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
                        <form action="{{ route('admin.submit_vehicle_maintenance') }}" enctype="multipart/form-data" id="update_form" method="post">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $vehicle_maintenance_data[0]->id }}" />

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Maintenace Type </label>
                                        <input type="text" class="form-control" name="type" id="type" value="{{ $vehicle_maintenance_data[0]->maintenance_type }}" readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Received Meter Reading <span class="error">*</span> <span id="last_reading"> (Last Reading:-0)</span></label>
                                        <input type="text" class="form-control" name="received_meter_reading" id="received_meter_reading">
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Amount  <span class="error">*</span> </label>
                                        <input type="text" class="form-control" name="amount" id="amount" value="{{ $vehicle_maintenance_data[0]->amount }}" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Received Date <span class="error">*</span> </label>
                                        <input type="text" class="form-control" name="received_date" id="received_date" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Vehicle Images <span class="error">*</span> </label>
                                        <div>
                                            <input type="file" name="vehicle_image[]" class="form-control" id="vehicle_images" accept="image/*"  multiple required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.vehicle_maintenance') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="education_div_count" id="education_div_count" value="0" />
    <input type="hidden" name="experience_div_count" id="experience_div_count" value="0" />
</div>
@endsection


@section('script')



<script>
    // Date Picker
    jQuery('.mydatepicker, #datepicker').datepicker();
    jQuery('#datepicker-autoclose').datepicker({
        autoclose: true,
        todayHighlight: true
    });

    jQuery('#date-range').datepicker({
        toggleActive: true
    });
    jQuery('#datepicker-inline').datepicker({

        todayHighlight: true
    });
</script>

<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

<script>
    $(document).ready(function() {

       
        $('#received_date').datetimepicker({ format:'DD-MM-YYYY HH:mm:ss' });

        $('#update_form').validate({
            rules: {

                amount: {
                    required: true
                },
                received_meter_reading: {
                    required: true
                },
                received_date: {
                    required: true
                }

            }
        })

    });
</script>
@endsection