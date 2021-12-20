<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChequeRegister extends Model
{
    protected $table="cheque_register";

    public static function get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str = array())
    {
        //die('ok');
        $output = array();
        $data = DB::table($table)
            ->select($getfiled);

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


        if (!empty($where_date)) {
            foreach ($where_date as $date) {
                $data->whereDate($date[0], $date[1], $date[2]);
            }
        }
        if (!empty($request) && $request['search']['value'] != '') {
            $data->where(function ($query) use ($request, $datatable_fields) {
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
        $start =  !empty($request['start']) ? $request['start'] : 0;
        $length =  !empty($request['length']) ? $request['length'] : 0;
        $draw = !empty($request['draw']) ? $request['draw'] : 10;
        $data->skip($start)->take($length);
        //print_r(DB::getQueryLog());exit;
        $output['recordsTotal'] = $count;
        $output['recordsFiltered'] = $count;
        $output['draw'] = $draw;
        $sms_data = $data->get();

        //$response['perPageCount'] = $i;
        //print_r($sms_data); die();
        $output['data'] = $sms_data;
        return json_encode($output);
    }
}
