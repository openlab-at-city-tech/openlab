<?php
/*  (c) Copyright 2017  MiKa (wp-osm-plugin.HanBlog.Net)

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

class Osm_OpenLayers
{
  //support different types of GML Layers
  public static function addVectorLayer($a_LayerName, $a_FileName, $a_Colour, $a_Type)
  {
    Osm::traceText(DEBUG_INFO, "addVectorLayer(".$a_LayerName.",".$a_FileName.",".$a_Colour.",".$a_Type.")");
    $Layer = '';


// Functions for KML files
    $Layer .= '  function osm_'.$a_LayerName.'onPopupClose(evt) {';
    $Layer .= '    select.unselectAll();';
    $Layer .= '  }';

    $Layer .= '  function osm_'.$a_LayerName.'onFeatureSelect(event) {';
    $Layer .= '    var feature = event.feature;';
    $Layer .= '    var content = "<b>"+feature.attributes.name + "</b> <br>" + feature.attributes.description;';

    $Layer .= '    if (content.search("<script") != -1) {';
    $Layer .= '       content = "Content contained Javascript! Escaped content below.<br>" + content.replace(/</g, "&lt;");';
    $Layer .= '    }';
    $Layer .= '    popup = new OpenLayers.Popup.FramedCloud("OSM Plugin",';
    $Layer .= '      feature.geometry.getBounds().getCenterLonLat(),';
    $Layer .= '        new OpenLayers.Size(200,100),';
    $Layer .= '        content,';
    $Layer .= '        null, true, osm_'.$a_LayerName.'onPopupClose);';
    $Layer .= '    popup.autoSize = true;';
    $Layer .= '    feature.popup = popup;';
    $Layer .= '    '.$a_LayerName.'.addPopup(popup);';
    $Layer .= '   }';

    $Layer .= '  function osm_'.$a_LayerName.'onFeatureUnselect(event) {';
    $Layer .= '    var feature = event.feature;';
    $Layer .= '    if(feature.popup) {';
    $Layer .= '      '.$a_LayerName.'.removePopup(feature.popup);';
    $Layer .= '      feature.popup.destroy();';
    $Layer .= '      delete feature.popup;';
    $Layer .= '    }   ';
    $Layer .= '  }';

    // Add the Layer with the GPX Track
    $Layer .= '  var lgml = new OpenLayers.Layer.Vector("'.$a_FileName.'",{';
    $Layer .= '   strategies: [new OpenLayers.Strategy.Fixed()],';
    $Layer .= '	  protocol: new OpenLayers.Protocol.HTTP({';
    $Layer .= '	   url: "'.$a_FileName.'",';


    if ($a_Type == 'GPX'){
    $Layer .= '	   format: new OpenLayers.Format.GPX()';
    $Layer .= '	  }),';
    
    $Layer .= '    style: {strokeColor: "'.$a_Colour.'", strokeWidth: 5, strokeOpacity: 0.5},';
    $Layer .= '    projection: new OpenLayers.Projection("EPSG:4326")';
    $Layer .= '  });';
    $Layer .= '  '.$a_LayerName.'.addLayer(lgml);';
    }
    else if ($a_Type == 'KML'){
    $Layer .= '	   format: new OpenLayers.Format.KML({';
    $Layer .= '	   extractStyles: true,';
    $Layer .= '	   extractAttributes: true,';
    $Layer .= '	   maxDepth: 2})';
    $Layer .= '	  }),';
    
    $Layer .= '    style: {strokeColor: "'.$a_Colour.'", strokeWidth: 5, strokeOpacity: 0.5},';
    $Layer .= '    projection: new OpenLayers.Projection("EPSG:4326")';
    $Layer .= '  });';
    $Layer .= '  '.$a_LayerName.'.addLayer(lgml);';

//+++
    $Layer .= '            select = new OpenLayers.Control.SelectFeature(lgml);';
            
    $Layer .= '            lgml.events.on({';
    $Layer .= '                "featureselected": osm_'.$a_LayerName.'onFeatureSelect,';
    $Layer .= '                "featureunselected": osm_'.$a_LayerName.'onFeatureUnselect';
    $Layer .= '            });';

    $Layer .= '            '.$a_LayerName.'.addControl(select);';
    $Layer .= '            select.activate();   ';
  //  $Layer .= '            map.zoomToExtent(new OpenLayers.Bounds(68.774414,11.381836,123.662109,34.628906));';
    }                 
    return $Layer;
  }

  public static function addGoogleTileLayer($a_LayerName, $a_Type){
    $Layer = '';
    if ($a_Type == 'GooglePhysical'){
    $Layer .= '
    var '.$a_LayerName.' = new OpenLayers.Map("'.$a_LayerName.'", {projection: "EPSG:3857", displayProjection: "EPSG:4326",
        layers: [new OpenLayers.Layer.Google("Google Physical",
                {type: google.maps.MapTypeId.TERRAIN, zoomMethod: null, animationEnabled: false, numZoomLevels: 23, MAX_ZOOM_LEVEL: 22}),
                new OpenLayers.Layer.Vector("OSM-plugin",{attribution:" <a href=\"http://www.hanblog.net\">OSM-Plugin<br><br></a>"})]});
    ';
    }
    else if ($a_Type == 'GoogleStreet'){
      $Layer .= '
      var '.$a_LayerName.' = new OpenLayers.Map(
        "'.$a_LayerName.'", 
        {projection: "EPSG:3857", 
         displayProjection: "EPSG:4326",
         layers: [new OpenLayers.Layer.Google("Google Streets",
                   {zoomMethod: null, animationEnabled: false, numZoomLevels: 23, MAX_ZOOM_LEVEL: 22}),
                  new OpenLayers.Layer.Vector("OSM-plugin",{attribution:" <a href=\"http://www.hanblog.net\">OSM-Plugin<br><br></a>"})]});
      ';
    }
    else if ($a_Type == 'GoogleHybrid'){
    $Layer .= '
    var '.$a_LayerName.' = new OpenLayers.Map("'.$a_LayerName.'", {projection: "EPSG:3857", displayProjection: "EPSG:4326",
        layers: [new OpenLayers.Layer.Google("Google Hybrid",
                {type: google.maps.MapTypeId.HYBRID, zoomMethod: null, animationEnabled: false, numZoomLevels: 23, MAX_ZOOM_LEVEL: 22}),
                 new OpenLayers.Layer.Vector("OSM-plugin",{attribution:" <a href=\"http://www.hanblog.net\">OSM-Plugin<br><br></a>"})]});
    ';
    }
    else if ($a_Type == 'GoogleSatellite'){
    $Layer .= '

    var '.$a_LayerName.' = new OpenLayers.Map("'.$a_LayerName.'", {projection: "EPSG:3857", displayProjection: "EPSG:4326",
        layers: [new OpenLayers.Layer.Google("Google Satellite",
                {type: google.maps.MapTypeId.SATELLITE, zoomMethod: null, animationEnabled: false, numZoomLevels: 23, MAX_ZOOM_LEVEL: 22}),
            new OpenLayers.Layer.Vector("OSM-plugin",{attribution:" <a href=\"http://www.hanblog.net\">OSM-Plugin<br><br></a>"})]});
    ';
    }
    else if ($a_Type == 'AllGoogle'){
    $Layer .= '
    var '.$a_LayerName.' = new OpenLayers.Map("'.$a_LayerName.'", {
        projection: "EPSG:3857", displayProjection: "EPSG:4326",
        layers: [
            new OpenLayers.Layer.Google(
                "Google Physical",
                {type: google.maps.MapTypeId.TERRAIN, zoomMethod: null}
            ),
            new OpenLayers.Layer.Google(
                "Google Streets", 
                {numZoomLevels: 20, zoomMethod: null}
            ),
            new OpenLayers.Layer.Google(
                "Google Hybrid",
                {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20, zoomMethod: null}
            ),
            new OpenLayers.Layer.Google(
                "Google Satellite",
                {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22, zoomMethod: null}
            ),
            new OpenLayers.Layer.Vector("OSM-plugin",{attribution:" <a href=\"http://www.hanblog.net\">OSM-Plugin<br><br></a>"})
        ]});
    '.$a_LayerName.'.addControl(new OpenLayers.Control.LayerSwitcher());
    ';
    }
    else {// ERROR => set to default
    $Layer .= '
    var '.$a_LayerName.' = new OpenLayers.Map("'.$a_LayerName.'", {projection: "EPSG:3857"});
     var Street = new OpenLayers.Layer.Google("Google Streets", {numZoomLevels: 20});
    '.$a_LayerName.'.addLayers([Street]);
    ';
    }
    return $Layer;
}

// support different types of GML Layers
  public static function addTileLayer($a_LayerName, $a_Type, $a_OverviewMapZoom, $a_MapControl, $a_ExtType, $a_ExtName, $a_ExtAddress, $a_ExtInit, $a_theme)
  {
    Osm::traceText(DEBUG_INFO, "addTileLayer(".$a_LayerName.",".$a_Type.",".$a_OverviewMapZoom.")");

    $Layer = '';
    if ($a_theme == 'private'){
      $Layer .= ' OpenLayers.ImgPath = "'.OSM_OPENLAYERS_THEMES_URL.'";';
    }
    else {
      $Layer .= ' OpenLayers.ImgPath = "'.OSM_PLUGIN_THEMES_URL.$a_theme.'/";';
    }
    $Layer .= ' '.$a_LayerName.' = new OpenLayers.Map ("'.$a_LayerName.'", {';
    $Layer .= '            controls:[';
    if (($a_MapControl[0] != 'off') && (strtolower($a_Type)!= 'ext')) {
      $Layer .= '              new OpenLayers.Control.Navigation(),';
      $Layer .= '              new OpenLayers.Control.PanZoom(),';
      $Layer .= '              new OpenLayers.Control.Attribution()';
    }
    else if (($a_MapControl[0] == 'off') && (strtolower($a_Type)!= 'ext')){
      $Layer .= '              new OpenLayers.Control.Attribution()';
    }
    else if (($a_MapControl[0] != 'off') && (strtolower($a_Type)== 'ext')){
      $Layer .= '              new OpenLayers.Control.Navigation(),';
      $Layer .= '              new OpenLayers.Control.PanZoom(),';
      $Layer .= '              new OpenLayers.Control.Attribution()';
    }
    else if (($a_MapControl[0] == 'off') && (strtolower($a_Type)== 'ext')){
      // there is nothing to do
    }
    else {
      Osm::traceText(DEBUG_ERROR, "addOsmLayer(".$a_MapControl[0].",".$a_Type.")");
    }
    $Layer .= '              ],';
    $Layer .= '          maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),';
    $Layer .= '          maxResolution: 156543.0399,';
    $Layer .= '          numZoomLevels: 19,';
    $Layer .= '          units: "m",';
    $Layer .= '          projection: new OpenLayers.Projection("EPSG:900913"),';
    $Layer .= '          displayProjection: new OpenLayers.Projection("EPSG:4326")';
    $Layer .= '      } );';
    if (($a_Type == 'AllOsm') || ($a_Type == 'All')){
      $Layer .= 'var layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik");';
      $Layer .= 'var layerCycle  = new OpenLayers.Layer.OSM.CycleMap("CycleMap");';
      //$Layer .= 'var layerOSM_Attr = new OpenLayers.Layer.Vector("OSM-plugin",{attribution:"<a href=\"http://wp-osm-plugin.hanblog.net\">OSM plugin</a>"});';
      $Layer .= ''.$a_LayerName.'.addLayers([layerMapnik, layerCycle]);';
      $Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.LayerSwitcher());';
    }
    else if ($a_Type == 'basemap_at'){
 	// Create a WMTS layer, with given matrix IDs.
      $Layer .= 'var matrixIds = new Array(18);';
      $Layer .= 'for (var i=0; i<=18; ++i) {';
      $Layer .= '  matrixIds[i] = new Object();';
      $Layer .= '  matrixIds[i].identifier = "" + i;';
      $Layer .= '} ';
      $Layer .= 'var layerosm = new OpenLayers.Layer.OSM.Mapnik("Mapnik");';
      $Layer .= 'var layerbasemap_at = new OpenLayers.Layer.WMTS({
                   url: "'.Osm_BaseMap_Tiles.'{Style}/{TileMatrixSet}/{TileMatrix}/{TileRow}/{TileCol}.png",
                   name: "basemap.at",
                   layer: "geolandbasemap",
                   style: "normal",
                   matrixSet: "google3857",
                   requestEncoding: "REST",
                   matrixIds: matrixIds,
                   tileOptions: {crossOriginKeyword: null},
                   transitionEffect: "resize"
                  });
      ';
      $Layer .= 'layerbasemap_at.metadata = {link: "http://www.basemap.at/"};';
      $Layer .= 'var layerOSM_Attr = new OpenLayers.Layer.Vector("OSM-plugin",{attribution:"<a href=\"http://basemap.at\">basemap.at</a> and <a href=\"http://www.hanblog.net\">OSM-Plugin</a>"});';
      $Layer .= ''.$a_LayerName.'.addLayers([layerbasemap_at, layerosm, layerOSM_Attr]);';
      $Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.LayerSwitcher());';
    }
    else if ($a_Type == 'OpenSeaMap'){
      $Layer .= 'var layerMapnik   = new OpenLayers.Layer.OSM.Mapnik("Mapnik");';
      $Layer .= 'var layerSeamark  = new OpenLayers.Layer.TMS("Seezeichen", "http://t1.openseamap.org/seamark/", { numZoomLevels: 18, type: "png", getURL: getTileURL, tileOptions: {crossOriginKeyword: null}, isBaseLayer: false, displayOutsideMaxExtent: true});';
      $Layer .= 'var layerPois = new OpenLayers.Layer.Vector("Haefen", { projection: new OpenLayers.Projection("EPSG:4326"), visibility: true, displayOutsideMaxExtent:true});';
      $Layer .= 'layerPois.setOpacity(0.8);';
      $Layer .= ''.$a_LayerName.'.addLayers([layerMapnik, layerSeamark, layerPois]);';
      $Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.LayerSwitcher());';
    }
    else if ($a_Type == 'OpenWeatherMap'){
      $Layer .= 'var layerMapnik   = new OpenLayers.Layer.OSM.Mapnik("Mapnik");';
      $Layer .= 'var layerWeather = new OpenLayers.Layer.Vector.OWMWeather("Weather");';
      $Layer .= ''.$a_LayerName.'.addLayers([layerMapnik, layerWeather]);';
      $Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.LayerSwitcher());';
    }
    else if ($a_Type == 'OSMRoadsMap'){
    	$Layer .= 'var layerMapnik   = new OpenLayers.Layer.OSM.Mapnik("Mapnik");';
        $Layer .= 'var layerCycle  = new OpenLayers.Layer.OSM.CycleMap("CycleMap");';

        $Layer .= 'var layerOSMRoadsMap   = new OpenLayers.Layer.TMS("OSMRoadsMap", "http://openmapsurfer.uni-hd.de/tiles/roads/x={x}&y={y}&z={z}",{ tileOptions: {crossOriginKeyword: null}, isBaseLayer: false});'; 
        $Layer .= 'var layerOSMHillshadeMap   = new OpenLayers.Layer.TMS("OSMHillshadeMap", " http://129.206.74.245:8004/tms_hs.ashx?x={x}&y={y}&z={z} ",{ numZoomLevels: 18, type: "png", getURL: getTileURL, tileOptions: {crossOriginKeyword: null}, isBaseLayer: false});'; 


    $Layer .= 'var testlayer = new OpenLayers.Layer.TMS(
              "OSM Roads",
              "http://openmapsurfer.uni-hd.de/tiles/roads/",
              {
                  numZoomLevels: 20,
                  type: "png", getURL: getTileURL,
                  displayOutsideMaxExtent: true,
                  isBaseLayer: true
              }
            ); ';

    $Layer .= 'var layMSNOsmHybrid = new OpenLayers.Layer.TMS(
              "OSM Semitransparent",
              "http://openmapsurfer.uni-hd.de/tiles/hybrid/",
              {
                  type: "png", getURL: getTileURL,
                  displayOutsideMaxExtent: true,
                  isBaseLayer: false
              }
            );  ';


    	$Layer .= ''.$a_LayerName.'.addLayers([layerMapnik, layerCycle, layMSNOsmHybrid, layerOSMRoadsMap, layerOSMHillshadeMap, testlayer]);';
    	$Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.LayerSwitcher());';
    }

    else if ($a_Type == 'stamen_watercolor'){
        $Layer .= 'var lmap = new OpenLayers.Layer.OSM.StamenWC("Stamen watercolor");';
        $Layer .= ''.$a_LayerName.'.addLayers([lmap]);';
    }
    else if ($a_Type == 'stamen_toner'){
        $Layer .= 'var lmap = new OpenLayers.Layer.OSM.StamenToner("Stamen Toner");';
        $Layer .= ''.$a_LayerName.'.addLayers([lmap]);';
    }
    else{
      if ($a_Type == 'Mapnik'){
        $Layer .= 'var lmap = new OpenLayers.Layer.OSM.Mapnik("Mapnik");';
        $Layer .= ''.$a_LayerName.'.addLayers([lmap]);';
      } 
      if ($a_Type == 'mapnik_ssl'){  
        $Layer .= 'var lmap = new OpenLayers.Layer.OSM.Mapnik("Mapnik");';
        $Layer .= ''.$a_LayerName.'.addLayers([lmap]);';
      }
      else if ($a_Type == 'CycleMap'){
        $Layer .= 'var lmap = new OpenLayers.Layer.OSM.CycleMap("CycleMap");';
        $Layer .= ''.$a_LayerName.'.addLayers([lmap]);';
      }
      else if (($a_Type == 'Ext') || ($a_Type == 'ext')) {
        $Layer .= 'var lmap = new OpenLayers.Layer.'.$a_ExtType.'("'.$a_ExtName.'","'.$a_ExtAddress.'",{'.$a_ExtInit.', attribution: "OpenLayers with"});';
        $Layer .= 'var layerOSM_Attr = new OpenLayers.Layer.Vector("OSM-plugin",{attribution:"<a href=\"http://www.hanblog.net\">OSM plugin</a>"});';
        $Layer .= ''.$a_LayerName.'.addLayers([lmap,layerOSM_Attr]);';
      }
    }
    if ($a_MapControl[0] != 'No'){
      foreach ( $a_MapControl as $MapControl ){
        $MapControl = strtolower($MapControl);
        if ( $MapControl == 'scaleline'){
          $Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.ScaleLine({geodesic: true}));';
        }
        elseif ($MapControl == 'scale'){
          //$Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.Scale());';
          $Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.Scale({geodesic: true}));';
//var scalebar = new OpenLayers.Control.ScaleLine({geodesic: true});

        }
        elseif ($MapControl == 'mouseposition'){
          $Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.MousePosition({displayProjection: new OpenLayers.Projection("EPSG:4326")}));';
        }
      }
    }

    // add the overview map
    if ($a_OverviewMapZoom >= 0){  
      $Layer .= 'layer_ov = new OpenLayers.Layer.OSM;';
      if ($a_OverviewMapZoom > 0 && $a_OverviewMapZoom < 18 ){
        $Layer .= 'var options = {
                      layers: [layer_ov],
                      mapOptions: {numZoomLevels: '.$a_OverviewMapZoom.'}
                      };';
      }
      else{
        $Layer .= 'var options = {layers: [layer_ov]};';
      }
      $Layer .= ''.$a_LayerName.'.addControl(new OpenLayers.Control.OverviewMap(options));';
    }
    return $Layer;
  }

  public static function AddClickHandler($a_MapName, $a_msgBox, $a_post_id)
  {
    Osm::traceText(DEBUG_INFO, "AddClickHandler(".$a_msgBox.")");
    $a_msgBox = strtolower($a_msgBox);
    $Layer = '';

//++ 
    $Layer .= '  var markerslayer = new OpenLayers.Layer.Markers( "Markers" );';
    $Layer .= '  '.$a_MapName.'.addLayer(markerslayer);'; 
//--  
    $Layer .= 'OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {';               
    $Layer .= ' 	                defaultHandlerOptions: {';
    $Layer .= ' 	                    "single": true,';
    $Layer .= ' 	                    "double": false,';
    $Layer .= ' 	                    "pixelTolerance": 0,';
    $Layer .= ' 	                    "stopSingle": false,';
    $Layer .= ' 	                    "stopDouble": false';
    $Layer .= ' 	                },';

    $Layer .= ' 	                initialize: function(options) {';
    $Layer .= ' 	                    this.handlerOptions = OpenLayers.Util.extend(';
    $Layer .= ' 	                        {}, this.defaultHandlerOptions';
    $Layer .= ' 	                    );';
    $Layer .= ' 	                    OpenLayers.Control.prototype.initialize.apply(';
    $Layer .= ' 	                        this, arguments';
    $Layer .= ' 	                    );';
    $Layer .= ' 	                    this.handler = new OpenLayers.Handler.Click(';
    $Layer .= ' 	                        this, {';
    $Layer .= ' 	                            "click": this.trigger';
    $Layer .= ' 	                        }, this.handlerOptions';
    $Layer .= ' 	                    );';
    $Layer .= ' 	                },';

    $Layer .= ' 	                trigger: function(e) {';
    $Layer .= '                     var LayerName =    '.$a_MapName.'.baseLayer.name; ';  
    $Layer .= ' 	            var Centerlonlat = '.$a_MapName.'.getCenter(e.xy).clone();';
    $Layer .= ' 	            var Clicklonlat = '.$a_MapName.'.getLonLatFromViewPortPx(e.xy);';
    $Layer .= ' 	            var zoom = '.$a_MapName.'.getZoom(e.xy);';
    $Layer .= '                     Centerlonlat.transform('.$a_MapName.'.getProjectionObject(), '.$a_MapName.'.displayProjection);';
    $Layer .= '                     Clicklonlat.transform('.$a_MapName.'.getProjectionObject(), '.$a_MapName.'.displayProjection);';
    $Layer .= '                     Centerlonlat.lat = Math.round( Centerlonlat.lat * 1000. ) / 1000.;'; // mapcenter
    $Layer .= '                     Centerlonlat.lon = Math.round( Centerlonlat.lon * 1000. ) / 1000.;';
    $Layer .= '                     Clicklonlat.lat = Math.round( Clicklonlat.lat * 100000. ) / 100000.;';// markerposition
    $Layer .= '                     Clicklonlat.lon = Math.round( Clicklonlat.lon * 100000. ) / 100000.;';  

    if( $a_msgBox == 'metabox_marker_sc_gen'){
    $Layer .= ' 
      MarkerId = "";
      BorderField  = "";
      MapTypeField = "";
      Controls = "";

      if (document.post.osm_marker_map_type.value != "Mapnik"){
        MapTypeField = " type=\"" + document.post.osm_marker_map_type.value + "\""; 
      }

      if (document.post.osm_marker_id.value != "no"){
        MarkerId = " post_markers=\"" + document.post.osm_marker_id.value + "\"";  
      }
      if (document.post.osm_marker_border.value != "none"){
        BorderField = " map_border=\"thin solid "  + document.post.osm_marker_border.value+ "\"";
     }
  
    if (document.post.fullscreen.checked){
        Controls = "fullscreen,";
    }

    if (document.post.scaleline.checked){
        Controls = Controls + "scaleline,";
    }
    if (document.post.mouseposition.checked){
        Controls = Controls + "mouseposition,";
    }
    if (Controls != ""){
      Controls = Controls.substr(0, Controls.length-1);
      ControlField = " control=\"" + Controls + "\"";
    }
    else {
      ControlField ="";
    }
      GenTxt = "[osm_map_v3 map_center=\"" + Centerlonlat.lat + "," + Centerlonlat.lon + "\" zoom=\"" + zoom + "\" width=\"100%\" height=\"450\" " + BorderField + MarkerId + MapTypeField + ControlField +"]"; 

      div = document.getElementById("ShortCode_Div");
      div.innerHTML = GenTxt;
    ';
}
    else if( $a_msgBox == 'metabox_add_marker_sc_gen'){
    $Layer .= ' 
      MarkerNameField = "";
      MarkerTextField ="";

      MarkerId = document.post.osm_add_marker_id.value;
      MarkerIcon = document.post.osm_add_marker_icon.value;
      MarkerName = document.post.osm_add_marker_name.value;
      MarkerTextField = document.post.osm_add_marker_text.value;
      MarkerTextField = MarkerTextField.replace(/(\r\n|\n|\r)/gm, "");
      MarkerTextField = MarkerTextField.replace(/(\')/gm, "&apos;");
      
      osm_ajax_object.MarkerLat = Clicklonlat.lat;
      osm_ajax_object.MarkerLon = Clicklonlat.lon;
      osm_ajax_object.MarkerId = MarkerId;
      osm_ajax_object.MarkerName = MarkerName;
      osm_ajax_object.MarkerText = MarkerTextField;
      osm_ajax_object.MarkerIcon = MarkerIcon;
      osm_ajax_object.post_id = '.$a_post_id.';

      GenTxt = "<br> Marker_Id: "+ MarkerId + "<br>Marker_Name: " + MarkerName + "<br>Marker_LatLon: "+Clicklonlat.lat+","+Clicklonlat.lon+ " <br>Icon: " + MarkerIcon + "<br>  Marker_Text:<br>"+ MarkerTextField + "<br><b>4. Press [Save] to store marker!</b>";

      div = document.getElementById("Marker_Div");
      div.innerHTML = GenTxt;
      div02 = document.getElementById("ShortCode_Div");
      div02.innerHTML = "";
    ';
    $Layer .= ' 
      markerslayer.clearMarkers();
        var icon_Obj = osm_getIconSize(MarkerIcon);
        var icon_size = new OpenLayers.Size(icon_Obj.width,icon_Obj.height);
        var icon_offset = new OpenLayers.Pixel(icon_Obj.offset_width, icon_Obj.offset_height);
        var icon_url = "'.OSM_PLUGIN_ICONS_URL.'" + MarkerIcon;
        var click_icon = new OpenLayers.Icon(icon_url,icon_size,icon_offset); 
        var icon_lonlat = new OpenLayers.LonLat(Clicklonlat.lon,Clicklonlat.lat).transform('.$a_MapName.'.displayProjection, '.$a_MapName.'.projection);
        markerslayer.addMarker(new OpenLayers.Marker(icon_lonlat,click_icon.clone()));
    ';
    }
    else if( $a_msgBox == 'metabox_file_list_sc_gen'){
    $Layer .= ' 
	
      var FileList_ColorField  = "";
      var FileList_TypeField   = "";
      var FileList_MapTypeField = "";
      var FileList_FileField = "";
      var FileList_TitleField = "";
      var DisplayName = "";
      
	fileUrls = [];
	fileTitles = [];
	fileColors = [];
      
	  var Controls = "";
      var ControlField =""; 
      BorderField = "";
	  
	  if (document.post.osm_file_list_map_type.value != "Mapnik"){
        FileList_MapTypeField = " type=\"" + document.post.osm_file_list_map_type.value + "\""; 
      }

      if (document.post.osm_file_border.value != "none"){
        BorderField = " map_border=\"thin solid "  + document.post.osm_file_border.value+ "\"";
     }
  
      if (document.post.file_fullscreen.checked){
        Controls = "fullscreen,";
     }

    if (document.post.file_scaleline.checked){
        Controls = Controls + "scaleline,";
    }
    if (document.post.file_mouseposition.checked){
        Controls = Controls + "mouseposition,";
    }
    if (Controls != ""){
      Controls = Controls.substr(0, Controls.length-1);
      ControlField = " control=\"" + Controls + "\"";
    }
    else {
      ControlField ="";
    }
  			
  	/** handle multiple form fields in metabox with same input (layers and their files/colors/titles - links still missing (tbc) */		
  		jQuery(".osmFileName").each(function(i,e) {
  		
  			if (jQuery(e).val() != "") {
	  			fileUrls.push( jQuery(e).val()); 
	  		}	
  		});
  		
  		jQuery(".osmFileTitle").each(function(i,e) {
  		
  			if (jQuery(e).val() != "" && fileUrls[i] != "") {
  				fileTitles.push( jQuery(e).val());
  			} 
  		});
  		
  		jQuery(".osmFileColor").each(function(i,e) {
  		
  			if (jQuery(e).val() != "" && typeof(fileUrls[i]) == "string") {
  				fileColors.push( jQuery(e).val()); 
  			}
  		});
  		
	  FileList_FileField = " file_list=\"" + fileUrls.join() + "\"";

	  FileList_ColorField = " file_color_list=\"" + fileColors.join() + "\""; 
	  
	  FileList_TitleField = " file_title=\"" + fileTitles.join() + "\""; 
	  
	   
	  GenTxt = "[osm_map_v3 map_center=\"" + Centerlonlat.lat + "," + Centerlonlat.lon + "\" zoom=\"" + zoom + "\" width=\"100%\" height=\"450\" " + FileList_FileField + FileList_MapTypeField + FileList_ColorField + DisplayName + ControlField + BorderField + FileList_TitleField + "]";

      div = document.getElementById("ShortCode_Div");
      div.innerHTML = GenTxt;
    ';
    $Layer .= ' 
      markerslayer.clearMarkers();
      /* vorsicht - hier gibt es kein document.post.osm_import (mehr?)
      if ((((document.post.osm_import.value == "single") || (document.post.osm_import.value == "none")) || (document.post.osm_mode.value == "geotagging")) && (document.post.osm_marker.value != "none")){
        var icon_Obj = osm_getIconSize(MarkerName);
        var icon_size = new OpenLayers.Size(icon_Obj.width,icon_Obj.height);
        var icon_offset = new OpenLayers.Pixel(icon_Obj.offset_width, icon_Obj.offset_height);
        var icon_url = "'.OSM_PLUGIN_ICONS_URL.'" + MarkerName;
        var click_icon = new OpenLayers.Icon(icon_url,icon_size,icon_offset); 
        var icon_lonlat = new OpenLayers.LonLat(Clicklonlat.lon,Clicklonlat.lat).transform('.$a_MapName.'.displayProjection, '.$a_MapName.'.projection);
        markerslayer.addMarker(new OpenLayers.Marker(icon_lonlat,click_icon.clone()));
      }*/';
    }	
    else if( $a_msgBox == 'metabox_file_sc_gen'){
    $Layer .= ' 
      var ThemeField  = "";
      var TypeField   = "";
      var MapTypeField = "";
      var FileField = "";

      var AddFileOption = document.post.osm_file_add_file.value;
      var FileUrl = document.post.file_FileURL.value;

      if (document.post.osm_file_map_type.value != "Mapnik"){
        MapTypeField = " type=\"" + document.post.osm_file_map_type.value + "\""; 
      }

      if (document.post.osm_file_theme.value == "dark"){
        ThemeField = " control=\"mouseposition,scaleline\" map_border=\"thin solid grey\" theme=\"dark\"";
      }
      else if (document.post.osm_file_theme.value == "blue"){
        ThemeField = " control=\"mouseposition,scaleline\" map_border=\"thin solid blue\" theme=\"ol\"";  
      }
      else if (document.post.osm_file_theme.value == "orange"){
        ThemeField = " control=\"mouseposition,scaleline\" map_border=\"thin solid orange\" theme=\"ol_orange\"";
      }


       if ((AddFileOption != "none") && (document.post.file_FileURL.value != "http://")){
         if (AddFileOption == "kml") {
           FileField = " kml_file=\""+ FileUrl + "\"";
         }
         if (AddFileOption == "gpx_red") {
           FileField = " gpx_file=\""+ FileUrl + "\" gpx_colour=\"red\"";
         }
         if (AddFileOption == "gpx_green") {
           FileField = " gpx_file=\""+ FileUrl + "\" gpx_colour=\"green\"";
         }
         if (AddFileOption == "gpx_blue") {
           FileField = " gpx_file=\""+ FileUrl + "\" gpx_colour=\"blue\"";
         }
         if (AddFileOption == "gpx_black") {
           FileField = " gpx_file=\""+ FileUrl + "\" gpx_colour=\"black\"";
         }
         if (AddFileOption == "text") {
           FileField = " marker_file=\""+ FileUrl + "\"";
         }
       } 

      GenTxt = "[osm_map lat=\"" + Centerlonlat.lat + "\" lon=\"" + Centerlonlat.lon + "\" zoom=\"" + zoom + "\" width=\"100%\" height=\"450\" " + ThemeField + FileField + MapTypeField + "]"; 

      div = document.getElementById("ShortCode_Div");
      div.innerHTML = GenTxt;
    ';
    $Layer .= ' 
      markerslayer.clearMarkers();
      if ((((document.post.osm_import.value == "single") || (document.post.osm_import.value == "none")) || (document.post.osm_mode.value == "geotagging")) && (document.post.osm_marker.value != "none")){
        var icon_Obj = osm_getIconSize(MarkerName);
        var icon_size = new OpenLayers.Size(icon_Obj.width,icon_Obj.height);
        var icon_offset = new OpenLayers.Pixel(icon_Obj.offset_width, icon_Obj.offset_height);
        var icon_url = "'.OSM_PLUGIN_ICONS_URL.'" + MarkerName;
        var click_icon = new OpenLayers.Icon(icon_url,icon_size,icon_offset); 
        var icon_lonlat = new OpenLayers.LonLat(Clicklonlat.lon,Clicklonlat.lat).transform('.$a_MapName.'.displayProjection, '.$a_MapName.'.projection);
        markerslayer.addMarker(new OpenLayers.Marker(icon_lonlat,click_icon.clone()));
      }';
    }
    else if( $a_msgBox == 'metabox_geotag_sc_gen'){
    $Layer .= ' 
      MarkerField = "";
      ThemeField  = "";
      MapTypeField = "";
      Linefield = "";
      PostTypeField ="";
      var CatFilterField = "";
      var MapBorderField = "";
      var MarkerStyleField = "";
      var StyleColorField = "";
      
      var dropdown = document.getElementById("cat");

      var MarkerName    = document.post.osm_geotag_marker.value;
      
      if (document.post.category_parent.value != "-1"){
        CatFilterField = " tagged_filter=\"" + document.post.category_parent.value + "\""; 
      }

      if (document.post.osm_geotag_map_type.value != "Mapnik"){
        MapTypeField = " type=\"" + document.post.osm_geotag_map_type.value + "\""; 
      }

      PostTypeField = " tagged_type=\""+document.post.osm_geotag_posttype.value+"\"";
      if (document.post.osm_geotag_marker.value != "none"){
        MarkerField = " marker_name=\"" + MarkerName + "\"";  
      }
  
        if (document.post.osm_geotag_map_border.value != "none"){
          MapBorderField = " map_border=\"thin solid "  + document.post.osm_geotag_map_border.value+ "\"";
        }
        if (document.post.osm_geotag_marker_style.value != "standard"){
          MarkerStyleField = " tagged_param=\""  + document.post.osm_geotag_marker_style.value+ "\"";
    }
      if (document.post.osm_geotag_marker_color.value != "none"){
          StyleColorField = " tagged_color=\""  + document.post.osm_geotag_marker_color.value+ "\"";
    }
    
  
      GenTxt = "[osm_map_v3 map_center=\"" + Centerlonlat.lat + "," + Centerlonlat.lon + "\" zoom=\"" + zoom + "\" width=\"100%\" height=\"450\" " + PostTypeField + MarkerField + MapTypeField + CatFilterField + MapBorderField + MarkerStyleField + StyleColorField + "]"; 

      div = document.getElementById("ShortCode_Div");
      div.innerHTML = GenTxt;
    ';
    }
    else if( $a_msgBox == 'metabox_geometry_sc_gen'){
    $Layer .= ' 
      MarkerField = "";
      ThemeField  = "";
      TypeField   = "";
      MapTypeField = "";

      var MarkerName    = document.post.osm_marker.value;
      if (document.post.osm_map_type.value != "none"){
        MapTypeField = " type=\""+ document.post.osm_map_type.value + "\"";
      }
      if ((document.post.osm_import.value != "none") && (document.post.osm_import.value != "single")){
        TypeField = " import=\""+ document.post.osm_import.value + "\"";
      }
      if (document.post.osm_marker.value != "none"){
        MarkerField = " marker=\""+Clicklonlat.lat+","+Clicklonlat.lon+"\" marker_name=\"" + MarkerName + "\"";  
      }
      if (document.post.osm_theme.value == "dark"){
        ThemeField = " control=\"mouseposition,scaleline\" map_border=\"thin solid grey\" theme=\"dark\"";
      }
      if (document.post.osm_theme.value == "blue"){
        ThemeField = " control=\"mouseposition,scaleline\" map_border=\"thin solid blue\" theme=\"ol\"";  
      }
      if (document.post.osm_theme.value == "orange"){
        ThemeField = " control=\"mouseposition,scaleline\" map_border=\"thin solid orange\" theme=\"ol_orange\"";
      }

      GenTxt = "[osm_map lat=\"" + Centerlonlat.lat + "\" lon=\"" + Centerlonlat.lon + "\" zoom=\"" + zoom + "\" width=\"100%\" height=\"450\" " + ThemeField + MarkerField + TypeField + MapTypeField + "]"; 

      div = document.getElementById("ShortCode_Div");
      div.innerHTML = GenTxt;
    ';
    $Layer .= ' 
      markerslayer.clearMarkers();
      if ((((document.post.osm_import.value == "single") || (document.post.osm_import.value == "none")) || (document.post.osm_mode.value == "geotagging")) && (document.post.osm_marker.value != "none")){
        var icon_Obj = osm_getIconSize(MarkerName);
        var icon_size = new OpenLayers.Size(icon_Obj.width,icon_Obj.height);
        var icon_offset = new OpenLayers.Pixel(icon_Obj.offset_width, icon_Obj.offset_height);
        var icon_url = "'.OSM_PLUGIN_ICONS_URL.'" + MarkerName;
        var click_icon = new OpenLayers.Icon(icon_url,icon_size,icon_offset); 
        var icon_lonlat = new OpenLayers.LonLat(Clicklonlat.lon,Clicklonlat.lat).transform('.$a_MapName.'.displayProjection, '.$a_MapName.'.projection);
        markerslayer.addMarker(new OpenLayers.Marker(icon_lonlat,click_icon.clone()));
      }';
    }
    else if( $a_msgBox == 'metabox_geotag_gen'){
    $Layer .= ' 
      MarkerField = "";

      var MarkerName = document.post.osm_marker_geotag.value;

      if (document.post.osm_marker_geotag.value != "none"){
        MarkerField = " marker=\""+Clicklonlat.lat+","+Clicklonlat.lon+"\" marker_name=\"" + MarkerName + "\"";  
      }

      osm_ajax_object.lat = Clicklonlat.lat;
      osm_ajax_object.lon = Clicklonlat.lon;
      osm_ajax_object.post_id = '.$a_post_id.';
      if (MarkerName != "none"){
        osm_ajax_object.icon = MarkerName;
        GenTxt = "Location: "+Clicklonlat.lat+","+Clicklonlat.lon+" <br>Icon: " + MarkerName + "<br><b>3. Press [Save] to store!</b>";
      }
      else {
        GenTxt = "Location: "+Clicklonlat.lat+","+Clicklonlat.lon + "<br><b>3. Press [Save] to store!</b>";
      }
      div = document.getElementById("Geotag_Div");
      div.innerHTML = GenTxt;
      div02 = document.getElementById("ShortCode_Div");
      div02.innerHTML = "";
    ';
    $Layer .= ' 
      markerslayer.clearMarkers();
      if (document.post.osm_marker_geotag.value != "none"){
        var icon_Obj = osm_getIconSize(MarkerName);
        var icon_size = new OpenLayers.Size(icon_Obj.width,icon_Obj.height);
        var icon_offset = new OpenLayers.Pixel(icon_Obj.offset_width, icon_Obj.offset_height);
        var icon_url = "'.OSM_PLUGIN_ICONS_URL.'" + MarkerName;
        var click_icon = new OpenLayers.Icon(icon_url,icon_size,icon_offset); 
        var icon_lonlat = new OpenLayers.LonLat(Clicklonlat.lon,Clicklonlat.lat).transform('.$a_MapName.'.displayProjection, '.$a_MapName.'.projection);
        markerslayer.addMarker(new OpenLayers.Marker(icon_lonlat,click_icon.clone()));
      }';
    }
    else if( $a_msgBox == 'lat_long'){
      $Layer .= ' alert("Lat= " + Clicklonlat.lat + " Lon= " + Clicklonlat.lon);';   
    }
    $Layer .= ' 	                }';
    $Layer .= ' 	';
    $Layer .= ' 	            });';
    $Layer .= 'var click = new OpenLayers.Control.Click();';
    $Layer .= ''.$a_MapName.'.addControl(click);';
    $Layer .= 'click.activate();';
    return $Layer;
  }

  public static function addMarkerListLayer($a_MapName, $Icon ,$a_MarkerArray, $a_DoPopUp)
  {
    Osm::traceText(DEBUG_INFO, "addMarkerListLayer(".$a_MapName.",".$Icon[name].",".$Icon[width].",".$Icon[height].",".$a_MarkerArray.",".$Icon[offset_width].",".$Icon[offset_height].",".$a_DoPopUp.")");

    $Layer = '';
    $Layer .= 'var MarkerLayer = new OpenLayers.Layer.Markers("Marker");';
    $Layer .= $a_MapName.'.addLayer(MarkerLayer);';

    $Layer .= '
      function osm_'.$a_MapName.'MarkerPopUpClick(a_evt){
        if (this.popup == null){
          this.popup = this.createPopup(this.closeBox);
          '.$a_MapName.'.addPopup(this.popup);
          this.popup.show();
        }
        else{
          for (var i = 0; i < '.$a_MapName.'.popups.length; i++){
          '.$a_MapName.'.popups[i].hide();
          }
          this.popup.toggle();
        }
        OpenLayers.Event.stop(a_evt);
      }
    ';

    $Layer .= 'var '.$a_MapName.'IconArray = [];';

    $NumOfMarker = count($a_MarkerArray);
    for ($row = 0; $row < $NumOfMarker; $row++){

      $Layer .= 'var Mdata = {};';
      $Icon_tmp = $Icon; 
      
      if ($a_MarkerArray[$row][Marker] != ""){
        $IconURL = OSM_PLUGIN_ICONS_URL.$a_MarkerArray[$row][Marker];
        if (Osm_icon::isOsmIcon($a_MarkerArray[$row][Marker]) == 1){
          $Icon_tmp = Osm_icon::getIconsize($a_MarkerArray[$row][Marker]);
        }
        else {
          // set it do invidual marker
          $this->traceText(DEBUG_INFO, "e_not_osm_icon");
          $this->traceText(DEBUG_INFO, $a_MarkerArray[$row][Marker]);
        }
      }
      else {
        $IconURL = OSM_PLUGIN_ICONS_URL.$Icon[name];
      } 
      $Layer .= '
        Mdata.icon = new OpenLayers.Icon("'.$IconURL.'",
          new OpenLayers.Size('.$Icon_tmp[width].','.$Icon_tmp[height].'),
          new OpenLayers.Pixel('.$Icon_tmp[offset_width].', '.$Icon_tmp[offset_height].'));';   
 
      $Layer .= ''.$a_MapName.'IconArray.push(Mdata);'; 
    }

    for ($row = 0; $row < $NumOfMarker; $row++){

      // add the the backslashes
      $OSM_HTML_TEXT = addslashes($a_MarkerArray[$row][text]);

      $Layer .= 'var ll = new OpenLayers.LonLat('.$a_MarkerArray[$row][lon].','.$a_MarkerArray[$row][lat].').transform('.$a_MapName.'.displayProjection, '.$a_MapName.'.projection);';

      $Layer .= 'var feature = new OpenLayers.Feature(MarkerLayer, ll, '.$a_MapName.'IconArray['.$row.']);';         
      $Layer .= 'feature.closeBox = true;';
      $Layer .= 'feature.popupClass = OpenLayers.Class(OpenLayers.Popup.FramedCloud, {"autoSize": true, minSize: new OpenLayers.Size('.$a_MarkerArray[$row][popup_width].','.$a_MarkerArray[$row][popup_height].'),"keepInMap": true } );';      
      $Layer .= 'feature.data.popupContentHTML = "'.$OSM_HTML_TEXT.'";';
      $Layer .= 'feature.data.overflow = "hidden";';

      $Layer .= 'var marker = new OpenLayers.Marker(ll,'.$a_MapName.'IconArray['.$row.'].icon.clone());';

      $Layer .= 'marker.feature = feature;';
      if ($a_DoPopUp == 'true'){
        $Layer .= 'marker.events.register("mousedown", feature, osm_'.$a_MapName.'MarkerPopUpClick);';
      }

      $Layer .= 'MarkerLayer.addMarker(marker);';

      // if there is just one marker, let's pop it up
      if ($a_DoPopUp == 'true'){
        $Layer .= $a_MapName.'.addPopup(feature.createPopup(feature.closeBox));';   // maybe there is a better way to do 
        if ($NumOfMarker > 1){
          $Layer .= 'feature.popup.toggle();';                              // it than create and toggle!
        }
      }
     // if (($a_DoPopUp == 'true')&&($NumOfMarker == 1)){
     //   $Layer .= ''.$a_MapName.'.addPopup(feature.createPopup(feature.closeBox));'; 
     // }
    }
    return $Layer;
  }

