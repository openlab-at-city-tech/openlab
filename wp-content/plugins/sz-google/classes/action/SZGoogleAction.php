<?php

/**
 * Define a class that identifies an action called by the
 * main module based on the options that have been activated
 *
 * @package SZGoogle
 * @subpackage Actions
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleAction'))
{
	class SZGoogleAction 
	{
		/**
		 * Definition private variables to store common information
		 * to insert into HTML document sections (header or footer)
		 */

		static private $getJavascriptsHead    = '';
		static private $getJavascriptsFoot    = '';
		static private $getCSSCodeInlineHead  = '';
		static private $getCSSCodeInlineFoot  = '';

		static private $setJavascriptsHead    = false;
		static private $setJavascriptsFoot    = false;
		static private $setCSSCodeInlineHead  = false;
		static private $setCSSCodeInlineFoot  = false;

		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct()
		{
			$this->setCodeCSSInlineHead(); // CSS Inline
			$this->setCodeCSSInlineFoot(); // CSS Inline
		}

		/**
		 * Function to retrieve the configuration options that are
		 * connected to the module shown in the parameter passed
		 */

		function getModuleOptions($name) {
			if (!$object = SZGoogleModule::getObject($name)) $object = new $name;
			return $object->getOptions();
		}

		/**
		 * Find the reference object module called to
		 * facilitate the recall of the methods by this action
		 */

		function getModuleObject($name) {
			if (!$object = SZGoogleModule::getObject($name)) $object = new $name;
			return $object;
		}

		/**
		 * Add to the action of the issuance of the footer CSS
		 * code created by the various modules of the plugin
		 */

		function setCodeCSSInlineHead()
		{
			if (self::$setCSSCodeInlineHead) return;
				else self::$setCSSCodeInlineHead = true;

			add_action('SZ_HEAD_HEAD',array($this,'getCodeCSSInlineHead'),99);
		}

		/**
		 * Add to the action of the issuance of the header CSS
		 * code created by the various modules of the plugin
		 */

		function setCodeCSSInlineFoot()
		{
			if (self::$setCSSCodeInlineFoot) return;
				else self::$setCSSCodeInlineFoot = true;

			add_action('SZ_FOOT_BODY',array($this,'getCodeCSSInlineFoot'));
		}

		/**
		 * Composition CSS code e storage in static variables with
		 * function that can be called from any form of plugin
		 */

		function addCodeCSSInlineHead($css) {
			self::$getCSSCodeInlineHead .= $css;
		}

		function addCodeCSSInlineFoot($css) {
			self::$getCSSCodeInlineFoot .= $css;
		}

		/**
		 * Get and output CSS code present in static variables with
		 * function that can be called from any form of plugin
		 */

		function getCodeCSSInlineHead() {
			if (self::$getCSSCodeInlineHead != '') {
				echo '<style>'.self::$getCSSCodeInlineHead.'</style>'."\n";
			}
		}

		/**
		 * Get and output CSS code present in static variables with
		 * function that can be called from any form of plugin
		 */

		function getCodeCSSInlineFoot() {
			if (self::$getCSSCodeInlineFoot != '') {
				echo '<style>'.self::$getCSSCodeInlineFoot.'</style>'."\n";
			}
		}
	}
}