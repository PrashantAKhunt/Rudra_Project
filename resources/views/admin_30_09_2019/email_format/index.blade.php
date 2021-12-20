@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Email Format</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="#">Email Format</a></li>

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
                                        <table class="table table-primary table-bordered" id="ordertbl" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    
                                                    <th>Subject</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            @foreach ($eformat as $i) 
                                            <tr>
                                                <td>{{ $i->title }}</td>
                                                
                                                <td>{{ $i->subject }}</td>
                                                <td><a href="{{ route('admin.editemail',['id'=>$i->id]) }}" class="btn btn-primary btn-rounded"><i class="glyphicon glyphicon-edit"></i></a></td>
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
@endsection
