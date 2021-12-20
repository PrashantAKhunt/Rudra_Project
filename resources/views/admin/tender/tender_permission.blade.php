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
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
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
            <div class="white-box">
                <h3 class="box-title m-t-0">Add Tender Permission<span class="error">*</span></h3>
                <div class="row">
                    <form id="add_tender_form" method="post">
                    @csrf
                    <input type="hidden" name="form_name" value="add_tender">
                      <div class="col-md-6">
                        <div class="form-group">
                          <select class="form-control add_tender_permission required" name="add_tender_permission" id="add_tender_permission">
                              <option value="">Select Employee</option>
                              @if($users)
                                @foreach($users as $key => $value)
                                <option value="{{$key}}" {{$add_user_permission == $key ? "selected" : ""}}>{{$value}}</option>
                                @endforeach
                              @endif
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <button type="button" class="btn btn-primary add_tender_form_btn">Save</button>
                        </div>
                      </div>
                    </form>
                </div>
                <h3 class="box-title m-t-0">Edit Tender Permission<span class="error">*</span></h3>
                <div class="row">
                    <form id="edit_tender_form" method="post">
                    @csrf
                    <input type="hidden" name="form_name" value="edit_tender">
                      <div class="col-md-6">
                        <div class="form-group">
                          <select class="form-control edit_tender_permission required" name="edit_tender_permission" id="edit_tender_permission">
                              <option value="">Select Employee</option>
                              @if($users)
                                @foreach($users as $key => $value)
                                <option value="{{$key}}" {{$edit_user_permission == $key ? "selected" : ""}}>{{$value}}</option>
                                @endforeach
                              @endif
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <button type="button" class="btn btn-primary edit_tender_form_btn">Save</button>
                        </div>
                      </div>
                  </form>
                </div>
                <h3 class="box-title m-t-0">Default Tender Assign User<span class="error">*</span></h3>
                <div class="row">
                    <form id="default_assign_user_form" method="post">
                    @csrf
                    <input type="hidden" name="form_name" value="default_assign_user">
                      <div class="col-md-6">
                        <div class="form-group">
                          <select class="select2 m-b-10 select2-multiple default_tender_user required" name="default_tender_user[]" id="default_tender_user" multiple="multiple" data-placeholder="Select User">
                              @if($users)
                                @foreach($users as $key => $value)
                                <option value="{{$key}}" {{in_array($key, $default_assign_user) ? "selected" : ""}}>{{$value}}</option>
                                @endforeach
                              @endif
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <button type="button" class="btn btn-primary default_assign_user_form_btn">Save</button>
                        </div>
                      </div>
                  </form>
                </div>
                <h3 class="box-title m-t-0">Tender Assign User<span class="error">*</span></h3>
                <div class="row">
                    <form id="simple_assign_user_form" method="post">
                    @csrf
                    <input type="hidden" name="form_name" value="simple_assign_user">
                      <div class="col-md-6">
                        <div class="form-group">
                          <select class="select2 m-b-10 select2-multiple tender_user required" name="tender_user[]" id="tender_user" multiple="multiple" data-placeholder="Select User">
                              @if($users)
                                @foreach($users as $key => $value)
                                <option value="{{$key}}" {{in_array($key, $simple_assign_user) ? "selected" : ""}}>{{$value}}</option>
                                @endforeach
                              @endif
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <button type="button" class="btn btn-primary simple_assign_user_form_btn">Save</button>
                        </div>
                      </div>
                  </form>
                </div>
            </div>
            <!--row -->
        </div>        
@endsection
@section('script')
<script>
$(document).ready(function () {
  $("#default_tender_user").select2();  
  $("#tender_user").select2();  
})

//Add Tender Permission
$(".add_tender_form_btn").on('click',function(){
    if($("#add_tender_form").valid())
    {
        $(".add_tender_form_btn").attr("disabled", true);
        var tender_form = $("#add_tender_form").serialize();
        $.ajax({
            type : "POST",
            url : "{{url('save_tender_permission')}}",
            data : tender_form,
            success : function(data){
                $(".add_tender_form_btn").attr("disabled", false);
                alertMassage(data);
            }
        });
    }
});

//Edit Tender Permission
$(".edit_tender_form_btn").on('click',function(){
    if($("#edit_tender_form").valid())
    {
        $(".edit_tender_form_btn").attr("disabled", true);
        var tender_form = $("#edit_tender_form").serialize();
        $.ajax({
            type : "POST",
            url : "{{url('save_tender_permission')}}",
            data : tender_form,
            success : function(data){
                // console.log(data);
                $(".edit_tender_form_btn").attr("disabled", false);
                alertMassage(data);
            }
        });
    }
});



//Default Assign User
$(".default_assign_user_form_btn").on('click',function(){
    if($("#default_assign_user_form").valid()){
        $(".default_assign_user_form_btn").attr("disabled", true);
        var tender_form = $("#default_assign_user_form").serialize();
        $.ajax({
            type : "POST",
            url : "{{url('save_tender_permission')}}",
            data : tender_form,
            success : function(data){
                // console.log(data);
                $(".default_assign_user_form_btn").attr("disabled", false);
                alertMassage(data);
            }
        });
    }
});

$("#default_assign_user_form").validate({
    ignore: [],
});

//Simple Assing User
$(".simple_assign_user_form_btn").on('click',function(){
    if($("#simple_assign_user_form").valid()){
        $(".simple_assign_user_form_btn").attr("disabled", true);
        var tender_form = $("#simple_assign_user_form").serialize();
        $.ajax({
            type : "POST",
            url : "{{url('save_tender_permission')}}",
            data : tender_form,
            success : function(data){
                // console.log(data);
                $(".simple_assign_user_form_btn").attr("disabled", false);
                alertMassage(data);
            }
        });
    }
});

$("#simple_assign_user_form").validate({
    ignore: [],
});
function alertMassage(data){
    if(data == "success"){
        swal("Tender permission save successfully.", "", "success");
    }else{
        swal({
            title: "Tender permission not save try again.",
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