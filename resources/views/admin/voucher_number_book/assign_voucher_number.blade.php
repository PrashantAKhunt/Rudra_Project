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
            <!-- {{ route('admin.add_voucher_number') }} -->
                
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Voucher Book Ref No</th>
                                <th>Voucher Number</th>
                                <th>Company</th>
                                {{-- <th>Client Name</th>
                                <th>Project Name</th>
                                <th>Project Site Name</th> --}}
                                <th>Assigned By</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- {{dd($assign_voucher)}} --}}
                            @if($assign_voucher)
                                @foreach($assign_voucher as $key => $value)
                                    <tr>
                                        <td>{{$value['voucher_ref_no']}}</td>
                                        <td>
                                        @php
                                        $new_numbers1 = implode(',',$value['voucher_numbers']);
                                        
                                        @endphp
                                        @if(strlen($new_numbers1) < 20)
                                            {{$new_numbers1}}
                                        @else
                                            {{substr($new_numbers1,0,20)}}
                                            (<a href="#" title="{{$new_numbers1}}">More</a>)
                                        @endif
                                        
                                        </td>
                                        <td>{{$value['company_name']}}</td>
                                        {{-- <td>{{$value['client_name']}}</td>
                                        <td>{{$value['project_name']}}</td>
                                        <td>{{$value['site_name']}}</td> --}}
                                        <td>{{$value['assigned_by']}}</td>
                                        <td>{{$value['created_at']}}</td>
                                        <td>
                                            @if($value['status'] == 'assigned')
                                                <b class="text-warning">Assigned</b>
                                            @elseif($value['status'] == 'submitted')
                                                <b class="text-warning">Submitted</b>
                                            @elseif($value['status'] == 'accepted')
                                                <b class="text-success">Accepted</b>
                                            @elseif($value['status'] == 'rejected')
                                                <b class="text-danger">Rejected</b>
                                            @endif
                                        </td>
                                        <td>
                                            @if($value['status'] == 'assigned')
                                                <button type="button" class="btn btn-info btn-circle" title="Accept Voucher" onclick="acceptVoucher('{{$value['id']}}')"><i class="fa fa-check"></i></button>
                                                <button type="button" class="btn btn-danger btn-circle" title="Reject Voucher" onclick="rejectVoucher('{{$value['id']}}')"><i class="fa fa-times"></i></button>
                                            @elseif($value['status'] == 'submitted')
                                                
                                            @elseif($value['status'] == 'accepted')
                                                <input type="hidden" id="company_id_{{$value['id']}}" value="{{$value['company_id']}}">
                                                <input type="hidden" id="client_id_{{$value['id']}}" value="{{$value['client_id']}}">
                                                <input type="hidden" id="project_id_{{$value['id']}}" value="{{$value['project_id']}}">
                                                <input type="hidden" id="project_site_id_{{$value['id']}}" value="{{$value['project_site_id']}}">
                                                <button type="button" class="btn btn-info btn-circle" title="Assign Voucher" onclick="assignVoucher('{{$value['id']}}')"><i class="fa fa-pencil-square-o"></i></button>
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
        <div class="modal fade" id="assign_voucher_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assign Voucher</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.assign_voucher_touser') }}" id="assign_user_form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group "> 
                            <label>Assign User <span class="error">*</span></label> 
                            @if(!empty($users))
                                <select name="to_user_id" class="form-control required" id="to_user_id">
                                <option value="">Select User</option>
                                    @foreach($users as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            @endif
                            <input type="hidden" name="assigned_voucher_id" id="assigned_voucher_id">
                        </div>
                        {{-- <div class="form-group ">
                            <label>Select Client</label>
                            <select class="form-control" id="client_id" name="client_id">
                                <option value="">Select Client</option>
                            </select>
                        </div>

                        <div class="form-group ">
                            <label>Select Project</label>
                            <select class="form-control" id="project_id" name="project_id">
                                <option value="">Select Project</option>
                            </select>
                        </div>
                        <div class="form-group ">
                            <label>Select Project Site</label>
                            <select class="form-control" id="project_site_id" name="project_site_id">
                                <option value="">Select Site</option>
                            </select>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary assign_user_form_btn">Save changes</button>
                    </div>
                </form>
                </div>
            </div>
            </div>
        @endsection
        @section('script')
        <script>
        $(document).ready(function () {
            $("#to_user_id").select2();
            var table = $('#company_table').DataTable({
                columnDefs: [
                    { orderable: false, targets: -1 },
                ],
                "order": [[ 4, "desc" ]]
            });

        })
        function acceptVoucher(id){
            swal({
                    title: "Accept Voucher",
                    text: "Are you sure you want to accept voucher.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    // alert(id);
                    location.href = "accept_voucher_user/"+id;
                });
        }

        function rejectVoucher(id){
            swal({
                    title: "Reject Voucher",
                    text: "Are you sure you want to reject voucher.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    // alert(id);
                    location.href = "reject_voucher_user/"+id;
                });
        }
        var company_id = "";
        var client_id = "";
        var project_id = "";
        var project_site_id = "";
        function assignVoucher(id){
            // alert(id);
            $("#assigned_voucher_id").val(id);
            company_id = $("#company_id_"+id).val();
            /* client_id = $("#client_id_"+id).val();
            project_id = $("#project_id_"+id).val();
            project_site_id = $("#project_site_id_"+id).val(); */
            $("#assign_user_form").validate({}).resetForm();
            /* if(company_id){
                htmlStr = '';
                 $.ajax({
                    url: "{{ route('admin.get_company_client_list') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        company_id: company_id
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $("#client_id").empty();
                        $("#client_id").append("<option value='' selected>Select Client</option>");
                        $.each(data, function(index, clients_obj) {
                            
                            if (clients_obj.id == 1) {
                                htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                            }else{
                                htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                            }
                            
                            //$("#client_id").append('<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>');
                        });
                        $("#client_id").append(htmlStr);
                        $("#client_id").val(client_id);
                        $('#client_id').trigger('change');
                    }
                });
            } */
            $("#assign_voucher_user").modal('show');
        }
        
        $(".assign_user_form_btn").on('click',function(){
            if($("#assign_user_form").valid()){
                // $("#assign_user_form").submit();
                swal({
                    title: "Assign Voucher",
                    text: "Are you sure you want to assign voucher to user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    $("#assign_user_form").submit();
                });
            }
        })
        $("#assign_user_form").validate({
            ignore: [],
            rules : {
                /* client_id : "required",
                project_id : "required",
                project_site_id : "required", */
            }
        });
        /* $('#client_id').on('change',function(){
            //project list
            client_id = $("#client_id").val();

            $.ajax({
                url: "{{ route('admin.get_client_project_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    client_id: client_id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#project_id").empty();
                    $("#project_id").append("<option value='' selected>Select Project</option>");

                    $.each(data, function(index, projects_obj) {

                        $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');

                    });
                    $("#project_id").val(project_id);
                    $('#project_id').trigger('change');
                }
            });
        });

        $("#project_id").on('change',function () {
            var project_id = $("#project_id").val();
            $.ajax({
                url: "{{ route('admin.get_project_sites_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    project_id: project_id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#project_site_id").empty();
                    $("#project_site_id").append("<option value='' selected>Select Site</option>");
                    $.each(data, function(index, project_site_obj) {
                        $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                    })
                    $("#project_site_id").val(project_site_id);
                }
            });
        }); */
        </script>
        @endsection