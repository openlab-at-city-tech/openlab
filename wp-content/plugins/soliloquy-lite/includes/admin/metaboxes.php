<?php
/**
 * Metabox Class.
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
 * Soliloquy Metabox
 *
 * @since 2.5.0
 */
class Soliloquy_Metaboxes_Lite {

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
	 * Holds the common class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $common;
	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load the base class object.
		$this->base   = Soliloquy_Lite::get_instance();
		$this->common = Soliloquy_Common_Admin_Lite::get_instance();
		// Load metabox assets.
		add_action( 'admin_enqueue_scripts', [ $this, 'meta_box_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'meta_box_scripts' ] );

		// Load the metabox hooks and filters.
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 100 );

		// Modals
		// add_filter( 'media_view_strings', array( $this, 'media_view_strings' ) );
		// Load all tabs.
		add_action( 'soliloquy_tab_slider', [ $this, 'images_tab' ] );
		add_action( 'soliloquy_tab_config', [ $this, 'config_tab' ] );
		add_action( 'soliloquy_tab_misc', [ $this, 'misc_tab' ] );
		add_action( 'soliloquy_tab_mobile_lite', [ $this, 'mobile_lite_tab' ] );
		add_action( 'soliloquy_tab_lightbox_lite', [ $this, 'lightbox_lite_tab' ] );
		add_action( 'soliloquy_tab_pinterest_lite', [ $this, 'pinterest_lite_tab' ] );
		add_action( 'soliloquy_tab_schedule_lite', [ $this, 'schedule_lite_tab' ] );
		add_action( 'soliloquy_tab_carousel_lite', [ $this, 'carousel_lite_tab' ] );
		add_action( 'soliloquy_tab_thumbnails_lite', [ $this, 'thumbnails_lite_tab' ] );

		// Add action to save metabox config options.
		add_action( 'save_post', [ $this, 'save_meta_boxes' ], 10, 2 );
	}

	/**
	 * Loads styles for our metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @return null Return early if not on the proper screen.
	 */
	public function meta_box_styles() {

		if ( isset( get_current_screen()->base ) && 'post' !== get_current_screen()->base ) {
			return;
		}

		if ( isset( get_current_screen()->post_type ) && in_array( get_current_screen()->post_type, $this->get_skipped_posttypes(), true ) ) {
			return;
		}

			// Load necessary metabox styles.
			wp_register_style( $this->base->plugin_slug . '-metabox-style', plugins_url( 'assets/css/metabox.css', $this->base->file ), [], $this->base->version );
			wp_enqueue_style( $this->base->plugin_slug . '-metabox-style' );

			wp_register_style( $this->base->plugin_slug . '-codemirror', plugins_url( 'assets/css/codemirror.css', $this->base->file ), [], $this->base->version );
			wp_enqueue_style( $this->base->plugin_slug . '-codemirror' );
			wp_enqueue_style( 'editor-button-css' );
			// Fire a hook to load in custom metabox styles.
			do_action( 'soliloquy_metabox_styles' );
	}

	/**
	 * Loads scripts for our metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @global int $id      The current post ID.
	 * @global object $post The current post object.
	 * @param string $hook  The current screen hook.
	 * @return null         Return early if not on the proper screen.
	 */
	public function meta_box_scripts( $hook ) {

		global $id, $post;

		if ( isset( get_current_screen()->base ) && 'post' !== get_current_screen()->base ) {
			return;
		}

		if ( isset( get_current_screen()->post_type ) && in_array( get_current_screen()->post_type, $this->get_skipped_posttypes(), true ) ) {
			return;
		}

		// Set the post_id for localization.
		$post_id = isset( $post->ID ) ? $post->ID : (int) $id;

			// Sortables.
			wp_enqueue_script( 'jquery-ui-sortable' );

			// Chosen JS.
			wp_register_script( $this->base->plugin_slug . '-chosen', plugins_url( 'assets/js/min/chosen.jquery-min.js', $this->base->file ), [ 'jquery' ], $this->base->version, true );
			wp_enqueue_script( $this->base->plugin_slug . '-chosen' );

			// Image Uploader.
			wp_enqueue_media(
				[
					'post' => $post_id,
				]
			);

			wp_enqueue_script( 'plupload-handlers' );

			// Load Code Mirror.
			wp_register_script( $this->base->plugin_slug . '-codemirror', plugins_url( 'assets/js/lib/codemirror.js', $this->base->file ), [], $this->base->version, true );
			wp_enqueue_script( $this->base->plugin_slug . '-codemirror' );

			// Load Clipboard.
			wp_register_script( $this->base->plugin_slug . '-clipboard', plugins_url( 'assets/js/min/clipboard-min.js', $this->base->file ), [ 'jquery' ], $this->base->version, true );
			wp_enqueue_script( $this->base->plugin_slug . '-clipboard' );

			// Load Chosen.
			wp_register_script( $this->base->plugin_slug . '-chosen', plugins_url( 'assets/js/min/chosen.jquery-min.js', $this->base->file ), [], $this->base->version, true );
			wp_enqueue_script( $this->base->plugin_slug . '-chosen' );

			// Form Conditionals.
			wp_register_script( 'jquery-form-conditionals', plugins_url( 'assets/js/min/jquery.form-conditionals-min.js', $this->base->file ), [ 'jquery', 'plupload-handlers', 'quicktags', 'jquery-ui-sortable', $this->base->plugin_slug . '-codemirror' ], $this->base->version, true );
			wp_enqueue_script( 'jquery-form-conditionals' );

			// media script.
			wp_register_script( $this->base->plugin_slug . '-metabox-script', plugins_url( 'assets/js/min/metabox-min.js', $this->base->file ), [ 'jquery', 'plupload-handlers', 'quicktags', 'jquery-ui-sortable' ], $this->base->version, true );
			wp_enqueue_script( $this->base->plugin_slug . '-metabox-script' );

			wp_localize_script(
				$this->base->plugin_slug . '-metabox-script',
				'soliloquy_media_uploader',
				[
					'ajax'                    => admin_url( 'admin-ajax.php' ),
					'id'                      => $post_id,
					'uploader_files_computer' => __( 'Select Files from Your Computer', 'soliloquy' ),
					'uploader_info_text'      => __( 'Drag and Drop Files to Upload', 'soliloquy' ),
					'load_image'              => wp_create_nonce( 'soliloquy-load-image' ),
					'media_position'          => get_option( 'soliloquy_slide_position' ),
					'heic_error_text'         => esc_attr__( 'HEIC images are not supported when ImageMagick is not enabled. Please convert to JPEG or PNG format.', 'soliloquy' ),
					'is_imagick_enabled'      => extension_loaded( 'imagick' ),
				]
			);

		wp_localize_script(
			$this->base->plugin_slug . '-metabox-script',
			'soliloquy_metabox_local',
			[
				'ajax'               => admin_url( 'admin-ajax.php' ),
				'change_nonce'       => wp_create_nonce( 'soliloquy-change-type' ),
				'id'                 => $post_id,
				'slide_width'        => Soliloquy_Common_Lite::get_instance()->get_config_default( 'slider_width' ),
				'slide_height'       => Soliloquy_Common_Lite::get_instance()->get_config_default( 'slider_height' ),
				'htmlcode'           => esc_attr__( 'HTML Slide Code', 'soliloquy' ),
				'htmlslide'          => esc_attr__( 'HTML Slide Title', 'soliloquy' ),
				'htmlplace'          => esc_attr__( 'Enter HTML slide title here...', 'soliloquy' ),
				'htmlstart'          => esc_attr__( '<!-- Enter your HTML code here for this slide (you can delete this line). -->', 'soliloquy' ),
				'htmluse'            => esc_attr__( 'Select Thumbnail', 'soliloquy' ),
				'import'             => esc_attr__( 'You must select a file to import before continuing.', 'soliloquy' ),
				'insert_nonce'       => wp_create_nonce( 'soliloquy-insert-images' ),
				'inserting'          => esc_attr__( 'Inserting...', 'soliloquy' ),
				'library_search'     => wp_create_nonce( 'soliloquy-library-search' ),
				'load_slider'        => wp_create_nonce( 'soliloquy-load-slider' ),
				'path'               => plugin_dir_path( 'assets' ),
				'hosted_nonce'       => wp_create_nonce( 'soliloquy-is-hosted' ),
				'refresh_nonce'      => wp_create_nonce( 'soliloquy-refresh' ),
				'remove'             => esc_attr__( 'Are you sure you want to remove this slide from the slider?', 'soliloquy' ),
				'remove_multiple'    => esc_attr__( 'Are you sure you want to remove these slides from the slider?', 'soliloquy' ),
				'remove_nonce'       => wp_create_nonce( 'soliloquy-remove-slide' ),
				'removeslide'        => esc_attr__( 'Remove', 'soliloquy' ),
				'save_nonce'         => wp_create_nonce( 'soliloquy-save-meta' ),
				'saving'             => esc_attr__( 'Saving...', 'soliloquy' ),
				'sort'               => wp_create_nonce( 'soliloquy-sort' ),
				'videocaption'       => esc_attr__( 'Video Slide Caption', 'soliloquy' ),
				'videoslide'         => esc_attr__( 'Video Slide Title', 'soliloquy' ),
				'videoplace'         => esc_attr__( 'Enter video slide title here...', 'soliloquy' ),
				'videotitle'         => esc_attr__( 'Video Slide URL', 'soliloquy' ),
				'videothumb'         => esc_attr__( 'Video Slide Placeholder Image', 'soliloquy' ),
				'videosrc'           => esc_attr__( 'Enter your video placeholder image URL here (or leave blank to pull from video itself)...', 'soliloquy' ),
				'videoselect'        => esc_attr__( 'Choose Video Placeholder Image', 'soliloquy' ),
				'videodelete'        => esc_attr__( 'Remove Video Placeholder Image', 'soliloquy' ),
				'videooutput'        => esc_attr__( 'Enter your video URL here...', 'soliloquy' ),
				'videoframe'         => esc_attr__( 'Choose a Video Placeholder Image', 'soliloquy' ),
				'videouse'           => esc_attr__( 'Select Placeholder Image', 'soliloquy' ),
				'selected'           => esc_attr__( 'Selected', 'soliloquy' ),
				'select_all'         => esc_attr__( 'Select All', 'soliloquy' ),
				'insert_placeholder' => esc_attr__( 'Insert Placeholder', 'soliloquy' ),
				'insert_video'       => esc_attr__( 'Insert Video', 'soliloquy' ),
				'expand'             => esc_attr__( 'Expand', 'soliloquy' ),
				'collapse'           => esc_attr__( 'Collapse', 'soliloquy' ),
				'active'             => esc_attr__( 'Active', 'soliloquy' ),
				'draft'              => esc_attr__( 'Draft', 'soliloquy' ),
			]
		);

		// If on an Soliloquy post type, add custom CSS for hiding specific things.
		if ( isset( get_current_screen()->post_type ) && 'soliloquy' === get_current_screen()->post_type ) {
			add_action( 'admin_head', [ $this, 'meta_box_css' ] );
		}

		// Fire a hook to load custom metabox scripts.
		do_action( 'soliloquy_metabox_scripts' );
	}
		/**
		 * Amends the default Plupload parameters for initialising the Media Uploader, to ensure
		 * the uploaded image is attached to our Soliloquy CPT
		 *
		 * @since 1.0.0
		 *
		 * @param array $params Params.
		 * @return array Params.
		 */
	public function plupload_init( $params ) {

		global $post_ID;

		// Define the Soliloquy ID, so Plupload attaches the uploaded images.
		// to this Slider.
		$params['multipart_params']['post_id'] = $post_ID;

		// Build an array of supported file types for Plupload.
		$supported_file_types = Soliloquy_Common_Lite::get_instance()->get_supported_filetypes();

		// Assign supported file types and return.
		$params['filters']['mime_types'] = $supported_file_types;

		// Return and apply a custom filter to our init data.
		$params = apply_filters( 'soliloquy_plupload_init', $params, $post_ID );
		return $params;
	}


