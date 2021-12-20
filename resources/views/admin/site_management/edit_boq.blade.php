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
                    <div class="col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.update_boq') }}" id="boq_form" method="post">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{$boq_detail[0]->id}}" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Company: {{ $boq_detail[0]->company_detail->company_name }}</label>
                                        <input type="hidden" name="company_id" value="{{ $boq_detail[0]->company_id }}">
                                        <input type="hidden" name="project_id" value="{{ $boq_detail[0]->project_id }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Project: {{ $boq_detail[0]->project_detail->project_name }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">                            
                                    <div class="form-group ">
                                        <label>Item Description</label>
                                        <textarea class="form-control item_description" name="item_description" id="item_description">{{ $boq_detail[0]->item_description }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>UOM</label>
                                        <input type="text" class="form-control UOM" name="UOM" id="UOM" value="{{ $boq_detail[0]->UOM }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Quantity</label>
                                        <input type="number" onkeyup="calculate_amount();" class="form-control quantity" name="quantity" id="quantity" value="{{ $boq_detail[0]->quantity }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Quantity As Dwawing</label>
                                        <input type="number" class="form-control quantity_as_drawing" name="quantity_as_drawing" id="quantity_as_drawing" value="{{ $boq_detail[0]->quantity_as_drawing }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Rate</label>
                                        <input type="text" onkeyup="calculate_amount();" class="form-control rate" name="rate" id="rate" value="{{ $boq_detail[0]->rate }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Amount</label>
                                        <input type="text" readonly="" class="form-control amount" name="amount" id="amount" value="{{ $boq_detail[0]->amount }}" />
                                    </div>
                                </div>
                            </div>
                            <div id="sub_item_part">
                                @if($boq_detail_items_counter)
                                    @foreach($boq_detail_items as $key => $value)
                                        <div class="sub_item_add_more" id="sub_item_add_more_{{$key}}">
                                            @if($key == 0)
                                            <div class="row">
                                                <div class="col-md-12">
                                                <span><strong>Sub Items</strong></span>
                                                </div>
                                            </div>
                                            @endif
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12">
                                                <button type="button" class="btn btn-danger remove_item_delete" id="{{$value['id']}}">Remove</button>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group ">
                                                        <label>Item Description</label>
                                                        <textarea class="form-control item_description" name="item_description_add[{{$key}}]" id="item_description_{{$key}}">{{$value['item_description']}}</textarea>
                                                        <input type="hidden" class="form-control iten_id" name="iten_id_add[{{$key}}]" id="iten_id_{{$key}}" value="{{$value['id']}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group ">
                                                        <label>UOM</label>
                                                        <input type="text" class="form-control UOM" name="UOM_add[{{$key}}]" id="UOM_{{$key}}" value="{{$value['UOM']}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group ">
                                                        <label>Quantity</label>
                                                        <input type="number" onkeyup="calculate_amount_item_add({{$key}});" class="form-control quantity" name="quantity_add[{{$key}}]" id="quantity_{{$key}}" value="{{$value['quantity']}}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group ">
                                                        <label>Quantity As Dwawing</label>
                                                        <input type="number" class="form-control quantity_as_drawing" name="quantity_as_drawing_add[{{$key}}]" id="quantity_as_drawing_{{$key}}" value="{{$value['quantity_as_drawing']}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group ">
                                                        <label>Rate</label>
                                                        <input type="text" onkeyup="calculate_amount_item_add({{$key}});" class="form-control rate" name="rate_add[{{$key}}]" id="rate_{{$key}}" value="{{$value['rate']}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group ">
                                                        <label>Amount</label>
                                                        <input type="text" readonly="" class="form-control amount" name="amount_add[{{$key}}]" id="amount_{{$key}}" value="{{$value['amount']}}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-primary" onclick="addMoreItem()">Add Sub Item</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" onclick="window.location.href ='{{ route('admin.site_management') }}'" class="btn btn-default">Cancel</button>
                                </div>
                            </div>
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
var item_counter = {{$boq_detail_items_counter}} ;    
    function calculate_amount(){
        var total_amt = parseFloat($('#rate').val()) * parseFloat($('#quantity').val());
        $('#amount').val(total_amt.toFixed(2));
    }

    function calculate_amount_item_add(element){
        var total_amt = parseFloat($('#rate_'+element).val()) * parseFloat($('#quantity_'+element).val());
        $('#amount_'+element).val(total_amt.toFixed(2))
    }
    
//remove item
$("body").on('click','.remove_item',function(){
    $(this).parents('.sub_item_add_more').remove();
    var numItems = $('.sub_item_add_more').length
    if(numItems == 0){
        item_counter = 0;
    }
});

$("body").on('click','.remove_item_delete',function(){
    var delete_id = $(this).attr('id');
    $(this).parents('.sub_item_add_more').remove();
    var numItems = $('.sub_item_add_more').length
    if(numItems == 0){
        item_counter = 0;
    }
    $.ajax({
        type : "POST",
        url : "{{url('delete_boq_sub_item')}}",
        data : {
            id : delete_id,
            "_token" : "{{csrf_token()}}"
        },
        success : function(data){
            
        }
    });
    
});

function addMoreItem(){
    item_counter += 1;
    if(item_counter == 1){
        display_label = "display:block;"
    }else{
        display_label = "display:none;"
    }
    $("#sub_item_part").append('<div class="sub_item_add_more" id="sub_item_add_more_'+item_counter+'">'+
                                '<div class="row" style="'+display_label+'">'+
                                    '<div class="col-md-12">'+
                                    '<span><strong>Sub Items</strong></span>'+
                                    '</div>'+
                                '</div><hr>'+
                                '<div class="row">'+
                                    '<div class="col-md-12">'+
                                    '<button type="button" class="btn btn-danger remove_item">Remove</button>'+
                                    '</div>'+
                                '</div><br>'+
                                '<div class="row">'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Item Description</label>'+
                                            '<textarea class="form-control item_description" name="item_description_add['+item_counter+']" id="item_description_'+item_counter+'"></textarea>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>UOM</label>'+
                                            '<input type="text" class="form-control UOM" name="UOM_add['+item_counter+']" id="UOM_'+item_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Quantity</label>'+
                                            '<input type="number" onkeyup="calculate_amount_item_add('+item_counter+');" class="form-control quantity" name="quantity_add['+item_counter+']" id="quantity_'+item_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="row">'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Quantity As Dwawing</label>'+
                                            '<input type="number" class="form-control quantity_as_drawing" name="quantity_as_drawing_add['+item_counter+']" id="quantity_as_drawing_'+item_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Rate</label>'+
                                            '<input type="text" onkeyup="calculate_amount_item_add('+item_counter+');" class="form-control rate" name="rate_add['+item_counter+']" id="rate_'+item_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Amount</label>'+
                                            '<input type="text" readonly="" class="form-control amount" name="amount_add['+item_counter+']" id="amount_'+item_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>');
}

$('form#boq_form').on('submit', function(event) {
    $('.item_description').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.UOM').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.quantity').each(function() {
        $(this).rules('add', {
            required: true,
            number: true
        });
    });
    $('.quantity_as_drawing').each(function() {
        $(this).rules('add', {
            required: true,
            number: true
        });
    });
    $('.rate').each(function() {
        $(this).rules('add', {
            required: true,
            number: true
        });
    });
    $('.amount').each(function() {
        $(this).rules('add', {
            required: true,
            number: true
        });
    });

});    
    jQuery("#boq_form").validate({
        rules: {
            
            item_description: {
                required: true,
            },
            UOM: {
                required: true,
            },
            quantity: {
                required: true,
                number: true
            },
            rate: {
                required: true,
                number: true
            },
            amount: {
                required: true,
                number: true
            },
        },

    });

</script>
@endsection
