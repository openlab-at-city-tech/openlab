<?php
/*
 * timeline.js.php
 * Description: JavaScript for the SIMILE Timline Plugin
 * Plugin URI: freshlabs.de
 * Author: freshlabs
 * 
	===========================================================================
	SIMILE Timeline for WordPress
	Copyright (C) 2006 freshlabs
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
	===========================================================================
*/
define('WP_USE_THEMES', false);
// load WordPress environment
include_once('../../../../wp-load.php');
// explicit HTTP header 200
header("HTTP/1.1 200 OK");
header("Status: 200 All rosy");
// Include timeline PHP API
include_once('../inc/timeline-js.inc.php');

// Create JavaScript file header
header("Cache-Control: public");
header("Pragma: cache");

$offset = 60*60*24*10; // 10 days
header("Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime(__FILE__))." GMT");
header("Content-Type: text/javascript; charset: UTF-8");
// ===========================================================================

// Timeline orientation, vertical timelines still don't work
if(get_option('stl_timeline_orientation')){
	$orientation = get_option('stl_timeline_orientation');	
} else $orientation = 0;

// get Timeline configurations
$stl_startdate_id = get_option('stl_timeline_startdate');

// Start and end date (scroll boundaries)
$stl_timeline_start = 0;
$stl_timeline_stop = 0;

// default category and DOM-Id
$categories = '1';
$domid = "stl-mytimeline";

if(get_option('stl_timeline_usestartstop')==1){
	$stl_timeline_start = get_option('stl_timeline_start');
	$stl_timeline_stop = get_option('stl_timeline_stop');
}
else{
	$stl_timeline_start = $_GET['start'];
	$stl_timeline_stop = $_GET['stop'];
}

if(isset($_GET['cat'])){
	// only allow comma seperated category IDs
	$categories = preg_replace('/[^0-9.,]/','', $_GET['cat']);
	$categories = esc_js($categories);
}

// Get ID of timeline div
if(isset($_GET['id'])){
	$domid=preg_replace('/[^a-z0-9.\-_]/','', $_GET['id']);
	$domid = esc_js($domid);
}

?>
/* WP SIMILE Timeline JavaScript configuration script
 * Plugin version: <?php echo get_option('stl_timeline_plugin_version'); ?>
 */

/* ================ CompactPainter Info Bubble Fix (http://www.simile-widgets.org/wiki/Timeline_CustomEventClickHandler) ========= */
Timeline.CompactEventPainter.prototype._onClickMultiplePreciseInstantEvent=function(E,A,B){var F=SimileAjax.DOM.getPageCoordinates(E);
this._showBubble(F.left+Math.ceil(E.offsetWidth/2),F.top+Math.ceil(E.offsetHeight/2),B);
var D=[];
for(var C=0;
C<B.length;
C++){D.push(B[C].getID());
}this._fireOnSelect(D);
A.cancelBubble=true;
SimileAjax.DOM.cancelEvent(A);
return false;
};
Timeline.CompactEventPainter.prototype._onClickInstantEvent=function(C,A,B){var D=SimileAjax.DOM.getPageCoordinates(C);
this._showBubble(D.left+Math.ceil(C.offsetWidth/2),D.top+Math.ceil(C.offsetHeight/2),B);
this._fireOnSelect(B.getID());
A.cancelBubble=true;
SimileAjax.DOM.cancelEvent(A);
return false;
};
Timeline.CompactEventPainter.prototype._onClickDurationEvent=function(F,B,C){if("pageX" in B){var A=B.pageX;
var E=B.pageY;
}else{var D=SimileAjax.DOM.getPageCoordinates(F);
var A=B.offsetX+D.left;
var E=B.offsetY+D.top;
}this._showBubble(A,E,C);
this._fireOnSelect(C.getID());
B.cancelBubble=true;
SimileAjax.DOM.cancelEvent(B);
return false;
};
Timeline.CompactEventPainter.prototype.showBubble=function(A){var B=this._eventIdToElmt[A.getID()];
if(B){var C=SimileAjax.DOM.getPageCoordinates(B);
this._showBubble(C.left+B.offsetWidth/2,C.top+B.offsetHeight/2,A);
}};
/* ============================================================================ */
var tl;
function loadSimileTimeline() {
	
	if(document.getElementById("<?php echo $domid; ?>")){	
		
		/* 
	 	* Switch off using the history
	 	* See issue http://code.google.com/p/simile-widgets/issues/detail?id=61
	 	*/
		SimileAjax.History.enabled = false;
	
		// ### create custom theme
<?php stl_api_createTheme(''.
				'label_width=200&'.
				'bubble_width=320&'.
				'bubble_height=300&'.
				'timeline_start='.$stl_timeline_start.'&'.
				'timeline_stop='.$stl_timeline_stop.''.
''); ?>

var eventSource = new Timeline.DefaultEventSource();
<?php
$datafile = STL_TIMELINE_DATA_FOLDER.'/timeline.xml.php';
// stl_api_createEventSourceArray($datafile, $categories);
// load data / event source
stl_api_createEventSource($datafile, $categories, 'eventSource');
?>

<?php
$stl_startdate = null;

// Evaluate the timeline start date (Where to focus on load)
switch($stl_startdate_id){
	case 3:		// In the middle of all events
		$a1= WPSimileTimelineToolbox::myGetDate(WPSimileTimelinePost::getMinMaxEventDate('MAX','end', null, $categories));
		$b1= WPSimileTimelineToolbox::myGetDate(WPSimileTimelinePost::getMinMaxEventDate('MIN','start', null, $categories));
		// calculate arithmetic middle date
		$amd = adodb_mktime(
			($a1['hour'] + $b1['hour'])/2,
			($a1['minute'] + $b1['minute'])/2,
			($a1['second'] + $b1['second'])/2,
			($a1['month'] + $b1['month'])/2,
			($a1['day'] + $b1['day'])/2,
			($a1['year'] + $b1['year'])/2
		);
		$stl_startdate = adodb_date('r', $amd);
		break;
	case 2:		// at end date of last post
		$stl_startdate = WPSimileTimelinePost::getMinMaxEventDate('MAX','end', 'r', $categories);
		break;
	case 1:		// at start date of first post
		$stl_startdate = WPSimileTimelinePost::getMinMaxEventDate('MIN','start', 'r', $categories);
		break;
	case 0:  // at current date
		$stl_startdate = date('r');
		break;
	default: // in case a custom date is set (TODO: implement)
		$stl_startdate = adodb_date('r', strtotime($stl_startdate_id));
}

// request all Timeline Band objects
$stl_timeline_band = new WPSimileTimelineBand();
$stl_bands = $stl_timeline_band->find_all();
$useCompactPainter = get_option('stl_timeline_useattachments') == 1 ? true : false;
// Build timeline band infos
$i=0;
echo 'var bandInfos = [' . "\n";
foreach($stl_bands as $band){
	$hotzones = '';
	
	foreach($band->hotzones as $hotzone){
		$hotzones .= stl_api_createZone($stl_timeline_resolutions[$hotzone->unit], adodb_date2('r',$hotzone->start_date), adodb_date2('r',$hotzone->end_date), $hotzone->magnify, $hotzone->multiple);
		if(is_array($hotzones) && $i<sizeof($hotzones)-1) $hotzones .= ',';
	}
	
	stl_api_createBandInfo(
				$hotzones,
				$band->height,
				$stl_timeline_resolutions[$band->unit],
				$band->interval_size,
				'mytheme',
				($band->show_labels == 1 ? 'true' : 'false'),
				"eventSource",
				$useCompactPainter,
				$stl_startdate,
				"0",
				'0.5',
				'0.3',
				'de'
	);
	if($i<sizeof($stl_bands)-1) echo ',';
	$i++;
}
echo '];';
?>


// Sync all timeline bands (has to be done manually)
for(var i=1;i < bandInfos.length;i++){
	bandInfos[i].syncWith = 0;   // synchronize with primary band
	bandInfos[i].highlight = true;   // highlight focused area
}

<?php
// Build Highlight decorators
$b=0; // band index
$stl_timeline_decorator = new WPSimileTimelineDecorator();
foreach($stl_bands as $band){
	$k=0; // decorator index
	$decorators = '';
	if(!empty($band->decorators)){
		$s = sizeof($band->decorators)-1;
		echo 'bandInfos['.$b.'].decorators = [';
		$decorator_types = $stl_timeline_decorator->get_types();
		foreach($band->decorators as $decorator){
			echo stl_api_createHighlightDecorator(
				$decorator_types[$decorator->type],
				adodb_date2('r',$decorator->start_date),
				adodb_date2('r',$decorator->end_date),
				$decorator->start_label,
				$decorator->end_label,
				$decorator->color,
				$decorator->css_class,
				$decorator->opacity
			);
			if($k<$s) echo ',';
			$k++;
		}
		$b++;
		echo '];' . "\n";
	}
}

	// create actual timeline object
	stl_api_createTimeLine($domid, 'bandInfos', $orientation);
	
	// OVERRIDE SIMILE API: change click event for timeline entries
	if(get_option('stl_timeline_linkhandling')){
		if($useCompactPainter){
			echo 'Timeline.CompactEventPainter.prototype._showBubble = function(x,y,evts){';
		}
		else{
			echo 'Timeline.OriginalEventPainter.prototype._showBubble = function(x,y,evts){';
		}
		echo 'document.location.href=evts.getLink();';
		echo '}';
	}
?>
	
<?php // OVERRIDE SIMILE API: Do not show event date in bubble
	if(get_option('stl_timeline_showbubbledate') == 0){
		echo 'Timeline.DefaultEventSource.Event.prototype.fillTime = function(){};';
	}
	else{ // OVERRIDE SIMILE API: Format date and time for popup bubbles 
?>
Timeline.GregorianDateLabeller.prototype.labelPrecise = function(date) {
	date = SimileAjax.DateTime.removeTimeZoneOffset(
		date,
		this._timeZone //+ (new Date().getTimezoneOffset() / 60)
	).toUTCString();
	
	var dateFormat = '<?php echo get_option('date_format'); ?>';
	var timeFormat = '<?php echo get_option('time_format'); ?>';
    
	date = new Date(date);
	date = date.format(dateFormat + ' ' + timeFormat);
	return date;
};
	<?php
	}
	?>

  }else{ /* empty - do nothing when no timeline-frame is found */ }
}


/*
 * Static functions and onload handler for the actual init process
 */
var resizeTimerID = null;
function resizeSimileTimeline() {
    if (resizeTimerID == null) {
        resizeTimerID = window.setTimeout(function() {
            resizeTimerID = null;
            tl.layout();
        }, 500);
    }
}

/* addEvent function - by Scott Andrew 
 * http://www.scottandrew.com/weblog/articles/cbs-events
 */
function addEvent(obj, evType, fn){ 
	if (obj.addEventListener){ 
		obj.addEventListener(evType, fn, false); 
		return true; 
	} else if (obj.attachEvent){ 
		var r = obj.attachEvent("on"+evType, fn); 
		return r; 
	} else { 
		return false; 
	} 
}

// load Timeline on window load
addEvent(window, "load", loadSimileTimeline);
addEvent(window, "resize", resizeSimileTimeline);

// Simulates PHPs date function (http://jacwright.com/projects/javascript/date_format)
Date.prototype.format = function(format) {
	var returnStr = '';
	var replace = Date.replaceChars;
	for (var i = 0; i < format.length; i++) {
		var curChar = format.charAt(i);
		if (replace[curChar]) {
			returnStr += replace[curChar].call(this);
		} else {
			returnStr += curChar;
		}
	}
	return returnStr;
};
Date.replaceChars = {
	shortMonths: Timeline.GregorianDateLabeller.monthNames[Timeline.getDefaultLocale()],
	longMonths: Timeline.GregorianDateLabeller.monthNames[Timeline.getDefaultLocale()],
	shortDays: Timeline.GregorianDateLabeller.dayNames[Timeline.getDefaultLocale()],
	longDays: Timeline.GregorianDateLabeller.dayNames[Timeline.getDefaultLocale()],
	
	// Day
	d: function() { return (this.getDate() < 10 ? '0' : '') + this.getDate(); },
	D: function() { return Date.replaceChars.shortDays[this.getDay()]; },
	j: function() { return this.getDate(); },
	l: function() { return Date.replaceChars.longDays[this.getDay()]; },
	N: function() { return this.getDay() + 1; },
	S: function() { return (this.getDate() % 10 == 1 && this.getDate() != 11 ? 'st' : (this.getDate() % 10 == 2 && this.getDate() != 12 ? 'nd' : (this.getDate() % 10 == 3 && this.getDate() != 13 ? 'rd' : 'th'))); },
	w: function() { return this.getDay(); },
	z: function() { return "Not Yet Supported"; },
	// Week
	W: function() { return "Not Yet Supported"; },
	// Month
	F: function() { return Date.replaceChars.longMonths[this.getMonth()]; },
	m: function() { return (this.getMonth() < 9 ? '0' : '') + (this.getMonth() + 1); },
	M: function() { return Date.replaceChars.shortMonths[this.getMonth()]; },
	n: function() { return this.getMonth() + 1; },
	t: function() { return "Not Yet Supported"; },
	// Year
	L: function() { return (((this.getFullYear()%4==0)&&(this.getFullYear()%100 != 0)) || (this.getFullYear()%400==0)) ? '1' : '0'; },
	o: function() { return "Not Supported"; },
	Y: function() { return this.getFullYear(); },
	y: function() { return ('' + this.getFullYear()).substr(2); },
	// Time
	a: function() { return this.getHours() < 12 ? 'am' : 'pm'; },
	A: function() { return this.getHours() < 12 ? 'AM' : 'PM'; },
	B: function() { return "Not Yet Supported"; },
	g: function() { return this.getHours() % 12 || 12; },
	G: function() { return this.getHours(); },
	h: function() { return ((this.getHours() % 12 || 12) < 10 ? '0' : '') + (this.getHours() % 12 || 12); },
	H: function() { return (this.getHours() < 10 ? '0' : '') + this.getHours(); },
	i: function() { return (this.getMinutes() < 10 ? '0' : '') + this.getMinutes(); },
	s: function() { return (this.getSeconds() < 10 ? '0' : '') + this.getSeconds(); },
	// Timezone
	e: function() { return "Not Yet Supported"; },
	I: function() { return "Not Supported"; },
	O: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + '00'; },
	P: function() { return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + ':' + (Math.abs(this.getTimezoneOffset() % 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() % 60)); },
	T: function() { var m = this.getMonth(); this.setMonth(0); var result = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, '$1'); this.setMonth(m); return result;},
	Z: function() { return -this.getTimezoneOffset() * 60; },
	// Full Date/Time
	c: function() { return this.format("Y-m-d") + "T" + this.format("H:i:sP"); },
	r: function() { return this.toString(); },
	U: function() { return this.getTime() / 1000; }
};