<?php
/*
Plugin Name: TablePress Extension: Responsive Tables
Plugin URI: https://tablepress.org/extensions/responsive-tables/
Description: Extension for TablePress that adds several modes for responsiveness of tables
Version: 1.5
Author: Tobias Bäthge
Author URI: https://tobias.baethge.com/
*/

/*
 * See https://datatables.net/extensions/responsive/
 * Flip and Scroll mode inspired by http://dbushell.com/demos/tables/rt_05-01-12.html
 * /

/*
 * Modern use:
 * Shortcode: [table id=1 responsive="scroll" /]
 * Shortcode: [table id=1 responsive="collapse" /]
 * Shortcode: [table id=1 responsive="flip" responsive_breakpoint="phone" /] (from 'phone', 'tablet', 'desktop', 'all')
 *
 * Legacy use:
 * Shortcode: [table id=1 responsive="tablet" /]
 * The parameter "responsive" (from 'phone', 'tablet', 'desktop', 'all') determines the largest device that shall show the responsive version of the table.
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

// Init TablePress_Responsive_Tables.
add_action( 'tablepress_run', array( 'TablePress_Responsive_Tables', 'init' ) );
TablePress_Responsive_Tables::init_update_checker();

/**
 * TablePress Extension: Responsive Tables
 * @author Tobias Bäthge
 * @since 1.3
 */
class TablePress_Responsive_Tables {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 * @since 1.3
	 */
	protected static $slug = 'tablepress-responsive-tables';

	/**
	 * Plugin version.
	 *
	 * @var string
	 * @since 1.3
	 */
	protected static $version = '1.5';

	/**
	 * Instance of the Plugin Update Checker class.
	 *
	 * @var PluginUpdateChecker
	 * @since 1.3
	 */
	protected static $plugin_update_checker;

