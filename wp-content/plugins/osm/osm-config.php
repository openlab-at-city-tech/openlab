<?php
// replace >True< with >False< if you want to disable it
define ("OSM_enable_Ajax", True);

// change them for shortcode generatgor & geotagger
// to your location
define ("OSM_default_lat",  apply_filters('get_osm_default_lat', get_option("osm_default_lat",43.758629))); 
define ("OSM_default_lon",  apply_filters('get_osm_default_lon', get_option("osm_default_lon",6.924225)));
define ("OSM_default_zoom", apply_filters('get_osm_default_zoom', get_option("osm_default_zoom",11)));


// SERVER_EMBEDDED   ... loaded by the plugin for each map (default)
// SERVER_WP_ENQUEUE ... registered and loaded by WordPress
define ("Osm_LoadLibraryMode", SERVER_EMBEDDED); 
// OpenStreetMap scripts and tiles
//define ("Osm_OSM_LibraryLocation", 'http://www.openstreetmap.org/openlayers/OpenStreetMap.js');
define ("Osm_OSM_LibraryLocation", OSM_PLUGIN_URL.'js/OSM/openlayers/OpenStreetMap.js');

  define ("Osm_Mapnik_Tiles_a", 'https://a.tile.openstreetmap.org');
  define ("Osm_Mapnik_Tiles_b", 'https://b.tile.openstreetmap.org');
  define ("Osm_Mapnik_Tiles_c", 'https://c.tile.openstreetmap.org');

  define ("Osm_OCM_Tiles_a", 'https://a.tile.opencyclemap.org/cycle');
  define ("Osm_OCM_Tiles_b", 'https://b.tile.opencyclemap.org/cycle');
  define ("Osm_OCM_Tiles_c", 'https://c.tile.opencyclemap.org/cycle');

// BaseMap (Austria)
  define ("Osm_BaseMap_Tiles", 'https://maps.wien.gv.at/basemap/geolandbasemap/');
 
//  thunderforest maps
define ("Osm_thunderforest_Landscape_Tiles", 'https://{a-c}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png');
define ("Osm_thunderforest_Outdoor_Tiles", 'https://{a-c}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png');
define ("Osm_thunderforest_Spinal_Tiles", 'https://{a-c}.tile.thunderforest.com/spinal-map/{z}/{x}/{y}.png');
define ("Osm_thunderforest_Pioneer_Tiles", 'https://{a-c}.tile.thunderforest.com/pioneer/{z}/{x}/{y}.png');
define ("Osm_thunderforest_Cycle_Tiles", 'https://{a-c}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png');
  
// OpenSeaMap
//define ("Osm_OpenSeaMap_Tiles", 'https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png');  
define ("Osm_OpenSeaMap_Tiles", 'https://t1.openseamap.org/seamark/{z}/{x}/{y}.png');

// OpenTopoMap
define ("Osm_OpenTopoMap_Tiles", 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png'); 

  


// Stamen
  define ("Osm_Stamen_Tiles_a", 'https://a.tile.stamen.com/');
  define ("Osm_Stamen_Tiles_b", 'https://b.tile.stamen.com/');
  define ("Osm_Stamen_Tiles_c", 'https://c.tile.stamen.com/');
// OpenLayers scripts
define ("Osm_OL_LibraryPath", OSM_PLUGIN_URL.'js/OL/2.13.1/');
define ("Osm_OL_LibraryLocation", OSM_PLUGIN_URL."js/OL/2.13.1/OpenLayers.js");

define ("Osm_OL_3_LibraryLocation", OSM_PLUGIN_URL."js/OL/6.3.1/ol.js");
define ("Osm_OL_3_CSS", OSM_PLUGIN_URL."js/OL/6.3.1/css/ol.css");
define ("Osm_map_CSS", OSM_PLUGIN_URL."css/osm_map.css");
define ("Osm_OL_3_Ext_LibraryLocation", OSM_PLUGIN_URL."js/osm-v3-plugin-lib.js");
define ("Osm_OL_3_MetaboxEvents_LibraryLocation", OSM_PLUGIN_URL."js/osm-metabox-events.js");
define ("Osm_OL_3_Ext_CSS", OSM_PLUGIN_URL."css/osm_map_v3.css");

// OpenSeaMap scripts
define ("Osm_harbours_LibraryLocation", OSM_PLUGIN_URL.'js/OSeaM/harbours.js');
define ("Osm_map_utils_LibraryLocation", OSM_PLUGIN_URL.'js/OSeaM/map_utils.js');
define ("Osm_utilities_LibraryLocation", OSM_PLUGIN_URL.'js/OSeaM/utilities.js');
// OpenWeather scripts
define ("Osm_openweather_LibraryLocation", 'https://openweathermap.org/js/OWM.OpenLayers.1.3.8.js');

//Startup multiple maps on one 
define ("Osm_map_startup_LibraryLocation", OSM_PLUGIN_URL.'js/osm-startup-lib.js');


?>
