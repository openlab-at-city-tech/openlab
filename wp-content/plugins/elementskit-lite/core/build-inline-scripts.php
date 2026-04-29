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

	public function __construct() {
		// Frontend + Admin scripts
		add_action( 'wp_print_scripts', array( $this, 'print_inline_script' ) );
	}

	/**
	 * Get common inline JavaScript.
	 *
	 * @return string
	 */
	public function common_js() {
		ob_start(); ?>

		var elementskit = {
			resturl: '<?php echo defined( 'ICL_SITEPRESS_VERSION' ) ? esc_url(home_url('/wp-json/elementskit/v1/')) : esc_url(get_rest_url() . 'elementskit/v1/'); ?>',
		}

		<?php
		$output =  ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Print inline JavaScript.
	 *
	 * @return void
	 */
	public function print_inline_script() {
		printf(
			"<script type='text/javascript'>%s</script>",
			\ElementsKit_Lite\Utils::render( $this->common_js() )
		);
	}
}
