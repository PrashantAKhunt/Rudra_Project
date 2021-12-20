<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Lib;

use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralNotification;

/**
 * Description of NotificationTask
 *
 * @author kishan
 */
class NotificationTask {

    public function get_user_obj($user_ids) {
        return \App\User::whereIn('id', $user_ids)->get();
    }

    public function getUnreadNotificationByUser($user_id) {
        $userObj = $this->get_user_obj([$user_id]);
        if ($userObj->count() > 0) {
            return $userObj[0]->unreadNotifications;
        } else {
            return [];
        }
    }

    public function markReadNotification($notifyObj) {
        $notifyObj->markAsRead();
        return;
    }

    public function allMarkReadNotificationByUser($user_id) {
        $userObj = $this->get_user_obj([$user_id]);
        $userObj->unreadNotifications()->update(['read_at' => now()]);
        return;
    }

    public function deleteNotificationByUser($user_id) {
        $userObj = $this->get_user_obj([$user_id]);
        $userObj->notifications()->delete();
        return;
    }

    //create different notification for different tasks
    public function remoteAttendanceRejectedNotify($user_ids) {

        $message = "Your remote attendance is rejected. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "remoteAttendanceRejected"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Remote attendance rejected"
        ];

        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function remoteAttendanceAcceptedNotify($user_ids) {

        $message = "Your remote attendance is approved. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "remoteAttendanceAccepted"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Remote attendance accepted"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function remoteAttendanceNotify($user_ids, $attendance_user_name) {

        $message = "$attendance_user_name has submitted remote attendance request.";
        $type = "Push,Dashboard";
        $tag = "remoteAttendance"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Remote attendance submitted"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function onDutyAttendanceNotify($user_ids, $attendance_user_name) {

        $message = "$attendance_user_name has submitted on duty remote attendance request. Please check and verify it.";
        $type = "Push,Dashboard";
        $tag = "onDutyAttendance"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "On duty request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function resignNotify($user_ids, $resignee_user_name) {

        $message = "$resignee_user_name had submitted Resignation request. Please take action needed on this matter.";
        $type = "Push,Dashboard";
        $tag = "resign"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Resignation Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function revokeNotify($user_ids, $resignee_user_name) {

        $message = "$resignee_user_name had revoked Resignation request. No action needed on this matter.";
        $type = "Push,Dashboard";
        $tag = "RevokedResign"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Revoked Resignation Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function leaveRelieverNotify($user_ids, $leave_user_name) {

        $message = "$leave_user_name had submitted leave request. And requested to you for leave reliever. ";
        $type = "Push,Dashboard";
        $tag = "leaveReliever"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leave Reliever Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function leaveRelieveRequestRejectedNotify($user_ids, $reject_user_name) {

        $message = "$reject_user_name had rejected your leave relive request. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "leaveRelieveRequestRejected"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leave Reliever Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function leaveRequestNotify($user_ids, $leave_user_name) {

        $message = "$leave_user_name had submitted leave request. you will be notified for accpet or reject once leave reliever accepts the work. ";
        $type = "Push,Dashboard";
        $tag = "leaveRequest"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leave Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function leaveReversalApproveNotify($user_ids,$leave_user_name,$type) {
        $message = "{$leave_user_name} leave reversal request approve by {$type}.";
        $type = "Push,Dashboard";
        $tag = "leaveReversalApprove"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leave Reversal Approve"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
    
    // leave leaveReversalApproveNotify reversal notification End
    public function leaveReversalRejectNotify($user_ids,$leave_user_name,$type) {
        $message = "{$leave_user_name} leave reversal request rejecte by {$type}.";
        $type = "Push,Dashboard";
        $tag = "leaveReversalReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leave Reversal Reject"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
    // leave leaveReversalApproveNotify reversal notification End
    
    public function leaveRequestActionNotify($user_ids, $leave_user_name) {
        
        $message = "$leave_user_name had submitted leave request. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "leaveRequestHr"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leave Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function leaveApprovedNotify($user_ids) {

        $message = "Your leave is approved. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "leaveApproved"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leave Approved"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function leaveRejectedNotify($user_ids) {

        $message = "Your leave is rejected. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "leaveRejected"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leave Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function bankPaymentFirstApprovalNotify($user_ids) {

        $message = "One bank payment request waiting for approval, already approved by accounts.";
        $type = "Push,Dashboard";
        $tag = "bankPaymentFirstApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Bank Payment Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function bankPaymentSecondApprovalNotify($user_ids) {

        $message = "One bank payment request waiting for approval, Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "bankPaymentSecondApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Bank Payment Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function bankPaymentThirdApprovalNotify($user_ids) {

        $message = "Your bank payment request is approved. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "bankPaymentThirdApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Bank Payment Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function bankPaymentRejectNotify($user_ids) {

        $message = "Your bank payment request is rejected. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "bankPaymentReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Bank Payment Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function cashRequestFirstApprovalNotify($user_ids) {

        $message = "One cash approval request waiting for approval.";
        $type = "Push,Dashboard";
        $tag = "cashRequestFirstApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Cash Approval Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function cashRequestSecondApprovalNotify($user_ids) {

        $message = "One cash approval request waiting for approval, Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "cashRequestSecondApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Cash Approval Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function cashRequestThirdApprovalNotify($user_ids) {

        $message = "Your cash approval request is approved. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "cashRequestThirdApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Cash Approval Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function cashRequestRejectNotify($user_ids) {

        $message = "Your cash approval request is rejected. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "cashRequestReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Cash Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function budgetSheetFirstApprovalNotify($user_ids) {

        $message = "One budget sheet request waiting for approval, Please login to website for approval.";
        $type = "Push,Dashboard";
        $tag = "budgetSheetFirstApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Budget Sheet Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function budgetSheetSecondApprovalNotify($user_ids) {

        $message = "Your budget sheet is approved. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "budgetSheetSecondApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Budget Sheet Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function budgetSheetRejectNotify($user_ids) {

        $message = "Your budget sheet is rejected. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "budgetSheetReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Budget Sheet Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function announcementNotify($user_ids,$announcement_id,$data) {

        // $message = "One announcement for you.";
        $message = $data['description'];
        $type = "Push,Dashboard";
        $tag = "announcement"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag.'|'.$announcement_id,
            'title' => "Announcement",
            'notification_meta' => $announcement_id,
        ];
        
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //Pre Sign First Approve Notification
    public function preSignRequestFirstApprovalNotify($user_ids) {

        $message = "One pre signed letter request waiting for approval.";
        $type = "Push,Dashboard";
        $tag = "preSignRequestFirstApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Pre Sign Approval Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //Pre Sign Second Approve Notification
    public function preSignRequestSecondApprovalNotify($user_ids) {

        $message = "Your pre signed letter request is approved. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "preSignRequestSecondApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Pre Sign Approval Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //Pre Sign Reject Notification
    public function preSignRequestRejectNotify($user_ids) {

        $message = "Your pre signed letter request is rejected. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "preSignRequestReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Pre Sign Letter-head Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //Pro Sign Approve Notification
    public function proSignRequestFirstApprovalNotify($user_ids) {

        $message = "One letter-head request Approved. Please login into website for details.";
        $type = "Push,Dashboard";
        $tag = "proSignRequestFirstApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Letter-head Request Approved"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //Pro Sign Reject Notification
    public function proSignRequestRejectNotify($user_ids) {

        $message = "Your letter-head request is rejected. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "proSignRequestReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Letter-head Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //Pre Sign Second  user deliver on hand letter
    public function preSignRequestDeliveryNotify($user_ids) {

        $message = "One pre signed letter request is approved so please deliver to respective user. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "preSignRequestDeliveryApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Pre Sign Letter Deliver Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function proSignRequestDeliveryNotify($user_ids) {

        $message = "One letter request is approved so please deliver to respective user. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "proSignRequestDeliveryApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Letter Deliver Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function preSignLetterheadDeliveryNotify($user_ids, $letter_head_number, $received_user) {

        $message = "Pre-signed letter-head number {$letter_head_number} is delivered to {$received_user}. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "preSignLetterheadDelivery"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Pre-Signed Letter-head Delivered"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function LetterheadDeliveryNotify($user_ids, $letter_head_number, $received_user) {

        $message = "Letter-head number {$letter_head_number} is delivered to {$received_user}. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "LetterheadDelivery"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Letter-head Delivered"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function driverExepenseRejectNotify($user_ids) {

        $message = "Your expense request is rejected. Please login into account for more details.";
        $type = "Push,Dashboard";
        $tag = "driverExepenseReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expense Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function employeeExepenseRejectNotify($user_ids) {

        $message = "Your expense request is rejected. Please login into account for more details.";
        $type = "Push,Dashboard";
        $tag = "employeeExepenseReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expense Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function employeeExepenseApprovedNotify($user_ids) {

        $message = "Your expense request is approved. Please login into account for more details.";
        $type = "Push,Dashboard";
        $tag = "employeeExepenseApproved"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expense Request Approved"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function driverExepenseFirstApprovalNotify($user_ids) {

        $message = "One driver expense request is pending. Please check in your account for more details.";
        $type = "Push,Dashboard";
        $tag = "driverExepenseFirstApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Driver Expense Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function employeeExepenseFirstApprovalNotify($user_ids) {

        $message = "One expense request is pending. Please check in your account for more details.";
        $type = "Push,Dashboard";
        $tag = "employeeExepenseFirstApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expense Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function driverExepenseSecondApprovalNotify($user_ids) {

        $message = "One driver expense request is pending. Please check in your account for more details.";
        $type = "Push,Dashboard";
        $tag = "driverExepenseSecondApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Driver Expense Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function employeeExepenseSecondApprovalNotify($user_ids) {

        $message = "One expense request is pending. Please check in your account for more details.";
        $type = "Push,Dashboard";
        $tag = "employeeExepenseSecondApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expense Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function driverExepenseThirdApprovalNotify($user_ids) {

        $message = "Your driver expense request is approved. Please check in your account for more details.";
        $type = "Push,Dashboard";
        $tag = "driverExepenseThirdApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expense Request Approved"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function longLeaveStartNotify($user_ids, $start_date) {

        $message = "Your leaves are going to start from $start_date. This is reminder about your leaves so you can plan accordingly.";
        $type = "Push,Dashboard";
        $tag = "longLeaveStart"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leaves Start Alert."
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function longLeaveEndNotify($user_ids, $end_date) {

        $message = "Your leaves are going to end on $end_date. This is reminder about your leaves so you can plan accordingly.";
        $type = "Push,Dashboard";
        $tag = "longLeaveEnd"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Leaves End Alert."
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function birthdayNotify($user_ids, $birthday_user) {

        $message = "Today is {$birthday_user} 's birthday. Please wish him on this occasion.";
        $type = "Push,Dashboard";
        $tag = "birthday"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "{$birthday_user} 's birthday"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function birthdayWishNotify($user_ids, $birthday_user) {

        $message = "Happy Birthday $birthday_user. Wish you many many happy returns of the day";
        $type = "Push,Dashboard";
        $tag = "birthdayWish"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Happy Birthday"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function marriageAniversaryNotify($user_ids, $marriage_user) {

        $message = "Today is {$marriage_user} 's marriage anniversary. Please wish him on this occasion.";
        $type = "Push,Dashboard";
        $tag = "marriageAniversary"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "{$marriage_user} 's marriage anniversary"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function marriageAniversaryWishNotify($user_ids, $marriage_user) {

        $message = "Happy Marriage Anniversary $marriage_user. May this special day bring you endless joy and tons of precious memories!
";
        $type = "Push,Dashboard";
        $tag = "marriageAniversaryWish"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Happy Marriage Anniversary"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function joiningAniversaryNotify($user_ids, $joining_user) {

        $message = "Today is {$joining_user} 's work anniversary. Please wish him on this occasion.";
        $type = "Push,Dashboard";
        $tag = "joiningAniversary"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "{$joining_user} 's work anniversary"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function joiningAniversaryWishNotify($user_ids, $joining_user) {

        $message = "Happy Work Anniversary $joining_user. Each day you better yourself, and you polish and refine people around you. You are a gem that will always shine. Your creativity and vision are impeccable. Have a happy work anniversary.";
        $type = "Push,Dashboard";
        $tag = "joiningAniversaryWish"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Happy Work Anniversary"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function addLoanNotify($user_ids, $apply_user_name) {

        $message = $apply_user_name . " apply for loan request";
        $type = "Push,Dashboard";
        $tag = "addLoan"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Apply Loan"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function editLoanNotify($user_ids, $apply_user_name) {

        $message = $apply_user_name . " edit loan request";
        $type = "Push,Dashboard";
        $tag = "editLoan"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Edit Loan"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function deleteLoanNotify($user_ids, $apply_user_name) {

        $message = $apply_user_name . " delete for loan request";
        $type = "Push,Dashboard";
        $tag = "deleteLoan"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Delete Loan"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function approveLoanNotify($user_ids) {

        $message = "Greetings ! Your loan Approved !";
        $type = "Push,Dashboard";
        $tag = "approveLoan"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Approve Loan"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function rejectLoanNotify($user_ids) {

        $message = "Sorry, Your requested loan is rejected !";
        $type = "Push,Dashboard";
        $tag = "rejectLoan"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Reject Loan"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function budgetSheetRequestNotify($user_ids, $request_user_name, $meeting_number) {

        $message = "{$request_user_name} had submitted budget sheet request for approval with meeting number {$meeting_number}. Please login to website for approve/reject";
        $type = "Push,Dashboard";
        $tag = "budgetSheetRequest"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Budget Sheet Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function approveBudgetSheetNotify($user_ids, $meeting_number) {

        $message = "Your budget sheet request is approved for meeting number {$meeting_number}. Please login in to website for more details.";
        $type = "Push,Dashboard";
        $tag = "approveBudgetSheet"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Budget Sheet Request Approved"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function rejectBudgetSheetNotify($user_ids, $meeting_number) {

        $message = "Your budget sheet request is rejected for meeting number {$meeting_number}. Please login in to website for more details.";
        $type = "Push,Dashboard";
        $tag = "rejectBudgetSheet"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Budget Sheet Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function remoteAttendanceRequestNotify($user_ids, $request_user_name) {

        $message = "{$request_user_name} had submitted remote visit attendance request for approval. Please login to your account for more detail";
        $type = "Push,Dashboard";
        $tag = "remoteVisitAttendanceRequest"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Remote Visit Attendance Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function approveRemoteAttendanceNotify($user_ids) {

        $message = "Your remote visit attendance request is approved. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "approveRemoteVisitAttendance"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Remote Visit Attendance Request Approved"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function rejectRemoteAttendanceNotify($user_ids) {

        $message = "Your remote visit attendance request is rejected. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "rejectRemoteVisitAttendance"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Remote Visit Attendance Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function cancelRemoteAttendanceNotify($user_ids, $request_user_name) {

        $message = "{$request_user_name} had cancelled remote visit attendance request. Please login to your account for more detail";
        $type = "Push,Dashboard";
        $tag = "cancelRemoteVisitAttendance"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Remote Visit Attendance Request cancelled"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function holdAmountReleaseNotify($user_ids, $budgetsheet_number) {

        $message = "Hold amount is released for budget meeting number {$budgetsheet_number}";
        $type = "Push,Dashboard";
        $tag = "holdAmountRelease"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Hold Amount Release"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function tripOpenAlertNotify($user_ids, $driver_name, $trip_user_name) {

        $message = "{$driver_name} had opned a new trip with {$trip_user_name}. Once trip will be close, you will notify about it.";
        $type = "Push,Dashboard";
        $tag = "tripOpenAlert"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Trip Open Alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function tripCloseAlertNotify($user_ids, $driver_name, $trip_user_name) {

        $message = "{$driver_name} had a close trip with {$trip_user_name} please Approve it.";
        $type = "Push,Dashboard";
        $tag = "tripCloseAlert"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Trip Open Alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function tripApproveAlertNotify($user_ids, $driver_name, $trip_user_name) {

        $message = "Hello {$driver_name} a trip with {$trip_user_name} is Approved !.";
        $type = "Push,Dashboard";
        $tag = "tripApproveAlert"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Trip Approve Alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function tripRejectAlertNotify($user_ids, $driver_name, $trip_user_name, $reason = "") {

        $message = "Hello {$driver_name} a trip with {$trip_user_name} is Rejected due to {$reason}!.";
        $type = "Push,Dashboard";
        $tag = "tripRejectAlert"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Trip Reject Alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function inwardAddAlertNotify($user_ids, $inward_number) {

        $message = "New inward is added with inward number {$inward_number}. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "inwardAddAlert"; //based on function names
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "New Inward Alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function outwardAddAlertNotify($user_ids, $outward_number) {

        $message = "New outward is added with inward number {$outward_number}. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "outwardAddAlert"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "New Outward Alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function registryMessageNotify($user_ids, $message_body, $registry_name) {

        $message = $message_body;
        $type = "Push,Dashboard";
        $tag = "registryMessage"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "New Message From Registry {$registry_name}"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function registryResponseReminderNotify($user_ids, $registry_number) {

        $message = "Expected answer date for registry number {$registry_number} is due soon. Please check and take actions.";
        $type = "Push,Dashboard";
        $tag = "registryResponseReminder"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expected Answer Date Reminder - {$registry_number}"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function employeeExepenseThirdApprovalNotify($user_ids) {

        $message = "One expense request is pending. Please check in your account for more details.";
        $type = "Push,Dashboard";
        $tag = "employeeExepenseThirdApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expense Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function employeeExepenseForthApprovalNotify($user_ids) {

        $message = "One expense request is pending. Please check in your account for more details.";
        $type = "Push,Dashboard";
        $tag = "employeeExepenseForthApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expense Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function travelExpenceNotify($user_ids, $title, $request_no) {

        $message = "Requested for Travel Expense with request no:$request_no, Please check.";
        $message = $message;
        $type = "Push,Dashboard";
        $tag = "travelExpense"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $title
        ];

        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function travelhotelExpenceNotify($user_ids, $title, $message) {
        $message = $message;
        $type = "Push,Dashboard";
        $tag = "travelhotelExpence"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $title
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function generalNotify($user_ids) {

        $message = "Please update new version of Raudratech app from playstore if not updated yet. Latest version is 1.6. Please contact HR department if any issue in version update.";
        $type = "Push,Dashboard";
        $tag = "general"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "APP version update needed"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function travelOptions($user_id, $user_name) {

        $message = "$user_name had successfully added travel details, Please check details via login in your account.";
        $type = "Push,Dashboard";
        $tag = "TravelOptionsDetails"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Travel Options"
        ];

        $user_ids = array_unique($user_id);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function ApprovetravelOption($user_id, $user_name) {

        $message = "This Travel Option is approved by $user_name, Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "ApproveTravelOption"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Approved Travel Option"
        ];

        $user_ids = array_unique($user_id);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function onlinePaymentFirstApprovalNotify($user_ids) {

        $message = "One online payment request waiting for approval, already approved by accounts.";
        $type = "Push,Dashboard";
        $tag = "onlinePaymentFirstApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Online Payment Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function onlinePaymentSecondApprovalNotify($user_ids) {

        $message = "One online payment request waiting for approval, Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "onlinePaymentSecondApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Online Payment Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function onlinePaymentThirdApprovalNotify($user_ids) {

        $message = "Your online payment request is approved. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "onlinePaymentThirdApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Online Payment Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function onlinePaymentRejectNotify($user_ids) {

        $message = "Your online payment request is rejected. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "onlinePaymentReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Online Payment Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function VehicleMaintenancePaymentFirstApprovalNotify($user_ids) {

        $message = "One VehicleMaintenance payment request waiting for approval, already approved by admin.";
        $type = "Push,Dashboard";
        $tag = "VehicleMaintenancePaymentFirstApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Vehicle Maintenance Payment Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function VehicleMaintenancePaymentSecondApprovalNotify($user_ids) {

        $message = "Your vehicle maintenance payment request is approved. Please login to website for more details..";
        $type = "Push,Dashboard";
        $tag = "VehicleMaintenancePaymentSecondApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Vehicle Maintenance Payment Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function VehicleMaintenancePaymentRejectNotify($user_ids) {

        $message = "Your VehicleMaintenance payment request is rejected. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "VehicleMaintenancePaymentReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Vehicle Maintenance Payment Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function PayrollApprovalNotify($user_ids) {

        $message = "Payroll requests are pending for approval. please login to your account for more details ";
        $type = "Push,Dashboard";
        $tag = "PayrollApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Payroll Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function LoanApprovalNotify($user_ids) {

        $message = "One loan request is waiting for approval. please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "LoanApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Loan Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function RejectTravelOptions($user_id, $user_name, $request_no) {

        $message = "Your all travel options are rejected by $user_name, Please login to your account,check travel request status with Request number:$request_no.";
        $type = "Push,Dashboard";
        $tag = "RejectTravelOptions"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Rejected Travel Options"
        ];


        $user_ids = array_unique($user_id);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	public function RejectTravelRequest($user_id,$user_name, $request_no) {
        
        $message = "Your Travel Expense request is rejected by $user_name, Please login to your account,check travel request status with Request number:$request_no.";
        $type = "Push,Dashboard";
        $tag = "RejectTravelExpense"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Rejected Travel Expense"
        ];
       
       
        $user_ids = array_unique($user_id);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function markAsImpoNotify($user_ids, $inward_outward_no) {

        $message = "{$inward_outward_no} registry is waiting for your approval. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "MarkAsImportant"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Mark As Important Registry"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function exepensApprovedNotifyFlow($user_ids, $expenseCode) {

        $message = "{$expenseCode} expense is approved. Please login into account for more details.";
        $type = "Push,Dashboard";
        $tag = "employeeExepenseApproved"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Expense Request Approved"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function workOffHrApprovalNotify($user_ids) {

        $message = "One Work-Off Attendance request waiting for approval, Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "workOffAttendanceApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Work-Off Attendance Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function workOffSuperUserApprovalNotify($user_ids) {

        $message = "One Work-Off Attendance request waiting for approval, Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "workOffAttendanceApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Work-Off Attendance Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function workOffSecondApprovalNotify($user_ids) {

        $message = "Hello,Your Work-Off attendance request is approved. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "workOffAttendanceRequestApproved"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Work-Off Attendance Request Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function workOffRejectNotify($user_ids) {

        $message = "Hello,Your Work-Off attendance request is rejected. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "workOffAttendanceRequestReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Work-Off Attendance Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function hrAssetRequestNotify($user_ids) {

        $message = "New Asset access request is waiting for ypur approval. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "assetAccessRequest"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Asset Access Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function hrAssetApprovalNotify($user_ids) {

        $message = "Asset access request is approved by HR ,Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "assetAccessApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Asset Access Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function assignerConfirmationAssetNotify($user_ids) {

        $message = "Greetings! Your asset is successfully assigned. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "assignerConfirmation"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Assigner Confirmation"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function hrAssetRejectNotfy($user_ids) {

        $message = "Oops,Your asset assign request is rejected by HR. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "assetAssignReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Asset Assign Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function userAssetRejectNotfy($user_ids) {

        $message = "Oops,Your asset assign request is rejected by user. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "assetAssignReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Asset Assign Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function assetNotfy($user_ids, $return_date ,$asset_name) {

        $message = "Kindaly note! {$asset_name} is on Maintenance, return date is {$return_date}.";
        $type = "Push,Dashboard";
        $tag = "assetOnMaintenance"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message, 
            'tag' => $tag,
            'title' => "Asset on Maintenance"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function meetingRequestNotify($user_ids) {

        $message = "You invite for meeting please accept meeting request!.";
        $type = "Push,Dashboard";
        $tag  = "meetingRequest"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Meeting Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
    
    //HardCopy Assignee Notify
    public function hardCopyAssigneeNotify($user_ids) {

    $message = "One Document HardCopy is waiting for your approval, Please login to your account for more details.!";
    $type = "Push,Dashboard";
    $tag  = "documentHardCopyRequest"; //based on function name
    $notification_data = [
        'type' => $type,
        'message' => $message,
        'tag' => $tag,
        'title' => "Document Hard Copy Request"
    ];
    $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
        $user_obj = $this->get_user_obj([$user]);
           Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function tenderAssignNotify($user_ids, $title, $message){
        $message = $message;
        $type = "Push,Dashboard";
        $tag = "tenderAssign"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $title
        ];

        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }   
    }

    public function tenderUpdateNotify($user_ids, $title, $message){
        $message = $message;
        $type = "Push,Dashboard";
        $tag = "tenderUpdate"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $title
        ];

        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }   
    }

    public function tenderSelectNotify($user_ids, $title, $message){
        $message = $message;
        $type = "Push,Dashboard";
        $tag = "tenderSelect"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $title
        ];

        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }   
    }

    public function openingTenderNotify($user_ids, $title, $message){
        $message = $message;
        $type = "Push,Dashboard";
        $tag = "openingTender"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $title
        ];

        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }   
    }
	
	 //18/05/2020
    //Accept Registry work by support employee Notify
    public function supportEmpAcceptNotify($user_ids, $user_name) {

        $message = "{$user_name} is accepted your distrubuted work request, Please login to your account for more details.!";
        $type = "Push,Dashboard";
        $tag  = "suppotingEmployeeAcceptWork"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Supporting Employee Accept work request"
        ];
        $user_ids = array_unique($user_ids);
            foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
            }
    }
	
	//Reject Registry work by support employee Notify
     public function supportEmpRejectNotify($user_ids, $user_name) {

        $message = "{$user_name} is Rejected your distrubuted work request, Please login to your account for more details.!";
        $type = "Push,Dashboard";
        $tag  = "suppotingEmployeeRejecttWork"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Supporting Employee Reject work request"
        ];
        $user_ids = array_unique($user_ids);
            foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
            }
    }
	
	//Reject Registry work by support employee Notify
    public function rejectReturnTaskPrimeUser($user_ids , $registry_no) {

        $message = "Registry No:{$registry_no} Distrubuted task is rejected by Main/Prime employee, Please login to your account for more details.!";
        $type = "Push,Dashboard";
        $tag  = "rejectReturnTaskPrimeUser"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Rejected Regstry Distrubuted Task"
        ];
        $user_ids = array_unique($user_ids);
            foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
            }
    }
	
	//25/05/2020
    public function empWorkPercentageUpdatedNotify($user_ids ,$registry_no, $user_name) {

        $message = "This Registry {$registry_no} for your work time percentage is updated by {$user_name} , Please login to your account for more details.!";
        $type = "Push,Dashboard";
        $tag  = "empWorkPercentageUpdatedNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Employee Work Percentage Updated"
        ];
        $user_ids = array_unique($user_ids);
            foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
            }
    }

    //09/06/2020
    
    public function signedChequeApprovalNotify($cheque_book_no , $user_ids) {

        $message = "Cheque Book No:{$cheque_book_no} is added for signed Cheque Approval.";
        $type = "Push,Dashboard";
        $tag = "signedChequeApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Signed Cheque Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function signedRtgsApprovalNotify($rtgs_ref_no , $user_ids) {

        $message = "Rtgs Reference No:{$rtgs_ref_no} is added for signed Rtgs Approval";
        $type = "Push,Dashboard";
        $tag = "signedRtgsApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Signed Rtgs Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //----------------------------- Cheque / RTGS 
    # 11/06/2020
    public function emptyChequeBookNotify($cheque_book_no , $user_ids) {

        $message = "Cheque book ref number {$cheque_book_no} has now near to empty and has left less then 10 cheque in that. So please continue process to add new cheque book.";
        $type = "Push,Dashboard";
        $tag = "emptyChequeBookNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Cheque book empty alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
    public function emptyRtgsBookNotify($rtgs_book_no , $user_ids) {

        $message = "RTGS book ref number {$rtgs_book_no} has now near to empty and has left less then 10 RTGS in that. So please continue process to add new RTGS book.";
        $type = "Push,Dashboard";
        $tag = "emptyRtgsBookNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "RTGS book empty alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
    public function emptySignedChequeBookNotify($cheque_book_no , $user_ids) {

        $message = "Cheque book ref number {$cheque_book_no} has now less then 5 signed cheques left. So please make new sign cheque request as per requirement.";
        $type = "Push,Dashboard";
        $tag = "emptySignedChequeBookNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Signed Cheque book empty alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
    public function emptySignedRtgsBookNotify($rtgs_book_no , $user_ids) {

        $message = "RTGS book ref number {$rtgs_book_no} has now less then 5 signed RTGS left. So please make new sign RTGS request as per requirement";
        $type = "Push,Dashboard";
        $tag = "emptySignedRtgsBookNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Signed RTGS book empty alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function signedLetterHeadApprovalNotify($letter_head_ref_no , $user_ids) {

        $message = "Letter Head Reference No:{$letter_head_ref_no} is added for signed Letter Head Approval";
        $type = "Push,Dashboard";
        $tag = "signedLetterHeadApproval"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Signed Letter Head Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    // START - Company Document Management Notification

    public function documentRequestNewNotfy($user_ids, $request_by, $document_title) {

        $message = "New ".$document_title." document request is added by ".$request_by.". Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "documentRequestNew"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "New Document Request Added"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function documentRequestApprovedNotfy($user_ids, $approved_by) {

        $message = "Your document request is approved by ".$approved_by.". Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "documentRequestApproved"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Document Request Approved"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function documentRequestRejectNotfy($user_ids) {

        $message = "Oops,Your document request is rejected. Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "documentRequestReject"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Document Request Rejected"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function documentRequestReturnNotfy($user_ids, $return_by, $document_title) {

        $message = $document_title." document is return by ".$return_by.". Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "documentRequestNew"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "New Document Request Added"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    // END - Company Document Management Notification

    public function voucherAssignNotify($user_ids, $title, $message,$tags){
        $message = $message;
        $type = "Push,Dashboard";
        $tag = $tags; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $title
        ];

        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }   
    }

    //-------- 29/06/2020
    public function entryApprovalNotify($module) {

        $user_ids = \App\User::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();
        $message = "New {$module} entry request waiting for approval, Please check and approve.";
        $type = "Push,Dashboard";
        $tag = "entryApprovalNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "New Entry Approval Alert"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //30/06/2020
    public function voucherFailedNotify($user_ids, $title, $message, $tags)
    {
        $message = $message;
        $type = "Push,Dashboard";
        $tag = $tags; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $title
        ];

        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //08/07/2020
    public function complianceReminderNotify($compliance,$user_mails) {

        $message = "Hello, Please take a action on this {$compliance} before due-date.";
        $type = "Push,Dashboard";
        $tag = "complianceReminderNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Compliance Reminder"
        ];
        $user_ids = \App\User::whereIn('email', $user_mails)->pluck('id')->toArray();
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //14/07/2020
    public function remindComplianceNotify($compliance , $user_mails, $date) {

        $message = "Hello, Please take a action on this {$compliance} before Due-date {$date}.";
        $type = "Push,Dashboard";
        $tag = "complianceReminderNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Compliance Reminder"
        ];
        $user_ids = \App\User::whereIn('email', $user_mails)->pluck('id')->toArray();
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
	
	public function chatOfflineNotify($message , $user_ids, $from_user_name) {
    //\App\Test::insert(['test_type'=>'ok']);
       
        $type = "Push";
        $tag = "chatOffline"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $from_user_name
        ];
        $user_ids = array_unique($user_ids);
		
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    //22/10/2020
    public function stationeryItemRequestNotify($item,$username,$user_ids) {

        $message = "{$username} has requsted to access this Stationery Item {$item}, Please check and approve.";
        $type = "Push,Dashboard";
        $tag = "stationeryItemRequestNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Stationery Item Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
    public function stationeryItemAcceptNotify($item,$username,$user_ids) {

        $message = "Your access request for this stationery item {$item} is approved by {$username},please check and confirm";
        $type = "Push,Dashboard";
        $tag = "stationeryItemAcceptNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Stationery Item Approval"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
    public function stationeryItemReturnNotify($item,$username,$user_ids) {

        $message = "Your Stationery item {$item} has been returned by {$username}, please check and confirm.";
        $type = "Push,Dashboard";
        $tag = "stationeryItemReturnNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Stationery Item Return Confirmation"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function periodicMaintenanceNotify($user_ids)
    {

        $message = "Your periodic maintenance kilometer over. Please login to your account for more details.";
        $type = "Push,Dashboard";
        $tag = "periodicMaintenance"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Vehicle Maintenance"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }

    public function softcopyRequestNotfy($user_ids, $request_by, $document_name, $request_type) {

        $message = $request_type." : ". $document_name." softcopy request by ".$request_by.". Please login to website for more details.";
        $type = "Push,Dashboard";
        $tag = "softcopyRequestNew"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => $request_type." Softcopy Request"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
    public function companyRuleNotify($user_ids) {
        $message = "Company rules added.";
        $type = "Push,Dashboard";
        $tag = "companyRuleNotify"; //based on function name
        $notification_data = [
            'type' => $type,
            'message' => $message,
            'tag' => $tag,
            'title' => "Company Rule"
        ];
        $user_ids = array_unique($user_ids);
        foreach ($user_ids as $user) {
            $user_obj = $this->get_user_obj([$user]);
            Notification::send($user_obj, new GeneralNotification($notification_data, [$user]));
        }
    }
}
