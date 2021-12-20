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
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
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

                <a href="{{ route('admin.expired_emp_insurances_list') }}" class="btn btn-danger pull-left"><i class="fa fa-history"></i> Expired Insurances</a>
                <a href="{{ route('admin.add_employee_insurance') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Insurance</a>

                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="emp_insurance_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Company </th>
                                <th>Insurance Type</th>
                                <th>Insurance Company</th>
                                <th>Policy Number</th>
                                <th>Agent</th>
                                <th>Contact Number</th>
                                <th>Contact Email</th>
                                <th>Amount</th>
                                <th>Insurance Date</th>
                                <th>Expiration Date</th>
                                <th>Upload Policy</th>
                                <th data-orderable="false">History</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if(count($emp_insurances) > 0)
                            @foreach($emp_insurances as $insurance)
                            <tr>
                                <td>{{$insurance->user_name}}</td>
                                <td>{{$insurance->user_company}}</td>
                                <td> {{ $insurance->title }}</td>
                                <td>{{$insurance->company_name}}</td>
                                <td>{{$insurance->policy_number}}</td>
                                <td>{{$insurance->agent_name}}</td>
                                <td>{{$insurance->contact_number}}</td>
                                <td>@if($insurance->contact_email)
                                    {{$insurance->contact_email}}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>{{$insurance->amount}}</td>

                                <td> {{ Carbon\Carbon::parse($insurance->insurance_date)->format('d-m-Y')}}</td>
                                <td class="{{ $insurance->color_class }}">
                                    {{ Carbon\Carbon::parse($insurance->renew_date)->format('d-m-Y')}}
                                    <br>
                                    {{ $insurance->left_day }} day left
                                </td>
                                <td>
                                    @if(count($insurance->get_insurance_policy))
                                        @foreach($insurance->get_insurance_policy as $key => $value)
                                            @php
                                            $main_path = str_replace('public','',$value['attachment']);
                                            $storage_path = url('storage/')."".$main_path;
                                            @endphp
                                            <a href="{{$storage_path}}" target="_blank">
                                                <i class="fa fa-cloud-download fa-lg"></i>
                                            </a>
                                        @endforeach
                                    @endif
                                </td>
                                <!-- This above field is recently added -->
                                <td>

                                    <a href="{{ route('admin.employee_insurances_history',['id'=>$insurance->employee_id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-eye"></i></a>
                                    <br>
                                    <a href="#" onclick="get_reminderDates({{ $insurance->id }});" title="Reminder Dates" data-target="#reminderDateModel" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-calendar"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div id="reminderDateModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" id="myModalLabel">Reminder Dates</h4>
                            </div>
                            <br>
                            <br>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="date_table">

                                </tbody>
                            </table>

                        </div>
                        <!-- /.modal-content -->
                    </div>
                </div>
            </div>
            <!--row -->

        </div>
    </div>


</div>
@endsection


@section('script')
<script>
    $(document).ready(function() {
        $('#emp_insurance_table').DataTable({
            dom: 'lBfrtip',
            buttons: ['excel'],
        });


    });
</script>
<script>
    function get_reminderDates(id) {
        trHTML = '';
        $.ajax({
            url: "{{ route('admin.emp_insurance_reminder_dates') }}",
            type: "post",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: id
            },
            success: function(data) {
                if (data.status) {
                    $('#date_table').empty();
                    var reminderDates = data.data.reminder_dates;
                    if (reminderDates.length == 0) {

                        $('#date_table').empty();
                        $('#date_table').append('<span>No Records Found !</span>');

                    } else {
                        $.each(reminderDates, function(index, files_obj) {

                            no = index + 1;
                            date = moment(files_obj).format("DD-MM-YYYY");
                            trHTML += '<tr>' +
                                '<td>' + no + '</td>' +
                                '<td>' + date + '</td>' +
                                '</tr>';
                        });
                        $('#date_table').append(trHTML);
                    }

                } else {
                    $('#date_table').empty();
                    $('#date_table').append('<span>No Records Found !</span>');
                }

            }
        });

    }

</script>
@endsection