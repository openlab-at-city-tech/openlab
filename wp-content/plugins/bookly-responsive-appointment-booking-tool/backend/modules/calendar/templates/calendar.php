<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
use Bookly\Backend\Components;
use Bookly\Backend\Modules as Backend;
use Bookly\Backend\Modules\Calendar\Proxy;

/**
 * @var Bookly\Lib\Entities\Staff[] $staff_members
 * @var array $staff_dropdown_data
 * @var array $services_dropdown_data
 * @var int $refresh_rate
 */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Calendar', 'bookly' ) ?></h4>
        <?php if ( Common::isCurrentUserSupervisor() ) : ?>
            <?php Components\Support\Buttons::render( $self::pageSlug() ) ?>
        <?php endif ?>
    </div>
    <div class="card">
        <div class="card-body">
            <?php if ( $staff_members ) : ?>
                <div class="form-row justify-content-xl-end justify-content-center">
                    <?php Proxy\OutlookCalendar::renderSyncButton( $staff_members ) ?>
                    <?php Proxy\AdvancedGoogleCalendar::renderSyncButton( $staff_members ) ?>
                    <?php Proxy\Locations::renderCalendarLocationFilter() ?>
                    <div class="col-sm-auto mb-2">
                        <ul id="bookly-js-services-filter"
                            data-icon-class="far fa-dot-circle"
                            data-align="right"
                            data-txt-select-all="<?php esc_attr_e( 'All services', 'bookly' ) ?>"
                            data-txt-all-selected="<?php esc_attr_e( 'All services', 'bookly' ) ?>"
                            data-txt-nothing-selected="<?php esc_attr_e( 'No service selected', 'bookly' ) ?>"
                        >
                            <?php Proxy\Pro::renderServicesFilterOption(); ?>
                            <?php foreach ( $services_dropdown_data as $category_id => $category ): ?>
                                <li<?php if ( ! $category_id ) : ?> data-flatten-if-single<?php endif ?>><?php echo esc_html( $category['name'] ) ?>
                                    <ul>
                                        <?php foreach ( $category['items'] as $service ) : ?>
                                            <li data-value="<?php echo esc_attr( $service['id'] ) ?>">
                                                <?php echo esc_html( $service['title'] ) ?>
                                            </li>
                                        <?php endforeach ?>
                                    </ul>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php if ( Common::isCurrentUserSupervisor() ) : ?>
                        <div class="col-sm-auto mb-2">
                            <ul id="bookly-js-staff-filter"
                                data-align="right"
                                data-txt-select-all="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                                data-txt-all-selected="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                                data-txt-nothing-selected="<?php esc_attr_e( 'No staff selected', 'bookly' ) ?>"
                            >
                                <?php foreach ( $staff_dropdown_data as $category_id => $category ): ?>
                                    <li<?php if ( ! $category_id ) : ?> data-flatten-if-single<?php endif ?>><?php echo esc_html( $category['name'] ) ?>
                                        <ul>
                                            <?php foreach ( $category['items'] as $staff ) : ?>
                                                <li data-value="<?php echo esc_attr( $staff['id'] ) ?>">
                                                    <?php echo esc_html( $staff['full_name'] ) ?>
                                                </li>
                                            <?php endforeach ?>
                                        </ul>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>
                    <div class="col-sm-auto mb-2 text-center">
                        <div class="btn-group">
                            <button type="button" class="btn <?php echo esc_attr( $refresh_rate > 0 ? 'btn-success' : 'btn-default' ) ?>" id="bookly-calendar-refresh"><i class="fas fa-sync-alt"></i></button>
                            <button type="button" class="btn <?php echo esc_attr( $refresh_rate > 0 ? 'btn-success' : 'btn-default' ) ?> bookly-dropdown-toggle bookly-dropdown-toggle-split" data-toggle="bookly-dropdown" aria-haspopup="true" aria-expanded="false"></button>
                            <div class="bookly-dropdown-menu pb-0 bookly-dropdown-menu-right overflow-hidden">
                                <h6 class="bookly-dropdown-header"><?php esc_html_e( 'Auto-refresh Calendar', 'bookly' ) ?></h6>
                                <div class="bookly-dropdown-divider"></div>
                                <?php Components\Controls\Inputs::renderRadioGroup( null, null,
                                    array(
                                        '60' => array( 'title' => __( 'Every 1 minute', 'bookly' ) ),
                                        '300' => array( 'title' => __( 'Every 5 minutes', 'bookly' ) ),
                                        '900' => array( 'title' => __( 'Every 15 minutes', 'bookly' ) ),
                                        '0' => array( 'title' => __( 'Disable', 'bookly' ) ),
                                    ),
                                    $refresh_rate,
                                    array( 'name' => 'bookly_calendar_refresh_rate', 'parent-class' => 'bookly-dropdown-item mx-3 w-100' ) ) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nav-scrollable nav-scrollable-pills justify-content-center justify-content-xl-start bookly-js-staff-pills <?php if ( count( $staff_members ) == 1 ) : ?>d-none<?php endif ?>">
                    <ul class="col-auto nav nav-pills flex-nowrap">
                        <?php if ( Common::isCurrentUserSupervisor() ) : ?>
                            <li class="nav-item mr-2">
                                <a class="nav-link d-block text-center p-2" href="#" data-staff_id="0">
                                <span class="d-block">
                                    <i class="fas fa-users fa-2x" style="width: 40px; height: 40px;"></i>
                                </span>
                                    <span class="small align-self-center"><?php esc_html_e( 'All', 'bookly' ) ?></span>
                                </a>
                            </li>
                        <?php endif ?>
                        <?php foreach ( $staff_members as $staff ) : ?>
                            <li class="nav-item mr-2 text-nowrap<?php if ( ! Common::isCurrentUserSupervisor() ) : ?> d-none<?php endif ?>" style="display:none;">
                                <a class="nav-link d-block p-2 text-center" href="#" data-staff_id="<?php echo esc_attr( $staff->getId() ) ?>">
                                    <?php if ( $image = $staff->getImageUrl( 'thumbnail' ) ) : ?>
                                        <span class="rounded-circle d-flex overflow-hidden m-auto" style="height: 40px; width: 40px;">
                                    <img src="<?php echo esc_attr( $image ) ?>" alt="<?php echo esc_attr( $staff->getFullName() ) ?>" class="d-block mx-auto" style="max-width: 40px; max-height: 40px; align-self: center;"/>
                                </span>
                                    <?php else : ?>
                                        <i class="far fa-user-circle fa-2x d-block mx-auto font-weight-bold" style="width: 40px; height: 40px;"></i>
                                    <?php endif ?>
                                    <span class="small align-self-center">
                                    <?php echo esc_html( $staff->getFullName() ) ?>
                                </span>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>
            <div class="mt-3 position-relative">
                <?php if ( $staff_members ) : ?>
                    <div class="bookly-ec-loading" style="display: none">
                        <div class="bookly-ec-loading-icon"></div>
                    </div>
                    <div class="bookly-js-calendar"></div>
                    <?php Components\Dialogs\Appointment\Edit\Dialog::render() ?>
                    <?php Proxy\Shared::renderAddOnsComponents() ?>
                <?php elseif ( Bookly\Lib\Config::proActive() ) : ?>
                    <?php Components\Notices\Proxy\Pro::renderWelcome() ?>
                <?php else : ?>
                    <div class="m-3">
                        <div class="h1"><?php esc_html_e( 'Welcome to Bookly and thank you for your choice!', 'bookly' ) ?></div>
                        <h4><?php esc_html_e( 'Bookly will simplify the booking process for your customers. This plugin creates another touchpoint to convert your visitors into customers. With Bookly your clients can see your availability, pick the services you provide, book them online and much more.', 'bookly' ) ?></h4>
                        <p><?php esc_html_e( 'To start using Bookly, you need to set up the services you provide and specify the staff members who will provide those services.', 'bookly' ) ?></p>
                        <ol>
                            <li><?php esc_html_e( 'Add a staff member (you can add only one service provider with a free version of Bookly).', 'bookly' ) ?></li>
                            <li><?php esc_html_e( 'Add services you provide (up to five with a free version of Bookly) and assign them to a staff member.', 'bookly' ) ?></li>
                            <li><?php esc_html_e( 'Go to Posts/Pages and click on the \'Add Bookly booking form\' button in the page editor to publish the booking form on your website.', 'bookly' ) ?></li>
                        </ol>
                        <p><?php printf( __( 'Bookly can boost your sales and scale together with your business. Get more features and remove the limits by upgrading to the paid version with the <a href="%s" target="_blank">Bookly Pro add-on</a>, which allows you to use a vast number of additional features and settings for booking services, install other add-ons for Bookly, and includes six months of customer support.', 'bookly' ), Common::prepareUrlReferrers( 'https://codecanyon.net/item/bookly/7226091?ref=ladela', 'welcome' ) ) ?></p>
                        <hr>
                        <a class="btn btn-success" href="<?php echo Common::escAdminUrl( Backend\Staff\Ajax::pageSlug() ) ?>">
                            <?php esc_html_e( 'Add Staff Members', 'bookly' ) ?>
                        </a>
                        <a class="btn btn-success" href="<?php echo Common::escAdminUrl( Backend\Services\Ajax::pageSlug() ) ?>">
                            <?php esc_html_e( 'Add Services', 'bookly' ) ?>
                        </a>
                        <a class="btn btn-success" href="<?php echo Common::prepareUrlReferrers( 'https://codecanyon.net/item/bookly/7226091?ref=ladela', 'welcome' ) ?>" target="_blank">
                            <?php esc_html_e( 'Try Bookly Pro add-on', 'bookly' ) ?>
                        </a>
                        <a class="btn btn-success" href="<?php echo Common::escAdminUrl( Backend\CloudProducts\Page::pageSlug() ) ?>">
                            <?php esc_html_e( 'Bookly Cloud', 'bookly' ) ?>
                        </a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?php Components\Dialogs\Appointment\Delete\Dialog::render() ?>
    <?php Components\Dialogs\Queue\Dialog::render() ?>
</div>