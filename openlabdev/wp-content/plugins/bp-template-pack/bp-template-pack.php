<?php

function bp_tpack_deactivate() {
	/* Cleanup */
	delete_option( 'bp_tpack_disable_js' );
	delete_option( 'bp_tpack_disable_css' );
	delete_option( 'bp_tpack_configured' );
}
register_deactivation_hook( __FILE__, 'bp_tpack_deactivate' );

function bp_tpack_init() {
	global $wp_themes;

	/* Check to make sure the active theme is not bp-default */
	if ( 'bp-default' == get_option( 'template' ) )
		return false;

	/* Load the default BuddyPress AJAX functions */
	if ( !(int)get_option( 'bp_tpack_disable_js' ) ) {
		require_once( BP_PLUGIN_DIR . '/bp-themes/bp-default/_inc/ajax.php' );

		/* Load the default BuddyPress javascript */
		wp_enqueue_script( 'bp-js', BP_PLUGIN_URL . '/bp-themes/bp-default/_inc/global.js', array( 'jquery' ) );
		
		// Add words that we need to use in JS to the end of the page so they can be 
		// translated and still used.
		$params = array(
			'my_favs'           => __( 'My Favorites', 'buddypress' ),
			'accepted'          => __( 'Accepted', 'buddypress' ),
			'rejected'          => __( 'Rejected', 'buddypress' ),
			'show_all_comments' => __( 'Show all comments for this thread', 'buddypress' ),
			'show_all'          => __( 'Show all', 'buddypress' ),
			'comments'          => __( 'comments', 'buddypress' ),
			'close'             => __( 'Close', 'buddypress' ),
			'mention_explain'   => sprintf( __( "%s is a unique identifier for %s that you can type into any message on this site. %s will be sent a notification and a link to your message any time you use it.", 'buddypress' ), '@' . bp_get_displayed_user_username(), bp_get_user_firstname( bp_get_displayed_user_fullname() ), bp_get_user_firstname( bp_get_displayed_user_fullname() ) )
		);
	
		wp_localize_script( 'bp-js', 'BP_DTheme', $params );
	}

	/* Add the wireframe BP page styles */
	if ( !(int)get_option( 'bp_tpack_disable_css' ) )
		wp_enqueue_style( 'bp-css', plugins_url( $path = basename( dirname( __FILE__ ) ) ) . '/bp.css' );
}
add_action( 'bp_init', 'bp_tpack_init' );

function bp_tpack_add_theme_menu() {
	add_theme_page( __( 'BP Compatibility', 'bp-template-pack' ), __( 'BP Compatibility', 'bp-template-pack' ), 'switch_themes', 'bp-tpack-options', 'bp_tpack_theme_menu' );
}
add_action( 'admin_menu', 'bp_tpack_add_theme_menu' );

function bp_tpack_admin_notices() {
	if ( isset( $_GET['page'] ) && 'bp-tpack-options' == $_GET['page'] )
		return;
		
	if ( !(int)get_option( 'bp_tpack_configured' ) ) {
		?>
		
		<div id="message" class="updated fade">
			<p>You have activated the BuddyPress Template Pack, but you haven't completed the setup process. Visit the <a href="<?php echo add_query_arg( 'page', 'bp-tpack-options', admin_url( 'themes.php' ) ) ?>">BP Compatibility</a> page to wrap up.</p>
		</div>
		
		<?php
	}
}
add_action( 'admin_notices', 'bp_tpack_admin_notices' );

