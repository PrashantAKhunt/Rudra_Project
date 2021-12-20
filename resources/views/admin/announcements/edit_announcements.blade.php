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
                        <form action="{{ route('admin.update_announcements') }}" id="edit_announcements" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $announcements_detail[0]->id }}" />
                            @csrf

                            <div class="form-group ">
                                <label>Title <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="title" id="title" value="{{ $announcements_detail[0]->title }}" />
                            </div>

                            <div class="form-group ">
                                <label>Description <span class="error">*</span> </label>
                                <textarea class="form-control" name="description" id="description"> {{ $announcements_detail[0]->description }} </textarea>
                            </div>
                            
                            <div class="form-group ">
                                <label>Announcement Schedule <span class="error">*</span> </label>
                                <div class="" id="date-range">
                                    <input type="text" class="form-control"  name="date_range" id="date_range" value="{{ date('d/m/Y H:i',strtotime($announcements_detail[0]->start_date)).' - '.date('d/m/Y H:i',strtotime($announcements_detail[0]->end_date)) }}" readonly="true" />

                                </div>
                            </div> 
                            
                            						
                            <?php $userIds = explode(",", $announcements_detail[0]->user_id); ?>
                            <div class="form-group ">
                                <div class="checkbox checkbox-success pull-right">
                                    <input id="select_all" type="checkbox">
                                    <label for="select_all">Select All</label>
                                </div>
                                <label>Users <span class="error">*</span> </label>
                                @if(!empty($user))
                                <select name="user_id[]" id="user_id" class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose">
                                    @foreach($user as $key => $value)
                                    <option <?php if (in_array($key, $userIds)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                    @endforeach								
                                </select>
                                @endif
                            </div>							
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.announcements') }}'" class="btn btn-default">Cancel</button>
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
    var __dayDiff = 0;
    jQuery('#edit_announcements').validate({
        ignore: [],
        rules: {
            title: {
                required: true,
            },
            description: {
                required: true
            },
            date_range: {
                required: true,
            },
           
            'user_id[]': {
                required: true,
            }
        }
    });

    $('.select2').select2();
$('#date_range').daterangepicker({
        timePicker: true,
        
        timePicker24Hour: true,
        locale: {
            format: 'DD/MM/YYYY HH:mm '
        },
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-danger',
        cancelClass: 'btn-inverse'
    });

    $(document).ready(function () {
        $('#select_all').click(function () {
            if ($(this).prop("checked") == true) {
                $('#user_id').select2('destroy');
                $('#user_id option').prop('selected', true);
                $('#user_id').select2();
            } else {
                $('#user_id').select2('destroy');
                $('#user_id option').prop('selected', false);
                $('#user_id').select2();
            }
        });
    });
</script>
@endsection
