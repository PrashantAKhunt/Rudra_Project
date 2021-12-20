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
            <a href="{{ route('admin.add_announcements') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Announcements</a>
            <p class="text-muted m-b-30"></p>
            <br>                
            <div class="table-responsive">
                <table id="announcements_table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Edit</th>
							<th>Delete</th>
						</tr>
                    </thead>
                    <tbody>                            
                    </tbody>
                </table>
            </div>
        </div>            
    </div>    
@endsection

@section('script')		
<script>
    $(document).ready(function () {
        var table = $('#announcements_table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                url: "<?php echo route('admin.get_announcements_list'); ?>",
                type: "GET",
            },
            "columns": [
				{"taregts": 0, "searchable": true, "data": "title"},
                {"taregts": 1, "searchable": true, "data": "description"},
				{"taregts": 2, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        return '<a href="<?php echo url("edit_announcements") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                    }
                },
				{"taregts": 3, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        return '<a href="<?php echo url("delete_announcements") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                    }
                },
            ]
        });
    })
</script>
@endsection