<?php

//https://documenter.getpostman.com/view/1030609/S1Lr5WmW
use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

Route::name('api.')->prefix('v1')->namespace('Api')->group(function () {

    //LoginController methods
    Route::post('/login', 'LoginController@authenticate')->name('login');
    Route::post('/logout', 'LoginController@logout')->name('logout');
    Route::post('/forgot_password', 'LoginController@forgot_password')->name('forgot_password');
    Route::post('/resend_otp', 'LoginController@resend_otp')->name('resend_otp');
    Route::post('/update_forgot_password', 'LoginController@update_forgot_password')->name('update_forgot_password');
    Route::post('/verify_user', 'LoginController@verify_user')->name('verify_user');
    Route::get('/get_app_update', 'LoginController@get_app_update')->name('get_app_update');
    Route::get('/update_app_version/{version}','LoginController@update_app_version')->name('update_app_version');
    //Route::post('/check_imei', 'LoginController@check_imei')->name('check_imei');
    Route::post('/get_attendance_request_data','MantraDeviceController@get_attendance_request_data')->name('get_attendance_request_data');
    Route::post('/device_hello','MantraDeviceController@device_hello')->name('device_hello');

    Route::post('/update_device_id', 'LoginController@update_device_id')->name('update_device_id');
    //UserController methods    
    Route::group(['middleware' => ['api_auth']], function() {
    
    	//ChatController methods
        Route::post('/get_contact_list','ChatController@get_contact_list')->name('get_contact_list');
    	Route::post('/get_chat_msg','ChatController@get_chat_msg')->name('get_chat_msg');
	Route::post('/chat_file_upload','ChatController@chat_file_upload')->name('chat_file_upload');
	Route::post('/getUnreadChatCount','ChatController@getUnreadChatCount')->name('getUnreadChatCount');
		
        //UserController methods
        Route::post('/get_profile', 'UsersController@get_profile')->name('get_profile');
        Route::post('/edit_profile', 'UsersController@edit_profile')->name('edit_profile');
        Route::post('/change_password', 'UsersController@change_password')->name('change_password');
        Route::post('/get_user_permissions', 'UsersController@get_user_permissions')->name('get_user_permissions');
        Route::post('/get_my_reportees', 'UsersController@get_my_reportees')->name('get_my_reportees');
        Route::post('/get_my_team', 'UsersController@get_my_team')->name('get_my_team');
        Route::post('/get_emp_details', 'UsersController@get_emp_details')->name('get_emp_details');
        Route::post('/get_department', 'UsersController@get_department')->name('get_department');
        Route::post('/get_salary_slip', 'UsersController@get_salary_slip')->name('get_salary_slip');
         Route::post('/check_imei', 'UsersController@check_imei')->name('check_imei');

        //LeaveController Methods
        Route::post('/apply_leave', 'LeaveController@apply_leave')->name('apply_leave');
        Route::post('/leave_statistics', 'LeaveController@leave_statistics')->name('leave_statistics');
        Route::post('/get_leaves', 'LeaveController@get_leaves')->name('get_leaves');
        Route::post('/get_leave_detail', 'LeaveController@get_leave_detail')->name('get_leave_detail');
        Route::post('/get_leave_category', 'LeaveController@get_leave_category')->name('get_leave_category');
        Route::post('/team_leaves', 'LeaveController@team_leaves')->name('team_leaves');
        Route::post('/get_leave_approval_list', 'LeaveController@get_leave_approval_list')->name('get_leave_approval_list');
        Route::post('/approve_leave', 'LeaveController@approve_leave')->name('approve_leave');
        Route::post('/reject_leave', 'LeaveController@reject_leave')->name('reject_leave');
        Route::post('/cancel_leave', 'LeaveController@cancel_leave')->name('cancel_leave');
        Route::post('/get_leave_assigned_work', 'LeaveController@get_leave_assigned_work')->name('get_leave_assigned_work');
        Route::post('/approve_reject_assigned_work', 'LeaveController@approve_reject_assigned_work')->name('approve_reject_assigned_work');
        Route::post('/reassign_work_again', 'LeaveController@reassign_work_again')->name('reassign_work_again');
        Route::post('/get_category_app_display', 'LeaveController@get_category_app_display')->name('get_category_app_display');
        Route::post('/get_all_pending_leave', 'LeaveController@get_all_pending_leave')->name('get_all_pending_leave');

        //AttendanceController Methods
        Route::post('/get_attendance_statistics', 'AttendanceController@get_attendance_statistics')->name('get_attendance_statistics');
        Route::post('/get_attendance_detail', 'AttendanceController@get_attendance_detail')->name('get_attendance_detail');
        Route::post('/remote_attendance_punch', 'AttendanceController@remote_attendance_punch')->name('remote_attendance_punch');
        Route::post('/get_last_attendance_activity', 'AttendanceController@get_last_attendance_activity')->name('get_last_attendance_activity');
        Route::post('/get_approval_attendance_list', 'AttendanceController@get_approval_attendance_list')->name('get_approval_attendance_list');
        Route::post('/approve_remote_attendance', 'AttendanceController@approve_remote_attendance')->name('approve_remote_attendance');
        Route::post('/reject_remote_attendance', 'AttendanceController@reject_remote_attendance')->name('reject_remote_attendance');
        Route::post('/onDutyAttendanceRequest', 'AttendanceController@onDutyAttendanceRequest')->name('onDutyAttendanceRequest');
        Route::post('/get_place_list', 'AttendanceController@get_place_list')->name('get_place_list');
        Route::post('/attendance_request', 'AttendanceController@attendance_request')->name('attendance_request');
        Route::post('/get_request_list', 'AttendanceController@get_request_list')->name('get_request_list');
        Route::post('/get_request_approval', 'AttendanceController@get_request_approval')->name('get_request_approval');
        Route::post('/get_request_reject', 'AttendanceController@get_request_reject')->name('get_request_reject');
        Route::post('/get_request_cancel', 'AttendanceController@get_request_cancel')->name('get_request_cancel');
        Route::post('/get_attendace_request_by_user', 'AttendanceController@get_attendace_request_by_user')->name('get_attendace_request_by_user');
        Route::post('/check_clockin_show', 'AttendanceController@check_clockin_show')->name('check_clockin_show');
        Route::post('/get_unused_attend_request', 'AttendanceController@get_unused_attend_request')->name('get_unused_attend_request');
        Route::post('/remote_attendance_map','AttendanceController@remote_attendance_map')->name('remote_attendance_map');
        
        Route::post('/add_travel', 'TravelController@add_travel')->name('add_travel');
        Route::post('/update_travel', 'TravelController@update_travel')->name('update_travel');
        Route::post('/get_travel_details', 'TravelController@get_travel_details')->name('get_travel_details');
        Route::post('/get_all_travel', 'TravelController@get_all_travel')->name('get_all_travel');

        Route::post('/add_hotel', 'HotelController@add_hotel')->name('add_hotel');
        Route::post('/update_hotel', 'HotelController@update_hotel')->name('update_hotel');
        Route::post('/get_hotel_details', 'HotelController@get_hotel_details')->name('get_hotel_details');
        Route::post('/get_all_hotel', 'HotelController@get_all_hotel')->name('get_all_hotel');
        
        //ExpenseController Methods
        Route::post('/get_expense_category', 'ExpenseController@get_expense_category')->name('get_expense_category');
        Route::post('/get_my_expense', 'ExpenseController@get_my_expense')->name('get_my_expense');
        Route::post('/add_expense', 'ExpenseController@add_expense')->name('add_expense');
        Route::post('/get_all_expense', 'ExpenseController@get_all_expense')->name('get_all_expense');
        Route::post('/delete_expense', 'ExpenseController@delete_expense')->name('delete_expense');
        Route::post('/edit_expense', 'ExpenseController@edit_expense')->name('edit_expense');
        Route::post('/approve_reject_expense', 'ExpenseController@approve_reject_expense')->name('approve_reject_expense');
        Route::post('/add_driver_expense', 'ExpenseController@add_driver_expense')->name('add_driver_expense');
        Route::post('/get_expense_list_by_driver', 'ExpenseController@get_expense_list_by_driver')->name('get_expense_list_by_driver');
        Route::post('/edit_driver_expense', 'ExpenseController@edit_driver_expense')->name('edit_driver_expense');
        Route::post('/delete_driver_expense', 'ExpenseController@delete_driver_expense')->name('delete_driver_expense');
        Route::post('/get_driver_expense_approval_list', 'ExpenseController@get_driver_expense_approval_list')->name('get_driver_expense_approval_list');
        Route::post('/reject_driver_expense', 'ExpenseController@reject_driver_expense')->name('reject_driver_expense');
        Route::post('/approve_driver_expense', 'ExpenseController@approve_driver_expense')->name('approve_driver_expense');
        Route::post('/get_vehicle_list', 'ExpenseController@get_vehicle_list')->name('get_vehicle_list');
        Route::post('/get_driver_expense_approvel_user', 'ExpenseController@get_driver_expense_approvel_user')->name('get_driver_expense_approvel_user');
        Route::post('/get_previous_meter_reading', 'ExpenseController@get_previous_meter_reading')->name('get_previous_meter_reading');
		Route::post('/company_bank_list', 'ExpenseController@company_bank_list')->name('company_bank_list');
        Route::post('/bank_cheques_list', 'ExpenseController@bank_cheques_list')->name('bank_cheques_list');
		Route::post('/bank_rtgs_list', 'ExpenseController@bank_rtgs_list')->name('bank_rtgs_list');
        Route::post('/get_voucher_ref_number','ExpenseController@get_voucher_ref_number')->name('get_voucher_ref_number');
        Route::post('/get_voucher_number', 'ExpenseController@get_voucher_number')->name('get_voucher_number');
		//10-08-2020
        Route::post('/get_bank_cheque_rtgs_reff_list', 'ExpenseController@get_bank_cheque_rtgs_reff_list')->name('get_bank_cheque_rtgs_reff_list');
        Route::post('/get_cheque_number', 'ExpenseController@get_cheque_number')->name('get_cheque_number');
        Route::post('/get_rtgs_number', 'ExpenseController@get_rtgs_number')->name('get_rtgs_number');
        //AnnouncementController methods
        Route::post('/get_announcement_list', 'AnnouncementController@get_announcement_list')->name('get_announcement_list');
        Route::post('/add_announcement_details', 'AnnouncementController@add_announcement_details')->name('add_announcement_details');
        Route::post('/edit_announcement_details', 'AnnouncementController@edit_announcement_details')->name('edit_announcement_details');
        Route::post('/delete_announcement_details', 'AnnouncementController@delete_announcement_details')->name('delete_announcement_details');
        Route::post('/announcement_users_list', 'AnnouncementController@announcement_users_list')->name('announcement_users_list');
        Route::post('/get_my_announcement_list','AnnouncementController@get_my_announcement_list')->name('get_my_announcement_list');
        //ApprovalController methods
        Route::post('/get_bankpayment_approval_list', 'ApprovalController@get_bankpayment_approval_list')->name('get_bankpayment_approval_list');
        Route::post('/approve_bank_payment', 'ApprovalController@approve_bank_payment')->name('approve_bank_payment');
        Route::post('/reject_bank_payment', 'ApprovalController@reject_bank_payment')->name('reject_bank_payment');
        Route::post('/get_cash_approval_list', 'ApprovalController@get_cash_approval_list')->name('get_cash_approval_list');
        Route::post('/approve_cash', 'ApprovalController@approve_cash')->name('approve_cash');
        Route::post('/reject_cash_approval', 'ApprovalController@reject_cash_approval')->name('reject_cash_approval');
        Route::post('/get_budgetsheet_approval_list', 'ApprovalController@get_budgetsheet_approval_list')->name('get_budgetsheet_approval_list');
        Route::post('/approve_budget_sheet', 'ApprovalController@approve_budget_sheet')->name('approve_budget_sheet');
        Route::post('/reject_budget_sheet', 'ApprovalController@reject_budget_sheet')->name('reject_budget_sheet');
        Route::post('/pre_sign_letterhead_approval_list', 'ApprovalController@pre_sign_letterhead_approval_list')->name('pre_sign_letterhead_approval_list');
        Route::post('/approve_pre_sign_letter', 'ApprovalController@approve_pre_sign_letter')->name('approve_pre_sign_letter');
        Route::post('/reject_pre_sign_letter', 'ApprovalController@reject_pre_sign_letter')->name('reject_pre_sign_letter');
        Route::post('/letterhead_approval_list', 'ApprovalController@letterhead_approval_list')->name('letterhead_approval_list');
        Route::post('/approve_letter_head', 'ApprovalController@approve_letter_head')->name('approve_letter_head');
        Route::post('/get_approved_letter_head_request', 'ApprovalController@get_approved_letter_head_request')->name('get_approved_letter_head_request');
        Route::post('/reject_letter_head', 'ApprovalController@reject_letter_head')->name('reject_letter_head');
        Route::post('/issuing_presigned_letter_head', 'ApprovalController@issuing_presigned_letter_head')->name('issuing_presigned_letter_head');
        Route::post('/issuing_letter_head', 'ApprovalController@issuing_letter_head')->name('issuing_letter_head');
        Route::post('/approval_count', 'ApprovalController@approval_count')->name('approval_count');
        Route::post('/salary_approval_list', 'ApprovalController@salary_approval_list')->name('salary_approval_list');
        Route::post('/approve_salary', 'ApprovalController@approve_salary')->name('approve_salary');
        Route::post('/reject_salary', 'ApprovalController@reject_salary')->name('reject_salary');
        Route::post('/trip_approval_list', 'ApprovalController@trip_approval_list')->name('trip_approval_list');
        Route::post('/approve_trip', 'ApprovalController@approve_trip')->name('approve_trip');
        Route::post('/reject_trip', 'ApprovalController@reject_trip')->name('reject_trip');
        Route::post('/get_hold_budget_sheet_list','ApprovalController@get_hold_budget_sheet_list')->name('get_hold_budget_sheet_list');
        Route::post('/complete_hold_amt','ApprovalController@complete_hold_amt')->name('complete_hold_amt');
         Route::post('/employee_loan_list', 'ApprovalController@employee_loan_list')->name('employee_loan_list');
        
         Route::post('/get_interview_approval_list', 'ApprovalController@get_interview_approval_list')->name('get_interview_approval_list');
		Route::post('/get_hold_interview_list', 'ApprovalController@get_hold_interview_list')->name('get_hold_interview_list');
         Route::post('/interviewIsOnHold', 'ApprovalController@interviewIsOnHold')->name('interviewIsOnHold');
         Route::post('/approve_emp_loan', 'ApprovalController@approve_emp_loan')->name('approve_emp_loan');
         Route::post('/reject_emp_loan', 'ApprovalController@reject_emp_loan')->name('reject_emp_loan');
          Route::post('/interview_action', 'ApprovalController@interview_action')->name('interview_action');
		  Route::post('/get_Onlinepayment_approval_list', 'ApprovalController@get_Onlinepayment_approval_list')->name('get_Onlinepayment_approval_list');
        Route::post('/approve_online_payment', 'ApprovalController@approve_online_payment')->name('approve_online_payment');
        Route::post('/reject_online_payment', 'ApprovalController@reject_online_payment')->name('reject_online_payment');
		Route::post('/get_VehicleMaintenance_approval_list', 'ApprovalController@get_VehicleMaintenance_approval_list')->name('get_VehicleMaintenance_approval_list');
     Route::post('/approve_vehicle_maintenance', 'ApprovalController@approve_vehicle_maintenance')->name('approve_vehicle_maintenance');
     Route::post('/reject_vehicle_maintenance', 'ApprovalController@reject_vehicle_maintenance')->name('reject_vehicle_maintenance');
		Route::post('/approve_all_salary','ApprovalController@approve_all_salary')->name('approve_all_salary');

          Route::post('/get_signed_cheque_approval_requests', 'ApprovalController@get_signed_cheque_approval_requests')->name('get_signed_cheque_approval_requests');
        Route::post('/accept_signed_cheque_approval_request', 'ApprovalController@accept_signed_cheque_approval_request')->name('accept_signed_cheque_approval_request');
        Route::post('/reject_signed_cheque_approval_request', 'ApprovalController@reject_signed_cheque_approval_request')->name('reject_signed_cheque_approval_request');
        Route::post('/get_signed_rtgs_approval_requests', 'ApprovalController@get_signed_rtgs_approval_requests')->name('get_signed_rtgs_approval_requests');
        Route::post('/accept_signed_rtgs_approval_request', 'ApprovalController@accept_signed_rtgs_approval_request')->name('accept_signed_rtgs_approval_request');
        Route::post('/reject_signed_rtgs_approval_request', 'ApprovalController@reject_signed_rtgs_approval_request')->name('reject_signed_rtgs_approval_request');
        
		  //16/06/2020
        Route::post('/get_signed_letterhead_approval_requests', 'ApprovalController@get_signed_letterhead_approval_requests')->name('get_signed_letterhead_approval_requests');
        Route::post('/accept_letterhead_cheque_approval_request', 'ApprovalController@accept_letterhead_cheque_approval_request')->name('accept_letterhead_cheque_approval_request');
        Route::post('/reject_signed_letterhead_approval_request', 'ApprovalController@reject_signed_letterhead_approval_request')->name('reject_signed_letterhead_approval_request');
        
        
        //TripController methods
        Route::post('/add_trip_opening', 'TripController@add_trip_opening')->name('add_trip_opening');
        Route::post('/get_trip_list_by_driver', 'TripController@get_trip_list_by_driver')->name('get_trip_list_by_driver');
        Route::post('/edit_trip', 'TripController@edit_trip')->name('edit_trip');
        Route::post('/add_trip_closing', 'TripController@add_trip_closing')->name('add_trip_closing');

        //Asset_assignController methods
        Route::post('/get_my_asset_list', 'Asset_assignController@get_my_asset_list')->name('get_my_asset_list');
        Route::post('/get_assigned_asset_list', 'Asset_assignController@get_assigned_asset_list')->name('get_assigned_asset_list');
        Route::post('/accept_asset', 'Asset_assignController@accept_asset')->name('accept_asset');
        Route::post('/reject_assign_asset', 'Asset_assignController@reject_assign_asset')->name('reject_assign_asset');
        Route::post('/get_all_asset_list', 'Asset_assignController@get_all_asset_list')->name('get_all_asset_list');
        Route::post('/re_assign_asset', 'Asset_assignController@re_assign_asset')->name('re_assign_asset');
		Route::post('/hr_asset_assign_requests', 'Asset_assignController@hr_asset_assign_requests')->name('hr_asset_assign_requests');
  Route::post('/hr_asset_assign_request_count', 'Asset_assignController@hr_asset_assign_request_count')->name('hr_asset_assign_request_count');
        //Inward_outwardController Methods
        Route::post('/get_inward_list', 'Inward_outwardController@get_inward_list')->name('get_inward_list');
        Route::post('/get_Category_list', 'Inward_outwardController@get_Category_list')->name('get_Category_list');
        Route::post('/get_registry_list', 'Inward_outwardController@get_registry_list')->name('get_registry_list');
        Route::post('/get_department_list', 'Inward_outwardController@get_department_list')->name('get_department_list');
        Route::post('/get_depart_user_list', 'Inward_outwardController@get_depart_user_list')->name('get_depart_user_list');
        Route::post('/get_Company_list', 'Inward_outwardController@get_Company_list')->name('get_Company_list');
        Route::post('/get_company_project_list', 'Inward_outwardController@get_company_project_list')->name('get_company_project_list');
        Route::post('/add_inwards', 'Inward_outwardController@add_inwards')->name('add_inwards');
        Route::post('/get_outward_list', 'Inward_outwardController@get_outward_list')->name('get_outward_list');
        Route::post('/add_outwards', 'Inward_outwardController@add_outwards')->name('add_outwards');
        Route::post('/get_registry_category', 'Inward_outwardController@get_registry_category')->name('get_registry_category');
        Route::post('/view_registry', 'Inward_outwardController@view_registry')->name('view_registry');
        Route::post('/pass_registry', 'Inward_outwardController@pass_registry')->name('pass_registry');
        Route::post('/get_unread_registry', 'Inward_outwardController@get_unread_registry')->name('get_unread_registry');
        Route::post('/mark_read_registry', 'Inward_outwardController@mark_read_registry')->name('mark_read_registry');
        Route::post('/chat_send_message', 'Inward_outwardController@chat_send_message')->name('chat_send_message');
        Route::post('/chat_messages', 'Inward_outwardController@chat_messages')->name('chat_messages');
        Route::post('/get_unread_messages', 'Inward_outwardController@get_unread_messages')->name('get_unread_messages');
        Route::post('/mark_read_messages', 'Inward_outwardController@mark_read_messages')->name('mark_read_messages');
		Route::post('/department_user_with_registry','Inward_outwardController@department_user_with_registry')->name('department_user_with_registry');
		Route::post('/get_registry_old_user_list','Inward_outwardController@get_registry_old_user_list')->name('get_registry_old_user_list');
		Route::post('/get_doc_sub_category','Inward_outwardController@get_doc_sub_category')->name('get_doc_sub_category');
		Route::post('/pending_registry_documents', 'Inward_outwardController@pending_registry_documents')->name('pending_registry_documents');
                Route::post('/approved_inwards_documents', 'Inward_outwardController@approved_inwards_documents')->name('approved_inwards_documents');
                Route::post('/approved_outwards_documents', 'Inward_outwardController@approved_outwards_documents')->name('approved_outwards_documents');
                Route::post('/mark_approve_documnet', 'Inward_outwardController@mark_approve_documnet')->name('mark_approve_documnet');

 //03/06/2020
                Route::post('/get_assignee_registry', 'Inward_outwardController@get_assignee_registry')->name('get_assignee_registry');  
                Route::post('/accept_assignee_registry', 'Inward_outwardController@accept_assignee_registry')->name('accept_assignee_registry');    
                Route::post('/reject_assignee_registry', 'Inward_outwardController@reject_assignee_registry')->name('reject_assignee_registry');
                Route::post('/get_prime_user_registry', 'Inward_outwardController@get_prime_user_registry')->name('get_prime_user_registry');  
                Route::post('/accept_requestByPrimeUser', 'Inward_outwardController@accept_requestByPrimeUser')->name('accept_requestByPrimeUser');    
                Route::post('/reject_requestByPrimeUser', 'Inward_outwardController@reject_requestByPrimeUser')->name('reject_requestByPrimeUser');  
                Route::post('/get_support_user_registry', 'Inward_outwardController@get_support_user_registry')->name('get_support_user_registry');  
                Route::post('/accept_requestBySupportEmp', 'Inward_outwardController@accept_requestBySupportEmp')->name('accept_requestBySupportEmp');    
                Route::post('/reject_requestBySupportEmp', 'Inward_outwardController@reject_requestBySupportEmp')->name('reject_requestBySupportEmp');

                 Route::post('/registry_module_count', 'Inward_outwardController@registry_module_count')->name('registry_module_count'); 
                //04/06/2020
                Route::post('/get_rejected_support_emp_entry', 'Inward_outwardController@get_rejected_support_emp_entry')->name('get_rejected_support_emp_entry');    
                Route::post('/acceptEmpRequest', 'Inward_outwardController@acceptEmpRequest')->name('acceptEmpRequest');  
                Route::post('/rejectEmpRequest', 'Inward_outwardController@rejectEmpRequest')->name('rejectEmpRequest');  
                Route::post('/removeEmpFromRegistry', 'Inward_outwardController@removeEmpFromRegistry')->name('removeEmpFromRegistry');    
                
                //16/06/2020
               Route::post('/get_today_inward_list', 'Inward_outwardController@get_today_inward_list')->name('get_today_inward_list'); 
               Route::post('/get_today_outward_list', 'Inward_outwardController@get_today_outward_list')->name('get_today_outward_list'); 
		
        //ReportsController methods
        Route::post('/leave_report','ReportsController@leave_report')->name('leave_report');
        Route::post('/attendance_report', 'ReportsController@attendance_report')->name('attendance_report');
        Route::post('/driver_expense_report', 'ReportsController@driver_expense_report')->name('driver_expense_report');
        Route::post('/regular_expense_report', 'ReportsController@regular_expense_report')->name('regular_expense_report');
        Route::post('/driver_trip_report', 'ReportsController@driver_trip_report')->name('driver_trip_report');
        Route::post('/assets_report', 'ReportsController@assets_report')->name('assets_report');
        Route::post('/cash_approval_report', 'ReportsController@cash_approval_report')->name('cash_approval_report');
        Route::post('/bank_payment_approval_report', 'ReportsController@bank_payment_approval_report')->name('bank_payment_approval_report');
        Route::post('/budget_sheet_weeks_arr', 'ReportsController@budget_sheet_weeks_arr')->name('budget_sheet_weeks_arr');
        Route::post('/budget_sheet_approvals_report', 'ReportsController@budget_sheet_approvals_report')->name('budget_sheet_approvals_report');
        Route::post('/salary_report', 'ReportsController@salary_report')->name('salary_report');
               Route::post('/travel_expense_report', 'ReportsController@travel_expense_report')->name('travel_expense_report');
               Route::post('/hotel_expense_report', 'ReportsController@hotel_expense_report')->name('hotel_expense_report');

        Route::post('/employee_loan_report', 'ReportsController@employee_loan_report')->name('employee_loan_report');
        Route::post('/cheque_register_report', 'ReportsController@cheque_register_report')->name('cheque_register_report');
        Route::post('/letter_head_register_report', 'ReportsController@letter_head_register_report')->name('letter_head_register_report');
        Route::post('/online_payment_approvals_report', 'ReportsController@online_payment_approvals_report')->name('online_payment_approvals_report');
		Route::post('/vehicle_maintenance_approvals_report', 'ReportsController@vehicle_maintenance_approvals_report')->name('vehicle_maintenance_approvals_report');
         Route::post('/rtgs_register_report', 'ReportsController@rtgs_register_report')->name('rtgs_register_report');
		//Route::post('/get_user_lists', 'UsersController@get_user_lists')->name('get_user_lists');
        //Route::post('/reset_virtual_money', 'UsersController@reset_virtual_money')->name('reset_virtual_money');
        //Route::post('/get_reset_page_data','UsersController@get_reset_page_data')->name('get_reset_page_data');              
		
		Route::post('/get_company_client_list', 'ExpenseController@get_company_client_list')->name('get_company_client_list');
        Route::post('/get_client_project_list', 'ExpenseController@get_client_project_list')->name('get_client_project_list');
        Route::post('/get_project_sites_list', 'ExpenseController@get_project_sites_list')->name('get_project_sites_list');
		
		//methods of WorkOffAttendanceRequestController
        Route::post('/get_userWorkOffAttendanceRequest', 'WorkOffAttendanceRequestController@get_userWorkOffAttendanceRequest')->name('get_userWorkOffAttendanceRequest');
        Route::post('/get_allWorkOffAttendanceRequest', 'WorkOffAttendanceRequestController@get_allWorkOffAttendanceRequest')->name('get_allWorkOffAttendanceRequest');
        Route::post('/add_attendance_request', 'WorkOffAttendanceRequestController@add_attendance_request')->name('add_attendance_request');
        Route::post('/cancel_request', 'WorkOffAttendanceRequestController@cancel_request')->name('cancel_request');
        Route::post('/edit_attendance_request', 'WorkOffAttendanceRequestController@edit_attendance_request')->name('edit_attendance_request');
        Route::post('/approve_reject_request', 'WorkOffAttendanceRequestController@approve_reject_request')->name('approve_reject_request');
        Route::post('/check_holiday', 'WorkOffAttendanceRequestController@check_holiday')->name('check_holiday');
		
		
		//ResignController
	Route::post('/list_of_resign_user_list', 'ResignController@list_of_resign_user_list')->name('list_of_resign_user_list');
    Route::post('/retain_resign_employee', 'ResignController@retain_resign_employee')->name('retain_resign_employee');
    Route::post('/approve_resign_employee', 'ResignController@approve_resign_employee')->name('approve_resign_employee');
		
		
		//MeetingController
		Route::post('/add_meeting', 'MeetingController@add_meeting')->name('add_meeting');
    Route::post('/edit_meeting', 'MeetingController@edit_meeting')->name('edit_meeting');
    Route::post('/get_meeting_user_list', 'MeetingController@get_meeting_user_list')->name('get_meeting_user_list');
    Route::post('/add_edit_meeting_mom', 'MeetingController@add_edit_meeting_mom')->name('add_edit_meeting_mom');
    Route::post('/get_all_user_meeting_mom_details', 'MeetingController@get_all_user_meeting_mom_details')->name('get_all_user_meeting_mom_details');
    Route::post('/get_user_meeting_mom_list', 'MeetingController@get_user_meeting_mom_list')->name('get_user_meeting_mom_list');
    
    Route::post('/accept_meeting_request', 'MeetingController@accept_meeting_request')->name('accept_meeting_request');
    Route::post('/reject_meeting_request', 'MeetingController@reject_meeting_request')->name('reject_meeting_request');
	Route::post('/get_meeting_list', 'MeetingController@get_meeting_list')->name('get_meeting_list');

	        Route::post('/all_meeting_list', 'MeetingController@all_meeting_list')->name('all_meeting_list');

	        //ModuleEntryApprovalController
        Route::post('/api_entry_approval_count', 'ModuleEntryApprovalController@api_entry_approval_count')->name('api_entry_approval_count');
        Route::post('/api_vendor_entry_approval_list', 'ModuleEntryApprovalController@api_vendor_entry_approval_list')->name('api_vendor_entry_approval_list');
        Route::post('/api_accept_vendor_entry', 'ModuleEntryApprovalController@api_accept_vendor_entry')->name('api_accept_vendor_entry');
        Route::post('/api_reject_vendor_entry', 'ModuleEntryApprovalController@api_reject_vendor_entry')->name('api_reject_vendor_entry');

         Route::post('/api_company_entry_approval_list', 'ModuleEntryApprovalController@api_company_entry_approval_list')->name('api_company_entry_approval_list');
        Route::post('/api_accept_company_entry', 'ModuleEntryApprovalController@api_accept_company_entry')->name('api_accept_company_entry');
        Route::post('/api_reject_company_entry', 'ModuleEntryApprovalController@api_reject_company_entry')->name('api_reject_company_entry');

        Route::post('/api_client_entry_approval_list', 'ModuleEntryApprovalController@api_client_entry_approval_list')->name('api_client_entry_approval_list');
        Route::post('/api_accept_client_entry', 'ModuleEntryApprovalController@api_accept_client_entry')->name('api_accept_client_entry');
        Route::post('/api_reject_client_entry', 'ModuleEntryApprovalController@api_reject_client_entry')->name('api_reject_client_entry');
        
        Route::post('/api_project_entry_approval_list', 'ModuleEntryApprovalController@api_project_entry_approval_list')->name('api_project_entry_approval_list');
        Route::post('/api_accept_project_entry', 'ModuleEntryApprovalController@api_accept_project_entry')->name('api_accept_project_entry');
        Route::post('/api_reject_project_entry', 'ModuleEntryApprovalController@api_reject_project_entry')->name('api_reject_project_entry');

        Route::post('/api_vendor_bank_entry_approval_list', 'ModuleEntryApprovalController@api_vendor_bank_entry_approval_list')->name('api_vendor_bank_entry_approval_list');
        Route::post('/api_accept_vendor_bank_entry', 'ModuleEntryApprovalController@api_accept_vendor_bank_entry')->name('api_accept_vendor_bank_entry');
        Route::post('/api_reject_vendor_bank_entry', 'ModuleEntryApprovalController@api_reject_vendor_bank_entry')->name('api_reject_vendor_bank_entry');
        
        Route::post('/api_project_site_entry_approval_list', 'ModuleEntryApprovalController@api_project_site_entry_approval_list')->name('api_project_site_entry_approval_list');
        Route::post('/api_accept_project_site_entry', 'ModuleEntryApprovalController@api_accept_project_site_entry')->name('api_accept_project_site_entry');
        Route::post('/api_reject_project_site_entry', 'ModuleEntryApprovalController@api_reject_project_site_entry')->name('api_reject_project_site_entry');

        Route::post('/api_bank_entry_approval_list', 'ModuleEntryApprovalController@api_bank_entry_approval_list')->name('api_bank_entry_approval_list');
        Route::post('/api_accept_bank_entry', 'ModuleEntryApprovalController@api_accept_bank_entry')->name('api_accept_bank_entry');
        Route::post('/api_reject_bank_entry', 'ModuleEntryApprovalController@api_reject_bank_entry')->name('api_reject_bank_entry');

        Route::post('/api_bank_charge_category_entry_approval_list', 'ModuleEntryApprovalController@api_bank_charge_category_entry_approval_list')->name('api_bank_charge_category_entry_approval_list');
        Route::post('/api_accept_bank_charge_category_entry', 'ModuleEntryApprovalController@api_accept_bank_charge_category_entry')->name('api_accept_bank_charge_category_entry');
        Route::post('/api_reject_bank_charge_category_entry', 'ModuleEntryApprovalController@api_reject_bank_charge_category_entry')->name('api_reject_bank_charge_category_entry');

        Route::post('/api_bank_charge_sub_category_entry_approval_list', 'ModuleEntryApprovalController@api_bank_charge_sub_category_entry_approval_list')->name('api_bank_charge_sub_category_entry_approval_list');
        Route::post('/api_accept_bank_charge_sub_category_entry', 'ModuleEntryApprovalController@api_accept_bank_charge_sub_category_entry')->name('api_accept_bank_charge_sub_category_entry');
        Route::post('/api_reject_bank_charge_sub_category_entry', 'ModuleEntryApprovalController@api_reject_bank_charge_sub_category_entry')->name('api_reject_bank_charge_sub_category_entry');

        Route::post('/api_payment_card_entry_approval_list', 'ModuleEntryApprovalController@api_payment_card_entry_approval_list')->name('api_payment_card_entry_approval_list');
        Route::post('/api_accept_payment_card_entry', 'ModuleEntryApprovalController@api_accept_payment_card_entry')->name('api_accept_payment_card_entry');
        Route::post('/api_reject_payment_card_entry', 'ModuleEntryApprovalController@api_reject_payment_card_entry')->name('api_reject_payment_card_entry');

         Route::post('/api_company_document_entry_approval_list', 'ModuleEntryApprovalController@api_company_document_entry_approval_list')->name('api_company_document_entry_approval_list');
        Route::post('/api_accept_company_document_entry', 'ModuleEntryApprovalController@api_accept_company_document_entry')->name('api_accept_company_document_entry');
        Route::post('/api_reject_company_document_entry', 'ModuleEntryApprovalController@api_reject_company_document_entry')->name('api_reject_company_document_entry');
        
        Route::post('/api_tender_category_entry_approval_list', 'ModuleEntryApprovalController@api_tender_category_entry_approval_list')->name('api_tender_category_entry_approval_list');
        Route::post('/api_accept_tender_category_entry', 'ModuleEntryApprovalController@api_accept_tender_category_entry')->name('api_accept_tender_category_entry');
        Route::post('/api_reject_tender_category_entry', 'ModuleEntryApprovalController@api_reject_tender_category_entry')->name('api_reject_tender_category_entry');

        Route::post('/api_tender_pattern_entry_approval_list', 'ModuleEntryApprovalController@api_tender_pattern_entry_approval_list')->name('api_tender_pattern_entry_approval_list');
        Route::post('/api_accept_tender_pattern_entry', 'ModuleEntryApprovalController@api_accept_tender_pattern_entry')->name('api_accept_tender_pattern_entry');
        Route::post('/api_reject_tender_pattern_entry', 'ModuleEntryApprovalController@api_reject_tender_pattern_entry')->name('api_reject_tender_pattern_entry');

        Route::post('/api_tender_physical_submission_entry_approval_list', 'ModuleEntryApprovalController@api_tender_physical_submission_entry_approval_list')->name('api_tender_physical_submission_entry_approval_list');
        Route::post('/api_accept_tender_physical_submission_entry', 'ModuleEntryApprovalController@api_accept_tender_physical_submission_entry')->name('api_accept_tender_physical_submission_entry');
        Route::post('/api_reject_tender_physical_submission_entry', 'ModuleEntryApprovalController@api_reject_tender_physical_submission_entry')->name('api_reject_tender_physical_submission_entry');

        Route::post('/api_regitry_category_entry_approval_list', 'ModuleEntryApprovalController@api_regitry_category_entry_approval_list')->name('api_regitry_category_entry_approval_list');
        Route::post('/api_accept_regitry_category_entry', 'ModuleEntryApprovalController@api_accept_regitry_category_entry')->name('api_accept_regitry_category_entry');
        Route::post('/api_reject_regitry_category_entry', 'ModuleEntryApprovalController@api_reject_regitry_category_entry')->name('api_reject_regitry_category_entry');

        Route::post('/api_regitry_subcategory_entry_approval_list', 'ModuleEntryApprovalController@api_regitry_subcategory_entry_approval_list')->name('api_regitry_subcategory_entry_approval_list');
        Route::post('/api_accept_regitry_subcategory_entry', 'ModuleEntryApprovalController@api_accept_regitry_subcategory_entry')->name('api_accept_regitry_subcategory_entry');
        Route::post('/api_reject_regitry_subcategory_entry', 'ModuleEntryApprovalController@api_reject_regitry_subcategory_entry')->name('api_reject_regitry_subcategory_entry');

        Route::post('/api_delivery_mode_entry_approval_list', 'ModuleEntryApprovalController@api_delivery_mode_entry_approval_list')->name('api_delivery_mode_entry_approval_list');
        Route::post('/api_accept_delivery_mode_entry', 'ModuleEntryApprovalController@api_accept_delivery_mode_entry')->name('api_accept_delivery_mode_entry');
        Route::post('/api_reject_delivery_mode_entry', 'ModuleEntryApprovalController@api_reject_delivery_mode_entry')->name('api_reject_delivery_mode_entry');

        Route::post('/api_sender_category_entry_approval_list', 'ModuleEntryApprovalController@api_sender_category_entry_approval_list')->name('api_sender_category_entry_approval_list');
        Route::post('/api_accept_sender_category_entry', 'ModuleEntryApprovalController@api_accept_sender_category_entry')->name('api_accept_sender_category_entry');
        Route::post('/api_reject_sender_category_entry', 'ModuleEntryApprovalController@api_reject_sender_category_entry')->name('api_reject_sender_category_entry');

        //21-08-2020
        Route::post('/api_sender_tds_section_entry_approval_list', 'ModuleEntryApprovalController@api_sender_tds_section_entry_approval_list')->name('api_sender_tds_section_entry_approval_list');
        Route::post('/api_accept_tds_section_entry', 'ModuleEntryApprovalController@api_accept_tds_section_entry')->name('api_accept_tds_section_entry');
        Route::post('/api_reject_tds_section_entry', 'ModuleEntryApprovalController@api_reject_tds_section_entry')->name('api_reject_tds_section_entry');
		
    });
    Route::post('/get_user_list', 'UsersController@get_user_list')->name('get_user_list');
    Route::post('/get_upcomings', 'EmployeeController@get_upcomings')->name('get_upcomings');
    Route::post('/get_holiday', 'LeaveController@get_holiday')->name('get_holiday');
    Route::post('/get_upcoming_holiday', 'LeaveController@get_upcoming_holiday')->name('get_upcoming_holiday');
	
	//SitemanageController methods
	Route::post('/add_work_progress','SitemanagementController@add_work_progress')->name('add_work_progress');
    Route::post('/site_company_list','SitemanagementController@site_company_list')->name('site_company_list');
    Route::post('/site_project_list','SitemanagementController@site_project_list')->name('site_project_list');
    Route::post('/get_project_boq','SitemanagementController@get_project_boq')->name('get_project_boq');
    Route::post('/get_work_progress','SitemanagementController@get_work_progress')->name('get_work_progress');
    Route::post('/insert_block_measurement', 'SitemanagementController@insert_block_measurement')->name('insert_block_measurement');
    Route::post('/edit_block_measurement', 'SitemanagementController@edit_block_measurement')->name('edit_block_measurement');
    Route::post('/work_shortfall_reason', 'SitemanagementController@work_shortfall_reason')->name('work_shortfall_reason');
    Route::post('/get_measurement', 'SitemanagementController@get_measurement')->name('get_measurement');
	
	// TenderController methods
    Route::post('/get_all_tender', 'TenderController@get_all_tender')->name('get_all_tender');
    Route::post('/get_selected_tender', 'TenderController@get_selected_tender')->name('get_selected_tender');
    Route::post('/get_submission_tender', 'TenderController@get_submission_tender')->name('get_submission_tender');
    Route::post('/get_opening_tender', 'TenderController@get_opening_tender')->name('get_opening_tender');
    Route::post('/get_compairision_commercial', 'TenderController@get_compairision_commercial')->name('get_compairision_commercial');
    Route::post('/select_tender', 'TenderController@select_tender')->name('select_tender');
    Route::post('/get_assign_user', 'TenderController@get_assign_user')->name('get_assign_user');
	//Route::get('/test', 'UsersController@test')->name('test');
    //Route::post('/delete_user', 'UsersController@delete_user')->name('delete_user');

    //CompanyDocumentRequestController methods
    Route::post('/get_admin_pending_request','CompanyDocumentRequestController@get_admin_pending_request')->name('get_admin_pending_request');
    Route::post('/get_custodian_pending_request','CompanyDocumentRequestController@get_custodian_pending_request')->name('get_custodian_pending_request');
    Route::post('/get_requester_pending_received','CompanyDocumentRequestController@get_requester_pending_received')->name('get_requester_pending_received');
    Route::post('/get_requester_pending_returned','CompanyDocumentRequestController@get_requester_pending_returned')->name('get_requester_pending_returned');
    Route::post('/get_custodian_pending_received','CompanyDocumentRequestController@get_custodian_pending_received')->name('get_custodian_pending_received');
    Route::post('/approve_company_document_request_by_admin','CompanyDocumentRequestController@approve_company_document_request_by_admin')->name('approve_company_document_request_by_admin');
    Route::post('/approve_company_document_request_by_custodian','CompanyDocumentRequestController@approve_company_document_request_by_custodian')->name('approve_company_document_request_by_custodian');
    Route::post('/received_company_document_by_requester','CompanyDocumentRequestController@received_company_document_by_requester')->name('received_company_document_by_requester');
    Route::post('/returned_company_document_by_requester','CompanyDocumentRequestController@returned_company_document_by_requester')->name('returned_company_document_by_requester');
    Route::post('/received_company_document_by_custodian','CompanyDocumentRequestController@received_company_document_by_custodian')->name('received_company_document_by_custodian');
    Route::post('/reject_company_document_request','CompanyDocumentRequestController@reject_company_document_request')->name('reject_company_document_request');
    
    //VoucherBookController
    Route::post('/get_voucher_ref_number_failed', 'VoucherBookController@get_voucher_ref_number')->name('get_voucher_ref_number_failed');
    Route::post('/failed_voucher_list', 'VoucherBookController@failed_voucher_list')->name('failed_voucher_list');
    Route::post('/add_failed_voucher', 'VoucherBookController@add_failed_voucher')->name('add_failed_voucher');
    Route::post('/accept_failed_voucher', 'VoucherBookController@accept_failed_voucher')->name('accept_failed_voucher');
    Route::post('/reject_failed_voucher', 'VoucherBookController@reject_failed_voucher')->name('reject_failed_voucher');
    Route::post('/get_users_list_voucher', 'VoucherBookController@get_users_list_voucher')->name('get_users_list_voucher');
    Route::post('/add_voucher_book', 'VoucherBookController@add_voucher_book')->name('add_voucher_book');
    Route::post('/assign_voucher_number_list', 'VoucherBookController@assign_voucher_number_list')->name('assign_voucher_number_list');
    Route::post('/my_voucher_book_list', 'VoucherBookController@my_voucher_book_list')->name('my_voucher_book_list');
    Route::post('/assign_voucher_touser', 'VoucherBookController@assign_voucher_touser')->name('assign_voucher_touser');
    Route::post('/accept_voucher_user', 'VoucherBookController@accept_voucher_user')->name('accept_voucher_user');
    Route::post('/reject_voucher_user', 'VoucherBookController@reject_voucher_user')->name('reject_voucher_user');

    //09/07/2020
    //APINotification
    Route::post('/apiUnraedNotificationByUser', 'APINotificationController@apiUnraedNotificationByUser')->name('apiUnraedNotificationByUser');
    Route::post('/apimarkraedNotificationByUser', 'APINotificationController@apimarkraedNotificationByUser')->name('apimarkraedNotificationByUser');
    Route::post('/apimarkraedNotificationByUserId', 'APINotificationController@apimarkraedNotificationByUserId')->name('apimarkraedNotificationByUserId');
    
    //15/07/2020
    Route::post('/api_complience_reminder_list', 'complienceReminderController@api_complience_reminder_list')->name('api_complience_reminder_list');
    Route::post('/api_complete_compliance_reminder', 'complienceReminderController@api_complete_compliance_reminder')->name('api_complete_compliance_reminder');

     //01/09/2020
    Route::post('/api_company_to_company_cash_transfer', 'APICompanyCashManagementController@api_company_to_company_cash_transfer')->name('api_company_to_company_cash_transfer');
    Route::post('/api_company_to_employee_cash_transfer', 'APICompanyCashManagementController@api_company_to_employee_cash_transfer')->name('api_company_to_employee_cash_transfer');
    Route::post('/api_employee_cash_transfer', 'APICompanyCashManagementController@api_employee_cash_transfer')->name('api_employee_cash_transfer');
    Route::post('/api_employee_current_balance', 'APICompanyCashManagementController@api_employee_current_balance')->name('api_employee_current_balance');
    Route::post('/cash_transaction_report', 'ReportsController@cash_transaction_report')->name('cash_transaction_report');

      //03/09/2020
    Route::post('/attendace_approval_list', 'ApprovalController@attendace_approval_list')->name('attendace_approval_list');
    Route::post('/accept_attendace_approval_request', 'ApprovalController@accept_attendace_approval_request')->name('accept_attendace_approval_request');
    Route::post('/reject_attendace_approval_request', 'ApprovalController@reject_attendace_approval_request')->name('reject_attendace_approval_request');

     //08/09/2020
    Route::post('/get_loginuser_project_list', 'ExpenseController@get_loginuser_project_list')->name('get_loginuser_project_list');

     //10/09/2020
    Route::post('/get_meeting_approvals', 'MeetingController@get_meeting_approvals')->name('get_meeting_approvals');
    Route::post('/get_meeting_approvals_ount', 'MeetingController@get_meeting_approvals_ount')->name('get_meeting_approvals_ount');
    //11/09/2020
    Route::post('/close_meeting', 'MeetingController@close_meeting')->name('close_meeting');

    //16/09/2020
    Route::post('/get_vendor_list', 'ApprovalController@get_vendor_list')->name('get_vendor_list');
    Route::post('/get_vendor_payments', 'ApprovalController@get_vendor_payments')->name('get_vendor_payments');

    //22/10/2020
    Route::post('/api_stationery_items_access_requestes', 'StationeryController@api_stationery_items_access_requestes')->name('api_stationery_items_access_requestes');
    Route::post('/api_stationery_items_accept_request', 'StationeryController@api_stationery_items_accept_request')->name('api_stationery_items_accept_request');
    Route::post('/api_stationery_items_confirm_request', 'StationeryController@api_stationery_items_confirm_request')->name('api_stationery_items_confirm_request');
    Route::post('/api_stationery_items_return', 'StationeryController@api_stationery_items_return')->name('api_stationery_items_return');

    //23/10/2020
    Route::post('/api_add_stationery_item_access_request', 'StationeryController@api_add_stationery_item_access_request')->name('api_add_stationery_item_access_request');
    Route::post('/api_edit_stationery_item_access_request', 'StationeryController@api_edit_stationery_item_access_request')->name('api_edit_stationery_item_access_request');
    Route::post('/api_delete_stationery_item_access_request', 'StationeryController@api_delete_stationery_item_access_request')->name('api_delete_stationery_item_access_request');
    Route::post('/api_stationery_items_list', 'StationeryController@api_stationery_items_list')->name('api_stationery_items_list');


    //SoftcopyRequestController : 24/09/2020
    Route::post('/get_softcopy_request_sent', 'SoftcopyRequestController@get_softcopy_request_sent')->name('get_softcopy_request_sent');
    Route::post('/get_softcopy_request_received', 'SoftcopyRequestController@get_softcopy_request_received')->name('get_softcopy_request_received');
    Route::post('/get_softcopy_category', 'SoftcopyRequestController@get_softcopy_category')->name('get_softcopy_category');
    Route::post('/add_softcopy_request', 'SoftcopyRequestController@add_softcopy_request')->name('add_softcopy_request');
    Route::post('/edit_softcopy_request', 'SoftcopyRequestController@edit_softcopy_request')->name('edit_softcopy_request');
    Route::post('/delete_softcopy_request', 'SoftcopyRequestController@delete_softcopy_request')->name('delete_softcopy_request');
    Route::post('/send_softcopy_request', 'SoftcopyRequestController@send_softcopy_request')->name('send_softcopy_request');
    Route::post('/reject_softcopy_request', 'SoftcopyRequestController@reject_softcopy_request')->name('reject_softcopy_request');
    
    
    // ProjectPendingRequest
    Route::post('/get_project_update_request', 'ApprovalController@get_project_update_request')->name('get_project_update_request');
    Route::post('/approve_reject_project_update_request', 'ApprovalController@approve_reject_project_update_request')->name('approve_reject_project_update_request');
   

});
