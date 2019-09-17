<?php
/*
Plugin Name: OSM
Plugin URI: https://wp-osm-plugin.HanBlog.net
Description: Embeds maps in your blog and adds geo data to your posts.  Find samples and a forum on the <a href="https://wp-osm-plugin.HanBlog.net">OSM plugin page</a>.
Version: 5.2.2
Author: MiKa
Author URI: http://www.HanBlog.net
Minimum WordPress Version Required: 3.0
*/

/*  (c) Copyright 2019  MiKa (www.HanBlog.Net)

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
load_plugin_textdomain('OSM', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

define ("PLUGIN_VER", "V5.2.2");

// modify anything about the marker for tagged posts here
// instead of the coding.
define ("POST_MARKER_PNG", "marker_posts.png");
define ('POST_MARKER_PNG_HEIGHT', 2);
define ('POST_MARKER_PNG_WIDTH', 2);

define ("GCSTATS_MARKER_PNG", "geocache.png");
define ('GCSTATS_MARKER_PNG_HEIGHT', 25);
define ('GCSTATS_MARKER_PNG_WIDTH', 25);

define ("INDIV_MARKER", "marker_blue.png");
define ('INDIV_MARKER_PNG_HEIGHT', 25);
define ('INDIV_MARKER_PNG_WIDTH', 25);

// these defines are given by OpenStreetMap.org
define ("URL_INDEX", "http://www.openstreetmap.org/index.html?");
define ("URL_LAT","&mlat=");
define ("URL_LON","&mlon=");
define ("URL_ZOOM_01","&zoom=[");
define ("URL_ZOOM_02","]");
define ('ZOOM_LEVEL_GOOGLE_MAX',22);
define ('ZOOM_LEVEL_MAX',18);       // standard is 17, only mapnik is 18
define ('ZOOM_LEVEL_MIN',1);

// other geo plugin defines
// google-maps-geocoder
define ("WPGMG_LAT", "lat");
define ("WPGMG_LON", "lng");

// some general defines
define ('LAT_MIN',-90);
define ('LAT_MAX',90);
define ('LON_MIN',-180);
define ('LON_MAX',180);

// tracelevels
define ('DEBUG_OFF', 0);
define ('DEBUG_ERROR', 1);
define ('DEBUG_WARNING', 2);
define ('DEBUG_INFO', 3);
define ('HTML_COMMENT', 10);

// Load OSM library mode
define ('SERVER_EMBEDDED', 1);
define ('SERVER_WP_ENQUEUE', 2);

define('OSM_PRIV_WP_CONTENT_URL', site_url() . '/wp-content' );
define('OSM_PRIV_WP_CONTENT_DIR', content_url() . 'wp-content' );
define('OSM_PRIV_WP_PLUGIN_URL', plugins_url() );
define('OSM_PRIV_WP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

define('OSM_PLUGIN_URL', OSM_PRIV_WP_PLUGIN_URL."/osm/");
define('OSM_PLUGIN_ICONS_URL', OSM_PLUGIN_URL."icons/");
define('URL_POST_MARKER', OSM_PLUGIN_URL.POST_MARKER_PNG);
define('OSM_PLUGIN_THEMES_URL', OSM_PLUGIN_URL."themes/");
define('OSM_OPENLAYERS_THEMES_URL', content_url(). '/uploads/osm/theme/' );
define('OSM_PLUGIN_JS_URL', OSM_PLUGIN_URL."js/");

global $wp_version;
if (version_compare($wp_version,"3.0","<")){
  exit('[OSM plugin - ERROR]: At least Wordpress Version 3.0 is required for this plugin!');
}

// get the configuratin by
// default or costumer settings

include ('osm-config.php');


// do not edit this
define ("Osm_TraceLevel", DEBUG_ERROR);
//define ("Osm_TraceLevel", DEBUG_OFF);

//define ("Osm_TraceLevel", DEBUG_INFO);


// If the function exists this file is called as upload_mimes.
// We don't do anything then.
if ( ! function_exists( 'osm_restrict_mime_types' ) ) {
  add_filter( 'upload_mimes', 'osm_restrict_mime_types' );
  /**
  * Retrun allowed mime types
  *
  * @see function get_allowed_mime_types in wp-includes/functions.php
  * @param array Array of mime types
  * @return array Array of mime types keyed by the file extension regex corresponding to those types.
  */
  function osm_restrict_mime_types( $mime_types ) {
    $mime_types['gpx'] = 'text/gpx';
    $mime_types['kml'] = 'text/kml';
    return $mime_types;
  }
}


function saveGeotagAndPic(){

  $latlon  = $_POST['lat'].','.$_POST['lon'];
  $icon    = $_POST['icon'];
  $post_id = $_POST['post_id'];
  $nonce   = $_POST['geotag_nonce'];


  if (!wp_verify_nonce($nonce, 'osm_geotag_nonce')){
    echo "Error: Bad ajax request";
  }
  else {
    _e('Geotag saved, you can use it at [Map & geotags]!','OSM');
    $CustomField =  get_option('osm_custom_field','OSM_geo_data');
    delete_post_meta($post_id, $CustomField);
    delete_post_meta($post_id, "OSM_geo_icon");
    add_post_meta($post_id, $CustomField, $latlon, true );
    if ($icon != ""){
      add_post_meta($post_id, "OSM_geo_icon", $icon, true );
    }
  }
  wp_die();
}

