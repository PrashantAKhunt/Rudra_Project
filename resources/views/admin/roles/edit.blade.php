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
                    <div class="col-sm-12 col-xs-12">
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
                        <div class="m-t-20">
                                    
                            <form action="{{ route('admin.updateroles') }}" method="post" id="rolefrm">
                                @csrf
                                    <input type="hidden" name="role_id" id="role_id" value="<?php echo $roleData[0]['id']; ?>">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">

                                            <h4 class="panel-title">Edit Role </h4>
                                            <!---p>Add Name Add</p-->
                                        </div><!-- panel-heading -->
                                        <div class="panel-body">
                                            
                                            <div class="row"> 

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">Role Name&nbsp;<label class="error">*</label></label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"  name="role_name" id="role_name" value="<?php echo $roleData[0]['role_name']; ?>" style="margin-bottom: 12px;">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="ckbox ckbox-default mt10">
                                                            <input id="select-ALL" type="checkbox" >
                                                            <label for="select-ALL">Select All</label>
                                                        </div>
                                                    </div>
                                                </div><!-- form-group -->

                                                <?php
                                                $Roles = unserialize(config('constants.ROLES'));
                                                $count = count($module);
                                                $step = 2;
                                                $iterators = range(0, $count - 1, $step);
                                                ?>
                                                <?php foreach ($iterators as $index => $iterator) { ?> 
                                                    <div class="col-xs-12">
                                                        <table class="table table-bordered text-center">
                                                            <tr>
                                                                <td>Action</td>
                                                                <td colspan="<?php echo (($index + 1) * $step < $count) ? $step : ($count - ($index * $step)) ?>">Function</td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <?php for ($i = $iterator; $i < ($iterator + $step) && $i < $count; $i++) { ?>
                                                                    <td><?php echo $module[$i]['name']; ?></td>
                                                                <?php } ?>
                                                            </tr>
                                                            <?php foreach ($Roles as $key => $val) { ?>
                                                                <tr class="noBlank">
                                                                    <td class="action"><?php echo $val ?></td>    
                                                                    <?php
                                                                    for ($i = $iterator; $i < ($iterator + $step) && $i < $count; $i++) {
                                                                        $actions_list = explode(',', $module[$i]['action']);
                                                                        ?>
                                                                        <td>
                                                                            <?php
                                                                            if (in_array($key, $actions_list)) {
                                                                                $checked = false;
                                                                                ?>
                                                                                <?php
                                                                                foreach ($role_module as $role_m) {
                                                                                    if ($role_m['module_id'] == $module[$i]['id']) {
                                                                                        $old_action_array = explode(',', $role_m['access_level']);
                                                                                        if (in_array($key, $old_action_array)) {
                                                                                            $checked = true;
                                                                                        }
                                                                                    }
                                                                                }
                                                                                ?>
                                                                                <div class="ckbox ckbox-default checkbox-center">
                                                                                    <input <?php echo ($checked) ? 'checked' : '' ?> id="action_<?php echo $module[$i]['id'] . '_' . $key ?>" type="checkbox" name="permissions[<?php echo $module[$i]['id'] ?>][<?php echo $key ?>]" class="action-checkbox" 
                                                                                                                                     <?php if ($key == 1) { ?>onclick="checkedview('action_<?php echo $module[$i]['id'] . '_' . $key ?>');"<?php } else { ?>
                                                                                                                                         onclick="checkededit('action_<?php echo $module[$i]['id'] . '_' . $key ?>');"<?php } ?>>
                                                                                    <label for="action_<?php echo $module[$i]['id'] . '_' . $key ?>"></label>
                                                                                </div>     
                                                                            <?php } ?>

                                                                        </td>
                                                                    <?php } ?>

                                                                </tr>
                                                            <?php } ?>

                                                        </table>

                                                    </div>
                                                <?php } ?>

                                            </div><!-- row -->
                                        </div><!-- panel-body -->
                                        <div class="panel-footer">
                                            <div class="row">
                                                <div class="col-sm-9 col-sm-offset-3">
                                                    <button type="submit" class="btn btn-info"  >Save</button>
                                                    <button type="reset" class="btn btn-default" onclick="window.location.href = '<?php echo route('admin.roles'); ?>'">Cancel</button>
                                                </div>
                                            </div>
                                        </div><!-- panel-footer -->  
                                    </div><!-- panel -->
                                    </form>
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection


@section('script')
<script type="text/javascript">

    function blankCountry() {
        $("#role_name").val('');
    }
    $(document).ready(function () {

        $('.noBlank').each(function () {
            var child = $(this).find('td');
            var hasAction = false;
            for (var i = 1; i < child.length; i++) {
                if (child.eq(i).children().length > 0) {
                    hasAction = true;
                }
            }
            if (hasAction == false) {
                $(this).hide();
            }
        });
        $(function () {
            $('#select-ALL').change(function () {
                if ($(this).is(':checked')) {
                    $('.action-checkbox').prop('checked', true);
                } else {
                    $('.action-checkbox').prop('checked', false);
                }

            });
            $('.action-checkbox').change(function () {
                if (!$(this).is(':checked')) {
                    $('#select-ALL').prop('checked', false);
                }

            });
            $("#rolefrm").validate({
                rules: {
                    role_name: {
                        required: true,

                    }

                },
                // Specify the validation error messages
                messages: {
                    role_name: {
                        required: "Role name is required.",

                    }

                },
                submitHandler: function (form) {
                    form.submit();
                }
            });

        });
    });

    function checkededit(edit) {
        var id = edit;
        var newid = id.slice(0, -1) + '1';
        //console.log(id);
        //console.log(newid);
        var editid = $('#' + id).is(":checked");
        var viewid = $('#' + newid).is(":checked");

        if (editid == true && viewid == false) {
            $('#' + newid).prop('checked', true);
            $('#' + id).prop('checked', true);
        }

    }
    function checkedview(view) {
        var id = view;
        var newid = id.slice(0, -1) + '2';
        var addid = id.slice(0, -1) + '3';
        var deleteid = id.slice(0, -1) + '4';
        var viewid = $('#' + id).is(":checked");
        // var editid = $('#' + newid).is(":checked");
        if (viewid == false) {
            $('#' + newid).prop('checked', false);
            $('#' + addid).prop('checked', false);
            $('#' + deleteid).prop('checked', false);
            $('#' + id).prop('checked', false);
        }

    }

</script>
@endsection
