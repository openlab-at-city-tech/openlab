<?php
/**
 * Kadence\Elementor_Pro\Component class
 *
 * @package kadence
 */

namespace Kadence\Elementor_Pro;

use Kadence\Component_Interface;
use Elementor;
use \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag;
use \Elementor\Modules\DynamicTags\Module;
use \Elementor\Controls_Manager;
use function Kadence\kadence;
use function add_action;

if ( class_exists( '\ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag' ) ) {

	/**
	 * Class for adding Elementor plugin support.
	 */
	class Elementor_Dynamic_Colors extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag {

		/**
		 * Get Name
		 *
		 * Returns the Name of the tag
		 *
		 * @since 2.0.0
		 * @access public
		 *
		 * @return string
		 */
		public function get_name() {
			return 'kadence-color-palette';
		}

		/**
		 * Get Title
		 *
		 * Returns the title of the Tag
		 *
		 * @since 2.0.0
		 * @access public
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'Kadence Color Palette', 'kadence' );
		}

		/**
		 * Get Group
		 *
		 * Returns the Group of the tag
		 *
		 * @since 2.0.0
		 * @access public
		 *
		 * @return string
		 */
		public function get_group() {
			return 'kadence-palette';
		}

		/**
		 * Get Categories
		 *
		 * Returns an array of tag categories
		 *
		 * @since 2.0.0
		 * @access public
		 *
		 * @return array
		 */
		public function get_categories() {
			return [ \Elementor\Modules\DynamicTags\Module::COLOR_CATEGORY ];
		}

		/**
		 * Register Controls
		 *
		 * Registers the Dynamic tag controls
		 *
		 * @since 2.0.0
		 * @access protected
		 *
		 * @return void
		 */
		protected function register_controls() {

			$variables = array(
				'palette1' => __( '1 - Accent', 'kadence' ),
				'palette2' => __( '2 - Accent - alt', 'kadence' ),
				'palette3' => __( '3 - Strongest text', 'kadence' ),
				'palette4' => __( '4 - Strong Text', 'kadence' ),
				'palette5' => __( '5 - Medium text', 'kadence' ),
				'palette6' => __( '6 - Subtle Text', 'kadence' ),
				'palette7' => __( '7 - Subtle Background', 'kadence' ),
				'palette8' => __( '8 - Lighter Background', 'kadence' ),
				'palette9' => __( '9 - White or offwhite', 'kadence' ),
				'palette10' => __( '10 - Accent - Complement', 'kadence' ),
				'palette11' => __( '11 - Notices - Success', 'kadence' ),
				'palette12' => __( '12 - Notices - Info', 'kadence' ),
				'palette13' => __( '13 - Notices - Alert', 'kadence' ),
				'palette14' => __( '14 - Notices - Warning', 'kadence' ),
				'palette15' => __( '15 - Notices - Rating', 'kadence' ),
			);
			$this->add_control(
				'kadence_palette',
				array(
					'label' => __( 'Color', 'kadence' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => $variables,
				)
			);
		}
		/**
		 * Get Value
		 *
		 * Returns the value of the Dynamic tag
		 *
		 * @since 2.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function get_value( array $options = array() ) {
			$param_name = $this->get_settings( 'kadence_palette' );
			if ( ! $param_name ) {
				return;
			}
			$value = 'var(--global-' . $param_name . ')';
			return $value;
		}
	}
}
