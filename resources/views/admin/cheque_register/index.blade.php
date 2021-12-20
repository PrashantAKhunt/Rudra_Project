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
                <form action="{{ route('admin.delete_cheques') }}" id="delete_cheque_frm" method="post">
                @csrf
                <input type="hidden" name="del_cheque_ids" id="del_cheque_ids"/>
                </form>

                <form action="{{ route('admin.signed_cheques') }}" id="signed_cheque_frm" method="post">
                @csrf
                <input type="hidden" name="signed_cheque_ids" id="signed_cheque_ids"/>
                </form>

                <a href="{{ route('admin.add_cheque_register') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Cheque</a>
                
                <button class="btn btn-danger btn-rounded" id="del_cheque">Delete Cheque</button>
                <button class="btn btn-success btn-rounded" id="sign_cheque" style="display: none;">Signed Cheque</button>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Bank</th>
                                <th>Check Ref No</th>
                                <th>Chque No</th>
                                <th>Project Name</th>
                                <th>Vendor</th>
                                <th>Issue Date</th>
                                <th>Clear Date</th>
                                <th>Amount</th>
                                <th>Work Detail</th>
                                <th>Remark</th>
                                <th>Is Used</th>
                                <th>Is Signed</th>
                                <th>Cheque Failed</th>
                                <th>Action</th>
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

        <!-- Reject -->
        <div id="reject_cheque_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Failed Reason</h4>
                    </div>
                    <div class="modal-body">
                    <form action="{{ route('admin.cheque_failed') }}" id="cheque_failed" method="post" enctype="multipart/form-data">
                            @csrf
                        <div class="row">
                        <input type="hidden" name="cheque_id" id="cheque_id" value="">
                            
                            <div class="form-group "> 
                                <label>Detail</label> 
                                <textarea class="form-control" rows="3" required name="failed_reason" id="failed_reason"></textarea>
                            </div>
                            <div class="form-group ">
                                <label>Document</label>
                                <input type="file" name="failed_document" id="failed_document" />
                            </div>
                        
                        </div>
                        <hr class="m-t-0">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div> -->
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--  -->

        <!-- View Failed Cheuqe -->
        <div id="failed_cheque_view" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Failed Cheque Detail</h4>
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
                
    //================================================= Delete 
                $("#del_cheque").click(function(){
                    var favorite = [];
                    $.each($("input[name='delete_ch']:checked"), function(){
                        favorite.push($(this).val());
                    });
                    var id = favorite.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select cheque !",
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
                            title: "Are you sure you want to delete cheque ?",
                            //text: "You want to change status of admin user.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            closeOnConfirm: false
                        }, function () {
                           $("#del_cheque_ids").val(id);
                           $("#delete_cheque_frm").submit();
                        });
                    }
                });
    //====================================================
                $("#sign_cheque").click(function(){
                    var favorite = [];
                    $.each($("input[name='delete_ch']:checked"), function(){
                        favorite.push($(this).val());
                    });
                    var id = favorite.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select cheque !",
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
                            title: "Are you sure you selected cheques are signed ?",
                            //text: "You want to change status of admin user.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            closeOnConfirm: false
                        }, function () {
                           $("#signed_cheque_ids").val(id);
                           $("#signed_cheque_frm").submit();
                        });
                    }
                });
    //=============================Ajax Table
                var table = $('#company_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_cheque_register_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 1, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                if(row.is_used=='not_used'){
                                    out = '&nbsp;<input type="checkbox" value='+id+' name="delete_ch" >';
                                }
                                return out;
                            }
                        },
                        {"taregts": 2, 'data': 'company_name'
                        },
                        {"taregts": 3, 'data': 'bank_name'
                        },
                        {"taregts": 4, 'data': 'check_ref_no'
                        },
                        {"taregts": 5, 'data': 'ch_no'
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
                        {"taregts":10,'data':'amount'
                        },
                        {"taregts": 11, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                return "<input type='hidden' value='"+row.work_detail+"' id='work_detail_hide_"+row.id+"' name='work_detail_hide' /><a onclick=openWork("+row.id+") href='#' data-toggle='modal' data-target='#workModal'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                            }
                        },
                        {"taregts": 12, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                return "<input type='hidden' value='"+row.remark+"' id='remark_detail_hide_"+row.id+"' name='remark_detail_hide' /><a onclick=openRemark('"+row.id+"') href='#' data-toggle='modal' data-target='#workModal'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                            }
                        },

                        {"taregts": 13, 
                            "render": function (data, type, row) {
                                if(row.is_used=='not_used'){
                                    out='<b class="text-warning">Cheque not used</b>';
                                }
                                else
                                {
                                    out='<b class="text-success">Cheque used</b>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 14,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if(row.is_used=='not_used'){
                                    if(row.is_signed=='no'){
                                     out += '<a href="<?php echo url('change_cheque_status') ?>'+'/'+id+'/yes'+'" class="btn btn-danger" title="Click to mark as already signed yes">'+'No'+'</a>';    
                                    }
                                    else{
                                   out += '<a href="<?php echo url('change_cheque_status') ?>'+'/'+id+'/no'+'" class="btn btn-success" title="Click to mark as already signed no">'+'Yes'+'</a>';
                                    }
                                }
                                else {
                                    out+='<span style="background:#8ed8c4" class="btn btn-success">'+'Yes'+'</span>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 15,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if(row.is_failed== 0){

                                     out += '<a onclick="getModel('+id+')" class="btn btn-success" data-toggle="modal" data-target="#reject_cheque_modal" title="Click to mark as cheque failed">'+'No'+'</a>';    
                                }
                                else {
                                    out += '<input type="hidden" value="'+row.failed_reason+'" id="failed_reason_hide'+row.id+'" name="failed_reason_hide" /><input type="hidden" value="'+row.failed_document+'" id="failed_document_hide'+row.id+'" name="failed_document_hide" /><a href="#" onclick="show_detail(' + row.id + ');" data-toggle="modal" data-target="#failed_cheque_view" class="btn btn-danger" title="View details">'+'Yes'+'</a>';
                                }
                                return out;
                            }
                        },


                        {"taregts": 16, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 
                                // out = '<a href="<?php echo url('edit_company') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>'; 
                                if(row.is_used=='not_used')
                                {
                                   
                                    /*out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_cheque_register'); ?>/' + id + '\'\n\
                                        title="Delete"><i class="fa fa-trash"></i></a>';*/

                                   
                                }
                                else
                                {
                                     out += '<a href="<?php echo url('edit_cheque_register') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>'; 
                                }
                                return out;
                            }
                        },
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
            function delete_confirm(e) {
            swal({
                title: "Are you sure you want to delete cheque ?",
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
        </script>
        @endsection