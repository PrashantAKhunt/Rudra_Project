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
                        <form action="{{ route('admin.cheque_use_report') }}" id="cheque_frm" method="post" class="form-material" accept-charset="utf-8">
                        @csrf
                        <div class="form-body">
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
                <a href="{{ $csv_data }}"  class="btn btn-primary pull-right"><i class="fa fa-download "></i> Download CSV</a>
                <p class="text-muted m-b-30"></p>
                </br>
                <div class="table-responsive">
                    <table id="chequeused_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Bank</th>
                                <th>Check Ref No</th>
                                <th>Chque No</th>
                                <th>Project Name</th>
                                <th>Vendor</th>
                                <th>Issue Date</th>
                                <th>Clear Date</th>
                                <th>Amount</th>
                                <th>Work Detail</th>
                                <th>Remark</th>
                                <th>Is Used</th>
                                <th>Is Signed</th>
                                <th>Cheque Failed</th>                           
                            </tr>
                        </thead>
                        <tbody>
                           <?php if (!empty($records[0])) {
                            foreach ($records as $key => $value) { 
                            ?>
                            <tr>
                                <td>{{$value->company_name}}</td>
                                <td>{{$value->bank_name}}</td>
                                <td>{{$value->check_ref_no}}</td>
                                <td>{{$value->ch_no}}</td>
                                <td>{{$value->project_name}}</td>
                                <td>{{$value->vendor_name}}</td>
                                <td>
                                
                                @if($value->issue_date)
                                {{ date('d-m-Y',strtotime($value->issue_date))  }}
                                @endif
                                   
                                </td>
                                <td>

                                @if($value->cl_date)
                                {{ date('d-m-Y',strtotime($value->cl_date))  }}
                                @endif
                               
                                </td>
                                <td>{{$value->amount}}</td>
                                <td>{{$value->work_detail}}</td>
                                <td>{{$value->remark}}</td>
                                <td>{{$value->is_used}}</td>
                                <td>{{$value->is_signed}}</td>
                                <td>@if($value->is_failed == 1)
                                    {{$value->failed_reason}}
                                    @else
                                    No
                                    @endif
                                </td>
                            </tr> 
                            <?php }
                            }?>
                                 
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
    $('#chequeused_table').DataTable( {
    } );

    $('.select2').select2();
    $('#select_all').click(function() {
        if($(this).prop("checked") == true){
            $('#user_id').select2('destroy');   
            $('#user_id option').prop('selected', true);
            $('#user_id').select2();
        }else{
            $('#user_id').select2('destroy');   
            $('#user_id option').prop('selected', false);
            $('#user_id').select2();
        }
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
@endsection