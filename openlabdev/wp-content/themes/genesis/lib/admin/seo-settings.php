<?php
/**
 * Creates the SEO Settings page.
 *
 * @package Genesis
 * @todo document the functions in seo-settings.php
 */

/**
 * This function registers the default values for Genesis SEO Settings
 */
function genesis_seo_settings_defaults() {
	$defaults = array( // define our defaults
		'append_description_home' => 1,
		'append_site_title' => 0,
		'doctitle_sep' => '—',
		'doctitle_seplocation' => 'right',

		'home_h1_on' => 'title',
		'home_doctitle' => '',
		'home_description' => '',
		'home_keywords' => '',
		'home_noindex' => 0,
		'home_nofollow' => 0,
		'home_noarchive' => 0,

		'canonical_archives' => 1,

		'head_index_rel_link' => 0,
		'head_parent_post_rel_link' => 0,
		'head_start_post_rel_link' => 0,
		'head_adjacent_posts_rel_link' => 0,
		'head_wlwmanifest_link' => 0,
		'head_shortlink' => 0,

		'noindex_cat_archive' => 1,
		'noindex_tag_archive' => 1,
		'noindex_author_archive' => 1,
		'noindex_date_archive' => 1,
		'noindex_search_archive' => 1,
		'noarchive_cat_archive' => 0,
		'noarchive_tag_archive' => 0,
		'noarchive_author_archive' => 0,
		'noarchive_date_archive' => 0,
		'noarchive_search_archive' => 0,
		'noarchive' => 0,
		'noodp' => 1,
		'noydir' => 1
	);

	return apply_filters('genesis_seo_settings_defaults', $defaults);
}

add_action('admin_init', 'genesis_register_seo_settings', 5);
/**
 * This registers the settings field and adds defaults to the options table
 */
function genesis_register_seo_settings() {
	register_setting( GENESIS_SEO_SETTINGS_FIELD, GENESIS_SEO_SETTINGS_FIELD );
	add_option( GENESIS_SEO_SETTINGS_FIELD, genesis_seo_settings_defaults() );

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'seo-settings' )
		return;

	if ( genesis_get_seo_option('reset') ) {
		update_option(GENESIS_SEO_SETTINGS_FIELD, genesis_seo_settings_defaults());

		genesis_admin_redirect( 'seo-settings', array( 'reset' => 'true' ) );
		exit;
	}
}

add_action('admin_notices', 'genesis_seo_settings_notice');
/**
 * This is the notice that displays when you successfully save or reset
 * the SEO settings.
 */
function genesis_seo_settings_notice() {

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'seo-settings' )
		return;

	if ( isset( $_REQUEST['reset'] ) && $_REQUEST['reset'] == 'true' ) {
		echo '<div id="message" class="updated" id="message"><p><strong>'.__('SEO Settings Reset', 'genesis').'</strong></p></div>';
	}
	elseif ( isset( $_REQUEST['settings-updated'] ) && $_REQUEST['settings-updated'] == 'true' ) {
		echo '<div id="message" class="updated" id="message"><p><strong>'.__('SEO Settings Saved', 'genesis').'</strong></p></div>';
	}

}

add_action('admin_menu', 'genesis_seo_settings_init');
/**
 * This is a necessary go-between to get our scripts and boxes loaded
 * on the theme settings page only, and not the rest of the admin
 */
function genesis_seo_settings_init() {
	global $_genesis_seo_settings_pagehook;

	add_action('load-'.$_genesis_seo_settings_pagehook, 'genesis_seo_settings_scripts');
	add_action('load-'.$_genesis_seo_settings_pagehook, 'genesis_seo_settings_boxes');
}

function genesis_seo_settings_scripts() {
	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
}

