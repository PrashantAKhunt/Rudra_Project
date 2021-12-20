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
                        <form action="{{ route('admin.insert_vehicle_maintenance') }}" enctype="multipart/form-data" id="add_insurance_frm" method="post">
                            @csrf
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Company<span class="error">*</span></label>
                                        <select class="form-control" name="company_id" id="company_id">
                                            <option value="">Select Company</option>
                                            @foreach($company_list as $key => $company)
                                            <option value="{{$company['id']}}">{{ $company['company_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Asset Name(Vehicle) <span class="error">*</span> </label>
                                        <select class="form-control" name="asset_id" id="asset_id">
                                            <option value="">--- Select Vehicle ---</option>
                                            @foreach($asset_data as $value)
                                            <option value="{{$value['id']}}">{{$value['name']}} - {{$value['asset_1']}} </option>
                                            @endforeach
                                        </select>


                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Maintenance Type <span class="error">*</span> </label>
                                        <select class="form-control" name="maintenance_type" id="maintenance_type">
                                            <option value="">--- Select Type ---</option>
                                            <option value="Major Accident Maintenance">Major Accident Maintenance</option>
                                            <option value="Minor Accident Maintenance">Minor Accident Maintenance</option>
                                            <option value="Part Failure Maintenance">Part Failure Maintenance</option>
                                            <option value="Periodic Maintenance">Periodic Maintenance</option>

                                        </select>


                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Description</label>
                                        <textarea id="description" name="description" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Current Meter Reading <span class="error">*</span> <span id="last_reading"> (Last Reading:-0)</span></label>
                                        <input type="text" class="form-control" name="start_meter_reading" id="start_meter_reading">
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Service Center Name <span class="error">*</span> </label>
                                        <input type="text" class="form-control" name="service_center_name" id="service_center_name" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Service Center Address <span class="error">*</span> </label>
                                        <textarea id="service_center_address" name="service_center_address" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Amount  <span class="error">*</span> </label>
                                        <input type="text" class="form-control" name="amount" id="amount" value="" />
                                    </div>
                                </div>
                            </div>


                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Maintenance Date <span class="error">*</span> </label>
                                        <input type="text" class="form-control"  name="maintenance_date" id="maintenance_date" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Return Date <span class="error">*</span> </label>
                                        <input type="text" class="form-control" name="received_date" id="received_date" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group periodic_date" id="periodic_date" hidden="true">
                                        <label>Next Scheduled Date <span class="error">*</span> </label>
                                        <input type="text" class="form-control" readonly="" name="next_periodic_date" id="next_periodic_date" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group periodic_date" id="periodic_date1" hidden="true">
                                        <label>Periodic Maintenance Kilometer <span class="error">*</span> </label>
                                        <input type="text" class="form-control" name="periodic_maintenance_km" id="periodic_maintenance_km" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Vehicle images <span class="error">*</span> </label>
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
    $("#maintenance_type").change(function() {
        let selection = $(this).val();
        //return alert(selection);
        if (selection == 'Periodic Maintenance') {

            $(".periodic_date").show()

        } else {

            $(".periodic_date").hide()
            $("#next_periodic_date").val('')
            $("#periodic_maintenance_km").val('')
        }
    });
    // Date Picker
    /* jQuery('.mydatepicker, #datepicker').datepicker();
    jQuery('#datepicker-autoclose').datepicker({
        autoclose: true,
        todayHighlight: true
    });

    jQuery('#date-range').datepicker({
        toggleActive: true
    });
    jQuery('#datepicker-inline').datepicker({

        todayHighlight: true
    }); */
</script>
<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<script>
    $(document).ready(function() {

    $('#maintenance_date').datetimepicker({ format:'DD-MM-YYYY HH:mm:ss' });
    $('#received_date').datetimepicker({ format:'DD-MM-YYYY HH:mm:ss' });


        jQuery('#next_periodic_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        $('#add_insurance_frm').validate({
            rules: {
                company_id: {
                    required: true
                },
                asset_id: {
                    required: true
                },
                maintenance_type: {
                    required: true
                },
                description: {
                    required: true
                },
                start_meter_reading: {
                    required: true,
                    number: true
                },

                service_center_name: {
                    required: true
                },
                service_center_address: {
                    required: true
                },
                amount: {
                    required: true,
                    number: true
                },
                maintenance_date: {
                    required: true
                },
                received_date: {
                    required: true
                },
                next_periodic_date: {
                    required: true
                },
                periodic_maintenance_km: {
                    required: true,
                    number: true
                },
                vehicle_images: {
                    required: true
                }

            }
        })

    });
</script>
@endsection
