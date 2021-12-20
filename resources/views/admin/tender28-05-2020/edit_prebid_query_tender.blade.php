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
                            {{-- <div class="col-md-3">
                                <button type="button" class="btn btn-danger btn-circle"><i class="fa fa-times"></i> </button>
                                <b>No Data Added Yet.</b>
                            </div>
                            <div class="col-md-9">
                                <button type="button" class="btn btn-success btn-circle"><i class="fa fa-check"></i> </button>
                                <b>Record added</b>(You can open the section and can update record if needed)
                            </div>
                            <br><br> --}}
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> TENDER DETAIL 
                                    {{-- <button type="button" class="btn btn-danger btn-circle"><i class="fa fa-times"></i> </button> --}}
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                        <form action="#" id="tender_form" method="post">
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
                                                  <input type="text" class="form-control" name="portal_name" id="portal_name" value="{{$tender['portal_name']}}">
                                                </div>
                                              </div>
                                            </div>

                                            <div class="row">
                                              
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Name Of Work</label>
                                                  <textarea class="form-control" name="name_of_work" id="name_of_work">{{$tender['name_of_work']}}</textarea>
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Tender No.</label>
                                                  <input type="text" class="form-control" name="tender_no" id="tender_no" value="{{$tender['tender_no']}}">
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">State Name Work Execute</label>
                                                  <input type="text" class="form-control" name="state_name_work_execute" id="state_name_work_execute" value="{{$tender['state_name_work_execute']}}">
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Estimate Cost</label>
                                                  <input type="text" class="form-control" name="estimate_cost" id="estimate_cost" value="{{$tender['estimate_cost']}}">
                                                </div>
                                              </div>
                                            </div>

                                            <div class="row">
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Joint Venture</label>
                                                  <select class="form-control" name="joint_venture" id="joint_venture">
                                                      <option value="">Select</option>
                                                      <option value="Yes" {{$tender['joint_venture'] == "Yes" ? "selected" : ""}}>Yes</option>
                                                      <option value="No" {{$tender['joint_venture'] == "No" ? "selected" : "" }}>No</option>
                                                  </select>
                                                </div>
                                              </div>
                                              <div class="col-md-3 joint_venture_count_div" style="display: none;">
                                                    <div class="form-group">
                                                      <label for="inputEmail4">Joint Venture Count</label>
                                                      <input type="number" class="form-control" name="joint_venture_count" id="joint_venture_count" value="{{$tender['joint_venture_count']}}">
                                                    </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Quote Type</label>
                                                  <select class="form-control" name="quote_type" id="quote_type">
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
                                                  <input type="text" class="form-control" name="other_quote_type" id="other_quote_type" value="{{$tender['other_quote_type']}}">
                                                </div>
                                              </div>
                                            </div>

                                            <div class="row">
                                              
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label for="inputEmail4">Tender Pattern</label>
                                                  <select class="form-control" name="tender_pattern" id="tender_pattern">
                                                      <option value="">Select</option>
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
                                                  <select class="form-control" name="tender_category_id" id="tender_category_id">
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
                                              
                                            </div>
                                            <hr>
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
                                        <button type="button" class="btn btn-success tender_form_btn">Save</button>
                                        </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                  <div class="panel panel-inverse">
                                      <div class="panel-heading"> Pre-Bid Meeting Query Point
                                          <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                      </div>
                                      <div class="panel-wrapper collapse" aria-expanded="false" style="height: 0px;">
                                          <div class="panel-body">
                                            <form action="#" id="tender_pre_bid_meet_form" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$tender_id}}">
                                              <div id="pre_bid_meeting_add_more">
                                                @if(count($tender_bid_meet))
                                                  @foreach($tender_bid_meet as $key_pb => $value_pb)
                                                    {{-- <div class="pre_bid_meeting" id="pre_bid_meeting_{{$key_pb}}">
                                                      <div class="row">
                                                          <h3 class="title">Query Point {{$key_pb + 1}}</h3>
                                                          <div class="row">
                                                              <div class="col-md-6">
                                                                  <div class="form-group">
                                                                      <label class="control-label">Query Point Document </label>
                                                                      <input type="text" class="form-control query_point_document_name" name="query_point_document_name[{{$key_pb}}]" id="query_point_document_name_{{$key_pb}}" value="{{$value_pb['query_point_document_name']}}">
                                                                  </div>
                                                              </div>
                                                              <div class="col-md-3">
                                                                  <div class="form-group">
                                                                      <label class="control-label">Document Attachment</label>
                                                                      <input type="file" class="form-control query_point_document_attechment" name="query_point_document_attechment[{{$key_pb}}]" id="query_point_document_attechment_{{$key_pb}}">
                                                                      <input type="hidden" name="query_point_document_attechment_hidden[{{$key_pb}}]" id="query_point_document_attechment_hidden_{{$key_pb}}" value="{{$value_pb['query_point_document_attechment']}}">
                                                                  </div>
                                                              </div>
                                                              <div class="col-md-1" style="margin-top: 29px;">
                                                                  <a href="{{url('downloadbiddoc')}}/{{$value_pb['id']}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                              </div>
                                                              <div class="col-md-2" style="margin-top: 29px;">
                                                                <div class="form-group">
                                                                  <button type="button" class="btn btn-danger remove_pre_bid" id="{{$value_pb['id']}}"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                                </div>
                                                              </div>
                                                          </div>
                                                          <div class="row">
                                                              <div class="col-md-3">
                                                                  <div class="form-group">
                                                                      <label class="control-label">Name of Section</label>
                                                                      <input type="text" class="form-control name_of_section" name="name_of_section[{{$key_pb}}]" id="name_of_section_{{$key_pb}}" value="{{$value_pb['name_of_section']}}">
                                                                  </div>
                                                              </div>
                                                              <div class="col-md-3">
                                                                  <div class="form-group">
                                                                      <label class="control-label">Clause No.</label>
                                                                      <input type="text" class="form-control clause_number" name="clause_number[{{$key_pb}}]" id="clause_number_{{$key_pb}}" value="{{$value_pb['clause_number']}}">
                                                                  </div>
                                                              </div>
                                                              <div class="col-md-3">
                                                                  <div class="form-group">
                                                                      <label class="control-label">Sub Clause No.</label>
                                                                      <input type="text" class="form-control sub_clause_number" name="sub_clause_number[{{$key_pb}}]" id="sub_clause_number_{{$key_pb}}" value="{{$value_pb['sub_clause_number']}}">
                                                                  </div>
                                                              </div>
                                                              <div class="col-md-3">
                                                                  <div class="form-group">
                                                                      <label class="control-label">Page No.</label>
                                                                      <input type="text" class="form-control page_number" name="page_number[{{$key_pb}}]" id="page_number_{{$key_pb}}" value="{{$value_pb['page_number']}}">
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                    </div> --}}
                                                  @endforeach
                                                @else
                                                  <div class="pre_bid_meeting" id="pre_bid_meeting_0">
                                                    <div class="row">
                                                        <h3 class="title">Query Point 1</h3>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label">Query Point Document </label>
                                                                    <input type="text" class="form-control query_point_document_name" name="query_point_document_name[]" id="query_point_document_name_0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">Document Attachment</label>
                                                                    <input type="file" class="form-control query_point_document_attechment" name="query_point_document_attechment[]" id="query_point_document_attechment_0">
                                                                </div>
                                                            </div>
                                                            {{-- <div class="col-md-1" style="margin-top: 29px;">
                                                                <a href="#" title="Download Already Uploaded Document" class="btn btn-primary btn-circle"><i class="fa fa-download"></i></a>
                                                            </div> --}}
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">Name of Section</label>
                                                                    <input type="text" class="form-control name_of_section" name="name_of_section[]" id="name_of_section_0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">Clause No.</label>
                                                                    <input type="text" class="form-control clause_number" name="clause_number[]" id="clause_number_0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">Sub Clause No.</label>
                                                                    <input type="text" class="form-control sub_clause_number" name="sub_clause_number[]" id="sub_clause_number_0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">Page No.</label>
                                                                    <input type="text" class="form-control page_number" name="page_number[]" id="page_number_0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                  </div>
                                                @endif
                                              </div>
                                              <div class="row" style="padding-top: 10px;">
                                                      <div class="col-sm-10"></div> 
                                                      <div class="col-sm-2">
                                                          <div class="form-group">
                                                              <button type="button" class="btn btn-primary" onclick="addPreBid()"><i class="fa fa-plus"></i> Add More</button>
                                                          </div>
                                                      </div>
                                                      <div class="col-sm-4"></div>  
                                                  </div>
                                                  <button type="button" class="btn btn-success tender_pre_bid_meet_form_btn">Save</button>
                                            </form>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> TENDER CORRIGENDUM
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a>  </div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <h3 class="title">Add Corrigendum</h3>
                                      <form action="#" id="tender_corrigendum_form" method="post" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Select Query (If corrigendum is for our query)</label>
                                                    <select class="form-control pre_bid_query_id" name="pre_bid_query_id" id="pre_bid_query_id">
                                                        <option value="">Select Query</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Corrigendum No.</label>
                                                    <input type="text" class="form-control corrigendum_number required" id="corrigendum_number" name="corrigendum_number">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Date</label>
                                                    <input type="text" class="form-control corrigendum_date required" name="corrigendum_date" id="corrigendum_date" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Sr. As Per Corrigendum</label>
                                                        <input type="text" class="form-control corrigendum_sr_number required" name="corrigendum_sr_number" id="corrigendum_sr_number">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="inputEmail4">Answer</label>
                                                        <textarea class="form-control corrigendum_answer required" name="corrigendum_answer" id="corrigendum_answer" spellcheck="false"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="inputEmail4">Corrigendum Attachment</label>
                                                        <input type="file" class="form-control corrigendum_attechement required" name="corrigendum_attechement" id="corrigendum_attechement">
                                                    </div>
                                                </div>
                                        </div>
                                        <button type="button" class="btn btn-success tender_corrigendum_form_btn">Save</button>
                                      </form>
                                        <hr>
                                        <h3 class="title">Corrigendum List</h3>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-striped" id="corrigendum_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Corrigendum No.</th>
                                                            <th>Date</th>
                                                            <th>Sr. As Per Corrigendum</th>
                                                            <th>Answer</th>
                                                            <th>Download</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
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
var counter_pre_bid = {{$tender_bid_meet_count}};
var counter_pre_bid_validate = {{$tender_bid_meet_count}};
var counter_authority = {{$tender_authority_contact_detail_count}};
var tender_id = "{{$tender_id}}";  
$(document).ready(function(){
getPreBidQuery();
getPreBidMeeting();
$('#corrigendum_table').DataTable({
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "stateSave": true,
    "order": [[0, "DESC"]],
    "ajax": {
        url: "<?php echo route('admin.get_corrigendum_list'); ?>",
        type: "POST",
        data :{
          "_token": "{{ csrf_token() }}",
          "id": tender_id
        }
    },
    "columns": [
        
        {"taregts": 1, 'data': 'corrigendum_number'},
        {"taregts": 2, 'data': 'corrigendum_date'},
        {"taregts": 3, 'data': 'corrigendum_sr_number'},
        {"taregts": 4, 'data': 'corrigendum_answer'},
        {"taregts": 5, "searchable": false, "orderable": false,
            "render": function (data, type, row) {
                var id = row.id;
                var out=""; 
                out = '<a href="<?php echo url('downloadcorrigendumdoc') ?>'+'/'+id+'" class="btn btn-primary btn-rounded" target="_blank"><i class="fa fa-download"></i></a>'; 
                return out;
            }
        },
    ]
  });
$(".select2").select2();  
});

