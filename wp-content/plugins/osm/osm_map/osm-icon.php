<?php
/*  (c) Copyright 2015  MiKa (wp-osm-plugin.HanBlog.Net)

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

class Osm_icon
{
 public static function getIconsize($a_IconName)
 {
   if (!strncmp($a_IconName, 'mic_', 4)){
     return array("height"=>41,"width"=>"32","offset_height"=>"-41","offset_width"=>"-16");
   }
   else if ((!strncmp($a_IconName, 'wpttemp-', 8))||($a_IconName == 'marker_blue.png')){
     return array("height"=>24,"width"=>"24","offset_height"=>"-24","offset_width"=>"0");
   }
   else if (($a_IconName == 'hostel.png') || ($a_IconName == 'restaurant.png')){
     return array("height"=>24,"width"=>"24","offset_height"=>"-12","offset_width"=>"-12");
   }
   else if (($a_IconName == 'guest_house.png') || ($a_IconName == 'home.png') || ($a_IconName == 'hotel.png') || ($a_IconName == 'friends.png') || ($a_IconName == 'camping.png') || ($a_IconName == 'toilets.png')){
     return array("height"=>32,"width"=>"32","offset_height"=>"-16","offset_width"=>"-16");
   }
   else if ($a_IconName == 'airport.png'){
     return array("height"=>32,"width"=>"31","offset_height"=>"-16","offset_width"=>"-16");
   }
   else if ($a_IconName == 'bus.png'){
     return array("height"=>32,"width"=>"26","offset_height"=>"-16","offset_width"=>"-13");
   }
   else if ($a_IconName == 'styria_linux.png'){
     return array("height"=>50,"width"=>"36","offset_height"=>"-25","offset_width"=>"-18");
   }
   else if ($a_IconName == 'geocache.png'){
     return array("height"=>25,"width"=>"25","offset_height"=>"-12","offset_width"=>"-12");
   }
   else if ($a_IconName == 'services.png'){
     return array("height"=>28,"width"=>"32","offset_height"=>"-14","offset_width"=>"-16");
   }
   else if ($a_IconName == 'marker_posts.png'){
     return array("height"=>2,"width"=>"2","offset_height"=>"-1","offset_width"=>"-1");
   }
   else {
     $this->traceText(DEBUG_INFO, "e_unknown_icon");
     $this->traceText(DEBUG_INFO, "Error: (marker_name: ".$a_IconName.")!"); 
     return array("height"=>41,"width"=>"32","offset_height"=>"-41","offset_width"=>"-16");
   }
 }

 // just not to break shortcodes before v3.1
 public static function replaceOldIcon($a_IconName)
 {
   if ($a_IconName == "car.png"){
     return "mic_black_car_01.png";
   }
   else if (($a_IconName == "mic_cycling_icon.png") || ($a_IconName == "bicycling.png")) {
     return "mic_blue_cycling_01.png";
   }
   else if ($a_IconName == "motorbike.png"){
     return "mic_orange_motorbike_01.png";
   }
   else if ($a_IconName == "mic_photo_icon.png"){
     return "mic_black_camera_01.png";
   }
   else if (($a_IconName == "mic_black_pinother_01.png") || 
           ($a_IconName == "mic_black_pin-export_01.png")){
     return "mic_black_pinother_02.png";
   }
   else if (!strncmp($a_IconName, 'mic_white_number_', 17)){
     return "mic_black_empty_01.png";
   }
   else if ($a_IconName == "mic_toilets_disability_01.png"){
     return "mic_blue_toilets_disability_01.png";
   }
   else if ($a_IconName == "mic_black_memorial_01.png"){
     return "mic_black_empty_01.png";
   }
   else if ($a_IconName == "mic_coldfoodcheckpoint_01.png"){
     return "mic_black_empty_01.png";
   }
   else if ($a_IconName == "mic_yel_icecream_01.png"){
     return "mic_green_icecream_01.png";
   }
   else{
     return $a_IconName;
   }
 }


 public static function isOsmIcon($a_IconName)
 {
   if ($a_IconName == "airport.png" ||
    $a_IconName == "bus.png" || $a_IconName == "camping.png" ||
    $a_IconName == "friends.png" ||
    $a_IconName == "geocache.png" || $a_IconName == "guest_house.png" ||
    $a_IconName == "home.png" || $a_IconName == "hostel.png" ||
    $a_IconName == "hotel.png"|| $a_IconName == "marker_blue.png" ||
    $a_IconName == "restaurant.png" ||
    $a_IconName == "services.png" || $a_IconName == "styria_linux.png" ||
    $a_IconName == "marker_posts.png" || $a_IconName == "restaurant.png" ||
    $a_IconName == "toilets.png" || $a_IconName == "wpttemp-yellow.png" ||
    $a_IconName == "wpttemp-blue.png" || $a_IconName == "mic_blue_cycling_01.png" ||
    $a_IconName == "wpttemp-green.png" || $a_IconName == "wpttemp-red.png" ||
    $a_IconName == "mic_yel_restaurant_chinese_01.png" ||
    $a_IconName == "mic_yel_campingtents_01.png" ||
    $a_IconName == "mic_toilets_disability_01.png" || $a_IconName == "mic_shark_icon.png" ||
    $a_IconName == "mic_red_pizzaria_01.png" || $a_IconName == "mic_parasailing_01.png" ||
    $a_IconName == "mic_green_horseriding_01.png" ||
    $a_IconName == "mic_blue_tweet_01.png" ||
    $a_IconName == "mic_blue_information_01.png" || $a_IconName == "mic_blue_horseriding_01.png" ||
    $a_IconName == "mic_black_train_01.png" || $a_IconName == "mic_black_steamtrain_01.png" ||
    $a_IconName == "mic_black_powerplant_01.png" || $a_IconName == "mic_black_parking_bicycle-2_01.png" ||
    $a_IconName == "mic_black_cctv_01.png" || $a_IconName == "mic_blue_bridge_old_01.png" ||
    $a_IconName == "mic_blue_toilets_01.png" || $a_IconName == "mic_blue_scubadiving_01.png" ||
    $a_IconName == "mic_orange_motorbike_01.png" || $a_IconName == "mic_orange_sailing_1.png" ||
    $a_IconName == "mic_orange_fishing_01.png" || $a_IconName == "mic_blue_mobilephonetower_01.png" ||
    $a_IconName == "mic_orange_hiking_01.png" || $a_IconName == "mic_brown_convertible_01.png" ||
    $a_IconName == "mic_red_pirates_01.png" || $a_IconName == "mic_brown_fillingstation_01.png" ||
    $a_IconName == "mic_brown_parking_01.png" || $a_IconName == "mic_brown_van_01.png" ||
    $a_IconName == "mic_brown_harbor_01.png" || $a_IconName == "mic_green_resort_01.png" ||
    $a_IconName == "mic_brown_fourbyfour_01.png" || $a_IconName == "mic_blue_marina-2_01.png" ||
    $a_IconName == "mic_green_palm-tree-export_01.png" || $a_IconName == "mic_blue_shower_01.png" ||
    $a_IconName == "mic_blue_lighthouse-2_01.png" || $a_IconName == "mic_black_memorial_01.png" ||
    $a_IconName == "mic_black_pinother_02.png" || $a_IconName == "mic_blue_pinother_02.png" ||
    $a_IconName == "mic_green_campingcar_01.png" || $a_IconName == "mic_green_icecream_01.png" ||
    $a_IconName == "mic_brown_pickup_camper_01.png" || $a_IconName == "mic_brown_van_01.png" ||
    $a_IconName == "mic_green_pinother_02.png" || $a_IconName == "mic_red_pinother_02.png" ||
    $a_IconName == "mic_blue_pickup_camper_01.png" || $a_IconName == "mic_green_vineyard-2_01.png" ||
    $a_IconName == "mic_green_arbol_01.png" || $a_IconName == "mic_black_finish_01.png" ||
    $a_IconName == "mic_black_finish2_01.png" || $a_IconName == "mic_black_start-race-2_01.png" ||
    $a_IconName == "mic_green_garden_01.png" || $a_IconName == "mic_blue_drinkingwater_01.png" ||
    $a_IconName == "mic_orange_archery_01.png" || $a_IconName == "mic_black_archery_01.png" ||
    $a_IconName == "mic_black_car_01.png" || $a_IconName == "mic_green_car_01.png" ||
    $a_IconName == "mic_brown_car_01.png" || $a_IconName == "mic_black_camera_01.png" ||
    $a_IconName == "mic_orange_archery_01.png" || $a_IconName == "mic_black_archery_01.png" ||
    $a_IconName == "mic_blue_empty_01.png" || $a_IconName == "mic_black_empty_01.png" ||
    $a_IconName == "mic_black_heart_01.png" || $a_IconName == "mic_green_vw_t3_01.png"
){
    return 1;
   }
   else {
    return 0;
   }
 } 


}
?>
