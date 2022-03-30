<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

class Heartbeat {

	public static function register() {
		//add_filter( 'heartbeat_settings', array( 'Inc\Base\Heartbeat', 'heartbeat_settings' ) );
		//add_filter( 'heartbeat_received', array( 'Inc\Base\Heartbeat', 'heartbeat_received' ), 10, 3 );
		//add_filter( 'heartbeat_nopriv_received', array( 'Inc\Base\Heartbeat', 'heartbeat_received' ), 10, 3 );
		//add_action( 'admin_print_footer_scripts', array( 'Inc\Base\Heartbeat', 'heartbeat_script' ) );
	}

	function heartbeat_received( $response, $data, $screen_id ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'zpm_projects';
		$query = "SELECT * FROM $table_name";
		$projects = $wpdb->query($query);

	    if ( isset( $data['zephyr-project-manager'] ) ) {
	        $response['zephyr-project-manager'] = array(
	            'projects' => $projects
	        );
	    }

	    return $response;
	}

	public static function heartbeat_settings( $settings ) {
	   $settings['interval'] = 60;
	   return $settings;
	}

	public static function heartbeat_script() {
		?>
		<script>
			jQuery(document).ready(function($) {
			    wp.heartbeat.interval( 'fast' );
			    $(document).on('heartbeat-send.zephyr-project-manager', function(e, data) {
					if ( data.hasOwnProperty( 'zephyr-project-manager' ) ) {
			        }
					data['zephyr-project-manager'] = 1;	//need some data to kick off AJAX call
				});
			    $(document).on( 'heartbeat-tick.zephyr-project-manager', function( event, data ) {
			        if ( data.hasOwnProperty( 'zephyr-project-manager' ) ) {
			        }
				}); 
			});
		</script>
		<?php	
	}
}