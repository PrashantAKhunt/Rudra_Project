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
      <a href="{{ route('admin.add_meeting') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Meetig</a>
      <p class="text-muted m-b-30"></p>
      <br>
      <div class="table-responsive">
         <table id="company_table" class="table table-striped">
            <thead>
               <tr>
                  <th>Meeting Code</th>
                  <th>Meeting Categories</th>   <!-- 2 field recently added -->
                  <th>Meeting Subject</th>
                  <th>Meeting Details</th>
                  <th>Meeting Start Datetime</th>
                  <th>Meeting End Datetime</th>
                  <th>Actual Meeting Start Datetime</th>
                  <th>Actual Meeting End Datetime</th>
                  <th>Meeting closed</th>
                  <th>MOM</th>
                  <th>MOM User</th>
                  <th>MOM Upload (Pdf)</th>
                  <th>Attended persons</th>
                  <th>Attended Outsiders Persons Mails</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
            </tbody>
         </table>
      </div>
   </div>
   <!--row -->
   <div id="meetingModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
               <h4 class="modal-title" id="myModalLabel">Add MOM</h4>
            </div>
            <div class="modal-body" id="userTable">
               <form action="{{ route('admin.add_edit_meeting_mom') }}" id="add_edit_meeting_mom" enctype='multipart/form-data' method="post">
                  @csrf
                  <input type="hidden" name="meeting_id" id="meeting_id">
                  <input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->id}}">
                  <div class="form-group ">
                     <label>Meeting MOM</label>
                     <input type="file" name="meeting_mom_asset_file" required class="form-control" id="meeting_mom_asset_file"/>
                     <!-- <textarea class="form-control col-md-3" rows="10" required name="meeting_mom" id="meeting_mom" value=""></textarea> -->
                  </div>
                  <div class="clearfix"></div>
                  <br>
            </div>
            <div class="col-md-12 pull-left">
            </div>
            <div class="modal-footer">
            <button type="submit" id="mom_submit" class="btn btn-success">Submit</button>
            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
            </div>
            </form>
         </div>
         <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
   </div>
   <div id="momlistModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
               <h4 class="modal-title" id="myModalLabel">Guest list</h4>
            </div>
            <div class="modal-body" id="userTable">
               <table id="user_policyTable" class="table table-striped">
               </table>
            </div>
            <div class="col-md-12 pull-left">
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
            </div>
         </div>
         <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
   </div>
   <!--  -->
   <div id="closemeetingModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
               <h4 class="modal-title" id="myModalLabel">Close Meeting</h4>
            </div>
            <div class="modal-body" id="userTable">
               <form action="{{ route('admin.close_meeting') }}" id="close_meeting" method="post">
                  @csrf
                  <input type="hidden" name="close_meeting_id" id="close_meeting_id">
           
                  <div class="form-group ">
                     <label>Meeting Start Datetime</label>
                     <input type="text" class="form-control"  name="actual_meeting_start_datetime" id="actual_meeting_start_datetime" value="" /> 
                  </div>
                  <div class="form-group ">
                     <label>Meeting End Datetime</label>
                     <input type="text" class="form-control" name="actual_meeting_end_datetime" id="actual_meeting_end_datetime" value="" /> 
                  </div>
                  <div class="form-group"> 
                     <label>Attended Employee</label>
                     <select multiple required id="attend_user_id" name="attend_user_id[]">
                        @foreach($users as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                     </select> 
                  </div>
                  <div class="form-group">
                      <label>Type Email for outsiders Persons</label>
                      <input type="text" class="form-control" name="outsiders_email" data-role="tagsinput" />
                  </div>
                  <div class="clearfix"></div>
                  <!-- <br> -->
            </div>
            <div class="col-md-12 pull-left">
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-success">Submit</button>
            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
            </div>
            </form>
         </div>
         <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
   </div>
   
</div>
@endsection
@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.min.js" integrity="sha512-yYTn2YZ0M3CvakInlFU6213Jp+ugbJOPlLKS0gbRxVqmyE5JMBG0a5qR7WdsWUu1uhYzIicM/lBLAOXTWZ2MGA==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.css" integrity="sha512-Tk4TerSHF/muDM5gpZlr/57nPRIfaGVSa4ECBeVwGvJpyw03NxcOJGMVYz9mrU7XDoUoSBGY/Bje1FBkOi5RSg==" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.3/jquery.tagsinput.js" integrity="sha512-HMExw5ClDPsY50kNrUL5jblsfhvHHcaggPVVFfH7X12pn/bcxTnwh1tzqVrF+6TjNFEOAuIC/9z6rTwuAN7+lA==" crossorigin="anonymous"></script>

<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<script>
   $(document).ready(function () {
      // var regex4 = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;// Email address
      // $('#tags_emails').tagsInput({
      //    width: 'auto',
      //    pattern: regex4
      // });
      
      $('#attend_user_id').select2(); 
        $("#actual_meeting_start_datetime").datetimepicker({format: 'YYYY-MM-DD HH:mm:ss'});
        $("#actual_meeting_end_datetime").datetimepicker({format: 'YYYY-MM-DD HH:mm:ss'});
       var table = $('#company_table').DataTable({
           "processing": true,
           "serverSide": true,
           "responsive": true,
           "order": [[2, "DESC"]],
           "ajax": {
               url: "<?php echo route('admin.get_meeting_list'); ?>",
               type: "GET",
           },
           "columns": [
               {"taregts": 0, 'data': 'meeting_code'
               },
               {"taregts": 1, 'data': 'meeting_categories'
               },
               {"taregts": 2, 'data': 'meeting_subject'
               },
               {"taregts": 3, 'data': 'meeting_details'
               },
               {"taregts": 4, 'data': 'meeting_date_time'
               },
               {"taregts": 5, 'data': 'meeting_end_date_time'
               },
               {"taregts": 6, 'data': 'actual_meeting_start_datetime'
               },
               {"taregts": 7, 'data': 'actual_meeting_end_datetime'
               },
               {"taregts": 8, "render": function (data, type, row) {
                     if (row.is_close == 'Yes') {
                        return '<span class="text-danger">'+row.is_close+'</span>';
                     } else {
                        return '<span class="text-success">'+row.is_close+'</span>';
                     }
                                
                  }
               },
               {"taregts": 9,"searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                              
                                var out = '';
                                var path = row.mom_asset;
                                if (path) {
                                    var baseURL = path.replace("public/","");
                                    var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                                    out += '<a href="'+ url +'" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>';
                                }
                                
                                return out;
                            }
               },
               {"taregts": 10, 'data': 'mom_user'
               },
               {
                    "taregts": 11,
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        if (row.mom_image) {
                            var pdf_path = row.mom_image.replace("public", "");
                            var storage_path = "{{url('storage/')}}" + pdf_path;
                            return '<a class="btn" href="' + storage_path + '" target="_blank"><i class="fa fa-cloud-download fa-lg"></i></a>';
                        } else {
                            return '';
                        }
                    }
                },
               {"taregts": 12, 'data': 'fullname',
               },
               {"taregts": 13,"searchable": false, "orderable": false,
                  "render": function (data, type, row) {
                     var out=""; 
                     if (row.outsiders_email) {
                        out+= '<strong>'+row.outsiders_email.split(",").join("\n")+'</strong>'; 
                     }   
                     return out;      
                  }
               },
               {"taregts": 14, "searchable": false, "orderable": false,
                   "render": function (data, type, row) {
                       var id = row.id;
                       var out=""; 
                       
                        if (row.is_close == 'Yes' && row.mom_user_id == "{{Auth::user()->id}}" && row.mom_asset == null) {
                           out +='&nbsp;<a data-toggle="modal" data-target="#meetingModal" onclick="getUserDetails(' + row.id + ')" class="btn btn-primary btn-rounded" title="add MOM"><i class="fa fa-plus"></i></a>';
                        } 
                        if(row.user_id == "{{Auth::user()->id}}") {
                           if (row.is_close == 'No') {
                              out = '<a href="<?php echo url('edit_meeting') ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>'; 
                              out +='&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_meeting'); ?>/' + id + '\'\n\
                                 title="Delete"><i class="fa fa-trash"></i></a>';
                              out +='&nbsp;<a data-toggle="modal" data-target="#closemeetingModal" onclick="closeMeeting(' + row.id + ')" class="btn btn-danger btn-rounded" title="close meeting"><i class="fa fa-ban"></i></a>';
                           }
                           out +='&nbsp;<a data-toggle="modal" data-target="#momlistModal" onclick="getMOMUserList(' + row.id + ')" class="btn btn-primary btn-rounded" title="guest list"><i class="fa fa-list"></i></a>';
                           
                        }
                      
                       return out;
                   }
               },
           ]
   
       });
   
   })


   
   jQuery("#add_edit_meeting_mom").validate({
        ignore: [],
        rules: {
            meeting_mom: {
                required: true,
            }
        }
  });

  jQuery("#close_meeting").validate({
        ignore: [],
        rules: {
         attend_user_id: {
                required: true,
            },
            actual_meeting_start_datetime: {
                required: true,
            },
            actual_meeting_end_datetime: {
                required: true,
            }
        }
  });

   function delete_confirm(e) {
   swal({
       title: "Are you sure you want to delete Meeting ?",
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

   //---------------
   function close_confirm(e) {
   swal({
       title: "Are you sure you want to close this Meeting ?",
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
   
   function getUserDetails(id)
   {
   $("#meeting_id").val(id);
    // AJAX request
   $.ajax({
       url: "<?php echo url('get_user_meeting_mom_list') ?>" + "/" + id + "",
       method: 'get',
       data: {meeting_id: id},
       dataType: 'json',
       success: function (response) {
           $("#meeting_mom").val(response.meeting_mom_details);
   
       }
   });   
   }
   //---------
   function closeMeeting(id) {
      $("#close_meeting_id").val(id);
   }

    function getMOMUserList(id)
    {
        // AJAX request
        $.ajax({
            url: "<?php echo url('get_mom_user_list') ?>" + "/" + id + "",
            method: 'get',
            data: {meeting_id: id},
            dataType: 'json',
            success: function (response) {
                var myJSON = JSON.stringify(response);
                if (response.length == 0 || response.status == 0)
                {
                    $('#user_policyTable').empty();
                    $('#user_policyTable').append('<span>No Records Found !</span>');
                } else {
                    //var myJSON = JSON.stringify(response);
                    //console.log(myJSON);
                    var html = '<thead>'
                            + '<tr>'
                            + '<th>Name</th>'
                            + '<th>Status</th>'
                            + '</tr>'
                            + '</thead>'
                            + '<tbody>';
                    $.each(response, function (k, v) {
                     
                      if (v.status == 'Pending') {
                        var tag = '<b class="text-warning">Pendig</b>';
                      } else if ( v.status == 'Accept') {
                        var tag = '<b class="text-success">Accepted</b>';
                      } else {
                        var tag = '<b class="text-danger">Rejected</b>';
                      }
                        html += '<tr>'
                                + '<td>'
                                + v.name
                                + '</td>'
                                + '<td>'
                                + tag
                                + '</td>'
                                + '</tr>'
                        
                    });
                    html += '</tbody>'
                    $('#user_policyTable').empty();
                    $('#user_policyTable').append(html);
                }
            }
        });
    }

</script>
@endsection