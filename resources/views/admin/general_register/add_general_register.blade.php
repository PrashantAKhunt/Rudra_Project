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
                <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">

                <div class="row">
                    <div class="col-sm-6 col-xs-6">
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
                        <form action="{{ route('admin.insert_general_register') }}" id="add_cheque" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Company</label> 
                                @if(!empty($companies))
                                    <select name="company_id" class="form-control" id="company_id">
                                    <option value="">Select company</option>
                                        @foreach($companies as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="form-group ">
                                <label>Bank Name</label>
                                <div id="response">
                                <select class="form-control" id="bank_id" name="bank_id">
                                    <option value="">Select bank</option>
                                </select>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label>User Name</label>
                                <select class="form-control" id="user_id" name="user_id">
                                    <option value="">Select user</option>
                                    <?php
                                    foreach ($users as $key => $usersData) {
                                    ?>
                                    <option value="<?php echo $usersData['id']?>">{{$usersData['name']}}</option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- <div class="form-group ">
                                <label>Vendor Name</label>
                                <div id="vendor_response">
                                <select class="form-control">
                                    <option>Select project</option>
                                </select>
                                </div>
                            </div> -->
                            
                            <div class="form-group "> 
                                <label>Cheque</label> 
                                <select name="cheque_id" class="form-control" id="cheque_id">
                                <option value="">Select cheque</option>
                                </select>
                            </div>

                            <div class="form-group "> 
                                <label>Issue Date</label> 
                                <input type="text" class="form-control" name="issue_date" id="issue_date" value="" /> 
                            </div>

                            <div class="form-group ">
                                <label>Amount</label>
                                 <input type="text" class="form-control" name="amount" id="amount" value="" /> 
                            </div>
                            <div class="form-group ">
                                <label>Work Details</label>
                                 <!-- <input type="text" class="form-control" name="work_detail" id="work_detail" value="" />  -->
                                 <textarea class="form-control" name="work_detail" id="work_detail"></textarea>
                            </div>
                            <div class="form-group ">
                                <label>Remark</label>
                                 <!-- <input type="text" class="form-control" name="work_detail" id="work_detail" value="" />  -->
                                 <textarea class="form-control" name="remark" id="remark"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.general_register') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection


@section('script')
<script>

   $(document).ready(function () {
        
        jQuery('#issue_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });


        $("#company_id").change(function() {

            var company_id = $("#company_id").val();
            
            $.ajax({
                url: "{{ route('admin.get_bank_list_cheque')}}",
                type: 'get',
                data: "company_id="+company_id,
                success: function( data, textStatus, jQxhr ){
                    $('#bank_id').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

            $.ajax({
                url: "{{ route('admin.get_project_list')}}",
                type: 'get',
                data: "company_id="+company_id,
                success: function( data, textStatus, jQxhr ){
                    $('#proj_response').html(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

            $.ajax({
                url: "{{ route('admin.get_vendor_list')}}",
                type: 'get',
                data: "company_id="+company_id,
                success: function( data, textStatus, jQxhr ){
                    $('#vendor_response').html(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });

        $("#bank_id").change(function() {
            var company_id = $("#company_id").val();
            var bank_id = $("#bank_id").val();

            $.ajax({
                url: "{{ route('admin.get_cheque_list')}}",
                type: 'get',
                data: "company_id="+company_id,
                success: function( data, textStatus, jQxhr ){
                    $('#cheque_id').html(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });
   });

    jQuery("#add_cheque").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            bank_id:{
                required: true,
            },
            work_detail:{
                required: true,
            },
            cheque_id:{
                required:true,
            },
            issue_date:{
                required: true,
            },
            amount:{
                required: true,
            },
            // check_ref_no:{
            //     required: true,
            // },
            remark:{
                required: true,
            }
        }
    });
      
</script>
@endsection
