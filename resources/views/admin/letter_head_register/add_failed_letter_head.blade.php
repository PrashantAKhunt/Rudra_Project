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
                        <form action="{{ route('admin.update_failed_letter_head') }}" id="failed_letter_head" method="post" enctype="multipart/form-data">
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
                                <label>Letter Head Number</label>
                                 <select name="letter_head_number[]" id="letter_head_number" class="select2 m-b-10 select2-multiple" multiple="multiple">
                                    
                                 </select>
                            </div>
                            <div class="form-group "> 
                                <label>Failed Detail</label> 
                                <textarea class="form-control" rows="3" required name="failed_reason" id="failed_reason"></textarea>
                            </div>
                            <div class="form-group ">
                                <label>Failed Document</label>
                                <input type="file" name="failed_document" class="form-control" id="failed_document" />
                            </div>
                            <button type="button" class="btn btn-success" id="btn-submit">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.failed_letter_head_list') }}'" class="btn btn-default">Cancel</button>
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
       $("#letter_head_number").select2();
        $("#company_id").change(function() {
            var company_id = $("#company_id").val();
            
            $.ajax({
                url: "{{ route('admin.get_unfailed_letter_head_ref_no')}}",
                type: 'post',
                data: {company_id : company_id},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    // console.log(data);
                    $('#letter_head_ref_no').empty();
                    $('#letter_head_number').empty();
                    $('#letter_head_number').append('<option value="">Select Letter Number</option>');
                    $('#letter_head_ref_no').append(data);
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
                url: "{{ route('admin.get_unfailed_letter_head_list')}}",
                type: 'post',
                data: {letter_head_ref_no : letter_head_ref_no},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    // console.log(data);
                    $('#letter_head_number').empty();
                    $('#letter_head_number').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
        });
   });

    jQuery("#failed_letter_head").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            letter_head_ref_no: {
                required: true,
            },
            "letter_head_number[]":{
                required: true,
            },
            failed_reason: {
                required: true
            },
            failed_document: {
                required: true
            }
        }
    });

    $(document).on('click', '#btn-submit', function(e) {
        if ($("#failed_letter_head").valid()) {
            e.preventDefault();
                swal({
                        title: "Are you sure you want to submit this form ? After submit you will not be able to edit !",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        closeOnConfirm: false
                }, function () {
                    $('#failed_letter_head').submit();
                });
        }
        
    });
      
</script>
@endsection
