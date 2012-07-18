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
    if ( $cfcw_view->display_images ) {
      if ( $cfcw_view->image_url )
        echo $cfcw_view->image_url;
      else
        echo $cfcw_view->avatar;
    }
    
  ?>

  <div class="cac-content">
    <h4><a href="<?php echo esc_url( $cfcw_view->blog->siteurl ) ?>"><?php esc_html_e( $cfcw_view->blog->blogname ) ?></a></h4>

    <p><?php echo $cfcw_view->blog->description ?></p>

    <?php if ( $cfcw_view->read_more ) : ?>
      <p class="more">
        <a href="<?php echo esc_url( $cfcw_view->blog->siteurl ) ?>"><?php esc_html_e( $cfcw_view->read_more ) ?></a>
      </p>
    <?php endif ?>
  </div>

</div>

