<?php

class FV_Player_lightbox {

  private $lightboxHtml;
  
  public $bCSSLoaded = false;
  
  public $bLoad = false;
  
  public function __construct() {
    add_action('init', array($this, 'remove_pro_hooks'), 10);

    add_filter('fv_flowplayer_shortcode', array($this, 'shortcode'), 15, 3);

    add_filter('fv_flowplayer_player_type', array($this, 'lightbox_enable'));

    add_filter('fv_flowplayer_playlist_style', array($this, 'lightbox_playlist'), 10, 5);

    add_filter('fv_flowplayer_args', array($this, 'disable_autoplay')); // disable autoplay for lightboxed videos, todo: it should work instead!
    
    add_filter('fv_flowplayer_args', array($this, 'parse_html_caption'), 0);

    add_filter('the_content', array($this, 'html_to_lightbox_videos'));
    add_filter('the_content', array($this, 'html_lightbox_images'), 999);  //  moved after the shortcodes are parsed to work for galleries

    add_action('fv_flowplayer_shortcode_editor_tab_options', array($this, 'shortcode_editor'), 8);

    add_action('fv_flowplayer_admin_default_options_after', array( $this, 'lightbox_admin_default_options_html' ) );
    add_filter('fv_flowplayer_admin_interface_options_after', array( $this, 'lightbox_admin_interface_html' ) );
    add_filter('fv_flowplayer_admin_integration_options_after', array( $this, 'lightbox_admin_integrations_html' ) );
    
    add_action( 'wp_footer', array( $this, 'disp__lightboxed_players' ), 0 );

    add_filter('fv_player_conf_defaults', array( $this, 'conf_defaults' ) );
    
    add_action('wp_head', array( $this, 'remove_other_fancybox' ), 8 );
    add_action('wp_footer', array( $this, 'remove_other_fancybox' ), 19 );

    //TODO is this hack needed?
    $conf = get_option('fvwpflowplayer');
    if(isset($conf['lightbox_images']) && $conf['lightbox_images'] == 'true' && 
      (!isset($conf['lightbox_improve_galleries']) || isset($conf['lightbox_improve_galleries']) && $conf['lightbox_improve_galleries'] == 'true')) {
      add_filter( 'shortcode_atts_gallery', array( $this, 'improve_galleries' ) );
    }
    
    add_action( 'wp_enqueue_scripts', array( $this, 'css_enqueue' ), 999 );
  }
  
  function css_enqueue( $force ) {
    global $fv_fp;
    if( !$force && !$fv_fp->_get_option('lightbox_images') ) return;
    
    $bCSSLoaded = true;
    
    global $fv_wp_flowplayer_ver;
    wp_enqueue_style( 'fv_player_lightbox', FV_FP_RELATIVE_PATH . '/css/fancybox.css', array(), $fv_wp_flowplayer_ver );
  }

  function conf_defaults($conf){
    //TODO probbably not needed in the future
    if(isset($conf['lightbox_images']) && $conf['lightbox_images'] && !isset($conf['lightbox_improve_galleries']) )$conf['lightbox_improve_galleries'] = false;

    $conf += array(
      'lightbox_images' => false,
      'lightbox_improve_galleries' => false
    );

    return $conf;
  }

  function remove_pro_hooks() {
    global $FV_Player_Pro;
    
    if (isset($FV_Player_Pro)) {
      //remove_filter('fv_flowplayer_shortcode', array($FV_Player_Pro, 'shortcode'));
      remove_filter('fv_flowplayer_html', array($FV_Player_Pro, 'lightbox_html'), 11 );
      remove_filter('fv_flowplayer_playlist_style', array($FV_Player_Pro, 'lightbox_playlist'), 10);
      remove_filter('fv_flowplayer_args', array($FV_Player_Pro, 'disable_autoplay')); // disable autoplay for lightboxed videos, todo: it should work instead!
      remove_filter('the_content', array($FV_Player_Pro, 'lightbox_add'));
      remove_filter('the_content', array($FV_Player_Pro, 'lightbox_add_post'), 999 );  //  moved after the shortcodes are parsed to work for galleries

    }
  }

  function improve_galleries( $args ) {
    if( !$args['link'] ) {
      $args['link'] = 'file';
    }
    return $args;
  }

