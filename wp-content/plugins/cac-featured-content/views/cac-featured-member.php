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

<?php // echo out the widget title using the element selected in the widget admin
  echo "<{$cfcw_view->title_element} class='widgettitle'>";
  esc_html_e( $cfcw_view->title );
  echo "</{$cfcw_view->title_element}>";
?>

<div class="cfcw-content">
  <?php
    if ( $cfcw_view->display_images ) {
      if ( $cfcw_view->image_url )
        echo $cfcw_view->image_url;
      else
        echo $cfcw_view->avatar;
    }
  ?>
  
  <p class="cfcw-title"><?php echo $cfcw_view->member->user_link ?></p>
  
  <p class="item-meta"><span class="activity"><?php esc_html_e( $cfcw_view->member->last_activity ) ?></span></p>

  <?php if ( $cfcw_view->read_more ) : ?>
    <p class="more">
      <?php echo $cfcw_view->member->user_link ?>
    </p>
  <?php endif ?>
</div>
