<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Auth::routes();
Route::get('/todayPunchInCron', 'SiteParseController@todayPunchInCron')->name('todayPunchInCron');
Route::get('/todayPunchOutCron', 'SiteParseController@todayPunchOutCron')->name('todayPunchOutCron');
Route::any('/offline_chat_notify', 'SiteParseController@offline_chat_notify')->name('offline_chat_notify');
Route::get('/leaveCron', 'SiteParseController@leaveCron')->name('leaveCron');
Route::get('/holidayCron', 'SiteParseController@holidayCron')->name('holidayCron');
Route::get('/weekendCron', 'SiteParseController@weekendCron')->name('weekendCron');
Route::get('/attendanceCron', 'SiteParseController@attendanceCron')->name('attendanceCron');
Route::get('/punchHoursCron', 'SiteParseController@punchHoursCron')->name('punchHoursCron');
Route::get('/yearlyLeaveBalance', 'SiteParseController@yearlyLeaveBalance')->name('yearlyLeaveBalance');
Route::get('/monthlyLeaveBalance', 'SiteParseController@monthlyLeaveBalance')->name('monthlyLeaveBalance');
Route::get('/shortLeaveBalance', 'SiteParseController@shortLeaveBalance')->name('shortLeaveBalance');
Route::get('/check_update_probation_period', 'SiteParseController@check_update_probation_period')->name('check_update_probation_period');
Route::get('/long_leave_notify', 'SiteParseController@long_leave_notify')->name('long_leave_notify');
Route::get('/spacial_day_notify', 'SiteParseController@spacial_day_notify')->name('spacial_day_notify');
Route::get('/payroll', 'SiteParseController@payroll')->name('payroll');
Route::get('/payroll_deduction', 'SiteParseController@payroll_deduction')->name('payroll_deduction');
Route::get('/general_notification', 'SiteParseController@general_notification')->name('general_notification');
Route::get('/addEarnedLeave', 'SiteParseController@addEarnedLeave')->name('addEarnedLeave');
Route::get('/releaseEarnedLeave', 'SiteParseController@releaseEarnedLeave')->name('releaseEarnedLeave');
Route::get('/inward_outward_due_remider', 'SiteParseController@inward_outward_due_remider')->name('inward_outward_due_remider');
Route::get('/suspendUser', 'SiteParseController@suspendUser')->name('suspendUser');
Route::get('/late_leave_approval', 'SiteParseController@late_leave_approval')->name('late_leave_approval');
Route::get('/calculate_sandwich', 'SiteParseController@calculate_sandwich')->name('calculate_sandwich');
Route::get('/execute_expense', 'SiteParseController@execute_expense')->name('execute_expense');
Route::get('/pass_to_process_inward', 'SiteParseController@pass_to_process_inward');
Route::get('/cron_insExpiredlefdays', 'SiteParseController@cron_insExpiredlefdays')->name('cron_insExpiredlefdays');
Route::get('/cron_insExpired', 'SiteParseController@cron_insExpired')->name('cron_insExpired');
Route::get('/auto_punch_out', 'SiteParseController@auto_punch_out')->name('auto_punch_out');
Route::get('/auto_punch_in', 'SiteParseController@auto_punch_in')->name('auto_punch_in');
Route::get('/expire_remote_attend_request', 'SiteParseController@expire_remote_attend_request')->name('expire_remote_attend_request');
Route::get('/auto_policy_approve', 'SiteParseController@auto_policy_approve')->name('auto_policy_approve');
Route::get('/update_letterhead_numbers', 'StatusUpdateController@update_letterhead_numbers')->name('update_letterhead_numbers');
//Tender Opening Notifiy 
Route::get('/preliminary_opening_tender_notify', 'SiteParseController@preliminary_opening_tender_notify')->name('preliminary_opening_tender_notify');
Route::get('/technical_opening_tender_notify', 'SiteParseController@technical_opening_tender_notify')->name('technical_opening_tender_notify');
Route::get('/financial_opening_tender_notify', 'SiteParseController@financial_opening_tender_notify')->name('financial_opening_tender_notify');
Route::get('/commercial_opening_tender_notify', 'SiteParseController@commercial_opening_tender_notify')->name('commercial_opening_tender_notify');
//15/06/2020

Route::get('/cron_remainingBlankCheque', 'SiteParseController@cron_remainingBlankCheque')->name('cron_remainingBlankCheque');
Route::get('/cron_remainingSignedCheque', 'SiteParseController@cron_remainingSignedCheque')->name('cron_remainingSignedCheque');
Route::get('/cron_remainingBlankRtgs', 'SiteParseController@cron_remainingBlankRtgs')->name('cron_remainingBlankRtgs');
Route::get('/cron_remainingSignedRtgs', 'SiteParseController@cron_remainingSignedRtgs')->name('cron_remainingSignedRtgs');
Route::get('/add_unattended_sandwich', 'SiteParseController@add_unattended_sandwich')->name('add_unattended_sandwich');


//19/06/2020
Route::get('/cron_registry_emp_on_leave', 'SiteParseController@cron_registry_emp_on_leave')->name('cron_registry_emp_on_leave');

//14/07/2020
Route::get('/cron_ComplianceReminder', 'SiteParseController@cron_ComplianceReminder')->name('cron_ComplianceReminder');
Route::get('/cron_ComplianceReminderNotify', 'SiteParseController@cron_ComplianceReminderNotify')->name('cron_ComplianceReminderNotify');

//StatusUpdateController methods
Route::get('/cron_pending_leave_notify', 'StatusUpdateController@cron_pending_leave_notify')->name('cron_pending_leave_notify');
Route::get('/cron_pending_relieving_leave_notify', 'StatusUpdateController@cron_pending_relieving_leave_notify')->name('cron_pending_relieving_leave_notify');
Route::get('/cron_cash_payment_status_notify', 'StatusUpdateController@cron_cash_payment_status_notify')->name('cron_cash_payment_status_notify');
Route::get('/cron_bank_payment_status_notify', 'StatusUpdateController@cron_bank_payment_status_notify')->name('cron_bank_payment_status_notify');
Route::get('/cron_online_payment_status_notify', 'StatusUpdateController@cron_online_payment_status_notify')->name('cron_online_payment_status_notify');
Route::get('/cron_employee_expense_status_notify', 'StatusUpdateController@cron_employee_expense_status_notify')->name('cron_employee_expense_status_notify');
Route::get('/cron_pending_attendance', 'StatusUpdateController@cron_pending_attendance')->name('cron_pending_attendance');
Route::get('/cron_pending_driver_expense', 'StatusUpdateController@cron_pending_driver_expense')->name('cron_pending_driver_expense');


// User activity log
Route::get('/cron_user_activity_email', 'SiteParseController@cron_user_activity_email')->name('cron_user_activity_email');

// cron resign day count
Route::get('/cron_resign_day_count','SiteParseController@cron_resign_day_count')->name('cron_resign_day_count');

// Generate Payload
Route::get('/cron_day_payroll_generate', 'SiteParseController@cron_day_payroll_generate')->name('cron_day_payroll_generate');


// Cash, Bank, Online daily report email
Route::get('/cron_daily_payment_email_report', 'SiteParseController@cron_daily_payment_email_report')->name('cron_daily_payment_email_report');

//cash temps setup recently added
Route::get('/cron_daily_cash_payment_email_report', 'SiteParseController@cron_daily_cash_payment_email_report')->name('cron_daily_cash_payment_email_report');

//bank temps setup recently added
Route::get('/cron_daily_bank_payment_email_report', 'SiteParseController@cron_daily_bank_payment_email_report')->name('cron_daily_bank_payment_email_report');

//online temps setup recently added 
Route::get('/cron_daily_online_payment_email_report', 'SiteParseController@cron_daily_online_payment_email_report')->name('cron_daily_online_payment_email_report');

//Employee current leave  temps setup recently added 
// cron_daily_employee_leave_report
Route::get('/cron_daily_employee_leave_report', 'SiteParseController@cron_daily_employee_leave_report')->name('cron_daily_employee_leave_report');

