<?php

//post type declerations


//help post type
add_action( 'init', 'citytech_register_help' );

function citytech_register_help() {

    $labels = array( 
        'name' => _x( 'Help', 'help' ),
        'singular_name' => _x( 'Help', 'help' ),
        'add_new' => _x( 'Add New', 'help' ),
        'add_new_item' => _x( 'Add New Help', 'help' ),
        'edit_item' => _x( 'Edit Help', 'help' ),
        'new_item' => _x( 'New Help', 'help' ),
        'view_item' => _x( 'View Help', 'help' ),
        'search_items' => _x( 'Search Help', 'help' ),
        'not_found' => _x( 'No help found', 'help' ),
        'not_found_in_trash' => _x( 'No help found in Trash', 'help' ),
        'parent_item_colon' => _x( 'Parent Help:', 'help' ),
        'menu_name' => _x( 'Help', 'help' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Help Pages',
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'revisions', 'page-attributes' ),
        'taxonomies' => array( '' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
		'menu_icon' => get_stylesheet_directory_uri() . '/images/help_icon.png',
        'can_export' => true,
        'rewrite' => array('slug' => 'help','with_front'=>false),
        'capability_type' => 'post'
    );

    register_post_type( 'help', $args );
}

//custom taxonomy to organize help
add_action( 'init', 'citytech_help_taxonomies', 0 );

function citytech_help_taxonomies() 
{
  $labels = array(
    'name' => _x( 'Help Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Help Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Help Categories' ),
    'all_items' => __( 'All Help Categories' ),
    'parent_item' => __( 'Parent Help Category' ),
    'parent_item_colon' => __( 'Parent Help Category:' ),
    'edit_item' => __( 'Edit Help Category' ), 
    'update_item' => __( 'Update Help Category' ),
    'add_new_item' => __( 'Add New Help Category' ),
    'new_item_name' => __( 'New Help Category Name' ),
    'menu_name' => __( 'Help Category' ),
  ); 	

  register_taxonomy('genre',array('help'), array(
    'hierarchical' => true,
    'labels' => $labels,
	'show_in_nav_menus' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'help-category' ),
  ));
}