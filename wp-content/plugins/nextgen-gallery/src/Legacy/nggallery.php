<?php

class nggLoader {

	public $version = NGG_PLUGIN_VERSION;
	public $options = [];

	public $nggAdminPanel = null;

	public $manage_album;

	/** @var nggManageGallery|nggManageAlbum $manage_page */
	public $manage_page;

	public function __construct() {
		$this->load_options();
		$this->define_constant();
		$this->define_tables();
		$this->load_dependencies();

		// Start this plugin once all other plugins are fully loaded.
		add_action( 'plugins_loaded', [ $this, 'start_plugin' ] );

		// Register_taxonomy must be used during the init.
		add_action( 'init', [ $this, 'register_taxonomy' ], 9 );
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
		} else {
			$settings = \Imagely\NGG\Settings\Settings::get_instance();
			if ( $settings->get( 'useMediaRSS' ) ) {
				add_action( 'wp_head', [ 'nggMediaRss', 'add_mrss_alternate_link' ] );
			}
		}
	}

	public function output_init_check_error() {
		printf( "<div id='message' class='error'><p><strong>%s</strong></p></div>", esc_html( get_option( 'ngg_init_check' ) ) );
	}

	public function define_tables() {
		global $wpdb;

		$wpdb->nggpictures = $wpdb->prefix . 'ngg_pictures';
		$wpdb->nggallery   = $wpdb->prefix . 'ngg_gallery';
		$wpdb->nggalbum    = $wpdb->prefix . 'ngg_album';
	}

	public function register_taxonomy() {
		// Register the NextGEN taxonomy.
		$args = [
			'label'    => __( 'Picture tag', 'nggallery' ),
			'template' => __( 'Picture tag: %2$l.', 'nggallery' ),
			'helps'    => __( 'Separate picture tags with commas.', 'nggallery' ),
			'sort'     => true,
			'args'     => [ 'orderby' => 'term_order' ],
		];

		register_taxonomy( 'ngg_tag', 'nggallery', $args );
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
		return strpos( $_SERVER['REQUEST_URI'], 'wp-json' ) !== false;
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
			$links[] = '<a href="https://wordpress.org/support/plugin/nextgen-gallery">' . __( 'Get help', 'nggallery' ) . '</a>';
			$links[] = '<a href="https://bitbucket.org/photocrati/nextgen-gallery">' . __( 'Contribute', 'nggallery' ) . '</a>';
		}

		return $links;
	}
}

class C_NGG_Legacy_Installer {

	public function install() {
		global $wpdb;
		include_once 'admin/install.php';

		$this->remove_transients();

		if ( is_multisite() ) {
			$network      = isset( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['SCRIPT_NAME'] : '';
			$activate     = isset( $_GET['action'] ) ? $_GET['action'] : '';
			$isNetwork    = $network == '/wp-admin/network/plugins.php';
			$isActivation = ! ( ( $activate == 'deactivate' ) );

			if ( $isNetwork && $isActivation ) {
				$old_blog = $wpdb->blogid;
				// $wpdb->prepare() cannot be used just yet as it only supported the %i placeholder for column names as of
				// WordPress 6.2 which is newer than NextGEN's current minimum WordPress version.
				//
				// TODO: Once NextGEN's minimum WP version is 6.2 or higher use wpdb->prepare() here.
				//
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$blogids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs", null ) );
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
			delete_option( 'ngg_update_exists' );
			delete_option( 'ngg_options' );
			delete_option( 'ngg_db_version' );
			delete_option( 'ngg_update_exists' );
			delete_option( 'ngg_next_update' );
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
