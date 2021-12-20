

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="panel-title">Edit Category</h3>
</div>
<div class="modal-body">
<form method="post" action="{{ route('admin.update_document') }}" id="edit_category">
        @csrf
    <div class="row">
        <div class="col-xs-12">
           
            <label for="category_name">Category Name <span class="error">*</span> </label>
           
            <input type="input" name="category_name" id="category_name" value="{{ $document_array['category_name'] }}" class="form-control" />
      
            <input type="hidden" name="id" id="id" value="{{ $document_array['id'] }}" />
            <label id="category_name-error" class="error" for="category_name"></label>
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
            category_name: {
                required: true,

            },
        },
        messages: {
            category_name: {
                required: "This field is require."
            },
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        }

    });

</script>

