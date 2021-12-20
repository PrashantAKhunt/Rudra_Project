@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Edit Email Format</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="#">Edit Email Format</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
               
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.updateemail') }}" id="edit_email_format" method="post">
                            @csrf
                            <div class="form-group ">
                                
                                    <label for="subject">Subject</label>

                                    <input type="text" class="form-control" name="subject" id="subject" value="{{ $email_array['subject'] }}" />

                                    <input type="hidden" name="id" id="id" value="{{ $email_array['id'] }}" />

                            </div>
                            <div class="form-group ">
                                
                                    <label id="emailformat">Email Format</label>

                                    <textarea id="emailformat" name="emailformat" class="form-control ckeditor" rows="10" cols="100">{{ $email_array['emailformat'] }}</textarea>

                            </div>


                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href='{{ route('admin.emailformat') }}'" class="btn btn-default">Cancel</button>
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
    jQuery("#edit_email_format").validate({
        ignore: [],
        rules: {
            subject: {
                required: true,

            },
            emailformat: {
                required: true,
            },
        },
        messages: {
            subject: {
                required: "Subject is require."
            },
            emailformat: {
                required: "Email Format is require."
            },
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());

        }

    });

</script>
@endsection