function savePostMarker(){
  $MarkerId = $_POST['MarkerId'];
  $MarkerLatLon  = $_POST['MarkerLat'].','.$_POST['MarkerLon'];
  $MarkerIcon      = $_POST['MarkerIcon'];
  $MarkerName   = $_POST['MarkerName'];
  $MarkerText      = $_POST['MarkerText'];
  $post_id = $_POST['post_id'];
  $nonce   = $_POST['marker_nonce'];

  $zoom    = $_POST['map_zoom'];
  $BorderField = ' map_border="thin solid '.$_POST['map_border'].'" ';
  $MarkerId = $MarkerId;
  $MapTypeField = 'type="'.$_POST['map_type'].'" ';
  $ControlField = ' control="'.$_POST['map_controls'].'" ';

  if (!wp_verify_nonce($nonce, 'osm_marker_nonce')){
    echo "Error: Bad ajax request";
  }
  else {
    delete_post_meta($post_id, 'OSM_Marker_0'.$MarkerId.'_Name');
    delete_post_meta($post_id, 'OSM_Marker_0'.$MarkerId.'_LatLon');
    delete_post_meta($post_id, 'OSM_Marker_0'.$MarkerId.'_Icon');
    delete_post_meta($post_id, 'OSM_Marker_0'.$MarkerId.'_Text');
    add_post_meta($post_id, 'OSM_Marker_0'.$MarkerId.'_Name', $MarkerName, true );
    add_post_meta($post_id, 'OSM_Marker_0'.$MarkerId.'_LatLon', $MarkerLatLon, true );
    add_post_meta($post_id, 'OSM_Marker_0'.$MarkerId.'_Icon', $MarkerIcon, true );
    add_post_meta($post_id, 'OSM_Marker_0'.$MarkerId.'_Text', $MarkerText, true );

    $GenTxt = '<br>'.'[osm_map_v3 map_center= "'.$MarkerLatLon.'" zoom="'.$zoom.'" width="95%" height="450" '.$BorderField.'post_markers="'.$MarkerId.'" '.$MapTypeField . $ControlField .']';

    echo $GenTxt;
  }
  wp_die();
}


// If the function exists this file is called as post-upload-ui.
// We don't do anything then.
if ( ! function_exists( 'osm_restrict_mime_types_hint' ) ) {
	// add to wp
	add_action( 'post-upload-ui', 'osm_restrict_mime_types_hint' );
	/**
	 * Get an Hint about the allowed mime types
	 *
	 * @return  void
	 */
	function osm_restrict_mime_types_hint() {
	  echo '<br />';
          _e('OSM plugin added: GPX / KML','OSM');
	}
}


// hook to create the meta box
// enable in osm-config-sample.php
if (OSM_enable_Ajax){
  add_action( 'add_meta_boxes', 'osm_map_create' );
}
include('osm-args-class.php');
include('osm-metabox.php');
include('osm_map/osm-oljs2.php');
include('osm_map_v3/osm-oljs3.php');
include('osm_map/osm-icon.php');
include('osm-icon-class.php');

// let's be unique ...
// with this namespace
class Osm
{
  function __construct(){
  $this->localizionName = 'Osm';
    //$this->TraceLevel = DEBUG_INFO;
	$this->ErrorMsg = new WP_Error();
	$this->initErrorMsg();

    // add the WP action
    add_action('wp_head', array(&$this, 'wp_head'));
    add_action('admin_head', array(&$this, 'admin_head'));
    add_action('admin_menu', array(&$this, 'admin_menu'));
    add_action('wp_print_scripts',array(&$this, 'show_enqueue_script'));
    add_action('widgets_init', 'register_osm_widget' );
    add_action('wp_ajax_act_saveGeotag', 'saveGeotagAndPic');
    add_action('wp_ajax_act_saveMarker', 'savePostMarker');

    // add the WP shortcode
    add_shortcode('osm_map',array(&$this, 'sc_showMap'));
    add_shortcode('osm_map_v3',array(&$this, 'sc_OL3JS'));
    add_shortcode('osm_image',array(&$this, 'sc_showImage'));
    add_shortcode('osm_info',array(&$this, 'sc_info'));
  }

  function initErrorMsg()
  {
    include('osm-error-msg.php');
  }

  public static function traceErrorMsg($e = '')
  {
   if (!isset($this) || $this == null){
     return $e;
   }
   $EMsg = $this->ErrorMsg->get_error_message($e);
   if ($EMsg == null){
     return $e;
     //return__("Unknown errormessage",$this->localizionName);
   }
   return $EMsg;
  }

  public static function traceText($a_Level, $a_String)
  {
    $TracePrefix = array(
    DEBUG_ERROR =>'[OSM-Plugin-Error]:',
    DEBUG_WARNING=>'[OSM-Plugin-Warning]:',
    DEBUG_INFO=>'[OSM-Plugin-Info]:');

    if ($a_Level == DEBUG_ERROR){
      echo '<div class="osm_error_msg"><p><strong style="color:red">'.$TracePrefix[$a_Level].Osm::traceErrorMsg($a_String).'</strong></p></div>';
    }
    else if ($a_Level <= Osm_TraceLevel){
      echo $TracePrefix[$a_Level].$a_String.'<br>';
    }
    else if ($a_Level == HTML_COMMENT){
      echo "<!-- ".$a_String." --> \n";
    }
  }

