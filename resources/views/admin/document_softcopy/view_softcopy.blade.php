<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="panel-title">Hardcopy files details</h3>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">            
            <div class="panel panel-default">                
                <div class="panel-wrapper collapse in">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>File Name</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($softcopyDetails)){
                                    foreach($softcopyDetails as $key => $value){ ?>
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $value['file_name'] }}</td>
                                    <td><a title="Download File" download href="<?php echo asset('storage/' . str_replace('public/', '', $value['file_path']));?>" ><i class="fa fa-cloud-download fa-lg"></i></a></td>
                                </tr>
                            <?php }
                                } else { ?>
                                <tr>
                                    <td colspan="3">No Record Found!</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>