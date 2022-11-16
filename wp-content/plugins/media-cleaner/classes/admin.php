<?php

class Meow_WPMC_Admin extends MeowCommon_Admin {

  private $core = null;

  public function __construct( $core ) {
    $this->core = $core;
    parent::__construct( WPMC_PREFIX, WPMC_ENTRY, WPMC_DOMAIN, class_exists( 'MeowPro_WPMC_Core' ) );
    add_action( 'admin_menu', array( $this, 'app_menu' ) );

    // Load the scripts only if they are needed by the current screen
    $page = isset( $_GET["page"] ) ? sanitize_text_field( $_GET["page"] ) : null;
    $is_wpmc_screen = in_array( $page, [ 'wpmc_dashboard', 'wpmc_settings' ] );
    $is_meowapps_dashboard = $page === 'meowapps-main-menu';
    if ( $is_meowapps_dashboard || $is_wpmc_screen ) {
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }
  }

  function admin_enqueue_scripts() {

    // Load the scripts
    $physical_file = WPMC_PATH . '/app/index.js';
    $cache_buster = file_exists( $physical_file ) ? filemtime( $physical_file ) : WPMC_VERSION;
    wp_register_script( 'wpmc_media_cleaner-vendor', WPMC_URL . 'app/vendor.js',
      ['wp-element', 'wp-i18n'], $cache_buster
    );
    wp_register_script( 'wpmc_media_cleaner', WPMC_URL . 'app/index.js',
      ['wpmc_media_cleaner-vendor', 'wp-i18n'], $cache_buster
    );
    if ( function_exists( 'wp_set_script_translations' ) ) {
      wp_set_script_translations( 'wpmc_media_cleaner', 'media-cleaner' );
    }
    wp_enqueue_script('wpmc_media_cleaner' );

    // Load the fonts
    wp_register_style( 'meow-neko-ui-lato-font', '//fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap');
    wp_enqueue_style( 'meow-neko-ui-lato-font' );

    // Localize and options
    wp_localize_script( 'wpmc_media_cleaner', 'wpmc_media_cleaner', [
      'api_url' => rest_url( 'media-cleaner/v1' ),
      'rest_url' => rest_url(),
      'plugin_url' => WPMC_URL,
      'prefix' => WPMC_PREFIX,
      'domain' => WPMC_DOMAIN,
      'is_pro' => class_exists( 'MeowPro_WPMC_Core' ),
      'is_registered' => !!$this->is_registered(),
      'rest_nonce' => wp_create_nonce( 'wp_rest' ),
      'options' => $this->core->get_all_options()
    ] );
  }

  function app_menu() {
    if ( !$this->core->can_access_settings() ) {
      return;
    }
    add_submenu_page( 'meowapps-main-menu', 'Media Cleaner', 'Media Cleaner', 'read', 'wpmc_settings', 
      array( $this, 'admin_settings' )
    );
  }

  public function admin_settings() {
    if ( !$this->core->can_access_settings() ) {
      return;
    }
    echo '<div id="wpmc-admin-settings"></div>';
  }

  

}

?>
