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
                        <form action="{{ route('admin.insert_client') }}" id="add_client" method="post">
                            @csrf
                            <div class="row">
                                    <div class="col-md-6">
        							<div class="form-group "> 
                                        <label>Company <strong> <span class="text-danger">*</span></strong> </label> 
                                        @if(!empty($companies))
                                            <select name="company_id" id="company_id" class="form-control" >
                                            <option value="">Select company</option>
                                                @foreach($companies as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Tender</label> 
                                            <select name="tender_id" id="tender_id" class="form-control tender_id" >
                                            <option value="0">Select Tender</option>
                                            </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Name <strong> <span class="text-danger">*</span></strong> </label> 
                                        <input type="text" class="form-control" name="client_name" id="client_name" value="" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Address <strong> <span class="text-danger">*</span></strong> </label> 
                                        <input type="text" class="form-control" name="location" id="location" value="" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Email <strong> <span class="text-danger">*</span></strong> </label> 
                                        <input type="text" class="form-control" data-role="tagsinput" name="email" id="client_email" value="" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Landline Number <strong> <span class="error">*</span></strong> </label> 
                                        <input type="text" class="form-control" data-role="tagsinput" name="client_landline_no" id="client_landline_no" value="" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Mobile Number <strong> <span class="error">*</span></strong> </label> 
                                        <input type="text" class="form-control" data-role="tagsinput" name="client_mobile_no" id="client_mobile_no" value="" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Fax Number <strong> <span class="error">*</span></strong> </label> 
                                        <input type="text" class="form-control" data-role="tagsinput" name="client_fax_no" id="client_fax_no" value="" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Client Detail</label>
                                        <textarea class="form-control" rows="10" name="detail" id="detail" ></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-12">
                                <label><b>Client Contact Person Details</b></label>
                                <hr>
                                <br>
                                <div id="single_data_div">
                                    <div id="dynamic_div_0">
                                        <div class="row">
                                        <!--/span-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Name <strong> <span class="error">*</span></strong> </label>
                                                <input type="text" name="contact_name[]" id="contact_name_0" class="form-control contact_name">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Designation <strong> <span class="error">*</span></strong> </label>
                                                <input type="text" name="contact_designation[]" id="contact_designation_0" class="form-control contact_designation">
                                            </div>
                                            <!--/span-->
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Email <strong> <span class="error">*</span></strong> </label>
                                                <input type="text" name="contact_email[]" id="contact_email_0" class="form-control contact_email">
                                            </div>
                                            <!--/span-->
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Phone Number <strong> <span class="error">*</span></strong> </label>
                                                <input type="text" name="contact_phone[]" id="contact_phone_0" class="form-control contact_phone">
                                            </div>
                                            <!--/span-->
                                        </div>
                                        
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <button type="button" onclick="remove_more(this)" class="btn btn-danger remove_btn"><i class="fa fa-trash"></i> Remove</button>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-12">
                                    <div id="dynamic_data_div">

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" onclick="add_more();" id="add_button" class="btn btn-primary"><i class="fa fa-plus"></i> Add More</button>
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.client') }}'" class="btn btn-default">Cancel</button>
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
var contact_counter = 0;
    $("#rolefrm").validate({
                rules: {
                    role_name: {
                        required: true,
                        remote: {
                            url: "{{ url('check_uniqueRoleName') }}",
                            type: "post",
                            data: {
                                role_name: function () {
                                    return $("#role_name").val();
                                },
                                "_token": "{{ csrf_token() }}",
                            }
                        }
                    }

                },
                // Specify the validation error messages
                messages: {
                    role_name: {
                        required: "Role name is required.",
                        remote: "Role name is already exists."
                    }

                },
                submitHandler: function (form) {
                    form.submit();
                }
            });
    $('form#add_client').on('submit', function(event) {
        $('.contact_name').each(function() {
            $(this).rules('add', {
                required: true,
            });
        });
        $('.contact_designation').each(function() {
            $(this).rules('add', {
                required: true,
            });
        });
        $('.contact_email').each(function() {
            $(this).rules('add', {
                required: true,
                email: true
            });
        });
        $('.contact_phone').each(function() {
            $(this).rules('add', {
                required: true,
                number: true
            });
        });
    });
    jQuery("#add_client").validate({
        ignore: [],
        rules: {
            client_name: {
                required: true,
            },
            /*'contact_name[]': {
                required: true,
            },
            'contact_designation[]': {
                required: true,
            },
            'contact_email[]': {
                required: true,
                email:true
            },
            'contact_phone[]': {
                required: true,
                number:true
            },*/
            email: {
                required: true,
            },
            /*tender_id: {
                required: true,
            },*/
            location:{
                required: true,
            },
            /*detail:{
                required: true,
            },*/
            company_id:{
                required: true,
            },
            client_landline_no : {
                required : true
            },
            client_mobile_no : {
                required : true
            },
            client_fax_no : {
                required : true
            },
        },
        // Specify the validation error messages
        messages: {
            pan_card_number: {
                required: "Pan Card Number is required.",
                remote: "Pan Card Number is already exists."
            }
        },
    });  

    function add_more() {
        /*var old_html = $('#single_data_div').html();
        var new_html = old_html.replace('remove_btn', 'btnremove_show');
        $('#dynamic_data_div').append(new_html);
        $('.input-daterange-datepicker').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('.btnremove_show').show(); */

        /*$("div#dynamic_data_div").find("#contact_phone").prop('required',true);
        $("div#dynamic_data_div").find("#contact_name").prop('required',true);
        $("div#dynamic_data_div").find("#contact_email").prop('required',true);*/

        contact_counter += 1;
        $('#dynamic_data_div').append('<div id="dynamic_div_0">'+
                                        '<div class="row">'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Name<span class="error">*</span></label>'+
                                                '<input type="text" name="contact_name['+contact_counter+']" id="contact_name_'+contact_counter+'" class="form-control contact_name">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Designation<span class="error">*</span></label>'+
                                                '<input type="text" name="contact_designation['+contact_counter+']" id="contact_designation_'+contact_counter+'" class="form-control contact_designation">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Email<span class="error">*</span></label>'+
                                                '<input type="text" name="contact_email['+contact_counter+']" id="contact_email_'+contact_counter+'" class="form-control contact_email">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Phone Number</label>'+
                                                '<input type="text" name="contact_phone['+contact_counter+']" id="contact_phone_'+contact_counter+'" class="form-control contact_phone">'+
                                            '</div>'+
                                        '</div>'+
                                        '</div>'+
                                        '<div class="row">'+
                                            '<div class="col-md-3">'+
                                                '<button type="button" onclick="remove_more(this)" class="btn btn-danger remove_btn"><i class="fa fa-trash"></i> Remove</button>'+
                                            '</div>'+
                                        '</div>'+
                                        '<hr>'+
                                    '</div>');
    }

    function remove_more(e) {
        $(e).parent().parent().parent().remove();
    }

    $(document).ready(function () {
        $('.remove_btn').hide();
    });

    $("#company_id").on('change',function(){
        var company_id = $(this).val();
        $.ajax({
            type : "POST",
            url : "{{url('get_client_tender')}}",
            data : {
                company_id : company_id,
                "_token" : "{{csrf_token()}}"
            },
            dataType : "json",
            success : function(data){
                // console.log(data);
                $("#client_name").val("");
                $("#location").val("");
                $("#client_email").tagsinput("removeAll");
                $("#client_landline_no").tagsinput("removeAll");
                $("#client_mobile_no").tagsinput("removeAll");
                $("#client_fax_no").tagsinput("removeAll");
                
                $('#dynamic_data_div').html("");
                // $("#contact_name_0").removeClass("error").rules("add");
                // $("#contact_designation_0").removeClass("error").rules("add");
                // $("#contact_email_0").removeClass("error").rules("add");
                // $("#contact_phone_0").removeClass("error").rules("add");
                if(data.length){
                    $("#tender_id").html('');
                    $("#tender_id").prepend('<option value="">Select Tender</option>');
                    $.each( data, function(key, value){
                      $("#tender_id").append('<option value="'+value.id+'">'+value.tender_sr_no+'</option>');                    
                    });
                }else{
                    $("#tender_id").html('');
                    $("#tender_id").append('<option value="">Tender Not Found</option>');
                }
            }
        });
    });
    $("#tender_id").on('change',function(){
        var tender_id = $(this).val();
        $.ajax({
            type : "POST",
            url : "{{url('get_tender_detail')}}",
            data : {
                tender_id : tender_id,
                "_token" : "{{csrf_token()}}"
            },
            dataType : "json",
            success : function(data){
                // console.log(data);
                $("#client_name").val(data.tender_client[0].client_name);
                $("#location").val(data.tender_client[0].client_address);

                $("#client_email").tagsinput("removeAll");
                $("#client_email").tagsinput('add',data.tender_client[0].client_email);

                $("#client_landline_no").tagsinput("removeAll");
                $("#client_landline_no").tagsinput('add',data.tender_client[0].client_landline_no);

                $("#client_mobile_no").tagsinput("removeAll");
                $("#client_mobile_no").tagsinput('add',data.tender_client[0].client_mobile_no);

                $("#client_fax_no").tagsinput("removeAll");
                $("#client_fax_no").tagsinput('add',data.tender_client[0].client_fax_no);

                if(data.tender_authorites.length){
                    // console.log(data.tender_authorites);
                    contact_counter = data.tender_authorites.length;
                    $('#single_data_div').html("");
                    
                    $('#dynamic_data_div').html('');
                    $.each( data.tender_authorites, function(key, value){
                        $('#dynamic_data_div').append('<div id="dynamic_div_0">'+
                                        '<div class="row">'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Name<span class="error">*</span></label>'+
                                                '<input type="text" name="contact_name['+key+']" id="contact_name_'+contact_counter+'" class="form-control contact_name" value="'+value.authority_name+'">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Designation<span class="error">*</span></label>'+
                                                '<input type="text" name="contact_designation['+key+']" id="contact_designation_'+contact_counter+'" class="form-control contact_designation" value="'+value.authority_designation+'">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Email<span class="error">*</span></label>'+
                                                '<input type="text" name="contact_email['+key+']" id="contact_email_'+contact_counter+'" class="form-control contact_email" value="'+value.authority_email+'">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Phone Number<span class="error">*</span></label>'+
                                                '<input type="text" name="contact_phone['+key+']" id="contact_phone_'+contact_counter+'" class="form-control contact_phone" value="'+value.authority_mobile_no+'">'+
                                            '</div>'+
                                        '</div>'+
                                        '</div>'+
                                        '<div class="row">'+
                                            '<div class="col-md-3">'+
                                                '<button type="button" onclick="remove_more(this)" class="btn btn-danger remove_btn"><i class="fa fa-trash"></i> Remove</button>'+
                                            '</div>'+
                                        '</div>'+
                                        '<hr>'+
                                    '</div>');                    
                    });
                }else{
                    
                }
            }
        });
    });
</script>
@endsection
