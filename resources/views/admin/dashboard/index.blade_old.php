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
        </div>
        <div class="col-md-6">
            <div class="white-box">
                <h3 class="box-title">Leave</h3>
                <div class="table-responsive">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>Reason</th>
                                <th>DATE</th>
                                <th>Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($current_leave_list))
                            <tr>
                                <td colspan="4">No record available.</td>
                            </tr>
                            @endif
                            <?php
                            foreach ($current_leave_list as $key => $leaveList) {
                                ?>
                                <tr>
                                    <td class="txt-oflo"><?php echo $leaveList['name'] ?> </td>
                                    <td><span class="label label-megna label-rounded"><?php echo $leaveList['subject'] ?></span> </td>
                                    <td class="txt-oflo"><?php echo $leaveList['start_date'] ?></td>
                                    <td class="txt-oflo"><?php echo $leaveList['end_day']; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <a href="{{ route('admin.leave') }}">View more</a> </div>
            </div>
        </div>
        @if($show_leave_approvals)
        <div class="col-md-6" id="leave_app">
            <div class="white-box">
                <h3 class="box-title">Leave Approvals </h3>
                <div class="table-responsive">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>Reason</th>
                                <th>DATE</th>
                               <!--  <th>Days</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @if($approval_leave_list->count()==0)
                            <tr>
                                <td colspan="3">No record available.</td>
                            </tr>
                            @endif
                            <?php
                            foreach ($approval_leave_list as $key => $approvalData) {
                                ?>
                                <tr>

                                    <td class="txt-oflo"><?php echo $approvalData['name']; ?></td>
                                    <td><span class="label label-megna label-rounded"><?php echo $approvalData['subject']; ?></span> </td>
                                    <td class="txt-oflo"><?php echo $approvalData['start_date']; ?></td>
                                    <!-- <td class="txt-oflo"><?php echo $approvalData['end_day']; ?></td> -->
                                </tr>
                                <?php }
                            ?>
                        </tbody>
                    </table>
                    <a href="{{ route('admin.all_leave') }}">View more</a> </div>
            </div>
        </div>
        @endif
    </div>
    <br><br>
    <div class="row">
        <div class="col-md-6" id="workApp">
            <div class="white-box">
                <h3 class="box-title">Work Remotely</h3>
                <div class="table-responsive">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>Punch Type</th>
                                <th>Time</th>
                                <th>IsAapproved</th>
                                <th>Available Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if(empty($remote_user_list))
                            <tr>
                                <td colspan="5">No record available.</td>
                            </tr>
                            @endif

                            <?php
                            foreach ($remote_user_list as $key => $remoteData) {
                                ?>
                                <tr>
                                    <td class="txt-oflo"><?php echo $remoteData['name']; ?></td>
                                    <td><span class="label label-megna label-rounded"><?php echo $remoteData['punch_type']; ?></span></td>
                                    <td class="txt-oflo"><?php echo $remoteData['time']; ?></td>
                                    <td class="txt-oflo"><?php echo $remoteData['is_approved']; ?></td>
                                    <?php
                                    if ($remoteData['availability_status'] == '1')
                                        $status = 'present';
                                    if ($remoteData['availability_status'] == '2')
                                        $status = 'pending';
                                    if ($remoteData['availability_status'] == '3')
                                        $status = 'leave';
                                    if ($remoteData['availability_status'] == '4')
                                        $status = 'holiday';
                                    if ($remoteData['availability_status'] == '5')
                                        $status = 'weekend';
                                    if ($remoteData['availability_status'] == '6')
                                        $status = 'mix leave';
                                    ?>
                                    <td class="txt-oflo"><span class="label label-megna label-rounded"><?php echo $status; ?></span></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <a href="#">View more</a> </div>
            </div>
        </div>
        <div class="col-md-6" id="expenseApp">
            <div class="white-box">
                <h3 class="box-title">Expense Approval</h3>
                <div class="table-responsive">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>DATE</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($expense_approval_list))
                            <tr>
                                <td colspan="3">No record available.</td>
                            </tr>
                            @endif
                            <?php
                            foreach ($expense_approval_list as $key => $approvalData) {
                                if (!empty($approvalData['name'])) {
                                    ?>
                                    <tr>
                                        <td class="txt-oflo"><?php echo $approvalData['name']; ?></td>
                                        <td class="txt-oflo"><?php echo $approvalData['expense_date']; ?></td>
                                        <?php
                                        if ($approvalData['status'] == 'Rejected') {
                                            $class = 'label-danger';
                                        } elseif ($approvalData['status'] == 'Approved') {
                                            $class = 'label-success';
                                        } else {
                                            $class = 'label-info';
                                        }
                                        ?>
                                        <td><span class="label <?php echo $class; ?> rounded"><?php echo (!empty($approvalData['status']) ? $approvalData['status'] : 'Pending'); ?></span></td>
                                    </tr>
                                    <?php }
                                } ?>
                        </tbody>
                    </table>
                    <a href="#">View more</a> </div>
            </div>
        </div>
    </div>
    <br><br>
    <div class="row">

        <div class="col-md-6">
            <div class="white-box">
                <h3 class="box-title">Holiday</h3>
                <div class="table-responsive">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>FESTIVAL NAME</th>
                                <th>START DATE</th>
                                <th>END DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($holidays))
                            <tr>
                                <td colspan="3">No record available.</td>
                            </tr>
                            @endif
