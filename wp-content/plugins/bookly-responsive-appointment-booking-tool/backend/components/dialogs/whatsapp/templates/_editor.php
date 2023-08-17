<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="form-group"><label for="bookly-notification-templates" name=""><?php esc_html_e( 'Template', 'bookly' ) ?></label>
            <select class="form-control custom-select" name="notification[message]" id="bookly-js-templates"></select>
        </div>
        <div class="form-group"><label for="bookly-js-notification-subject"><?php esc_html_e( 'Header', 'bookly' ) ?></label>
            <input class="form-control" id="bookly-js-notification-subject" readonly></input>
        </div>
    </div>
</div>
<div id="bookly-js-notification-subject-variables" class="mr-1 border-left ml-4 pl-3">
    <div class="row">
        <div class="col">
            <label><?php esc_html_e( 'Variable', 'bookly' ) ?></label>
        </div>
    </div>
    <div class="bookly-js-variables-list"></div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group"><label for="bookly-js-notification-body"><?php esc_html_e( 'Body', 'bookly' ) ?></label>
            <textarea class="form-control" rows="3" id="bookly-js-notification-body" readonly></textarea>
        </div>
    </div>
</div>
<div id="bookly-js-notification-body-variables" class="mb-3 border-left ml-4 pl-3">
    <div class="row">
        <div class="col">
            <label><?php esc_html_e( 'Variable', 'bookly' ) ?></label>
        </div>
    </div>
    <div class="bookly-js-variables-list"></div>
</div>