  function lightbox_enable($sType) {

    if ($sType === 'video') {
      add_filter('fv_flowplayer_html', array($this, 'lightbox_html'), 11, 2);
    } else {
      remove_filter('fv_flowplayer_html', array($this, 'lightbox_html'), 11);
    }

    return $sType;
  }
  
  function remove_other_fancybox() {
    global $fv_fp;
    if( $fv_fp->_get_option('lightbox_force') ) {
      global $wp_scripts;
      if( isset($wp_scripts) && isset($wp_scripts->queue) && is_array($wp_scripts->queue) ) {
        foreach( $wp_scripts->queue as $handle ) {
          if( stripos($handle,'fancybox') !== false ) {
            wp_dequeue_script($handle);
          }
        }
      }
    }    
  }  

  function shortcode($attrs) {
    $aArgs = func_get_args();

    if (isset($aArgs[2]) && isset($aArgs[2]['lightbox'])) {
      $attrs['lightbox'] = $aArgs[2]['lightbox'];
    }

    return $attrs;
  }

  function disp__lightboxed_players() {
    if (strlen($this->lightboxHtml)) {
      echo $this->lightboxHtml . "<!-- lightboxed players -->\n\n";
    }
  }
  
  function get_title_attr( $args ) {    
    return !empty($args['caption']) ? " title='" . esc_attr($args['caption']) . "'" : '';
  }

  function is_text_lightbox($aArgs) {
    $aLightbox = preg_split('~[;]~', $aArgs['lightbox']);
    
    foreach ($aLightbox AS $k => $i) {
      if ($i == 'text') {
        return true;
      }
    }
    return false;
  }

  function lightbox_html($html) {
    $aArgs = func_get_args();

    if (isset($aArgs[1]) && isset($aArgs[1]->aCurArgs['lightbox'])) {
      $this->bLoad = true;
      
      global $fv_fp;

      $iConfWidth = intval($fv_fp->_get_option('width'));
      $iConfHeight = intval($fv_fp->_get_option('height'));

      $iPlayerWidth = ( isset($aArgs[1]->aCurArgs['width']) && intval($aArgs[1]->aCurArgs['width']) > 0 ) ? intval($aArgs[1]->aCurArgs['width']) : $iConfWidth;
      $iPlayerHeight = ( isset($aArgs[1]->aCurArgs['height']) && intval($aArgs[1]->aCurArgs['height']) > 0 ) ? intval($aArgs[1]->aCurArgs['height']) : $iConfHeight;
      
      $aLightbox = preg_split('~[;]~', $aArgs[1]->aCurArgs['lightbox']);
      
      if ( $this->is_text_lightbox($aArgs[1]->aCurArgs) ) {
        $sTitle = empty($aArgs[1]->aCurArgs['playlist']) ? $sTitle = $this->get_title_attr($aArgs[1]->aCurArgs) : '';
        $html = str_replace(array('class="flowplayer ', "class='flowplayer "), array('class="flowplayer lightboxed ', "class='flowplayer lightboxed "), $html);
        $this->lightboxHtml .= "<div style='display: none'>\n" . $html . "</div>\n";
        $html = "<a".$this->fancybox_opts()." id='fv_flowplayer_" . $aArgs[1]->hash . "_lightbox_starter'" . $sTitle . " class='fv-player-lightbox-link' href=\"#\" data-src='#wpfp_" . $aArgs[1]->hash . "'>" . $aArgs[1]->aCurArgs['caption'] . "</a>";
        
      } else {
        $iWidth = ( isset($aLightbox[1]) && intval($aLightbox[1]) > 0 ) ? intval($aLightbox[1]) : ( ($iPlayerWidth > $iConfWidth) ? $iPlayerWidth : $iConfWidth );
        $iHeight = ( isset($aLightbox[2]) && intval($aLightbox[2]) > 0 ) ? intval($aLightbox[2]) : ( ($iPlayerHeight > $iConfHeight) ? $iPlayerHeight : $iConfHeight );

        $sSplash = apply_filters('fv_flowplayer_playlist_splash', $aArgs[1]->aCurArgs['splash'], $fv_fp);
        $sStyle = 'style="max-width: ' . $iWidth . 'px; max-height: ' . $iHeight . 'px; ';
        if (isset($aArgs[1]->aCurArgs['splash']) && $sSplash ) {
          $sStyle .= 'background-image: url(\'' . $sSplash . '\')';
        }
        $sStyle .= '"';

        if ($iWidth > 0) {
          $sStyle .= ' data-ratio="' . round($iHeight / $iWidth, 4) . '"';
        }

        $sClass = "";
        if (is_object($aArgs[1]) && method_exists($aArgs[1], 'get_align')) {
          $sClass = $aArgs[1]->get_align();
        }
        
        if( !empty($fv_fp->aCurArgs['skin']) ) {
          $skin = 'skin-'.$fv_fp->aCurArgs['skin'];
        } else {
          $skin = 'skin-'.$fv_fp->_get_option('skin');
        }
        $sClass .= ' no-svg is-paused '.$skin;
        $sClass .= ' '.$fv_fp->_get_option(array($skin, 'design-timeline')).' '.$fv_fp->_get_option(array($skin, 'design-icons'));

        $sTitle = '';
        if (isset($aLightbox[3])) {
          $sTitle = "title='" . esc_attr($aLightbox[3]) . "'";
        } else if (isset($aLightbox[1]) && !isset($aLightbox[2]) && !isset($aLightbox[3])) {
          $sTitle = "title='" . esc_attr($aLightbox[1]) . "'";
        }

        $lightboxed_player = str_replace(array('class="flowplayer ', "class='flowplayer "), array('class="flowplayer lightboxed ', "class='flowplayer lightboxed "), $html);
        /* $html = preg_replace( '~max-width: \d+px;~', 'max-width: '.$iWidth.'px;', $html );
          $html = preg_replace( '~max-height: \d+px;~', 'max-height: '.$iHeight.'px;', $html ); */

        $html = "<div".$this->fancybox_opts($sSplash)." id='fv_flowplayer_" . $aArgs[1]->hash . "_lightbox_starter' $sTitle href='#wpfp_" . $aArgs[1]->hash . "' class='flowplayer lightbox-starter is-splash$sClass' $sStyle>";
        
        $html .= '<div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>';
        
        if ($iWidth > 0) {
          $html .= '<div class="fp-ratio" style="padding-top: '.str_replace(',','.',round($iHeight / $iWidth, 4) * 100).'%"></div>';
        }
        $html .= "</div>\n<div class='fv_player_lightbox_hidden' style='display: none'>\n" . $lightboxed_player . "</div>";
      }
    }
    return $html;
  }

