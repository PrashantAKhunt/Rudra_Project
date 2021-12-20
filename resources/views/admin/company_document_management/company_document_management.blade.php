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
         <?php if($company_doc_add_permission){ ?>
            @if($view_special_permission)
            <a href="{{ route('admin.add_company_document_management') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Company Document</a>
            @endif
         <?php } ?>
         <p class="text-muted m-b-30"></p>
         <br>
         <div class="table-responsive">
            <table id="company_document" class="table table-striped">
               <thead>
                  <tr>
                     <th>Company Name</th>
                     <th>Document Title</th>
                     <th>Document Description</th>
                     <th>Custodian</th>
                     <th>Download</th>
                     <th>Uploaded Date</th>
                     <th>Status</th>
                     <th>Actions</th>
                  </tr>
               </thead>
               <tbody>
                <?php 
                if(!empty($company_document_management))
                {
                    foreach ($company_document_management as $key => $company_document_value) { ?>
                  <tr>
                     <td>{{$company_document_value->company_name}}</td>
                     <td>{{$company_document_value->title}}</td>
                     <td>{{$company_document_value->description}}</td>                     
                     <td>{{$company_document_value->name}}</td>
                     <td><a title="Download File" download href=<?php echo asset('storage/' . str_replace('public/', '', $company_document_value->file));?> ><i class="fa fa-cloud-download fa-lg"></i></a></td>
                     <td>{{ date('d-m-Y H:i:s', strtotime($company_document_value->created_at)) }}</td>
                     <td>{{$company_document_value->status}}</td>
                    
                        
                        <td>
                        <?php if($company_doc_edit_permission){ ?>
                        @if($view_special_permission)
                        <a href="<?php echo url('/edit_company_document_management').'/'.$company_document_value->id; ?>" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>
                        @endif
                        <?php } ?>
                        </td>
                        
                     
                  </tr>
                <?php } } ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
@endsection
@section('script')
<script>   
   $(document).ready(function () {
      $('#company_document').DataTable({
         stateSave: true,
      });
   });   
</script>
@endsection