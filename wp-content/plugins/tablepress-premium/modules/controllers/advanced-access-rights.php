<?php
/**
 * TablePress Advanced Access Rights.
 *
 * @package TablePress
 * @subpackage Advanced Access Rights
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the Advanced Access Rights feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_Advanced_Access_Rights {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Map with the users' access rights to the tables.
	 *
	 * @since 2.0.0
	 * @var array<string, array<int|string, 0|1>>
	 */
	protected $access_rights_map = array();

	/**
	 * Instance of the TablePress_WP_Option class.
	 *
	 * @since 2.0.0
	 * @var TablePress_WP_Option
	 */
	protected $option;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->init_advanced_access_rights();

		if ( is_admin() ) {
			$this->init_admin();
		}
	}

	/**
	 * Inits the Advanced Access Rights feature.
	 *
	 * @since 2.0.0
	 */
	public function init_advanced_access_rights(): void {
		$params = array(
			'option_name'   => 'tablepress_access_rights_map',
			'default_value' => array(),
		);
		$this->option = TablePress::load_class( 'TablePress_WP_Option', 'class-wp_option.php', 'classes', $params );
		$this->access_rights_map = $this->option->get();
		if ( empty( $this->access_rights_map ) ) {
			$this->access_rights_map = $this->create_access_rights_map();
			$this->option->update( $this->access_rights_map );
		}

		// React to modified user or table list, and adjust access rights map.
		add_action( 'user_register', array( $this, 'user_register_handler' ) );
		add_action( 'deleted_user', array( $this, 'deleted_user_handler' ) );
		add_action( 'tablepress_event_added_table', array( $this, 'added_table_handler' ) );
		add_action( 'tablepress_event_copied_table', array( $this, 'copied_table_handler' ), 10, 2 );
		add_action( 'tablepress_event_deleted_table', array( $this, 'deleted_table_handler' ) );
		add_action( 'tablepress_event_changed_table_id', array( $this, 'changed_table_id_handler' ), 10, 2 );

		// Take access rights map into account during capability checks.
		add_filter( 'tablepress_map_meta_caps', array( $this, 'check_access_rights' ), 10, 4 );
		/**
		 * Filters whether tables which a user can not access should be hidden in lists of tables.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $hide Whether prohibited tables should be hidden. Default false.
		 */
		if ( apply_filters( 'tablepress_advanced_access_rights_hide_prohibited_tables', true ) ) {
			add_filter( 'tablepress_load_all_tables', array( $this, 'hide_prohibited_tables' ) );
		}
	}

	/**
	 * Creates the initial access rights maps, based on existing tables and users.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string, array<int|string, 0|1>> Map with the users' access rights to the tables.
	 */
	protected function create_access_rights_map(): array {
		$table_ids = TablePress::$model_table->load_all( false, false ); // Don't prime the post meta cache, don't run filter.
		$users = wp_list_pluck( get_users(), 'data' );

		$access_rights_map = array();
		foreach ( $table_ids as $table_id ) {
			foreach ( $users as $user ) {
				$access_rights_map[ $table_id ][ $user->ID ] = 1;
			}
			$access_rights_map[ $table_id ]['#new_users'] = 0;
		}
		foreach ( $users as $user ) {
			$access_rights_map['#new_tables'][ $user->ID ] = 1;
		}
		$access_rights_map['#new_tables']['#new_users'] = 0;

		return $access_rights_map;
	}

	/**
	 * Merges an access rights map into another.
	 *
	 * This is used to make sure that a complete access rights map is used when saving,
	 * including tables/users that might have been created white the module was inactive.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, array<int|string, 0|1>> $full_map  Access rights map with all tables/users.
	 * @param array<string, array<int|string, 0|1>> $merge_map Access rights map that is to be merged into the full map.
	 * @return array<string, array<int|string, 0|1>> Merged access rights map.
	 */
	protected function merge_access_rights_maps( array $full_map, array $merge_map ): array {
		foreach ( $full_map as $table_id => $users ) {
			$table_id = (string) $table_id; // Ensure that the table ID is a string, as it comes from an array key where numeric strings are converted to integers.

			foreach ( $users as $user_id => $user_has_rights ) {
				if ( isset( $merge_map[ $table_id ][ $user_id ] ) ) {
					$full_map[ $table_id ][ $user_id ] = $merge_map[ $table_id ][ $user_id ];
				} elseif ( isset( $merge_map[ $table_id ]['#new_users'] ) ) {
					$full_map[ $table_id ][ $user_id ] = $merge_map[ $table_id ]['#new_users'];
				} elseif ( isset( $merge_map['#new_tables'][ $user_id ] ) ) {
					$full_map[ $table_id ][ $user_id ] = $merge_map['#new_tables'][ $user_id ];
				} elseif ( isset( $merge_map['#new_tables']['#new_users'] ) ) {
					$full_map[ $table_id ][ $user_id ] = $merge_map['#new_tables']['#new_users'];
				} else {
					$full_map[ $table_id ][ $user_id ] = 0;
				}
			}
		}

		return $full_map;
	}

	/**
	 * Takes access rights map into account during capability checks.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $caps    Current set of primitive caps.
	 * @param string   $cap     Meta cap that is to be checked/mapped.
	 * @param int      $user_id User ID for which meta cap is to be checked.
	 * @param mixed[]  $args    Arguments for the check, here e.g. the table ID.
	 * @return string[] Modified set of primitive caps.
	 */
	public function check_access_rights( array $caps, string $cap, int $user_id, array $args ): array {
		$prohibited_caps = array(
			'tablepress_edit_table',
			'tablepress_delete_table',
			'tablepress_copy_table',
			'tablepress_preview_table',
			'tablepress_export_table',
		);

		/**
		 * Filters the capabilities that are prohibited via the Advanced Access Rights user interface.
		 *
		 * @since 2.1.0
		 *
		 * @param string[] $prohibited_caps Array if prohibited capabilities.
		 * @param string   $cap             Meta cap that is to be checked/mapped.
		 * @param int      $user_id         User ID for which meta cap is to be checked.
		 * @param mixed[]  $args            Arguments for the check, here e.g. the table ID.
		 */
		$prohibited_caps = apply_filters( 'tablepress_advanced_access_rights_prohibited_caps', $prohibited_caps, $cap, $user_id, $args );

		if ( ! in_array( $cap, $prohibited_caps, true ) ) {
			return $caps;
		}

		if ( empty( $args ) || ! is_array( $args ) || ! isset( $args[0] ) ) {
			$caps[] = 'do_not_allow';
			return $caps;
		}

		$table_id = $args[0];
		if ( ! isset( $this->access_rights_map[ $table_id ][ $user_id ] ) || 1 !== $this->access_rights_map[ $table_id ][ $user_id ] ) {
			$caps[] = 'do_not_allow';
		}

		return $caps;
	}

	/**
	 * Removes tables that are not allowed from the list of loaded tables.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $table_ids List of loaded table IDs.
	 * @return string[] New list of table IDs.
	 */
	public function hide_prohibited_tables( array $table_ids ): array {
		foreach ( $table_ids as $key => $table_id ) {
			if ( ! current_user_can( 'tablepress_edit_table', $table_id ) ) {
				unset( $table_ids[ $key ] );
			}
		}
		return array_merge( $table_ids );
	}

	/**
	 * Extends access rights map with a new user.
	 *
	 * @since 2.0.0
	 *
	 * @param int $user_id ID of the added user.
	 */
	public function user_register_handler( int $user_id ): void {
		foreach ( $this->access_rights_map as $table_id => $dummy ) {
			$table_id = (string) $table_id; // Ensure that the table ID is a string, as it comes from an array key where numeric strings are converted to integers.
			$this->access_rights_map[ $table_id ][ $user_id ] = $this->access_rights_map[ $table_id ]['#new_users'];
			ksort( $this->access_rights_map[ $table_id ] );
			$new_users = $this->access_rights_map[ $table_id ]['#new_users'];
			unset( $this->access_rights_map[ $table_id ]['#new_users'] );
			$this->access_rights_map[ $table_id ]['#new_users'] = $new_users;
		}
		$this->option->update( $this->access_rights_map );
	}

	/**
	 * Removes an entry from access rights map after a user is deleted.
	 *
	 * @since 2.0.0
	 *
	 * @param int $user_id ID of the deleted user.
	 */
	public function deleted_user_handler( int $user_id ): void {
		foreach ( $this->access_rights_map as $table_id => $dummy ) {
			$table_id = (string) $table_id; // Ensure that the table ID is a string, as it comes from an array key where numeric strings are converted to integers.
			unset( $this->access_rights_map[ $table_id ][ $user_id ] );
		}
		$this->option->update( $this->access_rights_map );
	}

	/**
	 * Extends access rights map with a new table.
	 *
	 * @since 2.0.0
	 *
	 * @param string $table_id ID of the added table.
	 */
	public function added_table_handler( string $table_id ): void {
		$this->access_rights_map[ $table_id ] = $this->access_rights_map['#new_tables'];
		$this->access_rights_map[ $table_id ][ get_current_user_id() ] = 1;

		uksort( $this->access_rights_map, 'strnatcasecmp' );
		$new_tables = reset( $this->access_rights_map );
		unset( $this->access_rights_map['#new_tables'] );
		$this->access_rights_map['#new_tables'] = $new_tables;
		$this->option->update( $this->access_rights_map );
	}

	/**
	 * Extends access rights map with a copied table.
	 *
	 * @since 2.0.0
	 *
	 * @param string $table_id        ID of the new table.
	 * @param string $copied_table_id ID of the copied table.
	 */
	public function copied_table_handler( string $table_id, string $copied_table_id ): void {
		// $this->access_rights_map[ $table_id ] = $this->access_rights_map[ $copied_table_id ]; // Use same rights as original table.
		$this->access_rights_map[ $table_id ] = $this->access_rights_map['#new_tables'];
		$this->access_rights_map[ $table_id ][ get_current_user_id() ] = 1;

		uksort( $this->access_rights_map, 'strnatcasecmp' );
		$new_tables = reset( $this->access_rights_map );
		unset( $this->access_rights_map['#new_tables'] );
		$this->access_rights_map['#new_tables'] = $new_tables;
		$this->option->update( $this->access_rights_map );
	}

	/**
	 * Removes an entry from access rights map after a table is deleted.
	 *
	 * @since 2.0.0
	 *
	 * @param string $table_id ID of the deleted table.
	 */
	public function deleted_table_handler( string $table_id ): void {
		unset( $this->access_rights_map[ $table_id ] );
		$this->option->update( $this->access_rights_map );
	}

	/**
	 * Adjusts access rights map when a table's ID is changed.
	 *
	 * @since 2.0.0
	 *
	 * @param string $new_id New ID of the table.
	 * @param string $old_id Old ID of the table.
	 */
	public function changed_table_id_handler( string $new_id, string $old_id ): void {
		$this->access_rights_map[ $new_id ] = $this->access_rights_map[ $old_id ];
		unset( $this->access_rights_map[ $old_id ] );
		uksort( $this->access_rights_map, 'strnatcasecmp' );

		// Move the "#new_tables" entry to the end of the array.
		$new_tables = $this->access_rights_map['#new_tables'];
		unset( $this->access_rights_map['#new_tables'] );
		$this->access_rights_map['#new_tables'] = $new_tables;

		$this->option->update( $this->access_rights_map );
	}

	/**
	 * Initializes the admin screens of the Advanced Access Rights module.
	 *
	 * @since 2.0.0
	 */
	public function init_admin(): void {
		// Only allow access to the Advanced Access Rights screen for Admins.
		if ( current_user_can( 'manage_options' ) ) {
			add_filter( 'tablepress_load_file_full_path', array( $this, 'change_advanced_access_rights_view_full_path' ), 10, 3 );
			add_filter( 'tablepress_admin_view_actions', array( $this, 'add_view_action_advanced_access_rights' ) );
			add_filter( 'tablepress_view_data', array( $this, 'add_advanced_access_rights_view_data' ), 10, 2 );
			add_action( 'wp_ajax_tablepress_advanced_access_rights', array( $this, 'handle_ajax_action_advanced_access_rights' ) );
		}
	}

	/**
	 * Adjusts the path from which the Advanced Access Rights class file is loaded.
	 *
	 * @since 2.0.0
	 *
	 * @param string $full_path Full path of the class file.
	 * @param string $file      File name of the class file.
	 * @param string $folder    Folder name of the class file.
	 * @return string Modified full path.
	 */
	public function change_advanced_access_rights_view_full_path( string $full_path, string $file, string $folder ): string {
		if ( 'view-advanced_access_rights.php' === $file ) {
			$full_path = TABLEPRESS_ABSPATH . "modules/views/{$file}";
		}
		return $full_path;
	}

	/**
	 * Add the Advanced Access Rights view to the list of views in TablePress.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, array<string, bool|string>> $view_actions List of views.
	 * @return array<string, array<string, bool|string>> Modified list of views.
	 */
	public function add_view_action_advanced_access_rights( array $view_actions ): array {
		$view_advanced_access_rights = array(
			'advanced_access_rights' => array(
				'show_entry'       => true,
				'page_title'       => __( 'Advanced Access Rights', 'tablepress' ),
				'admin_menu_title' => __( 'Access Rights', 'tablepress' ),
				'nav_tab_title'    => __( 'Access Rights', 'tablepress' ),
				'required_cap'     => 'manage_options', // Only grant access to the Advanced Access Rights area for admins.
			),
		);
		// Insert Advanced Access Rights view before the About view.
		return array_slice( $view_actions, 0, -1, true ) + $view_advanced_access_rights + array_slice( $view_actions, -1, null, true );
	}

	/**
	 * Adds the view data for the Advanced Access Rights view.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data   Data for this screen.
	 * @param string               $action Action for this screen.
	 * @return array<string, mixed> Modified data for this screen.
	 */
	public function add_advanced_access_rights_view_data( array $data, string $action ): array {
		if ( 'advanced_access_rights' !== $action ) {
			return $data;
		}

		$default_access_rights_map = $this->create_access_rights_map();
		$access_rights_map = $this->merge_access_rights_maps( $default_access_rights_map, $this->access_rights_map );
		$data['access_rights_map'] = $access_rights_map;

		$table_ids = TablePress::$model_table->load_all( false, false ); // Don't prime the post meta cache, don't run filter.
		$data['tables'] = array();
		foreach ( $table_ids as $table_id ) {
			// Load table, without table data, options, and visibility settings.
			$table = TablePress::$model_table->load( $table_id, false, false );

			if ( is_wp_error( $table ) ) {
				continue;
			}

			$data['tables'][ $table['id'] ] = $table['name'];
		}

		$users = get_users();
		$data['users'] = array();
		foreach ( $users as $user ) {
			$data['users'][ $user->ID ] = array(
				'userLogin'   => $user->data->user_login,
				'displayName' => $user->data->display_name,
			);
		}

		return $data;
	}

	/**
	 * Saves changes on the "Advanced Access Rights" screen.
	 *
	 * @since 2.0.0
	 */
	public function handle_ajax_action_advanced_access_rights(): void {
		if ( empty( $_POST['tablepress'] ) ) {
			wp_die( '-1' );
		}

		// Check if the submitted nonce matches the generated nonce we created earlier, dies -1 on failure.
		TablePress::check_nonce( 'advanced_access_rights', false, '_ajax_nonce', true );

		// Ignore the request if the current user doesn't have sufficient permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( '-1' );
		}

		$advanced_access_rights = wp_unslash( $_POST['tablepress'] );
		$advanced_access_rights = json_decode( $advanced_access_rights, true );

		// Check if JSON could be decoded.
		if ( is_null( $advanced_access_rights ) ) {
			wp_die( '-1' );
		}

		// Specifically cast to an array again.
		$advanced_access_rights = (array) $advanced_access_rights;

		$default_access_rights_map = $this->create_access_rights_map();
		$this->access_rights_map = $this->merge_access_rights_maps( $default_access_rights_map, $this->access_rights_map );

		foreach ( $this->access_rights_map as $table_id => $users ) {
			$table_id = (string) $table_id; // Ensure that the table ID is a string, as it comes from an array key where numeric strings are converted to integers.

			foreach ( $users as $user_id => $user_has_rights ) {
				$user_has_rights = ( isset( $advanced_access_rights[ $table_id ][ $user_id ] ) && 1 === $advanced_access_rights[ $table_id ][ $user_id ] );
				$this->access_rights_map[ $table_id ][ $user_id ] = $user_has_rights ? 1 : 0;
			}
		}
		$this->option->update( $this->access_rights_map );

		$response = array(
			'success' => true,
			'message' => 'success_save',
		);
		// Buffer all outputs, to prevent errors/warnings being printed that make the JSON invalid.
		$output_buffer = ob_get_clean();
		if ( ! empty( $output_buffer ) ) {
			$response['output_buffer'] = $output_buffer;
		}

		// Send the response.
		wp_send_json( $response );
	}

} // class TablePress_Module_Advanced_Access_Rights