// -------- Tender detail start ---------
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

$(".tender_form_btn").on('click',function(){
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
    if($("#tender_form").valid()){
      $(".tender_form_btn").attr("disabled", true);
        var tender_form = $("#tender_form").serialize();
        $.ajax({
          type : "POST",
          url : "{{url('save_tender_detail')}}",
          data : tender_form,
          success : function(data){
            console.log(data);
            $(".tender_form_btn").attr("disabled", false);
            var success = "Tender detail save successfully";
            var error = "Tender detail not save try again";
              alertMassage(data,success,error);
          }
        })
    }    
});
$("#tender_form").validate({
  ignore: [],
});

// -------- Tender detail end ---------

// --------- Pre bid meeting start ----------------

//pre bid remove
$("body").on("click",".remove_pre_bid",function(){
        $(this).parents(".pre_bid_meeting").remove();
  });

//pre bid delete
/*$("body").on("click",".remove_pre_bid_delete",function(){
  var remove_bid_delete_id = $(this).attr('id');        
  $(this).parents(".pre_bid_meeting").remove();
  $.ajax({
      type : "POST",
      url : "{{url('delete_bid_document')}}",
      data: {
        "_token": "{{ csrf_token() }}",
        "id": remove_bid_delete_id
      },
      success : function(data){
        // console.log(data);
          // $(this).parents(".financial_eligibility").remove();
      }
  });
});*/

