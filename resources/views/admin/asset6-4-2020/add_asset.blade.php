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
                            <form action="{{ route('admin.insert_asset') }}" id="add_asset" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id"/> 

                            <div class="form-group "> 
                                <label>Company</label> 
                                <select id='company_id' name='company_id' class="form-control">
                                    <option value="">Select Company</option>
                                    <?php
                                     foreach ($companies as $key => $companiesvalue) {
                                        echo "<option value=".$companiesvalue['id'].">".$companiesvalue['company_name']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Asset Type</label> 
                                <select id='asset_type' name='asset_type' class="form-control">
                                    <option value="">Select Asset Type</option>
                                    <option value="General Asset">General Asset</option>
                                    <option value="Vehicle Asset">Vehicle Asset</option>
                                </select>
                            </div>

                            <div class="form-group" style="display: none;" id="fual_div"> 
                                <label>Fual Type</label> 
                                <select id='fuel_type' name='fuel_type' class="form-control">
                                    <option value="">Select Fual Type</option>
                                    <option value="CNG">CNG</option>
                                    <option value="Petrol">Petrol</option>
                                    <option value="Diesel">Diesel</option>
                                </select>
                            </div>

                             <div class="form-group "> 
                                <label>Asset Name</label> 
                                <input type="text" class="form-control" name="name" id="name"/>
                            </div>
                            <div class="form-group "> 
                                <label>Department Descption</label> 
                                <textarea class="form-control" rows="5" name="description" id="description">
                                </textarea>
                            </div>
                            <div class="form-group "> 
                                <label>Asset Number (Mobile/Vehicle)</label> 
                                <input type="text" class="form-control" name="asset_1" id="asset_1"/>
                            </div>
                            <div class="form-group "> 
                                <label>Asset Number (Imie/Chassis)</label> 
                                <input type="text" class="form-control" name="asset_2" id="asset_2"/>
                            </div>
                            <div class="form-group ">
                                <label>Asset Image</label>
                                <input type="file" accept="image/png,image/x-png, image/jpg, image/jpeg" name="image[]" id="image" class="dropify" multiple/>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.asset') }}'" class="btn btn-default">Cancel</button>
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
   $(document).ready(function () {
        removeTextAreaWhiteSpace();

         $("#asset_type").change(function() {
            var txt = $('option:selected', this).text();

            if(txt=="Vehicle Asset") {
                $("#fual_div").show();
                $('#fuel_type').prop('selectedIndex',0);
            }
            else {
                $("#fual_div").hide();
            }

         });

    });
    jQuery("#add_asset").validate({
        ignore: [],
        rules: {
            name: {
                required: true,
            },
            company_id: {
                required: true,
            },
            asset_type: {
                required: true,
            },
            description: {
                required: true,
            },
			asset_1:{
				required:true
			},
			asset_2:{
				required:true
			},
			'image[]':{
				required:true
			}
        }
    });
     function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('description');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }
</script>
@endsection
