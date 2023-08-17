<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Elements;
?>
<form id="bookly-service-order-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Services order', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <ul id="bookly-list" class="list-unstyled"></ul>
                <small class="text-muted form-text"><?php esc_html_e( 'Adjust the order of services in your booking form', 'bookly' ) ?></small>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderSubmit() ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</form>
<div class="bookly-collapse" id="bookly-service-template">
    <li class="mb-1">
        <div class="row align-items-center">
            <input type="hidden" name="id" value="{{id}}"/>
            <div class="col-auto"><?php Elements::renderReorder() ?></div>
            <div class="col px-1 text-truncate">{{title}}</div>
        </div>
    </li>
</div>