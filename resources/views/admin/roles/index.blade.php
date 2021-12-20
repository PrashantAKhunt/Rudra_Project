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
                
                <a href="{{ url('addroles') }}" class="btn btn-primary" style="float: right;"><i class="fa fa-plus"></i>&nbsp;Add Roles</a>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table class="table table-primary table-bordered" id="roletable" cellspacing="0" width="100%">
                                            <thead class="table_header_color">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Role Name</th>
                                                    <th>Action</th>
                                                </tr>
                                        </table>
                </div>
            </div>
            <!--row -->

        </div>
    </div>
    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close text-white" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="header-word" id="frm_title">Delete</h4>
            </div>
            <div class="modal-body">
                <h5>Are you sure you want to delete this role?</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-info danger">Delete</a>
            </div>
        </div>
    </div>
</div>  
        @endsection
        @section('script')
        <script>


    jQuery(document).ready(function () {
        jQuery("#slowout").delay(5000).show().fadeOut('slow');
        jQuery('#roletable').DataTable({
            "oLanguage": {
                "sProcessing": '<img alt src="<?php //echo site_url('images/loaders/CustomLoader.gif'); ?>" style="opacity: 1.0;filter: alpha(opacity=100);">'
            },
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [[1, "DESC"]],
            "ajax": "{{ url('getRole') }}",
            "columns": [

                {"taregts": 0, "searchable": false,"orderable": false,"render" : function(data,type,row,meta){
                    return meta.row + 1;
                    }
                },
                {"taregts": 1, "data": "role_name"},
                {"taregts": 2, "searchable": false,
                    "orderable": false,
                    "sClass": "text-center",
                    "render": function (data, type, row) {
                        var id = row.id;
                        var action_html;
                        action_html = '<a class="btn btn-primary btn-rounded" href=\'<?php echo url('editroles'); ?>/' + id + '\'\n\
                      title="Edit"><i class="glyphicon glyphicon-edit"></i></a>';

                        /*action_html += '&nbsp;<a class="btn btn-danger btn-rounded" title="Delete" data-href="<?php //echo url('deleteroles') ?>/' + id + '"  data-toggle="modal" data-target="#confirm-delete" href="#" >\n\
                     <i class="glyphicon glyphicon-trash"></i></a>';*/


                        return action_html;
                    }

                },
            ]
        });

        $('#confirm-delete').on('show.bs.modal', function (e) {
            $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
        });
        $('#mymodel').trigger('click');
    });
</script>
        @endsection
