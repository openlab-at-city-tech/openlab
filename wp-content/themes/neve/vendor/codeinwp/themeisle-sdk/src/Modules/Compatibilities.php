<?php
/**
 * The compatibilities model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
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
 * Promotions module for ThemeIsle SDK.
 */
class Compatibilities extends Abstract_Module {
	const REQUIRED  = 'required';
	const TESTED_UP = 'tested_up';

	/**
	 * Should we load this module.
	 *
	 * @param Product $product Product object.
	 *
	 * @return bool
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}
		if ( $product->is_theme() && ! current_user_can( 'switch_themes' ) ) {
			return false;
		}

		if ( $product->is_plugin() && ! current_user_can( 'install_plugins' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Registers the hooks.
	 *
	 * @param Product $product Product to load.
	 *
	 * @throws \Exception If the configuration is invalid.
	 *
	 * @return Compatibilities Module instance.
	 */
	public function load( $product ) {


		$this->product = $product;

		$compatibilities = apply_filters( 'themeisle_sdk_compatibilities/' . $this->product->get_slug(), [] );
		if ( empty( $compatibilities ) ) {
			return $this;
		}
		$requirement = null;
		$check_type  = null;
		foreach ( $compatibilities as $compatibility ) {

			if ( empty( $compatibility['basefile'] ) ) {
				return $this;
			}
			$requirement = new Product( $compatibility['basefile'] );
			$tested_up   = isset( $compatibility[ self::TESTED_UP ] ) ? $compatibility[ self::TESTED_UP ] : '999';
			$required    = $compatibility[ self::REQUIRED ];
			if ( ! version_compare( $required, $tested_up, '<' ) ) {
				throw new \Exception( sprintf( 'Invalid required/tested_up configuration. Required version %s should be lower than tested_up %s.', $required, $tested_up ) );
			}
			$check_type = self::REQUIRED;
			if ( ! version_compare( $requirement->get_version(), $required, '<' ) ) {
				$check_type = self::TESTED_UP;
				if ( version_compare( $requirement->get_version(), $tested_up . '.9999', '<' ) ) {
					return $this;
				}
			}

			break;
		}
		if ( empty( $requirement ) ) {
			return $this;
		}
		if ( $check_type === self::REQUIRED ) {
			$this->mark_required( $product, $requirement );
		}
		if ( $check_type === self::TESTED_UP ) {
			$this->mark_testedup( $product, $requirement );
		}

		return $this;
	}

	/**
	 * Mark the product tested up.
	 *
	 * @param Product $product Product object.
	 * @param Product $requirement Requirement object.
	 *
	 * @return void
	 */
	public function mark_testedup( $product, $requirement ) {
		add_action(
			'admin_head',
			function () use ( $product, $requirement ) {
				$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : '';

				if ( empty( $screen ) || ! isset( $screen->id ) ) {
					return;
				}
				if ( $requirement->is_theme() && $screen->id === 'themes' ) {
					?>
				<script type="text/javascript">
					jQuery(document).ready(function ($) {
						setInterval(checkTheme, 500);
						function checkTheme() {
							var theme = jQuery( '.theme.active[data-slug="<?php echo esc_attr( $requirement->get_slug() ); ?>"]' );
							var notice_id = 'testedup<?php echo esc_attr( $requirement->get_slug() . $product->get_slug() ); ?>';
							var product_name = '<?php echo esc_attr( $product->get_friendly_name() ); ?>';
							if (theme.length > 0 && jQuery('#' + notice_id).length === 0) {
								theme.find('.theme-id-container').prepend('<div style="bottom:100%;top:auto;" id="'+notice_id+'" class="notice notice-warning"><strong>Warning:</strong> This theme has not been tested with your current version of <strong>' + product_name +'</strong>. Please update '+product_name+' plugin.</div>');
							}
							if (theme.length > 0 && jQuery('#' + notice_id + 'overlay').length === 0) {
								jQuery('.theme-overlay.active .theme-author').after('<div style="bottom:100%;top:auto;" id="'+notice_id+'overlay" class="notice notice-warning"><p><strong>Warning:</strong> This theme has not been tested with your current version of <strong>' + product_name +'</strong>. Please update '+product_name+' plugin.</p></div>');
							}
						}
					})

				</script>
					<?php
				}
				if ( $requirement->is_plugin() && $screen->id === 'plugins' ) {
					?>
				<script type="text/javascript">
					jQuery(document).ready(function ($) {
						setInterval(checkPlugin, 500);
						function checkPlugin() {
							var plugin = jQuery( '.plugins .active[data-slug="<?php echo esc_attr( $requirement->get_slug() ); ?>"]' );
							var notice_id = 'testedup<?php echo esc_attr( $requirement->get_slug() . $product->get_slug() ); ?>';
							var product_name = '<?php echo esc_attr( $product->get_friendly_name() ); ?>';
							var product_type = '<?php echo ( $product->is_plugin() ? 'plugin' : 'theme' ); ?>';
							if (plugin.length > 0 && jQuery('#' + notice_id).length === 0) {
								plugin.find('.column-description').append('<div style="bottom:100%;top:auto;" id="'+notice_id+'" class="notice notice-warning notice-alt notice-inline"><strong>Warning:</strong> This plugin has not been tested with your current version of <strong>' + product_name +'</strong>. Please update '+product_name+' '+product_type+'.</div>');
							}
						}
					})

				</script>
					<?php
				}
			} 
		);

	}

