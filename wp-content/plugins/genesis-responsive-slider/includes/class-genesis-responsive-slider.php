<?php
/**
 * Genesis Responsive Slider Class.
 *
 * @package genesis-responsive-slider
 */

/**
 * Genesis Responsive Slider.
 */
class Genesis_Responsive_Slider {
	/**
	 * Constructor.
	 */
	public static function init() {

		if ( ! function_exists( 'genesis_get_option' ) ) {
			return false;
		}

		// Translation support.
		load_plugin_textdomain( 'genesis-responsive-slider', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

		/** Hook all frontend slider functions here to ensure Genesis is active. */
		add_action( 'wp_enqueue_scripts', array( 'Genesis_Responsive_Slider', 'genesis_responsive_slider_scripts' ) );
		add_action( 'wp_print_styles', array( 'Genesis_Responsive_Slider', 'genesis_responsive_slider_styles' ) );
		add_action( 'wp_head', array( 'Genesis_Responsive_Slider', 'genesis_responsive_slider_head' ), 1 );
		add_action( 'wp_footer', array( 'Genesis_Responsive_Slider', 'genesis_responsive_slider_flexslider_params' ) );
		add_action( 'widgets_init', array( 'Genesis_Responsive_Slider', 'genesis_responsive_slider_register' ) );
		add_action( 'after_switch_theme', array( 'Genesis_Responsive_Slider', 'genesis_responsive_slider_reset_on_theme_switch' ) );

		/** Add new image size */
		add_image_size( 'slider', (int) self::genesis_get_responsive_slider_option( 'slideshow_width' ), (int) self::genesis_get_responsive_slider_option( 'slideshow_height' ), true );

		add_action( 'genesis_settings_sanitizer_init', array( 'Genesis_Responsive_Slider', 'genesis_responsive_slider_sanitization' ) );

		add_filter( 'genesis_responsive_slider_defaults', array( 'Genesis_Responsive_Slider', 'genesis_responsive_slider_defaults' ) );
	}

	/**
	 * Uninstall hook.
	 */
	public static function genesis_responsive_slider_plugin_uninstall() {
		delete_option( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD );
	}

	/**
	 * Add settings to Genesis sanitization
	 */
	public static function genesis_responsive_slider_sanitization() {
		genesis_add_option_filter(
			'one_zero',
			GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD,
			array(
				'slideshow_arrows',
				'slideshow_excerpt_show',
				'slideshow_title_show',
				'slideshow_loop',
				'slideshow_hide_mobile',
				'slideshow_no_link',
				'slideshow_pager',
			)
		);

		genesis_add_option_filter(
			'no_html',
			GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD,
			array(
				'post_type',
				'posts_term',
				'exclude_terms',
				'include_exclude',
				'post_id',
				'posts_num',
				'posts_offset',
				'orderby',
				'slideshow_timer',
				'slideshow_delay',
				'slideshow_height',
				'slideshow_width',
				'slideshow_effect',
				'slideshow_excerpt_content',
				'slideshow_excerpt_content_limit',
				'slideshow_more_text',
				'slideshow_excerpt_width',
				'location_vertical',
				'location_horizontal',
			)
		);
	}

	/**
	 * Load the script files
	 */
	public static function genesis_responsive_slider_scripts() {

		/** EasySlider JavaScript code */
		wp_enqueue_script( 'flexslider', GENESIS_RESPONSIVE_SLIDER_PLUGIN_URL . '/assets/js/jquery.flexslider.js', array( 'jquery' ), GENESIS_RESPONSIVE_SLIDER_VERSION, true );

	}

	/**
	 * Load the CSS files
	 */
	public static function genesis_responsive_slider_styles() {

		/** Standard slideshow styles */
		wp_register_style( 'slider_styles', GENESIS_RESPONSIVE_SLIDER_PLUGIN_URL . '/assets/style.css', array(), GENESIS_RESPONSIVE_SLIDER_VERSION );
		wp_enqueue_style( 'slider_styles' );

	}

	/**
	 * Loads scripts and styles via wp_head hook.
	 */
	public static function genesis_responsive_slider_head() {

			$height = (int) self::genesis_get_responsive_slider_option( 'slideshow_height' );
			$width  = (int) self::genesis_get_responsive_slider_option( 'slideshow_width' );

			$slide_info_width = (int) self::genesis_get_responsive_slider_option( 'slideshow_excerpt_width' );
			$slide_nav_top    = (int) ( ( $height - 60 ) * .5 );

			$vertical   = self::genesis_get_responsive_slider_option( 'location_vertical' );
			$horizontal = self::genesis_get_responsive_slider_option( 'location_horizontal' );
			$display    = ( self::genesis_get_responsive_slider_option( 'posts_num' ) >= 2 && self::genesis_get_responsive_slider_option( 'slideshow_arrows' ) ) ? 'top: ' . $slide_nav_top . 'px' : 'display: none';

			$hide_mobile     = self::genesis_get_responsive_slider_option( 'slideshow_hide_mobile' );
			$slideshow_pager = self::genesis_get_responsive_slider_option( 'slideshow_pager' );

			echo '
			<style type="text/css">
				.slide-excerpt { width: ' . esc_html( $slide_info_width ) . '%; }
				.slide-excerpt { ' . esc_html( $vertical ) . ': 0; }
				.slide-excerpt { ' . esc_html( $horizontal ) . ': 0; }
				.flexslider { max-width: ' . esc_html( $width ) . 'px; max-height: ' . esc_html( $height ) . 'px; }
				.slide-image { max-height: ' . esc_html( $height ) . 'px; }
			</style>';

		if ( '1' === $hide_mobile ) {
			echo '
			<style type="text/css">
				@media only screen
				and (min-device-width : 320px)
				and (max-device-width : 480px) {
					.slide-excerpt { display: none !important; }
				}
			</style> ';
		}
	}

	/**
	 * Outputs slider script on wp_footer hook.
	 */
	public static function genesis_responsive_slider_flexslider_params() {

		$timer        = (int) self::genesis_get_responsive_slider_option( 'slideshow_timer' );
		$duration     = (int) self::genesis_get_responsive_slider_option( 'slideshow_delay' );
		$effect       = self::genesis_get_responsive_slider_option( 'slideshow_effect' );
		$controlnav   = self::genesis_get_responsive_slider_option( 'slideshow_pager' );
		$directionnav = self::genesis_get_responsive_slider_option( 'slideshow_arrows' );

		$output = 'jQuery(document).ready(function($) {
					$(".flexslider").flexslider({
						controlsContainer: "#genesis-responsive-slider",
						animation: "' . esc_js( $effect ) . '",
						directionNav: ' . $directionnav . ',
						controlNav: ' . $controlnav . ',
						animationDuration: ' . $duration . ',
						slideshowSpeed: ' . $timer . '
				    });
				  });';

