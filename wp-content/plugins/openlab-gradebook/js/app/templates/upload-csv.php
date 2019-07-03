<div class="modal-dialog" id="modalDialogUploadCSV">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
                aria-label="Close<?php esc_html_e('Close', 'openlab-gradebook')?>"><span
                    aria-hidden="true">&times;</span></button>

            <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Upload CSV', 'openlab-gradebook')?></h4>

        </div>
        <div class="modal-body">
            <input id="upload-csv-input" type="file" class="file" name="upload-csv"
                data-allowed-file-extensions='["csv"]'>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default"
                data-dismiss="modal"><?php esc_html_e('Close', 'openlab-gradebook')?></button>
        </div>
    </div>
</div>