<?php
/*
  OSM Error Msg for OSM wordpress plugin
  MiKa * created: april 2009
  plugin: http://wp-osm-plugin.Hanblog.net
  blog:   http://www.HanBlog.net
*/
?>
<?php  
    // these messages are related to the gcStats plugin
    $this->ErrorMsg->add('e_missing_gcStats', __('gcStats plugin is not activated!', 'OSM'));
    $this->ErrorMsg->add('e_version_gcStats', __('gcStats plugin has to be updated (If-Version)!', 'OSM'));
    $this->ErrorMsg->add('e_no_entry_gcStats', __('gcStats plugin do not have any entries for this user!', 'OSM'));
    // these messages are related to the extra comment field plugin
    $this->ErrorMsg->add('e_missing_ecf',  __('Extra Comment Field plugin is not activated!', 'OSM'));
    // these messages are related to the option page and map creation
    $this->ErrorMsg->add('e_options_not_updated',  __('Not all options updated!', 'OSM'));
    $this->ErrorMsg->add('i_options_updated',  __('Options updated!', 'OSM'));
    $this->ErrorMsg->add('e_zoomlevel_range',  __('Map Zoomlevel out of range or invalid! (using defaultvalue)', 'OSM'));
    $this->ErrorMsg->add('e_lat_lon_range',  __('Lat or Lon is out of range or invalid (using defaultvalue)!', 'OSM'));    
    $this->ErrorMsg->add('e_map_size',  __('Map width or height is out of range or invalid (using defaultsize)!', 'OSM'));
    $this->ErrorMsg->add('e_php_getlat_missing_arg',  __('Did not get latitude [missing argument @ OSM_getCoordinateLat]', 'OSM'));
    $this->ErrorMsg->add('e_php_getlon_missing_arg',  __('Did not get longitude [missing argument @ OSM_getCoordinateLong]', 'OSM'));    
    $this->ErrorMsg->add('e_marker_size',  __('If you define a marker, the width and height has to be defined as well!', 'OSM'));    
    $this->ErrorMsg->add('e_use_marker_all_posts',  __('Use the argument import instead of marker_all_posts!', 'OSM'));    
    $this->ErrorMsg->add('e_import_unknwon',  __('Import type is unknown!', 'OSM'));  
    $this->ErrorMsg->add('e_unknown_icon',  __('Invalid marker_name!', 'OSM'));  
	// these messages are related to the config file
    $this->ErrorMsg->add('e_library_config',  __('Could not load OSM library, check LoadLibraryMode @ wp-content/plugins/osm/osm-config.php!', 'OSM'));  
    $this->ErrorMsg->add('e_invalid_control',  __('Invalid usage of control tag!', 'OSM'));  
    $this->ErrorMsg->add('e_gpx_list_error',  __('Num of Gpx files does not match to num of Gpx colours!', 'OSM'));  
    $this->ErrorMsg->add('e_missing_rs_error',  __('Missing the routingservice at marker_routing argument!', 'OSM'));
    $this->ErrorMsg->add('e_not_osm_icon',  __('Not valid WP OSM Plugin icon!', 'OSM'));
    $this->ErrorMsg->add('e_not_osm_icon',  __('You have to add a marker to the post at [Add marker] tab!', 'OSM'));
?>
