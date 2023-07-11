<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils;
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Cloud;
/** @var array $datatables */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Bookly Cloud Billing', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row pb-3">
                <div class="col">
                </div>
                <div class="col-auto">
                    <?php Cloud\Account\Panel::render() ?>
                </div>
            </div>
            <div class="row justify-content-between">
                <div class="col-md-4">
                    <div class="form-group">
                        <button type="button" id="purchases_date_range" class="btn btn-default text-truncate text-left" data-date="<?php echo date( 'Y-m-d', strtotime( '-30 days' ) ) ?> - <?php echo date( 'Y-m-d' ) ?>">
                            <i class="far fa-calendar-alt mr-1"></i>
                            <input type="hidden" name="form-purchases">
                            <span>
                                <?php echo Utils\DateTime::formatDate( '-30 days' ) ?> - <?php echo Utils\DateTime::formatDate( 'today' ) ?>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="col-md-4 form-row justify-content-end">
                    <?php Dialogs\TableSettings\Dialog::renderButton( 'cloud_purchases', 'BooklyL10n' ) ?>
                </div>
            </div>
            <table id="bookly-purchases" class="table table-striped w-100">
                <thead>
                <tr>
                    <?php foreach ( $datatables['cloud_purchases']['settings']['columns'] as $column => $show ) : ?>
                        <?php if ( $show ) : ?>
                            <th><?php echo esc_html( $datatables['cloud_purchases']['titles'][ $column ] ) ?></th>
                        <?php endif ?>
                    <?php endforeach ?>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <?php Dialogs\TableSettings\Dialog::render() ?>
</div>