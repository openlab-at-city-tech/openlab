<?php

/**
 * Class to initialize the plugin and recall
 * of all classes that make up the main parts
 *
 * @package SZGoogle
 * @subpackage Classes
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition
// with the same name or the same as previously defined in other script

if (!class_exists('SZGooglePluginDeactivation'))
{
	class SZGooglePluginDeactivation
	{
		function action()
		{
			// Execution flush rules for rewrite custom,
			// If the plugin adds new options to rewrite

			SZGoogleCommon::rewriteFlushRules();
		}
	}
}