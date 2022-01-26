<?php
/*  (c) Copyright 2021  MiKa (wp-osm-plugin.HyuMiKa.com)

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

class Osm_OLJS3
{
  public static function addVectorLayer($a_MapName, $a_FileName, $a_Colour, $a_Type, $a_Counter, $a_MarkerName, $a_showMarkerName, $a_title, $a_file_param)
  {
    Osm::traceText(DEBUG_INFO, "addVectorLayer V3(".$a_MapName.",".$a_Type.",".$a_FileName.")");
    $VectorLayer = '';
    $VectorLayer .= '
    var style'.$a_Counter.' = {
      "Point": [new ol.style.Style({

          image: new ol.style.Icon({
            anchor: [0.5, 41],
            anchorXUnits: "fraction",
            anchorYUnits: "pixels",
            opacity: 0.75,
            src: "'.OSM_PLUGIN_ICONS_URL.$a_MarkerName.'"
          })
      })],

      "LineString": [new ol.style.Style({
        stroke: new ol.style.Stroke({
          color: "'.$a_Colour.'",
          width: 8
        })
      })],
      "MultiLineString": [new ol.style.Style({
        stroke: new ol.style.Stroke({
          color: "'.$a_Colour.'",
          width: 4
        })
      })]
    };';


	/** if no map_title is given, plugin output remains the same */
	if (empty($a_title)) {

		if (($a_Type == 'kml') &&  ($a_file_param == 'cluster')) {

        $kml_marker_name = "mic_blue_pinother_02.png";
		  if ($a_Colour == "blue"){$kml_marker_name = "mic_blue_pinother_02.png";}
		  else if ($a_Colour == "red"){$kml_marker_name = "mic_red_pinother_02.png";}
		  else if ($a_Colour == "green"){$kml_marker_name = "mic_green_pinother_02.png";}
		  else if ($a_Colour == "black"){$kml_marker_name = "mic_black_pinother_02.png";}

        $bordercolor = cOsm_arguments::getBorderColor($a_Colour);
        $innercolor  = cOsm_arguments::getInnerColor($a_Colour);

		  $VectorLayer .= '  
var KMLclusterSource = new ol.source.Cluster({
        distance: 15,
        geometryFunction: function (feature) {
          var geometry = feature.getGeometry();  
          if (geometry.getType() === "Point") {
            return geometry;
          } else{
            return geometry.getPoint(0);
          }
                 
        },
        source: new ol.source.Vector({
                url: "../../../../wp-content/uploads/osm/C22.kml",
                format: new ol.format.KML({
                extractStyles:false,
            }),
        })
    });  
  

var styleCache = {};


var KMLMarker = new ol.style.Style({
  image: new ol.style.Icon(({
    anchor: [(-16*-1),(-41*-1)],
    anchorXUnits: "pixels",
    anchorYUnits: "pixels",
    opacity: 0.9,
	 src: "http://www.localhost/wordpress/wp-content/plugins/osm/icons/'.$kml_marker_name.'"
  }))
});  
    

vectorL'.$a_Counter.' = new ol.layer.Vector({
  source: KMLclusterSource,
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
		         color: '.$bordercolor.',
		         width: 6,
	          }),
	          fill: new ol.style.Fill({
	  	       color: '.$innercolor.'
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
	    style = KMLMarker;
		 styleCache[size] = style;
	   }
		return style;
	}
  } 

});

	 ';
		}
		else if ($a_Type == 'kml'){
		  $VectorLayer .= '
		  vectorL'.$a_Counter.' = new ol.layer.Vector({

			source: new ol.source.Vector({
		  url:"'. trim($a_FileName).'",
			  format: new ol.format.KML({ showPointNames: '.$a_showMarkerName.'})
			}),
         zIndex: 92
		  });';
		}


		if ($a_Type == 'gpx'){
		  $VectorLayer .= '
		  var vectorL'.$a_Counter.' = new ol.layer.Vector({
				source: new ol.source.Vector({
				url:"' . trim($a_FileName) . '",
				format: new ol.format.GPX({
					extractStyles: false
				})
			}),
			zIndex: 92,
			style: function(feature, resolution) {return style'.$a_Counter.'[feature.getGeometry().getType()];}
		  });
		  ';
		}

		$VectorLayer .= $a_MapName .'.addLayer(vectorL'.$a_Counter.');';

	  } else {

		/** titles for layers are give, so let's start the magic :-)
		 * vectorM is a global array
		 * no adding of layer at the end of this script
		 */
		if ($a_Type == 'kml'){
			$VectorLayer .= '
				vectorM[\'' . $a_MapName . '\'][' . $a_Counter . '] = new ol.layer.Vector({
					options: {title: "overlay' . $a_Counter . '"},
					source: new ol.source.Vector({
						url:"' . trim($a_FileName) . '",
						format: new ol.format.KML({ showPointNames: ' . $a_showMarkerName . '})
					}),
               zIndex: 92
				});'
			;
		}
		if ($a_Type == 'gpx'){
			$VectorLayer .= '
				vectorM[\'' . $a_MapName . '\'][' . $a_Counter . '] = new ol.layer.Vector({
					options: {title: "overlay' . $a_Counter . '"},
					source: new ol.source.Vector({
						url:"' . trim($a_FileName) . '",
						
						format: new ol.format.GPX({
							extractStyles: false
						})
					}),
					zIndex: 92,
					style: function(feature, resolution) {return style' . $a_Counter . '[feature.getGeometry().getType()];}
				 });'
				;
			}
		}
	return $VectorLayer;
	}
}
