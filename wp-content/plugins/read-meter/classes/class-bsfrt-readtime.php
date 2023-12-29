<?php
/**
 * The Read meter Main Class
 *
 * @since      1.0.0
 * @package    BSF
 * @author     Brainstorm Force.
 */

/**
 * Class for calculating reading time.
 *
 * The class that contains all functions for calculating reading time.
 *
 * @since 1.0.0
 */
class BSFRT_ReadTime {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Member Variable
	 *
	 * @var reading_time
	 */
	public $reading_time;

	/**
	 * Member Variable
	 *
	 * @var bsf_rt_options
	 */
	public $bsf_rt_options = array();

	/**
	 * Member Varaible
	 *
	 * @var bsf_rt_check_the_page
	 */
	public static $bsf_rt_check_the_page;

	/**
	 * Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Construct function for Read Meter.
	 * Create default settings on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->bsf_rt_init_backend();

		add_action( 'wp', array( $this, 'bsf_rt_init_frontend' ) );

		// Shortcode.
		add_shortcode( 'read_meter', array( $this, 'read_meter_shortcode' ) );

		add_filter( 'comments_template', array( $this, 'bsf_rt_remove_the_title_from_comments' ) );
	}

	/**
	 * Backend settings.
	 */
	public function bsf_rt_init_backend() {
		$bsf_rt_show_read_time = array( 'bsf_rt_single_page' );

		$bsf_rt_posts = array( 'post' );

		$bsf_rt_show_read_time = array( 'bsf_rt_single_page' );

		$default_options_general = array(
			'bsf_rt_words_per_minute' => '275',
			'bsf_rt_post_types'       => $bsf_rt_posts,
		);
		add_option( 'bsf_rt_general_settings', $default_options_general );

		$default_options_readtime = array(
			'bsf_rt_show_read_time'             => $bsf_rt_show_read_time,
			'bsf_rt_reading_time_label'         => 'Reading Time',
			'bsf_rt_reading_time_postfix_label' => 'mins',
			'bsf_rt_words_per_minute'           => '275',
			'bsf_rt_position_of_read_time'      => 'above_the_content',
			'bsf_rt_read_time_background_color' => '#eeeeee',
			'bsf_rt_read_time_color'            => '#333333',
			'bsf_rt_read_time_font_size'        => 15,
			'bsf_rt_read_time_margin_top'       => 1,
			'bsf_rt_read_time_margin_right'     => 1,
			'bsf_rt_read_time_margin_bottom'    => 1,
			'bsf_rt_read_time_margin_left'      => 1,
			'bsf_rt_read_time_padding_top'      => 0.5,
			'bsf_rt_read_time_padding_right'    => 0.7,
			'bsf_rt_read_time_padding_bottom'   => 0.5,
			'bsf_rt_read_time_padding_left'     => 0.7,
			'bsf_rt_padding_unit'               => 'em',
			'bsf_rt_margin_unit'                => 'px',
		);
		add_option( 'bsf_rt_read_time_settings', $default_options_readtime );

		$default_options_progressbar = array(
			'bsf_rt_position_of_progress_bar'      => 'none',
			'bsf_rt_progress_bar_styles'           => 'Normal',
			'bsf_rt_progress_bar_background_color' => '#e8d5ff',
			'bsf_rt_progress_bar_gradiant_one'     => '#5540D9',
			'bsf_rt_progress_bar_gradiant_two'     => '#ee7fff',
		);
		add_option( 'bsf_rt_progress_bar_settings', $default_options_progressbar );

		$this->bsf_rt_set_options();

	}

	/**
	 * Setter function.
	 *
	 * @since 1.0.2
	 */
	public function bsf_rt_set_options() {
		$bsf_rt_general_settings      = get_option( 'bsf_rt_general_settings' );
		$bsf_rt_read_time_settings    = get_option( 'bsf_rt_read_time_settings' );
		$bsf_rt_progress_bar_settings = get_option( 'bsf_rt_progress_bar_settings' );

		if ( isset( $bsf_rt_general_settings ) && '' !== $bsf_rt_read_time_settings && isset( $bsf_rt_progress_bar_settings ) ) {
			$all_options = array_merge( $bsf_rt_general_settings, $bsf_rt_read_time_settings );
			$all_options = array_merge( $all_options, $bsf_rt_progress_bar_settings );
		}

		$this->bsf_rt_options = $all_options;
	}
	/**
	 * Getter Function.
	 *
	 * @since 1.0.2
	 * @param String  $key key of that option array to get.
	 * @param boolean $default default value to set to it.
	 * @return string value of variable.
	 */
	public function bsf_rt_get_option( $key, $default = false ) {

		if ( isset( $this->bsf_rt_options[ $key ] ) && '' !== $this->bsf_rt_options[ $key ] ) {
			return $this->bsf_rt_options[ $key ];
		}

		return $default;
	}

