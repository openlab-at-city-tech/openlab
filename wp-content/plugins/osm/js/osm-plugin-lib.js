/*
  OSM OpenLayers for OSM wordpress plugin
  plugin: http://wp-osm-plugin.HanBlog.net
  blog:   http://www.HanBlog.net
*/

// Display Disc / Circles
function osm_getFeatureDiscCenter(a_tileLayer, a_discLayer, a_lon, a_lat, a_radius, a_centeropac, a_centercol, a_strw, a_strcol, a_stropac, a_fillcol, a_fillopac) 
{
    var lonLat = new OpenLayers.LonLat(a_lon, a_lat).transform(a_tileLayer.displayProjection, a_tileLayer.projection);

    var discStyle    = { strokeColor: a_strcol,
                         strokeOpacity: a_stropac,
                         strokeWidth: a_strw,
                         fillColor: a_fillcol,
                         fillOpacity: a_fillopac
                       };
    var centerStyle  = { strokeColor: a_centercol,
                         strokeOpacity: a_centeropac,
                         strokeWidth: a_strw,
                         fillColor: a_centercol,
                         fillOpacity: a_centeropac
                       };

    var radius = a_radius / Math.cos(a_lat*(Math.PI/180));

    var disc = OpenLayers.Geometry.Polygon.createRegularPolygon(
                                             new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat),
                                             radius,
                                             200); // nombre de faces
                 
    var center = OpenLayers.Geometry.Polygon.createRegularPolygon(
                                             new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat),
                                             1,   // taille dans lunite de la carte
                                             5);  // nombre de faces
                 

    var featureDisc   = new OpenLayers.Feature.Vector(disc,null,discStyle);
    var featureCenter = new OpenLayers.Feature.Vector(center,null,centerStyle);
    a_discLayer.addFeatures([featureDisc,featureCenter]);
}

// Draw line
function osm_setLinePoints(a_tileLayer, a_lineLayer, a_strw, a_strcol, a_stropac, a_Points)
{
  var Points = new Array();

  for (var i = 0; i < a_Points.length; i++) {
   // var lonLat = new OpenLayers.LonLat(a_Points[i]["lon"], a_Points[i]["lat"]).transform(new OpenLayers.Projection("EPSG:4326"), a_tileLayer.getProjectionObject());
   var lonLat = new OpenLayers.LonLat(a_Points[i]["lon"], a_Points[i]["lat"]).transform(a_tileLayer.displayProjection, a_tileLayer.projection);
    Points[i] = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);
  }
  var line = new OpenLayers.Geometry.LineString(Points);
  var style = { 
    strokeColor: a_strcol, 
    strokeOpacity: a_stropac, 
    strokeWidth: a_strw 
   };

  var lineFeature = new OpenLayers.Feature.Vector(line, null, style);
  a_lineLayer.addFeatures([lineFeature]);
}


// Clickhandler / Shorcode generator

function osm_getIconSize(a_IconName){
  Icon = new Object();
  if (a_IconName.match('mic_')) {
    Icon["height"] = "37";
    Icon["width"] = "32";
    Icon["offset_height"] = "-37";
    Icon["offset_width"] = "-16";
  }
  else if (a_IconName.match('wpttemp')){
    Icon["height"] = "24";
    Icon["width"] = "24";
    Icon["offset_height"] = "-24";
    Icon["offset_width"] = "-0";
  }
  else if ((a_IconName == "camping.png") || (a_IconName == "friends.png") || (a_IconName == "guest_house.png") || (a_IconName == "home.png") || (a_IconName == "toilets.png")  || (a_IconName == "hotel.png")){
    Icon["height"] = "32";
    Icon["width"] = "32";
    Icon["offset_height"] = "-16";
    Icon["offset_width"] = "-16";
  }
  else if ((a_IconName == "hostel.png") || (a_IconName == "restaurant.png")){
    Icon["height"] = "24";
    Icon["width"] = "24";
    Icon["offset_height"] = "-12";
    Icon["offset_width"] = "-12";
  }
  else if (a_IconName == "marker_blue.png"){
    Icon["height"] = "24";
    Icon["width"] = "24";
    Icon["offset_height"] = "-24";
    Icon["offset_width"] = "0";
  }
  else if (a_IconName == "airport.png"){
    Icon["height"] = "32";
    Icon["width"] = "31";
    Icon["offset_height"] = "-16";
    Icon["offset_width"] = "-16";
  }
  else if (a_IconName == "bus.png"){
    Icon["height"] = "32";
    Icon["width"] = "26";
    Icon["offset_height"] = "-16";
    Icon["offset_width"] = "-13";
  }
  else if (a_IconName == "geocache.png"){
    Icon["height"] = "25";
    Icon["width"] = "25";
    Icon["offset_height"] = "-12";
    Icon["offset_width"] = "-12";
  }
  else if (a_IconName == "motorbike.png"){
    Icon["height"] = "23";
    Icon["width"] = "32";
    Icon["offset_height"] = "-12";
    Icon["offset_width"] = "-16";
  }
  else if (a_IconName == "services.png"){
    Icon["height"] = "28";
    Icon["width"] = "32";
    Icon["offset_height"] = "-14";
    Icon["offset_width"] = "-16";
  }
  else if (a_IconName == "styria_linux.png"){
    Icon["height"] = "50";
    Icon["width"] = "36";
    Icon["offset_height"] = "-25";
    Icon["offset_width"] = "-18";
  }
  else if (a_IconName == "marker_posts.png"){
    Icon["height"] = "2";
    Icon["width"] = "2";
    Icon["offset_height"] = "-1";
    Icon["offset_width"] = "-1";
  }
  else {
    Icon["height"] = "32";
    Icon["width"] = "32";
    Icon["offset_height"] = "-16";
    Icon["offset_width"] = "-16";
  }
  return Icon;
}

