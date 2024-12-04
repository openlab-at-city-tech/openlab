<?php
/**
 * Logo element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\CallToAction;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\ImageUploader;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\Controls\Typography;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Logo_Element' ) ) {

	class Kenta_Logo_Element extends Element {

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return [
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), [
						( new ImageUploader( $this->getSlug( 'logo' ) ) )
							->setLabel( __( 'Logo', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->setDefaultValue( $this->getDefaultSetting( 'logo', '' ) )
						,
						( new Toggle( 'kenta_enable_dark_scheme_logo' ) )
							->setLabel( __( 'Logo For Dark Mode', 'kenta' ) )
							->selectiveRefresh( '.kenta-site-header', 'kenta_header_render' )
							->closeByDefault()
						,
						( new \LottaFramework\Customizer\Controls\Condition() )
							->setCondition( [ 'kenta_enable_dark_scheme_logo' => 'yes' ] )
							->setControls( [
								( new ImageUploader( 'kenta_dark_scheme_logo' ) )
									->setLabel( __( 'Dark Mode Logo', 'kenta' ) )
									->setDefaultValue( '' )
									->selectiveRefresh( '.kenta-site-header', 'kenta_header_render' )
								,
							] ),
						( new Separator() ),
						( new Radio( $this->getSlug( 'position' ) ) )
							->setLabel( __( 'Logo Position', 'kenta' ) )
							->buttonsGroupView()
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->setDefaultValue( $this->getDefaultSetting( 'position', 'left' ) )
							->setChoices( [
								'left'  => __( 'Left', 'kenta' ),
								'right' => __( 'Right', 'kenta' ),
								'top'   => __( 'Top', 'kenta' ),
							] )
						,
						( new Slider( $this->getSlug( 'width' ) ) )
							->setLabel( __( 'Logo Width', 'kenta' ) )
							->enableResponsive()
							->asyncCss( ".{$this->slug}", [ '--logo-max-width' => 'value' ] )
							->setUnits( [
								[ 'unit' => 'px', 'min' => 0, 'max' => 600 ],
								[ 'unit' => '%', 'min' => 0, 'max' => 100 ],
							] )
							->postMessage()
							->setDefaultValue( $this->getDefaultSetting( 'width', '200px' ) )
						,
						( new Slider( $this->getSlug( 'spacing' ) ) )
							->setLabel( __( 'Logo Spacing', 'kenta' ) )
							->enableResponsive()
							->asyncCss( ".{$this->slug}", [ '--logo-spacing' => 'value' ] )
							->setMin( 0 )
							->setMax( 300 )
							->setDefaultUnit( 'px' )
							->setDefaultValue( $this->getDefaultSetting( 'spacing', '12px' ) )
						,
						( new Separator() ),
						( new Toggle( $this->getSlug( 'enable_site_title' ) ) )
							->setLabel( __( 'Site Title', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->setDefaultValue( $this->getDefaultSetting( 'enable-site-title', 'yes' ) )
						,
						( new Toggle( $this->getSlug( 'enable_site_tagline' ) ) )
							->setLabel( __( 'Site Tagline', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->setDefaultValue( $this->getDefaultSetting( 'enable-site-tagline', 'no' ) )
						,
						( new CallToAction() )
							->setLabel( __( 'Click here to edit the site title and tagline.', 'kenta' ) )
							->expandCustomize( 'title_tagline' )
						,
						( new Separator() )->dashedStyle(),
						( new Radio( $this->getSlug( 'content_alignment' ) ) )
							->enableResponsive()
							->asyncCss( ".{$this->slug}", [ 'text-align' => 'value' ] )
							->setLabel( __( 'Content Alignment', 'kenta' ) )
							->buttonsGroupView()
							->setDefaultValue( $this->getDefaultSetting( 'content-alignment', 'left' ) )
							->setChoices( [
								'left'   => __( 'Left', 'kenta' ),
								'center' => __( 'Center', 'kenta' ),
								'right'  => __( 'Right', 'kenta' ),
							] )
						,
					] )
					->addTab( 'style', __( 'Style', 'kenta' ), [
						( new Typography( $this->getSlug( 'site_title_typography' ) ) )
							->setLabel( __( 'Site Title Typography', 'kenta' ) )
							->asyncCss( ".{$this->slug} .site-title", AsyncCss::typography() )
							->setDefaultValue( $this->getDefaultSetting( 'site-title-typography', [
								'family'        => 'inherit',
								'fontSize'      => '28px',
								'variant'       => '400',
								'lineHeight'    => '1.5',
								'textTransform' => 'uppercase',
							] ) )
						,
						( new ColorPicker( $this->getSlug( 'site_title_color' ) ) )
							->setLabel( __( 'Site Title Color', 'kenta' ) )
							->enableAlpha()
							->asyncColors( ".{$this->slug} .site-title", [
								'initial' => '--kenta-link-initial-color',
								'hover'   => '--kenta-link-hover-color',
							] )
							->addColor( 'initial', __( 'Initial', 'kenta' ),
								$this->getDefaultSetting( 'title-initial', 'var(--kenta-accent-color)' ) )
							->addColor( 'hover', __( 'Hover', 'kenta' ),
								$this->getDefaultSetting( 'title-hover', 'var(--kenta-primary-color)' ) )
						,
						( new Separator() ),
						( new Typography( $this->getSlug( 'site_tagline_typography' ) ) )
							->setLabel( __( 'Site Tagline Typography', 'kenta' ) )
							->asyncCss( ".{$this->slug} .site-tagline", AsyncCss::typography() )
							->setDefaultValue( $this->getDefaultSetting( 'site-tagline-typography', [
								'family'     => 'inherit',
								'fontSize'   => '14px',
								'variant'    => '400',
								'lineHeight' => '1.5',
							] ) )
						,
						( new ColorPicker( $this->getSlug( 'site_tagline_color' ) ) )
							->setLabel( __( 'Site Tagline Color', 'kenta' ) )
							->enableAlpha()
							->asyncColors( ".{$this->slug} .site-tagline", [
								'initial' => '--kenta-link-initial-color',
							] )
							->addColor( 'initial', __( 'Initial', 'kenta' ),
								$this->getDefaultSetting( 'tagline-initial', 'var(--kenta-accent-active)' ) )
						,
					] )
			];
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {

				$css[".{$this->slug}"] = [
					'--logo-max-width' => CZ::get( $this->getSlug( 'width' ) ),
					'--logo-spacing'   => CZ::get( $this->getSlug( 'spacing' ) ),
					'text-align'       => CZ::get( $this->getSlug( 'content_alignment' ) ),
				];

				if ( CZ::checked( $this->getSlug( 'enable_site_title' ) ) ) {
					$css[".{$this->slug} .site-title"] = array_merge(
						Css::typography( CZ::get( $this->getSlug( 'site_title_typography' ) ) ),
						Css::colors( CZ::get( $this->getSlug( 'site_title_color' ) ), [
							'initial' => '--kenta-link-initial-color',
							'hover'   => '--kenta-link-hover-color',
						] )
					);
				}

				if ( CZ::checked( $this->getSlug( 'enable_site_tagline' ) ) ) {
					$css[".{$this->slug} .site-tagline"] = array_merge(
						Css::typography( CZ::get( $this->getSlug( 'site_tagline_typography' ) ) ),
						Css::colors( CZ::get( $this->getSlug( 'site_tagline_color' ) ), [
							'initial' => '--kenta-link-initial-color',
						] )
					);
				}

				return $css;
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( 'wrapper', $attr, $value );
			}

			$this->add_render_attribute( 'wrapper', 'class', 'kenta-site-branding ' . $this->slug );
			$this->add_render_attribute( 'wrapper', 'data-logo', CZ::get( $this->getSlug( 'position' ) ) );

			$trans_logo_attr = array();
			$dark_logo_attr  = array();
			$logo_attr       = CZ::imgAttrs( $this->getSlug( 'logo' ) );

			if ( $this->getDefaultSetting( 'transparent-logo', false )
			     && kenta_is_transparent_header()
			     && CZ::checked( 'kenta_enable_transparent_header_logo' )
			) {
				$trans_logo_attr          = CZ::imgAttrs( 'kenta_transparent_header_logo' );
				$trans_logo_attr['class'] = 'kenta-transparent-logo';
			}

			if ( CZ::checked( 'kenta_enable_dark_scheme_logo' ) ) {
				$dark_logo_attr          = CZ::imgAttrs( 'kenta_dark_scheme_logo' );
				$dark_logo_attr['class'] = 'kenta-dark-scheme-logo';
			}

			$title = CZ::checked( $this->getSlug( 'enable_site_title' ) )
				? get_bloginfo( 'name' )
				: '';

			$tagline = CZ::checked( $this->getSlug( 'enable_site_tagline' ) )
				? get_bloginfo( 'description' )
				: '';

			?>
            <div <?php $this->print_attribute_string( 'wrapper' ); ?>>
				<?php if ( ! empty( $logo_attr ) ): ?>
                    <a class="<?php Utils::the_clsx( [
						'site-logo'                  => true,
						'kenta-has-dark-scheme-logo' => ! empty( $dark_logo_attr ),
						'kenta-has-transparent-logo' => ! empty( $trans_logo_attr ),
					] ) ?>" href="<?php echo esc_url( home_url() ) ?>">
						<?php if ( ! empty( $trans_logo_attr ) ): ?>
                            <img <?php Utils::print_attribute_string( $trans_logo_attr ); ?> />
						<?php endif; ?>
						<?php if ( ! empty( $dark_logo_attr ) ): ?>
                            <img <?php Utils::print_attribute_string( $dark_logo_attr ); ?> />
						<?php endif; ?>
                        <img class="kenta-logo" <?php Utils::print_attribute_string( $logo_attr ); ?> />
                    </a>
				<?php endif; ?>
                <div class="site-identity">
					<?php if ( $title !== '' ): ?>
                        <span class="site-title">
                        <a href="<?php echo esc_url( home_url() ) ?>"><?php echo esc_html( $title ) ?></a>
                    </span>
					<?php endif; ?>
					<?php if ( $tagline !== '' ): ?>
                        <span class="site-tagline">
                        <?php echo esc_html( $tagline ) ?>
                    </span>
					<?php endif; ?>
                </div>
            </div>
			<?php
		}
	}
}
