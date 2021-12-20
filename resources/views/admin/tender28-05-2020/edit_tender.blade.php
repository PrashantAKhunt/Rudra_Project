@extends('layouts.admin_app')

@section('content')
<style type="text/css">
    .tender_div{
        border: 1px solid #a5a0a0;
        padding: 13px;
        margin-bottom: 5px;
    }
</style>
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
                        <form action="{{url('update_tender')}}" id="tender_form" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{$tender_id}}">
                            <div class="tender_div1 after-add-more">
                                <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class="control-label">Company</label>
                                      <select class="form-control company_id" name="company_id" id="company_id">
                                        <option value="">Select</option>
                                        @foreach($companies as $key => $value)
                                        <option value="{{$key}}" {{ $tender['company_id'] == $key ? 'selected' : '' }}>{{$value}}</option>
                                        @endforeach
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class="control-label">Department</label>
                                      <select class="form-control department_id" name="department_id" id="department_id">
                                        <option value="">Select</option>
                                        @foreach($department as $key => $value)
                                        <option value="{{$key}}" {{ $tender['department_id'] == $key ? 'selected' : '' }}>{{$value}}</option>
                                        @endforeach
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class="control-label">Tender Id Per Portal</label>
                                      <input type="text" class="form-control tender_id_per_portal" name="tender_id_per_portal" id="tender_id_per_portal" value="{{$tender['tender_id_per_portal']}}">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label class="control-label">Portal Name</label>
                                      <input type="text" class="form-control portal_name" name="portal_name" id="portal_name" value="{{$tender['portal_name']}}">
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Name Of Work</label>
                                      <textarea class="form-control name_of_work" name="name_of_work" id="name_of_work">{{$tender['name_of_work']}}</textarea>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Tender No.</label>
                                      <input type="text" class="form-control tender_no" name="tender_no" id="tender_no" value="{{$tender['tender_no']}}">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">State Name Work Execute</label>
                                      <input type="text" class="form-control state_name_work_execute" name="state_name_work_execute" id="state_name_work_execute" value="{{$tender['state_name_work_execute']}}">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Estimate Cost</label>
                                      <input type="text" class="form-control estimate_cost" name="estimate_cost" id="estimate_cost" value="{{$tender['estimate_cost']}}">
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Joint Venture</label>
                                      <select class="form-control joint_venture" name="joint_venture" id="joint_venture">
                                          <option value="">Select</option>
                                          <option value="Yes" {{$tender['joint_venture'] == "Yes" ? "selected" : ""}}>Yes</option>
                                          <option value="No" {{$tender['joint_venture'] == "No" ? "selected" : "" }}>No</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3 joint_venture_count_div" style="display: none;">
                                        <div class="form-group">
                                          <label for="inputEmail4">Joint Venture Count</label>
                                          <input type="number" class="form-control joint_venture_count" name="joint_venture_count" id="joint_venture_count" value="{{$tender['joint_venture_count']}}">
                                        </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Quote Type</label>
                                      <select class="form-control quote_type" name="quote_type" id="quote_type">
                                          <option value="">Select</option>
                                          <option value="Percentage Rate" {{$tender['quote_type'] == "Percentage Rate" ? "selected" : ""}}>Percentage Rate</option>
                                          <option value="Item Rate" {{$tender['quote_type'] == "Item Rate" ? "selected" : ""}}>Item Rate</option>
                                          <option value="Lumsum Rate" {{$tender['quote_type'] == "Lumsum Rate" ? "selected" : ""}}>Lumsum Rate</option>
                                          <option value="Other Type" {{$tender['quote_type'] == "Other Type" ? "selected" : ""}}>Other Type</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3 other_quote_type_div" style="display: none;">
                                    <div class="form-group">
                                      <label for="inputEmail4">Other Quote Type</label>
                                      <input type="text" class="form-control other_quote_type" name="other_quote_type" id="other_quote_type" value="{{$tender['other_quote_type']}}">
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Tender Pattern</label>
                                      <select class="form-control tender_pattern" name="tender_pattern" id="tender_pattern">
                                          <option value="">Select</option>
                                          @foreach($tenderpattern as $key => $value)
                                          <option value="{{$key}}" {{$tender['tender_pattern'] == $key ? "selected" : ""}}>{{$value}}</option>
                                          @endforeach
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Tender Category</label>
                                      <select class="form-control tender_category_id" name="tender_category_id" id="tender_category_id">
                                          <option value="">Select</option>
                                          @foreach($tendercategory as $key => $value)
                                            <option value="{{$key}}" {{$tender['tender_category_id'] == $key ? "selected" : ""}}>{{$value}}</option>
                                          @endforeach
                                      </select>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Client Name</label>
                                      <input type="text" class="form-control client_name" name="client_name" id="client_name" value="{{$tender['client_name']}}">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Client Address</label>
                                      <input type="text" class="form-control client_address" name="client_address" id="client_address" value="{{$tender['client_address']}}">
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Client Email</label>
                                      <input type="text" class="form-control client_email" name="client_email" id="client_email" data-role="tagsinput" value="{{$tender['client_email']}}"/>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Client Landline Number</label>
                                      <input type="text" class="form-control client_landline_no" name="client_landline_no" id="client_landline_no" data-role="tagsinput" value="{{$tender['client_landline_no']}}"/>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Client Mobile Number</label>
                                      <input type="text" class="form-control client_mobile_no" name="client_mobile_no" id="client_mobile_no" data-role="tagsinput" value="{{$tender['client_mobile_no']}}"/>
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                      <label for="inputEmail4">Client Fax Number</label>
                                      <input type="text" class="form-control client_fax_no" name="client_fax_no" id="client_fax_no" data-role="tagsinput" value="{{$tender['client_fax_no']}}"/>
                                    </div>
                                  </div>
                                </div>
                                <hr>
                                <p><strong>Authority Detail</strong></p>
                                <div id="authority_detail_part">
                                  @if(count($tender_authority_contact_detail))
                                    @foreach($tender_authority_contact_detail as $key => $value)
                                        <div class="row">
                                            <div class="authority_detail" id="authority_detail_div_{{$key}}">
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Name</label>
                                                  <input type="text" class="form-control authority_name" name="authority_name[{{$key}}]" id="authority_name_{{$key}}" value="{{$value->authority_name}}">
                                                </div>
                                              </div>
                                              <div class="col-md-2">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Designation</label>
                                                  <input type="text" class="form-control authority_designation" name="authority_designation[{{$key}}]" id="authority_designation_{{$key}}" value="{{$value->authority_designation}}">
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Email</label>
                                                  <input type="text" class="form-control authority_email" name="authority_email[{{$key}}]" id="authority_email_{{$key}}" value="{{$value->authority_email}}">
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Mobile Number</label>
                                                  <input type="text" class="form-control authority_mobile_no" name="authority_mobile_no[{{$key}}]" id="authority_mobile_no_{{$key}}" value="{{$value->authority_mobile_no}}">
                                                </div>
                                              </div>
                                              @if($key == 0)
                                              <div class="col-md-1" style="padding-top: 27px;">
                                                <div class="form-group">
                                                  <button type="button" class="btn btn-primary" onclick="addAuthority()"><i class="fa fa-plus"></i></button>
                                                </div>
                                              </div>
                                              @endif
                                              @if($key > 0)
                                              <div class="col-md-1" style="padding-top: 27px;">
                                                  <div class="form-group">
                                                    <button type="button" class="btn btn-danger remove_authority"><i class="fa fa-times"></i></button>
                                                  </div>
                                                </div>
                                              @endif
                                          </div>
                                        </div>
                                    @endforeach
                                  @else
                                    <div class="row">
                                        <div class="authority_detail" id="authority_detail_div_0">
                                          <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="inputEmail4">Name</label>
                                              <input type="text" class="form-control authority_name" name="authority_name[]" id="authority_name_0">
                                            </div>
                                          </div>
                                          <div class="col-md-2">
                                            <div class="form-group">
                                              <label for="inputEmail4">Designation</label>
                                              <input type="text" class="form-control authority_designation" name="authority_designation[]" id="authority_designation_0">
                                            </div>
                                          </div>
                                          <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="inputEmail4">Email</label>
                                              <input type="text" class="form-control authority_email" name="authority_email[]" id="authority_email_0">
                                            </div>
                                          </div>
                                          <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="inputEmail4">Mobile Number</label>
                                              <input type="text" class="form-control authority_mobile_no" name="authority_mobile_no[]" id="authority_mobile_no_0">
                                            </div>
                                          </div>
                                          <div class="col-md-1" style="padding-top: 27px;">
                                            <div class="form-group">
                                              <button type="button" class="btn btn-primary" onclick="addAuthority()"><i class="fa fa-plus"></i></button>
                                            </div>
                                          </div>
                                      </div>
                                    </div>
                                  @endif
                                  
                                </div><hr>
                                <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label for="inputEmail4">Last Date Time Of Download</label>
                                      <input type="text" class="form-control last_date_time_download" name="last_date_time_download" id="last_date_time_download" value="{{date('d-m-Y H:i a',strtotime($tender['last_date_time_download']))}}">
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label for="inputEmail4">Last Date Time Of Online Submit</label>
                                      <input type="text" class="form-control last_date_time_online_submit" name="last_date_time_online_submit" id="last_date_time_online_submit" value="{{date('d-m-Y H:i a',strtotime($tender['last_date_time_online_submit']))}}">
                                    </div>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label for="inputEmail4">Last Date Time Of physical Submit</label>
                                      <input type="text" class="form-control last_date_time_physical_submit" name="last_date_time_physical_submit" id="last_date_time_physical_submit" value="{{date('d-m-Y H:i a',strtotime($tender['last_date_time_physical_submit']))}}">
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      @php
                                      $arr_assing_tender = explode(',', $tender['assign_tender']);
                                      @endphp
                                      <label for="inputEmail4">Assign Tender</label>
                                      <select name="assign_tender[]" id="assign_tender" class="select2 m-b-10 select2-multiple assign_tender" multiple="multiple" data-placeholder="Choose">
                                          @foreach($user as $key => $value)
                                             
                                            <option value="{{$key}}" @if (in_array($key, $arr_assing_tender)) selected  @endif>{{$value}}</option>
                                          
                                          @endforeach
                                      </select>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.tender') }}'" class="btn btn-default">Cancel</button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" integrity="sha256-yMjaV542P+q1RnH6XByCPDfUFhmOafWbeLPmqKh11zo=" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
