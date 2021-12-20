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
                <form action="{{ route('admin.delete_rtgs') }}" id="delete_rtgs_frm" method="post">
                @csrf
                <input type="hidden" name="del_rtgs_ids" id="del_rtgs_ids"/>
                </form>

                <form action="{{ route('admin.signed_rtgs') }}" id="signed_rtgs_frm" method="post">
                @csrf
                <input type="hidden" name="signed_rtgs_ids" id="signed_rtgs_ids"/>
                </form>

                <a href="{{ route('admin.add_rtgs_register') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add RTGS</a>
                <button class="btn btn-danger btn-rounded" id="del_rtgs">Delete RTGS</button>
                <button class="btn btn-success btn-rounded" id="sign_rtgs" style="display: none;">Signed RTGS</button>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Bank</th>
                                <th>RTGS Ref No</th>
                                <th>RTGS No</th>
                                <th>Project Name</th>
                                <th>Vendor</th>
                                
                                <th>Amount</th>
                                <th>Work Detail</th>
                                <th>Remark</th>
                                <th>Created Date</th>
                                <th>Is Used</th>
                                <th>Is Signed</th>
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
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_rtgs_register_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 1, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                if(row.is_used=='not_used'){
                                    out = '&nbsp;<input type="checkbox" value='+id+' name="delete_rtgs" >';
                                }
                                return out;
                            }
                        },
                        {"taregts": 2, 'data': 'company_name'
                        },
                        {"taregts": 3, 'data': 'bank_name'
                        },
                        {"taregts": 4, 'data': 'rtgs_ref_no'
                        },
                        {"taregts": 5, 'data': 'rtgs_no'
                        },
                        {"taregts": 6, 'data': 'project_name'
                        },
                        {"taregts": 7, 'data': 'vendor_name'
                        },
                        {"taregts": 8,'data':'amount'
                        },
                        {"taregts": 9, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                return "<input type='hidden' value='"+row.work_detail+"' id='work_detail_hide_"+row.id+"' name='work_detail_hide' /><a onclick=openWork("+row.id+") href='#' data-toggle='modal' data-target='#workModal'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                            }
                        },
                        {"taregts": 10, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                return "<input type='hidden' value='"+row.remark+"' id='remark_detail_hide_"+row.id+"' name='remark_detail_hide' /><a onclick=openRemark('"+row.id+"') href='#' data-toggle='modal' data-target='#workModal'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                            }
                        },
                        {"taregts":11,'render' : function(data, type , row){
                            return row.created_at ? moment(row.created_at).format("DD-MM-YYYY") : '';
                        }
                        },

                        {"taregts": 12, 
                            "render": function (data, type, row) {
                                if(row.is_used=='not_used'){
                                    out='<b class="text-warning">RTGS not used</b>';
                                }
                                else
                                {
                                    out='<b class="text-success">RTGS used</b>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 13,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if(row.is_used=='not_used'){
                                    if(row.is_signed=='no'){
                                     out += '<a href="<?php echo url('change_rtgs_status') ?>'+'/'+id+'/yes'+'" class="btn btn-danger" title="Click to mark as already signed yes">'+'No'+'</a>';    
                                    }
                                    else{
                                   out += '<a href="<?php echo url('change_rtgs_status') ?>'+'/'+id+'/no'+'" class="btn btn-success" title="Click to mark as already signed no">'+'Yes'+'</a>';
                                    }
                                }
                                else {
                                    out+='<span style="background:#8ed8c4" class="btn btn-success">'+'Yes'+'</span>';
                                }
                                return out;
                            }
                        },


                        {"taregts": 14, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 
                                // out = '<a href="<?php echo url('edit_company') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>'; 
                                if(row.is_used=='not_used')
                                {

                                   
                                }
                                
                                return out;
                            }
                        },
                    ]

                });

            })
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
                //$('#tableBodyWork').html($('#work_detail_hide_'+id).val());    

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