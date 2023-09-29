<?php
/**
 * BuddyPress Groupblog Admin Settings.
 *
 * @package BP_Groupblog
 */

/**
 * Initializes a new site.
 *
 * The "wp_initialize_site" action has been available since WordPress 5.1.0.
 *
 * @since 1.9.3
 *
 * @param WP_Site $new_site The new site object.
 * @param array $args The array of initialization arguments.
 */
function bp_groupblog_site_defaults( $new_site, $args ) {
	bp_groupblog_blog_defaults( $new_site->blog_id );
}

/**
 * Legacy initialization of a new site.
 *
 * The "wpmu_new_blog" action has been deprecated since WordPress 5.1.0.
 *
 * @param int $blog_id The numeric ID of the WordPress Blog.
 *
 * @since 1.0
 */
function bp_groupblog_blog_defaults( $blog_id ) {
	global $bp, $wp_rewrite;

	// Only apply defaults to groupblog blogs.
	if ( bp_is_groups_component() ) {

		switch_to_blog( $blog_id );

		// Get the site options.
		$options = get_site_option( 'bp_groupblog_blog_defaults_options' );

		foreach ( (array) $options as $key => $value ) {
			update_option( $key, $value );
		}

		// Override default themes.
		if ( ! empty( $options['theme'] ) ) {
			// We want something other than the default theme.
			$values = explode( '|', $options['theme'] );
			switch_theme( $values[0], $values[1] );
		}

		// Groupblog bonus options.
		if ( isset( $options['default_cat_name'] ) && strlen( $options['default_cat_name'] ) > 0 ) {
			global $wpdb;
			$cat = $options['default_cat_name'];
			$slug = str_replace( ' ', '-', strtolower( $cat ) );
			$results = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->terms SET name = %s, slug = %s WHERE term_id = 1", $cat, $slug ) );
		}
		if ( isset( $options['default_link_cat'] ) && strlen( $options['default_link_cat'] ) > 0 ) {
			global $wpdb;
			$cat = $options['default_link_cat'];
			$slug = str_replace( ' ', '-', strtolower( $cat ) );
			$results = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->terms SET name = %s, slug = %s WHERE term_id = 2", $cat, $slug ) );
		}
		if ( isset( $options['delete_first_post'] ) && $options['delete_first_post'] == 1 ) {
			global $wpdb;
			$statement = "UPDATE $wpdb->posts SET post_status = 'draft' WHERE id = 1";
			$results = $wpdb->query( $statement );
		}
		if ( isset( $options['delete_first_comment'] ) && $options['delete_first_comment'] == 1 ) {
			wp_delete_comment( 1 );
		}
		if ( isset( $options['delete_blogroll_links'] ) && $options['delete_blogroll_links'] == 1 ) {
			wp_delete_link( 1 ); //delete Wordpress.com blogroll link
			wp_delete_link( 2 ); //delete Wordpress.org blogroll link
		}
		if ( isset( $options['redirectblog'] ) && $options['redirectblog'] == 2 ) {
			$blog_page = array(
				'comment_status' => 'closed', // 'closed' means no comments.
				'ping_status' => 'closed', // 'closed' means pingbacks or trackbacks turned off.
				'post_status' => 'publish', // Set the status of the new post.
				'post_name' => $options['pageslug'], // The name (slug) for your post.
				'post_title' => $options['pagetitle'], // The title of your post.
				'post_type' => 'page', // Sometimes you want to post a page.
				'post_content' => __( '<p><strong>This page has been created automatically by the BuddyPress GroupBlog plugin.</strong></p><p>Please contact the site admin if you see this message instead of your blog posts. Possible solution: please advise your site admin to create the <a href="https://wordpress.org/support/article/pages/">page template</a> needed for the BuddyPress GroupBlog plugin.<p>', 'bp-groupblog' ), // The full text of the post.
			);
			$blog_page_id = wp_insert_post( $blog_page );

			if ( $blog_page_id ) {
				add_post_meta( $blog_page_id, '_wp_page_template', 'blog.php' );
			}
			add_post_meta( $blog_page_id, 'created_by_groupblog_dont_change', '1' );

			// Set the Blog Reading Settings to load the template page as front page.
			if ( isset( $options['deep_group_integration'] ) && $options['deep_group_integration'] == 1 ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $blog_page_id );
			}
		}

		restore_current_blog();

	}
}

/**
 * Updates the plugin defaults.
 *
 * @since 1.0
 */
