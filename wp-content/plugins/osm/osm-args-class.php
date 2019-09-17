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

class cOsm_arguments
{
    private  $width_str = '100%'; 
    private  $height_str = '300';
    private  $map_Lat = '58.213';
    private  $map_Lon = '6.378';
    private  $zoom = '4';
    private  $map_api_key = 'NoKey';
    private  $file_list = 'NoFile';
    private  $file_color_list = 'NoColor';
    private  $map_type = 'Osm';
    private  $jsname = 'dummy';
    private  $marker_latlon = 'No';
    private  $map_border = '2px solid grey';
    private  $marker_name = 'NoName';
    private  $mapControl_array = '';
    private  $wms_type = 'wms_type';
    private  $wms_address = 'wms_address';
    private  $wms_param = 'wms_param';
    private  $wms_attr_name = 'wms_attr_name';
    private  $wms_attr_url = 'wms_attr_url';
    private  $tagged_type = 'no';
    private  $tagged_filter_type = 'category';
    private  $tagged_filter = 'osm_all';
    private  $mwz = 'false';
    private  $marker_height = '32';
    private  $marker_width = '32';
    private  $marker_focus = '0';
    private $post_markers = 'no';
    private $cntrl_fullscreen = 0;
    private $cntrl_mouseposition = 0;
    private $cntrl_scaleline = 0;      
    private $show_kml_marker_name  = "false";
    private $tagged_cluster = "false";
    private $tagged_border_color = "[0, 0, 255, 0.5]";
    private $tagged_inner_color = "[0, 0, 255, 0.5]";
    private $map_event = 'no';
    
   private function setMarkersize($a_marker_size){
      if ($a_marker_size == "no"){
      }
      else{
        $marker_size_array = explode(',', $a_marker_size);
        if(count($marker_size_array) == 3) {
          $this->marker_height = $marker_size_array[0];
          $this->marker_width = $marker_size_array[1];
          $this->marker_focus = $marker_size_array[2];
        }
        else{
          Osm::traceText(DEBUG_ERROR, "marker_size error!");
		  Osm::traceText(DEBUG_ERROR, $a_marker_size);
        }
      }
    }

   private function setLatLon($a_map_center){

     $map_center = preg_replace('/\s*,\s*/', ',',$a_map_center);
      // get pairs of coordination
      $map_center_Array = explode( ' ', $map_center );
      list($this->map_Lat, $this->map_Lon) = explode(',', $map_center_Array[0]);     
}

private function setMapSize($a_width,  $a_height){
     $pos = strpos($a_width, "%");
    if ($pos == false) {
      if ($a_width < 1){
        Osm::traceText(DEBUG_ERROR, (sprintf(__(' width =  %s is out of range [pix]!'), $a_width)));
        $a_width = 450;
      }
      $this->width_str = $a_width."px"; // make it 30px
    } else {// it's 30%
      $width_perc = substr($a_width, 0, $pos ); // make it 30 
      if (($width_perc < 1) || ($width_perc >100)){
        Osm::traceText(DEBUG_ERROR, (sprintf(__('width =  %s is out of range [perc]!'), $a_width)));
        $a_width = "100%";
      }
      $this->width_str = substr($a_width, 0, $pos+1 ); // make it 30% 
    }

    $pos = strpos($a_height, "%");
    if ($pos == false) {
      if ($a_height < 1){
        Osm::traceText(DEBUG_ERROR, (sprintf(__('height =  %s is out of range [pix]!'), $a_height)));
        $a_height = 300;
      }
      $this->height_str = $a_height."px"; // make it 30px
    } else {// it's 30%
      $height_perc = substr($a_height, 0, $pos ); // make it 30 
      if (($height_perc < 1) || ($height_perc >100)){
        Osm::traceText(DEBUG_ERROR, (sprintf(__('height =  %s is out of range [perc]!'), $a_height)));
        $a_height = "100%";
      }
      $this->height_str = substr($a_height, 0, $pos+1 ); // make it 30% 
    }
    
}

