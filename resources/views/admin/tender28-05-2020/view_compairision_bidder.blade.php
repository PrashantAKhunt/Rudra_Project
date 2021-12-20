<div class="row">
	<div class="col-lg-3 col-sm-3">
		<select class="select2 m-b-10 select2-multiple item_id" name="item_id" id="item_id" multiple="multiple">
			@if($items)
				@foreach($items as $key => $value)
					<option value="{{$value}}" {{in_array($value,$item_ids) ? "selected" : ""}}>{{$value}}</option>
				@endforeach
			@endif
		</select>
	</div>
	<div class="col-lg-3 col-sm-3">
		<select class="select2 m-b-10 select2-multiple bidder_id" name="bidder_id" id="bidder_id" multiple="multiple">
			@if($bidder)
				@foreach($bidder as $key => $value)
					<option value="{{$key}}" {{in_array($key,$bidder_ids) ? "selected" : ""}}>{{$value}}</option>
				@endforeach
			@endif
		</select>
	</div>
	<div class="col-lg-3 col-sm-3" style="margin-top: 3px;">
		<button type="button" class="btn btn-danger" id="reset_all">Reset</button>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-sm-12">
		<div class="table-responsive">
			<table class="table table-bordered table-striped" id="bidder_item_table">
				<thead>
					<tr>
						<th>Item No.</th>
						@if($bidder_item)
						@if($company_items)
						<th>{{$company_name}}</th>
						@endif
							@foreach($bidder_item as $key => $value)
									<th>{{$value['bidder_name']}}</th>
							@endforeach
						@endif
					</tr>
				</thead>
				<tbody>
					@if($bidder_item && $company_items)
					@php
					$new_arr = [];
					@endphp
					<?php $min_bid_amount="";$max_bid_amount=""; $bidder_price_arr=[]; $min_bidder="-1";$max_bidder="-1";$same_price_arr=[]; ?>
						@foreach($bidder_item[0]['get_bidder_item'] as $key => $value)
							<tr>
                                                            <td  >
								
                                                                <a href="#" data-toggle="tooltip" title="{{$value['item_of_work']}}" >{{$value['item_no']}}</a>
                                                                    
								</td>
								<?php foreach($bidder_item as $i=>$bidder_price){ 
										$bidder_price_arr[$i]['key']=$i;
                                                                                if(!empty($bidder_price['get_bidder_item'])){
										$bidder_price_arr[$i]['amount']=$bidder_price['get_bidder_item'][$key]['total_amount'];
										if($bidder_price_arr[$i]['amount']==$company_items[$key]['total_amount']){
											array_push($same_price_arr,$i);
										}
                                                                                }
                                                                                else{
                                                                                    $bidder_price_arr[$i]['amount']=0.00;
                                                                                }
									} 
									usort($bidder_price_arr,function($a,$b){
										return $a['amount']-$b['amount'];
									});
									$min_bidder=$bidder_price_arr[0]['key'];
									$min_bid_amount=$bidder_price_arr[0]['amount'];
									$max_bidder=$bidder_price_arr[count($bidder_price_arr)-1]['key'];
									$max_bid_amount=$bidder_price_arr[count($bidder_price_arr)-1]['amount'];
								?>
								
								@if($company_items)
								<td>
									<span class="mytooltip tooltip-effect-5">
									<?php $company_color_code=""; 
									if(($company_items[$key]['total_amount']<$min_bid_amount)){
										$company_color_code="text-success";
										$min_bidder="-1";
									}
									if(($company_items[$key]['total_amount']==$min_bid_amount)){
										$company_color_code="text-success";
									}
									if(($company_items[$key]['total_amount']>$max_bid_amount)){
										$company_color_code="text-danger";
										$max_bidder="-1";
									}
									if(($company_items[$key]['total_amount']==$max_bid_amount)){
										$company_color_code="text-danger";
									}
									?>
					                    <span class="tooltip-item {{ $company_color_code }}" >{{$company_items[$key]['total_amount']}}</span>
					                      <span class="tooltip-content clearfix">
					                      <span class="tooltip-text">
					                   		Quantities : {{$company_items[$key]['qty']}}<br>   	
					                   		Unit Price : {{$company_items[$key]['estimated_rates']}}   	
					                      </span>
					                    </span>
				                   </span>
								</td>
								@endif
								@foreach($bidder_item as $key1 => $bidder_detail)

									@if(count($bidder_detail['get_bidder_item']))
									@if($company_items)
									@php
									$new_arr[$key] = $bidder_detail['get_bidder_item'][$key]['total_amount']; 
									@endphp
										<?php 
										$color_class="";
										
										if(in_array($key1,$same_price_arr)){ $color_class="text-warning"; }
											if($min_bidder==$key1){  $color_class="text-success";  }
											if($max_bidder==$key1){ $color_class="text-danger";}
										?>
										<td  class="{{ $color_class }}">
											<span class="mytooltip tooltip-effect-5">
							                    <span class="tooltip-item">{{$bidder_detail['get_bidder_item'][$key]['total_amount']}}</span>
							                      <span class="tooltip-content clearfix">
							                      <span class="tooltip-text">
							                   		Quantities : {{$bidder_detail['get_bidder_item'][$key]['qty']}}<br>   	
							                   		Unit Price : {{$bidder_detail['get_bidder_item'][$key]['estimated_rates']}}   	
							                      </span>
							                    </span>
						                   </span>
										</td>
									
									
									{{-- {{print_r($new_arr)}} --}}
									@else
										<td class="">{{$bidder_detail['get_bidder_item'][$key]['total_amount']}}
										</td>
									@endif
									@else
									<td>NA</td>
									@endif	
								@endforeach
							</tr>
						@endforeach
					@endif
				</tbody>
			</table>
		</div>
	</div>
</div>
{{-- 
<span class="mytooltip tooltip-effect-5">
    <span class="tooltip-item">{{$bidder_detail['get_bidder_item'][$key]['total_amount']}}</span>
      <span class="tooltip-content clearfix">
      <span class="tooltip-text">
   		Quantities : {{$bidder_detail['get_bidder_item'][$key]['qty']}}<br>   	
   		Unit Price : {{$bidder_detail['get_bidder_item'][$key]['estimated_rates']}}   	
      </span>
    </span>
</span>
 --}}
<script type="text/javascript">
$(document).ready(function(){
	$("#bidder_item_table").DataTable({
		'columnDefs': [
		 {
		      "targets": 0,
		      "className": "text-center",
		 }],
	});
})
</script>