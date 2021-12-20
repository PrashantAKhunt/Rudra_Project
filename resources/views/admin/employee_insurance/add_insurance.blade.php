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
                <li><a href="{{ route('admin.employees_insurances') }}">{{ $module_title }}</a></li>
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
                        <form action="{{ route('admin.insert_employee_insurance') }}" enctype="multipart/form-data" id="add_insurance_frm" method="post">
                            @csrf
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Employee <span class="error">*</span></label>
                                        <select class="form-control" name="employee_id" id="employee_id" required>
                                            <option value="">--- Select Employee ---</option>

                                            @foreach($employee_list as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Company <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="emp_company" id="emp_company" value="" readonly />
                                        <input type="hidden" name="company_id" id="company_id" value="" readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Insurance Type <span class="error">*</span></label>
                                        <select class="form-control" name="type_id" id="type_id" required>
                                            <option value="">--- Select Type ---</option>

                                        </select>


                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Insurance Company Name <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="company_name" id="company_name" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Policy Number <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="policy_number" id="policy_number" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Agent Name <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="agent_name" id="agent_name" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Contact Number <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Contact Email</label>
                                        <input type="email" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" name="contact_email" id="contact_email" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Amount <span class="error">*</span></label>
                                        <input type="text" class="form-control" name="amount" id="amount" value="" />
                                    </div>
                                </div>
                            </div>


                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Effective Date <span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly="" name="insurance_date" id="insurance_date" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Expiration Date <span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly="" name="renew_date" id="renew_date" value="" />
                                    </div>
                                </div>
                            </div>
                            <!-- Last Employee_insurance_reminder_dates Logic 04062021  -->
                            <!-- <h4 class="page-title">-> Reminder Dates</h4>
                            <div class="row" id="dynamic_div">

                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <label>Date 1</label>
                                        <input type="text" class="form-control reminder_date" required name="reminder_date[0]" value="" />
                                    </div>
                                </div>

                            </div>
                            <button type="button" id="remove_btn" style="display: none;" title="Remove" class="btn btn-danger" onclick="remove_div();"><i class="fa fa-trash"></i></button>
                            <button type="button" id="add_btn" title="Add" class="btn btn-primary" onclick="add_div();"><i class="fa fa-plus"></i> ADD </button> -->
                            <!-- comment as asked by to in bug 04-06-2021 -->

                            <div class="row">
                                <div class="col-md-6"> 
                                    <div class="form-group "> 
                                        <label> Responsible Person <span class="error">*</span></label>
                                        <select class="form-control" name="responsible_person_id" id="responsible_person_id">
                                            <option value="">Select</option>
                                            @foreach($users_data as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group "> 
                                    <label> Payment Responsible Person <span class="error">*</span></label>
                                    <select class="form-control" name="payment_responsible_person_id" id="payment_responsible_person_id">
                                        <option value="">Select</option>
                                        @foreach($users_data as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                        
                                    </select>
                                </div> 
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                            <div class="form-group "> 
                                <label> Checker (Responsible) <span class="error">*</span></label>
                                <select class="form-control" name="checker_id" id="checker_id">
                                    <option value="">Select</option>
                                    @foreach($users_data as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div> 
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group "> 
                                    <label> Super Admin Verification </label>
                                    <select class="form-control" name="super_admin_checker_id" id="super_admin_checker_id">
                                        <option value="">Select</option>
                                        @foreach($users_data as $user)
                                        <option value="{{ $user->id }}" <?php echo (config('constants.SuperUser') == $user->id) ? "selected='selected'" : '' ?> >{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                            </div>
                            <h4>-> Reminder Schedule</h4>
                            <br>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group "> 
                                    <label> Before Day</label>
                                    <select class="form-control" name="first_day_interval" id="first_day_interval">
                                        <option value="">Select Day</option>
                                        @foreach($days as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group "> 
                                    <label> Before Day</label>
                                    <select class="form-control" name="second_day_interval" id="second_day_interval">
                                        <option value="">Select Day</option>
                                        @foreach($days as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group "> 
                                    <label> Before Day</label>
                                    <select class="form-control" name="third_day_interval" id="third_day_interval">
                                        <option value="">Select Day</option>
                                        @foreach($days as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-groups">
                                        <label>Upload Poilcy</label>
                                        <input type="file" class="form-control" multiple name="uploadpolicy[]" id="uploadpolicy">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.employees_insurances') }}'" class="btn btn-default">Cancel</button>
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
    $(document).ready(function() {

        $('#employee_id').select2();
        $('#type_id').select2();
        $('.reminder_date').datetimepicker({
            format: 'YYYY-MM-DD hh:mm:ss'
        });

    });


    var count = 0;

    function add_div() {
        count++;
        no = count + 1;
        let appHtml = '<div class="col-md-3 div_count" id="child' + count + '">' +
            '<div class="form-group ">' +
            '<label>Date ' + no + '</label>' +
            '<input type="text" class="form-control reminder_date" required name="reminder_date[' + count + ']"  value="" />' +
            '</div>' +
            '</div>';
        $('#dynamic_div').append(appHtml);
        $('#remove_btn').show();
        $('.reminder_date').datetimepicker({
            format: 'YYYY-MM-DD hh:mm:ss'
        });
    }

    function remove_div() {
        let div_counts = $(".div_count").length;

        $('#child' + div_counts).remove();
        if (div_counts == 1) {
            $('#remove_btn').hide();
        }
        count--;

    }
</script>
<script>
    $("#employee_id").change(function() {

        var employee_id = $(this).val();


        if (employee_id) {
            $("#emp_company").val('');
            $("#company_id").val('');
            $.ajax({

                url: "{{ route('admin.employee_company') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: employee_id
                },
                dataType: "JSON",
                success: function(data) {

                    $("#emp_company").val(data[0].company_name);
                    $("#company_id").val(data[0].id);

                }
            });

            $.ajax({

                url: "{{ route('admin.employee_insurances_types') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    employee_id: employee_id
                },
                dataType: "JSON",
                success: function(data) {

                    $('#type_id').empty();
                    $('#type_id').append("<option value='' selected>--- Select Type ---</option>");

                    $.each(data, function(index, object) {
                        $('#type_id').append('<option value="' + object.id + '">' + object.title + '</option>');
                    });

                }
            });

        } else {

            $("#emp_company").val('');
            $("#company_id").val('');

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
    $(document).ready(function() {

        jQuery('#renew_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        jQuery('#insurance_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        $('#add_insurance_frm').validate({
            ignore: [],
            rules: {
                employee_id: {
                    required: true
                },
                emp_company: {
                    required: true
                },
                type_id: {
                    required: true
                },
                agent_name: {
                    required: true
                },
                company_name: {
                    required: true
                },
                contact_number: {
                    required: true
                },
                policy_number: {
                    required: true
                },
                amount: {
                    required: true
                },
                insurance_date: {
                    required: true
                },
                renew_date: {
                    required: true
                },
                responsible_person_id: {
                    required: true
                },
                payment_responsible_person_id: {
                    required: true
                },
                checker_id: {
                    required: true
                },
            }
        })

    });
</script>
@endsection