  function lightbox_playlist($output, $aCurArgs, $aPlaylistItems, $aSplashScreens, $aCaptions) {    

    if ($output || empty($aCurArgs['lightbox']) || !count($aPlaylistItems) || count($aPlaylistItems) == 1 ) {
      return $output;
    }
   
    global $FV_Player_Pro;
    if( !empty($FV_Player_Pro) && count($FV_Player_Pro->bVideoAdsStatus) ) return $output;

    global $fv_fp;
    $output = array();
    $output['html'] = '';
    $output['script'] = array();

    $i = 0;
    $after = '';
    foreach ($aPlaylistItems AS $key => $aSrc) {
      $i++;
      unset($aCurArgs['playlist'], $aCurArgs['id']);
      $aCurArgs['src'] = $aSrc['sources'][0]['src'];  //  todo: remaining sources!
      $aCurArgs['splash'] = isset($aSplashScreens[$key]) ? $aSplashScreens[$key] : false;
      $aCurArgs['caption'] = isset($aCaptions[$key]) ? $aCaptions[$key] : false;
      
      $aCurArgs['liststyle'] = 'horizontal';  //  it's the only safe choice!

      $aPlayer = $fv_fp->build_min_player($aCurArgs['src'], $aCurArgs);

      if ( $this->is_text_lightbox($aCurArgs) ) {
        if ($i == 1) {
          $output['html'] .= "<li>".$aPlayer['html']."</li>";          
        }
        
        if( $i > 1 ) {
          $sTitle = $this->get_title_attr($fv_fp->aCurArgs);
          $output['html'] .= "<li><a".$this->fancybox_opts()." id='fv_flowplayer_lightbox_starter'$sTitle class='fv-player-lightbox-link' href='#' data-src='#wpfp_" . $fv_fp->hash . "'>" . $fv_fp->aCurArgs['caption'] . "</a></li>";
        }
        
      } else {
        if ($i == 1) {
          $output['html'] .= $aPlayer['html'];
          $output['html'] .= "<div class='fp-playlist-external ".$fv_fp->get_playlist_class($aCaptions)."'>";
        }
        
        $aPlayerParts = explode("<div class='fv_player_lightbox_hidden'", $aPlayer['html']);
        if( $i == 1 ) {
          $output['html'] .= "<a id='fv_flowplayer_lightbox_placeholder' href='#' onclick='document.getElementById(\"fv_flowplayer_" . $fv_fp->hash . "_lightbox_starter\").click(); return false'><div style=\"background-image: url('" . $fv_fp->aCurArgs['splash'] . "')\"></div><h4><span>" . $fv_fp->aCurArgs['caption'] . "</span></h4></a>";
          
        } else {
          $output['html'] .= "<a".$this->fancybox_opts($fv_fp->aCurArgs['splash'])."  id='fv_flowplayer_lightbox_starter' class='fv-player-lightbox-link' href='#' data-src='#wpfp_" . $fv_fp->hash . "'><div style=\"background-image: url('" . $fv_fp->aCurArgs['splash'] . "')\"></div><h4><span>" . $fv_fp->aCurArgs['caption'] . "</span></h4></a>";
          
        }
        
        if ($i > 1) {
          $after .= "<div class='fv_player_lightbox_hidden'" . $aPlayerParts[1];
        }
        
      }

      if ($i == count($aPlaylistItems)) {
        $output['html'] .= "</div>";
      }

      foreach ($aPlayer['script'] AS $key2 => $value) {
        $output['script'][$key2] = array_merge(isset($output['script'][$key2]) ? $output['script'][$key2] : array(), $aPlayer['script'][$key2]);
      }
    }
    
    if ( $this->is_text_lightbox($aCurArgs) ) {
      $output['html'] = "<ul>".$output['html']."</ul>";
    }

    $output['html'] .= $after;
    
    return $output;
  }

