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
                            <form action="{{ route('admin.update_holiday') }}" id="edit_holiday" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $holiday_detail[0]->id }}" />
                            @csrf
                             
                             <div class="form-group ">
								<label>Title <span class="text-danger">*</span></strong> </label>
                                <input type="text" class="form-control" name="title" id="title" value="{{ $holiday_detail[0]->title }}" />
                            </div>
							
							<div class="form-group ">
                                <label></label>
								<div class="input-daterange input-group" id="date-range">
									<input type="text" class="form-control"  name="start_date" id="start_date" value="{{ $holiday_detail[0]->start_date }}" readonly="true" />
									<span class="input-group-addon bg-info b-0 text-white">to</span>
									<input type="text" class="form-control" name="end_date" id="end_date" value="{{ $holiday_detail[0]->end_date }}" readonly="true" />
								</div>
							</div>							
							
                            <div class="form-group">
                                <label>Year <span class="text-danger">*</span></strong> </label>
                                <select class="form-control select2" class="year" name="year" id="year">
                                    @foreach($year_list as $key => $value)
                                        <option value="{{ $value }}" <?php if($holiday_detail[0]->year == $value){ ?> selected <?php } ?> > {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">                                
                                <div class="checkbox checkbox-success">
                                    <input id="is_optional" name="is_optional" type="checkbox" class="form-control" <?php if($holiday_detail[0]->is_optional == 2){ ?> checked <?php } ?>>
                                    <label for="is_optional">Is Optional</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.holiday') }}'" class="btn btn-default">Cancel</button>
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
    jQuery('#add_holiday').validate({
        ignore: [],
        rules: {
            title: {
                required: true,
            },
            start_date:{
                required: true
            },
            end_date:{
                required: true
            },
            year: {
                required: true,
            }
        }
    });
    
    $('.select2').select2();
       
    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'yyyy-mm-dd'
    });

</script>
@endsection
