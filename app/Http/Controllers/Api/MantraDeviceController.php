<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Test;

class MantraDeviceController extends Controller {

    public function get_attendance_request_data(Request $request) {
        $data = json_decode(file_get_contents('php://input'), true);
        $response=['transStatus'];
        foreach ($data['trans'] as $key=>$trans) {
            $tnx_date = explode(' ', $trans['txnDateTime']);
            $tnx_data = [
                'punchingcode' => $trans['punchId'],
                'date' => $tnx_date[0],
                'time' => $tnx_date[1],
                'Tid' => $trans['dvcId'],
                'execute' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            \App\TbltTimesheet::insert($tnx_data);
            $response['transStatus'][$key]['txnId']=$trans['txnId'];
            $response['transStatus'][$key]['status']=1;
        }
        return response()->json($response);
    }

    public function device_hello(Request $request) {
        //Test::insert(['test_type' => 'Hello']);
        return response()->json(['status' => 1]);
    }

}
