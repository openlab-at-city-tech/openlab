<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

class EMCS_Customizer
{
    public static function init()
    {
        add_submenu_page(
            'emcs-event-types',
            __('Customize Widget - Embed Calendly', 'embed-calendly-scheduling'),
            __('Customizer', 'embed-calendly-scheduling'),
            'manage_options',
            'emcs-customizer',
            'EMCS_Customizer::get_layout'
        );
    }

    public static function get_layout()
    {
        include_once(EMCS_EVENT_TYPES . 'event-types.php');

        // hook sync button listener
        EMCS_Event_Types::sync_event_types_button_listener();
        $events = EMCS_Event_Types::get_event_types();
?>
        <div class="emcs-title">
            <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc-logo.svg') ?>" alt="embed calendly logo" width="200px" />
        </div>
        <div class="emcs-subtitle">
            Widget Customizer
            <div class="emcs-sync-event-types">
                <form action="" method="POST">
                    <button type="submit" name="emcs_sync_event_types" class="button-primary emcs-sync-button"><span class="dashicons dashicons-update-alt emcs-dashicon"></span> Sync </button>
                </form>
            </div>
        </div>
        <div class="emcs-container emcs-customizer">
            <?php

            if (empty($events)) {
                echo '<div class="emcs-text-center">Create an event type in your Calendly account to begin customization.</div>';
                return;
            }

            $owner = EMCS_Event_Types::extract_event_type_owner($events[0]->url);
            ?>
            <div class="emcs-embed-title">Customize Widget</div>
            <?php
            include_once(EMCS_INCLUDES . 'widget-customizer/choose-customizer.php');
            include_once(EMCS_CUSTOMIZER_TEMPLATES . 'inline-form-customizer.php');
            include_once(EMCS_CUSTOMIZER_TEMPLATES . 'popup-text-customizer.php');
            include_once(EMCS_CUSTOMIZER_TEMPLATES . 'popup-button-customizer.php');
            ?>
        </div>
<?php
    }
}
