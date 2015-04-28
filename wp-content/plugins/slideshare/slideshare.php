<?php
/*
Plugin Name: SlideShare
Plugin URI: http://yoast.com/wordpress/slideshare/
Description: A plugin for WordPress to easily display slideshare.net presentations.
Version: 1.9.1
Author: Joost de Valk
Author URI: https://yoast.com/
*/

function yst_slideshare_init() {
	load_plugin_textdomain( 'slideshare', null, plugins_url( 'languages', __FILE__ ) );
}

add_action( 'init', 'yst_slideshare_init' );

if ( ! class_exists( 'Yoast_SlideShare_Admin' ) && is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( 'yst_plugin_tools.php' );

	class Yoast_SlideShare_Admin extends Yoast_Plugin_Admin {

		var $hook = 'slideshare';
		var $longname;
		var $shortname = 'SlideShare';

		/**
		 * Class constructor
		 */
		function __construct() {
			$this->longname = __( 'SlideShare Configuration', 'slideshare' );
			add_action( 'admin_menu', array( &$this, 'register_settings_page' ) );
			add_filter( 'plugin_action_links', array( &$this, 'add_action_link' ), 10, 2 );

			add_action( 'admin_print_scripts', array( &$this, 'config_page_scripts' ) );
			add_action( 'admin_print_styles', array( &$this, 'config_page_styles' ) );

			add_action( 'admin_init', array( &$this, 'options_init' ) );
		}

		/**
		 * Register the SlideShare option
		 */
		function options_init(){
			register_setting( 'yoast_slideshare_options', 'slideshare' );
		}

		/**
		 * Create the config page
		 */
		function config_page() {
			$options = get_option( 'slideshare' );

			if ( ! is_array( $options ) || empty( $options['postwidth'] ) || $options['postwidth'] === 0 ) {
				global $content_width;
				$options['postwidth'] = $content_width;
			}
			?>
			<div class="wrap">
				<h2><?php _e( "SlideShare Configuration", 'slideshare' ); ?></h2>

				<div class="postbox-container" style="width:70%;">
					<div class="metabox-holder">
						<div class="meta-box-sortables">
							<form action="options.php" method="post" id="slideshare-conf">
								<?php settings_fields( 'yoast_slideshare_options' ); ?>
								<?php
								$rows    = array();
								$rows[]  = array(
									"id"      => "slidesharepostwidth",
									"label"   => __( "Presentation width", 'slideshare' ),
									"content" => '<input size="5" type="text" id="slidesharepostwidth" name="slideshare[postwidth]" value="' . $options['postwidth'] . '"/> pixels',
								);
								$content = $this->form_table( $rows ) . '<div class="submit"><input type="submit" class="button-primary" name="submit" value="' . __( "Update SlideShare Settings", 'slideshare' ) . ' &raquo;" /></div>';
								$this->postbox( 'slidesharesettings', __( 'Settings', 'slideshare' ), $content );
								$this->postbox( 'usageexplanation', __( 'Explanation of usage', 'slideshare' ), '<p>' . sprintf( __( 'Just copy and paste the WordPress Embed code from %s and you\'re done. Or you can just put the presentation URL on a line of its own.', 'slideshare' ), '<a href="http://www.slideshare.net/">SlideShare</a>' ) . '</p>' );
								$this->postbox( 'defaultwidthexpl', __( "Explanation of default width", 'slideshare' ), '<p>' . __( "If you enter nothing in the setting above, you can change the width by hand by changing (or inserting) the w= value, that is bolded and red here:", 'slideshare' ) . '</p>' . '<pre>[slideshare id=1234&amp;doc=how-to-change-the-width-123456789-1&amp;<strong style="color:red;">w=425</strong>]</pre>' . '<p>' . __( "If you <em>do</em> enter a value, it will always replace the width with that value.", 'slideshare' ) ); ?>
							</form>
						</div>
					</div>
				</div>
				<div class="postbox-container" style="width:18%; margin-left: 10px;">
					<div class="metabox-holder">
						<div class="meta-box-sortables">
							<?php
							$this->plugin_like();
							?>
						</div>
						<br /><br /><br />
					</div>
				</div>
			</div>
		<?php
		}
	}

	$ssa = new Yoast_SlideShare_Admin();
}

