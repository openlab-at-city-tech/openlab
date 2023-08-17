<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Elements;
?>
<form id="bookly-table-settings-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Table settings', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row font-weight-bold mb-2">
                    <div class="col-1"></div>
                    <div class="col"><?php esc_html_e( 'Column', 'bookly' ) ?></div>
                    <div class="col text-right mr-2"><?php esc_html_e( 'Show', 'bookly' ) ?></div>
                </div>
                <ul class="list-unstyled bookly-js-table-columns"></ul>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="bookly-table-name" value="">
                <?php Buttons::renderSubmit( null, 'bookly-js-table-settings-save' ) ?>
                <?php Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</form>
<div id="bookly-table-settings-template" class="hidden">
    <li class="mb-1">
        <div class="row">
            <div class="col-1"><?php Elements::renderReorder() ?></div>
            <div class="col-9">{{title}}</div>
            <div class="col-2 text-center">
            <div class="custom-control custom-checkbox">
                <input id="{{id}}" name="{{name}}" type="checkbox" {{checked}} class="custom-control-input" />
                <label for="{{id}}" class="custom-control-label"></label>
            </div>
            </div>
        </div>
    </li>
</div>