//++
  public static function addLineLayer($a_LayerName, $a_MarkerArray)
  {
    Osm::traceText(DEBUG_INFO, "addLineLayer(".$a_LayerName.")");

    $Layer = '';
    $Layer .= 'var LonList = new Array()  ';
    $Layer .= 'var LatList = new Array()  ';
    $NumOfMarker = count($a_MarkerArray);
    for ($ii = 0; $ii < $NumOfMarker; $ii++){
      $Layer .= 'LonList['.$ii.']='.$a_MarkerArray[$ii][lon];
      $Layer .= 'LatList['.$ii.']='.$a_MarkerArray[$ii][lat];
    }
    return $Layer;
  }
//--

    
  public static function addTextLayer($a_MapName, $a_MarkerName, $a_marker_file)
  {
    Osm::traceText(DEBUG_INFO, "addTextLayer(".$a_marker_file.")");   

    $Layer = '';
    $Layer .= 'var pois = new OpenLayers.Layer.Text( "'.$a_MarkerName.'",
               { location:"'.$a_marker_file.'",
                 projection: '.$a_MapName.'.displayProjection
               });';
    $Layer .= $a_MapName.'.addLayer(pois);';
    return $Layer; 
  } 

/// discs
  public static  function addDiscs($centerListArray,$radiusListArray,$centerOpacityListArray,$centerColorListArray,
                     $borderWidthListArray,$borderColorListArray,$borderOpacityListArray,$fillColorListArray,$fillOpacityListArray,$a_MapName) {

   $layer ='var discLayer = new OpenLayers.Layer.Vector("Disc Layer");';

   for($i=0;$i<sizeof($centerListArray);$i++){
    // $centerListArray[$i] = lon lat -> lon,lat
    // only center and radius must be defined for each disc to be shown, else use first/default value (ie [0])
    $layer .= 'osm_getFeatureDiscCenter('.$a_MapName.', discLayer,'.implode(",",explode( " ", trim($centerListArray[$i]) )).', '.$radiusListArray[$i].', '.
                      ((isset($centerOpacityListArray[$i]))? $centerOpacityListArray[$i] : $centerOpacityListArray[0]).', "'.
                      ((isset($centerColorListArray[$i]))?   $centerColorListArray[$i]   : $centerColorListArray[0]).'", '.
                      ((isset($borderWidthListArray[$i]))?   $borderWidthListArray[$i]   : $borderWidthListArray[0]).', "'.
                      ((isset($borderColorListArray[$i]))?   $borderColorListArray[$i]   : $borderColorListArray[0]).'", '.
                      ((isset($borderOpacityListArray[$i]))? $borderOpacityListArray[$i] : $borderOpacityListArray[0]).', "'.
                      ((isset($fillColorListArray[$i]))?     $fillColorListArray[$i]     : $fillColorListArray[0]).'", '.
                      ((isset($fillOpacityListArray[$i]))?   $fillOpacityListArray[$i]   : $fillOpacityListArray[0]).');';
    }
    $layer.=' '.$a_MapName.'.addLayer(discLayer);';
    return $layer;
   }
 /// end discs 
