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
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label>Project</label>
                                     <select class="form-control">
                                         <option value="">Select</option>
                                         <option value="Project1">Project 1</option>
                                         <option value="Project2">Project 2</option>
                                         <option value="Project3">Project 3</option>
                                         <option value="Project4">Project 4</option>
                                         <option value="Project5">Project 5</option>
                                         <option value="Project6">Project 6</option>
                                     </select>
                                </div>
                            </div>
                            <div class="col-md-4" style="margin-top: 30px;">
                                <div class="form-group ">
                                    <button class="btn btn-success">Run New Bill</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="table_heading">Bill Number -1</h4>
                                <table class="table table-striped" id="bill_num1">
                                    <thead>
                                        <tr>
                                            <td rowspan="2">ITEM NO</td>
                                            <td rowspan="2">ITEM DESCRIPITION</td>
                                            <td rowspan="2">UOM</td>
                                            <td rowspan="2">Quantity as per Tender/SOR</td>
                                            <td rowspan="2">Unit Rate</td>
                                            <td colspan="3">Executed Quantity</td>
                                        </tr>
                                        <tr>
                                            <td>Prev. Bill</td>
                                            <td>Upto Date</td>
                                            <td>This Bill</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Excavation for foundation in dense or hard soil including sorting out and stacking of useful materials and disposing of the excavated stuff up to 50 meter lead.(A) up to 1.5 m depth</td>
                                            <td>800.00</td>
                                            <td>Cum</td>
                                            <td>125.00</td>
                                            <td>0.00</td>
                                            <td>180.00</td>
                                            <td>180.00</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Provding and fixing Polythelen pipes of working pressure 6 KgF/Sqcm, confirming to IS:4985 including jointing with sealing ring confirming to IS:5382 leaving 10 mm gap for thermal expansion (A) 110mm OD</td>
                                            <td>1250.00</td>
                                            <td>Rmt</td>
                                            <td>550.00</td>
                                            <td>0.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="table_heading">Bill Number -2</h4>
                                <table class="table table-striped" id="bill_num2">
                                    <thead>
                                        <tr>
                                            <td rowspan="2">ITEM NO</td>
                                            <td rowspan="2">ITEM DESCRIPITION</td>
                                            <td rowspan="2">UOM</td>
                                            <td rowspan="2">Quantity as per Tender/SOR</td>
                                            <td rowspan="2">Unit Rate</td>
                                            <td colspan="3">Executed Quantity</td>
                                        </tr>
                                        <tr>
                                            <td>Prev. Bill</td>
                                            <td>Upto Date</td>
                                            <td>This Bill</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Excavation for foundation in dense or hard soil including sorting out and stacking of useful materials and disposing of the excavated stuff up to 50 meter lead.(A) up to 1.5 m depth</td>
                                            <td>800.00</td>
                                            <td>Cum</td>
                                            <td>125.00</td>
                                            <td>180.00</td>
                                            <td>250.00</td>
                                            <td>430.00</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Provding and fixing Polythelen pipes of working pressure 6 KgF/Sqcm, confirming to IS:4985 including jointing with sealing ring confirming to IS:5382 leaving 10 mm gap for thermal expansion (A) 110mm OD</td>
                                            <td>1250.00</td>
                                            <td>Rmt</td>
                                            <td>550.00</td>
                                            <td>200.00</td>
                                            <td>300.00</td>
                                            <td>500.00</td>
                                        </tr>
                                    </tbody>
                                </table>
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
$("#bill_num1,#bill_num2").DataTable({
"columnDefs": [
    { "width": "40%", "targets": 1 }
  ]
})
</script>
@endsection
