<?php
/**
 * This file is responsible for displaying a featured member on the front end of 
 * the site. It is required by the core view template whenever the 
 * featured content type has been set to 'member'
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
    <h4><?php echo $cfcw_view->member->user_link ?></h4>
    <p class="item-meta"><span class="activity"><?php esc_html_e( $cfcw_view->member->last_activity ) ?></span></p>

    <?php if ( $cfcw_view->read_more ) : ?>
      <p class="more">
        <?php echo $cfcw_view->member->user_link ?>
      </p>
    <?php endif ?>
  </div>
</div>
