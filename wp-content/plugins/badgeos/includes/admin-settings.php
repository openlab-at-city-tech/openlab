<?php
/**
 * Admin Settings Pages
 *
 * @package BadgeOS
 * @subpackage Admin
 * @author LearningTimes, LLC
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 * @link https://credly.com
 */

/**
 * Register BadgeOS Settings with Settings API.
 *
 * @return void
 */
function badgeos_register_settings() {
	register_setting( 'badgeos_settings_group', 'badgeos_settings', 'badgeos_settings_validate' );
}
add_action( 'admin_init', 'badgeos_register_settings' );

/**
 * Grant BadgeOS manager role ability to edit BadgeOS settings.
 *
 * @since  1.4.0
 *
 * @param  string $capability Required capability.
 * @return string             Required capability.
 */
function badgeos_edit_settings_capability( $capability ) {
	return badgeos_get_manager_capability();
}
add_filter( 'option_page_capability_badgeos_settings_group', 'badgeos_edit_settings_capability' );

/**
 * BadgeOS Settings validation
 *
 * @param  string $input The input we want to validate
 * @return string        Our sanitized input
 */
function badgeos_settings_validate( $input = '' ) {

	global $wpdb;
	// Fetch existing settings
	$original_settings = badgeos_utilities::get_option( 'badgeos_settings' );
	// Allow add-on settings to be sanitized
	do_action( 'badgeos_settings_validate', $input );

	$original_settings = apply_filters( 'badgeos_before_validate_external_settings', $original_settings, $input );
	foreach ( $input as $key => $value ) {
		$original_settings[ $key ] = $value;
	}
	$original_settings = apply_filters( 'badgeos_after_validate_external_settings', $original_settings, $input );

	switch ( $input['tab_action'] ) {
		case 'general':
			$original_settings['minimum_role']                               = isset( $input['minimum_role'] ) ? sanitize_text_field( $input['minimum_role'] ) : $original_settings['minimum_role'];
			$original_settings['debug_mode']                                 = isset( $input['debug_mode'] ) ? sanitize_text_field( $input['debug_mode'] ) : $original_settings['debug_mode'];
			$original_settings['log_entries']                                = isset( $input['log_entries'] ) ? sanitize_text_field( $input['log_entries'] ) : $original_settings['log_entries'];
			$original_settings['ms_show_all_achievements']                   = isset( $input['ms_show_all_achievements'] ) ? sanitize_text_field( $input['ms_show_all_achievements'] ) : $original_settings['ms_show_all_achievements'];
			$original_settings['badgeos_not_earned_image']                   = isset( $input['badgeos_not_earned_image'] ) ? sanitize_text_field( $input['badgeos_not_earned_image'] ) : $original_settings['badgeos_not_earned_image'];
			$original_settings['remove_data_on_uninstall']                   = ( isset( $input['remove_data_on_uninstall'] ) && 'on' === $input['remove_data_on_uninstall'] ) ? 'on' : null;
			$original_settings['achievement_list_shortcode_default_view']    = isset( $input['achievement_list_shortcode_default_view'] ) ? sanitize_text_field( $input['achievement_list_shortcode_default_view'] ) : $original_settings['achievement_list_shortcode_default_view'];
			$original_settings['earned_achievements_shortcode_default_view'] = isset( $input['earned_achievements_shortcode_default_view'] ) ? sanitize_text_field( $input['earned_achievements_shortcode_default_view'] ) : $original_settings['earned_achievements_shortcode_default_view'];
			$original_settings['earned_ranks_shortcode_default_view']        = isset( $input['earned_ranks_shortcode_default_view'] ) ? sanitize_text_field( $input['earned_ranks_shortcode_default_view'] ) : $original_settings['earned_ranks_shortcode_default_view'];
			$original_settings['badgeos_achievement_global_image_width']     = isset( $input['badgeos_achievement_global_image_width'] ) ? sanitize_text_field( $input['badgeos_achievement_global_image_width'] ) : $original_settings['badgeos_achievement_global_image_width'];
			$original_settings['badgeos_achievement_global_image_height']    = isset( $input['badgeos_achievement_global_image_height'] ) ? sanitize_text_field( $input['badgeos_achievement_global_image_height'] ) : $original_settings['badgeos_achievement_global_image_height'];
			$original_settings['badgeos_rank_global_image_width']            = isset( $input['badgeos_rank_global_image_width'] ) ? sanitize_text_field( $input['badgeos_rank_global_image_width'] ) : $original_settings['badgeos_rank_global_image_width'];
			$original_settings['badgeos_rank_global_image_height']           = isset( $input['badgeos_rank_global_image_height'] ) ? sanitize_text_field( $input['badgeos_rank_global_image_height'] ) : $original_settings['badgeos_rank_global_image_height'];
			$original_settings['badgeos_point_global_image_width']           = isset( $input['badgeos_point_global_image_width'] ) ? sanitize_text_field( $input['badgeos_point_global_image_width'] ) : $original_settings['badgeos_point_global_image_width'];
			$original_settings['badgeos_point_global_image_height']          = isset( $input['badgeos_point_global_image_height'] ) ? sanitize_text_field( $input['badgeos_point_global_image_height'] ) : $original_settings['badgeos_point_global_image_height'];
			break;
		case 'ptypes':
			if ( ! empty( $input['achievement_step_post_type'] ) && trim( $original_settings['achievement_step_post_type'] ) !== trim( $input['achievement_step_post_type'] ) ) {
				$achievement_step_post_type = sanitize_text_field( str_replace( ' ', '_', trim( $input['achievement_step_post_type'] ) ) );
				$str_query                  = 'update ' . $wpdb->prefix . "badgeos_achievements set post_type='" . trim( $achievement_step_post_type ) . "' where post_type='" . trim( $original_settings['achievement_step_post_type'] ) . "'";
				$wpdb->query( $str_query );

				$str_query = 'update ' . $wpdb->prefix . "posts set post_type='" . trim( $achievement_step_post_type ) . "' where post_type='" . trim( $original_settings['achievement_step_post_type'] ) . "'";
				$wpdb->query( $str_query );

				badgeos_settings_update_p2p( $achievement_step_post_type, trim( $original_settings['achievement_step_post_type'] ), 'step' );
				$original_settings['achievement_step_post_type'] = ! empty( $input['achievement_step_post_type'] ) ? $achievement_step_post_type : ( isset( $original_settings['achievement_step_post_type'] ) ? $original_settings['achievement_step_post_type'] : 'step' );
			}

			if ( ! empty( $input['achievement_main_post_type'] ) && trim( $original_settings['achievement_main_post_type'] ) !== trim( $input['achievement_main_post_type'] ) ) {
				$achievement_main_post_type = sanitize_text_field( str_replace( ' ', '_', trim( $input['achievement_main_post_type'] ) ) );
				$str_query                  = 'update ' . $wpdb->prefix . "badgeos_achievements set post_type='" . trim( $achievement_main_post_type ) . "' where post_type='" . trim( $original_settings['achievement_main_post_type'] ) . "'";
				$wpdb->query( $str_query );

				$str_query = 'update ' . $wpdb->prefix . "posts set post_type='" . trim( $achievement_main_post_type ) . "' where post_type='" . trim( $original_settings['achievement_main_post_type'] ) . "'";
				$wpdb->query( $str_query );
				badgeos_settings_update_p2p( $input['achievement_main_post_type'], trim( $original_settings['achievement_main_post_type'] ), 'main' );
				$original_settings['achievement_main_post_type'] = ! empty( $achievement_main_post_type ) ? $achievement_main_post_type : ( isset( $original_settings['achievement_main_post_type'] ) ? $original_settings['achievement_main_post_type'] : 'achievement-type' );
			}

			if ( ! empty( $input['points_main_post_type'] ) && trim( $original_settings['points_main_post_type'] ) !== trim( $input['points_main_post_type'] ) ) {
				$points_main_post_type = sanitize_text_field( str_replace( ' ', '_', trim( $input['points_main_post_type'] ) ) );
				$str_query             = 'update ' . $wpdb->prefix . "posts set post_type='" . trim( $points_main_post_type ) . "' where post_type='" . trim( $original_settings['points_main_post_type'] ) . "'";
				$wpdb->query( $str_query );

				badgeos_settings_update_p2p( $points_main_post_type, trim( $original_settings['points_main_post_type'] ), 'main' );
				$original_settings['points_main_post_type'] = ! empty( $points_main_post_type ) ? $points_main_post_type : ( isset( $original_settings['points_main_post_type'] ) ? $original_settings['points_main_post_type'] : 'point_type' );
			}

			if ( ! empty( $input['points_award_post_type'] ) && trim( $original_settings['points_award_post_type'] ) !== trim( $input['points_award_post_type'] ) ) {
				$points_award_post_type = sanitize_text_field( str_replace( ' ', '_', trim( $input['points_award_post_type'] ) ) );
				$str_query              = 'update ' . $wpdb->prefix . "posts set post_type='" . trim( $points_award_post_type ) . "' where post_type='" . trim( $original_settings['points_award_post_type'] ) . "'";
				$wpdb->query( $str_query );

				badgeos_settings_update_p2p( $points_award_post_type, trim( $original_settings['points_award_post_type'] ), 'step' );
				$original_settings['points_award_post_type'] = ! empty( $points_award_post_type ) ? $points_award_post_type : ( isset( $original_settings['points_award_post_type'] ) ? $original_settings['points_award_post_type'] : 'point_award' );
			}

			if ( ! empty( $input['points_deduct_post_type'] ) && trim( $original_settings['points_deduct_post_type'] ) !== trim( $input['points_deduct_post_type'] ) ) {
				$points_deduct_post_type = sanitize_text_field( str_replace( ' ', '_', trim( $input['points_deduct_post_type'] ) ) );
				$str_query               = 'update ' . $wpdb->prefix . "posts set post_type='" . trim( $points_deduct_post_type ) . "' where post_type='" . trim( $original_settings['points_deduct_post_type'] ) . "'";
				$wpdb->query( $str_query );

				badgeos_settings_update_p2p( $points_deduct_post_type, trim( $original_settings['points_deduct_post_type'] ), 'step' );
				$original_settings['points_deduct_post_type'] = ! empty( $points_deduct_post_type ) ? $points_deduct_post_type : ( isset( $original_settings['points_deduct_post_type'] ) ? $original_settings['points_deduct_post_type'] : 'point_deduct' );
			}

			if ( ! empty( $input['ranks_main_post_type'] ) && trim( $original_settings['ranks_main_post_type'] ) !== trim( $input['ranks_main_post_type'] ) ) {

				$ranks_main_post_type = sanitize_text_field( str_replace( ' ', '_', trim( $input['ranks_main_post_type'] ) ) );

				$str_query = 'update ' . $wpdb->prefix . "badgeos_ranks set rank_type='" . trim( $ranks_main_post_type ) . "' where rank_type='" . trim( $original_settings['ranks_main_post_type'] ) . "'";
				$wpdb->query( $str_query );

				$str_query = 'update ' . $wpdb->prefix . "posts set post_type='" . trim( $ranks_main_post_type ) . "' where post_type='" . trim( $original_settings['ranks_main_post_type'] ) . "'";
				$wpdb->query( $str_query );

				badgeos_settings_update_p2p( $ranks_main_post_type, trim( $original_settings['ranks_main_post_type'] ), 'main' );
				$original_settings['ranks_main_post_type'] = ! empty( $ranks_main_post_type ) ? $ranks_main_post_type : ( isset( $original_settings['ranks_main_post_type'] ) ? $original_settings['ranks_main_post_type'] : 'ranks' );
			}

			if ( ! empty( $input['ranks_step_post_type'] ) && trim( $original_settings['ranks_step_post_type'] ) !== trim( $input['ranks_step_post_type'] ) ) {
				$ranks_step_post_type = sanitize_text_field( str_replace( ' ', '_', trim( $input['ranks_step_post_type'] ) ) );
				$str_query            = 'update ' . $wpdb->prefix . "badgeos_ranks set rank_type='" . trim( $ranks_step_post_type ) . "' where rank_type='" . trim( $original_settings['ranks_step_post_type'] ) . "'";
				$wpdb->query( $str_query );

				$str_query = 'update ' . $wpdb->prefix . "posts set post_type='" . trim( $ranks_step_post_type ) . "' where post_type='" . trim( $original_settings['ranks_step_post_type'] ) . "'";
				$wpdb->query( $str_query );

				badgeos_settings_update_p2p( $ranks_step_post_type, trim( $original_settings['ranks_step_post_type'] ), 'step' );
				$original_settings['ranks_step_post_type'] = ! empty( $ranks_step_post_type ) ? $ranks_step_post_type : ( isset( $original_settings['ranks_step_post_type'] ) ? $original_settings['ranks_step_post_type'] : 'rank_requirement' );

			}
			break;
	}

	$original_settings = apply_filters( 'badgeos_validate_setting_page_data', $original_settings, $input );
	// Sanitize the settings data submitted.
	return $original_settings;
}

