@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                {{-- <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li> --}}
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row" >
        <div class="col-md-12">
            <div class="white-box">
                <div class="row" id="print_area">
                  <h3><b>INVOICE</b> <span class="pull-right">#{{$bill_number_unique}}</span></h3>
                  <hr>
                  <div class="col-md-12">
                    <div class="pull-left">
                      <address>
                      <h3>To,</h3>
                      <h4> &nbsp;<b class="text-danger">{{$company_detail->company_name}}</b></h4>
                      <p class="text-muted m-l-5">{{$project_detail->project_location}}</p>
                      <p class="m-t-30"><b>GST IN :</b>  {{$company_detail->gst_no}}</p>
                      <p class="m-t-30"><b>RUNNING BILL NO :</b>  {{$bill_number_unique}}</p>
                      </address>
                    </div>
                    <div class="pull-right text-right">
                      <address>
                      {{-- <h3>To,</h3>
                      <h4 class="font-bold">{{$company_detail->company_name}}</h4>
                      <p class="text-muted m-l-30">{{$project_detail->project_location}}</p> --}}
                      <p class="m-t-30"><b>Invoice Date :</b> <i class="fa fa-calendar"></i> {{date('d M Y',strtotime($site_manage_bill[0]['created_at']))}}</p>
                      </address>
                    </div>
                  </div>
                  <div class="col-md-12">
                    {{-- table-responsive m-t-40 --}}
                    <div class="">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th class="text-center">SR. NO.</th>
                            <th class="text-center">LOCATION</th>
                            <th class="text-center">HSN CODE</th>
                            <th class="text-center">QTY.</th>
                            <th class="text-center">RATE</th>
                            <th class="text-center">UNIT</th>
                            <th class="text-center">AMOUNT</th>
                          </tr>
                        </thead>
                        <tbody>
                          @php
                          $total_amount = 0;
                          @endphp
                          @foreach($site_manage_bill as $key => $value)
                          @php
                          $total_amount += $value['a_upto_date'];
                          @endphp
                          <tr>
                              <td class="text-center">{{$key + 1}}</td>
                              <td class="text-center">{{$project_detail->project_location}}</td>
                              <td class="text-center">2 </td>
                              <td class="text-center">{{$value['get_boq_detail']['quantity']}}</td>
                              <td class="text-center">{{-- ₹ --}} {{$value['get_boq_detail']['rate']}}</td>
                              <td class="text-center">{{$value['qe_upto_date']}}</td>
                              <td class="text-center">  {{$value['a_upto_date']}} </td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="pull-right m-t-30 text-right">
                      @php
                      $sgst_amount = $total_amount * 18 / 100 ;
                      $cgst_amount = $total_amount * 18 / 100 ;

                      $grand_amount = $total_amount + $sgst_amount + $cgst_amount;
                      @endphp
                      
                      <p><b>SGST 18% :</b>  {{$sgst_amount}}</p>
                      <p><b>CGST 18% :</b>  {{$cgst_amount}} </p>
                      <hr>
                      <h3><b>GRAND TOTAL :</b>   {{$grand_amount}}</h3>
                    </div>
                    <div class="clearfix"></div>
                    <div class="text-left">
                      @php
                      $number = $grand_amount;
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
                        '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
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
                                " " . $digits[$counter] . $plural . " " . $hundred
                                :
                                $words[floor($number / 10) * 10]
                                . " " . $words[$number % 10] . " "
                                . $digits[$counter] . $plural . " " . $hundred;
                         } else $str[] = null;
                      }
                      $str = array_reverse($str);
                      $result = implode('', $str);
                      $points = ($point) ?
                        "." . $words[$point / 10] . " " . 
                              $words[$point = $point % 10] : '';
                      // echo $result . "Rupees  " . $points . " Paise";

                      $word_str = $result . "Rupees  " . $points . " Paise";
                      @endphp
                      <address>
                      <p class="m-t-30"><b>AMOUNT IN WORDS : @php echo ucfirst($word_str); @endphp</b></p>
                      <p class="m-t-30"><b>GST NO :</b>  {{$company_detail->gst_no}}</p>
                      <p class="m-t-30"><b>PAN NO :</b>  {{$company_detail->pan_no}}</p>
                      <p class="m-t-30"><b>NOTE :</b> HERE PROVIDE OPTION FOR GST INCLUDING OF EXCULDING ON ABOVE METIONED AMOUNT.</p>           
                        <p>IF SELECT INCLUDING THEN OPEN FILL FOR THE DATA FILLED FOR GST PERCENTAGE AND AUTO CALCULATE ON AMOUNT.</p>           
                      <p>IF SELECT EXCLUDING THEN OPEN FILL FOR THE DATA FILLED FOR GST PERCENTAGE AND AUTO CALCULATE ON AMOUNT.           
                      </p>
                      </address>
                    </div>
                    <hr>
                    
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="text-right">
                      {{-- javascript:window.print(); --}}
                      <button class="btn btn-default btn-outline" id="print_button" type="button"> <span><i class="fa fa-print"></i> Print</span> </button>
                    </div>
                  </div>
                </div>
              </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
$('#print_button').click(function(){
        /*var divToPrint = document.getElementById("print_area");
        newWin = window.open("");
        newWin.document.write(divToPrint.outerHTML);
        newWin.print();
        newWin.close();*/

        var printContents = document.getElementById("print_area").innerHTML;
      var originalContents = document.body.innerHTML;

      document.body.innerHTML = printContents;

      window.print();

      document.body.innerHTML = originalContents;
    });
</script>
@endsection
