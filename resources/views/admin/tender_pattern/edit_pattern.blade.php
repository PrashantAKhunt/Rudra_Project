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
                        <form action="{{ route('admin.save_tender_pattern') }}" id="tender_pattern_form" method="post">
                            @csrf
                            <div class="form-group ">
                                <label>Tender Pattern Name <span class="error">*</span> </label>
                                 <input type="text" class="form-control" name="tender_pattern_name" id="tender_pattern_name" value="{{$tender_pattern_detail->tender_pattern_name}}" />
                                 <input type="hidden" name="id" id="id" value="{{$tender_pattern_detail->id}}">
                                  
                            </div>
                            <div class="form-group ">
                                <label>Tender Pattern Detail</label>
                                 <input type="text" class="form-control" name="tender_pattern_detail" id="tender_pattern_detail" value="{{$tender_pattern_detail->tender_pattern_detail}}" /> 
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.tender_pattern') }}'" class="btn btn-default">Cancel</button>
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
    jQuery("#tender_pattern_form").validate({
        rules: {
            tender_pattern_name: {
                required: true,
                remote: {
                url: "{{url('check_tender_pattern')}}",
                type: "post",
                data: {
                "id" : $( "#id" ).val(),
                "_token": "{{csrf_token()}}",
                  username: function() {
                    return $( "#tender_pattern_name" ).val();
                  }
                }
              }
            },
        },
        messages : {
            tender_pattern_name : {
                remote : "Tender pattern already inserted use anothor",
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
