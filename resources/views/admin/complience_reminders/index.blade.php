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

                @if($compliance_add_permission)
                <a href="{{ route('admin.add_compliance_reminder') }}" class="btn btn-primary pull-right" ><i class="fa fa-plus"></i> Add Compliance Reminder</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="compliance_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">Company</th>
                                <th rowspan="2">Compliance Category</th>
                                <th rowspan="2">Compliance Name</th>
                                <th rowspan="2">Compliance Description</th>
                                <th rowspan="2">Periodicity Type</th>
                                <th rowspan="2">Start Date</th>
                                <th rowspan="2">End Date</th>
                                <th rowspan="2">Periodic Due-time</th>
                                <th rowspan="2">Responsible Person</th>
                                <th rowspan="2">Payment Responsible Person</th>
                                <th rowspan="2">Checker</th>
                                <th rowspan="2">Super Admin Varification</th>
                                <th colspan="3">Reminder Schedule</th>
                                <th rowspan="2" >Cretaed Date</th>
                                <th rowspan="2">Action</th>
                            </tr>
                            <tr>
                                <td>Before Days</td>
                                <td>Before Days</td>
                                <td>Before Days</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $list_data)
                            <tr>
                                <td>{{$list_data->company_name}}</td>
                                <td>{{$list_data->compliance_type}}</td>
                                <td>{{$list_data->compliance_name}}</td>
                                <td>{{$list_data->compliance_description}}</td>
                                <td>{{$list_data->periodicity_type}}</td>
                                <th>
                                    {{ date('d-m-Y',strtotime( $list_data->start_date )) }}
                                </th>
                                <th>
                                    {{ date('d-m-Y',strtotime( $list_data->end_date )) }}
                                </th>
                                <th>
                                    {{  date('h:i A',strtotime( $list_data->periodicity_time )) }}
                                </th>
                                <td>{{$list_data->responsible_person}}</td>
                                <td>{{$list_data->payment_responsible}}</td>
                                <td>{{$list_data->checker}}</td>
                                <td>{{$list_data->super_admin_checker}}</td>

                                <td>{{$list_data->first_day_interval}}</td>
                                <td>{{$list_data->second_day_interval}}</td>
                                <td>{{$list_data->third_day_interval}}</td>

                                <th>
                                    {{ date('d-m-Y',strtotime( $list_data->created_at )) }}
                                </th>
                                <td>
                                    @if($compliance_edit_permission)
                                    <a href="{{ route('admin.edit_compliance_reminder',['id'=> $list_data->id]) }}" title="Edit"  class="btn btn-primary btn-rounded"> <i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>

        @endsection

        @section('script')
        <script>
            $('#compliance_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    stateSave:true
                });
        </script>
        @endsection

