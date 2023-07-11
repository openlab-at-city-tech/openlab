<?php

class Meow_WPMC_Core {

	public $admin = null;
	public $is_rest = false;
	public $is_cli = false;
	public $is_pro = false;
	public $engine = null;
	public $catch_timeout = true; // This will halt the plugin before reaching the PHP timeout.
	public $types = "jpg|jpeg|jpe|gif|png|tiff|bmp|csv|svg|pdf|xls|xlsx|doc|docx|odt|wpd|rtf|tiff|mp3|mp4|mov|wav|lua";
	public $current_method = 'media';
	public $servername = null; // meowapps.com (site URL without http/https)
	public $site_url = null; // https://meowapps.com
	public $upload_path = null; // /www/wp-content/uploads (path to uploads)
	public $upload_url = null; // wp-content/uploads (uploads without domain)
	private $option_name = 'wpmc_options';

	private $regex_file = '/[A-Za-z0-9-_,.\(\)\s]+[.]{1}(MIMETYPES)/';
	private $refcache = array();
	private $check_content = null;
	private $debug_logs = null;
	private $multilingual = false;
	private $languages = array();

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'delete_attachment', array( $this, 'delete_attachment_related_data' ), 10, 1 );
		add_action( 'trashed_post', array( $this, 'delete_attachment_related_data' ), 10, 1 );
	}

	function plugins_loaded() {

		// Variables
		$this->site_url = get_site_url();
		$this->multilingual = $this->is_multilingual();
		$this->languages = $this->get_languages();
		$this->current_method = $this->get_option( 'method' );
		$this->regex_file = str_replace( "MIMETYPES", $this->types, $this->regex_file );
		$this->servername = str_replace( 'http://', '', str_replace( 'https://', '', $this->site_url ) );
		$uploaddir = wp_upload_dir();
		$this->upload_path = $uploaddir['basedir'];
		$this->upload_url = substr( $uploaddir['baseurl'], 1 + strlen( $this->site_url ) );
		$this->check_content = $this->get_option( 'content' );
		$this->debug_logs = $this->get_option( 'debuglogs' );
		$this->is_rest = MeowCommon_Helpers::is_rest();
		$this->is_cli = defined( 'WP_CLI' ) && WP_CLI;
		
		global $wpmc;
		$wpmc = $this;

		// Language
		load_plugin_textdomain( WPMC_DOMAIN, false, basename( WPMC_PATH ) . '/languages' );

		// Admin
		$this->admin = new Meow_WPMC_Admin( $this );

		// Advanced core
		if ( class_exists( 'MeowPro_WPMC_Core' ) ) {
			new MeowPro_WPMC_Core( $this );
		}

		// Install hooks and engine only if they might be used
		if ( is_admin() || $this->is_rest || $this->is_cli ) {
			add_action( 'wpmc_initialize_parsers', array( $this, 'initialize_parsers' ), 10, 0 );
			add_filter( 'wp_unique_filename', array( $this, 'wp_unique_filename' ), 10, 3 );
			$this->engine = new Meow_WPMC_Engine( $this, $this->admin );
		}

		// Only for REST
		if ( $this->is_rest ) {
			new Meow_WPMC_Rest( $this, $this->admin );
		}

		if ( is_admin() ) {
			new Meow_WPMC_UI( $this );
		}
	}

	function init() {
		remove_action( 'wp_scheduled_delete', 'wp_scheduled_delete' );
	}

	function initialize_parsers() {
		include_once( 'parsers.php' );
		new Meow_WPMC_Parsers();
	}

	function deepsleep( $seconds ) {
		$start_time = time();
		while( true ) {
			if ( ( time() - $start_time ) > $seconds ) {
				return false;
			}
			get_post( array( 'posts_per_page' => 50 ) );
		}
	}

	private $start_time;
	private $time_elapsed = 0;
	private $item_scan_avg_time = 0;
	private $wordpress_init_time = 0.5;
	private $max_execution_time;
	private $items_checked = 0;
	private $items_count = 0;

	function get_max_execution_time() {
		if ( isset( $this->max_execution_time ) )
			return $this->max_execution_time;

		$this->max_execution_time = ini_get( "max_execution_time" );
		if ( empty( $this->max_execution_time ) || $this->max_execution_time < 5 )
			$this->max_execution_time = 30;

		return $this->max_execution_time;
	}

	function timeout_check_start( $count ) {
		$this->start_time = time();
		$this->items_count = $count;
		$this->get_max_execution_time();
	}

	function timeout_get_elapsed() {
		return $this->time_elapsed . 'ms';
	}

	function timeout_check() {
		$this->time_elapsed = time() - $this->start_time;
		$this->time_remaining = $this->max_execution_time - $this->wordpress_init_time - $this->time_elapsed;
		if ( $this->catch_timeout ) {
			if ( $this->time_remaining - $this->item_scan_avg_time < 0 ) {
				error_log("Media Cleaner Timeout! Check the Media Cleaner logs for more info.");
				$this->log( "😵 Timeout! Some info for debug:" );
				$this->log( "🍀 Elapsed time: $this->time_elapsed" );
				$this->log( "🍀 WP init time: $this->wordpress_init_time" );
				$this->log( "🍀 Remaining time: $this->time_remaining" );
				$this->log( "🍀 Scan time per item: $this->item_scan_avg_time" );
				$this->log( "🍀 PHP max_execution_time: $this->max_execution_time" );
				header("HTTP/1.0 408 Request Timeout");
				exit;
			}
		}
	}

	function delete_attachment_related_data( $post_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE postId = %d", $post_id ) );
	}

	function timeout_check_additem() {
		$this->items_checked++;
		$this->time_elapsed = time() - $this->start_time;
		$this->item_scan_avg_time = ceil( ( $this->time_elapsed / $this->items_checked ) * 10 ) / 10;
	}

	// This checks if a new uploaded filename isn't the same one as a currently
	// filename in the trash (that would cause issues)
	function wp_unique_filename( $filename, $ext, $dir ) {
		$fullpath = trailingslashit( $dir ) . $filename;
		$relativepath = $this->clean_uploaded_filename( $fullpath );
		$trashfilepath = trailingslashit( $this->get_trashdir() ) . $relativepath;
		if ( file_exists( $trashfilepath ) ) {
			$path_parts = pathinfo( $fullpath );
			$filename_noext = $path_parts['filename'];
			$new_filename = $filename_noext . '-' . date('Ymd-His', time()) . '.' . $path_parts['extension'];
			//error_log( 'POTENTIALLY TRASH PATH: ' . $trashfilepath );
			//error_log( 'POTENTIALLY NEW FILE: ' . $new_filename );
			return $new_filename;
		}
		return $filename;
	}

	function array_to_ids_or_urls( &$meta, &$ids, &$urls ) {
		foreach ( $meta as $k => $m ) {
			if ( is_numeric( $m ) ) {
				// Probably a Media ID
				if ( $m > 0 )
					array_push( $ids, $m );
			}
			else if ( is_array( $m ) ) {
				// If it's an array with a width, probably that the index is the Media ID
				if ( isset( $m['width'] ) && is_numeric( $k ) ) {
					if ( $k > 0 )
						array_push( $ids, $k );
				}
			}
			else if ( !empty( $m ) ) {
				// If it's a string, maybe it's a file (with an extension)
				if ( preg_match( $this->regex_file, $m ) )
					array_push( $urls, $m );
			}
		}
	}

	function get_favicon() {
		// Yoast SEO plugin
		$vals = get_option( 'wpseo_titles' );
		if ( !empty( $vals ) ) {
			$url = $vals['company_logo'];
			if ( $this->is_url( $url ) )
				return $this->clean_url( $url );
		}
	}

	function get_shortcode_attributes( $shortcode_tag, $post ) {
		if ( has_shortcode( $post->post_content, $shortcode_tag ) ) {
			$output = array();
			//get shortcode regex pattern wordpress function
			$pattern = get_shortcode_regex( [ $shortcode_tag ] );
			if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) )
			{
					$keys = array();
					$output = array();
					foreach( $matches[0] as $key => $value) {
							// $matches[3] return the shortcode attribute as string
							// replace space with '&' for parse_str() function
							$get = str_replace(" ", "&" , trim( $matches[3][$key] ) );
							$get = str_replace('"', '' , $get );
							parse_str( $get, $sub_output );

							//get all shortcode attribute keys
							$keys = array_unique( array_merge(  $keys, array_keys( $sub_output )) );
							$output[] = $sub_output;
					}
					if ( $keys && $output ) {
							// Loop the output array and add the missing shortcode attribute key
							foreach ($output as $key => $value) {
									// Loop the shortcode attribute key
									foreach ($keys as $attr_key) {
											$output[$key][$attr_key] = isset( $output[$key] )  && isset( $output[$key] ) ? $output[$key][$attr_key] : NULL;
									}
									//sort the array key
									ksort( $output[$key]);
							}
					}
			}
			return $output;
		}
		else {
				return false;
		}
	}

	function get_urls_from_html( $html ) {
		if ( empty( $html ) ) {
			return array();
		}

		// Proposal/fix by @copytrans
		// Discussion: https://wordpress.org/support/topic/bug-in-core-php/#post-11647775
		// Modified by Jordy again in 2021 for those who don't have MB enabled
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$html = mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' );
		}
		else {
			$html = htmlspecialchars_decode( utf8_decode( htmlentities( $html, ENT_COMPAT, 'utf-8', false ) ) );
		}

		// Resolve src-set and shortcodes
		if ( !$this->get_option( 'shortcodes_disabled' ) ) {
			$html = do_shortcode( $html );
		}

		// TODO: Since WP 5.5, wp_filter_content_tags should be used instead of wp_make_content_images_responsive.
		$html = function_exists( 'wp_filter_content_tags' ) ? wp_filter_content_tags( $html ) :
			wp_make_content_images_responsive( $html );

		// Create the DOM Document
		if ( !class_exists("DOMDocument") ) {
			error_log( 'Media Cleaner: The DOM extension for PHP is not installed.' );
			throw new Error( 'The DOM extension for PHP is not installed.' );
		}

		if ( empty( $html ) ) {
			return array();
		}

		libxml_use_internal_errors(true);
		$dom = new DOMDocument();
		@$dom->loadHTML( $html );
		libxml_clear_errors();
		$results = array();

		// <meta> tags in <head> area
		$metas = $dom->getElementsByTagName( 'meta' );
		foreach ( $metas as $meta ) {
			$property = $meta->getAttribute( 'property' );
			if ( $property == 'og:image' || $property == 'og:image:secure_url' || $property == 'twitter:image' ) {
				$url = $meta->getAttribute( 'content' );
				if ( $this->is_url( $url ) ) {
					$src = $this->clean_url( $url );
					if ( !empty( $src ) ) {
						array_push( $results, $src );
					}
				}
			}
		}

		// IFrames (by Mike Meinz)
		$iframes = $dom->getElementsByTagName( 'iframe' );
		foreach( $iframes as $iframe ) {
			$iframe_src = $iframe->getAttribute( 'src' );
			// Ignore if the iframe src is not on this server
			if ( ( strpos( $iframe_src, $this->servername ) !== false) || ( substr( $iframe_src, 0, 1 ) == "/" ) ) {
				// Create a new DOM Document to hold iframe
				$iframe_doc = new DOMDocument();
				// Load the url's contents into the DOM
				libxml_use_internal_errors( true ); // ignore html formatting problems
				$rslt = @$iframe_doc->loadHTMLFile( $iframe_src );
				libxml_clear_errors();
				libxml_use_internal_errors( false );
				if ( $rslt ) {
					// Get the resulting html
					$iframe_html = $iframe_doc->saveHTML();
					if ( $iframe_html !== false ) {
						// Scan for links in the iframe
						$iframe_urls = $this->get_urls_from_html( $iframe_html ); // Recursion
						if ( !empty( $iframe_urls ) ) {	
							$results = array_merge( $results, $iframe_urls );
						}
					}
				}
				else {
					$this->log( '🚫 Failed to load iframe: ' . $iframe_src );
				}
			}
		}


		// Images: src, srcset
		$imgs = $dom->getElementsByTagName( 'img' );
		foreach ( $imgs as $img ) {
			//error_log($img->getAttribute('src'));
			$src = $this->clean_url( $img->getAttribute('src') );
    			array_push( $results, $src );
			$srcset = $img->getAttribute('srcset');
			if ( !empty( $srcset ) ) {
				$setImgs = explode( ',', trim( $srcset ) );
				foreach ( $setImgs as $setImg ) {
					$finalSetImg = explode( ' ', trim( $setImg ) );
					if ( is_array( $finalSetImg ) ) {
						array_push( $results, $this->clean_url( $finalSetImg[0] ) );
					}
				}
			}
		}

		// Videos: src
		$videos = $dom->getElementsByTagName( 'video' );
		foreach ( $videos as $video ) {
			//error_log($video->getAttribute('src'));
			$src = $this->clean_url( $video->getAttribute('src') );
    	array_push( $results, $src );
		}

		// Audios: src
		$audios = $dom->getElementsByTagName( 'audio' );
		foreach ( $audios as $audio ) {
			//error_log($audio->getAttribute('src'));
			$src = $this->clean_url( $audio->getAttribute('src') );
    	array_push( $results, $src );
		}

		// Sources: src
		$audios = $dom->getElementsByTagName( 'source' );
		foreach ( $audios as $audio ) {
			//error_log($audio->getAttribute('src'));
			$src = $this->clean_url( $audio->getAttribute('src') );
    	array_push( $results, $src );
		}

		// Links, href
		$urls = $dom->getElementsByTagName( 'a' );
		foreach ( $urls as $url ) {
			$url_href = $url->getAttribute('href'); // mm change
			if ( $this->is_url( $url_href ) ) { // mm change
				$src = $this->clean_url( $url_href );  // mm change
				if ( !empty( $src ) )
					array_push( $results, $src );
			}
		}

		// <link> tags in <head> area
		$urls = $dom->getElementsByTagName( 'link' );
		foreach ( $urls as $url ) {
			$url_href = $url->getAttribute( 'href' );
			if ( $this->is_url( $url_href ) ) {
				$src = $this->clean_url( $url_href );
				if ( !empty( $src ) ) {
					array_push( $results, $src );
				}
			}
		}

		// PDF
		preg_match_all( "/((https?:\/\/)?[^\\&\#\[\] \"\?]+\.pdf)/", $html, $res );
		if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 ) {
			foreach ( $res[1] as $url ) {
				if ( $this->is_url( $url ) )
					array_push( $results, $this->clean_url( $url ) );
			}
		}

		// Background images
		preg_match_all( "/url\(\'?\"?((https?:\/\/)?[^\\&\#\[\] \"\?]+\.(jpe?g|gif|png))\'?\"?/", $html, $res );
		if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 ) {
			foreach ( $res[1] as $url ) {
				if ( $this->is_url( $url ) )
					array_push( $results, $this->clean_url( $url ) );
			}
		}

		return $results;
	}

	// Parse a meta, visit all the arrays, look for the attributes, fill $ids and $urls arrays
	// If rawMode is enabled, it will not check if the value is an ID or an URL, it will just returns it in URLs
	function get_from_meta( $meta, $lookFor, &$ids, &$urls, $rawMode = false ) {
		if ( !is_array( $meta ) && !is_object( $meta) ) {
			return;
		}
		foreach ( $meta as $key => $value ) {
			if ( is_object( $value ) || is_array( $value ) )
				$this->get_from_meta( $value, $lookFor, $ids, $urls, $rawMode );
			else if ( in_array( $key, $lookFor ) ) {
				if ( empty( $value ) ) {
					continue;
				}
				else if ( $rawMode ) {
					array_push( $urls, $value );
				}
				else if ( is_numeric( $value ) ) {
					// It this an ID?
					array_push( $ids, $value );
				}
				else {
					if ( $this->is_url( $value ) ) {
						// Is this an URL?
						array_push( $urls, $this->clean_url( $value ) );
					}
					else {
						// Is this an array of IDs, encoded as a string? (like "20,13")
						$pieces = explode( ',', $value );
						foreach ( $pieces as $pval ) {
							if ( is_numeric( $pval ) ) {
								array_push( $ids, $pval );
							}
						}
					}
				}
			}
		}
	}

	function get_images_from_themes( &$ids, &$urls ) {
		// USE CURRENT THEME AND WP API
		$ch = get_custom_header();
		if ( !empty( $ch ) && !empty( $ch->url ) ) {
			array_push( $urls, $this->clean_url( $ch->url ) );
		}
		if ( $this->is_url( $ch->thumbnail_url ) ) {
			array_push( $urls, $this->clean_url( $ch->thumbnail_url ) );
		}
		if ( !empty( $ch ) && !empty( $ch->attachment_id ) ) {
			array_push( $ids, $ch->attachment_id );
		}
		$cl = get_custom_logo();
		if ( $this->is_url( $cl ) ) {
			$urls = array_merge( $this->get_urls_from_html( $cl ), $urls );
		}
		$custom_logo = get_theme_mod( 'custom_logo' );
		if ( !empty( $custom_logo ) && is_numeric( $custom_logo ) ) {
			array_push( $ids, (int)$custom_logo );
		}
		$si = get_site_icon_url();
		if ( $this->is_url( $si ) ) {
			array_push( $urls, $this->clean_url( $si ) );
		}
		$si_id = get_option( 'site_icon' );
		if ( !empty( $si_id ) && is_numeric( $si_id ) ) {
			array_push( $ids, (int)$si_id );
		}
		$cd = get_background_image();
		if ( $this->is_url( $cd ) ) {
			array_push( $urls, $this->clean_url( $cd ) );
		}
		$photography_hero_image = get_theme_mod( 'photography_hero_image' );
		if ( !empty( $photography_hero_image ) ) {
			array_push( $ids, $photography_hero_image );
		}
		$author_profile_picture = get_theme_mod( 'author_profile_picture' );
		if ( !empty( $author_profile_picture ) ) {
			array_push( $ids, $author_profile_picture );
		}
		if ( function_exists ( 'get_uploaded_header_images' ) ) {
			$header_images = get_uploaded_header_images();
			if ( !empty( $header_images ) ) {
				foreach ( $header_images as $hi ) {
					if ( !empty ( $hi['attachment_id'] ) ) {
						array_push( $ids, $hi['attachment_id'] );
					}
				}
			}
		}
	}

	function logs_directory_check() {
		if ( !file_exists( WPMC_PATH . '/logs/' ) ) {
			mkdir( WPMC_PATH . '/logs/', 0777 );
		}
	}

	function log( $data = null, $force = false ) {
		if ( !$this->debug_logs && !$force )
			return;
		$this->logs_directory_check();
		$fh = @fopen( WPMC_PATH . '/logs/media-cleaner.log', 'a' );
		if ( !$fh )
			return false;
		$date = current_datetime()->format( 'Y-m-d H:i:s' );
		if ( is_null( $data ) )
			fwrite( $fh, "\n" );
		else
			fwrite( $fh, "$date: {$data}\n" );
		fclose( $fh );
		return true;
	}

	/**
	 *
	 * HELPERS
	 *
	 */

	function get_trashdir() {
		return trailingslashit( $this->upload_path ) . 'wpmc-trash';
	}

	function get_trashurl() {
		return trailingslashit( $this->upload_url ) . 'wpmc-trash';
	}

	/**
	 *
	 * I18N RELATED HELPERS
	 *
	 */

	function is_multilingual() {
		return function_exists( 'icl_object_id' );
	}

	function get_languages() {
		$results = array();
		if ( $this->is_multilingual() ) {
			$languages = icl_get_languages();
			foreach ( $languages as $language ) {
				if ( isset( $language['code'] ) ) {
					array_push( $results, $language['code'] );
				}
				else if ( isset( $language['language_code'] ) ) {
					array_push( $results, $language['language_code'] );
				}
			}
		}
		return $results;
	}

	function get_translated_media_ids( $mediaId ) {
		$translated_ids = array();
		foreach ( $this->languages as $language ) {
			$id = apply_filters( 'wpml_object_id', $mediaId, 'attachment', false, $language );
			if ( !empty( $id ) ) {
				array_push( $translated_ids, $id );
			}
		}
		return $translated_ids;
	}

	/**
	 *
	 * DELETE / SCANNING / RESET
	 *
	 */

	function recover_file( $path ) {
		$originalPath = trailingslashit( $this->upload_path ) . $path;
		$trashPath = trailingslashit( $this->get_trashdir() ) . $path;
		if ( !file_exists( $trashPath ) ) {
			$this->log( "🚫 The file $originalPath actually does not exist in the trash." );
			return true;
		}
		$path_parts = pathinfo( $originalPath );
		if ( !file_exists( $path_parts['dirname'] ) && !wp_mkdir_p( $path_parts['dirname'] ) ) {
			die( 'Failed to create folder.' );
		}
		if ( !rename( $trashPath, $originalPath ) ) {
			die( 'Failed to move the file.' );
		}
		return true;
	}

	function recover( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $this->get_issue( $id );

		// Files
		if ( $issue->type === 0 ) {
			$this->recover_file( $issue->path );
			$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET deleted = 0 WHERE id = %d", $id ) );
			$this->log( "✅ Recovered {$issue->path}." );
			return true;
		}
		// Media
		else if ( $issue->type === 1 ) {

			// If there is no file attached, doesn't handle the files
			$fullpath = get_attached_file( $issue->postId );
			if ( empty( $fullpath ) ) {
				$this->log( "🚫 Media #{$issue->postId} does not have attached file anymore." );
				error_log( "Media #{$issue->postId} does not have attached file anymore." );
				return false;
			}

			$paths = $this->get_paths_from_attachment( $issue->postId );
			foreach ( $paths as $path ) {
				if ( !$this->recover_file( $path ) ) {
					$this->log( "🚫 Could not recover $path." );
					error_log( "Media Cleaner: Could not recover $path." );
				}
			}
			if ( !wp_update_post( array( 'ID' => $issue->postId, 'post_type' => 'attachment' ) ) ) {
				$this->log( "🚫 Failed to Untrash Post {$issue->postId} (but deleted it from Cleaner DB)." );
				error_log( "Media Cleaner: Failed to Untrash Post {$issue->postId} (but deleted it from Cleaner DB)." );
				return false;
			}
			$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET deleted = 0 WHERE id = %d", $id ) );
			$this->log( "✅ Recovered Media #{$issue->postId}." );
			return true;
		}
	}

	function trash_file( $fileIssuePath ) {
		$originalPath = trailingslashit( $this->upload_path ) . $fileIssuePath;
		$trashPath = trailingslashit( $this->get_trashdir() ) . $fileIssuePath;
		$path_parts = pathinfo( $trashPath );

		try {
			if ( !file_exists( $path_parts['dirname'] ) && !wp_mkdir_p( $path_parts['dirname'] ) ) {
				$this->log( "🚫 Could not create the trash directory for Media Cleaner." );
				error_log( "Media Cleaner: Could not create the trash directory." );
				return false;
			}
			// Rename the file (move). 'is_dir' is just there for security (no way we should move a whole directory)
			if ( is_dir( $originalPath ) ) {
				$this->log( "🚫 Attempted to delete a directory instead of a file ($originalPath). Can't do that." );
				error_log( "Media Cleaner: Attempted to delete a directory instead of a file ($originalPath). Can't do that." );
				return false;
			}
			if ( !file_exists( $originalPath ) ) {
				$this->log( "🚫 The file $originalPath actually does not exist." );
				error_log( "Media Cleaner: The file $originalPath actually does not exist." );
				return true;
			}
			if ( !@rename( $originalPath, $trashPath ) ) {
				error_log( "Media Cleaner: Unknown error occured while trying to delete a file ($originalPath)." );
				return false;
			}
		}
		catch ( Exception $e ) {
			return false;
		}
		$this->clean_dir( dirname( $originalPath ) );
		return true;
	}

	function ignore( $id, $ignore ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $this->get_issue( $id );
		if ( !$ignore ) {
			$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET ignored = 0 WHERE id = %d", $id ) );
		}
		else {
			// If it is in trash, recover it
			if ( $issue->deleted ) {
				$this->recover( $id );
			}
			$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET ignored = 1 WHERE id = %d", $id ) );
		}
		return true;
	}

	function endsWith( $haystack, $needle )
	{
	  $length = strlen( $needle );
	  if ( $length == 0 )
	    return true;
	  return ( substr( $haystack, -$length ) === $needle );
	}

	function clean_dir( $dir ) {
		if ( !file_exists( $dir ) )
			return;
		else if ( $this->endsWith( $dir, 'uploads' ) )
			return;
		$found = array_diff( scandir( $dir ), array( '.', '..' ) );
		if ( count( $found ) < 1 ) {
			if ( rmdir( $dir ) ) {
				$this->clean_dir( dirname( $dir ) );
			}
		}
	}

	function get_issue( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ), OBJECT );
		$issue->id = (int)$issue->id;
		$issue->postId = (int)$issue->postId;
		$issue->type = (int)$issue->type;
		$issue->deleted = (int)$issue->deleted;
		$issue->ignored = (int)$issue->ignored;
		$issue->path = stripslashes( $issue->path );
		return $issue;
	}

	function delete( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $this->get_issue( $id );

		if ( !isset( $issue ) ) {
			$this->log( "🚫 Issue {$id} could not be found." );
			return false;
		}

		$regex = "^(.*)(\\s\\(\\+.*)$";
		$issue->path = preg_replace( '/' . $regex . '/i', '$1', $issue->path ); // remove " (+ 6 files)" from path
		$skip_trash = $this->get_option( 'skip_trash' );

		if ( $issue->type === 0 ) {

			// Delete file from the trash
			if ( $issue->deleted === 1 ) {
				$trashPath = trailingslashit( $this->get_trashdir() ) . $issue->path;
				if ( unlink( $trashPath ) ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id = %d", $id ) );
					$this->clean_dir( dirname( $trashPath ) );
					return true;
				}
			}
			// Delete file without using trash
			else if ( $skip_trash ) {
				$originalPath = trailingslashit( $this->upload_path ) . $issue->path;
				if ( unlink( $originalPath ) ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id = %d", $id ) );
					$this->clean_dir( dirname( $originalPath ) );
					return true;
				}
			}
			// Move file to the trash
			else  if ( $this->trash_file( $issue->path ) ) {
				$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET deleted = 1, ignored = 0 WHERE id = %d", $id ) );
				return true;
			}

			$this->log( "🚫 Failed to delete/trash the file." );
			error_log( "Media Cleaner: Failed to delete/trash the file." );
		}

		if ( $issue->type === 1 ) {

			// Trash Media definitely by recovering it (to be like a normal Media) and remove it through the
			// standard WordPress workflow
			if ( $issue->deleted === 1 || $skip_trash  ) {
				if ( $issue->deleted === 1 ) {
					$this->recover( $id );
				}
				wp_update_post( array( 'ID' => $issue->postId, 'post_type' => 'attachment' ) );
				wp_delete_attachment( $issue->postId, true );
				$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id = %d", $id ) );
				return true;
			}
			else {
				// Move Media to trash
				// Let's copy the images to the trash so that it can be recovered.
				$paths = $this->get_paths_from_attachment( $issue->postId );
				foreach ( $paths as $path ) {
					if ( !$this->trash_file( $path ) ) {
						$this->log( "🚫 Could not trash $path." );
						error_log( "Media Cleaner: Could not trash $path." );
					}
				}
				wp_update_post( array( 'ID' => $issue->postId, 'post_type' => 'wmpc-trash' ) );
				$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET deleted = 1, ignored = 0 WHERE id = %d", $id ) );
				return true;
			}
		}
		return false;
	}

	/**
	 *
	 * SCANNING / RESET
	 *
	 */

	function add_reference_url( $urlOrUrls, $type, $origin = null, $extra = null ) {
		$urlOrUrls = !is_array( $urlOrUrls ) ? array( $urlOrUrls ) : $urlOrUrls;
		foreach ( $urlOrUrls as $url ) {
			// With files, we need both filename without resolution and filename with resolution, it's important
			// to make sure the original file is not deleted if a size exists for it.
			// With media, all URLs should be without resolution to make sure it matches Media.
			if ( $this->current_method == 'files' ) {
				$this->add_reference( null, $url, $type, $origin );
				$this->add_reference( 0, $this->clean_url_from_resolution( $url ), $type, $origin );
			}
			else {
				// 2021/11/08: I added this, the problem is that sometimes users create image filenames with the resolution
				// in it, even though it is the original.
				$this->add_reference( null, $url, $type, $origin );

				$this->add_reference( 0, $this->clean_url_from_resolution( $url ), $type, $origin );
			}
		}
	}

	function add_reference_id( $idOrIds, $type, $origin = null, $extra = null ) {
		$idOrIds = !is_array( $idOrIds ) ? array( $idOrIds ) : $idOrIds;
		foreach ( $idOrIds as $id ) {
			$this->add_reference( $id, "", $type, $origin );
			if ( $this->multilingual ) {
				$translatedIds = $this->get_translated_media_ids( (int)$id );
				
				// Test for WPML
				// if ( $id ===  '350') {
				// 	$translatedIds = $this->get_translated_media_ids( (int)$id );
				// 	$count = count($translatedIds);
				// 	error_log( "${id} => ${count}" );
				// }

				if ( !empty( $translatedIds ) ) {
					foreach ( $translatedIds as $translatedId ) {
						$this->add_reference( $translatedId, "", $type, $origin );
					}
				}
			}
		}
	}

	private $cached_ids = array();
	private $cached_urls = array();

	// The references are actually not being added directly in the DB, they are being pushed
	// into a cache ($this->refcache).
	private function add_reference( $id, $url, $type, $origin = null, $extra = null ) {

		if ( !empty( $origin ) ) {
			$type = $type . " [$origin]";
		}

		if ( !empty( $id ) ) {
			if ( !in_array( $id, $this->cached_ids ) ) {
				array_push( $this->cached_ids, $id );
				array_push( $this->refcache, array( 'id' => $id, 'url' => null, 'type' => $type, 'origin' => $origin ) );
			}
		}
		if ( !empty( $url ) ) {
			// The URL shouldn't contain http, https, javascript at the beginning (and there are probably many more cases)
			// The URL must be cleaned before being passed as a reference.
			if ( substr( $url, 0, 5 ) === "http:" || substr( $url, 0, 6 ) === "https:" || substr( $url, 0, 11 ) === "javascript:" ) {
				return;
			}
			if ( !in_array( $url, $this->cached_urls ) ) {
				array_push( $this->cached_urls, $url );
				array_push( $this->refcache, array( 'id' => null, 'url' => $url, 'type' => $type, 'origin' => $origin ) );
			}
		}
	}

	// The cache containing the references is wrote to the DB.
	function write_references() {
		global $wpdb;
		$table = $wpdb->prefix . "mclean_refs";
		$values = array();
		$place_holders = array();
		$query = "INSERT INTO $table (mediaId, mediaUrl, originType) VALUES ";
		foreach ( $this->refcache as $value ) {
			if ( !is_null( $value['id'] ) ) {
				array_push( $values, $value['id'], $value['type'] );
				$place_holders[] = "('%d',NULL,'%s')";
				if ( $this->debug_logs ) {
					$this->log( "＋ Media #{$value['id']} (as ID)" );
				}
			}
			else if ( !is_null( $value['url'] ) ) {
				array_push( $values, $value['url'], $value['type'] );
				$place_holders[] = "(NULL,'%s','%s')";
				if ( $this->debug_logs ) {
					$this->log( "＋ {$value['url']}" );
				}
			}
		}
		if ( !empty( $values ) ) {
			$query .= implode( ', ', $place_holders );
			$prepared = $wpdb->prepare( "$query ", $values );
			$wpdb->query( $prepared );
		}
		$this->refcache = array();
	}

	function check_is_ignore( $file ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$count = $wpdb->get_var( "SELECT COUNT(*)
			FROM $table_name
			WHERE ignored = 1
			AND path LIKE '%".  esc_sql( $wpdb->esc_like( $file ) ) . "%'" );
		if ( $count > 0 ) {
			$this->log( "🚫 Could not trash $file." );
		}
		return ($count > 0);
	}

	function find_media_id_from_file( $file, $doLog ) {
		global $wpdb;
		$postmeta_table_name = $wpdb->prefix . 'postmeta';
		$file = $this->clean_uploaded_filename( $file );
		$sql = $wpdb->prepare( "SELECT post_id
			FROM {$postmeta_table_name}
			WHERE meta_key = '_wp_attached_file'
			AND meta_value = %s", $file
		);
		$ret = $wpdb->get_var( $sql );
		if ( $doLog ) {
			if ( empty( $ret ) )
				$this->log( "🚫 File $file not found as _wp_attached_file (Library)." );
			else {
				$this->log( "✅ File $file found as Media $ret." );
			}
		}

		return $ret;
	}

	function get_image_sizes() {
		$sizes = array();
		global $_wp_additional_image_sizes;
		foreach ( get_intermediate_image_sizes() as $s ) {
			$crop = false;
			if ( isset( $_wp_additional_image_sizes[$s] ) ) {
				$width = intval( $_wp_additional_image_sizes[$s]['width'] );
				$height = intval( $_wp_additional_image_sizes[$s]['height'] );
				$crop = $_wp_additional_image_sizes[$s]['crop'];
			} else {
				$width = get_option( $s.'_size_w' );
				$height = get_option( $s.'_size_h' );
				$crop = get_option( $s.'_crop' );
			}
			$sizes[$s] = array( 'width' => $width, 'height' => $height, 'crop' => $crop );
		}
		return $sizes;
	}

	function clean_url_from_resolution( $url ) {
		$pattern = '/[_-]\d+x\d+(?=\.[a-z]{3,4}$)/';
		$url = preg_replace( $pattern, '', $url );
		return $url;
	}

	function is_url( $url ) {
		return ( (
			!empty( $url ) ) &&
			is_string( $url ) &&
			strlen( $url ) > 4 && (
				strtolower( substr( $url, 0, 4) ) == 'http' || $url[0] == '/'
			)
		);
	}

	function clean_url_from_resolution_ref( &$url ) {
		$url = $this->clean_url_from_resolution( $url );
	}

	// From a url to the shortened and cleaned url (for example '2013/02/file.png')
	function clean_url( $url ) {
		// if ( is_array( $url ) ) {
		// 	error_log( print_r( $url, 1 ) );
		// }
		$dirIndex = strpos( $url, $this->upload_url );
		if ( empty( $url ) || $dirIndex === false ) {
			$finalUrl =  null;
		}
		else {
			$finalUrl = urldecode( substr( $url, 1 + strlen( $this->upload_url ) + $dirIndex ) );
		}
		return $finalUrl;
	}

	// From a fullpath to the shortened and cleaned path (for example '2013/02/file.png')
	// Original version by Jordy
	// function clean_uploaded_filename( $fullpath ) {
	// 	$basedir = $this->upload_path;
	// 	$file = str_replace( $basedir, '', $fullpath );
	// 	$file = str_replace( "./", "", $file );
	// 	$file = trim( $file,  "/" );
	// 	return $file;
	// }

	// From a fullpath to the shortened and cleaned path (for example '2013/02/file.png')
	// Faster version, more difficult to read, by Mike Meinz
	function clean_uploaded_filename( $fullpath ) {
		$dirIndex = strpos( $fullpath, $this->upload_url );
		if ( $dirIndex == false ) {
			$file = $fullpath;
		}
		else {
		// Remove first part of the path leaving yyyy/mm/filename.ext
			$file = substr( $fullpath, 1 + strlen( $this->upload_url ) + $dirIndex );
		}
		if ( substr( $file, 0, 2 ) == './' ) {
			$file = substr( $file, 2 );
		}
		if ( substr( $file, 0, 1 ) == '/' ) {
			$file = substr( $file, 1 );
		}
		return $file;
	}

	/*
		Check if the file or the Media ID is used in the install.
		That file or ID will be checked against the database of references created by the plugin
		by the parsers.
	*/
	public function reference_exists( $file, $mediaId ) {
		global $wpdb;
		$table = $wpdb->prefix . "mclean_refs";
		$row = null;
		if ( !empty( $mediaId ) ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT originType FROM $table WHERE mediaId = %d", $mediaId ) );
			if ( !empty( $row ) ) {
				$origin = $row->originType === 'MEDIA LIBRARY' ? 'Media Library' : 'content';
				$this->log( "✅ Media #{$mediaId} used by {$origin}" );
				return $row->originType;
			}
		}
		if ( !empty( $file ) ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT originType FROM $table WHERE mediaUrl = %s", $file ) );
			if ( !empty( $row ) ) {
				$origin = $row->originType === 'MEDIA LIBRARY' ? 'Media Library' : 'content';
				$this->log( "✅ File {$file} used by {$origin}" );
				return $row->originType;
			}
		}
		return false;
	}

	function get_paths_from_attachment( $attachmentId ) {
		$paths = array();
		$fullpath = get_attached_file( $attachmentId );
		if ( empty( $fullpath ) ) {
			error_log( 'Media Cleaner: Could not find attached file for Media ID ' . $attachmentId );
			return array();
		}
		$mainfile = $this->clean_uploaded_filename( $fullpath );
		array_push( $paths, $mainfile );
		$baseUp = pathinfo( $mainfile );
		$filespath = trailingslashit( $this->upload_path ) . trailingslashit( $baseUp['dirname'] );
		$meta = wp_get_attachment_metadata( $attachmentId );
		if ( isset( $meta['original_image'] ) ) {
			$original_image = $this->clean_uploaded_filename( $filespath . $meta['original_image'] );
			array_push( $paths, $original_image );
		}
		$isImage = isset( $meta, $meta['width'], $meta['height'] );
		$sizes = $this->get_image_sizes();
		if ( $isImage && isset( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $name => $attr ) {
				if  ( isset( $attr['file'] ) ) {
					$file = $this->clean_uploaded_filename( $filespath . $attr['file'] );
					array_push( $paths, $file );
				}
			}
		}
		return $paths;
	}

	function is_media_ignored( $attachmentId ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE postId = %d", $attachmentId ), OBJECT );
		//error_log( $attachmentId );
		//error_log( print_r( $issue, 1 ) );
		if ( $issue && $issue->ignored )
			return true;
		return false;
	}

	function check_media( $attachmentId, $checkOnly = false ) {

		// Is Media ID ignored, consider as used.
		if ( $this->is_media_ignored( $attachmentId ) ) {
			return true;
		}

		// Remove everything related to this media from the database.
		if ( !$checkOnly ) {
			$this->delete_attachment_related_data( $attachmentId );
		}

		$size = 0;
		$countfiles = 0;
		$check_broken_media = !$this->check_content;
		$fullpath = get_attached_file( $attachmentId );
		$is_broken = apply_filters( 'wpmc_is_file_broken', !file_exists( $fullpath ), $attachmentId );

		// It's a broken-only scan
		if ( $check_broken_media && !$is_broken ) {
			$is_considered_used = apply_filters( 'wpmc_check_media', true, $attachmentId, false );
			return $is_considered_used;
		}

		// Let's analyze the usage of each path (thumbnails included) for this Media ID.
		$issue = 'NO_CONTENT';
		$paths = $this->get_paths_from_attachment( $attachmentId );
		foreach ( $paths as $path ) {
			
			// If it's found in the content, we stop the scan right away
			if ( $this->check_content && $this->reference_exists( $path, $attachmentId ) ) {
				$is_considered_used = apply_filters( 'wpmc_check_media', true, $attachmentId, false );
				if ( $is_considered_used ) {
					return true;
				}
			}

			// Let's count the size of the files for later, in case it's unused
			$filepath = trailingslashit( $this->upload_path ) . $path;
			if ( file_exists( $filepath ) )
				$size += filesize( $filepath );
			$countfiles++;
		}
		
		// This Media ID seems not in used (or broken)
		// Let's double-check through the filter (overridable by users)
		$is_considered_used = apply_filters( 'wpmc_check_media', false, $attachmentId, $is_broken );
		if ( !$is_considered_used ) {
			if ( $is_broken ) {
				$this->log( "🚫 File {$fullpath} does not exist." );
				$issue = 'ORPHAN_MEDIA';
			}
			if ( !$checkOnly ) {
				global $wpdb;
				$table_name = $wpdb->prefix . "mclean_scan";
				$mainfile = $this->clean_uploaded_filename( $fullpath );
				$wpdb->insert( $table_name,
					array(
						'time' => current_time('mysql'),
						'type' => 1,
						'size' => $size,
						'path' => $mainfile . ( $countfiles > 0 ? ( " (+ " . $countfiles . " files)" ) : "" ),
						'postId' => $attachmentId,
						'issue' => $issue
						)
					);
			}
		}
		return $is_considered_used;
	}

	// Delete all issues
	function reset_issues( $includingIgnored = false ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		if ( $includingIgnored ) {
			$wpdb->query( "DELETE FROM $table_name WHERE deleted = 0" );
		}
		else {
			$wpdb->query( "DELETE FROM $table_name WHERE ignored = 0 AND deleted = 0" );
		}
		if ( file_exists( WPMC_PATH . '/logs/media-cleaner.log' ) ) {
			file_put_contents( WPMC_PATH . '/logs/media-cleaner.log', '' );
		}
		$table_name = $wpdb->prefix . "mclean_refs";
		$wpdb->query("TRUNCATE $table_name");
	}

	function get_issue_for_postId( $postId ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE postId = %d", $postId ), OBJECT );
		return $issue;
	}

	function echo_issue( $issue ) {
		if ( $issue == 'NO_CONTENT' ) {
			_e( "Not found in content", 'media-cleaner' );
		}
		else if ( $issue == 'ORPHAN_FILE' ) {
			_e( "Not in Library", 'media-cleaner' );
		}
		else if ( $issue == 'ORPHAN_RETINA' ) {
			_e( "Orphan Retina", 'media-cleaner' );
		}
		else if ( $issue == 'ORPHAN_WEBP' ) {
			_e( "Orphan WebP", 'media-cleaner' );
		}
		else if ( $issue == 'ORPHAN_MEDIA' ) {
			_e( "No attached file", 'media-cleaner' );
		}
		else {
			echo $issue;
		}
	}

	/**
	 *
	 * Roles & Access Rights
	 *
	 */
	public function can_access_settings() {
		return apply_filters( 'wpmc_allow_setup', current_user_can( 'manage_options' ) );
	}

	public function can_access_features() {
		return apply_filters( 'wpmc_allow_usage', current_user_can( 'administrator' ) );
	}

	#region Options 

	function list_options() {
		return array(
			'method' => 'media',
			'content' => true,
			'filesystem_content' => false,
			'media_library' => true,
			'live_content' => false,
			'debuglogs' => false,
			'images_only' => false,
			'attach_is_use' => false,
			'thumbnails_only' => false,
			'dirs_filter' => '',
			'files_filter' => '',
			'hide_thumbnails' => false,
			'hide_warning' => false,
			'skip_trash' => false,
			'medias_buffer' => 100,
			'posts_buffer' => 5,
			'analysis_buffer' => 100,
			'file_op_buffer' => 20,
			'delay' => 100,
			'shortcodes_disabled' => false,
			'posts_per_page' => 10,
			'clean_uninstall' => false,
		);
	}

	function reset_options() {
		delete_option( $this->option_name );
	}

	function get_option( $option ) {
		$options = $this->get_all_options();
		return $options[$option];
	}

	function get_all_options() {
		$options = get_option( $this->option_name, null );
		$options = $this->check_options( $options );
		return $options;
	}

	// Let's work on this function if we need it.
	// Right now, it looks like the options are all updated at the same time.

	// function update_option( $option, $value ) {
	// 	if ( !array_key_exists( $name, $options ) ) {
	// 		return new WP_REST_Response([ 'success' => false, 'message' => 'This option does not exist.' ], 200 );
	// 	}
	//  $value = is_bool( $params['value'] ) ? ( $params['value'] ? '1' : '' ) : $params['value'];
	// }

	function update_options( $options ) {
		if ( !update_option( $this->option_name, $options, false ) ) {
			return false;
		}
		$options = $this->sanitize_options();
		return $options;
	}

	// Upgrade from the old way of storing options to the new way.
	function check_options( $options = [] ) {
		$plugin_options = $this->list_options();
		$options = empty( $options ) ? [] : $options;
		$hasChanges = false;
		foreach ( $plugin_options as $option => $default ) {
			// The option already exists
			if ( isset( $options[$option] ) ) {
				continue;
			}
			// The option does not exist, so we need to add it.
			// Let's use the old value if any, or the default value.
			$options[$option] = get_option( 'wpmc_' . $option, $default );
			delete_option( 'wpmc_' . $option );
			$hasChanges = true;
		}
		if ( $hasChanges ) {
			update_option( $this->option_name , $options );
		}
		return $options;
	}

	// Validate and keep the options clean and logical.
	function sanitize_options() {
		$options = $this->get_all_options();
		$medias = $options['medias_buffer'];
		$posts = $options['posts_buffer'];
		$analysis = $options['analysis_buffer'];
		$fileOp = $options['file_op_buffer'];
		$delay = $options['delay'];
		$hasChanges = false;
		if ( $medias === '' ) {
			$options['medias_buffer'] = 100;
			$hasChanges = true;
		}
		if ( $posts === '' ) {
			$options['posts_buffer'] = 5;
			$hasChanges = true;
		}
		if ( $analysis === '' ) {
			$options['analysis_buffer'] = 100;
			$hasChanges = true;
		}
		if ( $fileOp === '' ) {
			$options['file_op_buffer'] = 20;
			$hasChanges = true;
		}
		if ( $delay === '' ) {
			$options['delay'] = 100;
			$hasChanges = true;
		}
		if ( $hasChanges ) {
			update_option( $this->option_name, $options, false );
		}
		return $options;
	}

	#endregion
}

