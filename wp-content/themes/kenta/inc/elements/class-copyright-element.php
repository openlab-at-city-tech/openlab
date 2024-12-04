<?php
/**
 * Copyright element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Typography;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Copyright_Element' ) ) {

	class Kenta_Copyright_Element extends Element {

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return apply_filters( 'kenta_copyright_element_controls', [
				( new Typography( $this->getSlug( 'typography' ) ) )
					->setLabel( __( 'Typography', 'kenta' ) )
					->asyncCss( ".$this->slug", AsyncCss::typography() )
					->setDefaultValue( [
						'family'     => 'inherit',
						'fontSize'   => '0.85rem',
						'variant'    => '400',
						'lineHeight' => '1.5em'
					] )
				,
				( new ColorPicker( $this->getSlug( 'color' ) ) )
					->setLabel( __( 'Color', 'kenta' ) )
					->asyncColors( ".$this->slug", [
						'text'    => 'color',
						'initial' => '--kenta-link-initial-color',
						'hover'   => '--kenta-link-hover-color',
					] )
					->enableAlpha()
					->addColor( 'text', __( 'Text Initial', 'kenta' ), 'var(--kenta-accent-active)' )
					->addColor( 'initial', __( 'Initial', 'kenta' ), 'var(--kenta-primary-color)' )
					->addColor( 'hover', __( 'Hover', 'kenta' ), 'var(--kenta-primary-active)' )
				,
			], $this->slug, $this->selectiveRefresh() );
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			// Add copyright dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {
				$css[".$this->slug"] = array_merge(
					Css::typography( CZ::get( $this->getSlug( 'typography' ) ) ),
					Css::colors( CZ::get( $this->getSlug( 'color' ) ), [
						'text'    => 'color',
						'initial' => '--kenta-link-initial-color',
						'hover'   => '--kenta-link-hover-color',
					] )
				);

				return $css;
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {
			$attrs['class'] = Utils::clsx( [
				'kenta-copyright',
				'kenta-raw-html',
				$this->slug
			], $attrs['class'] ?? [] );

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( 'copyright', $attr, $value );
			}

			$theme       = wp_get_theme();
			$theme_info  = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $theme->get( 'ThemeURI' ) ), $theme->get( 'Name' ) );
			$author_info = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $theme->get( 'AuthorURI' ) ), $theme->get( 'Author' ) );

			$text = CZ::get( $this->getSlug( 'text' ) ) ?? 'Copyright &copy; {current_year}  -  {about_theme} By {about_author}';

			$text = str_replace( '{current_year}', date( 'Y' ), $text );
			$text = str_replace( '{site_title}', get_bloginfo( 'name' ), $text );
			$text = str_replace( '{about_theme}', $theme_info, $text );
			$text = str_replace( '{about_author}', $author_info, $text );

			?>
            <div <?php $this->print_attribute_string( 'copyright' ); ?>>
				<?php echo wp_kses_post( $text ); ?>
            </div>
			<?php
		}
	}
}
