<?php
/**
 * The plugin creation file.
 *
 * @package Reading_Time_WP
 *
 * Plugin Name: Reading Time WP
 * Plugin URI: https://jasonyingling.me/reading-time-wp/
 * Description: Add an estimated reading time to your posts.
 * Version: 2.0.16
 * Author: Jason Yingling
 * Author URI: https://jasonyingling.me
 * License: GPL2
 * Text Domain: reading-time-wp
 * Domain Path: /languages
 *
 * Copyright 2019  Jason Yingling  (email : yingling017@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class for calculating reading time.
 *
 * The class that contains all functions for calculating reading time.
 *
 * @since 1.0.0
 */
class Reading_Time_WP {

	/**
	 * Add label option using add_option if it does not already exist.
	 *
	 * @var string
	 */
	public $reading_time;

	/**
	 * Allowed HTML tags for setting fields.
	 *
	 * @var array
	 */
	public $rtwp_kses = array(
		'br'     => array(),
		'em'     => array(),
		'b'      => array(),
		'strong' => array(),
	);

	/**
	 * Construct function for Reading Time WP.
	 *
	 * Create default settings on plugin activation. Add shortcode. Add options.
	 * Add menu page.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'reading-time-wp', false, basename( dirname( __FILE__ ) ) . '/languages/' );

		$default_settings = array(
			'label'              => __( 'Reading Time: ', 'reading-time-wp' ),
			'postfix'            => __( 'minutes', 'reading-time-wp' ),
			'postfix_singular'   => __( 'minute', 'reading-time-wp' ),
			'wpm'                => 300,
			'before_content'     => true,
			'before_excerpt'     => true,
			'exclude_images'     => false,
			'include_shortcodes' => false,
		);

		$rtwp_post_type_args = array(
			'public' => true,
		);

		$rtwp_post_type_args = apply_filters( 'rtwp_post_type_args', $rtwp_post_type_args );

		$rtwp_post_types = get_post_types( $rtwp_post_type_args );

		foreach ( $rtwp_post_types as $rtwp_post_type ) {
			if ( 'attachment' === $rtwp_post_type ) {
				continue;
			}
			$default_settings['post_types'][ $rtwp_post_type ] = true;
		}

		$rt_reading_time_options = get_option( 'rt_reading_time_options' );

		add_shortcode( 'rt_reading_time', array( $this, 'rt_reading_time' ) );
		add_option( 'rt_reading_time_options', $default_settings );
		add_action( 'admin_menu', array( $this, 'rt_reading_time_admin_actions' ) );

		$rt_before_content = isset($rt_reading_time_options['before_content'] ) ? $this->rt_convert_boolean( $rt_reading_time_options['before_content'] ) : false;

		if ( isset( $rt_before_content ) && true === $rt_before_content ) {
			add_filter( 'the_content', array( $this, 'rt_add_reading_time_before_content' ) );
		}

		$rt_before_excerpt = isset( $rt_reading_time_options['before_excerpt'] ) ? $this->rt_convert_boolean( $rt_reading_time_options['before_excerpt'] ) : false;

		if ( isset( $rt_before_excerpt ) && true === $rt_before_excerpt ) {
			add_filter( 'get_the_excerpt', array( $this, 'rt_add_reading_time_before_excerpt' ), 1000 );
		}

	}

	/**
	 * Calculate the reading time of a post.
	 *
	 * Gets the post content, counts the images, strips shortcodes, and strips tags.
	 * Then counts the words. Converts images into a word count. And outputs the
	 * total reading time.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $rt_post_id The Post ID.
	 * @param array $rt_options The options selected for the plugin.
	 * @return string|int The total reading time for the article or string if it's 0.
	 */
	public function rt_calculate_reading_time( $rt_post_id, $rt_options ) {

		$rt_content       = get_post_field( 'post_content', $rt_post_id );
		$number_of_images = substr_count( strtolower( $rt_content ), '<img ' );

		if ( ! isset( $rt_options['include_shortcodes'] ) ) {
			$rt_content = strip_shortcodes( $rt_content );
		}

		$rt_content = wp_strip_all_tags( $rt_content );
		$word_count = count( preg_split( '/\s+/', $rt_content ) );

		if ( isset( $rt_options['exclude_images'] ) && ! $rt_options['exclude_images'] ) {
			// Calculate additional time added to post by images.
			$additional_words_for_images = $this->rt_calculate_images( $number_of_images, $rt_options['wpm'] );
			$word_count                 += $additional_words_for_images;
		}

		$word_count = apply_filters( 'rtwp_filter_wordcount', $word_count );

		$this->reading_time = $word_count / $rt_options['wpm'];

		// If the reading time is 0 then return it as < 1 instead of 0.
		if ( 1 > $this->reading_time ) {
			$this->reading_time = __( '< 1', 'reading-time-wp' );
		} else {
			$this->reading_time = ceil( $this->reading_time );
		}

		return $this->reading_time;

	}

