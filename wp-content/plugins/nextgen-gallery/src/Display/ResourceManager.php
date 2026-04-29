<?php

namespace Imagely\NGG\Display;

use Imagely\NGG\Util\URL;

/**
 * Manager for handling resource buffering and positioning.
 */
class ResourceManager {

	/**
	 * Singleton instance.
	 *
	 * @var ResourceManager|null
	 */
	public static $instance = null;

	/**
	 * Marker string for resource positioning.
	 *
	 * @var string
	 */
	public $marker = '<!-- ngg_resource_manager_marker -->';

	/**
	 * Output buffer.
	 *
	 * @var string
	 */
	public $buffer = '';

	/**
	 * Buffered styles.
	 *
	 * @var string
	 */
	public $styles = '';

	/**
	 * Buffered scripts.
	 *
	 * @var string
	 */
	public $scripts = '';

	/**
	 * Other buffered output.
	 *
	 * @var string
	 */
	public $other_output = '';

	/**
	 * Whether footer has been written.
	 *
	 * @var bool
	 */
	public $wrote_footer = false;

	/**
	 * Whether to run shutdown callback.
	 *
	 * @var bool
	 */
	public $run_shutdown = false;

	/**
	 * Whether this is a valid request.
	 *
	 * @var bool
	 */
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

	/**
	 * Initialize the resource manager.
	 *
	 * @return ResourceManager The initialized instance.
	 */
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
			print wp_kses_post( $this->marker );
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
	 * Check if resource manager is disabled.
	 *
	 * @return bool Whether the resource manager is disabled.
	 */
	public static function is_disabled(): bool {
		// This is admittedly an ugly hack, but much easier than reworking the entire nextgen_admin modules.
		//
		// Nonce verification is not necessary here.
		//
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( isset( $_GET['page'] ) && 'ngg_addgallery' === wp_unslash( $_GET['page'] ) && isset( $_GET['attach_to_post'] ) ) {
			return false;
		}

		// Provide users a method of forcing this on should it be necessary.
		if ( defined( 'NGG_ENABLE_RESOURCE_MANAGER' ) && NGG_ENABLE_RESOURCE_MANAGER ) {
			return false;
		}

		return self::addons_version_check();
	}

	/**
	 * Determines if the resource manager should perform it's routines for this request
	 *
	 * @return bool Whether the request is valid.
	 */
	public function is_valid_request() {
		$retval = true;

		// Nonce check is not necessary: this is not processing a form, but determining if this class' main feature
		// should be executed or not.
		//
        // phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// Do not apply to NextGEN's admin page.
		if ( is_admin() && isset( $_REQUEST['page'] ) && ! preg_match( '#^(ngg|nextgen)#', wp_unslash( $_REQUEST['page'] ) ) ) {
			$retval = false;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';

		// Skip anything found in the WP-Admin.
		if ( preg_match( '#wp-admin/#', $request_uri ) ) {
			$retval = false;
		} elseif ( isset( $_GET['display_gallery_iframe'] ) ) { // Legacy custom-post based displayed galleries loaded in an iframe.
			$retval = false;
		} elseif ( defined( 'WP_ADMIN' ) && WP_ADMIN && defined( 'DOING_AJAX' ) && DOING_AJAX ) { // Skip XHR requests.
			$retval = false;
		} elseif ( strpos( $request_uri, '/nextgen-image/' ) !== false ) { // Skip NGG's 'dynamic thumbnails' URL endpoints.
			$retval = false;
		} elseif ( preg_match( '/(js|css|xsl|xml|kml)$/', $request_uri ) ) { // Do not process proxy loaders of static resources.
			$retval = false;
		} elseif ( preg_match( '#/feed(/?)$#i', $request_uri ) || ! empty( $_GET['feed'] ) ) { // Skip the RSS feed.
			$retval = false;
		} elseif ( false !== strpos( $request_uri, 'nextgen-dcss?name' ) ) { // Skip the 'dynamic stylesheets' URL endpoints used by Pro.
			return false;
		} elseif ( false !== strpos( $request_uri, 'nextgen-gallery/src/Legacy/' ) ) { // Skip any files belonging to the NGG 1.x days.
			return false;
		} elseif ( preg_match( '/\\.(\\w{3,4})$/', $request_uri, $match ) ) { // Do not process requests made directly to files.
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ! in_array( $match[1], [ 'htm', 'html', 'php' ] ) ) {
				$retval = false;
			}
		} elseif ( ( isset( $_SERVER['PATH_INFO'] ) && strpos( wp_unslash( $_SERVER['PATH_INFO'] ), 'nextgen-pro-lightbox-gallery' ) !== false ) || strpos( $request_uri, 'nextgen-pro-lightbox-gallery' ) !== false ) { // Skip legacy versions of the Pro Lightbox.
			$retval = false;
		} elseif ( $this->is_rest_request() ) { // And lastly skip all REST endpoints.
			$retval = false;
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return $retval;
	}

	/**
	 * Check if this is a REST request.
	 *
	 * @return bool Whether this is a REST request.
	 */
	public function is_rest_request() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return defined( 'REST_REQUEST' ) || ( isset( $_SERVER['REQUEST_URI'] ) && strpos( wp_unslash( $_SERVER['REQUEST_URI'] ), 'wp-json' ) !== false );
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

	/**
	 * Get buffered resources.
	 */
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
	 * @param string $content The buffer content.
	 * @return string
	 */
	public function output_buffer_handler( $content ) {
		return $this->output_buffer();
	}

	/**
	 * Removes the closing </html> tag from the output buffer. We'll then write our own closing tag
	 * in the shutdown function after running wp_print_footer_scripts()
	 *
	 * @param string $content The buffer content.
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
	 * @param bool $in_shutdown Whether this is being called during shutdown.
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
			} elseif ( strpos( $this->buffer, '</body>' ) === false ) { // We don't want to manipulate the buffer if it doesn't contain HTML.
				$this->valid_request = false;
			}           // The output_buffer() function has been called in the PHP shutdown callback
			// This will allow us to print the scripts ourselves and manipulate the buffer.
			if ( true === $in_shutdown && $this->valid_request ) {
				ob_start();
				if ( ! did_action( 'wp_footer' ) ) {
					wp_footer();
				} else {
					wp_print_footer_scripts();
				}
				$this->other_output = ob_get_clean();
				$this->buffer       = str_ireplace( '</body>', $this->marker . '</body>', $this->buffer );
			} else { // W3TC isn't activated and we're not in the shutdown callback. We'll therefore add a shutdown callback to print the scripts.
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
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputting entire processed HTML buffer
			echo $this->output_buffer( true );
		}
	}
}
