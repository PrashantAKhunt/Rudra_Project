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
        <div class="col-md-12">
            <div class="white-box">

                <div class="row">
                    <div class="col-md-12">
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
                        <form action="{{ route('admin.insert_project_site') }}" enctype="multipart/form-data" id="add_site_frm" method="post">
                            @csrf


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label>Companies<span class="error">*</span></label>
                                        <select class="form-control" name="company_id" id="companies_list" required>
                                            <option value="" disabled selected>Please Select</option>
                                            @foreach($companies as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['company_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Clients<span class="error">*</span></label>
                                        <select class="form-control" name="client_id" id="clients_list" required>
                                        </select>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Projects<span class="error">*</span></label>
                                        <select class="form-control" name="project_id" id="projects_list" required>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Site Name<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="site_name" id="site_name" value="" />
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Site Address<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="site_address" id="site_address" value="" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Site Details<span class="error">*</span></label>
                                        <textarea class="form-control" name="site_details" id="site_details" value="" /></textarea>
                                    </div>
                                </div>
                            </div>





                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.project_site') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection


@section('script')
<script>

    $("#companies_list").change(function () {

        companies_list = $(this).val();

        if (companies_list) {
            //alert(companies_list);
            $.ajax({

                url: "{{ route('admin.companies_clients') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    company_id: companies_list
                },
                dataType: "JSON",
                //processData: false,
                //contentType: false,
                success: function (data) {
                    //alert(data.id)
                    //$("#user_list").html('');
                    $("#clients_list").empty();
                    $("#clients_list").append("<option value='' disabled selected>Please select</option>");
                    $.each(data, function (index, clients_obj) {
                        //alert(key);


                        $("#clients_list").append('<option value="' + clients_obj.id + '">' + clients_obj.client_name + '-' + clients_obj.location + '</option>');
                    })

                }
            });

        } else {

            $("#clients_list").empty();

        }

    });
</script>

<script>

    $("#clients_list").change(function () {

        clients_list = $(this).val();

        if (clients_list) {
//            alert(clients_list);
            $.ajax({

                url: "{{ route('admin.clients_projects') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    client_id: clients_list
                },
                dataType: "JSON",
                //processData: false,
                //contentType: false,
                success: function (data) {
                    //alert(projects_list);
                    //alert(data.id);
                    //$("#user_list").html('');
                    $("#projects_list").empty();
                    $("#projects_list").append("<option value='' disabled selected>Please select</option>");
                    $.each(data, function (index, projects_obj) {
                        //alert(key);


                        $("#projects_list").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                    })

                }
            });

        } else {

            $("#projects_list").empty();

        }

    });
</script>




<script>
    $(document).ready(function () {

        $('#add_site_frm').validate({
            rules: {

                companies_list: {
                    required: true
                },
                clients_list: {
                    required: true
                },
                projects_list: {
                    required: true
                },
                site_name: {
                    required: true,
                    remote: {
                        url: "{{ route('admin.checkProjectSiteName') }}",
                        type: "post",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "name": function () {
                                return $('#site_name').val()
                            },
                            "project_id": function () {
                                return $('#projects_list').val();
                            }
                        }
                    },
                },
                site_address: {
                    required: true
                },
                site_details: {
                    required: true
                },

            },
            messages:{
                site_name:{
                    remote:"Site name already exists for this project."
                }
            }
        })

    });
</script>




@endsection