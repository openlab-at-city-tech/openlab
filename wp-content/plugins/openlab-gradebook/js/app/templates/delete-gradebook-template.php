<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="<?php esc_html_e('Close', 'openlab-gradebook') ?>"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Delete Gradebook', 'openlab-gradebook') ?></h4>
        </div>
        <div class="modal-body">
            <form id="delete-gradebook-form" class="form-horizontal">      
                <input type="hidden" name="action" value="delete_gradebook"/>
                <input type="hidden" name="id" value="<%= course ? course.get('id') : '' %>"/> 			        
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', 'openlab-gradebook') ?></button>
            <button type="button" id="delete-gradebook-delete" data-dismiss="modal" class="btn btn-danger"><?php esc_html_e('Delete', 'openlab-gradebook') ?></button>
        </div>
    </div>
</div>