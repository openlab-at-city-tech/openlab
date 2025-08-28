<?php
/**
 * Editor Class.
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
 * Soliloquy Editor
 *
 * @since 2.5.0
 */
class Soliloquy_Editor_Lite {

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
	 * Flag to determine if media modal is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $loaded = false;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load the base class object.
		$this->base = Soliloquy_Lite::get_instance();

		// Add a custom media button to the editor.
		add_filter( 'media_buttons', [ $this, 'media_button' ] );
	}

	/**
	 * Adds a custom slider insert button beside the media uploader button.
	 *
	 * @since 1.0.0
	 *
	 * @param string $buttons  The media buttons context HTML.
	 * @return string $buttons Amended media buttons context HTML.
	 */
	public function media_button( $buttons ) {

		// Create the media button.
		$button  = '<style type="text/css">@media only screen and (-webkit-min-device-pixel-ratio: 2),only screen and (min--moz-device-pixel-ratio: 2),only screen and (-o-min-device-pixel-ratio: 2/1),only screen and (min-device-pixel-ratio: 2),only screen and (min-resolution: 192dpi),only screen and (min-resolution: 2dppx) { #soliloquy-media-modal-button .soliloquy-media-icon[style] { background-image: url(' . plugins_url( 'assets/css/images/editor-icon@2x.png', $this->base->file ) . ') !important; background-size: 16px 16px !important; } }</style>';
		$button .= '<a id="soliloquy-media-modal-button" href="#" class="button soliloquy-choose-slider" title="' . esc_attr__( 'Add Slider', 'soliloquy' ) . '" style="padding-left: .4em;"><span class="soliloquy-media-icon" style="background: transparent url(' . plugins_url( 'assets/css/images/editor-icon.png', $this->base->file ) . ') no-repeat scroll 0 0; width: 16px; height: 16px; display: inline-block; vertical-align: text-top;"></span> ' . __( 'Add Slider', 'soliloquy' ) . '</a>';

		// Enqueue the script that will trigger the editor button.
		wp_enqueue_script( $this->base->plugin_slug . '-editor-script', plugins_url( 'assets/js/min/editor-min.js', $this->base->file ), [ 'jquery' ], $this->base->version, true );
		wp_localize_script(
			$this->base->plugin_slug . '-editor-script',
			'soliloquy_metabox',
			[
				'ajax'       => admin_url( 'admin-ajax.php' ),
				'load_nonce' => wp_create_nonce( 'soliloquy-load-slider' ),
			]
		);

		// Add the action to the footer to output the modal window.
		add_action( 'admin_footer', [ $this, 'slider_selection_modal' ] );

		// Compat for Elementor.
		// @codingStandardsIgnoreStart
		// If Elementor UI is here, then we need to output this since there's no admin_footer
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) {
			echo $this->get_slider_selection_modal();
		}
		// @codingStandardsIgnoreEnd
		// Append the button.
		return $buttons . $button;
	}

	/**
	 * Outputs the slider selection modal to insert a slider into an editor.
	 *
	 * @since 1.0.0
	 */
	public function slider_selection_modal() {
		// @codingStandardsIgnoreStart
		echo $this->get_slider_selection_modal();
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Returns the slider selection modal to insert a slider into an editor.
	 *
	 * @since 1.0.0
	 *
	 * @global object $post The current post object.
	 * @return string Empty string if no sliders are found, otherwise modal UI.
	 */
	public function get_slider_selection_modal() {

		// Return early if already loaded.
		if ( $this->loaded ) {
			return '';
		}

		// Set the loaded flag to true.
		$this->loaded = true;

		global $post;
		$sliders = $this->base->get_sliders();

		// Return early if no sliders are found.
		if ( empty( $sliders ) ) {
			return '';
		}

		ob_start();
		?>
		<div class="soliloquy-default-ui-wrapper" style="display: none;">
			<div class="soliloquy-default-ui soliloquy-image-meta">
				<div class="media-modal wp-core-ui">
					<a class="media-modal-close" href="#"><span class="media-modal-icon"></span>
					</a>
					<div class="media-modal-content">
						<div class="media-frame wp-core-ui hide-menu hide-router soliloquy-meta-wrap">
							<div class="media-frame-title">
								<h1><?php esc_html_e( 'Choose Your Slider', 'soliloquy' ); ?></h1>
							</div>
							<div class="media-frame-content">
								<div class="attachments-browser">
									<ul class="soliloquy-meta attachments" style="padding-left: 8px; top: 1em;">
										<?php
										foreach ( (array) $sliders as $slider ) :
											// Return early if no slider ID.
											if ( ! isset( $slider['id'] ) || ( isset( $post->ID ) && $post->ID === $slider['id'] ) ) {
												continue;
											}
											?>
										<li class="attachment" data-soliloquy-id="<?php echo absint( $slider['id'] ); ?>" style="margin: 8px;">
											<div class="attachment-preview">
												<div class="thumbnail">
													<div class="inside">
														<?php
														if ( ! empty( $slider['config']['title'] ) ) {
															$title = $slider['config']['title'];
														} elseif ( ! empty( $slider['config']['slug'] ) ) {
															$title = $slider['config']['title'];
														} else {
															/* translators: %s: slider id */
															$title = sprintf( __( 'Slider ID #%s', 'soliloquy' ), $slider['id'] );
														}
														?>
														<h3 style="margin: 0;"><?php echo esc_html( $title ); ?></h3>
														<code>[soliloquy id="<?php echo absint( $slider['id'] ); ?>"]</code>
													</div>
												</div>
												<a class="check" href="#"><div class="media-modal-icon"></div></a>
											</div>
										</li>
										<?php endforeach; ?>
									</ul>
									<!-- end .soliloquy-meta -->
									<div class="media-sidebar">
										<div class="soliloquy-meta-sidebar">
											<h3 style="margin: 1.4em 0 1em;"><?php esc_html_e( 'Helpful Tips', 'soliloquy' ); ?></h3>
											<strong><?php esc_html_e( 'Choosing Your Slider', 'soliloquy' ); ?></strong>
											<p style="margin: 0 0 1.5em;"><?php esc_html_e( 'To choose your slider, simply click on one of the boxes to the left. The "Insert Slider" button will be activated once you have selected a slider.', 'soliloquy' ); ?></p>
											<strong><?php esc_html_e( 'Inserting Your Slider', 'soliloquy' ); ?></strong>
											<p style="margin: 0 0 1.5em;"><?php esc_html_e( 'To insert your slider into the editor, click on the "Insert Slider" button below.', 'soliloquy' ); ?></p>
										</div>
										<!-- end .soliloquy-meta-sidebar -->
									</div>
									<!-- end .media-sidebar -->
								</div>
								<!-- end .attachments-browser -->
							</div>
							<!-- end .media-frame-content -->
							<div class="media-frame-toolbar">
								<div class="media-toolbar">
									<div class="media-toolbar-secondary">
										<a href="#" class="soliloquy-cancel-insertion button media-button button-large button-secondary media-button-insert" title="<?php esc_attr_e( 'Cancel Slider Insertion', 'soliloquy' ); ?>"><?php esc_html_e( 'Cancel Slider Insertion', 'soliloquy' ); ?></a>
									</div>
									<div class="media-toolbar-primary">
										<a href="#" class="soliloquy-insert-slider button media-button button-large button-primary media-button-insert" disabled="disabled" title="<?php esc_attr_e( 'Insert Slider', 'soliloquy' ); ?>"><?php esc_html_e( 'Insert Slider', 'soliloquy' ); ?></a>
									</div>
									<!-- end .media-toolbar-primary -->
								</div>
								<!-- end .media-toolbar -->
							</div>
							<!-- end .media-frame-toolbar -->
						</div>
						<!-- end .media-frame -->
					</div>
					<!-- end .media-modal-content -->
				</div>
				<!-- end .media-modal -->
				<div class="media-modal-backdrop"></div>
			</div><!-- end #soliloquy-default-ui -->
		</div><!-- end #soliloquy-default-ui-wrapper -->
		<?php
		return ob_get_clean();
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Soliloquy_Editor_Lite object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Editor_Lite ) ) {
			self::$instance = new Soliloquy_Editor_Lite();
		}

		return self::$instance;
	}
}

// Load the editor class.
$soliloquy_editor_lite = Soliloquy_Editor_Lite::get_instance();
