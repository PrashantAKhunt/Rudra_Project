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
                                <th>Travel Via</th>
                                <th>Ticket No</th>
                                <th>Ticket File</th>
                                <th>Amount</th>
                                <th>Payment Type</th>
                                <th>Departure</th>
                                <th>Arrival</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Your Approval</th>
                                <th>Final Approval</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($travel_list as $travel)
                            <tr>
                                <td>{{ $travel->company_name }}</td>
                                <td>{{ $travel->project_name }}</td>
                                <td>{{ $travel->name }}</td>
                                <td>{{ config::get('constants.TRAVEL_VIA')[$travel->travel_via] }}</td>
                                <td>{{ $travel->ticket_no }}</td>
                                <td>
                                    <a target="_blank" href="<?php echo asset('storage/'.str_replace('public/','',$travel->ticket_image)); ?>"><i class="fa fa-cloud-download"></i></a>
                                </td>
                                <td>{{ $travel->total_amount }}</td>
                                <td>{{ config::get('constants.PAYMENT_TYPE')[$travel->payment_type] }}</td>
                                <td>{{ date('d-m-Y h:i:s',strtotime($travel->departure_datetime)) }}</td>
                                <td>{{ date('d-m-Y h:i:s',strtotime($travel->arrival_datetime)) }}</td>
                                <td>{{ $travel->from }}</td>
                                <td>{{ $travel->to }}</td>
                                <td>
                                    @if(Auth::user()->role == config('constants.ASSISTANT'))
                                        @if($travel->first_approval_status=="Pending")
                                            <span class="text-warning">{{ $travel->first_approval_status }}</span>
                                        @elseif($travel->first_approval_status=="Approved")
                                            <span class="text-success">{{ $travel->first_approval_status }}</span>
                                        @elseif($travel->first_approval_status=="Rejected")
                                            <span class="text-danger">{{ $travel->first_approval_status }}</span>
                                        @else
                                            <span class="text-warning">{{ $travel->first_approval_status }}</span>
                                        @endif
                                    @elseif(Auth::user()->role == config('constants.Admin'))
                                        @if($travel->second_approval_status=="Pending")
                                            <span class="text-warning">{{ $travel->second_approval_status }}</span>
                                        @elseif($travel->second_approval_status=="Approved")
                                            <span class="text-success">{{ $travel->second_approval_status }}</span>
                                        @elseif($travel->second_approval_status=="Rejected")
                                            <span class="text-danger">{{ $travel->second_approval_status }}</span>
                                        @else
                                            <span class="text-warning">{{ $travel->second_approval_status }}</span>
                                        @endif
                                    @elseif(Auth::user()->role == config('constants.SuperUser'))
                                        @if($travel->third_approval_status=="Pending")
                                            <span class="text-warning">{{ $travel->third_approval_status }}</span>
                                        @elseif($travel->third_approval_status=="Approved")
                                            <span class="text-success">{{ $travel->third_approval_status }}</span>
                                        @elseif($travel->third_approval_status=="Rejected")
                                            <span class="text-danger">{{ $travel->third_approval_status }}</span>
                                        @else
                                            <span class="text-warning">{{ $travel->third_approval_status }}</span>
                                        @endif
                                    @elseif(Auth::user()->role == config('constants.ACCOUNT_ROLE'))
                                        @if($travel->fourth_approval_status=="Pending")
                                            <span class="text-warning">{{ $travel->fourth_approval_status }}</span>
                                        @elseif($travel->fourth_approval_status=="Approved")
                                            <span class="text-success">{{ $travel->fourth_approval_status }}</span>
                                        @elseif($travel->fourth_approval_status=="Rejected")
                                            <span class="text-danger">{{ $travel->fourth_approval_status }}</span>
                                        @else
                                            <span class="text-warning">{{ $travel->fourth_approval_status }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($travel->status=="Pending")
                                        <span class="text-warning">{{ $travel->status }}</span>
                                    @elseif($travel->status=="Approved")
                                        <span class="text-success">{{ $travel->status }}</span>
                                    @elseif($travel->status=="Rejected")
                                        <span class="text-danger">{{ $travel->status }}</span>
                                    @else
                                        <span class="text-warning">{{ $travel->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" onclick="get_travel_detail({{$travel->id}});" data-target="#travel_view" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>                                    
                                    @if($travel->booked_by==Auth::user()->id && $travel->first_approval_status == 'Pending' && $travel->status == 'Pending')
                                        <a href="{{ route('admin.edit_travel',['id'=>$travel->id]) }}" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>
                                        <a href="{{ route('admin.cancel_travel',['id'=>$travel->id]) }}" class="btn btn-danger btn-rounded"><i class="fa fa-trash"></i></a>
                                    @endif

                                    @if(Auth::user()->role == config('constants.ASSISTANT') && $travel->first_approval_status=='Pending')
                                        <a href="{{ route('admin.approve_travel',['id'=>$travel->id, 'status'=>'Approved']) }}" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        <a href="#" onclick="reject_expence({{$travel->id}});" class="btn btn-danger btn-rounded"><i class="fa fa-times"></i></a>
                                    @elseif(Auth::user()->role == config('constants.Admin') && $travel->second_approval_status=='Pending' && $travel->first_approval_status=='Approved')
                                        <a href="{{ route('admin.approve_travel',['id'=>$travel->id, 'status'=>'Approved']) }}" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        <a href="#" onclick="reject_expence({{$travel->id}});" class="btn btn-danger btn-rounded"><i class="fa fa-times"></i></a>
                                    @elseif(Auth::user()->role == config('constants.SuperUser') && $travel->third_approval_status=='Pending' && $travel->second_approval_status=='Approved')
                                        <a href="{{ route('admin.approve_travel',['id'=>$travel->id, 'status'=>'Approved']) }}" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        <a href="#" onclick="reject_expence({{$travel->id}});" class="btn btn-danger btn-rounded"><i class="fa fa-times"></i></a>
                                    @elseif(Auth::user()->role == config('constants.ACCOUNT_ROLE') && $travel->fourth_approval_status=='Pending' && $travel->third_approval_status=='Approved' && $travel->status=='Pending')
                                        <a href="{{ route('admin.approve_travel',['id'=>$travel->id, 'status'=>'Approved']) }}" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                        <a href="#" onclick="reject_expence({{$travel->id}});" class="btn btn-danger btn-rounded"><i class="fa fa-times"></i></a>
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
                        <h4 class="page-title">All Travel Expenses History</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="all_travel_expense_list_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Project</th>
                                <th>Booked By</th>
                                <th>Travelers</th>
                                <th>Travel Via</th>
                                <th>Ticket No</th>
                                <th>Ticket File</th>                                
                                <th>Amount</th>
                                <th>Payment Type</th> 
                                <th>Departure</th>
                                <th>Arrival</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Work Details</th>
                                <th>Booking Notes</th>   
                                <th>Reject Note</th>                             
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($all_travel_expense_list->count()>0)
                            @foreach($all_travel_expense_list as $travel_expense)
                            <tr>
                                <td>{{ $travel_expense->company_name }}</td>
                                <td>{{ $travel_expense->project_name }}</td>
                                <td>{{ $travel_expense->name }}</td>
                                <td>{{ $travel_expense->traveler_ids }}</td>
                                <td>{{ config::get('constants.TRAVEL_VIA')[$travel_expense->travel_via] }}</td>
                                <td>{{ $travel_expense->ticket_no }}</td>
                                <td>
                                    <a target="_blank" href="<?php echo asset('storage/'.str_replace('public/','',$travel_expense->ticket_image)); ?>"><i class="fa fa-cloud-download"></i></a>
                                </td>
                                <td>{{ $travel_expense->total_amount }}</td>
                              <td>{{ $travel_expense->payment_type }}</td>   <!-- throw error -->
                                <td>{{ date('d-m-Y h:i:s',strtotime($travel_expense->departure_datetime)) }}</td>
                                <td>{{ date('d-m-Y h:i:s',strtotime($travel_expense->arrival_datetime)) }}</td>
                                <td>{{ $travel_expense->from }}</td>
                                <td>{{ $travel_expense->to }}</td>
                                <td>{{ $travel_expense->work_details }}</td>
                                <td>{{ $travel_expense->booking_note }}</td>
                                <td>{{ $travel_expense->reject_details }}</td>
                                <td>{{ $travel_expense->status }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="travel_view" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Travel Detail</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Travel User</label><p class="traveler"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Ticket No</label><p class="ticket"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Travel Via</label><p class="travel_via"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Travel Company</label><p class="travel_company"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">                            
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Ticket Amount</label><p class="amount"></p>
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
                                    <label>From</label><p class="from"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>To</label><p class="to"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Departure</label><p class="departure"></p>
                                </div>
                            </div>                                
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Arrival</label><p class="arrival"></p>
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
                    <form action="{{ route('admin.reject_travel_expence') }}" method="post">
                            @csrf
                        <div class="modal-body">
                            <div class="form-group ">
                                <input type="hidden" name="travel_id" id="travel_id" value="">
                                <textarea class="form-control" rows="4" name="reject_details" id="reject_details" required="required" ></textarea>
                            </div>
                            <button type="submit" class="btn btn-success" >Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.travel') }}'" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    function get_travel_detail(id) {
        $.ajax({
        url: "{{ route('admin.get_travel_detail') }}",
            type: "post",
            dataType: "json",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {id: id},
            success: function (data) {
                if (data.status) {
                    $('.traveler').html(data.data.traveler_list);
                    $('.travel_via').html(data.data.travel_list.travel_via);
                    $('.travel_company').html(data.data.travel_list.travel_company);
                    $('.ticket').html(data.data.travel_list.ticket_no);
                    $('.amount').html(data.data.travel_list.total_amount);
                    $('.payment').html(data.data.travel_list.payment_type);
                    $('.from').html(data.data.travel_list.from);
                    $('.to').html(data.data.travel_list.to);
                    $('.departure').html(data.data.travel_list.departure_datetime);
                    $('.arrival').html(data.data.travel_list.arrival_datetime);
                    $('.work').html(data.data.travel_list.work_details);
                    $('.booking').html(data.data.travel_list.booking_note);
                }
            }
        });
    }
    function reject_expence(id){
        $('#travel_id').val(id);
        $('#reject_expence').modal();
    }
    $(document).ready(function () {
        $('#travel_table').DataTable({stateSave:true});
        $('#all_travel_expense_list_table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel'
            ],stateSave: true
        });
    })
</script>
@endsection