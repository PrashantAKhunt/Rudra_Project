<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Roles extends Model {

    public $timestamps = false;
    protected $table="role";
    public static function getRolesRecord($table_name, $datatable_fields, $conditions_array, $getfiled, $request, $join_str = array()) {
        //DB::enableQueryLog();
        $output = array();
        $data = DB::table($table_name)
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
            $data->Where(function ($query) {
                $query->where('id', '!=', 1);
            });
        }
        if ($request['search']['value'] != '') {
            $data->where(function($query) use ($request, $datatable_fields) {
                for ($i = 0; $i < count($datatable_fields); $i++) {
                    if ($request['columns'][$i]['searchable'] == true) {
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

        $data->skip($request['start'])->take($request['length']);
        //print_r(DB::getQueryLog());exit;
        $output['recordsTotal'] = $count;
        $output['recordsFiltered'] = $count;
        $output['draw'] = $request['draw'];
        $output['data'] = $data->get();
        //$response['perPageCount'] = $i;

        return json_encode($output);
    }

}
