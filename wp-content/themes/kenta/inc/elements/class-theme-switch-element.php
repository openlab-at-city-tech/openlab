<?php
/**
 * Theme Switch element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Icons;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'Kenta_Theme_Switch_Element' ) ) {

	class Kenta_Theme_Switch_Element extends Element {

		use Kenta_Icon_Button_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {

			return array_merge( [
				( new Icons( $this->getSlug( 'light_icon' ) ) )
					->setLabel( __( 'Light Icon', 'kenta' ) )
					->selectiveRefresh( ...$this->selectiveRefresh() )
					->setDefaultValue( [
						'value'   => 'fas fa-sun',
						'library' => 'fa-solid',
					] )
				,
				( new Icons( $this->getSlug( 'dark_icon' ) ) )
					->setLabel( __( 'Dark Icon', 'kenta' ) )
					->selectiveRefresh( ...$this->selectiveRefresh() )
					->setDefaultValue( [
						'value'   => 'fas fa-moon',
						'library' => 'fa-solid',
					] )
				,
				( new Separator() ),
			],
				$this->getIconControls( [
					'render-callback' => $this->selectiveRefresh(),
					'selector'        => ".{$this->slug}"
				] ),
				$this->getIconStyleControls( [
					'selector' => ".{$this->slug}"
				] )
			);
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			// Add button dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {
				$css[".{$this->slug}"] = $this->getIconButtonCss();

				return $css;
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {
			$preset = $this->getIconButtonPreset( CZ::get( $this->getSlug( 'icon_button_preset' ) ) );
			$shape  = CZ::get( $this->getSlug( 'icon_button_icon_shape' ), $preset );
			$fill   = CZ::get( $this->getSlug( 'icon_button_shape_fill_type' ), $preset );

			$attrs['class'] = Utils::clsx( [
				'kenta-theme-switch',
				'kenta-icon-button',
				'kenta-icon-button-' . $shape,
				'kenta-icon-button-' . $fill => $shape !== 'none',
				$this->slug
			], $attrs['class'] ?? [] );

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( 'theme-switch', $attr, $value );
			}
			?>
            <button type="button" <?php $this->print_attribute_string( 'theme-switch' ); ?>>
	            <span class="light-mode">
				<?php IconsManager::print( CZ::get( $this->getSlug( 'light_icon' ) ) ); ?>
	            </span>
                <span class="dark-mode">
				<?php IconsManager::print( CZ::get( $this->getSlug( 'dark_icon' ) ) ); ?>
	            </span>
            </button>
			<?php
		}
	}
}
