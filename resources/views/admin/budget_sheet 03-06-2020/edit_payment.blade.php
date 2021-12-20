@extends('layouts.admin_app')

@section('content')
<?php

use App\Lib\CommonTask;

$common_task = new CommonTask();
?>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Add Budget Sheet</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li>
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
                    <div class="col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.update_budget_sheet') }}" id="update_budget_sheet" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{ $budget_sheet_detail[0]->id }}" />
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Budget Sheet Year</label>
                                        <input type="text" readonly="" class="form-control" value="{{ $budget_sheet_detail[0]->budget_sheet_year }}" name="budget_sheet_year" id="budget_sheet_year"/>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Budget Sheet Week</label>
                                        <select class="form-control" name="budget_sheet_week" id="budget_sheet_week">
                                            @for($i=$budget_sheet_detail[0]->budget_sheet_week;$i<=52;$i++)
                                            <option @if($budget_sheet_detail[0]->budget_sheet_week==$i) selected="" @endif value="{{ $i }}">{{ 'Week- '.$i.' ('.$common_task->getWeekStartAndEndDate($i,date("Y")).')' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Select Company</label>
                                        <select name="company_id" onchange="get_client_list(); get_vendor_list();" required="" id="company_id" class="form-control">
                                            <option value="">Select Company</option>
                                            @foreach($company_list as $company)
                                            <option @if($budget_sheet_detail[0]->company_id==$company->id) selected="" @endif value="{{ $company->id }}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->

                            </div>
                            <hr>
                            <div class="row">
                                <h3 class="title">Enter Records</h3>
                            </div>
                            <div id="single_data_div">
                                <div id="dynamic_div_0">
                                    <div class="row">
                                        <!--/span-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Select Department</label>
                                                <select name="department_id" required="" id="department_id" class="form-control">
                                                    <option value="">Select Department</option>
                                                    @foreach($department_list as $department)
                                                    <option @if($budget_sheet_detail[0]->department_id==$department->id) selected="" @endif value="{{ $department->id }}">{{ $department->dept_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Select Vendor</label>
                                                <select name="vendor_id" id="vendor_id" class="form-control">
                                                    <option value="">Select Vendor</option>
                                                    @foreach($vendor_list as $vendor)
                                                    <option @if($vendor->id==$budget_sheet_detail[0]->vendor_id) selected="" @endif value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!--/span-->
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                
                                                <label class="control-label">Budget Sheet Files</label>
                                                <input type="file"  class="form-control" name="budget_sheet_file[]" id="budget_sheet_file" multiple="" />
                                                
                                            </div>
                                            <!--/span-->
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Schedule Date</label>
                                                <input class="form-control input-daterange-datepicker" type="text" id="schedule_date" name="schedule_date" value="{{ date('d/m/Y',strtotime($budget_sheet_detail[0]->schedule_date_from)).' - '.date('d/m/Y',strtotime($budget_sheet_detail[0]->schedule_date_to))}}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Description</label>
                                                <textarea name="description" id="description" class="form-control">{{$budget_sheet_detail[0]->description}}</textarea>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Remarks if any</label>
                                                <textarea name="remark_by" id="remark_by" class="form-control">{{$budget_sheet_detail[0]->remark_by_user}}</textarea>
                                            </div>
                                        </div>

                                        <!--/span-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Request Amount</label>
                                                <input type="text" name="request_amount" value="{{$budget_sheet_detail[0]->request_amount}}" id="request_amount" class="form-control" />
                                            </div>
                                            <!--/span-->
                                        </div>
                                        
                                        <!--/span-->
                                        <!-- span -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Mode Of Payment</label>
                                                <select name="mode_of_payment" id="mode_of_payment" class="form-control">
                                                    <option @if($budget_sheet_detail[0]->mode_of_payment=='Cash') selected @endif value="Cash">Cash</option>
                                                    <option @if($budget_sheet_detail[0]->mode_of_payment=='Bank') selected @endif value="Bank">Bank</option>
                                                    <option @if($budget_sheet_detail[0]->mode_of_payment=='Online') selected @endif value="Online">Online</option>
                                                </select>
                                                
                                            </div>
                                            <!--/span-->
                                        </div>
                                        <!-- /span -->
                                    </div>

                                    <div class="row">

                                    <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Select Client</label>
                                        <select class="form-control" onchange="get_project_list(this);" required id="client_id" name="client_id">
                                        <option value="">Select Client</option>

                                        </select>
                                    </div>
                                </div>
                                      

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Select Project</label>
                                                <select name="project_id" onchange="get_sites_list();" id="project_id" class="form-control">
                                                    <option value="">Select Project</option>
                                                   
                                                   
                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-3 sites">
                                        <div class="form-group ">
                                <label>Select Project Site</label>
                                <select class="form-control site" id="project_site_id" name="project_site_id">
                                    <option value="">Select Site</option>

                                </select>
                            </div>
                            </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Total Amount</label>
                                                <input class="form-control" type="text" id="total_amount" name="total_amount" value="{{ $budget_sheet_detail[0]->total_amount }}"/>
                                            </div>
                                        </div>

                                    </div>


                                    <hr>
                                </div>
                            </div>

                            <br>
                            @if($budget_sheet_detail[0]->status=="Rejected")
                            <button type="submit"  class="btn btn-success">Re-Submit</button>
                            @else
                            <button type="submit"  class="btn btn-success">Submit</button>
                            @endif
                            <button type="button" onclick="window.location.href ='{{ route('admin.budget_sheet') }}'" class="btn btn-default">Cancel</button>
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


        htmlStr = '';
                 $.ajax({
                    url: "{{ route('admin.get_company_client_list') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        company_id: $('#company_id').val()
                    },
                    dataType: "JSON",
                    success: function(data) {

                        $('#client_id').empty();
                        $('#client_id').append("<option value='' selected>Select Client</option>");
                        $.each(data, function(index, clients_obj) {
                            
                            if (clients_obj.id == 1) {
                                htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                            }else{
                                htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                            }
                            
                            //$("#client_id").append('<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>');
                        });
                        $('#client_id').append(htmlStr);
                        setTimeout(() => {
                    $('#client_id').val("<?php echo $budget_sheet_detail[0]->client_id; ?>");
                }, 1000);

                    }
                });
    
    // project
    client_id = "<?php echo $budget_sheet_detail[0]->client_id; ?>";
        $.ajax({
            url: "{{ route('admin.get_client_project_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                client_id: client_id
            },
            dataType: "JSON",
            success: function(data) {
                $("#project_id").empty();
                $("#project_id").append("<option value='' selected>Select Project</option>");
                $.each(data, function(index, projects_obj) {
                    $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                });
               // setTimeout(() => {
                    
                    $("#project_id").val("<?php echo $budget_sheet_detail[0]->project_id; ?>");
                    var other_project = "<?php echo $budget_sheet_detail[0]->project_id; ?>";
                    if (other_project == 1) {   
                        
                        $("#other_project_txt").show();
                    }
                //}, 1000);
            }
        });

    //sites
    project_id = client_id = "<?php echo $budget_sheet_detail[0]->project_id; ?>";
        $.ajax({
            url: "{{ route('admin.get_project_sites_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                project_id: project_id
            },
            dataType: "JSON",
            success: function(data) {
                $("#project_site_id").empty();
                $("#project_site_id").append("<option value='' selected>Select Site</option>");
                $.each(data, function(index, project_site_obj) {
                    $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                });
                setTimeout(() => {
                    $("#project_site_id").val("<?php echo $budget_sheet_detail[0]->project_site_id; ?>");
                }, 1000);
            }
        });

    
    // function get_project_list() {
    //     $.ajax({
    //         url: "{{ route('admin.get_projectlist_by_company') }}",
    //         type: "post",
    //         dataType: "html",
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         data: {company_id: $('#company_id').val(), },
    //         success: function (data) {
    //             $('select[name="project_id"]').html(data);
    //         }
    //     });
    // }


//--------------------------new ajax -------------------------------------------------------------------//

function get_project_list() {
        

        $.ajax({
            url: "{{ route('admin.get_client_project_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                client_id: $("#client_id").val()
            },
            dataType: "JSON",
            success: function(data) {
                $("#project_id").empty();
                $("#project_id").append("<option value='' selected>Select Project</option>");

                $.each(data, function(index, projects_obj) {

                    $('#project_id').append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');

                });
            }
        });
    // $.ajax({
    //     url: "{{ route('admin.get_projectlist_by_company') }}",
    //     type: "post",
    //     dataType: "html",
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     },
    //     data: {company_id: $('#company_id').val(), },
    //     success: function (data) {
    //         $('select[name="project_id[]"]').html(data);
    //     }
    // });
}

