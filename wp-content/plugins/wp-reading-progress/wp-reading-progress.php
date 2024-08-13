<?php
/*
Plugin Name: WP Reading Progress
Plugin URI: https://github.com/joerivanveen/wp-reading-progress
Description: Light weight customizable reading progress bar. Great UX on longreads. Includes estimated reading time (beta).
Version: 1.6.0
Requires at least: 4.9
Tested up to: 6.5
Requires PHP: 5.6
Author: Joeri van Veen
Author URI: https://wp-developer.eu
License: GPLv3
Text Domain: wp-reading-progress
Domain Path: /languages/
*/
defined( 'ABSPATH' ) || die();
// This is plugin nr. 6 by Ruige hond. It identifies as: ruigehond006.
const RUIGEHOND006_VERSION = '1.6.0';
// Register install hook
register_activation_hook( __FILE__, 'ruigehond006_install' );
// Startup the plugin
add_action( 'init', 'ruigehond006_run' );
add_action( 'wp', 'ruigehond006_start' );
/**
 * the actual plugin on the frontend
 */
function ruigehond006_run() {
	if ( is_admin() ) {
		load_plugin_textdomain( 'wp-reading-progress', '', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'ruigehond006_admin_javascript', plugin_dir_url( __FILE__ ) . 'admin.min.js', 'wp-color-picker', RUIGEHOND006_VERSION, true );
		add_action( 'admin_init', 'ruigehond006_settings' );
		add_action( 'admin_menu', 'ruigehond006_menuitem' );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ruigehond006_settingslink' ); // settings link on plugins page
		add_action( 'add_meta_boxes', 'ruigehond006_meta_box_add' ); // in the box the user can activate the bar for a single post
		add_action( 'save_post', 'ruigehond006_meta_box_save' );
	} else {
		wp_enqueue_script( 'ruigehond006_javascript', plugin_dir_url( __FILE__ ) . 'wp-reading-progress.min.js', false, RUIGEHOND006_VERSION );
	}
}

function ruigehond006_start() {
	if ( is_admin() ) {
		return;
	}
	$post_identifier = null;
	// check if we're using the progress bar here
	$options = get_option( 'ruigehond006' );
	$post_id = get_the_ID();
	if ( is_singular() ) {
		if ( ( isset( $options['post_types'] ) && in_array( get_post_type( $post_id ), $options['post_types'] ) )
		     || 'yes' === get_post_meta( $post_id, '_ruigehond006_show', true )
		) {
			if ( isset( $options['include_comments'] ) ) {
				$post_identifier = 'body';
			} else {
				$post_identifier = '.' . implode( '.', get_post_class( '', $post_id ) );
			}
		}
	} elseif ( isset( $options['archives'] ) && isset( $options['post_types'] ) && in_array( get_post_type(), $options['post_types'] ) ) {
		$post_identifier = 'body';
	}
	if ( null !== $post_identifier ) {
		wp_localize_script( 'ruigehond006_javascript', 'ruigehond006_c', array_merge(
			$options, array(
				'post_identifier' => $post_identifier,
				'post_id'         => $post_id,
			)
		) );
	}
	if ( ! isset( $options['no_css'] ) ) {
		add_action( 'wp_head', 'ruigehond006_stylesheet' );
	}
	/**
	 * separate ert section...
	 */
	if (isset($options['use_ert'])) {
		if (isset($options['ert_speed']) && (int) $options['ert_speed'] > 0) {
			if ( isset( $options['use_ert_shortcode'] ) ) {
				add_shortcode( 'wp-reading-progress-ert', 'ruigehond006_shortcode' );
			}
			if ( isset( $options['use_ert_excerpt'] ) ) {
				add_filter( 'get_the_excerpt', 'ruigehond006_ert', 99 );
			}
			//add_filter( 'get_the_content', 'ruigehond006_ert', 99 );
			//add_filter( 'the_content', 'ruigehond006_ert', 99 );
		}
	}
}

function ruigehond006_stylesheet() {
	echo '<style>#ruigehond006_wrap{z-index:10001;position:fixed;display:block;left:0;width:100%;margin:0;overflow:visible}#ruigehond006_inner{position:absolute;height:0;width:inherit;background-color:rgba(255,255,255,.2);-webkit-transition:height .4s;transition:height .4s}html[dir=rtl] #ruigehond006_wrap{text-align:right}#ruigehond006_bar{width:0;height:100%;background-color:transparent}</style>';
}

