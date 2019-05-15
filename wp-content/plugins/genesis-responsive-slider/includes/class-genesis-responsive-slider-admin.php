<?php
/**
 * Genesis Responsive Slider Admin.
 *
 * @package genesis-responsive-slider
 */

/**
 * Creates settings and outputs admin menu and settings page.
 *
 * @package genesis-responsive-slider
 */
class Genesis_Responsive_Slider_Admin {

	/**
	 * Constructor.
	 */
	public static function init() {

		if ( ! function_exists( 'genesis_get_option' ) ) {
			return false;
		}

		add_action( 'admin_init', array( 'Genesis_Responsive_Slider_Admin', 'register_genesis_responsive_slider_maybe_reset_settings' ) );
		add_action( 'admin_notices', array( 'Genesis_Responsive_Slider_Admin', 'genesis_responsive_slider_notice' ) );
		add_action( 'admin_menu', array( 'Genesis_Responsive_Slider_Admin', 'genesis_responsive_slider_settings_init' ), 15 );
		add_filter( 'screen_layout_columns', array( 'Genesis_Responsive_Slider_Admin', 'genesis_responsive_slider_settings_layout_columns' ), 10, 2 );
	}

	/**
	 * Reset settings.
	 */
	public static function register_genesis_responsive_slider_maybe_reset_settings() {

		register_setting( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD, GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD );
		add_option( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD, Genesis_Responsive_Slider::genesis_responsive_slider_defaults(), '', 'yes' );

		if ( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'reset' ) ) {
			update_option( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD, Genesis_Responsive_Slider::genesis_responsive_slider_defaults() );

			genesis_admin_redirect( 'genesis_responsive_slider', array( 'reset' => 'true' ) );
			exit;
		}

	}

	/**
	 * This is the notice that displays when you successfully save or reset
	 * the slider settings.
	 */
	public static function genesis_responsive_slider_notice() {

		if ( ! isset( $_REQUEST['page'] ) || 'genesis_responsive_slider' !== $_REQUEST['page'] ) {  // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			return;
		}

		if ( ( isset( $_GET['reset'] ) && 'true' === $_GET['reset'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( __( 'Settings reset.', 'genesis-responsive-slider' ) ) . '</p></div>';
		} elseif ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( __( 'Settings saved.', 'genesis-responsive-slider' ) ) . '</p></div>';
		}

	}

	/**
	 * This is a necessary go-between to get our scripts and boxes loaded
	 * on the theme settings page only, and not the rest of the admin
	 */
	public static function genesis_responsive_slider_settings_init() {
		global $_genesis_responsive_slider_settings_pagehook;

		// Add "Design Settings" submenu.
		$_genesis_responsive_slider_settings_pagehook = add_submenu_page( 'genesis', __( 'Slider Settings', 'genesis-responsive-slider' ), __( 'Slider Settings', 'genesis-responsive-slider' ), 'manage_options', 'genesis_responsive_slider', array( 'Genesis_Responsive_Slider_Admin', 'genesis_responsive_slider_settings_admin' ) );

		add_action( 'load-' . $_genesis_responsive_slider_settings_pagehook, array( 'Genesis_Responsive_Slider_Admin', 'genesis_responsive_slider_settings_scripts' ) );
		add_action( 'load-' . $_genesis_responsive_slider_settings_pagehook, array( 'Genesis_Responsive_Slider_Admin', 'genesis_responsive_slider_settings_boxes' ) );
	}

	/**
	 * Loads the scripts required for the settings page
	 */
	public static function genesis_responsive_slider_settings_scripts() {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'genesis_responsive_slider_admin_scripts', GENESIS_RESPONSIVE_SLIDER_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), GENESIS_RESPONSIVE_SLIDER_VERSION, true );
	}

	/**
	 * Loads metaboxes.
	 */
	public static function genesis_responsive_slider_settings_boxes() {
		global $_genesis_responsive_slider_settings_pagehook;

		add_meta_box( 'genesis-responsive-slider-options', __( 'Genesis Responsive Slider Settings_Admin', 'genesis-responsive-slider' ), array( 'Genesis_Responsive_Slider_Admin', 'genesis_responsive_slider_options_box' ), $_genesis_responsive_slider_settings_pagehook, 'column1' );
	}

	/**
	 * Tell WordPress that we want only 1 column available for our meta-boxes.
	 *
	 * @param  int    $columns Column number.
	 * @param  string $screen  Screen.
	 * @return int             Columns.
	 */
	public static function genesis_responsive_slider_settings_layout_columns( $columns, $screen ) {
		global $_genesis_responsive_slider_settings_pagehook;

		if ( $screen === $_genesis_responsive_slider_settings_pagehook ) {
			// This page should have 1 column settings.
			$columns[ $_genesis_responsive_slider_settings_pagehook ] = 1;
		}

		return $columns;
	}

	/**
	 * This function is what actually gets output to the page. It handles the markup,
	 * builds the form, outputs necessary JS stuff, and fires <code>do_meta_boxes()</code>
	 */
	public static function genesis_responsive_slider_settings_admin() {
			global $_genesis_responsive_slider_settings_pagehook, $screen_layout_columns;

			$width = 'width: 99%;';
			$hide2 = 'display: none;';
			$hide3 = 'display: none;';
		?>
			<div id="gs" class="wrap genesis-metaboxes">
			<form method="POST" action="options.php">

				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
				<?php settings_fields( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); // important! ?>

				<h2>
					<?php esc_html_e( 'Genesis - Responsive Slider', 'genesis-responsive-slider' ); ?>
					<input type="submit" class="button-primary genesis-h2-button" value="<?php esc_html_e( 'Save Settings', 'genesis-responsive-slider' ); ?>" />
					<input type="submit" class="button-highlighted genesis-h2-button" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[reset]" value="<?php esc_html_e( 'Reset Settings', 'genesis-responsive-slider' ); ?>" onclick="return genesis_confirm('<?php echo esc_js( __( 'Are you sure you want to reset?', 'genesis-responsive-slider' ) ); ?>');" />
				</h2>

				<div class="metabox-holder">
					<div class="postbox-container" style="<?php echo esc_html( $width ); ?>">
						<?php do_meta_boxes( $_genesis_responsive_slider_settings_pagehook, 'column1', null ); ?>
					</div>
				</div>

				<div class="bottom-buttons">
					<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Settings', 'genesis-responsive-slider' ); ?>" />
					<input type="submit" class="button-highlighted" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[reset]" value="<?php esc_html_e( 'Reset Settings', 'genesis-responsive-slider' ); ?>" />
				</div>

				<?php wp_nonce_field( 'genesis_responsive_slider', 'genesis_responsive_slider_nonce' ); ?>

			</form>
			</div>
			<script type="text/javascript">
				//<![CDATA[
				jQuery(document).ready( function($) {
					// close postboxes that should be closed
					$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
					// postboxes setup
					postboxes.add_postbox_toggles('<?php echo esc_html( $_genesis_responsive_slider_settings_pagehook ); ?>');
				});
				//]]>
			</script>

			<?php
	}

	/**
	 * This function generates the form code to be used in the metaboxes
	 *
	 * @since 0.9
	 */
	public static function genesis_responsive_slider_options_box() {
		?>

				<div id="genesis-responsive-slider-content-type">

					<h4><?php esc_html_e( 'Type of Content', 'genesis-responsive-slider' ); ?></h4>

					<p><label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[post_type]"><?php esc_html_e( 'Would you like to use posts or pages', 'genesis-responsive-slider' ); ?>?</label>
						<select id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[post_type]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[post_type]">
							<?php

							$post_types = get_post_types( array( 'public' => true ), 'names', 'and' );
							$post_types = array_filter( $post_types, array( 'Genesis_Responsive_Slider_Admin', 'genesis_responsive_slider_maybe_exclude_post_types' ) );

							foreach ( $post_types as $post_type ) {
								?>

								<option style="padding-right:10px;" value="<?php echo esc_attr( $post_type ); ?>" <?php selected( esc_attr( $post_type ), Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'post_type' ) ); ?>><?php echo esc_attr( $post_type ); ?></option><?php } ?>

						</select></p>

				</div>

				<div id="genesis-responsive-slider-content-filter">

					<div id="genesis-responsive-slider-taxonomy">

						<p><strong style="display: block; font-size: 11px; margin-top: 10px;"><?php esc_html_e( 'By Taxonomy and Terms', 'genesis-responsive-slider' ); ?></strong><label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[posts_term]"><?php esc_html_e( 'Choose a term to determine what slides to include', 'genesis-responsive-slider' ); ?>.</label>

							<select id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[posts_term]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[posts_term]" style="margin-top: 5px;">

								<option style="padding-right:10px;" value="" <?php selected( '', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'posts_term' ) ); ?>><?php esc_html_e( 'All Taxonomies and Terms', 'genesis-responsive-slider' ); ?></option>
				<?php
				$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

				$taxonomies = array_filter( $taxonomies, array( 'Genesis_Responsive_Slider_Admin', 'genesis_responsive_slider_maybe_exclude_taxonomies' ) );

				foreach ( $taxonomies as $taxonomy ) {
					$query_label = '';
					if ( ! empty( $taxonomy->query_var ) ) {
						$query_label = $taxonomy->query_var;
					} else {
						$query_label = $taxonomy->name;
					}
					?>
									<optgroup label="<?php echo esc_attr( $taxonomy->labels->name ); ?>">

										<option style="margin-left: 5px; padding-right:10px;" value="<?php echo esc_attr( $query_label ); ?>" <?php selected( esc_attr( $query_label ), Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'posts_term' ) ); ?>><?php echo esc_html( $taxonomy->labels->all_items ); ?></option>
										<?php
										$terms = get_terms( $taxonomy->name, 'orderby=name&hide_empty=1' );
										foreach ( $terms as $term ) {
											?>
										<option style="margin-left: 8px; padding-right:10px;" value="<?php echo esc_attr( $query_label ) . ',' . esc_html( $term->slug ); ?>" <?php selected( esc_attr( $query_label ) . ',' . $term->slug, Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'posts_term' ) ); ?>><?php echo '-' . esc_attr( $term->name ); ?></option><?php } ?>

									</optgroup> <?php } ?>

							</select>
						</p>

						<p><strong style="display: block; font-size: 11px; margin-top: 10px;"><?php esc_html_e( 'Include or Exclude by Taxonomy ID', 'genesis-responsive-slider' ); ?></strong></p>

						<p>
							<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[exclude_terms]"><?php printf( esc_html__( 'List which category, tag or other taxonomy IDs to exclude. (1,2,3,4 for example)', 'genesis-responsive-slider' ), '<br />' ); ?></label>
						</p>

						<p>
							<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[exclude_terms]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[exclude_terms]" value="<?php echo esc_attr( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'exclude_terms' ) ); ?>" style="width:60%;" />
						</p>

					</div>

					<p>
						<?php /* Translators: %s is the ID. */ ?>
						<strong style="font-size:11px;margin-top:10px;"><label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[include_exclude]"><?php printf( esc_html( __( 'Include or Exclude by %s ID', 'genesis-responsive-slider' ) ), esc_html( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'post_type' ) ) ); ?></label></strong>
					</p>

					<p><?php esc_html_e( 'Choose the include / exclude slides using their post / page ID in a comma-separated list. (1,2,3,4 for example)', 'genesis-responsive-slider' ); ?></p>

					<p>
						<select style="margin-top: 5px;" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[include_exclude]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[include_exclude]">
							<option style="padding-right:10px;" value="" <?php selected( '', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'include_exclude' ) ); ?>><?php esc_html_e( 'Select', 'genesis-responsive-slider' ); ?></option>
							<option style="padding-right:10px;" value="include" <?php selected( 'include', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'include_exclude' ) ); ?>><?php esc_html_e( 'Include', 'genesis-responsive-slider' ); ?></option>
							<option style="padding-right:10px;" value="exclude" <?php selected( 'exclude', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'include_exclude' ) ); ?>><?php esc_html_e( 'Exclude', 'genesis-responsive-slider' ); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[post_id]"><?php esc_html_e( 'List which', 'genesis-responsive-slider' ); ?> <strong><?php echo esc_html( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'post_type' ) ) . ' ' . esc_html__( 'ID', 'genesis-responsive-slider' ); ?>s</strong> <?php esc_html_e( 'to include / exclude. (1,2,3,4 for example)', 'genesis-responsive-slider' ); ?></label></p>
					<p>
						<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[post_id]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[post_id]" value="<?php echo esc_attr( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'post_id' ) ); ?>" style="width:60%;" />
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[posts_num]"><?php esc_html_e( 'Number of Slides to Show', 'genesis-responsive-slider' ); ?>:</label>
						<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[posts_num]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[posts_num]" value="<?php echo esc_attr( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'posts_num' ) ); ?>" size="2" />
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[posts_offset]"><?php esc_html_e( 'Number of Posts to Offset', 'genesis-responsive-slider' ); ?>:</label>
						<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[posts_offset]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[posts_offset]" value="<?php echo esc_attr( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'posts_offset' ) ); ?>" size="2" />
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[orderby]"><?php esc_html_e( 'Order By', 'genesis-responsive-slider' ); ?>:</label>
						<select id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[orderby]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[orderby]">
							<option style="padding-right:10px;" value="date" <?php selected( 'date', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'orderby' ) ); ?>><?php esc_html_e( 'Date', 'genesis-responsive-slider' ); ?></option>
							<option style="padding-right:10px;" value="title" <?php selected( 'title', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'orderby' ) ); ?>><?php esc_html_e( 'Title', 'genesis-responsive-slider' ); ?></option>
							<option style="padding-right:10px;" value="ID" <?php selected( 'ID', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'orderby' ) ); ?>><?php esc_html_e( 'ID', 'genesis-responsive-slider' ); ?></option>
							<option style="padding-right:10px;" value="rand" <?php selected( 'rand', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'orderby' ) ); ?>><?php esc_html_e( 'Random', 'genesis-responsive-slider' ); ?></option>
						</select>
					</p>

				</div>

				<hr class="div" />

				<h4><?php esc_html_e( 'Transition Settings', 'genesis-responsive-slider' ); ?></h4>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_timer]"><?php esc_html_e( 'Time Between Slides (in milliseconds)', 'genesis-responsive-slider' ); ?>:
						<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_timer]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_timer]" value="<?php echo esc_html( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_timer' ) ); ?>" size="5" /></label>
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_delay]"><?php esc_html_e( 'Slide Transition Speed (in milliseconds)', 'genesis-responsive-slider' ); ?>:
						<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_delay]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_delay]" value="<?php echo esc_html( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_delay' ) ); ?>" size="5" /></label>
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_effect]"><?php esc_html_e( 'Slider Effect', 'genesis-responsive-slider' ); ?>:
						<?php esc_html_e( 'Select one of the following:', 'genesis-responsive-slider' ); ?>
						<select name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_effect]" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_effect]">
							<option value="slide" <?php selected( 'slide', genesis_get_option( 'slideshow_effect', GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ) ); ?>><?php esc_html_e( 'Slide', 'genesis-responsive-slider' ); ?></option>
							<option value="fade" <?php selected( 'fade', genesis_get_option( 'slideshow_effect', GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ) ); ?>><?php esc_html_e( 'Fade', 'genesis-responsive-slider' ); ?></option>
						</select>
					</p>

				<hr class="div" />

				<h4><?php esc_html_e( 'Display Settings', 'genesis-responsive-slider' ); ?></h4>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_width]"><?php esc_html_e( 'Maximum Slider Width (in pixels)', 'genesis-responsive-slider' ); ?>:
						<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_width]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_width]" value="<?php echo esc_html( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_width' ) ); ?>" size="5" /></label>
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_height]"><?php esc_html_e( 'Maximum Slider Height (in pixels)', 'genesis-responsive-slider' ); ?>:
						<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_height]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_height]" value="<?php echo esc_html( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_height' ) ); ?>" size="5" /></label>
					</p>

					<p>
						<input type="checkbox" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_arrows]" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_arrows]" value="1" <?php checked( 1, esc_html( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_arrows' ) ) ); ?> /> <label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_arrows]"><?php esc_html_e( 'Display Next / Previous Arrows in Slider?', 'genesis-responsive-slider' ); ?></label>
					</p>

					<p>
						<input type="checkbox" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_pager]" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_pager]" value="1" <?php checked( 1, Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_pager' ) ); ?> /> <label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_pager]"><?php esc_html_e( 'Display Pagination in Slider?', 'genesis-responsive-slider' ); ?></label>
					</p>

				<hr class="div" />

				<h4><?php esc_html_e( 'Content Settings', 'genesis-responsive-slider' ); ?></h4>

					<p>
						<input type="checkbox" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_no_link]" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_title_show]" value="1" <?php checked( 1, Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_no_link' ) ); ?> /> <label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_no_link]"><?php esc_html_e( 'Do not link Slider image to Post/Page.', 'genesis-responsive-slider' ); ?></label>
					</p>

					<p>
						<input type="checkbox" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_title_show]" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_title_show]" value="1" <?php checked( 1, Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_title_show' ) ); ?> /> <label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_title_show]"><?php esc_html_e( 'Display Post/Page Title in Slider?', 'genesis-responsive-slider' ); ?></label>
					</p>
					<p>
						<input type="checkbox" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_show]" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_show]" value="1" <?php checked( 1, Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_excerpt_show' ) ); ?> /> <label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_show]"><?php esc_html_e( 'Display Content in Slider?', 'genesis-responsive-slider' ); ?></label>
					</p>

					<p>
						<input type="checkbox" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_hide_mobile]" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_hide_mobile]" value="1" <?php checked( 1, Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_hide_mobile' ) ); ?> /> <label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_hide_mobile]"><?php esc_html_e( 'Hide Title & Content on Mobile Devices', 'genesis-responsive-slider' ); ?></label>
					</p>

					<p>
						<?php esc_html_e( 'Select one of the following:', 'genesis-responsive-slider' ); ?>
						<select name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_content]" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_content]">
							<option value="full" <?php selected( 'full', genesis_get_option( 'slideshow_excerpt_content', GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ) ); ?>><?php esc_html_e( 'Display post content', 'genesis-responsive-slider' ); ?></option>
							<option value="excerpts" <?php selected( 'excerpts', genesis_get_option( 'slideshow_excerpt_content', GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ) ); ?>><?php esc_html_e( 'Display post excerpts', 'genesis-responsive-slider' ); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_more_text]"><?php esc_html_e( 'More Text (if applicable)', 'genesis-responsive-slider' ); ?>:</label>
						<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_more_text]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_more_text]" value="<?php echo esc_attr( genesis_get_option( 'slideshow_more_text', GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ) ); ?>" />
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_content_limit]"><?php esc_html_e( 'Limit content to', 'genesis-responsive-slider' ); ?></label>
						<input type="text" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_content_limit]" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_content_limit]" value="<?php echo esc_attr( genesis_option( 'slideshow_excerpt_content_limit', GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ) ); ?>" size="3" />
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_content_limit]"><?php esc_html_e( 'characters', 'genesis-responsive-slider' ); ?></label>
					</p>

					<p><span class="description"><?php esc_html_e( 'Using this option will limit the text and strip all formatting from the text displayed. To use this option, choose "Display post content" in the select box above.', 'genesis-responsive-slider' ); ?></span></p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_width]"><?php esc_html_e( 'Slider Excerpt Width (in percentage)', 'genesis-responsive-slider' ); ?>:
						<input type="text" id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_width]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[slideshow_excerpt_width]" value="<?php echo esc_html( Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_excerpt_width' ) ); ?>" size="5" /></label>
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[location_vertical]"><?php esc_html_e( 'Excerpt Location (vertical)', 'genesis-responsive-slider' ); ?>:</label>
						<select id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[location_vertical]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[location_vertical]">
							<option style="padding-right:10px;" value="top" <?php selected( 'top', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'location_vertical' ) ); ?>><?php esc_html_e( 'Top', 'genesis-responsive-slider' ); ?></option>
							<option style="padding-right:10px;" value="bottom" <?php selected( 'bottom', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'location_vertical' ) ); ?>><?php esc_html_e( 'Bottom', 'genesis-responsive-slider' ); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[location_horizontal]"><?php esc_html_e( 'Excerpt Location (horizontal)', 'genesis-responsive-slider' ); ?>:</label>
						<select id="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[location_horizontal]" name="<?php echo esc_html( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD ); ?>[location_horizontal]">
							<option style="padding-right:10px;" value="left" <?php selected( 'left', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'location_horizontal' ) ); ?>><?php esc_html_e( 'Left', 'genesis-responsive-slider' ); ?></option>
							<option style="padding-right:10px;" value="right" <?php selected( 'right', Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'location_horizontal' ) ); ?>><?php esc_html_e( 'Right', 'genesis-responsive-slider' ); ?></option>
						</select>
					</p>
					<?php
	}

	/**
	 * Used to exclude taxonomies and related terms from list of available terms/taxonomies in widget form().
	 *
	 * @since 0.9
	 * @author Nick Croft
	 *
	 * @param string $taxonomy 'taxonomy' being tested.
	 * @return string
	 */
	public static function genesis_responsive_slider_maybe_exclude_taxonomies( $taxonomy ) {

		$filters = array( '', 'nav_menu' );
		$filters = apply_filters( 'genesis_responsive_slider_exclude_taxonomies', $filters );

		return ( in_array( $taxonomy->name, $filters, true ) ? false : true );

	}

	/**
	 * Used to exclude post types from list of available post_types in widget form().
	 *
	 * @since 0.9
	 * @author Nick Croft
	 *
	 * @param string $type 'post_type' being tested.
	 * @return string
	 */
	public static function genesis_responsive_slider_maybe_exclude_post_types( $type ) {

		$filters = array( '', 'attachment' );
		$filters = apply_filters( 'genesis_responsive_slider_exclude_post_types', $filters );

		return ( ! in_array( $type, $filters, true ) );

	}

	/**
	 * Echos form submit button for settings page.
	 *
	 * @param array $args Arguments.
	 */
	public static function genesis_responsive_slider_form_submit( $args = array() ) {
		echo '<p><input type="submit" class="button-primary" value="' . esc_html__( 'Save Changes', 'genesis-responsive-slider' ) . '" /></p>';
	}

}
