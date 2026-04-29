<?php

if ( !defined( 'ABSPATH' ) )
    exit();

class TRP_AI_Words_Notification {

    private $settings;

    /**
     * Returns the default AI words notification threshold based on product type.
     * 200 for free users, 5000 for paid users.
     */
    public static function get_default_threshold() {
        $tp_product_name = TRP_Translate_Press::set_tp_product_name_static();
        if ( array_key_exists( 'translatepress-multilingual', $tp_product_name ) ) {
            return 200;
        }
        return 5000;
    }

    public function __construct( $settings ) {
        $this->settings = $settings;

        add_action( 'set_transient_trp_mtapi_cached_quota', array( $this, 'check_quota_and_notify' ), 10, 3 );
        add_action( 'trp_ai_words_delayed_notification', array( $this, 'send_delayed_notification_email' ) );
    }

    /**
     * Main hook callback. Fires every time the trp_mtapi_cached_quota transient is set.
     *
     * @param mixed  $value      The transient value (characters remaining).
     * @param int    $expiration Time until expiration in seconds.
     * @param string $transient  The name of the transient.
     */
    public function check_quota_and_notify( $value, $expiration, $transient ) {
        $mt_settings = $this->settings['trp_machine_translation_settings'];

        // Only act when automatic translation is on, engine is mtapi and notification is enabled
        if ( empty( $mt_settings['machine-translation'] ) || $mt_settings['machine-translation'] !== 'yes' ) {
            return;
        }
        if ( empty( $mt_settings['translation-engine'] ) || $mt_settings['translation-engine'] !== 'mtapi' ) {
            return;
        }
        if ( empty( $mt_settings['ai_words_notification_enabled'] ) || $mt_settings['ai_words_notification_enabled'] !== 'yes' ) {
            return;
        }

        // Don't notify when the license is missing or invalid.
        // The transient is set to 0 for errors like "Site not found" or "Site is not active",
        // which are license issues, not actual low quota.
        if ( get_option( 'trp_license_status' ) !== 'valid' ) {
            return;
        }

        $words_remaining = ceil( $value / 5 );
        $threshold       = $this->get_effective_threshold( $mt_settings );

        // If above threshold: clear sent flag and cancel any pending cron
        if ( $words_remaining > $threshold ) {
            delete_option( 'trp_ai_words_notification_sent' );
            wp_clear_scheduled_hook( 'trp_ai_words_delayed_notification' );
            return;
        }

        // Below threshold - check if we already sent or is pending. Possible options: 'not_set', 'pending', true
        $notification_sent = get_option( 'trp_ai_words_notification_sent', 'not_set' );
        if ( $notification_sent !== 'not_set' ) {
            return;
        }

        update_option( 'trp_ai_words_notification_sent', 'pending' );
        // Determine free vs paid
        if ( $this->is_free_user() ) {
            $this->schedule_delayed_email( $words_remaining );
        } else {
            $this->send_notification_email( $words_remaining );
            update_option( 'trp_ai_words_notification_sent', true );
        }
    }

    /**
     * Check if the current installation is using the free version.
     */
    private function is_free_user() {
        $tp_product_name = TRP_Translate_Press::set_tp_product_name_static();
        return array_key_exists( 'translatepress-multilingual', $tp_product_name );
    }

    /**
     * Get the effective threshold from settings.
     */
    private function get_effective_threshold( $mt_settings ) {
        if ( isset( $mt_settings['ai_words_notification_threshold'] ) && $mt_settings['ai_words_notification_threshold'] !== '' ) {
            return absint( $mt_settings['ai_words_notification_threshold'] );
        }
        return self::get_default_threshold();
    }

    /**
     * Get the notification email address from settings, falling back to admin_email.
     */
    private function get_notification_email( $mt_settings = null ) {
        if ( $mt_settings === null ) {
            $mt_settings = $this->settings['trp_machine_translation_settings'];
        }
        if ( !empty( $mt_settings['ai_words_notification_email'] ) ) {
            return $mt_settings['ai_words_notification_email'];
        }
        return get_option( 'admin_email' );
    }

    /**
     * Send the notification email immediately.
     */
    private function send_notification_email( $words_remaining ) {
        $to      = $this->get_notification_email();
        $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
        /* translators: %s: site name */
        $subject = sprintf( __( '[%s] Low TranslatePress AI Words', 'translatepress-multilingual' ), $blogname );
        $body    = $this->build_email_body( $words_remaining );

        wp_mail( $to, $subject, $body );
    }

