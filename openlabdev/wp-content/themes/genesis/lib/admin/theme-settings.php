<?php
/**
 * Creates the Theme Settings page.
 *
 * @package Genesis
 * @todo document the functions in theme-settings.php
 */

/**
 * This function registers the default values for Genesis theme settings
 */
function genesis_theme_settings_defaults() {
	$defaults = array( // define our defaults
		'update' => 1,
		'blog_title' => 'text',
		'header_right' => 0,
		'site_layout' => 'content-sidebar',
		'nav' => 1,
		'nav_superfish' => 1,
		'nav_extras_enable' => 0,
		'nav_extras' => 'date',
		'nav_extras_twitter_id' => '',
		'nav_extras_twitter_text' => 'Follow me on Twitter',
		'subnav' => 0,
		'subnav_superfish' => 1,
		'feed_uri' => '',
		'comments_feed_uri' => '',
		'redirect_feeds' => 0,
		'comments_pages' => 0,
		'comments_posts' => 1,
		'trackbacks_pages' => 0,
		'trackbacks_posts' => 1,
		'breadcrumb_home' => 0,
		'breadcrumb_single' => 0,
		'breadcrumb_page' => 0,
		'breadcrumb_archive' => 0,
		'breadcrumb_404' => 0,
		'content_archive' => 'full',
		'content_archive_thumbnail' => 0,
		'posts_nav' => 'older-newer',
		'blog_cat' => '',
		'blog_cat_exclude' => '',
		'blog_cat_num' => 10,
		'header_scripts' => '',
		'footer_scripts' => '',
		'theme_version' => PARENT_THEME_VERSION
	);

	return apply_filters('genesis_theme_settings_defaults', $defaults);
}

add_action('admin_init', 'genesis_register_theme_settings', 5);
/**
 * This registers the settings field and adds defaults to the options table.
 * It also handles settings resets by pushing in the defaults.
 */
function genesis_register_theme_settings() {
	register_setting( GENESIS_SETTINGS_FIELD, GENESIS_SETTINGS_FIELD );
	add_option( GENESIS_SETTINGS_FIELD, genesis_theme_settings_defaults() );

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'genesis' )
		return;

	if ( genesis_get_option('reset') ) {
		update_option(GENESIS_SETTINGS_FIELD, genesis_theme_settings_defaults());

		genesis_admin_redirect( 'genesis', array( 'reset' => 'true' ) );
		exit;
	}

}

add_action('admin_notices', 'genesis_theme_settings_notice');
/**
 * This is the notice that displays when you successfully save or reset
 * the theme settings.
 */
function genesis_theme_settings_notice() {

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'genesis' )
		return;

	if ( isset( $_REQUEST['reset'] ) && $_REQUEST['reset'] == 'true' ) {
		echo '<div id="message" class="updated"><p><strong>'.__('Theme Settings Reset', 'genesis').'</strong></p></div>';
	}
	elseif ( isset( $_REQUEST['settings-updated'] ) && $_REQUEST['settings-updated'] == 'true' ) {
		echo '<div id="message" class="updated"><p><strong>'.__('Theme Settings Saved', 'genesis').'</strong></p></div>';
	}

}

add_action('admin_menu', 'genesis_theme_settings_init');
/**
 * This is a necessary go-between to get our scripts and boxes loaded
 * on the theme settings page only, and not the rest of the admin
 */
function genesis_theme_settings_init() {
	global $_genesis_theme_settings_pagehook;

	add_action('load-'.$_genesis_theme_settings_pagehook, 'genesis_theme_settings_scripts');
	add_action('load-'.$_genesis_theme_settings_pagehook, 'genesis_theme_settings_boxes');
}

function genesis_theme_settings_scripts() {
	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
}

