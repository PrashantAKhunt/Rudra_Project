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
                        <form action="{{ route('admin.update_company_document_request') }}" id="edit_company_document_request" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$request_detail->id}}">
                            <div class="form-group "> 
                                <label>Company</label> 
                                <select class="select2 form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company)
                                    <option value="{{ $company->id }}" <?php echo ($company->id == $request_detail->company_id) ? "selected='selected'" : '' ?> >{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>                            
                            <div class="form-group "> 
                                <label>Select Document</label>
                                <select class="form-control" name="document_id" id="document_id">
                                    <option value="">Select Document</option>
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Work Detail</label>
                                <textarea class="form-control" rows="5" name="work_detail" id="work_detail" value="{{$request_detail->work_detail}}" >{{$request_detail->work_detail}}
                                </textarea>
                            </div>
                            <div class="form-group "> 
                                <label>Require Date</label> 
                                <input type="text" class="form-control" name="require_date" id="require_date" value="{{$request_detail->require_date}}" />
                            </div>
                            <div class="form-group "> 
                                <label>Return Date</label> 
                                <input type="text" class="form-control" name="return_date" id="return_date" value="{{$request_detail->return_date}}" />
                            </div>                            
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.company_document_request') }}'" class="btn btn-default">Cancel</button>
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

        $('#company_id').select2();
        $('#document_id').select2();

        removeTextAreaWhiteSpace();

        jQuery('#require_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        jQuery('#return_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        $("#company_id").change(function () {
            $.ajax({
                url: "{{ route('admin.get_company_document_management')}}",
                type: 'get',
                data: "company_id=" + $(this).val(),
                success: function (data, textStatus, jQxhr) {                    
                    $('#document_id').html(data);
                    $('#document_id').select2('destroy');
                    $('#document_id').val('<?php echo $request_detail->document_id; ?>');
                    $('#document_id').select2();
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        });

        $("#company_id").trigger('change');

    });
    jQuery("#edit_company_document_request").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            document_id: {
                required: true,
            },
            require_date: {
                required: true,
            },
            return_date: {
                required: true,
            },
            work_detail: {
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
    function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('work_detail');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g, '');
    }
</script>
@endsection
