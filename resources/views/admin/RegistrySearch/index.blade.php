<?php
    use Illuminate\Support\Facades\Config;
?>

@extends('layouts.admin_app')

@section('content')

<link href="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<link href="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.inward_outward') }}">{{ $module_title }}</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
    </div>
    <div class="row">

        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('admin.registry_search') }}" id="attendance_frm" method="post" class="form-material" accept-charset="utf-8">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <input type="hidden" id="checkDate" value="{{ $registry_date }}">
                                <div class="col-md-5">
                                    <div class="form-group">
                                    <div class="col-sm-2">
                                    <button type="button" class="btn waves-effect btn-rounded waves-light btn-primary"><i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-sm-10">
                                    <input type="text"  autocomplete="off" placeholder="Search..." required name="search_registry" id="search_registry" value="<?php echo $search_registry ? $search_registry : "" ?>" class="form-control">
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group ">
                                         <div class="col-sm-2">
                                    <button type="button" class="btn waves-effect btn-rounded waves-light btn-primary"><i class="fa fa-calendar"></i></button>
                                    </div>
                                        <div class="col-sm-10">
                                            <input type="text" autocomplete="off" class="form-control timeseconds shawCalRanges" name="registry_date" id="registry_date" value="<?php echo $registry_date ? $registry_date : "" ?>"  >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                <button type="button"  onclick="ClearFields();" class="btn btn-secondary btn-rounded">Clear All</button>
                                </div>                                    
                            </div>
                        <br>
                        <br>
                        <br>
                            <div class="form-actions">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-12 col-sm-12">
        
            <div class="white-box">
               
                <p class="text-muted m-b-30"></p>
                </br>
                <div class="table-responsive">
                    <table id="registry_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Registry No</th>
                                <th width="150px">Title</th>
                                <th width="200px">Description</th>
                                <th>Company</th>
                                <th>Project</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Document</th>
                                <th>Received Date</th>
                                <th id="expected_date">Expected Ans Date</th>
                                <th>Created Date</th>
                                <th>Assign Users</th>
                                <th>Type</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if( count($records) > 0)
                        @foreach($records as $list_data)
                            <tr>
                            <td>{{$list_data->inward_outward_no}}</td>
                                <td>{{$list_data->inward_outward_title}}</td>
                                <td>{{$list_data->description}}</td>
                                <td>{{$list_data->company_name}}</td>
                                <td>{{$list_data->project_name}}</td>
                                <td>{{$list_data->category_name}}</td>
                                <td>{{$list_data->sub_category_name}}</td>
                                <td><a title="Download" href="{{ asset('storage/'.str_replace('public/','',!empty($list_data->document_file) ? $list_data->document_file : 'public/no_image')) }}" download><i class="fa fa-cloud-download  fa-lg"></i></a></td>
                                <td><span style="display: none;">{{ $list_data->received_date }}</span>
                                    {{ Carbon\Carbon::parse($list_data->received_date)->format('d-m-Y') }}
                                </td>
                                @if($list_data->expected_ans_date!="" || $list_data->expected_ans_date!=NULL)
                                <td>
                                    <span style="display: none;">{{ $list_data->expected_ans_date }}</span>{{ Carbon\Carbon::parse($list_data->expected_ans_date)->format('d-m-Y') }}
                                </td>
                                @else
                                <td>NA</td>
                                @endif

                                <td><span style="display: none;">{{ $list_data->created_at }}</span>
                                    {{ Carbon\Carbon::parse($list_data->created_at)->format('d-m-Y H:i:s') }}
                                </td>
                                <td>
                                <h5>{{$list_data->users_list}}</h5>
                                </td>

                                <td>
                                    @if($list_data->type == 'Inwards')
                                    <b class="text-success">Inward</b>
                                    @else
                                    <b class="text-danger">Outward</b>
                                    @endif
                                </td>
                                <td>
                                   <a title="View Details" href="{{ route('admin.view_inward_to_outward',['id'=>$list_data->parent_inward_outward_no]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>            
        </div>    
    </div>
</div>
@endsection

@section('script')		
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/moment/moment.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/select2/dist/js/select2.full.min.js" type="text/javascript"></script>

<script>
function ClearFields() {

document.getElementById("search_registry").value = "";
document.getElementById("registry_date").value = "";
}

$(document).ready(function () {
    

    $('#registry_table').DataTable({
		dom: 'Bfrtip',buttons: [
            'copy', 'csv', 'excel', 'print'
        ],
        stateSave: true
    });
    

    $('.shawCalRanges').daterangepicker({
        showDropdowns: false,
        // startDate : false,   //moment().subtract(10, 'days')
        // endDate: false,
        timePicker: true,
        timePickerIncrement: 1,
        timePicker24Hour: true,
        locale: {
        format: 'D/M/YYYY H:mm'
    },
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    alwaysShowCalendars: true,
    });

    let checkDate = document.getElementById("checkDate").value;
    if (checkDate) {
    }else{
        document.getElementById("registry_date").value = "";
    }

});
</script>
@endsection