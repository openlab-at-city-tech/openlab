/*  (c) Copyright 2019  MiKa (http://wp-osm-plugin.HanBlog.Net)

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

