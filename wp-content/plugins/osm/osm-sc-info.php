<?php
/*  (c) Copyright 2014  Michael Kang (wp-osm-plugin.HanBlog.Net)

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

     echo '<p><img src="'.OSM_PLUGIN_URL.'/WP_OSM_Plugin_Logo.png" alt="Osm Logo"></p>';
     echo '<h2>OpenStreetMap Plugin '.PLUGIN_VER.' </h2>';
     global $post;
     $post_org = $post;
     $Counter = 0;
     $CustomFieldName = get_option('osm_custom_field','OSM_geo_data');        
     $recentPosts = new WP_Query();
     $starttime = microtime(true);
     $recentPosts->query('meta_key='.$CustomFieldName.'&post_status=publish &showposts=-1 &post_type=post');
     while ($recentPosts->have_posts()) : $recentPosts->the_post();
       list($temp_lat, $temp_lon) = explode(',', get_post_meta($post->ID, $CustomFieldName, true)); 
       if ($temp_lat != '' && $temp_lon != '') {
         list($temp_lat, $temp_lon) = $this->checkLatLongRange('$marker_all_posts',$temp_lat, $temp_lon);
         $Counter += 1;
       }
 
     endwhile;
     $diff = microtime(true) - $starttime;
     $sec = intval($diff);
     $micro = $diff - $sec;
     $final = strftime('%T', mktime(0, 0, $sec)) . str_replace('0.', '.', sprintf('%.3f', $micro));
     $Counter = sprintf("%03d",$Counter);

     $count_posts = wp_count_posts('post');
     $published_posts = $count_posts->publish;
     echo "published posts: ".$published_posts."<br>";
     echo "   geo tagged:".$Counter."; DB request took: ".$final."<br>";
     $post = $post_org;

     $post_org = $post;
     $Counter = 0;
     $starttime = microtime(true);      
     $recentPosts = new WP_Query();
     $recentPosts->query('meta_key='.$CustomFieldName.'&post_status=publish'.'&showposts=-1'.'&post_type=page');
     while ($recentPosts->have_posts()) : $recentPosts->the_post();
       list($temp_lat, $temp_lon) = explode(',', get_post_meta($post->ID, $CustomFieldName, true)); 
       if ($temp_lat != '' && $temp_lon != '') {
         list($temp_lat, $temp_lon) = $this->checkLatLongRange('$marker_all_posts',$temp_lat, $temp_lon);
         $Counter += 1;
       }
 
     endwhile;
     $diff = microtime(true) - $starttime;
     $sec = intval($diff);
     $micro = $diff - $sec;
     $final = strftime('%T', mktime(0, 0, $sec)) . str_replace('0.', '.', sprintf('%.3f', $micro));
     $Counter = sprintf("%03d",$Counter);
     $count_pages = wp_count_posts('page');
     $published_pages = $count_pages->publish;
     echo "published pages: ".$published_pages."<br>";
     echo "   geo tagged:".$Counter."; DB request took: ".$final."<br>";
     $post = $post_org;
?>
