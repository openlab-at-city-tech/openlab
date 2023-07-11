<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Modules\Notifications\Proxy;
?>
<div class="form-group bookly-js-attach-container">
    <div class="bookly-js-attach bookly-js-ics">
        <input type="hidden" name="notification[attach_ics]" value="0">
        <?php Inputs::renderCheckBox( __( 'Attach ICS file', 'bookly' ), 1, null, array( 'name' => 'notification[attach_ics]' ) ) ?>
    </div>
    <?php Proxy\Invoices::renderAttach() ?>
</div>