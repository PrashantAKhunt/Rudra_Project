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
                            <form method="get" id="search_frm" action="{{ route('admin.excess_saving') }}">
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
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success">Go</button>
                                </div>
                            </form>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped bill_num">
                                        <thead>
                                            <tr>
                                                <td rowspan="2">ITEM NO</td>
                                                <td rowspan="2">ITEM DESCRIPITION</td>
                                                <td rowspan="2">UOM</td>
                                                <td rowspan="2">Quantity as per Tender/SOR</td>
                                                <td rowspan="2">Unit Rate</td>
                                                <td rowspan="2">Amount</td>
                                                <td colspan="2">Final Executed Quantity</td>
                                                <td colspan="2">Excess</td>
                                                <td colspan="2">Saving</td>
                                            </tr>
                                            <tr>
                                                <td>Qty</td>
                                                <td>Amount</td>
                                                <td>Qty</td>
                                                <td>Amount</td>
                                                <td>Qty</td>
                                                <td>Amount</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($item_list)
                                            @foreach($item_list as $key => $value)
                                                <tr>
                                                    <td>{{$value['item_no']}}</td>
                                                    <td>{{$value['item_description']}}</td>
                                                    <td>{{$value['UOM']}}</td>
                                                    <td>{{$value['quantity']}}</td>
                                                    <td>{{$value['rate']}}</td>
                                                    <td>{{$value['amount']}}</td>
                                                    <td class="text-primary">{{$value['final_qty']}}</td>
                                                    <td class="text-primary">{{$value['final_amount']}}</td>
                                                    <td class="text-danger">{{$value['excess_qty']}}</td>
                                                    <td class="text-danger">{{$value['excess_amount']}}</td>
                                                    <td class="text-success">{{$value['saving_qty']}}</td>
                                                    <td class="text-success">{{$value['saving_amount']}}</td>
                                                </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
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