	/**
	 * Initialize the plugin by registering necessary plugin filters and actions.
	 *
	 * @since 1.3
	 */
	public static function init() {
		add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( __CLASS__, 'shortcode_table_default_shortcode_atts' ) );
		add_filter( 'tablepress_table_render_options', array( __CLASS__, 'table_render_options' ), 10, 2 );
		add_filter( 'tablepress_table_js_options', array( __CLASS__, 'table_js_options' ), 10, 3 );
		add_filter( 'tablepress_datatables_parameters', array( __CLASS__, 'datatables_parameters' ), 10, 4 );
		add_filter( 'tablepress_table_output', array( __CLASS__, 'table_output' ), 10, 3 );
		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_css_files' ) );
			add_action( 'wp_print_scripts', array( __CLASS__, 'enqueue_css_files_flip' ) );
		}
	}

	/**
	 * Load and initialize the plugin update checker.
	 *
	 * @since 1.3
	 */
	public static function init_update_checker() {
		require_once dirname( __FILE__ ) . '/libraries/plugin-update-checker.php';
		self::$plugin_update_checker = PucFactory::buildUpdateChecker(
			'https://tablepress.org/downloads/extensions/update-check/' . self::$slug . '.json',
			__FILE__,
			self::$slug
		);
	}

	/**
	 * Add "responsive" and related parameters to the [table /] Shortcode.
	 *
	 * @since 1.3
	 *
	 * @param array $default_atts Default Shortcode attributes.
	 * @return array Extended Shortcode attributes.
	 */
	public static function shortcode_table_default_shortcode_atts( $default_atts ) {
		$default_atts['responsive'] = '';
		$default_atts['responsive_breakpoint'] = 'phone'; // 'phone', 'tablet', 'desktop', 'all'
		return $default_atts;
	}

	/**
	 * Enqueue CSS files with the responsive CSS for the scroll and collapse modes.
	 *
	 * @since 1.3
	 */
	public static function enqueue_css_files() {
		if ( ! apply_filters( 'tablepress_responsive_tables_enqueue_css_file', true ) ) {
			return;
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$url = plugins_url( "css/responsive.dataTables{$suffix}.css", __FILE__ );
		wp_enqueue_style( self::$slug, $url, array(), self::$version );
	}

	/**
	 * Enqueue the CSS file with the responsive CSS for the flip mode.
	 *
	 * @since 1.3
	 */
	public static function enqueue_css_files_flip() {
		if ( ! apply_filters( 'tablepress_responsive_tables_enqueue_flip_css_file', true ) ) {
			return;
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$url = plugins_url( "css/tablepress-responsive-flip{$suffix}.css", __FILE__ );
		wp_enqueue_style( self::$slug . '-flip', $url, array( 'tablepress-default' ), self::$version );
		// Wrap the <link> tag in a conditional comment to only use the CSS in non-IE browsers.
		echo "<!--[if !IE]><!-->\n";
		wp_print_styles( self::$slug . '-flip' );
		echo "<!--<![endif]-->\n";
	}

	/**
	 * Evaluate the responsiveness mode and set required parameters.
	 *
	 * @since 1.3
	 *
	 * @param array $render_options Render Options.
	 * @param array $table          Table.
	 * @return array Modified Render Options.
	 */
	public static function table_render_options( $render_options, $table ) {
		$render_options['responsive'] = strtolower( $render_options['responsive'] );
		$render_options['responsive_breakpoint'] = strtolower( $render_options['responsive_breakpoint'] );

		// Convert legacy parameter values to modern Shortcode parameters.
		if ( in_array( $render_options['responsive'], array( 'phone', 'tablet', 'desktop', 'all' ), true ) ) {
			$render_options['responsive_breakpoint'] = $render_options['responsive'];
			$render_options['responsive'] = 'flip';
		}

		// Scroll mode.
		if ( 'scroll' === $render_options['responsive'] ) {
			// Horizontal Scrolling from DataTables has to be turned off.
			$render_options['datatables_scrollx'] = false;
		}

		// Flip mode.
		if ( 'flip' === $render_options['responsive'] && in_array( $render_options['responsive_breakpoint'], array( 'phone', 'tablet', 'desktop', 'all' ), true ) ) {
			// Horizontal Scrolling from DataTables has to be turned off.
			$render_options['datatables_scrollx'] = false;

			// Add "Extra CSS class".
			if ( '' !== $render_options['extra_css_classes'] ) {
				$render_options['extra_css_classes'] .= ' ';
			}
			$render_options['extra_css_classes'] .= "tablepress-responsive-{$render_options['responsive_breakpoint']}";
		}

		// DataTables Responsive Collapse/Row Details mode.
		if ( 'collapse' === $render_options['responsive'] ) {
			// DataTables and with that the Header row must be turned on for DataTables Responsive being usable.
			$render_options['use_datatables'] = true;
			$render_options['table_head'] = true;
		}

		return $render_options;
	}

	/**
	 * Pass configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 1.3
	 *
	 * @param array  $js_options     Current JS options.
	 * @param string $table_id       Table ID.
	 * @param array  $render_options Render Options.
	 * @return array Modified JS options.
	 */
	public static function table_js_options( $js_options, $table_id, $render_options ) {
		$js_options['responsive'] = $render_options['responsive'];

		// Change parameters and register files for the collapse mode.
		if ( 'collapse' === $js_options['responsive'] ) {
			// Register the JS files.
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			$js_responsive_url = plugins_url( "js/dataTables.responsive{$suffix}.js", __FILE__ );
			wp_enqueue_script( self::$slug, $js_responsive_url, array( 'tablepress-datatables' ), self::$version, true );
		}

		return $js_options;
	}

	/**
	 * Evaluate JS parameters and convert them to DataTables parameters.
	 *
	 * @since 1.3
	 *
	 * @param array  $parameters DataTables parameters.
	 * @param string $table_id   Table ID.
	 * @param string $html_id    HTML ID of the table.
	 * @param array  $js_options JS options for DataTables.
	 * @return array Extended DataTables parameters.
	 */
	public static function datatables_parameters( $parameters, $table_id, $html_id, $js_options ) {
		// DataTables Responsive Collapse/Row Details mode.
		if ( 'collapse' === $js_options['responsive'] ) {
			$parameters['responsive'] = '"responsive":true';
		}

		return $parameters;
	}

	/**
	 * Possibly add extra HTML code around the table element.
	 *
	 * @since 1.3
	 *
	 * @param string $output         Table HTML code.
	 * @param array  $table          The table.
	 * @param array  $render_options Render Options.
	 * @return string Modified/extended table HTML code.
	 */
	public static function table_output( $output, $table, $render_options ) {
		// Horizontal Scrolling mode.
		if ( 'scroll' === $render_options['responsive'] ) {
			$output = "<div id=\"{$render_options['html_id']}-scroll-wrapper\" class=\"tablepress-scroll-wrapper\">\n{$output}\n</div>";
		}

		return $output;
	}

} // class TablePress_Responsive_Tables