function addPreBid(){
  counter_pre_bid += 1;
  var count_div = $("#pre_bid_meeting_add_more").find(".pre_bid_meeting").length ;
  $("#pre_bid_meeting_add_more").append('<div class="pre_bid_meeting" id="pre_bid_meeting_'+counter_pre_bid+'">'+
                                                    '<div class="row">'+
                                                        '<h3 class="title">Query Point '+(count_div +  1) +'</h3>'+
                                                        '<div class="row">'+
                                                            '<div class="col-md-6">'+
                                                                '<div class="form-group">'+
                                                                    '<label class="control-label">Query Point Document </label>'+
                                                                    '<input type="text" class="form-control query_point_document_name" name="query_point_document_name['+counter_pre_bid+']" id="query_point_document_name_'+counter_pre_bid+'">'+
                                                                '</div>'+
                                                            '</div>'+
                                                            '<div class="col-md-3">'+
                                                                '<div class="form-group">'+
                                                                    '<label class="control-label">Document Attachment</label>'+
                                                                    '<input type="file" class="form-control query_point_document_attechment" name="query_point_document_attechment['+counter_pre_bid+']" id="query_point_document_attechment_'+counter_pre_bid+'">'+
                                                                '</div>'+
                                                            '</div>'+
                                                            '<div class="col-md-2" style="margin-top: 29px;">'+
                                                            '<div class="form-group">'+
                                                                '<button type="button" class="btn btn-danger remove_pre_bid"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                              '</div>'+
                                                            '</div>'+
                                                        '</div>'+
                                                        '<div class="row">'+
                                                            '<div class="col-md-3">'+
                                                                '<div class="form-group">'+
                                                                    '<label class="control-label">Name of Section</label>'+
                                                                    '<input type="text" class="form-control name_of_section" name="name_of_section['+counter_pre_bid+']" id="name_of_section_'+counter_pre_bid+'">'+
                                                                '</div>'+
                                                            '</div>'+
                                                            '<div class="col-md-3">'+
                                                                '<div class="form-group">'+
                                                                    '<label class="control-label">Clause No.</label>'+
                                                                    '<input type="text" class="form-control clause_number" name="clause_number['+counter_pre_bid+']" id="clause_number_'+counter_pre_bid+'">'+
                                                                '</div>'+
                                                            '</div>'+
                                                            '<div class="col-md-3">'+
                                                                '<div class="form-group">'+
                                                                    '<label class="control-label">Sub Clause No.</label>'+
                                                                    '<input type="text" class="form-control sub_clause_number" name="sub_clause_number['+counter_pre_bid+']" id="sub_clause_number_'+counter_pre_bid+'">'+
                                                                '</div>'+
                                                            '</div>'+
                                                            '<div class="col-md-3">'+
                                                                '<div class="form-group">'+
                                                                    '<label class="control-label">Page No.</label>'+
                                                                    '<input type="text" class="form-control page_number" name="page_number['+counter_pre_bid+']" id="page_number_'+counter_pre_bid+'">'+
                                                                '</div>'+
                                                            '</div>'+
                                                        '</div>'+
                                                    '</div>'+
                                                  '</div>');
}


