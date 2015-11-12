<?php
/*
  import function for OSM wordpress plugin
  Michael Kang * april 2009
  http://www.Fotomobil.at/wp-osm-plugin
  file is used within: 
  function createMarkerList($a_import, $a_import_UserName, $a_Customfield)
*/
?>

<?php  

define (GC_STATS_INTERFACE_VER,1);

// wpgmg plugin of Karl Kevilus
// generate lat,lon from address
// http://karlkevilus.com/wordpress-google-maps-geocoder/
if ($a_import == 'wpgmg'){
   $temp_lat = get_post_meta($post->ID, WPGMG_LAT, true);  
   $temp_lon = get_post_meta($post->ID, WPGMG_LON, true);  
}
// gcStats plugin of Michael Jostmeyer
// statistics about geocaching
// http://michael.josi.de/projects/gcstats/
else if($a_import == 'gcstats'){

  // check whether the plugin is loaded
  if (!function_exists('gcStats__getInterfaceVersion')) {
     Osm::traceText(DEBUG_ERROR, "e_missing_gcStats");
     return;
  }
  // the plugin-version is not important, but the interface of gcStats
  // has to fullfill all the requests of OSM-plugin
  else if (gcStats__getInterfaceVersion() < GC_STATS_INTERFACE_VER){
     Osm::traceText(DEBUG_ERROR, "e_version_gcStats");
     return;
  }

  // get the data from gcstats plugin, check it and add it to the marker-array
  $temp_caches = gcStats__getCachesData($a_import_UserName, $a_Customfield);
  foreach($temp_caches as $CachesArray){
    list($temp_lat, $temp_long) = Osm::checkLatLongRange('gcStats',$CachesArray[lat], $CachesArray[lon]);
    $MarkerArray[] = array('lat'=> $temp_lat,'lon'=>$temp_long,'marker'=>GCSTATS_MARKER_PNG, 'text' => $CachesArray[text],'popup_height'=>'150', 'popup_width'=>'100');
  }
}

// extra comment field plugin of Nate Weiner
// adding comment fields 
// http://www.ideashower.com/our_solutions/wordpress-plugin-extra-comment-fields/
else if($a_import == 'ecf'){
  
  // Get Total Comments Poster
	global $wpdb;

    // check whether the plugin is loaded
    if (!function_exists('ecf_getComments')) {
       Osm::traceText(DEBUG_ERROR, "e_missing_ecf");
       return;
    }
  
    //		WHERE comment_approved = '1' AND comment_type = '' AND
    // SUBSTRING(comment_content,1,80) AS com_excerpt    
		$sql = "SELECT DISTINCT ID, post_title, comment_author, post_password, comment_ID, comment_post_ID, comment_content
    FROM $wpdb->comments
		LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
		$wpdb->posts.ID)
		WHERE comment_type = '' AND
		post_password = '' AND comment_post_ID = '".$post->ID."'
		ORDER BY comment_date DESC
		LIMIT 100";
		$comments = $wpdb->get_results($sql);
    $comments = ecf_getComments($comments, $post->ID);

		foreach ($comments as $comment) :
		  Osm::traceText(DEBUG_INFO, "Found a tagged comment!");
      $ecf_lat = $comment->extra_lat;
      $ecf_lon = $comment->extra_lon;
      $ecf_txt = $comment->comment_content;
      $ecf_txt= preg_replace("/[^a-zA-Z0-9 ]/","",$ecf_txt);
      //echo $ecf_txt;
      if ($ecf_lat != '' && $ecf_lon != ''){
        list($ecf_lat, $ecf_lon) = Osm::checkLatLongRange('ecf_'.$ecf_author,$ecf_lat, $ecf_lon);
        $MarkerArray[] = array('lat'=> $ecf_lat,'lon'=>$ecf_lon,'marker'=>INDIV_MARKERG, 'text' => $ecf_txt,'popup_height'=>'150', 'popup_width'=>'100');
      }
    endforeach;
  } 
?> 


