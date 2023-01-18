<?php

//----------------------------------------------------------------------------------
//	Include all required files
//----------------------------------------------------------------------------------
require_once(trailingslashit(get_template_directory()) . 'theme-options.php');
require_once(trailingslashit(get_template_directory()) . 'inc/customizer.php');
require_once(trailingslashit(get_template_directory()) . 'inc/last-updated-meta-box.php');
require_once(trailingslashit(get_template_directory()) . 'inc/review.php');
require_once(trailingslashit(get_template_directory()) . 'inc/scripts.php');
// TGMP
require_once(trailingslashit(get_template_directory()) . 'tgm/class-tgm-plugin-activation.php');

function ct_period_register_required_plugins()
{
    $plugins = array(

        array(
            'name'      => 'Independent Analytics',
            'slug'      => 'independent-analytics',
            'required'  => false,
        ),
    );
    
    $config = array(
        'id'           => 'ct-period',
        'default_path' => '',
        'menu'         => 'tgmpa-install-plugins',
        'has_notices'  => true,
        'dismissable'  => true,
        'dismiss_msg'  => '',
        'is_automatic' => false,
        'message'      => '',
        'strings'      => array(
            'page_title'                      => __('Install Recommended Plugins', 'period'),
            'menu_title'                      => __('Recommended Plugins', 'period'),
            'notice_can_install_recommended'     => _n_noop(
                'The makers of the Period theme now recommend installing Independent Analytics, their new plugin for visitor tracking: %1$s.',
                'The makers of the Period theme now recommend installing Independent Analytics, their new plugin for visitor tracking: %1$s.',
                'period'
            ),
        )
    );

    tgmpa($plugins, $config);
}
add_action('tgmpa_register', 'ct_period_register_required_plugins');


//----------------------------------------------------------------------------------
//	Include review request
//----------------------------------------------------------------------------------
require_once(trailingslashit(get_template_directory()) . 'dnh/handler.php');
new WP_Review_Me(
    array(
        'days_after' => 14,
        'type'       => 'theme',
        'slug'       => 'period',
        'message'    => __('Hey! Sorry to interrupt, but you\'ve been using Period for a little while now. If you\'re happy with this theme, could you take a minute to leave a review? <i>You won\'t see this notice again after closing it.</i>', 'period')
    )
);

if (! function_exists(('ct_period_set_content_width'))) {
    function ct_period_set_content_width()
    {
        if (! isset($content_width)) {
            $content_width = 891;
        }
    }
}
add_action('after_setup_theme', 'ct_period_set_content_width', 0);

if (! function_exists(('ct_period_theme_setup'))) {
    function ct_period_theme_setup()
    {
        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption'
        ));
        add_theme_support('infinite-scroll', array(
            'container' => 'loop-container',
            'footer'    => 'overflow-container',
            'render'    => 'ct_period_infinite_scroll_render'
        ));

        // Gutenberg - wide & full images
        add_theme_support('align-wide');

        // Gutenberg - add support for editor styles
        add_theme_support('editor-styles');

        // Gutenberg - modify the font sizes
        add_theme_support('editor-font-sizes', array(
            array(
                    'name' => __('small', 'period'),
                    'shortName' => __('S', 'period'),
                    'size' => 12,
                    'slug' => 'small'
            ),
            array(
                    'name' => __('regular', 'period'),
                    'shortName' => __('M', 'period'),
                    'size' => 16,
                    'slug' => 'regular'
            ),
            array(
                    'name' => __('large', 'period'),
                    'shortName' => __('L', 'period'),
                    'size' => 21,
                    'slug' => 'large'
            ),
            array(
                    'name' => __('larger', 'period'),
                    'shortName' => __('XL', 'period'),
                    'size' => 28,
                    'slug' => 'larger'
            )
    ));

        register_nav_menus(array(
            'primary' => esc_html__('Primary', 'period')
        ));

        // Add WooCommerce support
        add_theme_support('woocommerce');
        // Add support for WooCommerce image gallery features
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');

        load_theme_textdomain('period', get_template_directory() . '/languages');
    }
}
add_action('after_setup_theme', 'ct_period_theme_setup', 10);

