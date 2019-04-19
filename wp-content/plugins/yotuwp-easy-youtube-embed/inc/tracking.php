<?php

/**
 * 
 */
class PB_SDK_Tracking{

    private $data;

    public function __construct(){
        add_action( 'init',                         array( $this, 'schedule_send' ) );
        add_action( 'admin_notices',                array( $this, 'admin_notice'     ) );
        add_action( 'yotuwp_opt_into_tracking',     array( $this, 'check_for_optin'  ) );
		add_action( 'yotuwp_opt_out_of_tracking',   array( $this, 'check_for_optout' ) );
    }

    private function prepare_data() {

		$data = array();

		$theme_data = wp_get_theme();
		$theme      = $theme_data->Name . ' ' . $theme_data->Version;

		$data['php_version'] = phpversion();
		$data['version'] = YOTUWP_VERSION;
		$data['wp_version']  = get_bloginfo( 'version' );
		$data['server']      = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '';

		$data['multisite']   = is_multisite();
		$data['url']         = home_url();
		$data['domain']      = preg_replace( '/www\./i', '', $_SERVER['SERVER_NAME'] );
		$data['theme']       = $theme;
		$data['email']       = get_bloginfo( 'admin_email' );

		if (function_exists('wp_get_current_user')) {
			$userinfo = wp_get_current_user();
			if ( is_object($userinfo) && $userinfo->user_email !='')
				$data['email'] = $userinfo->user_email;
		}
		
		if( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins        = array_keys( get_plugins() );
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $key => $plugin ) {
			if ( in_array( $plugin, $active_plugins ) ) {
				unset( $plugins[ $key ] );
			}
		}

		$data['active_plugins']   = $active_plugins;
		$data['inactive_plugins'] = $plugins;
		$data['locale']           = get_locale();
		$data['last_sync']        = time();

		$this->data = $data;
	}

    public function admin_notice() {

		$hide_notice = get_option( 'yotuwp_tracking_notice', false );

		$date_now     = date( 'Y-m-d' );
		$install_date = get_option( 'yotuwp_install_date', $date_now);

		if ( $hide_notice || ( time() - strtotime( $install_date ) < 86400 ) ) {
			return;
		}

		if ( !current_user_can( 'manage_options' ) ) {
			return;
        }
        
		if (
			stristr( network_site_url( '/' ), 'dev'       ) !== false ||
			stristr( network_site_url( '/' ), 'localhost' ) !== false ||
			stristr( network_site_url( '/' ), ':8888'     ) !== false // This is common with MAMP on OS X
		) {
			update_option( 'yotuwp_tracking_notice', '1' );
		} else {

			$optin_url  = add_query_arg( 'ytwp_action', 'opt_into_tracking' );
			$optout_url = add_query_arg( 'ytwp_action', 'opt_out_of_tracking' );

			echo '<div class="updated"><p>';
			_e( 'Allow <strong>YotuWP - YouTube Gallery</strong> to track plugin usage? Become a contributor by opting in to our anonymous data tracking. We guarantee no sensitive data is collected.', 'yotuwp-easy-youtube-embed' );
			echo '&nbsp;<br/><a href="' . esc_url( $optin_url ) . '" class="button button-primary">' . __( 'Sure, I would love to help.', 'yotuwp-easy-youtube-embed' ) . '</a>';
			echo '&nbsp;<a href="' . esc_url( $optout_url ) . '" class="ytwp-skip">' . __( 'No, thanks', 'yotuwp-easy-youtube-embed' ) . '</a>';
			echo '</p></div>';
		}
	}

    public function schedule_send() {
		if ( yotuwp_doing_cron() ) {
			add_action( 'yotuwp_weekly_scheduled_events', array( $this, 'send_checkin' ) );
		}
    }
    
    public function check_for_optin( $data ) {

		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}

        update_option( 'yotuwp_allow_tracking', true );

		$this->send_checkin( true );

		update_option( 'yotuwp_tracking_notice', true );
        wp_redirect( remove_query_arg( 'ytwp_action' ) ); exit;
	}

	public function check_for_optout( $data ) {

		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}

		update_option( 'yotuwp_allow_tracking', false );
		update_option( 'yotuwp_tracking_notice', true );
		wp_redirect( remove_query_arg( 'ytwp_action' ) ); exit;
    }
    
    public function send_checkin( $override = false, $ignore_last_checkin = false ) {

		$home_url = trailingslashit( home_url() );
		
		// Allows us to stop our own site from checking in, and a filter for our additional sites
		if ( $home_url === 'https://www.yotuwp.com/' ) {
			return false;
		}

		if ( !get_option( 'yotuwp_allow_tracking', false ) && !$override ) {
			return false;
		}
        
		// Send a maximum of once per week
        $last_send = get_option( 'yotuwp_tracking_last_send' );
        
		if ( is_numeric( $last_send ) && $last_send > strtotime( '-2 week' ) && !$ignore_last_checkin ) {
			return false;
		}

		$this->prepare_data();

		$response = wp_remote_post( 'https://api.yotuwp.com/tracking/?ytwp_action=checkin&t='.time(), array(
			'method'      => 'POST',
			'timeout'     => 10,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking'    => false,
			'body'        => $this->data,
			'user-agent'  => 'YOTUWP/' . YOTUWP_VERSION . '; ' . get_bloginfo( 'url' )
		) );
		
		update_option( 'yotuwp_tracking_last_send', time() );

		return true;

	}

	public function get()
	{
		$this->prepare_data();
		return $this->data;
	}

}

$ytwp_tracking = new PB_SDK_Tracking();