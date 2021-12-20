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
    </div>
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
        <span class="error"> <b><p>Approval Flow:HR->SuperAdmin</p></b></span>       
        <a href="{{ route('admin.add_rule') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Rule</a>
            <p class="text-muted m-b-30"></p>
            <br>                
            <div class="table-responsive">
                <table id="policy_table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Company Rule</th>
                            <th>Rule Document</th>
                            <th>First Approval Status</th>
                            <th>Second Approval Status</th>
							<th>Action</th>
						</tr>
                    </thead>
                    <tbody>                            
                    </tbody>
                </table>
            </div>
        </div>  
        <div id="galleryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Compnay Rules</h4>
                    </div>
                    <div class="modal-body" id="tableBodyPolicy">
                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>          
    </div>    
@endsection

@section('script')		
<script>
    $(document).ready(function () {

        var table = $('#policy_table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
			"stateSave": true,
            "ajax": {
                url: "<?php echo route('admin.get_companyrule_list'); ?>",
                type: "GET",
            },
            "columns": [
				{"taregts": 0, "searchable": true, "data": "rule_name"},
                {"taregts": 1, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        // Download
                        var id = row.id;
                        var out = "";
                        if (row.rule_document) {
                            var images = row.rule_document;
                            var images_arr = images.split(',');
                            $.each(images_arr, function (key, val) {
                                var file_path = val.replace("public/", "");
                                var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                                out += '<a href="' + download_link + '" title="Download document" target="_blank" download><i class="fa fa-cloud-download fa-lg"></i></a>';
                            });
                        }
                        return out;
                    }
                },
                {"taregts": 2, "searchable": true, "orderable": false,
                    "render": function (data, type, row) {
                        // firstApproval status if need use isValid
                        var id = row.id;
                        var out = "";
                        if(row.first_approval_datetime){
                            out = moment(row.first_approval_datetime).format("DD-MM-YYYY hh:ss A");
                        }
                        if(row.first_approval_status == "Pending"){
                            return '<b class="text-warning">Pending</b>';
                        }else if (row.first_approval_status == 'Approve') {
                            return '<b class="text-success data-toggle="tooltip" >Approve<br>'+out+'</b>';
                        } else if (row.first_approval_status == 'Reject') {
                            return '<b class="text-danger">Reject<br>'+out+'</b>';
                        } else {
                            return '<b class="text-danger">Reject</b>';
                        }
                    }
                },
                {"taregts": 3, "searchable": true, "orderable": false,
                    "render": function (data, type, row) {
                        // secondApproval status if need use isValid;
                        var id = row.id;
                        var out = "";
                        if(row.second_approval_datetime){
                            out = moment(row.second_approval_datetime).format("DD-MM-YYYY hh:ss A");
                        }
                        if(row.second_approval_status == "Pending"){
                            return '<b class="text-warning">Pending</b>';
                        }else if (row.second_approval_status == 'Approve') {
                            return '<b class="text-success data-toggle="tooltip" >Approve <br>'+out+'</b>';
                        } else if (row.second_approval_status == 'Reject') {
                            return '<b class="text-danger">Reject<br>'+out+'</b>';
                        } else {
                            return '<b class="text-danger">Reject</b>';
                        }
                    }
                },
				{"taregts": 4, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = "";
                        if ("{{Auth::user()->role}}" == "{{config('constants.REAL_HR')}}" || "{{Auth::user()->role}}" == 1){
                            if (row.first_approval_status == "Pending") { 
                                out += '<a href="<?php echo url("edit_rules") ?>'+'/'+id+'" class="btn btn-primary btn-rounded" title="Edit"><i class="fa fa-edit"></i></a>';
                            }
                            if (row.first_approval_status == "Pending") {
                                out += '<a href="<?php echo url("delete_rule") ?>'+'/'+id+'" class="btn btn-danger btn-rounded" title="Delete"><i class="fa fa-trash"></i></a>';
                            }
                        }
                        

                        // hr approve/reject btn
                        if ("{{Auth::user()->role}}" == "{{config('constants.REAL_HR')}}" && row.first_approval_status == "Pending") {
                            out+= '<a href="<?php echo url("approve_rule") ?>' + '/' + id + '" class="btn btn-success btn-rounded" title="Approve"><i class="fa fa-check"></i></a>';
                            out+= '<a href="<?php echo url("reject_rule") ?>' + '/' + id + '" class="btn btn-danger btn-rounded" title="Reject"><i class="fa fa-times"></i></a>';
                        }

                        // super admin approve/reject btn
                        if ("{{Auth::user()->role}}" == "1" && row.first_approval_status == "Approve" && row.second_approval_status == "Pending") {
                            out+= '<a href="<?php echo url("approve_rule") ?>' + '/' + id + '" class="btn btn-success btn-rounded" title="Approve"><i class="fa fa-check"></i></a>';
                            out+= '<a href="<?php echo url("reject_rule") ?>' + '/' +id +'" class="btn btn-danger btn-rounded" title="Reject"><i class="fa fa-times"></i></a>';
                        }

                        // companyRuleNotify
                        return out;
                    }
                },
                
            ]
        });
    })
// function openPolicy(pdf,id) {
//     $('#tableBodyPolicy').empty();
//     var iframeUrl = "<iframe src="+pdf+"#toolbar=0 height='400' width='880'></iframe>";
//     $('#tableBodyPolicy').append(iframeUrl);
// }
</script>
@endsection