//add edit validation
$(".tender_pre_bid_meet_form_btn").on('click',function(){

  $('.query_point_document_name').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  $('.query_point_document_attechment').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });

  $('.name_of_section').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  $('.clause_number').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  $('.sub_clause_number').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  $('.page_number').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });

  for(j = 0 ; j < counter_pre_bid_validate ; j++){
    $("#query_point_document_attechment_"+j).removeClass("error").rules("remove");
  }

    if($("#tender_pre_bid_meet_form").valid()){
      $(".tender_pre_bid_meet_form_btn").attr("disabled", true);
      var form = $('#tender_pre_bid_meet_form')[0];
          var formData1 = new FormData(form);
            
          if(counter_pre_bid == 0){
              formData1.append('query_point_document_attechment[]', $('.query_point_document_attechment')[0].files[0]);
          }else{
            for(i = 0; i >= counter_pre_bid ; i++){
                formData1.append('query_point_document_attechment', $('.query_point_document_attechment')[1].files[1]);  
            }
          }  
        
          $.ajax({
            type : "POST",
            url : "{{url('tender_pre_bid_meet_query_point')}}",
            data : formData1,
            processData: false,
            contentType: false,
            success : function(data){
              // console.log(data);
              $(".tender_pre_bid_meet_form_btn").attr("disabled", false);
              var success = "Tender pre bid detail save successfully";
              var error = "Tender pre bid detail not save try again";
              getPreBidQuery();
              getPreBidMeeting();
              alertMassage(data,success,error);
            }
          });
    }
});
$("#tender_pre_bid_meet_form").validate();

