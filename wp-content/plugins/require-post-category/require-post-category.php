<?php
/*
Plugin Name: Require Post Category
Plugin URI: https://www.warpconduit.net/wordpress-plugins/require-post-category/
Description: Require users to choose a post category before saving a draft or publishing.
Version: 1.1
Author: Josh Hartman
Author URI: https://www.warpconduit.net
License: GPL2
Text Domain: require-post-category
*/
/*
    Copyright 2018 Josh Hartman
    
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

		$post_types[ $post_type ][ $taxonomy ]['type'] = $config['type'] = ( is_taxonomy_hierarchical( $taxonomy ) ? 'hierarchical' : 'non-hierarchical' );

		if ( ! isset( $config['message'] ) || $taxonomy === $config ) {
			$taxonomy_labels   = get_taxonomy_labels( get_taxonomy( $taxonomy ) );
			$post_type_labels  = get_post_type_labels( get_post_type_object( $post_type ) );
			$config['message'] = "Please choose at least one {$taxonomy_labels->singular_name} before publishing this {$post_type_labels->singular_name}.";
		}

		$post_types[ $post_type ][ $taxonomy ]['message'] = __( $config['message'], 'require-post-category' );
	}

	if ( empty( $post_types[ $post_type ] ) ) {
		return;
	}

	wp_enqueue_script( 'jquery-rpc', plugin_dir_url( __FILE__ ) . 'require-post-category.js', array( 'jquery' ), false, true );
	wp_localize_script( 'jquery-rpc', 'require_post_category', array(
			'taxonomies' => $post_types[ $post_type ],
			'error'      => false
		)
	);
}