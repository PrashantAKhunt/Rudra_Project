@extends('layouts.admin_app')

@section('content')

<?php use Illuminate\Support\Facades\Config; ?>

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
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.TRAVEL_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="travel_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Project</th>
                                <th>Booked By</th>
                                <th>Hotel Name</th>
                                <th>Booking No</th>
                                <th>Booking Image</th>
                                <th>Amount</th>
                                <th>Payment Type</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Place</th>
                                <th>Your Approval</th>
                                <th>Final Approval</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hotel_list as $hotel)
                            <tr>
                                <td>{{ $hotel->company_name }}</td>
                                <td>{{ $hotel->project_name }}</td>
                                <td>{{ $hotel->name }}</td>
                                <td>{{ $hotel->hotel_name }}</td>
                                <td>{{ $hotel->booking_no }}</td>
                                <td>
                                    <a target="_blank" href="<?php echo asset('storage/'.str_replace('public/','',$hotel->booking_image)); ?>"><i class="fa fa-cloud-download"></i></a>
                                </td>
                                <td>{{ $hotel->total_amount }}</td>
                                <td>{{ config::get('constants.PAYMENT_TYPE')[$hotel->payment_type] }}</td>
                                <td>{{ date('d-m-Y H:i:s',strtotime($hotel->check_in_datetime)) }}</td>
                                <td>{{ date('d-m-Y H:i:s',strtotime($hotel->check_out_datetime)) }}</td>
                                <td>{{ $hotel->place }}</td>                                
                                <td>
                                    @if(Auth::user()->role == config('constants.ASSISTANT'))
                                        @if($hotel->first_approval_status=="Pending")
                                            <span class="text-warning">{{ $hotel->first_approval_status }}</span>
                                        @elseif($hotel->first_approval_status=="Approved")
                                            <span class="text-success">{{ $hotel->first_approval_status }}</span>
                                        @elseif($hotel->first_approval_status=="Rejected")
                                            <span class="text-danger">{{ $hotel->first_approval_status }}</span>
                                        @else
                                            <span class="text-warning">{{ $hotel->first_approval_status }}</span>
                                        @endif
                                    @elseif(Auth::user()->role == config('constants.Admin'))
                                        @if($hotel->second_approval_status=="Pending")
                                            <span class="text-warning">{{ $hotel->second_approval_status }}</span>
                                        @elseif($hotel->second_approval_status=="Approved")
                                            <span class="text-success">{{ $hotel->second_approval_status }}</span>
                                        @elseif($hotel->second_approval_status=="Rejected")
                                            <span class="text-danger">{{ $hotel->second_approval_status }}</span>
                                        @else
                                            <span class="text-warning">{{ $hotel->second_approval_status }}</span>
                                        @endif
                                    @elseif(Auth::user()->role == config('constants.SuperUser'))
                                        @if($hotel->third_approval_status=="Pending")
                                            <span class="text-warning">{{ $hotel->third_approval_status }}</span>
                                        @elseif($hotel->third_approval_status=="Approved")
                                            <span class="text-success">{{ $hotel->third_approval_status }}</span>
                                        @elseif($hotel->third_approval_status=="Rejected")
                                            <span class="text-danger">{{ $hotel->third_approval_status }}</span>
                                        @else
                                            <span class="text-warning">{{ $hotel->third_approval_status }}</span>
                                        @endif
                                    @elseif(Auth::user()->role == config('constants.ACCOUNT_ROLE'))
                                        @if($hotel->fourth_approval_status=="Pending")
                                            <span class="text-warning">{{ $hotel->fourth_approval_status }}</span>
                                        @elseif($hotel->fourth_approval_status=="Approved")
                                            <span class="text-success">{{ $hotel->fourth_approval_status }}</span>
                                        @elseif($hotel->fourth_approval_status=="Rejected")
                                            <span class="text-danger">{{ $hotel->fourth_approval_status }}</span>
                                        @else
                                            <span class="text-warning">{{ $hotel->fourth_approval_status }}</span>
                                        @endif
                                    @endif
                                </td>
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
                                    
                                    @if(Auth::user()->role == config('constants.ASSISTANT') && $hotel->first_approval_status=='Pending')
                                        <a href="{{ route('admin.approve_hotel',['id'=>$hotel->id, 'status'=>'Approved']) }}" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        <a href="#" onclick="reject_expence({{$hotel->id}});" class="btn btn-danger btn-rounded"><i class="fa fa-times"></i></a>
                                    @elseif(Auth::user()->role == config('constants.Admin') && $hotel->second_approval_status=='Pending' && $hotel->first_approval_status=='Approved')
                                        <a href="{{ route('admin.approve_hotel',['id'=>$hotel->id, 'status'=>'Approved']) }}" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        <a href="#" onclick="reject_expence({{$hotel->id}});" class="btn btn-danger btn-rounded"><i class="fa fa-times"></i></a>
                                    @elseif(Auth::user()->role == config('constants.SuperUser') && $hotel->third_approval_status=='Pending' && $hotel->second_approval_status=='Approved')
                                        <a href="{{ route('admin.approve_hotel',['id'=>$hotel->id, 'status'=>'Approved']) }}" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        <a href="#" onclick="reject_expence({{$hotel->id}});" class="btn btn-danger btn-rounded"><i class="fa fa-times"></i></a>
                                    @elseif(Auth::user()->role == config('constants.ACCOUNT_ROLE') && $hotel->fourth_approval_status=='Pending' && $hotel->third_approval_status=='Approved' && $hotel->status=='Pending')
                                        <a href="{{ route('admin.approve_hotel',['id'=>$hotel->id, 'status'=>'Approved']) }}" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        <a href="#" onclick="reject_expence({{$hotel->id}});" class="btn btn-danger btn-rounded"><i class="fa fa-times"></i></a>
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

        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="white-box">                
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">All Hotel Expenses History</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="all_hotel_expense_list_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Project</th>
                                <th>Booked By</th>
                                <th>Stayed User</th>
                                <th>Hotel Name</th>
                                <th>Booking No</th>
                                <th>Booking Image</th>
                                <th>Amount</th>
                                <th>Payment Type</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Place</th>
                                <th>Work Details</th>
                                <th>Booking Notes</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($all_hotel_expense_list->count()>0)
                            @foreach($all_hotel_expense_list as $hotel_expense)
                            <tr>
                                <td>{{ $hotel_expense->company_name }}</td>
                                <td>{{ $hotel_expense->project_name }}</td>
                                <td>{{ $hotel_expense->name }}</td>
                                <td>{{ $hotel_expense->stay_user_ids }}</td>
                                <td>{{ $hotel_expense->hotel_name }}</td>
                                <td>{{ $hotel_expense->booking_no }}</td>
                                <td>
                                    @if($hotel_expense->booking_image)
