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

if (!class_exists('SZGoogleModuleDrive'))
{
	class SZGoogleModuleDrive extends SZGoogleModule
	{
		private $setJavascriptPlusone = false;

		/**
		 * Definition of the initial variable array which are
		 * used to identify the module and options related to it
		 */

		function moduleAddSetup()
		{
			$this->moduleSetClassName(__CLASS__);
			$this->moduleSetOptionSet('sz_google_options_drive');
			
			// Definition shortcode connected to the module with an array where you
			// have to specify the name activation option with the shortcode and function

			$this->moduleSetShortcodes(array(
				'drive_embed_shortcode'      => array('sz-drive-embed' ,array(new SZGoogleActionDriveEmbed() ,'getShortcode')),
				'drive_viewer_shortcode'     => array('sz-drive-viewer',array(new SZGoogleActionDriveViewer(),'getShortcode')),
				'drive_savebutton_shortcode' => array('sz-drive-save'  ,array(new SZGoogleActionDriveSave()  ,'getShortcode')),
			));

			// Definition widgets connected to the module with an array where you
			// have to specify the name option of activating and class to be loaded

			$this->moduleSetWidgets(array(
				'drive_embed_widget'         => 'SZGoogleWidgetDriveEmbed',
				'drive_viewer_widget'        => 'SZGoogleWidgetDriveViewer',
				'drive_savebutton_widget'    => 'SZGoogleWidgetDriveSaveButton',
			));
		}

		/**
		 * Add the Javascript code in the various components
		 * of google plus footer and if control was performed
		 */

		function addCodeJavascriptFooter()
		{
			// If you've already entered the Javascript code in the footer section
			// I leave the function otherwise set the variable and constant

			if ($this->setJavascriptPlusone) return;
				else $this->setJavascriptPlusone = true;

			// Loading action in the footer of the plugin to load
			// the javascript framework made available by google

			add_action('SZ_FOOT_BODY',array($this,'setJavascriptPlusOne'));
		}
	}

	/**
	 * Loading function for PHP allows developers to implement modules in this plugin.
	 * The functions have the same parameters of shortcodes, see the documentation.
	 */

	@require_once(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/functions/SZGoogleFunctionsDrive.php');
}