	// add it to the Settings page
  static function options_page_osm()
  {
      
    // 0 = no error;
    // 1 = error occured
    $Option_Error = 0;

    // name of the custom field to store Long and Lat
    // for the geodata of the post
    $osm_custom_field  = get_option('osm_custom_field','OSM_geo_data');
           
    //default map values 
    $osm_default_lat=get_option('osm_default_lat',OSM_default_lat);  
    $osm_default_lon=get_option('osm_default_lon',OSM_default_lon);  
    $osm_default_zoom=get_option('osm_default_zoom',OSM_default_zoom); 
    // update default maps values
    if (isset($_POST['osm_default_lat'])) {
        $temp_lat=(float) $_POST['osm_default_lat'];
        if ($temp_lat>LAT_MAX || $temp_lat<LAT_MIN) {
          $Option_Error = 1;
          Osm::traceText(DEBUG_ERROR, "e_default_lat_range");   
        } else {
          $osm_default_lat=$temp_lat;   
          update_option('osm_default_lat',$temp_lat);  
        }
    } 
    if (isset($_POST['osm_default_lon'])) {
        $temp_lon=(float) $_POST['osm_default_lon'];
        if ($temp_lon>LON_MAX || $temp_lon<LON_MIN) {
          $Option_Error = 1;
          Osm::traceText(DEBUG_ERROR, "e_default_lon_range");   
        } else {
          $osm_default_lon=$temp_lon;   
          update_option('osm_default_lon',$temp_lon);  
        }
     } 
    if (isset($_POST['osm_default_zoom'])) {
        $temp_zoom=(int) $_POST['osm_default_zoom'];
        if ($temp_zoom>19 || $temp_zoom<1) {
          $Option_Error = 1;
          Osm::traceText(DEBUG_ERROR, "e_default_zoom_range");   
        } else {
          $osm_default_zoom=$temp_zoom;
          update_option('osm_default_zoom',$temp_zoom);  
        }
     } 
     // Let the user know whether all was fine or not
     if ($Option_Error  == 0){
        Osm::traceText(DEBUG_INFO, "i_options_updated");
     }
     else {
        Osm::traceText(DEBUG_ERROR, "e_options_not_updated");
     }

    include('osm-options.php');
  }

  // put meta tags into the head section
  function wp_head($not_used)
  {
    global $wp_query;
    global $post;

    $lat = '';
    $lon = '';
    $CustomField =  get_option('osm_custom_field','OSM_geo_data');

    if (($CustomField != false) && (get_the_ID() !== false) && (get_post_meta($post->ID, $CustomField, true))){
      $PostLatLon = get_post_meta($post->ID, $CustomField, true);
      if (!empty($PostLatLon)) {
        list($lat, $lon) = explode(',', $PostLatLon);
      }
    }


// global Javascript variables
echo '<script type="text/javascript"> 

/**  all layers have to be in this global array - in further process each map will have something like vectorM[map_ol3js_n][layer_n] */
var vectorM = [[]];


/** put translations from PHP/mo to JavaScript */
var translations = [];

/** global GET-Parameters */
var HTTP_GET_VARS = [];

</script>';

    if(is_single() && ($lat != '') && ($lon != '')){
      $title = convert_chars(strip_tags(get_bloginfo("name")))." - ".$wp_query->post->post_title;
      Osm::traceText(HTML_COMMENT, 'OSM plugin '.PLUGIN_VER.': adding geo meta tags:');
    }
    else{
      Osm::traceText(HTML_COMMENT, 'OSM plugin '.PLUGIN_VER.': did not add geo meta tags.');
    return;
    }

    // let's store geo data with W3 standard
	echo "<meta name=\"ICBM\" content=\"{$lat}, {$lon}\" />\n";
	echo "<meta name=\"DC.title\" content=\"{$wp_query->post->post_title}\" />\n";
        echo "<meta name=\"geo.placename\" content=\"{$wp_query->post->post_title}\"/>\n";
	echo "<meta name=\"geo.position\"  content=\"{$lat};{$lon}\" />\n";
  }

  // global JS variable in admin area
  function admin_head($not_used)
  {
// global Javascript variables
echo '<script type="text/javascript"> 

/**  all layers have to be in this global array - in further process each map will have something like vectorM[map_ol3js_n][layer_n] */
var vectorM = [[]];


/** put translations from PHP/mo to JavaScript */
var translations = [];

/** global GET-Parameters */
var HTTP_GET_VARS = [];

</script>';

  }

  function gps2Num($coordPart) {
    $parts = explode('/', $coordPart);
    if (count($parts) <= 0){
        return 0;
    }
    if (count($parts) == 1){
        return $parts[0];
    }
    return floatval($parts[0]) / floatval($parts[1]);
  }

  function getGps($exifCoord, $hemi) {
    $degrees = count($exifCoord) > 0 ? OSM::gps2Num($exifCoord[0]) : 0;
    $minutes = count($exifCoord) > 1 ? OSM::gps2Num($exifCoord[1]) : 0;
    $seconds = count($exifCoord) > 2 ? OSM::gps2Num($exifCoord[2]) : 0;

    $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
    return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
  }

  static function getPostMarkerCFN($a_Number, $a_FieldName) {
    $CustomFieldName = "OSM_Marker_0".$a_Number."_".$a_FieldName;
    return $CustomFieldName;
  }

