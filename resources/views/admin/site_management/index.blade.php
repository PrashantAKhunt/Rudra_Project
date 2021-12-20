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
                    <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-success" id="add_bulk_boq_btn">Add Bulk BOQ</button>
                    <a href="{{ route('admin.add_boq') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add BOQ</a>
                    </div>
                </div>

                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <select onchange="get_project_by_company()" class="form-control" id="company_id" name="company_id">
                            <option value="">Select Company</option>
                            @foreach($company_list as $company)
                            <option @if($company_id==$company->id) selected="" @endif value="{{ $company->id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" onchange="get_boq_by_project()" id="project_id" name="project_id">
                            <option>Select Project</option>
                            @foreach($project_list as $project)
                            <option @if($project_id==$project->id) selected="" @endif value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="boq_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Item No</th>
                                <th>Item Description</th>
                                <th>UOM</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($boq_list as $key=>$boq)
                            <tr>
                                <td>{{ $boq->item_no }}</td>
                                <td>{{ $boq->item_description }}</td>
                                <td>{{ $boq->UOM }}</td>
                                <td>{{ $boq->quantity }}</td>
                                <td>{{ $boq->rate }}</td>
                                <td>{{ $boq->amount }}</td>
                                <td>
                                    <a href="{{ route('admin.edit_boq',['id'=>$boq->id]) }}" class="btn btn-primary btn-rounded" title="View/Edit Details">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
        <!-- Add Bulk BOQ -->
        <div class="modal" id="add_bulk_boq">
          <div class="modal-dialog">
            <div class="modal-content">

              <!-- Modal Header -->
              <div class="modal-header">
                <h4 class="modal-title">Add Bulk BOQ</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>

              <!-- Modal body -->
              <div class="modal-body">
                <form id="add_bulk_boq_form" enctype="multipart/form-data">
                @csrf
                  <div class="form-group">
                    <label for="inputAddress">Company <span class="error">*</span> </label>
                    <select  class="form-control" id="company_id_add" name="company_id_add">
                        <option value="">Select Company</option>
                        @foreach($company_list as $company)
                        <option @if($company_id==$company->id) selected="" @endif value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="inputAddress2">Project <span class="error">*</span> </label>
                    <select class="form-control" id="project_id_add" name="project_id_add">
                        <option value="">Select Project</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="inputAddress2">Your BOQ File <span class="error">*</span> </label>
                    <input type="file" class="form-control" name="boq_file" id="boq_file">
                  </div>
                  <button type="button" class="btn btn-primary add_bulk_boq_form_btn">Save</button>
                </form>

              </div>

              <!-- Modal footer -->
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              </div>

            </div>
          </div>
        </div>
        @endsection
        @section('script')
        <script>
            function get_project_by_company() {
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
                        $('#project_id').html(data);
                    }
                });
            }

            function get_boq_by_project() {

                var search_url = "{{url('site_management/')}}" + "/" + $('#company_id').val() + "/" + $('#project_id').val();

                window.location.replace(search_url);
            }
            $(document).ready(function () {
                var table = $('#boq_table').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        
                        'excelHtml5',
                        'csvHtml5',
                        
                    ],

                });
            })

            //Add Bulk BOQ
            $("#add_bulk_boq_btn").on('click',function(data){
                $("#add_bulk_boq").modal('show');
            });

            $("#company_id_add").on('change',function(){
                $.ajax({
                    url: "{{ route('admin.get_projectlist_by_company') }}",
                    type: "POST",
                    dataType: "html",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        company_id: $('#company_id_add').val()
                    },
                    success: function (data) {
                        $('#project_id_add').html(data);
                    }
                });
            });

            $(".add_bulk_boq_form_btn").on('click',function(){
                if($("#add_bulk_boq_form").valid()){
                    var form = $('#add_bulk_boq_form')[0];
                    var formData1 = new FormData(form);
                    $(".add_bulk_boq_form_btn").attr("disabled", true);
                    formData1.append('boq_file', $('#boq_file')[0].files[0]);
                    $.ajax({
                      type : "POST",
                      url : "{{url('add_bulk_boq')}}",
                      data : formData1,
                      processData: false,
                      contentType: false,
                      success : function(data){
                        // console.log(data);
                        data = JSON.parse(data);
                        $("#add_bulk_boq_form")[0].reset()
                        $(".add_bulk_boq_form_btn").attr("disabled", false);
                        $("#add_bulk_boq").modal('hide');
                        if(data.status == 'true'){
                            $.toast({
                              heading: 'File Upload Message',
                              text: data.message,
                              position: 'top-right',
                              loaderBg:'#ff6849',
                              icon: 'success',
                              hideAfter: 3500, 
                              stack: 6
                            });
                        }else{
                          $.toast({
                            heading: "File Upload Message",
                            text: data.message,
                            position: 'top-right',
                            loaderBg:'#ff6849',
                            icon: 'error',
                            hideAfter: 3500
                          });  
                        }
                      },
                      error :function(data){
                        $(".add_bulk_boq_form_btn").attr("disabled", false);
                        $.toast({
                          heading: "File Upload Message",
                          text: 'File not upload try again !!',
                          position: 'top-right',
                          loaderBg:'#ff6849',
                          icon: 'error',
                          hideAfter: 3500
                        });
                      }
                    });
                }
            })
            $("#add_bulk_boq_form").validate({
                rules : {
                    company_id_add : "required",
                    project_id_add : "required",
                    boq_file : {
                      required :true,
                      extension: "xls|xlsx"
                    }
                },
                messages : {
                    boq_file : {
                      extension: "Please select excel file"
                    }
                  }
            });
        </script>
        @endsection