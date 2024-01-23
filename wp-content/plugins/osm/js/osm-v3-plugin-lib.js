/*  (c) Copyright 2022  MiKa (http://wp-osm-plugin.Hyumika.com)

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
 
function osm_addClusterPopupClickhandler(a_MapObj, a_MapStr) {

  var container_div_id = a_MapStr + "_popup";
  var content_div_id = a_MapStr + "_popup-content";
  var closer_div_id = a_MapStr + "_popup-closer";

  var container = document.getElementById(container_div_id);
  var content = document.getElementById(content_div_id);
  var closer = document.getElementById(closer_div_id);

  var popup = new ol.Overlay(/** @type {olx.OverlayOptions} */ ({
    element: container,
    autoPan: true,
    autoPanAnimation: {
      duration: 250
    }
  }));
        
  closer.onclick = function() {
    popup.setPosition(undefined);
    closer.blur();
    return false;
  };
            
  a_MapObj.addOverlay(popup);

  var ClickdisplayFeatureInfo = function(a_evt) {
    var lonlat = ol.proj.transform(a_evt.coordinate, "EPSG:3857", "EPSG:4326");
    var lon = lonlat[0];
    var lat = lonlat[1];

	 pixel = a_evt.pixel;
		
    var features = [];
    var NumOfNamedFeatures = 0;
    a_MapObj.forEachFeatureAtPixel(pixel, function(feature, layer) {
      features.push(feature);
    });
    if (features.length > 0) {
      
      var name_str, desc_str, info = [];
      var description_str = [];
      var i, ii;
      for (i = 0, ii = features.length; i < ii; ++i) {
        var cluster_features = features[i].get('features');
        if (cluster_features.length == 1){
        if (cluster_features[i].get("name")){
          NumOfNamedFeatures++;
          name_str = cluster_features[i].get("name");
          desc_str = cluster_features[i].get("desc");
          description_str = features[i].get("description");
          if (desc_str != undefined){
            name_str = name_str + "<br>" + desc_str;
          }
          if (description_str != undefined){
            name_str = name_str + "<br>" + description_str;
          }
          if (cluster_features[i].length > 0) {name_str = name_str + "<br>"}
        }
        else{

        }
        info.push(name_str);
      }
      }

        content.innerHTML = info.join("") || "(unknown)";
        if (NumOfNamedFeatures > 0){
          popup.setPosition(a_evt.coordinate);
        }
         else {
           popup.setPosition(undefined);
        }
      } 
    };
    a_MapObj.on("singleclick", function(evt) {ClickdisplayFeatureInfo(evt);}); 
}



function osm_addPopupClickhandler(a_MapObj, a_MapStr) {
  var container_div_id = a_MapStr + "_popup";
  var content_div_id = a_MapStr + "_popup-content";
  var closer_div_id = a_MapStr + "_popup-closer";

  var container = document.getElementById(container_div_id);
  var content = document.getElementById(content_div_id);
  var closer = document.getElementById(closer_div_id);

  var popup = new ol.Overlay(/** @type {olx.OverlayOptions} */ ({
    element: container,
    autoPan: true,
    autoPanAnimation: {
      duration: 250
    }
  }));
        
  closer.onclick = function() {
    popup.setPosition(undefined);
    closer.blur();
    return false;
  };
            
  a_MapObj.addOverlay(popup);

  var ClickdisplayFeatureInfo = function(a_evt) {
    var lonlat = ol.proj.transform(a_evt.coordinate, "EPSG:3857", "EPSG:4326");
    var lon = lonlat[0];
    var lat = lonlat[1];

	 pixel = a_evt.pixel;
		
    var features = [];
    var NumOfNamedFeatures = 0;
    a_MapObj.forEachFeatureAtPixel(pixel, function(feature, layer) {
      features.push(feature);
    });
    if (features.length > 0) {
      var name_str, desc_str, info = [];
      var description_str = [];
      var i, ii;
      for (i = 0, ii = features.length; i < ii; ++i) {
        if (features[i].get("name")){
          NumOfNamedFeatures++;
          name_str = features[i].get("name");
          desc_str = features[i].get("desc");
          description_str = features[i].get("description");
          if (desc_str != undefined){
            name_str = name_str + "<br>" + desc_str;
          }
          if (description_str != undefined){
            name_str = name_str + "<br>" + description_str;
          }
          if (features.length > 0) {name_str = name_str + "<br>"}
        }
        else{
          //name_str = "empty";
        }
        info.push(name_str);
      }

        content.innerHTML = info.join("") || "(unknown)";
        if (NumOfNamedFeatures > 0){
          popup.setPosition(a_evt.coordinate);
        }
         else {
           popup.setPosition(undefined);
        }
      } 
    };
    a_MapObj.on("singleclick", function(evt) {ClickdisplayFeatureInfo(evt);}); 
}




