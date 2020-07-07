<?php
/*  (c) Copyright 2020  MiKa (wp-osm-plugin.HanBlog.Net)

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
  public static function addTileLayer($a_LayerName, $a_Type, $a_OverviewMapZoom, $a_MapControl, $a_WMSType, $a_WMSAttrName, $a_WMSAttrUrl, $a_WMSAddress, $a_WMSParam, $a_theme, $a_api_key){
    Osm::traceText(DEBUG_INFO, "addTileLayer V3(".$a_LayerName.",".$a_Type.",".$a_OverviewMapZoom.")");
    $TileLayer = '

	var attribution = new ol.control.Attribution({
        collapsible: false
      });


      ';
    if(($a_Type == "osm") || ($a_Type == "brezhoneg")) {
      $TileLayer .= '
      var raster = new ol.layer.Tile({
        source: new ol.source.OSM({ }),
        zIndex: 90
      });';
    }
    if ($a_Type == "hot"){
      $TileLayer .= '
          var raster = new ol.layer.Tile({
            source: new ol.source.OSM({
               attributions: "Maps &copy; " +
               "<a href=\"http://hot.openstreetmap.org/\">Humanitarian OpenStreetMap Team.</a>" + ol.source.OSM.ATTRIBUTION,
               url: "https://{a-c}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png"
             }),
             zIndex: 90
           });
          ';
    }
    if ($a_Type == "opentopomap"){
      $TileLayer .= '
          var raster = new ol.layer.Tile({
            source: new ol.source.XYZ({
               attributions: "Kartendarstellung: &copy;" + "<a href=\"https://opentopomap.org\">OpenTopoMap</a> (<a href=\"https://creativecommons.org/licenses/by-sa/3.0/\">CC-BY-SA</a>)" + "Maps &copy; " +
               "<a href=\"http://viewfinderpanoramas.org\">SRTM</a>" + ol.source.OSM.ATTRIBUTION,
               url: "'.Osm_OpenTopoMap_Tiles.'"
             }),
             zIndex: 90
           });
          ';
    }
             


    else if ($a_Type == "stamen_toner"){
      $TileLayer .= '
      var raster = new ol.layer.Tile({
        source: new ol.source.Stamen({
            layer: "toner"
          }),
          zIndex: 90
        });
      ';
  }
      else if ($a_Type == "stamen_watercolor"){
      $TileLayer .= '
      var raster = new ol.layer.Tile({
        source: new ol.source.Stamen({
            layer: "watercolor"
          }),
          zIndex: 90
        });
      ';
      }
    else if ($a_Type == "stamen_terrain"){
      $TileLayer .= '
      var raster = new ol.layer.Tile({
        source: new ol.source.Stamen({
            layer: "terrain"
          }),
          zIndex: 90
        });
      ';
  }
      else if ($a_Type == "stamen_terrain-labels"){
        $TileLayer .= '
        var raster = new ol.layer.Tile({
          source: new ol.source.Stamen({
          	layer: "terrain-labels"}),
          zIndex: 90
        });';
      }
      else if ($a_Type == "tilewms"){
        $TileLayer .= '
        var raster = new ol.layer.Tile({
          extent: [-13884991, 2870341, -7455066, 6338219],
          source: new ol.source.TileWMS({
            attributions: "Maps &copy; " +
              "<a href=\"http://www.HanBlog.Net/\">WP OSM Plugin</a> and &copy; " +
              "<a href=\"http://'.$a_WMSAttrUrl.'/\">'.$a_WMSAttrName.'</a>",
            url: "'.$a_WMSAddress.'",
            params: {'.$a_WMSParam.'},
            serverType: "'.$a_WMSType.'"
          }),
          zIndex: 90
        });';
      }
      else if ($a_Type == "openseamap"){
        $TileLayer .= '
          var raster = new ol.layer.Tile({
            source: new ol.source.OSM(),
            zIndex: 90
          });
          var Layer2 = new ol.layer.Tile({
            source: new ol.source.OSM({
              attributions: "Maps &copy; " +
              "<a href=\"http://www.openseamap.org/\">OpenSeaMap</a>",
              crossOrigin: null,
              url: "'.Osm_OpenSeaMap_Tiles.'"
            }),
            className: "ol-openseamap",
            zIndex: 91
          });';
    }
	      else if ($a_Type == "cyclemap"){
        $TileLayer .= '
          var raster = new ol.layer.Tile({
            source: new ol.source.OSM({
              attributions: "Maps &copy; " +
              "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
              url: "'.Osm_thunderforest_Cycle_Tiles.'?apikey='.$a_api_key.'"
             }),
             zIndex: 90
           });
          ';
    }
    else if ($a_Type == "outdoor"){
        $TileLayer .= '
          var raster = new ol.layer.Tile({
            source: new ol.source.OSM({
              attributions: "Maps &copy; " +
              "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
              url: "'.Osm_thunderforest_Outdoor_Tiles.'?apikey='.$a_api_key.'"
             }),
             zIndex: 90
           });
          ';
    }

    else if ($a_Type == "landscape"){
        $TileLayer .= '
          var raster = new ol.layer.Tile({
            source: new ol.source.OSM({
              attributions: "Maps &copy; " +
              "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
              url: "'.Osm_thunderforest_Landscape_Tiles.'?apikey='.$a_api_key.'"
             }),
             zIndex: 90
           });
          ';
    }

    else if ($a_Type == "spinal"){
        $TileLayer .= '
          var raster = new ol.layer.Tile({
            source: new ol.source.OSM({
              attributions: "Maps &copy; " +
              "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
              url: "'.Osm_thunderforest_Spinal_Tiles.'?apikey='.$a_api_key.'"
             }),
             zIndex: 90
           });
          ';
    }
     else if ($a_Type == "pioneer"){
        $TileLayer .= '
          var raster = new ol.layer.Tile({
            source: new ol.source.OSM({
               attributions: "Maps &copy; " +
               "<a href=\"http://www.thunderforest.com/\">Thunderforest, Data.</a>" + ol.source.OSM.ATTRIBUTION,
               url: "'.Osm_thunderforest_Pioneer_Tiles.'?apikey='.$a_api_key.'"
             }),
             zIndex: 90
           });
          ';
    }
/*
Server seams to be down
    else if ($a_Type == "brezhoneg"){
         $TileLayer .= '
           var raster = new ol.layer.Tile({
             source: new ol.source.OSM({
               attributions: [
                new ol.Attribution({
               html: "&copy; Les contributeurs OSM"
                }),
                ol.source.OSM.ATTRIBUTION
                ],
              url: "http://tile-b.openstreetmap.fr/bzh/{z}/{x}/{y}.png"
              }),
              zIndex: 90
            });
    ';
    }
*/
    else if ($a_Type == "basemap_at"){

        $TileLayer .= '
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

';
      }

      else {// unknwon => OSM map
        $TileLayer .= '
        var raster = new ol.layer.Tile({
          source: new ol.source.OSM()
        });';
     }
    if (!empty($a_MapControl)){
        $FirstString = $a_MapControl[0];
        if ((strtolower($FirstString)== 'fullscreen')) {
          $TileLayer .= '
            var Controls = ol.control.defaults({ attribution: false }).extend([
            new ol.control.FullScreen(),
            attribution
            ]); ';
        }
    }
    else {
      $TileLayer .= '
      var Controls = ol.control.defaults({ attribution: false }).extend([
          attribution
      ]); ';
}
      return $TileLayer;
  }

  public static function addVectorLayer($a_MapName, $a_FileName, $a_Colour, $a_Type, $a_Counter, $a_MarkerName, $a_showMarkerName, $a_title)
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

		if ($a_Type == 'kml'){
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
						}),
                  zIndex: 92
					}),
					style: function(feature, resolution) {return style' . $a_Counter . '[feature.getGeometry().getType()];}
				 });'
				;
			}
		}

	return $VectorLayer;
	}
}
