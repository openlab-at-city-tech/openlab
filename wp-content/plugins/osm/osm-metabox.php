<?php
/*  (c) Copyright 2022  MiKa (http://wp-osm-plugin.hyumika.com)

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

function osm_enqueue_scripts_styles() {

  wp_enqueue_style( 'Osm_OL_3_style', Osm_OL_3_CSS);
  wp_enqueue_style( 'Osm_OL_3_Ext_style', Osm_OL_3_Ext_CSS);
  wp_enqueue_style( 'osm_map_style', Osm_map_CSS);

  wp_enqueue_script('Osm_OL_3',Osm_OL_3_LibraryLocation); 
  wp_enqueue_script('Osm_OL_3ext',Osm_OL_3_Ext_LibraryLocation);  
  wp_enqueue_script('OSM_metabox_event_Script',Osm_OL_3_MetaboxEvents_LibraryLocation); 
  wp_enqueue_script('Osm_map_startup_3',Osm_map_startup_LibraryLocation);
  wp_enqueue_script('OSM_metabox-script',Osm_OL_3_Metabox_LibraryLocation, array('jquery') );
    
  wp_enqueue_script('ajax-script', plugins_url( '/js/osm-plugin-lib.js', __FILE__ ), array('jquery') );
 
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
                   'plugin_url' => OSM_PLUGIN_URL,
                   'map_name' => '',
                   'map_attr' => 1
            ));
}

function osm_map_create() {
  //create a custom meta box

  add_action( 'admin_enqueue_scripts', 'osm_enqueue_scripts_styles' ); 

  $post_types = get_post_types();
  foreach ($post_types as $post_type) {
    add_meta_box( 'osm-sc-meta', 'WP OSM Plugin shortcode generator', 'osm_map_create_shortcode_function', $post_type, 'normal', 'high' );
  }
}

function osm_map_create_shortcode_function( $post ) {
?>

<div class="osm-tab-container">
  	<ul class="osm-tabs">
		<li class="tab-link current" data-tab="tab_welcome"><?php _e('Welcome','OSM') ?></li>
		<li class="tab-link" data-tab="tab_add_marker"><?php _e('Map & Marker','OSM') ?></li>
		<li class="tab-link" data-tab="tab_file_list"><?php _e('Map & GPX | KML','OSM') ?></li>
		<li class="tab-link" data-tab="tab_geotag"><?php _e('Map & Locations','OSM') ?></li>
		<li class="tab-link" data-tab="tab_set_geotag"><?php _e('Add Location','OSM') ?></li>
		<li class="tab-link" data-tab="tab_troubleshooting"><?php _e('Troubleshooting','OSM') ?></li>
		<li class="tab-link" data-tab="tab_about"><?php _e('About','OSM') ?></li>		
	</ul>

	
   <!-- id="tab_welcome" -->	
	
     <div id="tab_welcome" class="osm-tab-content current">
<?php  echo '<p><img src="'.OSM_PLUGIN_URL.'/WP_OSM_Plugin_Logo.png" align="left" vspace="10" hspace="20" alt="Osm Logo"></p>'; ?>
 <h3><?php echo 'WordPress OSM Plugin '.PLUGIN_VER.' '; ?></h3>
At the top of this panel / metabox you find tabs which allow you to generate a shorcode. You have to copy (Ctrl+C) this shortcode and paste (Ctrl+V) it to your post / page.<br></br> 
<table border="0" >
  <tr>
    <th>Tab</th>
    <th>... what to do</th>
  </tr>
  <tr>
    <td><?php _e('Map & Marker','OSM') ?></td>
    <td>... use this tab if you want to create a map with no or one marker.</td>
   </tr>
   <tr>
     <td><?php _e('Map & GPX | KML','OSM') ?></td>
    <td>... use this tab if you want to create a map with one or more tracks (GPX or KML) or if you want to load a file with more than one marker.</td>
   </tr>
   <tr>
     <td><?php _e('Map & Locations','OSM') ?></td>
     <td>... use this tab if you want to show a map with markers where posts or pages are geotagged. You can geotag a post / page at tab <?php _e('Add Location','OSM') ?>.</td>
   </tr>
   <tr>
     <td><?php _e('Add Location','OSM') ?></td>
     <td>... set a geotag to your post and page. This location is saved with your post / page. You can show a map with your geotags at tab <?php _e('Map & Locations','OSM') ?></td>
   </tr>
   <tr>
     <td><?php _e('Troubleshooting','OSM') ?></td>
     <td>... if it does not work</td>
   </tr>     
   <tr>
     <td><?php _e('About','OSM') ?></td>
     <td>... some Information about the plugin</td>
   </tr>  
</table>
     </div> <!-- id="tab_welcome" -->	
	
	<!-- id="tab_add_marker" -->
    <div id="tab_add_marker" class="osm-tab-content"><br/>
    	<?php _e('Add a map with one marker.','OSM') ?><br/><br/>
      <b>1. <?php _e('map type','OSM') ?></b>:
      <select name="osm_add_marker_map_type" id="osm_add_marker_map_type">
      <?php include('osm-maptype-select.php'); ?>
      </select>
      <b>2. <?php _e('map border','OSM') ?></b>:
      <select name="osm_add_marker_border" id="osm_add_marker_border">
      <?php include('osm-color-select.php'); ?>
   
   
      </select><br/><br/>
       <b>3. <?php _e('map controls','OSM') ?></b>:
        <input type="checkbox" name="osm_add_marker_fullscreen" id="osm_add_marker_fullscreen" value="fullscreen"> <?php _e('fullscreen button','OSM') ?>
        <input type="checkbox" name="osm_add_marker_scaleline" id="osm_add_marker_scaleline" value="scaleline"> <?php _e('scaleline','OSM') ?>
        <input type="checkbox" name="osm_add_marker_mouseposition" id="osm_add_marker_mouseposition" value="mouseposition"> <?php _e('mouse position','OSM') ?>
        <input type="checkbox" name="osm_add_marker_overviewmap" id="osm_add_marker_overviewmap" value="overviewmap"> <?php _e('overviewmap','OSM') ?>
        <input type="checkbox" name="osm_add_marker_bckgrnd_img" id="osm_add_marker_bckgrnd_img" value="osm_add_marker_bckgrnd_img"> <?php _e('background image (GDPR)','OSM') ?> <br/> <br/>
        <input type="checkbox" name="osm_add_marker_show_attribution" id="osm_add_marker_show_attribution" value="osm_add_marker_show_attribution" checked> <?php _e('Display attribution (credit) in the map. ','OSM') ?>
        <span style="color:red"><?php _e('Warning: If you do not check this box, it may violate the license of data or map and have legal consequences!','OSM') ?></span> <!-- <br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Why not enabled by default? Read <a target="_new" href="https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#10-plugins-may-not-embed-external-links-or-credits-on-the-public-site-without-explicitly-asking-the-user%e2%80%99s-permission">Plugins may not embed external links or credits ...</a> --> <br/><br/>
      
       <b>4. <?php _e('marker icon','OSM') ?></b>:
       <br/>
      <?php include('osm-marker-select.php'); ?>
      <br/>
      <b>5. <?php _e('marker text','OSM') ?>  (<?php _e('optional','OSM') ?>)</b>: <br/>
        <?php _e('Use &lt;b&gt;&lt;/b&gt; for bold, &lt;i&gt;&lt;/i&gt; for kursiv and&lt;br&gt; for new line.','OSM') ?><br/>
         <textarea id="osm_add_marker_text" name="marker_text" cols="35" rows="4" placeholder="Use HTML Entities for special characters (eg HTML Tags)!"></textarea>
      <br/><br/>
      

      
      <b>5. <?php _e('Adjust the map and click into the map to place the marker.','OSM') ?></b>

	  <?php $latlon = OSM_default_lat.','.OSM_default_lon; $zoom = OSM_default_zoom;
	echo Osm::sc_OL3JS(array('map_center'=>$latlon,'zoom'=>$zoom, 'width'=>'75%','height'=>'450', 'map_event'=>'AddMarker', 'map_div_name' => 'AddMarker_map')); ?>

      <div id="Marker_Div"><br/>
      <!--  marker info is print here when clicking in the map -->
      </div><br/>
        <a class="button" onClick="osm_savePostMarker(); osm_generateAddMarkerSC();"> <?php _e('Save marker and generate shortcode','OSM')?> </a><br/><br/>      

 </div> <!-- id="tab_add_marker" -->

  <!-- id="add map with gpx or kml file" -->
	<div id="tab_file_list" class="osm-tab-content">
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
        <input type="checkbox" name="file_fullscreen" id="file_fullscreen" value="file_fullscreen"> <?php _e('fullscreen button','OSM') ?>
        <input type="checkbox" name="file_scaleline" id="file_scaleline" value="file_scaleline"> <?php _e('scaleline','OSM') ?>
        <input type="checkbox" name="file_mouseposition" id="file_mouseposition" value="file_mouseposition"> <?php _e('mouse position','OSM') ?>
        <input type="checkbox" name="file_overviewmap" id="file_overviewmap" value="file_overviewmap"> <?php _e('overviewmap','OSM') ?>        
        <input type="checkbox" name="file_bckgrnd_img" id="file_bckgrnd_img" value="file_bckgrnd_img"> <?php _e('background image (GDPR)','OSM') ?> <br/><br/>
        <input type="checkbox" name="osm_file_show_attribution" id="osm_file_show_attribution" value="osm_file_show_attribution" checked> <?php _e('Display attribution (credit) in the map. ','OSM') ?>
        <span style="color:red"><?php _e('Warning: If you do not check this box, it may violate the license of data or map and have legal consequences!','OSM') ?></span><!-- <br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Why not enabled by default? Read <a target="_new" href="https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#10-plugins-may-not-embed-external-links-or-credits-on-the-public-site-without-explicitly-asking-the-user%e2%80%99s-permission">Plugins may not embed external links or credits ...</a>--><br/><br/>

    <b>4. <?php _e('Paste the local URL of file here: ','OSM') ?></b>
	<p><?php _e('Do not save any of your personal data in the plugins/osm folder but in the upload folder!','OSM') ?></p>
	<input type="text" class="osmFileName" name="osm_file_list_URL[0]" placeholder="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
	<input type="text" class="osmFileTitle" name="osm_file_list_title[0]" placeholder="<?php _e('file title','OSM') ?>" />	
   <select class="osmFileColor" name="osm_file_list_color[0]" id="osm_file_list_color[0]">
     <?php include('osm-color-select.php'); ?>
   </select>	
	<br />
	<input type="text" class="osmFileName" name="osm_file_list_URL[1]" placeholder="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
	<input type="text" class="osmFileTitle" name="osm_file_list_title[1]" placeholder="<?php _e('file title','OSM') ?>" />
	<select class="osmFileColor" name="osm_file_list_color[1]" id="osm_file_list_color[1]">
     <?php include('osm-color-select.php'); ?>
   </select>	
	<br />
	<input type="text" class="osmFileName" name="osm_file_list_URL[2]" placeholder="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
	<input type="text" class="osmFileTitle" name="osm_file_list_title[2]" placeholder="<?php _e('file title','OSM') ?>" />
	<select class="osmFileColor" name="osm_file_list_color[2]" id="osm_file_list_color[2]">
     <?php include('osm-color-select.php'); ?>
   </select>	
	<br />
	<input type="text" class="osmFileName" name="osm_file_list_URL[3]" placeholder="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
	<input type="text" class="osmFileTitle" name="osm_file_list_title[3]" placeholder="<?php _e('file title','OSM') ?>" />
	<select class="osmFileColor" name="osm_file_list_color[3]" id="osm_file_list_color[3]">
     <?php include('osm-color-select.php'); ?>
   </select>
	<br />
	<input type="text" class="osmFileName" name="osm_file_list_URL[4]" placeholder="<?php _e('../../../../wp-content/uploads/YOUR-FILE','OSM') ?>" />
	<input type="text" class="osmFileTitle" name="osm_file_list_title[4]" placeholder="<?php _e('file title','OSM') ?>" />
	<select class="osmFileColor" name="osm_file_list_color[4]" id="osm_file_list_color[4]">
     <?php include('osm-color-select.php'); ?>
   </select>
	<br/> <br/>

    <input type="checkbox" name="show_selection_box" id="show_selection_box" value="show_selection_box" onclick="osm_showFileSCmap()"> <?php _e('Show track selection box under the map (only for two or more tracks)','OSM') ?><br/><br/>
                
      

     <b>5. <?php _e('Only if you enabled selection box you have to adjust the map manually.','OSM') ?></b><br/><br/>
      
      <?php $latlon = OSM_default_lat.','.OSM_default_lon; $zoom = OSM_default_zoom;
      echo Osm::sc_OL3JS(array('map_center'=>$latlon,'zoom'=>$zoom, 'width'=>'100%','height'=>'450', 'map_div_vis'=>'none', 'map_div_name' => 'FileSC_map')); ?>

     <div id="File_Div"><br/></div><br/>
     <a class="button" onClick="osm_generateFileSC();"> <?php _e('Generate shortcode for map with GPX/KML file','OSM')?> </a><br/>

     </div> <!-- id="tab_file_list" -->


  <!-- id="add map with geotag" -->
    <div id="tab_geotag" class="osm-tab-content">

	<?php _e('Add a map with all geotagged posts / pages of your site. <br/>Set geotag to your post at [Set geotag] tab.','OSM') ?><br/><br/>
  <ol>
        <li>
          <?php _e('map type','OSM') ?>
          <select name="osm_geotag_map_type" id="osm_geotag_map_type">
          <?php include('osm-maptype-select.php'); ?>
          </select>
        </li>
        <input type="checkbox" name="osm_geotag_show_attribution" id="osm_geotag_show_attribution" value="osm_geotag_show_attribution" checked> <?php _e('Display attribution (credit) in the map. ','OSM') ?>
        <span style="color:red"><?php _e('Warning: If you do not check this box, it may violate the license of data or map and have legal consequences!','OSM') ?></span> <!-- <br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Why not enabled by default? Read <a target="_new" href="https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#10-plugins-may-not-embed-external-links-or-credits-on-the-public-site-without-explicitly-asking-the-user%e2%80%99s-permission">Plugins may not embed external links or credits ...</a>--><br/><br/>
    <li>
      <?php _e('marker icon','OSM') ?><br/>
      <?php include('osm-marker-tagged-select.php'); ?><br/><br/><br/>
    </li>
    <li>
      <?php _e('marker style','OSM')?><br/>
      <label class="metabox-label">
        <input type="radio" name="tagged_marker_style" id="tagged_marker_style" onclick="osm_showTaggedSCmap()" value="standard" checked="checked" />
        <?php  echo '<img src="'.OSM_PLUGIN_URL.'/images/marker_standard_01.png" align="left" hspace="5" alt="mic_black_pinother_02.png">'; ?>
      </label>
      <label class="metabox-label">
        <input type="radio" name="tagged_marker_style" id="tagged_marker_style" onclick="osm_showTaggedSCmap()" value="cluster"/>
        <?php  echo '<img src="'.OSM_PLUGIN_URL.'/images/marker_cluster_01.png" align="left" hspace="5" alt="mic_red_pinother_02.png">'; ?>
      </label>
    </li>
    <br/><br/><br/><br/><br/><br/><br/>
    <li>
      <?php _e('post type','OSM') ?>
      <select name="osm_geotag_posttype" id="osm_geotag_posttype">
        <option value="post"><?php _e('all posts','OSM') ?></option>
        <option value="page"><?php _e('all pages','OSM') ?></option>
        <option value="actual"><?php _e('this post / page','OSM') ?></option>
      </select>
    </li>
    <li>
      <?php
        _e('Category Filter','OSM') ;
        wp_dropdown_categories(
        array('hide_empty' => 0, 'value_field'=>'name', 'name' => 'category_parent', 'orderby' => 'name', 'selected' => '0', 'hierarchical' => true, 'show_option_none' => __('None')));
        _e(' OR ','OSM') ;?>
	<label for="TagFilter">Tag Filter: </label>
	<input type="text" id="tag_filter" name="tag_filter">
    </li>  
    
     <?php _e('Only if marker style is set to cluster you have to adjust the map manually.','OSM') ?></b><br/><br/>
      
      <?php $latlon = OSM_default_lat.','.OSM_default_lon; $zoom = OSM_default_zoom;
      echo Osm::sc_OL3JS(array('map_center'=>$latlon,'zoom'=>$zoom, 'width'=>'100%','height'=>'450', 'map_div_vis'=>'none', 'map_div_name' => 'TaggedSC_map')); ?>   
    </li>
        <li>
        <a class="button" onClick="osm_generateTaggedPostsSC()"> <?php _e('Generate shortcode','OSM')?> </a><br/><br/>      
        <?php _e('Copy the shortcode and paste it to your post/page','OSM') ?><br/>  
    </li>   
    
  </ol>
     </div> <!-- id="tab_geotag" -->

    <div id="tab_set_geotag" class="osm-tab-content">
        <?php _e('You can set a geotag (lat/lon) and an icon for this post / page.') ?><br/>
        <b>1. <?php _e('post icon','OSM') ?></b>:
        <br/>
       <?php include('osm-marker-geotag-select.php'); ?>
       <br/><br/><br/>
       <b>2. <?php _e('Click into the map for geotag!','OSM') ?></b>:

	   <?php $latlon = OSM_default_lat.','.OSM_default_lon; $zoom = OSM_default_zoom;
		 echo Osm::sc_OL3JS(array('map_center'=>$latlon,'zoom'=>$zoom, 'width'=>'100%','height'=>'450', 'map_event'=>'SetGeotag', 'map_div_name' => 'AddGeotag_map')); ?>

       <div id="Geotag_Div"><br/></div><br/>
       <a class="button" onClick="osm_saveGeotag();"> <?php _e('Save','OSM')?> </a><br/><br/>
    </div>  <!-- class="tab_set_geotag" -->
   
   
   <!-- id="tab_troubleshooting" -->		
   <div id="tab_troubleshooting" class="osm-tab-content">
<table border="0" >
  <tr>
    <td><?php  echo '<p><img src="'.OSM_PLUGIN_URL.'/WP_OSM_Plugin_Logo.png" align="left" vspace="10" hspace="20" alt="Osm Logo"></p>'; ?></td>
    <td> <h3><?php echo 'WordPress OSM Plugin '.PLUGIN_VER.' '; ?></h3></td>
   </tr>
</table>
     <b><?php _e('Loading my GPX / KML file does not work','OSM') ?></b><br>
     There are three GPX files provided by the WP OSM Plugin. Try to use them and see if there is a generic problem or it is caused by your personal GPX / KML file: <br><br>
       <?php echo OSM_PLUGIN_URL; echo "examples/sample01.gpx" ?><br>
       <?php echo OSM_PLUGIN_URL; echo "examples/sample02.gpx" ?><br>
       <?php echo OSM_PLUGIN_URL; echo "examples/sample03.gpx" ?><br><br>
       
     <b><?php _e('How can I show more than one marker in a map?','OSM') ?></b><br>       
     You have to use a KML file, find a sample here: <br><br>
     <?php echo OSM_PLUGIN_URL; echo "examples/MarkerSample.kml" ?><br>
     </div> <!-- id="tab_troubleshooting" -->	
     
     <div id="tab_about" class="osm-tab-content">
     <b><?php echo 'WordPress OSM Plugin '.PLUGIN_VER.' '; ?></b><br/>
     <b><font color="#FF0000"><?php echo 'We need help for translations!'; ?></b></font>
     <table border="0" >
       <tr><td><?php  echo '<p><img src="'.OSM_PLUGIN_URL.'/WP_OSM_Plugin_Logo.png" align="left" vspace="10" hspace="20" alt="Osm Logo"></p>'; ?> </td>
       <td><b><?php _e('Coordination','OSM'); echo " & "; _e('Development','OSM') ?>:</b><a target="_new" href="http://hyumika.com"> Michael Kang</a><br/><br/>
       <b><?php _e('Thanks for Translation to','OSM') ?>:</b><br/> Вячеслав Стренадко, <a target="_new" href="http://tounoki.org/">Tounoki</a>, Sykane, <a target="_new" href="http://www.pibinko.org">Andrea Giacomelli</a>, <a target="_new" href="http://www.cspace.ro">Sorin Pop</a>, Olle Zettergren <br/><br/><b>


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
       <li><?php _e('for advanced samples visit the ','OSM') ?><a target="_new" href="http://wp-osm-plugin.hyumika.com">osm-plugin page</a>.</li>
       <li><?php _e('for questions, bugs and other feedback visit the','OSM') ?> <a target="_new" href="https://wp-osm-plugin.hyumika.com/survey/">EN | DE feedback</a></li>
       <li><?php _e('Follow us on twitter: ','OSM') ?><a target="_new" href="https://twitter.com/wp_osm_plugin">wp-osm-plugin</a>.</li>
      <li><?php _e('download the last version at WordPress.org ','OSM') ?><a target="_new" href="http://wordpress.org/extend/plugins/osm/">osm-plugin download</a>.</li>
    </ol>


 </div> <!-- id="tab_about" -->

</div>  <!-- class="tabs" -->
<h3><span style="color:green"><?php _e('Copy the generated shortcode/customfield/argument: ','OSM') ?></span></h3>
<div id="ShortCode_Div"><?php _e('If you click into the map this text is replaced','OSM') ?> </div> <br/>
<?php
}
