<?php

class Meow_WPMC_Support {     
  
  static function get_natives() {
    $natives = array();

   	if ( class_exists( 'WooCommerce' ) )
      array_push( $natives, 'WooCommerce' );

		if ( class_exists( 'Attachments' ) )
      array_push( $natives, 'Attachments' );

		if ( class_exists( 'MetaSliderPlugin' ) || class_exists( 'MetaSliderPro' ) )  
      array_push( $natives, 'Meta Slider' );

		if ( function_exists( 'mc_show_sidebar' ) )
      array_push( $natives, 'My Calendar' );

		if ( class_exists( 'Mega_Menu' ) )
     array_push( $natives, 'Mega Menu' );

		if ( class_exists( 'WPSEO_Options' ) ) 
      array_push( $natives, 'Yoast SEO' );

    if ( class_exists( 'Meow_MGL_Core' ) )
      array_push( $natives, 'Meow Gallery' );

    return $natives;
  }
        
  static function get_issues() {
    $unsupported = array();

    if ( class_exists( 'ACF' ) )
      array_push( $unsupported, 'ACF' );

    if ( function_exists( 'acfw_globals' ) )
      array_push( $unsupported, 'ACF Widgets' );

    if ( function_exists( '_et_core_find_latest' ) )
      array_push( $unsupported, 'Divi' );

    if ( class_exists( 'Vc_Manager' ) )
      array_push( $unsupported, 'Visual Composer' );

    if ( defined( 'FUSION_BUILDER_VERSION' ) || defined( 'FUSION_CORE_VERSION' ) || has_action( 'avada_author_info' ) )
      array_push( $unsupported, 'Fusion Builder' );

    if ( defined( 'ELEMENTOR_VERSION' ) )
      array_push( $unsupported, 'Elementor' );

    if ( class_exists( 'FLBuilderModel' ) )
      array_push( $unsupported, 'Beaver Builder' );

    if ( class_exists( 'Oxygen_VSB_Dynamic_Shortcodes' ) )
      array_push( $unsupported, 'Oxygen Builder' );

    if ( class_exists( 'Brizy_Editor_Post' ) )
      array_push( $unsupported, 'Brizy Editor' );

    if ( function_exists( 'amd_zlrecipe_convert_to_recipe' ) )
      array_push( $unsupported, 'ZipList Recipe' );

    if ( class_exists( 'UberMenu' ) )
      array_push( $unsupported, 'UberMenu' );

    if ( class_exists( 'X_Bootstrap' ) )
      array_push( $unsupported, 'Theme X' );

    if ( class_exists( 'Easy_Real_Estate' ) )
      array_push( $unsupported, 'Easy Real Estate' );

    if ( class_exists( 'SiteOrigin_Panels' ) )
      array_push( $unsupported, 'SiteOrigin PageBuilder' );

    if ( defined( 'TASTY_PINS_PLUGIN_FILE' ) )
      array_push( $unsupported, 'Tasty Pins' );

    if ( class_exists( 'WCFMmp' ) )
      array_push( $unsupported, 'WCFM Marketplace' );

    if ( class_exists( 'RevSliderFront' ) )
      array_push( $unsupported, 'Revolution Slider' );

    if ( defined( 'WPESTATE_PLUGIN_URL' ) )
      array_push( $unsupported, 'WP Residence' );

    if ( defined( 'AV_FRAMEWORK_VERSION' ) )
      array_push( $unsupported, 'Avia Framework' );

    if ( class_exists( 'FAT_Portfolio' ) )
      array_push( $unsupported, 'FAT Portfolio' );

    if ( class_exists( 'YIKES_Custom_Product_Tabs' ) )
      array_push( $unsupported, 'Yikes Custom Product Tabs' );

    if ( function_exists( 'drts' ) )
      array_push( $unsupported, 'Directories' );

    if ( class_exists( 'ImageMapPro' ) )
      array_push( $unsupported, 'Image Map Pro' );

    if ( class_exists( 'YOOtheme\\Builder' ) ) {
      array_push( $unsupported, 'YooTheme Builder' );
    }

    if ( class_exists( 'geodirectory' ) ) {
      array_push( $unsupported, 'GeoDirectory' );
    }

    if ( class_exists( 'Modula' ) ) {
      array_push( $unsupported, 'Modula Gallery' );
    }

		if ( class_exists( 'JustifiedImageGrid' ) ) {
			array_push( $unsupported, 'Justified Image Grid' );
    }

    if ( class_exists( 'Advanced_Ads' ) ) {
			array_push( $unsupported, 'Advanced Ads' );
    }

    if ( function_exists( 'smart_slider_3_plugins_loaded' ) ) {
			array_push( $unsupported, 'Smart Slider' );
    }

    if ( class_exists( 'w2dc_plugin' ) ) {
			array_push( $unsupported, 'WebDirectory' );
    }

    if ( class_exists( 'ElfsightSliderPlugin' ) ) {
      array_push( $unsupported, 'Elfsight Slider' );
    }

		if ( class_exists( '\Nimble\CZR_Fmk_Base' ) ) {
      array_push( $unsupported, 'Nimble Builder' );
    }

		if ( class_exists( 'fwds3dcar' ) ) {
      array_push( $unsupported, 'Simple 3D Carousel' );
    }

    if ( class_exists( 'Jet_Engine' ) ) {
      array_push( $unsupported, 'Jet Engine' );
    }

    if ( class_exists( 'Social_Warfare' ) ) {
      array_push( $unsupported, 'Social Warfare' );
    }

		if ( class_exists( 'WP_Job_Manager' ) ) {
			array_push( $unsupported, 'WP Job Manager' );
    }

    if ( class_exists( 'WpdiscuzCore' ) ) {
			array_push( $unsupported, 'wpDiscuz' );
    }

		if ( class_exists( 'Cornerstone_Plugin' ) ) {
			array_push( $unsupported, 'Cornerstone' );
		}

    if ( class_exists( 'WP_DLM' ) ) {
      array_push( $unsupported, 'Download Monitor' );
    }

    if ( class_exists( 'CMBusinessDirectory' ) ) {
      array_push( $unsupported, 'CM Business Directory' );
    }

    if ( class_exists( 'SunshineCart' ) ) {
      array_push( $unsupported, 'Sunshine Photo Cart' );
    }

		if ( defined( 'WOODMART_CORE_VERSION' ) ) {
      array_push( $unsupported, 'Woodmart Theme' );
		}

    if ( class_exists( 'BWG' ) ) {
      array_push( $unsupported, 'Photo Gallery (10Web)' );
		}

    if ( class_exists( 'Google\Web_Stories\Plugin' ) ) {
      array_push( $unsupported, 'Web Stories' );
    }

    if ( function_exists( 'rwmb_get_object_fields' ) ) {
      array_push( $unsupported, 'Metabox' );
		}

    if ( defined( 'URIS_PLUGIN_URL' ) ) {
      array_push( $unsupported, 'Ultimate Responsive Image Slider' );
    }

		if ( defined( 'PRESTO_PLAYER_PLUGIN_URL' ) ) {
      array_push( $unsupported, 'Presto Player' );
		}

		if ( defined( 'GG_VER' ) ) {
			array_push( $unsupported, 'Global Gallery' );
		}

		if ( defined( 'LANA_DOWNLOADS_MANAGER_VERSION' ) ) {
      array_push( $unsupported, 'Lana Downloads Manager' );
		}

    if ( defined( 'POWERPRESS_VERSION' ) ) {
			array_push( $unsupported, 'Powerpress' );
		}

    if ( class_exists( 'Connections_Directory' ) ) {
      array_push( $unsupported, 'Connections Business Directory' );
    }

		if ( defined( 'WONDERPLUGIN_3DCAROUSEL_VERSION' ) ) {
      array_push( $unsupported, 'WonderPlugin 3D Carousel' );
		}

		if ( defined( 'UNCODE_CORE_FILE' ) ) {
			array_push( $unsupported, 'Uncode' );
		}

    if ( defined( 'MAILPOET_MINIMUM_REQUIRED_WP_VERSION' ) ) {
      array_push( $unsupported, 'Mailpoet' );
		}

    if ( defined( 'ACADEMY_VERSION' ) ) {
      array_push( $unsupported, 'Academy LMS' );
    }

    if ( defined( 'BREAKDANCE_PLUGIN_URL' ) ) {
      array_push( $unsupported, 'Breakdance Builder' );
		}

		if ( defined( 'BRICKS_VERSION' ) ) {
      array_push( $unsupported, 'Bricks Builder' );
		}

    // W3 Total Cache
    if ( defined( 'W3TC_VERSION' ) ) {
      array_push( $unsupported, 'W3 Total Cache' );
    }

    // Spectra
		if ( defined( 'UAGB_PLUGIN_SHORT_NAME' ) ) {
      array_push( $unsupported, 'Spectra' );
		}

    // Foo Gallery
		if ( defined( 'FOOGALLERY_VERSION' ) ) {
			array_push( $unsupported, 'Foo Gallery' );
		}

    // Tutor LMS
    if ( defined( 'TUTOR_VERSION' ) ) {
      array_push( $unsupported, 'Tutor LMS' );
    }

    // Houzez
		if ( defined( 'HOUZEZ_THEME_VERSION' ) ) {
      array_push( $unsupported, 'Houzez' );
		}

    // Kadence Blocks
		if ( defined( 'KADENCE_BLOCKS_VERSION' ) ) {
			array_push( $unsupported, 'Kadence Blocks' );
		}

    // FLuent Forms
		if ( defined( 'FLUENTFORM_VERSION' ) ) {
      array_push( $unsupported, 'Fluent Forms' );
    }

    return $unsupported;
  }
}
?>