function get_client_list() {
    htmlStr = '';
             $.ajax({
                url: "{{ route('admin.get_company_client_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    company_id: $('#company_id').val()
                },
                dataType: "JSON",
                success: function(data) {

                    $('#client_id').empty();
                    $("#client_id").append("<option value='' selected>Select Client</option>");
                    $.each(data, function(index, clients_obj) {
                        
                        if (clients_obj.id == 1) {
                            htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                        }else{
                            htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                        }
                        
                        //$("#client_id").append('<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>');
                    });
                    $('#client_id').append(htmlStr);

                }
            });
}

function get_sites_list() {


   
        $.ajax({
            url: "{{ route('admin.get_project_sites_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                project_id: $("#project_id").val()
            },
            dataType: "JSON",
            success: function(data) {
                $("#project_site_id").empty();
                $("#project_site_id").append("<option value='' selected>Select Site</option>");
                $.each(data, function(index, project_site_obj) {
                    $('#project_site_id').append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                })
                
            }
        });

}    

//-----------------------------------------------------------------------------------------------//
    function get_vendor_list() {
        $.ajax({
            url: "{{ route('admin.get_vendorlist_by_company') }}",
            type: "post",
            dataType: "html",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {company_id: $('#company_id').val(), },
            success: function (data) {
                $('select[name="vendor_id"]').html(data);
            }
        });
    }

    

    jQuery('#update_budget_sheet').validate({
        ignore: [],
        rules: {
            id: {
                required: true,
            },
            budget_sheet_year: {
                required: true,
            },
            budget_sheet_week: {
                required: true,
            },
            client_id: {
                required: true
            },
            project_site_id: {
                required: true
            },
            company_id: {
                required: true,
            },
            'department_id': {
                required: true,
            },
            vendor_id: {
                required: true,
            },
            description: {
                required: true,
            },
            remark_by: {
                required: true,
            },

            request_amount: {
                required: true,
                number:true
            },
            schedule_date: {
                required: true,
            },
            mode_of_payment: {
                required: true,
            },
            project_id: {
                required: true,
            },
            total_amount: {
                required: true,
                number:true
            },
        }
    });
    $(document).ready(function () {
        $('.input-daterange-datepicker').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('.remove_btn').hide();
    });

</script>
@endsection
