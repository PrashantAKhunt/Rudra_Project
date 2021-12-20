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
                                <button type="button" class="btn btn-danger btn-circle"></button>
                                <b>No Action</b>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-warning btn-circle"></button>
                                <b>Under Progress</b>
                            </div>
                            <!--<div class="col-md-3">
                                <button type="button" class="btn btn-info btn-circle"></button>
                                <b>Documents Uploaded</b>
                            </div>-->
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success btn-circle"></button>
                                <b>Completed</b>
                            </div>
                            <br><br>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Submission of Priliminary Part 
                                    
                                    @if($tender['tender_fee'] == "Yes")
                                      @if($tender['tender_fee_check_complated'] == "1")
                                      <button type="button" class="btn btn-success btn-circle" id="tender_fee_info"></button>
                                    @else
                                      <button type="button" class="btn btn-danger btn-circle" id="tender_fee_info"></button>
                                    @endif
                                    @elseif($tender['tender_fee'] == "No")
                                      <button type="button" class="btn btn-success btn-circle" id="tender_emd_info"></button>
                                    @else
                                      <button type="button" class="btn btn-danger btn-circle" id="tender_emd_info"></button>
                                    @endif

                                    @if($tender['tender_emd'] == "Yes")
                                      @if($tender['tender_emd_check_complated'] == "1")
                                        <button type="button" class="btn btn-success btn-circle" id="tender_emd_info"></button>
                                      @else
                                        <button type="button" class="btn btn-danger btn-circle" id="tender_emd_info"></button>
                                      @endif
                                    @elseif($tender['tender_emd'] == "No")
                                      <button type="button" class="btn btn-success btn-circle" id="tender_emd_info"></button>
                                    @else
                                      <button type="button" class="btn btn-danger btn-circle" id="tender_emd_info"></button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                      @if($tender['tender_fee'] == "Yes")
                                      <form method="post" id="priliminary_form_fee" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <input type="hidden" name="form_name" value="fee">
                                      <h3 class="">Tender Fee Submission</h3>
                                      <div class="row">
                                        <div class="col-md-2">
                                          <div class="form-group">
                                            <label class="control-label">Check If Completed </label>
                                            <input type="checkbox" class="" name="tender_fee_check_complated" id="tender_fee_check_complated" {{$tender['tender_fee_check_complated'] == "1" ? "checked" : ""}}>
                                          </div>
                                        </div>
                                        <div class="col-md-3">
                                          <div class="form-group">
                                            <label class="control-label">Tender Fee Amount</label>
                                            <input type="text" class="form-control tender_fee_amount" name="tender_fee_amount" id="tender_fee_amount" value="{{$tender['tender_fee_amount']}}" readonly="readonly">
                                          </div>
                                        </div>
                                        <div class="col-md-3">
                                          <div class="form-group">
                                            <label class="control-label">Tender Fee Status</label>
                                            <select class="form-control tender_fee_status" name="tender_fee_status" id="tender_fee_status">
                                              <option value="Pending" >Pending</option>
                                              <option value="Uploaded" {{$tender['tender_fee_status'] == "Uploaded" ? "selected" : ""}}>Uploaded</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="col-md-3">
                                          <div class="form-group">
                                            <label class="control-label">Upload Document <span class="error">*</span></label>
                                            <input type="file" class="form-control tender_fee_attechment {{$tender['tender_fee_attechment'] ? "" : "required"}}" name="tender_fee_attechment" id="tender_fee_attechment">
                                            <input type="hidden" name="tender_fee_attechment_hidden" id="tender_fee_attechment_hidden" value="{{$tender['tender_fee_attechment']}}">
                                          </div>
                                        </div>
                                        <div class="col-md-1" style="padding-top: 30px;">
                                            <a href="{{url('downloadFeeDoc')}}/{{$tender_id}}" target="_blank" class="btn btn-primary btn-circle downloadFeeDoc" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                        </div>
                                      </div>
                                      @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                      <button type="button" class="btn btn-success priliminary_form_fee_btn">Save</button>
                                      @endif
                                      <hr>
                                      </form>
                                      @elseif($tender['tender_fee'] == "No")
                                      <h3>Tender Fee Not Required</h3>
                                      @else
                                      <h3>Tender Fee Not Added</h3>
                                      @endif
                                      @if($tender['tender_emd'] == "Yes")
                                      <form method="post" id="priliminary_form_emd" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <input type="hidden" name="form_name" value="emd">
                                        <h3 class="">Tender EMD Submission</h3>
                                      <div class="row">
                                        <div class="col-md-2">
                                          <div class="form-group">
                                            <label class="control-label">Check If Completed </label>
                                            <input type="checkbox" class="" name="tender_emd_check_complated" id="tender_emd_check_complated" {{$tender['tender_emd_check_complated'] == "1" ? "checked" : ""}}>
                                          </div>
                                        </div>
                                        <div class="col-md-3">
                                          <div class="form-group">
                                            <label class="control-label">Tender EMD Amount</label>
                                            <input type="text" class="form-control tender_emd" name="tender_emd_amount" id="tender_emd_amount" value="{{$tender['tender_emd_amount']}}" readonly="readonly">
                                          </div>
                                        </div>
                                        <div class="col-md-3">
                                          <div class="form-group">
                                            <label class="control-label">Tender EMD Status</label>
                                            <select class="form-control tender_emd_status" name="tender_emd_status" id="tender_emd_status">
                                              <option value="Pending" >Pending</option>
                                              <option value="Uploaded" {{$tender['tender_emd_status'] == "Uploaded" ? "selected" : ""}}>Uploaded</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="col-md-3">
                                          <div class="form-group">
                                            <label class="control-label">Upload Document <span class="error">*</span></label>
                                            <input type="file" class="form-control tender_emd_attechment {{$tender['tender_emd_attechment'] ? "" : "required"}}" name="tender_emd_attechment" id="tender_emd_attechment">
                                            <input type="hidden" name="tender_emd_attechment_hidden" id="tender_emd_attechment_hidden" value="{{$tender['tender_emd_attechment']}}">
                                          </div>
                                        </div>
                                        <div class="col-md-1" style="padding-top: 30px;">
                                            <a href="{{url('downloadEmdDoc')}}/{{$tender_id}}" target="_blank" class="btn btn-primary btn-circle downloadEmdDoc" data-toggle="tooltip" data-placement="top" title="Download Document"><i class="fa fa-download"></i></a>
                                        </div>
                                      </div>
                                      @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                      <button type="button" class="btn btn-success priliminary_form_emd_btn">Save</button>
                                      @endif
                                      </form>
                                      @elseif($tender['tender_emd'] == "No")
                                      <h3>Tender Emd Not Required</h3>
                                      @else
                                      <h3>Tender Emd Not Added</h3>
                                      @endif
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Submission of Technical Part  
                                    <button type="button" class="btn btn-danger btn-circle" id="tender_sub_tech_info"></button>
                                    <button type="button" class="btn btn-danger btn-circle" id="tender_sub_tech_info1"></button>
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true" style="">
                                    <div class="panel-body">
                                      <h3>Prepration of Documents</h3>
                                      <form method="post" id="prepare_document_form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div id="prepare_document_div">
                                          @if($tender_submission_technical_prepare_count)
                                            {{-- @foreach($tender_submission_technical_prepare as $key => $value) --}}
                                              {{-- <div class="prepare_document_technical" id="prepare_document_technical_{{$key}}">
                                                <div class="row">
                                                  <div class="col-md-1" style="padding-top: 34px;">
                                                    <div class="form-group">
                                                      <input type="checkbox" class="prepare_document_checked" {{$value->prepare_document_checked == 1 ? "checked" : ""}} name="prepare_document_checked[]" id="prepare_document_checked_{{$key}}">
                                                      <input type="hidden" name="prepare_document_tech_id[]" id="prepare_document_tech_id_{{$key}}" value="{{$value->id}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-4">
                                                    <div class="form-group">
                                                      <label class="control-label"> Document Name <span class="error">*</span> </label>
                                                      <input type="text" class="form-control prepare_document_name" name="prepare_document_name[]" id="prepare_document_name_{{$key}}" value="{{$value->prepare_document_name}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-4">
                                                    <div class="form-group">
                                                      <label class="control-label"> Document Attechment <span class="error">*</span> </label>
                                                      <input type="file" class="form-control prepare_document_attechment" name="prepare_document_attechment[]" id="prepare_document_attechment_{{$key}}">
                                                      <input type="hidden" name="prepare_document_attechment_hidden[]" id="prepare_document_attechment_hidden_{{$key}}" value="{{$value->prepare_document_attechment}}">
                                                    </div>
                                                  </div>
                                                  <div class="col-md-2" style="padding-top: 30px;">
                                                    <button type="button" class="btn btn-danger remove_prepare_tech_part_detele" id="{{$value->id}}"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                  </div>
                                                </div>
                                              </div> --}}
                                            {{-- @endforeach --}}
                                          @else
                                            <div class="prepare_document_technical" id="prepare_document_technical_0">
                                              <div class="row">
                                                <div class="col-md-1" style="padding-top: 34px;">
                                                  <div class="form-group">
                                                    <input type="checkbox" class="prepare_document_checked" name="prepare_document_checked[]" id="prepare_document_checked_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label class="control-label"> Document Name <span class="error">*</span> </label>
                                                    <input type="text" class="form-control prepare_document_name" name="prepare_document_name[]" id="prepare_document_name_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label class="control-label"> Document Attechment <span class="error">*</span> </label>
                                                    <input type="file" class="form-control prepare_document_attechment" name="prepare_document_attechment[]" id="prepare_document_attechment_0">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          @endif
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                              <div class="col-sm-6"></div> 
                                              <div class="col-sm-4"></div>  
                                              <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-primary add_more" onclick="addMorePrepareTech()"><i class="fa fa-plus"></i> Add More</button>
                                                    </div>
                                                </div>
                                          </div>
                                          @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                        <button type="button" class="btn btn-success prepare_document_form_btn">Save</button>
                                        @endif
                                      </form>
                                      <hr>
                                    <h3>Uploaded of Documents</h3>
                                      <form method="post" id="uploaded_document_form" enctype="multipart/form-data">
                                        @csrf
                                          <input type="hidden" name="id" value="{{$tender_id}}">
                                          <div id="uploaded_document_div">
                                            <div class="uploaded_document_technical" id="uploaded_document_technical_0">
                                              <div class="row">
                                                <div class="col-md-1" style="padding-top: 34px;">
                                                  <div class="form-group">
                                                    <input type="checkbox" class="uploaded_document_checked" name="uploaded_document_checked[]" id="uploaded_document_checked_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label class="control-label"> Document Name</label>
                                                    <input type="text" class="form-control uploaded_document_name" name="uploaded_document_name[]" id="uploaded_document_name_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label class="control-label"> Document Attachment</label>
                                                    <input type="file" class="form-control uploaded_document_attechment" name="uploaded_document_attechment[]" id="uploaded_document_attechment_0">
                                                  </div>
                                                </div>
                                                {{-- <div class="col-md-2" style="padding-top: 30px;">
                                                  <button class="btn btn-primary btn-circle"> <i class="fa fa-download" aria-hidden="true"></i></button>
                                                </div> --}}
                                              </div>
                                            </div>
                                          </div>
                                          {{-- <div class="row" style="padding-top: 10px;">
                                                <div class="col-sm-6"></div> 
                                                <div class="col-sm-4"></div>  
                                                <div class="col-sm-2">
                                                      <div class="form-group">
                                                          <button type="button" class="btn btn-primary add_more" onclick="addMoreUploadTech()"><i class="fa fa-plus"></i> Add More</button>
                                                      </div>
                                                  </div>
                                            </div> --}}
                                        @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                        <button type="button" class="btn btn-success uploaded_document_form_btn">Save</button>
                                        @endif
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Submission of Financial Part 
                                    <button type="button" class="btn btn-danger btn-circle" id="tender_sub_fina_info"></button>
                                    <button type="button" class="btn btn-danger btn-circle" id="tender_sub_fina_info1"></button>
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true" style="">
                                    <div class="panel-body">
                                      <h3>Preparation of Documents</h3>
                                      <form method="post" id="prepare_document_financial_form" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <div id="prepare_document_financial_div">
                                        @if($tender_submission_financial_prepare_count)
                                          {{-- @foreach($tender_submission_financial_prepare as $key => $value) --}}
                                            {{-- <div class="prepare_document_financial" id="prepare_document_financial_0">
                                              <div class="row">
                                                <div class="col-md-1" style="padding-top: 34px;">
                                                  <div class="form-group">
                                                    <input type="checkbox" class="prepare_document_checked_fina" {{$value->prepare_document_checked == 1 ? "checked" : "" }}  name="prepare_document_checked_fina[]" id="prepare_document_checked_fina_0">

                                                    <input type="hidden" name="prepare_document_fina_id[]" id="prepare_document_fina_id_{{$key}}" value="{{$value->id}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label class="control-label"> Document Name</label>
                                                    <input type="text" class="form-control prepare_document_name_fina" name="prepare_document_name_fina[]" id="prepare_document_name_fina_0" value="{{$value->prepare_document_name}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label class="control-label"> Document Attechment</label>
                                                    <input type="file" class="form-control prepare_document_attechment_fina" name="prepare_document_attechment_fina[]" id="prepare_document_attechment_fina_0">

                                                    <input type="hidden" name="prepare_document_attechment_fina_hidden[]" id="prepare_document_attechment_fina_hidden_{{$key}}" value="{{$value->prepare_document_attechment}}">
                                                  </div>
                                                </div>
                                                <div class="col-md-2" style="padding-top: 30px;">
                                                  <button type="button" class="btn btn-danger remove_prepare_fina_part_delete" id="{{$value->id}}"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                </div>
                                              </div>
                                            </div> --}}
                                          {{-- @endforeach --}}
                                        @else
                                          <div class="prepare_document_financial" id="prepare_document_financial_0">
                                            <div class="row">
                                              <div class="col-md-1" style="padding-top: 34px;">
                                                <div class="form-group">
                                                  <input type="checkbox" class="prepare_document_checked_fina" name="prepare_document_checked_fina[]" id="prepare_document_checked_fina_0">
                                                </div>
                                              </div>
                                              <div class="col-md-4">
                                                <div class="form-group">
                                                  <label class="control-label"> Document Name<span class="error">*</span></label>
                                                  <input type="text" class="form-control prepare_document_name_fina" name="prepare_document_name_fina[]" id="prepare_document_name_fina_0">
                                                </div>
                                              </div>
                                              <div class="col-md-4">
                                                <div class="form-group">
                                                  <label class="control-label"> Document Attechment<span class="error">*</span></label>
                                                  <input type="file" class="form-control prepare_document_attechment_fina" name="prepare_document_attechment_fina[]" id="prepare_document_attechment_fina_0">
                                                </div>
                                              </div>
                                              {{-- <div class="col-md-2" style="padding-top: 30px;">
                                                <button class="btn btn-primary btn-circle"> <i class="fa fa-download" aria-hidden="true"></i> </button>
                                              </div> --}}
                                            </div>
                                          </div>
                                        @endif
                                          
                                      </div>
                                      <div class="row" style="padding-top: 10px;">
                                            <div class="col-sm-6"></div> 
                                            <div class="col-sm-4"></div>  
                                            <div class="col-sm-2">
                                                  <div class="form-group">
                                                      <button type="button" class="btn btn-primary" onclick="addMorePrepareFina()"><i class="fa fa-plus"></i> Add More</button>
                                                  </div>
                                              </div>
                                        </div>
                                        @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                      <button type="button" class="btn btn-success prepare_document_financial_form_btn">Save</button>
                                      @endif
                                      </form>
                                      <hr>
                                    <h3>Uploaded of Documents</h3>
                                    <form method="post" id="uploaded_document_financial_form" enctype="multipart/form-data">
                                      @csrf
                                      <input type="hidden" name="id" value="{{$tender_id}}">
                                      <div id="uploaded_document_financial_div">
                                        <div class="uploaded_document_financial" id="uploaded_document_financial_0">
                                            <div class="row">
                                              <div class="col-md-1" style="padding-top: 34px;">
                                                <div class="form-group">
                                                  <input type="checkbox" class="uploaded_document_checked_fina" name="uploaded_document_checked_fina[]" id="uploaded_document_checked_fina_0">
                                                </div>
                                              </div>
                                              <div class="col-md-4">
                                                <div class="form-group">
                                                  <label class="control-label"> Document Name<span class="error">*</span></label>
                                                  <input type="text" class="form-control uploaded_document_name_fina" name="uploaded_document_name_fina[]" id="uploaded_document_name_fina_0">
                                                </div>
                                              </div>
                                              <div class="col-md-4">
                                                <div class="form-group">
                                                  <label class="control-label"> Document Attechment<span class="error">*</span></label>
                                                  <input type="file" class="form-control uploaded_document_attechment_fina" name="uploaded_document_attechment_fina[]" id="uploaded_document_attechment_fina_0">
                                                </div>
                                              </div>
                                            </div>
                                        </div>
                                      </div>
                                      {{-- <div class="row" style="padding-top: 10px;">
                                            <div class="col-sm-6"></div> 
                                            <div class="col-sm-4"></div>  
                                            <div class="col-sm-2">
                                                  <div class="form-group">
                                                      <button type="button" class="btn btn-primary" onclick="addMoreUploadFina()"><i class="fa fa-plus"></i> Add More</button>
                                                  </div>
                                              </div>
                                        </div> --}}
                                    @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                    <button type="button" class="btn btn-success uploaded_document_financial_form_btn">Save</button>
                                    @endif
                                    </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Submission of Commercial Part (BOQ) 
                                    <button type="button" class="btn btn-danger btn-circle" id="tender_sub_boq_info"></button>
                                    <button type="button" class="btn btn-danger btn-circle" id="tender_sub_boq_info1"></button>
                                    
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true" style="">
                                    <div class="panel-body">
                                      <h3>Preparation of Documents</h3>
                                      <form method="post" id="prepare_document_boq_form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div id="prepare_document_boq_div">
                                          @if($tender_submission_commercial_count)
                                              {{-- @foreach($tender_submission_commercial as $key => $value) --}}
                                                {{-- <div class="prepare_document_boq" id="prepare_document_boq_{{$key}}">
                                                  <div class="row">
                                                    <div class="col-md-1" style="padding-top: 34px;">
                                                      <div class="form-group">
                                                        <input type="checkbox" class="prepare_document_checked_boq" {{$value->prepare_document_checked == 1 ? "checked" : "" }} name="prepare_document_checked_boq[{{$key}}]" id="prepare_document_checked_boq_{{$key}}">

                                                        <input type="hidden" name="prepare_document_boq_id[]" id="prepare_document_boq_id_{{$key}}" value="{{$value->id}}">
                                                      </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                      <div class="form-group">
                                                        <label class="control-label"> Document Name <span class="error">*</span></label>
                                                        <input type="text" class="form-control prepare_document_name_boq" name="prepare_document_name_boq[{{$key}}]" id="prepare_document_name_boq_{{$key}}" value="{{$value->prepare_document_name}}">
                                                      </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                      <div class="form-group">
                                                        <label class="control-label"> Document Attechment<span class="error">*</span></label>
                                                        <input type="file" class="form-control prepare_document_attechment_boq" name="prepare_document_attechment_boq[{{$key}}]" id="prepare_document_attechment_boq_{{$key}}">

                                                        <input type="hidden" name="prepare_document_attechment_boq_hidden[]" id="prepare_document_attechment_boq_hidden_{{$key}}" value="{{$value->prepare_document_attechment}}">
                                                      </div>
                                                    </div>
                                                    <div class="col-md-2" style="padding-top: 30px;">
                                                      <button type="button" class="btn btn-danger remove_prepare_boq_part_delete" id="{{$value->id}}"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>
                                                    </div>
                                                  </div>
                                                </div> --}}
                                              {{-- @endforeach --}}
                                          @else
                                            <div class="prepare_document_boq" id="prepare_document_boq_0">
                                              <div class="row">
                                                <div class="col-md-1" style="padding-top: 34px;">
                                                  <div class="form-group">
                                                    <input type="checkbox" class="prepare_document_checked_boq" name="prepare_document_checked_boq[]" id="prepare_document_checked_boq_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label class="control-label"> Document Name<span class="error">*</span></label>
                                                    <input type="text" class="form-control prepare_document_name_boq" name="prepare_document_name_boq[]" id="prepare_document_name_boq_0">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label class="control-label"> Document Attechment<span class="error">*</span></label>
                                                    <input type="file" class="form-control prepare_document_attechment_boq" name="prepare_document_attechment_boq[]" id="prepare_document_attechment_boq_0">
                                                  </div>
                                                </div>
                                                {{-- <div class="col-md-2" style="padding-top: 30px;">
                                                  <button class="btn btn-primary btn-circle"> <i class="fa fa-download" aria-hidden="true"></i> </button>
                                                </div> --}}
                                              </div>
                                            </div>
                                          @endif
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-sm-6"></div> 
                                            <div class="col-sm-4"></div>  
                                            <div class="col-sm-2">
                                                  <div class="form-group">
                                                      <button type="button" class="btn btn-primary add_more" onclick="addMorePrepareBoq()"><i class="fa fa-plus"></i> Add More</button>
                                                  </div>
                                              </div>
                                        </div>
                                        @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                        <button type="button" class="btn btn-success prepare_document_boq_form_btn">Save</button>
                                        @endif
                                      </form>
                                      <hr>
                                      <h3>Uploaded of Documents</h3>
                                      <form method="post" id="uploaded_document_boq_form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div id="uploaded_document_boq_div">
                                          <div class="uploaded_document_boq" id="uploaded_document_boq_0">
                                            <div class="row">
                                              <div class="col-md-1" style="padding-top: 34px;">
                                                <div class="form-group">
                                                  <input type="checkbox" class="uploaded_document_checked_boq" name="uploaded_document_checked_boq[]" id="uploaded_document_checked_boq_0">
                                                </div>
                                              </div>
                                              <div class="col-md-4">
                                                <div class="form-group">
                                                  <label class="control-label"> Document Name<span class="error">*</span></label>
                                                  <input type="text" class="form-control uploaded_document_name_boq" name="uploaded_document_name_boq[]" id="uploaded_document_name_boq_0">
                                                </div>
                                              </div>
                                              <div class="col-md-4">
                                                <div class="form-group">
                                                  <label class="control-label"> Document Attechment<span class="error">*</span></label>
                                                  <input type="file" class="form-control uploaded_document_attechment_boq" name="uploaded_document_attechment_boq[]" id="uploaded_document_attechment_boq_0">
                                                </div>
                                              </div>
                                              {{-- <div class="col-md-2" style="padding-top: 30px;">
                                                <button class="btn btn-primary btn-circle"> <i class="fa fa-download" aria-hidden="true"></i> </button>
                                              </div> --}}
                                            </div>
                                          </div>
                                        </div>
                                        {{-- <div class="row" style="padding-top: 10px;">
                                            <div class="col-sm-6"></div> 
                                            <div class="col-sm-4"></div>  
                                            <div class="col-sm-2">
                                                  <div class="form-group">
                                                      <button type="button" class="btn btn-primary add_more" onclick="addMoreUploadedBoq()"><i class="fa fa-plus"></i> Add More</button>
                                                  </div>
                                              </div>
                                        </div> --}}
                                        @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                      <button type="button" class="btn btn-success uploaded_document_boq_form_btn">Save</button>
                                      @endif
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                  <div class="panel-heading"> Final Submission of Tender 

                                    @if($tender['final_sub_status'])
                                      <button type="button" class="btn btn-success btn-circle" id="final_sub_info"></button>
                                    @else
                                      <button type="button" class="btn btn-danger btn-circle" id="final_sub_info"></button>
                                    @endif
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                  </div>
                                  <div class="panel-wrapper collapse" aria-expanded="true" style="">
                                    <div class="panel-body">
                                      <form method="post" id="final_sub_form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$tender_id}}">
                                        <div class="row">
                                          <div class="col-md-3">
                                            <div class="form-group">
                                              <label class="control-label">Submission Status<span class="error">*</span></label>
                                              <select class="form-control required final_sub_status" name="final_sub_status" id="final_sub_status">
                                                <option value="">Select</option>
                                                <option value="Yes" {{$tender['final_sub_status'] == "Yes" ? "selected" : ""}}>Yes</option>
                                                <option value="No" {{$tender['final_sub_status'] == "No" ? "selected" : ""}}>No</option>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="col-md-3 final_sub_div" style="display: none;">
                                            <div class="form-group">
                                              <label class="control-label">Submission Number<span class="error">*</span></label>
                                              <input type="number" class="form-control required final_sub_number" name="final_sub_number" id="final_sub_number" value="{{$tender['final_sub_number']}}">
                                            </div>
                                          </div>
                                          <div class="col-md-3 final_sub_div" style="display: none;">
                                            <div class="form-group">
                                              <label class="control-label">Submission DateTime<span class="error">*</span></label>
                                              <input type="text" class="form-control required final_sub_date_time" name="final_sub_date_time" id="final_sub_date_time" value="{{$tender['final_sub_date_time'] != "" ? date('d-m-Y H:i a',strtotime($tender['final_sub_date_time'])) : ""}}">
                                            </div>
                                          </div>
                                          <div class="col-md-2 final_sub_div" style="display: none;">
                                            <div class="form-group">
                                              <label class="control-label">Submission File<span class="error">*</span></label>
                                              <input type="file" class="form-control final_sub_file {{$tender['final_sub_file'] ? "" : "required"}}" name="final_sub_file" id="final_sub_file">
                                              <input type="hidden" name="final_sub_file_hidden" id="final_sub_file_hidden" value="{{$tender['final_sub_file']}}">
                                            </div>
                                          </div>
                                          <div class="col-md-1 final_sub_div" style="padding-top: 30px;display: none;">
                                            <a href="{{url('downloadfinalsubdoc')}}/{{$tender_id}}" target="_blank" class="btn btn-primary btn-circle downloadfinalsubdoc" data-toggle="tooltip" data-placement="top" title="Download Document"> <i class="fa fa-download" aria-hidden="true"></i></a>
                                          </div>
                                        </div>
                                        @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                        <button type="button" class="btn btn-success final_sub_form_btn">Save</button>
                                        @endif
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
var counter_prepare_tech = {{$tender_submission_technical_prepare_count}};
var counter_prepare_tech_validate = {{$tender_submission_technical_prepare_count}};
var counter_upload_tech = 0;

