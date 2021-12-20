<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Gnello\OpenFireRestAPI\Client;
use Illuminate\Support\Facades\Validator;
class OpenfireconnectionController extends Controller
{
    public $client;
    public function __construct()
    {
        $this->client = new Client([
            'client' => [
                'secretKey' => 'T11HrvdzUq1xfoOEKT',
                'scheme' => 'http',
                //'basePath' => '/plugins/restapi/v1/',
                'host' => 'localhost',
                'port' => '9090',
            ],

        ]);
        //print_r($this->client); die();
    }

    public function add_open_fire_user(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            
            
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $user_data=User::where('id',$request_data['user_id'])->get();
        $response = $this->client->getUserModel()->createUser([
            "username" => 1000+$user_data[0]->id,
            "name" => $user_data[0]->name,
            "password" => 1000+$user_data[0]->id.'@'.$request_data['user_id'],

        ]);
        if ($response->getStatusCode() == 201) {
            return response()->json(['status'=>true,'msg'=>"User successfully added",'data'=>[]]);
        }
        else{
            return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
        }
    }
}
