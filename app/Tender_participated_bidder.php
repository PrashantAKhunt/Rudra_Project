<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tender_participated_bidder extends Model
{
    protected $table="tender_participated_bidder";


    public function getBidderItem()
    {
    	return $this->hasMany('App\Tender_boq_bidder','bidder_id','id');
    }
}
