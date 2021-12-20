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
                            <form action="{{ route('admin.update_client') }}" id="edit_vendor" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $client_detail[0]->id }}" /> 
                            @csrf
							
                                <div class="row">
                                    <div class="col-md-6"> 
        							     <div class="form-group "> 
                                        <label>Company : {{$companies}}</label> 
                                        
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Tender : {{$tender}}</label> 
                                    </div>
                                </div>
                            </div>
						      <div class="row">
                                    <div class="col-md-6">	
                                    <div class="form-group "> 
                                        <label>Client Name <strong> <span class="text-danger">*</span></strong> </label> 
                                        <input type="text" class="form-control" name="client_name" id="client_name" value="{{ $client_detail[0]->client_name }}" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Address</label> 
                                        <input type="text" class="form-control" name="location" id="location" value="{{ $client_detail[0]->location }}" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Email <strong> <span class="text-danger">*</span></strong> </label> 
                                        <input type="text" class="form-control" data-role="tagsinput" name="email" id="client_email" value="{{ $client_detail[0]->email }}" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Landline Number <strong> <span class="text-danger">*</span></strong> </label> 
                                        <input type="text" class="form-control" data-role="tagsinput" name="client_landline_no" id="client_landline_no" value="{{ $client_detail[0]->client_landline_no }}" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Mobile Number <strong> <span class="text-danger">*</span></strong> </label> 
                                        <input type="text" class="form-control" data-role="tagsinput" name="client_mobile_no" id="client_mobile_no" value="{{ $client_detail[0]->client_mobile_no }}" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Client Fax Number <strong> <span class="text-danger">*</span></strong> </label> 
                                        <input type="text" class="form-control" data-role="tagsinput" name="client_fax_no" id="client_fax_no" value="{{ $client_detail[0]->client_fax_no }}" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">  
                                    <div class="form-group "> 
                                        <label>Client Detail</label> 
                                        <textarea class="form-control" name="detail" id="detail" rows="10">{{$client_detail[0]->detail}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <?php 
                            foreach ($clientDetail as $key => $clientDetailValue) {
                            ?>
                            <div class="row">
                                    <div class="col-md-12">
                                    @if($key == 0)
                                    <label><b>Client Contact Person Details</b></label>
                                    <hr>
                                    <br>
                                    @endif
                                    
                                    <div id="single_data_div">
                                        <div id="dynamic_div_0">
                                            <div class="row">
                                                <!--/span-->
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Name <strong> <span class="text-danger">*</span></strong> </label>
                                                        <input type="text" name="contact_name[{{$key}}]" id="contact_name_{{$key}}" class="form-control" value="<?php echo $clientDetailValue['client_name'];?>">
                                                        <input type="hidden" name="contact_id[{{$key}}]" id="contact_id_{{$key}}" class="form-control contact_name" value="<?php echo $clientDetailValue['id'];?>">
                                                    </div>
                                                </div>
                                                <!--/span-->
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Designation <strong> <span class="text-danger">*</span></strong> </label>
                                                        <input type="text" name="contact_designation[{{$key}}]" id="contact_designation_{{$key}}" class="form-control contact_designation" value="<?php echo $clientDetailValue['client_designation'];?>">
                                                    </div>
                                                    <!--/span-->
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Email <strong> <span class="text-danger">*</span></strong> </label>
                                                        <input type="text" name="contact_email[{{$key}}]" id="contact_email_{{$key}}" class="form-control contact_email" value="<?php echo $clientDetailValue['client_email'];?>">
                                                    </div>
                                                    <!--/span-->
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Phone Number <strong> <span class="text-danger">*</span></strong> </label>
                                                        <input type="text" name="contact_phone[{{$key}}]" id="contact_phone_{{$key}}" class="form-control contact_phone" value="<?php echo $clientDetailValue['client_phone_number'];?>">
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
                            <?php
                                }
                            ?>
                            <div class="row">
                                    <div class="col-md-12">
                                    <div id="dynamic_data_div">

                                    </div>
                                </div>
                            </div>
                            <!-- hidden div for copy -->
                            <div id="dynamic_data_div_hide" style="display: none;">
                               <div id="dynamic_div_hide">
                                  <div class="row">
                                     <!--/span-->
                                     <div class="col-md-3">
                                        <div class="form-group">
                                           <label class="control-label">Name</label>
                                           <input type="text" name="contact_name[]" id="contact_name" class="form-control">
                                           <input type="hidden" name="contact_id[]" id="contact_id" class="form-control">
                                        </div>
                                     </div>
                                     <div class="col-md-3">
                                        <div class="form-group">
                                           <label class="control-label">Designation</label>
                                           <input type="text" name="contact_designation[]" id="contact_designation" class="form-control">
                                        </div>
                                        <!--/span-->
                                     </div>
                                     <!--/span-->
                                     <div class="col-md-3">
                                        <div class="form-group">
                                           <label class="control-label">Email</label>
                                           <input type="text" name="contact_email[]" id="contact_email" class="form-control">
                                        </div>
                                        <!--/span-->
                                     </div>
                                     <div class="col-md-3">
                                        <div class="form-group">
                                           <label class="control-label">Phone Number</label>
                                           <input type="text" name="contact_phone[]" id="contact_phone" class="form-control">
                                        </div>
                                        <!--/span-->
                                     </div>
                                     
                                  </div>
                                  <div class="row">
                                     <div class="col-md-3">
                                        <button type="button" onclick="remove_more(this)" class="btn btn-danger btnremove_show"><i class="fa fa-trash"></i> Remove</button>
                                     </div>
                                  </div>
                                  <hr>
                               </div>
                            </div>
                            <!-- End div for copy -->

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" onclick="add_more(this);" id="add_button" class="btn btn-primary"><i class="fa fa-plus"></i> Add More</button>
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
var contact_counter = {{$clientDetail_Count}};
$('form#edit_vendor').on('submit', function(event) {
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
    jQuery("#edit_vendor").validate({
        ignore: [],
        rules: {
            client_name: {
                required: true,
            },
            /*'contact_name[]': {
                required: true,
            },
            'contact_email[]': {
                required: true,
                email:true
            },
            'contact_phone[]': {
                required: true,
            },*/
            email: {
                required: true,
            },
            /*company_id: {
                required: true,
            },*/
            client_landline_no : {
                required : true
            },
            client_mobile_no : {
                required : true
            },
            client_fax_no : {
                required : true
            },
            pan_card_number:{
                required: true,
                remote: {
                    url: "{{ url('check_uniquePancardNumber') }}", //check_uniqueRoleName
                    type: "post",
                    data: {
                        pan_card_number: function () {
                            return $("#pan_card_number").val();
                        },
                        vendor_id:function(){
                            return $('#id').val();
                        },
						company_id:function(){
							return $("#company_id").val();
						},
                        "_token": "{{ csrf_token() }}",
                    }
                }
            },
            /*detail: {
                required: true,
            },*/        
        },
         // Specify the validation error messages
        messages: {
            pan_card_number: {
                required: "Pan Card Number is required.",
                remote: "Pan Card Number is already exists."
            }
        }
    });
    
    function add_more(e) {
        /*var old_html = $('#dynamic_data_div_hide').html();
        var new_html = old_html.replace('remove_btn', 'btnremove_show');
        $('#dynamic_data_div').append(new_html);*/
        
        /*$("div#dynamic_data_div").find("#contact_phone").prop('required',true);
        $("div#dynamic_data_div").find("#contact_name").prop('required',true);
        $("div#dynamic_data_div").find("#contact_email").prop('required',true);
*/
        /*$('.input-daterange-datepicker').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('.btnremove_show').show(); */

        contact_counter += 1;
        $('#dynamic_data_div').append('<div id="dynamic_div_0">'+
                                        '<div class="row">'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Name</label>'+
                                                '<input type="text" name="contact_name['+contact_counter+']" id="contact_name_'+contact_counter+'" class="form-control contact_name">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Designation</label>'+
                                                '<input type="text" name="contact_designation['+contact_counter+']" id="contact_designation_'+contact_counter+'" class="form-control contact_designation">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                                '<label class="control-label">Email</label>'+
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

</script>
@endsection