	/**
	 * Frontend settings.
	 */
	public function bsf_rt_init_frontend() {

		if ( false === $this->bsf_rt_check_selected_post_types() ) {

			return;
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'bsfrt_frontend_default_css' ) );
		add_filter( 'comments_template', array( $this, 'bsf_rt_marker_for_progressbar' ) );
		add_filter( 'the_content', array( $this, 'bsf_rt_add_marker_for_progress_bar_scroll' ), 90 );

		if ( 'none' !== $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

			if ( 'above_the_content' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {
				// Read time styles.
				add_action( 'wp_head', array( $this, 'bsf_rt_set_readtime_styles_content' ) );
			} else {

				add_action( 'wp_head', array( $this, 'bsf_rt_set_readtime_styles' ) );
			}
		}

		// For twenty fifteen Theme remove the extra markup in the nextpost and prev post section.
		$bsf_rt_current_theme = $this->bsf_rt_get_current_theme();

		if ( 'Twenty Fifteen' === $bsf_rt_current_theme || 'Twenty Nineteen' === $bsf_rt_current_theme || 'Twenty Thirteen' === $bsf_rt_current_theme || 'Twenty Fourteen' === $bsf_rt_current_theme || 'Twenty Sixteen' === $bsf_rt_current_theme || 'Twenty Seventeen' === $bsf_rt_current_theme || 'Twenty Twelve' === $bsf_rt_current_theme ) {
			add_filter( 'next_post_link', array( $this, 'bsf_rt_remove_markup_for_twenty_series' ) );
			add_filter( 'previous_post_link', array( $this, 'bsf_rt_remove_markup_for_twenty_series' ) );
		}

		// Show Reading time Conditions.
		if ( $this->bsf_rt_get_option( 'bsf_rt_show_read_time' ) && 'none' !== $this->bsf_rt_options['bsf_rt_position_of_read_time'] ) {
			if ( in_array( 'bsf_rt_single_page', $this->bsf_rt_options['bsf_rt_show_read_time'] ) && is_singular() ) {//PHPCS:ignore:WordPress.PHP.StrictInArray.MissingTrueStrict

				if ( 'above_the_content' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

					add_filter( 'the_content', array( $this, 'bsf_rt_add_reading_time_before_content' ), 90 );
				}
				if ( 'above_the_post_title' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

					add_filter( 'the_title', array( $this, 'bsf_rt_add_reading_time_above_the_post_title' ), 90, 2 );
				}
				if ( 'below_the_post_title' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

					add_filter( 'the_title', array( $this, 'bsf_rt_add_reading_time_below_the_post_title' ), 90 );
				}
			}
			if ( in_array( 'bsf_rt_home_blog_page', $this->bsf_rt_options['bsf_rt_show_read_time'] ) && is_home() && ! is_archive() ) { //PHPCS:ignore:WordPress.PHP.StrictInArray.MissingTrueStrict

				if ( 'above_the_content' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

					add_filter( 'get_the_excerpt', array( $this, 'bsf_rt_add_reading_time_before_content_excerpt' ), 1000 );
					if ( 'Twenty Fifteen' === $bsf_rt_current_theme || 'Twenty Nineteen' === $bsf_rt_current_theme || 'Twenty Thirteen' === $bsf_rt_current_theme || 'Twenty Fourteen' === $bsf_rt_current_theme || 'Twenty Sixteen' === $bsf_rt_current_theme || 'Twenty Seventeen' === $bsf_rt_current_theme || 'Twenty Twelve' === $bsf_rt_current_theme ) {
						add_filter( 'the_content', array( $this, 'bsf_rt_add_reading_time_before_content_excerpt' ), 1000 );
					}
				}
				if ( 'above_the_post_title' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

					add_filter( 'the_title', array( $this, 'bsf_rt_add_reading_time_before_title_excerpt' ), 1000 );
				}
				if ( 'below_the_post_title' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

					add_filter( 'the_title', array( $this, 'bsf_rt_add_reading_time_after_title_excerpt' ), 1000 );
				}
			}
			if ( in_array( 'bsf_rt_archive_page', $this->bsf_rt_options['bsf_rt_show_read_time'] ) && ! is_home() && is_archive() ) { //PHPCS:ignore:WordPress.PHP.StrictInArray.MissingTrueStrict

				if ( 'above_the_content' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

					add_filter( 'get_the_excerpt', array( $this, 'bsf_rt_add_reading_time_before_content_archive' ), 1000 );

					if ( 'Twenty Fifteen' === $bsf_rt_current_theme || 'Twenty Nineteen' === $bsf_rt_current_theme || 'Twenty Thirteen' === $bsf_rt_current_theme || 'Twenty Fourteen' === $bsf_rt_current_theme || 'Twenty Sixteen' === $bsf_rt_current_theme || 'Twenty Seventeen' === $bsf_rt_current_theme || 'Twenty Twelve' === $bsf_rt_current_theme ) {
						add_filter( 'the_content', array( $this, 'bsf_rt_add_reading_time_before_content_archive' ), 1000 );
					}
				}
				if ( 'above_the_post_title' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

					add_filter( 'the_title', array( $this, 'bsf_rt_add_reading_time_before_title_archive' ), 1000 );
				}
				if ( 'below_the_post_title' === $this->bsf_rt_get_option( 'bsf_rt_position_of_read_time' ) ) {

					add_filter( 'the_title', array( $this, 'bsf_rt_add_reading_time_after_title_archive' ), 1000 );
				}
			}
		}
		// Displaying Progress Bar Conditions.
		if ( 'none' === $this->bsf_rt_get_option( 'bsf_rt_position_of_progress_bar' ) ) {

			return;

		} elseif ( 'top_of_the_page' === $this->bsf_rt_get_option( 'bsf_rt_position_of_progress_bar' ) ) {

			add_action( 'wp_footer', array( $this, 'hook_header_top' ) );

		} elseif ( 'bottom_of_the_page' === $this->bsf_rt_get_option( 'bsf_rt_position_of_progress_bar' ) ) {

			add_action( 'wp_footer', array( $this, 'hook_header_bottom' ) );

		}

		if ( 'Normal' === $this->bsf_rt_get_option( 'bsf_rt_progress_bar_styles' ) ) {

				add_action( 'wp_head', array( $this, 'bsf_rt_set_progressbar_colors_normal' ) );

		} elseif ( 'Gradient' === $this->bsf_rt_get_option( 'bsf_rt_progress_bar_styles' ) ) {

				add_action( 'wp_head', array( $this, 'bsf_rt_set_progressbar_colors_gradient' ) );
		}
	}

