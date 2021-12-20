

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="panel-title">Edit Sub Category</h3>
</div>
<div class="modal-body">
    <form method="post" action="{{ route('admin.update_document_sub_categoery') }}" id="update_document_sub_categoery">
        @csrf
        <input type="hidden" name="id" id="id" value="{{ $document_array['sub_category_id'] }}" />
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group "> 
                    <label>Select Categoery <span class="error">*</span> </label>
                     <select name="category_id" id="category_id" class="form-control">
                        <option value="">Select Categoery</option>
                            @foreach($category_list as $list_data)
                            <?php 
                            if($document_array['category_id']==$list_data->id){
                            ?>
                            <option value="{{$list_data->id}}" selected="true">{{$list_data->category_name}} </option>
                            <?php
                            }
                            else {
                            ?>
                            <option value="{{$list_data->id}}">{{$list_data->category_name}} </option>
                            <?php 
                            }
                            ?>
                            @endforeach
                    </select>
                </div>
                <div class="form-group "> 
                    <label>Sub Categoery Name <span class="error">*</span> </label>
                    <input type="input" name="sub_category_name" id="sub_category_name" value="{{ $document_array['sub_category_name'] }}" value="" class="form-control" />
                    <label id="sub_category_name-error" class="error" for="sub_category_name"></label>
                </div>
            
            </div>
            <div class="col-xs-2">

                <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
            </div>
        </div>
    </form>
</div>


<script>
    jQuery("#update_document_sub_categoery").validate({
        ignore: [],
        rules: {
            sub_category_name: {
                required: true,

            },
            category_id: {
                required: true,

            }
        },
        messages: {
            sub_category_name: {
                required: "This field is require."
            },
            category_id: {
                required: "This field is require."
            }
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        }

    });

</script>

