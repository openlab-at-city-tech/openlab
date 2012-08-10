<?php
/**
 * The WP Admin area pages for Achievements.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements
 * @subpackage admin
 * @see dpa_add_admin_menu()
 *
 * $Id: achievements-admin.php 1016 2011-10-07 19:42:03Z DJPaul $
 */

/**
 * Adds a notification message to the wp-admin screens if the installed version of Achievements doesn't match
 * what version the database thinks is installed. For example, if you upgrade by SVN.
 *
 * @global int $blog_id Site ID (variable is from WordPress and hasn't been updated for 3.0; confusing name is confusing)
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_activation_notice() {
	global $blog_id, $bp;

	if ( !$bp->loggedin_user->is_super_admin || BP_ROOT_BLOG != $blog_id )
		return;

	$version = get_site_option( 'achievements-db-version' );
	if ( false !== $version && ACHIEVEMENTS_DB_VERSION == $version )
		return;
	?>
		<div id="message" class="updated fade">
			<p><?php printf( __( '<strong>Achievements is almost ready</strong>. Have you just upgraded to a new version? <a href="%s">Go to the Plugins page</a> and re-activate the plugin.', 'dpa' ), admin_url( 'plugins.php' ) ) ?></p>
		</div>
	<?php
}
add_action( 'admin_notices', 'dpa_activation_notice' );

/**
 * Add link to settings screen on the WP Admin 'plugins' page
 *
 * @param array $links Item links
 * @param string $file Plugin's file name
 * @since 2.0
 */
