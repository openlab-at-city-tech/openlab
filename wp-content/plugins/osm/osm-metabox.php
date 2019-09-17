<?php
/*  (c) Copyright 2019  MiKa (http://wp-osm-plugin.HanBlog.Net)

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
  wp_enqueue_script('OSM_metabox_Script',Osm_OL_3_MetaboxEvents_LibraryLocation);
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
                   'map_zoom' => '',
                   'map_type' => '',
                   'map_border' => '',
                   'map_controls' => '',
                   'geotag_nonce' => wp_create_nonce( 'osm_geotag_nonce'),
                   'marker_nonce' => wp_create_nonce( 'osm_marker_nonce'),
                   'plugin_url' => OSM_PLUGIN_URL
            ));

  $post_types = get_post_types();
  foreach ($post_types as $post_type) {
    add_meta_box( 'osm-sc-meta', 'WP OSM Plugin shortcode generator', 'osm_map_create_shortcode_function', $post_type, 'normal', 'high' );
  }
}

function osm_map_create_shortcode_function( $post ) {
?>

  <script type="text/javascript">
  /* <![CDATA[ */

  jQuery(document).ready(function(){

	jQuery('ul.osm-tabs li').click(function(){
		var tab_id = jQuery(this).attr('data-tab');

		jQuery('ul.osm-tabs li').removeClass('current');
		jQuery('.tab-content').removeClass('current');

		jQuery(this).addClass('current');
		jQuery("#"+tab_id).addClass('current');

                if(typeof map_ol3js_1 === "undefined"){
                 map_ol3js_6.updateSize();
                }
                else {
                  map_ol3js_1.updateSize();
                 }
		map_ol3js_2.updateSize();
		map_ol3js_3.updateSize();
		map_ol3js_4.updateSize();
		map_ol3js_5.updateSize();
                
	})

	jQuery('ul.osm-marker-tabs li').click(function(){
		var tab_id = jQuery(this).attr('marker-tab');

		jQuery('ul.osm-marker-tabs li').removeClass('current');
		jQuery('.marker-tab-content').removeClass('current');

		jQuery(this).addClass('current');
		jQuery("#"+tab_id).addClass('current');
                
	})
	jQuery('ul.osm-geo-marker-tabs li').click(function(){
		var tab_id = jQuery(this).attr('geo-marker-tab');

		jQuery('ul.osm-geo-marker-tabs li').removeClass('current');
		jQuery('.geo-marker-tab-content').removeClass('current');

		jQuery(this).addClass('current');
		jQuery("#"+tab_id).addClass('current');
                
	})

})

  // sometimes mapNr starts with 2 ... needs to by analysed
  jQuery(window).load(function(){	
  if(typeof map_ol3js_1 === "undefined"){
    map_ol3js_2.updateSize();
  }
  else {
   map_ol3js_1.updateSize();
  }
  });



  /* ]]> */
  </script>

