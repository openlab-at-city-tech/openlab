<?php
// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

/**
 * NextGEN Gallery loader class.
 */
class nggLoader {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = NGG_PLUGIN_VERSION;

	/**
	 * Options array.
	 *
	 * @var array
	 */
	public $options = [];

	/**
	 * Admin panel instance.
	 *
	 * @var object|null
	 */
	public $nggAdminPanel = null;

	/**
	 * Manage album instance.
	 *
	 * @var object|null
	 */
	public $manage_album;

	/**
	 * Manage page instance.
	 *
	 * @var nggManageGallery|nggManageAlbum
	 */
	public $manage_page;

	public function __construct() {
		$this->load_options();
		$this->define_constant();
		$this->define_tables();
		$this->load_dependencies();

		// Start this plugin once all other plugins are fully loaded.
		add_action( 'plugins_loaded', [ $this, 'start_plugin' ] );
		add_action( 'wpmu_new_blog', [ $this, 'multisite_new_blog' ], 10, 6 );

		// Add some links on the plugin page.
		add_filter( 'plugin_row_meta', [ $this, 'add_plugin_links' ], 10, 2 );
	}

	public function start_plugin() {
		// Content Filters.
		add_filter( 'ngg_gallery_name', 'sanitize_title' );

		// Check if we are in the admin area.
		if ( is_admin() ) {
			if ( get_option( 'ngg_init_check' ) ) {
				add_action( 'admin_notices', [ $this, 'output_init_check_error' ] );
			}

			if ( get_option( 'ngg_upgrade_error' ) ) {
				add_action( 'admin_notices', [ $this, 'output_upgrade_error_notice' ] );
			}

			if ( get_option( 'ngg_schema_error' ) ) {
				add_action( 'admin_notices', [ $this, 'output_schema_error_notice' ] );
			}

			add_action( 'admin_init', [ $this, 'check_schema_tables' ] );
		} else {
			$settings = \Imagely\NGG\Settings\Settings::get_instance();
			if ( $settings->get( 'useMediaRSS' ) ) {
				add_action( 'wp_head', [ 'nggMediaRss', 'add_mrss_alternate_link' ] );
			}
		}
	}

	/**
	 * Displays an initialization failure.
	 *
	 * Deliberately NOT capability-gated, unlike the two notices below. Its only remaining writer
	 * is Util\Installer::set_role_caps(), which sets it when the site has no administrator role
	 * at all -- exactly the situation where a current_user_can() gate is least trustworthy, and
	 * gating it could hide the message about broken roles from everyone. The message is also a
	 * fixed translated string that carries no server detail, so there is nothing here to
	 * disclose.
	 */
	public function output_init_check_error() {
		printf( "<div id='message' class='error'><p><strong>%s</strong></p></div>", esc_html( get_option( 'ngg_init_check' ) ) );
	}

