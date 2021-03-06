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
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                    <form action="#" id="used_cheque" method="get">
                        @csrf
                        <div class="row">                        
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="inputEmail4">Company</label>
                                    <select class="form-control" name="company_id" id="company_id">
                                        <option value="">Select Company</option>
                                        @if($companies)
                                            @foreach($companies as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Bank Name</label>
                                    <select class="form-control" id="bank_id" name="bank_id" >
                                        <option value="">Select Bank</option>
                                        @if($banks)
                                            @foreach($banks as $key => $value)
                                                <option value="{{$value['id']}}">{{$value['bank_name']}} ({{$value['ac_number']}})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Client</label>
                                    <select class="form-control" id="client_id" name="client_id">
                                        <option value="">Select Client</option>
                                        @if($clients)
                                            @foreach($clients as $key => $value)
                                                @if($value['id'] != 1)
                                                    <option value="{{$value['id']}}">{{$value['client_name']}}</option>
                                                @else
                                                    <option value="{{$value['id']}}">{{$value['client_name']}}-{{$value['location']}}</option>
                                                @endif
                                                
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Project</label>
                                    <select class="form-control" id="project_id" name="project_id">
                                        <option value="">Select Project</option>
                                        @if($projects)
                                            @foreach($projects as $key => $value)
                                                @if($value != "Other Project")
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Vendor</label>
                                    <select class="form-control" id="vendor_id" name="vendor_id">
                                        <option value="">Select Vendor</option>
                                        @if($vendors)
                                            @foreach($vendors as $key => $value)
                                                @if($value['vendor_name'] != "Other" && $value['vendor_name'] != "Others")
                                                    <option value="{{$value['id']}}">{{$value['vendor_name']}}{{(!empty($value['company']['company_short_name']) && $value['company']['company_short_name'] != null) ? " - ".$value['company']['company_short_name'] : ""}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>RTGS Ref No</label>
                                    <select class="form-control" id="rtgs_ref_no" name="rtgs_ref_no">
                                        <option value="">Select Rtgs Ref No</option>
                                        @if($rtgs_ref_no)
                                            @foreach($rtgs_ref_no as $key => $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>RTGS No</label>
                                    <select class="form-control" id="rtgs_no" name="rtgs_no">
                                        <option value="">Select Rtgs No</option>
                                        @if($rtgs_no)
                                            @foreach($rtgs_no as $key => $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Amount</label>
                                    <input type="text" class="form-control" id="amount" name="amount" placeholder="Amount">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Issue Date</label>
                                    <input type="text" class="form-control" id="issue_date" name="issue_date" placeholder="Issue Date">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Clear Date</label>
                                    <input type="text" class="form-control" id="cl_date" name="cl_date" placeholder="Clear Date">
                                </div>
                                <input type="hidden" name="search_used_cheque" value="search_used_cheque">
                            </div>
                        </div>
                        
                        {{-- <div class="row">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <button type="button" name="search_used_cheque" value="search_used_cheque" class="btn btn-success search_used_cheque">Search</button>
                                </div>
                            </div>
                        </div> --}}
                    </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12">
            @if (session('error'))
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">??</button>
                {{ session('error') }}
            </div>
            @endif
            @if (session('success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">??</button>
                {{ session('success') }}
            </div>
            @endif
            <div class="white-box">
                <form action="{{ route('admin.delete_rtgs') }}" id="delete_rtgs_frm" method="post">
                @csrf
                <input type="hidden" name="del_rtgs_ids" id="del_rtgs_ids"/>
                </form>

                <form action="{{ route('admin.signed_rtgs') }}" id="signed_rtgs_frm" method="post">
                @csrf
                <input type="hidden" name="signed_rtgs_ids" id="signed_rtgs_ids"/>
                </form>

                <!-- <a href="{{ route('admin.add_rtgs_register') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add RTGS</a> -->
                <!-- <button class="btn btn-danger btn-rounded" id="del_rtgs">Delete RTGS</button> -->
                <!-- <button class="btn btn-success btn-rounded" id="sign_rtgs" style="display: none;">Signed RTGS</button> -->
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>RTGS Ref No</th>
                                <th>Company</th>
                                <th>Bank(Account No)</th>
                                <th>RTGS No</th>
                                <th>Client Name</th>
                                <th>Project Name</th>
                                <th>Vender</th>
                                <th>Issue Date</th>
                                <th>Clear Date</th>
                                <th>Amount</th>
                                <th>Work Detail</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
         <div id="workModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">??</button>
                        <h4 class="modal-title" id="myModalLabel">Details</h4>
                    </div>
                    <div class="modal-body" id="tableBodyWork">
                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div> 
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                $("#company_id").select2();
                $("#bank_id").select2();
                $("#client_id").select2();
                $("#project_id").select2();
                $("#vendor_id").select2();
                $("#rtgs_ref_no").select2();
                $("#rtgs_no").select2();
                jQuery('#issue_date,#cl_date').datepicker({
                    format: 'dd-mm-yyyy',
                    autoclose: true,
                    todayHighlight: true
                });
                var table = $('#company_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    stateSave: true,
                    'serverMethod': 'post',
                    "ajax": {
                        url: "<?php echo route('admin.get_used_rtgs_list'); ?>",
                        type: "GET",
                        "data": function ( d ) {
                            var company_id = $('#company_id').val();
                            var amount = $('#amount').val();
                            var bank_id = $('#bank_id').val();
                            var client_id = $('#client_id').val();
                            var project_id = $('#project_id').val();
                            var vendor_id = $('#vendor_id').val();
                            var rtgs_ref_no = $('#rtgs_ref_no').val();
                            var rtgs_no = $('#rtgs_no').val();
                            var issue_date = $('#issue_date').val();
                            var cl_date = $('#cl_date').val();

                            d.company_id = company_id;
                            d.amount = amount;
                            d.bank_id = bank_id;
                            d.client_id = client_id;
                            d.project_id = project_id;
                            d.vendor_id = vendor_id;
                            d.rtgs_ref_no = rtgs_ref_no;
                            d.rtgs_no = rtgs_no;
                            d.issue_date = issue_date;
                            d.cl_date = cl_date;
                        }
                    },
                    "columns": [
                        {"taregts": 1, 'data': 'rtgs_ref_no'
                        },
                        {"taregts": 2, 'data': 'company_name'
                        },
                        {"taregts": 3, "render": function (data, type, row) {
                                return row.bank_name + '(' + row.ac_number + ')';
                            }
                        },
                        {"taregts": 4, 'data': 'rtgs_no'
                        },
                        {"taregts": 5, 'data': 'client_name'
                        },
                        {"taregts": 6, 'data': 'project_name'
                        },
                        {"taregts": 7, 'data': 'vendor_name'
                        },
                        {"taregts":8,'render' : function(data, type , row){
                            return row.issue_date ? moment(row.issue_date).format("DD-MM-YYYY") : '';
                        }
                        },
                        {"taregts":9,'render' : function(data, type , row){
                            return row.cl_date ? moment(row.cl_date).format("DD-MM-YYYY") : '';
                        }
                        },
                        {"taregts": 10, 'data': 'amount'
                        },
                        {"taregts": 11, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                return "<input type='hidden' value='"+row.work_detail+"' id='work_detail_hide_"+row.id+"' name='work_detail_hide' /><a onclick=openWork("+row.id+") href='#' data-toggle='modal' data-target='#workModal'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                            }
                        },
                        {"taregts": 12, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                if(row.remark){
                                    return "<input type='hidden' value='"+row.remark+"' id='remark_detail_hide_"+row.id+"' name='remark_detail_hide' /><a onclick=openRemark('"+row.id+"') href='#' data-toggle='modal' data-target='#workModal'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                }
                                return "";
                            }
                        }
                    ]

                });

            })
            function openWork(id) {
                $('#tableBodyWork').html(''); 
                if($('#work_detail_hide_'+id).val()!='null'){
                $('#tableBodyWork').html($('#work_detail_hide_'+id).val());   
                }
                else{
                    $('#tableBodyWork').html('No record added');  
                }  
            }
            function openRemark(id){
                $('#tableBodyWork').html('');
                if($('#remark_detail_hide_'+id).val()!='null'){
                    $('#tableBodyWork').html($('#remark_detail_hide_'+id).val());   
                }
                else{
                    $('#tableBodyWork').html('No record added');  
                }
            }

            $("#company_id").change(function() {
            var company_id = $("#company_id").val();
            if(company_id != ""){
            $('#company_table').DataTable().draw();
            $.ajax({
                url: "{{ route('admin.get_bank_list_cheque')}}",
                type: 'get',
                data: "company_id="+company_id,
                success: function( data, textStatus, jQxhr ){
                    $('#bank_id').empty();
                    $('#bank_id').append(data);
                    $('#bank_id').val("");
                    $('#bank_id').prepend('<option value="">All bank</option>');
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
            //client
            htmlStr = '';
                $.ajax({
                url: "{{ route('admin.get_company_client_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    company_id: company_id
                },
                dataType: "JSON",
                success: function(data) {

                    $("#client_id").empty();
                    $("#client_id").append("<option value='' selected>Select Client</option>");
                    $.each(data, function(index, clients_obj) {
                        
                        if (clients_obj.id == 1) {
                            //  htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                        }else{
                            htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                        }
                        
                        //$("#client_id").append('<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>');
                    });
                    $("#client_id").append(htmlStr);
                }
            });

            //vender
            $.ajax({
                url: "{{ route('admin.get_cash_vendor_list')}}",
                type: 'get',
                data: "company_id=" + company_id,
                success: function (data, textStatus, jQxhr) {
                    $('#vendor_id').empty();
                    $('#vendor_id').append(data);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

            //rtgs_ref_no
            $.ajax({
                url: "{{ route('admin.get_used_rtgs_ref_no_list')}}",
                type: 'post',
                data: {
                    '_token' : "{{csrf_token()}}",
                    company_id : company_id
                },
                success: function (data, textStatus, jQxhr) {
                    $('#rtgs_ref_no').empty();
                    $('#rtgs_ref_no').append(data);
                    $('#rtgs_ref_no').val("");
                    $('#rtgs_ref_no').prepend('<option value="">All Rtgs Ref No</option>');
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
            }else{
                /* render_banks();
                render_clients(); */

                location.reload();
            }
        });

        $('#client_id').change(() => {
            
            //project list
            client_id = $("#client_id").val();

            $.ajax({
                url: "{{ route('admin.get_client_project_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    client_id: client_id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#project_id").empty();
                    $("#project_id").append("<option value='' selected>Select Project</option>");

                    $.each(data, function(index, projects_obj) {


                        if(projects_obj.project_name != "Other" && projects_obj.project_name != "Others"  && projects_obj.project_name != "Other Project")
                            $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');

                    });
                }
            });
        });

        $("#rtgs_ref_no").on('change',function(){
            var rtgs_ref_no = $(this).val();
            var bank_id = $("#bank_id").val();
            var company_id = $("#company_id").val();
            $.ajax({
                        url: "{{ route('admin.get_used_rtgs_number_list')}}",
                        type: 'POST',
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                        data: {
                            rtgs_ref_no : rtgs_ref_no
                        },
                        success: function(data, textStatus, jQxhr) {
                            $('#rtgs_no').empty();
                            $('#rtgs_no').append(data);
                            $('#rtgs_no').val("");
                            $('#rtgs_no').prepend('<option value="">All Rtgs No</option>');
                        },
                        error: function(jqXhr, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    });
        });
        $('#amount').keyup(function(){
            $('#company_table').DataTable().draw();
        });
        
        $("#bank_id").on('change',function(){
            $('#company_table').DataTable().draw();
        });
        $("#client_id").on('change',function(){
            $('#company_table').DataTable().draw();
        });
        $("#project_id").on('change',function(){
            $('#company_table').DataTable().draw();
        });
        $("#vendor_id").on('change',function(){
            $('#company_table').DataTable().draw();
        });
        $("#rtgs_ref_no").on('change',function(){
            $('#company_table').DataTable().draw();
        });
        $("#rtgs_no").on('change',function(){
            $('#company_table').DataTable().draw();
        });
        $("#issue_date").on('change',function(){
            $('#company_table').DataTable().draw();
        });
        $("#cl_date").on('change',function(){
            $('#company_table').DataTable().draw();
        });
        </script>
        @endsection