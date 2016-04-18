<?php
// replace >True< with >False< if you want to disable it
define ("OSM_enable_Ajax", True);

// change them for shortcode generatgor & geotagger
// to your location
define ("OSM_default_lat", 48.856614);
define ("OSM_default_lon", 2.352222);
define ("OSM_default_zoom", 3);

// SERVER_EMBEDDED   ... loaded by the plugin for each map (default)
// SERVER_WP_ENQUEUE ... registered and loaded by WordPress
define ("Osm_LoadLibraryMode", SERVER_EMBEDDED); 
// OpenStreetMap scripts and tiles
//define ("Osm_OSM_LibraryLocation", 'http://www.openstreetmap.org/openlayers/OpenStreetMap.js');
define ("Osm_OSM_LibraryLocation", OSM_PLUGIN_URL.'js/OSM/openlayers/OpenStreetMap.js');

//if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
//  define ("Osm_Mapnik_Tiles_a", 'https://a.tile.openstreetmap.org');
//  define ("Osm_Mapnik_Tiles_b", 'https://b.tile.openstreetmap.org');
//  define ("Osm_Mapnik_Tiles_c", 'https://c.tile.openstreetmap.org');

//  define ("Osm_OCM_Tiles_a", 'https://a.tile.opencyclemap.org/cycle');
//  define ("Osm_OCM_Tiles_b", 'https://b.tile.opencyclemap.org/cycle');
//  define ("Osm_OCM_Tiles_c", 'https://c.tile.opencyclemap.org/cycle');

//}
//else{
  define ("Osm_Mapnik_Tiles_a", 'http://a.tile.openstreetmap.org');
  define ("Osm_Mapnik_Tiles_b", 'http://b.tile.openstreetmap.org');
  define ("Osm_Mapnik_Tiles_c", 'http://c.tile.openstreetmap.org');

  define ("Osm_OCM_Tiles_a", 'http://a.tile.opencyclemap.org/cycle');
  define ("Osm_OCM_Tiles_b", 'http://b.tile.opencyclemap.org/cycle');
  define ("Osm_OCM_Tiles_c", 'http://c.tile.opencyclemap.org/cycle');
//}

// BaseMap (Austria)
  define ("Osm_BaseMap_Tiles", 'http://maps.wien.gv.at/basemap/geolandbasemap/');
// Stamen
  define ("Osm_Stamen_Tiles_a", 'http://a.tile.stamen.com/');
  define ("Osm_Stamen_Tiles_b", 'http://b.tile.stamen.com/');
  define ("Osm_Stamen_Tiles_c", 'http://c.tile.stamen.com/');
// OpenLayers scripts
define ("Osm_OL_LibraryPath", OSM_PLUGIN_URL.'js/OL/2.13.1/');
define ("Osm_OL_LibraryLocation", OSM_PLUGIN_URL."js/OL/2.13.1/OpenLayers.js");

define ("Osm_OL_3_LibraryLocation", OSM_PLUGIN_URL."js/OL/3.10.1/ol.js");
define ("Osm_OL_3_CSS", OSM_PLUGIN_URL."js/OL/3.10.1/css/ol.css");
define ("Osm_OL_3_Ext_LibraryLocation", OSM_PLUGIN_URL."js/osm-v3-plugin-lib.js");
define ("Osm_OL_3_Ext_CSS", OSM_PLUGIN_URL."css/osm_map_v3.css");

// Google
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
  define ("Osm_GOOGLE_LibraryLocation", 'https://maps.google.com/maps/api/js?sensor=false');}
else{
//  define ("Osm_GOOGLE_LibraryLocation", 'http://maps.google.com/maps/api/js?sensor=false');
define ("Osm_GOOGLE_LibraryLocation", 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false');
}
// OpenSeaMap scripts
define ("Osm_harbours_LibraryLocation", OSM_PLUGIN_URL.'js/OSeaM/harbours.js');
define ("Osm_map_utils_LibraryLocation", OSM_PLUGIN_URL.'js/OSeaM/map_utils.js');
define ("Osm_utilities_LibraryLocation", OSM_PLUGIN_URL.'js/OSeaM/utilities.js');
// OpenWeather scripts
define ("Osm_openweather_LibraryLocation", 'http://openweathermap.org/js/OWM.OpenLayers.1.3.6.js');



?>
