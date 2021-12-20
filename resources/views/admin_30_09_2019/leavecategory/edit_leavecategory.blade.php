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
                            <form action="{{ route('admin.update_leavecategory') }}" id="edit_bank" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $category_detail[0]->id }}" /> 
                            @csrf
                             
                             <div class="form-group "> 
                                <label>Name</label> 
                                <input type="text" class="form-control" name="name" id="name" value="{{ $category_detail[0]->name }}" /> 
                            </div>

                            <div class="form-group "> 
                                <label>Frequency</label> 
                                 
                                    <select name="frequency" class="form-control" >
                                     <option <?php if($category_detail[0]->frequency == "Yearly" ) { ?> selected <?php } ?> value="Yearly">Yearly</option> 
									 <option <?php if($category_detail[0]->frequency == "Monthly" ) { ?> selected <?php } ?>  value="Monthly">Monthly</option> 
                                    </select>
                                
                            </div>
 
                            <div class="form-group ">
                                <label>Quantity</label>
                               <input type="text" class="form-control" name="quantity" id="quantity" value="{{$category_detail[0]->quantity }}" /> 
                            </div> 
                           
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.leavecategory') }}'" class="btn btn-default">Cancel</button>
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
  
   
    jQuery("#edit_bank").validate({
        ignore: [],
        rules: {
            bank_name: {
                required: true,
            },
            company_id: {
                required: true,
            },
            detail: {
                required: true,
            },
            
        }
    });
</script>
@endsection
