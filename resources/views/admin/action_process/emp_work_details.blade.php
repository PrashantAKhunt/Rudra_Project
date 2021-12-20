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

                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="emp_work_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Employee Name</th>
                                <th>Time Interval For Complete Task (Day / Hour)</th>
                                <th>Work Percentage/Hour</th>
                                <th>Work Start Datetime</th>
                                <th>Work Details</th>
                                <th>Document</th>
                                <th>Work Submit Datetime</th>
                                <th>Acceptance Status</th>
                                <th>Work Submit Status</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                           @if($emp_details->count() > 0)
                            @foreach($emp_details as $list_data)
                            <tr>
                               <td>{{$list_data->emp_name}}</td>
                               <td>
                               @if($list_data->work_day)
                                 {{$list_data->work_day}} day / {{$list_data->work_hour}} Hour
                               @endif
                               </td>
                               <td>
                               @if($list_data->task_percentage)
                                  {{$list_data->task_percentage}} % /  {{$list_data->task_hour}}H
                               @endif
                               </td>

                               <td>
                               @if($list_data->working_start_datetime)
                                  {{ date('d-m-Y h:i a', strtotime($list_data->working_start_datetime)) }}</td>
                                @endif
                               </td>
                                
                                <td>{{$list_data->work_details}}</td>
                                <td>
                                    @if($list_data->work_document)
                                    <a title="Download" href="{{ asset('storage/'.str_replace('public/','',!empty($list_data->work_document) ? $list_data->work_document : 'public/no_image')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a>
                                    @endif
                               
                                </td>
                                <td> @if($list_data->work_datetime)
                                    {{ Carbon\Carbon::parse($list_data->work_datetime)->format('d-m-Y h:i a') }}
                                    @endif
                                </td>
                                    
                                <td>
                                @if($list_data->emp_status == 'Assigned')
                                <b class="text-warning">
                                      {{ $list_data->emp_status }}</b>
                                @elseif($list_data->emp_status == 'Rejected')
                                     
                                       <textarea style="display:none;" id="reject__detail_{{ $list_data->id }}">{{ $list_data->general_reason ? $list_data->general_reason : $list_data->satisfied_reason }}</textarea>
                                       <button class="btn btn-danger btn-rounded" data-toggle="modal" data-target="#reject_detail_modal" onclick="reject_assign_reason('{{ $list_data->id }}')">Rejected</button>    
                                @else
                                <b class="text-success">
                                       {{ $list_data->emp_status }}</b>     
                                @endif
                                </td>

                                <td>
                                @if($list_data->work_status == 'Processing')
                                <b class="text-warning">
                                      {{ $list_data->work_status }}</b>
                                @elseif($list_data->work_status == 'Submitted')
                                <b class="text-info">
                                       {{ $list_data->work_status }}</b>
                                @elseif($list_data->work_status == 'Accepted')
                                <b class="text-success">
                                       {{ $list_data->work_status }}</b>
                                @else
                                <b class="text-danger">
                                       {{ $list_data->work_status }}</b>     
                                @endif
                                </td>
                                <td>
                                @if($list_data->work_status == 'Submitted')
                                    <a class="btn btn-danger btn-sm btn-rounded" href="#" onclick="reject_request(`{{ $list_data->id }}`)" data-toggle="modal" data-target="#reject_note_modal"><i class="fa fa-times"></i></a>
                                    <a onclick="accept_request(this);" class="btn btn-success btn-sm btn-rounded" href="#" data-href="{{ route('admin.accept_emp_work',['id'=> $list_data->id ]) }}"  title="Accept"><i class="fa fa-check"></i></a>
                                @endif
                                @if($list_data->final_status == 'Pending') 
                                    @if($list_data->emp_status == 'Rejected' && !empty($list_data->satisfied_reason) )
                                        <a onclick="accept_emp(this);" class="btn btn-success btn-sm btn-rounded" href="#" data-href="{{ route('admin.acceptEmpRequest',['id'=> $list_data->id ]) }}"  title="Accept"><i class="fa fa-check"></i></a>
                                        <a class="btn btn-danger btn-sm btn-rounded" title="Reject" href="#" onclick="reject_emp(`{{ $list_data->id }}`)" data-toggle="modal" data-target="#reject_final_modal"><i class="fa fa-times"></i></a>
                                    @elseif($list_data->emp_status == 'Rejected' && !empty($list_data->general_reason))
                                        <a onclick="remoe_emp(this);" class="btn btn-primary btn-sm btn-rounded" href="#" data-href="{{ route('admin.removeEmp',['id'=> $list_data->id ]) }}"  title="Remove Employee"><i class="fa fa-trash "></i></a>
                                    @endif
                                @endif 
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
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
                                                <form method="post" action="{{ route('admin.reject_emp_work') }}" >
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <input type="hidden" name="distrubuted_work_id" id="distrubuted_work_id" value="">
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
                <!-- End Reject Model -->
                <!-- Reject detail -->

                <div id="reject_detail_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" id="myModalLabel">Reject reason</h4>
                            </div>

                            <div class="modal-body" id="reject_reason">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /Reject detail -->
                <!-- Reject Model -->
                <div id="reject_final_modal" class="modal fade">
                                <div class="modal-dialog">
                                    <div class="modal-content" id="model_data">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h3 class="panel-title">Note: Please mention rejected reason.</h3>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="{{ route('admin.rejectEmpRequest') }}" >
                                                @csrf
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <input type="hidden" name="distrubuted_id" id="distrubuted_id" value="">
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
                <!-- End Reject Final Model -->
            </div>
            <!--row -->

        </div>
        
        @endsection

        @section('script')
        <script>
        //=====================================
        $(document).ready(function(){
            $('#emp_work_table').DataTable({
                    stateSave:true
                });
        });
        //=====================================
        function reject_assign_reason(id){

            // var txt = '<p>-> Employee has query regarding percentage of work you had assigned to him.<p>-> Expected Percentage of work by employee:'+ ' ' + $('#reject__detail_'+id).text();
            //     txt+= '<br><br>-> So Please Accept or Reject his query so system can proceed accordingly.';
                
                $('#reject_reason').html($('#reject__detail_'+id).text());
            }

        function reject_emp(id) {
            $('#distrubuted_id').val(id);
        }
        //=====================================
        function accept_request(e) {
                swal({
                    title: "Are you sure you want to accept this Employee work ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }
        //======================================
        function accept_emp(e) {
                swal({
                    title: "Are you sure you want to accept this Employee Request ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }
        //=====================================
        function remoe_emp(e) {
                swal({
                    title: "Are you sure you want to remove this Employee from registry work ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }
        //===================================        
        function reject_request(id) {
                $('#distrubuted_work_id').val(id);
            }
        //=====================================        
        </script>
        @endsection