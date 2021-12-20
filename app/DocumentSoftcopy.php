<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\DocumentSoftcopyFiles;

class DocumentSoftcopy extends Model
{
    protected $table="document_softcopy";
    
    public function files() {
        return $this->hasMany('App\DocumentSoftcopyFiles', 'document_softcopy_id', 'id');
    }

    public function ward() {
        return $this->hasMany('App\Inward_outwards', 'id', 'inward_outward_id');
    }

    public static function deleteRelatedFiles($id){
        $softcopyFiles = DocumentSoftcopyFiles::where('document_softcopy_id',$id)->get();
        foreach ($softcopyFiles as $key => $value) {
            unlink('storage/' . str_replace('public/', '', $value->file));
        }        
        DocumentSoftcopyFiles::where('document_softcopy_id',$id)->delete();
    }


    public static function get_list_datatable_ajax($table,$datatable_fields, $conditions_array, $getfiled, $request, $join_str = array(),$where_date=[]) {
        //die('ok');
		// DB::enableQueryLog();
		$output = array();
        $data = DB::table($table)
                ->select($getfiled)
                ->leftJoin('document_softcopy_access', function($query) 
                {
                   $query->on('document_softcopy.id','=','document_softcopy_access.document_softcopy_id')
                   ->whereRaw('document_softcopy_access.id IN (select MAX(a2.id) from document_softcopy_access as a2 join document_softcopy as u2 on u2.id = a2.document_softcopy_id group by u2.id)');
                });
               

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
        

        $output['data'] = $sms_data;        
        return json_encode($output);
    }

}
