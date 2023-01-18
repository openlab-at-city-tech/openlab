<?php
namespace ElementsKit_Lite\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Inline script registrar.
 * 
 * Returns all necessary inline js & css.
 * 
 * @since 1.0.0
 * @access public
 */
class Build_Inline_Scripts {

	use \ElementsKit_Lite\Traits\Singleton;

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_js' ) );
		add_action( 'admin_print_scripts', array( $this, 'admin_js' ) );
	}


	// scripts for common end, admin & frontend
	public function common_js() {
		ob_start(); ?>

		var elementskit = {
			resturl: '<?php echo defined( 'ICL_SITEPRESS_VERSION' ) ? esc_url(home_url('/wp-json/elementskit/v1/')) : esc_url(get_rest_url() . 'elementskit/v1/'); ?>',
		}

		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}



	// scripts for frontend
	public function frontend_js() {
		$js = $this->common_js();
		wp_add_inline_script( 'elementskit-framework-js-frontend', $js );
	}


	// scripts for admin
	public function admin_js() {
		echo "<script type='text/javascript'>\n";
		echo \ElementsKit_Lite\Utils::render( $this->common_js() );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content already escaped in common_js() method
		echo "\n</script>";
	}
}