/**
 * Settings validation
 *
 * @since  1.0.0
 * @param  array $options Form inputs data.
 * @return array          Sanitized form input data.
 */
function badgeos_settings_update_p2p( $new_post_type, $old_post_type, $type ) {
	global $wpdb;

	if ( $type === 'main' ) {
		$str_query = 'select * from ' . $wpdb->prefix . "p2p where p2p_type like '%-to-" . trim( $old_post_type ) . "'";
	} elseif ( $type === 'step' ) {
		$str_query = 'select * from ' . $wpdb->prefix . "p2p where p2p_type like '" . trim( $old_post_type ) . "-to-%'";
	}

	$records = $wpdb->get_results( $str_query );
	foreach ( $records as $rec ) {
		$new_value = '';
		if ( $type === 'main' ) {
			$new_value = str_replace( '-to-' . trim( $old_post_type ), '-to-' . trim( $new_post_type ), $rec->p2p_type );
		} elseif ( $type === 'step' ) {
			$new_value = str_replace( trim( $old_post_type ) . '-to-', trim( $new_post_type ) . '-to-', $rec->p2p_type );
		}
		if ( ! empty( $new_value ) ) {
			$str_query = 'update ' . $wpdb->prefix . "p2p set p2p_type='" . trim( $new_value ) . "' where p2p_id='" . trim( $rec->p2p_id ) . "'";

			$wpdb->query( $str_query );
		}
	}
}

