<?php
/**
 * Class that adds a misc notice
 *
 * @since v.2.0
 *
 * @return void
 */
class TRP_Add_General_Notices{
    public $notificationId = '';
    public $notificationMessage = '';
    public $notificationClass = '';
    public $startDate = '';
    public $endDate = '';
    public $force_show = false;//this attribute ignores the dismiss notification

    function __construct( $notificationId, $notificationMessage, $notificationClass = 'updated' , $startDate = '', $endDate = '', $force_show = false ){
        $this->notificationId = $notificationId;
        $this->notificationMessage = $notificationMessage;
        $this->notificationClass = $notificationClass;
        $this->force_show = $force_show;

        if( !empty( $startDate ) && time() < strtotime( $startDate ) )
            return;

        if( !empty( $endDate ) && time() > strtotime( $endDate ) )
            return;

        add_action( 'admin_notices', array( $this, 'add_admin_notice' ) );
        add_action( 'admin_init', array( $this, 'dismiss_notification' ) );
    }


    // Display a notice that can be dismissed in case the serial number is inactive
    function add_admin_notice() {
        global $current_user ;
        global $pagenow;

        $user_id = $current_user->ID;
        do_action( $this->notificationId.'_before_notification_displayed', $current_user, $pagenow );

        if ( current_user_can( 'manage_options' ) ){
            // Check that the user hasn't already clicked to ignore the message
            if ( ! get_user_meta($user_id, $this->notificationId.'_dismiss_notification' ) || $this->force_show  ) {//ignore the dismissal if we have force_show
                add_filter('safe_style_css', array( $this, 'allow_z_index_in_wp_kses'));
                echo wp_kses( apply_filters($this->notificationId.'_notification_message','<div class="'. $this->notificationClass .'" style="position:relative;'  . ((strpos($this->notificationClass, 'trp-narrow')!==false ) ? 'max-width: 825px;' : '') . '" >'.$this->notificationMessage.'</div>', $this->notificationMessage), [ 'div' => [ 'class' => [],'style' => [] ], 'p' => ['style' => [], 'class' => []], 'a' => ['href' => [], 'type'=> [], 'class'=> [], 'style'=>[], 'title'=>[],'target'=>[]], 'span' => ['class'=> []], 'strong' => [] ] );
                remove_filter('safe_style_css', array( $this, 'allow_z_index_in_wp_kses'));
            }
            do_action( $this->notificationId.'_notification_displayed', $current_user, $pagenow );
        }
        do_action( $this->notificationId.'_after_notification_displayed', $current_user, $pagenow );
    }

    function allow_z_index_in_wp_kses( $styles ) {
        $styles[] = 'z-index';
        $styles[] = 'position';
        return $styles;
    }

    function dismiss_notification() {
        global $current_user;

        $user_id = $current_user->ID;

        do_action( $this->notificationId.'_before_notification_dismissed', $current_user );

        // If user clicks to ignore the notice, add that to their user meta
        if ( isset( $_GET[$this->notificationId.'_dismiss_notification']) && '0' == $_GET[$this->notificationId.'_dismiss_notification'] )
            add_user_meta( $user_id, $this->notificationId.'_dismiss_notification', 'true', true );

        do_action( $this->notificationId.'_after_notification_dismissed', $current_user );
    }
}