function ruigehond006_shortcode( $attributes = [], $content = null, $short_code = 'wp-reading-progress-ert' ) {
	return ruigehond006_ert();
}

function ruigehond006_ert( $content = null, $args = null ) {
	global $post;
	if ( ! $post ) {
		return '';
	}
	$speed   = 250;
	$options = get_option( 'ruigehond006' );
	if ( isset( $options['ert_speed'] ) && (int) $options['ert_speed'] > 0 ) {
		$speed = (int) $options['ert_speed'];
	}
	$minutes = max( 1, round( $time =  (str_word_count( strip_tags( $post->post_content ) ) / $speed), 0 ) );
	if (
		isset( $options['ert_snippet'] )
		&& 1 === substr_count( ( $str = $options['ert_snippet'] ), '%d' )
	) {
		$str = sprintf( $str, $minutes );
	} else {
		$str = sprintf( '%d” read', $minutes );
	}
	$snippet = sprintf("<span class='wp-reading-progress-ert post-$post->ID' data-ert='$time' data-minutes='$minutes'>%s</span>", $str);

	if ( $content ) {
		return "$snippet $content";
	}

	return $snippet;
}

// meta box exposes setting to display reading progress for an individual post
// https://developer.wordpress.org/reference/functions/add_meta_box/
function ruigehond006_meta_box_add( $post_type = null ) {
	if ( ! get_the_ID() ) {
		return;
	}
	$option = get_option( 'ruigehond006' );
	if ( isset( $option['post_types'] ) && in_array( $post_type, $option['post_types'] ) ) {
		return; // you can't set this if the bar is displayed by default on this post type
	}
	add_meta_box( // WP function.
		'ruigehond006', // Unique ID
		'WP Reading Progress', // Box title
		'ruigehond006_meta_box', // Content callback, must be of type callable
		$post_type, // Post type
		'normal',
		'low',
		array( 'option' => $option )
	);
}

function ruigehond006_meta_box( $post, $obj ) {
	//$option = $obj['args']['option']; // not used at this moment
	wp_nonce_field( 'ruigehond006_save', 'ruigehond006_nonce' );
	echo '<input type="checkbox" id="ruigehond006_checkbox" name="ruigehond006_show"';
	if ( 'yes' === get_post_meta( $post->ID, '_ruigehond006_show', true ) ) {
		echo ' checked="checked"';
	}
	echo '/> <label for="ruigehond006_checkbox">';
	echo esc_html__( 'display reading progress bar', 'wp-reading-progress' );
	echo '</label>';
}

