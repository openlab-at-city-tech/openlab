<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

class EMCS_Event_Types_Dashboard
{

    public static function init()
    {
        add_menu_page(
            __('EMC Scheduling Manager', 'embed-calendly-scheduling'),
            __('EMC', 'embed-calendly-scheduling'),
            'manage_options',
            'emcs-event-types',
            'EMCS_Event_Types_Dashboard::emcs_event_list_html',
            'dashicons-calendar-alt',
            30
        );

        add_submenu_page(
            'emcs-event-types',
            __('Event Types - EMC', 'embed-calendly-scheduling'),
            __('Event Types', 'embed-calendly-scheduling'),
            'manage_options',
            'emcs-event-types',
            'EMCS_Event_Types_Dashboard::emcs_event_list_html'
        );
    }

    public static function emcs_event_list_html()
    {
        include_once(EMCS_EVENT_TYPES . 'event-types.php');

        // hook sync button listener
        EMCS_Event_Types::sync_event_types_button_listener();
        $events = EMCS_Event_Types::get_event_types();
?>
        <div class="emcs-title">
            <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc-logo.svg') ?>" alt="<?php esc_attr_e('emc logo', 'embed-calendly-scheduling'); ?>" width="200px" />
        </div>
        <div class="emcs-subtitle">
            <?php esc_html_e('Event Types', 'embed-calendly-scheduling'); ?>
            <div class="emcs-sync-event-types">
                <form action="" method="POST">
                    <button type="submit" name="emcs_sync_event_types" class="button-primary emcs-sync-button"><span class="dashicons dashicons-update-alt emcs-dashicon"></span> <?php esc_html_e('Sync', 'embed-calendly-scheduling'); ?> </button>
                </form>
            </div>
        </div>
        <div class="emcs-wrapper">
            <?php

            self::display_greeting();
            self::display_greeting_listener();

            if (empty($events)) {
                esc_html_e('No event types in your account', 'embed-calendly-scheduling');
            } else {
            ?>
                <!-- Event List Table -->
                <table class="wp-list-table widefat fixed striped table-view-list posts emcs-event-type-list">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-primary"><?php esc_html_e('Name', 'embed-calendly-scheduling'); ?></th>
                            <th scope="col" class="manage-column"><?php esc_html_e('Shortcode', 'embed-calendly-scheduling'); ?></th>
                            <th scope="col" class="manage-column"><?php esc_html_e('Status', 'embed-calendly-scheduling'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        foreach ($events as $event) {

                            $status = ($event->status) ? '<span class="emcs-active">' . esc_html__('Active', 'embed-calendly-scheduling') . '</span>' :
                                '<span class="emcs-inactive"> ' . esc_html__('In-active', 'embed-calendly-scheduling') . '</span>';
                        ?>
                            <tr>
                                <td class="title column-primary page-title emcs-event-type-column" data-colname="<?php esc_attr_e('Name', 'embed-calendly-scheduling'); ?>">
                                    <strong><span class="row-title"><?php echo esc_attr($event->name); ?></span></strong>
                                    <div class="row-actions"><a href="?page=emcs-customizer&event_type=<?php echo esc_attr($event->slug) ?>" id="emcs-admin-customize-event"><?php esc_html_e('Customize', 'embed-calendly-scheduling'); ?></a>
                                </td>
                                <td class="shortcode emcs-event-type-column" data-colname="<?php esc_attr_e('Shortcode', 'embed-calendly-scheduling'); ?>"> <input style="background:#bfefff" type="text" onclick="this.select();" value="[calendly url=&quot;<?php echo esc_attr($event->url)  ?>&quot; type=&quot;1&quot;]"><br>
                                </td>
                                <td class="date emcs-event-type-column" data-colname="<?php esc_attr_e('Status', 'embed-calendly-scheduling'); ?>"><?php echo $status; ?></td>
                            </tr>

                        <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="col" class="manage-column column-primary"><?php esc_html_e('Name', 'embed-calendly-scheduling'); ?></th>
                            <th scope="col" class="manage-column"><?php esc_html_e('Shortcode', 'embed-calendly-scheduling'); ?></th>
                            <th scope="col" class="manage-column"><?php esc_html_e('Status', 'embed-calendly-scheduling'); ?></th>
                        </tr>
                    </tfoot>

                </table>
        </div>
    <?php
            }
        }

        private static function display_greeting_listener()
        {
            if (isset($_GET['emcs_display_greeting'])) {
                if ($_GET['emcs_display_greeting'] == 0) {
                    update_option('emcs_display_greeting', 0);
                }
            }
        }
        private static function display_greeting()
        {
            $option = get_option('emcs_display_greeting');
            if ($option) {

                if (isset($_GET['emcs_display_greeting'])) {
                    if ($_GET['emcs_display_greeting'] == 0) {
                        return;
                    }
                }

                self::display_greeting_html();
            }
        }

        private static function display_greeting_html()
        {
    ?>
    <div class="emcs-dashboard-greeting">
        <?php esc_html_e('Thank you for downloading EMC Scheduling Manager!', 'embed-calendly-scheduling'); ?>
        <div class="emcs-greeting-right">
            <a href="<?php echo esc_url(admin_url('admin.php?page=emcs-settings#emcs-thankyou')); ?>"><?php esc_html_e('Read thank you note', 'embed-calendly-scheduling'); ?></a> |
            <a href="<?php echo esc_url(admin_url('admin.php?page=emcs-event-types&emcs_display_greeting=0')); ?>" class="emcs-greeting-dismiss"><?php esc_html_e('Dismiss', 'embed-calendly-scheduling'); ?></a>
        </div>
    </div>
<?php
        }
    }
