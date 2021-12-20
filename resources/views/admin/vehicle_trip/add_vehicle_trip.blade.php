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
                        <form action="{{ route('admin.insert_vehicle_trip') }}" id="add_vehicle_trip" enctype="multipart/form-data" method="post" >
                            @csrf
                            <div class="form-group ">
                                <label>Asset <span class="error">*</span></label>
                                @if(!empty($asset))
                                <select name="asset_id" class="form-control" id="asset_id">
                                    <option value="">Select Asset</option>
                                    @foreach($asset as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <!-- <div class="form-group">
                                <label>Trip Type</label>
                                <select name="trip_type" id="trip_type" class="form-control">
                                    <option value="">Select Trip Type</option>
                                    <option value="Individual">Individual</option>
                                    <option value="User">User</option>
                                </select>
                            </div> -->
                            <div class="form-group ">
                                <label> Trip User <span class="error">*</span</label>
                                @if(!empty($user))
                                <select name="user_id" class="form-control" id="user_id">
                                    <option value="">Select users</option>
                                    <option value="0">Self</option>
                                    @foreach($user as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Opening Meter Reading <span class="error">*</span</label>
                                <input type="number" class="form-control" name="opening_meter_reading" id="opening_meter_reading" value="" />
                            </div>

                            <div class="form-group">
                                <label>Open Meter Reading Image <span class="error">*</span</label>
                                <div>
                                    <input type="file" name="reading_image" accept="image/*" class="form-control" id="reading_image" required />
                                </div>
                            </div>

                            <!-- <div class="form-group ">
                                <label>Closing Meter Reading</label>
                                 <input type="number" class="form-control" name="closing_meter_reading" id="closing_meter_reading" value="" /> 
                            </div> -->
                            <div class="form-group ">
                                <label>Note</label>
                                <!-- <input type="note" class="form-control" name="note" id="note" value="" />  -->
                                <textarea name="note" id="note" class="form-control"></textarea>
                            </div>
                            <div class="form-group">

                                <label>From Location <span class="error">*</span</label>
                                <input type="text" class="form-control" name="from_location" id="from_location">
                            
                            </div>

                            <div class="form-group">

                                <label>To Location <span class="error">*</span</label>
                                <input type="text" class="form-control" name="to_location" id="to_location">
                            
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.vehicle_trip') }}'" class="btn btn-default">Cancel</button>
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
    $(document).ready(function() {
        $("#company_id").change(function() {

            var company_id = $("#company_id").val();

            $.ajax({
                url: "{{ route('admin.get_letter_head_bank_list')}}",
                type: 'get',
                data: "company_id=" + company_id,
                success: function(data, textStatus, jQxhr) {
                    $('#bank_id').append(data);
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });
    });

    jQuery("#add_vehicle_trip").validate({
        ignore: [],
        rules: {
            user_id: {
                required: true,
            },
            asset_id: {
                required: true,
            },
            opening_meter_reading: {
                required: true,
                number: true,
            },
            reading_image: {
                required: true
     
            },
            to_location: {
                required: true,
            },
            from_location: {
                required: true,
            }

        }
    });
</script>
@endsection