// lines ++
   public static function addLines($PointListArray,$a_LineColor,$a_LineWidth, $a_MapName)  
   {   
     $layer = '';
     $layer.='var lineLayer = new OpenLayers.Layer.Vector("Line Layer");';
     $layer.='var Points = new Array();';
     $layer.='var lineWidth = '.$a_LineWidth.';';
     $layer.='var lineColor = "'.$a_LineColor.'";';

     for($i=0;$i<sizeof($PointListArray);$i++){
       $layer.='Points['.$i.'] = new Object();';
       $layer.='Points['.$i.']["lon"] = '.$PointListArray[$i][lon].';';
       $layer.='Points['.$i.']["lat"] = '.$PointListArray[$i][lat].';';
     }
     $layer.=' osm_setLinePoints('.$a_MapName.', lineLayer, lineWidth, lineColor, 0.7, Points);';
     $layer.=' '.$a_MapName.'.addLayer(lineLayer);';

     return $layer;
   }
// //lines --

  public static function setMapCenterAndZoom($a_MapName, $a_lat, $a_lon, $a_zoom)
  {
    Osm::traceText(DEBUG_INFO, "setMapCenterAndZoom(".$a_lat.",".$a_lon.",".$a_zoom.")");
    $Layer = '';

    if (strtolower($a_zoom) == ('auto')){
      $a_zoom = 'null';
    }
    if ((strtolower($a_lat) == ('auto')) || (strtolower($a_lon) == ('auto'))) {
      $Layer .= 'var lonLat = null;';
    }
    else {
      $Layer .= 'var lonLat = new OpenLayers.LonLat('.$a_lon.','.$a_lat.').transform('.$a_MapName.'.displayProjection, '.$a_MapName.'.projection);';
    }
    
    $Layer .= ''.$a_MapName.'.setCenter (lonLat,'.$a_zoom.');'; // Zoomstufe einstellen
    return $Layer;
  } 


  public static function setGoogleMapCenterAndZoom($a_MapName, $a_lat, $a_lon, $a_zoom)
  {
    Osm::traceText(DEBUG_INFO, "setGoogleMapCenterAndZoom(".$a_lat.",".$a_lon.",".$a_zoom.")");
    $Layer = '';

    if (strtolower($a_zoom) == ('auto')){
      $a_zoom = 'null';
    }
    if ((strtolower($a_lat) == ('auto')) || (strtolower($a_lon) == ('auto'))) {
      $Layer .= 'var lonLat = null;';
    }
    else {
      $Layer .= 'var lonLat = new OpenLayers.LonLat('.$a_lon.','.$a_lat.').transform("EPSG:4326", '.$a_MapName.'.projection);';
    }
    
    $Layer .= ''.$a_MapName.'.setCenter (lonLat,'.$a_zoom.');'; // Zoomstufe einstellen
    return $Layer;
  } 
      
  // check the map-type, remove whit space and replace Osnmarender
 public static  function checkMapType($a_type){
    // Osmarender is replaced by Mapnik
    if ($a_type == 'Osmarender'){
      return "Mapnik";
  }
  elseif($a_type == 'GooglePhysical' || $a_type == 'GoogleStreet' || $a_type == 'GoogleHybrid' || $a_type == 'GoogleSatellite') {
    return 'Mapnik';
  }

    if ($a_type != 'Mapnik' && $a_type != 'mapnik_ssl' && $a_type != 'CycleMap' && $a_type != 'OpenSeaMap' && $a_type != 'stamen_watercolor' && $a_type != 'stamen_toner' && $a_type != 'OpenWeatherMap' && $a_type != 'OSMRoadsMap' && $a_type != 'basemap_at' && $a_type != 'Google' && $a_type != 'All' && $a_type != 'AllGoogle' && $a_type != 'AllOsm' && $a_type != 'ext' && $a_type != 'Ext'){
      return "AllOsm";
    }
    // eg "Google Hybrid" => "GoogleHybrid"
    $type = preg_replace('/\s+/', '', $a_type);
    return $type;
  }

  // check the num of zoomlevels
 public static function checkOverviewMapZoomlevels($a_Zoomlevels){
    if ( $a_Zoomlevels > 17){
      Osm::traceText(DEBUG_ERROR, "Zoomlevel out of range!");
      return 0;
    }
    return $a_Zoomlevels;
  }     
  
  public static function checkControlType($a_MapControl){
    foreach ( $a_MapControl as $MapControl ){
	  Osm::traceText(DEBUG_INFO, "Checking the Map Control");
	  $MapControl = strtolower($MapControl);
	  if (( $MapControl != 'scaleline') && ($MapControl != 'scale') && ($MapControl != 'no') && ($MapControl != 'mouseposition') && ($MapControl != 'off')) {
	    Osm::traceText(DEBUG_ERROR, "e_invalid_control");
	    $a_MapControl[0]='No';
	  }
    }
    return $a_MapControl;
  }
  
}
?>
