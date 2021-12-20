<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tender_boq_bidder extends Model
{
    protected $table="tender_boq_bidder";


    public function getBidderName()
    {
    	return $this->belongsTo('App\Tender_participated_bidder','bidder_id','id');
    }
}
