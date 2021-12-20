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
                        <form action="{{ route('admin.save_tender_physical_mode') }}" id="tender_sub_form" method="post">
                            @csrf
                            <div class="form-group ">
                                <label>Mode Name</label>
                                 <input type="text" class="form-control" name="mode_name" id="mode_name" value="{{$tender_physical_mode->mode_name}}" />
                                 <input type="hidden" name="id" id="id" value="{{$tender_physical_mode->id}}">
                                  
                            </div>
                            <div class="form-group ">
                                <label>Mode Detail</label>
                                 <input type="text" class="form-control" name="mode_detail" id="mode_detail" value="{{$tender_physical_mode->mode_detail}}" /> 
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.tender_physical_submission') }}'" class="btn btn-default">Cancel</button>
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
    jQuery("#tender_sub_form").validate({
        rules: {
            mode_name: {
                required: true,
                remote: {
                url: "{{url('check_tender_physical_mode')}}",
                type: "post",
                data: {
                "id" : $( "#id" ).val(),
                "_token": "{{csrf_token()}}",
                  username: function() {
                    return $( "#mode_name" ).val();
                  }
                }
              }
            },
        },
        messages : {
            mode_name : {
                remote : "Tender mode name already inserted use anothor",
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
      
</script>
@endsection
