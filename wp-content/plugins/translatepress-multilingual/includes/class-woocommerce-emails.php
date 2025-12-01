<?php


if ( !defined('ABSPATH' ) )
    exit();

class TRP_Woocommerce_Emails{

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

        if ( empty( $recipients ) ) {
            $recipients = [];
        } elseif ( !is_array($recipients) ) {
            $recipients = explode( ',', $recipients );
        }

        $language = $TRP_LANGUAGE;
        $user_id  = 0;

        if( $is_customer_email ){
            if ( $order ) {
                $user_id = $order->get_user_id();
                if ( $user_id > 0 ) {
                    $language = get_user_meta( $user_id, 'trp_language', true );
                } else {
                    $language = trp_woo_hpos_get_post_meta( $TRP_EMAIL_ORDER, 'trp_language', true );
                }
            }
        }
        else{
            if( ! empty( $recipients ) && count( $recipients ) == 1 ){
                $registered_user = get_user_by( 'email', $recipients[0] );
                if( $registered_user ){
                    // If language is set to site default, user object won't have a locale set. Fallback to WPLANG. In case WPLANG is not set either, fallback to trp_language
                    if ( !empty( $registered_user->locale ) ){
                        $language = $registered_user->locale;
                    } else {
                        $language = get_option( 'WPLANG' ) ?? get_user_meta( $registered_user->ID, 'trp_language', true );
                    }
                } else {
                    $language = trp_woo_hpos_get_post_meta( $TRP_EMAIL_ORDER, 'trp_language', true );
                }
            }
        }

        $language = apply_filters( 'trp_woo_email_language', $language, $is_customer_email, $recipients, $user_id );

        if ( empty( $language ) )
            $language = $TRP_LANGUAGE;

        trp_switch_language( $language );

        WC()->load_plugin_textdomain();

        $this->bootstrap_trp_gettext_for_email_language( $language );

        // calls necessary because the default additional_content field of an email is localized before this point and stored in a variable in the previous locale
        $wc_email->init_form_fields();
        $wc_email->init_settings();

        return false;
    }

    /**
     * Restore locale after email is sent
     *
     * @param $bool
     * @param $wc_email
     * @return false
     */
    public function trp_woo_restore_locale( $bool, $wc_email ) {

        trp_restore_language();
        WC()->load_plugin_textdomain();

        return false;

    }

    /**
     * Ensure TranslatePress gettext is active for the current email language.
     *
     * WooCommerce's emails are not always triggered in a normal frontend request.
     * They can be sent asynchronously (e.g. admin changing an order status, a
     * payment processor callback marking the order as paid, or cron/CLI jobs).
     *
     * In those cases, TranslatePress’s gettext global ($trp_translated_gettext_texts)
     * and filters are never initialized in time because the email is rendered much
     * later in the request lifecycle.
     *
     * To guarantee that TP’s database-backed translations are available for
     * the strings in WooCommerce emails, we:
     *   - check if at least one of TP’s WooCommerce gettext filters is already attached,
     *   - if not, force creation of the gettext global and force-attach the filters.
     *
     * This way, even when emails are sent outside a normal page render, the
     * gettext translations stored in TranslatePress are applied correctly.
     */
    private function bootstrap_trp_gettext_for_email_language( $language ) {
        $trp             = TRP_Translate_Press::get_trp_instance();
        $gettext_manager = $trp->get_component( 'gettext_manager' );
        $pg              = $gettext_manager->get_gettext_component( 'process_gettext' );

        // If at least one core handler is already attached, return
        if ( has_filter( 'gettext', [ $pg, 'woocommerce_process_gettext_strings_no_context' ] ) )
            return;

        if ( !$trp->get_component( 'machine_translator') || get_class( $trp->get_component( 'machine_translator' ) ) === TRP_Machine_Translator::class )
            $trp->init_machine_translation(); // Machine translator should be initialized by the get_trp_instance() call. In the case of cron jobs, it is not - so we initialize it here manually.

        // Bypass processing_gettext_is_needed usual checks. Otherwise, the below method calls wouldn't go through
        add_filter( 'trp_processing_gettext_is_needed', '__return_true' );

        $gettext_manager->create_gettext_translated_global();
        $gettext_manager->call_gettext_filters( 'woocommerce_' );
    }

}