  /**
    @param $a_import geotag(osm,osm_l) or postmarkers (1..9,all)
    @param $a_import_osm_cat_incl_name only for geotag (only used for geotagged)
    @param $a_import_osm_cat_excl_name only for getoag (only used for geotagged)
    @param $a_post_type  (only used for geotagged)
    @param $a_import_osm_custom_tax_incl_name only for geotag
    @param $a_custom_taxonomy only for geotag
    @return list of markers
  */
  static function OL3_createMarkerList($a_import, $a_import_osm_cat_incl_name,  $a_import_osm_cat_excl_name, $a_post_type, $a_import_osm_custom_tax_incl_name, $a_taxonomy)
  {
     Osm::traceText(DEBUG_INFO, "OL3_createMarkerList(".$a_import.",".$a_import_osm_cat_incl_name.",".$a_import_osm_cat_excl_name.",".$a_post_type.",".$a_import_osm_custom_tax_incl_name.",".$a_taxonomy.")");
     global $post;
     $post_org = $post;

     /**++++++++++++++++++++++++++++++++++++++++++
     + posts or pages with geotags              +
     +++++++++++++++++++++++++++++++++++++++++++*/
     if ($a_import == 'osm' || $a_import == 'osm_l'){
       $CustomFieldName = get_option('osm_custom_field','OSM_geo_data');
       // let's see which posts are using our geo data ...
       //+++
       global $wpdb;
       global $post_type;

       $post_type = esc_sql($a_post_type);
       $CustomFieldName     = esc_sql($CustomFieldName);

       if ($a_import_osm_cat_incl_name == "osm_all" ){
         $querystr = "
         SELECT DISTINCT wposts.*
         FROM $wpdb->posts wposts
	       LEFT JOIN $wpdb->postmeta wpostmeta ON wposts.ID = wpostmeta.post_id
	       LEFT JOIN $wpdb->term_relationships ON (wposts.ID = $wpdb->term_relationships.object_id)
	       LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
         WHERE wpostmeta.meta_key = '$CustomFieldName'
         AND wposts.post_status = 'publish'
         AND wposts.post_type = '$post_type'
         ";
       }
       else {
        $querystr = "
        SELECT wposts.*
        FROM $wpdb->posts wposts
          JOIN $wpdb->postmeta wpostmeta ON (wpostmeta.post_id = wposts.ID)
          JOIN $wpdb->term_relationships tr ON (tr.object_id = wposts.ID)
          JOIN $wpdb->term_taxonomy tt ON (tt.term_taxonomy_id = tr.term_taxonomy_id)
          JOIN $wpdb->terms t ON tt.term_id = t.term_id
        WHERE wpostmeta.meta_key = '$CustomFieldName'
        AND wposts.post_status = 'publish'
        AND wposts.post_type = '$post_type'
        AND tt.taxonomy = '$a_taxonomy'
        AND t.name = '$a_import_osm_cat_incl_name'
        ";
       }
       $pageposts = $wpdb->get_results($querystr, OBJECT);


       if ($pageposts){
         foreach ($pageposts as $post){
           setup_postdata($post);
           $Data = get_post_meta($post->ID, $CustomFieldName, true);
           // remove space before and after comma
           $Data = preg_replace('/\s*,\s*/', ',',$Data);
           $GeoData_Array = explode( ' ', $Data );
  	   list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]);
           $PostMarker = get_post_meta($post->ID, 'OSM_geo_icon', true);
           $PostMarker = Osm_icon::replaceOldIcon($PostMarker);

           $Marker_Txt = '<a href="'.get_permalink($post->ID).'">'.get_the_title($post->ID).'  </a><br>';
           //$Marker_Txt .= get_the_excerpt($post->ID);  <= not supported anymore ....
           /** picture of article
           $Marker_Txt .= get_the_post_thumbnail( $post->ID , 'medium');
           removed since it produce some empty maps at some pages - to be analysed */


           $MarkerArray[] = array('lat'=> $temp_lat,'lon'=>$temp_lon,'popup_height'=>'100', 'popup_width'=>'150', 'marker'=>$PostMarker, 'text'=>$Marker_Txt, 'Marker'=>$PostMarker);
         }
         $post = $post_org;
         Osm::traceText(DEBUG_INFO, "[OL3_createMarkerList]: Found Marker ".count($MarkerArray));
         return $MarkerArray;
       }
       else{
         Osm::traceText(DEBUG_ERROR, "[OL3_createMarkerList]: No posts/pages with geotag found! ");
         $Text = "CustomField: ".$CustomFieldName;
         Osm::traceText(DEBUG_ERROR, $Text);
         $Text = "PostType: ".$post_type;
         Osm::traceText(DEBUG_ERROR, $Text);
         $Text = "Tagged_Filter: ".$a_import_osm_cat_incl_name;
         Osm::traceText(DEBUG_ERROR, $Text);
       }
     }
     /**++++++++++++++++++++++++++++++++++++++++++
     + a single postmarker                       +
     +++++++++++++++++++++++++++++++++++++++++++*/
     else if (($a_import > 0) && ($a_import < 10)){
       $PostMarkerCFN_LatLon = OSM::getPostMarkerCFN($a_import,"LatLon");
       $PostMarkerCFN_Icon_Name = OSM::getPostMarkerCFN($a_import,"Icon");
       $PostMarkerCFN_Name = OSM::getPostMarkerCFN($a_import,"Name");
       $PostMarkerCFN_Text = OSM::getPostMarkerCFN($a_import,"Text");

       $metapostLatLon = get_post_meta($post->ID, $PostMarkerCFN_LatLon, true);
       $metapostIcon_name = get_post_meta($post->ID, $PostMarkerCFN_Icon_Name, true);
       $metapostmarker_name = get_post_meta($post->ID, $PostMarkerCFN_Name, true);
       $metapostmarker_text = get_post_meta($post->ID, $PostMarkerCFN_Text, true);

       // check lat lon
       $metapostLatLon = preg_replace('/\s*,\s*/', ',',$metapostLatLon);
       // get pairs of coordination
       $GeoData_Array = explode( ' ', $metapostLatLon );
       list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]);
       $DoPopUp = 'false';

       list($temp_lat, $temp_lon) = Osm::checkLatLongRange('Marker',$temp_lat, $temp_lon,'no');
       if (($temp_lat != 0) || ($temp_lon != 0)){
         $PostMarker = $metapostIcon_name;
         $PostMarker = Osm_icon::replaceOldIcon($PostMarker);

	     $Marker_Txt = $metapostmarker_text.'  </a><br />';
         $MarkerArray[] = array('lat'=> $temp_lat,'lon'=>$temp_lon,'popup_height'=>'100', 'popup_width'=>'150', 'marker'=>$PostMarker, 'text'=>$Marker_Txt, 'Marker'=>$PostMarker);
       }
       $post = $post_org;
       return $MarkerArray;
     }

     /**++++++++++++++++++++++++++++++++++++++++++
     + all postmarkers                           +
     +++++++++++++++++++++++++++++++++++++++++++*/
     else if ($a_import == "all"){
     for ($Counter = 1; $Counter < 10; $Counter++){
       $PostMarkerCFN_LatLon = OSM::getPostMarkerCFN($Counter,"LatLon");
       $PostMarkerCFN_Icon_Name = OSM::getPostMarkerCFN($Counter,"Icon");
       $PostMarkerCFN_Name = OSM::getPostMarkerCFN($Counter,"Name");
       $PostMarkerCFN_Text = OSM::getPostMarkerCFN($Counter,"Text");

       $metapostLatLon = get_post_meta($post->ID, $PostMarkerCFN_LatLon, true);
       $metapostIcon_name = get_post_meta($post->ID, $PostMarkerCFN_Icon_Name, true);
       $metapostmarker_name = get_post_meta($post->ID, $PostMarkerCFN_Name, true);
       $metapostmarker_text = get_post_meta($post->ID, $PostMarkerCFN_Text, true);

       // check lat lon
       $metapostLatLon = preg_replace('/\s*,\s*/', ',',$metapostLatLon);
       // get pairs of coordination
       $GeoData_Array = explode( ' ', $metapostLatLon );
       list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]);
       $DoPopUp = 'false';

       list($temp_lat, $temp_lon) = Osm::checkLatLongRange('Marker',$temp_lat, $temp_lon,'no');
       if (($temp_lat != 0) || ($temp_lon != 0)){
         $PostMarker = $metapostIcon_name;
         $PostMarker = Osm_icon::replaceOldIcon($PostMarker);
         $Marker_Txt = $metapostmarker_text.'  </a><br>';
         $MarkerArray[] = array('lat'=> $temp_lat,'lon'=>$temp_lon,'popup_height'=>'100', 'popup_width'=>'150', 'marker'=>$PostMarker, 'text'=>$Marker_Txt, 'Marker'=>$PostMarker);
       }
     }
     $post = $post_org;
     return $MarkerArray;
   }
}

  function createMarkerList($a_import, $a_import_UserName, $a_Customfield, $a_import_osm_cat_incl_name,  $a_import_osm_cat_excl_name, $a_post_type, $a_import_osm_custom_tax_incl_name, $a_custom_taxonomy)
  {
     Osm::traceText(DEBUG_INFO, "createMarkerList(".$a_import.",".$a_import_UserName.",".$a_Customfield.")");
     global $post;
     $post_org = $post;

     // make a dummymarker to you use icon.clone later
     if ($a_import == 'gcstats'){
       Osm::traceText(DEBUG_INFO, "Requesting data from gcStats-plugin");
       include('osm-import.php');
     }
     else if ($a_import == 'ecf'){
       Osm::traceText(DEBUG_INFO, "Requesting data from comments");
       include('osm-import.php');
     }
     else if ($a_import == 'osm' || $a_import == 'osm_l'){
       // let's see which posts are using our geo data ...
       Osm::traceText(DEBUG_INFO, "check all posts for osm geo custom fields");
       $CustomFieldName = get_option('osm_custom_field','OSM_geo_data');
       $recentPosts = new WP_Query();
       $recentPosts->query('meta_key='.$CustomFieldName.'&post_status=publish'.'&showposts=-1'.'&post_type='.$a_post_type.'');
       while ($recentPosts->have_posts()) : $recentPosts->the_post();
         $Data = get_post_meta($post->ID, $CustomFieldName, true);
         // remove space before and after comma
         $Data = preg_replace('/\s*,\s*/', ',',$Data);
         // get pairs of coordination
         $GeoData_Array = explode( ' ', $Data );
  	 list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]);
         //echo $post->ID.'Lat: '.$temp_lat.'Long '.$temp_lon.'<br>';
         // check if a filter is set and geodata are set
         // if filter is set and set then pretend there are no geodata
       if (($a_import_osm_cat_incl_name  != 'Osm_All' || $a_import_osm_cat_excl_name  != 'Osm_None' || $a_import_osm_custom_tax_incl_name != 'Osm_All')&&($temp_lat != '' && $temp_lon != '')){
         $categories = wp_get_post_categories($post->ID);
         foreach( $categories as $catid ) {
	       $cat = get_category($catid);
           if (($a_import_osm_cat_incl_name  != 'Osm_All') && (strtolower($cat->name) != (strtolower($a_import_osm_cat_incl_name)))){
             $temp_lat = '';
             $temp_lon = '';
            }
            if (strtolower($cat->name) == (strtolower($a_import_osm_cat_excl_name))){
              $temp_lat = '';
              $temp_lon = '';
            }
         }
         if ($a_import_osm_custom_tax_incl_name != 'Osm_All'){ // get rid of  Invalid argument supplied for foreach()
           $mycustomcategories = get_the_terms( $post->ID, $a_import_osm_custom_tax_incl_name);
           foreach( $mycustomcategories as $term ) {
             $taxonomies[0] = $term->term_taxonomy_id;
             // Get rid of the other data stored in the object
             unset($term);
           }
           foreach( $taxonomies as $taxid ) {
             $termsObjects = wp_get_object_terms($post->ID, $a_custom_taxonomy);
             foreach ($termsObjects as $termsObject) {
               $currentCustomCat[] = $termsObject->name;
             }
             if (($a_import_osm_custom_tax_incl_name  != 'Osm_All') &&  ! in_array($a_import_osm_custom_tax_incl_name, $currentCustomCat)) {
               $temp_lat = '';
               $temp_lon = '';
             }
             if (strtolower($currentCustomCat) == (strtolower($a_import_osm_cat_excl_name))){
               $temp_lat = '';
               $temp_lon = '';
             }
           }
         }
       }
       if ($temp_lat != '' && $temp_lon != '') {
         // how many tags do we have in this post?
         $NumOfGeoTagsInPost = count($GeoData_Array);
         $PostMarker = get_post_meta($post->ID, 'OSM_geo_icon', true);
         $PostMarker = Osm_icon::replaceOldIcon($PostMarker);
         for ($TagNum = 0; $TagNum < $NumOfGeoTagsInPost; $TagNum++){
           list($tag_lat, $tag_lon) = explode(',', $GeoData_Array[$TagNum]);
           list($tag_lat, $tag_lon) = Osm::checkLatLongRange('$marker_all_posts',$tag_lat, $tag_lon);
           //if ($a_import == 'osm_l' ){
           $categories = wp_get_post_categories($post->ID);
	   // take the last one but ignore those without a specific category
           foreach( $categories as $catid ) {
	     $cat = get_category($catid);
             if ((strtolower($cat->name) == 'uncategorized') || (strtolower($cat->name) == 'allgemein')){
               $Category_Txt = '';
             }
             else{
               $Category_Txt = $cat->name.': ';
             }
           }
           $Marker_Txt = '<a href="'.get_permalink($post->ID).'">'.$Category_Txt.get_the_title($post->ID).'  </a><br>';
           if ($a_import == 'osm_l' ){
             $Marker_Txt .= get_the_excerpt($post->ID);
           }
           $MarkerArray[] = array('lat'=> $tag_lat,'lon'=>$tag_lon,'popup_height'=>'100', 'popup_width'=>'150', 'marker'=>$PostMarker, 'text'=>$Marker_Txt, 'Marker'=>$PostMarker);
           //}
           //else{ // plain osm without link to the post
           //  $Marker_Txt = ' ';
           //  $MarkerArray[] = array('lat'=> $tag_lat,'lon'=>$tag_lon,'popup_height'=>'100', 'popup_width'=>'150', 'marker'=>$Icon["name"], 'text'=>$Marker_Txt, 'Marker'=>$PostMarker);
           //}
         }
       }
       Osm::traceText(DEBUG_INFO, "Found Marker ".count($MarkerArray));
       endwhile;
     }
     else if ($a_import == 'wpgmg'){
       // let's see which posts are using our geo data ...
       Osm::traceText(DEBUG_INFO, "check all posts for wpgmg geo custom fields");
       $recentPosts = new WP_Query();
       $recentPosts->query('meta_key='.WPGMG_LAT.'&meta_key='.WPGMG_LON.'&showposts=-1');
       while ($recentPosts->have_posts()) : $recentPosts->the_post();
         include('osm-import.php');
         if ($temp_lat != '' && $temp_lon != '') {
           list($temp_lat, $temp_lon) = $this->checkLatLongRange('$marker_all_posts',$temp_lat, $temp_lon);
           $MarkerArray[] = array('lat'=> $temp_lat,'lon'=>$temp_lon,'marker'=>$Icon["name"],'popup_height'=>'100', 'popup_width'=>'200');
         }
       endwhile;
     }
     else if ($a_import == 'exif_m'){
       $attachments = get_children( array(
         'post_parent'    => get_the_ID(),
         'post_type'      => 'attachment',
         'numberposts'    => -1, // show all -1
         'post_status'    => 'inherit',
         'post_mime_type' => 'image',
         'order'          => 'ASC',
         'orderby'        => 'menu_order ASC'));

       foreach ( $attachments as $attachment_id => $attachment ) {
	 $imagemeta = wp_get_attachment_metadata($attachment_id );

         $img_src   = wp_get_attachment_image_src($attachment_id,'full');
         $img_thmb   = wp_get_attachment_image_src($attachment_id,'thumbnail');
         $Popup_width  = $img_thmb[1]+20;
         $Popup_height = $img_thmb[2]+10;

         $file      = $img_src[0];
         if (is_callable('exif_read_data')) {
           $exif = @exif_read_data($file);
           if (!empty($exif['GPSLatitude'])) {
             $lat = OSM::getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
           }
           if (!empty($exif['GPSLongitude'])) {
             $lon = OSM::getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
           }
         }
         $Marker_Txt = wp_get_attachment_image( $attachment_id );
         $MarkerArray[] = array('lat'=> $lat,'lon'=>$lon,'popup_height'=>$Popup_height, 'popup_width'=>$Popup_width, 'marker'=>$Icon["name"], 'text'=>$Marker_Txt, 'Marker'=>$PostMarker);
       }
     }
     $post = $post_org;
     return $MarkerArray;
  }

  // if you miss a colour, just add it
 public static  function checkStyleColour($a_colour){
    if ($a_colour != 'red' && $a_colour != 'blue' && $a_colour != 'black' && $a_colour != 'green' && $a_colour != 'orange'){
      return "blue";
    }
    return $a_colour;
  }

  // get the layer for the markers
  static function getImportLayer($a_type, $a_UserName, $Icon, $a_osm_cat_incl_name, $a_osm_cat_excl_name, $a_line_color, $a_line_width, $a_line_opacity, $a_post_type, $a_import_osm_custom_tax_incl_name, $a_custom_taxonomy, $a_MapName){

    if ($a_type  == 'osm_l'){
      $LayerName = 'TaggedPosts';
      if ($Icon["name"] != 'NoName'){ // <= ToDo
        $PopUp = 'true';
      }
      else {
        $PopUp = 'false';
      }
    }

    // import data from tagged posts
    else if ($a_type  == 'osm'){
      $LayerName = 'TaggedPosts';
      $PopUp = 'true';
    }

    // import data from wpgmg
    else if ($a_type  == 'wpgmg'){
      $LayerName = 'TaggedPosts';
      $PopUp = 'false';
    }
    // import data from gcstats
    else if ($a_type == 'gcstats'){
      $LayerName     = 'GeoCaches';
      $PopUp = 'true';
      $Icon = Osm_icon::getIconsize(GCSTATS_MARKER_PNG);
      $Icon["name"] = GCSTATS_MARKER_PNG;
    }
    // import data from ecf
    else if ($a_type == 'ecf'){
      $LayerName = 'Comments';
      $PopUp = 'true';
      $Icon = Osm_icon::getIconsize(INDIV_MARKER);
      $Icon["name"] = INDIV_MARKER;
    }
    // import data from ecf
    else if ($a_type == 'exif_m'){
      $LayerName = 'Photos';
      $PopUp = 'true';
    }
    else{
      Osm::traceText(DEBUG_ERROR, "e_import_unknwon");
    }
    $MarkerArray = Osm::createMarkerList($a_type, $a_UserName,'Empty', $a_osm_cat_incl_name,  $a_osm_cat_excl_name, $a_post_type, $a_import_osm_custom_tax_incl_name, $a_custom_taxonomy);
    if ($a_line_color != 'none'){
      $line_color = Osm::checkStyleColour($a_line_color);
      $txt = Osm_OpenLayers::addLines($MarkerArray, $line_color, $a_line_width, $a_MapName);
    }
    $txt .= Osm_OpenLayers::addMarkerListLayer($a_MapName, $Icon, $MarkerArray, $PopUp);
    return $txt;
  }

 // check Lat and Long
  public static function getMapCenter($a_Lat, $a_Long, $a_import, $a_import_UserName){
    if ($a_import == 'wpgmg'){
      $Lat  = OSM_getCoordinateLat($a_import);
      $Lon = OSM_getCoordinateLong($a_import);
    }
    else if ($a_import == 'gcstats'){
      if (function_exists('gcStats__getInterfaceVersion')) {
        $Val = gcStats__getMinMaxLat($a_import_UserName);
        $Lat = ($Val["min"] + $Val["max"]) / 2;
        $Val = gcStats__getMinMaxLon($a_import_UserName);
        $Lon = ($Val["min"] + $Val["max"]) / 2;
      }
      else{
       Osm::traceText(DEBUG_WARNING, "getMapCenter() could not connect to gcStats plugin");
       $Lat  = 0;$Long = 0;
      }
    }
    else if ($a_Lat == '' || $a_Long == ''){
      $Lat = OSM_getCoordinateLat('osm');
      $Lon = OSM_getCoordinateLong('osm');
    }
    else {
      $Lat = $a_Lat;
      $Lon = $a_Long;
    }
    return array($Lat,$Lon);
}


  // check Lat and Long
  public static function getCustomFieldData()
  {
    Osm::traceText(DEBUG_INFO, "getCustomFieldData");
    //todo
    return array($MarkerName, $FirstLat,$FirstLong);
  }


  // check Lat and Long
  public static function checkLatLongRange($a_CallingId, $a_Lat, $a_Long, $a_traceError = "yes")
  {
    Osm::traceText(DEBUG_INFO, "checkLatLongRange(".$a_CallingId.",".$a_Lat.",".$a_Long.",".$a_traceError.")");
    if ($a_Lat >= LAT_MIN && $a_Lat <= LAT_MAX && $a_Long >= LON_MIN && $a_Long <= LON_MAX &&
                    preg_match('!^[^0-9]+$!', $a_Lat) != 1 && preg_match('!^[^0-9]+$!', $a_Long) != 1){
     // all is fine
    }
    else{
      if ($a_traceError == "yes"){
        Osm::traceText(DEBUG_ERROR, "e_lat_lon_range");
        Osm::traceText(DEBUG_INFO, "Error: ".$a_CallingId." Lat".$a_Lat." or Long".$a_Long);
      }
      $a_Lat  = 0;
      $a_Long = 0;
    }
    return array($a_Lat,$a_Long);
  }

  public static function getGPXName($filepath){
    $file = basename($filepath, ".gpx"); // $file is set to "index"
    return $file;
  }
  // shortcode for map with OpenLayers 3
  public static function sc_OL3JS($atts) {
    static  $MapCounter = 0;
    include('osm_map_v3/osm-sc-osm_map_v3.php');
    return $output;
  }
  // shortcode for map with OpenLayers 2
  public static function sc_showMap($atts) {
    static  $MapCounter = 0;
    include('osm_map/osm-sc-osm_map.php');
    return $output;
  }

  // shortcode for image OpenLayers 2
  function sc_showImage($atts) {
    include('osm-sc-osm_image.php');
    return $output;
  }

  // shortcode for image OpenLayers 2
  function sc_info($atts) {
    include('osm-sc-info.php');
    return $output;
  }


 // add OSM-config page to Settings
  function admin_menu($not_used){
  // place the info in the plugin settings page
    add_options_page(__('OpenStreetMap Manager', 'Osm'), __('OSM', 'Osm'), 'manage_options', basename(__FILE__), array('Osm', 'options_page_osm'));
  }

  // ask WP to handle the loading of scripts
  // if it is not admin area
  function show_enqueue_script() {
    wp_enqueue_script(array ('jquery'));

	if (Osm_LoadLibraryMode == SERVER_EMBEDDED){
      // it is loaded when the map is displayed
	}
	elseif (Osm_LoadLibraryMode == SERVER_WP_ENQUEUE){
      //wp_enqueue_script('OlScript', 'http://www.openlayers.org/api/OpenLayers.js');
      //wp_enqueue_script('OsnScript', 'http://www.openstreetmap.org/openlayers/OpenStreetMap.js');
	  wp_enqueue_script('OlScript',Osm_OL_LibraryLocation);
      wp_enqueue_script('OsnScript',Osm_OSM_LibraryLocation);
      wp_enqueue_script('OsnScript',Osm_GOOGLE_LibraryLocation);
      wp_enqueue_script('OsmScript',OSM_PLUGIN_JS_URL.'osm-plugin-lib.js');
      define ('OSM_LIBS_LOADED', 1);
      define ('OL_LIBS_LOADED', 1);
      define ('GOOGLE_LIBS_LOADED', 1);
	}
	 else{
	  // Errormsg is traced at another place
	}
  }
}	// End class Osm

