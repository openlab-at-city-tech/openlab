<?php

add_action('load-post.php', 'typology_meta_boxes_setup');
add_action('load-post-new.php', 'typology_meta_boxes_setup');

/**
 * Initialize all metaboxes
 *
 * @since 1.5
 */
if (!function_exists('typology_meta_boxes_setup')) :
	function typology_meta_boxes_setup() {
		global $typenow;
		
		if ( $typenow == 'post' ) {
			
			add_action('add_meta_boxes', 'typology_load_metaboxes', 1, 2);
			add_action('save_post', 'typology_save_metaboxes', 10, 2);
		}
		
		if ($typenow == 'page') {
			
			if(isset($_GET['post']) && in_array($_GET['post'], array( get_option('page_for_posts'), get_option('page_on_front') ) ) && get_option('show_on_front') != 'posts' ){
				return false;
			}
			
			add_action('add_meta_boxes', 'typology_load_metaboxes', 1, 2);
			add_action('save_post', 'typology_save_metaboxes', 10, 2);
		}
	}
endif;

include_once get_parent_theme_file_path( '/core/admin/metaboxes/post-and-page.php' );