	/**
	 * Displays a failure recorded during the upgrade routines (a stale lock that couldn't be
	 * reclaimed, a dedupe step that couldn't run). Kept in its own option, separate from
	 * ngg_init_check, because Installer::set_role_caps() unconditionally clears that option on
	 * every successful run -- which happens in the same request these failures are recorded in
	 * -- and would wipe the notice before this admin_notices hook ever runs.
	 *
	 * Capability-gated because this message can carry $wpdb->last_error verbatim (see
	 * Util\Installer::update()). admin_notices fires on every wp-admin page, including
	 * profile.php, which every logged-in user can load -- so without the gate a MySQL error
	 * string would be shown to the whole authenticated user base. Those strings routinely name
	 * the table prefix, and for permission or connection failures the database account and host
	 * ("CREATE command denied to user 'x'@'10.0.0.1'"). The escaping is already correct; the
	 * problem this closes is the audience, not the encoding.
	 *
	 * manage_options, not activate_plugins: on multisite map_meta_cap() appends
	 * manage_network_plugins to activate_plugins unless the network Plugins menu is enabled
	 * (wp-includes/capabilities.php), and populate_roles() grants that to no role -- so a site's
	 * own administrator would never see this, while the tables it reports on are per-blog.
	 * manage_options is also this plugin's dominant convention.
	 */
	public function output_upgrade_error_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		printf( "<div id='message' class='error'><p><strong>%s</strong></p></div>", esc_html( get_option( 'ngg_upgrade_error' ) ) );
	}

	/**
	 * Displays a missing pictures table. Kept in its own option rather than reusing
	 * ngg_upgrade_error, because this describes a condition that is either true right now or is
	 * not -- so it clears itself once the table exists, which must not also discard an
	 * upgrade-step failure that is still worth reporting.
	 *
	 * Capability-gated for the same reason as output_upgrade_error_notice(): the installer
	 * appends the database's own error to this message so support can see why the CREATE failed,
	 * and that detail is for whoever can act on it, not for every subscriber who opens their
	 * profile page. manage_options rather than activate_plugins for the multisite reason given
	 * on output_upgrade_error_notice().
	 */
	public function output_schema_error_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		printf( "<div id='message' class='error'><p><strong>%s</strong></p></div>", esc_html( get_option( 'ngg_schema_error' ) ) );
	}

	/**
	 * Re-checks that the plugin's tables exist, outside the installer.
	 *
	 * Covers all three tables rather than just the pictures table, because it shares
	 * ngg_schema_error with the installer's own verification -- which reports a missing gallery
	 * or album table too. A pictures-only check here would clear a notice about a table it never
	 * looked at.
	 *
	 * The installer's own missing-table check only runs while an upgrade is pending: once
	 * ngg_plugin_version matches NGG_PLUGIN_VERSION and the module list is unchanged,
	 * Util\Installer::update() never reaches the install handlers again. A site whose pictures
	 * table is absent -- because a CREATE TABLE failed on an index limit (issue #989), or because
	 * a migration or restore lost it -- therefore stays broken and unexamined indefinitely, and
	 * deactivating and reactivating the plugin is a no-op. With the table gone every gallery is
	 * permanently empty and uploads fail with six unrelated errors, so the cheapest useful thing
	 * is to notice and say so.
	 *
	 * Rate-limited by a transient: this adds a few queries to admin requests, and a missing table
	 * is not a condition that changes minute to minute. The transient is set only after every
	 * probe has actually answered -- setting it first would let one failed query consume the
	 * whole window. The notice itself is registered by start_plugin() on the next admin request
	 * rather than here -- admin_init has already run by the time this fires, so hooking
	 * admin_notices now would only work for this one request and would risk double-printing
	 * alongside the check above.
	 */
	public function check_schema_tables() {
		global $wpdb;

		// admin_init is not admin-page-only. wp-admin/admin-ajax.php fires it after checking
		// only that $_REQUEST['action'] is a non-empty scalar -- before any authentication or
		// action dispatch -- and is_admin() is true there, so this callback would otherwise run
		// for an unauthenticated request to admin-ajax.php?action=<anything> and let that caller
		// write the transient and clear ngg_schema_error. admin-post.php is the same entry point.
		// Gate on the capability that matches who the resulting notice is addressed to, and skip
		// AJAX outright: the query budget this check is designed around is one query per admin
		// page load, not one per AJAX request. manage_options rather than activate_plugins for
		// the multisite reason given on output_upgrade_error_notice() -- with activate_plugins
		// this check never ran at all for a subsite administrator, and the tables are per-blog.
		if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( get_transient( 'ngg_schema_tables_check' ) ) {
			return;
		}

		$tables = [
			$wpdb->nggpictures => __( 'NextGEN Gallery: the images table is missing from your database, so no gallery can hold an image.', 'nggallery' ),
			$wpdb->nggallery   => __( 'NextGEN Gallery: the galleries table is missing from your database, so no gallery can be saved.', 'nggallery' ),
			$wpdb->nggalbum    => __( 'NextGEN Gallery: the albums table is missing from your database, so no album can be saved.', 'nggallery' ),
		];

		$missing        = [];
		$missing_tables = [];

		foreach ( $tables as $table => $summary ) {
			// Checked before the probe as well as after it. wpdb::query() returns false *before*
			// it calls flush() when $wpdb->ready is false, so that query records no error at all
			// and last_error still holds whatever the previous one left -- an empty string after
			// any success -- while get_var() reads a stale last_result. The last_error test below
			// cannot see that, which would send a connection that never ran the query down the
			// success path: "absent" for every table, or worse, delete_option( 'ngg_schema_error' )
			// erasing an installer-recorded diagnostic. Same guard as
			// nggallery_table_exists() in admin/install.php.
			if ( ! $wpdb->ready ) {
				$this->record_schema_probe_failure( '' );
				return;
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', [ $wpdb->esc_like( $table ) ] ) );

			// get_var() returns null both for "no rows" and for a query that FAILED -- wpdb::query()
			// returns false, last_result stays empty, and get_var() hands back null either way. So
			// a lost connection whose reconnect fails, a statement killed by max_statement_time,
			// $wpdb->ready === false, or a proxy rejecting SHOW is indistinguishable from a table
			// that genuinely is not there. Treating that as absence is exactly the conflation the
			// rest of this feature exists to close, and here it would assert something false: a red
			// banner on every admin screen claiming the images table is missing when it is not.
			// Bail without writing, so a momentary blip doesn't cost 12 hours of false alarm.
			// record_schema_probe_failure() owns what does get written -- a short transient, and
			// the captured error once the failure has proved durable.
			if ( ! empty( $wpdb->last_error ) || ! $wpdb->ready ) {
				$this->record_schema_probe_failure( $wpdb->last_error );
				return;
			}

			if ( ! $exists ) {
				$missing[]        = $summary;
				$missing_tables[] = $table;
			}
		}

		// Only now that every probe has actually answered is it safe to spend the window.
		set_transient( 'ngg_schema_tables_check', 1, 12 * HOUR_IN_SECONDS );

		// The database answered, so any previous failure streak is over.
		if ( get_option( 'ngg_schema_check_failures' ) ) {
			delete_option( 'ngg_schema_check_failures' );
		}

		if ( get_option( 'ngg_schema_check_reported' ) ) {
			delete_option( 'ngg_schema_check_reported' );
		}

		if ( ! $missing_tables ) {
			if ( get_option( 'ngg_schema_error' ) ) {
				delete_option( 'ngg_schema_error' );
				delete_option( 'ngg_schema_error_tables' );
			}
			return;
		}

		// Keep the installer's message rather than replacing it -- that one can name the
		// database error behind the failure, which this check has no way to recover after the
		// fact -- but only while it still describes the tables that are actually missing now.
		// The stored value is otherwise frozen for the rest of the release: this branch never
		// overwrote, the self-clearing branch above only fires when nothing is missing, and the
		// installer rewrites it only while an upgrade is pending ($do_upgrade stays false until
		// ngg_plugin_version changes). So a second table going missing, or the first one coming
		// back while another is still gone, left the banner naming the wrong table for a whole
		// release cycle.
		//
		// Which tables a stored message describes is compared through its own option rather than
		// by searching the message text: the installer's wording differs from this check's ("could
		// not be created" vs "is missing from your database") and carries the appended database
		// error, so no text comparison could recognise it.
		$described = get_option( 'ngg_schema_error_tables' );

		if ( get_option( 'ngg_schema_error' ) && is_array( $described ) && ! array_diff( $described, $missing_tables ) && ! array_diff( $missing_tables, $described ) ) {
			return;
		}

		$missing[] = __( 'Please check your database settings.', 'nggallery' );
		update_option( 'ngg_schema_error', implode( ' ', $missing ) );
		update_option( 'ngg_schema_error_tables', $missing_tables, false );
	}

	/**
	 * Records that check_schema_tables() could not get an answer out of the database.
	 *
	 * Writes nothing about the tables themselves -- whether they exist is unknown, and claiming
	 * they are missing is the conflation this whole check refuses to make.
	 *
	 * @param string $probe_error The database's own error, empty when the query never ran.
	 * @return void
	 */
	private function record_schema_probe_failure( $probe_error ) {
		$failures = (int) get_option( 'ngg_schema_check_failures', 0 ) + 1;

		// A short transient, because the alternative is re-issuing all three probes on every
		// single admin request for as long as the failure lasts -- and a restricted grant,
		// $wpdb->ready === false or a statement-timeout policy is durable, not momentary.
		set_transient( 'ngg_schema_tables_check', 1, 5 * MINUTE_IN_SECONDS );

		// Stored on every failure, capped at the threshold. It used to be written only while
		// below the threshold, which froze it at 2 and made it useless as a trace -- the count
		// never reached the number the report fires on.
		update_option( 'ngg_schema_check_failures', min( $failures, 3 ), false );

		// Recorded once per upgrade cycle, not once per install. A durable failure re-enters this
		// branch every few minutes for as long as it lasts, and re-recording on each would keep
		// overwriting its own notice and re-pinning a banner -- this matters on platforms that do
		// not implement these statements at all, where the SQLite drop-in behind WP Playground
		// (#969) fails every probe rather than intermittently. But the message lands in
		// ngg_upgrade_error, which Installer::update() deletes at the start of every upgrade
		// pass, so the latch has to be cleared there too or the suppression outlives the thing it
		// suppressed and the failure goes silent for good. Installer::update() clears it.
		if ( $failures >= 3 && ! get_option( 'ngg_schema_check_reported' ) ) {
			\Imagely\NGG\Util\Installer::record_upgrade_error(
				__( 'NextGEN Gallery: could not check whether its database tables exist.', 'nggallery' ),
				$probe_error
			);
			update_option( 'ngg_schema_check_reported', 1, false );
		}
	}

	public function define_tables() {
		global $wpdb;

		$wpdb->nggpictures = $wpdb->prefix . 'ngg_pictures';
		$wpdb->nggallery   = $wpdb->prefix . 'ngg_gallery';
		$wpdb->nggalbum    = $wpdb->prefix . 'ngg_album';
	}

	public function define_constant() {
		define(
			'NGG_LEGACY_MOD_DIR',
			implode(
				DIRECTORY_SEPARATOR,
				[
					rtrim( NGG_PLUGIN_DIR, '/\\' ),
					'src',
					basename( __DIR__ ),
				]
			)
		);

		define( 'NGGVERSION', NGG_PLUGIN_VERSION );
		define( 'NGGFOLDER', dirname( NGG_PLUGIN_BASENAME ) );

		define( 'NGGALLERY_ABSPATH', rtrim( NGG_LEGACY_MOD_DIR, '/\\' ) . DIRECTORY_SEPARATOR );
		define( 'NGGALLERY_URLPATH', plugin_dir_url( __FILE__ ) );
	}

	public function load_dependencies() {
		// Load global libraries.
		require_once __DIR__ . '/lib/core.php';
		require_once __DIR__ . '/lib/ngg-db.php';
		require_once __DIR__ . '/lib/image.php';
		require_once __DIR__ . '/lib/tags.php';
		require_once __DIR__ . '/lib/post-thumbnail.php';
		require_once __DIR__ . '/lib/sitemap.php';

		// Load frontend libraries.
		require_once __DIR__ . '/lib/shortcodes.php';

		// We didn't need all stuff during a AJAX operation.
		if ( defined( 'DOING_AJAX' ) ) {
			require_once __DIR__ . '/admin/ajax.php';
		} else {
			require_once __DIR__ . '/lib/meta.php';
			require_once __DIR__ . '/lib/media-rss.php';

			if ( is_admin() && ! $this->is_rest_url() ) {
				require_once __DIR__ . '/admin/admin.php';
				require_once __DIR__ . '/admin/media-upload.php';
				$this->nggAdminPanel = new nggAdminPanel();
			}
		}
	}

	public function is_rest_url(): bool {
		return isset( $_SERVER['REQUEST_URI'] ) && strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'wp-json' ) !== false;
	}

	public function load_options() {
		$this->options = get_option( 'ngg_options' );
	}

	public function multisite_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		global $wpdb;

		include_once __DIR__ . '/admin/install.php';

		if ( is_plugin_active_for_network( NGG_PLUGIN_BASENAME ) ) {
			$current_blog = $wpdb->blogid;
			switch_to_blog( $blog_id );
			$installer = new C_NGG_Legacy_Installer();
			nggallery_install( $installer );
			switch_to_blog( $current_blog );
		}
	}

	public function add_plugin_links( $links, $file ) {
		if ( $file == NGG_PLUGIN_BASENAME ) {
			$links[] = '<a target="_blank" href="https://wordpress.org/support/plugin/nextgen-gallery">' . __( 'Get help', 'nggallery' ) . '</a>';
			foreach ( $links as $key => $link ) {
				if ( false !== strpos( $link, 'Imagely' ) ) {
					$links[ $key ] = str_replace( '<a ', '<a target="_blank" ', $link );
				}
			}
		}

		return $links;
	}
}

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
/**
 * Legacy installer class.
 */
