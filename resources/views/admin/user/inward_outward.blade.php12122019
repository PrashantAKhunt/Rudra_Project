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


    <div class="row">

    <div class="col-lg-12 col-sm-12 col-xs-12">
          <div class="row">
          <div class="col-lg-6 col-sm-6 col-xs-12">
          <div class="white-box">
          <a href="{{ route('admin.inwards') }}">
            <h3 class="box-title">Inwards</h3>
            <ul class="list-inline two-part">
              <li><i class="icon-arrow-down-circle text-info"></i></li>
              <li class="text-right"><span class="counter text-success">{{  $inward_count }}</span></li>
            </ul>
            </a>
          </div>
        </div>
            <div class="col-lg-6 col-sm-6 col-xs-12">
              <div class="white-box">
              <a href="{{ route('admin.outwards') }}">
                <h3 class="box-title">Outwards</h3>
                <ul class="list-inline two-part">
                  <li><i class="icon-arrow-up-circle text-purple"></i></li>
                  <li class="text-right"><span class="counter">{{  $outward_count }}</span></li>
                </ul>
              </a>
              </div>
            </div>
          <!--  <div class="col-lg-6 col-sm-6 col-xs-12">
              <div class="white-box">
                <h3 class="box-title">Unread Registry</h3>
                <ul class="list-inline two-part">
                  <li><i class="ti-comment text-danger"></i></li>
                  <li class="text-right"><span class="">0</span></li>
                </ul>
              </div>
            </div>
            <div class="col-lg-6 col-sm-6 col-xs-12">
              <div class="white-box">
                <h3 class="box-title">Unread Messages</h3>
                <ul class="list-inline two-part">
                  <li><i class=" ti-comments text-success"></i></li>
                  <li class="text-right"><span class="">0</span></li>
                </ul>
              </div>
            </div>  -->
          </div>
        </div>
    </div>

    <input type="hidden" name="education_div_count" id="education_div_count" value="0" />
    <input type="hidden" name="experience_div_count" id="experience_div_count" value="0" />
</div>
@endsection


@section('script')


@endsection
