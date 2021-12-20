<!DOCTYPE html>
<html>
<head>
<style>
.acc_details{
	border-collapse: collapse;
}
table, th, td {
  vertical-align: middle;
}
.acc_details td, th {
  border: 1px solid black;
  font-size: 10px;
  padding-left: 3px;
}
.td-left{
	text-align: left;
	width: 50%;
}
.td-right{
	text-align: right;
	width: 50%;
}
footer {
	position: fixed; 
	bottom: -50px; 
	left: 0px; 
	right: 0px;
	height: 50px;
	line-height: 35px;
}
.box{
	font-weight: bold;
	border-right: 1px solid black;
	padding-left: 2px;
	padding-right: 2px;
	margin-top: -2px;
	margin-bottom: -5px;
}
</style>
</head>
<body>
	<footer>
		{{ 'RTFL/BOB/'.$data['year'].'/'.$data['page_counter'] }}
	</footer>

	<?php //print_r($data['bank_payment_approval_history'][0]->branch);exit; ?>
	<table style="width:100%;">
		<tbody>
			<tr>
				<td style="width: 40% !important;">
					<img src='{{ public_path()."/assets/images/bank-of-baroda-1552282218.jpg" }}' height="45" width="130" />
				</td>
				<td style="text-align: center; width: 40% !important;">
					<p style="margin-top: -5px;"><strong>Bank Of Baroda</strong></p>
				</td>
				<td style="width: 17% !important;">
					<img style="float: right;" src='{{ public_path()."/assets/images/bank-of-baroda-1552282218.jpg" }}' height="45" width="130" />
				</td>
			</tr>
		</tbody>
	</table>
	<table style="width:100%; font-size: 12px;">
		<tbody>
			<tr>
				<td style="width: 16%;">
					Branch
				</td>
				<td style="width: 20%; font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->branch}}
				</td>
				<td style="width: 2% !important;"></td>
				<td style="width: 22%; font-weight: bold;">
					ANNEXURE - II
				</td>
				<td style="width: 40%; font-weight: bold;">
					PAYING-IN-SLIP-FOR NEFT / RTGS
				</td>
			</tr>
		</tbody>
	</table>
	<table style="width:100%; font-size: 12px;">
		<tbody>
			<tr>
				<td style="width: 38%;">
					
				</td>
				<td style="width: 22%; font-weight: bold;">
					Branch : {{$data['bank_payment_approval_history'][0]->branch}}
				</td>
				<td style="width: 20%; font-weight: bold;">
					Date: {{$data['date']}}
				</td>
				<td style="width: 20%; font-weight: bold;">
					Time of Receipt :
				</td>
			</tr>
		</tbody>
	</table>
	<table style="width:100%; font-size: 12px;">
		<tbody>
			<tr>
				<td style="width: 16%;">
					Date
				</td>
				<td style="width: 22%;">
					{{$data['date']}}
				</td>
				<td style="width: 42%; font-weight: bold;">
					(FOR RTGS-AMOUNT MUST BE FOR ` 2 LACS OR MORE)
				</td>
				<td style="width: 20%; text-align: right;">
					Form No. 404
				</td>
			</tr>
		</tbody>
	</table>
	<table style="width:100%; font-size: 12px;">
		<tbody>
			<tr>
				<td style="width: 16%;">
					Counter Foil
				</td>
				<td style="width: 22%;">
					
				</td>
				<td style="width: 62%; font-weight: bold;">
					Application for Electronic Funds Transfer to a Customer of another Bank through RTGS/NEFT. (to be filled in by Customer)
				</td>
			</tr>
		</tbody>
	</table>
	@php
		$igst_amount = 0;
		$cgst_amount = 0;
		$sgst_amount = 0;
		$amount = $data['bank_payment_approval_history'][0]->amount;

		if($data['bank_payment_approval_history'][0]->igst_amount){
			$igst_amount = $data['bank_payment_approval_history'][0]->igst_amount;
		}
		if($data['bank_payment_approval_history'][0]->cgst_amount){
			$cgst_amount = $data['bank_payment_approval_history'][0]->cgst_amount;
		}
		if($data['bank_payment_approval_history'][0]->sgst_amount){
			$sgst_amount = $data['bank_payment_approval_history'][0]->sgst_amount;
		}

		$amount = $amount + $igst_amount + $cgst_amount + $sgst_amount;
	@endphp
	<table style="width:100%; margin-top: 10px;" class="acc_details">
		<tbody>
			<tr>
				<td>
					Sender's A/C. No.
				</td>
				<td style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->user_ac_number}}
				</td>
				<td colspan="3">
					Sender's A/C. No. of base branch
				</td>
				<td colspan="6" style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->user_ac_number}}
				</td>
			</tr>
			<tr>
				<td>
					Name of A/C. Holder
				</td>
				<td style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->company_name}}
				</td>
				<td colspan="3">
					Name of A/C. Holder (Sender)
				</td>
				<td colspan="6" style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->company_name}}
				</td>
			</tr>
			<tr>
				<td>
					NEFT/RTGS
				</td>
				<td style="font-weight: bold;">
					{{$data['type']}}
				</td>
				<td colspan="3">
					NEFT/RTGS
				</td>
				<td colspan="6" style="font-weight: bold;">
					{{$data['type']}}
				</td>
			</tr>
			<tr>
				<td>
					Favouring Name
				</td>
				<td style="font-weight: bold;">
					@if("BANK OF BARODA" == $data['bank_payment_approval_history'][0]->vendor_bank_name)
						{{"BANK OF BARODA"}}
					@else
						{{$data['bank_payment_approval_history'][0]->vendor_name}}
					@endif
				</td>
				<td colspan="3">
					Favouring / Beneficiary Name
				</td>
				<td colspan="6" style="font-weight: bold;">
					@if("BANK OF BARODA" == $data['bank_payment_approval_history'][0]->vendor_bank_name)
						{{"BANK OF BARODA"}}
					@else
						{{$data['bank_payment_approval_history'][0]->vendor_name}}
					@endif
				</td>
			</tr>
			<tr>
				<td>
					Bank
				</td>
				<td style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->vendor_bank_name}}
				</td>
				<td colspan="3">
					Receiving Bank Name
				</td>
				<td colspan="6" style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->vendor_bank_name}}
				</td>
			</tr>
			<tr>
				<td>
					Branch
				</td>
				<td style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->vender_bank_branch}}
				</td>
				<td colspan="3">
					Receiving Branch
				</td>
				<td colspan="6" style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->vender_bank_branch}}
				</td>
			</tr>
			<tr>
				<td>
					IFSC / NEFT Code
				</td>
				<td style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->ifsc}}
				</td>
				<td colspan="3">
					Receiving Branch IFSC Code
				</td>
				<td colspan="6" style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->ifsc}}
				</td>
			</tr>
			<tr>
				<td>
					Beneficiary A/c. No.
				</td>
				<td style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->ac_number}}
				</td>
				<td colspan="3">
					Beneficiary A/c. No.
				</td>
				<td colspan="6" style="font-weight: bold; padding: 0px;">
					@for($i=0; $i<'18'; $i++)
						<span class="box">
							@if(isset($data['bank_payment_approval_history'][0]->ac_number[$i]))
								{{ $data['bank_payment_approval_history'][0]->ac_number[$i] }}
							@endif
						</span>
					@endfor
				</td>
			</tr>
			<tr>
				<td>
					Beneficiary A/c. Type
				</td>
				<td style="font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->account_type}}
				</td>
				<td colspan="6">
					Beneficiary A/c. Type: (SB/CA/OO/CC/NRE/Credit Card) / Remittance to Indo Nepal
				</td>
				<td colspan="3" style="text-align: center; font-weight: bold;">
					{{$data['bank_payment_approval_history'][0]->account_type}}
				</td>
			</tr>
			<tr>
				<td>
					
				</td>
				<td>
					
				</td>
				<td colspan="4">
					Message for beneficiary (applicable for RTGS Only)
				</td>
				<td colspan="5" style="font-weight: bold;">
					
				</td>
			</tr>
			<tr>
				<td>
					Amount
				</td>
				<td style="font-weight: bold;">
					{{number_format($amount, 2, '.', '')}}
				</td>
				<td colspan="3">
					Amount
				</td>
				<td colspan="6" style="font-weight: bold;">
					{{number_format($amount, 2, '.', '')}}
				</td>
			</tr>
			<tr>
				<td>
					Exchange
				</td>
				<td style="font-weight: bold;">
					
				</td>
				<td colspan="3">
					Exchange
				</td>
				<td colspan="6" style="font-weight: bold;">
					
				</td>
			</tr>
			<tr>
				<td>
					Total Amount
				</td>
				<td style="font-weight: bold;">
					{{number_format($amount, 2, '.', '')}}
				</td>
				<td colspan="3">
					Total Amount
				</td>
				<td colspan="6" style="font-weight: bold;">
					{{number_format($amount, 2, '.', '')}}
				</td>
			</tr>
			<tr>
				<td colspan="2" rowspan="3">
					@php
						$text = app(App\Lib\CommonTask::class)->convert_digits_into_words($amount)." Only/-";
					@endphp
					Total Amt. in words: <span style="font-weight: bold;">{!! wordwrap($text, 45, "<br />\n") !!}</span>
				</td>
				<td colspan="3">
					Total Amt. in words
				</td>
				<td colspan="6" style="font-weight: bold;">
					{{app(App\Lib\CommonTask::class)->convert_digits_into_words($amount)}} Only/-
				</td>
			</tr>
			<tr>
				<td colspan="3">
					Beneficiary A/c. No.
				</td>
				<td colspan="6" style="font-weight: bold;">
					@for($i=0; $i<'18'; $i++)
						<span class="box">
							@if(isset($data['bank_payment_approval_history'][0]->ac_number[$i]))
								{{ $data['bank_payment_approval_history'][0]->ac_number[$i] }}
							@endif
						</span>
					@endfor
				</td>
			</tr>
			<tr>
				<td colspan="3" style="font-weight: bold;">
					(to be written 2nd time as per RBI guidelines)
				</td>
				<td colspan="6">
					
				</td>
			</tr>
		</tbody>
	</table>
	<table style="width:100%; margin-top: 30px; font-size: 10px !important">
		<tbody>
			<tr>
				<td style="font-weight: bold; width: 59%;">
					Signature of Customer
				</td>
				<td rowspan="3" colspan="3" style="width: 41%;">
					I/We request you to make the above remittance. It is being understood that the remittance is to be sent at my/our own risk and my/our responsibility and on the distinct understanding that no liability thatsoever is to attach to the Bank for any loss or damage arising or resulting from delay in transmission, delivery or non delivery of the message or for any mistake, exchange or error in transmission or delivery thereof or in deciphering the message from whatsoever cause or from its misinterpretation when received or from failure to properly identity the person name. I/We also hereby undertake to refund to bank any over remittance, which is made as per RBI RTGS/NEFT scheme.
				</td>
			</tr>
			<tr>
				<td>
					<span style="font-weight: bold;">Mobile No.</span> 7211182514
				</td>
			</tr>
			<tr>
				<td>
					<span style="font-weight: bold;">PAN</span> {{ $data['bank_payment_approval_history'][0]->pan_no }}
				</td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>
			<tr>
				<td style="font-weight: bold; width: 32%;">
					Bank's Seal 
				</td>
				<td style="width: 68%;">
					Please remit the amount as per above details by (i) debiting my/our <span style="font-weight: bold;">SB/CA/OD/CC A/c. No.</span> with Satellite Branch (ii) I/We here with tender cheque No. Drawn on our a/c. towards its cost.
				</td>
			</tr>
		</tbody>
	</table>
	<table style="width:100%; margin-top: 40px; font-weight: bold; font-size: 10px !important">
		<tbody>
			<tr>
				<td style="width: 38% !important;">
					Sign. Of Clerk/Cashier/Teller
				</td>
				<td style="width: 22% !important;">
					Signature of Customer
				</td>
				<td style="width: 14% !important;">
					Sign of Operator
				</td>
				<td style="width: 14% !important;">
					Sign of Officer
				</td>
				<td style="width: 12% !important;">
					Sign of Officer
				</td>
			</tr>
			<tr>
				<td style="width: 38% !important;">
					
				</td>
				<td style="width: 22% !important;">
					Mobile No. 7211182514
				</td>
				<td style="width: 14% !important;">
					(Who Created Mes.)
				</td>
				<td style="width: 14% !important;">
					(who Authorized)
				</td>
				<td style="width: 12% !important;">
					(Who Verified)
				</td>
			</tr>
			<tr>
				<td style="width: 38% !important;">
					
				</td>
				<td style="width: 22% !important;">
					PAN: {{ $data['bank_payment_approval_history'][0]->pan_no }}
				</td>
				<td style="width: 14% !important;">
					
				</td>
				<td style="width: 14% !important;">
					
				</td>
				<td style="width: 12% !important;">
					
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>