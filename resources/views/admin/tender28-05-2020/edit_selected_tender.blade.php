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
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger btn-circle"><i class="fa fa-times"></i> </button>
                            <b>No Data Added Yet.</b>
                        </div>
                        <div class="col-md-9">
                            <button type="button" class="btn btn-success btn-circle"><i class="fa fa-check"></i> </button>
                            <b>Record added</b>(You can open the section and can update record if needed)
                        </div>
                        <br><br>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Tender Detail
                                    @if($tender['tender_sr_no'])
                                        <button type="button" class="btn btn-success btn-circle" id="tender_detail_info"><i class="fa fa-check" id="tender_detail_info1"></i> </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_detail_info"><i class="fa fa-times" id="tender_detail_info1"></i> </button>
                                    @endif
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
                                  <div class="panel-heading"> Tender Fee
                                    @if($tender['tender_fee'])
                                        <button type="button" class="btn btn-success btn-circle" id="tender_fee_info"><i class="fa fa-check" id="tender_fee_info1"></i> </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_fee_info"><i class="fa fa-times" id="tender_fee_info1"></i> </button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a> </div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form action="#" id="tender_fee_form" method="post">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div class="row">
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <label class="control-label">Tender Fee</label>
                                              <select class="form-control tender_fee required" name="tender_fee" id="tender_fee" >
                                                <option value="">Select</option>
                                                <option value="Yes" {{$tender['tender_fee'] == "Yes" ? "selected" : ""}}>Yes</option>
                                                <option value="No" {{$tender['tender_fee'] == "No" ? "selected" : ""}}>No</option>
                                            </select>
                                            </div>
                                          </div>
                                          <div class="col-md-4 tender_fee_div">
                                            <div class="form-group">
                                              <label class="control-label">Tender Fee Amount</label>
                                              <input type="text" class="form-control tender_fee_amount required" name="tender_fee_amount" id="tender_fee_amount" value="{{$tender['tender_fee_amount'] != "" ? $tender['tender_fee_amount'] : ""}}">
                                            </div>
                                          </div>
                                          <div class="col-md-4 tender_fee_div">
                                            <div class="form-group">
                                              <label class="control-label">In Favour Of</label>
                                              <input type="text" class="form-control tender_fee_in_favour_of required" name="tender_fee_in_favour_of" id="tender_fee_in_favour_of" value="{{$tender['tender_fee_in_favour_of'] != "" ? $tender['tender_fee_in_favour_of'] : ""}}">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class="col-md-4 tender_fee_div">
                                            <div class="form-group">
                                              <label class="control-label">In Form Of</label>
                                              <select class="form-control tender_fee_in_form_of required" name="tender_fee_in_form_of" id="tender_fee_in_form_of">
                                                <option value="">Select</option>
                                                <option value="Demand Draft" {{$tender['tender_fee_in_form_of'] == "Demand Draft" ? "selected" : ""}}>Demand Draft</option>
                                                <option value="Online" {{$tender['tender_fee_in_form_of'] == "Online" ? "selected" : ""}}>Online</option>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="col-md-4 tender_fee_div">
                                            <div class="form-group">
                                              <label class="control-label">Tender Fee Validity</label>
                                              <select class="form-control tender_fee_validity required" name="tender_fee_validity" id="tender_fee_validity">
                                                <option value="">Select</option>
                                                <option value="1 Month" {{$tender['tender_fee_validity'] == "1 Month" ? "selected" : ""}}>1 Month</option>
                                                <option value="3 Month" {{$tender['tender_fee_validity'] == "3 Month" ? "selected" : ""}}>3 Month</option>
                                                <option value="6 Month" {{$tender['tender_fee_validity'] == "6 Month" ? "selected" : ""}}>6 Month</option>
                                                <option value="1 Year" {{$tender['tender_fee_validity'] == "1 Year" ? "selected" : ""}}>1 Year</option>
                                                <option value="On Date" {{$tender['tender_fee_validity'] == "On Date" ? "selected" : ""}}>On Date</option>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="col-md-4 tender_fee_validity_date_div" style="display: none">
                                            <div class="form-group">
                                              <label class="control-label">Tender Fee Validity Date</label>
                                              <input type="text" class="form-control tender_fee_validity_date required" name="tender_fee_validity_date" id="tender_fee_validity_date" value="{{$tender['tender_fee_validity_date'] != "" ? date('d-m-Y',strtotime($tender['tender_fee_validity_date'])) : ""}}">
                                            </div>
                                          </div>
                                        </div>
                                        <button type="button" class="btn btn-success tender_fee_form_btn">Save Tender Fee</button>
                                      </form>
                                     </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Tender EMD
                                    @if($tender['tender_emd'])
                                        <button type="button" class="btn btn-success btn-circle" id="tender_emd_info"><i class="fa fa-check" id="tender_emd_info1"></i> </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_emd_info"><i class="fa fa-times" id="tender_emd_info1"></i> </button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a>  </div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form action="#" id="tender_emd_form" method="post">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div class="row">
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <label class="control-label">Tender EMD</label>
                                              <select class="form-control tender_emd required" name="tender_emd" id="tender_emd" >
                                                <option value="">Select</option>
                                                <option value="Yes" {{$tender['tender_emd'] == "Yes" ? "selected" : ""}}>Yes</option>
                                                <option value="No" {{$tender['tender_emd'] == "No" ? "selected" : ""}}>No</option>
                                            </select>
                                            </div>
                                          </div>
                                          <div class="col-md-4 tender_emd_div">
                                            <div class="form-group">
                                              <label class="control-label">Tender EMD Amount</label>
                                              <input type="text" class="form-control tender_emd_amount required" name="tender_emd_amount" id="tender_emd_amount" value="{{$tender['tender_emd_amount'] != "" ? $tender['tender_emd_amount'] : ""}}">
                                            </div>
                                          </div>
                                          <div class="col-md-4 tender_emd_div">
                                            <div class="form-group">
                                              <label class="control-label">In Favour Of</label>
                                              <input type="text" class="form-control tender_emd_in_favour_of required" name="tender_emd_in_favour_of" id="tender_emd_in_favour_of" value="{{$tender['tender_emd_in_favour_of'] != "" ? $tender['tender_emd_in_favour_of'] : ""}}">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row">
                                          <div class="col-md-4 tender_emd_div">
                                            <div class="form-group">
                                              <label class="control-label">In Form Of</label>
                                              <select class="form-control tender_emd_in_form_of required" name="tender_emd_in_form_of" id="tender_emd_in_form_of">
                                                <option value="">Select</option>
                                                <option value="Demand Draft" {{$tender['tender_emd_in_form_of'] == "Demand Draft" ? "selected" : ""}}>Demand Draft</option>
                                                <option value="Bank Gaurantee" {{$tender['tender_emd_in_form_of'] == "Bank Gaurantee" ? "selected" : ""}}>Bank Gaurantee</option>
                                                <option value="FDR" {{$tender['tender_emd_in_form_of'] == "FDR" ? "selected" : ""}}>FDR</option>
                                                <option value="SDR" {{$tender['tender_emd_in_form_of'] == "SDR" ? "selected" : ""}}>SDR</option>
                                                <option value="Documents" {{$tender['tender_emd_in_form_of'] == "Documents" ? "selected" : ""}}>Documents</option>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="col-md-4 tender_emd_div">
                                            <div class="form-group">
                                              <label class="control-label">Tender EMD Validity</label>
                                              <select class="form-control tender_emd_validity required" name="tender_emd_validity" id="tender_emd_validity">
                                                <option value="">Select</option>
                                                <option value="1 Month" {{$tender['tender_emd_validity'] == "1 Month" ? "selected" : ""}}>1 Month</option>
                                                <option value="3 Month" {{$tender['tender_emd_validity'] == "3 Month" ? "selected" : ""}}>3 Month</option>
                                                <option value="6 Month" {{$tender['tender_emd_validity'] == "6 Month" ? "selected" : ""}}>6 Month</option>
                                                <option value="1 Year" {{$tender['tender_emd_validity'] == "1 Year" ? "selected" : ""}}>1 Year</option>
                                                <option value="2 Year" {{$tender['tender_emd_validity'] == "2 Year" ? "selected" : ""}}>2 Year</option>
                                                <option value="3 Year" {{$tender['tender_emd_validity'] == "3 Year" ? "selected" : ""}}>3 Year</option>
                                                <option value="5 Year" {{$tender['tender_emd_validity'] == "5 Year" ? "selected" : ""}}>5 Year</option>
                                                <option value="On Date" {{$tender['tender_emd_validity'] == "On Date" ? "selected" : ""}}>On Date</option>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="col-md-4 tender_emd_validity_date_div">
                                            <div class="form-group">
                                              <label class="control-label">Tender EMD Validity Date</label>
                                              <input type="text" class="form-control tender_emd_validity_date required" name="tender_emd_validity_date" id="tender_emd_validity_date" value="{{$tender['tender_emd_validity_date'] != "" ? date('d-m-Y',strtotime($tender['tender_emd_validity_date'])) : ""}}">
                                            </div>
                                          </div>
                                        </div>
                                        <button type="button" class="btn btn-success tender_emd_form_btn">Save Tender EMD</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Technical Eligibility Criteria
                                    @if(count($tender_technical_eligibility))
                                        <button type="button" class="btn btn-success btn-circle" id="tender_tech_info"><i class="fa fa-check" id="tender_tech_info1"></i> </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_tech_info"><i class="fa fa-times" id="tender_tech_info1"></i> </button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a>  </div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form action="#" id="tender_tech_eli_form" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div id="technical_eligibility_add_more">
                                          @if(count($tender_technical_eligibility))
                                            {{-- @foreach($tender_technical_eligibility as $key_te => $value_te) --}}
                                              {{-- <div class="technical_eligibility" id="technical_eligibility_{{$key_te}}">
                                                <div class="row">
                                                  <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Document Name</label>
                                                        <input type="text" class="form-control technical_eligibility_document_name" name="technical_eligibility_document_name[{{$key_te}}]" id="technical_eligibility_document_name_{{$key_te}}" value="{{$value_te['document_name']}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Document Attechement</label>
                                                        <input type="file" class="form-control technical_eligibility_document_attechement" name="technical_eligibility_document_attechement[{{$key_te}}]" id="technical_eligibility_document_attechement_{{$key_te}}" data-do_id="{{$value_te['id']}}" >
                                                        <input type="hidden" name="technical_eligibility_document_attechement_hidden[{{$key_te}}]" id="technical_eligibility_document_attechement_hidden_{{$key_te}}" value="{{$value_te['document_attechement']}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-1" style="margin-top: 29px;">
                                                    <div class="form-group">
                                                      <a href="{{url('downloadtechdoc')}}/{{$value_te['id']}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                    </div>
                                                  </div>
                                                  <div class="col-md-2" style="margin-top: 29px;">
                                                    <div class="form-group">
                                                      <button type="button" class="btn btn-danger remove_tech_eli" id="{{$value_te['id']}}"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div> --}}
                                            {{-- @endforeach --}}
                                          @else
                                            <div class="technical_eligibility" id="technical_eligibility_0">
                                              <div class="row">
                                                <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">Document Name</label>
                                                      <input type="text" class="form-control technical_eligibility_document_name" name="technical_eligibility_document_name[]" id="technical_eligibility_document_name_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                      <label class="control-label">Document Attechement</label>
                                                      <input type="file" class="form-control technical_eligibility_document_attechement" name="technical_eligibility_document_attechement[]" id="technical_eligibility_document_attechement_0">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          @endif
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-sm-6"></div> 
                                            <div class="col-sm-3"></div>  
                                            <div class="col-sm-3">
                                                  <div class="form-group">
                                                      <button type="button" class="btn btn-primary add_more" onclick="addMoreTechEli()"><i class="fa fa-plus"></i> Add More</button>
                                                  </div>
                                              </div>
                                        </div>
                                        <button type="button" class="btn btn-success tender_tech_eli_form_btn">Save</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Financial Eligibility Criteria
                                    @if(count($tender_financial_eligibility))
                                        <button type="button" class="btn btn-success btn-circle" id="tender_fina_info"><i class="fa fa-check" id="tender_fina_info1"></i> </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_fina_info"><i class="fa fa-times" id="tender_fina_info1"></i> </button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a>  </div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form action="#" id="tender_fina_eli_form" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div id="financial_eligibility_add_more">
                                          @if(count($tender_financial_eligibility))
                                              {{-- @foreach($tender_financial_eligibility as $key_fe => $value_fe) --}}
                                                  {{-- <div class="financial_eligibility" id="financial_eligibility_{{$key_fe}}">
                                                    <div class="row">
                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Document Name</label>
                                                            <input type="text" class="form-control financial_eligibility_document_name" name="financial_eligibility_document_name[{{$key_fe}}]" id="financial_eligibility_document_name_{{$key_fe}}" value="{{$value_fe['document_name']}}">
                                                        </div>
                                                      </div>
                                                      <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Document Attechement</label>
                                                            <input type="file" class="form-control financial_eligibility_document_attechement" name="financial_eligibility_document_attechement[{{$key_fe}}]" id="financial_eligibility_document_attechement_{{$key_fe}}" data-do_id="{{$value_fe['id']}}">
                                                            <input type="hidden" name="financial_eligibility_document_attechement_hidden[{{$key_fe}}]" id="financial_eligibility_document_attechement_hidden_{{$key_fe}}" value="{{$value_fe['document_attechement']}}">
                                                        </div>
                                                      </div>
                                                      <div class="col-md-1" style="margin-top: 29px;">
                                                        <div class="form-group">
                                                          <a href="{{url('downloadfinadoc')}}/{{$value_fe['id']}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                        </div>
                                                      </div>
                                                      <div class="col-md-2" style="margin-top: 29px;">
                                                        <div class="form-group">
                                                          <button type="button" class="btn btn-danger remove_fina_eli" id="{{$value_fe['id']}}"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div> --}}      
                                              {{-- @endforeach --}}
                                          @else
                                            <div class="financial_eligibility" id="financial_eligibility_0">
                                              <div class="row">
                                                <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">Document Name</label>
                                                      <input type="text" class="form-control financial_eligibility_document_name" name="financial_eligibility_document_name[]" id="financial_eligibility_document_name_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                      <label class="control-label">Document Attechement</label>
                                                      <input type="file" class="form-control financial_eligibility_document_attechement" name="financial_eligibility_document_attechement[]" id="financial_eligibility_document_attechement_0">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          @endif
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-sm-6"></div> 
                                            <div class="col-sm-3"></div>  
                                            <div class="col-sm-3">
                                                  <div class="form-group">
                                                      <button type="button" class="btn btn-primary add_more" onclick="addMoreFinaEli()"><i class="fa fa-plus"></i> Add More</button>
                                                  </div>
                                              </div>
                                        </div>
                                        <button type="button" class="btn btn-success tender_fina_eli_form_btn">Save</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Pre Bid Meeting
                                  @if($tender['pre_bid_meeting'])
                                        <button type="button" class="btn btn-success btn-circle" id="tender_bid_info"><i class="fa fa-check" id="tender_bid_info1"></i> </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_bid_info"><i class="fa fa-times" id="tender_bid_info1"></i> </button>
                                    @endif 
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form action="#" id="tender_pre_bid_meet_form" method="post" enctype="multipart/form-data">
                                      @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div class="row">
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <label class="control-label">Pre Bid Meeting</label>
                                              <select class="form-control pre_bid_meeting" name="pre_bid_meeting" id="pre_bid_meeting">
                                                <option value="">Select</option>  
                                                <option value="Yes" {{$tender['pre_bid_meeting'] == 'Yes' ? "selected" : ""}}>Yes</option>  
                                                <option value="No" {{$tender['pre_bid_meeting'] == 'No' ? "selected" : ""}}>No</option>  
                                              </select>
                                              <input type="hidden" name="id" value="{{$tender_id}}">
                                            </div>
                                          </div>
                                          <div class="col-md-3 prebid_meeting_div">
                                              <div class="form-group">
                                                  <label class="control-label">Pre-Bid Meeting Date-time</label>
                                                  <input type="text" class="form-control pre_bid_meeting_datetime" name="pre_bid_meeting_datetime" id="pre_bid_meeting_datetime" value="{{$tender['pre_bid_meeting_datetime'] != "" ? date('d-m-Y H:i a',strtotime($tender['pre_bid_meeting_datetime'])) : "" }}">
                                              </div>
                                          </div>
                                          <div class="col-md-3 prebid_meeting_div">
                                              <div class="form-group">
                                                  <label class="control-label">Pre-Bid Meeting Venue</label>
                                                  <input type="text" class="form-control pre_bid_meeting_venue" name="pre_bid_meeting_venue" id="pre_bid_meeting_venue" value="{{$tender['pre_bid_meeting_venue']}}">
                                              </div>
                                          </div>
                                        </div>
                                        <div id="pre_bid_meeting_add_more">
                                          @if(count($tender_bid_meet))
                                            {{-- @foreach($tender_bid_meet as $key_bid => $value_bid) --}}
                                            {{-- <div class="pre_bid_meeting" id="pre_bid_meeting_{{$key_bid}}">
                                                <div class="row prebid_meeting_div">
                                                  @if($key_bid == 0)
                                                  <h3 class="title">Pre-Bid Meeting Query Point Documents</h3>
                                                  @endif
                                                  <div class="col-md-6">
                                                      <div class="form-group">
                                                          <label class="control-label">Query Point Document </label>
                                                          <input type="text" class="form-control query_point_document_name" name="query_point_document_name[{{$key_bid}}]" id="query_point_document_name_{{$key_bid}}" value="{{$value_bid['query_point_document_name']}}">
                                                      </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                      <div class="form-group">
                                                          <label class="control-label">Document Attachment</label>
                                                          <input type="file" class="form-control query_point_document_attechment" name="query_point_document_attechment[{{$key_bid}}]" id="query_point_document_attechment_{{$key_bid}}" data-do_id="{{$value_bid['id']}}">
                                                          <input type="hidden" name="query_point_document_attechment_hidden[{{$key_bid}}]" id="query_point_document_attechment_hidden_{{$key_bid}}" value="{{$value_bid['query_point_document_attechment']}}">
                                                      </div>
                                                  </div>
                                                  <div class="col-md-1" style="margin-top: 29px;">
                                                    <div class="form-group">
                                                      <a href="{{url('downloadbiddoc')}}/{{$value_bid['id']}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                    </div>
                                                  </div>
                                                  <div class="col-md-2" style="margin-top: 29px;">
                                                    <div class="form-group">
                                                      <button type="button" class="btn btn-danger remove_pre_bid" id="{{$value_bid['id']}}"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                    </div>
                                                  </div>
                                              </div>
                                            </div> --}}
                                            {{-- @endforeach --}}
                                          @else
                                          <div class="pre_bid_meeting" id="pre_bid_meeting_0">
                                              <div class="row prebid_meeting_div">
                                                <h3 class="title">Pre-Bid Meeting Query Point Documents</h3>
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
                                            </div>
                                          </div>
                                          @endif
                                        </div>
                                        <div class="row prebid_meeting_div" style="padding-top: 10px;">
                                                    <div class="col-sm-6"></div> 
                                                    <div class="col-sm-3"></div>  
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-primary" onclick="addPreBid()"><i class="fa fa-plus"></i> Add More</button>
                                                        </div>
                                                    </div>
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
                                    @if($tender_tender_corrigendum_count)
                                      <button type="button" class="btn btn-success btn-circle" id="tender_corr_info"><i class="fa fa-check" id="tender_corr_info1"></i> </button>
                                    @else
                                      <button type="button" class="btn btn-danger btn-circle" id="tender_corr_info"><i class="fa fa-times" id="tender_corr_info1"></i> </button>
                                    @endif
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
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> OTHER COMMUNICATION
                                    @if(count($tender_other_communication))
                                        <button type="button" class="btn btn-success btn-circle" id="tender_comm_info"><i class="fa fa-check" id="tender_comm_info1"></i> </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_comm_info"><i class="fa fa-times" id="tender_comm_info1"></i> </button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a>  </div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form action="#" id="tender_other_comm_form" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div id="other_communication_add_more">
                                          @if(count($tender_other_communication))
                                            {{-- @foreach($tender_other_communication as $key_com => $value_com) --}}
                                              {{-- <div class="other_communication" id="other_communication_{{$key_com}}">
                                                <div class="row">
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Other Communication Title</label>
                                                        <input type="text" class="form-control other_communication_title" name="other_communication_title[{{$key_com}}]" id="other_communication_title_{{$key_com}}" value="{{$value_com['other_communication_title']}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Other Communication Date</label>
                                                        <input type="text" class="form-control other_communication_date" name="other_communication_date[{{$key_com}}]" id="other_communication_date_{{$key_com}}" value="{{date('d-m-Y',strtotime($value_com['other_communication_date']))}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Document Attechement</label>
                                                        <input type="file" class="form-control communication_document_attechement" name="communication_document_attechement[{{$key_com}}]" id="communication_document_attechement_{{$key_com}}" data-do_id="{{$value_com['id']}}">

                                                        <input type="hidden" name="communication_document_attechement_hidden[{{$key_com}}]" id="communication_document_attechement_hidden_{{$key_com}}" value="{{$value_com['communication_document_attechement']}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-1" style="margin-top: 29px;">
                                                    <div class="form-group">
                                                      <a href="{{url('downloadcommdoc')}}/{{$value_com['id']}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                    </div>
                                                  </div>
                                                  <div class="col-md-2" style="margin-top: 29px;">
                                                    <div class="form-group">
                                                      <button type="button" class="btn btn-danger remove_communication" id="{{$value_com['id']}}"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div> --}}
                                            {{-- @endforeach --}}
                                          @else
                                            <div class="other_communication" id="other_communication_0">
                                              <div class="row">
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                      <label class="control-label">Other Communication Title</label>
                                                      <input type="text" class="form-control other_communication_title" name="other_communication_title[]" id="other_communication_title_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                      <label class="control-label">Other Communication Date</label>
                                                      <input type="text" class="form-control other_communication_date" name="other_communication_date[]" id="other_communication_date_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                      <label class="control-label">Document Attechement</label>
                                                      <input type="file" class="form-control communication_document_attechement" name="communication_document_attechement[]" id="communication_document_attechement_0">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          @endif
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-sm-6"></div> 
                                            <div class="col-sm-3"></div>  
                                            <div class="col-sm-3">
                                                  <div class="form-group">
                                                      <button type="button" class="btn btn-primary add_more" onclick="addMoreOtherComm()"><i class="fa fa-plus"></i> Add More</button>
                                                  </div>
                                              </div>
                                        </div>
                                        <button type="button" class="btn btn-success tender_other_comm_form_btn">Save</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> SPACIAL CONDITION OF CONTRACT
                                    @if(count($tender_condition_contract))
                                        <button type="button" class="btn btn-success btn-circle" id="tender_condition_info"><i class="fa fa-check" id="tender_condition_info1"></i> </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_condition_info"><i class="fa fa-times" id="tender_condition_info1"></i> </button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a>  </div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form action="#" id="tender_condition_form" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div id="tender_condition_add_more">
                                          @if(count($tender_condition_contract))
                                              {{-- @foreach($tender_condition_contract as $key_con => $value_con) --}}
                                                {{-- <div class="tender_condition" id="tender_condition_{{$key_con}}">
                                                  <div class="row">
                                                    <div class="col-md-6">
                                                      <div class="form-group">
                                                          <label class="control-label">Special Condition Title</label>
                                                          <input type="text" class="form-control condition_title" name="condition_title[{{$key_con}}]" id="condition_title_{{$key_con}}" value="{{$value_con['condition_title']}}">
                                                      </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                      <div class="form-group">
                                                          <label class="control-label">Document Attechement</label>
                                                          <input type="file" class="form-control condition_document_attechement" name="condition_document_attechement[{{$key_con}}]" id="condition_document_attechement_{{$key_con}}" data-do_id="{{$value_con['id']}}">
                                                          <input type="hidden" name="condition_document_attechement_hidden[{{$key_con}}]" id="condition_document_attechement_hidden_{{$key_con}}" value="{{$value_con['condition_document_attechement']}}">
                                                      </div>
                                                    </div>
                                                    <div class="col-md-1" style="margin-top: 29px;">
                                                      <div class="form-group">
                                                        <a href="{{url('downloadcondoc')}}/{{$value_con['id']}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                      </div>
                                                    </div>
                                                    <div class="col-md-2" style="margin-top: 29px;">
                                                      <div class="form-group">
                                                        <button type="button" class="btn btn-danger remove_condition" id="{{$value_con['id']}}"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div> --}}
                                              {{-- @endforeach --}}
                                          @else
                                            <div class="tender_condition" id="tender_condition_0">
                                              <div class="row">
                                                <div class="col-md-6">
                                                  <div class="form-group">
                                                      <label class="control-label">Special Condition Title</label>
                                                      <input type="text" class="form-control condition_title" name="condition_title[]" id="condition_title_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                      <label class="control-label">Document Attechement</label>
                                                      <input type="file" class="form-control condition_document_attechement" name="condition_document_attechement[]" id="condition_document_attechement_0">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          @endif
                                          
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-sm-6"></div> 
                                            <div class="col-sm-3"></div>  
                                            <div class="col-sm-3">
                                                  <div class="form-group">
                                                      <button type="button" class="btn btn-primary add_more" onclick="addMoreCondition()"><i class="fa fa-plus"></i> Add More</button>
                                                  </div>
                                              </div>
                                        </div>
                                        <button type="button" class="btn btn-success tender_condition_form_btn">Save</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> MODE OF PHYSICAL SUBMISSION 
                                  @if($tender['physical_sub_mode'])
                                        <button type="button" class="btn btn-success btn-circle" id="tender_phy_sub_info"><i class="fa fa-check" id="tender_phy_sub_info1"></i> </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_phy_sub_info"><i class="fa fa-times" id="tender_phy_sub_info1"></i> </button>
                                    @endif 
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form action="#" id="tender_phy_sub_form" method="post" enctype="multipart/form-data">
                                      @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div class="row">
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <label class="control-label">Select Mode Of Physical Submission</label>
                                              <select class="form-control physical_sub_mode" name="physical_sub_mode" id="physical_sub_mode">
                                                <option value="">Select Mode</option>
                                                @foreach($tender_physical_submission as $key_ps => $value_ps)
                                                  <option value="{{$key_ps}}" {{$tender['physical_sub_mode'] == $key_ps ? "selected" : ""}}>{{$value_ps}}</option>
                                                @endforeach 
                                              </select>
                                            </div>
                                          </div>
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <label class="control-label">Due Date Of Physical Submission</label>
                                              <input type="text" class="form-control physical_sub_mode_due_date" name="physical_sub_mode_due_date" id="physical_sub_mode_due_date" value="{{$tender['physical_sub_mode_due_date'] != "" ? date('d-m-Y H:i a',strtotime($tender['physical_sub_mode_due_date'])) : ""}}">
                                            </div>
                                          </div>
                                        </div>
                                        <button type="button" class="btn btn-success tender_phy_sub_form_btn">Save</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            {{-- <button type="submit" class="btn btn-success">Submit</button> --}}
                            {{-- <button type="button" onclick="window.location.href ='{{ route('admin.tender') }}'" class="btn btn-danger">Cancel</button> --}}
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
var technical_counter = {{$tender_technical_eligibility_count}};
var technical_counter_validate = {{$tender_technical_eligibility_count}};
var financial_counter = {{$tender_financial_eligibility_count}};
var financial_counter_validate = {{$tender_financial_eligibility_count}};
var counter_authority = {{$tender_authority_contact_detail_count}};
var counter_pre_bid = {{$tender_bid_meet_count}};
var counter_pre_bid_validate = {{$tender_bid_meet_count}};
var counter_communication = {{$tender_other_communication_count}};
var counter_communication_validate = {{$tender_other_communication_count}};
var counter_condition = {{$tender_condition_contract_count}};
var counter_condition_validate = {{$tender_condition_contract_count}};
var counter_corro = {{$tender_tender_corrigendum_count}};
var counter_physical_sub = {{$tender['physical_sub_mode'] != "" ? 1 : 0  }};
var tender_fee_counter = {{$tender['tender_fee'] != "" ? 1 : 0  }};
var tender_emd_counter = {{$tender['tender_emd'] != "" ? 1 : 0  }};

