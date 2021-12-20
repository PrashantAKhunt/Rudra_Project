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
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
    </div>
    <div class="row">
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
                @if($add_permission)
                <a href="{{ route('admin.add_attendance') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Attendance</a>
                <p class="text-muted m-b-30"></p>
                <br>
                @endif
                <div class="table-responsive">
                    <table id="attendance_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Date</th>
                                <th>Availability</th>
                                <th>First In</th>
                                <th>Last Out</th>
                                <th>Total Hours</th>
                                <th>Is Late</br>9:30</th>
                                <th>Is Late</br>9:45</th>
                                <th>Late Time</th>
                                <th>In/Out Logs</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="punch_model" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="model_data">
        </div>
    </div>
</div>

<div id="latemark_remove_model" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>Reason for late mark remove</h3>
                    </div>
                    <div  class="modal-body ">
                        <form method="post" id="late_remove_frm" action="{{ route('admin.late_mark_remove') }}">
                            @csrf
                            <div class="form-group ">
                                <label>Late remove note</label>
                                <textarea name="late_remove_note" id="late_remove_note" class="form-control"></textarea>
                            </div>
                            <input type="hidden" name="late_remove_attendance_id" id="late_remove_attendance_id" />
                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

@endsection

@section('script')
<script>
    $(document).ready(function () {

        $('#late_remove_frm').validate({
            rules:{
                'late_remove_note':{
                    required:true
                }
            }
        })

        var availability = {1: 'Present', 2: 'Pending', 3: 'Leave', 4: 'Holiday', 5: 'Weekend', 6: 'Mixed Leave'};

        var table = $('#attendance_table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            stateSave: true,
            "order": [[1, "DESC"]],
            "ajax": {
                url: "<?php echo route('admin.get_attendance_list'); ?>",
                type: "GET",

            },
            "createdRow": function (row, data, dataIndex) {

                if (availability[data.availability_status] == 'Weekend') {
                    $(row).addClass('text-info');
                }
            },
            "columns": [
                {"targets": 0, "searchable": true, "data": "name"},
                {"targets": 1, "searchable": true, "render": function (data, type, row) {
                        return moment(row.date).format("DD/MM/YYYY");
                    }
                },
                {"targets": 2, "searchable": false, "render": function (data, type, row) {
                        return availability[row.availability_status];
                    }
                },
                {"targets": 3, "searchable": true, "render": function (data, type, row) {
                        //return moment(row.first_in).format("hh:mm A");
                        if (moment(row.first_in).format("hh:mm A") == 'Invalid date') {
                            return '<span class="label label-rouded label-danger pull-right">Missing</span>';
                        } else {
                            return moment(row.first_in).format("hh:mm A");
                        }
                    }
                },
                {"targets": 4, "searchable": true, "render": function (data, type, row) {
                        if (moment(row.last_out).format("hh:mm A") == 'Invalid date') {
                            return '<span class="label label-rouded label-danger pull-right">Missing</span>';
                        } else {
                            return moment(row.last_out).format("hh:mm A");
                        }
                    }
                },
                {"targets": 5, "searchable": true, "data": "total_hours"},
                {"targets": 6,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = '';
                        @if(Auth::user()->role==config('constants.REAL_HR'))
                        if (row.is_late == 'NO') {
                            out += '<span class="btn btn-success" title="Change Status">No</span>';
                        } else {
                            out += '<a href="#" onclick="change_confirm('+row.id+');" data-toggle="modal" data-target="#latemark_remove_model" class="btn btn-danger" title="Change Status">Yes</a>';
                        }

                        @else
                            out +=row.is_late
                        @endif
                        return out;
                    }
                },
                {"targets": 6,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = '';
                        @if(Auth::user()->role==config('constants.REAL_HR'))
                        if (row.is_late_more == 'NO') {
                            out += '<span class="btn btn-success" title="Change Status">No</span>';
                        } else {
                            out += '<a href="#" onclick="change_confirm('+row.id+');" data-toggle="modal" data-target="#latemark_remove_model" class="btn btn-danger" title="Change Status">Yes</a>';
                        }
                         @else
                            out +=row.is_late
                        @endif
                        return out;
                    }
                },
                {"targets": 7, "searchable": true, "data": "late_time"},

                {"targets": 8, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var attendance_id = row.id;
                        return '<a href="#punch_model" onclick="getPunchData(&quot;<?php echo url('get_punch_data') ?>' + '/' + attendance_id + '&quot;)" data-toggle="modal" class="btn btn-primary btn-rounded" title="Punch data"><i class="fa fa-exchange"></i></a>';
                    }
                },
            ]
        });
    });
    function change_confirm(id) {
        $('#late_remove_attendance_id').val(id);
        /*swal({
            title: "Are you sure you want to change ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            window.location.href = $(e).attr('data-href');
        });*/
    }
    function getPunchData(route)
    {

        $('#model_data').html('');
        $.ajax({
            url: route,
            type: "GET",
            dataType: "html",
            catch : false,
            success: function (data) {
                $('#model_data').append(data);
            }
        });

    }

    function setTime(thisObject) {

        var minValue = $(thisObject).data('min');
        var maxValue = $(thisObject).data('max');
        var type = $(thisObject).data('type');
        var id = $(thisObject).data('id');

        var hours = $(thisObject).parents('td.block').find('.hours').val();
        var minutes = $(thisObject).parents('td.block').find('.minutes').val();
        var meridian = $(thisObject).parents('td.block').find('.meridian').val();
        var curr_time = hours + ':' + minutes + ' ' + meridian;

        if (get24Hr(curr_time) > get24Hr(minValue) && get24Hr(curr_time) < get24Hr(maxValue)) {

            $.ajax({
                url: '<?php echo url('set_punch_data') ?>' + '/' + id + '/' + curr_time + '/' + type,
                type: "GET",
                dataType: "html",
                catch : false,
                success: function (data) {
                    $(thisObject).parents('td.block').html(curr_time);
                }
            });

        } else {
            $(thisObject).parents('td.block').addClass('has-error');
            $(thisObject).parents('td.block').find('.condition').removeClass('hide');
        }
    }

    function get24Hr(curr_time) {

        var hours = Number(curr_time.match(/^(\d+)/)[1]);
        var AMPM = curr_time.match(/\s(.*)$/)[1];
        if (AMPM == "PM" && hours < 12)
            hours = hours + 12;
        if (AMPM == "AM" && hours == 12)
            hours = hours - 12;

        var minutes = Number(curr_time.match(/:(\d+)/)[1]);
        hours = hours * 100 + minutes;
        console.log(curr_time + " - " + hours);

        return hours;
    }

</script>
@endsection
