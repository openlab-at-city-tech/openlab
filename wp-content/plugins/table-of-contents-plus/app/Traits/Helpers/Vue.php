<?php
namespace AIOSEO\TableOfContents\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\TableOfContents\Api;
use AIOSEO\TableOfContents\Models;

/**
 * Generates the data we need for Vue.
 *
 * @since 1.0.0
 */
trait Vue {
	/**
	 * The data to pass to Vue.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $vueData = [];

	/**
	 * Returns the data for Vue.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $currentPage The current page.
	 * @return array               The data.
	 */
	public function getVueData( $currentPage = null ) {
		global $wp_version;

		static $showNotificationsDrawer = null;
		if ( null === $showNotificationsDrawer ) {
			$showNotificationsDrawer = aioseoTableOfContents()->core->cache->get( 'show_notifications_drawer' ) ? true : false;

			// IF this is set to true, let's disable it now so it doesn't pop up again.
			if ( $showNotificationsDrawer ) {
				aioseoTableOfContents()->core->cache->delete( 'show_notifications_drawer' );
			}
		}

		$this->vueData = [
			// The following data is needed on all screens.
			'wpVersion'                    => $wp_version,
			'page'                         => $currentPage,
			'screen'                       => aioseoTableOfContents()->helpers->getCurrentScreen(),
			'internalOptions'              => aioseoTableOfContents()->internalOptions->all(),
			'options'                      => aioseoTableOfContents()->options->all(),
			'settings'                     => aioseoTableOfContents()->vueSettings->all(),
			'tocSettings'                  => aioseoTableOfContents()->standalone->frontend->get_options(),
			'tocPostTypes'                 => Api\TocSettings::getPostTypes(),
			'notifications'                => array_merge( Models\Notification::getNotifications( false ), [ 'force' => $showNotificationsDrawer ] ),
			'helpPanel'                    => [],
			'urls'                         => [
				'domain'        => $this->getSiteDomain(),
				'mainSiteUrl'   => $this->getSiteUrl(),
				'home'          => home_url(),
				'restUrl'       => rest_url(),
				'editScreen'    => admin_url( 'edit.php' ),
				'publicPath'    => aioseoTableOfContents()->core->assets->normalizeAssetsHost( plugin_dir_url( AIOSEO_TABLE_OF_CONTENTS_FILE ) ),
				'assetsPath'    => aioseoTableOfContents()->core->assets->getAssetsPath(),
				'marketingSite' => $this->getMarketingSiteUrl(),
				'connect'       => admin_url( 'index.php?page=table-of-contents-connect' )
			],
			'isDev'                        => $this->isDev(),
			'isAioseoActive'               => function_exists( 'aioseo' ),
			'isAioseoInstalled'            => file_exists( WP_PLUGIN_DIR . '/all-in-one-seo-pack/all_in_one_seo_pack.php' )
				|| file_exists( WP_PLUGIN_DIR . '/all-in-one-seo-pack-pro/all_in_one_seo_pack.php' ),
			'isBrokenLinkCheckerActive'    => function_exists( 'aioseoBrokenLinkChecker' ),
			'isBrokenLinkCheckerInstalled' => file_exists( WP_PLUGIN_DIR . '/broken-link-checker-seo/aioseo-broken-link-checker.php' )
				|| file_exists( WP_PLUGIN_DIR . '/aioseo-broken-link-checker/aioseo-broken-link-checker.php' ),
			'isSsl'                        => is_ssl(),
			'isMultisite'                  => is_multisite(),
			'isNetworkAdmin'               => is_network_admin(),
			'mainSite'                     => is_main_site(),
			'hasUrlTrailingSlash'          => '/' === user_trailingslashit( '' ),
			'nonce'                        => wp_create_nonce( 'wp_rest' ),
			'translations'                 => $this->getJedLocaleData( 'table-of-contents-plus' )
		];

		// In multisite, super admins may not have explicit roles on subsites.
		// Ensure they have administrator role and capabilities for proper access.
		$userData     = wp_get_current_user();
		$roles        = $userData->roles;
		$capabilities = $userData->allcaps;

		// If the user is a network admin, and doesn't have a user on the subsite, give him admin role/caps.
		if ( is_multisite() && is_super_admin() && empty( $roles ) ) {
			$roles     = [ 'administrator' ];
			$adminRole = get_role( 'administrator' );
			if ( is_a( $adminRole, 'WP_Role' ) ) {
				$capabilities = $adminRole->capabilities;
			}
		}

		$this->vueData['user'] = [
			'canManage'    => aioseoTableOfContents()->access->canManage(),
			'roles'        => $roles,
			'capabilities' => $capabilities,
			'locale'       => function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale()
		];

		switch ( $currentPage ) {
			case 'about':
				$this->addAboutData();
				break;
			default:
				break;
		}

		return $this->vueData;
	}

	/**
	 * Adds the data for the About Us screen.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function addAboutData() {
		$this->vueData['plugins'] = $this->getPluginData();
	}

	/**
	 * Returns Jed-formatted localization data.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $domain The text domain.
	 * @return array          The information of the locale.
	 */
	private function getJedLocaleData( $domain ) {
		$translations = get_translations_for_domain( $domain );

		$locale = [
			'' => [
				'domain' => $domain,
				'lang'   => is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale(),
			],
		];

		if ( ! empty( $translations->headers['Plural-Forms'] ) ) {
			$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
		}

		foreach ( $translations->entries as $msgid => $entry ) {
			if ( empty( $entry->translations ) || ! is_array( $entry->translations ) ) {
				continue;
			}

			$locale[ $msgid ] = $entry->translations;
		}

		return $locale;
	}

	/**
	 * Returns the marketing site URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string The marketing site URL.
	 */
	private function getMarketingSiteUrl() {
		if ( defined( 'AIOSEO_MARKETING_SITE_URL' ) && AIOSEO_MARKETING_SITE_URL ) {
			return AIOSEO_MARKETING_SITE_URL;
		}

		return 'https://aioseo.com/';
	}
}