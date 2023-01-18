<?php 
namespace ElementsKit_Lite\Modules\Header_Footer\Theme_Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * TwentyNineteen support for the header footer.
 */
class TwentyNineteen {


	/**
	 * Run all the Actions / Filters.
	 */
	function __construct( $template_ids ) {
		if ( $template_ids[0] != null ) {
			add_action( 'get_header', array( $this, 'get_header' ) );
		}
		if ( $template_ids[1] != null ) {
			add_action( 'get_footer', array( $this, 'get_footer' ) );
		}
	}

	public function get_header( $name ) {
		add_action(
			'elementskit/template/after_header',
			function() {
				echo '<div id="page" class="site">';
				echo '<div id="content" class="site-content">';
			}
		);
		require __DIR__ . '/../views/theme-support-header.php';

		$templates = array();
		$name      = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "header-{$name}.php";
		}

		$templates[] = 'header.php';

		// Avoid running wp_head hooks again
		remove_all_actions( 'wp_head' );
		ob_start();
		// It cause a `require_once` so, in the get_header it self it will not be required again.
		locate_template( $templates, true );
		ob_get_clean();
	}

	public function get_footer( $name ) {
		add_action(
			'elementskit/template/after_footer',
			function() {
				echo '</div></div>';
			}
		);

		require __DIR__ . '/../views/theme-support-footer.php';

		$templates = array();
		$name      = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "footer-{$name}.php";
		}

		$templates[] = 'footer.php';

		ob_start();
		// It cause a `require_once` so, in the get_header it self it will not be required again.
		locate_template( $templates, true );
		ob_get_clean();
	}


}
