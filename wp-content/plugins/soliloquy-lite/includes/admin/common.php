<?php
/**
 * Admin Common Class.
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
 * Soliloquy Admin Common
 *
 * @since 2.5.0
 */
class Soliloquy_Common_Admin_Lite {

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

		// Check for upgrading sliders.
		add_action( 'admin_notices', [ $this, 'legacy_upgrade' ] );
		add_action( 'admin_notices', [ $this, 'legacy_upgrade_success' ] );

		// Load admin assets.
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

		// Delete any slider association on attachment deletion. Also delete any extra cropped images.
		add_action( 'delete_attachment', [ $this, 'delete_slider_association' ] );
		add_action( 'delete_attachment', [ $this, 'delete_cropped_image' ] );

		// Ensure slider display is correct when trashing/untrashing sliders.
		add_action( 'wp_trash_post', [ $this, 'trash_slider' ] );
		add_action( 'untrash_post', [ $this, 'untrash_slider' ] );
		add_filter( 'admin_footer_text', [ $this, 'admin_footer' ], 1, 2 );
		add_action( 'in_admin_footer', [ $this, 'footer_template' ] );
		add_action( 'admin_footer', [ $this, 'notifications_template' ] );
		add_action( 'admin_menu', [ $this, 'add_upgrade_menu_item' ], 99 );
		add_action( 'admin_head', [ $this, 'admin_inline_styles' ] );
		add_action( 'admin_footer', [ $this, 'admin_sidebar_target' ] );
	}

	/**
	 * Loads styles for all soliloquy-based Administration Screens.
	 *
	 * @since 1.3.1
	 *
	 * @return void Return early if not on the proper screen.
	 */
	public function admin_styles() {

		// Get current screen.
		$screen = get_current_screen();

		// Bail if we're not on the soliloquy Post Type screen.
		if ( 'soliloquy' !== $screen->post_type ) {
			return;
		}

		// Load necessary admin styles.
		wp_register_style( $this->base->plugin_slug . '-admin-style', plugins_url( 'assets/css/admin.css', $this->base->file ), [], $this->base->version );
		wp_enqueue_style( $this->base->plugin_slug . '-admin-style' );

		// Fire a hook to load in custom admin styles.
		do_action( 'soliloquy_gallery_admin_styles' );
	}

	/**
	 * Loads scripts for all soliloquy-based Administration Screens.
	 *
	 * @since 1.3.5
	 *
	 * @return void Return early if not on the proper screen.
	 */
	public function admin_scripts() {

		// Get current screen.
		$screen = get_current_screen();

		// Bail if we're not on the soliloquy Post Type screen.
		if ( 'soliloquy' !== $screen->post_type ) {
			return;
		}

		// Load necessary admin scripts.
		wp_register_script( $this->base->plugin_slug . '-admin-script', plugins_url( 'assets/js/min/admin-min.js', $this->base->file ), [ 'jquery', 'clipboard' ], $this->base->version, false );
		wp_enqueue_script( $this->base->plugin_slug . '-admin-script' );
		wp_localize_script(
			$this->base->plugin_slug . '-admin-script',
			'soliloquy_admin',
			[
				'ajax'                       => admin_url( 'admin-ajax.php' ),
				'dismiss_notification_nonce' => wp_create_nonce( 'soliloquy_dismiss_notification' ),
				'dismiss_notice_nonce'       => wp_create_nonce( 'soliloquy-dismiss-notice' ),
				'dismiss_topbar_nonce'       => wp_create_nonce( 'soliloquy-dismiss-topbar' ),
				'connect_nonce'              => wp_create_nonce( 'soliloquy_connect' ),
				'oops'                       => esc_html__( 'Oops!', 'soliloquy' ),
				'ok'                         => esc_html__( 'OK', 'soliloquy' ),
				'almost_done'                => esc_html__( 'Almost Done', 'soliloquy' ),
				'server_error'               => esc_html__( 'Unfortunately there was a server connection error.', 'soliloquy' ),
				'plugin_activate_btn'        => esc_html__( 'Activate', 'soliloquy' ),

			]
		);

		// Fire a hook to load in custom admin scripts.
		do_action( 'soliloquy_admin_scripts' );
	}

	/**
	 * Sidebar Target Blank
	 *
	 * @since 2.7.4
	 *
	 * @return void
	 */
	public function admin_sidebar_target() {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('li.soliloquy-sidebar-upgrade-pro a').attr('target','_blank');
		});
		</script>
		<?php
	}

	/**
	 * Global Admin Inline Styles
	 *
	 * @since 2.7.4
	 *
	 * @return void
	 */
	public function admin_inline_styles() {
		echo '<style>
			.soliloquy-sidebar-upgrade-pro {
				background-color: #37993B;
			}
			.soliloquy-sidebar-upgrade-pro a {
				color: #fff !important;
			}
		</style>';
	}

	/**
	 * Add lite-specific upgrade to pro menu item.
	 *
	 * @return void
	 */
	public function add_upgrade_menu_item() {
		global $submenu;

		add_submenu_page(
			'edit.php?post_type=soliloquy',
			esc_html__( 'Upgrade to Pro', 'soliloquy' ),
			esc_html__( 'Upgrade to Pro', 'soliloquy' ),
			apply_filters( 'soliloquy_gallery_menu_cap', 'manage_options' ),
			esc_url( $this->get_upgrade_link( 'http://soliloquywp.com/lite/', 'adminsidebar', 'unlockprosidebar' ) )
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$upgrade_link_position = key(
			array_filter(
				$submenu['edit.php?post_type=soliloquy'],
				static function ( $item ) {
					return strpos( $item[2], 'http://soliloquywp.com/lite/' ) !== false;
				}
			)
		);
		$screen                = get_current_screen();
		// Let's make sure we have an ID and the link is set in the menu.
		if ( isset( $screen->id ) && isset( $submenu['edit.php?post_type=soliloquy'][ $upgrade_link_position ][2] ) ) {
			// Let's clean up the screen id a bit.
			$screen_id = str_replace(
				[
					'code-snippets_page_',
					'toplevel_page_',
				],
				'',
				$screen->id
			);

			$submenu['edit.php?post_type=soliloquy'][ $upgrade_link_position ][2] = str_replace( 'soliloquy-admin', $screen_id, $submenu['edit.php?post_type=soliloquy'][ $upgrade_link_position ][2] );
		}

		// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
		if ( isset( $submenu['edit.php?post_type=soliloquy'][ $upgrade_link_position ][4] ) ) {
			$submenu['edit.php?post_type=soliloquy'][ $upgrade_link_position ][4] .= ' soliloquy-sidebar-upgrade-pro';
		} else {
			$submenu['edit.php?post_type=soliloquy'][ $upgrade_link_position ][] = 'soliloquy-sidebar-upgrade-pro';
		}
		// phpcs:enable WordPress.WP.GlobalVariablesOverride.Prohibited
	}

	/**
	 * Load footer template
	 *
	 * @since 2.7.4
	 */
	public function footer_template() {
		global $current_screen;
		if ( ! empty( $current_screen->id ) && strpos( $current_screen->id, 'soliloquy' ) !== false ) {
			// If here, we're on an soliloquy Gallery, so output the footer.
			$this->base->load_admin_partial(
				'footer.php'
			);
		}
	}
	/**
	 * Helper Method to load footer template
	 *
	 * @since 2.7.4
	 */
	public function notifications_template() {
		global $current_screen;
		if ( ! empty( $current_screen->id ) && strpos( $current_screen->id, 'soliloquy' ) !== false ) {
			// If here, we're on an soliloquy Gallery, so output the footer.
			$this->base->load_admin_partial(
				'notifications.php'
			);
		}
	}
	/**
	 * When user is on a soliloquy related admin page, display footer text
	 * that graciously asks them to rate us.
	 *
	 * @since
	 * @param string $text Text String to filter.
	 * @return string
	 */
	public function admin_footer( $text ) {
		global $current_screen;
		if ( ! empty( $current_screen->id ) && strpos( $current_screen->id, 'soliloquy' ) !== false ) {
			$url = 'https://wordpress.org/support/plugin/soliloquy-lite/reviews/?filter=5#new-post';
			/* translators: %s: url*/
			$text = sprintf( __( 'Please rate <strong>Soliloquy</strong> <a href="%1$s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%2$s" target="_blank">WordPress.org</a> to help us spread the word. Thank you from the Soliloquy team!', 'soliloquy' ), $url, $url );
		}
		return $text;
	}

	/**
	 * Performs a legacy upgrade for sliders from v1 to v2.
	 *
	 * @since 1.0.0
	 */
	public function legacy_upgrade() {

		// If the option exists for upgrading, do nothing.
		$upgrade = get_option( 'soliloquy_upgrade' );
		if ( $upgrade ) {
			return;
		}

		// If the option exists for already checking for sliders from previous versions, bail.
		$has_sliders = get_option( 'soliloquy_lite_upgrade' );
		if ( $has_sliders ) {
			return;
		}

		// If we have no sliders, only run this check once. Set option to prevent again.
		$sliders = get_posts(
			[
				'post_type'      => 'soliloquy',
				'posts_per_page' => -1,
			]
		);
		if ( ! $sliders ) {
			update_option( 'soliloquy_lite_upgrade', true );
			return;
		}

		?>
<div class="error">
		<?php /* translators: %s: url */ ?>
	<p><?php printf( esc_html__( 'Soliloquy Lite is now rocking v2! <strong>You need to upgrade your legacy v1 sliders to v2.</strong> <a href="%s">Click here to begin the upgrade process.</a>', 'soliloquy' ), esc_url( add_query_arg( 'page', 'soliloquy-lite-settings', admin_url( 'edit.php?post_type=soliloquy' ) ) ) ); ?>
	</p>
</div>
		<?php
	}

	/**
	 * Outputs the legacy upgrade notice message for folks who have just upgraded.
	 *
	 * @since 1.0.0
	 */
	public function legacy_upgrade_success() {

		// If the parameter is not set, do nothing.
		if ( empty( $_GET['soliloquy-upgraded'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		?>
<div class="updated">
	<p><strong><?php esc_html_e( 'Congratulations! You have upgraded your sliders successfully!', 'soliloquy' ); ?></strong>
	</p>
</div>
		<?php
	}

	/**
	 * Deletes the Soliloquy slider association for the image being deleted.
	 *
	 * @since 1.0.0
	 *
	 * @param int $attach_id The attachment ID being deleted.
	 */
	public function delete_slider_association( $attach_id ) {

		$has_slider = get_post_meta( $attach_id, '_sol_has_slider', true );

		// Only proceed if the image is attached to any Soliloquy sliders.
		if ( ! empty( $has_slider ) ) {
			foreach ( (array) $has_slider as $post_id ) {
				// Remove the in_slider association.
				$in_slider = get_post_meta( $post_id, '_sol_in_slider', true );
				if ( ! empty( $in_slider ) ) {
					$key = array_search( $attach_id, (array) $in_slider, true );
					if ( false !== $key ) {
						unset( $in_slider[ $key ] );
					}
				}

				update_post_meta( $post_id, '_sol_in_slider', $in_slider );

				// Remove the image from the slider altogether.
				$slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
				if ( ! empty( $slider_data['slider'] ) ) {
					unset( $slider_data['slider'][ $attach_id ] );
				}

				// Update the post meta for the slider.
				update_post_meta( $post_id, '_sol_slider_data', $slider_data );

				// Flush necessary slider caches.
				Soliloquy_Common_Lite::get_instance()->flush_slider_caches( $post_id, ( ! empty( $slider_data['config']['slug'] ) ? $slider_data['config']['slug'] : '' ) );
			}
		}
	}

	/**
	 * Removes any extra cropped images when an attachment is deleted.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The post ID.
	 * @return null        Return early if the appropriate metadata cannot be retrieved.
	 */
	public function delete_cropped_image( $post_id ) {

		// Get attachment image metadata.
		$metadata = wp_get_attachment_metadata( $post_id );

		// Return if no metadata is found.
		if ( ! $metadata ) {
			return;
		}

		// Return if we don't have the proper metadata.
		if ( ! isset( $metadata['file'] ) || ! isset( $metadata['image_meta']['resized_images'] ) ) {
			return;
		}

		// Grab the necessary info to removed the cropped images.
		$wp_upload_dir  = wp_upload_dir();
		$pathinfo       = pathinfo( $metadata['file'] );
		$resized_images = $metadata['image_meta']['resized_images'];

		// Loop through and deleted and resized/cropped images.
		foreach ( $resized_images as $dims ) {
			// Get the resized images filename and delete the image.
			$file = $wp_upload_dir['basedir'] . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $dims . '.' . $pathinfo['extension'];

			// Delete the resized image.
			if ( file_exists( $file ) ) {
				wp_delete_file( $file );
			}
		}
	}

	/**
	 * Trash a slider when the slider post type is trashed.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id   The post ID being trashed.
	 * @return null Return early if no slider is found.
	 */
	public function trash_slider( $id ) {

		$slider = get_post( $id );

		// Flush necessary slider caches to ensure trashed sliders are not showing.
		Soliloquy_Common_Lite::get_instance()->flush_slider_caches( $id );

		// Return early if not an Soliloquy slider.
		if ( 'soliloquyv2' !== $slider->post_type ) {
			return;
		}

		// Set the slider status to inactive.
		$slider_data = get_post_meta( $id, '_sol_slider_data', true );
		if ( empty( $slider_data ) ) {
			return;
		}

		$slider_data['status'] = 'inactive';
		update_post_meta( $id, '_sol_slider_data', $slider_data );
	}

	/**
	 * Untrash a slider when the slider post type is untrashed.
	 *
	 * @since 1.0.0
	 *
	 * @param  int $id   The post ID being untrashed.
	 * @return void
	 */
	public function untrash_slider( $id ) {

		$slider = get_post( $id );

		// Flush necessary slider caches to ensure untrashed sliders are showing.
		Soliloquy_Common_Lite::get_instance()->flush_slider_caches( $id );

		// Return early if not an Soliloquy slider.
		if ( 'soliloquyv2' !== $slider->post_type ) {
			return;
		}

		// Set the slider status to inactive.
		$slider_data = get_post_meta( $id, '_sol_slider_data', true );
		if ( empty( $slider_data ) ) {
			return;
		}

		if ( isset( $slider_data['status'] ) ) {
			unset( $slider_data['status'] );
		}

		update_post_meta( $id, '_sol_slider_data', $slider_data );
	}

	/**
	 * Called whenever an upgrade button / link is displayed in Lite, this function will
	 * check if there's a shareasale ID specified.
	 *
	 * There are three ways to specify an ID, ordered by highest to lowest priority
	 * - add_filter( 'soliloquy_shareasale_id', function() { return 1234; } );
	 * - define( 'SOLILOQUY_SHAREASALE_ID', 1234 );
	 * - get_option( 'soliloquy_shareasale_id' ); (with the option being in the wp_options table)
	 *
	 * If an ID is present, returns the ShareASale link with the affiliate ID, and tells
	 * ShareASale to then redirect to soliloquywp.com/lite
	 *
	 * If no ID is present, just returns the soliloquywp.com/lite URL with UTM tracking.
	 *
	 * @param string $url    The URL to use if there's no ShareASale ID.
	 * @param string $medium The UTM medium to use.
	 * @param string $button The UTM campaign to use.
	 * @param string $append Any additional query string to append.
	 * @return string
	 *
	 * @since 2.5.0
	 */
	public function get_upgrade_link( $url = false, $medium = 'default', $button = 'default', $append = false ) {

		// Check if there's a constant.
		$shareasale_id = '';
		if ( defined( 'SOLILOQUY_SHAREASALE_ID' ) ) {
			$shareasale_id = SOLILOQUY_SHAREASALE_ID;
		}

		// If there's no constant, check if there's an option.
		if ( empty( $shareasale_id ) ) {
			$shareasale_id = get_option( 'soliloquy_shareasale_id', '' );
		}

		// Whether we have an ID or not, filter the ID.
		$shareasale_id = apply_filters( 'soliloquy_shareasale_id', $shareasale_id );
		// If at this point we still don't have an ID, we really don't have one!
		// Just return the standard upgrade URL.
		if ( empty( $shareasale_id ) ) {
			if ( false === filter_var( $url, FILTER_VALIDATE_URL ) ) {
				// prevent a possible typo.
				$url = false;
			}
			$url = ( false !== $url ) ? trailingslashit( esc_url( $url ) ) : 'https://soliloquywp.com/lite/';
			return $url . '?utm_source=liteplugin&utm_medium=' . $medium . '&utm_campaign=' . $button . $append;
		}

		// If here, we have a ShareASale ID
		// Return ShareASale URL with redirect.
		return 'http://www.shareasale.com/r.cfm?u=' . $shareasale_id . '&b=380096&m=40286&afftrack=&urllink=soliloquywp%2Ecom%2Flite%2F';
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Soliloquy_Common_Admin_Lite object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Common_Admin_Lite ) ) {
			self::$instance = new Soliloquy_Common_Admin_Lite();
		}

		return self::$instance;
	}
}

// Load the common admin class.
$soliloquy_common_admin_lite = Soliloquy_Common_Admin_Lite::get_instance();
