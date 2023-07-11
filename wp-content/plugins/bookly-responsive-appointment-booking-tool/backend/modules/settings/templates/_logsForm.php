<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs as ControlsInputs;
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Log;
?>
<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'logs' ) ) ?>">
    <div class="card-body">
        <?php
        Selects::renderSingle( 'bookly_logs_enabled', __( 'Debug logs', 'bookly' ), __( 'If this setting is enabled then all actions with appointments will be recorded in a log table. We recommend enabling this setting as it will be helpful for our support team in case of unpredictable issues with appointments.', 'bookly' ) );
        ?>
        <div id="bookly-logs-table-wrap">
            <div class="form-row">
                <div class="col-lg-auto col-md-12">
                    <button type="button" class="btn btn-default w-100 mb-3 text-truncate text-left" id="bookly-logs-date-filter" data-date="<?php echo date( 'Y-m-d', strtotime( 'first day of' ) ) ?> - <?php echo date( 'Y-m-d', strtotime( 'last day of' ) ) ?>">
                        <i class="far fa-calendar-alt mr-1"></i>
                        <span>
                            <?php echo DateTime::formatDate( 'first day of this month' ) ?> - <?php echo DateTime::formatDate( 'last day of this month' ) ?>
                        </span>
                    </button>
                </div>
                <div class="col-md-3 col-xl-2">
                    <div class="form-group">
                        <ul id="bookly-filter-logs-action"
                            data-txt-select-all="<?php esc_attr_e( 'All actions', 'bookly' ) ?>"
                            data-txt-all-selected="<?php esc_attr_e( 'All actions', 'bookly' ) ?>"
                            data-txt-nothing-selected="<?php esc_attr_e( 'Nothing selected', 'bookly' ) ?>"
                            data-hide-icon
                        >
                            <?php foreach ( array( Log::ACTION_CREATE, Log::ACTION_DELETE, Log::ACTION_UPDATE, Log::ACTION_ERROR ) as $action ): ?>
                                <li data-value="<?php echo esc_attr( $action ) ?>">
                                    <?php echo ucfirst( $action ) ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 col-xl-2">
                    <div class="form-group">
                        <input class="form-control" type="text" id="bookly-filter-logs-target-id" placeholder="<?php esc_attr_e( 'Target ID', 'bookly' ) ?>"/>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <input class="form-control" type="text" id="bookly-log-search" placeholder="<?php esc_attr_e( 'Quick search', 'bookly' ) ?>"/>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="bookly-logs-table" class="table table-striped table-hover nowrap w-100 bookly-table-wrap">
                    <thead>
                    <tr>
                        <th><?php esc_html_e( 'Date', 'bookly' ) ?></th>
                        <th><?php esc_html_e( 'Action', 'bookly' ) ?></th>
                        <th><?php esc_html_e( 'Target', 'bookly' ) ?></th>
                        <th><?php esc_html_e( 'Target ID', 'bookly' ) ?></th>
                        <th><?php esc_html_e( 'Author', 'bookly' ) ?></th>
                        <th><?php esc_html_e( 'Details', 'bookly' ) ?></th>
                        <th><?php esc_html_e( 'Comment', 'bookly' ) ?></th>
                        <th><?php esc_html_e( 'Reference', 'bookly' ) ?></th>
                    </tr>
                    </thead>
                </table>
                <div class="text-right mt-3">
                    <?php Buttons::renderDelete( 'bookly-delete-logs', 'mr-2', __( 'Clear logs', 'bookly' ) ) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-end">
        <?php ControlsInputs::renderCsrf() ?>
        <?php Buttons::renderSubmit() ?>
    </div>
</form>