function genesis_theme_settings_boxes() {
	global $_genesis_theme_settings_pagehook;

	add_meta_box('genesis-theme-settings-version', __('Information', 'genesis'), 'genesis_theme_settings_info_box', $_genesis_theme_settings_pagehook, 'column1');
	add_meta_box('genesis-theme-settings-general', __('General Settings', 'genesis'), 'genesis_theme_settings_general_box', $_genesis_theme_settings_pagehook, 'column1');
	add_meta_box('genesis-theme-settings-nav', __('Primary Navigation', 'genesis'), 'genesis_theme_settings_nav_box', $_genesis_theme_settings_pagehook, 'column1');
	add_meta_box('genesis-theme-settings-subnav', __('Secondary Navigation', 'genesis'), 'genesis_theme_settings_subnav_box', $_genesis_theme_settings_pagehook, 'column1');
	add_meta_box('genesis-theme-settings-comments', __('Comments/Trackbacks', 'genesis'), 'genesis_theme_settings_comments_box', $_genesis_theme_settings_pagehook, 'column1');
	add_meta_box('genesis-theme-settings-feeds', __('Custom Feeds', 'genesis'), 'genesis_theme_settings_feeds_box', $_genesis_theme_settings_pagehook, 'column2');
	add_meta_box('genesis-theme-settings-breadcrumb', __('Breadcrumbs', 'genesis'), 'genesis_theme_settings_breadcrumb_box', $_genesis_theme_settings_pagehook, 'column2');
	add_meta_box('genesis-theme-settings-posts', __('Content Archives', 'genesis'), 'genesis_theme_settings_post_archives_box', $_genesis_theme_settings_pagehook, 'column2');
	add_meta_box('genesis-theme-settings-blogpage', __('Blog Page', 'genesis'), 'genesis_theme_settings_blogpage_box', $_genesis_theme_settings_pagehook, 'column2');
	add_meta_box('genesis-theme-settings-scripts', __('Header/Footer Scripts', 'genesis'), 'genesis_theme_settings_scripts_box', $_genesis_theme_settings_pagehook, 'column2');
}

add_filter('screen_layout_columns', 'genesis_theme_settings_layout_columns', 10, 2);
/**
 * Tell WordPress that we want only 2 columns available for our meta-boxes
 */
function genesis_theme_settings_layout_columns($columns, $screen) {
	global $_genesis_theme_settings_pagehook;
	if ($screen == $_genesis_theme_settings_pagehook) {
		// This page should only have 2 column options
		$columns[$_genesis_theme_settings_pagehook] = 2;
	}
	return $columns;
}

/**
 * This function is what actually gets output to the page. It handles the markup,
 * builds the form, outputs necessary JS stuff, and fires <code>do_meta_boxes()</code>
 */
function genesis_theme_settings_admin() {
	global $_genesis_theme_settings_pagehook, $screen_layout_columns;

	if ( $screen_layout_columns == 3 ) {
		$width = 'width: 32.67%';
		$hide2 = $hide3 = ' display: block;';
	}
	elseif ( $screen_layout_columns == 2 ) {
		$width = 'width: 49%;';
		$hide2 = ' display: block;';
		$hide3 = ' display: none;';
	}
	else {
		$width = 'width: 99%;';
		$hide2 = $hide3 = ' display: none;';
	}
?>
	<div id="genesis-theme-settings" class="wrap genesis-metaboxes">
	<form method="post" action="options.php">

		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
		<?php settings_fields(GENESIS_SETTINGS_FIELD); // important! ?>
		<input type="hidden" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[theme_version]>" value="<?php echo esc_attr(genesis_option('theme_version')); ?>" />

		<?php screen_icon('options-general'); ?>
		<h2>
			<?php _e('Genesis - Theme Settings', 'genesis'); ?>
			<input type="submit" class="button-primary add-new-h2" value="<?php _e('Save Settings', 'genesis') ?>" />
			<input type="submit" class="button-highlighted add-new-h2" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[reset]" value="<?php _e('Reset Settings', 'genesis'); ?>" onclick="return genesis_confirm('<?php echo esc_js( __('Are you sure you want to reset?', 'genesis') ); ?>');" />
		</h2>

		<div class="metabox-holder">
			<div class="postbox-container" style="<?php echo $width; ?>">
				<?php do_meta_boxes($_genesis_theme_settings_pagehook, 'column1', null); ?>
			</div>
			<div class="postbox-container" style="<?php echo $width; echo $hide2; ?>">
				<?php do_meta_boxes($_genesis_theme_settings_pagehook, 'column2', null); ?>
			</div>
		</div>

		<div class="bottom-buttons">
			<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'genesis') ?>" />
			<input type="submit" class="button-highlighted" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[reset]" value="<?php _e('Reset Settings', 'genesis'); ?>" />
		</div>
	</form>
	</div>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $_genesis_theme_settings_pagehook; ?>');
		});
		//]]>
	</script>

