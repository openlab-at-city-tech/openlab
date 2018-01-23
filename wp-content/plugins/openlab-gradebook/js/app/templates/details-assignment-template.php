<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="<?php esc_html_e('Close', 'openlab-gradebook') ?>"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Assignment', 'openlab-gradebook') ?></h4>
        </div>
        <div class="modal-body">  
            <dl class="dl-horizontal">
                <dt><?php esc_html_e('Title', 'openlab-gradebook') ?>:</dt> <dd><%= assignment.get('assign_name') %></dt>
                <dt><?php esc_html_e('Date Assigned', 'openlab-gradebook') ?>:</dt> <dd><%= assignment.get('assign_date') %></dt>
                <dt><?php esc_html_e('Date Due', 'openlab-gradebook') ?>:</dt> <dd><%= assignment.get('assign_due') %></dt>
                <dt><?php esc_html_e('Assignment Category', 'openlab-gradebook') ?>:</dt> <dd><%= assignment.get('assign_category') %></dt>
            </dl>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', 'openlab-gradebook') ?></button>
        </div>
    </div>
</div>