// Clickhandler / Shorcode generator

function osm_getRadioValue(a_Form){
  if (a_Form == "Markerform"){
    for (var i=0; i < document.Markerform.Art.length; i++){
      if (document.Markerform.Art[i].checked){
        var rad_val = document.Markerform.Art[i].value;
        return rad_val;
      }
    }
    return "undefined";
  }
  else if (a_Form == "GPXcolourform"){
    for (var i=0; i < document.GPXcolourform.Gpx_colour.length; i++){
      if (document.GPXcolourform.Gpx_colour[i].checked){
        var rad_val = document.GPXcolourform.Gpx_colour[i].value;
        return rad_val;
      }
    }
    return "undefined";
  }
  else if (a_Form == "Bordercolourform"){
    for (var i=0; i < document.Bordercolourform.Border_colour.length; i++){
      if (document.Bordercolourform.Border_colour[i].checked){
        var rad_val = document.Bordercolourform.Border_colour[i].value;
        return rad_val;
      }
    }
    return "undefined";
  }
  else if (a_Form == "Naviform"){
    for (var i=0; i < document.Naviform.Navi_Link.length; i++){
      if (document.Naviform.Navi_Link[i].checked){
        var rad_val = document.Naviform.Navi_Link[i].value;
        return rad_val;
      }
    }
    return "undefined";
  }
  else if (a_Form == "ControlStyleform"){
    for (var i=0; i < document.ControlStyleform.Cntrl_style.length; i++){
      if (document.ControlStyleform.Cntrl_style[i].checked){
        var rad_val = document.ControlStyleform.Cntrl_style[i].value;
        return rad_val;
      }
    }
    return "undefined";
  }
  return "not implemented";
}

function osm_saveGeotag(){
  if ((osm_ajax_object.lat == '') || (osm_ajax_object.lon == '')){
    alert('Place geotag in the map before save');
  }
  else
  {
    var data = {
      action: 'act_saveGeotag',
      lat: osm_ajax_object.lat,
      lon: osm_ajax_object.lon,
      icon: osm_ajax_object.icon,
      post_id: osm_ajax_object.post_id,
      geotag_nonce: osm_ajax_object.geotag_nonce
    };
    jQuery.post(osm_ajax_object.ajax_url, data, function(response) {
      div = document.getElementById("Geotag_Div");
      div.innerHTML = response;
    });
  }
}

function osm_savePostMarker(){
  if ((osm_ajax_object.MarkerLat == '') || (osm_ajax_object.MarkerLon == '')){
    alert('Place geotag in the map before save');
  }
  else
  {
    var data = {
      action: 'act_saveMarker',
      MarkerId: osm_ajax_object.MarkerId,
      MarkerLat: osm_ajax_object.MarkerLat,
      MarkerLon: osm_ajax_object.MarkerLon,
      MarkerIcon: osm_ajax_object.MarkerIcon,
      MarkerText: osm_ajax_object.MarkerText,
      MarkerName: osm_ajax_object.MarkerName,
      map_zoom: osm_ajax_object.map_zoom,
      map_type: osm_ajax_object.map_type,
      map_border: osm_ajax_object.map_border,
      map_controls: osm_ajax_object.map_controls,
      post_id: osm_ajax_object.post_id,
      marker_nonce: osm_ajax_object.marker_nonce
    };
    jQuery.post(osm_ajax_object.ajax_url, data, function(response) {
      div = document.getElementById("Marker_Div");
      div.innerHTML = response;
    });
  }
}