<?php
}

/**
 * This next section defines functions that contain the content of the "boxes" that will be
 * output by default on the "Theme Settings" page. There's a bunch of them.
 *
 * FWIW, you can copy this syntax and load your own boxes on the theme settings page too.
 */
function genesis_theme_settings_info_box() { ?>
	<p><strong><?php _e('Version:', 'genesis'); ?></strong> <?php genesis_option('theme_version'); ?> <?php echo g_ent('&middot;'); ?> <strong><?php _e('Released:', 'genesis'); ?></strong> <?php echo PARENT_THEME_RELEASE_DATE; ?></p>

	<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[show_info]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[show_info]" value="1" <?php checked(1, genesis_get_option('show_info')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[show_info]"><?php _e('Display Theme Information in your document source', 'genesis'); ?></label></p>

	<p><span class="description"><?php _e('<b>NOTE:</b> This can be helpful for diagnosing problems with your theme when seeking support in the forums.', 'genesis'); ?></span></p>

	<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[update]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[update]" value="1" <?php checked(1, genesis_get_option('update')); ?> <?php disabled( 0, is_super_admin() ); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[update]"><?php _e('Enable Automatic Updates', 'genesis'); ?></label></p>

	<div id="genesis_update_notification_setting">
		<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[update_email]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[update_email]" value="1" <?php checked(1, genesis_get_option('update_email')); ?> <?php disabled( 0, is_super_admin() ); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[update_email]"><?php _e('Notify', 'genesis'); ?></label> <input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[update_email_address]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[update_email_address]" value="<?php echo esc_attr( genesis_option('update_email_address') ); ?>" size="12" <?php disabled( 0, is_super_admin() ); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[update_email_address]"><?php _e('when updates are available', 'genesis'); ?></label></p>

		<p><span class="description"><?php _e('If you provide an email address in the above field, your blog can email you when a new version of Genesis is available', 'genesis'); ?></span></p>
	</div>
<?php
}

function genesis_theme_settings_general_box() { ?>

	<?php if ( ! current_theme_supports( 'genesis-custom-header' ) ) : ?>
	<p><?php _e("Use for blog title/logo:", 'genesis'); ?>
	<select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[blog_title]">
		<option value="text" <?php selected('text', genesis_get_option('blog_title')); ?>><?php _e("Dynamic text", 'genesis'); ?></option>
		<option value="image" <?php selected('image', genesis_get_option('blog_title')); ?>><?php _e("Image logo", 'genesis'); ?></option>
	</select></p>
	<?php endif; ?>

	<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[header_right]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[header_right]" value="1" <?php checked(1, genesis_get_option('header_right')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[header_right]"><?php _e("Widgetize Right Side of Header?", 'genesis'); ?></label></p>
	<p><?php _e("Select site layout:", 'genesis'); ?>
	<select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[site_layout]">
	<?php
	foreach ( genesis_get_layouts() as $id => $data ) {

		printf( '<option value="%s" %s>%s</option>', esc_attr( $id ), selected( $id, genesis_get_option('site_layout'), false ), esc_html( $data['label'] ) );

	}
	?>
	</select></p>
<?php
}

