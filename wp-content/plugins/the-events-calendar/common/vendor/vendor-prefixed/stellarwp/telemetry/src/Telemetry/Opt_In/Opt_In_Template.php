<?php
/**
 * Handles all methods related to rendering the Opt-In template.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Telemetry\Opt_In;

use TEC\Common\StellarWP\Telemetry\Admin\Resources;
use TEC\Common\StellarWP\Telemetry\Config;
use TEC\Common\StellarWP\Telemetry\Contracts\Template_Interface;
use TEC\Common\StellarWP\Telemetry\Core;

/**
 * Handles all methods related to rendering the Opt-In template.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Opt_In_Template implements Template_Interface {
	protected const YES = '1';
	protected const NO  = '-1';

	/**
	 * The opt-in status object.
	 *
	 * @since 1.0.0
	 *
	 * @var Status
	 */
	protected $opt_in_status;

	/**
	 * The Telemetry constructor
	 *
	 * @param Status $opt_in_status The opt-in status object.
	 */
	public function __construct( Status $opt_in_status ) {
		$this->opt_in_status = $opt_in_status;
	}

	/**
	 * Gets the arguments for configuring how the Opt-In modal is rendered.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 - Updated to handle passed in stellar slug
	 *
	 * @param string $stellar_slug The slug to use when configuring the modal args.
	 *
	 * @return array
	 */
	public function get_args( string $stellar_slug ) {

		$optin_args = [
			'plugin_logo'           => Resources::get_asset_path() . 'resources/images/stellar-logo.svg',
			'plugin_logo_width'     => 151,
			'plugin_logo_height'    => 32,
			'plugin_logo_alt'       => 'StellarWP Logo',
			'plugin_name'           => 'StellarWP',
			'plugin_slug'           => $stellar_slug,
			'user_name'             => wp_get_current_user()->display_name,
			'permissions_url'       => '#',
			'tos_url'               => '#',
			'privacy_url'           => 'https://stellarwp.com/privacy-policy/',
			'opted_in_plugins_text' => __( 'See which plugins you have opted in to tracking for', 'stellarwp-telemetry' ),
		];

		$optin_args['opted_in_plugins'] = $this->get_opted_in_plugin_names();

		$optin_args['heading'] = sprintf(
			// Translators: The plugin name.
			__( 'We hope you love %s.', 'stellarwp-telemetry' ),
			$optin_args['plugin_name']
		);

		$optin_args['intro'] = $this->get_intro( $optin_args['user_name'], $optin_args['plugin_name'] );

		/**
		 * Filters the arguments for rendering the Opt-In modal.
		 *
		 * @since 2.0.0
		 *
		 * @param array $optin_args
		 * @param string $stellar_slug
		 */
		$optin_args = apply_filters( 'stellarwp/telemetry/optin_args', $optin_args, $stellar_slug );

		/**
		 * Filters the arguments for rendering the Opt-In modal.
		 *
		 * Planned Deprecation: 3.0.0
		 *
		 * @since 1.0.0
		 *
		 * @param array $optin_args
		 */
		$optin_args = apply_filters( 'stellarwp/telemetry/' . $stellar_slug . '/optin_args', $optin_args );

		return $optin_args;
	}

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 * @since 2.0.0 - Update to handle passed in stellar slug.
	 *
	 * @param string $stellar_slug The slug to render the modal with.
	 *
	 * @return void
	 */
	public function render( string $stellar_slug ) {
		load_template( dirname( dirname( __DIR__ ) ) . '/views/optin.php', false, $this->get_args( $stellar_slug ) );
	}

	/**
	 * Gets the option that determines if the modal should be rendered.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 - Update to handle passed in stellar_slug.
	 *
	 * @param string $stellar_slug The current stellar slug to be used in the option name.
	 *
	 * @return string
	 */
	public function get_option_name( string $stellar_slug ) {
		$option_name = sprintf(
			'stellarwp_telemetry_%s_show_optin',
			$stellar_slug
		);

		/**
		 * Filters the name of the option stored in the options table.
		 *
		 * @since 1.0.0
		 * @since 2.0.0 - Update to pass stellar slug for checking the current filter context.
		 *
		 * @param string $option_name
		 * @param string $stellar_slug The current stellar slug.
		 */
		return apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'show_optin_option_name',
			$option_name,
			$stellar_slug
		);
	}

	/**
	 * Helper function to determine if the modal should be rendered.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 - update to handle passed in stellar_slug.
	 *
	 * @param string $stellar_slug The stellar slug to get the option name for.
	 *
	 * @return boolean
	 */
	public function should_render( string $stellar_slug ) {
		return (bool) get_option( $this->get_option_name( $stellar_slug ), false );
	}

	/**
	 * Renders the modal if it should be rendered.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 - Add ability to render multiple modals.
	 *
	 * @param string $stellar_slug The stellar slug for which the modal should be rendered.
	 *
	 * @return void
	 */
	public function maybe_render( string $stellar_slug ) {
		if ( $this->should_render( $stellar_slug ) ) {
			$this->render( $stellar_slug );
		}
	}

	/**
	 * Gets an array of opted-in plugin names.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_opted_in_plugin_names() {
		$option           = Config::get_container()->get( Status::class )->get_option();
		$site_plugins_dir = Config::get_container()->get( Core::SITE_PLUGIN_DIR );
		$opted_in_plugins = [];

		foreach ( $option['plugins'] as $plugin ) {
			if ( true !== $plugin['optin'] ) {
				continue;
			}

			$plugin_path = trailingslashit( $site_plugins_dir ) . $plugin['wp_slug'];
			if ( ! file_exists( $plugin_path ) ) {
				continue;
			}

			$plugin_data = get_plugin_data( $plugin_path );
			if ( empty( $plugin_data['Name'] ) ) {
				continue;
			}

			$opted_in_plugins[] = $plugin_data['Name'];
		}

		return $opted_in_plugins;
	}

	/**
	 * Gets the primary message displayed on the opt-in modal.
	 *
	 * @param string $user_name   The display name of the user.
	 * @param string $plugin_name The name of the plugin.
	 *
	 * @return string
	 */
	public function get_intro( $user_name, $plugin_name ) {
		return sprintf(
			// Translators: The user name and the plugin name.
			esc_html__(
				'Hi, %1$s! This is an invitation to help our StellarWP community.
				If you opt-in, some data about your usage of %2$s and future StellarWP Products will be shared with our teams (so they can work their butts off to improve).
				We will also share some helpful info on WordPress, and our products from time to time.
				And if you skip this, that’s okay! Our products still work just fine.',
				'stellarwp-telemetry'
			),
			$user_name,
			$plugin_name
		);
	}
}
