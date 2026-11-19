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

        $events = EMCS_Event_Types::get_event_types();
?>
        <div class="emcs-title">
            <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc-logo.svg') ?>" alt="<?php esc_attr_e('emc logo', 'embed-calendly-scheduling'); ?>" width="200px" />
        </div>
        <div class="emcs-subtitle">
            <?php esc_html_e('Event Types', 'embed-calendly-scheduling'); ?>
            <div class="emcs-sync-event-types">
                <div class="emcs-links">
                    <a href="https://simpma.com/contact/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Ask a Question', 'embed-calendly-scheduling'); ?></a> |
                    <a href="https://simpma.com/blog/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Articles', 'embed-calendly-scheduling'); ?></a> |
                    <a href="https://simpma.com/work-with-us/?utm_source=plugin&utm_medium=menu&utm_campaign=hire" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Hire An Expert', 'embed-calendly-scheduling'); ?></a> |
                    <a href="https://simpma.com/emc/pricing/?utm_source=plugin&utm_medium=menu&utm_campaign=pro" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Get Pro', 'embed-calendly-scheduling'); ?></a>
                </div>
            </div>
        </div>
        <div class="emcs-wrapper">
            <?php

            self::display_greeting();
            self::display_greeting_listener();
            self::display_sync_error();

            ?>

            <div class="emcs-sync-event-types">
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                    <input type="hidden" name="action" value="emcs_sync_event_types">
                    <?php wp_nonce_field('emcs_sync_event_types_action', '_wpnonce', true, true); ?>
                    <button type="submit" name="emcs_sync_event_types" class="button-primary emcs-sync-button">
                        <span class="dashicons dashicons-update-alt emcs-dashicon"></span>
                        <?php esc_html_e('Sync', 'embed-calendly-scheduling'); ?>
                    </button>
                </form><br><br>
            </div>

            <div class="emcs-availability">
                <?php
                printf(
                    /* translators: %s: example shortcode */
                    esc_html__('Display your availability summary with: %s', 'embed-calendly-scheduling'),
                    '<strong>' . esc_html('[calendly_availability theme="default" text_color="#cc3366"]') . '</strong>'
                );
                ?><br><br>
                <?php esc_html_e('Or copy the shortcode below to embed your booking widget on a page.', 'embed-calendly-scheduling'); ?>
            </div>

            <?php

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
                                <td class="shortcode emcs-event-type-column" data-colname="<?php esc_attr_e('Shortcode', 'embed-calendly-scheduling'); ?>"> <input style="background:#bfefff" type="text" onclick="this.select();" value="[calendly url=&quot;<?php echo esc_url($event->url)  ?>&quot; type=&quot;1&quot;]"><br>
                                </td>
                                <td class="date emcs-event-type-column" data-colname="<?php esc_attr_e('Status', 'embed-calendly-scheduling'); ?>"><?php echo esc_attr($status); ?></td>
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
            if (!isset($_GET['emcs_display_greeting'])) {
                return;
            }

            if (
                !isset($_GET['_wpnonce']) ||
                !wp_verify_nonce(
                    sanitize_text_field(wp_unslash($_GET['_wpnonce'])),
                    'emcs_display_greeting_action'
                )
            ) {
                return;
            }

            $display_greeting = sanitize_text_field(wp_unslash($_GET['emcs_display_greeting']));

            if ($display_greeting === '0') {

                update_option('emcs_display_greeting', 0);

                wp_safe_redirect(remove_query_arg(array('emcs_display_greeting', '_wpnonce')));
                exit;
            }
        }

        private static function display_sync_error()
        {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if (!isset($_GET['emcs_sync_error'])) {
                return;
            }

            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $error_msg = isset($_GET['emcs_error_msg']) ? sanitize_text_field(wp_unslash($_GET['emcs_error_msg'])) : '';

            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $error_code = isset($_GET['emcs_error_code']) ? sanitize_text_field(wp_unslash($_GET['emcs_error_code'])) : '';

            $message = '';

            if ($error_code) {
                $message = sprintf(
                    /* translators: %1$d HTTP error code, %2$s error message */
                    __('Calendly sync failed (HTTP %1$d): %2$s', 'embed-calendly-scheduling'),
                    (int) $error_code,
                    $error_msg
                );
            } elseif ($error_msg) {
                $message = sprintf(
                    /* translators: %s error message */
                    __('Calendly sync failed: %s', 'embed-calendly-scheduling'),
                    $error_msg
                );
            } else {
                $message = __('Calendly sync failed. Please check your API key and try again.', 'embed-calendly-scheduling');
            }

            // Remove the query args from the URL so the notice doesn't persist
            $clean_url = remove_query_arg(array('emcs_sync_error', 'emcs_error_msg', 'emcs_error_code'));
    ?>
    <div class="emcs-sync-error notice notice-error is-dismissible">
        <p><?php echo esc_html($message); ?></p>
        <p class="emcs-sync-error-hint"><?php esc_html_e('Check your API key in Settings and ensure your Calendly account is active.', 'embed-calendly-scheduling'); ?></p>
    </div>
    <script>
        (function() {
            var cleanUrl = <?php echo json_encode($clean_url); ?>;
            if (window.location.search.indexOf('emcs_sync_error') !== -1 && cleanUrl) {
                window.history.replaceState({}, document.title, cleanUrl);
            }
        })();
    </script>
<?php
        }

        private static function display_greeting()
        {
            $option = get_option('emcs_display_greeting');

            if ($option) {

                if (isset($_GET['emcs_display_greeting'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    $display_greeting = sanitize_text_field(wp_unslash($_GET['emcs_display_greeting'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

                    if ($display_greeting === '0') {
                        return;
                    }
                }

                self::display_greeting_html();
            }
        }

        private static function display_greeting_html()
        {

            $dismiss_notice_url = wp_nonce_url(
                add_query_arg(
                    array(
                        'page' => 'emcs-event-types',
                        'emcs_display_greeting' => '0',
                    ),
                    admin_url('admin.php')
                ),
                'emcs_display_greeting_action'
            );
?>
    <div class="emcs-dashboard-greeting">
        <?php esc_html_e('Thanks for using EMC! How\'s your experience?', 'embed-calendly-scheduling'); ?>
        <div class="emcs-greeting-right">
            <a href="https://wordpress.org/support/plugin/embed-calendly-scheduling/reviews/#new-post" target="_blank"><?php esc_html_e('Leave a review', 'embed-calendly-scheduling'); ?></a> |
            <a href="<?php echo esc_url($dismiss_notice_url); ?>" class="emcs-greeting-dismiss"><?php esc_html_e('Dismiss', 'embed-calendly-scheduling'); ?></a>
        </div>
    </div>
<?php
        }
    }
