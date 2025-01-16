<?php
/**
 * Socials element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Socials_Element' ) ) {


	class Kenta_Socials_Element extends Element {

		use Kenta_Socials_Controls;

		/**
		 * @param string $id
		 *
		 * @return string
		 */
		protected function getSocialControlId( $id ) {
			return $this->getSlug( $id );
		}

		public function getControls() {
			return $this->getSocialControls( wp_parse_args( $this->defaults, [
				'render-callback'   => $this->selectiveRefresh(),
				'selector'          => ".$this->slug",
				'icons-color-type'  => 'custom',
				'icons-box-spacing' => [
					'top'    => '0px',
					'bottom' => '0px',
					'left'   => '0px',
					'right'  => '0px',
					'linked' => true,
				],
			] ) );
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			// Add button dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {
				$css[".$this->slug"] = array_merge(
					[
						'--kenta-social-icons-size'    => CZ::get( $this->getSlug( 'icons_size' ) ),
						'--kenta-social-icons-spacing' => CZ::get( $this->getSlug( 'icons_spacing' ) )
					],
					Css::dimensions( CZ::get( $this->getSlug( 'padding' ) ), 'padding' ),
					Css::colors( CZ::get( $this->getSlug( 'icons_color' ) ), [
						'initial' => '--kenta-social-icon-initial-color',
						'hover'   => '--kenta-social-icon-hover-color',
					] ),
					Css::colors( CZ::get( $this->getSlug( 'icons_bg_color' ) ), [
						'initial' => '--kenta-social-bg-initial-color',
						'hover'   => '--kenta-social-bg-hover-color',
					] ),
					Css::colors( CZ::get( $this->getSlug( 'icons_border_color' ) ), [
						'initial' => '--kenta-social-border-initial-color',
						'hover'   => '--kenta-social-border-hover-color',
					] )
				);

				return $css;
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {
			$color = CZ::get( $this->getSlug( 'icons_color_type' ) );
			$shape = CZ::get( $this->getSlug( 'icons_shape' ) );
			$fill  = CZ::get( $this->getSlug( 'shape_fill_type' ) );

			$attrs['class'] = Utils::clsx( [
				$this->slug
			], $attrs['class'] ?? [] );

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( 'socials', $attr, $value );
			}

			$this->add_render_attribute( 'social-link', 'class', 'kenta-social-link' );

			if ( CZ::checked( $this->getSlug( 'open_new_tab' ) ) ) {
				$this->add_render_attribute( 'social-link', 'target', '_blank' );
			}

			if ( CZ::checked( $this->getSlug( 'no_follow' ) ) ) {
				$this->add_render_attribute( 'social-link', 'rel', 'nofollow' );
			}

			$socials = CZ::repeater( 'kenta_social_networks' );

			?>
            <div <?php $this->print_attribute_string( 'socials' ); ?>>
                <div class="<?php Utils::the_clsx( [
					'kenta-socials',
					'kenta-socials-' . $color,
					'kenta-socials-' . $shape,
					'kenta-socials-' . $fill => $shape !== 'none',
				] ); ?>">
					<?php foreach ( $socials as $social ) { ?>
                        <a <?php $this->print_attribute_string( 'social-link' ); ?>
                                style="--kenta-official-color: <?php echo esc_attr( $social['color']['official'] ?? 'var(--kenta-primary-color)' ) ?>;"
                                href="<?php echo esc_url( $social['url'] ) ?>">
					<span class="kenta-social-icon">
                        <?php IconsManager::print( $social['icon'] ); ?>
                    </span>
                        </a>
					<?php } ?>
                </div>
            </div>
			<?php
		}
	}
}
