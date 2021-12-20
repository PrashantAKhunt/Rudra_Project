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
                @if(Auth::user()->role != 1 && in_array(3,$role))
                    <a href="{{ route('admin.add_hotel') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Hotel Request</a>
                    <p class="text-muted m-b-30"></p>
                    <br>
                @endif
                
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.TRAVEL_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="hotel_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Hotel Name</th>
                                <th>Booking No</th>
                                <th>Booking Image</th>
                                <th>Amount</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Place</th>    
                                <th>Booked By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hotel_list as $hotel)
                            <tr>
                                <td>{{ $hotel->hotel_name }}</td>
                                <td>{{ $hotel->booking_no }}</td>
                                <td>
                                    <a target="_blank" href="<?php echo asset('storage/'.str_replace('public/','',$hotel->booking_image)); ?>"><i class="fa fa-cloud-download"></i></a>
                                </td>
                                <td>{{ $hotel->total_amount }}</td>
                                <td>{{ date('d-m-Y H:i:s',strtotime($hotel->check_in_datetime)) }}</td>
                                <td>{{ date('d-m-Y H:i:s',strtotime($hotel->check_out_datetime)) }}</td>
                                <td>{{ $hotel->place }}</td>      
                                <td>{{ $hotel->name }}</td>
                                <td>
                                    @if($hotel->status=="Pending")
                                        <span class="text-warning">{{ $hotel->status }}</span>
                                    @elseif($hotel->status=="Approved")
                                        <span class="text-success">{{ $hotel->status }}</span>
                                    @elseif($hotel->status=="Rejected")
                                        <span class="text-danger">{{ $hotel->status }}</span>
                                    @else
                                        <span class="text-warning">{{ $hotel->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" onclick="get_hotel_detail({{$hotel->id}});" data-target="#hotel_view" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>                                    
                                    @if($hotel->booked_by==Auth::user()->id && $hotel->first_approval_status == 'Pending' && $hotel->status == 'Pending')
                                        <a href="{{ route('admin.edit_hotel',['id'=>$hotel->id]) }}" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>
                                        <a href="{{ route('admin.cancel_hotel',['id'=>$hotel->id]) }}" class="btn btn-danger btn-rounded" title="Cancel Request"><i class="fa fa-times"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->
        </div>
        <div class="modal fade" id="hotel_view" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Hotel Detail</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Stayed User</label><p class="stay_user_ids"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Booking No</label><p class="booking_no"></p>
                                </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Hotel Name</label><p class="hotel_name"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Place</label><p class="place"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">                            
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Amount</label><p class="amount"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Payment Type</label><p class="payment"></p>
                                </div>
                            </div>
                        </div>                        
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Check In</label><p class="check_in_datetime"></p>
                                </div>
                            </div>                                
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Check Out</label><p class="check_out_datetime"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Work Details</label><p class="work"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Booking Notes</label><p class="booking"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group ">
                                    <label>Reject Note (In case of expense is rejected)</label>
                                    <p class="reject_note"></p>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $('#hotel_table').DataTable({
        stateSave:true
    });
    function get_hotel_detail(id) {
        $.ajax({
        url: "{{ route('admin.get_hotel_detail') }}",
            type: "post",
            dataType: "json",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {id: id},
            success: function (data) {
                if (data.status) {
                    $('.stay_user_ids').html(data.data.stay_user_list);
                    $('.hotel_name').html(data.data.hotel_list.hotel_name);
                    $('.booking_no').html(data.data.hotel_list.booking_no);
                    $('.amount').html(data.data.hotel_list.total_amount);
                    $('.payment').html(data.data.hotel_list.payment_type);
                    $('.place').html(data.data.hotel_list.place);
                    $('.check_in_datetime').html(data.data.hotel_list.check_in_datetime);
                    $('.check_out_datetime').html(data.data.hotel_list.check_out_datetime);
                    $('.work').html(data.data.hotel_list.work_details);
                    $('.booking').html(data.data.hotel_list.booking_note);
                    if(data.data.hotel_list.reject_details){
                    $('.reject_note').html(data.data.hotel_list.reject_details);
                    }
                    else{
                        $('.reject_note').html('NA');
                    }
                }
            }
        });
    }    
</script>
@endsection