var counter_prepare_fina = {{$tender_submission_financial_prepare_count}};
var counter_prepare_fina_validate = {{$tender_submission_financial_prepare_count}};
var counter_upload_fina = 0;

var counter_prepare_boq = {{$tender_submission_commercial_count}};
var counter_prepare_boq_validate = {{$tender_submission_commercial_count}};
var counter_upload_boq = 0;

var check_final_doc = "{{$tender['final_sub_status']}}";
var check_tender_fee_attechment = "{{$tender['tender_fee_attechment']}}";
var check_tender_emd_attechment = "{{$tender['tender_emd_attechment']}}";
var tender_id = "{{$tender_id}}";
$(document).ready(function(){
$('#final_sub_status').trigger('change');

getSubmissionTenderTech();
getSubmissionTenderFina();
getSubmissionTenderBoq();


checkfinaldocDoc(check_final_doc);
checkTenderFeeAttechment(check_tender_fee_attechment);
checkTenderEmdAttechment(check_tender_emd_attechment);
});

// ------------------ Submission Priliminary Start --------------

//Fee

function checkTenderFeeAttechment(check_tender_fee_attechment){
  if(check_tender_fee_attechment != ""){
    $(".downloadFeeDoc").show();
  }else{
    $(".downloadFeeDoc").hide();
  }
}

