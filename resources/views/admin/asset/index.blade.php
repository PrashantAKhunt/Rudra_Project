@extends('layouts.admin_app')

@section('content')
<style>
/* .modal-dialog{
  width:50%;
  margin: auto;
} */
</style>
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
                        <th width="50%">Details</th>
                        <th>Image</th>
                        <th>Status</th>
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
                                <label>Asset Name <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="name" id="name"/>
                            </div>
                            <div class="form-group "> 
                                <label>Descption <span class="error">*</span> </label> 
                                <textarea class="form-control" rows="5" name="description" id="description">
                                </textarea>
                            </div>
                            <div class="form-group "> 
                                <label>Asset Number (Mobile/Vehicle) <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="asset_1" id="asset_1"/>
                            </div>
                            <div class="form-group "> 
                                <label>Asset Number (Imie/Chassis) <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="asset_2" id="asset_2"/>
                            </div>
                            <div class="form-group ">
                                <label>Asset Image</label>
                                <input type="file" accept="image/png,image/x-png, image/jpg, image/jpeg" name="image[]" id="image" class="dropify" multiple/>
                            </div>
                            <div class="form-group ">
                                <label>Warranty Expiration Date</label>
                                <input type="text" class="form-control reminder_date" readonly="" name="expiration_date" id="expiration_date" value="" />
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
        <div id="reminderDateModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Reminder Dates</h4>
                    </div>
                    <div class="modal-body">
                    <button type="button" id="add_btn"  title="Add" style="display: none;" class="btn btn-primary pull-right" onclick="add_div();"><i class="fa fa-plus"></i> ADD </button>
                    <p class="text-muted m-b-30"></p>
                <br>
                    <form action="{{ route('admin.update_reminder_dates') }}" id="update_dates" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="asset_id" name="asset_id"/> 
                            
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="edit_reminder">
                        </tbody>
                    </table>

                            <button type="submit" id="submitBtn" style="display: none;" class="btn btn-success">Submit</button>
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </form>        
                    
                    </div>

                      </div>
                      </div>
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
jQuery('.reminder_date').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: "dd-mm-yyyy"
            }); 
    $(document).ready(function(){
         $('#update_asset').validate({
            rules:{
                name:{
                    required:true
                },
                description:{
                    required:true
                },
				asset_1:{
					required:true
				},
				asset_2:{
					required:true
				},
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
        stateSave: true,
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
                            var img = row.image;
                            var baseURL = img.replace("public/","");
                            var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;


                           //var url=  "<?php echo url('/storage/');?>"+"/"+row.image;
                           return "<a onclick=openImageGallery('"+url.replace("public/","")+"','"+row.id+"') href='#' data-toggle='modal' data-target='#galleryModal'><img height='100px' width='100px' src="+ url +"></a>";
                        }
                    }
            },
            {"taregts": 4,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = '';
                        if(row.status=='Enabled'){
                        out += '<a href="<?php echo url('change_asset') ?>'+'/'+id+'/Disabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                        }
                        else{
                        out += '<a href="<?php echo url('change_asset') ?>'+'/'+id+'/Enabled'+'" class="btn btn-danger" title="Change Status">'+row.status+'</a>';    
                        }
                        return out;
                    }
            },
            {"taregts": 5, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    if (($.inArray('2',access_rule) !== -1)){
                    out = '<a onclick="getTable('+id+')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#myModal" title="Edit"><i class="fa fa-edit"></i></a>';
                    }
                    if (($.inArray('4',access_rule) !== -1)){
                    /*out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_asset'); ?>/' + id + '\'\n\
                      title="Delete"><i class="fa fa-trash"></i></a>'*/
                    }
                    if (($.inArray('1',access_rule) !== -1)){
                    //out += '<a onclick="getAssetDetails('+row.id+')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#assetDetailsModal" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    }
                    out+=' <a href="#" onclick="get_reminderDates('+row.id+');" title="Reminder Dates" data-target="#reminderDateModel" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-calendar"></i></a>';
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

    function get_reminderDates(id) {
        $('#asset_id').val(id); 
        $('#submitBtn').hide(); 
        $('#add_btn').hide(); 
        trHTML = '';
            $.ajax({
                    url: "{{ route('admin.aseet_expired_reminder_dates') }}",
                    type: "post",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    success: function(data) {
                        if (data.status) {
                            $('#edit_reminder').empty();
                            var reminderDates = data.data.reminder_dates;
                            if (reminderDates.length == 0) {
                                
                                $('#edit_reminder').empty();
                                $('#edit_reminder').append('<span>No Records Found !</span>');
                            
                            }else{
                                    $.each(reminderDates, function(index, files_obj) {

                                        no = index + 1;
                                        date = moment(files_obj).format("DD-MM-YYYY");

                                        trHTML += '<tr><td>' + no + '</td>' +
                                            '<td><input type="text" class="form-control reminder_date" required readonly="" name="reminder_date['+index+']"  value="'+date+'" /></td>'+
                                            '<td></td>'+
                                            '<tr/>';
                                        });
                                   
                                $('#edit_reminder').append(trHTML);
                                $('#submitBtn').show(); 
                                $('#add_btn').show();
                                jQuery('.reminder_date').datepicker({
                                    autoclose: true,
                                    todayHighlight: true,
                                    format: "dd-mm-yyyy"
                                }); 
                            }
            
                        }else{
                            $('#edit_reminder').empty();
                            $('#edit_reminder').append('<span>No Records Found !</span>');
                        }
            
                    }
                });
    
} 
function remove_div(e) {
    $(e).closest("tr").remove();
    var numberOfInputs = $("#edit_reminder").find("input").length;
        
        re_id = '#remove_btn'+numberOfInputs+'';
        $(re_id).show(); 
    
}

function add_div() {

    var numberOfInputs = $("#edit_reminder").find("input").length;

    //var rowCount = $('#edit_reminder tr').length / 2;
    count = numberOfInputs +1;
    //index = rowCount -1;
    ins_count = count-1;
    trHTML = '<tr>' +
                '<td>' + count + '</td>' +
                '<td><input type="text" class="form-control reminder_date" required readonly="" name="reminder_date['+numberOfInputs+']"  value="" /></td>'+
                '<td><button type="button" id="remove_btn'+count+'" title="Remove" class="btn btn-danger" onclick="remove_div(this);"><i class="fa fa-trash"></i></button></td>'+
                '<tr/>'; 
          $('#edit_reminder').append(trHTML);
          jQuery('.reminder_date').datepicker({
                                    autoclose: true,
                                    todayHighlight: true,
                                    format: "dd-mm-yyyy"
                                });
          re_id = '#remove_btn'+ins_count+'';
          $(re_id).hide(); 
    
}   
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
                 let exp_date = v.expiration_date ? moment(v.expiration_date).format("DD-MM-YYYY") : '';
                 $("#expiration_date").val(exp_date);
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
                 var url =  "<?php echo url('/storage/');?>"+"/"+v.image;
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
                            var img = v.image;
                            var baseURL = img.replace("public/","");
                            var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                            
                 var imgTag = "<img height='100px' width='100px' src="+ url +">";
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
