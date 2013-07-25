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

<?php // echo out the widget title using the element selected in the widget admin
  echo "<{$cfcw_view->title_element} class='widgettitle'>";
  esc_html_e( $cfcw_view->title );
  echo "</{$cfcw_view->title_element}>";
?>

<div class="cfcw-content">
  <?php
    if ( $cfcw_view->display_images )
      if ( $cfcw_view->image_url )
        echo '<img src="' . $cfcw_view->image_url . '" alt="Thumbnail" class="avatar" width="' . $cfcw_view->image_width . '" height="' . $cfcw_view->image_width . '" />';
  ?>

  <p class="cfcw-title">
    <a href="<?php echo esc_url( $cfcw_view->resource_link ) ?>"><?php esc_html_e( $cfcw_view->resource_title ) ?></a>
  </p>
  
  <p><?php echo bp_create_excerpt( $cfcw_view->description, $cfcw_view->crop_length ) ?></p>
</div>
