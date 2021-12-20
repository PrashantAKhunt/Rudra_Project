

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="panel-title">Edit Category</h3>
</div>
<div class="modal-body">
<form method="post" action="{{ route('admin.update_softcopy_document_category') }}" id="edit_category">
        @csrf
    <div class="row">
        <div class="col-xs-12">
           
            <label for="name">Name</label>
           
            <input type="input" name="name" id="name" value="{{ $document_array['name'] }}" class="form-control" />
      
            <input type="hidden" name="id" id="id" value="{{ $document_array['id'] }}" />
            <label id="name-error" class="error" for="name"></label>
        </div>
        <div class="col-xs-2">
            
            <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
        </div>
    </div>

    </form>
</div>

<script>
    jQuery("#edit_category").validate({
        ignore: [],
        rules: {
            name: {
                required: true,
            },
        },
        messages: {
            name: {
                required: "This name is require."
            },
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        }
    });
</script>