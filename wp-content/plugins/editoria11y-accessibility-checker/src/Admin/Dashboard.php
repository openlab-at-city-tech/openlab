<?php
/**
 * Editoria11y dashboard menu + render.
 *
 * Top-level wp-admin sidebar entry that loads the in-page accessibility
 * report (the bundled `editoria11y-dashboard.js` module). Capability
 * gates on the `ed11y_report_restrict` setting: when set, only
 * administrators see the menu; otherwise any user with `edit_others_posts`
 * (so editors can review report data without the manage_options grant).
 *
 * @package Editoria11y
 */

namespace Editoria11y\Admin;

use Editoria11y\Installer;
use Editoria11y\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Wires the dashboard menu page + its render callback.
 */
class Dashboard {

	/** Wire the admin_menu hook. */
	public function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
	}

	/** Add the dashboard to the wp-admin sidebar. */
	public static function register_menu() {
		$capability = ed11y_report_reader_capability();
		$hook       = add_menu_page(
			esc_html__( 'Editoria11y Accessibility Report', 'editoria11y' ),
			esc_html__( 'Accessibility', 'editoria11y' ),
			$capability,
			'editoria11y',
			array( __CLASS__, 'render' ),
			'dashicons-chart-bar',
			90
		);

		// Ensure the global $title is populated before admin-header.php runs.
		// get_admin_page_title() can return null on the first hit if the
		// toplevel_page_editoria11y hook isn't registered yet, which then
		// trips strip_tags(null) in wp-admin/admin-header.php on PHP 8.1+.
		if ( $hook ) {
			add_action(
				"load-{$hook}",
				static function () {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- intentional: works around PHP 8.1+ strip_tags(null) fatal when admin-header.php runs before get_admin_page_title() has a registered hook.
					$GLOBALS['title'] = __( 'Editoria11y Accessibility Report', 'editoria11y' );
				}
			);
		}
	}

	/**
	 * Render the dashboard page. Lazy-creates DB tables on first hit
	 * (covers network-activation paths where activate() didn't fire).
	 *
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function render() {

		// Lazy-create DB if network activation failed.
		if ( ! Installer::check_tables() ) {
			printf(
				'<div class="notice notice-error notice-alt notice-large"><p><strong>%1$s</strong> %2$s</p><p>%3$s</p></div>',
				esc_html__( 'Error:', 'editoria11y' ),
				esc_html__( 'Editoria11y database tables are missing.', 'editoria11y' ),
				sprintf(
					/* translators: 1: bug report link, 2: site health page link. */
					esc_html__( 'Try deactivating and reactivating the plugin to reset config and recreate the tables, or %1$s with the information from the WordPress, Server and Database sections on your %2$s.', 'editoria11y' ),
					'<a href="https://github.com/itmaybejj/editoria11y-wp/issues">' . esc_html__( 'post a bug report', 'editoria11y' ) . '</a>',
					'<a href="' . esc_url( get_admin_url() . 'site-health.php?tab=debug' ) . '">' . esc_html__( 'site health page', 'editoria11y' ) . '</a>'
				)
			);
			return;
		}

		// The dashboard never runs a scan, so the bundled library isn't loaded
		// here — only the lang pack is needed for `Lang.testNames` lookups.
		// Dropping the library also drops `wp-api` (and its `wpApiSettings`
		// global), so REST root + nonce are passed through the JSON config
		// blob below; `wp_add_inline_script` does not attach to script modules.
		wp_enqueue_script_module(
			'editoria11y-lang',
			trailingslashit( ED11Y_ASSETS ) . 'lib/js/lang/' . ed11y_lang_pack_filename() . '.js',
			array(),
			Plugin::VERSION,
			array( 'in_footer' => true )
		);
		wp_enqueue_script_module(
			'editoria11y-js-dash',
			trailingslashit( ED11Y_ASSETS ) . 'js/editoria11y-dashboard.js',
			array( 'editoria11y-lang' ),
			Plugin::VERSION,
			array( 'in_footer' => true )
		);
		// The full editoria11y.min.css library stylesheet is intentionally not
		// enqueued on the dashboard; only the dashboard-specific CSS below is needed.
		wp_enqueue_style( 'editoria11y-css', trailingslashit( ED11Y_ASSETS ) . 'css/editoria11y-dashboard.css', null, Plugin::VERSION );

		// Two distinct nonces:
		// restNonce → X-WP-Nonce header for /wp-json/ed11y/v1/* calls.
		// WordPress REST verifies this against the literal
		// 'wp_rest' action; any other action fails as anonymous.
		// csvNonce  → _wpnonce param on the "Download as CSV" link.
		// CsvExport::handle_request() verifies against 'ed1ref'.
		// Both flows previously got their nonces from different sources
		// (wpApiSettings vs the page-level JSON blob); the v3 cutover folds
		// them into one config object but they are NOT interchangeable.
		$config = array(
			'restNonce' => wp_create_nonce( 'wp_rest' ),
			'csvNonce'  => wp_create_nonce( 'ed1ref' ),
			'csa'       => ed11y_is_csa_active(),
			'root'      => esc_url_raw( rest_url() ),
			'locale'    => ed11y_lang_pack_filename(),
			// User-facing strings for the dashboard module. Script modules
			// can't take wp_set_script_translations(), so translations flow
			// through this JSON blob (server-rendered, _n() picks the plural
			// form for the active locale). Plural keys are [singular, plural]
			// arrays; the JS helper picks one based on count. statusLabels
			// keys mirror raw WP post_status slugs so the JS can look up a
			// translated label without a JS-side title-case hack.
			'i18n'      => array(
				'viewAllIssues'           => __( 'View all issues', 'editoria11y' ),
				'viewAllPages'            => __( 'View issues on all pages', 'editoria11y' ),
				/* translators: %s: name of an alert/test, e.g. "Image is missing alt text". */
				'alertReport'             => __( 'Alert report: "%s"', 'editoria11y' ),
				/* translators: %s: post type slug, e.g. "page" or "post". */
				'alertsOnType'            => __( 'Alerts on pages of type "%s"', 'editoria11y' ),
				'alertsByAuthor'          => __( 'Alerts on pages created by author', 'editoria11y' ),
				/* translators: %s: author display name. */
				'alertsByAuthorN'         => __( 'Alerts on pages created by %s', 'editoria11y' ),
				'dismissedBy'             => __( 'Alerts dismissed by', 'editoria11y' ),
				/* translators: %s: user display name of the dismissor. */
				'dismissedByN'            => __( 'Alerts dismissed by %s', 'editoria11y' ),
				/* translators: %s: post-status label, e.g. "Published" or "Draft". */
				'statusPages'             => __( '%s pages', 'editoria11y' ),
				'apiError'                => __( 'API error.', 'editoria11y' ),
				'sessionExpired'          => __( 'Your login session expired. Reload this page to continue.', 'editoria11y' ),
				'loading'                 => __( 'loading...', 'editoria11y' ),
				/* translators: %d: pagination page number. */
				'pageN'                   => __( 'Page %d', 'editoria11y' ),
				'colAlerts'               => __( 'Alerts', 'editoria11y' ),
				'colDevAlerts'            => __( 'Dev alerts', 'editoria11y' ),
				'colPage'                 => __( 'Page', 'editoria11y' ),
				'colPath'                 => __( 'Path', 'editoria11y' ),
				'colType'                 => __( 'Type', 'editoria11y' ),
				'colStatus'               => __( 'Status', 'editoria11y' ),
				'colUpdated'              => __( 'Updated', 'editoria11y' ),
				'colAuthor'               => __( 'Author', 'editoria11y' ),
				'colDetected'             => __( 'Detected', 'editoria11y' ),
				'colAlert'                => __( 'Alert', 'editoria11y' ),
				'colCount'                => __( 'Count', 'editoria11y' ),
				'colPages'                => __( 'Pages', 'editoria11y' ),
				'colOn'                   => __( 'On', 'editoria11y' ),
				'colDismissedAlert'       => __( 'Dismissed alert', 'editoria11y' ),
				'colMarked'               => __( 'Marked', 'editoria11y' ),
				'colCurrent'              => __( 'Current', 'editoria11y' ),
				'colBy'                   => __( 'By', 'editoria11y' ),
				'sectionAlertsByPage'     => __( 'Alerts by page', 'editoria11y' ),
				'sectionRecent'           => __( 'Recent alerts', 'editoria11y' ),
				'sectionAlertTypes'       => __( 'Alert types', 'editoria11y' ),
				'sectionDismissals'       => __( 'Dismissals', 'editoria11y' ),
				'sectionRecentDismissals' => __( 'Recent dismissals', 'editoria11y' ),
				'csvDownload'             => __( 'Download results as CSV', 'editoria11y' ),
				'noneCell'                => __( 'None', 'editoria11y' ),
				'no'                      => __( 'No', 'editoria11y' ),
				'yes'                     => __( 'Yes', 'editoria11y' ),
				'na'                      => __( 'n/a', 'editoria11y' ),
				'emptyWpButton'           => __( 'Empty button-style link', 'editoria11y' ),
				/* translators: tooltip on a column header that's currently sorted descending. */
				'sortDescending'          => __( 'descending', 'editoria11y' ),
				/* translators: tooltip on a column header that's currently sorted ascending. */
				'sortAscending'           => __( 'ascending', 'editoria11y' ),
				'results'                 => array(
					/* translators: %d: number of result rows returned (singular form). */
					__( '%d result', 'editoria11y' ),
					/* translators: %d: number of result rows returned (plural form). */
					__( '%d results', 'editoria11y' ),
				),
				// Translated labels for every registered post status, keyed on
				// the raw WP slug so the JS can index by `result.post_status`.
				// Built from the registry (like CrawlerPage::status_options())
				// so custom editorial-workflow statuses get labels too instead
				// of falling back to their raw slug in the report table.
				'statusLabels'            => self::status_labels(),
			),
		);

		/**
		 * Filter the dashboard page title.
		 *
		 * @since 2.2.0
		 *
		 * @param string $title The page title. Default 'Editoria11y accessibility checker'.
		 */
		$page_title = apply_filters( 'editoria11y_dashboard_title', __( 'Editoria11y Accessibility Report', 'editoria11y' ) );

		echo '<div id="ed1">
			<h1>' . esc_html( $page_title ) . '</h1>';

		/**
		 * Fires at the top of the Editoria11y dashboard page, after the title.
		 *
		 * Use this hook to inject custom content, forms, or UI elements at the top of the dashboard.
		 *
		 * @since 2.2.0
		 */
		do_action( 'editoria11y_dashboard_top' );

		echo '<div id="ed1-recent-wrapper"></div>
			<div id="ed1-page-wrapper"></div>
			<div id="ed1-results-wrapper"></div>
			<div id="ed1-dismissals-wrapper"></div>
		</div>
		<script id="editoria11y-dash-config" type="application/json">
			' . wp_json_encode( $config ) . '
		</script>';
	}

	/**
	 * Slug → translated label for every registered post status.
	 *
	 * @return array<string, string>
	 */
	private static function status_labels(): array {
		$labels = array();
		foreach ( get_post_stati( array(), 'objects' ) as $slug => $status ) {
			$labels[ $slug ] = (string) $status->label;
		}
		return $labels;
	}
}