  private function setControlArray($a_MapControl){
    $mapControl_array = explode( ',',$a_MapControl);
    foreach ($mapControl_array as $MapControl ){
	  $MapControl = strtolower($MapControl);
	  if ($MapControl == 'fullscreen'){
          $this->cntrl_fullscreen = true;
     }
     else if($MapControl == 'mouseposition'){
       $this->cntrl_mouseposition = true;
     }
    else if($MapControl == 'scaleline'){
      $this->cntrl_scaleline = true;
    }
}
  return $this->mapControl_array;
}

private function setPostMarkers($a_post_markers){
    if (($a_post_markers == "1") || ($a_post_markers == "2")  || ($a_post_markers == "3") || ($a_post_markers == "4") || ($a_post_markers == "5") || ($a_post_markers == "6") || ($a_post_markers == "7") || ($a_post_markers == "8") || ($a_post_markers == "9") || ($a_post_markers == "all")){
      $this->post_markers = $a_post_markers;
   }
    else {
      $this->post_markers = 'no';  
   }
}

private function setMapType($a_type){
    $map_type = strtolower($a_type);
	if ((($map_type == "outdoor") || ($map_type == "landscape") || ($map_type == "spinal") || ($map_type == "pioneer") || ($map_type == "cyclemap")) && ($this->getMapAPIkey() == "NoKey")){
	  $this->map_type = "osm";
	}
	else {
	  $this->map_type = $map_type;
	}
}

private function setDisplayMarker($a_display_marker_name){
    if ($a_display_marker_name == "kml"){
      $this->show_kml_marker_name = "true";
   }
}
private function setTaggedParam($a_tagged_param){
    if ($a_tagged_param == "cluster"){
      $this->tagged_cluster = "true";
    }
}

private function setTaggedColor($a_tagged_color){
  if ($a_tagged_color == "blue"){
    $this->tagged_border_color = "[0, 0, 255, 0.5]";
    $this->tagged_inner_color = "[0, 0, 255, 0.85]";
   }
  elseif($a_tagged_color == "red"){
    $this->tagged_border_color = "[255,0,0, 0.5]";
    $this->tagged_inner_color = "[255,0,0, 0.85]";
}
  elseif($a_tagged_color == "yellow"){
    $this->tagged_border_color = "[255,255,0, 0.5]";
    $this->tagged_inner_color = "[255,255,0, 0.85]";
}
  elseif($a_tagged_color == "green"){
    $this->tagged_border_color = "[0,255,0, 0.5]";
    $this->tagged_inner_color = "[0,255,0, 0.85]";
}
  elseif($a_tagged_color == "black"){
    $this->tagged_border_color = "[0,0,0, 0.5]";
    $this->tagged_inner_color = "[0,0,0, 0.85]";
}
elseif($a_tagged_color == "purple"){
    $this->tagged_border_color = "[128,0,128, 0.5]";
    $this->tagged_inner_color = "[128,0,128, 0.85]";
}
  elseif(($a_tagged_color == "grey") || ($a_tagged_color == "gray")) {
    $this->tagged_border_color = "[128,128,128, 0.5]";
    $this->tagged_inner_color = "[128,128,128, 0.85]";
}
  elseif($a_tagged_color == "orange") {
    $this->tagged_border_color = "[255,128,64, 0.5]";
    $this->tagged_inner_color = "[255,128,64, 0.85]";
}
else {
  $this->tagged_border_color = "[0, 0, 255, 0.5]";
  $this->tagged_inner_color = "[0, 0, 255, 0.85]";
  }
}

private function setMapAPIkey($a_map_api_key){
  $this->map_api_key = $a_map_api_key;
}

public function setMap_event($a_map_event){
  $this->map_event = $a_map_event;  
}

public function setTaxonomy($a_tagged_filter_type){
  $this->tagged_filter_type = $a_tagged_filter_type;  
}

  function __construct($a_width, $a_height, $a_map_center, $zoom, $a_map_api_key, $file_list, $file_color_list, $a_type, $jsname, $marker_latlon, $map_border, $a_map_event, 
    $marker_name, $a_marker_size, $control, $wms_address, $wms_param, $wms_attr_name,  $wms_type, $wms_attr_url, 
    $tagged_type, $a_tagged_filter_type, $tagged_filter, $mwz, $a_post_markers, $a_display_marker_name, $a_tagged_param, $a_tagged_color){
        
    $this->setLatLon($a_map_center) ;
    $this->setMapSize($a_width,  $a_height);
    $this->setControlArray($control);
    $this->setMarkersize($a_marker_size);
    $this->setPostMarkers($a_post_markers);
    $this->setDisplayMarker($a_display_marker_name);
    $this->setTaggedParam($a_tagged_param);
    $this->setTaggedColor($a_tagged_color);
	$this->setMapAPIkey($a_map_api_key);
	$this->setMapType($a_type); // needs to be done after setMapAPIkey
	$this->setMap_event($a_map_event);
        $this->setTaxonomy($a_tagged_filter_type);
}

public function getPostMarkers(){
    return $this->post_markers;
}

public function getMapCenterLat(){
  return $this->map_Lat;
}
public function getMapCenterLon(){
  return $this->map_Lon;
}

public function getMapAPIkey(){
  return $this->map_api_key;
}

public function getMapWidth_str(){
  return $this->width_str;
}
public function getMapHeight_str(){
  return $this->height_str;
}
public function getMapControl(){
  return $this->mapControl_array; 
}
public function getMapType(){
  return $this->map_type;  
}
public function getMarkerHeight(){
  return $this->marker_height;  
}
public function getMarkerWidth(){
  return $this->marker_width;  
}
public function getMarkerFocus(){
  return $this->marker_focus;  
}

public function issetFullScreen(){
    return $this->cntrl_fullscreen;
}
public function issetMouseposition(){
    return $this->cntrl_mouseposition;
}
public function issetScaleline(){
    return $this->cntrl_scaleline;
}

public function showKmlMarkerName(){
    return $this->show_kml_marker_name;  
}
public function isclustered(){
    return $this->$tagged_cluster;  
}
public function getTaggedBorderColor(){
    return $this->tagged_border_color;  
}

public function getTaggedInnerColor(){
    return $this->tagged_inner_color;  
}

public function getMap_event(){
    return $this->map_event;  
}

public function getTaxonomy(){
  return $this->tagged_filter_type;  
}

}
?>
