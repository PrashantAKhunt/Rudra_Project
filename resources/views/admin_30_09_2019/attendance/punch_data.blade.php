<?php
use Illuminate\Support\Facades\Config;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="panel-title">Punch details</h3>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">            

            <div class="panel panel-default">                
                <div class="panel-wrapper collapse in">
                    <table class="table table-hover">
                        <thead>
                            <tr>                                
                                <th>In</th>
                                <th>Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($punch_data as $key => $value){
                                $hours_list = Config::get('app.hours');
                                $minutes_list = Config::get('app.minutes');
                            ?>
                            <tr>
                                <?php if(!empty($value['IN'])){
                                    if($value['IN'] == 'UNSET'){ ?>
                                        <td width="50%" class="block">
                                            <div class="col-sm-3">
                                                <select class="form-control select2 hours" name="hours" >
                                                    @foreach($hours_list as $hoursKey => $hoursValue)
                                                    <option value="{{ $hoursValue }}"> {{ $hoursValue }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control select2 minutes" name="minutes" >
                                                    @foreach($minutes_list as $hoursKey => $hoursValue)
                                                    <option value="{{ $hoursValue }}"> {{ $hoursValue }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control select2 meridian" name="meridian" >
                                                    <option value="AM">AM</option>
                                                    <option value="PM">PM</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <button onClick="setTime(this);" class="btn btn-block btn-success btn-rounded" data-min="{{ $punch_data[$key-1]['OUT'] }}" data-max="{{ $punch_data[$key]['OUT'] }}" data-type="IN" data-id="{{ $id }}" >Save</button>
                                            </div>
                                        </td>
                                    <?php } else { ?>
                                    <td width="50%"><?php echo $value['IN']; ?></td>
                                <?php } } ?>

                                <?php if(!empty($value['OUT'])){ 
                                    if($value['OUT'] == 'UNSET'){ ?>
                                        <td width="50%" class="block">
                                            <div class="col-sm-3">
                                                <select class="form-control select2 hours" name="hours" >
                                                    @foreach($hours_list as $hoursKey => $hoursValue)
                                                    <option value="{{ $hoursValue }}"> {{ $hoursValue }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control select2 minutes" name="minutes" >
                                                    @foreach($minutes_list as $hoursKey => $hoursValue)
                                                        <option value="{{ $hoursValue }}"> {{ $hoursValue }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control select2 meridian" name="meridian" >
                                                    <option value="AM">AM</option>
                                                    <option value="PM">PM</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <button onClick="setTime(this);" class="btn btn-block btn-success btn-rounded" data-min="{{ $punch_data[$key]['IN'] }}" data-max="{{ $punch_data[$key+1]['IN'] }}" data-type="OUT" data-id="{{ $id }}" >Save</button>
                                            </div>
                                            <div class="col-sm-12 hide condition"><span class="help-block">Select time between {{ $punch_data[$key]['IN'] }} To {{ $punch_data[$key+1]['IN'] }}</span></div>
                                        </td>
                                    <?php } else { ?>
                                    <td width="50%"><?php echo $value['OUT']; ?></td>
                                <?php } } ?>

                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>