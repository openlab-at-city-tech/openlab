<?php

class FV_Player_Facebook_Share {

  public function __construct() {

    add_action('wp_head', array($this, 'fb_share_tags'));
    add_action('fv_flowplayer_admin_integration_options_after', array($this, 'setting'), 0);
  }

  function fb_share_tags() {
    global $fv_fp, $post, $FV_Player_Pro;
    if (!isset($fv_fp->conf['integrations']) || !isset($fv_fp->conf['integrations']['facebook_sharing']) || $fv_fp->conf['integrations']['facebook_sharing'] !== 'true' || !is_singular())
      return;

    $content = $post->post_content;

    $matches = array();
    if (!preg_match_all("/\[fvplayer[^]]*/", $content, $aMatches))
      return;

    foreach( $aMatches[0] AS $aMatch ) {
      $aAtts = shortcode_parse_atts($aMatch . ' ]');
      if( !isset($aAtts['src']) || !$aAtts['src'] ) continue;
      
      $sUrl = $aAtts['src'];
      if (empty($sUrl) || strpos($sUrl, '.mp4') === false || $fv_fp->is_secure_amazon_s3($sUrl) || isset($FV_Player_Pro) && method_exists($FV_Player_Pro, 'is_dynamic_item') && $FV_Player_Pro->is_dynamic_item($sUrl))
        continue;
  
      $sUrl = preg_replace('/https?:\/\/?/', '', $sUrl);
      $httpUrl = 'http://' . $sUrl;
      $httpsUrl = 'https://' . $sUrl;
  
      $sName = get_bloginfo('name');
      $sTitle = get_the_title($post);      
      $sSplash = isset($aAtts['splash']) ? $aAtts['splash'] : false;
      if( !$sSplash ) {
        $aFeaturedImage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
        if( isset($aFeaturedImage[0]) ) {
          $sSplash = $aFeaturedImage[0];
        }
      }
      
      if( !$sSplash ) continue; 
      
      $sDescription = htmlspecialchars(strip_tags( wp_trim_words(strip_shortcodes(strip_tags($post->post_content)), 20, ' &hellip;') ) );      

      ?>
      <meta property="og:site_name" content="<?php echo esc_attr($sName); ?>">
      <meta property="og:url" content="<?php echo get_permalink($post); ?>" />
      <meta property="og:title" content="<?php echo esc_attr($sTitle); ?>">
      <?php if( $sSplash ) : ?>
        <meta property="og:image" content="<?php echo esc_attr($sSplash); ?>">
      <?php endif; ?>
      <meta property="og:description" content="<?php echo esc_attr($sDescription); ?>">
      <meta property="og:type" content="video">  
      <meta property="og:video:url" content="<?php echo esc_attr($httpUrl); ?>">
      <meta property="og:video:secure_url" content="<?php echo esc_attr($httpsUrl); ?>">
      <meta property="og:video:type" content="video/mp4">
      <meta property="og:video:width" content="640">
      <meta property="og:video:height" content="360">
      <?php
      break;
    }
  }

  function setting() {
    global $fv_fp;
    ?>
    <tr>
      <td><label for="facebook_sharing">Facebook Video Sharing:</label></td>
      <td>
        <p class="description">
          <input type="hidden" name="integrations[facebook_sharing]" value="false" />
          <input type="checkbox" name="integrations[facebook_sharing]" id="facebook_sharing" value="true" <?php if (isset($fv_fp->conf['integrations']['facebook_sharing']) && $fv_fp->conf['integrations']['facebook_sharing'] == 'true') echo 'checked="checked"'; ?> />
          <?php _e('When sharing your post to Facebook the first MP4 video will be shared directly rather than the post excerpt.', 'fv-wordpress-flowplayer'); ?>
          <span class="more"><?php _e('<strong>Requirements</strong>: video has to be on https:// and splash screen has to be present. Videos with download protection are automatically excluded.', 'fv-wordpress-flowplayer'); ?></span> <a href="#" class="show-more">(&hellip;)</a>
        </p>
      </td>
    </tr>
    <?php
  }

}

$FV_Player_Facebook_Share = new FV_Player_Facebook_Share();