	/**
	 * Adds the reading time before the_content.
	 *
	 * If the options is selected to automatically add the reading time before
	 * the_content, the reading time is calculated and added to the beginning of the_content.
	 *
	 * @since 1.0.0
	 * @param  string $content The original post content.
	 * @return string The post content with reading time prepended.
	 */
	public function bsf_rt_add_reading_time_before_content( $content ) {
		if ( in_the_loop() && is_singular() ) {

			$original_content = $content;

			$bsf_rt_post = get_the_ID();

			$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

			$label              = $this->bsf_rt_options['bsf_rt_reading_time_label'];
			$postfix            = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];
			$calculated_postfix = $postfix;
			$content            = '<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . esc_attr( $label ) . '"></span> <span class="bsf-rt-display-time" reading_time="' . esc_attr( $this->reading_time ) . '"></span> <span class="bsf-rt-display-postfix" postfix="' . esc_attr( $calculated_postfix ) . '"></span></span>';
			$content           .= $original_content;
			return $content;
		} else {

			return $content;
		}
	}

	/**
	 * Adds the reading time above the post title.
	 *
	 * @since 1.0.0
	 * @param  string $title The original post content.
	 * @return string The post content with reading time prepended.
	 */
	public function bsf_rt_add_reading_time_above_the_post_title( $title ) {

		if ( in_the_loop() && is_singular() ) {

			$original_title = $title;

			$bsf_rt_post = get_the_ID();

			$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

			$label   = $this->bsf_rt_options['bsf_rt_reading_time_label'];
			$postfix = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];

			$calculated_postfix = $postfix;
			$title              = '
<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . esc_attr( $label ) . '"></span> <span class="bsf-rt-display-time" reading_time="' . esc_attr( $this->reading_time ) . '"></span> <span class="bsf-rt-display-postfix" postfix="' . esc_attr( $calculated_postfix ) . '"></span></span><!-- .bsf-rt-reading-time -->';

			$title .= $original_title;

			return $title;

		} else {

			return $title;
		}

	}

	/**
	 * Adds the reading time below the post title.
	 *
	 * @since 1.0.0
	 * @param  string $title The original post title.
	 * @return string The post title with reading time prepended.
	 */
	public function bsf_rt_add_reading_time_below_the_post_title( $title ) {
		if ( in_the_loop() && is_singular() ) {

			$original_title = $title;

			$bsf_rt_post = get_the_ID();

			$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

			$label   = $this->bsf_rt_options['bsf_rt_reading_time_label'];
			$postfix = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];

			$calculated_postfix = $postfix;

			$title = '
<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . esc_attr( $label ) . '"></span> <span class="bsf-rt-display-time" reading_time="' . esc_attr( $this->reading_time ) . '"></span> <span class="bsf-rt-display-postfix" postfix="' . esc_attr( $calculated_postfix ) . '"></span></span><!-- .bsf-rt-reading-time -->';

			$original_title .= $title;

			$title = $original_title;

			return $title;

		} else {

			return $title;
		}

	}

	/**
	 * Adds the reading time before the_excerpt content.
	 *
	 * If the options is selected to automatically add the reading time before
	 * the_excerpt, the reading time is calculated and added to the beginning of the_excerpt.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $excerpt The original content of the_excerpt.
	 */
	public function bsf_rt_add_reading_time_before_content_excerpt( $excerpt ) {
		if ( in_the_loop() && is_home() && ! is_archive() ) {

			$original_excerpt = $excerpt;

			$bsf_rt_post = get_the_ID();

			$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

			$label   = $this->bsf_rt_options['bsf_rt_reading_time_label'];
			$postfix = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];

			$calculated_postfix = $postfix;

			$excerpt = '
<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . esc_attr( $label ) . '"></span> <span class="bsf-rt-display-time" reading_time="' . esc_attr( $this->reading_time ) . '"></span> <span class="bsf-rt-display-postfix" postfix="' . esc_attr( $calculated_postfix ) . '"></span></span>';

			$excerpt .= $original_excerpt;

			echo $excerpt; //PHPCS:ignore:WordPress.XSS.EscapeOutput.OutputNotEscaped

		} else {

			echo $excerpt; //PHPCS:ignore:WordPress.XSS.EscapeOutput.OutputNotEscaped

		}
	}

	/**
	 * Adds the reading time before the_excerpt title.
	 *
	 * If the options is selected to automatically add the reading time before
	 * the_excerpt, the reading time is calculated and added to the beginning of the_excerpt.
	 *
	 * @since 1.0.0
	 * @param  string $title The original content of the_excerpt.
	 * @return string The excerpt content with reading time prepended.
	 */
	public function bsf_rt_add_reading_time_before_title_excerpt( $title ) {
		if ( in_the_loop() && is_home() && ! is_archive() ) {

			$original_title = $title;

			$bsf_rt_post = get_the_ID();

			$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

			$label   = $this->bsf_rt_options['bsf_rt_reading_time_label'];
			$postfix = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];

			$calculated_postfix = $postfix;

			$title = '
<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . esc_attr( $label ) . '"></span> <span class="bsf-rt-display-time" reading_time="' . esc_attr( $this->reading_time ) . '"></span> <span class="bsf-rt-display-postfix" postfix="' . esc_attr( $calculated_postfix ) . '"></span></span>';

			$title .= $original_title;

			return $title;
		} else {

			return $title;
		}
	}

	/**
	 * Adds the reading time after the_excerpt title.
	 *
	 * If the options is selected to automatically add the reading time before
	 * the_excerpt, the reading time is calculated and added to the beginning of the_excerpt.
	 *
	 * @since 1.0.0
	 * @param  string $title The original content of the_excerpt.
	 * @return string The excerpt content with reading time prepended.
	 */
	public function bsf_rt_add_reading_time_after_title_excerpt( $title ) {
		if ( in_the_loop() && is_home() && ! is_archive() ) {

			$original_title = $title;

			$bsf_rt_post = get_the_ID();

			$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

			$label   = $this->bsf_rt_options['bsf_rt_reading_time_label'];
			$postfix = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];

			$calculated_postfix = $postfix;

			$title = ' 
<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . esc_attr( $label ) . '"></span> <span class="bsf-rt-display-time" reading_time="' . esc_attr( $this->reading_time ) . '"></span> <span class="bsf-rt-display-postfix" postfix="' . esc_attr( $calculated_postfix ) . '"></span></span>';

			$original_title .= $title;

			$title = $original_title;

			return $title;
		} else {

			return $title;
		}
	}

	/**
	 * Adds the reading time before the archive excerpt.
	 *
	 * If the options is selected to automatically add the reading time before
	 * the_excerpt, the reading time is calculated and added to the beginning of the_excerpt.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $excerpt The original content of the_excerpt.
	 */
	public function bsf_rt_add_reading_time_before_content_archive( $excerpt ) {
		if ( in_the_loop() && is_archive() ) {

			$original_excerpt = $excerpt;

			$bsf_rt_post = get_the_ID();

			$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

			$label   = $this->bsf_rt_options['bsf_rt_reading_time_label'];
			$postfix = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];

			$calculated_postfix = $postfix;

			$excerpt = '
<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . esc_attr( $label ) . '"></span> <span class="bsf-rt-display-time" reading_time="' . esc_attr( $this->reading_time ) . '"></span> <span class="bsf-rt-display-postfix" postfix="' . esc_attr( $calculated_postfix ) . '"></span></span>';

			$excerpt .= $original_excerpt;

			echo $excerpt; //PHPCS:ignore:WordPress.XSS.EscapeOutput.OutputNotEscaped

		} else {

			echo $excerpt; //PHPCS:ignore:WordPress.XSS.EscapeOutput.OutputNotEscaped

		}
	}

	/**
	 * Adds the reading time before the archive title.
	 *
	 * If the options is selected to automatically add the reading time before
	 * the_excerpt, the reading time is calculated and added to the beginning of the_excerpt.
	 *
	 * @since 1.0.0
	 * @param  string $title The original content of the_excerpt.
	 * @return string The excerpt content with reading time prepended.
	 */
	public function bsf_rt_add_reading_time_before_title_archive( $title ) {
		if ( in_the_loop() && is_archive() ) {

			$original_title = $title;

			$bsf_rt_post = get_the_ID();

			$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

			$label   = $this->bsf_rt_options['bsf_rt_reading_time_label'];
			$postfix = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];

			$calculated_postfix = $postfix;

			$title = '
<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . esc_attr( $label ) . '"></span> <span class="bsf-rt-display-time" reading_time="' . esc_attr( $this->reading_time ) . '"></span> <span class="bsf-rt-display-postfix" postfix="' . esc_attr( $calculated_postfix ) . '"></span></span>';

			$title .= $original_title;

			return $title;

		} else {

			return $title;
		}

	}

	/**
	 * Adds the reading time after the archive title.
	 *
	 * If the options is selected to automatically add the reading time before
	 * the_excerpt, the reading time is calculated and added to the beginning of the_excerpt.
	 *
	 * @since 1.0.0
	 * @param  string $title The original content of the_excerpt.
	 * @return string The excerpt content with reading time prepended.
	 */
	public function bsf_rt_add_reading_time_after_title_archive( $title ) {
		if ( in_the_loop() && is_archive() ) {

			$original_title = $title;

			$bsf_rt_post = get_the_ID();

			$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

			$label   = $this->bsf_rt_options['bsf_rt_reading_time_label'];
			$postfix = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];

			$calculated_postfix = $postfix;

			$title = '
<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . esc_attr( $label ) . '"></span> <span class="bsf-rt-display-time" reading_time="' . esc_attr( $this->reading_time ) . '"></span> <span class="bsf-rt-display-postfix" postfix="' . esc_attr( $calculated_postfix ) . '"></span></span>';

			$original_title .= $title;

			$title = $original_title;

			return $title;

		} else {

			return $title;
		}
	}

	/**
	 * Calculate the reading time of a post.
	 *
	 * Gets the post content, counts the images, strips shortcodes, and strips tags.
	 * Then counds the words. Converts images into a word coun and outputs the total reading time.
	 *
	 * @since 1.0.0
	 * @param  int   $bsf_rt_post The Post ID.
	 * @param  array $bsf_rt_options The options selected for the plugin.
	 * @return string|int The total reading time for the article or string if it's 0.
	 */
	public function bsf_rt_calculate_reading_time( $bsf_rt_post, $bsf_rt_options ) {

		$bsf_rt_current_post_type = get_post_type();

		if ( 'post' === $bsf_rt_current_post_type ) {

			if ( in_the_loop() && is_singular() ) {

				$args = array(

					'post_id' => $bsf_rt_post, // use post_id, not post_ID.

				);
				$comments = get_comments( $args );

				$comment_string = '';

				foreach ( $comments as $comment ) {

					$comment_string = $comment_string . ' ' . $comment->comment_content;
				}

				$comment_word_count = ( count( preg_split( '/\s+/', $comment_string ) ) );

			} else {

				$comment_word_count = 0;
			}
		} else {

			$comment_word_count = 0;
		}
		$bsf_rt_content = get_post_field( 'post_content', $bsf_rt_post );

		$number_of_images = substr_count( strtolower( $bsf_rt_content ), '<img ' );

		if ( ! isset( $this->bsf_rt_options['include_shortcodes'] ) ) {

			$bsf_rt_content = strip_shortcodes( $bsf_rt_content );
		}

		$bsf_rt_content = wp_strip_all_tags( $bsf_rt_content );

		$word_count = count( preg_split( '/\s+/', $bsf_rt_content ) );

		if ( isset( $this->bsf_rt_options['bsf_rt_include_comments'] ) && 'yes' === $this->bsf_rt_options['bsf_rt_include_comments'] ) {

			$word_count += $comment_word_count;
		}

		// Calculate additional time added to post by images.
		$additional_words_for_images = $this->bsf_rt_calculate_images( $number_of_images, $this->bsf_rt_options['bsf_rt_words_per_minute'] );

		if ( isset( $this->bsf_rt_options['bsf_rt_include_images'] ) && 'yes' === $this->bsf_rt_options['bsf_rt_include_images'] ) {

			$word_count += $additional_words_for_images;
		}

		$this->reading_time = ceil( $word_count / $this->bsf_rt_options['bsf_rt_words_per_minute'] );

		// If the reading time is 0 then return it as < 1 instead of 0.
		if ( 1 > $this->reading_time ) {

			$this->reading_time = '< 1';
		}

		return $this->reading_time;
	}

	/**
	 * Adds additional reading time for images.
	 * Calculate additional reading time added by images in posts based on calculations by Medium. https://blog.medium.com/read-time-and-you-bc2048ab620c
	 *
	 * @since 1.1.0
	 * @param int   $total_images number of images in post.
	 * @param array $bsf_rt_words_per_minute words per minute.
	 * @return int Additional time added to the reading time by images.
	 */
	public function bsf_rt_calculate_images( $total_images, $bsf_rt_words_per_minute ) {
		$additional_time = 0;

		// For the first image add 12 seconds, second image add 11, ..., for image 10+ add 3 seconds.

		for ( $i = 1; $i <= $total_images; $i++ ) {
			if ( $i >= 10 ) {

				$additional_time += 3 * (int) $bsf_rt_words_per_minute / 60;
			} else {

				$additional_time += ( 12 - ( $i - 1 ) ) * (int) $bsf_rt_words_per_minute / 60;
			}
		}

		return $additional_time;
	}

	/**
	 * Adds the Progress Bar at the bottom.
	 *
	 * @since 1.0.0
	 */
	public function hook_header_bottom() {
		if ( ! is_home() && ! is_archive() && ! is_404() ) {
			wp_enqueue_script( 'bsfrt_frontend' );

			echo '<div id="bsf_rt_progress_bar_container" class="progress-container-bottom">
				 <div class="progress-bar" id="bsf_rt_progress_bar"></div>
				 </div>';
		}
	}

	/**
	 * Adds the Progress Bar at the top.
	 *
	 * @since 1.0.0
	 */
	public function hook_header_top() {
		if ( ! is_home() && ! is_archive() && ! is_404() ) {
			wp_enqueue_script( 'bsfrt_frontend' );

			echo '<div id="bsf_rt_progress_bar_container" class="progress-container-top">
				<div class="progress-bar" id="bsf_rt_progress_bar"></div>
				</div>';

		}

	}

	/**
	 * Function of the read_meter shortcode.
	 *
	 * @since 1.0.0
	 * @return shortcode display value.
	 */
	public function read_meter_shortcode() {
		$bsf_rt_post = get_the_ID();

		$this->bsf_rt_calculate_reading_time( $bsf_rt_post, $this->bsf_rt_options );

		$label              = $this->bsf_rt_options['bsf_rt_reading_time_label'];
		$postfix            = $this->bsf_rt_options['bsf_rt_reading_time_postfix_label'];
		$calculated_postfix = $postfix;

		$shortcode_output = '<span class="bsf-rt-reading-time"><span class="bsf-rt-display-label" prefix="' . $label . '">
		</span> <span class="bsf-rt-display-time" reading_time="' . $this->reading_time . '"></span> 
		<span class="bsf-rt-display-postfix" postfix="' . $calculated_postfix . '"></span></span>';

		return $shortcode_output;
	}

	/**
	 * Remove markup for Twenty fifteen.
	 *
	 * @since 1.0.0
	 * @param  string $output Markup of readtime div.
	 */
	public function bsf_rt_remove_markup_for_twenty_series( $output ) {
		$start_str = esc_html( '<span class="bsf-rt-reading-time">' );
		$end_str   = esc_html( '<!-- .bsf-rt-reading-time -->' );

		$newstr = preg_replace( '/' . preg_quote( $start_str ) . '.*?' . preg_quote( $end_str ) . '/', '', esc_html( $output ) );//PHPCS:ignore:WordPress.PHP.PregQuoteDelimiter.Missing

		return htmlspecialchars_decode( $newstr );
	}

	/**
	 * Get the current Theme Name.
	 *
	 * @since 1.0.0
	 * @return current theme name.
	 */
	public function bsf_rt_get_current_theme() {
		$theme_name = '';
		$theme      = wp_get_theme();

		if ( isset( $theme->parent_theme ) && '' != $theme->parent_theme || null != $theme->parent_theme ) {//PHPCS:ignore:WordPress.PHP.StrictComparisons.LooseComparison

			$theme_name = $theme->parent_theme;

		} else {

			$theme_name = $theme->name;
		}

		return $theme_name;
	}

	/**
	 * Removes our Reading time from the comments title.
	 *
	 * @since 1.0.0
	 */
	public function bsf_rt_remove_the_title_from_comments() {
		remove_filter( 'the_title', array( self::get_instance(), 'bsf_rt_add_reading_time_above_the_post_title' ), 90, 2 );

		remove_filter( 'the_title', array( self::get_instance(), 'bsf_rt_add_reading_time_below_the_post_title' ), 90, 2 );
	}

	/**
	 * Adds CSS to the progress Bar as per User input , When Style is Selected Normal.
	 *
	 * @since 1.1.0
	 */
	public function bsf_rt_set_progressbar_colors_normal() {        ?>
		<style type="text/css">
		.admin-bar .progress-container-top {
		background: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) : 'unset'; ?>;
		height: <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_thickness'] ); ?>px;

		}
		.progress-container-top {
		background: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) : 'unset'; ?>;
		height: <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_thickness'] ); ?>px;

		}
		.progress-container-bottom {
		background: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) : 'unset'; ?>;
		height: <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_thickness'] ); ?>px;

		} 
		.progress-bar {
		background: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_progress_bar_gradiant_one'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_gradiant_one'] ) : 'unset'; ?>;
		height: <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_thickness'] ); ?>px;
		width: 0%;

		}           
		</style>
				<?php
	}

	/**
	 * Adds CSS to the progress Bar as per User input , When Style is Selected Gradient.
	 *
	 * @since 1.1.0
	 */
	public function bsf_rt_set_progressbar_colors_gradient() {
		?>
		<style type="text/css">
		.admin-bar .progress-container-top {
		background: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) : 'unset'; ?>;
		height: <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_thickness'] ); ?>px;

		}
		.progress-container-top {
		background: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) : 'unset'; ?>;
		height: <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_thickness'] ); ?>px;

		}
		.progress-container-bottom {
		background: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_background_color'] ) : 'unset'; ?>;
		height: <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_thickness'] ); ?>px;

		} 
		.progress-bar {
		background-color:  <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_progress_bar_gradiant_one'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_gradiant_one'] ) : 'unset'; ?>;
		background-image: linear-gradient(to bottom right, <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_gradiant_one'] ); ?>, <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_gradiant_two'] ); ?>);
		height: <?php echo esc_attr( $this->bsf_rt_options['bsf_rt_progress_bar_thickness'] ); ?>px;
		width: 0%;


		}
		</style>
				<?php
	}

	/**
	 * Adds CSS to the Read Time as per User input if color.
	 *
	 * @since  1.1.0.
	 */
	public function bsf_rt_set_readtime_styles() {
		?>

	<style type="text/css">
	.bsf-rt-reading-time {

	background: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_background_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_background_color'] ) : 'unset'; ?>;

	color: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_color'] ) : 'unset'; ?>;

	font-size: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_font_size'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_font_size'] ) : 'unset'; ?>px;

	margin-top: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_margin_top'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_margin_top'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_margin_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_margin_unit'] ) : 'unset';
		?>
	;

	margin-right: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_margin_right'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_margin_right'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_margin_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_margin_unit'] ) : 'unset';
		?>
	;

	margin-bottom: 
		<?php

		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_margin_bottom'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_margin_bottom'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_margin_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_margin_unit'] ) : 'unset';
		?>
	;

	margin-left: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_margin_left'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_margin_left'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_margin_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_margin_unit'] ) : 'unset';
		?>
	;

	padding-top: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_padding_top'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_padding_top'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_padding_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_padding_unit'] ) : 'unset';
		?>
	;

	padding-right: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_padding_right'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_padding_right'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_padding_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_padding_unit'] ) : 'unset';
		?>
	;

	padding-bottom: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_padding_bottom'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_padding_bottom'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_padding_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_padding_unit'] ) : 'unset';
		?>
	;

	padding-left: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_padding_left'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_padding_left'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_padding_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_padding_unit'] ) : 'unset';
		?>
	;

	width: max-content;

	display: block;

	min-width: 100px;

	}

		<?php
	}

	/**
	 * Adds CSS to the Read Time as per User input if color and in above content.
	 *
	 * @since  1.1.0
	 */
	public function bsf_rt_set_readtime_styles_content() {

		?>

<style type="text/css">
.entry-content .bsf-rt-reading-time{
background: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_background_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_background_color'] ) : 'unset'; ?>;

color: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_color'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_color'] ) : 'unset'; ?>;

