<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Dialogs;
use Bookly\Lib\Utils\Tables;
/** @var array $datatable */?>
<div class="form-row justify-content-end">
    <?php Dialogs\TableSettings\Dialog::renderButton( Tables::VOICE_PRICES, 'BooklyL10n', esc_attr( add_query_arg( '', '' ) ) . '#price_list' ) ?>
</div>
<div class="intl-tel-input">
    <table id="bookly-prices" class="table table-striped w-100">
        <thead>
        <tr>
            <?php foreach ( $datatable['settings']['columns'] as $column => $show ) : ?>
                <?php if ( $show ) : ?>
                    <th><?php echo esc_html( $datatable['titles'][ $column ] ) ?></th>
                <?php endif ?>
            <?php endforeach ?>
        </tr>
        </thead>
    </table>
</div>
<small class="text-muted form-text"><?php _e( 'If you do not see your country in the list please contact us at <a href="mailto:support@bookly.info">support@bookly.info</a>.', 'bookly' ) ?></small>