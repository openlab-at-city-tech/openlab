<?php 
namespace ElementsKit_Lite\Modules\Header_Footer\Theme_Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Neve theme compatibility.
 */
class Neve {

	/**
	 * Instance of Elementor Frontend class.
	 *
	 * @var \Elementor\Frontend()
	 */
	private $elementor;

	private $header;
	private $footer;

	/**
	 * Run all the Actions / Filters.
	 */
	function __construct( $template_ids ) {
		$this->header = $template_ids[0];
		$this->footer = $template_ids[1];
		
		if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
			$this->elementor = \Elementor\Plugin::instance();
		}

		if ( $this->header != null ) {
			add_action( 'template_redirect', array( $this, 'remove_theme_header_markup' ), 10 );
			add_action( 'neve_do_header', array( $this, 'add_plugin_header_markup' ) );
		}

		if ( $this->footer != null ) {
			add_action( 'template_redirect', array( $this, 'remove_theme_footer_markup' ), 10 );
			add_action( 'neve_do_footer', array( $this, 'add_plugin_footer_markup' ) );
		}
	}

	// header actions
	public function remove_theme_header_markup() {
		remove_all_actions( 'hfg_header_render' );
	}

	public function add_plugin_header_markup() {
		do_action( 'elementskit/template/before_header' );
		echo '<div class="ekit-template-content-markup ekit-template-content-header">';
			echo \ElementsKit_Lite\Utils::render_elementor_content( $this->header ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering
		echo '</div>';
		do_action( 'elementskit/template/after_header' );
	}

	// footer actions
	public function remove_theme_footer_markup() {
		remove_all_actions( 'hfg_footer_render' );
	}
	
	public function add_plugin_footer_markup() {
			do_action( 'elementskit/template/before_footer' );
			echo '<div class="ekit-template-content-markup ekit-template-content-footer">';
			echo \ElementsKit_Lite\Utils::render_elementor_content( $this->footer ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering
			echo '</div>';
			do_action( 'elementskit/template/after_footer' );
	}
}
