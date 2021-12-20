@extends('layouts.admin_app')

@section('content')
<link href="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<link href="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
<?php

use App\Lib\CommonTask;

$common_task = new CommonTask();
?>
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
            
            <div class="white-box">
                <form id="report_frm" action="{{ route('admin.budget_sheet_report') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <select class="form-control" onchange="$('#report_frm').submit()" name="budget_sheet_year" id="budget_sheet_year">
                                    <option>Select Year</option>
                                    @for($i=$min_year;$i<=$max_year;$i++) <option @if($selected_year==$i) selected="" @endif value="{{$i}}">{{ $i }}</option>
                                        @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" onchange="$('#report_frm').submit()" name="budget_sheet_week" id="budget_sheet_week">
                                    @for($i=1;$i<=52;$i++) <option @if($selected_week==$i) selected="" @endif value="{{ $i }}">{{ 'Week- '.$i.' ('.$common_task->getWeekStartAndEndDate($i,date("Y")).')' }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                    <br>
                    <div style='text-align: center;'>
                    <h2> OR </h2>
                    </div>
                            <form action="{{ route('admin.budget_sheet_reportByDate') }}" method="post" class="form-material" accept-charset="utf-8">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Date<label class="serror"></label></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control timeseconds shawCalRanges" required name="meeting_date" id="meeting_date" value=" ">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                   
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
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
                <br>
                <!-- <hr class="m-t-0"> -->
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="white-box">
                <div class="table-responsive">
                    <table id="report_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Meeting Number</th>
                                <th>Meeting Date</th>
                                <th>Budget Sheet Number</th>
                                <th>Company</th>
                                <th>Client</th>
                                <th>Department</th>
                                <th>Vendor</th>
                                <th>Description</th>
                                <th>Remarks by employee</th>
                                <th>Request Amount</th>
                                <th>Schedule Date</th>
                                <th>Mode Of Payment</th>
                                <th>Project</th>
                                <th>Site Name</th>
                                <th>Total Amount</th>
                                <th>Approved Amount</th>
                                <th>Approval Remark</th>
                                <th>Remaining Hold Amount</th>
                                <th>Admin Status</th>
                                <th>Super Admin Status</th>
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th>Payment Date</th>
                                <th data-orderable="false">View</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report_data as $report)
                            <tr>
                                <td>{{ $report->meeting_number }}</td>
                                <td>{{ date('d-m-Y',strtotime($report->meeting_date)) }}</td>
                                <td>{{ $report->budhet_sheet_no }}</td>
                                <td>{{ $report->company_name }}</td>
                                <td>
                                @if($report->client_name)
                                @if($report->client_name == 'Other Client')
                                        {{ $report->client_name }}
                                            @else
                                            {{ $report->client_name." (".$report->location.")" }}
                                            @endif
                                    @else
                                    NA
                                    @endif 

                                        </td>
                                <td>{{ $report->dept_name }}</td>
                                <td>{{ $report->vendor_name }}</td>
                                <td>{{ $report->description }}</td>
                                <td>{{ $report->remark_by_user }}</td>
                                <td>{{ $report->request_amount }}</td>
                                <td>{{ $report->schedule_date_from.' To '.$report->schedule_date_to }}</td>

                                <td>{{ $report->mode_of_payment }}</td>
                                <td>{{ $report->project_name }}</td>
                                <td>{{ $report->site_name }}</td>
                                <td>{{ $report->total_amount }}</td>
                                <td>{{ $report->approved_amount }}</td>
                                <td>{{ $report->approval_remark }}</td>
                                <td>{{ $report->remain_hold_amount }}</td>
                                <td>
                                    @if($report->first_approval_status=="Pending")
                                    <b class="text-warning">{{ $report->first_approval_status }}</b>
                                    @elseif($report->first_approval_status=="Approved")
                                    <b class="text-success">
                                        {{ $report->first_approval_status }}

                                    </b>
                                    @elseif($report->first_approval_status=="Rejected")
                                    <b class="text-danger">

                                        {{ $report->first_approval_status }}

                                    </b>
                                    @endif
                                </td>
                                <td>
                                    @if($report->second_approval_status=="Pending")
                                    <b class="text-warning">{{ $report->second_approval_status }}</b>
                                    @elseif($report->second_approval_status=="Approved")
                                    <b class="text-success">
                                        {{ $report->second_approval_status }}

                                    </b>
                                    @elseif($report->second_approval_status=="Rejected")
                                    <b class="text-danger">
                                        {{ $report->second_approval_status }}

                                        @endif
                                </td>

                                <td>
                                    @if($report->status=="Pending")
                                    <span class="text-warning">{{ $report->status }}</span>
                                    @elseif($report->status=="Approved")
                                    <span class="text-success">{{ $report->status }}</span>
                                    @else
                                    <input type="hidden" value="{{ $report->reject_note }}" name="reject_reason_{{ $report->id }}" id="reject_reason_{{ $report->id }}" />
                                    <a href="#" data-toggle="modal" onclick="set_reject_reason({{$report->id}});" data-target="#rejectReasonModel">{{ $report->status }}</a>
                                    @endif
                                </td>

                               
                                <td>
                                   @if($report->payment_status == 'Pending')
                                   <span class="label label-rouded label-danger">Pending</span>
                                    @else
                                    <span class="label label-rouded label-info">Done</span>
                                    @endif
                                </td>

                                <td>
                                   @if($report->payment_date)
                                   <?php echo date('d-m-Y h:i:s a', strtotime($report->payment_date))?>
                                    @else
                                    NA
                                    @endif
                                </td>
        

                                <td>
                                    <a href="#" onclick="get_budget_sheet_files('{{$report->id}}');" title="View Files" id="showFiles" data-target="#budgetSheetFilesModel" data-toggle="modal" class="btn btn-primary btn-circle"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>    
            </div>
        </div>


        <div id="rejectReasonModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Rejection Note</h4>
                    </div>
                    <div class="modal-body" id="reject_content">

                    </div>

                </div>
                <!-- /.modal-content -->
            </div>
        </div>

        <!-------------------------------------------------------- Files Modal ------------------------------------------>
        <div id="budgetSheetFilesModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Files</h4>
                    </div>

                    <br>
                    <br>

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Download</th>
                                <th>Filename</th>


                            </tr>
                        </thead>
                        <tbody id="file_table">

                        </tbody>
                    </table>

                    <!-- <div class="modal-body" id="files">
                    </div> -->

                </div>
                <!-- /.modal-content -->
            </div>
        </div>


        @endsection

        @section('script')
        <script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/moment/moment.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/select2/dist/js/select2.full.min.js" type="text/javascript"></script>
        <script>

