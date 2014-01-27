<?php
/*
Plugin Name: WP Smush.it
Plugin URI: http://wordpress.org/extend/plugins/wp-smushit/
Description: Reduce image file sizes and improve performance using the <a href="http://smush.it/">Smush.it</a> API within WordPress.
Author: WPMU DEV
Version: 1.6.5.4
Author URI: http://premium.wpmudev.org/
Textdomain: wp_smushit
*/

/*
This plugin was originally developed by Alex Dunae. 
http://dialect.ca/
*/

/* 
Copyright 2007-2013 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( !function_exists( 'download_url' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

if ( !class_exists( 'WpSmushit' ) ) {

class WpSmushit {

	var $version = "1.6.5.4";

	/**
     * Constructor
     */
	function __construct( ) {

		/**
		 * Constants
		 */
		define( 'SMUSHIT_REQ_URL', 'http://www.smushit.com/ysmush.it/ws.php?img=%s' );
		define( 'SMUSHIT_BASE_URL', 'http://www.smushit.com/' );

		define( 'WP_SMUSHIT_DOMAIN', 'wp_smushit' );
		define( 'WP_SMUSHIT_UA', "WP Smush.it/{$this->version} (+http://wordpress.org/extend/plugins/wp-smushit/)" );
		define( 'WP_SMUSHIT_PLUGIN_DIR', dirname( plugin_basename(__FILE__) ) );
		define( 'WP_SMUSHIT_MAX_BYTES', 1048576 );

		// The number of images (including generated sizes) that can return errors before abandoning all hope.
		// N.B. this doesn't work with the bulk uploader, since it creates a new HTTP request
		// for each image.  It does work with the bulk smusher, though.
		define( 'WP_SMUSHIT_ERRORS_BEFORE_QUITTING', 3 * count( get_intermediate_image_sizes( ) ) );

		define( 'WP_SMUSHIT_AUTO', intval( get_option( 'wp_smushit_smushit_auto', 0) ) );
		define( 'WP_SMUSHIT_TIMEOUT', intval( get_option( 'wp_smushit_smushit_timeout', 60) ) );
		define( 'WP_SMUSHIT_ENFORCE_SAME_URL', get_option( 'wp_smushit_smushit_enforce_same_url', 'on') );

		if ((!isset($_GET['action'])) || ($_GET['action'] != "wp_smushit_manual")) {
			define( 'WP_SMUSHIT_DEBUG', get_option( 'wp_smushit_smushit_debug', '') );
		} else {
			define( 'WP_SMUSHIT_DEBUG', '' );
		}
		
		/*
		Each service has a setting specifying whether it should be used automatically on upload.
		Values are:
			-1  Don't use (until manually enabled via Media > Settings)
			0   Use automatically
			n   Any other number is a Unix timestamp indicating when the service can be used again
		*/

		define('WP_SMUSHIT_AUTO_OK', 0);
		define('WP_SMUSHIT_AUTO_NEVER', -1);

		/**
		 * Hooks
		 */
		if ( WP_SMUSHIT_AUTO == WP_SMUSHIT_AUTO_OK ) {
		  add_filter( 'wp_generate_attachment_metadata', array( &$this, 'resize_from_meta_data' ), 10, 2 );
		}
		add_filter( 'manage_media_columns', array( &$this, 'columns' ) );
		add_action( 'manage_media_custom_column', array( &$this, 'custom_column' ), 10, 2 );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_action_wp_smushit_manual', array( &$this, 'smushit_manual' ) );
		add_action( 'admin_head-upload.php', array( &$this, 'add_bulk_actions_via_javascript' ) );
		add_action( 'admin_action_bulk_smushit', array( &$this, 'bulk_action_handler' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
	}
	
	function WpSmushit( ) {
		$this->__construct( );
	}
	
	/**
	 * Plugin setting functions
	 */
	function register_settings( ) {
		add_settings_section( 'wp_smushit_settings', 'WP Smush.it', array( &$this, 'settings_cb' ), 'media' );

		add_settings_field( 'wp_smushit_smushit_auto', __( 'Use Smush.it on upload?', WP_SMUSHIT_DOMAIN ), 
			array( &$this, 'render_auto_opts' ),  'media', 'wp_smushit_settings' );

		add_settings_field( 'wp_smushit_smushit_timeout', __( 'How many seconds should we wait for a response from Smush.it?', WP_SMUSHIT_DOMAIN ), 
			array( &$this, 'render_timeout_opts' ), 'media', 'wp_smushit_settings' );

		add_settings_field( 'wp_smushit_smushit_enforce_same_url', __( 'Enforce image URL is same as Home', WP_SMUSHIT_DOMAIN ), 
			array( &$this, 'render_enforce_same_url_opts' ), 'media', 'wp_smushit_settings' );

		add_settings_field( 'wp_smushit_smushit_debug', __( 'Enable debug processing', WP_SMUSHIT_DOMAIN ), 
			array( &$this, 'render_debug_opts' ), 'media', 'wp_smushit_settings' );

		register_setting( 'media', 'wp_smushit_smushit_auto' );
		register_setting( 'media', 'wp_smushit_smushit_timeout' );
		register_setting( 'media', 'wp_smushit_smushit_enforce_same_url' );
		register_setting( 'media', 'wp_smushit_smushit_debug' );
	}

	function settings_cb( ) {
	}

	function render_auto_opts( ) {
		$key = 'wp_smushit_smushit_auto';
		$val = intval( get_option( $key, WP_SMUSHIT_AUTO_OK ) );
		printf( "<select name='%1\$s' id='%1\$s'>",  esc_attr( $key ) );
		echo '<option value=' . WP_SMUSHIT_AUTO_OK . ' ' . selected( WP_SMUSHIT_AUTO_OK, $val ) . '>'. __( 'Automatically process on upload', WP_SMUSHIT_DOMAIN ) . '</option>';
		echo '<option value=' . WP_SMUSHIT_AUTO_NEVER . ' ' . selected( WP_SMUSHIT_AUTO_NEVER, $val ) . '>'. __( 'Do not process on upload', WP_SMUSHIT_DOMAIN ) . '</option>';

		if ( $val > 0 ) {
		  printf( '<option value="%d" selected="selected">', $val ) . 
		  printf( __( 'Temporarily disabled until %s', WP_SMUSHIT_DOMAIN ), date( 'M j, Y \a\t H:i', $val ) ).'</option>';
		}
		echo '</select>';
	}

	function render_timeout_opts( $key ) {
		$key = 'wp_smushit_smushit_timeout';
		$val = intval( get_option( $key, WP_SMUSHIT_AUTO_OK ) );
		printf( "<input type='text' name='%1\$s' id='%1\$s' value='%2\%d'>",  esc_attr( $key ), intval( get_option( $key, 60 ) ) );
	}

	function render_enforce_same_url_opts(  ) {
		$key = 'wp_smushit_smushit_enforce_same_url';
		$val = get_option( $key, WP_SMUSHIT_ENFORCE_SAME_URL );
		?><input type="checkbox" name="<?php echo $key ?>" <?php if ($val == 'on') { echo ' checked="checked" '; } ?>/> <?php 
		echo '<strong>'. get_option('home'). '</strong><br />'.__( 'By default the plugin will enforce that the image URL is the same domain as the home. If you are using a sub-domain pointed to this same host or an external Content Delivery Network (CDN) you want to unset this option.', WP_SMUSHIT_DOMAIN );
	}

	function render_debug_opts(  ) {
		$key = 'wp_smushit_smushit_debug';
		$val = get_option( $key, WP_SMUSHIT_DEBUG );
		?><input type="checkbox" name="<?php echo $key ?>" <?php if ($val) { echo ' checked="checked" '; } ?>/> <?php _e( 'If you are having trouble with the plugin enable this option can reveal some information about your system needed for support.', WP_SMUSHIT_DOMAIN );
	}

	// default is 6hrs
	function temporarily_disable( $seconds = 21600 ) {
		update_option( 'wp_smushit_smushit_auto', time() + $seconds );
	}	
	
	function admin_init( ) {
		load_plugin_textdomain(WP_SMUSHIT_DOMAIN, false, dirname(plugin_basename(__FILE__)).'/languages/');
		wp_enqueue_script( 'common' );
	}

	function admin_menu( ) {
		add_media_page( 'Bulk Smush.it', 'Bulk Smush.it', 'edit_others_posts', 'wp-smushit-bulk', array( &$this, 'bulk_preview' ) );
	}

	function bulk_preview( ) {
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv('no-gzip', 1);
		}
		@ini_set('output_buffering','on');
		@ini_set('zlib.output_compression', 0);
		@ini_set('implicit_flush', 1);

		$attachments = null;
		$auto_start = false;

		if ( isset($_REQUEST['ids'] ) ) {
			$attachments = get_posts( array(
				'numberposts' => -1,
				'include' => explode(',', $_REQUEST['ids']),
				'post_type' => 'attachment',
				'post_mime_type' => 'image'
			));
			$auto_start = true;
		} else {
			$attachments = get_posts( array(
				'numberposts' => -1,
				'post_type' => 'attachment',
				'post_mime_type' => 'image'
			));
		}
		?>
		<div class="wrap"> 
			<div id="icon-upload" class="icon32"><br /></div><h2><?php _e( 'Bulk WP Smush.it', WP_SMUSHIT_DOMAIN ) ?></h2>
		<?php 

		if ( sizeof($attachments) < 1 ) {
			_e( "<p>You don't appear to have uploaded any images yet.</p>", WP_SMUSHIT_DOMAIN );
		} else { 
			if ( empty($_POST) && !$auto_start ){ // instructions page
		
				_e("<p>This tool will run all of the images in your media library through the WP Smush.it web service. Any image already processed will not be reprocessed. Any new images or unsuccessful attempts will be processed.</p>", WP_SMUSHIT_DOMAIN );
				_e("<p>As part of the Yahoo! Smush.it API this plugin wil provide a URL to each of your images to be processed. The Yahoo! service will download the image via the URL. The Yahoo Smush.it service will then return a URL to this plugin of the new version of the image. This image will be downloaded and replace the original image on your server.</p>", WP_SMUSHIT_DOMAIN );

				_e('<p>Limitations of using the Yahoo Smush.it API</p>', WP_SMUSHIT_DOMAIN);
				?>
				<ol>
					<li><?php _e('The image MUST be less than 1 megabyte in size. This is a limit of the Yahoo! service not this plugin.', WP_SMUSHIT_DOMAIN); ?></li>
					<li><?php _e('The image MUST be accessible via a non-https URL. The Yahoo! Smush.it service will not handle https:// image URLs. This is a limit of the Yahoo! service not this plugin.', WP_SMUSHIT_DOMAIN); ?></li>
					<li><?php _e('The image MUST publicly accessible server. As the Yahoo! Smush.it service needs to download the image via a URL the image needs to be on a public server and not a local local development system. This is a limit of the Yahoo! service not this plugin.', WP_SMUSHIT_DOMAIN); ?></li>
					<li><?php _e('The image MUST be local to the site. This plugin cannot update images stored on Content Delivery Networks (CDN)', WP_SMUSHIT_DOMAIN); ?></li>
				</ol>
				<?php
				printf( __( "<p><strong>This is an experimental feature.</strong> Please post any feedback to the %s.</p>", WP_SMUSHIT_DOMAIN ), '<a href="http://wordpress.org/tags/wp-smushit">'. __( 'WordPress WP Smush.it forums', WP_SMUSHIT_DOMAIN ). '</a>' );

				?>
				<hr />
				<?php printf( __( "<p>We found %d images in your media library. Be forewarned, <strong>it will take <em>at least</em> %d minutes</strong> to process all these images if they have never been smushed before.</p>", WP_SMUSHIT_DOMAIN ), sizeof($attachments), sizeof($attachments) * 3 / 60 ); ?>
				<form method="post" action="">
					<?php wp_nonce_field( 'wp-smushit-bulk', '_wpnonce'); ?>
					<button type="submit" class="button-secondary action"><?php _e( 'Run all my images through WP Smush.it right now', WP_SMUSHIT_DOMAIN ) ?></button>
					<?php _e( "<p><em>N.B. If your server <tt>gzip</tt>s content you may not see the progress updates as your files are processed.</em></p>", WP_SMUSHIT_DOMAIN ); ?>
					<?php 
						if (WP_SMUSHIT_DEBUG) {
							_e( "<p>DEBUG mode is currently enabled. To disable see the Settings > Media page.</p>", WP_SMUSHIT_DOMAIN ); 
						}
					?>
				</form>
				<?php
			} else { // run the script

				if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'wp-smushit-bulk' ) || !current_user_can( 'edit_others_posts' ) ) {
					wp_die( __( 'Cheatin&#8217; uh?' ) );
				}


				@ob_implicit_flush( true );
				@ob_end_flush();
				foreach( $attachments as $attachment ) {
					printf( __("<p>Processing <strong>%s</strong>&hellip;<br />", WP_SMUSHIT_DOMAIN), esc_html( $attachment->post_name ) );
					$original_meta = wp_get_attachment_metadata( $attachment->ID, true );
					
					$meta = $this->resize_from_meta_data( $original_meta, $attachment->ID, false );
					printf( "&mdash; [original] %d x %d: ", intval($meta['width']), intval($meta['height']) );

					if ((isset( $original_meta['wp_smushit'] )) 
					 && ( $original_meta['wp_smushit'] == $meta['wp_smushit']) 
					 && (stripos( $meta['wp_smushit'], 'Smush.it error' ) === false ) ) {
					 	if ((stripos( $meta['wp_smushit'], '<a' ) === false)
					 	 && (stripos( $meta['wp_smushit'], __('No savings', WP_SMUSHIT_DOMAIN )) === false))
							echo $meta['wp_smushit'] .' '. __('<strong>already smushed</strong>', WP_SMUSHIT_DOMAIN);
						else	
							echo $meta['wp_smushit'];
					} else {
						echo $meta['wp_smushit'];
					}
					echo '<br />';

					if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
						foreach( $meta['sizes'] as $size_name => $size  ) {
							printf( "&mdash; [%s] %d x %d: ", $size_name, intval($size['width']), intval($size['height']) );
							if ( $original_meta['sizes'][$size_name]['wp_smushit'] == $size['wp_smushit'] && stripos( $meta['sizes'][$size_name]['wp_smushit'], 'Smush.it error' ) === false ) {
								echo $size['wp_smushit'] .' '. __('<strong>already smushed</strong>', WP_SMUSHIT_DOMAIN);
							} else {
								echo $size['wp_smushit'];
							}
							echo '<br />';
						}
					}
					echo "</p>";

					wp_update_attachment_metadata( $attachment->ID, $meta );

					// rate limiting is good manners, let's be nice to Yahoo!
					sleep(0.5); 
					@ob_flush();
					flush();
				}
				_e('<hr /></p>Smush.it finished processing.</p>', WP_SMUSHIT_DOMAIN);
			}
		}
		?>
		</div>
		<?php
	}

	/**
	 * Manually process an image from the Media Library
	 */
	function smushit_manual( ) {
		if ( !current_user_can('upload_files') ) {
			wp_die( __( "You don't have permission to work with uploaded files.", WP_SMUSHIT_DOMAIN ) );
		}

		if ( !isset( $_GET['attachment_ID'] ) ) {
			wp_die( __( 'No attachment ID was provided.', WP_SMUSHIT_DOMAIN ) );
		}

		$attachment_ID = intval( $_GET['attachment_ID'] );

		$original_meta = wp_get_attachment_metadata( $attachment_ID );

		$new_meta = $this->resize_from_meta_data( $original_meta, $attachment_ID );
		wp_update_attachment_metadata( $attachment_ID, $new_meta );

		wp_redirect( preg_replace( '|[^a-z0-9-~+_.?#=&;,/:]|i', '', wp_get_referer( ) ) );
		exit();
	}

	/**
	 * Process an image with Smush.it.
	 *
	 * Returns an array of the $file $results.
	 *
	 * @param   string $file            Full absolute path to the image file
	 * @param   string $file_url        Optional full URL to the image file
	 * @returns array
	 */
	function do_smushit( $file_path = '', $file_url = '' ) {

		if (empty($file_path)) {
			return __( "File path is empty", WP_SMUSHIT_DOMAIN );
		}

		if (empty($file_url)) {
			return __( "File URL is empty", WP_SMUSHIT_DOMAIN );
		}
		
		static $error_count = 0;

		if ( $error_count >= WP_SMUSHIT_ERRORS_BEFORE_QUITTING ) {
			return __( "Did not Smush.it due to previous errors", WP_SMUSHIT_DOMAIN );
		}

		// check that the file exists
		if ( !file_exists( $file_path ) || !is_file( $file_path ) ) {
			return sprintf( __( "ERROR: Could not find <span class='code'>%s</span>", WP_SMUSHIT_DOMAIN ), $file_path );
		}

		// check that the file is writable
		if ( !is_writable( dirname( $file_path)) ) {
			return sprintf( __("ERROR: <span class='code'>%s</span> is not writable", WP_SMUSHIT_DOMAIN ), dirname($file_path) );
		}

		$file_size = filesize( $file_path );
		if ( $file_size > WP_SMUSHIT_MAX_BYTES ) {
			return sprintf(__('ERROR: <span style="color:#FF0000;">Skipped (%s) Unable to Smush due to Yahoo 1mb size limits. See <a href="http://developer.yahoo.com/yslow/smushit/faq.html#faq_restrict">FAQ</a></span>', WP_SMUSHIT_DOMAIN), $this->format_bytes($file_size));
		}

// local file check disabled 2013-09-05 - I don't see a point in checking this. We only use the URL of the file to Yahoo! and there is no gaurentee 
// the file path is the SAME as the image from the URL. We can only really make sure the image URL starts with the home URL. This should prevent CDN URLs
// from being processed. 
		// check that the file is within the WP_CONTENT_DIR
		// But first convert the slashes to be uniform. Really with WP would already do this!
/*
		$file_path_tmp 	= str_replace('\\', '/', $file_path);
		$WP_CONTENT_DIR_tmp 	= str_replace('\\', '/', WP_CONTENT_DIR);		
		
		if (WP_SMUSHIT_DEBUG) {
			echo "DEBUG: file_path [". $file_path_tmp ."] WP_CONTENT_DIR [". $WP_CONTENT_DIR_tmp ."]<br />";		
		}
		if (stripos( $file_path_tmp, $WP_CONTENT_DIR_tmp) !== 0) {
				return sprintf( __( "ERROR: <span class='code'>%s</span> must be within the website content directory (<span class='code'>%s</span>)", WP_SMUSHIT_DOMAIN ), 
					htmlentities( $file_path_tmp ), $WP_CONTENT_DIR_tmp);
			}
		}
*/		
		// The Yahoo! Smush.it service does not working with https images. 
		$file_url = str_replace('https://', 'http://', $file_url);

// File URL check disabled 2013-10-11 - The assumption here is the URL may not be the local site URL. The image may be served via a sub-domain pointed
// to this same host or may be an external CDN. In either case the image would be the same. So we let the Yahoo! Smush.it service use the image URL with 
// the assumption the remote image and the local file are the same. Also with the assumption that the CDN service will somehow be updated when the image
// is changed. 
		if ((defined('WP_SMUSHIT_ENFORCE_SAME_URL')) && (WP_SMUSHIT_ENFORCE_SAME_URL == 'on')) {
			$home_url = str_replace('https://', 'http://', get_option('home'));
			if (WP_SMUSHIT_DEBUG) {		
				echo "DEBUG: file_url [". $file_url ."] home_url [". $home_url ."]<br />";		
			}

			if (stripos($file_url, $home_url) !== 0) {
				return sprintf( __( "ERROR: <span class='code'>%s</span> must be within the website home URL (<span class='code'>%s</span>)", WP_SMUSHIT_DOMAIN ), 
				htmlentities( $file_url ), $home_url);
			}
		}
		//echo "calling _post(". $file_url .")<br />";
		$data = $this->_post( $file_url );
		
		//echo "returned from _post data<pre>"; print_r($data); echo "</pre>";
		
		if ( false === $data ) {
			$error_count++;
			return __( 'ERROR: posting to Smush.it', WP_SMUSHIT_DOMAIN );
		}

		// make sure the response looks like JSON -- added 2008-12-19 when
		// Smush.it was returning PHP warnings before the JSON output
		if ( strpos( trim($data), '{' ) != 0 ) {
			return __('Bad response from Smush.it', WP_SMUSHIT_DOMAIN );
		}

		// read the JSON response
		if ( function_exists('json_decode') ) {
			$data = json_decode( $data );
		} else {
			require_once( 'JSON/JSON.php' );
			$json = new Services_JSON( );
			$data = $json->decode( $data );
		}
		if (WP_SMUSHIT_DEBUG) {		
			echo "DEBUG: return from API: data<pre>"; print_r($data); echo "</pre>";
		}

		//echo "returned from _post data<pre>"; print_r($data); echo "</pre>";

		if ( !isset( $data->dest_size ) )
			return __('Bad response from Smush.it', WP_SMUSHIT_DOMAIN );

		if ( -1 === intval( $data->dest_size ) )
			return __('No savings', WP_SMUSHIT_DOMAIN );

		if ( !isset( $data->dest ) ) {
			$err = ( $data->error ? __( 'Smush.it error: ', WP_SMUSHIT_DOMAIN ) . $data->error : __( 'unknown error', WP_SMUSHIT_DOMAIN ) );
			$err .= sprintf( __( " while processing <span class='code'>%s</span> (<span class='code'>%s</span>)", WP_SMUSHIT_DOMAIN ), $file_url, $file_path);
			return $err ;
		}

		$processed_url = $data->dest;

		// The smush.it web service does not append the domain;
		// smushit.com web service does
		if ( 0 !== stripos($processed_url, 'http://') ) {
			$processed_url = SMUSHIT_BASE_URL . $processed_url;
		}

		$temp_file = download_url( $processed_url );

		if ( is_wp_error( $temp_file ) ) {
			@unlink($temp_file);
			return sprintf( __("Error downloading file (%s)", WP_SMUSHIT_DOMAIN ), $temp_file->get_error_message());
		}

		if (!file_exists($temp_file)) {
			return sprintf( __("Unable to locate Smuch.it downloaded file (%s)", WP_SMUSHIT_DOMAIN ), $temp_file);
		}
		
		@unlink( $file_path );
		$success = @rename( $temp_file, $file_path );
		if (!$success) {
			copy($temp_file, $file_path);
			unlink($temp_file);
		}
		
		$savings = intval( $data->src_size ) - intval( $data->dest_size );
		$savings_str = $this->format_bytes( $savings, 1 );
		$savings_str = str_replace( ' ', '&nbsp;', $savings_str );

		$results_msg = sprintf( __("Reduced by %01.1f%% (%s)", WP_SMUSHIT_DOMAIN ),
						 $data->percent,
						 $savings_str );

		return $results_msg;
	}

	function should_resmush($previous_status) {
	  if ( !$previous_status || empty($previous_status ) ) {
		return true;
	  }

	  if ( stripos( $previous_status, 'no savings' ) !== false || stripos( $previous_status, 'reduced' ) !== false ) {
		return false;
	  }

	  // otherwise an error
	  return true;
	}


	/**
	 * Read the image paths from an attachment's meta data and process each image
	 * with wp_smushit().
	 *
	 * This method also adds a `wp_smushit` meta key for use in the media library.
	 *
	 * Called after `wp_generate_attachment_metadata` is completed.
	 */
	function resize_from_meta_data( $meta, $ID = null, $force_resmush = true ) {
		if ( $ID && wp_attachment_is_image( $ID ) === false ) {
			return $meta;
		}

		$attachment_file_path 	= get_attached_file($ID);
		if (WP_SMUSHIT_DEBUG) {
			echo "DEBUG: attachment_file_path=[". $attachment_file_path ."]<br />";
		}
		$attachment_file_url	= wp_get_attachment_url($ID);
		if (WP_SMUSHIT_DEBUG) {
			echo "DEBUG: attachment_file_url=[". $attachment_file_url ."]<br />";
		}

		if ( $force_resmush || $this->should_resmush(  @$meta['wp_smushit'] ) ) {
		  $meta['wp_smushit'] = $this->do_smushit($attachment_file_path, $attachment_file_url);
		}

		// no resized versions, so we can exit
		if ( !isset( $meta['sizes'] ) )
			return $meta;

		foreach($meta['sizes'] as $size_key => $size_data) {
			if ( !$force_resmush && $this->should_resmush( @$meta['sizes'][$size_key]['wp_smushit'] ) === false ) {
				continue;
			}

			// We take the original image. The 'sizes' will all match the same URL and 
			// path. So just get the dirname and rpelace the filename.
			$attachment_file_path_size 	= trailingslashit(dirname($attachment_file_path)) . $size_data['file'];
			if (WP_SMUSHIT_DEBUG) {
				echo "DEBUG: attachment_file_path_size=[". $attachment_file_path_size ."]<br />";
			}

			$attachment_file_url_size 	= trailingslashit(dirname($attachment_file_url)) . $size_data['file'];
			if (WP_SMUSHIT_DEBUG) {
				echo "DEBUG: attachment_file_url_size=[". $attachment_file_url_size ."]<br />";
			}

			$meta['sizes'][$size_key]['wp_smushit'] = $this->do_smushit( $attachment_file_path_size, $attachment_file_url_size ) ;
			
			//echo "size_key[". $size_key ."] wp_smushit<pre>"; print_r($meta['sizes'][$size_key]['wp_smushit']); echo "</pre>";
		}
		
		//echo "meta<pre>"; print_r($meta); echo "</pre>";
		return $meta;
	}

	/**
	 * Post an image to Smush.it.
	 *
	 * @param   string          $file_url     URL of the file to send to Smush.it
	 * @return  string|boolean  Returns the JSON response on success or else false
	 */
	function _post( $file_url ) {
		$req = sprintf( SMUSHIT_REQ_URL, urlencode( $file_url ) );

		$data = false;
		if (WP_SMUSHIT_DEBUG) {		
			echo "DEBUG: Calling API: [". $req."]<br />";
		}
		if ( function_exists( 'wp_remote_get' ) ) {
			$response = wp_remote_get( $req, array('user-agent' => WP_SMUSHIT_UA, 'timeout' => WP_SMUSHIT_TIMEOUT ) );
			if ( !$response || is_wp_error( $response ) ) {
			  $data = false;
			} else {
			  $data = wp_remote_retrieve_body( $response );
			}
		} else {
			wp_die( __('WP Smush.it requires WordPress 2.8 or greater', WP_SMUSHIT_DOMAIN) );
		}

		return $data;
	}


	/**
	 * Print column header for Smush.it results in the media library using
	 * the `manage_media_columns` hook.
	 */
	function columns( $defaults ) {
		$defaults['smushit'] = 'Smush.it';
		return $defaults;
	}

	/**
	 * Return the filesize in a humanly readable format.
	 * Taken from http://www.php.net/manual/en/function.filesize.php#91477
	 */
	function format_bytes( $bytes, $precision = 2 ) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	/**
	 * Print column data for Smush.it results in the media library using
	 * the `manage_media_custom_column` hook.
	 */
	function custom_column( $column_name, $id ) {
		if( 'smushit' == $column_name ) {
			$data = wp_get_attachment_metadata($id);
			if ( isset( $data['wp_smushit'] ) && !empty( $data['wp_smushit'] ) ) {
				print $data['wp_smushit'];
				printf( "<br><a href=\"admin.php?action=wp_smushit_manual&amp;attachment_ID=%d\">%s</a>",
						 $id,
						 __( 'Re-smush', WP_SMUSHIT_DOMAIN ) );
			} else {
			  if ( wp_attachment_is_image( $id ) ) {
				print __( 'Not processed', WP_SMUSHIT_DOMAIN );
				printf( "<br><a href=\"admin.php?action=wp_smushit_manual&amp;attachment_ID=%d\">%s</a>",
						 $id,
						 __('Smush.it now!', WP_SMUSHIT_DOMAIN));
				}
			}
		}
	}


	// Borrowed from http://www.viper007bond.com/wordpress-plugins/regenerate-thumbnails/
	function add_bulk_actions_via_javascript() { ?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$('select[name^="action"] option:last-child').before('<option value="bulk_smushit">Bulk Smush.it</option>');
			});
		</script>
	<?php }


	// Handles the bulk actions POST
	// Borrowed from http://www.viper007bond.com/wordpress-plugins/regenerate-thumbnails/
	function bulk_action_handler() {
		check_admin_referer( 'bulk-media' );

		if ( empty( $_REQUEST['media'] ) || ! is_array( $_REQUEST['media'] ) )
			return;

		$ids = implode( ',', array_map( 'intval', $_REQUEST['media'] ) );

		// Can't use wp_nonce_url() as it escapes HTML entities
		wp_redirect( add_query_arg( '_wpnonce', wp_create_nonce( 'wp-smushit-bulk' ), admin_url( 'upload.php?page=wp-smushit-bulk&goback=1&ids=' . $ids ) ) );
		exit();
	}
	
}

$WpSmushit = new WpSmushit();
global $WpSmushit;

}

if ( !function_exists( 'wp_basename' ) ) {
	  /**
	   * Introduced in WP 3.1... this is copied verbatim from wp-includes/formatting.php.
	   */
	function wp_basename( $path, $suffix = '' ) {
		return urldecode( basename( str_replace( '%2F', '/', urlencode( $path ) ), $suffix ) );
	}
}