function bp_groupblog_update_defaults() {

	// Retrieve the old landing page slug so we know which pages to delete.
	$oldoptions = get_site_option( 'bp_groupblog_blog_defaults_options' );

	// Create an array to hold the chosen options.
	$newoptions = array();
	$newoptions['theme'] = $_POST['theme'];

	// Groupblog validation settings.
	$newoptions['allowdashes']      = ! empty( $_POST['bp_groupblog_allowdashes'] ) ? 1 : 0;
	$newoptions['allowunderscores'] = ! empty( $_POST['bp_groupblog_allowunderscores'] ) ? 1 : 0;
	$newoptions['allownumeric']     = ! empty( $_POST['bp_groupblog_allownumeric'] ) ? 1 : 0;
	$newoptions['minlength']        = isset( $_POST['bp_groupblog_minlength'] ) && is_numeric( $_POST['bp_groupblog_minlength'] ) == true ? $_POST['bp_groupblog_minlength'] : 4;

	// Groupblog default settings.
	$newoptions['default_cat_name'] = isset( $_POST['default_cat_name'] ) ? $_POST['default_cat_name'] : '';
	$newoptions['default_link_cat'] = isset( $_POST['default_link_cat'] ) ? $_POST['default_link_cat'] : '';

	if ( ! empty( $_POST['delete_first_post'] ) ) {
		$newoptions['delete_first_post'] = 1;
	} else {
		$newoptions['delete_first_post'] = 0;
	}

	if ( ! empty( $_POST['delete_first_comment'] ) ) {
		$newoptions['delete_first_comment'] = 1;
	} else {
		$newoptions['delete_first_comment'] = 0;
	}

	if ( ! empty( $_POST['delete_blogroll_links'] ) ) {
		$newoptions['delete_blogroll_links'] = 1;
	} else {
		$newoptions['delete_blogroll_links'] = 0;
	}

	// Groupblog layout settings.
	if ( ! empty( $_POST['group_admin_layout'] ) ) {
		$newoptions['group_admin_layout'] = 1;
	} else {
		$newoptions['group_admin_layout'] = 0;
	}

	// Redirect group home to blog home.
	if ( ! empty( $_POST['deep_group_integration'] ) ) {
		$newoptions['deep_group_integration'] = 1;
	} else {
		$newoptions['deep_group_integration'] = 0;
	}

	// Groupblog redirect option.
	$newoptions['redirectblog']         = isset( $_POST['bp_groupblog_redirect_blog'] ) ? $_POST['bp_groupblog_redirect_blog'] : '';
	$newoptions['pagetitle']            = isset( $_POST['bp_groupblog_page_title'] ) ? $_POST['bp_groupblog_page_title'] : __( 'Blog', 'bp-groupblog' );
	$newoptions['pageslug']             = isset( $_POST['bp_groupblog_page_title'] ) ? sanitize_title( $_POST['bp_groupblog_page_title'] ) : '';
	$newoptions['page_template_layout'] = isset( $_POST['page_template_layout'] ) ? $_POST['page_template_layout'] : 'magazine';

	$newoptions['rerun'] = 0;

	if ( ( $newoptions['redirectblog'] == 2 ) ) {

		if ( bp_has_groups() ) :
			while ( bp_groups() ) :
				bp_the_group();
				if ( $blog_id = get_groupblog_blog_id( bp_get_group_id() ) ) {
					switch_to_blog( $blog_id );
					$change_front = new WP_Query( 'pagename=' . $newoptions['pageslug'] );
					if ( $change_front->have_posts() ) :
						while ( $change_front->have_posts() ) :
							$change_front->the_post();
							$blog_page_id = get_the_ID();
						endwhile;
					endif;
					if ( $newoptions['deep_group_integration'] == 1 ) {
						$page_or_posts = 'page';
						update_option( 'page_on_front', $blog_page_id );
					} else {
						$page_or_posts = 'posts';
					}
					update_option( 'show_on_front', $page_or_posts );
				}
			endwhile;
		endif;

		update_site_option( 'bp_groupblog_blog_defaults_options', $newoptions );

		$get_out = false;

		if ( $newoptions['redirectblog'] != 2 ) {
			$get_out = true;
		}

		if ( ( $oldoptions['pageslug'] == $newoptions['pageslug'] ) && ( $oldoptions['redirectblog'] == 2 ) ) {
			$get_out = true;
		}

		if ( $get_out && ( $oldoptions['rerun'] == 0 ) ) {
			return false;
		}

		echo '<div id="message" class="updated fade">';
		echo '<p><strong>The following blogs were updated</strong></p>';

		$exists_in = array();
		$updated_blogs = array();
		if ( bp_has_groups() ) :
			while ( bp_groups() ) :
				bp_the_group();
				if ( $blog_id = get_groupblog_blog_id( bp_get_group_id() ) ) {
					switch_to_blog( $blog_id );
					$create = new WP_Query( 'pagename=' . $newoptions['pageslug'] );

					if ( $create->have_posts() ) {
						$get_lost = 1;
						while ( $create->have_posts() ) :
							$create->the_post();
							if ( ! get_post_meta( get_the_ID(), 'created_by_groupblog_dont_change' ) ) {
								$exists_in[] = get_bloginfo( 'name' );
								$page_found = 1;
								$newoptions['rerun'] = 1;
							}
						endwhile;
					} else {

						if ( ! $get_lost ) {
							$blog_page = array(
								'comment_status' => 'closed', // 'closed' means no comments.
								'ping_status' => 'closed', // 'closed' means pingbacks or trackbacks turned off.
								'post_status' => 'publish', // Set the status of the new post.
								'post_name' => $newoptions['pageslug'], // The name (slug) for your post.
								'post_title' => $newoptions['pagetitle'], // The title of your post.
								'post_type' => 'page', // Sometimes you want to post a page.
								'post_content' => __( '<p><strong>This page has been created automatically by the BuddyPress GroupBlog plugin.</strong></p><p>Please contact the site admin if you see this message instead of your blog posts. Possible solution: please advise your site admin to create the <a href="https://wordpress.org/support/article/pages/">page template</a> needed for the BuddyPress GroupBlog plugin.<p>', 'bp-groupblog' ), // The full text of the post.
							);
							$blog_page_id = wp_insert_post( $blog_page );

							if ( $blog_page_id ) {
								add_post_meta( $blog_page_id, '_wp_page_template', 'blog.php' );
								// Add a special meta key so if we have to clean it up later we know the difference between pages
								// created by us and ones created by the user so we don't delete their pages.
								add_post_meta( $blog_page_id, 'created_by_groupblog_dont_change', '1' );
								$updated_blogs[] = get_bloginfo( 'name' );
							}
						}

						// Find the page created previously and delete it, checking first to see if it was one we created or not.
						if ( $oldoptions['pageslug'] != $newoptions['pageslug'] ) {
							$cleanup = new WP_Query( 'pagename=' . $oldoptions['pageslug'] );

							if ( $cleanup->have_posts() ) :
								while ( $cleanup->have_posts() ) :
								$cleanup->the_post();
									if ( get_post_meta( get_the_ID(), 'created_by_groupblog_dont_change' ) ) {
										wp_delete_post( get_the_ID(), $force_delete = true );
									}
								endwhile;
							endif; //cleanup
						}
					}
				}
				$get_lost = 0;

			endwhile;
		endif;

		foreach ( $updated_blogs as $blog ) {
			echo '<p>- ' . $blog . '</p>';
		}

		if ( $page_found ) {
			echo '<div class="error">';
			echo '<p style="line-height: 16px;"><strong>We skipped the following blogs</strong></p>';
			foreach ( $exists_in as $blog ) {
				echo '<p>- ' . $blog . '</p>';
			}
			echo '<p><em>These blogs already had a page named <strong>"' . $newoptions['pagetitle'] . '"</strong> which was not created by us. Please check and delete that page permanently after which you should return here and click save once more to finalize the process. Alternatively you can choose another template page name.</em></p></div>';
		}

		echo '</div>';

	} elseif ( $newoptions['redirectblog'] != 2 ) {
		if ( bp_has_groups() ) :
			while ( bp_groups() ) :
				bp_the_group();
				if ( $blog_id = get_groupblog_blog_id( bp_get_group_id() ) ) {
					switch_to_blog( $blog_id );
					update_option( 'show_on_front', 'posts' );
				}
			endwhile;
		endif;
	}

	// Override the site option.
	update_site_option( 'bp_groupblog_blog_defaults_options', $newoptions );

	$options = get_site_option( 'bp_groupblog_blog_defaults_options' );
}

