<?php

/**
 * Module to the definition of the functions that relate to both the
 * widgets that shortcode, but also filters and actions that the module
 * can integrating with adding functionality into wordpress.
 *
 * @package SZGoogle
 * @subpackage Modules
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleModuleAjax'))
{
	class SZGoogleModuleAjax extends SZGoogleModule
	{
		/**
		 * Definition of the initial variable array which are
		 * used to identify the module and options related to it
		 */

		function moduleAddSetup()
		{
			$this->moduleSetClassName(__CLASS__);
			add_action('wp_ajax_sz_google_shortcodes',array($this,'moduleAddAjaxShortcodes'));
		}
		
		/**
		 * Function for the Ajax call regarding the
		 * calling of template matching of shortcodes
		 */

		function moduleAddAjaxShortcodes() 
		{
			// Check if the call to this function contains the
			// parameters expected in form with the POST method

			if (!isset($_GET['action']))    return null;
			if (!isset($_GET['shortcode'])) return null;
			if (!isset($_GET['title']))     return null;

			// Checking existence specified shortcode and
			// loading template that covers the shortcode

			$shortcode  = $_GET['shortcode'];
			$shortcodes = $this->moduleGetAjaxShortcodes();

			if (isset($shortcodes[$shortcode])) {
				define('SZGOOGLE_AJAX_NAME',$shortcodes[$shortcode]);
				$filename = dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/'.$shortcodes[$shortcode].'.php';
				if (is_file($filename)) @include($filename);
			}

			// The AJAX call must be properly closed with the 
			// command exit() or die() and the process should continue

			die();
		}

		// Definition array to hold the shortcodes strings  
		// to use the plugin defined in the js file attached

		function moduleGetAjaxShortcodes() 
		{
			return array(
				'sz-gplus-author'    => 'SZGooglePlusAuthorBadge',
				'sz-gplus-comments'  => 'SZGooglePlusComments',
				'sz-gplus-community' => 'SZGooglePlusCommunity',
				'sz-gplus-follow'    => 'SZGooglePlusFollow',
				'sz-gplus-followers' => 'SZGooglePlusFollowers',
				'sz-gplus-page'      => 'SZGooglePlusPage',
				'sz-gplus-post'      => 'SZGooglePlusPost',
				'sz-gplus-profile'   => 'SZGooglePlusProfile',
				'sz-gplus-one'       => 'SZGooglePlusPlusone',
				'sz-gplus-share'     => 'SZGooglePlusShare',
				'sz-calendar'        => 'SZGoogleCalendar',
				'sz-drive-embed'     => 'SZGoogleDriveEmbed',
				'sz-drive-save'      => 'SZGoogleDriveSaveButton',
				'sz-drive-viewer'    => 'SZGoogleDriveViewer',
				'sz-ggroups'         => 'SZGoogleGroups',
				'sz-hangouts-start'  => 'SZGoogleHangoutsStart',
				'sz-maps'            => 'SZGoogleMaps',
				'sz-panoramio'       => 'SZGooglePanoramio',
				'sz-ytbadge'         => 'SZGoogleYoutubeBadge',
				'sz-ytbutton'        => 'SZGoogleYoutubeButton',
				'sz-ytlink'          => 'SZGoogleYoutubeLink',
				'sz-ytplaylist'      => 'SZGoogleYoutubePlaylist',
				'sz-ytvideo'         => 'SZGoogleYoutubeVideo',
			);
		}
	}
}