function genesis_seo_settings_boxes() {
	global $_genesis_seo_settings_pagehook;

	add_meta_box('genesis-seo-settings-doctitle', __('Doctitle Settings', 'genesis'), 'genesis_seo_settings_doctitle_box', $_genesis_seo_settings_pagehook, 'column1');
	add_meta_box('genesis-seo-settings-homepage', __('Homepage Settings', 'genesis'), 'genesis_seo_settings_homepage_box', $_genesis_seo_settings_pagehook, 'column1');
	add_meta_box('genesis-seo-settings-archives', __('Archives Settings', 'genesis'), 'genesis_seo_settings_archives_box', $_genesis_seo_settings_pagehook, 'column1');
	add_meta_box('genesis-seo-settings-dochead', __('Document Head Settings', 'genesis'), 'genesis_seo_settings_document_head_box', $_genesis_seo_settings_pagehook, 'column2');
	add_meta_box('genesis-seo-settings-robots', __('Robots Meta Settings', 'genesis'), 'genesis_seo_settings_robots_meta_box', $_genesis_seo_settings_pagehook, 'column2');
	add_meta_box('genesis-seo-settings-nofollow', __('Link nofollow Settings', 'genesis'), 'genesis_seo_settings_nofollow_box', $_genesis_seo_settings_pagehook, 'column2');
}

add_filter('screen_layout_columns', 'genesis_seo_settings_layout_columns', 10, 2);
/**
 * Tell WordPress that we want only 2 columns available for our meta-boxes
 */
function genesis_seo_settings_layout_columns($columns, $screen) {
	global $_genesis_seo_settings_pagehook;
	if ($screen == $_genesis_seo_settings_pagehook) {
		// This page should only have 2 column options
		$columns[$_genesis_seo_settings_pagehook] = 2;
	}
	return $columns;
}

/**
 * This function is what actually gets output to the page. It handles the markup,
 * builds the form, outputs necessary JS stuff, and fires <code>do_meta_boxes()</code>
 */
function genesis_seo_settings_admin() {
global $_genesis_seo_settings_pagehook, $screen_layout_columns;
if( $screen_layout_columns == 3 ) {
	$width = 'width: 32.67%';
	$hide2 = $hide3 = ' display: block;';
}
elseif( $screen_layout_columns == 2 ) {
	$width = 'width: 49%;';
	$hide2 = ' display: block;';
	$hide3 = ' display: none;';
}
else {
	$width = 'width: 99%;';
	$hide2 = $hide3 = ' display: none;';
}
?>
	<div id="genesis-seo-settings" class="wrap genesis-metaboxes">
	<form method="post" action="options.php">

		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
		<?php settings_fields(GENESIS_SEO_SETTINGS_FIELD); // important! ?>

		<?php screen_icon('options-general'); ?>
		<h2>
			<?php _e('Genesis - SEO Settings', 'genesis'); ?>
			<input type="submit" class="button-primary add-new-h2" value="<?php _e('Save Settings', 'genesis') ?>" />
			<input type="submit" class="button-highlighted add-new-h2" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[reset]" value="<?php _e('Reset Settings', 'genesis'); ?>" onclick="return genesis_confirm('<?php echo esc_js( __('Are you sure you want to reset?', 'genesis') ); ?>');" />
		</h2>

		<div class="metabox-holder">
			<div class="postbox-container" style="<?php echo $width; ?>">
				<?php do_meta_boxes($_genesis_seo_settings_pagehook, 'column1', null); ?>
			</div>
			<div class="postbox-container" style="<?php echo $width; echo $hide2; ?>">
				<?php do_meta_boxes($_genesis_seo_settings_pagehook, 'column2', null); ?>
			</div>
		</div>

		<div class="bottom-buttons">
			<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'genesis') ?>" />
			<?php $reset_onclick = 'onclick="if ( confirm(\'' . esc_js( __('Are you sure you want to reset?', 'genesis') ) . '\') ) {return true;}return false;"'; ?>
			<input type="submit" <?php echo $reset_onclick; ?> class="button-highlighted" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[reset]" value="<?php _e('Reset Settings', 'genesis'); ?>" />
		</div>
	</form>
	</div>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $_genesis_seo_settings_pagehook; ?>');
		});
		//]]>
	</script>

<?php
}

/**
 * This next section defines functions that contain the content of the "boxes" that will be
 * output by default on the "SEO Settings" page. There's a bunch of them.
 *
 */
