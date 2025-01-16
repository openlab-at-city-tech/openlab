<?php
/**
 * Breadcrumbs element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\ImageRadio;
use LottaFramework\Customizer\Controls\Spacing;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Typography;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\Breadcrumbs;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Breadcrumbs_Element' ) ) {

	class Kenta_Breadcrumbs_Element extends Element {

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return [
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), $this->getBreadcrumbsContentControls() )
					->addTab( 'style', __( 'Style', 'kenta' ), $this->getBreadcrumbsStyleControls() )
			];
		}

		protected function getBreadcrumbsContentControls() {
			$controls = [
				( new ImageRadio( $this->getSlug( 'separator' ) ) )
					->setLabel( __( 'Separator', 'kenta' ) )
					->selectiveRefresh( ...$this->selectiveRefresh() )
					->setDefaultValue( 'breadcrumb-sep-1' )
					->setColumns( 4 )
					->setChoices( [
						'breadcrumb-sep-1' => [
							'src'   => kenta_image( 'breadcrumb-sep-1' ),
							'title' => __( 'Type 1', 'kenta' ),
						],
						'breadcrumb-sep-2' => [
							'src'   => kenta_image( 'breadcrumb-sep-2' ),
							'title' => __( 'Type 2', 'kenta' ),
						],
						'breadcrumb-sep-3' => [
							'src'   => kenta_image( 'breadcrumb-sep-3' ),
							'title' => __( 'Type 3', 'kenta' ),
						],
						'breadcrumb-sep-4' => [
							'src'   => kenta_image( 'breadcrumb-sep-4' ),
							'title' => __( 'Type 4', 'kenta' ),
						],
					] ),
			];

			return apply_filters( 'kenta_breadcrumbs_element_content_controls', $controls, $this->slug, $this->selectiveRefresh() );
		}

		protected function getBreadcrumbsStyleControls() {
			$controls = [
				( new Spacing( $this->getSlug( 'spacing' ) ) )
					->setLabel( __( 'Spacing', 'kenta' ) )
					->enableResponsive()
					->setDisabled( [ 'left', 'right' ] )
					->asyncCss( ".$this->slug", AsyncCss::dimensions( 'padding' ) )
					->setDefaultValue( [
						'left'   => '0px',
						'right'  => '0px',
						'top'    => '12px',
						'bottom' => '12px',
						'linked' => true,
					] )
				,
			];

			return apply_filters( 'kenta_breadcrumbs_element_style_controls', $controls, $this->slug, $this->selectiveRefresh() );
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			// Add button dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {
				$css[".$this->slug"] = array_merge(
					Css::dimensions( CZ::get( $this->getSlug( 'spacing' ) ), 'padding' ),
					Css::typography( CZ::get( $this->getSlug( 'typography' ) ) ),
					Css::colors( CZ::get( $this->getSlug( 'text_color' ) ), [
						'text'    => '--breadcrumb-text',
						'initial' => '--breadcrumb-link-initial',
						'hover'   => '--breadcrumb-link-hover',
					] )
				);

				return $css;
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function shouldRender() {
			// Don't render in front page
			return ! is_front_page();
		}

		public function render( $attrs = [] ) {
			$attrs['class'] = Utils::clsx( [
				'kenta-breadcrumbs-element',
				$this->slug
			], $attrs['class'] ?? [] );

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( 'breadcrumbs', $attr, $value );
			}

			Breadcrumbs::setSep( '<span class="breadcrumb-sep mx-2">' . kenta_image( CZ::get( $this->getSlug( 'separator' ) ) ) . '</span>' );
			Breadcrumbs::setLinkFormat( '<a class="breadcrumb-link" href="%1$s">%2$s</a>' );
			Breadcrumbs::setItemFormat( '<span class="breadcrumb-item">%1$s</span>' );

			/**
			 * Before breadcrumbs element render
			 */
			do_action( 'kenta_before_breadcrumbs_render', $this->slug );

			?>
            <div <?php $this->print_attribute_string( 'breadcrumbs' ); ?>>
				<?php
				/**
				 * Render breadcrumbs element
				 */
				do_action( 'kenta_render_breadcrumbs' );
				?>
            </div>
			<?php

			/**
			 * After breadcrumbs element render
			 */
			do_action( 'kenta_after_breadcrumbs_render', $this->slug );
		}
	}
}
