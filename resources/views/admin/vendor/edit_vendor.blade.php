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
                            <form action="{{ route('admin.update_vendor') }}" id="edit_vendor" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $vendor_detail[0]->id }}" />
                            @csrf

							<div class="form-group ">
                                <label>Company</label>
                                @if(!empty($companies))
                                    <select id="company_id" name="company_id" class="form-control" >
                                    <option value="">Select company <span class="error">*</span> </option>
                                        @foreach($companies as $key => $value)
                                            <option value="{{$key}}" <?php echo ($vendor_detail[0]->company_id==$key)?"selected='selected'":'' ?> >{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="form-group ">
                                <label>Vendor Name <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="vendor_name" id="vendor_name" value="{{ $vendor_detail[0]->vendor_name }}" />
                                <div id="vendor_name_render" style="position: relative;"></div>
                            </div>
                            <div class="form-group ">
                                <label>Email <span class="error">*</span> </label>
                                <input type="email" class="form-control" name="email" id="email"  value="{{ $vendor_detail[0]->email }}" />
                            </div>
                             <div class="form-group ">
                                <label>Contact Number <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="contact_no" id="contact_no" value="{{ $vendor_detail[0]->contact_no }}" />
                            </div>
                            <div class="form-group ">
                                <label>Address <span class="error">*</span> </label>
                                <textarea class="form-control" rows="2" name="address" id="address" >{{ $vendor_detail[0]->address }}</textarea>
                            </div>
							<div class="form-group ">
                                <div class="checkbox checkbox-success">
                                    <input id="checkbox6c" type="checkbox" name="allow_fields" class="allow_fields" @if($vendor_detail[0]->pan_card_number) checked @endif>
                                    <label for="checkbox6c">Vender PAN Card Number Allow<!-- & GST Number -->  </label>
                                </div>
                            </div>
                            <div class="form-group pan_allow_div">
                                <label>Pan Card Number <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="pan_card_number" id="pan_card_number" value="{{ $vendor_detail[0]->pan_card_number }}" />
                            </div>

                            <div class="form-group pan_allow_div">
                                <label>GST Number</label>
                                <input type="text" class="form-control" name="gst_number" id="gst_number" value="{{ $vendor_detail[0]->gst_number }}" />
                            </div>


                            <div class="form-group ">
                                <label>Detail <span class="error">*</span> </label>
                                <textarea class="form-control" name="detail" id="detail" rows="10">{{$vendor_detail[0]->detail}}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.vendors') }}'" class="btn btn-default">Cancel</button>
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
$(function(){
        $(".allow_fields").trigger('change');
    });
    jQuery("#edit_vendor").validate({
        ignore: [],
        rules: {
            vendor_name: {
                required: true,
                remote: {
                    url: "{{ url('check_vender_name') }}",
                    type: "post",
                    data: {
                        company_id: function () {
                            return $("#company_id").val();
                        },
						vendor_name:function(){
							return $("#vendor_name").val();
						},
						id:function(){
							return $("#id").val();
						},
                        "_token": "{{ csrf_token() }}",
                    }
                }
            },
            email: {
                required: true,
            },
            company_id: {
                required: true,
            },
            pan_card_number:{
                required: ".allow_fields:checked",
                remote: {
                    url: "{{ url('check_uniquePancardNumber') }}", //check_uniqueRoleName
                    type: "post",
                    data: {
                        pan_card_number: function () {
                            return $("#pan_card_number").val();
                        },
                        vendor_id:function(){
                            return $('#id').val();
                        },
						company_id:function(){
							return $("#company_id").val();
						},
                        "_token": "{{ csrf_token() }}",
                    }
                }
            },
            detail: {
                required: true,
            },
            contact_no:{
                required: true,
            },
            address:{
                required: true,
            },
        },
         // Specify the validation error messages
        messages: {
            pan_card_number: {
                required: "Pan Card Number is required.",
                remote: "Pan Card Number is already exists."
            },
            vendor_name: {
                remote: "Vender name is already exists."
            }
        }
    });
    $(".allow_fields").on('change',function(){
        var value_fields = $(".allow_fields:checked").val();

        if(value_fields == "on"){
            $(".pan_allow_div").show();
        }else{
            $(".pan_allow_div").hide();
            $("#pan_card_number").val("");
            $("#gst_number").val("");
        }
    }) ;

    $('body').on('keyup',"#vendor_name",function(){
        var vendor_name = $(this).val();
        var company_id = $("#company_id").val();
        if(vendor_name != "" && company_id != ""){
            // console.log(search);
            // console.log(search_id);
            $.ajax({
                url: "{{url('vender_name_autosuggest')}}",
                type: 'post',
                data: {
                    vendor_name:vendor_name,
                    company_id:company_id,
                    "_token" : "{{csrf_token()}}",
                },
                dataType: 'json',
                success:function(data){
                    console.log(data);
                    if(data.length){
                        $('#vendor_name_render').html("");
                        output = '<ul class="dropdown-menu" style="display:block; position:absolute;">';
                        $.each( data, function(key, value){
                        output += '<li><a href="javascript:void(0)" id="'+value+'" onclick="setData(this.id)">'+value+'</a></li>';
                        });
                        output += '</ul>';
                        $('#vendor_name_render').show();
                        $('#vendor_name_render').html(output);
                        search_id = "";
                    }else{
                        $('#vendor_name_render').hide();
                        search_id = "";
                    }

                }
            });
        }
        });
        function setData(data){
            $("#vendor_name").val(data);
            $('#vendor_name_render').hide();
        }
</script>
@endsection