/**
 * BadgeOS main settings page output
 *
 * @since  1.0.0
 * @return void
 */
function badgeos_settings_page() {
	wp_enqueue_style( 'badgeos-jquery-ui-styles' );
	wp_enqueue_style( 'badgeos-admin-styles' );
	wp_enqueue_script( 'badgeos-jquery-ui-js' );
	wp_enqueue_script( 'badgeos-admin-tools-js' );
	$licensed_addons  = apply_filters( 'badgeos_licensed_addons', array() );
	$setting_page_tab = isset( $_GET['bos_s_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['bos_s_tab'] ) ) : 'general';
	if ( ! isset( $setting_page_tab ) || empty( $setting_page_tab ) ) {
		$setting_page_tab = 'general';
	}
	$base_tabs = array( 'general', 'ptypes', 'dupgrades', 'licenses' );
	?>
	<div class="wrap badgeos-tools-page">
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php esc_html_e( 'BadgeOS Settings', 'badgeos' ); ?></h2>

		<div class="nav-tab-wrapper">
			<a href="admin.php?page=badgeos_settings&bos_s_tab=general" class="nav-tab <?php echo ( $setting_page_tab === 'general' || empty( $setting_page_tab ) ) ? 'nav-tab-active' : ''; ?>">
				<i class="fa fa-shield" aria-hidden="true"></i>
				<?php esc_html_e( 'General', 'badgeos' ); ?>
			</a>
			<a href="admin.php?page=badgeos_settings&bos_s_tab=ptypes" class="nav-tab <?php echo $setting_page_tab === 'ptypes' ? 'nav-tab-active' : ''; ?>">
				<i class="fa fa-shield" aria-hidden="true"></i>
				<?php esc_html_e( 'Post Types', 'badgeos' ); ?>
			</a>
			<a href="admin.php?page=badgeos_settings&bos_s_tab=dupgrades" class="nav-tab <?php echo $setting_page_tab === 'dupgrades' ? 'nav-tab-active' : ''; ?>">
				<i class="fa fa-shield" aria-hidden="true"></i>
				<?php esc_html_e( 'Data Upgrade', 'badgeos' ); ?>
			</a>
			<?php do_action( 'badgeos_settings_main_tab_header', $setting_page_tab ); ?>
			<?php if ( count( $licensed_addons ) ) { ?>
				<a href="admin.php?page=badgeos_settings&bos_s_tab=licenses" class="nav-tab <?php echo $setting_page_tab === 'licenses' ? 'nav-tab-active' : ''; ?>">
					<i class="fa fa-shield" aria-hidden="true"></i>
					<?php esc_html_e( 'Licenses', 'badgeos' ); ?>
				</a>
			<?php } ?>
		</div>
		<?php
		if ( in_array( $setting_page_tab, $base_tabs ) ) {
			include 'settings/' . $setting_page_tab . '.php';
		} else {
			?>
		 
			<?php do_action( 'badgeos_settings_main_tab_content', $setting_page_tab ); ?>
		<?php } ?>
	</div>
	<?php
}


/**
 * Adds additional options to the BadgeOS Settings page
 *
 * @since 1.0.0
 */
function badgeos_license_settings() {

	// Get our licensed add-ons
	$licensed_addons = apply_filters( 'badgeos_licensed_addons', array() );

	// If we have any licensed add-ons
	if ( ! empty( $licensed_addons ) ) {

		// Output the header for licenses
		echo '<tr><td colspan="2"><hr/><h2>' . esc_html__( 'BadgeOS Add-on Licenses', 'badgeos' ) . '</h2></td></tr>';

		// Sort our licenses alphabetially
		ksort( $licensed_addons );

		// Output each individual licensed product
		foreach ( $licensed_addons as $slug => $addon ) {
			$status = ! empty( $addon['license_status'] ) ? $addon['license_status'] : 'inactive';
			echo '<tr valign="top">';
			echo '<th scope="row">';
			echo '<label for="badgeos_settings[licenses][' . esc_attr( $slug ) . ']">' . esc_html( urldecode( $addon['item_name'] ) ) . ': </label></th>';
			echo '<td>';
			echo '<input type="text" size="30" name="badgeos_settings[licenses][' . esc_attr( $slug ) . ']" id="badgeos_settings[licenses][' . esc_attr( $slug ) . ']" value="' . esc_attr( $addon['license'] ) . '" />';
			echo ' <span class="badgeos-license-status ' . esc_attr( $status ) . '">' . sprintf( esc_html__( 'License Status: %s', 'badgeos' ), '<strong>' . esc_html( ucfirst( $status ) ) . '</strong>' ) . '</span>';
			echo '</td>';
			echo '</tr>';
		}
	}

}
add_action( 'badgeos_license_settings', 'badgeos_license_settings', 0 );

/**
 * Add-ons settings page
 *
 * @since  1.0.0
 */
function badgeos_add_ons_page() {
	$image_url = $GLOBALS['badgeos']->directory_url . 'images/';
	?>
	<div class="wrap badgeos-addons">
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php printf( esc_html__( 'BadgeOS Add-Ons &nbsp;&mdash;&nbsp; %s', 'badgeos' ), '<a href="http://badgeos.org/add-ons/?ref=badgeos" class="button-primary" target="_blank">' . esc_html__( 'Browse All Add-Ons', 'badgeos' ) . '</a>' ); ?></h2>
		<p><?php esc_html_e( 'These add-ons extend the functionality of BadgeOS.', 'badgeos' ); ?></p>
		<?php echo wp_kses_post( badgeos_add_ons_get_feed() ); ?>
	</div>
	<?php
}

/**
 * Get all add-ons from the BadgeOS catalog feed.
 *
 * @since  1.2.0
 * @return string Concatenated markup from feed, or error message
 */
function badgeos_add_ons_get_feed() {

	// Attempt to pull back our cached feed
	$feed = get_transient( 'badgeos_add_ons_feed' );
	// If we don't have a cached feed, pull back fresh data
	if ( empty( $feed ) ) {

		// Retrieve and parse our feed
		$feed = wp_remote_get( 'http://badgeos.org/?feed=addons', array( 'sslverify' => false ) );
		if ( ! is_wp_error( $feed ) ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$feed = wp_remote_retrieve_body( $feed );
				$feed = str_replace( '<html><body>', '', $feed );
				$feed = str_replace( '</body></html>', '', $feed );
				// Cache our feed for 1 hour
				set_transient( 'badgeos_add_ons_feed', $feed, HOUR_IN_SECONDS );
			}
		} else {
			$feed = '<div class="error"><p>' . esc_html__( 'There was an error retrieving the add-ons list from the server. Please try again later.', 'badgeos' ) . '</div>';
		}
	}

	// Return our feed, or error message
	return $feed;
}

/**
 * Help and Support settings page
 *
 * @since  1.0.0
 * @return void
 */
function badgeos_help_support_page() {
	?>
	<div class="wrap" >
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php esc_html_e( 'BadgeOS Help and Support', 'badgeos' ); ?></h2>
		<h2><?php esc_html_e( 'About BadgeOS', 'badgeos' ); ?>:</h2>
		<p><?php esc_html_e( 'BadgeOS&trade; is plugin to WordPress that allows your site\'s users to complete tasks, demonstrate achievements, and earn badges. You define the achievement types, organize your requirements any way you like, and choose from a range of options to determine whether each task or requirement has been achieved.', 'badgeos' ); ?></p>
		<p>
		<?php
		printf(
			esc_html__( "BadgeOS is extremely extensible. Check out examples of what we've built with it, and stay connected to the project site for updates, add-ins and news. Share your ideas and code improvements on %s so we can keep making BadgeOS better for everyone.", 'badgeos' ),
			'<a href="https://github.com/opencredit/BadgeOS" target="_blank">GitHub</a>'
		);
		?>
		</p>
		<?php do_action( 'badgeos_help_support_page_about' ); ?>

		<h2><?php esc_html_e( 'Help / Support', 'badgeos' ); ?>:</h2>
		<p>
		<?php
		printf(
			esc_html__( 'For support on using BadgeOS or to suggest feature enhancements, visit the %1$s. The BadgeOS team does perform custom development that extends the BadgeOS platform in some incredibly powerful ways. %2$s with inquiries. See examples of %3$s.', 'badgeos' ),
			sprintf(
				'<a href="http://badgeos.org" target="_blank">%s</a>',
				esc_html__( 'BadgeOS site', 'badgeos' )
			),
			sprintf(
				'<a href="http://badgeos.org/contact/" target="_blank">%s</a>',
				esc_html__( 'Contact us', 'badgeos' )
			),
			sprintf(
				'<a href="http://badgeos.org/about/sample-sites/">%s</a>',
				esc_html__( 'enhanced BadgeOS projects', 'badgeos' )
			)
		);
		?>
		</p>
		<p><?php printf( esc_html__( 'Please submit bugs or issues to %s for the BadgeOS Project.', 'badgeos' ), '<a href="https://github.com/opencredit/BadgeOS" target="_blank">Github</a>' ); ?></p>
		<?php do_action( 'badgeos_help_support_page_help' ); ?>

		<h2><?php esc_html_e( 'Shortcodes', 'badgeos' ); ?>:</h2>
		<p>
		<?php
		printf(
			esc_html__( 'With BadgeOS activated, the following shortcodes can be placed on any page or post within WordPress to expose a variety of BadgeOS functions. Visit %s for additional information on shortcodes.', 'badgeos' ),
			'<a href="http://badgeos.org/support/shortcodes/" target="_blank">BadgeOS.org</a>'
		);
		?>
		</p>
		<?php do_action( 'badgeos_help_support_page_shortcodes' ); ?>
	</div>
	<?php
}

/**
 * Globally replace "Featured Image" text with "Achievement Image".
 *
 * @since  1.3.0
 *
 * @param  string $string Original output string.
 * @return string         Potentially modified output string.
 */
function badgeos_featured_image_metabox_title( $string = '' ) {

	// If this is a new achievement type post.
	// OR this is an existing achievement type post.
	// AND the text is "Featured Image".
	// ...replace the string.
	$badgeos_settings = ( $exists = badgeos_utilities::get_option( 'badgeos_settings' ) ) ? $exists : array();
	if (
		(
			( isset( $_GET['post_type'] ) && in_array( sanitize_text_field( wp_unslash( $_GET['post_type'] ) ), badgeos_get_achievement_types_slugs(), true ) )
			|| ( isset( $_GET['post'] ) && badgeos_is_achievement( sanitize_text_field( wp_unslash( $_GET['post'] ) ) ) )
		) && 'Featured Image' === $string

	) {
		$string = esc_html__( 'Achievement Image', 'badgeos' );
	} elseif (
		(
			( isset( $_GET['post_type'] ) && $badgeos_settings['achievement_main_post_type'] === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) )
			|| ( isset( $_GET['post'] ) && $badgeos_settings['achievement_main_post_type'] === badgeos_utilities::get_post_type( sanitize_text_field( wp_unslash( $_GET['post'] ) ) ) )
		) && 'Featured Image' === $string
	) {
		$string = esc_html__( 'Default Achievement Image', 'badgeos' );
	}

	return $string;
}