//-----------------------------------------------------------------------------
// Load custom stylesheet for the post editor
//-----------------------------------------------------------------------------
if (! function_exists('ct_period_add_editor_styles')) {
    function ct_period_add_editor_styles()
    {
        add_editor_style('styles/editor-style.css');
    }
}
add_action('admin_init', 'ct_period_add_editor_styles');

if (! function_exists(('ct_period_register_widget_areas'))) {
    function ct_period_register_widget_areas()
    {
        register_sidebar(array(
            'name'          => esc_html__('Primary Sidebar', 'period'),
            'id'            => 'primary',
            'description'   => esc_html__('Widgets in this area will be shown in the sidebar next to the main post content', 'period'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>'
        ));
    }
}
add_action('widgets_init', 'ct_period_register_widget_areas');

if (! function_exists(('ct_period_customize_comments'))) {
    function ct_period_customize_comments($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        global $post; ?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<div class="comment-author">
				<?php
                echo get_avatar(get_comment_author_email(), 48, '', get_comment_author()); ?>
				<div class="comment-meta">
					<span class="author-name"><?php comment_author_link(); ?></span>
					<span class="comment-date"><?php comment_date(); ?></span>
				</div>
			</div>
			<div class="comment-content">
				<?php if ($comment->comment_approved == '0') : ?>
					<em><?php esc_html_e('Your comment is awaiting moderation.', 'period') ?></em>
					<br/>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>
			<div class="comment-footer">
				<?php comment_reply_link(array_merge($args, array(
                    'reply_text' => esc_html_x('Reply', 'verb: reply to this comment', 'period'),
                    'depth'      => $depth,
                    'max_depth'  => $args['max_depth'],
                    'before'     => '<i class="fas fa-reply"></i>'
                ))); ?>
				<?php edit_comment_link(esc_html_x('Edit', 'verb: edit this comment', 'period'), '<i class="fas fa-edit"></i>'); ?>
			</div>
		</article>
		<?php
    }
}

if (! function_exists('ct_period_update_fields')) {
    function ct_period_update_fields($fields)
    {
        $commenter = wp_get_current_commenter();
        $req       = get_option('require_name_email');
        $label     = $req ? '*' : ' ' . esc_html__('(optional)', 'period');
        $aria_req  = $req ? "aria-required='true'" : '';

        $fields['author'] =
            '<p class="comment-form-author">
	            <label for="author">' . esc_html_x("Name", "noun", "period") . $label . '</label>
	            <input id="author" name="author" type="text" placeholder="' . esc_attr__("Jane Doe", "period") . '" value="' . esc_attr($commenter['comment_author']) .
            '" size="30" ' . $aria_req . ' />
	        </p>';

        $fields['email'] =
            '<p class="comment-form-email">
	            <label for="email">' . esc_html_x("Email", "noun", "period") . $label . '</label>
	            <input id="email" name="email" type="email" placeholder="' . esc_attr__("name@email.com", "period") . '" value="' . esc_attr($commenter['comment_author_email']) .
            '" size="30" ' . $aria_req . ' />
	        </p>';

        $fields['url'] =
            '<p class="comment-form-url">
	            <label for="url">' . esc_html__("Website", "period") . '</label>
	            <input id="url" name="url" type="url" placeholder="http://google.com" value="' . esc_attr($commenter['comment_author_url']) .
            '" size="30" />
	            </p>';

        return $fields;
    }
}
add_filter('comment_form_default_fields', 'ct_period_update_fields');

if (! function_exists('ct_period_update_comment_field')) {
    function ct_period_update_comment_field($comment_field)
    {

        // don't filter the WooCommerce review form
        if (function_exists('is_woocommerce')) {
            if (is_woocommerce()) {
                return $comment_field;
            }
        }
        
        $comment_field =
            '<p class="comment-form-comment">
	            <label for="comment">' . esc_html_x("Comment", "noun", "period") . '</label>
	            <textarea required id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
	        </p>';

        return $comment_field;
    }
}
add_filter('comment_form_field_comment', 'ct_period_update_comment_field', 7);

if (! function_exists('ct_period_remove_comments_notes_after')) {
    function ct_period_remove_comments_notes_after($defaults)
    {
        $defaults['comment_notes_after'] = '';
        return $defaults;
    }
}
add_action('comment_form_defaults', 'ct_period_remove_comments_notes_after');

if (! function_exists('ct_period_filter_read_more_link')) {
    function ct_period_filter_read_more_link($custom = false)
    {
        if (is_feed()) {
            return;
        }
        global $post;
        $ismore             = strpos($post->post_content, '<!--more-->');
        $read_more_text     = get_theme_mod('read_more_text');
        $new_excerpt_length = get_theme_mod('excerpt_length');
        $excerpt_more       = ($new_excerpt_length === 0) ? '' : '&#8230;';
        $output = '';

        // add ellipsis for automatic excerpts
        if (empty($ismore) && $custom !== true) {
            $output .= $excerpt_more;
        }
        // Because i18n text cannot be stored in a variable
        if (empty($read_more_text)) {
            $output .= '<div class="more-link-wrapper"><a class="more-link" href="' . esc_url(get_permalink()) . '">' . esc_html__('Continue reading', 'period') . '<span class="screen-reader-text">' . esc_html(get_the_title()) . '</span></a></div>';
        } else {
            $output .= '<div class="more-link-wrapper"><a class="more-link" href="' . esc_url(get_permalink()) . '">' . esc_html($read_more_text) . '<span class="screen-reader-text">' . esc_html(get_the_title()) . '</span></a></div>';
        }
        return $output;
    }
}
add_filter('the_content_more_link', 'ct_period_filter_read_more_link'); // more tags
add_filter('excerpt_more', 'ct_period_filter_read_more_link', 10); // automatic excerpts

// handle manual excerpts
if (! function_exists('ct_period_filter_manual_excerpts')) {
    function ct_period_filter_manual_excerpts($excerpt)
    {
        $excerpt_more = '';
        if (has_excerpt()) {
            $excerpt_more = ct_period_filter_read_more_link(true);
        }
        return $excerpt . $excerpt_more;
    }
}
add_filter('get_the_excerpt', 'ct_period_filter_manual_excerpts');

if (! function_exists('ct_period_excerpt')) {
    function ct_period_excerpt()
    {
        global $post;
        $show_full_post = get_theme_mod('full_post');
        $ismore         = strpos($post->post_content, '<!--more-->');

        if ($show_full_post === 'yes' || $ismore) {
            the_content();
        } else {
            the_excerpt();
        }
    }
}

if (! function_exists('ct_period_custom_excerpt_length')) {
    function ct_period_custom_excerpt_length($length)
    {
        $new_excerpt_length = get_theme_mod('excerpt_length');

        if (! empty($new_excerpt_length) && $new_excerpt_length != 25) {
            return $new_excerpt_length;
        } elseif ($new_excerpt_length === 0) {
            return 0;
        } else {
            return 25;
        }
    }
}
add_filter('excerpt_length', 'ct_period_custom_excerpt_length', 99);

if (! function_exists('ct_period_remove_more_link_scroll')) {
    function ct_period_remove_more_link_scroll($link)
    {
        $link = preg_replace('|#more-[0-9]+|', '', $link);
        return $link;
    }
}
add_filter('the_content_more_link', 'ct_period_remove_more_link_scroll');

// Yoast OG description has "Continue readingTitle of the Post" due to its use of get_the_excerpt(). This fixes that.
function ct_period_update_yoast_og_description($ogdesc)
{
    $read_more_text = get_theme_mod('read_more_text');
    if (empty($read_more_text)) {
        $read_more_text = esc_html__('Continue reading', 'period');
    }
    $ogdesc = substr($ogdesc, 0, strpos($ogdesc, $read_more_text));

    return $ogdesc;
}
add_filter('wpseo_opengraph_desc', 'ct_period_update_yoast_og_description');

if (! function_exists('ct_period_featured_image')) {
    function ct_period_featured_image()
    {
        global $post;
        $featured_image = '';

        if (has_post_thumbnail($post->ID)) {
            if (is_singular()) {
                $featured_image = '<div class="featured-image">' . get_the_post_thumbnail($post->ID, 'full') . '</div>';
            } else {
                $featured_image = '<div class="featured-image"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . get_the_post_thumbnail($post->ID, 'full') . '</a></div>';
            }
        }

        $featured_image = apply_filters('ct_period_featured_image', $featured_image);

        if ($featured_image) {
            echo $featured_image;
        }
    }
}

if (! function_exists('ct_period_social_array')) {
    function ct_period_social_array()
    {
        $social_sites = array(
            'twitter'       => 'period_twitter_profile',
            'facebook'      => 'period_facebook_profile',
            'instagram'     => 'period_instagram_profile',
            'tiktok'     	=> 'period_tiktok_profile',
            'linkedin'      => 'period_linkedin_profile',
            'pinterest'     => 'period_pinterest_profile',
            'youtube'       => 'period_youtube_profile',
            'rss'           => 'period_rss_profile',
            'email'         => 'period_email_profile',
            'phone'			=> 'period_phone_profile',
            'email-form'    => 'period_email_form_profile',
            'amazon'        => 'period_amazon_profile',
            'artstation'    => 'period_artstation_profile',
            'bandcamp'      => 'period_bandcamp_profile',
            'behance'       => 'period_behance_profile',
            'bitbucket'     => 'period_bitbucket_profile',
            'codepen'       => 'period_codepen_profile',
            'delicious'     => 'period_delicious_profile',
            'deviantart'    => 'period_deviantart_profile',
            'diaspora'      => 'period_diaspora_profile',
            'digg'          => 'period_digg_profile',
            'discord'    	=> 'period_discord_profile',
            'dribbble'      => 'period_dribbble_profile',
            'etsy'          => 'period_etsy_profile',
            'flickr'        => 'period_flickr_profile',
            'foursquare'    => 'period_foursquare_profile',
            'github'        => 'period_github_profile',
            'goodreads'		=> 'period_goodreads_profile',
            'google-wallet' => 'period_google_wallet_profile',
            'hacker-news'   => 'period_hacker-news_profile',
            'imdb'   		=> 'period_imdb_profile',
            'mastodon'      => 'period_mastodon_profile',
            'medium'        => 'period_medium_profile',
            'meetup'        => 'period_meetup_profile',
            'mixcloud'      => 'period_mixcloud_profile',
            'ok-ru'         => 'period_ok_ru_profile',
            'orcid'         => 'period_orcid_profile',
            'patreon'       => 'period_patreon_profile',
            'paypal'        => 'period_paypal_profile',
            'pocket'        => 'period_pocket_profile',
            'podcast'       => 'period_podcast_profile',
            'qq'            => 'period_qq_profile',
            'quora'         => 'period_quora_profile',
            'ravelry'       => 'period_ravelry_profile',
            'reddit'        => 'period_reddit_profile',
            'researchgate'  => 'period_researchgate_profile',
            'skype'         => 'period_skype_profile',
            'slack'         => 'period_slack_profile',
            'slideshare'    => 'period_slideshare_profile',
            'soundcloud'    => 'period_soundcloud_profile',
            'spotify'       => 'period_spotify_profile',
            'snapchat'      => 'period_snapchat_profile',
            'stack-overflow' => 'period_stack_overflow_profile',
            'steam'         => 'period_steam_profile',
            'strava'   		=> 'period_strava_profile',
            'stumbleupon'   => 'period_stumbleupon_profile',
            'telegram'      => 'period_telegram_profile',
            'tencent-weibo' => 'period_tencent_weibo_profile',
            'tumblr'        => 'period_tumblr_profile',
            'twitch'        => 'period_twitch_profile',
            'untappd'       => 'period_untappd_profile',
            'vimeo'         => 'period_vimeo_profile',
            'vine'          => 'period_vine_profile',
            'vk'            => 'period_vk_profile',
            'wechat'        => 'period_wechat_profile',
            'weibo'         => 'period_weibo_profile',
            'whatsapp'      => 'period_whatsapp_profile',
            'xing'          => 'period_xing_profile',
            'yahoo'         => 'period_yahoo_profile',
            'yelp'          => 'period_yelp_profile',
            '500px'         => 'period_500px_profile',
            'social_icon_custom_1' => 'social_icon_custom_1_profile',
            'social_icon_custom_2' => 'social_icon_custom_2_profile',
            'social_icon_custom_3' => 'social_icon_custom_3_profile'
        );

        return apply_filters('ct_period_social_array_filter', $social_sites);
    }
}

//----------------------------------------------------------------------------------
//	Output the social media icons
//----------------------------------------------------------------------------------
if (! function_exists('ct_period_social_icons_output')) {
    function ct_period_social_icons_output()
    {

        // Get the social icons array
        $social_sites = ct_period_social_array();
        // Store only icons with URLs saved
        $saved = array();

        /* Store the site name and ID if saved
        /* name: twitter
        /* id: period_twitter_profile */
        foreach ($social_sites as $name => $id) {
            if (strlen(get_theme_mod($name)) > 0) {
                $saved[ $name ] = $id;
            }
        }

        // If there are any social profiles saved
        if (!empty($saved)) {
            echo "<ul class='social-media-icons'>";

            // Output list item for every saved profile
            foreach ($saved as $name => $id) {
                if ($name == 'rss') {
                    $class = 'fas fa-rss';
                } elseif ($name == 'email') {
                    $class = 'fas fa-envelope';
                } elseif ($name == 'email-form') {
                    $class = 'far fa-envelope';
                } elseif ($name == 'podcast') {
                    $class = 'fas fa-podcast';
                } elseif ($name == 'ok-ru') {
                    $class = 'fab fa-odnoklassniki';
                } elseif ($name == 'wechat') {
                    $class = 'fab fa-weixin';
                } elseif ($name == 'pocket') {
                    $class = 'fab fa-get-pocket';
                } elseif ($name == 'phone') {
                    $class = 'fas fa-phone';
                } else {
                    $class = 'fab fa-' . $name;
                }

                $url = get_theme_mod($name);
                $title = $name;

                // Escape the URL based on protocol being used
                if ($name == 'email') {
                    $href = 'mailto:' . antispambot(is_email($url));
                    $title = antispambot(is_email($url));
                } elseif ($name == 'skype') {
                    $href = esc_url($url, array( 'http', 'https', 'skype' ));
                } elseif ($name == 'phone') {
                    $href = esc_url($url, array( 'tel' ));
                    $title = esc_url($url, array( 'tel' ));
                } else {
                    $href = esc_url($url);
                }
                // Output the icon
                if ($name == 'social_icon_custom_1' || $name == 'social_icon_custom_2' || $name == 'social_icon_custom_3') { ?>
					<li>
						<a class="custom-icon" target="_blank" href="<?php echo $href; ?>">
							<img class="icon" src="<?php echo esc_url(get_theme_mod($name .'_image')); ?>" style="width: <?php echo absint(get_theme_mod($name . '_size', '20')); ?>px;" alt="<?php echo esc_html(get_theme_mod($name . '_name'));  ?>" />
						</a>
					</li>
				<?php
                } else { ?>
					<li>
						<a class="<?php echo esc_attr($name); ?>" target="_blank" href="<?php echo $href; ?>">
							<i class="<?php echo esc_attr($class); ?>" aria-hidden="true" title="<?php echo esc_attr($title); ?>"></i>
							<span class="screen-reader-text"><?php echo esc_html($title);  ?></span>
						</a>
					</li>
				<?php
                }
            }
            echo "</ul>";
        }
    }
}

/*
 * WP will apply the ".menu-primary-items" class & id to the containing <div> instead of <ul>
 * making styling difficult and confusing. Using this wrapper to add a unique class to make styling easier.
 */
if (! function_exists(('ct_period_wp_page_menu'))) {
    function ct_period_wp_page_menu()
    {
        wp_page_menu(
            array(
                "menu_class" => "menu-unset",
                "depth"      => - 1
            )
        );
    }
}

if (! function_exists(('ct_period_nav_dropdown_buttons'))) {
    function ct_period_nav_dropdown_buttons($item_output, $item, $depth, $args)
    {
        if ($args->theme_location == 'primary') {
            if (in_array('menu-item-has-children', $item->classes) || in_array('page_item_has_children', $item->classes)) {
                $item_output = str_replace($args->link_after . '</a>', $args->link_after . '</a><button class="toggle-dropdown" aria-expanded="false" name="toggle-dropdown"><span class="screen-reader-text">' . esc_html_x("open dropdown menu", "verb: open the dropdown menu", "period") . '</span><span class="arrow"></span></button>', $item_output);
            }
        }

        return $item_output;
    }
}
add_filter('walker_nav_menu_start_el', 'ct_period_nav_dropdown_buttons', 10, 4);

if (! function_exists(('ct_period_sticky_post_marker'))) {
    function ct_period_sticky_post_marker()
    {
        if (is_sticky() && !is_archive() && !is_search()) {
            echo '<div class="sticky-status"><span>' . esc_html__("Featured", "period") . '</span></div>';
        }
    }
}
add_action('sticky_post_status', 'ct_period_sticky_post_marker');

if (! function_exists(('ct_period_reset_customizer_options'))) {
    function ct_period_reset_customizer_options()
    {
        if (empty($_POST['period_reset_customizer']) || 'period_reset_customizer_settings' !== $_POST['period_reset_customizer']) {
            return;
        }

        if (! wp_verify_nonce($_POST['period_reset_customizer_nonce'], 'period_reset_customizer_nonce')) {
            return;
        }

        if (! current_user_can('edit_theme_options')) {
            return;
        }

        $mods_array = array(
            'logo_upload',
            'search_bar',
            'layout',
            'layout_pages',
            'layout_blog',
            'layout_archives',
            'full_post',
            'excerpt_length',
            'read_more_text',
            'display_post_author',
            'display_post_date',
            'last_updated',
            'custom_css'
        );

        $social_sites = ct_period_social_array();

        // add social site settings to mods array
        foreach ($social_sites as $social_site => $value) {
            $mods_array[] = $social_site;
        }

        $mods_array = apply_filters('ct_period_mods_to_remove', $mods_array);

        foreach ($mods_array as $theme_mod) {
            remove_theme_mod($theme_mod);
        }

        $redirect = admin_url('themes.php?page=period-options');
        $redirect = add_query_arg('period_status', 'deleted', $redirect);

        // safely redirect
        wp_safe_redirect($redirect);
        exit;
    }
}
add_action('admin_init', 'ct_period_reset_customizer_options');

if (! function_exists(('ct_period_delete_settings_notice'))) {
    function ct_period_delete_settings_notice()
    {
        if (isset($_GET['period_status'])) {
            if ($_GET['period_status'] == 'deleted') {
                ?>
				<div class="updated">
					<p><?php esc_html_e('Customizer settings deleted.', 'period'); ?></p>
				</div>
				<?php
            }
        }
    }
}
add_action('admin_notices', 'ct_period_delete_settings_notice');

if (! function_exists(('ct_period_body_class'))) {
    function ct_period_body_class($classes)
    {
        global $post;
        $full_post 					= get_theme_mod('full_post');
        $post_layout    		= get_theme_mod('layout');
        $page_layout    		= get_theme_mod('layout_pages');
        $blog_layout    		= get_theme_mod('layout_blog');
        $archives_layout    = get_theme_mod('layout_archives');

        if ($full_post == 'yes') {
            $classes[] = 'full-post';
        }
        if (!empty($post_layout) && is_singular('post')) {
            $classes[] = $post_layout . '-sidebar';
        }
        if (!empty($page_layout) && is_singular('page')) {
            $classes[] = $page_layout . '-sidebar';
        }
        if (!empty($blog_layout) && is_home()) {
            $classes[] = $blog_layout . '-sidebar';
        }
        if (!empty($archives_layout) && is_archive()) {
            $classes[] = $archives_layout . '-sidebar';
        }

        return $classes;
    }
}
add_filter('body_class', 'ct_period_body_class');

if (! function_exists(('ct_period_post_class'))) {
    function ct_period_post_class($classes)
    {
        $classes[] = 'entry';

        return $classes;
    }
}
add_filter('post_class', 'ct_period_post_class');

if (! function_exists(('ct_period_custom_css_output'))) {
    function ct_period_custom_css_output()
    {
        if (function_exists('wp_get_custom_css')) {
            $custom_css = wp_get_custom_css();
        } else {
            $custom_css = get_theme_mod('custom_css');
        }
        $logo_size  = get_theme_mod('logo_size');

        if ($logo_size != 168 && ! empty($logo_size)) {
            $logo_size_css = '.logo {
							width: ' . $logo_size . 'px;
						  }';
            $custom_css .= $logo_size_css;
        }
        if (get_theme_mod('display_post_author') == 'hide') {
            $custom_css .= '';
        }
        if (get_theme_mod('display_post_date') == 'hide') {
            $custom_css .= '';
        }

        if (! empty($custom_css)) {
            $custom_css = ct_period_sanitize_css($custom_css);

            wp_add_inline_style('ct-period-style', $custom_css);
            wp_add_inline_style('ct-period-style-rtl', $custom_css);
        }
    }
}
add_action('wp_enqueue_scripts', 'ct_period_custom_css_output', 20);