<script>
var counter_authority = {{$tender_authority_contact_detail_count}};
$(document).ready(function() {
$('#joint_venture').trigger('change');
$('#quote_type').trigger('change');
load_datepicker();
});

$('#joint_venture').on('change', function(){
    var check_val = $(this).val();
    if(check_val == "Yes"){
        $(".joint_venture_count_div").show();
    }else{
        $(".joint_venture_count_div").hide();
    }    
});

$('#quote_type').on('change', function(){    
    var quote_type_val = $(this).val();
    if(quote_type_val == "Other Type"){
        $(".other_quote_type_div").show();
    }else{
        $(".other_quote_type_div").hide();
    }    
});

function load_datepicker(){
    jQuery('.last_date_time_download,.last_date_time_online_submit,.last_date_time_physical_submit').datetimepicker({
            format:'DD-MM-YYYY h:mm a',
      });

    // jQuery('.last_date_time_download,.last_date_time_online_submit,.last_date_time_physical_submit').datepicker({
    //         showDropdowns: true,
    //             timePicker: true,
    //             // timePickerIncrement: 30,
    //             locale: {
    //                 format: 'DD/MM/YYYY h:mm A'
    //             }
    //   });
    $(".select2").select2();        
}
$('form#tender_form').on('submit', function(event) {
    $('.department_id').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.company_id').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.tender_id_per_portal').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });    
    $('.portal_name').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.tender_no').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.name_of_work').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.state_name_work_execute').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.estimate_cost').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.joint_venture').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    /*$('.joint_venture_count').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });*/    
    $('.quote_type').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    /*$('.other_quote_type').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });*/
    $('.tender_pattern').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.tender_category_id').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.assign_tender').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.client_name').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.client_address').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.client_email').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.client_landline_no').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.client_mobile_no').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.client_fax_no').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.authority_name').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.authority_designation').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.authority_email').each(function() {
        $(this).rules('add', {
            required: true,
            email: true,
        });
    });
    $('.authority_mobile_no').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
});
$("#tender_form").validate({
  ignore: [],
  
});
// remove_authority
$("body").on("click",".remove_authority",function(){
      $(this).parents(".authority_detail").remove();
  });
