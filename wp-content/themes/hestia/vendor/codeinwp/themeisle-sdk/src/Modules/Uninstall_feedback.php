<?php
/**
 * The deactivate feedback model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Feedback
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uninstall feedback module for ThemeIsle SDK.
 */
class Uninstall_Feedback extends Abstract_Module {
	/**
	 * How many seconds before the deactivation window is triggered for themes?
	 *
	 * @var int Number of days.
	 */
	const AUTO_TRIGGER_DEACTIVATE_WINDOW_SECONDS = 3;
	/**
	 * How many days before the deactivation window pops up again for the theme?
	 *
	 * @var int Number of days.
	 */
	const PAUSE_DEACTIVATE_WINDOW_DAYS = 100;
	/**
	 * Where to send the data.
	 *
	 * @var string Endpoint url.
	 */
	const FEEDBACK_ENDPOINT = 'http://feedback.themeisle.com/wordpress/wp-json/__pirate_feedback_/v1/feedback';

	/**
	 * Default options for plugins.
	 *
	 * @var array $options_plugin The main options list for plugins.
	 */
	private $options_plugin = array(
		'I found a better plugin'            => array(
			'id'          => 3,
			'type'        => 'text',
			'placeholder' => 'What\'s the plugin\'s name?',
		),
		'I could not get the plugin to work' => array(
			'id' => 4,
		),
		'I no longer need the plugin'        => array(
			'id'          => 5,
			'type'        => 'textarea',
			'placeholder' => 'If you could improve one thing about our product, what would it be?',
		),
		'It\'s a temporary deactivation. I\'m just debugging an issue.' => array(
			'id' => 6,
		),
	);
	/**
	 * Default options for theme.
	 *
	 * @var array $options_theme The main options list for themes.
	 */
	private $options_theme = array(
		'I don\'t know how to make it look like demo' => array(
			'id' => 7,
		),
		'It lacks options'                            => array(
			'id' => 8,
		),
		'Is not working with a plugin that I need'    => array(
			'id'          => 9,
			'type'        => 'text',
			'placeholder' => 'What is the name of the plugin',
		),
		'I want to try a new design, I don\'t like {theme} style' => array(
			'id' => 10,
		),
	);
	/**
	 * Default other option.
	 *
	 * @var array $other The other option
	 */
	private $other = array(
		'Other' => array(
			'id'          => 999,
			'type'        => 'textarea',
			'placeholder' => 'cmon cmon tell us',
		),
	);
	/**
	 * Default heading for plugin.
	 *
	 * @var string $heading_plugin The heading of the modal
	 */
	private $heading_plugin = 'Quick Feedback <span>Because we care about our clients, please leave us feedback.</span>';
	/**
	 * Default heading for theme.
	 *
	 * @var string $heading_theme The heading of the modal
	 */
	private $heading_theme = 'Looking to change {theme}? <span> What does not work for you?</span>';
	/**
	 * Default submit button action text.
	 *
	 * @var string $button_submit The text of the deactivate button
	 */
	private $button_submit = 'Submit &amp; Deactivate';
	/**
	 * Default cancel button.
	 *
	 * @var string $button_cancel The text of the cancel button
	 */
	private $button_cancel = 'Skip &amp; Deactivate';

