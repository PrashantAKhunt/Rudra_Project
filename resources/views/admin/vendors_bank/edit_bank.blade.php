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
                <li><a href="{{ route('admin.vendors_bank') }}">{{ $module_title }}</a></li>
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
                        <form action="{{ route('admin.update_vendors_bank') }}" id="edit_bank" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $vendor_bank_detail[0]->id }}" />
                            @csrf

                            <div class="form-group ">
                                <label>Company</label>
                                <select class="form-control" name="company_id" id="companies_list">

                                    @foreach($companies as $value)
                                    <option @if($value->id==$vendor_bank_detail[0]->company_id) selected @endif value="{{$value['id']}}"> {{$value['company_name']}} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">

                                <label>Vendors</label>
                                
                                <select class="form-control" name="vendor_id" id="vendors_list" required>
                                @foreach($vendors as $value)
                                    @if($value['vendor_name'] != "Other")
                                        <option @if($value->id==$vendor_bank_detail[0]->vendor_id) 
                                        selected @endif value="{{$value['id']}}"> {{$value['vendor_name']}}
                                        </option>
                                    @endif
                                    <!-- //148 id -->
                                @endforeach
                                </select>


                            </div>
                            <div class="form-group ">
                                <label>Bank Name <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="bank_name" id="bank_name" value="{{ $vendor_bank_detail[0]->bank_name }}" />
                            </div>
                            <div class="form-group ">
                                <label>Account Number <span class="error">*</span> </label>
                                <input class="form-control" name="account_number" value="{{ $vendor_bank_detail[0]->ac_number }}" id="account_number" />
                            </div>

                            <div class="form-group ">
                                <label>Name on Account <span class="error">*</span> </label>
                                <input class="form-control" name="beneficiary_name" id="beneficiary_name" value="{{ $vendor_bank_detail[0]->beneficiary_name }}" />
                            </div>
                            <div class="form-group ">
                                <label>IFSC <span class="error">*</span> </label>
                                <input class="form-control" name="ifsc" id="ifsc" value="{{ $vendor_bank_detail[0]->ifsc }}" />
                            </div>
                            <div class="form-group ">
                                <label>MICR <span class="error">*</span> </label>
                                <input class="form-control" name="micr_code" id="micr_code" value="{{ $vendor_bank_detail[0]->micr_code }}"/>
                            </div>
                            <div class="form-group ">
                                <label>SWIFT <span class="error">*</span> </label>
                                <input class="form-control" name="swift_code" id="swift_code" value="{{ $vendor_bank_detail[0]->swift_code }}"/>
                            </div>
                            <div class="form-group ">
                                <label>Bank Branch <span class="error">*</span> </label>
                                <input class="form-control" name="branch" id="branch" value="{{ $vendor_bank_detail[0]->branch }}" />
                            </div>
                            <div class="form-group ">
                                <label>Account Type <span class="error">*</span> </label>
                                <select name="account_type" class="form-control">
                                    <option value="">Select Account Type</option>
                                    <option <?php if ($vendor_bank_detail[0]->account_type == "CC") { ?> selected <?php } ?> value="CC">CC</option>
                                    <option <?php if ($vendor_bank_detail[0]->account_type == "Current") { ?> selected <?php } ?> value="Current">Current</option>
                                    <option <?php if ($vendor_bank_detail[0]->account_type == "FD") { ?> selected <?php } ?> value="FD">FD</option>
                                    <option <?php if ($vendor_bank_detail[0]->account_type == "Saving") { ?> selected <?php } ?> value="Saving">Savings</option>

                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Detail <span class="error">*</span> </label>
                                <textarea class="form-control" name="detail" id="detail" rows="10">{{$vendor_bank_detail[0]->detail}}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.vendors_bank') }}'" class="btn btn-default">Cancel</button>
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
    $('#vendors_list').select2();
    $("#companies_list").change(function() {
       
        let company_id = $(this).val();
        let vendor_id = <?php echo json_encode($vendor_bank_detail[0]->vendor_id); ?>;
        
        
        if (company_id) {

            $.ajax({

                url: "{{ route('admin.companies_vendor') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    company_id: company_id
                },
                dataType: "JSON",
                //processData: false,
                //contentType: false,
                success: function(data) {
                    //alert(data.id)
                    //$("#user_list").html('');
                    $("#vendors_list").empty();
                    $.each(data, function(index, vendors_obj) {
                        //alert(key);
                        if(vendors_obj.vendor_name != "Other" && vendors_obj.vendor_name != "Others")
                            $("#vendors_list").append('<option value="' + vendors_obj.id + '">' + vendors_obj.vendor_name + '</option>');
                    })

                }
            });

        } else {

            $("#vendors_list").empty();

        }

    });
</script>
<script>
    jQuery("#edit_bank").validate({
        ignore: [],
        rules: {
            bank_name: {
                required: true,
            },
            detail: {
                required: true,
            },
            company_id: {
                required: true,
            },
            vendor_id: {
                required: true,
            },
            beneficiary_name: {
                required: true,
            },
            ifsc: {
                required: true,
            },
            micr_code: {
                required: true,
            },
            swift_code: {
                required: true,
            },
            branch: {
                required: true,
            },
            account_type: {
                required: true,
            },
            account_number: {
                required: true,
                remote: {
                    url: "{{ url('check_uniqueAccountNumber') }}", //check_uniqueRoleName
                    type: "post",
                    data: {
                        company_id: function () {
                            return $("#companies_list").val();
                        },
                        vendor_id: function () {
                            return $("#vendors_list").val();
                        },
                        account_number: function () {
                            return $("#account_number").val();
                        },
                        id: function () {
                            return $("#id").val();
                        },
                        "_token": "{{ csrf_token() }}",
                    }
                }
            }
        },
         // Specify the validation error messages
         messages: {
            account_number: {
                required: "Account Number is required.",
                remote: "Account Number is already exists."
            }
        },


    });
</script>
@endsection