<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Admin;

use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Resources\Collection;
use TEC\Common\StellarWP\Uplink\Resources\Plugin;
use Throwable;

class Plugins_Page {

	/**
	 * Storing the `plugin_notice` message for each plugin.
	 *
	 * @var array<string, array{slug: string, message_row_html: string}>
	 */
	private $plugin_notices = [];

	/**
	 * The memoization cache for existing plugins.
	 *
	 * @var ?Collection<Plugin>
	 */
	private $plugins;

	/**
	 * Displays messages on the plugins page in the dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @param string $page
	 *
	 * @return void
	 */
	public function display_plugin_messages( string $page ): void {
		if ( 'plugins.php' !== $page ) {
			return;
		}

		$messages       = [];
		$plugins        = $this->get_plugins();
		$plugin_updates = get_plugin_updates();

		foreach ( $plugins as $plugin ) {
			$plugin_file = $plugin->get_path();
			$resource    = $plugin_updates[ $plugin_file ] ?? null;

			if ( empty( $resource ) ) {
				continue;
			}

			if ( ! empty( $resource->update->license_error ) ) {
				$messages[] = $resource->update->license_error;
			}

			if ( empty( $messages ) ) {
				continue;
			}

			$message_row_html = '';

			foreach ( $messages as $message ) {
				$message_row_html .= sprintf(
					'<div class="update-message notice inline notice-warning notice-alt">%s</div>',
					$message
				);
			}

			$message_row_html = sprintf(
				'<tr class="plugin-update-tr active"><td colspan="4" class="plugin-update">%s</td></tr>',
				$message_row_html
			);

			$this->plugin_notices[ $plugin->get_slug() ] = [
				'slug'             => $plugin->get_slug(),
				'message_row_html' => $message_row_html,
			];
		}
	}

	/**
	 * Get plugin notices.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array{slug: string, message_row_html: string}>
	 */
	public function get_plugin_notices(): array {
		return apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/plugin_notices', $this->plugin_notices );
	}

	/**
	 * Add notices as JS variable
	 *
	 * @param string $page
	 *
	 * @return void
	 */
	public function store_admin_notices( string $page ): void {
		if ( 'plugins.php' !== $page ) {
			return;
		}

		add_action( 'admin_footer', [ $this, 'output_notices_script' ] );
	}

	/**
	 * Output the plugin-specific notices script.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function output_notices_script(): void {
		$notices = $this->get_plugin_notices();

		if ( empty( $notices ) ) {
			return;
		}

		foreach ( $this->get_plugins() as $resource ) :
			$notice = $notices[ $resource->get_slug() ] ?? null;

			if ( ! $notice ) {
				continue;
			}
		?>
		<script>
		/**
		 * Appends license key notifications inline within the plugin table.
		 *
		 * This is done via JS because the options for achieving the same things
		 * server-side are currently limited.
		 */
		(function( $, my ) {
			'use strict';

			my.init = function() {
				var $active_plugin_row = $( 'tr.active[data-slug="<?php echo esc_attr( $resource->get_slug() ); ?>"]' );

				if ( ! $active_plugin_row.length ) {
					return;
				}

				var notice = <?php echo wp_json_encode( $notice ); ?>;

				if ( ! notice.message_row_html ) {
					return;
				}

				// Add the .update class to the plugin row and append our new row with the update message
				$active_plugin_row.addClass( 'update' ).after( notice.message_row_html );
			};

			$( function() {
				my.init();
			} );
		})( jQuery, {} );
		</script>
		<?php
		endforeach;
	}

	/**
	 * @param mixed $transient
	 *
	 * @return mixed
	 */
	public function check_for_updates( $transient ) {
		try {
			/** @var Plugin $resource */
			foreach ( $this->get_plugins() as $resource ) {
				$transient = $resource->check_for_updates( $transient );
			}
		} catch ( Throwable $exception ) {
			return $transient;
		}

		return $transient;
	}

	/**
	 * Get the collection of Plugins/Services.
	 *
	 * @return Collection<Plugin>
	 */
	protected function get_plugins(): Collection {
		if ( isset( $this->plugins ) ) {
			return $this->plugins;
		}

		return $this->plugins = Config::get_container()->get( Collection::class )->get_plugins();
	}

	/**
	 * Intercept plugins_api() calls that request information about our plugin and
	 * use the configured API endpoint to satisfy them.
	 *
	 * @see plugins_api()
	 *
	 * @param mixed  $result
	 * @param string|null  $action
	 * @param array<mixed>|object|null  $args
	 *
	 * @return mixed
	 */
	public function inject_info( $result, ?string $action = null, $args = null ) {
		$relevant = ( 'plugin_information' === $action ) && is_object( $args ) && ! empty( $args->slug );

		if ( ! $relevant ) {
			return $result;
		}
		if ( apply_filters( 'stellarwp/uplink/' . $args->slug . '/prevent_update_check', false ) ) {
			return $result;
		}

		$plugin = $this->get_plugins()->offsetGet( $args->slug );

		if ( ! $plugin ) {
			return $result;
		}

		return $plugin->validate_license()->to_wp_format();
	}

}