function ruigehond006_meta_box_save( $post_id ) {
	if ( ! isset( $_POST['ruigehond006_nonce'] ) || ! wp_verify_nonce( $_POST['ruigehond006_nonce'], 'ruigehond006_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['ruigehond006_show'] ) ) {
		add_post_meta( $post_id, '_ruigehond006_show', 'yes', true );
	} else {
		delete_post_meta( $post_id, '_ruigehond006_show' );
	}
}

/**
 * manage global settings
 */
function ruigehond006_settings() {
	/**
	 * register a new setting, call this function for each setting
	 * Arguments: (Array)
	 * - group, the same as in settings_fields, for security / nonce etc.
	 * - the name of the options
	 * - the function that will validate the options, valid options are automatically saved by WP
	 */
	register_setting( 'ruigehond006', 'ruigehond006', 'ruigehond006_settings_validate' );
	// register a new section in the page
	add_settings_section(
		'progress_bar_settings', // section id
		esc_html__( 'Set your options', 'wp-reading-progress' ), // title
		function () {
			echo '<p>';
			echo esc_html__( 'This plugin displays a reading progress bar on your selected post types.', 'wp-reading-progress' );
			echo ' ';
			echo esc_html__( 'When it does not find a single article, it uses the whole page to calculate reading progress.', 'wp-reading-progress' );
			echo '<br/>';
			echo esc_html__( 'For post types which are switched off in the settings, you can activate the bar per post in the post-edit screen.', 'wp-reading-progress' );
			echo '<br/>';
			echo esc_html__( 'Please use valid input or the bar might not display.', 'wp-reading-progress' );
			echo '</p>';
		}, //callback
		'ruigehond006' // page
	);
	if ( false === ( $option = get_option( 'ruigehond006' ) ) ) {
		ruigehond006_add_defaults();
		$option = get_option( 'ruigehond006' );
	}
	// @since 1.5.4: check if the required placeholders are in the translated string
	$string = esc_html__( 'Use %s or %s, or any VALID selector of a fixed element where the bar can be appended to, e.g. a sticky menu.', 'wp-reading-progress' );
	if ( 3 !== count( explode( '%s', $string ) ) ) {
		$string = 'Use %s or %s, or any VALID selector of a fixed element where the bar can be appended to, e.g. a sticky menu.';
	}
	ruigehond006_add_settings_field(
		'bar_attach',
		'text',
		esc_html__( 'Stick the bar to this element', 'wp-reading-progress' ), // title
		$option,
		// #translators: two links are inserted that set the value accordingly, 'top' and 'bottom'
		sprintf( $string, '<a>top</a>', '<a>bottom</a>' ) . ' ' . esc_html__( 'Multiple selectors can be separated by commas, the bar will be attached to the first one visible.', 'wp-reading-progress' )
	);
	ruigehond006_add_settings_field(
		'stick_relative',
		'checkbox',
		esc_html__( 'How to stick', 'wp-reading-progress' ),
		$option,
		esc_html__( 'If the bar is too wide, try relative positioning by checking this box, or attach it to another element.', 'wp-reading-progress' )
	);
	ruigehond006_add_settings_field(
		'bar_color',
		'color',
		esc_html__( 'Color of the progress bar', 'wp-reading-progress' ), // title
		$option
	);
//    ruigehond006_add_settings_field(
//        'bar_color_dark_mode',
//        'color',
//        esc_html__('Color when in dark mode', 'wp-reading-progress'), // title
//        $option,
//        sprintf(__('Depends on a certain class added to the body or html container, including one of the following strings: %s', 'wp-reading-progress'), '*dark-mode*, *night-mode*')
//    );
	// @since 1.5.4: check if the required placeholders are in the translated string
	$string = esc_html__( 'Thickness based on screen height is recommended, e.g. %s. But you can also use pixels, e.g. %s.', 'wp-reading-progress' );
	if ( 3 !== count( explode( '%s', $string ) ) ) {
		$string = 'Thickness based on screen height is recommended, e.g. %s. But you can also use pixels, e.g. %s.';
	}
	ruigehond006_add_settings_field(
		'bar_height',
		'text-short',
		esc_html__( 'Progress bar thickness', 'wp-reading-progress' ), // title
		$option,
		sprintf( $string, '<a>.5vh</a>', '<a>6px</a>' )
	);
	ruigehond006_add_settings_field(
		'aria_label',
		'text',
		esc_html__( 'Aria label', 'wp-reading-progress' ), // title
		$option,
		esc_html__( 'Explain the purpose of this reading bar to screenreaders', 'wp-reading-progress' )
	);
	ruigehond006_add_settings_field(
		'mark_it_zero',
		'checkbox',
		esc_html__( 'Make bar start at 0%', 'wp-reading-progress' ),
		$option,
		esc_html__( 'Yes please', 'wp-reading-progress' )
	);
	ruigehond006_add_settings_field(
		'include_comments',
		'checkbox',
		esc_html__( 'On single post page', 'wp-reading-progress' ),
		$option,
		esc_html__( 'use whole page to calculate reading progress', 'wp-reading-progress' )
	);
	add_settings_field(
		'ruigehond006_post_types',
		// #TRANSLATORS: this is followed by a list of the available post_types
		esc_html__( 'Show reading progress on', 'wp-reading-progress' ),
		function ( $args ) {
			$post_types = [];
			if ( isset( $args['option']['post_types'] ) ) {
				$post_types = $args['option']['post_types'];
			}
			foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
				$post_type = htmlentities( $post_type, ENT_QUOTES );
				echo "<label><input type=\"checkbox\" name=\"ruigehond006[post_types][]\" value=\"$post_type\"";
				if ( in_array( $post_type, $post_types ) ) {
					echo ' checked="checked"';
				}
				echo "/>$post_type</label><br/>";
			}
			echo '<div class="ruigehond006 explanation"><em>';
			echo esc_html__( 'For unchecked post types you can enable the reading progress bar per post on the post edit page.', 'wp-reading-progress' );
			echo '</em></div>';
		},
		'ruigehond006',
		'progress_bar_settings',
		[ 'option' => $option ] // args
	);
	ruigehond006_add_settings_field(
		'archives',
		'checkbox',
		esc_html__( 'And on their archives', 'wp-reading-progress' ),
		$option
	);
	ruigehond006_add_settings_field(
		'no_css',
		'checkbox',
		'No css',
		$option,
		esc_html__( 'necessary css for the reading bar is included elsewhere', 'wp-reading-progress' )
	);
	add_settings_section(
		'ert_settings', // section id
		esc_html__( 'Estimated reading time', 'wp-reading-progress' ) . ' (BETA)', // title
		function () {
			echo '<p>';
			echo esc_html__( 'If you want to display estimated reading time (ert) and your theme does not support it, you can activate it here.', 'wp-reading-progress' );
			echo '<br/>';
			echo esc_html__( 'When activated, you need to set some extra options. Upon deactivation, those options will be removed as well.', 'wp-reading-progress' );
			echo '<br/>';
			echo esc_html__( 'The ert (snippet) will be output in a span with css class `wp-reading-progress-ert` for you to style.', 'wp-reading-progress' );
			echo '</p>';
		}, //callback
		'ruigehond006' // page
	);
	ruigehond006_add_settings_field(
		'use_ert',
		'checkbox',
		esc_html__( 'Activate ert', 'wp-reading-progress' ),
		$option,
		esc_html__( 'Check to activate ert, leave unchecked if you have ert in your theme.', 'wp-reading-progress' ),
		'ert_settings'
	);
	if (isset($option['use_ert'])) {
		ruigehond006_add_settings_field(
			'use_ert_shortcode',
			'checkbox',
			esc_html__( 'Use shortcode', 'wp-reading-progress' ),
			$option,
			esc_html__( 'Switch this on to display ert using the shortcode: [wp-reading-progress-ert].', 'wp-reading-progress' ),
			'ert_settings'
		);
		ruigehond006_add_settings_field(
			'use_ert_excerpt',
			'checkbox',
			esc_html__( 'Add before excerpt', 'wp-reading-progress' ),
			$option,
			esc_html__( 'This will add the snippet before any excerpt. Note that some themes strip the html.', 'wp-reading-progress' ),
			'ert_settings'
		);
		ruigehond006_add_settings_field(
			'ert_speed',
			'text-short',
			esc_html__( 'Reading speed', 'wp-reading-progress' ), // title
			$option,
			esc_html__( 'Average reading speed in words per minute, integers only. Used to estimate reading time. Usual is something between 200 and 300.', 'wp-reading-progress' ),
			'ert_settings'
		);
		ruigehond006_add_settings_field(
			'ert_snippet',
			'text-short',
			esc_html__( 'Snippet text', 'wp-reading-progress' ), // title
			$option,
			esc_html__( 'Define your own text here. Mandatory placeholder `%d` will display the minutes.', 'wp-reading-progress' ),
			'ert_settings'
		);
	}
}