function bp_tpack_theme_menu() {
	$theme_dir = WP_CONTENT_DIR . '/themes/' . get_option('stylesheet') . '/';

	if ( !empty( $_GET['finish'] ) )
		update_option( 'bp_tpack_configured', 1 );

	if ( !empty( $_GET['reset'] ) )
		delete_option( 'bp_tpack_configured' );

	if ( !file_exists( $theme_dir . 'activity' ) && !file_exists( $theme_dir . 'blogs' ) && !file_exists( $theme_dir . 'forums' ) && !file_exists( $theme_dir . 'groups' ) && !file_exists( $theme_dir . 'members' ) && !file_exists( $theme_dir . 'registration' ) ) {
		$step = 1;

		if ( !empty( $_GET['move'] ) ) {
			$step = 2;
			$error = false;

			/* Attempt to move the directories */
			if ( !bp_tpack_move_templates() )
				$error = true;
		}

		/* Make sure we reset if template files have been deleted. */
		delete_option( 'bp_tpack_configured' );
	} else
		$step = 3;

	if ( !empty( $_POST['bp_tpack_save'] ) ) {
		/* Save options */
		if ( !empty( $_POST['bp_tpack_disable_css'] ) )
			update_option( 'bp_tpack_disable_css', 1 );
		else
			delete_option( 'bp_tpack_disable_css' );

		if ( !empty( $_POST['bp_tpack_disable_js'] ) )
			update_option( 'bp_tpack_disable_js', 1 );
		else
			delete_option( 'bp_tpack_disable_js' );
	}

	if ( !(int)get_option( 'bp_tpack_configured' ) ) {
?>
	<div class="wrap">
		<h2>Making Your Theme BuddyPress Compatible</h2>

		<p>Adding support for BuddyPress to your existing WordPress theme is a straightforward process. Follow the setup instructions on this page.</p>

		<?php switch( $step ) {
			case 1: ?>

				<h2>Step One</h2>

				<p>BuddyPress needs some extra template files in order to display its pages correctly. This plugin will attempt to move the necessary files into your current theme. Click the button below to start the process.</p>

				<p><a class="button" href="?page=bp-tpack-options&move=1">Move Template Files</a></p>

			<?php break; ?>

		<?php case 2: ?>

				<h2>Step Two</h2>

				<?php if ( $error ) : ?>

					<p><strong>Moving templates failed.</strong> There was an error when trying to move the templates automatically. This probably means that we don't have the
					correct permissions. That's all right - it just means you'll have to move the template files manually.</p>

					<p>You will need to connect to your WordPress files using FTP. When you are connected browse to the following directory:<p>

					<p><code><?php echo dirname( __FILE__ ) . '/templates/' ?></code></p>

					<p>In this directory you will find six folders. If you want to use all of the features of BuddyPress then you must move all six directories to the following folder:</p>

					<p><code><?php echo $theme_dir ?></code></p>

					<p>If you decide that you don't want to use a feature of BuddyPress then you can actually ignore the template folders for these features. For example, if you don't want to use the groups and forums features, you can simply not copy the /groups/ and /forums/ template folders to your active theme. (If you're not sure what to do, just copy all six folders over to your theme directory.)</p>

					<p>Once you have correctly copied the folders into your active theme, please use the button below to move onto step three.</p>

					<p><a href="?page=bp-tpack-options" class="button">I've finished moving template folders</a></p>

				<?php else : ?>

					<p><strong>Templates moved successfully!</strong> Great news! BuddyPress templates are now in the correct position in your theme, which means you can skip step two and <a href="?page=bp-tpack-options">move on to step three</a>.</p>

				<?php endif; ?>

		<?php break; ?>
		<?php case 3: ?>
			<h2>Step Three</h2>

			<p>Now that the template files are in the correct location, click through your site (you can come back to this page at any point). You should see a BuddyPress admin bar at the top of the page. Try visiting some of the links in the "My Account" menu. You should find that BuddyPress pages now work and are displayed.</p>

			<p>If you find that the pages are not quite aligned correctly, or the content is overlapping the sidebar, you will need to tweak the template HTML. Please follow the "fixing alignment" instructions below. If the content in your pages is aligned in the correct place then you can skip to the "Finishing Up" section at the bottom of this page.</p>

			<h3>Fixing Alignment</h3>

			<p>By default BuddyPress templates use this HTML structure:</p>

<p><pre><code style="display: block; width: 40%; padding-left: 15px;">
[HEADER]

&lt;div id="container"&gt;
	&lt;div id="content"&gt;
		[PAGE CONTENT]
	&lt;/div&gt;

	&lt;div id="sidebar"&gt;
		[SIDEBAR CONTENT]
	&lt;/div&gt;
&lt;/div&gt;

[FOOTER]

</code></pre></p>

			<p>If BuddyPress pages are not aligned correctly, then you will need to modify some of the templates to match your theme's HTML structure. The best way to do this is to FTP to your theme's files at:</p>

			<p><code><?php echo $theme_dir ?></code></p>

			<p>Then open up the <code>page.php</code> file (if this does not exist use <code>index.php</code>). Make note of the HTML template structure of the file, specifically the <code>&lt;div&gt;</code> tags that surround the content and sidebar.</p>

			<p>You will need to change the HTML structure in the BuddyPress templates that you copied into your theme to match the structure in your <code>page.php</code> or <code>index.php</code> file. The files that you need to edit are as follows (leave out any folders you have not copied over in step two):</p>

			<ul style="list-style: disc; margin-left: 40px;">
				<li><?php echo '/activity/index.php' ?></li>
				<li><?php echo '/blogs/index.php' ?></li>
				<li><?php echo '/forums/index.php' ?></li>
				<li><?php echo '/groups/index.php' ?></li>
				<li><?php echo '/groups/create.php' ?></li>
				<li><?php echo '/groups/single/home.php' ?></li>
				<li><?php echo '/groups/single/plugins.php' ?></li>
				<li><?php echo '/members/index.php' ?></li>
				<li><?php echo '/members/single/home.php' ?></li>
				<li><?php echo '/members/single/plugins.php' ?></li>
				<li><?php echo '/registration/register.php' ?></li>

				<?php if ( is_multisite() ) : ?>
					<li><?php echo '/blogs/create.php' ?></li>
					<li><?php echo '/registration/activate.php' ?></li>
				<?php endif; ?>
			</ul>

			<p>Once you are done matching up the HTML structure of your theme in these template files, please take another look through your site. You should find that BuddyPress pages now fit inside the content structure of your theme.</p>

			<h3>Finishing Up</h3>

			<p>You're now all done with the conversion process. Your WordPress theme will now happily provide BuddyPress compatibility support. Once you hit the finish button you will be presented with a new permanent theme options page allowing you to tweak some settings.</p>

			<p><a href="?page=bp-tpack-options&finish=1" class="button-primary">Finish</a></p>
			<p>&nbsp;</p>

		<?php break;?>

		<?php } ?>
	</div>

<?php } else { // The theme steps have been completed, just show the permanent page ?>

	<div class="wrap">

		<h2>BuddyPress Theme Compatibility</h2>

		<?php if ( !empty( $_GET['finish'] ) ) : ?>
			<div id="message">
				<p><strong>Congratulations, you have completed the BuddyPress theme compatibility setup procedure!</strong></p>
			</div>
		<?php endif; ?>

		<form action="" name="bp-tpack-settings" method="post" style="width: 60%; float: left; margin-right: 3%;">

			<p><strong><input type="checkbox" name="bp_tpack_disable_css" value="1"<?php if ( (int)get_option( 'bp_tpack_disable_css' ) ) : ?> checked="checked"<?php endif; ?> /> Disable BP Template Pack CSS</strong></p>
			<p>
				<small style="display: block; margin-left:18px; font-size: 11px">The BuddyPress template pack comes with basic wireframe CSS styles that will format the layout of BuddyPress pages. You can
					extend upon these styles in your theme's CSS file, or simply turn them off and build your own styles.</small>
			</p>

			<p style="margin-top: 20px;"><strong><input type="checkbox" name="bp_tpack_disable_js" value="1"<?php if ( (int)get_option( 'bp_tpack_disable_js' ) ) : ?> checked="checked"<?php endif; ?> /> Disable BP Template Pack JS / AJAX</strong></p>
				<small style="display: block; margin-left:18px; font-size: 11px">The BuddyPress template pack will automatically integrate the BuddyPress default theme javascript and AJAX functionality into your
					theme. You can switch this off, however the experience will be somewhat degraded.</small>

			<p class="submit">
				<input type="submit" name="bp_tpack_save" value="Save Settings" class="button" />
			</p>
		</form>

		<div style="float: left; width: 37%;">
			<p style="line-height: 180%; border: 1px solid #eee; background: #fff; padding: 5px 10px;"><strong>NOTE:</strong> To remove the "BuddyPress is ready" message you will need to add a "buddypress" tag to your theme. You can do this by editing the <code>style.css</code> file of your active theme and adding the tag to the "Tags:" line in the comment header.</p>

			<h4>Navigation Links</h4>

			<p>You may want to add new navigation tabs or links to your theme to link to BuddyPress directory pages. The default set of links are:</p>
				<ul>
					<li>Activity: <a href="<?php echo get_option('home') . '/' . BP_ACTIVITY_SLUG . '/'; ?>"><?php echo get_option('home') . '/' . BP_ACTIVITY_SLUG . '/'; ?></a></li>
					<li>Members: <a href="<?php echo get_option('home') . '/' . BP_MEMBERS_SLUG . '/'; ?>"><?php echo get_option('home') . '/' . BP_MEMBERS_SLUG . '/'; ?></a></li>
					<li>Groups: <a href="<?php echo get_option('home') . '/' . BP_GROUPS_SLUG . '/'; ?>"><?php echo get_option('home') . '/' . BP_GROUPS_SLUG . '/'; ?></a></li>
					<li>Forums: <a href="<?php echo get_option('home') . '/' . BP_FORUMS_SLUG . '/'; ?>"><?php echo get_option('home') . '/' . BP_FORUMS_SLUG . '/'; ?></a></li>
					<li>Register: <a href="<?php echo get_option('home') . '/' . BP_REGISTER_SLUG . '/'; ?>"><?php echo get_option('home') . '/' . BP_REGISTER_SLUG . '/'; ?></a> (registration must be enabled)</li>

					<?php if ( is_multisite() ) : ?>
						<li>Blogs: <a href="<?php echo get_option('home') . '/' . BP_BLOGS_SLUG . '/'; ?>"><?php echo get_option('home') . '/' . BP_BLOGS_SLUG . '/'; ?></a></li>
					<?php endif; ?>
				</ul>

			<h4>Reset Setup</h4>
			<p>If you would like to run through the setup process again please use the reset button (you will start at step three if you haven't removed the template files):</p>
			<p><a class="button" href="?page=bp-tpack-options&reset=1">Reset</a></p>
		</div>

<?php
	}
}

function bp_tpack_move_templates() {
	$destination_dir = WP_CONTENT_DIR . '/themes/' . get_option('stylesheet') . '/';
	$source_dir = BP_PLUGIN_DIR . '/bp-themes/bp-default/';

	$dirs = array( 'activity', 'blogs', 'forums', 'groups', 'members', 'registration' );

	foreach ( (array)$dirs as $dir ) {
		if ( !bp_tpack_recurse_copy( $source_dir . $dir, $destination_dir . $dir ) )
			return false;
	}

	return true;
}

function bp_tpack_recurse_copy( $src, $dst ) {
	$dir = @opendir( $src );

	if ( !@mkdir( $dst ) )
		return false;

	while ( false !== ( $file = readdir( $dir ) ) ) {
		if ( ( $file != '.' ) && ( $file != '..' ) ) {
			if ( is_dir( $src . '/' . $file ) )
				bp_tpack_recurse_copy( $src . '/' . $file, $dst . '/' . $file );
			else {
				if ( !@copy( $src . '/' . $file, $dst . '/' . $file ) )
					return false;
			}
		}
	}

	@closedir( $dir );

	return true;
}


/*****
 * Add support for showing the activity stream as the front page of the site
 */

/* Filter the dropdown for selecting the page to show on front to include "Activity Stream" */
function bp_tpack_wp_pages_filter( $page_html ) {
	if ( 'page_on_front' != substr( $page_html, 14, 13 ) )
		return $page_html;

	$selected = false;
	$page_html = str_replace( '</select>', '', $page_html );

	if ( bp_tpack_page_on_front() == 'activity' )
		$selected = ' selected="selected"';

	$page_html .= '<option class="level-0" value="activity"' . $selected . '>' . __( 'Activity Stream', 'buddypress' ) . '</option></select>';
	return $page_html;
}
add_filter( 'wp_dropdown_pages', 'bp_tpack_wp_pages_filter' );

/* Hijack the saving of page on front setting to save the activity stream setting */
function bp_tpack_page_on_front_update( $oldvalue, $newvalue ) {
	if ( !is_admin() || !is_super_admin() )
		return false;

	if ( 'activity' == $_POST['page_on_front'] )
		return 'activity';
	else
		return $oldvalue;
}
add_action( 'pre_update_option_page_on_front', 'bp_tpack_page_on_front_update', 10, 2 );

/* Load the activity stream template if settings allow */
function bp_tpack_page_on_front_template( $template ) {
	global $wp_query;

	if ( empty( $wp_query->post->ID ) )
		return locate_template( array( 'activity/index.php' ), false );
	else
		return $template;
}
add_filter( 'page_template', 'bp_tpack_page_on_front_template' );

/* Return the ID of a page set as the home page. */
function bp_tpack_page_on_front() {
	if ( 'page' != get_option( 'show_on_front' ) )
		return false;

	return apply_filters( 'bp_tpack_page_on_front', get_option( 'page_on_front' ) );
}

/* Force the page ID as a string to stop the get_posts query from kicking up a fuss. */
function bp_tpack_fix_get_posts_on_activity_front() {
	global $wp_query;

	if ( !empty($wp_query->query_vars['page_id']) && 'activity' == $wp_query->query_vars['page_id'] )
		$wp_query->query_vars['page_id'] = '"activity"';
}
add_action( 'pre_get_posts', 'bp_tpack_fix_get_posts_on_activity_front' );

/**
 * Hooks BP's action buttons
 */
function bp_tpack_add_buttons() {
	// Member Buttons
	if ( bp_is_active( 'friends' ) )
		add_action( 'bp_member_header_actions',    'bp_add_friend_button' );
	
	if ( bp_is_active( 'activity' ) )
		add_action( 'bp_member_header_actions',    'bp_send_public_message_button' );
	
	if ( bp_is_active( 'messages' ) )
		add_action( 'bp_member_header_actions',    'bp_send_private_message_button' );
	
	// Group Buttons
	if ( bp_is_active( 'groups' ) ) {
		add_action( 'bp_group_header_actions',     'bp_group_join_button' );
		add_action( 'bp_group_header_actions',     'bp_group_new_topic_button' );
		add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
	}
	
	// Blog Buttons
	if ( bp_is_active( 'blogs' ) )
		add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );
}
add_action( 'bp_init', 'bp_tpack_add_buttons' );

?>
