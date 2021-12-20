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
                <h4>Prime Action for Work related to Documents</h4>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="process_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Inward Registry No</th>
                                <th>Inward Title</th>
                                <th>Assume the Time Ex.(Week->Days->Hour)</th>
                                <th>Action Plan Detail</th>
                                <th>Support Required from Department</th>
                                <th width="300px">Support Required from Employee <br>
                                    <h5> Name -> Percentage -> Hour -> Acceptance Status</h5></th>
                                <th>Inward Document</th>
                                <th>Status</th>
                                <th width="120px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($prime_list->count() > 0)
                            @foreach($prime_list as $list_data)
                            <tr>

                               <td>{{ $list_data->inward_outward_no }}</td>
                               <td>{{ $list_data->inward_outward_title }}</td>
                               <td>
                                @if($list_data->assume_work_hour)
                                 {{ $list_data->assume_work_type }} -> {{ $list_data->assume_work_time }} -> {{ $list_data->assume_work_hour }}
                                @else
                                 {{ $list_data->assume_work_type }} -> {{ $list_data->assume_work_time }}
                                @endif
                               </td>
                               <td>{{ $list_data->work_details }}</td>
                               <td>

                               @if( count($list_data->emp_distrubution) > 0 )
                                  {{ $list_data->emp_distrubution[0]['depart_name'] }}
                               @endif
                               </td>
                               <td>
                               @if( count($list_data->emp_distrubution) > 0 )
                               @foreach($list_data->emp_distrubution as $key => $list)

                                  <h5> {{ $list->name }} -> {{ $list->task_percentage }}% -> {{ $list->task_hour }}H -> {{ $list->emp_status }} </h5>

                               @endforeach
                               @endif
                               </td>
                               <td>
                                    <a title="Download" href="{{ asset('storage/'.str_replace('public/','',!empty($list_data->document_file) ? $list_data->document_file : '')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a>
                                </td>
                               <th>{{ $list_data->prime_user_status }}</th>
                                <td>
                                @if(  $list_data->prime_user_status == 'Assigned' )
                                    <a class="btn btn-danger btn-sm btn-rounded" href="#" onclick="reject_request(`{{ $list_data->inward_id }}`)" data-toggle="modal" data-target="#reject_note_modal"><i class="fa fa-times"></i></a>
                                    <a onclick="accept_request(this);" class="btn btn-success btn-sm btn-rounded" href="#" data-href="{{ route('admin.accept_requestByPrimeUser',['id'=> $list_data->inward_id ]) }}"  title="Accept"><i class="fa fa-check"></i></a>
                                @else
                                   @if(  $list_data->final_status == 'Pending' )
                                    <a href="{{ route('admin.add_distrubuted_details',['id'=>$list_data->id]) }}" title="Edit"  class="btn btn-primary btn-rounded"> <i class="glyphicon glyphicon-edit"></i></a>
                                    @endif
                                    <a href="{{ route('admin.get_emp_work_details',['id'=>$list_data->id]) }}" title="Employee Work Details"  class="btn btn-info btn-sm btn-rounded"> <i class="fa fa-eye"></i></a>
                                @endif
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

        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="white-box">


                <h4>Work assigned as Supporting/Prime Role</h4>
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="support_emp_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Inward Registry No</th>
                                <th>Inward Title</th>
                                <th>Prime/Main User</th>
                                <th> Work Detail</th>
                                <th>Work Percentage -> Hour</th>
                                <th>Work Start Datetime</th>
                                <th>Inward Document</th>
                                <th>Work Status</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($requests_list->count() > 0)
                            @foreach($requests_list as $list)
                            <tr>
                                <td>{{ $list->inward_outward_no  }}</td>
                                <td>{{ $list->inward_outward_title  }}</td>
                                <td>{{ $list->prime_user  }}</td>
                                <td>{{ $list->work_details  }}</td>
                                <td>{{ $list->task_percentage  }} % -> {{ $list->task_hour }}H</td>
                                <td>
                                @if($list->working_start_datetime)
                                  {{ date('d-m-Y h:i a', strtotime($list->working_start_datetime)) }}</td>
                                @endif
                                <td>
                                    <a title="Download" href="{{ asset('storage/'.str_replace('public/','',!empty($list->document_file) ? $list->document_file : '')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a>
                                </td>
                                <td>
                                @if($list->work_status == 'Processing')
                                <b class="text-warning">
                                      {{ $list->work_status }}</b>
                                @elseif($list->work_status == 'Submitted')
                                <b class="text-info">
                                       {{ $list->work_status }}</b>
                                @elseif($list->work_status == 'Accepted')
                                <b class="text-success">
                                       {{ $list->work_status }}</b>
                                @else
                                <b class="text-danger">
                                       {{ $list->work_status }}</b>
                                @endif
                                </td>
                                <td>
                                    @if( $list->emp_status == 'Assigned' )
                                        <a class="btn btn-danger btn-sm btn-rounded" href="#" onclick="reject_emp_request(`{{ $list->id }}`)" data-toggle="modal" data-target="#reject_emp_modal"><i class="fa fa-times"></i></a>
                                        <a onclick="accept_emp_request(this);" class="btn btn-success btn-sm btn-rounded" href="#" data-href="{{ route('admin.accept_requestBySupportEmp',['id'=> $list->id ]) }}"  title="Accept"><i class="fa fa-check"></i></a>
                                    @elseif( $list->emp_status == 'Accepted' )
                                        @if( empty($list->work_day) )
                                            <a class="btn btn-success btn-rounded" href="#" title="Add Your Work Plan" onclick="set_hour(`{{ $list->id }}`)" data-toggle="modal" data-target="#set_work_interval"><i class="fa fa-history"></i></a>
                                        @endif
                                        <!-- date('Y-m-d H:i:s') -->

                                        @if( !empty($list->work_day) && strtotime(date('Y-m-d', strtotime($list->working_start_datetime))) <= strtotime(date('Y-m-d')) )

                                            @if( $list->reliever_user_id == Auth::user()->id  &&  $list->reliever_dates )

                                                @if( strtotime($list->reliever_dates[0]) <= strtotime(date('Y-m-d')) && strtotime($list->reliever_dates[1]) >= strtotime(date('Y-m-d')) )
                                                <a href="{{ route('admin.edit_emp_work',['id'=>$list->id]) }}" title="Submit Your Work"  class="btn btn-primary btn-rounded"> <i class="fa fa-paper-plane "></i></a>
                                                @endif
                                            @elseif( $list->support_employee_id == Auth::user()->id )

                                                <a href="{{ route('admin.edit_emp_work',['id'=>$list->id]) }}" title="Submit Your Work"  class="btn btn-primary btn-rounded"> <i class="fa fa-paper-plane "></i></a>
                                            @endif
                                        @endif
                                    @endif

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
            <!-- Reject Model -->
            <div id="reject_note_modal" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content" id="model_data">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h3 class="panel-title">Note: Please mention rejected reason.</h3>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('admin.reject_requestByPrimeUser') }}" >
                                            @csrf
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <input type="hidden" name="inward_id" id="inward_id" value="">
                                                    <label for="reject_reason">Reject Note</label>
                                                    <textarea name="reject_reason" id="reject_reason" value="" class="form-control" required></textarea>
                                                </div>
                                            </div>
                                                <br>
                                            <div class="row">
                                                <div class="col-xs-2">

                                                    <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
            </div>
            <!-- End Reject Model -->
             <!-- Reject Model -->
             <div id="reject_final_modal" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content" id="model_data">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h3 class="panel-title">Note: Please mention rejected reason.</h3>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('admin.reject_distrubutionPrimeUser') }}" >
                                            @csrf
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <input type="hidden" name="prime_table_id" id="prime_table_id" value="">
                                                    <label for="reject_note">Reject Note</label>
                                                    <textarea name="reject_note" id="reject_note" value="" class="form-control" required></textarea>
                                                </div>
                                            </div>
                                                <br>
                                            <div class="row">
                                                <div class="col-xs-2">

                                                    <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
            </div>
            <!-- End Reject Final Model -->
             <!-- Reject EMP Model -->
             <div id="reject_emp_modal" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content" id="model_data">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h3 class="panel-title">Note: Please mention rejected reason.</h3>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('admin.reject_requestBySupportEmp') }}" >
                                            @csrf
                                            <div class="row">
                                            <div class="form-group">
                                            <label class="radio-inline">
                                                <input type="radio" name="check_btn" checked value="general_reason">General Reson
                                                </label>
                                                <label class="radio-inline">
                                                <input type="radio" name="check_btn" value="satisfied_reason">Work Assigned Percentage Query
                                                </label>
                                            </div>
                                            </div>
                                            <div class="row desc" id="general_reason">
                                                <div class="col-xs-12">
                                                    <input type="hidden" name="distrubuted_work_id" id="distrubuted_work_id" value="">
                                                    <label for="general">General Reason</label>
                                                    <textarea name="general_reason" id="general" value="" class="form-control text_field" ></textarea>
                                                </div>
                                            </div>

                                            <div class="row desc" style="display:none" id="satisfied_reason">
                                                <div class="col-xs-12">
                                                    <label for="satisfied">Please provide percentage of work you are expecting</label>
                                                    <input type="number" name="satisfied_reason" id="satisfied" value="" class="form-control text_field" >
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-xs-2">
                                                    <button type="submit" id="submit_btn" name="submit" disabled="disabled" class="btn btn-success btn-block">Submit</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
            </div>
            <!-- End Reject EMP Model -->
            <!-- Set hour and day Model -->
            <div id="set_work_interval" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content" id="model_data">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h3 class="panel-title">Note: Please mention your work plan in days and hours in below form.</h3>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('admin.update_workInterval') }}" >
                                            @csrf
                                            <div class="row">

                                                <div class="col-md-12">
                                                    <p id="set_hour"> </p>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                            <input type="hidden" name="work_id" id="work_id" value="">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>Working Day</label>
                                                        <input type="number" class="form-control" name="work_day" id="work_day" required value="" />
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-md-5">
                                                    <div class="form-group ">
                                                        <label>Working Hour</label>
                                                        <input type="number" class="form-control" placeholder="0.00" step="0.01" name="work_hour" id="work_hour" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>Working Start Datetime</label>
                                                        <input type="text" class="form-control" name="working_start_datetime" required id="working_start_datetime" value="{{date('d-m-Y h:i A')}}"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-2">
                                                    <button type="submit" id="submit_btn" name="submit" class="btn btn-success btn-block">Submit</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
            </div>
            <!-- hour and day Model -->

        @endsection

        @section('script')
        <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
        <script>
        function set_hour(id) {
            $('#work_id').val(id);
            $.ajax({
                    url: "{{ route('admin.get_hourByPercentage') }}",
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {id: id},
                    dataType: "JSON",
                    success: function(data) {
                        let set_hr = data.set_hour + ' ' + 'Hour';
                        let dy_hour = 'Total Hour provide by Main/Prime User is:' + set_hr;
                        $('#set_hour').empty();
                        $('#set_hour').append(dy_hour);
                        $('#work_day').val(data.work_day);
                        $('#work_hour').val(data.work_hour);
                    }
                });
        }
        //==============================================
        $(document).ready(function(){
            $('#working_start_datetime').datetimepicker({
                format: 'DD-MM-YYYY h:mm a'
            });
            $('#process_table').DataTable({
                    stateSave:true
                });
        //==============================================
            $('#support_emp_table').DataTable({
                stateSave:true
            });
        });
         //==============================================
        $("input[name='check_btn']").click(function() {
            var test = $(this).val();

            $("div.desc").hide();
            $("#" + test).show();

        });
        //=============================================
        $('.text_field').on('keyup', function() {
            var empty = false;
            if ($(this).val().length == 0) {
                empty = true;
            }
            if (empty)
            $('#submit_btn').attr('disabled', 'disabled');
            else
            $('#submit_btn').attr('disabled', false);
        });
        //==============================================
            function accept_request(e) {
                swal({
                    title: "Are you sure you want to accept this Registry Document request ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }
        //================================================
            function accept_emp_request(e) {
                swal({
                    title: "Are you sure you want to accept this Work related request ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }
        //================================================
            function reject_request(id) {
                $('#inward_id').val(id);
            }
        //==================================================
            function reject_emp_request(id) {
                $('#distrubuted_work_id').val(id);
            }
        //=================================================
        function reject_final_request(id) {
                $('#prime_table_id').val(id);
            }
        //====================================================
        </script>
        @endsection
