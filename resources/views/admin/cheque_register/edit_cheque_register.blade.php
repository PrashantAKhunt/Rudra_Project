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
                        <form action="{{ route('admin.update_cheque_register') }}" id="update_cheque_register" method="post" name="update_cheque_register">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $cheque_register_data[0]['id'] }}" /> 
                            <div class="form-group ">
                                <label>Clear Date</label>
                                <input type="text" class="form-control" name="cl_date" id="cl_date" value="{{ $cheque_register_data[0]['cl_date'] }}"/> 
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.cheque_register') }}'" class="btn btn-default">Cancel</button>
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
                url: "{{ route('admin.get_bank_list_cheque')}}",
                type: 'get',
                data: "company_id="+company_id,
                success: function( data, textStatus, jQxhr ){
                    $('#bank_id').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });
   });
   
   jQuery('#cl_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
    });

    jQuery("#update_cheque_register").validate({
        ignore: [],
        rules: {
            cl_date: {
                required: true,
            }
        }
    });
      
</script>
@endsection
