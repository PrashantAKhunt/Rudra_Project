<?php
use Illuminate\Support\Facades\Config;
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="panel-title">Attendance details</h3>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">            
            <div class="panel panel-default">                
                <div class="panel-wrapper collapse in">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Availability</th>
                                <th>First In</th>
                                <th>Last Out</th>
                                <th>Total Hours</th>
                                <th>Is Late 09:30</th>
                                <th>Is Late 09:45</th>
                                <th>Late Time</th>
                                <th>Manual Add Reason</th>
                                <th>Manual Add By</th>
                                <th>Late Mark Removed Detail</th>
                                <th>Late Mark Removed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($attendanceDetails)){
                                    foreach($attendanceDetails as $key => $value){ ?>
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime($value['date'])) }}</td>
                                    <td>{{ config('constants.AVAILABILITY_STATUS')[$value['availability_status']] }}</td>
                                    <td>{{ date("g:i A", strtotime($value['first_in'])) }}</td>
                                    <td>{{ date("g:i A", strtotime($value['last_out'])) }}</td>
                                    <td>{{ $value['total_hours'] }}</td>
                                    <td>{{ $value['is_late'] }}</td>
                                    <td>{{ $value['is_late_more'] }}</td>
                                    <td>{{ $value['late_time'] }}</td>
                                    <td>{{ $value['manual_add_reason'] }}</td>
                                    <td>{{ $value['manual_by']['name'] }}</td>
                                    <td>{{ $value['late_mark_removed_detail'] }}</td>
                                    <td>{{ $value['late_marked_by']['name'] }}</td>
                                </tr>
                            <?php } 
                                } else { ?>
                                <tr>
                                    <td colspan="12">No Record Found!</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>