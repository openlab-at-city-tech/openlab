<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Entities\Payment;
use Bookly\Backend\Components\Support;
use Bookly\Backend\Modules\Payments\Proxy;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Controls;

/** @var array $datatables */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Payments', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-row justify-content-end">
                <?php Dialogs\TableSettings\Dialog::renderButton( 'payments' ) ?>
            </div>
            <div class="form-row">
                <div class="col-md-1">
                    <div class="form-group">
                        <input class="form-control" type="text" id="bookly-filter-id" placeholder="<?php esc_attr_e( 'No.', 'bookly' ) ?>"/>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 mb-3 mb-md-0">
                    <button type="button" class="btn btn-block btn-default text-truncate text-left" id="bookly-filter-date" data-date="<?php echo date( 'Y-m-d', strtotime( '-30 day' ) ) ?> - <?php echo date( 'Y-m-d' ) ?>">
                        <i class="far fa-fw fa-calendar-alt"></i>
                        <span>
                            <?php echo Bookly\Lib\Utils\DateTime::formatDate( '-30 days' ) ?> - <?php echo Bookly\Lib\Utils\DateTime::formatDate( 'today' ) ?>
                        </span>
                    </button>
                </div>
                <div class="col-lg-2 col-md-2">
                    <button type="button" class="btn btn-default w-100 mb-3 text-truncate text-left" id="bookly-filter-appointment-date" data-date="any">
                        <i class="far fa-calendar-alt mr-1"></i>
                        <span>
                            <?php esc_html_e( 'Appointment at any time', 'bookly' ) ?>
                        </span>
                    </button>
                </div>
                <div class="col-lg-1 col-md-2">
                    <div class="form-group">
                        <select id="bookly-filter-type" class="form-control bookly-js-select" data-placeholder="<?php esc_attr_e( 'Type', 'bookly' ) ?>">
                            <?php foreach ( $types as $type ) : ?>
                                <option value="<?php echo esc_attr( $type ) ?>">
                                    <?php echo Payment::typeToString( $type ) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-1 col-md-4">
                    <div class="form-group">
                        <select class="form-control <?php echo esc_attr( $customers === false ? 'bookly-js-select-ajax' : 'bookly-js-select' ) ?>" id="bookly-filter-customer" data-placeholder="<?php esc_attr_e( 'Customer', 'bookly' ) ?>" <?php echo esc_attr( $customers === false ? 'data-ajax--action' : 'data-action' ) ?>="bookly_get_customers_list">
                        <?php if ( $customers !== false ) : ?>
                            <?php foreach ( $customers as $customer_id => $customer ) : ?>
                                <option value="<?php echo esc_attr( $customer_id ) ?>" data-search='<?php echo esc_attr( json_encode( array_values( $customer ) ) ) ?>'><?php echo esc_html( $customer['full_name'] ) ?></option>
                            <?php endforeach ?>
                        <?php endif ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <select id="bookly-filter-staff" class="form-control bookly-js-select" data-placeholder="<?php esc_attr_e( 'Provider', 'bookly' ) ?>">
                            <?php foreach ( $providers as $provider ) : ?>
                                <option value="<?php echo esc_attr( $provider['id'] ) ?>"><?php echo esc_html( $provider['full_name'] ) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <select id="bookly-filter-service" class="form-control bookly-js-select" data-placeholder="<?php esc_attr_e( 'Service', 'bookly' ) ?>">
                            <?php foreach ( $services as $service ) : ?>
                                <option value="<?php echo esc_attr( $service['id'] ) ?>"><?php echo esc_html( $service['title'] ) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-1 col-md-2">
                    <div class="form-group">
                        <select id="bookly-filter-status" class="form-control bookly-js-select" data-placeholder="<?php esc_attr_e( 'Status', 'bookly' ) ?>">
                            <option value="<?php echo Payment::STATUS_COMPLETED ?>"><?php echo Payment::statusToString( Payment::STATUS_COMPLETED ) ?></option>
                            <option value="<?php echo Payment::STATUS_PENDING ?>"><?php echo Payment::statusToString( Payment::STATUS_PENDING ) ?></option>
                            <option value="<?php echo Payment::STATUS_REJECTED ?>"><?php echo Payment::statusToString( Payment::STATUS_REJECTED ) ?></option>
                            <option value="<?php echo Payment::STATUS_REFUNDED ?>"><?php echo Payment::statusToString( Payment::STATUS_REFUNDED ) ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <table id="bookly-payments-list" class="table table-striped w-100">
                <thead>
                <tr>
                    <?php foreach ( $datatables['payments']['settings']['columns'] as $column => $show ) : ?>
                        <?php if ( $show ) : ?>
                            <th><?php echo esc_html( $datatables['payments']['titles'][ $column ] ) ?></th>
                        <?php endif ?>
                    <?php endforeach ?>
                    <th></th>
                    <th width="16"><?php Controls\Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-check-all' ) ) ?></th>
                </tr>
                </thead>
                <?php if ( array_key_exists( 'paid', $datatables['payments']['settings']['columns'] ) && $datatables['payments']['settings']['columns']['paid'] ) : ?>
                    <tfoot>
                    <tr>
                        <?php $columns = array_filter( $datatables['payments']['settings']['columns'] ) ?>
                        <?php $index = array_search( 'paid', array_keys( $columns ) ) ?>
                        <?php for ( $column = 0; $column < count( $columns ) + 2; $column++ ) : ?>
                            <?php if ( $column == $index - 1 ) : ?>
                                <th>
                                    <div class="pull-right"><?php esc_html_e( 'Total', 'bookly' ) ?>:</div>
                                </th>
                            <?php elseif ( $column == $index ) : ?>
                                <th><span id="bookly-payment-total"></span></th>
                            <?php else : ?>
                                <th></th>
                            <?php endif ?>
                        <?php endfor ?>
                    </tr>
                    </tfoot>
                <?php endif ?>
            </table>
            <div class="d-flex flex-row-reverse mt-3">
                <?php Controls\Buttons::renderDelete() ?>
                <?php Proxy\Invoices::renderDownloadButton() ?>
            </div>
        </div>

        <?php Dialogs\Payment\Dialog::render() ?>
        <?php Dialogs\TableSettings\Dialog::render() ?>
    </div>
</div>