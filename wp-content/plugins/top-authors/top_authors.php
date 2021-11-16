<?php
/*
Plugin Name:       Top Authors
Description:       A flexible author display widget which gives you control over the output as well as advanced options like top authors in category.
Version:           1.0.11
Author:            Daniel Pataki
Author URI:        http://danielpataki.com/
License:           GPLv2 or later
*/


include "class-top-authors-widget.php";



add_action('plugins_loaded', 'ta_load_textdomain');

/**
 * Load Text Domain
 *
 * Loads the textdomain for translations
 *
 * @author Daniel Pataki
 * @since 1.0.2
 *
 */
function ta_load_textdomain() {
	load_plugin_textdomain( 'top-authors', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

add_action( 'widgets_init', 'ta_widget_init' );
/**
 * Widget Initializer
 *
 * This function registers the top author widget with WordPress
 * The Top_Authors_Widget class must be included beforehand of course
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */
function ta_widget_init() {
	register_widget( 'Top_Authors_Widget' );
}

add_action( 'admin_enqueue_scripts', 'ta_admin_enqueue' );
/**
 * Backend Assets
 *
 * This function takes care of enqueueing all the assets we need. Right
 * now this consists of the SumoSelect Javascript and CSS and our own
 * backend styles, in addition to the localization of our script.
 *
 * @link https://github.com/HemantNegi/jquery.sumoselect
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */
function ta_admin_enqueue($hook) {
    if ( 'widgets.php' != $hook ) {
        return;
    }

    wp_enqueue_script( 'jquery-sumoselect', plugin_dir_url( __FILE__ ) . '/js/jquery.sumoselect.min.js', array('jquery'), '1.0.0', true );

	wp_enqueue_script( 'top-authors', plugin_dir_url( __FILE__ ) . '/js/scripts.js', array('jquery-sumoselect'), '1.0.0', true );

	wp_enqueue_style( 'jquery-sumoselect', plugin_dir_url( __FILE__ ) . '/css/sumoselect.css' );

	wp_enqueue_style( 'top-authors', plugin_dir_url( __FILE__ ) . '/css/styles.css' );

	wp_localize_script( 'top-authors', 'ta', array(
		'role_select_placeholder' => __( 'Select roles to exclude', 'top-authors' ),
		'post_type_select_placeholder' => __( 'Select post types to include', 'top-authors' )
	));

}



add_action( 'wp_enqueue_scripts', 'ta_frontend_enqueue' );
/**
 * Frontend Assets
 *
 * Registers the styles needed for the various presets we have. These are
 * enqueued inside the widget to make sure they're only enqueued when needed.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */
function ta_frontend_enqueue() {
	wp_register_style( 'ta-preset-gravatar-list-count', plugin_dir_url( __FILE__ ) . '/css/preset-gravatar-list-count.css' );
	wp_register_style( 'ta-preset-gravatar-name', plugin_dir_url( __FILE__ ) . '/css/preset-gravatar-name.css' );
	wp_register_style( 'ta-preset-gravatars', plugin_dir_url( __FILE__ ) . '/css/preset-gravatars.css' );

}


add_action( 'admin_init', 'ta_update_check' );

/**
 * Update Check
 *
 * Checks wether or not an update is needed. We need this because of the
 * considerable changes from pre 1.0.0 to 1.0.0.
 *
 * @uses ta_update_to_1_0_0
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */
function ta_update_check() {
	$version = get_option( 'ta_version' );
	if( empty( $version ) ) {
		ta_update_to_1_0_0();
		update_option( 'ta_version', '1.0.0' );
	}
}

/**
 * Version 1.0.0 Updater
 *
 * Updates pre 1.0.0 version to the new structure. We take all the
 * old widgets, build new widgets out of them and add them to
 * the array of new widgets and sidebar widgets.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */
function ta_update_to_1_0_0() {

	// Get old widgets and create new widgets
	$old_widgets = get_option( 'widget_top_authors' );
	$new_widgets = array();

	// If there are no widgets at the moment, no need to do anything
	if( empty( $old_widgets ) ) {
		return;
	}

	// Get usable post types
	$post_types = get_post_types( array( 'public' => true ), 'names' );
	unset( $post_types['attachment'] );

	// Loop through old widgets and build a new widget array from the data
	$new_key = 1;
	$id_map = array();
	foreach( $old_widgets as $i => $old_widget ) {
		if( is_array( $old_widget ) ) {
			$id_map[$i] = $new_key;

			$new_widgets[] = array(
				'title' => $old_widget['title'],
				'count' => $old_widget['number'],
				'include_post_types' => ( empty( $old_widget['include_CPT'] ) ) ? array( 'post' ) : array_values( $post_types ),
				'preset' => 'custom',
				'template' => htmlspecialchars_decode($old_widget['template']),
				'before_list' => $old_widget['before'],
				'after_list' => $old_widget['after'],
				'exclude_roles' => ( empty( $old_widget['include_CPT'] ) ) ? array() : array( 'administrator' ),
				'archive_specific' => false,
			);
		}
		$new_key++;
	}


	// Create the new widgets in the Database
	$new_widgets = update_option( 'widget_top-authors', $new_widgets );

	// Get and update the sidebars_widgets option which stores which widget goes in which sidebar
	$sidebars_widgets = get_option('sidebars_widgets');

	if( empty( $sidebar_widgets ) ) {
		return;
	}

	foreach( $sidebars_widgets as $sidebar => $sidebars_widget ) {
		if( !empty($sidebars_widget) && is_array( $sidebars_widget ) ) {
			foreach( $sidebars_widget as $i => $widget ) {
				if( substr( $widget, 0, 12 ) == 'top_authors-' ) {
					$id = substr( $widget, 12 );
					$new_id = $id_map[$id];
					$sidebars_widgets[$sidebar][$i] = str_replace( "top_authors-" . $id, "top-authors-" . $new_id, $widget );
				}
			}
		}
	}
	$sidebars_widgets = update_option('sidebars_widgets', $sidebars_widgets);
}
