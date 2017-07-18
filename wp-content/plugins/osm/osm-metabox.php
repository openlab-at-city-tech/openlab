<?php
/*  (c) Copyright 2015  MiKa (http://wp-osm-plugin.HanBlog.Net)

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
function osm_map_create() {
  //create a custom meta box

  wp_enqueue_script( 'ajax-script', plugins_url( '/js/osm-plugin-lib.js', __FILE__ ), array('jquery') );
  wp_localize_script( 'ajax-script', 'osm_ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 
                   'lat' => '', 
                   'lon' => '', 
                   'icon' => '', 
                   'post_id' => '',
                   'MarkerLat' =>'',
                   'MarkerLon' =>'',
                   'MarkerIcon' => '',
                   'MarkerName' => '',
                   'MarkerText' => '',
                   'geotag_nonce' => wp_create_nonce( 'osm_geotag_nonce'),
                   'marker_nonce' => wp_create_nonce( 'osm_marker_nonce')
            ));
  $screens = array( 'post', 'page' );
  foreach ($screens as $screen) {
    add_meta_box( 'osm-sc-meta', 'WP OSM Plugin shortcode generator', 'osm_map_create_shortcode_function', $screen, 'normal', 'high' );
  }
}

function osm_map_create_shortcode_function( $post ) {
?>

  <style type="text/css">
    <link rel="stylesheet" type="text/css" href="'.OSM_PLUGIN_URL.'/css/osm_map.css" />
  </style>
  <script type="text/javascript">
  /* <![CDATA[ */
  jQuery(document).ready(function(){

    jQuery('.tabs .tab-links a').on('click', function(e)  {
      var currentAttrValue = jQuery(this).attr('href');
 
      // Show/Hide Tabs
      jQuery('.tabs ' + currentAttrValue).show().siblings().hide();
 
      // Change/remove current tab to active
      jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
 
      e.preventDefault();
    });
  });
  /* ]]> */
  </script>

