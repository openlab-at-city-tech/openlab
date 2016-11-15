<?php
/*  (c) Copyright 2014  MiKa (http://wp-osm-plugin.HanBlog.Net)

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
    'map_center' => OSM_default_lat.','.OSM_default_lon,
    'zoom'       => '4',
    'file_list'  => 'NoFile',
    'file_color_list'  => 'NoColor',
    'type'       => 'osm',
    'jsname'     => 'dummy',
    'marker_latlon'  => 'No',
    'map_border'  => '2px solid grey',
    'marker_name' => 'NoName',
    'marker_size' => 'no',
    'post_markers' => 'no',
    'control' => 'No',
    'wms_type' => 'wms_type',
    'wms_address' => 'wms_address',
    'wms_param' => 'wms_param',
    'wms_attr_name' => 'wms_attr_name',
    'wms_attr_url' => 'wms_attr_url',
    'tagged_type' => 'no',
    'tagged_filter' => 'osm_all',
    'tagged_param' => 'no',
    'tagged_color' => 'blue',
    'mwz' => 'false',
    'debug_trc' => 'false',
    'display_marker_name' => 'false'
    ), $atts));

    $sc_args = new cOsm_arguments($width,  $height,  $map_center,  $zoom,  $file_list,  $file_color_list, $type, $jsname, $marker_latlon, $map_border, $marker_name, $marker_size, $control, $wms_address, $wms_param, $wms_attr_name, $wms_type, $wms_attr_url, $tagged_type, $tagged_filter, $mwz,$post_markers, $display_marker_name, $tagged_param, $tagged_color); 
 
    $lat = $sc_args->getMapCenterLat();
    $lon = $sc_args->getMapCenterLon();
    $array_control = $sc_args->getMapControl();
    $width_str = $sc_args->getMapWidth_str();
    $height_str = $sc_args->getMapHeight_str();
    $type =  $sc_args->getMapType();
    $postmarkers = $sc_args->getPostMarkers();

    if ($debug_trc == "true"){
      echo "WP version: ".get_bloginfo(version)."<br>";
      echo "OSM Plugin Version: ".PLUGIN_VER."<br>";
      echo "Plugin URL: ".OSM_PLUGIN_URL."<br>";
      print_r($atts);
      echo "<br>";
      print_r($sc_args);
      echo "<br><br>";
    }

if (($mwz != "true") && ($mwz != "false")){
        $mwz = "false";
        Osm::traceText(DEBUG_ERROR, "Error at argument mwz (true|false)!");
    }

    // if the markersize is set, we expect a private marker
    if ($marker_size == "no"){
      $default_icon = new cOsm_icon($marker_name); 
    }
    else{
      $default_icon = new cOsm_icon($marker_name, $sc_args->getMarkerHeight(), $sc_args->getMarkerWidth(), $sc_args->getMarkerFocus());
    }
       
    $MapCounter += 1;
    $MapName = 'map_ol3js_'.$MapCounter;

    $output = '
        <div id="'.$MapName.'" class="map" style="width:'.$width_str.'; height:'.$height_str.'; overflow:hidden;border:'.$map_border.';">
          <div id="'.$MapName.'_popup" class="ol-popup" >
            <a href="#" id="'.$MapName.'_popup-closer" class="ol-popup-closer"></a>
            <div id="'.$MapName.'_popup-content"></div>
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
        interactions: ol.interaction.defaults({mouseWheelZoom:'.$mwz.'}),
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
     interactions: ol.interaction.defaults({mouseWheelZoom:'.$mwz.'}),
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
        interactions: ol.interaction.defaults({mouseWheelZoom:'.$mwz.'}),
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
      Osm::traceText(DEBUG_INFO, "(NumOfFiles: ".sizeof($FileListArray)." NumOfColours: ".sizeof($FileColorListArray).")!");
      if (($FileColorListArray[0] != "NoColor") && (sizeof($FileColorListArray) != sizeof($FileListArray))){
         Osm::traceText(DEBUG_ERROR, __('file_color_list does not match to file_list!','OSM-plugin'));
      }
      else{
        for($x=0;$x<sizeof($FileListArray);$x++){
          $FileName = explode(".",$FileListArray[$x]);
	      $FileType = strtolower($FileName[(count($FileName)-1)]);
	      if (($FileType == "gpx")||($FileType == "kml")){
            $showMarkerName = "false";
            if ($FileType == "kml"){
              $showMarkerName = $sc_args->showKmlMarkerName();
            }
	        if (sizeof($FileColorListArray) == 0){$Color = "blue";}
	        else {$Color = $FileColorListArray[$x];}
	        $gpx_marker_name = "mic_blue_pinother_02.png";
	        if ($Color == "blue"){$gpx_marker_name = "mic_blue_pinother_02.png";}
            else if ($Color == "red"){$gpx_marker_name = "mic_red_pinother_02.png";}
            else if ($Color == "green"){$gpx_marker_name = "mic_green_pinother_02.png";}
            else if ($Color == "black"){$gpx_marker_name = "mic_black_pinother_02.png";}
            $output .= Osm_OLJS3::addVectorLayer($MapName, $FileListArray[$x], $Color, $FileType, $x, $gpx_marker_name, $showMarkerName);
          }
          else {        
             Osm::traceText(DEBUG_ERROR, (sprintf(__('%s hast got wrong file extension (gpx, kml)!'), $FileName)));
          }
        }
        //$output .= 'osm_addPopupClickhandler('.$MapName.',  "'.$MapName.'"); ';
	  }
    } // $file_list != "NoFile"
  if ((($tagged_type == "post") || ($tagged_type == "page") || ($tagged_type == "any")) && ($tagged_param == "cluster")){
    $tagged_icon = new cOsm_icon($default_icon->getIconName());
    
    $MarkerArray = OSM::OL3_createMarkerList('osm_l', $tagged_filter, 'Osm_None', $tagged_type, 'Osm_All', 'none');

    $NumOfMarker = count($MarkerArray);
    $Counter = 0;
    $output .= '
      var vectorMarkerSource = new ol.source.Vector({});
      
      ';
          foreach( $MarkerArray as $Marker ) {

       $MarkerText = addslashes($MarkerArray[$Counter]['text']);

     $output .= '
        var iconFeature'.$Counter.' = new ol.Feature({
          geometry: new ol.geom.Point(
	      ol.proj.transform(['.$MarkerArray[$Counter]['lon'].','.$MarkerArray[$Counter]['lat'].'], "EPSG:4326", "EPSG:3857")),
          name: "'.$MarkerText.'"
        });
        vectorMarkerSource.addFeature(iconFeature'.$Counter.');
       ';
       $Counter = $Counter +1;
    } // foreach(MarkerArray)
      
          $taggedborderColor = $sc_args->getTaggedBorderColor();
          $taggedinnerColor = $sc_args->getTaggedInnerColor();
          $output .= '

      var clusterSource = new ol.source.Cluster({
          distance: 30,
          source: vectorMarkerSource
       });
       var styleCache = {};
	  var vectorMarkerLayer = new ol.layer.Vector({
        source: clusterSource,
        style: function(feature, resolution) {
    var size = feature.get("features").length;
    var features = feature.get("features");
    
    if (size > 1){
    var style = styleCache[size];
    if (!style) {
      style = [new ol.style.Style({
        image: new ol.style.Circle({
          radius: 12,
          stroke: new ol.style.Stroke({
            color: '.$taggedborderColor.',
            width: 6,
          }),
          fill: new ol.style.Fill({
            color: '.$taggedinnerColor.'
          })
        }),
        text: new ol.style.Text({
          text: size.toString(),
          fill: new ol.style.Fill({
            color: "#fff"
          })
        })
      })];
      styleCache[size] = style;
    }
    return style;
    } 
    else {
          var style = styleCache[size];
    if (!style) {
      style = [new ol.style.Style({
        image: new ol.style.Icon(({
		    anchor: [('.$tagged_icon->getIconOffsetwidth().'*-1),('.$tagged_icon->getIconOffsetheight().'*-1)],
		    anchorXUnits: "pixels",
		    anchorYUnits: "pixels",
		    opacity: 0.9,
		    src: "'.$tagged_icon->getIconURL().'"}))
      })];
      styleCache[size] = style;
    }
    return style;  
    }
  }
       });
    ';
    $output .= $MapName.'.addLayer(vectorMarkerLayer);';
   }


if ((($tagged_type == "post") || ($tagged_type == "page") || ($tagged_type == "any")) && ($tagged_param != "cluster")){
    $tagged_icon = new cOsm_icon($default_icon->getIconName());
    
    $MarkerArray = OSM::OL3_createMarkerList('osm_l', $tagged_filter, 'Osm_None', $tagged_type, 'Osm_All', 'none');

    $NumOfMarker = count($MarkerArray);
    $Counter = 0;
    $output .= '
      var vectorMarkerSource = new ol.source.Vector({});
	  var vectorMarkerLayer = new ol.layer.Vector({
        source: vectorMarkerSource
       });
    ';
    foreach( $MarkerArray as $Marker ) {
      if ($MarkerArray[$Counter]['Marker'] != ""){
        $tagged_icon->setIcon($MarkerArray[$Counter]['Marker']);
      }
      else{
        $tagged_icon->setIcon($default_icon->getIconName());   
      }
 
       $MarkerText = addslashes($MarkerArray[$Counter]['text']);

     $output .= '
		var iconStyle'.$Counter.' = new ol.style.Style({
		  image: new ol.style.Icon(/** @type {olx.style.IconOptions} */({
		    anchor: [('.$tagged_icon->getIconOffsetwidth().'*-1),('.$tagged_icon->getIconOffsetheight().'*-1)],
		    anchorXUnits: "pixels",
		    anchorYUnits: "pixels",
		    opacity: 0.9,
		    src: "'.$tagged_icon->getIconURL().'"
		  }))
		});
        var iconFeature'.$Counter.' = new ol.Feature({
          geometry: new ol.geom.Point(
	      ol.proj.transform(['.$MarkerArray[$Counter]['lon'].','.$MarkerArray[$Counter]['lat'].'], "EPSG:4326", "EPSG:3857")),
          name: "'.$MarkerText.'"
        });
		iconFeature'.$Counter.'.setStyle(iconStyle'.$Counter.');
        vectorMarkerSource.addFeature(iconFeature'.$Counter.');
       ';
       $Counter = $Counter +1;
    } // foreach(MarkerArray)

    $output .= $MapName.'.addLayer(vectorMarkerLayer);';

   }


   $temp_popup = '';
   
   if (strtolower($marker_latlon) == 'osm_geotag'){ 
      global $post;
      $CustomFieldName = get_option('osm_custom_field','OSM_geo_data');
      $Data = get_post_meta($post->ID, $CustomFieldName, true);  
      $metaIcon_name = get_post_meta($post->ID, 'OSM_geo_icon', true);
      $postgeotag_icon = $default_icon;
      if ($metaIcon_name == ""){
          $postgeotag_icon=$default_icon;
      }
      else{
         $postgeotag_icon->setIcon( $metaIcon_name);
      }
      $Data = preg_replace('/\s*,\s*/', ',',$Data);
      // get pairs of coordination
      $GeoData_Array = explode( ' ', $Data );
      list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]); 
      $DoPopUp = 'false';

      list($temp_lat, $temp_lon) = Osm::checkLatLongRange('Marker',$temp_lat, $temp_lon,'no');
      if (($temp_lat != 0) || ($temp_lon != 0)){
      // set the center of the map to the first geotag
      $output .= $MapName.'.getView().setCenter(ol.proj.transform(['.$temp_lon.','.$temp_lat.'], "EPSG:4326", "EPSG:3857"));';
        
      $MarkerArray[] = array('lat'=> $temp_lat,'lon'=>$temp_lon,'text'=>$temp_popup,'popup_height'=>'150', 'popup_width'=>'150');
        //$output .= 'osm_addMarkerLayer('.$MapName.','.$temp_lon.','.$temp_lat.') ; ';
        $output .= 'osm_addMarkerLayer('.$MapName.','.$temp_lon.','.$temp_lat.',"'.$postgeotag_icon->getIconURL().'",'.$postgeotag_icon->getIconOffsetwidth().','.$postgeotag_icon->getIconOffsetheight().',"") ; ';
      }// templat lon != 0
    } //($marker_latlon  == 'OSM_geotag')
    else if (strtolower($marker_latlon) != 'no'){
      $DoPopUp = 'false';
      $marker_latlon_temp = preg_replace('/\s*,\s*/', ',',$marker_latlon);
      // get pairs of coordination
      $GeoData_Array = explode( ' ', $marker_latlon_temp);
      list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]); 

      list($temp_lat, $temp_lon) = Osm::checkLatLongRange('Marker',$temp_lat, $temp_lon,'no');
      if (($temp_lat != 0) || ($temp_lon != 0)){
        $lat_marker = $temp_lat;
        $lon_marker = $temp_lon;
        $MarkerArray[] = array('lat'=> $temp_lat,'lon'=>$temp_lon,'text'=>$temp_popup,'popup_height'=>'150', 'popup_width'=>'150');
        $output .= 'osm_addMarkerLayer('.$MapName.','.$temp_lon.','.$temp_lat.',"'.$default_icon->getIconURL().'",'.$default_icon->getIconOffsetwidth().','.$default_icon->getIconOffsetheight().',"") ; ';
      }// templat lon != 0

}