add_filter( 'gettext', 'badgeos_featured_image_metabox_title' );

/**
 * Change "Featured Image" to "Achievement Image" in post editor metabox.
 *
 * @since  1.3.0
 *
 * @param  string  $content HTML output.
 * @param  integer $id      Post ID.
 * @return string           Potentially modified output.
 */
function badgeos_featured_image_metabox_text( $content = '', $id = 0 ) {
	$badgeos_settings = ( $exists = badgeos_utilities::get_option( 'badgeos_settings' ) ) ? $exists : array();
	if ( badgeos_is_achievement( $id ) ) {
		$content = str_replace( 'featured image', esc_html__( 'achievement image', 'badgeos' ), $content );
	} elseif ( $badgeos_settings['achievement_main_post_type'] === badgeos_utilities::get_post_type( $id ) ) {
		$content = str_replace( 'featured image', esc_html__( 'default achievement image', 'badgeos' ), $content );
	}

	return $content;
}
add_filter( 'admin_post_thumbnail_html', 'badgeos_featured_image_metabox_text', 10, 2 );

/**
 * Change "Featured Image" to "Achievement Image" throughout media modal.
 *
 * @since  1.3.0
 *
 * @param  array  $strings All strings passed to media modal.
 * @param  object $post    Post object.
 * @return array           Potentially modified strings.
 */
