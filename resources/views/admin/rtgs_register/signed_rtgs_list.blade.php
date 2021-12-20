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
                    <a href="{{url('add_signed_rtgs')}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Signed RTGS</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>RTGS Ref No</th>
                                <th>RTGS No</th>
                                <th>Company</th>
                                <th>Bank(Account No)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($signed_list)
                                @foreach($signed_list as $key => $value)
                                <tr>
                                    <td>{{$value[0]['rtgs_ref_no']}}</td>
                                    @php
                                        $last_no = end($value);
                                    @endphp
                                    <td>{{$value[0]['rtgs_no']}} - {{$last_no['rtgs_no']}}</td>
                                    <td>{{$value[0]['company_name']}}</td>
                                    <td>{{$value[0]['bank_name']}}({{$value[0]['ac_number']}})</td>
                                    
                                </tr>
                                @endforeach
                            @endif
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
                $('#company_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ]
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
                        url: "<?php echo route('admin.get_signed_rtgs_list'); ?>",
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
                    ]

                });

            })
            
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