// --------- Pre bid meeting end ----------------

//---------- Corrigendum pre_bid_meeting_query start ------------------
function getPreBidQuery(){

  $.ajax({
    type : "POST",
    url : "{{url('get_pre_bid_query')}}",
    data: {
        "_token": "{{ csrf_token() }}",
        "id": tender_id
      },
    success : function(data){
      $("#pre_bid_query_id").html(data);
    }
  });
}

$(".tender_corrigendum_form_btn").on('click',function(){
if($("#tender_corrigendum_form").valid()){
  $(".tender_corrigendum_form_btn").attr("disabled", true);
    var form = $('#tender_corrigendum_form')[0];
    var formData1 = new FormData(form);
      
    formData1.append('corrigendum_attechement', $('.corrigendum_attechement')[0].files[0]);   

    $.ajax({
      type : "POST",
      url : "{{url('save_tender_corrigendum')}}",
      data : formData1,
      processData: false,
      contentType: false,
      success : function(data){
        // console.log(data);
        $(".tender_corrigendum_form_btn").attr("disabled", false);
        var success = "Tender corrigendum save successfully";
        var error = "Tender corrigendum not save try again";
        $('#corrigendum_table').DataTable().ajax.reload();        
        getPreBidQuery();
        $("#tender_corrigendum_form")[0].reset();
        alertMassage(data,success,error);
      }
    });
}
});

jQuery('.corrigendum_date').datetimepicker({
            // format:'DD-MM-YYYY h:mm a',
            format:'DD-MM-YYYY',
            minDate : new Date(),
      });

//---------- Corrigendum pre_bid_meeting_query end ------------------
function alertMassage(data,success,error){
  if(data == "success"){
        swal(success, "", "success");
    }else{
        swal({
            title: error,
            //text: "You want to change status of admin user.",
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK",
            closeOnConfirm: true
        });
    }
}