  function html_to_lightbox_videos($content) {

    //  todo: disabling the option should turn this off
    if (stripos($content, 'colorbox') !== false) {
      $content = preg_replace_callback('~<a[^>]*?class=[\'"][^\'"]*?colorbox[^\'"]*?[\'"][^>]*?>([\s\S]*?)</a>~', array($this, 'html_to_lightbox_videos_callback'), $content);
      return $content;
    }

    if( stripos($content, 'lightbox') !== false ) {
      $content = preg_replace_callback('~<a[^>]*?class=[\'"][^\'"]*?lightbox[^\'"]*?[\'"][^>]*?>([\s\S]*?)</a>~', array($this, 'html_to_lightbox_videos_callback'), $content);
      return $content;
    }

    return $content;
  }

  function html_to_lightbox_videos_callback($matches) {
    $html = $matches[0];
    $caption = trim($matches[1]);
    if( stripos($html,'.mp4') !== false &&
       stripos($html,'.webm') !== false &&
       stripos($html,'.m4v') !== false &&
       stripos($html,'.mov') !== false &&
       stripos($html,'.ogv') !== false &&
       stripos($html,'.ogg') !== false &&
       stripos($html,'.m3u8') !== false &&
       stripos($html,'youtube.com/') !== false &&
       stripos($html,'youtu.be/') !== false &&
       stripos($html,'vimeo.com/') !== false
       ) {
      return $html;  
    }
    
    if( preg_match( '~href=[\'"](.*?(?:mp4|webm|m4v|mov|ogv|ogg|m3u8|youtube\.com|youtu\.be|vimeo.com).*?)[\'"]~', $html, $href ) ) {
      if( stripos($caption,'<img') === 0 ) {
        return '[fvplayer src="'.esc_attr($href[1]).'" lightbox="true;text" caption_html="'.base64_encode($caption).'"]';
      } else {
        return '[fvplayer src="'.esc_attr($href[1]).'" lightbox="true;text" caption="'.esc_attr($caption).'"]';
      }
    }

    return $html;
  }

  function html_lightbox_images($content) {
    global $fv_fp;
    //TODO IMAGES

    if( $fv_fp->_get_option('lightbox_images') === false ) {
      return $content;
    }

    $content = preg_replace_callback('~(<a[^>]*?>\s*?)(<img.*?>)~', array($this, 'html_lightbox_images_callback'), $content);
    return $content;
  }

