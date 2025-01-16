<?php
/**
 * Button element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Text;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Button_Element' ) ) {

	class Kenta_Button_Element extends Element {

		use Kenta_Button_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			$button_defaults = array_merge( [
				'button-selector'      => ".{$this->slug}",
				'button-selective'     => $this->selectiveRefresh(),
				'button-css-selective' => 'kenta-header-selective-css',
				'show-arrow'           => 'yes',
			], $this->defaults );

			return [
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), array_merge( [
						( new Text( $this->getSlug( 'link' ) ) )
							->setLabel( __( 'Button Link', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->setDefaultValue( '#' )
						,
						( new Separator() ),
						( new Toggle( $this->getSlug( 'open_new_tab' ) ) )
							->setLabel( __( 'Open In New Tab', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->closeByDefault()
						,
						( new Toggle( $this->getSlug( 'no_follow' ) ) )
							->setLabel( __( 'No Follow', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->closeByDefault()
						,
						( new Separator() ),
					], $this->getButtonContentControls( $this->slug . '_', $button_defaults ) ) )
					->addTab( 'style', __( 'Style', 'kenta' ), $this->getButtonStyleControls( $this->slug . '_', $button_defaults ) )
				,
			];
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {

			// Add button dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {

				$preset = kenta_button_preset( $this->slug . '_', CZ::get( $this->getSlug( 'preset' ) ) );

				$css[".{$this->slug}"] = array_merge(
					[
						'--kenta-button-height' => CZ::get( $this->getSlug( 'min_height' ) )
					],
					Css::shadow( CZ::get( $this->getSlug( 'shadow' ), $preset ), '--kenta-button-shadow' ),
					Css::shadow( CZ::get( $this->getSlug( 'shadow_active' ), $preset ), '--kenta-button-shadow-active' ),
					Css::typography( CZ::get( $this->getSlug( 'typography' ), $preset ) ),
					Css::dimensions( CZ::get( $this->getSlug( 'padding' ), $preset ), '--kenta-button-padding' ),
					Css::dimensions( CZ::get( $this->getSlug( 'radius' ), $preset ), '--kenta-button-radius' ),
					Css::colors( CZ::get( $this->getSlug( 'text_color' ), $preset ), [
						'initial' => '--kenta-button-text-initial-color',
						'hover'   => '--kenta-button-text-hover-color',
					] ),
					Css::colors( CZ::get( $this->getSlug( 'button_color' ), $preset ), [
						'initial' => '--kenta-button-initial-color',
						'hover'   => '--kenta-button-hover-color',
					] ),
					Css::border( CZ::get( $this->getSlug( 'border' ), $preset ), '--kenta-button-border' )
				);

				return $css;
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {
			$attrs['class'] = Utils::clsx( [
				'kenta-button',
				'kenta-button-' . CZ::get( $this->getSlug( 'arrow_dir' ) ),
				$this->slug
			], $attrs['class'] ?? [] );

			$this->add_render_attribute( 'button', 'href', esc_url( CZ::get( $this->getSlug( 'link' ) ) ) );

			if ( CZ::checked( $this->getSlug( 'open_new_tab' ) ) ) {
				$this->add_render_attribute( 'button', 'target', '_blank' );
			}

			if ( CZ::checked( $this->getSlug( 'no_follow' ) ) ) {
				$this->add_render_attribute( 'button', 'rel', 'nofollow' );
			}

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( 'button', $attr, $value );
			}

			?>
            <a <?php $this->print_attribute_string( 'button' ); ?>>
				<?php
				if ( CZ::checked( $this->getSlug( 'show_arrow' ) ) ) {
					echo '<span class="kenta-button-icon">';
					IconsManager::print( CZ::get( $this->getSlug( 'arrow' ) ) );
					echo '</span>';
				}
				?>
                <span class="kenta-button-text">
				    <?php echo esc_html( CZ::get( $this->getSlug( 'text' ) ) ) ?>
                </span>
            </a>
			<?php
		}
	}
}
