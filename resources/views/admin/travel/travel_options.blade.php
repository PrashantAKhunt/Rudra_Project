@extends('layouts.admin_app')
@section('content')
<style>
    /*  .radio input[type="radio"] {
        cursor: pointer;
        opacity: 0;
        z-index: 1;
        background-color: #d1d3d1;
        outline: none !important;
    }

    :checked+label {
        color: blueviolet;

    } */
    .radio-item {
        display: inline-block;
        position: relative;
        padding: 0 6px;
        margin: 10px 0 0;
    }

    .radio-item input[type='radio'] {
        display: none;
    }

    .radio-item label {
        color: #04c1de;
        font-weight: normal;
    }

    .radio-item label:before {
        content: " ";
        display: inline-block;
        position: relative;
        top: 5px;
        margin: 0 5px 0 0;
        width: 20px;
        height: 20px;
        border-radius: 11px;
        border: 2px solid #04c1de;
        background-color: transparent;
    }

    .radio-item input[type=radio]:checked+label:after {
        border-radius: 11px;
        width: 12px;
        height: 12px;
        position: absolute;
        top: 9px;
        left: 10px;
        content: " ";
        display: block;
        border: 2px solid #004c97;
        background: #004c97;
    }
</style>
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
                    <div class="col-sm-12 col-xs-12">
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
                        <form id="short_by" action="{{ route('admin.get_travel_options',$travel_option[0]->travel_id ) }}" method="get">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="col-sm-2">Sort By</label>
                                        <div class="col-sm-4">
                                            <select class="form-control" onchange="$('#short_by').submit()" name="column_name" id="column_name">
                                                @foreach($sort_by_options as $key => $value)
                                                <option value="{{ $key }}" @if($key==$selected_option) selected @endif>{{ $value }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <br>
                        <div class="tab-content">
                            <!-- <form method="post" action="{{route('admin.approve_employee_expence_multiple')}}" >
                        @csrf -->
                            @foreach($travel_option as $key => $travel_data)
                            <div class="tab-pane fade active in">
                                <!--  <button class="btn btn-info" title="Inwards No:<?= $key + 1; ?>"><?= $key + 1; ?></button> -->

                                <!-- <div class="radio radio-primary">

                                    <input @if($key==0 ) checked="checked" @endif type="radio" name="option_id" id="option_id{{$key+1}}" value="{{ $travel_data->id}}">
                                     <input type="hidden" name="travel_id" id="radio5" value="{{ $travel_data->travel_id}}"> 
                                    <label for="option_id{{$key+1}}" class="text-info">Option:<?= $key + 1; ?></label>
                                </div> -->


                                <div class="radio-item">
                                    <input @if($key==0 ) checked="checked" @endif type="radio" id="option_id{{$key+1}}" name="option_id" value="{{ $travel_data->id}}">
                                    <label for="option_id{{$key+1}}">#<?= $key + 1; ?></label>
                                </div>

                                <br><br>

                                <div class="row">
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Amount</strong> <br>
                                        <p class="text-muted">{{ $travel_data->amount }}</p>
                                    </div>
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Travel Via</strong> <br>
                                        <p class="text-muted">
                                            {{ config::get('constants.TRAVEL_VIA')[$travel_data->travel_via] }}
                                            <br>
                                            @if($travel_data->travel_via == 4)

                                            @if($travel_data->flight_trip=="one_way")
                                            One way
                                            @elseif($travel_data->flight_trip=="round_trip")
                                            Round Trip
                                            @else
                                            Multi City

                                            @endif
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-xs-6"> <strong> File</strong> <br>
                                        <div class="text-center"><a title="Download File" download href="{{ asset('storage/'.str_replace('public/','',!empty($travel_data->travel_image) ? $travel_data->travel_image : 'public/no_image')) }}"><i class="fa fa-cloud-download fa-lg"></i></a></div>
                                    </div>


                                </div>
                                <br>
                                <hr class="m-t-0">
                                @foreach($travel_data->travel_info as $key => $info)
                                <div class="row">
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Departure Time</strong> <br>
                                        <p class="text-muted">{{ date('d-m-Y h:i:s',strtotime($info->departure_datetime)) }}</p>
                                    </div>
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Arrival Time</strong> <br>
                                        <p class="text-muted">{{ date('d-m-Y h:i:s',strtotime($info->arrival_datetime)) }}</p>
                                    </div>
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>From -> To</strong> <br>
                                        <p class="text-muted">{{ $info->from}} -> {{ $info->to}}</p>
                                    </div>
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Details</strong> <br>
                                        <p class="text-muted">{{ $info->details}}</p>
                                    </div>
                                </div>

                                @endforeach
                                <br>

                            </div>
                            <hr class="m-t-0 m-b-40">
                            @endforeach
                            <div class="row">
                                @if($travel_option->count()>0)
                                <a data-toggle="modal" data-target="#ApproveModel" onclick="set_option_id('<?php echo $travel_data->travel_id; ?>')" class="btn btn-success">Approve Option</a>
                                <a data-toggle="modal" data-target="#Rejectmodel" onclick="set_travel_id('<?php echo $travel_data->travel_id; ?>')" class="btn btn-danger">Reject All Option</a>
                                @endif
                                <!-- <button class="btn btn-success pull-left" onclick="approve_confirm(this);" data-href="<?php echo url('approve_travel_option') ?>/{{ $travel_data->id}}/{{ $travel_data->travel_id}}">Approve this Option</button>
                     </div> -->

                            </div>


                        </div>

                        <!-- Model  -->
                        <div id="ApproveModel" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content" id="model_data">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h3 class="panel-title">Note: Please right approval note for selected option</h3>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('admin.approve_travel_option') }}" id="travel_option">
                                            @csrf
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <input type="hidden" name="id" id="id" value="">
                                                    <input type="hidden" name="travel_id" id="travel_id" value="">
                                                    <label for="travel_option2">Approval Note</label>

                                                    <textarea name="approval_note" id="travel_option2" value="" class="form-control" required></textarea>

                                                    <label id="travel_options-error" class="error" for="travel_option2"></label>
                                                </div>
                                                <div class="col-xs-2">

                                                    <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>

                                                </div>
                                            </div>

                                        </form>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <!-- End Model here -->

                        <!-- Reject Model -->
                        <div id="Rejectmodel" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content" id="model_data">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h3 class="panel-title">Note: Please mention rejected reason for travel options.</h3>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('admin.reject_all_travel_option') }}" id="travel_option">
                                            @csrf
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <input type="hidden" name="travel_id2" id="travel_id2" value="">
                                                    <label for="travel_option">Reject Note</label>

                                                    <textarea name="reject_note" id="travel_option" value="" class="form-control" required></textarea>

                                                    <label id="travel_option-error" class="error" for="travel_option"></label>
                                                </div>
                                                <div class="col-xs-2">

                                                    <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <!-- End Reject Model -->

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<script>
    function approve_confirm(e) {
        swal({
            title: "Are you sure you want to approve this travel option ?",
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

    function set_option_id(travel_id) {
        let id = $('input[name="option_id"]:checked').val();

        $('#id').val(id);
        $('#travel_id').val(travel_id);
    }

    function set_travel_id(id) {
        $('#travel_id2').val(id);
    }
</script>




@endsection