var tender_id = "{{$tender_id}}";
$(document).ready(function() {
$('#tender_fee').trigger('change');
$('#tender_emd').trigger('change');
$('#joint_venture').trigger('change');
$('#quote_type').trigger('change');
$('#pre_bid_meeting').trigger('change');
$('#tender_fee_validity').trigger('change');
$('#tender_emd_validity').trigger('change');
getTechicalCriteria();
getFinancialCriteria();
getPreBidMeeting();
getOtherCommunication();
getConditionContract();
getPreBidQuery();
load_datepicker();
tenderSubmissionProcess();
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
});



// ------ tender fee start -------------
$('#tender_fee').on('change', function(){
    var check_val = $(this).val();
    if(check_val == "Yes"){
        $(".tender_fee_div").show();
    }else{
        $(".tender_fee_div").hide();
    }    
});
$('#tender_fee_validity').on('change', function(){
    var check_val = $(this).val();
    if(check_val == "On Date"){
        $(".tender_fee_validity_date_div").show();
    }else{
        $(".tender_fee_validity_date_div").hide();
    }    
});

//tender fee form
$(".tender_fee_form_btn").on('click',function(){
    if($("#tender_fee_form").valid()){
      $(".tender_fee_form_btn").attr("disabled", true);
      var fee_form = $("#tender_fee_form").serialize();
      $.ajax({
        type : "POST",
        url : "{{url('save_tender_fee')}}",
        data : fee_form,
        success : function(data){
          console.log(data);
          if(data == "success")
            tender_fee_counter = 1;

          $(".tender_fee_form_btn").attr("disabled", false);
            alertMassage(data,"tender_fee_info","tender_fee_info1");
        }
      })
    }
});

