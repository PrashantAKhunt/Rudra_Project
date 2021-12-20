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
                        <form action="{{ route('admin.update_cancel_cheque') }}" id="add_cheque" method="post" enctype="multipart/form-data">
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
                                   
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Cheque Book Ref No</label>
                                
                                <select class="form-control" id="cheque_book" name="cheque_book" required >
                                   
                                </select>
                            </div>
                            <div class="form-group">  
                                <label>Cheque Number</label>
                                <select class="form-control" id="cheque_no" name="cheque_no" required >
                                    
                                </select>
                            </div>
        
                            <div class="form-group ">
                                <label>Cancel cheque Image</label>
                                <input type="file" name="cancel_cheque_img" class="form-control" id="cancel_cheque_img" />
                            </div>

                            <div class="form-group ">
                                <label>Letter Head Image</label>
                                <input type="file" name="cancel_letterhead_img" class="form-control" id="cancel_letterhead_img" />
                            </div>

                            <div class="form-group">  
                                <label>Outward Number</label>
                                <select class="form-control" id="outward_no" name="outward_no" required >
                                <option value="">Select Outward</option>
                                        @foreach($outward_list as $key => $value)
                                            <option value="{{$value->inward_outward_no}}">{{$value->inward_outward_no}}</option>
                                        @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" id="btn-submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.cancel_cheque_list') }}'" class="btn btn-default">Cancel</button>
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

        $('#company_id').select2();
        $('#bank_id').select2();
        $('#cheque_book').select2();
        $('#cheque_no').select2();
        $('#outward_no').select2();


        $("#company_id").change(function() {

            var company_id = $("#company_id").val();
            if (company_id >= 1) {
                
                $.ajax({
                    url: "{{ route('admin.get_bank_list_cheque')}}",
                    type: 'get',
                    data: "company_id="+company_id,
                    success: function( data, textStatus, jQxhr ){
                       
                        $('#bank_id').select2('destroy').empty().select2();
                        $('#cheque_book').select2('destroy').empty().select2();
                        $('#cheque_no').select2('destroy').empty().select2();

                        $('#bank_id').append('<option value="">Select bank</option>');
                        $('#bank_id').append(data);
                    },
                    error: function( jqXhr, textStatus, errorThrown ){
                        console.log( errorThrown );
                    }
                });

            }

        });
        //-------------------------------------------------
        $("#bank_id").change(function() {
            if ($("#company_id").val() >= 1 && $("#bank_id").val() >=1 ) {
                $.ajax({
                    url: "{{ route('admin.get_unfailed_cheque_book')}}",
                    type: 'post',
                    data: { company_id: $("#company_id").val(), bank_id: $("#bank_id").val() },
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function( data, textStatus, jQxhr ){
                        
                        $('#cheque_book').select2('destroy').empty().select2();
                        $('#cheque_no').select2('destroy').empty().select2();
                        $('#cheque_book').append(data);
                    },
                    error: function( jqXhr, textStatus, errorThrown ){
                        console.log( errorThrown );
                    }
                });
            }

        });
        //-------------------------------------------------
        $("#cheque_book").change(function() {
            var cheque_book_ref = $("#cheque_book").val();
            if (cheque_book_ref.length >= 1) {

                $.ajax({
                    url: "{{ route('admin.get_unfailed_cheque_list')}}",
                    type: 'post',
                    data: { cheque_book: $("#cheque_book").val() },
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function( data, textStatus, jQxhr ){
                        
                        $('#cheque_no').select2('destroy').empty().select2();
                        $('#cheque_no').append(data);
                    },
                    error: function( jqXhr, textStatus, errorThrown ){
                        console.log( errorThrown );
                    }
                });

            }

            });
    });

    jQuery("#add_cheque").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            bank_id:{
                required: true,
            },
            cheque_no:{
                required: true,
            },
            cheque_book: {
                required: true
            },
            cancel_cheque_img: {
                required: true
            },
            cancel_letterhead_img: {
                required: true
            },
            outward_no: {
                required: true
            }
        }
    });

    $(document).on('click', '#btn-submit', function(e) {
        if ($("#add_cheque").valid()) {
            e.preventDefault();
                swal({
                        title: "Are you sure you want to submit this form ? After submit you will not be able to edit !",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        closeOnConfirm: false
                }, function () {
                    $('#add_cheque').submit();
                });
        }
        
    });

      
</script>
@endsection
