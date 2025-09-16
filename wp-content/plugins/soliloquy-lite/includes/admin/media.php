<?php
/**
 * Media Class.
 *
 * @since 2.5.0
 * @package SoliloquyWP Lite
 * @author SoliloquyWP Team <support@soliloquywp.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Soliloquy Media
 *
 * @since 2.5.0
 */
class Soliloquy_Media_Lite {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Path to the file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Holds the base class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $base;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load the base class object.
		$this->base = Soliloquy_Lite::get_instance();
	}

	/**
	 * Prepares a custom media upload form that allows multiple forms on one page.
	 *
	 * @since 1.0.0
	 *
	 * @return null Return early if the form cannot be output.
	 */
	public function media_upload_form() {

		do_action( 'pre-upload-ui' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

		if ( ! $this->device_can_upload() ) {
			// Translators: Url Link in open tag dont translate.
			echo '<p>' . sprintf( esc_html__( 'The web browser on your device cannot be used to upload files. You may be able to use the <a href="%s">native app for your device</a> instead.', 'soliloquy' ), 'http://wordpress.org/mobile/' ) . '</p>';
			return;
		}

		if ( ! $this->has_upload_capacity() ) {
			do_action( 'upload_ui_over_quota' );
			return;
		}

		// Get both resize width and height for the media form.
		$width  = $this->get_resize_width();
		$height = $this->get_resize_height();

		// Output the media form.
		$this->do_media_upload_form( $width, $height );
	}

	/**
	 * Outputs a custom media upload form that allows multiple forms on one page.
	 *
	 * @since 1.0.0
	 *
	 * @global bool $ie_IE    Flag for Internet Explorer.
	 * @global bool $is_opera Flag for Opera.
	 * @param int $width      The media resize width.
	 * @param int $height     The media resize height.
	 */
	public function do_media_upload_form( $width, $height ) {

		// Prepare globals and variables.
		global $is_IE, $is_opera;
		$sizes           = [ 'KB', 'MB', 'GB' ];
		$max_upload_size = wp_max_upload_size();

		?>
		<script type="text/javascript">var resize_width = <?php echo esc_attr( $width ); ?>, resize_height = <?php echo esc_attr( $height ); ?>;</script>
		<div id="soliloquy-upload-error"></div>
		<div id="soliloquy-plupload-upload-ui" class="hide-if-no-js">
			<?php do_action( 'pre-plupload-upload-ui' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores ?>
			<div id="soliloquy-drag-drop-area">
				<div class="drag-drop-inside">
					<p class="drag-drop-info"><?php esc_html_e( 'Drop images here', 'soliloquy' ); ?></p>
					<p><?php esc_html_e( 'Uploader: Drop images here - or - Select Images', 'soliloquy' ); ?></p>
					<p class="drag-drop-buttons">
						<input id="soliloquy-plupload-browse-button" type="button" value="<?php esc_attr_e( 'Select Images', 'soliloquy' ); ?>" class="button" />
					</p>
				</div>
			</div>
			<?php do_action( 'post-plupload-upload-ui' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores ?>
		</div>

		<div id="soliloquy-html-upload-ui" class="hide-if-js">
			<?php do_action( 'pre-html-upload-ui' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores ?>
			<p id="soliloquy-async-upload-wrap">
				<label class="screen-reader-text" for="soliloquy-async-upload"><?php esc_html_e( 'Upload', 'soliloquy' ); ?></label>
				<input type="file" name="async-upload" id="soliloquy-async-upload" />
				<?php submit_button( __( 'Upload', 'soliloquy' ), 'button', 'html-upload', false ); ?>
				<a href="#" onclick="try{top.tb_remove();}catch(e){};return false;"><?php esc_html_e( 'Cancel', 'soliloquy' ); ?></a>
			</p>
			<div class="clear"></div>
			<?php do_action( 'post-html-upload-ui' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores ?>
		</div>
		<?php // Translators: %1$s - Max Upload Size, do not translate. %2$s - Max Upload Size, do not translate. ?>
		<span class="max-upload-size"><?php printf( esc_html__( 'Maximum upload file size: %1$d%2$s.', 'soliloquy' ), esc_html( $this->get_upload_size_unit( $sizes ) ), esc_html( $sizes[ $this->get_upload_size_unit( $sizes, 'unit' ) ] ) ); ?></span>
		<?php

		// Output a notice if the browser may have trouble with uploading large images.
		if ( ( $is_IE || $is_opera ) && $max_upload_size > 100 * 1024 * 1024 ) {
			echo '<span class="big-file-warning">' . esc_html__( 'Your browser has some limitations uploading large files with the multi-file uploader. Please use the browser uploader for files over 100MB.', 'soliloquy' ) . '</span>';
		}

		do_action( 'post-upload-ui' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	}

	/**
	 * Flag if the device can upload images.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if it can, false otherwise.
	 */
	public function device_can_upload() {

		// Why is this method internal? It is quite useful.
		return _device_can_upload();
	}

	/**
	 * Flag if the site has the capacity to receive an upload.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if it can, false otherwise.
	 */
	public function has_upload_capacity() {

		return ! ( is_multisite() && ! is_upload_space_available() );
	}

	/**
	 * Returns the resize width for the media form.
	 *
	 * @since 1.0.0
	 *
	 * @return int $width The resize width.
	 */
	public function get_resize_width() {

		$width = absint( get_option( 'large_size_w' ) );
		if ( ! $width ) {
			$width = 1024;
		}

		return $width;
	}

	/**
	 * Returns the resize height for the media form.
	 *
	 * @since 1.0.0
	 *
	 * @return int $width The resize height.
	 */
	public function get_resize_height() {

		$height = absint( get_option( 'large_size_h' ) );
		if ( ! $height ) {
			$height = 1024;
		}

		return $height;
	}

	/**
	 * Returns the upload unit for the media uploader.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $sizes Array of sizes to use for units.
	 * @param string $type Type of unit to retrieve ('size' or 'unit').
	 */
	public function get_upload_size_unit( $sizes, $type = 'size' ) {

		$upload_size_unit = wp_max_upload_size();
		for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ ) { // phpcs:ignore Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed,Squiz.PHP.DisallowSizeFunctionsInLoops.Found
			$upload_size_unit /= 1024;
		}

		// If the upload size is 0, disable uploading, otherwise allow uploading to continue.
		if ( $u < 0 ) {
			$upload_size_unit = 0;
			$u                = 0;
		} else {
			$upload_size_unit = (int) $upload_size_unit;
		}

		return 'unit' === $type ? $u : $upload_size_unit;
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Soliloquy_Media_Lite object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Media_Lite ) ) {
			self::$instance = new Soliloquy_Media_Lite();
		}

		return self::$instance;
	}
}

// Load the media class.
$soliloquy_media_lite = Soliloquy_Media_Lite::get_instance();
