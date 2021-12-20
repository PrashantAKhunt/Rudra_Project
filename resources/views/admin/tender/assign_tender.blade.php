@extends('layouts.admin_app')

@section('content')
<style type="text/css">
  .tender_div{
        border: 1px solid #a5a0a0;
        padding: 13px;
        margin-bottom: 5px;
    }
  .tender_title {
    background: #cac6c2;
    padding: 5px 0px 4px 6px;
    font-weight: 400;
    font-size: 18px;
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
                <form action="save_tender_assign" method="post" id=assign_tender_form>
                  @csrf
                  @php
                  $default_user = [];
                  $default_name = [];
                  foreach ($default_assign_user as $key1 => $value1) {
                      $default_user[$key1] = $key1;
                      $default_name[$key1] = $value1;
                  }
                  $default_user_string = implode(',', $default_user);
                  @endphp
                  <input type="hidden" name="default_user" value="{{$default_user_string}}">
                  <h3 class="box-title m-t-0">Default employee assign to all this tender</h3>
                  <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <span class="box-title"><strong>Name : </strong></span><label> 
                          {{implode(', ',$default_name)}}
                          </label>
                        </div>
                      </div>
                  </div>
                  @if($select_tender)
                    @foreach($select_tender as $key => $value)
                      <div class="tender_div">
                        <h3 class="tender_title">{{$key+1}}) Tender (Tender Sr No. : {{$value['tender_sr_no']}} - Client Name : {{$value['tender_client'][0]['client_name']}})</h3>
                        {{-- <h3 class="box-title m-t-0">Tender Detail</h3> --}}
                        <input type="hidden" name="tender_id[{{$key}}]" id="" value="{{$value['id']}}">
                        <div class="row">
                            {{-- <div class="col-md-4">
                              <div class="form-group">
                                <label>Tender Sr No. : {{$value['tender_sr_no']}}</label>
                              </div>
                            </div> --}}
                            <div class="col-md-12">
                              <div class="form-group">
                                <span class="box-title"><strong>Tender Work :</strong></span>
                                <p>{{$value['name_of_work']}}</p>
                              </div>
                            </div>
                            {{-- <div class="col-md-4">
                              <div class="form-group">
                                <label></b>Client Name : </b>{{$value['tender_client'][0]['client_name']}}</label>
                              </div>
                            </div> --}}
                        </div>
                        
                        <h3 class="box-title m-t-0">Please select employee you want to assign this tender</h3>
                        <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                @if($simple_assign_user)
                                  @foreach($simple_assign_user as $simple_key => $simple_val)
                                  <input type="checkbox" class="assign_tender_user" name="assign_tender_user[{{$key}}][]" id="{{$simple_key}}" value="{{$simple_key}}">
                                    <label>{{$simple_val}} </label>
                                    <br>
                                  @endforeach
                                @endif
                              </div>
                            </div>
                        </div>
                      </div>
                    @endforeach
                  @endif
                  <button type="submit" class="btn btn-success">Submit</button>
                </form>
            </div>
            <!--row -->
        </div>        
@endsection
@section('script')
<script>
$(document).ready(function () {
  
});
$('form#assign_tender_form').on('submit', function(event) {
  $('.assign_tender_user').each(function() {
      $(this).rules('add', {
          required: true,
          minlength: 1,
      });
  });
});
$("#assign_tender_form").validate();
</script>
@endsection