$(".priliminary_form_fee_btn").on('click',function(){
  if($("#priliminary_form_fee").valid()){
    $(".priliminary_form_fee_btn").attr("disabled", true);
    var form = $('#priliminary_form_fee')[0];
    var formData1 = new FormData(form);

    formData1.append('tender_fee_attechment', $('.tender_fee_attechment')[0].files[0]);

      $.ajax({
        type : "POST",
        url : "{{url('save_tender_priliminary')}}",
        data : formData1,
        processData: false,
        contentType: false,
        success : function(data){
          console.log(data);
          $(".priliminary_form_fee_btn").attr("disabled", false);
          if(data == "success"){
            checkTenderFeeAttechment("1");
            if($("#tender_fee_check_complated").prop("checked") == true){
                $("#tender_fee_info").removeClass('btn btn-danger btn-circle');
                $("#tender_fee_info").addClass('btn btn-success btn-circle');
            }else{
                $("#tender_fee_info").removeClass('btn btn-success btn-circle');
                $("#tender_fee_info").addClass('btn btn-danger btn-circle');
            }
          swal("Tender detail save successfully.", "", "success");
          }else{
              swal({
                  title: "Tender detail not save try again.",
                  //text: "You want to change status of admin user.",
                  type: "error",
                  showCancelButton: false,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "OK",
                  closeOnConfirm: true
              });
          }
        }
      })
  }
})

