<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Site_manage_work_progress;
use App\Site_manage_boq;
use App\User;
use App\ProjectManager;

class SitemanagementController extends Controller {

    private $page_limit = 20;

    public function add_work_progress(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'boq_id' => 'required',
                    'qty_planned' => 'required',
                    'plan_date' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $boq_detail = Site_manage_boq::where('id', $request_data['boq_id'])
                ->get();
        //check if already planned
        $check_result = Site_manage_work_progress::where('boq_id', $request_data['boq_id'])
                ->where('plan_date', $request_data['plan_date'])
                ->get(['id']);
        if ($check_result->count() > 0) {
            return response()->json(['status' => false, 'msg' => "Quantity is already planned and added for this date.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }
        $work_arr = [
            'boq_id' => $request_data['boq_id'],
            'plan_date' => $request_data['plan_date'],
            'parent_boq_id' => $boq_detail[0]->parent_boq,
            'today_plan_qty' => $request_data['qty_planned'],
            'user_id' => $request_data['user_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];
        Site_manage_work_progress::insert($work_arr);
        
        //set tomorrow plan quantity
        $yesterday_record= Site_manage_work_progress::where('boq_id',$request_data['boq_id'])
                ->where('plan_date','!=',$request_data['plan_date'])
                ->orderBy('plan_date','DESC')
                ->first();
        if($yesterday_record){
            $yesterday_record->tomorrow_plan_qty=$request_data['qty_planned'];
            $yesterday_record->save();
        }
        return response()->json(['status' => true, 'msg' => "Tomorrow planned quantity is added.", 'data' => []]);
    }

    public function site_company_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $company_list = \App\Companies::where('status', 'Enabled')
                ->get();

        return response()->json(['status' => true, 'msg' => "data available", 'data' => $company_list]);
    }

    public function site_project_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'company_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $projectmanager = ProjectManager::where('user_id',$request_data['user_id'])->pluck('project_id');

        $project_list = \App\Projects::where('company_id', $request_data['company_id'])
                ->whereIn('id', $projectmanager)
                ->where('status', 'Enabled')
                ->get();
        if ($project_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        return response()->json(['status' => true, 'msg' => "data available", 'data' => $project_list]);
    }

    public function get_project_boq(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'project_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $boq_list = Site_manage_boq::with(['get_item_blocks', 'sub_item' => function($query) {
                        return $query->with(['get_item_blocks'])->whereColumn('parent_boq', '!=', 'id');
                    }])->where('project_id', $request_data['project_id'])
                ->where('status', 'Enabled')
                ->whereColumn('parent_boq', 'id')
                ->where(function($query) use($request_data) {
                    if (isset($request_data['search_keyword']) && $request_data['search_keyword'] != "") {
                        $query->where('item_description', 'Like', $request_data['search_keyword'] . '%');
                    }
                })
                ->get();
        if ($boq_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        return response()->json(['status' => true, 'msg' => "data available", 'data' => $boq_list]);
    }

    public function get_work_progress(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'boq_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
		$all_date = $this->get_all_dates($request_data['start_date'],$request_data['end_date']);
        $main_arr=[];
		foreach ($all_date as $key => $value) {
            //$main_arr[$key]['date']=$value;
			$site_work_progress = Site_manage_work_progress::with(['boq_detail'])
                ->where('plan_date', '=', $value)
				->where('boq_id', $request_data['boq_id'])
                ->get()->toArray();
        
			foreach($site_work_progress as $key1=>$site_work){
				$measurement_list= \App\Site_manage_measurement::where('boq_id', $site_work['boq_id'])
						->where('measure_date',$site_work['plan_date'])
						->get()->toArray();
				$site_work_progress[$key1]['measurement_detail']=$measurement_list;
			}
			if(count($site_work_progress)>0){
			$main_arr[$key]=$site_work_progress[0];
			}
			$main_arr[$key]['plan_date']=$value;
		}
        
        if (count($main_arr) == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        return response()->json(['status' => true, 'msg' => "data available", 'data' => $main_arr]);
    }

    public function insert_block_measurement(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'measure_date' => 'required',
                    'item_block_id' => 'required',
                    'boq_id' => 'required',
                    'co_efficient' => 'required',
                    'no_unit' => 'required',
                    'length' => 'required',
                    'width' => 'required',
                    'height' => 'required',
                    //'remark' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        
        $request_data = $request->all();
        
        //check if already inserted
        $check_result= \App\Site_manage_measurement::where('boq_id',$request_data['boq_id'])
                ->where('item_block_id',$request_data['item_block_id'])
                ->where('measure_date',$request_data['measure_date'])
                ->get();
        
        if($check_result->count()>0){
            return response()->json(['status' => false, 'msg' => "Record already inserted. Please edit it if need any changes.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }
        if($request_data['measure_date']!=date('Y-m-d')){
            return response()->json(['status' => false, 'msg' => "You can add measurements of today's date only.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }
        //get boq detail
        $boq_detail= Site_manage_boq::where('id',$request_data['boq_id'])
                    ->first();
        
        $work_progress_obj= Site_manage_work_progress::where('plan_date',$request_data['measure_date'])
                ->where('boq_id',$request_data['boq_id'])
                ->first();
        if(!$work_progress_obj){
            return response()->json(['status' => false, 'msg' => "You have not added planed work for this date for this BOQ item. Please first add planned quantity then only you can add measurements.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }
        
        $measurement_obj=new \App\Site_manage_measurement();
        $measurement_obj->user_id=$request_data['user_id'];
        $measurement_obj->boq_id=$request_data['boq_id'];
        $measurement_obj->item_block_id=$request_data['item_block_id'];
        $measurement_obj->measure_date=$request_data['measure_date'];
        $measurement_obj->co_efficient=$request_data['co_efficient'];
        $measurement_obj->no_unit=$request_data['no_unit'];
        $measurement_obj->length=$request_data['length'];
        $measurement_obj->width=$request_data['width'];
        $measurement_obj->height=$request_data['height'];
        if(isset($request_data['remark'])){
        $measurement_obj->remark=$request_data['remark'];
        }
        $measurement_obj->created_at=date('Y-m-d H:i:s');
        $measurement_obj->created_ip=$request->ip();
        $measurement_obj->updated_at=date('Y-m-d H:i:s');
        $measurement_obj->updated_ip=$request->ip();
        $length=1;$height=1;$width=1;$co_efficient=1;$no_unit=1;
        if($request_data['length']>0){
            $length=$request_data['length'];
        }
        if($request_data['height']>0){
            $height=$request_data['height'];
        }
        if($request_data['width']>0){
            $width=$request_data['width'];
        }
        if($request_data['co_efficient']>0){
            $co_efficient=$request_data['co_efficient'];
        }
        if($request_data['no_unit']>0){
            $no_unit=$request_data['no_unit'];
        }
        $measurement_obj->quantity=$length*$height*$width*$co_efficient*$no_unit;
        
        $measurement_obj->save();
        
        
        $work_progress_obj->qty_achieved=$work_progress_obj->qty_achieved+$measurement_obj->quantity;
        $work_progress_obj->achievement_percent=($work_progress_obj->qty_achieved*100)/$work_progress_obj->today_plan_qty;
        /*if($work_progress_obj->achievement_percent<100.00){
            if(isset($request_data['shortfall_reason'])){
            $work_progress_obj->shortfall_reason=$request_data['shortfall_reason'];
            }
        }*/
        $yesterday_record= Site_manage_work_progress::where('boq_id',$request_data['boq_id'])
                ->where('plan_date','!=',$request_data['measure_date'])
                ->orderBy('plan_date','DESC')
                ->first();
        if($yesterday_record){
            $work_progress_obj->cumulative_qty_executed=$yesterday_record->cumulative_qty_executed+$work_progress_obj->qty_achieved;
            //get original boq details
            
            $work_progress_obj->cumulative_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity;
            if($boq_detail->quantity_as_drawing>0){
			$work_progress_obj->cumulative_drawing_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity_as_drawing;
            }
			else{
				$work_progress_obj->cumulative_drawing_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity;
			}
        }
        else{
            $work_progress_obj->cumulative_qty_executed=$work_progress_obj->qty_achieved;
            $work_progress_obj->cumulative_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity;
			if($boq_detail->quantity_as_drawing){
            $work_progress_obj->cumulative_drawing_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity_as_drawing;
			}
			else{
				$work_progress_obj->cumulative_drawing_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity;
			}
        }
        $work_progress_obj->updated_at=date('Y-m-d H:i:s');
        $work_progress_obj->updated_ip=$request->ip();
        $work_progress_obj->save();
        
        //update abstract table
        $abstract_obj= \App\Site_manage_daily_abstract::where('boq_id',$request_data['boq_id'])
                ->whereDate('abstract_date',$request_data['measure_date'])
                ->first();
        //get previous day abstract report
        $last_abstract= \App\Site_manage_daily_abstract::where('boq_id',$request_data['boq_id'])
                ->whereDate('abstract_date','!=',$request_data['measure_date'])
                ->orderBy('abstract_date','DESC')
                ->limit(1)
                ->get();
        
        if($last_abstract->count()==0){
            $yesterday_qty=0.00;
        }
        else{
            $yesterday_qty=$last_abstract[0]->qe_executed_today_qty;
        }
        if(!$abstract_obj){
			
            $abstract_obj=new \App\Site_manage_daily_abstract();
        }
		
        $abstract_obj->boq_id=$request_data['boq_id'];
        $abstract_obj->qe_prev_day_qty=$yesterday_qty;
        $abstract_obj->qe_executed_today_qty=$work_progress_obj->qty_achieved;
        $abstract_obj->qe_total_qty=$abstract_obj->qe_prev_day_qty+$abstract_obj->qe_executed_today_qty;
        $abstract_obj->wea_prev_day=$yesterday_qty*$boq_detail->rate;
        $abstract_obj->wea_today= $abstract_obj->qe_executed_today_qty*$boq_detail->rate;
        $abstract_obj->wea_total=$abstract_obj->qe_total_qty*$boq_detail->rate;
        $abstract_obj->bill_generate=0;
        $abstract_obj->abstract_date=$request_data['measure_date'];
        $abstract_obj->created_at=date('Y-m-d H:i:s');
        $abstract_obj->created_ip=$request->ip();
		
        $abstract_obj->save();
        
        return response()->json(['status' => true, 'msg' => "Measurements successfully saved.", 'data' => []]);
    }
    
    public function edit_block_measurement(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'measure_id'=>'required',
                    'co_efficient' => 'required',
                    'no_unit' => 'required',
                    'length' => 'required',
                    'width' => 'required',
                    'height' => 'required',
                    //'remark' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        
        $request_data = $request->all();
        //get measurement detail
        $measurement_obj= \App\Site_manage_measurement::where('id',$request_data['measure_id'])
                ->first();
        
        if(!$measurement_obj){
            return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
        }
        
        
        if($measurement_obj->measure_date!=date('Y-m-d')){
            return response()->json(['status' => false, 'msg' => "You can edit measurements of today's date only.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }
        //get boq detail
        $boq_detail= Site_manage_boq::where('id',$measurement_obj->boq_id)
                    ->first();
        
        $work_progress_obj= Site_manage_work_progress::where('plan_date',$measurement_obj->measure_date)
                ->where('boq_id',$measurement_obj->boq_id)
                ->first();
				
        if(!$work_progress_obj){
            return response()->json(['status' => false, 'msg' => "You have not added planed work for this date for this BOQ item. Please first add planned quantity then only you can add measurements.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }
        
        $old_qty=$measurement_obj->quantity;
        $measurement_obj->user_id=$request_data['user_id'];
        
        $measurement_obj->co_efficient=$request_data['co_efficient'];
        $measurement_obj->no_unit=$request_data['no_unit'];
        $measurement_obj->length=$request_data['length'];
        $measurement_obj->width=$request_data['width'];
        $measurement_obj->height=$request_data['height'];
        if(isset($request_data['remark'])){
        $measurement_obj->remark=$request_data['remark'];
        }
        
        
        $measurement_obj->updated_at=date('Y-m-d H:i:s');
        $measurement_obj->updated_ip=$request->ip();
        $length=1;$height=1;$width=1;$co_efficient=1;$no_unit=1;
        if($request_data['length']>0){
            $length=$request_data['length'];
        }
        if($request_data['height']>0){
            $height=$request_data['height'];
        }
        if($request_data['width']>0){
            $width=$request_data['width'];
        }
        if($request_data['co_efficient']>0){
            $co_efficient=$request_data['co_efficient'];
        }
        if($request_data['no_unit']>0){
            $no_unit=$request_data['no_unit'];
        }
        $measurement_obj->quantity=$length*$height*$width*$co_efficient*$no_unit;
        
        $measurement_obj->save();
        
        
        $work_progress_obj->qty_achieved=($work_progress_obj->qty_achieved-$old_qty)+$measurement_obj->quantity;
		if($work_progress_obj->today_plan_qty>0){
        $work_progress_obj->achievement_percent=($work_progress_obj->qty_achieved*100)/$work_progress_obj->today_plan_qty;
		}
		else{
			$work_progress_obj->achievement_percent=0.00;
		}
		
        /*if($work_progress_obj->achievement_percent<100.00){
            if(isset($request_data['shortfall_reason'])){
            $work_progress_obj->shortfall_reason=$request_data['shortfall_reason'];
            }
        }*/
        $yesterday_record= Site_manage_work_progress::where('boq_id',$measurement_obj->boq_id)
                ->where('plan_date','!=',$measurement_obj->measure_date)
                ->orderBy('plan_date','DESC')
                ->first();
				
        if($yesterday_record){
            $work_progress_obj->cumulative_qty_executed=$yesterday_record->cumulative_qty_executed+$work_progress_obj->qty_achieved;
            //get original boq details
            
            $work_progress_obj->cumulative_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity;
            if($boq_detail->quantity_as_drawing>0){
			$work_progress_obj->cumulative_drawing_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity_as_drawing;
			}
			else{
				$work_progress_obj->cumulative_drawing_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity;
			}
            
        }
        else{
            $work_progress_obj->cumulative_qty_executed=$work_progress_obj->qty_achieved;
            $work_progress_obj->cumulative_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity;
            if($boq_detail->quantity_as_drawing>0){
			$work_progress_obj->cumulative_drawing_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity_as_drawing;
			}
			else{
				$work_progress_obj->cumulative_drawing_progress_percent=($work_progress_obj->cumulative_qty_executed*100)/$boq_detail->quantity;
			}
        }
		
        $work_progress_obj->updated_at=date('Y-m-d H:i:s');
        $work_progress_obj->updated_ip=$request->ip();
        $work_progress_obj->save();
        
        //update abstract table
        $abstract_obj= \App\Site_manage_daily_abstract::where('boq_id',$measurement_obj->boq_id)
                ->whereDate('abstract_date',$measurement_obj->measure_date)
                ->first();
        //get previous day abstract report
        $last_abstract= \App\Site_manage_daily_abstract::where('boq_id',$measurement_obj->boq_id)
                ->whereDate('abstract_date','!=',$measurement_obj->measure_date)
                ->orderBy('abstract_date','DESC')
                ->limit(1)
                ->get();
        
        if($last_abstract->count()==0){
            $yesterday_qty=0.00;
        }
        else{
            $yesterday_qty=$last_abstract[0]->qe_executed_today_qty;
        }
        
        
        $abstract_obj->qe_prev_day_qty=$yesterday_qty;
		
        $abstract_obj->qe_executed_today_qty=$work_progress_obj->qty_achieved;
        $abstract_obj->qe_total_qty=$abstract_obj->qe_prev_day_qty+$abstract_obj->qe_executed_today_qty;
        $abstract_obj->wea_prev_day=$yesterday_qty*$boq_detail->rate;
        $abstract_obj->wea_today= $abstract_obj->qe_executed_today_qty*$boq_detail->rate;
        $abstract_obj->wea_total=$abstract_obj->qe_total_qty*$boq_detail->rate;
        $abstract_obj->bill_generate=0;
        
        $abstract_obj->created_at=date('Y-m-d H:i:s');
        $abstract_obj->created_ip=$request->ip();
        $abstract_obj->save();
        
        
        return response()->json(['status' => true, 'msg' => "Measurements successfully saved.", 'data' => []]);
    }

    public function work_shortfall_reason(Request $request){
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'work_progress_id' => 'required',
                    'shortfall_reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $work_progress = Site_manage_work_progress::whereId($request->get('work_progress_id'))->update(['shortfall_reason' => $request->get('shortfall_reason'), 'updated_ip' => $request->ip()]);
        if($work_progress){
            return response()->json(['status' => true, 'msg' => "Work reason added", 'data' => []]);
        }

        return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);       
    }

    public function get_measurement(Request $request){
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'boq_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
            
        /*$measurement_list= \App\Site_manage_measurement::where('boq_id', $request_data['boq_id'])
                    ->get();
        if(count($measurement_list) == 0){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }*/

        $all_date = $this->get_all_dates($request_data['start_date'],$request_data['end_date']);
        $measurement_arr = [];
        foreach ($all_date as $key => $value) {
            $measurement_arr[$key]['date']=$value;
            //get measurement block
            $blocks = \App\Site_manage_item_block::where('boq_id',$request_data['boq_id'])
                    ->get();
            
            if($blocks->count()==0){
                return response()->json(['status' => false, 'msg' => "No block designes added yet for this BOQ item. Please add atleast one design.", 'data' => [], 'error' => config('errors.no_record.code')]);
            }
            
            foreach($blocks as $block_key=>$block){
            $measurement_arr[$key]['data'][$block_key]['block_detail']=$block;
            $measurement_arr[$key]['data'][$block_key]['block_detail']['block_measurement'] = \App\Site_manage_measurement::where('boq_id', $request_data['boq_id'])
                    ->where('measure_date', $value)
                    ->where('item_block_id',$block->id)
                    ->get()->toArray();
            }
        }

        // echo "<pre>";print_r($all_date);exit;
        if(count($measurement_arr) == 0){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        return response()->json(['status' => true, 'msg' => "data available", 'data' => $measurement_arr]);
    }

    public function get_all_dates($date1,$date2){
        // Declare an empty array 
        $array = array(); 
          
        // Use strtotime function 
        $Variable1 = strtotime($date1); 
        $Variable2 = strtotime($date2); 
          
        // Use for loop to store dates into array 
        // 86400 sec = 24 hrs = 60*60*24 = 1 day 
        for ($currentDate = $Variable1; $currentDate <= $Variable2;  
                                        $currentDate += (86400)) { 
                                              
        $Store = date('Y-m-d', $currentDate); 
        $array[] = $Store; 
        }

        return $array; 
    }
}
