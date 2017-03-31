<?php
/************************
*** CUSTOM POST TYPES ***
*************************/

function add_custom_posttype_folder_taxonomy() {
  // get post types
  global $globOptions, $folder_types, $typenow;

  if ($globOptions) {

    foreach($folder_types as $type) {
      $tax_slug = $type.'_folder';
      $args = array(
        'hierarchical' => true,
        'labels' => array(
          'name' => _x( 'Folders', 'taxonomy general name' ),
          'singular_name' => _x( 'Folder', 'taxonomy singular name' ),
          'search_items' =>  __( 'Search Folders' ),
          'all_items' => __( 'All Folders' ),
          'parent_item' => __( 'Parent Folder' ),
          'parent_item_colon' => __( 'Parent Folder:' ),
          'edit_item' => __( 'Edit Folder' ),
          'update_item' => __( 'Update Folder' ),
          'add_new_item' => __( 'Add New Folder' ),
          'new_item_name' => __( 'New Folder Name' ),
          'menu_name' => __( 'Folders' ),
          ),
        'rewrite' => array(
          'slug' => '',
          'with_front' => false,
          'hierarchical' => false
          ),
        'update_count_callback' => '_update_generic_term_count'
        );
      if ($type == 'attachment') {
        $tax_slug = 'media_folder';
        $args = array(
          'hierarchical' => true,
          'labels' => array(
            'name' => _x( 'Folders', 'taxonomy general name' ),
            'singular_name' => _x( 'Folder', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search Folders' ),
            'all_items' => __( 'All Folders' ),
            'parent_item' => __( 'Parent Folder' ),
            'parent_item_colon' => __( 'Parent Folder:' ),
            'edit_item' => __( 'Edit Folder' ),
            'update_item' => __( 'Update Folder' ),
            'add_new_item' => __( 'Add New Folder' ),
            'new_item_name' => __( 'New Folder Name' ),
            'menu_name' => __( 'Folders' ),
            ),
          'sort' => true,
          'show_admin_column' => true,
          'update_count_callback' => '_update_generic_term_count'
          );
      } elseif ($type == 'page') {
        $tax_slug = 'folder';
        $args = array(
          'hierarchical' => true,
          'labels' => array(
            'name' => _x( 'Folders', 'taxonomy general name' ),
            'singular_name' => _x( 'Folder', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search Folders' ),
            'all_items' => __( 'All Folders' ),
            'parent_item' => __( 'Parent Folder' ),
            'parent_item_colon' => __( 'Parent Folder:' ),
            'edit_item' => __( 'Edit Folder' ),
            'update_item' => __( 'Update Folder' ),
            'add_new_item' => __( 'Add New Folder' ),
            'new_item_name' => __( 'New Folder Name' ),
            'menu_name' => __( 'Folders' ),
            ),
          'sort' => true,
          'show_admin_column' => true,
          'update_count_callback' => '_update_generic_term_count'
          );
      }
      register_taxonomy($tax_slug, $type, $args);
    }
  }
}
add_action( 'init', 'add_custom_posttype_folder_taxonomy', 0 );

function folders_add_posttype_taxonomy_filters() {
  global $globOptions, $folder_types, $typenow;
  if ($folder_types) {
    foreach($folder_types as $type) {
      if($type == 'page') {
        $tax_slug = 'folder';
      } else {
        $tax_slug = $type.'_folder';
      }
      if( $typenow == $type ) {
        $tax_obj = get_taxonomy($tax_slug);
        $tax_name = $tax_obj->labels->name;
        $terms = get_terms($tax_slug);
        if(count($terms)) {
          echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
          echo "<option value=''>Show All $tax_name</option>";
          foreach ($terms as $term) {
            echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
          }
          echo "</select>";
        }
      }
    }
  }
}
add_action( 'restrict_manage_posts', 'folders_add_posttype_taxonomy_filters' );

// Add Folders into Admin Menu
function folders_posttype_in_admin_menu() {
  global $globOptions, $folder_types, $menu;
  if (empty($folder_types)) {
    return;
  }

  foreach($folder_types as $type) {
    $itemKey = searchForId($type, $menu);
    switch (true) {
      case ($type == 'attachment'):
      $itemKey = 10;
      $edit = 'upload.php';
      break;
      case ($type === 'post'):
      $edit = 'edit.php';
      $itemKey = 5;
      break;
      default:
      $edit = 'edit.php';
      break;
    }


    $folder = $type == 'attachment' ? 'media' : $type;
    $upper = $type == 'attachment' ? 'Media' : ucwords(str_replace(array('-','_'), ' ', $type));
    if ($type == 'page') {
      $tax_slug = 'folder';
    } else {
      $tax_slug = $folder . '_folder';
    }


    if ($type == 'attachment') {
      add_menu_page( 'Media Folders', 'Media Folders', 'publish_pages', "edit-tags.php?taxonomy=media_folder&post_type=attachment", false, 'dashicons-portfolio', "{$itemKey}.5" );
    } else {
      add_menu_page( $upper.' Folders', "{$upper} Folders", 'publish_pages', "{$edit}?post_type={$type}&{$tax_slug}", false, 'dashicons-portfolio', "{$itemKey}.5" );
    }
    add_submenu_page( "{$edit}?post_type={$type}&{$tax_slug}", 'Add/Edit Folders', 'Add/Edit Folders', 'publish_pages', "edit-tags.php?taxonomy={$tax_slug}&post_type={$type}", false );
    $tax_obj = get_taxonomy($tax_slug);
    $tax_name = $tax_obj->labels->name;
    $terms = get_terms($tax_slug);

    if($terms) {
      foreach ($terms as $term) {
        if ($type == 'attachment') {
          add_submenu_page( "edit-tags.php?taxonomy=media_folder&post_type=attachment", $term->name, $term->name, 'publish_pages', "{$edit}?taxonomy=media_folder&term={$term->slug}", false );
        } else {
          add_submenu_page( "{$edit}?post_type={$type}&{$tax_slug}", $term->name, $term->name, 'publish_pages', "{$edit}?post_type={$type}&{$tax_slug}={$term->slug}", false );
        }
      }
    }
    remove_submenu_page( "{$edit}", "edit-tags.php?taxonomy=post_folder" );
    remove_submenu_page( "{$edit}?post_type={$type}", "edit-tags.php?taxonomy={$tax_slug}&amp;post_type={$type}" );
    remove_submenu_page( "{$edit}", "edit-tags.php?taxonomy={$tax_slug}&amp;post_type={$type}" );
  }
}
add_action('admin_menu', 'folders_posttype_in_admin_menu');

function add_folder_posttype_column( $columns ) {
  $myCustomColumns = array(
    'folder' => __( 'Folder', 'Folder' )
    );
  $columns = array_merge( $columns, $myCustomColumns );
  return $columns;
}
add_filter( 'manage_posts_columns', 'add_folder_posttype_column' );

function add_folder_posttype_column_content( $column_name, $post_id ) {
  if ( $column_name == 'folder' ) {
    $getType = get_post_type($post_id);
    if ($getType == 'page') {
      $ter = wp_get_post_terms($post_id, 'folder' );
    } else {
      $ter = wp_get_post_terms($post_id, $getType.'_folder' );
    }
    $count = count($ter);

    if ($ter && is_wp_error($ter) === false) {
        foreach ($ter as $key => $term) {
            if ($term->name) {
                if ($count === $key + 1) {
                  echo $term->name;
                }
                else {
                  echo $term->name.', ';
                }
            }
        }
    }
  }
}
add_action( 'manage_posts_custom_column', 'add_folder_posttype_column_content', 10, 2 );
