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
                {{-- <a href="{{ route('admin.add_tender') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Tender</a> --}}

                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="tender_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tender Sr No</th>
                                <th>Department</th>
                                <th>Tender Id Per Portal</th>
                                <th>Portal Name</th>
                                <th>Tender No</th>
                                <th>Name Of Work</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                {{-- <tr>
                                    <td>100024</td>
                                    <td>MD</td>
                                    <td>568</td>
                                    <td>XYZ</td>
                                    <td>xyz123</td>
                                    <td>Providing 15 mm. thick cement plaster</td>
                                    <td>
                                        <a href="{{url('edit_submission_tender')}}/1" class="btn btn-primary btn-rounded" title="View/Edit Details">
                                        <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr> --}}
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
                var table = $('#tender_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "order": [[0, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_submission_tender_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        
                        {"taregts": 1, 'data': 'tender_sr_no'},
                        {"taregts": 2, 'data': 'dept_name'},
                        {"taregts": 3, 'data': 'tender_id_per_portal'},
                        {"taregts": 4, 'data': 'portal_name'},
                        {"taregts": 5, 'data': 'tender_no'},
                        {"taregts": 6, 'data': 'name_of_work'},
                        {"taregts": 19, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 
                                out = '<a href="<?php echo url('edit_submission_tender') ?>'+'/'+id+'" class="btn btn-primary btn-rounded" title="View/Edit Tender"><i class="fa fa-eye"></i></a>'; 
                                return out;
                            }
                        },
                    ]

                });
            })
        </script>
        @endsection