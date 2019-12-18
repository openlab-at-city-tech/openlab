<?php

class Shortcodes_Ultimate_Admin_Extra_Shortcodes {

	public function __construct() {}

	public function register_shortcodes() {

		if ( $this->is_extra_active() ) {
			return;
		}

		foreach ( $this->get_shortcodes() as $shortcode ) {

			su_add_shortcode(
				wp_parse_args(
					$shortcode,
					array(
						'group'              => 'extra',
						'image'              => $this->get_image_url( 'icon-available-shortcodes.png' ),
						'icon'               => $this->get_image_url( 'icon-generator.png' ),
						'desc'               => '',
						'callback'           => '__return_empty_string',
						'atts'               => array(),
						'generator_callback' => array( $this, 'generator_callback' ),
						'as_callback'        => array( $this, 'as_callback' ),
					)
				)
			);

		}

	}

	public function register_group( $groups ) {

		if ( ! $this->is_extra_active() ) {
			$groups['extra'] = _x( 'Extra Shortcodes', 'Custom shortcodes group name', 'shortcodes-ultimate' );
		}

		return $groups;

	}

	public function generator_callback( $shortcode ) {
		// phpcs:disable
		echo $this->get_template( 'generator', $shortcode );
		// phpcs:enable
	}

	public function as_callback( $shortcode ) {
		// phpcs:disable
		echo $this->get_template( 'available-shortcodes', $shortcode );
		// phpcs:enable
	}

	public function get_image_url( $path ) {
		return plugin_dir_url( __FILE__ ) . 'images/extra/' . $path;
	}

	private function is_extra_active() {
		return did_action( 'su/extra/ready' );
	}

	private function get_shortcodes() {

		return array(
			array(
				'id'   => 'splash',
				'name' => __( 'Splash screen', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'exit_popup',
				'name' => __( 'Exit popup', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'panel',
				'name' => __( 'Panel', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'photo_panel',
				'name' => __( 'Photo panel', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'icon_panel',
				'name' => __( 'Icon panel', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'icon_text',
				'name' => __( 'Text with icon', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'progress_pie',
				'name' => __( 'Progress pie', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'progress_bar',
				'name' => __( 'Progress bar', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'member',
				'name' => __( 'Member', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'section',
				'name' => __( 'Section', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'pricing_table',
				'name' => __( 'Pricing table', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'testimonial',
				'name' => __( 'Testimonial', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'icon',
				'name' => __( 'Icon', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'content_slider',
				'name' => __( 'Content slider', 'shortcodes-ultimate' ),
			),
			array(
				'id'   => 'shadow',
				'name' => __( 'Shadow', 'shortcodes-ultimate' ),
			),
		);

	}

	protected function get_template( $name = '', $data = array() ) {

		if ( preg_match( '/^(?!-)[a-z0-9-_]+(?<!-)(\/(?!-)[a-z0-9-_]+(?<!-))*$/', $name ) !== 1 ) {
			return '';
		}

		$file = plugin_dir_path( __FILE__ ) . 'partials/extra/' . $name . '.php';

		if ( ! file_exists( $file ) ) {
			return '';
		}

		ob_start();
		include $file;
		return ob_get_clean();

	}

}
