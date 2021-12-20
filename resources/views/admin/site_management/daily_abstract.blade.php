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
                <div class="row">
                    <form method="get" id="search_frm" action="{{ route('admin.daily_abstract') }}">
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
                                <option>Select Project</option>
                                @foreach($project_list as $project)
                                <option @if($project_id==$project->id) selected="" @endif value="{{ $project->id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" readonly="" class="form-control" name="date_range" id="date_range" value="{{$date_range}}" />
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success">Go</button>
                        </div>
                    </form>
                </div>
                <hr>
                {{-- <div class="row">
                    <div class="col-md-10"></div>
                    <div class="col-md-2">
                        <a href="{{url('generate_bill_invoice')}}"><button type="button" class="btn btn-success">Bill Invoice</button></a>
                    </div>
                </div> --}}
                <br>
                @if(count($item_list))
                <div class="table-responsive">
                    @foreach($item_list as $key => $value)
                    <center><span><strong>Daily Abstract {{date('d-m-Y',strtotime($key))}}</strong></span></center><hr>
                    <table id="daily_abstract_table" class="table table-striped daily_abstract_table">
                        <thead>
                            <tr>
                                <td rowspan="2">ITEM NO</td>
                                <td rowspan="2">ITEM DESCRIPITION</td>
                                <td rowspan="2">Quantity as per Tender/SOR</td>
                                <td rowspan="2">Unit</td>
                                <td rowspan="2">Unit Rate</td>
                                <td colspan="3">Quantity Executed</td>
                                <td colspan="3">Amount Of Work Executed</td>
                                {{-- <td rowspan="2">Today Remark</td> --}}
                            </tr>
                            <tr>
                                <td>Upto Prev. Day</td>
                                <td>Qty Executed Today</td>
                                <td>Upto Date</td>
                                <td>Upto Prev. Day</td>
                                <td>Today</td>
                                <td>Upto Date</td>
                            </tr>

                        </thead>

                        <tbody>
                            @foreach($value as $item_key => $item_value)
                            <tr>
                                <td>{{$item_value['sub_item']['item_no']}}</td>
                                <td>{{$item_value['sub_item']['item_description']}}</td>
                                <td>{{$item_value['sub_item']['quantity']}}</td>
                                <td>{{$item_value['sub_item']['UOM']}}</td>
                                <td>{{$item_value['sub_item']['rate']}}</td>
                                <td>{{$item_value['qe_prev_day_qty']}}</td>
                                <td>{{$item_value['qe_executed_today_qty']}}</td>
                                <td>{{$item_value['qe_total_qty']}}</td>
                                <td>{{$item_value['wea_prev_day']}}</td>
                                <td>{{$item_value['wea_today']}}</td>
                                <td>{{$item_value['wea_total']}}</td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                    @endforeach
                </div>
                @else
                    <div class="table-responsive">
                    <table id="daily_abstract_table" class="table table-striped daily_abstract_table">
                        <thead>
                            <tr>
                                <td rowspan="2">ITEM NO</td>
                                <td rowspan="2">ITEM DESCRIPITION</td>
                                <td rowspan="2">Quantity as per Tender/SOR</td>
                                <td rowspan="2">Unit</td>
                                <td rowspan="2">Unit Rate</td>
                                <td colspan="3">Quantity Executed</td>
                                <td colspan="3">Amount Of Work Executed</td>
                                {{-- <td rowspan="2">Today Remark</td> --}}
                            </tr>
                            <tr>
                                <td>Upto Prev. Day</td>
                                <td>Qty Executed Today</td>
                                <td>Upto Date</td>
                                <td>Upto Prev. Day</td>
                                <td>Today</td>
                                <td>Upto Date</td>
                            </tr>

                        </thead>

                        <tbody>
                            
                        </tbody>

                    </table>
                </div>
                @endif
                
            </div>
            <!--row -->

        </div>

        @endsection
        @section('script')
        <script>

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

            
            $(document).ready(function () {
                var table = $('.daily_abstract_table').DataTable();
            })

        </script>
        @endsection