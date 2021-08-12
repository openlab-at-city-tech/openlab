<?php
/**
 * Plugin Name: Dropr – Dropbox Plugin for WordPress
 * Plugin URI: http://awsm.in/dropr-documentation/
 * Description: Dropr lets you to add files from your Dropbox account straight to your WordPress website. Securely and safely.
 * Version: 1.3.0
 * Author: Awsm Innovations
 * Author URI: http://awsm.in
 * License: GPL V3
 * Text Domain: dropr
 * Domain Path: /language
 *
 * @package dropr
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'AWSM_DROPR_VERSION' ) ) {
	define( 'AWSM_DROPR_VERSION', '1.3.0' );
}

require_once dirname( __FILE__ ) . '/inc/functions.php';
require_once dirname( __FILE__ ) . '/lib/dropr-download.php';
require_once dirname( __FILE__ ) . '/lib/dropr-media.php';

/**
 * Dropbox Picker Main Class.
 *
 * @link http://awsm.in/awsm-dropbox
 *
 * @since 1.1
 *
 */
class Dropr_main {
	private static $instance = null;
	private $plugin_path;
	private $plugin_url;
	private $plugin_base;
	private $plugin_file;
	private $plugin_version;
	private $settings_slug;
	private $text_domain = 'dropr';
	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since    1.0.0
	 */
	public static function get_instance() {
		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
	 *
	 * @since    1.0.0
	 */
	private function __construct() {
		$this->plugin_path    = plugin_dir_path( __FILE__ );
		$this->plugin_url     = plugin_dir_url( __FILE__ );
		$this->plugin_base    = dirname( plugin_basename( __FILE__ ) );
		$this->plugin_file    = __FILE__;
		$this->settings_slug  = 'dropr-settings';
		$this->plugin_version = AWSM_DROPR_VERSION;
		// Language Localization
		load_plugin_textdomain( 'dropr', false, $this->plugin_base . '/language' );
		add_shortcode( 'docembed', array( $this, 'embed_shortcode' ) );
		add_action( 'wp_head', array( $this, 'buttons_css' ) );
		add_action( 'admin_head-post.php', array( $this, 'buttons_css' ) );
		add_action( 'admin_head-post-new.php', array( $this, 'buttons_css' ) );
		register_activation_hook( $this->plugin_file, array( $this, 'reset_default' ) );

		add_action( 'wp_loaded', array( $this, 'register_scripts' ) );
		add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), 10, 3 );
		add_filter( 'ajax_query_attachments_args', array( $this, 'ajax_query_attachments_args' ) );

		$this->adminfunctions();