  function html_lightbox_images_callback($matches) {
    if( stripos($matches[1],'data-fancybox') ) return $matches[0];
    
    if (!preg_match('/href=[\'"].*?(jpeg|jpg|jpe|gif|png)(?:\?.*?|\s*?)[\'"]/i', $matches[1]))
      return $matches[0];

    $matches[1] = str_replace( '<a ', '<a data-fancybox="gallery" ', $matches[1] );

    return $matches[1] . $matches[2];
  }

  function disable_autoplay($aArgs) {
    if (isset($aArgs['lightbox'])) {
      $aArgs['autoplay'] = 'false';
    }
    return $aArgs;
  }
  
  function parse_args( $aArgs ) {
    foreach ($aArgs AS $k => $i) {
      if ($i == 'text') {
        unset($aArgs[$k]);
        $bUseAnchor = true;
      }
    }
    return $aArgs;
  }

  function parse_html_caption( $aArgs ) {
    if( isset($aArgs['caption_html']) && $aArgs['caption_html'] ) {
      $aArgs['caption'] = base64_decode($aArgs['caption_html']);
      unset($aArgs['caption_html']);
    }
    return $aArgs;
  }

  function shortcode_editor() {
    global $fv_fp;

    $bLightbox = $fv_fp->_get_option(array('interface','lightbox'));

    if ($bLightbox) {
      ?>

      <tr<?php if (!$bLightbox) echo ' style="display: none"'; ?>>
        <th scope="row" class="label"><label for="fv_wp_flowplayer_field_lightbox" class="alignright">Lightbox popup</label></th>
        <td class="field">
          <input type="checkbox" id="fv_wp_flowplayer_field_lightbox" name="fv_wp_flowplayer_field_lightbox" />        
          <input type="text" id="fv_wp_flowplayer_field_lightbox_width" name="fv_wp_flowplayer_field_lightbox_width" style="width: 12%" placeholder="Width" />
          <input type="text" id="fv_wp_flowplayer_field_lightbox_height" name="fv_wp_flowplayer_field_lightbox_height" style="width: 12%" placeholder="Height" />
          <input type="text" id="fv_wp_flowplayer_field_lightbox_caption" name="fv_wp_flowplayer_field_lightbox_caption" style="width: 62%" placeholder="Title" />
        </td>
      </tr>
      <script>

        jQuery(document).on('fv_flowplayer_shortcode_parse', function (e, shortcode) {

          document.getElementById("fv_wp_flowplayer_field_lightbox").checked = 0;
          document.getElementById("fv_wp_flowplayer_field_lightbox_width").value = '';
          document.getElementById("fv_wp_flowplayer_field_lightbox_height").value = '';
          document.getElementById("fv_wp_flowplayer_field_lightbox_caption").value = '';

          var sLightbox = shortcode.match(/lightbox="(.*?)"/);
          if (sLightbox && typeof (sLightbox) != "undefined" && typeof (sLightbox[1]) != "undefined") {
            sLightbox = sLightbox[1];
            fv_wp_fp_shortcode_remains = fv_wp_fp_shortcode_remains.replace(/lightbox="(.*?)"/, '');

            if (sLightbox) {
              var aLightbox = sLightbox.split(/[;]/, 4);
              if (aLightbox.length > 2) {
                for (var i in aLightbox) {
                  if (i == 0 && aLightbox[i] == 'true') {
                    document.getElementById("fv_wp_flowplayer_field_lightbox").checked = 1;
                  } else if (i == 1) {
                    document.getElementById("fv_wp_flowplayer_field_lightbox_width").value = parseInt(aLightbox[i]);
                  } else if (i == 2) {
                    document.getElementById("fv_wp_flowplayer_field_lightbox_height").value = parseInt(aLightbox[i]);
                  } else if (i == 3) {
                    document.getElementById("fv_wp_flowplayer_field_lightbox_caption").value = aLightbox[i].trim();
                  }
                }
              } else {
                if (typeof (aLightbox[0]) != "undefined" && aLightbox[0] == 'true') {
                  document.getElementById("fv_wp_flowplayer_field_lightbox").checked = 1;
                }
                if (typeof (aLightbox[1]) != "undefined") {
                  document.getElementById("fv_wp_flowplayer_field_lightbox_caption").value = aLightbox[1].trim();
                }
              }
            }
          }
        });
        jQuery(document).on('fv_flowplayer_shortcode_create', function () {
          if (document.getElementById("fv_wp_flowplayer_field_lightbox").checked) {
            var iWidth = parseInt(document.getElementById("fv_wp_flowplayer_field_lightbox_width").value);
            var iHeight = parseInt(document.getElementById("fv_wp_flowplayer_field_lightbox_height").value);
            var sSize = (iWidth && iHeight) ? ';' + iWidth + ';' + iHeight : '';
            var sCaption = ';' + document.getElementById("fv_wp_flowplayer_field_lightbox_caption").value.trim();
            fv_wp_fp_shortcode += ' lightbox="true' + sSize + sCaption + '"';
          }
        })

      </script>
      <?php
    }
  }
  