function genesis_seo_settings_doctitle_box() { ?>

	<p><span class="description"><?php _e('The Document Title is the single most important SEO tag in your document source. It succinctly informs search engines of what information is contained in the document. The doctitle changes from page to page, but these options will help you control what it looks by default.', 'genesis'); ?></span></p>

	<p><span class="description"><?php _e('<b>By default</b>, the homepage doctitle will contain the site title, the single post and page doctitle will contain the post/page title, archive pages will contain the archive type, etc.', 'genesis'); ?></span></p>

	<p><label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[append_description_home]" value="1" <?php checked(1, genesis_get_seo_option('append_description_home')); ?> /> <?php _e('Append Site Description to Doctitle on homepage?', 'genesis'); ?></label></p>

	<p><label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[append_site_title]" value="1" <?php checked(1, genesis_get_seo_option('append_site_title')); ?> /> <?php _e('Append Site Name to Doctitle on inner pages?', 'genesis'); ?> </label></p>

	<p><?php _e('Doctitle (<code>&lt;title&gt;</code>) Append Location', 'genesis'); ?>:<br />
	<span class="description"><?php _e('Determines what side the appended doctitle text will go on', 'genesis'); ?></span></p>

	<p><label><input type="radio" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[doctitle_seplocation]" value="left" <?php checked('left', genesis_get_seo_option('doctitle_seplocation')); ?> />
	<?php _e('Left', 'genesis'); ?></label>
	<label><input type="radio" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[doctitle_seplocation]" value="right" <?php checked('right', genesis_get_seo_option('doctitle_seplocation')); ?> />
	<?php _e('Right', 'genesis'); ?></label></p>

	<p><?php _e('Doctitle (<code>&lt;title&gt;</code>) Separator', 'genesis'); ?>:
	<input type="text" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[doctitle_sep]" value="<?php echo esc_attr( genesis_get_seo_option('doctitle_sep') ); ?>" size="15" /></p>

	<p><span class="description"><?php _e('<b>NOTE:</b> If the doctitle consists of two parts (Title &amp; Appended Text), then the Doctitle Separator will go between them.', 'genesis'); ?></span></p>

<?php
}

function genesis_seo_settings_homepage_box() { ?>

	<p><?php printf(__('Which text would you like to be wrapped in %s tags?', 'genesis'), '<code>&lt;h1&gt;</code>'); ?><br />
	<span class="description"><?php printf(__('The %s tag is, arguably, the second most important SEO tag in the document source. Choose wisely.', 'genesis'), '<code>&lt;h1&gt;</code>'); ?></span><br /></p>

	<p><label><input type="radio" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[home_h1_on]" value="title" <?php checked('title', genesis_get_seo_option('home_h1_on')); ?> />
	<?php _e('Site Title', 'genesis'); ?></label><br />
	<label><input type="radio" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[home_h1_on]" value="description" <?php checked('description', genesis_get_seo_option('home_h1_on')); ?> />
	<?php _e('Site Description', 'genesis'); ?></label><br />
	<label><input type="radio" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[home_h1_on]" value="neither" <?php checked('neither', genesis_get_seo_option('home_h1_on')); ?> />
	<?php _e('Neither. I\'ll manually wrap my own text on the homepage', 'genesis'); ?></label></p>

	<p><?php _e('Home Doctitle', 'genesis'); ?>:<br />
	<input type="text" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[home_doctitle]" value="<?php echo esc_attr( genesis_get_seo_option('home_doctitle') ); ?>" size="40" /></p>

	<p><span class="description"><?php _e('<b>NOTE:</b> If you leave the doctitle field blank, your site&rsquo;s title will be used instead.', 'genesis'); ?></span></p>

	<p><?php _e('Home META Description', 'genesis'); ?>:<br />
	<textarea name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[home_description]" rows="3" cols="34"><?php echo esc_textarea( genesis_get_seo_option('home_description') ); ?></textarea></p>

	<p><span class="description"><?php _e('<b>NOTE:</b> The META Description can be used to determine the text used under the title on search engine results pages.', 'genesis'); ?></span></p>

	<p><?php _e('Home META Keywords (comma separated)', 'genesis'); ?>:<br />
	<input type="text" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[home_keywords]" value="<?php echo esc_attr( genesis_get_seo_option('home_keywords') ); ?>" size="40" /></p>

	<p><span class="description"><?php _e('<b>NOTE:</b> Keywords are generally ignored by Search Engines.', 'genesis'); ?></span></p>

	<p><?php _e('Homepage Robots Meta Tags:', 'genesis'); ?><p>

	<p>
		<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[home_noindex]" value="1" <?php checked(1, genesis_get_seo_option('home_noindex')); ?> /> <?php printf( __('Apply %s to the homepage?', 'genesis'), '<code>noindex</code>' ); ?> </label><br />
		<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[home_nofollow]" value="1" <?php checked(1, genesis_get_seo_option('home_nofollow')); ?> /> <?php printf( __('Apply %s to the homepage?', 'genesis'), '<code>nofollow</code>' ); ?> </label><br />
		<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[home_noarchive]" value="1" <?php checked(1, genesis_get_seo_option('home_noarchive')); ?> /> <?php printf( __('Apply %s to the homepage?', 'genesis'), '<code>noarchive</code>' ); ?> </label>
	</p>

<?php
}

