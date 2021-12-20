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
    </div>
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12">
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
        <div class="white-box">                
            <p class="text-muted m-b-30"></p>
            <br>                
            <div class="table-responsive">
            <form action="{{ route('admin.insert_announcements') }}" id="add_announcements" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Title <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="title" id="title" value="" />
                            </div>
                           
                           <h1 class="btn btn-danger">Still under construction</h1>

                           <div class="form-group">
                                <input type="file" multiple name="filess" id="filess" class="form-control">
                           </div>
                            
                            <div class="form-group ">                                
                                <div class="checkbox checkbox-success pull-right">
                                    <input id="select_all" type="checkbox">
                                    <!-- <label for="select_all">Select All</label> -->
                                </div>
                                <label>Users <span class="error">*</span> </label>
                                @if(!empty($user))
                                <select name="user_id[]" id="user_id" class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose">
                                    @foreach($user as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach								
                                </select>									
                                @endif
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.documentsignature') }}'" class="btn btn-default">Cancel</button>
                        </form>
                <!-- <table id="announcements_table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Edit</th>
							<th>Delete</th>
						</tr>
                    </thead>
                    <tbody>                            
                    </tbody>
                </table> -->
            </div>
        </div>            
    </div>    
@endsection

@section('script')		
<script>
    var __dayDiff = 0;
    jQuery('#add_announcements').validate({
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
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(24, 'hour'),
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