/**
 * Adds the admin menu item.
 *
 * @since 1.4
 */
function bp_groupblog_add_admin_menu() {
	global $wpdb, $bp;

	if ( ! is_super_admin() ) {
		return false;
	}

	// Test for BP1.6+. Truncated to allow testing on beta versions.
	if ( version_compare( substr( BP_VERSION, 0, 3 ), '1.6', '>=' ) ) {

		// BuddyPress 1.6 moves its admin pages elsewhere, so use Settings menu.
		$location = 'settings.php';

	} else {

		// Versions prior to 1.6 have a BuddyPress top-level menu.
		$location = 'bp-general-settings';

	}

	// Add the administration tab under the "Site Admin" tab for site administrators.
	$page = add_submenu_page(
		$location,
		__( 'GroupBlog Setup', 'bp-groupblog' ),
		'<span class="bp-groupblog-admin-menu-header">' . __( 'GroupBlog Setup', 'bp-groupblog' ) . '&nbsp;&nbsp;&nbsp;</span>',
		'manage_options',
		'bp_groupblog_management_page',
		'bp_groupblog_management_page'
	);

	/*
	 * Add styles only on bp-groupblog admin page
	 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/#Load_scripts_only_on_plugin_pages
	 */
	add_action( 'admin_print_styles-' . $page, 'bp_groupblog_add_admin_style' );

}

