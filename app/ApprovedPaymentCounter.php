<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovedPaymentCounter extends Model
{
    //
    protected $table="approved_payment_counter";
    protected $fillable = ['bank_payment_id', 'counter'];
}
