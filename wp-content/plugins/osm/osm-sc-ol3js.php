<?php
/*  (c) Copyright 2014  MiKa (wp-osm-plugin.HanBlog.Net)

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
    extract(shortcode_atts(array(
    // size of the map
    'width'      => '100%', 
    'height'     => '300',
    'map_center' => '58.213, 6.378',
    'zoom'       => '4',
    'file_list'  => 'NoFile',
    'file_color_list'  => 'NoColor',
    'type'       => 'Osm',
    'jsname'     => 'dummy',
    'marker_latlon'  => 'No',
    'map_border'  => '2px solid grey',
    'marker_name' => 'NoName',
    'control' => 'No',
    'wms_type' => 'wms_type',
    'wms_address' => 'wms_address',
    'wms_param' => 'wms_param',
    'wms_attr_name' => 'wms_attr_name',
    'wms_attr_url' => 'wms_attr_url',
    'tagged_type' => 'no'
    ), $atts));

    $type = strtolower($type);

    $map_center = preg_replace('/\s*,\s*/', ',',$map_center);
    // get pairs of coordination
    $map_center_Array = explode( ' ', $map_center );
    list($lat, $lon) = explode(',', $map_center_Array[0]); 

    $array_control = explode( ',', $control);
    $array_control    = Osm_OLJS3::checkControlType($array_control);

    $pos = strpos($width, "%");
    if ($pos == false) {
      if ($width < 1){
        Osm::traceText(DEBUG_ERROR, "e_map_size");
        Osm::traceText(DEBUG_INFO, "Error: ($width: ".$width.")!");
        $width = 450;
      }
      $width_str = $width."px"; // make it 30px
    } else {// it's 30%
      $width_perc = substr($width, 0, $pos ); // make it 30 
      if (($width_perc < 1) || ($width_perc >100)){
        Osm::traceText(DEBUG_ERROR, "e_map_size");
        Osm::traceText(DEBUG_INFO, "Error: ($width: ".$width.")!");
        $width = "100%";
      }
      $width_str = substr($width, 0, $pos+1 ); // make it 30% 
    }

    $pos = strpos($height, "%");
    if ($pos == false) {
      if ($height < 1){
        Osm::traceText(DEBUG_ERROR, "e_map_size");
        Osm::traceText(DEBUG_INFO, "Error: ($height: ".$height.")!");
        $height = 300;
      }
      $height_str = $height."px"; // make it 30px
    } else {// it's 30%
      $height_perc = substr($height, 0, $pos ); // make it 30 
      if (($height_perc < 1) || ($height_perc >100)){
        Osm::traceText(DEBUG_ERROR, "e_map_size");
        Osm::traceText(DEBUG_INFO, "Error: ($height: ".$height.")!");
        $height = "100%";
      }
      $height_str = substr($height, 0, $pos+1 ); // make it 30% 
    }

    $marker_name = Osm_icon::replaceOldIcon($marker_name);

    $MapCounter += 1;
    $MapName = 'map_ol3js_'.$MapCounter;

    $output = '
    <div class="row-fluid">
      <div class="span12">
        <div id="'.$MapName.'" class="OSM_Map" style="width:'.$width_str.'; height:'.$height_str.'; overflow:hidden;border:'.$map_border.';">
          <div id="'.$MapName.'_popup" class="ol-popup" >
            <a href="#" id="'.$MapName.'_popup-closer" class="ol-popup-closer"></a>
            <div id="'.$MapName.'_popup-content"></div>
          </div>
        </div>
      </div>
    </div>
    ';
    
    if(!defined('OL3_LIBS_LOADED')) {
      define ('OL3_LIBS_LOADED', 1);
      $output .= '
        <link rel="stylesheet" href="'.Osm_OL_3_CSS.'" type="text/css"> 
        <link rel="stylesheet" href="'.Osm_OL_3_Ext_CSS.'" type="text/css"> 
        <script src="'.Osm_OL_3_LibraryLocation.'" type="text/javascript"></script> 
        <script src="'.Osm_OL_3_Ext_LibraryLocation.'" type="text/javascript"></script>
      ';
    }
 
    $output .= '<script type="text/javascript">'; 
    $output .= '/* <![CDATA[ */';
    $output .= '(function($) {';

    $ov_map = "ov_map";
    $theme = "theme";
    $output .= Osm_OLJS3::addTileLayer($MapName, $type, $ov_map, $array_control, $wms_type, $wms_attr_name, $wms_attr_url, $wms_address, $wms_param, $theme);

    if ($type == "openseamap"){
      $output .= '
      var '.$MapName.' = new ol.Map({
        layers: [raster, Layer2],
        interactions: ol.interaction.defaults({mouseWheelZoom:false}),
        target: "'.$MapName.'",
        view: new ol.View({
          center: ol.proj.transform(['.$lon.','.$lat.'], "EPSG:4326", "EPSG:3857"),
          zoom: '.$zoom.'
        })
      });';
    }
    else if ($type == "basemap_at"){
      $output .= '
     var '.$MapName.' = new ol.Map({
     layers: [
       new ol.layer.Tile({
         extent: [977844.377599999, 5837774.6617, 1915609.8654, 6295560.8122],
         source: source_basemap
       })
     ],
     interactions: ol.interaction.defaults({mouseWheelZoom:false}),
     target: "'.$MapName.'",
     view: new ol.View({
     center: ol.proj.transform(['.$lon.','.$lat.'], "EPSG:4326", "EPSG:3857"),
     zoom: '.$zoom.'
   })
    });';
    }
    else{
      $output .= '
      var '.$MapName.' = new ol.Map({';


      $output .= '
controls: Controls,
';

$output .= '
        interactions: ol.interaction.defaults({mouseWheelZoom:false}),
        layers: [raster],
        target: "'.$MapName.'",
        view: new ol.View({
          center: ol.proj.transform(['.$lon.','.$lat.'], "EPSG:4326", "EPSG:3857"),
          zoom: '.$zoom.'
        })
      });
      ';
    }

    if ($file_list != "NoFile"){
      $FileListArray   = explode( ',', $file_list ); 
      $FileColorListArray = explode( ',', $file_color_list);
      $this->traceText(DEBUG_INFO, "(NumOfFiles: ".sizeof($FileListArray)." NumOfColours: ".sizeof($FileColorListArray).")!");
      if ((sizeof($FileColorListArray) > 0) && (sizeof($FileColorListArray) != sizeof($FileListArray))){
        $this->traceText(DEBUG_ERROR, "e_gpx_list_error");
      }
      else{
        for($x=0;$x<sizeof($FileListArray);$x++){
          $temp = explode(".",$FileListArray[$x]);
	      $FileType = strtolower($temp[(count($temp)-1)]);
	      if (($FileType == "gpx")||($FileType == "kml")){
	        if (sizeof($FileColorListArray) == 0){$Color = "blue";}
	        else {$Color = $FileColorListArray[$x];}
	        $gpx_marker_name = "mic_blue_pinother_02.png";
	        if ($Color == "blue"){$gpx_marker_name = "mic_blue_pinother_02.png";}
            else if ($Color == "red"){$gpx_marker_name = "mic_red_pinother_02.png";}
            else if ($Color == "green"){$gpx_marker_name = "mic_green_pinother_02.png";}
            else if ($Color == "black"){$gpx_marker_name = "mic_black_pinother_02.png";}
            $output .= Osm_OLJS3::addVectorLayer($MapName, $FileListArray[$x], $Color, $FileType, $x, $gpx_marker_name);
          }
          else {
            $this->traceText(DEBUG_ERROR, "e_gpx_type_error");
          }
        }
        //$output .= 'osm_addPopupClickhandler('.$MapName.',  "'.$MapName.'"); ';
	  }
    } // $file_list != "NoFile"


  if (($tagged_type == "post") || ($tagged_type == "page") || ($tagged_type == "any")){
    $marker_name = Osm_icon::replaceOldIcon($marker_name);
      if (Osm_icon::isOsmIcon($marker_name) == 1){
        $Icon = Osm_icon::getIconsize($marker_name);
        $Icon["name"]  = $marker_name;
      }
      else { // if no marker is set for the post
        $this->traceText(DEBUG_ERROR, "e_not_osm_icon");
        $this->traceText(DEBUG_ERROR, $marker_name);
        $Icon = Osm_icon::getIconsize($marker_name);
        $Icon["name"]  = $marker_name;
      }      
    $Icon_tmp = Osm_icon::getIconsize($Icon["name"] );

    $MarkerArray = OSM::OL3_createMarkerList('osm_l', 'Osm_All', 'Osm_None', $tagged_type, 'Osm_All', 'none');

    $NumOfMarker = count($MarkerArray);
    $Counter = 0;
    foreach( $MarkerArray as $Marker ) {

      if ($MarkerArray[$Counter][Marker] != ""){
        $IconURL = OSM_PLUGIN_ICONS_URL.$MarkerArray[$Counter][Marker];
        $MarkerText = addslashes($MarkerArray[$Counter][text]);
        if (Osm_icon::isOsmIcon($MarkerArray[$Counter][Marker]) == 1){
          $Icon_tmp = Osm_icon::getIconsize($MarkerArray[$Counter][Marker]);
        }
        else {
          $Icon_tmp = Osm_icon::getIconsize($Icon["name"] );
          // set it do invidual marker
          $this->traceText(DEBUG_INFO, "e_not_osm_icon");
          //$this->traceText(DEBUG_INFO, $MarkerArray[$Counter][Marker]);
        }
      }
      else {
        $MarkerText = addslashes($MarkerArray[$Counter][text]);
        $IconURL = OSM_PLUGIN_ICONS_URL.$Icon[name];
        $Icon_tmp = Osm_icon::getIconsize($Icon["name"] );
      } 

     $output .= '
		var iconStyle'.$Counter.' = new ol.style.Style({
		  image: new ol.style.Icon(/** @type {olx.style.IconOptions} */({
		    anchor: [('.$Icon_tmp[offset_width].'*-1),('.$Icon_tmp[offset_height].'*-1)],
		    anchorXUnits: "pixels",
		    anchorYUnits: "pixels",
		    opacity: 0.9,
		    src: "'.$IconURL.'"
		  }))
		});
        var iconFeature'.$Counter.' = new ol.Feature({
          geometry: new ol.geom.Point(
	      ol.proj.transform(['.$MarkerArray[$Counter][lon].','.$MarkerArray[$Counter][lat].'], "EPSG:4326", "EPSG:3857")),
          name: "'.$MarkerText.'"
        });
		iconFeature'.$Counter.'.setStyle(iconStyle'.$Counter.');
        if ('.$Counter.' == 0){
          var vectorMarkerSource = new ol.source.Vector({});
		  var vectorMarkerLayer = new ol.layer.Vector({
		    source: vectorMarkerSource
		  });
        }
        vectorMarkerSource.addFeature(iconFeature'.$Counter.');
       ';
       $Counter = $Counter +1;
    } // foreach(MarkerArray)

    $output .= $MapName.'.addLayer(vectorMarkerLayer);';

   }

   if (strtolower($marker_latlon) == 'osm_geotag'){ 
      global $post;
      $CustomFieldName = get_option('osm_custom_field','OSM_geo_data');
      $Data = get_post_meta($post->ID, $CustomFieldName, true);  
      $PostMarker = get_post_meta($post->ID, 'OSM_geo_icon', true);
      if ($PostMarker == ""){
        $PostMarker = $marker_name;
      }

      $Data = preg_replace('/\s*,\s*/', ',',$Data);
      // get pairs of coordination
      $GeoData_Array = explode( ' ', $Data );
      list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]); 
      $DoPopUp = 'false';

      $PostMarker = Osm_icon::replaceOldIcon($PostMarker);
      if (Osm_icon::isOsmIcon($PostMarker) == 1){
        $Icon = Osm_icon::getIconsize($PostMarker);
        $Icon["name"]  = $PostMarker;
      }
      else { // if no marker is set for the post
        $this->traceText(DEBUG_ERROR, "e_not_osm_icon");
        $this->traceText(DEBUG_ERROR, $PostMarker);
        $Icon = Osm_icon::getIconsize($PostMarker);
        $Icon["name"]  = $marker_name;
      }

     $MarkerUrl = OSM_PLUGIN_ICONS_URL.$Icon["name"];
      list($temp_lat, $temp_lon) = Osm::checkLatLongRange('Marker',$temp_lat, $temp_lon,'no');
      if (($temp_lat != 0) || ($temp_lon != 0)){
      // set the center of the map to the first geotag
        $output .= $MapName.'.getView().setCenter(ol.proj.transform(['.$temp_lon.','.$temp_lat.'], "EPSG:4326", "EPSG:3857"));';
        
        $MarkerArray[] = array('lat'=> $temp_lat,'lon'=>$temp_lon,'text'=>$temp_popup,'popup_height'=>'150', 'popup_width'=>'150');
        //$output .= 'osm_addMarkerLayer('.$MapName.','.$temp_lon.','.$temp_lat.') ; ';
        $output .= 'osm_addMarkerLayer('.$MapName.','.$temp_lon.','.$temp_lat.',"'.$MarkerUrl.'",'.$Icon["offset_width"].','.$Icon["offset_height"].') ; ';
      }// templat lon != 0
    } //($marker_latlon  == 'OSM_geotag')
    else if (strtolower($marker_latlon) != 'no'){
      $DoPopUp = 'false';

      $marker_name = Osm_icon::replaceOldIcon($marker_name);
      if (Osm_icon::isOsmIcon($marker_name) == 1){
        $Icon = Osm_icon::getIconsize($marker_name);
        $Icon["name"]  = $marker_name;
      }
      else { // if no marker is set for the post
        $this->traceText(DEBUG_ERROR, "e_not_osm_icon");
        $this->traceText(DEBUG_ERROR, $marker_name);
        $Icon = Osm_icon::getIconsize($marker_name);
        $Icon["name"]  = $marker_name;
      }

      $marker_latlon_temp = preg_replace('/\s*,\s*/', ',',$marker_latlon);
      // get pairs of coordination
      $GeoData_Array = explode( ' ', $marker_latlon_temp);
      list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]); 

      list($temp_lat, $temp_lon) = Osm::checkLatLongRange('Marker',$temp_lat, $temp_lon,'no');
      if (($temp_lat != 0) || ($temp_lon != 0)){
        $lat_marker = $temp_lat;
        $lon_marker = $temp_lon;
        $MarkerUrl = OSM_PLUGIN_ICONS_URL.$Icon["name"];
        $MarkerArray[] = array('lat'=> $temp_lat,'lon'=>$temp_lon,'text'=>$temp_popup,'popup_height'=>'150', 'popup_width'=>'150');
        $output .= 'osm_addMarkerLayer('.$MapName.','.$temp_lon.','.$temp_lat.',"'.$MarkerUrl.'",'.$Icon["offset_width"].','.$Icon["offset_height"].') ; ';
      }// templat lon != 0

}
    $output .= 'osm_addPopupClickhandler('.$MapName.',  "'.$MapName.'"); ';
    $output .= '})(jQuery)';
    $output .= '/* ]]> */';
    $output .= ' </script>';

?>
