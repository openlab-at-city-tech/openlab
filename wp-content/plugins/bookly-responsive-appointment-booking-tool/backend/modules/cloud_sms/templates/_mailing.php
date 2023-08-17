<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/** @var array $datatables */
?>
<div id="mailing_lists">
    <?php static::renderTemplate( '_mailing_lists', array( 'datatable' => $datatables['sms_mailing_lists'] ) ) ?>
</div>
<div id="mailing_recipients" style="display: none">
    <?php static::renderTemplate( '_mailing_recipients', array( 'datatable' => $datatables['sms_mailing_recipients_list'] ) ) ?>
</div>

