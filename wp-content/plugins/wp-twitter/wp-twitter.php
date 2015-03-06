<?php
/*
 * Plugin Name: WP Twitter
 * Plugin URI: http://fabrix.net/wp-twitter
 * Description: Is a plugin that creates a complete integration between your WordPress blog and your Twitter account including a Twitter Button and Widgets.
 * Version: 4.2.3
 * Author: Fabrix DoRoMo
 * Author URI: http://fabrix.net
 * License: GPL2+
 * Text Domain: wp-twitter
 * Domain Path: /lang
 * Copyright 2013 fabrix.net (email: fabrix@fabrix.net)
 */
class WP_Twitter {
        public $pluginversion 	    = '4.2.3';
        public $pluginname			= 'WP Twitter';
		public $hook 				= 'wp-twitter'; // $this->hook
        public $_p2 	            = 'widgets-settings';
        public $_p3 	            = 'button-integration'; // $this->hook . '-'.$this->_p3
		public $accesslvl			= 'manage_options';
        public $p1_defaults         = array(
                 'tags'                        => array(),
                 'reverse'                     => false,
                 'message'                     => 'Blog Post: [title] - [link]',
                 'message2'                    => 'Blog Page: [title] - [link]',
                 'url_type'                    => 'post_id',
        	     'oauth_request_token'         => false,
                 'oauth_request_token_secret'  => false,
                 'oauth_access_token'          => false,
                 'oauth_access_token_secret'   => false,
	             'user_id'                     => 0,
                 'tweet_run_1'                 => 1,
                 'tweet_run_2'                 => 0,
                                           );
        //----------------------------------------------
        public $sbar_homepage       = 'http://fabrix.net/wp-twitter';
        public $sbar_glotpress      = 'http://dev.fabrix.net/translate/projects/wp-twitter';
        public $sbar_supportpage    = 'http://wordpress.org/extend/plugins/wp-twitter';
        public $sbar_paypalcode     = 'Z9SRNRLLDAFZJ';
        public $sbar_rss            = 'http://feeds.feedburner.com/fdxplugins/';

	function __construct() {
	  //--------------ALL
        load_plugin_textdomain( $this->hook, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); // Load plugin text domain
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );  // Register admin styles
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );// Register admin scripts
        add_action( 'admin_menu', array( $this, 'action_menu_pages' ) ); // Registers all WordPress admin menu items

      //-------------P1
        add_action('publish_post', array( $this, 'fdx1_post_now_published') ); // post
        add_action('publish_page', array( $this, 'fdx1_post_now_published2') );  // page
        add_filter('init', array( $this, 'fdx1_init') );

      //-------------P2
        add_action('init', array( $this, 'fdx_widgets_init'), 1 );
        add_filter('the_content', array( $this, 'filter_wp_twitter_fdx_profile') ) ;
        add_filter('the_content', array( $this, 'filter_wp_twitter_fdx_search') ) ;

      //-------------P3
        add_action('wp_head', array( $this, 'fdx_sharethis_script' ) );
        add_filter('the_content', array( $this, 'filter_wp_twitter_fdx_tweet_button_show') );
        add_filter('the_excerpt', array( $this, 'filter_wp_twitter_fdx_tweet_button_show') );
        add_action('wp_footer', array( $this, 'filter_wp_twitter_fdx_tweet_button_show') );

     //-------------P1
        global $fdx1_oauth;
        require_once( 'modules/p1/oauth-twitter.php' );
        $fdx1_oauth = new FDX1OAuth;

     //-------------P2
        require_once( 'modules/class-p2.php' );
        new FDX_Widget_profile;
        new FDX_Widget_search;

}//end construct

/*
 * Registers and enqueues admin-specific styles.
 */
public function register_admin_styles() {
             if ( isset( $_GET['page'] ) && strpos( $_GET['page'], $this->hook ) !== false ) {
             wp_enqueue_style('fdx-core', plugins_url( 'css/admin.css',__FILE__ ), array(), $this->pluginversion );
             //-------------P2
//---------------------------------------------
    }
}

/*
 * Registers and enqueues admin-specific JavaScript.
 */
public function register_admin_scripts() {
              if ( isset( $_GET['page'] ) && strpos( $_GET['page'], $this->hook ) !== false ) {
              wp_enqueue_script('dashboard');
              wp_enqueue_script('postbox');
           // wp_enqueue_script( 'media-upload' );
              wp_enqueue_script( 'admin-core', plugins_url( 'js/admin.js',__FILE__), array(), $this->pluginversion);
              wp_enqueue_script( 'admin-jscolor', plugins_url( 'js/jscolor.js',__FILE__), array(), $this->pluginversion);

              //-------------P2
//---------------------------------------------
    }
}

