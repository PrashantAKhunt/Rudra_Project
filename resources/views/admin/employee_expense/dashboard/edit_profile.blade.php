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
                        <form class="" method="POST" id="edit_profile_frm" action="{{ route('admin.update_profile') }}" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="form-group">
                                <label>Email</label>
                                <div> 
                                    <input type="text" readonly="" name="email" class="form-control"  value="{{ Auth::user()->email }}" placeholder="Email" />
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <div> 
                                    <input type="text" name="name" id="name" class="form-control"   placeholder="Name" value="{{ Auth::user()->name }}"/>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Mobile</label>
                                <div> 
                                    <input type="text" name="mobile" class="form-control"   placeholder="Mobile" value="{{ Auth::user()->mobile }}"/>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <div> 
                                    <textarea name="address" class="form-control" id="address" placeholder="Address">{{ Auth::user()->address }}</textarea>
                                    
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Profile image <span class="text-muted">Allowed file extensions are png, jpg, jpeg</span></label>
                                <div> 
                                    <input type="file" name="profile_image" class="form-control" id="profile_image" />
                                </div>
                            </div>
                            
                                <div> 
                                    <button type="submit" class="btn btn-primary waves-effect waves-light"> Submit </button> 
                                    <!--<button type="reset" class="btn btn-default waves-effect m-l-5"> Cancel </button></div>-->
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
    $("#edit_profile_frm").validate({
        ignore: [],
        rules: {
            name: {
                required: true,

            },
            mobile: {
                required: true,
                
            },
            address: {
                required: true,
            },
            profile_image:{
               
               extension: "jpg|jpeg|png"
            }
        },
        

    });
    
</script>
@endsection
