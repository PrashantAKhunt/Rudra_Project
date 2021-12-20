@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Dashboard</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>

            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="white-box">
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
                <div class="row row-in">
                    <div class="col-md-5 col-lg-3 col-sm-6 col-xs-12">
                      <div class="bg-theme-dark m-b-15">
                        <h3 class="text-white" style="margin-left: 10px"><b>Leave</b></h3>
                        <div class="row weather p-20">
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 m-t-40">
                            <h3>&nbsp;</h3>
                            <h1>1:30<sup>PM</sup></h1>
                            <p><span class="label label-info label-rounded">Clock-Out</span>&nbsp<span class="label label-info label-rounded">Clock-Out</span></p>
                            <br>
                            <p><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">5:25 Avg Hours</button></p>
                            <p class="text-white"><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">0% Time Arrival</button></p>
                          </div>
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 text-right"> <i class="fa fa-clock-o" aria-hidden="true"></i><br>
                            <br>
                            <b class="text-white">April 14,2019</b>
                            <!-- <p class="w-title-sub">April 14,2019</p> -->
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-5 col-lg-3 col-sm-6 col-xs-12">
                      <div class="bg-theme-dark m-b-15" style="background-color:#b9699d !important;">
                        <h3 class="text-white" style="margin-left: 10px"><b>Leave Approvals</b></h3>
                        <div class="row weather p-20">
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 m-t-40">
                            <p><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">1 Leave</button></p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-5 col-lg-3 col-sm-6 col-xs-12">
                      <div class="bg-theme-dark m-b-15" style="background-color:#697eb9 !important;">
                        <h3 class="text-white" style="margin-left: 10px"><b>Working Remotely</b></h3>
                        <div class="row weather p-20">
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 m-t-40">
                            <h3>&nbsp;</h3>
                            <br>
                            <p><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">5:25 Hours</button></p>
                            <p class="text-white"><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">0% Time Arrival</button></p>
                          </div>
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 text-right"> <i class="fa fa-clock-o" aria-hidden="true"></i><br>
                            <br>
                            <b class="text-white">April 14,2019</b>
                            <!-- <p class="w-title-sub">April 14,2019</p> -->
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-5 col-lg-3 col-sm-6 col-xs-12">
                      <div class="bg-theme-dark m-b-15" style="background-color:#01c8ac !important;">
                        <h3 class="text-white" style="margin-left: 10px"><b>Attendance</b></h3>
                        <div class="row weather p-20">
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 m-t-40">
                            <h3>&nbsp;</h3>
                             
                             
                            <br>
                            <p><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">25 Present</button></p>
                            <p class="text-white"><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">2 Absent</button></p>
                          </div>
                          <br>
                            <br>
                            
                            <!-- <p class="w-title-sub">April 14,2019</p> -->
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-5 col-lg-3 col-sm-6 col-xs-12">
                      <div class="bg-theme-dark m-b-15" style="background-color:#9769b9 !important;">
                        <h3 class="text-white" style="margin-left: 10px"><b>On Leave Today</b></h3>
                        <div class="row weather p-20">
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 m-t-40">
                            <p><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">12 Leave</button></p>
                          </div>
                        </div>
                      </div>
                    </div>

                    

                    <div class="col-md-5 col-lg-3 col-sm-6 col-xs-12">
                      <div class="bg-theme-dark m-b-15" style="background-color:#69a3b9 !important;">
                        <h3 class="text-white" style="margin-left: 10px"><b>Holidays</b></h3>
                        <div class="row weather p-20">
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 m-t-40">
                            <h3>&nbsp;</h3>
                            <h1>1:30<sup>PM</sup></h1>
                            <p><span class="label label-info label-rounded">Clock-Out</span>&nbsp<span class="label label-info label-rounded">Clock-Out</span></p>
                            <br>
                            <p><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">5:25 Avg Hours</button></p>
                            <p class="text-white"><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">0% Time Arrival</button></p>
                          </div>
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 text-right"> <i class="fa fa-clock-o" aria-hidden="true"></i><br>
                            <br>
                            <b class="text-white">April 14,2019</b>
                            <!-- <p class="w-title-sub">April 14,2019</p> -->
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- <div class="col-md-5 col-lg-3 col-sm-6 col-xs-12">
                      <div class="bg-theme-dark m-b-15" style="background-color:#9ab969 !important;">
                        <h3 class="text-white" style="margin-left: 10px"><b>Employee</b></h3>
                        <div class="row weather p-20">
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 m-t-40">
                            <h3>&nbsp;</h3>
                            <h1>1:30<sup>PM</sup></h1>
                            <p><span class="label label-info label-rounded">Clock-Out</span>&nbsp<span class="label label-info label-rounded">Clock-Out</span></p>
                            <br>
                            <p><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">5:25 Avg Hours</button></p>
                            <p class="text-white"><button class="btn btn-block btn-warning btn-rounded" style="width: 170px;">0% Time Arrival</button></p>
                          </div>
                          <div class="col-md-6 col-xs-6 col-lg-6 col-sm-6 text-right"> <i class="fa fa-clock-o" aria-hidden="true"></i><br>
                            <br>
                            <b class="text-white">April 14,2019</b>
                          </div>
                        </div>
                      </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <!--row -->

</div>
@endsection
@section('script')
<script type="text/javascript">
  
   $(document).ready(function() {
      $.toast({
        heading: 'Birthday',
        text: 'Today is birthday of Parth',
        position: 'top-right',
        loaderBg:'#ff6849',
        icon: 'success',
        hideAfter: 60000, 
        
        stack: 6
      });
      $.toast({
        heading: 'Leave Application',
        text: 'Your leave application is approved.',
        position: 'top-right',
        loaderBg:'#ff6849',
        icon: 'success',
        hideAfter: 60000, 
        
        stack: 6
      })
    });
</script>
@endsection
<style type="text/css">
.bg-theme-dark.m-b-15 {
    height: 370px;
}
</style>
