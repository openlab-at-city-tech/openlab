/*  (c) Copyright 2020  MiKa (http://wp-osm-plugin.HanBlog.Net)

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

// This namespace covers the eventhandler in the metabox to
// generate the shortcodes
var MetaboxEventhandler = {
	
    TaggedPostsSC: function osm_addTaggedPostsMapEvent(a_mapname) {
      a_mapname.on('click', function(evt) {
   
jQuery( document ).ready( function( $ ) { 
  
      var lonlat = ol.proj.transform(a_mapname.getView().getCenter(), "EPSG:3857", "EPSG:4326");
   	var lon = lonlat[0].toFixed(4);
      var lat = lonlat[1].toFixed(4);
      var zoom = a_mapname.getView().getZoom();

      var MarkerField = "";
      var ThemeField  = "";
      var MapTypeField = "";
      var Linefield = "";
      var PostTypeField ="";
      var CatFilterField = "";
      var MapBorderField = "";
      var MarkerStyleField = "";
      var StyleColorField = "";
      var TagTypeField = "";

      //var dropdown = document.getElementById("cat");
      var dropdown = $('#cat').val();

      var MarkerName    = $('#tagged_marker_icon:checked').val();

      var TagFilter = $('#tag_filter').val();
      var CategoryFilter = $('#category_parent').val();
      
      var ErrorFlag = 0;



      if ((CategoryFilter != "-1") && (TagFilter != "")){
        CatFilterField = "";
        TagFilter ="";
        ErrorFlag = 1;
      } 
      else if (CategoryFilter != "-1"){
        CatFilterField = " tagged_filter=\"" + CategoryFilter + "\"";
      }
      else if (TagFilter != ""){
        CatFilterField = " tagged_filter=\"" + TagFilter + "\"";
        TagTypeField = " tagged_filter_type=\"post_tag\"";
      }

      if ($('#osm_geotag_map_type').val() != "Mapnik"){
        MapTypeField = " type=\"" + $('#osm_geotag_map_type').val() + "\"";
      }

      PostTypeField = " tagged_type=\""+$('#osm_geotag_posttype').val()+"\"";
      if ($('#osm_geotag_posttype').val() != "none"){
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

      if ($('#tagged_marker_style:checked').val() != "standard"){
        MarkerStyleField = " tagged_param=\""  + $('#tagged_marker_style:checked').val()+ "\"";
      }

      if (ErrorFlag > 0){
        GenTxt = "[Error]: You must not set category filter and tag filter at the same time! <br> Set Category filter to None or delete tag tilter."
      }
      else {
        GenTxt = "[osm_map_v3 map_center=\"" + lat + "," + lon + "\" zoom=\"" + zoom + "\" width=\"100%\" height=\"450\" " + PostTypeField + MarkerField + MapTypeField + CatFilterField + MapBorderField + MarkerStyleField + StyleColorField + TagTypeField+"]";
      }

      $('#ShortCode_Div').html(GenTxt);
      } ); /** JQuery **/
	});
  },

  AddMarker: function osm_AddMarker(a_mapname, a_post_id) {
      a_mapname.on('click', function(evt) {

jQuery( document ).ready( function( $ ) {

      var lonlat = ol.proj.transform(evt.coordinate, "EPSG:3857", "EPSG:4326");
      var lon = lonlat[0].toFixed(4);
      var lat = lonlat[1].toFixed(4);
      var zoom = a_mapname.getView().getZoom();
      var Controls = "";
      var BckgrndImg = "";

      MarkerId = "";
      BorderField  = "";
      MapTypeField = "";  

      if ($('#osm_add_marker_map_type').val() != "Mapnik"){
        MapTypeField = $('#osm_add_marker_map_type').val();
      }
      

      if ($('#osm_add_marker_border').val() != "none"){
        BorderField = $('#osm_add_marker_border').val();
      }

      if($('#osm_add_marker_fullscreen').prop('checked') == true) {
        Controls = "fullscreen,";
      }

      if($('#osm_add_marker_scaleline').prop('checked') == true) {
        Controls = Controls + "scaleline,";
      }

      if($('#osm_add_marker_mouseposition').prop('checked') == true) {
        Controls = Controls + "mouseposition,";
      }
      
      if($('#osm_add_marker_bckgrnd_img').prop('checked') == true) {
        BckgrndImg = "GDPR_bckgrnd.png";
      }
      
      
      if (Controls != ""){
        Controls = Controls.substr(0, Controls.length-1);
        ControlField = Controls;
      }
      else {
        ControlField ="";
      }


      MarkerNameField = "";
      MarkerTextField = "";

      MarkerId = 1;

      MarkerName = $('#osm_add_marker_name').val();
      MarkerIcon = $('#marker_icon:checked').val();
      MarkerTextField = $('#osm_add_marker_text').val();

      MarkerTextField = MarkerTextField.replace(/(\r\n|\n|\r)/gm, "");
      MarkerTextField = MarkerTextField.replace(/(\')/gm, "&apos;");

      osm_ajax_object.MarkerLat = lat;
      osm_ajax_object.MarkerLon = lon;
      osm_ajax_object.MarkerId = MarkerId;
      osm_ajax_object.MarkerName = MarkerName;
      osm_ajax_object.MarkerText = MarkerTextField;
      osm_ajax_object.MarkerIcon = MarkerIcon;
      osm_ajax_object.post_id = a_post_id;

      osm_ajax_object.map_zoom = zoom;
      osm_ajax_object.map_type = MapTypeField;
      osm_ajax_object.map_border = BorderField;
      osm_ajax_object.map_controls = ControlField;
      osm_ajax_object.map_name = a_mapname;
      osm_ajax_object.bckgrnd_img = BckgrndImg;

      GenTxt = "<br> Marker_Id: "+ MarkerId + "<br>Marker_Name: " + MarkerName + "<br>Marker_LatLon: "+lat+","+lon+ " <br>Icon: " + MarkerIcon + "<br>  Marker_Text:<br>"+ MarkerTextField + "<br><b>4. Press [Save] to store marker!</b>";

      $('#Marker_Div').html(GenTxt);
      $('#ShortCode_Div').html("");
           

      /** if there is already a marker, delete the layer with it **/
      var layers = a_mapname.getLayers().getArray();
      if (layers.length > 1){
       a_mapname.removeLayer(layers[1])
      }
      MarkerIconUrl = osm_ajax_object.plugin_url + "/icons/"+MarkerIcon;
      osm_addMarkerLayer(a_mapname,   Number(lon), Number(lat), MarkerIconUrl, -16, -41, "");
} ); /** JQuery **/
      
	  });

	  
	  
  },

  SetGeotag: function osm_setGeotagEvent(a_mapname, a_post_id) {
    a_mapname.on('click', function(evt) {

jQuery( document ).ready( function( $ ) {

	var lonlat = ol.proj.transform(evt.coordinate, "EPSG:3857", "EPSG:4326");
	var lon = lonlat[0].toFixed(4);
   var lat = lonlat[1].toFixed(4); 
   
	var zoom = a_mapname.getView().getZoom();

    MarkerField = "";


    var MarkerName = $('#geotag_marker_icon:checked').val();

    if ($('#geotag_marker_icon:checked').val() != "none"){
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
 
    $('#Geotag_Div').html(GenTxt);
    $('#ShortCode_Div').html("");

    /** if there is already a marker, delete the layer with it **/
    var layers = a_mapname.getLayers().getArray();
    if (layers.length > 1){
     a_mapname.removeLayer(layers[1])
    }

    MarkerIconUrl = osm_ajax_object.plugin_url + "/icons/"+MarkerName;
    osm_addMarkerLayer(a_mapname,   Number(lon), Number(lat), MarkerIconUrl, -16, -41, "");

    } ); /** JQuery **/

	});
  }
}
