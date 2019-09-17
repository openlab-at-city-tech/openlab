<?php
/**
 * Theme-specific fixes.
 */

/**
 * Loads CSS theme fixes for OpenLab site themes.
 */
function openlab_load_theme_fixes() {
	$t = get_stylesheet();

	switch ( $t ) {
		case 'carrington-blog':
		case 'coraline':
		case 'education-pro':
		case 'filtered':
		case 'hamilton':
		case 'hemingway':
		case 'herothemetrust':
		case 'koji':
		case 'lingonberry' :
		case 'motion':
		case 'openlab-twentysixteen':
		case 'p2':
		case 'pilcrow':
		case 'sliding-door':
		case 'themorningafter':
		case 'wu-wei':
		case 'twentyseventeen':
		case 'twentysixteen':
		case 'twentyfifteen':
		case 'twentythirteen':
		case 'twentytwelve':
		case 'twentyeleven':
		case 'twentynineteen':
		case 'twentyten':
			echo '<link rel="stylesheet" id="' . esc_attr( $t ) . '-fixes" type="text/css" media="screen" href="' . esc_attr( get_home_url() ) . '/wp-content/mu-plugins/theme-fixes/' . esc_attr( $t ) . '/' . esc_attr( $t ) . '.css" />
';
		break;
	}
}
add_action( 'wp_head', 'openlab_load_theme_fixes', 9999 );

/**
 * Loads PHP-based theme mods for OpenLab site themes.
 */
add_action(
	'after_setup_theme',
	function() {
		$t = get_stylesheet();

		switch ( $t ) {
			case 'education-pro' :
			case 'hamilton':
			case 'hemingway':
			case 'koji':
			case 'pilcrow':
			case 'sliding-door':
			case 'twentynineteen':
				include __DIR__ . '/theme-fixes/' . $t . '/' . $t . '.php';
			break;
		}
	}
);

/**
 * Loads JS-based theme mods for OpenLab site themes.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		$t = get_stylesheet();

		switch ( $t ) {
			// All of the below require only jQuery.
			case 'education-pro' :
			case 'hamilton':
			case 'koji' :
			case 'lingonberry' :
				wp_enqueue_script( $t, content_url( 'mu-plugins/theme-fixes/' . $t . '/' . $t . '.js', array( 'jquery' ) ) );
			break;
		}
	}
);

/**
 * Arrange themes so that preferred themes appear first in the list.
 */
function openlab_reorder_theme_selections( $themes ) {
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

	foreach ( $themes as $theme_name => $theme ) {
		if ( in_array( $theme_name, $preferred_themes, true ) ) {
			$t1[ $theme_name ] = $theme;
		} else {
			$t2[ $theme_name ] = $theme;
		}
	}

	// Sort the $t1 array to match the preferred order.
	uasort(
		$t1,
		function( $a, $b ) use ( $preferred_themes ) {
			$apos = array_search( $a['id'], $preferred_themes, true );
			$bpos = array_search( $b['id'], $preferred_themes, true );

			return ( $apos < $bpos ) ? -1 : 1;
		}
	);

	return array_merge( $t1, $t2 );
}
add_filter( 'wp_prepare_themes_for_js', 'openlab_reorder_theme_selections' );

/**
 * Filtering blog info to fix items in theme
 *
 * @param type $output
 * @return string
 */
function openlab_theme_fixes_filter_bloginfo( $output ) {

	$theme = wp_get_theme();

	switch ( $theme->get( 'TextDomain' ) ) {
		case 'twentyeleven':
		case 'twentytwelve':
			/**
			 * Targets empty h2s
			 * The empty header will be cleaned up client-side
			 */
			if ( ! $output || $output === '' || ctype_space( $output ) ) {

				$output  = '<span class="empty-header">Just Another WordPress Site</span>';
				$output .= '<script type="text/javascript">(function ($) { $(".empty-header").addClass("processing"); })(jQuery);</script>';
			}

			break;
	}

	return $output;
}
add_filter( 'bloginfo', 'openlab_theme_fixes_filter_bloginfo', 10, 2 );

/**
 * Targeted enqueues for specific-theme, specific-script fixes
 */
function openlab_theme_fixes_init_actions() {

	/**
	 * Targets colorbox to fix accessibility issue where some versions of colorbox
	 * output empty buttons on document load
	 */
	$dependencies = array( 'aec_frontend', 'afg_colorbox_js', 'gform_gravityforms' );
	$plugins_url  = plugins_url( 'js', __FILE__ );

	foreach ( $dependencies as $dep ) {

		// we'll keep the handle the same so this fix doesn't register twice
		wp_register_script( 'openlab-colorbox-fixes', "$plugins_url/targeted-theme-fixes/openlab.colorbox.fixes.js", array( $dep ), '0.0.0.1', true );
		wp_enqueue_script( 'openlab-colorbox-fixes' );
	}
}
add_action( 'wp_enqueue_scripts', 'openlab_theme_fixes_init_actions', 1000 );

/**
 * For instances where get_search_form() is called multiple times in a template
 * This creates mulitple IDs with the same name, which is not semantic and fails
 * WAVE accessibility testing
 * This function uses a global to iterate the searchform IDS
 *
 * @param type $form
 * @return type
 */
function openlab_themes_filter_search_form( $form ) {

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

	if ( ! in_array( $template, $relevant_themes, true ) ) {
		return $form;
	}

	if ( ! isset( $GLOBALS['twentyeleven_search_form_count'] ) ) {
		$GLOBALS['twentyeleven_search_form_count'] = 1;
	} else {
		$GLOBALS['twentyeleven_search_form_count'] ++;
	}

	$current_form_num = $GLOBALS['twentyeleven_search_form_count'];

	$dom = new DOMDocument();
	$dom->loadHTML( $form );
	$all_tags    = $dom->getElementsByTagName( '*' );
	$target_tags = array( 'form', 'label', 'input' );

	foreach ( $all_tags as $key => $this_tag ) {

		if ( ! in_array( $this_tag->tagName, $target_tags, true ) ) {
			continue;
		}

		$legacy_id = $this_tag->getAttribute( 'id' );

		if ( $legacy_id ) {
			$this_tag->setAttribute( 'id', $legacy_id . $current_form_num );
			$this_tag->setAttribute( 'class', $legacy_id );
		}

		$legacy_for = $this_tag->getAttribute( 'for' );

		if ( $legacy_for ) {
			$this_tag->setAttribute( 'for', $legacy_for . $current_form_num );
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

		$input_id    = $input_tag->getAttribute( 'id' );
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
add_filter( 'get_search_form', 'openlab_themes_filter_search_form' );
