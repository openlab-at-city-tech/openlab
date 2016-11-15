<?php
/**
 * Adds OSM_Widget widget.
 */
class OSM_Tagged_Widget extends WP_Widget {

  function __construct() {
    parent::__construct(
      'osm_widget', // Base ID
       __('OSM Widget', 'OSM-plugin'), // Name
       array( 'description' => __( 'WP OSM Geotag Widget', 'OSM-plugin' ), ) // Args
    );
   }

  public function widget( $args, $instance ) {
    $title  = apply_filters( 'widget_title', $instance['title'] );
    $height = $instance['height'];
    $map_type   = $instance['map_type'];
    $ctrl_theme   = $instance['ctrl_theme'];
    $marker   = $instance['marker'];
    $zoom   = $instance['zoom'];
    $border_col   = $instance['border_col'];

    if ((OSM_isGeotagged()) && (is_singular())){ 
      echo $args['before_widget'];
      if ( ! empty( $title ) ){
        echo $args['before_title'] . $title . $args['after_title'];
      } 
      echo do_shortcode('[osm_map_v3 type="'.$map_type.'" map_center="0,0" zoom="'.$zoom.'" width="99%" height="'.$height.'"  marker_latlon="osm_geotag" map_border="2px solid '.$border_col.'" marker_name="'.$marker.'" theme="'.$ctrl_theme.'"]');
      echo $args['after_widget'];
    }
  }

