<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="form-group">
            <label for="bookly-js-notification-subject"><?php esc_attr_e( 'Subject', 'bookly' ) ?></label>
            <input type="text" class="form-control" id="bookly-js-notification-subject" name="notification[subject]">
            <input type="hidden" name="notification[gateway]" value="email"/>
        </div>
    </div>
</div>