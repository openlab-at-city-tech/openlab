<?php
/**
 * This file is responsible for displaying a featured blog on the front end of 
 * the site. It is required by the core view template whenever the 
 * featured content type has been set to 'blog'
 *
 * @author Dominic Giglio
 *
 */

?>

<h3><?php esc_html_e( $cfcw_view->title ) ?></h3>

<div>

  <?php
    if ( $cfcw_view->display_images )
      if ( $cfcw_view->image_url )
        echo '<img src="' . $cfcw_view->image_url . '" alt="Thumbnail" class="avatar" width="' . $cfcw_view->image_width . '" height="' . $cfcw_view->image_width . '" />';
  ?>

  <div class="cac-content">
    <h4><a href="<?php echo esc_url( $cfcw_view->resource_link ) ?>"><?php esc_html_e( $cfcw_view->resource_title ) ?></a></h4>
    <p><?php echo bp_create_excerpt( $cfcw_view->description, $cfcw_view->crop_length ) ?></p>
  </div>

</div>