// ------ tender fee end -------------

// ------ tender emd start -------------
$('#tender_emd').on('change', function(){
    var check_val = $(this).val();
    if(check_val == "Yes"){
        $(".tender_emd_div").show();
    }else{
        $(".tender_emd_div").hide();
    }    
});
$('#tender_emd_validity').on('change', function(){
    var check_val = $(this).val();
    if(check_val == "On Date"){
        $(".tender_emd_validity_date_div").show();
    }else{
        $(".tender_emd_validity_date_div").hide();
    }    
});

//tender emd form
$(".tender_emd_form_btn").on('click',function(){
    if($("#tender_emd_form").valid()){
      $(".tender_emd_form_btn").attr("disabled", true);
      var fee_form = $("#tender_emd_form").serialize();
      $.ajax({
        type : "POST",
        url : "{{url('save_tender_emd')}}",
        data : fee_form,
        success : function(data){
          console.log(data);
          $(".tender_emd_form_btn").attr("disabled", false);
          
          if(data == "success")
            tender_emd_counter = 1;

          alertMassage(data,"tender_emd_info","tender_emd_info1");
        }
      })
    }
});

// ------ tender emd end -------------


//---------------------Techical Eligibility start ----------
//Remove
$("body").on("click",".remove_tech_eli",function(){
      $(this).parents(".technical_eligibility").remove();
});
//Delete
/*$("body").on("click",".remove_tech_eli_delete",function(){
  var remove_tech_eli_delete_id = $(this).attr('id');
      $(this).parents(".technical_eligibility").remove();
      $.ajax({
            type : "POST",
            url : "{{url('delete_technical_file')}}",
            data: {
              "_token": "{{ csrf_token() }}",
              "id": remove_tech_eli_delete_id
            },
            success : function(data){
              // console.log(data);
                // $(this).parents(".financial_eligibility").remove();
            }
        });
});*/

