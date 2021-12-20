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
            if(in_array(3, $role)){
            ?>
            <a href="{{ route('admin.add_policy') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Policy</a>
            <?php } ?>
            <p class="text-muted m-b-30"></p>
            <br>                
            <div class="table-responsive">
                <table id="policy_table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Name</th>
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
                        <h4 class="modal-title" id="myModalLabel">Policy</h4>
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
        var access_rule = '<?php echo $access_rule; ?>';
        access_rule = access_rule.split(',');

        var table = $('#policy_table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                url: "<?php echo route('admin.get_policy_list'); ?>",
                type: "GET",
            },
            "columns": [
				{"taregts": 0, "searchable": true, "data": "title"},
                {"taregts": 1,
                    "render": function (data, type, row) {
                        if(row.name==null) {
                            return "Not Policy";
                        }
                        else {
                           var url=  "<?php echo url('/storage/app/');?>"+"/"+row.name;
                           return "<a onclick=openPolicy('"+url.replace("public/","")+"','"+row.id+"') href='#' data-toggle='modal' data-target='#galleryModal'>Open Policy</a>";
                        }
                    }
                },
				{"taregts": 2, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = "";
                        if (($.inArray('2',access_rule) !== -1)) {
                            out = '<a href="<?php echo url("edit_policy") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                        }
                        if (($.inArray('2',access_rule) !== -1)) {
                            out+= '<a href="<?php echo url("delete_policy") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                        }
                        if (($.inArray('2',access_rule) !== -1)) {
                            out+= '<a href="<?php echo url("revise_policy") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-list"></i></a>';
                        }
                        return out;
                    }
                }
            ]
        });
    })
function openPolicy(pdf,id) {
    $('#tableBodyPolicy').empty();
    var iframeUrl = "<iframe src="+pdf+"#toolbar=0 height='400' width='880'></iframe>";
    $('#tableBodyPolicy').append(iframeUrl);
}
</script>
@endsection