include('osm-widget.php');


// register OSM_Widget widget
function register_osm_widget() {
    register_widget( 'OSM_Tagged_Widget' );
}



$pOsm = new Osm();

function OSM_isGeotagged(){
  global $post;
  $temp_lat = 0;
  $temp_lon= 0;
  //$Data = get_post_meta($post->ID, 'OSM_geo_data', true);
  $CustomFieldName = get_option('osm_custom_field','OSM_geo_data');
  $Data = get_post_meta($post->ID, $CustomFieldName, true);
  if (!empty($Data)) {
    $Data = preg_replace('/\s*,\s*/', ',',$Data);
    // get pairs of coordination
    $GeoData_Array = explode( ' ', $Data );
    list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]);
    list($temp_lat, $temp_lon) = Osm::checkLatLongRange('Marker',$temp_lat, $temp_lon,'no');
  }
  if (($temp_lat != 0) || ($temp_lon != 0)){
    return 1;
  }
  else {
    return 0;
  }
}


// This is meant to be the interface used
// in your WP-template

// returns Lat data of the first (!) coordination
function OSM_getCoordinateLat($a_import)
{
  global $post;
  $a_import = strtolower($a_import);
  if ($a_import == 'osm' || $a_import == 'osm_l'){
         $Data = get_post_meta($post->ID, get_option('osm_custom_field','OSM_geo_data'), true);
         // remove space before and after comma
         $Data = preg_replace('/\s*,\s*/', ',',$Data);
         // get pairs of coordination
         $GeoData_Array = explode( ' ', $Data );
  	 list($lat, $lon) = explode(',', $GeoData_Array[0]);
	//list($lat, $lon) = explode(',', get_post_meta($post->ID, get_option('osm_custom_field','OSM_geo_data'), true));
  }
  else if ($a_import == 'wpgmg'){
	$lat = get_post_meta($post->ID, WPGMG_LAT, true);
  }
  else {
    Osm::traceText(DEBUG_ERROR, "e_php_getlat_missing_arg");
    $lat = 0;
  }
  if ($lat != '') {
    return trim($lat);
  }
  return '';
}

