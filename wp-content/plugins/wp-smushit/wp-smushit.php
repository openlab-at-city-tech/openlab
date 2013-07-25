<?php
/*
Plugin Name: WP Smush.it
Plugin URI: http://wordpress.org/extend/plugins/wp-smushit/
Description: Reduce image file sizes and improve performance using the <a href="http://smush.it/">Smush.it</a> API within WordPress.
Author: WPMU DEV
Version: 1.6.5
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

if ( !class_exists( 'WpSmushit' ) ) {

class WpSmushit {

	var $version = "1.6.5";

	/**
     * Constructor
     */
	function WpSmushit( ) {
		$this->__construct( );
	}
	function __construct( ) {

		if ( !function_exists( 'download_url' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

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
	
	/**
	 * Plugin setting functions
	 */
	function register_settings( ) {
		add_settings_section( 'wp_smushit_settings', 'WP Smush.it', array( &$this, 'settings_cb' ), 'media' );
		add_settings_field( 'wp_smushit_smushit_auto', __( 'Use Smush.it on upload?', WP_SMUSHIT_DOMAIN ), array( &$this, 'render_auto_opts' ),  'media', 'wp_smushit_settings' );
		add_settings_field( 'wp_smushit_smushit_timeout', __( 'How many seconds should we wait for a response from Smush.it?', WP_SMUSHIT_DOMAIN ), array( &$this, 'render_timeout_opts' ), 'media', 'wp_smushit_settings' );
		register_setting( 'media', array( &$this, 'smushit_auto' ) );
		register_setting( 'media', array( &$this, 'smushit_timeout' ) );
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

	// default is 6hrs
	function temporarily_disable( $seconds = 21600 ) {
		update_option( 'wp_smushit_smushit_auto', time() + $seconds );
	}	
	
	function admin_init( ) {
		load_plugin_textdomain( WP_SMUSHIT_DOMAIN );
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

		if ( sizeof($attachments) < 1 ):
			_e( '<p>You don’t appear to have uploaded any images yet.</p>', WP_SMUSHIT_DOMAIN );
		else: 
			if ( empty($_POST) && !$auto_start ): // instructions page
		
			_e( "<p>This tool will run all of the images in your media library through the WP Smush.it web service.  It won't re-smush images that were successfully smushed before. It will retry images that were not successfully smushed.</p>", WP_SMUSHIT_DOMAIN );

			_e( "<p>It uploads each and every file to Yahoo! and then downloads the resulting file. It can take a long time.</p>", WP_SMUSHIT_DOMAIN );

			printf( __( "<p>We found %d images in your media library. Be forewarned, <strong>it will take <em>at least</em> %d minutes</strong> to process all these images if they have never been smushed before.</p>", WP_SMUSHIT_DOMAIN ), sizeof($attachments), sizeof($attachments) * 3 / 60 );

			_e( "<p><em>N.B. If your server <tt>gzip</tt>s content you may not see the progress updates as your files are processed.</em></p>", WP_SMUSHIT_DOMAIN );

			printf( __( "<p><strong>This is an experimental feature.</strong> Please post any feedback to the %s.</p>", WP_SMUSHIT_DOMAIN ), '<a href="http://wordpress.org/tags/wp-smushit">'. __( 'WordPress WP Smush.it forums', WP_SMUSHIT_DOMAIN ). '</a>' );
		?>
		  <form method="post" action="">
			<?php wp_nonce_field( 'wp-smushit-bulk', '_wpnonce'); ?>
			<button type="submit" class="button-secondary action"><?php _e( 'Run all my images through WP Smush.it right now', WP_SMUSHIT_DOMAIN ) ?></button>
		  </form>
		  
		<?php
			else: // run the script

				if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'wp-smushit-bulk' ) || !current_user_can( 'edit_others_posts' ) ) {
					wp_die( __( 'Cheatin&#8217; uh?' ) );
				}


				@ob_implicit_flush( true );
				@ob_end_flush();
				foreach( $attachments as $attachment ) {
					printf( "<p>Processing <strong>%s</strong>&hellip;<br>", esc_html( $attachment->post_name ) );
					$original_meta = wp_get_attachment_metadata( $attachment->ID, true );
						
					$meta = $this->resize_from_meta_data( $original_meta, $attachment->ID, false );

					printf( "– %dx%d: ", intval($meta['width']), intval($meta['height']) );

					if ( $original_meta['wp_smushit'] == $meta['wp_smushit'] && stripos( $meta['wp_smushit'], 'Smush.it error' ) === false ) {
						echo 'already smushed' . $meta['wp_smushit'];
					} else {
						echo $meta['wp_smushit'];
					}
					echo '<br>';

					if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
						foreach( $meta['sizes'] as $size_name => $size  ) {
							printf( "– %dx%d: ", intval($size['width']), intval($size['height']) );
							if ( $original_meta['sizes'][$size_name]['wp_smushit'] == $size['wp_smushit'] && stripos( $meta['sizes'][$size_name]['wp_smushit'], 'Smush.it error' ) === false ) {
								echo 'already smushed';
							} else {
								echo $size['wp_smushit'];
							}
							echo '<br>';
						}
					}
					echo "</p>";

					wp_update_attachment_metadata( $attachment->ID, $meta );

					// rate limiting is good manners, let's be nice to Yahoo!
					sleep(0.5); 
					@ob_flush();
					flush();
				}
			endif; 
		  endif; 
		?>
		</div>
		<?php
	}

	/**
	 * Manually process an image from the Media Library
	 */
	function smushit_manual( ) {
		if ( !current_user_can('upload_files') ) {
			wp_die( __( 'You don\'t have permission to work with uploaded files.', WP_SMUSHIT_DOMAIN ) );
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
	function do_smushit( $file, $file_url = null ) {
		// don't run on localhost, IPv4 and IPv6 checks
		// if( in_array($_SERVER['SERVER_ADDR'], array('127.0.0.1', '::1')) )
		//	return array($file, __('Not processed (local file)', WP_SMUSHIT_DOMAIN));

		// canonicalize path - disabled 2011-02-1 troubleshooting 'Could not find...' errors.
		// From the PHP docs: "The running script must have executable permissions on
		// all directories in the hierarchy, otherwise realpath() will return false."
		// $file_path = realpath($file);

		static $error_count = 0;

		if ( $error_count >= WP_SMUSHIT_ERRORS_BEFORE_QUITTING ) {
			$msg = __( "Did not smush due to previous errors", WP_SMUSHIT_DOMAIN );
			return array( $file, $msg );
		}

		$file_path = $file;
		// check that the file exists
		if ( !file_exists( $file_path ) || !is_file( $file_path ) ) {
			$msg = sprintf( __( "Could not find <span class='code'>%s</span>", WP_SMUSHIT_DOMAIN ), $file_path );
			return array( $file, $msg );
		}

		// check that the file is writable
		if ( !is_writable( $file_path ) ) {
			$msg = sprintf( __("<span class='code'>%s</span> is not writable", WP_SMUSHIT_DOMAIN ), $file_path );
			return array( $file, $msg );
		}

		$file_size = filesize( $file_path );
		if ( $file_size > WP_SMUSHIT_MAX_BYTES ) {
			$msg = sprintf(__("<a href='http://developer.yahoo.com/yslow/smushit/faq.html#faq_restrict'>Too big</a> for Smush.it (%s)", WP_SMUSHIT_DOMAIN), $this->format_bytes($file_size));
			return array( $file, $msg );
		}

		// check that the file is within the WP_CONTENT_DIR
		$upload_dir = wp_upload_dir();
		$wp_upload_dir = $upload_dir['basedir'];
		$wp_upload_url = $upload_dir['baseurl'];
		if ( 0 !== stripos(realpath($file_path), realpath(ABSPATH)) ) {
			$msg = sprintf( __( "<span class='code'>%s</span> must be within the content directory (<span class='code'>%s</span>)", 
				WP_SMUSHIT_DOMAIN ), htmlentities( $file_path ), $wp_upload_dir);

			return array($file, $msg);
		}

		if ( !$file_url ) {
			// determine the public URL
			$file_url = str_replace( $wp_upload_dir, $wp_upload_url, $file );
		}

		$data = $this->_post( $file_url );

		if ( false === $data ) {
			$error_count++;
			return array( $file, __( 'Error posting to Smush.it', WP_SMUSHIT_DOMAIN ) );
		}

		// make sure the response looks like JSON -- added 2008-12-19 when
		// Smush.it was returning PHP warnings before the JSON output
		if ( strpos( trim($data), '{' ) != 0 ) {
			return array( $file, __('Bad response from Smush.it', WP_SMUSHIT_DOMAIN ) );
		}

		// read the JSON response
		if ( function_exists('json_decode') ) {
			$data = json_decode( $data );
		} else {
			require_once( 'JSON/JSON.php' );
			$json = new Services_JSON( );
			$data = $json->decode( $data );
		}
		
		if ( !isset( $data->dest_size ) )
			return array( $file, __('Bad response from Smush.it', WP_SMUSHIT_DOMAIN ) );

		if ( -1 === intval( $data->dest_size ) )
			return array( $file, __('No savings', WP_SMUSHIT_DOMAIN ) );

		if ( !isset( $data->dest ) ) {
			$err = ( $data->error ? __( 'Smush.it error: ', WP_SMUSHIT_DOMAIN ) . $data->error : __( 'unknown error', WP_SMUSHIT_DOMAIN ) );
			$err .= sprintf( __( " while processing <span class='code'>%s</span> (<span class='code'>%s</span>)", WP_SMUSHIT_DOMAIN ), $file_url, $file_path);
			return array( $file, $err );
		}

		$processed_url = $data->dest;

		// The smush.it web service does not append the domain;
		// smushit.com web service does
		if ( 0 !== stripos($processed_url, 'http://') ) {
			$processed_url = SMUSHIT_BASE_URL . $processed_url;
		}

		$temp_file = download_url( $processed_url );

		if ( is_wp_error( $temp_file ) ) {
			@unlink($tmp);
			$results_msg = sprintf( __("Error downloading file (%s)", WP_SMUSHIT_DOMAIN ),
						 $temp_file->get_error_message());
			return array($file, $results_msg );
		}

		@rename( $temp_file, $file_path );

		$savings = intval( $data->src_size ) - intval( $data->dest_size );
		$savings_str = $this->format_bytes( $savings, 1 );
		$savings_str = str_replace( ' ', '&nbsp;', $savings_str );

		$results_msg = sprintf( __("Reduced by %01.1f%% (%s)", WP_SMUSHIT_DOMAIN ),
						 $data->percent,
						 $savings_str );

		return array( $file, $results_msg );
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

		$file_path = $meta['file'];
		$store_absolute_path = true;
		$upload_dir = wp_upload_dir();
		$upload_path = trailingslashit( $upload_dir['basedir'] );

		// WordPress >= 2.6.2: determine the absolute $file_path (http://core.trac.wordpress.org/changeset/8796)
		if ( false === strpos($file_path,  $upload_path) ) {
			$store_absolute_path = false;
			$file_path =  $upload_path . $file_path;
		}

		if ( $force_resmush || $this->should_resmush(  @$meta['wp_smushit'] ) ) {
		  list($file, $msg) = $this->do_smushit($file_path);
			$meta['wp_smushit'] = $msg;
		}

		// strip absolute path for Wordpress >= 2.6.2
		if ( false === $store_absolute_path ) {
			$meta['file'] = str_replace($upload_path, '', $meta['file']);
		}

		// no resized versions, so we can exit
		if ( !isset( $meta['sizes'] ) )
			return $meta;

		// meta sizes don't contain a path, so we calculate one
		$base_dir = trailingslashit( dirname($file_path) );

		foreach($meta['sizes'] as $size => $data) {
			if ( !$force_resmush && $this->should_resmush( @$meta['sizes'][$size]['wp_smushit'] ) === false ) {
				continue;
			}

			list($smushed_file, $results) = $this->do_smushit( $base_dir . wp_basename( $data['file'] ) );
			$meta['sizes'][$size]['wp_smushit'] = $results;
		}
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