<?php

namespace TEC\Common\StellarWP\Uplink\Site;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\Config;

class Data {
	/**
	 * Container.
	 *
	 * @since 1.0.0
	 *
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// @phpstan-ignore-next-line
		$this->container = Config::get_container();
	}

	/**
	 * Build full stats for endpoints
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,array<mixed>> $stats Initial stats
	 *
	 * @return array<string,mixed>
	 */
	protected function build_full_stats( array $stats ): array {
		$stats['versions']['php']   = $this->get_php_version();
		$stats['versions']['mysql'] = $this->get_db_version();
		$stats['theme']             = $this->get_theme_info();
		$stats['site_language']     = $this->get_site_language();
		$stats['user_language']     = $this->get_user_language();
		$stats['is_public']         = $this->is_public();
		$stats['wp_debug']          = $this->is_debug_enabled();
		$stats['site_timezone']     = $this->get_timezone();
		$stats['totals']            = $this->get_totals();
		return $stats;
	}

	/**
	 * Build stats for sending to the license server.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,array<mixed>>
	 */
	protected function build_stats(): array {
		$stats = [
			'versions' => [
				'wp' => $this->get_wp_version(),
			],
			'network'  => [
				'multisite'         => (int) is_multisite(),
				'network_activated' => 0,
				'active_sites'      => $this->get_multisite_active_sites(),
			],
		];

		return $stats;

	}

	/**
	 * Gets the database version.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_db_version(): string {
		global $wpdb;

		$version = $wpdb->db_version();

		/**
		 * Filters the DB version.
		 *
		 * @since 1.0.0
		 *
		 * @param string $version DB version.
		 */
		$version = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_db_version', $version );

