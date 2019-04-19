<?php
/*  FV Folopress Base Class - set of useful functions for Wordpress plugins    
    Copyright (C) 2013  Foliovision

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 

require_once( dirname(__FILE__) . '/../includes/fp-api-private.php' );

class flowplayer extends FV_Wordpress_Flowplayer_Plugin_Private {
  private $count = 0;
  /**
   * Relative URL path
   */
  const FV_FP_RELATIVE_PATH = '';
  /**
   * Where videos should be stored
   */
  const VIDEO_PATH = '';
  /**
   * Where the config file should be
   */
  private $conf_path = '';
  /**
   * Configuration variables array
   */
  public $conf = array();
  
  public $load_tabs = false;    
  /**
   * Store scripts to load in footer
   */
  public $scripts = array();    
  
  var $ret = array('html' => false, 'script' => false);
  
  var $hash = false;

  var $bCSSInline = false;
  
  public $ad_css_default = ".wpfp_custom_ad { position: absolute; bottom: 10%; z-index: 20; width: 100%; }\n.wpfp_custom_ad_content { background: white; margin: 0 auto; position: relative }";
  
  public $ad_css_bottom = ".wpfp_custom_ad { position: absolute; bottom: 0; z-index: 20; width: 100%; }\n.wpfp_custom_ad_content { background: white; margin: 0 auto; position: relative }";
  
  public $load_dash = false;
  
  public $load_hlsjs = false;
  
  public $bCSSLoaded = false;
  
  public $aDefaultSkins = array(
      'skin-slim' => array(
          'hasBorder' => false,
          'bottom-fs' => false,
          'borderColor' => false,
          'bufferColor' => false,
          'canvas' => '#000000',
          'backgroundColor' => 'transparent',
          'font-face' => 'Tahoma, Geneva, sans-serif',
          'player-position' => '',
          'timeColor' => '#ffffff',
          'durationColor' => false,
          'design-timeline' => 'fp-slim',
          'design-icons' => 'fp-edgy'
        ),
      'skin-youtuby' => array(
          'hasBorder' => false,
          'bottom-fs' => true,
          'borderColor' => false,        
          'bufferColor' => false,          
          'canvas' => '#000000',
          'backgroundColor' => 'rgba(0, 0, 0, 0.5)',
          'font-face' =>'Tahoma, Geneva, sans-serif',
          'player-position' => '',
          'timeColor' => '#ffffff',
          'durationColor' => false,          
          'design-timeline' => 'fp-full',
          'design-icons' => ' '
        )      
    );
  

  public function __construct() {
    //load conf data into stack
    $this->_get_conf();
    
    if( is_admin() ) {
      //  update notices
      $this->readme_URL = 'https://plugins.trac.wordpress.org/browser/fv-wordpress-flowplayer/trunk/readme.txt?format=txt';   
      if( !has_action( 'in_plugin_update_message-fv-wordpress-flowplayer/flowplayer.php' ) ) {
        add_action( 'in_plugin_update_message-fv-wordpress-flowplayer/flowplayer.php', array( &$this, 'plugin_update_message' ) );
      }
       
       //  pointer boxes
      parent::__construct();
    }
    

    // define needed constants
    if (!defined('FV_FP_RELATIVE_PATH')) {
      define('FV_FP_RELATIVE_PATH', flowplayer::get_plugin_url() );
      
      $aURL = parse_url( home_url() );
      $vid = isset($_SERVER['SERVER_NAME']) ? 'http://'.$_SERVER['SERVER_NAME'] : $aURL['scheme'].'://'.$aURL['host'];
      if (dirname($_SERVER['PHP_SELF']) != '/') 
        $vid .= dirname($_SERVER['PHP_SELF']);
      define('VIDEO_DIR', '/videos/');
      define('VIDEO_PATH', $vid.VIDEO_DIR);  
    }
    
    
    //add_filter( 'fv_flowplayer_caption', array( $this, 'get_duration_playlist' ), 10, 3 );
    add_filter( 'fv_flowplayer_inner_html', array( $this, 'get_duration_video' ), 10, 2 );
    
    add_filter( 'fv_flowplayer_video_src', array( $this, 'get_amazon_secure') );
    
    add_filter( 'fv_flowplayer_splash', array( $this, 'get_amazon_secure') );
    add_filter( 'fv_flowplayer_playlist_splash', array( $this, 'get_amazon_secure') );
    
    add_filter('fv_flowplayer_css_writeout', array( $this, 'css_writeout_option' ) );
    
    add_action( 'wp_enqueue_scripts', array( $this, 'css_enqueue' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'css_enqueue' ) );
    
    add_filter( 'rewrite_rules_array', array( $this, 'rewrite_embed' ), 999999 );    
    add_filter( 'query_vars', array( $this, 'rewrite_vars' ) );
    add_filter( 'init', array( $this, 'rewrite_check' ) );
    
    add_filter( 'fv_player_custom_css', array( $this, 'popup_css' ) );

    add_action( 'wp_head', array( $this, 'template_embed_buffer' ), 999999);
    add_action( 'wp_footer', array( $this, 'template_embed' ), 0 );
    
    add_filter( 'fv_flowplayer_video_src', array( $this, 'add_fake_extension' ) );

  }
  

  public function _get_checkbox() {
    $args_num = func_num_args();

    // new method syntax with all options in the first parameter (which will be an array)
    if ($args_num == 1) {
      $options = func_get_arg(0);

      // options must be an array
      if (!is_array($options)) {
          throw new Exception('Options parameter passed to the _get_checkbox() method needs to be an array!');
      }

      $first_td_class = (!empty($options['first_td_class']) ? ' class="'.$options['first_td_class'].'"' : '');
      $key            = (!empty($options['key']) ? $options['key'] : '');
      $name           = (!empty($options['name']) ? $options['name'] : '');
      $help           = (!empty($options['help']) ? $options['help'] : '');
      $more           = (!empty($options['more']) ? $options['more'] : '');      

      if (!$key || !$name) {
        throw new Exception('Both, "name" and "key" options need to be set for _get_checkbox()!');
      }
    } else if ($args_num >= 2) {
      // old method syntax with function parameters defined as ($name, $key, $help = false, $more = false)
      $first_td_class = ' class="first"';
      $name = func_get_arg(0);
      $key = func_get_arg(1);
      $help = ($args_num >= 3 ? func_get_arg(2) : false);
      $more = ($args_num >= 4 ? func_get_arg(3) : false);      
    } else {
        throw new Exception('Invalid number of arguments passed to the _get_checkbox() method!');
    }

    $checked = $this->_get_option( $key );
    if ( $checked === 'false' ) {
      $checked = false;
    }

    if ( is_array( $key ) && count( $key ) > 1 ) {
      $key = $key[0] . '[' . $key[1] . ']';
    }
      ?>
      <tr>
          <td<?php echo $first_td_class; ?>><label for="<?php echo $key; ?>"><?php echo $name; ?>:</label></td>
          <td>
              <p class="description">
                  <input type="hidden" name="<?php echo $key; ?>" value="false"/>
                  <input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="true"<?php
                    if ( $checked ) { echo ' checked="checked"'; }

                    if (isset($options) && isset($options['data']) && is_array($options['data'])) {
                        foreach ($options['data'] as $data_item => $data_value) {
                            echo ' data-'.$data_item.'="'.$data_value.'"';
                        }
                    }
                  ?> />
                  <?php if ( $help ) {
                      echo $help;
                  } ?>
                  <?php if ( $more ) { ?>
                      <span class="more"><?php echo $more; ?></span> <a href="#" class="show-more">(&hellip;)</a>
                  <?php } ?>
              </p>
          </td>
      </tr>
      <?php
  }


  public function _get_radio($options) {
    // options must be an array
    if (!is_array($options)) {
      throw new Exception('Options parameter passed to the _get_radio() method needs to be an array!');
    }

    $first_td_class = (!empty($options['first_td_class']) ? ' class="'.$options['first_td_class'].'"' : '');
    $key            = (!empty($options['key']) ? $options['key'] : '');
    $name           = (!empty($options['name']) ? $options['name'] : '');
    $values         = (!empty($options['values']) ? $options['values'] : '');
    $value_keys     = (is_array($values) ? array_keys($values) : array());
    $help           = (!empty($options['help']) ? $options['help'] : '');
    $more           = (!empty($options['more']) ? $options['more'] : '');
    $default        = (!empty($options['default']) ? $options['default'] : reset($value_keys));

    if (!$key || !$name || !$values) {
      throw new Exception('The "name", "key" and "values" options need to be set for _get_radio()!');
    }

    $saved_value = $this->_get_option( $key );
    $selected = $default;

    // check if any of the given values match the saved one and store it for a pre-select
    foreach ($values as $index => $input_value) {
        if ($saved_value == $index) {
            $selected = $index;
            break;
        }
    }

    if ( is_array( $key ) && count( $key ) > 1 ) {
      $key = $key[0] . '[' . $key[1] . ']';
    }

    // determine style (display all checkboxes below each other or next to each other in multiple columns
    $style = (!empty($options['style']) ? $options['style'] : 'rows');

    // rows style
    if ($style == 'rows') {
      ?>
        <tr>
            <td<?php echo $first_td_class; ?>><label for="<?php echo $key; ?>"><?php echo $name; ?>:</label></td>
            <td>
                <fieldset>
                    <p>
                      <?php
                      foreach ( $values as $index => $input_value ) {
                        ?>

                          &nbsp;<input type="radio" name="<?php echo $key; ?>"
                                       id="<?php echo $key . '-' . $input_value; ?>" value="<?php echo $index; ?>"<?php
                        if ( ( $selected == $index ) ) {
                          echo ' checked="checked"';
                        }

                        if ( isset( $options ) && isset( $options['data'] ) && is_array( $options['data'] ) ) {
                          foreach ( $options['data'] as $data_item => $data_value ) {
                            echo ' data-' . $data_item . '="' . $data_value . '"';
                          }
                        }
                        ?> /> <label for="<?php echo $key . '-' . $input_value; ?>"><?php echo $input_value ?></label><br/>

                        <?php
                      }
                      ?>

                    </p>
                </fieldset>
              <?php if ( $help ) {
                echo $help;
              } ?>
              <?php if ( $more ) { ?>
                  <span class="more"><?php echo $more; ?></span> <a href="#" class="show-more">(&hellip;)</a>
              <?php } ?>
            </td>
        </tr>
      <?php
    } else {

      // columns style
?>
          <tr>
<?php
      foreach ( $values as $index => $input_value ) {
        ?>
              <td style="white-space: nowrap">
                  <fieldset>
                      <p>
                        &nbsp;<input type="radio" name="<?php echo $key; ?>"
                                     id="<?php echo $key . '-' . $input_value; ?>"
                                     value="<?php echo $index; ?>"<?php
                      if ( ( $selected == $index ) ) {
                        echo ' checked="checked"';
                      }

                      if ( isset( $options ) && isset( $options['data'] ) && is_array( $options['data'] ) ) {
                        foreach ( $options['data'] as $data_item => $data_value ) {
                          echo ' data-' . $data_item . '="' . $data_value . '"';
                        }
                      }
                      ?> /> <label for="<?php echo $key . '-' . $input_value; ?>"><?php echo $input_value ?></label><br/>
                      </p>
                  </fieldset>
                <?php if ( $help ) {
                  echo $help;
                } ?>
                <?php if ( $more ) { ?>
                    <span class="more"><?php echo $more; ?></span> <a href="#" class="show-more">(&hellip;)</a>
                <?php } ?>
              </td>
        <?php
      }
?>
          </tr>

<?php
    }
  }


  public function _get_input_text($options = array()) {
    // options must be an array
    if (!is_array($options)) {
      throw new Exception('Options parameter passed to the _get_input_text() method needs to be an array!');
    }

    $first_td_class = (!empty($options['first_td_class']) ? ' class="'.$options['first_td_class'].'"' : '');
    $class_name     = (!empty($options['class']) ? ' class="'.$options['class'].'"' : '');
    $key            = (!empty($options['key']) ? $options['key'] : '');
    $name           = (!empty($options['name']) ? $options['name'] : '');
    $title          = (!empty($options['title']) ? ' title="'.$options['title'].'" ' : '');
    $default        = (!empty($options['default']) ? $options['default'] : '');
    $help           = (!empty($options['help']) ? $options['help'] : '');     

    if (!$key || !$name) {
      throw new Exception('Both, "name" and "key" options need to be set for _get_input_text()!');
    }

    $saved_value = esc_attr( $this->_get_option($key) );
    if ( is_array( $key ) && count( $key ) > 1 ) {
      $key = $key[0] . '[' . $key[1] . ']';
    }
    ?>
      <tr>
        <td<?php echo $first_td_class; ?>><label for="<?php echo $key; ?>"><?php echo $name; ?> <?php if( $help ) echo '<a href="#" class="show-more">(?)</a>'; ?>:</label></td>
        <td>
          <input <?php echo $class_name; ?> id="<?php echo $key; ?>" name="<?php echo $key; ?>" <?php if ($title) { echo $title; } ?>type="text"  value="<?php echo (!empty($saved_value) ? $saved_value : $default); ?>"<?php
            if (isset($options['data']) && is_array($options['data'])) {
              foreach ($options['data'] as $data_item => $data_value) {
                echo ' data-'.$data_item.'="'.$data_value.'"';
              }
            }
          ?> />          
          <?php if ( $help ) { ?>
            <p class="description"><span class="more"><?php echo $help; ?></span></p>
          <?php } ?>
        </td>
      </tr>

    <?php
  }


  public function _get_input_hidden($options = array()) {
    // options must be an array
    if (!is_array($options)) {
      throw new Exception('Options parameter passed to the _get_input_hidden() method needs to be an array!');
    }

    $key     = (!empty($options['key']) ? $options['key'] : '');
    $default = (isset($options['default']) ? $options['default'] : '');

    if (!$key) {
      throw new Exception('The "key" option need to be set for _get_input_hidden()!');
    }

    $saved_value = esc_attr( $this->_get_option($key) );
    if ( is_array( $key ) && count( $key ) > 1 ) {
      $key = $key[0] . '[' . $key[1] . ']';
    }
    ?>
      <input id="<?php echo $key; ?>" name="<?php echo $key; ?>" type="hidden"  value="<?php echo (!empty($saved_value) ? $saved_value : $default); ?>"<?php
            if (isset($options['data']) && is_array($options['data'])) {
              foreach ($options['data'] as $data_item => $data_value) {
                echo ' data-'.$data_item.'="'.esc_attr($data_value).'"';
              }
            }
            ?> />

    <?php
  }
  
  
  public function _get_select() {
    $args_num = func_num_args();

    // new method syntax with all options in the first parameter (which will be an array)
    if ($args_num == 1) {
      $options = func_get_arg(0);

      // options must be an array
      if (!is_array($options)) {
        throw new Exception('Options parameter passed to the _get_select() method needs to be an array!');
      }

      $first_td_class = (!empty($options['first_td_class']) ? ' class="'.$options['first_td_class'].'"' : '');
      $key            = (!empty($options['key']) ? $options['key'] : '');
      $name           = (!empty($options['name']) ? $options['name'] : '');
      $aOptions       = (!empty($options['options']) ? $options['options'] : '');
      $class_name     = (!empty($options['class']) ? ' class="'.$options['class'].'"' : '');
      $help           = (!empty($options['help']) ? $options['help'] : '');
      $more           = (!empty($options['more']) ? $options['more'] : '');
      $default        = (isset($options['default']) ? $options['default'] : '');

      if (!$key || !$name || !$aOptions) {
        throw new Exception('The items "name", "key" and "options" need to be set in options for _get_select()!');
      }
    } else if ($args_num >= 5) {
      // old method syntax with function parameters defined as ($name, $key, $help = false, $more = false)
      $first_td_class = '';
      $name = func_get_arg(0);
      $key = func_get_arg(1);
      $aOptions = func_get_arg(4);
      $help = ($args_num >= 3 ? func_get_arg(2) : false);
      $more = ($args_num >= 4 ? func_get_arg(3) : false);
      $class_name = '';
      $default = '';
    } else {
      throw new Exception('Invalid number of arguments passed to the _get_checkbox() method!');
    }

    // check which option should be selected by default
    $option = $this->_get_option($key);
    foreach( $aOptions AS $k => $v ) {
        if ($k == $option) {
            $selected = $k;
        }
    }

    // if no option is selected, make a default one selected
    if (!isset($selected) && $default) {
        $selected = $default;
    }

    if ( is_array( $key ) && count( $key ) > 1 ) {
      $key = $key[0] . '[' . $key[1] . ']';
    }

    $key = esc_attr($key);
    ?>
      <tr>  
        <td<?php echo $first_td_class; ?>><label for="<?php echo $key ?>"><?php echo $name ?></label></td>
        <td>
          <select <?php echo $class_name; ?>id="<?php echo $key ?>" name="<?php echo $key ?>"<?php
            if (!isset($options) || !isset($options['data']) || !isset($options['data']['fv-preview'])) { echo ' data-fv-preview=""'; }

            if (isset($options) && isset($options['data']) && is_array($options['data'])) {
              foreach ($options['data'] as $data_item => $data_value) {
                echo ' data-'.$data_item.'="'.$data_value.'"';
              }
            }
          ?>>
            <?php foreach( $aOptions AS $k => $v ) : ?>
              <option value="<?php echo esc_attr($k); ?>"<?php if( (isset($selected) && $selected == $k) || ($option == $k) ) echo ' selected="selected"'; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>      
          </select>
        </td>   
      </tr>

    <?php
  }  
  
  
  private function _get_conf() {
    $conf = get_option( 'fvwpflowplayer' );
    
    if( !$conf ) { // new install, hide some of the notices
      $conf['nag_fv_player_7'] = true;
      $conf['notice_new_lightbox'] = true;
      $conf['notice_db'] = true;
    } 
        
    if( !isset( $conf['autoplay'] ) ) $conf['autoplay'] = 'false';
    if( !isset( $conf['googleanalytics'] ) ) $conf['googleanalytics'] = 'false';
    if( !isset( $conf['key'] ) ) $conf['key'] = 'false';
    if( !isset( $conf['logo'] ) ) $conf['logo'] = 'false';
    if( !isset( $conf['rtmp'] ) ) $conf['rtmp'] = 'false';
    if( !isset( $conf['auto_buffering'] ) ) $conf['auto_buffering'] = 'false';
    if( !isset( $conf['disableembedding'] ) ) $conf['disableembedding'] = 'false';
    if( !isset( $conf['disablesharing'] ) ) $conf['disablesharing'] = 'false';
    
    if( !isset( $conf['disable_video_hash_links'] ) ) $conf['disable_video_hash_links'] = $conf['disableembedding'] == 'true' ? true : false;
    
    if( !isset( $conf['popupbox'] ) ) $conf['popupbox'] = 'false';    
    if( !isset( $conf['allowfullscreen'] ) ) $conf['allowfullscreen'] = 'true';
    if( !isset( $conf['allowuploads'] ) ) $conf['allowuploads'] = 'true';
    if( !isset( $conf['postthumbnail'] ) ) $conf['postthumbnail'] = 'false';
    
    //default colors
    if( !isset( $conf['tgt'] ) ) $conf['tgt'] = 'backgroundcolor';
    if( !isset( $conf['backgroundColor'] ) ) $conf['backgroundColor'] = '#333333';
    if( !isset( $conf['canvas'] ) ) $conf['canvas'] = '#000000';
    if( !isset( $conf['sliderColor'] ) ) $conf['sliderColor'] = '#ffffff';
    /*if( !isset( $conf['buttonColor'] ) ) $conf['buttonColor'] = '#ffffff';
    if( !isset( $conf['buttonOverColor'] ) ) $conf['buttonOverColor'] = '#ffffff';*/
    if( !isset( $conf['durationColor'] ) ) $conf['durationColor'] = '#eeeeee';
    if( !isset( $conf['timeColor'] ) ) $conf['timeColor'] = '#eeeeee';
    if( !isset( $conf['progressColor'] ) ) $conf['progressColor'] = '#bb0000';
    if( !isset( $conf['bufferColor'] ) ) $conf['bufferColor'] = '#eeeeee';
    if( !isset( $conf['timelineColor'] ) ) $conf['timelineColor'] = '#666666';
    if( !isset( $conf['borderColor'] ) ) $conf['borderColor'] = '#666666';
    if( !isset( $conf['hasBorder'] ) ) $conf['hasBorder'] = 'false';
    if( !isset( $conf['adTextColor'] ) ) $conf['adTextColor'] = '#888';
    if( !isset( $conf['adLinksColor'] ) ) $conf['adLinksColor'] = '#ff3333';
    if( !isset( $conf['subtitleBgColor'] ) ) $conf['subtitleBgColor'] = 'rgba(0,0,0,0.5)';  
    if( !isset( $conf['subtitleSize'] ) ) $conf['subtitleSize'] = 16;

    //unset( $conf['playlistBgColor'], $conf['playlistFontColor'], $conf['playlistSelectedColor']);
    if( !isset( $conf['playlistBgColor'] ) ) $conf['playlistBgColor'] = '#808080';
    if( !isset( $conf['playlistFontColor'] ) ) $conf['playlistFontColor'] = '';
    if( !isset( $conf['playlistSelectedColor'] ) ) $conf['playlistSelectedColor'] = '#bb0000';
    if( !isset( $conf['logoPosition'] ) ) $conf['logoPosition'] = 'bottom-left';

    //
    
    if( !isset( $conf['parse_commas'] ) ) $conf['parse_commas'] = 'false';
    if( !isset( $conf['width'] ) ) $conf['width'] = '640';
    if( !isset( $conf['height'] ) ) $conf['height'] = '360';
    if( !isset( $conf['engine'] ) ) $conf['engine'] = 'false';
    if( !isset( $conf['font-face'] ) ) $conf['font-face'] = 'Tahoma, Geneva, sans-serif';
    if( !isset( $conf['ad'] ) ) $conf['ad'] = '';     
    if( !isset( $conf['ad_width'] ) ) $conf['ad_width'] = '';     
    if( !isset( $conf['ad_height'] ) ) $conf['ad_height'] = '';     
    if( !isset( $conf['ad_css'] ) ) $conf['ad_css'] = $this->ad_css_default;
    if( !isset( $conf['ad_show_after'] ) ) $conf['ad_show_after'] = 0;         
    if( !isset( $conf['disable_videochecker'] ) ) $conf['disable_videochecker'] = 'false';            
    if(  isset( $conf['videochecker'] ) && $conf['videochecker'] == 'off' ) { $conf['disable_videochecker'] = 'true'; unset($conf['videochecker']); }
    if( !isset( $conf['interface'] ) ) $conf['interface'] = array( 'playlist' => false, 'redirect' => false, 'autoplay' => false, 'loop' => false, 'splashend' => false, 'embed' => false, 'subtitles' => false, 'ads' => false, 'mobile' => false, 'align' => false );        
    if( !isset( $conf['interface']['popup'] ) ) $conf['interface']['popup'] = 'true';    
    if( !isset( $conf['amazon_bucket'] ) || !is_array($conf['amazon_bucket']) ) $conf['amazon_bucket'] = array('');       
    if( !isset( $conf['amazon_key'] ) || !is_array($conf['amazon_key']) ) $conf['amazon_key'] = array('');   
    if( !isset( $conf['amazon_secret'] ) || !is_array($conf['amazon_secret']) ) $conf['amazon_secret'] = array('');
    if( !isset( $conf['amazon_region'] ) || !is_array($conf['amazon_region']) ) $conf['amazon_region'] = array('');      
    if( !isset( $conf['amazon_expire'] ) ) $conf['amazon_expire'] = '5';
    if( !isset( $conf['amazon_expire_force'] ) ) $conf['amazon_expire_force'] = 'false';   
    if( !isset( $conf['fixed_size'] ) ) $conf['fixed_size'] = 'false';       
    if(  isset( $conf['responsive'] ) && $conf['responsive'] == 'fixed' ) { $conf['fixed_size'] = true; unset($conf['responsive']); }  //  some legacy setting
    if( !isset( $conf['js-everywhere'] ) ) $conf['js-everywhere'] = 'false';
    if( !isset( $conf['marginBottom'] ) ) $conf['marginBottom'] = '28';
    if( !isset( $conf['ui_play_button'] ) ) $conf['ui_play_button'] = 'true';
    if( !isset( $conf['volume'] ) ) $conf['volume'] = '0.7';
    if( !isset( $conf['player-position'] ) ) $conf['player-position'] = '';
    if( !isset( $conf['playlist_advance'] ) ) $conf['playlist_advance'] = ''; 
    if( empty( $conf['sharing_email_text'] ) ) $conf['sharing_email_text'] = __('Check out the amazing video here', 'fv-wordpress-flowplayer');


    if( !isset( $conf['liststyle'] ) ) $conf['liststyle'] = 'horizontal';
    if( !isset( $conf['ui_speed_increment'] ) ) $conf['ui_speed_increment'] = 0.25;
    if( !isset( $conf['popups_default'] ) ) $conf['popups_default'] = 'no';
    if( !isset( $conf['email_lists'] ) ) $conf['email_lists'] = array();
    
    if( !isset( $conf['sticky_video'] ) ) $conf['sticky_video'] = 'false';
    if( !isset( $conf['sticky_place'] ) ) $conf['sticky_place'] = 'right-bottom';
    if( !isset( $conf['sticky_width'] ) ) $conf['sticky_width'] = '380';
    
    if( !isset( $conf['playlist-design'] ) ) $conf['playlist-design'] = '2017';

    if (!isset($conf['skin-slim'])) $conf['skin-slim'] = array();
    if (!isset($conf['skin-youtuby'])) $conf['skin-youtuby'] = array();

    // apply existing colors from old config values to the new, skin-based config array
    if (!isset($conf['skin-custom'])) {
      $conf['skin-custom'] = array();

      // iterate over old keys and bring them in to the new, but skin marginBottom as it's in em units now
      $old_skinless_settings_array = array(
        'hasBorder', 'borderColor', /*'marginBottom',*/ 'bufferColor', 'canvas', 'backgroundColor',
        'font-face', 'player-position', 'progressColor', 'timeColor', 'durationColor',
        'design-timeline', 'design-icons'
      );

      foreach ($old_skinless_settings_array as $configKey) {
        if (isset($conf[$configKey])) {
          $conf['skin-custom'][ $configKey ] = $conf[$configKey];
        }
      }
      
      $conf['skin-slim']['progressColor'] = '#bb0000';
      $conf['skin-youtuby']['progressColor'] = '#bb0000';
    }

    // set to slim, if no skin set
    if (!isset($conf['skin'])) $conf['skin'] = 'slim';
    if (!isset($conf['hlsjs'])) $conf['hlsjs'] = 'true';

    $conf = apply_filters('fv_player_conf_defaults', $conf);
    
    update_option( 'fvwpflowplayer', $conf );
    
    //  hard-coded defaults for the skin preset
    $conf['skin-slim'] = array_merge( $conf['skin-slim'], $this->aDefaultSkins['skin-slim'] );
    $conf['skin-youtuby'] = array_merge( $conf['skin-youtuby'], $this->aDefaultSkins['skin-youtuby'] );
    
    $conf = apply_filters('fv_player_conf_loaded', $conf);
    
    $this->conf = $conf;
    return true;   
    /// End of addition
  }


  public function _get_option($key) {    
    $conf = $this->conf;

    $value = false;
    if( is_array($key) && count($key) === 2) {
      if( isset($conf[$key[0]]) && isset($conf[$key[0]][$key[1]]) ) {
        $value = $conf[$key[0]][$key[1]];
      }
    } elseif( isset($conf[$key]) ) {
      $value = $conf[$key];
    }

    if( is_string($value) ) $value = trim($value);

    if($value === 'false')
        $value = false;
    else if($value === 'true')
        $value = true;

    return $value;
  }

  
  public function _set_conf( $aNewOptions = false ) {
    if( !$aNewOptions ) $aNewOptions = $_POST;
    $sKey = !empty($aNewOptions['key']) ? trim($aNewOptions['key']) : false;
    $sKey7 = !empty($aNewOptions['key7']) ? trim($aNewOptions['key7']) : false;
    
    //  make sure the preset Skin properties are not over-written
    foreach( $this->aDefaultSkins AS $skin => $aSettings ) {
      foreach( $aSettings AS $k => $v ) {
        unset($aNewOptions[$skin][$k]);
      }
    }    

    if(isset($aNewOptions['popups'])){
      unset($aNewOptions['popups']['#fv_popup_dummy_key#']);
      
      foreach( $aNewOptions['popups'] AS $key => $value ) {
        $aNewOptions['popups'][$key]['css'] = stripslashes($value['css']);
        $aNewOptions['popups'][$key]['html'] = stripslashes($value['html']);
      }
      
      update_option('fv_player_popups',$aNewOptions['popups']);
      unset($aNewOptions['popups']);
    }
    
    foreach( $aNewOptions AS $key => $value ) {
      if( is_array($value) ) {
        $aNewOptions[$key] = $value;

        // now that we have skin colors separated in an arrayed sub-values,
        // we also need to check their values for HEX colors
        foreach ($value as $sub_array_key => $sub_array_value) {
          if( ( strpos( $sub_array_key, 'Color' ) !== FALSE || strpos( $sub_array_key, 'canvas' ) !== FALSE ) && strpos($sub_array_value, 'rgba') === FALSE) {
            $aNewOptions[$key][$sub_array_key] = (strpos($sub_array_value, '#') === FALSE ? '#' : '').strtolower($sub_array_value);
          }
        }
      } else if( in_array( $key, array('width', 'height') ) ) {
        $aNewOptions[$key] = trim( preg_replace('/[^0-9%]/', '', $value) );
      } else if( !in_array( $key, array('amazon_region', 'amazon_bucket', 'amazon_key', 'amazon_secret', 'font-face', 'ad', 'ad_css', 'subtitleFontFace','sharing_email_text','mailchimp_label','email_lists','playlist-design','subtitleBgColor') ) ) {
        $aNewOptions[$key] = trim( preg_replace('/[^A-Za-z0-9.:\-_\/]/', '', $value) );
      } else {
        $aNewOptions[$key] = stripslashes(trim($value));
      }
      if( ( strpos( $key, 'Color' ) !== FALSE || strpos( $key, 'canvas' ) !== FALSE ) && strpos($aNewOptions[$key], 'rgba') === FALSE) {
        $aNewOptions[$key] = (strpos($aNewOptions[$key], '#') === FALSE ? '#' : '').strtolower($aNewOptions[$key]);
      }
    }
    
    if( $sKey ) $aNewOptions['key'] = trim($sKey);
    if( $sKey7 ) $aNewOptions['key7'] = $sKey7;

    $aOldOptions = is_array(get_option('fvwpflowplayer')) ? get_option('fvwpflowplayer') : array();
    
    if( isset($aNewOptions['db_duration']) && $aNewOptions['db_duration'] == "true" && ( !isset($aOldOptions['db_duration']) || $aOldOptions['db_duration'] == "false" ) ) {
      global $FV_Player_Checker;
      $FV_Player_Checker->queue_add_all();
    }
    
    if( !isset($aNewOptions['pro']) || !is_array($aNewOptions['pro']) ) {
      $aNewOptions['pro'] = array();
    }
    
    if( !isset($aOldOptions['pro']) || !is_array($aOldOptions['pro']) ) {
      $aOldOptions['pro'] = array();
    }    
    
    $aNewOptions['pro'] = array_merge($aOldOptions['pro'],$aNewOptions['pro']);
    $aNewOptions = array_merge($aOldOptions,$aNewOptions);
    
    $aNewOptions = apply_filters( 'fv_flowplayer_settings_save', $aNewOptions, $aOldOptions );
    update_option( 'fvwpflowplayer', $aNewOptions );
    $this->_get_conf();   
    
    $this->css_writeout();
    
    fv_wp_flowplayer_delete_extensions_transients();
    
    if( $aOldOptions['key'] != $sKey ) {      
      global $FV_Player_Pro_loader;
      if( isset($FV_Player_Pro_loader) ) {
        $FV_Player_Pro_loader->license_key = $sKey;
      }
    }
    
    return true;  
  }

  public function _set_option($key, $value) {
    $aOldOptions = is_array(get_option('fvwpflowplayer')) ? get_option('fvwpflowplayer') : array();
    $aNewOptions = array_merge($aOldOptions,array($key => $value));
    $aNewOptions = apply_filters( 'fv_flowplayer_settings_save', $aNewOptions, $aOldOptions );
    update_option( 'fvwpflowplayer', $aNewOptions );
    $this->_get_conf();
  }

  /**
   * Salt function - returns pseudorandom string hash.
   * @return Pseudorandom string hash.
   */
  public function _salt() {
    $salt = substr(md5(uniqid(rand(), true)), 0, 10);    
    return $salt;
  }
  
  
  public function add_fake_extension( $media ) {
    if( stripos( $media, '(format=m3u8' ) !== false ) { //  http://*.streaming.mediaservices.windows.net/*.ism/manifest(format=m3u8-aapl)
      $media .= '#.m3u8'; //  if this is not added then the Flowpalyer Flash HLS won't play the HLS stream!
    }
    return $media;
  }
  
  
  private function build_playlist_html( $aArgs, $sSplashImage, $sItemCaption, $aPlayer, $index ) {
    $aPlayer = apply_filters( 'fv_player_item', $aPlayer, $index, $aArgs );
    
    if( !$sItemCaption && isset($aArgs['liststyle']) && $aArgs['liststyle'] == 'text' ) $sItemCaption = 'Video '.($index+1);
    
    $sHTML = "\t\t<a href='#' onclick='return false' data-item='".$this->json_encode($aPlayer)."'>";
    if( !isset($aArgs['liststyle']) || $aArgs['liststyle'] != 'text' ) $sHTML .= $sSplashImage ? "<div style='background-image: url(\"".$sSplashImage."\")'></div>" : "<div></div>";
        
    $sDuration = false;    
    if ($this->current_video()) {
      $sDuration = $this->current_video()->getDuration();
    }
    
    if( !empty($aArgs['durations']) ) {
      $durations = explode( ';', $aArgs['durations'] );
      if( !empty($durations[$index]) ) {
        $sDuration = $durations[$index];
      }
    }
    
    global $post;
    if( !$sDuration && $post && isset($post->ID) && isset($aPlayer['sources']) && isset($aPlayer['sources'][0]) && isset($aPlayer['sources'][0]['src']) ) {
      $sDuration = flowplayer::get_duration( $post->ID, $aPlayer['sources'][0]['src'] );
    }   
    
    if( $sItemCaption ) $sItemCaption = "<span>".$sItemCaption."</span>";
    
    if( $sDuration ) {
      $sItemCaption .= '<i class="dur">'.$sDuration.'</i>';
    }
    
    if( $sItemCaption ) {
      $sHTML .= "<h4>".$sItemCaption."</h4>";
    }
    
    $sHTML .= "</a>\n";
    
    $sHTML = apply_filters( 'fv_player_item_html', $sHTML, $aArgs, $sSplashImage, $sItemCaption, $aPlayer, $index );
    
    return $sHTML;
  }
  
  //  todo: this could be parsing rtmp://host/path/mp4:rtmp_path links as well
  function build_playlist( $aArgs, $media, $src1, $src2, $rtmp, $splash_img, $suppress_filters = false ) {

      $sShortcode = isset($aArgs['playlist']) ? $aArgs['playlist'] : false;
      $sCaption = isset($aArgs['caption']) ? $aArgs['caption'] : false;
  
      $replace_from = array('&amp;','\;', '\,');        
      $replace_to = array('<!--amp-->','<!--semicolon-->','<!--comma-->');        
      $sShortcode = str_replace( $replace_from, $replace_to, $sShortcode );
      $sItems = explode( ';', $sShortcode );

      if( $sCaption ) {
        $replace_from = array('&amp;quot;','&amp;','\;','&quot;');        
        $replace_to = array('"','<!--amp-->','<!--semicolon-->','"');        
        $sCaption = str_replace( $replace_from, $replace_to, $sCaption );
        $aCaption = explode( ';', $sCaption );        
      }
      if( isset($aCaption) && count($aCaption) > 0 ) {
        foreach( $aCaption AS $key => $item ) {
          $aCaption[$key] = str_replace('<!--amp-->','&',$item);
        }
      } 
                 
      $aItem = array();      
      
      if( $rtmp && stripos($rtmp,'rtmp://') === false ) {
        $rtmp = 'rtmp:'.$rtmp;  
      }
      
      foreach( apply_filters( 'fv_player_media', array($media, $src1, $src2, $rtmp), $this ) AS $key => $media_item ) {
        if( !$media_item ) continue;
        
        if( stripos($media_item,'rtmp:') === 0 && stripos($media_item,'rtmp://') === false ) {
          $media_item_tmp = preg_replace( '~^rtmp:~', '', $media_item );
        } else {
          $media_item_tmp = $media_item;
        }
        
        $media_url = $this->get_video_src( $media_item_tmp, array( 'suppress_filters' => $suppress_filters ) );
        
        //  add domain for relative video URLs if it's not RTMP
        if( stripos($media_item,'rtmp://') === false && $key != 3 ) {
          $media_url = $this->get_video_url($media_url);
        }
        
        if( stripos( $media_item, 'rtmp:' ) === 0 ) {
          if( !preg_match( '~^[a-z0-9]+:~', $media_url ) ) { //  no RTMP extension provided
            $ext = $this->get_mime_type($media_url,false,true) ? $this->get_mime_type($media_url,false,true).':' : false;
            $aItem[] = array( 'src' => $ext.str_replace( '+', ' ', $media_url ), 'type' => 'video/flash' );
          } else {
            $aItem[] = array( 'src' => str_replace( '+', ' ', $media_url ), 'type' => 'video/flash' );
          }
        } else {
          $aItem[] = array( 'src' => $media_url, 'type' => $this->get_mime_type($media_url) );
        }
      }
      
      $sItemCaption = ( isset($aCaption) ) ? array_shift($aCaption) : false;      
      
      list( $rtmp_server, $rtmp ) = $this->get_rtmp_server($rtmp);
      
      if( !empty($aArgs['mobile']) ) {
        $mobile = $this->get_video_src( $this->get_video_url($aArgs['mobile']) );
        $aItem[] = array( 'src' => $mobile, 'type' => $this->get_mime_type($mobile), 'mobile' => true );
      }
      
      $aPlayer = array( 'sources' => $aItem );      
      if( $rtmp_server ) $aPlayer['rtmp'] = array( 'url' => $rtmp_server );
            
      $aPlayer = apply_filters( 'fv_player_item_pre', $aPlayer, 0, $aArgs );
      
      if ($this->current_video()) {
        if( !$splash_img ) $splash_img = $this->current_video()->getSplash();            
        if( !$sItemCaption ) $sItemCaption = $this->current_video()->getCaption();            
      }
      
      $splash_img = apply_filters( 'fv_flowplayer_playlist_splash', $splash_img, $this );
      $sItemCaption = apply_filters( 'fv_flowplayer_caption', $sItemCaption, $aItem, $aArgs );
      
      $aPlaylistItems[] = $aPlayer;
      $aSplashScreens[] = $splash_img;
      $aCaptions[] = $sItemCaption;

      
      $sHTML = array();
      
      if( isset($aArgs['liststyle']) && !empty($aArgs['liststyle'])   ){
        
        $sHTML[] = $this->build_playlist_html( $aArgs, $splash_img, $sItemCaption, $aPlayer, 0 );
      }else{
        $sHTML[] = "<a href='#' class='is-active' onclick='return false'><span ".( (isset($splash_img) && !empty($splash_img)) ? "style='background-image: url(\"".$splash_img."\")' " : "" )."></span>$sItemCaption</a>\n";
      }
      
      if( count($sItems) > 0 ) {
        foreach( $sItems AS $iKey => $sItem ) {
          
          if( !$sItem ) continue;
          
          $index = $iKey + 1;
          
          $aPlaylist_item = explode( ',', $sItem );
        
          foreach( $aPlaylist_item AS $key => $item ) {
            if( $key > 0 && ( stripos($item,'http:') !== 0 && stripos($item,'https:') !== 0 && stripos($item,'rtmp:') !== 0 && stripos($item,'/') !== 0 ) ) {
              $aPlaylist_item[$key-1] .= ','.$item;              
              $aPlaylist_item[$key] = $aPlaylist_item[$key-1];
              unset($aPlaylist_item[$key-1]);
            }
            $aPlaylist_item[$key] = str_replace( $replace_to, $replace_from, $aPlaylist_item[$key] );                          
          }
  
          $aItem = array();
          $sSplashImage = false;

          foreach( apply_filters( 'fv_player_media', $aPlaylist_item, $this ) AS $aPlaylist_item_i ) {
            if( preg_match('~\.(png|gif|jpg|jpe|jpeg)($|\?)~',$aPlaylist_item_i) ) {
              $sSplashImage = $aPlaylist_item_i;
              continue;
            }
            
            $media_url = $this->get_video_src( preg_replace( '~^rtmp:~', '', $aPlaylist_item_i ), array( 'suppress_filters' => $suppress_filters ) );
            
            if( stripos( $aPlaylist_item_i, 'rtmp:' ) === 0 ) {
              if( !preg_match( '~^[a-z0-9]+:~', $media_url ) ) { //  no RTMP extension provided
                $ext = $this->get_mime_type($media_url,false,true) ? $this->get_mime_type($media_url,false,true).':' : false;
                $aItem[] = array( 'src' => $ext.str_replace( '+', ' ', $media_url ), 'type' => 'video/flash' );
              } else {
                $aItem[] = array( 'src' => str_replace( '+', ' ', $media_url ), 'type' => 'video/flash' );
              }             
            } else {
              $aItem[] = array( 'src' => $media_url, 'type' => $this->get_mime_type($media_url) ); 
            }                
            
          }          

          $aPlayer = array( 'sources' => $aItem );      
          if( $rtmp_server ) $aPlayer['rtmp'] = array( 'url' => $rtmp_server );
          
          $sItemCaption = ( isset($aCaption[$iKey]) ) ? __($aCaption[$iKey]) : false;                    
          
          if( !$sSplashImage && $this->_get_option('splash') ) {
            $sSplashImage = $this->_get_option('splash');
          }
          
          $aPlayer = apply_filters( 'fv_player_item_pre', $aPlayer, $index, $aArgs );
          
          if ($this->current_video()) {
            if( !$sSplashImage ) $sSplashImage = $this->current_video()->getSplash();            
            if( !$sItemCaption ) $sItemCaption = $this->current_video()->getCaption();            
          }
          
          $aPlaylistItems[] = $aPlayer;
          $sSplashImage = apply_filters( 'fv_flowplayer_playlist_splash', $sSplashImage, $this, $aPlaylist_item );
          $sItemCaption = apply_filters( 'fv_flowplayer_caption', $sItemCaption, $aItem, $aArgs );
          
          $sHTML[] = $this->build_playlist_html( $aArgs, $sSplashImage, $sItemCaption, $aPlayer, $index );
          if( $sSplashImage ) {
            $aSplashScreens[] = $sSplashImage;  
          } 
          $aCaptions[] = $sItemCaption;
        }
      }  
      
      if(isset($this->aCurArgs['liststyle']) && $this->aCurArgs['liststyle'] != 'tabs'){
        $aPlaylistItems = apply_filters('fv_flowplayer_playlist_items',$aPlaylistItems,$this);
      } 
      
      
      $sHTML = apply_filters( 'fv_flowplayer_playlist_item_html', $sHTML );
      
      $attributes = array();
      $attributes_html = '';
      $attributes['class'] = 'fp-playlist-external '.$this->get_playlist_class($aCaptions);
      $attributes['rel'] = 'wpfp_'.$this->hash;
      if( isset($this->aCurArgs['liststyle']) && $this->aCurArgs['liststyle'] == 'slider' ) {
        $attributes['style'] = "width: ".(count($aPlaylistItems)*250)."px"; // we put in enough to be sure it will fit in, later JS calculates a better value
      }
      
      $attributes = apply_filters( 'fv_player_playlist_attributes', $attributes, $media, $this );
      foreach( $attributes AS $attr_key => $attr_value ) {
        $attributes_html .= ' '.$attr_key.'="'.esc_attr( $attr_value ).'"';
      }

      $sHTML = "\t<div$attributes_html>\n".implode( '', $sHTML )."\t</div>\n";
      
      if( isset($aArgs['liststyle']) && $this->aCurArgs['liststyle'] == 'slider' ) {
        $sHTML = "<div class='fv-playlist-slider-wrapper'>".$sHTML."</div>\n";
      }
      
      return array( $sHTML, $aPlaylistItems, $aSplashScreens, $aCaptions );      
  }
  
  
  function css_generate( $skip_style_tag = true ) {
    $this->_get_conf(); //  todo: without this the colors for skin-slim might end up empty, why?
    
    $sSubtitleBgColor = $this->_get_option('subtitleBgColor');
    if( $sSubtitleBgColor[0] == '#' && $this->_get_option('subtitleBgAlpha') ) {
      $sSubtitleBgColor = 'rgba('.hexdec(substr($sSubtitleBgColor,1,2)).','.hexdec(substr($sSubtitleBgColor,3,2)).','.hexdec(substr($sSubtitleBgColor,5,2)).','.$this->_get_option('subtitleBgAlpha').')';
    }

    if( !$skip_style_tag ) : ?>
      <style type="text/css">
    <?php endif;
    
    $css = '';
    
    //  generate CSS for all the available skin settings
    foreach( array('skin-slim','skin-youtuby','skin-custom') AS $skin ) {
      $sel = '.flowplayer.'.$skin;
      
      $sBackground = $this->_get_option( array($skin, 'backgroundColor') );
      $sBuffer = $this->_get_option(array($skin, 'bufferColor') );
      $sDuration = $this->_get_option( array($skin, 'durationColor') );
      $sProgress = $this->_get_option(array($skin, 'progressColor'));
      $sTime = $this->_get_option( array($skin, 'timeColor') );
      $sTimeline = $this->_get_option( array($skin, 'timelineColor') );
      
      if( $this->_get_option(array($skin, 'hasBorder')) ) {
        $css .= $sel." { border: 1px solid ".$this->_get_option(array($skin, 'borderColor'))."; }\n";
      }
  
      if( $this->_get_option(array($skin, 'marginBottom')) !== false ) {
        $iMargin = intval($this->_get_option(array($skin, 'marginBottom')));
        $css .= $sel." { margin: 0 auto ".$iMargin."em auto; display: block; }\n";
        $css .= $sel.".has-caption { margin: 0 auto; }\n";
        $css .= $sel.".fixed-controls { margin-bottom: ".($iMargin+2.4)."em; display: block; }\n";
        $css .= $sel.".has-abloop { margin-bottom: ".($iMargin+2.4)."em; }\n";
        $css .= $sel.".fixed-controls.has-abloop { margin-bottom: ".($iMargin+2.4)."em; }\n";
      }
      
      $css .= $sel." { background-color: ".$this->_get_option(array($skin, 'canvas'))." !important; }\n";
      $css .= $sel." .fp-color, ".$sel." .fp-selected { background-color: ".$this->_get_option(array($skin, 'progressColor'))." !important; }\n";
      $css .= $sel." .fp-color-fill .svg-color, ".$sel." .fp-color-fill svg.fvp-icon, ".$sel." .fp-color-fill { fill: ".$this->_get_option(array($skin, 'progressColor'))." !important; color: ".$this->_get_option(array($skin, 'progressColor'))." !important; }\n";
      $css .= $sel." .fp-controls, .fv-player-buttons a:active, .fv-player-buttons a { background-color: ".$sBackground." !important; }\n";
      if( $sDuration ) {
        $css .= $sel." a.fp-play, ".$sel." a.fp-mute, ".$sel." .fp-controls, ".$sel." .fv-ab-loop, .fv-player-buttons a:active, .fv-player-buttons a { color: ".$sDuration." !important; }\n";
        $css .= $sel." .fv-fp-prevbtn:before, ".$sel." .fv-fp-nextbtn:before { border-color: ".$sDuration." !important; }\n";
        $css .= $sel." .fvfp_admin_error, ".$sel." .fvfp_admin_error a, #content ".$sel." .fvfp_admin_error a { color: ".$sDuration."; }\n";
      }
      if( $sBuffer ) {
        $css .= $sel." .fp-volumeslider, ".$sel." .fp-buffer, ".$sel." .noUi-background, ".$sel." .fv-ab-loop .noUi-handle { background-color: ".$sBuffer." !important; }\n";
      }
      if( $sTimeline ) {
        $css .= $sel." .fp-timeline { background-color: ".$sTimeline." !important; }\n";
      }
      
      $css .= $sel." .fp-elapsed, ".$sel." .fp-duration, ".$sel." .noUI-time-in, ".$sel." .noUI-time-out { color: ".$sTime." !important; }\n";
      $css .= $sel." .fv-wp-flowplayer-notice-small { color: ".$sTime." !important; }\n";
      
      if( $sBackground != 'transparent' ) {
        $css .= $sel." .fv-ab-loop { background-color: ".$sBackground." !important; }\n";
        $css .= $sel." .fv-ab-loop .noUi-handle { color: ".$sBackground." !important; }\n";
        $css .= $sel." .fv_player_popup, .fvfp_admin_error_content {  background: ".$sBackground."; }\n";
      }
      $css .= $sel." .fv-ab-loop .noUi-connect, .fv-player-buttons a.current { background-color: ".$sProgress." !important; }\n";
      $css .= "#content ".$sel.", ".$sel." { font-family: ".$this->_get_option(array($skin, 'font-face'))."; }\n";
      $css .= $sel." .fp-dropdown li.active { background-color: ".$sProgress." !important }\n";      
    }
    
    echo $css;
    
    //  rest is not depending of the skin settings or can use the default skin
    $skin = 'skin-'.$this->_get_option('skin');
    
    if ( $this->_get_option('key') && $this->_get_option('logo') ) : ?>
      .flowplayer .fp-logo { display: block; opacity: 1; }
    <?php endif; ?>
      
    .wpfp_custom_background { display: none; }  
    .wpfp_custom_popup { position: absolute; top: 10%; z-index: 20; text-align: center; width: 100%; color: #fff; }
    .wpfp_custom_popup h1, .wpfp_custom_popup h2, .wpfp_custom_popup h3, .wpfp_custom_popup h4 { color: #fff; }
    .is-finished .wpfp_custom_background { display: block; }  
    
    <?php echo $this->_get_option('ad_css'); ?>
    .wpfp_custom_ad { color: <?php echo $this->_get_option('adTextColor'); ?>; z-index: 20 !important; }
    .wpfp_custom_ad a { color: <?php echo $this->_get_option('adLinksColor'); ?> }
    
    .fp-playlist-external > a > span { background-color:<?php echo $this->_get_option('playlistBgColor');?>; }
    <?php if ( $this->_get_option('playlistFontColor') && $this->_get_option('playlistFontColor') !=='#') : ?>.fp-playlist-external > a,.fp-playlist-vertical a h4 { color:<?php echo $this->_get_option('playlistFontColor');?>; }<?php endif; ?>
    .fp-playlist-external > a.is-active > span { border-color:<?php echo $this->_get_option('playlistSelectedColor');?>; }
    .fp-playlist-external.fv-playlist-design-2014 a.is-active,.fp-playlist-external.fv-playlist-design-2014 a.is-active h4,.fp-playlist-external.fp-playlist-only-captions a.is-active,.fp-playlist-external.fv-playlist-design-2014 a.is-active h4, .fp-playlist-external.fp-playlist-only-captions a.is-active h4 { color:<?php echo $this->_get_option('playlistSelectedColor');?>; }
    <?php if ( $this->_get_option('playlistBgColor') !=='#') : ?>.fp-playlist-vertical { background-color:<?php echo $this->_get_option('playlistBgColor');?>; }<?php endif; ?>

    <?php if( $this->_get_option('subtitleSize') ) : ?>.flowplayer .fp-captions p { font-size: <?php echo intval($this->_get_option('subtitleSize')); ?>px; }<?php endif; ?>
    <?php if( $this->_get_option('subtitleFontFace') ) : ?>.flowplayer .fp-captions p { font-family: <?php echo $this->_get_option('subtitleFontFace'); ?>; }<?php endif; ?>
    <?php if( $this->_get_option('logoPosition') ) :
      $value = $this->_get_option('logoPosition');
      if( $value == 'bottom-left' ) {
        $sCSS = "bottom: 30px; left: 15px";
      } else if( $value == 'bottom-right' ) {
        $sCSS = "bottom: 30px; right: 15px; left: auto";
      } else if( $value == 'top-left' ) {
        $sCSS = "top: 30px; left: 15px; bottom: auto";
      } else if( $value == 'top-right' ) {
        $sCSS = "top: 30px; right: 15px; bottom: auto; left: auto";
      }
      ?>.flowplayer .fp-logo { <?php echo $sCSS; ?> }<?php endif; ?>
      
    .flowplayer .fp-captions p { background-color: <?php echo $sSubtitleBgColor; ?> }
  
    <?php if( $this->_get_option(array($skin, 'player-position')) && 'left' == $this->_get_option(array($skin, 'player-position')) ) : ?>.flowplayer { margin-left: 0; }<?php endif; ?>
    <?php echo apply_filters('fv_player_custom_css',''); ?>
    <?php if( !$skip_style_tag ) : ?>
      </style>  
    <?php endif;
  }
  
  
  function css_enqueue( $force = false ) {
    
    if( is_admin() && !did_action('admin_footer') && ( !isset($_GET['page']) || $_GET['page'] != 'fvplayer' ) ) {
      return;
    }
    
    /*
     *  Let's check if FV Player is going to be used before loading CSS!
     */
    global $posts, $post;
    if( !$posts || empty($posts) ) $posts = array( $post );
    
    if( !$force && !$this->_get_option('js-everywhere') && isset($posts) && count($posts) > 0 ) {
      $bFound = false;
      
      if( $this->_get_option('parse_comments') ) { //  if video link parsing is enabled, we need to check if there might be a video somewhere
        $bFound = true;
      }         
      
      foreach( $posts AS $objPost ) {
        if( !empty($objPost->post_content) && (
            stripos($objPost->post_content,'[fvplayer') !== false ||
            stripos($objPost->post_content,'[flowplayer') !== false ||
            stripos($objPost->post_content,'[video') !== false
          )
        ) {
          $bFound = true;
          break;
        }
      }
      
      //  also check widgets - is there widget_fvplayer among active widgets?
      if( !$bFound ) {
        $aWidgets = get_option('sidebars_widgets');
        if( isset($aWidgets['wp_inactive_widgets']) ) {
          unset($aWidgets['wp_inactive_widgets']);
        }
        if( stripos(json_encode($aWidgets),'widget_fvplayer') !== false ) {
          $bFound = true;
        }
      }
      
      if( !$bFound ) {
        return;
      }
    }
    
    $this->bCSSLoaded = true;
    
    global $fv_wp_flowplayer_ver;
    $this->bCSSInline = true;
    $sURL = FV_FP_RELATIVE_PATH.'/css/flowplayer.css';
    $sVer = $fv_wp_flowplayer_ver;

    if( apply_filters('fv_flowplayer_css_writeout', true ) && $this->_get_option($this->css_option()) ) {      
      if( @file_exists($this->css_path()) ) {
        $sURL = $this->css_path('url');
        $sVer = $this->_get_option($this->css_option());
        $this->bCSSInline = false;
      }
    }
    
    if( is_admin() &&  did_action('admin_footer') ) {
      echo "<link rel='stylesheet' id='fv_flowplayer-css'  href='".esc_attr($sURL)."?ver=".$sVer."' type='text/css' media='all' />\n";
      echo "<link rel='stylesheet' id='fv_flowplayer_admin'  href='".FV_FP_RELATIVE_PATH."/css/admin.css?ver=".$fv_wp_flowplayer_ver."' type='text/css' media='all' />\n";            
      
      if( $this->bCSSInline ) {
        $this->css_generate(false);        
      }
      
    } else {
      $aDeps = array();
      if( class_exists('OptimizePress_Default_Assets') ) $aDeps = array('optimizepress-default'); //  make sure the CSS loads after optimizePressPlugin
      
      wp_enqueue_style( 'fv_flowplayer', $sURL, $aDeps, $sVer );
      
      if(is_user_logged_in()){        
        wp_enqueue_style( 'fv_flowplayer_admin', FV_FP_RELATIVE_PATH.'/css/admin.css', array(), $fv_wp_flowplayer_ver );
      }
      
      if( $this->bCSSInline ) {
        add_action( 'wp_head', array( $this, 'css_generate' ) );
        add_action( 'admin_head', array( $this, 'css_generate' ) );
      }
      
    }
    
  }
  
  
  function css_option() {
    return 'css_writeout-'.sanitize_title(WP_CONTENT_URL);
  }
  
  
  function css_path( $type = false ) {
    if( is_multisite() ) {
      global $blog_id;
      $site_id = $blog_id;
    } else {
      $site_id = 1;
    }
    
    $name = 'fv-flowplayer-custom/style-'.$site_id.'.css';
    if( 'name' == $type ) {
      return $name;
    } else if( 'url' == $type ) {
      return trailingslashit( str_replace( array('/plugins','\\plugins'), '', plugins_url() )).$name;
    } else {
      return trailingslashit(WP_CONTENT_DIR).$name;
    }
  }
  
  
  function css_writeout() {
    if( !apply_filters('fv_flowplayer_css_writeout', true ) ) {
      return false;
    }
    
    $aOptions = get_option( 'fvwpflowplayer' );
    $aOptions[$this->css_option()] = false;
    update_option( 'fvwpflowplayer', $aOptions );
    
    /*$url = wp_nonce_url('options-general.php?page=fvplayer','otto-theme-options');
    if( false === ($creds = request_filesystem_credentials($url, $method, false, false, $_POST) ) ) { //  todo: no annoying notices here      
      return false; // stop the normal page form from displaying
    }   */ 
    
    if ( ! WP_Filesystem(true) ) {
      return false;
    }

    global $wp_filesystem;
    $filename = $wp_filesystem->wp_content_dir().$this->css_path('name');
     
    // by this point, the $wp_filesystem global should be working, so let's use it to create a file
    
    $bDirExists = false;
    if( !$wp_filesystem->exists($wp_filesystem->wp_content_dir().'fv-flowplayer-custom/') ) {
      if( $wp_filesystem->mkdir($wp_filesystem->wp_content_dir().'fv-flowplayer-custom/') ) {
        $bDirExists = true;
      }
    } else {
      $bDirExists = true;
    }
    
    if( !$bDirExists ) {
      return false;
    }
    
    ob_start();
    $this->css_generate(true);
    
    $sCSS = "\n/*CSS writeout performed on FV Flowplayer Settings save  on ".date('r')."*/\n".ob_get_clean();    
    if( !$sCSSCurrent = $wp_filesystem->get_contents( dirname(__FILE__).'/../css/flowplayer.css' ) ) {
      return false;
    }
    $sCSSCurrent = apply_filters('fv_player_custom_css',$sCSSCurrent);
    $sCSSCurrent = preg_replace( '~url\(([\'"])?~', 'url($1'.self::get_plugin_url().'/css/', $sCSSCurrent ); //  fix relative paths!
    $sCSSCurrent = str_replace( array('http://', 'https://'), array('//','//'), $sCSSCurrent );

    if( !$wp_filesystem->put_contents( $filename, "/*\nFV Flowplayer custom styles\n\nWarning: This file is not mean to be edited. Please put your custom CSS into your theme stylesheet or any custom CSS field of your template.\n*/\n\n".$sCSSCurrent.$sCSS, FS_CHMOD_FILE) ) {
      return false;
    } else {
      $aOptions[$this->css_option()] = date('U');
      update_option( 'fvwpflowplayer', $aOptions );
      $this->_get_conf();
    }
  }
  
  
  public static function esc_caption( $caption ) {
    return str_replace( array(';','[',']'), array('\;','(',')'), $caption );
  }
  
  
  function get_amazon_secure( $media ) {
    
    if( stripos($media,'X-Amz-Expires') !== false || stripos($media,'AWSAccessKeyId') !== false ) return $media;
    
    global $fv_fp;
  
    $amazon_key = -1;
    if( count($fv_fp->_get_option('amazon_key')) && count($fv_fp->_get_option('amazon_secret')) && count($fv_fp->_get_option('amazon_bucket')) ) {
      foreach( $fv_fp->_get_option('amazon_bucket') AS $key => $item ) {
        if( stripos($media,$item.'/') != false  || stripos($media,$item.'.') != false ) {
          $amazon_key = $key;
          break;
        }
      }
    }
    
    if( $amazon_key != -1 &&
       $fv_fp->_get_option( array('amazon_key', $amazon_key) ) &&$fv_fp->_get_option( array('amazon_secret', $amazon_key) ) && $fv_fp->_get_option( array('amazon_bucket', $amazon_key) ) && stripos( $media, $fv_fp->_get_option( array('amazon_bucket', $amazon_key) ) ) !== false && apply_filters( 'fv_flowplayer_amazon_secure_exclude', $media ) ) {
    
      $resource = trim( $media );

      if( !isset($fv_fp->expire_time) ) {
        $time = 60 * intval($fv_fp->_get_option('amazon_expire'));
      } else {
        $time = intval(ceil($fv_fp->expire_time));
      }
      
      if( $fv_fp->_get_option('amazon_expire_force') ) {
        $time = 60 * intval($fv_fp->_get_option('amazon_expire'));
      }
      
      if( $time < 900 ) {
        $time = 900;
      }
      
      $time = apply_filters( 'fv_flowplayer_amazon_expires', $time, $media );
      
      $url_components = parse_url($resource);
      
      $iAWSVersion = $fv_fp->_get_option( array( 'amazon_region', $amazon_key ) ) ? 4 : 2;
      
      if( $iAWSVersion == 4 ) {
        $url_components['path'] = str_replace( array('%20','+'), ' ', $url_components['path']);
      }
      
      $url_components['path'] = rawurlencode($url_components['path']);
      $url_components['path'] = str_replace('%2F', '/', $url_components['path']);
      $url_components['path'] = str_replace('%2B', '+', $url_components['path']);
      $url_components['path'] = str_replace('%2523', '%23', $url_components['path']);
      $url_components['path'] = str_replace('%252B', '%2B', $url_components['path']);  
      $url_components['path'] = str_replace('%2527', '%27', $url_components['path']);  
      
      if( $iAWSVersion == 4 ) {
        $sXAMZDate = gmdate('Ymd\THis\Z');
        $sDate = gmdate('Ymd');
        $sCredentialScope = $sDate."/".$fv_fp->_get_option( array('amazon_region', $amazon_key ) )."/s3/aws4_request"; //  todo: variable
        $sSignedHeaders = "host";
        $sXAMZCredential = urlencode( $fv_fp->_get_option( array('amazon_key', $amazon_key ) ).'/'.$sCredentialScope);
        
        //  1. http://docs.aws.amazon.com/general/latest/gr/sigv4-create-canonical-request.html      
        $sCanonicalRequest = "GET\n";
        $sCanonicalRequest .= $url_components['path']."\n";
        $sCanonicalRequest .= "X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=$sXAMZCredential&X-Amz-Date=$sXAMZDate&X-Amz-Expires=$time&X-Amz-SignedHeaders=$sSignedHeaders\n";
        $sCanonicalRequest .= "host:".$url_components['host']."\n";        
        $sCanonicalRequest .= "\n$sSignedHeaders\n";
        $sCanonicalRequest .= "UNSIGNED-PAYLOAD";
        
        //  2. http://docs.aws.amazon.com/general/latest/gr/sigv4-create-string-to-sign.html
        $sStringToSign = "AWS4-HMAC-SHA256\n";
        $sStringToSign .= "$sXAMZDate\n";
        $sStringToSign .= "$sCredentialScope\n";
        $sStringToSign .= hash('sha256',$sCanonicalRequest);
        
        //  3. http://docs.aws.amazon.com/general/latest/gr/sigv4-calculate-signature.html
        $sSignature = hash_hmac('sha256', $sDate, "AWS4".$fv_fp->_get_option( array('amazon_secret', $amazon_key) ), true );
        $sSignature = hash_hmac('sha256', $fv_fp->_get_option( array('amazon_region', $amazon_key) ), $sSignature, true );  //  todo: variable
        $sSignature = hash_hmac('sha256', 's3', $sSignature, true );
        $sSignature = hash_hmac('sha256', 'aws4_request', $sSignature, true );
        $sSignature = hash_hmac('sha256', $sStringToSign, $sSignature );
                
        //  4. http://docs.aws.amazon.com/general/latest/gr/sigv4-add-signature-to-request.html        
        $resource .= "?X-Amz-Algorithm=AWS4-HMAC-SHA256";        
        $resource .= "&X-Amz-Credential=$sXAMZCredential";
        $resource .= "&X-Amz-Date=$sXAMZDate";
        $resource .= "&X-Amz-Expires=$time";
        $resource .= "&X-Amz-SignedHeaders=$sSignedHeaders";
        $resource .= "&X-Amz-Signature=".$sSignature;              
        
        $this->ret['script']['fv_flowplayer_amazon_s3'][$this->hash] = $time;
  
      } else {
        $expires = time() + $time;
        
        if( strpos( $url_components['path'], $fv_fp->_get_option( array('amazon_bucket', $amazon_key) ) ) === false ) {
          $url_components['path'] = '/'.$fv_fp->_get_option( array('amazon_bucket', $amazon_key) ).$url_components['path'];
        }        
            
        do {
          $expires++;
          $stringToSign = "GET\n\n\n$expires\n{$url_components['path']}";  
        
          $signature = utf8_encode($stringToSign);
    
          $signature = hash_hmac('sha1', $signature, $fv_fp->_get_option( array('amazon_secret', $amazon_key ) ), true);
          $signature = base64_encode($signature);
          
          $signature = urlencode($signature);        
        } while( stripos($signature,'%2B') !== false );      
      
        $resource .= '?AWSAccessKeyId='.$fv_fp->_get_option( array('amazon_key', $amazon_key) ).'&Expires='.$expires.'&Signature='.$signature;
        
      }
      
      $media = $resource;
    
    }
    
    return $media;
  }
  
  
  public static function get_duration( $post_id, $video_src ) {
    $sDuration = false;
    if( $sVideoMeta = get_post_meta( $post_id, flowplayer::get_video_key($video_src), true ) ) {  //  todo: should probably work regardles of quality version
      if( isset($sVideoMeta['duration']) && $sVideoMeta['duration'] > 0 ) {
        $tDuration = $sVideoMeta['duration'];
        if( $tDuration < 3600 ) {
          $sDuration = gmdate( "i:s", $tDuration );
        } else {
          $sDuration = gmdate( "H:i:s", $tDuration );
        }
      }      
    }
    return $sDuration;
  }
  
  
  public static function get_duration_post( $post_id = false ) {
    global $post, $fv_fp;
    $post_id = ( $post_id ) ? $post_id : $post->ID;
    
    global $wpdb;
    $tDuration = intval( $wpdb->get_var( "SELECT vm.meta_value FROM {$wpdb->prefix}fv_player_playermeta AS pm JOIN {$wpdb->prefix}fv_player_players AS p ON p.id = pm.id_player JOIN {$wpdb->prefix}fv_player_videos AS v ON FIND_IN_SET(v.id, p.videos) > 0 JOIN {$wpdb->prefix}fv_player_videometa AS vm ON v.id = vm.id_video WHERE pm.meta_key = 'post_id' AND pm.meta_value = ".intval($post_id)." AND vm.meta_key = 'duration' ORDER BY CAST(vm.meta_value AS UNSIGNED) DESC LIMIT 1" ) );
    if( $tDuration > 3600 ) {
      return gmdate( "H:i:s", $tDuration );
    } else if( $tDuration > 0 ) {
      return gmdate( "i:s", $tDuration );
    }

    $content = false;
    $objPost = get_post($post_id);
    if( $aVideos = FV_Player_Checker::get_videos($objPost->ID) ) {
      if( $sDuration = flowplayer::get_duration($post_id, $aVideos[0]) ) {
        $content = $sDuration;
      }
    }
    
    return $content;
  }   
  
  
  public static function get_duration_playlist( $caption ) {
    global $fv_fp;
    if( !$fv_fp->_get_option('db_duration') || !$caption ) return $caption;
    
    global $post;
    $aArgs = func_get_args();
    
    if( $post && isset($aArgs[1][0]) && is_array($aArgs[1][0]) ) {        
      $sItemKeys = array_keys($aArgs[1][0]);
      if( $sDuration = flowplayer::get_duration( $post->ID, $aArgs[1][0][$sItemKeys[0]] ) ) {
        $caption .= '<i class="dur">'.$sDuration.'</i>';
      } 
    }
    
    return $caption;
  }
  
  
  public static function get_duration_video( $content ) {
    global $fv_fp, $post;    
    if( !$post || !$fv_fp->_get_option('db_duration') ) return $content;

    $aArgs = func_get_args();
    if( $sDuration = flowplayer::get_duration( $post->ID, $aArgs[1]->aCurArgs['src']) ) {
      $content .= '<div class="fvfp_duration">'.$sDuration.'</div>';
    }
    
    return $content;
  }    
  
  
  public static function get_encoded_url( $sURL ) {
    //if( !preg_match('~%[0-9A-F]{2}~',$sURL) ) {
      $url_parts = parse_url( $sURL );
      $url_parts_encoded = parse_url( $sURL );      
      if( !empty($url_parts['path']) ) {
          $url_parts['path'] = join('/', array_map('rawurlencode', explode('/', $url_parts_encoded['path'])));
      }
      if( !empty($url_parts['query']) ) {
          $url_parts['query'] = str_replace( '&amp;', '&', $url_parts_encoded['query'] );        
      }
      
      $url_parts['path'] = str_replace( '%2B', '+', $url_parts['path'] );
      return fv_http_build_url($sURL, $url_parts);
    /*} else {
      return $sURL;
    }*/    
  }
  
  
  public static function get_languages() {
    $aLangs = array(
      'SDH' => 'SDH',
      'AB' => 'Abkhazian',
      'AA' => 'Afar',
      'AF' => 'Afrikaans',
      'SQ' => 'Albanian',
      'AM' => 'Amharic',
      'AR' => 'Arabic',
      'HY' => 'Armenian',
      'AS' => 'Assamese',
      'AY' => 'Aymara',
      'AZ' => 'Azerbaijani',
      'BA' => 'Bashkir',
      'EU' => 'Basque',
      'BN' => 'Bengali, Bangla',
      'DZ' => 'Bhutani',
      'BH' => 'Bihari',
      'BI' => 'Bislama',
      'BR' => 'Breton',
      'BG' => 'Bulgarian',
      'MY' => 'Burmese',
      'BE' => 'Byelorussian',
      'KM' => 'Cambodian',
      'CA' => 'Catalan',
      'ZH' => 'Chinese',
      'ZH_HANS' => 'Chinese (Simplified)',
      'CO' => 'Corsican',
      'HR' => 'Croatian',
      'CS' => 'Czech',
      'DA' => 'Danish',
      'NL' => 'Dutch',
      'EN' => 'English',
      'EO' => 'Esperanto',
      'ET' => 'Estonian',
      'FO' => 'Faeroese',
      'FJ' => 'Fiji',
      'FI' => 'Finnish',
      'FR' => 'French',
      'FY' => 'Frisian',
      'GD' => 'Gaelic (Scots Gaelic)',
      'GL' => 'Galician',
      'KA' => 'Georgian',
      'DE' => 'German',
      'EL' => 'Greek',
      'KL' => 'Greenlandic',
      'GN' => 'Guarani',
      'GU' => 'Gujarati',
      'HA' => 'Hausa',
      'HE' => 'Hebrew',
      'HI' => 'Hindi',
      'HU' => 'Hungarian',
      'IS' => 'Icelandic',
      'ID' => 'Indonesian',
      'IA' => 'Interlingua',
      'IE' => 'Interlingue',
      'IK' => 'Inupiak',
      'GA' => 'Irish',
      'IT' => 'Italian',
      'JA' => 'Japanese',
      'JV' => 'Javanese',
      'KN' => 'Kannada',
      'KS' => 'Kashmiri',
      'KK' => 'Kazakh',
      'RW' => 'Kinyarwanda',
      'KY' => 'Kirghiz',      
      'KO' => 'Korean',
      'KU' => 'Kurdish',
      'LO' => 'Laothian',
      'LA' => 'Latin',
      'LV' => 'Latvian, Lettish',
      'LN' => 'Lingala',
      'LT' => 'Lithuanian',
      'MK' => 'Macedonian',
      'MG' => 'Malagasy',
      'MS' => 'Malay',
      'ML' => 'Malayalam',
      'MT' => 'Maltese',
      'MI' => 'Maori',
      'MR' => 'Marathi',      
      'MN' => 'Mongolian',
      'NA' => 'Nauru',
      'NE' => 'Nepali',
      'NO' => 'Norwegian',
      'OC' => 'Occitan',
      'OR' => 'Oriya',
      'OM' => 'Oromo, Afan',
      'PS' => 'Pashto, Pushto',
      'FA' => 'Persian',
      'PL' => 'Polish',
      'PT' => 'Portuguese',
      'PA' => 'Punjabi',
      'QU' => 'Quechua',
      'RM' => 'Rhaeto-Romance',
      'RO' => 'Romanian',
      'RN' => 'Rundi',
      'RU' => 'Russian',
      'SM' => 'Samoan',
      'SG' => 'Sangro',
      'SA' => 'Sanskrit',
      'SR' => 'Serbian',      
      'ST' => 'Sesotho',
      'TN' => 'Setswana',
      'SN' => 'Shona',
      'SD' => 'Sindhi',
      'SI' => 'Singhalese',
      'SS' => 'Siswati',
      'SK' => 'Slovak',
      'SL' => 'Slovenian',
      'SO' => 'Somali',
      'ES' => 'Spanish',
      'SU' => 'Sudanese',
      'SW' => 'Swahili',
      'SV' => 'Swedish',
      'TL' => 'Tagalog',
      'TG' => 'Tajik',
      'TA' => 'Tamil',
      'TT' => 'Tatar',
      'TE' => 'Tegulu',
      'TH' => 'Thai',
      'BO' => 'Tibetan',
      'TI' => 'Tigrinya',
      'TO' => 'Tonga',
      'TS' => 'Tsonga',
      'TR' => 'Turkish',
      'TK' => 'Turkmen',
      'TW' => 'Twi',
      'UK' => 'Ukrainian',
      'UR' => 'Urdu',
      'UZ' => 'Uzbek',
      'VI' => 'Vietnamese',
      'VO' => 'Volapuk',
      'CY' => 'Welsh',
      'WO' => 'Wolof',
      'XH' => 'Xhosa',
      'JI' => 'Yiddish',
      'YO' => 'Yoruba',
      'ZU' => 'Zulu'
    );
    
    ksort($aLangs);
    
    return $aLangs;
  }
  
  
  function get_mime_type($media, $default = 'flash', $no_video = false) {
    $media = trim($media);
    $aURL = explode( '?', $media ); //  throwing away query argument here
    $pathinfo = pathinfo( $aURL[0] );
    if( empty($pathinfo['extension']) ) $pathinfo = pathinfo( $media ); // but if no extension remains, keep the query arguments, todo: unit test for https://drive.google.com/uc?export=download&id=0B32098YdDwTAcmJxVl9Kc1piT2s#.mp4

    $extension = ( isset($pathinfo['extension']) ) ? $pathinfo['extension'] : false;       
    $extension = preg_replace( '~[?#].+$~', '', $extension );
    $extension = strtolower($extension);

    if( !$extension ) {
      $output = $default;
    } else {
      if ($extension == 'm3u8' || $extension == 'm3u') {
        $output = 'x-mpegurl';
      } else if ($extension == 'mpd') {
        $output = 'dash+xml';
      } else if ($extension == 'm4v') {
        $output = 'mp4';
      } else if( $extension == 'mp3' ) {
        $output = 'mpeg';
      } else if( $extension == 'wav' ) {
        $output = 'wav';
      } else if( $extension == 'ogg' ) {
        $output = 'ogg';
      } else if( $extension == 'ogv' ) {
        $output = 'ogg';
      } else if( $extension == 'mov' ) {
        $output = 'mp4';
      } else if( $extension == '3gp' ) {
        $output = 'mp4';      
      } else if( $extension == 'mkv' ) {
        $output = 'mp4';      
      } else if( $extension == 'mp3' ) {
        $output = 'mpeg';      
      } else if( !in_array($extension, array('mp4', 'm4v', 'webm', 'ogv', 'ogg', 'wav', '3gp')) ) {
        $output = $default;  
      } else {
        $output = $extension;
      }
    }
    
    if( $output == 'flash' ) {
      if( stripos( $media, '(format=m3u8' ) !== false ) { //  http://*.streaming.mediaservices.windows.net/*.ism/manifest(format=m3u8-aapl)
        $output = 'x-mpegurl';
        $extension = 'm3u8';
      }
      if( stripos( $media, '(format=mpd' ) !== false ) {  //  http://*.streaming.mediaservices.windows.net/*.ism/manifest(format=mpd-time-csf)
        $output = 'dash+xml';
        $extension = 'mpd';
      }
    }
    
    global $fv_fp;    
    if( $extension == 'mpd' ) {
      $fv_fp->load_dash = true;
    } else if( $extension == 'm3u8' ) {
      $fv_fp->load_hlsjs = true;
    }
    
    if( !$no_video ) {
      switch($extension)  {
        case 'dash+xml' :
        case 'mpd' :
          $output = 'application/'.$output;
          break;
        case 'x-mpegurl' :
          $output = 'application/'.$output;
          break;
        case 'm3u8' :
          $output = 'application/'.$output;
          break;
        case 'mp3' :
        case 'ogv' :
        case 'wav' :
          $output = 'audio/'.$output;
          break;   
        default:
          $output = 'video/'.$output;
          break;
      }
    }

    return apply_filters( 'fv_flowplayer_get_mime_type', $output, $media );  
  }
  
  
  public static function get_plugin_url() {
    if( stripos( __FILE__, '/themes/' ) !== false || stripos( __FILE__, '\\themes\\' ) !== false ) {
      return get_template_directory_uri().'/fv-wordpress-flowplayer';
    } else {
      $plugin_folder = basename(dirname(dirname(__FILE__))); // make fv-wordpress-flowplayer out of {anything}/fv-wordpress-flowplayer/models/flowplayer.php
      return plugins_url($plugin_folder);
    }
  }
  
  
  public function get_playlist_class($aCaptions) {
    $sPlaylistClass = 'fv-playlist-design-'.$this->_get_option('playlist-design');

    if( isset($this->aCurArgs['liststyle']) && in_array($this->aCurArgs['liststyle'], array('horizontal','slider') ) ) {
      $sPlaylistClass .= ' fp-playlist-horizontal';
    } else if( isset($this->aCurArgs['liststyle']) && $this->aCurArgs['liststyle'] == 'vertical' ){
      $sPlaylistClass .= ' fp-playlist-vertical';
    } else if( isset($this->aCurArgs['liststyle']) && $this->aCurArgs['liststyle'] == 'text' ){
      $sPlaylistClass = 'fp-playlist-vertical';
    }
    //var_dump($aCaptions);
    if( isset($this->aCurArgs['liststyle']) && $this->aCurArgs['liststyle'] == 'text' ){
      $sPlaylistClass .= ' fp-playlist-only-captions';
    } else if( isset($this->aCurArgs['liststyle']) && sizeof($aCaptions) > 0 && strlen(implode($aCaptions)) > 0 ){
      $sPlaylistClass .= ' fp-playlist-has-captions';
    }
    
    if( get_query_var('fv_player_embed') ) {
      $sPlaylistClass .= ' fp-is-embed';
    }

    return $sPlaylistClass;
  }
  
  
  public static function get_core_version() {
    global $fv_wp_flowplayer_core_ver;
    return $fv_wp_flowplayer_core_ver;
  }
  
  
  public static function get_video_key( $sURL ) {
    $sURL = str_replace( '?v=', '-v=', $sURL );
    $sURL = preg_replace( '~\?.*$~', '', $sURL );
    $sURL = str_replace( array('/','://'), array('-','-'), $sURL );
    return '_fv_flowplayer_'.sanitize_title($sURL);
  }
  
  
  
  
  function get_video_src($media, $aArgs = array() ) {
    $aArgs = wp_parse_args( $aArgs, array(
          'dynamic' => false, // apply URL signature for CDNs which normally use Ajax
          'suppress_filters' => false,
        )
      );
    
    if( $media ) { 
      if( !$aArgs['suppress_filters'] ) {
        $media = apply_filters( 'fv_flowplayer_video_src', $media, $aArgs );          
      }
      return strip_tags(trim($media));
    }
    return null;
  }
  
  
  function get_video_url($media) {
    if( !is_string($media) ) return $media;
    
    if( strpos($media,'rtmp://') !== false ) {
      return null;
    }
    if( strpos($media,'http://') !== 0 && strpos($media,'https://') !== 0 && strpos($media,'//') !== 0 ) {
      $http = is_ssl() ? 'https://' : 'http://';
      // strip the first / from $media
      if($media[0]=='/') $media = substr($media, 1);
      if((dirname($_SERVER['PHP_SELF'])!='/')&&(file_exists($_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']).VIDEO_DIR.$media))){  //if the site does not live in the document root
        $media = $http.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).VIDEO_DIR.$media;
      }
      else if(file_exists($_SERVER['DOCUMENT_ROOT'].VIDEO_DIR.$media)){ // if the videos folder is in the root
        $media = $http.$_SERVER['SERVER_NAME'].VIDEO_DIR.$media;//VIDEO_PATH.$media;
      }
      else{ // if the videos are not in the videos directory but they are adressed relatively
        $media_path = str_replace('//','/',$_SERVER['SERVER_NAME'].'/'.$media);
        $media = $http.$media_path;
      }
    }
    
    $media = apply_filters( 'fv_flowplayer_media', $media, $this );
    
    return $media;
  }
  
  
  public static function is_licensed() {
    global $fv_fp;
    return preg_match( '!^\$\d+!', $fv_fp->_get_option('key') );
  }
  
  
  public static function is_special_editor() {
    return flowplayer::is_optimizepress() || flowplayer::is_themify();
  }
  
  
  public static function is_optimizepress() {
    if( ( isset($_GET['page']) && $_GET['page'] == 'optimizepress-page-builder' ) ||
        ( isset($_POST['action']) && $_POST['action'] == 'optimizepress-live-editor-parse' )
      ) {
      return true;
    }
    return false;
  }
  
  
  public static function is_themify() {
    if( isset($_POST['action']) && $_POST['action'] == 'tfb_load_module_partial' ) {
      return true;
    }
    return false;
  }    
  
  
  public function is_secure_amazon_s3( $url ) {
    return preg_match( '/^.+?s3.*?\.amazonaws\.com\/.+Signature=.+?$/', $url ) || preg_match( '/^.+?\.cloudfront\.net\/.+Signature=.+?$/', $url );
  }
  
  
  public static function json_encode( $input ) {
    if( version_compare(phpversion(), '5.3.0', '>') ) {        
      return json_encode( $input, JSON_HEX_APOS );
    } else {
      return str_replace( "'", '\u0027', json_encode( $input ) );
    }
  }
  
  
  function css_writeout_option( $value ) {
    if( $this->_get_option('css_disable') ) {
      return false;
    }
    return $value;
  }
  

  function popup_css( $css ){
    $aPopupData = get_option('fv_player_popups');
    $sNewCss = '';
    if( is_array($aPopupData) ) {
      foreach($aPopupData as $key => $val){
        if( empty($val['css']) ){
          continue;
        }
        $sNewCss .= '.flowplayer '.stripslashes($val['css'])."\n";
      }
    }
    if( strlen($sNewCss) ){
      $css .= "\n/*custom popup css*/\n".$sNewCss."/*end custom popup css*/\n";
    }
    return $css;
  }  
    
  
  function rewrite_check( $aRules ) {
    $aRewriteRules = get_option('rewrite_rules');
    if( empty($aRewriteRules) || !is_array($aRewriteRules) || count($aRewriteRules) == 0 ) {
      return;
    }
    
    $bFound = false;
    foreach( $aRewriteRules AS $k => $v ) {
      if( stripos($v,'&fv_player_embed=') !== false ) {
        $bFound = true;
        break;
      }
    }
    
    if( !$bFound ) {
      flush_rewrite_rules( true );
    }
  }
  
  
  function rewrite_embed( $aRules ) {
    $aRulesNew = array();
    foreach( $aRules AS $k => $v ) {
      $aRulesNew[$k] = $v;
      if( stripos($k,'/trackback/') !== false ) {
        $new_k = str_replace( '/trackback/', '/fvp/', $k );
        $new_v = str_replace( '&tb=1', '&fv_player_embed=1', $v );
        $aRulesNew[$new_k] = $new_v;
        $new_k = str_replace( '/trackback/', '/fvp(\d+)?/', $k );
        $new_v = str_replace( '&tb=1', '&fv_player_embed=$matches['.(substr_count($v,'$matches[')+1).']', $v );
        $aRulesNew[$new_k] = $new_v;        
      }
    }
    return $aRulesNew;
  }
  
  
  function rewrite_vars( $public_query_vars ) {
    $public_query_vars[] = 'fv_player_embed';
    return $public_query_vars;
  }
  
  function template_embed_buffer(){
    if( get_query_var('fv_player_embed') ) {
      ob_start();
      
      global $fvseo;
      if( isset($_REQUEST['fv_player_preview']) ) {
        global $fvseo;
        if( isset($fvseo) ) remove_action('wp_footer', array($fvseo, 'script_footer_content'), 999999 );
        
        global $objTracker;
        if( isset($objTracker) ) remove_action( 'wp_footer', array( $objTracker, 'OutputFooter' ) );
      }
    }
  }
  
  function template_embed() {
  
    if( get_query_var('fv_player_embed') ) {
      $content = ob_get_contents();
      ob_clean();
      
      if( function_exists('rocket_insert_load_css') ) rocket_insert_load_css();

      remove_action( 'wp_footer', array( $this, 'template_embed' ),0 );
      //remove_action('wp_head', '_admin_bar_bump_cb');
      show_admin_bar(false);
      ?>
  <style>
    body { margin: 0; padding: 0; overflow:hidden; background:white;}
    body:before { height: 0px!important;}
    html {margin-top: 0px !important; overflow:hidden; }
  </style>
</head>
<body class="fv-player-preview">
  <?php if( isset($_GET['fv_player_preview']) && !empty($_GET['fv_player_preview']) ) :
    
    if( !is_user_logged_in() || !current_user_can('edit_posts') || !wp_verify_nonce( get_query_var('fv_player_embed'),"fv-player-preview-".get_current_user_id() ) ){
      ?><script>window.parent.jQuery(window.parent.document).trigger('fvp-preview-complete');</script>
      <div style="background:white;">
        <div id="wrapper" style="background:white; overflow:hidden; <?php echo $width . $height; ?>;">
          <h1 style="margin: auto;text-align: center; padding: 60px; color: darkgray;">Please log in.</h1>
        </div>
      </div>
      <?php
      die();
    }
    $dataInPost = ($_GET['fv_player_preview'] === 'POST');
    $shortcode = (!$dataInPost ? base64_decode($_GET['fv_player_preview']) : json_decode( stripslashes($_POST['fv_player_preview_json']), true ) );
    $matches = null;
    $width ='';
    $height ='';

    // width from regular shortcdode data
    if (!$dataInPost && preg_match('/width="([0-9.,]*)"/', $shortcode, $matches)){
      $width = 'width:'.$matches[1].'px;';
    }

    // width from DB shortcdode data
    if ($dataInPost && !empty($shortcode['fv_wp_flowplayer_field_width'])){
      $width = 'width:'.$shortcode['fv_wp_flowplayer_field_width'].'px;';
    }

    // height from regular shortcdode data
    if(!$dataInPost && preg_match('/height="([0-9.,]*)"/', $shortcode, $matches)){
      $height = 'min-height:'.$matches[1].'px;';
    }

    // height from DB shortcdode data
    if ($dataInPost && !empty($shortcode['fv_wp_flowplayer_field_height'])){
      $height = 'min-height:'.$shortcode['fv_wp_flowplayer_field_height'].'px;';
    }

    ?> 
    <div style="background:white;">
      <div id="wrapper" style="background:white; overflow:hidden; <?php echo $width . $height; ?>;">
        <?php
        // regular shortcode data with source
        global $fv_fp;
        if (!$dataInPost && preg_match('/id="\d+"|src="[^"][^"]*"/i',$shortcode)) {
          $aAtts = shortcode_parse_atts($shortcode);
          if ( $aAtts && !empty($aAtts['liststyle'] ) && $aAtts['liststyle'] == 'vertical' || $fv_fp->_get_option('liststyle') == 'vertical' ) {
            _e('The preview is too narrow, vertical playlist will shift below the player as it would on mobile.','fv-wordpress-flowplayer');
          }
          echo do_shortcode($shortcode);          
        } else if ($dataInPost) {
          // DB-based shortcode data
          if (
            !empty($shortcode['fv_wp_flowplayer_field_playlist']) &&
            $shortcode['fv_wp_flowplayer_field_playlist'] == 'vertical' ||
            $fv_fp->_get_option('liststyle') == 'vertical'
          ) {
            _e('The preview is too narrow, vertical playlist will shift below the player as it would on mobile.','fv-wordpress-flowplayer');
          }

          // note: we need to put "src" into the code or it won't get parsed at all
          //       and at the same time, it displays the correct SRC in the preview
          if( count($shortcode['videos']) > 0 ) {
            $tmp = array_reverse($shortcode['videos']);
            $item = array_pop($tmp);
            $src = $item['fv_wp_flowplayer_field_src'];            
          } else {
            $src = 'none';
          }
          echo do_shortcode('[fvplayer src="'.$src.'" id="POST"]');
        } else { ?>
          <h1 style="margin: auto;text-align: center; padding: 60px; color: darkgray;">No video.</h1>
          <?php
        }
        ?>
      </div>
    </div>
    
  <?php else :
    
    if( stripos($content,'<!--fv player end-->') !== false ) {
      
      $bFound = false;
      $rewrite = get_option('rewrite_rules');
      if( empty($rewrite) ) {
        $sLink = 'fv_player_embed='.get_query_var('fv_player_embed');
      } else {
        $sPostfix = get_query_var('fv_player_embed') > 1 ? 'fvp'.get_query_var('fv_player_embed') : 'fvp';
        $sLink = user_trailingslashit( trailingslashit( get_permalink() ).$sPostfix );
      }
            
      $aPlayers = explode( '<!--fv player end-->', $content );
      if( $aPlayers ) {
        foreach( $aPlayers AS $k => $v ) {
          if( stripos($v,$sLink.'"') !== false ) {
            echo substr($v, stripos($v,'<div id="wpfp_') );
            $bFound = true;
            break;
          }
        }
      }
      
      if( !$bFound ) {
        echo "<p>Player not found, see the full article: <a href='".get_permalink()."' target='_blank'>".get_the_title()."</a>.</p>";
      }    
      
    }
  endif;
  
  wp_footer();
  
  if( isset($_GET['fv_player_preview']) && !empty($_GET['fv_player_preview']) ) : ?>
    <script>
    jQuery(document).ready( function(){
      var parent = window.parent.jQuery(window.parent.document);
      if( typeof(flowplayer) != "undefined" ) {      
        parent.trigger('fvp-preview-complete', [jQuery(document).width(),jQuery(document).height()]);
      
      } else {
        parent.trigger('fvp-preview-error');
      }
    
    });
    
    if (window.top===window.self) {
      jQuery('#wrapper').css('margin','25px 50px 0 50px');
    } 
    </script>
  <?php endif; ?>

</body>

</html>       
      <?php
      exit();  
    }
  }
  

}

function fv_wp_flowplayer_save_post( $post_id ) {
  if( $parent_id = wp_is_post_revision($post_id) ) {
    $post_id = $parent_id;
  }
  
  global $post;
  $post_id = ( isset($post->ID) ) ? $post->ID : $post_id;
  
  global $fv_fp, $post, $FV_Player_Checker;
  if( !$FV_Player_Checker->is_cron && $FV_Player_Checker->queue_check($post_id) ) {
    //return;
  }
  
  $saved_post = get_post($post_id);
  $videos = FV_Player_Checker::get_videos($saved_post->ID);

  $iDone = 0;
  if( is_array($videos) && count($videos) > 0 ) {
    $tStart = microtime(true);
    foreach( $videos AS $video ) {
      if( microtime(true) - $tStart > apply_filters( 'fv_flowplayer_checker_save_post_time', 5 ) ) {
        FV_Player_Checker::queue_add($post_id);
        break;
      }
      
      if( isset($post->ID) && !get_post_meta( $post->ID, flowplayer::get_video_key($video), true ) ) {
        $video_secured = array( 'media' => $fv_fp->get_video_src( $video, array( 'dynamic' => true ) ) );
        if( isset($video_secured['media']) && $FV_Player_Checker->check_mimetype( array($video_secured['media']), array( 'meta_action' => 'check_time', 'meta_original' => $video ) ) ) {
          $iDone++;
          if( isset($_GET['fv_flowplayer_checker'] ) ) {
            echo "<p>Post $post_id video '$video' ok!</p>\n";
          }
        } else {
          if( isset($_GET['fv_flowplayer_checker'] ) ) {
            echo "<p>Post $post_id video '$video' not done, adding into queue!</p>\n";
          }
          FV_Player_Checker::queue_add($post_id);
        }
      } else {
        $iDone++;
      }
      
    }
  }

  if( !$videos || $iDone == count($videos) ) {
    FV_Player_Checker::queue_remove($post_id);
    if( isset($_GET['fv_flowplayer_checker'] ) ) {
      echo "<p>Post $post_id done, removing from queue!</p>\n";
    }
  }
}
