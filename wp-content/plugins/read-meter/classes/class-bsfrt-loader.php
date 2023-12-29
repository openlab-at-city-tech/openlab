<?php
/**
 * BSF Read Meter Loader Doc comment
 *
 * PHP version 7
 *
 * @category PHP
 * @package  Read Meter
 * @author   Display Name <username@rajkiranb.com>
 * @license  http://brainstormforce.com
 * @link     http://brainstormforce.com
 */

if ( ! class_exists( 'BSFRT_Loader' ) ) :
	/**
	 * Read Meter Loader Doc comment
	 *
	 * PHP version 7
	 *
	 * @category PHP
	 * @package  Read Meter
	 * @author   Display Name <username@rajkiranb.com>
	 * @license  http://brainstormforce.com
	 * @link     http://brainstormforce.com
	 */
	class BSFRT_Loader {
		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;
		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'bsfrt_pluginstyle_frontend' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'bsfrt_pluginstyle_dashboard' ) );

			add_action( 'init', array( $this, 'bsf_rt_process_form_general_settings' ) );

			add_action( 'init', array( $this, 'bsf_rt_process_form_read_time_settings' ) );

			add_action( 'init', array( $this, 'bsf_rt_process_form_progress_bar_settings' ) );

		}
		/**
		 * Process plugin's General setting Tab form Data.
		 *
		 * @return Nothing.
		 */
		public function bsf_rt_process_form_general_settings() {

			require_once BSF_RT_ABSPATH . 'includes/bsf-rt-page.php';

			$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;

			if ( 'bsf_rt' !== $page ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( ! isset( $_POST['bsf-rt-general'] ) || ! wp_verify_nonce( $_POST['bsf-rt-general'], 'bsf-rt-nonce-general' ) ) {
				return;
			}

			$bsf_rt_words_per_minute = ( ! empty( $_POST['bsf_rt_words_per_minute'] ) ? intval( $_POST['bsf_rt_words_per_minute'] ) : '' );

			$bsf_rt_post_types_array = ( ! empty( $_POST['posts'] ) ? $_POST['posts'] : array() );
			$bsf_rt_post_types       = array();
			if ( ! empty( $bsf_rt_post_types_array ) ) {

				foreach ( $bsf_rt_post_types_array as $key ) {
					// Sanitizing each element of array separately and then storing them.
					array_push( $bsf_rt_post_types, filter_var( $key, FILTER_SANITIZE_STRING ) );
				}
			}

			$bsf_rt_include_images = ( ! empty( $_POST['bsf_rt_include_images'] ) ? sanitize_text_field( $_POST['bsf_rt_include_images'] ) : '' );

			$bsf_rt_include_comments = ( ! empty( $_POST['bsf_rt_include_comments'] ) ? sanitize_text_field( $_POST['bsf_rt_include_comments'] ) : '' );

			$update_options = array(
				'bsf_rt_words_per_minute' => $bsf_rt_words_per_minute,
				'bsf_rt_post_types'       => $bsf_rt_post_types,
				'bsf_rt_include_comments' => $bsf_rt_include_comments,
				'bsf_rt_include_images'   => $bsf_rt_include_images,

			);
			update_option( 'bsf_rt_general_settings', $update_options );

			update_option( 'bsf_rt_saved_msg', 'ok' );
		}
		/**
		 * Process plugin's Read time setting Tab form Data.
		 *
		 * @return Nothing.
		 */
		public function bsf_rt_process_form_read_time_settings() {

			$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;

			if ( 'bsf_rt' !== $page ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( ! isset( $_POST['bsf-rt-reading'] ) || ! wp_verify_nonce( $_POST['bsf-rt-reading'], 'bsf-rt-nonce-reading' ) ) {
				return;
			}

			$bsf_rt_position_of_read_time      = sanitize_text_field( $_POST['bsf_rt_position_of_read_time'] );
			$bsf_rt_read_time_background_color = sanitize_hex_color( $_POST['bsf_rt_read_time_background_color'] );
			$bsf_rt_read_time_color            = sanitize_hex_color( $_POST['bsf_rt_read_time_color'] );
			$bsf_rt_padding_unit               = sanitize_text_field( $_POST['bsf_rt_padding_unit'] );
			$bsf_rt_margin_unit                = sanitize_text_field( $_POST['bsf_rt_margin_unit'] );

			$bsf_rt_reading_time_label = ( ! empty( $_POST['bsf_rt_reading_time_prefix_label'] ) ? sanitize_text_field( $_POST['bsf_rt_reading_time_prefix_label'] ) : '' );

			$bsf_rt_reading_time_postfix_label = ( ! empty( $_POST['bsf_rt_reading_time_postfix_label'] ) ? sanitize_text_field( $_POST['bsf_rt_reading_time_postfix_label'] ) : '' );

			$bsf_rt_readtime_post_types_array = ( ! empty( $_POST['bsf_rt_show_read_time'] ) ? $_POST['bsf_rt_show_read_time'] : array() );
			$bsf_rt_show_read_time            = array();
			if ( ! empty( $bsf_rt_readtime_post_types_array ) ) {
					// Sanitizing each element of array separately and then storing them.
				foreach ( $bsf_rt_readtime_post_types_array as $key ) {

					array_push( $bsf_rt_show_read_time, filter_var( $key, FILTER_SANITIZE_STRING ) );
				}
			}

			$bsf_rt_read_time_font_size = ( ! empty( $_POST['bsf_rt_read_time_font_size'] ) ? floatval( $_POST['bsf_rt_read_time_font_size'] ) : 10 );

			$bsf_rt_read_time_margin_top = ( ! empty( $_POST['bsf_rt_read_time_margin_top'] ) ? floatval( $_POST['bsf_rt_read_time_margin_top'] ) : 0 );

			$bsf_rt_read_time_margin_right = ( ! empty( $_POST['bsf_rt_read_time_margin_right'] ) ? floatval( $_POST['bsf_rt_read_time_margin_right'] ) : 0 );

			$bsf_rt_read_time_margin_bottom = ( ! empty( $_POST['bsf_rt_read_time_margin_bottom'] ) ? floatval( $_POST['bsf_rt_read_time_margin_bottom'] ) : 0 );

			$bsf_rt_read_time_margin_left = ( ! empty( $_POST['bsf_rt_read_time_margin_left'] ) ? floatval( $_POST['bsf_rt_read_time_margin_left'] ) : 0 );

			$bsf_rt_read_time_padding_top = ( ! empty( $_POST['bsf_rt_read_time_padding_top'] ) ? floatval( $_POST['bsf_rt_read_time_padding_top'] ) : 0 );

			$bsf_rt_read_time_padding_right = ( ! empty( $_POST['bsf_rt_read_time_padding_right'] ) ? floatval( $_POST['bsf_rt_read_time_padding_right'] ) : 0 );

			$bsf_rt_read_time_padding_bottom = ( ! empty( $_POST['bsf_rt_read_time_padding_bottom'] ) ? floatval( $_POST['bsf_rt_read_time_padding_bottom'] ) : 0 );

			$bsf_rt_read_time_padding_left = ( ! empty( $_POST['bsf_rt_read_time_padding_left'] ) ? floatval( $_POST['bsf_rt_read_time_padding_left'] ) : 0 );

			$update_options = array(
				'bsf_rt_reading_time_label'         => $bsf_rt_reading_time_label,
				'bsf_rt_reading_time_postfix_label' => $bsf_rt_reading_time_postfix_label,
				'bsf_rt_position_of_read_time'      => $bsf_rt_position_of_read_time,
				'bsf_rt_show_read_time'             => $bsf_rt_show_read_time,
				'bsf_rt_position_of_read_time'      => $bsf_rt_position_of_read_time,
				'bsf_rt_read_time_background_color' => $bsf_rt_read_time_background_color,
				'bsf_rt_read_time_color'            => $bsf_rt_read_time_color,
				'bsf_rt_read_time_font_size'        => $bsf_rt_read_time_font_size,
				'bsf_rt_read_time_margin_top'       => $bsf_rt_read_time_margin_top,
				'bsf_rt_read_time_margin_right'     => $bsf_rt_read_time_margin_right,
				'bsf_rt_read_time_margin_bottom'    => $bsf_rt_read_time_margin_bottom,
				'bsf_rt_read_time_margin_left'      => $bsf_rt_read_time_margin_left,
				'bsf_rt_read_time_padding_top'      => $bsf_rt_read_time_padding_top,
				'bsf_rt_read_time_padding_right'    => $bsf_rt_read_time_padding_right,
				'bsf_rt_read_time_padding_bottom'   => $bsf_rt_read_time_padding_bottom,
				'bsf_rt_read_time_padding_left'     => $bsf_rt_read_time_padding_left,
				'bsf_rt_padding_unit'               => $bsf_rt_padding_unit,
				'bsf_rt_margin_unit'                => $bsf_rt_margin_unit,
			);

			update_option( 'bsf_rt_read_time_settings', $update_options );
			update_option( 'bsf_rt_saved_msg', 'ok' );
		}
		/**
		 * Process plugin's Progress Bar setting Tab form Data.
		 *
		 * @return Nothing.
		 */
		public function bsf_rt_process_form_progress_bar_settings() {

			$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : null;

			if ( 'bsf_rt' !== $page ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( ! isset( $_POST['bsf-rt-progress'] ) || ! wp_verify_nonce( $_POST['bsf-rt-progress'], 'bsf-rt-nonce-progress' ) ) {
				return;
			}

			$bsf_rt_position_of_progress_bar = sanitize_text_field( $_POST['bsf_rt_position_of_progress_bar'] );

			$bsf_rt_progress_bar_background_color = sanitize_hex_color( $_POST['bsf_rt_progress_bar_background_color'] );

			$bsf_rt_progress_bar_thickness = floatval( $_POST['bsf_rt_progress_bar_thickness'] );

			$bsf_rt_progress_bar_styles = sanitize_text_field( $_POST['bsf_rt_progress_bar_styles'] );

			$bsf_rt_progress_bar_gradiant_one = sanitize_hex_color( $_POST['bsf_rt_progress_bar_color_g1'] );

			$bsf_rt_progress_bar_gradiant_two = sanitize_hex_color( $_POST['bsf_rt_progress_bar_color_g2'] );

			$update_options = array(
				'bsf_rt_position_of_progress_bar'      => $bsf_rt_position_of_progress_bar,
				'bsf_rt_progress_bar_styles'           => $bsf_rt_progress_bar_styles,
				'bsf_rt_progress_bar_background_color' => $bsf_rt_progress_bar_background_color,
				'bsf_rt_progress_bar_gradiant_one'     => $bsf_rt_progress_bar_gradiant_one,
				'bsf_rt_progress_bar_gradiant_two'     => $bsf_rt_progress_bar_gradiant_two,
				'bsf_rt_progress_bar_thickness'        => $bsf_rt_progress_bar_thickness,
			);

			update_option( 'bsf_rt_progress_bar_settings', $update_options );
			update_option( 'bsf_rt_saved_msg', 'ok' );
		}


		/**
		 * Plugin Styles for frontend.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function bsfrt_pluginstyle_frontend() {
			$option = get_option( 'bsf_rt_general_settings' );
			if ( empty( $option['bsf_rt_include_comments'] ) ) {

				$option['bsf_rt_include_comments'] = '';
			}
			wp_register_style( 'bsfrt_frontend', BSF_RT_PLUGIN_URL . '/assets/min-css/bsfrt-frontend-css.min.css', null, BSF_RT_VER );
			wp_register_script( 'bsfrt_frontend', BSF_RT_PLUGIN_URL . '/assets/min-js/bsf-rt-frontend.min.js', null, BSF_RT_VER, false );

			wp_localize_script( 'bsfrt_frontend', 'myObj', array( 'option' => $option['bsf_rt_include_comments'] ) );

		}
		/**
		 * Plugin Styles for admin dashboard.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function bsfrt_pluginstyle_dashboard() {
			wp_enqueue_style( 'wp-color-picker' );

			wp_register_style( 'bsfrt_dashboard', BSF_RT_PLUGIN_URL . '/assets/min-css/bsfrt-admin-dashboard-css.min.css', null, BSF_RT_VER );

			wp_register_script( 'bsfrt_backend', BSF_RT_PLUGIN_URL . '/assets/min-js/bsf-rt-backend.min.js', null, BSF_RT_VER, false );

			wp_register_script( 'colorpickerscript', BSF_RT_PLUGIN_URL . '/assets/min-js/color-picker.min.js', array( 'jquery', 'wp-color-picker' ), BSF_RT_VER, true );
		}
	}
	BSFRT_Loader::get_instance();
endif;

