<?php
// Admin panel that gets added to the page edit page for per page options

add_action('admin_menu', 'weaver_add_page_fields');

function weaver_add_page_fields() {
	add_meta_box('ttw-page-box', 'Weaver Options For This Page', 'weaver_page_extras', 'page', 'normal', 'high');
	add_meta_box('ttw-post-box', 'Weaver Options For This Post', 'weaver_post_extras', 'post', 'normal', 'high');
}

function weaver_page_checkbox($opt, $msg) {
	global $post;
?>
    <input type="checkbox" id="<?php echo($opt); ?>" name="<?php echo($opt); ?>"
	<?php if (get_post_meta($post->ID, $opt, true)) { echo " checked='checked' ";} ?> />
	<?php echo($msg); ?>&nbsp;&nbsp;
<?php }

function weaver_page_extras() {
	global $post;

 	echo("<p>\n");
	_e("<strong>Page Templates</strong>",WEAVER_TRANSADMIN);
	weaver_help_link('help.html#PageTemplates',__('Help for Weaver Page Templates',WEAVER_TRANSADMIN));
	echo '<span style="float:right;">(This Page\'s ID: '; the_ID() ; echo ')</span>';
	weaver_html_br();
	_e('Please click the (?) for more information about all the Weaver Page Templates.',WEAVER_TRANSADMIN);
	echo("</p><p>\n");
	_e("<strong>Per Page Options</strong>",WEAVER_TRANSADMIN);
	weaver_help_link('help.html#optsperpage', __('Help for Per Page Options',WEAVER_TRANSADMIN));
	weaver_html_br();
	_e("These settings let you hide various elements on a per page basis.", WEAVER_TRANSADMIN);
	weaver_html_br();

	weaver_page_checkbox('ttw-hide-page-title',__('Hide Page Title', WEAVER_TRANSADMIN));
	weaver_page_checkbox('ttw-hide-site-title',__('Hide Site Title/Description', WEAVER_TRANSADMIN));
	weaver_page_checkbox('ttw-hide-menus',__('Hide Menus', WEAVER_TRANSADMIN));
	weaver_page_checkbox('ttw-hide-header-image',__('Hide Standard Header Image', WEAVER_TRANSADMIN));
	weaver_html_br();
	weaver_page_checkbox('ttw-hide-header',__('Hide Entire Header', WEAVER_TRANSADMIN));
	weaver_page_checkbox('ttw-hide-footer',__('Hide Entire Footer', WEAVER_TRANSADMIN));
	weaver_page_checkbox('ttw_hide_sidebars',__('Hide Sidebars', WEAVER_TRANSADMIN));
	weaver_page_checkbox('ttw-hide-on-menu',__('Hide Page on the Primary Menu',WEAVER_TRANSADMIN));
	weaver_html_br();
	weaver_page_checkbox('ttw-stay-on-page',__('Menu "Placeholder" page. Useful for top-level menu item - don\'t go anywhere when menu item is clicked.',WEAVER_TRANSADMIN));
	weaver_html_br();
	weaver_page_checkbox('hide_visual_editor',__('Disable Visual Editor for this page. Useful if you enter simple HTML or other code.',WEAVER_TRANSADMIN));
	if (weaver_allow_multisite()) {
	    weaver_html_br();
	    weaver_page_checkbox('wvr_raw_html',__('Allow Raw HTML and scripts. Disables auto paragraph, texturize, and other processing.',WEAVER_TRANSADMIN));
	}
	weaver_html_br();
	weaver_html_br();

	if (function_exists( 'weaver_plus_plugin' ) ) {	// add option only if weaver plus installed
	    weaver_plus_per_page_opts();
	}

	_e("<strong>Selective Display of Widget Areas</strong><br />
	These settings let you hide display of widget areas that would normally be displayed for a given page template. (Note that
	different page templates don't necessarily display the same widget areas.)", WEAVER_TRANSADMIN);
	weaver_html_br();
	weaver_page_checkbox('hide-primary-widget-area',__('Hide Primary Area', WEAVER_TRANSADMIN));
	weaver_page_checkbox('hide-secondary-widget-area',__('Hide Secondary Area', WEAVER_TRANSADMIN));
	weaver_page_checkbox('top-widget-area',__('Hide Top Area', WEAVER_TRANSADMIN));
	weaver_page_checkbox('bottom-widget-area',__('Hide Bottom Area', WEAVER_TRANSADMIN));
	weaver_html_br();
	weaver_page_checkbox('sitewide-top-widget-area',__('Hide Sitewide Top Area', WEAVER_TRANSADMIN));
	weaver_page_checkbox('sitewide-bottom-widget-area',__('Hide Sitewide Bottom Area', WEAVER_TRANSADMIN));
	?>
	<br />
	Use Weaver Advanced Options tab to define additional Per Page Widget areas to use here.
	<?php weaver_help_link('help.html#PPWidgets',__('Help for Per Page Widget Areas',WEAVER_TRANSADMIN)); ?>
	<br />
	<input type="text" size="15" id="ttw_show_extra_areas" name="ttw_show_extra_areas"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_show_extra_areas", true)); ?>" />
	<?php _e("<em>Additional Top Widget Area</em> - Enter name of a Per Page Widget Top Area to display.", WEAVER_TRANSADMIN); ?> <br />

	<input type="text" size="15" id="ttw_show_replace_primary" name="ttw_show_replace_primary"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_show_replace_primary", true)); ?>" />
	<?php _e("<em>Primary Replacement</em> - Enter name of a Per Page Widget Area to replace the standard Primary area.", WEAVER_TRANSADMIN); ?> <br />

	<input type="text" size="15" id="ttw_show_replace_secondary" name="ttw_show_replace_secondary"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_show_replace_secondary", true)); ?>" />
	<?php _e("<em>Secondary Replacement</em> - Enter name of a Per Page Widget Area to replace the standard Secondary area.", WEAVER_TRANSADMIN); ?> <br />

	<input type="text" size="15" id="ttw_show_replace_alternative" name="ttw_show_replace_alternative"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_show_replace_alternative", true)); ?>" />
	<?php _e("<em>Alternative Replacement</em> - Enter name of a Per Page Widget Area to replace the Alternative area on alt-left and alt-right template pages.", WEAVER_TRANSADMIN); ?> <br />


	<?php // No need to hide other widget areas - it would make no sense to hide the alt widget area, for example ?>
</p>
<p>
	<?php _e('<strong>Settings for "Page with Posts" Templates</strong>',WEAVER_TRANSADMIN);
	weaver_help_link('help.html#PerPostTemplate',__('Help for Page with Posts Templates',WEAVER_TRANSADMIN) );
	?>
	<br />
	<?php _e('These settings are optional, and can filter which posts are displayed when you use one of the "Page
	with Posts" templates. The settings will be combined for the final filtered list of posts displayed.
	(If you make mistakes in your settings, it won\'t be apparent until you display the page.)',WEAVER_TRANSADMIN); ?><br />


	<input type="text" size="30" id="ttw_category" name="ttw_category"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_category", true)); ?>" />
	<?php _e("<em>Category</em> - Enter list of category slugs of posts to include. (-slug will exclude specified category)", WEAVER_TRANSADMIN); ?> <br />

	<input type="text" size="30" id="ttw_tag" name="ttw_tag"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_tag", true)); ?>" />
	<?php _e("<em>Tags</em> - Enter list of tag slugs of posts to include.", WEAVER_TRANSADMIN); ?> <br />

	<input type="text" size="30" id="ttw_onepost" name="ttw_onepost"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_onepost", true)); ?>" />
	<?php _e("<em>Single Post</em> - Enter post slug of a single post to display.", WEAVER_TRANSADMIN); ?> <br />

	<input type="text" size="30" id="ttw_orderby" name="ttw_orderby"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_orderby", true)); ?>" />
	<?php _e("<em>Order by</em> - Enter method to order posts by: author, date, title, or rand.", WEAVER_TRANSADMIN); ?> <br />

	<input type="text" size="30" id="ttw_order" name="ttw_order"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_order", true)); ?>" />
	<?php _e("<em>Sort order</em> - Enter ASC or DESC for sort order.", WEAVER_TRANSADMIN); ?> <br />

	<input type="text" size="30" id="ttw_posts_per_page" name="ttw_posts_per_page"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_posts_per_page", true)); ?>" />
	<?php _e("<em>Posts per Page</em> - Enter maximum number of posts per page.", WEAVER_TRANSADMIN); ?> <br />

	<input type="text" size="30" id="ttw_author" name="ttw_author"
	value="<?php echo weaver_esc_textarea(get_post_meta($post->ID, "ttw_author", true)); ?>" />
	<?php _e('<em>Author</em> - Enter author (use username, including spaces)', WEAVER_TRANSADMIN); ?> <br />

	<?php weaver_page_checkbox('ttw_hide_sticky',__('Hide Sticky Posts', WEAVER_TRANSADMIN)); ?>
	<?php weaver_page_checkbox('ttw_hide_pp_infotop',__('Hide top info line', WEAVER_TRANSADMIN)); ?>
	<?php weaver_page_checkbox('ttw_hide_pp_infobot',__('Hide bottom info line', WEAVER_TRANSADMIN)); ?>
</p>
<p>
	<?php _e('<em>Note:</em> when you add settings for the page here, values will be created and displayed in the "Custom Fields" box.', WEAVER_TRANSADMIN);
	weaver_html_br();
	_e('Other per page options (Manually define a specified <em>Custom Field Name</em> and <em>Value</em>):',WEAVER_TRANSADMIN);
	weaver_help_link('help.html#ExtraPP', __('Help for Extra Per Page Options',WEAVER_TRANSADMIN));
	weaver_html_br();
	_e('Define <em>page-head-code</em>, and the value contents will be added to the &lt;HEAD&gt; section. Include &lt;style>...&lt;/style> if adding CSS.', WEAVER_TRANSADMIN);
	weaver_html_br();
	_e('Define <em>page-pre-header-code</em>, and the value contents will be inserted before the &lt;header&gt; div.', WEAVER_TRANSADMIN);
	weaver_html_br();
	_e('Define <em>page-header-insert-code</em>, and the value content will be inserted above the header image.',WEAVER_TRANSADMIN);
	weaver_html_br();
	_e('These areas also supported: <em>page-postheader-code</em>, <em>page-presidebar-code</em>, <em>page-prefooter-code</em>, and <em>page-postfooter-code</em>.',WEAVER_TRANSADMIN)
	?>
</p>
	<input type='hidden' id='ttw_post_meta' name='ttw_post_meta' value='ttw_post_meta'/>
<?php
}

function weaver_post_extras() {
	global $post; ?>
<p>
	<?php
	_e("<strong>Per Post Options</strong>",WEAVER_TRANSADMIN);
	weaver_help_link('help.html#PerPage', __('Help for Per Post Options',WEAVER_TRANSADMIN));
	weaver_html_br();
	_e("These settings let you control display of this individual post.", WEAVER_TRANSADMIN);
	weaver_html_br();
	weaver_page_checkbox('ttw-force-post-full',__('Display as full post where normally excerpted.', WEAVER_TRANSADMIN));
	weaver_page_checkbox('ttw-force-post-excerpt',__('Display post excerpt on main blog pages', WEAVER_TRANSADMIN));
	weaver_html_br();
	weaver_page_checkbox('ttw-show-featured',__('Show Featured Image with post', WEAVER_TRANSADMIN));
	// Can't add an option to hide featured in header per post because we don't know the post at header time.
	weaver_html_br();
	weaver_page_checkbox('ttw-show-post-avatar',__('Show author avatar with post', WEAVER_TRANSADMIN));
	weaver_page_checkbox('ttw-favorite-post',__('Mark as a favorite post (adds star to title)', WEAVER_TRANSADMIN));
	weaver_html_br();
	weaver_page_checkbox('ttw_hide_sidebars',__('Hide Sidebars when this post displayed on Single Post page.', WEAVER_TRANSADMIN));
	weaver_html_br();
	weaver_page_checkbox('hide_visual_editor',__('Disable Visual Editor for this page. Useful if you enter simple HTML or other code.',WEAVER_TRANSADMIN));
	if (weaver_allow_multisite()) {
	    weaver_html_br();
	    weaver_page_checkbox('wvr_raw_html',__('Allow Raw HTML and scripts. Disables auto paragraph, texturize, and other processing.',WEAVER_TRANSADMIN));
	}

	if (function_exists( 'weaver_plus_plugin' ) ) {	// add option sonly if weaver plus installed
	    weaver_plus_per_post_opts();
	}
	?>
</p>
<p>
	<?php _e('The above settings are not used by the [weaver_show_posts] shortcode.', WEAVER_TRANSADMIN); ?><br />
	<?php _e('<strong>Per Post Style</strong>',WEAVER_TRANSADMIN);
		weaver_help_link('help.html#perpoststyle', __('Help for Per Post Style',WEAVER_TRANSADMIN));?> <br />
	<?php _e("Enter optional per post CSS style rules. <strong>Do not</strong> include the &lt;style> and &lt;/style> tags.
	    Include the {}'s. Don't use class names if rules apply to whole post, but do include class names
	    (e.g., <em>.entry-title a</em>) for specific elements. Custom styles will not be displayed by the Post Editor."); ?> <br />
	<textarea name="ttw_per_post_style" rows=2 style="width: 95%"><?php echo(get_post_meta($post->ID, "ttw_per_post_style", true)); ?></textarea>
	<br>
	<?php _e('<strong>Per Post Format</strong>',WEAVER_TRANSADMIN);
	weaver_help_link('help.html#gallerypost', __('Help for Per Post Format',WEAVER_TRANSADMIN));
	weaver_html_br();
	_e('Weaver supports two post formats: Standard and Gallery. Click the ? for more info.',WEAVER_TRANSADMIN);
	weaver_html_br();

	_e('<em>Note:</em> when you add settings for the post here, values will be created and displayed in the "Custom Fields" box.', WEAVER_TRANSADMIN); ?>
</p>
	<input type='hidden' id='ttw_post_meta' name='ttw_post_meta' value='ttw_post_meta'/>

<?php
}

function weaver_save_post_fields($post_id) {
    $default_post_fields = array('ttw_category', 'ttw_tag', 'ttw_onepost', 'ttw_orderby', 'ttw_order', 'ttw_author', 'ttw_posts_per_page', 'ttw_author',
	'hide-primary-widget-area','hide-secondary-widget-area','top-widget-area','bottom-widget-area','sitewide-top-widget-area','sitewide-bottom-widget-area',
	'ttw-hide-page-title','ttw-hide-site-title','ttw-hide-menus','ttw-hide-header-image','ttw-hide-footer','ttw-hide-header','ttw_hide_sticky',
	'ttw-force-post-full','ttw-force-post-excerpt','ttw-show-post-avatar','ttw-favorite-post','ttw_show_extra_areas','ttw_hide_sidebars',
	'ttw_show_replace_primary','ttw_show_replace_secondary','ttw-show-featured','ttw-hide-featured-header','ttw-stay-on-page', 'ttw-hide-on-menu',
	'ttw_hide_pp_infotop','ttw_hide_pp_infobot','ttw_show_replace_alternative', 'ttw_per_post_style', 'hide_visual_editor'
	);
if (weaver_allow_multisite()) {
	array_push($default_post_fields, 'wvr_raw_html');
}

    if (function_exists( 'weaver_plus_plugin' ) ) {	// add option only if weaver plus installed
	$all_post_fields = array_merge($default_post_fields, weaver_plus_add_per_opts_list());
    }  else {
	$all_post_fields = $default_post_fields;
    }


    if (isset($_POST['ttw_post_meta'])) {
        foreach ($all_post_fields as $post_field) {
	    if (isset($_POST[$post_field])) {
                $data = stripslashes($_POST[$post_field]);
		if ($post_field == 'ttw_show_extra_areas' || $post_field == 'ttw_show_replace_primary' ||
		    $post_field == 'ttw_show_replace_secondary' || $post_field == 'ttw_show_replace_alternative' ) {
		    $data = strtolower($data);	// force to lower case
		}
                if (get_post_meta($post_id, $post_field) == '') {
                    add_post_meta($post_id, $post_field, weaver_filter_textarea($data), true);
                }
                else if ($data != get_post_meta($post_id, $post_field, true)) {
                    update_post_meta($post_id, $post_field, weaver_filter_textarea($data));
                }
		else if ($data == '') {
                    delete_post_meta($post_id, $post_field, get_post_meta($post_id, $post_field, true));
		}
	    } else {
		delete_post_meta($post_id, $post_field, get_post_meta($post_id, $post_field, true));
	    }
        }
    }
}

add_action("save_post", "weaver_save_post_fields");
add_action("publish_post", "weaver_save_post_fields");
?>
