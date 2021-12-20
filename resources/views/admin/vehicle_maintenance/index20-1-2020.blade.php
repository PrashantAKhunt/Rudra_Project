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
            <div class="white-box">


                <a href="{{ route('admin.add_vehicle_maintenance') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Maintenance</a>

                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="vehicle_main_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Vehicle Name</th>
                                <th>Vehicle Number</th>
                                <th>Vehicle Image</th>
                                <th>Maintenance Type</th>
                                <th>Description</th>
                                <th>Start Meter Reading</th>
                                <th>Received Meter Reading</th>
                                <th>Service center </th>
                                <th>Address</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Maintenance Date</th>
                                <th>Received Date</th>
                                <th>Next Scheduled Date</th>
                                <th data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($maintenance_data as $vehicle_data)
                            <tr>
                                <td>{{$vehicle_data->name}}</td>
                                <td>{{$vehicle_data->asset_1}}</td>

                                <td><a href="{{ asset('storage/'.str_replace('public/','',$vehicle_data->image)) }}" data-toggle="lightbox" data-title="{{$vehicle_data->name}}" data-gallery="multiimages1{{ $vehicle_data->v_id}}"><img alt="User Pic" src="{{ asset('storage/'.str_replace('public/','',$vehicle_data->image)) }}" id="myImage" height="42" width="42"></a>
                                    @foreach($vehicle_images as $vehicle_image)
                                    @if($vehicle_data->id == $vehicle_image->vehicle_maintenance_id)

                                    <a href="{{ asset('storage/'.str_replace('public/','',$vehicle_image->image)) }}"  data-title="{{$vehicle_data->name}}"  data-gallery="multiimages1{{ $vehicle_data->v_id}}" style="visibility: hidden"></a>

                                    @endif
                                    @endforeach
                                </td>


                                <td>{{$vehicle_data->maintenance_type}}</td>
                                <td>{{$vehicle_data->description}}</td>
                                <td>{{$vehicle_data->start_meter_reading}}</td>
                                <td>{{$vehicle_data->received_meter_reading}}</td>
                                <td>{{$vehicle_data->service_center_name}}</td>
                                <td> {{ $vehicle_data->service_center_address}}</td>
                                <td> {{ $vehicle_data->amount}}</td>
                                @if($vehicle_data->status == 'Pending')
                                <td>
                                    <span class="label label-rouded label-warning">Pending</span>
                                </td>
                                @else
                                <td><span class="label label-rouded label-info">Completed</span></td>
                                @endif

                                <td> {{ Carbon\Carbon::parse($vehicle_data->maintenance_date)->format('d-m-Y')}}</td>
                                <td> {{ Carbon\Carbon::parse($vehicle_data->maintenance_date)->format('d-m-Y')}}</td>
                                
                                <td>
                                    <?php if($vehicle_data->maintenance_type == "Periodic Maintenance") {
                                    echo Carbon\Carbon::parse($vehicle_data->next_periodic_date)->format('d-m-Y');
                                     } ?>       
                                </td>
                                
                                <td>

                                    <a href="{{ route('admin.update_vehicle_maintenance',['id'=>$vehicle_data->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>

                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
    </div>


</div>
@endsection


@section('script')
<script>
    $(document).ready(function() {
        var table = $('#vehicle_main_table').DataTable({
            "processing": true,
            "responsive": true

        });
    });
</script>
<!-- start - This is for export functionality only -->
<!-- end - This is for export functionality only -->
<script type="text/javascript" src="{{asset('admin_asset/assets/plugins/bower_components/gallery/js/animated-masonry-gallery.js')}}"></script>
<script type="text/javascript" src="{{asset('admin_asset/assets/plugins/bower_components/gallery/js/jquery.isotope.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin_asset/assets/plugins/bower_components/fancybox/ekko-lightbox.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function($) {
        // delegate calls to data-toggle="lightbox"
        $(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(event) {
            event.preventDefault();
            return $(this).ekkoLightbox({
                onShown: function() {
                    if (window.console) {
                        return console.log('Checking our the events huh?');
                    }
                },
                onNavigate: function(direction, itemIndex) {
                    if (window.console) {
                        return console.log('Navigating ' + direction + '. Current item: ' + itemIndex);
                    }
                }
            });
        });

        //Programatically call
        $('#open-image').click(function(e) {
            e.preventDefault();
            $(this).ekkoLightbox();
        });
        $('#open-youtube').click(function(e) {
            e.preventDefault();
            $(this).ekkoLightbox();
        });

        // navigateTo
        $(document).delegate('*[data-gallery="navigateTo"]', 'click', function(event) {
            event.preventDefault();

            var lb;
            return $(this).ekkoLightbox({
                onShown: function() {

                    lb = this;

                    $(lb.modal_content).on('click', '.modal-footer a', function(e) {

                        e.preventDefault();
                        lb.navigateTo(2);

                    });

                }
            });
        });


    });
</script>
@endsection