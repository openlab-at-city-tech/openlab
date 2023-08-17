<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Settings\Inputs;
$whatsapp = \Bookly\Lib\Cloud\API::getInstance()->whatsapp;
?>
<div class="form-row">
    <div class="col-lg-6 col-xs-12">
        <?php Inputs::renderTextValue( 'access_token', $whatsapp->access_token, __( 'Permanent access token', 'bookly' ) ) ?>
        <?php Inputs::renderTextValue( 'phone_id', $whatsapp->phone_id, __( 'Phone number ID ', 'bookly' ) ) ?>
        <?php Inputs::renderTextValue( 'business_account_id', $whatsapp->business_account_id, __( 'WhatsApp Business Account ID', 'bookly' ) ) ?>
    </div>
</div>
<div class="form-row">
    <div class="col-lg-6 col-xs-12">
        <div class="d-flex justify-content-end">
            <?php Buttons::renderSubmit() ?>
        </div>
    </div>
</div>