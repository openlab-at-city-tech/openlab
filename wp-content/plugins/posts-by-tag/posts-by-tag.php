<?php
/**
 * Plugin Name: Posts By Tag
 * Plugin Script: posts-by-tag.php
 * Plugin URI: http://sudarmuthu.com/wordpress/posts-by-tag
 * Description: Provide sidebar widgets that can be used to display posts from a set of tags in the sidebar
 * Author: Sudar
 * Donate Link: http://sudarmuthu.com/if-you-wanna-thank-me
 * License: GPL
 * Version: 3.2.1
 * Author URI: http://sudarmuthu.com/
 * Text Domain: posts-by-tag
 * Domain Path: languages/
 * === RELEASE NOTES ===
 * Check readme file for full release notes
 */

/**
 * Copyright 2009  Sudar Muthu  (email : sudar@sudarmuthu.com)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Widget class
if ( ! class_exists( 'TagWidget' ) ) {
	require_once dirname( __FILE__ ) . '/include/class-tagwidget.php';
}

// Util class
if ( ! class_exists( 'Posts_By_Tag_Util' ) ) {
	require_once dirname( __FILE__ ) . '/include/class-posts-by-tag-util.php';
}

// template functions
require_once dirname( __FILE__ ) . '/include/template-functions.php';

// Google Analytics (was a pro add-on earlier)
require_once dirname( __FILE__ ) . '/include/posts-by-tag-google-analytics.php';

/**
 * The main Plugin class
 *
 * @author Sudar
 * @package Posts_By_Tag
 */
class Posts_By_Tag {

	// boolean fields that needs to be validated
	private $boolean_fields     = array(
		'tag_from_post',
		'tag_from_post_slug',
		'tag_from_post_custom_field',
		'exclude',
		'exclude_current_post',
		'excerpt',
		'excerpt_filter',
		'content',
		'content_filter',
		'thumbnail',
		'author',
		'date',
		'tag_links',
	);

	// constants
	const VERSION               = '3.2.1';
	const CUSTOM_POST_FIELD_OLD = 'posts_by_tag_page_fields'; // till v 2.7.4
	const CUSTOM_POST_FIELD     = '_posts_by_tag_page_fields';

	// Filters
	const FILTER_PERMALINK      = 'pbt_permalink_filter';
	const FILTER_ONCLICK        = 'pbt_onclick_filter';
	const FILTER_PRO_ANALYTICS  = 'pbt_pro_analytics_filter';

	public static $TEMPLATES    = array( '[TAGS]', '[POST_ID]', '[POST_SLUG]' );

	/**
	 * Initalize the plugin by registering the hooks
	 */
	function __construct() {

		// Load localization domain
		$this->translations = dirname( plugin_basename( __FILE__ ) ) . '/languages/' ;
		load_plugin_textdomain( 'posts-by-tag', false, $this->translations );

		// Register hooks
		add_action( 'admin_print_scripts', array( $this, 'add_script' ) );
		add_action( 'admin_head', array( $this, 'add_script_config' ) );

		/* Use the admin_menu action to define the custom boxes */
		add_action( 'admin_menu', array( $this, 'add_custom_box' ) );

		/* Use the save_post action to do something with the data entered */
		add_action( 'save_post', array( $this, 'save_postdata' ) );

		// Add more links in the plugin listing page
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_links' ), 10, 2 );

		//Short code
		add_shortcode( 'posts-by-tag', array( $this, 'shortcode_handler' ) );
	}

	/**
	 * Add script to admin page
	 */
	function add_script() {
		if ( $this->is_on_plugin_page() ) {
			// Build in tag auto complete script
			wp_enqueue_script( 'suggest' );
		}
	}

	/**
	 * add script to admin page
	 */
	function add_script_config() {
		// Add script only to Widgets page
		if ( $this->is_on_plugin_page() ) {
			//TODO: Move this to a seperate js file
?>

<script type="text/javascript">
    // Function to add auto suggest
    function setSuggest(id) {
        jQuery('#' + id).suggest("<?php echo get_bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag", {multiple:true, multipleSep: ","});
    }

    function thumbnailChanged(id, size_id) {
        if (jQuery('#' + id).is(':checked')) {
            jQuery('#'  + size_id).parents('p').show();
            thumbnailSizeChanged(size_id);
        } else {
            jQuery('#' + size_id).parents('p').hide();
        }
    }

    function thumbnailSizeChanged(id) {
        if (jQuery('#' + id).val() === 'custom') {
            jQuery('#' + id + '-span').show();
        } else {
            jQuery('#' + id + '-span').hide();
        }
    }
</script>
<?php
		}
	}

	/**
	 * Adds additional links in the Plugin listing. Based on http://zourbuth.com/archives/751/creating-additional-wordpress-plugin-links-row-meta/
	 *
	 * @param unknown $links
	 * @param unknown $file
	 * @return unknown
	 */
	function add_plugin_links( $links, $file ) {
		$plugin = plugin_basename( __FILE__ );

		if ( $file == $plugin ) { // only for this plugin
			return array_merge( $links,
				array( '<a href="http://sudarmuthu.com/wordpress/posts-by-tag/pro-addons?utm_source=wpadmin&utm_campaign=PostsByTag&utm_medium=plugin-listing&utm_content=pro-addon" target="_blank">' . __( 'Buy Addons', 'posts-by-tag' ) . '</a>' )
			);
		}

		return $links;
	}

