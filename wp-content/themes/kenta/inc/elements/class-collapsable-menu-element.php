<?php
/**
 * Divider element
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Border;
use LottaFramework\Customizer\Controls\CallToAction;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Icons;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\Controls\Typography;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Collapsable_Menu_Element' ) ) {

	class Kenta_Collapsable_Menu_Element extends Element {

		/**
		 * After element register
		 */
		public function after_register() {
			// Register nav menu
			add_action( 'after_setup_theme', function () {
				register_nav_menu( $this->slug, $this->getLabel() );
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return [
				( new CallToAction( $this->getSlug( 'edit_locations' ) ) )
					->setLabel( __( 'Edit Menu Locations', 'kenta' ) )
					->expandCustomize( 'menu_locations' )
				,
				( new Separator() ),
				( new Slider( $this->getSlug( 'depth' ) ) )
					->setLabel( __( 'Menu Depth', 'kenta' ) )
					->setDescription( __( '"0" meas no limit.', 'kenta' ) )
					->selectiveRefresh( ...$this->selectiveRefresh() )
					->displayInline()
					->setMin( 0 )
					->setMax( 10 )
					->setDefaultUnit( false )
					->setDefaultValue( $this->getDefaultSetting( 'depth', 0 ) )
				,
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), [
						( new Toggle( $this->getSlug( 'collapsable' ) ) )
							->setLabel( __( 'Collapsable', 'kenta' ) )
							->setDescription( __( 'This option will collapse/expand the sub menu items on click/touch.', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->openByDefault()
						,
						( new Separator() ),
						( new Icons( $this->getSlug( 'toggle-icon' ) ) )
							->setLabel( __( 'Toggle Icon', 'kenta' ) )
							->selectiveRefresh( ...$this->selectiveRefresh() )
							->setDefaultValue( [
								'value'   => 'fas fa-angle-down',
								'library' => 'fa-solid',
							] )
						,
						( new Separator() ),
						( new Radio( $this->getSlug( 'toggle-style' ) ) )
							->setLabel( __( 'Toggle Style', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-header-selective-css' )
							->setDefaultValue( 'bordered' )
							->buttonsGroupView()
							->setChoices( [
								'simple'   => __( 'Simple', 'kenta' ),
								'bordered' => __( 'Bordered', 'kenta' ),
							] )
						,
						( new Condition() )
							->setCondition( [ $this->getSlug( 'toggle-style' ) => 'bordered' ] )
							->setControls( [
								( new Border( $this->getSlug( 'toggle-border' ) ) )
									->setLabel( __( 'Toggle Border', 'kenta' ) )
									->bindSelectiveRefresh( 'kenta-header-selective-css' )
									->setDefaultBorder( 1, 'solid', 'var(--kenta-base-200)' )
								,
							] )
						,
					] )
					->addTab( 'style', __( 'Style', 'kenta' ), [
						( new Typography( $this->getSlug( 'typography' ) ) )
							->setLabel( __( 'Typography', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-header-selective-css' )
							->setDefaultValue( [
								'family'     => 'inherit',
								'fontSize'   => '1rem',
								'variant'    => '700',
								'lineHeight' => '1.5em'
							] )
						,
						( new ColorPicker( $this->getSlug( 'color' ) ) )
							->setLabel( __( 'Color', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-header-selective-css' )
							->addColor( 'initial', __( 'Initial', 'kenta' ), 'var(--kenta-accent-color)' )
							->addColor( 'hover', __( 'Hover', 'kenta' ), 'var(--kenta-primary-color)' )
							->addColor( 'active', __( 'Active', 'kenta' ), 'var(--kenta-primary-color)' )
						,
						( new Separator() ),
						( new Border( $this->getSlug( 'divider' ) ) )
							->setLabel( __( 'Items Divider', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-header-selective-css' )
							->setDefaultBorder( 1, 'solid', 'var(--kenta-base-200)' )
						,
					] )
				,
			];
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			// Add button dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {

				$css[".$this->slug"] = array_merge(
					Css::typography( CZ::get( $this->getSlug( 'typography' ) ) ),
					Css::border( CZ::get( $this->getSlug( 'divider' ) ), '--menu-items-divider' ),
					Css::colors( CZ::get( $this->getSlug( 'color' ) ), [
						'initial' => '--menu-text-initial-color',
						'hover'   => '--menu-text-hover-color',
						'active'  => '--menu-text-active-color',
					] )
				);

				if ( CZ::get( $this->getSlug( 'toggle-style' ) ) === 'bordered' ) {
					$css[".$this->slug"] = array_merge(
						$css[".$this->slug"],
						Css::border( CZ::get( $this->getSlug( 'toggle-border' ) ), '--menu-dropdown-toggle-border' )
					);
				}

				return $css;
			} );
		}

		/**
		 * Seletive refresh args
		 *
		 * @return array
		 */
		protected function selectiveRefresh() {
			return [
				".{$this->getSlug( 'wrap' )}",
				[ $this, 'build' ],
				[ 'container_inclusive' => true ]
			];
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {

			$attrs['class'] = Utils::clsx(
				'kenta-collapsable-menu h-full',
				$this->getSlug( 'wrap' ),
				$attrs['class'] ?? []
			);

			foreach ( $attrs as $attr => $value ) {
				$this->add_render_attribute( $this->slug, $attr, $value );
			}

			$menu = CZ::get( $this->getSlug( 'menu' ) );

			$depth    = absint( CZ::get( $this->getSlug( 'depth' ) ) );
			$hasArrow = $depth !== 1;

			echo '<div ' . $this->render_attribute_string( $this->slug ) . '>';
			wp_nav_menu( [
				'menu'           => $menu,
				'theme_location' => $this->slug,
				'menu_class'     => Utils::clsx( 'kenta-collapsable-menu', $this->slug, [
					'collapsable'          => CZ::checked( $this->getSlug( 'collapsable' ) ),
					'kenta-menu-has-arrow' => $hasArrow,
				] ),
				'fallback_cb'    => function ( $args ) {
					// for customize menu style, the default one not work.
					wp_page_menu( array_merge( $args, [
						'container' => 'ul'
					] ) );
				},
				'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'depth'          => $depth,
				'container'      => false,
				'echo'           => true,
				'link_after'     => CZ::checked( $this->getSlug( 'collapsable' ) )
					? '<button type="button" class="kenta-dropdown-toggle"><span class="kenta-menu-icon">' . IconsManager::render( CZ::get( $this->getSlug( 'toggle-icon' ) ) ) . '</span></button>'
					: '',
			] );
			echo '</div>';
		}
	}
}
