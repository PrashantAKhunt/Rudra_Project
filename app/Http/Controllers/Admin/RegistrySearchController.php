<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use Carbon\Carbon;
use App\Inward_outward_users;
use App\Inward_outwards;
use App\Lib\CommonTask;


class RegistrySearchController extends Controller
{

    public $data;
    public $common_task;

    public function __construct()
    {
        $this->data['module_title'] = "Inward Outward";
        $this->data['module_link'] = "admin.registry_search";
        $this->common_task = new CommonTask();
    
    }

    public function registry_search(Request $request)
    {

        $request_data = $request->all();

        $this->data['page_title'] = "Registry Search";
        $this->data['records'] = [];
        $this->data['search_registry'] = "";
        $this->data['registry_date'] = "";
        $responseData = [];

        $from = date('Y-m-d H:i:s');
        $date = strtotime($from);
        $first_date = strtotime("-7 day", $date);
        $second_date   = date('Y-m-d H:i:s', $date);
        
        if($request->method() == 'POST'){
            
            $search_registry = $request->get('search_registry');
            $date = $request->get('registry_date');
    
            $this->data['search_registry'] = $search_registry;
            $this->data['registry_date'] = $date;  
           
            if ($date) {
                $mainDate = explode("-", $date);
                $strFirstdate = str_replace("/", "-", $mainDate[0]);
                $strLastdate = str_replace("/", "-", $mainDate[1]);
                $first_date = date('Y-m-d h:m:s', strtotime($strFirstdate.' -1 day'));
                $second_date = date('Y-m-d h:m:s', strtotime($strLastdate.' +1 day'));
            }

            $apiResponse = $this->common_task->search_document($search_registry);
            $find_ids = [];
            if ($apiResponse->status == true) {
                 
                $registry_arr = $apiResponse->data;
                $find_ids  = array_column($registry_arr, 0);

            }
                
                $responseData = Inward_outwards::select('inward_outward_doc_sub_category.sub_category_name', 'company.company_name', 'project.project_name', 'inward_outwards.id', 'inward_outwards.document_file', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.description','inward_outwards.ref_outward_number', 'inward_outwards.type', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date')
                    ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->where(function ($query) use ($find_ids, $apiResponse, $search_registry) {
                        if ($apiResponse->status == true) {
                    
                            $query->whereIn('inward_outwards.id', $find_ids);
							$query->orWhere('inward_outwards.inward_outward_title','like','%' . $search_registry . '%');
							$query->orWhere('inward_outwards.description','like', '%' . $search_registry . '%');
                        }

                    })
                    
                    ->where(function ($query) use ($request_data , $first_date , $second_date) {
                        if (isset($request_data['registry_date'])) {
                      
                            $query->whereBetween('inward_outwards.created_at', [$first_date, $second_date]);
                        }

                    })
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->get();
                
                        foreach ($responseData as $key => $value) {
                            $registry_user_list = Inward_outward_users::join('users', 'users.id', '=', 'inward_outward_users.user_id')
                                ->where('inward_outward_users.inward_outward_id', '=', $value->id)
                                ->pluck('users.name')->toArray();
                
                            $responseData[$key]->users_list = implode(',', $registry_user_list);
                        }
    
            $this->data['records'] = $responseData;
        }

        return view('admin.RegistrySearch.index', $this->data);
    }


                                  

}
