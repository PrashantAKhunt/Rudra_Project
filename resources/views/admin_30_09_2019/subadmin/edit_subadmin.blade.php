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
                    <div class="col-sm-6 col-xs-6">
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
                        <form action="{{ route('admin.update_subadmin') }}" id="edit_subadmin_frm" method="post">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $user_detail[0]->id }}" /> 
                            <div class="form-group ">

                                <label>Name</label>

                                <input type="text" class="form-control" name="name" id="name" value="{{ $user_detail[0]->name }}" />


                            </div>
                            <div class="form-group ">

                                <label>Email</label>
                                <input type="text" class="form-control" readonly="" name="email" id="email" value="{{ $user_detail[0]->email }}" />
                            </div>

                            <div class="form-group ">

                                <label>Password</label>
                                <input type="password" class="form-control" name="password" id="password" value="" />
                            </div>

                            <div class="form-group ">

                                <label>Confirm Password</label>
                                <input type="password" class="form-control" name="conf_password" id="conf_password" value="" />
                            </div>

                            <div class="form-group ">

                                <label>Mobile</label>
                                <input type="text" class="form-control" name="mobile" id="mobile" value="{{ $user_detail[0]->mobile }}" />
                            </div>

                            <div class="form-group ">

                                <label>Sub-admin Role</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="">Select Sub-admin Role</option>
                                    @foreach($role_list as $role)
                                    <option @if($role->id==$user_detail[0]->role) selected="" @endif value="{{ $role->id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                           
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.subadmins') }}'" class="btn btn-default">Cancel</button>
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
   
    jQuery("#edit_subadmin_frm").validate({
        ignore: [],
        rules: {
            name: {
                required: true,

            },
            email: {
                required: true,
                email: true
            },
            password: {

                minlength: 8,
                pwcheck: true
            },
            conf_password: {
                equalTo: "#password"
            },
            role: {
                required: true,
            },
            

        },

    });
    $.validator.addMethod("pwcheck",
            function (value, element) {
                if (value != "") {
                    return /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/.test(value);
                } else {
                    return true;
                }
            }, 'Password must contain minimum 8 character, atleast one number and one special character.');
</script>
@endsection
