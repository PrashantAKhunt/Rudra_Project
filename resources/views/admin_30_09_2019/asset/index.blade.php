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
        <div class="col-md-12">
            <div class="white-box">
                 <?php
                 $role = explode(',', $access_rule);
                 if(in_array(3, $role)){
                ?>
                <a href="{{ route('admin.add_asset') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Asset</a>
                <?php
                }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th>Name</th>
                        <th>Details</th>
                        <th>Image</th>
                        <th>Action</th>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- sample modal content -->
        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Edit Asset</h4>
                    </div>
                    <div class="modal-body">


                        <form action="{{ route('admin.update_asset') }}" id="update_asset" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id"/>                            
                             <div class="form-group "> 
                                <label>Asset Name</label> 
                                <input type="text" class="form-control" name="name" id="name"/>
                            </div>
                            <div class="form-group "> 
                                <label>Department Descption</label> 
                                <textarea class="form-control" rows="5" name="description" id="description">
                                </textarea>
                            </div>
                            <div class="form-group "> 
                                <label>Asset Number (Mobile/Vehicle)</label> 
                                <input type="text" class="form-control" name="asset_1" id="asset_1"/>
                            </div>
                            <div class="form-group "> 
                                <label>Asset Number (Imie/Chassis)</label> 
                                <input type="text" class="form-control" name="asset_2" id="asset_2"/>
                            </div>
                            <div class="form-group ">
                                <label>Asset Image</label>
                                <input type="file" accept="image/png,image/x-png, image/jpg, image/jpeg" name="image" id="image" class="dropify" />
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div> 

        <div id="assetDetailsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Asset Expense Details</h4>
                    </div>
                    <div class="modal-body" id="tableBody">
                      <table id="asset_expenseTable" class="table table-striped">
                      </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div id="galleryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Images</h4>
                    </div>
                    <div class="modal-body" id="tableBodyImage">
                       
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
    $(document).ready(function(){
         $('#update_asset').validate({
            rules:{
                name:{
                    required:true
                },
                description:{
                    required:true
                }
            }
        });
    });
    var access_rule = '<?php echo $access_rule; ?>';
    access_rule = access_rule.split(',');
    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[1, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.asset_list'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 1, 'data': 'name'
            },
            {"taregts": 2, 'data': 'description'
            },
            {"taregts": 3,
                    "render": function (data, type, row) {
                        if(row.image==null)
                        {
                            return "Not Image";
                        }
                        else
                        {
                           var url=  "<?php echo url('/storage/app/');?>"+"/"+row.image;
                           return "<a onclick=openImageGallery('"+url.replace("public/","")+"','"+row.id+"') href='#' data-toggle='modal' data-target='#galleryModal'><img height='100px' width='100px' src="+url.replace("public/","")+"></a>";
                        }
                    }
            },
            {"taregts": 4, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    if (($.inArray('2',access_rule) !== -1)){
                    out = '<a onclick="getTable('+id+')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#myModal" title="Edit"><i class="fa fa-edit"></i></a>';
                    }
                    if (($.inArray('4',access_rule) !== -1)){
                    out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_asset'); ?>/' + id + '\'\n\
                      title="Delete"><i class="fa fa-trash"></i></a>'
                    }
                    if (($.inArray('1',access_rule) !== -1)){
                    out += '<a onclick="getAssetDetails('+row.id+')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#assetDetailsModal" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    }
                    return out;
                }
            },
        ]

    });

    jQuery("#upload_bank_transactions").validate({
        ignore: [],
        rules: {

            file: {
                required: true,
            },

        },

    });
function changeDateformat(date)
{
    //return date.split("-");
    var fields = date.split('-');
    var name = fields[0];
    var street = fields[1];
    var Month = ['Jan','Feb','March','April','May','Jun','July','Aug','Sep','Oct','Nov','Dec'];
    return Month[name-1]+'-'+street;
}
function delete_confirm(e) {
    swal({
        title: "Are you sure you want to delete Asset ?",
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
function getTable(id)
    {
        // AJAX request
        $.ajax({
            url:"<?php echo url('edit_asset') ?>"+"/"+id+"",
            method: 'get',
            data: {event_id: id},
            dataType: 'json',
            success: function(response){
                if(response.length==0)
                {
                    $('#clTable').empty();
                    $('#clTable').append('<span>No Records Found !</span>');
                }
                else{
                    //var myJSON = JSON.stringify(response);
                    //console.log(myJSON);
                    $.each(response, function(k, v) {
                     $("#id").val(v.id);
                     $("#name").val(v.name);
                     $("#description").val(v.description);
                     $("#asset_1").val(v.asset_1);
                     $("#asset_2").val(v.asset_2);
                    });
                    
                }
            }
        });
    }
function getAssetDetails(id)
{
    // AJAX request
    $.ajax({
        url:"<?php echo url('get_asset_expense_details') ?>"+"/"+id+"",
        method: 'get',
        data: {asset_id: id},
        dataType: 'json',
        success: function(response){
           var myJSON = JSON.stringify(response);
            if(response.length==0 || response.status==0)
            {
                $('#asset_expenseTable').empty();
                $('#asset_expenseTable').append('<span>No Records Found !</span>');
            }
            else{
                //var myJSON = JSON.stringify(response);
                //console.log(myJSON);
                var html = '<thead>'
                            +'<tr>'
                            +'<th>Amount</th>'
                            +'<th>Image</th>'
                            +'<th>Note</th>'
                            +'</tr>'
                            +'</thead>'
                            +'<tbody>'; 
                $.each(response, function(k, v) {
                 var url =  "<?php echo url('/storage/app/');?>"+"/"+v.image;
                 var imgTag = "<img height='100px' width='100px' src="+url.replace("public/","")+">";
                 html+='<tr>'
                        +'<td>'
                        +v.amount
                        +'</td>'
                        +'<td>'
                        +imgTag
                        +'</td>'
                         +'<td>'
                        +v.note
                        +'</td>'
                        +'</tr>'
                }); 
                html+='</tbody>'
                $('#asset_expenseTable').empty();
                $('#asset_expenseTable').append(html);
            }
        }
    });
}
function openImageGallery(images,id) {
    $('#tableBodyImage').empty();
    // AJAX request
    $.ajax({
        url:"<?php echo url('get_asset_images') ?>"+"/"+id+"",
        method: 'get',
        data: {asset_id: id},
        dataType: 'json',
        success: function(response){
           var myJSON = JSON.stringify(response);
            if(response.length==0 || response.status==0)
            {
                $('#tableBodyImage').empty();
                $('#tableBodyImage').append('<span>No Records Found !</span>');
            }
            else{
                //var myJSON = JSON.stringify(response);
                //console.log(myJSON);
                var html = '<thead>'
                            +'</thead>'
                            +'<tbody><tr>'; 
                $.each(response, function(k, v) {
                 var url =  "<?php echo url('/storage/app/');?>"+"/"+v.image;
                 var imgTag = "<img height='100px' width='100px' src="+url.replace("public/","")+">";
                 html   +='<td>'
                        +imgTag
                        +'</td>'
                }); 
                html+='</tr></tbody>'
                $('#tableBodyImage').empty();
                $('#tableBodyImage').append(html);
            }
        }
    });
}
</script>
@endsection
