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
                <?php
                $role = explode(',', $access_rule);
                if (in_array(3, $role)) {
                    ?>
                    <a href="{{ route('admin.add_pro_sign_letter_detail') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Letter-head Request</a>
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br> 
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.LETTER_HEAD_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>UserName</th>
                                <th>Title</th>
                                <th>Company Name</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                
                                <th>Letter Head Number</th>
                                <th>Vendor Name</th>
                                <th>Request Date</th>
                                <th>Requested Content</th>
                                <th>Status</th>
                                <th>Delivered Letter-head Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>        
        </div>  

        <div id="letter_content" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Requested Content</h4>
                    </div>
                    <div class="modal-body" id="tableBodylatterContent">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>  
        
        <div id="viewLetterModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">View Letter-head</h4>
                        </div>
                        <div class="modal-body" id="letterView">
                            <!-- <img src="" id="letterImage"> -->
                            <iframe id="letterImage" src="" height="200" width="500"></iframe>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div> 
            </div> 
        @endsection

        @section('script')		
        <script>
            $(document).ready(function () {
                var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');

                var table = $('#policy_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_pro_sign_letter_list'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {"taregts": 0, "searchable": true, "data": "user_name"},
                        {"taregts": 1, "searchable": true, "data": "title"},
                        {"taregts": 2, "searchable": true, "data": "company_name"},
                        {"taregts": 3, "searchable": true, "data": "project_name"},
                        {"taregts": 4, "searchable": true, "data": "other_project_detail"},
                        
                        {"taregts": 5, "searchable": true, "data": "letter_head_number"},
                        {"taregts": 6, "searchable": true, "data": "vendor_name"}, 
                        {"taregts": 7, "searchable": true, "render": function (data, type, row) {

                                return moment(row.created_at).format("DD-MM-YYYY h:m a");
                            }
                        },
                        {"taregts": 8, "searchable": false,"orderable": false, "render": function (data, type, row) {

                                return '<input type="hidden" id="letter_content_input_' + row.id + '" value="' + row.note + '" /><a href="#" class="btn btn-info btn-rounded" onclick="open_letter_content(' + row.id + ')" data-toggle="modal" data-target="#letter_content" title="View Content"><i class="fa fa-eye"></i></a>';
                            }
                        },
                            
                        {"taregts": 9,
                            "render": function (data, type, row) {
                                var out = '';
                                if (row.status == 'Pending')
                                {
                                    return'<b class="text-warning">Pending</b>';
                                } else if (row.status == 'Approved')
                                {
                                    return '<b class="text-success">Approved</b>';
                                } else
                                {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },
                        {"taregts": 10, "searchable": false,"orderable": false, "render": function (data, type, row) {
                                if(row.letter_head_image){
                                return '<a href="#" class="btn btn-info btn-rounded" onclick=view_letter_head("'+row.letter_head_image+'") title="View delivered letterhead image"><i class="fa fa-eye"></i></a>';
                            }
                            else{
                                return "Not Delivered";
                            }
                            }
                        },  
                        {"taregts": 11, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                out +='<a title="Download Request Content Document file" class="btn btn-rounded btn-info" href="<?php echo url("download_normal_letter_head_content") ?>' + '/' + id + '" target="_blank"><i class="fa fa-download"></i></a> &nbsp;';
                                if (($.inArray('2', access_rule) !== -1)) {
                                    out += '<a href="<?php echo url("edit_pro_sign_letter_detail") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                }
                                // if (($.inArray('2',access_rule) !== -1)) {
                                //     out+= '<a href="<?php echo url("delete_policy") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                                // }
                                return out;
                            }
                        }
                    ]
                });
            })
            function open_letter_content(id) {

                $('#tableBodylatterContent').html($('#letter_content_input_' + id).val());
            }
            function view_letter_head(letterImage) {
        var img_url="<?php echo url('/storage/'); ?>"+"/"+letterImage.replace('public/','');
        $("#letterImage").attr("src", img_url);
        $('#viewLetterModel').modal('toggle');
    }
        </script>
        @endsection