function genesis_theme_settings_nav_box() { ?>

	<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav]" value="1" <?php checked(1, genesis_get_option('nav')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav]"><?php _e("Include Primary Navigation Menu?", 'genesis'); ?></label>
	</p>

	<div id="genesis_nav_settings">
		<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_superfish]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_superfish]" value="1" <?php checked(1, genesis_get_option('nav_superfish')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_superfish]"><?php _e("Enable Fancy Dropdowns?", 'genesis'); ?></label>
		</p>

		<p><span class="description"><?php printf( __('<b>NOTE:</b> In order to use the navigation menus, you must build a <a href="%s">custom menu</a>, then assign it to the proper Menu Location.', 'genesis'), admin_url('nav-menus.php') ); ?></span></p>

		<hr class="div" />

		<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_extras_enable]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_extras_enable]" value="1" <?php checked(1, genesis_get_option('nav_extras_enable')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_extras_enable]"><?php _e('Enable Extras on Right Side?', 'genesis'); ?></label></p>

		<div id="genesis_nav_extras_settings">
			<p><?php _e("Display the following:", 'genesis'); ?>
			<select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_extras]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_extras]">
				<option value="date" <?php selected('date', genesis_get_option('nav_extras')); ?>><?php _e("Today's date", 'genesis'); ?></option>
				<option value="rss" <?php selected('rss', genesis_get_option('nav_extras')); ?>><?php _e("RSS feed links", 'genesis'); ?></option>
				<option value="search" <?php selected('search', genesis_get_option('nav_extras')); ?>><?php _e("Search form", 'genesis'); ?></option>
				<option value="twitter" <?php selected('twitter', genesis_get_option('nav_extras')); ?>><?php _e("Twitter link", 'genesis'); ?></option>
			</select></p>
			<div id="genesis_nav_extras_twitter">
				<p><?php _e("Enter Twitter ID:", 'genesis'); ?>
				<input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_extras_twitter_id]" value="<?php echo esc_attr( genesis_get_option('nav_extras_twitter_id') ); ?>" size="27" /></p>
				<p><?php _e("Twitter Link Text:", 'genesis'); ?>
				<input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[nav_extras_twitter_text]" value="<?php echo esc_attr( genesis_get_option('nav_extras_twitter_text') ); ?>" size="27" /></p>
			</div>
		</div>
	</div>
<?php
}

function genesis_theme_settings_subnav_box() { ?>
	<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[subnav]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[subnav]" value="1" <?php checked(1, genesis_get_option('subnav')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[subnav]"><?php _e("Include Secondary Navigation Menu?", 'genesis'); ?></label>
	</p>

	<div id="genesis_subnav_settings">
		<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[subnav_superfish]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[subnav_superfish]" value="1" <?php checked(1, genesis_get_option('subnav_superfish')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[subnav_superfish]"><?php _e("Enable Fancy Dropdowns?", 'genesis'); ?></label>
		</p>

		<p><span class="description"><?php printf( __('<b>NOTE:</b> In order to use the navigation menus, you must build a <a href="%s">custom menu</a>, then assign it to the proper Menu Location.', 'genesis'), admin_url('nav-menus.php') ); ?></span></p>
	</div>

<?php
}

function genesis_theme_settings_feeds_box() { ?>

	<p><?php _e('Enter your custom feed URI:', 'genesis'); ?><br />
	<input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[feed_uri]" value="<?php echo esc_attr( genesis_get_option('feed_uri') ); ?>" size="30" /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[redirect_feed]"><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[redirect_feed]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[redirect_feed]" value="1" <?php checked(1, genesis_get_option('redirect_feed')); ?> /> <?php _e("Redirect Feed?", 'genesis'); ?></label></p>

	<p><?php _e('Enter your custom comments feed URI:', 'genesis'); ?><br />
	<input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[comments_feed_uri]" value="<?php echo esc_attr( genesis_get_option('comments_feed_uri') ); ?>" size="30" /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[redirect_comments_feed]"><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[redirect_comments_feed]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[redirect_comments_feed]" value="1" <?php checked(1, genesis_get_option('redirect_comments__feed')); ?> /> <?php _e("Redirect Feed?", 'genesis'); ?></label></p>

	<p><span class="description"><?php printf( __('<b>NOTE:</b> If your custom feed(s) are not handled by Feedburner, we do not recommend that you use the redirect options. They will not work properly.', 'genesis') ); ?></span></p>

<?php
}

