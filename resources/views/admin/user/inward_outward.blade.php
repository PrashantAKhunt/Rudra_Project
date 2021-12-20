@extends('layouts.admin_app')

@section('content')
<?php

use Illuminate\Support\Facades\Config; ?>

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
        <div class="col-lg-4 col-sm-4 col-xs-12">
          <div class="white-box">
            <a href="{{ route('admin.inwards') }}">
              <h3 class="box-title">Inwards</h3>
              <ul class="list-inline two-part">
                <li><i class="icon-arrow-down-circle text-info"></i></li>
                <li class="text-right"><span class="counter text-success">{{ $inward_count }}</span></li>
              </ul>
            </a>
          </div>
        </div>
        <div class="col-lg-4 col-sm-4 col-xs-12">
          <div class="white-box">
            <a href="{{ route('admin.outwards') }}">
              <h3 class="box-title">Outwards</h3>
              <ul class="list-inline two-part">
                <li><i class="icon-arrow-up-circle text-purple"></i></li>
                <li class="text-right"><span class="counter">{{ $outward_count }}</span></li>
              </ul>
            </a>
          </div>
        </div>

        <div class="col-lg-4 col-sm-4 col-xs-12">
          <div class="white-box">
            <a href="{{ route('admin.pending_registry_documents') }}">
              <h3 class="box-title">Important Registry</h3>
              <ul class="list-inline two-part">
                <li><i class="fa fa-file-pdf-o text-info"></i></li>
                <li class="text-right"><span class="counter text-success">{{ $impo_registry }}</span></li>
              </ul>
            </a>
          </div>
        </div>

        <div class="col-lg-4 col-sm-4 col-xs-12">
          <div class="white-box">
            <a href="{{ route('admin.assignee_registry') }}">
              <h3 class="box-title">Registry Assigned To You</h3>
              <ul class="list-inline two-part">
                <li><i class="fa fa-file-pdf-o text-info"></i></li>
                <li class="text-right"><span class="counter text-success">{{ $assignee_registry_count }}</span></li>
              </ul>
            </a>
          </div>
        </div>

        <div class="col-lg-4 col-sm-4 col-xs-12">
          <div class="white-box">
            <a href="{{ route('admin.prelimary_action_list') }}">
              <h3 class="box-title">Action Required Inwards</h3>
              <ul class="list-inline two-part">
                <li><i class="fa fa-file-pdf-o text-purple"></i></li>
                <li class="text-right"><span class="counter">{{ $process_list }}</span></li>
              </ul>
            </a>
          </div>
        </div>

     
        <div class="col-lg-4 col-sm-4 col-xs-12">
          <div class="white-box">
            <a href="{{ route('admin.prime_action_list') }}">
              <h3 class="box-title">Supporting/Prime Employee Documents</h3>
              <ul class="list-inline two-part">
                <li><i class="fa fa-file-pdf-o text-info"></i></li>
                <li class="text-right"><span class="counter">{{ $all_state_count }}</span></li>
              </ul>
            </a>
          </div>
        </div>


      
        <div class="col-lg-4 col-sm-4 col-xs-12">
          <div class="white-box">
            <a href="{{ route('admin.managment_view_list') }}">
              <h3 class="box-title">Task Distribution</h3>
              <ul class="list-inline two-part">
                <li><i class="fa fa-file-pdf-o text-danger"></i></li>
                <li class="text-right"><span class="counter text-white">*</span></li>
              </ul>
            </a>
          </div>
        </div>
    

        <div class="col-lg-4 col-sm-4 col-xs-12">
          <div class="white-box">
            <a href="{{ route('admin.registry_search') }}">
              <h3 class="box-title">Registry Search</h3>
              <ul class="list-inline two-part">
                <li><i class="fa fa-search"></i></li>
                <li class="text-right"><span class="counter text-white">*</span></li>
              </ul>
            </a>
          </div>
        </div>

      </div>

      <!-- <div class="row">
       
      </div> -->


    </div>
  </div>

  <input type="hidden" name="education_div_count" id="education_div_count" value="0" />
  <input type="hidden" name="experience_div_count" id="experience_div_count" value="0" />
</div>
@endsection


@section('script')


@endsection