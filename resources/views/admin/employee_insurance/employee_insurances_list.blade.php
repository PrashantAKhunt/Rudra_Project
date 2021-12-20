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
                <li><a href="{{ route('admin.employees_insurances') }}">{{ $module_title }}</a></li>
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
            <button type="button" onclick="window.location.href ='{{ route('admin.employees_insurances') }}'" class="btn btn-info pull-right"><i class="fa fa-arrow-left"></i> BACK</button>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="emp_insurances_table" class="table table-striped">
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
                                <th data-orderable="false" >Status</th>
                                <th data-orderable="false" >View</th>

                            </tr>
                        </thead>
                        <tbody>
                        @if( count($insurance_list) > 0)
                        @foreach($insurance_list as $list)
                            <tr>
                            <td>{{$list->user_name}}</td>
                                <td>{{$list->user_company}}</td>
                                <td> {{ $list->title }}</td>
                                <td>{{$list->company_name}}</td>
                                <td>{{$list->policy_number}}</td>
                                <td>{{$list->agent_name}}</td>
                                <td>{{$list->contact_number}}</td>
                                <td>@if($list->contact_email)
                                  {{$list->contact_email}}
                                  @else
                                  N/A
                                  @endif
                                </td>
                                <td>{{$list->amount}}</td>

                                <td> {{ Carbon\Carbon::parse($list->insurance_date)->format('d-m-Y')}}</td>

                                <td class="{{ $list->color_class }}">

                                 {{ Carbon\Carbon::parse($list->renew_date)->format('d-m-Y')}}
                                 <br>
                                 @if($list->status == 'Live')
                                 {{ $list->left_day }} day left
                                 @endif
                                 </td>
                                @if($list->status == 'Expired')
                                <td>
                                    <span class="label label-rouded label-danger">EXPIRED</span>
                                </td>
                                @else
                                <td><span class="label label-rouded label-warning">LIVE</span></td>
                                @endif

                                <td>
                                <a href="#" onclick="get_reminderDates({{ $list->id }});" title="Reminder Dates" data-target="#reminderDateModel" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-calendar"></i></a>
                                </td>

                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <!--  -->
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
                <!--  -->
            </div>
            <!--row -->

        </div>
    </div>


</div>
@endsection


@section('script')
<script>
    $(document).ready(function() {
        $('#emp_insurances_table').DataTable({
            dom: 'lBfrtip',
            buttons: ['excel'],
        });
    });

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

                            }else{
                                    $.each(reminderDates, function(index, files_obj) {

                                        no = index + 1;
                                        date = moment(files_obj).format("DD-MM-YYYY");
                                        trHTML += '<tr>' +
                                           '<td>' + no + '</td>' +
                                            '<td>' + date + '</td>'+
                                            '<tr/>';
                                        });
                                $('#date_table').append(trHTML);
                            }

                        }else{
                            $('#date_table').empty();
                            $('#date_table').append('<span>No Records Found !</span>');
                        }

                    }
                });

}
</script>


@endsection
