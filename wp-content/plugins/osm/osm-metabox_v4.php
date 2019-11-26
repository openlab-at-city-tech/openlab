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
    add_meta_box( 'osm-sc-meta', 'WP OSM Plugin shortcode generator 1', 'osm_map_create_shortcode_function', $screen, 'normal', 'high',    array(
        '__block_editor_compatible_meta_box' => false,
    )
);



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
    <li class="active"><a href="#tab_set_geotag"><?php _e('Set Geotag','OSM') ?></a></li>
    <li><a href="#tab_marker"><?php _e('Map & Marker','OSM') ?></a></li>
    <li><a href="#tab_add_marker"><?php _e('Add Marker','OSM') ?></a></li>
    <li><a href="#tab_geotag"><?php _e('Map & geotags','OSM') ?></a></li>
    <li><a href="#tab_file_list"><?php _e('Map & GPX | KML','OSM') ?></a></li>
    <li><a href="#tab_icons"><?php _e('Icons','OSM') ?></a></li>
    <li><a href="#tab_about"><?php _e('About','OSM') ?></a></li>
  </ul>
 
  <div class="tab-content">
    <div id="tab_marker" class="tab ">
    <?php _e('Add a map with or without a marker. <br>Markers have to be created at [Add Marker] tab.','OSM') ?>
    	<ol>
      		<li>
      			<label for="osm_marker_map_type">
      				<?php _e('map type','OSM') ?>
      			</label>
		    	<select name="osm_marker_map_type">
			      <?php include('osm-maptype-select.php'); ?>
      			</select>
      		</li>
      		<li>	
      			<label for="osm_marker_border">
	  				<?php _e('map border','OSM') ?>
	  			</label> 
		    	<select name="osm_marker_border">
			      <?php include('osm-color-select.php'); ?>
				</select>
			</li>
			<li>
				<label for="osm_marker_id">
					<?php _e('marker id','OSM') ?>
				</label>
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
				</select>
			</li>
			<li>
				<label class="osmCheckoxHeadline">
					<?php _e('map controls','OSM') ?>
				</label>
		        <input type="checkbox" name="fullscreen" value="fullscreen"><label for="fullscreen" class="osmCheckbox"><?php _e('fullscreen','OSM') ?></label>
       			<input type="checkbox" name="scaleline" value="scaleline"><label for="scaleline" class="osmCheckbox"><?php _e('scaleline','OSM') ?></label>
		        <input type="checkbox" name="mouseposition" value="mouseposition"><label for="mouseposition" class="osmCheckbox"><?php _e('mouse position','OSM') ?></label>
		    </li>
		    <li> 
		    	<label class="osmMapSelect">
		    	<?php 
		    		$url = 'http://wp-osm-plugin.hanblog.net/'; 
     				$link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode!', 'OSM' ), esc_url( $url ) );
      				echo $link; 
      			?>
      			</label>
		      	<?php 
		      		echo Osm::sc_showMap(
		      			array(
		      				'msg_box' => 'metabox_marker_sc_gen',
		      				'lat' => OSM_default_lat,
		      				'long' => OSM_default_lon,
		      				'zoom' => OSM_default_zoom, 
		      				'type' => 'mapnik_ssl', 
		      				'width' => '600',
		      				'height' => '400', 
		      				'map_border' => 'thin solid grey', 
		      				'theme' => 'dark', 
		      				'control' => 'mouseposition,scaleline'
		      			)
		      		); 
		      	?>
			</li>
		</ol> 	
    </div> <!-- id="tab_marker" -->

	<div id="tab_file_list" class="tab">
		<?php _e('Add a map with an GPX or KML file. <br>Copy file address at Meditathek.','OSM') ?>
		<ol>
      		<li>
      			<label for="osm_file_list_map_type">
      				<?php _e('map type','OSM') ?>
      			</label>
		    	<select name="osm_file_list_map_type">
			      <?php include('osm-maptype-select.php'); ?>
      			</select>
      		</li>
      		<li>
      			<label for="osm_file_border">
      				<?php _e('map border','OSM') ?>
      			</label>
		    	<select name="osm_file_border">
			      <?php include('osm-color-select.php'); ?>
      			</select>
      		</li>
			<li>
				<label class="osmCheckoxHeadline">
					<?php _e('map controls','OSM') ?>
				</label>
		        <input type="checkbox" name="file_fullscreen" value="file_fullscreen"><label for="file_fullscreen" class="osmCheckbox"><?php _e('fullscreen','OSM') ?></label>
       			<input type="checkbox" name="file_scaleline" value="file_scaleline"><label for="file_scaleline" class="osmCheckbox"><?php _e('scaleline','OSM') ?></label>
		        <input type="checkbox" name="file_mouseposition" value="mouseposition"><label for="file_mouseposition" class="osmCheckbox"><?php _e('mouse position','OSM') ?></label>
		    </li>
		    <li>
				<label class="osmCheckoxHeadline">
					<?php _e('Paste the local URL of file here: ','OSM') ?>
				</label>
				<p class="notice"><?php _e('Do not save any of your personal data in the plugins/osm folder but in the upload folder!','OSM') ?></p>
		    	<input type="text" class="osmFileName" name="osm_file_list_URL[0]" value="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
		    	<input type="text" class="osmFileTitle" name="osm_file_list_title[0]" placeholder="<?php _e('file title','OSM') ?>" />
		    	<input type="color" class="osmFileColor"  name="osm_file_list_color[0]" />
		    	<br />
		    	<input type="text" class="osmFileName" name="osm_file_list_URL[1]" placeholder="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
		    	<input type="text" class="osmFileTitle" name="osm_file_list_title[1]" placeholder="<?php _e('file title','OSM') ?>" />
		    	<input type="color" class="osmFileColor"  name="osm_file_list_color[1]" />
		    	<br />
		    	<input type="text" class="osmFileName" name="osm_file_list_URL[2]" placeholder="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
		    	<input type="text" class="osmFileTitle" name="osm_file_list_title[2]" placeholder="<?php _e('file title','OSM') ?>" />
		    	<input type="color" class="osmFileColor"  name="osm_file_list_color[2]" />
		    	<br />
		    	<input type="text" class="osmFileName" name="osm_file_list_URL[3]" placeholder="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
		    	<input type="text" class="osmFileTitle" name="osm_file_list_title[3]" placeholder="<?php _e('file title','OSM') ?>" />
		    	<input type="color" class="osmFileColor"  name="osm_file_list_color[3]" />
		    	<br />
		    	<input type="text" class="osmFileName" name="osm_file_list_URL[4]" placeholder="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
		    	<input type="text" class="osmFileTitle" name="osm_file_list_title[4]" placeholder="<?php _e('file title','OSM') ?>" />
		    	<input type="color" class="osmFileColor"  name="osm_file_list_color[4]" />
		    	<br />
	      	</li>		
	      	<li> 
		    	<label class="osmMapSelect">
		    	<?php 
		    		$url = 'http://wp-osm-plugin.hanblog.net/'; 
     				$link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode. Find more features  <a href="%s" target="_blank">here</a> !', 'OSM' ), esc_url( $url ) );
      				echo $link; 
      			?>
      			</label>
		      	<?php 
		      		echo Osm::sc_showMap(
		      			array(
		      				'msg_box' => 'metabox_file_list_sc_gen',
		      				'lat' => OSM_default_lat,
		      				'long' => OSM_default_lon,
		      				'zoom' => OSM_default_zoom, 
		      				'type' => 'mapnik_ssl', 
		      				'width' => '600',
		      				'height' => '400', 
		      				'map_border' => 'thin solid grey', 
		      				'theme' => 'dark', 
		      				'control' => 'mouseposition,scaleline'
		      			)
		      		); 
		      	?>
			</li>
		</ol>	
	</div> <!-- id="tab_file_list" -->

	<div id="tab_geotag" class="tab">
		<?php _e('Add a map with all geotagged posts / pages of your site. <br />Set geotag to your post at [Set geotag] tab.','OSM') ?>
		<ol>
      		<li>
      			<label for="osm_geotag_map_type">
      				<?php _e('map type','OSM') ?>
      			</label>
		    	<select name="osm_geotag_map_type">
			      <?php include('osm-maptype-select.php'); ?>
      			</select>
      		</li>
      		<li>
      			<label for="osm_geotag_map_border">
      				<?php _e('map border','OSM') ?>
      			</label>
		    	<select name="osm_geotag_map_border">
			      <?php include('osm-color-select.php'); ?>
      			</select>
      		</li>
			<li>
				<label for="osm_geotag_marker">
					<?php _e('marker icon','OSM') ?> (<a href="http://wp-osm-plugin.hanblog.net/cc0-license-map-icons-collection/" target="_blank">Icons</a> )
				</label>
				<select name="osm_geotag_marker" class="osm_add_marker_icon">
					<?php include('osm-marker-select.php'); ?>
				</select> 
			</li>
			<li>
				<label for="osm_geotag_marker_style">
      				<?php _e('marker style','OSM') ?>
      			</label>
      			<select name="osm_geotag_marker_style">
					<option value="standard"><?php _e('standard','OSM') ?></option>
					<option value="cluster"><?php _e('cluster','OSM') ?></option>
				</select>
			</li>
			<li>	
				<label for="osm_geotag_marker_color">
					<?php _e('style color','OSM') ?>
				</label>
				<select name="osm_geotag_marker_color">
					<?php include('osm-color-select.php'); ?>
				</select> 
			</li>
			<li>
				<label for="osm_geotag_posttype">
					<?php _e('post type','OSM') ?>
				</label>
				<select name="osm_geotag_posttype">
					<option value="post"><?php _e('post','OSM') ?></option>
					<option value="page"><?php _e('page','OSM') ?></option>
				</select>
			</li>
			<li>
				<label for="category_parent">
					<?php _e('Category Filter','OSM') ?>
				</label>
				<?php wp_dropdown_categories(
					array(
						'hide_empty' => 0, 
						'value_field'=>'name', 
						'name' => 'category_parent', 
						'orderby' => 'name', 
						'selected' => $category->parent, 
						'hierarchical' => true, 
						'show_option_none' => __('None')
					));
				?>
			</li>
			<li> 
		    	<label class="osmMapSelect">
		    	<?php 
		    		$url = 'http://wp-osm-plugin.hanblog.net/'; 
     				$link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode. Find more features  <a href="%s" target="_blank">here</a> !', 'OSM' ), esc_url( $url ) );
      				echo $link; 
      			?>
      			</label>
		      	<?php 
		      		echo Osm::sc_showMap(
		      			array(
		      				'msg_box' => 'metabox_geotag_sc_gen',
		      				'lat' => OSM_default_lat,
		      				'long' => OSM_default_lon,
		      				'zoom' => OSM_default_zoom, 
		      				'type' => 'mapnik_ssl', 
		      				'width' => '600',
		      				'height' => '400', 
		      				'map_border' => 'thin solid grey', 
		      				'theme' => 'dark', 
		      				'control' => 'mouseposition,scaleline'
		      			)
		      		); 
		      	?>
			</li>
		</ol>	
	</div> <!-- id="tab_geotag" -->

	<div id="tab_set_geotag" class="tab active">
		<?php _e('You can set a geotag (lat/lon) and an icon for this post / page.') ?>
		<ol>
			<li>
				<label for="osm_marker_geotag">
					<?php _e('post icon','OSM') ?>
				</label>
				<select name="osm_marker_geotag">
					<?php include('osm-marker-select.php'); ?>
				</select>
			</li>
			<li>
				<label class="osmMapSelect">
					<?php _e('Click into the map for geotag!','OSM') ?>
				</label>	
				<?php 
		      		echo Osm::sc_showMap(
		      			array(
		      				'msg_box' => 'metabox_geotag_gen',
		      				'lat' => OSM_default_lat,
		      				'long' => OSM_default_lon,
		      				'zoom' => OSM_default_zoom, 
		      				'type' => 'mapnik_ssl', 
		      				'width' => '600',
		      				'height' => '400', 
		      				'map_border' => 'thin solid grey', 
		      				'theme' => 'dark', 
		      				'control' => 'mouseposition,scaleline'
		      			)
		      		); 
		      	?>
		      	<div id="Geotag_Div"></div>
		      	<a class="button" onClick="osm_saveGeotag();"> <?php _e('Save','OSM')?> </a>
			</li>
		</ol>
	</div> <!-- id="tab_set_geotag" -->

 
	<div id="tab_add_marker" class="tab">
    	<?php _e('You can store up to nine markers here for this post / page','OSM') ?><br />
		<ol> 
			<li>
				<label for="osm_add_marker_id">
					<?php _e('marker id','OSM') ?>
				</label>
				<select name="osm_add_marker_id">
					<option value="1">01</option>
					<option value="2">02</option>
					<option value="3">03</option>
					<option value="4">04</option>
					<option value="5">05</option>
					<option value="6">06</option>
					<option value="7">07</option>
					<option value="8">08</option>
					<option value="9">09</option>
      			</select>
      		</li>
      		<li>
      			<label for="osm_add_marker_name">
      				<?php _e('marker name','OSM') ?>
      			</label> 
		        <input name="osm_add_marker_name" type="text" size="20" maxlength="30" placeholder="NoName">
		    </li>
		    <li> 
		    	<label for="osm_add_marker_icon">
      				<?php _e('marker icon','OSM') ?> (<a href="http://wp-osm-plugin.hanblog.net/cc0-license-map-icons-collection/" target="_blank">icons</a> )
      			</label>
      			<select name="osm_add_marker_icon" class="osm_add_marker_icon">
			      <?php include('osm-marker-select.php'); ?>
		    	</select>
      		</li>
			<li>
				<label for="osm_add_marker_text">
					<?php _e('marker text','OSM') ?>  (<?php _e('optional','OSM') ?>)
				</label>
				<?php _e('Use &lt;b&gt;&lt;/b&gt; for bold, &lt;i&gt;&lt;/i&gt; for kursiv and&lt;br&gt; for new line.','OSM') ?>
        		<textarea id="osm_add_marker_text" name="marker_text" cols="35" rows="4"></textarea> 	
        	</li>
        	<li> 
		    	<label class="osmMapSelect">
		    	<?php 
		    		$url = 'http://wp-osm-plugin.hanblog.net/'; 
     				$link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode. Find more features  <a href="%s" target="_blank">here</a> !', 'OSM' ), esc_url( $url ) );
      				echo $link; 
      			?>
      			</label>
		      	<?php 
		      		echo Osm::sc_showMap(
		      			array(
		      				'msg_box' => 'metabox_add_marker_sc_gen',
		      				'lat' => OSM_default_lat,
		      				'long' => OSM_default_lon,
		      				'zoom' => OSM_default_zoom, 
		      				'type' => 'mapnik_ssl', 
		      				'width' => '600',
		      				'height' => '400', 
		      				'map_border' => 'thin solid grey', 
		      				'theme' => 'dark', 
		      				'control' => 'mouseposition,scaleline'
		      			)
		      		); 
		      	?>
			</li>
      	</ol>
      	<div id="Marker_Div"></div>
        <a class="button" onClick="osm_savePostMarker();"> <?php _e('Save','OSM')?> </a>
    </div> <!-- id="tab_add_marker" -->
 
 
 <div id="tab_icons" class="tab">
    <strong><?php _e('These icons are integrated into WP OSM Plugin with different colors:','OSM') ?></strong><br />
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
     <br /><?php _e('Find them in the icon dropdown box of the map generators','OSM') ?>
    </div> <!-- id="tab_icons" -->
 
 
     <div id="tab_about" class="tab">
     <strong><?php echo 'WordPress OSM Plugin '.PLUGIN_VER.' '; ?></strong><br />
     <strong><font color="#FF0000"><?php echo 'We need help for translations!'; ?></strong></font>
     <table border="0" >
       </tr><td><?php  echo '<p><img src="'.OSM_PLUGIN_URL.'/WP_OSM_Plugin_Logo.png" align="left" vspace="10" hspace="20" alt="Osm Logo"></p>'; ?> </td>
       <td><strong><?php _e('Coordination','OSM'); echo " & "; _e('Development','OSM') ?>:</strong><a target="_new" href="http://mika.HanBlog.net"> MiKa</a><br /><br />
       <strong><?php _e('Thanks for Translation to','OSM') ?>:</strong><br /> Вячеслав Стренадко, <a target="_new" href="http://tounoki.org/">Tounoki</a>, Sykane, <a target="_new" href="http://www.pibinko.org">Andrea Giacomelli</a><br /><br /><strong>
       <?php
       $wp_url = "https://wordpress.org/support/view/plugin-reviews/osm";
       $rate_txt = sprintf( __( 'If you like the OSM plugin rate it on WP <a href="%s">here</a> ', 'OSM' ), esc_url($wp_url));
       echo $rate_txt; ?>
	   <?php
       $fb_url = "https://www.facebook.com/WpOsmPlugin";
	   $fb_txt = sprintf( __( ' or ike us on facebook <a href="%s">here</a>. ', 'OSM' ), esc_url($fb_url));
       echo $fb_txt; ?>
       <?php _e('Thanks!','OSM') ?></strong>
       </td></tr>
     </table>
                  
     <strong><?php _e('Some usefull sites for this plugin:','OSM') ?></strong>
     <ol>
       <li><?php _e('for advanced samples visit the ','OSM') ?><a target="_new" href="http://wp-osm-plugin.HanBlog.net">osm-plugin page</a>.</li>
       <li><?php _e('for questions, bugs and other feedback visit the','OSM') ?> <a target="_new" href="http://wp-osm-plugin.hanblog.net/forums/">EN | DE forum</a></li>
       <li><?php _e('Follow us on twitter: ','OSM') ?><a target="_new" href="https://twitter.com/wp_osm_plugin">wp-osm-plugin</a>.</li>
      <li><?php _e('download the last version at WordPress.org ','OSM') ?><a target="_new" href="http://wordpress.org/extend/plugins/osm/">osm-plugin download</a>.</li>
    </ol>
    </div> <!-- id="tab_about" -->
    
  </div><!-- class="tab-content" -->
</div>  <!-- class="tabs" --><br /><br />
<h3><span style="color:green"><?php _e('Copy the generated shortcode/customfield/argument: ','OSM') ?></span></h3>
<div id="ShortCode_Div"><?php _e('If you click into the map this text is replaced','OSM') ?> </div>
<?php
}
?>