//Add more
function addMoreTechEli(){
  technical_counter += 1;

  $('#technical_eligibility_add_more').append('<div class="technical_eligibility" id="technical_eligibility_'+technical_counter+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-6">'+
                                                '<div class="form-group">'+
                                                    '<label class="control-label">Document Name</label>'+
                                                    '<input type="text" class="form-control technical_eligibility_document_name" name="technical_eligibility_document_name['+technical_counter+']" id="technical_eligibility_document_name_'+technical_counter+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                    '<label class="control-label">Document Attechement</label>'+
                                                    '<input type="file" class="form-control technical_eligibility_document_attechement" name="technical_eligibility_document_attechement['+technical_counter+']" id="technical_eligibility_document_attechement_'+technical_counter+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="margin-top: 29px;">'+
                                                '<div class="form-group">'+
                                                  '<button type="button" class="btn btn-danger remove_tech_eli"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                '</div>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');
}

//Technical eligibility add edit validation
$(".tender_tech_eli_form_btn").on('click',function(){
      $('.technical_eligibility_document_name').each(function() {
          $(this).rules('add', {
              required: true,
          });
      });
      $('.technical_eligibility_document_attechement').each(function() {
          $(this).rules('add', {
              required: true,
          });
      });

      for(j = 0 ; j < technical_counter_validate ; j++){
        $("#technical_eligibility_document_attechement_"+j).removeClass("error").rules("remove");
      }

      if($("#tender_tech_eli_form").valid()){
        var form = $('#tender_tech_eli_form')[0];
        var formData = new FormData(form);
          
        if(technical_counter == 0){
            formData.append('technical_eligibility_document_attechement[]', $('.technical_eligibility_document_attechement')[0].files[0]);
        }else{
          for(i = 0; i >= technical_counter ; i++){
              formData.append('technical_eligibility_document_attechement', $('.technical_eligibility_document_attechement')[1].files[1]);  
          }  
        }
      
       $(".tender_tech_eli_form_btn").attr("disabled", true); 
        $.ajax({
          type : "POST",
          url : "{{url('tender_tech_eli_sub')}}",
          data : formData,
          processData: false,
          contentType: false,
          success : function(data){
            console.log(data);
            $(".tender_tech_eli_form_btn").attr("disabled", false);
            getTechicalCriteria();
            alertMassage(data,"tender_tech_info","tender_tech_info1");
          }
        });
      }
});
$("#tender_tech_eli_form").validate();

// Change file
/*$(".technical_eligibility_document_attechement").on('change',function(){
  var te_file_do_id = $(this).attr('data-do_id');
  var te_file_id = $(this).attr('id');
  if(te_file_do_id){
    files = event.target.files;
    var fd = new FormData();
    fd.append('file_img', $('#'+te_file_id)[0].files[0]);
    fd.append('id',te_file_do_id);
    // console.log(fd);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type : "POST",
        url : "{{url('change_technical_file')}}",
        data : fd,
        processData: false,
        contentType: false,
        success : function(data){
          console.log(data);
          swal({
              title: "Tender document updated successfully.",
              //text: "You want to change status of admin user.",
              // type: "info",
              showCancelButton: false,
              confirmButtonColor: "#006600",
              confirmButtonText: "Okay",
              closeOnConfirm: true
          });
        }
    });
  }
});*/

//---------------------Techical Eligibility End ----------

//----------- Financial Eligibility Start -------------
//remove
$("body").on("click",".remove_fina_eli",function(){
      $(this).parents(".financial_eligibility").remove();
  });
//delete
/*$("body").on("click",".remove_fina_eli_delete",function(){
        var remove_fina_eli_delete_id = $(this).attr('id');        
        $(this).parents(".financial_eligibility").remove();
        $.ajax({
            type : "POST",
            url : "{{url('delete_financial_file')}}",
            data: {
              "_token": "{{ csrf_token() }}",
              "id": remove_fina_eli_delete_id
            },
            success : function(data){
              // console.log(data);
                // $(this).parents(".financial_eligibility").remove();
            }
        });
  });*/
//add more
function addMoreFinaEli(){
 financial_counter += 1;

$("#financial_eligibility_add_more").append('<div class="financial_eligibility" id="financial_eligibility_'+financial_counter+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-6">'+
                                                '<div class="form-group">'+
                                                    '<label class="control-label">Document Name</label>'+
                                                    '<input type="text" class="form-control financial_eligibility_document_name" name="financial_eligibility_document_name['+financial_counter+']" id="financial_eligibility_document_name_'+financial_counter+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                    '<label class="control-label">Document Attechement</label>'+
                                                    '<input type="file" class="form-control financial_eligibility_document_attechement" name="financial_eligibility_document_attechement['+financial_counter+']" id="financial_eligibility_document_attechement_'+financial_counter+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="margin-top: 29px;">'+
                                                '<div class="form-group">'+
                                                  '<button type="button" class="btn btn-danger remove_fina_eli"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                '</div>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');

}

//financial eligibility add edit validation
$(".tender_fina_eli_form_btn").on('click',function(){
      $('.financial_eligibility_document_name').each(function() {
          $(this).rules('add', {
              required: true,
          });
      });
      $('.financial_eligibility_document_attechement').each(function() {
          $(this).rules('add', {
              required: true,
          });
      });

      for(j = 0 ; j < financial_counter_validate ; j++){
        $("#financial_eligibility_document_attechement_"+j).removeClass("error").rules("remove");
      }

      if($("#tender_fina_eli_form").valid()){
          var form = $('#tender_fina_eli_form')[0];
          var formData1 = new FormData(form);
            
          if(financial_counter == 0){
              formData1.append('financial_eligibility_document_attechement[]', $('.financial_eligibility_document_attechement')[0].files[0]);
          }else{
            for(i = 0; i >= financial_counter ; i++){
                formData1.append('financial_eligibility_document_attechement', $('.financial_eligibility_document_attechement')[1].files[1]);  
            }  
          }
        
          $(".tender_fina_eli_form_btn").attr("disabled", true);  
          $.ajax({
            type : "POST",
            url : "{{url('tender_fina_eli_sub')}}",
            data : formData1,
            processData: false,
            contentType: false,
            success : function(data){
              console.log(data);
              $(".tender_fina_eli_form_btn").attr("disabled", false);
              getFinancialCriteria();
              alertMassage(data,"tender_fina_info","tender_fina_info1");
            }
          });
      }
});
$("#tender_fina_eli_form").validate();

//financial_eligibility_document_attechement
/*$(".financial_eligibility_document_attechement").on('change',function(){
  var te_file_do_id = $(this).attr('data-do_id');
  var te_file_id = $(this).attr('id');
  if(te_file_do_id){
    files = event.target.files;
    var fd = new FormData();
    fd.append('file_img', $('#'+te_file_id)[0].files[0]);
    fd.append('id',te_file_do_id);
    // console.log(fd);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type : "POST",
        url : "{{url('change_financial_file')}}",
        data : fd,
        processData: false,
        contentType: false,
        success : function(data){
          console.log(data);
          swal({
              title: "Tender document updated successfully.",
              //text: "You want to change status of admin user.",
              // type: "info",
              showCancelButton: false,
              confirmButtonColor: "#006600",
              confirmButtonText: "Okay",
              closeOnConfirm: true
          });
        }
    });
  }
});*/

//----------- Financial Eligibility End -------------


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
              alertMassage(data,"tender_detail_info","tender_detail_info1");
          }
        })
    }    
});
$("#tender_form").validate({
  ignore: [],
});

