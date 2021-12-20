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
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div class="row">                    
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
                        <form action="{{ route('admin.update_expense') }}" enctype="multipart/form-data" id="edit_expense" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $expense->id }}" />
                            @csrf
                            <div class="col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" class="form-control amount"  name="amount" id="amount" value="{{ $expense->amount }}" /> 
                                </div>                             
                                <div class="form-group">
                                    <label>Meter Reading Photo <span class="text-muted">Allowed file extensions are png, jpg, jpeg</span></label>
                                    <div> 
                                        <input type="file" name="meter_reading_photo" class="form-control" id="meter_reading_photo"  value="{{ $expense->meter_reading_photo }}" accept="image/x-png, image/jpg, image/jpeg" data-accept="jpg,png,jpeg"/>
                                        <span><a onclick="getBillPhoto('<?php echo asset('storage/'.str_replace('public/','',$expense->meter_reading_photo))?>','Meter Reading Photo')" data-toggle="modal" data-target="#billModal">view photo</a></span>
                                    </div>

                                </div>  
                                <div class="form-group ">
                                    <label>Fuel Price</label>
                                    <input type="text" class="form-control" name="fuel_price" id="fuel_price" value="{{ $expense->fuel_price }}">
                                </div>
                                <br>
                                <div class="form-group ">
                                <label>Accountant</label>
                                    <select class="form-control" id="moniter_user_id" name="moniter_user_id">
                                        <option value="">Select user</option>
                                        <?php
                                        foreach ($users as $key => $usersData) {
                                        ?>
                                        <option <?php if($expense->moniter_user_id==$usersData['id']){ echo "selected";}?> value="<?php echo $usersData['id']?>">{{$usersData['name']}}</option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>       

                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <label>Vehicle Type</label>
                                    <select class="form-control" class="vehicle_type" name="vehicle_type" id="vehicle_type">
                                        @foreach($asset_list as $asset)
                                    <option @if($expense->asset_id==$asset->id) selected="" @endif value="{{ $asset->id }}">{{ $asset->name.' ('.$asset->asset_1.'-'.$asset->fuel_type.')' }}</option>
                                    @endforeach
                                        
                                    </select>
                                </div>
                                <div class="form-group ">
                                    <label>Meter Reading<span id="last_reading"></span></label>
                                    <input type="text" class="form-control" name="meter_reading" id="meter_reading" value="{{ $expense->meter_reading }}">
                                </div>
                                <div class="form-group">
                                    <label>Comment</label>
                                    <textarea name="comment" id="comment" class="form-control" >{{ $expense->comment }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Bill Photo <span class="text-muted">Allowed file extensions are png, jpg, jpeg</span></label>
                                    <div> 
                                        <input type="file" name="bill_photo" class="form-control" id="bill_photo" accept="image/x-png, image/jpg, image/jpeg" data-accept="jpg,png,jpeg"/>
                                        <span><a onclick="getBillPhoto('<?php echo asset('storage/'.str_replace('public/','',$expense->bill_photo))?>','Bill Photo')" data-toggle="modal" data-target="#billModal">view photo</a></span>
                                    </div>                                    
                                </div>
                                @if($expense->status=="Rejected")
                                <button type="submit" class="btn btn-success">Re-Submit</button>
                                @else
                                <button type="submit" class="btn btn-success">Submit</button>
                                @endif
                                <button type="button" onclick="window.location.href ='{{ route('admin.expense') }}'" class="btn btn-default">Cancel</button>
                            </div>
                        </form>                            
                </div>
            </div>
        </div>
    </div>

    <div id="billModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="billModalLabel">Bill Photo</h4>
                    </div>
                    <div class="modal-body" id="userTable">
                       <center><img src="" width="250" height="250px" id="bill_photo_url"></center>
                    </div>
                   
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>          

</div>
@endsection
@section('script')
<script>
    var __dayDiff = 0;
    jQuery('#edit_expense').validate({
        ignore: [],
        rules: {
            vehicle_type: {
                required: true
            },
            comment: {
                required: true,
            },
            meter_reading: {
                required: true,
                number:true
            },
            fuel_price: {
                required: true,
                number:true
            },
            moniter_user_id: {
                required: true,
            },
            amount: {
                required: true,
                number : true
            }
        }
    });

    $('.select2').select2();
    $('.datepicker').datepicker({
        format : 'yyyy-m-d',
    });
    $('.clockpicker').clockpicker({
        donetext: 'Done',    
    });

    function getBillPhoto(img,label) {
        $("#bill_photo_url").attr('src',img);
        $("#billModalLabel").text(label);
    }
</script>
@endsection