// returns Lon data
function OSM_getCoordinateLong($a_import)
{
	global $post;

  $a_import = strtolower($a_import);
  if ($a_import == 'osm' || $a_import == 'osm_l'){
    $Data = get_post_meta($post->ID, get_option('osm_custom_field','OSM_geo_data'), true);
    // remove space before and after comma
    $Data = preg_replace('/\s*,\s*/', ',',$Data);
    // get pairs of coordination
    $GeoData_Array = explode( ' ', $Data );
    list($lat, $lon) = explode(',', $GeoData_Array[0]);
    //list($lat, $lon) = explode(',', get_post_meta($post->ID, get_option('osm_custom_field','OSM_geo_data'), true));
  }
  else if ($a_import == 'wpgmg'){
    list($lon) = get_post_meta($post->ID,WPGMG_LON, true);
  }
  else {
    Osm::traceText(DEBUG_ERROR, "e_php_getlon_missing_arg");
    $lon = 0;
  }
  if ($lon != '') {
	  return trim($lon);
  }
  return '';
}

function OSM_getOpenStreetMapUrl() {
  $zoom_level = get_option('osm_zoom_level','7');
  $lat = $lat == ''? OSM_getCoordinateLat('osm') : $lat;
  $lon = $lon == ''? OSM_getCoordinateLong('osm'): $lon;
  return URL_INDEX.URL_LAT.$lat.URL_LON.$lon.URL_ZOOM_01.$zoom_level.URL_ZOOM_02;
}

