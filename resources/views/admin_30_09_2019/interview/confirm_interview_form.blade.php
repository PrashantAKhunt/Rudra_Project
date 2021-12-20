<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin_asset/assets/plugins/images/favicon.png') }}">
        <title>{{ $setting_details[0]->setting_value }}</title>
        <!-- Bootstrap Core CSS -->
        <link href="{{ asset('admin_asset/assets/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- animation CSS -->
        <link href="{{ asset('admin_asset/assets/css/animate.css') }}" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="{{ asset('admin_asset/assets/css/style.css') }}" rel="stylesheet">
        <!-- color CSS -->
        <link href="{{ asset('admin_asset/assets/css/colors/default.css') }}" id="theme"  rel="stylesheet">

    </head>
    <body>
        <!-- Preloader -->
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header"> 
                <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>
                <div class="top-left-part"><a class="logo" href="http://localhost/account_manager/public/dashboard"><b><!--This is dark logo icon--><img src="http://localhost/account_manager/public/admin_asset/assets/plugins/images/eliteadmin-logo.png" alt="home" class="dark-logo"><!--This is light logo icon--><img src="http://localhost/account_manager/public/admin_asset/assets/plugins/images/eliteadmin-logo-dark.png" alt="home" class="light-logo"></b><span class="hidden-xs"><!--This is dark logo text--><img src="http://localhost/account_manager/public/admin_asset/assets/plugins/images/eliteadmin-text.png" alt="home" class="dark-logo"><!--This is light logo text--><img src="http://localhost/account_manager/public/admin_asset/assets/plugins/images/eliteadmin-text-dark.png" alt="home" class="light-logo"></span></a></div>
                <ul class="nav navbar-top-links navbar-left hidden-xs">
                    <li><a href="javascript:void(0)" class="open-close hidden-xs waves-effect waves-light"><i class="icon-arrow-left-circle ti-menu"></i></a></li>

                </ul>

            </div>
        </nav>
        <div class="white-box">
            <h2 class="box-title">Yours Confirmation Details</h2>
            <form action="{{ route('admin.emp_confirm_interview') }}" id="emp_confirm_interview" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $interview_list[0]['id'] }}">
                <div class="col-md-2">
                <label>Candidate Name</label>
                {{ $interview_list[0]['name'] }}
                <input type="hidden" name="name" value="{{ $interview_list[0]['name'] }}">
                </div>
                <div class="col-md-3">
                <label>Designation</label>
                {{$_SESSION['guest_job_position']}}
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="col-md-2">
                <label>Email</label>
                {{ $interview_list[0]['email'] }}
                 <input type="hidden" name="email" value="{{ $interview_list[0]['email'] }}">
                </div>
                <div class="col-md-3">
                <label>Phone Number</label>
                {{ $interview_list[0]['contact_number'] }}
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="col-md-2">
                <label>residential_address</label>
                {{ $interview_list[0]['residential_address'] }}
                </div>
                <div class="col-md-3">
                <label>permanent_address</label>
                {{ $interview_list[0]['permanent_address'] }}
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="col-md-2">
                <label>Birt Date</label>
                {{ $interview_list[0]['birth_date'] }}
                </div>
                <div class="col-md-3">
                <label>Gender</label>
                {{ $interview_list[0]['gender'] }}
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="col-md-2">
                <label>Annual Package</label>
                {{ $interview_list[0]['package'] }}
                </div>
                <div class="col-md-3">
                <label>Join Date</label>
                {{ date('Y-m-d',strtotime($interview_list[0]['join_date'])) }}
                </div>
                <br>
                <div class="clearfix"></div>
                <br>

                 <div class="row">
                    <div class="col-md-12">
                      <h4 class="box-title m-b-20">Note:- Please read below given all the policy carefully before confirmation</h4>
                      <div class="panel-group" id="exampleAccordionDefault" aria-multiselectable="true" role="tablist">
                        <?php 
                        foreach ($policy_list as $key => $policy_list_data) {
                        ?>
                        <div class="panel">
                          <div class="panel-heading" id="exampleHeadingDefaultOne<?php echo $key?>" role="tab"> <a class="panel-title collapsed" data-toggle="collapse" href="#exampleCollapseDefaultOne<?php echo $key?>" data-parent="#exampleAccordionDefault" aria-expanded="false" aria-controls="exampleCollapseDefaultOne"> {{$policy_list_data['title']}} </a> </div>
                          <div class="panel-collapse collapse" id="exampleCollapseDefaultOne<?php echo $key?>" aria-labelledby="exampleHeadingDefaultOne<?php echo $key?>" role="tabpanel" aria-expanded="false" style="height: 0px;">
                            <div class="panel-body">
                                <?php 
                                $url  = url('/storage/app/'.$policy_list_data['name']);
                                $path = str_replace("public/","",$url);
                                ?>
                                <a href="#" onclick="openPolicy('<?php echo $path; ?>')" data-toggle="modal" data-target="#policyModal" title="View">Click here for Privacy Policy</a>
                            </div>
                          </div>
                        </div>
                        <?php }?>
                      </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="col-md-3">
                <input type="checkbox" name="term">
                <label> <b>Agree With Term & Condition</b></label>
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="col-md-3 pull-left">
                    <button type="submit" class="btn btn-success">Confirm</button>
                </div>
            </form>
            <div id="policyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">Privacy Policy</h4>
                        </div>
                        <div class="modal-body" id="tableBodyPolicy">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
            </div>
        </div>
        <!-- <footer class="footer text-center"> 2019 © Account Manager, Developed By <a target="_blank" href="https://wappnet.com">Wappnet Systems</a> </footer> -->
        <!-- jQuery -->
        <script src="{{ asset('admin_asset/assets/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="{{ asset('admin_asset/assets/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <!-- Menu Plugin JavaScript -->
        <script src="{{ asset('admin_asset/assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>

        <!--slimscroll JavaScript -->
        <script src="{{ asset('admin_asset/assets/js/jquery.slimscroll.js') }}"></script>
        <!--Wave Effects -->
        <script src="{{ asset('admin_asset/assets/js/waves.js') }}"></script>
        <!-- Custom Theme JavaScript -->
        <script src="{{ asset('admin_asset/assets/js/custom.min.js') }}"></script>
        <!--Style Switcher -->
        <script src="{{ asset('admin_asset/assets/plugins/bower_components/styleswitcher/jQuery.style.switcher.js') }}"></script>
        <script src="{{asset('admin_asset/assets/js/validate.js') }}"></script>
        
        <script>
            $(document).ready(function(){
               $('#emp_confirm_interview').validate({
                     rules: {
                        term:{
                        required:true,
                        message:"js jsjs"
                        }
                    }
               }) 
            });
            function openPolicy(pdf,id) {
                $('#tableBodyPolicy').empty();
                var iframeUrl = "<iframe src="+pdf+"#toolbar=0 height='400' width='880'></iframe>";
                $('#tableBodyPolicy').append(iframeUrl);
            }
        </script>
        
    </body>
</html>
<style type="text/css">
.white-box {
    background: #442a2a17 !important;
}
</style>