/*
 * Registers all WordPress admin menu items.
 */
function action_menu_pages() {
 			add_menu_page(
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Basic Settings and Connect', $this->hook ),
				__( $this->pluginname, $this->hook ),
				$this->accesslvl,
				$this->hook,
				array( $this, 'fdx_options_subpanel_p1' ),
				plugins_url( 'images/_16x16.png', __FILE__)
			);

				add_submenu_page(
					$this->hook,
					__( $this->pluginname, $this->hook ) . ' - ' . __( 'Widgets Settings', $this->hook ),
					__( 'Widgets', $this->hook ),
					$this->accesslvl,
                      $this->hook . '-'.$this->_p2,
					array( $this, 'fdx_options_subpanel_p2' )
				);

                add_submenu_page(
					$this->hook,
					__( $this->pluginname, $this->hook ) . ' - ' . __( 'Sharethis Button Integration', $this->hook ),
					__( 'Integration', $this->hook ),
					$this->accesslvl,
                    $this->hook . '-'.$this->_p3,
					array( $this, 'fdx_options_subpanel_p3' )
				);

				//Make the dashboard the first submenu item and the item to appear when clicking the parent.
				global $submenu;
				if ( isset( $submenu[$this->hook] ) ) {
					$submenu[$this->hook][0][0] = __( 'Settings', $this->hook );
				}

} 

/*********************************** P1 *****************************************
************* CONNECT TO TWITTER
********************************************************************************/

function fdx_options_subpanel_p1() {
include( dirname(__FILE__) . '/modules/inc-p1.php' );
}

/*
 * abc
 */
function fdx1_get_settings() {
	$settings = $this->p1_defaults;
	$wordpress_settings = get_option( 'fdx1_settings' );
	if ( $wordpress_settings ) {
		foreach( $wordpress_settings as $key => $value ) {
			$settings[ $key ] = $value;
		}
	}
	return $settings;
}



/*
 * abc
 */
function fdx1_update_post_settings() {
	if ( is_admin() ) {
		$settings = self::fdx1_get_settings();
	  	if ( isset($_POST['fdx1_update_settings']) ) {
			if ( isset($_POST['message']) ) {
				$message = $_POST['message'];
			} else {
				$message = '';
			}
          	if ( isset($_POST['message2']) ) {
				$message2 = $_POST['message2'];
			} else {
				$message2 = '';
			}
			if ( isset( $_POST['fdx1-url-type'] ) ) {
				$settings['url_type'] = $_POST['fdx1-url-type'];
			}
			if ( isset( $_POST['bitly-api-key'] ) ) {
				$settings['bitly-api-key'] = $_POST['bitly-api-key'];
			} else {
				$settings['bitly-api-key'] = '';
			}
			if ( isset( $_POST['bitly-user-name'] ) ) {
				$settings['bitly-user-name'] = $_POST['bitly-user-name'];
			} else {
				$settings['bitly-user-name'] = '';
			}
            if ( isset( $_POST['tweet_run_1'] ) ) {
				$settings['tweet_run_1'] = true;
			} else {
				$settings['tweet_run_1'] = false;
			}
            if ( isset( $_POST['tweet_run_2'] ) ) {
				$settings['tweet_run_2'] = true;
			} else {
				$settings['tweet_run_2'] = false;
			}
            if ( isset( $_POST['yourls-api-key'] ) ) {
				$settings['yourls-api-key'] = $_POST['yourls-api-key'];
			} else {
				$settings['yourls-api-key'] = '';
			}
			if ( isset( $_POST['yourls-user-name'] ) ) {
				$settings['yourls-user-name'] = $_POST['yourls-user-name'];
			} else {
				$settings['yourls-user-name'] = '';
			}
			if ( isset( $_POST['fdx1-reverse'] ) ) {
				$reverse = true;
			} else {
				$reverse = false;
			}
			$wt_tags = explode( ",", stripslashes( $_POST['fdx1-tags'] ) );
     		$new_tags = array();
			foreach ( $wt_tags as $tags ) {
				$clean_tag = strtolower( rtrim( ltrim( $tags ) ) );
				if ( strlen( $clean_tag ) ) {
					$new_tags[] = $clean_tag;
				}
			}
			$settings['tags'] = $new_tags;
			$settings['message'] = stripslashes( $message );
            $settings['message2'] = stripslashes( $message2 );
			$settings['reverse'] = $reverse;
			self::fdx1_save_settings( $settings );
		}
	 	elseif ( isset( $_POST['reset'] ) ) {
			update_option( 'fdx1_settings', false );
	    }
	}
}


