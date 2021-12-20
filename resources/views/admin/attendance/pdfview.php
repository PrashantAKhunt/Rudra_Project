<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body >
<table cellpadding="0" cellspacing="0" style="text-align: left; border: 2px solid; width: 100%; border-collapse: separate; background-color: #ffffff; text-transform: uppercase;">
    <tr>
        <td style="text-align: center;">
            <table cellpadding="0" cellspacing="0" style="text-align: center; border: 0; width: 100%;" >
                <tr>
                    <td style="text-align: center; width: 10%;">
                        <!-- <img src="<?php echo url('admin_asset/assets/plugins/images/logo_pdf.png'); ?>" alt="" style="float:center; width: 80%;"> -->
                                    <img src="<?php echo asset('admin_asset/assets/plugins/images/logo_pdf.png'); ?>" alt="RAUDRA TECHNOCRATS PVT LTD" style="float:center;padding-top: 50px;padding-left: 12px;" height="70">
                    </td>
                    <td style="text-align: center; width: 70%;">
                        <h2>RAUDRA TECHNOCRATS PVT LTD</h2>
                        <p>SALARY SLIP FOR THE MONTH : <?php echo $display_month_year; ?></p>
                    </td>
                    <td style="text-align: left; width: 20%; line-height: 1.4; ">
                        <h4>RAUDRA TECHNOCRATS PVT LTD</h4>
                        <h5 style="font-weight: 500; padding-right: 30px;">BLOCK/C-118, SWAGAT RAIN FOREST 2, OPP. SWAMINARAYAN DHAM, KOBA - GANDHINAGAR HIGHWAY, KUDASAN, GANDHINAGAR Gandhinagar Gujarat 382421</h5>
                    </td>
                </tr>
            </table>
            <hr style="border: 1px solid;" />
            <table cellpadding="0" cellspacing="0" style="text-align: left; border: 0; width: 100%;">
                <tr>
                    <td style="padding: 0px 40px 0px 40px;">
                        <p>EMP NAME :</p> 
                        <p>DATE JOINING :</p> 
                        <p>Designation :</p>
                        <!-- <p>BRANCH/SITE :</p> -->
                        <p>BANK NAME :</p>  
                        <p>PAN :</p>  
                        <p>PF Number :</p>  
                    </td>
                    <td style="padding: 0px 40px 0px 40px;">
                        <p><?php echo strtoupper($data->user->name); ?></p>
                        <p><?php echo $data->user->employee->joining_date; ?></p>
                        <p><?php echo $data->user->employee->designation; ?></p>
                        <!-- <p>RAUDRA TECHNOCRATS HO</p> -->
                        <p>
                            <?php
                            if (isset($data->user->employee_bank)) {
                                echo $data->user->employee_bank->bank_name;
                            } else {
                                echo 'NA';
                            }
                            ?>
                        </p>
                        <p>
                            <?php
                            if (isset($data->user->employee_bank)) {
                                echo $data->user->employee_bank->pancard_number;
                            } else {
                                echo 'NA';
                            }
                            ?>
                        </p>
                        <p>
                            <?php
                            if (isset($data->user->employee_bank)) {
                                echo $data->user->employee_bank->pf_number;
                            } else {
                                echo 'NA';
                            }
                            ?>
                        </p>
                    </td>
                    <td style="padding: 0px 40px 0px 40px;" >
                        <p>EMP CODE :</p>
                        <p>DEPARTMENT :</p>
                        <p>PAY MODE :</p>
                        <p>A/C NO :</p>
                        <p>Bank IFSC :</p>
                        <p>UAN :</p>
                    </td>
                    <td style="padding: 0px 40px 0px 40px;">
                        <p><?php echo $data->user->employee->emp_code; ?></p>
                        <p><?php echo $data->user->employee->department->dept_name; ?></p>
                        <p>bank</p>
                        <p>
                            <?php
                            if (isset($data->user->employee_bank)) {
                                echo $data->user->employee_bank->account_number;
                            } else {
                                echo 'NA';
                            }
                            ?>
                        </p>
                        <p>
                            <?php
                            if (isset($data->user->employee_bank)) {
                                echo $data->user->employee_bank->ifsc_code;
                            } else {
                                echo 'NA';
                            }
                            ?>
                        </p>                        
                        <p>
                            <?php
                            if (isset($data->user->employee_bank->UAN)) {
                                echo $data->user->employee_bank->UAN;
                            } else {
                                echo 'NA';
                            }
                            ?>
                        </p>
                    </td>
                </tr>
            </table>
            <hr style="border: 1px solid;" />
            <table cellpadding="0" cellspacing="0" style="text-align: left; border: 0; width: 100%;">
                <tr>
                    <td style="padding: 0px 40px 0px 40px;">
                        <p>Total Month days : <?php echo $data->total_month_days; ?></p>  
                    </td>
                    <td style="padding: 0px 40px 0px 40px;">
                        <p>Total Working Days : <?php echo $data->working_day; ?></p>
                    </td>
                    <td style="padding: 0px 40px 0px 40px;" >
                        <p>Total Leaves : <?php echo $data->total_leave; ?></p>
                    </td>
                    <td style="padding: 0px 40px 0px 40px;">
                        <p>Total Sandwich Leave : <?php echo ($data->total_sandwich_leave != "")? $data->total_sandwich_leave  : "0.0"; ?></p>
                    </td>