		return sanitize_text_field( $version );
	}

	/**
	 * Gets the domain for the site.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_domain(): string {
		$cache_key = 'stellarwp_uplink_domain';
		$domain    = $this->container->has( $cache_key ) ? $this->container->get( $cache_key ) : null;

		if ( null === $domain ) {
			$domain = is_multisite() ? $this->get_domain_multisite_option() : $this->get_site_domain();
			$this->container->bind( $cache_key, function() use ( $domain ) { return $domain; } );
		}

		/**
		 * Filters the domain for the site.
		 *
		 * @since 1.0.0
		 *
		 * @param string $domain Domain.
		 */
		$domain = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_domain', $domain );

		return sanitize_text_field( $domain );
	}

	/**
	 * Return domain for multisite
	 *
	 * @return string
	 */
	protected function get_domain_multisite_option(): string {
		/** @var string */
		$site_url = get_site_option( 'siteurl', '' );

		/** @var array<string> */
		$site_url = wp_parse_url( $site_url );
		if ( ! $site_url || ! isset( $site_url['host'] ) ) {
			return '';
		}

		return strtolower( $site_url['host'] );
	}

	/**
	 * Gets multi-site active site count.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_multisite_active_sites(): int {
		global $wpdb;

		$cache_key    = 'stellarwp_uplink_multisite_active_sites';

		/** @var int|null */
		$active_sites = $this->container->has( $cache_key ) ? $this->container->get( $cache_key ) : null;

		if ( null === $active_sites ) {
			if ( ! is_multisite() ) {
				$active_sites = 1;
			} else {
				$sql_count = "
					SELECT
						COUNT( `blog_id` )
					FROM
						`{$wpdb->blogs}`
					WHERE
						`public` = '1'
						AND `archived` = '0'
						AND `spam` = '0'
						AND `deleted` = '0'
				";

				$active_sites = (int) $wpdb->get_var( $sql_count );
			}

			$this->container->bind( $cache_key, function() use ( $active_sites ) { return $active_sites; } );
		}

		/**
		 * Filters the active site count for the site.
		 *
		 * @since 1.0.0
		 *
		 * @param int $active_sites Number of active sites.
		 */
		$active_sites = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_multisite_active_sites', $active_sites );

		return (int) $active_sites;
	}

	/**
	 * Gets the PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_php_version(): string {
		$version = phpversion();

		/**
		 * Filters the PHP version.
		 *
		 * @since 1.0.0
		 *
		 * @param string $version PHP version.
		 */
		$version = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_php_version', $version );

		return sanitize_text_field( $version );
	}

	/**
	 * Returns the domain of the single site installation
	 *
	 * Will try to read it from the $_SERVER['SERVER_NAME'] variable
	 * and fall back on the one contained in the siteurl option.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_site_domain() {
		/** @var string */
		$site_url = get_option( 'siteurl', '' );

		/** @var array<string> */
		$site_url = wp_parse_url( $site_url );
		if ( ! $site_url || ! isset( $site_url['host'] ) ) {
			if ( isset( $_SERVER['SERVER_NAME'] ) ) {
				return $_SERVER['SERVER_NAME'];
			}

			return '';
		}

		return strtolower( $site_url['host'] );
	}

	/**
	 * Gets the site language.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_site_language(): string {
		$locale = get_locale();

		/**
		 * Filters the locale.
		 *
		 * @since 1.0.0
		 *
		 * @param string $locale Site language.
		 */
		$locale = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_site_language', $locale );

		return sanitize_text_field( $locale );
	}

	/**
	 * Build and get the stats
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,mixed>
	 */
	public function get_stats(): array {
		$stats = $this->build_stats();

		/**
		 * Allow full stats data to be built and sent.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean $use_full_stats Whether to send full stats
		 */
		$use_full_stats = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/use_full_stats', false );

		if ( $use_full_stats ) {
			$stats = $this->build_full_stats( $stats );
		}

		/**
		 * Filter stats and allow plugins to add their own stats for tracking specific points of data.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string,mixed> $stats          Stats gathered by PUE Checker class.
		 * @param boolean             $use_full_stats Whether to send full stats.
		 * @param Data                $checker        Data object.
		 */
		$stats = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_stats', $stats, $use_full_stats, $this );

		return $stats;
	}

	/**
	 * Gets the theme data.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string>
	 */
	public function get_theme_info(): array {
		$theme = wp_get_theme();

		/** @var string */
		$name    = $theme->get( 'Name' );

		/** @var string */
		$version = $theme->get( 'Version' );

		$info = [
			'name'       => sanitize_text_field( $name ),
			'version'    => sanitize_text_field( $version ),
			'stylesheet' => sanitize_text_field( $theme->get_stylesheet() ),
			'template'   => sanitize_text_field( $theme->get_template() ),
		];

		/**
		 * Filters the theme info.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string> $info Theme info.
		 */
		$info = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_theme_info', $info );

		return (array) $info;
	}

	/**
	 * Gets the timezone string.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_timezone(): string {
		$cache_key = 'stellarwp_uplink_timezone';

		/** @var string|null */
		$timezone  = $this->container->has( $cache_key ) ? $this->container->get( $cache_key ) : null;

		if ( null === $timezone ) {
			$current_offset = get_option( 'gmt_offset', 0 );

			if ( ! is_numeric( $current_offset ) ) {
				$current_offset = 0;
			}

			/** @var string */
			$tzstring = get_option( 'timezone_string', '' );

			// Remove old Etc mappings. Fallback to gmt_offset.
			if ( false !== strpos( $tzstring, 'Etc/GMT' ) ) {
				$timezone = '';
			}

			// Create a UTC+- zone if no timezone string exists
			if ( empty( $tzstring ) ) {
				if ( 0 === $current_offset ) {
					$timezone = 'UTC+0';
				} elseif ( $current_offset < 0 ) {
					$timezone = 'UTC' . $current_offset;
				} else {
					$timezone = 'UTC+' . $current_offset;
				}
			}

			$this->container->bind( $cache_key, function() use ( $timezone ) { return $timezone; } );
		}

		/**
		 * Filters the timezone.
		 *
		 * @since 1.0.0
		 *
		 * @param string $timezone Site timezone.
		 */
		$timezone = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_timezone', $timezone ?: '' );

		return sanitize_text_field( $timezone );
	}

	/**
	 * Gets the site's post totals.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int>
	 */
	public function get_totals(): array {
		global $wpdb;

		$cache_key = 'stellarwp_uplink_totals';

		/** @var array<int>|null */
		$totals    = $this->container->has( $cache_key ) ? $this->container->get( $cache_key ) : null;

		if ( null === $totals ) {
			$totals = [
				'all_post_types' => (int) $wpdb->get_var( "SELECT COUNT(1) FROM {$wpdb->posts}" ),
			];

			$this->container->bind( $cache_key, function() use ( $totals ) { return $totals; } );
		}

		/**
		 * Filters the site post totals.
		 *
		 * @since 1.0.0
		 *
		 * @param array<int> $totals Site post totals.
		 */
		$totals = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_totals', $totals );

		return (array) $totals;
	}

	/**
	 * Gets the user language.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_user_language(): string {
		$locale = get_user_locale();

		/**
		 * Filters the locale.
		 *
		 * @since 1.0.0
		 *
		 * @param string $locale Site language.
		 */
		$locale = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_user_language', $locale );

		return sanitize_text_field( $locale );
	}

	/**
	 * Gets the WordPress version.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_wp_version(): string {
		global $wp_version;

		$version = $wp_version;

		/**
		 * Filters the WordPress version.
		 *
		 * @since 1.0.0
		 *
		 * @param string $wp_version WordPress version.
		 */
		$version = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_wp_version', $version );

		return sanitize_text_field( $version );
	}

	/**
	 * Gets the debug state.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_debug_enabled(): bool {
		$debug_status = (bool) ( defined( 'WP_DEBUG' ) && WP_DEBUG );

		/**
		 * Filters the Debug status.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $debug_status Debug status.
		 */
		$debug_status = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/is_debug_enabled', $debug_status );

		return (bool) $debug_status;
	}

	/**
	 * Returns whether or not the site is public.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_public(): bool {
		$cache_key = 'stellarwp_uplink_is_public';

		/** @var bool|null */
		$is_public = $this->container->has( $cache_key ) ? $this->container->get( $cache_key ) : null;

		if ( null === $is_public ) {
			$is_public = (bool) get_option('blog_public', false);
			$this->container->bind( $cache_key, function() use ( $is_public ) { return $is_public; } );
		}

		/**
		 * Filters the DB version.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $is_public Is the site public?
		 */
		$is_public = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix(). '/get_db_version', $is_public );

		return (bool) $is_public;
	}
}