// -------- Tender detail end ---------

// ----------------- Pre bid start ------------------
// hide show
$('#pre_bid_meeting').on('change', function(){
    var check_val = $(this).val();
    if(check_val == "Yes"){
        $(".prebid_meeting_div").show();
    }else{
        $(".prebid_meeting_div").hide();
    }    
});

//add edit validation
$(".tender_pre_bid_meet_form_btn").on('click',function(){

  $('.pre_bid_meeting').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  $('.pre_bid_meeting_datetime').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  $('.pre_bid_meeting_venue').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
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
            url : "{{url('tender_pre_bid_meet')}}",
            data : formData1,
            processData: false,
            contentType: false,
            success : function(data){
              // console.log(data);
              $(".tender_pre_bid_meet_form_btn").attr("disabled", false);
              getPreBidQuery();
              alertMassage(data,"tender_bid_info","tender_bid_info1");
              getPreBidMeeting();
            }
          });
    }
});
$("#tender_pre_bid_meet_form").validate();

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
//pre bid add more
function addPreBid(){
  counter_pre_bid += 1;
  $("#pre_bid_meeting_add_more").append('<div class="pre_bid_meeting" id="pre_bid_meeting_'+counter_pre_bid+'">'+
                                              '<div class="row pre_bid_meeting_query_point_div">'+
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
                                          '</div>');
}

