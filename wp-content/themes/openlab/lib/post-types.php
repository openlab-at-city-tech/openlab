<?php

//post type declerations
//custom taxonomy to organize help
add_action('init', 'openlab_help_taxonomies', 0);

function openlab_help_taxonomies() {
    $labels = array(
        'name' => _x('Help Categories', 'taxonomy general name'),
        'singular_name' => _x('Help Category', 'taxonomy singular name'),
        'search_items' => __('Search Help Categories'),
        'all_items' => __('All Help Categories'),
        'parent_item' => __('Parent Help Category'),
        'parent_item_colon' => __('Parent Help Category:'),
        'edit_item' => __('Edit Help Category'),
        'update_item' => __('Update Help Category'),
        'add_new_item' => __('Add New Help Category'),
        'new_item_name' => __('New Help Category Name'),
        'menu_name' => __('Help Category'),
    );

    register_taxonomy('help_category', array('help'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'help/help-category'),
    ));
    $labels_tags = array(
        'name' => _x('Help Tags', 'taxonomy general name'),
        'singular_name' => _x('Help Tag', 'taxonomy singular name'),
        'search_items' => __('Search Help Tags'),
        'popular_items' => __('Popular Help Tags'),
        'all_items' => __('All Help Tags'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Help Tag'),
        'update_item' => __('Update Help Tag'),
        'add_new_item' => __('Add New Help Tag'),
        'new_item_name' => __('New Help Tag Name'),
        'separate_items_with_commas' => __('Separate help tags with commas'),
        'add_or_remove_items' => __('Add or remove help tags'),
        'choose_from_most_used' => __('Choose from the most used help tags'),
        'menu_name' => __('Help Tags'),
    );

    register_taxonomy('help_tags', 'help', array(
        'hierarchical' => false,
        'labels' => $labels_tags,
        'show_ui' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array('slug' => 'help-tags'),
    ));
}

//help post type
add_action('init', 'openlab_register_help');

function openlab_register_help() {

    $labels = array(
        'name' => _x('Help', 'help'),
        'singular_name' => _x('Help', 'help'),
        'add_new' => _x('Add New', 'help'),
        'add_new_item' => _x('Add New Help', 'help'),
        'edit_item' => _x('Edit Help', 'help'),
        'new_item' => _x('New Help', 'help'),
        'view_item' => _x('View Help', 'help'),
        'search_items' => _x('Search Help', 'help'),
        'not_found' => _x('No help found', 'help'),
        'not_found_in_trash' => _x('No help found in Trash', 'help'),
        'parent_item_colon' => _x('Parent Help:', 'help'),
        'menu_name' => _x('Help', 'help'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Help Pages',
        'supports' => array('title', 'editor', 'excerpt', 'author', 'revisions', 'page-attributes'),
        'taxonomies' => array(''),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => false,
        'query_var' => true,
        'menu_icon' => 'dashicons-editor-help',
        'can_export' => true,
        'capability_type' => 'post',
		'show_in_rest' => true,
    );

    register_post_type('help', $args);
}

//add some information to the Help overview page
add_filter("manage_edit-help_columns", "help_edit_columns");
add_action("manage_help_posts_custom_column", "help_custom_columns");
add_filter("manage_edit-help_sortable_columns", "help_column_register_sortable");

function help_edit_columns($columns) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Title",
        "author" => "Author",
        "help_categories" => "Help Categories",
        "help_tags" => "Help Tags",
        "menu_order" => 'Menu Order',
        "date" => "Date",
    );

    return $columns;
}

function help_custom_columns($column) {
    global $post;

    switch ($column) {
        case "help_categories":
            if (get_the_term_list($post->ID, 'help_category')) {
                echo get_the_term_list($post->ID, 'help_category', '', ', ', '');
            } else {
                echo "None";
            }
            break;
        case "help_tags":
            if (get_the_term_list($post->ID, 'help_tags')) {
                echo get_the_term_list($post->ID, 'help_tags', '', ', ', '');
            } else {
                echo "None";
            }
            break;
        case 'menu_order':
            $order = $post->menu_order;
            echo $order;
            break;
    }
}

function help_column_register_sortable($columns) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}

//custom post type - help glossary
add_action('init', 'openlab_register_help_glossary');

function openlab_register_help_glossary() {

    $labels = array(
        'name' => _x('Help Glossary', 'help glossary'),
        'singular_name' => _x('Help Glossary', 'help glossary'),
        'add_new' => _x('Add New', 'help glossary'),
        'add_new_item' => _x('Add New Help Glossary', 'help glossary'),
        'edit_item' => _x('Edit Help Glossary', 'help glossary'),
        'new_item' => _x('New Help Glossary', 'help glossary'),
        'view_item' => _x('View Help Glossary', 'help glossary'),
        'search_items' => _x('Search Help Glossary', 'help glossary'),
        'not_found' => _x('No help glossary found', 'help glossary'),
        'not_found_in_trash' => _x('No help glossary found in Trash', 'help glossary'),
        'parent_item_colon' => _x('Parent Help Glossary:', 'help glossary'),
        'menu_name' => _x('Help Glossary', 'help glossary'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Help Glossary Pages',
        'supports' => array('title', 'editor', 'excerpt', 'author', 'revisions', 'page-attributes'),
        'taxonomies' => array(''),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => false,
        'rewrite' => false,
        'query_var' => true,
        'menu_icon' => 'dashicons-editor-help',
        'can_export' => true,
        'capability_type' => 'post'
    );

    register_post_type('help_glossary', $args);
}

//add some information to the Glossary overview page
add_filter("manage_edit-help_glossary_columns", "help_glossary_edit_columns");
add_action("manage_help_glossary_posts_custom_column", "help_glossary_custom_columns");
add_filter("manage_edit-help_glossary_sortable_columns", "help_glossary_column_register_sortable");

function help_glossary_edit_columns($columns) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Title",
        "author" => "Author",
        "menu_order" => 'Menu Order',
        "date" => "Date",
    );

    return $columns;
}

function help_glossary_custom_columns($column) {
    global $post;

    switch ($column) {
        case 'menu_order':
            $order = $post->menu_order;
            echo $order;
            break;
    }
}

function help_glossary_column_register_sortable($columns) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}

//adding slider post type
function register_cpt_slider() {
    $labels = array(
        'name' => _x('Sliders', 'slider'),
        'singular_name' => _x('Slider', 'slider'),
        'add_new' => _x('Add New', 'slider'),
        'add_new_item' => _x('Add New Slider', 'slider'),
        'edit_item' => _x('Edit Slider', 'slider'),
        'new_item' => _x('New Slider', 'slider'),
        'view_item' => _x('View Slider', 'slider'),
        'search_items' => _x('Search Sliders', 'slider'),
        'not_found' => _x('No sliders found', 'slider'),
        'not_found_in_trash' => _x('No sliders found in Trash', 'slider'),
        'parent_item_colon' => _x('Parent Slider:', 'slider'),
        'menu_name' => _x('Sliders', 'slider'),
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'thumbnail'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-images-alt2',
        'show_in_nav_menus' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => false,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => false,
        'capability_type' => 'post'
    );
    register_post_type('slider', $args);
}

add_action('init', 'register_cpt_slider');
