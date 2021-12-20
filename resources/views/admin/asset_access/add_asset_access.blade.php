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
                        <form action="{{ route('admin.insert_asset_access') }}" id="add_asset" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group "> 
                                <label>Select Asset <span class="error">*</span> </label>
                                <select class="form-control" onclick="find_assigned_user();" name="asset_id" id="asset_id">
                                    <option value="">Select Asset</option>
                                    @foreach($Asset_List as $asset_list_data)
                                    <option value="{{ $asset_list_data->id }}">{{ $asset_list_data->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group "> 
                                <label>Currently Assigned To:  <span class="error">*</span> <b><span id="assigned_to_user"></span></b></label>
                                <!-- here u dropped your mind -->
                            </div>

                            <div class="form-group "> 
                                <label>Employee Name <span class="error">*</span> </label> 
                                <select class="select2 form-control" name="user_id" id="user_id">
                                    <option value="">Select User</option>
                                    @foreach($UsersName as $users_name_data)
                                    <option value="{{ $users_name_data->id }}">{{ $users_name_data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Assign Date <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="assign_date" id="assign_date"/>
                            </div>
                            <!--<div class="form-group "> 
                                <label>Return Date</label> 
                                <input type="text" class="form-control" name="return_date" id="return_date"/>
                            </div>-->
                            <div class="form-group "> 
                                <label>Asset Description <span class="error">*</span> </label> 
                                <textarea class="form-control" rows="5" name="asset_description" id="asset_description">
                                </textarea>
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.asset_access') }}'" class="btn btn-default">Cancel</button>
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

    function find_assigned_user() {
        $.ajax({
            url: "{{route('admin.get_assigned_user')}}",
            type: "post",
            dataType: "json",
            data: {id: $('#asset_id').val()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(data){
                $('#assigned_to_user').text(data.name);
            }
        })
    }

    $(document).ready(function () {

        $('#user_id').select2();
        $('#asset_id').select2();
        removeTextAreaWhiteSpace();

        jQuery('#assign_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        jQuery('#return_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
    });
    jQuery("#add_asset").validate({
        ignore: [],
        rules: {
            asset_id: {
                required: true,
            },
            user_id: {
                required: true,
            },
            assign_date: {
                required: true,
            },
            asset_description: {
                required: true,
            }
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });
    function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('asset_description');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g, '');
    }
</script>
@endsection
