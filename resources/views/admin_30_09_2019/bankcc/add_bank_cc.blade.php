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
                        <form action="{{ route('admin.insert_bankcc') }}" id="add_bankcc_frm" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Select Company</label> 
                                <select class="form-control" onchange="get_bank_list()" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($company_list as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group "> 
                                <label>Select Bank</label> 
                                <select class="form-control" name="bank_id" id="bank_id">
                                    <option value="">Select Bank</option>
                                    @foreach($company_list as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
 
                            <div class="form-group ">
                                <label>Bank Detail</label>
                                <textarea class="form-control" rows="10" name="detail" id="detail" ></textarea>
                            </div> 
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.banks') }}'" class="btn btn-default">Cancel</button>
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
   function get_bank_list(){
       $.ajax({
          url:"{{ route('admin.get_bank_by_company') }}",
          type:"post",
          dataType:"html",
          headers:{
              'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
          },
         
       });
   }
    jQuery("#add_bankcc_frm").validate({
        ignore: [],
        rules: {
            bank_name: {
                required: true,
            },
            detail:{
                required: true,
            },
            company_id:{
                required: true,
            }
        }
    });
      
</script>
@endsection
