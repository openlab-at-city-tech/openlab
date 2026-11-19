<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

class EMCS_Customizer
{
    public static function init()
    {
        add_submenu_page(
            'emcs-event-types',
            __('Customize Widget - EMC', 'embed-calendly-scheduling'),
            __('Customizer', 'embed-calendly-scheduling'),
            'manage_options',
            'emcs-customizer',
            'EMCS_Customizer::get_layout'
        );
    }

    public static function get_layout()
    {
        include_once(EMCS_EVENT_TYPES . 'event-types.php');

        $emcs_events = EMCS_Event_Types::get_event_types();
?>
        <div class="emcs-title">
            <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc-logo.svg') ?>" alt="<?php esc_attr_e('emc logo', 'embed-calendly-scheduling'); ?>" width="200px" />
        </div>
        <div class="emcs-subtitle">
            <?php esc_html_e('Widget Customizer', 'embed-calendly-scheduling'); ?>
            <div class="emcs-sync-event-types">
                <div class="emcs-links">
                    <a href="https://simpma.com/contact/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Ask a Question', 'embed-calendly-scheduling'); ?></a> |
                    <a href="https://simpma.com/blog/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Articles', 'embed-calendly-scheduling'); ?></a> |
                    <a href="https://simpma.com/work-with-us/?utm_source=plugin&utm_medium=menu&utm_campaign=hire" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Hire An Expert', 'embed-calendly-scheduling'); ?></a> |
                    <a href="https://simpma.com/emc/pricing/?utm_source=plugin&utm_medium=menu&utm_campaign=pro" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Get Pro', 'embed-calendly-scheduling'); ?></a>
                </div>
            </div>
        </div>
        <div class="emcs-customizer-notice">
            <?php
            printf(
                /* translators: 1: opening anchor tag, 2: closing anchor tag */
                esc_html__('Want your booking widget to match your brand? %1$sUnlock Pro >>%2$s', 'embed-calendly-scheduling'),
                '<a href="' . esc_url('https://simpma.com/emc/pricing/?utm_source=plugin&utm_medium=customizer-notice') . '" target="_blank" rel="noopener noreferrer">',
                '</a>'
            );
            ?>
            <br><br>
            <small>
                <span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php esc_html_e('Full Customizer', 'embed-calendly-scheduling'); ?>
            </small>
            <small>
                <span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php esc_html_e('Multiple Event Types', 'embed-calendly-scheduling'); ?>
            </small>
            <small>
                <span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php esc_html_e('Track Bookings', 'embed-calendly-scheduling'); ?>
            </small>
        </div>
        <div class="emcs-customizer">
            <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc-customizer.png'); ?>" width="100%" alt="emc customizer demo">
        </div>
<?php
    }
}