// change pre bid doc
/*$(".query_point_document_attechment").on('change',function(){
  var bid_file_do_id = $(this).attr('data-do_id');
  var bid_file_id = $(this).attr('id');
  if(bid_file_do_id){
    files = event.target.files;
    var fd = new FormData();
    fd.append('file_img', $('#'+bid_file_id)[0].files[0]);
    fd.append('id',bid_file_do_id);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type : "POST",
        url : "{{url('change_bid_document_file')}}",
        data : fd,
        processData: false,
        contentType: false,
        success : function(data){
          console.log(data);
          swal({
              title: "Tender document updated successfully.",
              //text: "You want to change status of admin user.",
              // type: "info",
              showCancelButton: false,
              confirmButtonColor: "#006600",
              confirmButtonText: "Okay",
              closeOnConfirm: true
          });
        }
    });
  }
});*/
// ----------- Pre end bid --------------------------

// ------------------ Other Communication start --------------------
//pre Communication remove
$("body").on("click",".remove_communication",function(){
        $(this).parents(".other_communication").remove();
  });
//delete
/*$("body").on("click",".remove_communication_delete",function(){
       var remove_com_delete_id = $(this).attr('id');
        $(this).parents(".other_communication").remove();
      $.ajax({
          type : "POST",
          url : "{{url('delete_communication_document')}}",
          data: {
            "_token": "{{ csrf_token() }}",
            "id": remove_com_delete_id
          },
          success : function(data){
            // console.log(data);
              // $(this).parents(".financial_eligibility").remove();
          }
      });
  });*/
function addMoreOtherComm(){
  counter_communication += 1;
  $("#other_communication_add_more").append('<div class="other_communication" id="other_communication_'+counter_communication+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                    '<label class="control-label">Other Communication Title</label>'+
                                                    '<input type="text" class="form-control other_communication_title" name="other_communication_title['+counter_communication+']" id="other_communication_title_'+counter_communication+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                    '<label class="control-label">Other Communication Date</label>'+
                                                    '<input type="text" class="form-control other_communication_date" name="other_communication_date['+counter_communication+']" id="other_communication_date_'+counter_communication+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                    '<label class="control-label">Document Attechement</label>'+
                                                    '<input type="file" class="form-control communication_document_attechement" name="communication_document_attechement['+counter_communication+']" id="communication_document_attechement_'+counter_communication+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="margin-top: 29px;">'+
                                                  '<div class="form-group">'+
                                                    '<button type="button" class="btn btn-danger remove_communication"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                  '</div>'+
                                                '</div>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');

  jQuery('.other_communication_date').datetimepicker({
            // format:'DD-MM-YYYY h:mm a',
            format:'DD-MM-YYYY',
            minDate : new Date(),
      });
}

$(".tender_other_comm_form_btn").on('click',function(){
  $('.other_communication_title').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  $('.other_communication_date').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  $('.communication_document_attechement').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });

  for(j = 0 ; j < counter_communication_validate ; j++){
        $("#communication_document_attechement_"+j).removeClass("error").rules("remove");
      }

  if($("#tender_other_comm_form").valid()){
    $(".tender_other_comm_form_btn").attr("disabled", true);
    var form = $('#tender_other_comm_form')[0];
    var formData1 = new FormData(form);
          
    if(counter_communication == 0){
        formData1.append('communication_document_attechement[]', $('.communication_document_attechement')[0].files[0]);
    }else{
      for(i = 0; i >= counter_communication ; i++){
          formData1.append('communication_document_attechement', $('.communication_document_attechement')[1].files[1]);  
      }
    }  
  
    $.ajax({
      type : "POST",
      url : "{{url('tender_other_communication')}}",
      data : formData1,
      processData: false,
      contentType: false,
      success : function(data){
        // console.log(data);
        $(".tender_other_comm_form_btn").attr("disabled", false);
        alertMassage(data,"tender_comm_info","tender_comm_info1");
        getOtherCommunication();
      }
    });
  }
});
$("#tender_other_comm_form").validate();

//communication_document_attechement change
/*$(".communication_document_attechement").on('change',function(){
  var comm_file_do_id = $(this).attr('data-do_id');
  var comm_file_id = $(this).attr('id');
  if(comm_file_do_id){
    files = event.target.files;
    var fd = new FormData();
    fd.append('file_img', $('#'+comm_file_id)[0].files[0]);
    fd.append('id',comm_file_do_id);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type : "POST",
        url : "{{url('change_communication_document_file')}}",
        data : fd,
        processData: false,
        contentType: false,
        success : function(data){
          // console.log(data);
          swal({
              title: "Tender document updated successfully.",
              //text: "You want to change status of admin user.",
              // type: "info",
              showCancelButton: false,
              confirmButtonColor: "#006600",
              confirmButtonText: "Okay",
              closeOnConfirm: true
          });
        }
    });
  }
});*/

// ------------------ Other Communication end --------------------


// ---------------- Spacial condition contract start ------------
//remove
$("body").on("click",".remove_condition",function(){
        $(this).parents(".tender_condition").remove();
  });
