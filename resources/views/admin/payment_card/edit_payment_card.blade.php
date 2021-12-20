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
                        <form action="{{ route('admin.update_payment_card') }}" id="add_bank" method="post">
                            @csrf

                            <input type="hidden" id="id" name="id" value="{{ $card_detail[0]->id }}" /> 
                         

                            <div class="form-group ">
                                <label>Company</label>
                                <select class="form-control" name="company_id" id="companies_list">

                                    @foreach($companies as $value)
                                    <option @if($value->id==$card_detail[0]->company_id) selected @endif value="{{$value['id']}}"> {{$value['company_name']}} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">

                                <label>Bank</label>
                                
                                <select class="form-control" name="bank_id" id="banks_list" required>
                                @foreach($banks as $value)
                                    <option @if($value->id==$card_detail[0]->vendor_id) selected @endif value="{{$value['id']}}"> {{$value['bank_name']}} - {{$value['ac_number']}} </option>
                                    @endforeach
                                </select>


                            </div>
                            <div class="form-group ">
                                <label>Card Type</label>
                                <select name="card_type" class="form-control">
                                <option <?php if ($card_detail[0]->account_type == "Credit Card") { ?> selected <?php } ?> value="Credit Card">Credit Card</option>
                                    <option <?php if ($card_detail[0]->account_type == "Debit Card") { ?> selected <?php } ?> value="Debit Card">Debit Card</option>
                        
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Card Number</label>
                                <input class="form-control" name="card_number" value="{{$card_detail[0]->card_number}}" id="card_number" />
                            </div>
                            <div class="form-group ">
                                <label>Name on Card</label>
                                <input class="form-control" name="name_on_card" id="name_on_card" value="{{$card_detail[0]->name_on_card}}" />
                            </div>

                            <div class="form-group "> 
                                <label>Assign To Employee <span class="error">*</span> </label> 
                                <select class="form-control" name="assigncard_user_id"  id="assigncard_user_id">
                                    <option value="" >Select User</option>
                                    @foreach($users_data as $user)
                                        <option @if($card_detail[0]->assigncard_user_id==$user->id) selected @endif value="{{$user->id}}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                           


                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.payment_card') }}'" class="btn btn-default">Cancel</button>
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

$(document).ready(function(){   
    $('#assigncard_user_id').select2(); 

    jQuery("#add_bank").validate({
        ignore: [],
        rules: {
            companies_list: {
                required: true,
            },
            assigncard_user_id: {
                required: true,
            },
            banks_list: {
                required: true,
            },
            card_type: {
                required: true,
            },
            card_number: {
                required: true,
                remote: {
                    url: "{{ url('check_uniqueCardNumber') }}", //check_uniquecardNo
                    type: "GET",
                    data: {
                        company_id: function () {
                            return $("#card_number").val();
                        },
                        payment_card_id:function(){
                            return $('#id').val();
                        },
                        "_token": "{{ csrf_token() }}",
                    }
                }
            },
            name_on_card: {
                required: true,
            }
          
        },

         // Specify the validation error messages
         messages: {
            card_number: {
                required: "Card Number is required.",
                remote: "Card Number is already exists."
            }
        }
    });

});
</script>
<script>

$("#companies_list").change(function () {

    companies_list = $(this).val();

    if (companies_list) {
        //alert(companies_list);
        $.ajax({

            url: "{{ route('admin.companies_bank') }}",
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                company_id: companies_list
            },
            dataType: "JSON",
            //processData: false,
            //contentType: false,
            success: function (data) {
                //alert(data.id)
                //$("#user_list").html('');
                $("#banks_list").empty();
                $("#banks_list").append("<option value='' disabled selected>Please select</option>");
                $.each(data, function (index, bank_obj) {
                    //alert(key);


                    $("#banks_list").append('<option value="' + bank_obj.id + '">' + bank_obj.bank_name + ' - ' + bank_obj.ac_number+ '</option>');
                })

            }
        });

    } else {

        $("#projects_list").empty();

    }

});
</script>
@endsection
