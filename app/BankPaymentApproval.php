<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankPaymentApproval extends Model
{
    protected $table="bank_payment_approval";
    
    public function paymentFiles() {
        return $this->hasMany('App\Bank_payment_file', 'bank_payment_id', 'id')->orderBy('id', 'ASC');
    }
}
