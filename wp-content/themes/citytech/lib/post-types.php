<?php

//post type declerations

//custom taxonomy to organize help
add_action( 'init', 'openlab_help_taxonomies', 0 );

function openlab_help_taxonomies() 
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

  register_taxonomy('help_category',array('help'), array(
    'hierarchical' => true,
    'labels' => $labels,
	'show_in_nav_menus' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'help/help-category' ),
  ));
  $labels_tags = array(
    'name' => _x( 'Help Tags', 'taxonomy general name' ),
    'singular_name' => _x( 'Help Tag', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Help Tags' ),
    'popular_items' => __( 'Popular Help Tags' ),
    'all_items' => __( 'All Help Tags' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Help Tag' ), 
    'update_item' => __( 'Update Help Tag' ),
    'add_new_item' => __( 'Add New Help Tag' ),
    'new_item_name' => __( 'New Help Tag Name' ),
    'separate_items_with_commas' => __( 'Separate help tags with commas' ),
    'add_or_remove_items' => __( 'Add or remove help tags' ),
    'choose_from_most_used' => __( 'Choose from the most used help tags' ),
    'menu_name' => __( 'Help Tags' ),
  ); 

  register_taxonomy('help_tags','help',array(
    'hierarchical' => false,
    'labels' => $labels_tags,
    'show_ui' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'help-tags' ),
  ));
}

//help post type
add_action( 'init', 'openlab_register_help' );

function openlab_register_help() {

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
        'capability_type' => 'post'
    );

    register_post_type( 'help', $args );
}

//add some information to the Help overview page
add_filter("manage_edit-help_columns", "help_edit_columns");
add_action("manage_help_posts_custom_column",  "help_custom_columns");

function help_edit_columns($columns){
  $columns = array(
    "cb" => "<input type=\"checkbox\" />",
    "title" => "Title",
	"author" => "Author",
    "help_categories" => "Help Categories",
	"help_tags" => "Help Tags",
	"date" => "Date",
  );
 
  return $columns;
}
function help_custom_columns($column){
  global $post;
 
  switch ($column) {
    case "help_categories":
	 if (get_the_term_list($post->ID, 'help_category'))
	 {
     	echo get_the_term_list($post->ID, 'help_category', '', ', ','');
	 } else {
		echo "None";
	 }
      break;
	 case "help_tags":
	 if (get_the_term_list($post->ID, 'help_tags'))
	 {
     	echo get_the_term_list($post->ID, 'help_tags', '', ', ','');
	 } else {
		echo "None";
	 }
      break;
  }
}
