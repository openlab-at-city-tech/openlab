<?php
/**
 * TablePress DataTables Server-side Processing.
 *
 * @package TablePress
 * @subpackage DataTables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the DataTables Server-side Processing feature.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_ServerSide_Processing {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Store for the row counts for each table, for use in `deferLoading` DataTables parameter.
	 *
	 * @since 2.0.0
	 * @var int[]
	 */
	public static $row_counts = array();

	/**
	 * Registers necessary plugin filter hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		TablePress::load_class( 'TablePress_Module_DataTables_ServerSide_Processing_REST_API', 'datatables-serverside-processing-rest-api.php', 'modules/controllers' );

		if ( is_admin() ) {
			add_filter( 'tablepress_view_data', array( __CLASS__, 'add_edit_screen_elements' ), 10, 2 );
		}

		add_filter( 'tablepress_table_template', array( __CLASS__, 'add_option_to_table_template' ) );
		add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( __CLASS__, 'add_shortcode_parameters' ) );
		add_filter( 'tablepress_table_js_options', array( __CLASS__, 'pass_render_options_to_js_options' ), 10, 3 );
		add_filter( 'tablepress_datatables_parameters', array( __CLASS__, 'set_datatables_parameters' ), 10, 4 );
		add_filter( 'tablepress_table_render_data', array( __CLASS__, 'shorten_rendered_table' ), 10, 3 );

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_css_files' ) );
		}
	}

	/**
	 * Encodes a (binary) string to base64 and replaces characters so that the output can be used without URL encoding.
	 *
	 * The output is the same as base64 encoding, but with `-` instead of `+`, `_` instead of `/`, and `=` removed.
	 *
	 * @since 2.0.4
	 *
	 * @param string $input (Binary) string that is to be encoded.
	 * @return string String that is base64 and has characters replaced so that no URL encoding is needed.
	 */
	public static function base64_url_encode( string $input ): string {
		return str_replace( array( '+', '/', '=' ), array( '-', '_', '' ), base64_encode( $input ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * Decodes a base64 string that potentiall has replaced characters back to a (binary) string.
	 *
	 * Characters `-` and `_` are replaced by `+` and `/` before decoding the base64 format.
	 *
	 * @since 2.0.4
	 *
	 * @param string $input Base64 string that potentially has replaced characters.
	 * @return string|false Base64-decoded (binary) string, or false on failure.
	 */
	public static function base64_url_decode( string $input ) /* : string|false */ {
		return base64_decode( str_replace( array( '-', '_' ), array( '+', '/' ), $input ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	}

	/**
	 * Adds options related to DataTables Server-side Processing to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['datatables_serverside_processing'] = false;
		$table['options']['datatables_serverside_processing_cached_pages'] = 0;
		$table['options']['datatables_serverside_processing_periodic_refresh'] = 0;
		return $table;
	}

	/**
	 * Registers "Edit" screen elements for the "DataTables Server-side Processing" feature.
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
			add_meta_box( 'tablepress_edit-datatables-serverside-processing', __( 'Server-side Processing', 'tablepress' ), array( __CLASS__, 'postbox_datatables_serverside_processing' ), null, 'normal', 'low' );

			TablePress_Modules_Helper::enqueue_script( 'datatables-serverside-processing' );
			add_filter( 'tablepress_admin_page_script_dependencies', array( __CLASS__, 'add_script_dependencies' ), 10, 2 );
		}
		return $data;
	}

	/**
	 * Adds DataTables Server-side Processing script as a dependency for the "Edit" script, so that hooks are added before they are executed.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $dependencies List of the dependencies that the $name script relies on.
	 * @param string   $name         Name of the JS script, without extension.
	 * @return string[] Modified list of the dependencies that the $name script relies on.
	 */
	public static function add_script_dependencies( array $dependencies, string $name ): array {
		if ( 'edit' === $name ) {
			$dependencies[] = 'tablepress-datatables-serverside-processing';
		}
		return $dependencies;
	}

	/**
	 * Prints the content of the "DataTables Server-side Processing" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public static function postbox_datatables_serverside_processing( array $data, array $box ): void {
		$help_box_content = '';
		self::print_help_box_markup( $help_box_content );
		?>
		<p id="notice-datatables-serverside-processing-requirements"><em><?php printf( __( 'This feature is only available when the “%1$s” and “%2$s” checkboxes in the “%3$s” and “%4$s” sections are checked.', 'tablepress' ), __( 'Table Head Row', 'tablepress' ), __( 'Enable Visitor Features', 'tablepress' ), __( 'Table Options', 'tablepress' ), __( 'Table Features for Site Visitors', 'tablepress' ) ); ?></em></p>
		<p id="notice-datatables-serverside-processing-conflict-datatables-advanced-loading"><em><?php printf( __( 'This feature is only available when the “%1$s” feature is turned off.', 'tablepress' ), __( 'Advanced Loading', 'tablepress' ) ); ?></em></p>
		<p><label for="option-datatables_serverside_processing"><input type="checkbox" name="datatables_serverside_processing" id="option-datatables_serverside_processing"> <?php _e( 'Load the table via the TablePress REST API.', 'tablepress' ); ?></label></p>
		<details id="tablepress-datatables_serverside_processing-advanced-settings">
			<summary><?php _e( 'Advanced settings', 'tablepress' ); ?></summary>
			<div>
				<table class="tablepress-postbox-table fixed">
					<tr class="top-border">
						<th class="column-1 top-align" scope="row"><?php _e( 'Cache', 'tablepress' ); ?>:</th>
						<td class="column-2">
							<label for="option-datatables_serverside_processing_cached_pages"><?php printf( __( 'Cache %s pages to reduce requests to the server.', 'tablepress' ), '<input type="number" name="datatables_serverside_processing_cached_pages" id="option-datatables_serverside_processing_cached_pages" class="small-text" min="0" max="10" required>' ); ?></label>
						</td>
					</tr>
					<tr class="top-border">
						<th class="column-1 top-align" scope="row"><?php _e( 'Periodic Refresh', 'tablepress' ); ?>:</th>
						<td class="column-2">
							<label for="option-datatables_serverside_processing_periodic_refresh"><?php printf( __( 'Automatically reload the table data every %s seconds.', 'tablepress' ), '<input type="number" name="datatables_serverside_processing_periodic_refresh" id="option-datatables_serverside_processing_periodic_refresh" class="small-text" min="0" required>' ); ?></label>
							<p class="description"><?php _e( 'This allows site visitors to stay on the page while always getting the latest data, which is useful for often updated tables, like race result tables or a leader board.', 'tablepress' ); ?> <?php _e( 'The refresh interval should be chosen reasonably big, to not overload the server.', 'tablepress' ); ?></p>
						</td>
					</tr>
				</table>
			</div>
		</details>
		<p id="notice-datatables-serverside-processing-note"><em><?php _e( 'Please note that features like “Column Filter Dropdowns”, Custom Search Builder”, “Individual Column Filtering”, “Alphabet Search”, “Fuzzy Search”, “Search Panes”, “Cell Highlighting”, and “Row Highlighting” are not compatible with Server-side Processing.', 'tablepress' ); ?></em></p>
		<?php
	}

	/**
	 * Adds parameters for the DataTables Server-side Processing feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default attributes for the TablePress [table /] Shortcode.
	 * @return array<string, mixed> Extended attributes for the Shortcode.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['datatables_serverside_processing'] = null;
		$default_atts['datatables_serverside_processing_cached_pages'] = null;
		$default_atts['datatables_serverside_processing_periodic_refresh'] = null;
		$default_atts['datatables_serverside_processing_request_type'] = 'GET'; // This is only a Shortcode parameter, but not part of the UI.
		$default_atts['datatables_serverside_processing_html_rows'] = ''; // This is only a Shortcode parameter, but not part of the UI, as, by default, the "datatables_paginate_entries" value is used.
		return $default_atts;
	}

	/**
	 * Passes the DataTables Server-side Processing configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $js_options     Current JS options.
	 * @param string               $table_id       Table ID.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified JS options.
	 */
	public static function pass_render_options_to_js_options( array $js_options, string $table_id, array $render_options ): array {
		if ( empty( $render_options['datatables_serverside_processing'] ) ) {
			return $js_options;
		}

		$js_options['datatables_serverside_processing'] = $render_options['datatables_serverside_processing'];
		$js_options['datatables_serverside_processing_cached_pages'] = absint( $render_options['datatables_serverside_processing_cached_pages'] );
		$js_options['datatables_serverside_processing_periodic_refresh'] = absint( $render_options['datatables_serverside_processing_periodic_refresh'] );
		$js_options['datatables_serverside_processing_request_type'] = $render_options['datatables_serverside_processing_request_type'];

		$render_options_ssp = array(
			'id',
			'cache_table_output',
			'convert_line_breaks',
			'evaluate_formulas',
			'hide_columns',
			'hide_rows',
			'show_columns',
			'show_rows',
			'table_head',
			'table_foot',
		);

		/**
		 * Filters the list of render option keys that are passed as a request parameter in the AJAX call to the Server-side Processing REST API endpoint.
		 *
		 * This can be used to make other render options available to e.g. other filter hooks in the Render class.
		 *
		 * @since 2.0.4
		 *
		 * @param string[]             $render_options_ssp Render Options list for Server-side Processing.
		 * @param string               $table_id           Table ID.
		 * @param array<string, mixed> $render_options     Render Options.
		 * @return string[] Modified Render Options list for Server-side Processing.
		 */
		$render_options_ssp = apply_filters( 'tablepress_datatables_serverside_processing_render_options', $render_options_ssp, $table_id, $render_options );

		$request_render_options = array_intersect_key( $render_options, array_flip( $render_options_ssp ) );
		$js_options['encrypted_render_options'] = self::encrypt_render_options( $request_render_options );

		if ( 0 !== $js_options['datatables_serverside_processing_cached_pages'] ) {
			$js_url = plugins_url( 'modules/js/datatables.serverside-processing.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-serverside-processing', $js_url, array( 'tablepress-datatables' ), TablePress::version, true );
		}

		return $js_options;
	}

	/**
	 * Encrypts and encodes a Render Options array.
	 *
	 * @since 2.0.0
	 * @param array<string, mixed> $render_options Render options.
	 * @return array{request: string, nonce: string} URL-safe, base64-encoded encryted render options and nonce.
	 */
	protected static function encrypt_render_options( array $render_options ): array {
		$message = wp_json_encode( $render_options, TABLEPRESS_JSON_OPTIONS );
		$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$secret_key = sodium_crypto_generichash( wp_salt( 'nonce' ), '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );

		$ciphertext = sodium_crypto_secretbox( $message, $nonce, $secret_key ); // @phpstan-ignore-line

		return array(
			'request' => self::base64_url_encode( $ciphertext ),
			'nonce'   => self::base64_url_encode( $nonce ),
		);
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
		if ( empty( $js_options['datatables_serverside_processing'] ) ) {
			return $parameters;
		}

		$parameters['serverSide'] = '"serverSide":true';
		$parameters['processing'] = '"processing":true';

		$table_rest_url = get_rest_url( null, "/tablepress/v1/ssp/{$table_id}" );
		$table_rest_url = add_query_arg( 'r', $js_options['encrypted_render_options']['request'], $table_rest_url );
		$table_rest_url = add_query_arg( 'n', $js_options['encrypted_render_options']['nonce'], $table_rest_url );
		if ( is_user_logged_in() ) {
			$table_rest_url = add_query_arg( '_wpnonce', wp_create_nonce( 'wp_rest' ), $table_rest_url );
		}
		$table_rest_url = "\"{$table_rest_url}\"";
		if ( 'POST' === $js_options['datatables_serverside_processing_request_type'] ) {
			$parameters['ajax'] = "\"ajax\":{\"url\":{$table_rest_url},\"type\":\"POST\"}";
		} else {
			// Using Pipeplining is only possible with the "GET" request type.
			if ( 0 < $js_options['datatables_serverside_processing_cached_pages'] ) {
				$pages = '';
				if ( 5 !== $js_options['datatables_serverside_processing_cached_pages'] ) {
					$pages = ",\"pages\":{$js_options['datatables_serverside_processing_cached_pages']}";
				}
				$table_rest_url = "$.fn.dataTable.pipeline({\"url\":{$table_rest_url}{$pages}})";
			}

			$parameters['ajax'] = "\"ajax\":{$table_rest_url}";
		}

		if ( isset( self::$row_counts[ $html_id ] ) ) {
			$row_count = self::$row_counts[ $html_id ];
			$parameters['deferLoading'] = "\"deferLoading\":{$row_count}";
		}

		// Add an option to the "search" value, if one is already set, otherwise set it.
		$search_sub_option = '"return":true';
		if ( isset( $parameters['search'] ) ) {
			$parameters['search'] = str_replace( '"search":{', "\"search\":{{$search_sub_option},", $parameters['search'] );
		} else {
			$parameters['search'] = "\"search\":{{$search_sub_option}}";
		}

		if ( 0 < $js_options['datatables_serverside_processing_periodic_refresh'] ) {
			$interval = 1000 * $js_options['datatables_serverside_processing_periodic_refresh'];
			$parameters['initComplete'] = "\"initComplete\":function(){setInterval(()=>this.api().ajax.reload(null,false),{$interval});}";
		}

		return $parameters;
	}

	/**
	 * Enqueues CSS files for the DataTables Server-side Processing module.
	 *
	 * @since 2.1.3
	 */
	public static function enqueue_css_files(): void {
		/** This filter is documented in modules/controllers/datatables-alphabetsearch.php */
		if ( ! apply_filters( 'tablepress_module_enqueue_css_files', true, self::$module['slug'] ) ) {
			return;
		}

		$css_url = plugins_url( 'modules/css/build/datatables.serverside-processing.css', TABLEPRESS__FILE__ );
		wp_enqueue_style( 'tablepress-datatables-serverside-processing', $css_url, array( 'tablepress-default' ), TablePress::version );
	}

	/**
	 * Attaches the full render data to a shortened copy of the render data.
	 *
	 * The shortened version will be rendered as HTML while the full data will be fetched via the REST API.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table          The table.
	 * @param array<string, mixed> $orig_table     The unmodified table.
	 * @param array<string, mixed> $render_options Render options.
	 * @return array<string, mixed> The modified table.
	 */
	public static function shorten_rendered_table( array $table, array $orig_table, array $render_options ): array {
		if ( empty( $render_options['datatables_serverside_processing'] ) ) {
			return $table;
		}

		if ( empty( $render_options['datatables_serverside_processing_html_rows'] ) ) {
			$render_options['datatables_serverside_processing_html_rows'] = $render_options['datatables_paginate_entries'];
		}

		// Store a copy of the full data, which will be printed as JS.
		$full_table_data = $table['data'];

		// Cut out a limited number of body rows for HTML rendering.
		$length = max( 1, absint( $render_options['datatables_serverside_processing_html_rows'] ) ); // Require at least one tbody row.
		if ( $render_options['table_head'] ) {
			++$length;
		}
		$table['data'] = array_slice( $table['data'], 0, $length );
		$last_row_idx = count( $full_table_data ) - 1;
		if ( $render_options['table_foot'] && $length <= $last_row_idx ) {
			$table['data'][] = $full_table_data[ $last_row_idx ];
		}

		$row_count = count( $full_table_data );
		if ( $render_options['table_head'] ) {
			--$row_count;
		}
		if ( $render_options['table_foot'] ) {
			--$row_count;
		}
		self::$row_counts[ $render_options['html_id'] ] = $row_count;

		return $table;
	}

} // class TablePress_Module_DataTables_ServerSide_Processing
