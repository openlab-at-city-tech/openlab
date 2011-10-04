<?php

/**
 * Embedded Google Calendar customization wrapper script created by:
 * author      Chris Dornfeld <dornfeld (at) unitz.com>
 * version     $Id: gcalendar-wrapper.php 1571 2010-11-15 07:08:05Z dornfeld $
 *
 * Extended and adapted for the Stout Google Calendar WordPress plugin by Matt McKenny <sgc (at) stoutdesign.com>
 * Applies a custom color scheme to an embedded Google Calendar.
 * updated 2011-02-23 - Stout Google Calendar v1.2.2
 * @author Matt McKenny <sgc (at) stoutdesign.com>
 */

define('GOOGLE_CALENDAR_BASE', 'https://www.google.com/');
define('GOOGLE_CALENDAR_EMBED_URL', GOOGLE_CALENDAR_BASE . 'calendar/embed');

/**
 * Construct calendar URL
 */

$calQuery = '';
if (isset($_SERVER['QUERY_STRING'])) {
	$calQuery = $_SERVER['QUERY_STRING'];
} else if (isset($HTTP_SERVER_VARS['QUERY_STRING'])) {
	$calQuery = $HTTP_SERVER_VARS['QUERY_STRING'];
}
$calUrl = GOOGLE_CALENDAR_EMBED_URL.'?'.$calQuery;

/**
 * Retrieve calendar embedding code using WP_Http class in WordPress
 * Thanks to http://planetozh.com/blog/2009/08/how-to-make-http-requests-with-wordpress
 */
include_once( '../../../wp-load.php' );
include_once( ABSPATH . WPINC. '/class-http.php' );
$request = new WP_Http;
$result = $request->request($calUrl); 
$calRaw = array();

//Handle errors from WP_Http
if (isset($result->errors)) {
	// display error message of some sort
	$err_msg = array_keys($result->errors);
	$errors = $err_msg[0]."<br/>";
	foreach($result->errors as $error){
		$errors .= $error[0]."<br/>";
	}
	die('The following error(s) occurred: '.$errors);
} else {
	$calRaw = $result['body'];
}


/**
 * Set your color scheme below
 */

preg_match('/sgc0=(\w+)/',$calQuery,$color0);
preg_match('/sgc1=(\w+)/',$calQuery,$color1);
preg_match('/sgc2=(\w+)/',$calQuery,$color2);
preg_match('/sgc3=(\w+)/',$calQuery,$color3);
preg_match('/sgc4=(\w+)/',$calQuery,$color4);
preg_match('/sgc5=(\w+)/',$calQuery,$color5);
preg_match('/sgc6=(\w+)/',$calQuery,$color6);
preg_match('/sgcBkgrdTrans=(\d)/',$calQuery,$bkgrdTrans);
preg_match('/sgcImage=(\d+)/',$calQuery,$sgcImage);
preg_match('/bubbleWidth=(\d+)/',$calQuery,$bubbleWidth);
preg_match('/bubbleUnit=([a-z]*)/',$calQuery,$bubbleUnit);

$calBkgrd 						= ($bkgrdTrans[1] == 0)  ? "#".$color0[1] : 'transparent';
$calColorBgDark 			= ($color1[1] != '') ? "#".$color1[1] : '#c3d9ff';
$calColorTextOnDark 	= ($color2[1] != '') ? "#".$color2[1] : '#000000';
$calColorBgLight 			= ($color3[1] != '') ? "#".$color3[1] : '#e8eef7';
$calColorTextOnLight 	=	($color4[1] != '') ? "#".$color4[1] : '#000000';
$calColorBgToday 			=	($color5[1] != '') ? "#".$color5[1] : '#ffffcc';
$calBkgrdText 				=	($color6[1] != '') ? "#".$color6[1] : '#000000';

if ($bubbleWidth[1] != '') {
	$bubbleOutput =	($bubbleUnit[1] == 'percentage') ? $bubbleWidth[1]."%" :$bubbleWidth[1]."px";
	$bubbleCss = ("div.bubble { width: $bubbleOutput !important;} ");
}else {
	$bubbleCss = '';
}

switch ($sgcImage[1]) {
	case 0 :
		$sgcImage = 'https://calendar.google.com/googlecalendar/images/combined_v18.png';
		break;
	case 1 :
		//gray
		$sgcImage = 'https://lh6.googleusercontent.com/_TKDu_kHO3SM/TWVbgXNbUKI/AAAAAAAAABI/qvChd-AIxh8/sgc_gray_combined_v18.png';
		break;
	case 2 :
		//50% black
		$sgcImage = 'https://lh5.googleusercontent.com/_TKDu_kHO3SM/TWVbgRyKW0I/AAAAAAAAABE/5DSz9dwLiG8/sgc_50black_combined_v18.png';
		break;
	case 3 :
		//50% white
		$sgcImage = 'https://lh4.googleusercontent.com/_TKDu_kHO3SM/TWVbgTKZZHI/AAAAAAAAAA8/6nYyRbAU0yI/sgc_50white_combined_v18.png';
		break;
}


/**
 * Prepare stylesheet customizations
 */

$calCustomStyle =<<<EOT

body {
	background-color: {$calBkgrd}  !important;
}
.navBack, .navForward {
	background-image: url({$sgcImage}) !important;
}
#currentDate1, .tab-name {
	color: {$calBkgrdText} !important;
}
#calendarTitle {
	display:none;
}
/* misc interface */
.cc-titlebar {
	background-color: {$calColorBgLight} !important;
}
.date-picker-arrow-on,
.drag-lasso,
.agenda-scrollboxBoundary {
	background-color: {$calColorBgDark} !important;
}
td#timezone {
	color: {$calColorTextOnDark} !important;
}

