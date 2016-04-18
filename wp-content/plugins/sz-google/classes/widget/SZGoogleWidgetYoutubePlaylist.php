<?php

/**
 * Class for the definition of a widget that is
 * called by the class of the main module
 *
 * @package SZGoogle
 * @subpackage Widgets
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition
// with the same name or the same as previously defined in other scripte

if (!class_exists('SZGoogleWidgetYoutubePlaylist'))
{
	class SZGoogleWidgetYoutubePlaylist extends SZGoogleWidget
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('SZ-Google-Youtube-Playlist',__('SZ-Google - Youtube playlist','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-youtube sz-widget-google-youtube-playlist', 
				'description' => ucfirst(__('youtube playlist.','sz-google'))
			));
		}

		/**
		 * Generation of the HTML code of the widget
		 * for the full display in the sidebar associated
		 */

		function widget($args,$instance) 
		{
			// Checking whether there are the variables that are used during the processing
			// the script and check the default values ​​in case they were not specified

			$options = $this->common_empty(array(
				'id'              => '', // default value
				'responsive'      => '', // default value
				'width'           => '', // default value
				'height'          => '', // default value
				'margintop'       => '', // default value
				'marginright'     => '', // default value
				'marginbottom'    => '', // default value
				'marginleft'      => '', // default value
				'marginunit'      => '', // default value
				'analytics'       => '', // default value
				'delayed'         => '', // default value
				'autoplay'        => '', // default value
				'loop'            => '', // default value
				'fullscreen'      => '', // default value
				'disableiframe'   => '', // default value
				'disablekeyboard' => '', // default value
				'disablerelated'  => '', // default value
				'theme'           => '', // default value
				'cover'           => '', // default value
			),$instance);

			// Cancel the variable title that belongs to the component  
			// since there is the title of the widget and have the same name

			$options['title'] = '';

			// Create the HTML code for the current widget recalling the basic
			// function which is also invoked by the corresponding shortcode

			$OBJC = new SZGoogleActionYoutubePlaylist();
			$HTML = $OBJC->getHTMLCode($options);

			// Output HTML code linked to the widget to
			// display call to the general standard for wrap

			echo $this->common_widget($args,$instance,$HTML);
		}

		/**
		 * Changing parameters related to the widget FORM 
		 * with storing the values ​​directly in the database
		 */

		function update($new_instance,$old_instance) 
		{
			// Performing additional operations on fields of the
			// form widget before it is stored in the database

			return $this->common_update(array(
				'title'           => '0', // strip_tags
				'id'              => '1', // strip_tags
				'responsive'      => '1', // strip_tags
				'width'           => '1', // strip_tags
				'height'          => '1', // strip_tags
				'delayed'         => '1', // strip_tags
				'autoplay'        => '1', // strip_tags
				'loop'            => '1', // strip_tags
				'fullscreen'      => '1', // strip_tags
				'disableiframe'   => '1', // strip_tags
				'disablekeyboard' => '1', // strip_tags
				'disablerelated'  => '1', // strip_tags
				'theme'           => '1', // strip_tags
				'cover'           => '1', // strip_tags
			),$new_instance,$old_instance);
		}

		/**
		 * FORM display the widget in the management of 
		 * sidebar in the administration panel of wordpress
		 */

		function form($instance) 
		{
			// Creating arrays for list fields that must be
			// present in the form before calling wp_parse_args()

			$array = array(
				'title'           => '', // default value
				'id'              => '', // default value
				'responsive'      => '', // default value
				'width'           => '', // default value
				'height'          => '', // default value
				'delayed'         => '', // default value
				'autoplay'        => '', // default value
				'loop'            => '', // default value
				'fullscreen'      => '', // default value
				'disableiframe'   => '', // default value
				'disablekeyboard' => '', // default value
				'disablerelated'  => '', // default value
				'theme'           => '', // default value
				'cover'           => '', // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

			// Reading of the options for the control of default values
			// be assigned to the widget when it is placed in the sidebar

			if ($object = SZGoogleModule::getObject('SZGoogleModuleYoutube')) 
			{
				$options = (object) $object->getOptions();

				if (!in_array($theme,array('light','dark')))    $theme = $options->youtube_theme;
				if (!in_array($cover,array('local','youtube'))) $cover = $options->youtube_cover;

				if (!in_array($responsive     ,array('n','y'))) $responsive      = $options->youtube_responsive;
				if (!in_array($delayed        ,array('n','y'))) $delayed         = $options->youtube_delayed;
				if (!in_array($autoplay       ,array('n','y'))) $autoplay        = $options->youtube_autoplay;
				if (!in_array($loop           ,array('n','y'))) $loop            = $options->youtube_loop;
				if (!in_array($fullscreen     ,array('n','y'))) $fullscreen      = $options->youtube_fullscreen;
				if (!in_array($disableiframe  ,array('n','y'))) $disableiframe   = $options->youtube_disableiframe;
				if (!in_array($disablekeyboard,array('n','y'))) $disablekeyboard = $options->youtube_disablekeyboard;
				if (!in_array($disablerelated ,array('n','y'))) $disablerelated  = $options->youtube_disablerelated;

				if (!ctype_digit($width)  and $width  != 'auto') $width  = $options->youtube_width;
				if (!ctype_digit($height) and $height != 'auto') $height = $options->youtube_height;
			}

			// Setting any of the default parameters for the
			// fields that contain invalid values ​​or inconsistent

			$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_youtube.php");

			if (!in_array($theme,array('light','dark')))    $theme = 'dark';
			if (!in_array($cover,array('local','youtube'))) $cover = 'local';

			if (!in_array($responsive     ,array('0','1','n','y'))) $responsive      = $DEFAULT['youtube_responsive']['value'];
			if (!in_array($delayed        ,array('0','1','n','y'))) $delayed         = $DEFAULT['youtube_delayed']['value'];
			if (!in_array($autoplay       ,array('0','1','n','y'))) $autoplay        = $DEFAULT['youtube_autoplay']['value'];
			if (!in_array($loop           ,array('0','1','n','y'))) $loop            = $DEFAULT['youtube_loop']['value'];
			if (!in_array($fullscreen     ,array('0','1','n','y'))) $fullscreen      = $DEFAULT['youtube_fullscreen']['value'];
			if (!in_array($disableiframe  ,array('0','1','n','y'))) $disableiframe   = $DEFAULT['youtube_disableiframe']['value'];
			if (!in_array($disablekeyboard,array('0','1','n','y'))) $disablekeyboard = $DEFAULT['youtube_disablekeyboard']['value'];
			if (!in_array($disablerelated ,array('0','1','n','y'))) $disablerelated  = $DEFAULT['youtube_disablerelated']['value'];

			if (!ctype_digit($width)  or $width  == 0) { $width  = $DEFAULT['youtube_width']['value'];  $width_auto  = '1'; }
			if (!ctype_digit($height) or $height == 0) { $height = $DEFAULT['youtube_height']['value']; $height_auto = '1'; }

			// Unfortunately youtube values ​​were set differently
			// the values ​​of configuration options so we do a replace

			$responsive      = str_replace(array('0','1'),array('n','y'),$responsive);
			$delayed         = str_replace(array('0','1'),array('n','y'),$delayed);
			$autoplay        = str_replace(array('0','1'),array('n','y'),$autoplay);
			$loop            = str_replace(array('0','1'),array('n','y'),$loop);
			$fullscreen      = str_replace(array('0','1'),array('n','y'),$fullscreen);
			$disableiframe   = str_replace(array('0','1'),array('n','y'),$disableiframe);
			$disablekeyboard = str_replace(array('0','1'),array('n','y'),$disablekeyboard);
			$disablerelated  = str_replace(array('0','1'),array('n','y'),$disablerelated);

			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}