//delete
/*$("body").on("click",".remove_condition_delete",function(){
        var remove_con_delete_id = $(this).attr('id');
        $(this).parents(".tender_condition").remove();
        $.ajax({
          type : "POST",
          url : "{{url('delete_condition_document')}}",
          data: {
            "_token": "{{ csrf_token() }}",
            "id": remove_con_delete_id
          },
          success : function(data){
            // console.log(data);
              // $(this).parents(".financial_eligibility").remove();
          }
      });
  });*/
function addMoreCondition(){
  counter_condition += 1;
  $("#tender_condition_add_more").append('<div class="tender_condition" id="tender_condition_'+counter_condition+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-6">'+
                                                '<div class="form-group">'+
                                                    '<label class="control-label">Special Condition Title</label>'+
                                                    '<input type="text" class="form-control condition_title" name="condition_title['+counter_condition+']" id="condition_title_'+counter_condition+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                    '<label class="control-label">Document Attechement</label>'+
                                                    '<input type="file" class="form-control condition_document_attechement" name="condition_document_attechement['+counter_condition+']" id="condition_document_attechement_'+counter_condition+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="margin-top: 29px;">'+
                                                  '<div class="form-group">'+
                                                    '<button type="button" class="btn btn-danger remove_condition"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                  '</div>'+
                                                '</div>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');
}

//add edit validation
$(".tender_condition_form_btn").on('click',function(){
  $('.condition_title').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  $('.condition_document_attechement').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });

  for(j = 0 ; j < counter_condition_validate ; j++){
        $("#condition_document_attechement_"+j).removeClass("error").rules("remove");
      }

  if($("#tender_condition_form").valid()){
    $(".tender_condition_form_btn").attr("disabled", true);
    var form = $('#tender_condition_form')[0];
    var formData1 = new FormData(form);
    
    if(counter_condition == 0){
        formData1.append('condition_document_attechement[]', $('.condition_document_attechement')[0].files[0]);
    }else{      
      for(i = 0; i >= counter_condition ; i++){
          formData1.append('condition_document_attechement', $('.condition_document_attechement')[1].files[1]);  
      }
    }  
  
    $.ajax({
      type : "POST",
      url : "{{url('tender_condition_contract')}}",
      data : formData1,
      processData: false,
      contentType: false,
      success : function(data){
        // console.log(data);
        $(".tender_condition_form_btn").attr("disabled", false);
        alertMassage(data,"tender_condition_info","tender_condition_info1");
        getConditionContract();
      }
    });
  }
});
$("#tender_condition_form").validate();

// change condition doc
/*$(".condition_document_attechement").on('change',function(){
  var condition_file_do_id = $(this).attr('data-do_id');
  var condition_file_id = $(this).attr('id');
  if(condition_file_do_id){
    files = event.target.files;
    var fd = new FormData();
    fd.append('file_img', $('#'+condition_file_id)[0].files[0]);
    fd.append('id',condition_file_do_id);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type : "POST",
        url : "{{url('change_condition_file')}}",
        data : fd,
        processData: false,
        contentType: false,
        success : function(data){
          console.log(data);
          swal({
              title: "Tender document updated successfully.",
              //text: "You want to change status of admin user.",
              // type: "info",
              showCancelButton: false,
              confirmButtonColor: "#006600",
              confirmButtonText: "Okay",
              closeOnConfirm: true
          });
        }
    });
  }
});*/

// ---------------- Spacial condition contract end ------------ 


// -------------------- Physical Submission star -------------------

$(".tender_phy_sub_form_btn").on('click',function(){
  $('.physical_sub_mode').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });

  $('.physical_sub_mode_due_date').each(function() {
      $(this).rules('add', {
          required: true,
      });
  });
  if($("#tender_phy_sub_form").valid()){
    $(".tender_phy_sub_form_btn").attr("disabled", true);
    var phy_form = $("#tender_phy_sub_form").serialize();
      $.ajax({
        type : "POST",
        url : "{{url('save_tender_physical_sub')}}",
        data : phy_form,
        success : function(data){
          console.log(data);
          $(".tender_phy_sub_form_btn").attr("disabled", false);
          if(data == "success")
              counter_physical_sub = 1;
            alertMassage(data,"tender_phy_sub_info","tender_phy_sub_info1");
        }
      })
  }  
});
$("#tender_phy_sub_form").validate();

// -------------------- Physical Submission end -------------------


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
        if(data == "success"){
          counter_corro = 1;
          $("#tender_corrigendum_form")[0].reset();
            swal(success, "", "success");
            $("#tender_corr_info").removeClass('btn btn-danger btn-circle');
            $("#tender_corr_info1").removeClass('fa fa-times');
            $("#tender_corr_info").addClass('btn btn-success btn-circle');
            $("#tender_corr_info1").addClass('fa fa-check');
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
    });
}
});

jQuery('.corrigendum_date').datetimepicker({
            // format:'DD-MM-YYYY h:mm a',
            format:'DD-MM-YYYY',
            minDate : new Date(),
      });

//---------- Corrigendum pre_bid_meeting_query end ------------------