<div class="tabs">
  <ul class="tab-links">
    <li class="active"><a href="#tab_marker"><?php _e('Map & Marker','OSM') ?></a></li>
    <li><a href="#tab_file_list">Map & GPX | KML</a></li>
    <li><a href="#tab_geotag"><?php _e('Map & geotags','OSM') ?></a></li>
    <li><a href="#tab_add_marker"><?php _e('Add Marker','OSM') ?></a></li>
    <li><a href="#tab_set_geotag"><?php _e('Set Geotag','OSM') ?></a></li>
    <li><a href="#tab_icons"><?php _e('Icons','OSM') ?></a></li>
    <li><a href="#tab_about"><?php _e('About','OSM') ?></a></li>
  </ul>
 
  <div class="tab-content">
    <div id="tab_marker" class="tab active">
    <?php _e('Add a map with or without a marker. <br>Markers have to be created at [Add Marker] tab.','OSM') ?><br><br>
      <b>1. <?php _e('map type','OSM') ?></b>:
      <select name="osm_marker_map_type">
      <?php include('osm-maptype-select.php'); ?>
      </select>
      <b>2. <?php _e('map border','OSM') ?></b>: 
      <select name="osm_marker_border">
      <?php include('osm-color-select.php'); ?>
      </select>
      <b>3. <?php _e('marker id','OSM') ?></b>:
      <select name="osm_marker_id">
      <option value="no"><?php _e('none','OSM') ?></option>
      <option value="all"><?php _e('all','OSM') ?></option>
      <option value="1">01</option>
      <option value="2">02</option>
      <option value="3">03</option>
      <option value="4">04</option>
      <option value="5">05</option>
      <option value="6">06</option>
      <option value="7">07</option>
      <option value="8">08</option>
      <option value="9">09</option>
      </select><br>
      <br>
      <b>3. <?php _e('map controls','OSM') ?></b>: 
        <input type="checkbox" name="fullscreen" value="fullscreen"> <?php _e('fullscreen','OSM') ?> 
        <input type="checkbox" name="scaleline" value="scaleline"> <?php _e('scaleline','OSM') ?> 
        <input type="checkbox" name="mouseposition" value="mouseposition"> <?php _e('mouse position','OSM') ?> <br><br>
      <b>4. <?php $url = 'http://wp-osm-plugin.hanblog.net/'; 
      $link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode!', 'OSM' ), esc_url( $url ) );
      echo $link; ?></b><br><br>
      <?php echo Osm::sc_showMap(array('msg_box'=>'metabox_marker_sc_gen','lat'=>OSM_default_lat,'long'=>OSM_default_lon,'zoom'=>OSM_default_zoom, 'type'=>'mapnik_ssl', 'width'=>'600','height'=>'400', 'map_border'=>'thin solid grey', 'theme'=>'dark', 'control'=>'mouseposition,scaleline')); ?>
    </div> <!-- id="tab_marker" -->

	<div id="tab_file_list" class="tab">
     <?php _e('Add a map with an GPX or KML file. <br>Copy file address at Meditathek.','OSM') ?><br><br>
      <b>1. <?php _e('Map type','OSM') ?></b>:
      <select name="osm_file_list_map_type">
      <?php include('osm-maptype-select.php'); ?>
      </select>
      <b>2. <?php _e('map border','OSM') ?></b>: 
      <select name="osm_file_border">
      <?php include('osm-color-select.php'); ?>
      </select>
      <br>
      <b>3. <?php _e('map controls','OSM') ?></b>: 
        <input type="checkbox" name="file_fullscreen" value="file_fullscreen"> <?php _e('fullscreen','OSM') ?> 
        <input type="checkbox" name="file_scaleline" value="file_scaleline"> <?php _e('scaleline','OSM') ?> 
        <input type="checkbox" name="file_mouseposition" value="file_mouseposition"> <?php _e('mouse position','OSM') ?> <br><br>
      <b>4. <?php _e('Paste the local URL of file here: ','OSM') ?></b><br>
      <?php _e('Do not save any of your personal data in the plugins/osm folder but in the upload folder!','OSM') ?><br>
      <input name="osm_file_list_URL" type="text" size="30" maxlength="200" value="../../../../wp-content/uploads/YOUR-FILE"><br>
      <b>5. <?php _e('Route Color','OSM') ?></b>: 
      <select name="osm_file_list_color">
      <?php include('osm-color-select.php'); ?>
      </select><br><br>     
     <b>6. <?php $url = 'http://wp-osm-plugin.hanblog.net/'; 
      $link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode. Find more features  <a href="%s" target="_blank">here</a> !', 'OSM' ), esc_url( $url ) );
      echo $link;?></b><br><br>
      <?php echo Osm::sc_showMap(array('msg_box'=>'metabox_file_list_sc_gen','lat'=>OSM_default_lat,'long'=>OSM_default_lon,'zoom'=>OSM_default_zoom, 'type'=>'mapnik_ssl', 'width'=>'600','height'=>'400', 'map_border'=>'thin solid grey', 'theme'=>'dark', 'control'=>'mouseposition,scaleline')); ?>
     </div> <!-- id="tab_file_list" -->
     
    <div id="tab_geotag" class="tab">
    <?php _e('Add a map with all geotagged posts / pages of your site. <br>Set geotag to your post at [Set geotag] tab.','OSM') ?><br><br>
       <b>1. <?php _e('map type','OSM') ?></b>:
       <select name="osm_geotag_map_type">
       <?php include('osm-maptype-select.php'); ?>
       </select>
             </select>
      <b>2. <?php _e('map border','OSM') ?></b>: 
      <select name="osm_geotag_map_border">
      <?php include('osm-color-select.php'); ?>
      </select>
       <br><br>
        <b>2. <?php _e('marker icon','OSM') ?></b>:
       <select name="osm_geotag_marker">
       <?php include('osm-marker-select.php'); ?>
       </select> 
       ( <a href="http://wp-osm-plugin.hanblog.net/cc0-license-map-icons-collection/" target="_blank"> icons</a> )
       <br>
       <b>3. <?php _e('marker style','OSM') ?></b>: 
      <select name="osm_geotag_marker_style">
             <option value="standard"><?php _e('standard','OSM') ?></option>
       <option value="cluster"><?php _e('cluster','OSM') ?></option>
      
      </select>
      <b>4. <?php _e('style color','OSM') ?></b>: 
      <select name="osm_geotag_marker_color">
      <?php include('osm-color-select.php'); ?>
      </select>
      <br><br>
       <b>3. <?php _e('post type','OSM') ?></b>:
       <select name="osm_geotag_posttype">
       <option value="post"><?php _e('post','OSM') ?></option>
       <option value="page"><?php _e('page','OSM') ?></option>
       </select><br>
        <b>4. <?php _e('Category Filter','OSM') ?></b>:
       <?php $SelectedCat = wp_dropdown_categories(array('hide_empty' => 0, 'value_field'=>'name', 'name' => 'category_parent', 'orderby' => 'name', 'selected' => $category->parent, 'hierarchical' => true, 'show_option_none' => __('None')));?>
       <br>
       <b>5. <?php $url = 'http://wp-osm-plugin.hanblog.net/'; 
       $link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode. Find more features  <a href="%s" target="_blank">here</a> !', 'OSM' ), esc_url( $url ) );
        echo $link; ?><br><br></b>
       <?php echo Osm::sc_showMap(array('msg_box'=>'metabox_geotag_sc_gen','lat'=>OSM_default_lat,'long'=>OSM_default_lon,'zoom'=>OSM_default_zoom, 'type'=>'mapnik_ssl', 'width'=>'600','height'=>'400', 'map_border'=>'thin solid grey', 'theme'=>'dark', 'control'=>'mouseposition,scaleline')); ?>
     </div> <!-- id="tab_geotag" -->
 
      <div id="tab_set_geotag" class="tab">
         <?php _e('You can set a geotag (lat/lon) and an icon for this post / page.') ?><br>
        <b>1. <?php _e('post icon','OSM') ?></b>:
         <select name="osm_marker_geotag"><?php include('osm-marker-select.php'); ?></select><br>
       <b>2. <?php _e('Click into the map for geotag!','OSM') ?></b>:
       <?php echo Osm::sc_showMap(array('msg_box'=>'metabox_geotag_gen','lat'=>OSM_default_lat,'long'=>OSM_default_lon,'zoom'=>OSM_default_zoom, 'type'=>'mapnik_ssl', 'width'=>'600','height'=>'400', 'map_border'=>'thin solid grey', 'theme'=>'dark', 'control'=>'mouseposition')); ?><br>
      <div id="Geotag_Div"><br></div><br>
        <a class="button" onClick="osm_saveGeotag();"> <?php _e('Save','OSM')?> </a><br><br>
       </div> <!-- id="tab_set_geotag" -->
 
 
 <div id="tab_add_marker" class="tab">
      <?php _e('You can store up to nine markers here for this post / page','OSM') ?><br>

      <b>1. <?php _e('marker id','OSM') ?></b>: 
      <select name="osm_add_marker_id">
      <option value="1">01</option><option value="2">02</option><option value="3">03</option>
      <option value="4">04</option><option value="5">05</option><option value="6">06</option>
      <option value="7">07</option><option value="8">08</option><option value="9">09</option>
      </select><br>
      <b>2. <?php _e('marker name','OSM') ?></b>: 
        <input name="osm_add_marker_name" type="text" size="20" maxlength="30" value="NoName"><br>
       <b>3. <?php _e('marker icon','OSM') ?></b>:
      <select name="osm_add_marker_icon">
      <?php include('osm-marker-select.php'); ?>
      </select>
      ( <a href="http://wp-osm-plugin.hanblog.net/cc0-license-map-icons-collection/" target="_blank"> icons</a> )<br>

        <b>4. <?php _e('marker text','OSM') ?>  (<?php _e('optional','OSM') ?>)</b>: <br>
        <?php _e('Use &lt;b&gt;&lt;/b&gt; for bold, &lt;i&gt;&lt;/i&gt; for kursiv and&lt;br&gt; for new line.','OSM') ?><br>
         <textarea id="osm_add_marker_text" name="marker_text" cols="35" rows="4"></textarea> 	
      <br>
      <br>
      <b>5. <?php $url = 'http://wp-osm-plugin.hanblog.net/'; 
      $link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode. Find more features  <a href="%s" target="_blank">here</a> !', 'OSM' ), esc_url( $url ) );
      echo $link; ?></b><br><br>
      <?php echo Osm::sc_showMap(array('msg_box'=>'metabox_add_marker_sc_gen','lat'=>OSM_default_lat,'long'=>OSM_default_lon,'zoom'=>OSM_default_zoom, 'type'=>'mapnik_ssl', 'width'=>'600','height'=>'400', 'map_border'=>'thin solid grey', 'theme'=>'dark', 'control'=>'mouseposition,scaleline')); ?>
      <div id="Marker_Div"><br></div><br>
        <a class="button" onClick="osm_savePostMarker();"> <?php _e('Save','OSM')?> </a><br><br>
    </div> <!-- id="tab_add_marker" -->
 
 
 <div id="tab_icons" class="tab">
    <b><?php _e('These icons are integrated into WP OSM Plugin with different colors:','OSM') ?></b><br>
     <table border="0" >
       <tr>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_red_pinother_02.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Pin #3','OSM') ?></td>
              <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_black_empty_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Empty Marker','OSM') ?></td>
              <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/wpttemp-green.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Waypoint','OSM') ?></td>
       </tr>
       <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_green_caravan_01.png" align="left" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Caravan','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_orange_archery_01.png" align="left" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Archery','OSM') ?></td>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_black_train_01.png" align="left" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Train','OSM') ?></td>
       </tr>
        <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_green_audio_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Speaker','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_toilets_disability_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Toilets disability','OSM') ?></td>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_information_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Information','OSM') ?></td>
       </tr>
        <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_green_icecream_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Icecream','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_black_heart_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Heart','OSM') ?></td>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_mobilephonetower_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Mobilephonetower','OSM') ?></td>
       </tr>       
        <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_black_camera_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Camera','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_cycling_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Cycling','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_orange_fishing_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Fishing','OSM') ?></td>
       </tr>   
        <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_drinkingwater_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Drinkingwater','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_black_powerplant_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Powerplant','OSM') ?></td>
              <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_red_pirates_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Pirates','OSM') ?></td>
       </tr>   
        <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_orange_hiking_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Hiking','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_horseriding_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Horserinding','OSM') ?></td>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_black_parking_bicycle-2_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Bicycle Parking','OSM') ?></td>
       </tr>   
        <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_brown_harbor_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Harbor','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_bridge_old_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Bridge','OSM') ?></td>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_red_pizzaria_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Pizzaria','OSM') ?></td>
       </tr> 
        <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_toilets_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Toilets','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_shower_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Shower','OSM') ?></td>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_brown_fillingstation_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Fillingstation','OSM') ?></td>
       </tr>       
               <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_lighthouse-2_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Lighthouse','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_brown_parking_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Parking','OSM') ?></td>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_green_palm-tree-export_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Palm tree','OSM') ?></td>
       </tr>   
       <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_orange_sailing_1.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Sailing','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_black_cctv_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('cctv','OSM') ?></td>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_orange_motorbike_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Motorbike','OSM') ?></td>
       </tr>  
       <tr>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_blue_scubadiving_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Scubadiving','OSM') ?></td>
       <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_green_car_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Car','OSM') ?></td>
        <td><?php  echo '<p><img src="'.OSM_PLUGIN_ICONS_URL.'/mic_black_steamtrain_01.png" align="left" hspace="5" alt="Osm Logo"></p>'; ?> </td>
       <td><?php _e('Steamtrain','OSM') ?></td>
       </tr>  
