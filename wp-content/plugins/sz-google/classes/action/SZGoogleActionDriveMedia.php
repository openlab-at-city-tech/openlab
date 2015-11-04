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

if (!class_exists('SZGoogleActionDriveMedia'))
{
	class SZGoogleActionDriveMedia extends SZGoogleAction
	{
		/**
		 * Definizione della funzione che viene normalmente richiamata
		 * dagli hook presenti in add_action e add_filter di wordpress
		 */
		function action() {

			add_filter('media_upload_tabs',    array($this,'addMediaUploadTabName'));
			add_action('media_upload_flickr_uploads',array($this,'addMediaUploadTabContent'));
//add_filter( 'media_view_strings', array($this,'custom_media_uploader' ));

		}

		/**
		 * Creazione codice HTML per il componente richiamato che
		 * deve essere usato in comune sia per widget che shortcode
		 *
		 * @return string
		 */
		function addMediaUploadTabName($tabs)
		{
 			$tabs['flickr_uploads'] = "Flickr Uploads";
    		return $tabs;
		}

		function addMediaUploadTabContent()
		{
			$errors= false;
			return wp_iframe(array($this,'addMediaUploadTabContentWP'), 'media', $errors );
		}

		function addMediaUploadTabContentWP() {			
			echo "Ciao MAssimo";
		}
	}
}