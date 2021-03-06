


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
                <li><a href="{{ route('admin.inward_outward') }}">{{ $module_title }}</a></li>
                <li><a href="{{ route('admin.inwards') }}">{{ $page_title }}</a></li>
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
                @if($full_view_permission && $add_permission && Auth::user()->role!=config('constants.SuperUser'))
                <a href="{{ route('admin.add_inward') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Inward</a>
                @endif

                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="150px">Title</th>
                                <th width="200px">Description</th>                              
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Received Date</th>
                                <th id="expected_date">Expected Ans Date</th>
                                <th data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inward_list as $list_data)
                            <tr>
                                <td>{{$list_data->inward_outward_title}}</td>
                                <td>{{$list_data->description}}</td>

                                <td>{{$list_data->category_name}}</td>
                                <td>{{$list_data->sub_category_name}}</td>
                                <td ><span style="display: none;">{{ $list_data->received_date }}</span>
                                    {{ Carbon\Carbon::parse($list_data->received_date)->format('d-m-Y') }} </td>
                                @if($list_data->expected_ans_date!="" || $list_data->expected_ans_date!=NULL)
                                <td ><span style="display: none;">{{ $list_data->expected_ans_date }}</span>{{ Carbon\Carbon::parse($list_data->expected_ans_date)->format('d-m-Y') }} </td>
                                @else
                                <td>NA</td>
                                @endif
                                <td>

                                    <a href="{{ route('admin.view_inward_to_outward',['id'=>$list_data->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('admin.registry_chat',['id'=>$list_data->id]) }}" class="btn btn-rounded btn-success"><i class="fa fa-comments-o"></i></a>
                        <!--       <a href="#" title="Edit" id="edit_btn" onclick="delete_confirm(this);" data-href="delete_document/{{$list_data->id}}" class="btn btn-danger btn-rounded"> <i class="fa fa-trash"></i></a>
                                    -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            <!--row -->

        </div>

        @endsection
        @section('script')

        <script>
            $(document).ready(function () {
                $('#user_table').DataTable({"order": [[4, "DESC"]], });
                $(document).ready(function () {


                    // Order by the grouping
                    /*$('#example tbody').on('click', 'tr.group', function () {
                     var currentOrder = table.order()[0];
                     if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                     table.order([2, 'desc']).draw();
                     } else {
                     table.order([2, 'asc']).draw();
                     }
                     });*/
                });
            });

        </script>
             <!-- <script>
                  $(document).ready(function(inward_list) {
      
      
                      $("#expected_date").append(moment(inward_list.expected_ans_date).format("DD-MM-YYYY"));
      
                  });
              </script>-->
        <script>
            function delete_confirm(e) {
                swal({
                    title: "Are you sure you want to delete document ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    window.location.href = $(e).attr('data-href');
                });
            }
        </script>
        @endsection

