<?php
/**
 * Runtime hook orchestrator for the Editoria11y plugin.
 *
 * Owns nothing data-side — schema, migration, rehash, and lifecycle hooks live
 * on \Editoria11y\Installer. This class is purely the WordPress-integration
 * layer: it wires plugins_loaded callbacks to load i18n, shared functions,
 * admin pages, and REST controllers in the right priority order.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

use Editoria11y\Admin\CsvExport;
use Editoria11y\Admin\Dashboard;
use Editoria11y\Controller\ApiConfig;
use Editoria11y\Controller\ApiCrawler;
use Editoria11y\Controller\ApiResults;
use Editoria11y\Controller\ApiDismissals;
use Editoria11y\Form\CrawlerPage;
use Editoria11y\Form\CustomRulesPage;
use Editoria11y\Form\MigrationPanel;
use Editoria11y\Form\NetworkCustomRulesPage;
use Editoria11y\Form\NetworkLicensePage;
use Editoria11y\Form\NetworkSettingsPage;
use Editoria11y\Form\SettingsPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin
 */
class Plugin {

	/** Library version; used as cache buster for enqueued assets. */
	const VERSION = '3.0.7';

	/**
	 * Wires plugins_loaded callbacks in dependency order.
	 *
	 * Priorities are explicit (1..5) so that other plugins / themes hooking the
	 * same actions can predict ordering relative to ours: i18n must come before
	 * any code that renders translatable strings, and includes/admin must run
	 * before the REST controllers reach for the helpers in src/functions.php.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );
		add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );
		add_action( 'plugins_loaded', array( $this, 'admin' ), 4 );
		add_action( 'plugins_loaded', array( $this, 'api' ), 5 );
	}

	/**
	 * Load translation files from the plugin's languages/ directory.
	 *
	 * This plugin is distributed via Freemius rather than WordPress.org, so
	 * WP's just-in-time translation loader cannot find the .mo files on its
	 * own — load_plugin_textdomain() is still the right entry point. WP.org
	 * builds get translations auto-loaded by the platform regardless.
	 */
	public function i18n() {
		// phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- Freemius-distributed builds need the explicit call; WP's just-in-time loader only auto-loads WordPress.org-hosted slugs. See the method docblock above.
		load_plugin_textdomain( 'editoria11y', false, dirname( ED11Y_BASE ) . '/languages/' );
	}

	/** Load shared frontend / utility functions. */
	public function includes() {
		require_once ED11Y_SRC . 'functions.php';
	}

	/**
	 * Wire admin-only classes: settings page (registration + render),
	 * the migration panel (state notice + AJAX), the Custom Rules
	 * submenu page, the dashboard menu, and the CSV export endpoint.
	 *
	 * Each class owns its own hook registrations via `init()`. The
	 * shared procedural helpers in `functions.php` are still required
	 * here because admin-side renderers reach for `ed11y_get_raw_setting`,
	 * `ed11y_is_csa_active`, etc.
	 */
	public function admin() {
		if ( is_admin() ) {
			require_once ED11Y_SRC . 'functions.php';

			$settings_page = new SettingsPage();
			$settings_page->init();

			// Multisite super-admins get a Network → Settings → Editoria11y
			// defaults page that authors the network-default option blobs.
			// Single-site installs skip registration entirely; the read
			// pipeline still consults the option but it stays empty without
			// a write path.
			if ( is_multisite() ) {
				$network_settings_page = new NetworkSettingsPage();
				$network_settings_page->init();

				// Network-level custom rules CRUD. Premium-only — the
				// file is stripped from the free build, so the
				// preprocessor gate keeps the reference out of the free
				// shell.
			}

			$migration_panel = new MigrationPanel();
			$migration_panel->init();

			// Freemius preprocessor strips this if-block from the free
			// build, removing the references to the @fs_premium_only-stripped
			// CrawlerPage and CustomRulesPage classes. Dashboard is shared
			// (free build keeps it) and is instantiated below.

			$dashboard = new Dashboard();
			$dashboard->init();

			$csv_export = new CsvExport();
			$csv_export->init();

			// Inject WCAG fixes for the Freemius SDK's admin chrome
			// (sticky notices and modal forms). Must enqueue site-wide
			// in admin because Freemius renders its notices on any
			// screen, not just our settings page.
			FreemiusAccessibilityShim::register();
		}
	}

	/** Register REST API controllers under the ed11y/v1 namespace. */
	public function api() {
		$ed11y_api_results = new ApiResults();
		$ed11y_api_results->init();
		$ed11y_api_dismissals = new ApiDismissals();
		$ed11y_api_dismissals->init();
		$ed11y_api_config = new ApiConfig();
		$ed11y_api_config->init();
	}
}
