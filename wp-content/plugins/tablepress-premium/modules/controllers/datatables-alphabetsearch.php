<?php
/**
 * TablePress DataTables AlphabetSearch.
 *
 * @package TablePress
 * @subpackage DataTables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the DataTables AlphabetSearch feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_AlphabetSearch {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Registers necessary plugin filter hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'tablepress_view_data', array( __CLASS__, 'add_edit_screen_elements' ), 10, 2 );
		}

		add_filter( 'tablepress_table_template', array( __CLASS__, 'add_option_to_table_template' ) );
		add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( __CLASS__, 'add_shortcode_parameters' ) );
		add_filter( 'tablepress_table_js_options', array( __CLASS__, 'pass_render_options_to_js_options' ), 10, 3 );
		add_filter( 'tablepress_datatables_parameters', array( __CLASS__, 'set_datatables_parameters' ), 10, 4 );
		add_filter( 'tablepress_datatables_language_strings', array( __CLASS__, 'add_datatables_language_strings' ), 9, 2 ); // Run at priority 9 so that overriding is easier on default priority.

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_css_files' ) );
		}
	}

	/**
	 * Adds options related to DataTables AlphabetSearch to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['datatables_alphabetsearch'] = false;
		$table['options']['datatables_alphabetsearch_column'] = '1';
		$table['options']['datatables_alphabetsearch_alphabet'] = 'latin';
		$table['options']['datatables_alphabetsearch_numbers'] = false;
		$table['options']['datatables_alphabetsearch_letters'] = true;
		$table['options']['datatables_alphabetsearch_case_sensitive'] = false;
		return $table;
	}

	/**
	 * Registers "Edit" screen elements for the "DataTables AlphabetSearch" feature.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data   Data for this screen.
	 * @param string               $action Action for this screen.
	 * @return array<string, mixed> Modified data for this screen.
	 */
	public static function add_edit_screen_elements( array $data, string $action ): array {
		if ( 'edit' === $action ) {
			// Add a meta box below the default meta boxes, by using the "low" priority.
			add_meta_box( 'tablepress_edit-datatables-alphabetsearch', __( 'Alphabet Search', 'tablepress' ), array( __CLASS__, 'postbox_datatables_alphabetsearch' ), null, 'normal', 'low' );

			TablePress_Modules_Helper::enqueue_script( 'datatables-alphabetsearch' );
			add_filter( 'tablepress_admin_page_script_dependencies', array( __CLASS__, 'add_script_dependencies' ), 10, 2 );
		}
		return $data;
	}

	/**
	 * Adds DataTables AlphabetSearch script as a dependency for the "Edit" script, so that hooks are added before they are executed.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $dependencies List of the dependencies that the $name script relies on.
	 * @param string   $name         Name of the JS script, without extension.
	 * @return string[] Modified list of the dependencies that the $name script relies on.
	 */
	public static function add_script_dependencies( array $dependencies, string $name ): array {
		if ( 'edit' === $name ) {
			$dependencies[] = 'tablepress-datatables-alphabetsearch';
		}
		return $dependencies;
	}

	/**
	 * Prints the content of the "DataTables AlphabetSearch" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public static function postbox_datatables_alphabetsearch( array $data, array $box ): void {
		$help_box_content = '';
		self::print_help_box_markup( $help_box_content );
		?>
		<p id="notice-datatables-alphabetsearch-requirements"><em><?php printf( __( 'This feature is only available when the “%1$s”, “%2$s”, and “%3$s” checkboxes in the “%4$s” and “%5$s” sections are checked.', 'tablepress' ), __( 'Table Head Row', 'tablepress' ), __( 'Enable Visitor Features', 'tablepress' ), __( 'Search/Filtering', 'tablepress' ), __( 'Table Options', 'tablepress' ), __( 'Table Features for Site Visitors', 'tablepress' ) ); ?></em></p>
		<p id="notice-datatables-alphabetsearch-conflict-datatables-serverside-processing"><em><?php printf( __( 'This feature is only available when the “%1$s” feature is turned off.', 'tablepress' ), __( 'Server-side Processing', 'tablepress' ) ); ?></em></p>
		<p><label for="option-datatables_alphabetsearch"><input type="checkbox" name="datatables_alphabetsearch" id="option-datatables_alphabetsearch"> <?php _e( 'Show an alphabet for searching a column by first letter.', 'tablepress' ); ?></label></p>
		<details id="tablepress-datatables_alphabetsearch-advanced-settings">
			<summary><?php _e( 'Advanced settings', 'tablepress' ); ?></summary>
			<div>
				<table class="tablepress-postbox-table fixed">
					<tr>
						<th class="column-1" scope="row"><label for="option-datatables_alphabetsearch_column"><?php _e( 'Search column', 'tablepress' ); ?>:</label></th>
						<td class="column-2">
							<input type="text" name="datatables_alphabetsearch_column" id="option-datatables_alphabetsearch_column" class="small-text" title="<?php esc_attr_e( 'This field can only contain letters and numbers.', 'tablepress' ); ?>" pattern="[0-9A-Z]+">
						</td>
					</tr>
					<tr>
						<th class="column-1" scope="row"><?php _e( 'Alphabet', 'tablepress' ); ?>:</th>
						<td class="column-2"><label for="option-datatables_alphabetsearch_alphabet-latin"><input type="radio" name="datatables_alphabetsearch_alphabet" value="latin" id="option-datatables_alphabetsearch_alphabet-latin"> <?php _e( 'Latin alphabet (A-Z)', 'tablepress' ); ?></label>&nbsp;&nbsp;&nbsp;<label for="option-datatables_alphabetsearch_alphabet-greek"><input type="radio" name="datatables_alphabetsearch_alphabet" value="greek" id="option-datatables_alphabetsearch_alphabet-greek"> <?php _e( 'Greek alphabet (Α-Ω)', 'tablepress' ); ?></label></td>
					</tr>
					<tr>
						<th class="column-1" scope="row"><?php _e( 'Show letters', 'tablepress' ); ?>:</th>
						<td class="column-2"><label for="option-datatables_alphabetsearch_letters"><input type="checkbox" name="datatables_alphabetsearch_letters" id="option-datatables_alphabetsearch_letters"> <?php _e( 'Show letters.', 'tablepress' ); ?></label></td>
					</tr>
					<tr>
						<th class="column-1" scope="row"><?php _e( 'Show numbers', 'tablepress' ); ?>:</th>
						<td class="column-2"><label for="option-datatables_alphabetsearch_numbers"><input type="checkbox" name="datatables_alphabetsearch_numbers" id="option-datatables_alphabetsearch_numbers"> <?php _e( 'Show numbers.', 'tablepress' ); ?></label></td>
					</tr>
					<tr>
						<th class="column-1" scope="row"><?php _e( 'Case sensitivity', 'tablepress' ); ?>:</th>
						<td class="column-2"><label for="option-datatables_alphabetsearch_case_sensitive"><input type="checkbox" name="datatables_alphabetsearch_case_sensitive" id="option-datatables_alphabetsearch_case_sensitive"> <?php _e( 'Treat upper and lower case letters separately.', 'tablepress' ); ?></label></td>
					</tr>
				</table>
			</div>
		</details>
		<?php
	}

	/**
	 * Adds parameters for the DataTables AlphabetSearch feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default attributes for the TablePress [table /] Shortcode.
	 * @return array<string, mixed> Extended attributes for the Shortcode.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['datatables_alphabetsearch'] = null;
		$default_atts['datatables_alphabetsearch_column'] = null;
		$default_atts['datatables_alphabetsearch_alphabet'] = null;
		$default_atts['datatables_alphabetsearch_numbers'] = null;
		$default_atts['datatables_alphabetsearch_letters'] = null;
		$default_atts['datatables_alphabetsearch_case_sensitive'] = null;
		return $default_atts;
	}

	/**
	 * Passes the DataTables AlphabetSearch configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $js_options     Current JS options.
	 * @param string               $table_id       Table ID.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified JS options.
	 */
	public static function pass_render_options_to_js_options( array $js_options, string $table_id, array $render_options ): array {
		$js_options['datatables_alphabetsearch'] = $render_options['datatables_alphabetsearch'] && $render_options['datatables_filter'];

		// Alphabet Search is not supported with Server-side Processing.
		if ( isset( $render_options['datatables_serverside_processing'] ) && $render_options['datatables_serverside_processing'] ) {
			$js_options['datatables_alphabetsearch'] = false;
		}

		if ( false !== $js_options['datatables_alphabetsearch'] ) {
			$js_options['datatables_alphabetsearch_column'] = $render_options['datatables_alphabetsearch_column'];
			$js_options['datatables_alphabetsearch_alphabet'] = $render_options['datatables_alphabetsearch_alphabet'];
			$js_options['datatables_alphabetsearch_numbers'] = $render_options['datatables_alphabetsearch_numbers'];
			$js_options['datatables_alphabetsearch_letters'] = $render_options['datatables_alphabetsearch_letters'];
			$js_options['datatables_alphabetsearch_case_sensitive'] = $render_options['datatables_alphabetsearch_case_sensitive'];

			$js_url = plugins_url( 'modules/js/datatables.alphabetsearch.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-alphabetsearch', $js_url, array( 'tablepress-datatables' ), TablePress::version, true );
		}

		return $js_options;
	}

	/**
	 * Evaluates JS parameters and converts them to DataTables parameters.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $parameters DataTables parameters.
	 * @param string               $table_id   Table ID.
	 * @param string               $html_id    HTML ID of the table.
	 * @param array<string, mixed> $js_options JS options for DataTables.
	 * @return array<string, mixed> Extended DataTables parameters.
	 */
	public static function set_datatables_parameters( array $parameters, string $table_id, string $html_id, array $js_options ): array {
		if ( ! empty( $js_options['datatables_alphabetsearch'] ) ) {
			// Prepend "A" to the "dom" value, if one is already set, otherwise use the default.
			if ( isset( $parameters['dom'] ) ) {
				$parameters['dom'] = str_replace( ':"', ':"A', $parameters['dom'] );
			} else {
				$parameters['dom'] = '"dom":"Alfrtip"';
			}

			$alphabet = array();

			$column = trim( $js_options['datatables_alphabetsearch_column'] );
			if ( ! is_numeric( $column ) ) {
				$column = TablePress::letter_to_number( $column );
			}
			$column = (int) $column;
			if ( 1 !== $column ) {
				$alphabet['column'] = $column - 1; // Zero-based counting.
			}
			if ( 'greek' === $js_options['datatables_alphabetsearch_alphabet'] ) {
				$alphabet['alphabet'] = 'greek';
			}
			if ( false !== $js_options['datatables_alphabetsearch_numbers'] ) {
				$alphabet['numbers'] = true;
			}
			if ( true !== $js_options['datatables_alphabetsearch_letters'] ) {
				$alphabet['letters'] = false;
			}
			if ( false !== $js_options['datatables_alphabetsearch_case_sensitive'] ) {
				$alphabet['caseSensitive'] = true;
			}

			if ( ! empty( $alphabet ) ) {
				$parameters['alphabet'] = '"alphabet":' . wp_json_encode( $alphabet, JSON_FORCE_OBJECT | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES );
			}
		}
		return $parameters;
	}

	/**
	 * Adds strings that the module uses on the frontend to the DataTables language array.
	 *
	 * @since 2.1.0
	 *
	 * @param array<string, string|mixed[]> $datatables_strings The language strings for DataTables.
	 * @param string                        $datatables_locale  Current locale/language for the DataTables JS library.
	 * @return array<string, string|mixed[]> Extended array of strings for DataTables.
	 */
	public static function add_datatables_language_strings( array $datatables_strings, string $datatables_locale ): array {
		if ( 'en_US' === $datatables_locale ) {
			return $datatables_strings;
		}

		TablePress_Modules_Loader::load_language_file();

		$new_strings = array(
			'alphabetsearch' => array(
				'search' => _x( 'Search: ', 'AlphabetSearch module', 'tablepress' ),
				'none'   => _x( 'None', 'AlphabetSearch module', 'tablepress' ),
			),
		);
		// Merge existing strings into the new strings, so that existing translations are not lost.
		$datatables_strings = array_replace_recursive( $new_strings, $datatables_strings );

		return $datatables_strings;
	}

	/**
	 * Enqueues CSS files for the DataTables AlphabetSearch module.
	 *
	 * @since 2.0.0
	 */
	public static function enqueue_css_files(): void {
		/**
		 * Filters whether the module's frontend CSS files should be enqueued.
		 *
		 * This allows for conditionally loading these files only on desired pages.
		 *
		 * @since 2.0.0
		 *
		 * @param bool   $enqueue     Whether the CSS files for the module should be enqueued.
		 * @param string $module_slug The module's slug.
		 */
		if ( ! apply_filters( 'tablepress_module_enqueue_css_files', true, self::$module['slug'] ) ) {
			return;
		}

		$css_url = plugins_url( 'modules/css/build/datatables.alphabetsearch.css', TABLEPRESS__FILE__ );
		wp_enqueue_style( 'tablepress-datatables-alphabetsearch', $css_url, array( 'tablepress-default' ), TablePress::version );
	}

} // class TablePress_Module_DataTables_AlphabetSearch
