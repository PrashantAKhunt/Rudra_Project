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
                <li><a href="{{ route($module_link) }}">Job Openings</a></li>
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
            <div class="white-box">
            <form action="{{ route('admin.multiple_candidate_approval') }}" id="multiple_candidate_frm" method="post">
                @csrf
                <input type="hidden" name="candidate_ids" id="candidate_ids"/>
                <input type="hidden" name="id" id="id" value="{{ $route_id }}"/>
                <input type="hidden" name="emp_status" id="emp_status" value=""/>
            </form>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    <table id="candidate_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                  #
                                </th>
                                <th>Candidate Name</th>
                                <th>Skills Parameters</th>
                                
                               <?php  for ($i = 0; $i < $round; ++$i) { ?>
                                    <th>Round {{$i + 1}}</th>
                               <?php  } ?>
                                
                                <th>Avarage</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                    @if( count($candidates_list) > 0)
                        @foreach($candidates_list as $value)
                            <tr>
                                <td>

                                @if( Auth::user()->role == config('constants.REAL_HR') && $value->hr_status == 'Pending')
                                    <input type="checkbox" value="{{ $value->id }}" name="candidate_list" />
                                @endif

                                @if( Auth::user()->role == config('constants.SuperUser') && $value->hr_status == 'Selected' && $value->superUser_status == 'Pending')
                                    <input type="checkbox" value="{{ $value->id }}" name="candidate_list" />
                                @endif
                                
                                </td>
                                <td>{{ $value->name }}</td>
                                <td>
                               
                                    <?php 
                                    foreach ($value->interview_result as $index => $list) {
                                    if(!empty($list->technical_skill)){
                                        $technical = unserialize($list->technical_skill);
                                         foreach ($technical['skill'] as $key => $val) { ?>
                                
                                    <label>{{ $val }} : </label> {{ $technical['comment'][$key] }}
                                
                                     <?php } 
                                    
                                    } }?>
                               
                                </td>

                                
                                <?php $k = count($value->interview_result); ?> 
                                
                                @if ($k == $round)
                                    <?php foreach ($value->interview_result as $k => $list) { ?>
                                    
                                    <td> {{ $list->round_average }}  </td>
                               
                                    <?php  } ?>
                                @else
                                    <?php foreach ($value->interview_result as $k => $list) { ?>
                                    
                                    <td> {{ $list->round_average }}  </td>
                               
                                    <?php  } ?>

                                    <?php $z = $round - $k;  ?>
                                    
                                    <?php  for ($count = 1; $count < $z; $count++) { ?>
                                        <td> </td>
                                   <?php  } ?>

                                @endif


                                <td>{{ $value->totalAverage }}</td>
        
                               
                            </tr>
                        @endforeach 
                        @endif
                        </tbody>
                    </table>
                    <br>
                    @if( count($candidates_list) > 0)
                    @if( Auth::user()->role == config('constants.SuperUser') || Auth::user()->role == config('constants.REAL_HR') )
                    <button type="submit" class="btn btn-success" onclick="onAction('selected');">Select Candidates</button>
                    <button type="submit" class="btn btn-danger" onclick="onAction('rejected');">Reject Candidates</button>
                    @endif
                    @endif
                    <div class="clearfix"></div>
                    <br>
                </div>
            </div>
            <!--row -->

        </div>
        
        <!--  -->
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                $('#candidate_table').DataTable({
                });

            });
            
                function onAction(status) {
        
                    $('#emp_status').val('');
                    var favorite = [];
                    
                    $.each($("input[name='candidate_list']:checked"), function(){
                        favorite.push($(this).val());
                    });
                    var id = favorite.join(",");
                    if(id.length==0) {
                        swal({
                            title: "Please select first !",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Okay",
                            closeOnConfirm: true
                        });
                    }
                    else {

                        $('#emp_status').val(status);
                        swal({
                            title: "Are you sure ?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            closeOnConfirm: false
                        }, function () {
                           $("#candidate_ids").val(id);
                           $("#multiple_candidate_frm").submit();
                        });
                    }
                };
       
        </script>
        @endsection