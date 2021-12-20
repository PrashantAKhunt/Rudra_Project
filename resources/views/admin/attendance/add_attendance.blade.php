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
                        <form action="{{ route('admin.insert_attendance_approval') }}" id="add_attendance" method="post">
                            @csrf

                            <div class="form-group ">
                                <label>User</label>
                                @if(!empty($user))
                                <select name="user_id" class="select2 form-control user_id">
                                    @foreach($user as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach								
                                </select>
                                @endif
                            </div>
                            <div class="form-group "> 
                                <label>Date <span class="error">*</span> </label> 
                                <input type="text" class="form-control datepicker date"  name="date" id="date" value="" readonly="true" />
                            </div>

                            <div class="form-group ">
                                <label>In <span class="error">*</span> </label>
                                <div class="input-group clockpicker">
                                    <input type="text" class="form-control" name="in" value="09:00">
                                    <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span> </div>
                            </div>

                            <div class="form-group ">
                                <label>Out <span class="error">*</span> </label>
                                <div class="input-group clockpicker">
                                    <input type="text" class="form-control" name="out" value="18:00">
                                    <span class="input-group-addon"> <span class="glyphicon glyphicon-time"></span> </span> </div>
                            </div>

                            <div class="form-group ">
                                <label>Reason</label>
                                
                                    <textarea class="form-control" name="manual_add_reason"></textarea>
                                
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.attendance') }}'" class="btn btn-default">Cancel</button>
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
    var __dayDiff = 0;
    jQuery('#add_attendance').validate({
        ignore: [],
        rules: {
            user_id: {
                required: true,
            },
            date: {
                required: true
            },
            in: {
                required: true,
            },
            out: {
                required: true
            }
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });

    $('.select2').select2();
    $today = new Date();
    $yesterday = new Date($today);
    $yesterday.setDate($today.getDate() - 1);
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        endDate: $yesterday
    });
    $('.clockpicker').clockpicker({
        donetext: 'Done',
    });

</script>
@endsection
