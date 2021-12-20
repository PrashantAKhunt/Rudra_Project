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
                
                <a href="{{ route('admin.add_site_management') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Site Management</a>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="category_table" class="table table-striped">
                        <thead>
                            <tr>
                                
                                <th>Item No</th>
                                <th>Item Description</th>
                                <th>UOM</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Excavation for foundation in dense or hard soil including sorting out and stacking of useful materials and disposing of the excavated stuff up to 50 meter lead.(A) up to 1.5 m depth</td>
                                <td>Cum</td>
                                <td>800.00</td>
                                <td>125.00</td>
                                <td>100000.00</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-rounded" title="View/Edit Details">
                                        <i class="fa fa-edit"></i>
                                        </a>
                                </td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>Providing & laying cement concrete flooring 1:2:4 (1 cement : 2 coarse sand : 4 stone aggregate 20 mm. nominal size) laid in one layer finished with a floating coat of neat cement.</td>
                                <td>Cum</td>
                                <td>600.00</td>
                                <td>3150.00</td>
                                <td>1890000.00</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-rounded" title="View/Edit Details">
                                        <i class="fa fa-edit"></i>
                                        </a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Providing 15 mm. thick cement plaster in single coat on fair side of brick/concrete wall for interior plastering upto floor two level finished even and smooth in (A) Cement mortar 1:4 (1 cement : 4 fine sand)</td>
                                <td>Sqm</td>
                                <td>500.00</td>
                                <td>275.00</td>
                                <td>137500.00</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-rounded" title="View/Edit Details">
                                        <i class="fa fa-edit"></i>
                                        </a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Provding and fixing Polythelen pipes of working pressure 6 KgF/Sqcm, confirming to IS:4985 including jointing with sealing ring confirming to IS:5382 leaving 10 mm gap for thermal expansion (A) 110mm OD</td>
                                <td>Rmt</td>
                                <td>1250.00</td>
                                <td>550.00</td>
                                <td>687500.00</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-rounded" title="View/Edit Details">
                                        <i class="fa fa-edit"></i>
                                        </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
        
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                var table = $('#category_table').DataTable({
                });
            })
        // function delete_confirm(e) {
        //     swal({
        //         title: "Are you sure you want to delete tender category ?",
        //         //text: "You want to change status of admin user.",
        //         type: "warning",
        //         showCancelButton: true,
        //         confirmButtonColor: "#DD6B55",
        //         confirmButtonText: "Yes",
        //         closeOnConfirm: false
        //     }, function () {
        //         window.location.href = $(e).attr('data-href');
        //     });
        // }
        </script>
        @endsection