function ruigehond006_add_settings_field( $name, $type, $title, $option, $explanation = null, $section = 'progress_bar_settings' ) {
	add_settings_field(
		"ruigehond006_$name",
		$title,
		function ( $args ) {
			switch ( $args['type'] ) {
				case 'checkbox':
					echo '<label><input type="checkbox" name="ruigehond006[';
					echo htmlentities( $args['name'], ENT_QUOTES );
					echo ']"';
					if ( $args['value'] ) {
						echo ' checked="checked"';
					}
					echo '/> ';
					if ( isset( $args['explanation'] ) ) {
						echo wp_kses_post( $args['explanation'] );
					}
					echo '</label>';

					return;
				case 'color':
					echo '<input type="text" class="ruigehond006_colorpicker" name="ruigehond006[';
					echo htmlentities( $args['name'], ENT_QUOTES );
					echo ']" value="';
					echo htmlentities( $args['value'], ENT_QUOTES );
					echo '"/>';
					break;
				default: // regular input
					echo '<input type="text" name="ruigehond006[';
					echo htmlentities( $args['name'], ENT_QUOTES );
					echo ']" value="';
					echo htmlentities( $args['value'], ENT_QUOTES );
					if ( 'text-short' !== $args['type'] ) {
						echo '" class="regular-text';
					}
					echo '"/>';
			}
			if ( isset( $args['explanation'] ) ) {
				echo '<div class="ruigehond006 explanation"><em>';
				echo wp_kses_post( $args['explanation'] );
				echo '</em></div>';
			}
		},
		'ruigehond006',
		$section,
		array(
			'name'        => $name,
			'type'        => $type,
			'value'       => isset( $option[ $name ] ) ? $option[ $name ] : '',
			'explanation' => $explanation,
		) // args
	);
}

