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
        <li><a href="{{ route('admin.inwards') }}">{{ $module_title }}</a></li>
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
        <button type="button" onclick="window.location.href ='{{ route('admin.inwards') }}'" class="btn btn-info pull-right"><i class="fa fa-arrow-left"></i> BACK</button>
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
                  <li><a href="#0" @if($key==0) class="selected" @endif data-date={{ Carbon\Carbon::parse($dates)->format('d/m/Y') }}>{{ Carbon\Carbon::parse($dates)->format('d/M') }}</a></li>
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
                      <p class="text-muted">{{ $list_data->inward_outward_title }}</p>
                    </div>
                    <div class="col-md-3 col-xs-6 b-r"> <strong>Type</strong> <br>

                      @if ($list_data->type ==='Inwards' )
                      <p class="text-success">{{ $list_data->type }}</p>
                      @else
                      <p class="text-danger">{{ $list_data->type }}</p>
                      @endif

                    </div>
                    <div class="col-md-3 col-xs-6 b-r"> <strong>Category</strong> <br>
                      <p class="text-muted">{{ $list_data->category_name }}</p>
                    </div>
                    <div class="col-md-3 col-xs-6"> <strong>Company</strong> <br>
                      <p class="text-muted">{{ $list_data->company_name }}</p>
                    </div>
                  </div>
                  <br>
                  <hr class="m-t-0">

                    <div class="row">

                    <div class="col-md-4 col-xs-6 b-r"> <strong>External Outward Number</strong> <br>
                      <p class="text-muted">{{ $list_data->ref_outward_number }}</p>
                    </div>
                    <div class="col-md-4 col-xs-6 b-r"> <strong>Number</strong> <br>
                      <p class="text-muted">{{ $list_data->inward_outward_no }}</p>
                    </div>
                    <div class="col-md-4 col-xs-6"> <strong>Answered</strong> <br>
                     @if ($list_data->is_answered ==='Yes' )
                      <p class="text-success">{{ $list_data->is_answered }}</p>
                      @else
                      <p class="text-danger">{{ $list_data->is_answered }}</p>
                      @endif
                    </div>
                  </div>
                  <br>
                  <hr class="m-t-0">

                  <div class="row">

                    <div class="col-md-3 col-xs-6 b-r"> <strong>Recieved Date</strong> <br>
                      <p class="text-muted">{{ date('d-m-Y',strtotime($list_data->received_date)) }}</p>
                    </div>
                    <div class="col-md-3 col-xs-6 b-r"> <strong>Expected Answer Date</strong> <br>
                        @if(date('d-m-Y',strtotime($list_data->expected_ans_date))=='01-01-1970')
                        <p class="text-muted">NA</p>
                        @else
                      <p class="text-muted">{{ date('d-m-Y',strtotime($list_data->expected_ans_date)) }}</p>
                      @endif
                    </div>
                    <div class="col-md-3 col-xs-6"> <strong>Description</strong> <br>
                      <p class="text-muted">{{ $list_data->description }}</p>
                    </div>
                  </div>
                  <br>
                  <hr class="m-t-0">

                  <div class="row">

                    <div class="col-md-3 col-xs-12 b-r"> <strong>Project</strong> <br>
                      <p class="text-muted">{{ $list_data->project_name }}</p>
                      <p class="text-muted">{{ $list_data->other_project_details }}</p>
                    </div>
                    <div class="col-md-3 col-xs-12 b-r"> <strong>File</strong> <br>

                      <div class="text-center"><a title="Download image" href="{{ asset('storage/'.str_replace('public/','',!empty($list_data->document_file) ? $list_data->document_file : 'public/no_image')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a></div>
                    </div>
                    <div class="col-md-6 col-xs-12"> <strong>Assign Users Status</strong> <br>
                      <div class="col-lg-9 col-md-4 col-sm-4">
                        @foreach($users_details as $key=> $users_data)

                        @if($key == $list_data->id)

                        @foreach($users_data as $users)
                        @if ($users->status ==='Processing' )
                        <h5>{{ $users->name }} <span class="pull-right">Processing</span></h5>
                        <div class="progress">
                          <div class="progress-bar progress-bar-success" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 45%" role="progressbar"> <span class="sr-only">85% Complete (success)</span> </div>
                        </div>
                        @if(Auth::user()->id==$users->user_id)
                        <a href="#" class="btn btn-default btn-sm" onclick="pass_next(this);" data-href="<?php echo url('pass_registry') ?>/{{ $list_data->parent_inward_outward_no}}/{{ $list_data->id}}">Pass to Next...</a>
                        @endif
                        @elseif ($users->status ==='Completed')
                        <h5>{{ $users->name }}<span class="pull-right">Completed</span></h5>
                        <div class="progress">
                          <div class="progress-bar progress-bar-info" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 100%" role="progressbar"> <span class="sr-only">85% Complete (success)</span> </div>
                        </div>

                        @else
                        <h5>{{ $users->name }}<span class="pull-right">Pending</span></h5>
                        <div class="progress">
                          <div class="progress-bar progress-bar-warning" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 10%" role="progressbar"> <span class="sr-only">85% Complete (success)</span> </div>
                        </div>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                      </div>

                    </div>
                  </div>
                </div>
                <br>
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
<script>
  function pass_next(e) {
    swal({
      title: "Are you sure  ?",
      //text: "You want to change status of admin user.",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes",
      closeOnConfirm: false
    }, function() {
      window.location.href = $(e).attr('data-href');
    });
  }
</script>

<script src="{{asset('admin_asset/assets/plugins/bower_components/horizontal-timeline/js/horizontal-timeline.js') }}"></script>


@endsection