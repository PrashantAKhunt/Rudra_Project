<!DOCTYPE html>
<html>
    <head>
        <style>
            body{
                height: 100vh;
            }
            .letfDiv{
                width: 39%;
                float: left;
                display: inline-block;
            }
            .rightDiv{
                width: 59%;
                float: right;
                display: inline-block;
            }
            .bottom-border{
                border-bottom: 1px solid;
            }
            .bottom-border-dashed{
                border-bottom: 2px solid;
                border-bottom-style: dotted;
            }
            .after-dashed-border{
                display: flex;
            }
            .after-dashed-border span{
                content:"";
                flex: 1;
                border-bottom: 2px solid;
                border-bottom-style: dotted;
                margin-left: 7px;
            }
            .font-weight-500{
                font-weight: 500;
            }
            .font-weight-400{
                font-weight: 400;
            }
            .font-size-14{
                font-size: 14px;
            }
            .font-size-16{
                font-size: 16px;
            }
            .padding-bottom-5{
                padding-bottom: 5px;
            }
        </style>
    </head>
    <body>
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
        <div class="letfDiv">
            <div style="text-align: center; margin-top: 5px;">
                <p style="font-weight: bold; font-size: 16px; line-height: 5px;">Counter Foil</p>
                <h2 style="line-height: 5px; margin-top: -10px;"><img src='{{ public_path()."/assets/images/1200px-SBI-logo.svg.png" }}' width="30px" height="30px" /> STATE BANK OF INDIA</h2>
                <p style="font-weight: bold; font-size: 16px; line-height: 5px;">{{$data['bank_payment_approval_history'][0]->branch}}</p>
            </div>
            <div style="text-align: right;">
                <p class="font-weight-500 font-size-16">Date : <span class="bottom-border">{{$data['date']}}</span></p>
            </div>
            <div style="text-align: left;">
                <table style="width: 100%;"><tr><td style="width: 22%; padding: 0px !important;"><span class="font-weight-500 font-size-14">Received From : </span></td><td style="border-bottom: 2px solid; border-bottom-style: dotted; padding: 0px !important;"><span class="font-weight-500 font-size-14">GAUTAMKUMAR K. BHESANIA</span></td></tr></table>

                <table style="width: 100%; margin-top: 10px;"><tr><td style="padding: 0px !important;"><span class="font-weight-500 font-size-14">By Cheque I Transfer for RTGS | NEFT </span></td></tr></table>

                <table style="width: 100%; margin-top: 10px;"><tr><td style="padding: 0px !important; width: 2%;"><span class="font-weight-500 font-size-14">On</span></td><td style="border-bottom: 2px solid; border-bottom-style: dotted; padding: 0px !important;"><span class="font-weight-500 font-size-14"></span></td></tr></table>

                <p>{{$data['bank_payment_approval_history'][0]->vendor_bank_name}}</p>
                <p>Branch {{$data['bank_payment_approval_history'][0]->branch}}</p>

                <table style="width: 100%;"><tr><td style="width: 16%; padding: 0px !important;"><span class="font-weight-500 font-size-14">Favouring </span></td><td style="border-bottom: 2px solid; border-bottom-style: dotted; padding: 0px !important;"><span class="font-weight-500 font-size-14"><b>{{$data['bank_payment_approval_history'][0]->vendor_name}}</b></span></td></tr></table>

                <p class="font-weight-500 font-size-14">A/c. No. <span>{{$data['bank_payment_approval_history'][0]->user_ac_number}}</span></p>
                <p>Cheque No {{$data['bank_payment_approval_history'][0]->cheque_number}}</p>
                <p>Amount Rs. {{number_format($amount, 2, '.', '')}}</p>
                <p>Bank's Charges Rs. 0.00/-</p>
                <p>Total Rs. {{number_format($amount, 2, '.', '')}}</p>
                <p>({{app(App\Lib\CommonTask::class)->convert_digits_into_words($amount)." Only/-"}})</p>
                <h4 style="text-align: right;">Signature</h4>
            </div>
        </div>
        <div class="rightDiv">
            <div style="text-align: center;">
                <p><span style="border-bottom: 1px solid; font-weight: bold; font-size: 18px;">Application Form RTGS NEFT</span></p>
                <h2 style="line-height: 5px; margin-top: -10px;"><img src='{{ public_path()."/assets/images/1200px-SBI-logo.svg.png" }}' width="30px" height="30px" /> STATE BANK OF INDIA</h2>
                <p style="font-weight: bold; font-size: 16px; line-height: 5px;">{{$data['bank_payment_approval_history'][0]->branch}}</p>
            </div>
            <div style="text-align: left;">
                <p class="font-weight-500 font-size-16">Date : {{$data['date']}}</p>

                <p>Please remit the sum of Rs.{{number_format($amount, 2, '.', '')}} ({{app(App\Lib\CommonTask::class)->convert_digits_into_words($amount)." Only/-"}}) as per detailsbelow by debiting my lour Account No. <span class="bottom-border">{{$data['bank_payment_approval_history'][0]->user_ac_number}}</span> For the total amount including your charges.</p>

                <p>Name of the beneficiary : {{$data['bank_payment_approval_history'][0]->vendor_name}}</p>

                <p>Destination Bank's Name & Branch : {{$data['bank_payment_approval_history'][0]->vendor_bank_name}} & {{$data['bank_payment_approval_history'][0]->branch}}</p>
                <p><div style="width: 50%; display: inline-block;">IFSC Code : {{$data['bank_payment_approval_history'][0]->ifsc}}</div><div style="width: 50%; display: inline-block;">Account No. : {{$data['bank_payment_approval_history'][0]->user_ac_number}}</div></p>

                <p>Amount (in words)({{app(App\Lib\CommonTask::class)->convert_digits_into_words($amount)." Only/-"}})</p>

                <p><div style="width: 33%; display: inline-block;">Amount (in figures) : <span class="bottom-border">{{number_format($amount, 2, '.', '')}}</span></div><div style="width: 33%; display: inline-block;">Charges : 0.00/-</div><div style="width: 34%; display: inline-block;">Total : {{number_format($amount, 2, '.', '')}}</div></p>

                <div style="height: 0px !important; width: 100%;"></div>

                <p><div style="width: 50%; display: inline-block;">Cheque No. : {{$data['bank_payment_approval_history'][0]->cheque_number}}</div><div style="width: 50%; display: inline-block;">Name of applicant : GAUTAMKUMAR K. BHESANIA</div></p>

                <div style="height: 0px !important; width: 100%;"></div>

                <p><div style="width: 50%; display: inline-block;">Date of transfer I cash : <span class="bottom-border">{{$data['date']}}</span></div><div style="width: 50%; display: inline-block;">Address : {{$data['bank_payment_approval_history'][0]->detail}}</div></p>

                <p>Amount : {{number_format($amount, 2, '.', '')}}</p>

                <p><div style="width: 50%; display: inline-block;">

                <table style="width: 98%;"><tr><td style="width: 22%; padding: 0px !important;"><span class="font-weight-500 font-size-14">Scroll No. : </span></td><td style="border-bottom: 1px solid; padding: 0px !important;"><span class="font-weight-500 font-size-14"></span></td></tr></table>

                </div><div style="width: 50%; display: inline-block;">Mobile No. : 7211182514</div></p>
                
                <div style="height: 0px !important; width: 100%;"></div>

                <p><div style="width: 50%; display: inline-block;">
                
                <table style="width: 98%;"><tr><td style="width: 22%; padding: 0px !important;"><span class="font-weight-500 font-size-14">UTR No. : </span></td><td style="border-bottom: 1px solid; padding: 0px !important;"><span class="font-weight-500 font-size-14"></span></td></tr></table>

                </div>
                <div style="width: 50%; float: right; display: inline-block;">

                <table style="width: 98%;"><tr><td style="width: 22%; padding: 0px !important;"><span class="font-weight-500 font-size-14">Signature : </span></td><td style="border-bottom: 1px solid; padding: 0px !important;"><span class="font-weight-500 font-size-14"></span></td></tr></table>

                </div>
                
                <div style="width: 50%; display: inline-block; float: right; text-align: center; margin-top: 20px;"><small>(Please See Conditions)</small></div></p>

                <h3>Conditions for transfer :</h3>
                <p>1. All payment instructions should be carefully checked by the remitter. As crediting the proceeds of the remittance is based on the beneficiary's account number, the name of the other bank and its branch, SBI shall not be responsible if these particulars are not provided correctly be the remitier.</p>
                <p>2. Application/Massage received after the business hours will be sent on the immediate nextworking day.</p>
                <p>3.   SBI shall not be responsible for any delay in processing of the payment due to RBI RTGS system not being available / failure of internal Communication system at the reipient bank / branch / incorrect infomation provided by the mitter/ any incorrect credit accorded by the recipient bank / branch due tro incorreditinformation provided by the remitted.</p>
                <p>4.   (i) Remitting BrancA shall not be liable for any loss or damage arising out or resulting from delay in transmission delivery or non-delivery of electronic message or any mistake, omission of error in transmission of delivery thereof or in encrypting/decryping the messages for any cause whatsoever of from misinterpretation when received or for the action of the destination bank or for any act beyond the control of state Bank of India.<br>(ii) If the recipientbfanch is closed for any reason, the account shall be credited on the immediatenextworking day.<br>(iii) Bank is free to recover charges in respect of remittances returned ofaccountoffaullyfinadequate information.</p>
                <p>5.  IMS have fully read the terms and conditions of the RTGS remittance and shall abideby tha same.</p>

                <p><div style="width: 50%; display: inline-block;"><h4>Authorised Officer</h4></div><div style="width: 50%; display: inline-block; text-align: right;"><h4>Signature of the applicant(s)</h4></div></p>
            </div>
        </div>
    </body>
</html>