function getTileURL(bounds) {
  var res = this.map.getResolution();
  var x = Math.round((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
  var y = Math.round((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
  var z = this.map.getZoom();
  var limit = Math.pow(2, z);
  if (y < 0 || y >= limit) {
    return null;
  } 
  else {
    x = ((x % limit) + limit) % limit;
    url = this.url;
    path= z + "/" + x + "/" + y + "." + this.type;
    if (url instanceof Array) {
      url = this.selectUrl(path, url);
    }
    return url+path;
  }
}

// http://trac.openlayers.org/changeset/9023
function osm_getTileURL(bounds) {
    var res = this.map.getResolution();
    var x = Math.round((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
    var y = Math.round((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
    var z = this.map.getZoom();
    var limit = Math.pow(2, z);

    if (y < 0 || y >= limit) {
        return OpenLayers.Util.getImagesLocation() + "404.png";
    } else {
        x = ((x % limit) + limit) % limit;
        return this.url + z + "/" + x + "/" + y + "." + this.type;
    }
}


function AddMarker(a_tileLayer, a_marker_distance)
{
    var ll;
    var feature;
    var marker;

    var popup_maxwidth = 999;
    var popup_maxheight = 250;

    // Combine marker with a distance lower then 12 pixels
    var PopUpArray = CombineNearMarker(a_marker_distance, a_tileLayer);

    // Create marker
    for (var i = 0; i < PopUpArray.length; i++)
    {
        // Convert coordinates
        ll = new OpenLayers.LonLat(PopUpArray[i].Lon, PopUpArray[i].Lat).transform(a_tileLayer.displayProjection, a_tileLayer.projection);

        // Create pop-up
        feature = new OpenLayers.Feature(markers, ll, data);
        feature.closeBox = true;
        feature.popupClass = OpenLayers.Class(OpenLayers.Popup.FramedCloud,
        {
            "autoSize": true,
            /*
             * For "panMapIfOutOfView" and "keepInMap" see:
             * http://permalink.gmane.org/gmane.comp.gis.openlayers.user/25702
             */ 
            "panMapIfOutOfView": false,
            "keepInMap": false,
            "contentDisplayClass": "olPopupContent",
            maxSize: new OpenLayers.Size(popup_maxwidth, popup_maxheight)
        });
        //feature.data.overflow = "hidden";

        // Create pop-up text
        var TextArray = PopUpArray[i].Text;
        var Text = 0;
        for (var j = 0; j < TextArray.length; j++)
        {
        	if(j == 0)
        	    Text = TextArray[j];
        	else
        		Text += "<br>" + TextArray[j];
        }

        feature.data.popupContentHTML = Text;

        a_tileLayer.addPopup(feature.createPopup(feature.closeBox));
        feature.popup.toggle();

        // Create marker
        if (PopUpArray[i].Count > 1) // If we have a combined marker than take an onther icon
        {
            marker = new OpenLayers.Marker(ll, data2.icon.clone());
        }
        else
        {
            marker = new OpenLayers.Marker(ll, data.icon.clone());
        }

        marker.feature = feature;
        marker.events.register("mousedown", feature, markerClick);
        markers.addMarker(marker);
    }
}

function CombineNearMarker(PixelDiff, a_tileLayer)
{
    var px1 = a_tileLayer.getLonLatFromPixel(new OpenLayers.Pixel(0, 0));
    var px2 = a_tileLayer.getLonLatFromPixel(new OpenLayers.Pixel(PixelDiff, 0));
    var PixelLonLatDiff = Math.abs(px1.lon - px2.lon);

    var PopUpArray = new Array();
    var isNowPopUp = 0;

    // Read all markers
    for (var i = 0; i < MarkerArray.length; i++)
    {
        isNowPopUp = 0;

        // Get the position of marker1 coordinates in pixel
        var ll = new OpenLayers.LonLat(MarkerArray[i].Lon, MarkerArray[i].Lat).transform(a_tileLayer.displayProjection, a_tileLayer.projection);
        var pixel = a_tileLayer.getPixelFromLonLat(ll);
        var LonTemp1 = ll.lon;
        var LatTemp1 = ll.lat;

        // Use only the visible markers
        if (pixel.x >= 0 && pixel.x <= a_tileLayer.size.w && pixel.y >= 0 && pixel.y <= a_tileLayer.size.h)
        {
            // Check if marker near to this marker exist
            for (var j = 0; j < PopUpArray.length; j++)
            {
                // Get the position of marker2 coordinates in pixel
                var ll = new OpenLayers.LonLat(PopUpArray[j].Lon, PopUpArray[j].Lat).transform(a_tileLayer.displayProjection, a_tileLayer.projection);
                var LonTemp2 = ll.lon;
                var LatTemp2 = ll.lat;

                // Calculate difference of these two marker in pixel
                var LonDiff = Math.abs(LonTemp1 - LonTemp2);
                var LatDiff = Math.abs(LatTemp1 - LatTemp2);

                // Check the distance
                if (LonDiff <= PixelLonLatDiff && LatDiff <= PixelLonLatDiff)
                {
                    var Count = PopUpArray[j].Count;
                    // Add this link to the existing marker
                    PopUpArray[j].Text[Count] = MarkerArray[i].Text;
                    // Calculate the mean position of these combined markers
                    PopUpArray[j].Lat = (PopUpArray[j].Lat + MarkerArray[i].Lat) / 2;
                    PopUpArray[j].Lon = (PopUpArray[j].Lon + MarkerArray[i].Lon) / 2;
                    Count++;
                    PopUpArray[j].Count = Count;
                    isNowPopUp = 1;
                }
            }

            // Add a new marker to the list
            if (isNowPopUp == 0)
            {
                PopUpArray.push(
                {
                    Text: new Array(MarkerArray[i].Text),
                    Lon: MarkerArray[i].Lon,
                    Lat: MarkerArray[i].Lat,
                    Count: 1
                });
            }
        }
    }

    return PopUpArray;
}


