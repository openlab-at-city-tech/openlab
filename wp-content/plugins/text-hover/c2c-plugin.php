<?php
/**
 * @package C2C_Plugins
 * @author  Scott Reilly
 * @version 044
 */
/*
Basis for other plugins.

Compatible with WordPress 3.6+ through 4.5+.

*/

/*
	Copyright (c) 2010-2016 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_TextHover_Plugin_044' ) ) :

abstract class c2c_TextHover_Plugin_044 {
	protected $plugin_css_version = '009';
	protected $options            = array();
	protected $options_from_db    = '';
	protected $option_names       = array();
	protected $required_config    = array( 'menu_name', 'name' );
	protected $config_attributes  = array(
		'allow_html'       => false,
		'class'            => array(),
		'datatype'         => '',
		'default'          => '',
		'help'             => '',
		'input'            => '',
		'input_attributes' => '',
		'label'            => '',
		'no_wrap'          => false,
		'numbered'         => false,
		'options'          => '',
		'output'           => '', // likely deprecated
		'required'         => false
	);
	protected $saved_settings     = false;
	protected $saved_settings_msg = '';

	private   $setting_index      = 0;

	/**
	 * Returns the plugin framework's version.
	 *
	 * @since 040
	 */
	public function c2c_plugin_version() {
		return '044';
	}

	/**
	 * Handles installation tasks, such as ensuring plugin options are instantiated and saved to options table.
	 *
	 * @param string $version       Version of the plugin.
	 * @param string $id_base       A unique base ID for the plugin (generally a lower-case, dash-separated version of plugin name).
	 * @param string $author_prefix Short (2-3 char) identifier for plugin author.
	 * @param string $file          The __FILE__ value for the sub-class.
	 * @param array $plugin_options Optional. Array specifying further customization of plugin configuration.
	 */
	protected function __construct( $version, $id_base, $author_prefix, $file, $plugin_options = array() ) {
		$id_base = sanitize_title( $id_base );
		if ( ! file_exists( $file ) ) {
			die( sprintf( __( 'Invalid file specified for C2C_Plugin: %s', 'text-hover' ), $file ) );
		}

		$u_id_base = str_replace( '-', '_', $id_base );
		$author_prefix .= '_';
		$defaults = array(
			'admin_options_name'      => $author_prefix . $u_id_base, // The setting under which all plugin settings are stored under (as array)
			'config'                  => array(),                     // Default configuration
			'disable_contextual_help' => false,                       // Prevent overriding of the contextual help?
			'disable_update_check'    => false,                       // Prevent WP from checking for updates to this plugin?
			'hook_prefix'             => $u_id_base . '_',            // Prefix for all hooks
			'form_name'               => $u_id_base,                  // Name for the <form>
			'menu_name'               => '',                          // Specify this via plugin
			'name'                    => '',                          // Full, localized version of the plugin name
			'nonce_field'             => 'update-' . $u_id_base,      // Nonce field value
			'settings_page'           => 'options-general',           // The type of the settings page.
			'show_admin'              => true,                        // Should admin be shown? Only applies if admin is enabled
			'textdomain'              => $id_base,                    // Textdomain for localization
			'textdomain_subdir'       => 'lang'                       // Subdirectory, relative to plugin's root, to hold localization files
		);
		$settings = wp_parse_args( $plugin_options, $defaults );

		foreach ( array_keys( $defaults ) as $key ) {
			$this->$key = $settings[ $key ];
		}

		$this->author_prefix        = $author_prefix;
		$this->id_base              = $id_base;
		$this->options_page         = ''; // This will be set when the options is created
		$this->plugin_basename      = plugin_basename( $file );
		$this->plugin_file          = $file;
		$this->plugin_path          = plugins_url( '', $file );
		$this->u_id_base            = $u_id_base; // Underscored version of id_base
		$this->version              = $version;

		$plugin_file = implode( '/', array_slice( explode( '/', $this->plugin_basename ), -2 ) );

		add_action( 'init',                         array( $this, 'init' ) );
		add_action( 'activate_' . $plugin_file,     array( $this, 'install' ) );
		add_action( 'deactivate_' . $plugin_file,   array( $this, 'deactivate' ) );
		if ( $this->is_plugin_admin_page() || $this->is_submitting_form() ) {
			add_action( 'admin_init', array( $this, 'init_options' ) );
			if ( ! $this->is_submitting_form() ) {
				add_action( 'admin_head', array( $this, 'add_c2c_admin_css' ) );
			}
		}
	}

	/**
	 * A dummy magic method to prevent object from being cloned
	 *
	 * @since 036
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'text-hover' ), '036' ); }

	/**
	 * A dummy magic method to prevent object from being unserialized
	 *
	 * @since 036
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'text-hover' ), '036' ); }

	/**
	 * Returns the plugin's version.
	 *
	 * @since 031
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * Handles installation tasks, such as ensuring plugin options are instantiated and saved to options table.
	 *
	 * This can be overridden.
	 */
	public function install() {
		$this->options = $this->get_options();
		update_option( $this->admin_options_name, $this->options );
	}

	/**
	 * Handles deactivation tasks
	 *
	 * This should be overridden.
	 */
	public function deactivate() { }

	/**
	 * Handles actions to be hooked to 'init' action, such as loading text domain and loading plugin config data array.
	 */
	public function init() {
		global $c2c_plugin_max_css_version;

		if ( ! isset( $c2c_plugin_max_css_version ) || ( $c2c_plugin_max_css_version < $this->plugin_css_version ) ) {
			$c2c_plugin_max_css_version = $this->plugin_css_version;
		}

		$this->load_textdomain();
		$this->load_config();
		$this->verify_config();

		add_filter( 'plugin_row_meta', array( $this, 'donate_link' ), 10, 2 );

		if ( $this->disable_update_check ) {
			add_filter( 'http_request_args', array( $this, 'disable_update_check' ), 5, 2 );
		}

		if ( $this->show_admin && $this->settings_page && ! empty( $this->config ) && current_user_can( 'manage_options' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			if ( ! $this->disable_contextual_help ) {
				if ( version_compare( $GLOBALS['wp_version'], '3.3', '<' ) ) {
					add_filter( 'contextual_help', array( $this, 'contextual_help' ), 10, 3 );
				}
				if ( $this->is_plugin_admin_page() ) {
					add_thickbox();
				}
			}
		}

		$this->register_filters();
	}

	/**
	 * Checks to see if the plugin has been upgraded from an earlier version.
	 *
	 * Calls handle_plugin_update() if an upgrade was detected. Override that
	 * to do whatever needs done to bring older settings, etc up-to-date.
	 *
	 * @since 021
	 */
	public function check_if_plugin_was_upgraded() {
		// If there was no previous install of this plugin, then don't need to do anything here
		if ( empty( $this->options_from_db ) ) {
			return;
		}

		$_version = isset( $this->options['_version'] ) ? $this->options['_version'] : '0.0';

		if ( $_version != $this->version ) {
			// Save the original options into another option in case something goes wrong.
			// TODO: Currently just saves one version back... should it save more?
			update_option( 'bkup_' . $this->admin_options_name, $this->options );

			$this->options['_version'] = $this->version;
			$this->options = $this->handle_plugin_upgrade( $_version, $this->options );
			update_option( $this->admin_options_name, $this->options );
		}
	}

	/**
	 * Handle plugin updates. (To be implemented by inheriting class, if
	 * necessary.)
	 *
	 * Intended to be used for updating plugin options, etc.
	 *
	 * This is only called if the version stored in the db doesn't match the
	 * plugin's current version. At the very least the settings will get
	 * re-saved so that the new current version can be recorded.
	 *
	 * @since 021
	 *
	 * @param string $old_version The version number of the old version of
	 *                            the plugin. '0.0' indicates no version
	 *                            previously stored.
	 * @param array $options      Array of all plugin options
	 */
	protected function handle_plugin_upgrade( $old_version, $options ) {
		/* Example:
		if ( version_compare( '1.2', $old_version ) > 0 ) {
			// Plugin got upgraded from a version earlier than 1.2
			// Which (for this example) is when a minimum value got raised
			if ( $options['min_value'] < 5 )
				$options['min_value'] = 5;
		}
		*/
		return $options; // Important!
	}

	/**
	 * Prevents this plugin from being included when WordPress phones home
	 * to check for plugin updates.
	 *
	 * @param array $r Response array.
	 * @param string $url URL for the update check.
	 *
	 * @return array The response array with this plugin removed, if present.
	 */
	public function disable_update_check( $r, $url ) {
		// Bail immediately if not a plugin update request.
		$plugin_update_check_strpos = strpos( $url, '://api.wordpress.org/plugins/update-check' );
		if ( 4 !== $plugin_update_check_strpos && 5 !== $plugin_update_check_strpos ) {
			return $r;
		}

		$plugins = unserialize( $r['body']['plugins'] );
		unset( $plugins->plugins[ $this->plugin_basename ] );
		unset( $plugins->active[ array_search( $this->plugin_basename, $plugins->active ) ] );
		$r['body']['plugins'] = serialize( $plugins );
		return $r;
	}

	/**
	 * Initializes options.
	 */
	public function init_options() {
		register_setting( $this->admin_options_name, $this->admin_options_name, array( $this, 'sanitize_inputs' ) );
		add_settings_section( 'default', '', array( $this, 'options_page_description' ), $this->plugin_file );
		add_filter( 'whitelist_options', array( $this, 'whitelist_options' ) );
		foreach ( $this->get_option_names( false ) as $opt ) {
			add_settings_field( $opt, $this->get_option_label( $opt ), array( $this, 'display_option' ), $this->plugin_file, 'default', $opt );
		}
	}

	/**
	 * Whitelist the plugin's option(s)
	 *
	 * @param array $options Array of options.
	 *
	 * @return array The whitelist-amended $options array.
	 */
	public function whitelist_options( $options ) {
		$added = array( $this->admin_options_name => array( $this->admin_options_name ) );
		$options = add_option_whitelist( $added, $options );
		return $options;
	}

	/**
	 * Outputs the descriptive text (and h2 heading) for the options page.
	 *
	 * Intended to be overridden by sub-class.
	 *
	 * @param string $localized_heading_text (optional) Localized page heading text.
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		if ( ! is_string( $localized_heading_text ) ) {
			$localized_heading_text = '';
		}

		if ( empty( $localized_heading_text ) ) {
			$localized_heading_text = $this->name;
		}
		if ( $localized_heading_text ) {
			echo '<h1>' . $localized_heading_text . "</h1>\n";
		}
		if ( ! $this->disable_contextual_help ) {
			echo '<p class="see-help">' . __( 'See the "Help" link to the top-right of the page for more help.', 'text-hover' ) . "</p>\n";
		}
	}

	/**
	 * Gets the label for a given option.
	 *
	 * @param string $opt The option.
	 *
	 * @return string The label for the option.
	 */
	public function get_option_label( $opt ) {
		$label = isset( $this->config[ $opt ]['label'] ) ? $this->config[ $opt ]['label'] : '';
		if ( true === $this->config[ $opt ]['numbered'] ) {
			$label = ++$this->setting_index . ". $label";
		}
		return $label;
	}

	/**
	 * Resets plugin options.
	 *
	 * @return array
	 */
	public function reset_options() {
		$this->reset_caches();
		$this->options = $this->get_options( false );
		return $this->options;
	}

	/**
	 * Resets caches and data memoization.
	 *
	 * @since 044
	 */
	public function reset_caches() {
		$this->options         = array();
		$this->option_names    = array();
		$this->options_from_db = '';
	}

	/**
	 * Sanitizes user inputs prior to saving.
	 */
	public function sanitize_inputs( $inputs ) {
		do_action( $this->get_hook( 'before_save_options' ), $this );
		if ( isset( $_POST['Reset'] ) ) {
			$options = $this->reset_options();
			add_settings_error( 'general', 'settings_reset', __( 'Settings reset.', 'text-hover' ), 'updated' );
		} else {
			// Start with the existing options, then start overwriting their potential override value. (This prevents
			// unscrupulous addition of fields by the user)
			$options = $this->get_options();
			$option_names = $this->get_option_names();
			$option_names = (array) apply_filters( $this->get_hook( 'sanitized_option_names' ), $option_names, $inputs );
			foreach ( $option_names as $opt ) {
				if ( !isset( $inputs[ $opt ] ) ) {
					if ( $this->config[ $opt ]['input'] == 'checkbox' ) {
						$options[ $opt ] = '';
					} elseif ( true === $this->config[ $opt ]['required'] ) {
						$msg = sprintf( __( 'A value is required for: "%s"', 'text-hover' ), $this->config[ $opt ]['label'] );
						add_settings_error( 'general', 'setting_required', $msg, 'error' );
					}
				}
				else {
					$val = $inputs[ $opt ];
					$error = false;
					if ( empty( $val ) && ( true === $this->config[ $opt ]['required'] ) ) {
						$msg = sprintf( __( 'A value is required for: "%s"', 'text-hover' ), $this->config[ $opt ]['label'] );
						$error = true;
					} else {
						$input = $this->config[ $opt ]['input'];
						switch ( $this->config[ $opt ]['datatype'] ) {
							case 'checkbox':
								break;
							case 'int':
								if ( ! empty( $val ) && ( ! is_numeric( $val ) || ( intval( $val ) != round( $val ) ) ) ) {
									$msg = sprintf( __( 'Expected integer value for: %s', 'text-hover' ), $this->config[ $opt ]['label'] );
									$error = true;
									$val = '';
								}
								break;
							case 'array':
								if ( empty( $val ) )
									$val = array();
								elseif ( is_array( $val ) )
									$val = array_map( 'trim', $val );
								elseif ( $input == 'text' )
									$val = explode( ',', str_replace( array( ', ', ' ', ',' ), ',', $val ) );
								else
									$val = array_map( 'trim', explode( "\n", trim( $val ) ) );
								break;
							case 'hash':
								if ( ! empty( $val ) && $input != 'select' && !is_array( $val ) ) {
									$new_values = array();
									foreach ( explode( "\n", $val ) AS $line ) {
										// TODO: It's possible to allow multi-line replacement text, in which case
										// instead of skipping invalid looking lines, simply append them to the
										// previous line, joined with "\n".
										if ( false === strpos( $line, '=>' ) ) {
											continue;
										}
										list( $shortcut, $text ) = array_map( 'trim', explode( "=>", $line, 2 ) );
										if ( $shortcut && $text ) {
											$new_values[str_replace( '\\', '', $shortcut )] = str_replace( '\\', '', $text );
										}
									}
									$val = $new_values;
								}
								break;
						}
					}
					if ( $error ) {
						add_settings_error( 'general', 'setting_not_int', $msg, 'error' );
					}
					$options[ $opt ] = $val;
				}
			}
			$options = apply_filters( $this->get_hook( 'before_update_option' ), $options, $this );
		}
		$options['_version'] = $this->version;
		return $options;
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	abstract protected function load_config();

	/**
	 * Adds a new option to the plugin's configuration.
	 *
	 * Intended to be used for dynamically adding a new option after the config
	 * is initially created via load_config(), but it can be called earlier.
	 *
	 * @since 044
	 *
	 * @param string $option_name The option name.
	 * @param array  $args        The configuration for the setting.
	 * @return array The fully initialized option.
	 */
	public function add_option( $option_name, $args ) {
		$this->config[ $option_name ] = $args;

		// This function may be running after the config array has already been
		// processed by the plugin, thus this new option won't be automatically
		// verified, which includes setting defaults for setting attributes that
		// weren't explicitly specified.
		$this->verify_options( array( $option_name ) );

		return $this->config[ $option_name ];
	}

	/**
	 * Verify that the necessary configuration files were set in the inheriting class.
	 */
	public function verify_config() {
		// Ensure required configuration options have been configured via the sub-class. Die if any aren't.
		foreach ( $this->required_config as $config ) {
			if ( empty( $this->$config ) ) {
				die( sprintf( __( "The plugin configuration option '%s' must be supplied.", 'text-hover' ), $config ) );
			}
		}

		// Set/change configuration options based on sub-class changes.
		if ( empty( $this->config ) ) {
			$this->show_admin = false;
		} else {
			$this->verify_options();
		}
	}

	/**
	 * Initializes any option attributes that weren't specified by the plugin.
	 *
	 * @since 044
	 *
	 * @param array $options Array of all the option names to verify. Leave empty
	 *                       to verify them all. Default empty array.
	 */
	public function verify_options( $options = array() ) {
		// If no options specified, assume them all.
		if ( ! $options ) {
			$options = $this->get_option_names( true );
		}

		foreach ( $options as $opt ) {
			foreach ( $this->config_attributes as $attrib => $default) {
				if ( ! isset( $this->config[ $opt ][ $attrib ] ) ) {
					$this->config[ $opt ][ $attrib ] = $default;
				}
			}
			if ( 'array' === $this->config[ $opt ]['datatype'] && ! is_array( $this->config[ $opt ]['default'] ) ) {
				$this->config[ $opt ]['default'] = $this->config[ $opt ]['default'] ?
					array( $this->config[ $opt ]['default'] ) :
					array();
			}
		}
		$this->reset_caches();
	}

	/**
	 * Loads the localization textdomain for the plugin.
	 */
	protected function load_textdomain() {
		load_plugin_textdomain( 'text-hover' );
	}

	/**
	 * Registers filters.
	 * NOTE: This occurs during the 'init' filter, so you can't use this to hook
	 * anything that happens earlier.
	 */
	public function register_filters() {
		// This should be overridden in order to define filters.
	}

	/**
	 * Outputs simple contextual help text, comprising solely of a thickboxed link
	 * to the plugin's hosted readme.txt file.
	 *
	 * NOTE: If overriding this in a sub-class, before sure to include the
	 * check at the beginning of the function to ensure it shows up on its
	 * own settings admin page.
	 *
	 * @param string $contextual_help The default contextual help.
	 * @param int    $screen_id       The screen ID.
	 * @param object $screen          The screen object (only supplied in WP 3.0).
	 */
	public function contextual_help( $contextual_help, $screen_id, $screen = null ) {
		if ( $screen_id != $this->options_page ) {
			return $contextual_help;
		}

		$help_url = admin_url( "plugin-install.php?tab=plugin-information&amp;plugin={$this->id_base}&amp;TB_iframe=true&amp;width=640&amp;height=514" );

		$help = '<h3>' . __( 'More Plugin Help', 'text-hover' ) . '</h3>';
		$help .= '<p class="more-help">';
		$help .= '<a title="' . esc_attr( sprintf( __( 'More information about %1$s %2$s', 'text-hover' ), $this->name, $this->version ) ) .
			'" class="thickbox" href="' . $help_url . '">' . __( 'Click for more help on this plugin', 'text-hover' ) . '</a>' .
			__( ' (especially check out the "Other Notes" tab, if present)', 'text-hover' );
		$help .= ".</p>\n";
		return $help;
	}

	/**
	 * Outputs CSS into admin head of the plugin's settings page.
	 */
	public function add_c2c_admin_css() {
		global $c2c_plugin_max_css_version, $c2c_plugin_css_was_output;
		if ( ( $c2c_plugin_max_css_version != $this->plugin_css_version ) || ( isset( $c2c_plugin_css_was_output ) && $c2c_plugin_css_was_output ) ) {
			return;
		}

		$c2c_plugin_css_was_output = true;
		$logo = plugins_url( 'c2c_minilogo.png', $this->plugin_file );
		/**
		 * Remember to increment the plugin_css_version variable if changing the CSS
		 */
		echo <<<HTML
		<style type="text/css">
		.long-text {width:98% !important;}
		#c2c {
			text-align:center;
			color:#888;
			background-color:#ffffef;
			padding:5px 0 0;
			margin-top:12px;
			border-style:solid;
			border-color:#dadada;
			border-width:1px 0;
			overflow: auto;
		}
		#c2c div:first-child {
			margin:0 auto;
			padding:5px 40px 0 0;
			width:45%;
			min-height:40px;
			background:url('$logo') no-repeat top right;
		}
		#c2c span {
			display:block;
			font-size:x-small;
		}
		.form-table {margin-bottom:20px;}
		.c2c-plugin-list {margin-left:2em;}
		.c2c-plugin-list li {list-style:disc outside;}
		.wrap {margin-bottom:30px !important;}
		.c2c-form .hr, .c2c-hr {border-bottom:1px solid #ccc;padding:0 2px;margin-bottom:6px;}
		.c2c-fieldset {border:1px solid #ccc; padding:2px 8px;}
		.c2c-textarea, .c2c-inline_textarea {width:98%;font-family:"Courier New", Courier, mono;}
		.see-help {font-size:x-small;font-style:italic;}
		.more-help {display:block;margin-top:8px;}
		</style>

HTML;
	}

	/**
	 * Registers the admin options page and the Settings link.
	 */
	public function admin_menu() {
		add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'plugin_action_links' ) );
		switch ( $this->settings_page ) {
			case 'options-general' :
				$func_root = 'options';
				break;
			case 'themes' :
				$func_root = 'theme';
				break;
			default :
				$func_root = $this->settings_page;
		}
		$menu_func = 'add_' . $func_root . '_page';
		if ( function_exists( $menu_func ) ) {
			$this->options_page = call_user_func( $menu_func, $this->name, $this->menu_name, 'manage_options', $this->plugin_basename, array( $this, 'options_page' ) );
			add_action( 'load-' . $this->options_page, array( $this, 'help_tabs' ) );
		}
	}

	/**
	 * Initialize help tabs.
	 *
	 * @since 034
	 */
	public function help_tabs() {
		if ( ! class_exists( 'WP_Screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( $screen->id != $this->options_page ) {
			return;
		}

		$this->help_tabs_content( $screen );
	}

	/**
	 * Configures help tabs content.
	 *
	 * This should be overridden by inheriting class if it needs help content.
	 *
	 * @since 034
	 */
	public function help_tabs_content( $screen ) {
		$screen->add_help_tab( array(
			'id'      => 'c2c-more-help-' . $this->id_base,
			'title'   => __( 'More Help', 'text-hover' ),
			'content' => self::contextual_help( '', $this->options_page )
		) );
	}

	/**
	 * Adds a 'Settings' link to the plugin action links.
	 *
	 * @param int $limit The default limit value for the current posts query.
	 *
	 * @return array Links associated with a plugin on the admin Plugins page
	 */
	public function plugin_action_links( $action_links ) {
		$settings_link = '<a href="' . $this->settings_page . '.php?page='.$this->plugin_basename.'">' . __( 'Settings', 'text-hover' ) . '</a>';
		array_unshift( $action_links, $settings_link );
		return $action_links;
	}

	/**
	 * Adds donate link to plugin row.
	 */
	public function donate_link( $links, $file ) {
		if ( $file == $this->plugin_basename ) {
			$donation_url  = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522';
			$donation_url .= urlencode( sprintf( __( 'Donation for coffee2code plugin: %s', 'text-hover' ), $this->name ) );
			$title         = __( 'Coffee fuels my coding.', 'text-hover' );
			$links[] = '<a href="' . esc_url( $donation_url ) . '" title="' . esc_attr( $title ) . '">' . __( 'Donate', 'text-hover' ) . '</a>';
		}
		return $links;
	}

	/**
	 * See if the setting is pertinent to this version of WP
	 *
	 * @since 013
	 *
	 * @param string $opt The option name.
	 *
	 * @return bool If the option is valid for this version of WP.
	 */
	protected function is_option_valid( $opt ) {
		global $wp_version;
		$valid = true;
		$ver_operators = array( 'wpgt' => '>', 'wpgte' => '>=', 'wplt' => '<', 'wplte' => '<=' );
		foreach ( $ver_operators as $ver_check => $ver_op ) {
			if ( isset( $this->config[ $opt ][ $ver_check ] )
				&& ! empty( $this->config[ $opt ][ $ver_check ] )
				&& ! version_compare( $wp_version, $this->config[ $opt ][ $ver_check ], $ver_op ) ) {
					$valid = false;
					break;
			}
		}
		return $valid;
	}

	/**
	 * Returns the list of option names.
	 *
	 * @param bool $include_non_options Optional. Should non-options be included? Default is false.
	 *
	 * @return array Array of option names.
	 */
	protected function get_option_names( $include_non_options = false ) {
		$option_names = array();

		if ( $include_non_options ) {
			$option_names = array_keys( $this->config );
		} else {
			if ( ! $this->option_names ) {
				$this->option_names = array();
				foreach ( array_keys( $this->config ) as $opt ) {
					if ( isset( $this->config[ $opt ]['input'] ) && $this->config[ $opt ]['input'] != '' && $this->config[ $opt ]['input'] != 'none' && $this->is_option_valid( $opt ) ) {
						$this->option_names[] = $opt;
					}
				}
			}
			$option_names = $this->option_names;
		}

		return $option_names;
	}

	/**
	 * Returns either the buffered array of all options for the plugin, or
	 * obtains the options and buffers the value.
	 *
	 * @param bool $with_current_values Optional. Should the currently saved values be returned? If false, then the plugin's defaults are returned. Default is true.
	 *
	 * @return array The options array for the plugin (which is also stored in $this->options if !$with_options).
	 */
	public function get_options( $with_current_values = true ) {
		if ( $with_current_values && $this->options ) {
			return $this->options;
		}
		// Derive options from the config
		$options = array();
		$option_names = $this->get_option_names( ! $with_current_values );
		foreach ( $option_names as $opt ) {
			$options[ $opt ] = $this->config[ $opt ]['default'];
		}
		if ( ! $with_current_values ) {
			return $options;
		}
		$this->options_from_db = get_option( $this->admin_options_name );
		$this->options = wp_parse_args( $this->options_from_db, $options );

		// Check to see if the plugin has been updated
		$this->check_if_plugin_was_upgraded();

		// Un-escape fields
		foreach ( $option_names as $opt ) {
			if ( $this->config[ $opt ]['allow_html'] == true ) {
				if ( is_array( $this->options[ $opt ] ) ) {
					foreach ( $this->options[ $opt ] as $key => $val ) {
						$new_key = wp_specialchars_decode( $key, ENT_QUOTES );
						$new_val = wp_specialchars_decode( $val, ENT_QUOTES );
						$this->options[ $opt ][ $new_key ] = $new_val;
						if ( $key != $new_key ) {
							unset( $this->options[ $opt ][ $key ] );
						}
					}
				} else {
					$this->options[ $opt ] = wp_specialchars_decode( $this->options[ $opt ], ENT_QUOTES );
				}
			}
		}
		return apply_filters( $this->get_hook( 'options' ), $this->options );
	}

	/**
	 * Updates the options with values specifically defined.
	 *
	 * @since 037
	 *
	 * @param array $settings   The new setting value(s)
	 * @param bool  $with_reset Should the options be reset, with the new settings overlaid on top of the default settings?
	 *
	 * @return array
	 */
	public function update_option( $settings, $with_reset = false ) {
		if ( $with_reset ) {
			$this->reset_options();
		}
		$settings = $this->sanitize_inputs( $settings );
		update_option( $this->admin_options_name, $settings );
		return $this->options = $settings;
	}

	/**
	 * Gets the name to use for a form's <input type="hidden" name="XXX" value="1" />
	 *
	 * @param string $prefix A prefix string, unique to the form.
	 *
	 * @return string The name.
	 */
	protected function get_form_submit_name( $prefix ) {
		return $prefix . '_' . $this->u_id_base;
	}

	/**
	 * Returns the URL for a plugin's form to use for its action attribute
	 *
	 * @return string The action URL
	 */
	protected function form_action_url() {
		return $_SERVER['PHP_SELF'] . '?page=' . $this->plugin_basename;
	}

	/**
	 * Checks if the plugin's settings page has been submitted.
	 *
	 * @return bool True if the plugin's settings have been submitted for saving, else false.
	 */
	protected function is_submitting_form() {
		return ( isset( $_POST['option_page'] ) && ( $_POST['option_page'] == $this->admin_options_name ) );
	}

	/**
	 * Checks if the current page is the plugin's settings page.
	 *
	 * @return bool True if on the plugin's settings page, else false.
	 */
	protected function is_plugin_admin_page() {
		return ( basename( $_SERVER['PHP_SELF'], '.php' ) == $this->settings_page && isset( $_REQUEST['page'] ) && $_REQUEST['page'] == $this->plugin_basename );
	}

	/**
	 * Outputs the markup for an option's form field (and surrounding markup)
	 *
	 * @param string $opt The name/key of the option.
	 */
	public function display_option( $opt ) {
		do_action( $this->get_hook( 'pre_display_option' ), $opt );

		$options = $this->get_options();

		foreach ( array( 'datatype', 'input' ) as $attrib ) {
			$$attrib = isset( $this->config[ $opt ][ $attrib ] ) ? $this->config[ $opt ][ $attrib ] : '';
		}

		if ( $input == '' || $input == 'none' ) {
			return;
		} elseif ( $input == 'custom' ) {
			do_action( $this->get_hook( 'custom_display_option' ), $opt );
			return;
		}
		$value = isset( $options[ $opt ] ) ? $options[ $opt ] : '';
		$popt = $this->admin_options_name . "[{$opt}]";
		if ( $input == 'multiselect' ) {
			// Do nothing since it needs the values as an array
			$popt .= '[]';
		} elseif ( $datatype == 'array' ) {
			if ( ! is_array( $value ) ) {
				$value = '';
			} else {
				if ( $input == 'textarea' || $input == 'inline_textarea' ) {
					$value = implode( "\n", $value );
				} else {
					$value = implode( ', ', $value );
				}
			}
		} elseif ( $datatype == 'hash' && $input != 'select' ) {
			if ( ! is_array( $value ) ) {
				$value = '';
			} else {
				$new_value = '';
				foreach ( $value AS $shortcut => $replacement ) {
					$new_value .= "$shortcut => $replacement\n";
				}
				$value = $new_value;
			}
		}
		$attributes = $this->config[ $opt ]['input_attributes'];
		$this->config[ $opt ]['class'][] = 'c2c-' . $input;
		if ( ( 'textarea' == $input || 'inline_textarea' == $input ) && $this->config[ $opt ]['no_wrap'] ) {
			$attributes .= ' wrap="off"'; // Does not validate, but only cross-browser technique
		}
		elseif ( in_array( $input, array( 'text', 'long_text', 'short_text' ) ) ) {
			$this->config[ $opt ]['class'][]  = ( ( $input == 'short_text' ) ? 'small-text' : 'regular-text' );
			if ( $input == 'long_text' ) {
				$this->config[ $opt ]['class'][] = ' long-text';
			}
			$input = 'text';
		}
		$class = implode( ' ', $this->config[ $opt ]['class'] );
		$attribs = "name='{$popt}' id='{$opt}' class='{$class}' {$attributes}";
		if ( $input == '' ) {
// Change of implementation prevents this from being possible (since this function only gets called for registered settings)
//			if ( !empty( $this->config[ $opt ]['output'] ) )
//				echo $this->config[ $opt ]['output'] . "\n";
//			else
//				echo '<div class="hr">&nbsp;</div>' . "\n";
		} elseif ( $input == 'textarea' || $input == 'inline_textarea' ) {
			if ( $input == 'textarea' ) {
				echo "</td><tr><td colspan='2'>";
			}
			echo "<textarea {$attribs}>{$value}</textarea>\n";
		} elseif ( $input == 'select' ) {
			echo "<select $attribs>";
			if ( $this->config[ $opt ]['datatype'] == 'hash' ) {
				foreach ( (array) $this->config[ $opt ]['options'] as $sopt => $sval ) {
					echo "<option value='{$sopt}' " . selected( $value, $sopt, false ) . ">{$sval}</option>\n";
				}
			} else {
				foreach ( (array) $this->config[ $opt ]['options'] as $sopt ) {
					echo "<option value='{$sopt}' " . selected( $value, $sopt, false ) . ">{$sopt}</option>\n";
				}
			}
			echo "</select>";
		} elseif ( $input == 'multiselect' ) {
			echo '<fieldset class="c2c-fieldset">' . "\n";
			foreach ( (array) $this->config[ $opt ]['options'] as $sopt ) {
				echo "<input type='checkbox' {$attribs} value='{$sopt}' " . checked( in_array( $sopt, $value ), true, false ) . ">{$sopt}</input><br />\n";
			}
			echo '</fieldset>';
		} elseif ( $input == 'checkbox' ) {
			echo "<input type='{$input}' {$attribs} value='1' " . checked( $value, 1, false ) . " />\n";
		} else { // Only 'text' and 'password' should fall through to here.
			echo "<input type='{$input}' {$attribs} value='" . esc_attr( $value ) . "' />\n";
		}
		if ( $help = apply_filters( $this->get_hook( 'option_help'), $this->config[ $opt ]['help'], $opt ) ) {
			echo "<p class='description'>{$help}</p>\n";
		}

		do_action( $this->get_hook( 'post_display_option' ), $opt );
	}

	/**
	 * Outputs the options page for the plugin, and saves user updates to the
	 * options.
	 */
	public function options_page() {
		$options = $this->get_options();

		if ( $this->saved_settings ) {
			echo "<div id='message' class='updated fade'><p><strong>" . $this->saved_settings_msg . '</strong></p></div>';
		}

		$logo = plugins_url( 'c2c_minilogo.png', $this->plugin_file );

		echo "<div class='wrap'>\n";

		do_action( $this->get_hook( 'before_settings_form' ), $this );

		echo "<form action='" . admin_url( 'options.php' ) . "' method='post' class='c2c-form'>\n";

		settings_fields( $this->admin_options_name );
		do_settings_sections( $this->plugin_file );

		echo '<input type="submit" name="Submit" class="button-primary" value="' . esc_attr__( 'Save Changes', 'text-hover' ) . '" />' . "\n";
		echo '<input type="submit" name="Reset" class="button" value="' . esc_attr__( 'Reset Settings', 'text-hover' ) . '" />' . "\n";
		echo '</form>' . "\n";

		do_action( $this->get_hook( 'after_settings_form' ), $this );

		echo '<div id="c2c" class="wrap"><div>' . "\n";
		$c2c = '<a href="http://coffee2code.com" title="coffee2code.com">' . __( 'Scott Reilly, aka coffee2code', 'text-hover' ) . '</a>';
		echo sprintf( __( 'This plugin brought to you by %s.', 'text-hover' ), $c2c );
		echo '<span><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522" title="' . esc_attr__( 'Please consider a donation', 'text-hover' ) . '">' .
		__( 'Did you find this plugin useful?', 'text-hover' ) . '</a></span>';
		echo '</div>' . "\n";

		echo '</div>' . "\n";
	}

	/**
	 * Returns the full plugin-specific name for a hook.
	 *
	 * @param string $hook The name of a hook, to be made plugin-specific.
	 *
	 * @return string The plugin-specific version of the hook name.
	 */
	protected function get_hook( $hook ) {
		return $this->hook_prefix . '_' . $hook;
	}

	/**
	 * Returns the URL for the plugin's readme.txt file on wordpress.org/plugins
	 *
	 * @since 005
	 *
	 * @return string The URL
	 */
	public function readme_url() {
		return 'https://wordpress.org/plugins/' . $this->id_base . '/tags/' . $this->version . '/readme.txt';
	}
} // end class

endif; // end if !class_exists()
