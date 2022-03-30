<?php
/**
 * Plugin Name: OpenLab oEmbed Comments
 * Description: oEmbed support for comments
 * Author: OpenLab
 * Author URI: http://openlab.citytech.cuny.edu
 * Plugin URI: http://openlab.citytech.cuny.edu
 * Version: 1.0.0
 * License: GPL-2.0-or-later
 */

class Openlab_Oembed_Comments {

    /**
     * Comment ID
     */
    protected $comment_id = 0;

    /**
     * Comment Author ID
     */
    protected $comment_author_id = 0;

    /**
     * Constructor
     */
    public function __construct() {
        // Get comment data
        add_filter( 'get_comment_text', array( $this, 'get_comment_data' ), 1, 2 );
        // Attempt to embed all URLs in the post
        add_filter( 'get_comment_text', array( $this, 'autoembed' ), 8 );
        // Run [embed] shortcode
        add_filter( 'get_comment_text', array( $this, 'run_shortcode' ), 7 );
        // Allow iframe in KSES HTML
        add_filter( 'wp_kses_allowed_html', array( $this, 'wp_kses_post_tags' ), 10, 2 );
    }

    /**
	 * Get comment id and comment author id
	 *
	 * @param string     $retval  Comment text
	 * @param WP_Comment $comment The comment object.
	 */
    public function get_comment_data( $retval, $comment ) {
        $this->comment_id = $comment->comment_ID;
        $this->comment_author_id = $comment->user_id;

		return $retval;
    }

    /**
     * Check if the comment is from a guest
     * 
     * @return Boolean
     */
    private function is_guest_comment() {
        return ( $this->comment_author_id == 0 ) ? true : false;
    }

    /**
     * Override WP_Embed::autoembed to check if the comment is posted from a guest user before embeding.
     * 
     * Passes any unlinked URLs that are on their own line to WP_Embed::shortcode() for potential embedding.
     *
     * @see WP_Embed::autoembed_callback()
     *
     * @param string $content The content to be searched.
     * @return string Potentially modified $content.
     */
    public function autoembed( $content ) {
		global $wp_embed;

		if ( $this->is_guest_comment() ) {
			return $content;
		}

		return $wp_embed->autoembed( $content );
    }

    /**
     * Override WP_Embed::run_shortcode to check if the comment is posted from a guest user before embeding.
     * 
     * Process the [embed] shortcode.
     *
     * Since the [embed] shortcode needs to be run earlier than other shortcodes,
     * this function removes all existing shortcodes, registers the [embed] shortcode,
     * calls do_shortcode(), and then re-registers the old shortcodes.
     *
     * @global array $shortcode_tags
     *
     * @param string $content Content to parse
     * @return string Content with shortcode parsed
     */
    public function run_shortcode( $content ) {
		global $wp_embed;

		if ( $this->is_guest_comment() ) {
			return $content;
		}

		return $wp_embed->run_shortcode( $content );
    }

    /**
     * Add iFrame to allowed wp_kses_post tags
     *
     * @param array  $tags Allowed tags and attributes.
     * @param string $context Context for which to retrieve the tags (post, strip, data and entities).
     *
     * @return array
     */
    public function wp_kses_post_tags( $tags, $context ) {
        if( $context === 'post' ) {
            $tags['iframe'] = array(
                'src'               => true,
                'height'            => true,
                'width'             => true,
                'frameborder'       => true,
                'allowfullscreen'   => true,
            );
        }

        return $tags;
    }

}

$oloc = new Openlab_Oembed_Comments();
