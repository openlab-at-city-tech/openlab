<?php

namespace PDFEmbedder;

use PDFEmbedder\Tasks\Tasks;
use PDFEmbedder\Helpers\Assets;
use PDFEmbedder\Helpers\Multisite;
use PDFEmbedder\Shortcodes\PdfEmbedder;

/**
 * Main plugin class.
 *
 * @since 4.7.0
 */
final class Plugin {

	/**
	 * Premium-specific flag.
	 * Do not use directly, there are wrappers available.
	 *
	 * @since 4.7.0
	 *
	 * @see Plugin::is_premium()
	 * @see Plugin::premium()
	 *
	 * @var Premium\Plugin
	 */
	public $premium;

	/**
	 * Instance of the plugin main Admin class.
	 *
	 * @since 4.7.0
	 *
	 * @var Admin\Admin
	 */
	private $admin;

	/**
	 * Instance of the plugin main Options class.
	 *
	 * @since 4.7.0
	 *
	 * @var Options
	 */
	private $options;

	/**
	 * Instance of the plugin main Tasks class.
	 *
	 * @since 4.7.0
	 *
	 * @var Tasks
	 */
	private $tasks;

	/**
	 * Prepare classes.
	 * This method acts as a container constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {

		$this->admin   = new Admin\Admin();
		$this->options = new Options();
		$this->tasks   = new Tasks();
	}

	/**
	 * Assign all hooks to proper places.
	 * They are listed in their loading order.
	 *
	 * @since 4.7.0
	 */
	public function hooks() {

		// Initialize Action Scheduler tasks a bit earlier than the rest of the plugin.
		add_action( 'init', [ $this->tasks, 'init' ], 5 );

		add_action( 'init', [ $this, 'hook_init' ] );

		// Admin menu registration should fire on 'admin_menu' hook that runs before 'admin_init'.
		add_action(
			Multisite::is_network_activated() ? 'network_admin_menu' : 'admin_menu',
			[ $this->admin, 'register_menu' ]
		);

		add_action( 'admin_init', [ $this, 'hook_admin_init' ] );

		/**
		 * Plugin is loaded.
		 * You can extend it now.
		 *
		 * @since 4.7.0
		 *
		 * @param Plugin $plugin The plugin instance.
		 */
		do_action( 'pdfemb_loaded', $this );
	}

	/**
	 * Plugin logic that should be available on both front- and back-end.
	 *
	 * @since 4.7.0
	 */
	public function hook_init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$shortcode = new PdfEmbedder();

		add_shortcode( PdfEmbedder::TAG, [ $shortcode, 'render' ] );

		register_block_type(
			PDFEMB_PLUGIN_DIR . 'block/build/block.json',
			[
				'render_callback' => [ $shortcode, 'render' ],
			]
		);

		add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_assets' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 5, 0 );
	}

	/**
	 * Register front-end scripts.
	 * TODO: move to the Viewer.
	 *
	 * @since 4.7.0
	 */
	public function enqueue_scripts() {

		// PDF.js library.
		wp_register_script(
			'pdfemb_pdfjs',
			Assets::url( 'js/pdfjs/pdf.js' ),
			[ 'jquery' ],
			'2.2.228',
			false
		);

		wp_register_script(
			'pdfemb_embed_pdf',
			Assets::url( 'js/pdfemb.js' ),
			[ 'jquery', 'pdfemb_pdfjs' ],
			Assets::ver(),
			false
		);

		$front = [
			'worker_src' => Assets::url( 'js/pdfjs/pdf.worker.js' ),
			'cmap_url'   => PDFEMB_PLUGIN_URL . 'assets/js/pdfjs/cmaps/',
			'objectL10n' => [
				'loading'            => esc_html__( 'Loading...', 'pdf-embedder' ),
				'page'               => esc_html__( 'Page', 'pdf-embedder' ),
				'zoom'               => esc_html__( 'Zoom', 'pdf-embedder' ),
				'prev'               => esc_html__( 'Previous page', 'pdf-embedder' ),
				'next'               => esc_html__( 'Next page', 'pdf-embedder' ),
				'zoomin'             => esc_html__( 'Zoom In', 'pdf-embedder' ),
				'secure'             => esc_html__( 'Secure', 'pdf-embedder' ),
				'zoomout'            => esc_html__( 'Zoom Out', 'pdf-embedder' ),
				'download'           => esc_html__( 'Download PDF', 'pdf-embedder' ),
				'fullscreen'         => esc_html__( 'Full Screen', 'pdf-embedder' ),
				'domainerror'        => esc_html__( 'Error: URL to the PDF file must be on exactly the same domain as the current web page.', 'pdf-embedder' ),
				'clickhereinfo'      => esc_html__( 'Click here for more info', 'pdf-embedder' ),
				'widthheightinvalid' => esc_html__( 'PDF page width or height are invalid', 'pdf-embedder' ),
				'viewinfullscreen'   => esc_html__( 'View in Full Screen', 'pdf-embedder' ),
			],
		];

		// Translatable strings.
		wp_localize_script( 'pdfemb_embed_pdf', 'pdfemb_trans', $front );
	}

	/**
	 * Register block editor scripts.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_block_assets() {

		if ( ! is_admin() ) {
			return;
		}

		// As the block is extended by Premium, we need this "once" flag to load all assets only once.
		static $once = false;

		if ( $once ) {
			return;
		}

		$once = true;

		$options   = pdf_embedder()->options()->get();
		$processed = [];

		foreach ( $options as $key => $value ) {

			if ( is_array( $value ) ) {
				continue;
			}

			$processed[ str_replace( 'pdfemb_', '', $key ) ] = $value;
		}

		$data = 'const pdfembPluginOptions=' . wp_json_encode( $processed ) . ';';

		wp_add_inline_script( 'pdfemb-pdf-embedder-viewer-editor-script', $data, 'before' );
	}

	/**
	 * Plugin logic that should be available in admin area only.
	 *
	 * @since 4.7.0
	 */
	public function hook_admin_init() {

		$this->admin->init();
		$this->admin->hooks();

		/**
		 * Plugin admin area is initialized.
		 * You can extend it now.
		 *
		 * @since 4.7.0
		 */
		do_action( 'pdfemb_admin_init' );
	}

	/**
	 * Check if the plugin is a paid version.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	public function is_premium(): bool {

		return $this->premium !== null;
	}

	/**
	 * Premium plugin instance.
	 *
	 * @since 4.7.0
	 *
	 * @return Premium\Plugin
	 */
	public function premium() {

		return $this->is_premium() ? $this->premium : null;
	}

	/**
	 * Get access to the plugin admin area.
	 *
	 * @since 4.7.0
	 */
	public function admin(): Admin\Admin {

		return $this->admin;
	}

	/**
	 * Get the Tasks code of the plugin.
	 *
	 * @since 4.7.0
	 */
	public function tasks(): Tasks {

		return $this->tasks;
	}

	/**
	 * Get/Load the Options code of the plugin.
	 *
	 * @since 4.7.0
	 */
	public function options(): Options {

		return $this->options;
	}

	/**
	 * Activation hook.
	 *
	 * @since 4.7.0
	 */
	public static function activated() {

		// Activation time, added only once.
		add_option( 'wppdf_emb_activation', time(), '', 'no' );
	}
}
