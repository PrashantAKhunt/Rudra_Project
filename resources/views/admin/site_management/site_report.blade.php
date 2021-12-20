@extends('layouts.admin_app')

@section('content')
<style type="text/css">
.table_heading{
text-align: center;
font-weight: 600;
font-size: larger;
}
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                {{-- <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li> --}}
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
                        <div class="row">
                            <form method="get" id="search_frm" action="{{ route('admin.site_report') }}">
                                <div class="col-md-3">
                                    <select onchange="get_project_by_company()" class="form-control" id="company_id" name="company_id">
                                        <option value="">Select Company</option>
                                        @foreach($company_list as $company)
                                        <option @if($company_id==$company->id) selected="" @endif value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="project_id" name="project_id">
                                        <option value="">Select Project</option>
                                        @foreach($project_list as $project)
                                        <option @if($project_id==$project->id) selected="" @endif value="{{ $project->id }}">{{ $project->project_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" readonly="" class="form-control" name="date_range" id="date_range"  value="{{$date_range}}" />
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success">Go</button>
                                </div>
                            </form>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-10"></div>
                            <div class="col-md-2">
                                <a href="{{url('generate_boq_bill')}}"><button type="button" class="btn btn-success">Generate Bill</button></a>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                @if(count($item_list))
                                    <div class="table-responsive">
                                        @foreach($item_list as $key => $value)
                                        <center><span><strong>Abstract Report {{date('d-m-Y',strtotime($key))}}</strong></span></center><hr>
                                        <table class="table table-striped bill_num">
                                            <thead>
                                                <tr>
                                                    <td rowspan="2">ITEM NO</td>
                                                    <td rowspan="2">ITEM DESCRIPITION</td>
                                                    <td rowspan="2">UOM</td>
                                                    <td rowspan="2">Quantity as per Tender/SOR</td>
                                                    <td rowspan="2">Unit Rate</td>
                                                    <td colspan="3">Executed Quantity</td>
                                                    <td colspan="3">Amount</td>
                                                </tr>
                                                <tr>
                                                    <td>Prev. Bill</td>
                                                    <td>This Bill</td>
                                                    <td>Upto Date</td>
                                                    <td>Prev. Bill</td>
                                                    <td>This Bill</td>
                                                    <td>Upto Date</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($value as $item_key => $item_value)
                                                    <tr>
                                                        <td>{{$item_value['sub_item']['item_no']}}</td>
                                                        <td>{{$item_value['sub_item']['item_description']}}</td>
                                                        <td>{{$item_value['sub_item']['UOM']}}</td>
                                                        <td>{{$item_value['sub_item']['quantity']}}</td>
                                                        <td>{{$item_value['sub_item']['rate']}}</td>
                                                        <td>{{$item_value['qe_prev_bill']}}</td>
                                                        <td>{{$item_value['qe_today_bill']}}</td>
                                                        <td>{{$item_value['qe_upto_date']}}</td>
                                                        <td>{{$item_value['a_prev_bill']}}</td>
                                                        <td>{{$item_value['a_today_bill']}}</td>
                                                        <td>{{$item_value['a_upto_date']}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-striped bill_num">
                                            <thead>
                                                <tr>
                                                    <td rowspan="2">ITEM NO</td>
                                                    <td rowspan="2">ITEM DESCRIPITION</td>
                                                    <td rowspan="2">UOM</td>
                                                    <td rowspan="2">Quantity as per Tender/SOR</td>
                                                    <td rowspan="2">Unit Rate</td>
                                                    <td colspan="3">Executed Quantity</td>
                                                    <td colspan="3">Amount</td>
                                                </tr>
                                                <tr>
                                                    <td>Prev. Bill</td>
                                                    <td>Upto Date</td>
                                                    <td>This Bill</td>
                                                    <td>Prev. Bill</td>
                                                    <td>Upto Date</td>
                                                    <td>This Bill</td>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                
                            </div>
                        </div>
{{--                             <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.site_management') }}'" class="btn btn-default">Cancel</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
$(".bill_num").DataTable({
"columnDefs": [
    // { "width": "30%", "targets": 1 }
  ]
})

$(document).ready(function () {
    $('#date_range').daterangepicker({
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-danger',
        cancelClass: 'btn-inverse',
        locale: {
            format: 'DD/MM/YYYY'
        },
    });

    $('#search_frm').validate({
        rules: {
            company_id: {
                required: true
            },
            project_id: {
                required: true
            },
            date_range: {
                required: true
            }
        }
    })

})
function get_project_by_company() {
$.ajax({
    url: "{{ route('admin.get_projectlist_by_company') }}",
    type: "POST",
    dataType: "html",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
        company_id: $('#company_id').val()
    },
    success: function (data) {
        $('#project_id').html(data);
    }
});
}
</script>
@endsection
