<?php
/*
  Option page for OSM wordpress plugin
  MiKa * created: april 2009
  plugin: http://wp-osm-plugin.HanBlog.net
*/
?>

<div class="wrap">
<table border="0">
 <tr>
  <td><p><img src="<?php echo OSM_PLUGIN_URL ?>/WP_OSM_Plugin_Logo.png" alt="Osm Logo"></p></td>
  <td><h2>OpenStreetMap Plugin <?php echo PLUGIN_VER ?> </h2></td>
 </tr>
</table>
<h3><?php _e('How to add a map to your post/page','OSM-plugin') ?></h3>
<ol>
  <li><?php _e('choose a marker if you want to','OSM-plugin') ?></li>
  <li><?php _e('add a gpx file and/or marker file if you want to','OSM-plugin') ?></li>
  <li><?php _e('add a border around the map and or some controls if you want to','OSM-plugin') ?></li>
  <li><?php _e('click on the map to generate the shortcode (if you chose a marker it is placed where you clicked)','OSM-plugin') ?></li>
  <li><?php _e('copy the shortcode from below the map and paste it in your post/page','OSM-plugin') ?></li>
  <li><?php _e('delete the argument - type - if you want all osm maps to be available','OSM-plugin') ?></li>
  <li><?php _e('add other arguments to insert tracks, points ... or modify mapsize ... if needed','OSM-plugin') ?></li>
  <li style="color:red"> <?php _e('do not save any of your personal data in the plugins/osm folder but in the upload folder!','OSM-plugin') ?></li>
</ol>
<br>

<h3><?php _e('If you want to add a marker choose one of the supported:','OSM-plugin') ?></h3>
  <li><?php _e('the marker is placed where you click into the map','OSM-plugin') ?></li>