function dpa_admin_add_settings_link( $links, $file ) {
	if ( 'achievements/loader.php' != $file )
		return $links;

	$settings_link = '<a href="' . admin_url( 'admin.php?page=achievements' ) . '">' . __( 'Settings', 'dpa' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'dpa_admin_add_settings_link', 10, 2 );

/**
 * Registers admin settings API
 *
 * @since 2.0
 */
function dpa_register_admin_settings() {
	register_setting( 'dpa-settings-group', 'achievements', 'dpa_admin_screen_validate' );
}

/**
 * Admin settings API validation function
 *
 * @param array $input New form values
 * @since 2.0
 */
function dpa_admin_screen_validate( $input ) {
	$current_settings = get_blog_option( BP_ROOT_BLOG, 'achievements' );

	if ( is_string( $input ) )  // wpmu-edit.php
		return get_blog_option( BP_ROOT_BLOG, 'achievements' );

	if ( isset( $input['mediakeywords'] ) )
		$input['mediakeywords'] = apply_filters( 'dpa_admin_settings_mediakeywords_before_save', stripslashes( $input['mediakeywords'] ) );

	return wp_parse_args( $input, $current_settings );
}

/**
 * Tells WP that we support two columns
 *
 * @param array $columns Column settings for WordPress admin pages
 * @param string WordPress' admin page "name"
 * @since 2.0
 */
function dpa_admin_screen_layout_columns( $columns, $screen ) {
	if ( 'buddypress_page_achievements' == $screen )
		$columns['buddypress_page_achievements'] = 2;

	return $columns;
}
add_filter( 'screen_layout_columns', 'dpa_admin_screen_layout_columns', 10, 2 );

/**
 * Add metaboxes and contextual help to admin screen
 *
 * @since 2.0
 */
function dpa_admin_screen_on_load() {
	// Configure tab
	add_meta_box( 'dpa-admin-metaboxes-sidebox-1', __( 'Like this plugin?', 'dpa' ), 'dpa_admin_screen_socialmedia', 'buddypress_page_achievements', 'side', 'core' );
	add_meta_box( 'dpa-admin-metaboxes-sidebox-2', __( 'Latest news from the author', 'dpa' ), 'dpa_admin_screen_news', 'buddypress_page_achievements', 'side', 'core' );
	add_meta_box( 'dpa-admin-metaboxes-settingsbox', __( 'Settings', 'dpa' ), 'dpa_admin_screen_settings', 'buddypress_page_achievements', 'normal', 'core' );

	// Support tab
	add_meta_box( 'dpa-admin-metaboxes-email', __( 'Contact us', 'dpa' ), 'dpa_admin_screen_contactform', 'buddypress_page_achievements-support', 'normal', 'core' );
	add_meta_box( 'dpa-admin-metaboxes-helpushelpyou', __( 'Help us help you', 'dpa' ), 'dpa_admin_screen_siteinfo', 'buddypress_page_achievements-support', 'side', 'core' );
	add_meta_box( 'dpa-admin-metaboxes-sidebox-1', __( 'Like this plugin?', 'dpa' ), 'dpa_admin_screen_socialmedia', 'buddypress_page_achievements-support', 'side', 'core' );
	add_meta_box( 'dpa-admin-metaboxes-sidebox-2', __( 'Latest news from the author', 'dpa' ), 'dpa_admin_screen_news', 'buddypress_page_achievements-support', 'side', 'core' );

	// Help panel
	add_filter( 'default_contextual_help', 'dpa_admin_screen_contextual_help' );
}


/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

/**
 * Create the Achievements admin page.
 *
 * @global object $bp BuddyPress global settings
 * @global int $screen_layout_columns Number of columns shown on this admin page
 * @see dpa_admin_screen_layout_columns()
 * @since 2.0
 */
function dpa_admin_screen() {
	global $bp, $screen_layout_columns;

	if ( !$settings = get_blog_option( BP_ROOT_BLOG, 'achievements' ) )
		update_blog_option( BP_ROOT_BLOG, 'achievements', array() );

	$is_support_tab = false;
	if ( !empty( $_GET['tab'] ) && DPA_SLUG_ADMIN_SUPPORT == stripslashes( $_GET['tab'] ) )
		$is_support_tab = true;

	// Email contact form
	if ( !empty( $_POST['contact_body'] ) && !empty( $_POST['contact_type'] ) && !empty( $_POST['contact_email'] ) ) {
		$body  = force_balance_tags( wp_filter_kses( stripslashes( $_POST['contact_body'] ) ) );
		$type  = force_balance_tags( wp_filter_kses( stripslashes( $_POST['contact_type'] ) ) );
		$email = sanitize_email( force_balance_tags( wp_filter_kses( stripslashes( $_POST['contact_email'] ) ) ) );

		if ( $body && $type && $email && is_email( $email ) )
			$email_sent = wp_mail( array( 'paul@byotos.com', $email ), "Achievements support request: " . $type, $body );
	}
?>
	<div id="bp-admin">
		<div id="dpa-admin-metaboxes-general" class="wrap">

			<div id="bp-admin-header">
				<h3><?php _e( 'BuddyPress', 'dpa' ) ?></h3>
				<h4><?php _e( 'Achievements', 'dpa' ) ?></h4>
			</div>

			<div id="bp-admin-nav">
				<ol>
					<li <?php if ( !$is_support_tab ) echo 'class="current"' ?>><a href="<?php echo site_url( 'wp-admin/admin.php?page=' . $bp->achievements->id, 'admin') ?>"><?php _e( 'Configure', 'dpa' ) ?></a></li>
					<li <?php if ( $is_support_tab ) echo 'class="current"' ?>><a href="<?php echo site_url( 'wp-admin/admin.php?page=' . $bp->achievements->id . '&amp;tab=' . DPA_SLUG_ADMIN_SUPPORT, 'admin')  ?>"><?php _e( 'Support', 'dpa' ) ?></a></li>
				</ol>
			</div>

			<?php if ( !empty( $_GET['updated'] ) ) : ?>
				<div id="message" class="updated">
					<p><?php _e( 'Your Achievements settings have been saved.', 'dpa' ) ?></p>
				</div>
			<?php endif; ?>

			<?php if ( isset( $email_sent ) ) : ?>
				<div id="message" class="updated">
					<p><?php _e( "Thanks, we've recieved your message and have emailed you a copy for your records. We'll be in touch soon!", 'dpa' ) ?></p>
				</div>
			<?php endif; ?>

			<div class="dpa-spacer">
				<?php if ( !$is_support_tab ) : ?>
					<p><?php _e( "Achievements gives your BuddyPress community fresh impetus by promoting and rewarding social interaction with challenges, badges and points. For information, support, premium enhancements and developer documentation, visit <a href='http://achievementsapp.com/'>our website</a>.", 'dpa' ) ?></p>
					<p><?php printf( __( "To create and manage Achievements, visit the <a href='%s'>Achievements Directory</a>.", 'dpa' ), dpa_get_achievements_permalink() ) ?></p>
				<?php else : ?>
					<p><?php printf( __( "Have you found a bug or do you have a great idea for the next release? Please make a report on <a href='%s'>BuddyPress.org</a>, or use the form below to get in contact. We're listening.", 'dpa' ), 'http://buddypress.org/community/groups/achievements/forum/' ) ?></p>
				<?php endif; ?>
			</div>

			<?php if ( !$is_support_tab ) : ?>
				<form method="post" action="options.php" id="achievements">
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
				<?php settings_fields( 'dpa-settings-group' ) ?>
			<?php endif; ?>

				<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
					<div id="side-info-column" class="inner-sidebar">
						<?php
						if ( $is_support_tab )
							do_meta_boxes( 'buddypress_page_achievements-support', 'side', $settings );
						else
							do_meta_boxes( 'buddypress_page_achievements', 'side', $settings );
						?>
					</div>
					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content">
							<?php
							if ( $is_support_tab )
								do_meta_boxes( 'buddypress_page_achievements-support', 'normal', $settings );
							else
								do_meta_boxes( 'buddypress_page_achievements', 'normal', $settings );
							?>
						</div>

						<?php if ( !$is_support_tab ) : ?>
							<p><input type="submit" class="button-primary" value="<?php _e( 'Save Settings', 'dpa' ) ?>" /></p>
						<?php endif; ?>
					</div>
				</div>

			<?php if ( !$is_support_tab ) : ?>
			</form>
			<?php endif; ?>

		</div><!-- #dpa-admin-metaboxes-general -->
	</div><!-- #bp-admin -->
<?php
}

/**
 * Adds social media sharing links to the admin screen
 *
 * @since 2.0
 * @param array $settings Site's options 'achievements' meta
 */
function dpa_admin_screen_socialmedia( $settings ) {
?>
	<p><?php _e( 'Why not do any or all of the following:', 'dpa' ) ?></p>
	<ul>
		<li><p><?php _e( 'Tell your friends!', 'dpa' ) ?></a></p></li>
		<li><p><a href="http://wordpress.org/extend/plugins/achievements/"><?php _e( 'Give it a good rating on WordPress.org', 'dpa' ) ?></a>.</p></li>
		<li><p><a href="http://buddypress.org/community/groups/achievements/reviews/"><?php _e( 'Write a review on BuddyPress.org', 'dpa' ) ?></a>.</p></li>
		<li><p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=P3K7Z7NHWZ5CL&amp;lc=GB&amp;item_name=B%2eY%2eO%2eT%2eO%2eS%20%2d%20BuddyPress%20plugins&amp;currency_code=GBP&amp;bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted"><?php _e( 'Thank me by donating towards future development', 'dpa' ) ?></a>.</p></li>
		<li><p><a href="mailto:paul@byotos.com"><?php _e( 'Hire me to create a custom plugin for your site.', 'dpa' ) ?></a></p></li>
	</ul>
	<p><?php _e( 'Or share on one of these social networks:', 'dpa' ) ?></p>
	<ul class="menu">
		<li><a href="http://twitter.com/?status=Check%20out%20Achievements%20for%20%23BuddyPress%20http://wordpress.org/extend/plugins/achievements/"><img src="<?php echo plugins_url( '/images/twitter_32.png', __FILE__ ) ?>" alt="<?php _e( 'Twitter', 'dpa' ) ?>" /></a></li>
		<li><a href="http://www.facebook.com/sharer.php?u=http://wordpress.org/extend/plugins/achievements/"><img src="<?php echo plugins_url( '/images/facebook_32.png', __FILE__ ) ?>" alt="<?php _e( 'Facebook', 'dpa' ) ?>" /></a></li>
		<li><a href="http://del.icio.us/post?url=http://wordpress.org/extend/plugins/achievements&title=Achievements%20for%20BuddyPress"><img src="<?php echo plugins_url( '/images/delicious_32.png', __FILE__ ) ?>" alt="<?php _e( 'Delicious - social bookmarking', 'dpa' ) ?>" /></a></li>
		<li><a href="http://www.stumbleupon.com/submit?url=http://wordpress.org/extend/plugins/achievements&amp;title=Achievements%20for%20BuddyPress"><img src="<?php echo plugins_url( '/images/stumbleupon_32.png', __FILE__ ) ?>" alt="<?php _e( 'Stumble Upon', 'dpa' ) ?>" /></a></li>
	</ul>
<?php
}

/**
 * Add http://byotos.com/ RSS feed to the admin screen
 *
 * @since 2.0
 * @param array $settings Site's options 'achievements' meta
 */
function dpa_admin_screen_news( $settings ) {
	$rss = fetch_feed( 'http://feeds.feedburner.com/achievements-for-buddypress' );
	if ( !is_wp_error( $rss ) ) {
		$content = '<ul>';
		$items = $rss->get_items( 0, $rss->get_item_quantity( 3 ) );

		foreach ( $items as $item )
			$content .= '<li><p><a href="' . esc_url( $item->get_permalink(), null, 'display' ) . '">' . apply_filters( 'dpa_admin_get_rss_feed', stripslashes( $item->get_title() ) ) . '</a></p></li>';

		$content .= '<li class="rss"><p><a href="http://feeds.feedburner.com/achievements-for-buddypress">' . __( 'Subscribe with RSS', 'dpa' ) . '</a></p></li></ul>';
		echo $content;
	} else {
		echo '<ul><li>' . __( 'No news!', 'dpa' ) . '</li></ul>';
	}
}

/**
 * Adds WP Help panel link (the "Help" dropdown in the top-right of the page)
 *
 * @param string $default_text
 * @since 2.0
 */
function dpa_admin_screen_contextual_help( $default_text ) {
	return '<a href="http://buddypress.org/community/groups/achievements/">' . __( 'Support Forums', 'dpa' ) . '</a>';
}

/**
 * Site info box
 *
 * @global wpdb $wpdb WordPress database object
 * @global string $wp_version WordPress version number
 * @global WP_Rewrite $wp_rewrite WordPress Rewrite object for creating pretty URLs
 * @global object $wp_rewrite
 * @param array $settings Site's options 'achievements' meta
 * @since 2.0
 */
function dpa_admin_screen_siteinfo( $settings ) {
	global $wpdb, $wp_rewrite, $wp_version;

	$active_plugins = array();
	$all_plugins = apply_filters( 'all_plugins', get_plugins() );

	foreach ( $all_plugins as $filename => $plugin ) {
		if ( 'Achievements' != $plugin['Name'] && 'BuddyPress' != $plugin['Name'] && is_plugin_active( $filename ) )
			$active_plugins[] = $plugin['Name'] . ': ' . $plugin['Version'];
	}
	natcasesort( $active_plugins );

	if ( !$active_plugins )
		$active_plugins[] = __( 'No other plugins are active', 'dpa' );

	if ( defined( 'MULTISITE' ) && constant( 'MULTISITE' ) == true ) {
		if ( defined( 'SUBDOMAIN_INSTALL' ) && constant( 'SUBDOMAIN_INSTALL' ) == true )
			$is_multisite = __( 'subdomain', 'dpa' );
		else
			$is_multisite = __( 'subdirectory', 'dpa' );
	} else {
		$is_multisite = __( 'no', 'dpa' );
	}

	if ( 1 == constant( 'BP_ROOT_BLOG' ) )
		$is_bp_root_blog = __( 'standard', 'dpa' );
	else
		$is_bp_root_blog = __( 'non-standard', 'dpa' );

	$is_bp_default_child_theme = __( 'no', 'dpa' );
	$theme = current_theme_info();

	if ( 'BuddyPress Default' == $theme->parent_theme )
		$is_bp_default_child_theme = __( 'yes', 'dpa' );

	if ( 'BuddyPress Default' == $theme->name )
		$is_bp_default_child_theme = __( 'n/a', 'dpa' );

  if ( empty( $wp_rewrite->permalink_structure ) )
		$custom_permalinks = __( 'default', 'dpa' );
	else
		if ( strpos( $wp_rewrite->permalink_structure, 'index.php' ) )
			$custom_permalinks = __( 'almost', 'dpa' );
		else
			$custom_permalinks = __( 'custom', 'dpa' );
?>
	<p><?php _e( "If you're submitting a support request, some information about your WordPress site really helps us to diagnose the problem.", 'dpa' ) ?></p>
	<p><?php _e( "It's entirely optional, but if you include the information below in your support request, it really will help. Thank you!", 'dpa' ) ?></p>

	<h4><?php _e( 'Versions', 'dpa' ) ?></h4>
	<ul>
		<li><?php printf( __( 'Achievements: %s', 'dpa' ), ACHIEVEMENTS_VERSION ) ?></li>
		<li><?php printf( __( 'BuddyPress: %s', 'dpa' ), BP_VERSION ) ?></li>
		<li><?php printf( __( 'BP_ROOT_BLOG: %s', 'dpa' ), $is_bp_root_blog ) ?></li>
		<li><?php printf( __( 'MySQL: %s', 'dpa' ), $wpdb->db_version() ) ?></li>
		<li><?php printf( __( 'Permalinks: %s', 'dpa' ), $custom_permalinks ) ?></li>
		<li><?php printf( __( 'PHP: %s', 'dpa' ), phpversion() ) ?></li>
		<li><?php printf( __( 'WordPress: %s', 'dpa' ), $wp_version ) ?></li>
		<li><?php printf( __( 'WordPress multisite: %s', 'dpa' ), $is_multisite ) ?></li>
	</ul>

	<h4><?php _e( 'Theme', 'dpa' ) ?></h4>
	<ul>
		<li><?php printf( __( 'BP-Default child theme: %s', 'dpa' ), $is_bp_default_child_theme ) ?></li>
		<li><?php printf( __( 'Current theme: %s', 'dpa' ), $theme->name ) ?></li>
	</ul>

	<h4><?php _e( 'Active Plugins', 'dpa' ) ?></h4>
	<ul>
		<?php foreach ( $active_plugins as $plugin ) : ?>
			<li><?php echo $plugin ?></li>
		<?php endforeach; ?>
	</ul>
<?php
}

/**
 * Email contact form for support
 *
 * @param array $settings Site's options 'achievements' meta
 * @since 2.0
 */
function dpa_admin_screen_contactform( $settings ) {
?>
	<form name="contact_form" method="post"><div class="setting-contactform setting-group">
			<div class="setting wide">
				<div class="settingname">
					<p><?php _e( "Hello, how can we help?", 'dpa' ) ?></p>
				</div>
				<div class="settingvalue">
						<textarea id="contact_body" name="contact_body"></textarea>
				</div>
				<div style="clear: left"></div>
			</div>

			<div class="setting">
				<div class="settingname">
					<p><?php _e( "What type of request do you have?", 'dpa' ) ?></p>
				</div>
				<div class="settingvalue">
					<select name="contact_type">
						<option value="bug" selected="selected"><?php _e( "Bug report", 'dpa' ) ?></option>
						<option value="idea"><?php _e( "Idea", 'dpa' ) ?></option>
						<option value="suggestion"><?php _e( "Other support request", 'dpa' ) ?></option>
					</select>
				</div>
				<div style="clear: left"></div>
			</div>

			<div class="setting">
				<div class="settingname">
					<p><?php _e( "What's your email address?", 'dpa' ) ?></p>
				</div>
				<div class="settingvalue">
						<input type="text" name="contact_email" />
				</div>
				<div style="clear: left"></div>
			</div>
			<input type="submit" class="button-primary" value="<?php _e( 'Send', 'dpa' ) ?>" />
		</div></form>
<?php
}

/**
 * Main settings for configure screen
 *
 * @param array $settings Site's options 'achievements' meta
 * @since 2.0
 */
function dpa_admin_screen_settings( $settings ) {
	$keywords = !empty( $settings['mediakeywords'] ) ? $settings['mediakeywords'] : '';
?>
	<div class="component">
		<h5><?php _e( "Change Picture", 'dpa' ) ?></h5>

		<div class="setting-group setting-medialibrary">
			<div class="setting wide">
				<div class="settingname">
					<p><?php printf( __( "If your <a href='%s'>WordPress Media Library</a> contains a lot of content, it may be hard to find the images which you want to use for your Achievements' pictures.", 'dpa' ), admin_url( 'upload.php' ) ) ?></p>
					<p><?php _e( "Enter keywords here to search for in your images' titles and descriptions:", 'dpa' ) ?></p>
				</div>
				<div class="settingvalue">
					<input type="text" name="achievements[mediakeywords]" value="<?php echo esc_attr( apply_filters( 'dpa_admin_settings_mediakeywords', $keywords ) ); ?>" />
				</div>
				<div style="clear: left"></div>
			</div>
		</div>

	</div><!-- .component -->
<?php
}
?>