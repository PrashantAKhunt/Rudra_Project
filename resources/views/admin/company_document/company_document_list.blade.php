@extends('layouts.admin_app')
@section('content')
<div class="container-fluid">
<div class="row bg-title">
   <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
      <h4 class="page-title">{{ $page_title }}</h4>
   </div>
   <div class="col-lg-7 col-sm-8 col-md-7 col-xs-12">
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
        <form action="{{ route('admin.company_document_list') }}" method="POST" id="company_document_frm">
        @csrf
        <input type="hidden" name="document_type" id="document_type" value="<?php echo $document_type;?>">
        <div class="row">
        <div class="form-group col-md-6"> 
            <label>Select Company</label>
            <select class="form-control" name="company_id" id="company_id">
                <option value="">Select Company</option>
                @foreach($Companies as $company_list_data)
                <option value="{{ $company_list_data->id }}" <?php echo ($company_id == $company_list_data->id) ? "selected='selected'" : '' ?> >{{ $company_list_data->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            <label>Select Project</label>
            <select class="form-control" id="project_id" name="project_id">
            <option value="">Select Project</option>
             @foreach($Projects as $project_list_data)
             @if($project_list_data->project_name != "Other Project")
               <option value="{{ $project_list_data->id }}" <?php echo ($project_id == $project_list_data->id) ? "selected='selected'" : '' ?> >{{ $project_list_data->project_name }}</option>
             @endif
            @endforeach
        </select>
        </div>
        </div>
        </form>
 
         <div class="white-box">
            <ul class="nav customtab nav-tabs" role="tablist" style="width: 980px;margin-left: -30px; font-size: 14px;">
               <li role="presentation" class=<?php echo ($document_type=='Tender')?"active":""; ?> ><a onclick="document_form_submit('Tender')" href="#tender" aria-controls="tender" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-user"></i></span><span class="hidden-xs"> Tender</span></a></li>
               <li role="presentation" class=<?php echo ($document_type=='BOQ')?"active":""; ?> ><a onclick="document_form_submit('BOQ')" href="#BOQ" aria-controls="BOQ" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-email"></i></span> <span class="hidden-xs">BOQ</span></a></li>
               <li role="presentation" class=<?php echo ($document_type=='Specifications')?"active":""; ?> ><a onclick="document_form_submit('Specifications')" href="#Specifications" aria-controls="Specifications" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-receipt"></i></span> <span class="hidden-xs">Specifications</span></a></li>
               <li role="presentation" class=<?php echo ($document_type=='Drawings')?"active":""; ?> ><a onclick="document_form_submit('Drawings')" href="#Drawings" aria-controls="Drawings" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-book"></i></span> <span class="hidden-xs">Drawings</span></a></li>
               <li role="presentation" class=<?php echo ($document_type=='MOM')?"active":""; ?> ><a onclick="document_form_submit('MOM')" href="#MOM" aria-controls="MOM" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-briefcase"></i></span> <span class="hidden-xs">MOM</span></a></li>
               <li role="presentation" class=<?php echo ($document_type=='Correspondence')?"active":""; ?> ><a onclick="document_form_submit('Correspondence')" href="#Correspondence" aria-controls="Correspondence" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-briefcase"></i></span> <span class="hidden-xs">Correspondence</span></a></li>
               <li role="presentation" class=<?php echo ($document_type=='RABills+Final Bills')?"active":""; ?> ><a onclick="document_form_submit('RABills+Final Bills')" href="#Bills" aria-controls="Bills" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-briefcase"></i></span> <span class="hidden-xs">RA Bills+ Final Bills</span></a></li>
               <li role="presentation" class=<?php echo ($document_type=='PaymentsAdvice')?"active":""; ?> ><a onclick="document_form_submit('PaymentsAdvice')" href="#Payments" aria-controls="Payments" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-briefcase"></i></span> <span class="hidden-xs">Payments Advice</span></a></li>
               <li role="presentation" class=<?php echo ($document_type=='Photos')?"active":""; ?> ><a onclick="document_form_submit('Photos')" href="#Photo" aria-controls="Photo" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-briefcase"></i></span> <span class="hidden-xs">Photo</span></a></li>
            </ul>
            <div class="tab-content">
            <?php
            $role = explode(',', $access_rule);
            if (in_array(3, $role)) {
            ?>
             <form action="{{ route('admin.add_company_document_list') }}" method="POST" id="add_company_document_frm">
                @csrf
                <input type="hidden" name="document_type" id="document_type" value="<?php echo $document_type;?>">
                <input type="submit" name="submit" id="add_btn" value="Add <?php echo $document_type;?> Document" class="btn btn-primary pull-right">
            </form>
            <?php } ?>
            <br>
            </div>
         </div>
         <div class="table-responsive">
            <table id="document_payment" class="table table-striped">
               <thead>
                  <tr>
                     <th>Company Name</th>
                     <th>Project Name</th>
                     <th>Document Type</th>
                     <th>Document Title</th>
                     <th>Document Description</th>
                     <th>Download</th>
                     <th>Uploaded Date</th>
                     <th>Actions</th>
                  </tr>
               </thead>
               <tbody>
                <?php 
                if(!empty($company_document_list))
                {
                    foreach ($company_document_list as $key => $company_document_list_value) {
                ?>
                <tr>
                <td>{{$company_document_list_value->company_name}}</td>
                <td>{{$company_document_list_value->project_name}}</td>
                <td>{{$company_document_list_value->document_type}}</td>
                <td>{{$company_document_list_value->doc_title}}</td>
                <td>{{$company_document_list_value->doc_detail}}</td>
                <td><a title="Download File" download href=<?php echo asset('storage/' . str_replace('public/', '', $company_document_list_value->document_file));?> ><i class="fa fa-cloud-download fa-lg"></i></a></td>
                <td>{{$company_document_list_value->created_at}}</td>
                <td><a href="<?php echo url('/edit_company_document').'/'.$company_document_list_value->id; ?>" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a></td>
                </tr>
                <?php 
                }
                }
                ?>
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
   
   var access_rule = '<?php echo $access_rule; ?>';
   access_rule = access_rule.split(',');
   
   $('#document_payment').DataTable({
    stateSave: true,
   });

    $("#company_id").change(function () {
        var company_id = $("#company_id").val();
        if (company_id.length >= 1)
        {
            $.ajax({
                url: "{{ route('admin.get_cash_project_list')}}",
                type: 'get',
                data: "company_id=" + company_id,
                success: function (data, textStatus, jQxhr) {
                    $('#project_id').empty();
                    $('#project_id').append(data);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });  
        }

        $('#company_document_frm').submit();
    });
    
    $("#project_id").change(function () {
       
       $('#company_document_frm').submit();
        
    });

   });
   
   function document_form_submit(doc_type)
   {
        $('#document_type').val(doc_type);
        $('#add_btn').val('Add '+doc_type+' Document');
        $('#company_document_frm').submit();
   }

</script>
@endsection