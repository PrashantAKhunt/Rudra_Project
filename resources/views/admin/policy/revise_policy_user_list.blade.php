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
    </div>
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
                    <table id="policy_table" class="table table-striped">
                        <thead>
                        </thead>
                        <tbody>
                        @if($policyImageList)
                        <a target="_blank" href='{{ asset('storage/'.str_replace('public/','',$policyImageList)) }}' class="btn btn-primary">
                            <i class="fa fa-eye"></i> View Policy
                        </a>
                        @else
                        <tr>
                            <td colspan="12" align="center">
                                No Records Found !
                            </td>
                        </tr>
                        @endif             
                        </tbody>
                    </table>
                    @if($policyImageList && $check_result==0)
                    <form action="{{ route('admin.confirm_user_policy') }}" id="confirm_user_policy" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $policy_revise_list[0]['id'] }}">
                        <input type="hidden" name="policy_id" value="{{ $policy_revise_list[0]['policy_id'] }}" id="policy_id">
                        <input type="hidden" name="status" id="status">
                        <div class="col-md-12 pull-left">
                            <button type="button" onclick="UserConfirmPolicy('Approved')" class="btn btn-success">Confirm Policy</button>
                            <!--<button type="button" onclick="UserConfirmPolicy('Rejected')" class="btn btn-danger">Reject Policy</button>-->
                            <button type="button" onclick="window.location.href ='{{ route('admin.revise_policy_list') }}'" class="btn btn-default">Cancel</button>
                        </div>
                    </form>
                    @else
                    <b class="error">Policy Already Accepted.</b>
                    <button type="button" onclick="window.location.href ='{{ route('admin.revise_policy_list') }}'" class="btn btn-default">Back</button>
                    @endif
                </div>
            </div>  
            <div id="galleryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">Revise Policy</h4>
                        </div>
                        <div class="modal-body" id="tableBodyPolicy">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>          
        </div>    
        @endsection

        @section('script')		
        <script>
            $(document).ready(function () {

            })
            function UserConfirmPolicy(msg) {
                $('#status').val(msg);
                swal({
                    title: "Are you sure you want to " + msg + " revise policy?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    $("#confirm_user_policy").submit();
                });
            }

            function openPolicy(image) {
                $('#tableBodyPolicy').empty();
                var iframeUrl = "<iframe src=" + image + "#toolbar=0 height='400' width='880'></iframe>";
                $('#tableBodyPolicy').append(iframeUrl);
            }
        </script>
        @endsection