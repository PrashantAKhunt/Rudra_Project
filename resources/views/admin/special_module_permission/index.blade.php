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
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">

                <div class="row">
                    <div class="col-sm-10 col-xs-10">
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
                        <form action="{{ route('admin.save_special_permission') }}" id="add_special_permission" method="post">
                            @csrf

                           <!--  -->
                           <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Bank Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="bank[]" multiple="multiple" id="bank" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $bank_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Bank charge Category Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="bank_category[]" multiple="multiple" id="bank_category" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $bank_category_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Bank charge sub-category Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="bank_sub_category[]" multiple="multiple" id="bank_sub_category" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $bank_sub_category_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Client Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="client[]" multiple="multiple" id="client" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $client_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Company Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="company[]" multiple="multiple" id="company" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $company_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Company Document Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="company_document[]" multiple="multiple" id="company_document" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $company_document_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Project Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="project[]" multiple="multiple" id="project" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $project_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Project Site Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="project_site[]" multiple="multiple" id="project_site" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $project_site_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Payment Card Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="payment_card[]" multiple="multiple" id="payment_card" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $payment_card_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Registry Category Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="registry_category[]" multiple="multiple" id="registry_category" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $registry_category_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Registry Sub-Category Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="registry_sub_category[]" multiple="multiple" id="registry_sub_category" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $registry_sub_category_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Registry Delivery Mode Category Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="delivery_mode[]" multiple="multiple" id="delivery_mode" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $delivery_mode_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Registry Sender Category Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="sender_category[]" multiple="multiple" id="sender_category" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $sender_category_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>TDS Section Type Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="tds_section_type[]" multiple="multiple" id="tds_section_type" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $tds_section_type_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Tender Category Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="tender_category[]" multiple="multiple" id="tender_category" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $tender_category_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Tender Pattern Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="tender_pattern[]" multiple="multiple" id="tender_pattern" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $tender_pattern_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Tender Physical Submission Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="tender_submission[]" multiple="multiple" id="tender_submission" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $tender_submission_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Vendor Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="vendor[]" multiple="multiple" id="vendor" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $vendor_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Vendor Bank Module <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 select2-multiple" name="vendor_bank[]" multiple="multiple" id="vendor_bank" >

                                                @foreach($users as $key => $value)
                                                <option <?php if (in_array($key, $vendor_bank_users)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <button type="submit" class="btn btn-success">Save</button>

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
    jQuery('#add_special_permission').validate({
        ignore: [],
        rules: {
            'company[]': {
                required: true,
            },
            'vendor[]': {
                required: true,
            },
            'project[]': {
                required: true,
            },
            'client[]': {
                required: true,
            },
            'vendor_bank[]': {
                required: true,
            },
            'project_site[]': {
                required: true,
            },
            'bank[]': {
                required: true,
            },
            'bank_category[]': {
                required: true,
            },
            'bank_sub_category[]': {
                required: true,
            },
            'payment_card[]': {
                required: true,
            },
            'company_document[]': {
                required: true,
            },
            'tender_category[]': {
                required: true,
            },
            'tender_pattern[]': {
                required: true,
            },
            'tender_submission[]': {
                required: true,
            },
            'registry_category[]': {
                required: true,
            },
            'registry_sub_category[]': {
                required: true,
            },
            'delivery_mode[]': {
                required: true,
            },
            'sender_category[]': {
                required: true,
            },
            'tds_section_type[]': {
                required: true,
            }
        }
    });

    $('.select2').select2();


</script>
@endsection
