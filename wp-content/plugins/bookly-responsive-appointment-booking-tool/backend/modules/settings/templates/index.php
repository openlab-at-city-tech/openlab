<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Backend\Components;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Settings', 'bookly' ) ?></h4>
        <?php Components\Support\Buttons::render( '' ) ?>
    </div>

    <style>
        .bookly-settings-search-wrap {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }
        .bookly-settings-search-wrap .bookly-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            pointer-events: none;
            z-index: 2;
            font-size: 13px;
            line-height: 1;
            width: 14px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #bookly-settings-search {
            padding-left: 2rem !important;
        }
        .bookly-search-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,.1);
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
        }
        .bookly-search-dropdown .bookly-search-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }
        .bookly-search-dropdown .bookly-search-item:last-child {
            border-bottom: none;
        }
        .bookly-search-dropdown .bookly-search-item:hover {
            background: #f7f7f7;
        }
        .bookly-search-dropdown .bookly-search-item-label {
            display: block;
            font-weight: 400;
        }
        .bookly-search-dropdown .bookly-search-item-help {
            display: block;
            font-size: 12px;
            color: #888;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .bookly-search-dropdown .bookly-search-item-tab {
            display: block;
            font-size: 11px;
            color: #999;
            margin-top: 2px;
            font-weight: bold;
        }
        .bookly-search-dropdown .bookly-search-show-more {
            padding: 8px 12px;
            text-align: center;
            cursor: pointer;
            color: #0073aa;
            font-size: 13px;
            border-top: 1px solid #eee;
        }
        .bookly-search-dropdown .bookly-search-show-more:hover {
            background: #f7f7f7;
        }
        .bookly-search-dropdown .bookly-search-no-results {
            padding: 12px;
            text-align: center;
            color: #999;
        }
        .bookly-search-highlight mark,
        .bookly-search-dropdown mark {
            background: transparent;
            font-weight: 600;
            padding: 0 !important;
            margin: 0;
        }
        .bookly-setting-highlight-active {
            background-color: #fff3cd !important;
            border-radius: 4px;
        }
        .bookly-setting-highlight-fade {
            background-color: transparent !important;
            border-radius: 4px;
            transition: background-color 1.5s ease !important;
        }
    </style>

    <div class="bookly-settings-search-wrap">
        <span class="bookly-search-icon"><i class="fas fa-search"></i></span>
        <input type="text" id="bookly-settings-search" class="form-control" placeholder="<?php esc_attr_e( 'Search settings...', 'bookly' ) ?>" autocomplete="off" />
        <div class="bookly-search-dropdown" id="bookly-search-dropdown"></div>
    </div>

    <div class="form-row">
        <div id="bookly-sidebar" class="col-12 col-sm-auto">
            <div class="nav flex-column nav-pills" role="tablist">
                <?php Components\Settings\Menu::renderItem( __( 'General', 'bookly' ), 'general' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'URL Settings', 'bookly' ), 'url' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Calendar', 'bookly' ), 'calendar' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Company', 'bookly' ), 'company' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Customers', 'bookly' ), 'customers' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Appointments', 'bookly' ), 'appointments' ) ?>
                <?php Proxy\Mailchimp::renderMenuItem() ?>
                <?php Proxy\Pro::renderMenuItem( __( 'Google Calendar', 'bookly' ), 'google_calendar' ) ?>
                <?php Proxy\Shared::renderMenuItem() ?>
                <?php Proxy\Pro::renderMenuItem( __( 'Online Meetings', 'bookly' ), 'online_meetings' ) ?>
                <?php Proxy\Pro::renderMenuItem( __( 'User Permissions', 'bookly' ), 'user_permissions' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Payments', 'bookly' ), 'payments' ) ?>
                <?php Proxy\Pro::renderMenuItem( __( 'Additional', 'bookly' ), 'additional' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Business Hours', 'bookly' ), 'business_hours' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Holidays', 'bookly' ), 'holidays' ) ?>
            </div>
        </div>

        <div id="bookly_settings_controls" class="col">
            <div class="card">
                <div class="tab-content">
                    <div class="tab-pane active" id="bookly_settings_general">
                        <?php self::renderTemplate( '_generalForm', $values ) ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_url">
                        <?php include '_urlForm.php' ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_calendar">
                        <?php include '_calendarForm.php' ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_company">
                        <?php include '_companyForm.php' ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_customers">
                        <?php include '_customers.php' ?>
                    </div>
                    <div class='tab-pane' id='bookly_settings_appointments'>
                        <?php self::renderTemplate( '_appointmentsForm', array( 'statuses' => $values['statuses'] ) ) ?>
                    </div>
                    <?php Proxy\Mailchimp::renderTab() ?>
                    <?php Proxy\Shared::renderTab() ?>
                    <?php Proxy\CustomStatuses::renderTab() ?>
                    <div class="tab-pane" id="bookly_settings_payments">
                        <?php include '_paymentsForm.php' ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_business_hours">
                        <?php include '_hoursForm.php' ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_holidays">
                        <?php include '_holidaysForm.php' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>