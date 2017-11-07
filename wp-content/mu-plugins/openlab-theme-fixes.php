<?php

/**
 * Loads theme fixes for OpenLab site themes
 */
function openlab_load_theme_fixes() {
    $t = get_stylesheet();

    switch ($t) {
        case 'carrington-blog' :
        case 'coraline' :
        case 'filtered' :
        case 'hemingway' :
        case 'herothemetrust' :
        case 'motion' :
        case 'p2' :
        case 'pilcrow' :
        case 'sliding-door' :
        case 'themorningafter' :
        case 'wu-wei' :
        case 'twentyfifteen':
        case 'twentytwelve':
        case 'twentyeleven':
        case 'twentyten':
            echo '<link rel="stylesheet" id="' . $t . '-fixes" type="text/css" media="screen" href="' . get_home_url() . '/wp-content/mu-plugins/theme-fixes/' . $t . '.css" />
';
            break;
    }
}

add_action('wp_print_styles', 'openlab_load_theme_fixes', 9999);

/**
 * Arrange themes so that preferred themes appear first in the list.
 */
function openlab_reorder_theme_selections($themes) {
    $preferred_themes = array(
        'twentyfifteen',
        'filtered',
        'herothemetrust',
        'twentyeleven',
        'twentyfourteen',
        'twentysixteen',
        'twentythirteen',
        'twentytwelve',
    );

    $t1 = $t2 = array();

    foreach ($themes as $theme_name => $theme) {
        if (in_array($theme_name, $preferred_themes, true)) {
            $t1[$theme_name] = $theme;
        } else {
            $t2[$theme_name] = $theme;
        }
    }

    // Sort the $t1 array to match the preferred order.
    uasort($t1, function( $a, $b ) use ( $preferred_themes ) {
        $apos = array_search($a['id'], $preferred_themes);
        $bpos = array_search($b['id'], $preferred_themes);

        return ( $apos < $bpos ) ? -1 : 1;
    });

    return array_merge($t1, $t2);
}

add_filter('wp_prepare_themes_for_js', 'openlab_reorder_theme_selections');

/**
 * Hemingway: When there's no nav menu, ensure that Course Profile and Home links appear.
 *
 * This theme uses wp_list_pages() rather than a normal WP function for building
 * the default menu.
 */
function openlab_fix_fallback_menu_for_hemingway($output, $r, $pages) {
    if ('hemingway' !== get_template()) {
        return $output;
    }

    $dbs = debug_backtrace();
    $gp_key = null;
    foreach ($dbs as $key => $db) {
        if ('wp_list_pages' === $db['function']) {
            $lp_key = $key;
            break;
        }
    }

    if (null === $lp_key) {
        return $output;
    }

    // It really doesn't get any worse than this.
    if (!isset($dbs[$lp_key + 4]) || 'get_header' !== $dbs[$lp_key + 4]['function']) {
        return $output;
    }

    // Fake pages.
    $group_id = openlab_get_group_id_by_blog_id(get_current_blog_id());
    if (!$group_id) {
        return $output;
    }

    $home_link = sprintf(
            '<li><a title="Site Home" href="%s">Home</a></li>', esc_url(trailingslashit(get_option('home')))
    );

    $group_type_label = openlab_get_group_type_label(array(
        'group_id' => $group_id,
        'case' => 'upper',
    ));
    $group_link = bp_get_group_permalink(groups_get_group(array('group_id' => $group_id)));

    $profile_link = sprintf(
            '<li id="menu-item-group-profile-link" class="group-profile-link"><a href="%s">%s</a>', esc_url($group_link), sprintf('%s Profile', $group_type_label)
    );

    $output = $profile_link . "\n" . $home_link . "\n" . $output;

    return $output;
}

add_filter('wp_list_pages', 'openlab_fix_fallback_menu_for_hemingway', 10, 3);

/**
 * Hemingway: Add missing label element to comment form.
 */
function openlab_add_missing_label_element_to_comment_form_for_hemingway($fields) {
    if ('hemingway' !== get_template()) {
        return $fields;
    }

    $fields['comment'] .= '<label for="comment" class="sr-only">Comment Text</label>';

    return $fields;
}

add_filter('comment_form_fields', 'openlab_add_missing_label_element_to_comment_form_for_hemingway');

/**
 * Prevent Sliding Door from showing plugin installation notice.
 */
function openlab_remove_sliding_door_plugin_installation_notice() {
    if ('sliding-door' === get_template()) {
        remove_action('tgmpa_register', 'my_theme_register_required_plugins');
    }
}

add_action('after_setup_theme', 'openlab_remove_sliding_door_plugin_installation_notice', 100);

/**
 * Sliding Door requires the Page Links To plugin.
 */
function openlab_activate_page_links_to_on_sliding_door() {
    if ('sliding-door' !== get_template()) {
        return;
    }

    if (!is_admin() || !current_user_can('activate_plugins')) {
        return;
    }

    if (!function_exists('is_plugin_active')) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    if (!is_plugin_active('page-links-to/page-links-to.php')) {
        activate_plugin('page-links-to/page-links-to.php');
    }
}

