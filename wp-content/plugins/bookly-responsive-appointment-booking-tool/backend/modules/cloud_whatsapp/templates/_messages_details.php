<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\DateTime;
use Bookly\Backend\Components\Dialogs;
use Bookly\Lib\Utils\Tables;
/** @var array $datatables */
?>
<div class="row justify-content-between">
    <div class="col-md-4">
        <div class="form-group">
            <button type="button" id="whatsapp_date_range" class="btn btn-default text-left text-truncate" data-date="<?php echo date( 'Y-m-d', strtotime( '-30 days' ) ) ?> - <?php echo date( 'Y-m-d' ) ?>">
                <i class="far fa-calendar-alt mr-1"></i>
                <input type="hidden" name="form-purchases">
                <span>
                    <?php echo DateTime::formatDate( '-30 days' ) ?> - <?php echo DateTime::formatDate( 'today' ) ?>
                </span>
            </button>
        </div>
    </div>
    <div class="col-md-4 form-row justify-content-end">
        <?php Dialogs\TableSettings\Dialog::renderButton( Tables::WHATSAPP_DETAILS, 'BooklyL10n', esc_attr( add_query_arg( '', '' ) ) . '#details' ) ?>
    </div>
</div>

<table id="bookly-messages" class="table table-striped w-100">
    <thead>
    <tr>
        <?php foreach ( $datatable['settings']['columns'] as $column => $show ) : ?>
            <?php if ( $show ) : ?>
                <?php if ( $column === 'info' ) : ?>
                    <th style="max-width: 15%"><?php echo esc_html( $datatable['titles'][ $column ] ) ?></th>
                <?php else : ?>
                    <th><?php echo esc_html( $datatable['titles'][ $column ] ) ?></th>
                <?php endif ?>
            <?php endif ?>
        <?php endforeach ?>
    </tr>
    </thead>
</table>