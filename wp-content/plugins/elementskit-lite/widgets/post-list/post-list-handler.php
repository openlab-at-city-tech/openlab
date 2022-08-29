<?php
namespace Elementor;


class ElementsKit_Widget_Post_List_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

	public function wp_init(){
		// post view count based on single page visit
		add_action( 'wp_head', array($this, 'ekit_track_post_views'));
	}

	public function ekit_track_post_views ($post_id) {
		if ( !is_single() ) return;
		if ( empty ( $post_id) ) {
			global $post;
			$post_id = $post->ID;    
		}
		$this->ekit_set_post_views($post_id);
	}
	

	public function ekit_set_post_views($postID) {
		$count_key = 'ekit_post_views_count';
		$count = get_post_meta($postID, $count_key, true);
		if($count==''){
			$count = 1;
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '1');
		}else{
			$count++;
			update_post_meta($postID, $count_key, $count);
		}
	}

    static function get_name() {
		return 'elementskit-post-list';
	}


	static function get_title() {
		return esc_html__( 'Post List', 'elementskit-lite' );
	}


	static function get_icon() {
		return 'eicon-bullet-list ekit-widget-icon ';
	}

	static function get_keywords() {
	    return [ 'list', 'post list', 'post', 'ekit', 'elementskit post list' ];
	}

    static function get_categories() {
        return [ 'elementskit_headerfooter' ];
	}

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'post-list/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'post-list/';
    }
}