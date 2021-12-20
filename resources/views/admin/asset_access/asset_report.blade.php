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
                        <form action="{{ route('admin.asset_report') }}" id="asset_report" method="post">
                            @csrf
                            <div class="col-md-4">
                                <div class="form-group "> 
                                    <label>Select Asset</label>
                                    <select class="form-control" name="asset_id" id="asset_id">
                                        <option value="">Select Asset</option>
                                        @foreach($Asset_List as $asset_list_data)
                                            <option <?php if($asset_id == $asset_list_data->id ){ ?> selected <?php } ?> value="{{ $asset_list_data->id }}">{{ $asset_list_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group ">
                                    <label>Select Asset</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="">Select Status</option>
                                        <option <?php if($status == 'Assigned' ){ ?> selected <?php } ?> value="Assigned">Assigned</option>
                                        <option <?php if($status == 'Confirmed' ){ ?> selected <?php } ?> value="Confirmed">Confirmed</option>
                                        <option <?php if($status == 'Rejected' ){ ?> selected <?php } ?> value="Rejected">Rejected</option>
                                        <option <?php if($status == 'Submited' ){ ?> selected <?php } ?> value="Submited">Submited</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label>Date</label>
                                    <div class="input-daterange input-group" id="date-range">
                                        <input type="text" class="form-control"  name="start_date" id="start_date" readonly="true" value="<?php echo $start_date ?>" />
                                        <span class="input-group-addon bg-info b-0 text-white">to</span>
                                        <input type="text" class="form-control" name="end_date" id="end_date" readonly="true" value="<?php echo $end_date ?>" />
                                    </div>
                                </div>
                                </div>
                            <div class="col-md-1">
                                <div class="form-group ">                                    
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" onclick="clearFields();" class="btn btn-default">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="white-box">
                <div class="table-responsive">
                    <table id="asset_list_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Asset Name</th>
                                <th>Assign Date</th>
                                <th>Return Date</th>                                
                                <th>Acceptance Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($assets)) {
                                foreach ($assets as $value) { ?>
                                    <tr>
                                        <td><?= $value->user->name; ?></td>
                                        <td><?= $value->asset->name; ?></td>
                                        <td><?= date('d/m/Y', strtotime($value->asset_access_date)); ?></td>
                                        @if($value->status=="Submited")
                                            <td><?= date('d/m/Y', strtotime($value->asset_return_date)); ?></td>
                                        @else
                                            <td>N/A</td>
                                        @endif
                                        <td>
                                            @if($value->status=="Submited")
                                                <b class="text-warning">
                                                    {{ $value->status }}
                                                </b>
                                            @elseif($value->status=="Assigned")
                                                <b class="text-primary">
                                                    {{ $value->status }}
                                                </b>
                                            @elseif($value->status=="Confirmed")
                                                <b class="text-success">
                                                    {{ $value->status }}
                                                </b>
                                            @elseif($value->status=="Rejected")
                                                <b class="text-danger">
                                                    {{ $value->status }}
                                                </b>
                                            @endif
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $('#asset_list_table').DataTable({
        "order": [
            [3, "DESC"]
        ],
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf', 'print'
        ]
    });
    jQuery("#asset_report").validate({
        ignore: [],
        rules: {},
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });

    $('.select2').select2();
	   
	jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'yyyy-mm-dd'
    });

    function clearFields(){
        $('#asset_id').val('');
        $('#status').val('');
        $('#start_date').val('');
        $('#end_date').val('');
    }

</script>
@endsection