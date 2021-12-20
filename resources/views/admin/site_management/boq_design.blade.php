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
                <div class="row">
                    <form method="get" id="search_frm" action="{{ route('admin.boq_design') }}">
                        <div class="col-md-3">
                            <select class="form-control" id="company_id" name="company_id">
                                <option value="">Select Company </option>
                                @foreach($company_list as $company)
                                <option @if($company_id==$company->id) selected="" @endif value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="project_id" name="project_id">
                                <option value="">Select Project</option>
                                @foreach($project_list as $project)
                                <option @if($project_id==$project->id) selected="" @endif value="{{ $project->id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" name="item_no" id="item_no">
                                <option value="">Select Item</option>
                                {{-- @if($boq_item)
                                    @foreach($boq_item as $key => $value)
                                        <option value="{{$value['id']}}">{{$value['item_no']}}</option>
                                    @endforeach
                                @endif --}}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success">Go</button>
                        </div>
                    </form>
                </div>
                <br>
                <a href="{{ route('admin.add_boq_design') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add BOQ Design</a>
                <p class="text-muted m-b-30"></p>
                <br>
                
                <div class="table-responsive">
                    @if($item_no)
                        <table id="boq_table_search" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Item No</th>
                                <th>Block Title</th>
                                <th>Block Detail</th>
                                <th>Block Drawing</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item_list as $key => $value)
                                <tr>
                                    <td><span style="cursor: pointer;" title="{{$value['get_boq_item']['item_description']}}">{{$value['get_boq_item']['item_no']}}</span></td>
                                    <td>{{$value['block_title']}}</td>
                                    <td>{{$value['block_detail']}}</td>
                                    <td><a href="<?php echo url('boq_design_drawing') ?>/{{$value['id']}}" class="btn btn-primary btn-rounded" target="_blank"><i class="fa fa-download"></i></a></td>
                                    <td>
                                        <a href="<?php echo url('update_boq_design') ?>/{{$value['id']}}" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <table id="boq_table_default" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Company</th>
                                <th>Project</th>
                                <th>Item No</th>
                                <th>Block Title</th>
                                <th>Block Detail</th>
                                <th>Block Drawing</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            <!--row -->

        </div>

        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                if("{{$item_no}}" != ""){
                    $("#project_id").trigger('change');
                }
                // $("#item_no").trigger('change');
                var table = $('#boq_table_default').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "order": [[4, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_boq_design_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'company_name'
                        },
                        {"taregts": 1, 'data': 'project_name'
                        },
                        {"taregts": 2, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 

                                out = '<span style="cursor: pointer;" title="'+row.item_description+'">'+row.item_no+'</span>'; 
                                return out;
                            }
                        },
                        {"taregts": 3, 'data': 'block_title'
                        },
                        {"taregts": 4, 'data': 'block_detail'
                        },
                        {"taregts": 5, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 

                                out = '<a href="<?php echo url('boq_design_drawing') ?>'+'/'+id+'" class="btn btn-primary btn-rounded" target="_blank"><i class="fa fa-download"></i></a>'; 
                                return out;
                            }
                        },
                        {"taregts": 6, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out=""; 
                                out = '<a href="<?php echo url('update_boq_design') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>'; 
                                /*out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_tender_category'); ?>/' + id + '\'\n\
                                title="Delete"><i class="fa fa-trash"></i></a>';*/
                                return out;
                            }
                        },
                    ]

                });

                $("#boq_table_search").DataTable();

                $('#search_frm').validate({
                    rules: {
                        company_id: {
                            required: true
                        },
                        project_id: {
                            required: true
                        },
                        item_no: {
                            required: true
                        },
                    }
                })
            })
            $("#company_id").on('change',function(){
                $.ajax({
                    url: "{{ route('admin.get_projectlist_by_company') }}",
                    type: "POST",
                    dataType: "html",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        company_id: $('#company_id').val()
                    },
                    success: function (data) {
                        $("#item_no").val("");
                        $('#project_id').html(data);
                    }
                });
            });

            $("#project_id").on('change',function(){
                var company_id = $("#company_id").val();
                var project_id = $(this).val();
                $.ajax({
                    type : "POST",
                    url : "{{url('get_itemno_block')}}",
                    data : {
                        '_token' : "{{csrf_token()}}",
                        'company_id' : company_id,
                        'project_id' : project_id
                    },
                    success : function(data){
                        // console.log(data);
                        $("#item_no").html(data);
                        if("{{$item_no}}" != ""){
                            $("#item_no").val("{{$item_no}}");
                        }
                    }
                });
            });
        </script>
        @endsection