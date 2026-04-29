<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use Bookly\Lib;

/** @var Bookly\Lib\Entities\Staff $staff */
?>
<?php if ( $staff->getId() ) : ?>
    <div class="nav-scrollable mb-3 bookly-js-staff-tabs">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a id="bookly-details-tab" href="#details" data-toggle="bookly-tab" class="nav-link active">
                    <i class="fas fa-cog fa-fw mr-lg-1"></i>
                    <span class="d-none d-lg-inline"><?php esc_html_e( 'Details', 'bookly' ) ?></span>
                </a>
            </li>
            <?php if ( Lib\Config::proActive() || Lib\Utils\Advertisement::isVisible( 'staff-modal-advanced-tab' ) ) : ?>
                <li class="nav-item">
                    <a id="bookly-advanced-tab" href="#advanced" data-toggle="bookly-tab" class="nav-link">
                        <i class="fas fa-fw fa-cogs mr-lg-1"></i>
                        <span class="d-none d-lg-inline"><?php esc_html_e( 'Advanced', 'bookly' ) ?></span>
                    </a>
                </li>
            <?php endif ?>
            <li class="nav-item">
                <a id="bookly-services-tab" href="#services" data-toggle="bookly-tab" class="nav-link">
                    <i class="fas fa-th fa-fw mr-lg-1"></i>
                    <span class="d-none d-lg-inline"><?php esc_html_e( 'Services', 'bookly' ) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a id="bookly-schedule-tab" href="#schedule" data-toggle="bookly-tab" class="nav-link">
                    <i class="far fa-fw fa-calendar-alt mr-lg-1"></i>
                    <span class="d-none d-lg-inline"><?php esc_html_e( 'Schedule', 'bookly' ) ?></span>
                </a>
            </li>
            <?php Proxy\Shared::renderStaffTab() ?>
            <li class="nav-item">
                <a id="bookly-holidays-tab" href="#days_off" data-toggle="bookly-tab" class="nav-link">
                    <i class="far fa-calendar fa-fw mr-lg-1"></i>
                    <span class="d-none d-lg-inline"><?php esc_html_e( 'Days Off', 'bookly' ) ?></span>
                </a>
            </li>
        </ul>
    </div>
<?php endif ?>

<div class="tab-content bookly-js-staff-containers">
    <div class="tab-pane active" id="details">
        <div id="bookly-details-container"></div>
    </div>
    <div class="tab-pane" id="advanced">
        <div id="bookly-advanced-container"></div>
    </div>
    <div class="tab-pane" id="services">
        <div id="bookly-services-container"></div>
    </div>
    <div class="tab-pane" id="schedule">
        <div id="bookly-schedule-container"></div>
    </div>
    <div class="tab-pane" id="special_days">
        <div id="bookly-special-days-container"></div>
    </div>
    <div class="tab-pane" id="days_off">
        <div id="bookly-holidays-container"></div>
    </div>
</div>