// Check the DB. If does not exist, let's create it.
// TODO: When PHP 7 only, let's clean this and use anonymous functions.
function wpmc_check_database() {
	global $wpdb;
	static $wpmc_check_database_done = false;
	if ( $wpmc_check_database_done ) {
		return true;
	}
	$table_scan = $wpdb->prefix . "mclean_refs";
	$table_refs = $wpdb->prefix . "mclean_scan";
	$db_init = !( strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_scan'" ) ) != strtolower( $table_scan )
		|| strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_refs'" ) ) != strtolower( $table_refs ) );
	if ( !$db_init ) {
		wpmc_create_database();
		$db_init = !( strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_scan'" ) ) != strtolower( $table_scan )
			|| strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_refs'" ) ) != strtolower( $table_refs ) );
	}
	$wpmc_check_database_done = true;
}

function wpmc_create_database() {
	global $wpdb;
	$table_name = $wpdb->prefix . "mclean_scan";
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
		id BIGINT(20) NOT NULL AUTO_INCREMENT,
		time DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
		type TINYINT(1) NOT NULL,
		postId BIGINT(20) NULL,
		path TINYTEXT NULL,
		size INT(9) NULL,
		ignored TINYINT(1) NOT NULL DEFAULT 0,
		deleted TINYINT(1) NOT NULL DEFAULT 0,
		issue TINYTEXT NOT NULL,
		PRIMARY KEY  (id)
	) " . $charset_collate . ";" ;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	$sql="ALTER TABLE $table_name ADD INDEX IgnoredIndex (ignored) USING BTREE;";
	$wpdb->query($sql);
	$table_name = $wpdb->prefix . "mclean_refs";
	$charset_collate = $wpdb->get_charset_collate();
	// This key doesn't work on too many installs because of the 'Specified key was too long' issue
	// KEY mediaLookUp (mediaId, mediaUrl)
	$sql = "CREATE TABLE $table_name (
		id BIGINT(20) NOT NULL AUTO_INCREMENT,
		mediaId BIGINT(20) NULL,
		mediaUrl TINYTEXT NULL,
		originType TINYTEXT NOT NULL,
		PRIMARY KEY  (id)
	) " . $charset_collate . ";";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function wpmc_remove_database() {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "mclean_scan";
	$table_name2 = $wpdb->prefix . "mclean_refs";
	$table_name3 = $wpdb->prefix . "wpmcleaner";
	$sql = "DROP TABLE IF EXISTS $table_name1, $table_name2, $table_name3;";
	$wpdb->query( $sql );
}

#region Install / Uninstall

/*
	INSTALL / UNINSTALL
*/

function wpmc_init( $mainfile ) {
	//register_activation_hook( $mainfile, 'wpmc_install' );
	//register_deactivation_hook( $mainfile, 'wpmc_uninstall' );
	register_uninstall_hook( $mainfile, 'wpmc_uninstall' );
}

function wpmc_install() {
	wpmc_create_database();
}

function wpmc_reset () {
	wpmc_remove_database();
	wpmc_create_database();
}

function wpmc_remove_options() {
	global $wpdb;
	$options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'wpmc_%'" );
	foreach( $options as $option ) {
		delete_option( $option->option_name );
	}
}

function wpmc_uninstall () {
	$options = get_option( 'wpmc_options', [] );
	$cleanUninstall = $options['clean_uninstall'];
	if ($cleanUninstall) {
		wpmc_remove_options();
		wpmc_remove_database();
	}
}

#endregion