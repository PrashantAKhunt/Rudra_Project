@extends('layouts.admin_app')

@section('content')
<style>
    table{
            border-collapse: unset !important;
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            
                @if (session('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('error') }}
                </div>
                @endif
                @if (session('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('success') }}
                </div>
                @endif 
            
        </div>
        <div class="col-md-12">
            <div class="white-box">
                <h3 class="box-title">{{ $page_title }}</h3>
				
                <div id="chart_div" style="overflow-x: auto;"></div>
            </div>
        </div>

    </div>

    @endsection
    @section('script')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta3/html2canvas.js"></script>
    <script>
	function convertCanvasToImage(canvas) 
{
    var image = new Image();
    image.src = canvas.toDataURL("image/png");
    return image;
}
	function printImg() {
        html2canvas($('#chart_div').get(0)).then( function (canvas) {
            var image = convertCanvasToImage(canvas);
			window.open(image.src);
			console.log(image.src);
            /*var htmlToPrint = image.outerHTML ;
            newWin = window.open("");
            newWin.document.write(htmlToPrint);
            newWin.print();
            newWin.close();*/
        });
    }
    google.charts.load('current', {packages:["orgchart"]});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');
        data.addColumn('string', 'ToolTip');

        // For each orgchart box, provide the name, manager, and tooltip to show.
        data.addRows([
            @foreach($employee_list as $key=>$emp)
            
            @if($emp["reporting_user_id"]==0)
          [{'v':'{{ $emp["user_id"] }}', 'f':'{{ $emp["name"] }}<br><img width="100px" height="100px" src="{{ $emp->profile_image }}" alt="user-img" class="img-circle"><div style="color:red; font-style:italic">{{ $emp["designation"] }}</div>'},
           '', '{{ $emp["designation"] }}'],
           @else
               [{'v':'{{ $emp["user_id"] }}', 'f':'{{ $emp["name"] }}<br><img width="100px" height="100px" src="{{ $emp->profile_image }}" alt="user-img" class="img-circle"><div style="color:red; font-style:italic">{{ $emp["designation"] }}</div>'},
           '{{ $emp["reporting_user_id"] }}', '{{ $emp["designation"] }}'],
               @endif
          @endforeach
        ]);

        // Create the chart.
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        // Draw the chart, setting the allowHtml option to true for the tooltips.
        
		
		chart.draw(data, {'allowHtml':true,size:"large"});
        chart.setSelection([{row: '{{ $selected_row }}'}]);
		
      }
    </script>

    @endsection