	/**
	 * Mark the product requirements.
	 *
	 * @param Product $product Product object.
	 * @param Product $requirement Requirement object.
	 *
	 * @return void
	 */
	public function mark_required( $product, $requirement ) {
		add_filter(
			'upgrader_pre_download',
			function ( $return, $package, $upgrader ) use ( $product, $requirement ) {
				/**
				 * Upgrader object.
				 *
				 * @var \WP_Upgrader $upgrader Upgrader object.
				 */
				$should_block = false;
				if ( $product->is_theme()
					 && property_exists( $upgrader, 'skin' )
					 && property_exists( $upgrader->skin, 'theme_info' )
					 && $upgrader->skin->theme_info->template === $product->get_slug() ) {
					$should_block = true;

				}
				if ( ! $should_block && $product->is_plugin()
					 && property_exists( $upgrader, 'skin' )
					 && property_exists( $upgrader->skin, 'plugin_info' )
					 && $upgrader->skin->plugin_info['Name'] === $product->get_name() ) {
					$should_block = true;
				}
				if ( $should_block ) {
					echo( sprintf(
						'%s update requires a newer version of %s. Please %supdate%s %s %s.',
						esc_attr( $product->get_friendly_name() ),
						esc_attr( $requirement->get_friendly_name() ),
						'<a href="' . esc_url( admin_url( $requirement->is_theme() ? 'themes.php' : 'plugins.php' ) ) . '">',
						'</a>',
						esc_attr( $requirement->get_friendly_name() ),
						esc_attr( $requirement->is_theme() ? 'theme' : 'plugin' )
					) );
					$upgrader->maintenance_mode( false );
					die();
				}

				return $return;
			},
			10,
			3
		);

		add_action(
			'admin_notices',
			function () use ( $product, $requirement ) {
				echo '<div class="notice notice-error "><p>';
				echo( sprintf(
					'%s requires a newer version of %s. Please %supdate%s %s %s to the latest version.',
					'<strong>' . esc_attr( $product->get_friendly_name() ) . '</strong>',
					'<strong>' . esc_attr( $requirement->get_friendly_name() ) . '</strong>',
					'<a href="' . esc_url( admin_url( $requirement->is_theme() ? 'themes.php' : 'plugins.php' ) ) . '">',
					'</a>',
					'<strong>' . esc_attr( $requirement->get_friendly_name() ) . '</strong>',
					esc_attr( $requirement->is_theme() ? 'theme' : 'plugin' )
				) );
				echo '</p></div>';
			}
		);

	}
}