  public function form( $instance ) {
    if ( isset( $instance[ 'title' ] ) ) {
      $title = $instance[ 'title' ];
    }
    else {
      $title = __( 'New title', 'OSM-plugin' );
    }
    if ( isset( $instance[ 'marker' ] ) ) {
      $marker = $instance[ 'marker' ];
    }
    else {
      $marker = 'wpttemp-red.png';
    }
    if ( isset( $instance[ 'border_col' ] ) ) {
      $border_col = $instance[ 'border_col' ];
    }
    else {
      $border_col = 'none';
    }
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>

    <p>
    <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height of map:', 'OSM-plugin'); ?></label>
    <input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" style="width:100%;" />
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'zoom' ); ?>"><?php _e('Zoom', 'OSM-plugin'); ?></label> 
    <select id="<?php echo $this->get_field_id( 'zoom' ); ?>" name="<?php echo $this->get_field_name( 'zoom' ); ?>" class="widefat" style="width:100%;">
      <option <?php if ( '1' == $instance['zoom'] ) echo 'selected="selected"'; ?>>1</option>
      <option <?php if ( '2' == $instance['zoom'] ) echo 'selected="selected"'; ?>>2</option>
      <option <?php if ( '3' == $instance['zoom'] ) echo 'selected="selected"'; ?>>3</option>
      <option <?php if ( '4' == $instance['zoom'] ) echo 'selected="selected"'; ?>>4</option>
      <option <?php if ( '5' == $instance['zoom'] ) echo 'selected="selected"'; ?>>5</option>
      <option <?php if ( '6' == $instance['zoom'] ) echo 'selected="selected"'; ?>>6</option>
      <option <?php if ( '7' == $instance['zoom'] ) echo 'selected="selected"'; ?>>7</option>
      <option <?php if ( '8' == $instance['zoom'] ) echo 'selected="selected"'; ?>>8</option>
      <option <?php if ( '9' == $instance['zoom'] ) echo 'selected="selected"'; ?>>9</option>
      <option <?php if ( '10' == $instance['zoom'] ) echo 'selected="selected"'; ?>>10</option>
      <option <?php if ( '11' == $instance['zoom'] ) echo 'selected="selected"'; ?>>11</option>
      <option <?php if ( '12' == $instance['zoom'] ) echo 'selected="selected"'; ?>>12</option>
      <option <?php if ( '13' == $instance['zoom'] ) echo 'selected="selected"'; ?>>13</option>
      <option <?php if ( '14' == $instance['zoom'] ) echo 'selected="selected"'; ?>>14</option>
      <option <?php if ( '15' == $instance['zoom'] ) echo 'selected="selected"'; ?>>15</option>
      <option <?php if ( '16' == $instance['zoom'] ) echo 'selected="selected"'; ?>>16</option>
      <option <?php if ( '17' == $instance['zoom'] ) echo 'selected="selected"'; ?>>17</option>
      <option <?php if ( '18' == $instance['zoom'] ) echo 'selected="selected"'; ?>>18</option>
    </select>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'ctrl_theme' ); ?>"><?php _e('Control Theme', 'OSM-plugin'); ?></label> 
    <select id="<?php echo $this->get_field_id( 'ctrl_theme' ); ?>" name="<?php echo $this->get_field_name( 'ctrl_theme' ); ?>" class="widefat" style="width:100%;">
        <option <?php selected( $instance['ctrl_theme'], 'dark'); ?> value="dark"><?php _e('dark','OSM-plugin') ?></option>
        <option <?php selected( $instance['ctrl_theme'], 'ol_orange'); ?> value="ol_orange"><?php _e('orange','OSM-plugin') ?></option>
        <option <?php selected( $instance['ctrl_theme'], 'ol'); ?> value="ol"><?php _e('blue','OSM-plugin') ?></option>

  <!--<option <?php if ( 'blue' == $instance['ctrl_theme'] ) echo 'selected="selected"'; ?>>blue</option>
      <option <?php if ( 'orange' == $instance['ctrl_theme'] ) echo 'selected="selected"'; ?>>orange</option>
      <option <?php if ( 'dark' == $instance['ctrl_theme'] ) echo 'selected="selected"'; ?>>dark</option> --->
    </select>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'marker' ); ?>"><?php _e('marker', 'OSM-plugin'); ?></label> 
    <select id="<?php echo $this->get_field_id( 'marker' ); ?>" name="<?php echo $this->get_field_name( 'marker' ); ?>" class="widefat" style="width:100%;">
        <option <?php selected( $instance['marker'], 'wpttemp-green.png'); ?> value="wpttemp-green.png"><?php _e('Waypoint','OSM-plugin');echo ' ';_e('green','OSM-plugin') ?></option> 
        <option <?php selected( $instance['marker'], 'wpttemp-red.png'); ?> value="wpttemp-red.png"><?php _e('Waypoint','OSM-plugin');echo ' ';_e('red','OSM-plugin') ?></option> 
        <option <?php selected( $instance['marker'], 'wpttemp-yellow.png'); ?> value="wpttemp-yellow.png"><?php _e('Waypoint','OSM-plugin');echo ' ';_e('yellow','OSM-plugin') ?></option> 
        <option <?php selected( $instance['marker'], 'mic_photo_icon.png'); ?> value="mic_photo_icon.png"><?php _e('Camera','OSM-plugin');echo ' ';_e('black','OSM-plugin') ?></option> 
        <option <?php selected( $instance['marker'], 'mic_blue_bridge_old_01.png'); ?> value="mic_blue_bridge_old_01.png"><?php _e('Bridge','OSM-plugin');echo ' ';_e('blue','OSM-plugin') ?></option> 
        <option <?php selected( $instance['marker'], 'mic_orange_hiking_01.png'); ?> value="mic_orange_hiking_01.png"><?php _e('Hiking','OSM-plugin');echo ' ';_e('orange','OSM-plugin') ?></option> 
    </select>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'border_col' ); ?>"><?php _e('border', 'OSM-plugin'); ?></label> 
    <select id="<?php echo $this->get_field_id( 'border_col' ); ?>" name="<?php echo $this->get_field_name( 'border_col' ); ?>" class="widefat" style="width:100%;">
      <option <?php selected( $instance['border_col'], 'none'); ?> value="no"><?php _e('none','OSM-plugin') ?></option> 
      <option <?php selected( $instance['border_col'], 'green'); ?> value="green"><?php _e('green','OSM-plugin') ?></option> 
      <option <?php selected( $instance['border_col'], 'red'); ?> value="red"><?php _e('red','OSM-plugin') ?></option> 
      <option <?php selected( $instance['border_col'], 'blue'); ?> value="blue"><?php _e('blue','OSM-plugin') ?></option> 
      <option <?php selected( $instance['border_col'], 'orange'); ?> value="orange"><?php _e('orange','OSM-plugin') ?></option>
      <option <?php selected( $instance['border_col'], 'black'); ?> value="black"><?php _e('black','OSM-plugin') ?></option>
      <option <?php selected( $instance['border_col'], 'grey'); ?> value="grey"><?php _e('grey','OSM-plugin') ?></option>    
    </select>
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'map_type' ); ?>"><?php _e('Map type', 'OSM-plugin'); ?></label> 
    <select id="<?php echo $this->get_field_id( 'map_type' ); ?>" name="<?php echo $this->get_field_name( 'map_type' ); ?>" class="widefat" style="width:100%;">
      <option <?php selected( $instance['map_type'], 'Mapnik'); ?> value="Mapnik"><?php _e('OpenStreetMap','OSM-plugin') ?></option> 
      <option <?php selected( $instance['map_type'], 'CycleMap'); ?> value="CycleMap"><?php _e('CycleMap','OSM-plugin') ?></option> 
      <option <?php selected( $instance['map_type'], 'OpenSeaMap'); ?> value="OpenSeaMap"><?php _e('OpenSeaMap','OSM-plugin') ?></option> 
      <option <?php selected( $instance['map_type'], 'basemap_at'); ?> value="basemap_at"><?php _e('BaseMap','OSM-plugin') ?></option> 
      <option <?php selected( $instance['map_type'], 'stamen_watercolor'); ?> value="stamen_watercolor"><?php _e('Stamen Watercolor','OSM-plugin') ?></option> 
      <option <?php selected( $instance['map_type'], 'stamen_toner'); ?> value="stamen_toner"><?php _e('Stamen Toner','OSM-plugin') ?></option>
    </select>
    </p>
    <?php 

  }

  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['height'] = strip_tags( $new_instance['height'] );
    $instance['marker'] = strip_tags( $new_instance['marker'] );
    $instance['zoom'] = $new_instance['zoom'];
    $instance['map_type'] = $new_instance['map_type'];
    $instance['ctrl_theme'] = $new_instance['ctrl_theme'];
    $instance['border_col'] = strip_tags( $new_instance['border_col'] );
    return $instance;
  }

} // class OSM_Widget
