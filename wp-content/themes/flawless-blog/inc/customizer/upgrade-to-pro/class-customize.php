<?php
/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  Flawless Blog 1.0.0
 * @access public
 */
final class Flawless_Blog_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since Flawless Blog 1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since Flawless Blog 1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since Flawless Blog 1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since Flawless Blog 1.0.0
	 * @access public
	 * @param  object $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		require trailingslashit( get_template_directory() ) . 'inc/customizer/upgrade-to-pro/section-pro.php';

		// Register custom section types.
		$manager->register_section_type( 'Flawless_Blog_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new Flawless_Blog_Customize_Section_Pro(
				$manager,
				'flawless-blog',
				array(
					'title'    => esc_html__( 'Flawless Blog Pro', 'flawless-blog' ),
					'pro_text' => esc_html__( 'Go Pro', 'flawless-blog' ),
					'pro_url'  => esc_url( 'https://adorethemes.com/downloads/flawless-blog-pro/' ),
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since Flawless Blog 1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'flawless-blog-go-pro-customize-controls', trailingslashit( get_template_directory_uri() ) . 'inc/customizer/upgrade-to-pro/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'flawless-blog-go-pro-customize-controls', trailingslashit( get_template_directory_uri() ) . 'inc/customizer/upgrade-to-pro/customize-controls.css' );
	}
}

// Doing this customizer thang!
Flawless_Blog_Customize::get_instance();