class C_NGG_Legacy_Installer {

	public function install() {
		global $wpdb;
		include_once 'admin/install.php';

		$this->remove_transients();

		if ( is_multisite() ) {
			$network = isset( $_SERVER['SCRIPT_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_NAME'] ) ) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for activation check
			$activate     = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			$isNetwork    = $network == '/wp-admin/network/plugins.php';
			$isActivation = ! ( ( $activate == 'deactivate' ) );

			if ( $isNetwork && $isActivation ) {
				$old_blog = $wpdb->blogid;
				// $wpdb->prepare() cannot be used just yet as it only supported the %i placeholder for column names as of
				// WordPress 6.2 which is newer than NextGEN's current minimum WordPress version.
				//
				// TODO: Once NextGEN's minimum WP version is 6.2 or higher use wpdb->prepare() here.
				//
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					\switch_to_blog( $blog_id );
					\nggallery_install( $this );
				}
				switch_to_blog( $old_blog );
				return;
			}
		}
		// remove the update message.
		delete_option( 'ngg_update_exists' );
		nggallery_install( $this );
	}

	public function uninstall( $hard = false ) {
		include_once 'admin/install.php';
		if ( $hard ) {
			delete_option( 'ngg_init_check' );
			delete_option( 'ngg_schema_error' );
			delete_option( 'ngg_schema_error_tables' );
			delete_option( 'ngg_schema_check_failures' );
			delete_option( 'ngg_schema_check_reported' );
			delete_option( 'ngg_pictures_guard_deferred' );
			delete_option( 'ngg_pictures_dedupe_done' );
			delete_option( 'ngg_pictures_dedupe_attempts' );
			delete_option( 'ngg_upgrade_error' );
			delete_option( 'ngg_update_exists' );
			delete_option( 'ngg_options' );
			delete_option( 'ngg_db_version' );
			delete_option( 'ngg_update_exists' );
			delete_option( 'ngg_next_update' );
			delete_transient( 'ngg_schema_tables_check' );
		}

		// now remove the capability.
		ngg_remove_capability( 'NextGEN Attach Interface' );
		ngg_remove_capability( 'NextGEN Change options' );
		ngg_remove_capability( 'NextGEN Change style' );
		ngg_remove_capability( 'NextGEN Edit album' );
		ngg_remove_capability( 'NextGEN Gallery overview' );
		ngg_remove_capability( 'NextGEN Manage gallery' );
		ngg_remove_capability( 'NextGEN Upload images' );
		ngg_remove_capability( 'NextGEN Use TinyMCE' );
		ngg_remove_capability( 'NextGEN Manage others gallery' );
		ngg_remove_capability( 'NextGEN Manage tags' );

		$this->remove_transients();
	}

	public function remove_transients() {
		global $wpdb, $_wp_using_ext_object_cache;

		// Fetch all transients
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$transient_names = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options}
                    WHERE  option_name LIKE %s",
				[
					'%' . $wpdb->esc_like( 'ngg_request' ) . '%',
				]
			)
		);

		// Delete all transients in the database
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options}
                        WHERE option_name LIKE %s",
				[
					'%' . $wpdb->esc_like( 'ngg_request' ) . '%',
				]
			)
		);

		// If using an external caching mechanism, delete the cached items.
		if ( $_wp_using_ext_object_cache ) {
			foreach ( $transient_names as $transient ) {
				wp_cache_delete( $transient, 'transient' );
				wp_cache_delete( substr( $transient, 11 ), 'transient' );
			}
		}
	}

	public function upgrade_schema( $sql ) {
		global $wpdb;

		// upgrade function changed in WordPress 2.3.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// add charset & collate like wp core.
		$charset_collate = '';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		if ( version_compare( $wpdb->get_var( 'SELECT VERSION() AS `mysql_version`' ), '4.1.0', '>=' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$charset_collate .= " COLLATE $wpdb->collate";
			}
		}

		// Add charset to table creation query.
		$sql = str_replace( $charset_collate, '', str_replace( ';', '', $sql ) );

		// Execute the query.
		return dbDelta( $sql . ' ' . $charset_collate . ';' );
	}
}

global $ngg;
$ngg = new nggLoader();
