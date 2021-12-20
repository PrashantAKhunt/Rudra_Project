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
    <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <?php if(Auth::user()->role==config('constants.ACCOUNT_ROLE')) { ?>
                    <div class="panel-body">
                        <form action="{{ route('admin.tax_declaration') }}" id="user_form_16_frm" method="post" class="form-material" accept-charset="utf-8">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label class="col-sm-3 control-label" >Users</label>
                                            <div class="col-sm-6">
                                                @if(!empty($user))
                                                    <select name="user_id" id="user_id" class="select2 m-b-10 form-control" data-placeholder="Choose">
                                                        <option value="">-------Select User-------</option>
                                                        @foreach($user as $key => $value)
                                                            <?php if (!empty($user_id) && $user_id==$key) { ?>
                                                                <option selected value="{{$key}}">{{$value}}</option>
                                                            <?php } else { ?>
                                                                <option value="{{$key}}">{{$value}}</option>
                                                            <?php } ?>    
                                                        @endforeach
                                                    </select>                                   
                                                @endif
                                            </div>
                                        </div>
                                    </div>      

                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label class="col-sm-3 control-label" >Year</label>
                                            <div class="col-sm-6">
                                                <select name="year" id="year" class="form-control" data-placeholder="Choose">
                                                    <option value="">-------Select Year-------</option>
                                                    <?php
                                                        $dates = range('2018', date('Y'));
                                                        foreach($dates as $date){

                                                            if (date('m', strtotime($date)) <= 6) {//Upto June
                                                                $year = ($date-1) . '-' . $date;
                                                            } else {//After June
                                                                $year = $date . '-' . ($date + 1);
                                                            }
                                                            if($year==$selectedYear)
                                                            {
                                                                echo "<option value='$year' selected>$year</option>";
                                                            }
                                                            else
                                                            {
                                                                echo "<option value='$year'>$year</option>";    
                                                            }
                                                            
                                                        }
                                                        ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>    


                                </div>
                                <div class="form-actions">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="submit" class="btn btn-success" id="search_user">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div class="row">    
                    <form action="{{ route('admin.upload_user_form_16') }}" id="upload_user_form_16" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="form16_user_id" name="form16_user_id" value="<?php echo $user_id;?>" />
                    <input type="hidden" id="form16_year" name="form16_year" value="<?php echo $selectedYear;?>"/>                            
                    <div class="form-group pull-right">
                         <?php if(Auth::user()->role==config('constants.ACCOUNT_ROLE')) { ?>
                        <label>User Form 16</label>
                        <input type="file" accept="application/pdf" name="form_16" id="form_16" class="dropify"/>
                        <br>
                        <button type="submit" class="btn btn-success" id="upload">Upload</button>
                        <?php }?>
                        <?php if(!empty($form_data)) { ?>
                            <a href="{{ $form_data }}"  target="_blank" class="btn btn-primary pull-right"><i class="fa fa-download "></i> Download Form16</a>
                        <?php 
                            }
                        ?>
                    </div>

                    </form>
                </div>
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
                        <th>Username</th>
                        <th>Section Name</th>
                        <th>Deduction Name</th>
                        <th>Declaration</th>
                        <th>Proofs</th>
                         <th>Status</th>
                         <th>Action</th>
                        </thead>

                        <tbody>
                            <?php if (!empty($tax_data[0])) {
                                foreach ($tax_data as $key => $value) { ?>
                                    <tr>
                                        <td><?php echo $value->name; ?></td>
                                        <td><?php echo $value->section_name; ?></td>
                                        <td><?php echo $value->deduction_name; ?></td>
                                        <td>
                                        <?php 
                                           echo ($value->declaration!=null)?$value->declaration:'Not Declared';
                                        ?>        
                                        </td>
                                        <?php 
                                            $url = url('/storage/'.$value->proofs);
                                            $img = str_replace("public/","",$url);
                                        ?>
                                        <?php 
                                        if(!empty($value->proofs)){
                                        ?>
                                        <td><img height='100px' width='100px' src="<?php echo $img;?>"></td>
                                        <?php 
                                            }else{
                                        ?>
                                        <td>No proof</td>
                                        <?php }?>
                                        <td>
                                            <?php echo ($value->status=='Pending')?$value->status:'Not Submitted'; ?>
                                        </td>
                                        <?php
                                            $btn = '';
                                            if(!empty($role)) {
                                                if(in_array('2', $role)) {
                                                    $btn = '<a onclick="getTable('.$value->id.')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#myModal" title="Edit"><i class="fa fa-edit"></i></a>';
                                                }
                                                if(in_array('4', $role)) {
                                                    //$btn .= '<a href="'.url("edit_tax_declaration/$value->id").'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                                    $btn ='<a onclick="getTable('.$value->id.')" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#myModal" title="Edit"><i class="fa fa-edit"></i></a>';
                                                }
                                            }
                                        ?>
                                        <td><?php echo $btn; ?></td>
                                    </tr>
                                <?php }
                                    }
                                ?>
                        </tbody>
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


                        <form action="{{ route('admin.update') }}" id="upload_form_16" method="post" enctype="multipart/form-data">
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
    jQuery('#user_form_16_frm').validate({
        ignore: [],
        rules: {
            user_id: {
                required: true,
            },
            year: {
                required: true,
            }
        }
    });

    // $('.select2').select2();
    // $('.select22').select2();
    $('#upload').prop('disabled', true);
    //$('#search_user').prop('disabled', true);

    var access_rule = '<?php echo $access_rule; ?>';
    access_rule = access_rule.split(',');

    $('#emp_table').DataTable( {
        "stateSave": true,
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'csvHtml5',
        ]
    } );

    $(document).ready(function() {
        $("#upload").click(function(){
            var user_id = $("#user_id").val();
            if(user_id.length==0)
            {
                swal({
                        title: "Please select User first !",
                        //text: "You want to change status of admin user.",
                        type: "error",
                        showCancelButton: false,
                    })

                return false;
            }

            var year    = $("#year").val();
            if(year.length==0)
            {
                swal({
                        title: "Please select Year first !",
                        //text: "You want to change status of admin user.",
                        type: "error",
                        showCancelButton: false,
                    })

                return false;
            }
            $('#form16_year').val(year);
            $('#form16_user_id').val(user_id);
            $('#upload_user_form_16').submit();
        });
    });    

    $('#upload_user_form_16').submit(function() {
        var user_id = $("#user_id").val();
        var year    = $("#year").val();
        $('#form16_year').val(year);
        $('#form16_user_id').val(user_id);
    });

    $('#form_16').on('change', function(){ 
        $('#upload').prop('disabled', false);
        var year    = $("#year").val();
        $('#form16_year').val(year);

        var user_id    = $("#user_id").val();
        $('#form16_user_id').val(user_id);
    });

    $('#year').on('change', function(){ 
        var year    = $("#year").val();
        $('#form16_year').val(year);
    });

    $('#user_id').on('change', function(){ 
        var user_id    = $("#user_id").val();
        $('#form16_user_id').val(user_id);
    });

    var table = $('#emp_table1').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
		"stateSave": true,
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
                           var url=  "<?php echo url('/storage/');?>"+"/"+row.proofs;
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
            data: {tax_id: id},
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
