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
                <form action="{{ route('admin.select_tender') }}" id="delete_cheque_frm" method="post">
                @csrf
                <input type="hidden" name="select_tender_ids" id="select_tender_ids"/>
                </form>
                @if($add_permission)
                <a href="{{ route('admin.add_tender') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Tender</a>
                @endif
                <button class="btn btn-success btn-rounded" id="select_tender">Select Tender</button>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="tender_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tender Sr No</th>
                                <th>Department</th>
                                <th>Tender Id Per Portal</th>
                                <th>Portal Name</th>
                                <th>Tender No</th>
                                <th>Name Of Work</th>
                                <th>State Name Work Execute</th>
                                <th>Estimate Cost</th>
                                <th>Joint Venture</th>
                                <th>Joint Venture Count</th>
                                <th>Quote Type</th>
                                <th>Other Quote Type</th>
                                <th>Tender Pattern</th>
                                <th>Tender Category</th>
                                <th>Last Date Time Download</th>
                                <th>Last Date Time Online Submit</th>
                                <th>Last Date Time Physical Submit</th>
                                <th>Tender Assign</th>
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
        
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {

//================================================= Select Tender start 
                $("#select_tender").click(function(){
                    var favorite = [];
                    $.each($("input[name='delete_ch']:checked"), function(){
                        favorite.push($(this).val());
                    });
                    var id = favorite.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select tender !",
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
                            title: "Are you sure you want to select tender ?",
                            //text: "You want to change status of admin user.",
                            type: "info",
                            showCancelButton: true,
                            confirmButtonColor: "#006600",
                            confirmButtonText: "Yes",
                            closeOnConfirm: false
                        }, function () {
                           $("#select_tender_ids").val(id);
                           $("#delete_cheque_frm").submit();
                           console.log(id);
                        });
                    }
                });
                //================================================= Select Tender end


                var table = $('#tender_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "order": [[0, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_tender_list_all'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var user_id_arr= row.user_ids.split(',');
                                var out = "";
                                
                                if(user_id_arr.includes('{{ Auth::user()->id }}')){
                                    out = '&nbsp;<input type="checkbox" value='+id+' name="delete_ch" >';
                                }
                                return out;
                            }
                        },
                        {"taregts": 1, 'data': 'tender_sr_no'},
                        {"taregts": 2, 'data': 'dept_name'},
                        {"taregts": 3, 'data': 'tender_id_per_portal'},
                        {"taregts": 4, 'data': 'portal_name'},
                        {"taregts": 5, 'data': 'tender_no'},
                        {"taregts": 6, 'data': 'name_of_work'},
                        {"taregts": 7, 'data': 'state_name_work_execute'},
                        {"taregts": 8, 'data': 'estimate_cost'},
                        {"taregts": 9, 'data': 'joint_venture'},
                        {"taregts": 10, "orderable": false,'data': 'joint_venture_count'},
                        {"taregts": 11, 'data': 'quote_type'},
                        {"taregts": 12, "orderable": false,'data': 'other_quote_type'},
                        {"taregts": 13, 'data': 'tender_pattern_name'},
                        {"taregts": 14, 'data': 'tender_category'},
                        {"taregts": 15, "searchable": false, "orderable": false,"render":function(data,type,row){
                                return moment(row.last_date_time_download).format("DD-MM-YYYY h:mm:a");
                            }
                        },
                        {"taregts": 16, "searchable": false, "orderable": false,"render":function(data,type,row){
                                return moment(row.last_date_time_online_submit).format("DD-MM-YYYY h:mm:a");
                            }
                        },
                        {"taregts": 17, "searchable": false, "orderable": false,"render":function(data,type,row){
                                return moment(row.last_date_time_physical_submit).format("DD-MM-YYYY h:mm:a");
                            }
                        },
                        {"taregts": 18, 'data': 'fullname'},
                        {"taregts": 19, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 
                                out = '<a href="<?php echo url('edit_tender') ?>'+'/'+id+'" class="btn btn-primary btn-rounded" title="Edit Tender"><i class="fa fa-edit"></i></a>'; 
                                /*out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_tender'); ?>/' + id + '\'\n\
                                title="Delete Tender"><i class="fa fa-trash"></i></a>';*/
                                return out;
                            }
                        },
                    ]

                });
            })
        function delete_confirm(e) {
            swal({
                title: "Are you sure you want to delete tender ?",
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
        @endsection