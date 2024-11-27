<?php
/*  (c) Copyright 2024  MiKa (http://wp-osm-plugin.hyumika.com)

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
		<li class="tab-link current" data-tab="tab_welcome"><?php esc_html_e('Welcome', 'OSM'); ?></li>
                <li class="tab-link" data-tab="tab_add_marker"><?php esc_html_e('Map & Marker', 'OSM'); ?></li>
                <li class="tab-link" data-tab="tab_file_list"><?php esc_html_e('Map & GPX | KML', 'OSM'); ?></li>
                <li class="tab-link" data-tab="tab_geotag"><?php esc_html_e('Map & Locations', 'OSM'); ?></li>
                <li class="tab-link" data-tab="tab_set_geotag"><?php esc_html_e('Add Location', 'OSM'); ?></li>
                <li class="tab-link" data-tab="tab_troubleshooting"><?php esc_html_e('Troubleshooting', 'OSM'); ?></li>
                <li class="tab-link" data-tab="tab_about"><?php esc_html_e('About', 'OSM'); ?></li>
	</ul>

	
   <!-- id="tab_welcome" -->	
	
     <div id="tab_welcome" class="osm-tab-content current">
<?php echo '<p><img src="' . esc_url(OSM_PLUGIN_URL . '/WP_OSM_Plugin_Logo.png') . '" align="left" vspace="10" hspace="20" alt="' . esc_attr__('Osm Logo', 'OSM') . '"></p>'; ?>
<h3><?php echo esc_html__('WordPress OSM Plugin ', 'OSM') . esc_html(PLUGIN_VER) . ' '; ?></h3>
At the top of this panel / metabox you find tabs which allow you to generate a shorcode. You have to copy (Ctrl+C) this shortcode and paste (Ctrl+V) it to your post / page.<br></br> 

<table border="0">
  <tr>
    <th><?php esc_html_e('Tab', 'OSM'); ?></th>
    <th><?php esc_html_e('... what to do', 'OSM'); ?></th>
  </tr>
  <tr>
    <td><?php esc_html_e('Map & Marker', 'OSM'); ?></td>
    <td><?php esc_html_e('... use this tab if you want to create a map with no or one marker.', 'OSM'); ?></td>
  </tr>
  <tr>
    <td><?php esc_html_e('Map & GPX | KML', 'OSM'); ?></td>
    <td><?php esc_html_e('... use this tab if you want to create a map with one or more tracks (GPX or KML) or if you want to load a file with more than one marker.', 'OSM'); ?></td>
  </tr>
  <tr>
    <td><?php esc_html_e('Map & Locations', 'OSM'); ?></td>
    <td><?php esc_html_e('... use this tab if you want to show a map with markers where posts or pages are geotagged. You can geotag a post / page at tab', 'OSM'); ?> <?php esc_html_e('Add Location', 'OSM'); ?>.</td>
  </tr>
  <tr>
    <td><?php esc_html_e('Add Location', 'OSM'); ?></td>
    <td><?php esc_html_e('... set a geotag to your post and page. This location is saved with your post / page. You can show a map with your geotags at tab', 'OSM'); ?> <?php esc_html_e('Map & Locations', 'OSM'); ?></td>
  </tr>
  <tr>
    <td><?php esc_html_e('Troubleshooting', 'OSM'); ?></td>
    <td><?php esc_html_e('... if it does not work', 'OSM'); ?></td>
  </tr>
  <tr>
    <td><?php esc_html_e('About', 'OSM'); ?></td>
    <td><?php esc_html_e('... some Information about the plugin', 'OSM'); ?></td>
  </tr>
</table>



     </div> <!-- id="tab_welcome" -->	
	
<!-- id="tab_add_marker" -->
<div id="tab_add_marker" class="osm-tab-content"><br/>
    <?php esc_html_e('Add a map with one marker.', 'OSM'); ?><br/><br/>
    <b>1. <?php esc_html_e('map type', 'OSM'); ?></b>:
    <select name="osm_add_marker_map_type" id="osm_add_marker_map_type">
        <?php include('osm-maptype-select.php'); ?>
    </select>
    <b>2. <?php esc_html_e('map border', 'OSM'); ?></b>:
    <select name="osm_add_marker_border" id="osm_add_marker_border">
        <?php include('osm-color-select.php'); ?>
    </select><br/><br/>
    <b>3. <?php esc_html_e('map controls', 'OSM'); ?></b>:
    <input type="checkbox" name="osm_add_marker_fullscreen" id="osm_add_marker_fullscreen" value="fullscreen"> <?php esc_html_e('fullscreen button', 'OSM'); ?>
    <input type="checkbox" name="osm_add_marker_scaleline" id="osm_add_marker_scaleline" value="scaleline"> <?php esc_html_e('scaleline', 'OSM'); ?>
    <input type="checkbox" name="osm_add_marker_mouseposition" id="osm_add_marker_mouseposition" value="mouseposition"> <?php esc_html_e('mouse position', 'OSM'); ?>
    <input type="checkbox" name="osm_add_marker_overviewmap" id="osm_add_marker_overviewmap" value="overviewmap"> <?php esc_html_e('overviewmap', 'OSM'); ?>
    <input type="checkbox" name="osm_add_marker_bckgrnd_img" id="osm_add_marker_bckgrnd_img" value="osm_add_marker_bckgrnd_img"> <?php esc_html_e('background image (GDPR)', 'OSM'); ?> <br/><br/>
    <input type="checkbox" name="osm_add_marker_show_attribution" id="osm_add_marker_show_attribution" value="osm_add_marker_show_attribution" checked> <?php esc_html_e('Display attribution (credit) in the map.', 'OSM'); ?>
    <span style="color:red"><?php esc_html_e('Warning: If you do not check this box, it may violate the license of data or map and have legal consequences!', 'OSM'); ?></span> <!-- <br/>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Why not enabled by default? Read <a target="_new" href="https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#10-plugins-may-not-embed-external-links-or-credits-on-the-public-site-without-explicitly-asking-the-user%e2%80%99s-permission">Plugins may not embed external links or credits ...</a> --> <br/><br/>

    <b>4. <?php esc_html_e('marker icon', 'OSM'); ?></b>:<br/>
    <?php include('osm-marker-select.php'); ?><br/>
    <b>5. <?php esc_html_e('marker text', 'OSM'); ?> (<?php esc_html_e('optional', 'OSM'); ?>)</b>:<br/>
    <?php esc_html_e('Use &lt;b&gt;&lt;/b&gt; for bold, &lt;i&gt;&lt;/i&gt; for kursiv and &lt;br&gt; for new line.', 'OSM'); ?><br/>
    <textarea id="osm_add_marker_text" name="marker_text" cols="35" rows="4" placeholder="<?php esc_attr_e('Use HTML Entities for special characters (eg HTML Tags)!', 'OSM'); ?>"></textarea><br/><br/>

    <b>5. <?php esc_html_e('Adjust the map and click into the map to place the marker.', 'OSM'); ?></b>
    <?php
    $latlon = OSM_default_lat . ',' . OSM_default_lon;
    $zoom = OSM_default_zoom;
    echo Osm::sc_OL3JS([
        'map_center' => $latlon,
        'zoom' => $zoom,
        'width' => '75%',
        'height' => '450',
        'map_event' => 'AddMarker',
        'map_div_name' => 'AddMarker_map'
    ]);
    ?>
    <div id="Marker_Div"><br/>
    <!-- marker info is printed here when clicking in the map -->
    </div><br/>
        <a class="button" onClick="osm_savePostMarker(); osm_generateAddMarkerSC();"> <?php _e('Save marker and generate shortcode','OSM')?> </a><br/><br/>      

 </div> <!-- id="tab_add_marker" -->

<!-- id="add map with gpx or kml file" -->
<div id="tab_file_list" class="osm-tab-content">
    <?php esc_html_e('Add a map with a GPX or KML file. <br/>Copy file address at Mediathek.', 'OSM'); ?><br/><br/>
    <b>1. <?php esc_html_e('Map type', 'OSM'); ?></b>:
    <select name="osm_file_list_map_type" id="osm_file_list_map_type">
        <?php include('osm-maptype-select.php'); ?>
    </select>
    <b>2. <?php esc_html_e('Map border', 'OSM'); ?></b>:
    <select name="osm_file_border" id="osm_file_border">
        <?php include('osm-color-select.php'); ?>
    </select>
    <br/><br/>
    <b>3. <?php esc_html_e('Map controls', 'OSM'); ?></b>:
    <input type="checkbox" name="file_fullscreen" id="file_fullscreen" value="file_fullscreen"> <?php esc_html_e('Fullscreen button', 'OSM'); ?>
    <input type="checkbox" name="file_scaleline" id="file_scaleline" value="file_scaleline"> <?php esc_html_e('Scaleline', 'OSM'); ?>
    <input type="checkbox" name="file_mouseposition" id="file_mouseposition" value="file_mouseposition"> <?php esc_html_e('Mouse position', 'OSM'); ?>
    <input type="checkbox" name="file_overviewmap" id="file_overviewmap" value="file_overviewmap"> <?php esc_html_e('Overview map', 'OSM'); ?>
    <input type="checkbox" name="file_bckgrnd_img" id="file_bckgrnd_img" value="file_bckgrnd_img"> <?php esc_html_e('Background image (GDPR)', 'OSM'); ?> <br/><br/>
    <input type="checkbox" name="osm_file_show_attribution" id="osm_file_show_attribution" value="osm_file_show_attribution" checked> <?php esc_html_e('Display attribution (credit) in the map.', 'OSM'); ?>
    <span style="color:red"><?php esc_html_e('Warning: If you do not check this box, it may violate the license of data or map and have legal consequences!', 'OSM'); ?></span><br/><br/>

    <b>4. <?php esc_html_e('Paste the local URL of file here:', 'OSM'); ?></b>
    <p><?php esc_html_e('Do not save any of your personal data in the plugins/osm folder but in the upload folder!', 'OSM'); ?></p>

    <?php for ($i = 0; $i < 5; $i++) : ?>
        <input type="text" class="osmFileName" name="osm_file_list_URL[<?php echo $i; ?>]" placeholder="<?php esc_attr_e('../../../../wp-content/uploads/YOUR-FILE', 'OSM'); ?>" />
        <input type="text" class="osmFileTitle" name="osm_file_list_title[<?php echo $i; ?>]" placeholder="<?php esc_attr_e('File title', 'OSM'); ?>" />
        <select class="osmFileColor" name="osm_file_list_color[<?php echo $i; ?>]" id="osm_file_list_color[<?php echo $i; ?>]">
            <?php include('osm-color-select.php'); ?>
        </select>
        <br />
    <?php endfor; ?>
    <br/><br/>

    <input type="checkbox" name="show_selection_box" id="show_selection_box" value="show_selection_box" onclick="osm_showFileSCmap()"> <?php esc_html_e('Show track selection box under the map (only for two or more tracks)', 'OSM'); ?><br/><br/>

    <b>5. <?php esc_html_e('Only if you enabled selection box you have to adjust the map manually.', 'OSM'); ?></b><br/><br/>

    <?php
    $latlon = OSM_default_lat . ',' . OSM_default_lon;
    $zoom = OSM_default_zoom;
    echo Osm::sc_OL3JS([
        'map_center' => $latlon,
        'zoom' => $zoom,
        'width' => '100%',
        'height' => '450',
        'map_div_vis' => 'none',
        'map_div_name' => 'FileSC_map'
    ]);
    ?>

    <div id="File_Div"><br/></div><br/>
    <a class="button" onClick="osm_generateFileSC();"> <?php esc_html_e('Generate shortcode for map with GPX/KML file', 'OSM'); ?> </a><br/>
</div> <!-- id="tab_file_list" -->


<!-- id="add map with geotag" -->
<div id="tab_geotag" class="osm-tab-content">

    <?php esc_html_e('Add a map with all geotagged posts / pages of your site. <br/>Set geotag to your post at [Set geotag] tab.', 'OSM'); ?><br/><br/>
    <ol>
        <li>
            <?php esc_html_e('Map type', 'OSM'); ?>
            <select name="osm_geotag_map_type" id="osm_geotag_map_type">
                <?php include('osm-maptype-select.php'); ?>
            </select>
        </li>
        <input type="checkbox" name="osm_geotag_show_attribution" id="osm_geotag_show_attribution" value="osm_geotag_show_attribution" checked> <?php esc_html_e('Display attribution (credit) in the map.', 'OSM'); ?>
        <span style="color:red"><?php esc_html_e('Warning: If you do not check this box, it may violate the license of data or map and have legal consequences!', 'OSM'); ?></span><br/><br/>
        <li>
            <?php esc_html_e('Marker icon', 'OSM'); ?><br/>
            <?php include('osm-marker-tagged-select.php'); ?><br/><br/><br/>
        </li>
        <li>
            <?php esc_html_e('Marker style', 'OSM'); ?><br/>
            <label class="metabox-label">
                <input type="radio" name="tagged_marker_style" id="tagged_marker_style" onclick="osm_showTaggedSCmap()" value="standard" checked="checked" />
                <?php echo '<img src="' . esc_url(OSM_PLUGIN_URL . '/images/marker_standard_01.png') . '" align="left" hspace="5" alt="' . esc_attr('marker_standard_01.png') . '">'; ?>
            </label>
            <label class="metabox-label">
                <input type="radio" name="tagged_marker_style" id="tagged_marker_style" onclick="osm_showTaggedSCmap()" value="cluster" />
                <?php echo '<img src="' . esc_url(OSM_PLUGIN_URL . '/images/marker_cluster_01.png') . '" align="left" hspace="5" alt="' . esc_attr('marker_cluster_01.png') . '">'; ?>
            </label>
        </li>
        <br/><br/><br/><br/><br/><br/><br/>
        <li>
            <?php esc_html_e('Post type', 'OSM'); ?>
            <select name="osm_geotag_posttype" id="osm_geotag_posttype">
                <option value="post"><?php esc_html_e('All posts', 'OSM'); ?></option>
                <option value="page"><?php esc_html_e('All pages', 'OSM'); ?></option>
                <option value="actual"><?php esc_html_e('This post / page', 'OSM'); ?></option>
            </select>
        </li>
        <li>
            <?php
            esc_html_e('Category Filter', 'OSM');
            wp_dropdown_categories([
                'hide_empty' => 0,
                'value_field' => 'name',
                'name' => 'category_parent',
                'orderby' => 'name',
                'selected' => '0',
                'hierarchical' => true,
                'show_option_none' => esc_html__('None', 'OSM')
            ]);
            esc_html_e(' OR ', 'OSM');
            ?>
            <label for="tag_filter"><?php esc_html_e('Tag Filter:', 'OSM'); ?></label>
            <input type="text" id="tag_filter" name="tag_filter">
        </li>

        <?php esc_html_e('Only if marker style is set to cluster you have to adjust the map manually.', 'OSM'); ?><br/><br/>

        <?php
        $latlon = OSM_default_lat . ',' . OSM_default_lon;
        $zoom = OSM_default_zoom;
        echo Osm::sc_OL3JS([
            'map_center' => $latlon,
            'zoom' => $zoom,
            'width' => '100%',
            'height' => '450',
            'map_div_vis' => 'none',
            'map_div_name' => 'TaggedSC_map'
        ]);
        ?>
        <li>
            <a class="button" onClick="osm_generateTaggedPostsSC()"><?php esc_html_e('Generate shortcode', 'OSM'); ?></a><br/><br/>
            <?php esc_html_e('Copy the shortcode and paste it to your post/page', 'OSM'); ?><br/>
        </li>
    </ol>
</div> <!-- id="tab_geotag" -->


<!-- class="tab_set_geotag" -->
<div id="tab_set_geotag" class="osm-tab-content">
    <?php esc_html_e('You can set a geotag (lat/lon) and an icon for this post / page.', 'OSM'); ?><br/>
    <b>1. <?php esc_html_e('Post icon', 'OSM'); ?></b>:<br/>
    <?php include('osm-marker-geotag-select.php'); ?><br/><br/><br/>
    <b>2. <?php esc_html_e('Click into the map for geotag!', 'OSM'); ?></b>:

    <?php
    $latlon = OSM_default_lat . ',' . OSM_default_lon;
    $zoom = OSM_default_zoom;
    echo Osm::sc_OL3JS([
        'map_center' => $latlon,
        'zoom' => $zoom,
        'width' => '100%',
        'height' => '450',
        'map_event' => 'SetGeotag',
        'map_div_name' => 'AddGeotag_map'
    ]);
    ?>

    <div id="Geotag_Div"><br/></div><br/>
    <a class="button" onClick="osm_saveGeotag();"> <?php esc_html_e('Save', 'OSM'); ?> </a><br/><br/>
</div> <!-- class="tab_set_geotag" -->
   
   
<!-- id="tab_troubleshooting" -->
<div id="tab_troubleshooting" class="osm-tab-content">
    <table border="0">
        <tr>
            <td><?php echo '<p><img src="' . esc_url(OSM_PLUGIN_URL . '/WP_OSM_Plugin_Logo.png') . '" align="left" vspace="10" hspace="20" alt="' . esc_attr__('Osm Logo', 'OSM') . '"></p>'; ?></td>
            <td><h3><?php echo esc_html__('WordPress OSM Plugin ', 'OSM') . esc_html(PLUGIN_VER); ?></h3></td>
        </tr>
    </table>
    <b><?php esc_html_e('Loading my GPX / KML file does not work', 'OSM'); ?></b><br>
    <?php esc_html_e('There are three GPX files provided by the WP OSM Plugin. Try to use them and see if there is a generic problem or it is caused by your personal GPX / KML file:', 'OSM'); ?><br><br>
    <?php echo esc_url(OSM_PLUGIN_URL . 'examples/sample01.gpx'); ?><br>
    <?php echo esc_url(OSM_PLUGIN_URL . 'examples/sample02.gpx'); ?><br>
    <?php echo esc_url(OSM_PLUGIN_URL . 'examples/sample03.gpx'); ?><br><br>
    
    <b><?php esc_html_e('How can I show more than one marker in a map?', 'OSM'); ?></b><br>
    <?php esc_html_e('You have to use a KML file, find a sample here:', 'OSM'); ?><br><br>
    <?php echo esc_url(OSM_PLUGIN_URL . 'examples/MarkerSample.kml'); ?><br>
</div> <!-- id="tab_troubleshooting" -->
     
<div id="tab_about" class="osm-tab-content">
    <b><?php echo esc_html__('WordPress OSM Plugin ', 'OSM') . esc_html(PLUGIN_VER) . ' '; ?></b><br/>
    <b><span style="color:#FF0000;"><?php esc_html_e('We need help for translations!', 'OSM'); ?></span></b>
    <table border="0">
        <tr>
            <td><?php echo '<p><img src="' . esc_url(OSM_PLUGIN_URL . '/WP_OSM_Plugin_Logo.png') . '" align="left" vspace="10" hspace="20" alt="' . esc_attr__('Osm Logo', 'OSM') . '"></p>'; ?></td>
            <td>
                <b><?php esc_html_e('Coordination', 'OSM'); echo ' & '; esc_html_e('Development', 'OSM'); ?>:</b>
                <a target="_blank" href="http://hyumika.com"> Michael Kang</a><br/><br/>
                <b><?php esc_html_e('Thanks for Translation to', 'OSM'); ?>:</b><br/>
                Вячеслав Стренадко, <a target="_blank" href="http://tounoki.org/">Tounoki</a>, Sykane, <a target="_blank" href="http://www.pibinko.org">Andrea Giacomelli</a>, <a target="_blank" href="http://www.cspace.ro">Sorin Pop</a>, Olle Zettergren<br/><br/>
                <b><?php esc_html_e('Thanks for Icons to', 'OSM'); ?>:</b><br/>
                <a target="_blank" href="http://rgb-labs.com">Dash</a>, <a target="_blank" href="https://github.com/mapbox/maki">Maki</a>, <a target="_blank" href="http://www.sjjb.co.uk/mapicons/">SJJB</a>, <a target="_blank" href="http://publicdomainvectors.org/en/free-clipart/">Publicdomainvectors</a>, <a target="_blank" href="https://pixabay.com/de/">Pixabay</a>, <a target="_blank" href="https://thenounproject.com/">TheNounProject</a><br/><br/>
                <?php
                $wp_url = "https://wordpress.org/support/view/plugin-reviews/osm";
                // Translators: %s is the URL to the WordPress plugin review page.
                $rate_txt = sprintf(esc_html__('If you like the OSM plugin rate it on WP %s', 'OSM'), '<a href="' . esc_url($wp_url) . '">' . esc_html__('here', 'OSM') . '</a>');
                echo $rate_txt;
                ?><br/>
                <?php esc_html_e('Thanks!', 'OSM'); ?>
            </td>
        </tr>
    </table>

    <b><?php esc_html_e('Some useful sites for this plugin:', 'OSM'); ?></b>
    <ol>
        <li><?php esc_html_e('For advanced samples visit the', 'OSM'); ?> <a target="_blank" href="http://wp-osm-plugin.hyumika.com">osm-plugin page</a>.</li>
        <li><?php esc_html_e('For questions, bugs and other feedback visit the', 'OSM'); ?> <a target="_blank" href="https://wp-osm-plugin.hyumika.com/survey/">EN | DE feedback</a></li>
        <li><?php esc_html_e('Follow us on Twitter:', 'OSM'); ?> <a target="_blank" href="https://twitter.com/wp_osm_plugin">wp-osm-plugin</a>.</li>
        <li><?php esc_html_e('Download the latest version at WordPress.org', 'OSM'); ?> <a target="_blank" href="http://wordpress.org/extend/plugins/osm/">osm-plugin download</a>.</li>
    </ol>
</div> <!-- id="tab_about" -->

</div>  <!-- class="tabs" -->
<h3><span style="color:green"><?php _e('Copy the generated shortcode/customfield/argument: ','OSM') ?></span></h3>
<div id="ShortCode_Div"><?php _e('If you click into the map this text is replaced','OSM') ?> </div> <br/>
<?php
}