// add post markers
if (strtolower($postmarkers) != 'no'){ 
      global $post;
      $metapostLatLon = get_post_meta($post->ID, 'OSM_Marker_01_LatLon', true);  
      $metapostIcon_name = get_post_meta($post->ID, 'OSM_Marker_01_Icon', true);
      $metapostmarker_name = get_post_meta($post->ID, 'OSM_Marker_01_Name', true);
      $metapostmarker_text = get_post_meta($post->ID, 'OSM_Marker_01_Text', true);

       if ($metapostIcon_name == ""){
           Osm::traceText(DEBUG_ERROR, __('You have to add a marker to the post at [Add marker] tab!','OSM-plugin'));
       }
       $postmarker_icon = new cOsm_icon($metapostIcon_name); 


     // check lat lon
      $metapostLatLon = preg_replace('/\s*,\s*/', ',',$metapostLatLon);
      // get pairs of coordination
      $GeoData_Array = explode( ' ', $metapostLatLon );
      list($temp_lat, $temp_lon) = explode(',', $GeoData_Array[0]); 
      $DoPopUp = 'false';

      list($temp_lat, $temp_lon) = Osm::checkLatLongRange('Marker',$temp_lat, $temp_lon,'no');
      if (($temp_lat != 0) || ($temp_lon != 0)){
      $output .= 'osm_addMarkerLayer('.$MapName.','.$temp_lon.','.$temp_lat.',"'.$postmarker_icon->getIconURL().'",'.$postmarker_icon->getIconOffsetwidth().','.$postmarker_icon->getIconOffsetheight().',"'.$metapostmarker_text.'") ; ';
      }// templat lon != 0
    } //($postmarkers) != 'no'')
          $output.= '
            var osm_controls = [
                new ol.control.Attribution(),
                new ol.control.MousePosition({
                    undefinedHTML: "outside",
                    projection: "EPSG:4326",
                    coordinateFormat: function(coordinate) {
                        return ol.coordinate.format(coordinate, "{y}, {x}", 4);
                    }
                }),
                new ol.control.OverviewMap({
                    collapsed: false
                }),
                new ol.control.Rotate({
                    autoHide: false
                }),
                new ol.control.ScaleLine(),
                new ol.control.Zoom(),
                new ol.control.ZoomSlider(),
                new ol.control.ZoomToExtent(),
                new ol.control.FullScreen()
            ]; ';
            if ($sc_args->issetFullScreen()){
              $output .= $MapName.'.addControl(osm_controls[8]);';
            }
            if ($sc_args->issetScaleline()){
              $output .= $MapName.'.addControl(osm_controls[4]);';
            }
            if ($sc_args->issetMouseposition()){
              $output .= $MapName.'.addControl(osm_controls[1]);';
            }           
  if ($tagged_param == "cluster"){
          $output .= 'osm_addClusterPopupClickhandler('.$MapName.',  "'.$MapName.'"); ';
  }
  else{
    $output .= 'osm_addPopupClickhandler('.$MapName.',  "'.$MapName.'"); ';
}
    $output .= '})(jQuery)';
    $output .= '/* ]]> */';
    $output .= ' </script>';

?>