	/**
	 * Hides unnecessary meta box items on Soliloquy post type screens.
	 *
	 * @since 1.0.0
	 */
	public function meta_box_css() {

		?>
		<style type="text/css">.misc-pub-section:not(.misc-pub-post-status) { display: none; }</style>
		<?php

		// Fire action for CSS on Soliloquy post type screens.
		do_action( 'soliloquy_admin_css' );
	}

		/**
		 * Creates metaboxes for handling and managing sliders.
		 *
		 * @since 1.0.0
		 */
	public function add_meta_boxes() {

		global $post;

		if ( 'soliloquy' !== $post->post_type ) {

			return;

		}

		// Let's remove all of those dumb metaboxes from our post type screen to control the experience.
		$this->remove_all_the_metaboxes();

		// Get all public post types.
		$post_types = get_post_types( [ 'public' => true ] );
		$custom     = [
			get_option( 'soliloquy_dynamic' ),
			get_option( 'soliloquy_default_slider' ),
		];
		// Splice the soliloquy post type since it is not visible to the public by default.
		$post_types[] = 'soliloquy';

		// Loops through the post types and add the metaboxes.
		foreach ( (array) $post_types as $post_type ) {
			// Don't output boxes on these post types.
			if ( in_array( $post_type, $this->get_skipped_posttypes(), true ) ) {
				continue;
			}
			if ( ! in_array( $post->ID, $custom, true ) ) {

				add_action( 'edit_form_after_title', [ $this, 'uploader_html' ], 10 );
				add_action( 'edit_form_after_title', [ $this, 'settings_html' ], 11 );
				add_meta_box( 'soliloquy-codepanel', __( 'Soliloquy Slider Code', 'soliloquy' ), [ $this, 'code_panel' ], $post_type, 'side', apply_filters( 'soliloquy_metabox_priority', 'low' ) );

			}
		}
		// Output 'Select Files from Other Sources' button on the media uploader form.
		add_action( 'post-plupload-upload-ui', [ $this, 'append_media_upload_form' ], 1 );
		add_action( 'post-html-upload-ui', [ $this, 'append_media_upload_form' ], 1 );
	}

