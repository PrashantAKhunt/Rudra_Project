@extends('layouts.admin_app')

@section('content')
<?php
use Illuminate\Support\Facades\Config;
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
            @if(config::get('constants.Admin') == Auth::user()->role || config::get('constants.ADMIN_EXECUTIVE') == Auth::user()->role)
                <a href="{{ route('admin.add_failed_letter_head') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Failed Leter Head</a>
            @endif
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Letter Head Ref No</th>
                                <th>Letter Head Number</th>
                                <th>Company</th>
                                <th>Failed Reason</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($failed_list)
                                @foreach($failed_list as $key => $value)
                                    <tr>
                                        <td>{{$value['letter_head_ref_no']}}</td>
                                        @php
                                        $last_no = end($value);
                                        @endphp
                                        <td>{{$value['letter_head_numbers']}}</td>
                                        <td>{{$value['company_name']}}</td>
                                        <td>{{$value['failed_reason']}}</td>
                                        <td>
                                        @php
                                            $path = $value['failed_document'];
                                            $baseURL = str_replace("public/","",$path);
                                            $url =  URL::to('/')."/storage/".$baseURL;
                                        @endphp
                                        <a href="{{$url}}" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
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
                $('#company_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ],
                });
                var table = $('#company_table_last').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_failed_letter_head_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'letter_head_ref_no'
                        },
                        {"taregts": 1, 'data': 'company_name'
                        },
                        {"taregts": 2, 'data': 'letter_head_number'
                        },
                        {"taregts": 3, 'data': 'failed_reason'
                        },
                        {"taregts": 4,"searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                var path = row.failed_document;
                                var baseURL = path.replace("public/","");
                                var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                                out += '<a href="'+ url +'" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>';
                                return out;
                            }
                        }
                    ]

                });

            })
            
        </script>
        @endsection