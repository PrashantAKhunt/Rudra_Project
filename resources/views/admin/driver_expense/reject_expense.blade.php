<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="panel-title">Reject Driver Expense</h3>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">                
                <div class="panel-wrapper collapse in">                    
                    <form action="{{ route('admin.reject_update_expense') }}" id="add_expense" method="post">
                        @csrf                        
                            <input type="hidden" id="id" name="id" value="{{ $id }}" />
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Reject Reason</label>
                                    <textarea name="reject_reason" id="reject_reason" class="form-control" ></textarea>
                                </div>
                            </div>                                    
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.all_expense') }}'" class="btn btn-default">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>