<?php
/*
  OSM Error Msg for OSM wordpress plugin
  MiKa * created: april 2009
  plugin: http://wp-osm-plugin.Hyumika.com
  blog:   http://www.Hyumika.com
*/
?>
<?php  
  // these messages are related to the gcStats plugin
  self::$OSM_ErrorMsg->add('e_missing_gcStats', 'gcStats plugin is not activated!');
  self::$OSM_ErrorMsg->add('e_version_gcStats', 'gcStats plugin has to be updated (If-Version)!');
  self::$OSM_ErrorMsg->add('e_no_entry_gcStats', 'gcStats plugin do not have any entries for this user!');
  // these messages are related to the extra comment field plugin
  self::$OSM_ErrorMsg->add('e_missing_ecf',  'Extra Comment Field plugin is not activated!');
  // these messages are related to the option page and map creation
  self::$OSM_ErrorMsg->add('e_options_not_updated',  'Not all options updated!');
  self::$OSM_ErrorMsg->add('i_options_updated',  'Options updated!');
  self::$OSM_ErrorMsg->add('e_zoomlevel_range',  'Map Zoomlevel out of range or invalid! (using defaultvalue)');
  self::$OSM_ErrorMsg->add('e_lat_lon_range',  'Lat or Lon is out of range or invalid (using defaultvalue)!');    
  self::$OSM_ErrorMsg->add('e_map_size',  'Map width or height is out of range or invalid (using defaultsize)!');
  self::$OSM_ErrorMsg->add('e_php_getlat_missing_arg',  'Did not get latitude [missing argument @ OSM_getCoordinateLat]');
  self::$OSM_ErrorMsg->add('e_php_getlon_missing_arg',  'Did not get longitude [missing argument @ OSM_getCoordinateLong]');    
  self::$OSM_ErrorMsg->add('e_marker_size',  'If you define a marker, the width and height has to be defined as well!');    
  self::$OSM_ErrorMsg->add('e_use_marker_all_posts',  'Use the argument import instead of marker_all_posts!');    
  self::$OSM_ErrorMsg->add('e_import_unknwon',  'Import type is unknown!');  
  self::$OSM_ErrorMsg->add('e_unknown_icon',  'Invalid marker_name!');  
	// these messages are related to the config file
  self::$OSM_ErrorMsg->add('e_library_config',  'Could not load OSM library, check LoadLibraryMode @ wp-content/plugins/osm/osm-config.php!');  
  self::$OSM_ErrorMsg->add('e_invalid_control',  'Invalid usage of control tag!');  
  self::$OSM_ErrorMsg->add('e_gpx_list_error',  'Num of Gpx files does not match to num of Gpx colours!');  
  self::$OSM_ErrorMsg->add('e_missing_rs_error',  'Missing the routingservice at marker_routing argument!');
  self::$OSM_ErrorMsg->add('e_not_osm_icon',  'Not valid WP OSM Plugin icon!');
  self::$OSM_ErrorMsg->add('e_not_osm_icon',  'You have to add a marker to the post at [Add marker] tab!');
  // others
  self::$OSM_ErrorMsg->add('e_mww_error_arg',  'Error at argument mwz (true|false)!');
  self::$OSM_ErrorMsg->add('e_filelist_mismatch',  'file_color_list does not match to file_list!');
  self::$OSM_ErrorMsg->add('e_add_marker',  'You have to add a marker to the post at [Add marker] tab!');
?>