//EMD
function checkTenderEmdAttechment(check_tender_emd_attechment){
  if(check_tender_emd_attechment != ""){
    $(".downloadEmdDoc").show();
  }else{
    $(".downloadEmdDoc").hide();
  }
}
$(".priliminary_form_emd_btn").on('click',function(){
  if($("#priliminary_form_emd").valid()){
    $(".priliminary_form_emd_btn").attr("disabled", true);
    var form = $('#priliminary_form_emd')[0];
    var formData1 = new FormData(form);

    formData1.append('tender_emd_attechment', $('.tender_emd_attechment')[0].files[0]);

      $.ajax({
        type : "POST",
        url : "{{url('save_tender_priliminary')}}",
        data : formData1,
        processData: false,
        contentType: false,
        success : function(data){
          console.log(data);
          $(".priliminary_form_emd_btn").attr("disabled", false);
          if(data == "success"){
            checkTenderEmdAttechment("1");
            if($("#tender_emd_check_complated").prop("checked") == true){
                $("#tender_emd_info").removeClass('btn btn-danger btn-circle');
                $("#tender_emd_info").addClass('btn btn-success btn-circle');
            }else{
                $("#tender_emd_info").removeClass('btn btn-success btn-circle');
                $("#tender_emd_info").addClass('btn btn-danger btn-circle');
            }
          swal("Tender detail save successfully.", "", "success");
          }else{
              swal({
                  title: "Tender detail not save try again.",
                  //text: "You want to change status of admin user.",
                  type: "error",
                  showCancelButton: false,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "OK",
                  closeOnConfirm: true
              });
          }
        }
      })
  }
})

// ------------------ Submission Priliminary End ----------------


// ---------------- Final Submission Tender Start ---------------------
jQuery('.final_sub_date_time').datetimepicker({
            format:'DD-MM-YYYY h:mm a',
      });

$('#final_sub_status').on('change', function(){
    var check_val = $(this).val();
    if(check_val == "Yes"){
        $(".final_sub_div").show();
    }else{
        $(".final_sub_div").hide();
    }    
});


function checkfinaldocDoc(check_final_doc){
  if(check_final_doc == "Yes"){
    $(".downloadfinalsubdoc").show();
  }else{
    $(".downloadfinalsubdoc").hide();
  }  
}

$(".final_sub_form_btn").on('click',function(){
  if($("#final_sub_form").valid()){
    $(".final_sub_form_btn").attr("disabled", true);
    var form = $('#final_sub_form')[0];
    var formData1 = new FormData(form);

    formData1.append('final_sub_file', $('.final_sub_file')[0].files[0]);

      $.ajax({
        type : "POST",
        url : "{{url('save_tender_submission')}}",
        data : formData1,
        processData: false,
        contentType: false,
        success : function(data){
          // console.log(sudata);
          $(".final_sub_form_btn").attr("disabled", false);
          if(data == "success"){
          $("#final_sub_info").removeClass('btn btn-danger btn-circle');
          $("#final_sub_info").addClass('btn btn-success btn-circle');
          //final_sub_status
          if($("#final_sub_status").val() == "Yes"){
              checkfinaldocDoc("Yes");
          }else{
              checkfinaldocDoc("No");
          }            
          /*swal({
                  title: "Tender submitted successfully.",
                  //text: "You want to change status of admin user.",
                  type: "info",
                  showCancelButton: false,
                  confirmButtonColor: "#006600",
                  confirmButtonText: "Okay",
                  closeOnConfirm: true
              });*/
          swal("Tender submitted successfully.", "", "success");
          }else{
              swal({
                  title: "Tender not submit try again.",
                  //text: "You want to change status of admin user.",
                  type: "error",
                  showCancelButton: false,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "OK",
                  closeOnConfirm: true
              });
          }
        }
      })
  }
});
// ---------------- Final Submission Tender End -----------------------


// ---------- Submission Technical Part Start ----------------------

//Prepare part start --
function addMorePrepareTech(){
 counter_prepare_tech += 1;

//add
$("#prepare_document_div").append('<div class="prepare_document_technical" id="prepare_document_technical_'+counter_prepare_tech+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-1" style="padding-top: 34px;">'+
                                                '<div class="form-group">'+
                                                  '<input type="checkbox" class="prepare_document_checked" name="prepare_document_checked['+counter_prepare_tech+']" id="prepare_document_checked_'+counter_prepare_tech+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Name <span class="error">*</span> </label>'+
                                                  '<input type="text" class="form-control prepare_document_name" name="prepare_document_name['+counter_prepare_tech+']" id="prepare_document_name_'+counter_prepare_tech+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Attechment <span class="error">*</span> </label>'+
                                                  '<input type="file" class="form-control prepare_document_attechment" name="prepare_document_attechment['+counter_prepare_tech+']" id="prepare_document_attechment_'+counter_prepare_tech+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="padding-top: 30px;">'+
                                                '<button type="button" class="btn btn-danger remove_prepare_tech_part"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');
}

//Remove
$("body").on("click",".remove_prepare_tech_part",function(){
      $(this).parents(".prepare_document_technical").remove();
});
//delete
$("body").on("click",".remove_prepare_tech_part_detele",function(){
  var remove_tech_delete_id = $(this).attr('id');
      $(this).parents(".prepare_document_technical").remove();
      $.ajax({
            type : "POST",
            url : "{{url('delete_prepare_technical_file')}}",
            data: {
              "_token": "{{ csrf_token() }}",
              "id": remove_tech_delete_id
            },
            success : function(data){
              console.log(data);
              getSubmissionTenderTech();
            }
        });
});

