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
                        <form action="{{ route('admin.update_softcopy_request') }}" id="edit_softcopy_request" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$request_detail->id}}">
                            <div class="form-group "> 
                                <label>Company</label> 
                                <select class="select2 form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($companies as $companyId => $companyName)
                                        <option value="{{ $companyId }}" <?php echo ($companyId == $request_detail->company_id) ? "selected='selected'" : '' ?> >{{ $companyName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Employee</label>
                                <select class="select2 form-control" name="receiver_user_id" id="receiver_user_id">
                                    <option value="">Select Employee</option>
                                    @foreach($users as $userId =>$userName )                                        
                                        <option value="{{ $userId }}" <?php echo ($userId == $request_detail->receiver_user_id) ? "selected='selected'" : '' ?> >{{ $userName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Type of Document</label>
                                <select class="form-control" name="softcopy_document_category_id" id="softcopy_document_category_id">
                                    <option value="">Select Document</option>
                                    @foreach($documents as $documentId => $documentName )
                                        <option value="{{ $documentId }}" <?php echo ($documentId == $request_detail->softcopy_document_category_id) ? "selected='selected'" : '' ?> >{{ $documentName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Comment</label>
                                <textarea class="form-control" rows="5" name="comment" id="comment" value="{{$request_detail->comment}}" >{{$request_detail->comment}}
                                </textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.softcopy_request_sent') }}'" class="btn btn-default">Cancel</button>
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

        $('#company_id').select2();
        $('#receiver_user_id').select2();
        $('#softcopy_document_category_id').select2();

        removeTextAreaWhiteSpace();
    });
    jQuery("#edit_softcopy_request").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            softcopy_document_category_id: {
                required: true,
            },
            receiver_user_id: {
                required: true,
            },
            comment: {
                required: true,
            }
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });
    function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('comment');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g, '');
    }
</script>
@endsection
