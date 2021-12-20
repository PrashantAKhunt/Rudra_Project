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
                        <form action="{{ route('admin.renewed_employee_insurance') }}" id="add_insurance_frm" method="post">
                            @csrf

                            <input type="hidden" id="id" name="id" value="{{ $exp_insurance_data[0]->id }}" />
                            <input type="hidden" id="company_id" name="company_id" value="{{ $exp_insurance_data[0]->company_id }}" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Employee</label>
                                        <select style="pointer-events: none; " class="form-control" name="employee_id" id="employee_id">
                                           
                                            @foreach($employee_list as $value)
                                            <option @if($value->id==$exp_insurance_data[0]->employee_id) selected @endif value="{{ $value->id }}"> {{ $value->name }} </option>
                                            @endforeach
                                        </select>


                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Insurance Company</label>
                                        <input type="text" class="form-control" name="company_name" id="company_name" value="{{ $exp_insurance_data[0]->company_name }}" readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Insurance Type </label>
                                       
                                        <select style="pointer-events: none; " class="form-control" name="type_id" id="type_id">
                                           
                                           @foreach($insurances_types as $value)
                                           <option @if($value->id==$exp_insurance_data[0]->type_id) selected @endif value="{{ $value->id }}">  {{ $value->title }}  </option>
                                           @endforeach
                                       </select>

                                    </div>
                                </div>
                            </div>
                         
                            
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Policy Number</label>
                                        <input type="text" class="form-control" name="policy_number" id="policy_number" value="{{ $exp_insurance_data[0]->policy_number	 }}" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Agent Name</label>
                                        <input type="text" class="form-control" name="agent_name" id="agent_name" value="{{ $exp_insurance_data[0]->agent_name }}" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Contact Number</label>
                                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="{{ $exp_insurance_data[0]->contact_number }}" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Contact Email</label>
                                        <input type="email" class="form-control" name="contact_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" id="contact_email" value="{{ $exp_insurance_data[0]->contact_email }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Amount </label>
                                        <input type="text" class="form-control" name="amount" id="amount" value="{{ $exp_insurance_data[0]->amount }}" />
                                    </div>
                                </div>
                            </div>


                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Effective Date</label>
                                        <input type="text" class="form-control" readonly="" name="insurance_date" id="insurance_date" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Expiration Date</label>
                                        <input type="text" class="form-control" readonly="" name="renew_date" id="renew_date" value="" />
                                    </div>
                                </div>
                            </div>
                            <h4 class="page-title">->  Reminder Dates</h4>
                            <div class="row" id="dynamic_div">
                            
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <label>Date 1</label>
                                        <input type="text" class="form-control reminder_date" required readonly="" name="reminder_date[0]" value="" />
                                    </div>
                                </div>

                            </div>
                            <button type="button" id="remove_btn" style="display: none;" title="Remove" class="btn btn-danger" onclick="remove_div();"><i class="fa fa-trash"></i></button>
                            <button type="button" id="add_btn"  title="Add" class="btn btn-primary" onclick="add_div();"><i class="fa fa-plus"></i> ADD </button>
                            <div class="clearfix"></div>
                            <br>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.expired_insurances_list') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
 var count = 0;

function add_div() {
     count++;
     no = count+1;
     let appHtml =  '<div class="col-md-3 div_count" id="child'+ count +'">'+
                        '<div class="form-group ">'+
                            '<label>Date '+no+'</label>'+
                            '<input type="text" class="form-control reminder_date" required readonly="" name="reminder_date['+count+']"  value="" />'+
                        '</div>'+
                    '</div>';
            $('#dynamic_div').append(appHtml);
            $('#remove_btn').show();   
            jQuery('.reminder_date').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: "dd-mm-yyyy"
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
            rules: {
                employee_id: {
                    required: true
                },
                company_name: {
                    required: true
                },
                policy_number: {
                    required: true
                },
                agent_name: {
                    required: true
                },
                contact_number: {
                    required: true
                },
                type_id: {
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
                }

            }
        })

    });
</script>
@endsection