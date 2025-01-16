<?php
/**
 * The data class.
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

/**
 * Class Data
 */
class Data {

	use Singleton;

	/**
	 * Returns the URL with wpmudev.com base URL prefixed.
	 *
	 * If a custom server is configured, it will be used as base url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Path to suffix.
	 *
	 * @return string The full URL.
	 */
	public function server_url( $path = '' ) {
		$base_url = 'https://wpmudev.com/';
		// If custom api server is set.
		if ( defined( '\WPMUDEV_CUSTOM_API_SERVER' ) && ! empty( \WPMUDEV_CUSTOM_API_SERVER ) ) {
			$base_url = trailingslashit( \WPMUDEV_CUSTOM_API_SERVER );
		}

		return $base_url . $path;
	}

	/**
	 * Returns current full URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string The full URL.
	 */
	public function current_url() {
		$parts = parse_url( home_url() );

		// Attempt to get from parsed data.
		if ( ! empty( $parts['scheme'] ) && ! empty( $parts['host'] ) ) {
			return "{$parts['scheme']}://{$parts['host']}" . add_query_arg( null, null );
		}

		return add_query_arg( null, null );
	}

	/**
	 * Returns the canonical site_url that should be used for the site in the hub.
	 *
	 * Define WPMUDEV_HUB_SITE_URL to override or make static the url it should show as
	 * in the hub. Defaults to network_site_url() which may be dynamically filtered
	 * by some plugins and hosting providers.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function network_site_url() {
		return defined( '\WPMUDEV_HUB_SITE_URL' ) ? \WPMUDEV_HUB_SITE_URL : network_site_url();
	}

	/**
	 * Returns the canonical home_url that should be used for the site in the hub.
	 *
	 * Define WPMUDEV_HUB_HOME_URL to override or make static the url it should show as
	 * in the hub. Defaults to WPMUDEV_HUB_SITE_URL if set, or network_home_url() which
	 * may be dynamically filtered by some plugins and hosting providers.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function network_home_url() {
		if ( defined( '\WPMUDEV_HUB_HOME_URL' ) ) {
			return \WPMUDEV_HUB_HOME_URL;
		} elseif ( defined( '\WPMUDEV_HUB_SITE_URL' ) ) {
			return \WPMUDEV_HUB_SITE_URL;
		} else {
			return network_home_url();
		}
	}

	/**
	 * Returns the canonical home_url that should be used for the site in the hub.
	 *
	 * Define WPMUDEV_HUB_ADMIN_URL to override or make static the url it should show as
	 * in the hub. Defaults to deriving from WPMUDEV_HUB_SITE_URL if set, or network_admin_url()
	 * which may be dynamically filtered by some plugins and hosting providers.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function network_admin_url() {
		if ( defined( '\WPMUDEV_HUB_ADMIN_URL' ) ) {
			return \WPMUDEV_HUB_ADMIN_URL;
		} elseif ( defined( '\WPMUDEV_HUB_SITE_URL' ) ) {
			return is_multisite() ? trailingslashit( \WPMUDEV_HUB_SITE_URL ) . 'wp-admin/network/' : trailingslashit( \WPMUDEV_HUB_SITE_URL ) . 'wp-admin/';
		} else {
			return network_admin_url();
		}
	}

	/**
	 * The proper way to get details about the current membership.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function membership_data() {
		$data = Options::get( 'membership_data', array() );
		// Basic sanitation, to avoid incompatible return values.
		if ( ! is_array( $data ) ) {
			$data = array();
		}

		// Make sure it's in correct structure.
		$data = wp_parse_args( $data, array( 'membership' => '' ) );

		/**
		 * Filter to modify raw membership data.
		 *
		 * @since 1.0.0
		 *
		 * @param array $data Membership data.
		 */
		return apply_filters( 'wpmudev_hub_connector_get_membership_data', $data );
	}

	/**
	 * Get the current Hub site ID.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function hub_site_id() {
		$membership = $this->membership_data();

		return isset( $membership['hub_site_id'] ) ? $membership['hub_site_id'] : 0;
	}

	/**
	 * Get current membership type.
	 *
	 * Possible return values:
	 * 'free'    - Free hub membership.
	 * 'single'  - Single membership (i.e. only 1 project is licensed)
	 * 'unit'    - One or more projects licensed
	 * 'full'    - Full membership, no restrictions.
	 * 'paused'  - Membership access is paused.
	 * 'expired' - Expired membership.
	 * ''        - (empty string) If user is not logged in or with an unknown type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The membership type.
	 */
	public function membership_type() {
		$data = $this->membership_data();

		// Default type is empty.
		$type = '';

		// Available membership types.
		$types = array(
			'full',
			'unit',
			'free',
			'paused',
			'expired',
		);

		// All possible string values.
		if ( is_string( $data['membership'] ) && in_array( $data['membership'], $types, true ) ) {
			$type = $data['membership'];
		} elseif (
			is_numeric( $data['membership'] ) ||
			( is_bool( $data['membership'] ) && isset( $data['membership_full_level'] ) && is_numeric( $data['membership_full_level'] ) )
		) {
			$type = 'single';
		}

		return $type;
	}

	/**
	 * Get the list of installed plugins.
	 *
	 * We will exclude WPMUDEV plugins from the list.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function plugins() {
		// Make sure required functions are ready.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once \ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins   = array();
		$installed = get_plugins();

		// Extract and collect details we need.
		foreach ( $installed as $slug => $data ) {
			// Skip WPMUDEV plugins because we get them in different list.
			if ( ! empty( $data['WDP ID'] ) ) {
				continue;
			}

			// Only network active plugin should be considered as active.
			$active = is_multisite() ? is_plugin_active_for_network( $slug ) : is_plugin_active( $slug );

			$plugins[ $slug ] = array(
				'name'       => $data['Name'],
				'version'    => $data['Version'],
				'plugin_url' => $data['PluginURI'],
				'author'     => $data['Author'],
				'author_url' => $data['AuthorURI'],
				'network'    => $data['Network'],
				'active'     => $active,
			);
		}

		return $plugins;
	}

	/**
	 * Get the list of installed themes.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function themes() {
		$themes     = array();
		$installed  = wp_get_themes();
		$stylesheet = get_stylesheet();

		foreach ( $installed as $slug => $theme ) {
			if ( is_multisite() ) {
				$active = $theme->is_allowed() || $stylesheet === $slug; // network enabled or on main site.
			} else {
				// If the theme is available on main site it's "active".
				$active = $stylesheet === $slug;
			}

			$themes[ $slug ] = array(
				'name'       => $theme->display( 'Name', false ),
				'version'    => $theme->display( 'Version', false ),
				'author'     => $theme->display( 'Author', false ),
				'author_url' => $theme->display( 'AuthorURI', false ),
				'screenshot' => $theme->get_screenshot(),
				'parent'     => $theme->parent() ? $theme->get_template() : false,
				'active'     => $active,
			);
		}

		return $themes;
	}

	/**
	 * Get the list of WPMUDEV projects.
	 *
	 * Only plugins are in this list as of now.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function wpmudev_projects() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once \ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$projects  = array();
		$installed = get_plugins();

		// Extract and collect details we need.
		foreach ( $installed as $slug => $data ) {
			// Not a WPMUDEV Pro plugin.
			if ( empty( $data['WDP ID'] ) ) {
				continue;
			}

			// Project ID.
			$project_id = $data['WDP ID'];

			// On multisite, only consider network active plugins as active.
			$active = is_multisite() ? is_plugin_active_for_network( $slug ) : is_plugin_active( $slug );

			/**
			 * Collect extra data from individual plugins.
			 *
			 * @since 1.0.0
			 *
			 * @param string $extra Default extra data is an empty string.
			 * @param int    $pid   Project ID.
			 */
			$extra = apply_filters( 'wpmudev_api_project_extra_data', '', $project_id );

			$projects[ $project_id ] = array(
				'version' => $data['Version'],
				'active'  => $active,
				'extra'   => $extra,
			);
		}

		/**
		 * Allows modification of the plugin data that is sent to the server.
		 *
		 * @since 1.0.0
		 *
		 * @param array $projects The whole array of project details.
		 */
		return apply_filters( 'wpmudev_api_project_data', $projects );
	}

	/**
	 * Get currently logged in member user data.
	 *
	 * To avoid unwanted API calls, profile data will not be available
	 * unless we call it for the first time. Once called, it will be stored
	 * within the plugin options and will be cleared only when member logout.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function profile_data() {
		// Get profile data.
		$profile = API::get()->get_profile();

		if ( is_wp_error( $profile ) || empty( $profile ) || ! is_array( $profile ) ) {
			$profile = array();
		}

		// Make sure the structure is correct.
		$profile = wp_parse_args(
			$profile,
			array(
				'avatar'       => '',
				'member_since' => '',
				'name'         => '',
				'title'        => '',
				'user_name'    => '',
			)
		);

		/**
		 * Allows modification of the profile data.
		 *
		 * @since 1.0.0
		 *
		 * @param array $profile Profile data.
		 */
		return apply_filters( 'wpmudev_api_profile_data', $profile );
	}
}
