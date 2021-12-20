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
           
            <a href="{{ route('admin.expired_insurances_list') }}" class="btn btn-danger pull-left"><i class="fa fa-history"></i> Expired Insurances</a>
            <a href="{{ route('admin.add_vehicle_insurance') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Insurance</a>
            
            <p class="text-muted m-b-30"></p>           
                <br>
                <div class="table-responsive">
                    <table id="vehicle_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Vehicle Name</th>
                                <th>Vehicle Number</th>
                                <th>Insurance Company</th>
                                <th>Insurance Number</th>
                                <th>Agent</th>
                                <th>Contact Number</th>
                                <th>Contact Email</th>
                                <th>Amount</th>
                                <th>Insurance Type</th>
                                <th>Insurance Date</th>
                                <th>Expiration Date</th>

                                <th data-orderable="false" >History</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicle_assets as $vehicle_asset)
                            <tr>
                                <td>{{$vehicle_asset->name}}</td>
                                <td>{{$vehicle_asset->asset_1}}</td>
                                <td>{{$vehicle_asset->company_name}}</td>
                                <td>{{$vehicle_asset->insurance_number}}</td>
                                <td>{{$vehicle_asset->agent_name}}</td>
                                <td>{{$vehicle_asset->contact_number}}</td>
                                <td>@if($vehicle_asset->contact_email)
                                  {{$vehicle_asset->contact_email}}
                                  @else
                                  N/A
                                  @endif
                                </td>
                                <td>{{$vehicle_asset->amount}}</td>
                                <td> {{ $vehicle_asset->type }}</td>
                                <td> {{ Carbon\Carbon::parse($vehicle_asset->insurance_date)->format('d-m-Y')}}</td>
                                <td class="{{ $vehicle_asset->color_class }}">
                                {{ Carbon\Carbon::parse($vehicle_asset->renew_date)->format('d-m-Y')}}
                                <br>
                                {{ $vehicle_asset->left_day }} day left
                                </td>
                                <td>

                                    <a href="{{ route('admin.insurances_list',['id'=>$vehicle_asset->asset_id, 'type'=>$vehicle_asset->type]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-eye"></i></a>
                                    <br>
                                    <a href="#" onclick="get_reminderDates({{ $vehicle_asset->id }});" title="Reminder Dates" data-target="#reminderDateModel" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-calendar"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="reminderDateModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Reminder Dates</h4>
                    </div>
                    <br>
                    <br>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="date_table">

                        </tbody>
                    </table>

                </div>
                <!-- /.modal-content -->
            </div>
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
        $('#vehicle_table').DataTable();
        



    });
</script>
<script>



// Run function for each tbody tr
$("#vehicle_table tbody tr").each(function() {

// Within tr we find the last td child element and get content
 var expDate =  $(this).find("#uid").html();
 

});

function get_reminderDates(id) {
    trHTML = '';
            $.ajax({
                    url: "{{ route('admin.get_reminder_dates') }}",
                    type: "post",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    success: function(data) {
                        if (data.status) {
                            $('#date_table').empty();
                            var reminderDates = data.data.reminder_dates;
                            if (reminderDates.length == 0) {
                                
                                $('#date_table').empty();
                                $('#date_table').append('<span>No Records Found !</span>');
                            
                            }else{
                                    $.each(reminderDates, function(index, files_obj) {

                                        no = index + 1;
                                        date = moment(files_obj).format("DD-MM-YYYY");
                                        trHTML += '<tr>' +
                                           '<td>' + no + '</td>' +
                                            '<td>' + date + '</td>'+
                                            '<tr/>';   
                                        });
                                $('#date_table').append(trHTML);
                            }
            
                        }else{
                            $('#date_table').empty();
                            $('#date_table').append('<span>No Records Found !</span>');
                        }
            
                    }
                });
    
}

</script>
@endsection