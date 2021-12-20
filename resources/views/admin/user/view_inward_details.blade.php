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
        <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li>
        <li><a href="#">{{ $page_title }}</a></li>
      </ol>
    </div>
    <!-- /.col-lg-12 -->
  </div>
  <!-- /.row -->
  <!-- .row -->
  <div class="row">
    <div class="col-md-12">
      <div class="white-box">
        <a href="<?php if (isset($_SERVER['HTTP_REFERER'])) {
          echo $_SERVER['HTTP_REFERER'];
        }else{
          echo url('admin.inwards');
        } ?>" class="btn btn-info pull-right"><i class="fa fa-arrow-left"></i> BACK</a>
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
        <section class="cd-horizontal-timeline">
          <div class="timeline">
            <div class="events-wrapper">
              <!-- for showing date on timelline -->

              <div class="events">
                <ol>

                  @foreach($received_dates as $key =>$dates)
                  <li><a href="#0" @if($key==0) class="selected" @endif data-date={{ Carbon\Carbon::parse($dates)->format('d/m/Y') }}>{{ Carbon\Carbon::parse($dates)->format('d/M/Y') }}</a></li>
                  @endforeach

                </ol>

                <span class="filling-line" aria-hidden="true"></span>
              </div> <!-- .events -->

            </div> <!-- .events-wrapper -->

            <ul class="cd-timeline-navigation">
              <li><a href="#0" class="prev inactive">Prev</a></li>
              <li><a href="#0" class="next">Next</a></li>
            </ul> <!-- .cd-timeline-navigation -->
          </div> <!-- .timeline -->


          <div class="events-content">
            <!-- showing content related to date -->

            <ol style="padding-inline-start: 0px">
              @foreach($all_inward_outward_data as $key =>$data)

              <li @if($key==$received_dates[0] ) class="selected" @endif data-date={{  Carbon\Carbon::parse($key)->format('d/m/Y') }}>
                @foreach($data as $l=> $list_data)
                <div class="tab-content" style="margin-top:10px;">
                  
                <button class="btn btn-info" title="Inwards No:<?=   $l + 1; ?>"><?=   $l + 1; ?></button>
                  
                  
                  <div class="row">

                        <div class="col-md-3 col-xs-6 b-r"> <strong>Title</strong> <br>
                          <p class="text-muted">{{ $list_data->inward_outward_title }} 
                           @if ($list_data->type ==='Inwards' )
                            <p class="text-success">{{ $list_data->type }}</p>
                          @else
                          <p class="text-danger">{{ $list_data->type }}</p>
                          @endif
                          </p>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r"> <strong>Inward/Outward Number</strong> <br>
                           <p class="text-muted">{{ $list_data->inward_outward_no }}</p>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r"> <strong>{{$module_title}} Date</strong> <br>
                           <p class="text-muted">{{ date('d-m-Y h:i:s a',strtotime($list_data->received_date)) }}</p>
                         </div>
                         <div class="col-md-3 col-xs-6"> <strong>Delivery Mode</strong> <br>
                           <p class="text-muted">{{ $list_data->delivery_mode_name }}</p>
                           <div class="text-center"><a title="Download Cover image" href="{{ asset('storage/'.str_replace('public/','',!empty($list_data->delivery_file) ? $list_data->delivery_file : 'public/no_image')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a></div>
                        </div>

                  </div>
                  @if ($list_data->type ==='Inwards' )
                  <br>
                  <hr class="m-t-0">
                   <!--  -->
                  <div class="row">

                        <div class="col-md-3 col-xs-6 b-r"> <strong>Sender Name</strong> <br>
                          <p class="text-muted">{{ $list_data->sender_name }}</p>  
                        </div>
                        <div class="col-md-3 col-xs-6 b-r"> <strong>Type Of Sender</strong> <br>
                          <p class="text-muted">{{ $list_data->sender_type }}</p>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r"> <strong>Sender Letter/Invoice Date</strong> <br>
                          <p class="text-muted">{{ date('d-m-Y', strtotime($list_data->sender_invoice_date)) }}</p>
                        </div>
                        <div class="col-md-3 col-xs-12"> <strong>Sender Letter/Invoice No</strong> <br>
                          @if($list_data->ref_outward_number)
                          <p class="text-muted">{{ $list_data->ref_outward_number }}</p>
                          @else
                          <!-- <p class="text-muted">NA</p> -->
                          <p class="text-muted">Comment: {{ $list_data->sender_comment }}</p>
                          @endif
                        </div>
                   </div>
                   @endif 
                   <br>
                   <hr class="m-t-0">
                   <div class="row">
                          <div class="col-md-3 col-xs-6 b-r"> <strong>Document Category</strong> <br>
                            <p class="text-muted">{{ $list_data->category_name }}</p>
                          </div>
                          <div class="col-md-3 col-xs-6 b-r"> <strong>Document Sub Category</strong> <br>
                            <p class="text-muted">{{ $list_data->sub_category_name }}</p>
                          </div>
                          <div class="col-md-6 col-xs-12"> <strong>Description</strong> <br>
                            <p class="text-muted">{{ $list_data->description }}</p>
                          </div>
                  </div>
                  
                  <br>
                  <hr class="m-t-0">
                 
                    <div class="row">
                        <div class="col-md-3 col-xs-6 b-r"> <strong>Company</strong> <br>
                          <p class="text-muted">{{ $list_data->company_name }}</p>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r"> <strong>Project</strong> <br>
                          <p class="text-muted">{{ $list_data->project_name }}</p>
                          <p class="text-muted">{{ $list_data->other_project_details }}</p>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r"> <strong>Detail Entered by/Requested By</strong> <br>
                          <p class="text-muted">{{ $list_data->requested_by_name }}</p>
                      </div>
                      <div class="col-md-3 col-xs-6 "> <strong>Document File</strong> <br>
                          <div class="text-center"><a title="Download " href="{{ asset('storage/'.str_replace('public/','',!empty($list_data->document_file) ? $list_data->document_file : 'public/no_image')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a></div>
                      </div>
                   
                        
                  </div>
                  <br>
                  <hr class="m-t-0">
                  <div class="row">
                       <div class="col-md-3 col-xs-6 b-r"> <strong>Number Of Pages</strong> <br>
                          <p class="text-muted">{{ $list_data->pdf_page_no }}</p>
                        </div>
                        <div class="col-md-3 col-xs-6 b-r"> <strong>Document size</strong> <br>
                          <p class="text-muted">{{ $list_data->pdf_size }}</p>
                        </div>
                        <div class="col-md-3 col-xs-6 "> <strong>Expected Answer Datetime</strong> <br>
                           @if(date('d-m-Y',strtotime($list_data->expected_ans_date))=='01-01-1970')
                           <p class="text-muted">NA</p>
                           @else
                          <p class="text-muted">{{ date('d-m-Y h:i a',strtotime($list_data->expected_ans_date)) }}</p>
                          @endif
                       </div>
                       <!-- <div class="col-md-3 col-xs-6"> <strong>Answered</strong> <br>
                          @if ($list_data->is_answered ==='Yes' )
                         <p class="text-success">{{ $list_data->is_answered }}</p>
                         @else
                         <p class="text-danger">{{ $list_data->is_answered }}</p>
                         @endif
                        </div> -->
                  </div>
                  <br>
                  <hr class="m-t-0">
                  <div class="row">

                      <div class="col-md-3 col-xs-6 b-r"> <strong>Department</strong> <br>
                        <p class="text-muted">{{ $list_data->dept_name }}</p>
                      </div>
                      <div class="col-md-3 col-xs-6 b-r"> <strong>First Assigned user</strong> <br>
                
                          @foreach($users_details as $key=> $users_data)
                          @if($key == $list_data->id)
                          @foreach($users_data as $users)
                          <p class="text-muted">{{ $users->name }} </p>
                          @endforeach
                          @endif
                          @endforeach
                  
                      </div>
                      <div class="col-md-3 col-xs-6 b-r"> <strong>Mode Of Document Delivered</strong> <br>
                        <p class="text-muted">@if($list_data->doc_delivery_mode){{ $list_data->doc_delivery_mode }} @else NA @endif</p>
                      </div>
                      <div class="col-md-3 col-xs-6 "> <strong>Allotment Datetime</strong> <br>
                        <p class="text-muted">@if($list_data->doc_allotment_time){{ date('d-m-Y h:i:s',strtotime($list_data->doc_allotment_time)) }} @else NA @endif
                        </p>
                      </div>

                  </div>
                  <br>
                  @if ($list_data->type ==='Inwards' )
                  <br>
                  <hr class="m-t-0">
                  <div class="row">
                      <div class="col-md-9 col-xs-9"> <strong>Employee Type -> Employees -> Work Submit Status</strong> <br>
                        @if($support_users_details)
                          @foreach($support_users_details as $support_emp => $support_users)
                                @if($support_emp == $list_data->id)
                                    @if($support_users)
                                      @foreach($support_users as $emp_key => $support)
                                        <h4 class="text-muted"> @if($emp_key == 0)Prime/Main Employee @else Support Employee @endif  -> {{ $support['name'] }} -> {{ $support['work_status'] }} </h4>
                                      @endforeach
                                    @else
                                    <h4 class="text-muted">NA</h4>
                                    @endif
                                @endif
                          @endforeach
                         @endif
                      </div>


                  </div>
  
                 
                </div>
                <br>
                @endif
                <br>
                @endforeach
              </li>
              @endforeach

            </ol>
          </div> <!-- .events-content -->


        </section>

      </div>
    </div>
  </div>
  <!-- /.row -->


</div>
@endsection


@section('script')


<script src="{{asset('admin_asset/assets/plugins/bower_components/horizontal-timeline/js/horizontal-timeline.js') }}"></script>


@endsection