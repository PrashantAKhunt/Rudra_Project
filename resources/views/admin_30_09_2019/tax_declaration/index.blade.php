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
                <!-- <a href="{{ route('admin.add_employee_loan') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Apply Tax</a> -->
                <?php
                $role = [];
                if(!empty($access_rule)) {
                    $role = explode(',', $access_rule);
                }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th>Section Name</th>
                        <th>Deduction Name</th>
                        <th>Declaration</th>
                        <th>Proofs</th>
                        <!-- <th>Financial Start Year</th>
                        <th>Financial End Year</th> -->
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
                        <h4 class="modal-title" id="myModalLabel">Edit Tax Declartion</h4>
                    </div>
                    <div class="modal-body">


                        <form action="{{ route('admin.update') }}" id="update_employee_loan" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id"/>                            
                             <div class="form-group "> 
                                <label>Section Name</label> 
                                <input type="text" class="form-control" name="section_name" id="section_name" readonly="" />
                            </div>
                             <div class="form-group "> 
                                <label>Deduction Name</label> 
                                <input type="text" class="form-control" name="deduction_name" id="deduction_name" readonly="" />
                            </div>
                            <div class="form-group "> 
                                <label>Declaration</label> 
                                <input type="text" class="form-control" name="declaration" id="declaration"/>
                            </div>
                            
                            <!-- <div class="form-group "> 
                                <label>Proofs</label> 
                                <input type="text" class="form-control" name="proofs" id="proofs"/>
                            </div> -->
                            <div class="form-group ">
                                <label>Proofs</label>
                                <input type="file" accept="image/x-png, image/jpg, image/jpeg" name="proofs" id="proofs" class="dropify" />
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
</div>
@endsection


@section('script')
<script>
    var access_rule = '<?php echo $access_rule; ?>';
    access_rule = access_rule.split(',');
    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[1, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.employee_tax_declaration_list'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 1, 'data': 'section_name'
            },
            {"taregts": 2, 'data': 'deduction_name'
            },
            // {"taregts": 3, 'data': 'declaration'
            // },
            {"taregts": 3,
                    "render": function (data, type, row) {
                        if(row.declaration==null)
                        {
                            return "Not Declared";
                        }
                        else
                        {
                            return row.declaration;
                        }
                    }
            },
            {"taregts": 4,
                    "render": function (data, type, row) {
                        if(row.proofs==null)
                        {
                            return "Not Proof";
                        }
                        else
                        {
                           var url=  "<?php echo url('/storage/app/');?>"+"/"+row.proofs;
                           return "<img height='100px' width='100px' src="+url.replace("public/","")+">";
                        }
                    }
            },
            {"taregts": 7,
                    "render": function (data, type, row) {
                        if(row.status=='Pending')
                        {
                            return "Not Submitted";
                        }
                        else
                        {
                            return row.status;
                        }
                    }
            },
            {"taregts": 8, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    if (($.inArray('2',access_rule) !== -1)){
                    out = '<a href="<?php echo url('edit_tax_declaration') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                    }
                    if (($.inArray('4',access_rule) !== -1)){
                    out = '<a onclick="getTable('+id+')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#myModal" title="Edit"><i class="fa fa-edit"></i></a>';
                    }
                    
                    /*out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_employee_loan'); ?>/' + id + '\'\n\
                      title="Delete"><i class="fa fa-trash"></i></a>';*/
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
        title: "Are you sure you want to delete loan ?",
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
            url:"<?php echo url('edit_tax_declaration') ?>"+"/"+id+"",
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
                     $("#section_name").val(v.section_name);
                     $("#deduction_name").val(v.deduction_name);
                     $("#declaration").val(v.declaration);
                     $("#proofs").val(v.proofs);
                    });
                    
                }
            }
        });
    }
</script>
@endsection
