@extends('layouts.admin_app')

@section('content')
<style type="text/css">
    .tender_div{
        border: 1px solid #a5a0a0;
        padding: 13px;
        margin-bottom: 5px;
    }
    .bidderlist{
      display: block;
      position: absolute;
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
                            <div class="col-md-2">
                                <button type="button" class="btn btn-warning btn-circle"></button>
                                <b>Pending</b>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-circle"></button>
                                <b>Reject</b>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success btn-circle"></button>
                                <b>Eligible</b>
                            </div>
                            <br><br>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Opening Status of Preliminary Part
                                    @if($tender['tender_opening_fee_status'] == "Eligible")
                                      <button type="button" class="btn btn-success btn-circle" id="opening_status_info"></button>
                                    @elseif($tender['tender_opening_fee_status'] == "Reject")
                                      <button type="button" class="btn btn-danger btn-circle" id="opening_status_info"></button>
                                    @else
                                      <button type="button" class="btn btn-warning btn-circle" id="opening_status_info"></button>
                                    @endif 
                                    

                                    @if($tender['tender_opening_emd_status'] == "Eligible")
                                      <button type="button" class="btn btn-success btn-circle" id="opening_status_info1"></button>
                                    @elseif($tender['tender_opening_emd_status'] == "Reject")
                                      <button type="button" class="btn btn-danger btn-circle" id="opening_status_info1"></button>
                                    @else
                                      <button type="button" class="btn btn-warning btn-circle" id="opening_status_info1"></button>
                                    @endif

                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form method="post" action="#" id="opening_status_fee_form" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <input type="hidden" name="form_name" value="fee">
                                      <h3>Tender Fee</h3>
                                      <div class="row">
                                        <div class="col-md-4">
                                          <div class="form-group">
                                            <label class="control-label">Tender Fee Status</label>
                                            <select class="form-control tender_opening_fee_status required" name="tender_opening_fee_status" id="tender_opening_fee_status">
                                              <option value="">Select</option>
                                              <option value="Eligible" {{$tender['tender_opening_fee_status'] == "Eligible" ? "selected" : ""}}>Eligible</option>
                                              <option value="Reject" {{$tender['tender_opening_fee_status'] == "Reject" ? "selected" : ""}}>Reject</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="col-md-3 tender_fee_reject_div">
                                          <div class="form-group">
                                            <label class="control-label">Tender Fee Reject Reason</label>
                                            {{-- <input type="text" class="form-control tender_opening_fee_reject_reason required" name="tender_opening_fee_reject_reason" id="tender_opening_fee_reject_reason" value="{{$tender['tender_opening_fee_reject_reason']}}"> --}}
                                            <textarea class="form-control tender_opening_fee_reject_reason required" name="tender_opening_fee_reject_reason" id="tender_opening_fee_reject_reason">{{$tender['tender_opening_fee_reject_reason']}}</textarea>
                                          </div>
                                        </div>
                                        <div class="col-md-4 tender_fee_reject_div">
                                          <div class="form-group">
                                            <label class="control-label">Tender Fee Reject Attachment</label>
                                            <input type="file" class="form-control tender_opening_fee_reject_attachment {{$tender['tender_opening_fee_reject_attachment'] ? "" : "required"}}" name="tender_opening_fee_reject_attachment" id="tender_opening_fee_reject_attachment">
                                            <input type="hidden" name="tender_opening_fee_reject_attachment_hidden" class="tender_opening_fee_reject_attachment_hidden" value="{{$tender['tender_opening_fee_reject_attachment']}}">
                                          </div>
                                        </div>
                                        <div class="col-md-1 tender_fee_reject_div" style="padding-top: 30px;">
                                            {{-- @if($tender['tender_opening_fee_reject_attachment']) --}}
                                            <a href="{{url('downloadFeeRejectDoc')}}/{{$tender_id}}" target="_blank" class="btn btn-primary btn-circle downloadFeeRejectDoc" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                            {{-- @endif --}}
                                        </div>
                                      </div>
                                      <button type="button" class="btn btn-success opening_status_fee_form_btn">Save</button>
                                      </form>
                                      <hr>
                                      <form method="post" action="#" id="opening_status_emd_form" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <input type="hidden" name="form_name" value="emd">
                                      <h3>Tender EMD</h3>
                                      <div class="row">
                                        <div class="col-md-4">
                                          <div class="form-group">
                                            <label class="control-label">Tender EMD Status</label>
                                            <select class="form-control tender_opening_emd_status required" name="tender_opening_emd_status" id="tender_opening_emd_status">
                                              <option value="">Select</option>
                                              <option value="Eligible" {{$tender['tender_opening_emd_status'] == "Eligible" ? "selected" : ""}}>Eligible</option>
                                              <option value="Reject" {{$tender['tender_opening_emd_status'] == "Reject" ? "selected" : ""}}>Reject</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="col-md-4">
                                          <div class="form-group">
                                            <label class="control-label">Tender EMD Release Date</label>
                                            <input type="text" class="form-control tender_opening_release_date required" name="tender_opening_release_date" id="tender_opening_release_date" value="{{date('d-m-Y H:i a',strtotime($tender['tender_opening_release_date']))}}">
                                          </div>
                                        </div>
                                        
                                      </div>
                                      <div class="row tender_emd_reject_div">
                                        <div class="col-md-4">
                                          <div class="form-group">
                                            <label class="control-label">Tender EMD Reject Reason</label>
                                            {{-- <input type="text" class="form-control tender_opening_emd_reject_reason required" name="tender_opening_emd_reject_reason" id="tender_opening_emd_reject_reason" value="{{$tender['tender_opening_emd_reject_reason']}}"> --}}
                                            <textarea class="form-control tender_opening_emd_reject_reason required" name="tender_opening_emd_reject_reason" id="tender_opening_emd_reject_reason">{{$tender['tender_opening_emd_reject_reason']}}</textarea>
                                          </div>
                                        </div>
                                        <div class="col-md-4">
                                          <div class="form-group">
                                            <label class="control-label">Tender EMD Reject Attachment</label>
                                            <input type="file" class="form-control tender_opening_emd_reject_attachment {{$tender['tender_opening_emd_reject_attachment'] ? "" : "required"}}" name="tender_opening_emd_reject_attachment" id="tender_opening_emd_reject_attachment">
                                            <input type="hidden" name="tender_opening_emd_reject_attachment_hidden" class="tender_opening_emd_reject_attachment_hidden" value="{{$tender['tender_opening_emd_reject_attachment']}}">
                                          </div>
                                        </div>
                                        <div class="col-md-1" style="padding-top: 30px;">
                                            <a href="{{url('downloadEmdRejectDoc')}}/{{$tender_id}}" target="_blank" class="btn btn-primary btn-circle downloadEmdRejectDoc" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                        </div>
                                      </div>
                                      <button type="button" class="btn btn-success opening_status_emd_form_btn">Save</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> List of Participated Bidder 
                                    {{-- @if($tender_participated_bidder_count)
                                      <button type="button" class="btn btn-success btn-circle" id="participated_bidder_info"></button>
                                    @else
                                      <button type="button" class="btn btn-danger btn-circle" id="participated_bidder_info"></button>
                                    @endif --}}
                                    
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form method="post" action="#" id="participated_bidder_form">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <div id="participated_bidder_div">
                                        @if(count($tender_participated_bidder))
                                          @foreach($tender_participated_bidder as $key_p => $value_p)
                                            <div class="participated_bidder" id="participated_bidder_{{$key_p}}">
                                              <div class="row">
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label class="control-label">Name</label>
                                                    <input type="text" name="bidder_name[{{$key_p}}]" class="form-control bidder_name" id="bidder_name_{{$key_p}}" value="{{$value_p->bidder_name}}">
                                                    <input type="hidden" name="bidder_id[{{$key_p}}]" value="{{$value_p->id}}">
                                                    <div class="bidderlist" id="bidderList_bidder_name_{{$key_p}}"></div>
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label class="control-label">Address</label>
                                                    <input type="text" name="bidder_address[{{$key_p}}]" class="form-control bidder_address" id="bidder_address_{{$key_p}}" value="{{$value_p->bidder_address}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label class="control-label">Contact Number</label>
                                                    <input type="text" name="bidder_contact_no[{{$key_p}}]" class="form-control bidder_contact_no" id="bidder_contact_no_{{$key_p}}" value="{{$value_p->bidder_contact_no}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-2" style="padding-top: 30px;">
                                                  <button type="button" class="btn btn-danger remove_participated_bidder_delete" id="{{$value_p->id}}"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                </div>
                                              </div>
                                            </div>
                                          @endforeach
                                        @else
                                          <div class="participated_bidder" id="participated_bidder_0">
                                            <div class="row">
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label class="control-label">Name</label>
                                                  <input type="text" name="bidder_name[]" class="form-control bidder_name" id="bidder_name_0">
                                                  <div class="bidderlist" id="bidderList_bidder_name_0"></div>
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label class="control-label">Address</label>
                                                  <input type="text" name="bidder_address[]" class="form-control bidder_address" id="bidder_address_0">
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label class="control-label">Contact Number</label>
                                                  <input type="text" name="bidder_contact_no[]" class="form-control bidder_contact_no" id="bidder_contact_no_0">
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        @endif
                                        
                                      </div>
                                      <div class="row">
                                          <div class="col-md-9"></div>
                                          <div class="col-md-3">
                                            <button type="button" class="btn btn-success" onclick="addMoreParticipate()"> <i class="fa fa-plus"></i> Add More</button>
                                          </div>
                                      </div>
                                      <button type="button" class="btn btn-success participated_bidder_form_btn">Save</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Opening Status of Technical Part
                                    @if($tender['open_status_tech'] == "Eligible")
                                      <button type="button" class="btn btn-success btn-circle" id="open_status_tech_info"></button>
                                    @elseif($tender['open_status_tech'] == "Reject")
                                      <button type="button" class="btn btn-danger btn-circle" id="open_status_tech_info"></button>
                                    @else
                                      <button type="button" class="btn btn-warning btn-circle" id="open_status_tech_info"></button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form method="post" action="#" id="opening_status_tech_form" enctype="multipart/form-data">
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      @csrf
                                      <div class="row">
                                        <div class="col-md-3">
                                          <div class="form-group">
                                            <label class="control-label">Query Status</label>
                                            <select class="form-control open_query_status_tech required" name="open_query_status_tech" id="open_query_status_tech">
                                              <option value="">Select</option>
                                              <option value="Yes" {{$tender['open_query_status_tech'] == "Yes" ? "selected" : ""}}>Yes</option>
                                              <option value="No" {{$tender['open_query_status_tech'] == "No" ? "selected" : ""}}>No</option>
                                            </select>
                                          </div>
                                        </div>
                                      </div>
                                      <div id="opening_status_tech_div">
                                        @if($tender_opening_status_technical_count)
                                          {{-- @foreach($tender_opening_status_technical as $key => $value) --}}
                                            {{-- <div class="open_status_tech_part" id="open_status_tech_part_{{$key}}"><hr>
                                              <div class="row open_status_tech_div">
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label class="control-label">Query Detail</label>
                                                    <input type="text" name="query_detail_tech[{{$key}}]" id="query_detail_tech_{{$key}}" class="form-control query_detail_tech" value="{{$value->query_detail_tech}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label class="control-label">Query Document</label>
                                                    <input type="file" name="query_document_tech[{{$key}}]" class="form-control query_document_tech" id="query_document_tech_{{$key}}">
                                                    <input type="hidden" name="query_document_tech_hidden[{{$key}}]"  id="query_document_tech_hidden_{{$key}}" value="{{$value->query_document_tech}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label class="control-label">Query Receive Date</label>
                                                    <input type="text" name="query_receive_date_tech[{{$key}}]" class="form-control query_receive_date_tech" id="query_receive_date_tech_{{$key}}" value="{{date('d-m-Y',strtotime($value->query_receive_date_tech))}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-1" style="margin-top: 29px;">
                                                  <div class="form-group">
                                                    <a href="{{url('downloadtechQDdoc')}}/{{$value->id}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="row open_status_tech_div">
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label class="control-label">Query Reply</label>
                                                    <textarea class="form-control query_reply_tech" name="query_reply_tech[{{$key}}]" id="query_reply_tech_{{$key}}">{{$value->query_reply_tech}}</textarea>
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label class="control-label">Query Reply Document</label>
                                                    <input type="file" name="query_reply_document_tech[{{$key}}]" id="query_reply_document_tech_{{$key}}" class="form-control query_reply_document_tech">
                                                    <input type="hidden" name="query_reply_document_tech_hidden[{{$key}}]" id="query_reply_document_tech_hidden_{{$key}}" value="{{$value->query_reply_document_tech}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label class="control-label">Query Submission Date/Time</label>
                                                    <input type="text" name="query_sub_date_tech[{{$key}}]" id="query_sub_date_tech_{{$key}}" class="form-control query_sub_date_tech" value="{{date('d-m-Y H:i a',strtotime($value->query_sub_date_tech))}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-1" style="margin-top: 29px;">
                                                  <div class="form-group">
                                                    <a href="{{url('downloadtechQRdoc')}}/{{$value->id}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                  </div>
                                                </div>
                                                <div class="col-md-2" style="margin-top: 29px;">
                                                  <div class="form-group">
                                                    <button type="button" class="btn btn-danger remove_query_tech"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                  </div>
                                                </div>
                                              </div>
                                            </div> --}}
                                          {{-- @endforeach --}}
                                        @else
                                          <div class="open_status_tech_part" id="open_status_tech_part_0">
                                            <div class="row open_status_tech_div">
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label class="control-label">Query Detail</label>
                                                  <input type="text" name="query_detail_tech[]" id="query_detail_tech_0" class="form-control query_detail_tech">
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label class="control-label">Query Document</label>
                                                  <input type="file" name="query_document_tech[]" class="form-control query_document_tech" id="query_document_tech_0">
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label class="control-label">Query Receive Date</label>
                                                  <input type="text" name="query_receive_date_tech[]" class="form-control query_receive_date_tech" id="query_receive_date_tech_0">
                                                </div>
                                              </div>
                                            </div>
                                            <div class="row open_status_tech_div">
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label class="control-label">Query Reply</label>
                                                  <textarea class="form-control query_reply_tech" name="query_reply_tech[]" id="query_reply_tech_0"></textarea>
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label class="control-label">Query Reply Document</label>
                                                  <input type="file" name="query_reply_document_tech[]" id="query_reply_document_tech_0" class="form-control query_reply_document_tech">
                                                </div>
                                              </div>
                                              <div class="col-md-3">
                                                <div class="form-group">
                                                  <label class="control-label">Query Submission Date/Time</label>
                                                  <input type="text" name="query_sub_date_tech[]" id="query_sub_date_tech_0" class="form-control query_sub_date_tech">
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          @endif
                                        </div>
                                      <div class="row open_status_tech_div">
                                          <div class="col-md-9"></div>
                                          <div class="col-md-3">
                                            <button type="button" class="btn btn-success" onclick="addMoreTechQuery()"> <i class="fa fa-plus"></i> Add More</button>
                                          </div>
                                      </div>
                                      <br><br>
                                      <div class="row">
                                        <div class="col-md-4">
                                          <div class="form-group">
                                            <label class="control-label">Technical Part Opening Status</label>
                                            <select class="form-control required open_status_tech" name="open_status_tech" id="open_status_tech">
                                              <option value="">Select</option>
                                              <option value="Pending" selected="selected">Pending</option>
                                              <option value="Eligible" {{$tender['open_status_tech'] == "Eligible" ? "selected" : ""}}>Eligible</option>
                                              <option value="Reject" {{$tender['open_status_tech'] == "Reject" ? "selected" : ""}}>Reject</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="col-md-3 open_status_tech_reject_div">
                                          <div class="form-group">
                                            <label class="control-label">Technical Part Reject Reason</label>
                                            {{-- <input type="text" class="form-control open_status_tech_reject_reason required" name="open_status_tech_reject_reason" id="open_status_tech_reject_reason" value="{{$tender['open_status_tech_reject_reason']}}"> --}}
                                            <textarea class="form-control open_status_tech_reject_reason required" name="open_status_tech_reject_reason" id="open_status_tech_reject_reason">{{$tender['open_status_tech_reject_reason']}}</textarea>
                                          </div>
                                        </div>
                                        <div class="col-md-4 open_status_tech_reject_div">
                                          <div class="form-group">
                                            <label class="control-label">Technical Part Reject Attachment</label>
                                            <input type="file" class="form-control open_status_tech_reject_attachment {{$tender['open_status_tech_reject_attachment'] ? : "required"}}" name="open_status_tech_reject_attachment" id="open_status_tech_reject_attachment">
                                            <input type="hidden" class="form-control open_status_tech_reject_attachment_hidden" name="open_status_tech_reject_attachment_hidden" value="{{$tender['open_status_tech_reject_attachment']}}">
                                          </div>
                                        </div>
                                        <div class="col-md-1 open_status_tech_reject_div" style="padding-top: 30px;">
                                            <a href="{{url('downloadTechRejectDoc')}}/{{$tender_id}}" target="_blank" class="btn btn-primary btn-circle downloadTechRejectDoc" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                        </div>
                                      </div>
                                      <button type="button" class="btn btn-success opening_status_tech_form_btn">Save</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Opening Status of Financial Part 
                                    @if($tender['open_status_fina'] == "Eligible")
                                      <button type="button" class="btn btn-success btn-circle" id="open_status_fina_info"></button>
                                    @elseif($tender['open_status_fina'] == "Reject")
                                      <button type="button" class="btn btn-danger btn-circle" id="open_status_fina_info"></button>
                                    @else
                                      <button type="button" class="btn btn-warning btn-circle" id="open_status_fina_info"></button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form method="post" action="#" id="opening_status_fina_form" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <div class="row">
                                        <div class="col-md-3">
                                          <div class="form-group">
                                            <label class="control-label">Query Status</label>
                                            <select class="form-control required open_query_status_fina" name="open_query_status_fina" id="open_query_status_fina">
                                              <option value="">Select</option>
                                              <option value="Yes" {{$tender['open_query_status_fina'] == "Yes" ? "selected" : ""}}>Yes</option>
                                              <option value="No" {{$tender['open_query_status_fina'] == "No" ? "selected" : ""}}>No</option>
                                            </select>
                                          </div>
                                        </div>
                                      </div>
                                      <div id="opening_status_fina_div">
                                        @if($tender_opening_status_financial_count)
                                          @foreach($tender_opening_status_financial as $key => $value)
                                              {{-- <div class="open_status_fina_part" id="open_status_fina_part_{{$key}}"><hr>
                                                <div class="row open_status_fina_div">
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                      <label class="control-label">Query Detail</label>
                                                      <input type="text" name="query_detail_fina[{{$key}}]" id="query_detail_fina_{{$key}}" class="form-control query_detail_fina" value="{{$value->query_detail_fina}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                      <label class="control-label">Query Document</label>
                                                      <input type="file" name="query_document_fina[{{$key}}]" class="form-control query_document_fina" id="query_document_fina_{{$key}}">
                                                      <input type="hidden" name="query_document_fina_hidden[{{$key}}]" id="query_document_fina_hidden_{{$key}}" value="{{$value->query_document_fina}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                      <label class="control-label">Query Receive Date</label>
                                                      <input type="text" name="query_receive_date_fina[{{$key}}]" class="form-control query_receive_date_fina" id="query_receive_date_fina_{{$key}}" value="{{date('d-m-Y',strtotime($value->query_receive_date_fina))}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-1" style="margin-top: 29px;">
                                                    <div class="form-group">
                                                      <a href="{{url('downloadfinaQDdoc')}}/{{$value->id}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="row open_status_fina_div">
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                      <label class="control-label">Query Reply</label>
                                                      <textarea class="form-control query_reply_fina" name="query_reply_fina[{{$key}}]" id="query_reply_fina_{{$key}}">{{$value->query_reply_fina}}</textarea>
                                                    </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                      <label class="control-label">Query Reply Document</label>
                                                      <input type="file" name="query_reply_document_fina[{{$key}}]" id="query_reply_document_fina_{{$key}}" class="form-control query_reply_document_fina">
                                                      <input type="hidden" name="query_reply_document_fina_hidden[{{$key}}]" id="query_reply_document_fina_hidden_{{$key}}" value="{{$value->query_reply_document_fina}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                    <div class="form-group">
                                                      <label class="control-label">Query Submission Date/Time</label>
                                                      <input type="text" name="query_sub_date_fina[{{$key}}]" id="query_sub_date_fina_{{$key}}" class="form-control query_sub_date_fina" value="{{date('d-m-Y H:i a',strtotime($value->query_sub_date_fina))}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-1" style="margin-top: 29px;">
                                                    <div class="form-group">
                                                      <a href="{{url('downloadfinaQRdoc')}}/{{$value->id}}" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                                    </div>
                                                  </div>
                                                  <div class="col-md-2" style="margin-top: 29px;">
                                                  <div class="form-group">
                                                    <button type="button" class="btn btn-danger remove_query_fina"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                  </div>
                                                </div>
                                                </div>
                                              </div> --}}
                                          @endforeach
                                        @else
                                        <div class="open_status_fina_part" id="open_status_fina_part_0">
                                          <div class="row open_status_fina_div">
                                            <div class="col-md-3">
                                              <div class="form-group">
                                                <label class="control-label">Query Detail</label>
                                                <input type="text" name="query_detail_fina[]" id="query_detail_fina_0" class="form-control query_detail_fina">
                                              </div>
                                            </div>
                                            <div class="col-md-3">
                                              <div class="form-group">
                                                <label class="control-label">Query Document</label>
                                                <input type="file" name="query_document_fina[]" class="form-control query_document_fina" id="query_document_fina_0">
                                              </div>
                                            </div>
                                            <div class="col-md-3">
                                              <div class="form-group">
                                                <label class="control-label">Query Receive Date</label>
                                                <input type="text" name="query_receive_date_fina[]" class="form-control query_receive_date_fina" id="query_receive_date_fina_0">
                                              </div>
                                            </div>
                                          </div>
                                          <div class="row open_status_fina_div">
                                            <div class="col-md-3">
                                              <div class="form-group">
                                                <label class="control-label">Query Reply</label>
                                                <textarea class="form-control query_reply_fina" name="query_reply_fina[]" id="query_reply_fina_0"></textarea>
                                              </div>
                                            </div>
                                            <div class="col-md-3">
                                              <div class="form-group">
                                                <label class="control-label">Query Reply Document</label>
                                                <input type="file" name="query_reply_document_fina[]" id="query_reply_document_fina_0" class="form-control query_reply_document_fina">
                                              </div>
                                            </div>
                                            <div class="col-md-3">
                                              <div class="form-group">
                                                <label class="control-label">Query Submission Date/Time</label>
                                                <input type="text" name="query_sub_date_fina[]" id="query_sub_date_fina_0" class="form-control query_sub_date_fina">
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                        @endif
                                      </div>
                                      <div class="row open_status_fina_div">
                                          <div class="col-md-9"></div>
                                          <div class="col-md-3">
                                            <button type="button" class="btn btn-success" onclick="addMoreFinaQuery()"> <i class="fa fa-plus"></i> Add More</button>
                                          </div>
                                      </div>
                                      <br><br>
                                      <div class="row">
                                        <div class="col-md-4">
                                          <div class="form-group">
                                            <label class="control-label">Financial Part Opening Status</label>
                                            <select class="form-control open_status_fina required" name="open_status_fina" id="open_status_fina">
                                              <option value="">Select</option>
                                              <option value="Pending" selected="selected">Pending</option>
                                              <option value="Eligible" {{$tender['open_status_fina'] == "Eligible" ? "selected" : ""}}>Eligible</option>
                                              <option value="Reject" {{$tender['open_status_fina'] == "Reject" ? "selected" : ""}}>Reject</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="col-md-3 open_status_fina_reject_div">
                                            <div class="form-group">
                                              <label class="control-label">Financial Part Reject Reason</label>
                                              {{-- <input type="text" class="form-control open_status_fina_reject_reason required" name="open_status_fina_reject_reason" id="open_status_fina_reject_reason" value="{{$tender['open_status_fina_reject_reason']}}"> --}}
                                              <textarea class="form-control open_status_fina_reject_reason required" name="open_status_fina_reject_reason" id="open_status_fina_reject_reason">{{$tender['open_status_fina_reject_reason']}}</textarea>
                                            </div>
                                          </div>
                                          <div class="col-md-4 open_status_fina_reject_div">
                                            <div class="form-group">
                                              <label class="control-label">Financial Part Reject Attachment</label>
                                              <input type="file" class="form-control open_status_fina_reject_attachment {{$tender['open_status_fina_reject_attachment'] ? : "required"}}" name="open_status_fina_reject_attachment" id="open_status_fina_reject_attachment">
                                              <input type="hidden" class="form-control open_status_fina_reject_attachment_hidden" name="open_status_fina_reject_attachment_hidden" value="{{$tender['open_status_fina_reject_attachment']}}">
                                            </div>
                                          </div>
                                          <div class="col-md-1 open_status_fina_reject_div" style="padding-top: 30px;">
                                            <a href="{{url('downloadFinaRejectDoc')}}/{{$tender_id}}" target="_blank" class="btn btn-primary btn-circle downloadFinaRejectDoc" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                        </div>
                                      </div>
                                      <button type="button" class="btn btn-success opening_status_fina_form_btn">Save</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Opening Status of Commercial Part
                                    @if($tender['opening_commercial_status'] == "Eligible")
                                      <button type="button" class="btn btn-success btn-circle" id="opening_status_comm_info"></button>
                                    @elseif($tender['opening_commercial_status'] == "Reject")
                                      <button type="button" class="btn btn-danger btn-circle" id="opening_status_comm_info"></button>
                                    @else
                                      <button type="button" class="btn btn-warning btn-circle" id="opening_status_comm_info"></button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      <form method="post" action="#" id="opening_status_commer_form" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <h3>Tender Commercial Status</h3>
                                      <div class="row">
                                        <div class="col-md-4">
                                          <div class="form-group">
                                            <label class="control-label">Status</label>
                                            <select class="form-control opening_commercial_status required" name="opening_commercial_status" id="opening_commercial_status">
                                              <option value="Pending" {{$tender['opening_commercial_status'] == "Pending" ? "selected" : "selected"}}>Pending</option>
                                              <option value="Eligible" {{$tender['opening_commercial_status'] == "Eligible" ? "selected" : ""}}>Eligible</option>
                                              <option value="Reject" {{$tender['opening_commercial_status'] == "Reject" ? "selected" : ""}}>Reject</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="col-md-4 opening_commercial_reject_div">
                                          <div class="form-group">
                                            <label class="control-label">Reject Reason</label>
                                            {{-- <input type="text" class="form-control opening_commercial_reject_reason required" name="opening_commercial_reject_reason" id="opening_commercial_reject_reason" value="{{$tender['opening_commercial_reject_reason']}}"> --}}
                                            <textarea class="form-control opening_commercial_reject_reason required" name="opening_commercial_reject_reason" id="opening_commercial_reject_reason">{{$tender['opening_commercial_reject_reason']}}</textarea>
                                          </div>
                                        </div>
                                        <div class="col-md-3 opening_commercial_reject_div">
                                          <div class="form-group">
                                            <label class="control-label">Reject Attachment</label>
                                            <input type="file" class="form-control opening_commercial_reject_attachment {{$tender['opening_commercial_reject_attachment'] ?  : "required"}}" name="opening_commercial_reject_attachment" id="opening_commercial_reject_attachment">
                                            <input type="hidden" name="opening_commercial_reject_attachment_hidden" class="opening_commercial_reject_attachment_hidden" value="{{$tender['opening_commercial_reject_attachment']}}">
                                          </div>
                                        </div>
                                        <div class="col-md-1 opening_commercial_reject_div" style="padding-top: 30px;">
                                            <a href="{{url('downloadCommRejectDoc')}}/{{$tender_id}}" target="_blank" class="btn btn-primary btn-circle downloadCommRejectDoc" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                        </div>
                                      </div>
                                      <button type="button" class="btn btn-success opening_status_commer_form_btn">Save</button>
                                      </form><hr>
                                      <form method="post" action="#" id="opening_bidder_upload_form" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                        <h3>Bidder BOQ Upload</h3>
                                        <div class="row">
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <label class="control-label">Bidder</label>
                                              <select class="form-control opening_bidder_id" name="opening_bidder_id" id="opening_bidder_id">
                                              </select>
                                            </div>
                                          </div>
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <label class="control-label">Upload Excel</label>
                                              <input type="file" name="opening_upload_file" id="opening_upload_file" class="form-control opening_upload_file">
                                            </div>
                                          </div>
                                          <div class="col-md-4" style="padding-top: 29px;">
                                            <a href="{{url('sampleBOQUpload')}}" target="_blank"><button type="button" class="btn btn-info">Sample Sheet</button></a>
                                          </div>
                                        </div>
                                        <button type="button" class="btn btn-success opening_bidder_upload_form_btn">Save</button>
                                      </form>
                                      <hr>
                                      <form method="post" action="#" id="opening_your_upload_form" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <input type="hidden" name="company_id" id="company_id" value="{{$tender['company_id']}}">
                                        <h3>Your BOQ Upload</h3>
                                        <div class="row">
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <label class="control-label">Upload Excel</label>
                                              <input type="file" name="opening_upload_file" id="opening_upload_file" class="form-control opening_upload_file">
                                            </div>
                                          </div>
                                          <div class="col-md-4" style="padding-top: 29px;">
                                            <a href="{{url('sampleBOQUpload')}}" target="_blank"><button type="button" class="btn btn-info">Sample Sheet</button></a>
                                          </div>
                                        </div>
                                        <button type="button" class="btn btn-success opening_your_upload_form_btn">Save</button>
                                      </form>
                                      <hr>
                                      <h3>View Compairision Commercial Part</h3>
                                        <div class="row">
                                          <div class="col-md-4">
                                            <div class="form-group">
                                              <button type="button" class="btn btn-primary view_compairision_btn">View</button>
                                            </div>
                                          </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                          <div class="col-md-6">
                                            <span class="display_winner_div"><STRONG>
                                              Tender Lowest :
                                            </STRONG></span><span class="text-success display_winner_div display_winner_low">-</span>
                                          </div>

                                          <div class="col-md-6">
                                            <span class="display_winner_div"><STRONG>
                                              Tender Highest :
                                            </STRONG></span><span class="text-success display_winner_div display_winner_high">-</span>
                                          </div>
                                        </div>  
                                        
                                        
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            {{-- <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Final Status of Bid
                                    
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true">
                                    <div class="panel-body">
                                      
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div> --}}
                            {{-- <button type="submit" class="btn btn-success">Submit</button> --}}
                            {{-- <button type="button" onclick="window.location.href ='{{ route('admin.tender') }}'" class="btn btn-danger">Cancel</button> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- The Modal -->
<div class="modal" id="view_bidders_item">
  <div class="modal-dialog modal-dialog-centered modal-lg" style="width:1200px;">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Commercial Status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body" id="put_dynamic_data">
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

@endsection
@section('script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" integrity="sha256-yMjaV542P+q1RnH6XByCPDfUFhmOafWbeLPmqKh11zo=" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
<script>
var counter_participated_bidder = {{$tender_participated_bidder_count}};
var counter_tech_query = {{$tender_opening_status_technical_count}};
var counter_tech_query_validate = {{$tender_opening_status_technical_count}};
var counter_fina_query = {{$tender_opening_status_financial_count}};
var counter_fina_query_validate = {{$tender_opening_status_financial_count}};
var tender_id = {{$tender_id}};

var check_fee_reject_doc = "{{$tender['tender_opening_fee_status']}}";
var check_emd_reject_doc = "{{$tender['tender_opening_emd_status']}}";
var check_comm_reject_doc = "{{$tender['opening_commercial_status']}}";
var check_tech_reject_doc = "{{$tender['open_status_tech']}}";
var check_fina_reject_doc = "{{$tender['open_status_fina']}}";
$(document).ready(function(){
$('#tender_opening_fee_status').trigger('change');
$('#tender_opening_emd_status').trigger('change');
$('#open_query_status_fina').trigger('change');
$('#open_status_fina').trigger('change');
$('#open_query_status_tech').trigger('change');
$('#open_status_tech').trigger('change');
$('#opening_commercial_status').trigger('change');
getBidder();
getTenderWinnerName();
getOpeningTechnical();
getOpeningFinancial();




checkFeeRejectDoc(check_fee_reject_doc);
checkEmdRejectDoc(check_emd_reject_doc)
checkCommRejectDoc(check_comm_reject_doc);
checkTechRejectDoc(check_tech_reject_doc);
checkFinaRejectDoc(check_fina_reject_doc);

});

// ------ Opening Status Preliminary Start --------

$("#tender_opening_fee_status").on('change',function(){
  var check_val = $(this).val();
  if(check_val == "Reject"){
    $(".tender_fee_reject_div").show();
  }else{
    $(".tender_fee_reject_div").hide();
  }
});


$(".opening_status_fee_form_btn").on('click',function(){
  if($("#opening_status_fee_form").valid()){
    $(".opening_status_fee_form_btn").attr("disabled", true);
    var form = $('#opening_status_fee_form')[0];
    var formData1 = new FormData(form);

    formData1.append('tender_opening_fee_reject_attachment', $('.tender_opening_fee_reject_attachment')[0].files[0]);
    $.ajax({
      type : "POST",
      url : "{{url('save_opening_status')}}",
      data : formData1,
      processData: false,
      contentType: false,
      success : function(data){
        // console.log(data);
        $(".opening_status_fee_form_btn").attr("disabled", false);

        if($("#tender_opening_fee_status").val() == "Eligible"){
          checkFeeRejectDoc("Eligible");
          $(".tender_opening_fee_reject_reason").val("");
          $(".tender_opening_fee_reject_attachment_hidden").val("");
          $("#opening_status_info").removeClass($("#opening_status_info").attr('class'));
          $("#opening_status_info").addClass('btn btn-success btn-circle');
        }else if($("#tender_opening_fee_status").val() == "Reject"){
          checkFeeRejectDoc("Reject");
          $("#opening_status_info").removeClass($("#opening_status_info").attr('class'));
          $("#opening_status_info").addClass('btn btn-danger btn-circle');
        }else{
          $("#opening_status_info").removeClass($("#opening_status_info").attr('class'));
          $("#opening_status_info").addClass('btn btn-warning btn-circle');
        }
        alertMassage(data,"");
      }
    });
  }
});

function checkFeeRejectDoc(check_fee_reject_doc){
  if(check_fee_reject_doc == "Reject"){
    $(".downloadFeeRejectDoc").show();
  }else{
    $(".downloadFeeRejectDoc").hide();
  }  
}


$("#tender_opening_emd_status").on('change',function(){
  var check_val = $(this).val();
  if(check_val == "Reject"){
    $(".tender_emd_reject_div").show();
  }else{
    $(".tender_emd_reject_div").hide();
  }
});


function checkEmdRejectDoc(check_emd_reject_doc){
  if(check_emd_reject_doc == "Reject"){
    $(".downloadEmdRejectDoc").show();
  }else{
    $(".downloadEmdRejectDoc").hide();
  }  
}

$(".opening_status_emd_form_btn").on('click',function(){
  if($("#opening_status_emd_form").valid()){
    $(".opening_status_emd_form_btn").attr("disabled", true);
    var form = $('#opening_status_emd_form')[0];
    var formData1 = new FormData(form);

    formData1.append('tender_opening_emd_reject_attachment', $('.tender_opening_emd_reject_attachment')[0].files[0]);
    
    $.ajax({
      type : "POST",
      url : "{{url('save_opening_status')}}",
      data : formData1,
      processData: false,
      contentType: false,
      success : function(data){
        // console.log(data);
        $(".opening_status_emd_form_btn").attr("disabled", false);

        if($("#tender_opening_emd_status").val() == "Eligible"){
          $("#opening_status_info1").removeClass($("#opening_status_info1").attr('class'));
          $("#opening_status_info1").addClass('btn btn-success btn-circle');
          checkEmdRejectDoc("Eligible");
          $(".tender_opening_emd_reject_reason").val("");
          $(".tender_opening_emd_reject_attachment_hidden").val("");
        }else if($("#tender_opening_emd_status").val() == "Reject"){
          $("#opening_status_info1").removeClass($("#opening_status_info1").attr('class'));
          $("#opening_status_info1").addClass('btn btn-danger btn-circle');
          checkEmdRejectDoc("Reject");
        }else{
          $("#opening_status_info1").removeClass($("#opening_status_info1").attr('class'));
          $("#opening_status_info1").addClass('btn btn-warning btn-circle');
        }

        alertMassage(data,"");
      }
    });
  }
});

jQuery('.tender_opening_release_date,.query_receive_date_tech,.query_receive_date_fina').datetimepicker({
            // format:'DD-MM-YYYY h:mm a',
            format:'DD-MM-YYYY',
            minDate : new Date(),
      });
jQuery('.query_sub_date_tech,.query_sub_date_fina').datetimepicker({
            format:'DD-MM-YYYY h:mm a',
            minDate : new Date(),
      });

// ------ Opening Status Preliminary End ----------

// --------- LIST OF PARTICIPATED BIDDER Start -------

function addMoreParticipate(){
  counter_participated_bidder += 1;
  $("#participated_bidder_div").append('<div class="participated_bidder" id="participated_bidder_'+counter_participated_bidder+'">'+
                                          '<div class="row">'+
                                            '<div class="col-md-3">'+
                                              '<div class="form-group">'+
                                                '<label class="control-label">Name</label>'+
                                                '<input type="text" name="bidder_name['+counter_participated_bidder+']" class="form-control bidder_name" id="bidder_name_'+counter_participated_bidder+'">'+
                                                '<div class="bidderlist" id="bidderList_bidder_name_'+counter_participated_bidder+'"></div>'+
                                              '</div>'+
                                            '</div>'+
                                            '<div class="col-md-3">'+
                                              '<div class="form-group">'+
                                                '<label class="control-label">Address</label>'+
                                                '<input type="text" name="bidder_address['+counter_participated_bidder+']" class="form-control bidder_address" id="bidder_address_'+counter_participated_bidder+'">'+
                                              '</div>'+
                                            '</div>'+
                                            '<div class="col-md-3">'+
                                              '<div class="form-group">'+
                                                '<label class="control-label">Contact Number</label>'+
                                                '<input type="text" name="bidder_contact_no['+counter_participated_bidder+']" class="form-control bidder_contact_no" id="bidder_contact_no_'+counter_participated_bidder+'">'+
                                              '</div>'+
                                            '</div>'+
                                            '<div class="col-md-2" style="padding-top: 30px;">'+
                                                '<button type="button" class="btn btn-danger remove_participated_bidder"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                              '</div>'+
                                          '</div>'+
                                        '</div>');
}

//remove
$('body').on('click','.remove_participated_bidder',function(){
  $(this).parents('.participated_bidder').remove();
});
//delete
$('body').on('click','.remove_participated_bidder_delete',function(){
  var bidder_delete_id = $(this).attr('id');
  $(this).parents('.participated_bidder').remove();
  $.ajax({
    type : "POST",
    url : "{{url('delete_participated_bidder')}}",
    data : {
      "id" : bidder_delete_id,
      "_token" : "{{csrf_token()}}",
    },
    success : function(data){
      getBidder();
    }
  });
});

//add edit data
$(".participated_bidder_form_btn").on('click',function(){
  $(".bidder_name").each(function(){
      $(this).rules('add',{
        required : true
      });
    });
  $(".bidder_address").each(function(){
    $(this).rules('add',{
      required : true
    });
  });
  $(".bidder_contact_no").each(function(){
    $(this).rules('add',{
      required : true
    });
  });
  if($("#participated_bidder_form").valid()){
    $(".participated_bidder_form_btn").attr("disabled", true);
    var form = $("#participated_bidder_form").serialize();
    $.ajax({
      type : "POST",
      url : "{{url('save_participated_bidder')}}",
      data : form,
      success : function(data){
        // console.log(data);
        $(".participated_bidder_form_btn").attr("disabled", false);
        alertMassage(data,"participated_bidder_info");
        getBidder();
      },
    });     
  }
});
$("#participated_bidder_form").validate();

$('body').on('keyup',".bidder_name",function(){
  var search = $(this).val();
  var search_id = $(this).attr('id');
  if(search != ""){
      // console.log(search);
      // console.log(search_id);
      $.ajax({
          url: "{{url('get_bidder_log')}}",
          type: 'post',
          data: {
            search:search,
            "_token" : "{{csrf_token()}}",
          },
          dataType: 'json',
          success:function(data){
              // console.log(data);
              if(data.length){
                $('#bidderList_'+search_id).html("");
                output = '<ul class="dropdown-menu" style="display:block; position:relative">';
                $.each( data, function(key, value){
                  output += '<li><a href="javascript:void(0)" id="'+value.id+'" onclick="getBidderData(this.id)" data-dynamicid="'+search_id+'">'+value.bidder_name+'</a></li>';
                });
                output += '</ul>';
                $('#bidderList_'+search_id).show();  
                $('#bidderList_'+search_id).html(output);
                search_id = "";  
              }else{
                $('#bidderList_'+search_id).hide();
                search_id = "";
              }
              
          }
      });
  }
});

// $('body').on('mouseup',".bidder_name",function(){
//   var search = $(this).val();
//   var search_id = $(this).attr('id');
//   $('#bidderList_'+search_id).fadeOut();
// });

function getBidderData(id)
{
  var search_id = $("#"+id).data("dynamicid");
  $.ajax({
    type : "POST",
    url : "{{url('get_bidder_log_detail')}}",
    data : {
      id : id,
      "_token" : "{{csrf_token()}}",
    },
    dataType : 'json',
    success : function(data){
      // console.log(data);
      var search_id_arr = search_id.split('_');
      // console.log(search_id);
      // console.log(search_id_arr[2]);
      $('#bidderList_'+search_id).hide();
      $("#bidder_name_"+search_id_arr[2]).val(data.bidder_name);
      $("#bidder_address_"+search_id_arr[2]).val(data.bidder_address);
      $("#bidder_contact_no_"+search_id_arr[2]).val(data.bidder_contact_no);
    }
  })
}

// --------- LIST OF PARTICIPATED BIDDER End ---------

// ---------- OPENING STATUS OF Financial Start -----
$('#open_query_status_fina').on('change', function(){
    var check_val = $(this).val();
    if(check_val == "Yes"){
        $(".open_status_fina_div").show();
    }else{
        $(".open_status_fina_div").hide();
    }    
});

//remove
$('body').on('click','.remove_query_fina',function(){
  $(this).parents('.open_status_fina_part').remove();
});

//Add more
function addMoreFinaQuery(){
  counter_fina_query += 1;
  $("#opening_status_fina_div").append('<div class="open_status_fina_part" id="open_status_fina_part_'+counter_fina_query+'">'+
                                            '<div class="row open_status_fina_div"><hr>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Detail</label>'+
                                                  '<input type="text" name="query_detail_fina['+counter_fina_query+']" id="query_detail_fina_'+counter_fina_query+'" class="form-control query_detail_fina">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Document</label>'+
                                                  '<input type="file" name="query_document_fina['+counter_fina_query+']" class="form-control query_document_fina" id="query_document_fina_'+counter_fina_query+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Receive Date</label>'+
                                                  '<input type="text" name="query_receive_date_fina['+counter_fina_query+']" class="form-control query_receive_date_fina" id="query_receive_date_fina_'+counter_fina_query+'">'+
                                                '</div>'+
                                              '</div>'+
                                            '</div>'+
                                            '<div class="row open_status_fina_div">'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Reply</label>'+
                                                  '<textarea class="form-control query_reply_fina" name="query_reply_fina['+counter_fina_query+']" id="query_reply_fina_'+counter_fina_query+'"></textarea>'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Reply Document</label>'+
                                                  '<input type="file" name="query_reply_document_fina['+counter_fina_query+']" id="query_reply_document_fina_'+counter_fina_query+'" class="form-control query_reply_document_fina">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Submission Date/Time</label>'+
                                                  '<input type="text" name="query_sub_date_fina['+counter_fina_query+']" id="query_sub_date_fina_'+counter_fina_query+'" class="form-control query_sub_date_fina">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="margin-top: 29px;">'+
                                                '<div class="form-group">'+
                                                  '<button type="button" class="btn btn-danger remove_query_fina"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                '</div>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');
  jQuery('.query_receive_date_fina').datetimepicker({
            // format:'DD-MM-YYYY h:mm a',
            format:'DD-MM-YYYY',
            minDate : new Date(),
      });
  jQuery('.query_sub_date_fina').datetimepicker({
              format:'DD-MM-YYYY h:mm a',
              minDate : new Date(),
        });
}

//Add edit data
$(".opening_status_fina_form_btn").on('click',function(){
  $(".query_detail_fina").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_document_fina").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_receive_date_fina").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_reply_fina").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_reply_document_fina").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_sub_date_fina").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  for(j = 0 ; j < counter_fina_query_validate ; j++){
    $("#query_document_fina_"+j).removeClass("error").rules("remove");
  }

  for(j = 0 ; j < counter_fina_query_validate ; j++){
    $("#query_reply_document_fina_"+j).removeClass("error").rules("remove");
  }

  if($("#opening_status_fina_form").valid()){
    $(".opening_status_fina_form_btn").attr("disabled", true);
    var form = $('#opening_status_fina_form')[0];
        var formData = new FormData(form);
        
        formData.append('open_status_fina_reject_attachment', $('.open_status_fina_reject_attachment')[0].files[0]);    

        if(counter_fina_query == 0){
            formData.append('query_document_fina[]', $('.query_document_fina')[0].files[0]);
            formData.append('query_reply_document_fina[]', $('.query_reply_document_fina')[0].files[0]);
        }else{
          for(i = 0; i >= counter_fina_query ; i++){
              formData.append('query_document_fina', $('.query_document_fina')[1].files[1]);  
              formData.append('query_reply_document_fina', $('.query_reply_document_fina')[1].files[1]);  
          }  
        }
          
        $.ajax({
          type : "POST",
          url : "{{url('tender_opening_query_fina')}}",
          data : formData,
          processData: false,
          contentType: false,
          success : function(data){
            console.log(data);
            $(".opening_status_fina_form_btn").attr("disabled", false);
            if($("#open_status_fina").val() == "Eligible"){
              $("#open_status_fina_info").removeClass($("#open_status_fina_info").attr('class'));
              $("#open_status_fina_info").addClass('btn btn-success btn-circle');
              checkFinaRejectDoc("Eligible");
              $(".open_status_fina_reject_reason").val('');
              $(".open_status_fina_reject_attachment").val('');
            }else if($("#open_status_fina").val() == "Reject"){
              $("#open_status_fina_info").removeClass($("#open_status_fina_info").attr('class'));
              $("#open_status_fina_info").addClass('btn btn-danger btn-circle');
              checkFinaRejectDoc("Reject");
            }else{
              $("#open_status_fina_info").removeClass($("#open_status_fina_info").attr('class'));
              $("#open_status_fina_info").addClass('btn btn-warning btn-circle');
              checkFinaRejectDoc("Pending");
            }
            alertMassage(data,"");
            getOpeningFinancial();
          },
          error:function(data){
            $(".opening_status_fina_form_btn").attr("disabled", false);
              alertMassage("error",""); //===Show Error Message====
          }
        });
  }
});
$("#opening_status_fina_form").validate();

$("#open_status_fina").on('change',function(){
var check_val = $(this).val();
if(check_val == "Reject"){
  $(".open_status_fina_reject_div").show();
}else{
  $(".open_status_fina_reject_div").hide();
}
});

function checkFinaRejectDoc(check_fina_reject_doc){
  if(check_fina_reject_doc == "Reject"){
    $(".downloadFinaRejectDoc").show();
  }else{
    $(".downloadFinaRejectDoc").hide();
  }
}

// ---------- OPENING STATUS OF Financial End -----

// ---------- OPENING STATUS OF TECHNICAL Start -----
$('#open_query_status_tech').on('change', function(){
    var check_val = $(this).val();
    if(check_val == "Yes"){
        $(".open_status_tech_div").show();
    }else{
        $(".open_status_tech_div").hide();
    }    
});

//remove
$('body').on('click','.remove_query_tech',function(){
  $(this).parents('.open_status_tech_part').remove();
});

//Add More 
function addMoreTechQuery(){
  counter_tech_query += 1;
  $("#opening_status_tech_div").append('<div class="open_status_tech_part" id="open_status_tech_part_'+counter_tech_query+'">'+
                                            '<div class="row open_status_tech_div"><hr>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Detail</label>'+
                                                  '<input type="text" name="query_detail_tech['+counter_tech_query+']" id="query_detail_tech_'+counter_tech_query+'" class="form-control query_detail_tech">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Document</label>'+
                                                  '<input type="file" name="query_document_tech['+counter_tech_query+']" class="form-control query_document_tech" id="query_document_tech_'+counter_tech_query+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Receive Date</label>'+
                                                  '<input type="text" name="query_receive_date_tech['+counter_tech_query+']" class="form-control query_receive_date_tech" id="query_receive_date_tech_'+counter_tech_query+'">'+
                                                '</div>'+
                                              '</div>'+
                                            '</div>'+
                                            '<div class="row open_status_tech_div">'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Reply</label>'+
                                                  '<textarea class="form-control query_reply_tech" name="query_reply_tech['+counter_tech_query+']" id="query_reply_tech_'+counter_tech_query+'"></textarea>'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Reply Document</label>'+
                                                  '<input type="file" name="query_reply_document_tech['+counter_tech_query+']" id="query_reply_document_tech_'+counter_tech_query+'" class="form-control query_reply_document_tech">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-3">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label">Query Submission Date/Time</label>'+
                                                  '<input type="text" name="query_sub_date_tech['+counter_tech_query+']" id="query_sub_date_tech_'+counter_tech_query+'" class="form-control query_sub_date_tech">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="margin-top: 29px;">'+
                                                '<div class="form-group">'+
                                                  '<button type="button" class="btn btn-danger remove_query_tech"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                '</div>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');

  jQuery('.query_receive_date_tech').datetimepicker({
            // format:'DD-MM-YYYY h:mm a',
            format:'DD-MM-YYYY',
            minDate : new Date(),
      });
  jQuery('.query_sub_date_tech').datetimepicker({
              format:'DD-MM-YYYY h:mm a',
              minDate : new Date(),
        });
}

//Add edit data
$(".opening_status_tech_form_btn").on('click',function(){
  $(".query_detail_tech").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_document_tech").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_receive_date_tech").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_reply_tech").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_reply_document_tech").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  $(".query_sub_date_tech").each(function(){
    $(this).rules('add',{
      required : true
    });
  });

  for(j = 0 ; j < counter_tech_query_validate ; j++){
    $("#query_document_tech_"+j).removeClass("error").rules("remove");
  }

    for(j = 0 ; j < counter_tech_query_validate ; j++){
    $("#query_reply_document_tech_"+j).removeClass("error").rules("remove");
  }

  if($("#opening_status_tech_form").valid()){
    $(".opening_status_tech_form_btn").attr("disabled", true);
    var form = $('#opening_status_tech_form')[0];
        var formData = new FormData(form);
        
        formData.append('open_status_tech_reject_attachment', $('.open_status_tech_reject_attachment')[0].files[0]);  

        if(counter_tech_query == 0){
            formData.append('query_document_tech[]', $('.query_document_tech')[0].files[0]);
            formData.append('query_reply_document_tech[]', $('.query_reply_document_tech')[0].files[0]);
        }else{
          for(i = 0; i >= counter_tech_query ; i++){
              formData.append('query_document_tech', $('.query_document_tech')[1].files[1]);  
              formData.append('query_reply_document_tech', $('.query_reply_document_tech')[1].files[1]);  
          }  
        }
          
        $.ajax({
          type : "POST",
          url : "{{url('tender_opening_query_tech')}}",
          data : formData,
          processData: false,
          contentType: false,
          success : function(data){
            console.log(data);
            $(".opening_status_tech_form_btn").attr("disabled", false);

            if($("#open_status_tech").val() == "Eligible"){
              $("#open_status_tech_info").removeClass($("#open_status_tech_info").attr('class'));
              $("#open_status_tech_info").addClass('btn btn-success btn-circle');
              checkTechRejectDoc("Eligible");
              $(".open_status_tech_reject_reason").val("");
              $(".open_status_tech_reject_attachment_hidden").val("");
            }else if($("#open_status_tech").val() == "Reject"){
              $("#open_status_tech_info").removeClass($("#open_status_tech_info").attr('class'));
              $("#open_status_tech_info").addClass('btn btn-danger btn-circle');
              checkTechRejectDoc("Reject");
            }else{
              $("#open_status_tech_info").removeClass($("#open_status_tech_info").attr('class'));
              $("#open_status_tech_info").addClass('btn btn-warning btn-circle');
              checkTechRejectDoc("Pending");
              $(".open_status_tech_reject_reason").val("");
              $(".open_status_tech_reject_attachment_hidden").val("");
            }

            alertMassage(data,"");
            getOpeningTechnical();
          },
          error:function(data){
            $(".opening_status_tech_form_btn").attr("disabled", false);
              alertMassage("error",""); //===Show Error Message====
          }
        });
  }
});
$("#opening_status_tech_form").validate();


$("#open_status_tech").on('change',function(){
  var check_val = $(this).val();
  if(check_val == "Reject"){
    $(".open_status_tech_reject_div").show();
  }else{
    $(".open_status_tech_reject_div").hide();
  }
});


function checkTechRejectDoc(check_tech_reject_doc){
  if(check_tech_reject_doc == "Reject"){
    $(".downloadTechRejectDoc").show();
  }else{
    $(".downloadTechRejectDoc").hide();
  }
}

// ---------- OPENING STATUS OF TECHNICAL End -----

// ---- Opening Status of Commercial Part Start -------

//Commercial status

$(".opening_commercial_status").on('change',function(){
  var check_val = $(this).val();
  if(check_val == "Reject"){
    $(".opening_commercial_reject_div").show();
  }else{
    $(".opening_commercial_reject_div").hide();
  }
});

function checkCommRejectDoc(check_comm_reject_doc){
  if(check_comm_reject_doc === "Reject"){
    $(".downloadCommRejectDoc").show();
  }else{
    $(".downloadCommRejectDoc").hide();
  }  
}

$(".opening_status_commer_form_btn").on('click',function(){
  if($("#opening_status_commer_form").valid()){
      $(".opening_status_commer_form_btn").attr("disabled", true);
    var form = $('#opening_status_commer_form')[0];
    var formData1 = new FormData(form);

    formData1.append('opening_commercial_reject_attachment', $('.opening_commercial_reject_attachment')[0].files[0]);
      $.ajax({
        type : "POST",
        url : "{{url('save_opening_commercial_status')}}",
        data : formData1,
        processData: false,
        contentType: false,
        success : function(data){
          // console.log(data);
          $(".opening_status_commer_form_btn").attr("disabled", false);

          if($("#opening_commercial_status").val() == "Eligible"){
            $("#opening_status_comm_info").removeClass($("#opening_status_comm_info").attr('class'));
            $("#opening_status_comm_info").addClass('btn btn-success btn-circle');
            checkCommRejectDoc("Eligible");
            $(".opening_commercial_reject_reason").val('');
            $(".opening_commercial_reject_attachment_hidden").val('');
          }else if($("#opening_commercial_status").val() == "Reject"){
            $("#opening_status_comm_info").removeClass($("#opening_status_comm_info").attr('class'));
            $("#opening_status_comm_info").addClass('btn btn-danger btn-circle');
            checkCommRejectDoc("Reject");
          }else{
            $("#opening_status_comm_info").removeClass($("#opening_status_comm_info").attr('class'));
            $("#opening_status_comm_info").addClass('btn btn-warning btn-circle');
            checkCommRejectDoc("Pending");
            $(".opening_commercial_reject_reason").val('');
            $(".opening_commercial_reject_attachment_hidden").val('');
          }
          alertMassage(data,"");
        }
      });
  }
});

$("#opening_bidder_upload_form").validate({
  rules : {
    opening_bidder_id : {
      required : true,
    },
    opening_upload_file : {
      required :true,
      extension: "xls|xlsx"
    }
  },
  messages : {
    opening_upload_file : {
      extension: "Please select excel file"
    }
  }
});

//Bidder Upload file
$(".opening_bidder_upload_form_btn").on('click',function(){
  if($("#opening_bidder_upload_form").valid()){
    var form = $('#opening_bidder_upload_form')[0];
    var formData1 = new FormData(form);
    $(".opening_bidder_upload_form_btn").attr("disabled", true);
    formData1.append('opening_upload_file', $('.opening_upload_file')[0].files[0]);
    $.ajax({
      type : "POST",
      url : "{{url('boqImportData')}}",
      data : formData1,
      processData: false,
      contentType: false,
      success : function(data){
        data = JSON.parse(data);
        $("#opening_bidder_upload_form")[0].reset()
        $(".opening_bidder_upload_form_btn").attr("disabled", false);
        getBidder();
        if(data.status == 'true'){
            $.toast({
              heading: 'File Upload Message',
              text: data.message,
              position: 'top-right',
              loaderBg:'#ff6849',
              icon: 'success',
              hideAfter: 3500, 
              stack: 6
            });
        }else{
          $.toast({
            heading: "File Upload Message",
            text: data.message,
            position: 'top-right',
            loaderBg:'#ff6849',
            icon: 'error',
            hideAfter: 3500
          });  
        }
      },
      error :function(data){
        $(".opening_bidder_upload_form_btn").attr("disabled", false);
        $.toast({
          heading: "File Upload Message",
          text: 'File not upload try again !!',
          position: 'top-right',
          loaderBg:'#ff6849',
          icon: 'error',
          hideAfter: 3500
        });
      }
    });
  }
});

//Your boq upload
$("#opening_your_upload_form").validate({
  rules : {
    opening_upload_file : {
      required :true,
      extension: "xls|xlsx"
    }
  },
  messages : {
    opening_upload_file : {
      extension: "Please select excel file"
    },
  }
});

$(".opening_your_upload_form_btn").on('click',function(){
  if($("#opening_your_upload_form").valid()){
    var form = $('#opening_your_upload_form')[0];
    var formData1 = new FormData(form);
    $(".opening_your_upload_form_btn").attr("disabled", true);
    formData1.append('opening_upload_file', $('.opening_upload_file')[0].files[0]);
    $.ajax({
      type : "POST",
      url : "{{url('yourboqImportData')}}",
      data : formData1,
      processData: false,
      contentType: false,
      success : function(data){
        data = JSON.parse(data);
        $("#opening_your_upload_form")[0].reset()
        $(".opening_your_upload_form_btn").attr("disabled", false);
        getBidder();
        if(data.status == 'true'){
            $.toast({
              heading: 'File Upload Message',
              text: data.message,
              position: 'top-right',
              loaderBg:'#ff6849',
              icon: 'success',
              hideAfter: 3500, 
              stack: 6
            });
        }else{
          $.toast({
            heading: "File Upload Message",
            text: data.message,
            position: 'top-right',
            loaderBg:'#ff6849',
            icon: 'error',
            hideAfter: 3500
          });  
        }
      },
      error :function(data){
        $(".opening_your_upload_form_btn").attr("disabled", false);
        $.toast({
          heading: "File Upload Message",
          text: 'File not upload try again !!',
          position: 'top-right',
          loaderBg:'#ff6849',
          icon: 'error',
          hideAfter: 3500
        });
      }
    });
  }
});


function getBidder(){
  $.ajax({
    type : "POST",
    url : "{{url('get_bidder')}}",
    data : {
      'id' : tender_id,
      '_token' : "{{csrf_token()}}"
    },
    success : function(data){
      data = JSON.parse(data);
      if(data.length){
        $("#opening_bidder_id").html('');
        $("#opening_bidder_id").prepend('<option value="">Select</option>');
        $.each(data, function(key, value){
              $("#opening_bidder_id").append('<option value="'+value.id+'">'+value.bidder_name+' '+value.check_uploade_boq+'</option>');
        });
      }else{
        $("#opening_bidder_id").html('');
        $("#opening_bidder_id").append('<option value="">Bidder Not Found</option>');
      }
    }
  });
}

// View Compairision Commercial Part

$(".view_compairision_btn").on('click',function(){

$.ajax({
  type : "POST",
  url : "{{url('view_compairision_bidder')}}",
  data : {
    'id' : tender_id,
    'company_id' : {{$tender['company_id']}},
    '_token' : "{{csrf_token()}}"
  },
  success : function(data){
    // console.log(data);
    $("#put_dynamic_data").html(data);
    $("#view_bidders_item").modal('show');

    $("#item_id").select2({
      placeholder: "Items",
      allowClear: true
  });
    $("#bidder_id").select2({
        placeholder: "Bidders",
        allowClear: true
    });


  },
  error : function(data){
    /*$.toast({
          heading: "Something want wrong",
          // text: 'File not upload try again !!',
          position: 'top-right',
          loaderBg:'#ff6849',
          icon: 'error',
          hideAfter: 3500
        });*/
  }
});
});

$("#view_bidders_item").on('change','#bidder_id',function(){
getBidderItem();
});

$("#view_bidders_item").on('change','#item_id',function(){

getBidderItem()
});

function getBidderItem(){
var bidder_ids = $("#bidder_id").val();
var item_ids = $("#item_id").val();

$.ajax({
  type : "POST",
  url : "{{url('get_compairision_bidder')}}",
  data : {
    "id" : tender_id,
    "bidder_ids" : bidder_ids,
    "item_ids" : item_ids,
    "_token" : "{{csrf_token()}}"
  },
  success : function(data){
    // console.log(data);
    /*$("#bidder_item_table").DataTable({
      destroy : true
    });*/    
    $("#bidder_item_table").html(data);
    // $("#bidder_item_table").DataTable();
  }
});
}

$("#view_bidders_item").on('click','#reset_all',function(){
$("#bidder_id").val("");
$("#item_id").val("");
$(".view_compairision_btn").trigger('click');
});

function getTenderWinnerName(){
$.ajax({
    type : "POST",
    url : "{{url('get_tender_winner')}}",
    data : {
      'id' : tender_id,
      'company_id' : {{$tender['company_id']}},
      '_token' : "{{csrf_token()}}"
    },
    success : function(data){
      // console.log(data);
      data = JSON.parse(data);
      if(data.status === "false"){
        $(".display_winner_div").hide();
      }else{
        $(".display_winner_div").show();
        $(".display_winner_low").html(data.min_name);
        $(".display_winner_high").html(data.max_name);
      }
    },
    error : function(data){
      $(".display_winner_div").hide();
    }
  });
}

// ---- Opening Status of Commercial Part End ---------

//
function alertMassage(data,param1){
  if(data == "success"){
        changeInfoIcon(param1);
        
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

function changeInfoIcon(param1){
  $("#"+param1).removeClass('btn btn-danger btn-circle');
  $("#"+param1).addClass('btn btn-success btn-circle');

  // $("#"+param1).removeClass('btn btn-success btn-circle');
  // $("#"+param2).removeClass('fa fa-check');
  // $("#"+param1).addClass('btn btn-danger btn-circle');
  // $("#"+param2).addClass('fa fa-times');
}

// Get Opening Status Technical
function getOpeningTechnical(){
$.ajax({
      type : "POST",
      url : "{{url('get_opening_technical')}}",
      data : {
        "id" : tender_id,
        "_token" : "{{csrf_token()}}"
      },
      success : function(data){
        data = JSON.parse(data);
        // console.log(data);
        if(data.length){

          $("#opening_status_tech_div").html('');
          counter_tech_query = data.length - 1;
          counter_tech_query_validate = data.length;
          $.each( data, function(key, value){


          $("#opening_status_tech_div").append('<div class="open_status_tech_part" id="open_status_tech_part_'+key+'">'+
                                              '<div class="row open_status_tech_div">'+
                                                '<div class="col-md-3">'+
                                                  '<div class="form-group">'+
                                                    '<label class="control-label">Query Detail</label>'+
                                                    '<input type="text" name="query_detail_tech['+key+']" id="query_detail_tech_'+key+'" class="form-control query_detail_tech" value="'+value.query_detail_tech+'">'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-3">'+
                                                  '<div class="form-group">'+
                                                    '<label class="control-label">Query Document</label>'+
                                                    '<input type="file" name="query_document_tech['+key+']" class="form-control query_document_tech" id="query_document_tech_'+key+'">'+
                                                    '<input type="hidden" name="query_document_tech_hidden['+key+']"  id="query_document_tech_hidden_'+key+'" value="'+value.query_document_tech+'">'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-3">'+
                                                  '<div class="form-group">'+
                                                    '<label class="control-label">Query Receive Date</label>'+
                                                    '<input type="text" name="query_receive_date_tech['+key+']" class="form-control query_receive_date_tech" id="query_receive_date_tech_'+key+'" value="'+value.query_receive_date_tech+'">'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-1" style="margin-top: 29px;">'+
                                                  '<div class="form-group">'+
                                                    '<a href="{{url('downloadtechQDdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
                                                  '</div>'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="row open_status_tech_div">'+
                                                '<div class="col-md-3">'+
                                                  '<div class="form-group">'+
                                                    '<label class="control-label">Query Reply</label>'+
                                                    '<textarea class="form-control query_reply_tech" name="query_reply_tech['+key+']" id="query_reply_tech_'+key+'">'+value.query_reply_tech+'</textarea>'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-3">'+
                                                  '<div class="form-group">'+
                                                    '<label class="control-label">Query Reply Document</label>'+
                                                    '<input type="file" name="query_reply_document_tech['+key+']" id="query_reply_document_tech_'+key+'" class="form-control query_reply_document_tech">'+
                                                    '<input type="hidden" name="query_reply_document_tech_hidden['+key+']" id="query_reply_document_tech_hidden_'+key+'" value="'+value.query_reply_document_tech+'">'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-3">'+
                                                  '<div class="form-group">'+
                                                    '<label class="control-label">Query Submission Date/Time</label>'+
                                                    '<input type="text" name="query_sub_date_tech['+key+']" id="query_sub_date_tech_'+key+'" class="form-control query_sub_date_tech" value="'+value.query_sub_date_tech+'">'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-1" style="margin-top: 29px;">'+
                                                  '<div class="form-group">'+
                                                    '<a href="{{url('downloadtechQRdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-2" style="margin-top: 29px;">'+
                                                  '<div class="form-group">'+
                                                    '<button type="button" class="btn btn-danger remove_query_tech"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                  '</div>'+
                                                '</div>'+
                                              '</div>'+
                                            '</div>');

            
          });

          jQuery('.query_receive_date_tech').datetimepicker({
            // format:'DD-MM-YYYY h:mm a',
            format:'DD-MM-YYYY',
            // minDate : new Date(),
          });
          jQuery('.query_sub_date_tech').datetimepicker({
                      format:'DD-MM-YYYY h:mm a',
                      // minDate : new Date(),
                });
        }else{

        }
      }
    });
}

// Get Opening Status Financial
function getOpeningFinancial(){
$.ajax({
      type : "POST",
      url : "{{url('get_opening_financial')}}",
      data : {
        "id" : tender_id,
        "_token" : "{{csrf_token()}}"
      },
      success : function(data){
        data = JSON.parse(data);
        // console.log(data);
        if(data.length){

          $("#opening_status_fina_div").html('');
          counter_fina_query = data.length - 1;
          counter_fina_query_validate = data.length;
          $.each( data, function(key, value){


          $("#opening_status_fina_div").append('<div class="open_status_fina_part" id="open_status_fina_part_'+key+'">'+
                                                '<div class="row open_status_fina_div">'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                      '<label class="control-label">Query Detail</label>'+
                                                      '<input type="text" name="query_detail_fina['+key+']" id="query_detail_fina_'+key+'" class="form-control query_detail_fina" value="'+value.query_detail_fina+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                      '<label class="control-label">Query Document</label>'+
                                                      '<input type="file" name="query_document_fina['+key+']" class="form-control query_document_fina" id="query_document_fina_'+key+'">'+
                                                      '<input type="hidden" name="query_document_fina_hidden['+key+']" id="query_document_fina_hidden_'+key+'" value="'+value.query_document_fina+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                      '<label class="control-label">Query Receive Date</label>'+
                                                      '<input type="text" name="query_receive_date_fina['+key+']" class="form-control query_receive_date_fina" id="query_receive_date_fina_'+key+'" value="'+value.query_receive_date_fina+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-1" style="margin-top: 29px;">'+
                                                    '<div class="form-group">'+
                                                      '<a href="{{url('downloadfinaQDdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
                                                    '</div>'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="row open_status_fina_div">'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                      '<label class="control-label">Query Reply</label>'+
                                                      '<textarea class="form-control query_reply_fina" name="query_reply_fina['+key+']" id="query_reply_fina_'+key+'">'+value.query_reply_fina+'</textarea>'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                      '<label class="control-label">Query Reply Document</label>'+
                                                      '<input type="file" name="query_reply_document_fina['+key+']" id="query_reply_document_fina_'+key+'" class="form-control query_reply_document_fina">'+
                                                      '<input type="hidden" name="query_reply_document_fina_hidden['+key+']" id="query_reply_document_fina_hidden_'+key+'" value="'+value.query_reply_document_fina+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-3">'+
                                                    '<div class="form-group">'+
                                                      '<label class="control-label">Query Submission Date/Time</label>'+
                                                      '<input type="text" name="query_sub_date_fina['+key+']" id="query_sub_date_fina_'+key+'" class="form-control query_sub_date_fina" value="'+value.query_sub_date_fina+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-1" style="margin-top: 29px;">'+
                                                    '<div class="form-group">'+
                                                      '<a href="{{url('downloadfinaQRdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-2" style="margin-top: 29px;">'+
                                                  '<div class="form-group">'+
                                                    '<button type="button" class="btn btn-danger remove_query_fina"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                  '</div>'+
                                                '</div>'+
                                                '</div>'+
                                              '</div>');

            
          });

          jQuery('.query_receive_date_fina').datetimepicker({
            // format:'DD-MM-YYYY h:mm a',
            format:'DD-MM-YYYY',
            // minDate : new Date(),
          });
          jQuery('.query_sub_date_fina').datetimepicker({
                      format:'DD-MM-YYYY h:mm a',
                      // minDate : new Date(),
                });
        }else{

        }
      }
    });
}

</script>
@endsection
