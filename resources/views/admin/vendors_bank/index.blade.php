@extends('layouts.admin_app')

@section('content')
<?php 
use App\Lib\Permissions;
$vendor_bank_add_permission = Permissions::checkPermission(45, 3);
$vendor_bank_edit_permission = Permissions::checkPermission(45, 2);
?>
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

                @if($vendor_bank_add_permission)
                    @if($view_special_permission)
                    <a href="{{ route('admin.add_vendors_bank') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Vendor Bank</a>
                    @endif
                @endif 
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="vendor_bank_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Bank name</th>

                                <th>Company name</th>
                                <th>Vendor name</th>
                                <th>Account Number</th>
                                <th>Name on Account</th>
                                <th>IFSC</th>
                                <th>MICR Code</th>
                                <th>SWIFT Code</th>
                                <th>Branch</th>
                                <th>Type</th>
                                <th> Detail</th>
                                <th>Status</th>
                                <th>Created date</th>
                                <th data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($vendor_bank as $bank)
                            <tr>
                                <td>{{$bank->bank_name}}</td>
                                <td>{{$bank->company_name}}</td>
                                <td>{{$bank->vendor_name}}</td>
                                <td>{{$bank->ac_number}}</td>
                                <td>{{$bank->beneficiary_name}}</td>
                                <td>{{$bank->ifsc}}</td>
                                <td>{{$bank->micr_code}}</td>
                                <td>{{$bank->swift_code}}</td>
                                <td>{{$bank->branch}}</td>
                                <td> {{ $bank->account_type}}</td>
                                <td> {{ $bank->detail}}</td>
                                <td>

                                @if($view_special_permission)
                                    @if($bank->status=='Enabled')
                                    <a href="{{ route('admin.change_vendor_bank_status',['id'=>$bank->id,'status'=>'Disabled']) }}" class="btn btn-success" title="Click To Disable">Enabled</a>
                                    @else
                                    <a href="{{ route('admin.change_vendor_bank_status',['id'=>$bank->id,'status'=>'Enabled']) }}" class="btn btn-danger" title="Click To Enable">Disabled</a>
                                    @endif
                                @else 
                                   @if($bank->status=='Enabled')
                                    <a href="#" class="btn btn-success" title="Click To Disable">Enabled</a>
                                    @else
                                    <a href="#" class="btn btn-danger" title="Click To Enable">Disabled</a>
                                    @endif
                                @endif

                                </td>

                                <td> {{ Carbon\Carbon::parse($bank->created_at)->format('d-m-Y')}}</td>


                                <td>
                                    @if($vendor_bank_edit_permission)
                                        @if($view_special_permission)
                                        <a href="{{ route('admin.edit_vendors_bank',['id'=>$bank->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>
                                        @endif
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
    </div>


</div>
@endsection


@section('script')
<script>
    $(document).ready(function() {

        $('#vendor_bank_table').DataTable({
            dom: 'Bfrtip',
                    buttons: [
                            'csv', 'excel'
                    ], stateSave: true
            });
    });
</script>
<!-- start - This is for export functionality only -->
<!-- end - This is for export functionality only -->
<script type="text/javascript" src="{{asset('admin_asset/assets/plugins/bower_components/gallery/js/animated-masonry-gallery.js')}}"></script>
<script type="text/javascript" src="{{asset('admin_asset/assets/plugins/bower_components/gallery/js/jquery.isotope.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin_asset/assets/plugins/bower_components/fancybox/ekko-lightbox.min.js')}}"></script>


@endsection