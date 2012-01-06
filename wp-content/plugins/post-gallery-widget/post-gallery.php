<?php
/*
Plugin Name: Rotating Post Gallery
Plugin URI: http://wpmututorials.com/plugins/post-gallery-widget/
Description: A Rotating Gallery Widget using a custom post type to create Gallery Posts.
Author: Ron Rennick
Version: 0.3
Author URI: http://ronandandrea.com/

This plugin is a collaboration project with contributions from the CUNY Acedemic Commons (http://dev.commons.gc.cuny.edu/)
*/
/* Copyright:   (C) 2010 Ron Rennick, All rights reserved.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// @todo - add text domain and .po file
/*
 * Set values for post type
 */
class PGW_Post_Type {
	var $post_type_name = 'pgw_post';
	var $handle = 'pgw-meta-box';
	var $attachments = null;

	var $post_type = array(
		'menu_position' => '1',
		'taxonomies' => array(),
		'public' => true,
		'show_ui' => true,
		'rewrite' => false,
		'query_var' => false,
		'supports' => array( 'title', 'editor', 'author' )
		);


	function PGW_Post_Type() {
		return $this->__construct();
	}

	function  __construct() {
		add_action( 'init', array( &$this, 'init' ) );

		load_plugin_textdomain( 'post-gallery-widget' );
		$this->post_type['label'] = __( 'Gallery Posts', 'post-gallery-widget' );
		$this->post_type['singular_label'] = __( 'Gallery Post', 'post-gallery-widget' );
		$this->post_type['description'] = $this->post_type['singular_label'];
		$this->post_type['labels'] = array(
			'name' => $this->post_type['label'],
			'singular_name' => $this->post_type["singular_label"],
			'add_new' => 'Add ' . $this->post_type["singular_label"],
			'add_new_item' => 'Add New ' . $this->post_type["singular_label"],
			'edit' => 'Edit',
			'edit_item' => 'Edit ' . $this->post_type["singular_label"],
			'new_item' => 'New ' . $this->post_type["singular_label"],
			'view' => 'View ' . $this->post_type["singular_label"],
			'view_item' => 'View ' . $this->post_type["singular_label"],
			'search_items' => 'Search ' . $this->post_type["singular_label"],
			'not_found' => 'No ' . $this->post_type["singular_label"] . ' Found',
			'not_found_in_trash' => 'No ' . $this->post_type["singular_label"] . ' Found in Trash'
			);
	}

	function init() {
		register_post_type( $this->post_type_name, $this->post_type );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 20 );
		add_action( 'save_post', array( &$this, 'save_post' ), 12, 2 );
	}

	function query_posts( $num_posts = -1, $size = 'full', $order = false ) {
		if( !$order )
			$order = 'date';
		switch( $order ) {
			case 'rand':
				$query = sprintf( 'showposts=%d&post_type=%s&orderby=none', $num_posts, $this->post_type_name );
				break;
			case 'date':
				$query = sprintf( 'showposts=%d&post_type=%s&orderby=post_date&order=DESC', $num_posts, $this->post_type_name );
				break;
			case 'menu':
				$query = sprintf( 'showposts=%d&post_type=%s&orderby=menu_order&order=ASC', $num_posts, $this->post_type_name );
				break;
			default:
				return array();
				break;
		}
		$posts = new WP_Query( $query );
		$gallery = array();
		$child = array( 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'none' );
		$index = -1;
		while( $posts->have_posts() ) {
			if( 'rand' == $order ) {
				while( $index < 0 || array_key_exists( $index, $gallery ) )
					$index = rand( 0, ( count( $gallery ) * 2 ) + 10000 );
			} else
				$index++;

			$posts->the_post();
			$child['post_parent'] = get_the_ID();
			$attachments = get_children( $child );
			if( empty( $attachments ) )
				continue;

			$p = new stdClass();
			$p->post_title = get_the_title();
			$p->post_excerpt = get_the_content();
			if( ( $c = count( $attachments ) ) > 1 ) {
				$x = rand( 1, $c );
				while( $c > $x++ )
					next( $attachments );
			}
			$p->tag = wp_get_attachment_image( key( $attachments ), $size, false );
			$gallery[$index] = $p;
		}
		if( 'rand' == $order )
			ksort( $gallery );

		wp_reset_query();
		return $gallery;
	}
	function admin_menu() {
		add_action( 'do_meta_boxes', array( &$this, 'add_metabox' ), 9 );
	}
	function add_metabox() {
		global $post;
		if( empty( $post ) || $this->post_type_name != $post->post_type )
			return;
		$child = array( 'post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'none' );
		$this->attachments = get_children( $child );
		if( !empty( $this->attachments ) )
			add_meta_box( $this->handle, __( 'Attached Images', 'post-gallery-widget' ), array( &$this, 'image_metabox' ), $this->post_type_name, 'normal' );

		add_meta_box( 'pageparentdiv', __('Attributes'), array( &$this, 'menu_order_metabox' ), $this->post_type_name, 'side' );
	}
	function image_metabox() {
		echo '<p>';
		foreach( (array) $this->attachments as $k => $v )
			echo '<span style="padding:3px;">' . wp_get_attachment_image( $k, 'thumbnail', false ) . '</span>';
		echo '</p>';
	}
	function menu_order_metabox() { 
		global $post; ?>
<p><strong><?php _e( 'Display Order', 'post-gallery-widget' ) ?></strong></p>
<p><label class="screen-reader-text" for="menu_order"><?php _e( 'Display Order', 'post-gallery-widget' ); ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order) ?>" /></p>
<?php	}
	function save_post( $post_id, $post ) {
		global $wpdb;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if ( defined('DOING_AJAX') && DOING_AJAX ) return;
		if ( defined('DOING_CRON') && DOING_CRON ) return;
		if ( $post->post_status != 'publish' || $post->post_type != $this->post_type_name ) return;

		$perm = 'edit_' . $this->post_type_name;
		if ( current_user_can( $perm, $post_id ) ) {
			$where = $wpdb->prepare( "WHERE post_status = 'publish' AND post_type = %s", $this->post_type_name );
			$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM {$wpdb->posts} {$where} AND menu_order = %d", $post->menu_order ) );
			if( $count > 1 )
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET menu_order = menu_order + 1 {$where} AND menu_order >= %d AND ID != %d", $post->menu_order, $post->ID ) );
		}
	}
}

