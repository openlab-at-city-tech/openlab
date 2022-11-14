<?php

class GP_Perk {

	public $tooltips = array();

	/**
	* When HTML elements are generated for the settings page, they are saved here and auto saved.
	*
	* @see generate_enable_checkbox()
	*
	*/
	public $setting_ids;

	public $basename;
	public $data;
	/**
	* @var GP_Perk|null
	*/
	public $parent;

	/**
	* A safe "slug" for use in option names, html IDs, etc...
	*
	* @var mixed
	*/
	protected $slug;

	function __construct( $perk_file = null, $product_id = null ) {

		if ( ! class_exists( 'GWPerks' ) ) {
			return;
		}

		if ( ! $perk_file && empty( $this->basename ) ) {
			_doing_it_wrong( __CLASS__ . ':' . __METHOD__, 'Oops! You\'re instantiating this perk to early.', '1.2.21' );
			return;
		}

		// phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		$args = func_get_args();

		$this->basename   = $perk_file;
		$this->slug       = strtolower( basename( $perk_file, '.php' ) );
		$this->product_id = $product_id;
		$this->parent     = isset( $args[1] ) ? $args[1] : null;

	}

	public function __call( $method, $arguments ) {

		if ( ! method_exists( $this, $method ) && $this->parent && method_exists( $this->parent, $method ) ) {
			return call_user_func_array( array( $this->parent, $method ), $arguments );
		}

		// Whitelisted methods.
		if ( in_array( $method, array( 'tooltips' ) ) ) {
			return null;
		}

		trigger_error( sprintf( 'Call to undefined method %s::%s()', get_class( $this ), $method ), E_USER_ERROR );

		return null;
	}

	function init() {

		$this->maybe_setup();

		add_filter( 'gform_tooltips', array( $this, 'get_tooltips' ) );

	}

	/**
	* Get a Perk object by file.
	*
	* @param string $perk_file
	* @return GWPerk|WP_Error
	*/
	public static function get_perk( $perk_file ) {

		$perk_class = str_replace( '-', '_', basename( $perk_file, '.php' ) );

		if ( ! class_exists( $perk_class ) ) {

			$perk_path = WP_PLUGIN_DIR . '/' . $perk_file;
			if ( ! file_exists( $perk_path ) ) {
				return new WP_Error( 'perk_file_error', __( 'The file for this perk does not exist.', 'gravityperks' ) );
			}

			include_once( $perk_path );
			if ( ! class_exists( $perk_class ) ) {

				$perk_bits     = explode( '/', $perk_file );
				$alt_perk_file = sprintf( '%s/%s/class-%s', WP_PLUGIN_DIR, $perk_bits[0], $perk_bits[1] );

				if ( file_exists( $alt_perk_file ) ) {
					include_once( $alt_perk_file );
				}

				if ( ! class_exists( $perk_class ) ) {
					$perk_data = self::get_perk_data( $perk_file );
					if ( ! empty( $perk_data ) ) {
						$filename      = strtolower( str_replace( ' ', '-', $perk_data['Name'] ) );
						$alt_perk_file = sprintf( '%s/%s/class-%s.php', WP_PLUGIN_DIR, $perk_bits[0], $filename );
						if ( file_exists( $alt_perk_file ) ) {
							include_once( $alt_perk_file );
						}
					}
				}

				if ( ! class_exists( $perk_class ) ) {
					return new WP_Error( 'perk_class_error', __( 'There is no class for this perk.', 'gravityperks' ) );
				}
			}
		}

		if ( is_callable( array( $perk_class, 'get_instance' ) ) ) {
			$perk = call_user_func( array( $perk_class, 'get_instance' ), $perk_file );
		} else {
			$perk = new $perk_class( $perk_file );
		}

		if ( ! is_a( $perk, 'GP_Perk' ) ) {
			$perk = $perk->perk;
		}

		return $perk;
	}

	public function get_property( $property ) {
		return isset( $this->$property ) ? $this->$property : false;
	}

	public function get_id() {
		return $this->get_property( 'id' );
	}

	public function is_old_school() {
		return $this->parent === null;
	}

	public function has_method( $method ) {

		$args = func_get_args();

		if ( $this->is_old_school() ) {
			return $this->method_is_overridden( $method );
		} else {
			$method = isset( $args[1] ) ? $args[1] : $method;
			return method_exists( $this->parent, $method );
		}

	}

	public final function method_is_overridden( $method_name, $base_class = 'GP_Perk' ) {

		$reflector = new ReflectionMethod( $this, $method_name );
		$name      = $reflector->getDeclaringClass()->getName();

		return $name !== $base_class;
	}

