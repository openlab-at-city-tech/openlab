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
  
  <p class="cfcw-title">
    <a href="<?php echo esc_url( $cfcw_view->post->guid ) ?>"><?php esc_html_e( $cfcw_view->post->post_title ) ?></a>
  </p>
  
  <p class="cfcw-description"><?php echo $cfcw_view->post->description ?></p>
  
  <?php if ( $cfcw_view->read_more ) : ?>
    <p class="more">
      <a href="<?php echo esc_url( $cfcw_view->post->guid ) ?>"><?php esc_html_e( $cfcw_view->read_more ) ?></a>
    </p>
  <?php endif ?>
</div>
