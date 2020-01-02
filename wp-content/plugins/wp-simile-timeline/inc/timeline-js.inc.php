<?php
/*
 * timeline-js.inc.php
 * Description: Timeline functions used to generate JavaScript
 * Plugin URI: freshlabs.de
 * Author: Pete Myers, freshlabs
 * 
	===========================================================================
	SIMILE Timeline for WordPress
	Copyright (C) 2006-2019 freshlabs
	
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

$stl_timeline_resolutions = array(
		0 => 'Timeline.DateTime.MILLISECOND',
		1 => 'Timeline.DateTime.SECOND',
		2 => 'Timeline.DateTime.MINUTE',
		3 => 'Timeline.DateTime.HOUR',
		4 => 'Timeline.DateTime.DAY',
		5 => 'Timeline.DateTime.WEEK',
		6 => 'Timeline.DateTime.MONTH',
		7 => 'Timeline.DateTime.YEAR',
		8 => 'Timeline.DateTime.DECADE',
		9 => 'Timeline.DateTime.CENTURY',
		10 => 'Timeline.DateTime.MILLENNIUM'

	);

/***********************************************************************
 * API FUNCTIONS
 ***********************************************************************/

/* ---------------------------------------------------------------------------------
 * stl_api_createTimeLine()
 * Create the actual Timeline object
 * ---------------------------------------------------------------------------------*/
function stl_api_createTimeLine($id, $bandInfos, $orientation) {
	echo 'tl = Timeline.create(document.getElementById("'.$id.'"), '.$bandInfos.', '.$orientation.');' . "\n";
}

/* ---------------------------------------------------------------------------------
 * stl_api_createEventSource()
 * Create and execute the Timeline request for data sources
 * ---------------------------------------------------------------------------------*/
function stl_api_createEventSource($datafile, $categories, $name) {
	echo 'Timeline.loadXML("'.$datafile.'?terms='.$categories.'",'."\n";
	echo '		function(xml, url) {'."\n";
	echo '			'.$name.'.loadXML(xml, url);'."\n";
	echo '});';
	echo "\n";
}

/* ---------------------------------------------------------------------------------
 * stl_api_createEventSourceArray()
 * Create a JavaScript array of Event sources to load from multiple sources
 * ---------------------------------------------------------------------------------*/
function stl_api_createEventSourceArray($datafile, $categories) {
	$numBands = count($categories);
	echo "var eventSources = new Array();\n";
	for ( $i=0; $i<=$numBands; $i+=1 ) {
		echo "var eventSource_".strval($i)." = new Timeline.DefaultEventSource();\n";
		stl_api_createEventSource($datafile, $categories[$i], "eventSource_".strval($i));
		echo "eventSources.push(eventSource_".strval($i).");\n";
	}
}

/* ---------------------------------------------------------------------------------
 * stl_api_createZone()
 * Build zone parameters
 * ---------------------------------------------------------------------------------*/
function stl_api_createZone(
			$unit,
			$startTime,
			$endTime,
			$magnify="1",
			$multiple="1"
) {
	$jvcode = '{'."\n";
	$jvcode .= '		start:	"'.$startTime.'",'."\n";
	$jvcode .= '		end:	"'.$endTime.'",'."\n";
	$jvcode .= '		magnify:	'.$magnify.','."\n";
	$jvcode .= '		unit:		'.$unit.','."\n";
	$jvcode .= '		multiple:	'.$multiple."\n";
	$jvcode .= '	}';
	return $jvcode;
}

/* ---------------------------------------------------------------------------------
 * stl_api_createHighlightDecorator()
 * Build highlight decorator parameters
 * ---------------------------------------------------------------------------------*/
function stl_api_createHighlightDecorator(
			$type,
			$start_date,
			$end_date,
			$start_label='',
			$end_label='',
			$color='#aaaaaa',
			$css_class='',
			$opacity='70'
) {
	
	$jvcode = 'new ' . $type . '(';
	$jvcode .= '{'."\n";
	if(strpos($type, 'Span') != false && isset($start_date) && isset($end_date)){
		$jvcode .= '	startDate:	"'.$start_date.'",'."\n";
		$jvcode .= '	endDate:	"'.$end_date.'",'."\n";
		$jvcode .= '	startLabel:	"'.$start_label.'",'."\n";
		$jvcode .= '	endLabel:	"'.$end_label.'",'."\n";
	}elseif(isset($start_date)){
		$jvcode .= '	date:	"'.$start_date.'",'."\n";
	}
	$jvcode .= '	color:	"'.$color.'",'."\n";
	$jvcode .= '	cssClass:	"'.$css_class.'",'."\n";
	$jvcode .= '	opacity:	'.$opacity. ','. "\n";
	$jvcode .= '	width: 10 ' . "\n";
	$jvcode .= '})';
	return $jvcode;
}

