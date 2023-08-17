<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Ace;
?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="form-group"><label for="bookly-js-message"><?php esc_html_e( 'Body', 'bookly' ) ?></label>
            <?php Ace\Editor::render( 'bookly-cloud-sms' ) ?>
            <input type="hidden" name="notification[message]" />
        </div>
    </div>
</div>