// Controllers Within The "App\Http\Controllers\Admin" Namespace
Route::name('admin.')->namespace('Admin')->middleware(['ipcheck'])->group(function () {



    //methods of LoginController
    Route::get('/', 'LoginController@login')->name('login');
    Route::post('/authenticate', 'LoginController@authenticate')->name('authenticate');

    Route::get('/reset_password', 'LoginController@reset_password')->name('reset_password');
    Route::get('/guestLogin', 'InterviewController@guestLogin')->name('guestLogin');
    Route::post('/guestLoginAuth', 'InterviewController@guestLoginAuth')->name('guestLoginAuth');
    Route::get('/confirm_interview_form', 'InterviewController@confirm_interview_form')->name('confirm_interview_form');
    Route::post('/emp_confirm_interview', 'InterviewController@emp_confirm_interview')->name('emp_confirm_interview');

    Route::get('/hide_show_announcement', 'AnnouncementsController@hide_show_announcement')->name('hide_show_announcement');
    
    

    Route::group(['middleware' => 'auth'], function () {

        //methods of ChatController
        Route::get('/chat', 'ChatController@chat')->name('chat');
        Route::get('/get_contact_list/{keyword?}', 'ChatController@get_contact_list')->name('get_contact_list');
        Route::post('/get_chat_msg', 'ChatController@get_chat_msg')->name('get_chat_msg');
        Route::post('/chat_file_upload', 'ChatController@chat_file_upload')->name('chat_file_upload');
        Route::get('/createChatRoom', 'ChatController@createChatRoom')->name('createChatRoom');

        //methods of DashboardController
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
        Route::get('/changepassword', 'DashboardController@changepassword')->name('changepassword');
        Route::post('/savepassword', 'DashboardController@savepassword')->name('savepassword');
        Route::get('/edit_profile', 'DashboardController@edit_profile')->name('edit_profile');
        Route::get('/profile', 'DashboardController@profile')->name('profile');
        Route::get('/education_document', 'DashboardController@education_document')->name('education_document');
        Route::get('/experience_document', 'DashboardController@experience_document')->name('experience_document');
        Route::get('/identity_document', 'DashboardController@identity_document')->name('identity_document');
        Route::get('/organization_chart', 'DashboardController@organization_chart')->name('organization_chart');
        //few new created from 04-2021
        Route::post('/dashboard_pro_sign_letter_list', 'DashboardController@dashboard_pro_sign_letter_list')->name('dashboard_pro_sign_letter_list');
        Route::post('/dashboard_get_softcopy_received_request', 'DashboardController@dashboard_get_softcopy_received_request')->name('dashboard_get_softcopy_received_request');
        Route::post('/dashboard_get_leave_reversal_request', 'DashboardController@dashboard_get_leave_reversal_request')->name('dashboard_get_leave_reversal_request');
        Route::post('/dashboard_get_work_assigned_leave', 'DashboardController@dashboard_get_work_assigned_leave')->name('dashboard_get_work_assigned_leave');

        //methods of SettingController
        Route::get('/setting', 'SettingController@index')->name('setting');
        Route::get('/setting/editsetting/{id}', 'SettingController@editsetting')->name('editsetting');
        Route::post('/setting/update', 'SettingController@update')->name('updatesetting');

        //methods of Email_formatController
        Route::get('/email_format', 'Email_formatController@index')->name('emailformat');
        Route::get('/email_format/editemail/{id}', 'Email_formatController@editemail')->name('editemail');
        Route::post('/email_format/update', 'Email_formatController@update')->name('updateemail');

        //methods of UserController
        Route::get('/users', 'UserController@index')->name('users');
        Route::get('/get_user_list', 'UserController@get_user_list')->name('get_user_list');
        Route::get('/change_status/{id}/{status}', 'UserController@change_status')->name('change_status');
        Route::get('/change_suspend_status/{id}/{status}', 'UserController@change_suspend_status')->name('change_suspend_status');
        Route::post('/change_attend_type', 'UserController@change_attend_type')->name('change_attend_type');
        Route::get('/add_user', 'UserController@add_user')->name('add_user');
        Route::get('/export_users', 'UserController@export_users')->name('export_users');
        Route::post('/insert_user', 'UserController@insert_user')->name('insert_user');
        Route::get('/edit_user/{id}', 'UserController@edit_user')->name('edit_user');
        Route::post('/update_user', 'UserController@update_user')->name('update_user');
        Route::post('/check_email', 'UserController@check_email')->name('check_email');
        Route::post('/check_emp_code', 'UserController@check_emp_code')->name('check_emp_code');
        Route::get('/view_user/{id}', 'UserController@view_user')->name('view_user');
        // loan_statement
        Route::get('/loan_statement/{id}', 'UserController@loan_statement')->name('loan_statement');
        Route::get('/selected_employee_loan_list', 'UserController@selected_employee_loan_list')->name('selected_employee_loan_list');
        Route::get('/delete_user/{id}', 'UserController@delete_user')->name('delete_user');
        Route::post('/edit_check_email', 'UserController@edit_check_email')->name('edit_check_email');
        Route::get('/upload_education/{id}', 'UserController@upload_education')->name('upload_education');
        Route::post('/insert_education_document', 'UserController@insert_education_document')->name('insert_education_document');
        Route::get('/delete_experience/{id}/{user_id}', 'UserController@delete_experience')->name('delete_experience');
        Route::get('/upload_experience/{id}', 'UserController@upload_experience')->name('upload_experience');
        Route::post('/insert_experience_document', 'UserController@insert_experience_document')->name('insert_experience_document');
        Route::get('/delete_education/{id}/{user_id}', 'UserController@delete_education')->name('delete_education');
        Route::get('/upload_identity/{id}', 'UserController@upload_identity')->name('upload_identity');
        Route::post('/insert_identity_document', 'UserController@insert_identity_document')->name('insert_identity_document');
        Route::post('/reliever_user', 'UserController@reliever_user')->name('reliever_user');
        Route::get('/get_device_user', 'UserController@get_device_user')->name('get_device_user');
        Route::get('/get_allow_user_list_all', 'UserController@get_allow_user_list_all')->name('get_allow_user_list_all');
        Route::get('/approved_device_user/{id}', 'UserController@approved_device_user')->name('approved_device_user');
        Route::get('/delete_device_user/{id}', 'UserController@delete_device_user')->name('delete_device_user');

        //methods of SubAdminController
        Route::get('/subadmins', 'SubAdminController@index')->name('subadmins');
        Route::get('/get_subadmin_list', 'SubAdminController@get_subadmin_list')->name('get_subadmin_list');
        Route::get('/admin_change_status/{id}/{status}', 'SubAdminController@admin_change_status')->name('admin_change_status');
        Route::get('/add_subadmin', 'SubAdminController@add_subadmin')->name('add_subadmin');
        Route::post('/insert_subadmin', 'SubAdminController@insert_subadmin')->name('insert_subadmin');
        Route::get('/edit_subadmin/{id}', 'SubAdminController@edit_subadmin')->name('edit_subadmin');
        Route::post('/update_subadmin', 'SubAdminController@update_subadmin')->name('update_subadmin');
        Route::post('/check_user_exists', 'SubAdminController@check_user_exists')->name('check_user_exists');

        //methods of CompanyController
        Route::get('/companies', 'CompanyController@index')->name('companies');
        Route::get('/get_company_list', 'CompanyController@get_company_list')->name('get_company_list');
        Route::get('/add_company', 'CompanyController@add_company')->name('add_company');
        Route::post('/insert_company', 'CompanyController@insert_company')->name('insert_company');
        Route::get('/change_company_status/{id}/{status}', 'CompanyController@change_company_status')->name('change_company_status');
        Route::get('/edit_company/{id}', 'CompanyController@edit_company')->name('edit_company');
        Route::post('/update_company', 'CompanyController@update_company')->name('update_company');
        Route::get('/delete_company/{id}', 'CompanyController@delete_company')->name('delete_company');
        Route::get('/cmp_document_list/{id}', 'CompanyController@cmp_document_list')->name('cmp_document_list');
        Route::post('/insert_company_document', 'CompanyController@insert_company_document')->name('insert_company_document');
        Route::get('/add_company_document/{id}', 'CompanyController@add_company_document')->name('add_company_document');
        Route::get('/delete_document/{id}/{company_id}', 'CompanyController@delete_document')->name('delete_document');
        Route::post('/get_company_crt_images', 'CompanyController@get_company_crt_images')->name('get_company_crt_images');

        //methods of ProjectController
        Route::get('/projects', 'ProjectController@index')->name('projects');
        Route::get('/project_update_request', 'ProjectController@project_update_request')->name('project_update_request');
        Route::get('/get_project_list_last', 'ProjectController@get_project_list_last')->name('get_project_list_last');
        Route::get('/get_project_list_all', 'ProjectController@get_project_list')->name('get_project_list_all');
        Route::get('/add_project', 'ProjectController@add_project')->name('add_project');
        Route::post('/insert_project', 'ProjectController@insert_project')->name('insert_project');
        Route::get('/change_project_status/{id}/{status}', 'ProjectController@change_project_status')->name('change_project_status');
        Route::get('/edit_project/{id}', 'ProjectController@edit_project')->name('edit_project');
        Route::post('/update_project', 'ProjectController@update_project')->name('update_project');
        Route::get('/delete_project/{id}', 'ProjectController@delete_project')->name('delete_project');
        Route::get('/delete_project_last/{id}', 'ProjectController@delete_project_last')->name('delete_project_last');
        Route::get('/approve_confirm_last/{id}', 'ProjectController@approve_confirm_last')->name('approve_confirm_last');
        Route::post('/get_projectlist_by_company', 'ProjectController@get_projectlist_by_company')->name('get_projectlist_by_company');
        Route::post('/get_project_managers', 'ProjectController@get_project_managers')->name('get_project_managers');
        Route::post('/checkProjectName', 'ProjectController@checkProjectName')->name('checkProjectName');
        Route::post('/checkEditProjectName', 'ProjectController@checkEditProjectName')->name('checkEditProjectName');

        //methods of VendorController
        Route::get('/vendors', 'VendorController@index')->name('vendors');
        Route::get('/get_vendor_list_all', 'VendorController@get_vendor_list')->name('get_vendor_list_all');
        Route::get('/add_vendor', 'VendorController@add_vendor')->name('add_vendor');
        Route::post('/insert_vendor', 'VendorController@insert_vendor')->name('insert_vendor');
        Route::get('/change_vendor_status/{id}/{status}', 'VendorController@change_vendor_status')->name('change_vendor_status');
        Route::get('/edit_vendor/{id}', 'VendorController@edit_vendor')->name('edit_vendor');
        Route::post('/update_vendor', 'VendorController@update_vendor')->name('update_vendor');
        Route::post('/get_vendorlist_by_company', 'VendorController@get_vendorlist_by_company')->name('get_vendorlist_by_company');
        Route::any('/check_uniquePancardNumber', 'VendorController@check_uniquePancardNumber')->name('check_uniquePancardNumber');

        //24/08/2020
        Route::any('/check_vender_name', 'VendorController@check_vender_name')->name('check_vender_name');
        Route::any('/vender_name_autosuggest', 'VendorController@vender_name_autosuggest')->name('vender_name_autosuggest');

        //methods of EmployeeController
        Route::get('/employees', 'EmployeeSalaryController@index')->name('employees');
        Route::get('/salary_slip', 'EmployeeSalaryController@salary_slip')->name('salary_slip');
        Route::get('/get_employee_list', 'EmployeeSalaryController@get_employee_list')->name('get_employee_list');
        Route::get('/add_employee', 'EmployeeSalaryController@add_employee')->name('add_employee');
        Route::post('/insert_employee', 'EmployeeSalaryController@insert_employee')->name('insert_employee');
        Route::get('/change_employee_status/{id}/{status}', 'EmployeeSalaryController@change_employee_status')->name('change_employee_status');
        Route::get('/edit_employee/{id}', 'EmployeeSalaryController@edit_employee')->name('edit_employee');
        Route::post('/update_employee', 'EmployeeSalaryController@update_employee')->name('update_employee');
        Route::get('/employee_salary', 'EmployeeSalaryController@employee_salary')->name('employee_salary');
        Route::post('/employee_salary_upload', 'EmployeeSalaryController@employee_salary_upload')->name('employee_salary_upload');
        Route::get('/employee_salary_format', 'EmployeeSalaryController@employee_salary_format')->name('employee_salary_format');
        Route::get('/employee_salary_list', 'EmployeeSalaryController@employee_salary_list')->name('employee_salary_list');
        Route::get('/add_employee_salary', 'EmployeeSalaryController@add_employee_salary')->name('add_employee_salary');
        Route::get('/edit_employee_salary/{id}', 'EmployeeSalaryController@edit_employee_salary')->name('edit_employee_salary');
        Route::post('/insert_employee_salary', 'EmployeeSalaryController@insert_employee_salary')->name('insert_employee_salary');
        Route::post('/update_employee_salary', 'EmployeeSalaryController@update_employee_salary')->name('update_employee_salary');
        Route::get('/delete_employee_salary/{id}', 'EmployeeSalaryController@delete_employee_salary')->name('delete_employee_salary');
        Route::get('/delete_employee/{id}', 'EmployeeSalaryController@delete_employee')->name('delete_employee');

        //methods of BankController
        Route::get('/banks', 'BankController@index')->name('banks');
        Route::get('/get_bank_list', 'BankController@get_bank_list')->name('get_bank_list');
        Route::get('/add_bank', 'BankController@add_bank')->name('add_bank');
        Route::post('/insert_bank', 'BankController@insert_bank')->name('insert_bank');
        Route::get('/change_bank_status_now/{id}/{status}', 'BankController@change_bank_status_now')->name('change_bank_status_now');
        Route::get('/edit_bank/{id}', 'BankController@edit_bank')->name('edit_bank');
        Route::post('/update_bank', 'BankController@update_bank')->name('update_bank');
        Route::get('/import_csv', 'BankController@import_csv')->name('import_csv');
        Route::post('/upload_bank_transactions', 'BankController@uploadBankTransactions')->name('upload_bank_transactions');
        Route::get('/transactions', 'BankController@get_transactions')->name('transactions');
        Route::get('/get_transaction_list', 'BankController@get_transaction_list')->name('get_transaction_list');
        Route::post('/get_bank_by_company', 'BankController@get_bank_by_company')->name('get_bank_by_company');
        Route::get('/delete_bank/{id}', 'BankController@delete_bank')->name('delete_bank');

        //methods of HeadController
        Route::get('/heads', 'HeadController@index')->name('heads');
        Route::get('/get_head_list', 'HeadController@get_head_list')->name('get_head_list');
        Route::get('/add_head', 'HeadController@add_head')->name('add_head');
        Route::post('/insert_head', 'HeadController@insert_head')->name('insert_head');
        Route::get('/change_head_status/{id}/{status}', 'HeadController@change_head_status')->name('change_head_status');
        Route::get('/edit_head/{id}', 'HeadController@edit_head')->name('edit_head');
        Route::post('/update_head', 'HeadController@update_head')->name('update_head');

        //methods of BillTransactionController
        Route::get('/bills', 'BillTransactionController@index')->name('bills');
        Route::get('/get_bill_list', 'BillTransactionController@get_bill_list')->name('get_bill_list');
        Route::get('/add_bill', 'BillTransactionController@add_bill')->name('add_bill');
        Route::post('/insert_bill', 'BillTransactionController@insert_bill')->name('insert_bill');
        Route::get('/change_bill_status/{id}/{status}', 'BillTransactionController@change_bill_status')->name('change_bill_status');
        Route::get('/edit_bill/{id}', 'BillTransactionController@edit_bill')->name('edit_bill');
        Route::post('/update_bill', 'BillTransactionController@update_bill')->name('update_bill');

        //methods of BankccController
        Route::get('/bankcc', 'BankccController@index')->name('bankcc');
        Route::get('/get_bankcc_list', 'BankccController@get_bankcc_list')->name('get_bankcc_list');
        Route::get('/edit_bankcc', 'BankccController@edit_bankcc')->name('edit_bankcc');
        Route::get('/add_bank_cc', 'BankccController@add_bank_cc')->name('add_bank_cc');
        Route::post('/insert_bankcc', 'BankccController@insert_bankcc')->name('insert_bankcc');

        //methods of RoleController
        Route::get('/roles', 'RoleController@index')->name('roles');
        Route::get('/getRole', 'RoleController@getRole')->name('getRole');
        Route::get('/addroles', 'RoleController@addroles')->name('addroles');
        Route::get('/editroles/{id}', 'RoleController@editroles')->name('editroles');
        Route::post('/updateroles', 'RoleController@updateroles')->name('updateroles');
        Route::post('/insertroles', 'RoleController@insertRole')->name('insertroles');
        Route::get('/deleteroles/{id}', 'RoleController@deleteroles')->name('deleteroles');
        Route::post('/check_uniqueRoleName', 'RoleController@check_uniqueRoleName')->name('check_uniqueRoleName');

        //methods of LeaveCategoryController
        Route::get('/leavecategory', 'LeaveCategoryController@index')->name('leavecategory');
        Route::get('/get_leavecatogry_list', 'LeaveCategoryController@get_leavecatogry_list')->name('get_leavecatogry_list');
        Route::get('/change_category_status/{id}/{status}', 'LeaveCategoryController@change_category_status')->name('change_category_status');
        Route::get('/edit_leavecategory/{id}', 'LeaveCategoryController@edit_leavecategory')->name('edit_leavecategory');
        Route::get('/add_leavecategory', 'LeaveCategoryController@add_leavecategory')->name('add_leavecategory');
        Route::post('/insert_leavecategory', 'LeaveCategoryController@insert_leavecategory')->name('insert_leavecategory');
        Route::post('/update_leavecategory', 'LeaveCategoryController@update_leavecategory')->name('update_leavecategory');


        //methods of LeaveController
        Route::get('/leave', 'LeaveController@index')->name('leave');
        Route::get('/get_my_leave_list', 'LeaveController@get_my_leave_list')->name('get_my_leave_list');
        Route::get('/edit_leave/{id}', 'LeaveController@edit_leave')->name('edit_leave');
        Route::get('/add_leave', 'LeaveController@add_leave')->name('add_leave');
        Route::post('/insert_leave', 'LeaveController@insert_leave')->name('insert_leave');
        Route::post('/update_leave', 'LeaveController@update_leave')->name('update_leave');
        Route::get('/cancel_leave/{id}', 'LeaveController@cancel_leave')->name('cancel_leave');
        Route::post('/reversal_leave', 'LeaveController@reversal_leave')->name('reversal_leave');
        Route::post('/get_today_leaves', 'LeaveController@get_today_leaves')->name('get_today_leaves');
        Route::get('/all_leave', 'LeaveController@all_leave')->name('all_leave');
        //  New sub module for Leave Reversal
        Route::get('/leave_reversal', 'LeaveController@leave_reversal')->name('leave_reversal');
        Route::get('/get_all_leave_list', 'LeaveController@get_all_leave_list')->name('get_all_leave_list');
        Route::get('/approve_leave/{id}', 'LeaveController@approve_leave')->name('approve_leave');
        Route::get('/reject_leave/{id}', 'LeaveController@reject_leave')->name('reject_leave');
        // new routes for reversal of the process fo leaves
        Route::get('/reverse_approve_leave/{id}', 'LeaveController@reverse_approve_leave')->name('reverse_approve_leave');
        Route::get('/reverse_reject_leave/{id}', 'LeaveController@reverse_reject_leave')->name('reverse_reject_leave');
        Route::post('/reject_update_leave', 'LeaveController@reject_update_leave')->name('reject_update_leave');
        Route::get('/relieving_request', 'LeaveController@relieving_request')->name('relieving_request');
        Route::get('/relival_change_status/{id}/{status}', 'LeaveController@relival_change_status')->name('relival_change_status');
        Route::post('/confirm_relieving', 'LeaveController@confirm_relieving')->name('confirm_relieving');
        Route::get('/get_leave_list_for_all', 'LeaveController@get_leave_list_for_all')->name('get_leave_list_for_all');
        Route::get('/get_leave_balance', 'LeaveController@get_leave_balance')->name('get_leave_balance');
        //methode of EmployeeBankController
        Route::get('/employee_bank', 'EmployeeBankController@employee_bank')->name('employee_bank');
        Route::get('/employee_bank_list', 'EmployeeBankController@employee_bank_list')->name('employee_bank_list');
        Route::get('/add_employee_bank', 'EmployeeBankController@add_employee_bank')->name('add_employee_bank');
        Route::get('/edit_employee_bank/{id}', 'EmployeeBankController@edit_employee_bank')->name('edit_employee_bank');
        Route::post('/insert_employee_bank', 'EmployeeBankController@insert_employee_bank')->name('insert_employee_bank');
        Route::post('/update_employee_bank', 'EmployeeBankController@update_employee_bank')->name('update_employee_bank');
        Route::get('/change_bank_status/{id}/{status}', 'EmployeeBankController@change_bank_status')->name('change_bank_status');
        Route::get('/delete_employee_bank/{id}', 'EmployeeBankController@delete_employee_bank')->name('delete_employee_bank');

        //methode of EmployeeLoanController
        Route::get('/employee_loan', 'EmployeeLoanController@employee_loan')->name('employee_loan');
        Route::get('/employee_loan_list', 'EmployeeLoanController@employee_loan_list')->name('employee_loan_list');
        Route::get('/add_employee_loan', 'EmployeeLoanController@add_employee_loan')->name('add_employee_loan');
        Route::get('/edit_employee_loan/{id}', 'EmployeeLoanController@edit_employee_loan')->name('edit_employee_loan');
        Route::post('/insert_employee_loan', 'EmployeeLoanController@insert_employee_loan')->name('insert_employee_loan');
        Route::post('/update_employee_loan', 'EmployeeLoanController@update_employee_loan')->name('update_employee_loan');
        Route::get('/change_loan_status/{id}/{status}', 'EmployeeLoanController@change_loan_status')->name('change_loan_status');
        Route::get('/delete_employee_loan/{id}', 'EmployeeLoanController@delete_employee_loan')->name('delete_employee_loan');
        Route::get('/approve_emp_loan/{id}', 'EmployeeLoanController@approve_emp_loan')->name('approve_emp_loan');
        Route::post('/reject_emp_loan', 'EmployeeLoanController@reject_emp_loan')->name('reject_emp_loan');
        // new route added for advance salary.
        Route::get('/advance_salary', 'EmployeeLoanController@advance_salary')->name('advance_salary');

        //methods of AnnouncementsController
        Route::get('/announcements', 'AnnouncementsController@index')->name('announcements');
        Route::get('/get_announcements_list', 'AnnouncementsController@get_announcements_list')->name('get_announcements_list');
        Route::get('/add_announcements', 'AnnouncementsController@add_announcements')->name('add_announcements');
        Route::post('/insert_announcements', 'AnnouncementsController@insert_announcements')->name('insert_announcements');
        Route::get('/edit_announcements/{id}', 'AnnouncementsController@edit_announcements')->name('edit_announcements');
        Route::post('/update_announcements', 'AnnouncementsController@update_announcements')->name('update_announcements');
        Route::get('/delete_announcements/{id}', 'AnnouncementsController@delete_announcements')->name('delete_announcements');


        //methods of HolidayController
        Route::get('/holiday', 'HolidayController@index')->name('holiday');
        Route::get('/get_holiday_list', 'HolidayController@get_holiday_list')->name('get_holiday_list');
        Route::get('/add_holiday', 'HolidayController@add_holiday')->name('add_holiday');
        Route::post('/insert_holiday', 'HolidayController@insert_holiday')->name('insert_holiday');
        Route::get('/edit_holiday/{id}', 'HolidayController@edit_holiday')->name('edit_holiday');
        Route::post('/update_holiday', 'HolidayController@update_holiday')->name('update_holiday');
        Route::get('/delete_holiday/{id}', 'HolidayController@delete_holiday')->name('delete_holiday');
        Route::get('/change_holiday_status/{id}/{status}', 'HolidayController@change_holiday_status')->name('change_holiday_status');

        //methods of AttendanceController
        Route::get('/attendance', 'AttendanceController@index')->name('attendance');
        Route::get('/get_attendance_list', 'AttendanceController@get_attendance_list')->name('get_attendance_list');
        Route::get('get_punch_data/{id}', 'AttendanceController@get_punch_data')->name('get_punch_data');
        Route::get('set_punch_data/{id}/{time}/{type}', 'AttendanceController@set_punch_data')->name('set_punch_data');
        Route::get('get_user_attendance/{id}/{month}/{year}', 'AttendanceController@get_user_attendance')->name('get_user_attendance');
        Route::get('/approve_attendance', 'AttendanceController@approve_attendance')->name('approve_attendance');
        Route::get('/approve_attendance_list', 'AttendanceController@approve_attendance_list')->name('approve_attendance_list');
        Route::post('/attendance_approval', 'AttendanceController@attendance_approval')->name('attendance_approval');
        Route::get('/late_change_status/{id}', 'AttendanceController@late_change_status')->name('late_change_status');

        Route::get('/add_attendance', 'AttendanceController@add_attendance')->name('add_attendance');
        //Route::post('/insert_attendance', 'AttendanceController@insert_attendance')->name('insert_attendance');

        Route::get('/get_payroll', 'AttendanceController@get_payroll')->name('get_payroll');
        Route::get('/get_payroll_list', 'AttendanceController@get_payroll_list')->name('get_payroll_list');
        Route::get('/lock_payroll/{id}', 'AttendanceController@lock_payroll')->name('lock_payroll');
        Route::get('/edit_payroll/{id}', 'AttendanceController@edit_payroll')->name('edit_payroll');
        Route::post('/update_payroll', 'AttendanceController@update_payroll')->name('update_payroll');

        Route::get('/get_salary_slip', 'AttendanceController@get_salary_slip')->name('get_salary_slip');
        Route::post('/download_salary', 'AttendanceController@download_salary')->name('download_salary');
        Route::get('/payroll_approve/{id}', 'AttendanceController@payroll_approve')->name('payroll_approve');
        Route::post('/payroll_approve_all', 'AttendanceController@payroll_approve_all')->name('payroll_approve_all');
        Route::post('/payroll_reject', 'AttendanceController@payroll_reject')->name('payroll_reject');
        Route::get('/pause_loan/{id}', 'AttendanceController@pause_loan')->name('pause_loan');
        Route::get('/resume_loan/{id}', 'AttendanceController@resume_loan')->name('resume_loan');
        Route::post('/late_mark_remove', 'AttendanceController@late_mark_remove')->name('late_mark_remove');
        // 21/12/2020 Alpesh Patel Change
        Route::get('/payroll_generate_hr', 'AttendanceController@payroll_generate_hr')->name('payroll_generate_hr');

        //methods of TaxDeclarationController
        Route::any('/tax_declaration', 'TaxDeclarationController@index')->name('tax_declaration');
        Route::get('/employee_tax_declaration_list', 'TaxDeclarationController@employee_tax_declaration_list')->name('employee_tax_declaration_list');
        Route::get('/edit_tax_declaration/{id}', 'TaxDeclarationController@edit_tax_declaration')->name('edit_tax_declaration');
        Route::post('/update', 'TaxDeclarationController@update')->name('update');
        Route::get('/generate_form_16', 'TaxDeclarationController@generate_form_16')->name('generate_form_16');
        Route::post('/get_user_form', 'TaxDeclarationController@get_user_form')->name('get_user_form');
        Route::any('/user_form_16', 'TaxDeclarationController@user_form_16')->name('user_form_16');
        Route::post('/upload_user_form_16', 'TaxDeclarationController@upload_user_form_16')->name('upload_user_form_16');
        Route::get('/save_form_16_data', 'TaxDeclarationController@save_form_16_data')->name('save_form_16_data');

        //methods of JobOpeningController
        Route::get('/job_opening', 'JobOpeningController@index')->name('job_opening');
        Route::get('/opening_change_status/{id}/{status}', 'JobOpeningController@opening_change_status')->name('opening_change_status');
        Route::get('/edit_opening/{id}', 'JobOpeningController@edit_opening')->name('edit_opening');
        Route::get('/close_opening/{id}/{status}', 'JobOpeningController@close_opening')->name('close_opening');
        Route::get('/add_opening', 'JobOpeningController@add_opening')->name('add_opening');
        Route::post('/insert_job_opening', 'JobOpeningController@insert_job_opening')->name('insert_job_opening');
        Route::post('/update_job_opening', 'JobOpeningController@update_job_opening')->name('update_job_opening');

        ////methods of InterviewController
        Route::get('/interview', 'InterviewController@index')->name('interview');
        Route::get('/interview_change_status/{id}/{status}', 'InterviewController@interview_change_status')->name('interview_change_status');
        Route::get('/add_interview', 'InterviewController@add_interview')->name('add_interview');
        Route::get('/edit_interview/{id}', 'InterviewController@edit_interview')->name('edit_interview');
        Route::post('/insert_interview', 'InterviewController@insert_interview')->name('insert_interview');
        Route::post('/update_interview', 'InterviewController@update_interview')->name('update_interview');
        Route::get('/add_next_interview/{id}', 'InterviewController@add_next_interview')->name('add_next_interview');
        Route::any('/insert_next_interview', 'InterviewController@insert_next_interview')->name('insert_next_interview');
        Route::post('/update_round2_interview', 'InterviewController@update_round2_interview')->name('update_round2_interview');
        Route::post('/update_round3_interview', 'InterviewController@update_round3_interview')->name('update_round3_interview');
        Route::get('/confirm_interview/{id}', 'InterviewController@confirm_interview')->name('confirm_interview');
        Route::post('/add_confirm_interview', 'InterviewController@add_confirm_interview')->name('add_confirm_interview');
        Route::get('/interview_details/{id}', 'InterviewController@interview_details')->name('interview_details');
        Route::get('/interview_marks/{id}', 'InterviewController@interview_marks')->name('interview_marks');
        Route::any('/insert_interview_marks', 'InterviewController@insert_interview_marks')->name('insert_interview_marks');
        Route::get('/interview_action/{id}/{status}', 'InterviewController@interview_action')->name('interview_action');
        Route::get('/interview_complete/{id}', 'InterviewController@interview_complete')->name('interview_complete');
        Route::get('/interviewIsOnHold/{id}/{status}', 'InterviewController@interviewIsOnHold')->name('interviewIsOnHold');
        Route::post('/interviewer_detail', 'InterviewController@interviewer_detail')->name('interviewer_detail');
        Route::get('/job_candidates/{id}', 'InterviewController@job_candidates')->name('job_candidates');
        Route::post('/multiple_candidate_approval', 'InterviewController@multiple_candidate_approval')->name('multiple_candidate_approval');

        //methods of DepartmentController
        Route::get('/department', 'DepartmentController@index')->name('department');
        Route::get('/add_department', 'DepartmentController@add_department')->name('add_department');
        Route::get('/edit_department/{id}', 'DepartmentController@edit_department')->name('edit_department');
        Route::post('/insert_department', 'DepartmentController@insert_department')->name('insert_department');
        Route::post('/update_department', 'DepartmentController@update_department')->name('update_department');
        Route::post('/get_departmentlist_by_company', 'DepartmentController@get_departmentlist_by_company')->name('get_departmentlist_by_company');


        //methods of AssetController
        Route::get('/asset', 'AssetController@index')->name('asset');
        Route::get('/asset_list', 'AssetController@asset_list')->name('asset_list');
        Route::get('/edit_asset/{id}', 'AssetController@edit_asset')->name('edit_asset');
        Route::post('/update_asset', 'AssetController@update_asset')->name('update_asset');
        Route::get('/add_asset', 'AssetController@add_asset')->name('add_asset');
        Route::post('/insert_asset', 'AssetController@insert_asset')->name('insert_asset');
        Route::get('/delete_asset/{id}', 'AssetController@delete_asset')->name('delete_asset');
        Route::get('/get_asset_images/{id}', 'AssetController@get_asset_images')->name('get_asset_images');
        Route::get('/change_asset/{id}/{status}', 'AssetController@change_asset')->name('change_asset');
        Route::post('/aseet_expired_reminder_dates', 'AssetController@aseet_expired_reminder_dates')->name('aseet_expired_reminder_dates');
        Route::post('/update_reminder_dates', 'AssetController@update_reminder_dates')->name('update_reminder_dates');

        //methods of AssetAccessController
        Route::get('/asset_access', 'AssetAccessController@index')->name('asset_access');
        Route::get('/asset_access_list', 'AssetAccessController@asset_access_list')->name('asset_access_list');
        Route::get('/edit_asset_access/{id}', 'AssetAccessController@edit_asset_access')->name('edit_asset_access');
        Route::post('/update_asset_access', 'AssetAccessController@update_asset_access')->name('update_asset_access');
        Route::get('/add_asset_access', 'AssetAccessController@add_asset_access')->name('add_asset_access');
        Route::post('/insert_asset_access', 'AssetAccessController@insert_asset_access')->name('insert_asset_access');
        Route::get('/delete_asset_access/{id}', 'AssetAccessController@delete_asset_access')->name('delete_asset_access');
        Route::get('/change_asset_access/{id}/{status}', 'AssetAccessController@change_asset_access')->name('change_asset_access');
        Route::post('/add_asset_expense', 'AssetAccessController@add_asset_expense')->name('add_asset_expense');
        Route::get('/get_asset_expense_details/{id}', 'AssetAccessController@get_asset_expense_details')->name('get_asset_expense_details');
        Route::post('/reject_asset_assigned', 'AssetAccessController@reject_asset_assigned')->name('reject_asset_assigned');
        Route::post('/get_assigned_user', 'AssetAccessController@get_assigned_user')->name('get_assigned_user');
        Route::any('/asset_report', 'AssetAccessController@asset_report')->name('asset_report');
        //methods of Recruitment_consultantController
        Route::get('/recruitment_consultant', 'Recruitment_consultantController@index')->name('recruitment_consultant');
        Route::get('/recruitment_change_status/{id}/{status}', 'Recruitment_consultantController@recruitment_change_status')->name('recruitment_change_status');
        Route::get('/edit_consultant/{id}', 'Recruitment_consultantController@edit_consultant')->name('edit_consultant');
        Route::post('/update_consultant', 'Recruitment_consultantController@update_consultant')->name('update_consultant');
        Route::get('/add_consultant', 'Recruitment_consultantController@add_consultant')->name('add_consultant');
        Route::post('/insert_consultant', 'Recruitment_consultantController@insert_consultant')->name('insert_consultant');
        Route::get('/hr_access_request', 'AssetAccessController@hr_access_request')->name('hr_access_request');
        Route::get('/hr_access_request_list', 'AssetAccessController@hr_access_request_list')->name('hr_access_request_list');
        //methods of ExpenseCategoryController
        Route::get('/expense_category', 'ExpenseCategoryController@index')->name('expense_category');
        Route::get('/change_expense_category/{id}/{status}', 'ExpenseCategoryController@change_expense_category')->name('change_expense_category');
        Route::get('/edit_expense_category/{id}', 'ExpenseCategoryController@edit_expense_category')->name('edit_expense_category');
        Route::post('/update_expense_category', 'ExpenseCategoryController@update_expense_category')->name('update_expense_category');
        Route::get('/add_expense_category', 'ExpenseCategoryController@add_expense_category')->name('add_expense_category');
        Route::post('/insert_expense_category', 'ExpenseCategoryController@insert_expense_category')->name('insert_expense_category');

        //methods of EmployeeExpenseController
        Route::get('/employee_expense', 'EmployeeExpenseController@index')->name('employee_expense');
        Route::get('/change_employee_expense/{id}/{status}', 'EmployeeExpenseController@change_employee_expense')->name('change_employee_expense');
        Route::get('/edit_employee_expense/{id}', 'EmployeeExpenseController@edit_employee_expense')->name('edit_employee_expense');
        Route::post('/update_employee_expense', 'EmployeeExpenseController@update_employee_expense')->name('update_employee_expense');
        Route::get('/add_employee_expense', 'EmployeeExpenseController@add_employee_expense')->name('add_employee_expense');
        Route::post('/insert_employee_expense', 'EmployeeExpenseController@insert_employee_expense')->name('insert_employee_expense');
        Route::get('/delete_employee_expense/{id}', 'EmployeeExpenseController@delete_employee_expense')->name('delete_employee_expense');
        Route::get('/paid_employee_expense/{id}', 'EmployeeExpenseController@paid_employee_expense')->name('paid_employee_expense');
        Route::get('/employee_expense_list', 'EmployeeExpenseController@employee_expense_list')->name('employee_expense_list');
        Route::get('/approve_employee_expense/{id}', 'EmployeeExpenseController@approve_employee_expense')->name('approve_employee_expense');
        Route::get('/reject_emp_expense/{id}', 'EmployeeExpenseController@reject_emp_expense')->name('reject_emp_expense');
        Route::post('/reject_employee_expense', 'EmployeeExpenseController@reject_employee_expense')->name('reject_employee_expense');
        Route::get('/get_expense_project_list', 'EmployeeExpenseController@get_expense_project_list')->name('get_expense_project_list');
        Route::post('/approve_employee_expence_multiple', 'EmployeeExpenseController@approve_employee_expence_multiple')->name('approve_employee_expence_multiple');
        Route::post('/get_expense', 'EmployeeExpenseController@get_expense')->name('get_expense');
        Route::post('/approve_employee_expenseByAccountant', 'EmployeeExpenseController@approve_employee_expenseByAccountant')->name('approve_employee_expenseByAccountant');
        Route::post('/banks_cheque_list', 'EmployeeExpenseController@banks_cheque_list')->name('banks_cheque_list');
        Route::get('/all_employee_expense_list_ajax', 'EmployeeExpenseController@all_employee_expense_list_ajax')->name('all_employee_expense_list_ajax');
        Route::post('/banks_cheque_rtgs_reff_list', 'EmployeeExpenseController@banks_cheque_rtgs_reff_list')->name('banks_cheque_rtgs_reff_list');
        Route::post('/get_voucher_ref_number', 'EmployeeExpenseController@get_voucher_ref_number')->name('get_voucher_ref_number');
        Route::post('/get_voucher_number', 'EmployeeExpenseController@get_voucher_number')->name('get_voucher_number');


        //methods of ResignController
        Route::get('/resign', 'ResignController@index')->name('resign');
        Route::get('/add_resign', 'ResignController@add_resign')->name('add_resign');
        Route::post('/submit_resign', 'ResignController@submit_resign')->name('submit_resign');
        Route::get('/edit_resign/{id}', 'ResignController@edit_resign')->name('edit_resign');
        Route::post('/update_resign', 'ResignController@update_resign')->name('update_resign');
        Route::post('/get_resign_detail', 'ResignController@get_resign_detail')->name('get_resign_detail');
        Route::post('/confirm_resign', 'ResignController@confirm_resign')->name('confirm_resign');
        Route::post('/revoked_resign', 'ResignController@revoked_resign')->name('revoked_resign');
        Route::get('/approve_resign/{id}', 'ResignController@approve_resign')->name('approve_resign');
        Route::post('/relieving_date', 'ResignController@relieving_date')->name('relieving_date');
        Route::post('/relieving_letter', 'ResignController@relieving_letter')->name('relieving_letter');
        Route::post('/retain_resign', 'ResignController@retain_resign')->name('retain_resign');
        Route::post('/superadmin_confirm_resign', 'ResignController@superadmin_confirm_resign')->name('superadmin_confirm_resign');
        Route::get('/exit_interview_sheet/{id}', 'ResignController@exit_interview_sheet')->name('exit_interview_sheet');
        Route::post('/asset_takerByHr', 'ResignController@asset_takerByHr')->name('asset_takerByHr');
        Route::post('/upload_exitInterviewSheet', 'ResignController@upload_exitInterviewSheet')->name('upload_exitInterviewSheet');

        //methods of TravelController
        Route::get('/travel', 'TravelController@index')->name('travel');
        Route::get('/add_travel', 'TravelController@add_travel')->name('add_travel');
        Route::post('/save_travel', 'TravelController@save_travel')->name('save_travel');
        Route::get('/edit_travel/{id}', 'TravelController@edit_travel')->name('edit_travel');
        Route::post('/update_travel', 'TravelController@update_travel')->name('update_travel');
        Route::post('/get_travel_detail', 'TravelController@get_travel_detail')->name('get_travel_detail');
        //Route::get('/approve_travel/{id}/{status}', 'TravelController@approve_travel')->name('approve_travel');
        Route::get('/cancel_travel/{id}', 'TravelController@cancel_travel')->name('cancel_travel');
        //Route::get('/all_travel','TravelController@all_travel')->name('all_travel');
        //Route::post('/reject_travel_expence','TravelController@reject_travel_expence')->name('reject_travel_expence');
        Route::get('/travel_requests', 'TravelController@travel_requests')->name('travel_requests');
        Route::get('/add_travel_option/{id}', 'TravelController@add_travel_option')->name('add_travel_option');
        Route::get('/edit_travel_option/{id}', 'TravelController@edit_travel_option')->name('edit_travel_option');
        Route::post('/insert_travel_option', 'TravelController@insert_travel_option')->name('insert_travel_option');
        Route::post('/update_travel_option', 'TravelController@update_travel_option')->name('update_travel_option');
        Route::get('/get_travel_options/{id}', 'TravelController@get_travel_options')->name('get_travel_options');
        //Route::get('/approve_travel_option/{id}/{travel_id}', 'TravelController@approve_travel_option')->name('approve_travel_option');
        Route::post('/get_travel_files', 'TravelController@get_travel_files')->name('get_travel_files');
        Route::get('/travel_booking/{id}', 'TravelController@travel_booking')->name('travel_booking');
        Route::post('/get_company_payment_cards', 'TravelController@get_company_payment_cards')->name('get_company_payment_cards');
        Route::post('/save_travel_booking', 'TravelController@save_travel_booking')->name('save_travel_booking');
        Route::post('/get_flight_detail', 'TravelController@get_flight_detail')->name('get_flight_detail');
        Route::post('/approve_travel_option', 'TravelController@approve_travel_option')->name('approve_travel_option');
        Route::post('/reject_all_travel_option', 'TravelController@reject_all_travel_option')->name('reject_all_travel_option');
        Route: Route::post('/reject_travel_request', 'TravelController@reject_travel_request')->name('reject_travel_request');
        Route::post('/get_confirm_travel_detail', 'TravelController@get_confirm_travel_detail')->name('get_confirm_travel_detail');


        //methods of HotelController
        Route::get('/hotel', 'HotelController@index')->name('hotel');
        Route::get('/add_hotel', 'HotelController@add_hotel')->name('add_hotel');
        Route::post('/save_hotel', 'HotelController@save_hotel')->name('save_hotel');
        Route::get('/edit_hotel/{id}', 'HotelController@edit_hotel')->name('edit_hotel');
        Route::post('/update_hotel', 'HotelController@update_hotel')->name('update_hotel');
        Route::post('/get_hotel_detail', 'HotelController@get_hotel_detail')->name('get_hotel_detail');
        Route::get('/approve_hotel/{id}/{status}', 'HotelController@approve_hotel')->name('approve_hotel');
        Route::get('/cancel_hotel/{id}', 'HotelController@cancel_hotel')->name('cancel_hotel');
        Route::get('/all_hotel', 'HotelController@all_hotel')->name('all_hotel');
        Route::post('/reject_hotel_expence', 'HotelController@reject_hotel_expence')->name('reject_hotel_expence');

        //methods of PolicyController
        Route::get('/policy', 'PolicyController@index')->name('policy');
        Route::get('/get_policy_list', 'PolicyController@get_policy_list')->name('get_policy_list');
        Route::get('/add_policy', 'PolicyController@add_policy')->name('add_policy');
        Route::post('/insert_policy', 'PolicyController@insert_policy')->name('insert_policy');
        Route::get('/edit_policy/{id}', 'PolicyController@edit_policy')->name('edit_policy');
        Route::post('/update_policy', 'PolicyController@update_policy')->name('update_policy');
        Route::get('/delete_policy/{id}', 'PolicyController@delete_policy')->name('delete_policy');
        Route::get('/revise_policy/{id}', 'PolicyController@revise_policy')->name('revise_policy');
        Route::post('/update_revise_policy', 'PolicyController@update_revise_policy')->name('update_revise_policy');
        Route::get('/revise_policy_list', 'PolicyController@revise_policy_list')->name('revise_policy_list');
        Route::get('/get_revise_policy_list', 'PolicyController@get_revise_policy_list')->name('get_revise_policy_list');
        Route::get('/approve_revise_policy/{id}', 'PolicyController@approve_revise_policy')->name('approve_revise_policy');
        Route::get('/reject_revise_policy/{id}', 'PolicyController@reject_revise_policy')->name('reject_revise_policy');
        Route::get('/revise_policy_user_list/{id}', 'PolicyController@revise_policy_user_list')->name('revise_policy_user_list');
        Route::post('/confirm_user_policy', 'PolicyController@confirm_user_policy')->name('confirm_user_policy');
        Route::get('/get_policy_user_list/{id}', 'PolicyController@get_policy_user_list')->name('get_policy_user_list');
        // new sub module added for company rules 11-06-2021company_rules
        Route::get('/company_rules', 'PolicyController@company_rules')->name('company_rules');
        Route::get('/add_rule', 'PolicyController@add_rule')->name('add_rule');
        Route::get('/get_companyrule_list', 'PolicyController@get_companyrule_list')->name('get_companyrule_list');
        Route::post('/insert_rule', 'PolicyController@insert_rule')->name('insert_rule');
        Route::get('/edit_rules/{id}', 'PolicyController@edit_rules')->name('edit_rules');
        Route::post('/update_rules', 'PolicyController@update_rules')->name('update_rules');
        Route::get('/delete_rule/{id}', 'PolicyController@delete_rule')->name('delete_rule');
        Route::get('/approve_rule/{id}', 'PolicyController@approve_rule')->name('approve_rule');
        Route::get('/reject_rule/{id}', 'PolicyController@reject_rule')->name('reject_rule');

        // Attendance Report
        Route::any('/attendance_report', 'AttendanceReportController@index')->name('attendance_report');
        Route::any('/download_excel', 'AttendanceReportController@generateExcelFiles')->name('download_excel');
        // Finance Report
        Route::any('/finance_report', 'FinanceReportController@index')->name('finance_report');
// 
        //methods of BankPaymentApprovalController
        Route::get('/payment', 'BankPaymentApprovalController@index')->name('payment');
        Route::get('/get_bank_payment_list', 'BankPaymentApprovalController@get_bank_payment_list')->name('get_bank_payment_list');
        Route::get('/add_bank_payment_detail/{tender_id?}/{type?}', 'BankPaymentApprovalController@add_bank_payment_detail')->name('add_bank_payment_detail');
        Route::post('/insert_payment', 'BankPaymentApprovalController@insert_payment')->name('insert_payment');
        Route::get('/edit_bank_payment_detail/{id}', 'BankPaymentApprovalController@edit_bank_payment_detail')->name('edit_bank_payment_detail');
        Route::post('/update_payment', 'BankPaymentApprovalController@update_payment')->name('update_payment');
        Route::any('/payment_list', 'BankPaymentApprovalController@payment_list')->name('payment_list');
        Route::post('/approve_bank_payment', 'BankPaymentApprovalController@approve_bank_payment')->name('approve_bank_payment');
        Route::post('/reject_bank_payment', 'BankPaymentApprovalController@reject_bank_payment')->name('reject_bank_payment');
        Route::get('/get_bank_payment_list_ajax', 'BankPaymentApprovalController@get_bank_payment_list_ajax')->name('get_bank_payment_list_ajax');
        Route::get('/get_bank_cheque_list_edit', 'BankPaymentApprovalController@get_bank_cheque_list_edit')->name('get_bank_cheque_list_edit');
        Route::get('/get_cheque_list_bank_payment', 'BankPaymentApprovalController@get_cheque_list_bank_payment')->name('get_cheque_list_bank_payment');
        Route::get('/get_vendor_bank_details', 'BankPaymentApprovalController@get_vendor_bank_details')->name('get_vendor_bank_details');
        Route::get('/get_cheque_list_bank', 'BankPaymentApprovalController@get_cheque_list_bank')->name('get_cheque_list_bank');
        Route::post('/get_approval_note', 'BankPaymentApprovalController@get_approval_note')->name('get_approval_note');
        Route::post('/delete_bankpayment_file', 'BankPaymentApprovalController@delete_bankpayment_file')->name('delete_bankpayment_file');
        Route::post('/get_bank_payment_files', 'BankPaymentApprovalController@get_bank_payment_files')->name('get_bank_payment_files');
        Route::post('/get_bank_payment_data', 'BankPaymentApprovalController@get_bank_payment_data')->name('get_bank_payment_data');
        Route::post('/get_bank_rtgs_list', 'BankPaymentApprovalController@get_bank_rtgs_list')->name('get_bank_rtgs_list');
        Route::post('/get_bank_cheque_list', 'BankPaymentApprovalController@get_bank_cheque_list')->name('get_bank_cheque_list');
        Route::post('/get_bankApproval', 'BankPaymentApprovalController@get_bankApproval')->name('get_bankApproval');
        Route::post('/approve_bankPaymentByAccountant', 'BankPaymentApprovalController@approve_bankPaymentByAccountant')->name('approve_bankPaymentByAccountant');
        Route::post('/get_previous_payments', 'BankPaymentApprovalController@get_previous_payments')->name('get_previous_payments');
        Route::post('/get_bank_cheque_reff_list', 'BankPaymentApprovalController@get_bank_cheque_reff_list')->name('get_bank_cheque_reff_list');
        Route::post('/get_budget_sheet_data', 'BankPaymentApprovalController@get_budget_sheet_data')->name('get_budget_sheet_data');
        Route::get('/payment_approve_pdf/{id}/{type}/{date}', 'BankPaymentApprovalPDFController@index');
        Route::get('/sbi_approve_pdf/{id}/{type}/{date}', 'BankPaymentApprovalPDFController@sbi_index');

        //24/08/2020
        Route::post('/get_budget_sheet_entry_code', 'BankPaymentApprovalController@get_budget_sheet_entry_code')->name('get_budget_sheet_entry_code');

        //18/08/2020
        Route::get('/get_bank_tds_report', 'BankPaymentApprovalController@get_bank_tds_report')->name('get_bank_tds_report');
        Route::get('/get_bank_payment_tds_report', 'BankPaymentApprovalController@get_bank_payment_tds_report')->name('get_bank_payment_tds_report');

        // 30/09/2020
        Route::get('/tender_payment_request_list', 'BankPaymentApprovalController@tender_payment_request_list')->name('tender_payment_request_list');
        Route::get('/get_tender_payment_request_list', 'BankPaymentApprovalController@get_tender_payment_request_list')->name('get_tender_payment_request_list');

        //methods of CashApprovalController
        Route::get('/cash_payment', 'CashApprovalController@index')->name('cash_payment');
        Route::get('/get_cash_payment_list', 'CashApprovalController@get_cash_payment_list')->name('get_cash_payment_list');
        Route::get('/add_cash_payment_detail', 'CashApprovalController@add_cash_payment_detail')->name('add_cash_payment_detail');
        Route::post('/insert_cash_payment', 'CashApprovalController@insert_cash_payment')->name('insert_cash_payment');
        Route::get('/edit_cash_payment_detail/{id}', 'CashApprovalController@edit_cash_payment_detail')->name('edit_cash_payment_detail');
        Route::post('/update_cash_payment', 'CashApprovalController@update_cash_payment')->name('update_cash_payment');
        Route::any('/cash_payment_list', 'CashApprovalController@cash_payment_list')->name('cash_payment_list');
        Route::get('/approve_cash_payment/{id}', 'CashApprovalController@approve_cash_payment')->name('approve_cash_payment');
        Route::get('/reject_cash_payment/{id}/{note}', 'CashApprovalController@reject_cash_payment')->name('reject_cash_payment');
        Route::get('/get_cash_payment_list_ajax', 'CashApprovalController@get_cash_payment_list_ajax')->name('get_cash_payment_list_ajax');
        Route::get('/get_cash_project_list', 'CashApprovalController@get_cash_project_list')->name('get_cash_project_list');
        Route::get('/get_cash_vendor_list', 'CashApprovalController@get_cash_vendor_list')->name('get_cash_vendor_list');
        Route::post('/get_cashApproval', 'CashApprovalController@get_cashApproval')->name('get_cashApproval');
        Route::post('/approve_cashPaymentByAccountant', 'CashApprovalController@approve_cashPaymentByAccountant')->name('approve_cashPaymentByAccountant');

        //methods of BudgetApprovalController
        Route::get('/budget_sheet', 'BudgetSheetController@index')->name('budget_sheet');
        Route::get('/get_budget_sheet_list', 'BudgetSheetController@get_budget_sheet_list')->name('get_budget_sheet_list');
        Route::get('/add_budget_sheet_detail', 'BudgetSheetController@add_budget_sheet_detail')->name('add_budget_sheet_detail');
        Route::post('/insert_budget_sheet', 'BudgetSheetController@insert_budget_sheet')->name('insert_budget_sheet');
        Route::get('/edit_budget_sheet_detail/{id}', 'BudgetSheetController@edit_budget_sheet_detail')->name('edit_budget_sheet_detail');
        Route::post('/update_budget_sheet', 'BudgetSheetController@update_budget_sheet')->name('update_budget_sheet');
        Route::get('/budget_sheet_list', 'BudgetSheetController@budget_sheet_list')->name('budget_sheet_list');
        Route::get('/approve_budget_sheet/{id}', 'BudgetSheetController@approve_budget_sheet')->name('approve_budget_sheet');
        Route::get('/reject_budget_sheet/{id}/{note}', 'BudgetSheetController@reject_budget_sheet')->name('reject_budget_sheet');
        Route::get('/get_budget_sheet_list_ajax', 'BudgetSheetController@get_budget_sheet_list_ajax')->name('get_budget_sheet_list_ajax');
        Route::get('/approve_budget/{id}', 'BudgetSheetController@approve_budget')->name('approve_budget');
        Route::get('/reject_budget/{id}', 'BudgetSheetController@reject_budget')->name('reject_budget');
        Route::post('/get_previous_hold_amt', 'BudgetSheetController@get_previous_hold_amt')->name('get_previous_hold_amt');
        Route::post('/approve_budget_sheet_entry', 'BudgetSheetController@approve_budget_sheet_entry')->name('approve_budget_sheet_entry');
        Route::post('/reject_budget_sheet_entry', 'BudgetSheetController@reject_budget_sheet_entry')->name('reject_budget_sheet_entry');
        Route::get('/hold_budget_sheet_list', 'BudgetSheetController@hold_budget_sheet_list')->name('hold_budget_sheet_list');
        Route::get('/get_hold_budget_sheet_list_ajax', 'BudgetSheetController@get_hold_budget_sheet_list_ajax')->name('get_hold_budget_sheet_list_ajax');
        Route::post('/get_hold_budget_sheet_list_ajax', 'BudgetSheetController@get_hold_budget_sheet_list_ajax')->name('get_hold_budget_sheet_list_ajax');
        Route::get('/manage_hold_amt/{id}', 'BudgetSheetController@manage_hold_amt')->name('manage_hold_amt');
        Route::post('/update_hold_amount', 'BudgetSheetController@update_hold_amount')->name('update_hold_amount');
        Route::any('/budget_sheet_report', 'BudgetSheetController@budget_sheet_report')->name('budget_sheet_report');
        Route::post('/get_budget_sheet_files', 'BudgetSheetController@get_budget_sheet_files')->name('get_budget_sheet_files');
        Route::post('/delete_file', 'BudgetSheetController@delete_file')->name('delete_file');
        Route::get('/getBudgetSheets', 'BudgetSheetController@getBudgetSheets')->name('getBudgetSheets');
        Route::post('/budget_sheet_reportByDate', 'BudgetSheetController@budget_sheet_reportByDate')->name('budget_sheet_reportByDate');
        Route::get('/payment_done_budgetSheet/{id}', 'BudgetSheetController@payment_done_budgetSheet')->name('payment_done_budgetSheet');
        //13-08-2020
        Route::post('/check_purchase_order_number', 'BudgetSheetController@check_purchase_order_number')->name('check_purchase_order_number');
        Route::post('/check_bill_number', 'BudgetSheetController@check_bill_number')->name('check_bill_number');

        //21-08-2020
        Route::get('/your_hold_budget_sheet_list', 'BudgetSheetController@your_hold_budget_sheet_list')->name('your_hold_budget_sheet_list');
        Route::get('/get_your_hold_budget_sheet_list_ajax', 'BudgetSheetController@get_your_hold_budget_sheet_list_ajax')->name('get_your_hold_budget_sheet_list_ajax');
        Route::post('/release_hold_amount', 'BudgetSheetController@release_hold_amount')->name('release_hold_amount');

        // 28/08/2020
        Route::get('/manage_your_hold_amt/{id}', 'BudgetSheetController@manage_your_hold_amt')->name('manage_your_hold_amt');

        //07/09/2020
        Route::post('/get_invoice_files', 'BudgetSheetController@get_invoice_files')->name('get_invoice_files');


        //methods of PreSignedLetterController
        Route::get('/pre_sign_letter', 'PreSignedLetterController@index')->name('pre_sign_letter');
        Route::get('/get_pre_sign_letter_list', 'PreSignedLetterController@get_pre_sign_letter_list')->name('get_pre_sign_letter_list');
        Route::get('/add_pre_sign_letter_detail', 'PreSignedLetterController@add_pre_sign_letter_detail')->name('add_pre_sign_letter_detail');
        Route::post('/insert_pre_sign_letter', 'PreSignedLetterController@insert_pre_sign_letter')->name('insert_pre_sign_letter');
        Route::get('/edit_pre_sign_letter_detail/{id}', 'PreSignedLetterController@edit_pre_sign_letter_detail')->name('edit_pre_sign_letter_detail');
        Route::post('/update_pre_sign_letter', 'PreSignedLetterController@update_pre_sign_letter')->name('update_pre_sign_letter');
        Route::get('/pre_sign_letter_list', 'PreSignedLetterController@pre_sign_letter_list')->name('pre_sign_letter_list');
        Route::get('/approve_pre_sign_letter/{id}/{assign_letter_user_id?}', 'PreSignedLetterController@approve_pre_sign_letter')->name('approve_pre_sign_letter');
        Route::get('/reject_pre_sign_letter/{id}/{note}', 'PreSignedLetterController@reject_pre_sign_letter')->name('reject_pre_sign_letter');
        Route::get('/get_pre_sign_letter_list_ajax', 'PreSignedLetterController@get_pre_sign_letter_list_ajax')->name('get_pre_sign_letter_list_ajax');
        Route::get('/letter_head_delivery', 'PreSignedLetterController@letter_head_delivery')->name('letter_head_delivery');
        Route::get('/letter_head_delivery_list', 'PreSignedLetterController@letter_head_delivery_list')->name('letter_head_delivery_list');
        Route::get('/deliver_pre_sign_letter/{id}', 'PreSignedLetterController@deliver_pre_sign_letter')->name('deliver_pre_sign_letter');
        Route::post('/confirm_pre_request', 'PreSignedLetterController@confirm_pre_request')->name('confirm_pre_request');
        Route::get('/download_letter_head_content/{id}', 'PreSignedLetterController@download_letter_head_content')->name('download_letter_head_content');

        //nishit
        Route::get('/approved_letter_head_report', 'PreSignedLetterController@approved_letter_head_report')->name('approved_letter_head_report');
        Route::get('/approved_letter_head_report_list', 'PreSignedLetterController@approved_letter_head_report_list')->name('approved_letter_head_report_list');
        Route::get('/letter_head_report_list', 'ProSignedLetterController@letter_head_report_list')->name('letter_head_report_list');
        Route::post('/get_letter_head_client_list', 'PreSignedLetterController@get_letter_head_client_list')->name('get_letter_head_client_list');
        Route::post('/get_letter_head_project_list', 'PreSignedLetterController@get_letter_head_project_list')->name('get_letter_head_project_list');
        Route::post('/get_company_pre_letter_head_ref_number', 'PreSignedLetterController@get_company_pre_letter_head_ref_number')->name('get_company_pre_letter_head_ref_number');
        Route::post('/get_company_pre_letter_head_number', 'PreSignedLetterController@get_company_pre_letter_head_number')->name('get_company_pre_letter_head_number');


        //methods of ProSignedLetterController
        Route::get('/pro_sign_letter', 'ProSignedLetterController@index')->name('pro_sign_letter');
        Route::get('/get_pro_sign_letter_list', 'ProSignedLetterController@get_pro_sign_letter_list')->name('get_pro_sign_letter_list');
        Route::get('/add_pro_sign_letter_detail', 'ProSignedLetterController@add_pro_sign_letter_detail')->name('add_pro_sign_letter_detail');
        Route::post('/insert_pro_sign_letter', 'ProSignedLetterController@insert_pro_sign_letter')->name('insert_pro_sign_letter');
        Route::get('/edit_pro_sign_letter_detail/{id}', 'ProSignedLetterController@edit_pro_sign_letter_detail')->name('edit_pro_sign_letter_detail');
        Route::post('/update_pro_sign_letter', 'ProSignedLetterController@update_pro_sign_letter')->name('update_pro_sign_letter');
        Route::get('/pro_sign_letter_list', 'ProSignedLetterController@pro_sign_letter_list')->name('pro_sign_letter_list');
        Route::get('/approve_pro_sign_letter/{id}/{assign_letter_user_id?}', 'ProSignedLetterController@approve_pro_sign_letter')->name('approve_pro_sign_letter');
        Route::get('/reject_pro_sign_letter/{id}/{note}', 'ProSignedLetterController@reject_pro_sign_letter')->name('reject_pro_sign_letter');
        Route::get('/get_pro_sign_letter_list_ajax', 'ProSignedLetterController@get_pro_sign_letter_list_ajax')->name('get_pro_sign_letter_list_ajax');
        Route::get('/letter_head_delivery_pro_list', 'ProSignedLetterController@letter_head_delivery_pro_list')->name('letter_head_delivery_pro_list');
        Route::get('/deliver_pro_sign_letter/{id}', 'ProSignedLetterController@deliver_pro_sign_letter')->name('deliver_pro_sign_letter');
        Route::get('/deliver_pro_sign_letter/{id}', 'ProSignedLetterController@deliver_pro_sign_letter')->name('deliver_pro_sign_letter');
        Route::post('/confirm_pro_request', 'ProSignedLetterController@confirm_pro_request')->name('confirm_pro_request');
        Route::get('/download_normal_letter_head_content/{id}', 'ProSignedLetterController@download_normal_letter_head_content')->name('download_normal_letter_head_content');
        Route::post('/get_company_letter_head_ref_number', 'ProSignedLetterController@get_company_letter_head_ref_number')->name('get_company_letter_head_ref_number');
        Route::post('/get_company_letter_head_number', 'ProSignedLetterController@get_company_letter_head_number')->name('get_company_letter_head_number');

        //methods of DriverExpenseController
        Route::get('/expense', 'DriverExpenseController@index')->name('expense');
        Route::get('/get_my_expense_list', 'DriverExpenseController@get_my_expense_list')->name('get_my_expense_list');
        Route::get('/edit_expense/{id}', 'DriverExpenseController@edit_expense')->name('edit_expense');
        Route::get('/add_expense', 'DriverExpenseController@add_expense')->name('add_expense');
        Route::post('/insert_expense', 'DriverExpenseController@insert_expense')->name('insert_expense');
        Route::post('/update_expense', 'DriverExpenseController@update_expense')->name('update_expense');
        Route::get('/delete_driver_expense/{id}', 'DriverExpenseController@delete_driver_expense')->name('delete_driver_expense');

        Route::get('/all_expense', 'DriverExpenseController@all_expense')->name('all_expense');
        Route::get('/get_all_expense_list', 'DriverExpenseController@get_all_expense_list')->name('get_all_expense_list');
        Route::get('/approve_expense/{id}', 'DriverExpenseController@approve_expense')->name('approve_expense');
        Route::get('/reject_expense/{id}', 'DriverExpenseController@reject_expense')->name('reject_expense');
        Route::post('/reject_update_expense', 'DriverExpenseController@reject_update_expense')->name('reject_update_expense');
        Route::get('/get_assign_asset', 'DriverExpenseController@get_assign_asset')->name('get_assign_asset');

        Route::get('/cheque_register', 'ChequeRegisterController@index')->name('cheque_register');
        Route::get('/add_cheque_register', 'ChequeRegisterController@add_cheque_register')->name('add_cheque_register');
        Route::get('/edit_cheque_register/{id}', 'ChequeRegisterController@edit_cheque_register')->name('edit_cheque_register');
        Route::post('/update_cheque_register', 'ChequeRegisterController@update_cheque_register')->name('update_cheque_register');
        Route::get('/get_cheque_register_list', 'ChequeRegisterController@get_cheque_register_list')->name('get_cheque_register_list');
        Route::post('/insert_cheque_register', 'ChequeRegisterController@insert_cheque_register')->name('insert_cheque_register');
        Route::get('/get_bank_list_cheque', 'ChequeRegisterController@get_bank_list_cheque')->name('get_bank_list_cheque');
        Route::get('/delete_cheque_register/{id}', 'ChequeRegisterController@delete_cheque_register')->name('delete_cheque_register');
        Route::post('/delete_cheques', 'ChequeRegisterController@delete_cheques')->name('delete_cheques');
        Route::get('/change_cheque_status/{id}/{status}', 'ChequeRegisterController@change_cheque_status')->name('change_cheque_status');
        Route::post('/signed_cheques', 'ChequeRegisterController@signed_cheques')->name('signed_cheques');
        Route::any('/cheque_use_report', 'ChequeRegisterController@cheque_use_report')->name('cheque_use_report');
        Route::post('/cheque_failed', 'ChequeRegisterController@cheque_failed')->name('cheque_failed');

        Route::any('/cheque_stats_report', 'ChequeRegisterController@cheque_stats_report')->name('cheque_stats_report');
        Route::get('/cheque_balanced_report/{id}', 'ChequeRegisterController@cheque_balanced_report')->name('cheque_balanced_report');



        //08/06/2020
        Route::get('/blank_cheque_list', 'ChequeRegisterController@blank_cheque_list')->name('blank_cheque_list');
        Route::get('/used_cheque_list', 'ChequeRegisterController@used_cheque_list')->name('used_cheque_list');
        Route::get('/signed_cheque_list', 'ChequeRegisterController@signed_cheque_list')->name('signed_cheque_list');
        Route::get('/failed_cheque_list', 'ChequeRegisterController@failed_cheque_list')->name('failed_cheque_list');

        Route::get('/get_blank_cheque_list', 'ChequeRegisterController@get_blank_cheque_list')->name('get_blank_cheque_list');
        Route::get('/get_used_cheque_list', 'ChequeRegisterController@get_used_cheque_list')->name('get_used_cheque_list');
        Route::get('/get_failed_cheque_list', 'ChequeRegisterController@get_failed_cheque_list')->name('get_failed_cheque_list');
        Route::get('/get_signed_cheque_list', 'ChequeRegisterController@get_signed_cheque_list')->name('get_signed_cheque_list');

        Route::get('/add_signed_cheque', 'ChequeRegisterController@add_signed_cheque')->name('add_signed_cheque');
        Route::post('/update_signed_cheque', 'ChequeRegisterController@update_signed_cheque')->name('update_signed_cheque');
        Route::post('/get_cheque_book', 'ChequeRegisterController@get_cheque_book')->name('get_cheque_book');
        Route::post('/get_unsigned_cheque_list', 'ChequeRegisterController@get_unsigned_cheque_list')->name('get_unsigned_cheque_list');

        Route::post('/get_remaining_cheque', 'ChequeRegisterController@get_remaining_cheque')->name('get_remaining_cheque');

        Route::get('/signed_approval_requests', 'ChequeRegisterController@signed_approval_requests')->name('signed_approval_requests');
        Route::get('/accept_approval_cheque_book/{id}', 'ChequeRegisterController@accept_approval_cheque_book')->name('accept_approval_cheque_book');
        Route::post('/reject_approval_cheque_book', 'ChequeRegisterController@reject_approval_cheque_book')->name('reject_approval_cheque_book');

        Route::get('/add_failed_cheque', 'ChequeRegisterController@add_failed_cheque')->name('add_failed_cheque');
        Route::post('/update_failed_cheque', 'ChequeRegisterController@update_failed_cheque')->name('update_failed_cheque');
        Route::post('/get_unfailed_cheque_book', 'ChequeRegisterController@get_unfailed_cheque_book')->name('get_unfailed_cheque_book');
        Route::post('/get_unfailed_cheque_list', 'ChequeRegisterController@get_unfailed_cheque_list')->name('get_unfailed_cheque_list');
        Route::post('/get_used_cheque_ref_no_list', 'ChequeRegisterController@get_used_cheque_ref_no_list')->name('get_used_cheque_ref_no_list');
        Route::post('/get_used_cheque_number_list', 'ChequeRegisterController@get_used_cheque_number_list')->name('get_used_cheque_number_list');


        //RTGS-NEFTRegisterController
        Route::get('/rtgs_neft_register', 'RtgsNeftRegisterController@index')->name('rtgs_neft_register');
        Route::get('/add_rtgs_neft__register', 'RtgsNeftRegisterController@add_rtgs_neft__register')->name('add_rtgs_neft__register');
        Route::get('/get_rtgs_neft_register_list', 'RtgsNeftRegisterController@get_rtgs_neft_register_list')->name('get_rtgs_neft_register_list');
        Route::post('/insert_rtgs_neft_register', 'RtgsNeftRegisterController@insert_rtgs_neft_register')->name('insert_rtgs_neft_register');
        Route::get('/delete_rtgs_neft_register/{id}', 'RtgsNeftRegisterController@delete_rtgs_neft_register')->name('delete_rtgs_neft_register');
        Route::post('/delete_rtgs_neft', 'RtgsNeftRegisterController@delete_rtgs_neft')->name('delete_rtgs_neft');
        Route::get('/get_bank_rtgs_neft_list', 'RtgsNeftRegisterController@get_bank_rtgs_neft_list')->name('get_bank_rtgs_neft_list');
        Route::get('/get_project_list', 'RtgsNeftRegisterController@get_project_list')->name('get_project_list');
        Route::get('/get_vendor_list', 'RtgsNeftRegisterController@get_vendor_list')->name('get_vendor_list');
        Route::get('/get_cheque_list', 'RtgsNeftRegisterController@get_cheque_list')->name('get_cheque_list');

        //GeneralChequeRegisterController
        Route::get('/general_register', 'GeneralRegisterController@index')->name('general_register');
        Route::get('/add_general_register', 'GeneralRegisterController@add_general_register')->name('add_general_register');
        Route::get('/get_general_register_list', 'GeneralRegisterController@get_general_register_list')->name('get_general_register_list');
        Route::post('/insert_general_register', 'GeneralRegisterController@insert_general_register')->name('insert_general_register');
        Route::get('/delete_general_register/{id}', 'GeneralRegisterController@delete_general_register')->name('delete_general_register');
        Route::post('/delete_general', 'GeneralRegisterController@delete_general')->name('delete_general');
        Route::get('/get_bank_general_list', 'GeneralRegisterController@get_bank_general_list')->name('get_bank_general_list');
        Route::get('/get_general_project_list', 'GeneralRegisterController@get_general_project_list')->name('get_general_project_list');
        Route::get('/get_general_vendor_list', 'GeneralRegisterController@get_general_vendor_list')->name('get_general_vendor_list');
        Route::get('/get_general_cheque_list', 'GeneralRegisterController@get_general_cheque_list')->name('get_general_cheque_list');

        // LetterHeadRegisterController
        Route::get('/letter_head_register', 'LetterHeadRegisterController@index')->name('letter_head_register');
        Route::get('/add_letter_head_register', 'LetterHeadRegisterController@add_letter_head_register')->name('add_letter_head_register');
        Route::get('/get_letter_head_register_list', 'LetterHeadRegisterController@get_letter_head_register_list')->name('get_letter_head_register_list');
        Route::post('/insert_letter_head_register', 'LetterHeadRegisterController@insert_letter_head_register')->name('insert_letter_head_register');
        Route::get('/get_letter_head_bank_list', 'LetterHeadRegisterController@get_letter_head_bank_list')->name('get_letter_head_bank_list');
        Route::get('/delete_letter_head_register/{id}', 'LetterHeadRegisterController@delete_letter_head_register')->name('delete_letter_head_register');
        Route::post('/delete_letter_head_cheques', 'LetterHeadRegisterController@delete_letter_head_cheques')->name('delete_letter_head_cheques');
        Route::get('/change_letter_head_status/{id}/{status}', 'LetterHeadRegisterController@change_letter_head_status')->name('change_letter_head_status');
        Route::post('/signed_letter_head', 'LetterHeadRegisterController@signed_letter_head')->name('signed_letter_head');

        //10/06/2020
        Route::get('blank_letter_head_list', 'LetterHeadRegisterController@blank_letter_head_list')->name('blank_letter_head_list');
        Route::get('get_blank_letter_head_list', 'LetterHeadRegisterController@get_blank_letter_head_list')->name('get_blank_letter_head_list');
        Route::get('signed_letter_head_list', 'LetterHeadRegisterController@signed_letter_head_list')->name('signed_letter_head_list');
        Route::get('get_signed_letter_head_list', 'LetterHeadRegisterController@get_signed_letter_head_list')->name('get_signed_letter_head_list');
        Route::get('used_letter_head_list', 'LetterHeadRegisterController@used_letter_head_list')->name('used_letter_head_list');
        Route::get('get_used_letter_head_list', 'LetterHeadRegisterController@get_used_letter_head_list')->name('get_used_letter_head_list');
        Route::get('failed_letter_head_list', 'LetterHeadRegisterController@failed_letter_head_list')->name('failed_letter_head_list');
        Route::get('get_failed_letter_head_list', 'LetterHeadRegisterController@get_failed_letter_head_list')->name('get_failed_letter_head_list');
        Route::get('add_signed_letter_head', 'LetterHeadRegisterController@add_signed_letter_head')->name('add_signed_letter_head');
        Route::post('get_letter_head_ref_no', 'LetterHeadRegisterController@get_letter_head_ref_no')->name('get_letter_head_ref_no');
        Route::post('get_unsigned_letter_head_list', 'LetterHeadRegisterController@get_unsigned_letter_head_list')->name('get_unsigned_letter_head_list');
        Route::post('get_remaining_letter_head_list', 'LetterHeadRegisterController@get_remaining_letter_head_list')->name('get_remaining_letter_head_list');
        Route::post('letter_head_register_request', 'LetterHeadRegisterController@letter_head_register_request')->name('letter_head_register_request');
        Route::get('signed_letter_head_approval', 'LetterHeadRegisterController@signed_letter_head_approval')->name('signed_letter_head_approval');
        Route::get('/accept_approval_letter_head_ref/{id}', 'LetterHeadRegisterController@accept_approval_letter_head_ref')->name('accept_approval_letter_head_ref');
        Route::post('reject_approval_letter_head_ref', 'LetterHeadRegisterController@reject_approval_letter_head_ref')->name('reject_approval_letter_head_ref');
        Route::get('add_failed_letter_head', 'LetterHeadRegisterController@add_failed_letter_head')->name('add_failed_letter_head');
        Route::post('get_unfailed_letter_head_ref_no', 'LetterHeadRegisterController@get_unfailed_letter_head_ref_no')->name('get_unfailed_letter_head_ref_no');
        Route::post('update_failed_letter_head', 'LetterHeadRegisterController@update_failed_letter_head')->name('update_failed_letter_head');
        Route::post('get_unfailed_letter_head_list', 'LetterHeadRegisterController@get_unfailed_letter_head_list')->name('get_unfailed_letter_head_list');

        Route::get('/apply_unique/', 'LetterHeadRegisterController@apply_unique')->name('apply_unique');

        // VehicleTripController
        Route::get('/vehicle_trip', 'VehicleTripController@index')->name('vehicle_trip');
        Route::get('/add_vehicle_trip', 'VehicleTripController@add_vehicle_trip')->name('add_vehicle_trip');
        Route::get('/edit_vehicle_trip/{id}', 'VehicleTripController@edit_vehicle_trip')->name('edit_vehicle_trip');
        Route::get('/get_vehicle_trip_list', 'VehicleTripController@get_vehicle_trip_list')->name('get_vehicle_trip_list');
        Route::post('/insert_vehicle_trip', 'VehicleTripController@insert_vehicle_trip')->name('insert_vehicle_trip');
        Route::post('/update_vehicle_trip', 'VehicleTripController@update_vehicle_trip')->name('update_vehicle_trip');
        Route::get('/delete_vehicle_trip/{id}', 'VehicleTripController@delete_vehicle_trip')->name('delete_vehicle_trip');
        Route::get('/get_close_vehicle_trip_list', 'VehicleTripController@get_close_vehicle_trip_list')->name('get_close_vehicle_trip_list');
        Route::get('/close_trip_index', 'VehicleTripController@close_trip_index')->name('close_trip_index');
        Route::get('/approve_vehicle_trip/{id}', 'VehicleTripController@approve_vehicle_trip')->name('approve_vehicle_trip');
        Route::post('/reject_vehicle_trip', 'VehicleTripController@reject_vehicle_trip')->name('reject_vehicle_trip');
        Route::any('/vehicle_trip_list_report', 'VehicleTripController@vehicle_trip_list_report')->name('vehicle_trip_list_report');

        //VehicleAssetController
        Route::get('/vehicle_assets', 'VehicleAssetController@get_vehicle_assets')->name('vehicle_assets');
        Route::get('/add_vehicle_insurance', 'VehicleAssetController@add_vehicle_insurance')->name('add_vehicle_insurance');
        Route::post('/insert_vehicle_insurance', 'VehicleAssetController@insert_vehicle_insurance')->name('insert_vehicle_insurance');
        Route::get('/expired_insurances_list', 'VehicleAssetController@expired_insurances_list')->name('expired_insurances_list');
        Route::get('/renew_expired_vehicle_insurance/{id}', 'VehicleAssetController@renew_expired_vehicle_insurance')->name('renew_expired_vehicle_insurance');
        Route::post('/renewed_insurance', 'VehicleAssetController@renewed_insurance')->name('renewed_insurance');
        Route::post('/asset_details', 'VehicleAssetController@asset_details')->name('asset_details');
        Route::get('/insurances_list/{id}/{type}', 'VehicleAssetController@insurances_list')->name('insurances_list');
        Route::post('/get_reminder_dates', 'VehicleAssetController@get_reminder_dates')->name('get_reminder_dates');

        //VendorBankController by nishit
        Route::get('/vendors_bank', 'VendorsBankController@index')->name('vendors_bank');
        Route::get('/companies_vendor', 'VendorsBankController@companies_vendor')->name('companies_vendor');
        Route::get('/add_vendors_bank', 'VendorsBankController@add_vendors_bank')->name('add_vendors_bank');
        Route::get('/change_vendor_bank_status/{id}/{status}', 'VendorsBankController@change_vendor_bank_status')->name('change_vendor_bank_status');
        Route::post('/insert_vendors_bank', 'VendorsBankController@insert_vendors_bank')->name('insert_vendors_bank');
        Route::get('/edit_vendors_bank/{id}', 'VendorsBankController@edit_vendors_bank')->name('edit_vendors_bank');
        Route::post('/update_vendors_bank', 'VendorsBankController@update_vendors_bank')->name('update_vendors_bank');
        Route::any('/check_uniqueAccountNumber', 'VendorsBankController@check_uniqueAccountNumber')->name('check_uniqueAccountNumber');


        //methods of Inward_outwardController
        //  Route::get('/document_category', 'Inward_outwardController@category_list')->name('document_category');
        //  Route::get('/change_doc_status/{id}/{status}', 'Inward_outwardController@change_doc_status')->name('change_doc_status');
        //  Route::post('/add_document', 'Inward_outwardController@add_document')->name('add_document');
        //  Route::get('/edit_document/{id}', 'Inward_outwardController@edit_document')->name('edit_document');
        //  Route::post('/update_document', 'Inward_outwardController@update_document')->name('update_document');
        //  Route::get('/delete_document/{id}', 'Inward_outwardController@delete_document')->name('delete_document');
        //  Route::get('/inward_outward', 'Inward_outwardController@index')->name('inward_outward');
        //  Route::get('/inwards', 'Inward_outwardController@inwards')->name('inwards');
        //  Route::get('/outwards', 'Inward_outwardController@outwards')->name('outwards');
        //  Route::get('/add_inward', 'Inward_outwardController@add_inward')->name('add_inward');
        //  Route::get('/add_outward', 'Inward_outwardController@add_outward')->name('add_outward');
        //  Route::post('/companies_project', 'Inward_outwardController@companies_project')->name('companies_project');
        //  Route::post('/depart_user_list', 'Inward_outwardController@depart_user_list')->name('depart_user_list');
        //  Route::post('/insert_inward', 'Inward_outwardController@insert_inward')->name('insert_inward');
        //  Route::post('/insert_outward', 'Inward_outwardController@insert_outward')->name('insert_outward');
        //  Route::get('/view_inward_to_outward/{id}', 'Inward_outwardController@view_inward_to_outward')->name('view_inward_to_outward');
        //  Route::get('/view_outward_to_inward/{id}', 'Inward_outwardController@view_outward_to_inward')->name('view_outward_to_inward');
        //  Route::get('/pass_registry/{parent_id}/{id}', 'Inward_outwardController@pass_registry')->name('pass_registry');

        Route::get('/document_sub_category', 'DocumentSubCategoryController@document_sub_category_list')->name('document_sub_category');
        Route::get('/change_doc_sub_cat_status/{id}/{status}', 'DocumentSubCategoryController@change_doc_sub_cat_status')->name('change_doc_sub_cat_status');
        Route::get('/delete_document_sub_cat/{id}', 'DocumentSubCategoryController@delete_document_sub_cat')->name('delete_document_sub_cat');
        Route::post('/add_document_sub_categoery', 'DocumentSubCategoryController@add_document_sub_categoery')->name('add_document_sub_categoery');
        Route::get('/edit_document_sub_categoery/{id}', 'DocumentSubCategoryController@edit_document_sub_categoery')->name('edit_document_sub_categoery');
        Route::post('/update_document_sub_categoery', 'DocumentSubCategoryController@update_document_sub_categoery')->name('update_document_sub_categoery');
        Route::get('/get_doc_sub_cat', 'DocumentSubCategoryController@get_doc_sub_cat')->name('get_doc_sub_cat');

        //methods of Inward_outwardController Nishit R
        Route::get('/document_category', 'Inward_outwardController@category_list')->name('document_category');
        Route::get('/change_doc_status/{id}/{status}', 'Inward_outwardController@change_doc_status')->name('change_doc_status');
        Route::post('/add_document', 'Inward_outwardController@add_document')->name('add_document');
        Route::get('/edit_document/{id}', 'Inward_outwardController@edit_document')->name('edit_document');
        Route::post('/update_document', 'Inward_outwardController@update_document')->name('update_document');
        Route::get('/delete_document/{id}', 'Inward_outwardController@delete_document')->name('delete_document');
        Route::get('/pending_registry_documents', 'Inward_outwardController@pending_registry_documents')->name('pending_registry_documents');
        Route::get('/approved_inwards_documents', 'Inward_outwardController@approved_inwards_documents')->name('approved_inwards_documents');
        Route::get('/approved_outwards_documents', 'Inward_outwardController@approved_outwards_documents')->name('approved_outwards_documents');
        Route::post('/mark_approve_documnet', 'Inward_outwardController@mark_approve_documnet')->name('mark_approve_documnet');
        Route::get('/is_doc_special/{id}/{type}', 'Inward_outwardController@is_doc_special')->name('is_doc_special');

        Route::get('/inward_outward', 'Inward_outwardController@index')->name('inward_outward');
        Route::any('/inwards', 'Inward_outwardController@inwards')->name('inwards');
        //  new added for ajax listing
        Route::any('/inwards_list_ajax', 'Inward_outwardController@inwards_list_ajax')->name('inwards_list_ajax');
        Route::any('/outwards', 'Inward_outwardController@outwards')->name('outwards');
        Route::get('/add_inward', 'Inward_outwardController@add_inward')->name('add_inward');
        Route::get('/add_outward', 'Inward_outwardController@add_outward')->name('add_outward');
        Route::post('/companies_project', 'Inward_outwardController@companies_project')->name('companies_project');
        Route::post('/depart_user_list', 'Inward_outwardController@depart_user_list')->name('depart_user_list');
        Route::post('/insert_inward', 'Inward_outwardController@insert_inward')->name('insert_inward');
        Route::post('/insert_outward', 'Inward_outwardController@insert_outward')->name('insert_outward');

        Route::get('/view_inward_to_outward/{id}/{type}', 'Inward_outwardController@view_inward_to_outward')->name('view_inward_to_outward');
        Route::get('/view_outward_to_inward/{id}', 'Inward_outwardController@view_outward_to_inward')->name('view_outward_to_inward');
        Route::get('/pass_registry/{parent_id}/{id}', 'Inward_outwardController@pass_registry')->name('pass_registry');

        Route::get('/registry_chat/{id}', 'Inward_outwardController@registry_chat')->name('registry_chat');
        Route::post('/send_message', 'Inward_outwardController@send_message')->name('send_message');
        Route::get('/get_inward_pending_list', 'Inward_outwardController@get_inward_pending_list')->name('get_inward_pending_list');
        Route::post('/department_user_with_registry', 'Inward_outwardController@department_user_with_registry')->name('department_user_with_registry');
        Route::post('/get_registry_old_user_list', 'Inward_outwardController@get_registry_old_user_list')->name('get_registry_old_user_list');

        Route::get('/search_sender_name', 'Inward_outwardController@search_sender_name')->name('search_sender_name');
        Route::get('/inward_no', 'Inward_outwardController@inward_no')->name('inward_no');
        Route::get('/outward_no', 'Inward_outwardController@outward_no')->name('outward_no');

        Route::get('/edit_inward/{id}', 'Inward_outwardController@edit_inward')->name('edit_inward');
        Route::get('/edit_outward/{id}', 'Inward_outwardController@edit_outward')->name('edit_outward');

        Route::post('/update_inward', 'Inward_outwardController@update_inward')->name('update_inward');
        Route::post('/update_outward', 'Inward_outwardController@update_outward')->name('update_outward');

        Route::get('/assignee_registry', 'Inward_outwardController@assignee_registry')->name('assignee_registry');
        Route::get('/accept_registry/{id}', 'Inward_outwardController@accept_registry')->name('accept_registry');
        Route::post('/reject_registry', 'Inward_outwardController@reject_registry')->name('reject_registry');

        Route::get('/prelimary_action_list', 'Inward_outwardController@prelimary_action_list')->name('prelimary_action_list');
        Route::get('/add_prelimary_process', 'Inward_outwardController@add_prelimary_process')->name('add_prelimary_process');
        Route::post('/insert_prelimary_process', 'Inward_outwardController@insert_prelimary_process')->name('insert_prelimary_process');
        Route::get('/edit_prelimary_process/{id}', 'Inward_outwardController@edit_prelimary_process')->name('edit_prelimary_process');
        Route::post('/update_prelimary_process', 'Inward_outwardController@update_prelimary_process')->name('update_prelimary_process');

        //12/05/2020
        Route::get('/prime_action_list', 'Inward_outwardController@prime_action_list')->name('prime_action_list');
        Route::post('/depart_multi_user_list', 'Inward_outwardController@depart_multi_user_list')->name('depart_multi_user_list');
        Route::get('/add_distrubuted_details/{id}', 'Inward_outwardController@add_distrubuted_details')->name('add_distrubuted_details');
        Route::post('/update_prime_process', 'Inward_outwardController@update_prime_process')->name('update_prime_process');

        Route::get('/accept_requestByPrimeUser/{id}', 'Inward_outwardController@accept_requestByPrimeUser')->name('accept_requestByPrimeUser');
        Route::post('/reject_requestByPrimeUser', 'Inward_outwardController@reject_requestByPrimeUser')->name('reject_requestByPrimeUser');

        Route::get('/accept_requestBySupportEmp/{id}', 'Inward_outwardController@accept_requestBySupportEmp')->name('accept_requestBySupportEmp');
        Route::post('/reject_requestBySupportEmp', 'Inward_outwardController@reject_requestBySupportEmp')->name('reject_requestBySupportEmp');

        Route::get('/managment_view_list', 'Inward_outwardController@managment_view_list')->name('managment_view_list');
        Route::get('/distrubuted_llist/{id}', 'Inward_outwardController@distrubuted_llist')->name('distrubuted_llist');
        Route::post('/update_distrubated_task', 'Inward_outwardController@update_distrubated_task')->name('update_distrubated_task');
        Route::post('/reject_distrubutionPrimeUser', 'Inward_outwardController@reject_distrubutionPrimeUser')->name('reject_distrubutionPrimeUser');

        Route::get('/get_emp_work_details/{id}', 'Inward_outwardController@get_emp_work_details')->name('get_emp_work_details');
        Route::get('/edit_emp_work/{id}', 'Inward_outwardController@edit_emp_work')->name('edit_emp_work');
        Route::post('/update_emp_work', 'Inward_outwardController@update_emp_work')->name('update_emp_work');
        Route::post('/reject_emp_work', 'Inward_outwardController@reject_emp_work')->name('reject_emp_work');
        Route::get('/accept_emp_work/{id}', 'Inward_outwardController@accept_emp_work')->name('accept_emp_work');

        Route::post('/get_hourByPercentage', 'Inward_outwardController@get_hourByPercentage')->name('get_hourByPercentage');
        Route::post('/update_workInterval', 'Inward_outwardController@update_workInterval')->name('update_workInterval');

        Route::get('/removeEmp/{id}', 'Inward_outwardController@removeEmp')->name('removeEmp');
        Route::get('/acceptEmpRequest/{id}', 'Inward_outwardController@acceptEmpRequest')->name('acceptEmpRequest');
        Route::post('/rejectEmpRequest', 'Inward_outwardController@rejectEmpRequest')->name('rejectEmpRequest');


        // Vehicle maintance from Nishit
        Route::get('/vehicle_maintenance', 'VehicleMaintenanceController@vehicle_maintenance')->name('vehicle_maintenance');
        Route::get('/add_vehicle_maintenance', 'VehicleMaintenanceController@add_vehicle_maintenance')->name('add_vehicle_maintenance');
        Route::post('/insert_vehicle_maintenance', 'VehicleMaintenanceController@insert_vehicle_maintenance')->name('insert_vehicle_maintenance');

        Route::get('/update_vehicle_maintenance/{id}', 'VehicleMaintenanceController@update_vehicle_maintenance')->name('update_vehicle_maintenance');
        Route::post('/submit_vehicle_maintenance', 'VehicleMaintenanceController@submit_vehicle_maintenance')->name('submit_vehicle_maintenance');
        Route::get('/vehicle_maintenance_list', 'VehicleMaintenanceController@vehicle_maintenance_list')->name('vehicle_maintenance_list');
        Route::get('/get_vehicle_maintenance_list_ajax', 'VehicleMaintenanceController@get_vehicle_maintenance_list_ajax')->name('get_vehicle_maintenance_list_ajax');
        Route::post('/approve_vehicle_maintenance', 'VehicleMaintenanceController@approve_vehicle_maintenance')->name('approve_vehicle_maintenance');
        Route::post('/reject_vehicle_maintenance', 'VehicleMaintenanceController@reject_vehicle_maintenance')->name('reject_vehicle_maintenance');
        Route::post('/get_vehicle_maintenanace_files', 'VehicleMaintenanceController@get_vehicle_maintenanace_files')->name('get_vehicle_maintenanace_files');
        //methods of ClientsController
        Route::get('/client', 'ClientsController@index')->name('client');
        Route::get('/get_client_list_all', 'ClientsController@get_clients_list')->name('get_client_list_all');
        Route::get('/add_client', 'ClientsController@add_client')->name('add_client');
        Route::post('/insert_client', 'ClientsController@insert_client')->name('insert_client');
        Route::get('/change_client_status/{id}/{status}', 'ClientsController@change_client_status')->name('change_client_status');
        Route::get('/edit_client/{id}', 'ClientsController@edit_client')->name('edit_client');
        Route::post('/update_client', 'ClientsController@update_client')->name('update_client');
        Route::get('/get_clientlist_by_company', 'ClientsController@get_clientlist_by_company')->name('get_clientlist_by_company');
        Route::post('/get_client_contact_list', 'ClientsController@get_client_contact_list')->name('get_client_contact_list');
        Route::post('/get_client_tender', 'ClientsController@get_client_tender')->name('get_client_tender');
        Route::post('/get_tender_detail', 'ClientsController@get_tender_detail')->name('get_tender_detail');
        //Route::any('/check_uniquePancardNumber', 'ClientsController@check_uniquePancardNumber')->name('check_uniquePancardNumber');
        //methods of PaymentCardController
        Route::get('/payment_card', 'PaymentCardController@index')->name('payment_card');
        Route::get('/get_payment_card_list', 'PaymentCardController@get_payment_card_list')->name('get_payment_card_list');
        Route::get('/add_payment_card', 'PaymentCardController@add_payment_card')->name('add_payment_card');
        Route::get('/companies_bank', 'PaymentCardController@companies_bank')->name('companies_bank');
        Route::post('/insert_payment_card', 'PaymentCardController@insert_payment_card')->name('insert_payment_card');
        Route::get('/change_payment_card_status/{id}/{status}', 'PaymentCardController@change_payment_card_status')->name('change_payment_card_status');
        Route::get('/edit_payment_card/{id}', 'PaymentCardController@edit_payment_card')->name('edit_payment_card');
        Route::post('/update_payment_card', 'PaymentCardController@update_payment_card')->name('update_payment_card');
        //Route::get('/delete_payment_card/{id}', 'PaymentCardController@delete_payment_card')->name('delete_payment_card');
        Route::any('/check_uniqueCardNumber', 'PaymentCardController@check_uniqueCardNumber')->name('check_uniqueCardNumber');

        //methods of OnlinePaymentApprovalController
        Route::get('/online_payment', 'OnlinePaymentApprovalController@index')->name('online_payment');
        Route::get('/get_online_payment_list', 'OnlinePaymentApprovalController@get_online_payment_list')->name('get_online_payment_list');
        Route::get('/add_online_payment_detail', 'OnlinePaymentApprovalController@add_online_payment_detail')->name('add_online_payment_detail');
        Route::post('/insert_online_payment', 'OnlinePaymentApprovalController@insert_online_payment')->name('insert_online_payment');
        Route::get('/edit_online_payment_detail/{id}', 'OnlinePaymentApprovalController@edit_online_payment_detail')->name('edit_online_payment_detail');
        Route::post('/update_online_payment', 'OnlinePaymentApprovalController@update_online_payment')->name('update_online_payment');
        Route::any('/online_payment_list', 'OnlinePaymentApprovalController@online_payment_list')->name('online_payment_list');
        Route::post('/approve_online_payment', 'OnlinePaymentApprovalController@approve_online_payment')->name('approve_online_payment');
        Route::post('/reject_online_payment', 'OnlinePaymentApprovalController@reject_online_payment')->name('reject_online_payment');
        Route::get('/get_online_payment_list_ajax', 'OnlinePaymentApprovalController@get_online_payment_list_ajax')->name('get_online_payment_list_ajax');
        Route::get('/get_online_cheque_list_edit', 'OnlinePaymentApprovalController@get_online_cheque_list_edit')->name('get_online_cheque_list_edit');
        Route::get('/get_cheque_list_online_payment', 'OnlinePaymentApprovalController@get_cheque_list_online_payment')->name('get_cheque_list_online_payment');
        Route::get('/get_vendor_online_details', 'OnlinePaymentApprovalController@get_vendor_online_details')->name('get_vendor_online_details');
        Route::get('/get_cheque_list_online', 'OnlinePaymentApprovalController@get_cheque_list_online')->name('get_cheque_list_online');
        Route::post('/get_online_payment_approval_note', 'OnlinePaymentApprovalController@get_online_payment_approval_note')->name('get_online_payment_approval_note');
        Route::post('/get_online_payment_files', 'OnlinePaymentApprovalController@get_online_payment_files')->name('get_online_payment_files');
        Route::post('/delete_online_file', 'OnlinePaymentApprovalController@delete_online_file')->name('delete_online_file');
        Route::get('/get_bank_card_list', 'OnlinePaymentApprovalController@get_bank_card_list')->name('get_bank_card_list');
        Route::post('/get_onlineApproval', 'OnlinePaymentApprovalController@get_onlineApproval')->name('get_onlineApproval');
        Route::post('/approve_onlinePaymentByAccountant', 'OnlinePaymentApprovalController@approve_onlinePaymentByAccountant')->name('approve_onlinePaymentByAccountant');

        //18/08/2020 
        Route::get('/get_online_tds_report', 'OnlinePaymentApprovalController@get_online_tds_report')->name('get_online_tds_report');
        Route::get('/get_online_payment_tds_report', 'OnlinePaymentApprovalController@get_online_payment_tds_report')->name('get_online_payment_tds_report');

        //26/08/2020
        Route::post('/get_online_payment_data', 'OnlinePaymentApprovalController@get_online_payment_data')->name('get_online_payment_data');
        Route::post('/get_budget_sheet_online_entry_code', 'OnlinePaymentApprovalController@get_budget_sheet_online_entry_code')->name('get_budget_sheet_online_entry_code');


        //Vendors Bank Report
        Route::get('/vendorsBank_report', 'VendorsBankReportController@index')->name('vendorsBank_report');

        //LeaveRelieverReportController methods
        Route::any('/leave_reliever_report', 'LeaveRelieverReportController@leave_reliever_report')->name('leave_reliever_report');

        //CompanyDocumentController methods
        Route::any('/company_document_list', 'CompanyDocumentController@company_document_list')->name('company_document_list');
        Route::get('/get_company_documet_list', 'CompanyDocumentController@get_company_documet_list')->name('get_company_documet_list');
        Route::any('/add_company_document_list', 'CompanyDocumentController@add_company_document_list')->name('add_company_document_list');
        Route::post('/insert_company_document_list', 'CompanyDocumentController@insert_company_document_list')->name('insert_company_document_list');
        Route::get('/edit_company_document/{id}', 'CompanyDocumentController@edit_company_document')->name('edit_company_document');
        Route::post('/update_company_document', 'CompanyDocumentController@update_company_document')->name('update_company_document');

        //methods of DocumentSoftcopyController
        Route::get('/hardcopy_reck', 'DocumentSoftcopyController@hardcopy_reck')->name('hardcopy_reck');
        Route::get('/get_hardcopy_reck_list', 'DocumentSoftcopyController@get_hardcopy_reck_list')->name('get_hardcopy_reck_list');
        Route::get('/add_hardcopy_reck', 'DocumentSoftcopyController@add_hardcopy_reck')->name('add_hardcopy_reck');
        Route::post('/insert_hardcopy_reck', 'DocumentSoftcopyController@insert_hardcopy_reck')->name('insert_hardcopy_reck');
        Route::get('/edit_hardcopy_reck/{id}', 'DocumentSoftcopyController@edit_hardcopy_reck')->name('edit_hardcopy_reck');
        Route::post('/update_hardcopy_reck', 'DocumentSoftcopyController@update_hardcopy_reck')->name('update_hardcopy_reck');
        Route::get('/delete_hardcopy_reck/{id}', 'DocumentSoftcopyController@delete_hardcopy_reck')->name('delete_hardcopy_reck');
        Route::get('/change_hardcopy_reck_status/{id}/{status}', 'DocumentSoftcopyController@change_hardcopy_reck_status')->name('change_hardcopy_reck_status');
        Route::get('/get_department', 'DocumentSoftcopyController@get_department')->name('get_department');

        Route::get('/hardcopy_folder', 'DocumentSoftcopyController@hardcopy_folder')->name('hardcopy_folder');
        Route::get('/get_hardcopy_folder_list', 'DocumentSoftcopyController@get_hardcopy_folder_list')->name('get_hardcopy_folder_list');
        Route::get('/add_hardcopy_folder', 'DocumentSoftcopyController@add_hardcopy_folder')->name('add_hardcopy_folder');
        Route::post('/insert_hardcopy_folder', 'DocumentSoftcopyController@insert_hardcopy_folder')->name('insert_hardcopy_folder');
        Route::get('/edit_hardcopy_folder/{id}', 'DocumentSoftcopyController@edit_hardcopy_folder')->name('edit_hardcopy_folder');
        Route::post('/update_hardcopy_folder', 'DocumentSoftcopyController@update_hardcopy_folder')->name('update_hardcopy_folder');
        Route::get('/delete_hardcopy_folder/{id}', 'DocumentSoftcopyController@delete_hardcopy_folder')->name('delete_hardcopy_folder');
        Route::get('/change_hardcopy_folder_status/{id}/{status}', 'DocumentSoftcopyController@change_hardcopy_folder_status')->name('change_hardcopy_folder_status');
        Route::get('/get_hardcopy_reck', 'DocumentSoftcopyController@get_hardcopy_reck')->name('get_hardcopy_reck');

        Route::get('/hardcopy', 'DocumentSoftcopyController@hardcopy')->name('hardcopy');
        Route::get('/get_hardcopy_list', 'DocumentSoftcopyController@get_hardcopy_list')->name('get_hardcopy_list');
        Route::get('/add_hardcopy', 'DocumentSoftcopyController@add_hardcopy')->name('add_hardcopy');
        Route::post('/insert_hardcopy', 'DocumentSoftcopyController@insert_hardcopy')->name('insert_hardcopy');
        Route::get('/edit_hardcopy/{id}', 'DocumentSoftcopyController@edit_hardcopy')->name('edit_hardcopy');
        Route::post('/update_hardcopy', 'DocumentSoftcopyController@update_hardcopy')->name('update_hardcopy');
        Route::get('/delete_hardcopy/{id}', 'DocumentSoftcopyController@delete_hardcopy')->name('delete_hardcopy');
        Route::get('/change_hardcopy_status/{id}/{status}', 'DocumentSoftcopyController@change_hardcopy_status')->name('change_hardcopy_status');
        Route::get('/get_hardcopy_folder', 'DocumentSoftcopyController@get_hardcopy_folder')->name('get_hardcopy_folder');
        Route::get('/get_inward_outward', 'DocumentSoftcopyController@get_inward_outward')->name('get_inward_outward');
        Route::get('/get_hardcopy_file/{id}', 'DocumentSoftcopyController@get_hardcopy_file')->name('get_hardcopy_file');

        Route::post('/check_reck_number', 'DocumentSoftcopyController@check_reck_number')->name('check_reck_number');
        Route::post('/check_folder_number', 'DocumentSoftcopyController@check_folder_number')->name('check_folder_number');
        Route::get('/assignee_requests', 'DocumentSoftcopyController@assignee_requests')->name('assignee_requests');
        Route::get('/get_assignee_requests', 'DocumentSoftcopyController@get_assignee_requests')->name('get_assignee_requests');

        Route::post('/assignee_return_date', 'DocumentSoftcopyController@assignee_return_date')->name('assignee_return_date');
        Route::get('/assignee_completed/{id}', 'DocumentSoftcopyController@assignee_completed')->name('assignee_completed');
        Route::get('/assignee_rejected/{id}', 'DocumentSoftcopyController@assignee_rejected')->name('assignee_rejected');
        Route::post('/get_pdf_page_no', 'DocumentSoftcopyController@get_pdf_page_no')->name('get_pdf_page_no');
        Route::post('/get_inward_outward_edit', 'DocumentSoftcopyController@get_inward_outward_edit')->name('get_inward_outward_edit');
        Route::post('/get_last_page_no', 'DocumentSoftcopyController@get_last_page_no')->name('get_last_page_no');
        Route::post('/get_inward_details', 'DocumentSoftcopyController@get_inward_details')->name('get_inward_details');

        Route::get('/hardcopy_cupboard', 'DocumentSoftcopyController@hardcopy_cupboard')->name('hardcopy_cupboard');
        Route::get('/get_hardcopy_cupboard_list', 'DocumentSoftcopyController@get_hardcopy_cupboard_list')->name('get_hardcopy_cupboard_list');
        Route::get('/add_hardcopy_cupboard', 'DocumentSoftcopyController@add_hardcopy_cupboard')->name('add_hardcopy_cupboard');
        Route::post('/insert_hardcopy_cupboard', 'DocumentSoftcopyController@insert_hardcopy_cupboard')->name('insert_hardcopy_cupboard');
        Route::get('/edit_hardcopy_cupboard/{id}', 'DocumentSoftcopyController@edit_hardcopy_cupboard')->name('edit_hardcopy_cupboard');
        Route::post('/update_hardcopy_cupboard', 'DocumentSoftcopyController@update_hardcopy_cupboard')->name('update_hardcopy_cupboard');
        Route::get('/change_hardcopy_cupboard_status/{id}/{status}', 'DocumentSoftcopyController@change_hardcopy_cupboard_status')->name('change_hardcopy_cupboard_status');
        Route::get('/get_hardcopy_cupboard', 'DocumentSoftcopyController@get_hardcopy_cupboard')->name('get_hardcopy_cupboard');

        Route::post('/check_cupboard_number', 'DocumentSoftcopyController@check_cupboard_number')->name('check_cupboard_number');

        //methods of ProjectSitesController by Kiran
        Route::get('/project_site', 'ProjectSitesController@index')->name('project_site');
        Route::get('/get_list_datatable_ajax', 'ProjectSitesController@get_list_datatable_ajax')->name('get_list_datatable_ajax');
        Route::get('/add_sites', 'ProjectSitesController@add_sites')->name('add_sites');
        Route::post('/companies_clients', 'ProjectSitesController@companies_clients')->name('companies_clients');
        Route::post('/clients_projects', 'ProjectSitesController@clients_projects')->name('clients_projects');
        Route::get('/project_site__status/{id}/{status}', 'ProjectSitesController@project_site__status')->name('project_site__status');
        Route::post('/insert_project_site', 'ProjectSitesController@insert_project_site')->name('insert_project_site');
        Route::get('/edit_project_sites/{id}', 'ProjectSitesController@edit_project_sites')->name('edit_project_sites');
        Route::post('/update_project_sites', 'ProjectSitesController@update_project_sites')->name('update_project_sites');
        Route::post('/checkProjectSiteName','ProjectSitesController@checkProjectSiteName')->name('checkProjectSiteName');
        Route::post('/checkEditProjectSiteName','ProjectSitesController@checkEditProjectSiteName')->name('checkEditProjectSiteName');
        
        Route::post('/get_company_client_list', 'BankPaymentApprovalController@get_company_client_list')->name('get_company_client_list');
        Route::post('/get_client_project_list', 'BankPaymentApprovalController@get_client_project_list')->name('get_client_project_list');
        Route::post('/get_project_sites_list', 'BankPaymentApprovalController@get_project_sites_list')->name('get_project_sites_list');

        //methods of WorkOffAttendanceRequestController
        Route::get('/holiday_work_attendance', 'WorkOffAttendanceRequestController@index')->name('holiday_work_attendance');
        Route::get('/get_workOff_attendance_request_list', 'WorkOffAttendanceRequestController@get_workOff_attendance_request_list')->name('get_workOff_attendance_request_list');
        Route::get('/add_attendance_request', 'WorkOffAttendanceRequestController@add_attendance_request')->name('add_attendance_request');
        Route::post('/insert_attendance_request', 'WorkOffAttendanceRequestController@insert_attendance_request')->name('insert_attendance_request');
        Route::get('/edit_attendance_request/{id}', 'WorkOffAttendanceRequestController@edit_attendance_request')->name('edit_attendance_request');
        Route::post('/update_attendance_request', 'WorkOffAttendanceRequestController@update_attendance_request')->name('update_attendance_request');

        Route::get('/get_workOff_attendance_request_all_list_ajax', 'WorkOffAttendanceRequestController@get_workOff_attendance_request_all_list_ajax')->name('get_workOff_attendance_request_all_list_ajax');
        Route::get('/work_off_all_attendance_history', 'WorkOffAttendanceRequestController@work_off_all_attendance_history')->name('work_off_all_attendance_history');
        Route::get('/cancel_request/{id}', 'WorkOffAttendanceRequestController@cancel_request')->name('cancel_request');

        Route::post('/approve_work_off_attendance_request', 'WorkOffAttendanceRequestController@approve_work_off_attendance_request')->name('approve_work_off_attendance_request');
        Route::post('/reject_work_off_attendance_request', 'WorkOffAttendanceRequestController@reject_work_off_attendance_request')->name('reject_work_off_attendance_request');

        Route::post('/check_holiday', 'WorkOffAttendanceRequestController@check_holiday')->name('check_holiday');

        //methods of RtgsRegisterController
        Route::get('/rtgs_register', 'RtgsRegisterController@index')->name('rtgs_register');
        Route::get('/add_rtgs_register', 'RtgsRegisterController@add_rtgs_register')->name('add_rtgs_register');

        Route::get('/get_rtgs_register_list', 'RtgsRegisterController@get_rtgs_register_list')->name('get_rtgs_register_list');
        Route::post('/insert_rtgs_register', 'RtgsRegisterController@insert_rtgs_register')->name('insert_rtgs_register');

        Route::post('/delete_rtgs', 'RtgsRegisterController@delete_rtgs')->name('delete_rtgs');
        Route::get('/change_rtgs_status/{id}/{status}', 'RtgsRegisterController@change_rtgs_status')->name('change_rtgs_status');
        Route::post('/signed_rtgs', 'RtgsRegisterController@signed_rtgs')->name('signed_rtgs');

        Route::any('/rtgs_use_report', 'RtgsRegisterController@rtgs_use_report')->name('rtgs_use_report');
        //09/06/2020
        Route::get('/blank_rtgs_list', 'RtgsRegisterController@blank_rtgs_list')->name('blank_rtgs_list');
        Route::get('/get_rtgs_blank_list', 'RtgsRegisterController@get_rtgs_blank_list')->name('get_rtgs_blank_list');
        Route::get('/used_rtgs_list', 'RtgsRegisterController@used_rtgs_list')->name('used_rtgs_list');
        Route::get('/get_used_rtgs_list', 'RtgsRegisterController@get_used_rtgs_list')->name('get_used_rtgs_list');
        Route::get('/signed_rtgs_list', 'RtgsRegisterController@signed_rtgs_list')->name('signed_rtgs_list');
        Route::get('/get_signed_rtgs_list', 'RtgsRegisterController@get_signed_rtgs_list')->name('get_signed_rtgs_list');
        Route::get('/add_signed_rtgs', 'RtgsRegisterController@add_signed_rtgs')->name('add_signed_rtgs');
        Route::post('/get_rtgs_ref', 'RtgsRegisterController@get_rtgs_ref')->name('get_rtgs_ref');
        Route::post('/get_unsigned_rtgs_list', 'RtgsRegisterController@get_unsigned_rtgs_list')->name('get_unsigned_rtgs_list');
        Route::post('/signed_rtgs_request', 'RtgsRegisterController@signed_rtgs_request')->name('signed_rtgs_request');
        Route::get('/signed_rtgs_approval_requests', 'RtgsRegisterController@signed_rtgs_approval_requests')->name('signed_rtgs_approval_requests');
        Route::get('/accept_approval_rtgs_ref/{id}', 'RtgsRegisterController@accept_approval_rtgs_ref')->name('accept_approval_rtgs_ref');
        Route::post('/reject_approval_rtgs_ref', 'RtgsRegisterController@reject_approval_rtgs_ref')->name('reject_approval_rtgs_ref');
        Route::get('/failed_rtgs_list', 'RtgsRegisterController@failed_rtgs_list')->name('failed_rtgs_list');
        Route::get('/get_failed_rtgs_list', 'RtgsRegisterController@get_failed_rtgs_list')->name('get_failed_rtgs_list');
        Route::get('/add_failed_rtgs', 'RtgsRegisterController@add_failed_rtgs')->name('add_failed_rtgs');
        Route::post('/get_unfailed_rtgs', 'RtgsRegisterController@get_unfailed_rtgs')->name('get_unfailed_rtgs');
        Route::post('/get_unfailed_rtgs_list', 'RtgsRegisterController@get_unfailed_rtgs_list')->name('get_unfailed_rtgs_list');
        Route::post('/update_failed_rtgs', 'RtgsRegisterController@update_failed_rtgs')->name('update_failed_rtgs');
        Route::post('/get_remaining_rtgs', 'RtgsRegisterController@get_remaining_rtgs')->name('get_remaining_rtgs');
        Route::post('/get_used_rtgs_ref_no_list', 'RtgsRegisterController@get_used_rtgs_ref_no_list')->name('get_used_rtgs_ref_no_list');
        Route::post('/get_used_rtgs_number_list', 'RtgsRegisterController@get_used_rtgs_number_list')->name('get_used_rtgs_number_list');

        //EmployeeInsuranceController by nishit
        Route::get('/employees_insurances', 'EmployeeInsuranceController@index')->name('employees_insurances');
        Route::get('/add_employee_insurance', 'EmployeeInsuranceController@add_employee_insurance')->name('add_employee_insurance');
        Route::post('/insert_employee_insurance', 'EmployeeInsuranceController@insert_employee_insurance')->name('insert_employee_insurance');
        Route::get('/expired_emp_insurances_list', 'EmployeeInsuranceController@expired_emp_insurances_list')->name('expired_emp_insurances_list');
        Route::get('/renew_expired_employee_insurance/{id}', 'EmployeeInsuranceController@renew_expired_employee_insurance')->name('renew_expired_employee_insurance');
        Route::post('/renewed_employee_insurance', 'EmployeeInsuranceController@renewed_employee_insurance')->name('renewed_employee_insurance');
        Route::post('/employee_company', 'EmployeeInsuranceController@employee_company')->name('employee_company');
        Route::get('/employee_insurances_history/{id}', 'EmployeeInsuranceController@employee_insurances_history')->name('employee_insurances_history');
        Route::post('/emp_insurance_reminder_dates', 'EmployeeInsuranceController@emp_insurance_reminder_dates')->name('emp_insurance_reminder_dates');

        Route::post('/employee_insurances_types', 'EmployeeInsuranceController@employee_insurances_types')->name('employee_insurances_types');
        Route::post('/get_insurance_upload_policy', 'EmployeeInsuranceController@get_insurance_upload_policy')->name('get_insurance_upload_policy');

        //RegistrySearch Controller
        Route::any('/registry_search', 'RegistrySearchController@registry_search')->name('registry_search');


        //SiteManagement Controller
        Route::get('/site_management/{company_id?}/{project_id?}', 'SiteManagementController@index')->name('site_management');
        Route::get('add_site_management', 'SiteManagementController@add_site_management')->name('add_site_management');
        Route::get('add_boq', 'SiteManagementController@add_boq')->name('add_boq');
        Route::post('insert_boq', 'SiteManagementController@insert_boq')->name('insert_boq');
        Route::get('edit_boq/{id}', 'SiteManagementController@edit_boq')->name('edit_boq');
        Route::post('update_boq', 'SiteManagementController@update_boq')->name('update_boq');
        Route::post('delete_boq_sub_item', 'SiteManagementController@delete_boq_sub_item')->name('delete_boq_sub_item');
        Route::get('daily_abstract', 'SiteManagementController@daily_abstract')->name('daily_abstract');
        Route::get('site_report', 'SiteManagementController@site_report')->name('site_report');
        Route::get('generate_boq_bill', 'SiteManagementController@generate_boq_bill')->name('generate_boq_bill');
        Route::post('boq_bill_create', 'SiteManagementController@boq_bill_create')->name('boq_bill_create');
        Route::get('generate_bill_invoice', 'SiteManagementController@generate_bill_invoice')->name('generate_bill_invoice');
        Route::post('get_bill_invoice', 'SiteManagementController@get_bill_invoice')->name('get_bill_invoice');
        Route::post('get_boq_bill_number', 'SiteManagementController@get_boq_bill_number')->name('get_boq_bill_number');
        Route::get('excess_saving', 'SiteManagementController@excess_saving')->name('excess_saving');
        Route::get('boq_design', 'SiteManagementController@boq_design')->name('boq_design');
        Route::get('add_boq_design', 'SiteManagementController@add_boq_design')->name('add_boq_design');
        Route::post('insert_boq_design', 'SiteManagementController@insert_boq_design')->name('insert_boq_design');
        Route::get('get_boq_design_list', 'SiteManagementController@get_boq_design_list')->name('get_boq_design_list');
        Route::get('boq_design_drawing/{id}', 'SiteManagementController@boq_design_drawing')->name('boq_design_drawing');
        Route::get('update_boq_design/{id}', 'SiteManagementController@update_boq_design')->name('update_boq_design');
        Route::post('edit_boq_design', 'SiteManagementController@edit_boq_design')->name('edit_boq_design');
        Route::post('get_itemno_block', 'SiteManagementController@get_itemno_block')->name('get_itemno_block');
        Route::post('add_bulk_boq', 'SiteManagementController@add_bulk_boq')->name('add_bulk_boq');


        //methods of MeetingController
        Route::get('/meeting', 'MeetingController@index')->name('meeting'); //
        Route::get('/get_meeting_list', 'MeetingController@get_meeting_list')->name('get_meeting_list'); //
        Route::get('/add_meeting', 'MeetingController@add_meeting')->name('add_meeting'); //
        Route::post('/insert_meeting', 'MeetingController@insert_meeting')->name('insert_meeting'); //
        Route::get('/edit_meeting/{id}', 'MeetingController@edit_meeting')->name('edit_meeting'); //
        Route::post('/update_meeting', 'MeetingController@update_meeting')->name('update_meeting'); //

        Route::get('/get_user_meeting_mom_list/{id}', 'MeetingController@get_user_meeting_mom_list')->name('get_user_meeting_mom_list'); //
        Route::get('/delete_meeting/{id}', 'MeetingController@delete_meeting')->name('delete_meeting'); //
        Route::get('/get_mom_user_list/{id}', 'MeetingController@get_mom_user_list')->name('get_mom_user_list'); //
        Route::post('/add_edit_meeting_mom', 'MeetingController@add_edit_meeting_mom')->name('add_edit_meeting_mom'); //
        //Tender start
        Route::get('/tender', 'TenderController@index')->name('tender');
        Route::get('/add_tender', 'TenderController@add_tender')->name('add_tender');
        Route::post('/save_tender', 'TenderController@save_tender')->name('save_tender');
        Route::get('/get_tender_list_all', 'TenderController@get_tender_list')->name('get_tender_list_all');
        Route::get('/delete_tender/{id}', 'TenderController@delete_tender')->name('delete_tender');
        Route::get('/edit_tender/{id}', 'TenderController@edit_tender')->name('edit_tender');
        Route::post('update_tender', 'TenderController@update_tender')->name('update_tender');
        Route::post('select_tender', 'TenderController@select_tender')->name('select_tender');
        //selected tender
        Route::get('/selected_tender', 'TenderController@selected_tender')->name('selected_tender');
        Route::get('/get_seleced_tender_list', 'TenderController@get_seleced_tender_list')->name('get_seleced_tender_list');

        Route::get('/edit_selected_tender/{id}', 'TenderController@edit_selected_tender')->name('edit_selected_tender');
        Route::post('save_tender_fee', 'TenderController@save_tender_fee')->name('save_tender_fee');
        Route::post('save_tender_emd', 'TenderController@save_tender_emd')->name('save_tender_emd');
        Route::post('tender_tech_eli_sub', 'TenderController@tender_tech_eli_sub')->name('tender_tech_eli_sub');
        Route::post('tender_fina_eli_sub', 'TenderController@tender_fina_eli_sub')->name('tender_fina_eli_sub');
        Route::post('delete_financial_file', 'TenderController@delete_financial_file')->name('delete_financial_file');
        Route::post('delete_technical_file', 'TenderController@delete_technical_file')->name('delete_technical_file');
        Route::post('change_technical_file', 'TenderController@change_technical_file')->name('change_technical_file');
        Route::post('change_financial_file', 'TenderController@change_financial_file')->name('change_financial_file');

        Route::post('save_tender_detail', 'TenderController@save_tender_detail')->name('save_tender_detail');
        Route::post('tender_pre_bid_meet', 'TenderController@tender_pre_bid_meet')->name('tender_pre_bid_meet');
        Route::post('delete_bid_document', 'TenderController@delete_bid_document')->name('delete_bid_document');
        Route::post('change_bid_document_file', 'TenderController@change_bid_document_file')->name('change_bid_document_file');

        //30/09/2020
          Route::post('tender_payment_request','TenderController@tender_payment_request')->name('tender_payment_request');
          
        //Other Communication
        Route::post('tender_other_communication', 'TenderController@tender_other_communication')->name('tender_other_communication');
        Route::post('delete_communication_document', 'TenderController@delete_communication_document')->name('delete_communication_document');
        Route::post('change_communication_document_file', 'TenderController@change_communication_document_file')->name('change_communication_document_file');

        //tender_condition_contract
        Route::post('tender_condition_contract', 'TenderController@tender_condition_contract')->name('tender_condition_contract');
        Route::post('delete_condition_document', 'TenderController@delete_condition_document')->name('delete_condition_document');
        Route::post('change_condition_file', 'TenderController@change_condition_file')->name('change_condition_file');

        //18/05/2020
        Route::post('get_techical_criteria', 'TenderController@get_techical_criteria')->name('get_techical_criteria');
        Route::post('get_financial_criteria', 'TenderController@get_financial_criteria')->name('get_financial_criteria');
        Route::post('get_pre_bid_meeting', 'TenderController@get_pre_bid_meeting')->name('get_pre_bid_meeting');
        Route::post('get_other_communication', 'TenderController@get_other_communication')->name('get_other_communication');
        Route::post('get_condition_contract', 'TenderController@get_condition_contract')->name('get_condition_contract');

        //physical_sub
        Route::post('save_tender_physical_sub', 'TenderController@save_tender_physical_sub')->name('save_tender_physical_sub');

        //download
        Route::get('downloadtechdoc/{id}', 'TenderController@downloadtechdoc')->name('downloadtechdoc');
        Route::get('downloadfinadoc/{id}', 'TenderController@downloadfinadoc')->name('downloadfinadoc');
        Route::get('downloadbiddoc/{id}', 'TenderController@downloadbiddoc')->name('downloadbiddoc');
        Route::get('downloadcommdoc/{id}', 'TenderController@downloadcommdoc')->name('downloadcommdoc');
        Route::get('downloadcondoc/{id}', 'TenderController@downloadcondoc')->name('downloadcondoc');


        Route::get('tender_submission', 'TenderController@tender_submission')->name('tender_submission');
        Route::get('edit_submission_tender/{id}', 'TenderController@edit_submission_tender')->name('edit_submission_tender');

        //tender_pattern
        Route::get('tender_pattern', 'TenderPatternController@index')->name('tender_pattern');
        Route::get('add_tender_pattern', 'TenderPatternController@add_tender_pattern')->name('add_tender_pattern');
        Route::post('/check_tender_pattern', 'TenderPatternController@check_tender_pattern')->name('check_tender_pattern');
        Route::post('/save_tender_pattern', 'TenderPatternController@save_tender_pattern')->name('save_tender_pattern');
        Route::get('/get_tender_pattern_list_all', 'TenderPatternController@get_tender_pattern_list')->name('get_tender_pattern_list_all');
        Route::get('/change_tender_pattern_status/{id}/{status}', 'TenderPatternController@change_tender_pattern_status')->name('change_tender_pattern_status');
        Route::get('/delete_tender_pattern/{id}', 'TenderPatternController@delete_tender_pattern')->name('delete_tender_pattern');
        Route::get('/edit_tender_pattern/{id}', 'TenderPatternController@edit_tender_pattern')->name('edit_tender_pattern');

        //tender_physical_submission
        Route::get('tender_physical_submission', 'TenderPhysicalSubmissionController@index')->name('tender_physical_submission');
        Route::get('get_tender_physical_sub_list_all', 'TenderPhysicalSubmissionController@get_tender_physical_sub_list')->name('get_tender_physical_sub_list_all');
        Route::get('/change_tender_physical_sub_status/{id}/{status}', 'TenderPhysicalSubmissionController@change_tender_physical_sub_status')->name('change_tender_physical_sub_status');
        Route::get('/delete_tender_physical_sub/{id}', 'TenderPhysicalSubmissionController@delete_tender_physical_sub')->name('delete_tender_physical_sub');
        Route::get('add_tender_physical_sub', 'TenderPhysicalSubmissionController@add_tender_physical_sub')->name('add_tender_physical_sub');
        Route::post('/check_tender_physical_mode', 'TenderPhysicalSubmissionController@check_tender_physical_mode')->name('check_tender_physical_mode');
        Route::post('/save_tender_physical_mode', 'TenderPhysicalSubmissionController@save_tender_physical_mode')->name('save_tender_physical_mode');
        Route::get('/edit_tender_physical_mode/{id}', 'TenderPhysicalSubmissionController@edit_tender_physical_mode')->name('edit_tender_physical_mode');


        //pre_bid_query_report
        Route::get('/pre_bid_query_report', 'TenderController@pre_bid_query_report')->name('pre_bid_query_report');
        Route::get('/get_prebid_query_tender_list', 'TenderController@get_prebid_query_tender_list')->name('get_prebid_query_tender_list');
        Route::get('/edit_prebid_query_tender/{id}', 'TenderController@edit_prebid_query_tender')->name('edit_prebid_query_tender');
        Route::post('tender_pre_bid_meet_query_point', 'TenderController@tender_pre_bid_meet_query_point')->name('tender_pre_bid_meet_query_point');
        Route::post('get_pre_bid_query', 'TenderController@get_pre_bid_query')->name('get_pre_bid_query');
        Route::post('get_corrigendum_list', 'TenderController@get_corrigendum_list')->name('get_corrigendum_list');
        Route::post('save_tender_corrigendum', 'TenderController@save_tender_corrigendum')->name('save_tender_corrigendum');
        Route::get('downloadcorrigendumdoc/{id}', 'TenderController@downloadcorrigendumdoc')->name('downloadcorrigendumdoc');

        //tender_opening_report
        Route::get('tender_opening_report', 'TenderController@tender_opening_report')->name('tender_opening_report');
        Route::get('edit_tender_opening_report/{id}', 'TenderController@edit_tender_opening_report')->name('edit_tender_opening_report');
        Route::post('get_opening_date', 'TenderController@get_opening_date')->name('get_opening_date');
        Route::post('save_opening_datetime', 'TenderController@save_opening_datetime')->name('save_opening_datetime');
        Route::post('delete_participated_bidder', 'TenderController@delete_participated_bidder')->name('delete_participated_bidder');

        Route::post('view_compairision_bidder', 'TenderController@view_compairision_bidder')->name('view_compairision_bidder');
        Route::post('get_compairision_bidder', 'TenderController@get_compairision_bidder')->name('get_compairision_bidder');
        Route::post('yourboqImportData', 'TenderController@yourboqImportData')->name('yourboqImportData');

        //priliminary
        //02/04/2020
        Route::post('save_tender_priliminary', 'TenderController@save_tender_priliminary')->name('save_tender_priliminary');
        Route::post('save_tender_submission', 'TenderController@save_tender_submission')->name('save_tender_submission');
        Route::get('downloadFeeDoc/{id}', 'TenderController@downloadFeeDoc')->name('downloadFeeDoc');
        Route::get('downloadEmdDoc/{id}', 'TenderController@downloadEmdDoc')->name('downloadEmdDoc');
        Route::post('tender_sub_prepare_tech', 'TenderController@tender_sub_prepare_tech')->name('tender_sub_prepare_tech');
        Route::post('tender_sub_uploaded_tech', 'TenderController@tender_sub_uploaded_tech')->name('tender_sub_uploaded_tech');
        //04/04/2020
        Route::post('get_tender_submission_tech', 'TenderController@get_tender_submission_tech')->name('get_tender_submission_tech');
        Route::get('downloadsubtechdoc/{id}', 'TenderController@downloadsubtechdoc')->name('downloadsubtechdoc');
        Route::post('tender_sub_prepare_fina', 'TenderController@tender_sub_prepare_fina')->name('tender_sub_prepare_fina');
        Route::post('tender_sub_uploaded_fina', 'TenderController@tender_sub_uploaded_fina')->name('tender_sub_uploaded_fina');
        Route::post('get_tender_submission_fina', 'TenderController@get_tender_submission_fina')->name('get_tender_submission_fina');
        Route::get('downloadsubfinadoc/{id}', 'TenderController@downloadsubfinadoc')->name('downloadsubfinadoc');
        Route::post('tender_sub_prepare_boq', 'TenderController@tender_sub_prepare_boq')->name('tender_sub_prepare_boq');
        Route::post('tender_sub_uploaded_boq', 'TenderController@tender_sub_uploaded_boq')->name('tender_sub_uploaded_boq');
        Route::post('get_tender_submission_boq', 'TenderController@get_tender_submission_boq')->name('get_tender_submission_boq');
        Route::get('downloadsubboqdoc/{id}', 'TenderController@downloadsubboqdoc')->name('downloadsubboqdoc');
        //08/05/2020
        Route::post('delete_prepare_technical_file', 'TenderController@delete_prepare_technical_file')->name('delete_prepare_technical_file');
        Route::post('delete_prepare_financial_file', 'TenderController@delete_prepare_financial_file')->name('delete_prepare_financial_file');
        Route::post('delete_prepare_boq_file', 'TenderController@delete_prepare_boq_file')->name('delete_prepare_boq_file');


        Route::get('get_opening_tender_list', 'TenderController@get_opening_tender_list')->name('get_opening_tender_list');
        Route::post('save_participated_bidder', 'TenderController@save_participated_bidder')->name('save_participated_bidder');
        Route::post('save_opening_status', 'TenderController@save_opening_status')->name('save_opening_status');
        Route::post('save_opening_commercial_status', 'TenderController@save_opening_commercial_status')->name('save_opening_commercial_status');
        Route::post('get_bidder', 'TenderController@get_bidder')->name('get_bidder');

        //05/04/2020
        Route::post('tender_opening_query_tech', 'TenderController@tender_opening_query_tech')->name('tender_opening_query_tech');
        Route::get('downloadtechQDdoc/{id}', 'TenderController@downloadtechQDdoc')->name('downloadtechQDdoc');
        Route::get('downloadtechQRdoc/{id}', 'TenderController@downloadtechQRdoc')->name('downloadtechQRdoc');
        Route::post('tender_opening_query_fina', 'TenderController@tender_opening_query_fina')->name('tender_opening_query_fina');
        Route::get('downloadfinaQDdoc/{id}', 'TenderController@downloadfinaQDdoc')->name('downloadfinaQDdoc');
        Route::get('downloadfinaQRdoc/{id}', 'TenderController@downloadfinaQRdoc')->name('downloadfinaQRdoc');

        Route::post('tender_submission_process', 'TenderController@tender_submission_process')->name('tender_submission_process');
        Route::get('get_submission_tender_list', 'TenderController@get_submission_tender_list')->name('get_submission_tender_list');
        Route::post('boqImportData', 'TenderController@boqImportData')->name('boqImportData');
        Route::get('sampleBOQUpload', 'TenderController@sampleBOQUpload')->name('sampleBOQUpload');
        Route::post('get_tender_winner', 'TenderController@get_tender_winner')->name('get_tender_winner');

        Route::post('get_opening_technical', 'TenderController@get_opening_technical')->name('get_opening_technical');
        Route::post('get_opening_financial', 'TenderController@get_opening_financial')->name('get_opening_financial');
        Route::get('downloadfinalsubdoc/{id}', 'TenderController@downloadfinalsubdoc')->name('downloadfinalsubdoc');

        Route::post('get_bidder_log', 'TenderController@get_bidder_log')->name('get_bidder_log');
        Route::post('get_bidder_log_detail', 'TenderController@get_bidder_log_detail')->name('get_bidder_log_detail');
        Route::get('downloadFeeRejectDoc/{id}', 'TenderController@downloadFeeRejectDoc')->name('downloadFeeRejectDoc');
        Route::get('downloadEmdRejectDoc/{id}', 'TenderController@downloadEmdRejectDoc')->name('downloadEmdRejectDoc');
        Route::get('downloadCommRejectDoc/{id}', 'TenderController@downloadCommRejectDoc')->name('downloadCommRejectDoc');
        Route::get('downloadTechRejectDoc/{id}', 'TenderController@downloadTechRejectDoc')->name('downloadTechRejectDoc');
        Route::get('downloadFinaRejectDoc/{id}', 'TenderController@downloadFinaRejectDoc')->name('downloadFinaRejectDoc');
        Route::get('tender_permission', 'TenderController@tender_permission')->name('tender_permission');
        Route::post('save_tender_permission', 'TenderController@save_tender_permission')->name('save_tender_permission');
        Route::post('save_tender_assign', 'TenderController@save_tender_assign')->name('save_tender_assign');

        Route::get('/tender_category', 'TenderCategoryController@index')->name('tender_category');
        Route::get('/add_tender_category', 'TenderCategoryController@add_tender_category')->name('add_tender_category');
        Route::get('/edit_tender_category/{id}', 'TenderCategoryController@edit_tender_category')->name('edit_tender_category');
        Route::get('/get_tender_category_list_all', 'TenderCategoryController@get_tender_category_list')->name('get_tender_category_list_all');
        Route::get('/change_tender_category_status/{id}/{status}', 'TenderCategoryController@change_tender_category_status')->name('change_tender_category_status');
        Route::get('/delete_tender_category/{id}', 'TenderCategoryController@delete_tender_category')->name('delete_tender_category');
        Route::post('/save_tender_category', 'TenderCategoryController@save_tender_category')->name('save_tender_category');
        Route::post('/check_tender_category', 'TenderCategoryController@check_tender_category')->name('check_tender_category');

        // Tender end
        //methods of SenderController
        Route::get('/sender', 'SenderController@index')->name('sender');
        Route::get('/add_sender', 'SenderController@add_sender')->name('add_sender');
        Route::post('/insert_sender', 'SenderController@insert_sender')->name('insert_sender');
        Route::get('/change_sender_status/{id}/{status}', 'SenderController@change_sender_status')->name('change_sender_status');
        Route::get('/edit_sender/{id}', 'SenderController@edit_sender')->name('edit_sender');
        Route::post('/update_sender', 'SenderController@update_sender')->name('update_sender');
        Route::get('/delete_sender/{id}', 'SenderController@delete_sender')->name('delete_sender');

        Route::get('/delivery_mode', 'DeliveryModeController@index')->name('delivery_mode');
        Route::get('/change_delivery_mode_status/{id}/{status}', 'DeliveryModeController@change_delivery_mode_status')->name('change_delivery_mode_status');
        Route::get('/delete_delivery_mode/{id}', 'DeliveryModeController@delete_delivery_mode')->name('delete_delivery_mode');
        Route::post('/add_delivery_mode', 'DeliveryModeController@add_delivery_mode')->name('add_delivery_mode');
        Route::get('/edit_delivery_mode/{id}', 'DeliveryModeController@edit_delivery_mode')->name('edit_delivery_mode');
        Route::post('/update_delivery_mode', 'DeliveryModeController@update_delivery_mode')->name('update_delivery_mode');


        //BankChargeCategoryController
        Route::get('/bank_charge_category', 'BankChargeCategoryController@index')->name('bank_charge_category');
        Route::get('/get_bank_charge_table_list', 'BankChargeCategoryController@get_bank_charge_table_list')->name('get_bank_charge_table_list');
        Route::get('/add_bank_charge_category', 'BankChargeCategoryController@add_bank_charge_category')->name('add_bank_charge_category');
        Route::post('/save_bank_charge', 'BankChargeCategoryController@save_bank_charge')->name('save_bank_charge');
        Route::get('/edit_bank_charge_category/{id}', 'BankChargeCategoryController@edit_bank_charge_category')->name('edit_bank_charge_category');

        // BankChargeSubCategoryController
        Route::get('/bank_charge_sub_category', 'BankChargeSubCategoryController@index')->name('bank_charge_sub_category');
        Route::get('/get_bank_sub_charge_table_list', 'BankChargeSubCategoryController@get_bank_sub_charge_table_list')->name('get_bank_sub_charge_table_list');
        Route::get('/add_bank_charge_sub_category', 'BankChargeSubCategoryController@add_bank_charge_sub_category')->name('add_bank_charge_sub_category');
        Route::post('/save_bank_charge_sub_category', 'BankChargeSubCategoryController@save_bank_charge_sub_category')->name('save_bank_charge_sub_category');
        Route::get('/edit_bank_charge_sub_category/{id}', 'BankChargeSubCategoryController@edit_bank_charge_sub_category')->name('edit_bank_charge_sub_category');


        //CompanyDocumentManagementController methods
        Route::any('/company_document_management', 'CompanyDocumentManagementController@company_document_management')->name('company_document_management');
        Route::get('/get_company_documet_management', 'CompanyDocumentManagementController@get_company_documet_management')->name('get_company_documet_management');
        Route::any('/add_company_document_management', 'CompanyDocumentManagementController@add_company_document_management')->name('add_company_document_management');
        Route::post('/insert_company_document_management', 'CompanyDocumentManagementController@insert_company_document_management')->name('insert_company_document_management');
        Route::get('/edit_company_document_management/{id}', 'CompanyDocumentManagementController@edit_company_document_management')->name('edit_company_document_management');
        Route::post('/update_company_document_management', 'CompanyDocumentManagementController@update_company_document_management')->name('update_company_document_management');

        //CompanyDocumentRequestController methods
        Route::get('/company_document_request', 'CompanyDocumentRequestController@index')->name('company_document_request');
        Route::get('/get_company_document_request', 'CompanyDocumentRequestController@get_company_document_request')->name('get_company_document_request');
        Route::get('/get_company_document_management', 'CompanyDocumentRequestController@get_company_document_management')->name('get_company_document_management');
        Route::get('/add_company_document_request', 'CompanyDocumentRequestController@add_company_document_request')->name('add_company_document_request');
        Route::post('/insert_company_document_request', 'CompanyDocumentRequestController@insert_company_document_request')->name('insert_company_document_request');
        Route::get('/edit_company_document_request/{id}', 'CompanyDocumentRequestController@edit_company_document_request')->name('edit_company_document_request');
        Route::post('/update_company_document_request', 'CompanyDocumentRequestController@update_company_document_request')->name('update_company_document_request');
        Route::post('/reject_company_document_request', 'CompanyDocumentRequestController@reject_company_document_request')->name('reject_company_document_request');
        Route::post('/approve_company_document_request_by_admin', 'CompanyDocumentRequestController@approve_company_document_request_by_admin')->name('approve_company_document_request_by_admin');
        Route::get('/approve_company_document_request_by_custodian/{id}', 'CompanyDocumentRequestController@approve_company_document_request_by_custodian')->name('approve_company_document_request_by_custodian');
        Route::get('/received_company_document_by_requester/{id}', 'CompanyDocumentRequestController@received_company_document_by_requester')->name('received_company_document_by_requester');
        Route::get('/returned_company_document_by_requester/{id}', 'CompanyDocumentRequestController@returned_company_document_by_requester')->name('returned_company_document_by_requester');
        Route::get('/received_company_document_by_custodian/{id}', 'CompanyDocumentRequestController@received_company_document_by_custodian')->name('received_company_document_by_custodian');
        Route::get('/delete_company_document_request/{id}', 'CompanyDocumentRequestController@delete_company_document_request')->name('delete_company_document_request');



        //VoucherNumberRegisterController
        Route::get('/voucher_number_book', 'VoucherNumberRegisterController@index')->name('voucher_number_book');
        Route::get('/get_blank_voucher_number_list', 'VoucherNumberRegisterController@get_blank_voucher_number_list')->name('get_blank_voucher_number_list');
        Route::get('/add_voucher_number', 'VoucherNumberRegisterController@add_voucher_number')->name('add_voucher_number');
        Route::post('/insert_voucher_number', 'VoucherNumberRegisterController@insert_voucher_number')->name('insert_voucher_number');
        Route::get('/used_voucher_number', 'VoucherNumberRegisterController@used_voucher_number')->name('used_voucher_number');
        Route::get('/get_used_voucher_number_list', 'VoucherNumberRegisterController@get_used_voucher_number_list')->name('get_used_voucher_number_list');
        Route::get('/failed_voucher_number', 'VoucherNumberRegisterController@failed_voucher_number')->name('failed_voucher_number');
        Route::get('/get_failed_voucher_number_list', 'VoucherNumberRegisterController@get_failed_voucher_number_list')->name('get_failed_voucher_number_list');
        Route::get('/assign_voucher_number', 'VoucherNumberRegisterController@assign_voucher_number')->name('assign_voucher_number');
        Route::get('/assign_user_voucher', 'VoucherNumberRegisterController@assign_user_voucher')->name('assign_user_voucher');
        Route::post('/assign_voucher_touser', 'VoucherNumberRegisterController@assign_voucher_touser')->name('assign_voucher_touser');
        Route::get('/accept_voucher_user/{id}', 'VoucherNumberRegisterController@accept_voucher_user')->name('accept_voucher_user');
        Route::get('/reject_voucher_user/{id}', 'VoucherNumberRegisterController@reject_voucher_user')->name('reject_voucher_user');

        //22/06/2020
        Route::get('add_special_permission', 'SpecialModulePermissionController@add_special_permission')->name('add_special_permission');
        Route::post('save_special_permission', 'SpecialModulePermissionController@save_special_permission')->name('save_special_permission');

        //02/07/2020
        Route::post('/get_cashNewApproval', 'CashApprovalController@get_cashNewApproval')->name('get_cashNewApproval');
        Route::post('/get_unfailed_voucher', 'CashApprovalController@get_unfailed_voucher')->name('get_unfailed_voucher');
        Route::post('/approve_cashNewPaymentByAccountant', 'CashApprovalController@approve_cashNewPaymentByAccountant')->name('approve_cashNewPaymentByAccountant');


        //06/07/2020
        Route::get('/cancel_cheque_list', 'ChequeRegisterController@cancel_cheque_list')->name('cancel_cheque_list');
        Route::get('/add_cancel_cheque', 'ChequeRegisterController@add_cancel_cheque')->name('add_cancel_cheque');
        Route::post('/update_cancel_cheque', 'ChequeRegisterController@update_cancel_cheque')->name('update_cancel_cheque');
        Route::get('/get_cancel_cheque_list', 'ChequeRegisterController@get_cancel_cheque_list')->name('get_cancel_cheque_list');

        //CompliencCategoryController
        Route::get('/compliance_category', 'CompliencCategoryController@index')->name('compliance_category');
        Route::get('/change_compliance_status/{id}/{status}', 'CompliencCategoryController@change_compliance_status')->name('change_compliance_status');
        Route::get('/add_compliance_category', 'CompliencCategoryController@add_compliance_category')->name('add_compliance_category');
        Route::post('/insert_compliance_category', 'CompliencCategoryController@insert_compliance_category')->name('insert_compliance_category');
        Route::get('/edit_compliance_category/{id}', 'CompliencCategoryController@edit_compliance_category')->name('edit_compliance_category');
        Route::post('/update_compliance_category', 'CompliencCategoryController@update_compliance_category')->name('update_compliance_category');

        //complienceReminderController
        Route::get('/compliance_reminders', 'complienceReminderController@index')->name('compliance_reminders');
        Route::get('/add_compliance_reminder', 'complienceReminderController@add_compliance_reminder')->name('add_compliance_reminder');
        Route::post('/insert_compliance_reminder', 'complienceReminderController@insert_compliance_reminder')->name('insert_compliance_reminder');
        Route::get('/edit_compliance_reminder/{id}', 'complienceReminderController@edit_compliance_reminder')->name('edit_compliance_reminder');
        Route::post('/update_compliance_reminder', 'complienceReminderController@update_compliance_reminder')->name('update_compliance_reminder');

        Route::get('/complience_reminder_list', 'complienceReminderController@complience_reminder_list')->name('complience_reminder_list');
        // Route::get('/complete_compliance_reminder/{id}/{type}', 'complienceReminderController@complete_compliance_reminder')->name('complete_compliance_reminder');
        Route::post('/complete_compliance_reminder', 'complienceReminderController@complete_compliance_reminder')->name('complete_compliance_reminder');

        //09/07/2020
        Route::get('/company_cash_management', 'CompanyCashManagementController@index')->name('company_cash_management');
        Route::get('/get_company_cash_list', 'CompanyCashManagementController@get_company_cash_list')->name('get_company_cash_list');
        Route::get('/add_company_cash', 'CompanyCashManagementController@add_company_cash')->name('add_company_cash');
        Route::post('/insert_company_cash', 'CompanyCashManagementController@insert_company_cash')->name('insert_company_cash');

        //29/07/2020
        Route::get('/edit_company_cash/{id}', 'CompanyCashManagementController@edit_company_cash')->name('edit_company_cash');
        Route::post('/update_company_cash', 'CompanyCashManagementController@update_company_cash')->name('update_company_cash');

        Route::get('/add_cash_transfer', 'CompanyCashManagementController@add_cash_transfer')->name('add_cash_transfer');
        //Route::post('/insert_cash_transfer', 'CompanyCashManagementController@insert_cash_transfer')->name('insert_cash_transfer');
        Route::any('/cash_transfer_list', 'CompanyCashManagementController@cash_transfer_list')->name('cash_transfer_list');

        Route::get('/add_employee_cash_transfer', 'CompanyCashManagementController@add_employee_cash_transfer')->name('add_employee_cash_transfer');
        Route::post('/insert_emplyee_cash_transfer', 'CompanyCashManagementController@insert_emplyee_cash_transfer')->name('insert_emplyee_cash_transfer');


        Route::get('/employee_cash_management', 'EmployeeCashManagementController@index')->name('employee_cash_management');
        Route::get('/get_employee_cash_list', 'EmployeeCashManagementController@get_employee_cash_list')->name('get_employee_cash_list');
        Route::get('/confirm_employee_cash/{id}', 'EmployeeCashManagementController@confirm_employee_cash')->name('confirm_employee_cash');

//01/09/2020
        Route::get('/company_to_company_cash_transfer', 'CompanyCashManagementController@company_to_company_cash_transfer')->name('company_to_company_cash_transfer');
        Route::get('/company_to_employee_cash_transfer', 'CompanyCashManagementController@company_to_employee_cash_transfer')->name('company_to_employee_cash_transfer');
        Route::post('/insert_company_to_company_cash_transfer', 'CompanyCashManagementController@insert_company_to_company_cash_transfer')->name('insert_company_to_company_cash_transfer');
        Route::post('/insert_company_to_employee_cash_transfer', 'CompanyCashManagementController@insert_company_to_employee_cash_transfer')->name('insert_company_to_employee_cash_transfer');

        // TDS Section
        Route::get('/tds_section', 'TdsSectionController@index')->name('tds_section');
        Route::get('/add_tds_section', 'TdsSectionController@add_tds_section')->name('add_tds_section');
        Route::post('/save_tds_section', 'TdsSectionController@save_tds_section')->name('save_tds_section');
        Route::get('/change_tds_section_status/{id}/{status}', 'TdsSectionController@change_tds_section_status')->name('change_tds_section_status');
        Route::get('/edit_tds_section/{id}', 'TdsSectionController@edit_tds_section')->name('edit_tds_section');

        //03/09/2020
        Route::post('/insert_attendance_approval', 'AttendanceController@insert_attendance_approval')->name('insert_attendance_approval');

        //08/09/2020
        Route::post('/get_loginuser_project_list', 'EmployeeExpenseController@get_loginuser_project_list')->name('get_loginuser_project_list');

        //09/09/2020
        Route::get('/change_project_type/{id}/{status}', 'ProjectController@change_project_type')->name('change_project_type');
        Route::post('/close_meeting', 'MeetingController@close_meeting')->name('close_meeting');

        Route::post('/get_vendor_payments', 'DashboardController@get_vendor_payments')->name('get_vendor_payments');
        //17/09/2020
        Route::post('/get_payroll_details', 'AttendanceController@get_payroll_details')->name('get_payroll_details');
        Route::post('/submit_payments_details', 'AttendanceController@submit_payments_details')->name('submit_payments_details');
        Route::post('/get_all_cheque_ref_list', 'ChequeRegisterController@get_all_cheque_ref_list')->name('get_all_cheque_ref_list');
        Route::post('/get_signedUnfailed_cheque_list', 'ChequeRegisterController@get_signedUnfailed_cheque_list')->name('get_signedUnfailed_cheque_list');
        Route::post('/get_company_bank_list_ajax', 'AttendanceController@get_company_bank_list_ajax')->name('get_company_bank_list_ajax');

        //17/09/2020
        Route::post('/get_loan_payment_details', 'EmployeeLoanController@get_loan_payment_details')->name('get_loan_payment_details');
        Route::post('/submit_loan_payments_details', 'EmployeeLoanController@submit_loan_payments_details')->name('submit_loan_payments_details');

        Route::get('/delete_bank_payment/{id}', 'BankPaymentApprovalController@delete_bank_payment')->name('delete_bank_payment');
        Route::get('/delete_cash_payment/{id}', 'CashApprovalController@delete_cash_payment')->name('delete_cash_payment');
        Route::get('/delete_online_payment/{id}', 'OnlinePaymentApprovalController@delete_online_payment')->name('delete_online_payment');

        //Stationery Module
        Route::get('/stationery_items', 'StationeryController@stationery_items')->name('stationery_items');
        Route::get('/stationery_items_list_ajax', 'StationeryController@stationery_items_list_ajax')->name('stationery_items_list_ajax');
        Route::get('/add_stationery_items', 'StationeryController@add_stationery_items')->name('add_stationery_items');
        Route::post('/insert_stationery_items', 'StationeryController@insert_stationery_items')->name('insert_stationery_items');
        Route::get('/edit_stationery_items/{id}', 'StationeryController@edit_stationery_items')->name('edit_stationery_items');
        Route::post('update_stationery_items', 'StationeryController@update_stationery_items')->name('update_stationery_items');
        Route::get('/delete_stationery_items/{id}', 'StationeryController@delete_stationery_items')->name('delete_stationery_items');
        Route::get('/change_stationery_item_status/{id}/{status}', 'StationeryController@change_stationery_item_status')->name('change_stationery_item_status');

        Route::get('/stationery_access_requests', 'StationeryController@stationery_access_requests')->name('stationery_access_requests');
        Route::get('/stationery_access_requests_list_ajax', 'StationeryController@stationery_access_requests_list_ajax')->name('stationery_access_requests_list_ajax');
        Route::get('/add_stationery_item_access_request', 'StationeryController@add_stationery_item_access_request')->name('add_stationery_item_access_request');
        Route::post('/insert_stationery_item_access_request', 'StationeryController@insert_stationery_item_access_request')->name('insert_stationery_item_access_request');
        Route::get('/edit_stationery_item_access_request/{id}', 'StationeryController@edit_stationery_item_access_request')->name('edit_stationery_item_access_request');
        Route::post('update_stationery_item_access_request', 'StationeryController@update_stationery_item_access_request')->name('update_stationery_item_access_request');
        Route::get('/delete_stationery_item_access_request/{id}', 'StationeryController@delete_stationery_item_access_request')->name('delete_stationery_item_access_request');

        Route::get('/accept_stationery_item_access_request/{id}/{status}', 'StationeryController@accept_stationery_item_access_request')->name('accept_stationery_item_access_request');
        Route::get('/return_stationery_item/{id}', 'StationeryController@return_stationery_item')->name('return_stationery_item');
        Route::get('/confirm_stationery_item_access_request/{id}/{type}', 'StationeryController@confirm_stationery_item_access_request')->name('confirm_stationery_item_access_request');


        Route::get('/softcopy_document_category', 'SoftcopyDocumentCategoryController@softcopy_document_category_list')->name('softcopy_document_category');
        Route::get('/change_softcopy_document_status/{id}/{status}', 'SoftcopyDocumentCategoryController@change_softcopy_document_status')->name('change_softcopy_document_status');
        Route::post('/add_softcopy_document_category', 'SoftcopyDocumentCategoryController@add_softcopy_document_category')->name('add_softcopy_document_category');
        Route::get('/edit_softcopy_document_category/{id}', 'SoftcopyDocumentCategoryController@edit_softcopy_document_category')->name('edit_softcopy_document_category');
        Route::post('/update_softcopy_docume-nt_category', 'SoftcopyDocumentCategoryController@update_softcopy_document_category')->name('update_softcopy_document_category');
        //Route::get('/delete_softcopy_document_category/{id}', 'SoftcopyDocumentCategoryController@delete_softcopy_document_category')->name('delete_softcopy_document_category');
        //SoftcopyRequestController methods
        Route::get('/softcopy_request_sent', 'SoftcopyRequestController@softcopy_request_sent')->name('softcopy_request_sent');
        Route::get('/sent_softcopy_request', 'SoftcopyRequestController@sent_softcopy_request')->name('sent_softcopy_request');
        Route::get('/softcopy_request_received', 'SoftcopyRequestController@softcopy_request_received')->name('softcopy_request_received');
        Route::get('/get_softcopy_request', 'SoftcopyRequestController@get_softcopy_request')->name('get_softcopy_request');
        Route::get('/add_softcopy_request', 'SoftcopyRequestController@add_softcopy_request')->name('add_softcopy_request');
        Route::post('/insert_softcopy_request', 'SoftcopyRequestController@insert_softcopy_request')->name('insert_softcopy_request');
        Route::get('/edit_softcopy_request/{id}', 'SoftcopyRequestController@edit_softcopy_request')->name('edit_softcopy_request');
        Route::post('/update_softcopy_request', 'SoftcopyRequestController@update_softcopy_request')->name('update_softcopy_request');
        Route::post('/reject_softcopy_request', 'SoftcopyRequestController@reject_softcopy_request')->name('reject_softcopy_request');
        Route::post('/send_softcopy', 'SoftcopyRequestController@send_softcopy')->name('send_softcopy');
        Route::get('/delete_softcopy_request/{id}', 'SoftcopyRequestController@delete_softcopy_request')->name('delete_softcopy_request');

        // Methods  of documentsignature ;"22-04-2021" under apsir r
        Route::get('/documentsignature', 'DocumentSignatureController@index')->name('documentsignature');
        Route::get('/add_modules', 'DocumentSignatureController@add_modules')->name('add_modules');

        //Nishit 14/05/2021


        //Inventory stock management
        Route::get('/inventory_stock_requests', 'StationaryStockInventoryController@inventory_stock_requests')->name('inventory_stock_requests');
        Route::get('/inventory_stock_requests_list_ajax', 'StationaryStockInventoryController@inventory_stock_requests_list_ajax')->name('inventory_stock_requests_list_ajax');
        Route::get('/add_inventory_stock_request', 'StationaryStockInventoryController@add_inventory_stock_request')->name('add_inventory_stock_request');
        Route::post('/insert_inventory_stock_request', 'StationaryStockInventoryController@insert_inventory_stock_request')->name('insert_inventory_stock_request');
        Route::get('/stock_request_approval/{id}', 'StationaryStockInventoryController@stock_request_approval')->name('stock_request_approval');
        Route::post('/purchase_completion', 'StationaryStockInventoryController@purchase_completion')->name('purchase_completion');
        Route::get('/purchase_cofirmed_by_inventory_manager/{id}', 'StationaryStockInventoryController@purchase_cofirmed_by_inventory_manager')->name('purchase_cofirmed_by_inventory_manager');
        Route::post('/get_purchase_proof_details', 'StationaryStockInventoryController@get_purchase_proof_details')->name('get_purchase_proof_details');
        //Inventory Items
        Route::get('/stationary_items', 'StationaryItemsController@index')->name('stationary_items');
        Route::get('/inventory_items_list_ajax', 'StationaryItemsController@inventory_items_list_ajax')->name('inventory_items_list_ajax');
        Route::get('/add_inventory_item', 'StationaryItemsController@add_inventory_item')->name('add_inventory_item');
        Route::post('/insert_inventory_item', 'StationaryItemsController@insert_inventory_item')->name('insert_inventory_item');
        //13/05/2021
        Route::get('/item_access_request_list/{id}', 'StationaryItemsController@item_access_request_list')->name('item_access_request_list');
        Route::get('/item_access_request_list_ajax', 'StationaryItemsController@item_access_request_list_ajax')->name('item_access_request_list_ajax');
        Route::post('/access_request_approval', 'StationaryItemsController@access_request_approval')->name('access_request_approval');
        Route::post('/access_request_rejection', 'StationaryItemsController@access_request_rejection')->name('access_request_rejection');
        Route::post('/add_returnItem_to_stock', 'StationeryController@add_returnItem_to_stock')->name('add_returnItem_to_stock');
        Route::get('/confirm_returnItem_to_stock/{itemid}/{id}', 'StationaryItemsController@confirm_returnItem_to_stock')->name('confirm_returnItem_to_stock');
        Route::get('/item_return_request_list/{id}', 'StationaryItemsController@item_return_request_list')->name('item_return_request_list');
        Route::get('/item_return_request_list_ajax', 'StationaryItemsController@item_return_request_list_ajax')->name('item_return_request_list_ajax');
        //Inventory manager
        Route::get('/add_inventory_managers', 'InventoryManagerController@add_inventory_managers')->name('add_inventory_managers');
        Route::post('/save_manager_types', 'InventoryManagerController@save_manager_types')->name('save_manager_types');



    });
});