if (! function_exists(('ct_period_svg_output'))) {
    function ct_period_svg_output($type)
    {
        $svg = '';

        if ($type == 'toggle-navigation') {
            $svg = '<svg width="36px" height="23px" viewBox="0 0 36 23" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				    <desc>mobile menu toggle button</desc>
				    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				        <g transform="translate(-142.000000, -104.000000)" fill="#FFFFFF">
				            <g transform="translate(142.000000, 104.000000)">
				                <rect x="0" y="20" width="36" height="3"></rect>
				                <rect x="0" y="10" width="36" height="3"></rect>
				                <rect x="0" y="0" width="36" height="3"></rect>
				            </g>
				        </g>
				    </g>
				</svg>';
        }

        return $svg;
    }
}

if (! function_exists(('ct_period_add_meta_elements'))) {
    function ct_period_add_meta_elements()
    {
        $meta_elements = '';

        $meta_elements .= sprintf('<meta charset="%s" />' . "\n", esc_attr(get_bloginfo('charset')));
        $meta_elements .= '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";

        $theme    = wp_get_theme(get_template());
        $template = sprintf('<meta name="template" content="%s %s" />' . "\n", esc_attr($theme->get('Name')), esc_attr($theme->get('Version')));
        $meta_elements .= $template;

        echo $meta_elements;
    }
}
add_action('wp_head', 'ct_period_add_meta_elements', 1);

