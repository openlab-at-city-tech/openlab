<?php
/*
Plugin Name: Require Post Category
Plugin URI: https://wordpress.org/plugins/require-post-category/
Description: Require users to choose a post category before updating or publishing a post.
Version: 2.0.3
Author: Josh Hartman
Author URI: https://www.warpconduit.net
License: GPL2
Text Domain: require-post-category
*/
/*
    Copyright 2020 Josh Hartman
    
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'plugins_loaded', 'rpc_load_translation_files' );
add_action( 'admin_enqueue_scripts', 'rpc_admin_enqueue_scripts_func' );

function rpc_load_translation_files() {
	load_plugin_textdomain( 'require-post-category', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

function rpc_admin_enqueue_scripts_func( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		return;
	}
	global $post_type;

	$rpc_is_gutenberg_active = rpc_is_gutenberg_active();

	$default_post_types = array(
		'post' => array(
			'category' => array(
				'message' => 'Please select a category before publishing this post.'
			)
		)
	);

	$post_types = apply_filters( 'rpc_post_types', $default_post_types );

	if ( ! is_array( $post_types ) ) {
		return;
	}

	if ( ! isset( $post_types[ $post_type ] ) ) {
		return;
	}

	if ( ! isset( $post_types[ $post_type ] ) || ! is_array( $post_types[ $post_type ] ) || empty( $post_types[ $post_type ] ) ) {
		if ( is_string( $post_types[ $post_type ] ) ) {
			$post_types[ $post_type ] = array(
				'taxonomies' => array(
					$post_types[ $post_type ]
				)
			);
		} else if ( is_array( $post_types[ $post_type ] ) ) {
			$post_types[ $post_type ] = array(
				'taxonomies' => $post_types[ $post_type ]
			);
		} else {
			return;
		}
	}

	$post_type_taxonomies = get_object_taxonomies( $post_type );

	foreach ( $post_types[ $post_type ] as $taxonomy => $config ) {
		if ( is_int( $taxonomy ) && is_string( $config ) ) {
			unset( $post_types[ $post_type ][ $taxonomy ] );
			$taxonomy = $config;

			$post_types[ $post_type ][ $taxonomy ] = $config = array();
		}

		if ( ! taxonomy_exists( $taxonomy ) || ! in_array( $taxonomy, $post_type_taxonomies ) ) {
			unset( $post_types[ $post_type ][ $taxonomy ] );
			continue;
		}

		$taxonomy_object = get_taxonomy( $taxonomy );
		$taxonomy_labels = get_taxonomy_labels( $taxonomy_object );

		$post_types[ $post_type ][ $taxonomy ]['type'] = $config['type'] = ( is_taxonomy_hierarchical( $taxonomy ) ? 'hierarchical' : 'non-hierarchical' );

		if ( ! isset( $config['message'] ) || $taxonomy === $config ) {
			$post_type_labels  = get_post_type_labels( get_post_type_object( $post_type ) );
			$config['message'] = "Please choose at least one {$taxonomy_labels->singular_name} before publishing this {$post_type_labels->singular_name}.";
		}

		$post_types[ $post_type ][ $taxonomy ]['message'] = __( $config['message'], 'require-post-category' );

		if ( $rpc_is_gutenberg_active && !empty($taxonomy_object->rest_base) && $taxonomy !== $taxonomy_object->rest_base ) {
			$post_types[ $post_type ][ $taxonomy_object->rest_base ] = $post_types[ $post_type ][ $taxonomy ];
			unset( $post_types[ $post_type ][ $taxonomy ] );
		}
	}

	if ( empty( $post_types[ $post_type ] ) ) {
		return;
	}

	if ( $rpc_is_gutenberg_active ) {
		wp_enqueue_script( 'jquery-rpc', plugin_dir_url( __FILE__ ) . 'require-post-category-gutenberg.js', array(
			'jquery', 'wp-data', 'wp-editor', 'wp-edit-post'
		));
	} else {
		wp_enqueue_script( 'jquery-rpc', plugin_dir_url( __FILE__ ) . 'require-post-category.js', array( 'jquery' ), false, true );
	}

	wp_localize_script( 'jquery-rpc', 'require_post_category', array(
			'taxonomies' => $post_types[ $post_type ],
			'error'      => false
		)
	);

}

function rpc_is_gutenberg_active() {
	if ( function_exists( 'is_gutenberg_page' ) &&
	     is_gutenberg_page()
	) {
		return true;
	}

	$current_screen = get_current_screen();

	if ( method_exists( $current_screen, 'is_block_editor' ) &&
	     $current_screen->is_block_editor()
	) {
		return true;
	}
	
	return false;
}