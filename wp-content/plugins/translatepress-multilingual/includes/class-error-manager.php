<?php

/**
 * Class TRP_Error_Manager
 */
class TRP_Error_Manager{
    protected $settings;
    /* @var TRP_Settings */
    protected $trp_settings;

    public function __construct( $settings){
        $this->settings = $settings;
    }

    public function is_error_manager_disabled(){
        return apply_filters( 'trp_disable_error_manager', false );
    }

    /**
     * Record specified error in trp_db_errors option
     *
     * @param $error_details array Suggested fields:
    'last_error'  => $this->db->last_error,
    'details'   => 'Insert general description',
    'disable_automatic_translations' => bool
     */
    public function record_error( $error_details ){
        if ( $this->is_error_manager_disabled() ){
            return;
        }

        $option = get_option('trp_db_errors', array(
            'notifications' => array(),
            'errors' => array()
        ));

        if ( !isset( $option ) || !is_array( $option['errors'] ) ){
            $option['errors'] = [];
        }

        if ( count( $option['errors'] ) >= 5 ){
            // only record the last few errors to avoid huge db options
            array_shift($option['errors'] );
        }
        $error_details['date_time'] = date('Y-m-d H:i:s');
        $error_details['timestamp'] = time();

        $error_message = wp_kses( sprintf(  __('<strong>TranslatePress</strong> encountered SQL errors. <a href="%s" title="View TranslatePress SQL Errors">Check out the errors</a>.', 'translatepress-multilingual'), admin_url( 'admin.php?page=trp_error_manager' ) ), array('a' => array('href' => array(), 'title' => array()), 'strong' => array()));

        // specific actions for this error: add notification message and disable machine translation
        if ( isset( $error_details['disable_automatic_translations'] ) && $error_details['disable_automatic_translations'] === true ){

            if ( ! $this->trp_settings ) {
                $trp = TRP_Translate_Press::get_trp_instance();
                $this->trp_settings = $trp->get_component( 'settings' );
            }

            $mt_settings_option = get_option('trp_machine_translation_settings', $this->trp_settings->get_default_trp_machine_translation_settings() );
            if ( $mt_settings_option['machine-translation'] != 'no' ) {
                $mt_settings_option['machine-translation'] = 'no';
                update_option('trp_machine_translation_settings', $mt_settings_option );

                // filter is needed to block automatic translation in this execution. The settings don't update throughout the plugin for this request. Only the next request will have machine translation turned off.
                add_filter( 'trp_disable_automatic_translations_due_to_error', '__return_true' );

                $error_message = wp_kses( __('Automatic translation has been disabled.','translatepress-multilingual'), array('strong' => array() ) ) . ' ' . $error_message ;
            }
            if ( !isset( $option['notifications']['disable_automatic_translations'] ) ) {

                $option['notifications']['disable_automatic_translations' ] = array(
                    // we need a unique ID so that after the notice is dismissed and this type of error appears again, it's not already marked as dismissed for that user
                    'notification_id' => 'disable_automatic_translations' . time(),
                    'message' => $error_message
                );
            }
        }
        if ( isset( $error_details['notification_id'] ) && isset( $error_details['message'] ) ) {
            $option['notifications'][$error_details['notification_id']] = array(
                'notification_id' => $error_details['notification_id'],
                'message' => $error_details['message'] .' ' . $error_message
            );
        }


        $option['errors'][] = $error_details;
        update_option( 'trp_db_errors', $option );
    }


    /**
     * Remove notification from trp_db_errors too (not only user_meta) when dismissed by user
     *
     * Necessary in order to allow logging of this error in the future. Basically allow creation of new notifications about this error.
     *
     * Hooked to trp_dismiss_notification
     *
     * @param $notification_id
     * @param $current_user
     */
    public function clear_notification_from_db($notification_id, $current_user ){
        $option = get_option( 'trp_db_errors', false );
        if ( isset( $option['notifications'] ) ) {
            foreach ($option['notifications'] as $key => $logged_notification ){
                if ( $notification_id == '' || $logged_notification['notification_id'] === $notification_id || $key === $notification_id ) {
                    unset( $option['notifications'][$key] );
                    update_option('trp_db_errors', $option );
                    break;
                }
            }
        }
    }

    /**
     * When enabling machine translation, clear the Automatic translation has been disabled message
     *
     * @param $mt_settings
     * @return string $mt_settings
     */
    public function clear_disable_machine_translation_notification_from_db( $mt_settings ){
        if ( $mt_settings['machine-translation'] === 'yes' ){
            $this->clear_notification_from_db('disable_automatic_translations', null);
        }

        return $mt_settings;
    }

    /**
     * Disable the notification after the link is clicked.
     */


    public function disable_error_after_click_link(){

        $link = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

        if($link === 'trp_error_manager') {
            $this->clear_notification_from_db('', null);
        }

    }

    /**
     *
     * Hooked to admin_init
     */
    public function show_notification_about_errors(){
        if ( $this->is_error_manager_disabled() ){
            return;
        }
        $option = get_option( 'trp_db_errors', false );
        if ( $option !== false && isset($option['notifications'])) {
            foreach( $option['notifications'] as $logged_notification ) {
                $notifications = TRP_Plugin_Notifications::get_instance();

                $notification_id = $logged_notification['notification_id'];

                $message = '<p style="padding-right:30px;">' . $logged_notification['message'] . '</p>';
                //make sure to use the trp_dismiss_admin_notification arg
                $message .= '<a href="' . add_query_arg(array('trp_dismiss_admin_notification' => $notification_id)) . '" type="button" class="notice-dismiss" style="text-decoration: none;z-index:100;"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'translatepress-multilingual') . '</span></a>';

                $notifications->add_notification($notification_id, $message, 'trp-notice trp-narrow notice error is-dismissible', true, array('translate-press'), true);
            }
        }
    }

