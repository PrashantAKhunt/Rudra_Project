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
    //UserController methods    
    Route::group(['middleware' => ['api_auth']], function() {
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

        Route::post('/cheque_register_report', 'ReportsController@cheque_register_report')->name('cheque_register_report');
        Route::post('/letter_head_register_report', 'ReportsController@letter_head_register_report')->name('letter_head_register_report');
        //Route::post('/get_user_lists', 'UsersController@get_user_lists')->name('get_user_lists');
        //Route::post('/reset_virtual_money', 'UsersController@reset_virtual_money')->name('reset_virtual_money');
        //Route::post('/get_reset_page_data','UsersController@get_reset_page_data')->name('get_reset_page_data');              
    });
    Route::post('/get_user_list', 'UsersController@get_user_list')->name('get_user_list');
    Route::post('/get_upcomings', 'EmployeeController@get_upcomings')->name('get_upcomings');
    Route::post('/get_holiday', 'LeaveController@get_holiday')->name('get_holiday');
    Route::post('/get_upcoming_holiday', 'LeaveController@get_upcoming_holiday')->name('get_upcoming_holiday');

    //Route::get('/test', 'UsersController@test')->name('test');
    //Route::post('/delete_user', 'UsersController@delete_user')->name('delete_user');
});
