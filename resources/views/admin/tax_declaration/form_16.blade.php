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
            <form action="{{ route('admin.update_employee_salary') }}" id="edit_employee_salary" method="post">
                @csrf
               <div class="form-group "> 
                    @if(!empty($employee))
                    <select name="user_id" class="form-control" id="user_id">
                        <option value="">Select Employee</option>
                        @foreach($employee as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
                <button type="button" id="genrate_form" class="btn btn-success" data-toggle="modal" data-target="#myModal">Generate</button>
                </form>
            </div>
        </div>
    </div>

    <!-- sample modal content -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Form 16</h4>
                </div>
                <div class="modal-body">
                     <table id="user_policyTable" class="table table-striped">
                      </table>

                   <!--  <form action="{{ route('admin.update') }}" id="update_employee_loan" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id"/>                            
                         <div class="form-group "> 
                            <label>Section Name</label> 
                            <input type="text" class="form-control" name="section_name" id="section_name" readonly="" />
                        </div>
                         <div class="form-group "> 
                            <label>Deduction Name</label> 
                            <input type="text" class="form-control" name="deduction_name" id="deduction_name" readonly="" />
                        </div>
                        <div class="form-group "> 
                            <label>Declaration</label> 
                            <input type="text" class="form-control" name="declaration" id="declaration"/>
                        </div>
                        
                        <!-- <div class="form-group "> 
                            <label>Proofs</label> 
                            <input type="text" class="form-control" name="proofs" id="proofs"/>
                        </div> 
                        <div class="form-group ">
                            <label>Proofs</label>
                            <input type="file" accept="image/x-png, image/jpg, image/jpeg" name="proofs" id="proofs" class="dropify" />
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>-->
                    </form>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>   
</div>
@endsection


@section('script')
<script>
 $( "#genrate_form" ).click(function() {
    $.ajaxSetup({
        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let user_id = $("#user_id").val();
    var html = ''; 
    $.ajax({
           type:'POSt',
           url: "<?php echo route('admin.get_user_form'); ?>",
           data:{user_id:user_id},
           success:function(response) {
             $.each(JSON.parse(response), function(k, v) {
                html+='<tr>'
                        +'<td>'
                        +v.deduction_name
                        +'</td>'
                        +'</tr>'
                 html+='</tbody>'
                $('#user_policyTable').append(html);
            }); 
        }
    });
});
</script>
@endsection