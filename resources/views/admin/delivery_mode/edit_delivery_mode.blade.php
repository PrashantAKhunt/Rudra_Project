

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="panel-title">Edit Delivery</h3>
</div>
<div class="modal-body">
<form method="post" action="{{ route('admin.update_delivery_mode') }}" id="edit_delivery">
        @csrf
    <div class="row">
        <div class="col-xs-12">
           
            <label for="category_name">Delivery Name <span class="error">*</span></label>
           
            <input type="input" name="name" id="name" value="{{ $delivery_mode['name'] }}" class="form-control" />
      
            <input type="hidden" name="id" id="id" value="{{ $delivery_mode['id'] }}" />
            <label id="category_name-error" class="error" for="name"></label>
        </div>
        <div class="col-xs-2">
            
            <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
        </div>
    </div>

    </form>
</div>


<script>
    jQuery("#edit_delivery").validate({
        ignore: [],
        rules: {
            name: {
                required: true,
            },
        },
        messages: {
            name: {
                required: "This field is require."
            },
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        }

    });

</script>

