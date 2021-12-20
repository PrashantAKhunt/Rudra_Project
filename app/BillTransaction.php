<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillTransaction extends Model
{
    protected $table="bill_transactions";

    Protected $fillable = ['bill_date','vendor_id','request_by','verify_by','account_transfer_detail','company_id'  ,'mode_of_payment' ,'head_id','account_number','deduction_details' ,'pending_amount','amount_released','notes' ,'budget_sheet_no','created_at' ];
}
