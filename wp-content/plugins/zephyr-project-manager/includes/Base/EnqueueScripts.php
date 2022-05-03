<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Zephyr;
use Inc\Core\Projects;
use Inc\Core\Utillities;
use Inc\Base\AjaxHandler;
use Inc\Base\BaseController;
use Inc\ZephyrProjectManager;

class EnqueueScripts {

	public static function register() {
		add_action( 'admin_enqueue_scripts', array( 'Inc\Base\EnqueueScripts', 'enqueue_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( 'Inc\Base\EnqueueScripts', 'enqueue_user_scripts' ) );
		add_filter( 'zpm_disable_scripts', array( 'Inc\Base\EnqueueScripts', 'handle_scripts' ) );
	}

	/**
	* Enqueue all admin scripts and styles
	*/
	public static function enqueue_admin_scripts($hook) {
		$version = '4.34.0';//Zephyr::getPluginVersion();
		$manager = ZephyrProjectManager::get_instance();

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
	    wp_register_style( 'linearicons', ZPM_PLUGIN_URL . 'assets/css/linearicons.css' );
	    wp_register_style( 'fullcalender_css', ZPM_PLUGIN_URL . 'assets/css/fullcalendar.css' );
	    // wp_register_style( 'zephyr-fontawesome','https://use.fontawesome.com/releases/v5.7.2/css/all.css' );
	    // wp_enqueue_script( 'zephyr-fontawesome' );
	    $custom_css = EnqueueScripts::custom_styles();

	    $rest_url = get_rest_url();
	    $handle = curl_init( $rest_url );
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($handle);
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		if ($httpCode == 404 || $response == false && strpos( $rest_url , 'localhost' ) !== false) {
			$rest_url = get_home_url() . '/index.php/wp-json/';
		}

		curl_close($handle);

		if (isZephyrPage()) {
			wp_register_style( 'jquery-ui-styles', '//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
			wp_enqueue_style( 'jquery-ui-styles' );
		}

	    wp_register_style( 'chosen_css', ZPM_PLUGIN_URL . 'assets/css/chosen.css' );
	    wp_enqueue_style( 'zpm-open-sans', '//fonts.googleapis.com/css?family=Roboto' );
	    wp_enqueue_style( 'linearicons' );
	    wp_enqueue_style( 'fullcalender_css' );
	    wp_enqueue_style( 'chosen_css' );
	    wp_enqueue_style( 'zpm-admin-styles', ZPM_PLUGIN_URL . 'assets/css/admin-styles.css', array(), $version );
	    wp_add_inline_style( 'zpm-admin-styles', $custom_css);

	    wp_enqueue_style( 'zpm-global-styles', ZPM_PLUGIN_URL . 'assets/css/zpm-global-styles.css', array(), $version );

	    wp_enqueue_style( 'zpm-admin-rtl-styles', ZPM_PLUGIN_URL . 'assets/css/zpm-rtl-styles.css', array(), $version );
		wp_enqueue_style( 'font-awesome', '//use.fontawesome.com/releases/v5.0.8/css/all.css' );

		if ( apply_filters( 'zpm_disable_scripts', false ) ) {
			return;
		}

		wp_register_style( 'zpm-mention-css', ZPM_PLUGIN_URL . 'assets/css/jquery.mentionsInput.css' );
		wp_enqueue_style( 'zpm-mention-css' );
	    wp_register_script( 'zpm-mention-js', ZPM_PLUGIN_URL . 'assets/js/jquery.mentionsInput.js', array( 'jquery', 'wp-util' ) );
	    wp_enqueue_script( 'zpm-mention-js' );

		// Scripts
		wp_enqueue_script('jquery-ui-resizable');
		wp_register_script( 'zpm_moment_js', ZPM_PLUGIN_URL . 'assets/js/moment.min.js', array( 'jquery' ) );
		wp_register_script( 'fullcalender_js', ZPM_PLUGIN_URL . 'assets/js/fullcalendar.js', array( 'jquery', 'moment' ) );
		wp_register_script( 'chosen_js', ZPM_PLUGIN_URL . 'assets/js/chosen.jquery.js', array( 'jquery' ) );
		wp_register_script( 'chartjs', ZPM_PLUGIN_URL . 'assets/js/chart.js', array( 'jquery' ) );
		wp_enqueue_script( 'chartjs' );

		wp_register_script( 'dragula', ZPM_PLUGIN_URL . 'assets/js/dragula/dragula.js', array( 'jquery' ) );
		wp_enqueue_script( 'dragula' );

		wp_register_script( 'zephyr-socket-io', ZPM_PLUGIN_URL . '/assets/js/socket.io.js' );
		wp_enqueue_script( 'zephyr-socket-io' );

		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'zpm_moment_js' );
		wp_register_script( 'jquery-ui-timepicker', ZPM_PLUGIN_URL . 'assets/js/jquery-timepicker.js', array( 'jquery', 'jquery-ui-datepicker' ) );
		wp_enqueue_script( 'jquery-ui-timepicker' );
		wp_enqueue_script( 'fullcalender_js', array( 'jquery', 'zpm_moment_js' ) );
		wp_enqueue_script( 'chosen_js' );
		wp_enqueue_script( 'zephyr-projects', ZPM_PLUGIN_URL . 'assets/js/zephyr-projects.js', array( 'jquery', 'fullcalender_js', 'zephyr-socket-io' ), $version );
		$adminScriptDeps = array( 'jquery', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-timepicker', 'zephyr-socket-io', 'chosen_js', 'zephyr-projects', 'zpm-mention-js' );
		$adminScriptDeps = apply_filters( 'zpm_admin_script_deps', $adminScriptDeps );
		wp_enqueue_script( 'zpm-core-admin', ZPM_PLUGIN_URL . 'assets/js/core-admin.js', $adminScriptDeps, $version );

		$localizedData = Utillities::getLocalizedData();
		wp_localize_script( 'zpm-core-admin', 'zpm_localized', $localizedData );
		wp_enqueue_script( 'zpm-basic-global', ZPM_PLUGIN_URL . 'assets/js/zpm-global.js', array( 'jquery', 'zpm-core-admin', 'dragula' ), $version );

		wp_register_script( 'zpm-progress-charts', ZPM_PLUGIN_URL . 'assets/js/progress-charts.js' );
		wp_enqueue_script( 'zpm-progress-charts' );
		wp_enqueue_script('heartbeat');
	}

	public static function enqueue_user_scripts() {
		$version = '4.1.0';

		if (apply_filters( 'zpm_should_load_shortcode_scripts', true )) {
			wp_enqueue_media();
			wp_register_style( 'chosen_css', ZPM_PLUGIN_URL . 'assets/css/chosen.css' );
			wp_enqueue_style( 'chosen_css' );
			wp_register_script( 'chosen_js', ZPM_PLUGIN_URL . 'assets/js/chosen.jquery.js', array( 'jquery' ) );
			wp_enqueue_script( 'chosen_js' );
			wp_register_style( 'linearicons', ZPM_PLUGIN_URL . 'assets/css/linearicons.css' );
			wp_enqueue_style( 'linearicons' );
			//wp_register_style( 'jquery-ui-styles', '//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
			wp_register_style( 'fullcalender_css', ZPM_PLUGIN_URL . 'assets/css/fullcalendar.css' );
			//wp_enqueue_style( 'jquery-ui-styles' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'fullcalender_css' );
			wp_enqueue_script('jquery-ui-resizable');
		    wp_register_script( 'zpm_moment_js', ZPM_PLUGIN_URL . 'assets/js/moment.min.js', array( 'jquery' ) );
		    wp_register_script( 'fullcalender_js', ZPM_PLUGIN_URL . 'assets/js/fullcalendar.js', array( 'jquery', 'moment' ) );
			wp_enqueue_script( 'zpm_moment_js' );
				    wp_enqueue_script( 'fullcalender_js', array( 'jquery', 'zpm_moment_js' ) );
			wp_enqueue_script( 'zephyr-projects', ZPM_PLUGIN_URL . '/assets/js/zephyr-projects.js', array( 'jquery', 'fullcalender_js' ), $version );

			wp_register_script( 'zephyr-shortcodes-scripts', ZPM_PLUGIN_URL . 'assets/js/shortcodes.js', array( 'jquery', 'zephyr-projects', 'chosen_js', 'jquery-ui-datepicker' ) );
			wp_enqueue_script( 'zephyr-shortcodes-scripts' );
			$localizedData = Utillities::getLocalizedData();
			wp_localize_script( 'zephyr-shortcodes-scripts', 'zpm_localized', $localizedData );

			wp_register_style( 'zephyr-shortcodes-styles', ZPM_PLUGIN_URL . 'assets/css/zephyr-shortcodes.css' );
			wp_enqueue_style( 'zephyr-shortcodes-styles' );

			$custom_css = EnqueueScripts::custom_frontend_styles();
			wp_add_inline_style( 'zephyr-shortcodes-styles', $custom_css);
		}
	}

	public static function custom_styles() {
		$general_settings = Utillities::general_settings();
		$primary = $general_settings['primary_color'];
		$primary_light = $general_settings['primary_color_light'];
		$primary_shifted = Utillities::adjust_brightness( $primary, -40 );
		$primary_dark = $general_settings['primary_color_dark'];
		$primary_dark_adjust = Utillities::adjust_brightness( $primary, -40 );
		$statuses = Utillities::get_statuses( 'all' );

		$html = "
			.zpm-color__primary {
				color: {$primary};
			}
			.zpm_button,
			.zpm_modal_body button {
				background: {$primary} !important;
			}
			.zpm_button:hover,
			.zpm_modal_body button:hover,
			.zpm_dropdown_list li:hover {
				background: {$primary_light} !important;
			}

			#zpm_add_new_btn {
				background: {$primary} !important;
			}

			#zpm_add_new_btn.active {
				background: {$primary_dark} !important;
			}

			.zpm_input:hover,
			.zpm_input:focus,
			.zpm_input:active,
			.zpm-modal .zpm_input:hover,
			.zpm-modal .zpm_input:focus,
			.zpm-modal .zpm_input:active,
			.chosen-container .chosen-single:hover,
			.chosen-container .chosen-single:focus,
			.chosen-container .chosen-single:active {
			    border-color: {$primary} !important;
			}
			.zpm_checkbox_label input:checked+.zpm_main_checkbox svg path {
			    fill: {$primary} !important;
			    stroke: {$primary} !important;
			}
			.zpm_project_name_input:focus, .zpm_project_name_input:active {
			    border-color: {$primary} !important;
			}
			.zpm_project_title:hover {
			    background: linear-gradient(45deg, {$primary_dark_adjust} 0%,{$primary} 100%);
			    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$primary_dark_adjust}', endColorstr='{$primary}',GradientType=1 );
			    color: #fff;
			}
			.zpm_project_progress_bar,
			.zpm_nav_item_selected:after,
			.zpm_nav_item:hover:after {
			    background: {$primary} !important;
			}
			.zpm_fancy_item:hover,
			.zpm_fancy_item:hover a,
			.zpm_nav_item_selected,
			.zpm_nav_item:hover {
				color: {$primary} !important;
			}
			button.zpm_button_outline {
			    background: none !important;
			    border: 1px solid {$primary} !important;
			    color: {$primary} !important;;
			}
			button.zpm_button_outline:hover {
				background: {$primary} !important;
				color: #fff;
			}
			.zpm-toggle:checked + .zpm-toggle-label:before {
			    background-color: {$primary_shifted};
			}

			.zpm-toggle:checked + .zpm-toggle-label:after {
			    background-color: {$primary};
			}
			.zpm_comment_attachment a:hover,
			.zpm_link {
			    color: {$primary};
			}
			.zpm_task_loader:after {
				border-color: {$primary} transparent {$primary} transparent;
			}
			.zpm_message_action_buttons #zpm_task_chat_files:hover, .zpm_message_action_buttons #zpm_task_chat_comment:hover {
			    background-color: {$primary} !important;
			}
			.zpm_message_action_buttons #zpm_task_chat_files, .zpm_message_action_buttons #zpm_task_chat_comment {
			    border: 1px solid {$primary} !important;
			    color: {$primary} !important;
			}
			.zpm_task_due_date,
			.zpm-task__type-label {
				color: {$primary} !important;
			}
			.zpm_modal_list li:hover {
			    background: {$primary_light} !important;
			}
			.zpm_activity_date {
				background: {$primary} !important;
			}
			.zpm_tab_title.zpm_tab_selected,
			.zpm_tab_title:hover {
			    color: {$primary} !important;
			}
			.nav-tabs li a:hover {
				color: {$primary};
			}
			#zpm_system_notification {
				background: {$primary} !important;
			}
			.zpm-form__field:focus ~ .zpm-form__label {
			  color: {$primary} !important;
			}
			.zpm-form__field:focus {
			  border-bottom: 1.5px solid {$primary} !important;
			}
			#zpm_task_editor_settings .zpm-form__field:active, #zpm_task_editor_settings .zpm-form__field:focus {
			  border-color: {$primary} !important;
			}
			#zpm_create_task .zpm_task_custom_field:focus, #zpm_create_task .zpm_task_custom_field:active, #zpm_create_task .zpm_task_custom_field:hover, #zpm_create_task .zpm_task_custom_field:focus, #zpm_create_task .zpm_task_custom_field:active {
				border-color: {$primary} !important;
			}
			.zpm_settings_wrap .chosen-container:hover,
			.zpm-modal .chosen-container:hover,
			.zpm-chosen-select + .chosen-container:hover {
				border: 1px solid {$primary} !important;
			}
			.zpm-material-checkbox > input:checked + span::before, .zpm-material-checkbox > input:indeterminate + span::before {
		      background: {$primary} !important;
		      border-color: {$primary} !important;
		    }

		    .zpm-material-checkbox > input:checked, .zpm-material-checkbox > input:indeterminate {
		      background-color: {$primary} !important;
		    }

		    #zpm-zephyr-info > i {
			    color: {$primary} !important;
			}

		    .row.day.today,
		    .row.date.sa.today  {
		      background-color: {$primary} !important;
		      color: #fff;
		    }

		    .nav-slider-right .nav-link,
		    .nav-slider-left .nav-link{
		      background-color: {$primary} !important;
		      border-color: {$primary} !important;
		    }
		    .zpm_floating_notification_button {
		    	border: 1px solid {$primary};
    			color: {$primary};
		    }
		    .zpm_floating_notification_button:hover {
		    	background: {$primary};
		    }
		    .zpm_nav_item:after {
		    	background: {$primary};
		    }
		    .zpm-task-team {
		    	background: {$primary};
		    }
		    #zpm_calendar .fc-button {
		   		background: {$primary} !important;
			}
			#zpm_calendar .fc-button:hover {
				background: {$primary_dark} !important;
			}
			.ui-datepicker .ui-widget-header {
				border-radius: 3px 3px 0 0;
				background: {$primary};
			}
			.ui-datepicker .ui-state-default:hover {
				background-color: {$primary} !important;
			}
			.ui-datepicker .ui-state-highlight, .ui-datepicker-current-day {
				background-color: {$primary} !important;
			}
			.ui-datepicker .ui-datepicker-header {
				background: {$primary} !important;
			}

			.zpm-progress__task-completed.completed {
				color: {$primary} !important;
			}
			.zpm-search-cat-name {
				background: {$primary} !important;
			}
			.search-choice {
				border-color: {$primary} !important;
				color: {$primary} !important;
			}
			.zpm-header-back:hover {
				color: {$primary} !important;
			}
			.zpm_button_inverted,
			a.zpm_button_inverted,
			a.zpm_button_inverted:visited {
		        border: 1px solid " . $primary . " !important;
		        color: " . $primary . " !important;
		        background: transparent !important;
		    }
	      	.zpm_button_inverted:hover,
	      	.zpm_button_inverted:focus,
	      	.zpm_button_inverted:active,
	      	a.zpm_button_inverted:hover,
	      	.zpm_button_inverted:focus,
	      	.zpm_button_inverted.zpm-pagination__current-page {
		        border-color: " . $primary_dark . " !important;
		        background: " . $primary_dark . " !important;
		        color: #fff !important;
		    }
		    .zpm_button_inverted[disabled='disabled']:hover {
		        color: " . $primary . " !important;
		    }
		    .zpm-color__hover-primary:hover,
		      .zpm-color__hover-primary.zpm-state__active {
		        color: " . $primary . " !important;
		      }
		";

		$gradientCss = Utillities::auto_gradient_css($primary);

		$html .= '.zpm-gradient { ' . $gradientCss . ' }';

		foreach ($statuses as $slug => $status) {
			$html .= "
				#zpm_calendar .fc-event[status='" . $slug . "'],
				#zpm_calendar .fc-event[status='" . $slug . "']:hover {
					background-color: " . $status['color'] . " !important;
					color: #fff;
				}
				.zpm_project_status.{$slug} {
					border-color: {$status['color']} !important;
				}
				.zpm_project_status.active.{$slug},
				.zpm_project_status.{$slug}:hover {
					background: {$status['color']} !important;
				}
				.zpm-project-preview__status-color.{$slug} {
					background: {$status['color']} !important;
				}
			";
		}

		$html .= isset($general_settings['custom_css']) ? $general_settings['custom_css'] : '';
		return $html;
	}

	public static function custom_frontend_styles() {
		$general_settings = Utillities::general_settings();
		$primary = $general_settings['primary_color'];
		$primary_light = $general_settings['primary_color_light'];
		$primary_shifted = Utillities::adjust_brightness( $primary, -40 );
		$primary_dark = $general_settings['primary_color_dark'];
		$primary_dark_adjust = Utillities::adjust_brightness( $primary, -40 );
		$html = "
			.zpm-shortcode-progress-bar {
				background: {$primary};
			}

			.zpm-task-card.completed {
				background: linear-gradient(45deg, {$primary_dark_adjust} 0%,{$primary} 100%);
			    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$primary_dark_adjust}', endColorstr='{$primary}',GradientType=1 );
			    color: #fff;
			}
			.zpm-task-shortcode-list-item.zpm-task-completed {
				background: linear-gradient(45deg, {$primary_dark_adjust} 0%,{$primary} 100%);
			    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$primary_dark_adjust}', endColorstr='{$primary}',GradientType=1 );
			    color: #fff;
			}
		";
		return $html;
	}

	public static function handle_scripts() {
		$pages = zpm_get_pages();

		if ( isset( $_GET['page'] ) && ( in_array( $_GET['page'], $pages ) ) )  {
			return false;
		}
		return true;
	}

}
