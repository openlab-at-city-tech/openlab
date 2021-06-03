<?php
/**************************************************************
 *   Author: Pippin Williamson                                *
 *   Profile: http://codecanyon.net/user/mordauk              *
 **************************************************************/
define( 'DROPR_NOTIFIER_PLUGIN_NAME', 'Dropr – Dropbox Plugin for WordPress' ); 
define( 'DROPR_NOTIFIER_PLUGIN_SHORT_NAME', 'Dropr' );
define( 'DROPR_NOTIFIER_PLUGIN_FOLDER_NAME', 'dropr-for-dropbox' ); 
define( 'DROPR_NOTIFIER_PLUGIN_FILE_NAME', 'aswm-dropr.php' );
define( 'DROPR_NOTIFIER_PLUGIN_XML_FILE', 'http://awsm.in/dropr-documentation/notifier.xml' ); 
define( 'DROPR_PLUGIN_NOTIFIER_CACHE_INTERVAL', 86400 ); 
define( 'DROPR_PLUGIN_NOTIFIER_CODECANYON_USERNAME', 'awsmin' );  
function dropr_update_plugin_notifier_menu() {  
	if ( function_exists( 'simplexml_load_string' ) ) {
	    $xml 			= dropr_get_latest_plugin_version( DROPR_PLUGIN_NOTIFIER_CACHE_INTERVAL ); 
		$plugin_data 	= get_plugin_data( WP_PLUGIN_DIR . '/' . DROPR_NOTIFIER_PLUGIN_FOLDER_NAME . '/' . DROPR_NOTIFIER_PLUGIN_FILE_NAME );
		if ( (string) $xml->latest > (string) $plugin_data['Version'] ) {  
			if ( defined( 'DROPR_NOTIFIER_PLUGIN_SHORT_NAME' ) ) {
				$menu_name = DROPR_NOTIFIER_PLUGIN_SHORT_NAME;
			} else {
				$menu_name = DROPR_NOTIFIER_PLUGIN_NAME;
			}
			add_dashboard_page( DROPR_NOTIFIER_PLUGIN_NAME . ' Plugin Updates', $menu_name . ' <span class="update-plugins count-1"><span class="update-count">New Updates</span></span>', 'administrator', 'dropr-plugin-update-notifier', 'dropr_update_notifier');
		}
	}	
}
add_action('admin_menu', 'dropr_update_plugin_notifier_menu');  
function dropr_update_notifier_bar_menu() {
	if ( function_exists( 'simplexml_load_string' ) ) { 
		global $wp_admin_bar, $wpdb;
		if ( ! is_super_admin() || ! is_admin_bar_showing() ) 
		return;
		$xml 		= dropr_get_latest_plugin_version( DROPR_PLUGIN_NOTIFIER_CACHE_INTERVAL ); 
		$plugin_data 	= get_plugin_data( WP_PLUGIN_DIR . '/' . DROPR_NOTIFIER_PLUGIN_FOLDER_NAME . '/' .DROPR_NOTIFIER_PLUGIN_FILE_NAME ); 
		if( (string) $xml->latest > (string) $plugin_data['Version'] ) { 
			$wp_admin_bar->add_menu( array( 'id' => 'plugin_update_notifier', 'title' => '<span>' . DROPR_NOTIFIER_PLUGIN_NAME . ' <span id="ab-updates">New Updates</span></span>', 'href' => get_admin_url() . 'index.php?page=dropr-plugin-update-notifier' ) );
		}
	}
}
add_action( 'admin_bar_menu', 'dropr_update_notifier_bar_menu', 1000 );
function dropr_update_notifier() { 
	$xml 			= dropr_get_latest_plugin_version( DROPR_PLUGIN_NOTIFIER_CACHE_INTERVAL );
	$plugin_data 	= get_plugin_data( WP_PLUGIN_DIR . '/' . DROPR_NOTIFIER_PLUGIN_FOLDER_NAME . '/' .DROPR_NOTIFIER_PLUGIN_FILE_NAME );?>

	<style>
		.update-nag { display: none; }
		#instructions {max-width: 670px;}
		h3.title {margin: 30px 0 0 0; padding: 30px 0 0 0; border-top: 1px solid #ddd;}
	</style>

	<div class="wrap">

		<div id="icon-tools" class="icon32"></div>
		<h2><?php echo DROPR_NOTIFIER_PLUGIN_NAME ?> Plugin Updates</h2>
	    <div id="message" class="updated below-h2"><p><strong>There is a new version of the <?php echo DROPR_NOTIFIER_PLUGIN_NAME; ?> plugin available.</strong> You have version <?php echo $plugin_data['Version']; ?> installed. Update to version <?php echo $xml->latest; ?>.</p></div>
		
		<div id="instructions">
		    <h3>Update Download and Instructions</h3>
		    <p><strong>Please note:</strong> make a <strong>backup</strong> of the Plugin inside your WordPress installation folder <strong>/wp-content/plugins/<?php echo DROPR_NOTIFIER_PLUGIN_FOLDER_NAME; ?>/</strong></p>
		    <p>To update the Plugin, login to <a href="http://www.codecanyon.net/?ref=<?php echo DROPR_PLUGIN_NOTIFIER_CODECANYON_USERNAME; ?>">CodeCanyon</a>, head over to your <strong>downloads</strong> section and re-download the plugin like you did when you bought it.</p>
		    <p>Extract the zip's contents, look for the extracted plugin folder, and after you have all the new files upload them using FTP to the <strong>/wp-content/plugins/<?php echo DROPR_NOTIFIER_PLUGIN_FOLDER_NAME; ?>/</strong> folder overwriting the old ones (this is why it's important to backup any changes you've made to the plugin files).</p>
		    <p>If you didn't make any changes to the plugin files, you are free to overwrite them with the new ones without the risk of losing any plugins settings, and backwards compatibility is guaranteed.</p>
		</div>
	    
	    <h3 class="title">Changelog</h3>
	    <?php echo $xml->changelog; ?>

	</div>
    
<?php } 
function dropr_get_latest_plugin_version( $interval ) {
	$notifier_file_url = DROPR_NOTIFIER_PLUGIN_XML_FILE;	
	$db_cache_field = 'notifier-cache';
	$db_cache_field_last_updated = 'notifier-cache-last-updated';
	$last = get_option( $db_cache_field_last_updated );
	$now = time();
	if ( ! $last || ( ( $now - $last ) > $interval ) ) {
		if( function_exists( 'curl_init' ) ) { 
			$ch = curl_init( $notifier_file_url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
			$cache = curl_exec( $ch );
			curl_close( $ch );
		} else {
			$cache = file_get_contents( $notifier_file_url ); 
		}
		if ( $cache ) {			
			update_option( $db_cache_field, $cache );
			update_option( $db_cache_field_last_updated, time() );
		} 
		$notifier_data = get_option( $db_cache_field );
	}
	else {
		$notifier_data = get_option( $db_cache_field );
	}
	if( strpos( (string) $notifier_data, '<notifier>' ) === false ) {
		$notifier_data = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><changelog></changelog></notifier>';
	}
	$xml = simplexml_load_string( $notifier_data ); 
	return $xml;
}