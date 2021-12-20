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
                        <form action="{{ route('admin.update_failed_cheque') }}" id="add_cheque" method="post" enctype="multipart/form-data">
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
                                <label>Detail</label> 
                                <textarea class="form-control" rows="3" required name="failed_reason" id="failed_reason"></textarea>
                            </div>
                            <div class="form-group ">
                                <label>Document</label>
                                <input type="file" name="failed_document" class="form-control" id="failed_document" />
                            </div>
                            
                            <button type="submit" id="btn-submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.failed_cheque_list') }}'" class="btn btn-default">Cancel</button>
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
                    $('#cheque_book').empty();
                    $('#cheque_no').empty();
                    $('#bank_id').append('<option value="">Select bank</option>');
                    $('#bank_id').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });
        //-------------------------------------------------
        $("#bank_id").change(function() {

            $.ajax({
                url: "{{ route('admin.get_unfailed_cheque_book')}}",
                type: 'post',
                data: { company_id: $("#company_id").val(), bank_id: $("#bank_id").val() },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    $('#cheque_book').empty();
                    $('#cheque_no').empty();
                    $('#cheque_book').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });
        //-------------------------------------------------
        $("#cheque_book").change(function() {

            $.ajax({
                url: "{{ route('admin.get_unfailed_cheque_list')}}",
                type: 'post',
                data: { cheque_book: $("#cheque_book").val() },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    $('#cheque_no').empty();
                    $('#cheque_no').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

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
            failed_reason: {
                required: true
            },
            failed_document: {
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
