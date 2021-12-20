<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlinePaymentApproval extends Model
{
    protected $table="online_payment_approval";

    public function paymentFiles() {
        return $this->hasMany('App\OnlinePaymentFile', 'online_payment_id', 'id')->orderBy('id', 'ASC');
    }
}
