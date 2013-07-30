<?php
/*
Page Tagger wordpress plugin
Copyright (C) 2009-2012 Ramesh Nair

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/*
Plugin Name: Page Tagger
Plugin URI: http://www.hiddentao.com/code/wordpress-page-tagger-plugin/
Description: Enables tagging for pages. PHP 5 required.
Version: 0.4.2
Author: Ramesh Nair
Author URI: http://www.hiddentao.com/
*/


// name of my parent folder
define('PAGE_TAGGER_PARENT_DIR', basename(dirname(__FILE__)) );


load_plugin_textdomain( 'page-tagger', false, 'page-tagger/languages' );



/**
 * Inform user of the minimum PHP version requird for Page Tagger.
 */
function _page_tagger_min_version_notice()
{
	echo "<div class='updated' style='background-color:#f99;'><p><strong>WARNING:</strong> " +
        __('Page Tagger plugin requires PHP 5 or above to work', 'page-tagger') +
        "</p></div>";
}


// need atleast PHP 5
if (5 > intval(phpversion()))
{
	add_action('admin_notices', '_page_tagger_min_version_notice');
}
else
{
  // if we're at version 3 or above then we can keep it simple using WP hooks:
  global $wp_version;
  if (3 <= substr($wp_version,0,1))
  {
    // Based on code by Bjorn Wijers at https://github.com/BjornW/tag-pages

    /**
     * Add the 'post_tag' taxonomy, which is the name of the existing taxonomy
     * used for tags to the Post type page. Normally in WordPress Pages cannot
     * be tagged, but this let's WordPress treat Pages just like Posts
     * and enables the tags metabox so you can add tags to a Page.
     * NB: This uses the register_taxonomy_for_object_type() function which is only
     * in WordPress 3 and higher!
     */
    if( ! function_exists('page_tagger_register_taxonomy') ){
        function page_tagger_register_taxonomy()
        {
            register_taxonomy_for_object_type('post_tag', 'page');
        }
        add_action('admin_init', 'page_tagger_register_taxonomy');
    }

    /**
     * Display all post_types on the tags archive page. This forces WordPress to
     * show tagged Pages together with tagged Posts.
     */
    if( ! function_exists('page_tagger_display_tagged_pages_archive') ){
        function page_tagger_display_tagged_pages_archive(&$query)
        {
            if ( $query->is_archive && $query->is_tag ) {
                $q = &$query->query_vars;
                $q['post_type'] = 'any';
            }
        }
        add_action('pre_get_posts', 'page_tagger_display_tagged_pages_archive');
    }
  }
  // if we're before version 3
  else
  {
    require_once('page-tagger-class.php');
   	add_action('plugins_loaded',array('PageTagger','init'));
  }
}



