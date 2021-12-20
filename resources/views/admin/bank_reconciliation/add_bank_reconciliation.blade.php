@extends('layouts.admin_app')

@section('content')
<!-- add_bank_reconciliation -->
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <!-- <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li> -->
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
                        <form action="{{url('save_bank_reconciliation')}}" id="bank_reconciliation_form" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group "> 
                                <label>Company</label> 
                                @if(!empty($companies))
                                    <select name="company_id" class="form-control" id="company_id" required >
                                    <option value="">Select company</option>
                                        @foreach($companies as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group ">
                                <label>Bank Name</label>
                                <select class="form-control" id="bank_id" name="bank_id" required >
                                   <option value="">Select Bank</option>
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Bank Type</label>
                                
                                <select class="form-control" id="bank_type" name="bank_type" required >
                                    <option value="">Select Bank Type</option>
                                    @foreach(config('constants.BANK_NAME') as $val)
                                        <option value="{{$val}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Bank Statement</label>
                                <input type="file" class="form-control" id="bank_statement" name="bank_statement">
                            </div>
                            <button type="submit" id="btn-submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.bank_reconciliation') }}'" class="btn btn-default">Cancel</button>
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

    $(document).ready(function () {
        $("#company_id").change(function() {

            var company_id = $("#company_id").val();
            $.ajax({
                url: "{{ route('admin.get_company_bank_list')}}",
                type: 'post',
                data: {company_id : company_id,"_token" : "{{csrf_token()}}"},
                success: function( data, textStatus, jQxhr ){
                    $('#bank_id').empty();
                    $('#bank_id').append('<option value="">Select Bank</option>');
                    $('#bank_id').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });
    });

    jQuery("#bank_reconciliation_form").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            bank_id:{
                required: true,
            },
            bank_statement:{
                required: true,
                // extension: "csv"
            },
            bank_type : {
                required : true,
            }
        }
    });
      
</script>
@endsection
