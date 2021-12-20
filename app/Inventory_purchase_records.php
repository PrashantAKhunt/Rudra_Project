<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lib\Upload_file;


class Inventory_purchase_records extends Model
{
    protected $table="inventory_purchase_records";
    protected $appends = ['proof_image'];
    public $upload_file;

    public function __construct() {
        $this->upload_file = new Upload_file();
    }

    public function getProofImageAttribute($value)
    {
        if($this->proof){
            //return  asset('storage/'.str_replace('public/','', $this->proof));
            return $this->upload_file->get_s3_file_path('inventory_proof/',$this->proof);
        }else{
            return "";
        }
        
    }
}