add_action( bp_core_admin_hook(), 'bp_groupblog_add_admin_menu', 10 );

/**
 * Echoes the admin page.
 *
 * @since 1.4
 */
function bp_groupblog_management_page() {
	global $wpdb;

	// Only allow site admins to come here.
	if ( is_super_admin() == false ) {
		wp_die( __( 'You do not have permission to access this page.', 'bp-groupblog' ) );
	}

	// Process form submission.
	if ( isset( $_POST['action'] ) && $_POST['action'] == 'update' ) {
		bp_groupblog_update_defaults();
		$updated = true;
	} else {
		$updated = false;
	}

	// Make sure we're using latest data.
	$opt = get_site_option( 'bp_groupblog_blog_defaults_options' );
	?>

	<?php if ( $updated ) { ?>
		<div id="message" class="updated fade">
			<p><?php esc_html_e( 'Options saved.', 'bp-groupblog' ); ?></p>
		</div>
	<?php } ?>

	<div class="wrap" style="position: relative">
		<h2><?php esc_html_e( 'BuddyPress GroupBlog Settings', 'bp-groupblog' ); ?></h2>

		<form name="bp-groupblog-setup" id="bp-groupblog-setup" action="" method="post">

			<div id="tabctnr">
				<ul class="tabnav">
					<li><a href="#groupblog_default_theme"><?php esc_html_e( 'Theme', 'bp-groupblog' ); ?></a></li>
					<li><a href="#groupblog_landing_page"><?php esc_html_e( 'Redirect', 'bp-groupblog' ); ?></a></li>
					<li><a href="#groupblog_template_layout"><?php esc_html_e( 'Layout', 'bp-groupblog' ); ?></a></li>
					<li><a href="#groupblog_default_blog_settings"><?php esc_html_e( 'Defaults', 'bp-groupblog' ); ?></a></li>
					<li><a href="#groupblog_validation_settings"><?php esc_html_e( 'Validation', 'bp-groupblog' ); ?></a></li>
					<li><a href="#groupblog_about"><?php esc_html_e( 'About', 'bp-groupblog' ); ?></a></li>
				</ul>

				<div id='groupblog_default_theme'>
					<?php

					$current_groupblog_theme = '';

					// Get all themes.
					if ( function_exists( 'wp_get_themes' ) ) {

						// Get theme data the WP3.4 way.
						$themes = wp_get_themes(
							false,     // Only error-free themes.
							'network', // Only network-allowed themes.
							0          // Use current blog as reference.
						);

						$ct = wp_get_theme();
						$allowed_themes = WP_Theme::get_allowed_on_network();
						$blog_allowed_themes = WP_Theme::get_allowed_on_site();

					} else {

						// Pre WP3.4 functions.
						$themes = get_themes();

						$ct = current_theme_info();
						$allowed_themes = get_site_allowed_themes();
						$blog_allowed_themes = wpmu_get_blog_allowedthemes();

					}

					if ( $allowed_themes == false ) {
						$allowed_themes = array();
					}

					if ( is_array( $blog_allowed_themes ) ) {
						$allowed_themes = array_merge( $allowed_themes, $blog_allowed_themes );
					}

					if ( $wpdb->blogid != 1 ) {
						unset( $allowed_themes['h3'] );
					}

					if ( isset( $allowed_themes[ esc_html( $ct->stylesheet ) ] ) == false ) {
						$allowed_themes[ esc_html( $ct->stylesheet ) ] = true;
					}

					reset( $themes );
					foreach ( $themes as $key => $theme ) {
						if ( isset( $allowed_themes[ esc_html( $theme['Stylesheet'] ) ] ) == false ) {
							unset( $themes[ $key ] );
						}
					}
					reset( $themes );

					/*
					 * Get the names of the themes & sort them.
					 *
					 * Note: pre-WP3.4 the keys are the theme names. In 3.4, the keys are folder names
					 * Fortunately, the magic methods of the object retain backwards compatibility and allow
					 * array-style access to work
					 */
					$theme_names = array_keys( $themes );
					natcasesort( $theme_names );
					?>

					<h3><?php esc_html_e( 'Default Theme', 'bp-groupblog' ); ?></h3>

					<div id="select-theme">
						<label for="theme"><?php esc_html_e( 'Select the default theme for new groupblogs:', 'bp-groupblog' ); ?></label>
						<select id="theme" name="theme" size="1">
							<optgroup label="<?php echo esc_attr( __( 'GroupBlog Themes:', 'bp-groupblog' ) ); ?>">
								<?php

								$groupblog_themes_options = '';

								foreach ( $theme_names as $theme_name ) {

									if ( in_array( 'groupblog', (array) $themes[ $theme_name ]['Tags'] ) ) {

										$template = $themes[ $theme_name ]['Template'];
										$stylesheet = $themes[ $theme_name ]['Stylesheet'];
										$title = $themes[ $theme_name ]['Title'];
										$selected = '';
										if ( isset( $opt['theme'] ) && $opt['theme'] == $template . '|' . $stylesheet ) {
											$selected = "selected = 'selected' ";
											$current_groupblog_theme = $theme_name;
										}
										$groupblog_themes_options .= '<option value="' . $template . '|' . $stylesheet . '"' . $selected . '>' . $title . '</option>';
									}
								}

								if ( ! empty( $groupblog_themes_options ) ) {
									echo $groupblog_themes_options;
								} else {
									echo '<option value="" disabled="disabled">' . __( 'No groupblog-enabled themes available', 'bp-groupblog' ) . '</option>';
								}

								?>
							</optgroup>
							<optgroup label="<?php echo esc_attr( __( 'Regular Themes:', 'bp-groupblog' ) ); ?>">
								<?php

								$non_groupblog_themes_options = '';

								foreach ( $theme_names as $theme_name ) {

									if ( ! in_array( 'groupblog', (array) $themes[ $theme_name ]['Tags'] ) ) {

										$template = $themes[ $theme_name ]['Template'];
										$stylesheet = $themes[ $theme_name ]['Stylesheet'];
										$title = $themes[ $theme_name ]['Title'];
										$selected = '';
										if ( isset( $opt['theme'] ) && $opt['theme'] == $template . '|' . $stylesheet ) {
											$selected = "selected = 'selected' ";
											$current_groupblog_theme = $theme_name;
										}
										$non_groupblog_themes_options .= '<option value="' . $template . '|' . $stylesheet . '"' . $selected . '>' . $title . '</option>';
									}
								}

								if ( ! empty( $non_groupblog_themes_options ) ) {
									echo $non_groupblog_themes_options;
								} else {
									echo '<option value="" disabled="disabled">' . __( 'No regular themes available', 'bp-groupblog' ) . '</option>';
								}

								?>
							</optgroup>

							<option value="" <?php selected( $current_groupblog_theme, '' ); ?>><?php esc_html_e( '- None selected -', 'bp-groupblog' ); ?></option>

						</select>
					</div>

					<?php if ( ! empty( $current_groupblog_theme ) ) : ?>

						<?php

						// Set a class for WP3.4+ which has bigger screenshots.
						$wp3point4class = '';
						if ( function_exists( 'wp_get_themes' ) ) {
							$wp3point4class = 'current-theme-3point4plus';
						}

						// Not all themes have screenshots.
						$theme_has_screenshot = false;
						if (
							isset( $themes[ $current_groupblog_theme ]['Screenshot'] ) &&
							$themes[ $current_groupblog_theme ]['Screenshot'] != ''
						) {
							$theme_has_screenshot = true;
						}

						// Add class to container if theme has screenshot.
						if ( $theme_has_screenshot ) {
							$wp3point4class .= ' current-theme-has-screenshot';
						}

						// Construct attribute.
						$wp3point4classes = '';
						if ( $wp3point4class != '' ) {
							$wp3point4classes = ' class="' . $wp3point4class . '"';
						}

						?>

						<div id="current-theme"<?php echo $wp3point4classes; ?>>
							<?php if ( $theme_has_screenshot ) : ?>
								<img src="<?php echo esc_url( $themes[ $current_groupblog_theme ]['Theme Root URI'] . '/' . $themes[ $current_groupblog_theme ]['Stylesheet'] . '/' . $themes[ $current_groupblog_theme ]['Screenshot'] ); ?>" alt="<?php esc_attr_e( 'Current theme preview', 'bp-groupblog' ); ?>" />
							<?php endif; ?>

							<div class="alt" id="current-theme-info">
								<h4>
								<?php
								/* translators: 1: theme title, 2: theme version, 3: theme author */
								printf( __( '%1$s %2$s by %3$s', 'bp-groupblog' ), $themes[ $current_groupblog_theme ]['Title'], $themes[ $current_groupblog_theme ]['Version'], $themes[ $current_groupblog_theme ]['Author'] );
								?>
								</h4>
								<p class="theme-description"><?php /* print_r( $themes[ $current_groupblog_theme ] ); */ echo $themes[ $current_groupblog_theme ]['Description']; ?></p>
							</div>
						</div>

					<?php endif; ?>

					<div class="clear"></div>

				</div>
				<div id='groupblog_landing_page'>

					<h3><?php esc_html_e( 'Default Landing Page', 'bp-groupblog' ); ?></h3>

					<p><?php esc_html_e( 'The page that is linked to from the "Blog" tab of the Group navigation. Selecting "Disabled" will use the buddypress template included in the plugin, no redirect will take place. The "Home Page" setting will create a redirect to the blog front page. The "Template Page" setting will create a redirect to the blog template page, additionally when using this setting you can choose a specific page template layout in the next tab.', 'bp-groupblog' ); ?></p>
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php esc_html_e( 'Redirect Enabled to:', 'bp-groupblog' ); ?></th>
								<td>
									<label><input class="info-off" name="bp_groupblog_redirect_blog" id="bp_groupblog_redirect_blog" value="0" type="radio" <?php if ( isset( $opt['redirectblog'] ) && $opt['redirectblog'] == 0 ) { echo 'checked="checked"';} ?>> <?php esc_html_e( 'Disabled', 'bp-groupblog' ); ?></label>
								</td>
							</tr>
							<tr>
								<th></th>
								<td>
									<label><input class="info-off" name="bp_groupblog_redirect_blog" id="bp_groupblog_redirect_blog" value="1" type="radio" <?php if ( isset( $opt['redirectblog'] ) && $opt['redirectblog'] == 1 ) { echo 'checked="checked"';} ?>> <?php esc_html_e( 'Home Page', 'bp-groupblog' ); ?></label>
								</td>
							<tr>
								<th></th>
								<td>
									<label><input class="info-on" name="bp_groupblog_redirect_blog" id="bp_groupblog_redirect_blog" value="2" type="radio"<?php if ( isset( $opt['redirectblog'] ) && $opt['redirectblog'] == 2 ) { echo 'checked="checked"'; } ?>> <?php esc_html_e( 'Page Template Title: ', 'bp-groupblog' ); ?></label>
									<input name="bp_groupblog_page_title" id="bp_groupblog_page_title" value="<?php echo ( isset( $opt['pagetitle'] ) ? $opt['pagetitle'] : '' ); ?>" size="10" type="text" />
									<span class="notice" id="redirect_notice" style="display:none;"> <?php esc_html_e( 'All existing Group Blogs will be automatically updated on each change.', 'bp-groupblog' ); ?></span>
									<p class="info"><?php _e( 'The "Template Page" option will create a page on group blogs and links to a template file within your theme. Don\'t worry about the name you choose, we\'ll make sure your page finds it way to the template file. For custom themes make sure to <a href="https://wordpress.org/support/article/pages/">create</a> this template file manually.', 'bp-groupblog' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Redirect Group Home:', 'bp-groupblog' ); ?></th>
								<td>
									<label for="deep_group_integration"><input name="deep_group_integration" type="checkbox" id="deep_group_integration" value="1" <?php if ( isset( $opt['deep_group_integration'] ) && $opt['deep_group_integration'] == 1 ) { echo 'checked="checked"';} ?> /> <?php esc_html_e( 'Yes, redirect Group Home to Blog Home', 'bp-groupblog' ); ?></label><p><?php esc_html_e( 'This option will take control of the GROUP home page and redirects it to the BLOG home page. This will enable posting from the group Home using P2 instead of the BuddyPress form.', 'bp-groupblog' ); ?></p>
								</td>
							</tr>
						</tbody>
					</table>

				</div>
				<div id='groupblog_template_layout'>
					<?php if ( isset( $opt['theme'] ) && $opt['theme'] == 'p2|p2-buddypress' ) { ?>

						<h3><?php esc_html_e( 'Template Page Layout', 'bp-groupblog' ); ?></h3>

						<p class="disabled"><?php esc_html_e( 'Please select the option "Template Page" on the Redirect tab in order to choose a layout.', 'bp-groupblog' ); ?></p>

						<p class="enabled"><?php esc_html_e( 'Please select a Layout which you would like to use for your Group Blog. Additionally, incombination with "Redirect Group Home" setting you can set this as your Group Home page.', 'bp-groupblog' ); ?></p>

						<table class="enabled" id="availablethemes" cellspacing="0" cellpadding="0">
							<tbody>
								<tr class="alt">
									<td class="available-theme top left">
										<?php echo '<img src="' . WP_PLUGIN_URL . '/bp-groupblog/inc/i/screenshot-mag.png">'; ?>
										<div class="clear"></div>
										<input name="page_template_layout" id="page_template_layout" value="magazine" type="radio" <?php if ( isset( $opt['page_template_layout'] ) && $opt['page_template_layout'] == 'magazine' ) { echo 'checked="checked"'; } ?> /><h3 style="display:inline;"> <?php esc_html_e( 'Magazine', 'bp-groupblog' ); ?></h3>
										<p class="description"><?php esc_html_e( 'Balanced template for groups with diverse postings.', 'bp-groupblog' ); ?></p>
									</td>
									<td class="available-theme top">
										<?php echo '<img src="' . WP_PLUGIN_URL . '/bp-groupblog/inc/i/screenshot-micro.png">'; ?>
										<div class="clear"></div>
										<input name="page_template_layout" id="page_template_layout" value="microblog" type="radio" <?php if ( isset( $opt['page_template_layout'] ) && $opt['page_template_layout'] == 'microblog' ) { echo 'checked="checked"'; } ?> /><h3 style="display:inline;"> <?php esc_html_e( 'Microblog', 'bp-groupblog' ); ?></h3>
										<p class="description"><?php esc_html_e( 'Great for simple listing of posts in a chronological order.', 'bp-groupblog' ); ?></p>
									</td>
								</tr>
							</tbody>
						</table>

						<table class="form-table enabled">
							<tbody>
								<tr>
									<th><?php esc_html_e( 'Group admin layout control:', 'bp-groupblog' ); ?></th>
									<td>
										<label for="group_admin_layout"><input name="group_admin_layout" type="checkbox" id="group_admin_layout" value="1" <?php if ( isset( $opt['group_admin_layout'] ) && $opt['group_admin_layout'] == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Allow group admins to select the layout for their group themselves.', 'bp-groupblog' ); ?></label>
									</td>
								</tr>
							</tbody>
						</table>
					<?php } else { ?>
						<h3><?php esc_html_e( 'Template Page Layout', 'bp-groupblog' ); ?></h3>

						<p><?php esc_html_e( 'Layout options are only available for the "P2 BuddyPress" Theme. Please select the "P2 Buddypress" theme on the "Theme" tab in order to choose a layout. Additionally the Redirect option needs to be set to "Template Page".', 'bp-groupblog' ); ?></p>
					<?php } ?>
				</div>
				<div id='groupblog_default_blog_settings'>

					<h3><?php esc_html_e( 'Default Blog Settings', 'bp-groupblog' ); ?></h3>

					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th><?php esc_html_e( 'Default Post Category:', 'bp-groupblog' ); ?></th>
								<td>
									<input name="default_cat_name" type="text" id="default_cat_name" size="30" value="<?php echo ( isset( $opt['default_cat_name'] ) ? $opt['default_cat_name'] : '' ); ?>" /> <?php esc_html_e( '(Overwrites "Uncategorized")', 'bp-groupblog' ); ?>
								</td>
							</tr>
							<tr valign="top">
								<th><?php esc_html_e( 'Default Link Category:', 'bp-groupblog' ); ?></th>
								<td>
									<input name="default_link_cat" type="text" id="default_link_cat" size="30" value="<?php echo ( isset( $opt['default_link_cat'] ) ? $opt['default_link_cat'] : '' ); ?>" /> <?php esc_html_e( '(Overwrites "Blogroll")', 'bp-groupblog' ); ?>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Set First Post to Draft', 'bp-groupblog' ); ?></th>
								<td>
									<label for="delete_first_post">
										<input name="delete_first_post" type="checkbox" id="delete_first_post" value="1" <?php if ( isset( $opt['delete_first_post'] ) && $opt['delete_first_post'] == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Yes', 'bp-groupblog' ); ?> <?php esc_html_e( '(Default Post "Hello World")', 'bp-groupblog' ); ?>
									</label>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Delete Initial Comment', 'bp-groupblog' ); ?></th>
								<td>
									<label for="delete_first_comment">
										<input name="delete_first_comment" type="checkbox" id="delete_first_comment" value="1" <?php if ( isset( $opt['delete_first_comment'] ) && $opt['delete_first_comment'] == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Yes', 'bp-groupblog' ); ?>
									</label>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Delete Blogroll Links', 'bp-groupblog' ); ?></th>
								<td>
									<label for="delete_blogroll_links">
										<input name="delete_blogroll_links" type="checkbox" id="delete_blogroll_links" value="1" <?php if ( isset( $opt['delete_blogroll_links'] ) && $opt['delete_blogroll_links'] == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Yes', 'bp-groupblog' ); ?>
									</label>
								</td>
							</tr>
						</tbody>
					</table>

				</div>
				<div id='groupblog_validation_settings'>

					<h3><?php esc_html_e( 'Validation Settings', 'bp-groupblog' ); ?></h3>

					<div><?php esc_html_e( 'Change the default WordPress blog validation settings.', 'bp-groupblog' ); ?></div>
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php esc_html_e( 'Allow:', 'bp-groupblog' ); ?></th>
								<td>
									<label for="bp_groupblog_allowdashes">
										<input name="bp_groupblog_allowdashes" type="checkbox" id="bp_groupblog_allowdashes" value="1" <?php if ( isset( $opt['allowdashes'] ) && $opt['allowdashes'] == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Dashes', 'bp-groupblog' ); ?> <?php esc_html_e( '(Default: Not Allowed)', 'bp-groupblog' ); ?>
									</label>
								</td>
							</tr>
							<tr>
								<th></th>
								<td>
									<label for="bp_groupblog_allowunderscores">
										<input name="bp_groupblog_allowunderscores" type="checkbox" id="bp_groupblog_allowunderscores" value="1" <?php if ( isset( $opt['allowunderscores'] ) && $opt['allowunderscores'] == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Underscores', 'bp-groupblog' ); ?> <?php esc_html_e( '(Default: Not Allowed)', 'bp-groupblog' ); ?>
									</label>
								</td>
							</tr>
							<tr>
								<th></th>
								<td>
									<label for="bp_groupblog_allownumeric">
										<input name="bp_groupblog_allownumeric" type="checkbox" id="bp_groupblog_allownumeric" value="1" <?php if ( isset( $opt['allownumeric'] ) && $opt['allownumeric'] == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'All Numeric Names', 'bp-groupblog' ); ?> <?php esc_html_e( '(Default: Not Allowed)', 'bp-groupblog' ); ?>
									</label>
								</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Minimum Length:', 'bp-groupblog' ); ?></th>
								<td>
									<input name="bp_groupblog_minlength" style="width: 10%;" id="bp_groupblog_minlenth" value="<?php echo ( isset( $opt['minlength'] ) ? $opt['minlength'] : '' ); ?>" size="10" type="text" /> <?php esc_html_e( '(Default: 4 Characters)', 'bp-groupblog' ); ?>
								</td>
							</tr>
						</tbody>
					</table>

				</div>
				<div id='groupblog_about'>

					<h3><?php esc_html_e( 'About This Plugin', 'bp-groupblog' ); ?></h3>

					<div>
						<span class="indent"><strong><?php esc_html_e( 'Authors', 'bp-groupblog' ); ?></strong></span>
						<span><a href="http://oomsonline.com">Marius Ooms</a> & <a href="http://blevins.nl">Rodney Blevins</a></span>
					</div>
					<div>
						<span class="indent"><strong><?php esc_html_e( 'Donate', 'bp-groupblog' ); ?></strong></span>
						<span><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7374704"><?php esc_html_e( 'PayPal', 'bp-groupblog' ); ?></a></span>
					</div>
					<div>
						<span class="indent"><strong><?php esc_html_e( 'Support', 'bp-groupblog' ); ?></strong></span>
						<span><a href="https://buddypress.org/support/"><?php esc_html_e( 'BuddyPress Forums', 'bp-groupblog' ); ?></a> |
						<a href="https://wordpress.org/support/plugin/bp-groupblog/"><?php esc_html_e( 'WordPress Forums', 'bp-groupblog' ); ?></a></span>
					</div>
					<div>
						<span class="indent"><strong><?php esc_html_e( 'Trac', 'bp-groupblog' ); ?></strong></span>
						<span><a href="https://plugins.trac.wordpress.org/log/bp-groupblog"><?php esc_html_e( 'Revision Log', 'bp-groupblog' ); ?></a> | <a href="https://plugins.trac.wordpress.org/browser/bp-groupblog/"><?php esc_html_e( 'Trac Browser', 'bp-groupblog' ); ?></a></span>
					</div>
					<div>
						<span class="indent"><strong><?php esc_html_e( 'Rate', 'bp-groupblog' ); ?></strong></span>
						<span><a href="https://wordpress.org/plugins/bp-groupblog/"><?php esc_html_e( 'Let everyone know! Only if you like it :)', 'bp-groupblog' ); ?></a></span>
					</div>
					<hr />
					<div>
						<span class="indent"><strong><?php esc_html_e( 'Acknowledgement', 'bp-groupblog' ); ?></strong></span>
						<span><?php esc_html_e( 'Thanks goes out to the following people:', 'bp-groupblog' ); ?></span>
						<ul id="acknowledge">
							<li><a href="http://buddypress.org/developers/apeatling/">Andy Peatling</a></li>
							<li>Thijs Huijssoon</li>
							<li><a href="http://deannaschneider.wordpress.com/">Deanna Schneider</a></li>
							<li><a href="http://buddypress.org/developers/boonebgorges/">Boone Gorges</a></li>
							<li><a href="http://wordpress.org/support/profile/5499080">Luiz Armesto</a></li>
							<li><a href="http://buddypress.org/developers/burtadsit/">Burt Adsit</a></li>
						</ul>
					</div>

				</div>

			</div>

			<p class="submit">
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'bp-groupblog' ); ?>" />
			</p>

		</form>

	</div>

	<?php

}

// When a new blog is created, set the options.
if ( version_compare( $GLOBALS['wp_version'], '5.1.0', '>=' ) ) {
	add_action( 'wp_initialize_site', 'bp_groupblog_site_defaults', 10, 2 );
} else {
	add_action( 'wpmu_new_blog', 'bp_groupblog_blog_defaults' );
}