<?php
foreach ($holidays as $key => $holidaysData) {
    ?>
                                <tr>   
                                    <td class="txt-oflo"><?= $holidaysData['title']; ?></td>
                                    <td class="txt-oflo"><?= $holidaysData['start_date']; ?></td>
                                    <td class="txt-oflo"><?= $holidaysData['end_date']; ?></td>
    <?php
}
?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6" id="bodDive">
            <div class="white-box">
                <h3 class="box-title">Upcomming Birtday & Anniversary</h3>
                <div class="table-responsive">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>Type</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($birthdays) && empty($marriage) && empty($work))
                            <tr>
                                <td colspan="2">No record available</td>
                            </tr>
                            @endif
<?php
foreach ($birthdays as $key => $birthdaysData) {
    ?>
                                <tr>
                                    <td class="txt-oflo"><?php echo $birthdaysData['user']['name']; ?></td>
                                    <td><span class="label label-megna label-rounded"><i class="fa fa-birthday-cake" aria-hidden="true"></i></span> </td>
                                </tr>
<?php } ?>

                            <?php
                            foreach ($marriage as $key => $marriageData) {
                                ?>
                                <tr>
                                    <td class="txt-oflo"><?php echo $marriageData['user']['name']; ?></td>
                                    <td><img width="30px;" height="50px;" src="https://cdn.iconscout.com/icon/premium/png-256-thumb/marriage-1433898-1212219.png"></td>
                                </tr>
<?php } ?>

                            <?php
                            foreach ($work as $key => $workData) {
                                ?>
                                <tr>
                                    <td class="txt-oflo"><?php echo $workData['user']['name']; ?></td>
                                    <td><span class="label label-megna label-rounded"><i class="ti-medall"></i></span> </td>
                                </tr>
<?php } ?>

                        </tbody>
                    </table>
                    <a href="#"> </a> </div>
            </div>
        </div>
        <!--row -->
        @if(Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.Admin') || Auth::user()->role==config('constants.SuperUser'))
        <div class="row">
            <div class="col-md-12" >
                <div class="white-box">
                    <h3 class="box-title">Work Remotely Locator</h3>
                    <div id="map" class="gmaps" style="min-height:700px;"></div>
                </div>
            </div>

        </div>
        @endif

    </div>



    @endsection
    @section('script')
    <script type="text/javascript">

        $(document).ready(function() {
        @foreach($all_notify_list as $notify)
                $.toast({
                heading: "{{$notify['title']}}",
                        text: "{{$notify['message']}}",
                        position: 'top-right',
                        //loaderBg:'#ff6849',
                        icon: 'info',
                        hideAfter: false,
                        textColor: 'white',
                        stack: 100
                });
        @endforeach
        });</script>
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyDgkVVgz-uXLTW-_UEwHw0CWx9EBdY2L-E" type="text/javascript"></script>

    <script type="text/javascript">
        var markers = [];
        var locations = [
<?php foreach ($graphDetails as $key => $loco) { ?>
            ['<?php echo $loco['name'] . '(' . $loco['remote_punch_reason'] . ')(' . $loco['punch_type'] . ')' ?>', <?php echo $loco['attend_latitude'] ?>, <?php echo $loco['attend_longitude'] ?>, <?php echo $key + 1; ?>],
<?php } ?>
        ];
        var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 6,
                center: new google.maps.LatLng(23.033863, 72.585022),
                mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        var infowindow = new google.maps.InfoWindow();
        var marker, i;
        for (i = 0; i < locations.length; i++) {

        marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map,
                icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/library_maps.png'
        });
        google.maps.event.addListener(marker, 'click', (function (marker, i) {
        return function () {
        infowindow.setContent('<strong>' + locations[i][0] + '</strong>');
        infowindow.open(map, marker);
        }
        })(marker, i));
        markers.push(marker);
        }
    </script>
    @endsection
    <style type="text/css">
        .bg-theme-dark.m-b-15 {
            height: 370px;
        }
        div#leave_app {
            float: left;
            overflow-y: auto;
            height: 270px;
        }
        div#expenseApp {
            float: left;
            overflow-y: auto;
            height: 270px;
            /*width: 410px;*/
        }

    </style>
