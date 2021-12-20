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
                        <form action="{{ route('admin.letter_head_register_request') }}" id="add_letter_head" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Company</label> 
                                @if(!empty($companies))
                                    <select name="company_id" class="form-control" id="company_id">
                                    <option value="">Select company</option>
                                        @foreach($companies as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group "> 
                                <label>Letter Head Ref No</label> 
                                    <select name="letter_head_ref_no" class="form-control" id="letter_head_ref_no">
                                        <option value="">Select Letter Head Ref No</option>
                                        
                                    </select>
                            </div>
                            <div class="form-group ">
                                <label>Letter Head Start Number</label>
                                 <select name="letter_head_start_number" id="letter_head_start_number" class="form-control">
                                    <option value="">Select Letter Head Start Number</option>
                                 </select>
                            </div>
                            <div class="form-group ">
                                <label>Letter Head End Number</label>
                                 <select name="letter_head_end_number" id="letter_head_end_number" class="form-control">
                                    <option value="">Select Letter Head End Number</option>
                                 </select>
                            </div>
                            <button type="button" class="btn btn-success" id="btn-submit">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.signed_letter_head_list') }}'" class="btn btn-default">Cancel</button>
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
                url: "{{ route('admin.get_letter_head_ref_no')}}",
                type: 'post',
                data: {company_id : company_id},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    // console.log(data);
                    $('#letter_head_ref_no').empty();
                    $('#letter_head_ref_no').append(data);
                    $('#letter_head_start_number').empty();
                    $('#letter_head_start_number').append('<option value="">Select Letter Head Start Number</option>');
                    $('#letter_head_end_number').empty();
                    $('#letter_head_end_number').append('<option value="">Select Letter Head End Number</option>');
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });
        // ------------------------------------------------------------
        $("#letter_head_ref_no").on('change',function(){
          var letter_head_ref_no = $("#letter_head_ref_no").val();
          
          $.ajax({
                url: "{{ route('admin.get_unsigned_letter_head_list')}}",
                type: 'post',
                data: {letter_head_ref_no : letter_head_ref_no},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    // console.log(data);
                    $('#letter_head_start_number').empty();
                    $('#letter_head_start_number').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
        });

        // ------------------------------------------------------------
        $("#letter_head_start_number").on('click',function(){
            var letter_head_ref_no = $("#letter_head_ref_no").val();
            var letter_head_start_number = $("#letter_head_start_number").val();
            $.ajax({
                url: "{{ route('admin.get_remaining_letter_head_list')}}",
                type: 'post',
                data: {
                    letter_head_ref_no : letter_head_ref_no, 
                    letter_head_start_number : letter_head_start_number
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    // console.log(data);
                    $('#letter_head_end_number').empty();
                    $('#letter_head_end_number').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
        })
   });

    jQuery("#add_letter_head").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            letter_head_ref_no: {
                required: true,
            },
            letter_head_start_number:{
                required: true,
            },
            letter_head_end_number:{
                required: true,
            }
        }
    });

    $(document).on('click', '#btn-submit', function(e) {
        if ($("#add_letter_head").valid()) {
            e.preventDefault();
                swal({
                        title: "Are you sure you want to submit this form ? After submit you will not be able to edit !",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        closeOnConfirm: false
                }, function () {
                    $('#add_letter_head').submit();
                });
        }
        
    });
      
</script>
@endsection
