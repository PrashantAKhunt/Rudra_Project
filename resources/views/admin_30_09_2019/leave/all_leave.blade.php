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
                <p class="text-muted m-b-30"></p>
                <br>                
                <div class="table-responsive">
                    <table id="leave_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>LEAVE DATES</th>
                                <th>LEAVE TYPE</th>                              
								<th>STATUS</th>
								<th>ACTION TAKEN ON</th>                                
                                <th>Approve</th>
								<th>Reject</th>
							</tr>
                        </thead>
                        <tbody>                            
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
				
				var leaveStatus = {1 : 'Pending', 2 : 'Approved', 3 : 'Rejected', 4 : 'Canceled'};
				var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');

				function month_name(dt) {
					const objDate = new Date(dt);
					const locale = "en-us";
					const month = objDate.toLocaleString(locale, {
						month: "short"
					});
					return month;
				}				
                var table = $('#leave_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[1, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_all_leave_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "render":function(data,type,row){
                                return row.user_name;
                            }
                        },
						{"taregts": 1, "render":function(data,type,row){
								var diff = new Date(new Date(row.end_date) - new Date(row.start_date));
								var days = (diff/1000/60/60/24) + 1;
								var date = month_name(row.start_date) + moment(row.start_date).format(" DD, YYYY");
								return date +'</br> No. of days - '+ days;
							}
                        },                        
						{"taregts": 2, "render":function(data,type,row){								
								var leaveType = row.category_name;
								var date = month_name(row.created_at) + moment(row.created_at).format(" DD, YYYY");
								return leaveType +'</br> Requested on - '+ date;
							}
                        },						
						{"taregts": 3, "searchable": false, "render":function(data,type,row){
								return leaveStatus[row.leave_status];
							}
                        },
						{"taregts": 4, "render":function(data,type,row){
								return month_name(row.updated_at) + moment(row.updated_at).format(" DD, YYYY");
							}
                        },                                                                     
                        {"taregts": 5, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                if (row.leave_status == 1 && ($.inArray('2',access_rule) !== -1))
									return '<a href="<?php echo url("approve_leave") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-check"></i></a>'; 
								else
									return "";                               
                            }
                        },
						{"taregts": 6, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                if (row.leave_status == 1 && ($.inArray('2',access_rule) !== -1))
									return '<a href="<?php echo url("reject_leave") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>'; 
								else
									return "";                               
                            }
                        },
                    ]

                });
            })
        </script>
        @endsection
