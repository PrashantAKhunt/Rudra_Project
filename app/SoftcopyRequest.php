<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoftcopyRequest extends Model
{
    protected $table="softcopy_request";

    public function requester() {
        return $this->hasOne('App\User', 'id', 'request_user_id');
    }

    public function receiver() {
        return $this->hasOne('App\User', 'id', 'receiver_user_id');
    }

    public function company() {
        return $this->hasOne('App\Companies', 'id', 'company_id');
    }

    public function category() {
        return $this->hasOne('App\SoftcopyDocumentCategory', 'id', 'softcopy_document_category_id');
    }
}