function genesis_theme_settings_comments_box() { ?>
	<p><label><?php _e('Enable Comments', 'genesis'); ?></label>
	<label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[comments_posts]"><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[comments_posts]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[comments_posts]" value="1" <?php checked(1, genesis_get_option('comments_posts')); ?> /> <?php _e("on posts?", 'genesis'); ?></label>

	<label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[comments_pages]"><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[comments_pages]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[comments_pages]" value="1" <?php checked(1, genesis_get_option('comments_pages')); ?> /> <?php _e("on pages?", 'genesis'); ?></label>
	</p>

	<p><label><?php _e('Enable Trackbacks', 'genesis'); ?></label>
	<label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[trackbacks_posts]"><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[trackbacks_posts]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[trackbacks_posts]" value="1" <?php checked(1, genesis_get_option('trackbacks_posts')); ?> /> <?php _e("on posts?", 'genesis'); ?></label>

	<label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[trackbacks_pages]"><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[trackbacks_pages]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[trackbacks_pages]" value="1" <?php checked(1, genesis_get_option('trackbacks_pages')); ?> /> <?php _e("on pages?", 'genesis'); ?></label>
	</p>

	<p><span class="description"><?php _e("<b>NOTE:</b> Comments and Trackbacks can also be disabled on a per post/page basis when creating/editing posts/pages.", 'genesis'); ?></span></p>

<?php
}

function genesis_theme_settings_breadcrumb_box() { ?>

	<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_home]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_home]" value="1" <?php checked(1, genesis_get_option('breadcrumb_home')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_home]"><?php _e("Enable on Front Page", 'genesis'); ?></label><br />
	<input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_single]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_single]" value="1" <?php checked(1, genesis_get_option('breadcrumb_single')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_single]"><?php _e("Enable on Posts", 'genesis'); ?></label><br />
	<input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_page]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_page]" value="1" <?php checked(1, genesis_get_option('breadcrumb_page')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_page]"><?php _e("Enable on Pages", 'genesis'); ?></label><br />
	<input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_archive]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_archive]" value="1" <?php checked(1, genesis_get_option('breadcrumb_archive')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_archive]"><?php _e("Enable on Archives", 'genesis'); ?></label><br />
	<input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_404]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_404]" value="1" <?php checked(1, genesis_get_option('breadcrumb_404')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[breadcrumb_404]"><?php _e("Enable on 404 Page", 'genesis'); ?></label>
	</p>

	<p><span class="description"><?php _e('<b>NOTE:</b> Breadcrumbs are a great way of letting your visitors find out where they are on your site with just a glance. You can enable/disable them on certain areas of your site.', 'genesis'); ?></span></p>
<?php
}