if (! function_exists(('ct_period_infinite_scroll_render'))) {
    function ct_period_infinite_scroll_render()
    {
        while (have_posts()) {
            the_post();
            get_template_part('content', 'archive');
        }
    }
}

if (! function_exists('ct_period_get_content_template')) {
    function ct_period_get_content_template()
    {

        // Get bbpress.php for all bbpress pages
        if (function_exists('is_bbpress')) {
            if (is_bbpress()) {
                get_template_part('content/bbpress');
                return;
            }
        }
        if (is_home() || is_archive()) {
            get_template_part('content-archive', get_post_type());
        } else {
            get_template_part('content', get_post_type());
        }
    }
}

// allow skype URIs to be used
if (! function_exists(('ct_period_allow_skype_protocol'))) {
    function ct_period_allow_skype_protocol($protocols)
    {
        $protocols[] = 'skype';

        return $protocols;
    }
}
add_filter('kses_allowed_protocols', 'ct_period_allow_skype_protocol');

//----------------------------------------------------------------------------------
// Add paragraph tags for author bio displayed in content/archive-header.php.
// the_archive_description includes paragraph tags for tag and category descriptions, but not the author bio.
//----------------------------------------------------------------------------------
if (! function_exists('ct_period_modify_archive_descriptions')) {
    function ct_period_modify_archive_descriptions($description)
    {
        if (is_author()) {
            $description = wpautop($description);
        }
        return $description;
    }
}
add_filter('get_the_archive_description', 'ct_period_modify_archive_descriptions');

