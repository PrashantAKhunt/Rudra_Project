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
                        <form action="{{ route('admin.insert_boq') }}" id="boq_form" method="post">
                            @csrf
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Select Company <span class="error">*</span> </label>
                                        <select onchange="get_project_by_company()" class="form-control company_id" id="company_id" name="company_id">
                                            <option value="">Select Company</option>
                                            @foreach($company_list as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Select Project</label>
                                        <select class="form-control project_id" id="project_id" name="project_id">
                                            <option>Select Project</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Item Description <span class="error">*</span> </label>
                                        <textarea class="form-control item_description" name="item_description" id="item_description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>UOM <span class="error">*</span> </label>
                                        <input type="text" class="form-control UOM" name="UOM" id="UOM" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Quantity <span class="error">*</span> </label>
                                        <input type="number" onkeyup="calculate_amount();" class="form-control quantity" name="quantity" id="quantity" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Quantity As Dwawing <span class="error">*</span> </label>
                                        <input type="number" class="form-control quantity_as_drawing" name="quantity_as_drawing" id="quantity_as_drawing" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Rate <span class="error">*</span> </label>
                                        <input type="text" onkeyup="calculate_amount();" class="form-control rate" name="rate" id="rate" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Amount <span class="error">*</span> </label>
                                        <input type="text" readonly="" class="form-control amount" name="amount" id="amount" value="" />
                                    </div>
                                </div>
                            </div>
                            <div id="sub_item_part"></div>
                            
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
var item_counter = 0 ; 
    function calculate_amount(){
        var total_amt = parseFloat($('#rate').val()) * parseFloat($('#quantity').val());
        $('#amount').val(total_amt.toFixed(2))
    }

    function calculate_amount_item_add(element){
        var total_amt = parseFloat($('#rate_'+element).val()) * parseFloat($('#quantity_'+element).val());
        $('#amount_'+element).val(total_amt.toFixed(2))
    }
    
    function get_project_by_company() {
        $.ajax({
            url: "{{ route('admin.get_projectlist_by_company') }}",
            type: "POST",
            dataType: "html",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                company_id: $('#company_id').val()
            },
            success: function (data) {
                $('#project_id').html(data);
            }
        });
    }

//remove item
$("body").on('click','.remove_item',function(){
    $(this).parents('.sub_item_add_more').remove();
    var numItems = $('.sub_item_add_more').length
    if(numItems == 0){
        item_counter = 0;
    }
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
                                            '<label>Item Description <span class="error">*</span ></label>'+
                                            '<textarea class="form-control item_description" name="item_description_add['+item_counter+']" id="item_description_'+item_counter+'"></textarea>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>UOM  <span class="error">*</span </label>'+
                                            '<input type="text" class="form-control UOM" name="UOM_add['+item_counter+']" id="UOM_'+item_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Quantity  <span class="error">*</span </label>'+
                                            '<input type="number" onkeyup="calculate_amount_item_add('+item_counter+');" class="form-control quantity" name="quantity_add['+item_counter+']" id="quantity_'+item_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="row">'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Quantity As Dwawing  <span class="error">*</span </label>'+
                                            '<input type="number" class="form-control quantity_as_drawing" name="quantity_as_drawing_add['+item_counter+']" id="quantity_as_drawing_'+item_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Rate  <span class="error">*</span </label>'+
                                            '<input type="text" onkeyup="calculate_amount_item_add('+item_counter+');" class="form-control rate" name="rate_add['+item_counter+']" id="rate_'+item_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Amount  <span class="error">*</span </label>'+
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
            company_id:{
                required: true,
            },
            project_id:{
                required: true,
            },
            
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
            quantity_as_drawing: {
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