	/**
	 * Loads the additional resources
	 */
	function load_resources() {
		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, array( 'theme-install', 'plugins' ) ) ) {
			return;
		}

		add_thickbox();

		$id = $this->product->get_key() . '_deactivate';

		$this->add_css( $this->product->get_type(), $this->product->get_key() );
		$this->add_js( $this->product->get_type(), $this->product->get_key(), '#TB_inline?' . apply_filters( $this->product->get_key() . '_feedback_deactivate_attributes', 'width=600&height=550' ) . '&inlineId=' . $id );

		echo '<div id="' . $id . '" style="display:none;" class="themeisle-deactivate-box">' . $this->get_html( $this->product->get_type(), $this->product->get_key() ) . '</div>';
	}

	/**
	 * Loads the css
	 *
	 * @param string $type The type of product.
	 * @param string $key The product key.
	 */
	function add_css( $type, $key ) {
		$key    = esc_attr( $key );
		$suffix = Product::THEME_TYPE === $type ? 'theme-install-php' : 'plugins-php';
		$icon   = esc_attr( apply_filters( $this->product->get_slug() . '_uninstall_feedback_icon', '' ) );
		if ( empty( $icon ) ) {
			$icon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDNkVDM0M4RkYxMzMxMUU3OEMyMkQ0NTIxRTVEQ0ZBRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDNkVDM0M5MEYxMzMxMUU3OEMyMkQ0NTIxRTVEQ0ZBRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkM2RUMzQzhERjEzMzExRTc4QzIyRDQ1MjFFNURDRkFGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM2RUMzQzhFRjEzMzExRTc4QzIyRDQ1MjFFNURDRkFGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+KBNOswAAFtFJREFUeNrkWwmUXVWV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';
		}
		?>
		<style type="text/css" id="<?php echo $key; ?>ti-deactivate-css">
			input[name="ti-deactivate-option"] ~ div {
				display: none;
			}

			input[name="ti-deactivate-option"]:checked ~ div {
				display: block;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container #TB_window.thickbox-loading:before {
				background: none !important;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container #TB_title {
				background: url('<?php echo $icon; ?>') 40px 30px no-repeat;
				border: none;
				box-sizing: border-box;
				color: #373e40;
				font-size: 24px;
				font-weight: 700;
				height: 90px;
				padding: 40px 40px 0 120px;
				text-transform: uppercase;
				width: 100%;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container #<?php echo $key; ?>-info-disclosure-content ul i {
				padding-left: 5px;
				margin: 0 1px;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container #<?php echo $key; ?>-info-disclosure-content ul strong {
				width: 125px;
				display: block;
				margin: 0;
				float: left;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container #<?php echo $key; ?>-info-disclosure-content ul {
				margin-left: 39px;
				margin-top: 2px;
				padding-top: 0px;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container #<?php echo $key; ?>-info-disclosure-content p {
				font-style: italic;
				margin-bottom: 0px;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container #<?php echo $key; ?>-info-disclosure-content {
				display: none;
			}
			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container.<?php echo $key; ?>-container-disc-open #<?php echo $key; ?>-info-disclosure-content {
				display: block;
				position:absolute;
				bottom: 100px;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container.<?php echo $key; ?>-container-disc-open #<?php echo $key; ?>-info-disclosure {
				top: -130px;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container.<?php echo $key; ?>-container-disc-open {
				height: 590px !important;
			}
			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container #<?php echo $key; ?>-info-disclosure {
				position: absolute;
				top: -50px;
				font-size: 13px;
				color: #8d9192;
				font-weight: 400;
				right: 40px;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container div.actions {
				box-sizing: border-box;
				padding: 30px 40px;
				background-color: #eaeaea;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container input.button {
				background: #ec5d60;
				border: none;
				box-shadow: none;
				color: #ffffff;
				font-size: 15px;
				font-weight: 700;
				height: auto;
				line-height: 20px;
				padding: 10px 15px;
				text-transform: uppercase;
				-webkit-transition: 0.3s ease;
				-moz-transition: 0.3s ease;
				-ms-transition: 0.3s ease;
				-o-transition: 0.3s ease;
				transition: 0.3s ease;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container input.button.button-primary {
				background: transparent;
				box-shadow: none;
				color: #8d9192;
				font-weight: 400;
				float: right;
				line-height: 40px;
				padding: 0;
				text-decoration: underline;
				text-shadow: none;
				text-transform: none;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container input.button:hover {
				background: #e83f42;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container input.button.button-primary:hover {
				background: transparent;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container input.button:focus {
				box-shadow: none;
				outline: none;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container input.button:active {
				box-shadow: none;
				transform: translateY(0);
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container input.button:disabled {
				cursor: not-allowed;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container input.button.button-primary:hover {
				text-decoration: none;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container div.revive_network-container {
				background-color: #ffffff;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container ul.ti-list {
				margin: 0;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container ul.ti-list li {
				color: #373e40;
				font-size: 13px;
				margin-bottom: 5px;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container ul.ti-list li label {
				margin-left: 10px;
				line-height: 28px;
				font-size: 15px;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container ul.ti-list input[type=radio] {
				margin-top: 1px;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container #TB_ajaxContent {
				box-sizing: border-box;
				height: auto !important;
				padding: 20px 40px;
				width: 100% !important;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container li div textarea {
				padding: 10px 15px;
				width: 100%;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container ul.ti-list li div {
				margin: 10px 30px;
			}

			.<?php echo $key; ?>-container #TB_title #TB_ajaxWindowTitle {
				box-sizing: border-box;
				display: block;
				float: none;
				font-weight: 700;
				line-height: 1;
				padding: 0;
				text-align: left;
				width: 100%;
			}

			.<?php echo $key; ?>-container #TB_title #TB_ajaxWindowTitle span {
				color: #8d9192;
				display: block;
				font-size: 15px;
				font-weight: 400;
				margin-top: 5px;
				text-transform: none;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container .actions {
				width: 100%;
				display: block;
				position: absolute;
				left: 0;
				bottom: 0;
			}

			.theme-install-php .<?php echo $key; ?>-container #TB_closeWindowButton .tb-close-icon:before {
				font-size: 32px;
			}

			.<?php echo $key; ?>-container #TB_closeWindowButton .tb-close-icon {
				color: #eee;
			}

			.<?php echo $key; ?>-container #TB_closeWindowButton {
				left: auto;
				right: -5px;
				top: -35px;
				color: #eee;
			}

			.<?php echo $key; ?>-container #TB_closeWindowButton .tb-close-icon {
				text-align: right;
				line-height: 25px;
				width: 25px;
				height: 25px;
			}

			.<?php echo $key; ?>-container #TB_closeWindowButton:focus .tb-close-icon {
				box-shadow: none;
				outline: none;
			}

			.<?php echo $key; ?>-container #TB_closeWindowButton .tb-close-icon:before {
				font: normal 25px dashicons;
			}

			body.<?php echo $suffix; ?> .<?php echo $key; ?>-container {
				margin: auto !important;
				height: 500px !important;
				top: 0 !important;
				left: 0 !important;
				bottom: 0 !important;
				right: 0 !important;
				width: 600px !important;
			}
		</style>
		<?php
		do_action( $this->product->get_key() . '_uninstall_feedback_after_css' );
	}

	/**
	 * Loads the js.
	 *
	 * @param string $type The type of product.
	 * @param string $key The product key.
	 * @param string $src The url that will hijack the deactivate button url.
	 */
	function add_js( $type, $key, $src ) {
		$heading = Product::PLUGIN_TYPE === $type ? $this->heading_plugin : str_replace( '{theme}', $this->product->get_name(), $this->heading_theme );
		$key     = esc_attr( $key );
		$heading = apply_filters( $this->product->get_key() . '_feedback_deactivate_heading', $heading );
		?>
		<script type="text/javascript" id="ti-deactivate-js">
			(function ($) {
				$(document).ready(function () {
					var auto_trigger = false;
					var target_element = 'tr[data-plugin^="<?php echo $this->product->get_slug(); ?>/"] span.deactivate a';
					<?php
					if ( 'theme' === $type ) {
						?>
					auto_trigger = true;
					if ($('a.ti-auto-anchor').length == 0) {
						$('body').append($('<a class="ti-auto-anchor" href=""></a>'));
					}
					target_element = 'a.ti-auto-anchor';
						<?php
					}
					?>

					if (auto_trigger) {
						setTimeout(function () {
							$('a.ti-auto-anchor').trigger('click');
						}, <?php echo self::AUTO_TRIGGER_DEACTIVATE_WINDOW_SECONDS * 1000; ?> );
					}
					$(document).on('thickbox:removed', function () {
						$.post(ajaxurl, {
							'action': '<?php echo $key . '_uninstall_feedback'; ?>',
							'nonce': '<?php echo wp_create_nonce( (string) __CLASS__ ); ?>',
							'type': '<?php echo $type; ?>',
							'key': '<?php echo $key; ?>'
						});
					});
					var href = $(target_element).attr('href');
					$('#<?php echo $key; ?>ti-deactivate-no').attr('data-ti-action', href).on('click', function (e) {
						e.preventDefault();
						e.stopPropagation();

						$('body').unbind('thickbox:removed');
						tb_remove();
						var redirect = $(this).attr('data-ti-action');
						if (redirect !== '') {
							location.href = redirect;
						}
					});

					$('#<?php echo $key; ?> ul.ti-list label, #<?php echo $key; ?> ul.ti-list input[name="ti-deactivate-option"]').on('click', function (e) {
						$('#<?php echo $key; ?>ti-deactivate-yes').val($('#<?php echo $key; ?>ti-deactivate-yes').attr('data-after-text'));

						var radio = $(this).prop('tagName') === 'LABEL' ? $(this).parent() : $(this);
						if (radio.parent().find('textarea').length > 0 && radio.parent().find('textarea').val().length === 0) {
							$('#<?php echo $key; ?>ti-deactivate-yes').attr('disabled', 'disabled');
							radio.parent().find('textarea').on('keyup', function (ee) {
								if ($(this).val().length === 0) {
									$('#<?php echo $key; ?>ti-deactivate-yes').attr('disabled', 'disabled');
								} else {
									$('#<?php echo $key; ?>ti-deactivate-yes').removeAttr('disabled');
								}
							});
						} else {
							$('#<?php echo $key; ?>ti-deactivate-yes').removeAttr('disabled');
						}
					});
					$("#<?php echo $key; ?>-info-disclosure").on('click', function () {
						$("#TB_window").toggleClass("<?php echo $key; ?>-container-disc-open");
						return false;
					});
					$('#<?php echo $key; ?>ti-deactivate-yes').attr('data-ti-action', href).on('click', function (e) {
						e.preventDefault();
						e.stopPropagation();
						$.post(ajaxurl, {
							'action': '<?php echo $key . '_uninstall_feedback'; ?>',
							'nonce': '<?php echo wp_create_nonce( (string) __CLASS__ ); ?>',
							'id': $('#<?php echo $key; ?> input[name="ti-deactivate-option"]:checked').parent().attr('ti-option-id'),
							'msg': $('#<?php echo $key; ?> input[name="ti-deactivate-option"]:checked').parent().find('textarea').val(),
							'type': '<?php echo $type; ?>',
						});
						var redirect = $(this).attr('data-ti-action');
						if (redirect != '') {
							location.href = redirect;
						} else {
							$('body').unbind('thickbox:removed');
							tb_remove();
						}
					});

					$(target_element).attr('name', '<?php echo wp_kses( $heading, array( 'span' => array() ) ); ?>').attr('href', '<?php echo $src; ?>').addClass('thickbox');
					var thicbox_timer;
					$(target_element).on('click', function () {
						tiBindThickbox();
					});

					function tiBindThickbox() {
						var thicbox_timer = setTimeout(function () {
							if ($("#<?php echo esc_html( $key ); ?>").is(":visible")) {
								$("body").trigger('thickbox:iframe:loaded');
								$("#TB_window").addClass("<?php echo $key; ?>-container");
								clearTimeout(thicbox_timer);
								$('body').unbind('thickbox:removed');
							} else {
								tiBindThickbox();
							}
						}, 100);
					}
				});
			})(jQuery);
		</script>
		<?php

		do_action( $this->product->get_key() . '_uninstall_feedback_after_js' );
	}

	/**
	 * Generates the HTML.
	 *
	 * @param string $type The type of product.
	 * @param string $key The product key.
	 */
	function get_html( $type, $key ) {
		$options       = Product::PLUGIN_TYPE === $type ? $this->options_plugin : $this->options_theme;
		$button_cancel = Product::PLUGIN_TYPE === $type ? $this->button_cancel : 'Skip';
		$button_submit = Product::PLUGIN_TYPE === $type ? $this->button_submit : 'Submit';
		$options       = $this->randomize_options( apply_filters( $this->product->get_key() . '_feedback_deactivate_options', $options ) );
		$button_submit = apply_filters( $this->product->get_key() . '_feedback_deactivate_button_submit', $button_submit );
		$button_cancel = apply_filters( $this->product->get_key() . '_feedback_deactivate_button_cancel', $button_cancel );

		$options += $this->other;

		$list = '';
		foreach ( $options as $title => $attributes ) {
			$id    = $attributes['id'];
			$list .= '<li ti-option-id="' . $id . '"><input type="radio" name="ti-deactivate-option" id="' . $key . $id . '"><label for="' . $key . $id . '">' . str_replace( '{theme}', $this->product->get_name(), $title ) . '</label>';
			if ( array_key_exists( 'type', $attributes ) ) {
				$list       .= '<div>';
				$placeholder = array_key_exists( 'placeholder', $attributes ) ? $attributes['placeholder'] : '';
				switch ( $attributes['type'] ) {
					case 'text':
						$list .= '<textarea style="width: 100%" rows="1" name="comments" placeholder="' . $placeholder . '"></textarea>';
						break;
					case 'textarea':
						$list .= '<textarea style="width: 100%" rows="2" name="comments" placeholder="' . $placeholder . '"></textarea>';
						break;
				}
				$list .= '</div>';
			}
			$list .= '</li>';
		}

		$disclosure_new_labels = apply_filters( $this->product->get_slug() . '_themeisle_sdk_disclosure_content_labels', [], $this->product );
		$disclosure_labels     = array_merge(
			[
				'title' => 'Below is a detailed view of all data that ThemeIsle will receive if you fill in this survey. No domain name, email address or IP addresses are transmited after you submit the survey.',
				'items' => [
					sprintf( '%s %s version %s %s %s %s', '<strong>', ucwords( $this->product->get_type() ), '</strong>', '<code>', $this->product->get_version(), '</code>' ),
					sprintf( '%s Uninstall reason %s %s Selected reson from the above survey %s ', '<strong>', '</strong>', '<i>', '</i>' ),
				],
			],
			$disclosure_new_labels
		);

		$info_disclosure_link    = '<a href="#" id="' . $this->product->get_key() . '-info-disclosure">' . apply_filters( $this->product->get_slug() . '_themeisle_sdk_info_collect_cta', 'What info do we collect?' ) . '</a>';
		$info_disclosure_content = '<div id="' . $this->product->get_key() . '-info-disclosure-content"><p>' . wp_kses_post( $disclosure_labels['title'] ) . '</p><ul>';
		foreach ( $disclosure_labels['items'] as $disclosure_item ) {
			$info_disclosure_content .= sprintf( '<li>%s</li>', wp_kses_post( $disclosure_item ) );
		}
		$info_disclosure_content .= '</ul></div>';

		return
			'<div id="' . $this->product->get_key() . '"><ul class="ti-list">' . $list . '</ul>'
			. $info_disclosure_content
			. '<div class="actions">'
			. get_submit_button(
				$button_submit,
				'secondary',
				$this->product->get_key() . 'ti-deactivate-yes',
				false,
				array(
					'data-after-text' => $button_submit,
					'disabled'        => true,
				)
			)
			. wp_kses_post( $info_disclosure_link )
			. get_submit_button( $button_cancel, 'primary', $this->product->get_key() . 'ti-deactivate-no', false )
			. '</div></div>';
	}

	/**
	 * Randomizes the options array.
	 *
	 * @param array $options The options array.
	 */
	function randomize_options( $options ) {
		$new  = array();
		$keys = array_keys( $options );
		shuffle( $keys );

		foreach ( $keys as $key ) {
			$new[ $key ] = $options[ $key ];
		}

		return $new;
	}

	/**
	 * Called when the deactivate button is clicked.
	 */
	function post_deactivate() {
		check_ajax_referer( (string) __CLASS__, 'nonce' );

		$this->post_deactivate_or_cancel();

		if ( empty( $_POST['id'] ) ) {

			wp_send_json( [] );

			return;
		}
		$this->call_api(
			array(
				'type'    => 'deactivate',
				'id'      => $_POST['id'],
				'comment' => isset( $_POST['msg'] ) ? $_POST['msg'] : '',
			)
		);
		wp_send_json( [] );

	}

	/**
	 * Called when the deactivate/cancel button is clicked.
	 */
	private function post_deactivate_or_cancel() {
		if ( ! isset( $_POST['type'] ) || ! isset( $_POST['key'] ) ) {
			return;
		}
		if ( 'theme' !== $_POST['type'] ) {
			return;
		}

		set_transient( 'ti_sdk_pause_' . $_POST['key'], true, self::PAUSE_DEACTIVATE_WINDOW_DAYS * DAY_IN_SECONDS );

	}

	/**
	 * Calls the API
	 *
	 * @param array $attributes The attributes of the post body.
	 *
	 * @return bool Is the request succesfull?
	 */
	protected function call_api( $attributes ) {
		$slug                  = $this->product->get_slug();
		$version               = $this->product->get_version();
		$attributes['slug']    = $slug;
		$attributes['version'] = $version;

		$response = wp_remote_post(
			self::FEEDBACK_ENDPOINT,
			array(
				'body' => $attributes,
			)
		);

		return is_wp_error( $response );
	}

	/**
	 * Should we load this object?.
	 *
	 * @param Product $product Product object.
	 *
	 * @return bool Should we load the module?
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}
		if ( $product->is_theme() && ( false !== get_transient( 'ti_sdk_pause_' . $product->get_key(), false ) ) ) {
			return false;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}
		global $pagenow;

		if ( ! isset( $pagenow ) || empty( $pagenow ) ) {
			return false;
		}

		if ( $product->is_plugin() && 'plugins.php' !== $pagenow ) {
			return false;

		}
		if ( $product->is_theme() && 'theme-install.php' !== $pagenow ) {
			return false;
		}

		return true;
	}

	/**
	 * Loads module hooks.
	 *
	 * @param Product $product Product details.
	 *
	 * @return Uninstall_Feedback Current module instance.
	 */
	public function load( $product ) {
		$this->product = $product;
		add_action( 'admin_head', array( $this, 'load_resources' ) );
		add_action( 'wp_ajax_' . $this->product->get_key() . '_uninstall_feedback', array( $this, 'post_deactivate' ) );

		return $this;
	}
}