	/**
	* Check if the minimum version of Gravity Perks is installed for this perk.
	*
	*/
	public function is_gravity_perks_supported() {

		if ( isset( $this->min_gravity_perks_version ) ) {
			$is_supported = version_compare( GWPerks::get_version(), $this->min_gravity_perks_version, '>=' );
		} else {
			$is_supported = true;
		}

		return $is_supported;
	}

	/**
	* Check if the minimum version of Gravity Forms is installed for this perk.
	*
	*/
	public function is_gravity_forms_supported() {
		return isset( $this->min_gravity_forms_version ) ? GWPerks::is_gravity_forms_supported( $this->min_gravity_forms_version ) : true;
	}

	/**
	* Check if the minimum version of WordPress is installed for this perk.
	*
	*/
	public function is_wp_supported() {
		return isset( $this->min_wp_version ) ? GWPerks::is_wp_supported( $this->min_wp_version ) : true;
	}

	/**
	* Check for minimum version of plugin.
	*
	* @param  array  $args Requirement options including 'class', 'property', 'method', and 'version'
	* @return boolean       Return true if minimum version of required plugin is installed. If no minimum version is specified, return true if required plugin class exists.
	*/
	public function has_min_version( $args ) {

		/**
		* @var $class
		* @var $property
		* @var $method
		* @var $version
		*/
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( wp_parse_args( $args, array(
			'class'    => false,
			'property' => false,
			'method'   => false,
			'version'  => false,
		) ) );

		if ( ! $class || ! class_exists( $class ) ) {
			return false;
		}

		if ( $property ) {
			$class_properties = get_class_vars( $class );
			if ( isset( $class_properties[ $property ] ) && version_compare( $class_properties[ $property ], $version, '<' ) ) {
				return false;
			}
		}

		if ( $method && method_exists( $class, $method ) ) {
			$plugin_version = call_user_func( array( $class, $method ) );
			if ( version_compare( $plugin_version, $version, '<' ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	* Check if the current perk has all it's requirements met.
	*
	* If requirements are not met, return an array of failed requirements;
	*
	* @return mixed true if requirements are met, array of failed requirements otherwise
	*/
	public function is_supported() {
		$failed_requirements = $this->get_failed_requirements();
		return empty( $failed_requirements );
	}

	public function get_failed_requirements() {

		$failed_requirements = array();

		if ( ! $this->is_gravity_perks_supported() ) {
			$failed_requirements[] = array( 'code' => 'gravity_perks_required' );
		}

		if ( $this->is_old_school() ) {

			if ( ! $this->is_gravity_forms_supported() ) {
				$failed_requirements[] = array( 'code' => 'gravity_forms_required' );
			}

			if ( ! $this->is_wp_supported() ) {
				$failed_requirements[] = array( 'code' => 'wp_required' );
			}

			$requirements = array();
			if ( method_exists( $this, 'requirements' ) ) {
				$requirements = $this->requirements();
			}

			foreach ( $requirements as $requirement ) {

				if ( $this->has_min_version( $requirement ) ) {
					continue;
				}

				$failed_requirements[] = array(
					'code'    => 'other_required',
					'message' => gwar( $requirement, 'message' ),
				);

			}
		} else {

			$minimum_requirements_result = $this->parent->meets_minimum_requirements();
			$failed_requirements         = array();

			/* @TODO: Hook this up to the perks's $_min_gravityforms_version property */
			if ( ! $this->parent->is_gravityforms_supported() ) {
				$failed_requirements[] = array( 'code' => 'gravity_forms_required' );
			}

			foreach ( rgar( $minimum_requirements_result, 'errors', array() ) as $error ) {
				$failed_requirements[] = array(
					'code'    => 'other_required',
					'message' => $error,
				);
			}
		}

		return $failed_requirements;
	}

	/**
	* Adds support for version checking for 'plugins' in GFAddOn::minimum_requirements()
	*
	* This is a monkey patch until Gravity Forms implements this into core.
	*/
	public function check_gf_requirements_plugins_array() {

		$requirements       = $this->parent->minimum_requirements();
		$meets_requirements = array();

		foreach ( rgar( $requirements, 'plugins', array() ) as $plugin_path => $plugin_requirement ) {

			if ( ! is_array( $plugin_requirement ) ) {
				continue;
			}

			$name    = rgar( $plugin_requirement, 'name' );
			$version = rgar( $plugin_requirement, 'version' );

			if ( ! $name || ! $version ) {
				continue;
			}

			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			if ( ! is_plugin_active( $plugin_path ) ) {
				$meets_requirements['meets_requirements'] = false;

				if ( ! $version ) {
					// translators: placeholder is required WordPress plugin name
					$meets_requirements['errors'][] = sprintf( esc_html__( 'Required WordPress plugin is missing: %s.', 'gravityperks' ), $name );

					continue;
				}

				// translators: placeholder is required WordPress plugin name, second placeholder is required version
				$meets_requirements['errors'][] = sprintf( esc_html__( 'Required WordPress plugin is missing: %1$s (%2$s).', 'gravityperks' ), $name, $version );
				continue;
			}

			$plugin_data = get_plugin_data( trailingslashit( WP_PLUGIN_DIR ) . $plugin_path );

			if ( version_compare( rgar( $plugin_data, 'Version' ), $version, '<' ) ) {
				// translators: placeholders are plugin name, current plugin version, plugin name, and required version respectively
				$meets_requirements['errors'][] = sprintf( esc_html__( 'Current %1$s version (%2$s) does not meet minimum %1$s version requirement (%3$s).', 'gravityperks' ), $name, rgar( $plugin_data, 'Version' ), $version );
			}
		}

		return $meets_requirements;

	}

	public function check_requirements() {

		if ( $this->get_failed_requirements() ) {
			add_action( 'admin_notices', array( $this, 'requirements_admin_notice' ) );
			add_action( 'network_admin_notices', array( $this, 'requirements_admin_notice' ) );
			add_action( 'after_plugin_row_' . $this->basename, array( $this, 'requirements_plugin_row_notice' ), 10, 2 );

			return false;
		}

		return true;

	}

	public function get_requirements_notice_text() {

		$screen = get_current_screen();
		if ( ! GravityPerks::is_gravity_page() && ! GravityPerks::is_plugins_page() && $screen->id !== 'dashboard' ) {
			return null;
		}

		$this->load_perk_data();
		$failed_requirements = $this->get_failed_requirements();

		$incompatibilities = '';

		foreach ( $failed_requirements as $failed_requirement ) {
			$incompatibility_text = rgar( $failed_requirement, 'message' ) ? rgar( $failed_requirement, 'message' ) : GravityPerks::get_message( rgar( $failed_requirement, 'code' ), $this->basename );
			$incompatibilities   .= '<li>' . $incompatibility_text . "</li>\n";
		}

		if ( ! $incompatibilities ) {
			return null;
		}

		return $incompatibilities;

	}

	public function requirements_admin_notice() {

		$incompatibilities = $this->get_requirements_notice_text();

		if ( ! $incompatibilities ) {
			return;
		}

		wp_enqueue_style( 'gwp-plugins', GravityPerks::get_base_url() . '/styles/plugins.css' );

		?>

		<div class="error notice gp-requirements-notice">
			<p>
				<?php // translators: placeholder is perk name ?>
				<strong><?php printf( __( 'Uh-oh! <strong>%s</strong> needs your attention.', 'gravityperks' ), $this->data['Name'] ); ?></strong>
			</p>

			<ul>
				<?php echo $incompatibilities; ?>
			</ul>
		</div>

		<?php
	}

	public function requirements_plugin_row_notice( $plugin_file, $plugin_data ) {

		$incompatibilities = $this->get_requirements_notice_text();

		if ( ! $incompatibilities ) {
			return;
		}

		GravityPerks::display_plugin_row_message( '<ul>' . $incompatibilities . '</ul>', $plugin_data, true, $plugin_file );

	}

	public function maybe_setup() {

		$is_doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX === true;

		if ( ( $is_doing_ajax || ! is_admin() ) && is_multisite() ) {
			return;
		}

		$saved_version_key   = $this->key( 'version' );
		$has_version_changed = isset( $this->version ) && $this->version != get_option( $saved_version_key );

		if ( $has_version_changed ) {

			$this->setup();

			update_option( $saved_version_key, $this->version );

		}

	}

	protected function setup() { }

	public function activate() { }

	public function uninstall() { }



	// HELPER FUNCTIONS //

	function get_link_for( $type, $plugin_file = false ) {

		$plugin_file = $plugin_file ? $plugin_file : $this->basename;
		$base_url    = admin_url( 'admin.php?page=gwp_perks' );

		switch ( $type ) {

			case 'activate':
				return wp_nonce_url( admin_url( "plugins.php?action=activate&plugin=$plugin_file" ), "activate-plugin_{$plugin_file}" );

			case 'deactivate':
				return wp_nonce_url( admin_url( "plugins.php?action=deactivate&plugin=$plugin_file" ), "deactivate-plugin_{$plugin_file}" );

			case 'uninstall':
				return wp_nonce_url( admin_url( "plugins.php?action=uninstall&plugin=$plugin_file" ), "uninstall-plugin_{$plugin_file}" );

			case 'delete':
				$page = "plugins.php?action=delete-selected&checked[0]=$plugin_file&gwp=1";
				$url  = is_multisite() ? network_admin_url( $page . '&blog_id=' . get_current_blog_id() ) : admin_url( $page );
				return wp_nonce_url( $url, 'bulk-plugins' );

			case 'install':
				$page = "update.php?action=install-plugin&plugin=$plugin_file&gwp=1&from=gwp";
				// @TODO: might not need to pass blog ID anymore since we no longer are sending to network page for install
				$url = is_multisite() ? admin_url( $page . '&blog_id=' . get_current_blog_id() ) : admin_url( $page );
				return wp_nonce_url( $url, "install-plugin_$plugin_file" );

			case 'upgrade':
				$page = "update.php?action=upgrade-plugin&plugin={$plugin_file}&gwp=1&from=gwp";
				$url  = is_multisite() ? network_admin_url( $page . '&blog_id=' . get_current_blog_id() ) : admin_url( $page );
				return wp_nonce_url( $url, "upgrade-plugin_{$plugin_file}" );

			case 'documentation':
				$documentation = $this->get_documentation();

				// support returning a array with a URL for documentation
				if ( is_array( $documentation ) ) {
					switch ( $documentation['type'] ) {
						case 'url':
							$url = $documentation['value'];
							break;
						default:
							$url = false;
					}
				} elseif ( strpos( (string) $documentation, 'http' ) === 0 ) {
					// Supports returning a plain string URL (e.g. https://gravitywiz.com/documentation/gp-net-perk/).
					$url = $documentation;
				} else {
					// Markdown-based documentation content.
					$url = esc_url( add_query_arg( array(
						'view'      => 'documentation',
						'slug'      => $this->basename,
						'TB_iframe' => true,
						'width'     => 600,
						'height'    => 500,
					), $base_url ) );
				}

				return $url;

			case 'settings':
				return esc_url( add_query_arg( array(
					'view'      => 'perk_settings',
					'slug'      => $this->basename,
					'TB_iframe' => true,
					'width'     => 600,
					'height'    => 500,
				), $base_url ) );
			break;

			// @TODO REVIEW BELOW //

			case 'purchase':
				return 'https://gravitywiz.com/gravity-perks/';
			break;

			case 'deregister':
				return esc_url( wp_nonce_url( add_query_arg( array(
					'page'                => 'gwp_perks',
					'gwp_deregister_perk' => $this->product_id,
				), admin_url( 'admin.php' ) ), 'gwp_deregister_perk' ) );
			break;

			case 'upgrade_details':
				return esc_url( add_query_arg( array(
					'page'      => 'gwp_perks',
					'view'      => 'perk_info',
					'plugin'    => $this->slug,
					'TB_iframe' => 'true',
					'width'     => 600,
					'height'    => 500,
				), admin_url( 'admin.php' ) ) );
			break;
		}

		return '';
	}

	/**
	* Load perk data from plugin headers
	*
	*/
	function load_perk_data() {

		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		if ( empty( $this->data ) ) {
			$this->data = self::get_perk_data( $this->basename );
		}

	}

	function has_update() {

		if ( current_user_can( 'update_plugins' ) ) {
			$current = get_site_transient( 'update_plugins' );
			if ( isset( $current->response[ $this->basename ] ) ) {
				return $current->response[ $this->basename ];
			}
		}

		return false;
	}

	function failed_requirements_tooltip( $requirements ) {

		$messages = $this->get_requirement_messages( $requirements );

		$tooltip = '<ul><li>' . implode( '</li><li>', $messages ) . '</li></ul>';

		return $tooltip;
	}

	function get_requirement_messages( $requirements ) {

		$messages = array();
		foreach ( $requirements as $requirement ) {
			if ( gwar( $requirement, 'message' ) ) {
				$messages[] = gwar( $requirement, 'message' );
			} else {
				$messages[] = str_replace( '"', "'", GWPerks::get_message( gwar( $requirement, 'code' ), $this->basename ) );
			}
		}

		return $messages;
	}

	function drop_tables( $tables ) {
		GravityPerks::drop_tables( $tables );
	}

	function drop_options() {
		global $wpdb;

		$key = $this->key( '' );
		$sql = "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$key}%'";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $sql );
		$options = wp_list_pluck( $results, 'option_name' );

		foreach ( $options as $option ) {
			delete_option( $option );
		}

	}

	function add_css_class( $class, $classes = '' ) {
		$classes = explode( ' ', $classes );
		array_push( $classes, $class );
		return implode( ' ', array_unique( $classes ) );
	}

	public function should_enqueue_frontend_script( $form ) {
		return ! GFForms::get_page() && ! rgempty( GFFormsModel::get_fields_by_type( $form, array( 'form' ) ) );
	}

	public static function doing_ajax( $action = false ) {

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return false;
		}

		return $action ? $action == $_REQUEST['action'] : true;
	}





	// STATIC HELPER FUNCTIONS //

	public static function is_perk( $perk_file, $clear_plugin_cache = false ) {

		$plugin_path = WP_PLUGIN_DIR . '/' . $perk_file;
		if ( ! file_exists( $plugin_path ) ) {
			return false;
		}

		$plugin = self::get_perk_data( $perk_file, $clear_plugin_cache );
		if ( empty( $plugin ) || gwar( $plugin, 'Perk' ) != 'True' ) {
			return false;
		}

		return true;
	}

	public static function is_installed( $perk_file ) {
		return self::get_perk_data( $perk_file ) !== false;
	}

	public static function get_perk_data( $perk_file, $clear_plugin_cache = false ) {

		// get all plugin data (cached via get_plugins function)
		$plugins = GWPerks::get_plugins( $clear_plugin_cache );

		foreach ( $plugins as $plugin_file => $plugin ) {
			if ( $perk_file == $plugin_file ) {
				return $plugin;
			}
		}

		return false;
	}



	// PERK DISPLAY VIEWS //

	function get_documentation() {
		return $this->documentation();
	}

	/**
	* Get the documentation URL for this perk.
	*
	* In the past, the documentation() method was overridden in the perk. Moving forward, it will automatically be
	* retrieved from the "Plugin URI" plugin header.
	*
	* @return mixed
	*/
	function documentation() {
		$this->load_perk_data();
		return $this->data['PluginURI'];
	}

	function get_settings() {
		ob_start();
		if ( ! $this->is_old_school() ) {
			$this->parent->perk_settings( $this );
		} else {
			$this->settings();
		}
		return ob_get_clean();
	}

	function settings() { }

	function perk_settings() { }

	/**
	* Include Markdown and run the perk documentation through it before outputting it to the screen.
	*
	*/
	function display_documentation() {
		_deprecated_function( __method__, '1.2.18.8' );
		echo GWPerks::markdown( $this->get_documentation() );
	}



	// PERK SETTINGS API //

	public static function save_perk_settings( $slug, $new_settings ) {

		$stored_settings = get_option( "{$slug}_settings" );
		if ( ! $stored_settings ) {
			$stored_settings = array();
		}

		foreach ( $new_settings as $key => $setting ) {
			$stored_settings[ $key ] = $setting;
		}

		return update_option( "{$slug}_settings", $stored_settings );
	}

	public static function get_perk_settings( $slug ) {
		return get_option( "{$slug}_settings" );
	}





	// REVIEW ALL CODE BELOW THIS LINE //





	public function update() {
		$perk_options = $this->get_save_options();
		GWPerks::update_perk_option( $perk_options );
	}

	public function set_property( $property, $value ) {
		$this->$property = $value;
		$this->update();
	}

	//    public function activate() {
	//        $this->set_property('is_active', true);
	//    }

	public function deactivate() {
		$this->set_property( 'is_active', false );
	}

	public function delete() {

		// force refresh of installed perks cache on next page load
		GWPerks::flush_installed_perks();

		$perk_dir = str_replace( basename( $this->filename ), '', $this->filename );

		if ( ! $perk_dir ) {
			return new WP_Error( 'perk_delete', __( 'There was an issue deleting this perk. The perk directory was not available.', 'gravityperks' ) );
		}

		$perk_dir_path = GWP_PERKS_DIR . $perk_dir;

		$success = self::remove_directory( $perk_dir_path );

		if ( ! is_wp_error( $installer->result ) ) {
			GWPerks::flush_installed_perks();
		}

		return $success;
	}

	/**
	* Get default perks options, loop through and save current Perk values for each property. New default options
	* will automatically be saved.
	*
	*/
	public function get_save_options() {

		$default_options = self::get_default_perk_options( $this->slug );
		$save_options    = array();

		foreach ( $default_options as $key => $value ) {
			$save_options[ $key ] = isset( $this->$key ) ? $this->$key : '';
		}

		return $save_options;
	}



	/**
	* Looks for the form_settings_ui() method and the form_settings_js() method and add hooks
	* for the existing methods. Also sets $has_form_settings property to true, indicating that
	* the custom form settings tab should be displayed.
	*
	*/
	public function enqueue_form_settings() {

		GWPerks::$has_form_settings = true;

		if ( method_exists( $this, 'form_settings_ui' ) ) {
			add_action( 'gws_form_settings', array( &$this, 'form_settings_ui' ) );
		}

		if ( method_exists( $this, 'form_settings_js' ) ) {
			add_action( 'gform_editor_js', array( &$this, 'form_settings_js' ) );
		}

	}

	/**
	* Looks for the field_settings_ui() method and the field_settings_js() method and enqueue
	* the existing methods. Also sets $has_field_settings property to true, indicating that
	* the custom field settings tab should be displayed.
	*
	*/
	public function enqueue_field_settings( $priority = 10 ) {

		GWPerks::enqueue_field_settings();

		if ( method_exists( $this, 'field_settings_ui' ) ) {
			add_action( 'gws_field_settings', array( $this, 'field_settings_ui' ) );
		}

		if ( method_exists( $this, 'field_settings_js' ) ) {
			add_action( 'gform_editor_js', array( $this, 'field_settings_js' ) );
		}

	}

	/**
	* Add a tooltip.
	*
	* This method has been deprecated. The recommended method for adding tooltips now is to include all tooltips via a
	* tooltips() method.
	*
	* @deprecated
	*
	* @param $key
	* @param $content
	*/
	public function add_tooltip( $key, $content ) {
		$this->tooltips[ $key ] = $content;
		if ( ! has_filter( 'gform_tooltips', array( $this, 'load_tooltips' ) ) ) {
			add_filter( 'gform_tooltips', array( $this, 'load_tooltips' ) );
		}
	}

	public function load_tooltips( $tooltips ) {
		return array_merge( $tooltips, $this->tooltips );
	}

	public function get_tooltips( $tooltips ) {

		if ( ! $this->is_old_school() && method_exists( $this->parent, 'tooltips' ) ) {
			return $this->parent->tooltips( $tooltips );
		}

		return $tooltips;
	}

	/**
	* Enqueue a script via WPs wp_enqueue_script() function. Optionally specify an array of pages for
	* which the script should be loaded.
	*
	* @param mixed $args
	*/
	public static function enqueue_script( $args ) {

		/**
		* @var $pages
		* @var $handle
		* @var $src
		* @var $deps
		* @var $ver
		* @var $in_footer
		*/
		$defaults = array(
			'pages'     => array(),
			'handle'    => '',
			'src'       => false,
			'deps'      => array(),
			'ver'       => false,
			'in_footer' => false,
		);

		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( shortcode_atts( $defaults, $args ) );

		if ( ! $handle ) {
			return;
		}

		if ( ! empty( $pages ) ) {
			$pages = is_array( $pages ) ? $pages : array( $pages );
			if ( in_array( gwget( 'page' ), $pages ) ) {
				wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
			}
		} elseif ( empty( $pages ) ) {
			wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
		}

	}

	public function get_base_url() {
		$folder = basename( dirname( $this->basename ) );
		// plugins_url() will auto-handle http/https, WP_UPLOAD_URL will not
		return plugins_url( $folder );
	}

	public function get_base_path() {
		$folder = basename( dirname( $this->basename ) );
		return WP_PLUGIN_DIR . "/$folder";
	}

	public function key( $key ) {
		$prefix = isset( $this->prefix ) ? $this->prefix : $this->slug . '_';
		return $prefix . $key;
	}

	public function field_prop( $field, $prop, $prefix = false ) {
		if ( $prefix === false ) {
			$prefix = $this->slug . '_';
		}

		return gwar( $field, $prefix . $prop );
	}

	function include_field( $class_name = false, $file = false ) {

		$field_file_paths = array(
			$file,
			$this->get_base_path() . '/fields.php',
			$this->get_base_path() . '/includes/fields.php',
		);

		foreach ( $field_file_paths as $file_path ) {

			if ( $file_path && file_exists( $file_path ) ) {

				require_once( GWPerks::get_base_path() . '/model/field.php' );
				require_once( $file_path );

				$field_class = $class_name ? $class_name : $this->slug . 'Field';
				if ( ! class_exists( $field_class ) ) {
					return false;
				}

				$args = array( 'perk' => $this );

				if ( is_callable( array( $field_class, 'get_instance' ) ) ) {
					$field_obj = call_user_func( array( $field_class, 'get_instance' ), $args );
				} else {
					$field_obj = new $field_class( $args );
				}

				return $field_obj;
			}
		}

		return false;
	}



	// UI HELPERS //

	public static function generate_checkbox( &$perk, $args ) {

		/**
		* @var $id
		* @var $class
		* @var $label
		* @var $value
		* @var $description
		* @var $data
		* @var $onclick
		* @var $toggle_selection
		*/
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( wp_parse_args( $args, array(
			'id'             => '',
			'class'          => '',
			'label'          => '',
			'value'          => 1,
			'description'    => false,
			'data'           => GWPerk::get_perk_settings( $perk->get_id() ),
			'onclick'        => '',
			'toggle_section' => false,
		) ) );

		$id = $perk->get_id() . "_$id";

		if ( $label ) {
			$label = "<label for=\"$id\">$label</label>";
		}

		if ( $description ) {
			$description = "<p class=\"description\">$description</p>";
		}

		if ( is_array( $data ) ) {
			$is_checked = isset( $data[ $id ] ) && $data[ $id ] ? 'checked="checked"' : '';
		} else {
			$is_checked = $data ? 'checked="checked"' : '';
		}

		if ( $toggle_section && ! $onclick ) {
			$onclick = 'onclick="gperk.toggleSection(this, \'' . $toggle_section . '\');"';
			$class  .= ' gwp-expandable';
			$class  .= $is_checked ? ' open' : '';
		}

		return "<div class=\"gwp-field gwp-checkbox $class\"><input type=\"checkbox\" id=\"$id\" name=\"$id\" value=\"$value\" $is_checked $onclick /> <div class=\"label\">$label $description</div></div>";
	}

	public static function generate_textarea( $perk, $args ) {

		/**
		* @var $id
		* @var $class
		* @var $label
		* @var $value
		* @var $description
		* @var $data
		*/
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract(wp_parse_args($args, array(
			'id'          => '',
			'class'       => '',
			'label'       => '',
			'value'       => 1,
			'description' => false,
			'data'        => GWPerk::get_perk_settings( $perk->get_id() ),
		)));

		$id = $perk->get_id() . "_$id";

		if ( $label ) {
			$label = "<label for=\"$id\">$label</label>";
		}

		if ( $description ) {
			$description = "<p class=\"description\">$description</p>";
		}

		$value = gwar( $data, $id );

		return "
			<div class=\"gwp-field gwp-textarea\">
				<div class=\"label\">
					$label
					$description
				</div>
				<textarea id=\"$id\" name=\"$id\">$value</textarea>
			</div>";
	}

	public static function generate_select( $perk, $args ) {

		/**
		* @var $id
		* @var $class
		* @var $label
		* @var $values
		* @var $description
		* @var $data
		*/
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract(wp_parse_args($args, array(
			'id'          => '',
			'class'       => '',
			'label'       => '',
			'values'      => array(),
			'description' => false,
			'data'        => GWPerk::get_perk_settings( $perk->get_id() ),
		)));

		$id = $perk->get_id() . "_$id";

		if ( $label ) {
			$label = "<label for=\"$id\">$label</label>";
		}

		if ( $description ) {
			$description = "<p class=\"description\">$description</p>";
		}

		$value   = gwar( $data, $id );
		$options = self::generate_options( $perk, $values, $value );

		return "
			<div class=\"gwp-field gwp-select\">
				<div class=\"label\">
					$label
					$description
				</div>
				<select id=\"$id\" name=\"$id\">
					$options
				</select>
			</div>";
	}

	public static function generate_options( $perk, $values, $selected_value ) {

		$options  = array();
		$is_assoc = self::is_associative_array( $values );

		foreach ( $values as $value => $text ) {
			// allow non-associative arrays to be passed, use $value as as $text and $value
			if ( ! $is_assoc ) {
				$value = $text;
			}
			$is_selected = $selected_value == $value ? 'selected="selected"' : '';
			$options[]   = "<option value=\"$value\" $is_selected>$text</option>";
		}

		return implode( "\n", $options );
	}

	public static function generate_input( $perk, $args ) {

		/**
		* @var $id
		* @var $class
		* @var $label
		* @var $description
		* @var $data
		* @var $type
		*/
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( wp_parse_args( $args, array(
			'id'          => '',
			'class'       => '',
			'label'       => '',
			'description' => false,
			'data'        => GWPerk::get_perk_settings( $perk->get_id() ),
			'type'        => 'text',
		) ) );

		$id    = $perk->get_id() . "_$id";
		$value = gwar( $data, $id );

		if ( $label ) {
			$label = "<label for=\"$id\">$label</label>";
		}

		if ( $description ) {
			$description = "<p class=\"description\">$description</p>";
		}

		return "
			<div class=\"gwp-field gwp-input gwp-input-$type $class\">
				<div class=\"label\">
					$label
					$description
				</div>
				<input type=\"$type\" id=\"$id\" name=\"$id\" value=\"$value\" />
			</div>";
	}

	public static function is_associative_array( $array ) {
		return array_keys( $array ) !== range( 0, count( $array ) - 1 );
	}




	// SORT... //

	public static function get_default_perk_options( $slug ) {
		return array(
			'slug'      => $slug,
			'is_active' => false,
			'filename'  => '',
		);
	}

	public static function remove_directory( $dir ) {

		if ( is_dir( $dir ) ) {

			$objects = scandir( $dir );

			foreach ( $objects as $object ) {
				if ( $object != '.' && $object != '..' ) {
					if ( filetype( $dir . '/' . $object ) == 'dir' ) {
						self::remove_directory( $dir . '/' . $object );
					} else {
						unlink( $dir . '/' . $object );
					}
				}
			}

			reset( $objects );
			rmdir( $dir );

			return true;
		}

		return false;
	}

	public static function create_lead_object( $form ) {

		$lead            = array();
		$lead['id']      = -1;
		$lead['form_id'] = $form['id'];

		foreach ( $form['fields'] as $field ) {

			//Ignore fields that are marked as display only
			if ( gwget( 'displayOnly', $field ) && $field['type'] != 'password' ) {
				continue;
			}

			//only save fields that are not hidden (except on entry screen)
			if ( ! RGFormsModel::is_field_hidden( $form, $field, array() ) ) {

				if ( isset( $field['inputs'] ) && is_array( $field['inputs'] ) ) {
					foreach ( $field['inputs'] as $input ) {
						$lead[ (string) $input['id'] ] = self::get_input_value( $form, $field, $lead, $input['id'] );
					}
				} else {
					$lead[ $field['id'] ] = self::get_input_value( $form, $field, $lead, $field['id'] );
				}
			}
		}

		return $lead;
	}

	public static function get_input_value( $form, $field, &$lead, $input_id ) {

		$input_name = 'input_' . str_replace( '.', '_', $input_id );
		$value      = gwpost( $input_name );

		if ( empty( $value ) && $field->adminOnly && ! IS_ADMIN ) {
			$value = GFFormsModel::get_default_value( $field, $input_id );
		}

		//processing values so that they are in the correct format for each input type
		$value = RGFormsModel::prepare_value( $form, $field, $value, $input_name, gwar( $lead, 'id' ) );

		if ( ! empty( $value ) || $value === '0' ) {

			$value = apply_filters( 'gform_save_field_value', $value, $lead, $field, $form, $input_id );

		}

		return $value;
	}

	public static function is_form_valid( $form ) {

		foreach ( $form['fields'] as $field ) {
			if ( $field['failed_validation'] == true ) {
				return false;
			}
		}

		return true;
	}

	public static function has_merge_tag( $merge_tag, $text ) {
		preg_match( '{' . $merge_tag . '([:]+)?([\w\s!?,\'"]+)?}', $text, $matches );
		return ! empty( $matches );
	}

	/**
	* Get perk setting, will automatically append the perk slug.
	*
	* @param mixed $key
	* @param mixed $settings
	*/
	function get_setting( $key, $settings = array() ) {
		if ( empty( $settings ) ) {
			$settings = GWPerk::get_perk_settings( $this->get_id() );
		}

		return gwar( $settings, $this->get_id() . "_$key" );
	}

	public static function echo_if( $text, $option, $value = true ) {
		if ( $option == $value ) {
			echo $text;
		}
	}

	protected function register_script( $handle, $src, $deps, $ver, $in_footer ) {
		wp_register_script( $handle, $src, $deps, $ver, $in_footer );
		self::register_noconflict_script( $handle );
	}

	public static function register_noconflict_script( $script_name ) {
		add_filter( 'gform_noconflict_scripts', array( new GP_Late_Static_Binding( array( 'value' => $script_name ) ), 'Perk_array_push' ) );

	}

	public static function register_noconflict_styles( $style_name ) {
		add_filter( 'gform_noconflict_styles', array( new GP_Late_Static_Binding( array( 'value' => $style_name ) ), 'Perk_array_push' ) );
	}

	public static function register_preview_style( $style_name ) {
		add_filter( 'gform_preview_styles', array( new GP_Late_Static_Binding( array( 'value' => $style_name ) ), 'Perk_array_push' ) );
	}

}

class GWPerk extends GP_Perk { }