<div class="osm-tab-container">
  	<ul class="osm-tabs">
		<li class="tab-link current" data-tab="tab_add_marker"><?php _e('Map & Marker','OSM') ?></li>
		<li class="tab-link" data-tab="tab_file_list"><?php _e('Map & GPX | KML','OSM') ?></li>
		<li class="tab-link" data-tab="tab_geotag"><?php _e('Map & geotags','OSM') ?></li>
		<li class="tab-link" data-tab="tab_set_geotag"><?php _e('Set Geotag','OSM') ?></li>
		<li class="tab-link" data-tab="tab_about"><?php _e('About','OSM') ?></li>
	</ul>

    <div id="tab_add_marker" class="tab-content current"><br/>
      <b>1. <?php _e('map type','OSM') ?></b>:
      <select name="osm_add_marker_map_type" id="osm_add_marker_map_type">
      <?php include('osm-maptype-select.php'); ?>
      </select>
      <b>2. <?php _e('map border','OSM') ?></b>:
      <select name="osm_add_marker_border" id="osm_add_marker_border">
      <?php include('osm-color-select.php'); ?>
      </select><br/><br/>
       <b>3. <?php _e('map controls','OSM') ?></b>:
        <input type="checkbox" name="osm_add_marker_fullscreen" id="osm_add_marker_fullscreen" value="fullscreen"> <?php _e('fullscreen','OSM') ?>
        <input type="checkbox" name="osm_add_marker_scaleline" id="osm_add_marker_scaleline" value="scaleline"> <?php _e('scaleline','OSM') ?>
        <input type="checkbox" name="osm_add_marker_mouseposition" id="osm_add_marker_mouseposition" value="mouseposition"> <?php _e('mouse position','OSM') ?> <br/>
       <br/>
       <b>4. <?php _e('marker icon','OSM') ?></b>:
       <br/>
      <?php include('osm-marker-select.php'); ?>
      <br/>
      <b>5. <?php _e('marker text','OSM') ?>  (<?php _e('optional','OSM') ?>)</b>: <br/>
        <?php _e('Use &lt;b&gt;&lt;/b&gt; for bold, &lt;i&gt;&lt;/i&gt; for kursiv and&lt;br&gt; for new line.','OSM') ?><br/>
         <textarea id="osm_add_marker_text" name="marker_text" cols="35" rows="4"></textarea>
      <br/><br/>
      <b>5. <?php $url = 'http://wp-osm-plugin.hanblog.net/';
      $link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode. Find more features  <a href="%s" target="_blank">here</a> !', 'OSM' ), esc_url( $url ) );
      echo $link; ?></b>

	  <?php $latlon = OSM_default_lat.','.OSM_default_lon; $zoom = OSM_default_zoom;
	echo Osm::sc_OL3JS(array('map_center'=>$latlon,'zoom'=>$zoom, 'width'=>'100%','height'=>'450', 'map_event'=>'AddMarker')); ?>

      <div id="Marker_Div"><br/></div><br/>
        <a class="button" onClick="osm_savePostMarker();"> <?php _e('Save marker and generate shortcode','OSM')?> </a><br/><br/>      <?php _e('Copy the shortcode and paste it to your post/page','OSM') ?><br/>


 </div> <!-- id="tab_geotag" -->

  <!-- id="add map with gpx or kml file" -->
	<div id="tab_file_list" class="tab-content">
	  <?php _e('Add a map with an GPX or KML file. <br/>Copy file address at Meditathek.','OSM') ?><br/><br/>
      <b>1. <?php _e('Map type','OSM') ?></b>:
      <select name="osm_file_list_map_type" id="osm_file_list_map_type">
      <?php include('osm-maptype-select.php'); ?>
      </select>
      <b>2. <?php _e('map border','OSM') ?></b>:
      <select name="osm_file_border" id="osm_file_border">
      <?php include('osm-color-select.php'); ?>
      </select>
      <br/><br/>
      <b>3. <?php _e('map controls','OSM') ?></b>:
        <input type="checkbox" name="file_fullscreen" id="file_fullscreen" value="file_fullscreen"> <?php _e('fullscreen','OSM') ?>
        <input type="checkbox" name="file_scaleline" id="file_scaleline" value="file_scaleline"> <?php _e('scaleline','OSM') ?>
        <input type="checkbox" name="file_mouseposition" id="file_mouseposition" value="file_mouseposition"> <?php _e('mouse position','OSM') ?> <br/><br/>

    <b>4. <?php _e('Paste the local URL of file here: ','OSM') ?></b>
	<p><?php _e('Do not save any of your personal data in the plugins/osm folder but in the upload folder!','OSM') ?></p>
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
	<br/> <br/>

  <input type="checkbox" name="show_selection_box" value="show_selection_box"> <?php _e('Show track selection box under the map','OSM') ?><br/><br/>

     <b>5. <?php $url = 'http://wp-osm-plugin.hanblog.net/';
      $link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode. Find more features  <a href="%s" target="_blank">here</a> !', 'OSM' ), esc_url( $url ) );
      echo $link;?></b><br/><br/>

    <?php $latlon = OSM_default_lat.','.OSM_default_lon; $zoom = OSM_default_zoom;
	  echo Osm::sc_OL3JS(array('map_center'=>$latlon,'zoom'=>$zoom, 'width'=>'100%','height'=>'450', 'map_event'=>'FileSC')); ?>


     </div> <!-- id="tab_file_list" -->

    <div id="tab_geotag" class="tab-content">

	<?php _e('Add a map with all geotagged posts / pages of your site. <br/>Set geotag to your post at [Set geotag] tab.','OSM') ?><br/><br/>
  <ol>
        <li>
          <?php _e('map type','OSM') ?>
          <select name="osm_geotag_map_type" id="osm_geotag_map_type">
          <?php include('osm-maptype-select.php'); ?>
          </select>
        </li>
    <li>
      <?php _e('marker icon','OSM') ?><br/>
      <?php include('osm-marker-tagged-select.php'); ?><br/><br/><br/>
    </li>
    <li>
      <?php _e('marker style','OSM');echo " "; _e('cluster','OSM') ?><br/>
      <label class="metabox-label">
        <input type="radio" name="tagged_marker_style" id="tagged_marker_style" value="standard" checked="checked" />
        <?php  echo '<img src="'.OSM_PLUGIN_URL.'/images/marker_standard_01.png" align="left" hspace="5" alt="mic_black_pinother_02.png">'; ?>
      </label>
      <label class="metabox-label">
        <input type="radio" name="tagged_marker_style" id="tagged_marker_style" value="cluster"/>
        <?php  echo '<img src="'.OSM_PLUGIN_URL.'/images/marker_cluster_01.png" align="left" hspace="5" alt="mic_red_pinother_02.png">'; ?>
      </label>
    </li>
    <br/><br/><br/><br/><br/><br/><br/>
    <li>
      <?php _e('post type','OSM') ?>
      <select name="osm_geotag_posttype" id="osm_geotag_posttype">
        <option value="post"><?php _e('post','OSM') ?></option>
        <option value="page"><?php _e('page','OSM') ?></option>
      </select>
    </li>
    <li>
      <?php
        _e('Category Filter','OSM') ;
        wp_dropdown_categories(
        array('hide_empty' => 0, 'value_field'=>'name', 'name' => 'category_parent', 'orderby' => 'name', 'selected' => '0', 'hierarchical' => true, 'show_option_none' => __('None')));
        _e(' OR ','OSM') ;?>
	<label for="TagFilter">Tag Tilter: </label>
	<input type="text" id="tag_filter" name="tag_filter">
    </li>
    <li>
        <?php
          $url = 'http://wp-osm-plugin.hanblog.net/';
          $link = sprintf( __( 'Adjust the map and click into the map to generate the shortcode. Find more features  <a href="%s" target="_blank">here</a> !', 'OSM' ), esc_url( $url ) );
            echo $link;
            echo Osm::sc_OL3JS(array('map_center'=>$latlon,'zoom'=>$zoom,'width'=>'100%','height'=>'450','map_event'=>'TaggedPostsSC'));
          ?>
    </li>
  </ol>

     </div> <!-- id="tab_geotag" -->

    <div id="tab_set_geotag" class="tab-content">
        <?php _e('You can set a geotag (lat/lon) and an icon for this post / page.') ?><br/>
        <b>1. <?php _e('post icon','OSM') ?></b>:
        <br/>
       <?php include('osm-marker-geotag-select.php'); ?>
       <br/><br/><br/><br/><br/><br/><br/>
       <b>2. <?php _e('Click into the map for geotag!','OSM') ?></b>:

	   <?php $latlon = OSM_default_lat.','.OSM_default_lon; $zoom = OSM_default_zoom;
		 echo Osm::sc_OL3JS(array('map_center'=>$latlon,'zoom'=>$zoom, 'width'=>'100%','height'=>'450', 'map_event'=>'SetGeotag')); ?>

       <div id="Geotag_Div"><br/></div><br/>
       <a class="button" onClick="osm_saveGeotag();"> <?php _e('Save','OSM')?> </a><br/><br/>
    </div>  <!-- class="tab_set_geotag" -->

     <div id="tab_about" class="tab-content">
     <b><?php echo 'WordPress OSM Plugin '.PLUGIN_VER.' '; ?></b><br/>
     <b><font color="#FF0000"><?php echo 'We need help for translations!'; ?></b></font>
     <table border="0" >
       <tr><td><?php  echo '<p><img src="'.OSM_PLUGIN_URL.'/WP_OSM_Plugin_Logo.png" align="left" vspace="10" hspace="20" alt="Osm Logo"></p>'; ?> </td>
       <td><b><?php _e('Coordination','OSM'); echo " & "; _e('Development','OSM') ?>:</b><a target="_new" href="http://mika.HanBlog.net"> MiKa</a><br/><br/>
       <b><?php _e('Thanks for Translation to','OSM') ?>:</b><br/> Вячеслав Стренадко, <a target="_new" href="http://tounoki.org/">Tounoki</a>, Sykane, <a target="_new" href="http://www.pibinko.org">Andrea Giacomelli</a>, <a target="_new" href="http://www.cspace.ro">Sorin Pop</a><br/><br/><b>


<b><?php _e('Thanks for Icons to','OSM') ?>:</b><br/>
<a target="_new" href="http://rgb-labs.com">Dash</a>, <a target="_new" href="https://github.com/mapbox/maki">Maki</a>, <a target="_new" href="http://www.sjjb.co.uk/mapicons/">SJJB</a>, <a target="_new" href="http://publicdomainvectors.org/en/free-clipart/">Publicdomainvectors</a>, <a target="_new" href="https://pixabay.com/de/">Pixaba</a>, <a target="_new" href="https://thenounproject.com/">TheNounProject</a><br/><br/>
       <?php
       $wp_url = "https://wordpress.org/support/view/plugin-reviews/osm";
       $rate_txt = sprintf( __( 'If you like the OSM plugin rate it on WP <a href="%s">here</a> ', 'OSM' ), esc_url($wp_url));
       echo $rate_txt; ?>
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

</div>  <!-- class="tabs" --><br/><br/>
<h3><span style="color:green"><?php _e('Copy the generated shortcode/customfield/argument: ','OSM') ?></span></h3>
<div id="ShortCode_Div"><?php _e('If you click into the map this text is replaced','OSM') ?> </div> <br/>
<?php
}