function OSM_echoOpenStreetMapUrl(){
  echo OSM_getOpenStreetMapUrl() ;
}
// functions to display a map in your theme
// by using the custom fields
// default values should be set only at sc_showMap()
function OSM_displayOpenStreetMap($a_widht, $a_hight, $a_zoom, $a_type){

  $atts = array ('width'        => $a_widht,
                 'height'       => $a_hight,
                 'type'         => $a_type,
                 'zoom'         => $a_zoom,
	               'control'		  => 'off');

  if ((OSM_getCoordinateLong("osm"))&&(OSM_getCoordinateLat("osm"))) {
    echo OSM::sc_showMap($atts);
  }
}

function OSM_displayOpenStreetMapExt($a_widht, $a_hight, $a_zoom, $a_type, $a_control, $a_marker_name, $a_marker_height, $a_marker_width, $a_marker_text, $a_ov_map, $a_marker_focus = 0, $a_routing = 'No', $a_theme = 'dark'){

  $atts = array ('width'          => $a_widht,
                 'height'         => $a_hight,
                 'type'           => $a_type,
                 'zoom'           => $a_zoom,
                 'ov_map'         => $a_ov_map,
                 'marker_name'    => $a_marker_name,
                 'marker_height'  => $a_marker_height,
                 'marker_width'   => $a_marker_width,
                 'marker'         => OSM_getCoordinateLat("osm") . ',' . OSM_getCoordinateLong("osm") . ',' . $a_marker_text,
	         'control'        => $a_control,
                 'marker_focus'   => $a_marker_focus,
                 'theme'          => $a_theme,
                 'marker_routing' => $a_routing);

  if ((OSM_getCoordinateLong("osm"))&&(OSM_getCoordinateLat("osm"))) {
    echo OSM::sc_showMap($atts);
  }
}
?>