<!--                     <td style="padding: 0px 40px 0px 40px;">
                        <p>lop days : 0.0</p>
                    </td> -->
                </tr>
            </table>
            <hr style="border: 1px solid; margin-bottom: 0;" />
            
            <table cellpadding="0" cellspacing="0" style="text-align: left; border: 0; width: 100%;">
                <tr style="text-align:center">
                    <th style="padding: 18px 40px 18px 40px; border-right: 2px solid;">earning</th>
                    <th style="padding: 18px 40px 18px 40px; border-right: 2px solid;">Amount</th>
                    <th style="padding: 18px 40px 18px 40px; text-align:center">Deductions</th>
                </tr>
                <tr>
                    <td style="padding: 16px 40px 0px 40px; border-right: 2px solid; border-top: 2px solid;">Basic</td>
                    <td style="padding: 16px 40px 0px 40px; border-right: 2px solid; border-top: 2px solid;"><?php echo $data->basic_salary; ?></td>
                    <td style="padding: 16px 40px 0px 40px; border-top: 2px solid;">Provident Fund</td>
                    <td style="padding: 16px 40px 0px 40px; border-top: 2px solid;"><?php echo $data->pf; ?></td>
                </tr>
                <tr style="padding: 0px 40px 0px 40px; border-right: 2px solid;" >
                    <td style="padding: 16px 40px 0px 40px; border-right: 2px solid;">HRA</td>
                    <td style="padding: 16px 40px 0px 40px; border-right: 2px solid;"><?php echo $data->hra; ?></td>
                    <td style="padding: 16px 40px 0px 40px;">Profession Tax</td>
                    <td style="padding: 16px 40px 0px 40px;"><?php echo $data->professional_tax; ?></td>
                </tr> 
                <tr>
                    <td style="padding: 16px 40px 0px 40px; border-right: 2px solid;">Other allowance</td>
                    <td style="padding: 16px 40px 0px 40px; border-right: 2px solid;"><?php echo $data->others; ?></td>
                    <td style="padding: 16px 40px 0px 40px;">Loan</td>
                    <td style="padding: 16px 40px 0px 40px;"><?php echo $data->loan_installment ?></td>
                </tr>

                <tr>
                    <td style="padding: 16px 40px 16px 40px; border-right: 2px solid;">FOOD ALLOWANCE</td>
                    <td style="padding: 0px 40px 0px 40px; border-right: 2px solid;"><?php echo $data->food;?></td>
                    <td style="padding: 0px 40px 0px 40px;">Penalty
                    </td>
                    <td style="padding: 0px 40px 0px 40px;"><?php echo $data->penalty; ?></td>
                </tr>
                
                <?php
                if($data->manual_penalty > 0){
                ?>
                <tr>
                    <td style="padding: 16px 40px 16px 40px; border-right: 2px solid;"></td>
                    <td style="padding: 0px 40px 0px 40px; border-right: 2px solid;"></td>
                    <td style="padding: 0px 40px 0px 40px;"><?php 
                    if($data->penalty_note!=""){
                    echo $data->penalty_note; 
                    }
                    else{
                        echo "Manual Penalty"; 
                    }
                    ?>
                    </td>
                    <td style="padding: 0px 40px 0px 40px;"><?php echo $data->manual_penalty; 
                    ?>
                </tr>
                <?php } ?>
                <tr>
                    <td style="padding: 16px 40px 16px 40px; border-right: 2px solid;"></td>
                    <td style="padding: 0px 40px 0px 40px; border-right: 2px solid;"></td>
                    <td style="padding: 0px 40px 0px 40px;">Extra Loan
                    </td>
                    <td style="padding: 0px 40px 0px 40px;"><?php echo $data->extra_loan_amount; ?></td>
                </tr>

                <tr>
                    <th style="padding: 16px 40px 16px 40px; border-right: 2px solid; border-top: 2px solid;">Total earning</th>
                    <?php $total_earning = round(($data->basic_salary + $data->hra + $data->others + $data->food), 2); ?>
                    <th style="padding: 0px 40px 0px 40px; border-right: 2px solid; border-top: 2px solid;"><?php echo $total_earning; ?></th>
                    <th style="padding: 0px 40px 0px 40px; border-top: 2px solid;">Total Deductions</th>
                    <?php $main_total_dedu = round(($data->pf + $data->professional_tax + $data->loan_installment + $data->penalty + $data->manual_penalty + $data->extra_loan_amount), 2); ?>
                    <th style="padding: 0px 40px 0px 40px; border-top: 2px solid;"><?php echo $main_total_dedu; ?></th>
                </tr>                
                
                <tr>
                    <th style="padding: 0px 40px 0px 40px; border-top: 2px solid;"></th>
                    <th style="padding: 0px 40px 0px 40px; border-right: 2px solid; border-top: 2px solid;"></th>
                    <th style="padding: 0px 40px 0px 40px; border-top: 2px solid;">Net Salary</th>
                    <th style="padding: 0px 40px 0px 40px; border-top: 2px solid;"><?php echo $data->payable_salary;?></th>
                </tr>
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
                <tr>
                    <th style="padding: 16px 40px 16px 20px; border-top: 2px solid; text-transform: capitalize">Rs. <?php echo ucfirst($result) . "Rupees  " . ucfirst($points) . " Paise"; ?></th>
                    <th style="padding: 16px 40px 0px 40px; border-top: 2px solid;"></th>
                    <th style="padding: 16px 40px 0px 40px; border-top: 2px solid;"></th>
                    <th style="padding: 16px 40px 0px 40px; border-top: 2px solid;"></th>
                    
                </tr>
                <tr>
                    <th style="padding: 16px 40px 16px 20px; border-top: 2px solid; text-transform: capitalize">This is computer generated statement, does not required signature.</th>
                    <th style="padding: 16px 40px 0px 40px; border-top: 2px solid;"></th>
                    <th style="padding: 16px 40px 0px 40px; border-top: 2px solid;"></th>
                    <th style="padding: 16px 40px 0px 40px; border-top: 2px solid;"></th>
                    
                </tr>
              </table>

        </td>
    </tr>
</table>
</body>
</html>