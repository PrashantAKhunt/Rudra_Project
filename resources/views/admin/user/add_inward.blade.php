@extends('layouts.admin_app')

@section('content')
<?php use Illuminate\Support\Facades\Auth; ?>
<style>
hr {
  border-top: 1px solid ;
}
.errorMsq {
  color: red;
  display: block;
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
                        <form action="{{ route('admin.insert_inward') }}" enctype="multipart/form-data" id="add_inward_frm" method="post">
                            @csrf

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Inward Title<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="inward_outward_title" id="inward_title" value="" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Select registry</label>
                                        <select class="form-control" name="registry" id="registry_list">
                                            <option value="">Select Registry</option>
                                            @foreach($registry_list as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['inward_outward_title'] }} ( {{ $value['inward_outward_no'] }} )</option>
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
                                    <div class="form-group ">
                                        <label>Received Date<span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly="" id="received_date" value="" />
                                    </div>
                                </div>
                            </div>

                    
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mode of Delivery<span class="error">*</span></label>
                                        <select class="form-control" name="delivery_mode" id="delivery_mode" required>
                                        <option value="" disabled selected>Please Select</option>
                                            @foreach($delivery_mode_list as $mode)
                                            <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cover upload</label>
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Type Of Sender<span class="error">*</span></label>
                                        <select class="form-control" name="sender_id" id="sender_id"  required>
                                        <option value="" selected>Please Select</option>
                                           
                                        @foreach($sender_list as $list)
                                            <option value="{{ $list->id }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <label>Name of Sender <span class="error">*</span></label>
                                        <input type="text" class="form-control" required name="sender_name" id="sender_name" value="" />
                                        <div id="sender_list" style="position:absolute;">
                                        
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <label>Sender Letter/Invoice Date <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="sender_invoice_date" id="sender_invoice_date" value="" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <label>Sender Letter/Invoice No</label>
                                        <input type="text" class="form-control" onchange="comment_box();" name="ref_outward_number" id="ref_outward_number" value="" />
                                    </div>
                                </div>
                            </div>

                        
                             <div class="row">
                                
                                <div class="col-md-12">
                                    <div class="form-group" id="sender_comment">
                                        
                                        <label>Comment Box: In Case of Nil Input For Invoice No</label>
                                        <textarea name="sender_comment" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Document Category<span class="error">*</span></label>
                                        <select class="form-control" name="doc_category_id" id="doc_category_id" required>
                                            <option value="" disabled selected>Please Select</option>
                                            @foreach($doc_category_list as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['category_name']}}</option>
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
                                        <textarea id="description" name="description" row="3" class="form-control" required></textarea>
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
                                        <select class="form-control" name="company_id" id="companies_list" required>
                                            
                                            <option value="" disabled selected>Please Select</option>
                                            @foreach($companies as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['company_name']}}</option>
                                            @endforeach
                                        </select>


                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">

                                        <label>Projects<span class="error">*</span></label>
                                        <select class="form-control" name="project_id" id="projects_list" required>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" style="display: none;" id="details">
                                    <div class="form-group">
                                        <label>Other Project Details<span class="error">*</span></label>
                                        <textarea id="other_details" name="other_details" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Detail Entered by/Requested By<span class="error">*</span></label>
                                        <select class="form-control" name="requested_by" id="requested_by" required>
                                            
                                        
                                            @foreach($users_list as $key => $value)
                                            <option value="{{$value['id']}}" <?php echo (Auth::user()->id == $value['id']) ? "selected='selected'" : '' ?>>{{$value['name']}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>  
                                
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Document Inward No.</label>
                                        <input type="text" class="form-control" readonly  id="inward_no" value="" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="errorTxt">
                                            <span id="errNm2"></span>
                                        </div>
                                        <div class="checkbox checkbox-success">
                                            <input id="checkbox34" onclick="attach();" name="scan_copy_attach" class="scan_copy_attach" type="checkbox" value="Yes">
                                            <label for="checkbox34">Scan Copy Check & Attached!</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="document_attach" style="display:none">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Document file<span class="error">*</span></label>
                                        <div>
                                            <input type="file" name="document_file" accept="application/pdf,application/vnd.ms-excel" class="form-control" id="document_file" required />
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
                                            <option value="{{ $Department->id }}">{{ $Department->dept_name }}</option>
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


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="doc_delivery">
                                        <label>Mode of Documents Delivered<span class="error">*</span></label>
                                        <div class="errorTxt">
                                            <span id="errNm1"></span>
                                        </div>
                                           <label class="radio-inline p-0">
                                                <div class="checkbox checkbox-purple checkbox-circle">
                                                <input id="checkbox-13" type="checkbox" name="doc_delivery_mode[]" value="Hard Copy" checked>
                                                <label for="checkbox-13"> Hard Copy</label>
                                                </div>
                                            </label>

                                            <label class="radio-inline">
                                                <div class="checkbox checkbox-purple checkbox-circle">
                                                <input id="checkbox-14" type="checkbox" name="doc_delivery_mode[]" value="Soft Copy" >
                                                <label for="checkbox-14"> Soft Copy </label>
                                                </div>
                                            </label>
                                    </div>
                                </div>
                            </div>
                           <hr> 

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-success">
                                            <input id="checkbox33" name="is_important" type="checkbox" value="Pending">
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
                                        <label>Expected Answer Date<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="expected_ans_date" id="expected_ans_date" value="{{date('d-m-Y h:i a')}}" />
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.inwards') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="education_div_count" id="education_div_count" value="0" />
    <input type="hidden" name="experience_div_count" id="experience_div_count" value="0" />
    <input type="hidden" name="isreply" id="isreply" value="0" />
</div>
@endsection


@section('script')
<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<script>
$(document).ready(function(){
            
            $("#sender_name").keyup(function() {

                sender_name = $(this).val();
                if (sender_name != '') {
                
                    $.ajax({
                        url: "{{ route('admin.search_sender_name') }}",
                        type: "Get",
                        data: {  sender_name: sender_name },
                        dataType: "JSON",
                        success: function(data) {
                            $('#sender_list').fadeIn();  
                            $('#sender_list').html(data);
                        }
                    });
                } 
            });

            $(document).on('click', 'li', function(){  
                $('#sender_name').val($(this).text());  
                $('#sender_list').fadeOut();  
            }); 

});

    function attach() {
        var checkedValue = $('.scan_copy_attach:checked').val();
        if (checkedValue) {
            $('#document_attach').show();
        } else {
            $('#document_attach').hide();
        }
    }
    function comment_box() {
        var checkedVal = $('#ref_outward_number').val();
        if (checkedVal) {
            $('#sender_comment').hide();
        } else {
            $('#sender_comment').show();
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
   
    $("#companies_list").change(function() {

        companies_list = $(this).val();

        if (companies_list) {
         
            $.ajax({
                url: "{{ route('admin.companies_project') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    company_id: companies_list
                },
                dataType: "JSON",
                success: function(data) {
                   
                    $("#projects_list").empty();
                    $("#projects_list").append("<option value='' disabled selected>Please select</option>");
                    $.each(data, function(index, projects_obj) {
                        if(projects_obj.project_name != "Other Project")
                            $("#projects_list").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                    })

                }
            });

            //=======================================  INWARD NO =======

            $.ajax({

                url: "{{ route('admin.inward_no') }}",
                type: "get",
                data: {
                    company_id: companies_list
                },
                dataType: "JSON",
                success: function(data) {
                    $("#inward_no").val(data);
                }
            });

        } else {

            $("#projects_list").empty();

        }

    });
     $("#department_list").change(function() {

        department_list = $(this).val();

        if (department_list) {
            //alert(department_list);
            // if ($('#isreply').val() == '1') {
            //     //user has selected parent registry so do not have to show old users already added
            //     $.ajax({

            //         url: "{{ route('admin.department_user_with_registry') }}",
            //         type: "POST",
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         data: {
            //             id: department_list,
            //             'registry_id': $('#registry_list').val()
            //         },
            //         dataType: "JSON",
            //         success: function(data) {
            //             $("#user_list").html('');
            //             $('#user_list').select2({
            //                 allowClear: true,         
            //             });
            //             $.each(data, function(index, user_list_obj) {
            //                 $("#user_list").append('<option value="' + user_list_obj.id + '">' + user_list_obj.name + '</option>');
            //             });

            //         }
            //     });
            // } else {
                //user has not selected parent registry so do have to show all users
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

                    }
                });
            //}

        } else {
            $("#user_list").empty();
        }

    });
    $("#projects_list").change(function() {
        let selection = $(this).val();
        if (selection == 1) {
            $("#details").show()
            document.getElementById("other_details").required = true;
        } else {
            document.getElementById("other_details").required = false;
            $("#details").hide()
            $("#other_details").val('')
        }
    });
    $("#registry_list").change(function() {
        selection = $(this).val();
            // $('#department_list').val(null).trigger('change');
            // $('#user_list').val(null).trigger('change');
        if (selection > 0) {
            $('#old_registry_user_list').html("");
            $('#isreply').val('1');
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
                success: function(data) {
                    $('#old_registry_user_list').html('<b>Already added users: </b>' + data.registry_user_list);
                }
            })

        } else {
            $('#isreply').val('0');
            $('#old_registry_user_list').html("");
        }
    });
    $("#doc_category_id").change(function() {
            var doc_category_id = $("#doc_category_id").val();
            $.ajax({
                url: "{{ route('admin.get_doc_sub_cat')}}",
                type: 'get',
                data: "doc_category_id=" + doc_category_id,
                success: function(data, textStatus, jQxhr) {
                    //$('#doc_sub_category_id').empty();
                    $("#doc_sub_category_id").html('');
                    $('#doc_sub_category_id').select2({
                            allowClear: true,         
                    });
                    $('#doc_sub_category_id').append(data);
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });
</script>

<script>
    // Date Picker
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
    $(document).ready(function() {
        $('#registry_list').select2();
        $('#department_list').select2();
        $('#user_list').select2();
        $('#delivery_mode').select2();
        $('#sender_id').select2();
        $('#requested_by').select2();
        $('#doc_category_id').select2();
        $('#doc_sub_category_id').select2();
        
        $('#expected_ans_date').datetimepicker({
            format: 'DD-MM-YYYY h:mm a'
        });

        jQuery('#sender_invoice_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

            var interval = setInterval(function() {
            var momentNow = moment();
                $('#received_date').val(momentNow.format('DD-MM-YYYY hh:mm:ss'));
            }, 100);

            $('#add_inward_frm').validate({
            ignore: [],
            rules: {
                scan_copy_attach: {
                    required: true
                },
                'doc_delivery_mode[]': {
                    required: true,
                    minlength: 1
                },
                inward_title: {
                    required: true
                },
                description: {
                    required: true
                },
                document_file: {
                    required: true
                },
                doc_category_id: {
                    required: true
                },
                doc_sub_category_id: {
                    required: true
                },
                companies_list: {
                    required: true
                },
                projects_list: {
                    required: true
                },
                expected_ans_date: {
                    required: true
                },
                delivery_mode: {
                    required: true
                },
                sender_id: {
                    required: true
                },
                sender_name: {
                    required: true
                },
                sender_invoice_date: {
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
                'doc_delivery_mode[]': {
                    required: "You must check at least 1 box"
                },
                inward_title: {
                    required: "This field is required!"
                },
                description: {
                    required: "This field is required!"
                },
                document_file: {
                    required: "This field is required!"
                },
                doc_category_id: {
                    required: "This field is required!"
                },
                doc_sub_category_id: {
                    required: "This field is required!"
                },
                companies_list: {
                    required: "This field is required!"
                },
                projects_list: {
                    required: "This field is required!"
                },
                expected_ans_date: {
                    required: "This field is required!"
                },
                delivery_mode: {
                    required: "This field is required!"
                },
                sender_id: {
                    required: "This field is required!"
                },
                sender_name: {
                    required: "This field is required!"
                },
                sender_invoice_date: {
                    required: "This field is required!"
                },
                requested_by: {
                    required: "This field is required!"
                }
            },
            errorPlacement: function(label, element) {
                label.addClass('errorMsg');
                if (element.attr("name") == "doc_delivery_mode[]" ) {
                    $("#errNm1").append(label);
                } else if (element.attr("name") == "scan_copy_attach" ) {
                    $("#errNm2").append(label);
                } else {
                    element.parent().append(label);
                }
            }
        });

    });

//========================================================================================================
   
        // $('#user_list').click(function() {
    //     // 'data' brings the unordered list, while val does not
    //     var data = $('#user_list').select2('data');

    //     // Push each item into an array
    //     var finalResult = data.map(function(i) {
    //         return i.id;
    //     });

    //     // Display the result with a comma
    //     $('#inward_user_list').val(finalResult.join(','));
    // });
</script>
@endsection