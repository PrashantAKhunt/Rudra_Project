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
           
            <h3 class="box-title ">These are not get renewal yet... </h3>
                <p class="text-muted m-b-30"></p>
          
                <div class="table-responsive">
                    <table id="vehicle_table" class="table table-striped">
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
                                <th data-orderable="false" >RENEWAL</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        @if( count($exp_insurance_list) > 0)
                        @foreach($exp_insurance_list as $list)
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
                                
                                <td> {{ Carbon\Carbon::parse($list->renew_date)->format('d-m-Y')}}</td>
                             
                                <td>
                                <a href="{{ route('admin.renew_expired_employee_insurance',['id'=>$list->id ]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-refresh"></i></a>
                                </td>
                              
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
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
        $('#vehicle_table').DataTable();
        $(document).ready(function() {



        });
    });
</script>


@endsection