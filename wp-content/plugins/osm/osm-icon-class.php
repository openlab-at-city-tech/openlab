<?php
/*  (c) Copyright 2019  MiKa (wp-osm-plugin.HanBlog.Net)

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
/**
 *  \brief class to keep the icon in an object
 */
class cOsm_icon
{
  private $icon_name= 'no', $icon_URL = 'no';
  private $icon_height = -1,$icon_width = -1,$icon_offset_height = -1,$icon_offset_width = -1;
  private $icon_color = 'no';
  
// just not to break shortcodes before v3.1
 private function replaceOldIcon($a_IconName){
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

  private function isOsmIcon($a_IconName){
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
    $a_IconName == "mic_blue_toilets_disability_01.png" || $a_IconName == "mic_shark_icon.png" ||
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
    $a_IconName == "mic_black_heart_01.png" || $a_IconName == "mic_green_vw_t3_01.png" ||
    $a_IconName == "mic_orange_van_01.png" || $a_IconName == "mic_orange_climbing_01.png" ||
    $a_IconName == "mic_green_restaurant_02.png" || $a_IconName == "mic_green_hut_01.png" ||
    $a_IconName == "mic_green_tent_01.png" || $a_IconName == "mic_blue_ski_tour_01.png" ||
    $a_IconName == "mic_green_hut_02.png" || $a_IconName == "mic_green_caravan_01.png" ||
$a_IconName == "mic_black_acupuncture_10.png" || $a_IconName == "mic_black_airline_10.png" ||  
$a_IconName == "mic_black_airport_10.png" || $a_IconName == "mic_black_archery_10.png" || 
$a_IconName == "mic_black_area51_10.png" || $a_IconName == "mic_black_atom_10.png" || 
$a_IconName == "mic_black_badmint_10.png" || $a_IconName == "mic_black_balloon_10.png" || 
$a_IconName == "mic_black_bar_10.png" || $a_IconName == "mic_black_bank_10.png" || 
$a_IconName == "mic_black_barrierfree_10.png" || $a_IconName == "mic_black_barbe_10.png" || 
$a_IconName == "mic_black_basketb_10.png" || $a_IconName == "mic_black_baseball_10.png" || 
$a_IconName == "mic_black_beer_10.png" || $a_IconName == "mic_black_beach_10.png" || 
$a_IconName == "mic_black_boatbridge_10.png" || $a_IconName == "mic_black_bicycle_10.png" ||
$a_IconName == "mic_black_boxing_10.png" || $a_IconName == "mic_black_border_10.png" || 
$a_IconName == "mic_black_bridge_10.png" || $a_IconName == "mic_black_brental_10.png" || 
$a_IconName == "mic_black_bus_10.png" || $a_IconName == "mic_black_building_10.png" || 
$a_IconName == "mic_black_camp_10.png" || $a_IconName == "mic_black_busstop_10.png" || 
$a_IconName == "mic_black_camp_11.png" || $a_IconName == "mic_black_camp_12.png" || 
$a_IconName == "mic_black_cardmoney_10.png" || $a_IconName == "mic_green_caravan_01.png" || 
$a_IconName == "mic_black_carno_10.png" || $a_IconName == "mic_black_carnival_10.png" || 
$a_IconName == "mic_black_cars_10.png" || $a_IconName == "mic_black_ccski_10.png" || 
$a_IconName == "mic_black_cemetary_10.png" || $a_IconName == "mic_black_champ_10.png" || 
$a_IconName == "mic_black_charger_10.png" || $a_IconName == "mic_green_caravan_01.png" || 
$a_IconName == "mic_black_chess_10.png" || $a_IconName == "mic_black_check_10.png" || 
$a_IconName == "mic_black_cinema_10.png" || $a_IconName == "mic_black_chute_10.png" || 
$a_IconName == "mic_black_climbing_10.png" || $a_IconName == "mic_black_citytrain_10.png" || 
$a_IconName == "mic_black_coffeshop_10.png" || $a_IconName == "mic_black_climbing_11.png" || 
$a_IconName == "mic_green_hut_02.png" || $a_IconName == "mic_black_congress_10.png" || 
$a_IconName == "mic_black_court_10.png" || $a_IconName == "mic_black_crisisarea_10.png" || 
$a_IconName == "mic_black_cross_10.png" || $a_IconName == "mic_black_crossbike_10.png" || 
$a_IconName == "mic_black_crossbike2_10.png" || $a_IconName == "mic_black_crossing_10.png" || 
$a_IconName == "mic_black_cycling_10.png" || $a_IconName == "mic_black_darling_10.png" || 
$a_IconName == "mic_black_desert_10.png" || $a_IconName == "mic_green_caravan_01.png" || 
$a_IconName == "mic_black_diver_10.png" || $a_IconName == "mic_black_diver2_10.png" || 
$a_IconName == "mic_black_doc_10.png" || $a_IconName == "mic_black_drinkwater_10.png" || 
$a_IconName == "mic_black_drone_10.png" || $a_IconName == "mic_black_embassy_10.png" || 
$a_IconName == "mic_black_energy_10.png" || $a_IconName == "mic_black_fasting_10.png" || 
$a_IconName == "mic_black_ferry_10.png" || $a_IconName == "mic_black_firstaid_10.png" || 
$a_IconName == "mic_black_fishing_10.png" || $a_IconName == "mic_black_flowers_10.png" || 
$a_IconName == "mic_black_football_10.png" || $a_IconName == "mic_black_forbidden_10.png" || 
$a_IconName == "mic_black_forbidden_11.png" || $a_IconName == "mic_black_funpark_10.png" || 
$a_IconName == "mic_black_g10088_10.png" || $a_IconName == "mic_black_g21090_10.png" || 
$a_IconName == "mic_black_g6269_10.png" || $a_IconName == "mic_black_gasstation_10.png" || 
$a_IconName == "mic_black_geocaching_10.png" || $a_IconName == "mic_black_goal_10.png" || 
$a_IconName == "mic_black_godhouse_10.png" || $a_IconName == "mic_black_graffity_10.png" || 
$a_IconName == "mic_black_grandprix_10.png" || $a_IconName == "mic_black_handball_10.png" || 
$a_IconName == "mic_black_harbor_10.png" || $a_IconName == "mic_black_hardrock_10.png" || 
$a_IconName == "mic_black_healer_10.png" || $a_IconName == "mic_black_helicopter_10.png" || 
$a_IconName == "mic_black_highschool_10.png" || $a_IconName == "mic_black_highway_10.png" || 
$a_IconName == "mic_black_hockey_10.png" || $a_IconName == "mic_black_hospital_10.png" ||
$a_IconName == "mic_black_hut_10.png" || $a_IconName == "mic_black_hymering_10.png" || 
$a_IconName == "mic_black_icefish_10.png" || $a_IconName == "mic_black_icehok_10.png" || 
$a_IconName == "mic_black_iceskate_10.png" || $a_IconName == "mic_black_industry_10.png" || 
$a_IconName == "mic_black_infopoint_10.png" || $a_IconName == "mic_black_jailhouse_10.png" || 
$a_IconName == "mic_black_library_10.png" || $a_IconName == "mic_black_light_10.png" || 
$a_IconName == "mic_black_limit_10.png" || $a_IconName == "mic_black_marathon_10.png" || 
$a_IconName == "mic_black_market_10.png" || $a_IconName == "mic_black_military_10.png" || 
$a_IconName == "mic_black_mobile_10.png" || $a_IconName == "mic_black_monument_10.png" || 
$a_IconName == "mic_black_monument_10.png" || $a_IconName == "mic_black_mosque_10.png" || 
$a_IconName == "mic_black_museum_10.png" || $a_IconName == "mic_black_nardicwalk_10.png" || 
$a_IconName == "mic_black_nature_10.png" || $a_IconName == "mic_black_netcoffee_10.png" || 
$a_IconName == "mic_black_nicolastation_10.png" || $a_IconName == "mic_black_nodrone_10.png" || 
$a_IconName == "mic_black_noenergy_10.png" || $a_IconName == "mic_black_nolan_10.png" || 
$a_IconName == "mic_black_nolight_10.png" || $a_IconName == "mic_black_nomobile_10.png" || 
$a_IconName == "mic_black_nudebeach_10.png" || $a_IconName == "mic_black_observ_10.png" || 
$a_IconName == "mic_black_olymp_10.png" || $a_IconName == "mic_black_olymp2_10.png" || 
$a_IconName == "mic_black_olymphouse_10.png" || $a_IconName == "mic_black_paraglide_10.png" || 
$a_IconName == "mic_black_parking_10.png" || $a_IconName == "mic_black_parkride_10.png" || 
$a_IconName == "mic_black_pharmacy_10.png" || $a_IconName == "mic_black_playyard_10.png" || 
$a_IconName == "mic_black_police_10.png" || $a_IconName == "mic_black_postoffice_10.png" || 
$a_IconName == "mic_black_pov_10.png" || $a_IconName == "mic_black_pubview_10.png" || 
$a_IconName == "mic_black_radiotele_10.png" || $a_IconName == "mic_black_railroad_10.png" || 
$a_IconName == "mic_black_rambler_10.png" || $a_IconName == "mic_black_redlight_10.png" || 
$a_IconName == "mic_black_remote_10.png" || $a_IconName == "mic_black_rent_10.png" || 
$a_IconName == "mic_black_rentbike_10.png" || $a_IconName == "mic_green_caravan_01.png" || 
$a_IconName == "mic_black_rescheli_10.png" || $a_IconName == "mic_black_renting_10.png" || 
$a_IconName == "mic_black_ride_10.png" || $a_IconName == "mic_black_restaurant_10.png" || 
$a_IconName == "mic_black_ropeway_10.png" || $a_IconName == "mic_black_roller_10.png" || 
$a_IconName == "mic_black_sail_10.png" || $a_IconName == "mic_black_runner_10.png" || 
$a_IconName == "mic_black_sailing2_10.png" || $a_IconName == "mic_black_science_10.png" || 
$a_IconName == "mic_black_seaworld_10.png" || $a_IconName == "mic_black_service_10.png" || 
$a_IconName == "mic_black_shopping_10.png" || $a_IconName == "mic_black_skatboard_10.png" || 
$a_IconName == "mic_black_skijump_10.png" || $a_IconName == "mic_black_smoker_10.png" || 
$a_IconName == "mic_black_snow_10.png" || $a_IconName == "mic_black_spotfield_10.png" || 
$a_IconName == "mic_black_start_10.png" || $a_IconName == "mic_black_stop_10.png" || 
$a_IconName == "mic_black_sub_10.png" || $a_IconName == "mic_black_subway_10.png" || 
$a_IconName == "mic_black_surfer_10.png" || $a_IconName == "mic_black_survival_10.png" || 
$a_IconName == "mic_black_swimmer_10.png" || $a_IconName == "mic_black_table_10.png" || 
$a_IconName == "mic_black_tennis_10.png" || $a_IconName == "mic_green_caravan_01.png" || 
$a_IconName == "mic_black_tomb_10.png" || $a_IconName == "mic_black_theater_10.png" || 
$a_IconName == "mic_black_townhall_10.png" || $a_IconName == "mic_black_train_10.png" || 
$a_IconName == "mic_black_triathlon_10.png" || $a_IconName == "mic_black_turbine_10.png" || 
$a_IconName == "mic_black_undergr_10.png" || $a_IconName == "mic_black_walker_10.png" || 
$a_IconName == "mic_black_wastewater_10.png" || $a_IconName == "mic_black_waterball_10.png" || 
$a_IconName == "mic_black_waterhole_10.png" || $a_IconName == "mic_black_waters_10.png" || 
$a_IconName == "mic_black_wifi_10.png" || $a_IconName == "mic_black_wifino_10.png" || 
$a_IconName == "mic_black_windsurf_10.png" || $a_IconName == "mic_black_wine_10.png" || 
$a_IconName == "mic_black_xgames_10.png" || $a_IconName == "mic_black_xgames_11.png" || 
$a_IconName == "mic_black_yoga_10.png" || $a_IconName == "mic_black_youthhostel_10.png" || 
$a_IconName == "mic_black_zoo_10.png" || $a_IconName == "mic_black_soccer_10.png"
){
    return true;
   }
   else {
    return false;
   }
 } 

private function setIconsize($a_IconName, $a_IconHeight = -1, $a_IconWidth = -1, $a_IconFocus = -1){
   Osm::traceText(DEBUG_INFO, "[setIconsize]: Name: ". $a_IconName." Height: ".$a_IconHeight."Width: ".$a_IconWidth." Focus: ".$a_IconFocus);
   if ($this->isOsmIcon($a_IconName)){
   if (!strncmp($a_IconName, 'mic_', 4)){
     $this->icon_height = 41;
	 $this->icon_width = 32;
	 $this->icon_offset_height = -41;
	 $this->icon_offset_width = -16;
   }
   else if ((!strncmp($a_IconName, 'wpttemp-', 8))||($a_IconName == 'marker_blue.png')){
     $this->icon_height = 24;
	 $this->icon_width = 24;
	 $this->icon_offset_height = -24;
	 $this->icon_offset_width = 0;
   }
   else if (($a_IconName == 'hostel.png') || ($a_IconName == 'restaurant.png')){
   	 $this->icon_height = 24;
	 $this->icon_width = 24;
	 $this->icon_offset_height = -12;
	 $this->icon_offset_width = -12;
   }
   else if (($a_IconName == 'guest_house.png') || ($a_IconName == 'home.png') || ($a_IconName == 'hotel.png') || ($a_IconName == 'friends.png') || ($a_IconName == 'camping.png') || ($a_IconName == 'toilets.png')){
	 $this->icon_height = 32;
	 $this->icon_width = 32;
	 $this->icon_offset_height = -16;
	 $this->icon_offset_width = -16;
   }
   else if ($a_IconName == 'airport.png'){
   	 $this->icon_height = 32;
	 $this->icon_width = 31;
	 $this->icon_offset_height = -16;
	 $this->icon_offset_width = -16;
   }
   else if ($a_IconName == 'bus.png'){
     $this->icon_height = 32;
	 $this->icon_width = 26;
	 $this->icon_offset_height = -16;
	 $this->icon_offset_width = -13;
   }
   else if ($a_IconName == 'styria_linux.png'){
     $this->icon_height = 50;
	 $this->icon_width = 36;
	 $this->icon_offset_height = -25;
	 $this->icon_offset_width = -18;
   }
   else if ($a_IconName == 'geocache.png'){
     $this->icon_height = 25;
	 $this->icon_width = 25;
	 $this->icon_offset_height = -12;
	 $this->icon_offset_width = -12;
   }
   else if ($a_IconName == 'services.png'){
     $this->icon_height = 28;
	 $this->icon_width = 32;
	 $this->icon_offset_height = -14;
	 $this->icon_offset_width = -16;
   }
   else if ($a_IconName == 'marker_posts.png'){
     $this->icon_height = 2;
	 $this->icon_width = 3;
	 $this->icon_offset_height = -1;
	 $this->icon_offset_width = -1;
   }
   else {
     echo "e_unknown_icon";
     echo $a_IconName; 
   }
  }
  else{ // it's not an OSM icon
    if (($a_IconHeight != 0) && ($a_IconWidth != 0)){
	  $this->icon_height = $a_IconHeight;
	  $this->icon_width  = $a_IconWidth;
	}
	else {
      Osm::traceText(DEBUG_ERROR, "e_marker_size"); //<= ToDo
      $this->icon_height  = 24;
      $this->icon_width  = 24;
	}
    if ($a_IconFocus == 0){ // center is default
        $this->icon_offset_height = round(-$this->icon_height/2);
        $this->icon_offset_width = round(-$this->icon_width/2);
      }
      else if ($a_IconFocus == 1){ // left bottom
        $this->icon_offset_height = -$this->icon_height;
        $this->icon_offset_width  = 0;
      }
      else if ($a_IconFocus == 2){ // left top
        $this->icon_offset_height = 0;
        $this->icon_offset_width  = 0;
      }
      else if ($a_IconFocus == 3){ // right top
        $this->icon_offset_height = 0;
        $this->icon_offset_width  = -$this->icon_width;
      }
      else if ($a_IconFocus == 4){ // right bottom
        $this->icon_offset_height = -$this->icon_height;
        $this->icon_offset_width  = -$this->icon_width;
      }
      else if ($a_IconFocus == 5){ // center bottom
        $this->icon_offset_height = -$this->icon_height;
        $this->icon_offset_width = round(-$this->icon_width/2);
  }
   else {
	$this->icon_offset_height = -1;
	$this->icon_offset_width = -1;
    }
}
//Osm::traceText(DEBUG_ERROR, "height: ". $a_IconHeight);
//Osm::traceText(DEBUG_ERROR, "width: ". $a_IconWidth);
//Osm::traceText(DEBUG_ERROR, "height: ". $this->icon_offset_height);
//Osm::traceText(DEBUG_ERROR, "width: ". $this->icon_offset_width);
}

