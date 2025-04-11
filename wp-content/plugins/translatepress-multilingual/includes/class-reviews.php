<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Reviews
 */
class TRP_Reviews{
    protected $settings;
    /* @var TRP_Settings */
    protected $trp_settings;
    protected $date_of_install;

    public function __construct( $settings){
        $this->settings = $settings;
        $this->maybe_set_date_of_install();
    }

    /**
     * Marks timestamp TP install if not already set
     *
     * Started tracking timestamp of installation since version 1.9.8
     */
    public function maybe_set_date_of_install(){
        $trp_db_stored_data = get_option( 'trp_db_stored_data', array() );
        if ( !isset( $trp_db_stored_data['install_timestamp'] ) ){
            $trp_db_stored_data['install_timestamp'] = time();
            update_option('trp_db_stored_data', $trp_db_stored_data );
        }
        $this->date_of_install = $trp_db_stored_data['install_timestamp'];
    }

    public function get_date_of_install(){
        return $this->date_of_install;
    }

    public function should_it_show_review_notice(){

        // conditions
        $time_to_wait_condition = WEEK_IN_SECONDS;
        $number_of_translations_condition = 25;
        $how_often_to_check = DAY_IN_SECONDS;


        $trp_db_stored_data = get_option( 'trp_db_stored_data', array() );
        $notification_dismissed = isset( $trp_db_stored_data['trp_review_notification_dismiss_notification'] ) && $trp_db_stored_data['trp_review_notification_dismiss_notification'] === true;
        $site_meets_conditions_for_review = isset( $trp_db_stored_data['trp_site_meets_conditions_for_review'] ) && $trp_db_stored_data['trp_site_meets_conditions_for_review'] === true;

        if ( !$notification_dismissed && !$site_meets_conditions_for_review ) {
            $trp                = TRP_Translate_Press::get_trp_instance();
            $machine_translator = $trp->get_component( 'machine_translator' );
            $trp_query          = $trp->get_component( 'query' );

            $transient = get_transient( 'trp_checked_if_site_meets_conditions_for_review' );
            if ( $transient === false ) {
                // Do sql checks because transient has expired. Transient is used to ensure checking is not made on every page load.

                if ( time() - $this->get_date_of_install() > $time_to_wait_condition ) {

                    foreach ( $this->settings['translation-languages'] as $language ) {
                        if ( $language === $this->settings['default-language']){
                            continue;
                        }
                        if ( $trp_query->minimum_rows_with_status( $language, $number_of_translations_condition, 2 ) ) {
                            $site_meets_conditions_for_review = true;
                            break;
                        }

                        if ( $machine_translator->is_available( array() ) && $trp_query->minimum_rows_with_status( $language, $number_of_translations_condition, 1 ) ) {
                            $site_meets_conditions_for_review = true;
                            break;
                        }
                    }
                }
                set_transient( 'trp_checked_if_site_meets_conditions_for_review', 'yes', $how_often_to_check );
            }
        }

        if ( !isset( $trp_db_stored_data['trp_site_meets_conditions_for_review'] ) && $site_meets_conditions_for_review ){
            // once a site meets the conditions, remember so that we don't check anymore
            $trp_db_stored_data['trp_site_meets_conditions_for_review'] = true;
            update_option( 'trp_db_stored_data', $trp_db_stored_data );
        }

        // actual logic for showing reviews or not
        $show_review_notice = ( !$notification_dismissed && $site_meets_conditions_for_review );

        return apply_filters( 'trp_show_notification_about_review', $show_review_notice, $notification_dismissed, $site_meets_conditions_for_review );
    }

    /**
     * Show an admin notice inviting the user to review TP
     *
     * hooked to admin_init
     */
    public function display_review_notice(){

        if ( !$this->should_it_show_review_notice() ){
            return;
        }
        $notifications = TRP_Plugin_Notifications::get_instance();
        /* this must be unique */
        $notification_id = 'trp_review_notification';
        $url = 'https://wordpress.org/support/plugin/translatepress-multilingual/reviews/?filter=5#new-post';

        $message = '<p style="margin-top: 16px;font-size: 14px;padding-right:20px">';
        $message .= wp_kses( __( "Hello! Seems like you've been using <strong>TranslatePress</strong> for a while now to translate your website. That's awesome! ", 'translatepress-multilingual' ), array('strong' => array() ) );
        $message .= '</p>';

        $message .= '<p style="font-size: 14px">';
        $message .= esc_html__( "If you can spare a few moments to rate it on WordPress.org it would help us a lot (and boost my motivation).", 'translatepress-multilingual' );
        $message .= '</p>';

        $message .= '<p>';
        $message .= esc_html__( "~ Razvan, developer of TranslatePress", 'translatepress-multilingual' ) ;
        $message .= '</p>';

        // buttons for OK / No, thanks
        $message .= '<p>';
        $message .= '<a href="' . esc_url( $url ) . '" title="' . esc_attr__( 'Rate TranslatePress on WordPress.org plugin page', 'translatepress-multilingual' ) . '" class="button-primary" style="margin-right: 20px">' . esc_html__( "Ok, I will gladly help!", 'translatepress-multilingual' ) . '</a>';
        $message .= '<a href="' . add_query_arg( array( 'trp_dismiss_admin_notification' => $notification_id ) ) . '"  title="' . esc_attr__( 'Dismiss this notice.', 'translatepress-multilingual' ) . '" class="button-secondary" >' . esc_html__( "No, thanks.", 'translatepress-multilingual' ) . '</a>';
        $message .= '</p>';
        //make sure to use the trp_dismiss_admin_notification arg
        $message .= '<a href="' . add_query_arg( array( 'trp_dismiss_admin_notification' => $notification_id ) ) . '" style="text-decoration:none" type="button" class="notice-dismiss"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'translatepress-multilingual' ) . '</span></a>';

        $notifications->add_notification( $notification_id, $message, 'trp-notice trp-narrow notice notice-info', true, array( 'translate-press' ), true );

    }

    /**
     * Set option to not display notification
     *
     * Necessary because the plugin notification system is originally user meta based.
     * Change this behaviour so that dismissing the notification is known site-wide
     *
     * hooked to trp_dismiss_notification
     *
     * @param $notification_id
     * @param $current_user
     */
    public function dismiss_notification($notification_id, $current_user){
        if ( $notification_id === 'trp_review_notification' ) {
            $trp_db_stored_data = get_option( 'trp_db_stored_data', array() );
            $trp_db_stored_data['trp_review_notification_dismiss_notification'] = true;
            update_option('trp_db_stored_data', $trp_db_stored_data );
        }
    }
}