$pgw_post_type = new PGW_Post_Type();

class Rotating_Post_Widget extends WP_Widget {
	// Note: these strings match strings in WP exactly. If changed the gettext domain will need to be added
	var $sizes = array( 'full' => 'Full Size', 'medium' => 'Medium', 'large' => 'Large' );
	var $order = array( 'date' => 'Date descending', 'menu' => 'Menu order ascending', 'rand' => 'Random' );
	var $id = 'post_gallery_widget';
	var $queued = false;

	function Rotating_Post_Widget() {
		$widget_ops = array( 'description' => __( 'Rotating Post Gallery Widget', 'post-gallery-widget' ) );
		$this->WP_Widget( $this->id, __( 'Rotating Post Gallery Widget', 'post-gallery-widget' ), $widget_ops );
		add_action( 'wp_head', array( &$this, 'wp_head' ), 1 );
		add_action( 'wp_footer', array( &$this, 'wp_footer' ), 2 );
	}

	function widget( $args, $instance ) {
		global $pgw_post_type;
		extract( $args );
		echo $before_widget; ?>
			<div id="pgw-gallery<?php echo ( $instance['size'] ? '-' . $instance['size'] : '' ); ?>">
				<div class="slideshow">
<?php		$first = true;
		$num_posts = -1;
		if( $instance['how_many'] > 0 )
			$num_posts = $instance['how_many'];
		if( !empty( $pgw_post_type ) ) {
			$posts = $pgw_post_type->query_posts( $num_posts, $instance['size'], $instance['order'] );
			foreach( $posts as $p ) { ?>
		<div class="slide<?php if( $first ) { echo ' first_slide'; } ?>">
<?php				 echo apply_filters( 'pgw_image_markup', $p->tag ); ?>
			<span><h2><?php echo $p->post_title; ?></h2>
				<p><?php echo $p->post_excerpt; ?><br /></p>
			</span>
		</div>
<?php				$first = false;
			}
		}
?>
				</div>
				<a id="pgw-prev" href="#">Previous</a>
				<a id="pgw-next" href="#">Next</a>
				<div style="clear:both;"></div>
			</div>
<?php 		echo $after_widget;
		if( $this->queued )
			$this->queued = false;
	}

 	function update( $new_instance, $old_instance ) {
		$new_instance['how_many'] = intval( $new_instance['how_many'] );
		if( !in_array( $new_instance['size'], array_keys( $this->sizes ) ) )
			$new_instance['size'] = 'full';
		if( !in_array( $new_instance['order'], array_keys( $this->order ) ) )
			$new_instance['order'] = 'date';

		return $new_instance;
	}

	function form( $instance ) { ?>
		<p><label for="<?php echo $this->get_field_id( 'how_many' ); ?>"><?php _e( 'How many gallery posts:', 'post-gallery-widget' ) ?></label>
		<input type="text" id="<?php echo $this->get_field_id( 'how_many' ); ?>" name="<?php echo $this->get_field_name( 'how_many' ); ?>" value="<?php echo ( $instance['how_many'] > 0 ? esc_attr( $instance['how_many'] ) : '' ); ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Image Size:', 'post-gallery-widget' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'size' ); ?>" id="<?php echo $this->get_field_id( 'size' ); ?>" class="widefat">
<?php		foreach( $this->sizes as $k => $v ) { ?>
				<option value="<?php echo $k; ?>"<?php selected( $instance['size'], $k ); ?>><?php _e( $v ); ?></option>
<?php		} ?>
			</select>
		</p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Post order:', 'post-gallery-widget' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'order' ); ?>" id="<?php echo $this->get_field_id( 'order' ); ?>" class="widefat">
<?php		foreach( $this->order as $k => $v ) { ?>
				<option value="<?php echo $k; ?>"<?php selected( $instance['order'], $k ); ?>><?php _e( $v ); ?></option>
<?php		} ?>
			</select>
		</p>
<?php	}

	function wp_head() {
		if( !is_admin() ) {
			$this->queued = true;
			$url = plugin_dir_url( __FILE__ );

			wp_enqueue_style( 'pgw-cycle', $url . 'css/style.css' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'pgw-cycle-js', $url . 'js/jquery.cycle.lite.min.js', array( 'jquery' ), '1.4', true );
			wp_enqueue_script( 'pgw-cycle-slide-js', $url . 'js/pgw-slide.js', false, false, true );
		}
	}

	function wp_footer() {
		if( $this->queued ) {
			wp_deregister_script( 'pgw-cycle-js' );
			wp_deregister_script( 'pgw-cycle-slide-js' );
		}
	}
}

function register_rotating_post_widget() {
	register_widget( 'Rotating_Post_Widget' );
}
add_action( 'widgets_init', 'register_rotating_post_widget' );
