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
                <form action="{{ route('admin.delete_rtgs') }}" id="delete_rtgs_frm" method="post">
                @csrf
                <input type="hidden" name="del_rtgs_ids" id="del_rtgs_ids"/>
                </form>

                <form action="{{ route('admin.signed_rtgs') }}" id="signed_rtgs_frm" method="post">
                @csrf
                <input type="hidden" name="signed_rtgs_ids" id="signed_rtgs_ids"/>
                </form>
                @if(config::get('constants.ACCOUNT_ROLE') == Auth::user()->role)
                <a href="{{url('add_failed_rtgs')}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Failed RTGS</a>
                @endif
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
                                <th>Failed Reason</th>
                                <th>Download</th>
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
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
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
        <!-- View Failed Cheuqe -->
        <div id="failed_cheque_view" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Failed RTGS Detail</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Detail :-</label>
                                    <p id="failed_reason_detail"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Attach File :-</label>
                                    <div id="failed_document_file">
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  -->
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                
                $("#del_rtgs").click(function(){
                    var favorite = [];
                    $.each($("input[name='delete_rtgs']:checked"), function(){
                        favorite.push($(this).val());
                    });
                    
                    var id = favorite.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select RTGS !",
                            //text: "You want to change status of admin user.",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Okay",
                            closeOnConfirm: true
                        });
                    }
                    else {
                        
                        swal({
                            title: "Are you sure you want to delete RTGS ?",
                            //text: "You want to change status of admin user.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            closeOnConfirm: false
                        }, function () {
                           $("#del_rtgs_ids").val(id);
                           $("#delete_rtgs_frm").submit();
                        });
                    }
                });

                $("#sign_rtgs").click(function(){
                    var favorite = [];
                    $.each($("input[name='delete_rtgs']:checked"), function(){
                        favorite.push($(this).val());
                    });
                    var id = favorite.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select RTGS !",
                            //text: "You want to change status of admin user.",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Okay",
                            closeOnConfirm: true
                        });
                    }
                    else {
                        
                        swal({
                            title: "Are you sure you selected RTGS are signed ?",
                            //text: "You want to change status of admin user.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            closeOnConfirm: false
                        }, function () {
                           $("#signed_rtgs_ids").val(id);
                           $("#signed_rtgs_frm").submit();
                        });
                    }
                });

                var table = $('#company_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    // "order": [[5, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_failed_rtgs_list'); ?>",
                        type: "GET",
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
                        {"taregts": 5, 'data': 'failed_reason'
                        },
                        {"taregts": 6,"searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                var path = row.failed_document;
                                var baseURL = path.replace("public/","");
                                var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                                // out += '<input type="hidden" value="'+row.failed_reason+'" id="failed_reason_hide'+row.id+'" name="failed_reason_hide" /><input type="hidden" value="'+row.failed_document+'" id="failed_document_hide'+row.id+'" name="failed_document_hide" /><a href="#" onclick="show_detail(' + row.id + ');" data-toggle="modal" data-target="#failed_cheque_view" class="btn btn-danger" title="View details">'+'Reason'+'</a>';
                                out += '<a href="'+ url +'" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>';
                                return out;
                            }
                        }
                    ]

                });

            })
            
        function getModel(id) {
                $('#failed_reason').val(''); 
                $('#failed_document').val(''); 
                
                $('#cheque_id').val(id);   
            }
            function show_detail(id) {
                
                $('#failed_document_file').empty();
            
                $('#failed_reason_detail').html($('#failed_reason_hide'+id).val());

                var path = $('#failed_document_hide'+id).val();
                if (path != 'null') {

                    var baseURL = path.replace("public/","");
                    var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;


                    $('#failed_document_file').append('<a href="'+ url +'" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>');
                }else{
                    $('#failed_document_file').append('<span>No File!</span>');
                }
                
            }
        </script>
        @endsection