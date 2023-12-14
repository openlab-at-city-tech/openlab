<?php

namespace Imagely\NGG\Display;

use Imagely\NGG\Util\URL;

class ResourceManager {

	static $instance = null;

	public $marker = '<!-- ngg_resource_manager_marker -->';

	public $buffer        = '';
	public $styles        = '';
	public $scripts       = '';
	public $other_output  = '';
	public $wrote_footer  = false;
	public $run_shutdown  = false;
	public $valid_request = true;

	/**
	 * Start buffering all generated output. We'll then do two things with the buffer
	 * 1) Find stylesheets lately enqueued and move them to the header
	 * 2) Ensure that wp_print_footer_scripts() is called
	 */
	public function __construct() {
		// Validate the request.
		$this->validate_request();

		add_action( 'init', [ $this, 'start_buffer' ], -1 );
		add_action( 'wp_footer', [ $this, 'print_marker' ], -1 );
	}

	public static function init() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new ResourceManager();
		}
		return self::$instance;
	}

	/**
	 * Created early as possible in the wp_footer action this is the string to which we
	 * will move JS resources after
	 */
	public function print_marker() {
		if ( self::is_disabled() ) {
			return;
		}

		// is_feed() is important to not break WordPress feeds and the WooCommerce api.
		if ( $this->valid_request && ! is_feed() ) {
			print $this->marker;
		}
	}

	/**
	 * Determines if the resource manager should perform it's routines for this request
	 */
	public function validate_request() {
		$this->valid_request = $this->is_valid_request();
	}

	/**
	 * Pro, Plus, and Starter versions below these were not ready to function without the resource manager
	 *
	 * @return bool
	 */
	public static function addons_version_check() {
		if ( defined( 'NGG_PRO_PLUGIN_VERSION' ) && version_compare( NGG_PRO_PLUGIN_VERSION, '3.3', '<' ) ) {
			return false;
		}
		if ( defined( 'NGG_STARTER_PLUGIN_VERSION' ) && version_compare( NGG_STARTER_PLUGIN_VERSION, '1.1', '<' ) ) {
			return false;
		}
		if ( defined( 'NGG_PLUS_PLUGIN_VERSION' ) && version_compare( NGG_PLUS_PLUGIN_VERSION, '1.8', '<' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public static function is_disabled(): bool {
		// This is admittedly an ugly hack, but much easier than reworking the entire nextgen_admin modules.
		//
		// Nonce verification is not necessary here.
		//
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'] ) && 'ngg_addgallery' === $_GET['page'] && isset( $_GET['attach_to_post'] ) ) {
			return false;
		}

		// Provide users a method of forcing this on should it be necessary.
		if ( defined( 'NGG_ENABLE_RESOURCE_MANAGER' ) && NGG_ENABLE_RESOURCE_MANAGER ) {
			return false;
		}

		return self::addons_version_check();
	}

	public function is_valid_request() {
		$retval = true;

		// Nonce check is not necessary: this is not processing a form, but determining if this class' main feature
		// should be executed or not.
		//
        // phpcs:disable WordPress.Security.NonceVerification.Recommended

		// Do not apply to NextGEN's admin page.
		if ( is_admin() && isset( $_REQUEST['page'] ) && ! preg_match( '#^(ngg|nextgen)#', $_REQUEST['page'] ) ) {
			$retval = false;
		}

		// Skip anything found in the WP-Admin.
		if ( preg_match( '#wp-admin/#', $_SERVER['REQUEST_URI'] ) ) {
			$retval = false;
		}
		// Legacy custom-post based displayed galleries loaded in an iframe.
		elseif ( isset( $_GET['display_gallery_iframe'] ) ) {
			$retval = false;
		}
		// Skip XHR requests.
		elseif ( defined( 'WP_ADMIN' ) && WP_ADMIN && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$retval = false;
		}
		// Skip NGG's 'dynamic thumbnails' URL endpoints.
		elseif ( strpos( $_SERVER['REQUEST_URI'], '/nextgen-image/' ) !== false ) {
			$retval = false;
		}
		// Do not process proxy loaders of static resources.
		elseif ( preg_match( '/(js|css|xsl|xml|kml)$/', $_SERVER['REQUEST_URI'] ) ) {
			$retval = false;
		}
		// Skip the RSS feed.
		elseif ( preg_match( '#/feed(/?)$#i', $_SERVER['REQUEST_URI'] ) || ! empty( $_GET['feed'] ) ) {
			$retval = false;
		}
		// Skip the 'dynamic stylesheets' URL endpoints used by Pro.
		elseif ( false !== strpos( $_SERVER['REQUEST_URI'], 'nextgen-dcss?name' ) ) {
			return false;
		}
		// Skip any files belonging to the NGG 1.x days.
		elseif ( false !== strpos( $_SERVER['REQUEST_URI'], 'nextgen-gallery/src/Legacy/' ) ) {
			return false;
		}
		// Do not process requests made directly to files.
		elseif ( preg_match( '/\\.(\\w{3,4})$/', $_SERVER['REQUEST_URI'], $match ) ) {
			if ( ! in_array( $match[1], [ 'htm', 'html', 'php' ] ) ) {
				$retval = false;
			}
		}
		// Skip legacy versions of the Pro Lightbox.
		elseif ( ( isset( $_SERVER['PATH_INFO'] ) && strpos( $_SERVER['PATH_INFO'], 'nextgen-pro-lightbox-gallery' ) !== false ) or strpos( $_SERVER['REQUEST_URI'], 'nextgen-pro-lightbox-gallery' ) !== false ) {
			$retval = false;
		}
		// And lastly skip all REST endpoints.
		elseif ( $this->is_rest_request() ) {
			$retval = false;
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $retval;
	}

	public function is_rest_request() {
		return defined( 'REST_REQUEST' ) || strpos( $_SERVER['REQUEST_URI'], 'wp-json' ) !== false;
	}

	/**
	 * Start the output buffers
	 */
	public function start_buffer() {
		if ( self::is_disabled() ) {
			return;
		}

		if ( apply_filters( 'run_ngg_resource_manager', $this->valid_request ) ) {
			ob_start( [ $this, 'output_buffer_handler' ] );
			ob_start( [ $this, 'get_buffer' ] );

			add_action( 'wp_print_footer_scripts', [ $this, 'get_resources' ], 1 );
			add_action( 'admin_print_footer_scripts', [ $this, 'get_resources' ], 1 );
			add_action( 'shutdown', [ $this, 'shutdown' ] );
		}
	}

	public function get_resources() {
		ob_start();
		wp_print_styles();
		print_admin_styles();
		$this->styles = ob_get_clean();

		if ( ! is_admin() ) {
			ob_start();
			wp_print_scripts();
			$this->scripts = ob_get_clean();
		}

		$this->wrote_footer = true;
	}

	/**
	 * Output the buffer after PHP execution has ended (but before shutdown)
	 *
	 * @param $content
	 * @return string
	 */
	public function output_buffer_handler( $content ) {
		return $this->output_buffer();
	}

	/**
	 * Removes the closing </html> tag from the output buffer. We'll then write our own closing tag
	 * in the shutdown function after running wp_print_footer_scripts()
	 *
	 * @param $content
	 * @return mixed
	 */
	public function get_buffer( $content ) {
		$this->buffer = $content;
		return '';
	}

	/**
	 * Moves resources to their appropriate place
	 */
	public function move_resources() {
		if ( $this->valid_request ) {

			// Move stylesheets to head.
			if ( $this->styles ) {
				$this->buffer = str_ireplace( '</head>', $this->styles . '</head>', $this->buffer );
			}

			// Move the scripts to the bottom of the page.
			if ( $this->scripts ) {
				$this->buffer = str_ireplace( $this->marker, $this->marker . $this->scripts, $this->buffer );
			}

			if ( $this->other_output ) {
				$this->buffer = str_replace( $this->marker, $this->marker . $this->other_output, $this->buffer );
			}
		}
	}

	/**
	 * When PHP has finished, we output the footer scripts and closing tags
	 *
	 * @param bool $in_shutdown
	 * @return string
	 */
	public function output_buffer( $in_shutdown = false ) {
		// If the footer scripts haven't been outputted, then
		// we need to take action - as they're required.
		if ( ! $this->wrote_footer ) {
			// If W3TC is installed and activated, we can't output the scripts and manipulate the buffer, so we can only provide a warning.
			if ( defined( 'W3TC' ) && defined( 'WP_DEBUG' ) && WP_DEBUG && ! is_admin() ) {
				if ( ! defined( 'DONOTCACHEPAGE' ) ) {
					define( 'DONOTCACHEPAGE', true );
				}
				if ( ! did_action( 'wp_footer' ) ) {
					error_log( "We're sorry, but your theme's page template didn't make a call to wp_footer(), which is required by NextGEN Gallery. Please add this call to your page templates." );
				} else {
					error_log( "We're sorry, but your theme's page template didn't make a call to wp_print_footer_scripts(), which is required by NextGEN Gallery. Please add this call to your page templates." );
				}
			}

			// We don't want to manipulate the buffer if it doesn't contain HTML.
			elseif ( strpos( $this->buffer, '</body>' ) === false ) {
				$this->valid_request = false;
			}

			// The output_buffer() function has been called in the PHP shutdown callback
			// This will allow us to print the scripts ourselves and manipulate the buffer.
			if ( $in_shutdown === true && $this->valid_request ) {
				ob_start();
				if ( ! did_action( 'wp_footer' ) ) {
					wp_footer();
				} else {
					wp_print_footer_scripts();
				}
				$this->other_output = ob_get_clean();
				$this->buffer       = str_ireplace( '</body>', $this->marker . '</body>', $this->buffer );
			}

			// W3TC isn't activated and we're not in the shutdown callback. We'll therefore add a shutdown callback to print the scripts.
			else {
				$this->run_shutdown = true;
				return '';
			}
		}

		// Once we have the footer scripts, we can modify the buffer and move the resources around.
		if ( $this->wrote_footer ) {
			$this->move_resources();
		}

		return $this->buffer;
	}

	/**
	 * PHP shutdown callback. Manipulate and output the buffer
	 */
	public function shutdown() {
		if ( $this->run_shutdown ) {
			echo $this->output_buffer( true );
		}
	}
}
