<?php
/*  (c) Copyright 2021  MiKa (http://wp-osm-plugin.hyumika.com)

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
    'map_api_key' => 'NoKey',
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
    'tagged_filter_type' => 'category',
    'tagged_filter' => 'osm_all',
    'tagged_param' => 'no',
    'tagged_color' => 'blue',
    'mwz' => 'false',
    'debug_trc' => 'false',
    'display_marker_name' => 'false',
    'file_title' => 'no',
    'file_link' => 'no',
    'file_param' => 'no',
	 'hide_kml_sel_box' => 'no',
    'setup_zoom' => 'undefined',
    'setup_layer' => 'undefined',
    'setup_center' => 'undefined',
    'setup_trigger' => 'undefined',
    'setup_map_name' => 'undefined',
    'map_event' => 'no',
    'file_select_box' => 'no',
    'bckgrndimg' => 'no',
    'attribution' => 'true',
    'map_div_name' => 'default',
    'map_div_vis' => 'block'
    ), $atts));


    $sc_args = new cOsm_arguments(
    	$width,
    	$height,
    	$map_center,
    	$zoom,
      $map_api_key,
    	$file_list,
    	$file_color_list,
		$type,
		$jsname,
		$marker_latlon,
		$map_border,
		$map_event,
		$marker_name,
		$marker_size,
		$control,
		$wms_address,
		$wms_param,
		$wms_attr_name,
		$wms_type,
		$wms_attr_url,
		$tagged_type,
		$tagged_filter,
      $tagged_filter_type,
		$mwz,
		$post_markers,
		$display_marker_name,
		$tagged_param,
		$tagged_color,
		$file_title,
		$file_link,
		$setup_zoom,
		$setup_layer,
		$setup_center,
		$setup_trigger,
		$setup_map_name,
		$file_select_box,
		$bckgrndimg,
		$attribution
		);

    global $OL3_LIBS_LOADED;
    
    $dontShow = array('&#8243;','&#8220;');

    $lat = str_replace($dontShow, '', $sc_args->getMapCenterLat());
    $lon = str_replace($dontShow, '', $sc_args->getMapCenterLon());

    $zoom = $sc_args->getMapZoom();

    $map_autocenter = $sc_args->isAutocenter();
 
    
    $array_control = $sc_args->getMapControl();
    $width_str = $sc_args->getMapWidth_str();
    $height_str = $sc_args->getMapHeight_str();
    $type =  $sc_args->getMapType();
    $postmarkers = $sc_args->getPostMarkers();
    $api_key = $sc_args->getMapAPIkey();

        
    if ($debug_trc == "true"){
      echo "WP version: ".get_bloginfo(version)."<br>";
      echo "OSM Plugin Version: ".PLUGIN_VER."<br>";
      echo "Plugin URL: ".OSM_PLUGIN_URL."<br>";
      print_r($atts);
      echo "<br>";
      print_r($sc_args);
      echo "<br><br>";
    }

	global $post;

    /** if not all 5 parameters are correctly set, a map instead of the text link will be shown */
   if (($setup_zoom != 'undefined') &&
    	($setup_layer != 'undefined') &&
    	($setup_center != 'undefined') &&
    	($setup_trigger != 'undefined') &&
    	($setup_map_name != 'undefined')) {

    	$output = '<a class="setupChange" data-zoom="' . $setup_zoom .'"  data-center="' . $setup_center .'"  data-layer="' . $setup_layer .'"  data-map_name="' . $setup_map_name .'" title="' .  __('Klick auf diesen Text um die Karte zu beeinflussen', 'OSM_Plugin') .'">' . $setup_trigger . '</a>';

	 } else {

		if (($mwz != "true") && ($mwz != "false")){
				$mwz = "false";
				Osm::traceText(DEBUG_ERROR, "e_mww_error_arg");
		}

			// if the markersize is set, we expect a private marker
		if ($marker_size == "no"){
		  $default_icon = new cOsm_icon($marker_name);
		}
		else{
		  $default_icon = new cOsm_icon($marker_name, $sc_args->getMarkerHeight(), $sc_args->getMarkerWidth(), $sc_args->getMarkerFocus());
		}


      if ($map_div_name == "default"){
        $MapCounter += 1;
        $MapName = 'map_ol3js_' . $MapCounter;
      }
      else {
        $MapName = $map_div_name;
      }

		// $setup_map_name is a class name - to control several maps at once map_name need not to be unique on one page
		if (!($sc_args->isMapAttr())){
		  Osm::traceText(HTML_COMMENT, 'WP OSM Plugin Warning: map attribution is disabled, make sure site follows copyright!');	              
      }		
		
	   $vis_str = $map_div_vis;		
		
if ($bckgrndimg != 'no'){
		$output = '

				<div id="' . $MapName . '" class="map ' . $setup_map_name . '" data-map_name="' . $setup_map_name . '" data-map="' . $MapName . '" style="width:' . $width_str . '; max-width:100%; height:' . $height_str . '; display:' . $vis_str . '; overflow:hidden;border:' . $map_border . '; background-image: url('.OSM_PLUGIN_URL.$bckgrndimg.'); background-repeat: no-repeat; background-position: center; position: relative;" >
				  <div id="' . $MapName . '_popup" class="ol-popup" >
					<a href="#" id="' . $MapName . '_popup-closer" class="ol-popup-closer"></a>
					<div id="' . $MapName . '_popup-content" ></div>
				  </div>
				</div>
			';
}
else{
		$output = '

				<div id="' . $MapName . '" class="map ' . $setup_map_name . '" data-map_name="' . $setup_map_name . '" data-map="' . $MapName . '" style="width:' . $width_str . '; max-width:100%; height:' . $height_str . '; display:' . $vis_str . '; overflow:hidden;border:' . $map_border . ';" >
				  <div id="' . $MapName . '_popup" class="ol-popup" >
					<a href="#" id="' . $MapName . '_popup-closer" class="ol-popup-closer"></a>
					<div id="' . $MapName . '_popup-content" ></div>
				  </div>
				</div>
			';
}

      if( $OL3_LIBS_LOADED == 0) {
			  $OL3_LIBS_LOADED = 1;
			  $output .= '
				<link rel="stylesheet" href="' . Osm_OL_3_CSS . '" type="text/css">
				<link rel="stylesheet" href="' . Osm_OL_3_Ext_CSS . '" type="text/css">
				<link rel="stylesheet" href="' . Osm_map_CSS. '" type="text/css">
				<!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->
                                <script src="' . OSM_PLUGIN_URL .'js/polyfill/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>

				<script src="' . Osm_OL_3_LibraryLocation .'" type="text/javascript"></script>
				<script src="' . Osm_OL_3_Ext_LibraryLocation .'" type="text/javascript"></script>
				<script src="' . Osm_OL_3_MetaboxEvents_LibraryLocation .'" type="text/javascript"></script>
				<script src="' . Osm_map_startup_LibraryLocation . '" type="text/javascript"></script>
				<script type="text/javascript">
					translations[\'openlayer\'] = "' . __('open layer', 'OSM_Plugin') . '";
					translations[\'openlayerAtStartup\'] = "' . __('open layer at startup', 'OSM_Plugin') . '";
					translations[\'generateLink\'] = "' . __('link to this map with opened layers', 'OSM') . '";
					translations[\'shortDescription\'] = "' . __('short description', 'OSM') . '";
					translations[\'generatedShortCode\'] = "' . __('to get a text control link paste this code in your wordpress editor', 'OSM') . '";
					translations[\'closeLayer\'] = "' . __('close layer', 'OSM_Plugin') . '";
					translations[\'cantGenerateLink\'] = "' . __('put this string in the existing map short code to control this map', 'OSM_Plugin') . '";
			  </script>


			  ';
			}

			$FileColorListArray = array();
			$FileLinkArray = array();
			$FileTitleArray = array();
			$showSelectbox = false;
			$NumOfGpxKmlFiles = 0;
			
			if ($file_color_list != 'NoColor') {
				$FileColorListArray = explode(',', $file_color_list);
			} else {
				$FileColorListArray[0] = 'NoColor';
			}

			/** add links at the end of the clickable title of the layer */
			if ($file_link != 'no') {
				$FileLinkArray = explode(',', $file_link);
			}



         if(($file_select_box != 'no') && ($file_title != 'no')){
			    $showSelectbox = true;
			  }

			/** if title are set - my code will run - otherwise not
			if ($file_title != 'no') {*/
			if ($showSelectbox == true){
				if ($hide_kml_sel_box == 'no' ){
				$output .= '
					<div id="osmLayerSelect">
					<h5>' . __('Click title to show track', 'OSM') . '</h5>' . PHP_EOL;

				$FileTitleArray = explode(',', $file_title);

				foreach ($FileTitleArray as $key => $val) {

					$output .= '
						<span id="layerBox' . $key . $MapName . '" class="layerBoxes layerOf' . $MapName . '" data-map="' . $MapName . '" data-layer="' . $key . '" data-active="false" data-layer_title="' . trim($val) . '"><i class="fa fa-eye-slash"></i>';


					if (!empty($FileColorListArray[$key])) {
						$output .= '<span class="layerColor layerColorHidden" style="background-color:'  . $FileColorListArray[$key] . '"></span>';
					}

					$output .= '<span class="padding1em">' . trim($val) . '</span></span>';

					/** link not in span id#layerBox to remain still executeable */
					if (!empty($FileLinkArray[$key])) {
						$output .= '<a href="' .  $FileLinkArray[$key]. '" class="fileLink"><i class="fa fa-external-link" aria-hidden="true"></i></a>';
					}
					$output .= '
						<br />' . PHP_EOL;
				}


				/** if setup_map_name is set, set setup_map_name otherwise map */
				if ($setup_map_name != 'undefined') {
				  $map_link_name  = $setup_map_name;
                                  echo "ERROR";
				} else {
					$map_link_name  = $MapName;
				}

				$output .= '
				    <!--
						<a id="generatedLink" class="generatedLink" data-map="' . $MapName . '" data-map_name="' . $map_link_name . '">' . __('get link to map with choosen layers', 'OSM') . '</a>
						-->
					</div>';

				}

				else { /** show only textlink not box */

				$FileTitleArray = explode(',', $file_title);

				foreach ($FileTitleArray as $key => $val) {
					$output .= '
						<span id="layerBox' . $key . $MapName . '" class="layerBoxes layerOf' . $MapName . '" data-map="' . $MapName . '" data-layer="' . $key . '" data-active="false" data-layer_title="' . trim($val) . '">';

				}
				}
			}

			/** logged in users will see one of these links <== ToDo with Version 4.0
 			if ( is_admin_bar_showing() ) {

				if ($setup_map_name == 'undefined') {
					$output .= '<div class="cantGenerateShortCode"><a class="shortCodeGeneration cantGenerateShortCode" >' . __('if you want to setup a control via text link, set setup_map_name in shortcode of map to control')  . '</a></div>';

				} else {

					$output .= '<div class="generatedShortCode"><a class="shortCodeGeneration generatedShortCode" data-map="' . $MapName . '" data-map_name="' . $setup_map_name . '">' . __('get shotcut to this map with choosen layers', 'OSM') . '</a></div>';
				}
			}
			*/

			$ov_map = "ov_map";
			$theme = "theme";





			/** vectorM is global */
			$output .= '<script type="text/javascript">
			  vectorM[\''. $MapName .'\'] = [];
	        
        var raster = getTileLayer("'.$type.'","'.$api_key.'");			

			  var '. $MapName .' = new ol.Map({
				interactions: ol.interaction.defaults.defaults({mouseWheelZoom:'.$mwz.'}),
				layers: [raster],
				target: "'. $MapName .'",
				view: new ol.View({
				  center: ol.proj.transform(['.$lon.','.$lat.'], "EPSG:4326", "EPSG:3857"),
				  zoom: '.$zoom.'
				})
			  });
			  ';

			if ($type == "openseamap"){
			  $output .= '

          var Layer2 = new ol.layer.Tile({
            source: new ol.source.OSM({
              attributions: "Maps &copy; " +
              "<a href=\"http://www.openseamap.org/\">OpenSeaMap</a>",
              crossOrigin: null,
              url: "'.Osm_OpenSeaMap_Tiles.'"
            }),
            className: "ol-openseamap",
            zIndex: 91
          });			  
			  
			  '. $MapName .'.addLayer(Layer2);';
			}


			if ($file_list != "NoFile"){
			  $FileListArray   = explode( ',', $file_list );
				/** FileColorListArray is set on line 181 */

			  Osm::traceText(DEBUG_INFO, "(NumOfFiles: ".sizeof($FileListArray)." NumOfColours: ".sizeof($FileColorListArray).")!");
			  if (($FileColorListArray[0] != "NoColor") && (sizeof($FileColorListArray) != sizeof($FileListArray))){
				 Osm::traceText(DEBUG_ERROR, "e_filelist_mismatch");
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
					if ($FileColorListArray[0] == "NoColor"){
						$Color = "blue";
					}
					else {
						$Color = $FileColorListArray[$x];
					}

               if (sizeof($FileTitleArray) == 0){$FileTitle = 0;}
					else {$FileTitle = $FileTitleArray[$x];}

					$gpx_marker_name = "mic_blue_pinother_02.png";
					if ($Color == "blue"){$gpx_marker_name = "mic_blue_pinother_02.png";}
					else if ($Color == "red"){$gpx_marker_name = "mic_red_pinother_02.png";}
					else if ($Color == "green"){$gpx_marker_name = "mic_green_pinother_02.png";}
					else if ($Color == "black"){$gpx_marker_name = "mic_black_pinother_02.png";}
					$output .= Osm_OLJS3::addVectorLayer($MapName, $FileListArray[$x], $Color, $FileType, $x, $gpx_marker_name, $showMarkerName, $FileTitle, $file_param);
				  }
				  else {
					 Osm::traceText(DEBUG_ERROR, (sprintf(__('%s hast got wrong file extension (gpx, kml)!'), $FileName)));
				  }
				}
				//$output .= 'osm_addPopupClickhandler('. $MapName .',  "'. $MapName .'"); ';
			  }
			} // $file_list != "NoFile" 
		  
        $custom_post_types = array_values(get_post_types());
        $post_marked = in_array($tagged_type, $custom_post_types);
        if (($post_marked) && ($tagged_param == "cluster")) {	  
		    $tagged_icon = new cOsm_icon($default_icon->getIconName());
			 $MarkerArray = OSM::OL3_createMarkerList('osm_l', $tagged_filter, 'Osm_None', $tagged_type, 'Osm_All', $tagged_filter_type);
			 $NumOfMarker = count($MarkerArray);
			 $Counter = 0;
			 $output .= 'var vectorMarkerSource = new ol.source.Vector({});';

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

          $output .= 'vectorMarkerLayer = getVectorClusterLayer(
                                            vectorMarkerSource,
                                            '.$sc_args->getTaggedBorderColor().',
                                            '.$sc_args->getTaggedInnerColor().',
                                            "'.$tagged_icon->getIconURL().'",
                                            '.$tagged_icon->getIconOffsetwidth().',
                                            '.$tagged_icon->getIconOffsetheight().');';	
			
			 $output .= $MapName.'.addLayer(vectorMarkerLayer);';
		  }
		  elseif (($post_marked) && ($tagged_param != "cluster")) {
		
		
			$tagged_icon = new cOsm_icon($default_icon->getIconName());

			$MarkerArray = OSM::OL3_createMarkerList('osm_l', $tagged_filter, 'Osm_None', $tagged_type, 'Osm_All', $tagged_filter_type);

			$NumOfMarker = count($MarkerArray);
			$Counter = 0;
			$output .= '
			  var vectorMarkerSource = new ol.source.Vector({});
			  var vectorMarkerLayer = new ol.layer.Vector({
				source: vectorMarkerSource,
				zIndex: 92
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

		   if ((strtolower($marker_latlon) == 'osm_geotag') || (strtolower($tagged_type) == 'actual')){
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
				//$output .= 'osm_addMarkerLayer('. $MapName .','.$temp_lon.','.$temp_lat.') ; ';
				$output .= 'osm_addMarkerLayer('. $MapName .','.$temp_lon.','.$temp_lat.',"'.$postgeotag_icon->getIconURL().'",'.$postgeotag_icon->getIconOffsetwidth().','.$postgeotag_icon->getIconOffsetheight().',"") ; ';
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
				$output .= 'osm_addMarkerLayer('. $MapName .','.$temp_lon.','.$temp_lat.',"'.$default_icon->getIconURL().'",'.$default_icon->getIconOffsetwidth().','.$default_icon->getIconOffsetheight().',"") ; ';
			  }// templat lon != 0

		}

		// add post markers
		if (strtolower($postmarkers) != 'no'){

			$MarkerArray = OSM::OL3_createMarkerList($postmarkers, $tagged_filter, 'Osm_None', $tagged_type, 'Osm_All', $tagged_filter_type);

         if (is_array($MarkerArray) || is_object($MarkerArray)) {
			  $NumOfMarker = count($MarkerArray);
			  $Counter = 0;

			foreach( $MarkerArray as $Marker ) {
			  $metapostmarker_text = addslashes($MarkerArray[$Counter]['text']);
			  $temp_lat = $MarkerArray[$Counter]['lat'];
			  $temp_lon = $MarkerArray[$Counter]['lon'];
			  $metapostIcon_name = $MarkerArray[$Counter]['marker'];

			  $metapostmarker_name = "MISSING";

			  if ($metapostIcon_name == ""){
				Osm::traceText(DEBUG_ERROR, "e_add_marker");
			  }
			  $postmarker_icon = new cOsm_icon($metapostIcon_name);

			  $DoPopUp = 'false';

			  list($temp_lat, $temp_lon) = Osm::checkLatLongRange('Marker',$temp_lat, $temp_lon,'no');
			  if (($temp_lat != 0) || ($temp_lon != 0)){
				$output .= 'osm_addMarkerLayer('. $MapName .','.$temp_lon.','.$temp_lat.',"'.$postmarker_icon->getIconURL().'",'.$postmarker_icon->getIconOffsetwidth().','.$postmarker_icon->getIconOffsetheight().',"'.$metapostmarker_text.'") ; '. PHP_EOL;
				$Counter = $Counter +1;
			  }


			}// foreach(MarkerArray)
                    } // is array
                    else {
                      // no markers found
                    }
		} //($postmarkers) != 'no'')

    if (($map_autocenter == true) && (($file_list != "NoFile") || ($tagged_type != "no"))) {

    // maxZoom level for autocenter
    
    $Fitzoom = "";    

    if (!($sc_args->isAutozoom())){
      $Fitzoom = ",maxZoom: ".$zoom;
    }

    $output.= '
      var extension'.$MapCounter.' = ol.extent.createEmpty();
      var curZoom'.$MapCounter.' = '.$MapName.'.getView().getZoom();

      '.$MapName.'.getLayers().forEach(function(layer){
         if(!layer.get("id")) {     
            layer.once("change", function(e){
               ol.extent.extend(extension'.$MapCounter.', (layer.getSource().getExtent()));
               '.$MapName.'.getView().fit(extension'. $MapCounter.', {padding: [50, 50, 50, 50]'.$Fitzoom.'});
            });
         }
      });

    '. PHP_EOL;
    }; 

		  //eventhanlder for metabox 
		  include('osm-sc-osm_map_v3_backend.php');
		  		                                 
      $output .= 'addControls2Map('.$MapName.','.$sc_args->issetMouseposition().','.$sc_args->issetOverview().',3,'.$sc_args->issetScaleline().',5,6,7,'.$sc_args->issetFullScreen().','.$sc_args->isMapAttr().');' . PHP_EOL;

		if (($tagged_param == "cluster")||($file_param == "cluster")){
		  $output .= 'osm_addClusterPopupClickhandler('. $MapName .',  "'. $MapName .'"); ' . PHP_EOL;
		}
		else{
		  $output .= 'osm_addPopupClickhandler('. $MapName .',  "'. $MapName .'"); ' . PHP_EOL;
		}

		$output .= 'osm_addMouseHover(' . $MapName . '); ';

		$output .= '</script>';
}
?>
