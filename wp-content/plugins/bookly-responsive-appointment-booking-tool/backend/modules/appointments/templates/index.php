<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Support;
use Bookly\Backend\Modules\Appointments\Proxy;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Config;

/** @var array $datatables */
?>
<div id="bookly-tbs" class="wrap bookly-css-root">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Appointments', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div id="bookly-appointments-datatables-filters" class="bookly:hidden bookly:grid bookly:grid-cols-1 bookly:gap-2 bookly:relative bookly:datatable-filters">
                <div>
                    <input class="form-control" type="text" id="bookly-filter-id" placeholder="<?php esc_attr_e( 'ID', 'bookly' ) ?>"/>
                </div>
                <button type="button" class="btn btn-default w-100 text-truncate text-left" id="bookly-filter-date" data-date="<?php echo date( 'Y-m-d', strtotime( 'first day of' ) ) ?> - <?php echo date( 'Y-m-d', strtotime( 'last day of' ) ) ?>">
                    <i class="far fa-calendar-alt mr-1"></i>
                    <span>
                            <?php echo DateTime::formatDate( 'first day of this month' ) ?> - <?php echo DateTime::formatDate( 'last day of this month' ) ?>
                        </span>
                </button>
                <button type="button" class="btn btn-default w-100 text-truncate text-left" id="bookly-filter-creation-date" data-date="any">
                    <i class="far fa-calendar-alt mr-1"></i>
                    <span>
                            <?php esc_html_e( 'Created at any time', 'bookly' ) ?>
                        </span>
                </button>
                <div>
                    <select class="form-control bookly-js-select" id="bookly-filter-staff" data-placeholder="<?php echo esc_attr( Common::getTranslatedOption( 'bookly_l10n_label_employee' ) ) ?>">
                        <?php foreach ( $staff_members as $staff ) : ?>
                            <option value="<?php echo esc_attr( $staff['id'] ) ?>"><?php echo esc_html( $staff['full_name'] ) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <select class="form-control <?php echo esc_attr( $customers === false ? 'bookly-js-select-ajax' : 'bookly-js-select' ) ?>" id="bookly-filter-customer"
                            data-placeholder="<?php esc_attr_e( 'Customer', 'bookly' ) ?>" <?php echo esc_attr( $customers === false ? 'data-ajax--action' : 'data-action' ) ?>="bookly_get_customers_list">
                    <?php if ( $customers !== false ) : ?>
                        <?php foreach ( $customers as $customer_id => $customer ) : ?>
                            <option value="<?php echo esc_attr( $customer_id ) ?>" data-search='<?php echo esc_attr( json_encode( array_values( $customer ) ) ) ?>'><?php echo esc_html( $customer['full_name'] ) ?></option>
                        <?php endforeach ?>
                    <?php endif ?>
                    </select>
                </div>
                <div>
                    <select class="form-control bookly-js-select" id="bookly-filter-service" data-placeholder="<?php echo esc_attr( Common::getTranslatedOption( 'bookly_l10n_label_service' ) ) ?>">
                        <option value="0"><?php esc_html_e( 'Custom', 'bookly' ) ?></option>
                        <?php foreach ( $services as $service ) : ?>
                            <option value="<?php echo esc_attr( $service['id'] ) ?>"><?php echo esc_html( $service['title'] ) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <?php Proxy\Locations::renderFilter() ?>
                <div>
                    <ul id="bookly-filter-status"
                        data-txt-select-all="<?php esc_attr_e( 'All statuses', 'bookly' ) ?>"
                        data-txt-all-selected="<?php esc_attr_e( 'All statuses', 'bookly' ) ?>"
                        data-txt-nothing-selected="<?php esc_attr_e( 'No status selected', 'bookly' ) ?>"
                        data-hide-icon
                        data-align="right"
                    >
                        <?php foreach ( CustomerAppointment::getStatuses() as $status ): ?>
                            <li data-value="<?php echo esc_attr( $status ) ?>">
                                <?php echo esc_html( CustomerAppointment::statusToString( $status ) ) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
            <div id="bookly-appointments-datatables"></div>
        </div>
    </div>

    <?php Proxy\Pro::renderExportDialog( $datatables['appointments'] ) ?>
    <?php Proxy\Pro::renderPrintDialog( $datatables['appointments'] ) ?>

    <?php Dialogs\Appointment\Delete\Dialog::render() ?>
    <?php Dialogs\TableSettings\Dialog::render() ?>
    <?php Dialogs\Appointment\Edit\Dialog::render() ?>
    <?php Dialogs\Queue\Dialog::render() ?>
    <?php Proxy\Shared::renderAddOnsComponents() ?>
</div>