<form name="Markerform" action="">
    <select name="osm_marker">
        <option value="none"><?php _e('none','OSM-plugin') ?></option>
        <option value="wpttemp-green.png"><?php _e('Waypoint','OSM-plugin');echo ' ';_e('green','OSM-plugin') ?></option>
        <option value="wpttemp-red.png"><?php _e('Waypoint','OSM-plugin');echo ' ';_e('red','OSM-plugin') ?></option>
        <option value="wpttemp-yellow.png"><?php _e('Waypoint','OSM-plugin');echo ' ';_e('yellow','OSM-plugin') ?></option>
        <option value="marker_blue.png"><?php _e('Marker','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_black_pinother_01.png"><?php _e('Pin #1','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
        <option value="mic_black_pin-export_01.png"><?php _e('Pin #2','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
        <option value="mic_black_pinother_02.png"><?php _e('Pin #3','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
        <option value="mic_red_pinother_02.png"><?php _e('Pin #3','OSM-plugin');echo ' ';_e('red','OSM-plugin') ?></option>
        <option value="mic_green_pinother_02.png"><?php _e('Pin #3','OSM-plugin');echo ' ';_e('green','OSM-plugin') ?></option>
        <option value="mic_blue_pinother_02.png"><?php _e('Pin #3','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>       
        <option value="mic_photo_icon.png"><?php _e('Camera','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option> 
        <option value="mic_yel_restaurant_chinese_01.png"><?php _e('Chin. restaurant','OSM-plugin');echo ' ';_e('yellow','OSM-plugin') ?></option>
        <option value="mic_yel_icecream_01.png"><?php _e('Icecream','OSM-plugin');echo ' ';_e('yellow','OSM-plugin') ?></option>
        <option value="mic_yel_campingtents_01.png"><?php _e('Campingtents','OSM-plugin');echo ' ';_e('yellow','OSM-plugin') ?></option>
        <option value="mic_green_campingcar_01.png"><?php _e('Campingcar','OSM-plugin');echo ' ';_e('green','OSM-plugin') ?></option>
        <option value="mic_brown_pickup_camper_01.png"><?php _e('Pickup camper','OSM-plugin');echo ' ';_e('brown','OSM-plugin') ?></option>
        <option value="mic_toilets_disability_01.png"><?php _e('Toilets disability','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_shark_icon.png"><?php _e('Shark','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_red_pizzaria_01.png"><?php _e('Pizzaria','OSM-plugin');echo ' ';_e('red','OSM-plugin') ?></option>
        <option value="mic_parasailing_01.png"><?php _e('Parasailing','OSM-plugin');echo ' ';_e('orange','OSM-plugin') ?></option>
        <option value="mic_green_horseriding_01.png"><?php _e('Horseriding','OSM-plugin');echo ' ';_e('green','OSM-plugin') ?></option>
        <option value="mic_cycling_icon.png"><?php _e('Cycling','OSM-plugin');echo ' ';_e('orange','OSM-plugin') ?></option>
        <option value="mic_coldfoodcheckpoint_01.png"><?php _e('Coldfookcheckpoint','OSM-plugin');echo ' ';_e('orange','OSM-plugin') ?></option>
        <option value="mic_blue_tweet_01.png"><?php _e('Tweet','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_blue_information_01.png"><?php _e('Information','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_blue_horseriding_01.png"><?php _e('Horserinding','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_black_train_01.png"><?php _e('Train','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
        <option value="mic_black_steamtrain_01.png"><?php _e('Steamtrain','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
        <option value="mic_black_powerplant_01.png"><?php _e('Powerplant','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
        <option value="mic_black_parking_bicycle-2_01.png"><?php _e('Bicycle Parking','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
        <option value="mic_black_cctv_01.png"><?php _e('cctv','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
        <option value="mic_blue_toilets_01.png"><?php _e('Toilets','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_blue_scubadiving_01.png"><?php _e('Scubadiving','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_orange_motorbike_01.png"><?php _e('Motorbike','OSM-plugin');echo ' ';_e('orange','OSM-plugin') ?></option>
        <option value="mic_orange_sailing_1.png"><?php _e('Sailing','OSM-plugin');echo ' ';_e('orange','OSM-plugin') ?></option>
        <option value="mic_orange_fishing_01.png"><?php _e('Fishing','OSM-plugin');echo ' ';_e('orange','OSM-plugin') ?></option>
        <option value="mic_blue_mobilephonetower_01.png"><?php _e('Mobilephonetower','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_orange_hiking_01.png"><?php _e('Hiking','OSM-plugin');echo ' ';_e('orange','OSM-plugin') ?></option>
        <option value="mic_blue_bridge_old_01.png"><?php _e('Bridge','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
        <option value="mic_black_memorial_01.png"><?php _e('Memorial','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
        <option value="car.png"><?php _e('Car','OSM-plugin') ?></option>
        <option value="bus.png"><?php _e('Bus','OSM-plugin') ?></option>
        <option value="bicycling.png"><?php _e('Bicycling','OSM-plugin') ?></option>
        <option value="airport.png"><?php _e('Airport','OSM-plugin') ?></option>
        <option value="motorbike.png"><?php _e('Motorbike','OSM-plugin') ?></option>
        <option value="hostel.png"><?php _e('Hostel','OSM-plugin') ?></option>
        <option value="guest_house.png"><?php _e('Guesthouse','OSM-plugin') ?></option>
        <option value="camping.png"><?php _e('Camping','OSM-plugin') ?></option>
        <option value="geocache.png"><?php _e('Geocache','OSM-plugin') ?></option>
        <option value="styria_linux.png"><?php _e('Styria Tux','OSM-plugin') ?></option>
    </select>
</form>
  <li><?php _e('alternativly you can also add privat marker from the upload folder (see ','OSM-plugin') ?> <a target="_new" href="http://wp-osm-plugin.hanblog.net/exsamples/display-a-marker/">osm plugin page</a>)</li>

<h3><?php _e('If you want to add a text to your marker change the text here','OSM-plugin') ?></h3>
<form name="Markertextform">
    <?php _e('1st row of markertext: ','OSM-plugin') ?> <input name="MarkerText_01" type="text" size="30" maxlength="200" value="Max Mustermann"><br>
    <?php _e('2nd row of markertext: ','OSM-plugin') ?> <input name="MarkerText_02" type="text" size="30" maxlength="200" value="Musterstr. 90"><br>
    <?php _e('3rd row of markertext: ','OSM-plugin') ?> <input name="MarkerText_03" type="text" size="30" maxlength="200" value="1020 Mustercity"><br>
    <?php _e('4th row of markertext: ','OSM-plugin') ?> <input name="MarkerText_04" type="text" size="30" maxlength="200" value="MusterCountry"><br>
  </p>
</form>

<form name="Naviform" action="">
<?php _e('Add a link in the marker to route to your marker: ','OSM-plugin') ?> <br>
<img src="<?php echo OSM_PLUGIN_URL ?>/icons/YN_01.png" alt="YourNavigation"><input type="radio" name="Navi_Link" value="yn"> <span><?php _e('YourNavigation   ','OSM-plugin') ?> </span>
<img src="<?php echo OSM_PLUGIN_URL ?>/icons/OSRM_01.png" alt="Open Source Routing Machine"><input type="radio" name="Navi_Link" value="osrm"> <span><?php _e('Open Source Routing Machine ','OSM-plugin') ?> </span>
</form>


<h3><?php _e('If you want to add a file (KML, GPX, TXT):','OSM-plugin') ?></h3>
<form name="Addfileform" action="">
  <select name="osm_add_file">
    <option value="none"><?php _e('none','OSM-plugin') ?></option>
    <option value="kml"><?php _e('KML file','OSM-plugin') ?></option>
    <option value="gpx_red"><?php _e('GPX file','OSM-plugin');echo ' ';_e('red','OSM-plugin') ?></option>
    <option value="gpx_green"><?php _e('GPX file','OSM-plugin');echo ' ';_e('green','OSM-plugin') ?></option>
    <option value="gpx_blue"><?php _e('GPX file','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option>
    <option value="gpx_black"><?php _e('GPX file','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option>
    <option value="text"><?php _e('text file','OSM-plugin') ?></option>
    </select>
    <br>
    <?php _e('paste the local URL of file here: ','OSM-plugin') ?><input name="FileURL" type="text" size="30" maxlength="200" value="http://">
</form>

<h3><?php _e('If you want to add a border around the map choose the colour:','OSM-plugin') ?></h3>
<form name="Bordercolourform" action="">
<li> <?php _e('colour of a thin solid border:','OSM-plugin') ?> 
  <input type="radio" name="Border_colour" value="red"> <span style="color:red"><?php _e('red ','OSM-plugin') ?> </span>
  <input type="radio" name="Border_colour" value="green"> <span style="color:green"><?php _e('green ','OSM-plugin') ?> </span>
  <input type="radio" name="Border_colour" value="blue"> <span style="color:blue"><?php _e('blue ','OSM-plugin') ?> </span>
  <input type="radio" name="Border_colour" value="black"> <span style="color:black"><?php _e('black ','OSM-plugin') ?> </span>
</li>
</form>
<h3><?php _e('If you want to add some controls to your map add it here:','OSM-plugin') ?></h3>


<form name="MapControlform" action="">
<img src="<?php echo OSM_PLUGIN_URL ?>/WP_OSM_Plugin_Scaleline.png" alt="Scaleline"><input type="checkbox" name="MapControl" value="scaleline"> <?php _e('scaleline  ','OSM-plugin') ?>
<img src="<?php echo OSM_PLUGIN_URL ?>/WP_OSM_Plugin_Mouseposition.png" alt="Scaleline"><input type="checkbox" name="Mouseposition" value="mouseposition"> <?php _e('mouse position','OSM-plugin') ?><br>
</form>

<br>
<form name="ControlStyleform" action="">
<?php _e('Choose the style of the controls: ','OSM-plugin') ?> <br>
<img src="<?php echo OSM_PLUGIN_URL ?>/themes/ol/zoom-world-mini.png" alt="OpenLayers default theme"><input type="radio" name="Cntrl_style" value="ol"> 
<span><?php _e('default theme   ','OSM-plugin') ?> </span>
<img src="<?php echo OSM_PLUGIN_URL ?>/themes/ol_orange/zoom-world-mini.png" alt="OpenLayers orange theme"><input type="radio" name="Cntrl_style" value="ol_orange"> 
<span><?php _e('orange   ','OSM-plugin') ?> </span>
<img src="<?php echo OSM_PLUGIN_URL ?>/themes/dark/zoom-world-mini.png" alt="dark theme">
<input type="radio" name="Cntrl_style" value="dark"> 
<span><?php _e('dark theme   ','OSM-plugin') ?> </span>
<input type="radio" name="Cntrl_style" value="private"> 
<span><?php _e('/uploads/osm/theme/','OSM-plugin') ?> </span>
</form>


<h3><?php _e('Misc. settings:','OSM-plugin') ?></h3>
<form name="ZIndexform" action="">
<input type="checkbox" name="ZIndex" value="0"> <?php _e('z-index: 0','OSM-plugin') ?>
</form>

<br>
<h3> <?php _e('Adjust the map and click into the map to get your shortcode below the map','OSM-plugin') ?></h3>
  <li><?php _e('select the area and zoomlevel on the map (get a zoomwindow with shift and mousebutton)','OSM-plugin') ?></li>
  <li><?php _e('choose your maptype with this icon ','OSM-plugin') ?><img src="<?php echo Osm_OL_LibraryPath ?>img/layer-switcher-maximize.png" alt="map type icon"> <?php _e('in the map (google maps will have a license pop up in yor post/page)','OSM-plugin') ?></li>
  <li> <?php _e('your inputs (gpx-file, marker,...) are not displayed in this map but in your post/page ','OSM-plugin') ?></li>
  <li> <?php _e('you can modify your inputs and click again into the map to generate another shortcode ','OSM-plugin') ?></li> 
<br> 
<?php echo Osm::sc_showMap(array('msg_box'=>'sc_gen','lat'=>OSM_default_lat,'long'=>OSM_default_lon,'zoom'=>OSM_default_zoom, 'type'=>'AllOsm', 'width'=>'600','height'=>'450', 'map_border'=>'thin solid blue', 'control'=>'mouseposition,scaleline')); ?>
<br>
<h3><span style="color:green"> >> <?php _e('Copy the shortcode and paste it into the content of your post/article: ','OSM-plugin') ?></span></h3>
<div id="ShortCode_Div"><?php _e('If you click into the map the shortcode is displayed instead of this text','OSM-plugin') ?></div><br>
<li><?php _e('NOTE: For OpenSeaMap just modify the type to type="OpenSeaMap" manually in the generated shortcode!','OSM-plugin') ?></li>
<h3><?php _e('Some usefull sites for this plugin:','OSM-plugin') ?></h3>
<ol>
  <li><?php _e('for advanced samples visit the ','OSM-plugin') ?><a target="_new" href="http://wp-osm-plugin.HanBlog.net">osm-plugin page</a>.</li>
  <li><?php _e('for questions, bugs and other feedback visit the','OSM-plugin') ?> <a target="_new" href="http://wp-osm-plugin.hanblog.net/forum/forum-en/">EN forum</a>, <a target="_new" href="http://wp-osm-plugin.hanblog.net/forum/forum-de/">DE forum</a></li>
  <li><?php _e('Follow us on twitter: ','OSM-plugin') ?><a target="_new" href="https://twitter.com/wp_osm_plugin">wp-osm-plugin</a>.</li>
  <li><?php _e('download the last version at WordPress.org ','OSM-plugin') ?><a target="_new" href="http://wordpress.org/extend/plugins/osm/">osm-plugin download</a>.</li>
</ol>
<h3><?php _e('If you want to express thanks for this plugin ...','OSM-plugin') ?></h3>
<ol>
  <li><?php _e('give this plugin a good ranking at ','OSM-plugin') ?><a target="_new" href="http://wordpress.org/support/view/plugin-reviews/osm">WordPress.org</a>.</li>
  <li><?php _e('post an article about ','OSM-plugin') ?><a target="_new" href="http://www.OpenStreetMap.org">OpenStreetMap</a><?php _e(' on your blog.','OSM-plugin') ?></li>
</ol>
<form method="post">
 <?php
 /*
 <tr> <h3><?php _e('How to geotag your post/page ','OSM-plugin') ?></h3> </tr>
  <ol>
    <li><?php _e('Choose a Custom Field name here.','OSM-plugin') ?></li>
    <li><?php _e('Add the geoaddress to this Custom Field in your post/page.','OSM-plugin') ?></li>
  </ol>
 <tr>
  <td><label for="osm_custom_field"><?php _e('Custom Field Name','OSM-plugin') ?>:</label></td>
  <td><input type="text" name="osm_custom_field" value="<?php echo $osm_custom_field ?>" /></td>
 </tr>
 */
 ?>
 <tr> <h3>  PHP Interface</h3> </tr>
 <tr>
  <td><label for="osm_zoom_level"><?php _e('Map Zoomlevel for the PHP Link (1-17)','OSM-plugin') ?>:</label></td>
  <td><input type="text" name="osm_zoom_level" value="<?php echo $osm_zoom_level ?>" /></td>
 </tr>
</table>
<div class="submit"><input type="submit" name="Options" value="<?php _e('Update Options','OSM-plugin') ?> &raquo;" /></div>
</div>
</form>