		/**
		 * Renders the Uploader HTML
		 *
		 * @param mixed $post Global $post object.
		 * @return void
		 * @since 2.5
		 */
	public function uploader_html( $post ) {

		global $id, $post;

		if ( isset( get_current_screen()->base ) && 'post' !== get_current_screen()->base ) {
			return;
		}

		if ( isset( get_current_screen()->post_type ) && in_array( get_current_screen()->post_type, $this->get_skipped_posttypes(), true ) ) {
			return;
		}

		// Load view.
		$this->base->load_admin_partial(
			'metabox-slider-type.php',
			[
				'post'     => $post,
				'types'    => $this->get_soliloquy_types( $post ),
				'instance' => $this,
			]
		);
	}
		/**
		 * Appends the "Select Files From Other Sources" button to the Media Uploader, which is called using WordPress'
		 * media_upload_form() function
		 *
		 * CSS positions this button to improve the layout.
		 *
		 * @since 2.5.0
		 */
	public function append_media_upload_form() {

		?>
			<!-- Add from Media Library -->
			<a href="#" class="soliloquy-media-library button"  title="<?php esc_attr_e( 'Click Here to Insert from Other Image Sources', 'soliloquy' ); ?>" style="vertical-align: baseline;">
			<?php esc_html_e( 'Select Files from Other Sources', 'soliloquy' ); ?>
			</a>
			<?php
	}
		/**
		 * The settings_html function.
		 *
		 * @access public
		 * @param mixed $post Post Object.
		 * @return void
		 */
	public function settings_html( $post ) {

		if ( isset( get_current_screen()->base ) && 'post' !== get_current_screen()->base ) {
			return;
		}

		if ( isset( get_current_screen()->post_type ) && in_array( get_current_screen()->post_type, $this->get_skipped_posttypes(), true ) ) {
			return;
		}

		// Keep security first.
		wp_nonce_field( 'soliloquy', 'soliloquy' );

		// Check for our meta overlay helper.
		$slider_data = get_post_meta( $post->ID, '_sol_slider_data', true );
		$helper      = get_post_meta( $post->ID, '_sol_just_published', true );
		?>

			<div id="soliloquy-slider-settings">

				<ul id="soliloquy-settings-tabs" class="soliloquy-tabs-nav soliloquy-clear" data-update-hashbang="1">

				<?php
					$i = 0; foreach ( (array) $this->get_soliloquy_tab_nav() as $id => $title ) :

						$class = 0 === $i ? 'soliloquy-tab-nav-active' : '';
					?>

						<li id="soliloquy-tab-nav-<?php echo esc_attr( $id ); ?>" data-soliloquy-tab class="soliloquy-setting-tab <?php echo esc_attr( $class ); ?>" data-tab-id="soliloquy-tab-<?php echo esc_attr( $id ); ?>"><a href="#soliloquy-tab-<?php echo esc_attr( $id ); ?>" title="<?php echo esc_attr( $title ); ?>"><span><?php echo esc_html( $title ); ?></span></a></li>

					<?php
					++$i;
endforeach;
					?>

				</ul>

				<div id="soliloquy-settings-content" class="soliloquy-clear">

				<?php
				$i = 0; foreach ( (array) $this->get_soliloquy_tab_nav() as $id => $title ) :

					$class = 0 === $i ? 'soliloquy-tab-active' : '';
					?>

					<div id="soliloquy-tab-<?php echo esc_attr( $id ); ?>" class="soliloquy-tab soliloquy-clear <?php echo esc_attr( $class ); ?>">

						<?php do_action( 'soliloquy_tab_' . $id, $post ); ?>

					</div>

					<?php
					++$i;
endforeach;
				?>

				</div>

				<div class="soliloquy-clearfix"></div>

			</div>

			<?php
	}

	/**
	 * Code Panel Markup
	 *
	 * @access public
	 *
	 * @param object $post Post Object.
	 *
	 * @return void
	 */
	public function code_panel( $post ) {

		$slider_data = get_post_meta( $post->ID, '_sol_slider_data', true );

		if ( isset( $post->post_status ) && 'auto-draft' !== $post->post_status ) {

			// Check for our meta overlay helper.
			$helper = get_post_meta( $post->ID, '_sol_just_published', true );
			$class  = '';
			if ( $helper ) {
				$class = 'soliloquy-helper-active';
				delete_post_meta( $post->ID, '_sol_just_published' );
			}
			?>

				<p><?php esc_html_e( 'You can place this slider into your posts, pages, custom post types or widgets using the shortcode below:', 'soliloquy' ); ?></p>
					<code id="soliloquy-shortcode" class="soliloquy-code"><?php echo '[soliloquy id="' . esc_attr( $post->ID ) . '"]'; ?></code>
					<a href="#" class="soliloquy-clipboard" data-clipboard-target="#soliloquy-shortcode"><?php esc_html_e( 'Copy to Clipboard', 'soliloquy' ); ?></a>

				<?php if ( ! empty( $slider_data['config']['slug'] ) ) : ?>
						<br><code id="soliloquy-slug-shortcode" class="soliloquy-code"><?php echo '[soliloquy slug="' . esc_attr( $slider_data['config']['slug'] ) . '"]'; ?></code>
						<a href="#" class="soliloquy-clipboard" data-clipboard-target="#soliloquy-slug-shortcode"><?php esc_html_e( 'Copy to Clipboard', 'soliloquy' ); ?></a>

					<?php endif; ?>


				<p><?php esc_html_e( "You can place this slider into your theme's template files by using the template tag below:", 'soliloquy' ); ?></p>
					<code id="soliloquy-template-tag" class="soliloquy-code"><?php echo 'if ( function_exists( \'soliloquy\' ) ) { soliloquy( \'' . esc_attr( $post->ID ) . '\' ); }'; ?></code>
				<a href="#" class="soliloquy-clipboard" data-clipboard-target="#soliloquy-template-tag"><?php esc_html_e( 'Copy to Clipboard', 'soliloquy' ); ?></a>

				<?php if ( ! empty( $slider_data['config']['slug'] ) ) : ?>

						<br><code id="soliloquy-slug-tag" class="soliloquy-code"><?php echo 'if ( function_exists( \'soliloquy\' ) ) { soliloquy( \'' . esc_attr( $slider_data['config']['slug'] ) . '\', \'slug\' ); }'; ?></code>
					<a href="#" class="soliloquy-clipboard" data-clipboard-target="#soliloquy-slug-tag"><?php esc_html_e( 'Copy to Clipboard', 'soliloquy' ); ?></a>

					<?php endif; ?>

			<?php } ?>

			<h2 class="soliloquy-code-panel-title"><?php esc_html_e( 'Need Help?', 'soliloquy' ); ?></h2>
			<div class="soliloquy-yt">
			<iframe width="560" height="315" src="https://www.youtube.com/embed/DAR_dL3biWw?si=BP2lNqEMrrkF9LaJ" frameborder="0" allowfullscreen></iframe>
			</div>
			<?php
	}


	/**
	 * Removes all the metaboxes except the ones I want on MY POST TYPE. RAGE.
	 *
	 * @since 1.0.0
	 *
	 * @global array $wp_meta_boxes Array of registered metaboxes.
	 * @return void
	 */
	public function remove_all_the_metaboxes() {

		global $wp_meta_boxes;

		// This is the post type you want to target. Adjust it to match yours.
		$post_type = 'soliloquy';

		// These are the metabox IDs you want to pass over. They don't have to match exactly. preg_match will be run on them.
		$pass_over = [ 'submitdiv', 'soliloquy' ];

		// All the metabox contexts you want to check.
		$contexts = [ 'normal', 'advanced', 'side' ];

		// All the priorities you want to check.
		$priorities = [ 'high', 'core', 'default', 'low' ];

		// Loop through and target each context.
		foreach ( $contexts as $context ) {
			// Now loop through each priority and start the purging process.
			foreach ( $priorities as $priority ) {
				if ( isset( $wp_meta_boxes[ $post_type ][ $context ][ $priority ] ) ) {
					foreach ( (array) $wp_meta_boxes[ $post_type ][ $context ][ $priority ] as $id => $metabox_data ) {
						// If the metabox ID to pass over matches the ID given, remove it from the array and continue.
						if ( in_array( $id, $pass_over, true ) ) {
							unset( $pass_over[ $id ] );
							continue;
						}

						// Otherwise, loop through the pass_over IDs and if we have a match, continue.
						foreach ( $pass_over as $to_pass ) {
							if ( preg_match( '#^' . $id . '#i', $to_pass ) ) {
								continue;
							}
						}

						// If we reach this point, remove the metabox completely.
						unset( $wp_meta_boxes[ $post_type ][ $context ][ $priority ][ $id ] );
					}
				}
			}
		}
	}

	/**
	 * Callback for getting all of the tabs for Soliloquy sliders.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of tab information.
	 */
	public function get_soliloquy_tab_nav() {

		$tabs                    = [
			'slider' => __( 'Slider', 'soliloquy' ),
			'config' => __( 'Configuration', 'soliloquy' ),
		];
		$tabs                    = apply_filters( 'soliloquy_tab_nav', $tabs );
		$tabs['mobile_lite']     = __( 'Mobile', 'soliloquy' );
		$tabs['thumbnails_lite'] = __( 'Thumbnails', 'soliloquy' );
		$tabs['carousel_lite']   = __( 'Carousel', 'soliloquy' );
		$tabs['pinterest_lite']  = __( 'Pinterest', 'soliloquy' );
		$tabs['lightbox_lite']   = __( 'Lightbox', 'soliloquy' );
		$tabs['schedule_lite']   = __( 'Schedule', 'soliloquy' );

		// "Misc" tab is required.
		$tabs['misc'] = __( 'Misc', 'soliloquy' );

		return $tabs;
	}