	/**
	 * Adds additional reading time for images
	 *
	 * Calculate additional reading time added by images in posts. Based on calculations by Medium. https://blog.medium.com/read-time-and-you-bc2048ab620c
	 *
	 * @since 1.1.0
	 *
	 * @param int   $total_images number of images in post.
	 * @param array $wpm words per minute.
	 * @return int  Additional time added to the reading time by images.
	 */
	public function rt_calculate_images( $total_images, $wpm ) {
		$additional_time = 0;
		// For the first image add 12 seconds, second image add 11, ..., for image 10+ add 3 seconds.
		for ( $i = 1; $i <= $total_images; $i++ ) {
			if ( $i >= 10 ) {
				$additional_time += 3 * (int) $wpm / 60;
			} else {
				$additional_time += ( 12 - ( $i - 1 ) ) * (int) $wpm / 60;
			}
		}

		return $additional_time;
	}

	/**
	 * Output the proper postfix for the reading time.
	 *
	 * @since 2.0.5
	 *
	 * @param string|int $time The total reading time for the article or string if it's 0.
	 * @param string     $singular The postfix singular label.
	 * @param string     $multiple The postfix label.
	 *
	 * @return string $postfix The calculated postfix.
	 */
	public function rt_add_postfix( $time, $singular, $multiple ) {
		if ( (int) $time > 1 ) {
			$postfix = $multiple;
		} else {
			$postfix = $singular;
		}

		$postfix = apply_filters( 'rt_edit_postfix', $postfix, $time, $singular, $multiple );

		return $postfix;
	}

	/**
	 * Creates the [rt_reading_time] shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts The attributes of the shortcode.
	 * @param string $content The content of the shortcode.
	 * @return string.
	 */
	public function rt_reading_time( $atts, $content = null ) {

		$atts = shortcode_atts(
			array(
				'label'            => '',
				'postfix'          => '',
				'postfix_singular' => '',
				'post_id'          => '',
			),
			$atts,
			'rt_reading_time'
		);

		$rt_reading_time_options = get_option( 'rt_reading_time_options' );

		// If post_id attribute was specified that exists, then use that to calculate read time, else use the current post ID.
		$rt_post = $atts['post_id'] && ( get_post_status( $atts['post_id'] ) ) ? $atts['post_id'] : get_the_ID();

		$this->rt_calculate_reading_time( $rt_post, $rt_reading_time_options );

		$calculated_postfix = $this->rt_add_postfix( $this->reading_time, $atts['postfix_singular'], $atts['postfix'] );

		return '<span class="span-reading-time rt-reading-time"><span class="rt-label rt-prefix">' . wp_kses( $atts['label'], $this->rtwp_kses ) . '</span> <span class="rt-time"> ' . esc_html( $this->reading_time ) . '</span> <span class="rt-label rt-postfix">' . wp_kses( $calculated_postfix, $this->rtwp_kses ) . '</span></span>';
	}

	/**
	 * Include the Reading Time Admin page.
	 *
	 * The reading-time-admin.php contains everything needed to handle
	 * the options in the admin screen.
	 *
	 * @since 1.0.0
	 */
	public function rt_reading_time_admin() {
		include 'rt-reading-time-admin.php';
	}

	/**
	 * Create the options page for the admin screen.
	 *
	 * @since 1.0.0
	 */
	public function rt_reading_time_admin_actions() {
		add_options_page(
			__( 'Reading Time WP Settings', 'reading-time-wp' ),
			__( 'Reading Time WP', 'reading-time-wp' ),
			'manage_options',
			'rt-reading-time-settings',
			array( $this, 'rt_reading_time_admin' )
		);
	}

