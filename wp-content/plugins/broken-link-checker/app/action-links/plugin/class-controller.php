<?php
/**
 * Plugin action links.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Action_Links\Plugin
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Action_Links\Plugin;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Action_Links\Plugin
 */
class Controller extends Base {

	public function init() {
		Settings::instance()->init();

		$plugin_file = plugin_basename( WPMUDEV_BLC_PLUGIN_FILE );

		add_filter( "plugin_action_links_{$plugin_file}", array( $this, 'action_links' ), 10, 4 );
		add_filter( "network_admin_plugin_action_links_{$plugin_file}", array( $this, 'action_links' ), 10, 4 );
	}

	/**
	 * Sets the plugin action links in plugins page.
	 *
	 * @param array $actions
	 * @param string $plugin_file
	 * @param array $plugin_data
	 * @param string $context
	 *
	 * @return array
	 */
	public function action_links( $actions = array(), $plugin_file = '', $plugin_data = null, $context = '' ) {
		if ( ! is_array( $actions ) ) {
			$actions = array();
		}

		$new_actions = $this->get_action_links();

		return apply_filters(
			'wpmudev_blc_plugin_action_links',
			wp_parse_args( $actions, $new_actions ),
			$new_actions,
			$actions,
			$plugin_file,
			$plugin_data,
			$context
		);
	}

	/**
	 * Returns the plugin's action links.
	 *
	 * @return array
	 */
	public function get_action_links() {
		$actions         = array();
		$dashboard_url   = menu_page_url( 'blc_dash', false );
		$dashboard_label = esc_html__( 'Cloud', 'broken-link-checker' );
		$local_url       = menu_page_url( 'blc_local', false );
		$local_label     = esc_html__( 'Local', 'broken-link-checker' );
		$docs_url        = 'https://wpmudev.com/docs/wpmu-dev-plugins/broken-link-checker';
		$docs_label      = esc_html__( 'Docs', 'broken-link-checker' );

		if ( ! Utilities::is_subsite() ) {
			$admin_url     = get_admin_url( get_main_site_id(), 'admin.php' );
			$dashboard_url = add_query_arg(
				array(
					'page' => 'blc_dash',
				),
				$admin_url
			);
			$local_url     = add_query_arg(
				array(
					'page' => 'blc_local',
				),
				$admin_url
			);

			$actions['cloud'] = "<a href=\"{$dashboard_url}\">{$dashboard_label}</a>";
			$actions['local'] = "<a href=\"{$local_url}\">{$local_label}</a>";
		} else {
			$local_label = esc_html__( 'Broken Links', 'broken-link-checker' );
			$local_url   = add_query_arg(
				array(
					'page' => 'blc_local',
				),
				get_admin_url()
			);

			$actions['local'] = "<a href=\"{$local_url}\">{$local_label}</a>";
		}

		$actions['docs'] = "<a href=\"{$docs_url}\" target=\"_blank\">{$docs_label}</a>";

		return $actions;
	}
}