function addAuthority(){
  counter_authority += 1;
  $("#authority_detail_part").append('<div class="row">'+
                                      '<div class="authority_detail" id="authority_detail_div_'+counter_authority+'">'+
                                          '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                              '<label for="inputEmail4">Name</label>'+
                                              '<input type="text" class="form-control authority_name" name="authority_name['+counter_authority+']" id="authority_name_'+counter_authority+'">'+
                                            '</div>'+
                                          '</div>'+
                                          '<div class="col-md-2">'+
                                            '<div class="form-group">'+
                                              '<label for="inputEmail4">Designation</label>'+
                                              '<input type="text" class="form-control authority_designation" name="authority_designation['+counter_authority+']" id="authority_designation_'+counter_authority+'">'+
                                            '</div>'+
                                          '</div>'+
                                          '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                              '<label for="inputEmail4">Email</label>'+
                                              '<input type="text" class="form-control authority_email" name="authority_email['+counter_authority+']" id="authority_email_'+counter_authority+'">'+
                                            '</div>'+
                                          '</div>'+
                                          '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                              '<label for="inputEmail4">Mobile Number</label>'+
                                              '<input type="text" class="form-control authority_mobile_no" name="authority_mobile_no['+counter_authority+']" id="authority_mobile_no_'+counter_authority+'">'+
                                            '</div>'+
                                          '</div>'+
                                          '<div class="col-md-1" style="padding-top: 27px;">'+
                                            '<div class="form-group">'+
                                              '<button type="button" class="btn btn-danger remove_authority"><i class="fa fa-times"></i></button>'+
                                            '</div>'+
                                          '</div>'+
                                          '</div>'+
                                        '</div>');
}
</script>
@endsection