//Get pre bid meeting
function getPreBidMeeting(){
$.ajax({
      type : "POST",
      url : "{{url('get_pre_bid_meeting')}}",
      data : {
        "id" : tender_id,
        "_token" : "{{csrf_token()}}"
      },
      success : function(data){
        data = JSON.parse(data);
        // console.log(data);
        if(data.length){

          $("#pre_bid_meeting_add_more").html('');
          counter_pre_bid = data.length - 1;
          counter_pre_bid_validate = data.length;
          $.each( data, function(key, value){

          var name_of_section = "";
          if(value.name_of_section != null){
            name_of_section = value.name_of_section
          }

          var clause_number = "";
          if(value.clause_number != null){
            clause_number = value.clause_number
          }

          var sub_clause_number = "";
          if(value.sub_clause_number != null){
            sub_clause_number = value.sub_clause_number
          }

          var page_number = "";
          if(value.page_number != null){
            page_number = value.page_number
          }

          $("#pre_bid_meeting_add_more").append('<div class="pre_bid_meeting" id="pre_bid_meeting_'+key+'">'+
                                                      '<div class="row">'+
                                                          '<h3 class="title">Query Point '+(key + 1) +'</h3>'+
                                                          '<div class="row">'+
                                                              '<div class="col-md-6">'+
                                                                  '<div class="form-group">'+
                                                                      '<label class="control-label">Query Point Document </label>'+
                                                                      '<input type="text" class="form-control query_point_document_name" name="query_point_document_name['+key+']" id="query_point_document_name_'+key+'" value="'+value.query_point_document_name+'">'+
                                                                  '</div>'+
                                                              '</div>'+
                                                              '<div class="col-md-3">'+
                                                                  '<div class="form-group">'+
                                                                      '<label class="control-label">Document Attachment</label>'+
                                                                      '<input type="file" class="form-control query_point_document_attechment" name="query_point_document_attechment['+key+']" id="query_point_document_attechment_'+key+'">'+
                                                                      '<input type="hidden" name="query_point_document_attechment_hidden['+key+']" id="query_point_document_attechment_hidden_'+key+'" value="'+value.query_point_document_attechment+'">'+
                                                                  '</div>'+
                                                              '</div>'+
                                                              '<div class="col-md-1" style="margin-top: 29px;">'+
                                                                  '<a href="{{url('downloadbiddoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
                                                              '</div>'+
                                                              '<div class="col-md-2" style="margin-top: 29px;">'+
                                                                '<div class="form-group">'+
                                                                  '<button type="button" class="btn btn-danger remove_pre_bid" id="'+value.id+'"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                                '</div>'+
                                                              '</div>'+
                                                          '</div>'+
                                                          '<div class="row">'+
                                                              '<div class="col-md-3">'+
                                                                  '<div class="form-group">'+
                                                                      '<label class="control-label">Name of Section</label>'+
                                                                      '<input type="text" class="form-control name_of_section" name="name_of_section['+key+']" id="name_of_section_'+key+'" value="'+name_of_section+'">'+
                                                                  '</div>'+
                                                              '</div>'+
                                                              '<div class="col-md-3">'+
                                                                  '<div class="form-group">'+
                                                                      '<label class="control-label">Clause No.</label>'+
                                                                      '<input type="text" class="form-control clause_number" name="clause_number['+key+']" id="clause_number_'+key+'" value="'+clause_number+'">'+
                                                                  '</div>'+
                                                              '</div>'+
                                                              '<div class="col-md-3">'+
                                                                  '<div class="form-group">'+
                                                                      '<label class="control-label">Sub Clause No.</label>'+
                                                                      '<input type="text" class="form-control sub_clause_number" name="sub_clause_number['+key+']" id="sub_clause_number_'+key+'" value="'+sub_clause_number+'">'+
                                                                  '</div>'+
                                                              '</div>'+
                                                              '<div class="col-md-3">'+
                                                                  '<div class="form-group">'+
                                                                      '<label class="control-label">Page No.</label>'+
                                                                      '<input type="text" class="form-control page_number" name="page_number['+key+']" id="page_number_'+key+'" value="'+page_number+'">'+
                                                                  '</div>'+
                                                              '</div>'+
                                                          '</div>'+
                                                      '</div>'+
                                                    '</div>');
          });
        }else{

        }
      }
    });
}
</script>
@endsection