		$output = str_replace( array( "\n", "\t", "\r" ), '', $output );

		echo '<script type=\'text/javascript\'>' . wp_kses_post( $output ) . '</script>';
	}

	/**
	 * Registers the slider widget
	 */
	public static function genesis_responsive_slider_register() {
		register_widget( 'Genesis_Responsive_Slider_Widget' );
	}

	/**
	 * Returns Slider Option
	 *
	 * @param string $key key value for option.
	 * @return string
	 */
	public static function genesis_get_responsive_slider_option( $key ) {
		return genesis_get_option( $key, GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD );
	}

	/**
	 * Echos Slider Option
	 *
	 * @param string $key key value for option.
	 */
	public static function genesis_responsive_slider_option( $key ) {

		if ( ! self::genesis_get_responsive_slider_option( $key ) ) {
			return false;
		}

		echo esc_html( self::genesis_get_responsive_slider_option( $key ) );
	}

	/**
	 * Return the defaults array
	 *
	 * @since 0.9
	 */
	public static function genesis_responsive_slider_defaults() {

		$defaults = array(
			'post_type'                       => 'post',
			'posts_term'                      => '',
			'exclude_terms'                   => '',
			'include_exclude'                 => '',
			'post_id'                         => '',
			'posts_num'                       => 5,
			'posts_offset'                    => 0,
			'orderby'                         => 'date',
			'slideshow_timer'                 => 4000,
			'slideshow_delay'                 => 800,
			'slideshow_arrows'                => 1,
			'slideshow_pager'                 => 1,
			'slideshow_loop'                  => 1,
			'slideshow_no_link'               => 0,
			'slideshow_height'                => 400,
			'slideshow_width'                 => 920,
			'slideshow_effect'                => 'slide',
			'slideshow_excerpt_content'       => 'excerpts',
			'slideshow_excerpt_content_limit' => 150,
			'slideshow_more_text'             => __( '[Continue Reading]', 'genesis-responsive-slider' ),
			'slideshow_excerpt_show'          => 1,
			'slideshow_excerpt_width'         => 50,
			'location_vertical'               => 'bottom',
			'location_horizontal'             => 'right',
			'slideshow_hide_mobile'           => 1,
		);

		return apply_filters( 'genesis_responsive_slider_settings_defaults', $defaults );
	}

	/**
	 * Uses the filter default settings when changing themes.
	 */
	public static function genesis_responsive_slider_reset_on_theme_switch() {
		if ( has_filter( 'genesis_responsive_slider_settings_defaults' ) ) {
			update_option( GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD, self::genesis_responsive_slider_defaults(), '', 'yes' );
		}
	}
}

/**
 * Creates read more link after excerpt.
 *
 * @param string $more Content.
 */
function genesis_responsive_slider_excerpt_more( $more ) {
	global $post;
	static $read_more = null;

	if ( null === $read_more ) {
		$read_more = Genesis_Responsive_Slider::genesis_get_responsive_slider_option( 'slideshow_more_text' );
	}

	if ( ! $read_more ) {
		return '';
	}

	return '&hellip; <a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . $read_more . '</a>';
}