/* popup bubble display */
{$bubbleCss}

/* tabs */
td#calendarTabs1 div.ui-rtsr-selected,
div.view-cap,
div.view-container-border {
	background-color: {$calColorBgDark} !important;
}
td#calendarTabs1 div.ui-rtsr-selected {
	color: {$calColorTextOnDark} !important;
}
td#calendarTabs1 div.ui-rtsr-unselected {
	background-color: {$calColorBgLight} !important;
	color: {$calColorTextOnLight} !important;
}

/* week view */
table.wk-weektop,
th.wk-dummyth {
	/* days of the week */
	background-color: {$calColorBgDark} !important;
}
div.wk-dayname {
	color: {$calColorTextOnDark} !important;
}
div.wk-today {
	background-color: {$calColorBgLight} !important;
	border: 1px solid #EEEEEE !important;
	color: {$calColorTextOnLight} !important;
}
td.wk-allday {
	background-color: #EEEEEE !important;
}
td.tg-times {
	background-color: {$calColorBgLight} !important;
	color: {$calColorTextOnLight} !important;
}
div.tg-today {
	background-color: {$calColorBgToday} !important;
}
td.tg-times-pri, td.tg-times-sec {
	background-color: {$calColorBgLight} !important;
	color: {$calColorTextOnLight}  !important;
}

/* month view */
table.mv-daynames-table {
	background-color: {$calColorBgDark} !important;
	/* days of the week */
	color: {$calColorTextOnDark} !important;
}
td.st-bg,
td.st-dtitle {
	/* cell borders */
	border-left: 1px solid {$calColorBgDark} !important;
}
td.st-dtitle {
	/* days of the month */
	background-color: {$calColorBgLight} !important;
	color: {$calColorTextOnLight} !important;
	/* cell borders */
	border-top: 1px solid {$calColorBgDark} !important;
}
td.st-bg-today {
	background-color: {$calColorBgToday} !important;
	border-right: {$calColorBgToday} !important;
}
td.st-dtitle-today {
	border:none;
}

/* agenda view */
div.scrollbox {
	border-top: 1px solid {$calColorBgDark} !important;
	border-left: 1px solid {$calColorBgDark} !important;
}
div.underflow-top {
	border-bottom: 1px solid {$calColorBgDark} !important;
}
div.date-label {
	background-color: {$calColorBgLight} !important;
	color: {$calColorTextOnLight} !important;
}
div.event {
	border-top: 1px solid {$calColorTextOnLight} !important;
}
div.day {
	border-bottom: 1px solid {$calColorTextOnLight} !important;
}
.mv-event-container {
	border-top:1px solid {$calColorBgDark} !important;
	border-bottom:1px solid {$calColorBgDark} !important;
}
.agenda .event-links a:link {
	color: {$calColorBgDark} !important;
}

/* Popup calendar * /
td.dp-cell, td.dp-weekday-selected, td.dp-onmonth-selected {
	background-color: {$calColorTextOnDark} !important;
}
#dpPopup1 #dpPopup1_header {
	background-color: {$calColorTextOnDark} !important;
}

EOT;

$calCustomStyle = '<style type="text/css">'.$calCustomStyle.'</style>';

/**
 * Insert BASE tag to accommodate relative paths
 */

$titleTag = '<title>';
$baseTag = '<base href="'.GOOGLE_CALENDAR_EMBED_URL.'">';
$calCustomized = preg_replace("/".preg_quote($titleTag,'/')."/i", $baseTag.$titleTag, $calRaw);

/**
 * Insert custom styles
 */

$headEndTag = '</head>';
$calCustomized = preg_replace("/".preg_quote($headEndTag,'/')."/i", $calCustomStyle.$headEndTag, $calCustomized);

/**
 * Extract and modify calendar setup data
 */

$calSettingsPattern = "(\{\s*window\._init\(\s*)(\{.+\})(\s*\)\;\s*\})";

if (preg_match("/$calSettingsPattern/", $calCustomized, $matches)) {
	$calSettingsJson = $matches[2];

	$pearJson = null;
	if (!function_exists('json_encode')) {
		// no built-in JSON support, attempt to use PEAR::Services_JSON library
		if (!class_exists('Services_JSON')) {
			require_once('JSON.php');
		}
		$pearJson = new Services_JSON();
	}

	if (function_exists('json_decode')) {
		$calSettings = json_decode($calSettingsJson);
	} else {
		$calSettings = $pearJson->decode($calSettingsJson);
	}

	// set base URL to accommodate relative paths
	$calSettings->baseUrl = GOOGLE_CALENDAR_BASE;

	// splice in updated calendar setup data
	if (function_exists('json_encode')) {
		$calSettingsJson = json_encode($calSettings);
	} else {
		$calSettingsJson = $pearJson->encode($calSettings);
	}
	// prevent unwanted variable substitutions within JSON data
	// preg_quote() results in excessive escaping
	$calSettingsJson = str_replace('$', '\\$', $calSettingsJson);
	$calCustomized = preg_replace("/$calSettingsPattern/", "\\1$calSettingsJson\\3", $calCustomized);
}

/**
 * Show output
 */

header('Content-type: text/html');
print $calCustomized;

?>
