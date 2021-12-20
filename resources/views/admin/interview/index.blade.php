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
                <?php if(in_array(3, $role)){
                    if(Auth::user()->role==config('constants.REAL_HR')){ ?>
                    <a href="{{ route('admin.add_interview') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Interview Detail</a>
                <?php } 
                } ?>
                <p class="text-muted m-b-30"></p>
                <br>                
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Interviewee Id</th>
                                <th>Designation</th>
                                <th>Job Id</th>
                                <th>Contact Number</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Confirm</th>
                                <th>Schedule</th>
                                <th>Marks</th>
                                <th>Details</th>
                                <th>Candidate Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($interview_list)>0)
                            @foreach($interview_list as $interview)
                            <tr>
                                <td>{{ $interview->name }}</td>
                                <td>{{ $interview->interviewee_id }}</td>
                                <td>{{ $interview->title }}</td>
                                <td>{{ $interview->job_id }}</td>
                                <td>{{ $interview->contact_number }}</td>
                                <td>
                                    @if($interview->emp_status=='pending')
                                    <b class="text-warning">Pending</b>
                                    @elseif($interview->emp_status=='selected')
                                    <b class="text-success">Selected</b>
                                    @elseif($interview->emp_status=='completed')
                                    <b class="text-info">Completed</b>
                                    @elseif($interview->emp_status=='hold')
                                    <b class="text-primary">Hold</b>
                                    @else
                                    <b class="text-danger">Rejected</b>
                                    @endif
                                </td>
                                <td>
                                    <?php if(in_array(2, $role) && in_array(5, $role)){
                                        if($interview->emp_status != 'selected'){ ?>
                                            <a href="{{ route('admin.edit_interview',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary" title="Edit Interview"><i class="fa fa-edit"></i></a>
                                    <?php } } ?>
                                </td>
                                <td>
                                    <?php if(in_array(5, $role)){
                                        if($interview->emp_status == 'selected'){ ?>
                                        <a href="{{ route('admin.confirm_interview',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary" title="Confirm Interview"><i class="fa fa-check"></i></a>
                                    <?php } } ?>
                                </td>
                                <td>
                                    <?php if(in_array(5, $role) && $interview->emp_status == 'pending'){ ?>
                                        <a href="{{ route('admin.add_next_interview',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary" title="Schedule Interview"><i class="fa fa-calendar"></i></a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if(in_array(Auth::user()->id, explode(',',$interview->interviewers)) && $interview->emp_status = 'pending'){ ?>
                                        <a href="{{ route('admin.interview_marks',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary" title="Interview Marks"><i class="fa  fa-file-text-o"></i></a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if(in_array(6, $role)){ ?>
                                        <a href="{{ route('admin.interview_details',['id'=>$interview->id]) }}" class="btn btn-rounded btn-primary" title="Interview Details"><i class="fa fa-file"></i></a>
                                    <?php } ?>
                                </td>
                                <td>
                                <a href="#" title="Candidate Info" onclick="user_detail({{ $interview->id}});" data-target="#interviewer_info" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            
                            @endif
                        </tbody>
                    </table>
                </div>

            <!-- Confirm interviewer_info view -->
            <div id="interviewer_info" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Candidate Detail</h4>
                        </div>
                        <div class="modal-body">
                        <div class="row">
                        <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Candidate Name</label>
                                    <h4 class="cn_name"></h4>
                                </div>
                            </div>
                             <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <h4 class="cn_email"></h4>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Contact Number</label>
                                    <h4 class="cn_number"></h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Emgency Contact Number</label>
                                    <h4 class="cn_emg_number"></h4>
                                </div>
                            </div>
                           

                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Residential Address</label>
                                    <h4 class="cn_address"></h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Permanent Address</label>
                                    <h4 class="cn_pr_address"></h4>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Gender</label>
                                    <h4 class="cn_gender"></h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Birth Date</label>
                                    <h4 class="cn_date"></h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Marital Status</label>
                                    <h4 class="cn_status"></h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Physically Handicapped?</label>
                                    <h4 class="cn_handicapped"></h4>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Annual Package</label>
                                    <h4 class="cn_package"></h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Joining Date</label>
                                    <h4 class="cn_join_date"></h4>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    </div>
                </div>
            </div>
            <!-- End this view -->
            </div>
        </div>
        @endsection 
        @section('script')
        <script>
            $(document).ready(function () {
               $('#user_table').DataTable({
                   stateSave:true
               });
            });

        function user_detail(id) {
            $.ajax({
                url: "{{ route('admin.interviewer_detail') }}",
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
                            $('.cn_name').html(data.data.interviewer_info.name);
                            $('.cn_designation').html(data.data.interviewer_info.job_opening);
                            $('.cn_number').html(data.data.interviewer_info.contact_number);
                            $('.cn_gender').html(data.data.interviewer_info.gender);
                            $('.cn_emg_number').html(data.data.interviewer_info.emg_contact_number);
                            $('.cn_email').html(data.data.interviewer_info.email);
                            $('.cn_address').html(data.data.interviewer_info.residential_address);
                            $('.cn_pr_address').html(data.data.interviewer_info.permanent_address);
                            
                            let brth_date = data.data.interviewer_info.birth_date ? moment(data.data.interviewer_info.birth_date).format("DD-MM-YYYY") : '';
                            $('.cn_date').html(brth_date);
                            $('.cn_status').html(data.data.interviewer_info.marital_status);
                            $('.cn_handicapped').html(data.data.interviewer_info.physically_handicapped);
                            
                            let join_date = data.data.interviewer_info.join_date ? moment(data.data.interviewer_info.join_date).format("DD-MM-YYYY") : '';
                            let package = data.data.interviewer_info.package ? data.data.interviewer_info.package + ' ' + 'INR' : '';
                            
                            $('.cn_package').html(package);
                            $('.cn_join_date').html(join_date);
                            

                    }
                }
            })
        }
        </script>
        @endsection