Class TRP_Plugin_Notifications {

    public $notifications = array();
    private static $_instance = null;
    private $prefix = 'trp';
    private $menu_slug = 'options-general.php';
    public $pluginPages = array( 'translate-press', 'trp_addons_page', 'trp_license_key', 'trp_advanced_page', 'trp_machine_translation', 'trp_test_machine_api' );

    protected function __construct() {
        add_action( 'admin_init', array( $this, 'dismiss_admin_notifications' ), 200 );
        add_action( 'admin_init', array( $this, 'add_admin_menu_notification_counts' ), 1000 );
        add_action( 'admin_init', array( $this, 'remove_other_plugin_notices' ), 1001 );
    }


    function dismiss_admin_notifications() {
        if( ! empty( $_GET[$this->prefix.'_dismiss_admin_notification'] ) ) {
            $notifications = self::get_instance();
            $notifications->dismiss_notification( sanitize_text_field( $_GET[$this->prefix.'_dismiss_admin_notification'] ) );
        }

    }

    function add_admin_menu_notification_counts() {

        global $menu, $submenu;

        $notifications = TRP_Plugin_Notifications::get_instance();

        if( ! empty( $menu ) ) {
            foreach( $menu as $menu_position => $menu_data ) {
                if( ! empty( $menu_data[2] ) && $menu_data[2] == $this->menu_slug ) {
                    $menu_count = $notifications->get_count_in_menu();
                    if( ! empty( $menu_count ) )
                        $menu[$menu_position][0] .= '<span class="update-plugins '.$this->prefix.'-update-plugins"><span class="plugin-count">' . $menu_count . '</span></span>';
                }
            }
        }

        if( ! empty( $submenu[$this->menu_slug] ) ) {
            foreach( $submenu[$this->menu_slug] as $menu_position => $menu_data ) {
                $menu_count = $notifications->get_count_in_submenu( $menu_data[2] );
                if( ! empty( $menu_count ) )
                    $submenu[$this->menu_slug][$menu_position][0] .= '<span class="update-plugins '.$this->prefix.'-update-plugins"><span class="plugin-count">' . $menu_count . '</span></span>';
            }
        }
    }

    /* handle other plugin notifications on our plugin pages */
    function remove_other_plugin_notices(){
        /* remove all other plugin notifications except our own from the rest of the PB pages */
        if( $this->is_plugin_page() ) {
            global $wp_filter;
            if (!empty($wp_filter['admin_notices'])) {
                if (!empty($wp_filter['admin_notices']->callbacks)) {
                    foreach ($wp_filter['admin_notices']->callbacks as $priority => $callbacks_level) {
                        if (!empty($callbacks_level)) {
                            foreach ($callbacks_level as $key => $callback) {
                                if( is_array( $callback['function'] ) ){
                                    if( is_object($callback['function'][0])) {//object here
                                        if (strpos(get_class($callback['function'][0]), 'PMS_') !== 0 && strpos(get_class($callback['function'][0]), 'WPPB_') !== 0 && strpos(get_class($callback['function'][0]), 'TRP_') !== 0 && strpos(get_class($callback['function'][0]), 'WCK_') !== 0) {
                                            unset($wp_filter['admin_notices']->callbacks[$priority][$key]);//unset everything that doesn't come from our plugins
                                        }
                                    }
                                } else if( is_string( $callback['function'] ) ){//it should be a function name
                                    if (strpos($callback['function'], 'pms_') !== 0 && strpos($callback['function'], 'wppb_') !== 0 && strpos($callback['function'], 'trp_') !== 0 && strpos($callback['function'], 'wck_') !== 0) {
                                        unset($wp_filter['admin_notices']->callbacks[$priority][$key]);//unset everything that doesn't come from our plugins
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    /**
     *
     *
     */
    public static function get_instance() {
        if( is_null( self::$_instance ) )
            self::$_instance = new TRP_Plugin_Notifications();

        return self::$_instance;
    }


    /**
     *
     *
     */
    public function add_notification( $notification_id = '', $notification_message = '', $notification_class = 'update-nag', $count_in_menu = true, $count_in_submenu = array(), $show_in_all_backend = false, $non_dismissable = false ) {

        if( empty( $notification_id ) )
            return;

        if( empty( $notification_message ) )
            return;

        global $current_user;

        /**
         * added a $show_in_all_backend argument in version 1.4.6  that allows some notifications to be displayed on all the pages not just the plugin pages
         * we needed it for license notifications
         */
        $force_show = false;
        if( get_user_meta( $current_user->ID, $notification_id . '_dismiss_notification' ) ) {
            if( !$non_dismissable && !($this->is_plugin_page() && $show_in_all_backend) ){
                return;
            }
            else{
                $force_show = true; //if $show_in_all_backend is true then we ignore the dismiss on plugin pages, but on the rest of the pages it can be dismissed
            }
        }

        $this->notifications[$notification_id] = array(
            'id' 	  		   => $notification_id,
            'message' 		   => $notification_message,
            'class'   		   => $notification_class,
            'count_in_menu'    => $count_in_menu,
            'count_in_submenu' => $count_in_submenu
        );


        if( $this->is_plugin_page() || ($show_in_all_backend && isset( $GLOBALS['PHP_SELF']) && $GLOBALS['PHP_SELF'] === '/wp-admin/index.php' ) ) {
            new TRP_Add_General_Notices( $notification_id, $notification_message, $notification_class, '', '', $force_show );
        }

    }


    /**
     *
     *
     */
    public function get_notifications() {
        return $this->notifications;
    }


    /**
     *
     *
     */
    public function get_notification( $notification_id = '' ) {

        if( empty( $notification_id ) )
            return null;

        $notifications = $this->get_notifications();

        if( ! empty( $notifications[$notification_id] ) )
            return $notifications[$notification_id];
        else
            return null;

    }


    /**
     *
     *
     */
    public function dismiss_notification( $notification_id = '' ) {
        global $current_user;
        add_user_meta( $current_user->ID, $notification_id . '_dismiss_notification', 'true', true );
        do_action('trp_dismiss_notification', $notification_id, $current_user);
    }


    /**
     *
     *
     */
    public function get_count_in_menu() {
        $count = 0;

        foreach( $this->notifications as $notification ) {
            if( ! empty( $notification['count_in_menu'] ) )
                $count++;
        }

        return $count;
    }


    /**
     *
     *
     */
    public function get_count_in_submenu( $submenu = '' ) {

        if( empty( $submenu ) )
            return 0;

        $count = 0;

        foreach( $this->notifications as $notification ) {
            if( empty( $notification['count_in_submenu'] ) )
                continue;

            if( ! is_array( $notification['count_in_submenu'] ) )
                continue;

            if( ! in_array( $submenu, $notification['count_in_submenu'] ) )
                continue;

            $count++;
        }

        return $count;

    }


    /**
     * Test if we are an a page that belong to our plugin
     *
     */
    public function is_plugin_page() {
        if( !empty( $this->pluginPages ) ){
            foreach ( $this->pluginPages as $pluginPage ){
                if( ! empty( $_GET['page'] ) && false !== strpos( sanitize_text_field( $_GET['page'] ), $pluginPage ) )
                    return true;

                if( ! empty( $_GET['post_type'] ) && false !== strpos( sanitize_text_field( $_GET['post_type'] ), $pluginPage ) )
                    return true;

                if( ! empty( $_GET['post'] ) && false !== strpos( get_post_type( (int)$_GET['post'] ), $pluginPage ) )
                    return true;
            }
        }

        return false;
    }

}


class TRP_Trigger_Plugin_Notifications{

    private $settings;
    private $settings_obj;
    private $machine_translator_logger;

    function __construct($settings) {
        $this->settings = $settings;

        add_action( 'admin_init', array( $this, 'add_plugin_notifications' ) );
    }

    function add_plugin_notifications() {

        $notifications = TRP_Plugin_Notifications::get_instance();

        /* only show this notice if there isn't a pretty permalink structure enabled */
        if( !get_option('permalink_structure') ) {
            /* this must be unique */
            $notification_id = 'trp_new_add_on_invoices';

            $message = '<img style="float: left; margin: 10px 12px 10px 0; max-width: 80px;" src="' . TRP_PLUGIN_URL . 'assets/images/get_param_addon.jpg" />';
            $message .= '<p style="margin-top: 16px;padding-right:30px;">' . sprintf( __('You are not using a permalink structure! Please <a href="%s">enable</a> one or install our <a href="%s">"Language by GET parameter"</a> addon, so that TranslatePress can function properly.', 'translatepress-multilingual' ), admin_url('options-permalink.php'),admin_url('admin.php?page=trp_addons_page#language-by-get-parameter') ) . '</p>';
            //make sure to use the trp_dismiss_admin_notification arg
            $message .= '<a href="' . add_query_arg(array('trp_dismiss_admin_notification' => $notification_id)) . '" type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'translatepress-multilingual') . '</span></a>';

            $notifications->add_notification($notification_id, $message, 'trp-notice trp-narrow notice notice-info', true, array('translate-press'));
        }


        /* License Notifications */
        $license_details = get_option( 'trp_license_details' );
        $is_demosite = ( strpos(site_url(), 'https://demo.translatepress.com' ) !== false );
        $free_version = !class_exists( 'TRP_Handle_Included_Addons' );
        if( !empty($license_details) && !$is_demosite && !$free_version){
            /* if we have any invalid response for any of the addon show just the error notification and ignore any valid responses */
            if( !empty( $license_details['invalid'] ) ){

                //take the first addon details (it should be the same for the rest of the invalid ones)
                $license_detail = $license_details['invalid'][0];

                /* this must be unique */
                $notification_id = 'trp_invalid_license';

                $message = '<p style="padding-right:30px;">';

                    if( $license_detail->error == 'missing' )
                        $message .= '<p>'. sprintf( __('Your <strong>TranslatePress</strong> serial number is invalid or missing. <br/>Please %1$sregister your copy%2$s to receive access to automatic updates and support. Need a license key? %3$sPurchase one now%4$s' , 'translatepress-multilingual' ), "<a href='". admin_url('/admin.php?page=trp_license_key') ."'>", "</a>", "<a href='https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettings&utm_campaign=TP-SN-Purchase' target='_blank' class='button-primary'>", "</a>" ).'</p>';
                    elseif( $license_detail->error == 'expired' )
                        $message .= '<p>'. sprintf( __('Your <strong>TranslatePress</strong> license has expired. <br/>Please %1$sRenew Your Licence%2$s to continue receiving access to product downloads, automatic updates and support. %3$sRenew now %4$s' , 'translatepress-multilingual' ), "<a href='https://www.translatepress.com/account/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettings&utm_campaign=TP-Renewal' target='_blank'>", "</a>", "<a href='https://www.translatepress.com/account/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettings&utm_campaign=TP-Renewal' target='_blank' class='button-primary'>", "</a>" ). '</p>';
                    else
                        $message .= '<p>' . __( 'Something went wrong, please try again.', 'translatepress-multilingual' ) . '</p>';

                $message .= '</p>';

                $notifications->add_notification( $notification_id, $message, 'trp-notice notice error', true, array('translate-press'), true, true);
            }
            elseif( !empty( $license_details['valid'] ) ){

                //take the first addon details (it should be the same for the rest of the valid ones)
                $license_detail =  $license_details['valid'][0];

                if( isset($license_detail->auto_billing) && !$license_detail->auto_billing ) {//auto_billing was added by us in a filter on translatepress.com
                    if ((strtotime($license_detail->expires) - time()) / (60 * 60 * 24) < 30) {

                        /* this must be unique */
                        $notification_id = 'trp_will_expire_license';
                        $message = '<p style="padding-right:30px;">' . sprintf( __( 'Your <strong>TranslatePress</strong> license will expire on %1$s. Please %2$sRenew Your Licence%3$s to continue receiving access to product downloads, automatic updates and support.', 'translatepress-multilingual'), date_i18n( get_option( 'date_format' ), strtotime( $license_detail->expires, current_time( 'timestamp' ) ) ), '<a href="https://translatepress.com/account/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettings&utm_campaign=TP-Renewal" target="_blank">', '</a>'). '</p>';

                        if (!$notifications->is_plugin_page()) {
                            //make sure to use the trp_dismiss_admin_notification arg
                            $message .= '<a style="text-decoration: none;z-index:100;" href="' . add_query_arg(array('trp_dismiss_admin_notification' => $notification_id)) . '" type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'translatepress-multilingual') . '</span></a>';
                        }

                        $notifications->add_notification($notification_id, $message, 'trp-notice notice notice-info is-dismissible', true, array('translate-press'), true);
                    }
                }
            }
        }

	    /* this must be unique */
//	    $notification_id = 'trp_new_feature_image_translation';
//
//	    $message = '<p style="padding-right:30px;">' . __('NEW: Display different images based on language. Find out <a href="https://translatepress.com/docs/image-translation/" >how to translate images, sliders and more</a> from the TranslatePress editor.' , 'translatepress-multilingual' ) . '</p>';
//	    //make sure to use the trp_dismiss_admin_notification arg
//	    $message .= '<a href="' . add_query_arg(array('trp_dismiss_admin_notification' => $notification_id)) . '" type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'translatepress-multilingual') . '</span></a>';
//
//	    $notifications->add_notification($notification_id, $message, 'trp-notice trp-narrow notice notice-info', true, array('translate-press'));


	    /* String translation */
	    $notification_id = 'trp_new_feature_string_translation';

	    $message = '<p style="padding-right:30px;">' . __('NEW: Translate Emails and other plugin texts using String Translation. Find out <a href="https://translatepress.com/docs/translation-editor/string-translation/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpsettings&utm_campaign=TRP" >how to search for a specific text to translate</a>.' , 'translatepress-multilingual' ) . '</p>';
	    //make sure to use the trp_dismiss_admin_notification arg
	    $message .= '<a href="' . add_query_arg(array('trp_dismiss_admin_notification' => $notification_id)) . '" type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'translatepress-multilingual') . '</span></a>';

	    $notifications->add_notification($notification_id, $message, 'trp-notice trp-narrow notice notice-info', true, array('translate-press'));


	    /*
		 *
		 *  Machine translation enabled and  quota is met.
		 *
		 */
        $trp = TRP_Translate_Press::get_trp_instance();
        if ( ! $this->settings_obj )
            $this->settings_obj = $trp->get_component( 'settings' );

        if ( ! $this->machine_translator_logger )
            $this->machine_translator_logger = $trp->get_component( 'machine_translator_logger' );

        if( 'yes' === $this->settings['trp_machine_translation_settings']['machine-translation'] && $this->machine_translator_logger->quota_exceeded() ) {
            /* this must be unique */
            $notification_id = 'trp_machine_translation_quota_exceeded_'. date('Ymd');

            $message = '<img style="float: left; margin: 10px 12px 10px 0; max-width: 80px;" src="' . TRP_PLUGIN_URL . 'assets/images/get_param_addon.jpg" />';
            $message .= '<p style="margin-top: 16px;padding-right:30px;">';
                $message .= sprintf( __( 'The daily quota for machine translation characters exceeded. Please check the <strong>TranslatePress -> <a href="%s">Automatic Translation</a></strong> page for more information.', 'translatepress-multilingual' ), admin_url( 'admin.php?page=trp_machine_translation' ) );
            $message .= '</p>';
            //make sure to use the trp_dismiss_admin_notification arg
            $message .= '<a href="' . add_query_arg(array('trp_dismiss_admin_notification' => $notification_id)) . '" type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'translatepress-multilingual') . '</span></a>';

            $notifications->add_notification($notification_id, $message, 'trp-notice trp-narrow notice notice-info', true, array('translate-press'));
        }


        /*
         * One or more languages are unsupported by automatic translation.
         */
        $trp = TRP_Translate_Press::get_trp_instance();
        $machine_translator = $trp->get_component( 'machine_translator' );

        if ($machine_translator != null ) {
            if ( apply_filters( 'trp_mt_available_supported_languages_show_notice', true, $this->settings['translation-languages'], $this->settings ) &&
                'yes' === $this->settings['trp_machine_translation_settings']['machine-translation'] &&
                !$machine_translator->check_languages_availability( $this->settings['translation-languages'] )
            ) {
                /* this must be unique */
                $notification_id = 'trp_mt_unsupported_languages';

                $message = '<p style="margin-top: 16px;padding-right:30px;">';
                $message .= sprintf( __( 'One or more languages are unsupported by the automatic translation provider. Please check the <strong>TranslatePress -> <a href="%s">Automatic Translation</a></strong> page for more information.', 'translatepress-multilingual' ), admin_url( 'admin.php?page=trp_machine_translation#trp_unsupported_languages' ) );
                $message .= '</p>';
                //make sure to use the trp_dismiss_admin_notification arg
                $message .= '<a href="' . add_query_arg( array( 'trp_dismiss_admin_notification' => $notification_id ) ) . '" type="button" class="notice-dismiss"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'translatepress-multilingual' ) . '</span></a>';

                $notifications->add_notification( $notification_id, $message, 'trp-notice trp-narrow notice notice-info', true, array( 'translate-press' ) );
            }
        }

    }

}
