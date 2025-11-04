<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class used to manage block bindings for the theme.
 *
 * @link       https://acmeit.com/
 * @since      1.0.0
 * Requires at least: 6.5.0
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/Patterns_Docs_Block_Bindings
 */

/**
 * Block bindings for the theme.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/Patterns_Docs_Block_Bindings
 * @author     codersantosh <codersantosh@gmail.com>
 */

if ( ! class_exists( 'Patterns_Docs_Block_Bindings' ) ) {

	/**
	 * Patterns_Docs_Block_Bindings
	 *
	 * @package Patterns_Docs
	 * @since 1.0.0
	 */
	class Patterns_Docs_Block_Bindings {

		/**
		 * Initialize the class and set up actions.
		 * Register block bindings
		 *
		 * @access public
		 * @return void
		 */
		public function run() {
			/*Register block bindings*/
			add_action( 'init', array( $this, 'register_block_bindings' ) );
		}

		/**
		 * Registers copyright source in the block bindings registry.
		 *
		 * @since    1.0.0
		 */
		public function register_block_bindings() {
			if ( ! function_exists( 'register_block_bindings_source' ) ) {
				return;
			}

			/* Register the copyright block binding source. */
			register_block_bindings_source(
				'patterns-docs/copyright',
				array(
					'label'              => _x( '&copy; YEAR', 'Label for the copyright placeholder in the editor', 'patterns-docs' ),
					'get_value_callback' => array( $this, 'get_binding_data' ),
				)
			);

			/* Register the archive-title block binding source. */
			register_block_bindings_source(
				'patterns-docs/archive-title',
				array(
					'label'              => _x( 'Archive title', 'Label for the archive title placeholder in the editor', 'patterns-docs' ),
					'get_value_callback' => array( $this, 'get_binding_data' ),
				)
			);
		}

		/**
		 * Gets value of binding data.
		 *
		 * @since 1.0.0
		 *
		 * @param array    $source_args    Array containing source arguments used to look up the override value.
		 *                                 Example: array( "key" => "copyright" ).
		 * @param WP_Block $block_instance The block instance.
		 * @return mixed The value computed for the source.
		 */
		public function get_binding_data( array $source_args, $block_instance ) {
			if ( empty( $source_args['key'] ) ) {
				return null;
			}

			$binding_data = null;

			if ( 'copyright' === $source_args['key'] ) {
				/* translators: Copyright date format, see https://www.php.net/manual/datetime.format.php */
				$date_format  = _x( 'Y', 'copyright date format', 'patterns-docs' );
				$binding_data = sprintf(
					/* translators: 1: Copyright symbol or word, 2: Date, 3: Site title */
					__( '%1$s %2$s %3$s', 'patterns-docs' ),
					'&copy;',
					wp_date( $date_format ),
					get_bloginfo( 'name' )
				);
			} elseif ( 'archive-title' === $source_args['key'] ) {
				if ( is_archive() ) {
					$binding_data = get_the_archive_title();
				} elseif ( is_search() ) {
					$binding_data = sprintf(
						/* translators: %s is the search term. */
						__( 'Search results for: "%s"', 'patterns-docs' ),
						get_search_query()
					);
				} elseif ( is_home() ) {
					$binding_data = __( 'Blog', 'patterns-docs' );
				}
			}

			return apply_filters( 'patterns_docs_binding_get_binding_data', $binding_data, $source_args, $block_instance );
		}

		/**
		 * Gets an instance of this object.
		 * Prevents duplicate instances which avoid artefacts and improves performance.
		 *
		 * @static
		 * @access public
		 * @return object
		 * @since 1.0.0
		 */
		public static function get_instance() {
			// Store the instance locally to avoid private static replication.
			static $instance = null;

			// Only run these methods if they haven't been ran previously.
			if ( null === $instance ) {
				$instance = new self();
			}

			// Always return the instance.
			return $instance;
		}
	}
}

/**
 * Return instance of  Patterns_Docs_Block_Bindings class
 *
 * @since 1.0.0
 *
 * @return Patterns_Docs_Block_Bindings
 */
function patterns_docs_block_bindings() { //phpcs:ignore
	return Patterns_Docs_Block_Bindings::get_instance();
}
patterns_docs_block_bindings()->run();
