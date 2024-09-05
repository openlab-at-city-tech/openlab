<?php
/**
 * TablePress Modules Loader class with functions for loading premium modules.
 *
 * @package TablePress
 * @subpackage Modules
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * TablePress Modules loader functions.
 *
 * @package TablePress
 * @subpackage Modules
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Modules_Loader {

	/**
	 * TablePress premium modules that could not be loaded, e.g. because a conflicting TablePress Extension is already active.
	 *
	 * @since 2.0.0
	 * @var string[]
	 */
	protected static $failed_modules = array();

	/**
	 * Minimum plan that is needed for the modules initialization and management.
	 *
	 * @since 2.0.3
	 * @const string
	 */
	protected const MINIMUM_MODULES_PLAN = 'pro';

	/**
	 * Inits the TablePress Modules Loader.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		// If the Automatic Periodic Table Import module is active, load the Action Scheduler library.
		if ( 'true' === get_option( 'tablepress_load_action_scheduler', 'false' ) ) {
			require_once TABLEPRESS_ABSPATH . 'modules/libraries/action-scheduler/action-scheduler.php';
		}

		add_action( 'tablepress_run', array( __CLASS__, 'init_modules' ) );
		add_filter( 'tablepress_exit_early', array( __CLASS__, 'load_early_modules' ) );
		add_action( 'tablepress_loaded', array( __CLASS__, 'load_modules' ) );
	}

	/**
	 * Loads the TablePress Premium language file.
	 *
	 * Try loading the Premium translations file for the current locale.
	 * If one doesn't exist, WordPress will fall back to the free version's file.
	 *
	 * @since 2.2.4
	 */
	public static function load_language_file(): void {
		// Prevent repeated execution via a static variable, as loading the translation file once is enough.
		static $translation_file_loaded = false;
		if ( $translation_file_loaded ) {
			return;
		}

		/** This filter is documented in the WordPress file wp-includes/l10n.php */
		$locale = apply_filters( 'plugin_locale', determine_locale(), 'tablepress' );
		$premium_mofile = TABLEPRESS_ABSPATH . 'modules/i18n/' . "tablepress-{$locale}.mo";
		load_textdomain( 'tablepress', $premium_mofile );

		// Prevent repeated execution via a static variable, as loading the translation file once is enough.
		$translation_file_loaded = true;
	}

	/**
	 * Initializes the Modules integration.
	 *
	 * @since 2.0.0
	 */
	public static function init_modules(): void {
		if ( is_admin() ) {
			self::load_language_file();
		}

		// Load the module trait, so that it's available for all module classes.
		TablePress::load_file( 'trait-module.php', 'modules/classes' );

		TablePress::init_modules();
	}

	/**
	 * Filters whether TablePress should exit early.
	 *
	 * The Advanced Access Rights module, if active, will be loaded during all requests, to prevent capability check issues.
	 * The Automatic Periodic Table Import module, if active, will be loaded during cron requests, so that the import can run.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $exit_early Whether TablePress should exit early.
	 * @return bool Whether TablePress should exit early.
	 */
	public static function load_early_modules( bool $exit_early ): bool {
		// Let TablePress continue if it should not exit.
		if ( false === $exit_early ) {
			return $exit_early;
		}

		// Let TablePress continue if the modules have not been initialized yet or if no modules are loaded.
		$active_modules = TablePress::$model_options->get( 'modules', false );
		if ( empty( $active_modules ) ) {
			return $exit_early;
		}

		$active_modules = explode( ',', $active_modules );

		// If activated, load the Advanced Access Rights module if TablePress should exit early during any request.
		if (
			in_array( 'advanced-access-rights', $active_modules, true )
			&& tb_tp_fs()->is_plan_or_trial( TablePress::$modules['advanced-access-rights']['minimum_plan'] )
		) {
			TablePress::load_class( TablePress::$modules['advanced-access-rights']['class'], 'advanced-access-rights.php', 'modules/controllers' );
		}

		// If activated, load the Automatic Periodic Table Import module if TablePress should exit early during a cron request.
		if (
			in_array( 'automatic-periodic-table-import', $active_modules, true )
			&& tb_tp_fs()->is_plan_or_trial( TablePress::$modules['automatic-periodic-table-import']['minimum_plan'] )
			&& wp_doing_cron()
		) {
			TablePress::load_class( TablePress::$modules['automatic-periodic-table-import']['class'], 'automatic-periodic-table-import.php', 'modules/controllers' );

			// If activated, also load the Automatic Table Export module, so that import events triggered by the Automatic Periodic Table Import module can be captured and acted upon.
			if (
				in_array( 'automatic-table-export', $active_modules, true )
				// Checking tb_tp_fs()->is_plan_or_trial() is not necessary here, as the Automatic Table Export module is in a lower plan than the Automatic Periodic Table Import module.
			) {
				TablePress::load_class( TablePress::$modules['automatic-table-export']['class'], 'automatic-table-export.php', 'modules/controllers' );
			}
		}

		// Due to the `false` check above, `$exit_early` is `true` here, which means that TablePress will bail early now, before loading a controller.
		return $exit_early;
	}

	/**
	 * Initializes the TablePress plugin option with the default active modules of the first activated premium plan.
	 *
	 * @since 2.0.3
	 *
	 * @return string Comma-separated list of slugs of active premium modules.
	 */
	public static function init_active_modules_plugin_option(): string {
		$active_modules = array_filter(
			TablePress::$modules,
			static function ( array $module ): bool {
				return $module['default_active'] && tb_tp_fs()->is_plan_or_trial( $module['minimum_plan'] );
			}
		);
		$active_modules = array_keys( $active_modules );
		$active_modules = implode( ',', $active_modules );

		TablePress::$model_options->update( 'modules', $active_modules );

		return $active_modules;
	}

	/**
	 * Loads and runs the available premium modules.
	 *
	 * @since 2.0.0
	 */
	public static function load_modules(): void {
		if ( is_admin() ) {
			self::init_admin();
		}

		// Initialize the active modules list on the first run after the activation of a license.
		$initialize_table_options = false;
		$active_modules = TablePress::$model_options->get( 'modules', false );
		if ( false === $active_modules ) {
			if ( tb_tp_fs()->is_plan_or_trial( self::MINIMUM_MODULES_PLAN ) ) {
				$active_modules = self::init_active_modules_plugin_option();
				$initialize_table_options = true;
			} else {
				return; // Bail and don't load any modules if there's no active license.
			}
		}

		// Load active modules.
		$active_modules = explode( ',', $active_modules );
		foreach ( $active_modules as $module ) {
			// Don't try to load a module that (no longer?) exists.
			if ( ! isset( TablePress::$modules[ $module ] ) ) {
				continue;
			}

			// Don't load modules that belong to a higher plan.
			if ( ! tb_tp_fs()->is_plan_or_trial( TablePress::$modules[ $module ]['minimum_plan'] ) ) {
				continue;
			}

			// Don't load a module if an incompatible class exists, e.g. from a TablePress Extension.
			foreach ( TablePress::$modules[ $module ]['incompatible_classes'] as $incompatible_class ) {
				if ( class_exists( $incompatible_class, false ) ) {
					self::$failed_modules[] = TablePress::$modules[ $module ]['name'];
					continue 2;
				}
			}

			$module_class = TablePress::$modules[ $module ]['class']; // Use a simple variable to be able to use it in a variable variable below.
			TablePress::load_class( $module_class, "{$module}.php", 'modules/controllers' );

			// Add module's properties to the module class, so that the information is available inside the module, e.g. for help box content.
			$module_class::$module = TablePress::$modules[ $module ];
			$module_class::$module['slug'] = $module;
		}

		/*
		 * Trigger update of the table options as new modules might have been activated and thereby modified the table template.
		 * Activated modules add new table options to the table template. These need to be added to existing tables as well. Old options are not removed, to prevent data loss when a module is only deactivated temporarily.
		 */
		$refresh_table_options = ( ! empty( $_GET['refresh_table_options'] ) && 'true' === $_GET['refresh_table_options'] ); // The GET parameter is set by `handle_post_action_modules()` and in `TablePress_Controller::plugin_update_check()`.
		if ( $initialize_table_options || $refresh_table_options ) {
			// Stop loading the Action Scheduler library and unschedule the recurring actions when the "Automatic Periodic Table Import" module is deactivated.
			$automatic_periodic_table_import_active = tb_tp_fs()->is_plan_or_trial( TablePress::$modules['automatic-periodic-table-import']['minimum_plan'] ) && in_array( 'automatic-periodic-table-import', $active_modules, true );
			if ( ! $automatic_periodic_table_import_active ) {
				update_option( 'tablepress_load_action_scheduler', 'false', true );
				if ( function_exists( 'as_unschedule_all_actions' ) ) {
					as_unschedule_all_actions( 'tablepress_automatic_periodic_table_import_action' );
				}
			}

			TablePress::$model_table->merge_table_options_defaults( false );
		}

		if ( count( self::$failed_modules ) > 0 && current_user_can( 'update_plugins' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'show_failed_modules_error_notice' ) );
		}
	}

	/**
	 * Show an error notice to admins, if a TablePress module class already exists, maybe from an already active TablePress Extension.
	 *
	 * @since 2.0.0
	 */
	public static function show_failed_modules_error_notice(): void {
		$failed_modules = '<ul class="ul-disc">';
		foreach ( self::$failed_modules as $failed_module ) {
			$failed_modules .= "<li>{$failed_module}</li>";
		}
		$failed_modules .= '</ul>';
		?>
		<div class="notice notice-error notice-alt notice-large">
			<h3><em>
				<span aria-hidden="true" class="dashicons dashicons-warning" style="color:#d63638;vertical-align:bottom"></span>
				<?php _e( 'Attention: Unfortunately, there is a problem!', 'tablepress' ); ?>
			</em></h3>
			<p style="font-size:14px">
				<?php _e( 'Not all TablePress premium modules could be loaded, because a conflicting TablePress Extension is already active:', 'tablepress' ); ?>
			</p>
			<?php echo $failed_modules; ?>
			<p style="font-size:14px">
				<strong><?php printf( __( 'Please deactivate corresponding TablePress Extensions on the <a href="%s">WordPress “Plugins” page</a> to use these modules.', 'tablepress' ), esc_url( admin_url( 'plugins.php' ) ) ); ?></strong>
			</p>
		</div>
		<?php
	}

	/**
	 * Initializes the admin screens of the Modules management.
	 *
	 * @since 2.0.0
	 */
	public static function init_admin(): void {
		TablePress::load_class( 'TablePress_Modules_Helper', 'class-modules-helper.php', 'modules/classes' );

		add_filter( 'tablepress_view_data', array( __CLASS__, 'enqueue_common_modules_css' ), 10, 2 );

		// Only allow access to the Modules screen for Admins.
		if ( current_user_can( 'manage_options' ) ) {
			add_filter( 'tablepress_load_file_full_path', array( __CLASS__, 'change_modules_view_full_path' ), 10, 3 );
			add_filter( 'tablepress_admin_view_actions', array( __CLASS__, 'add_view_action_modules' ) );
			add_filter( 'tablepress_view_data', array( __CLASS__, 'add_modules_view_data' ), 10, 2 );
			add_action( 'admin_post_tablepress_modules', array( __CLASS__, 'handle_post_action_modules' ) );
		}
	}

	/**
	 * Adjusts the path from which the Modules view class file is loaded.
	 *
	 * @since 2.0.0
	 *
	 * @param string $full_path Full path of the class file.
	 * @param string $file      File name of the class file.
	 * @param string $folder    Folder name of the class file.
	 * @return string Modified full path.
	 */
	public static function change_modules_view_full_path( string $full_path, string $file, string $folder ): string {
		if ( 'view-modules.php' === $file ) {
			$full_path = TABLEPRESS_ABSPATH . "modules/views/{$file}";
		}
		return $full_path;
	}

	/**
	 * Adds the Modules view to the list of views in TablePress.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, array<string, bool|string>> $view_actions List of views.
	 * @return array<string, array<string, bool|string>> Modified list of views.
	 */
	public static function add_view_action_modules( array $view_actions ): array {
		$view_modules = array(
			'modules' => array(
				'show_entry'       => true,
				'page_title'       => __( 'Modules', 'tablepress' ),
				'admin_menu_title' => __( 'Modules', 'tablepress' ),
				'nav_tab_title'    => __( 'Modules', 'tablepress' ),
				'required_cap'     => 'manage_options', // Only grant access to the "Modules" screen for admins.
			),
		);
		// Insert Modules view before the About view.
		return array_slice( $view_actions, 0, -1, true ) + $view_modules + array_slice( $view_actions, -1, null, true );
	}

	/**
	 * Adds the view data for the Modules view.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data   View data.
	 * @param string               $action The current action.
	 * @return array<string, mixed> The extended view data.
	 */
	public static function add_modules_view_data( array $data, string $action ): array {
		if ( 'modules' !== $action ) {
			return $data;
		}

		$data['categories'] = array(
			'frontend'        => __( 'Frontend and Styling', 'tablepress' ),
			'search-filter'   => __( 'Search and Filter', 'tablepress' ),
			'data-management' => __( 'Table Data Management', 'tablepress' ),
			'backend'         => __( 'Backend and Admin', 'tablepress' ),
		);

		// Group modules by category.
		$data['available_modules'] = array();
		foreach ( TablePress::$modules as $slug => $module ) {
			if ( ! isset( $data['available_modules'][ $module['category'] ] ) ) {
				$data['available_modules'][ $module['category'] ] = array();
			}
			$data['available_modules'][ $module['category'] ][ $slug ] = $module;
		}

		$data['active_modules'] = TablePress::$model_options->get( 'modules', '' );
		$data['active_modules'] = explode( ',', $data['active_modules'] );

		$data['minimum_modules_plan'] = self::MINIMUM_MODULES_PLAN;

		return $data;
	}

	/**
	 * Saves changes on the "Modules" screen.
	 *
	 * @since 2.0.0
	 */
	public static function handle_post_action_modules(): void {
		TablePress::check_nonce( 'modules' );

		// Don't save Premium modules if no sufficient plan is active.
		if ( ! tb_tp_fs()->is_plan_or_trial( self::MINIMUM_MODULES_PLAN ) ) {
			TablePress::redirect( array( 'action' => 'modules', 'message' => 'error_save' ) );
		}

		// Check that the `_http-test` value exists, as that field value is part of the HTTP request even when no modules are activated.
		if ( empty( $_POST['modules'] ) || ! is_array( $_POST['modules'] ) || ! in_array( '_http-test', $_POST['modules'], true ) ) {
			TablePress::redirect( array( 'action' => 'modules', 'message' => 'error_save' ) );
		}

		$modules = stripslashes_deep( $_POST['modules'] );

		// Filter out the `_http-test` value and any non-existent modules.
		$modules = array_filter(
			$modules,
			static function ( string $module ): bool {
				return isset( TablePress::$modules[ $module ] );
			}
		);
		$modules = implode( ',', $modules );
		TablePress::$model_options->update( 'modules', $modules );

		TablePress::redirect( array( 'action' => 'modules', 'message' => 'success_save', 'refresh_table_options' => 'true' ) );
	}

	/**
	 * Enqueues the common modules CSS on the  "Edit" screen.
	 *
	 * @since 2.4.0
	 *
	 * @param array<string, mixed> $data   Data for this screen.
	 * @param string               $action Action for this screen.
	 * @return array<string, mixed> Modified data for this screen.
	 */
	public static function enqueue_common_modules_css( array $data, string $action ): array {
		if ( 'edit' === $action ) {
			TablePress_Modules_Helper::enqueue_style( 'modules-common' );
		}
		return $data;
	}

} // class TablePress_Modules_Loader
