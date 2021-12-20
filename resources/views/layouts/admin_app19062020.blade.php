<?php

use App\Roles;
use App\Role_module;
use App\Lib\Permissions;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('admin_asset/assets/plugins/images/favicon.png') }}">
        <title>{{ $setting_details[0]->setting_value }}</title>
        <!-- Bootstrap Core CSS -->
        <link href="{{asset('admin_asset/assets/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- Menu CSS -->
        <link href="{{asset('admin_asset/assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
        <!-- toast CSS -->
        <link href="{{asset('admin_asset/assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <!-- morris CSS -->
        <link href="{{asset('admin_asset/assets/plugins/bower_components/morrisjs/morris.css') }}" rel="stylesheet">
        <!-- Image Popup CSS -->
        <link href="{{asset('admin_asset/assets/plugins/bower_components/Magnific-Popup-master/dist/magnific-popup.css') }}" rel="stylesheet">
        <!-- animation CSS -->
        <link href="{{asset('admin_asset/assets/css/animate.css') }}" rel="stylesheet">
        <link href="{{asset('admin_asset/assets/plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" rel="stylesheet" />

        <link href="{{asset('admin_asset/assets/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{asset('admin_asset/assets/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{asset('admin_asset/assets/plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" />
        <link href="{{asset('admin_asset/assets/plugins/bower_components/multiselect/css/multi-select.css') }}"  rel="stylesheet" type="text/css" />
        <!-- Custom CSS -->
        <link href="{{asset('admin_asset/assets/css/style.css') }}" rel="stylesheet">
        <!-- color CSS -->
        <link href="{{asset('admin_asset/assets/css/colors/default.css') }}" id="theme"  rel="stylesheet">
        <link href="{{asset('admin_asset/assets/plugins/bower_components/sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css">
        <link href="{{asset('admin_asset/assets/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet" type="text/css">
        <!-- Date picker plugins css -->
        <link href="{{asset('admin_asset/assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Daterange picker plugins css -->
        <link href="{{asset('admin_asset/assets/plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
        <link href="{{asset('admin_asset/assets/plugins/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('admin_asset/assets/plugins/bower_components/dropify/dist/css/dropify.min.css') }}">
        <link href="{{asset('admin_asset/assets/plugins/bower_components/multiselect/css/multi-select.css') }}"  rel="stylesheet" type="text/css" />       
        <link href="{{asset('admin_asset/assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('admin_asset/assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') }}" />
        <link href="{{asset('admin_asset/assets/plugins/bower_components/horizontal-timeline/css/horizontal-timeline.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{asset('admin_asset/assets/plugins/bower_components/fancybox/ekko-lightbox.min.css') }}"/>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