	/**
	 * Callback for displaying the UI for main images tab.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function images_tab( $post ) {

			// Output a notice if missing cropping extensions because Soliloquy needs them.
		if ( ! $this->has_gd_extension() && ! $this->has_imagick_extension() ) {
			?>
				<div class="error below-h2">
					<p><strong><?php esc_html_e( 'The GD or Imagick libraries are not installed on your server. Soliloquy requires at least one (preferably Imagick) in order to crop images and may not work properly without it. Please contact your webhost and ask them to compile GD or Imagick for your PHP install.', 'soliloquy' ); ?></strong></p>
				</div>
				<?php
		}

			// Output the slider type selection items.
		?>

			<?php

			// Output the display based on the type of slider being created.
			echo '<div id="soliloquy-slider-main" class="soliloquy-clear">';

				$this->images_display( $this->get_config( 'type', $this->get_config_default( 'type' ) ), $post );

			echo '</div>';
	}

	/**
	 * Returns the types of sliders available.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 * @return array       Array of slider types to choose.
	 */
	public function get_soliloquy_types( $post ) {

		$types = [
			'default' => __( 'Default', 'soliloquy' ),
		];

		return apply_filters( 'soliloquy_slider_types', $types, $post );
	}

	/**
	 * Determines the Images tab display based on the type of slider selected.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type The type of display to output.
	 * @param object $post The current post object.
	 */
	public function images_display( $type = 'default', $post = false ) {

		// Output a unique hidden field for settings save testing for each type of slider.
		echo '<input type="hidden" name="_soliloquy[type_' . esc_attr( $type ) . ']" value="1" />';

		// Output the display based on the type of slider available.
		switch ( $type ) {
			case 'default':
				$this->do_default_display( $post );
				break;
			default:
				do_action( 'soliloquy_display_' . $type, $post );
				break;
		}
	}

