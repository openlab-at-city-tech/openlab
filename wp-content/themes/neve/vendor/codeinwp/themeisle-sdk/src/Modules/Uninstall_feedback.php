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
			$icon = 'data:image/png;base64,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';
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
				background: url('<?php echo $icon; ?>') 23px 0px no-repeat;
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
