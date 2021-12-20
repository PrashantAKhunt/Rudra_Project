

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="panel-title">Edit Settings</h3>
</div>
<div class="modal-body">
<form method="post" action="{{ route('admin.updatesetting') }}" id="edit_setting">
        @csrf
    <div class="row">
        <div class="col-xs-12">
           
            <label for="setting_value">Setting Value</label>
           
            <input type="input" name="setting_value" id="setting_value" value="{{ $settings_array['setting_value'] }}" class="form-control" />
      
            <input type="hidden" name="id" id="id" value="{{ $settings_array['id'] }}" />
            <label id="setting_value-error" class="error" for="setting_value"></label>
        </div>
        <div class="col-xs-2">
            
            <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
        </div>
    </div>

    </form>
</div>


<script>
    jQuery("#edit_setting").validate({
        ignore: [],
        rules: {
            setting_value: {
                required: true,

            },
        },
        messages: {
            setting_value: {
                required: "This field is require."
            },
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        }

    });

</script>