function load_datepicker(){
    jQuery('.tender_fee_validity_date,.tender_emd_validity_date,.other_communication_date').datetimepicker({
            // format:'DD-MM-YYYY h:mm a',
            format:'DD-MM-YYYY',
            minDate : new Date(),
      });
    jQuery('.last_date_time_download,.last_date_time_online_submit,.last_date_time_physical_submit,.pre_bid_meeting_datetime,.physical_sub_mode_due_date').datetimepicker({
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

function alertMassage(data,param1,param2){
  if(data == "success"){
        changeInfoIcon(param1,param2);
        tenderSubmissionProcess();
        swal("Tender detail save successfully.", "", "success");
    }else{
        swal({
            title: "Tender not save try again.",
            //text: "You want to change status of admin user.",
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK",
            closeOnConfirm: true
        });
    }
}

function changeInfoIcon(param1,param2){
  $("#"+param1).removeClass('btn btn-danger btn-circle');
  $("#"+param2).removeClass('fa fa-times');
  $("#"+param1).addClass('btn btn-success btn-circle');
  $("#"+param2).addClass('fa fa-check');

  // $("#"+param1).removeClass('btn btn-success btn-circle');
  // $("#"+param2).removeClass('fa fa-check');
  // $("#"+param1).addClass('btn btn-danger btn-circle');
  // $("#"+param2).addClass('fa fa-times');
}

function tenderSubmissionProcess(){
  var status = 0;
  if(tender_fee_counter > 0 &&
     tender_emd_counter > 0 && 
     technical_counter > 0 && 
     financial_counter > 0  && 
     // counter_pre_bid > 0 && 
     // counter_corro > 0 && 
     // counter_communication > 0 && 
     // counter_condition > 0 && 
     counter_physical_sub > 0){
    
    status = 1;
  }else{
    status = 0;
  }

  $.ajax({
      type : "POST",
      url : "{{url('tender_submission_process')}}",
      data : {
        "id" : tender_id,
        "status" : status,
        "_token" : "{{csrf_token()}}"
      },
      success : function(data){
        console.log(data);
      }
    });

}

//Get Technical Eligibility Criteria
function getTechicalCriteria(){
  $.ajax({
      type : "POST",
      url : "{{url('get_techical_criteria')}}",
      data : {
        "id" : tender_id,
        "_token" : "{{csrf_token()}}"
      },
      success : function(data){
        // console.log(data);
        data = JSON.parse(data);
        if(data.length){
          $("#technical_eligibility_add_more").html('');
          technical_counter = data.length - 1;
          technical_counter_validate = data.length;
          $.each( data, function(key, value){
          $("#technical_eligibility_add_more").append('<div class="technical_eligibility" id="technical_eligibility_'+key+'">'+
                                                '<div class="row">'+
                                                  '<div class="col-md-6">'+
                                                    '<div class="form-group">'+
                                                        '<label class="control-label">Document Name</label>'+
                                                        '<input type="text" class="form-control technical_eligibility_document_name" name="technical_eligibility_document_name['+key+']" id="technical_eligibility_document_name_'+key+'" value="'+value.document_name+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                        '<label class="control-label">Document Attechement</label>'+
                                                        '<input type="file" class="form-control technical_eligibility_document_attechement" name="technical_eligibility_document_attechement['+key+']" id="technical_eligibility_document_attechement_'+key+'" data-do_id="'+value.id+'" >'+
                                                        '<input type="hidden" name="technical_eligibility_document_attechement_hidden['+key+']" id="technical_eligibility_document_attechement_hidden_'+key+'" value="'+value.document_attechement+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-1" style="margin-top: 29px;">'+
                                                    '<div class="form-group">'+
                                                      '<a href="{{url('downloadtechdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
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

//Get Financial Eligibility Criteria
function getFinancialCriteria(){
  $.ajax({
      type : "POST",
      url : "{{url('get_financial_criteria')}}",
      data : {
        "id" : tender_id,
        "_token" : "{{csrf_token()}}"
      },
      success : function(data){
        // console.log(data);
        data = JSON.parse(data);
        if(data.length){
          $("#financial_eligibility_add_more").html('');
          financial_counter = data.length - 1;
          financial_counter_validate = data.length;
          $.each( data, function(key, value){
          $("#financial_eligibility_add_more").append('<div class="financial_eligibility" id="financial_eligibility_'+key+'">'+
                                                '<div class="row">'+
                                                  '<div class="col-md-6">'+
                                                    '<div class="form-group">'+
                                                        '<label class="control-label">Document Name</label>'+
                                                        '<input type="text" class="form-control financial_eligibility_document_name" name="financial_eligibility_document_name['+key+']" id="financial_eligibility_document_name_'+key+'" value="'+value.document_name+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                        '<label class="control-label">Document Attechement</label>'+
                                                        '<input type="file" class="form-control financial_eligibility_document_attechement" name="financial_eligibility_document_attechement['+key+']" id="financial_eligibility_document_attechement_'+key+'" data-do_id="'+value.id+'" >'+
                                                        '<input type="hidden" name="financial_eligibility_document_attechement_hidden['+key+']" id="financial_eligibility_document_attechement_hidden_'+key+'" value="'+value.document_attechement+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-1" style="margin-top: 29px;">'+
                                                    '<div class="form-group">'+
                                                      '<a href="{{url('downloadfinadoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
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

//Get Pre Bid Meeting
function getPreBidMeeting(){
$.ajax({
      type : "POST",
      url : "{{url('get_pre_bid_meeting')}}",
      data : {
        "id" : tender_id,
        "_token" : "{{csrf_token()}}"
      },
      success : function(data){
        // console.log(data);
        data = JSON.parse(data);
        if(data.length){

          $("#pre_bid_meeting_add_more").html('');
          counter_pre_bid = data.length - 1;
          counter_pre_bid_validate = data.length;
          $.each( data, function(key, value){
          if(key == 0){
            display_title = "";
          }else{
            display_title = "display:none;";
          }
          $("#pre_bid_meeting_add_more").append('<div class="pre_bid_meeting" id="pre_bid_meeting_'+key+'">'+
                                                '<div class="row prebid_meeting_div">'+
                                                  '<h3 class="title" style="'+display_title+'">Pre-Bid Meeting Query Point Documents</h3>'+
                                                  '<div class="col-md-6">'+
                                                      '<div class="form-group">'+
                                                          '<label class="control-label">Query Point Document </label>'+
                                                          '<input type="text" class="form-control query_point_document_name" name="query_point_document_name['+key+']" id="query_point_document_name_'+key+'" value="'+value.query_point_document_name+'">'+
                                                      '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-3">'+
                                                      '<div class="form-group">'+
                                                          '<label class="control-label">Document Attachment</label>'+
                                                          '<input type="file" class="form-control query_point_document_attechment" name="query_point_document_attechment['+key+']" id="query_point_document_attechment_'+key+'" data-do_id="'+value.id+'">'+
                                                          '<input type="hidden" name="query_point_document_attechment_hidden['+key+']" id="query_point_document_attechment_hidden_'+key+'" value="'+value.query_point_document_attechment+'">'+
                                                      '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-1" style="margin-top: 29px;">'+
                                                    '<div class="form-group">'+
                                                      '<a href="{{url('downloadbiddoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
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

//Get Other Communication
function getOtherCommunication(){
$.ajax({
      type : "POST",
      url : "{{url('get_other_communication')}}",
      data : {
        "id" : tender_id,
        "_token" : "{{csrf_token()}}"
      },
      success : function(data){
        // console.log(data);
        data = JSON.parse(data);
        if(data.length){

          $("#other_communication_add_more").html('');
          counter_communication = data.length - 1;
          counter_communication_validate = data.length;
          $.each( data, function(key, value){


          $("#other_communication_add_more").append('<div class="other_communication" id="other_communication_'+key+'">'+
                                                '<div class="row">'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                        '<label class="control-label">Other Communication Title</label>'+
                                                        '<input type="text" class="form-control other_communication_title" name="other_communication_title['+key+']" id="other_communication_title_'+key+'" value="'+value.other_communication_title+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                        '<label class="control-label">Other Communication Date</label>'+
                                                        '<input type="text" class="form-control other_communication_date" name="other_communication_date['+key+']" id="other_communication_date_'+key+'" value="'+value.other_communication_date+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                        '<label class="control-label">Document Attechement</label>'+
                                                        '<input type="file" class="form-control communication_document_attechement" name="communication_document_attechement['+key+']" id="communication_document_attechement_'+key+'" data-do_id="'+value.id+'">'+

                                                        '<input type="hidden" name="communication_document_attechement_hidden['+key+']" id="communication_document_attechement_hidden_'+key+'" value="'+value.communication_document_attechement+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-1" style="margin-top: 29px;">'+
                                                    '<div class="form-group">'+
                                                      '<a href="{{url('downloadcommdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
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

// Get Condition And Contract
function getConditionContract(){
$.ajax({
      type : "POST",
      url : "{{url('get_condition_contract')}}",
      data : {
        "id" : tender_id,
        "_token" : "{{csrf_token()}}"
      },
      success : function(data){
        // console.log(data);
        data = JSON.parse(data);
        if(data.length){

          $("#tender_condition_add_more").html('');
          counter_condition = data.length - 1;
          counter_condition_validate = data.length;
          $.each( data, function(key, value){


          $("#tender_condition_add_more").append('<div class="tender_condition" id="tender_condition_'+key+'">'+
                                                  '<div class="row">'+
                                                    '<div class="col-md-6">'+
                                                      '<div class="form-group">'+
                                                          '<label class="control-label">Special Condition Title</label>'+
                                                          '<input type="text" class="form-control condition_title" name="condition_title['+key+']" id="condition_title_'+key+'" value="'+value.condition_title+'">'+
                                                      '</div>'+
                                                    '</div>'+
                                                    '<div class="col-md-3">'+
                                                      '<div class="form-group">'+
                                                          '<label class="control-label">Document Attechement</label>'+
                                                          '<input type="file" class="form-control condition_document_attechement" name="condition_document_attechement['+key+']" id="condition_document_attechement_'+key+'" data-do_id="'+value.id+'">'+
                                                          '<input type="hidden" name="condition_document_attechement_hidden['+key+']" id="condition_document_attechement_hidden_'+key+'" value="'+value.condition_document_attechement+'">'+
                                                      '</div>'+
                                                    '</div>'+
                                                    '<div class="col-md-1" style="margin-top: 29px;">'+
                                                      '<div class="form-group">'+
                                                        '<a href="{{url('downloadcondoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
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