function genesis_theme_settings_post_archives_box() { ?>
	<p><?php _e("Select one of the following:", 'genesis'); ?>
	<select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[content_archive]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[content_archive]">
		<option value="full" <?php selected('full', genesis_get_option('content_archive')); ?>><?php _e("Display post content", 'genesis'); ?></option>
		<option value="excerpts" <?php selected('excerpts', genesis_get_option('content_archive')); ?>><?php _e("Display post excerpts", 'genesis'); ?></option>
	</select></p>

	<div id="genesis_content_limit_setting">
		<p><label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[content_archive_limit]"><?php _e('Limit content to', 'genesis'); ?></label> <input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[content_archive_limit]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[content_archive_limit]" value="<?php echo esc_attr( genesis_option('content_archive_limit') ); ?>" size="3" /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[content_archive_limit]"><?php _e('characters', 'genesis'); ?></label></p>

		<p><span class="description"><?php _e('<b>NOTE:</b> Using this option will limit the text and strip all formatting from the text displayed. To use this option, choose "Display post content" in the select box above.', 'genesis'); ?></span></p>
	</div>

	<p><input type="checkbox" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[content_archive_thumbnail]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[content_archive_thumbnail]" value="1" <?php checked(1, genesis_get_option('content_archive_thumbnail')); ?> /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[content_archive_thumbnail]"><?php _e("Include the Featured Image?", 'genesis'); ?></label>
	</p>

	<p id="genesis_image_size"><?php _e('Image Size', 'genesis'); ?>:
	<?php $sizes = genesis_get_image_sizes(); ?>
	<select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[image_size]">
		<?php
		foreach( (array) $sizes as $name => $size ) :
		echo '<option value="'.$name.'" '.selected($name, genesis_get_option('image_size'), FALSE).'>'.$name.' ('.$size['width'].'x'.$size['height'].')</option>';
		endforeach;
		?>
	</select></p>

	<p><?php _e("Select Post Navigation Technique:", 'genesis'); ?>
	<select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[posts_nav]">
		<option value="older-newer" <?php selected('older-newer', genesis_get_option('posts_nav')); ?>><?php _e("Older / Newer", 'genesis'); ?></option>
		<option value="prev-next" <?php selected('prev-next', genesis_get_option('posts_nav')); ?>><?php _e("Previous / Next", 'genesis'); ?></option>
		<option value="numeric" <?php selected('numeric', genesis_get_option('posts_nav')); ?>><?php _e("Numeric", 'genesis'); ?></option>
	</select></p>

	<p><span class="description"><?php _e("<b>NOTE:</b> The content archives options will affect any blog listings page, including archive, author, blog, category, search, and tag pages.", 'genesis'); ?></span></p>
<?php
}

function genesis_theme_settings_blogpage_box() { ?>
	<p><?php _e("Display which category:", 'genesis'); ?>
	<?php wp_dropdown_categories(array('selected' => genesis_get_option('blog_cat'), 'name' => GENESIS_SETTINGS_FIELD.'[blog_cat]', 'orderby' => 'Name' , 'hierarchical' => 1, 'show_option_all' => __("All Categories", 'genesis'), 'hide_empty' => '0' )); ?></p>

	<p><?php _e("Exclude the following Category IDs:", 'genesis'); ?><br />
	<input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[blog_cat_exclude]" value="<?php echo esc_attr( genesis_get_option('blog_cat_exclude') ); ?>" size="40" /><br />
	<small><strong><?php _e("Comma separated - 1,2,3 for example", 'genesis'); ?></strong></small></p>

	<p><?php _e('Number of Posts to Show', 'genesis'); ?>:
	<input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[blog_cat_num]" value="<?php echo esc_attr( genesis_option('blog_cat_num') ); ?>" size="2" /></p>
<?php
}

function genesis_theme_settings_scripts_box() { ?>
	<p><?php _e("Enter scripts/code you would like output to <code>wp_head()</code>:", 'genesis'); ?><br />
	<textarea name="<?php echo GENESIS_SETTINGS_FIELD; ?>[header_scripts]" cols="39" rows="5"><?php echo esc_textarea( genesis_get_option('header_scripts') ); ?></textarea><br />
	<span class="description"><?php _e('<b>NOTE:</b> The <code>wp_head()</code> hook executes immediately before the closing <code>&lt;/head&gt;</code> tag in the document source', 'genesis'); ?></span></p>

	<p><?php _e("Enter scripts/code you would like output to <code>wp_footer()</code>:", 'genesis'); ?><br />
	<textarea name="<?php echo GENESIS_SETTINGS_FIELD; ?>[footer_scripts]" cols="39" rows="5"><?php echo esc_textarea( genesis_get_option('footer_scripts') ); ?></textarea><br />
	<span class="description"><?php _e('<b>NOTE:</b> The <code>wp_footer()</code> hook executes immediately before the closing <code>&lt;/body&gt;</code> tag in the document source', 'genesis'); ?></span></p>
<?php
}