function osm_addMarkerLayer(a_mapname, a_lon, a_lat, a_MarkerIcon, a_MarkerXAnchor, a_MarkerYAnchor, a_MarkerText) {
  var iconFeature = new ol.Feature({
    geometry: new ol.geom.Point(
      ol.proj.transform([a_lon,a_lat], "EPSG:4326", "EPSG:3857")),
    name: a_MarkerText
  });
  var iconStyle = new ol.style.Style({
    image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
      anchor: [(a_MarkerXAnchor * -1),(a_MarkerYAnchor * -1)],
      anchorXUnits: "pixels",
      anchorYUnits: "pixels",
      opacity: 0.9,
      src: a_MarkerIcon
    }))
  });
  iconFeature.setStyle(iconStyle);

  var vectorMarkerSource = new ol.source.Vector({
    features: [iconFeature]
  });

  var vectorMarkerLayer = new ol.layer.Vector({
    source: vectorMarkerSource,
    zIndex: 92
  });

  a_mapname.addLayer(vectorMarkerLayer);
}

function osm_addMouseHover(a_mapname){
  a_mapname.on('pointermove', function(evt) {
    a_mapname.getTargetElement().style.cursor =
      a_mapname.hasFeatureAtPixel(evt.pixel) ? 'pointer' : '';
  });
}

function getTileLayer(a_source, a_api_key) {

/* ++++++++ quickfix since stamen is now hosted by Stadia Maps */
  if ((a_source == "stamen_toner") || (a_source == "stamen_watercolor") || (a_source == "stamen_terrain")||(a_source == "stamen_terrain-labels")){
    a_source = "osm";
  } 
/*  ------- */

  if ((a_source == "osm") || (a_source == "brezhoneg")||(a_source == "openseamap")){
   return new ol.layer.Tile({
        source: new ol.source.OSM({ }),
        zIndex: 90
      });
  }
  else if (a_source == "hot"){
      return new ol.layer.Tile({
            source: new ol.source.OSM({
               attributions: "Maps &copy; " +
               "<a href=\"http://hot.openstreetmap.org/\">Humanitarian OpenStreetMap Team.</a>" + ol.source.OSM.ATTRIBUTION,
               url: "https://{a-c}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png"
             }),
             zIndex: 90
           });
    }	
	 else if (a_source == "opentopomap"){
      return new ol.layer.Tile({
            source: new ol.source.XYZ({
               attributions: "Kartendarstellung: &copy;" + "<a href=\"https://opentopomap.org\">OpenTopoMap</a> (<a href=\"https://creativecommons.org/licenses/by-sa/3.0/\">CC-BY-SA</a>)" + "Maps &copy; " +
               "<a href=\"http://viewfinderpanoramas.org\">SRTM</a>" + ol.source.OSM.ATTRIBUTION,
               url: "https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png"
             }),
             zIndex: 90
           });
    }
    else if (a_source == "stamen_toner"){
      return new ol.layer.Tile({
        source: new ol.source.Stamen({
            layer: "toner"
          }),
          zIndex: 90
        });
     }
	  else if (a_source == "stamen_watercolor"){
      return new ol.layer.Tile({
        source: new ol.source.Stamen({
            layer: "watercolor"
          }),
          zIndex: 90
        });
      }
      else if (a_source == "stamen_terrain"){
      return new ol.layer.Tile({
        source: new ol.source.Stamen({
            layer: "terrain"
          }),
          zIndex: 90
        });
      }
      else if (a_source == "stamen_terrain-labels"){
        return new ol.layer.Tile({
          source: new ol.source.Stamen({
          	layer: "terrain-labels"}),
          zIndex: 90
        });
      }  
	  else if (a_source == "cyclemap"){
       return new ol.layer.Tile({
            source: new ol.source.OSM({
              attributions: "Maps &copy; " +
              "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
              url:   "https://{a-c}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png?apikey="+ a_api_key                           
             }),
             zIndex: 90
           });
     }
     else if (a_source == "outdoor"){
          return new ol.layer.Tile({
            source: new ol.source.OSM({
              attributions: "Maps &copy; " +
              "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
              url: "https://{a-c}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png?apikey="+ a_api_key
             }),
             zIndex: 90
           });
      }

    else if (a_source == "landscape"){
      return new ol.layer.Tile({
        source: new ol.source.OSM({
        attributions: "Maps &copy; " +
              "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
              url: "https://{a-c}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png?apikey="+ a_api_key
             }),
             zIndex: 90
           });
    }

    else if (a_source == "spinal"){
          return new ol.layer.Tile({
            source: new ol.source.OSM({
              attributions: "Maps &copy; " +
              "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
              url: "https://{a-c}.tile.thunderforest.com/spinal-map/{z}/{x}/{y}.png?apikey="+ a_api_key
             }),
             zIndex: 90
           });
    }
     else if (a_source == "pioneer"){
          return new ol.layer.Tile({
            source: new ol.source.OSM({
               attributions: "Maps &copy; " +
               "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
               url: "https://{a-c}.tile.thunderforest.com/pioneer/{z}/{x}/{y}.png?apikey="+ a_api_key
             }),
             zIndex: 90
           });
          
    }

    else if (a_source == "basemap_at"){

      var template = "{Layer}/{Style}/{TileMatrixSet}/{TileMatrix}/{TileRow}/{TileCol}.png";
      var urls_basemap = [
        "https://maps1.wien.gv.at/basemap/" + template,
        "https://maps2.wien.gv.at/basemap/" + template,
        "https://maps3.wien.gv.at/basemap/" + template,
        "https://maps4.wien.gv.at/basemap/" + template,
        "https://maps.wien.gv.at/basemap/" + template
      ];

      // HiDPI support:
      // * Use "bmaphidpi" layer (pixel ratio 2) for device pixel ratio > 1
      // * Use "geolandbasemap" layer (pixel ratio 1) for device pixel ratio == 1
      var hiDPI = ol.has.DEVICE_PIXEL_RATIO > 1;

      var source_basemap = new ol.source.WMTS({
        projection: "EPSG:3857",
        //layer: hiDPI ? "bmaphidpi" : "geolandbasemap",
        layer: "geolandbasemap",
        tilePixelRatio: hiDPI ? 2 : 1,
        style: "normal",
        matrixSet: "google3857",
        urls: urls_basemap,
        requestEncoding: "REST",
        tileGrid: new ol.tilegrid.WMTS({
          origin: [-20037508.3428, 20037508.3428],
            resolutions: [
            559082264.029 * 0.28E-3,
            279541132.015 * 0.28E-3,
            139770566.007 * 0.28E-3,
            69885283.0036 * 0.28E-3,
            34942641.5018 * 0.28E-3,
            17471320.7509 * 0.28E-3,
            8735660.37545 * 0.28E-3,
            4367830.18773 * 0.28E-3,
            2183915.09386 * 0.28E-3,
            1091957.54693 * 0.28E-3,
            545978.773466 * 0.28E-3,
            272989.386733 * 0.28E-3,
            136494.693366 * 0.28E-3,
            68247.3466832 * 0.28E-3,
            34123.6733416 * 0.28E-3,
            17061.8366708 * 0.28E-3,
            8530.91833540 * 0.28E-3,
            4265.45916770 * 0.28E-3,
            2132.72958385 * 0.28E-3,
            1066.36479193 * 0.28E-3
            ],
            matrixIds: [
              0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19
            ]
       }),

       attributions: "Tiles &copy; " +
       "<a href=\"http://www.basemap.at/\">basemap.at</a>" + ol.source.OSM.ATTRIBUTION,
   });


    return new ol.layer.Tile({
				 extent: [977844.377599999, 5837774.6617, 1915609.8654, 6295560.8122],
				 source: source_basemap
			   })

      }

      else {// unknwon => OSM map
        return new ol.layer.Tile({
          source: new ol.source.OSM()
        });
     }      
}

