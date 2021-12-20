@extends('layouts.app')

@section('content')
<style>
    #chartdiv {
        width: 100%;
        height: 500px;
    }
    .modal.fade .modal-dialog{transform:none !important;}

</style>

<div class="dash-content-wrap chiller-theme wrapper">
    <!-- sidebar-search  -->
    @include('../layouts/sidebar')
    <div class="dast-mainwrap" id="content"> 
        <div class="registration-wrap text-center">
            <div class="card">
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
                <div class="card-header d-flex">
                    <h3 class="title">Advertisement Statistics</h3>
                </div>
                <div class="card-body">
                    <form method="post" id="filter_frm" action="{{ route('dashboard') }}">
                        @csrf
                        <div class="col-md-12 row">
                            <div class="col-md-4">
                                <input class="form-control" value="" readonly="" placeholder="Select Date Range" name="filter_date" id="filter_date"  />
                            </div>
                            <div class="col-md-4">
                                <select  id="ad_id" name="ad_id" class="form-control">
                                    @foreach($all_ads as $ads)
                                    <option @if($selected_ad_id==$ads->id) selected @endif value="{{ $ads->id }}">{{ $ads->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success">Search</button>
                            </div>
                        </div>
                    </form>
                    @if(!empty($ad_list))
                    <div id="chartdiv"></div>

                    @else
                    <h3>You do not have any running advertisement</h3>
                    @endif
                </div>
            </div>
            
            <div class="card">
            <div class="card-header d-flex">
                <h3 class="title">My Advertisement</h3>
            </div>
            <div class="card-body">
                <table id="ad_table" class="table table-bordered video-list">
                    <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Video</th>
                            <th scope="col">Total Budget</th>
                            <th scope="col">Price per minute</th>
                            <th scope="col">Total Views</th>
                            <th scope="col">Budget Used</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($my_ads as $ad)
                        <tr>
                            <td>{{ $ad->title }}</td>
                            <td><a href="#" onclick="video_open('{{ asset('storage/' . str_replace('public/', '', $ad->ad_file)) }}')" data-toggle="modal" data-target="#video_modal" title="view">
                                    <img src="{{asset('assets/images/video-icon.jpg') }}" alt="{{ $ad->title }}">
                                </a>
                            </td>
                            <td>{{ $ad->total_amt }}</td>
                            <td>{{ $ad->per_minute_amt }}</td>
                            <td>{{ $ad->views }}</td>
                            <td>{{ $ad->used_amt }}</td>
                            <td @if($ad->status=='Enabled') class="text-success" @else class="text-danger" @endif >{{ $ad->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
            
        </div>
        
    </div>

</div>
<div id="video_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        
        <h4 class="modal-title">View Video</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <iframe allowfullscreen="true" frameborder="0" id="video_src" src="" width="100%" height="350px" ></iframe>
      </div>
      
    </div>

  </div>
</div>

@endsection

@section('custom_script')


<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

<script>
    
    function video_open(path){
        $('#video_src').attr('src','');
        setTimeout(function(){
            
            $('#video_src').attr('src',path+'?autoplay=1');
           
        },3000);
        
        
    }
    
jQuery(document).ready(function () {
    
    $('#video_modal').on('hidden.bs.modal', function () {
    $('#video_src').attr('src','');
})
    
    $('#ad_table').dataTable();
    
    $('#filter_date').daterangepicker({
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-danger',
        cancelClass: 'btn-inverse',
        locale: {
            format: 'YYYY/MM/DD'
        },
    });

    $('#filter_date').val("{{ $display_start_date.' - '.$display_end_date }}");
    am4core.ready(function () {

// Themes begin
        am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
        var chart = am4core.create("chartdiv", am4charts.XYChart);
// Increase contrast by taking evey second color
        chart.colors.step = 2;
// Add data
        chart.data = generateChartData();
// Create axes
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.minGridDistance = 50;
// Create series
        function createAxisAndSeries(field, name, opposite, bullet) {
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            var series = chart.series.push(new am4charts.LineSeries());
            series.dataFields.valueY = field;
            series.dataFields.dateX = "date";
            series.strokeWidth = 2;
            series.yAxis = valueAxis;
            series.name = name;
            series.tooltipText = "{name}: [bold]{valueY}[/]";
            //series.tensionX = 0.8;

            var interfaceColors = new am4core.InterfaceColorSet();
            switch (bullet) {
                case "triangle":
                    var bullet = series.bullets.push(new am4charts.Bullet());
                    bullet.width = 12;
                    bullet.height = 12;
                    bullet.horizontalCenter = "middle";
                    bullet.verticalCenter = "middle";
                    var triangle = bullet.createChild(am4core.Triangle);
                    triangle.stroke = interfaceColors.getFor("background");
                    triangle.strokeWidth = 2;
                    triangle.direction = "top";
                    triangle.width = 12;
                    triangle.height = 12;
                    break;
                case "rectangle":
                    var bullet = series.bullets.push(new am4charts.Bullet());
                    bullet.width = 10;
                    bullet.height = 10;
                    bullet.horizontalCenter = "middle";
                    bullet.verticalCenter = "middle";
                    var rectangle = bullet.createChild(am4core.Rectangle);
                    rectangle.stroke = interfaceColors.getFor("background");
                    rectangle.strokeWidth = 2;
                    rectangle.width = 10;
                    rectangle.height = 10;
                    break;
                default:
                    var bullet = series.bullets.push(new am4charts.CircleBullet());
                    bullet.circle.stroke = interfaceColors.getFor("background");
                    bullet.circle.strokeWidth = 1;
                    break;
            }

            valueAxis.renderer.line.strokeOpacity = 1;
            valueAxis.renderer.line.strokeWidth = 2;
            valueAxis.renderer.line.stroke = series.stroke;
            valueAxis.renderer.labels.template.fill = series.stroke;
            valueAxis.renderer.opposite = opposite;
            valueAxis.renderer.grid.template.disabled = false;
            }
            @foreach($ad_list as $ad)
            createAxisAndSeries("{{ $ad->title }}", "{{ $ad->title }}", true, "circle");
            @endforeach
// Add legend
                    chart.legend = new am4charts.Legend();
// Add cursor
            chart.cursor = new am4charts.XYCursor();
// generate some random data, quite different range
            function generateChartData() {
                var chartData = [];
                var firstDate = new Date();
                firstDate.setDate(firstDate.getDate() - 100);
                firstDate.setHours(0, 0, 0, 0);
                console.log(firstDate);
                var views = 0;
                        @foreach($ad_view_detail as $ad_data)
                var fina_view_data = {
                    'date': "{{ date('Y-m-d',strtotime($ad_data['created_at'])) }}",
                };
                @if (!empty($ad_data['view_data']))
                        @foreach($ad_data['view_data'] as $ad_view)
                        fina_view_data["{{ $ad_view['title'] }}"] = {{ $ad_view['views'] }}

            @endforeach
                    @endif
                    chartData.push(fina_view_data);
            @endforeach

                    console.log(chartData);
            return chartData;
        }

    }); // end am4core.ready()
})

</script>
@endsection