</table>
     <br><?php _e('Find them in the icon dropdown box of the map generators','OSM') ?>
    </div> <!-- id="tab_icons" -->
 
 
     <div id="tab_about" class="tab">
     <b><?php echo 'WordPress OSM Plugin '.PLUGIN_VER.' '; ?></b><br>
     <b><font color="#FF0000"><?php echo 'We need help for translations!'; ?></b></font>
     <table border="0" >
       </tr><td><?php  echo '<p><img src="'.OSM_PLUGIN_URL.'/WP_OSM_Plugin_Logo.png" align="left" vspace="10" hspace="20" alt="Osm Logo"></p>'; ?> </td>
       <td><b><?php _e('Coordination','OSM'); echo " & "; _e('Development','OSM') ?>:</b><a target="_new" href="http://mika.HanBlog.net"> MiKa</a><br><br>
       <b><?php _e('Thanks for Translation to','OSM') ?>:</b><br> Вячеслав Стренадко, <a target="_new" href="http://tounoki.org/">Tounoki</a>, Sykane, <a target="_new" href="http://www.pibinko.org">Andrea Giacomelli</a><br><br><b>
       <?php
       $wp_url = "https://wordpress.org/support/view/plugin-reviews/osm";
       $rate_txt = sprintf( __( 'If you like the OSM plugin rate it on WP <a href="%s">here</a> ', 'OSM' ), esc_url($wp_url));
       echo $rate_txt; ?>
	   <?php
       $fb_url = "https://www.facebook.com/WpOsmPlugin";
	   $fb_txt = sprintf( __( ' or ike us on facebook <a href="%s">here</a>. ', 'OSM' ), esc_url($fb_url));
       echo $fb_txt; ?>
       <?php _e('Thanks!','OSM') ?></b>
       </td></tr>
     </table>
                  
     <b><?php _e('Some usefull sites for this plugin:','OSM') ?></b>
     <ol>
       <li><?php _e('for advanced samples visit the ','OSM') ?><a target="_new" href="http://wp-osm-plugin.HanBlog.net">osm-plugin page</a>.</li>
       <li><?php _e('for questions, bugs and other feedback visit the','OSM') ?> <a target="_new" href="http://wp-osm-plugin.hanblog.net/forums/">EN | DE forum</a></li>
       <li><?php _e('Follow us on twitter: ','OSM') ?><a target="_new" href="https://twitter.com/wp_osm_plugin">wp-osm-plugin</a>.</li>
      <li><?php _e('download the last version at WordPress.org ','OSM') ?><a target="_new" href="http://wordpress.org/extend/plugins/osm/">osm-plugin download</a>.</li>
    </ol>
    </div> <!-- id="tab_about" -->
    
  </div><!-- class="tab-content" -->
</div>  <!-- class="tabs" --><br><br>
<h3><span style="color:green"><?php _e('Copy the generated shortcode/customfield/argument: ','OSM') ?></span></h3>
<div id="ShortCode_Div"><?php _e('If you click into the map this text is replaced','OSM') ?> </div> <br>
<?php
}