function ruigehond006_settingspage() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap"><h1>';
	echo esc_html( get_admin_page_title() );
	echo '</h1><form action="options.php" method="post">';
	// output security fields for the registered setting
	settings_fields( 'ruigehond006' );
	// output setting sections and their fields
	do_settings_sections( 'ruigehond006' );
	// output save settings button
	submit_button( esc_html__( 'Save Settings', 'wp-reading-progress' ) );
	echo '</form></div>';
}

function ruigehond006_settingslink( $links ) {
	$url           = get_admin_url();
	$txt           = esc_html__( 'Settings', 'wp-reading-progress' );
	$settings_link = "<a href=\"{$url}options-general.php?page=wp-reading-progress\">$txt</a>";
	array_unshift( $links, $settings_link );

	return $links;
}

function ruigehond006_menuitem() {
	add_options_page(
		'WP Reading Progress',
		'WP Reading Progress',
		'manage_options',
		'wp-reading-progress',
		'ruigehond006_settingspage'
	);
}

function ruigehond006_settings_validate( $input ) {
	$options = (array) get_option( 'ruigehond006' );
	if ( false === is_array( $input ) ) {
		return $options;
	}

	// these are all the settings we have
	$settings = array(
		'stick_relative',
		'mark_it_zero',
		'include_comments',
		'archives',
		'no_css',
		'bar_color',
		'bar_height',
		'bar_attach',
		'aria_label',
		'post_types',
		'use_ert',
		'ert_speed',
		'ert_snippet',
		'use_ert_shortcode',
		'use_ert_excerpt',
	);

	foreach ( $settings as $index => $key ) {
		$value = null;
		// note: this only works because we have 1 settings page.
		if ( isset( $input[ $key ] ) ) {
			$value = $input[ $key ];
		}
		switch ( $key ) {
			// on / off flags
			case 'stick_relative':
			case 'mark_it_zero':
			case 'include_comments':
			case 'archives':
			case 'no_css':
			case 'use_ert':
			case 'use_ert_shortcode':
			case 'use_ert_excerpt':
				// IMPORTANT: this is backwards compatible, 'false' options must not be present
				if ( 'on' === $value ) {
					$options[ $key ] = 'on';
				} else {
					unset( $options[ $key ] );
				}
				break;
			case 'bar_color':
			case 'bar_height':
			case 'bar_attach':
			case 'ert_snippet':
			case 'aria_label':
				$options[ $key ] = strip_tags( $value );
				break;
			case 'ert_speed':
				$options[ $key ] = (int) $value;
				break;
			case 'post_types': // array of strings
				$options[ $key ] = array_map( static function ( $value ) {
					return sanitize_key( $value );
				}, $value );
				break;
		}
	}

	return $options;
}

/**
 * plugin management functions
 */
function ruigehond006_add_defaults() {
	add_option( 'ruigehond006', array(
		'bar_attach' => 'top',
		'bar_color'  => '#f1592a',
		'bar_height' => '.5vh',
		'post_types' => array( 'post' ),
	), null, true );
}

function ruigehond006_install() {
	if ( ! get_option( 'ruigehond006' ) ) { // insert default settings:
		ruigehond006_add_defaults();
	}
}