//Add data
$(".prepare_document_form_btn").on('click',function(){
  $(".prepare_document_name").each(function(){
    $(this).rules('add',{
        required :true
    });
  });
  $(".prepare_document_attechment").each(function(){
    $(this).rules('add',{
        required :true
    });
  });

  for(j = 0 ; j < counter_prepare_tech_validate ; j++){
    $("#prepare_document_attechment_"+j).removeClass("error").rules("remove");
  }

  if($("#prepare_document_form").valid()){
    $(".prepare_document_form_btn").attr("disabled", true);
    var form = $('#prepare_document_form')[0];
        var formData = new FormData(form);
          

        if(counter_prepare_tech == 0){
            formData.append('prepare_document_attechment[]', $('.prepare_document_attechment')[0].files[0]);
        }else{
          for(i = 0; i >= counter_prepare_tech ; i++){
              formData.append('prepare_document_attechment', $('.prepare_document_attechment')[1].files[1]);  
          }  
        }
          
        $.ajax({
          type : "POST",
          url : "{{url('tender_sub_prepare_tech')}}",
          data : formData,
          processData: false,
          contentType: false,
          success : function(data){
            // console.log(data);
            $(".prepare_document_form_btn").attr("disabled", false);
            if(data == "success"){
            $("#tender_sub_tech_info").removeClass('btn btn-danger btn-circle');
            $("#tender_sub_tech_info").addClass('btn btn-warning btn-circle');
            getSubmissionTenderTech();  
                /*swal({
                    title: "Tender detail save successfully.",
                    //text: "You want to change status of admin user.",
                    type: "info",
                    showCancelButton: false,
                    confirmButtonColor: "#006600",
                    confirmButtonText: "Okay",
                    closeOnConfirm: true
                });*/
                swal("Tender detail save successfully.", "", "success");
            }else{
                swal({
                    title: "Tender detail not save try again.",
                    //text: "You want to change status of admin user.",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                });
            }
          },
          error : function(data){
            $(".prepare_document_form_btn").attr("disabled", false);
              swal({
                    title: "Tender detail not save try again.",
                    //text: "You want to change status of admin user.",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                });
          }
        });
  }
})
$("#prepare_document_form").validate();
//Prepare part end --
//Upload part start --
function addMoreUploadTech(){
  counter_upload_tech += 1;

  $("#uploaded_document_div").append('<div class="uploaded_document_technical" id="uploaded_document_technical_'+counter_upload_tech+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-1" style="padding-top: 34px;">'+
                                                '<div class="form-group">'+
                                                  '<input type="checkbox" class="uploaded_document_checked" name="uploaded_document_checked['+counter_upload_tech+']" id="uploaded_document_checked_'+counter_upload_tech+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Name <span class="error">*</span></label>'+
                                                  '<input type="text" class="form-control uploaded_document_name" name="uploaded_document_name['+counter_upload_tech+']" id="uploaded_document_name_'+counter_upload_tech+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Attechment<span class="error">*</span></label>'+
                                                  '<input type="file" class="form-control uploaded_document_attechment" name="uploaded_document_attechment['+counter_upload_tech+']" id="uploaded_document_attechment_'+counter_upload_tech+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="padding-top: 30px;">'+
                                                '<button type="button" class="btn btn-danger remove_uploaded_tech_part"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');
}

//Remove
$("body").on("click",".remove_uploaded_tech_part",function(){
      $(this).parents(".uploaded_document_technical").remove();
});

//Add data
$(".uploaded_document_form_btn").on('click',function(){
  $(".uploaded_document_name").each(function(){
    $(this).rules('add',{
        required :true
    });
  });
  // $(".uploaded_document_attechment").each(function(){
  //   $(this).rules('add',{
  //       required :true
  //   });
  // });
  if($("#uploaded_document_form").valid()){
    $(".uploaded_document_form_btn").attr("disabled", true);
    var form = $('#uploaded_document_form')[0];
        var formData = new FormData(form);
          

        if(counter_upload_tech == 0){
            formData.append('uploaded_document_attechment[]', $('.uploaded_document_attechment')[0].files[0]);
        }else{
          for(i = 0; i >= counter_upload_tech ; i++){
              formData.append('uploaded_document_attechment', $('.uploaded_document_attechment')[1].files[1]);  
          }  
        }
          
        $.ajax({
          type : "POST",
          url : "{{url('tender_sub_uploaded_tech')}}",
          data : formData,
          processData: false,
          contentType: false,
          success : function(data){
            // console.log(data);
            getSubmissionTenderTech();
            $(".uploaded_document_form_btn").attr("disabled", false);
            if(data == "success"){  
                /*swal({
                    title: "Tender detail save successfully.",
                    //text: "You want to change status of admin user.",
                    type: "info",
                    showCancelButton: false,
                    confirmButtonColor: "#006600",
                    confirmButtonText: "Okay",
                    closeOnConfirm: true
                });*/
                swal("Tender detail save successfully.", "", "success");
            }else{
                swal({
                    title: "Tender detail not save try again.",
                    //text: "You want to change status of admin user.",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                });
            }
          },
          error : function(data){
            $(".uploaded_document_form_btn").attr("disabled", false);
            swal({
                    title: "Tender detail not save try again.",
                    //text: "You want to change status of admin user.",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                });
          }
        });
  }
})
$("#uploaded_document_form").validate();
//Upload part end --

//Get submission tender Technical
function getSubmissionTenderTech()
{
  $.ajax({
      type : "POST",
      url : "{{url('get_tender_submission_tech')}}",
      data : {
        "_token": "{{ csrf_token() }}",
          "id": tender_id
      },
      success : function(data){
          var data = JSON.parse(data);
          
          //render prepare data

          if(data.length){
            var check_val = "";
            var arr_prepare_val = 0;
            $("#prepare_document_div").html('');
            counter_prepare_tech = data.length - 1;
            counter_prepare_tech_validate = data.length;
            $.each( data, function(key, value){
              if(value.prepare_document_checked === 1){
                check_val = "checked";
                arr_prepare_val += 1;
              }else{
                check_val = "";
              }

              if(arr_prepare_val === data.length){
                $("#tender_sub_tech_info").removeClass($("#tender_sub_tech_info").attr('class'));
                $("#tender_sub_tech_info").addClass('btn btn-success btn-circle');
              }

              if(arr_prepare_val < data.length){
                $("#tender_sub_tech_info").removeClass($("#tender_sub_tech_info").attr('class'));
                $("#tender_sub_tech_info").addClass('btn btn-warning btn-circle');
              }

              /*if(arr_prepare_val === 0){
                $("#tender_sub_tech_info").removeClass($("#tender_sub_tech_info").attr('class'));
                $("#tender_sub_tech_info").addClass('btn btn-danger btn-circle');
              }*/

              $("#prepare_document_div").append('<div class="prepare_document_technical" id="prepare_document_technical_'+key+'">'+
                                                '<div class="row">'+
                                                  '<div class="col-md-1" style="padding-top: 34px;">'+
                                                    '<div class="form-group">'+
                                                      '<input type="checkbox" class="prepare_document_checked" '+check_val+' name="prepare_document_checked['+key+']" id="prepare_document_checked_'+key+'">'+
                                                        '<input type="hidden" name="prepare_document_tech_id['+key+']" id="prepare_document_tech_id_'+key+'" value="'+value.id+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-4">'+
                                                    '<div class="form-group">'+
                                                      '<label class="control-label"> Document Name <span class="error">*</span> </label>'+
                                                      '<input type="text" class="form-control prepare_document_name" name="prepare_document_name['+key+']" id="prepare_document_name_'+key+'" value="'+value.prepare_document_name+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-4">'+
                                                    '<div class="form-group">'+
                                                      '<label class="control-label"> Document Attechment <span class="error">*</span> </label>'+
                                                      '<input type="file" class="form-control prepare_document_attechment" name="prepare_document_attechment['+key+']" id="prepare_document_attechment_'+key+'">'+
                                                        '<input type="hidden" name="prepare_document_attechment_hidden['+key+']" id="prepare_document_attechment_hidden_'+key+'" value="'+value.prepare_document_attechment+'">'+
                                                    '</div>'+
                                                  '</div>'+
                                                  '<div class="col-md-1" style="padding-top: 30px;">'+
                                                      '<a href="{{url('downloadsubpreparetechdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"> <i class="fa fa-download" aria-hidden="true"></i></a>'+
                                                    '</div>'+
                                                  @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                                  '<div class="col-md-2" style="padding-top: 30px;">'+
                                                    '<button type="button" class="btn btn-danger remove_prepare_tech_part_detele" id="'+value.id+'"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                  '</div>'+
                                                  @endif
                                                '</div>'+
                                              '</div>');
            });
          }else{
            $("#tender_sub_tech_info").removeClass($("#tender_sub_tech_info").attr('class'));
            $("#tender_sub_tech_info").addClass('btn btn-danger btn-circle');
          }

          //render upload data
          if(data.length){
            var check_val = "";
            
            var arr_upload_val = 0;
            var arr_upload_val1 = 0;
            counter_upload_tech = data.length - 1;
            $("#uploaded_document_div").html('');
            $(".uploaded_document_form_btn").show();
              $.each( data, function(key, value){


              if(value.uploaded_document_checked === 1){
                check_val = "checked";
                arr_upload_val += 1;
              }else{
                check_val = "";
              }

              if(value.uploaded_document_name != null){
                  arr_upload_val1 = 1;
              }

              if(arr_upload_val === data.length){
                $("#tender_sub_tech_info1").removeClass($("#tender_sub_tech_info1").attr('class'));
                $("#tender_sub_tech_info1").addClass('btn btn-success btn-circle');
              }

              if(arr_upload_val < data.length && arr_upload_val1 == 1){
                $("#tender_sub_tech_info1").removeClass($("#tender_sub_tech_info1").attr('class'));
                $("#tender_sub_tech_info1").addClass('btn btn-warning btn-circle');
              }

              if(arr_upload_val1 == 0){
                $("#tender_sub_tech_info1").removeClass($("#tender_sub_tech_info1").attr('class'));
                $("#tender_sub_tech_info1").addClass('btn btn-danger btn-circle');
              }

              /*if(arr_upload_val === 0){
                $("#tender_sub_tech_info1").removeClass($("#tender_sub_tech_info1").attr('class'));
                $("#tender_sub_tech_info1").addClass('btn btn-danger btn-circle');
              }*/

              if(value.uploaded_document_name){
                uploaded_document_name = value.uploaded_document_name;
              }else{
                uploaded_document_name = value.prepare_document_name;
              }

              if(value.uploaded_document_attechment){
                validate_img = "";
                display_download = "";
              }else{
                display_download = "display:none;";
                validate_img = "required";
              }

            $("#uploaded_document_div").append('<div class="uploaded_document_technical" id="uploaded_document_technical_'+key+'">'+
              '<div class="row">'+
                '<div class="col-md-1" style="padding-top: 34px;">'+
                  '<div class="form-group">'+
                    '<input type="checkbox" class="uploaded_document_checked" '+check_val+' name="uploaded_document_checked['+key+']" id="uploaded_document_checked_'+key+'">'+
                  '</div>'+
                '</div>'+
                '<div class="col-md-4">'+
                  '<div class="form-group">'+
                    '<label class="control-label"> Document Name<span class="error">*</span></label>'+
                    '<input type="text" class="form-control uploaded_document_name" name="uploaded_document_name['+key+']" id="uploaded_document_name_'+key+'" value="'+uploaded_document_name+'">'+
                    '<input type="hidden" name="uploaded_document_tech_id['+key+']" id="uploaded_document_tech_id_'+key+'" value="'+value.id+'">'+
                  '</div>'+
                '</div>'+
                '<div class="col-md-4">'+
                  '<div class="form-group">'+
                    '<label class="control-label"> Document Attechment<span class="error">*</span></label>'+
                    '<input type="file" class="form-control uploaded_document_attechment '+validate_img+'" name="uploaded_document_attechment['+key+']" id="uploaded_document_attechment_'+key+'">'+
                    '<input type="hidden" name="uploaded_document_attechment_hidden['+key+']" id="uploaded_document_attechment_hidden_'+key+'" value="'+value.uploaded_document_attechment+'">'+
                  '</div>'+
                '</div>'+
                '<div class="col-md-1" style="padding-top: 30px;'+display_download+'">'+
                  '<a href="{{url('downloadsubtechdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"> <i class="fa fa-download" aria-hidden="true"></i></a>'+
                '</div>'+
                /*'<div class="col-md-2" style="padding-top: 30px;">'+
                  '<button type="button" class="btn btn-danger remove_uploaded_tech_part"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+*/
                '</div>'+
              '</div>'+
            '</div>');

              });
          }else{
            $("#uploaded_document_div").html('');
            $(".uploaded_document_form_btn").hide();
            
            $("#tender_sub_tech_info1").removeClass($("#tender_sub_tech_info1").attr('class'));
            $("#tender_sub_tech_info1").addClass('btn btn-danger btn-circle'); 
          }
      }

  });
}

// ---------- Submission Technical Part End ------------------------
// ---------- Submission Financial Part Start ----------------------

//Prepare start
//remove
$('body').on('click','.remove_prepare_fina_part',function(){
    $(this).parents('.prepare_document_financial').remove();
});

//delete
$('body').on('click','.remove_prepare_fina_part_delete',function(){
    var remove_fina_delete_id = $(this).attr('id');
    $(this).parents('.prepare_document_financial').remove();
      $.ajax({
            type : "POST",
            url : "{{url('delete_prepare_financial_file')}}",
            data: {
              "_token": "{{ csrf_token() }}",
              "id": remove_fina_delete_id
            },
            success : function(data){
              console.log(data);
              getSubmissionTenderFina();
            }
        });
});

function addMorePrepareFina(){
  counter_prepare_fina += 1;
  $("#prepare_document_financial_div").append('<div class="prepare_document_financial" id="prepare_document_financial_'+counter_prepare_fina+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-1" style="padding-top: 34px;">'+
                                                '<div class="form-group">'+
                                                  '<input type="checkbox" class="prepare_document_checked_fina" name="prepare_document_checked_fina['+counter_prepare_fina+']" id="prepare_document_checked_fina_'+counter_prepare_fina+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Name<span class="error">*</span></label>'+
                                                  '<input type="text" class="form-control prepare_document_name_fina" name="prepare_document_name_fina['+counter_prepare_fina+']" id="prepare_document_name_fina_'+counter_prepare_fina+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Attechment<span class="error">*</span></label>'+
                                                  '<input type="file" class="form-control prepare_document_attechment_fina" name="prepare_document_attechment_fina['+counter_prepare_fina+']" id="prepare_document_attechment_fina_'+counter_prepare_fina+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="padding-top: 30px;">'+
                                                '<button type="button" class="btn btn-danger remove_prepare_fina_part"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');
}

//Add data
$(".prepare_document_financial_form_btn").on('click',function(){
  $(".prepare_document_name_fina").each(function(){
    $(this).rules('add',{
        required :true
    });
  });
  $(".prepare_document_attechment_fina").each(function(){
    $(this).rules('add',{
        required :true
    });
  });

  for(j = 0 ; j < counter_prepare_fina_validate ; j++){
    $("#prepare_document_attechment_fina_"+j).removeClass("error").rules("remove");
  }

  if($("#prepare_document_financial_form").valid()){
    $(".prepare_document_financial_form_btn").attr("disabled", true);
    var form = $('#prepare_document_financial_form')[0];
        var formData = new FormData(form);
          

        if(counter_prepare_fina == 0){
            formData.append('prepare_document_attechment_fina[]', $('.prepare_document_attechment_fina')[0].files[0]);
        }else{
          for(i = 0; i >= counter_prepare_fina ; i++){
              formData.append('prepare_document_attechment_fina', $('.prepare_document_attechment_fina')[1].files[1]);  
          }  
        }
          
        $.ajax({
          type : "POST",
          url : "{{url('tender_sub_prepare_fina')}}",
          data : formData,
          processData: false,
          contentType: false,
          success : function(data){
            // console.log(data);
            $(".prepare_document_financial_form_btn").attr("disabled", false);
            if(data == "success"){
            $("#tender_sub_fina_info").removeClass('btn btn-danger btn-circle');
            $("#tender_sub_fina_info").addClass('btn btn-warning btn-circle');
            $("#tender_sub_fina_info1").removeClass('btn btn-danger btn-circle');
            $("#tender_sub_fina_info1").addClass('btn btn-warning btn-circle');  
            getSubmissionTenderFina();
                /*swal({
                    title: "Tender detail save successfully.",
                    //text: "You want to change status of admin user.",
                    type: "info",
                    showCancelButton: false,
                    confirmButtonColor: "#006600",
                    confirmButtonText: "Okay",
                    closeOnConfirm: true
                });*/
                swal("Tender detail save successfully.", "", "success");
            }else{
                swal({
                    title: "Tender detail not save try again.",
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
})
$("#prepare_document_financial_form").validate();

//Prepare end
//Upload start

function addMoreUploadFina(){
  counter_upload_fina += 1;
  $("#uploaded_document_financial_div").append('<div class="uploaded_document_financial" id="uploaded_document_financial_'+counter_upload_fina+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-1" style="padding-top: 34px;">'+
                                                '<div class="form-group">'+
                                                  '<input type="checkbox" class="uploaded_document_checked_fina" name="uploaded_document_checked_fina['+counter_upload_fina+']" id="uploaded_document_checked_fina_'+counter_upload_fina+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Name<span class="error">*</span></label>'+
                                                  '<input type="text" class="form-control uploaded_document_name_fina" name="uploaded_document_name_fina['+counter_upload_fina+']" id="uploaded_document_name_fina_'+counter_upload_fina+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Attechment<span class="error">*</span></label>'+
                                                  '<input type="file" class="form-control uploaded_document_attechment_fina" name="uploaded_document_attechment_fina['+counter_upload_fina+']" id="uploaded_document_attechment_fina_'+counter_upload_fina+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="padding-top: 30px;">'+
                                                '<button type="button" class="btn btn-danger remove_uploaded_fina_part"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');
}

//remove
$('body').on('click','.remove_uploaded_fina_part',function(){
    $(this).parents('.uploaded_document_financial').remove();
});

//Add data
$(".uploaded_document_financial_form_btn").on('click',function(){
  $(".uploaded_document_name_fina").each(function(){
    $(this).rules('add',{
        required :true
    });
  });
  // $(".uploaded_document_attechment_fina").each(function(){
  //   $(this).rules('add',{
  //       required :true
  //   });
  // });
  if($("#uploaded_document_financial_form").valid()){
    $(".uploaded_document_financial_form_btn").attr("disabled", true);
    var form = $('#uploaded_document_financial_form')[0];
        var formData = new FormData(form);
          

        if(counter_upload_fina == 0){
            formData.append('uploaded_document_attechment_fina[]', $('.uploaded_document_attechment_fina')[0].files[0]);
        }else{
          for(i = 0; i >= counter_upload_fina ; i++){
              formData.append('uploaded_document_attechment_fina', $('.uploaded_document_attechment_fina')[1].files[1]);  
          }  
        }
          
        $.ajax({
          type : "POST",
          url : "{{url('tender_sub_uploaded_fina')}}",
          data : formData,
          processData: false,
          contentType: false,
          success : function(data){
            // console.log(data);
            getSubmissionTenderFina();
            $(".uploaded_document_financial_form_btn").attr("disabled", false);
            if(data == "success"){  
              
                /*swal({
                    title: "Tender detail save successfully.",
                    //text: "You want to change status of admin user.",
                    type: "info",
                    showCancelButton: false,
                    confirmButtonColor: "#006600",
                    confirmButtonText: "Okay",
                    closeOnConfirm: true
                });*/
                swal("Tender detail save successfully.", "", "success");
            }else{
                swal({
                    title: "Tender detail not save try again.",
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
})
$("#uploaded_document_financial_form").validate();

//Upload end

//Get submission tender
function getSubmissionTenderFina()
{
  $.ajax({
      type : "POST",
      url : "{{url('get_tender_submission_fina')}}",
      data : {
        "_token": "{{ csrf_token() }}",
          "id": tender_id
      },
      success : function(data){
          var data = JSON.parse(data);
          //render prepare part
          if(data.length){
            counter_prepare_fina = data.length - 1;
            counter_prepare_fina_validate = data.length;
            var check_val = "";
            var arr_prepare_val = 0;
            // prepare_document_financial_div
            $("#prepare_document_financial_div").html('');
            $.each( data, function(key, value){
            
            if(value.prepare_document_checked === 1){
                check_val = "checked";
                arr_prepare_val += 1;
              }else{
                check_val = "";
              }

              if(arr_prepare_val === data.length){
                $("#tender_sub_fina_info").removeClass($("#tender_sub_fina_info").attr('class'));
                $("#tender_sub_fina_info").addClass('btn btn-success btn-circle');
              }

              if(arr_prepare_val < data.length){
                $("#tender_sub_fina_info").removeClass($("#tender_sub_fina_info").attr('class'));
                $("#tender_sub_fina_info").addClass('btn btn-warning btn-circle');
              }

              /*if(arr_prepare_val === 0){
                $("#tender_sub_fina_info").removeClass($("#tender_sub_fina_info").attr('class'));
                $("#tender_sub_fina_info").addClass('btn btn-danger btn-circle');
              }*/

            $("#prepare_document_financial_div").append('<div class="prepare_document_financial" id="prepare_document_financial_'+key+'">'+
                                              '<div class="row">'+
                                                '<div class="col-md-1" style="padding-top: 34px;">'+
                                                  '<div class="form-group">'+
                                                    '<input type="checkbox" class="prepare_document_checked_fina" '+check_val+'  name="prepare_document_checked_fina['+key+']" id="prepare_document_checked_fina_'+key+'">'+
                                                    '<input type="hidden" name="prepare_document_fina_id['+key+']" id="prepare_document_fina_id_'+key+'" value="'+value.id+'">'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-4">'+
                                                  '<div class="form-group">'+
                                                    '<label class="control-label"> Document Name<span class="error">*</span></label>'+
                                                    '<input type="text" class="form-control prepare_document_name_fina" name="prepare_document_name_fina['+key+']" id="prepare_document_name_fina_'+key+'" value="'+value.prepare_document_name+'">'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-4">'+
                                                  '<div class="form-group">'+
                                                    '<label class="control-label"> Document Attechment<span class="error">*</span></label>'+
                                                    '<input type="file" class="form-control prepare_document_attechment_fina" name="prepare_document_attechment_fina['+key+']" id="prepare_document_attechment_fina_'+key+'">'+
                                                    '<input type="hidden" name="prepare_document_attechment_fina_hidden['+key+']" id="prepare_document_attechment_fina_hidden_'+key+'" value="'+value.prepare_document_attechment+'">'+
                                                  '</div>'+
                                                '</div>'+
                                                '<div class="col-md-1" style="padding-top: 30px;">'+
                                                  '<a href="{{url('downloadsubpreparefinadoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"> <i class="fa fa-download" aria-hidden="true"></i></a>'+
                                                '</div>'+
                                                @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission)
                                                '<div class="col-md-2" style="padding-top: 30px;">'+
                                                  '<button type="button" class="btn btn-danger remove_prepare_fina_part_delete" id="'+value.id+'"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                '</div>'+
                                                @endif
                                              '</div>'+
                                            '</div>');
            });
          }else{
            $("#tender_sub_fina_info").removeClass($("#tender_sub_fina_info").attr('class'));
            $("#tender_sub_fina_info").addClass('btn btn-danger btn-circle');
          }

          //render upload part
          if(data.length){
            counter_upload_fina = data.length - 1;
            var check_val = "";
            
            var arr_upload_val = 0;
            var arr_upload_val1 = 0;
            $(".uploaded_document_financial_form_btn").show();
            $("#uploaded_document_financial_div").html('');
              $.each( data, function(key, value){

              if(value.uploaded_document_checked === 1){
                check_val = "checked";
                arr_upload_val += 1;
              }else{
                check_val = "";
              }

              if(value.uploaded_document_name != null){
                  arr_upload_val1 = 1;
              }

              if(arr_upload_val === data.length){
                $("#tender_sub_fina_info1").removeClass($("#tender_sub_fina_info1").attr('class'));
                $("#tender_sub_fina_info1").addClass('btn btn-success btn-circle');
              }

              if(arr_upload_val < data.length && arr_upload_val1 == 1){
                $("#tender_sub_fina_info1").removeClass($("#tender_sub_fina_info1").attr('class'));
                $("#tender_sub_fina_info1").addClass('btn btn-warning btn-circle');
              }

              if(arr_upload_val1 == 0){
                $("#tender_sub_fina_info1").removeClass($("#tender_sub_fina_info1").attr('class'));
                $("#tender_sub_fina_info1").addClass('btn btn-danger btn-circle');
              }

              /*if(arr_upload_val === 0){
                $("#tender_sub_fina_info1").removeClass($("#tender_sub_fina_info1").attr('class'));
                $("#tender_sub_fina_info1").addClass('btn btn-danger btn-circle');
              }*/


              if(value.uploaded_document_name){
                uploaded_document_name = value.uploaded_document_name;
              }else{
                uploaded_document_name = value.prepare_document_name;
              }

              if(value.uploaded_document_attechment){
                validate_img = "";
                display_download = "";
              }else{
                display_download = "display:none;";
                validate_img = "required";
              }
            $("#uploaded_document_financial_div").append('<div class="uploaded_document_financial" id="uploaded_document_financial_'+key+'">'+
              '<div class="row">'+
                '<div class="col-md-1" style="padding-top: 34px;">'+
                  '<div class="form-group">'+
                    '<input type="checkbox" class="uploaded_document_checked_fina" '+check_val+' name="uploaded_document_checked_fina['+key+']" id="uploaded_document_checked_fina_'+key+'">'+
                  '</div>'+
                '</div>'+
                '<div class="col-md-4">'+
                  '<div class="form-group">'+
                    '<label class="control-label"> Document Name<span class="error">*</span></label>'+
                    '<input type="text" class="form-control uploaded_document_name_fina" name="uploaded_document_name_fina['+key+']" id="uploaded_document_name_fina_'+key+'" value="'+uploaded_document_name+'">'+
                    '<input type="hidden" name="uploaded_document_fina_id['+key+']" id="uploaded_document_fina_id_'+key+'" value="'+value.id+'">'+
                  '</div>'+
                '</div>'+
                '<div class="col-md-4">'+
                  '<div class="form-group">'+
                    '<label class="control-label"> Document Attechment<span class="error">*</span></label>'+
                    '<input type="file" class="form-control uploaded_document_attechment_fina '+validate_img+'" name="uploaded_document_attechment_fina['+key+']" id="uploaded_document_attechment_fina_'+key+'">'+
                    '<input type="hidden" name="uploaded_document_attechment_fina_hidden['+key+']" id="uploaded_document_attechment_fina_hidden_'+key+'" value="'+value.uploaded_document_attechment+'">'+
                  '</div>'+
                '</div>'+
                '<div class="col-md-1" style="padding-top: 30px;'+display_download+'">'+
                  '<a href="{{url('downloadsubfinadoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"> <i class="fa fa-download" aria-hidden="true"></i></a>'+
                '</div>'+
                /*'<div class="col-md-2" style="padding-top: 30px;">'+
                  '<button type="button" class="btn btn-danger remove_uploaded_fina_part"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                '</div>'+*/
              '</div>'+
            '</div>');

              });
          }else{
            $("#uploaded_document_financial_div").html('');
            $(".uploaded_document_financial_form_btn").hide(); 
            
            $("#tender_sub_fina_info1").removeClass($("#tender_sub_fina_info1").attr('class'));
            $("#tender_sub_fina_info1").addClass('btn btn-danger btn-circle');
          }
      }

  });
}

// ---------- Submission Financial Part End ------------------------
// ---------- Submission Commercial BOQ Part Start ------------------------

// Prepare start

//remove

$('body').on('click','.remove_prepare_boq_part',function(){
  $(this).parents(".prepare_document_boq").remove();
})

//delete
$('body').on('click','.remove_prepare_boq_part_delete',function(){
  $(this).parents(".prepare_document_boq").remove();
  var remove_boq_delete_id = $(this).attr('id');
  $.ajax({
            type : "POST",
            url : "{{url('delete_prepare_boq_file')}}",
            data: {
              "_token": "{{ csrf_token() }}",
              "id": remove_boq_delete_id
            },
            success : function(data){
              console.log(data);
              getSubmissionTenderBoq();
            }
        });
})
function addMorePrepareBoq(){
  counter_prepare_boq += 1;
  $("#prepare_document_boq_div").append('<div class="prepare_document_boq" id="prepare_document_boq_'+counter_prepare_boq+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-1" style="padding-top: 34px;">'+
                                                '<div class="form-group">'+
                                                  '<input type="checkbox" class="prepare_document_checked_boq" name="prepare_document_checked_boq['+counter_prepare_boq+']" id="prepare_document_checked_boq_'+counter_prepare_boq+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Name<span class="error">*</span></label>'+
                                                  '<input type="text" class="form-control prepare_document_name_boq" name="prepare_document_name_boq['+counter_prepare_boq+']" id="prepare_document_name_boq_'+counter_prepare_boq+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Attechment<span class="error">*</span></label>'+
                                                  '<input type="file" class="form-control prepare_document_attechment_boq" name="prepare_document_attechment_boq['+counter_prepare_boq+']" id="prepare_document_attechment_boq_'+counter_prepare_boq+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="padding-top: 30px;">'+
                                                '<button type="button" class="btn btn-danger remove_prepare_boq_part"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');
}

$(".prepare_document_boq_form_btn").on('click',function(){
  $(".prepare_document_name_boq").each(function(){
    $(this).rules('add',{
      required : true
    })
  });

  $(".prepare_document_attechment_boq").each(function(){
    $(this).rules('add',{
        required :true
    });
  });

  for(j = 0 ; j < counter_prepare_boq_validate ; j++){
    $("#prepare_document_attechment_boq_"+j).removeClass("error").rules("remove");
  }

  if($("#prepare_document_boq_form").valid()){
    $(".prepare_document_boq_form_btn").attr("disabled", true);
    var form = $('#prepare_document_boq_form')[0];
        var formData = new FormData(form);
          

        if(counter_prepare_boq == 0){
            formData.append('prepare_document_attechment_boq[]', $('.prepare_document_attechment_boq')[0].files[0]);
        }else{
          for(i = 0; i >= counter_prepare_boq ; i++){
              formData.append('prepare_document_attechment_boq', $('.prepare_document_attechment_boq')[1].files[1]);  
          }  
        }
          
        $.ajax({
          type : "POST",
          url : "{{url('tender_sub_prepare_boq')}}",
          data : formData,
          processData: false,
          contentType: false,
          success : function(data){
            // console.log(data);
            $(".prepare_document_boq_form_btn").attr("disabled", false);
            if(data == "success"){
            $("#tender_sub_boq_info").removeClass('btn btn-danger btn-circle');
            $("#tender_sub_boq_info").addClass('btn btn-warning btn-circle');  
              getSubmissionTenderBoq();
                /*swal({
                    title: "Tender detail save successfully.",
                    //text: "You want to change status of admin user.",
                    type: "info",
                    showCancelButton: false,
                    confirmButtonColor: "#006600",
                    confirmButtonText: "Okay",
                    closeOnConfirm: true
                });*/
                swal("Tender detail save successfully.", "", "success");
            }else{
                swal({
                    title: "Tender detail not save try again.",
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
$("#prepare_document_boq_form").validate();
//Prepare End

//Uploaded start
function addMoreUploadedBoq(){
  counter_upload_boq += 1;
  $("#uploaded_document_boq_div").append('<div class="uploaded_document_boq" id="uploaded_document_boq_'+counter_upload_boq+'">'+
                                            '<div class="row">'+
                                              '<div class="col-md-1" style="padding-top: 34px;">'+
                                                '<div class="form-group">'+
                                                  '<input type="checkbox" class="uploaded_document_checked_boq" name="uploaded_document_checked_boq['+counter_upload_boq+']" id="uploaded_document_checked_boq_'+counter_upload_boq+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Name<span class="error">*</span></label>'+
                                                  '<input type="text" class="form-control uploaded_document_name_boq" name="uploaded_document_name_boq['+counter_upload_boq+']" id="uploaded_document_name_boq_'+counter_upload_boq+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-4">'+
                                                '<div class="form-group">'+
                                                  '<label class="control-label"> Document Attechment<span class="error">*</span></label>'+
                                                  '<input type="file" class="form-control uploaded_document_attechment_boq" name="uploaded_document_attechment_boq['+counter_upload_boq+']" id="uploaded_document_attechment_boq_'+counter_upload_boq+'">'+
                                                '</div>'+
                                              '</div>'+
                                              '<div class="col-md-2" style="padding-top: 30px;">'+
                                                '<button type="button" class="btn btn-danger remove_uploaded_boq_part"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                              '</div>'+
                                            '</div>'+
                                          '</div>');
}

$('body').on('click','.remove_uploaded_boq_part',function(){
  $(this).parents('.uploaded_document_boq').remove();
});

$('.uploaded_document_boq_form_btn').on('click',function(){
  $('.uploaded_document_name_boq').each(function(){
    $(this).rules('add',{
      required : true
    });
  });
  // $('.uploaded_document_attechment_boq').each(function(){
  //   $(this).rules('add',{
  //     required : true
  //   });
  // });
  if($("#uploaded_document_boq_form").valid()){
    $(".uploaded_document_boq_form_btn").attr("disabled", true);
    var form = $('#uploaded_document_boq_form')[0];
        var formData = new FormData(form);
          

        if(counter_upload_boq == 0){
            formData.append('uploaded_document_attechment_boq[]', $('.uploaded_document_attechment_boq')[0].files[0]);
        }else{
          for(i = 0; i >= counter_upload_boq ; i++){
              formData.append('uploaded_document_attechment_boq', $('.uploaded_document_attechment_boq')[1].files[1]);  
          }  
        }
          
        $.ajax({
          type : "POST",
          url : "{{url('tender_sub_uploaded_boq')}}",
          data : formData,
          processData: false,
          contentType: false,
          success : function(data){
            // console.log(data);
            $(".uploaded_document_boq_form_btn").attr("disabled", false);
            getSubmissionTenderBoq();
            if(data == "success"){  
              
                /*swal({
                    title: "Tender detail save successfully.",
                    //text: "You want to change status of admin user.",
                    type: "info",
                    showCancelButton: false,
                    confirmButtonColor: "#006600",
                    confirmButtonText: "Okay",
                    closeOnConfirm: true
                });*/
                swal("Tender detail save successfully.", "", "success");
            }else{
                swal({
                    title: "Tender detail not save try again.",
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
$("#uploaded_document_boq_form").validate();

//Uploaded end

//Get submission tender
function getSubmissionTenderBoq()
{
  $.ajax({
      type : "POST",
      url : "{{url('get_tender_submission_boq')}}",
      data : {
        "_token": "{{ csrf_token() }}",
          "id": tender_id
      },
      success : function(data){
          var data = JSON.parse(data);
          //render prepare part

          if(data.length){
            counter_prepare_boq = data.length - 1;
            counter_prepare_boq_validate = data.length;
            var check_val = "";
            var arr_prepare_val = 0;
            $("#prepare_document_boq_div").html('');
            $.each( data, function(key, value){
              if(value.prepare_document_checked === 1){
                check_val = "checked";
                arr_prepare_val += 1;
              }else{
                check_val = "";
              }

              if(arr_prepare_val === data.length){
                $("#tender_sub_boq_info").removeClass($("#tender_sub_boq_info").attr('class'));
                $("#tender_sub_boq_info").addClass('btn btn-success btn-circle');
              }

              if(arr_prepare_val < data.length){
                $("#tender_sub_boq_info").removeClass($("#tender_sub_boq_info").attr('class'));
                $("#tender_sub_boq_info").addClass('btn btn-warning btn-circle');
              }

              /*if(arr_prepare_val === 0){
                $("#tender_sub_boq_info").removeClass($("#tender_sub_boq_info").attr('class'));
                $("#tender_sub_boq_info").addClass('btn btn-danger btn-circle');
              }*/

              $("#prepare_document_boq_div").append('<div class="prepare_document_boq" id="prepare_document_boq_'+key+'">'+
                                                  '<div class="row">'+
                                                    '<div class="col-md-1" style="padding-top: 34px;">'+
                                                      '<div class="form-group">'+
                                                        '<input type="checkbox" class="prepare_document_checked_boq" '+check_val+' name="prepare_document_checked_boq['+key+']" id="prepare_document_checked_boq_'+key+'">'+

                                                        '<input type="hidden" name="prepare_document_boq_id[]" id="prepare_document_boq_id_'+key+'" value="'+value.id+'">'+
                                                      '</div>'+
                                                    '</div>'+
                                                    '<div class="col-md-4">'+
                                                      '<div class="form-group">'+
                                                        '<label class="control-label"> Document Name<span class="error">*</span></label>'+
                                                        '<input type="text" class="form-control prepare_document_name_boq" name="prepare_document_name_boq['+key+']" id="prepare_document_name_boq_'+key+'" value="'+value.prepare_document_name+'">'+
                                                      '</div>'+
                                                    '</div>'+
                                                    '<div class="col-md-4">'+
                                                      '<div class="form-group">'+
                                                        '<label class="control-label"> Document Attechment<span class="error">*</span></label>'+
                                                        '<input type="file" class="form-control prepare_document_attechment_boq" name="prepare_document_attechment_boq['+key+']" id="prepare_document_attechment_boq_'+key+'">'+

                                                        '<input type="hidden" name="prepare_document_attechment_boq_hidden[]" id="prepare_document_attechment_boq_hidden_'+key+'" value="'+value.prepare_document_attechment+'">'+
                                                      '</div>'+
                                                    '</div>'+
                                                    '<div class="col-md-1" style="padding-top: 30px;">'+
                                                        '<a href="{{url('downloadsubprepareboqdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"> <i class="fa fa-download" aria-hidden="true"></i></a>'+
                                                      '</div>'+
                                                    @if(Auth::user()->id == $add_tender_permission || Auth::user()->id == $edit_tender_permission) 
                                                    '<div class="col-md-2" style="padding-top: 30px;">'+
                                                      '<button type="button" class="btn btn-danger remove_prepare_boq_part_delete" id="'+value.id+'"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                                                    '</div>'+
                                                    @endif
                                                  '</div>'+
                                                '</div>');
            });
          }else{
                $("#tender_sub_boq_info").removeClass($("#tender_sub_boq_info").attr('class'));
                $("#tender_sub_boq_info").addClass('btn btn-danger btn-circle');
          }

          //render upload part
          if(data.length){
            counter_upload_boq = data.length - 1;
            var check_val = "";
            
            var arr_upload_val = 0;
            var arr_upload_val1 = 0;
            $("#uploaded_document_boq_div").html('');
            $(".uploaded_document_boq_form_btn").show();
              $.each( data, function(key, value){
              
                

              if(value.uploaded_document_checked === 1){
                check_val = "checked";
                arr_upload_val += 1;
              }else{
                check_val = "";
              }

              if(value.uploaded_document_name != null){
                  arr_upload_val1 = 1;
              }
              

              if(arr_upload_val === data.length){
                $("#tender_sub_boq_info1").removeClass($("#tender_sub_boq_info1").attr('class'));
                $("#tender_sub_boq_info1").addClass('btn btn-success btn-circle');
              }

              if(arr_upload_val < data.length && arr_upload_val1 == 1){
                $("#tender_sub_boq_info1").removeClass($("#tender_sub_boq_info1").attr('class'));
                $("#tender_sub_boq_info1").addClass('btn btn-warning btn-circle');
              }

              if(arr_upload_val1 == 0){
                $("#tender_sub_boq_info1").removeClass($("#tender_sub_boq_info1").attr('class'));
                $("#tender_sub_boq_info1").addClass('btn btn-danger btn-circle');
              }

              /*if(arr_upload_val === 0){
                $("#tender_sub_boq_info1").removeClass($("#tender_sub_boq_info1").attr('class'));
                $("#tender_sub_boq_info1").addClass('btn btn-danger btn-circle');
              }*/

              if(value.uploaded_document_name){
                uploaded_document_name = value.uploaded_document_name;
              }else{
                uploaded_document_name = value.prepare_document_name;
              }

              if(value.uploaded_document_attechment){
                validate_img = "";
                display_download = "";
              }else{
                display_download = "display:none;";
                validate_img = "required";
              }

            $("#uploaded_document_boq_div").append('<div class="uploaded_document_boq" id="uploaded_document_boq_'+key+'">'+
              '<div class="row">'+
                '<div class="col-md-1" style="padding-top: 34px;">'+
                  '<div class="form-group">'+
                    '<input type="checkbox" class="uploaded_document_checked_boq" '+check_val+' name="uploaded_document_checked_boq['+key+']" id="uploaded_document_checked_boq_'+key+'">'+
                  '</div>'+
                '</div>'+
                '<div class="col-md-4">'+
                  '<div class="form-group">'+
                    '<label class="control-label"> Document Name<span class="error">*</span></label>'+
                    '<input type="text" class="form-control uploaded_document_name_boq" name="uploaded_document_name_boq['+key+']" id="uploaded_document_name_boq_'+key+'" value="'+uploaded_document_name+'">'+
                    '<input type="hidden" name="uploaded_document_boq_id['+key+']" id="uploaded_document_boq_id_'+key+'" value="'+value.id+'">'+
                  '</div>'+
                '</div>'+
                '<div class="col-md-4">'+
                  '<div class="form-group">'+
                    '<label class="control-label"> Document Attechment</label>'+
                    '<input type="file" class="form-control uploaded_document_attechment_boq" '+validate_img+' name="uploaded_document_attechment_boq['+key+']" id="uploaded_document_attechment_boq_'+key+'">'+
                    '<input type="hidden" name="uploaded_document_attechment_boq_hidden['+key+']" id="uploaded_document_attechment_boq_hidden_'+key+'" value="'+value.uploaded_document_attechment+'">'+
                  '</div>'+
                '</div>'+
                '<div class="col-md-1" style="padding-top: 30px;'+display_download+'">'+
                  '<a href="{{url('downloadsubboqdoc')}}/'+value.id+'" target="_blank" class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Download Document"> <i class="fa fa-download" aria-hidden="true"></i></a>'+
                '</div>'+
                /*'<div class="col-md-2" style="padding-top: 30px;">'+
                  '<button type="button" class="btn btn-danger remove_uploaded_boq_part"> <i class="fa fa-times" aria-hidden="true"></i> Remove</button>'+
                '</div>'+*/
              '</div>'+
            '</div>');

              });
          }else{
            $("#uploaded_document_boq_div").html('');
            $(".uploaded_document_boq_form_btn").hide();
            
            $("#tender_sub_boq_info1").removeClass($("#tender_sub_boq_info1").attr('class'));
            $("#tender_sub_boq_info1").addClass('btn btn-danger btn-circle');
          }
      }

  });
}

// ---------- Submission Commercial BOQ Part End ------------------------
function alertMassage(data,success,error){
  if(data == "success"){

    // $("#"+param1).removeClass('btn btn-danger btn-circle');
    // $("#"+param1).addClass('btn btn-success btn-circle');

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
</script>
@endsection



