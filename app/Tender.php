<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tender extends Model
{
    protected $table="tender";


    public static function get_list_datatable_ajax($table,$datatable_fields, $conditions_array, $getfiled, $request, $join_str = array(),$where_date=[],$where_raw="") {
        //die('ok');
		// DB::enableQueryLog();
		$output = array();
        $data = DB::table($table)
                ->select($getfiled)
                // ->whereRaw("find_in_set('users.id',tender.assign_tender)")''
                ->leftjoin("users",DB::raw("FIND_IN_SET(users.id,tender.assign_tender)"),">",DB::raw("0"))
                ->groupBy('tender.id')
                ;
        if($where_raw!=""){
            $data->whereRaw($where_raw);
        }
        if (!empty($join_str)) {
            //$data->where(function($query) use ($join_str) {
            foreach ($join_str as $join) {
                if (!isset($join['join_type'])) {
                    $data->join($join['table'], $join['join_table_id'], '=', $join['from_table_id']);
                } else {
                    $data->join($join['table'], $join['join_table_id'], '=', $join['from_table_id'], $join['join_type']);
                }
            }
            //});
        }
        if (!empty($conditions_array)) {
            $data->where($conditions_array);
        }
        if(!empty($where_date)){
            foreach($where_date as $date){
                $data->whereDate($date[0],$date[1],$date[2]);
            }
            
        }
        if ( !empty($request) && $request['search']['value'] != '') {
            $data->where(function($query) use ($request, $datatable_fields) {
                for ($i = 0; $i < count($datatable_fields); $i++) {
                    if ($request['columns'][$i]['searchable'] == 'true') {
                        $query->orWhere($datatable_fields[$i], 'like', '%' . $request['search']['value'] . '%');
                    }
                }
            });
        }
        if (isset($request['order']) && count($request['order'])) {
            for ($i = 0; $i < count($request['order']); $i++) {
                if ($request['columns'][$request['order'][$i]['column']]['orderable'] == true) {
                    $data->orderBy($datatable_fields[$request['order'][$i]['column']], $request['order'][$i]['dir']);
                }
            }
        }
        $count = $data->count();
        $start =  !empty($request['start'])?$request['start']:0;
        $length =  !empty($request['length'])?$request['length']:0;
        $draw = !empty($request['draw'])?$request['draw']:10;
        $data->skip($start)->take($length);
        //print_r(DB::getQueryLog());exit;
        $output['recordsTotal'] = $count;
        $output['recordsFiltered'] = $count;
        $output['draw'] = $draw;
        $sms_data = $data->get();
        $output['recordsTotal'] = $sms_data->count();
        $output['recordsFiltered'] = $sms_data->count();
//         $query = DB::getQueryLog();

// print_r($query);exit;


        //$response['perPageCount'] = $i;
		// print_r($sms_data); die();
        $output['data'] = $sms_data;        
        return json_encode($output);
    }
	
	public function tender_technical_eligibility() {
        return $this->hasMany('App\Tender_technical_eligibility','tender_id','id');
    }
	
	public function tender_financial_eligibility(){
        return $this->hasMany('App\Tender_financial_eligibility','tender_id','id');
    }
	
	public function tender_other_communication() {
        return $this->hasMany('App\Tender_other_communication','tender_id','id');
    }
	 public function tender_condition_contract() {
        return $this->hasMany('App\Tender_condition_contract','tender_id','id');
    }
	
	public function tender_pre_bid_document() {
        return $this->hasMany('App\Tender_pre_bid_document','tender_id','id');
    }
	
	public function tender_submission_technical_part() {
        return $this->hasMany('App\Tender_submission_technical_part','tender_id','id');
    }
	
	public function tender_submission_financial_part() {
        return $this->hasMany('App\Tender_submission_financial_part','tender_id','id');
    }

    public function tender_submission_commercial() {
        return $this->hasMany('App\Tender_submission_commercial','tender_id','id');
    }

    public function tender_participated_bidder() {
        return $this->hasMany('App\Tender_participated_bidder','tender_id','id');
    }
	
    public function tender_opening_status_technical() {
        return $this->hasMany('App\Tender_opening_status_technical','tender_id','id');
    }

    public function tender_opening_status_financial() {
        return $this->hasMany('App\Tender_opening_status_financial','tender_id','id');
    }
    public function tender_client() {
        return $this->hasMany('App\Tender_client_detail','tender_id','id');
    }
    public function tender_authorites() {
        return $this->hasMany('App\Tender_authority_contact_detail','tender_id','id');
    }
    public function tender_corrigendum() {
        return $this->hasMany('App\TenderCorrigendum','tender_id','id');
    }

}
