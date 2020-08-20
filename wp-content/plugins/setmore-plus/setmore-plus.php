<?php
/**
 * Plugin Name: Setmore Plus
 * Plugin URI: https://strongplugins.com/plugins/setmore-plus
 * Description: Easy online appointments with a widget, shortcode, or menu link.
 * Version: 3.7.2
 * Author: Chris Dillon
 * Author URI: https://strongplugins.com
 * Text Domain: setmore-plus
 * Requires: 3.5 or higher
 * License: GPLv3 or later
 *
 * Copyright 2014-2019  Chris Dillon  chris@strongwp.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Setmore_Plus {

    private static $lang;

    private static $lang_codes;

    public $admin;

	function __construct() {

		$this->set_lang();

		$this->constants();

	    $this->add_actions();

		require_once SETMOREPLUS_DIR . 'inc/class-setmore-plus-widget.php';

		if ( is_admin() ) {
		    require_once SETMOREPLUS_DIR . 'inc/class-setmore-plus-admin.php';
			$this->admin = new Setmore_Plus_Admin();
		}

	}

	public function constants() {
		if ( ! defined( 'SETMOREPLUS' ) )
			define( 'SETMOREPLUS', plugin_basename( __FILE__ ) );

		if ( ! defined( 'SETMOREPLUS_URL' ) )
			define( 'SETMOREPLUS_URL', plugin_dir_url( __FILE__ ) );

		if ( ! defined( 'SETMOREPLUS_DIR' ) )
			define( 'SETMOREPLUS_DIR', plugin_dir_path( __FILE__ ) );

		if ( ! defined( 'SETMOREPLUS_IMAGES' ) )
			define( 'SETMOREPLUS_IMAGES', plugin_dir_url( __FILE__ ) . '/images/' );
	}

	public function add_actions() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		add_action( 'init', array( $this, 'register_shortcodes' ) );

		add_action( 'wp_head', array( $this, 'show_version_info' ), 999 );

		add_filter( 'no_texturize_shortcodes', array( $this, 'shortcodes_to_exempt_from_wptexturize' ) );

		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_colorbox' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'setmoreplus_script' ) );

		add_filter( 'setmoreplus_url', array( $this, 'url_filter' ), 10, 3 );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'setmore-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	private function set_lang() {
	    /*
	     * Arabic, Bulgarian, Czech, Croatia, Danish, Dutch, English, Estonian, French, Finnish, German, Greek, Hebrew, Hungarian, Italian, Icelandic, Japanese, Korean, Latin, Lithuanian, Norwegian, Polish, Portuguese, Romanian, Russian, Serbian, Slovenian, Spanish, Swedish, Turkish, Ukrainian
	     */

	    self::$lang = array(
			'arabic'     => 'Arabic',
			'bulgarian'  => 'Bulgarian',
			'czech'      => 'Czech',
			'croatia'    => 'Croatia',
			'danish'     => 'Danish',
			'dutch'      => 'Dutch',
			'english'    => 'English',
			'estonian'   => 'Estonian',
			'french'     => 'French',
			'finnish'    => 'Finnish',
			'german'     => 'German',
			'greek'      => 'Greek',
			'hebrew'     => 'Hebrew',
			'hungarian'  => 'Hungarian',
			'italian'    => 'Italian',
			'icelandic'  => 'Icelandic',
			'japanese'   => 'Japanese',
			'korean'     => 'Korean',
			'latin'      => 'Latin',
			'lithuanian' => 'Lithuanian',
			'norwegian'  => 'Norwegian',
			'polish'     => 'Polish',
			'portuguese' => 'Portuguese',
			'romanian'   => 'Romanian',
			'russian'    => 'Russian',
			'serbian'    => 'Serbian',
			'slovenian'  => 'Slovenian',
			'spanish'    => 'Spanish',
			'swedish'    => 'Swedish',
			'turkish'    => 'Turkish',
			'ukrainian'  => 'Ukrainian',
        );

		self::$lang_codes = array(
			'en-US' => 'english',
			'en-GB' => 'english',
			'en-NZ' => 'english',
			'en-CA' => 'english',
			'en-AU' => 'english',
			'en-ZA' => 'english',

			'ar'  => 'arabic',
			'ary' => 'arabic',

			'bg-BG' => 'bulgarian',

			'cs-CZ' => 'czech',

			'hr' => 'croatia',

			'da-DK' => 'danish',

			'nl-BE'        => 'dutch',
			'nl-NL'        => 'dutch',
			'nl-NL-formal' => 'dutch',

			'et' => 'estonian',

			'fr-FR' => 'french',
			'fr-BE' => 'french',
			'fr-CA' => 'french',

			'fi' => 'finnish',

			'de-DE'          => 'german',
			'de-DE-formal'   => 'german',
			'de-CH'          => 'german',
			'de-CH-informal' => 'german',

			'el' => 'greek',

			'he-IL' => 'hebrew',

			'hu-HU' => 'hungarian',

			'it-IT' => 'italian',

			'is-IS' => 'icelandic',

			'ja' => 'japanese',

			'ko-KR' => 'korean',

			'lt-LT' => 'lithuanian',

			'nb-NO' => 'norwegian',

			'nn-NO' => 'norwegian',

			'pl-PL' => 'polish',

			'pt-BR' => 'portuguese',
			'pt-PT' => 'portuguese',

			'ro-RO' => 'romanian',

			'ru-RU' => 'russian',
			'ru-UA' => 'russian',

			'sr-RS' => 'serbian',

			'sl-SI' => 'slovenian',

			'es-ES' => 'spanish',
			'es-MX' => 'spanish',
			'es-GT' => 'spanish',
			'es-CO' => 'spanish',
			'es-VE' => 'spanish',
			'es-CL' => 'spanish',
			'es-PE' => 'spanish',
			'es-AR' => 'spanish',
			'es-PR' => 'spanish',

			'sv-SE' => 'swedish',

			'tr-TR' => 'turkish',

			'uk' => 'ukrainian',
		);
	}

	public static function get_lang() {
	    return self::$lang;
	}

	public static function get_lang_codes() {
	    return self::$lang_codes;
	}

	public function load_colorbox() {
		$options = get_option( 'setmoreplus' );
		wp_enqueue_style( 'colorbox-style', SETMOREPLUS_URL . 'inc/colorbox/colorbox.css' );
		wp_enqueue_script( 'colorbox-script', SETMOREPLUS_URL . 'inc/colorbox/jquery.colorbox-min.js', array( 'jquery' ), false, $options['defer'] );
	}

	public function load_scripts() {
		$options = get_option( 'setmoreplus' );
		$version = get_option( 'setmoreplus_version' );
		wp_enqueue_script( 'setmoreplus-script', SETMOREPLUS_URL . 'js/setmoreplus.js', array( 'colorbox-script' ), $version, $options['defer'] );
	}

	public function register_widget() {
		register_widget( 'Setmore_Plus_Widget' );
	}

	public function register_shortcodes() {
		add_shortcode( 'setmoreplus', array( $this, 'render_popup' ) );
	}

	public function render_popup( $atts, $content = '' ) {
		// TODO Without using extract
		$atts = shortcode_atts(
			array(
				'button' => '',
				'link'   => '',
				'class'  => '',
				'staff'  => '',
                'lang'   => '',
			),
			$this->normalize_empty_atts( $atts ), 'setmoreplus'
		);

		$options = get_option( 'setmoreplus' );
		$url     = $options['url'];
		$content = $content ? $content : $options['link_text'];

		/**
		 * CSS classes
		 *
		 * .setmore : style only
		 * .setmore-iframe : for Colorbox
		 */
		$classes = join( ' ', array_merge( array( 'setmore', 'setmore-iframe' ), explode( ' ', $atts['class'] ) ) );

		/**
		 * Language
         *
         * @since 3.7.0
		 */
        $lang_code = get_bloginfo( 'language' );
        // Shortcode attribute takes precedence
		$lang = $atts['lang'];
        if ( ! $lang ) {
			if ( $options['lang'] ) {
				$lang = $options['lang'];
			} elseif ( isset( self::$lang_codes[ $lang_code ] ) ) {
				$lang = self::$lang_codes[ $lang_code ];
				if ( 'english' == $lang ) {
					$lang = '';
				}
			} else {
				$lang = '';
			}
		}

        // Filter will find staff URL, add lang, and force https.
		$url = apply_filters( 'setmoreplus_url', $url, $atts['staff'], $lang );

		/**
		 * Assemble the HTML
		 */
		if ( $atts['link'] ) {

			$html = sprintf( '<a class="%s" href="%s">%s</a>', $classes, $url, $content );

		} elseif ( $atts['button'] ) {

			// href is not a valid attribute for <button> but Colorbox needs it to load the target page
			$html = sprintf( '<button class="%s" href="%s">%s</button>', $classes, $url, $content );

		} else {

			// embed an iframe in the page
			$html = sprintf( '<iframe class="setmore-iframe" src="%s" width="%s" height="%s" frameborder="0"></iframe>',
				$url,
				$options['embed_desktop_width'] . ('%' == $options['embed_desktop_width_p'] ? '%' : '' ),
				$options['embed_desktop_height'] );

			$html .= '<style>@media only screen and (max-width: ' . $options['embed_mobile_breakpoint'] . 'px) { iframe.setmore-iframe { width: 100%; } }</style>';

		}

		return $html;
	}

	/**
     * Find staff URL if used, add lang, and force https.
     *
	 * @param $url
	 * @param string $staff
	 * @param string $lang
	 *
	 * @return mixed
	 */
	public function url_filter( $url, $staff = '', $lang = '' ) {
		$options = get_option( 'setmoreplus' );

		if ( $staff ) {
			if ( is_numeric( $staff ) ) {
				if ( isset( $options['staff_urls'][ $staff ] ) && $options['staff_urls'][ $staff ] ) {
					$url = $options['staff_urls'][ $staff ]['url'];
				} else {
					echo "<!-- Setmore Plus error: staff '$staff' not found -->";
				}
			} else {
				if ( isset( $options['staff_urls'] ) ) {
					foreach ( $options['staff_urls'] as $staff_info ) {
						if ( strtolower( $staff ) == strtolower( $staff_info['name'] ) ) {
							$url = $staff_info['url'];
							break;
						}
					}
				} else {
					echo "<!-- Setmore Plus error: staff '$staff' not found -->";
				}
			}
		}

		if ( $lang ) {
		    if ( in_array( $lang, array_keys( self::$lang ) ) ){
			    $url .= "?lang=$lang";
                $url = self::add_scheme( $url, $lang );
		    } else {
		        echo "<!-- Setmore Plus error: lang '$lang' not found -->";
			}
		}

		return $url;
	}

	/**
	 * Add correct URL scheme.
	 *
	 * @param $url
	 * @param string $lang
	 * @return mixed
	 */
	public static function add_scheme( $url, $lang = '' ) {
		/**
		 * Four possible conditions:
		 *
		 * 1. wpmission.setmore.com
		 * 2. //wpmission.setmore.com
		 * 3. http://wpmission.setmore.com
		 * 4. https://wpmission.setmore.com
		 */

		// Remove double slash at start of string.
		// Previous version recommended this to allow browser to decide protocol.
		if ( 0 === strpos( $url, '//' ) ) {
			$url = substr( $url, 2 );
		}

		// Remove existing scheme.
		$url = str_replace( array( 'http://', 'https://' ), array( '', '' ), $url );

		// Determine scheme.
		// TODO Remove localhost code
		// If local dev, use http unless adding lang
		// Otherwise, use https
		//$scheme = '127.0.0.1' == $_SERVER['SERVER_ADDR'] && !$lang ? 'http://' : 'https://';
		$scheme = 'https://';

		// Prepend correct scheme.
		$url = parse_url( $url, PHP_URL_SCHEME ) === null ? $scheme . $url : $url;

		return $url;
	}

	/**
	 * Display lightbox.
	 *
	 * @since 2.3.0
	 */
	public function setmoreplus_script() {
		$options = get_option( 'setmoreplus' );
		$var = array(
			'iframe'      => true,
			'transition'  => 'elastic',
			'speed'       => 200,
			'height'      => $options['height'] . $options['height_p'],
			'width'       => $options['width'] . $options['width_p'],
			'breakpoint'  => $options['mobile_breakpoint'],
			'opacity'     => 0.8,
			'returnFocus' => false,
			'rel'         => false,
		);
		wp_localize_script( 'colorbox-script', 'setmoreplus', $var );
	}

	/**
	 * Do not texturize shortcode.
	 *
	 * For WordPress 4.0.1+
	 * @since 2.2.2
	 * @param $shortcodes
	 * @return array
	 */
	public function shortcodes_to_exempt_from_wptexturize( $shortcodes ) {
		$shortcodes[] = 'setmoreplus';

		return $shortcodes;
	}

	/**
	 * Normalize empty shortcode attributes.
	 *
	 * Turns atts into tags - brilliant!
	 * Thanks http://wordpress.stackexchange.com/a/123073/32076
	 *
	 * @since 2.3.0
	 * @param $atts
	 * @return array
	 */
	public function normalize_empty_atts( $atts ) {
		if ( !empty( $atts ) ) {
			foreach ( $atts as $attribute => $value ) {
				if ( is_int( $attribute ) ) {
					$atts[ strtolower( $value ) ] = true;
					unset( $atts[ $attribute ] );
				}
			}
		}

		return $atts;
	}

	/**
	 * Show version number in <head> section.
	 *
	 * For troubleshooting only.
	 *
	 * @since 3.6.1
	 */
	function show_version_info() {
		$version = get_option( 'setmoreplus_version');
		$comment = array(
			'Setmore Plus ' . $version,
		);

		echo "<!-- " . implode( ' | ', $comment ) . " -->\n";
	}

	/**
	 * Return plugin version.
	 *
	 * @since 3.7
	 *
	 * @return mixed
	 */
	public static function get_plugin_version() {
		$data = get_file_data( __FILE__, array( 'version' => 'Version' ) );

		return $data['version'];
	}

}

new Setmore_Plus();
