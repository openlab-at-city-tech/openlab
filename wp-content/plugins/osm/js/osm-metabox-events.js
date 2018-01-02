/*
  OSM OpenLayers for OpenStreetMap wordpress plugin
  plugin: http://wp-osm-plugin.HanBlog.net
  blog:   http://www.HanBlog.net
  author: MiKa
*/

// This namespace covers the eventhandler in the metabox to
// generate the shortcodes
var MetaboxEventhandler = {

  MarkerSC: function osm_addMarkerMapEvent(a_mapname) {
    a_mapname.on('click', function(evt) {
	  var lonlat = ol.proj.transform(a_mapname.getView().getCenter(), "EPSG:3857", "EPSG:4326");
	  var lon = lonlat[0].toFixed(4);
      var lat = lonlat[1].toFixed(4);
	  var zoom = a_mapname.getView().getZoom();

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
      GenTxt = "[osm_map_v3 map_center=\"" + lat + "," + lon + "\" zoom=\"" + zoom + "\" width=\"100%\" height=\"450\" " + BorderField + MarkerId + MapTypeField + ControlField +"]";

      div = document.getElementById("ShortCode_Div");
      div.innerHTML = GenTxt;
    });
  },

	FileSC: function osm_addFilesMapEvent(a_mapname) {
	  a_mapname.on('click', function(evt) {
	  var lonlat = ol.proj.transform(a_mapname.getView().getCenter(), "EPSG:3857", "EPSG:4326");
	  var lon = lonlat[0].toFixed(4);
      var lat = lonlat[1].toFixed(4);
	  var zoom = a_mapname.getView().getZoom();

      var FileList_ColorField  = "";
      var FileList_TypeField   = "";
      var FileList_MapTypeField = "";
      var FileList_FileField = "";
	    var FileList_TitleField = "";
		  var FileList_SelectBoxField = "";
      var DisplayName = "";
      var Controls = "";
      var ControlField ="";
      BorderField = "";
	    fileUrls = [];
	    fileTitles = [];
	    fileColors = [];

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
			if (document.post.show_selection_box.checked){
        FileList_SelectBoxField = " file_select_box=\"one\"";
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
				else if ((fileUrls[i] != "") && (typeof fileUrls[i] !== "undefined")) {
				  var filename = fileUrls[i];
					var filename = filename.replace(/^.*[\\\/]/, '')
          fileTitles.push(filename);
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

	  GenTxt = "[osm_map_v3 map_center=\"" + lat + "," + lon + "\" zoom=\"" + zoom + "\" width=\"100%\" height=\"450\" " + FileList_FileField + FileList_MapTypeField + FileList_ColorField + DisplayName + ControlField + BorderField + FileList_TitleField + FileList_SelectBoxField +"]";

      div = document.getElementById("ShortCode_Div");
      div.innerHTML = GenTxt;
	});
    },

	TaggedPostsSC: function osm_addTaggedPostsMapEvent(a_mapname) {
	  a_mapname.on('click', function(evt) {
	  var lonlat = ol.proj.transform(a_mapname.getView().getCenter(), "EPSG:3857", "EPSG:4326");
	  var lon = lonlat[0].toFixed(4);
      var lat = lonlat[1].toFixed(4);
	  var zoom = a_mapname.getView().getZoom();

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

      var MarkerName    = document.post.tagged_marker_icon.value;

      if (document.post.category_parent.value != "-1"){
        CatFilterField = " tagged_filter=\"" + document.post.category_parent.value + "\"";
      }

      if (document.post.osm_geotag_map_type.value != "Mapnik"){
        MapTypeField = " type=\"" + document.post.osm_geotag_map_type.value + "\"";
      }

      PostTypeField = " tagged_type=\""+document.post.osm_geotag_posttype.value+"\"";
      if (document.post.tagged_marker_icon.value != "none"){
        MarkerField = " marker_name=\"" + MarkerName + "\"";
      }

      if (MarkerName == "mic_black_pinother_02.png"){
        MapBorderField = " map_border=\"thin solid grey\"";
        StyleColorField = " tagged_color=\"black\"";
      }
      else if (MarkerName == "mic_red_pinother_02.png"){
        MapBorderField = " map_border=\"thin solid red\"";
        StyleColorField = " tagged_color=\"red\"";
      }
      else if (MarkerName == "mic_green_pinother_02.png"){
        MapBorderField = " map_border=\"thin solid green\"";
        StyleColorField = " tagged_color=\"green\"";
      }
      else if (MarkerName == "mic_blue_pinother_02.png"){
        MapBorderField = " map_border=\"thin solid blue\"";
        StyleColorField = " tagged_color=\"blue\"";
      }


	  if (document.post.tagged_marker_style.value != "standard"){
        MarkerStyleField = " tagged_param=\""  + document.post.tagged_marker_style.value+ "\"";
      }

      GenTxt = "[osm_map_v3 map_center=\"" + lat + "," + lon + "\" zoom=\"" + zoom + "\" width=\"100%\" height=\"450\" " + PostTypeField + MarkerField + MapTypeField + CatFilterField + MapBorderField + MarkerStyleField + StyleColorField + "]";

      div = document.getElementById("ShortCode_Div");
      div.innerHTML = GenTxt;
	});
  },

  AddMarker: function osm_AddMarker(a_mapname, a_post_id) {
	  a_mapname.on('click', function(evt) {
	  var lonlat = ol.proj.transform(evt.coordinate, "EPSG:3857", "EPSG:4326");
	    var lon = lonlat[0].toFixed(4);
      var lat = lonlat[1].toFixed(4);

      MarkerNameField = "";
      MarkerTextField = "";

      MarkerId = document.post.osm_add_marker_id.value;
      /** MarkerIcon = document.post.osm_add_marker_icon.value;**/
      MarkerName = document.post.osm_add_marker_name.value;
      MarkerIcon = document.post.marker_icon.value;
      MarkerTextField = document.post.osm_add_marker_text.value;
      MarkerTextField = MarkerTextField.replace(/(\r\n|\n|\r)/gm, "");
      MarkerTextField = MarkerTextField.replace(/(\')/gm, "&apos;");

      osm_ajax_object.MarkerLat = lat;
      osm_ajax_object.MarkerLon = lon;
      osm_ajax_object.MarkerId = MarkerId;
      osm_ajax_object.MarkerName = MarkerName;
      osm_ajax_object.MarkerText = MarkerTextField;
      osm_ajax_object.MarkerIcon = MarkerIcon;
      osm_ajax_object.post_id = a_post_id;

      GenTxt = "<br> Marker_Id: "+ MarkerId + "<br>Marker_Name: " + MarkerName + "<br>Marker_LatLon: "+lat+","+lon+ " <br>Icon: " + MarkerIcon + "<br>  Marker_Text:<br>"+ MarkerTextField + "<br><b>4. Press [Save] to store marker!</b>";

      div = document.getElementById("Marker_Div");
      div.innerHTML = GenTxt;
      div02 = document.getElementById("ShortCode_Div");
      div02.innerHTML = "";

      /** if there is already a marker, delete the layer with it **/
      var layers = a_mapname.getLayers().getArray();
      if (layers.length > 1){
       a_mapname.removeLayer(layers[1])
      }
      MarkerIconUrl = osm_ajax_object.plugin_url + "/icons/"+MarkerIcon;
      osm_addMarkerLayer(a_mapname,   Number(lon), Number(lat), MarkerIconUrl, -16, -41, "");
	  });
  },

  SetGeotag: function osm_setGeotagEvent(a_mapname, a_post_id) {
    a_mapname.on('click', function(evt) {
	var lonlat = ol.proj.transform(evt.coordinate, "EPSG:3857", "EPSG:4326");
	var lon = lonlat[0].toFixed(4);
    var lat = lonlat[1].toFixed(4);
	var zoom = a_mapname.getView().getZoom();

    MarkerField = "";

    var MarkerName = document.post.geotag_marker_icon.value;

    if (document.post.geotag_marker_icon.value != "none"){
      MarkerField = " marker=\""+lat+","+lon+"\" marker_name=\"" + MarkerName + "\"";
    }

    osm_ajax_object.lat = lat;
    osm_ajax_object.lon = lon;
    osm_ajax_object.post_id = a_post_id;
    if (MarkerName != "none"){
      osm_ajax_object.icon = MarkerName;
      GenTxt = "Location: "+lat+","+lon+" <br>Icon: " + MarkerName + "<br><b>3. Press [Save] to store!</b>";
    }
    else {
      GenTxt = "Location: "+lat+","+lon + "<br><b>3. Press [Save] to store!</b>";
    }
    div = document.getElementById("Geotag_Div");
    div.innerHTML = GenTxt;
    div02 = document.getElementById("ShortCode_Div");
    div02.innerHTML = "";

    /** if there is already a marker, delete the layer with it **/
    var layers = a_mapname.getLayers().getArray();
    if (layers.length > 1){
     a_mapname.removeLayer(layers[1])
    }

    MarkerIconUrl = osm_ajax_object.plugin_url + "/icons/"+MarkerName;
    osm_addMarkerLayer(a_mapname,   Number(lon), Number(lat), MarkerIconUrl, -16, -41, "");
	});
  }
}