    /**
     * Schedule a delayed email for free users (1 day delay).
     */
    private function schedule_delayed_email( $words_remaining ) {
        // Store the word count so the cron callback can use it
        update_option( 'trp_ai_words_notification_pending_count', $words_remaining );

        if ( ! wp_next_scheduled( 'trp_ai_words_delayed_notification' ) ) {
            wp_schedule_single_event( time() + DAY_IN_SECONDS, 'trp_ai_words_delayed_notification' );
        }
    }

    /**
     * Cron callback for delayed free-user notification.
     * Re-checks quota and settings before sending.
     */
    public function send_delayed_notification_email() {
        $mt_settings = get_option( 'trp_machine_translation_settings', array() );

        // Re-check that automatic translation is on
        if ( empty( $mt_settings['machine-translation'] ) || $mt_settings['machine-translation'] !== 'yes' ) {
            return;
        }

        // Re-check that notification is still enabled
        if ( empty( $mt_settings['ai_words_notification_enabled'] ) || $mt_settings['ai_words_notification_enabled'] !== 'yes' ) {
            return;
        }

        // Re-check engine is still mtapi
        if ( empty( $mt_settings['translation-engine'] ) || $mt_settings['translation-engine'] !== 'mtapi' ) {
            return;
        }

        // Re-check license is still valid
        if ( get_option( 'trp_license_status' ) !== 'valid' ) {
            return;
        }

        // Re-check if already sent. Possible options: 'not_set', 'pending', true
        $notification_sent = get_option( 'trp_ai_words_notification_sent', 'not_set' );
        if ( $notification_sent === true ) {
            return;
        }

        // Prefer live transient data, fall back to stored pending count from when cron was scheduled
        $cached_quota    = get_transient( 'trp_mtapi_cached_quota' );
        $words_remaining = ( $cached_quota !== false )
            ? ceil( $cached_quota / 5 )
            : get_option( 'trp_ai_words_notification_pending_count', 0 );

        $threshold = $this->get_effective_threshold( $mt_settings );
        if ( $words_remaining > $threshold ) {
            // Quota recovered since cron was scheduled, reset flags
            delete_option( 'trp_ai_words_notification_sent' );
            delete_option( 'trp_ai_words_notification_pending_count' );
            return;
        }

        $to      = $this->get_notification_email( $mt_settings );
        $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
        /* translators: %s: site name */
        $subject = sprintf( __( '[%s] Low TranslatePress AI Words', 'translatepress-multilingual' ), $blogname );
        $body    = $this->build_email_body( $words_remaining );

        wp_mail( $to, $subject, $body );

        update_option( 'trp_ai_words_notification_sent', true );
        delete_option( 'trp_ai_words_notification_pending_count' );
    }

    /**
     * Build the plain text email body.
     */
    private function build_email_body( $words_remaining ) {
        $site_name       = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
        $manage_url      = admin_url( 'admin.php?page=trp_machine_translation' );
        $account_url     = 'https://translatepress.com/account/?utm_source=tp-automatic-translation&utm_medium=client-site&utm_campaign=ai_words_notification';
        $formatted_words = number_format( $words_remaining );

        if ( $this->is_free_user() ) {
            $upgrade_text = __( 'upgrade your plan', 'translatepress-multilingual' );
        } else {
            $upgrade_text = __( 'upgrade your plan or get extra AI Words', 'translatepress-multilingual' );
        }

        $message  = $site_name . ' - ' . site_url() . "\r\n\r\n";
        $message .= __( 'TranslatePress AI words are running low on your site.', 'translatepress-multilingual' ) . "\r\n\r\n";
        /* translators: %s: formatted number of words remaining */
        $message .= sprintf( __( 'Current status: %s words remaining', 'translatepress-multilingual' ), $formatted_words ) . "\r\n\r\n";
        $message .= __( 'Once the remaining AI words run out, automatic translation through TranslatePress AI will be paused.', 'translatepress-multilingual' ) . "\r\n\r\n";
        /* translators: 1: upgrade action text, 2: account URL */
        $message .= sprintf( __( 'To continue translating your content, please %1$s over at %2$s', 'translatepress-multilingual' ), $upgrade_text, $account_url ) . "\r\n\r\n";
        /* translators: %s: notification settings URL */
        $message .= sprintf( __( 'Manage Notification: %s', 'translatepress-multilingual' ), $manage_url ) . "\r\n";

        return $message;
    }
}