/* ---------------------------------------------------------------------------------
 * stl_api_createBandInfo()
 * --------------------------------------------------------------------------------*/
function stl_api_createBandInfo(
			$zones,
			$width,
			$intervalUnit,
			$intervalPixels,
			$theme,
			$showEventText="true",
			$eventSource="eventSource",
			$useCompactPainter=false,
			$date = null,
			$timeZone="0",
			$trackGap="",
			$trackHeight="",
			$locale='en'
) {
	if($date==null) $date = date('r'); // use current date as focus if none is set
	echo 'Timeline.createHotZoneBandInfo({' . "\n";
/*	echo 'zoomIndex:      10,
            zoomSteps:      new Array(
              {pixelsPerInterval: 280,  unit: Timeline.DateTime.HOUR},
              {pixelsPerInterval: 140,  unit: Timeline.DateTime.HOUR},
              {pixelsPerInterval:  70,  unit: Timeline.DateTime.HOUR},
              {pixelsPerInterval:  35,  unit: Timeline.DateTime.HOUR},
              {pixelsPerInterval: 400,  unit: Timeline.DateTime.DAY},
              {pixelsPerInterval: 200,  unit: Timeline.DateTime.DAY},
              {pixelsPerInterval: 100,  unit: Timeline.DateTime.DAY},
              {pixelsPerInterval:  50,  unit: Timeline.DateTime.DAY},
              {pixelsPerInterval: 400,  unit: Timeline.DateTime.MONTH},
              {pixelsPerInterval: 200,  unit: Timeline.DateTime.MONTH},
              {pixelsPerInterval: 100,  unit: Timeline.DateTime.MONTH}),';*/
	echo '	zones:['.$zones.'],'."\n";
	echo '	width:          "'.$width.'",'."\n";
	echo '	intervalUnit:   '.$intervalUnit.','."\n";
	echo '	intervalPixels: '.$intervalPixels.','."\n";
	echo '	eventSource:    '.$eventSource.','."\n";
	
	if($useCompactPainter && $showEventText=='true'){
		echo stl_api_createCompactEventPainter();
	}
	
	echo '	date:           "'.$date.'",'."\n";
	echo '	timeZone:	'.$timeZone.','."\n";
	if ($trackGap){		echo '	trackGap:	'.$trackGap.','."\n";	}
	if ($trackHeight){	echo '	trackHeight:	'.$trackHeight.','."\n";}
	echo '	showText:	'.$showEventText.','."\n";
	echo '	theme:		'.$theme.','."\n";
	// TODO: layout or overview? implement options in admin interface?
	#echo '	layout:		"overview",'."\n";
	echo '	overview:	'.($showEventText=='false'?'true':'false').','."\n";
	echo '	locale:		"'.$locale.'"'."\n";
	echo '})';
}

/* ---------------------------------------------------------------------------------
 * stl_api_createCompactEventPainter()
 * --------------------------------------------------------------------------------*/
function stl_api_createCompactEventPainter($args=''){
	$s  = '	eventPainter: Timeline.CompactEventPainter,' . "\n";
	$s .= '		eventPainterParams: {
		iconLabelGap: 10,
		labelRightMargin: 20,
		iconWidth: 50, // These are for per-event custom icons
		iconHeight: 50,
		stackConcurrentPreciseInstantEvents: {
			limit: 5,
			moreMessageTemplate: "%0 More Events",
			icon: "no-image-80.png", // default icon in stacks
			iconWidth: 50,
			iconHeight: 50
		} 
	}, ';
	return $s;
}


/* ---------------------------------------------------------------------------------
 * stl_api_createTheme()
 * --------------------------------------------------------------------------------*/