	/**
	 * Adds the reading time before the_content.
	 *
	 * If the option is selected to automatically add the reading time before
	 * the_content, the reading time is calculated and added to the beginning of the_content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The original post content.
	 * @return string The post content with reading time prepended.
	 */
	public function rt_add_reading_time_before_content( $content ) {
		$rt_reading_time_options = get_option( 'rt_reading_time_options' );

		// Get the post type of the current post.
		$rtwp_current_post_type = get_post_type();

		if ( ! isset( $rt_reading_time_options['post_types'] ) ) {
			$rt_reading_time_options['post_types'] = array();
		}

		// If the current post type isn't included in the array of post types or it is and set to false, don't display it.
		if ( ! isset( $rt_reading_time_options['post_types'][ $rtwp_current_post_type ] ) || ! $rt_reading_time_options['post_types'][ $rtwp_current_post_type ] ) {
			return $content;
		}

		$original_content = $content;
		$rt_post          = get_the_ID();

		$this->rt_calculate_reading_time( $rt_post, $rt_reading_time_options );

		$label            = $rt_reading_time_options['label'];
		$postfix          = $rt_reading_time_options['postfix'];
		$postfix_singular = $rt_reading_time_options['postfix_singular'];

		if ( in_array( 'get_the_excerpt', $GLOBALS['wp_current_filter'], true ) ) {
			return $content;
		}

		$calculated_postfix = $this->rt_add_postfix( $this->reading_time, $postfix_singular, $postfix );

		$content  = '<span class="rt-reading-time" style="display: block;"><span class="rt-label rt-prefix">' . wp_kses( $label, $this->rtwp_kses ) . '</span> <span class="rt-time">' . esc_html( $this->reading_time ) . '</span> <span class="rt-label rt-postfix">' . wp_kses( $calculated_postfix, $this->rtwp_kses ) . '</span></span>';
		$content .= $original_content;
		return $content;
	}

	/**
	 * Adds the reading time before the_excerpt.
	 *
	 * If the options is selected to automatically add the reading time before
	 * the_excerpt, the reading time is calculated and added to the beginning of the_excerpt.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The original content of the_excerpt.
	 * @return string The excerpt content with reading time prepended.
	 */
	public function rt_add_reading_time_before_excerpt( $content ) {
		$rt_reading_time_options = get_option( 'rt_reading_time_options' );

		// Get the post type of the current post.
		$rtwp_current_post_type = get_post_type();

		if ( ! isset( $rt_reading_time_options['post_types'] ) ) {
			$rt_reading_time_options['post_types'] = array();
		}

		// If the current post type isn't included in the array of post types or it is and set to false, don't display it.
		if ( ! isset( $rt_reading_time_options['post_types'][ $rtwp_current_post_type ] ) || ! $rt_reading_time_options['post_types'][ $rtwp_current_post_type ] ) {
			return $content;
		}

		$original_content = $content;
		$rt_post          = get_the_ID();

		$this->rt_calculate_reading_time( $rt_post, $rt_reading_time_options );

		$label            = $rt_reading_time_options['label'];
		$postfix          = $rt_reading_time_options['postfix'];
		$postfix_singular = $rt_reading_time_options['postfix_singular'];

		$calculated_postfix = $this->rt_add_postfix( $this->reading_time, $postfix_singular, $postfix );

		$content  = '<span class="rt-reading-time" style="display: block;"><span class="rt-label rt-prefix">' . wp_kses( $label, $this->rtwp_kses ) . '</span> <span class="rt-time">' . esc_html( $this->reading_time ) . '</span> <span class="rt-label rt-postfix">' . wp_kses( $calculated_postfix, $this->rtwp_kses ) . '</span></span> ';
		$content .= $original_content;
		return $content;
	}

	/**
	 * A function to fix some bad legacy code using a string for true and false.
	 *
	 * @param string $value A string set to either 'true' or 'false'.
	 */
	public function rt_convert_boolean( $value ) {
		if ( 'true' === $value || true === $value ) {
			return true;
		} else {
			return false;
		}
	}

}

function rtwp_init() {
	global $reading_time_wp;
	$reading_time_wp = new Reading_Time_WP();
}
add_action( 'init', 'rtwp_init' );