<style>
.slimScrollDiv{
	overflow-y:scroll !important;
}
.sidebar-nav{
	overflow:unset !important;
	width:unset !important;
	height:unset !important;
}
</style>

    </head>
    <body class="fix-sidebar">
        <!-- Preloader -->
        <div class="preloader">
            <div class="cssload-speeding-wheel"></div>
        </div>
        <div id="wrapper">
            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top m-b-0">
                <div class="navbar-header"> 
                    <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>
                    <div class="top-left-part"><a class="logo" href="{{ route('admin.dashboard') }}"><b><!--This is dark logo icon--><img src="{{asset('admin_asset/assets/plugins/images/eliteadmin-logo.png') }}" alt="home" class="dark-logo" /><!--This is light logo icon--><img src="{{asset('admin_asset/assets/plugins/images/eliteadmin-logo-dark.png')}}" alt="home" class="light-logo" /></b><span class="hidden-xs"><!--This is dark logo text--><img src="{{asset('admin_asset/assets/plugins/images/eliteadmin-text.png') }}" alt="home" class="dark-logo" /><!--This is light logo text--><img src="{{asset('admin_asset/assets/plugins/images/eliteadmin-text-dark.png') }}" alt="home" class="light-logo" /></span></a></div>
                    <ul class="nav navbar-top-links navbar-left hidden-xs">
                        <li><a href="javascript:void(0)" class="open-close hidden-xs waves-effect waves-light"><i class="icon-arrow-left-circle ti-menu"></i></a></li>

                    </ul>

                </div>
                <!-- /.navbar-header -->
                <!-- /.navbar-top-links -->
                <!-- /.navbar-static-side -->
            </nav>
            <!-- Left navbar-header -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse slimscrollsidebar">
                    <div class="user-profile">
                        <div class="dropdown user-pro-body">
                            <div>
                                @if(Auth::user()->profile_image)

                                <img src="{{ asset('storage/'.str_replace('public/','',Auth::user()->profile_image)) }}" alt="user-img" class="img-circle">
                                @else
                                <img src="{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}" alt="user-img" class="img-circle">
                                @endif
                            </div>
                            <a href="#" class="dropdown-toggle u-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{  Auth::user()->name }} <span class="caret"></span></a>
                            <ul class="dropdown-menu animated flipInY">
                                <li><a href="{{ route('admin.profile') }}"><i class="ti-wallet"></i> Profile</a></li>
                                <li><a href="{{ route('admin.changepassword') }}"><i class="ti-wallet"></i> Change Password</a></li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: one;">
                                    {{ csrf_field() }}
                                    <input name="role_type" id="role_type" type="hidden" value="Admin" />
                                </form>
                                <li><a href="#" onclick="javascript:$('#logout-form').submit();"><i class="fa fa-power-off"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                    <ul class="nav" id="side-menu">

                        <li> <a href="{{ route('admin.dashboard') }}" class="waves-effect"><i data-icon="a" class="linea-icon linea-basic fa-fw"></i> <span class="hide-menu">Dashboard</span></a> </li>
                        <li> <a href="{{ route('admin.organization_chart') }}" class="waves-effect"><i class="fa fa-sitemap"></i> <span class="hide-menu">Organizational Structure</span></a> </li>
                        @if(Auth::user()->role==config('constants.SuperUser'))
						<li> <a href="#" class="waves-effect"><i class="fa fa-gears" data-icon="v"></i> <span class="hide-menu">General<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">

                                <?php
                                $setting_my_view_permission = Permissions::checkPermission(1, 1);
                                ?>
                                @if($setting_my_view_permission)
                                <li> <a href="{{ route('admin.setting') }}" class="waves-effect"><i class="fa fa-gears"></i> <span class="hide-menu">Settings</span></a> </li>
                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser'))
                                <li> <a href="{{ route('admin.roles') }}" class="waves-effect"><i class="icon-key fa-fw"></i> <span class="hide-menu">Role</span></a> </li>
                                @endif

                                <?php
                                $emailformat_my_view_permission = Permissions::checkPermission(2, 1);
                                ?>
                                @if($emailformat_my_view_permission)
                                <li> <a href="{{ route('admin.emailformat') }}" class="waves-effect"><i class="fa fa-envelope"></i> <span class="hide-menu">Email Format</span></a> </li>
                                @endif

                                <?php
                                $department_my_view_permission = Permissions::checkPermission(6, 1);
                                $department_full_view_permission = Permissions::checkPermission(6, 5);
                                $department_partial_view_permission = Permissions::checkPermission(6, 6);
                                ?>
                                @if($department_full_view_permission)
                                <li> <a href="{{ route('admin.department') }}" class="waves-effect"><i class="fa fa-sitemap fa-fw"></i> <span class="hide-menu">Department</span></a> </li>
                                @endif

                                <?php
                                $companies_full_view_permission = Permissions::checkPermission(17, 5);
                                $companies_partial_view_permission = Permissions::checkPermission(17, 6);
                                $companies_my_view_permission = Permissions::checkPermission(17, 1);
                                ?>
                                @if($companies_full_view_permission)
                                <li> <a href="{{ route('admin.companies') }}" class="waves-effect"><i class="fa fa-fort-awesome fa-fw"></i> <span class="hide-menu">Company</span></a> </li>
                                @endif

                                

                                <?php
                                $vendor_full_view_permission = Permissions::checkPermission(35, 5);
                                ?>
                                @if($vendor_full_view_permission)
									<li> <a href="{{ route('admin.client') }}" class="waves-effect"><i class="fa fa-fort-awesome fa-fw"></i> <span class="hide-menu">Client</span></a> </li>
                                <li> <a href="{{ route('admin.vendors') }}" class="waves-effect"><i class="fa fa-gavel fa-fw"></i> <span class="hide-menu">Vendors</span></a> </li>
                                @endif

                                <?php
                                $vendor_bank_full_view_permission = Permissions::checkPermission(45, 5);
                                ?>
                                @if($vendor_bank_full_view_permission)
                                <li> <a href="{{ route('admin.vendors_bank') }}" class="waves-effect"><i class="fa fa-gavel fa-fw"></i> <span class="hide-menu">Vendors Bank Detail</span></a> </li>
                                <li> <a href="{{ route('admin.vendorsBank_report') }}" class="waves-effect"><i class="fa fa-file-excel-o"></i> <span class="hide-menu">Vendors Bank Report</span></a> </li>
								@endif

                                <?php
                                $bank_full_view_permission = Permissions::checkPermission(9, 5);
                                ?>
                                @if($bank_full_view_permission)
                                <li> <a href="{{ route('admin.banks') }}" class="waves-effect"><i class="fa fa-bank"></i> <span class="hide-menu">Bank</span></a> </li>
                                <li> <a href="{{ route('admin.bank_charge_category') }}" class="waves-effect"><i class="fa fa-bank"></i> <span class="hide-menu">Bank Charge Category</span></a> </li>
                                <li> <a href="{{ route('admin.bank_charge_sub_category') }}" class="waves-effect"><i class="fa fa-bank"></i> <span class="hide-menu">Bank Charge Sub Category</span></a> </li>
                                @endif
                                <?php
                                $project_full_view_permission = Permissions::checkPermission(36, 5);
                                ?>
                                @if($project_full_view_permission)
                                <li> <a href="{{ route('admin.projects') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Projects</span></a> </li>
                                @endif
								
								<?php
                                $project_sites_full_view_permission = Permissions::checkPermission(53, 5);
                                ?>
                                @if($project_sites_full_view_permission)
                                <li> <a href="{{ route('admin.project_site') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Project sites</span></a> </li>
                                @endif
								
                                <?php
                                $holiday_full_view_permission = Permissions::checkPermission(17, 5);

                                $holiday_my_view_permission = Permissions::checkPermission(17, 1);
                                ?>
                                @if($holiday_full_view_permission || $holiday_my_view_permission)
                                <li> <a href="{{ route('admin.holiday') }}" class="waves-effect"><i class="fa fa-file-movie-o fa-fw"></i> <span class="hide-menu">Holiday</span></a> </li>
                                @endif
                                
                                <?php
                                $payment_card_full_view_permission = Permissions::checkPermission(47, 5);

                                ?>
                                @if($payment_card_full_view_permission)
                                <li> <a href="{{ route('admin.payment_card') }}" class="waves-effect"><i class="fa fa-credit-card"></i> <span class="hide-menu">Payment Cards</span></a> </li>
                                @endif

                            </ul>
                        </li>
						@endif
                        <li>
                            <a href="#" class="waves-effect"><i class="fa fa-file" data-icon="v"></i> 
                                <span class="hide-menu">Company Document Management<span class="fa arrow"></span></span>
                            </a>
                            <ul class="nav nav-second-level">
                                <?php
                                $company_doc_full_view_permission = Permissions::checkPermission(60, 5);
                                $company_doc_partial_view_permission = Permissions::checkPermission(60, 6);
                                $company_doc_my_view_permission = Permissions::checkPermission(60, 1);
                                ?>
                                @if($company_doc_full_view_permission || $company_doc_partial_view_permission || $company_doc_my_view_permission)
                                    <li> <a href="{{ route('admin.company_document_management') }}" class="waves-effect"><i data-icon="a" class="fa fa-file"></i> <span class="hide-menu">Document List</span></a> </li>
                                    
                                @endif
								<li> <a href="{{ route('admin.company_document_request') }}" class="waves-effect"><i data-icon="a" class="fa fa-file"></i> <span class="hide-menu">Request Document</span></a> </li>
                            </ul>
                        </li>

                        <?php
                                $company_project_document_full_view_permission = Permissions::checkPermission(49, 5);
                                
                                ?>
                        @if($company_project_document_full_view_permission)
                        <!--<li> <a href="{{ route('admin.company_document_list') }}" class="waves-effect"><i class="fa fa-folder-open"></i> <span class="hide-menu">Company Project Documents</span></a> </li>-->
                        @endif
                        <li><a href="#" class="waves-effect"><i class="icon-people fa-fw" data-icon="v"></i> <span class="hide-menu">Employee<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                $users_full_view_permission = Permissions::checkPermission(3, 5);
                                $users_partial_view_permission = Permissions::checkPermission(3, 6);
                                $users_my_view_permission = Permissions::checkPermission(3, 1);
                                ?>
                                @if($users_full_view_permission || $users_partial_view_permission || $users_my_view_permission)
                                <li> <a href="{{ route('admin.users') }}" class="waves-effect"><i class="icon-people fa-fw"></i> <span class="hide-menu">List Of Employee</span></a> </li>
                                @endif

                                <?php
                                $employee_bank_full_view_permission = Permissions::checkPermission(11, 5);
                                $employee_bank_partial_view_permission = Permissions::checkPermission(11, 6);
                                $employee_bank_my_view_permission = Permissions::checkPermission(11, 1);
                                ?>
                                @if($employee_bank_full_view_permission || $employee_bank_partial_view_permission || $employee_bank_my_view_permission)
                                <li> <a href="{{ route('admin.employee_bank') }}" class="waves-effect"><i class="fa fa-bank"></i> <span class="hide-menu">Employee Bank</span></a> </li>
                                @endif

                                <?php
                                $employee_loan_full_view_permission = Permissions::checkPermission(10, 5);
                                $employee_loan_partial_view_permission = Permissions::checkPermission(10, 6);
                                $employee_loan_my_view_permission = Permissions::checkPermission(10, 1);
                                ?>
                                @if($employee_loan_full_view_permission || $employee_loan_partial_view_permission || $employee_loan_my_view_permission)
                                <li> <a href="{{ route('admin.employee_loan') }}" class="waves-effect"><i class="fa fa-money"></i> <span class="hide-menu">Employee Loan</span></a> </li>
                                @endif
								
								<?php
                                $employee_insurance_full_view_permission = Permissions::checkPermission(55, 5);
                                
                                ?>
                                @if($employee_insurance_full_view_permission)
                                <li> <a href="{{ route('admin.employees_insurances') }}" class="waves-effect"><i class="fa fa-user"></i> <span class="hide-menu">Employee Insurance</span></a> </li>
                                @endif
                            </ul>
                        </li>

                        <li><a href="#" class="waves-effect"><i class="icon-briefcase fa-fw" data-icon="v"></i> <span class="hide-menu">Recruitment<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                $job_opening_full_view_permission = Permissions::checkPermission(7, 5);
                                $job_opening_partial_view_permission = Permissions::checkPermission(7, 6);
                                $job_opening_my_view_permission = Permissions::checkPermission(7, 1);
                                ?>
                                @if($employee_loan_full_view_permission || $employee_loan_partial_view_permission || $employee_loan_my_view_permission)
                                <li> <a href="{{ route('admin.job_opening') }}" class="waves-effect"><i class="icon-wallet fa-fw"></i> <span class="hide-menu">Job Opening</span></a> </li>
                                @endif

                                <?php
                                $interview_full_view_permission = Permissions::checkPermission(5, 5);
                                $interview_partial_view_permission = Permissions::checkPermission(5, 6);
                                $interview_my_view_permission = Permissions::checkPermission(5, 1);
                                ?>
                                @if($interview_full_view_permission || $interview_partial_view_permission || $interview_my_view_permission)
                                <li> <a href="{{ route('admin.interview') }}" class="waves-effect"><i class="icon-briefcase fa-fw"></i> <span class="hide-menu">Interview Process</span></a> </li>
                                @endif

                                <?php
                                $recruitment_consultant_full_view_permission = Permissions::checkPermission(33, 5);
                                $recruitment_consultant_partial_view_permission = Permissions::checkPermission(33, 6);
                                $recruitment_consultant_my_view_permission = Permissions::checkPermission(33, 1);
                                ?>
                                @if($recruitment_consultant_full_view_permission || $recruitment_consultant_partial_view_permission || $recruitment_consultant_my_view_permission)
                                <li> <a href="{{ route('admin.recruitment_consultant') }}" class="waves-effect"><i class="icon-call-in fa-fw"></i> <span class="hide-menu">Consultant</span></a> </li>
                                @endif
                            </ul>
                        </li>
						<?php
                                $meeting_full_view_permission = Permissions::checkPermission(56, 5);
                               
                                ?>
								@if($meeting_full_view_permission)
									<li> <a href="{{ route('admin.meeting') }}" class="waves-effect"><i data-icon="a" class="linea-icon linea-basic fa-fw"></i> <span class="hide-menu">Meetings</span></a> </li>
								@endif
                        <li><a href="#" class="waves-effect"><i class="icon-handbag fa-fw" data-icon="v"></i> <span class="hide-menu">Leave<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                $leave_full_view_permission = Permissions::checkPermission(4, 5);
                                $leave_partial_view_permission = Permissions::checkPermission(4, 6);
                                $leave_my_view_permission = Permissions::checkPermission(4, 1);
                                ?>
                                @if($leave_full_view_permission || $leave_partial_view_permission || $leave_my_view_permission)
                                <li><a href="{{ route('admin.leave') }}"><i class="ti-wallet"></i> Leave</a></li>
                                @endif

                                <?php
                                $leavecategory_full_view_permission = Permissions::checkPermission(32, 5);
                                $leavecategory_partial_view_permission = Permissions::checkPermission(32, 6);
                                $leavecategory_my_view_permission = Permissions::checkPermission(32, 1);
                                ?>
                                @if($leave_full_view_permission || $leave_partial_view_permission)
                                <li><a href="{{ route('admin.leavecategory') }}"><i class="ti-wallet"></i> Leave Category</a></li>
                                @endif


                                <li><a href="{{ route('admin.relieving_request') }}"><i class="ti-wallet"></i> Reliever Leave</a></li>
                                <?php
                                $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 4])->get()->first();
                                $access = '';
                                $role = [];
                                if (!empty($access_level)) {
                                    $access_rule = $access_level->access_level;
                                    $role = explode(',', $access_rule);
                                }
                                ?>
                                <?php
                                if (in_array(2, $role) && in_array(5, $role) || Auth::user()->role == config('constants.SuperUser') || Auth::user()->role == config('constants.REAL_HR')) {
                                    ?>
                                    <li><a href="{{ route('admin.all_leave') }}"><i class="ti-wallet"></i> Leave Approval</a></li>
                                    <?php
                                }
                                ?>
                                @if($leave_full_view_permission)
                                <li><a href="{{ route('admin.leave_reliever_report') }}"><i class="fa fa-file-excel-o"></i> Leave Reliever Report</a></li>
                                @endif
                            </ul>
                        </li>
                        <li><a href="#" class="waves-effect"><i class="icon-handbag fa-fw" data-icon="v"></i> <span class="hide-menu">Driver Expense<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                $expense_full_view_permission = Permissions::checkPermission(31, 5);
                                $expense_partial_view_permission = Permissions::checkPermission(31, 6);
                                $expense_my_view_permission = Permissions::checkPermission(31, 1);
                                ?>
                                @if($expense_full_view_permission || $expense_partial_view_permission || $expense_my_view_permission)
                                <li><a href="{{ route('admin.expense') }}"><i class="ti-wallet"></i> Expense</a></li>
                                @endif


                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.Admin') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                                <li><a href="{{ route('admin.all_expense') }}"><i class="ti-wallet"></i> Approve Expense</a></li>
                                @endif



                            </ul>
                        </li>

                        <li>
                            <a href="#" class="waves-effect"><i class="fa fa-clock-o" data-icon="v"></i> 
                                <span class="hide-menu">Attendance Management<span class="fa arrow"></span></span>
                            </a>
                            <ul class="nav nav-second-level">
							<li> <a href="{{ route('admin.holiday_work_attendance') }}" class="waves-effect"><i data-icon="a" class="fa fa-clock-o"></i> <span class="hide-menu">Weekend/Holiday Attendance Request</span></a> </li>
							@if(Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.SuperUser'))
							<li> <a href="{{ route('admin.work_off_all_attendance_history') }}" class="waves-effect"><i data-icon="a" class="fa fa-clock-o"></i> <span class="hide-menu">Weekend/Holiday Attendance Request Approval</span></a> </li>
							@endif
                                <li> <a href="{{ route('admin.attendance') }}" class="waves-effect"><i data-icon="a" class="fa fa-clock-o"></i> <span class="hide-menu">Attendance</span></a> </li>
                                @if(Auth::user()->role==config('constants.REAL_HR'))
                                <li> <a href="{{ route('admin.approve_attendance') }}" class="waves-effect"><i data-icon="a" class="fa fa-clock-o"></i> <span class="hide-menu">Approve Attendance</span></a> </li>
                                @endif
                                @if(Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.Admin') || Auth::user()->role==config('constants.SuperUser'))
                                <li> <a href="{{ route('admin.attendance_report') }}" class="waves-effect"><i data-icon="a" class="fa fa-file-excel-o"></i> <span class="hide-menu">Attendance Report</span></a> </li>
                                @endif
                            </ul>
                        </li>


                        <li><a href="#" class="waves-effect"><i class="icon-credit-card fa-fw" data-icon="v"></i> <span class="hide-menu">Finance<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                $employee_salary_full_view_permission = Permissions::checkPermission(12, 5);
                                $employee_salary_partial_view_permission = Permissions::checkPermission(12, 6);
                                $employee_salary_my_view_permission = Permissions::checkPermission(12, 1);
                                ?>
                                @if($employee_salary_full_view_permission || $employee_salary_partial_view_permission || $employee_salary_my_view_permission)
                                <li> <a href="{{ route('admin.employee_salary') }}" class="waves-effect"><i class="fa fa-usd"></i> <span class="hide-menu">Employee Salary Structure</span></a> </li>
                                @endif

                                <?php
                                $tax_declaration_full_view_permission = Permissions::checkPermission(8, 5);
                                $tax_declaration_partial_view_permission = Permissions::checkPermission(8, 6);
                                $tax_declaration_my_view_permission = Permissions::checkPermission(8, 1);
                                ?>
                                @if($tax_declaration_full_view_permission || $tax_declaration_partial_view_permission || $tax_declaration_my_view_permission)
                                <li> <a href="{{ route('admin.tax_declaration') }}" class="waves-effect"><i class="icon-notebook"></i> <span class="hide-menu">Tax Declration</span></a> </li>
                                @endif

                                <?php
                                $expense_category_full_view_permission = Permissions::checkPermission(16, 5);
                                $expense_category_partial_view_permission = Permissions::checkPermission(16, 6);
                                $expense_category_my_view_permission = Permissions::checkPermission(16, 1);
                                ?>
                                @if($expense_category_full_view_permission || $expense_category_partial_view_permission || $expense_category_my_view_permission)
                                <li> <a href="{{ route('admin.expense_category') }}" class="waves-effect"><i class="icon-chemistry fa-fw"></i> <span class="hide-menu">Expense Category</span></a> </li>
                                @endif

                                <?php
                                $employee_expense_full_view_permission = Permissions::checkPermission(19, 5);
                                $employee_expense_partial_view_permission = Permissions::checkPermission(19, 6);
                                $employee_expense_my_view_permission = Permissions::checkPermission(19, 1);
                                ?>
                                @if($employee_expense_full_view_permission || $employee_expense_partial_view_permission || $employee_expense_my_view_permission)
                                <li> <a href="{{ route('admin.employee_expense') }}" class="waves-effect"><i class="icon-hourglass fa-fw"></i> <span class="hide-menu">Employee Expense</span></a> </li>
                                @endif


                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.ASSISTANT') || Auth::user()->role==config('constants.Admin') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                                <li> <a href="{{ route('admin.employee_expense_list') }}" class="waves-effect"><i class="icon-hourglass fa-fw"></i> <span class="hide-menu">Approve Employee Expense</span></a> </li>
                                @endif

                                <?php
                                $travel_full_view_permission = Permissions::checkPermission(41, 5);

                                $travel_my_view_permission = Permissions::checkPermission(41, 1);
                                ?>
                                @if($travel_full_view_permission || $travel_my_view_permission)
                                <li><a href="{{ route('admin.travel') }}"><i class="fa fa fa-plane"></i> Travel Request</a></li>
                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser') ||
                                Auth::user()->role==config('constants.ASSISTANT'))
                                <li><a href="{{ route('admin.travel_requests') }}"><i class="fa fa-plane"></i> Approve Travel Request</a></li>
                                @endif

                                <?php
                                $hotel_full_view_permission = Permissions::checkPermission(42, 5);

                                $hotel_my_view_permission = Permissions::checkPermission(42, 1);
                                ?>
                                @if($hotel_full_view_permission || $hotel_my_view_permission)
                                <!--<li><a href="{{ route('admin.hotel') }}"><i class="fa fa fa-hotel"></i> Hotel Booking Expense</a></li>-->
                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.Admin') || Auth::user()->role==config('constants.ACCOUNT_ROLE') ||
                                Auth::user()->role==config('constants.ASSISTANT'))
                                <!--<li><a href="{{ route('admin.all_hotel') }}"><i class="fa fa-hotel"></i> Approve Hotel Booking</a></li>-->
                                @endif

                                <?php
                                $get_salary_slip_full_view_permission = Permissions::checkPermission(30, 5);
