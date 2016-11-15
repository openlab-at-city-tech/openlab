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
<h3><?php _e('NOTE: There is a WP OSM Plugin shortcode generator when you create a post / page','OSM-plugin') ?></h3>

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
 <tr> <h3>  PHP Interface setting:</h3> </tr>
 <tr>
  <td><label for="osm_zoom_level"><?php _e('Map Zoomlevel for the PHP Link (1-17)','OSM-plugin') ?>:</label></td>
  <td><input type="text" name="osm_zoom_level" value="<?php echo $osm_zoom_level ?>" /></td>
 </tr>
</table>
<div class="submit"><input type="submit" name="Options" value="<?php _e('Update Options','OSM-plugin') ?> &raquo;" /></div>
</div>
</form>
