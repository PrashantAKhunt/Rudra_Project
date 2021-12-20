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



                                <th rowspan="2">Responsible Person Name</th>
                                <th rowspan="2">Payment Responsible Person Name</th>
                                <th rowspan="2">Checker Name</th>

                                <th rowspan="2">Responsible Person Status</th>
                                <th rowspan="2">Responsible Person Payment Copy</th>
                                <th rowspan="2">Payment Responsible Person Status</th>
                                <th rowspan="2">Payment Responsible Person Payment Done Copy</th>
                                <th rowspan="2">Checker Status</th>
                                <th rowspan="2">Due Date-time</th>

                                <th colspan="3">Reminder Schedule</th>

                                <th rowspan="2" >Action</th>

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

                                <td>{{ date('d-m-Y',strtotime( $list_data->start_date )) }}</td>
                                <td>{{ date('d-m-Y',strtotime( $list_data->end_date )) }}</td>
                                <td>
                                   {{  date('h:i A',strtotime( $list_data->periodicity_time )) }}
                                </td>

                                <td>{{$list_data->responsible_person}}</td>
                                <td>{{$list_data->payment_responsible}}</td>
                                <td>{{$list_data->checker}}</td>



                                <td>
                                @if(  $list_data->responsible_person_status == 'Pending' )
                                    <p class="text-warning">Pending</p>
                                @else
                                     <p class="text-success">Completed</p>
                                @endif

                                </td>
                                <td>
                                    @if ($list_data->responsible_person_attachment)
                                        <a download="" title="Download File" href="{{asset('storage/'.str_replace('public/','',$list_data->responsible_person_attachment))}}" class="btn btn-rounded btn-primary"><i class="fa fa-download"></i></a>
                                    @endif
                                </td>
                                <td>
                                @if(  $list_data->payment_responsible_person_status == 'Pending' )
                                    <p class="text-warning">Pending</p>
                                @else
                                     <p class="text-success">Completed</p>
                                @endif

                                </td>
                                <td>
                                    @if ($list_data->payment_responsible_person_attachment)
                                        <a download="" title="Download File" href="{{asset('storage/'.str_replace('public/','',$list_data->payment_responsible_person_attachment))}}" class="btn btn-rounded btn-primary"><i class="fa fa-download"></i></a>
                                    @endif
                                </td>
                                <td>
                                @if(  $list_data->checker_status == 'Pending' )
                                    <p class="text-warning">Pending</p>
                                @else
                                     <p class="text-success">Completed</p>
                                @endif
                                </td>
                                <td>{{ date('d-m-Y',strtotime( $list_data->remind_entry_date )).' '.date('h:i A',strtotime( $list_data->remind_entry_time )) }}</td>

                                <td>{{$list_data->first_day_interval}}</td>
                                <td>{{$list_data->second_day_interval}}</td>
                                <td>{{$list_data->third_day_interval}}</td>




                                <td>

                                @if($list_data->responsible_person_id == Auth::user()->email && $list_data->responsible_person_status == 'Pending')
                                    <a href="javascript:void(0)" onclick="done_compliance(this);" data-id="{{$list_data->id}}" data-type="responsible_person_status" data-href="{{ route('admin.complete_compliance_reminder',['id'=>$list_data->id,'type'=>'responsible_person_status']) }}" class="btn btn-danger">Mark as Done</a>
                                @elseif($list_data->payment_responsible_person_id == Auth::user()->email && $list_data->responsible_person_status == 'Completed' &&  $list_data->payment_responsible_person_status == 'Pending')
                                       <a href="javascript:void(0)" onclick="done_compliance(this);" data-id="{{$list_data->id}}" data-type="payment_responsible_person_status" data-href="{{ route('admin.complete_compliance_reminder',['id'=>$list_data->id,'type'=>'payment_responsible_person_status']) }}" class="btn btn-danger">Mark as Done</a>
                                @elseif($list_data->checker_id == Auth::user()->email && $list_data->responsible_person_status == 'Completed' &&  $list_data->payment_responsible_person_status == 'Completed' && $list_data->checker_status == 'Pending')
                                       <a href="javascript:void(0)" onclick="done_compliance(this);" data-id="{{$list_data->id}}" data-type="checker_status" data-href="{{ route('admin.complete_compliance_reminder',['id'=>$list_data->id,'type'=>'checker_status']) }}" class="btn btn-danger">Mark as Done</a>
                                @elseif($list_data->super_admin_checker_id == Auth::user()->email && $list_data->responsible_person_status == 'Completed' &&  $list_data->payment_responsible_person_status == 'Completed' && $list_data->checker_status == 'Completed' && $list_data->super_admin_checker_status == 'Pending')
                                       <a href="javascript:void(0)" onclick="done_compliance(this);" data-id="{{$list_data->id}}" data-type="super_admin_checker_status" data-href="{{ route('admin.complete_compliance_reminder',['id'=>$list_data->id,'type'=>'super_admin_checker_status']) }}" class="btn btn-danger">Mark as Done</a>
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
        <!-- Modal -->
        <div class="modal fade" id="done_compliance" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Done Compliance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.complete_compliance_reminder')}}" method="post" enctype="multipart/form-data" id="complete_compliance_reminder_form">
                        @csrf
                        <div class="form-group ">
                            <input type="hidden" name="id" id="id">
                            <input type="hidden" name="type" id="type">
                            <label id="attech_text">Attachment</label>
                            <input type="file" class="form-control" name="file_attachment" id="file_attachment" required/>
                        </div>
                        <div class="form-group ">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
        @endsection

        @section('script')
        <script>
            $('#compliance_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    stateSave:true
                });
            function done_compliance(e) {
                var id = $(e).attr('data-id');
                var type = $(e).attr('data-type');
                var attechment_text = "Attechment";

                $("#id").val(id);
                $("#type").val(type);

                if(type == "responsible_person_status"){
                    attechment_text = "Payment Copy";
                    $("#attech_text").html(attechment_text);
                    $("#done_compliance").modal('show');
                }else if(type == "payment_responsible_person_status"){
                    attechment_text = "Payment Done Copy";
                    $("#attech_text").html(attechment_text);
                    $("#done_compliance").modal('show');
                }else{
                    $("#complete_compliance_reminder_form").submit();
                }

                /* swal({
                    title: "Are you sure you want to mark this compliance as done?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                }); */
            }

            $("#complete_compliance_reminder_form").validate();
        </script>
        @endsection