function getVectorClusterLayer(a_vectorMarkerSource, a_taggedborderColor, a_taggedinnerColor, a_IconURL, a_IconOffsetwidth, a_IconOffsetheight) {
			
  var clusterSource = new ol.source.Cluster({
    distance: 30,
    source: a_vectorMarkerSource,
    zIndex: 92
  });
  
  var styleCache = {};
  var vectorMarkerLayer = new ol.layer.Vector({
    source: clusterSource,
	 zIndex: 92,
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
					  color: a_taggedborderColor,
					  width: 6,
				   }),
				   fill: new ol.style.Fill({
					  color: a_taggedinnerColor
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
					anchor: [(a_IconOffsetwidth*-1),(a_IconOffsetheight*-1)],
					anchorXUnits: "pixels",
					anchorYUnits: "pixels",
					opacity: 0.9,
					src: a_IconURL}))
			  })];
			  styleCache[size] = style;
			}
			return style;
			}
		  }
		});
		return vectorMarkerLayer;
				
}

function addControls2Map(a_map, MousePosition, OverviewMap, Rotate,Scaleline,Zoom,ZoomSlider,ZoomToExtent,FullScreen, Attribution) {
  
  var osm_controls = [
		new ol.control.Attribution(),
		new ol.control.MousePosition({
		  undefinedHTML: "outside",
			projection: "EPSG:4326",
			coordinateFormat: function(coordinate) {
			  return ol.coordinate.format(coordinate, "{y}, {x}", 5);
			}
		}),
		new ol.control.OverviewMap({
        layers: [
          new ol.layer.Tile({
            source: new ol.source.OSM(),
          }),
        ],
        collapsed: false,
		 }),
		new ol.control.Rotate({
		  autoHide: false
		}),
		new ol.control.ScaleLine(),
		new ol.control.Zoom(),
		new ol.control.ZoomSlider(),
		new ol.control.ZoomToExtent({
        extent: [-11243808.051695308, 1.202710291, 9561377.290892059, 6852382.107835932]
      }),
		new ol.control.FullScreen()
	]; 	
	
	if (Scaleline == 1){a_map.addControl(osm_controls[4]);}
   if (MousePosition == 1){a_map.addControl(osm_controls[1]);}
   if (OverviewMap == 1){a_map.addControl(osm_controls[2]);}
	if (FullScreen == 1){a_map.addControl(osm_controls[8]);}	 
	if (Attribution == 0){
     a_map.getControls().forEach(function(control) {
       if (control instanceof ol.control.Attribution) {
         a_map.removeControl(control);
       }
     }, this);	
   }	
	  
}

