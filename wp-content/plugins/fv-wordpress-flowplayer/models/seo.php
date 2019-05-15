<?php

class FV_Player_SEO {
  
  var $can_seo = false;
  
  public function __construct() {
    add_filter('fv_flowplayer_args_pre', array($this, 'should_i'), 10, 3 );
    add_filter('fv_flowplayer_attributes', array($this, 'single_attributes'), 10, 3 );
    add_filter('fv_flowplayer_inner_html', array($this, 'single_video_seo'), 10, 2 );
    add_filter('fv_player_item_html', array($this, 'playlist_video_seo'), 10, 6 );

  }
  
  function single_attributes( $attributes, $media, $fv_fp ) {
    if( !empty($fv_fp->aCurArgs['playlist']) || !$this->can_seo ) {
      return $attributes;
    }
    
    $attributes['itemprop'] = 'video';
    $attributes['itemscope'] = '';
    $attributes['itemtype'] = 'http://schema.org/VideoObject';
    
    return $attributes;
  }
  
  function get_markup( $title, $description, $splash, $url ) {
    if( !$title ) {
      $title = get_the_title();
    }
    
    if( !$description ) { //  todo: read this from shortcode
      $description = get_post_meta(get_the_ID(),'_aioseop_description', true );
    }
    $post_content = get_the_content();
    if( !$description && strlen($post_content) > 0 ) {
      $post_content = strip_shortcodes( $post_content );
      $post_content = strip_tags( $post_content );
      $excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
      $description = wp_trim_words( $post_content, '10', $excerpt_more );
    }
    if( !$description ) {
      $description = get_option('blogdescription');
    }
    
    if( !$url ) {
      $url = get_permalink();
    }
    
    if( stripos($splash,'://') === false ) {
      $splash = home_url($splash);
    }
    
    return '<meta itemprop="name" content="'.esc_attr($title).'" />
        <meta itemprop="description" content="'.esc_attr($description).'" />
        <meta itemprop="thumbnailUrl" content="'.esc_attr($splash).'" />
        <meta itemprop="contentURL" content="'.esc_attr($url).'" />
        <meta itemprop="uploadDate" content="'.esc_attr(get_the_modified_date('Y-m-d')).'" />';        
  }
  
  function playlist_video_seo( $sHTML, $aArgs, $sSplashImage, $sItemCaption, $aPlayer, $index ) {
    if( $this->can_seo ) {
      $sHTML = str_replace( '<a', '<a itemprop="video" itemscope itemtype="http://schema.org/VideoObject" ', $sHTML );
      $sHTML = str_replace( '</a>', $this->get_markup($sItemCaption,false,$sSplashImage,false).'</a>', $sHTML );
    }
    return $sHTML;
  }
  
  function should_i( $args ) {
    global $fv_fp;
    if( !$fv_fp->_get_option( array( 'integrations', 'schema_org' ) ) ) {
      $this->can_seo = false;
      return $args;
    }
    
    if( !get_permalink() || !$fv_fp->get_splash() ) {
      $this->can_seo = false;
    }
    
    $dynamic_domains = apply_filters('fv_player_pro_video_ajaxify_domains', array());
    $amazon = $fv_fp->_get_option('amazon_bucket');
    if( $amazon && is_array($amazon) && count($amazon) > 0 ) {
      foreach( $amazon AS $bucket ) {
        $dynamic_domains[] = 'amazonaws.com/'.$bucket.'/';
        $dynamic_domains[] = '//'.$bucket.'.s3';
      }      
    }
    
    $cf = $fv_fp->_get_option( array('pro','cf_domain') );
    if( $cf ) {
      $cf = explode( ',', $cf );
      if( is_array($cf) && count($cf) > 0 ) {
        foreach( $cf AS $cf_domain ) {
          $dynamic_domains[] = $cf_domain;
        }
      }
    }  
    
    if( count($dynamic_domains) ) {
      $is_dynamic = false;
      foreach( $dynamic_domains AS $domain ) {
        if( stripos($args['src'],$domain) !== false ) {
          $this->can_seo = false;
          return $args;
        }
      }
    }
    
    $this->can_seo = true;    
    return $args;
  }
  
  function single_video_seo( $html, $fv_fp ) {
    if( $this->can_seo ) {
      if( !$fv_fp->aCurArgs['playlist'] ) {        
        //  todo: use iframe or video link URL
        $html .= "\n".$this->get_markup($fv_fp->aCurArgs['caption'],false,$fv_fp->get_splash(),false)."\n";    
      }
    }
    return $html;
  }  

}

$FV_Player_SEO = new FV_Player_SEO();
