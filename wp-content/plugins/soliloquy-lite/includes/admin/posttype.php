<?php
/**
 * Admin Posttype Class.
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
 * Soliloquy Post Type
 *
 * @since 2.5.0
 */
class Soliloquy_Lite_Posttype_Admin {

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
	 * Holds the base class object.
	 *
	 * @since 2.5.0
	 *
	 * @var object
	 */
	public $metabox;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load the base class object.
		$this->base = Soliloquy_Lite::get_instance();

		// Load the metabox class object.
		$this->metabox = Soliloquy_Metaboxes_Lite::get_instance();

		// Update post type messages.
		add_filter( 'post_updated_messages', [ $this, 'messages' ] );

		// Force the menu icon to be scaled to proper size (for Retina displays).
		add_action( 'admin_head', [ $this, 'menu_icon' ] );

		// Check if any soliloquyv2 post types still exist, and if so migrate them once.
		add_action( 'init', [ $this, 'maybe_fix_soliloquyv2_cpts' ] );

		add_action( 'in_admin_header', [ $this, 'admin_header_html' ], 100 );

		// Load CSS and JS.
		add_action( 'admin_enqueue_scripts', [ $this, 'styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );

		// Append data to various admin columns.
		add_filter( 'manage_edit-soliloquy_columns', [ &$this, 'soliloquy_columns' ] );
		add_action( 'manage_soliloquy_posts_custom_column', [ &$this, 'soliloquy_custom_columns' ], 10, 2 );

		// Quick and Bulk Editing support.
		add_action( 'quick_edit_custom_box', [ $this, 'quick_edit_custom_box' ], 10, 2 ); // Single Item.
		add_action( 'bulk_edit_custom_box', [ $this, 'bulk_edit_custom_box' ], 10, 2 ); // Multiple Items.
		add_action( 'post_updated', [ $this, 'bulk_edit_save' ] );

		add_action( 'admin_init', [ $this, 'check_user_capability_on_edit' ] );
	}

	/**
	 * Loads Admin Header markup.
	 *
	 * @access public
	 * @return void
	 * @since 2.5.0
	 */
	public function admin_header_html() {

		$screen = get_current_screen();

		if ( 'soliloquy' === $screen->post_type && apply_filters( 'soliloquy_whitelabel', true ) ) {

			$this->base->load_admin_partial( '_header.php' );

		}
	}

	/**
	 * Loads scripts for all soliloquy-based Administration Screens.
	 *
	 * @since 2.5.0
	 *
	 * @return null Return early if not on the proper screen.
	 */
	public function scripts() {

		// Get current screen.
		$screen = get_current_screen();

		// Bail if we're not on the soliloquy Post Type screen.
		if ( 'soliloquy' !== $screen->post_type ) {
			return;
		}

		// Bail if we're not on a WP_List_Table.
		if ( 'edit' !== $screen->base ) {
			return;
		}

		// Chosen JS.
		wp_register_script( $this->base->plugin_slug . '-chosen', plugins_url( 'assets/js/min/chosen.jquery-min.js', $this->base->file ), [ 'jquery' ], $this->base->version, true );
		wp_enqueue_script( $this->base->plugin_slug . '-chosen' );

		// Load necessary admin scripts.
		wp_register_script( $this->base->plugin_slug . '-clipboard-script', plugins_url( 'assets/js/min/clipboard-min.js', $this->base->file ), [ 'jquery' ], $this->base->version, true );
		wp_enqueue_script( $this->base->plugin_slug . '-clipboard-script' );

		wp_register_script( $this->base->plugin_slug . '-overview-script', plugins_url( 'assets/js/min/overview-min.js', $this->base->file ), [ 'jquery' ], $this->base->version, true );
		wp_enqueue_script( $this->base->plugin_slug . '-overview-script' );

		// Fire a hook to load in custom admin scripts.
		do_action( 'soliloquy_admin_scripts' );
	}

	/**
	 * Loads styles for all soliloquy-based WP_List_Table Screens.
	 *
	 * @since 2.5.0
	 *
	 * @return null Return early if not on the proper screen.
	 */
	public function styles() {

		// Get current screen.
		$screen = get_current_screen();

		// Bail if we're not on the soliloquy Post Type screen.
		if ( 'soliloquy' !== $screen->post_type ) {
			return;
		}

		// Bail if we're not on a WP_List_Table.
		if ( 'edit' !== $screen->base ) {
			return;
		}

		wp_register_style( $this->base->plugin_slug . '-overview-style', plugins_url( 'assets/css/overview.css', $this->base->file ), [], $this->base->version );
		wp_enqueue_style( $this->base->plugin_slug . '-overview-style' );

		// Fire a hook to load in custom admin styles.
		do_action( 'soliloquy_table_styles' );
	}

	/**
	 * Customize the post columns for the soliloquy post type.
	 *
	 * @since 2.5
	 *
	 * @param array $columns  The default columns.
	 * @return array $columns Amended columns.
	 */
	public function soliloquy_columns( $columns ) {

		// Remove the default Checkbox and Title columns
		// We remove the Title column, as WordPress doesn't provide actions or filters that we can use
		// to append information to the Gallery Title e.g. number of images.
		unset( $columns['cb'], $columns['title'] );

		// Add additional columns we want to display.
		$soliloquy_columns = [
			'cb'        => '<input type="checkbox" />',
			'image'     => '',
			'title'     => __( 'Title', 'soliloquy' ),
			'shortcode' => __( 'Shortcode', 'soliloquy' ),
			'modified'  => __( 'Last Modified', 'soliloquy' ),
			'date'      => __( 'Date', 'soliloquy' ),
		];

		// Allow filtering of columns.
		$soliloquy_columns = apply_filters( 'soliloquy_table_columns', $soliloquy_columns, $columns );

		// Return merged column set.  This allows plugins to output their columns (e.g. Yoast SEO),
		// and column management plugins, such as Admin Columns, should play nicely.
		return array_merge( $soliloquy_columns, $columns );
	}
	/**
	 * Add data to the custom columns added to the soliloquy post type.
	 *
	 * @since 1.0.0
	 *
	 * @global object $post  The current post object.
	 * @param string $column The name of the custom column.
	 * @param int    $post_id   The current post ID.
	 */
	public function soliloquy_custom_columns( $column, $post_id ) {

		global $post;
		$post_id = absint( $post_id );

		switch ( $column ) {

			/**
			* Image
			*/
			case 'image':
				// Get Gallery Images.
				$slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
				if ( ! empty( $slider_data['slider'] ) && is_array( $slider_data['slider'] ) ) {
					$image = reset( $slider_data['slider'] );

					switch ( $image['type'] ) {
						case 'image':
							echo '<img src="' . esc_url( $image['src'] ) . '" width="75" /><br />';
							break;
						case 'video':
							echo '<img src="' . esc_url( plugins_url( 'assets/images/video.png', $this->base->file ) ) . '" width="75" /><br />';
							break;
						case 'html':
							echo '<img src="' . esc_url( plugins_url( 'assets/images/html.png', $this->base->file ) ) . '" width="75" /><br />';
							break;
					}
					/* translators: %s: image count*/
					printf( esc_html( _n( '%d Image', '%d Slides', count( $slider_data['slider'] ), 'soliloquy' ) ), esc_html( count( $slider_data['slider'] ) ) );             }
				break;

			/**
			* Shortcode
			*/
			case 'shortcode':
				echo '<code id="soliloquy-shortcode-' . sanitize_html_class( $post_id ) . '">[soliloquy id="' . sanitize_html_class( $post_id ) . '"]</code>';
				echo '<a class="soliloquy-clipboard" href="#" data-clipboard-target="#soliloquy-shortcode-' . sanitize_html_class( $post_id ) . '"">' . esc_html__( 'Copy to clipboard', 'soliloquy' ) . '</a>';

				// Hidden fields are for Quick Edit
				// class is used by assets/js/admin.js to remove these fields when a search is about to be submitted, so we dont' get long URLs.
				echo '
                <input class="soliloquy-quick-edit" type="hidden" name="_soliloquy_' . sanitize_html_class( $post_id ) . '[slider_theme]" value="' . esc_attr( $this->metabox->get_config( 'slider_theme' ) ) . '" />
                <input class="soliloquy-quick-edit" type="hidden" name="_soliloquy_' . sanitize_html_class( $post_id ) . '[transition]" value="' . esc_attr( $this->metabox->get_config( 'transition' ) ) . '" />';

				break;

			/**
			* Template
			*/
			case 'template':
				echo '<code id="soliloquy-code-' . sanitize_html_class( $post_id ) . '">if ( function_exists( \'Soliloquy\' ) ) { Soliloquy( \'' . intval( $post_id ) . '\' ); }</code>';
				echo '<a class="soliloquy-clipboard" href="#" data-clipboard-target="#soliloquy-code-' . sanitize_html_class( $post_id ) . '"">' . esc_html__( 'Copy to clipboard', 'soliloquy' ) . '</a>';
				break;

			/**
			* Posts
			*/
			case 'slides':
				$slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
				if ( ! empty( $slider_data['slider'] ) && is_array( $slider_data['slider'] ) ) {
					/* translators: %s: image count*/
					printf( esc_html( _n( '%d Slide', '%d Slides', intval( count( $slider_data['slider'] ) ), 'soliloquy' ) ), intval( count( $slider_data['slider'] ) ) );
				}
				$posts = get_post_meta( $post_id, '_eg_in_posts', true );
				if ( is_array( $posts ) ) {
					foreach ( $posts as $in_post_id ) {
						echo '<a href="' . esc_url( get_permalink( $in_post_id ) ) . '" target="_blank">' . esc_html( get_the_title( $in_post_id ) ) . '</a><br />';
					}
				}
				break;

			/**
			* Last Modified
			*/
			case 'modified':
				the_modified_date();
				break;
		}
	}
	/**
	 * Adds soliloquy fields to the quick editing and bulk editing screens
	 *
	 * @since 1.3.1
	 *
	 * @param string $column_name Column Name.
	 * @param string $post_type Post Type.
	 * @return HTML
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {

		// Check post type is soliloquy.
		if ( 'soliloquy' !== $post_type ) {
			return;
		}

		// Get metabox instance.
		$this->metabox = Soliloquy_Metaboxes_Lite::get_instance();

		wp_nonce_field( 'soliloquy', 'soliloquy' );

		switch ( $column_name ) {
			case 'image':
				break;
			case 'modified':
				?>

				<?php
				break;
			case 'shortcode':
				?>

				<fieldset class="inline-edit-col-left inline-edit-soliloquy">

					<div class="inline-edit-col inline-edit-<?php echo sanitize_html_class( $column_name ); ?>">

						<label class="inline-edit-group">

							<span class="title"><?php esc_html_e( 'Slider Theme', 'soliloquy' ); ?></span>

							<select name="_soliloquy[slider_theme]">

									<?php foreach ( (array) $this->metabox->get_slider_themes() as $i => $data ) : ?>

										<option value="<?php echo esc_attr( $data['value'] ); ?>"><?php echo esc_html( $data['name'] ); ?></option>

									<?php endforeach; ?>

							</select>

						</label>

					</div>

				</fieldset>

				<fieldset class="inline-edit-col-left inline-edit-soliloquy">

					<div class="inline-edit-col inline-edit-<?php echo sanitize_html_class( $column_name ); ?>">

						<label class="inline-edit-group">

							<span class="title"><?php esc_html_e( 'Slider Transition', 'soliloquy' ); ?></span>

							<select name="_soliloquy[transition]">

								<?php foreach ( (array) $this->metabox->get_slider_transitions() as $i => $data ) : ?>

									<option value="<?php echo esc_attr( $data['value'] ); ?>"><?php echo esc_html( $data['name'] ); ?></option>

									<?php endforeach; ?>

							</select>

						</label>

					</div>

				</fieldset>

				<?php
				break;

		}
	}

	/**
	 * Adds soliloquy fields to the  bulk editing screens
	 *
	 * @since 1.3.1
	 *
	 * @param string $column_name Column Name.
	 * @param string $post_type   Post Type.
	 * @return HTML
	 */
	public function bulk_edit_custom_box( $column_name, $post_type ) {

		// Check post type is soliloquy.
		if ( 'soliloquy' !== $post_type ) {
			return;
		}

		// Only apply to shortcode column.
		if ( 'shortcode' !== $column_name ) {
			return;
		}

		// Get metabox instance.
		$this->metabox = Soliloquy_Metaboxes_Lite::get_instance();

		switch ( $column_name ) {
			case 'shortcode':
				wp_nonce_field( 'soliloquy', 'soliloquy' );
				?>

				<fieldset class="inline-edit-col-left inline-edit-soliloquy">

					<div class="inline-edit-col inline-edit-<?php echo sanitize_html_class( $column_name ); ?>">

						<label class="inline-edit-group">
							<span class="title"><?php esc_html_e( 'Slider Theme', 'soliloquy' ); ?></span>

							<select name="_soliloquy[slider_theme]">

								<?php foreach ( (array) $this->metabox->get_slider_themes() as $i => $data ) : ?>

									<option value="<?php echo esc_attr( $data['value'] ); ?>"><?php echo esc_html( $data['name'] ); ?></option>

								<?php endforeach; ?>

							</select>

						</label>

						<label class="inline-edit-group">

							<span class="title"><?php esc_html_e( 'Slider Transition', 'soliloquy' ); ?></span>
							<div class="soliloquy-select">
								<select id="soliloquy-config-transition" name="_soliloquy[transition]" class="soliloquy-chosen" data-soliloquy-chosen-options='{ "disable_search":"true", "width": "100%" }'>

								<?php foreach ( (array) $this->metabox->get_slider_transitions() as $i => $data ) : ?>
									<option value="<?php echo esc_attr( $data['value'] ); ?>"><?php echo esc_html( $data['name'] ); ?></option>
								<?php endforeach; ?>

							</select>
							</div>
						</label>

					</div>

				</fieldset>

				<?php
				break;
		}
	}

	/**
	 * Called every time a WordPress Post is updated
	 *
	 * Checks to see if the request came from submitting the Bulk Editor form,
	 * and if so applies the updates.  This is because there is no direct action
	 * or filter fired for bulk saving
	 *
	 * @since 1.3.1
	 *
	 * @param int $post_ID Post ID.
	 */
	public function bulk_edit_save( $post_ID ) {

		// Check we are performing a Bulk Edit.
		if ( ! isset( $_REQUEST['bulk_edit'] ) ) {
			return;
		}

		if ( ! isset( $_POST['soliloquy'] ) || ! wp_verify_nonce( sanitize_key( $_POST['soliloquy'] ), 'soliloquy' ) ) {
			return;
		}

		// Check Post IDs have been submitted.
		$post_ids = ( ! empty( $_REQUEST['post'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['post'] ) ) : [];
		if ( empty( $post_ids ) || ! is_array( $post_ids ) ) {
			return;
		}

		// Get metabox instance.
		$this->metabox = Soliloquy_Lite_Metaboxes::get_instance();

		// Iterate through post IDs, updating settings.
		foreach ( $post_ids as $post_id ) {
			// Get settings.
			$settings = get_post_meta( $post_id, '_sol_slider_data', true );
			if ( empty( $settings ) ) {
				continue;
			}

			if ( ! empty( $_REQUEST['_soliloquy']['slider_theme'] ) && -1 !== sanitize_text_field( wp_unslash( $_REQUEST['_soliloquy']['slider_theme'] ) ) ) {

				$settings['config']['slider_theme'] = preg_replace( '#[^a-z0-9-_]#', '', sanitize_text_field( wp_unslash( $_REQUEST['_soliloquy']['slider_theme'] ) ) );

			}

			if ( ! empty( $_REQUEST['_soliloquy']['transition'] ) && -1 !== sanitize_text_field( wp_unslash( $_REQUEST['_soliloquy']['transition'] ) ) ) {

				$settings['config']['transition'] = preg_replace( '#[^a-z0-9-_]#', '', sanitize_text_field( wp_unslash( $_REQUEST['_soliloquy']['transition'] ) ) );

			}

			// Provide a filter to override settings.
			$settings = apply_filters( 'soliloquy_bulk_edit_save_settings', $settings, $post_id );

			// Update the post meta.
			update_post_meta( $post_id, '_sol_slider_data', $settings );

			// Finally, flush all gallery caches to ensure everything is up to date.
			$this->metabox->flush_slider_caches( $post_id, $settings['config']['slug'] );

		}
	}

	/**
	 * Contextualizes the post updated messages.
	 *
	 * @since 1.0.0
	 *
	 * @global object $post    The current post object.
	 * @param array $messages  Array of default post updated messages.
	 * @return array $messages Amended array of post updated messages.
	 */
	public function messages( $messages ) {

		global $post;

		// Contextualize the messages.
		$messages_array = [
			0  => '',
			1  => __( 'Soliloquy slider updated.', 'soliloquy' ),
			2  => __( 'Soliloquy slider custom field updated.', 'soliloquy' ),
			3  => __( 'Soliloquy slider custom field deleted.', 'soliloquy' ),
			4  => __( 'Soliloquy slider updated.', 'soliloquy' ),
			// @codingStandardsIgnoreStart
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Soliloquy slider restored to revision from %s.', 'soliloquy' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			// @codingStandardsIgnoreEnd
			6  => __( 'Soliloquy slider published.', 'soliloquy' ),
			7  => __( 'Soliloquy slider saved.', 'soliloquy' ),
			8  => __( 'Soliloquy slider submitted.', 'soliloquy' ),
			/* translators: %s: schedule posting date*/
			9  => sprintf( __( 'Soliloquy slider scheduled for: <strong>%1$s</strong>.', 'soliloquy' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Soliloquy slider draft updated.', 'soliloquy' ),
		];

		// Allow addons/devs to filter.
		$messages['soliloquy'] = apply_filters( 'soliloquy_messages', $messages_array );

		return $messages;
	}

	/**
	 * Forces the Soliloquy menu icon width/height for Retina devices.
	 *
	 * @since 1.0.0
	 */
	public function menu_icon() {

		?>
		<style type="text/css">#menu-posts-soliloquy .wp-menu-image img { width: 16px; height: 16px; } #menu-posts-soliloquy ul li:last-child a{color: rgb(255,55, 0); }</style>
		<?php
	}

	/**
	 * Maybe fixes a v1 to v2 upgrade where the sliders end up with the soliloquyv2
	 * post type, when it should be the soliloquy CPT.
	 *
	 * Once run, sets an option in wp_options so we don't run this every time.
	 *
	 * @since 2.4.1
	 */
	public function maybe_fix_soliloquyv2_cpts() {

		global $fixed_sliders;

		// Check if this routine has already run.
		$soliloquy_upgrade_cpts = get_option( 'soliloquy_upgrade_cpts' );
		if ( $soliloquy_upgrade_cpts ) {
			return;
		}

		// Retrieve any soliloquyv2 sliders and convert the post type back to the proper CPT.
		$v2_sliders = get_posts(
			[
				'post_type'      => 'soliloquyv2',
				'posts_per_page' => -1,
			]
		);

		// If no soliloquyv2 CPT posts exist, bail.
		if ( count( $v2_sliders ) === 0 ) {
			update_option( 'soliloquy_upgrade_cpts', true );
			return;
		}

		// Loop through the sliders, grab the data, delete and backwards convert them back to 'soliloquy' post type.
		$fixed_sliders = 0;
		foreach ( (array) $v2_sliders as $slider ) {
			// Grab any slider meta and add the attachment ID to the data array.
			$slider_meta = get_post_meta( $slider->ID, '_sol_slider_data', true );
			if ( ! empty( $slider_meta['slider'] ) ) {
				foreach ( $slider_meta['slider'] as $id => $data ) {
					$slider_meta['slider'][ $id ]['id'] = $id;
				}
			}

			update_post_meta( $slider->ID, '_sol_slider_data', $slider_meta );

			$data = [
				'ID'        => $slider->ID,
				'post_type' => 'soliloquy',
			];
			wp_update_post( $data );

			// Increment count for notice.
			++$fixed_sliders;
		}

		// Make sure this doesn't run again.
		update_option( 'soliloquy_upgrade_cpts', true );

		// Output an admin notice so the user knows what happened.
		if ( $fixed_sliders > 0 ) {
			add_action( 'admin_notices', [ $this, 'fixed_soliloquyv2_cpts' ] );
		}
	}

	/**
	 * Outputs a WordPress style notification to tell the user how many sliders were
	 * fixed after running the soliloquyv2 --> soliloquy CPT migration automatically
	 *
	 * @since 2.4.1
	 */
	public function fixed_soliloquyv2_cpts() {
		global $fixed_sliders;

		?>
		<div class="updated">
			<p><strong><?php echo esc_html( $fixed_sliders ) . esc_html__( ' slider(s) fixed successfully. This is a one time operation, and you don\'t need to do anything else.', 'soliloquy' ); ?></strong></p>
		</div>
		<?php
	}

	/**
	 * Check if the current user has the capability to edit the slider.
	 *
	 * @return void
	 */
	public function check_user_capability_on_edit() {
		global $pagenow;
		// phpcs:disable WordPress.Security.NonceVerification -- Not needed here.
		// Check if we are on the post edit screen.
		if ( 'post.php' === $pagenow && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) {

			$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : false;
			// phpcs:enable WordPress.Security.NonceVerification
			if ( $post_id ) {
				$post = get_post( $post_id );

				if ( 'soliloquy' !== get_post_type( $post ) ) {
					return;
				}

				// Check if the current user can edit others' posts or if they own this post.
				if ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'edit_others_posts', $post_id ) ) {
					wp_die( 'You do not have permission to edit this slider.' );
				}
			}
		}
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Soliloquy_Posttype_Admin object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Lite_Posttype_Admin ) ) {
			self::$instance = new Soliloquy_Lite_Posttype_Admin();
		}

		return self::$instance;
	}
}

// Load the posttype admin class.
$soliloquy_posttype_admin = Soliloquy_Lite_Posttype_Admin::get_instance();
