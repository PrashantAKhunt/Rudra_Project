@extends('layouts.admin_app')

@section('content')
<?php

use App\Department;
use App\TenderCategory;
use App\User;

$department = Department::pluck('dept_name', 'id');
$tendercategory = TenderCategory::whereStatus('Enabled')->pluck('tender_category', 'id');
$user = User::whereStatus('Enabled')->pluck('name', 'id');
?>
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


                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                    <div class="panel-heading"> Tender Detail 
                                        <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                    </div>
                                    <div class="panel-wrapper collapse" aria-expanded="false">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Tender Sr No.</label>
                                                        <input type="text" class="form-control tender_sr_no" name="tender_sr_no" id="tender_sr_no" value="{{$tender['tender_sr_no']}}" readonly="readonly">
                                                        <input type="hidden" name="id" value="{{$tender['id']}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Department</label>
                                                        <select class="form-control department_id" name="department_id" id="department_id" disabled="disabled">
                                                            <option value="">Select</option>
                                                            @foreach($department as $key => $value)
                                                            <option value="{{$key}}" {{ $tender['department_id'] == $key ? 'selected' : '' }}>{{$value}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Name Of Work</label>
                                                        <input type="text" class="form-control name_of_work" name="name_of_work" id="name_of_work" value="{{$tender['name_of_work']}}" readonly="readonly">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Estimate Cost</label>
                                                        <input type="text" class="form-control" name="estimate_cost" id="estimate_cost" value="{{$tender['estimate_cost']}}" readonly="readonly">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="inputEmail4">Joint Venture</label>
                                                        <select class="form-control" name="joint_venture" id="joint_venture" disabled="disabled">
                                                            <option value="">Select</option>
                                                            <option value="Yes" {{$tender['joint_venture'] == "Yes" ? "selected" : ""}}>Yes</option>
                                                            <option value="No" {{$tender['joint_venture'] == "No" ? "selected" : "" }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="inputEmail4">Quote Type</label>
                                                        <select class="form-control" name="quote_type" id="quote_type" disabled="disabled">
                                                            <option value="">Select</option>
                                                            <option value="Percentage Rate" {{$tender['quote_type'] == "Percentage Rate" ? "selected" : ""}}>Percentage Rate</option>
                                                            <option value="Item Rate" {{$tender['quote_type'] == "Item Rate" ? "selected" : ""}}>Item Rate</option>
                                                            <option value="Lumsum Rate" {{$tender['quote_type'] == "Lumsum Rate" ? "selected" : ""}}>Lumsum Rate</option>
                                                            <option value="Other Type" {{$tender['quote_type'] == "Other Type" ? "selected" : ""}}>Other Type</option>
                                                        </select>
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
                                    <div class="panel-heading"> Pre-Bid Meeting Query Point
                                        <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                    </div>
                                    <div class="panel-wrapper collapse" aria-expanded="false">
                                        <div class="panel-body">
                                            
                                            <div id="pre_bid_meeting_query_point_div" class="row">
                                                <h3 class="title">Query Point 1</h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Query Point Document </label>
                                                            <input type="text" class="form-control " name="" id="" value="Query 1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Document Attachment</label>
                                                            <input type="file" class="form-control " name="" id="">
                                                            
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-md-1">
                                                        <a href="#" title="Download Already Uploaded Document" class="btn btn-primary btn-circle"><i class="fa fa-download"></i></a>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Name of Section</label>
                                                            <input type="text" class="form-control " name="" id="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Clause No.</label>
                                                            <input type="text" class="form-control " name="" id="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Sub Clause No.</label>
                                                            <input type="text" class="form-control " name="" id="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Page No.</label>
                                                            <input type="text" class="form-control " name="" id="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <h3 class="title">Query Point 2</h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Query Point Document </label>
                                                            <input type="text" class="form-control " name="" id="" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Document Attachment</label>
                                                            <input type="file" class="form-control " name="" id="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <a href="#" title="Download Already Uploaded Document" class="btn btn-primary btn-circle"><i class="fa fa-download"></i></a>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Name of Section</label>
                                                            <input type="text" class="form-control " name="" id="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Clause No.</label>
                                                            <input type="text" class="form-control " name="" id="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Sub Clause No.</label>
                                                            <input type="text" class="form-control " name="" id="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Page No.</label>
                                                            <input type="text" class="form-control " name="" id="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-top: 10px;">
                                                    <div class="col-sm-10"></div> 
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Add More</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4"></div>  
                                                </div>
                                                <button type="button" class="btn btn-success tender_fee_form_btn">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="panel panel-inverse">
                                    <div class="panel-heading"> Tender Corrigendum
                                        <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-plus"></i></a></div>
                                    </div>
                                    <div class="panel-wrapper collapse" aria-expanded="false">
                                        <div class="panel-body">
                                            <h3 class="title">Add Corrigendum</h3>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Select Query (If corrigendum is for our query)</label>
                                                        <select class="form-control">
                                                            <option>Select Query</option>
                                                            <option>Query 1</option>
                                                            <option>Query 2</option>
                                                            <option>Query 3</option>
                                                            <option>Query 4</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Corrigendum No.</label>
                                                        <input type="text" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Date</label>
                                                        <input type="text" class="form-control " name="" id="" value="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Sr. As Per Corrigendum</label>
                                                        <input type="text" class="form-control" name="" id="" value="" >
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="inputEmail4">Answer</label>
                                                        <textarea class="form-control"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="inputEmail4">Corrigendum Attachment</label>
                                                        <input type="file" class="form-control" />
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-success tender_fee_form_btn">Save</button>
                                            <hr>
                                            <h3 class="title">Corrigendum List</h3>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table id="corrigendum_table" class="table table-striped">
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
                                                            <tr>
                                                                <td>63464</td>
                                                                <td>12-04-2020</td>
                                                                <td>293849</td>
                                                                <td>test Answer</td>
                                                                <td><a href="#" class="btn btn-primary btn-circle"><i class="fa fa-download"></i></a></td>
                                                            </tr>
                                                            <tr>
                                                                <td>63464</td>
                                                                <td>12-04-2020</td>
                                                                <td>293849</td>
                                                                <td>test Answer</td>
                                                                <td><a href="#" class="btn btn-primary btn-circle"><i class="fa fa-download"></i></a></td>
                                                            </tr>
                                                            <tr>
                                                                <td>63464</td>
                                                                <td>12-04-2020</td>
                                                                <td>293849</td>
                                                                <td>test Answer</td>
                                                                <td><a href="#" class="btn btn-primary btn-circle"><i class="fa fa-download"></i></a></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
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
    $('#corrigendum_table').DataTable();
                                                            function show_hide_prebid_meeting(){
                                                            if ($('#tender_pre_bid').val() == 'Yes'){
                                                            $('#prebid_meeting_datetime').show();
                                                            $('#pre_bid_meeting_venue').show();
                                                            $('#pre_bid_meeting_query_point_div').show();
                                                            }
                                                            else{
                                                            $('#prebid_meeting_datetime').hide();
                                                            $('#pre_bid_meeting_venue').hide();
                                                            $('#pre_bid_meeting_query_point_div').hide();
                                                            }
                                                            }
                                                            var technical_counter = {{$tender_technical_eligibility_count}};
                                                            var financial_counter = {{$tender_financial_eligibility_count}};
                                                            $(document).ready(function() {
                                                            $('#tender_fee').trigger('change');
                                                            $('#tender_emd').trigger('change');
                                                            load_datepicker();
                                                            });
                                                            $('#tender_fee').on('change', function(){
                                                            var check_val = $(this).val();
                                                            if (check_val == "Yes"){
                                                            $(".tender_fee_div").show();
                                                            } else{
                                                            $(".tender_fee_div").hide();
                                                            }
                                                            });
                                                            $('#tender_emd').on('change', function(){
                                                            var check_val = $(this).val();
                                                            if (check_val == "Yes"){
                                                            $(".tender_emd_div").show();
                                                            } else{
                                                            $(".tender_emd_div").hide();
                                                            }
                                                            });
//Techical Eligibility Part
                                                            $("body").on("click", ".remove_tech_eli", function(){
                                                            $(this).parents(".technical_eligibility").remove();
                                                            });
                                                            $("body").on("click", ".remove_tech_eli_delete", function(){
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
                                                            });
                                                            function addMoreTechEli(){
                                                            technical_counter += 1;
                                                            $('#technical_eligibility_add_more').append('<div class="technical_eligibility" id="technical_eligibility_' + technical_counter + '">' +
                                                                    '<div class="row">' +
                                                                    '<div class="col-md-3">' +
                                                                    '<div class="form-group">' +
                                                                    '<label class="control-label">Document Name</label>' +
                                                                    '<input type="text" class="form-control technical_eligibility_document_name" name="technical_eligibility_document_name[' + technical_counter + ']" id="technical_eligibility_document_name_' + technical_counter + '">' +
                                                                    '</div>' +
                                                                    '</div>' +
                                                                    '<div class="col-md-3">' +
                                                                    '<div class="form-group">' +
                                                                    '<label class="control-label">Document Attechement</label>' +
                                                                    '<input type="file" class="form-control technical_eligibility_document_attechement" name="technical_eligibility_document_attechement[' + technical_counter + ']" id="technical_eligibility_document_attechement_' + technical_counter + '">' +
                                                                    '</div>' +
                                                                    '</div>' +
                                                                    '<div class="col-md-2" style="margin-top: 29px;">' +
                                                                    '<div class="form-group">' +
                                                                    '<button type="button" class="btn btn-danger remove_tech_eli"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>' +
                                                                    '</div>' +
                                                                    '</div>' +
                                                                    '</div>' +
                                                                    '</div>');
                                                            }


//Financial Eligibility Part
                                                            $("body").on("click", ".remove_fina_eli", function(){
                                                            $(this).parents(".financial_eligibility").remove();
                                                            });
                                                            $("body").on("click", ".remove_fina_eli_delete", function(){
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
                                                            });
                                                            function addMoreFinaEli(){
                                                            financial_counter += 1;
                                                            $("#financial_eligibility_add_more").append('<div class="financial_eligibility" id="financial_eligibility_' + financial_counter + '">' +
                                                                    '<div class="row">' +
                                                                    '<div class="col-md-3">' +
                                                                    '<div class="form-group">' +
                                                                    '<label class="control-label">Document Name</label>' +
                                                                    '<input type="text" class="form-control financial_eligibility_document_name" name="financial_eligibility_document_name[' + financial_counter + ']" id="financial_eligibility_document_name_' + financial_counter + '">' +
                                                                    '</div>' +
                                                                    '</div>' +
                                                                    '<div class="col-md-3">' +
                                                                    '<div class="form-group">' +
                                                                    '<label class="control-label">Document Attechement</label>' +
                                                                    '<input type="file" class="form-control financial_eligibility_document_attechement" name="financial_eligibility_document_attechement[' + financial_counter + ']" id="financial_eligibility_document_attechement_' + financial_counter + '">' +
                                                                    '</div>' +
                                                                    '</div>' +
                                                                    '<div class="col-md-2" style="margin-top: 29px;">' +
                                                                    '<div class="form-group">' +
                                                                    '<button type="button" class="btn btn-danger remove_fina_eli"><i class="fa fa-times" aria-hidden="true"></i> Remove</button>' +
                                                                    '</div>' +
                                                                    '</div>' +
                                                                    '</div>' +
                                                                    '</div>');
                                                            }
//tender fee form
                                                            $(".tender_fee_form_btn").on('click', function(){
                                                            if ($("#tender_fee_form").valid()){
                                                            var fee_form = $("#tender_fee_form").serialize();
                                                            $.ajax({
                                                            type : "POST",
                                                                    url : "{{url('save_tender_fee')}}",
                                                                    data : fee_form,
                                                                    success : function(data){
                                                                    console.log(data);
                                                                    alertMassage(data);
                                                                    }
                                                            })
                                                            }
                                                            });
//tender emd form
                                                            $(".tender_emd_form_btn").on('click', function(){
                                                            if ($("#tender_emd_form").valid()){
                                                            var fee_form = $("#tender_emd_form").serialize();
                                                            $.ajax({
                                                            type : "POST",
                                                                    url : "{{url('save_tender_emd')}}",
                                                                    data : fee_form,
                                                                    success : function(data){
                                                                    console.log(data);
                                                                    alertMassage(data);
                                                                    }
                                                            })
                                                            }
                                                            });
//financial eligibility part
                                                            $(".tender_fina_eli_form_btn").on('click', function(){
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
                                                            if ($("#tender_fina_eli_form").valid()){
                                                            var form = $('#tender_fina_eli_form')[0];
                                                            var formData1 = new FormData(form);
                                                            for (i = 0; i >= financial_counter; i++){
                                                            formData1.append('financial_eligibility_document_attechement', $('.financial_eligibility_document_attechement')[1].files[1]);
                                                            }


                                                            $.ajax({
                                                            type : "POST",
                                                                    url : "{{url('tender_fina_eli_sub')}}",
                                                                    data : formData1,
                                                                    processData: false,
                                                                    contentType: false,
                                                                    success : function(data){
                                                                    console.log(data);
                                                                    alertMassage(data);
                                                                    }
                                                            });
                                                            }
                                                            });
                                                            $("#tender_fina_eli_form").validate();
//Technical eligibility part
                                                            $(".tender_tech_eli_form_btn").on('click', function(){
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
                                                            if ($("#tender_tech_eli_form").valid()){
                                                            var form = $('#tender_tech_eli_form')[0];
                                                            var formData = new FormData(form);
                                                            for (i = 0; i >= technical_counter; i++){
                                                            formData.append('technical_eligibility_document_attechement', $('.technical_eligibility_document_attechement')[1].files[1]);
                                                            }


                                                            $.ajax({
                                                            type : "POST",
                                                                    url : "{{url('tender_tech_eli_sub')}}",
                                                                    data : formData,
                                                                    processData: false,
                                                                    contentType: false,
                                                                    success : function(data){
                                                                    console.log(data);
                                                                    alertMassage(data);
                                                                    }
                                                            });
                                                            }
                                                            });
                                                            $("#tender_tech_eli_form").validate();
                                                            function load_datepicker(){
                                                            jQuery('.tender_fee_validity,.tender_emd_validity').datetimepicker({
                                                            // format:'DD-MM-YYYY h:mm a',
                                                            format:'DD-MM-YYYY',
                                                                    minDate : new Date(),
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
// $("#tender_form").validate();
                                                            function alertMassage(data){
                                                            if (data == "success"){
                                                            swal({
                                                            title: "Tender detail save successfully.",
                                                                    //text: "You want to change status of admin user.",
                                                                    // type: "info",
                                                                    showCancelButton: false,
                                                                    confirmButtonColor: "#006600",
                                                                    confirmButtonText: "Okay",
                                                                    closeOnConfirm: true
                                                            });
                                                            } else{
                                                            swal({
                                                            title: "Tender not save try again.",
                                                                    //text: "You want to change status of admin user.",
                                                                    type: "error",
                                                                    showCancelButton: false,
                                                                    confirmButtonColor: "#DD6B55",
                                                                    confirmButtonText: "Okay",
                                                                    closeOnConfirm: true
                                                            });
                                                            }
                                                            }

                                                            $(".technical_eligibility_document_attechement").on('change', function(){
                                                            var te_file_do_id = $(this).attr('data-do_id');
                                                            var te_file_id = $(this).attr('id');
                                                            if (te_file_id){
                                                            files = event.target.files;
                                                            var fd = new FormData();
                                                            fd.append('file_img', $('#' + te_file_id)[0].files[0]);
                                                            fd.append('id', te_file_do_id);
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
                                                            });
//financial_eligibility_document_attechement

                                                            $(".financial_eligibility_document_attechement").on('change', function(){
                                                            var te_file_do_id = $(this).attr('data-do_id');
                                                            var te_file_id = $(this).attr('id');
                                                            if (te_file_id){
                                                            files = event.target.files;
                                                            var fd = new FormData();
                                                            fd.append('file_img', $('#' + te_file_id)[0].files[0]);
                                                            fd.append('id', te_file_do_id);
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
                                                            });

</script>
@endsection
