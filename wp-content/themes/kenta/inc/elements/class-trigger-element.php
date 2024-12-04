<?php
/**
 * Trigger element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Icons;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Trigger_Element' ) ) {

	class Kenta_Trigger_Element extends Element {

		use Kenta_Icon_Button_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return [
				( new Tabs() )
					->setActiveTab( 'icon' )
					->addTab( 'icon', __( 'Icon', 'kenta' ), array_merge( [
						( new Icons( $this->getSlug( 'icon_button_icon' ) ) )
							->setLabel( __( 'Icon', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->setDefaultValue( [
								'value'   => 'fas fa-bars-staggered',
								'library' => 'fa-solid',
							] )
						,
						( new Separator() ),
					], $this->getIconControls( [
						'selector'              => ".{$this->slug}",
						'render-callback'       => $this->selectiveRefresh(),
						'css-selective-refresh' => 'kenta-header-selective-css',
					] ) ) )
					->addTab( 'style', __( 'Style', 'kenta' ), $this->getIconStyleControls( [
						'selector'              => ".{$this->slug}",
						'render-callback'       => $this->selectiveRefresh(),
						'css-selective-refresh' => 'kenta-header-selective-css',
                    ] ) )
			];
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
				'kenta-trigger',
				'kenta-icon-button',
				'kenta-icon-button-' . $shape,
				'kenta-icon-button-' . $fill => $shape !== 'none',
				$this->slug
			], $attrs['class'] ?? [] );

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( 'trigger', $attr, $value );
			}

			$this->add_render_attribute( 'trigger', 'data-toggle-target', '#kenta-off-canvas-modal' );
			$this->add_render_attribute( 'trigger', ' data-toggle-show-focus', '#kenta-off-canvas-modal :focusable' );

			?>
            <button type="button" <?php $this->print_attribute_string( 'trigger' ); ?>>
				<?php IconsManager::print( CZ::get( $this->getSlug( 'icon_button_icon' ) ) ); ?>
            </button>
			<?php
		}
	}
}
