<?php
    use Illuminate\Support\Facades\Config;
?>

@extends('layouts.admin_app')

@section('content')

<link href="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<link href="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
    </div>
    <div class="row">

        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('admin.cheque_stats_report') }}" id="cheque_frm" method="post" class="form-material" accept-charset="utf-8">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <!--  -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="col-sm-3 control-label">Company</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2" name="company_id" id="company_id">
                                        <option selected disabled>select company</option>
                                        @foreach($company as $key => $value)
                                            <option value="{{ $key }}"> {{ $value }}</option>
                                        @endforeach
                                        
                                        </select>
                                    </div>    
                                    </div>
                                </div>
                                <!--  -->
                                <!-- Bank -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="col-sm-3 control-label">Select Bank</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2"  name="bank_id" id="bank_id">
                                    
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                <!--  -->                                         
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                <div class="form-group">
                                        <label class="col-sm-3 control-label">Date<label class="serror"></label> </label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control timeseconds shawCalRanges" name="date" id="date" value="<?php echo!empty($date) ? $date : "" ?>" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-12 col-sm-12">
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
            <div class="white-box">

                <p class="text-muted m-b-30"></p>
                </br>
                <div class="table-responsive">
                    <table id="cheque_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bank</th>
                                <th>A/C Number</th>
                                <th>Cheque Ref Number</th>                                
                                <th>From->To</th>
                                <th>Total Cheques</th>
                                <th>Balanced Cheques</th>
                                <th>Report</th>
                                                            
                            </tr>
                        </thead>
                        <tbody>
                        @if( count($records) > 0)
                        @foreach($records as $value)
                            <tr>
                                <td>{{ $value->bank_name }}</td>
                                <td>{{ $value->ac_number }}</td>
                                <td>{{ $value->check_ref_no }}</td>
                                <td>{{ $value->from }} to {{ $value->to }}</td>      
                                <td>{{ $value->total_cheque }}</td>
                                <td>{{ $value->balanced_cheque }}</td>
                                <td>
                                <a href="{{ route('admin.cheque_balanced_report',['id'=>$value->id]) }}" target="_blank" class="btn btn-rounded btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                               
                            </tr>
                            @endforeach 
                            @endif 
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>    
    </div>
</div>
@endsection

@section('script')		
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/moment/moment.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/select2/dist/js/select2.full.min.js" type="text/javascript"></script>

<script>
$(document).ready(function () {
    $('.select2').select2();
	$('#cheque_table').DataTable({
		dom: 'Bfrtip',buttons: [
             'csv', 'excel', 'print'
        ],
        stateSave: true
    });

    $('.showdropdowns').daterangepicker({
        showDropdowns: true,
          timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'MM/DD/YYYY h:mm A'
            }
    }); 

    $('.shawCalRanges').daterangepicker({
        showDropdowns: false,
        timePicker: true,
        timePickerIncrement: 1,
        timePicker24Hour: true,
        locale: {
        format: 'D/M/YYYY H:mm'
    },
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    alwaysShowCalendars: true,
    });

});
</script>

<script>
$("#company_id").change(function() {

    var company_id = $("#company_id").val();
    if (company_id.length >= 1) {
             
                $.ajax({
                    url: "{{ route('admin.get_bank_list_cheque')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {
                        $('#bank_id').empty();
                        $('#bank_id').append(data);

                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });

    }

});
</script>
@endsection