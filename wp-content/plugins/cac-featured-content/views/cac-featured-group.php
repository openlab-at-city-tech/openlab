<?php
/**
 * This file is responsible for displaying a featured group on the front end of 
 * the site. It is required by the core view template whenever the 
 * featured content type has been set to 'group'
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
    <h4><a href="<?php echo esc_url( $cfcw_view->group->permalink ) ?>"><?php esc_html_e( $cfcw_view->group->name ) ?></a></h4>

    <p><?php echo $cfcw_view->group->description ?></p>

    <?php if ( $cfcw_view->read_more ) : ?>
      <p class="more">
        Status: <?php esc_html_e( $cfcw_view->group->status ) ?> | <?php esc_html_e( $cfcw_view->group->total_member_count ) ?> Members
      </p>
    <?php endif ?>
  </div>

</div>
