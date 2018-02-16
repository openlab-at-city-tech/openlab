<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="<?php esc_html_e('Close', 'openlab-gradebook') ?>"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Assignment Statistics', 'openlab-gradebook') ?>: <%= assignment.get('assign_name') %></h4>
        </div>
        <div class="modal-body">
            <div class="labeled-chart-container">
                <canvas id = "myChart"></canvas>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', 'openlab-gradebook') ?></button>
        </div>
    </div>
</div>