		Dropr_media::getInstance( $this->plugin_path, $this->plugin_version );
		$apikey = get_option( 'wpdropbox-apikey' );
		if ( $apikey ) {
			$dropr_download = DroprDownload::getInstance( $this->plugin_path, $this->plugin_version );
			$dropr_download->addMediasupport();
		}
		// Initialize block
		include_once $this->plugin_path . 'blocks/dropr.php';
	}

	/**
	 * Initiate admin functions.
	 *
	 * @since 1.0
	 */
	public function adminfunctions() {
		if ( is_admin() ) {
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'settingslink' ) );
			add_action( 'admin_footer', array( $this, 'dropr_popup' ) );
			add_action( 'media_buttons', array( $this, 'dropr_button' ), 15 );
			add_action( 'wp_enqueue_media', array( $this, 'dropr_media' ) );

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}
	}

	/**
	 * Dropr media button
	 */
	public function dropr_button( $args = array() ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$target = is_string( $args ) ? $args : 'content';
		$args   = wp_parse_args(
			$args,
			array(
				'target'    => $target,
				'text'      => __( 'Add From Dropbox', 'dropr' ),
				'class'     => 'awsm-dropr button',
				'icon'      => plugins_url( 'images/dropr-icon.png', __FILE__ ),
				'echo'      => true,
				'id'        => 'awsm-dropr',
				'shortcode' => false,
			)
		);
		if ( $args['icon'] ) {
			$args['icon'] = '<img src="' . esc_url( $args['icon'] ) . '" /> ';
		}
		$button = '<a href="javascript:void(0);" id="' . esc_attr( $args['id'] ) . '" class="' . esc_attr( $args['class'] ) . '" title="' . esc_attr( $args['text'] ) . '" data-target="' . esc_attr( $args['target'] ) . '" >' . $args['icon'] . $args['text'] . '</a>';
		if ( $args['echo'] ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $button;
		}
		return $button;
	}

	/**
	 * Register scripts for both back-end and front-end use.
	 */
	public function register_scripts() {
		wp_register_script( 'dropr-core', '//www.dropbox.com/static/api/2/dropins.js', array(), '2', true );
	}

	/**
	 * Enqueues media button style and script
	 */
	public function dropr_media() {
		// Add media library templates.
		add_action( 'admin_footer', array( $this, 'media_library_templates' ) );
		add_action( 'wp_footer', array( $this, 'media_library_templates' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'media_library_templates' ) );

		wp_enqueue_script( 'magnific-popup', plugins_url( 'js/magnific-popup.js', $this->plugin_file ), array( 'jquery' ), $this->plugin_version, true );
		wp_enqueue_style( 'magnific-popup', plugins_url( 'css/magnific-popup.css', $this->plugin_file ), false, $this->plugin_version, 'all' );
		wp_enqueue_style( 'awsmdrop-embed', plugins_url( 'css/embed.css', $this->plugin_file ), array( 'magnific-popup' ), $this->plugin_version, 'all' );

		$script_deps = array( 'jquery', 'dropr-core', 'media-views' );
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
				$script_deps = array_merge( $script_deps, array( 'wp-blocks', 'wp-data' ) );
			}
		}
		wp_enqueue_script( 'dropr-main', plugins_url( 'js/dropr.js', $this->plugin_file ), $script_deps, $this->plugin_version, true );

		$options         = dropr_getoptions();
		$generic_options = dropr_get_generic_options();
		$max_upload_size = wp_max_upload_size();
		$jsoptions       = array(
			'wpdropboxapikey' => get_option( 'wpdropbox-apikey', '' ),
			'dropr_nonce'     => wp_create_nonce( 'dropr-featured' ),
			'linktext'        => $options['btntxt'],
			'ajaxurl'         => admin_url( 'admin-ajax.php' ),
			'plugin_url'      => esc_url( $this->plugin_url ),
			'max_upload_size' => $max_upload_size ? $max_upload_size : 0,
			'extensions'      => self::get_allowed_extensions(),
			'storage'         => array(
				'media_library' => $generic_options['media_library_storage'],
				'featured_img'  => $generic_options['featured_image_storage'],
			),
		);
		wp_localize_script( 'dropr-main', 'dropr_options', $jsoptions );
	}

	/**
	 * Get allowed extensions array.
	 *
	 * @return array
	 */
	public static function get_allowed_extensions() {
		$extensions = array_keys( get_allowed_mime_types() );
		$extns_list = '.' . implode( ',.', $extensions );
		$extns_list = str_replace( '|', ',.', $extns_list );
		$extensions = explode( ',', $extns_list );
		/**
		 * Customize the allowed extensions for Media Library.
		 *
		 * @since 1.3.0
		 *
		 * @param array $extensions Allowed extensions array.
		 */
		return apply_filters( 'awsm_dropr_allowed_extensions', $extensions );
	}

	/**
	 * Customize the script tag of an enqueued script.
	 *
	 * @param string $tag The script tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 * @param string $src The script's source URL.
	 * @return string
	 */
	public function script_loader_tag( $tag, $handle, $src ) {
		if ( $handle === 'dropr-core' ) {
			$app_key = get_option( 'wpdropbox-apikey' );
			if ( ! empty( $app_key ) ) {
				// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
				$tag = sprintf( '<script type="text/javascript" src="%1$s" id="dropboxjs" data-app-key="%2$s"></script>', esc_url( $src ), esc_attr( $app_key ) );
			}
		}
		return $tag;
	}

	/**
	 * Media Library View templates.
	 */
	public function media_library_templates() {
		$generic_options  = dropr_get_generic_options();
		$is_local_storage = $generic_options['media_library_storage'] === 'local' ? true : false;
		$settings_link    = sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'options-general.php?page=' . $this->settings_slug ) ), esc_html__( 'Settings', 'dropr' ) );
		?>
		<script type="text/html" id="tmpl-awsm-dropr-uploader">
			<div class="uploader-inline-content">
				<# if ( data.message.length > 0 ) { #>
					<h2 class="upload-message">{{ data.message }}</h2>
				<# } #>
				<?php if ( is_multisite() && $is_local_storage && ! is_upload_space_available() ) : ?>
					<div class="upload-ui">
						<h2 class="upload-instructions"><?php _e( 'Upload Limit Exceeded' ); ?></h2>
					</div>
				<?php else : ?>
					<div class="awsm-dropr-media-btn-wrapper">
						<button type="button" class="awsm-dropr-media-btn button button-hero{{ data.buttonClass }}"><?php esc_html_e( 'Add from Dropbox', 'dropr' ); ?></button>
					</div>
					<# if ( data.isUploading ) { #>
						<div class="awsm-dropr-media-uploading">
							<?php esc_html_e( 'Processing, please wait...', 'dropr' ); ?>
							<span class="spinner"></span>
						</div>
					<# } #>

					<div class="awsm-dropr-media-uploader-status media-uploader-status errors" style="display: none;">
						<div class="upload-errors">
							<div class="upload-error">
								<p class="upload-error-message"><?php esc_html_e( 'Failed to process the file.', 'dropr' ); ?></p>
							</div>
						</div>
					</div>

					<?php if ( $is_local_storage ) : ?>
						<div class="post-upload-ui">
							<p>
							<?php
								/* translators: %s: Settings link. */
								printf( esc_html__( 'The file(s) will be copied to media library (Change it in %s)', 'dropr' ), $settings_link );
							?>
							</p>
							<?php
								$max_upload_size = wp_max_upload_size();
							if ( ! $max_upload_size ) {
								$max_upload_size = 0;
							}
							?>

							<p class="max-upload-size">
							<?php
								printf(
									/* translators: %s: Maximum allowed file size. */
									__( 'Maximum upload file size: %s.' ),
									esc_html( size_format( $max_upload_size ) )
								);
							?>
							</p>
						</div>
					<?php else : ?>
						<div class="post-upload-ui">
							<p>
								<?php
									/* translators: %s: Settings link. */
									printf( esc_html__( 'The file(s) will NOT be copied to media library (Change it in %s)', 'dropr' ), $settings_link );
								?>
							</p>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</script>
		<?php
	}

	/**
	 * Cutomize arguments passed to WP_Query during an Ajax call for querying attachments.
	 *
	 * @param array $query An array of query variables.
	 * @return array
	 */
	public function ajax_query_attachments_args( $query ) {
		if ( isset( $_POST['query'] ) && isset( $_POST['query']['dropr_meta'] ) && $_POST['query']['dropr_meta'] === '_awsm_dropr_attached_file' ) {
			$query['meta_key'] = '_awsm_dropr_attached_file';
		}
		return $query;
	}

	/**
	 * Dropbox files embed setting popup
	 *
	 * @since 1.0
	 */
	public function dropr_popup() {
		if ( wp_script_is( 'dropr-main' ) ) {
			include $this->plugin_path . 'inc/popup.php';
		}
	}

	/**
	 * Admin Easy access settings link
	 *
	 * @since 1.0
	 */
	public function settingslink( $links ) {
		$links[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'options-general.php?page=' . $this->settings_slug ) ), esc_html__( 'Settings', 'dropr' ) );
		return $links;
	}

	/**
	 * Admin menu setup
	 *
	 * @since 1.0
	 */
	public function admin_menu() {
		$eadsettings = add_options_page( 'Dropr', 'Dropr', 'manage_options', $this->settings_slug, array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $eadsettings, array( $this, 'setting_styles' ) );
		add_action( 'admin_print_scripts-' . $eadsettings, array( $this, 'setting_scripts' ) );
	}

	/**
	 * Dropr settings enqueue style
	 *
	 * @since 1.0
	 */
	public function setting_styles() {
		wp_enqueue_style( 'wp-dropbox', plugins_url( 'css/settings.css', $this->plugin_file ), false, $this->plugin_version, 'all' );
	}

	/**
	 * Dropr settings enqueue scripts
	 *
	 * @since 1.0
	 */
	public function setting_scripts() {
		wp_enqueue_script( 'wpdrop-rangeslider', plugins_url( 'js/rangeslider.min.js', $this->plugin_file ), array( 'jquery' ), $this->plugin_version );
		wp_enqueue_script( 'wpdrop-colorpicker', plugins_url( 'js/colorpicker.js', $this->plugin_file ), array( 'jquery' ), $this->plugin_version );
		wp_enqueue_script( 'wpdrop-settings', plugins_url( 'js/settings.js', $this->plugin_file ), array( 'jquery' ), $this->plugin_version );
	}

	/**
	 * Dropr settings page
	 *
	 * @since  1.0
	 */
	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}
		include $this->plugin_path . 'inc/settings.php';
	}

	/**
	 * Dropr settings api register
	 *
	 * @since  1.0
	 */
	public function register_settings() {
		register_setting( 'wp-dropbox-api', 'wpdropbox-apikey' );
		register_setting( 'wp-dropbox-settings', 'dropr-settings' );
		register_setting( 'wp-dropbox-settings', 'dropr-generic-settings' );
	}

	/**
	 * Shortcode 'docembed' handler
	 *
	 * @since  1.0
	 */
	public function embed_shortcode( $atts ) {
		$embed            = '';
		$durl             = '';
		$default_width    = dropr_sanitize( '100%' );
		$default_height   = dropr_sanitize( '500px' );
		$default_provider = 'google';
		$default_download = 'none';
		$show             = false;
		extract(
			shortcode_atts(
				array(
					'url'      => '',
					'drive'    => '',
					'width'    => $default_width,
					'height'   => $default_height,
					'language' => 'en',
					'viewer'   => $default_provider,
					'download' => $default_download,
				),
				$atts
			)
		);

		if ( $url ) :
			if ( $viewer === 'dropbox' ) {
				wp_enqueue_script( 'dropr-core' );
			}

			$durl = '';
			if ( $download == 'alluser' || $download == 'all' ) {
				$show = true;
			} elseif ( $download == 'logged' && is_user_logged_in() ) {
				$show = true;
			}
			if ( $show ) {
				$downurl = str_replace( '?raw=1', '?dl=1', $url );
				$downurl = esc_url( $downurl, array( 'http', 'https' ) );
				$durl    = '<p class="embed_download"><a href="' . $downurl . '" download >' . __( 'Download', 'dropr' ) . '</a></p>';
			}

			$url           = esc_url( $url, array( 'http', 'https' ) );
			$provider_list = array( 'dropbox', 'google', 'microsoft' );
			if ( ! in_array( $viewer, $provider_list ) ) {
				$viewer = 'google';
			}

			$iframe_src = '';
			switch ( $viewer ) {
				case 'google':
					$embedsrc   = '//docs.google.com/viewer?url=%1$s&embedded=true&hl=%2$s';
					$iframe_src = sprintf( $embedsrc, urlencode( $url ), esc_attr( $language ) );
					break;

				case 'microsoft':
					$embedsrc   = '//view.officeapps.live.com/op/embed.aspx?src=%1$s';
					$iframe_src = sprintf( $embedsrc, urlencode( $url ) );
					break;
			}

			$iframe = '';
			if ( $viewer === 'dropbox' ) {
				$url    = str_replace( array( '?raw=1', 'dl=1' ), 'dl=0', $url );
				$iframe = sprintf( '<a href="%s" class="dropbox-embed" data-height="%s" data-width="%s"></a>', $url, esc_attr( dropr_sanitize( $height ) ), esc_attr( dropr_sanitize( $width ) ) );
			} else {
				$style     = 'style="width:%1$s; height:%2$s; border: none;"';
				$stylelink = sprintf( $style, dropr_sanitize( $width ), dropr_sanitize( $height ) );
				$iframe    = '<iframe src="' . esc_url( $iframe_src ) . '" ' . $stylelink . '></iframe>';
			}
			$embed = '<div class="ead-document"><div class="dropr-iframe-wrapper">' . $iframe . '</div>' . $durl . '</div>';
		else :
			$embed = __( 'No Url Found', 'dropr' );
		endif;
		return $embed;
	}

	/**
	 * Button style generator
	 *
	 * @since  1.0
	 * @return  string wpdropbox-button css
	 */
	public function buttons_css() {
		$defaults      = dropr_defaults();
		$wpdropoptions = get_option( 'dropr-settings' );
		$options       = wp_parse_args( $wpdropoptions, $defaults );
		extract( $options );
		echo '<style type="text/css">
            a.wpdropbox-button { 
              background: ' . esc_attr( $bgcolor ) . '!important;
              color: ' . esc_attr( $btntxtcolor ) . '!important;
              padding: ' . esc_attr( $vpadding ) . 'px ' . esc_attr( $hpadding ) . 'px!important;
              font-size: ' . esc_attr( $fontsize ) . 'px!important;
              border: ' . esc_attr( $brthick ) . 'px solid  ' . esc_attr( $brcolor ) . '!important;
              -webkit-border-radius: ' . esc_attr( $brradius ) . 'px!important;
              border-radius: ' . esc_attr( $brradius ) . 'px!important;
              display: inline-block;
              text-decoration: none;
              line-height:1!important;
            }
            a.wpdropbox-button:hover{
              opacity:0.8;
            }  
       </style>';
	}

	/**
	 * To initialize with default options
	 *
	 * @since  1.0
	 */
	public function reset_default() {
		$defaults = dropr_defaults();
		if ( ! get_option( 'dropr-settings' ) ) {
			update_option( 'dropr-settings', $defaults );
		}
	}
}
Dropr_main::get_instance();