$(document).ready(function() {

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
            $('#report_table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel',
                    'csv',
                    'print'
                ]
            });

            function set_reject_reason(id) {
                $('#reject_content').html($('#reject_reason_' + id).val());
            }
        </script>

        <script>
            function get_budget_sheet_files(id) {


                $.ajax({
                    url: "{{ route('admin.get_budget_sheet_files') }}",
                    type: "post",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    success: function(data) {
                        var trHTML = '';
                        if (data.status) {

                            let sheet_files_arr = data.data.budget_sheet_files;
                            if (sheet_files_arr.length == 0) {

                                $('#file_table').empty();
                                trHTML += '<span>No Records Found !</span>';
                                $('#file_table').append(trHTML);

                            } else {

                                $('#file_table').empty();

                                $.each(sheet_files_arr, function(index, files_obj) {

                                    no = index + 1;
                                    trHTML += '<tr>' +
                                        '<td>' + no + '</td>' +
                                        '<td><a title="Download File" target="_blank;" href="' + files_obj.budget_sheet_file + '"><i class="fa fa-cloud-download fa-lg"></i></a></td>' +
                                        '<td>' + files_obj.file_name + '</td>' +
                                        '</tr>';


                                });
                                $('#file_table').append(trHTML);

                            }

                        } else {

                            $('#file_table').empty();
                            trHTML += '<span>No Records Found !</span>';
                            $('#file_table').append(trHTML);
                        }

                    }
                });
            }
        </script>
        @endsection