//$get_salary_slip_partial_view_permission= Permissions::checkPermission(19,6);
                                $get_salary_slip_my_view_permission = Permissions::checkPermission(30, 1);
                                ?>
                                @if($get_salary_slip_full_view_permission || $get_salary_slip_my_view_permission)
                                <li> <a href="{{ route('admin.get_salary_slip') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Salary Slip</span></a> </li>
                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.ASSISTANT') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE') || Auth::user()->role==config('constants.Admin'))
                                <li> <a href="{{ route('admin.get_payroll') }}" class="waves-effect"><i class="icon-calculator fa-fw"></i> <span class="hide-menu">Payroll</span></a> </li>
                                @endif
                            </ul>
                        </li>

                        <li><a href="#" class="waves-effect"><i class="icon-rocket fa-fw" data-icon="v"></i> <span class="hide-menu">Asset<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                $asset_full_view_permission = Permissions::checkPermission(14, 5);
                                $asset_partial_view_permission = Permissions::checkPermission(14, 6);
                                $asset_my_view_permission = Permissions::checkPermission(14, 1);
                                ?>
                                @if($asset_full_view_permission || $asset_my_view_permission || $asset_partial_view_permission)
                                <li> <a href="{{ route('admin.asset') }}" class="waves-effect"><i class="icon-emotsmile fa-fw"></i> <span class="hide-menu">Asset</span></a> </li>
                                @endif
								 @if(Auth::user()->role==config('constants.REAL_HR'))
									 <li> <a href="{{ route('admin.hr_access_request') }}" class="waves-effect"><i class="fa fa-check"></i> <span class="hide-menu">Approve Asset Assigned</span></a> </li>
								 @endif
								@if($asset_full_view_permission)
									<li> <a href="{{ route('admin.asset_report') }}" class="waves-effect"><i class="fa fa-file-excel-o"></i> <span class="hide-menu">Asset Report</span></a> </li>
								@endif

                                <?php
                                $asset_access_full_view_permission = Permissions::checkPermission(15, 5);
                                $asset_access_partial_view_permission = Permissions::checkPermission(15, 6);
                                $asset_access_my_view_permission = Permissions::checkPermission(15, 1);
                                ?>
                                @if($asset_access_full_view_permission || $asset_access_my_view_permission || $asset_access_partial_view_permission)

                                <li> <a href="{{ route('admin.asset_access') }}" class="waves-effect"><i class="icon-briefcase"></i> <span class="hide-menu">Asset Assign</span></a> </li>
                                @endif

                                <?php
                                $vehicle_insurance_full_view_permission = Permissions::checkPermission(38, 5);
                                ?>
                                @if($vehicle_insurance_full_view_permission)
                                <li> <a href="{{ route('admin.vehicle_assets') }}" class="waves-effect"><i class="fa fa-car"></i> <span class="hide-menu">Vehicle Insurance Management</span></a> </li>
                                
                                @endif
                                
                                <?php 
                                $vehicle_maintanence_my_view_permission= Permissions::checkPermission(46,1);
                                $vehicle_maintanence_full_view_permission= Permissions::checkPermission(46,5);
                                ?>
                                @if($vehicle_maintanence_my_view_permission || $vehicle_maintanence_full_view_permission)
                                <li> <a href="{{ route('admin.vehicle_maintenance') }}" class="waves-effect"><i class="fa fa-cog"></i> <span class="hide-menu">Vehicle Maintenance</span></a> </li>
                                @endif
								
								@if (Auth::user()->role == config('constants.Admin') || Auth::user()->role==config('constants.SuperUser'))
                                <li> <a href="{{ route('admin.vehicle_maintenance_list') }}" class="waves-effect"><i class="fa fa-cog"></i> <span class="hide-menu">Approve Vehicle Maintenance</span></a> </li>
                                @endif
                            </ul>
                        </li>

                        <li><a href="#" class="waves-effect"><i class="ti-announcement fa-fw" data-icon="v"></i> <span class="hide-menu">Policy<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                $policy_full_view_permission = Permissions::checkPermission(21, 5);
                                $policy_partial_view_permission = Permissions::checkPermission(21, 6);
                                $policy_my_view_permission = Permissions::checkPermission(21, 1);
                                ?>
                                @if($policy_full_view_permission || $policy_partial_view_permission || $policy_my_view_permission)
                                <li> <a href="{{ route('admin.policy') }}" class="waves-effect"><i class="icon-emotsmile fa-fw"></i> <span class="hide-menu">Policy</span></a> </li>
                                @endif
                                <?php
                                $revise_policy_list_full_view_permission = Permissions::checkPermission(22, 5);
                                $revise_policy_list_partial_view_permission = Permissions::checkPermission(22, 6);
                                $revise_policy_list_my_view_permission = Permissions::checkPermission(22, 1);
                                ?>
                                @if($revise_policy_list_full_view_permission || $revise_policy_list_partial_view_permission || $revise_policy_list_my_view_permission)
                                <li> <a href="{{ route('admin.revise_policy_list') }}" class="waves-effect"><i class="icon-briefcase"></i> <span class="hide-menu">Revise Policy List</span></a> </li>
                                @endif
                            </ul>
                        </li>

                        <li>
                            <a href="#" class="waves-effect"><i class="fa fa-file" data-icon="v"></i> 
                                <span class="hide-menu">Budget Sheets<span class="fa arrow"></span>

                                </span>
                            </a>
                            <ul class="nav nav-second-level">
                                <?php
                                $budget_sheet_full_view_permission = Permissions::checkPermission(26, 5);
                                $budget_sheet_partial_view_permission = Permissions::checkPermission(26, 6);
                                $budget_sheet_my_view_permission = Permissions::checkPermission(26, 1);
                                ?>
                                @if($budget_sheet_full_view_permission || $budget_sheet_partial_view_permission || $budget_sheet_my_view_permission)
                                <li> <a href="{{ route('admin.budget_sheet') }}" class="waves-effect"><i class="fa fa-paw" aria-hidden="true"></i> <span class="hide-menu">Budget Sheet</span></a> </li>
                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.Admin'))
                                <li> <a href="{{ route('admin.budget_sheet_list') }}" class="waves-effect"><i class="fa fa-paw"></i> <span class="hide-menu">Pending Budget Sheet Approvals</span></a> </li>

                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser'))

                                <li> <a href="{{ route('admin.hold_budget_sheet_list') }}" class="waves-effect"><i class="fa fa-paw"></i> <span class="hide-menu">Hold Budget Sheet</span></a> </li>

                                @endif
                                @if($budget_sheet_full_view_permission)
                                <li> <a href="{{ route('admin.budget_sheet_report') }}" class="waves-effect"><i class="fa fa-file-excel-o"></i> <span class="hide-menu">Budget Sheet Report</span></a> </li>
                                @endif
                            </ul>
                        </li>

                        <li><a href="#" class="waves-effect"><i class="fa fa-usd fa-fw" data-icon="v"></i> <span class="hide-menu">Payment<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                $payment_list_full_view_permission = Permissions::checkPermission(24, 5);
                                $payment_list_partial_view_permission = Permissions::checkPermission(24, 6);
                                $payment_list_my_view_permission = Permissions::checkPermission(24, 1);
                                ?>
                                @if($payment_list_full_view_permission || $payment_list_partial_view_permission || $payment_list_my_view_permission)
                                <li> <a href="{{ route('admin.payment') }}" class="waves-effect"><i class="fa fa-university fa-fw"></i> <span class="hide-menu">Bank Payment</span></a> </li>
                                @endif
								
								<?php
                                $online_payment_list_full_view_permission = Permissions::checkPermission(48, 5);
                                $online_payment_list_partial_view_permission = Permissions::checkPermission(48, 6);
                                $online_payment_list_my_view_permission = Permissions::checkPermission(48, 1);
                                ?>
                                @if($online_payment_list_full_view_permission || $online_payment_list_partial_view_permission || $online_payment_list_my_view_permission)
                                <li> <a href="{{ route('admin.online_payment') }}" class="waves-effect"><i class="fa fa-university fa-fw"></i> <span class="hide-menu">Online Payment</span></a> </li>
                                @endif
								
                                <?php
                                $cash_payment_full_view_permission = Permissions::checkPermission(25, 5);
                                $cash_payment_partial_view_permission = Permissions::checkPermission(25, 6);
                                $cash_payment_my_view_permission = Permissions::checkPermission(25, 1);
                                ?>
                                @if($cash_payment_full_view_permission || $cash_payment_partial_view_permission || $cash_payment_my_view_permission)
                                <li> <a href="{{ route('admin.cash_payment') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw"></i> <span class="hide-menu">Cash Payment</span></a> </li>
                                @endif



                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.ACCOUNT_ROLE') || Auth::user()->role==config('constants.Admin'))
                                <li> <a href="{{ route('admin.payment_list') }}" class="waves-effect"><i class="fa fa-university fa-fw"></i> <span class="hide-menu">Approve Bank Payment</span></a> </li>
                                <li> <a href="{{ route('admin.cash_payment_list') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw"></i> <span class="hide-menu">Approve Cash Payment</span></a> </li>
								<li> <a href="{{ route('admin.online_payment_list') }}" class="waves-effect"><i class="fa fa-university fa-fw"></i> <span class="hide-menu">Approve Online Payment</span></a> </li>
                                @endif
                            </ul>
                        </li>

                        <li><a href="#" class="waves-effect"><i class="fa fa-paper-plane" data-icon="v"></i> <span class="hide-menu">Letter-head Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                $pre_sign_letter_full_view_permission = Permissions::checkPermission(27, 5);
                                $pre_sign_letter_partial_view_permission = Permissions::checkPermission(27, 6);
                                $pre_sign_letter_my_view_permission = Permissions::checkPermission(27, 1);
                                ?>
                                @if($pre_sign_letter_full_view_permission || $pre_sign_letter_partial_view_permission || $pre_sign_letter_my_view_permission)
                                <li> <a href="{{ route('admin.pre_sign_letter') }}" class="waves-effect"><i class="fa fa-pencil fa-fw"></i> <span class="hide-menu">Signed Letter-head Request</span></a> </li>
                                @endif

                                <?php
                                $pro_sign_letter_full_view_permission = Permissions::checkPermission(28, 5);
                                $pro_sign_letter_partial_view_permission = Permissions::checkPermission(28, 6);
                                $pro_sign_letter_my_view_permission = Permissions::checkPermission(28, 1);
                                ?>
                                @if($pro_sign_letter_full_view_permission || $pro_sign_letter_partial_view_permission || $pro_sign_letter_my_view_permission)
                                <li> <a href="{{ route('admin.pro_sign_letter') }}" class="waves-effect"><i class="fa fa-pencil fa-fw"></i> <span class="hide-menu">Blank Letter Head Request</span></a> </li>
                                <hr>
                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.ASSISTANT'))
                                <li> <a href="{{ route('admin.pre_sign_letter_list') }}" class="waves-effect"><i class="icon-briefcase"></i> <span class="hide-menu">Approve Signed Letter-head Request</span></a> </li>
                                @endif

                                @if(Auth::user()->role==config('constants.ASSISTANT'))
                                <li> <a href="{{ route('admin.pro_sign_letter_list') }}" class="waves-effect"><i class="icon-briefcase"></i> <span class="hide-menu">Approve Blank Letter Head Request</span></a> </li>
                                <hr>
                                @endif

                                <?php
                                $letter_head_delivery_full_view_permission = Permissions::checkPermission(29, 5);
                                $letter_head_delivery_edit_permission = Permissions::checkPermission(29, 2);
                                ?>
                                @if(($letter_head_delivery_full_view_permission || $letter_head_delivery_edit_permission) && Auth::user()->role!=config('constants.SuperUser'))
                                <li> <a href="{{ route('admin.letter_head_delivery') }}" class="waves-effect"><i data-icon="a" class="fa fa-pencil fa-fw"></i> <span class="hide-menu">Letter Head Delivery</span></a> </li>
                                <hr>
                                @endif
                                
                                @if(Auth::user()->role==config('constants.ACCOUNT_ROLE') || Auth::user()->role==config('constants.SuperUser'))
                                <li> <a href="{{ route('admin.approved_letter_head_report') }}" class="waves-effect"><i data-icon="a" class="fa fa-pencil fa-fw"></i> <span class="hide-menu">Approved Letter Head Report</span></a> </li>
                                @endif
                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                                <hr>
                                    <li> <a href="{{ route('admin.blank_letter_head_list') }}" class="waves-effect"><i data-icon="a" class="fa fa-credit-card fa-fw"></i> <span class="hide-menu">Blank Letter Head</span></a> </li>
                                    <li> <a href="{{ route('admin.signed_letter_head_list') }}" class="waves-effect"><i data-icon="a" class="fa fa-credit-card fa-fw"></i> <span class="hide-menu">Signed Letter Head</span></a> </li>
                                    <li> <a href="{{ route('admin.signed_letter_head_approval') }}" class="waves-effect"><i data-icon="a" class="fa fa-credit-card fa-fw"></i> <span class="hide-menu">Signed Letter Head Approval</span></a> </li>
                                    <li> <a href="{{ route('admin.used_letter_head_list') }}" class="waves-effect"><i data-icon="a" class="fa fa-credit-card fa-fw"></i> <span class="hide-menu">Used Letter Head</span></a> </li>
                                    <li> <a href="{{ route('admin.failed_letter_head_list') }}" class="waves-effect"><i data-icon="a" class="fa fa-credit-card fa-fw"></i> <span class="hide-menu">Failed Letter Head</span></a> </li>
                                @endif
                            </ul>
                        </li>
                        
                        
                        <li><a href="#" class="waves-effect"><i class="fa fa-file" data-icon="v"></i> <span class="hide-menu">Hard Copy Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                               <?php
                                $hard_copy_rack_full_view_permission = Permissions::checkPermission(50, 5);
                                
                                ?>
                                @if($hard_copy_rack_full_view_permission)
								<li> <a href="{{ route('admin.hardcopy_cupboard') }}" class="waves-effect"><i class="fa fa-bars"></i> <span class="hide-menu">Hard Copy Cupboard</span></a> </li>
                                <li> <a href="{{ route('admin.hardcopy_reck') }}" class="waves-effect"><i data-icon="a" class="fa fa-cubes"></i> <span class="hide-menu">Hard Copy Department Rack</span></a> </li>
                                @endif
                                
                                <?php
                                $hard_copy_folder_full_view_permission = Permissions::checkPermission(51, 5);
                                
                                ?>
                                @if($hard_copy_folder_full_view_permission)
                                <li> <a href="{{ route('admin.hardcopy_folder') }}" class="waves-effect"><i data-icon="a" class="fa fa-folder-open"></i> <span class="hide-menu">Hard Copy File Folder</span></a> </li>
                                @endif
                                
                                <?php
                                $hard_copy_full_view_permission = Permissions::checkPermission(52, 5);
                                
                                ?>
                                @if($hard_copy_full_view_permission)
                                <li> <a href="{{ route('admin.hardcopy') }}" class="waves-effect"><i data-icon="a" class="fa fa-file-text"></i> <span class="hide-menu">Document Hard Copy</span></a> </li>
                                @endif
                            </ul>
                        </li>
                        
                        @if(Auth::user()->role==7)
                        <li> <a href="{{ route('admin.generate_form_16') }}" class="waves-effect"><i class="icon-doc"></i> <span class="hide-menu">Employee Form 16</span></a> </li>
                        @endif
                        <?php
                        $announcements_full_view_permission = Permissions::checkPermission(23, 5);
                        $announcements_partial_view_permission = Permissions::checkPermission(23, 6);
                        $announcements_my_view_permission = Permissions::checkPermission(23, 1);
                        ?>
                        @if($announcements_full_view_permission || $announcements_partial_view_permission || $announcements_my_view_permission)
                        <li> <a href="{{ route('admin.announcements') }}" class="waves-effect"><i data-icon="a" class="ti-announcement"></i> <span class="hide-menu">Announcements</span></a> </li>
                        @endif

                        <?php
                        $resign_full_view_permission = Permissions::checkPermission(18, 5);
                        $resign_partial_view_permission = Permissions::checkPermission(18, 6);
                        $resign_my_view_permission = Permissions::checkPermission(18, 1);
                        ?>
                        @if($resign_full_view_permission || $resign_partial_view_permission || $resign_my_view_permission)
                        <li> <a href="{{ route('admin.resign') }}" class="waves-effect"><i data-icon="a" class="icon-compass fa-fw"></i> <span class="hide-menu">Resignation</span></a> </li>
                        @endif

                        @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.Admin') || Auth::user()->role==config('constants.REAL_HR'))
                        <li> <a href="{{ route('admin.attendance_report') }}" class="waves-effect"><i class="fa fa-gears"></i> <span class="hide-menu">Report</span></a> </li>
                        @endif
						
						<?php
                        $rtgs_register_full_view_permission = Permissions::checkPermission(54, 5);
                        
                        ?>
                        <!-- @if($rtgs_register_full_view_permission)
                        <li> <a href="{{ route('admin.rtgs_register') }}" class="waves-effect"><i class="fa fa-file-text"></i> <span class="hide-menu">RTGS Register</span></a> </li>
					<li> <a href="{{ route('admin.rtgs_use_report') }}" class="waves-effect"><i class="fa fa-file-excel-o"></i> <span class="hide-menu">RTGS Use Report</span></a> </li>
                        @endif -->


                       <!--  <li><a href="#" class="waves-effect"><i class="fa fa-university" aria-hidden="true"></i> <span class="hide-menu">Cheque Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">

                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                                <li> <a href="{{ route('admin.cheque_register') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw"></i> <span class="hide-menu">Cheque Register</span></a> </li>
                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                                <li> <a href="{{ route('admin.rtgs_neft_register') }}" class="waves-effect"><i class="fa fa-university" aria-hidden="true"></i> <span class="hide-menu">NEFT/RTGS management</span></a> </li>
                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                                <li> <a href="{{ route('admin.general_register') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw"></i> <span class="hide-menu">General Cheque use</span></a> </li>
                                @endif

                                @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.ASSISTANT') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                                <li> <a href="{{ route('admin.cheque_use_report') }}" class="waves-effect"><i class="fa fa-gears"></i> <span class="hide-menu">Cheque use Report</span></a> </li>
                                @endif
								
								@if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.ASSISTANT') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                                <li> <a href="{{ route('admin.cheque_stats_report') }}" class="waves-effect"><i class="fa fa-file-excel-o"></i> <span class="hide-menu">Cheque Stats Report</span></a> </li>
                                @endif

                            </ul>
                        </li> -->


                        <!-- New Cheuqe menu -->
                        @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                        <li><a href="#" class="waves-effect"><i class="fa fa-university" aria-hidden="true"></i> <span class="hide-menu">Cheque Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <li> <a href="{{ route('admin.blank_cheque_list') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw"></i> <span class="hide-menu">Blank Cheque</span></a> </li>
                                <li> <a href="{{ route('admin.signed_cheque_list') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw"></i> <span class="hide-menu">Signed Cheque</span></a> </li>
                                <li> <a href="{{ route('admin.signed_approval_requests') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw"></i> <span class="hide-menu">Signed Cheque Approval</span></a> </li>
                                <li> <a href="{{ route('admin.used_cheque_list') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw" aria-hidden="true"></i> <span class="hide-menu">Used Cheque </span></a> </li>
                                <li> <a href="{{ route('admin.failed_cheque_list') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw"></i> <span class="hide-menu">Failed Cheque</span></a> </li>
                            </ul>
                        </li>
                        @endif
                        <!-- / -->
                        <!-- New Rtgs menu -->
                        @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.REAL_HR') || Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                        <li><a href="#" class="waves-effect"><i class="fa fa-university" aria-hidden="true"></i><i class="fa fa-refresh" aria-hidden="true"></i><i class="fa fa-university" aria-hidden="true"></i><span class="hide-menu"> RTGS Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <li> <a href="{{ route('admin.blank_rtgs_list') }}" class="waves-effect"><i class="fa fa-refresh fa-fw"></i> <span class="hide-menu">Blank RTGS</span></a> </li>
                                <li> <a href="{{ route('admin.signed_rtgs_list') }}" class="waves-effect"><i class="fa fa-refresh fa-fw"></i> <span class="hide-menu">Signed RTGS</span></a> </li>
                                <li> <a href="{{ route('admin.signed_rtgs_approval_requests') }}" class="waves-effect"><i class="fa fa-refresh fa-fw"></i> <span class="hide-menu">Signed RTGS Approval</span></a> </li>
                                <li> <a href="{{ route('admin.used_rtgs_list') }}" class="waves-effect"><i class="fa fa-refresh fa-fw"></i> <span class="hide-menu">Used RTGS</span></a> </li>
                                <li> <a href="{{ route('admin.failed_rtgs_list') }}" class="waves-effect"><i class="fa fa-refresh fa-fw"></i> <span class="hide-menu">Failed RTGS</span></a> </li>                                
                            </ul>
                        </li>
                        @endif
                        <!-- / -->
                        

                        <!-- @if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.REAL_HR'))
                        <li> <a href="{{ route('admin.letter_head_register') }}" class="waves-effect"><i class="fa fa-credit-card-alt fa-fw"></i> <span class="hide-menu">Letter Head Register</span></a> </li>
                        @endif -->

                        <?php
                        $trip_my_view_permission = Permissions::checkPermission(37, 1);
                        $trip_edit_view_permission = Permissions::checkPermission(37, 2);
                        $trip_add_view_permission = Permissions::checkPermission(37, 3);
                        $trip_delete_view_permission = Permissions::checkPermission(37, 4);
                        $trip_full_view_permission = Permissions::checkPermission(37, 5);
                        $trip_partial_view_permission = Permissions::checkPermission(37, 6);
                        ?>
                        <li><a href="#" class="waves-effect"><i class="fa fa-car" aria-hidden="true"></i> <span class="hide-menu">Trip Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                @if($trip_my_view_permission && Auth::user()->role==config('constants.DRIVER'))
                                <li> <a href="{{ route('admin.vehicle_trip') }}" class="waves-effect"><i class="fa fa-modx fa-fw"></i> <span class="hide-menu">Add Vehicle Trip</span></a> </li>
                                @endif

                                <li> <a href="{{ route('admin.close_trip_index') }}" class="waves-effect"><i class="fa fa-reddit-alien fa-fw"></i> <span class="hide-menu">Trip Approval</span></a> </li>

                                @if($trip_full_view_permission)
                                <li> <a href="{{ route('admin.vehicle_trip_list_report') }}" class="waves-effect"><i class="fa fa-car fa-fw"></i> <span class="hide-menu">Vehicle Trip Report</span></a> </li>
                                @endif
                            </ul>
                        </li>
                        <?php
                        $registry_view_permission = Permissions::checkPermission(40, 1);

                        $registry_full_view_permission = Permissions::checkPermission(40, 5);
                        $registry_doc_full_view_permission = Permissions::checkPermission(39, 5);
                        ?>
                        <li><a href="#" class="waves-effect"><i class="fa fa-folder" aria-hidden="true"></i> <span class="hide-menu">Registry Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                @if($registry_view_permission || $registry_full_view_permission)
                                <li> <a href="{{ route('admin.inward_outward') }}" class="waves-effect"><i class="fa fa fa-indent"></i> <span class="hide-menu">Inwards/Outwards</span></a> </li>
                                @endif
                                @if($registry_doc_full_view_permission)
                                <li> <a href="{{ route('admin.document_category') }}" class="waves-effect"><i class="fa fa fa-list"></i> <span class="hide-menu">Inward/Outward Document Category</span></a> </li>
                                @endif
                                @if($registry_doc_full_view_permission)
                                <li> <a href="{{ route('admin.document_sub_category') }}" class="waves-effect"><i class="fa fa fa-list"></i> <span class="hide-menu">Inward/Outward Document Sub Category</span></a> </li>
                                @endif
								
								@if($registry_full_view_permission)
                                <li> <a href="{{ route('admin.delivery_mode') }}" class="waves-effect"><i class="fa fa fa-list"></i> <span class="hide-menu">Delivery Mode Category</span></a> </li>
                                <li> <a href="{{ route('admin.sender') }}" class="waves-effect"><i class="fa fa fa-list"></i> <span class="hide-menu">Sender Category</span></a> </li>
                                @endif
								
                            </ul> 
                        </li>
						<?php
                        $tender_full_view_permission = Permissions::checkPermission(57, 5);

                        ?>
						@if($tender_full_view_permission)
						<li> <a href="#" class="waves-effect"><i class="fa fa-gavel" data-icon="v"></i> <span class="hide-menu">Tender Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <li> <a href="{{ route('admin.tender_category') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Tender Category Types</span></a> </li>
                                <li> <a href="{{ route('admin.tender_pattern') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Tender Pattern Types</span></a> </li>
                                <li> <a href="{{ route('admin.tender_physical_submission') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Tender Physical-Submission Types</span></a> </li>
                                
                                <li> <a href="{{ route('admin.tender') }}" class="waves-effect"><i class="fa fa-gavel"></i> <span class="hide-menu">Daily Tender Report</span></a> </li>
                                <li> <a href="{{ route('admin.selected_tender') }}" class="waves-effect"><i class="fa fa-check"></i> <span class="hide-menu">Selected Tender Report</span></a> </li>
                                <li> <a href="{{ route('admin.pre_bid_query_report') }}" class="waves-effect"><i class="fa fa-calendar"></i> <span class="hide-menu">Pre-bid Meeting Tender Report</span></a> </li>
                                <li> <a href="{{ route('admin.tender_submission') }}" class="waves-effect"><i class="fa fa-send"></i> <span class="hide-menu">Tender Submission Report</span></a> </li>
                                <li> <a href="{{ route('admin.tender_opening_report') }}" class="waves-effect"><i class="fa fa-envelope-open"></i> <span class="hide-menu">Tender Opening Report</span></a> </li>
								@if(Auth::user()->role==config('constants.SuperUser') || Auth::user()->role==config('constants.Admin'))
                                <li> <a href="{{ route('admin.tender_permission') }}" class="waves-effect"><i class="fa fa-lock"></i> <span class="hide-menu">Tender Permission</span></a> </li>
								@endif
							</ul>
                        </li>
						@endif
						
						<?php
						
                        $site_manage_full_view_permission = Permissions::checkPermission(59, 5);

                        ?>
						@if($site_manage_full_view_permission)
						<li> 
					<a href="#" class="waves-effect"><i class="fa fa-gavel" data-icon="v"></i> <span class="hide-menu">Site Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <li> <a href="{{ route('admin.site_management') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Site Management BOQ</span></a> </li>
								<li> <a href="{{ route('admin.boq_design') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">BOQ Design Blcok</span></a> </li>
                                <li> <a href="{{ route('admin.daily_abstract') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Daily Abstract Report</span></a> </li>
								<li> <a href="{{ route('admin.site_report') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Abstract Sheet</span></a> </li>
								<li> <a href="{{ route('admin.generate_bill_invoice') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Generate Invoice</span></a> </li>
								<li> <a href="{{ route('admin.excess_saving') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Excess Saving Report</span></a> </li>
							</ul>
                        </li>
						@endif
						<!--<li><a href="#" class="waves-effect"><i class="icon-people fa-fw" data-icon="v"></i> <span class="hide-menu">Site Management<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
								<li> <a href="{{ route('admin.site_management') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">BOQ</span></a> </li>
                                <li> <a href="{{ route('admin.daily_abstract') }}" class="waves-effect"><i class="fa fa-file"></i> <span class="hide-menu">Abstract Report</span></a> </li>
                                
                            </ul>
                        </li>-->
                        <!--<li><a href="{{ route('admin.travel') }}" class="waves-effect"><i class="fa fa-plane"></i> <span class="hide-menu">Travel Managment</span></a> </li>-->