  public function setIcon($a_IconName = 'mic_blue_pinother_02.png', $a_IconHeight = -1, $a_IconWidth = -1, $a_IconFocus = -1)
  {
    if ($a_IconName == 'NoName'){
      $a_IconName = 'mic_blue_pinother_02.png';
    }
    $this->icon_name = $this->replaceOldIcon($a_IconName);
    $this->setIconsize($this->icon_name, $a_IconHeight, $a_IconWidth, $a_IconFocus);
    $this->icon_URL = OSM_PLUGIN_ICONS_URL.$this->icon_name;
}

public function __construct($a_IconName = 'mic_blue_pinother_02.png', $a_IconHeight = -1, $a_IconWidth = -1, $a_IconFocus = -1){
    $this->setIcon($a_IconName, $a_IconHeight, $a_IconWidth, $a_IconFocus);
    return;
  }

  public function getIconURL(){
    return $this->icon_URL;
  }

  public function getIconName(){
    return $this->icon_name;
  }
  
  public function getMarkerheight(){
    return $this->icon_height;
  }
  public function getMarkerwidth(){
    return $this->icon_width;
  }
  public function getIconOffsetheight(){
    return $this->icon_offset_height;
  }
  public function getIconOffsetwidth(){
    return $this->icon_offset_width;
}
  public function traceIconInfo(){
    echo $this->icon_name;
    echo $this->icon_URL;
    echo $this->icon_height;
    echo $this->icon_width;
    echo $this->icon_offset_width;
    echo $this->icon_offset_height;
  }

}
?>