//----------------------------------------------------------------------------------
// So existing users don't have certain templates revert to the left sidebar
//----------------------------------------------------------------------------------
function ct_period_set_default_layouts()
{
    if (get_option('period_layouts_set') == '') {
        $current_layout = get_theme_mod('layout');
        set_theme_mod('layout_pages', $current_layout);
        set_theme_mod('layout_blog', $current_layout);
        set_theme_mod('layout_archives', $current_layout);
        update_option('period_layouts_set', 'yes');
    }
}
add_action('after_setup_theme', 'ct_period_set_default_layouts');

//----------------------------------------------------------------------------------
// Output the markup for the optional scroll-to-top arrow
//----------------------------------------------------------------------------------
function ct_period_scroll_to_top_arrow()
{
    $setting = get_theme_mod('scroll_to_top');
    
    if ($setting == 'yes') {
        echo '<button id="scroll-to-top" class="scroll-to-top"><span class="screen-reader-text">'. esc_html__('Scroll to the top', 'period') .'</span><i class="fas fa-arrow-up"></i></button>';
    }
}
add_action('body_bottom', 'ct_period_scroll_to_top_arrow');

//----------------------------------------------------------------------------------
// Output the "Last Updated" date on posts
//----------------------------------------------------------------------------------
function ct_period_output_last_updated_date()
{
    global $post;

    if (get_the_modified_date() != get_the_date()) {
        $updated_post = get_post_meta($post->ID, 'ct_period_last_updated', true);
        $updated_customizer = get_theme_mod('last_updated');
        if (
            ($updated_customizer == 'yes' && ($updated_post != 'no'))
            || $updated_post == 'yes'
            ) {
            echo '<p class="last-updated">'. esc_html__("Last updated on", "period") . ' ' . get_the_modified_date() . ' </p>';
        }
    }
}

//----------------------------------------------------------------------------------
// Output standard post pagination
//----------------------------------------------------------------------------------
if (! function_exists(('ct_period_pagination'))) {
    function ct_period_pagination()
    {

    // Never output pagination on bbpress pages
        if (function_exists('is_bbpress')) {
            if (is_bbpress()) {
                return;
            }
        }
        // Output pagination if Jetpack not installed, otherwise check if infinite scroll is active before outputting
        if (!class_exists('Jetpack')) {
            the_posts_pagination(array(
        'prev_text' => esc_html__('Previous', 'period'),
        'next_text' => esc_html__('Next', 'period')
      ));
        } elseif (!Jetpack::is_module_active('infinite-scroll')) {
            the_posts_pagination(array(
        'prev_text' => esc_html__('Previous', 'period'),
        'next_text' => esc_html__('Next', 'period')
      ));
        }
    }
}

//----------------------------------------------------------------------------------
// Add support for Elementor headers & footers
//----------------------------------------------------------------------------------
function ct_period_register_elementor_locations($elementor_theme_manager)
{
    $elementor_theme_manager->register_location('header');
    $elementor_theme_manager->register_location('footer');
}
add_action('elementor/theme/register_locations', 'ct_period_register_elementor_locations');
