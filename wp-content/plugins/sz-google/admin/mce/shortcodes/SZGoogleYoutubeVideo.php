<?php

/**
 * Script to implement the HTML code shared with widgets 
 * in the function pop-up insert shortcodes via GUI
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Creating array to list the fields that must be 
// present in the form before calling wp_parse_args ()

$array = array(
	'title'           => '', // valore predefinito
	'url'             => '', // valore predefinito
	'responsive'      => '', // valore predefinito
	'width'           => '', // valore predefinito
	'height'          => '', // valore predefinito
	'delayed'         => '', // valore predefinito
	'autoplay'        => '', // valore predefinito
	'loop'            => '', // valore predefinito
	'fullscreen'      => '', // valore predefinito
	'schemaorg'       => '', // valore predefinito
	'disableiframe'   => '', // valore predefinito
	'disablekeyboard' => '', // valore predefinito
	'disablerelated'  => '', // valore predefinito
	'start'           => '', // valore predefinito
	'end'             => '', // valore predefinito
	'theme'           => '', // valore predefinito
	'cover'           => '', // valore predefinito
);

// Creating arrays to list of fields to be retrieved FORM 
// and loading the file with the HTML template to display

extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

// Read the options to control the default values ​​to be 
// assigned to the widget when it is added to the sidebar

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
	if (!in_array($schemaorg      ,array('n','y'))) $schemaorg       = $options->youtube_schemaorg;
	if (!in_array($disableiframe  ,array('n','y'))) $disableiframe   = $options->youtube_disableiframe;
	if (!in_array($disablekeyboard,array('n','y'))) $disablekeyboard = $options->youtube_disablekeyboard;
	if (!in_array($disablerelated ,array('n','y'))) $disablerelated  = $options->youtube_disablerelated;

	if (!ctype_digit($width)  and $width  != 'auto') $width  = $options->youtube_width;
	if (!ctype_digit($height) and $height != 'auto') $height = $options->youtube_height;
}

// Setting any of the default parameters for fields 
// that contain invalid values ​​or inconsistent

$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_youtube.php");

if (!in_array($theme,array('light','dark')))    $theme = 'dark';
if (!in_array($cover,array('local','youtube'))) $cover = 'local';

if (!in_array($responsive     ,array('0','1','n','y'))) $responsive      = $DEFAULT['youtube_responsive']['value'];
if (!in_array($delayed        ,array('0','1','n','y'))) $delayed         = $DEFAULT['youtube_delayed']['value'];
if (!in_array($autoplay       ,array('0','1','n','y'))) $autoplay        = $DEFAULT['youtube_autoplay']['value'];
if (!in_array($loop           ,array('0','1','n','y'))) $loop            = $DEFAULT['youtube_loop']['value'];
if (!in_array($fullscreen     ,array('0','1','n','y'))) $fullscreen      = $DEFAULT['youtube_fullscreen']['value'];
if (!in_array($schemaorg      ,array('0','1','n','y'))) $schemaorg       = $DEFAULT['youtube_schemaorg']['value'];
if (!in_array($disableiframe  ,array('0','1','n','y'))) $disableiframe   = $DEFAULT['youtube_disableiframe']['value'];
if (!in_array($disablekeyboard,array('0','1','n','y'))) $disablekeyboard = $DEFAULT['youtube_disablekeyboard']['value'];
if (!in_array($disablerelated ,array('0','1','n','y'))) $disablerelated  = $DEFAULT['youtube_disablerelated']['value'];

if (!ctype_digit($width)  or $width  == 0) { $width  = $DEFAULT['youtube_width']['value'];  $width_auto  = '1'; }
if (!ctype_digit($height) or $height == 0) { $height = $DEFAULT['youtube_height']['value']; $height_auto = '1'; }

// Unfortunately, the values ​​of youtube are set differently 
// from the values ​​of configuration options, so let's replace

$responsive      = str_replace(array('0','1'),array('n','y'),$responsive);
$delayed         = str_replace(array('0','1'),array('n','y'),$delayed);
$autoplay        = str_replace(array('0','1'),array('n','y'),$autoplay);
$loop            = str_replace(array('0','1'),array('n','y'),$loop);
$fullscreen      = str_replace(array('0','1'),array('n','y'),$fullscreen);
$schemaorg       = str_replace(array('0','1'),array('n','y'),$schemaorg);
$disableiframe   = str_replace(array('0','1'),array('n','y'),$disableiframe);
$disablekeyboard = str_replace(array('0','1'),array('n','y'),$disablekeyboard);
$disablerelated  = str_replace(array('0','1'),array('n','y'),$disablerelated);

// Loading ADMIN template for composition using
// shortcodes in many cases the same code Widget

@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseHeader.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidgetYoutubeVideo.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseFooter.php');