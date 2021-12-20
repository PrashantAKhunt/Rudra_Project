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
                <li><a href="{{ route('admin.outwards') }}">{{ $page_title }}</a></li>
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
                
                @if($full_view_permission && $add_permission && Auth::user()->role!=config('constants.SuperUser'))
                <a href="{{ route('admin.add_outward') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add outward</a>
                @endif
                <!-- <form action="{{ route('admin.outwards') }}" method="POST" id="range_date"> -->
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-material">
                                <label class="col-sm-3 control-label">Date<label class="serror"></label> </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control timeseconds shawCalRanges" name="range_date" id="range_date" value="{{($range_date != "") ? $range_date : ""}}"  value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-offset-1 col-md-3">
                            <button type="button" class="btn btn-success filter_daterange"> <i class="fa fa-check"></i> Search</button>
                        </div>
                    </div>
                <!-- </form> -->

                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                            <th width="100px">Outward Registry No</th>
                                 <th width="100px">Outward Title</th>
                                <th width="200px">Description</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Send Datetime</th>
                                <th id="expected_date">Expected Ans Date</th>
                                <th>Outward Document</th>
                                <th>Created Date</th>
                                <th width="150px", data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        </tbody>
                    </table>
                </div>

            </div>
            <!--row -->

        </div>

        @endsection
        @section('script')

<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/moment/moment.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="http://eliteadmin.themedesigner.in/demos/bt4/assets/node_modules/select2/dist/js/select2.full.min.js" type="text/javascript"></script>

<script>
            $(document).ready(function () {
                var table = $('#user_table').DataTable({
                    "lengthMenu": [[10, 25, 50,100,300,500,-1], [10, 25, 50,100,300,500, "All"]],
                    dom: 'Blfrtip',
                    buttons: [
                        // 'excelHtml5',
                        'pdfHtml5'
                    ],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    stateSave: true,
                    /* "order": [
                        [3, "DESC"]
                    ], */
                    "ajax": {
                        url: "<?php echo route('admin.outwards'); ?>",
                        type: "GET",
                        "data": function ( d ) {
                            var range_date = $('#range_date').val();

                            d.range_date = range_date;
                        }
                    },
                    "columns": [{
                            "targets": 0,
                            'data': 'inward_outward_no'
                        },
                        {
                            "targets": 1,
                            'data': 'inward_outward_title'
                        },
                        {
                            "targets": 2,
                            'data': 'description'
                        },
                        {
                            "targets": 3,
                            'data': 'category_name',
                            'render' : function(data,type,row){
                                if(row.category_name){
                                    return row.category_name;
                                }else{
                                    return "";
                                }
                            }
                        },
                        {
                            "targets": 4,
                            'data': 'sub_category_name',
                            'render' : function(data,type,row){
                                if(row.sub_category_name){
                                    return row.sub_category_name;
                                }else{
                                    return "";
                                }
                            }
                        },
                        {
                            "targets": 5,
                            'data': 'received_date'
                        },
                        {
                            "targets": 6,
                            'data': 'expected_ans_date'
                        },
                        {
                            "targets": 7,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = "";
                                var path = row.document_file;
                                if (path) {
                                    var baseURL = path.replace("public/","");
                                    var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                                    out += '<a href="'+ url +'" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>';
                                }
                                return out;
                            }
                        },
                        {
                            "targets": 8,
                            'data': 'created_at'
                        },
                        {
                            "targets": 9,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = "";
                                out += '<a href="<?php echo url('view_inward_to_outward') ?>' + '/' + id + '/outward" class="btn btn-primary btn-rounded" title="view records"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                                out += '<a href="<?php echo url('registry_chat') ?>' + '/' + id + '" class="btn btn-success btn-rounded" title="Chat"><i class="fa fa-comments-o" aria-hidden="true"></i></a>';
                                return out;
                            }
                        },
                        

                    ]
                });
            });
            function delete_confirm(e) {
                swal({
                    title: "Are you sure you want to delete document ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    window.location.href = $(e).attr('data-href');
                });
            }
        </script>
        <script>
            function delete_confirm(e) {
                swal({
                    title: "Are you sure you want to delete document ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }
        </script>
<!-- 2nd date picker -->

<script>

$('#select_all').click(function() {
        if($(this).prop("checked") == true){
            $('#user_id').select2('destroy');   
            $('#user_id option').prop('selected', true);
            $('#user_id').select2();
        }else{
            $('#user_id').select2('destroy');   
            $('#user_id option').prop('selected', false);
            $('#user_id').select2();
        }
    });
    $('.showdropdowns').daterangepicker({
        showDropdowns: true,
        //   timePicker: false,
        //     timePickerIncrement: 30,
            locale: {
                format: 'MM/DD/YYYY'
            }
    }); 

    $('.shawCalRanges').daterangepicker({
        showDropdowns: false,
        // timePicker: false,
        // timePickerIncrement: 1,
        // timePicker24Hour: true,
        // singleDate : true,
        locale: {
        format: 'D/M/YYYY'
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
    $(".shawCalRanges").val("");

// });

$(".filter_daterange").on('click',function(){
    $('#user_table').DataTable().draw();
});

</script>
<!-- 2nd date picker -->

@endsection
