<?php


if ( !defined('ABSPATH' ) )
    exit();

class TRP_Woocommerce_Emails{

    private static $email_contexts = [];
    private static $sending_order_email = false;
    private static $email_global_names = [
        'TRP_LANGUAGE',
        'TRP_LANGUAGE_COPY',
        'TRP_LANGUAGE_ORIGINAL',
        'trp_translated_gettext_texts',
        'trp_translated_gettext_texts_language',
        'trp_wpdb_prefix',
    ];

    public function __construct(){}

    public function initialize_hooks(){

        // Save current language for user every time wp_footer is loaded
        add_action( 'wp_footer', array( $this, 'save_current_language' ) );

        // In order for the email translation to work properly, WC_VERSION needs to be >= 6.8.0
        if( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '6.8.0' ) >= 0 ) {

            // Save user language on checkout
            add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_language_on_checkout' ), 10, 2 );
            add_action( 'woocommerce_store_api_checkout_update_order_meta', array( $this, 'save_language_on_checkout_store_api' ), 10, 1 );

            // WooCommerce email notifications
            add_action( 'woocommerce_order_status_processing_to_cancelled_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_on-hold_to_cancelled_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_completed_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_cancelled_to_on-hold_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_cancelled_to_processing_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_failed_to_processing_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_on-hold_to_processing_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_fully_refunded_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_partially_refunded_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_pending_to_failed_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_on-hold_to_failed_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_pending_to_completed_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_failed_to_completed_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_cancelled_to_completed_notification', array( $this, 'store_email_order_id' ), 5, 1 );
            add_action( 'woocommerce_order_status_failed_notification', array( $this, 'store_email_order_id' ), 5, 1 );

            // WooCommerce emails when resent by admin
            add_action( 'woocommerce_before_resend_order_emails', array( $this, 'prepare_order_id_for_resend_emails' ), 5, 2 );
            // WooCommerce note to customer email
            add_action( 'woocommerce_new_customer_note_notification', array( $this, 'prepare_order_id_for_note_emails' ), 5, 1 );

            // Hijack execution to translate emails in user language accordingly
            add_filter( 'woocommerce_allow_switching_email_locale', array( $this, 'trp_woo_setup_locale' ), 10, 2 );
            add_filter( 'woocommerce_allow_restoring_email_locale', array( $this, 'trp_woo_restore_locale' ), 10, 2 );
            add_filter( 'woocommerce_mail_callback', [ $this, 'wrap_mail_callback' ], 99999, 2 );
        }
    }

    /**
     * Save user language on WooCommerce checkout
     *
     * @param $order_id
     * @param $posted
     * @return void
     */
    public function save_language_on_checkout( $order_id, $posted ) {
        global $TRP_LANGUAGE, $TRP_EMAIL_ORDER;
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();

        $TRP_EMAIL_ORDER = $order_id;
        if( $user_id != 0 ){

            $user_preferred_language = get_user_meta($user_id, 'trp_language', true);
            $always_use_this_language = get_user_meta( $user_id, 'trp_always_use_this_language', true );

            if (!empty($always_use_this_language) && $always_use_this_language == 'yes' && !empty($user_preferred_language) ){
                update_user_meta( $user_id, 'trp_language', $user_preferred_language );
                trp_woo_hpos_manipulate_post_meta( $order_id, 'trp_language', $user_preferred_language, 'update' );

            }else {
                update_user_meta( $user_id, 'trp_language', $TRP_LANGUAGE );
                trp_woo_hpos_manipulate_post_meta( $order_id, 'trp_language', $TRP_LANGUAGE, 'update' );
            }
        }
        else{
            trp_woo_hpos_manipulate_post_meta( $order_id, 'trp_language', $TRP_LANGUAGE, 'update' );
        }
    }

    /**
     * Fires when the Checkout Block/Store API updates an order's meta data.
     *
     * @param $order
     * @return void
     */
    public function save_language_on_checkout_store_api( $order ) {
        global $TRP_LANGUAGE, $TRP_EMAIL_ORDER;
        $user_id = $order->get_user_id();
        $order_id = $order->get_id();
        $TRP_EMAIL_ORDER = $order_id;
        if( $user_id != 0 ){

            $user_preferred_language = get_user_meta($user_id, 'trp_language', true);
            $always_use_this_language = get_user_meta( $user_id, 'trp_always_use_this_language', true );

            if (!empty($always_use_this_language) && $always_use_this_language == 'yes' && !empty($user_preferred_language) ){
                update_user_meta( $user_id, 'trp_language', $user_preferred_language );
                trp_woo_hpos_manipulate_post_meta( $order_id, 'trp_language', $user_preferred_language, 'update' );
            }else {
                update_user_meta( $user_id, 'trp_language', $TRP_LANGUAGE );
                trp_woo_hpos_manipulate_post_meta( $order_id, 'trp_language', $TRP_LANGUAGE, 'update' );
            }
        }
        else{
            trp_woo_hpos_manipulate_post_meta( $order_id, 'trp_language', $TRP_LANGUAGE, 'update' );
        }
    }



    /**
     * Save current user language
     *
     * The hook was added on 'wp_footer' to prevent logout or backend admin actions from resetting $TRP_LANGUAGE to TRP default language
     *
     * @return void
     */
    public function save_current_language(){
        global $TRP_LANGUAGE;
        $user_id = get_current_user_id();

        if( $user_id > 0 ){
            $language_meta = get_user_meta( $user_id, 'trp_language', true);
            $always_use_this_language = get_user_meta( $user_id, 'trp_always_use_this_language', true );

            if( $language_meta != $TRP_LANGUAGE && $always_use_this_language !== 'yes') {
                update_user_meta( $user_id, 'trp_language', $TRP_LANGUAGE );
            }
        }
    }

    /**
     * Store order id in a separate global to access its value later in the execution
     *
     * @param $order_id
     * @return void
     */
    public function store_email_order_id( $order_id ) {
        global $TRP_EMAIL_ORDER;
        $TRP_EMAIL_ORDER = $order_id;
    }

    /**
     * Prepare order id for resend emails
     *
     * @param $order
     * @param $email_type
     * @return void
     */
    public function prepare_order_id_for_resend_emails( $order, $email_type ) {
        if( $email_type == 'customer_invoice' )
            $this->store_email_order_id( $order->get_id() );
    }

    /**
     * Prepare order id for note emails
     *
     * @param $note_and_order_id
     * @return void
     */
    public function prepare_order_id_for_note_emails( $note_and_order_id ) {
        $this->store_email_order_id( $note_and_order_id['order_id'] );
    }

    /**
     * Set the language for WooCommerce emails according to the user information:
     * user profile language for admin AND language metadata for customer
     *
     * @param $bool
     * @param $wc_email
     * @return false
     */
    public function trp_woo_setup_locale( $bool, $wc_email ) {
        global $TRP_EMAIL_ORDER, $TRP_LANGUAGE;
        $order = false;

        $is_customer_email = $wc_email->is_customer_email();

        if ( $TRP_EMAIL_ORDER  ) {
            $order = wc_get_order( $TRP_EMAIL_ORDER );
        }

        $trp_settings = TRP_Translate_Press::get_trp_instance()->get_component( 'settings' );
        $settings     = $trp_settings->get_settings();

        $default_language = $settings["default-language"];

        /**
         * At this point in the execution, $wc_email->get_recipient() returns null and throws a PHP warning inside WooCommerce /woocommerce/includes/emails/class-wc-email.php
         * This is why we use $wc_email->get_option( 'recipient' ). It properly returns the recipient in the case of admin emails.
         *
         * We treat customer emails differently.
         */
        $recipients = $wc_email->get_option( 'recipient' );

        /**
         * When dealing with customer emails, recipient will not be set. We need to retrieve it via get_billing_email().
         */
        if ( $is_customer_email && is_a( $order, 'WC_Order' ) && empty( $recipients ) )
            $recipients = $order->get_billing_email();

        $recipients = $this->normalize_email_recipients( $recipients );

        $user_id  = 0;

        if( $is_customer_email ){
            $customer_email_data = $this->get_customer_email_data( $order );
            $language            = $customer_email_data['language'];
            $user_id             = $customer_email_data['user_id'];
        }
        else{
            $language = $this->get_admin_email_language( $recipients, $default_language );
        }

        $language = apply_filters( 'trp_woo_email_language', $language, $is_customer_email, $recipients, $user_id );

        if ( empty( $language ) )
            $language = $TRP_LANGUAGE;

        // Save the request state so the previous language and gettext overrides can be restored.
        $context = [
            'email'   => $wc_email,
            'globals' => [],
            'filters' => [],
        ];
        foreach ( self::$email_global_names as $name ) {
            if ( array_key_exists( $name, $GLOBALS ) ) {
                $context['globals'][ $name ] = $GLOBALS[ $name ];
            }
        }
        $email_filters = [
            [ 'trp_before_translate_content', 'trp_reset_language', 99999999 ],
            [ 'plugin_locale', 'trp_get_locale', 99999999 ],
            [ 'trp_allow_gettext_write', '__return_true', 10 ],
            [ 'trp_processing_gettext_is_needed', '__return_true', 10 ],
        ];
        foreach ( $email_filters as $filter ) {
            if ( has_filter( $filter[0], $filter[1] ) === false ) {
                $context['filters'][] = $filter;
            }
        }
        self::$email_contexts[] = $context;
        $index = count( self::$email_contexts ) - 1;
        self::$email_contexts[ $index ]['switched_locale'] = trp_switch_language( $language );

        $this->reload_woocommerce_textdomain();

        // Skip gettext bootstrap for en_US as secondary language due to WooCommerce not providing a .mo file for en_US.
        // This causes TP to load the default language gettext.
        $skip_bootstrap_for_en_us_fallback = ( $language === 'en_US' && $default_language !== 'en_US' );
        if ( ! $skip_bootstrap_for_en_us_fallback ) {
            $this->bootstrap_trp_gettext_for_emails();
        }

        // calls necessary because the default additional_content field of an email is localized before this point and stored in a variable in the previous locale
        $wc_email->init_form_fields();
        $wc_email->init_settings();

        return false;
    }

    /**
     * Normalize a WooCommerce recipient list to trimmed email strings.
     *
     * @param string|array $recipients
     * @return array
     */
    private function normalize_email_recipients( $recipients ) {
        if ( empty( $recipients ) ) {
            return array();
        }

        if ( ! is_array( $recipients ) ) {
            $recipients = explode( ',', $recipients );
        }

        return array_values( array_filter( array_map( 'trim', $recipients ) ) );
    }

    /**
     * Resolve the language used for admin emails.
     *
     * Multiple admin recipients receive the same email body, so we use the first
     * configured recipient as the source of truth for the email language.
     *
     * @param array  $recipients
     * @param string $default_language
     * @return string
     */
    private function get_admin_email_language( $recipients, $default_language ) {
        if ( empty( $recipients ) ) {
            return $this->get_order_email_language();
        }

        $registered_user = get_user_by( 'email', $recipients[0] );

        if ( $registered_user ) {
            return $this->get_registered_user_email_language( $registered_user, $default_language );
        }

        return $this->get_order_email_language();
    }

    /**
     * Resolve the locale used for an admin recipient.
     *
     * @param WP_User $registered_user
     * @param string  $default_language
     * @return string
     */
    private function get_registered_user_email_language( $registered_user, $default_language ) {
        if ( ! empty( $registered_user->locale ) ) {
            return $registered_user->locale;
        }

        $wplang = get_option( 'WPLANG' );

        return ! empty( $wplang ) ? $wplang : $default_language;
    }

    /**
     * Resolve the language stored on the order tied to the current WooCommerce email.
     *
     * @return string
     */
    private function get_order_email_language() {
        global $TRP_EMAIL_ORDER, $TRP_LANGUAGE;

        if ( empty( $TRP_EMAIL_ORDER ) ) {
            return $TRP_LANGUAGE;
        }

        $order_language = trp_woo_hpos_get_post_meta( $TRP_EMAIL_ORDER, 'trp_language', true );

        return ! empty( $order_language ) ? $order_language : $TRP_LANGUAGE;
    }

    /**
     * Resolve the language and user id used for customer emails.
     *
     * @param WC_Order|false $order
     * @return array
     */
    private function get_customer_email_data( $order ) {
        global $TRP_LANGUAGE;

        $customer_email_data = array(
            'language' => $TRP_LANGUAGE,
            'user_id'  => 0,
        );

        if ( ! $order ) {
            return $customer_email_data;
        }

        $customer_email_data['user_id'] = $order->get_user_id();

        if ( $customer_email_data['user_id'] > 0 ) {
            $customer_email_data['language'] = get_user_meta( $customer_email_data['user_id'], 'trp_language', true );
        } else {
            $customer_email_data['language'] = $this->get_order_email_language();
        }

        return $customer_email_data;
    }

    /**
     * Restore locale after email is sent
     *
     * @param $bool
     * @param $wc_email
     * @return false
     */
    public function trp_woo_restore_locale( $bool, $wc_email ) {
        $context = end( self::$email_contexts );
        if ( ! $context || $context['email'] !== $wc_email ) {
            return $bool;
        }
        array_pop( self::$email_contexts );

        foreach ( $context['filters'] as $filter ) {
            remove_filter( $filter[0], $filter[1], $filter[2] );
        }
        foreach ( self::$email_global_names as $name ) {
            if ( array_key_exists( $name, $context['globals'] ) ) {
                $GLOBALS[ $name ] = $context['globals'][ $name ];
            } else {
                unset( $GLOBALS[ $name ] );
            }
        }
        if ( ! empty( $context['switched_locale'] ) ) {
            restore_previous_locale();
        }
        $this->reload_woocommerce_textdomain();

        return false;

    }

    /** Whether WooCommerce is currently building a localized email. */
    public static function is_translating_email() {
        return ! empty( self::$email_contexts );
    }

    /** Order emails can safely pass their gettext markers to the regular translator. */
    public static function is_order_email() {
        $context = end( self::$email_contexts );
        return $context && is_a( $context['email']->object, 'WC_Order' );
    }

    public static function is_sending_order_email() {
        return self::$sending_order_email;
    }

    /** Limit the admin wp_mail exception to the actual order-email delivery callback. */
    public function wrap_mail_callback( $callback, $wc_email ) {
        $context = end( self::$email_contexts );
        if ( ! $context || $context['email'] !== $wc_email || ! self::is_order_email() ) {
            return $callback;
        }

        return function ( ...$args ) use ( $callback ) {
            $previous = self::$sending_order_email;
            self::$sending_order_email = true;
            try {
                return call_user_func_array( $callback, $args );
            } finally {
                self::$sending_order_email = $previous;
            }
        };
    }

    /** Load the selected email language and include gettext from email extensions. */
    private function bootstrap_trp_gettext_for_emails() {
        $trp             = TRP_Translate_Press::get_trp_instance();
        $gettext_manager = $trp->get_component( 'gettext_manager' );
        $pg              = $gettext_manager->get_gettext_component( 'process_gettext' );

        if ( !$trp->get_component( 'machine_translator') || get_class( $trp->get_component( 'machine_translator' ) ) === TRP_Machine_Translator::class )
            $trp->init_machine_translation(); // Machine translator should be initialized by the get_trp_instance() call. In the case of cron jobs, it is not - so we initialize it here manually.

        // Bypass processing_gettext_is_needed usual checks. Otherwise, the below method calls wouldn't go through
        add_filter( 'trp_processing_gettext_is_needed', '__return_true' );
        add_filter( 'trp_allow_gettext_write', '__return_true' );

        // An existing hook does not mean its dictionary belongs to this email language.
        $gettext_manager->create_gettext_translated_global();
        $gettext_handlers = [
            'gettext' => [ 'process_gettext_strings_no_context', 3 ],
            'gettext_with_context' => [ 'process_gettext_strings_with_context', 4 ],
            'ngettext' => [ 'process_ngettext_strings', 5 ],
            'ngettext_with_context' => [ 'process_ngettext_strings_with_context', 6 ],
        ];
        foreach ( $gettext_handlers as $hook => $handler ) {
            $callback = [ $pg, $handler[0] ];
            if ( has_filter( $hook, $callback ) === false ) {
                add_filter( $hook, $callback, 100, $handler[1] );
                $index = count( self::$email_contexts ) - 1;
                self::$email_contexts[ $index ]['filters'][] = [ $hook, $callback, 100 ];
            }
        }
    }

    function reload_woocommerce_textdomain() {
        $domain = 'woocommerce';

        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        $custom_translation_path  = WP_LANG_DIR . '/woocommerce/woocommerce-' . $locale . '.mo';
        $global_translation_path  = WP_LANG_DIR . '/plugins/woocommerce-' . $locale . '.mo';
        $bundled_translation_path = trailingslashit( WC()->plugin_path() ) . 'i18n/languages/woocommerce-' . $locale . '.mo';

        unload_textdomain( $domain );

        // Custom file present: mimic WC
        if ( is_readable( $custom_translation_path ) ) {
            load_textdomain( $domain, $custom_translation_path );

            if ( is_readable( $global_translation_path ) ) {
                load_textdomain( $domain, $global_translation_path );
            }

            return true;
        }

        if ( is_readable( $global_translation_path ) ) {
            load_textdomain( $domain, $global_translation_path );
            return true;
        }

        if ( is_readable( $bundled_translation_path ) ) {
            load_textdomain( $domain, $bundled_translation_path );
            return true;
        }

        return false;
    }

}
