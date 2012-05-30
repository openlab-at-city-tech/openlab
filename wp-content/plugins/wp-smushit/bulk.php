<div class="wrap"> 
<div id="icon-upload" class="icon32"><br /></div><h2>Bulk WP Smush.it </h2>

<?php 

  if ( sizeof($attachments) < 1 ):
    echo '<p>You don’t appear to have uploaded any images yet.</p>';
  else: 
    if ( empty($_POST) && !$auto_start ): // instructions page
?>
  <p>This tool will run all of the images in your media library through the WP Smush.it web service.  It won't re-smush images that were successfully smushed before. It will retry images that were not successfully smushed.</p>
  
  <p>It uploads each and every file to Yahoo! and then downloads the resulting file. It can take a long time.</p>
  
  <p>We found <?php echo sizeof($attachments); ?> images in your media library. Be forewarned, <strong>it will take <em>at least</em> <?php echo (sizeof($attachments) * 3 / 60); ?> minutes</strong> to process all these images if they have never been smushed before.</p>
  
  <p><em>N.B. If your server <tt>gzip</tt>s content you may not see the progress updates as your files are processed.</em></p>
  
  <p><strong>This is an experimental feature.</strong> Please post any feedback to the <a href="http://wordpress.org/tags/wp-smushit">WordPress WP Smush.it forums</a>.</p>

  <form method="post" action="">
    <?php wp_nonce_field( 'wp-smushit-bulk', '_wpnonce'); ?>
    <button type="submit" class="button-secondary action">Run all my images through WP Smush.it right now</button>
  </form>
  
<?php
      else: // run the script
  
      if (!wp_verify_nonce( $_REQUEST['_wpnonce'], 'wp-smushit-bulk' ) || !current_user_can( 'edit_others_posts' ) ) {
  				wp_die( __( 'Cheatin&#8217; uh?' ) );
      }


      ob_implicit_flush(true);
      ob_end_flush();
      foreach( $attachments as $attachment ) {
        printf( "<p>Processing <strong>%s</strong>&hellip;<br>", esc_html($attachment->post_name) );
        $original_meta = wp_get_attachment_metadata( $attachment->ID, true );
            
        $meta = wp_smushit_resize_from_meta_data( $original_meta, $attachment->ID, false );

        printf( "– %dx%d: ", intval($meta['width']), intval($meta['height']) );
        
        if ( $original_meta['wp_smushit'] == $meta['wp_smushit'] && stripos( $meta['wp_smushit'], 'Smush.it error' ) === false ) {
          echo 'already smushed' . $meta['wp_smushit'];
        } else {
          echo $meta['wp_smushit'];
        }
        echo '<br>';



        if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
          foreach( $meta['sizes'] as $size_name => $size  ) {
            printf( "– %dx%d: ", intval($size['width']), intval($size['height']) );
            if ( $original_meta['sizes'][$size_name]['wp_smushit'] == $size['wp_smushit'] && stripos( $meta['sizes'][$size_name]['wp_smushit'], 'Smush.it error' ) === false ) {
              echo 'already smushed';
            } else {
              echo $size['wp_smushit'];
            }
            echo '<br>';
            
          }
        }
        echo "</p>";
        
        wp_update_attachment_metadata( $attachment->ID, $meta );

        // rate limiting is good manners, let's be nice to Yahoo!
        sleep(0.5); 
        @ob_flush();
        flush();
      }
    endif; 
  endif; 
?>
</div>