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
                @if($view_special_permission)
                <a href="{{ route('admin.add_company') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Company</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company Title</th>
                                <th>Company Short Name</th>
                                <th>GST No</th>
                                <th>PAN No</th>
                                <th>CIN No</th>
                                <th>TAN No</th>
                                <th>Status</th>
                                <th>Created date</th>
                                <th>MOA Document</th>
                                <th>GST Document</th>
                                <th>PAN Card Document</th>
                                <th>TAN Card Document</th>
                                <th> Certificate of Incorporation (COI) Document</th>
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
    </div>
</div>
<div class="modal fade" id="crt_imagesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Certificate of Incorporation (COI) Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="cmp_crt_images">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
    $(document).ready(function() {
        var check_permission = <?php echo json_encode($view_special_permission); ?>;
        var table = $('#company_table').DataTable({
            dom: 'lBfrtip',
            buttons: ['excel'],
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [
                [1, "DESC"]
            ],
            "ajax": {
                url: "<?php echo route('admin.get_company_list'); ?>",
                type: "GET",
            },
            "columns": [

                {
                    "taregts": 0,
                    'data': 'company_name'
                },
                {
                    "taregts": 1,
                    'data': 'company_short_name'
                },
                {
                    "taregts": 2,
                    'data': 'gst_no'
                },
                {
                    "taregts": 3,
                    'data': 'pan_no'
                },
                {
                    "taregts": 4,
                    'data': 'cin_no'
                },
                {
                    "taregts": 5,
                    'data': 'tan_no'
                },
                {
                    "taregts": 6,
                    "render": function(data, type, row) {
                        var id = row.id;
                        var out = '';
                        if (check_permission) {
                            if (row.status == 'Enabled') {
                                out += '<a href="<?php echo url('change_company_status') ?>' + '/' + id + '/Disabled' + '" class="btn btn-success" title="Change Status">' + row.status + '</a>';
                            } else {
                                out += '<a href="<?php echo url('change_company_status') ?>' + '/' + id + '/Enabled' + '" class="btn btn-danger" title="Change Status">' + row.status + '</a>';
                            }
                        } else {
                            if (row.status == 'Enabled') {
                                out += '<a href="#" class="btn btn-success" title="Change Status">' + row.status + '</a>';
                            } else {
                                out += '<a href="#" class="btn btn-danger" title="Change Status">' + row.status + '</a>';
                            }
                        }
                        return out;
                    }
                },
                {
                    "taregts": 7,
                    "render": function(data, type, row) {
                        return moment(row.created_at).format("DD-MM-YYYY");
                    }
                },
                {
                    "taregts": 8,
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        if (row.moa_image) {
                            var pdf_path = row.moa_image.replace("public", "");
                            var storage_path = "{{url('storage/')}}" + pdf_path;
                            return '<a class="btn btn-rounded btn-primary" href="' + storage_path + '" target="_blank"><i class="fa fa-eye"></i></a>';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "taregts": 9,
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        if (row.gst_image) {
                            var pdf_path = row.gst_image.replace("public", "");
                            var storage_path = "{{url('storage/')}}" + pdf_path;
                            return '<a class="btn btn-rounded btn-primary" href="' + storage_path + '" target="_blank"><i class="fa fa-eye"></i></a>';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "taregts": 10,
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        if (row.pan_image) {
                            var pdf_path = row.pan_image.replace("public", "");
                            var storage_path = "{{url('storage/')}}" + pdf_path;
                            return '<a class="btn btn-rounded btn-primary" href="' + storage_path + '" target="_blank"><i class="fa fa-eye"></i></a>';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "taregts": 11,
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        if (row.tan_image) {
                            var pdf_path = row.tan_image.replace("public", "");
                            var storage_path = "{{url('storage/')}}" + pdf_path;
                            return '<a class="btn btn-rounded btn-primary" href="' + storage_path + '" target="_blank"><i class="fa fa-eye"></i></a>';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "taregts": 12,
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return '<a class="btn btn-rounded btn-primary" onclick="get_crt_images(' + row.id + ');" href="javascript:void(0)" ><i class="fa fa-eye"></i></a>';
                    }
                },
                /* u need to focus above here for display the code */
                {
                    "taregts": 13,
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        var id = row.id;
                        var out = "";
                        if (check_permission) {
                            out = '<a href="<?php echo url('edit_company') ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                        }
                        //out +='&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_company'); ?>/' + id + '\'\n\
                        //       title="Delete"><i class="fa fa-trash"></i></a>';
                        //out +='&nbsp;<a href="<?php echo url('cmp_document_list') ?>'+'/'+id+'" class="btn btn-primary btn-rounded" title="List"><i class="fa fa-list"></i></a>';
                        return out;
                    }
                },
            ]

        });

    })

    function delete_confirm(e) {
        swal({
            title: "Are you sure you want to delete Company ?",
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

    function get_crt_images(id) {
        $.ajax({
            type: "POST",
            url: "{{route('admin.get_company_crt_images')}}",
            data: {
                "_token": "{{csrf_token()}}",
                company_id: id
            },
            success: function(res) {
                if (res.data.length) {
                    $("#cmp_crt_images").empty();
                    // cmp_crt_images

                    let cmp_crt_images = '';


                    $.each(res.data, function(index, row) {
                        let img = row.image;
                        let baseURL = img.replace("public/", "");
                        let url = "<?php echo url('/storage/'); ?>" + "/" + baseURL;
                        var storage_path = "{{url('storage/')}}/" + baseURL;
                        cmp_crt_images += '<a href="' + storage_path + '" target="_blank"><i class="fa fa-cloud-download" aria-hidden="true"></i> &nbsp;</a>';
                    })

                    $("#cmp_crt_images").append(cmp_crt_images);

                    $("#crt_imagesModal").modal('show');
                } else {
                    $.toast({
                        heading: "Notification",
                        text: "Image not found",
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'info',
                        hideAfter: 5000
                    });
                }
                console.log(res);
            }
        })
    }
</script>
@endsection