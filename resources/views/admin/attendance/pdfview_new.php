<style>
    td { font-size: 13px !important; }
</style>
<table align="center" border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: separate; bgcolor:#FFFFF;">
    <tr>
        <td align="center">
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" >
                <tr>
                    <td align="center" width="10%">
                        <img src="<?php echo asset('admin_asset/assets/plugins/images/eliteadmin-logo-dark.png'); ?>" alt="RAUDRA TECHNOCRATS PVT LTD" style="float:center;">
                        <img src="<?php echo asset('admin_asset/assets/plugins/images/eliteadmin-text-dark.png'); ?>" alt="RAUDRA TECHNOCRATS PVT LTD" style="float:center;">
                    </td>
                    <td align="center" width="90%">
                        <h2>RAUDRA TECHNOCRATS PVT LTD</h2>
                        <p>SALARY SLIP FOR THE MONTH : <?php echo $display_month.' '.$display_year; ?></p>
                    </td>
                </tr>
            </table>
            <hr/>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center" >
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">                        
                            <tr >
                                <td width="3%" >
                                    <p>EMP NAME :</p>
                                    <p>DATE JOINED :</p>
                                    <p>Designation :</p>
                                    <p>BANK NAME :</p>
                                    <p>PAYMENT MODE :</p>                                    
                                </td>
                                <td width="47%" style="padding: 0px 40px 0px 40px; border-right: 1px solid grey;">
                                    <p><?php echo strtoupper($data->user->name); ?></p>
                                    <p><?php echo $data->user->employee->joining_date; ?></p>
                                    <p><?php echo $data->user->employee->designation; ?></p>
                                    <p><?php
                                        if (isset($data->user->employee_bank)) {
                                            echo $data->user->employee_bank->account_number;
                                        } else {
                                            echo 'NA';
                                        }
                                        ?></p>
                                    <p>Bank Transfer</p>
                                </td>
                                <td width="3%">
                                    <p>EMP CODE :</p>
                                    <p>PAN :</p>
                                    <p>DEPARTMENT :</p>
                                    <p>A/C NO :</p>
                                    <p>BANK IFSC :</p>                                                                        
                                </td>
                                <td width="47%" style="padding: 0px 40px 0px 40px; border-right: 1px solid grey;">                                        
                                    <p><?php echo $data->user->employee->emp_code; ?></p>
                                    <p><?php
                                        if (isset($data->user->employee_bank)) {
                                            echo $data->user->employee_bank->pancard_number;
                                        } else {
                                            echo 'NA';
                                        }
                                        ?></p>             
                                    <p><?php
                                        if (isset($data->user->employee_bank)) {
                                            echo $data->user->employee_bank->account_number;
                                        } else {
                                            echo 'NA';
                                        }
                                        ?></p>
                                    <p><?php echo $data->user->employee->department->dept_name; ?></p>
                                    <p><?php
                                        if (isset($data->user->employee_bank)) {
                                            echo $data->user->employee_bank->ifsc_code;
                                        } else {
                                            echo 'NA';
                                        }
                                        ?></p>                                    
                                </td>
                            </tr>                            
                            <tr >
                                <td width="25%" >
                                    <b>EARNINGS</b>
                                    <p>Basic</p>
                                    <p>HRA</p>
                                    <p>Other Allowance</p>
                                    <p>Total Earnings(A)</p>
                                </td>
                                <td width="25%" style="padding: 0px 40px 0px 40px; border-right: 1px solid grey;" align="right" >
                                    <br/>
                                    <p><?php echo $data->basic_salary; ?></p>
                                    <p><?php echo $data->hra; ?></p>
                                    <p><?php echo $data->others; ?></p>
                                    <p><?php echo round(($data->basic_salary + $data->hra + $data->others), 2); ?></p>
                                </td>
                                <td width="25%" >
                                    <b>TAXES & DEDUCTIONS</b>
                                    <p>Professional Tax</p>
                                    <p>PF</p>     
                                    <p>Loan</p>
                                    <p>Total Penalty</p>
                                    <p>Total Deductions(c)</p>
                                </td>
                                <td width="25%" style="padding: 0px 40px 0px 40px; border-right: 1px solid grey;" align="right" >
                                    <br/>
                                    <p><?php echo $data->professional_tax; ?></p>
                                    <p><?php echo $data->pf; ?></p>             
                                    <p><?php echo $data->loan_installment ?></p>
                                    <p><?php echo $data->penalty+$data->manual_penalty; ?></p>
                                    <p><?php echo round(($data->professional_tax + $data->pf), 2); ?></p>
                                </td>
                            </tr>                                        
                        </table>

            <?php
            $number = $data->payable_salary;
            $no = floor($number);
            $point = round($number - $no, 2) * 100;
            $hundred = null;
            $digits_1 = strlen($no);
            $i = 0;
            $str = array();
            $words = array('0' => '', '1' => 'one', '2' => 'two',
                '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
                '7' => 'seven', '8' => 'eight', '9' => 'nine',
                '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
                '13' => 'thirteen', '14' => 'fourteen',
                '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
                '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
                '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
                '60' => 'sixty', '70' => 'seventy',
                '80' => 'eighty', '90' => 'ninety');
            $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
            while ($i < $digits_1) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += ($divider == 10) ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number] .
                            " " . $digits[$counter] . $plural . " " . $hundred :
                            $words[floor($number / 10) * 10]
                            . " " . $words[$number % 10] . " "
                            . $digits[$counter] . $plural . " " . $hundred;
                } else
                    $str[] = null;
            }
            $str = array_reverse($str);
            $result = implode('', $str);
            $points = ($point) ? $words[$point / 10] . " " .
                    $words[$point = $point % 10] : '';
            ?>

                        <table width="100%">
                            <tbody>
                                <tr>
                                    <td  >
                                        <p>Net Salary Payable (A-C)</p>                            
                                    </td>
                                    <td  align="right">
                                        <b><?php echo number_format($data->payable_salary,2); ?></b>
                                    </td>                    
                                </tr>
                                <tr>
                                    <td >
                                        <b>Net Salary In Words</b>
                                    </td>
                                    <td  align="right">
                                        <b><?php echo ucfirst($result) . "Rupees  " . ucfirst($points) . " Paise"; ?></b>
                                    </td>                    
                                </tr>
                                <tr>
                                    <td >
                                        <p>This is computer generated statement, does not required signature.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>