<?php
/**
Plugin Name: Posts By Tag - Google Analytics Addon
Plugin URI: http://sudarmuthu.com/wordpress/posts-by-tag
Description: Adds Google analytics tracking code. Needs Posts By Tag Plugin
Donate Link: http://sudarmuthu.com/if-you-wanna-thank-me
License: GPL
Author: Sudar
Version: 0.1
Author URI: http://sudarmuthu.com/
Text Domain: posts-by-tag

=== RELEASE NOTES ===
2013-01-27 - v0.1 - Initial Release

*/

/*  Copyright 2010  Sudar Muthu  (email : sudar@sudarmuthu.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//TODO: make sure Posts By Tag Plugin is activated

/**
 * Main Plugin class
 */
class Posts_By_Tag_Google_Analytics {

    const VERSION   = '0.1';
    const JS_HANDLE = 'posts-by-tag-google-analytics';

    /**
     * Initalize the plugin by registering the hooks
     */
    function __construct() {
        global $Posts_By_Tag; // refering the main bulk delete Plugin object
            
        // Load localization domain
        load_plugin_textdomain( 'posts-by-tag', FALSE, $Posts_By_Tag->translations );

        // filters
        add_filter( Posts_By_Tag::FILTER_PRO_ANALYTICS, array( &$this, 'check_pro' ), 10, 2 );
        add_filter( Posts_By_Tag::FILTER_PERMALINK, array( &$this, 'filter_permalink' ), 10, 3 );
        add_filter( Posts_By_Tag::FILTER_ONCLICK, array( &$this, 'filter_onclick' ), 10, 3 );
    }

    /**
     * Check if pro is installed
     */
    function check_pro( $value ) {
        return TRUE;
    }

    /**
     * Filter function for Campaign
     */
    function filter_permalink( $permalink, $options, $post ) {
        if ( isset( $options['campaign'] ) && $options['campaign'] != '' ) {
            $campaign = $this->replace_placeholders( $options['campaign'], $options, $post );
            return $permalink . $campaign;
        } else {
            return $permalink;
        }
    }

    /**
     * Filter function for events
     */
    function filter_onclick( $onclick, $options, $post ) {
        if ( isset( $options['event'] ) && $options['event'] != '' ) {
            return $onclick . $this->replace_placeholders( $options['event'], $options, $post );
        } else {
            return $onclick;
        }
    }

    /**
     * Replace templates
     */
    private function replace_placeholders( $subject, $options, $post ) {
        $tags = array();
        foreach ( $options['tag_ids'] as $tag_id ) {
            $tag = get_term_by( 'id', $tag_id, 'post_tag' );
            $tags[] = $tag->name;
        }

        $replacement = array( implode( ',', $tags ), $post->ID, $post->post_name );
        return str_replace( Posts_By_Tag::$TEMPLATES, $replacement, $subject );
    }
}

// Start this plugin once all other plugins are fully loaded
add_action( 'init', 'Posts_By_Tag_Google_Analytics', 100 ); function Posts_By_Tag_Google_Analytics() { global $Posts_By_Tag_Google_Analytics; $Posts_By_Tag_Google_Analytics = new Posts_By_Tag_Google_Analytics(); }
?>
