@extends('layouts.admin_app')

@section('content')
<style type="text/css">
    .set_button {
        margin-top: 65px;
    }
</style>
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
                {{-- <a href="{{ route('admin.add_tender') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Tender</a> --}}

                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="tender_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tender Sr No</th>
                                <th>Department</th>
                                <th>Tender Id Per Portal</th>
                                <th>Portal Name</th>
                                <th>Tender No</th>
                                <th>Name Of Work</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                {{-- <tr>
                                    <td>100024</td>
                                    <td>MD</td>
                                    <td>568</td>
                                    <td>XYZ</td>
                                    <td>xyz123</td>
                                    <td>Providing 15 mm. thick cement plaster</td>
                                    <td>
                                        <a href="{{url('edit_tender_opening_report')}}/3" class="btn btn-primary btn-rounded" title="View/Edit Details">
                                        <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
        <!-- The Modal -->
        <div class="modal" id="opening_date_time">
          <div class="modal-dialog ">
            <div class="modal-content">

              <!-- Modal Header -->
              <div class="modal-header">
                <h4 class="modal-title">Select Tender Opening Date/Time</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>

              <!-- Modal body -->
              <div class="modal-body">
                <div class="row">
                    <form method="post" id="preliminary_form">
                        @csrf
                        <div class="col-md-6">
                        <p><strong>Opening Status of Preliminary</strong></p>
                          <div class="form-group">
                            <label class="control-label">DateTime</label>
                            <input type="text" name="opening_status_preliminary_datetime" id="opening_status_preliminary_datetime" class="form-control required">
                            <input type="hidden" name="opening_status_preliminary_datetime_id" id="opening_status_preliminary_datetime_id">
                            <input type="hidden" name="form_name" value="preliminary">
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-2 set_button">
                          <div class="form-group">
                            <button type="button" class="btn btn-primary" id="preliminary_form_btn">Save</button>
                          </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <form method="post" id="technical_form">
                        @csrf
                        <div class="col-md-6">
                        <p><strong>Opening Status of Technical</strong></p>
                          <div class="form-group">
                            <label class="control-label">DateTime</label>
                            <input type="text" name="opening_status_technical_datetime" id="opening_status_technical_datetime" class="form-control required">
                            <input type="hidden" name="opening_status_technical_datetime_id" id="opening_status_technical_datetime_id">
                            <input type="hidden" name="form_name" value="technical">
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-2 set_button">
                          <div class="form-group">
                            <button type="button" class="btn btn-primary" id="technical_form_btn">Save</button>
                          </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <form method="post" id="financial_form">
                        @csrf
                        <div class="col-md-6">
                        <p><strong>Opening Status of Financial</strong></p>
                          <div class="form-group">
                            <label class="control-label">DateTime</label>
                            <input type="text" name="opening_status_financial_datetime" id="opening_status_financial_datetime" class="form-control required">
                            <input type="hidden" name="opening_status_financial_datetime_id" id="opening_status_financial_datetime_id">
                            <input type="hidden" name="form_name" value="financial">
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-2 set_button">
                          <div class="form-group">
                            <button type="button" class="btn btn-primary" id="financial_form_btn">Save</button>
                          </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <form method="post" id="commercial_form">
                        @csrf
                        <div class="col-md-6">
                        <p><strong>Opening Status of Commercial</strong></p>
                          <div class="form-group">
                            <label class="control-label">DateTime</label>
                            <input type="text" name="opening_status_commercial_datetime" id="opening_status_commercial_datetime" class="form-control required">
                            <input type="hidden" name="opening_status_commercial_datetime_id" id="opening_status_commercial_datetime_id">
                            <input type="hidden" name="form_name" value="commercial">
                          </div>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-2 set_button">
                          <div class="form-group">
                            <button type="button" class="btn btn-primary" id="commercial_form_btn">Save</button>
                          </div>
                        </div>
                    </form>
                </div>
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" integrity="sha256-yMjaV542P+q1RnH6XByCPDfUFhmOafWbeLPmqKh11zo=" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function () {
                var table = $('#tender_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "order": [[0, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_opening_tender_list'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {"taregts": 1, 'data': 'tender_sr_no'},
                        {"taregts": 2, 'data': 'dept_name'},
                        {"taregts": 3, 'data': 'tender_id_per_portal'},
                        {"taregts": 4, 'data': 'portal_name'},
                        {"taregts": 5, 'data': 'tender_no'},
                        {"taregts": 6, 'data': 'name_of_work'},
                        {"taregts": 7, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out="";
                                out = '<a href="<?php echo url('edit_tender_opening_report') ?>'+'/'+id+'" class="btn btn-primary btn-rounded" title="View/Edit Tender"><i class="fa fa-eye"></i></a>';
                                out += '&nbsp;&nbsp;<button type="button" class="btn btn-success btn-rounded" title="Opening Date/Time" id="'+id+'" onclick="addEditDateTime(this.id)"><i class="fa fa-calendar-check-o"></i></button>'
                                return out;
                            }
                        },
                    ]

                });
            })
        function delete_confirm(e) {
            swal({
                title: "Are you sure you want to delete tender ?",
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
        function addEditDateTime(id){

            $.ajax({
                type : "POST",
                url : "{{url('get_opening_date')}}",
                data : {
                    "id" : id,
                    "_token" : "{{csrf_token()}}"
                },
                success :function(data){
                    data = JSON.parse(data);
                    console.log(data);

                    $("#preliminary_form").validate().resetForm();
                    $("#technical_form").validate().resetForm();
                    $("#financial_form").validate().resetForm();
                    $("#commercial_form").validate().resetForm();

                    $("#opening_status_preliminary_datetime_id").val(id);
                    $("#opening_status_technical_datetime_id").val(id);
                    $("#opening_status_financial_datetime_id").val(id);
                    $("#opening_status_commercial_datetime_id").val(id);

                    $("#opening_status_preliminary_datetime").val(data.opening_status_preliminary_datetime);
                    $("#opening_status_technical_datetime").val(data.opening_status_technical_datetime);
                    $("#opening_status_financial_datetime").val(data.opening_status_financial_datetime);
                    $("#opening_status_commercial_datetime").val(data.opening_status_commercial_datetime);

                    $("#opening_date_time").modal('show');
                }
            });
        }
        jQuery('#opening_status_preliminary_datetime,#opening_status_technical_datetime,#opening_status_financial_datetime,#opening_status_commercial_datetime').datetimepicker({
                format:'DD-MM-YYYY h:mm a',
          });
        //preliminary
        $("#preliminary_form_btn").on('click',function(){
            if($("#preliminary_form").valid()){
                var form = $("#preliminary_form").serialize();
                $.ajax({
                    type : "POST",
                    url : "{{url('save_opening_datetime')}}",
                    data : form,
                    success : function(data){
                        // console.log(data);
                        // $("#opening_date_time").modal('hide');
                        $.toast({
                          heading: 'Opening DateTime',
                          text: "DateTime save successfully",
                          position: 'top-right',
                          loaderBg:'#ff6849',
                          icon: 'success',
                          hideAfter: 3500,
                          stack: 6
                        });
                    },
                    error : function(data){
                        $.toast({
                          heading: "Opening DateTime",
                          text: 'DateTime not save try again !!',
                          position: 'top-right',
                          loaderBg:'#ff6849',
                          icon: 'error',
                          hideAfter: 3500
                        });
                    }
                })
            }
        });

        //technical
        $("#technical_form_btn").on('click',function(){
            if($("#technical_form").valid()){
                var form = $("#technical_form").serialize();
                $.ajax({
                    type : "POST",
                    url : "{{url('save_opening_datetime')}}",
                    data : form,
                    success : function(data){
                        // console.log(data);
                        // $("#opening_date_time").modal('hide');
                        $.toast({
                          heading: 'Opening DateTime',
                          text: "DateTime save successfully",
                          position: 'top-right',
                          loaderBg:'#ff6849',
                          icon: 'success',
                          hideAfter: 3500,
                          stack: 6
                        });
                    },
                    error : function(data){
                        $.toast({
                          heading: "Opening DateTime",
                          text: 'DateTime not save try again !!',
                          position: 'top-right',
                          loaderBg:'#ff6849',
                          icon: 'error',
                          hideAfter: 3500
                        });
                    }
                })
            }
        });

        //financial
        $("#financial_form_btn").on('click',function(){
            if($("#financial_form").valid()){
                var form = $("#financial_form").serialize();
                $.ajax({
                    type : "POST",
                    url : "{{url('save_opening_datetime')}}",
                    data : form,
                    success : function(data){
                        // console.log(data);
                        // $("#opening_date_time").modal('hide');
                        $.toast({
                          heading: 'Opening DateTime',
                          text: "DateTime save successfully",
                          position: 'top-right',
                          loaderBg:'#ff6849',
                          icon: 'success',
                          hideAfter: 3500,
                          stack: 6
                        });
                    },
                    error : function(data){
                        $.toast({
                          heading: "Opening DateTime",
                          text: 'DateTime not save try again !!',
                          position: 'top-right',
                          loaderBg:'#ff6849',
                          icon: 'error',
                          hideAfter: 3500
                        });
                    }
                })
            }
        });

        //commercial
        $("#commercial_form_btn").on('click',function(){
            if($("#commercial_form").valid()){
                var form = $("#commercial_form").serialize();
                $.ajax({
                    type : "POST",
                    url : "{{url('save_opening_datetime')}}",
                    data : form,
                    success : function(data){
                        // console.log(data);
                        // $("#opening_date_time").modal('hide');
                        $.toast({
                          heading: 'Opening DateTime',
                          text: "DateTime save successfully",
                          position: 'top-right',
                          loaderBg:'#ff6849',
                          icon: 'success',
                          hideAfter: 3500,
                          stack: 6
                        });
                    },
                    error : function(data){
                        $.toast({
                          heading: "Opening DateTime",
                          text: 'DateTime not save try again !!',
                          position: 'top-right',
                          loaderBg:'#ff6849',
                          icon: 'error',
                          hideAfter: 3500
                        });
                    }
                })
            }
        });
        </script>
        @endsection