/*
 * abc
 */
function fdx1_init() {
	self::fdx1_update_post_settings();
	if ( isset( $_GET['fdx1_oauth'] ) ) {
		global $fdx1_oauth;
		$settings = self::fdx1_get_settings();
		$result = $fdx1_oauth->get_access_token( $settings['oauth_request_token'], $settings['oauth_request_token_secret'], $_GET['oauth_verifier'] );
		if ( $result ) {
			$settings['oauth_access_token'] = $result['oauth_token'];
			$settings['oauth_access_token_secret'] = $result['oauth_token_secret'];
			$settings['user_id'] = $result['user_id'];
			self::fdx1_save_settings( $settings );
			header( 'Location: ' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page='. $this->hook);
			die;
		}
	} else if ( isset( $_GET['fdx1'] ) && $_GET['fdx1'] == 'deauthorize' ) {
		$settings = self::fdx1_get_settings();
		$settings['oauth_access_token'] = '';
		$settings['oauth_access_token_secret'] = '';
		$settings['user_id'] = '';
		self::fdx1_save_settings( $settings );
		header( 'Location: ' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page='. $this->hook);
		die;
	}
}
 

/*
 * abc
 */
function fdx1_save_settings( $settings ) {
	update_option( 'fdx1_settings', $settings );
}


/*
 * abc
 */
function fdx1_get_auth_url() {
	global $fdx1_oauth;
	$settings = self::fdx1_get_settings();
	$token = $fdx1_oauth->get_request_token();
	if ( $token ) {
		$settings['oauth_request_token'] = $token['oauth_token'];
		$settings['oauth_request_token_secret'] = $token['oauth_token_secret'];
		self::fdx1_save_settings( $settings );
		return $fdx1_oauth->get_auth_url( $token['oauth_token'] );
	}
}

/*
 * abc
 */
function fdx1_do_tweet( $post_id ) {
	$settings = self::fdx1_get_settings();
  	$message = self::fdx1_get_message( $post_id );
	if ( $message ) {
		$result_of_tweet = self::fdx1_twit_update_status( $message );
		if ( $result_of_tweet ) {
			return true;
		}
	}
	return false;
}

/*
 * abc
 */
function fdx1_do_tweet2( $post_id ) {
	$settings = self::fdx1_get_settings();
  	$message2 = self::fdx1_get_message2( $post_id );
	if ( $message2 ) {
		$result_of_tweet = self::fdx1_twit_update_status( $message2 );
		if ( $result_of_tweet ) {
			return true;
		}
	}
	return false;
}


/*
 * abc
 */
function fdx1_twit_update_status( $new_status ) {
	global $fdx1_oauth;
	$settings = self::fdx1_get_settings();
	if ( isset( $settings['oauth_access_token'] ) && isset( $settings['oauth_access_token_secret'] ) ) {
		return $fdx1_oauth->update_status( $settings['oauth_access_token'], $settings['oauth_access_token_secret'], $new_status );
	}
	return false;
}

/*
 * abc
 */
function fdx1_twit_has_tokens() {
	$settings = self::fdx1_get_settings();
	return ( $settings[ 'oauth_access_token' ] && $settings['oauth_access_token_secret'] );
}

/*
 * abc
 */
function fdx1_is_valid() {
	return self::fdx1_twit_has_tokens();
}


/*
 * abc
 */
function fdx1_make_tinyurl( $link, $update = true, $post_id ) {
	if ( strpos( $link, 'http://' ) === false ) {
		return $link;
	}
	$settings = self::fdx1_get_settings();
	$short_link = false;

if ( $settings['url_type'] == 'tinyurl' ) {  //ok
require_once(dirname(__FILE__) . '/modules/p1/tinyurl.php' );
$tinyurl = new FDX1TinyUrlShortener;
$short_link = $tinyurl->shorten( $link );
//yourls
} else if ( $settings['url_type'] == 'yourls' ) {
require_once(dirname(__FILE__) . '/modules/p1/yourls.php' );
$settings = self::fdx1_get_settings();
$yourls_link = new FDX1YourlsShortener($settings['yourls-api-key'], $settings['yourls-user-name'] );
$short_link = $yourls_link->shorten( $link );
//bitly
} else if ( $settings['url_type'] == 'bitly' ) {
require_once(dirname(__FILE__) . '/modules/p1/bitly.php' );
$settings = self::fdx1_get_settings();
$tinyurl = new FDX1BitlyShortener( $settings['bitly-user-name'], $settings['bitly-api-key'] );
$short_link = $tinyurl->shorten( $link );
//isgd
} else if ( $settings['url_type'] == 'isgd' ) {
require_once(dirname(__FILE__) . '/modules/p1/isgd.php' );
$isgd_link = new FDX1IsgdShortener;
$short_link = $isgd_link->shorten( $link );
//post_id
} else if ( $settings['url_type'] == 'post_id' ) {
$short_link = self::fdx1_post_id_url_base() . $post_id;
}
return $short_link;
}

/*
 * abc
 */
function fdx1_post_id_url_base() {
  	$url = get_bloginfo( 'url' ) . '/?p=';
	$url = str_replace( 'www.', '', $url );
	return $url;
}


/*
 * abc
 */
function fdx_hash_tags( $post_id ) {
	$hashtags = '';
	$max_tags = '3'; //Maximum number of tags to include
	$max_characters = '15'; //Maximum length in characters for included tags
	$max_characters = ( $max_characters == 0 || $max_characters == "" )?100:$max_characters + 1;
	if ($max_tags == 0 || $max_tags == "") { $max_tags = 100; }
		$tags = get_the_tags( $post_id );
		if ( $tags > 0 ) {
		$i = 1;
			foreach ( $tags as $value ) {
			$tag = $value->name;
			$replace = '_'; //Spaces in tags replaced with
			$strip = '0'; //Strip nonalphanumeric characters from tags no=0 | yes=1
			$search = "/[^\p{L}\p{N}\s]/u";
			if ($replace == "[ ]") { $replace = ""; }
			$tag = str_ireplace( " ",$replace,trim( $tag ) );
			if ($strip == '1') { $tag = preg_replace( $search, $replace, $tag ); }
			if ($replace == "" || !$replace) { $replace = "_"; }
				$newtag = "#$tag";
					if ( mb_strlen( $newtag ) > 2 && (mb_strlen( $newtag ) <= $max_characters) && ($i <= $max_tags) ) {
					$hashtags .= "$newtag ";
					$i++;
					}
			}
		}
	$hashtags = trim( $hashtags );
	if ( mb_strlen( $hashtags ) <= 1 ) { $hashtags = ""; }
	return $hashtags;
}

/*
 * abc
 */
function fdx1_get_message( $post_id ) {
	$my_post = get_post( $post_id );
	if ( $my_post ) {
		$settings = self::fdx1_get_settings();
		$message = $settings['message'];
		$message = str_replace( '[title]', $my_post->post_title, $message );
//--------------------------
        $message = str_replace( '[author]', get_the_author_meta( 'display_name',$my_post->post_author ), $message );
        $message = str_replace( '[tags]', self::fdx_hash_tags( $post_id ), $message );
        $categorys = get_the_category($post_id);
        $message = str_replace( '[cat]', $categorys[0]->name, $message );
//--------------------------
		$tinyurl = self::fdx1_make_tinyurl( get_permalink( $post_id ), true, $my_post->ID );
		if ( $tinyurl ) {
			$message = str_replace( '[link]', $tinyurl, $message );
			return $message;
		}
	}

	return false;
}

/*
 * abc
 */
function fdx1_get_message2( $post_id ) {
	$my_post = get_post( $post_id );
	if ( $my_post ) {
		$settings = self::fdx1_get_settings();
		$message2 = $settings['message2'];
		$message2 = str_replace( '[title]', $my_post->post_title, $message2 );
//--------------------------
        $message2 = str_replace( '[author]', get_the_author_meta( 'display_name',$my_post->post_author ), $message2 );
//--------------------------
		$tinyurl = self::fdx1_make_tinyurl( get_permalink( $post_id ), true, $my_post->ID );
		if ( $tinyurl ) {
			$message2 = str_replace( '[link]', $tinyurl, $message2 );
			return $message2;
		}
	}

	return false;
}

/*
 * abc
 */
function fdx1_post_now_published( $post_id, $force_tweet = false) {
	$settings = self::fdx1_get_settings();
	$wt_tags = $settings['tags'];
	$wt_reverse = $settings['reverse'];

    if ( !$force_tweet && !$settings['tweet_run_1'] ) {
 		return;
		}

    $can_tweet = true;
		// check tags
		if ( count( $wt_tags ) ) {
			// we have a tag or a category
			$new_taxonomy = array();
			$post_tags = get_the_tags();
			if ( $post_tags ) {
				foreach ( $post_tags as $some_tag ) {
					$new_taxonomy[] = strtolower( $some_tag->name );
				}
			}
			$post_categories = get_the_category();
			if ( $post_categories ) {
				foreach ( $post_categories as $some_category ) {
					$new_taxonomy[] = strtolower( $some_category->name );
				}
			}
			$category_hits = array_intersect( $wt_tags, $new_taxonomy );
			if ( $wt_reverse ) {
				$can_tweet = ( count( $category_hits ) == 0);
			} else {
				$can_tweet = ( count( $category_hits ) > 0);
			}
		}
		if ($can_tweet ) {
 			$result = self::fdx1_do_tweet( $post_id );
		}
}

/*
 * abc
 */
function fdx1_post_now_published2( $post_id, $force_tweet = false) {
$settings = self::fdx1_get_settings();
if ( !$force_tweet && !$settings['tweet_run_2'] ) {
 		return;
		}
$result = self::fdx1_do_tweet2( $post_id );
}


/*********************************** P2 *****************************************
************* WIDGETS
********************************************************************************/

function fdx_options_subpanel_p2() {
require_once dirname( __FILE__ ) . '/modules/inc-p2.php';
}

/*
 * abc
 */
function filter_wp_twitter_fdx_profile($content)
{
    if (strpos($content, "[--wp_twitter--]") !== FALSE)
    {
        $content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
        $content = str_replace('[--wp_twitter--]', self::wp_twitter_fdx_profile(), $content);
    }
    return $content;
}

/*
 * abc
 */
function filter_wp_twitter_fdx_search($content)
{
    if (strpos($content, "[--wp_twitter_search--]") !== FALSE)
    {
        $content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
        $content = str_replace('[--wp_twitter_search--]', self::wp_twitter_fdx_search(), $content);
    }
    return $content;
}

/*
 *  Profile Widget
 */
function wp_twitter_fdx_profile()
{
	$account = get_option('wp_twitter_fdx_username');
	$height = get_option('wp_twitter_fdx_height');
	$width = get_option('wp_twitter_fdx_width');
    $shell_bg = get_option('wp_twitter_fdx_shell_bg');
	$shell_text = get_option('wp_twitter_fdx_shell_text');
	$tweet_bg = get_option('wp_twitter_fdx_tweet_bg');
	$tweet_text = get_option('wp_twitter_fdx_tweet_text');
	$links = get_option('wp_twitter_fdx_links');

    $output = '<a class="twitter-timeline" width="'.$width.'" height="'.$height.'" data-theme="'.$shell_text.'" data-link-color="#'.$links.'" data-border-color="#'.$tweet_text.'" data-chrome="'.$tweet_bg.'" data-tweet-limit="'.$shell_bg.'" data-widget-id="'.$account.'"></a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
	$output_profile = $output;
	return $output_profile;
}

/*
 * Search Widget
 */
function wp_twitter_fdx_search()
{
	$search_title = get_option('wp_twitter_fdx_widget_search_title');
	$search_caption = get_option('wp_twitter_fdx_widget_search_caption');
	$account = get_option('wp_twitter_fdx_username');
	$search_sidebar_title = get_option('wp_twitter_fdx_search_widget_sidebar_title');
	$search_height = get_option('wp_twitter_fdx_search_height');
	$search_width = get_option('wp_twitter_fdx_search_width');
 	$search_shell_bg = get_option('wp_twitter_fdx_search_shell_bg');
	$search_tweet_bg = get_option('wp_twitter_fdx_search_tweet_bg');
	$search_tweet_text = get_option('wp_twitter_fdx_search_tweet_text');
	$search_links = get_option('wp_twitter_fdx_search_links');

    $output1 = '<a class="twitter-timeline" width="'.$search_width.'" height="'.$search_height.'" data-theme="'.$search_tweet_text.'" data-link-color="#'.$search_links.'" data-border-color="#'.$search_tweet_bg.'" data-chrome="'.$search_caption.'" data-tweet-limit="'.$search_shell_bg.'" data-widget-id="'.$search_title.'"></a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

    $output_search = $output1;

	return $output_search;
}



/*
 * abc
 */
function fdx_widgets_init() {
	register_widget('FDX_Widget_profile');
 	register_widget('FDX_Widget_search');
}

/********************************** P3 ******************************************
************* SHARETHIS BUTTON INTEGRATION
********************************************************************************/
function fdx_options_subpanel_p3() {
require_once dirname( __FILE__ ) . '/modules/inc-p3.php';
}

/*
 * abc
 */
function fdx_sharethis_script() {
    $tweet_btn_twt_username = get_option('wp_twitter_fdx_tweet_button_twitter_username');
    $tweet_btn_display_single = get_option('wp_twitter_fdx_tweet_button_display_single');
	$tweet_btn_display_page = get_option('wp_twitter_fdx_tweet_button_display_page');
	$tweet_btn_display_home = get_option('wp_twitter_fdx_tweet_button_display_home');

    $tweet_btn_services = get_option('wp_twitter_fdx_services');   //New

     $tweet_btn_display_copynshare = get_option('wp_twitter_copynshare');

	$tweet_btn_display_arquive = get_option('wp_twitter_fdx_tweet_button_display_arquive');
    $tweet_btn_choose = get_option('wp_twitter_fdx_tweet_button_choose');
    $tweet_btn_place = get_option('wp_twitter_fdx_tweet_button_place');
    $tweet_btn_logotop = get_option('wp_twitter_fdx_logo_top');


  if ($tweet_btn_choose == "multi_post")
    {
     $widget_style = 'true';
    }

 if ($tweet_btn_choose == "direct_post")
    {
     $widget_style = 'false';
    }


//adiciona se ativado
  if (is_single() && $tweet_btn_display_single == 1 || is_page() && $tweet_btn_display_page == 1 || is_home() && $tweet_btn_display_home == 1 || is_archive() && $tweet_btn_display_arquive == 1 || is_attachment() && $tweet_btn_display_arquive == 1 ) {

     if ($tweet_btn_place == "before" || $tweet_btn_place == "after" )
    {
       echo "<!-- WP Twitter - http://fabrix.net/wp-twitter/  -->\n";
       echo "<script type='text/javascript'>var switchTo5x=". $widget_style .";</script>\n";
       echo "<script type='text/javascript' src='http://w.sharethis.com/button/buttons.js'></script>\n";

       if ($tweet_btn_display_copynshare == 1) {
       echo "<script type='text/javascript'>stLight.options({publisher: '" . $tweet_btn_twt_username . "', doNotHash: false, doNotCopy: false, hashAddressBar: true}); </script>\n";
       } else {
       echo "<script type='text/javascript'>stLight.options({publisher: '" . $tweet_btn_twt_username . "'}); </script>\n";
       }

     } elseif ($tweet_btn_place == "floatside" || $tweet_btn_place == "fixedbottom"){
        echo "<!-- WP Twitter - http://fabrix.net/wp-twitter/  -->\n";
        echo "<script type='text/javascript'>var switchTo5x=". $widget_style .";</script>\n";
        echo "<script type='text/javascript' src='http://w.sharethis.com/button/buttons.js'></script>\n";
        echo "<script type='text/javascript' src='http://s.sharethis.com/loader.js'></script>\n";

     } elseif ($tweet_btn_place == "fixedtop"){
        echo "<!-- WP Twitter - http://fabrix.net/wp-twitter/  -->\n";
        echo "<style type='text/css'>.stpulldown-gradient{background: #E1E1E1;background: -moz-linear-gradient(top, #E1E1E1 0%, #A7A7A7 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#E1E1E1), color-stop(100%,#A7A7A7)); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#E1E1E1', endColorstr='#A7A7A7',GradientType=0 ); background: -o-linear-gradient(top, #E1E1E1 0%,#A7A7A7 100%); color: #636363;}#stpulldown .stpulldown-logo {height: 40px;width: 300px;margin-left: 20px;margin-top: 5px;background:url('".$tweet_btn_logotop."') no-repeat;}</style>\n";
        echo "<script type='text/javascript'>var switchTo5x=". $widget_style .";</script>\n";
        echo "<script type='text/javascript' src='http://w.sharethis.com/button/buttons.js'></script>\n";
        echo "<script type='text/javascript' src='http://s.sharethis.com/loader.js'></script>\n";

     } elseif ($tweet_btn_place == "sharenow") {
        echo "<!-- WP Twitter - http://fabrix.net/wp-twitter/  -->\n";
        echo "<script type='text/javascript' src='http://w.sharethis.com/button/buttons.js'></script>\n";
        echo "<script type='text/javascript' src='http://s.sharethis.com/loader.js'></script>\n";

     } elseif ($tweet_btn_place == "shareegg") {
       echo "<!-- WP Twitter - http://fabrix.net/wp-twitter/  -->\n";
       echo "<script type='text/javascript' src='http://w.sharethis.com/gallery/shareegg/shareegg.js'></script>\n";
       echo "<script type='text/javascript' src='http://w.sharethis.com/button/buttons.js'></script>\n";

    if ($tweet_btn_display_copynshare == 1) {
    echo "<script type='text/javascript'>stLight.options({publisher: '" . $tweet_btn_twt_username . "', doNotHash: false, doNotCopy: false, hashAddressBar: true, onhover:false});</script>\n";
    } else {
      echo "<script type='text/javascript'>stLight.options({publisher: '" . $tweet_btn_twt_username . "', onhover:false}); </script>\n";
      }
       echo "<link media='screen' type='text/css' rel='stylesheet' href='http://w.sharethis.com/gallery/shareegg/shareegg.css'></link>\n";
     }


      }
}

/*
 * abc
 */
function filter_wp_twitter_fdx_tweet_button_show($related_content)
{

    $tweet_btn_display_single = get_option('wp_twitter_fdx_tweet_button_display_single');
	$tweet_btn_display_page = get_option('wp_twitter_fdx_tweet_button_display_page');
	$tweet_btn_display_home = get_option('wp_twitter_fdx_tweet_button_display_home');
	$tweet_btn_display_arquive = get_option('wp_twitter_fdx_tweet_button_display_arquive');

    $tweet_btn_display_copynshare = get_option('wp_twitter_copynshare');

	$tweet_btn_place = get_option('wp_twitter_fdx_tweet_button_place');
	$tweet_btn_style = get_option('wp_twitter_fdx_tweet_button_style');
    $tweet_btn_style2 = get_option('wp_twitter_fdx_tweet_button_style2');
    $tweet_btn_style3 = get_option('wp_twitter_fdx_tweet_button_style3');
	$tweet_btn_float = get_option('wp_twitter_fdx_tweet_button_container');
	$tweet_btn_twt_username = get_option('wp_twitter_fdx_tweet_button_twitter_username');

    $tweet_btn_services = get_option('wp_twitter_fdx_services');


if ($tweet_btn_style == "large_buton")
    {
       $sh_services = explode(',' , $tweet_btn_services);
       foreach($sh_services as $key) {
       $key = preg_replace("/\s/", "", $key);
       @$final_url.='<span class="st_'.$key.'_large" displayText="'.$key.'"></span>';
    }
    $final_url = '<div style="'.$tweet_btn_float.'">' . $final_url . '</div>';
}

if ($tweet_btn_style == "small_buton")
      {
       $sh_services = explode(',' , $tweet_btn_services);
       foreach($sh_services as $key) {
       $key = preg_replace("/\s/", "", $key);
       @$final_url.='<span class="st_'.$key.'"></span>';
       }
    $final_url = '<div style="'.$tweet_btn_float.'">' . $final_url . '</div>';
    }

if ($tweet_btn_style == "h_count_buton")
      {
       $sh_services = explode(',' , $tweet_btn_services);
       foreach($sh_services as $key) {
       $key = preg_replace("/\s/", "", $key);
       @$final_url.='<span class="st_'.$key.'_hcount" displayText="'.$key.'"></span>';
      }
      $final_url = '<div style="'.$tweet_btn_float.'">' . $final_url . '</div>';
   }

if ($tweet_btn_style == "v_count_buton")
       {
       $sh_services = explode(',' , $tweet_btn_services);
       foreach($sh_services as $key) {
       $key = preg_replace("/\s/", "", $key);
       @$final_url.='<span class="st_'.$key.'_vcount" displayText="'.$key.'"></span>';
      }
    $final_url = '<div style="'.$tweet_btn_float.'">' . $final_url . '</div>';
    }
/************************** SHAREEGG *********************************/
if ($tweet_btn_place == "shareegg")
    {
     if (is_single() || is_page() ){
       $sh_services = explode(',' , $tweet_btn_services);
       foreach($sh_services as $key) {
       $key = preg_replace("/\s/", "", $key);
       @$allopt.= '"'.$key.'",';
       }
    $final_url = '<script type="text/javascript">stlib.shareEgg.createEgg("shareThisShareEgg", ['.$allopt.'], {title:"ShareThis Rocks!!!",url:"http://www.sharethis.com",theme:"shareegg"});</script>';
    $final_url = '<div id="shareThisShareEgg" class="shareEgg">' . $final_url . '</div>';
     }else {
      $final_url = '';
     }

   }

//copynshare
if ($tweet_btn_display_copynshare == 1) {
$display_copynshare = ", doNotHash: false, doNotCopy: false, hashAddressBar: true";
} else {
$display_copynshare = "";
}


// ***********************************************************************************
if (is_single() && $tweet_btn_display_single == 1 || is_page() && $tweet_btn_display_page == 1 || is_home() && $tweet_btn_display_home == 1 || is_archive() && $tweet_btn_display_arquive == 1 )
{
//------------------------------------------------------------
if ($tweet_btn_place == "before")
{
$related_content =  $final_url . $related_content;
}
//------------------------------------------------------------
if ($tweet_btn_place == "after")
{
$related_content =  $related_content . $final_url;
}
//------------------------------------------------------------
if ($tweet_btn_place == "floatside")
{
      $sh_services = explode(',' , $tweet_btn_services);
       foreach($sh_services as $key) {
       $key = preg_replace("/\s/", "", $key);
       @$allopt.= '"'.$key.'",';
       }
    if ($tweet_btn_style2 == "floatside_left")
       {
       echo '<script type="text/javascript">stLight.options({publisher: "'.$tweet_btn_twt_username.'"'.$display_copynshare.'});</script><script>var options={ "publisher": "'.$tweet_btn_twt_username.'", "position": "left", "ad": { "visible": false, "openDelay": 5, "closeDelay": 0}, "chicklets": { "items": ['.$allopt.']}}; var st_hover_widget = new sharethis.widgets.hoverbuttons(options);</script>';
       }
    if ($tweet_btn_style2 == "floatside_right")
       {
       echo '<script type="text/javascript">stLight.options({publisher: "'.$tweet_btn_twt_username.'"'.$display_copynshare.'});</script><script>var options={ "publisher": "'.$tweet_btn_twt_username.'", "position": "right", "ad": { "visible": false, "openDelay": 5, "closeDelay": 0}, "chicklets": { "items": ['.$allopt.']}}; var st_hover_widget = new sharethis.widgets.hoverbuttons(options);</script>';
       }
}
//------------------------------------------------------------
if ($tweet_btn_place == "fixedtop"){
       $sh_services = explode(',' , $tweet_btn_services);
       foreach($sh_services as $key) {
       $key = preg_replace("/\s/", "", $key);
       @$allopt.= '"'.$key.'",';
       }
echo '<script type="text/javascript">stLight.options({publisher: "'.$tweet_btn_twt_username.'"'.$display_copynshare.'});</script><script>var options={ "publisher": "'.$tweet_btn_twt_username.'", "scrollpx": 10, "ad": { "visible": false}, "chicklets": { "items": ['.$allopt.']}}; var st_pulldown_widget = new sharethis.widgets.pulldownbar(options);</script>';
}
//------------------------------------------------------------
if ($tweet_btn_place == "fixedbottom")
{
       $sh_services = explode(',' , $tweet_btn_services);
       foreach($sh_services as $key) {
       $key = preg_replace("/\s/", "", $key);
       @$allopt.= '"'.$key.'",';
       }
echo '<script type="text/javascript">stLight.options({publisher: "'.$tweet_btn_twt_username.'"'.$display_copynshare.'});</script>';
echo '<script>var options={ "publisher": "'.$tweet_btn_twt_username.'", "logo": { "visible": false, "url": "", "img": "http://sd.sharethis.com/disc/_inc/images/demo_logo.png", "height": 45}, "ad": { "visible": false, "openDelay": "5", "closeDelay": "0"}, "livestream": { "domain": "", "type": "sharethis", "customColors": { "widgetBackgroundColor": "#FFFFFF", "articleLinkColor": "#006fbb"}}, "ticker": { "visible": false, "domain": "", "title": "", "type": "sharethis", "customColors": { "widgetBackgroundColor": "#a0adc7", "articleLinkColor": "#00487f"}}, "facebook": { "visible": false, "profile": "sharethis"}, "fblike": { "visible": false, "url": ""}, "twitter": { "visible": false, "user": "sharethis"}, "twfollow": { "visible": false, "url": "http://www.twitter.com/sharethis"}, "custom": [{ "visible": false, "title": "Custom 1", "url": "", "img": "", "popup": false, "popupCustom": { "width": 300, "height": 250}}, { "visible": false, "title": "Custom 2", "url": "", "img": "", "popup": false, "popupCustom": { "width": 300, "height": 250}}, { "visible": false, "title": "Custom 3", "url": "", "img": "", "popup": false, "popupCustom": { "width": 300, "height": 250}}], "shadow": "gloss", "background": "#c2c2c2", "color": "#555555", "arrowStyle": "light", "chicklets": { "items": ['.$allopt.']}};';
echo 'var st_bar_widget = new sharethis.widgets.sharebar(options);</script>';
}
//------------------------------------------------------------
if ($tweet_btn_place == "sharenow")
{
echo '<script type="text/javascript">stLight.options({publisher: "'.$tweet_btn_twt_username.'"'.$display_copynshare.'});</script><script>var options={ "service": "facebook", "timer": { "countdown": 30, "interval": 10, "enable": false}, "frictionlessShare": true, "style": "'.$tweet_btn_style3.'", "publisher": "'.$tweet_btn_twt_username.'"};var st_service_widget = new sharethis.widgets.serviceWidget(options);</script>';
}
//------------------------------------------------------------
if ($tweet_btn_place == "shareegg")
{
$related_content =  $related_content . $final_url;
}
//------------------------------------------------------------
}

// $post = $p;
return $related_content;

}//end add content



}//++++++++++++++++++++++++++++end ALL
$wp_twitter = new WP_Twitter();