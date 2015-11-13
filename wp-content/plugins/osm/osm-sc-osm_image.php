<?php
/*  (c) Copyright 2014  Michael Kang (wp-osm-plugin.HanBlog.Net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// let's get the shortcode arguments
  	extract(shortcode_atts(array(
    // size of the map
    'width'     => '450', 'height' => '300',  
    // the zoomlevel of the map 
    'zoom'      => '7',     
    // track info
    'control'     => 'No',
    'map_border'      => 'none',
    'z_index'         => 'none',
    'extmap_address'  => 'No',
    'theme'           => 'ol'
	  ), $atts));
   
    if (($zoom < ZOOM_LEVEL_MIN || $zoom > ZOOM_LEVEL_MAX) && ($zoom != 'auto')){
      $this->traceText(DEBUG_ERROR, "e_zoomlevel_range");
      $this->traceText(DEBUG_INFO, "Error: (Zoomlevel: ".$zoom.")!");
      $zoom = 0;   
    }
    if ($width < 1 || $height < 1){
      Osm::traceText(DEBUG_ERROR, "e_map_size");
      Osm::traceText(DEBUG_INFO, "Error: ($width: ".$width." $height: ".$height.")!");
      $width = 450; $height = 300;
    }


	  $array_control = explode( ',', $control);
	  
    $array_control    = Osm_OpenLayers::checkControlType($array_control);

    // to manage several maps on the same page
    // create names with index
    static  $MapCounter = 0;
    $MapCounter += 1;
    $MapName = 'map_'.$MapCounter;
	
    Osm::traceText(DEBUG_INFO, "MapCounter = ".$MapCounter);
      
    // if we came up to here, let's load the image
    $output = '';	
    $output .= '<link rel="stylesheet" type="text/css" href="'.OSM_PLUGIN_URL.'/css/osm_map.css" />';
    $output .= '<style type="text/css">';
    if ($z_index != 'none'){ // fix for NextGen-Gallery
      $output .= '.entry .olMapViewport img {z-index: '.$z_index.' !important;}';   
      $output .= '.olControlNoSelect {z-index: '.$z_index.'+1.'.' !important;}';    
      $output .= '.olControlAttribution {z-index: '.$z_index.'+1.'.' !important;}';
    }
     
	$output .= '#'.$MapName.' {clear: both; padding: 0px; margin: 0px; border: 0px; width: 100%; height: 100%; margin-top:0px; margin-right:0px;margin-left:0px; margin-bottom:0px; left: 0px;}';
    $output .= '#'.$MapName.' img{clear: both; padding: 0px; margin: 0px; border: 0px; width: 100%; height: 100%; position: absolute; margin-top:0px; margin-right:0px;margin-left:0px; margin-bottom:0px;}';
	$output .= '</style>';

    $output .= '<div id="'.$MapName.'" class="OSM_IMG" style="width:'.$width.'px; height:'.$height.'px; overflow:hidden;padding:0px;border:'.$map_border.';">';

    
	if (Osm_LoadLibraryMode == SERVER_EMBEDDED){
          if(!defined('OL_LIBS_LOADED')) {
            $output .= '<script type="text/javascript" src="'.Osm_OL_LibraryLocation.'"></script>';
            define ('OL_LIBS_LOADED', 1);
          }
  
    if ($type == 'Mapnik' || $type == 'mapnik_ssl' || $type == 'Osmarender' || $type == 'CycleMap' || $type == 'basemap_at' || $type == 'stamen_watercolor' || $type == 'stamen_toner' || $type == 'All' || $type == 'AllOsm' || $type == 'Ext'){
      if(!defined('OSM_LIBS_LOADED')) {
        $output .= '<script type="text/javascript" src="'.Osm_OSM_LibraryLocation.'"></script>';
        define ('OSM_LIBS_LOADED', 1);
      }
    }
    elseif ($type == 'OpenSeaMap'){
      if(!defined('OSM_LIBS_LOADED')) {
        $output .= '<script type="text/javascript" src="'.Osm_OSM_LibraryLocation.'"></script>';
        $output .= '<script type="text/javascript" src="'.Osm_harbours_LibraryLocation.'"></script>';
        $output .= '<script type="text/javascript" src="'.Osm_map_utils_LibraryLocation.'"></script>';
        $output .= '<script type="text/javascript" src="'.Osm_utilities_LibraryLocation.'"></script>';
        define ('OSM_LIBS_LOADED', 1);
      }
    }
    elseif ($type == 'OpenWeatherMap'){
        if(!defined('OSM_LIBS_LOADED')) {
    		$output .= '<script type="text/javascript" src="'.Osm_OSM_LibraryLocation.'"></script>';
    		$output .= '<script type="text/javascript" src="'.Osm_openweather_LibraryLocation.'"></script>';
    		define ('OSM_LIBS_LOADED', 1);
    	}
    }    
    if ($type == 'GooglePhysical' || $type == 'GoogleStreet' || $type == 'GoogleHybrid' || $type == 'GoogleSatellite' || $type == 'All' || $type == 'AllGoogle' || $a_type == 'Ext' || $type == 'Google Physical' || $type == 'Google Street' || $type == 'Google Hybrid' || $type == 'Google Satellite'){
	  if (GOOGLE_LIBS_LOADED == 0) {
        $output .= '<script type="text/javascript" src="'.Osm_GOOGLE_LibraryLocation.'"></script>';
        define (GOOGLE_LIBS_LOADED, 1);
      }
    }
    $output .= '<script type="text/javascript" src="'.OSM_PLUGIN_JS_URL.'osm-plugin-lib.js"></script>';
  }
  elseif (Osm_LoadLibraryMode == SERVER_WP_ENQUEUE){
  // registered and loaded by WordPress
  }
  else{
    $this->traceText(DEBUG_ERROR, "e_library_config");
  }
      
  $extmap_init = 'new OpenLayers.Size('.width.', '.height.' )';

  $output .= '<script type="text/javascript">';
  $output .= '/* <![CDATA[ */';
  //$output .= 'jQuery(document).ready(';
  //$output .= 'function($) {';
  $output .= '(function($) {';
  $output .= Osm_OpenLayers::addOsmLayer("Zoomify", "ext", "0", "ext", "Zoomify", "Zoomify", $extmap_address, $extmap_init, $theme);

  // set center and zoom of the map
  //$output .= Osm_OpenLayers::setMapCenterAndZoom($lat, $long, $zoom);
  
  //$output .= '}';
  //$output .= ');';
  $output .= '})(jQuery)';
  $output .= '/* ]]> */';
  $output .= ' </script>';
  $output .= '</div>';

?>