font-size: <?php echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_font_size'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_font_size'] ) : 'unset'; ?>px;

margin-top: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_margin_top'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_margin_top'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_margin_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_margin_unit'] ) : 'unset';
		?>
;

margin-right: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_margin_right'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_margin_right'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_margin_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_margin_unit'] ) : 'unset';
		?>
;

margin-bottom: 
		<?php

		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_margin_bottom'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_margin_bottom'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_margin_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_margin_unit'] ) : 'unset';
		?>
;

margin-left: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_margin_left'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_margin_left'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_margin_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_margin_unit'] ) : 'unset';
		?>
;

padding-top: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_padding_top'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_padding_top'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_padding_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_padding_unit'] ) : 'unset';
		?>
;

padding-right: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_padding_right'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_padding_right'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_padding_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_padding_unit'] ) : 'unset';
		?>
;

padding-bottom: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_padding_bottom'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_padding_bottom'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_padding_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_padding_unit'] ) : 'unset';
		?>
;

padding-left: 
		<?php
		echo ( '' !== $this->bsf_rt_options['bsf_rt_read_time_padding_left'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_read_time_padding_left'] ) : 'unset';
		echo ( '' !== $this->bsf_rt_options['bsf_rt_padding_unit'] ) ? esc_attr( $this->bsf_rt_options['bsf_rt_padding_unit'] ) : 'unset';
		?>
;

width: max-content;

display: block;

min-width: 100px;

}

</style>
		<?php
	}

	/**
	 * Adding Shortcode in Astra Theme hook.
	 *
	 * @since  1.1.0
	 */
	public function bsf_rt_add_reading_time_after_astra_header() {
		echo do_shortcode( '[read_meter]' );
	}

	/**
	 * Checking current page
	 *
	 * @since  1.1.0
	 */
	public function bsf_rt_check_the_page() {
		if ( is_singular() ) {

			self::$bsf_rt_check_the_page = 'single';
		} elseif ( is_home() ) {

			self::$bsf_rt_check_the_page = 'home';

		} elseif ( is_archive() ) {

			self::$bsf_rt_check_the_page = 'archive';

		}
	}

	/**
	 * Adding Marker for Progress Bar.
	 *
	 * @since  1.1.0
	 * @param  string $content content of post.
	 * @return content.
	 */
	public function bsf_rt_add_marker_for_progress_bar_scroll( $content ) {

		$markup_start = '<div id="bsf_rt_marker">';
		$markup_end   = '</div>';

		$content = $markup_start . $content . $markup_end;

		return $content;
	}

	/**
	 * Checking If the Current Post type is in the user selected Post types array.
	 *
	 * @since  1.1.0
	 * @return bool true/false.
	 */
	public function bsf_rt_check_selected_post_types() {
			// Get the post type of the current post.
			$bsf_rt_current_post_type = get_post_type();

			// If the current post type isn't included in the array of post types or it is and set to false, don't display it.
		if ( null === $this->bsf_rt_options['bsf_rt_post_types'] ) {

			return false;
		}

		if ( isset( $this->bsf_rt_options['bsf_rt_post_types'] ) && ! in_array( $bsf_rt_current_post_type, $this->bsf_rt_options['bsf_rt_post_types'] ) ) { //PHPCS:ignore:WordPress.PHP.StrictInArray.MissingTrueStrict

			return false;
		}

			return true;
	}

	/**
	 * Enqueue Plugin's style and script
	 *
	 * @since  1.1.0
	 */
	public function bsfrt_frontend_default_css() {
		wp_enqueue_style( 'bsfrt_frontend' );
	}

	/**
	 * Marker for progress bar
	 *
	 * @param string $template input of the filter.
	 * @return string $template for the purpose to execute comments.
	 * @since  1.1.0
	 */
	public function bsf_rt_marker_for_progressbar( $template ) {
		echo '<div id="bsf-rt-comments"></div>';
				$temp     = '<div id="bsf-rt-comments"></div>';
				$template = $template . $temp;
				return $template;
	}



}

BSFRT_ReadTime::get_instance();

