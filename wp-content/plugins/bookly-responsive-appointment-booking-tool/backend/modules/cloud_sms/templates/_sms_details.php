<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\DateTime;
use Bookly\Backend\Components\Dialogs;
/** @var array $datatables */
?>
<div class="row justify-content-between">
    <div class="col-md-4">
        <div class="form-group">
            <button type="button" id="sms_date_range" class="btn btn-default text-left text-truncate" data-date="<?php echo date( 'Y-m-d', strtotime( '-30 days' ) ) ?> - <?php echo date( 'Y-m-d' ) ?>">
                <i class="far fa-calendar-alt mr-1"></i>
                <input type="hidden" name="form-purchases">
                <span>
                    <?php echo DateTime::formatDate( '-30 days' ) ?> - <?php echo DateTime::formatDate( 'today' ) ?>
                </span>
            </button>
        </div>
    </div>
    <div class="col-md-4 form-row justify-content-end">
        <?php Dialogs\TableSettings\Dialog::renderButton( 'sms_details', 'BooklyL10n', esc_attr( add_query_arg( 'tab', 'sms_details' ) ) ) ?>
    </div>
</div>

<table id="bookly-sms" class="table table-striped w-100">
    <thead>
    <tr>
        <?php foreach ( $datatables['sms_details']['settings']['columns'] as $column => $show ) : ?>
            <?php if ( $show ) : ?>
                <th><?php echo esc_html( $datatables['sms_details']['titles'][ $column ] ) ?></th>
            <?php endif ?>
        <?php endforeach ?>
    </tr>
    </thead>
</table>