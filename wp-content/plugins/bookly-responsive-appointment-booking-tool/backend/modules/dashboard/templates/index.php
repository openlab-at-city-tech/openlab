<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components;
use Bookly\Backend\Components\Dashboard;
use Bookly\Backend\Modules\Dashboard\Proxy;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\DateTime;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center">
        <h4 class="col m-0"><?php esc_html_e( 'Dashboard', 'bookly' ) ?></h4>
        <?php if ( Common::isCurrentUserSupervisor() ) : ?>
            <?php Components\Support\Buttons::render( $self::pageSlug() ) ?>
        <?php endif ?>
    </div>
    <div class="row my-3">
        <div class="col-md-3 col-sm-6">
            <button type="button" class="btn btn-block btn-default text-left text-truncate" id="bookly-filter-date" data-date="<?php printf( '%s - %s', date( 'Y-m-d', strtotime( '-7 days' ) ), date( 'Y-m-d' ) ) ?>">
                <i class="far fa-calendar-alt mr-1"></i>
                <span>
                    <?php echo DateTime::formatDate( '-7 days' ) ?> - <?php echo DateTime::formatDate( 'today' ) ?>
                </span>
            </button>
        </div>
        <div class="col-md-9 col-sm-6">
            <h6 class="mt-2 text-muted">
                <?php esc_html_e( 'See the number of appointments and total revenue for the selected period', 'bookly' ) ?>
            </h6>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <?php Dashboard\Appointments\Widget::renderChart() ?>
            <?php Proxy\Pro::renderAnalytics() ?>
        </div>
    </div>
</div>