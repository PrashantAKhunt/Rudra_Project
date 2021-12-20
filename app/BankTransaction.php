<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $table="bank_transaction";

    Protected $fillable = ['company_id'  ,'bank_id' ,'tx_id','tx_date','particular' ,'cheque_num','internal','voucher_type' ,'project','head_id','sub_head' ,'received','paid','balance' ,'narration','remark'  ];
}
