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
                <!-- <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li> -->
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
                        <form action="{{ route('admin.insert_rtgs_register') }}" id="add_rtgs" method="post">
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
                                
                                <select class="form-control" id="bank_id" name="bank_id">
                                    <option value="">Select bank</option>
                                </select>
                                

                                 <!-- <select name="bank_id" class="form-control" >
                                    <option>Select Bank</option>
                                    @foreach($banks as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                 </select> -->
                            </div>
                           <!--  <div class="form-group ">
                                <label>Ref. No</label>
                                 <input type="text" class="form-control" name="check_ref_no" id="check_ref_no" value="" /> 
                            </div> -->
                            <div class="form-group ">
                                <label>RTGS Start Number</label><span>Please not change start RTGS number</span>
                                 <input type="number" class="form-control" name="rtgs_start_number" id="rtgs_start_number" value="<?php echo $last_rtgs;?>" /> 
                            </div>
                            <div class="form-group ">
                                <label>RTGS End Number</label>
                                 <input type="number" class="form-control" name="rtgs_end_number" id="rtgs_end_number" value="" /> 
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.blank_rtgs_list') }}'" class="btn btn-default">Cancel</button>
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
        $("#company_id").change(function() {

            var company_id = $("#company_id").val();
            
            $.ajax({
                url: "{{ route('admin.get_bank_list_cheque')}}",
                type: 'get',
                data: "company_id="+company_id,
                success: function( data, textStatus, jQxhr ){
                    $('#bank_id').empty();
                    $('#bank_id').append('<option value="">Select bank</option>');
                    $('#bank_id').append(data);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        });
   });

    jQuery("#add_rtgs").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            bank_id:{
                required: true,
            },
            rtgs_start_number:{
                required: true,
            },
            rtgs_end_number:{
                required: true,
            }
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });
      
</script>
@endsection