	/**
	 * Adds the custom section in the edit screens for all post types
	 */
	function add_custom_box() {
		$post_types = get_post_types( array(), 'objects' );
		foreach ( $post_types as $post_type ) {
			if ( $post_type->show_ui ) {
				add_meta_box(
					'posts_by_tag_page_box',
					__( 'Posts By Tag Page Fields', 'posts-by-tag' ),
					array( $this, 'inner_custom_box' ),
					$post_type->name,
					'side'
				);
			}
		}
	}

	/**
	 * Prints the inner fields for the custom post/page section
	 */
	function inner_custom_box() {
		global $post;
		$post_id = $post->ID;

		$widget_title = '';
		$widget_tags = '';

		if ( $post_id > 0 ) {
			Posts_By_Tag::update_postmeta_key( $post_id );
			$posts_by_tag_page_fields = get_post_meta( $post_id, self::CUSTOM_POST_FIELD, true );

			if ( isset( $posts_by_tag_page_fields ) && is_array( $posts_by_tag_page_fields ) ) {
				$widget_title = $posts_by_tag_page_fields['widget_title'];
				$widget_tags = $posts_by_tag_page_fields['widget_tags'];
			}
		}
		// Use nonce for verification
?>
        <input type="hidden" name="posts_by_tag_noncename" id="posts_by_tag_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) );?>" />
        <p>
            <label> <?php _e( 'Widget Title', 'posts-by-tag' ); ?> <input type="text" name="widget_title" value ="<?php echo $widget_title; ?>"></label><br>
            <label> <?php _e( 'Widget Tags', 'posts-by-tag' ); ?> <input type="text" name="widget_tags" id = "widget_tags" value ="<?php echo $widget_tags; ?>" onfocus ="setSuggest('widget_tags');"></label>
        </p>
<?php
	}

	/**
	 * When the post is saved, saves our custom data
	 *
	 * @param string  $post_id
	 * @return string return post id if nothing is saved
	 */
	function save_postdata( $post_id ) {

		// Don't do anything during Autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times

		if ( ! array_key_exists( 'posts_by_tag_noncename', $_POST ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $_POST['posts_by_tag_noncename'], plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// OK, we're authenticated: we need to find and save the data

		$fields = array();

		if ( isset( $_POST['widget_title'] ) ) {
			$fields['widget_title'] = $_POST['widget_title'];
		} else {
			$fields['widget_title'] = '';
		}

		if ( isset( $_POST['widget_tags'] ) ) {
			$fields['widget_tags'] = $_POST['widget_tags'];
		} else {
			$fields['widget_tags'] = '';
		}

		update_post_meta( $post_id, self::CUSTOM_POST_FIELD, $fields );

	}

	/**
	 * Expand the shortcode
	 *
	 * @param <array> $attributes
	 * @return unknown
	 */
	function shortcode_handler( $attributes ) {
		$options = shortcode_atts( array(
				'tags'                       => '',   // comma Separated list of tags
				'number'                     => 5,
				'tag_from_post'              => false,
				'tag_from_post_slug'         => false,
				'tag_from_post_custom_field' => false,
				'exclude'                    => false,
				'exclude_current_post'       => false,
				'excerpt'                    => false,
				'excerpt_filter'             => true,
				'content'                    => false,
				'content_filter'             => true,
				'thumbnail'                  => false,
				'thumbnail_size'             => 'thumbnail',
				'thumbnail_size_width'       => 100,
				'thumbnail_size_height'      => 100,
				'order_by'                   => 'date',
				'order'                      => 'desc',
				'author'                     => false,
				'date'                       => false,
				'tag_links'                  => false,
				'link_target'                => '',
			), $attributes );

		$options = Posts_By_Tag_Util::validate_boolean_options( $options, $this->boolean_fields );
		$tags = $options['tags'];

		// call the template function
		$output = get_posts_by_tag( $tags, $options );

		if ( $options['tag_links'] && ! $options['exclude'] ) {
			$output .= Posts_By_Tag_Util::get_tag_more_links( $tags );
		}

		return $output;
	}

	/**
	 * Check whether you are on a Plugin page
	 *
	 * @author Sudar
	 * @return boolean
	 */
	private function is_on_plugin_page() {
		if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) ||
			strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ||
			strstr( $_SERVER['REQUEST_URI'], 'wp-admin/edit.php' ) ||
			$this->is_widget_page() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check whether you are on the widget page
	 *
	 * @return unknown
	 */
	private function is_widget_page() {
		if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/widgets.php' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update postmeta key.
	 *
	 * Uptill v2.7.4 the Plugin was using a old postmeta key without '_'.
	 * This function updates the postmeta key. Eventually this function will be removed.
	 *
	 * @param unknown $post_id
	 * @return void
	 */
	public static function update_postmeta_key( $post_id ) {
		$old_value = get_post_meta( $post_id, self::CUSTOM_POST_FIELD_OLD, true );

		if ( isset( $old_value ) && is_array( $old_value ) ) {
			update_post_meta( $post_id, self::CUSTOM_POST_FIELD, $old_value );
			delete_post_meta( $post_id, self::CUSTOM_POST_FIELD_OLD );
		}
	}
}

/**
 * Start this plugin once all other plugins are fully loaded
 */
function posts_by_tag_init() {
	global $Posts_By_Tag;
	$Posts_By_Tag = new Posts_By_Tag();
}
add_action( 'init', 'posts_by_tag_init' );

// Init Simple Tags widget
function simple_tags_register_widget() {
  return register_widget("TagWidget");
}
add_action( 'widgets_init', 'simple_tags_register_widget' );

?>
