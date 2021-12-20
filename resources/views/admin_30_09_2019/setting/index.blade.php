@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Settings</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="#">Settings</a></li>

            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">

            <div class="white-box">
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
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="m-t-20">
                            <div class="table-responsive" style="overflow-x:auto;">
                                <table class="table table-striped table-bordered" id="setting_table" >
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Value</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    @foreach ($settings as $i) 
                                    <tr>
                                        <td>{{ $i->setting_name }}</td>
                                        <td>{{ $i->setting_value }}</td>
                                        <td><a href="#myModal" title="Edit" id="edit_btn" onclick="edit_setting('{{ route('admin.editsetting',['id'=>$i->id]) }}');" data-toggle="modal" class="btn btn-primary btn-rounded"> <i class="glyphicon glyphicon-edit"></i></a></td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--row -->

</div>
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content" id="model_data">

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function edit_setting(route)
    {

    
    $('#model_data').html('');
    $.ajax({
    url: route,
            type: "GET",
            dataType: "html",
            catch : false,
            success: function (data) {
            $('#model_data').append(data);
            }
    });
    }
</script>
@endsection
