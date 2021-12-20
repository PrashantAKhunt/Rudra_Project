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
                        <form action="{{url('signed_rtgs_request')}}" id="add_rtgs" method="post">
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
                                <label>RTGS Ref No</label>
                                <select class="form-control" id="rtgs_ref_no" name="rtgs_ref_no" required >
                                   <option value="">Select RTGS No</option>
                                </select>
                            </div>
                            <div class="form-group">  
                                <label>RTGS Start Number</label>
                                <select class="form-control" id="rtgs_start_number" name="rtgs_start_number" required >
                                    <option value="">Select Start Number</option>
                                </select>
                                
                            </div>
                            <div class="form-group">
                                <label>RTGS End Number</label>
                                <select class="form-control" id="rtgs_end_number" name="rtgs_end_number" required >
                                    <option value="">Select End Number</option>
                                </select>
                                
                            </div>
                            <button type="submit" id="btn-submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.signed_rtgs_list') }}'" class="btn btn-default">Cancel</button>
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
                    $('#bank_id').empty();
                    $('#rtgs_start_number').empty();
                    $('#rtgs_end_number').empty();
                    $('#bank_id').append('<option value="">Select bank</option>');
                    $('#bank_id').append(data);
                    $("#rtgs_ref_no").empty();
                    $("#rtgs_ref_no").append('<option value="">Select RTGS No</option>');
                    $("#rtgs_start_number").empty();
                    $("#rtgs_start_number").append('<option value="">Select Start Number</option>');
                    $("#rtgs_end_number").empty();
                    $("#rtgs_end_number").append('<option value="">Select End Number</option>');
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });
        //-------------------------------------------------
        $("#bank_id").change(function() {

            $.ajax({
                url: "{{ route('admin.get_rtgs_ref')}}",
                type: 'post',
                data: { company_id: $("#company_id").val(), bank_id: $("#bank_id").val() },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    $('#rtgs_ref_no').empty();
                    $('#rtgs_start_number').empty();
                    $('#rtgs_end_number').empty();
                    $('#rtgs_ref_no').append(data);
                    $("#rtgs_start_number").empty();
                    $("#rtgs_start_number").append('<option value="">Select Start Number</option>');
                    $("#rtgs_end_number").empty();
                    $("#rtgs_end_number").append('<option value="">Select End Number</option>');
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });
        //-------------------------------------------------
        $("#rtgs_ref_no").change(function() {
            $.ajax({
                url: "{{ route('admin.get_unsigned_rtgs_list')}}",
                type: 'post',
                data: { rtgs_ref_no: $("#rtgs_ref_no").val() },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    $('#rtgs_start_number').empty();
                    $('#rtgs_start_number').append('<option value="">Select Start Number</option>');
                    $('#rtgs_start_number').append(data);
                    $("#rtgs_end_number").empty();
                    $("#rtgs_end_number").append('<option value="">Select End Number</option>');
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
        });
    });

    jQuery("#add_rtgs").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            bank_id:{
                required: true,
            },
            rtgs_start_number:{
                required: true,
            },
            rtgs_end_number:{
                required: true,
            },
            rtgs_ref_no: {
                required: true
            }
        }
    });

   $(document).on('click', '#btn-submit', function(e) {
        if ($("#add_rtgs").valid()) {
            e.preventDefault();
                swal({
                        title: "Are you sure you want to submit this form ? After submit you will not be able to edit !",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        closeOnConfirm: false
                }, function () {
                    $('#add_rtgs').submit();
                });
        }
        
    });

    $("#rtgs_start_number").change(function() {
            $.ajax({
                url: "{{ route('admin.get_remaining_rtgs')}}",
                type: 'post',
                data: { rtgs_ref_no: $("#rtgs_ref_no").val(), rtgs_no: $("#rtgs_start_number").val() },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    $('#rtgs_end_number').empty();
                    $('#rtgs_end_number').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
            });
      
</script>
@endsection