function stl_api_createTheme($args='') {
	$defaults = array(
		'name' => 'mytheme',
		'instant_icon' => 'Timeline.urlPrefix + "images/gray-circle.png"',
		'label_width' => '200',
		'bubble_width' => '320',
		'bubble_height' => '300',
		'firstDayOfWeek' => '0',
		'highlightOpacity' => '50',
		'line_show' => 'true',
		'line_opacity' => '25',
		'line_color' => '',
		'weekend_opacity' => '30',
		'weekend_color' => '',
		'hAlign' => "Bottom",
		'vAlign' => "Right",
		'track_offset' => '0.5',
		'track_height' => '1.5',
		'track_gap' => '0.5',
		'instant_impreciseOpacity' => '20',
		'instant_showLineForNoText' => 'true',
		'duration_opacity' => '100',
		'duration_impreciseOpacity' => '20',
		'highlightColors' => '["#FFFF00","#FFC000","#FF0000","#0000FF"]',
		'timeline_event_bubble_title' => "timeline-event-bubble-title",
		'timeline_event_bubble_body' => "timeline-event-bubble-body",
		'timeline_event_bubble_image' => "timeline-event-bubble-image",
		'timeline_event_bubble_wiki' => "timeline-event-bubble-wiki",
		'timeline_event_bubble_time' => "timeline-event-bubble-time"
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	echo 'var '.$name.' = Timeline.ClassicTheme.create();'."\n";

	echo $name.'.firstDayOfWeek = '.$firstDayOfWeek.';'."\n";
	// autoWidth new in 2.3.0
	#echo $name.'.autoWidth = true;'."\n";
// TODO: implement custom focus date on options page
//	echo $name .'.timelineStart = "Sun, 30 Jul 2002 11:37:44 +0200";';
// TODO: check correct zoom levels based on timeline resolution
//	echo $name .'.mouseWheel = "zoom";';
	echo $name.'.ether.highlightOpacity = '.$highlightOpacity.';'."\n";
	echo $name.'.ether.interval.line.show = '.$line_show.';'."\n";
	echo $name.'.ether.interval.line.color = "'.$line_color.'";'."\n";
	echo $name.'.ether.interval.line.opacity = '.$line_opacity.';'."\n";
	echo $name.'.ether.interval.weekend.color = "'.$weekend_color.'";'."\n";
	echo $name.'.ether.interval.weekend.opacity = '.$weekend_opacity.';'."\n";
	echo $name.'.ether.interval.marker.hAlign = "'.$hAlign.'";'."\n";

	echo $name.'.event.track.offset = '.$track_offset.';'."\n";
	echo $name.'.event.track.height = '.$track_height.';'."\n";
	echo $name.'.event.track.gap = '.$track_gap.';'."\n";
	echo $name.'.event.instant.icon = '.$instant_icon.';'."\n";
	echo $name.'.event.instant.impreciseOpacity = '.$instant_impreciseOpacity.';'."\n";
	echo $name.'.event.instant.showLineForNoText = '.$instant_showLineForNoText.';'."\n";
	echo $name.'.event.instant.iconWidth = 10'."\n";
	echo $name.'.event.instant.iconHeight = 10'."\n";
	echo $name.'.event.duration.opacity = '.$duration_opacity.';'."\n";
	echo $name.'.event.duration.impreciseOpacity = '.$duration_impreciseOpacity.';'."\n";
	echo $name.'.event.label.width = '.$label_width.';'."\n";
	echo $name.'.event.highlightColors = '.$highlightColors.';'."\n";
	echo $name.'.event.bubble.width = '.$bubble_width.';'."\n";
	echo $name.'.event.bubble.maxHeight = '.$bubble_height.';'."\n";
	echo $name.'.event.bubble.titleStyler = function(elmt) {';
	echo 'elmt.className = "'.$timeline_event_bubble_title.'";';
	echo '};'."\n";
	echo $name.'.event.bubble.bodyStyler = function(elmt) {';
	echo 'elmt.className = "'.$timeline_event_bubble_body.'";';
	echo '};'."\n";
	echo $name.'.event.bubble.imageStyler = function(elmt) {';
	echo 'elmt.className = "'.$timeline_event_bubble_image.'";';
	echo '};'."\n";
	echo $name.'.event.bubble.wikiStyler = function(elmt) {';
	echo 'elmt.className = "'.$timeline_event_bubble_wiki.'";';
	echo '};'."\n";
	echo $name.'.event.bubble.timeStyler = function(elmt) {';
	echo 'elmt.className = "'.$timeline_event_bubble_time.'";';
	echo '};'."\n";
	
	if($timeline_start!=0){
		echo $name.'.timeline_start = new Date("'.date('r', strtotime($timeline_start)).'");'."\n";
	}
	if($timeline_stop!=0){
		echo $name.'.timeline_stop = new Date("'.date('r', strtotime($timeline_stop)).'");'."\n";
	}
}
// EOF
?>