function genesis_seo_settings_archives_box() { ?>

	<p><label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[canonical_archives]" value="1" <?php checked(1, genesis_get_seo_option('canonical_archives')); ?> /> <?php printf( __('Canonical Paginated Archives', 'genesis') ); ?> </label></p>

	<p><span class="description"><?php _e('This option points search engines to the first page of an archive, if viewing a paginated page. If you do not know what this means, leave it on.', 'genesis'); ?></span></p>

<?php
}

function genesis_seo_settings_document_head_box() { ?>

	<p><span class="description"><?php printf( __('By default, WordPress places several tags in your document %1$s. Most of these tags are completely unnecessary, and provide no SEO value whatsoever. They just make your site slower to load. Choose which tags you would like included in your document %1$s. If you do not know what something is, leave it unchecked.', 'genesis'), '<code>&lt;head&gt;</code>' ); ?></span></p>

	<p><b><?php _e('Relationship Link Tags:', 'genesis'); ?></b></p>

	<p>
		<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[head_index_rel_link]" value="1" <?php checked(1, genesis_get_seo_option('head_index_rel_link')); ?> /> <?php printf( __('Index %s link tag', 'genesis'), '<code>rel</code>' ); ?></label><br />
		<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[head_parent_post_rel_link]" value="1" <?php checked(1, genesis_get_seo_option('head_parent_post_rel_link')); ?> /> <?php printf( __('Parent Post %s link tag', 'genesis'), '<code>rel</code>' ); ?></label><br />
		<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[head_start_post_rel_link]" value="1" <?php checked(1, genesis_get_seo_option('head_start_post_rel_link')); ?> /> <?php printf( __('Start Post %s link tag', 'genesis'), '<code>rel</code>' ); ?></label><br />
		<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[head_adjacent_posts_rel_link]" value="1" <?php checked(1, genesis_get_seo_option('head_adjacent_posts_rel_link')); ?> /> <?php printf( __('Adjacent Posts %s link tag', 'genesis'), '<code>rel</code>' ); ?></label>
	</p>

	<p><b><?php _e('Windows Live Writer Support:', 'genesis'); ?></b></p>

	<p><label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[head_wlwmanifest_link]" value="1" <?php checked(1, genesis_get_seo_option('head_wlwmanifest_link')); ?> /> <?php printf( __('Include Windows Live Writer Support Tag?', 'genesis') ); ?></label></p>

	<p><b><?php _e('Shortlink Tag:', 'genesis'); ?></b></p>

	<p><label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[head_shortlink]" value="1" <?php checked(1, genesis_get_seo_option('head_shortlink')); ?> /> <?php printf( __('Include Shortlink tag?', 'genesis') ); ?></label></p>

	<p><span class="description"><?php _e('<b>NOTE:</b> The shortlink tag might have some use for 3rd party service discoverability, but it has no SEO value whatsoever.', 'genesis'); ?></span></p>

<?php
}

