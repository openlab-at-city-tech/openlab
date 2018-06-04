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
<h3><?php _e('NOTE: There is a WP OSM Plugin shortcode generator when you create a post / page','OSM') ?></h3>

<h3><?php _e('Some usefull sites for this plugin:','OSM') ?></h3>
<ol>
  <li><?php _e('for advanced samples visit the ','OSM') ?><a target="_new" href="http://wp-osm-plugin.HanBlog.net">osm-plugin page</a>.</li>
  <li><?php _e('for questions, bugs and other feedback visit the','OSM') ?> <a target="_new" href="http://wp-osm-plugin.hanblog.net/forum/forum-en/">EN forum</a>, <a target="_new" href="http://wp-osm-plugin.hanblog.net/forum/forum-de/">DE forum</a></li>
  <li><?php _e('Follow us on twitter: ','OSM') ?><a target="_new" href="https://twitter.com/wp_osm_plugin">wp-osm-plugin</a>.</li>
  <li><?php _e('download the last version at WordPress.org ','OSM') ?><a target="_new" href="http://wordpress.org/extend/plugins/osm/">osm-plugin download</a>.</li>
</ol>
<h3><?php _e('If you want to express thanks for this plugin ...','OSM') ?></h3>
<ol>
  <li><?php _e('give this plugin a good ranking at ','OSM') ?><a target="_new" href="http://wordpress.org/support/view/plugin-reviews/osm">WordPress.org</a>.</li>
  <li><?php _e('post an article about ','OSM') ?><a target="_new" href="http://www.OpenStreetMap.org">OpenStreetMap</a><?php _e(' on your blog.','OSM') ?></li>
</ol>
<form method="post">   
<table>
 <tr> <h3> Shortcodegenerator Settings:</h3> </tr>
 <tr>
  <td><label for="osm_zoom_level"><?php _e('Default Latitude for maps','OSM') ?>:</label></td>
  <td><input type="text" name="osm_default_lat" value="<?php echo esc_attr($osm_default_lat) ?>" /></td>
 </tr>
 <tr>
  <td><label for="osm_zoom_level"><?php _e('Default Longitude for maps','OSM') ?>:</label></td>
  <td><input type="text" name="osm_default_lon" value="<?php echo esc_attr($osm_default_lon) ?>" /></td>
 </tr>
 <tr>
  <td><label for="osm_zoom_level"><?php _e('Default Zoom Level for maps','OSM') ?>:</label></td>
  <td><input type="text" name="osm_default_zoom" value="<?php echo esc_attr($osm_default_zoom) ?>" /></td>
 </tr>
</table>
<div class="submit"><input type="submit" name="Options" value="<?php _e('Update Options','OSM') ?> &raquo;" /></div>
</form>
