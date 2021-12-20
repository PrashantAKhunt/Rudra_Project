@extends('layouts.admin_app')

@section('content')
<?php use Illuminate\Support\Facades\Auth; ?>
<style>
hr {
  border-top: 1px solid ;
}

</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.inward_outward') }}">{{ $module_title }}</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">

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
                        <form action="{{ route('admin.update_outward') }}" enctype="multipart/form-data" id="add_outward_frm" method="post">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                <input type="hidden" name="id" value="{{ $outwards_data[0]->id }}">
                                    <div class="form-group ">
                                        <label>Outward Title<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="inward_outward_title" id="inward_title" value="{{ $outwards_data[0]->inward_outward_title }}" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Select registry</label>
                                        <select class="form-control" name="registry" id="registry_list">
                                            <option value="">Select Registry</option>
                                            @foreach($registry_list as $key => $value)
                                            <option value="{{$value['id']}}"
                                            
    
                                             <?php echo ($outwards_data[0]->parent_inward_outward_no == $value['id']) ? "selected='selected'" : '' ?>
                                    
                                             >{{$value['inward_outward_title']}} ( {{$value['inward_outward_no']}} )</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4 class="page-title">->  Reciept of Documents</h4>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mode of Delivery<span class="error">*</span></label>
                                        <select class="form-control" name="delivery_mode" id="delivery_mode" required>
                                        <option value="" disabled selected>Please Select</option>
                                            @foreach($delivery_mode_list as $mode)
                                            <option value="{{ $mode->id }}" <?php echo ($outwards_data[0]->inward_outward_delivery_mode_id == $mode->id ) ? "selected='selected'" : '' ?>>{{ $mode->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cover upload -> Uploaded Image
                                        <a title="Download Image" href="{{ asset('storage/'.str_replace('public/','',!empty($outwards_data[0]->delivery_file) ? $outwards_data[0]->delivery_file : 'public/no_image')) }}" download><i class="fa fa-cloud-download fa-lg"></i></a></a>
                                        </label>
                                        <div>
                                            <input type="file" name="delivery_file" class="form-control" id="delivery_file" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4 class="page-title">->  Particular Detail of Documents</h4>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Document Category<span class="error">*</span></label>
                                        <select class="form-control" name="doc_category_id" id="doc_category_id" required>
                                            <option value="" disabled selected>Please Select</option>
                                            @foreach($doc_category_list as $key => $value)
                                            <option value="{{$value['id']}}" <?php echo ($outwards_data[0]->doc_category_id == $value['id'] ) ? "selected='selected'" : '' ?>>{{$value['category_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Document Sub Category<span class="error">*</span></label>
                                        <select class="form-control" name="doc_sub_category_id" id="doc_sub_category_id" required>
                                            <option value="" disabled selected>Please Select</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group ">
                                        <label>Description<span class="error">*</span></label>
                                        <textarea id="description" name="description" row="3" class="form-control" required>{{ $outwards_data[0]->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4 class="page-title">->  Record Keeping of Document in System</h4>
                            <br>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Companies<span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly name="company_id" id="company_id" value="{{ $company_name }}" />

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <label>Projects<span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly name="project_id" id="projects_list" value="{{ $project_name }}" />

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" id="details" style="display: none;">
                                    <div class="form-group">
                                        <label>Other Project Details<span class="error">*</span></label>
                                        <textarea id="other_details" name="other_details" class="form-control">{{ $outwards_data[0]->other_project_details }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Detail Entered by/Requested By<span class="error">*</span></label>
                                        <select class="form-control" name="requested_by" id="requested_by" required>
                                            
                                        
                                            @foreach($users_list as $key => $value)
                                            <option value="{{$value['id']}}" <?php echo ($outwards_data[0]->requested_by  == $value['id']) ? "selected='selected'" : '' ?>>{{$value['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>  
                            </div>

                            <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Document Outward No.</label>
                                        <input type="text" class="form-control" readonly  id="outward_no" value="{{ $outwards_data[0]->inward_outward_no }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                    <div class="errorTxt">
                                            <span id="errNm1"></span>
                                        </div>
                                        <div class="checkbox checkbox-success" style="pointer-events: none;">
                                            <input id="checkbox34"  name="scan_copy_attach" class="scan_copy_attach" type="checkbox" value="Yes" checked>
                                            <label for="checkbox34">Scan Copy Check & Attached!</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="document_attach" >
                                <div class="col-md-6">
                                <div class="col-sm-12">
                                        <h4>Uploaded Document
                                        <a title="Download File" href="{{ asset('storage/'.str_replace('public/','',!empty($outwards_data[0]->document_file) ? $outwards_data[0]->outwards_data : 'public/no_image')) }}" download><i class="fa fa-cloud-download fa-lg"></i></a></a>
                                        </h4>  
                                        <br>
                                    </div>
                                    <div class="form-group">
                                        <label>Document file<span class="error">*</span></label>
                                        <div>
                                            <input type="file" name="document_file" accept="application/pdf,application/vnd.ms-excel" class="form-control" id="document_file" />
                                        </div>
                                    </div>
                                </div>
                            </div>       
                            <hr>
                            <h4 class="page-title">->  Document Allotment</h4>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <p id="old_registry_user_list"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="departList">
                                        <label>Department Category<span class="error">*</span></label>
                                        <select class="select2 form-control" name="department_id" id="department_list" required>
                                            <option value="" disabled selected>Please Select</option>
                                            @foreach($department_category as $Department)
                                            <option value="{{ $Department->id }}" <?php echo ($outwards_data[0]->department_id == $Department->id) ? "selected='selected'" : '' ?>>{{ $Department->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="userList">
                                        <label>Assign Employees<span class="error">*</span></label>
                                        <select class="select2 form-control" name="inward_user_id" id="user_list" required>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-success">
                                            <input id="checkbox33" name="is_important" type="checkbox" value="Pending"  @if($outwards_data[0]->doc_mark == 'Pending') checked @endif>
                                            <label for="checkbox33">Mark AS Important!</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                       

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Answer Expected?<span class="error">*</span></label>
                                        <select onchange="answer_required_check();" class="form-control" name="ans_expected" id="ans_expected">
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="ans_date_div">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Expected Date<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="expected_ans_date" id="expected_ans_date" value="{{date('d-m-Y h:i a')}}" />
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.outwards') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

<script>
   
    function attach() {
        var checkedValue = $('.scan_copy_attach:checked').val();
        if (checkedValue) {
            $('#document_attach').show();
        } else {
            $('#document_attach').hide();
        }
    }

    function answer_required_check() {
        if ($('#ans_expected').val() == 'Yes') {
            $('#ans_date_div').show();
        } else {
            $('#ans_date_div').hide();
        }
    }

</script>
<script>
    //===============================================================
    $("#department_list").change(function () {

        department_list = $(this).val();

        if (department_list) {
         
            $.ajax({
                url: "{{ route('admin.depart_user_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: department_list  //'registry_id':$('#registry_list').val()
                },
                dataType: "JSON",
                success: function (data) {
                    $("#user_list").html('');
                    $('#user_list').select2({
                        allowClear: true,
                    });
                    $.each(data, function (index, user_list_obj) {
                        $("#user_list").append('<option value="' + user_list_obj.id + '">' + user_list_obj.name + '</option>');
                    })

                }
            });

        } else {
            $("#user_list").empty();

        }

    });
    //=======================================================
    $("#registry_list").change(function () {
        
        selection = $(this).val();
        if (selection > 0) {
            $.ajax({
                url: "{{ route('admin.get_registry_old_user_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    registry_id: selection
                },
                dataType: "JSON",
                success: function (data) {
                    $('#old_registry_user_list').html('<b>Already added users: </b>'+data.registry_user_list);

                }
            });

        } else {

            $('#isreply').val('0');
            $('#old_registry_user_list').html("");
            
        }
        
    });
    //============================================================
    $("#doc_category_id").change(function () {
            var doc_category_id = $("#doc_category_id").val();

            $.ajax({
                url: "{{ route('admin.get_doc_sub_cat')}}",
                type: 'get',
                data: "doc_category_id=" + doc_category_id,
                success: function (data, textStatus, jQxhr) {

                    $("#doc_sub_category_id").html('');
                    $('#doc_sub_category_id').select2({
                            allowClear: true,         
                    });
                    $('#doc_sub_category_id').append(data);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });
    //================================================================    
   
</script>




<script>
    jQuery('.mydatepicker, #datepicker').datepicker();
    jQuery('#datepicker-autoclose').datepicker({
        autoclose: true,
        todayHighlight: true
    });

    jQuery('#date-range').datepicker({
        toggleActive: true
    });
    jQuery('#datepicker-inline').datepicker({
        todayHighlight: true
    });
</script>

<script>
    $(document).ready(function () {

        var user_id = "<?php echo $outwards_data[0]->assign_employee_id; ?>";
            var department_list = "<?php echo $outwards_data[0]->department_id; ?>";
                $.ajax({
                    url: "{{ route('admin.depart_user_list') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: department_list
                    },
                    dataType: "JSON",
                    success: function(data) {
                        
                        $("#user_list").html('');
                        $('#user_list').select2({
                            allowClear: true, 
                        });
                        $.each(data, function(index, user_list_obj) { 
                            $("#user_list").append('<option value="' + user_list_obj.id + '">' + user_list_obj.name + '</option>');
                        });
                        $('#user_list').val(user_id).trigger('change'); 

                    }
                });
                //=============================================
                var doc_category_id = "<?php echo $outwards_data[0]->doc_category_id; ?>";
                var doc_sub_category_id = "<?php echo $outwards_data[0]->doc_sub_category_id; ?>";
                    $.ajax({
                        url: "{{ route('admin.get_doc_sub_cat')}}",
                        type: 'get',
                        data: "doc_category_id=" + doc_category_id,
                        success: function(data, textStatus, jQxhr) {
                            $("#doc_sub_category_id").html('');
                            $('#doc_sub_category_id').select2({
                                    allowClear: true,         
                            });
                            $('#doc_sub_category_id').append(data);
                            $("#doc_sub_category_id").val(doc_sub_category_id).trigger('change.select2');
                        },
                        error: function(jqXhr, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                    //==================================================
                    var project_id = "<?php echo $outwards_data[0]->project_id; ?>";
                    if (project_id == 1) {
                        $("#details").show();
                    } else {
                        $("#details").hide();
                        $("#other_details").val('');
                    }
                    //==================================================
                    var is_answered = "<?php echo $outwards_data[0]->is_answered; ?>";
                    if (is_answered == 'Not Required') {
                        $('#ans_date_div').hide();
                        $('#ans_expected').val('No');
                    } else {
                        $('#ans_date_div').show();
                    } 

        $('#registry_list').select2(); 
        $('#user_list').select2();
        $('#delivery_mode').select2();
        $('#requested_by').select2();
        $('#department_list').select2();
        $('#user_list ').select2();
        $('#doc_category_id').select2();
        $('#doc_sub_category_id').select2();

        $('#add_outward_frm').validate({
            ignore: [],
            rules: {
                scan_copy_attach: {
                    required: true
                },
                inward_title: {
                    required: true
                },
                description: {
                    required: true
                },
                doc_category_id: {
                    required: true
                },
                doc_sub_category_id: {
                    required: true
                },
                received_date: {
                    required: true
                },
                expected_ans_date: {
                    required: true
                },
                requested_by: {
                    required: true
                }
            },
            messages: {
                scan_copy_attach: {
                    required: "Please select scan copy!"
                },
                inward_title: {
                    required: "This field is required!"
                },
                description: {
                    required: "This field is required!"
                },
                doc_category_id: {
                    required: "This field is required!"
                },
                doc_sub_category_id: {
                    required: "This field is required!"
                },
                expected_ans_date: {
                    required: "This field is required!"
                },
                delivery_mode: {
                    required: "This field is required!"
                },
                requested_by: {
                    required: "This field is required!"
                }
            },
            errorPlacement: function(label, element) {
                label.addClass('errorMsq');
                if (element.attr("name") == "scan_copy_attach" ) {
                    $("#errNm1").append(label);
                } else {
                    element.parent().append(label);
                }
            }
        });

        $('#expected_ans_date').datetimepicker({
            format: 'DD-MM-YYYY h:mm a'
        });
        // jQuery('#expected_ans_date').datepicker({
        //     autoclose: true,
        //     todayHighlight: true,
        //     format: "dd-mm-yyyy"
        // });

        jQuery('#received_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

    });
</script>
@endsection