  function lightbox_admin_integrations_html() {
    global $fv_fp;
    $fv_fp->_get_checkbox(__('Remove fancyBox', 'fv-wordpress-flowplayer'), 'lightbox_force', __('Use if FV Player lightbox is not working and you see a "fancyBox already initialized" message on JavaScript console.', 'fv-wordpress-flowplayer'));
  }

  function lightbox_admin_interface_html() {
    global $fv_fp;
    $fv_fp->_get_checkbox(__('Enable video lightbox', 'fv-wordpress-flowplayer'), array('interface', 'lightbox'), __('You can also put in <code>&lt;a href="http://path.to.your/video.mp4" class="colorbox"&gt;Your link title&lt;/a&gt;</code> for a quick lightboxed video.', 'fv-wordpress-flowplayer'));
  }

  function lightbox_admin_default_options_html() {
    global $fv_fp;
    ?>
    <tr>
      <td style="width: 250px"><label for="lightbox_images"><?php _e('Use video lightbox for images as well', 'fv-wordpress-flowplayer'); ?>:</label></td>
      <td>
        <p class="description">
          <input type="hidden" value="false" name="lightbox_images" />
          <input type="checkbox" value="true" name="lightbox_images" id="lightbox_images" <?php if ($fv_fp->_get_option('lightbox_images')) echo 'checked="checked"'; ?> />
          <?php _e('Will group images as well as videos into the same lightbox gallery. Turn <strong>off</strong> your lightbox plugin when using this.', 'fv-wordpress-flowplayer'); ?> <span class="more"><?php _e('Also works with WordPress <code>[gallery]</code> galleries - these are automatically switched to link to image URLs rather than the attachment pages.'); ?></span> <a href="#" class="show-more">(&hellip;)</a>
        </p>
      </td>
    </tr>
    <tr id="lightbox-wp-galleries">
      <td style="width: 250px"><label for="lightbox_improve_galleries"><?php _e('Use video lightbox for WP Galleries', 'fv-wordpress-flowplayer'); ?>:</label></td>
      <td>
        <p class="description">
          <input type="hidden" value="false" name="lightbox_improve_galleries" />
          <input type="checkbox" value="true" name="lightbox_improve_galleries" id="lightbox_improve_galleries" <?php if ($fv_fp->_get_option('lightbox_improve_galleries')) echo 'checked="checked"'; ?> />
          <?php _e('Your gallery items will link to image files directly to allow this.', 'fv-wordpress-flowplayer'); ?>
        </p>
      </td>
    </tr>
    <script>
      jQuery(document).ready(function(){
        var lightbox_images = jQuery('#lightbox_images');
        if(lightbox_images.attr('checked')){
            jQuery('#lightbox-wp-galleries').show();
          }else{
            jQuery('#lightbox-wp-galleries').hide();
          }
        lightbox_images.on('click',function(){
          if(jQuery(this).attr('checked')){
            jQuery('#lightbox-wp-galleries').show();
          }else{
            jQuery('#lightbox-wp-galleries').hide();
          }
        })
      })   
    </script>
    <?php
  }
  
  function fancybox_opts( $splash = false ) {
    $options = array('touch' => false);
    if( !empty($splash) ) $options['thumb'] = $splash;
    return " data-fancybox='gallery' data-options='".json_encode($options)."'";
  }

}

global $FV_Player_lightbox;
$FV_Player_lightbox = new FV_Player_lightbox();