add_action('after_setup_theme', 'openlab_activate_page_links_to_on_sliding_door', 50);

/**
 * Override Pilcrow's fallback page menu overrides.
 */
function openlab_pilcrow_page_menu_args($args) {
    remove_filter('wp_page_menu_args', 'pilcrow_page_menu_args');
    $args['depth'] = 0;
    return $args;
}

add_filter('wp_page_menu_args', 'openlab_pilcrow_page_menu_args', 5);

/**
 * Filtering blog info to fix items in theme
 * @param type $output
 * @param type $show
 * @return string
 */
function openlab_theme_fixes_filter_bloginfo($output, $show) {

    $theme = wp_get_theme();

    switch ($theme->get('TextDomain')) {
        case 'twentyeleven':
        case 'twentytwelve':

            /**
             * Targets empty h2s
             * The empty header will be cleaned up client-side
             */
            if (!$output || $output === '' || ctype_space($output)) {

                $output = '<span class="empty-header">Just Another WordPress Site</span>';
                $output .= '<script type="text/javascript">(function ($) { $(".empty-header").addClass("processing"); })(jQuery);</script>';
            }

            break;
    }

    return $output;
}

add_filter('bloginfo', 'openlab_theme_fixes_filter_bloginfo', 10, 2);

/**
 * Targeted enqueues for specific-theme, specific-script fixes
 */
function openlab_theme_fixes_init_actions() {

    /**
     * Targets colorbox to fix accessibility issue where some versions of colorbox
     * output empty buttons on document load
     */
    $dependencies = array('aec_frontend', 'afg_colorbox_js', 'gform_gravityforms');
    $plugins_url = plugins_url('js', __FILE__);

    foreach ($dependencies as $dep) {

        //we'll keep the handle the same so this fix doesn't register twice
        wp_register_script("openlab-colorbox-fixes", "$plugins_url/targeted-theme-fixes/openlab.colorbox.fixes.js", array($dep), '0.0.0.1', true);
        wp_enqueue_script("openlab-colorbox-fixes");
    }
}

add_action('wp_enqueue_scripts', 'openlab_theme_fixes_init_actions', 1000);

/**
 * For instances where get_search_form() is called multiple times in a template
 * This creates mulitple IDs with the same name, which is not semantic and fails
 * WAVE accessibility testing
 * This function uses a global to iterate the searchform IDS
 * @param type $form
 * @return type
 */
function openlab_themes_filter_search_form($form) {

    $template = get_template();

    $relevant_themes = array(
        'coraline',
        'filtered',
        'hemingway',
        'herothemetrust',
        'p2',
        'pilcrow',
        'sliding-door',
        'twentyeleven',
        'twentyten',
        'twentytwelve',
    );

    if (!in_array($template, $relevant_themes)) {
        return $form;
    }

    if (!isset($GLOBALS['twentyeleven_search_form_count'])) {
        $GLOBALS['twentyeleven_search_form_count'] = 1;
    } else {
        $GLOBALS['twentyeleven_search_form_count'] ++;
    }

    $current_form_num = $GLOBALS['twentyeleven_search_form_count'];

    $dom = new DOMDocument;
    $dom->loadHTML($form);
    $all_tags = $dom->getElementsByTagName('*');
    $target_tags = array('form', 'label', 'input');

    foreach ($all_tags as $key => $this_tag) {

        if (!in_array($this_tag->tagName, $target_tags)) {
            continue;
        }

        $legacy_id = $this_tag->getAttribute('id');

        if ($legacy_id) {
            $this_tag->setAttribute('id', $legacy_id . $current_form_num);
            $this_tag->setAttribute('class', $legacy_id);
        }

        $legacy_for = $this_tag->getAttribute('for');

        if ($legacy_for) {
            $this_tag->setAttribute('for', $legacy_for . $current_form_num);
        }
    }

	// Clean up to ensure that a label element exists for each input.
	$input_tags = $dom->getElementsByTagName( 'input' );
	$label_tags = $dom->getElementsByTagName( 'label' );
	foreach ( $input_tags as $input_tag ) {
		$input_type = $input_tag->getAttribute( 'type' );
		if ( 'submit' === $input_type ) {
			continue;
		}

		$input_id = $input_tag->getAttribute( 'id' );
		$input_label = null;
		foreach ( $label_tags as $label_tag ) {
			$label_for = $label_tag->getAttribute( 'for' );
			if ( $label_for === $input_id ) {
				$input_label = $label_tag;
				break;
			}
		}

		if ( ! $input_label ) {
			$new_label = $dom->createElement( 'label', 'Enter search terms' );
			$new_label->setAttribute( 'for', $input_id );
			$new_label->setAttribute( 'class', 'sr-only' );
			$input_tag->parentNode->appendChild( $new_label );
		}
	}

    $form = $dom->saveHTML();

    return $form;
}

add_filter('get_search_form', 'openlab_themes_filter_search_form');