if ( ! is_admin() ) {

	/**
	 * Class Yoast_SlideShare_Front
	 */
	class Yoast_SlideShare_Front {

		/**
		 * Regex used to recognize SlideShare URLs
		 *
		 * @var string
		 */
		var $regex = '#https?://(www\.)?slideshare\.net/([^/]+)/([^/]+)#i';

		/**
		 * Class constructor
		 */
		function __construct() {
			// Make open embed work
			add_action( 'init', array( $this, 'enable_openembed' ) );

			// Change the open embed code to use the iframe and remove all the silly links
			add_filter( 'embed_oembed_html', array( $this, 'change_oembed' ), 1, 3 );

			// Backwards compatibility for the old shortcode
			add_shortcode( 'slideshare', array( $this, 'shortcode' ) );

		}

		/**
		 * Takes the ID of a presentation and the width to present it at and returns the iframe string for that presentation
		 *
		 * @param int $id
		 * @param int $width
		 *
		 * @return string
		 */
		function embed( $id, $width ) {
			$height = round( $width / 1.32 ) + 34;

			return '<iframe src="https://www.slideshare.net/slideshow/embed_code/' . $id . '" width="' . $width . '" height="' . $height . '" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe><br/>';
		}

		/**
		 * Generate the SlideShare embed from a shortcode
		 *
		 * @param      $atts
		 *
		 * @return bool|string
		 */
		function shortcode( $atts ) {
			if ( isset( $atts ) ) {
				$options = get_option( 'SlideShare' );

				$args = str_replace( '&#038;', '&', $atts['id'] );
				$args = str_replace( '&amp;', '&', $args );
				$r    = wp_parse_args( 'id=' . $args );

				if ( $options['postwidth'] == '' ) {
					$width = $r['w'];
				} else {
					$width = $options['postwidth'];
				}
				if ( $width == 0 ) {
					global $content_width;
					$width = $content_width;
				}
				if ( $width == 0 ) {
					$width = 400;
				}

				return $this->embed( $r['id'], $width );
			}

			return false;
		}

		/**
		 * Enable the Slideshare oEmbed provider
		 */
		function enable_openembed() {
			wp_oembed_add_provider( $this->regex, 'http://www.slideshare.net/api/oembed/2', true );
		}

		/**
		 * Change the oembed output to match the site's width settings
		 *
		 * @param string $html
		 * @param string $url
		 * @param array  $attr
		 *
		 * @return string
		 */
		function change_oembed( $html, $url, $attr ) {
			if ( ! preg_match( $this->regex, $url ) ) {
				return $html;
			}

			$options = get_option( 'SlideShare' );

			// try getting the width set in the settings, if that's not there, default to the theme's content width, otherwise default to 425.
			$width = $options['postwidth'];
			if ( $width == '' ) {
				global $content_width;
				$width = $content_width;
			}
			if ( $width == '' ) {
				$width = 425;
			}

			if ( preg_match( '#slideshow/embed_code/(\d+)#', $html, $matches ) ) {
				$new_html = $this->embed( $matches[1], $width );
			} else {
				$new_html = $html;
			}

			// Force https
			$new_html = str_replace( 'http://www.slideshare.net', 'https://www.slideshare.net', $new_html );

			// Strip out credit links
			$new_html = preg_replace( '/(.*<\/iframe>)(.*)/', '$1', $new_html );

			// Debug
			// $new_html .= htmlentities($html);

			return $new_html;
		}

	}

	$yoast_slideshare = new Yoast_SlideShare_Front();

}