<!--                                    <div id="gallery-content">
                                        <div id="gallery-content-center">
                                            <a href="{{asset('storage/' . str_replace('public/', '', $hotel_expense->booking_image))}}" data-toggle="lightbox"  data-title="">
                                                <img width="100px" height="100px" src="{{asset('storage/' . str_replace('public/', '', $hotel_expense->booking_image))}}" alt="gallery" class="all studio"/> 
                                            </a>
                                        </div>
                                    </div>-->
                                    <a target="_blank" href="<?php echo asset('storage/'.str_replace('public/','',$hotel_expense->booking_image)); ?>"><i class="fa fa-cloud-download"></i></a>
                                    @endif
                                </td>
                                <td>{{ $hotel_expense->total_amount }}</td>
                                <td>{{ config::get('constants.PAYMENT_TYPE')[$hotel_expense->payment_type] }}</td>
                                <td>{{ date('d-m-Y H:i:s',strtotime($hotel_expense->check_in_datetime)) }}</td>
                                <td>{{ date('d-m-Y H:i:s',strtotime($hotel_expense->check_out_datetime)) }}</td>
                                <td>{{ $hotel_expense->place }}</td>
                                <td>{{ $hotel_expense->work_details }}</td>
                                <td>{{ $hotel_expense->booking_note }}</td>
                                <td>{{ $hotel_expense->status }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
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
                        
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="reject_expence" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Reject Travel Expence</h4>
                    </div>
                    <form action="{{ route('admin.reject_hotel_expence') }}" method="post">
                            @csrf
                        <div class="modal-body">
                            <div class="form-group ">
                                <input type="hidden" name="hotel_id" id="hotel_id" value="">
                                <textarea class="form-control" rows="4" name="reject_details" id="reject_details" required="required" ></textarea>
                            </div>
                            <button type="submit" class="btn btn-success" >Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.hotel') }}'" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $('#travel_table').DataTable({
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
                    
                }
            }
        });
    }
    function reject_expence(id){
        $('#hotel_id').val(id);
        $('#reject_expence').modal();
    }
    $(document).ready(function () {
        $('#all_hotel_expense_list_table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel'
            ],stateSave: true
        });
    })
</script>
@endsection