function badgeos_media_modal_featured_image_text( $strings = array(), $post = null ) {

	if ( is_object( $post ) ) {
		$badgeos_settings = ( $exists = badgeos_utilities::get_option( 'badgeos_settings' ) ) ? $exists : array();
		if ( badgeos_is_achievement( $post->ID ) ) {
			$strings['setFeaturedImageTitle'] = esc_html__( 'Set Achievement Image', 'badgeos' );
			$strings['setFeaturedImage']      = esc_html__( 'Set achievement image', 'badgeos' );
		} elseif ( $badgeos_settings['achievement_main_post_type'] === $post->post_type ) {
			$strings['setFeaturedImageTitle'] = esc_html__( 'Set Default Achievement Image', 'badgeos' );
			$strings['setFeaturedImage']      = esc_html__( 'Set default achievement image', 'badgeos' );
		}
	}

	return $strings;
}
add_filter( 'media_view_strings', 'badgeos_media_modal_featured_image_text', 10, 2 );

/**
 * Get capability required for BadgeOS administration.
 *
 * @since  1.4.0
 *
 * @return string User capability.
 */
function badgeos_get_manager_capability() {
	$badgeos_settings = badgeos_utilities::get_option( 'badgeos_settings' );
	return isset( $badgeos_settings['minimum_role'] ) ? $badgeos_settings['minimum_role'] : 'manage_options';
}
