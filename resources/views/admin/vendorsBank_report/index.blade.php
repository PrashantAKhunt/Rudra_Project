
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
           
            <div class="white-box">           
                <div class="table-responsive">   

                    <table id="vendor_bank_report_table" class="table table-striped">
                        <thead>
                            <tr>
                            <th>Vendor name</th>
                                <th>Bank name</th>
                                <th>Company name</th>
                                <th>Beneficiary name</th>
                                <th>Account Number</th>
                                <th>IFSC</th>
                                <th>Branch</th>
                                <th>Type</th>
                                <th>Bank Detail</th>
                                
                                <th>Email</th>
                                <th>GST Number</th>
                                <th>PANCARD Number</th>
                                <th>Vendor Detail</th>


                            </tr>
                        </thead>
                        <tbody>


                            @if($records->count()>0)
                            @foreach($records as $bank)
                            <tr>
                               <td> {{ $bank->vendor_name}}</td>
                                <td>{{$bank->bank_name}}</td>
                                <td>{{$bank->company_name}}</td>
                                <td>{{$bank->beneficiary_name}}</td>
                                <td>{{$bank->ac_number}}</td>
                                <td>{{$bank->ifsc}}</td>
                                <td>{{$bank->branch}}</td>
                                <td>{{$bank->account_type}}</td>
                                <td> {{ $bank->bank_detail}}</td>
                                
                                <td> {{ $bank->email}}</td>
                                <td> {{ $bank->gst_number}}</td>
                                <td> {{ $bank->pan_card_number}}</td>
                                <td> {{ $bank->detail}}</td>


                            </tr>
                            @endforeach
                            @endif


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

        $('#vendor_bank_report_table').DataTable({
            dom: 'Bfrtip',
                    buttons: [
                            'csv', 'excel'
                    ], stateSave: true
        });
    });
</script>

@endsection