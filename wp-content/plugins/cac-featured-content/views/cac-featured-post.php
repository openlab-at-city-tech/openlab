<?php
/**
 * This file is responsible for displaying a featured post on the front end of 
 * the site. It is required by the core view template whenever the 
 * featured content type has been set to 'post'
 *
 * @author Dominic Giglio
 *
 */

?>

<h3><?php esc_html_e( $cfcw_view->title ) ?></h3>

<div>

  <?php
    if ( $cfcw_view->display_images ) {
      if ( $cfcw_view->image_url )
        echo $cfcw_view->image_url;
      else
        echo $cfcw_view->avatar;
    }
  ?>
  
  <div class="cac-content">
    <h4><a href="<?php echo esc_url( $cfcw_view->post->guid ) ?>"><?php esc_html_e( $cfcw_view->post->post_title ) ?></a></h4>
    
    <p><?php echo $cfcw_view->post->description ?></p>
    
    <?php if ( $cfcw_view->read_more ) : ?>
      <p class="more">
        <a href="<?php echo esc_url( $cfcw_view->post->guid ) ?>"><?php esc_html_e( $cfcw_view->read_more ) ?></a>
      </p>
    <?php endif ?>
  </div>

</div>
