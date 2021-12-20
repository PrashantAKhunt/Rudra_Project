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
                        <form action="{{ route('admin.insert_outward') }}" enctype="multipart/form-data" id="add_user_frm" method="post">
                            @csrf

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Outward Title<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="inward_outward_title" id="inward_title" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Select registry</label>
                                        <select class="form-control" name="registry" id="registry_list">
                                            <option value="">Select Registry</option>
                                            @foreach($registry_list as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['inward_outward_title']}}</option>
                                            @endforeach
                                        </select>


                                    </div>
                                </div>
                            </div>
                            
<!--                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Reference Outward Number </label>
                                        <input type="text" class="form-control" name="ref_outward_number" id="inward_title" value=""/>
                                    </div>
                                </div>
                            </div>-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label>Companies<span class="error">*</span></label>
                                        <select class="form-control" name="company_id" id="companies_list" required>
                                            <!-- <option value="">Select Category</option>  -->
                                            <option value="" disabled selected>Please Select</option>
                                            @foreach($companies as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['company_name']}}</option>
                                            @endforeach
                                        </select>


                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Projects<span class="error">*</span></label>
                                        <select class="form-control" name="project_id" id="projects_list" required>

                                        </select>


                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="details" hidden="true">
                                        <label>Other Project Details<span class="error">*</span></label>
                                        <textarea id="other_details" name="other_details" class="form-control" required></textarea>
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
                            </div> 

                            <div class="row">
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
                                <div class="col-md-6">
                                    <div class="form-group" id="departList">
                                        <label>Department Category<span class="error">*</span></label>
                                        <select class="select2 m-b-10 select2-multiple" name="department_id[]" multiple="multiple" id="department_list" required>

                                            @foreach($department_category as $Department)
                                            <option value="{{ $Department->id }}">{{ $Department->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="userList">
                                        <label>Assign Emoloyees<span class="error">*</span></label>
                                        <select class="select2 m-b-10 select2-multiple" name="inward_user_id[]" multiple="multiple" id="user_list" required>


                                        </select>
                                        <input type="hidden" name="inward_user_list" id="inward_user_list" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Description<span class="error">*</span></label>
                                        <textarea id="description" name="description" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Document file<span class="error">*</span></label>
                                        <div>
                                            <input type="file" name="document_file" accept="application/pdf,application/vnd.ms-excel"  class="form-control" id="document_file" required />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label> Date<span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly="" name="received_date" id="received_date" value="" />
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
                                        <input type="text" class="form-control" readonly="" name="expected_ans_date" id="expected_ans_date" value="{{date('d-m-Y')}}" />
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

    <input type="hidden" name="education_div_count" id="education_div_count" value="0" />
    <input type="hidden" name="experience_div_count" id="experience_div_count" value="0" />
</div>
@endsection


@section('script')


<script>
$('#registry_list').select2();

function answer_required_check(){
        if($('#ans_expected').val()=='Yes'){
            $('#ans_date_div').show();
        }
        else{
            $('#ans_date_div').hide();
        }
    }
    
    $('#user_list').click(function () {
  // 'data' brings the unordered list, while val does not
  var data = $('#user_list').select2('data');
  
  // Push each item into an array
  var finalResult = data.map(function (i) {
    return i.id;
  });

  // Display the result with a comma
  $('#inward_user_list').val(finalResult.join(','));
});
    
    $("#department_list").change(function () {

        department_list = $(this).val();

        if (department_list) {
            //alert(department_list);
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
                //processData: false,
                //contentType: false,
                success: function (data) {
                    //alert(data.id)
                    $("#user_list").html('');
                    $('#user_list').select2({
                        allowClear: true,
                        //minimumResultsForSearch: -1,
                        //width: 600,
                        //placeholder: 'past your placeholder'

                    });
                    $.each(data, function (index, user_list_obj) {
                        //alert(key);

                        $("#user_list").append('<option value="' + user_list_obj.id + '">' + user_list_obj.name + '</option>');
                    })

                }
            });

        } else {

            $("#user_list").empty();

        }

    });
</script>
<script>

    $("#companies_list").change(function () {

        companies_list = $(this).val();

        if (companies_list) {
            //alert(companies_list);
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
                //processData: false,
                //contentType: false,
                success: function (data) {
                    //alert(data.id)
                    //$("#user_list").html('');
                    $("#projects_list").empty();
                    $("#projects_list").append("<option value='' disabled selected>Please select</option>");
                    $.each(data, function (index, projects_obj) {
                        //alert(key);

                        $("#projects_list").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                    })

                }
            });

        } else {

            $("#projects_list").empty();

        }

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
    $("#registry_list").change(function () {
        selection = $(this).val();
        //return alert(selection);
        if (selection > 0) {
            $("#departList").hide()

            $("#userList").hide()

        } else {
            $("#departList").show()

            $("#userList").show()
        }
    });
</script>
<script>
    $("#projects_list").change(function () {
        let selection = $(this).val();
        //return alert(selection);
        if (selection == 1) {

            $("#details").show()


        } else {

            $("#details").hide()
            $("#other_details").val('')
        }
    });
</script>

<script>
    $(document).ready(function () {

        jQuery('#expected_ans_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        jQuery('#received_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        $("#doc_category_id").change(function () {
            var doc_category_id = $("#doc_category_id").val();

            $.ajax({
                url: "{{ route('admin.get_doc_sub_cat')}}",
                type: 'get',
                data: "doc_category_id=" + doc_category_id,
                success: function (data, textStatus, jQxhr) {
                    $('#doc_sub_category_id').empty();
                    $('#doc_sub_category_id').append(data);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

        $('#department_list').select2();

        $('#user_list ').select2();

        $('#add_user_frm').validate({
            rules: {
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
                received_date: {
                    required: true
                },
                expected_ans_date: {
                    required: true
                }

            }
        })

    });
</script>
@endsection