function genesis_seo_settings_robots_meta_box() { ?>

	<p><span class="description"><?php _e('Depending on your situation, you may or may not want the following archive pages to be indexed by search engines. Only you can make that determination.', 'genesis'); ?></span></p>

	<p><label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noindex_cat_archive]" value="1" <?php checked(1, genesis_get_seo_option('noindex_cat_archive')); ?> /> <?php printf( __('Apply %s to Category Archives?', 'genesis'), '<code>noindex</code>' ); ?></label><br />
	<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noindex_tag_archive]" value="1" <?php checked(1, genesis_get_seo_option('noindex_tag_archive')); ?> /> <?php printf( __('Apply %s to Tag Archives?', 'genesis'), '<code>noindex</code>' ); ?></label><br />
	<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noindex_author_archive]" value="1" <?php checked(1, genesis_get_seo_option('noindex_author_archive')); ?> /> <?php printf( __('Apply %s to Author Archives?', 'genesis'), '<code>noindex</code>' ); ?></label><br />
	<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noindex_date_archive]" value="1" <?php checked(1, genesis_get_seo_option('noindex_date_archive')); ?> /> <?php printf( __('Apply %s to Date Archives?', 'genesis'), '<code>noindex</code>' ); ?></label><br />
	<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noindex_search_archive]" value="1" <?php checked(1, genesis_get_seo_option('noindex_search_archive')); ?> /> <?php printf( __('Apply %s to Search Archives?', 'genesis'), '<code>noindex</code>' ); ?></label></p>

	<p><span class="description"><?php printf( __('Some search engines will cache pages in your site (e.g Google Cache). The %1$s tag will prevent them from doing so. Choose what archives you want to %1$s.', 'genesis'), '<code>noarchive</code>' ); ?></span></p>

	<p><label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noarchive]" value="1" <?php checked(1, genesis_get_seo_option('noarchive')); ?> /> <?php printf( __('Apply %s to Entire Site?', 'genesis'), '<code>noarchive</code>' ); ?></label></p>

	<p><label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noarchive_cat_archive]" value="1" <?php checked(1, genesis_get_seo_option('noarchive_cat_archive')); ?> /> <?php printf( __('Apply %s to Category Archives?', 'genesis'), '<code>noarchive</code>' ); ?></label><br />
	<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noarchive_tag_archive]" value="1" <?php checked(1, genesis_get_seo_option('noarchive_tag_archive')); ?> /> <?php printf( __('Apply %s to Tag Archives?', 'genesis'), '<code>noarchive</code>' ); ?></label><br />
	<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noarchive_author_archive]" value="1" <?php checked(1, genesis_get_seo_option('noarchive_author_archive')); ?> /> <?php printf( __('Apply %s to Author Archives?', 'genesis'), '<code>noarchive</code>' ); ?></label><br />
	<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noarchive_date_archive]" value="1" <?php checked(1, genesis_get_seo_option('noarchive_date_archive')); ?> /> <?php printf( __('Apply %s to Date Archives?', 'genesis'), '<code>noarchive</code>' ); ?></label><br />
	<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noarchive_search_archive]" value="1" <?php checked(1, genesis_get_seo_option('noarchive_search_archive')); ?> /> <?php printf( __('Apply %s to Search Archives?', 'genesis'), '<code>noarchive</code>' ); ?></label></p>

	<p><span class="description"><?php printf( __('Occasionally, search engines use resources like the Open Directory Project and the Yahoo! Directory to find titles and descriptions for your content. Generally, you will not want them to do this. The %s and %s tags prevent them from doing so.', 'genesis'), '<code>noodp</code>', '<code>noydir</code>' ); ?></span></p>

	<p>
		<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noodp]" value="1" <?php checked(1, genesis_get_seo_option('noodp')); ?> /> <?php printf( __('Apply %s to your site?', 'genesis'), '<code>noodp</code>' ) ?></label><br />
		<label><input type="checkbox" name="<?php echo GENESIS_SEO_SETTINGS_FIELD; ?>[noydir]" value="1" <?php checked(1, genesis_get_seo_option('noydir')); ?> /> <?php printf( __('Apply %s to your site?', 'genesis'), '<code>noydir</code>' ) ?></label>
	<p>

<?php
}

function genesis_seo_settings_nofollow_box() { ?>

	<p><span class="description"><?php printf( __('<b>NOTE:</b> Don&apos;t be alarmed. We have deprecated these settings, because according to the <a href="%s" target="_blank">latest information available</a>, applying %s to internal links provides no SEO value to your site.', 'genesis'), 'http://www.mattcutts.com/blog/pagerank-sculpting/', '<code>nofollow</code>' ); ?></span></p>

<?php
}