	/**
	 * Callback for displaying the default slider UI.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function do_default_display( $post ) {

			// Prepare output data.
			$slider_data = get_post_meta( $post->ID, '_sol_slider_data', true );

		if ( ! empty( $slider_data ) && ! empty( $slider_data['slider'] ) && is_array( $slider_data['slider'] ) ) {

			$maybe_update = $this->maybe_update_slides( $slider_data['slider'] );

			if ( false === $maybe_update ) {

				$slider_data = $this->update_slides( $post->ID );

			}
		}

			// Check if slider has an admin view set.
		if ( isset( $slider_data['admin_view'] ) && '' !== $slider_data['admin_view'] ) {

			$slide_view = $slider_data['admin_view'];

		} else {

			$slide_view = get_option( 'soliloquy_slide_view' );

		}
			// Get View from settings.
			$default_view = 'grid' === $slide_view ? 'soliloquy-grid' : 'soliloquy-list';
			// Show/Hide stuff.
			$visible    = empty( $slider_data['slider'] ) ? ' soliloquy-hidden' : '';
			$notvisible = ! empty( $slider_data['slider'] ) ? ' soliloquy-hidden' : 'soliloquy-show';

		?>
			<div id="soliloquy-empty-slider" class="<?php echo esc_attr( $notvisible ); ?>">
				<div>
				<img class="soliloquy-item-img" src="<?php echo esc_url( plugins_url( 'assets/images/logo-color.png', $this->base->file ) ); ?>" />
				<h3><?php esc_html_e( 'Create your slider by adding your media files above.', 'soliloquy' ); ?></h3>
				<p class="soliloquy-help-text"><?php esc_html_e( 'Need some help?', 'soliloquy' ); ?> <a href="http://soliloquywp.com/docs/creating-your-first-slider/" target="_blank"><?php esc_html_e( 'Watch a video how to add media and create a slider', 'soliloquy' ); ?></a></p>
				</div>
			</div>

			<div class="soliloquy-slide-header<?php echo esc_attr( $visible ); ?>">

				<h2 class="soliloquy-intro"><?php esc_html_e( 'Currently in Your Slider', 'soliloquy' ); ?></h2>

				<ul class="soliloquy-list-inline soliloquy-display-toggle">
					<li><a href="#" class="soliloquy-display-grid soliloquy-display<?php echo 'grid' === $slide_view ? ' active-display' : ''; ?>" data-soliloquy-display="grid"><i class="soliloquy-icon-grid"></i></a></li>
					<li><a href="#" class="soliloquy-display-list soliloquy-display<?php echo 'list' === $slide_view ? ' active-display' : ''; ?>" data-soliloquy-display="list"><i class="soliloquy-icon-list"></i></a></li>
				</ul>

				<label>
					<input class="soliloquy-select-all" type="checkbox">
					<span class="select-all"><?php esc_html_e( 'Select All', 'soliloquy' ); ?></span> (<span class="soliloquy-count">0</span>)
					<a href="#" class="soliloquy-clear-selected"><?php esc_html_e( 'Clear Selected', 'soliloquy' ); ?></a>
				</label>

			</div>

			<div class="soliloquy-bulk-actions">

				<a href="#" class="button button-soliloquy-delete soliloquy-slides-delete"><?php esc_html_e( 'Delete selected files from slider', 'soliloquy' ); ?></a>
				<a href="#" class="button button-soliloquy-secondary soliloquy-slides-edit"><?php esc_html_e( 'Edit Selected Slides', 'soliloquy' ); ?></a>

			</div>

			<ul id="soliloquy-output" class="<?php echo esc_attr( $default_view ); ?> soliloquy-clear" data-view="<?php echo esc_attr( $slide_view ); ?>">

				<?php if ( ! empty( $slider_data['slider'] ) ) : ?>

					<?php foreach ( $slider_data['slider'] as $id => $data ) : ?>

						<?php echo $this->get_slider_item( $id, $data, ( ! empty( $data['type'] ) ? $data['type'] : 'image' ), $post->ID ); // @codingStandardsIgnoreLine ?>

					<?php endforeach; ?>

				<?php endif; ?>

			</ul>

			<div class="soliloquy-bulk-actions">

				<a href="#" class="button button-soliloquy-delete soliloquy-slides-delete"><?php esc_html_e( 'Delete selected files from slider', 'soliloquy' ); ?></a>
				<a href="#" class="button button-soliloquy-secondary soliloquy-slides-edit"><?php esc_html_e( 'Edit Selected Slides', 'soliloquy' ); ?></a>

			</div>

			<div class="soliloquy-alert">
				<p class="soliloquy-intro"><?php esc_html_e( 'Want to make your slider workflow even better?', 'soliloquy' ); ?></p>
				<p><?php esc_html_e( 'By upgrading to Soliloquy Pro, you can get access to numerous other features, including: a fully featured slider widget, complete slider API, powerful slider documentation, full mobile and Retina support, dedicated customer support and so much more!' ); ?></p>
				<a href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy"><?php esc_attr_e( 'Click here to Upgrade', 'soliloquy' ); ?></a>
			</div>
			<?php
	}

	/**
	 * Callback for displaying the UI for setting slider config options.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function config_tab( $post ) {

		?>
		<div id="soliloquy-config">
			<p class="soliloquy-intro"><?php esc_html_e( 'The settings below adjust the basic configuration options for the slider display.', 'soliloquy' ); ?></p>
			<table class="form-table">
				<tbody>
					<tr id="soliloquy-config-slider-theme-box">
						<th scope="row">
							<label for="soliloquy-config-slider-theme"><?php esc_html_e( 'Slider Theme', 'soliloquy' ); ?></label>
						</th>
						<td>
							<div class="soliloquy-select">
							<select id="soliloquy-config-slider-theme" name="_soliloquy[slider_theme]" class="soliloquy-chosen" data-soliloquy-chosen-options='{ "disable_search":"true", "width": "100%" }'>
								<?php foreach ( (array) $this->get_slider_themes() as $i => $data ) : ?>
									<option value="<?php echo esc_attr( $data['value'] ); ?>"<?php selected( $data['value'], $this->get_config( 'slider_theme', $this->get_config_default( 'slider_theme' ) ) ); ?>><?php echo esc_html( $data['name'] ); ?></option>
								<?php endforeach; ?>
							</select>
							</div>
							<p class="description"><?php esc_html_e( 'Sets the theme for the slider display.', 'soliloquy' ); ?></p>
						</td>
					</tr>
					<tr id="soliloquy-config-slider-size-box">
						<th scope="row">
							<label for="soliloquy-config-slider-width"><?php esc_html_e( 'Slider Dimensions', 'soliloquy' ); ?></label>
						</th>
						<td>
							<input id="soliloquy-config-slider-width" type="number" name="_soliloquy[slider_width]" value="<?php echo esc_attr( $this->get_config( 'slider_width', $this->get_config_default( 'slider_width' ) ) ); ?>" /> &#215; <input id="soliloquy-config-slider-height" type="number" name="_soliloquy[slider_height]" value="<?php echo esc_attr( $this->get_config( 'slider_height', $this->get_config_default( 'slider_height' ) ) ); ?>" /> <span class="soliloquy-unit"><?php esc_html_e( 'px', 'soliloquy' ); ?></span>
							<p class="description"><?php esc_html_e( 'Sets the width and height dimensions for the slider.', 'soliloquy' ); ?></p>
						</td>
					</tr>
					<tr id="soliloquy-config-transition-box">
						<th scope="row">
							<label for="soliloquy-config-transition"><?php esc_html_e( 'Slider Transition', 'soliloquy' ); ?></label>
						</th>
						<td>
							<div class="soliloquy-select">
							<select id="soliloquy-config-transition" name="_soliloquy[transition]" class="soliloquy-chosen" data-soliloquy-chosen-options='{ "disable_search":"true", "width": "100%" }'>
								<?php foreach ( (array) $this->get_slider_transitions() as $i => $data ) : ?>
									<option value="<?php echo esc_attr( $data['value'] ); ?>"<?php selected( $data['value'], $this->get_config( 'transition', $this->get_config_default( 'transition' ) ) ); ?>><?php echo esc_html( $data['name'] ); ?></option>
								<?php endforeach; ?>
							</select>
							</div>
							<p class="description"><?php esc_html_e( 'Sets the type of transition for the slider.', 'soliloquy' ); ?></p>
						</td>
					</tr>
					<tr id="soliloquy-config-slider-duration-box">
						<th scope="row">
							<label for="soliloquy-config-duration"><?php esc_html_e( 'Slider Transition Duration', 'soliloquy' ); ?></label>
						</th>
						<td>
							<input id="soliloquy-config-duration" type="number" name="_soliloquy[duration]" value="<?php echo esc_attr( $this->get_config( 'duration', $this->get_config_default( 'duration' ) ) ); ?>" /> <span class="soliloquy-unit"><?php esc_html_e( 'ms', 'soliloquy' ); ?></span>
							<p class="description">
								<?php
									$text = __( 'Sets the amount of time between each slide transition <strong>(in milliseconds)</strong>.', 'soliloquy' );
									echo wp_kses(
										$text,
										[
											'strong' => [], // Allow only the <strong> tag.
										]
									);
								?>
							</p>
						</td>
					</tr>
					<tr id="soliloquy-config-slider-speed-box">
						<th scope="row">
							<label for="soliloquy-config-speed"><?php esc_html_e( 'Slider Transition Speed', 'soliloquy' ); ?></label>
						</th>
						<td>
							<input id="soliloquy-config-speed" type="number" name="_soliloquy[speed]" value="<?php echo esc_attr( $this->get_config( 'speed', $this->get_config_default( 'speed' ) ) ); ?>" /> <span class="soliloquy-unit"><?php esc_html_e( 'ms', 'soliloquy' ); ?></span>
							<p class="description">
								<?php
									$text = __( 'Sets the transition speed when moving from one slide to the next <strong>(in milliseconds)</strong>.', 'soliloquy' );
									echo wp_kses(
										$text,
										[
											'strong' => [], // Allow only the <strong> tag.
										]
									);
								?>
							</p>
						</td>
					</tr>
					<tr id="soliloquy-config-gutter-box">
						<th scope="row">
							<label for="soliloquy-config-gutter"><?php esc_html_e( 'Slider Gutter', 'soliloquy' ); ?></label>
						</th>
						<td>
							<input id="soliloquy-config-gutter" type="number" name="_soliloquy[gutter]" value="<?php echo esc_attr( $this->get_config( 'gutter', $this->get_config_default( 'gutter' ) ) ); ?>" /> <span class="soliloquy-unit"><?php esc_html_e( 'px', 'soliloquy' ); ?></span>
							<p class="description"><?php esc_html_e( 'Sets the gutter between the slider and your content based on slider position.', 'soliloquy' ); ?></p>
						</td>
					</tr>
					<tr id="soliloquy-config-slider-box">
						<th scope="row">
							<label for="soliloquy-config-slider"><?php esc_html_e( 'Crop Images in Slider?', 'soliloquy' ); ?></label>
						</th>
						<td>
						<label class="soliloquy-toggle">
							<input id="soliloquy-config-slider" type="checkbox" name="_soliloquy[slider]" value="<?php echo esc_attr( $this->get_config( 'slider', $this->get_config_default( 'slider' ) ) ); ?>" <?php checked( $this->get_config( 'slider', $this->get_config_default( 'slider' ) ), 1 ); ?> />
							<span class="soliloquy-switch"></span>
							<span class="description">
								<?php
									$text = __( 'Enables or disables image cropping based on slider dimensions <strong>(recommended)</strong>.', 'soliloquy' );
									echo wp_kses(
										$text,
										[
											'strong' => [], // Allow only the <strong> tag.
										]
									);
								?>
							</span>
						</label>
						</td>
					</tr>
					<tr id="soliloquy-config-aria-live-box">
						<th scope="row">
							<label for="soliloquy-config-aria-live"><?php esc_html_e( 'ARIA Live Value', 'soliloquy' ); ?></label>
						</th>
						<td>
							<div class="soliloquy-select">
							<select id="soliloquy-config-aria-live" name="_soliloquy[aria_live]" class="soliloquy-chosen" data-soliloquy-chosen-options='{ "disable_search":"true", "width": "100%" }'>
								<?php foreach ( (array) Soliloquy_Common_Lite::get_instance()->get_aria_live_values() as $i => $data ) : ?>
									<option value="<?php echo esc_attr( $data['value'] ); ?>"<?php selected( $data['value'], $this->get_config( 'aria_live', $this->get_config_default( 'aria_live' ) ) ); ?>><?php echo esc_html( $data['name'] ); ?></option>
								<?php endforeach; ?>
							</select>

							</div>
							<p class="description"><?php esc_html_e( 'Accessibility: Defines the priority with which screen readers should treat updates to this slider.', 'soliloquy' ); ?></p>
						</td>
					</tr>
					<?php do_action( 'soliloquy_config_box', $post ); ?>
				</tbody>
			</table>

			<div class="soliloquy-alert">

				<p class="soliloquy-intro"><?php esc_html_e( 'Want to do even more with your slider display?', 'soliloquy' ); ?></p>
				<p><?php esc_html_e( 'By upgrading to Soliloquy Pro, you can get access to numerous other gallery display features, including: custom image tagging and filtering, mobile specific image assets for blazing fast load times, dedicated and unique gallery URLs, custom gallery themes, gallery thumbnail support and so much more!', 'soliloquy' ); ?></p>
				<a href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy"><?php esc_attr_e( 'Click here to Upgrade', 'soliloquy' ); ?></a>

			</div>

		</div>

		<?php
	}

	/**
	 * Callback for displaying the UI for setting slider miscellaneous options.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object.
	 */
	public function misc_tab( $post ) {
		?>
		<div id="soliloquy-misc">
			<p class="soliloquy-intro"><?php esc_html_e( 'The settings below adjust the miscellaneous settings for the slider display.', 'soliloquy' ); ?></p>
			<table class="form-table">
				<tbody>
					<tr id="soliloquy-config-title-box">
						<th scope="row">
							<label for="soliloquy-config-title"><?php esc_html_e( 'Slider Title', 'soliloquy' ); ?></label>
						</th>
						<td>
							<input id="soliloquy-config-title" type="text" name="_soliloquy[title]" value="<?php echo esc_attr( $this->get_config( 'title', $this->get_config_default( 'title' ) ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Internal slider title for identification in the admin.', 'soliloquy' ); ?></p>
						</td>
					</tr>
					<tr id="soliloquy-config-slug-box">
						<th scope="row">
							<label for="soliloquy-config-slug"><?php esc_html_e( 'Slider Slug', 'soliloquy' ); ?></label>
						</th>
						<td>
							<input id="soliloquy-config-slug" type="text" name="_soliloquy[slug]" value="<?php echo esc_attr( $this->get_config( 'slug', $this->get_config_default( 'slug' ) ) ); ?>" />
							<p class="description">
								<?php
									$text = __( '<strong>Unique</strong> internal slider slug for identification and advanced slider queries.', 'soliloquy' );
									echo wp_kses(
										$text,
										[
											'strong' => [], // Allow only the <strong> tag.
										]
									);
								?>
							</p>
						</td>
					</tr>
					<tr id="soliloquy-config-classes-box">
						<th scope="row">
							<label for="soliloquy-config-classes"><?php esc_html_e( 'Custom Slider Classes', 'soliloquy' ); ?></label>
						</th>
						<td>
							<textarea id="soliloquy-config-classes" rows="5" cols="75" name="_soliloquy[classes]" placeholder="<?php esc_attr_e( 'Enter custom slider CSS classes here, one per line.', 'soliloquy' ); ?>"><?php echo esc_html( implode( "\n", (array) $this->get_config( 'classes', $this->get_config_default( 'classes' ) ) ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Adds custom CSS classes to this slider. Enter one class per line.', 'soliloquy' ); ?></p>
						</td>
					</tr>
					<tr id="soliloquy-config-rtl-box">
						<th scope="row">
							<label for="soliloquy-config-rtl"><?php esc_html_e( 'Enable RTL Support?', 'soliloquy' ); ?></label>
						</th>
						<td>
						<label class="soliloquy-toggle">

							<input id="soliloquy-config-rtl" type="checkbox" name="_soliloquy[rtl]" value="<?php echo esc_attr( $this->get_config( 'rtl', $this->get_config_default( 'rtl' ) ) ); ?>" <?php checked( $this->get_config( 'rtl', $this->get_config_default( 'rtl' ) ), 1 ); ?> />
							<span class="soliloquy-switch"></span>
							<span class="description"><?php esc_html_e( 'Enables or disables RTL support in Soliloquy for right-to-left languages.', 'soliloquy' ); ?></span>
						</label>
						</td>
					</tr>
					<?php do_action( 'soliloquy_misc_box', $post ); ?>
				</tbody>
			</table>

			<div class="soliloquy-alert">

				<p class="soliloquy-intro"><?php esc_html_e( 'Want to take your sliders further?', 'soliloquy' ); ?></p>
				<p><?php esc_html_e( 'By upgrading to Soliloquy Pro, you can get access to numerous other features, including: a fully-integrated import/export module for your slider, custom CSS controls for each slider and so much more!', 'soliloquy' ); ?></p>
				<a href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy"><?php esc_attr_e( 'Click here to Upgrade', 'soliloquy' ); ?></a>

			</div>

		</div>

		<?php
	}

	/**
	 * Callback for displaying the UI for setting slider Mobile Lite.
	 *
	 * @since 2.5.0
	 */
	public function mobile_lite_tab() {
		?>

		<div id="soliloquy-mobile-lite">

			<div class="soliloquy-alert">

				<p class="soliloquy-intro"><?php esc_attr_e( 'Want to take your sliders further?', 'soliloquy' ); ?></p>
				<p><?php esc_attr_e( 'By upgrading to Soliloquy Pro, you can get access to mobile-specific settings, including mobile image sizes, number of columns, mobile-specific lightbox options and so much more!', 'soliloquy' ); ?></p>

				<a href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy"><?php esc_attr_e( 'Click here to Upgrade', 'soliloquy' ); ?></a>

			</div>

		</div>
		<?php
	}
	/**
	 * Callback for displaying the UI for setting slider Mobile Lite.
	 *
	 * @since 2.5.0
	 */
	public function carousel_lite_tab() {
		?>

		<div id="soliloquy-carousel-lite">

			<div class="soliloquy-alert">

				<p class="soliloquy-intro">
				<?php
				esc_attr_e(
					'Want to create a Responsive Carousel Slider?
',
					'soliloquy'
				);
				?>
											</p>
				<p><?php esc_attr_e( 'By upgrading to Soliloquy Pro, you can create a responsive carousel slider in WordPress for your images, photos, videos, and even galleries.', 'soliloquy' ); ?></p>

				<a href="<?php echo esc_html( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy"><?php esc_attr_e( 'Click here to Upgrade', 'soliloquy' ); ?></a>

			</div>

		</div>

		<?php
	}
	/**
	 * Callback for displaying the UI for setting slider Mobile Lite.
	 *
	 * @since 2.5.0
	 */
	public function pinterest_lite_tab() {
		?>

		<div id="soliloquy-pinterest-lite">

			<div class="soliloquy-alert">

				<p class="soliloquy-intro"><?php esc_attr_e( 'Want to take your sliders further?', 'soliloquy' ); ?></p>
				<p><?php esc_attr_e( 'By upgrading to Soliloquy Pro, you can add pinterest sharing buttons to your slider images and Lightbox images. Why not check it out?', 'soliloquy' ); ?></p>
				<a href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy"><?php esc_attr_e( 'Click here to Upgrade', 'soliloquy' ); ?></a>

			</div>

		</div>
		<?php
	}
	/**
	 * Callback for displaying the UI for setting slider Mobile Lite.
	 *
	 * @since 2.5.0
	 */
	public function schedule_lite_tab() {
		?>

		<div id="soliloquy-schedule-lite">
			<div class="soliloquy-alert">
				<p class="soliloquy-intro"><?php esc_attr_e( 'Want to take your sliders further?', 'soliloquy' ); ?></p>
				<p><?php esc_attr_e( 'By upgrading to Soliloquy Pro, you can easily schedule both sliders and individual slides to be displayed at specific time intervals (perfect for highlight time-sensitive content).', 'soliloquy' ); ?></p>
				<a href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy"><?php esc_attr_e( 'Click here to Upgrade', 'soliloquy' ); ?></a>
			</div>

		</div>

		<?php
	}

	/**
	 * Callback for displaying the UI for setting slider Mobile Lite.
	 *
	 * @since 2.5.0
	 */
	public function lightbox_lite_tab() {
		?>

		<div id="soliloquy-lightbox-lite">

			<div class="soliloquy-alert">
				<p class="soliloquy-intro"><?php esc_attr_e( 'Want even more fine tuned control over your lightbox display?', 'soliloquy' ); ?></p>
				<p><?php esc_attr_e( 'By upgrading to Soliloquy Pro, you can get access to numerous other lightbox features, including: custom lightbox titles, enable/disable lightbox controls (arrow, keyboard and mousehweel navigation), custom lightbox transition effects, native fullscreen support, gallery deeplinking, image protection, lightbox supersize effects, lightbox slideshows and so much more!', 'soliloquy' ); ?></p>
				<a href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy"><?php esc_attr_e( 'Click here to Upgrade', 'soliloquy' ); ?></a>

			</div>

		</div>
		<?php
	}

	/**
	 * Callback for displaying the UI for setting slider Mobile Lite.
	 *
	 * @since 2.5.0
	 */
	public function thumbnails_lite_tab() {
		?>

		<div id="soliloquy-thumbnails-lite">

			<div class="soliloquy-alert">

				<p class="soliloquy-intro"><?php esc_attr_e( 'Want to add Thumbnail Navigation?', 'soliloquy' ); ?></p>
				<p><?php esc_html_e( 'By upgrading to Soliloquy Pro, you can add thumbnail images as navigation for your WordPress slider. ', 'soliloquy' ); ?>
					<a target="_blank" href="<?php echo esc_url( 'http://soliloquywp.com/addons/thumbnails/' ); ?>"><?php esc_attr_e( '(See Demo)', 'soliloquy' ); ?></a>
				</p>
				<a href="<?php echo esc_url( $this->common->get_upgrade_link() ); ?>" target="_blank" class="button button-soliloquy"><?php esc_attr_e( 'Click here to Upgrade', 'soliloquy' ); ?></a>

			</div>

		</div>

		<?php
	}
	/**
	 * Callback for saving values from Soliloquy metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id The current post ID.
	 * @param object $post The current post object.
	 */
	public function save_meta_boxes( $post_id, $post ) {

		// Bail out if we fail a security check.
		// Bail out if we fail a security check.
		if ( ! isset( $_POST['soliloquy'] ) || ! wp_verify_nonce( sanitize_key( $_POST['soliloquy'] ), 'soliloquy' ) || ! isset( $_POST['_soliloquy'] ) ) {
			return;
		}

		// Bail out if running an autosave, ajax, cron or revision.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Bail out if the user doesn't have the correct permissions to update the slider.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Sanitize the input data.
		$soliloquy = array_map( 'sanitize_text_field', wp_unslash( $_POST['_soliloquy'] ) );

		// If the post has just been published for the first time, set meta field for the slider meta overlay helper.
		if ( isset( $post->post_date ) && isset( $post->post_modified ) && $post->post_date === $post->post_modified ) {
			update_post_meta( $post_id, '_sol_just_published', true );
		}

		// Sanitize all user inputs.
		$settings = get_post_meta( $post_id, '_sol_slider_data', true );
		if ( empty( $settings ) ) {
			$settings = [];
		}

		// Force slider ID to match Post ID. This is deliberate; if a slider is duplicated (either using a duplication)
		// plugin or WPML, the ID remains as the original slider ID, which breaks things for translations etc.
		$settings['id'] = $post_id;

		// Save the config settings.
		$settings['config']['type']          = isset( $soliloquy['type'] ) ? $soliloquy['type'] : $this->get_config_default( 'type' );
		$settings['config']['slider_theme']  = preg_replace( '#[^a-z0-9-_]#', '', $soliloquy['slider_theme'] );
		$settings['config']['slider_width']  = absint( $soliloquy['slider_width'] );
		$settings['config']['slider_height'] = absint( $soliloquy['slider_height'] );
		$settings['config']['transition']    = preg_replace( '#[^a-z0-9-_]#', '', $soliloquy['transition'] );
		$settings['config']['duration']      = absint( $soliloquy['duration'] );
		$settings['config']['speed']         = absint( $soliloquy['speed'] );
		$settings['config']['gutter']        = absint( $soliloquy['gutter'] );
		$settings['config']['slider']        = isset( $soliloquy['slider'] ) ? 1 : 0;
		$settings['config']['aria_live']     = preg_replace( '#[^a-z0-9-_]#', '', $soliloquy['aria_live'] );
		$settings['config']['classes']       = explode( "\n", wp_unslash( $soliloquy['classes'] ) );
		$settings['config']['title']         = sanitize_text_field( wp_unslash( $soliloquy['title'] ) );
		$settings['config']['slug']          = sanitize_text_field( wp_unslash( $soliloquy['slug'] ) );
		$settings['config']['rtl']           = ( isset( $soliloquy['rtl'] ) ? 1 : 0 );

		// If on an soliloquy post type, map the title and slug of the post object to the custom fields if no value exists yet.
		if ( isset( $post->post_type ) && 'soliloquy' === $post->post_type ) {
			if ( empty( $settings['config']['title'] ) ) {
				$settings['config']['title'] = trim( wp_strip_all_tags( $post->post_title ) );
			}

			if ( empty( $settings['config']['slug'] ) ) {
				$settings['config']['slug'] = sanitize_text_field( $post->post_name );
			}
		}

		// Provide a filter to override settings.
		$settings = apply_filters( 'soliloquy_save_settings', $settings, $post_id, $post );

		// Update the post meta.
		update_post_meta( $post_id, '_sol_slider_data', $settings );

		// Change states of images in slider from pending to active.
		$this->change_slider_states( $post_id );

		// If the crop option is checked, crop images accordingly.
		if ( isset( $settings['config']['slider'] ) && $settings['config']['slider'] ) {
			$args = apply_filters(
				'soliloquy_crop_image_args',
				[
					'position' => 'c',
					'width'    => $this->get_config( 'slider_width', $this->get_config_default( 'slider_width' ) ),
					'height'   => $this->get_config( 'slider_height', $this->get_config_default( 'slider_height' ) ),
					'quality'  => 100,
					'retina'   => false,
				]
			);
			$this->crop_images( $args, $post_id );
		}

		// Fire a hook for addons that need to utilize the cropping feature.
		do_action( 'soliloquy_saved_settings', $settings, $post_id, $post );

		// Finally, flush all slider caches to ensure everything is up to date.
		$this->flush_slider_caches( $post_id, $settings['config']['slug'] );
	}

	/**
	 * Helper method for retrieving the slider layout for an item in the admin.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $id The  ID of the item to retrieve.
	 * @param array  $data  Array of data for the item.
	 * @param string $type The type of slide to retrieve.
	 * @param int    $post_id The current post ID.
	 * @return string The  HTML output for the slider item.
	 */
	public function get_slider_item( $id, $data, $type, $post_id = 0 ) {

		switch ( $type ) {
			case 'image':
				$item = $this->get_slider_image( $id, $data, $post_id );
				break;
			case 'video':
				$item = '';
				break;
			case 'html':
				$item = '';
				break;
		}

		return apply_filters( 'soliloquy_slide_item', $item, $id, $data, $type, $post_id );
	}

	/**
	 * Helper method for retrieving the slider image layout in the admin.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $id The  ID of the item to retrieve.
	 * @param array $data  Array of data for the item.
	 * @param int   $post_id The current post ID.
	 * @return string The  HTML output for the slider item.
	 */
	public function get_slider_image( $id, $data, $post_id = 0 ) {

			$thumbnail = wp_get_attachment_image_src( $id, 'thumbnail' );

			$json = version_compare( PHP_VERSION, '5.3.0' ) >= 0 ? wp_json_encode( $data, JSON_HEX_APOS ) : wp_json_encode( $data );

			ob_start();
		?>

			<li id="<?php echo esc_attr( $id ); ?>" class="soliloquy-slide soliloquy-image soliloquy-status-<?php echo esc_attr( $data['status'] ); ?>" data-soliloquy-slide="<?php echo esc_attr( $id ); ?>" data-soliloquy-image-model='<?php echo esc_html( htmlspecialchars( $json, ENT_QUOTES, 'UTF-8' ) ); ?>'>
				<a href="#" class="check"><div class="media-modal-icon"></div></a>

				<a href="#" class="soliloquy-remove-slide" title="<?php esc_attr_e( 'Remove Image Slide from Slider?', 'soliloquy' ); ?>"><i class="soliloquy-icon-close"></i></a>

				<a href="#" class="soliloquy-modify-slide" title="<?php esc_attr_e( 'Modify Image Slide', 'soliloquy' ); ?>"><i class="soliloquy-icon-pencil"></i></a>

				<div class="soliloquy-item-content">
				<img class="soliloquy-item-img" src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="<?php esc_attr( $data['alt'] ); ?>" />

				<div class="soliloquy-item-info">

				<h3 class="soliloquy-item-title"><?php echo esc_html( get_the_title( $id ) ); ?></h3>

				</div>
				</div>

			</li>

			<?php
			return ob_get_clean();
	}

	/**
	 * Helper method to change a slider state from pending to active. This is done
	 * automatically on post save. For previewing sliders before publishing,
	 * simply click the "Preview" button and Soliloquy will load all the images present
	 * in the slider at that time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The current post ID.
	 */
	public function change_slider_states( $post_id ) {

		$slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
		if ( ! empty( $slider_data['slider'] ) ) {
			foreach ( (array) $slider_data['slider'] as $id => $item ) {
				$slider_data['slider'][ $id ]['status'] = 'active';
			}
		}

		update_post_meta( $post_id, '_sol_slider_data', $slider_data );
	}

	/**
	 * Helper method to crop slider images to the specified sizes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args  Array of args used when cropping the images.
	 * @param int   $post_id The current post ID.
	 */
	public function crop_images( $args, $post_id ) {

		// Gather all available images to crop.
		$slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
		$images      = ! empty( $slider_data['slider'] ) ? $slider_data['slider'] : false;
		$common      = Soliloquy_Common_Lite::get_instance();

		// Loop through the images and crop them.
		if ( $images ) {
			// Increase the time limit to account for large image sets and suspend cache invalidations.
			set_time_limit( Soliloquy_Common_Lite::get_instance()->get_max_execution_time() );
			wp_suspend_cache_invalidation( true );

			foreach ( $images as $id => $item ) {
				// Get the full image attachment. If it does not return the data we need, skip over it.
				$image = wp_get_attachment_image_src( $id, 'full' );
				if ( ! is_array( $image ) ) {
					// Check for video/HTML slide and possibly use a thumbnail instead.
					if ( ( ( isset( $item['type'] ) && 'video' === $item['type'] )
					|| ( isset( $item['type'] ) && 'html' === $item['type'] ) )
					&& ! empty( $item['thumb'] ) ) {
						$image = $item['thumb'];
					} else {
						continue;
					}
				} else {
					$image = $image[0];
				}

				// Allow image to be filtered to use a different thumbnail than the main image.
				$image = apply_filters( 'soliloquy_cropped_image', $image, $id, $item, $args, $post_id );

				// Generate the cropped image.
				$cropped_image = $common->resize_image( $image, $args['width'], $args['height'], true, $args['position'], $args['quality'], $args['retina'] );

			}

			// Turn off cache suspension and flush the cache to remove any cache inconsistencies.
			wp_suspend_cache_invalidation( false );
			wp_cache_flush();
		}
	}

	/**
	 * Helper method to flush slider caches once a slider is updated.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id The current post ID.
	 * @param string $slug The unique slider slug.
	 */
	public function flush_slider_caches( $post_id, $slug ) {

		Soliloquy_Common_Lite::get_instance()->flush_slider_caches( $post_id, $slug );
	}

	/**
	 * Helper method for retrieving config values.
	 *
	 * @since 1.0.0
	 *
	 * @global int $id        The current post ID.
	 * @global object $post   The current post object.
	 * @param string $key     The config key to retrieve.
	 * @param string $default A default value to use.
	 * @return string         Key value on success, empty string on failure.
	 */
	public function get_config( $key, $default = false ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound

		global $id, $post;

		// Get the current post ID. If ajax, grab it from the $_POST variable.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$post_id = isset( $_POST['post_id'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) ) : (int) $id; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		} else {
			$post_id = isset( $post->ID ) ? $post->ID : (int) $id;
		}

		$settings = get_post_meta( $post_id, '_sol_slider_data', true );
		if ( isset( $settings['config'][ $key ] ) ) {
			return $settings['config'][ $key ];
		} else {
			return $default ? $default : '';
		}
	}

	/**
	 * Helper method for setting default config values.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The default config key to retrieve.
	 * @return string Key value on success, false on failure.
	 */
	public function get_config_default( $key ) {

		$instance = Soliloquy_Common_Lite::get_instance();
		return $instance->get_config_default( $key );
	}

	/**
	 * Helper method for retrieving slider themes.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of slider theme data.
	 */
	public function get_slider_themes() {

		$instance = Soliloquy_Common_Lite::get_instance();
		return $instance->get_slider_themes();
	}

	/**
	 * Helper method for retrieving slider transitions.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of thumbnail transition data.
	 */
	public function get_slider_transitions() {

		$instance = Soliloquy_Common_Lite::get_instance();
		return $instance->get_slider_transitions();
	}

	/**
	 * Returns the post types to skip for loading Soliloquy metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of skipped posttypes.
	 */
	public function get_skipped_posttypes() {

		$post_types = get_post_types( [ 'public' => true ] );
		unset( $post_types['soliloquy'] );
		return apply_filters( 'soliloquy_skipped_posttypes', $post_types );
	}

	/**
	 * Flag to determine if the GD library has been compiled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if has proper extension, false otherwise.
	 */
	public function has_gd_extension() {

		return extension_loaded( 'gd' ) && function_exists( 'gd_info' );
	}

	/**
	 * Flag to determine if the Imagick library has been compiled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if has proper extension, false otherwise.
	 */
	public function has_imagick_extension() {

		return extension_loaded( 'imagick' );
	}
	/**
	 * Run through the array and check if ID or attachment_id is set.
	 *
	 * @access public
	 * @param mixed $slides Array of slides to check.
	 * @return boolean
	 */
	public function maybe_update_slides( $slides ) {

		foreach ( $slides as $id => $data ) {

			if ( ! array_key_exists( 'id', $data ) || ! array_key_exists( 'attachment_id', $data ) ) {

				return false;

			} else {

				continue;

			}
		}

		return true;
	}

	/**
	 * Update Soliloquy Lite slides to include ID and Attachemnt ID.
	 *
	 * @access public
	 * @param mixed $post_id Post ID to update slides for.
	 * @return array
	 */
	public function update_slides( $post_id ) {

		// Grab and update any slider data if necessary.
		$in_slider = get_post_meta( $post_id, '_sol_in_slider', true );
		if ( empty( $in_slider ) ) {
			$in_slider = [];
		}

		// Set data and order of image in slider.
		$slider_data = get_post_meta( $post_id, '_sol_slider_data', true );

		if ( empty( $slider_data ) ) {
			$slider_data = [];
		}

		// If no slider ID has been set, set it now.
		if ( empty( $slider_data['id'] ) ) {
			$slider_data['id'] = $post_id;
		}

		foreach ( $slider_data['slider'] as $id => $data ) {

			if ( ! array_key_exists( 'id', $data ) || ! array_key_exists( 'attachment_id', $data ) ) {

				$slide = [
					'status'        => isset( $data['status'] ) ? $data['status'] : 'published',
					'id'            => $id,
					'attachment_id' => $id,
				];

				$slide = wp_parse_args( $slide, $data );

			} else {

				$slide = [
					'status' => isset( $data['status'] ) ? $data['status'] : 'published',
					'id'     => isset( $data['id'] ) ? $data['id'] : $id,
				];

				$slide = wp_parse_args( $slide, $data );

			}

				$slider_data['slider'][ $id ] = $slide;
				$in_slider[]                  = $id;

		}

		// Update the slider data.
		update_post_meta( $post_id, '_sol_slider_data', $slider_data );

		Soliloquy_Common_Lite::get_instance()->flush_slider_caches( $post_id );

		return $slider_data;
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Soliloquy_Metaboxes_Lite object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Metaboxes_Lite ) ) {
			self::$instance = new Soliloquy_Metaboxes_Lite();
		}

		return self::$instance;
	}
}

// Load the metabox class.
$soliloquy_metaboxes_lite = Soliloquy_Metaboxes_Lite::get_instance();