    public function register_submenu_errors_page(){
        add_submenu_page( 'TRPHidden', 'TranslatePress Error Manager', 'TRPHidden', apply_filters( 'trp_settings_capability', 'manage_options' ), 'trp_error_manager', array( $this, 'error_manager_page_content' ) );
    }

    public function error_manager_page_content(){
        require_once TRP_PLUGIN_DIR . 'partials/error-manager-page.php';
    }

    public function output_db_errors( $html_content ){
        $option = get_option( 'trp_db_errors', false );
        if ( $option !== false && isset($option['errors']) ) {
            $html_content .= '<h2>' . esc_html__('Logged errors', 'translatepress-multilingual') . '</h2>';
            $html_content .= '<p>' . esc_html__('These are the most recent 5 errors logged by TranslatePress:', 'translatepress-multilingual' ) . '</p>';
            $html_content .= '<table>';
            $option['errors'] = array_reverse($option['errors']);
            foreach ($option['errors'] as $count => $error) {
                $count = ( is_int( $count) ) ? $count + 1 : $count;
                $html_content .= '<tr><td>' . esc_html($count) . '</td></tr>';
                foreach( $error as $key => $error_detail ){
                    $error_detail = ($error_detail === true ) ? esc_html__('Yes', 'translatepress-multilingual') : $error_detail;
                    $html_content .= '<tr><td><strong>' . esc_html($key ) . '</strong></td>' . '<td>' .esc_html( $error_detail ) . '</td></tr>';
                }

            }
            $html_content .= '</table>';
        }
        return $html_content;
    }

    /**
     * Hooked to trp_error_manager_page_output
     *
     * @param $html_content
     * @return string
     */
    public function show_instructions_on_how_to_fix( $html_content ){
        $html_content .= '<h2>' . esc_html__('Why are these errors occuring', 'translatepress-multilingual') . '</h2>';
        $html_content .= '<p>' . esc_html__('If TranslatePress detects something wrong when executing queries on your database, it may disable the Automatic Translation feature in order to avoid any extra charging by Google/DeepL. Automatic Translation needs to be manually turned on, after you solve the issues.', 'translatepress-multilingual') . '</p>';
        $html_content .= '<p>' . esc_html__('The SQL errors detected can occur for various reasons including missing tables, missing permissions for the SQL user to create tables or perform other operations, problems after site migration or changes to SQL server configuration.', 'translatepress-multilingual') . '</p>';

        $html_content .= '<h2>' . esc_html__('What you can do in this situation', 'translatepress-multilingual') . '</h2>';

        $html_content .= '<h4>' . esc_html__('Plan A.', 'translatepress-multilingual') . '</h4>';
        $html_content .= '<p>' . esc_html__('Go to Settings -> TranslatePress -> General tab and Save Settings. This will regenerate the tables using your current SQL settings. Check if no more errors occur while browsing your website in a translated language. Look at the timestamps of the errors to make sure you are not seeing the old errors. Only the most recent 5 errors are displayed.', 'translatepress-multilingual') . '</p>';

        $html_content .= '<h4>' . esc_html__('Plan B.', 'translatepress-multilingual') . '</h4>';
        $html_content .= '<p>' . esc_html__('If your problem isn\'t solved, try the following steps:', 'translatepress-multilingual') . '</p>';
        $html_content .= '<ol>';
        $html_content .= '<li>' . esc_html__('Create a backup of your database', 'translatepress-multilingual') . '</li>';
        $html_content .= '<li>' . esc_html__('Create a copy of each translation table where you encounter errors. You can copy the table within the same database (trp_dictionary_en_us_es_es_COPY for example) -- perform this step only if you want to keep the current translations', 'translatepress-multilingual') . '</li>';
        $html_content .= '<li>' . esc_html__('Remove the trouble tables by executing the DROP function on them', 'translatepress-multilingual') . '</li>';
        $html_content .= '<li>' . esc_html__('Go to Settings -> TranslatePress -> General tab and Save Settings. This will regenerate the tables using your current SQL server.', 'translatepress-multilingual') . '</li>';
        $html_content .= '<li>' . esc_html__('Copy the relevant content from the duplicated tables (trp_dictionary_en_us_es_es_COPY for example) in the newly generated table (trp_dictionary_en_us_es_es) -- perform this step only if you want to keep the current translations', 'translatepress-multilingual') . '</li>';
        $html_content .= '<li>' . esc_html__('Test it to see if everything is working. If something went wrong, you can restore the backup that you\'ve made at the first step. Check if no more errors occur while browsing your website in a translated language. Look at the timestamps of the errors to make sure you are not seeing the old errors. Only the most recent 5 errors are displayed.', 'translatepress-multilingual') . '</li>';
        $html_content .= '</ol>';

        $html_content .= '<h4>' . esc_html__('Plan C.', 'translatepress-multilingual') . '</h4>';
        $html_content .= '<p>' . esc_html__('If your problem still isn\'t solved, try asking your hosting about your errors. The most common issue is missing permissions for the SQL user, such as the Create Tables permission.', 'translatepress-multilingual') . '</p>';

        return $html_content;
    }
}
