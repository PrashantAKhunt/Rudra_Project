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
    </div>
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
                        <form action="{{ route('admin.download_salary') }}" id="salary" method="post">
                            @csrf
                            <!--<div class="form-group ">
                                <label>User</label>
								@if(!empty($user))
									<select name="user_id" class="select2 form-control user_id">
										@foreach($user as $key => $value)
											<option value="{{$key}}">{{$value}}</option>
										@endforeach
									</select>
								@endif
                            </div>-->

                            <div class="form-group">
                                <label>Year</label>
                                <select class="form-control" class="year" name="year" id="year">
                                    @foreach($year as $value)
                                        <?php if($value <= date('Y')){ ?>
                                            <option value="{{$value}}">{{$value}}</option>    
                                        <?php } ?>
                                    }
                                    @endforeach
                                </select>                                
                            </div>

                            <div class="form-group">
                                <label>Month</label>                                
                                <select class="form-control" class="month" name="month" id="month">
                                    @foreach($months as $value)
                                    <option value="{{$value}}">{{$value}}</option>
                                    @endforeach
                                </select>                                
                            </div>
                            
                            @if(!empty($user_list))
                            <div class="form-group">
                                <label>Select Employee</label>                                
                                <select class="form-control" name="user_id" id="user_id">
                                    @foreach($user_list as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>                                
                            </div>
                            @endif
                            <button type="submit" class="btn btn-success">Submit</button>
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
    $('.select2').select2();
    $('#user_id').select2();
</script>
@endsection
