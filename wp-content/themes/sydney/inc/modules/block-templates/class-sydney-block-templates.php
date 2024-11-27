<?php
/**
 * Functions for block templates module
 *
 * @package Sydney
 */

if ( !Sydney_Modules::is_module_active( 'block-templates' ) ) {
	return;
}

if ( !class_exists('Sydney_Block_Templates') ) {
	class Sydney_Block_Templates {
		/**
		 * Instance
		 */		
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}    

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'customizer' ) );

			add_action( 'wp', array( $this, 'setup' ) );
		}

		/**
		 * Customizer
		 */
		public function customizer( $wp_customize ) {
			require get_template_directory() . '/inc/customizer/options/modules/block-templates.php';
		}

		/**
		 * Actions
		 */
		public function setup() {
			$config = array(
				//template type => action
				'header' 	=> 'sydney_header',
				'footer' 	=> 'sydney_footer',
				'single' 	=> 'sydney_single_content',
				'page' 		=> 'sydney_page_content',
				'archive' 	=> 'sydney_archive_content',
				'search' 	=> 'sydney_search_content',
				'404' 		=> 'sydney_404_content',
			);

			foreach ( $config as $type => $action ) {
				$this->part_setup( $type, $action );
			}
		}

		/**
		 * Header template
		 */
		public function part_setup( $type, $action ) {
			$enable = get_theme_mod( 'enable_' . $type . '_block_template', 0 );
		
			if ( $enable ) {
				remove_all_actions( $action );

				if ( ( 'single' === $type && is_single() ) || ( 'archive' === $type && is_archive() ) || ( 'search' === $type && is_search() ) ) {
					remove_action( 'sydney_get_sidebar', 'sydney_get_sidebar' );
					add_filter( 'sydney_content_area_class', function() { return 'fullwidth'; } );
				}

				add_action( $action, function() use ( $type ) {
					echo $this->do_block_template_part( $type );
				}, 10 );
			}
		}
		
		/**
		 * Print template
		 */
		public function do_block_template_part( $type ) {
			ob_start();
			block_template_part( $type );
			return ob_get_clean();
		}
	}

	/**
	 * Initialize class
	 */
	Sydney_Block_Templates::get_instance();	
}