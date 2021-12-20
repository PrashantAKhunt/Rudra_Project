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
                        <form action="{{url('save_tender')}}" id="tender_form" method="post">
                            @csrf
                            <div id="put_tender">
                                <div class="tender_div" id="tender_0">
                                    <div class="row">
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label class="control-label">Company <span class="error">*</span> </label>
                                          <select class="form-control company_id" name="company_id[]" id="company_id_0">
                                            <option value="">Select</option>
                                            @foreach($companies as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label class="control-label">Department <span class="error">*</span> </label>
                                          <select class="form-control department_id" name="department_id[]" id="department_id_0">
                                            <option value="">Select</option>
                                            @foreach($department as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label class="control-label">Tender Id Per Portal <span class="error">*</span> </label>
                                          <input type="text" class="form-control tender_id_per_portal" name="tender_id_per_portal[]" id="tender_id_per_portal_0">
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label class="control-label">Portal Name <span class="error">*</span> </label>
                                          <input type="text" class="form-control portal_name" name="portal_name[]" id="portal_name_0">
                                        </div>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Name Of Work <span class="error">*</span> </label>
                                          <textarea class="form-control name_of_work" name="name_of_work[]" id="name_of_work_0"></textarea>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Tender No. <span class="error">*</span> </label>
                                          <input type="text" class="form-control tender_no" name="tender_no[]" id="tender_no_0">
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">State Name Work Execute <span class="error">*</span> </label>
                                          <input type="text" class="form-control state_name_work_execute" name="state_name_work_execute[]" id="state_name_work_execute_0">
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Estimate Cost <span class="error">*</span> </label>
                                          <input type="text" class="form-control estimate_cost" name="estimate_cost[]" id="estimate_cost_0">
                                        </div>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Joint Venture <span class="error">*</span> </label>
                                          <select class="form-control joint_venture" name="joint_venture[]" id="joint_venture_0" onchange="addJointCount(0)">
                                              <option value="">Select</option>
                                              <option value="Yes">Yes</option>
                                              <option value="No">No</option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-3 joint_venture_count_div_0" style="display: none;">
                                          <div class="form-group">
                                            <label for="inputEmail4">Joint Venture Count</label>
                                            <input type="number" class="form-control joint_venture_count" name="joint_venture_count[]" id="joint_venture_count_0">
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Quote Type <span class="error">*</span> </label>
                                          <select class="form-control quote_type" name="quote_type[]" id="quote_type_0" onchange="addOtherQuoteType(0)">
                                              <option value="">Select</option>
                                              <option value="Item Rate">Item Rate</option>
                                              <option value="Lumsum Rate">Lumsum Rate</option>
                                              <option value="Other Type">Other Type</option>
                                              <option value="Percentage Rate">Percentage Rate</option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-3 other_quote_type_div_0" style="display: none;">
                                        <div class="form-group">
                                          <label for="inputEmail4">Other Quote Type</label>
                                          <input type="text" class="form-control other_quote_type" name="other_quote_type[]" id="other_quote_type_0">
                                        </div>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Tender Pattern <span class="error">*</span> </label>
                                          <select class="form-control tender_pattern" name="tender_pattern[]" id="tender_pattern_0">
                                              <option value="">Select</option>
                                              @foreach($tenderpattern as $key => $value)
                                              <option value="{{$key}}">{{$value}}</option>
                                              @endforeach
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Tender Category <span class="error">*</span> </label>
                                          <select class="form-control tender_category_id" name="tender_category_id[]" id="tender_category_id_0">
                                              <option value="">Select</option>
                                              @foreach($tendercategory as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                              @endforeach
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Client Name <span class="error">*</span> </label>
                                          <input type="text" class="form-control client_name" name="client_name[]" id="client_name_0">
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Client Address <span class="error">*</span> </label>
                                          <input type="text" class="form-control client_address" name="client_address[]" id="client_address_0">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row">
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Client Email <span class="error">*</span> </label>
                                          <input type="text" class="form-control client_email" name="client_email[]" id="client_email_0" data-role="tagsinput" />
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Client Landline Number <span class="error">*</span> </label>
                                          <input type="text" class="form-control client_landline_no" name="client_landline_no[]" id="client_landline_no_0" data-role="tagsinput" />
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Client Mobile Number <span class="error">*</span> </label>
                                          <input type="text" class="form-control client_mobile_no" name="client_mobile_no[]" id="client_mobile_no_0" data-role="tagsinput" />
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label for="inputEmail4">Client Fax Number <span class="error">*</span> </label>
                                          <input type="text" class="form-control client_fax_no" name="client_fax_no[]" id="client_fax_no_0" data-role="tagsinput"/>
                                        </div>
                                      </div>
                                    </div>
                                    <hr>
                                    <p><strong>Authority Detail</strong></p>
                                    <div id="authority_detail_part_0">
                                      <div class="row">
                                          <div class="authority_detail" id="authority_detail_div_0">
                                          <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="inputEmail4">Name <span class="error">*</span> </label>
                                              <input type="text" class="form-control authority_name" name="authority_name[0][]" id="authority_name_0">
                                            </div>
                                          </div>
                                          <div class="col-md-2">
                                            <div class="form-group">
                                              <label for="inputEmail4">Designation <span class="error">*</span> </label>
                                              <input type="text" class="form-control authority_designation" name="authority_designation[0][]" id="authority_designation_0">
                                            </div>
                                          </div>
                                          <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="inputEmail4">Email <span class="error">*</span> </label>
                                              <input type="text" class="form-control authority_email" name="authority_email[0][]" id="authority_email_0">
                                            </div>
                                          </div>
                                          <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="inputEmail4">Mobile Number <span class="error">*</span> </label>
                                              <input type="text" class="form-control authority_mobile_no" name="authority_mobile_no[0][]" id="authority_mobile_no_0">
                                            </div>
                                          </div>
                                          <div class="col-md-1" style="padding-top: 27px;">
                                            <div class="form-group">
                                              <button type="button" class="btn btn-primary" onclick="addAuthority(0)"><i class="fa fa-plus"></i></button>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="inputEmail4">Last Date Time Of Download</label>
                                          <input type="text" class="form-control last_date_time_download" name="last_date_time_download[]" id="last_date_time_download_0">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="inputEmail4">Last Date Time Of Online Submit</label>
                                          <input type="text" class="form-control last_date_time_online_submit" name="last_date_time_online_submit[]" id="last_date_time_online_submit_0">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="inputEmail4">Last Date Time Of physical Submit</label>
                                          <input type="text" class="form-control last_date_time_physical_submit" name="last_date_time_physical_submit[]" id="last_date_time_physical_submit_0">
                                        </div>
                                      </div>
                                    </div>
                                    {{-- <div class="row">
                                      <div class="col-md-4">
                                        <div class="form-group">
                                          <label for="inputEmail4">Assign Tender</label>
                                          <select name="assign_tender[0][]" id="assign_tender_0" class="select2 m-b-10 select2-multiple assign_tender" multiple="multiple" data-placeholder="Choose">
                                              @foreach($user as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                              @endforeach
                                          </select>
                                        </div>
                                      </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="row" style="padding-top: 10px;">
                                <div class="col-sm-6"></div> 
                                <div class="col-sm-4"></div> 
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary add_more" onclick="addMoreTender()"><i class="fa fa-plus"></i> Add More</button>
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
</div>
@endsection
@section('script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" integrity="sha256-yMjaV542P+q1RnH6XByCPDfUFhmOafWbeLPmqKh11zo=" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
<script>
var counter = 0;
var counter_authority = 0;
$(document).ready(function() {
load_datepicker();
  
});

$("body").on("click",".remove",function(){
      $(this).parents(".tender_div").remove();
  });

function addMoreTender(){
counter += 1;
counter_authority += 1;
$('#put_tender').append('<hr><div class="tender_div" id="tender_'+counter+'">'+
                              '<div class="row">'+
                              '<div class="col-md-2" style="margin-top: 29px;">'+
                                    '<div class="form-group">'+
                                      '<button type="button" class="btn btn-danger remove"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                    '</div>'+
                                  '</div>'+
                              '</div>'+
                               '<div class="row">'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label class="control-label">Company <span class="error">*</span> </label>'+
                                      '<select class="form-control company_id" name="company_id['+counter+']" id="company_id_'+counter+'">'+
                                        '<option value="">Select</option>'+
                                        @foreach($companies as $key => $value)
                                        '<option value="{{$key}}">{{$value}}</option>'+
                                        @endforeach
                                      '</select>'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label class="control-label">Department <span class="error">*</span> </label>'+
                                      '<select class="form-control department_id" name="department_id['+counter+']" id="department_id_'+counter+'">'+
                                        '<option value="">Select</option>'+
                                        @foreach($department as $key => $value)
                                        '<option value="{{$key}}">{{$value}}</option>'+
                                        @endforeach
                                      '</select>'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label class="control-label">Tender Id Per Portal <span class="error">*</span> </label>'+
                                      '<input type="text" class="form-control tender_id_per_portal" name="tender_id_per_portal['+counter+']" id="tender_id_per_portal_'+counter+'">'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label class="control-label">Portal Name <span class="error">*</span> </label>'+
                                      '<input type="text" class="form-control portal_name" name="portal_name['+counter+']" id="portal_name_'+counter+'">'+
                                    '</div>'+
                                  '</div>'+
                                '</div>'+

                                '<div class="row">'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Name Of Work <span class="error">*</span> </label>'+
                                      '<textarea class="form-control name_of_work" name="name_of_work['+counter+']" id="name_of_work_'+counter+'"></textarea>'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Tender No. <span class="error">*</span> </label>'+
                                      '<input type="text" class="form-control tender_no" name="tender_no['+counter+']" id="tender_no_'+counter+'">'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">State Name Work Execute <span class="error">*</span> </label>'+
                                      '<input type="text" class="form-control state_name_work_execute" name="state_name_work_execute['+counter+']" id="state_name_work_execute_'+counter+'">'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Estimate Cost <span class="error">*</span> </label>'+
                                      '<input type="text" class="form-control estimate_cost" name="estimate_cost['+counter+']" id="estimate_cost_'+counter+'">'+
                                    '</div>'+
                                  '</div>'+
                                '</div>'+

                                '<div class="row">'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Joint Venture <span class="error">*</span> </label>'+
                                      '<select class="form-control joint_venture" name="joint_venture['+counter+']" id="joint_venture_'+counter+'" onchange="addJointCount('+counter+')">'+
                                          '<option value="">Select</option>'+
                                          '<option value="Yes">Yes</option>'+
                                          '<option value="No">No</option>'+
                                      '</select>'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3 joint_venture_count_div_'+counter+'" style="display: none;">'+
                                        '<div class="form-group">'+
                                          '<label for="inputEmail4">Joint Venture Count</label>'+
                                          '<input type="number" class="form-control joint_venture_count" name="joint_venture_count['+counter+']" id="joint_venture_count_'+counter+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-3">'+
                                        '<div class="form-group">'+
                                          '<label for="inputEmail4">Quote Type <span class="error">*</span> </label>'+
                                          '<select class="form-control quote_type" name="quote_type['+counter+']" id="quote_type_'+counter+'" onchange="addOtherQuoteType('+counter+')">'+
                                              '<option value="">Select</option>'+
                                              '<option value="Percentage Rate">Percentage Rate</option>'+
                                              '<option value="Item Rate">Item Rate</option>'+
                                              '<option value="Lumsum Rate">Lumsum Rate</option>'+
                                              '<option value="Other Type">Other Type</option>'+
                                          '</select>'+
                                        '</div>'+
                                      '</div>'+
                                      '<div class="col-md-3 other_quote_type_div_'+counter+'" style="display: none;">'+
                                        '<div class="form-group">'+
                                          '<label for="inputEmail4">Other Quote Type <span class="error">*</span> </label>'+
                                          '<input type="text" class="form-control other_quote_type" name="other_quote_type['+counter+']" id="other_quote_type_'+counter+'">'+
                                        '</div>'+
                                      '</div>'+
                                '</div>'+

                                '<div class="row">'+
                                  
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Tender Pattern <span class="error">*</span> </label>'+
                                      '<select class="form-control tender_pattern" name="tender_pattern['+counter+']" id="tender_pattern_'+counter+'">'+
                                          '<option value="">Select</option>'+
                                          @foreach($tenderpattern as $key => $value)
                                            '<option value="{{$key}}">{{$value}}</option>'+
                                          @endforeach
                                      '</select>'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Tender Category <span class="error">*</span> </label>'+
                                      '<select class="form-control tender_category_id" name="tender_category_id['+counter+']" id="tender_category_id_'+counter+'">'+
                                          '<option value="">Select</option>'+
                                          @foreach($tendercategory as $key => $value)
                                            '<option value="{{$key}}">{{$value}}</option>'+
                                          @endforeach
                                      '</select>'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                        '<div class="form-group">'+
                                          '<label for="inputEmail4">Client Name <span class="error">*</span> </label>'+
                                          '<input type="text" class="form-control client_name" name="client_name['+counter+']" id="client_name_'+counter+'">'+
                                        '</div>'+
                                      '</div>'+
                                      '<div class="col-md-3">'+
                                        '<div class="form-group">'+
                                          '<label for="inputEmail4">Client Address <span class="error">*</span> </label>'+
                                          '<input type="text" class="form-control client_address" name="client_address['+counter+']" id="client_address_'+counter+'">'+
                                        '</div>'+
                                      '</div>'+
                                '</div>'+
                                '<div class="row">'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Client Email <span class="error">*</span> </label>'+
                                      '<input type="text" class="form-control client_email" name="client_email['+counter+']" id="client_email_'+counter+'" data-role="tagsinput" />'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Client Landline Number <span class="error">*</span> </label>'+
                                      '<input type="text" class="form-control client_landline_no" name="client_landline_no['+counter+']" id="client_landline_no_'+counter+'" data-role="tagsinput" />'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Client Mobile Number <span class="error">*</span> </label>'+
                                      '<input type="text" class="form-control client_mobile_no" name="client_mobile_no['+counter+']" id="client_mobile_no_'+counter+'" data-role="tagsinput" />'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-3">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Client Fax Number <span class="error">*</span> </label>'+
                                      '<input type="text" class="form-control client_fax_no" name="client_fax_no['+counter+']" id="client_fax_no_'+counter+'" data-role="tagsinput"/>'+
                                    '</div>'+
                                  '</div>'+
                                '</div>'+
                                '<hr><p><strong>Authority Detail</strong></p>'+
                                '<div id="authority_detail_part_'+counter+'">'+
                                  '<div class="row">'+
                                    '<div class="authority_detail" id="authority_detail_div_'+counter_authority+'">'+
                                      '<div class="col-md-3">'+
                                        '<div class="form-group">'+
                                          '<label for="inputEmail4">Name <span class="error">*</span> </label>'+
                                          '<input type="text" class="form-control authority_name" name="authority_name['+counter+']['+counter_authority+']" id="authority_name_'+counter_authority+'">'+
                                        '</div>'+
                                      '</div>'+
                                      '<div class="col-md-2">'+
                                        '<div class="form-group">'+
                                          '<label for="inputEmail4">Designation <span class="error">*</span> </label>'+
                                          '<input type="text" class="form-control authority_designation" name="authority_designation['+counter+']['+counter_authority+']" id="authority_designation_'+counter_authority+'">'+
                                        '</div>'+
                                      '</div>'+
                                      '<div class="col-md-3">'+
                                        '<div class="form-group">'+
                                          '<label for="inputEmail4">Email <span class="error">*</span> </label>'+
                                          '<input type="text" class="form-control authority_email" name="authority_email['+counter+']['+counter_authority+']" id="authority_email_'+counter_authority+'">'+
                                        '</div>'+
                                      '</div>'+
                                      '<div class="col-md-3">'+
                                        '<div class="form-group">'+
                                          '<label for="inputEmail4">Mobile Number <span class="error">*</span> </label>'+
                                          '<input type="text" class="form-control authority_mobile_no" name="authority_mobile_no['+counter+']['+counter_authority+']" id="authority_mobile_no_'+counter_authority+'">'+
                                        '</div>'+
                                      '</div>'+
                                      '<div class="col-md-1" style="padding-top: 27px;">'+
                                        '<div class="form-group">'+
                                          '<button type="button" class="btn btn-primary" onclick="addAuthority('+counter+')"><i class="fa fa-plus"></i></button>'+
                                        '</div>'+
                                      '</div>'+
                                    '</div>'+
                                  '</div>'+
                                '</div><hr>'+
                                '<div class="row">'+
                                  '<div class="col-md-4">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Last Date Time Of Download</label>'+
                                      '<input type="text" class="form-control last_date_time_download" name="last_date_time_download['+counter+']" id="last_date_time_download_'+counter+'">'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-4">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Last Date Time Of Online Submit</label>'+
                                      '<input type="text" class="form-control last_date_time_online_submit" name="last_date_time_online_submit['+counter+']" id="last_date_time_online_submit_'+counter+'">'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="col-md-4">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Last Date Time Of physical Submit</label>'+
                                      '<input type="text" class="form-control last_date_time_physical_submit" name="last_date_time_physical_submit['+counter+']" id="last_date_time_physical_submit_'+counter+'">'+
                                    '</div>'+
                                  '</div>'+
                                '</div>'+
                                /*'<div class="row">'+
                                  '<div class="col-md-4">'+
                                    '<div class="form-group">'+
                                      '<label for="inputEmail4">Assign Tender</label>'+
                                      '<select name="assign_tender['+counter
                                      +'][]" id="assign_tender_'+counter+'" class="select2 m-b-10 select2-multiple assign_tender" multiple="multiple" data-placeholder="Choose">'+
                                          '<option value="">Select</option>'+
                                          @foreach($user as $key => $value)
                                          '<option value="{{$key}}">{{$value}}</option>'+
                                          @endforeach
                                      '</select>'+
                                    '</div>'+
                                  '</div>'+
                                '</div>'+*/
                            '</div>');

// $("#assign_tender_"+counter).select2();
$('.client_email,.client_landline_no,.client_mobile_no,.client_fax_no').tagsinput('refresh');
load_datepicker();
}
// remove_authority
$("body").on("click",".remove_authority",function(){
      $(this).parents(".authority_detail").remove();
  });
function addAuthority(put_counter){
  counter_authority += 1;
  $("#authority_detail_part_"+put_counter).append('<div class="row">'+
                                      '<div class="authority_detail" id="authority_detail_div_'+counter_authority+'">'+
                                          '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                              '<label for="inputEmail4">Name <span class="error">*</span> </label>'+
                                              '<input type="text" class="form-control authority_name" name="authority_name['+counter+']['+counter_authority+']" id="authority_name_'+counter_authority+'">'+
                                            '</div>'+
                                          '</div>'+
                                          '<div class="col-md-2">'+
                                            '<div class="form-group">'+
                                              '<label for="inputEmail4">Designation <span class="error">*</span> </label>'+
                                              '<input type="text" class="form-control authority_designation" name="authority_designation['+counter+']['+counter_authority+']" id="authority_designation_'+counter_authority+'">'+
                                            '</div>'+
                                          '</div>'+
                                          '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                              '<label for="inputEmail4">Email <span class="error">*</span> </label>'+
                                              '<input type="text" class="form-control authority_email" name="authority_email['+counter+']['+counter_authority+']" id="authority_email_'+counter_authority+'">'+
                                            '</div>'+
                                          '</div>'+
                                          '<div class="col-md-3">'+
                                            '<div class="form-group">'+
                                              '<label for="inputEmail4">Mobile Number <span class="error">*</span> </label>'+
                                              '<input type="text" class="form-control authority_mobile_no" name="authority_mobile_no['+counter+']['+counter_authority+']" id="authority_mobile_no_'+counter_authority+'">'+
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

function addJointCount(id){
var check_val = $("#joint_venture_"+id).val();

if(check_val == "Yes"){
        $(".joint_venture_count_div_"+id).show();
    }else{
        $(".joint_venture_count_div_"+id).hide();
    }    
}

function addOtherQuoteType(id){
var check_val = $("#quote_type_"+id).val();

if(check_val == "Other Type"){
        $(".other_quote_type_div_"+id).show();
    }else{
        $(".other_quote_type_div_"+id).hide();
    }    
}

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

    
}
    // $("#assign_tender_"+counter).select2();        

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
    /*$('.assign_tender').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });*/
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
  /*submitHandler: function (form) {
      
      var isValid = true;
      var isMsg = "";
      $(".client_email").each(function() {
         var element = $(this);
         if (element.val() == "") {
             isValid = false;
             isMsg = "Please fill client email";
         }
      });
      $(".client_landline_no").each(function() {
         var element = $(this);
         if (element.val() == "") {
             isValid = false;
             isMsg = "Please fill client landline number";
         }
      });
      $(".client_mobile_no").each(function() {
         var element = $(this);
         if (element.val() == "") {
             isValid = false;
             isMsg = "Please fill client mobile number";
         }
      });
      $(".client_fax_no").each(function() {
         var element = $(this);
         if (element.val() == "") {
             isValid = false;
             isMsg = "Please fill client fax number";
         }
      });
      // $(".assign_tender").each(function() {
      //    var element = $(this);
      //    if (element.val() == "") {
      //        isValid = false;
      //        isMsg = "Please assign tender to user";
      //    }
      // });
      if(isValid){
        if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
      }else{
        $.toast({
          heading: isMsg,
          text: 'Please fill required fields.',
          position: 'top-right',
          loaderBg:'#ff6849',
          icon: 'error',
          hideAfter: 3500
        });
      }      
  }*/
});
</script>
@endsection