<!-- <li> <a href="{{ route('admin.projects') }}" class="waves-effect"><i class="icon-people fa-fw"></i> <span class="hide-menu">Projects</span></a> </li>
<li> <a href="{{ route('admin.vendors') }}" class="waves-effect"><i class="icon-people fa-fw"></i> <span class="hide-menu">Vendors</span></a> </li> -->
<!-- <li> <a href="{{ route('admin.employees') }}" class="waves-effect"><i class="icon-user"></i> <span class="hide-menu">Employee</span></a> </li> -->

<!-- <li> <a href="{{ route('admin.banks') }}" class="waves-effect"><i class="fa fa-bank"></i> <span class="hide-menu">Bank</span></a> </li> -->
<!-- <li> <a href="{{ route('admin.import_csv') }}" class="waves-effect"><i class="icon-rocket fa-fw"></i> <span class="hide-menu">Import Bank Transactions</span></a> </li> -->
<!-- <li> <a href="{{ route('admin.transactions') }}" class="waves-effect"><i class="icon-wallet"></i> <span class="hide-menu">Bank Transactions</span></a> </li> -->
<!-- <li> <a href="{{ route('admin.heads') }}" class="waves-effect"><i class="icon-magnet fa-fw"></i> <span class="hide-menu">Heads</span></a> </li> -->
<!-- <li> <a href="{{ route('admin.bills') }}" class="waves-effect"><i class="icon-notebook"></i> <span class="hide-menu">Bills</span></a> </li> -->
                    </ul>
                </div>
            </div>
            <!-- Left navbar-header end -->
            <!-- Page Content -->
            <div id="page-wrapper">
                @yield('content')
                <!-- /.container-fluid -->
                <footer class="footer text-center"> {{ date('Y') }} &copy; {{ $setting_details[0]->setting_value }}, Developed By <a target="_blank" href="https://wappnet.com">Wappnet Systems</a> </footer>
            </div>
            <!-- /#page-wrapper -->
        </div>
        <!-- /#wrapper -->
        <!-- jQuery -->
        <script src="{{asset('admin_asset/assets/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="{{asset('admin_asset/assets/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <!-- Menu Plugin JavaScript -->
        <script src="{{asset('admin_asset/assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
        <!--slimscroll JavaScript -->
        <script src="{{asset('admin_asset/assets/js/jquery.slimscroll.js') }}"></script>
        <!--Wave Effects -->
        <script src="{{asset('admin_asset/assets/js/waves.js') }}"></script>
        <!--Counter js -->
        <script src="{{asset('admin_asset/assets/plugins/bower_components/waypoints/lib/jquery.waypoints.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/counterup/jquery.counterup.min.js') }}"></script>
        <!--Image Popup js -->
        <script src="{{asset('admin_asset/assets/plugins/bower_components/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}"></script>
        <!--Morris JavaScript -->
        <script src="{{asset('assets/plugins/bower_components/raphael/raphael-min.js') }}"></script>
        <!--<script src="{{asset('assets/plugins/bower_components/morrisjs/morris.js') }}"></script>-->
        <!-- Custom Theme JavaScript -->
        <script src="{{asset('admin_asset/assets/js/custom.min.js') }}"></script>
        <!--<script src="{{asset('assets/js/dashboard1.js') }}"></script>-->
        <!-- Sparkline chart JavaScript -->
        <script src="{{asset('admin_asset/assets/plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/jquery-sparkline/jquery.charts-sparkline.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

        <!--Style Switcher -->
        <script src="{{asset('admin_asset/assets/plugins/bower_components/styleswitcher/jQuery.style.switcher.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
        <script src="{{asset('admin_asset/assets/js/validate.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>

        <script src="{{asset('admin_asset/assets/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
        <script type="text/javascript" src="{{asset('admin_asset/assets/plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
        <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/moment/moment.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
        <!-- Date Picker Plugin JavaScript -->
        <script src="{{asset('admin_asset/assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <!-- Date range Plugin JavaScript -->
        <script src="{{asset('admin_asset/assets/plugins/bower_components/timepicker/bootstrap-timepicker.min.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/dropify/dist/js/dropify.min.js') }}"></script>

        <script src="{{asset('admin_asset/assets/plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

        <!-- start - This is for export functionality only -->
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script type="text/javascript" src="{{asset('admin_asset/assets/plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') }}"></script>
        <script src="{{asset('admin_asset/assets/plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') }}"></script>
        <script type="text/javascript" src="{{asset('admin_asset/assets/plugins/bower_components/fancybox/ekko-lightbox.min.js') }}"></script>
        <script>
                                    function magnify(imgID, zoom) {
                                        var img, glass, w, h, bw;
                                        img = document.getElementById(imgID);
                                        /*create magnifier glass:*/
                                        glass = document.createElement("DIV");
                                        glass.setAttribute("class", "img-magnifier-glass");
                                        /*insert magnifier glass:*/
                                        img.parentElement.insertBefore(glass, img);
                                        /*set background properties for the magnifier glass:*/
                                        glass.style.backgroundImage = "url('" + img.src + "')";
                                        glass.style.backgroundRepeat = "no-repeat";
                                        glass.style.backgroundSize = (img.width * zoom) + "px " + (img.height * zoom) + "px";
                                        bw = 3;
                                        w = glass.offsetWidth / 2;
                                        h = glass.offsetHeight / 2;
                                        /*execute a function when someone moves the magnifier glass over the image:*/
                                        glass.addEventListener("mousemove", moveMagnifier);
                                        img.addEventListener("mousemove", moveMagnifier);
                                        /*and also for touch screens:*/
                                        glass.addEventListener("touchmove", moveMagnifier);
                                        img.addEventListener("touchmove", moveMagnifier);
                                        function moveMagnifier(e) {
                                            var pos, x, y;
                                            /*prevent any other actions that may occur when moving over the image*/
                                            e.preventDefault();
                                            /*get the cursor's x and y positions:*/
                                            pos = getCursorPos(e);
                                            x = pos.x;
                                            y = pos.y;
                                            /*prevent the magnifier glass from being positioned outside the image:*/
                                            if (x > img.width - (w / zoom)) {
                                                x = img.width - (w / zoom);
                                            }
                                            if (x < w / zoom) {
                                                x = w / zoom;
                                            }
                                            if (y > img.height - (h / zoom)) {
                                                y = img.height - (h / zoom);
                                            }
                                            if (y < h / zoom) {
                                                y = h / zoom;
                                            }
                                            /*set the position of the magnifier glass:*/
                                            glass.style.left = (x - w) + "px";
                                            glass.style.top = (y - h) + "px";
                                            /*display what the magnifier glass "sees":*/
                                            glass.style.backgroundPosition = "-" + ((x * zoom) - w + bw) + "px -" + ((y * zoom) - h + bw) + "px";
                                        }
                                        function getCursorPos(e) {
                                            var a, x = 0, y = 0;
                                            e = e || window.event;
                                            /*get the x and y positions of the image:*/
                                            a = img.getBoundingClientRect();
                                            /*calculate the cursor's x and y coordinates, relative to the image:*/
                                            x = e.pageX - a.left;
                                            y = e.pageY - a.top;
                                            /*consider any page scrolling:*/
                                            x = x - window.pageXOffset;
                                            y = y - window.pageYOffset;
                                            return {x: x, y: y};
                                        }
                                    }
        </script>
        @yield('script')
        <style type="text/css">
            #side-menu ul > li > a.active {
                color: #8a45a4 !important;
            }   
        </style>
        <style>
            * {box-sizing: border-box;}

            .img-magnifier-container {
                position:relative;
            }

            .img-magnifier-glass {
                position: absolute;
                border: 3px solid #000;
                border-radius: 50%;
                cursor: none;
                /*Set the size of the magnifier glass:*/
                width: 150px;
                height: 150px;
            }
        </style>
        
    </body>
</html>

