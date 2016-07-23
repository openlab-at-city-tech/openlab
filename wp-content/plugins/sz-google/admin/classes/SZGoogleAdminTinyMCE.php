<?php

/**
 * Module to the definition of the functions that relate to both the
 * widgets that shortcode, but also filters and actions that the module
 * can integrating with adding functionality into wordpress.
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleAdminTinyMCE'))
{
	class SZGoogleAdminTinyMCE extends SZGoogleAdmin
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */
		
		function __construct() {
			if (version_compare($GLOBALS['wp_version'],'3.9','>=')) {
				add_action('admin_head',array($this,'register_tinymce_init'));
 			}
 		}

		/**
		 * Defining filters and actions for integration of the components
		 * present in the plugin used as a standard editor with TinyMCE
		 */

		function register_tinymce_init() {
			if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
				if (get_user_option('rich_editing') == 'true') {
					add_filter('mce_external_plugins',array($this,'register_tinymce_plugin'));
					add_filter('mce_buttons',array($this,'register_tinymce_button'));
					add_action('after_wp_tiny_mce',array($this,'register_tinymce_hidden'));
				}
			}
		}

		/**
		 * Registration plugin for TinyMCE in order to load the javascript
		 * file with the configuration of the component only when necessary
		 */

		function register_tinymce_plugin($plugin_array) {
			$plugin_array['sz_google_mce_button'] = 
				plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'admin/mce/plugins/sz-google-mce.js';
			return $plugin_array;
		}

		/**
		 * Registration button to TinyMCE using the same name which is associated
		 * with the resource javascript and with which we need to define the plugin
		 */

		function register_tinymce_button($buttons) {
			array_push($buttons,'sz_google_mce_button');
			return $buttons;
		}

		/**
		 * Using the action of end processing TinyMCE to run the code
		 * that allows me to hide the translation to use the plugin
		 */

		function register_tinymce_hidden() 
		{
			$translate = $this->register_tinymce_translate();

			// Creating division with a hidden div for each
			// shortcode plugin that is active right now in session

			echo '<div id="sz-google-hidden-shortcodes" style="display:none !important">';

			foreach ($translate as $key=>$value) {
				if (shortcode_exists($key)) {
					echo '<div class="'.$key.'" ';
					echo 'data-width="'      .$value['width'] .'" ';
					echo 'data-height="'     .$value['height'].'" ';
					echo 'data-description="'.$value['title'] .'"';
					echo '></div>';
				}
			}

			echo '</div>';
		}

		/**
		 * Definition array to hold the translation strings
		 * to use the plugin defined in the js file attached
		 */

		function register_tinymce_translate() 
		{
			return array(
				'sz-gplus-profile'   => array('width'=>'430','height'=>'470','title'=>__('G+ Badge Profile'    ,'sz-google')),
				'sz-gplus-page'      => array('width'=>'430','height'=>'470','title'=>__('G+ Badge Page'       ,'sz-google')),
				'sz-gplus-community' => array('width'=>'430','height'=>'390','title'=>__('G+ Badge Community'  ,'sz-google')),
				'sz-gplus-followers' => array('width'=>'430','height'=>'300','title'=>__('G+ Badge Followers'  ,'sz-google')),
				'sz-gplus-one'       => array('width'=>'430','height'=>'450','title'=>__('G+ Button +1'        ,'sz-google')),
				'sz-gplus-share'     => array('width'=>'430','height'=>'450','title'=>__('G+ Button Share'     ,'sz-google')),
				'sz-gplus-follow'    => array('width'=>'430','height'=>'450','title'=>__('G+ Button Follow'    ,'sz-google')),
				'sz-gplus-author'    => array('width'=>'430','height'=>'290','title'=>__('G+ Widget Author'    ,'sz-google')),
				'sz-gplus-comments'  => array('width'=>'430','height'=>'270','title'=>__('G+ Widget Comments'  ,'sz-google')),
				'sz-gplus-post'      => array('width'=>'430','height'=>'170','title'=>__('G+ Widget Post'      ,'sz-google')),
				'sz-calendar'        => array('width'=>'430','height'=>'610','title'=>__('Calendar Widget'     ,'sz-google')),
				'sz-drive-embed'     => array('width'=>'430','height'=>'380','title'=>__('Drive Embed'         ,'sz-google')),
				'sz-drive-viewer'    => array('width'=>'430','height'=>'270','title'=>__('Drive Viewer'        ,'sz-google')),
				'sz-drive-save'      => array('width'=>'430','height'=>'350','title'=>__('Drive Save Button'   ,'sz-google')),
				'sz-ggroups'         => array('width'=>'430','height'=>'370','title'=>__('Groups Widget'       ,'sz-google')),
				'sz-hangouts-start'  => array('width'=>'430','height'=>'610','title'=>__('Hangout Start Button','sz-google')),
				'sz-maps'            => array('width'=>'430','height'=>'470','title'=>__('Embed Maps'          ,'sz-google')),
				'sz-panoramio'       => array('width'=>'430','height'=>'520','title'=>__('Panoramio Widget'    ,'sz-google')),
				'sz-ytvideo'         => array('width'=>'430','height'=>'580','title'=>__('Youtube Video'       ,'sz-google')),
				'sz-ytplaylist'      => array('width'=>'430','height'=>'560','title'=>__('Youtube Playlist'    ,'sz-google')),
				'sz-ytbadge'         => array('width'=>'430','height'=>'250','title'=>__('Youtube Badge'       ,'sz-google')),
				'sz-ytlink'          => array('width'=>'430','height'=>'370','title'=>__('Youtube Link'        ,'sz-google')),
				'sz-ytbutton'        => array('width'=>'430','height'=>'350','title'=>__('Youtube Button'      ,'sz-google')),
			);
		}
	}
}